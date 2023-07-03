/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para revocar los parametros detalles de INVOCACION_KONIBIT_ACTUALIZACION 

 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 29-06-2022 - Version Inicial.
 */

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN ( SELECT ID_PARAMETRO
                        FROM DB_GENERAL.ADMI_PARAMETRO_CAB
                        WHERE NOMBRE_PARAMETRO = 'INVOCACION_KONIBIT_ACTUALIZACION'
                        AND ESTADO             = 'Activo');


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para revocar el parametro INVOCACION_KONIBIT_ACTUALIZACION 
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */                        
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'INVOCACION_KONIBIT_ACTUALIZACION'
  AND ESTADO           = 'Activo';


/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para revocar el producto caracteristica CANT MAX KONIBIT del producto Netlife Assistance Pro para ADMI_PROD_CARAC_COMPORTAMIENTO
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
DELETE FROM DB_COMERCIAL.ADMI_PROD_CARAC_COMPORTAMIENTO 
WHERE PRODUCTO_CARACTERISTICA_ID IN (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                                     WHERE PRODUCTO_ID IN (SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
                                                           where DESCRIPCION_PRODUCTO = 'Netlife Assistance Pro')
                                     AND CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
                                                               where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT'));


/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para revocar el producto caracteristica CANT MAX KONIBIT del producto ECOMMERCE BASIC para ADMI_PROD_CARAC_COMPORTAMIENTO
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
DELETE FROM DB_COMERCIAL.ADMI_PROD_CARAC_COMPORTAMIENTO 
WHERE PRODUCTO_CARACTERISTICA_ID IN (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
                                     WHERE PRODUCTO_ID IN (SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
                                                           where DESCRIPCION_PRODUCTO = 'ECOMMERCE BASIC')
                                     AND CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
                                                               where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT'));
                                                               

/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para revocar la caracteristica CANT MAX KONIBIT del producto ECOMMERCE BASIC
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
WHERE PRODUCTO_ID IN (SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
                      where DESCRIPCION_PRODUCTO = 'ECOMMERCE BASIC')
AND CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
                          where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT');


/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para revocar la caracteristica CANT MAX KONIBIT del producto Netlife Assistance Pro
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
WHERE PRODUCTO_ID IN (SELECT ID_PRODUCTO from DB_COMERCIAL.ADMI_PRODUCTO
                      where DESCRIPCION_PRODUCTO = 'Netlife Assistance Pro')
AND CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA
                          where DESCRIPCION_CARACTERISTICA = 'CANT MAX KONIBIT');


/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para revocar la caracteristica CANT MAX KONIBIT
 * @author José Candelario <jcandelario@telconet.ec>
 * @version 1.0 16-03-2022 - Version Inicial.
 */
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA ='CANT MAX KONIBIT';


COMMIT;
/
