DECLARE
  --
  Lv_CodEmpresa         VARCHAR2(2) := '18';
  Lv_UsrCreacion        VARCHAR2(30) := 'db_financiero';
  Lv_TipoProceso        VARCHAR2(200) := NULL;
  Lv_DescPlantilla      VARCHAR2(200) := NULL;
  Ln_IdPlantillaContCab NUMBER := NULL;
  Ln_TipoCtaContable    NUMBER := NULL;
  Ln_TipoCtaAntMesAnt   NUMBER := NULL;
  Ln_TransGrupalMd      NUMBER := NULL;
  Ln_DeposGrupalMd      NUMBER := NULL;
  Ln_TipoCtaRetIva50    NUMBER := NULL;
  Ln_NdTipoCtaId        NUMBER := NULL;
  Ln_TipCtaContServicio NUMBER := NULL;
  Ln_TipCtaContPortador NUMBER := NULL;
  --
  --
  FUNCTION F_EXISTE_PLANTILLA ( Pv_Tipoproceso   IN VARCHAR2,
                                Pv_DescPlantilla IN VARCHAR2,
                                Pn_TipoDocumento IN NUMBER,
                                Pv_CodEmpresa    IN VARCHAR2) RETURN BOOLEAN IS
    CURSOR C_VERIFICA_PLANTILLA IS
      SELECT PC.ID_PLANTILLA_CONTABLE_CAB
      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB PC
      WHERE PC.TIPO_PROCESO = Pv_Tipoproceso
      AND PC.DESCRIPCION = Pv_DescPlantilla
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

  -- se recupera nuevo tipo cuenta contable SERVICIO
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
              WHERE DESCRIPCION = 'SERVICIO'),0)
  INTO Ln_TipCtaContServicio
  FROM DUAL;
  --
  IF Ln_TipCtaContServicio = 0 THEN
    --
    Ln_TipCtaContServicio := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipCtaContServicio, 'SERVICIO', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
  END IF;
  --
  -- se recupera nuevo tipo cuenta contable PORTADOR MD
