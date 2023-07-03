
--REVERSO PRODUCTOS PARA SOPORTE MASIVO DE CANCELACIONES
UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET SOPORTE_MASIVO = NULL WHERE ID_PRODUCTO IN (1450,1451);

--REVERSO PARAMETRO DE LA RELACION IP INTERNET VPNoGPON y INTERNET VPNoGPON
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR4 = '2', VALOR7 = NULL WHERE ID_PARAMETRO_DET = '27028';

--DELETE ADMI_PRODUCTO_CARACTERISTICA 'RELACION_INTERNET_VPNoGPON' AL PRODUCTO 'IP INTERNET VPNoGPON'
DELETE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
WHERE PRODUCTO_ID = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'IP INTERNET VPNoGPON' AND ESTADO = 'Activo' )
AND CARACTERISTICA_ID = ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_INTERNET_VPNoGPON' AND ESTADO = 'Activo' );

--DELETE CARACTERISTICA 'RELACION_INTERNET_VPNoGPON'
DELETE DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_INTERNET_VPNoGPON' AND ESTADO = 'Activo';

COMMIT;
/
