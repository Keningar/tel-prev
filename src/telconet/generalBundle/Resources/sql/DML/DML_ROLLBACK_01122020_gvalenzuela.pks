/*
 * Rollback del parámetro de tareas de alquiler de servidor de soluciones DC.
 */
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET VALOR3 = 'INSTALACION DE NUEVO STORAGE',
      VALOR6 =  NULL
WHERE PARAMETRO_ID = (
    SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'HOSTING TAREAS POR DEPARTAMENTO'
  )
  AND DESCRIPCION = 'FACTIBILIDAD POOL RECURSOS - ALQ';
COMMIT;
/