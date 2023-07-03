DECLARE
  --
  Lv_CodEmpresa         VARCHAR2(2) := '18';
  Lv_UsrCreacion        VARCHAR2(100) := 'db_financiero';
  --
  Lv_TipoProceso        VARCHAR2(200) := NULL;
  Ln_IdPlantillaContCab NUMBER := NULL;
  Ln_TipoCtaContable    NUMBER := NULL;
  Ln_TipoCtaBancDebMd   NUMBER := NULL;
  Ln_TipoCtaAntMesAnt   NUMBER := NULL;
  Ln_TransGrupalMd      NUMBER := NULL;
  Ln_FpRetIva50         NUMBER := NULL;
  Ln_DeposGrupalMd      NUMBER := NULL;
  Ln_TipoDocFinanciero  NUMBER := NULL;
  --
  --
  FUNCTION F_EXISTE_PLANTILLA ( Pv_Tipoproceso   IN VARCHAR2,
                                Pn_IdFormaPago   IN NUMBER,
                                Pn_TipoDocumento IN NUMBER,
                                Pv_CodEmpresa    IN VARCHAR2) RETURN BOOLEAN IS
    CURSOR C_VERIFICA_PLANTILLA IS
      SELECT PC.ID_PLANTILLA_CONTABLE_CAB
      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PC
      WHERE PC.TIPO_PROCESO = Pv_Tipoproceso
      AND PC.FORMA_PAGO_ID = Pn_IdFormaPago
      AND PC.TIPO_DOCUMENTO_ID = Pn_TipoDocumento
      AND PC.EMPRESA_COD = Pv_CodEmpresa
      ;
    
    Lb_Existe          BOOLEAN := FALSE;
    Ln_IdPlantillaCont NUMBER := NULL;
    --
  BEGIN
    --
    IF C_VERIFICA_PLANTILLA%ISOPEN THEN
      CLOSE C_VERIFICA_PLANTILLA;
    END IF;
    --
    OPEN C_VERIFICA_PLANTILLA;
    FETCH C_VERIFICA_PLANTILLA INTO Ln_IdPlantillaCont;
    Lb_Existe := C_VERIFICA_PLANTILLA%FOUND;
    CLOSE C_VERIFICA_PLANTILLA;
    --
    RETURN Lb_Existe;
    --
  END;
  --
