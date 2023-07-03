/**
 * Documentación para reversar creación de parámetros
 * Eliminación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Steven Ruano <sruano@telconet.ec>
 * @version 1.0 29-11-2022
 *
 */

SET SERVEROUTPUT ON

--ELIMINAR EN ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_NUEVO_ALGORITMO');

--ELIMINAR EN ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'FACTIBILIDAD_NUEVO_ALGORITMO';

--ELIMINAR EN ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'VALIDACION_FACTIBILIDAD_NUEVO_ALGORITMO');

--ELIMINAR EN ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'VALIDACION_FACTIBILIDAD_NUEVO_ALGORITMO';

--ELIMINAR EN ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'URL_CALCULO_DISTANCIA');

--ELIMINAR EN ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'URL_CALCULO_DISTANCIA';

COMMIT;
/


