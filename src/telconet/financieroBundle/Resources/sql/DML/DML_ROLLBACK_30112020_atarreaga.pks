/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 30-11-2020    
 * Se crea DML para reversar configuración de parámetros de estados de contrato en el proceso 
 *  de envío de débitos. 
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'DEBITOS_ESTADOS_CONTRATO' 
                      AND MODULO = 'FINANCIERO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'DEBITOS_ESTADOS_CONTRATO' 
AND MODULO = 'FINANCIERO' 
AND ESTADO = 'Activo';

COMMIT;
/
