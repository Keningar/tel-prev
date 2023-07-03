
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN (SELECT ID_PARAMETRO 
                       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE NOMBRE_PARAMETRO = 'Metraje que cubre el precio de instalación');
                       
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'Metraje que cubre el precio de instalación';

COMMIT;
/










