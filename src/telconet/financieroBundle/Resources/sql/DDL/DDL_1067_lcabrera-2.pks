   /**
    * @author Luis Cabrera <lcabrera@telconet.ec>
    * @version 1.0
    * Script de regularización de facturas de instalación.
    */
    DECLARE
        CURSOR C_ObtieneFacturas IS
        SELECT DISTINCT CAB.ID_DOCUMENTO, CAB.ESTADO_IMPRESION_FACT
          FROM DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_DET DET,
               DB_FINANCIERO.INFO_DOCUMENTO_FINANCIERO_CAB CAB
         WHERE CAB.ID_DOCUMENTO = DET.DOCUMENTO_ID
           AND DET.PLAN_ID IN (SELECT ID_PLAN FROM DB_COMERCIAL.INFO_PLAN_CAB WHERE UPPER(NOMBRE_PLAN) LIKE 'INSTALACION%' AND ESTADO = 'Activo' AND EMPRESA_COD = '18')
           AND CAB.TIPO_DOCUMENTO_ID IN (1,5)
           AND CAB.ESTADO_IMPRESION_FACT IN ('Pendiente','Activo','Cerrado')
            AND NOT EXISTS (
                SELECT 1
                  FROM DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA CARAC,
                       DB_COMERCIAL.ADMI_CARACTERISTICA AC
                 WHERE CARAC.VALOR = 'S'
                   AND CARAC.CARACTERISTICA_ID = AC.ID_CARACTERISTICA
                   AND AC.DESCRIPCION_CARACTERISTICA IN ('POR_CONTRATO_DIGITAL','POR_CONTRATO_FISICO')
                   AND CARAC.ESTADO = 'Activo'
                   AND CARAC.DOCUMENTO_ID = CAB.ID_DOCUMENTO
            );
            Lr_InfoDocumentoCaracteristica DB_FINANCIERO.INFO_DOCUMENTO_CARACTERISTICA%ROWTYPE;
    
            Lv_Mensaje VARCHAR2(3000);
            Ln_PorContratoWeb DB_COMERCIAL.ADMI_CARACTERISTICA.ID_CARACTERISTICA%TYPE;
            Lr_InfoDocumentoHistorial DB_FINANCIERO.INFO_DOCUMENTO_HISTORIAL%ROWTYPE := NULL;
            Ln_Contador        NUMBER := 0;
            Ln_ContadorGeneral NUMBER := 0;
            Le_Exception       EXCEPTION;
    BEGIN
        SELECT ID_CARACTERISTICA
          INTO Ln_PorContratoWeb
          FROM DB_COMERCIAL.ADMI_CARACTERISTICA
         WHERE DESCRIPCION_CARACTERISTICA = 'POR_CONTRATO_FISICO'
           AND ESTADO = 'Activo';

        FOR Lr_Facturas IN C_ObtieneFacturas
        LOOP
            BEGIN
                Ln_Contador := Ln_Contador +1;
                --Caso contrario, se clonan las características de la solicitud como características de la factura.
                Lr_InfoDocumentoCaracteristica                             := NULL;
                Lv_Mensaje                                                 := NULL;
                Lr_InfoDocumentoCaracteristica.ID_DOCUMENTO_CARACTERISTICA := DB_FINANCIERO.SEQ_INFO_DOCUMENTO_CARACT.NEXTVAL;
                Lr_InfoDocumentoCaracteristica.DOCUMENTO_ID                := Lr_Facturas.ID_DOCUMENTO;
                Lr_InfoDocumentoCaracteristica.CARACTERISTICA_ID           := Ln_PorContratoWeb;
                Lr_InfoDocumentoCaracteristica.VALOR                       := 'S';
                Lr_InfoDocumentoCaracteristica.ESTADO                      := 'Activo';
                Lr_InfoDocumentoCaracteristica.FE_CREACION                 := SYSDATE;
                Lr_InfoDocumentoCaracteristica.USR_CREACION                := 'telcos_web';
                Lr_InfoDocumentoCaracteristica.IP_CREACION                 := '127.0.0.1';
    
                --SE INSERTA LA CARACTERÍSTICA EN INFO_DOCUMENTO_CARACTERISTICA
                DB_FINANCIERO.FNCK_TRANSACTION.P_INSERT_INFO_DOCUMENTO_CARACT(Lr_InfoDocumentoCaracteristica, Lv_Mensaje);
                IF Lv_Mensaje IS NOT NULL THEN
                  RAISE Le_Exception;
                END IF;
                
                Lr_InfoDocumentoHistorial                        := NULL;
                Lr_InfoDocumentoHistorial.ID_DOCUMENTO_HISTORIAL := DB_FINANCIERO.SEQ_INFO_DOCUMENTO_HISTORIAL.NEXTVAL;
                Lr_InfoDocumentoHistorial.DOCUMENTO_ID           := Lr_Facturas.ID_DOCUMENTO;
                Lr_InfoDocumentoHistorial.FE_CREACION            := SYSDATE;
                Lr_InfoDocumentoHistorial.USR_CREACION           := 'telcos_web';
                Lr_InfoDocumentoHistorial.ESTADO                 := Lr_Facturas.ESTADO_IMPRESION_FACT;
                Lr_InfoDocumentoHistorial.OBSERVACION            := 'Regularización de facturas de instalación creadas manualmente.';
                --
                DB_FINANCIERO.FNCK_TRANSACTION.INSERT_INFO_DOC_FINANCIERO_HST(Lr_InfoDocumentoHistorial, Lv_Mensaje);
                IF Lv_Mensaje IS NOT NULL THEN
                    RAISE Le_Exception;
                END IF;
                
                IF Ln_Contador >= 5000 THEN
                    Ln_Contador := 0;
                    COMMIT;
                END IF;

                Ln_ContadorGeneral := Ln_ContadorGeneral + 1;
            EXCEPTION
                WHEN Le_Exception THEN
                    ROLLBACK;
                    DB_FINANCIERO.FNCK_TRANSACTION.INSERT_ERROR('SCRIPTS', 'DDL_1067_lcabrera-2', Lv_Mensaje);
            END;
        END LOOP;
        COMMIT;

        DBMS_OUTPUT.PUT_LINE('Proceso finalizado satisfactoriamente: ' || Ln_ContadorGeneral || ' facturas regularizadas.');
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            DB_FINANCIERO.FNCK_TRANSACTION.INSERT_ERROR('SCRIPTS', 'DDL_1067_lcabrera', DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ' ||
                                                                                        DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
    END;