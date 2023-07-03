
/**
 * Script que regulariza las facturas con al menos un historial cerrado.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 19-09-2018
 * Tiempo de ejecución en desarrollo: 428 segundos.
 */
DECLARE

  --Cursor que obtiene todas las facturas (masivas y proporcionales) cuyo estado sea "Activo", que tenga un historial en estado "Cerrado"
  --Y que el saldo de la factura sea cero
  CURSOR C_DocumentosNoCerrados (Cv_EstadoCerrado   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE,
                                 Cv_EstadoActivo    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE,
                                 Cd_FeInicioProceso DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.FE_CREACION%TYPE) IS
    SELECT
        DISTINCT CAB.ID_DOCUMENTO
     FROM
         DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB,
         DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL HISTO
     WHERE
         TIPO_DOCUMENTO_ID IN (1,5)
         AND CAB.FE_CREACION >= Cd_FeInicioProceso
         AND CAB.ESTADO_IMPRESION_FACT = Cv_EstadoActivo
         AND CAB.ID_DOCUMENTO = HISTO.DOCUMENTO_ID
         AND HISTO.ESTADO = Cv_EstadoCerrado
         AND ROUND( DB_FINANCIERO.FNKG_CARTERA_CLIENTES.F_SALDO_X_FACTURA( CAB.ID_DOCUMENTO, TO_CHAR(SYSDATE,'DD-MON-YYYY'), 'saldo' ), 2 ) <= 0;

  --Cursor que obtiene todas las facturas (masivas y proporcionales) cuyo estado sea "Activo" y que el saldo de la factura sea cero
  CURSOR C_DocumentosSinHisto (Cv_EstadoActivo    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE,
                               Cd_FeInicioProceso DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.FE_CREACION%TYPE) IS
    SELECT
        DISTINCT CAB.ID_DOCUMENTO
     FROM
         DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB
     WHERE
         TIPO_DOCUMENTO_ID IN (1,5)
         AND CAB.FE_CREACION >= Cd_FeInicioProceso
         AND CAB.ESTADO_IMPRESION_FACT = Cv_EstadoActivo
         AND ROUND( DB_FINANCIERO.FNKG_CARTERA_CLIENTES.F_SALDO_X_FACTURA( CAB.ID_DOCUMENTO, TO_CHAR(SYSDATE,'DD-MON-YYYY'), 'saldo' ), 2 ) <= 0;

  Le_Exception                  EXCEPTION;
  Lv_MsnError                   VARCHAR2(500);
  Lv_OcurrioError               VARCHAR2(100) := 'Ocurrió un error al realizar la regularización de los documentos: ';
  Lv_EstadoActivo               DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE := 'Activo';
  Lv_EstadoCerrado              DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE := 'Cerrado';
  Ln_Contador                   NUMBER := 0;

    --PROCEDIMIENTO QUE CIERRA LAS FACTURAS.
    PROCEDURE P_CIERRA_FACTURAS(Pn_IdDocumento IN  DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ID_DOCUMENTO%TYPE,
                                Pv_UsrCreacion IN  DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL.USR_CREACION%TYPE,
                                Pv_Observacion IN  DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL.OBSERVACION%TYPE,
                                Pv_MsnError    OUT VARCHAR2)
    IS
      Le_ExceptionProc              EXCEPTION;
      Lr_InfoDocumentoFinancieroCab DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB%ROWTYPE;
      Lr_InfoDocumentoHistorial     DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE;
      Lv_EstadoCerradoProc          DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE := 'Cerrado';

    BEGIN
      --SE INICIALIZAN LAS VARIABLES POR CADA ITERACIÓN.
        Lr_InfoDocumentoFinancieroCab := NULL;
        Lr_InfoDocumentoHistorial     := NULL;

        Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT := Lv_EstadoCerradoProc;
        DB_FINANCIERO.FNCK_TRANSACTION.UPDATE_INFO_DOC_FINANCIERO_CAB(Pn_IdDocumento,
                                                                      Lr_InfoDocumentoFinancieroCab,
                                                                      Pv_MsnError);

        IF Pv_MsnError IS NOT NULL THEN
          RAISE Le_ExceptionProc;
        END IF;

        --SE INSERTA EL HISTORIAL POR REGULARIZACIÓN.
        Lr_InfoDocumentoHistorial.ID_DOCUMENTO_HISTORIAL    := DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.NEXTVAL ;
        Lr_InfoDocumentoHistorial.DOCUMENTO_ID              := Pn_IdDocumento;
        Lr_InfoDocumentoHistorial.MOTIVO_ID                 := NULL;
        Lr_InfoDocumentoHistorial.FE_CREACION               := SYSDATE;
        Lr_InfoDocumentoHistorial.USR_CREACION              := Pv_UsrCreacion;
        Lr_InfoDocumentoHistorial.ESTADO                    := Lv_EstadoCerradoProc;
        Lr_InfoDocumentoHistorial.OBSERVACION               := Pv_Observacion;

        DB_FINANCIERO.FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_HST(Lr_InfoDocumentoHistorial, Pv_MsnError);

        IF Pv_MsnError IS NOT NULL THEN
          RAISE Le_ExceptionProc;
        END IF;
    EXCEPTION
        WHEN Le_ExceptionProc THEN
            ROLLBACK;
        WHEN OTHERS THEN
            ROLLBACK;
    END P_CIERRA_FACTURAS;

