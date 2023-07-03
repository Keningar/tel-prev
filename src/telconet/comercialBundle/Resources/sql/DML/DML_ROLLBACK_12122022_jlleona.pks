/**
 *  UPDATE a tabla ADMI_PARAMETRO_DET para cambiar el parámetro con descripción APIKEY-ORQ usado por el Orquestador a APIKEY
 *
 * @author Jefferson León <jlleona@telconet.ec>
 * @version 1.0 12-12-2022
*/

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET DESCRIPCION = 'APIKEY'
WHERE DESCRIPCION = 'APIKEY-ORQ' AND PARAMETRO_ID IN (SELECT ID_PARAMETRO FROM db_general.admi_parametro_cab WHERE NOMBRE_PARAMETRO='DATOS_ORQUESTADOR_APIKEY');


COMMIT;
/