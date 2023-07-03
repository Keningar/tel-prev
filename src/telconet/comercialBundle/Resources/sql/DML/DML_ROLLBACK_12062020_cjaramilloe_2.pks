/**
 * Rollback de parámetros contrato digital persona juridica
 * 
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
 *
 * @version 1.0
 * @since 15-06-2020
 */

DELETE 
    FROM DB_GENERAL.ADMI_PARAMETRO_DET t 
    WHERE t.DESCRIPCION = 'PARAMSQUERYJUR' 
    AND USR_CREACION = 'jnazareno';

DELETE 
    FROM DB_SFTP.ADMI_CONFIGURACION ac
    WHERE ac.NOMBRE = 'SECURITYDATA_SERVER' 
    AND USR_CREACION = 'cjaramilloe';

COMMIT;
/
