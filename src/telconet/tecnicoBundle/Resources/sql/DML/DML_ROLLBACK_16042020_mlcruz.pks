DELETE
FROM db_comercial.admi_producto_caracteristica
WHERE producto_id =
  (SELECT id_producto
  FROM db_comercial.admi_producto
  WHERE descripcion_producto = 'TELCOTEACHER'
  );
DELETE
FROM db_comercial.admi_producto_caracteristica
WHERE producto_id =
  (SELECT id_producto
  FROM db_comercial.admi_producto
  WHERE descripcion_producto = 'IP TELCOTEACHER'
  );
DELETE
FROM db_comercial.admi_producto
WHERE descripcion_producto = 'TELCOTEACHER';
DELETE
FROM db_comercial.admi_producto
WHERE descripcion_producto = 'IP TELCOTEACHER';
DELETE
FROM db_comercial.admi_comision_det
WHERE comision_id =
  (SELECT acca.id_comision
  FROM db_comercial.admi_comision_cab acca,
    db_comercial.admi_producto adpr
  WHERE acca.producto_id        = adpr.id_producto
  AND adpr.descripcion_producto = 'TELCOTEACHER'
  AND acca.usr_creacion         = 'mlcruz'
  );
DELETE
FROM db_comercial.admi_comision_det
WHERE comision_id =
  (SELECT acca.id_comision
  FROM db_comercial.admi_comision_cab acca,
    db_comercial.admi_producto adpr
  WHERE acca.producto_id        = adpr.id_producto
  AND adpr.descripcion_producto = 'IP TELCOTEACHER'
  AND acca.usr_creacion         = 'mlcruz'
  );
DELETE
FROM db_comercial.admi_comision_cab
WHERE producto_id =
  (SELECT id_producto
  FROM db_comercial.admi_producto
  WHERE descripcion_producto = 'TELCOTEACHER'
  );
DELETE
FROM db_comercial.admi_comision_cab
WHERE producto_id =
  (SELECT id_producto
  FROM db_comercial.admi_producto
  WHERE descripcion_producto = 'IP TELCOTEACHER'
  );
DELETE
FROM db_general.admi_parametro_det
WHERE parametro_id =
  (SELECT id_parametro
  FROM db_general.admi_parametro_cab
  WHERE nombre_parametro = 'PRODUCTO_RELACIONADO_SMB'
  )
AND valor1 =
  (SELECT adpr.descripcion_producto
  FROM db_comercial.admi_producto adpr
  WHERE adpr.descripcion_producto = 'TELCOTEACHER'
  AND adpr.empresa_cod            = 10
  );
DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_DET B
WHERE B.PARAMETRO_ID IN
  (SELECT A.ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB A
  WHERE A.NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_TELCOTEACHER'
  );
DELETE
FROM DB_GENERAL.ADMI_PARAMETRO_CAB A
WHERE A.NOMBRE_PARAMETRO = 'PROD_VELOCIDAD_TELCOTEACHER';
COMMIT;
/
