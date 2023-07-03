DECLARE
  --
  Ln_PlantillaCab NUMBER := 0;
  Ln_PlantillaDet NUMBER := 0;
  --
BEGIN
  --
  SELECT NVL((SELECT ID_PLANTILLA_CONTABLE_CAB
              FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB
              WHERE DESCRIPCION = 'ANTICIPO AUTOMATICO'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_PlantillaCab
  FROM DUAL;
  --
  IF Ln_PlantillaCab = 0 THEN
    --
    Ln_PlantillaCab := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_CAB.NEXTVAL;
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_CAB (id_plantilla_contable_cab, forma_pago_id, tipo_documento_id, descripcion, empresa_cod, fe_creacion, usr_creacion, ip_creacion, tabla_cabecera, tabla_detalle, tipo_proceso, cod_diario, formato_no_docu_asiento, estado, formato_glosa, nombre_paquete_sql, tipo_doc)
    values (Ln_PlantillaCab, null, 10, 'ANTICIPO AUTOMATICO', '10', sysdate, 'llindao', '127.0.0.1', 'MIGRA_ARCGAE', 'MIGRA_ARCGAL', 'INDIVIDUAL', 'M_ANT', '026|id_oficina|anio2_fe_emision|mes_fe_emision|dia_fe_emision', 'Activo', 'TELCOS| |codigo_tipo_documento| : |nombre_forma_pago| |del| |pag_fe_creacion| | - | |nombre_oficina|longitud_240', 'FNKG_CONTABILIZAR_PAGO_MANUAL', 'NC');
    --
  END IF;
  --
  --
  SELECT NVL((SELECT ID_PLANTILLA_CONTABLE_DET
              FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET
              WHERE DESCRIPCION = 'ANTICIPO AUTOMATICO - DEBITO - CXC CLIENTES'
              AND PLANTILLA_CONTABLE_CAB_ID = Ln_PlantillaCab), 0)
  INTO Ln_PlantillaDet
  FROM DUAL;
  --
  IF Ln_PlantillaDet = 0 THEN
    --
    Ln_PlantillaDet := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL;
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_PlantillaCab, Ln_PlantillaDet, 1, 'ANTICIPO AUTOMATICO - DEBITO - CXC CLIENTES', 'D', sysdate, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'telcos aplicacion ant| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  Ln_PlantillaDet := 0;
  --
  --
  SELECT NVL((SELECT ID_PLANTILLA_CONTABLE_DET
              FROM DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET
              WHERE DESCRIPCION = 'ANTICIPO AUTOMATICO - CREDITO - CUENTA ANTICIPOS CLIENTES'
              AND PLANTILLA_CONTABLE_CAB_ID = Ln_PlantillaCab), 0)
  INTO Ln_PlantillaDet
  FROM DUAL;
  --
  IF Ln_PlantillaDet = 0 THEN
    --
    Ln_PlantillaDet := DB_FINANCIERO.SEQ_ADMI_PLANTILLA_CONTAB_DET.NEXTVAL;
    insert into DB_FINANCIERO.ADMI_PLANTILLA_CONTABLE_DET (plantilla_contable_cab_id, id_plantilla_contable_det, tipo_cuenta_contable_id, descripcion, posicion, fe_creacion, usr_creacion, ip_creacion, estado, tipo_detalle, formato_glosa, porcentaje)
    values (Ln_PlantillaCab, Ln_PlantillaDet, 2, 'ANTICIPO AUTOMATICO - CREDITO - CUENTA ANTICIPOS CLIENTES', 'C', sysdate, 'llindao', '127.0.0.1', 'Activo', 'FIJO', 'telcos aplicacion ant| |pag_fe_creacion| |numero_pago| |no_asiento| |nombre_forma_pago| - |nombre_oficina|longitud_100', null);
    --
  END IF;
  --
  --
  COMMIT;
  --
  --
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE(SQLERRM); 
    ROLLBACK;
END;
/

