/**
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 
 * @version 1.0
 * @since 15-06-2020
 * Se borra tabla de configuración SFTP. 
 */

DROP INDEX DB_SFTP.IDX_ADMI_CONFIGURACION_FECHA;
DROP INDEX DB_SFTP.IDX_ADMI_CONFIGURACION_NOMBRE;

DROP SEQUENCE DB_SFTP.SEQ_ADMI_CONFIGURACION;

DROP TABLE DB_SFTP.ADMI_CONFIGURACION;

COMMIT;
/
