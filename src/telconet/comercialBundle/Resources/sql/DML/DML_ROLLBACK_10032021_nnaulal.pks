/* PARAMETROS */
/* CREACIÓN DEL PARÁMETRO CAB  - REPROGRAMAR_DEPARTAMENTO_HAL*/
                     
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'PARAMS_PLANTILLA_EXTRANET');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PARAMS_PLANTILLA_EXTRANET';

DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA WHERE PLANTILLA_ID =(
SELECT ID_PLANTILLA 
FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE CODIGO='NOTIF_CLIE_EXT');

DELETE FROM DB_COMUNICACION.INFO_ALIAS_PLANTILLA WHERE PLANTILLA_ID =(
SELECT ID_PLANTILLA 
FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE CODIGO='NOTIF_EXTRANET');
                        
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE CODIGO     = 'NOTIF_EXTRANET' 
AND MODULO       = 'COMERCIAL'
AND ESTADO       = 'Activo';

DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA 
WHERE CODIGO     = 'NOTIF_CLIE_EXT' 
AND MODULO       = 'COMERCIAL'
AND ESTADO       = 'Activo';

COMMIT;
/
