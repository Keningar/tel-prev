/**
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @version 1.0
 * @since 27-09-2021    
 * Se crea DML para reversar configuración de parámetros 
 * de codigos de plantillas que tienen caracteres especiales
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'PLANTILLAS_CON_CARACTERES_ESPECIALES' 
                      AND MODULO = 'TECNICO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'PLANTILLAS_CON_CARACTERES_ESPECIALES' 
AND MODULO = 'TECNICO' 
AND ESTADO = 'Activo';

COMMIT;
/