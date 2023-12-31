----DELETE PARA ELIMINAR PARAMETRO PARA EL CANAL DE EXTRANET

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET WHERE PARAMETRO_ID =
(SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='CANALES_PUNTO_VENTA') 
AND VALOR1='CANAL_EXTRANET';


----DELETE PARA ELIMINAR USUARIO DE VENTA EXTRANET

DELETE FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL WHERE PERSONA_ID= (SELECT IP.ID_PERSONA FROM DB_COMERCIAL.INFO_PERSONA IP WHERE IP.LOGIN='extranet');
DELETE FROM COMERCIAL.INFO_PERSONA WHERE ID_PERSONA = (SELECT IP.ID_PERSONA FROM DB_COMERCIAL.INFO_PERSONA IP WHERE IP.LOGIN='extranet');


/*
 * Delete para eliminar los parametros ingresados para guardar los datos para crear una tarea para traslado.
 *
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET B WHERE B.PARAMETRO_ID= (SELECT A.ID_PARAMETRO
FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'DATOS_CREACION_TAREA');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'DATOS_CREACION_TAREA';


/*
 * Delete para eliminar el parametro del mensaje de error general para el proceso de traslado.
 *
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET B WHERE B.PARAMETRO_ID= (SELECT A.ID_PARAMETRO
FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'ERRORES_TRASLADO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB A WHERE A.NOMBRE_PARAMETRO = 'ERRORES_TRASLADO';


COMMIT;

/