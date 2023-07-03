/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 25-01-2023
 * Se crean las sentencias DML para reversar creación y clonación de perfiles (compartidos) a nivel de tabla SEGU_ASIGNACION.
 */

DELETE FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE USR_CREACION = 'telcosRegClon';

COMMIT;        
 /  
