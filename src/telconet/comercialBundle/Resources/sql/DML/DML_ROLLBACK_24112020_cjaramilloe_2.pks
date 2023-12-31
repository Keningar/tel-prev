/**
 * Documentación DELETE en ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET
 *
 * Eliminación de parámetros
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 24-1-2020
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  =
 (SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo')
 AND USR_CREACION = 'cjaramilloe';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB t WHERE t.NOMBRE_PARAMETRO = 'PROMOCIONES_APLICABLES_TM_COMERCIAL' AND ESTADO = 'Activo'
 AND USR_CREACION = 'cjaramilloe';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  =
(SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL' AND ESTADO = 'Activo')
AND DESCRIPCION = 'PUNTOS_POR_PAGINA'
AND USR_CREACION = 'cjaramilloe';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t WHERE t.PARAMETRO_ID  =
(SELECT MAX(ID_PARAMETRO) FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TM_COMERCIAL' AND ESTADO = 'Activo')
AND DESCRIPCION = 'ACTIVA_PAGINACION_PUNTOS'
AND USR_CREACION = 'cjaramilloe';

COMMIT;
/
