declare
  Ln_IdPlantillaCab    DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB.ID_PLANTILLA_CONTABLE_CAB%TYPE := 0;
  Ln_TipoCtaContable   DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE.ID_TIPO_CUENTA_CONTABLE%TYPE := 0;
  Ln_TipoDocFinanciero DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO.ID_TIPO_DOCUMENTO%TYPE := 0;
begin
  --------------
  -- TELCONET --
  --------------
  
  -- Parametros Generales
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'FNKG_RECAUDACIONES', 'ESTADO_PAGO', 'Cerrado', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', '10');
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'FNKG_RECAUDACIONES', 'ESTADO_PAGO', 'Pendiente', 'NULL', 'NULL', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', '10');
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'FNKG_RECAUDACIONES', 'CODIGO_TIPO_DOCUMENTO', 'PAG', 'REC', 'MASIVO', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', '10');
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'FNKG_RECAUDACIONES', 'CODIGO_TIPO_DOCUMENTO', 'ANT', 'REC', 'MASIVO', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', '10');
  --
  insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod)
  values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, 544, 'FNKG_RECAUDACIONES', 'CODIGO_TIPO_DOCUMENTO', 'ANTS', 'REC', 'MASIVO', 'Activo', 'DB_FINANCIERO', SYSDATE, '127.0.0.1', null, null, null, 'NULL', '10');

  ---------------
  --PLANTILLAS --
  ---------------
  -- PAG
  Ln_IdPlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_IdPlantillaCab, 11, 2, 'RECAUDACION - PAGOS', '10', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Pagos por Recaudación| |TELCOS| |Varios Clientes| |TN|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'NC');
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'VALOR RECAUDACION - BANCOS', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Pago por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |TN|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'VALOR RECAUDACION - CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Pago por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |TN|longitud_100', null);
  --
  Ln_IdPlantillaCab := 0;
  -- ANT 
  Ln_IdPlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_IdPlantillaCab, 11, 3, 'RECAUDACION - ANTICIPOS', '10', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Anticipos por Recaudación| |TELCOS| |Varios Clientes| |TN|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'NC');
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'VALOR RECAUDACION - BANCOS', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipo por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |TN|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'VALOR RECAUDACION - ANTICIPO CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipo por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |TN|longitud_100', null);
  --
  --
  Ln_IdPlantillaCab := 0;
  -- ANTS
  Ln_IdPlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_IdPlantillaCab, 11, 4, 'RECAUDACION - ANTICIPOS SIN CLIENTE', '10', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Anticipos sin Cliente por Recaudación| |TELCOS| |Varios Clientes| |TN|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'NC');
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'VALOR RECAUDACION - BANCOS', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipos sin Cliente por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |TN|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'VALOR RECAUDACION - ANTICIPO CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipos sin Cliente por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |TN|longitud_100', null);
  --
  --
  -- Tipo Doc: ASIGNACION DE CLIENTE
  Ln_TipoDocFinanciero := DB_FINANCIERO.SEQ_ADMI_TIPO_DOCUMENTO.NEXTVAL;
  --
  insert into DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO (id_tipo_documento, codigo_tipo_documento, nombre_tipo_documento, codigo_tipo_comprob_sri, asociado_a, numero_lineas, estado, afecta_venta, permite_eliminacion, fe_creacion, fe_ult_mod, usr_creacion, usr_ult_mod, movimiento, sumatoria, codigo_tipo_comp_ats_sri)
  values (Ln_TipoDocFinanciero, 'APSC', 'Asigna Pago sin Cliente', null, null, null, 'Activo', null, null, SYSDATE, null, 'DB_FINANCIERO', null, '+', null, null);
  --
  --
  Ln_IdPlantillaCab := 0;
  -- APSC
  Ln_IdPlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_IdPlantillaCab, 11, Ln_TipoDocFinanciero, 'CRUCE ANTICIPO SIN CLIENTE', '10', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Cruce Anticipo sin Cliente | |TELCOS| |Recaudación| |TN|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'ND');
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'VALOR RECAUDACION - ANTICIPO CLIENTES', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Cruce Anticipo sin Cliente| |TELCOS| |fe_actual| |Recaudación| |TN| |longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_IdPlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'VALOR RECAUDACION - CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Cruce Anticipo sin Cliente| |TELCOS| |fe_actual| |Recaudación| |TN| |longitud_100', null);
  --
END;