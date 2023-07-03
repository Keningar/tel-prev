DECLARE
  Ln_IdParametro  NUMBER := 0;
  Ln_IdDetallePar NUMBER := 0;
BEGIN
  -------------------------------------------------------------
  -- CONFIGURACION DE PARAMETRO CONTABILZA ANTC POR EMPRESA  --
  -------------------------------------------------------------
  
  SELECT NVL((SELECT ID_PARAMETRO
              FROM DB_GENERAL.ADMI_PARAMETRO_CAB
              WHERE NOMBRE_PARAMETRO = 'VALIDACIONES_PROCESOS_CONTABLES'), 0)
  INTO Ln_IdParametro
  FROM DUAL;
  --
  IF Ln_IdParametro = 0 THEN
    --
    Ln_IdParametro := DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL;
    insert into DB_GENERAL.ADMI_PARAMETRO_CAB (id_parametro, nombre_parametro, descripcion, modulo, proceso, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod)
    values (Ln_IdParametro, 'VALIDACIONES_PROCESOS_CONTABLES', 'VALIDACIONES PERMITIDAS EN EL PROCESO DE CREACION DE PAGOS, ANTICIPOS Y/O PROCESO DE DEPOSITOS', 'FINANCIERO', 'PAGOS_DEPOSITOS', 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null);
    --
  END IF;

  --
  -- contabiliza ANTC
  SELECT NVL((SELECT ID_PARAMETRO_DET
              FROM DB_GENERAL.ADMI_PARAMETRO_DET
              WHERE PARAMETRO_ID = Ln_IdParametro
              AND DESCRIPCION = 'CONTABILIZA_ANTICIPO_NOTA_CREDITO'
              AND EMPRESA_COD = '10'), 0)
  INTO Ln_IdDetallePar
  FROM DUAL;
  --
  IF Ln_IdDetallePar = 0 THEN
    insert into DB_GENERAL.ADMI_PARAMETRO_DET (id_parametro_det, parametro_id, descripcion, valor1, valor2, valor3, valor4, estado, usr_creacion, fe_creacion, ip_creacion, usr_ult_mod, fe_ult_mod, ip_ult_mod, valor5, empresa_cod, valor6, valor7, observacion)
    values (DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL, Ln_IdParametro, 'CONTABILIZA_ANTICIPO_NOTA_CREDITO', 'SI', 'NULL', 'NULL', 'NULL', 'Activo', 'llindao', sysdate, '0.0.0.0', null, null, null, 'NULL', '10', null, null, null);
  END IF;
  ---
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

