/**
 * @author Ruben Gomez <rgomez@telconet.ec>
 * @version 1.0
 * @since 06-05-2021    
 * Se crea DML para reversar configuración de parámetros 
 * de codigos de empresas validas
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'COD_EMPRESAS_VALIDAS' 
                      AND MODULO = 'FINANCIERO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'COD_EMPRESAS_VALIDAS' 
AND MODULO = 'FINANCIERO' 
AND ESTADO = 'Activo';

COMMIT;
/