/*  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
              WHERE DESCRIPCION = 'PORTADOR MD'),0)
  INTO Ln_TipCtaContPortador
  FROM DUAL;
  --
  IF Ln_TipCtaContPortador = 0 THEN
    --
    Ln_TipCtaContPortador := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipCtaContPortador, 'PORTADOR MD', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
  END IF;
*/  --
  Ln_TipCtaContPortador := 33;
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'FACTURA MASIVA CON IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 1, Lv_CodEmpresa) THEN
    --NULL;
    -- TIPO CUENTA CONTABLE
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Factura con IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 1, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_F_1', '1|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Facturación con IVA|fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Facturación con IVA|fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 7, Lv_DescPlantilla||' - PRODUCTOS', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Facturación con IVA|fe_emision|-|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Facturación con IVA|fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Facturación con IVA|fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 8, Lv_DescPlantilla||' - IVA ', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Facturación con IVA|fe_emision|-|nombre_oficina', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'FACTURA MASIVA SIN IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 1, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Factura sin IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 1, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_F_1', '2|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Facturación sin IVA|fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Facturación sin IVA|fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 7, Lv_DescPlantilla||' - PRODUCTOS', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Facturación sin IVA|fe_emision|-|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Facturación sin IVA|fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Facturación sin IVA|fe_emision|-|nombre_oficina', 50);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'NOTA DE CREDITO MASIVA CON IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 6, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Nota Crédito con IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 6, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_NC1', '5|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Nota de Crédito con IVA mes del |fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 36, Lv_DescPlantilla||' - PRODUCTOS NC', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 8, Lv_DescPlantilla||' - IVA ', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Facturación con IVA|fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 40, Lv_DescPlantilla||' - COMPENSACION SOLIDARIA ', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Facturación con IVA|fe_emision|-|nombre_oficina', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'NOTA DE CREDITO SIN IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 6, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Nota Crédito sin IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 6, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_NC1', '6|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Nota de Crédito sin IVA mes del |fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 36, Lv_DescPlantilla||' - PRODUCTOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'ANULACION DE FACTURAS MASIVAS CON IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 1, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Factura con IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 1, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_F_2', '3|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Anulacion de Facturación con IVA mes del |fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulacion de Facturación con IVA mes del |fe_emision| - |nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 7, Lv_DescPlantilla||' - PRODUCTOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulacion de Facturación con IVA mes del |fe_emision| - |nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulacion de Facturación con IVA mes del |fe_emision| - |nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulacion de Facturación con IVA mes del |fe_emision| - |nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 8, Lv_DescPlantilla||' - IVA ', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulacion de Facturación con IVA mes del |fe_emision| - |nombre_oficina', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'ANULACION DE FACTURAS MASIVAS SIN IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 1, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Factura con IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 1, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_F_2', '4|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Anulacion Facturación sin IVA mes del |fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulacion de Facturación sin IVA mes del |fe_emision| - |nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 7, Lv_DescPlantilla||' - PRODUCTOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulacion de Facturación sin IVA mes del |fe_emision| - |nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulacion de Facturación sin IVA mes del |fe_emision| - |nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulacion de Facturación sin IVA mes del |fe_emision| - |nombre_oficina', 50);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'INDIVIDUAL';
  Lv_DescPlantilla := 'NOTA DE CREDITO INTERNA INDIVIDUAL';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 8, Lv_CodEmpresa) THEN
    --NULL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Nota de Crédito Interna
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 8, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_NCI', '010|id_oficina|id_documento', 'Activo', 'TELCOS |fe_emision| |numero_factura_sri| |Ajuste Interno NCI Login: |login_pto|nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 37, Lv_DescPlantilla||' - OTROS GASTOS', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS |fe_emision| |numero_factura_sri| |Ajuste Interno NCI Login: |login_pto|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'TELCOS |fe_emision| |numero_factura_sri| |Ajuste Interno NCI Login: |login_pto|nombre_oficina', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'ANULACION DE NOTA DE CREDITO MASIVA CON IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 6, Lv_CodEmpresa) THEN
    --NULL;
    -- TIPO CUENTA CONTABLE
    -- se recupera nuevo tipo cuenta contable SERVICIO
    SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
                FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
                WHERE DESCRIPCION = 'SERVICIO'),0)
    INTO Ln_TipoCtaContable
    FROM DUAL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anulación Nota Crédito con IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 6, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_NC2', '7|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Anulación de Nota de Crédito con IVA mes del |fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulación de Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 36, Lv_DescPlantilla||' - PRODUCTOS', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulación de Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulación de Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContPortador, Lv_DescPlantilla||' - PORTADOR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulación de Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 8, Lv_DescPlantilla||' - IVA ', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulación de Nota de Crédito con IVA mes del |fe_emision|-|nombre_oficina', null);
    --
  END IF;
  --
  --
  Lv_TipoProceso   := 'MIXTO';
  Lv_DescPlantilla := 'ANULACION DE NOTA DE CREDITO MASIVA SIN IVA';
  --
  IF NOT F_EXISTE_PLANTILLA (Lv_TipoProceso, Lv_DescPlantilla, 6, Lv_CodEmpresa) THEN
    --NULL;
    -- TIPO CUENTA CONTABLE
    -- se recupera nuevo tipo cuenta contable SERVICIO
    SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
                FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
                WHERE DESCRIPCION = 'SERVICIO'),0)
    INTO Ln_TipoCtaContable
    FROM DUAL;
    --
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- Cab Plantilla - Anulación Nota Crédito con IVA
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, null, 6, Lv_DescPlantilla, Lv_CodEmpresa, sysdate, 'db_financiero', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', Lv_TipoProceso, 'M_NC2', '8|id_oficina|anio_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'Anulación de Nota de Crédito con IVA mes del |fe_emision| - |nombre_oficina', null, null);
    --  
    -- Detalle Plantilla
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 1, Lv_DescPlantilla||' - CUENTAS X COBRAR', 'D', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulación de Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', null);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 36, Lv_DescPlantilla||' - PRODUCTOS', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulación de Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', 100);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContServicio, Lv_DescPlantilla||' - SERVICIO', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulación de Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Ln_TipCtaContportador, Lv_DescPlantilla||' - PORTADOR', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'VARIABLE', 'Anulación de Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', 50);
    --
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, 8, Lv_DescPlantilla||' - IVA ', 'C', sysdate, 'db_financiero', '127.0.0.1', 'Activo', 'FIJO', 'Anulación de Nota de Crédito sin IVA mes del |fe_emision|-|nombre_oficina', null);
    --
  END IF;
  
  -- se verifica si existe retención 50%
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
              WHERE DESCRIPCION = 'RETENCION SOBRE VENTAS 50%'),0)
  INTO Ln_TipoCtaRetIva50
  FROM DUAL;
  --
  IF Ln_TipoCtaRetIva50 = 0 THEN
    --
    Ln_TipoCtaRetIva50 := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaRetIva50, 'RETENCION SOBRE VENTAS 50%', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
  END IF; 
  --
  FOR Lr_PlantCab IN (SELECT *
                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB A
                      WHERE A.EMPRESA_COD = '10'
                      AND A.TIPO_DOCUMENTO_ID = 9
                      AND A.ID_PLANTILLA_CONTABLE_CAB NOT IN (62,65)
                      AND NOT EXISTS (SELECT NULL
                                      FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB B
                                      WHERE B.TIPO_DOCUMENTO_ID = A.TIPO_DOCUMENTO_ID
                                      AND DECODE(B.DESCRIPCION,'NOTA DE DEBITO INDIVIDUAL - REVERSA RETENCION 50%','NOTA DE DEBITO INDIVIDUAL - REVERSA RETENCION 70%', B.DESCRIPCION) = A.DESCRIPCION
                                      AND B.EMPRESA_COD = '18')) LOOP
    --
    Lr_PlantCab.Empresa_Cod := Lv_CodEmpresa;
    Lr_PlantCab.Usr_Creacion := Lv_UsrCreacion;
    Lr_PlantCab.Fe_Creacion := sysdate;
    Ln_IdPlantillaContCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    -- retencion 70% tELCONET se usara Para generar retencion 50% Megadatos
    IF Lr_PlantCab.Id_Plantilla_Contable_Cab = 66 THEN
      Lr_PlantCab.Descripcion := REPLACE(Lr_PlantCab.Descripcion, 'RETENCION 70%','RETENCION 50%');
    END IF;
    --
    -- Cab Plantilla 
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_IdPlantillaContCab, Lr_PlantCab.forma_pago_id, Lr_PlantCab.tipo_documento_id, Lr_PlantCab.descripcion, Lr_PlantCab.empresa_cod, Lr_PlantCab.fe_creacion, Lr_PlantCab.usr_creacion, Lr_PlantCab.ip_creacion, Lr_PlantCab.tabla_cabecera, Lr_PlantCab.tabla_detalle, Lr_PlantCab.tipo_proceso, Lr_PlantCab.cod_diario, Lr_PlantCab.formato_no_docu_asiento, Lr_PlantCab.estado, Lr_PlantCab.formato_glosa, Lr_PlantCab.nombre_paquete_sql, Lr_PlantCab.tipo_doc);
    --
    -- Detalle plantilla
    FOR Lr_PlantDet in (SELECT *
                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET PCD
                        WHERE PCD.PLANTILLA_CONTABLE_CAB_ID = Lr_PlantCab.id_plantilla_contable_cab
                        AND NOT EXISTS (SELECT NULL
                                        FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET B
                                        WHERE B.TIPO_CUENTA_CONTABLE_ID = PCD.TIPO_CUENTA_CONTABLE_ID
                                        AND B.PLANTILLA_CONTABLE_CAB_ID = Ln_IdPlantillaContCab)) LOOP
        --
        Lr_PlantDet.Usr_Creacion := Lv_UsrCreacion;
        Lr_PlantDet.Fe_Creacion := sysdate;
        Lr_PlantDet.Descripcion := REPLACE(Lr_PlantDet.Descripcion, 'RETENCION IVA 70%', 'RETENCION IVA 50%');
        
        -- retencion 70% tELCONET se usara Para generar retencion 50% Megadatos
        IF Lr_PlantCab.Id_Plantilla_Contable_Cab = 66 THEN
          IF Lr_PlantDet.Posicion = 'C' THEN
            Lr_PlantDet.Tipo_Cuenta_Contable_Id := Ln_TipoCtaRetIva50;
          END IF;
        END IF;
        --
        IF Lr_PlantCab.Descripcion = 'AJUSTE INTERNO POR REVERSA DE PAGO' THEN
          IF Lr_PlantDet.Posicion = 'C' THEN
            Lr_PlantDet.Tipo_Cuenta_Contable_Id := 4;
          END IF;
        END IF;
        --
        insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
        values (Ln_IdPlantillaContCab, DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL, Lr_PlantDet.tipo_cuenta_contable_id, Lr_PlantDet.descripcion, Lr_PlantDet.posicion, Lr_PlantDet.fe_creacion, Lr_PlantDet.usr_creacion, Lr_PlantDet.ip_creacion, Lr_PlantDet.estado, Lr_PlantDet.tipo_detalle, Lr_PlantDet.formato_glosa, Lr_PlantDet.porcentaje);
      --
    END LOOP;
  END LOOP;
  --
  
  --
  COMMIT;
  --

END;
/
