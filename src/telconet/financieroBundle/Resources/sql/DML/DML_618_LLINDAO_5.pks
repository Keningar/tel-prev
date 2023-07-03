DECLARE
  --
  CURSOR C_OFICINA (Cv_TipoCtaCont NUMBER) IS
    SELECT ID_OFICINA, NOMBRE_OFICINA
    FROM DB_COMERCIAL.INFO_OFICINA_GRUPO OG
    WHERE OG.EMPRESA_ID = '18'
    AND OG.ESTADO = 'Activo'
    AND NOT EXISTS (SELECT NULL
                    FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE CC
                    WHERE CC.VALOR_CAMPO_REFERENCIAL = OG.ID_OFICINA
                    AND CC.NO_CIA = OG.EMPRESA_ID
                    AND CC.TIPO_CUENTA_CONTABLE_ID = Cv_TipoCtaCont);
  --
  CURSOR C_PRODUCTO_OFICINA (Cv_TipoCtaCont NUMBER) IS
    SELECT ID_OFICINA, NOMBRE_OFICINA
    FROM DB_COMERCIAL.INFO_OFICINA_GRUPO OG
    WHERE OG.EMPRESA_ID = '18'
    AND OG.ESTADO = 'Activo'
    AND NOT EXISTS (SELECT NULL
                    FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE CC
                    WHERE CC.OFICINA_ID = OG.ID_OFICINA
                    AND CC.NO_CIA = OG.EMPRESA_ID
                    AND CC.TIPO_CUENTA_CONTABLE_ID = Cv_TipoCtaCont);
  --
  CURSOR C_IMPUESTO_OFICINA (Cv_TipoCtaCont NUMBER,
                             Cv_ImpiestoId  NUMBER) IS
    SELECT ID_OFICINA, NOMBRE_OFICINA
    FROM DB_COMERCIAL.INFO_OFICINA_GRUPO OG
    WHERE OG.EMPRESA_ID = '18'
    AND OG.ESTADO = 'Activo'
    AND NOT EXISTS (SELECT NULL
                    FROM DB_FINANCIERO.ADMI_CUENTA_CONTABLE CC
                    WHERE CC.OFICINA_ID = OG.ID_OFICINA
                    AND CC.NO_CIA = OG.EMPRESA_ID
                    AND CC.TIPO_CUENTA_CONTABLE_ID = Cv_TipoCtaCont
                    AND CC.VALOR_CAMPO_REFERENCIAL = Cv_ImpiestoId);
  --

  --
  Ln_Registros       NUMBER(5) := 0;
  Ln_TipoCtaCont     NUMBER := 0;
  Ln_TipoCtaServicio NUMBER := 0;
  Ln_TipoCtaRetIva50 NUMBER := 0;
  --
