/**
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0
 * @since 12-08-2021    
 * Se crea sentencia DCL para reversar permiso de la tabla de Comunicacion a Soporte.
 */

 REVOKE SELECT 
   ON DB_COMUNICACION.ADMI_CLASE_DOCUMENTO FROM DB_SOPORTE;
 
 /