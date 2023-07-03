/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 27-01-2023
 * Se crean las sentencias DML para reversar actualización de perfiles (compartidos) asignados a nivel de tabla SEGU_PERFIL_PERSONA.
 */


DELETE  FROM DB_SEGURIDAD.SEGU_PERFIL_PERSONA WHERE USR_CREACION = 'telcosRegClon';


COMMIT;        
 /  

























