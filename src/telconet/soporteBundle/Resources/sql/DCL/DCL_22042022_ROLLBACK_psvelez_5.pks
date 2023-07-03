/**
 * @author Pedro Velez <psvelez@telconet.ec>
 * @version 1.0
 * @since 22-04-2022
 * Se crea sentencia DCL para reversar permiso de la tabla de DB_INFRAESTRUCTURA a DB_SOPORTE.
 * Se debe ejecutar en DB_INFRAESTRUCTURA
 */

  REVOKE SELECT 
   ON DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_CAB FROM DB_SOPORTE;
  REVOKE SELECT 
   ON DB_INFRAESTRUCTURA.INFO_PROCESO_MASIVO_DET FROM DB_SOPORTE;

/