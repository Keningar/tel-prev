/**
 * DEBE EJECUTARSE EN DB_SOPORTE.
 * Script para quitar permisos necesarios para ejecutar 
 * el procedimiento P_CAMBIAR_ESTADO_TAREA en el esquema DB_COMERCIAL
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @version 1.0 10-10-2019 - Versión Inicial.
 */
REVOKE EXECUTE ON DB_SOPORTE.SPKG_SOPORTE FROM DB_COMERCIAL;

/**
 * DEBE EJECUTARSE EN DB_SOPORTE.
 */
REVOKE SELECT ON DB_SOPORTE.INFO_DETALLE_HISTORIAL FROM DB_COMUNICACION;
/
