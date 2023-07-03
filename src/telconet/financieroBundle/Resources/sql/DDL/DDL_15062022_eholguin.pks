create or replace TRIGGER DB_FINANCIERO.AFTER_INFO_PAG_AUT_DET 
AFTER UPDATE ON DB_FINANCIERO.INFO_PAGO_AUTOMATICO_DET 
REFERENCING OLD AS OLD NEW AS NEW FOR EACH ROW 
--
DECLARE 
/**
  * Documentacion para trigger AFTER_INFO_PAG_AUT_DET
  * Trigger que realiza envio de notificación cuando un detalle de pago automático cambia de estado Procesado a Eliminado y posee un pago generado.
  * @author Edgar Holguin <eholguin@telconet.ec>
  * @version 1.0 14-06-2022
  *
  * @author Kevin Villegas <kmvillegas@telconet.ec>
  * @version 1.1 14-09-2022  Se agrega envio de notificación al alias según oficina de facturación.
  */
  --

  CURSOR C_GetInfoNdiPagoCab(Cn_DetallePagAutId DB_FINANCIERO.INFO_PAGO_CAB.DETALLE_PAGO_AUTOMATICO_ID%TYPE)
  IS
    SELECT NVL(PERS.RAZON_SOCIAL,CONCAT(PERS.NOMBRES,CONCAT(' ',PERS.APELLIDOS))) AS CLIENTE,
           TO_CHAR(IDFC.FE_CREACION, 'DD-MM-YYYY HH24:MI:SS') AS FECHA_CREACION,
           IDFC.NUMERO_FACTURA_SRI AS NUMERO_FACTURA_SRI, 
           IPC.NUMERO_PAGO         AS NUMERO_PAGO, 
           IDFC.VALOR_TOTAL         AS VALOR_TOTAL, 
           IOG.NOMBRE_OFICINA      AS NOMBRE_OFICINA, 
           AMOT.NOMBRE_MOTIVO      AS NOMBRE_MOTIVO,
           IPT.LOGIN               AS LOGIN,   
           IDFC.OFICINA_ID         AS OFICINA,
           APDT.VALOR2             AS CORREO_ALIAS
    FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB  IDFC
    JOIN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET  IDFD  ON IDFC.ID_DOCUMENTO = IDFD.DOCUMENTO_ID
    JOIN DB_FINANCIERO.INFO_PAGO_DET                  IPD   ON IPD.ID_PAGO_DET   = IDFD.PAGO_DET_ID
    JOIN DB_FINANCIERO.INFO_PAGO_CAB                  IPC   ON IPC.ID_PAGO       = IPD.PAGO_ID
    JOIN DB_COMERCIAL.INFO_OFICINA_GRUPO              IOG   ON IOG.ID_OFICINA    = IDFC.OFICINA_ID
    JOIN DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET  IDFD  ON IDFD.PAGO_DET_ID  = IPD.ID_PAGO_DET
    JOIN DB_GENERAL.ADMI_MOTIVO                       AMOT  ON AMOT.ID_MOTIVO    = IDFD.MOTIVO_ID
    JOIN DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO ATDF  ON ATDF.ID_TIPO_DOCUMENTO          = IDFC.TIPO_DOCUMENTO_ID
    JOIN DB_COMERCIAL.INFO_PUNTO                      IPT   ON IPT.ID_PUNTO                    = IDFC.PUNTO_ID
    JOIN DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL        IPER  ON IPER.ID_PERSONA_ROL             = IPT.PERSONA_EMPRESA_ROL_ID
    JOIN DB_COMERCIAL.INFO_PERSONA                    PERS  ON PERS.ID_PERSONA                 = IPER.PERSONA_ID                        
    JOIN DB_GENERAL.ADMI_PARAMETRO_CAB                APCB  ON APCB.NOMBRE_PARAMETRO           = 'AUTOMATIZACION PAGOS'
    JOIN DB_GENERAL.ADMI_PARAMETRO_DET                APDT  ON APDT.PARAMETRO_ID               = APCB.ID_PARAMETRO
    AND APDT.VALOR1                     = CAST(IPC.OFICINA_ID as VARCHAR2(2))
    WHERE ATDF.CODIGO_TIPO_DOCUMENTO    IN ('NDI') AND 
          IPC.DETALLE_PAGO_AUTOMATICO_ID = Cn_DetallePagAutId;  
          
  --
  Lr_NdiReversoPago             DB_FINANCIERO.FNKG_PAGO_AUTOMATICO.Lr_NdiReversoPago;
  La_NdiReversoPago             DB_FINANCIERO.FNKG_PAGO_AUTOMATICO.T_NdiReversoPago;
  Ln_IdDetallePagoAut           DB_FINANCIERO.INFO_PAGO_AUTOMATICO_DET.ID_DETALLE_PAGO_AUTOMATICO%TYPE;
  Lr_GetAliasPlantilla          DB_FINANCIERO.FNKG_TYPES.Lr_AliasPlantilla;
  Lv_MimeType                   VARCHAR2(50)         := 'text/html; charset=UTF-8';
  Lv_NombreTablaContenido       VARCHAR2(100)        := 'strTablaInfoPagoNdi' ;
  Lv_Remitente                  VARCHAR2(100)        := 'notificaciones_telcos@telconet.ec';
  Lv_Asunto                     VARCHAR2(200)        := 'NOTIFICACION DE REVERSO DE PAGO';
  Lv_CodigoPlantilla            VARCHAR2(15)         := 'NOT_NDI_PAGAUT';
  Ln_Indx                       NUMBER;
  Ln_Indr                       NUMBER := 0;
  Lcl_TablaInfoPagoNdi          CLOB;
  Lcl_MessageMail               CLOB;
  Lv_MensajeError               VARCHAR2(4000);
  --
  Le_Exception                  EXCEPTION;
  -- Variable para correo de cobranza
  Lr_Emails                     VARCHAR2(500);
  Ln_Oficina                    NUMBER :=0;
  Lb_TieneNDI                   BOOLEAN := FALSE;
  --
  BEGIN
    --Entra si se actualiza el estado de la tabla
   
    IF UPDATING('ESTADO') THEN

      DBMS_LOB.CREATETEMPORARY(Lcl_TablaInfoPagoNdi, TRUE);
         
      Lr_GetAliasPlantilla    := DB_FINANCIERO.FNCK_CONSULTS.F_GET_ALIAS_PLANTILLA(Lv_CodigoPlantilla);

      IF :NEW.ESTADO = 'Pendiente' AND Lr_GetAliasPlantilla.PLANTILLA IS NOT NULL THEN
        -- Obtiene pago asociado al detalle de estado de cuenta

        Ln_IdDetallePagoAut := :NEW.ID_DETALLE_PAGO_AUTOMATICO;

        FNCK_TRANSACTION.INSERT_ERROR('LOG AFTER_INFO_PAG_AUT_DET', 'DB_FINANCIERO.AFTER_INFO_PAG_AUT_DET', 'SE REALIZA CAMBIO DE ESTADO DE ' ||:OLD.ESTADO|| ' A : '||:NEW.ESTADO|| ' ID_DETALLE_PAGO_AUTOMATICO: '||:OLD.ID_DETALLE_PAGO_AUTOMATICO);

        IF C_GetInfoNdiPagoCab%ISOPEN THEN
          CLOSE C_GetInfoNdiPagoCab;
        END IF;

        OPEN C_GetInfoNdiPagoCab(Ln_IdDetallePagoAut) ;
          
          LOOP
            FETCH C_GetInfoNdiPagoCab BULK COLLECT INTO La_NdiReversoPago LIMIT 1000 ;
            --                  
              Ln_Indx := La_NdiReversoPago.FIRST;
              --
              EXIT WHEN La_NdiReversoPago.COUNT = 0;
              --
              WHILE (Ln_Indx IS NOT NULL)  
              LOOP
  
                Lb_TieneNDI := TRUE;

                Ln_Indr := Ln_Indr + 1;
                Lr_NdiReversoPago := La_NdiReversoPago(Ln_Indx);
                -- datos para la oficina 
                IF Lr_NdiReversoPago.OFICINA IS NOT NULL AND Ln_Oficina <> Lr_NdiReversoPago.OFICINA THEN
                    Ln_Oficina := Lr_NdiReversoPago.OFICINA;
                    Lr_Emails  := Lr_NdiReversoPago.CORREO_ALIAS||';'||Lr_Emails;                  
                END IF;
                  --
                  DBMS_LOB.APPEND(Lcl_TablaInfoPagoNdi, '<tr><td> Fecha: </td><td>' 
                                  || Lr_NdiReversoPago.FECHA_CREACION
                                  || '</td></tr> <tr><td> Cliente: </td><td>' 
                                  || Lr_NdiReversoPago.CLIENTE
                                  || '</td></tr> <tr><td> Numero de Documento (NDI): </td><td>' 
                                  || Lr_NdiReversoPago.NUMERO_FACTURA_SRI 
                                  || '</td></tr> <tr><td> Motivo: </td><td>' 
                                  || Lr_NdiReversoPago.NOMBRE_MOTIVO 
                                  || '</td></tr> <tr><td> No Pago Apl: </td><td>' 
                                  || Lr_NdiReversoPago.NUMERO_PAGO 
                                  || '</td></tr> <tr><td> Valor: </td><td>' 
                                  || Lr_NdiReversoPago.VALOR_TOTAL 
                                  || '</td></tr> <tr><td> Oficina de Facturacion: </td><td>' 
                                  || Lr_NdiReversoPago.NOMBRE_OFICINA
                                  || '</td></tr> <tr><td> Login: </td><td>' 
                                  || Lr_NdiReversoPago.LOGIN
                                  || '</td></tr><tr><td></td><td></td></tr>');
                    

                  Ln_Indx := La_NdiReversoPago.NEXT(Ln_Indx);

              END LOOP;
            --
          END LOOP;
          --
        CLOSE C_GetInfoNdiPagoCab;
        --
          IF Lb_TieneNDI THEN
          --
            Lr_Emails :=  Lr_GetAliasPlantilla.ALIAS_CORREOS||';'||Lr_Emails;

            Lcl_MessageMail  := DB_FINANCIERO.FNCK_CONSULTS.F_CLOB_REPLACE(Lr_GetAliasPlantilla.PLANTILLA, 
                                                                          Lv_NombreTablaContenido, 
                                                                          Lcl_TablaInfoPagoNdi);

            --Envia correo
            DB_FINANCIERO.FNCK_CONSULTS.P_SEND_MAIL(Lv_Remitente, 
                                                    Lr_Emails,
                                                    Lv_Asunto,
                                                    SUBSTR(Lcl_MessageMail, 1, 32767), 
                                                    Lv_MimeType,
                                                    Lv_MensajeError);

            IF TRIM(Lv_MensajeError) IS NOT NULL THEN

              Lv_MensajeError := 'Error Trigger - DB_FINANCIERO.AFTER_INFO_PAG_AUT_DET ' || Lv_MensajeError ;          
              RAISE Le_Exception;

            END IF;

            DBMS_LOB.FREETEMPORARY(Lcl_TablaInfoPagoNdi);
            --
            Lcl_TablaInfoPagoNdi := '';
          END IF;
      --
      END IF;
    END IF;
    --
  EXCEPTION

  WHEN Le_Exception THEN  
      
    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'DB_FINANCIERO.AFTER_INFO_PAG_AUT_DET', 
                                          Lv_MensajeError, 
                                          NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_FINANCIERO'), 
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1') );
  WHEN OTHERS THEN

    DB_GENERAL.GNRLPCK_UTIL.INSERT_ERROR( 'Telcos+',
                                          'DB_FINANCIERO.AFTER_INFO_PAG_AUT_DET', 
                                          'Existio un error en el trigger - DB_FINANCIERO.AFTER_INFO_PAG_AUT_DET: ' ||:OLD.ID_DETALLE_PAGO_AUTOMATICO
                                           ||'--'||DBMS_UTILITY.FORMAT_ERROR_STACK || ' ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE, 
                                          NVL(SYS_CONTEXT( 'USERENV','HOST'), 'DB_FINANCIERO'), 
                                          SYSDATE,
                                          NVL(SYS_CONTEXT('USERENV', 'IP_ADDRESS'), '127.0.0.1') );

  END AFTER_INFO_PAG_AUT_DET;