BEGIN
  --
  UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE A
  SET A.OFICINA_ID = A.VALOR_CAMPO_REFERENCIAL
  WHERE A.TIPO_CUENTA_CONTABLE_ID = 1
  AND A.NO_CIA = '18'
  AND A.OFICINA_ID IS NULL;
  
  --
  Ln_TipoCtaCont := 3;
  -- Configuración 3:Cuenta Puente - Oficina
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110101001', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'CUENTA CAJA - '||Ofi.NOMBRE_OFICINA, '18', null, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Cuenta Puente - Oficina');
  ---------------
  ---------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 1;
  -- Configuración 1:Clientes - Oficina
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110301001', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'CUENTA CLIENTE - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Clientes - Oficina');
  --

  ---------------
  ---------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 2;
  -- Configuración 1:Anticipo Clientes - Oficina
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '2110301004', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'CUENTA CLIENTE - '||Ofi.NOMBRE_OFICINA, '18', null, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Anticipos Clientes - Oficina');
  --

  -- se verifica nueto tipo de cueta contable
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
                FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
                WHERE DESCRIPCION = 'ANTICIPO MES ANTERIOR'),0)
  INTO Ln_TipoCtaCont
  FROM DUAL;
  --
  IF NVL(Ln_TipoCtaCont,0) = 0 THEN
    -- Se define nuevo tipo de cuenta contable para anticipos generados x meses anteriores
    Ln_TipoCtaCont := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaCont, 'ANTICIPO MES ANTERIOR', SYSDATE, 'DB_FINANCIERO', '127.0.0.1', 'Activo');
    --
  END IF;
  
  -- Configuración 1:Anticipo Clientes Mes Anterior - Oficina
  Ln_Registros := 0;
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --
    --NULL;
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '2110301004', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'CUENTA CLIENTE - '||Ofi.NOMBRE_OFICINA, '18', null, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Anticipos Clientes Mes Anteriore - Oficina');

  -- Configuración 1: IVA - Oficina
  Ln_Registros := 0;
  Ln_TipoCtaCont := 8;
  FOR Ofi IN C_IMPUESTO_OFICINA  (Ln_TipoCtaCont, 1) LOOP
    --
    --NULL;
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '2110101001', 'ADMI_IMPUESTO', 'ID_IMPUESTO', 1, 'ARCGMS', Ln_TipoCtaCont, '12% IVA  VENTAS - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '2110101001', 'ADMI_IMPUESTO', 'ID_IMPUESTO', 3, 'ARCGMS', Ln_TipoCtaCont, '12% IVA  VENTAS - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Iva - Oficina');

  -- Configuración 1: Compensación Solidaria - Oficina
  Ln_Registros := 0;
  Ln_TipoCtaCont := 40;
  FOR Ofi IN C_IMPUESTO_OFICINA  (Ln_TipoCtaCont, 121) LOOP
    --
    --NULL;
    --
    insert into ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '2110101001', 'ADMI_IMPUESTO', 'ID_IMPUESTO', '121', 'ARCGMS', Ln_TipoCtaCont, 'Compensacion IVA  2% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros compensación solidaria Iva - Oficina');


  --
  -- Configuración 1: Productos - Oficina
  Ln_Registros := 0;
  Ln_TipoCtaCont := 7;
  --
  FOR Ofi IN C_PRODUCTO_OFICINA (Ln_TipoCtaCont) LOOP
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '2', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CORREO ELECTRONICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '5', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '46', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP PUBLICA ESTATICA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '65', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: INTERNET PROTEGIDO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '79', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: GASTOS ADMINISTRATIVOS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '89', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: ROUTER WIFI E6500', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '91', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP PUBLICA ESTATICA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1130', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NETLIFE ASSISTANCE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1141', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: PORTAL NETLIFE CAM', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '80', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: OTROS GPON', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '204', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: EQUIPOS VARIOS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4110101008', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '55', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CUENTAS DE NAVEGACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4310101003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '69', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: OTROS CARGOS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4310101003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '234', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: WEBHOSTING', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4310201004', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '56', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: DOMINIO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4410101003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '90', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Office 365 Home', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '70', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: PORTADORES', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '51', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CARTERA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '63', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: INTERNET DEDICADO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '275', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Netlife Zone', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610104002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '864', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Renta SmartWiFi (Aironet 1602)', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610104002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '201', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Smart WiFi (Aironet 1602)', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '54', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CUENTAS CORREO ELECTRONICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '75', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: TARIFARIOS ECUANET', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '53', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CORREO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1017', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Cargo por Gestion de Cobranza', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1143', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Cargo por Reconexion de Servicio', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '58', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: EQUIPO PROTEGIDO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '59', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: HARDWARE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '212', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: I. PROTECCION TOTAL PAID', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '210', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: I. PROTEGIDO MULTI PAID', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '209', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: I. PROTEGIDO MULTI TRIAL', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '66', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP FIJA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '216', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: MATERIALES', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1128', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NetlifeAssistance', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '939', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NetlifeCloud', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1142', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: 24 HORAS STORAGE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1139', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Camara IP', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '78', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NETLIFECAM - Servicio Básico de Visualización Remota Residencial', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1140', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: STORAGE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 41;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Productos - Oficina');
  
  -- Configuración 1: Productos NC - Oficina
  Ln_Registros := 0;
  Ln_TipoCtaCont := 36;
  --
  FOR Ofi IN C_PRODUCTO_OFICINA (Ln_TipoCtaCont) LOOP
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '2', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CORREO ELECTRONICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '5', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '46', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP PUBLICA ESTATICA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '65', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: INTERNET PROTEGIDO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '79', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: GASTOS ADMINISTRATIVOS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '89', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: ROUTER WIFI E6500', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '91', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP PUBLICA ESTATICA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1130', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NETLIFE ASSISTANCE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1141', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: PORTAL NETLIFE CAM', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '80', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: OTROS GPON', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '204', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: EQUIPOS VARIOS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4110101008', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '55', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CUENTAS DE NAVEGACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4310101003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '69', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: OTROS CARGOS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4310101003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '234', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: WEBHOSTING', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4310202001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '56', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: DOMINIO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4410101003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '90', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Office 365 Home', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '70', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: PORTADORES', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '51', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CARTERA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '63', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: INTERNET DEDICADO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610101002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '275', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Netlife Zone', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610104002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '864', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Renta SmartWiFi (Aironet 1602)', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610104002', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '201', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Smart WiFi (Aironet 1602)', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '54', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CUENTAS CORREO ELECTRONICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '75', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: TARIFARIOS ECUANET', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '53', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: CORREO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1017', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Cargo por Gestion de Cobranza', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1143', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Cargo por Reconexion de Servicio', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '58', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: EQUIPO PROTEGIDO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '59', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: HARDWARE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '212', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: I. PROTECCION TOTAL PAID', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '210', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: I. PROTEGIDO MULTI PAID', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '209', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: I. PROTEGIDO MULTI TRIAL', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '66', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: IP FIJA', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '216', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: MATERIALES', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1128', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NetlifeAssistance', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610105003', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '939', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NetlifeCloud', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1142', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: 24 HORAS STORAGE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1139', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: Camara IP', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '78', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: NETLIFECAM - Servicio Básico de Visualización Remota Residencial', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4710104001', 'ADMI_PRODUCTO', 'ID_PRODUCTO', '1140', 'ARCGMS', Ln_TipoCtaCont, 'PRODUCTO: STORAGE', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 41;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Productos NC - Oficina');
  
  -- Configuración 1: Portador - Oficina
  Ln_Registros := 0;
  Ln_TipoCtaCont := 33;
  --
  FOR Ofi IN C_PRODUCTO_OFICINA (Ln_TipoCtaCont) LOOP
    --NULL;

    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '62', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: INSTALACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '68', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: OTROS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610103001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '74', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: SOPORTE TECNICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '92', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: TRASLADO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '93', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: REUBICACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '94', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: TRASLADO PYME O PRO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '213', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: TRASLADO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610103001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '241', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: SOPORTE TECNICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102001', 'ADMI_PRODUCTO', 'ID_PORTADOR', '972', 'ARCGMS', Ln_TipoCtaCont, 'PORTADOR: INSTALACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 8;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Portador - Oficina');

  
  -- se recupera nuevo tipo cuenta cntable SERVICIO
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
              WHERE DESCRIPCION = 'SERVICIO'),0)
  INTO Ln_TipoCtaCont
  FROM DUAL;
  --
  IF Ln_TipoCtaCont = 0 THEN
    --
    Ln_TipoCtaCont := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaCont, 'SERVICIO', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
  END IF;
  --
  -- Configuración 1: Servicio - Oficina
  Ln_Registros := 0;
  --
  FOR Ofi IN C_PRODUCTO_OFICINA (Ln_TipoCtaCont) LOOP
    --NULL;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '93', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: Reubicacion', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --

    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '972', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: INSTALACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610103002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '241', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: SOPORTE TECNICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '213', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: TRASLADO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --

    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '62', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: INSTALACION', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610103002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '74', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: SOPORTE TECNICO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '92', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: TRASLADO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '94', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: TRASLADO PYME O PRO', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado) 
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '4610102002', 'ADMI_PRODUCTO', 'ID_PORTADOR', '68', 'ARCGMS', Ln_TipoCtaCont, 'SERVICIO: OTROS', '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --


    Ln_Registros := Ln_Registros + 4;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Servicio - Oficina');
  ---------------
  ---------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 37;
  -- Configuración 1:Otros Gastos - Oficina
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '9110101001', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'OTROS GASTOS NO DEDUCIBLES - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Otros Gastos - Oficina');
  --
   
  ---------------------------------------------------
  -- Configuración 1: Protestos Clientes - Oficina --
  ---------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 9;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110301006', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'PROTESTO CLIENTES - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Protestos Clientes - Oficina');
  --

  ----------------------------------------------
  -- Configuración 1: Reverso Canje - Oficina --
  ----------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 21;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110301010', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'REVERSA CANJE - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Reverso Canje - Oficina');
  --

  ----------------------------------------------------------
  -- Configuración 1: retencion sobre ventas 1% - Oficina --
  ----------------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 11;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110501008', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 1% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros retencion sobre ventas 1% - Oficina');
  --
  --
  ----------------------------------------------------------
  -- Configuración 1: retencion sobre ventas 10% - Oficina --
  ----------------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 14;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110501006', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 10% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros retencion sobre ventas 10% - Oficina');
  --

  ------------------------------------------------------------
  -- Configuración 1: retencion sobre ventas 100% - Oficina --
  ------------------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 20;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110501005', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 100% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros retencion sobre ventas 100% - Oficina');
  --

  ----------------------------------------------------------
  -- Configuración 1: retencion sobre ventas 2% - Oficina --
  ----------------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 12;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110501002', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 2% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros retencion sobre ventas 2% - Oficina');
  --
  
  -----------------------------------------------------------
  -- Configuración 1: retencion sobre ventas 20% - Oficina --
  -----------------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 17;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110501007', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 20% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros retencion sobre ventas 20% - Oficina');
  --
  
  -----------------------------------------------------------
  -- Configuración 1: retencion sobre ventas 50% - Oficina --
  -----------------------------------------------------------
  -- se verifica si existe retención 50%
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
              WHERE DESCRIPCION = 'RETENCION SOBRE VENTAS 50%'),0)
  INTO Ln_TipoCtaCont
  FROM DUAL;
  --
  IF Ln_TipoCtaCont = 0 THEN
    --
    Ln_TipoCtaCont := DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL;
    --
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 50%', sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
  END IF; 
  --
  Ln_Registros := 0;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110501009', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'RETENCION SOBRE VENTAS 50% - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros retencion sobre ventas 50% - Oficina');
  --
  
  
  -----------------------------------------------------------
  -- Configuración 10: Rverso y reubicacion Pago - Oficina --
  -----------------------------------------------------------
  Ln_Registros := 0;
  Ln_TipoCtaCont := 10;
  --
  FOR Ofi IN C_OFICINA (Ln_TipoCtaCont) LOOP
    --null;
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '18', null, '1110301006', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', Ofi.ID_OFICINA, 'ARCGMS', Ln_TipoCtaCont, 'PROTESTO CLIENTES - '||Ofi.NOMBRE_OFICINA, '18', Ofi.ID_OFICINA, null, null, sysdate, 'db_financiero', '127.0.0.1', 'Activo');
    --
    Ln_Registros := Ln_Registros + 1;
    --
  END LOOP;
  --
  DBMS_OUTPUT.PUT_LINE ('Se configuraron '||Ln_Registros||' registros Protestos Clientes - Oficina');
  --
  COMMIT;
  --
  --
END;
/

