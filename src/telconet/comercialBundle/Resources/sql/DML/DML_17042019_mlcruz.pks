--Actualización de nombres de acciones a ejecutarse
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2         = 'Suspender'
WHERE PARAMETRO_ID =
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PROCESOS_MASIVOS_TELCOHOME'
  AND ESTADO            = 'Activo'
  )
AND VALOR2 = 'Cortar';
/