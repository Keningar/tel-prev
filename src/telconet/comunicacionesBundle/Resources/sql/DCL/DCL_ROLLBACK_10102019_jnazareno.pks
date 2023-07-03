/**
 * DEBE EJECUTARSE EN DB_COMUNICACION.
 * Script para quitar permisos necesarios para ejecutar 
 * la funcion F_GET_COUNT_TAREAS en el esquema DB_SOPORTE
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @version 1.0 04-10-2019 - Versión Inicial.
 */
REVOKE EXECUTE ON DB_COMUNICACION.CUKG_CONSULTS FROM DB_SOPORTE;
/
