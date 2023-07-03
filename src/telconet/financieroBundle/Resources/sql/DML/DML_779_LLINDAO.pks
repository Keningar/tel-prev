-- DOCUMENTOS CONTABILIZAR 
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'CODIGO_TIPO_DOCUMENTO', 'FAC', 'FAC', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', null);

  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'CODIGO_TIPO_DOCUMENTO', 'FAC', 'FACP', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', null);

  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'CODIGO_TIPO_DOCUMENTO', 'NC', 'NC', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', null);
--
COMMIT;
