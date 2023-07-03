--=======================================================================
-- Reverso aprovisionamiento de subredes SAFECITY
--=======================================================================

--actualizar producto id y uso de la vlan del producto 'DATOS GPON VIDEO ANALYTICS CAM'
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR3 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
               WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
    VALOR1 = 'VLAN_SAFECITY_GPON'
WHERE ID_PARAMETRO_DET = (
  SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET
  INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB ON CAB.ID_PARAMETRO = DET.PARAMETRO_ID
  WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
  AND DET.DESCRIPCION = 'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY'
  AND DET.VALOR1 = 'VLAN SAFECITY GPON'
);

--actualizar url de configuración del producto 'DATOS GPON VIDEO ANALYTICS CAM'
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR1 = 'rtsp://admin:{{password}}#@{{ipCamara}}:puertortsp/Streaming/Channels/PATH'
WHERE ID_PARAMETRO_DET = (
  SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET
  INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB ON CAB.ID_PARAMETRO = DET.PARAMETRO_ID
  WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'
  AND DET.DESCRIPCION = 'FORMATO_URL_CAMARA_SAFECITY'
);

--Reverso el detalle para la vlan del producto 'SAFE VIDEO ANALYTICS CAM'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET PDE WHERE PDE.ID_PARAMETRO_DET IN (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
    ) AND DET.DESCRIPCION = 'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY'
    AND DET.VALOR3 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                       WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' )
);

--Reverso el detalle para el nombre del uso de la subred del producto 'SAFE VIDEO ANALYTICS CAM'
DELETE DB_GENERAL.ADMI_PARAMETRO_DET PDE WHERE PDE.ID_PARAMETRO_DET IN (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
    ) AND DET.DESCRIPCION = 'PARAMETRO USO SUBRED PARA SERVICIOS ADICIONALES SAFECITY'
    AND DET.VALOR1 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                       WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' )
);

--DELETE SUBREDES
DELETE DB_INFRAESTRUCTURA.INFO_IP WHERE SUBRED_ID IN (SELECT ID_SUBRED FROM DB_INFRAESTRUCTURA.INFO_SUBRED WHERE USO = 'SAFECITYGPON');
DELETE DB_INFRAESTRUCTURA.INFO_SUBRED WHERE USO = 'SAFECITYGPON';

COMMIT;
/
