/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 22-03-2020
 * Se crea la sentencia DCL para reversar permiso de los paquetes utilizados por el proyecto de Adulto Mayor fase 2.
 */

REVOKE EXECUTE ON DB_COMERCIAL.CMKG_BENEFICIOS FROM DB_GENERAL;
REVOKE EXECUTE ON DB_COMERCIAL.CMKG_BENEFICIOS FROM DB_FINANCIERO;
REVOKE EXECUTE ON DB_COMERCIAL.CMKG_BENEFICIOS FROM DB_INFRAESTRUCTURA;
 /