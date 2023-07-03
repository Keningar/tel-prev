/**
 * @author Ruben Gomez <rgomez@telconet.ec>
 * @version 1.0
 * @since 11-10-2021    
 * Se crea DML para reversar configuración de parámetros 
 * de roles permitidos para crear tareas externas
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'ROLES_PERMITIDOS_CREAR_TAREAS_EXTERNAS' 
                      AND PROCESO = 'CREAR_TAREAS_EXTERNAS');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'ROLES_PERMITIDOS_CREAR_TAREAS_EXTERNAS' 
AND PROCESO = 'CREAR_TAREAS_EXTERNAS' 
AND ESTADO = 'Activo';

COMMIT;
/