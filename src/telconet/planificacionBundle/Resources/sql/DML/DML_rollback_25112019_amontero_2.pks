
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = 
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PREFERENCIAS_CUADRILLAS_HAL')
AND USR_CREACION = 'amontero';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE 
NOMBRE_PARAMETRO = 'PREFERENCIAS_CUADRILLAS_HAL'
AND MODULO = 'PLANIFICACION'
AND USR_CREACION = 'amontero';

COMMIT;

/
