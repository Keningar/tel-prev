/**
 * @author Ruben Gomez <rgomez@telconet.ec>
 * @version 1.0
 * @since 13-08-2021    
 * Se crea DML para reversar configuración de parámetros 
 * de codigos de equipos permitidos sin MAC
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'EQUIPOS_PERMITIDOS_SIN_MAC' 
                      AND MODULO = 'TECNICO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'EQUIPOS_PERMITIDOS_SIN_MAC' 
AND MODULO = 'TECNICO' 
AND ESTADO = 'Activo';

COMMIT;
/