--Se des-habilita la tecnologia ZTE para producto Small Business
UPDATE DB_GENERAL.ADMI_PARAMETRO_CAB 
SET estado='Activo'
WHERE nombre_parametro='ISB_TECNOLOGIAS_NO_PERMITIDAS';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET estado='Activo'
WHERE parametro_id=(select id_parametro from DB_GENERAL.ADMI_PARAMETRO_CAB  where nombre_parametro='ISB_TECNOLOGIAS_NO_PERMITIDAS');

COMMIT;

/
