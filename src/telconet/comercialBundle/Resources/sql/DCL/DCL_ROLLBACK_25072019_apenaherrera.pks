/**
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0
 * @since 29-07-2019    
 * Se crean las sentencias DCL para reversar permiso de grand select al esquema DB_COMERCIAL 
 * para la tabla DB_GENERAL.INFO_ERROR.
 */

 REVOKE SELECT 
   ON DB_GENERAL.INFO_ERROR FROM DB_COMERCIAL;
 /