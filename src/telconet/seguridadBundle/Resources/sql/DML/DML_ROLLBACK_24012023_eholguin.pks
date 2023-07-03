/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 24-01-2023
 * Se crean las sentencias DML para reversar creación y clonación de perfiles (compartidos) a nivel de tabla SIST_PERFIL.
 */

DELETE FROM DB_SEGURIDAD.SIST_PERFIL WHERE USR_CREACION = 'telcosRegClon' AND ESTADO = 'Activo';

COMMIT;        
 /  
