/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para reversar parametro de minutos que se deben restar a la fecha actual para buscar tareas
 * por observacion, con el objetivo de evitar duplicidad de tareas
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 26-08-2021 - Versión Inicial.
 */


DELETE FROM 
db_general.admi_parametro_det apdt
WHERE apdt.PARAMETRO_ID in (SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'WEB SERVICE TAREAS'
            AND estado = 'Activo')
AND apdt.DESCRIPCION = 'RESTAR_MINUTOS'
AND apdt.USR_CREACION = 'ddelacruz';

commit;

/
