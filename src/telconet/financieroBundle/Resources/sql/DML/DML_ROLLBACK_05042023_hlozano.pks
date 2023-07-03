/** 
 * @author Hector Lozano <hlozano@telconet.ec>
 * @version 1.0 
 * @since 05-04-2023
 * Se crea DML Rollback de parametrización de la empresa ECUANET para envío de notificaciones en Facturación Mensual.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
  WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'ENVIO_CORREO'
                        AND ESTADO             = 'Activo'
                        AND PROCESO            = 'DOCUMENTOS_ELECTRONICOS') AND 
  VALOR1 = 'FAC_MASIVA_EN'; 


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
  WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'ENVIO_CORREO'
                        AND ESTADO             = 'Activo'
                        AND PROCESO            = 'DOCUMENTOS_ELECTRONICOS') AND 
  VALOR1 = 'FAC_MASIVA_EN_FROM_SUBJECT'; 


COMMIT;
/
