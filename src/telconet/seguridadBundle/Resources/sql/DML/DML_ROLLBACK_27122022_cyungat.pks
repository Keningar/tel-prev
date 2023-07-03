/*
 * @author Christian Yunga <cyungat@telconet.ec>
 * @version 1.0
 * @since 22-12-2022
 * Se crean las sentencias DML para eliminación de perfiles asignados a nivel de tabla SEGU_ASIGNACION.
 */

DELETE  FROM DB_SEGURIDAD.SEGU_ASIGNACION WHERE USR_CREACION = 'telcosReg';
COMMIT;
/

