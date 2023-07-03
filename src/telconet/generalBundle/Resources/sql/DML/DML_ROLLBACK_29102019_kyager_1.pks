/**
 * Documentación reverso de INSERT DE PARÁMETROS DE MOTIVO POR INACTIVACIÓN POR FECHAS DE VIGENCIAS.
 *
 * Se reversan parámetros para el motivo de inactivación por fechas de vigencias.
 *
 * @author Katherine Yager <kyager@telconet.ec>
 * @version 1.0 25-10-2019
 */

--ROLLBACK 
DELETE FROM DB_GENERAL.ADMI_MOTIVO WHERE NOMBRE_MOTIVO = 'Inactivación automática por fechas de vigencias';

COMMIT;
/