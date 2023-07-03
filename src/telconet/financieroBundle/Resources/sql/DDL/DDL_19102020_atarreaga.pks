/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 19-10-2020    
 * Se crea script para dar permisos de tablas a paquete financiero, y creación de índice.
 */
 
GRANT SELECT on "DB_SOPORTE"."INFO_DETALLE_HISTORIAL" to "DB_FINANCIERO";
GRANT SELECT on "DB_COMUNICACION"."INFO_COMUNICACION" to "DB_FINANCIERO"; 

CREATE INDEX DB_COMERCIAL.INFO_DET_SOL_CARACT_INDEX_USR ON DB_COMERCIAL.INFO_DETALLE_SOL_CARACT (USR_CREACION ASC);

COMMIT;
/
