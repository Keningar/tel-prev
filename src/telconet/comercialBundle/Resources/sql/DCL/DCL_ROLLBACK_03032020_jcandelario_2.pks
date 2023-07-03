/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0
 * @since 20-04-2020
 * Se crean las sentencias DCL para reversar permiso de los paquetes utilizados por el proyecto de diferidos por emergencia sanitaria.
 */

REVOKE EXECUTE ON DB_COMERCIAL.CMKG_GRUPO_PROMOCIONES FROM DB_FINANCIERO;
REVOKE EXECUTE ON DB_COMERCIAL.COMEK_MODELO FROM DB_FINANCIERO;
REVOKE EXECUTE ON DB_COMERCIAL.CMKG_PROMOCIONES FROM DB_FINANCIERO;
 /