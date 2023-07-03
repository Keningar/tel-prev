/**
 * Se crea dml para el roolback de la creacion de parametro para el envio de notificaciones por cambio de datos de facturacion.
 * @author Adrian Limones <alimonesr@telconet.ec>
 * @since 1.0 25-09-2020
 */
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT CAB.ID_PARAMETRO
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
                      WHERE CAB.NOMBRE_PARAMETRO = 'NOTIFICACION CAMBIO TIPO FACTURACION');


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'NOTIFICACION CAMBIO TIPO FACTURACION';
COMMIT;    
/
