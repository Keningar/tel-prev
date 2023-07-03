/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 18-11-2020    
 * Se crea DML para reversar configuraciones en el proceso de Cancelacion Administrativa. 
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'CANCELACION_MASIVA' 
                      AND MODULO = 'TECNICO')
AND VALOR2 = 'GeneraNdiAgrupada';


DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'PROCESO_AGRUPA_NDI_DIFERIDA' 
                      AND MODULO = 'FINANCIERO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'PROCESO_AGRUPA_NDI_DIFERIDA' 
AND MODULO = 'FINANCIERO' 
AND ESTADO = 'Activo';

COMMIT;
/
