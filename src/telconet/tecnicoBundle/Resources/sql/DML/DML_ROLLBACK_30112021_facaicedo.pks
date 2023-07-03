
--ELIMINAR CARACTERISTICAS DEL PRODUCTO 'FPS'
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYDATOS' AND ESTADO='Activo' AND EMPRESA_COD=10)
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'FPS' AND ESTADO='Activo');

--ELIMINAR CARACTERISTICA 'FPS'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'FPS';

--ELIMINAR CARACTERISTICAS DEL PRODUCTO 'CODEC'
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYDATOS' AND ESTADO='Activo' AND EMPRESA_COD=10)
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'CODEC' AND ESTADO='Activo');

--ELIMINAR CARACTERISTICA 'CODEC'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'CODEC';

--ELIMINAR CARACTERISTICAS DEL PRODUCTO 'RESOLUCION'
DELETE FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
WHERE PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYDATOS' AND ESTADO='Activo' AND EMPRESA_COD=10)
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'RESOLUCION' AND ESTADO='Activo');

--ELIMINAR CARACTERISTICA 'RESOLUCION'
DELETE FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'RESOLUCION';

COMMIT;
/