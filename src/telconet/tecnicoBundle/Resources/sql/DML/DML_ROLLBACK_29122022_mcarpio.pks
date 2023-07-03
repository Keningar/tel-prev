/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para rollback caracteristica elemento adiconales en el nodo y cliente servicios safe city
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 
 * @since 9-1-2023 - Versión Inicial.
 */
 
--SW POE
DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_CLIENTE_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1443' and c.estado = 'Activo');
      

DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_NODO_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1443' and c.estado = 'Activo');
      

--WIFI GPON
DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE  c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_CLIENTE_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1442' and c.estado = 'Activo');
      

DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_NODO_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1442' and c.estado = 'Activo');
    

--CAMARAS
DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_CLIENTE_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1402' and c.estado = 'Activo');
      

DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_NODO_ID' 
      and d.estado = 'Activo') 
      AND c.producto_id = '1402' and c.estado = 'Activo');
      

--DATOS GPON
DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_CLIENTE_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1401' and c.estado = 'Activo');
      

DELETE FROM DB_COMERCIAL.admi_producto_caracteristica 
WHERE id_producto_caracterisitica in(SELECT id_producto_caracterisitica FROM DB_COMERCIAL.admi_producto_caracteristica C 
      WHERE c.caracteristica_id in(SELECT d.id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_NODO_ID' 
      and d.estado = 'Activo')
      AND c.producto_id = '1401' and c.estado = 'Activo');
      

--CARACTERISTICA CLIENTE ADD
DELETE FROM DB_COMERCIAL.admi_caracteristica 
WHERE id_caracteristica in(SELECT id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D 
          WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_CLIENTE_ID' and d.estado = 'Activo');
      

--CARACTERISTICA NODO ADD
DELETE FROM DB_COMERCIAL.admi_caracteristica 
WHERE id_caracteristica in(SELECT id_caracteristica FROM DB_COMERCIAL.admi_caracteristica D 
          WHERE d.descripcion_caracteristica = 'ELEMENTO_ADD_NODO_ID' and d.estado = 'Activo');


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para rollback cabecera y detalle de parametros para tipos de elemento cliente y nodo
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 
 * @since 9-1-2023 - Versión Inicial.
 */
 
 DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE descripcion = 'ELEMENTO_ADICIONAL_CLIENTE'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
  
 
 DELETE FROM DB_GENERAL.admi_parametro_det 
  WHERE descripcion = 'ELEMENTO_ADICIONAL_NODO'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
  

 DELETE FROM db_general.admi_parametro_cab 
  WHERE nombre_parametro = 'ELEMENTOS_ADICIONALES_CLIENTE_NODO'
  AND ESTADO = 'Activo'
  AND USR_CREACION = 'mcarpio';
  

COMMIT;

/