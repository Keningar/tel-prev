
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID=829 AND DESCRIPCION = 'TIPO DE PROBLEMA MODULO AGENTE';
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET ESTADO='Activo' WHERE PARAMETRO_ID=829 AND VALOR1 = 'TAREA' OR VALOR1='CASO';

COMMIT;

/
