DECLARE
  ln_id_param NUMBER;
BEGIN
  SELECT id_parametro
  INTO ln_id_param
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'TIEMPO_PERMITIDO_OPCION';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      ln_id_param,
      'TIEMPO_PERMITIDO_DASHBOARD',
      '20:00',      
      '08:00',
      'Activo',
      'kbaque',
      sysdate,
      '127.0.0.1'
    );
  COMMIT;
END;
