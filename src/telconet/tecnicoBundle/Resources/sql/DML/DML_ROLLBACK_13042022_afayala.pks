SET DEFINE OFF;

-- Eliminar en la tabla ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'FORMATOS_TILDES_BD');

-- Eliminar en la tabla ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'FORMATOS_TILDES_BD';

COMMIT;

/
