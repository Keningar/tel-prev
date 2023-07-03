DECLARE
  
  Ln_TipoCtaCble NUMBER := 0;


BEGIN
  SELECT NVL((SELECT ID_TIPO_CUENTA_CONTABLE
              FROM DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE
              WHERE DESCRIPCION = 'OFICINAS - CENTROS COSTOS'),0) 
  INTO Ln_TipoCtaCble
  FROM DUAL;
  --
  IF Ln_TipoCtaCble = 0 THEN
    insert into DB_FINANCIERO.ADMI_TIPO_CUENTA_CONTABLE (id_tipo_cuenta_contable, descripcion, fe_creacion, usr_creacion, ip_creacion, estado)
    values (DB_FINANCIERO.SEQ_ADMI_TIPO_CUENTA_CONTABLE.NEXTVAL, 'OFICINAS - CENTROS COSTOS', sysdate, 'llindao', '127.0.0.1', 'Activo');
    --
  END IF;
    --
    --
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado, centro_costo)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '10', null, '6110201042', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', '2', 'ARCGMS', Ln_TipoCtaCble, 'FORMA PAGO IMPUESTOS - TELCONET', '10', 2, null, null, sysdate, 'llindao', '127.0.0.1', 'Activo', '200002001');
    
    insert into DB_FINANCIERO.ADMI_CUENTA_CONTABLE (id_cuenta_contable, no_cia, no_cta, cuenta, tabla_referencial, campo_referencial, valor_campo_referencial, nombre_objeto_naf, tipo_cuenta_contable_id, descripcion, empresa_cod, oficina_id, fe_ini, fe_fin, fe_creacion, usr_creacion, ip_creacion, estado, centro_costo)
    values (DB_FINANCIERO.SEQ_ADMI_CUENTA_CONTABLE.NEXTVAL, '10', null, '6110201032', 'INFO_OFICINA_GRUPO', 'ID_OFICINA', '7', 'ARCGMS', Ln_TipoCtaCble, 'FORMA PAGO PROVISION INCOBRABLE - TELCONET', '10', 7, null, null, sysdate, 'llindao', '127.0.0.1', 'Activo', '200002002');
  --
  -- Se asigna centro de costo a cuentas respectivas, no se cambia el tipo de cuenta contable porque el ccosto es el mismo
  UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
  SET ACC.CENTRO_COSTO = '001003001'
  WHERE ACC.NO_CIA = '18'
  AND EXISTS (SELECT NULL
              FROM NAF47_TNET.ARCGMS MS
              WHERE MS.CUENTA = ACC.CUENTA
              AND MS.NO_CIA = ACC.NO_CIA
              AND MS.ACEPTA_CC = 'S');
  --
  -- Se Inactiva cuentas que manejan ccosto de Telconet porque se manejarar con tipo cuenta contable OFICINA - CENTRO COSTO
  UPDATE DB_FINANCIERO.ADMI_CUENTA_CONTABLE ACC
  SET ACC.ESTADO = 'Inactivo'
  WHERE ACC.NO_CIA = '10'
  AND ACC.CAMPO_REFERENCIAL = 'ID_FORMA_PAGO'
  AND ACC.TABLA_REFERENCIAL = 'ADMI_FORMA_PAGO'
  AND ACC.DESCRIPCION NOT LIKE '%VARIOS IMPUESTOS%'
  AND EXISTS (SELECT NULL
              FROM NAF47_TNET.ARCGMS MS
              WHERE MS.CUENTA = ACC.CUENTA
              AND MS.NO_CIA = ACC.NO_CIA
              AND MS.ACEPTA_CC = 'S');
  --
  UPDATE DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET APCD
  SET APCD.TIPO_CUENTA_CONTABLE_ID = Ln_TipoCtaCble
  WHERE APCD.TIPO_CUENTA_CONTABLE_ID = 16
  AND EXISTS (SELECT NULL
              FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB APCC
              WHERE APCC.ID_PLANTILLA_CONTABLE_CAB = APCD.PLANTILLA_CONTABLE_CAB_ID
              AND FORMA_PAGO_ID IN (29,27)
              AND EMPRESA_COD = '10');
  --
  COMMIT;
  --

END;
