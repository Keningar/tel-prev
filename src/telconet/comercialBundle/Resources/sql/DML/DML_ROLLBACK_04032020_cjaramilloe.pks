/**
 * Documentación DELETE ADMI_PARAMETRO_CAB, ADMI_PARAMETRO_DET
 *
 * Eliminación parámetro para mensajes de usuario TM Comercial.
 *
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 * @version 1.0 04-03-2020
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET t 
    WHERE t.DESCRIPCION = 'RESTRICCION_ACCESO' 
    AND USR_CREACION = 'cjaramilloe';
    
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB t 
    WHERE t.NOMBRE_PARAMETRO = 'MENSAJES_TM_COMERCIAL' 
    AND USR_CREACION = 'cjaramilloe';
    
COMMIT;
/
