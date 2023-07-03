
/**
 * Script que regulariza las facturas activas o abiertas con saldo cero y que tengan un pago asignado y no tengan NC por valor total de la factura
 * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
 * @version 1.0
 * @since 18-12-2019
 */
DECLARE

  --Cursor que obtiene todas las facturas (masivas y proporcionales) cuyo estado sea "Activo" que tengan un pago registrado 
  --y no tengan NC por valor total y que el saldo de la factura sea cero,
  --recibe fecha de inicio de proceso a considerar para la regularización.
  CURSOR C_DocumentosNoCerrados (Cv_EstadoCerrado   DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE,
                                 Cv_EstadoActivo    DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE,
                                 Cd_FeInicioProceso DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.FE_CREACION%TYPE) IS
    SELECT IDFC.ID_DOCUMENTO AS ID_DOCUMENTO, 
        IDFC.FE_EMISION AS FE_EMISION,     
        IDFC.NUMERO_FACTURA_SRI,  
        IDFC.VALOR_TOTAL,
        IDFC.PUNTO_ID,
        PTO.LOGIN
       ,(SELECT COUNT(*) FROM DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL DOCH 
        WHERE DOCH.DOCUMENTO_ID=IDFC.ID_DOCUMENTO AND DOCH.ESTADO=Cv_EstadoCerrado) AS CIERRE_FACT
        FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB IDFC,
        DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATD,
        DB_COMERCIAL.INFO_PUNTO PTO
        WHERE 
        IDFC.TIPO_DOCUMENTO_ID         = ATD.ID_TIPO_DOCUMENTO
        AND ATD.CODIGO_TIPO_DOCUMENTO  IN ('FAC', 'FACP')   
        AND IDFC.FE_CREACION           >= Cd_FeInicioProceso          
        AND IDFC.ESTADO_IMPRESION_FACT = Cv_EstadoActivo
        AND IDFC.OFICINA_ID IN (SELECT OFI.ID_OFICINA FROM DB_COMERCIAL.INFO_OFICINA_GRUPO OFI WHERE OFI.EMPRESA_ID IN ('18'))   
        AND IDFC.PUNTO_ID = PTO.ID_PUNTO
        AND (
           EXISTS (SELECT 1 FROM DB_FINANCIERO.INFO_PAGO_CAB PCAB,
                   DB_FINANCIERO.INFO_PAGO_DET PDET
                   WHERE PCAB.ID_PAGO=PDET.PAGO_ID
                   AND PDET.REFERENCIA_ID  = IDFC.ID_DOCUMENTO
                   AND PCAB.ESTADO_PAGO    = Cv_EstadoCerrado
                  )        
           )          
        AND NOT EXISTS (SELECT 1 
                       FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB NC,
                       DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET NCD,
                       DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDNC
                       WHERE NC.REFERENCIA_DOCUMENTO_ID = IDFC.ID_DOCUMENTO
                       AND NC.TIPO_DOCUMENTO_ID         = ATDNC.ID_TIPO_DOCUMENTO
                       AND NC.ID_DOCUMENTO              = NCD.DOCUMENTO_ID
                       AND ATDNC.ESTADO                 = Cv_EstadoActivo
                       AND ATDNC.CODIGO_TIPO_DOCUMENTO IN ('NC', 'NCI')
                       AND NC.ESTADO_IMPRESION_FACT     = Cv_EstadoActivo
                       AND NC.PUNTO_ID                  = PTO.ID_PUNTO  
                       AND IDFC.VALOR_TOTAL             = NC.VALOR_TOTAL
                       )
        AND ROUND(DB_FINANCIERO.FNKG_CARTERA_CLIENTES.F_SALDO_X_FACTURA(IDFC.ID_DOCUMENTO, '', 'saldo'),2)=0
      ;

  Le_Exception                  EXCEPTION;
  Lv_MsnError                   VARCHAR2(500);
  Lv_OcurrioError               VARCHAR2(100) := 'Ocurrió un error al realizar la regularización de los documentos: ';
  Lv_EstadoActivo               DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE := 'Activo';
  Lv_EstadoCerrado              DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB.ESTADO_IMPRESION_FACT%TYPE := 'Cerrado';
  Ln_Contador                   NUMBER := 0;
  Lv_Observacion                DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL.OBSERVACION%TYPE;

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
        Lr_InfoDocumentoFinancieroCab := NULL;
        Lr_InfoDocumentoHistorial     := NULL;

        Lr_InfoDocumentoFinancieroCab.ESTADO_IMPRESION_FACT := Lv_EstadoCerradoProc;
        DB_FINANCIERO.FNCK_TRANSACTION.UPDATE_INFO_DOC_FINANCIERO_CAB(Pn_IdDocumento,
                                                                      Lr_InfoDocumentoFinancieroCab,
                                                                      Pv_MsnError);

        IF Pv_MsnError IS NOT NULL THEN
          RAISE Le_ExceptionProc;
        END IF;

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

  --FACTURAS CON SALDO CERO Y CABECERA ACTIVA.
  FOR Lr_DocumentosNoCerrados IN C_DocumentosNoCerrados (Cv_EstadoCerrado   => Lv_EstadoCerrado,
                                                         Cv_EstadoActivo    => Lv_EstadoActivo,
                                                         Cd_FeInicioProceso => TO_DATE('01/01/2010','DD/MM/YYYY'))
  LOOP
    IF Lr_DocumentosNoCerrados.CIERRE_FACT IS NOT NULL AND Lr_DocumentosNoCerrados.CIERRE_FACT>0 THEN
      Lv_Observacion := 'Se regulariza el documento: Estado HISTORIAL = Cerrado, Documento con saldo 0 ';
    ELSE
      Lv_Observacion := 'Se regulariza el documento: Documento con saldo 0 ';
    END IF;
    P_CIERRA_FACTURAS(Pn_IdDocumento => Lr_DocumentosNoCerrados.ID_DOCUMENTO,
                      Pv_UsrCreacion => 'regulaFact',
                      Pv_Observacion => Lv_Observacion,
                      Pv_MsnError    => Lv_MsnError);

    IF Lv_MsnError IS NOT NULL THEN
      RAISE Le_Exception;
    END IF;
    Ln_Contador := Ln_Contador +1;

    IF Ln_Contador >= 5000 THEN
        COMMIT;
        DBMS_OUTPUT.PUT_LINE(Ln_Contador || ' documentos regularizados se cierra factura y se crea historial de cerrado por saldo 0.');
        Ln_Contador := 0;
    END IF;
  END LOOP;
  COMMIT;
  DBMS_OUTPUT.PUT_LINE(Ln_Contador || ' documentos regularizados se cierra factura y se crea historial de cerrado por saldo 0.');
  
EXCEPTION
  WHEN Le_Exception THEN
    ROLLBACK;
    DBMS_OUTPUT.PUT_LINE(Lv_OcurrioError || Lv_MsnError);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'Regulación Facturas',
                                          Lv_OcurrioError || Lv_MsnError,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
  WHEN OTHERS THEN
    ROLLBACK;
    DBMS_OUTPUT.PUT_LINE(Lv_OcurrioError || DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'Regulación Facturas',
                                          Lv_OcurrioError || DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || 
                                          DBMS_UTILITY.FORMAT_ERROR_BACKTRACE,
                                          NVL(SYS_CONTEXT('USERENV','HOST'), 'DB_FINANCIERO'),
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV','IP_ADDRESS'), '127.0.0.1') );
END;
