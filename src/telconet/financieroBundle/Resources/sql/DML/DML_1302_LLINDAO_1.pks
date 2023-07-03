DECLARE
  --
  Ln_VariosImp  NUMBER := 0;
  Ln_PlantillaCab NUMBER := 0;
  --
BEGIN
  --
  SELECT NVL((SELECT ID_FORMA_PAGO
              FROM DB_GENERAL.ADMI_FORMA_PAGO
              WHERE DESCRIPCION_FORMA_PAGO = 'VARIOS IMPUESTOS'), 0)
  INTO Ln_VariosImp
  FROM DUAL;
  --
  IF Ln_VariosImp = 0 THEN
    Ln_VariosImp := DB_GENERAL.SEQ_ADMI_FORMA_PAGO.NEXTVAL;
    --
    insert into DB_GENERAL.ADMI_FORMA_PAGO (id_forma_pago, codigo_forma_pago, descripcion_forma_pago, es_depositable, es_monetario, es_pago_para_contrato, estado, usr_creacion, fe_creacion, usr_ult_mod, fe_ult_mod, cta_contable, visible_en_pago, corte_masivo, codigo_sri, tipo_forma_pago)
    values (Ln_VariosImp, 'VAIM', 'VARIOS IMPUESTOS', 'N', 'N', 'N', 'Activo', 'llindao', sysdate, 'llindao', sysdate, null, 'S', 'N', '01', 'RETENCION');
    --
  END IF;
  --
  DBMS_OUTPUT.PUT_LINE('Forma pago Varios Impuestos: '||Ln_VariosImp);
  -----------------------------
  -- PAGO MANUAL VARIOS IMPUESTOS --
  -----------------------------
  Ln_PlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_PlantillaCab, Ln_VariosImp, 2, 'PAGO MANUAL VARIOS IMPUESTOS', '10', SYSDATE, 'llindao', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', 'INDIVIDUAL', 'M_RET', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
  --
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 16, 'PAGO MANUAL VARIOS IMPUESTOS - DEBITO', 'D', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL VARIOS IMPUESTOS – CREDITO - CUENTA CLIENTES', 'C', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'PAGO MANUAL VARIOS IMPUESTOS – CREDITO - ANTICIPO CLIENTES', 'C', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  --
  --
  ---------------------------------------------------
  -- ANTICIPO GENERADO POR PAGO MANUAL VARIOS IMPUESTOS --
  ---------------------------------------------------
  Ln_PlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_PlantillaCab, Ln_VariosImp, 3, 'ANTICIPO MANUAL VARIOS IMPUESTOS', '10', SYSDATE, 'llindao', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', 'INDIVIDUAL', 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
  --
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 16, 'ANTICIPO MANUAL VARIOS IMPUESTOS - DEBITO', 'D', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO MANUAL VARIOS IMPUESTOS – CREDITO - CUENTA ANTICIPOS CLIENTES', 'C', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  --
  --
  ---------------------------------
  -- ANTICIPO MANUAL VARIOS IMPUESTOS --
  ---------------------------------
  Ln_PlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_PlantillaCab, Ln_VariosImp, 11, 'ANTICIPO MANUAL VARIOS IMPUESTOS', '10', SYSDATE, 'llindao', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', 'INDIVIDUAL-CRUCE-ANT', 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
  --
  --
  Insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'ANTICIPO MANUAL VARIOS IMPUESTOS - DEBITO - CLIENTES', 'C', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO MANUAL VARIOS IMPUESTOS – CREDITO - ANTICIPOS CLIENTES', 'D', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  --
  --
  ---------------------------------
  -- ANTICIPO MANUAL VARIOS IMPUESTOS --
  ---------------------------------
  Ln_PlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
  values (Ln_PlantillaCab, Ln_VariosImp, 10, 'CRUCE ANTC MANUAL VARIOS IMPUESTOS', '10', SYSDATE, 'llindao', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', 'INDIVIDUAL-CRUCE-ANT', 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
  --
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTC MANUAL VARIOS IMPUESTOS - CREDITO - CLIENTES', 'C', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
  values (Ln_PlantillaCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTC MANUAL VARIOS IMPUESTOS – DEBITO - ANTICIPOS CLIENTES', 'D', SYSDATE, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
  --
  --
  --
  insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado, centro_costo)
  values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '10', null, '6110201042', 'ADMI_FORMA_PAGO', 'ID_FORMA_PAGO', TO_CHAR(Ln_VariosImp), 'ARCGMS', 16, 'FORMA PAGO - VARIOS IMPUESTOS%', '10', null, null, null, SYSDATE, 'llindao', '127.0.0.1', 'Activo','200001016');
  --
  --
  COMMIT;
  --
  DBMS_OUTPUT.PUT_LINE('Se ha configurado nueva forma de pago VARIOS IMPUESTOS.');
  --
END;
