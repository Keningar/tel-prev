/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 19-10-2020    
 * Se crea script para reversar permisos de tablas a paquete financiero, e índice.
 */
 
REVOKE SELECT ON DB_SOPORTE.INFO_DETALLE_HISTORIAL FROM DB_FINANCIERO;
REVOKE SELECT ON DB_COMUNICACION.INFO_COMUNICACION FROM DB_FINANCIERO; 

DROP INDEX DB_COMERCIAL.INFO_DET_SOL_CARACT_INDEX_USR;

COMMIT;
/
