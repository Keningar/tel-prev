/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para reversar cabecera y detalle de parametro con numero de Horas que se restan a la fecha actual, 
 * para buscar actividades de Sisred, casos y tareas de Telcos, que hayan afectado al login
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 10-08-2021 - Versión Inicial.
 */

DELETE FROM 
db_general.admi_parametro_det apdt
WHERE apdt.PARAMETRO_ID in (SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'TIEMPO_AFECTACIONES_LOGIN'
            AND estado = 'Activo'
			AND usr_creacion = 'ddelacruz');

DELETE FROM 
db_general.admi_parametro_cab
WHERE 
nombre_parametro = 'TIEMPO_AFECTACIONES_LOGIN'
AND estado = 'Activo'
AND usr_creacion = 'ddelacruz';

commit;

/