BEGIN
  --
  -- se recupera forma pago transferencia grupal MD
  SELECT NVL((SELECT ID_FORMA_PAGO
              FROM DB_GENERAL.ADMI_FORMA_PAGO A
              WHERE A.CODIGO_FORMA_PAGO = 'TRGM'),0)
   INTO Ln_TransGrupalMd
   FROM DUAL;
  --
  -- se recupera forma pago transferencia grupal MD
  SELECT NVL((SELECT ID_FORMA_PAGO
              FROM DB_GENERAL.ADMI_FORMA_PAGO A
              WHERE A.CODIGO_FORMA_PAGO = 'DPGM'),0)
   INTO Ln_DeposGrupalMd
   FROM DUAL;
  --
  -- se recupera forma pago transferencia grupal MD
  SELECT NVL((SELECT A.ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE A
              WHERE A.DESCRIPCION = 'BANCOS DEBITOS MD'),0)
  INTO Ln_TipoCtaBancDebMd
  FROM DUAL;
    
  -- se recupera forma pago transferencia grupal MD
  SELECT NVL((SELECT ID_FORMA_PAGO 
              FROM DB_GENERAL.ADMI_FORMA_PAGO 
              WHERE DESCRIPCION_FORMA_PAGO = 'RETENCION IVA 50%'),0)
  INTO Ln_FpRetIva50
  FROM DUAL;
  --
  IF Ln_TipoCtaBancDebMd = 0 THEN
    Ln_TipoCtaBancDebMd := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaBancDebMd, 'BANCOS DEBITOS MD', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
  END IF;
  
  

  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 2, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Caja: Cobro de Cheque
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 2, 2, 'CAJA: COBRO CHEQUE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_CP', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 3, 'CAJA: COBRO CHEQUE - DEBITO - CAJA', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CAJA: COBRO CHEQUE - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 2, 3, Lv_CodEmpresa) THEN
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --NULL;
    -- Cab Plantilla - Caja: Cobro de Anticipo por Cheque
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 2, 3, 'CAJA: ANTICIPO GENERADO X COBRO CHEQUE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 3, 'CAJA: ANTICIPO GENERADO X COBRO CHEQUE: DEBITO - CAJA', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago||no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CAJA: ANTICIPO GENERADO X COBRO CHEQUE: CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 2, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo por Cheque
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 2, 3, 'CRUCE ANTICIPO GENERADO X CHEQUE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X CHEQUE - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X CHEQUE - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 2, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Manual Anticipo por Cheque
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 2, 10, 'CRUCE MANUAL ANTICIPO - COBRO CHEQUE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE MANUAL ANTICIPO - COBRO CHEQUE - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE MANUAL ANTICIPO - COBRO CHEQUE - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);

  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 2, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Caja: Cruce Anticipo generado por Cheque
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 2, 11, 'CAJA: CRUCE SALDO ANTICIPO X CHEQUE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    -- Detalle de Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CAJA: CRUCE SALDO ANTICIPO X CHEQUE - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CAJA: CRUCE SALDO ANTICIPO X CHEQUE - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 1, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Caja: Cobro en efectivo
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 1, 2, 'CAJA: COBRO EFECTIVO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_CP', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 3, 'CAJA: COBRO EFECTIVO - DEBITO - CAJA', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CAJA: COBRO EFECTIVO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 1, 3, Lv_CodEmpresa) THEN
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --NULL;
    -- Cab Plantilla - Caja: Anticipo genrado por Cobro Efectivo
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 1, 3, 'CAJA: ANTICIPO GENERADO X COBRO EFECTIVO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 3, 'CAJA: ANTICIPO GENERADO X COBRO EFECTIVO - DEBITO - CAJA', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CAJA: ANTICIPO GENERADO X COBRO EFECTIVO - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 1, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo por Cobro Efectivo
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 1, 3, 'CRUCE ANTICIPO X COBRO EFECTIVO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X COBRO EFECTIVO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X COBRO EFECTIVO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 1, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Manual Anticipo por cobro efectivo
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 1, 10, 'CRUCE MANUAL ANTICIPO X COBRO EFECTIVO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE MANUAL ANTICIPO X COBRO EFECTIVO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE MANUAL ANTICIPO X COBRO EFECTIVO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 1, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Caja: Cruce Saldo Anticipo x Cobro Efectivo
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 1, 11, 'CAJA: CRUCE SALDO ANTICIPO X COBRO EFECTIVO',  Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    -- Detalle de Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CAJA: CRUCE SALDO ANTICIPO X COBRO EFECTIVO - DEBITO - CLIENTES', 'C', to_timestamp('19-08-2016 00:21:13.000000', 'dd-mm-yyyy hh24:mi:ss.ff'), 'amontero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CAJA: CRUCE SALDO ANTICIPO X COBRO EFECTIVO - CREDITO - ANTICIPOS CLIENTES', 'D', to_timestamp('19-08-2016 00:21:13.000000', 'dd-mm-yyyy hh24:mi:ss.ff'), 'amontero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Debito Bancario manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 2, 'DEBITO BANCARIO MANUAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_CP', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'DEBITO BANCARIO MANUAL - DEBITO - BANCOS - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'DEBITO BANCARIO MANUAL - CREDITO - CLIENTES - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado por Debito Bancario manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 3, 'ANTICIPO GENERADO X DEBITO MANUAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'ANTICIPO GENERADO X DEBITO MANUAL - DEBITO - BANCOS - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'D', to_timestamp('10-06-2016 23:06:22.000000', 'dd-mm-yyyy hh24:mi:ss.ff'), 'amontero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEBITO MANUAL - CREDITO - ANTICIPOS CLIENTES - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'MASIVO';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Debito Bancario Automatico
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 2, 'DEBITO BANCARIO AUTOMATICO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_DEB', 'id|hora_actual', 'Activo', 'Pgo. |deb_banco| TELCOS |fe_actual| | varios clientes | |deb_fe_creacion|longitud_250', 'FNKG_CONTABILIZAR_DEBITOS', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'DEBITO BANCARIO AUTOMATICO - DEBITO - BANCOS - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Pgo. |deb_banco| TELCOS |fe_actual| | varios clientes | |TN| |deb_fe_creacion|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'DEBITO BANCARIO AUTOMATICO - CREDITO - CLIENTES - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Pgo. |deb_banco| TELCOS |fe_actual| | varios clientes | |TN|: |deb_oficina| |deb_fe_creacion|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado por Debito Automatico
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 3, 'ANTICIPO GENERADO X DEBITO AUTOMATICO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_ANT', 'id|hora_actual', 'Activo', 'Ant. |deb_banco| TELCOS |fe_actual| | varios clientes | |deb_fe_creacion|longitud_250', 'FNKG_CONTABILIZAR_DEBITOS', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'ANTICIPO GENERADO X DEBITO AUTOMATICO - DEBITO - BANCOS - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Ant. |deb_banco| TELCOS |fe_actual| | varios clientes | |TN| |deb_fe_creacion|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEBITO AUTOMATICO - CREDITO - CLIENTES - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Ant. |deb_banco| TELCOS |fe_actual| | varios clientes | |TN|: |deb_oficina| |deb_fe_creacion|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 4, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado por Debito Automatico
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 4, 'ANTICIPO SIN CLIENTE GENERADO X DEBITO AUTOMATICO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_ANT', 'id|hora_actual', 'Activo', 'Anticipos sin Cliente por débito bancario| |TELCOS| |Varios Clientes| |MD|longitud_250', 'FNKG_CONTABILIZAR_DEBITOS', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'ANTICIPO SIN CLIENTE GENERADO X DEBITO AUTOMATICO - DEBITO - BANCOS - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Ant. |deb_banco| TELCOS |fe_actual| | varios clientes | |TN| |deb_fe_creacion|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO SIN CLIENTE GENERADO X DEBITO AUTOMATICO - CREDITO - CLIENTES - VALOR PAGOS - VALOR NETO - VALOR ANTICIPO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anticipos sin Cliente por débito bancario| |TELCOS| |pag_fe_creacion| |Varios', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce anticipo generado x debito bancario
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 3, 'CRUCE ANTICIPO X DEBITO BANCARIO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X DEBITO BANCARIO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X DEBITO BANCARIO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 4, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce anticipo generado x debito bancario
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 4, 'CRUCE ANTICIPO X DEBITO BANCARIO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X DEBITO BANCARIO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X DEBITO BANCARIO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Anticipo x debito Bancario
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 10, 'CRUCE PARCIAL ANTICIPO X DEBITO BANCARIO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X DEBITO BANCARIO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X DEBITO BANCARIO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 3, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Anticipo x Debito Bancario
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 3, 11, 'CRUCE TOTAL ANTICIPO X DEBITO BANCARIO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X DEBITO BANCARIO - DEBITO - BANCOS', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X DEBITO BANCARIO - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 4, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Bancaria manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 4, 2, 'TRANSFERENCIA BANCARIA MANUAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_CP', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'TRANSFERENCIA BANCARIA MANUAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'TRANSFERENCIA BANCARIA MANUAL - CREDITO - CUENTA CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 4, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado por Transferencia
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 4, 3, 'ANTICIPO GENERADO X TRANSFERENCIA', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'ANTICIPO GENERADO X TRANSFERENCIA - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TRANSFERENCIA - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 4, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo x Transferencia
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 4, 3, 'CRUCE ANTICIPO X TRANSFERENCIA', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X TRANSFERENCIA - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X TRANSFERENCIA - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 4, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Anticipo x Transferencia
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 4, 10, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 4, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Anticipo x Transferencia
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 4, 11, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA - DEBITO - BANCOS', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 5, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Deposito Manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 5, 2, 'DEPOSITO MANUAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_DEPM', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'DEPOSITO MANUAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'DEPOSITO MANUAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'MASIVO';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 5, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Deposito
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 5, 2, 'PROCESO DEPOSITOS', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_DEP', '05|id', 'Activo', 'TELCOS| |dep_fe_proceso| |Deposito| |no_comprobante_deposito| |varios clientes| |dep_nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_DEPOSITOS', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'PROCESO DEPOSITOS - CREDITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |dep_fe_proceso| |Deposito| |no_comprobante_deposito| |varios clientes| |dep_nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 3, 'PROCESO DEPOSITOS - DEBITO - CAJA', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |dep_fe_proceso| |Deposito| |no_comprobante_deposito| |varios clientes| |dep_nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 5, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado x Deposito Manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 5, 3, 'ANTICIPO GENERADO X DEPOSITO MANUAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_ADPM', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'ANTICIPO GENERADO X DEPOSITO MANUAL - CREDITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEPOSITO MANUAL - DEBITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 5, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo x Deposito Manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 5, 3, 'CRUCE ANTICIPO X DEPOSITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X DEPOSITO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X DEPOSITO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 5, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo Parcial x Deposito Manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 5, 10, 'CRUCE PARCIAL ANTICIPO X DEPOSITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X DEPOSITO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X DEPOSITO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 5, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Anticipo x Deposito Manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 5, 11, 'CRUCE ANTICIPO TOTAL X DEPOSITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO TOTAL X DEPOSITO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO TOTAL X DEPOSITO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 7, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Canje
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 7, 2, 'PAGO MANUAL CANJE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_CAN', '020|id_oficina|anio2_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGOS_RET', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'PAGO MANUAL CANJE - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL CANJE - CREDITO - CUENTA CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_100', null);
    --
  END IF;
  
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 7, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado por Canje Manual
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 7, 3, 'ANTICIPO GENERADO X CANJE MANUAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ANT', '039|id_oficina|anio2_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGOS_RET', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'ANTICIPO GENERADO X CANJE MANUAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X CANJE MANUAL - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_100', null);
    --
  END IF;
  
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 7, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cuce de Anticipo x Canje
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 7, 3, 'CRUCE ANTICIPO X CANJE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X CANJE - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X CANJE - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 7, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Anticipo por Canje
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 7, 10, 'CRUCE PARCIAL ANTICIPO X CANJE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X CANJE - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X CANJE - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 7, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Anticipo por Canje
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 7, 11, 'CRUCE TOTAL ANTICIPO X CANJE', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X CANJE - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X CANJE - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 10, 2, Lv_CodEmpresa) THEN
    --NULL;
    -- TIPO CUENTA CONTABLE
    Ln_TipoCtaContable := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaContable, 'DOCUMENTO_X_COBRAR', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Tarjeta de Credito
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 10, 2, 'PAGO MANUAL TARJETA CREDITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_CP', '3|id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaContable, 'PAGO MANUAL TARJETA CREDITO - DEBITO - DOCUMENTO X COBRAR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL TARJETA CREDITO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    -- configuración de la cuenta contable de rubro documentos x cobrar
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, Lv_CodEmpresa, null, '1110301008', 'ADMI_FORMA_PAGO', 'ID_FORMA_PAGO', '10', 'ARCGMS', Ln_TipoCtaContable, 'FORMA PAGO - SIN CLIENTE', Lv_CodEmpresa, null, null, null, SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo');
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 10, 3, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado x Tarjeta Credito
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 10, 3, 'ANTICIPO GENERADO X TARJETA CREDITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ANT', '3|id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaContable, 'ANTICIPO GENERADO X TARJETA CREDITO - DEBITO - DOCUMENTO X COBRAR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TARJETA CREDITO - CREDITO  ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 10, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cuce de Anticipo x Tarjeta Credito
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 10, 3, 'CRUCE ANTICIPO X TARJETA CREDITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X TARJETA CREDITO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X TARJETA CREDITO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 10, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cuce Parcial Anticipo x Tarjeta Credito
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 10, 10, 'CRUCE PARCIAL ANTICIPO TARJETA CREDITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO TARJETA CREDITO - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO TARJETA CREDITO - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 10, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cuce Total Anticipo x Tarjeta Credito
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 10, 11, 'CRUCE TOTAL ANTICIPO TARJETA DE CREDITO', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO TARJETA DE CREDITO - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO TARJETA DE CREDITO - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_DeposGrupalMd, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Deposito Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_DeposGrupalMd, 2, 'PAGO MANUAL DEPOSITO GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_DPGM', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'PAGO MANUAL DEPOSITO GRUPAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL DEPOSITO GRUPAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_DeposGrupalMd, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Generacion de Anticiopo x Deposito Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_DeposGrupalMd, 3, 'ANTICIPO GENERADO X DEPOSITO GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_ADGM', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'ANTICIPO GENERADO X DEPOSITO GRUPAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEPOSITO GRUPAL - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_DeposGrupalMd, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Antipo x Deposito Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_DeposGrupalMd, 3, 'CRUCE ANTICIPO X DEPOSITO GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X DEPOSITO GRUPAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X DEPOSITO GRUPAL - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_DeposGrupalMd, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Antipo x Deposito Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_DeposGrupalMd, 10, 'CRUCE PARCIAL ANTICIPO X DEPOSITO GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X DEPOSITO GRUPAL - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X DEPOSITO GRUPAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_DeposGrupalMd, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Antipo x Deposito Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_DeposGrupalMd, 11, 'CRUCE TOTAL ANTICIPO X DEPOSITO GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X DEPOSITO GRUPAL - CREDITO - ANTICIPOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X DEPOSITO GRUPAL - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_TransGrupalMd, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_TransGrupalMd, 2, 'PAGO MANUAL TRANSFERENCIA GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_CP', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'PAGO MANUAL TRANSFERENCIA GRUPAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL TRANSFERENCIA GRUPAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_TransGrupalMd, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado x Transferencia Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_TransGrupalMd, 2, 'ANTICIPO GENERADO X TRANSFERENCIA GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', Lv_TipoProceso, 'M_CP', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 4, 'ANTICIPO GENERADO X TRANSFERENCIA GRUPAL - DEBITO - BANCOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TRANSFERENCIA GRUPAL - CREDITO - ANTICIPO CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_TransGrupalMd, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Antipo x Transferencia Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_TransGrupalMd, 3, 'CRUCE ANTICIPO X TRANSFERENCIA GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X TRANSFERENCIA GRUPAL - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X TRANSFERENCIA GRUPAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_TransGrupalMd, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Antipo x Transferencia Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_TransGrupalMd, 10, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA GRUPAL - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA GRUPAL - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Ln_TransGrupalMd, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Antipo x Deposito Grupal 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Ln_TransGrupalMd, 11, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA GRUPAL', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA GRUPAL - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA GRUPAL - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  
  ----------------------------------------------------------------------------------------  
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
                FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
                WHERE DESCRIPCION = 'ANTICIPO MES ANTERIOR'),0)
  INTO Ln_TipoCtaAntMesAnt
  FROM DUAL;
  --
  IF NVL(Ln_TipoCtaAntMesAnt,0) = 0 THEN
    -- Se define nuevo tipo de cuenta contable para anticipos generados x meses anteriores
    Ln_TipoCtaAntMesAnt := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaAntMesAnt, 'ANTICIPO MES ANTERIOR', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo');
    --
  END IF;
  ----------------------------------------------------------------------------------------
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 18, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Deposito Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 18, 2, 'DEPOSITO MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_MA', 'id', 'Activo', 'TELCOS| |pag_fe_deposito| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaAntMesAnt, 'DEPOSITO MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'DEPOSITO MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 18, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo Generado x Deposito Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 18, 3, 'ANTICIPO GENERADO X DEPOSITO MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ADMA', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEPOSITO MESES ANTERIORES - DEBITO - ANTICIPO CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEPOSITO MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 18, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo x Deposito Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 18, 3, 'CRUCE ANTICIPO X DEPOSITO MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X DEPOSITO MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X DEPOSITO MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 18, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Anticipo x Deposito Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 18, 10, 'CRUCE PARCIAL ANTICIPO X DEPOSITO MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X DEPOSITO MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X DEPOSITO MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 18, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Anticipo x Deposito Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 18, 11, 'CRUCE TOTAL ANTICIPO X DEPOSITO MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X DEPOSITO MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X DEPOSITO MESES ANTERIORES - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 19, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 19, 2, 'TRANSFERENCIA MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_MA', 'id', 'Activo', 'TELCOS| |pag_fe_deposito| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaAntMesAnt, 'TRANSFERENCIA MESES ANTERIORES - DEBITO - ANTICIPO CIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'TRANSFERENCIA MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 19, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo generado x Transferencia Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 19, 3, 'ANTICIPO GENERADO X TRANSFERENCIA MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TRANSFERENCIA MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TRANSFERENCIA MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 19, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Anticipo x Transferencia Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 19, 3, 'CRUCE ANTICIPO X TRANSFERENCIA MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X TRANSFERENCIA MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X TRANSFERENCIA MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 19, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Parcial Anticipo x Transferencia Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 19, 10, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 19, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total Anticipo x Transferencia Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 19, 11, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |nombre_forma_pago| |numero_cuenta_banco| |USER:| |login| |nombre_oficina|longitud_250', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA MESES ANTERIORES - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 37, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Depósito Grupal Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 37, 2, 'PAGO MANUAL DEPOSITO GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_MADG', 'id', 'Activo', 'TELCOS| |pag_fe_deposito| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaAntMesAnt, 'PAGO MANUAL DEPOSITO GRUPAL MESES ANTERIORES - DEBITO - ANTICIPO CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL DEPOSITO GRUPAL MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 37, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anticipo generado x deposito grupal meses anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 37, 3, 'ANTICIPO GENERADO X DEPOSITO GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_AGMA', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERTADO X DEPOSITO GRUPAL MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X DEPOSITO GRUPAL MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 37, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - cruce de anticipo x deposito Grupal meses anteriore
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 37, 3, 'CRUCE ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES ¿ DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 37, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - cruce parcial anticipo x deposito Grupal meses anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 37, 10, 'CRUCE PARCIAL ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES - CREDITO - CxC CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES ¿ DEBITO - CUENTA ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 37, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Cruce Total deposito Grupal x deposito meses anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 37, 11, 'CRUCE TOTAL ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X DEPOSITO GRUPAL MESES ANTERIORES - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso := 'INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 38, 2, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Grupal Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 38, 2, 'PAGO MANUAL TRANSFERENCIA GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_MA', 'id', 'Activo', 'TELCOS| |pag_fe_deposito| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaAntMesAnt, 'PAGO MANUAL TRANSFERENCIA GRUPAL MESES ANTERIORES - DEBITO - ANTICIPO CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'PAGO MANUAL TRANSFERENCIA GRUPAL MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_deposito| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 38, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Grupal Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 38, 3, 'ANTICIPO GENERADO X TRANSFERENCIA GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_ANT', 'id', 'Activo', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TRANSFERENCIA GRUPAL MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'ANTICIPO GENERADO X TRANSFERENCIA GRUPAL MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  Lv_TipoProceso := 'INDIVIDUAL-CRUCE-ANT';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 38, 3, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Grupal Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 38, 3, 'CRUCE ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '9|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 38, 10, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Grupal Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 38, 10, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '7|id', 'Activo', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES - DEBITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE PARCIAL ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES - CREDITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANTC| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 38, 11, Lv_CodEmpresa) THEN
    --NULL;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Transferencia Grupal Meses Anteriores
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 38, 11, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES', Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', '8|id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'NC');
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES - CREDITO - ANTICIPOS CLIENTES', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE TOTAL ANTICIPO X TRANSFERENCIA GRUPAL MESES ANTERIORES - DEBITO - CLIENTES', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  -----------------------------
  --   PAGO RETENCIONES 1%   --
  -----------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 20
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = PCC.FORMA_PAGO_ID
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND ID_PLANTILLA_CONTABLE_DET != 86
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  -----------------------------
  --   PAGO RETENCIONES 2%   --
  -----------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 8
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = PCC.FORMA_PAGO_ID
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND ID_PLANTILLA_CONTABLE_DET != 83
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  ---------------------------------
  --  PAGO RETENCIONES IVA 10%   --
  ---------------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 23
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = PCC.FORMA_PAGO_ID
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND ID_PLANTILLA_CONTABLE_DET != 68
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  ---------------------------------
  --  PAGO RETENCIONES IVA 100%  --
  ---------------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 22
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = PCC.FORMA_PAGO_ID
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND ID_PLANTILLA_CONTABLE_DET != 71
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  ---------------------------------
  --  PAGO RETENCIONES IVA 20%   --
  ---------------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 24
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = PCC.FORMA_PAGO_ID
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND ID_PLANTILLA_CONTABLE_DET != 65
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  ---------------------------------
  --  PAGO RETENCIONES IVA 50%   --
  ---------------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 24
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = Ln_FpRetIva50
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    Lr_PlantCab.Forma_Pago_Id := Ln_FpRetIva50;
    Lr_PlantCab.Descripcion := REPLACE(Lr_PlantCab.Descripcion, 'RETENCION IVA 20%', 'RETENCION IVA 50%');
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND ID_PLANTILLA_CONTABLE_DET != 65
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        Lr_PlantDet.Descripcion := REPLACE(Lr_PlantDet.Descripcion, 'RETENCION IVA 20%', 'RETENCION IVA 50%');
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  --
  Lv_TipoProceso:= 'MASIVO';
  -- PAG
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 11, 2, Lv_CodEmpresa) THEN
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 11, 2, 'RECAUDACION - PAGOS', '18', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Pagos por Recaudación| |TELCOS| |Varios Clientes| |MD|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'NC');
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'VALOR RECAUDACION - BANCOS', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Pago por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |MD|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'VALOR RECAUDACION - CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Pago por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |MD|longitud_100', null);
    --
  END IF;
  --
  --
  Ln_IdPlantillaContCab := 0;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 11, 3, Lv_CodEmpresa) THEN
    -- ANT 
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 11, 3, 'RECAUDACION - ANTICIPOS', '18', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Anticipos por Recaudación| |TELCOS| |Varios Clientes| |MD|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'NC');
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'VALOR RECAUDACION - BANCOS', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipo por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |MD|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'VALOR RECAUDACION - ANTICIPO CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipo por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |MD|longitud_100', null);
    --
  END IF;
  --

  Ln_IdPlantillaContCab := 0;
  -- ANTS
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 11, 4, Lv_CodEmpresa) THEN
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 11, 4, 'RECAUDACION - ANTICIPOS SIN CLIENTE', '18', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCKMM', 'MIGRA_ARCKML', 'MASIVO', 'M_REC', 'id', 'Activo', 'Anticipos sin Cliente por Recaudación| |TELCOS| |Varios Clientes| |MD|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'NC');
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipoCtaBancDebMd, 'VALOR RECAUDACION - BANCOS', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipos sin Cliente por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |MD|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'VALOR RECAUDACION - ANTICIPO CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Anticipos sin Cliente por Recaudación| |TELCOS| |pag_fe_creacion| |Varios Clientes| |MD|longitud_100', null);
    --
  END IF;
  --
  Ln_IdPlantillaContCab := 0;
  -- APSC
  SELECT NVL((SELECT A.ID_TIPO_DOCUMENTO
              FROM DB_FINANCIERO.ADMI_TIPO_DOCUMENTO_FINANCIERO A
              WHERE UPPER(A.NOMBRE_TIPO_DOCUMENTO) = 'ASIGNA PAGO SIN CLIENTE'), 0)
  INTO Ln_TipoDocFinanciero
  FROM DUAL;
  --
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 11, Ln_TipoDocFinanciero, Lv_CodEmpresa) THEN
    --
    IF Ln_TipoDocFinanciero > 0 THEN
    --
      Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
      --
      insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
      values (Ln_IdPlantillaContCab, 11, Ln_TipoDocFinanciero, 'CRUCE ANTICIPO SIN CLIENTE', '18', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', 'MASIVO', 'M_REC', 'id', 'Activo', 'Cruce Anticipo sin Cliente | |TELCOS| |Recaudación| |MD|longitud_250', 'FNKG_CONTABILIZAR_RECAUDACION', 'ND');
      --
      insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
      values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'VALOR RECAUDACION - ANTICIPO CLIENTES', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Cruce Anticipo sin Cliente| |TELCOS| |fe_actual| |Recaudación| |MD| |longitud_100', null);
      --
      insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
      values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'VALOR RECAUDACION - CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'Cruce Anticipo sin Cliente| |TELCOS| |fe_actual| |Recaudación| |MD| |longitud_100', null);
      --
    END IF;
  END IF;
  --
  --
  Lv_TipoProceso:= 'INDIVIDUAL-CRUCE-ANT';
  Ln_IdPlantillaContCab := 0;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 11, 3, Lv_CodEmpresa) THEN
    -- CRUCE ANT 
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 11, 3, 'CRUCE ANTICIPO POR RECAUDACION', '18', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', 'id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO POR VALOR RECAUDACION - ANTICIPO CLIENTES', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO POR VALOR RECAUDACION - CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;

  Ln_IdPlantillaContCab := 0;
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, 11, 4, Lv_CodEmpresa) THEN
    -- CRUCE ANTS
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, 11, 4, 'CRUCE ANTICIPO POR RECAUDACION SIN CLIENTE', '18', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'CONAP', 'id', 'Activo', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_CRUCEANT', 'DP');
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 2, 'CRUCE ANTICIPO POR VALOR RECAUDACION SIN CLIENTE - ANTICIPO CLIENTES', 'D', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, 'CRUCE ANTICIPO POR VALOR RECAUDACION SIN CLIENTE - CLIENTES', 'C', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS APLICACION ANT| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;


  ------------------------
  --  CRUCE DE ANTICIPO --
  ------------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '10'
                      AND PCC.FORMA_PAGO_ID = 13
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = 13
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;

  --------------------
  --  PAGO EN LINEA --
  --------------------
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PCC
                      WHERE PCC.EMPRESA_COD = '18'
                      AND PCC.FORMA_PAGO_ID = 3
                      AND PCC.DESCRIPCION NOT LIKE '%MANUAL%'
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.FORMA_PAGO_ID = 17
                                      AND B.TIPO_DOCUMENTO_ID = PCC.TIPO_DOCUMENTO_ID
                                      AND B.EMPRESA_COD = Lv_CodEmpresa)) LOOP
    --
    Lr_PlantCab.empresa_cod := Lv_CodEmpresa;
    Lr_PlantCab.usr_creacion := Lv_UsrCreacion;
    Lr_PlantCab.fe_creacion := sysdate;
    Lr_PlantCab.Forma_Pago_Id := 17;
    Lr_PlantCab.Descripcion := REPLACE(REPLACE(Lr_PlantCab.Descripcion, 'DEBITO BANCARIO','PAGO EN LINEA'), 'DEBITO AUTOMATICO', 'PAGO EN LINEA');
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;    
    --
    -- Cabecera plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);

    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab
                                        )) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        Lr_PlantDet.Descripcion := REPLACE(REPLACE(Lr_PlantDet.Descripcion, 'DEBITO BANCARIO','PAGO EN LINEA'), 'DEBITO AUTOMATICO', 'PAGO EN LINEA');
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;

  --
  COMMIT;
  --

END;
/
