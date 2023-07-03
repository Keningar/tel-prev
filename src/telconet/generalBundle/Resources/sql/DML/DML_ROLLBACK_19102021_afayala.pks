/**
 * Documentación para reversar creación de parámetros
 * Eliminación en DB_GENERAL.ADMI_PARAMETRO_CAB 
 * y DB_GENERAL.ADMI_PARAMATRO_DET.
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 28-07-2021
 */

SET SERVEROUTPUT ON

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR4 = '3'
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PARAMS_PRODS_TN_GPON')
AND VALOR1 = 'PRODUCTOS_RELACIONADOS_INTERNET_IP' AND VALOR2 = '1155' AND VALOR3 = '1188';

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1 = '1271,1272,1275,1276'
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'IP_PRIVADA_FIJA_GPON');

--ELIMINAR EN ADMI_PARAMETRO_DET
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PRECIO_VELOCIDAD_ISB');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'IP_PRIVADA_GPON_CARACTERISTICAS');

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_DET
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_ISB_CAMBIO_PUERTO');

--ELIMINAR EN ADMI_PARAMETRO_CAB
DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'PRECIO_VELOCIDAD_ISB';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'IP_PRIVADA_GPON_CARACTERISTICAS';

DELETE FROM DB_GENERAL.ADMI_PARAMETRO_CAB
WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_ISB_CAMBIO_PUERTO';

-- ELIMINAR EN ADMI_PRODUCTO_CARACTERISTICA
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'Internet Small Business' 
AND EMPRESA_COD = '10') AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA = 'TIPO_ENRUTAMIENTO');

COMMIT;
/
