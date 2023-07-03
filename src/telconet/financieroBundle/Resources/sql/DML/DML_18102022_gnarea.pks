insert into db_comprobantes.info_campo_adicional (id_campo_adicional, empresa_id, tipo_doc_id, codigo, etiqueta,
descripcion, posicion, estado, fe_creacion, usr_creacion, ip_creacion) values 
(db_comprobantes.seq_info_campo_adicional.nextval, 
(select id_empresa from db_comprobantes.admi_empresa where codigo = 'TN'), 
(select id_tipo_doc from db_comprobantes.admi_tipo_documento where descripcion = 'factura'),
'detalleAdicional', 'Detalle Adicional', null, 
(select max(posicion)+1 from db_comprobantes.info_campo_adicional where tipo_doc_id= 1 and  empresa_id = 1), 
'Activo', sysdate, 'admin', '127.0.0.1');
commit;