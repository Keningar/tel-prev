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
  Le_Exception        EXCEPTION;
BEGIN
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval ,
      'MAX_IDENTIFICACION',
      'PERMITE OBTENER LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
      'COMERCIAL',
      'MAX_IDENTIFICACION',
      'Activo',
      'eholguin',
      sysdate,
      '0.0.0.0',
      NULL,
      NULL,
      NULL
    );
  --
  IF C_GetIdParametroCab%ISOPEN THEN CLOSE C_GetIdParametroCab; END IF;

  OPEN C_GetIdParametroCab('MAX_IDENTIFICACION', 'COMERCIAL', 'Activo', 'eholguin');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  IF C_GetIdParametroCab%NOTFOUND THEN
    RAISE Le_Exception;
  END IF;
  CLOSE C_GetIdParametroCab;
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
    'CED',
    'ECUADOR',
    '10' ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
--
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
    'RUC',
    'ECUADOR',
    '13' ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
--
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
    'PAS',
    'ECUADOR',
    '20' ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
  
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
    'CED',
    'PANAMA',
    '12' ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  ); 
--
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
    'RUC',
    'PANAMA',
    '50' ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
  
--
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA LA LONGITUD MAXIMA DEL CAMPO IDENTIFICACION SEGUN EL TIPO Y PAIS',
    'PAS',
    'PANAMA',
    '20' ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );  
  --
  Lv_IdParametroCab := '';
  --
  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR2='50' WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'RUC_PANAMA_GENERAL');
  
COMMIT;
EXCEPTION
WHEN Le_Exception THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
WHEN OTHERS THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
END;