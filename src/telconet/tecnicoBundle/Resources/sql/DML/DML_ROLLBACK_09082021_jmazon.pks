/**
 *
 * ROLLBACK PARA LOS PARAMETROS CREADOS EN EL PROYECTO ECDF
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 9-08-2021
 */


--ADMI PRODUCT CARACTERISTICAS
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO ap WHERE NOMBRE_TECNICO = 'ECDF')
AND caracteristica_id IN (SELECT id_caracteristica FROM DB_COMERCIAL.ADMI_CARACTERISTICA 
                           WHERE DESCRIPCION_CARACTERISTICA IN ('ECDF','SSID_CANAL_DEL_FUTBOL',
                                                                'USUARIO_CANAL_DEL_FUTBOL','PASSWORD_CANAL_DEL_FUTBOL',
                                                                'MIGRADO_CANAL_DEL_FUTBOL') 
                         );

--ADMI CARACTERISTICA
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'ECDF';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'SSID_CANAL_DEL_FUTBOL';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'USUARIO_CANAL_DEL_FUTBOL';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'PASSWORD_CANAL_DEL_FUTBOL';
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA
WHERE DESCRIPCION_CARACTERISTICA = 'MIGRADO_CANAL_DEL_FUTBOL';

--PARAMETRO CARACT TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CARAC_PRODUCTOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'CARAC_PRODUCTOS_TV';

--PARAMETRO USER TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'USUARIOS_PRODUCTOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'USUARIOS_PRODUCTOS_TV';

--PARAMETRO URN ECDF
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CODIGO_URN_ECDF'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'CODIGO_URN_ECDF';

--PARAMETRO URN PRODUCTOS TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'URN_PRODUCTOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'URN_PRODUCTOS_TV';

--PARAMETRO CAB COD_PLANTILLA-CORREO TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'COD_PLANTILLA_CORREO_PRODUCTOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'COD_PLANTILLA_CORREO_PRODUCTOS_TV';

--PARAMETER CAB COD_PLANTILLA-SMS TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'COD_PLANTILLA_SMS_PRODUCTOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'COD_PLANTILLA_SMS_PRODUCTOS_TV';

--PLANTILLA SMS
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_REST_ECDF';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_REENV_ECDF';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_NUEVO_ECDF';

--PLANTILLAS  SMS FOX
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_REST_FOX';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_REENV_FOX';
DELETE FROM DB_COMUNICACION.ADMI_PLANTILLA
WHERE CODIGO = 'SMS_NUEVO_FOX';

--PARAMETER CAB PARAMETROS DATOS PROD TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'CONFI_ADICIONAL_PROD_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'CONFI_ADICIONAL_PROD_TV';

--PARAMETER CAB NOMBRES TECNICOS DE PRODUCTOS DE TV
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PRODUCTOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'PRODUCTOS_TV';

--PARAMETER CAB  FLUJO DE ESTADO INTERNET DIFERENTE A ACTIVO
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'FLUJO_INGRESO_POR_ESTADO_INTERNET'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'FLUJO_INGRESO_POR_ESTADO_INTERNET';

--ESTADOS DE INTERNET
DELETE FROM db_general.admi_parametro_det 
WHERE VALOR1 = 'ULTIMAS_MILLAS_INTERNET_ECDF';
DELETE FROM db_general.admi_parametro_det 
WHERE VALOR1 = 'ESTADOS_INTERNET_ECDF';

--VALIDACION AL AGREGAR SERVICIOS
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_SERVICIOS_TV'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'VALIDA_SERVICIOS_TV';

--VALIDACION NOTIFICACION CORREO PERSONA
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_NOTIFICACION_CORREO'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'VALIDA_NOTIFICACION_CORREO';

--NOMBRE DE PRODUCTOS ENVIADOS DEL WS DE AUTENTICACION Y DE RESTABLECER CONTRASEÑA
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_PRODUCTO_WS'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'NOMBRE_PRODUCTO_WS';

