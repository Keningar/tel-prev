/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para reversar el cambio en campo login_cliente para igualar la longitud con la que tiene actualmente el campo login de info_punto
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 05-05-2022 - Versión Inicial.
 */

ALTER TABLE DB_COMERCIAL.INFO_VISUALIZACION_DOC_HIST MODIFY LOGIN_CLIENTE VARCHAR2(20);

/
