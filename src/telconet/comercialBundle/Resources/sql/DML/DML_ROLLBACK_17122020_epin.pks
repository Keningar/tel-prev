DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE  PARAMETRO_ID = (SELECT ID_PARAMETRO
                       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                       WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL')
  AND DESCRIPCION = 'ESTADOS_SERVICIO_FACTIBILIDAD'; 
commit;
/             