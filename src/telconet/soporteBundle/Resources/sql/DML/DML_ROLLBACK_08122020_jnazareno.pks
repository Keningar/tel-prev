-------------------------------------------------------------------------- ROLLBACK
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = 1 WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO  = 'UTP')
  AND DESCRIPCION = 'MIN_PORCENTAJE_PAQUETES_RECIBIDO';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = 3 WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO  = 'FO')
  AND DESCRIPCION = 'MIN_PORCENTAJE_PAQUETES_RECIBIDO';
  
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = 20 WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO  = 'RAD')
  AND DESCRIPCION = 'MIN_PORCENTAJE_PAQUETES_RECIBIDO';

COMMIT;