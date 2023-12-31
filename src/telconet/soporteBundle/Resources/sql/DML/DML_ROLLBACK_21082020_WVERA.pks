--Deletes 
DELETE  FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE VALOR1 = 'TIEMPO_VALIDAR_ENLACE'; 

--INSERTANDO NUEVOS UMBRALES
--RADIO 
DELETE  FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'RAD');
DELETE  FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'RAD';
--UTP 
DELETE  FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'UTP');
DELETE FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'UTP';
--
--FO 
DELETE  FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'FO');
DELETE  FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'FO';

--PARAMETRIZANDO MENSAJES DE ENLACES 
DELETE  FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MENSAJE_USUARIO_VALIDACION_ENLACES');
DELETE  FROM ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MENSAJE_USUARIO_VALIDACION_ENLACES';

COMMIT 

/