BEGIN

  --FACTURAS CON HISTORIAL CERRADO, SALDO CERO Y CABECERA ACTIVA.
  FOR Lr_DocumentosNoCerrados IN C_DocumentosNoCerrados (Cv_EstadoCerrado   => Lv_EstadoCerrado,
                                                         Cv_EstadoActivo    => Lv_EstadoActivo,
                                                         Cd_FeInicioProceso => TO_DATE('01/01/2018','DD/MM/YYYY'))
  LOOP
    P_CIERRA_FACTURAS(Pn_IdDocumento => Lr_DocumentosNoCerrados.ID_DOCUMENTO,
                      Pv_UsrCreacion => 'regulaFact1',
                      Pv_Observacion => 'Se regulariza el documento: Estado HISTORIAL = Cerrado, Documento con saldo 0 ',
                      Pv_MsnError    => Lv_MsnError);

    IF Lv_MsnError IS NOT NULL THEN
      RAISE Le_Exception;
    END IF;
    Ln_Contador := Ln_Contador +1;

    IF Ln_Contador >= 5000 THEN
        COMMIT;
        DBMS_OUTPUT.PUT_LINE(Ln_Contador || ' registros afectados con historial cerrado y saldo 0.');
        Ln_Contador := 0;
    END IF;
  END LOOP;
  COMMIT;
  DBMS_OUTPUT.PUT_LINE(Ln_Contador || ' registros afectados con historial cerrado y saldo 0.');

  Ln_Contador := 0;
  --FACTURAS SIN HISTORIAL CERRADO, SALDO CERO Y CABECERA ACTIVA.
  FOR Lr_DocumentosSinHisto IN C_DocumentosSinHisto (Cv_EstadoActivo    => Lv_EstadoActivo,
                                                     Cd_FeInicioProceso => TO_DATE('01/01/2018','DD/MM/YYYY'))
  LOOP
    P_CIERRA_FACTURAS(Pn_IdDocumento => Lr_DocumentosSinHisto.ID_DOCUMENTO,
                      Pv_UsrCreacion => 'regulaFact2',
                      Pv_Observacion => 'Se regulariza el documento: Documento con saldo 0 ',
                      Pv_MsnError    => Lv_MsnError);

    IF Lv_MsnError IS NOT NULL THEN
      RAISE Le_Exception;
    END IF;
    Ln_Contador := Ln_Contador +1;

    IF Ln_Contador >= 5000 THEN
        COMMIT;
        DBMS_OUTPUT.PUT_LINE(Ln_Contador || ' registros afectados con saldo 0 ');
        Ln_Contador := 0;
    END IF;
  END LOOP;
  COMMIT;
  DBMS_OUTPUT.PUT_LINE(Ln_Contador || ' registros afectados con saldo 0 ');

EXCEPTION
  WHEN Le_Exception THEN
    ROLLBACK;
    DBMS_OUTPUT.PUT_LINE(Lv_OcurrioError || Lv_MsnError);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'Script Regulación Facturas',
                                          Lv_OcurrioError || Lv_MsnError,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  WHEN OTHERS THEN
    ROLLBACK;
    DBMS_OUTPUT.PUT_LINE(Lv_OcurrioError || DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'Script Regulación Facturas',
                                          Lv_OcurrioError || DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || 
                                          DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
END;