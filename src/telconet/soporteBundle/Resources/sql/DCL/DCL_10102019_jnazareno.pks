/**
 * DEBE EJECUTARSE EN DB_SOPORTE.
 * Script para dar permisos necesarios para ejecutar 
 * el procedimiento P_CAMBIAR_ESTADO_TAREA en el esquema DB_COMERCIAL
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @version 1.0 10-10-2019 - Versión Inicial.
 */
GRANT EXECUTE ON DB_SOPORTE.SPKG_SOPORTE TO DB_COMERCIAL;

/**
 * DEBE EJECUTARSE EN DB_SOPORTE.
 */
GRANT SELECT ON DB_SOPORTE.INFO_DETALLE_HISTORIAL TO DB_COMUNICACION;
/
