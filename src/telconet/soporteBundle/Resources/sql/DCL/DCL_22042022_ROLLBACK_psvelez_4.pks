/**
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0
 * @since 22-04-2022
 * Se crea sentencia DCL para reversar permiso de la tabla de DB_SOPORTE  a DB_INFRAESTRUCTURA.
 * Se debe ejecutar en DB_SOPORTE
 */

  REVOKE SELECT 
   ON DB_SOPORTE.INFO_DETALLE_HIPOTESIS FROM DB_INFRAESTRUCTURA;

/
