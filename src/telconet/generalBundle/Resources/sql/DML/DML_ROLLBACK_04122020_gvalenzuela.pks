/* ROLLBACK PARAMETROS */
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (
    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_PLAN_POR_SUSPENSION'
);
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'TIPO_PLAN_POR_SUSPENSION';
COMMIT;
/