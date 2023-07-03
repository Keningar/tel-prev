/**
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 10-03-2022    
 * Se crea la sentencia DML para eliminación de registros.
 */
 
  DELETE * FROM DB_COMERCIAL.INFO_SERVICIO_HISTORIAL WHERE SERVICIO_ID = 543817 AND USR_CREACION = 'telcosRegulariza';

  UPDATE DB_COMERCIAL.INFO_SERVICIO set MESES_RESTANTES = 0 WHERE ID_SERVICIO=543817;

COMMIT;

/
