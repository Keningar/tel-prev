DELETE DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'wvera' AND VALOR1 = 'ENDPOINT_ARCGIS_MD';
DELETE DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'wvera' AND VALOR1 = 'ENDPOINT_ARCGIS_TN';

DELETE DB_GENERAL.ADMI_PARAMETRO_DET WHERE USR_CREACION = 'wvera' AND  PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CONF_ERRORES_GENERALES_TMO');

DELETE DB_GENERAL.ADMI_PARAMETRO_CAB WHERE USR_CREACION = 'wvera' AND  NOMBRE_PARAMETRO = 'CONF_ERRORES_GENERALES_TMO';

DELETE DB_COMERCIAL.ADMI_CARACTERISTICA WHERE USR_CREACION = 'wvera' AND  DESCRIPCION_CARACTERISTICA = 'AUTH_CREACION_KML';


COMMIT;