DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                      WHERE NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS')
AND DESCRIPCION = 'GolTv Play' AND VALOR1 = 'GTV1' AND USR_CREACION = 'djreyes';

COMMIT;
/
