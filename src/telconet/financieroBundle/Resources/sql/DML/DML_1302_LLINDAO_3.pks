Declare
  Ln_IdParametro  NUMBER := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
  Ln_IdDetallePar NUMBER := 0;
Begin

  -- CONFIGURACION EN PARAMETROS
  --
  SELECT NVL((SELECT ID_PARAMETRO
              FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'FORMA_PAGO_CIERRE_CAJA'), 0)
  INTO Ln_IdParametro
  FROM DUAL;
  --
  IF Ln_IdParametro = 0 THEN
    dbms_output.put_line('10');
    Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
    insert into DB_GENERAL.ADMI_PARAMETRO_CAB (id_parametro, nombre_parametro, descripcion, modulo, proceso, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod)
    values (Ln_IdParametro, 'FORMA_PAGO_CIERRE_CAJA', 'CONFIGURA LAS FORMAS DE PAGOS QUE SON PARTE DEL PROCESO CIERRE CAJA', 'FINANCIERO', 'CIERRE_CAJA', 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null);
  END IF;
  --
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'EFEC'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('20');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'EFEC', 'EFECTIVO', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'CHEQ'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('30');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'CHEQ', 'CHEQUE', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TRNG'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('40');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TRNG', 'TRANSFERENCIA GRUPAL', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DEGR'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('50');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DEGR', 'DEPOSITO GRUPAL', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;  
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DEB'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('60');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DEB', 'DEBITO BANCARIO', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DEMA'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('70');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DEMA', 'DEPOSITO MESES ANTERIORES', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DEP'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('80');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DEP', 'DEPOSITO', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DGMA'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('90');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DGMA', 'DEPOSITO GRUPAL MESES ANTERIORES', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RF1'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('100');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RF1', 'RETENCION FUENTE 1%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RF2'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('110');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RF2', 'RETENCION FUENTE 2%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RF8'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('120');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RF8', 'RETENCION FUENTE 8%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RI1'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('130');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RI1', 'RETENCION IVA 10%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RI10'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('140');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RI10', 'RETENCION IVA 100%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RI20'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('150');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RI20', 'RETENCION IVA 20%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RI70'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('160');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RI70', 'RETENCION IVA 70%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'RTIV'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('170');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'RTIV', 'RETENCION IVA 30%', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TARC'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('180');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TARC', 'TARJETA DE CREDITO', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TGMA'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('190');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TGMA', 'TRANSFERENCIA GRUPAL MESES ANTERIORES', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TRAN'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('200');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TRAN', 'TRANSFERENCIA', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TRMA'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('210');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TRMA', 'TRANSFERENCIA MESES ANTERIORES', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'FDCM'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'FDCM', 'FIDEICOMISO', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'VAIM'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('220');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'VAIM', 'VARIOS IMPUESTOS', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '10', null, null, null);
  END IF;
  --
  --
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'EFEC'
              AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('230');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'EFEC', 'EFECTIVO', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '18', null, null, null);
  END IF;
  --
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'CHEQ'
              AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('240');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'CHEQ', 'CHEQUE', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '18', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TRNG'
              AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('250');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TRNG', 'TRANSFERENCIA GRUPAL', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '18', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DEGR'
              AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('260');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DEGR', 'DEPOSITO GRUPAL', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '18', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'DGMA'
              AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    dbms_output.put_line('270');
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'DGMA', 'DEPOSITO GRUPAL MESES ANTERIORES', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '18', null, null, null);
  END IF;
  --
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'FORMA PAGO'
              AND VALOR1 = 'TGMA'
              AND EMPRESA_COD = '18'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
   dbms_output.put_line('280');
   insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
   values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'FORMA PAGO', 'TGMA', 'TRANSFERENCIA GRUPAL MESES ANTERIORES', null, null, 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, null, '18', null, null, null);
  END IF;
--
  DELETE DB_GENERAL.ADMI_PARAMETRO_DET
  WHERE PARAMETRO_ID = 0;
  --
  DELETE DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE ID_PARAMETRO = 0;
  --
  COMMIT;
  --
END;