--PARAMETRO DE PRODUCTOS QUE SIGUEN EL FLUJO DE CANCELACION INDIVIDUAL
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_PRODUCTO_CANCELACION_INDIVIDUAL'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'NOMBRE_PRODUCTO_CANCELACION_INDIVIDUAL';

--PARAMETRO CAB PRODUCTOS TV QUE SIGUEN FLUJO DE CRS
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_TECNICO_PRODUCTOSTV_CRS'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'NOMBRE_TECNICO_PRODUCTOSTV_CRS';

--PARAMETRO CAB PRODUCTOS TV QUE SIGUEN FLUJO DE CRS ELIMINA CARACT CORREO
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_PRODUCTOSTV_ELIMINA_CARAC_CORREO'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'NOMBRE_PRODUCTOSTV_ELIMINA_CARAC_CORREO';

--PARAMETRO CAB VALIDA ESTADOS DEL INTERNET
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_ESTADOS_INTERNET'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'VALIDA_ESTADOS_INTERNET';

--PARAMETRO CAB FLUJO REINGRESO
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'FLUJO_DE_REEINGRESO'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'FLUJO_DE_REEINGRESO';

--PARAMETRO CAB FLUJO RESTABLECER PASS CON SERVICIO CANCELADO
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'PERMITIR_RESTABLECER_PASS'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'PERMITIR_RESTABLECER_PASS';

--#################
--# ENVIOS DE SMS #
--#################
DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE ID_PARAMETRO_DET IN
  ( SELECT DET.ID_PARAMETRO_DET
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    INNER JOIN DB_GENERAL.ADMI_PARAMETRO_DET DET
    ON DET.PARAMETRO_ID = CAB.ID_PARAMETRO
    WHERE CAB.NOMBRE_PARAMETRO = 'ENVIO_SMS_POR_PRODUCTO'
  );

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'ENVIO_SMS_POR_PRODUCTO';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID IN
  ( SELECT CAB.ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS_WS_PRODUCTOS_TV'
  );
DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PARAMETROS_WS_PRODUCTOS_TV';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID =
  ( SELECT CAB.ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    WHERE CAB.NOMBRE_PARAMETRO = 'ACTIVACION_PRODUCTOS_MEGADATOS'
  )
AND DESCRIPCION = 'El canal del fútbol';

DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID =
  ( SELECT CAB.ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB
    WHERE CAB.NOMBRE_PARAMETRO = 'SERVICIOS_ADICIONALES_FACTURAR'
  )
AND VALOR2 = 'ECDF';

--PARAMETRO CAB VALIDA CORREO
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'VALIDA_CORREO_EXISTENTE_ECDF'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'VALIDA_CORREO_EXISTENTE_ECDF';

--PARAMETRO CAB FLUJO RECHAZAR SOLICITUD INTERNET
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NOMBRE_TECNICO_PROD_PERMITIDOS_FLUJO_RECHAZADA_Y_ANULADA'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'NOMBRE_TECNICO_PROD_PERMITIDOS_FLUJO_RECHAZADA_Y_ANULADA';

--PARAMETRO CAB FLUJO NO GENERA CREDENCIALES
DELETE FROM db_general.admi_parametro_det 
WHERE parametro_id = (
                        SELECT id_parametro FROM db_general.admi_parametro_cab 
                        WHERE nombre_parametro = 'NO_GENERA_CREDENCIALES_CRS'
                     );
DELETE FROM db_general.admi_parametro_cab 
WHERE nombre_parametro = 'NO_GENERA_CREDENCIALES_CRS';

--NOMBRE TECNICO DE PRODUCTO ECDF
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
  SET NOMBRE_TECNICO='OTROS', DESCRIPCION_PRODUCTO = 'ECDF-CANAL DEL FULTBOL'
 WHERE ID_PRODUCTO = 1404;

COMMIT;
/
