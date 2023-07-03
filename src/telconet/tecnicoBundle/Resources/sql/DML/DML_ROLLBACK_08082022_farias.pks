/**
 *
 * ROLLBACK PARA LOS PARAMETROS CREADOS EN EL PROYECTO HBO-MAX
 *	 
 * @author Alberto Arias <farias@telconet.ec>
 * @version 1.0 08-08-2022
 */
 
 --ADMI PRODUCT CARACTERISTICAS
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO ap WHERE NOMBRE_TECNICO = 'HBO-MAX')
AND caracteristica_id IN (SELECT id_caracteristica FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                           WHERE DESCRIPCION_CARACTERISTICA IN ('HBO-MAX','SSID_HBO_MAX',
                                                                'PASSWORD_HBO_MAX','MIGRADO_HBO_MAX') 
                         );

--ADMI CARACTERISTICA
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'HBO-MAX';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'SSID_HBO_MAX';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'PASSWORD_HBO_MAX';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'MIGRADO_HBO_MAX';

-- admi_parametro_det
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CARAC_PRODUCTOS_TV'
                     ) and valor1 = 'HBO-MAX';
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'USUARIOS_PRODUCTOS_TV'
                     ) and descripcion = 'HBO-MAX';
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CONFI_ADICIONAL_PROD_TV'
                     ) and descripcion = 'HBO-MAX';
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'URN_PRODUCTOS_TV'
                     ) and descripcion = 'HBO-MAX';
                    
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CONFI_ADICIONAL_PROD_TV'
                     ) and descripcion = 'HBO-MAX';
-- CODIGOS DE CORREO       
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'COD_PLANTILLA_CORREO_PRODUCTOS_TV'
                     ) and descripcion = 'HBO-MAX';
-- CODIGOS DE SMS                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'COD_PLANTILLA_SMS_PRODUCTOS_TV'
                     ) and descripcion = 'HBO-MAX';                    
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PARAMETROS_WS_PRODUCTOS_TV'
                     ) and valor1 = 'ESTADOS_SPC_PERMITIDOS' AND valor2 = 'HBO-MAX';
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PARAMETROS_WS_PRODUCTOS_TV'
                     ) and valor1 = 'MENSAJES_ERRORES_USUARIO_AUTENTICACION' AND valor2 = 'HBO-MAX';                     
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_ESTADOS_INTERNET'
                     ) and descripcion = 'PRODUCTO' AND valor1 = 'HBO-MAX';                          
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'INFO_SERVICIO'
                     )  AND valor1 IN  ('ULTIMAS_MILLAS_INTERNET_HBO-MAX', 'ESTADOS_INTERNET_HBO-MAX');                       
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_ESTADOS_INTERNET'
                     ) and descripcion = 'PRODUCTO' AND valor1 = 'HBO-MAX';

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_CORREO_EXISTENTE_HBO_ELEARN'
                     );
                     
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'VALIDA_CORREO_EXISTENTE_HBO_ELEARN';                     
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'FLUJO_INGRESO_POR_ESTADO_INTERNET'
                     ) and descripcion = 'FLUJO_ESTADO_PENDIENTE' AND valor1 = 'HBO-MAX';                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PRODUCTOS_STREAMING_SIN_CREDENCIALES'
                     );          
                     
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'PRODUCTOS_STREAMING_SIN_CREDENCIALES';                      
                    
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PRODUCTO_FECHA_MINIMA_SUSCRIPCION'
                     ) and descripcion = 'NOMBRE_TECNICO' AND valor1 = 'HBO-MAX';                      
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PRODUCTO_FECHA_MINIMA_SUSCRIPCION'
                     ) and descripcion = 'MESES_MINIMOS' AND valor1 = 'HBO-MAX';                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PRODUCTO_FECHA_MINIMA_SUSCRIPCION'
                     ) and descripcion = 'MESES_MINIMOS' AND valor1 = 'HBO-MAX';                       
                     
--PLANTILLA SMS
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_RES_HBO_MAX';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_REE_HBO_MAX';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_NUE_HBO_MAX';                     
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'ENVIO_SMS_POR_PRODUCTO'
                     ) and valor1 = 'NOMBRE_TECNICO' AND valor2 = 'HBO-MAX';  
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_PRODUCTO_WS'
                     ) and valor1 = 'netlife-hbomax';                     
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PARAMETROS_WS_PRODUCTOS_TV'
                     ) and valor1 = 'VERIFICACIONES_AUTORIZACION' and valor2 = 'HBO-MAX';                      
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'ACTIVACION_PRODUCTOS_MEGADATOS'
                     ) and valor1 = 'HBO-MAX';                      
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'SERVICIOS_ADICIONALES_FACTURAR'
                     ) and descripcion = 'NOMBRE_TECNICO' and valor2 = 'HBO-MAX';                      
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PERMITIR_RESTABLECER_PASS'
                     ) and descripcion = 'PRODUCTO_TV' and valor1 = 'HBO-MAX';                     
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_TECNICO_PROD_PERMITIDOS_FLUJO_RECHAZADA_Y_ANULADA'
                     ) and descripcion = 'PRODUCTO_TV' and valor1 = 'HBO-MAX';                     
                     
                     
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_PRODUCTO_CANCELACION_INDIVIDUAL'
                     ) and descripcion = 'PRODUCTOS_CANCELACION' and valor1 = 'HBO-MAX';                     

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PRODUCTO_CANCELACION_TAREA_RAPIDA'
                     ) and descripcion = 'CREAR_TAREA_FLUJO_CANCELAR_PROD_TV' and valor1 = 'HBO-MAX';                     

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'FACTURACION_SERV_ADICIONAL'
                     ) and descripcion = 'DESCRIPCION_FACTURA' and valor1 = 'HBO-MAX';                     

DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'FACTURACION_SERV_ADICIONAL';

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_CORREO_EXISTENTE_PROD_STREAMING'
                     );

DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'VALIDA_CORREO_EXISTENTE_PROD_STREAMING';

DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'SERVICIOS_ADICIONALES_FACTURAR'
                     ) and valor2 = 'HBO-MAX';

COMMIT;
/

