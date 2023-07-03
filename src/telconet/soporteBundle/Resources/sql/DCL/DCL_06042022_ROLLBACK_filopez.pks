/**
 * Se agrega rollback del grant para poder consultar la tabla INFO_DETALLE_SOL_CARACT en el esquema de DB_SOPORTE
 * @author Fernando López <filopez@telconet.ec>
 * @version 1.0 06-04-2022 - Versión Inicial.
 */

REVOKE SELECT ON DB_COMERCIAL.INFO_DETALLE_SOL_CARACT FROM DB_SOPORTE;

/