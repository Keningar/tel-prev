/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 02-07-2021    
 * Se crea DML para reversar configuración de cantidad límite de detalles para el proceso de facturación mensual masiva TN. 
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET 
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO 
                      FROM DB_GENERAL.ADMI_PARAMETRO_CAB  
                      WHERE NOMBRE_PARAMETRO = 'FACTURACION_MASIVA_TN' 
                      AND MODULO = 'FINANCIERO');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB 
WHERE NOMBRE_PARAMETRO = 'FACTURACION_MASIVA_TN' 
AND MODULO = 'FINANCIERO' 
AND ESTADO = 'Activo'; 


COMMIT;
/
