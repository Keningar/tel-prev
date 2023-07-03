DECLARE
  CURSOR C_GetIdParametroCab(Cv_NombreParametro DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
                             Cv_Modulo          DB_GENERAL.ADMI_PARAMETRO_CAB.MODULO%TYPE, 
                             Cv_Estado          DB_GENERAL.ADMI_PARAMETRO_CAB.ESTADO%TYPE, 
                             Cv_UsrCreacion     DB_GENERAL.ADMI_PARAMETRO_CAB.USR_CREACION%TYPE )
  IS
    SELECT APC.ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
    WHERE APC.NOMBRE_PARAMETRO = Cv_NombreParametro
    AND APC.MODULO             = Cv_Modulo
    AND APC.ESTADO             = Cv_Estado
    AND APC.USR_CREACION       = Cv_UsrCreacion;
  
  Lv_IdParametroCab DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE;
BEGIN
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval ,
      'ESTADOS_NO_PERMITIDOS_CRS',
      'Parámetro para almacenar lo estados de servicios no permitidos para realizar cambio de razón social.',
      'FINANCIERO',
      'CAMBIO_RAZON_SOCIAL',
      'Activo',
      'eholguin',
      SYSDATE,
      '0.0.0.0',
      NULL,
      NULL,
      NULL
    );
  --
  OPEN C_GetIdParametroCab('ESTADOS_NO_PERMITIDOS_CRS', 'FINANCIERO', 'Activo', 'eholguin');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO Asignada NO PERMITIDO EN CRS ',
    'Asignada',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO Asignada NO PERMITIDO EN CRS ',
    'Asignada',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO AsignadoTarea NO PERMITIDO EN CRS ',
    'AsignadoTarea',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO AsignadoTarea NO PERMITIDO EN CRS ',
    'AsignadoTarea',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );

  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO Detenido NO PERMITIDO EN CRS ',
    'Detenido',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO Detenido NO PERMITIDO EN CRS ',
    'Detenido',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO PrePlanificada NO PERMITIDO EN CRS ',
    'PrePlanificada',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO PrePlanificada NO PERMITIDO EN CRS ',
    'PrePlanificada',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );


  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO Replanificada NO PERMITIDO EN CRS ',
    'Replanificada',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'ESTADO DE SERVICIO Replanificada NO PERMITIDO EN CRS ',
    'Replanificada',
    NULL,
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    'Campo Valor1 representa el estado no permitido para ejecutar un cambio de razon social.'
  );
  --
  Lv_IdParametroCab := '';
  --
COMMIT;
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
END;
