
--reverso de los detalles de parametros para el formato de la vrf de camaras safecity
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'FORMATO_VRF_SERVICIOS';

--reverso de los detalles de parametros para la cantidad maxima de la vrf por elemento en los servicios camaras safecity
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'MAXIMO_VRF_ELEMENTO_POR_SERVICIOS';

--reverso de los detalles de parametros para el producto camaras safecity
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' AND ESTADO = 'Activo')
    AND VALOR1 = 'PRODUCTO_ADICIONAL_CAMARA';

--reverso de los detalles de parametros para los datos del ws rda para la verificacion de la vrf
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' AND ESTADO = 'Activo')
    AND DESCRIPCION = 'PARAMETROS PARA WS de RDA - Verificacion VRF' AND VALOR1 = 'VERIFICACION_VRF';

--reverso de los detalles de parametros para los nombres tecnicos de los productos
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' AND ESTADO = 'Activo')
    AND VALOR1 IN ('WS_CAMBIO_PLAN_DATOS','WS_CAMBIO_PLAN_INTERNET');

--reverso de los estados de los servicios en la sumatoria de las capacidades
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' AND ESTADO = 'Activo')
    AND VALOR1 = 'ESTADOS_SERVICIOS_PERMITIDOS_TOTAL_BW';

--reverso de los detalles de la lista de los id de los productos para la sumatoria de las capacidades en la red GPON_MPLS
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN' AND ESTADO = 'Activo')
    AND VALOR1 = 'PRODUCTO_TOTAL_BW_WS';

--ELIMINAR CARACTERISTICAS DEL PRODUCTO 'SPID ADMIN'
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO='Activo' AND EMPRESA_COD=10 )
AND CARACTERISTICA_ID IN (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SPID ADMIN' AND ESTADO = 'Activo');

--ELIMINAR CARACTERISTICA 'SPID ADMIN'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SPID ADMIN';

--reverso del detalle de parametro para el id del producto de camara en la red mpls
DELETE DB_GENERAL.ADMI_PARAMETRO_DET
    WHERE PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
          WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' AND ESTADO = 'Activo')
    AND VALOR1 = 'PRODUCTO_CAMARA_MPLS';

COMMIT;
/
