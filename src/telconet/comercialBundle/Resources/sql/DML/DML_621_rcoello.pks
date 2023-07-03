--
-- INI - ISB con UM FTTX
--
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
  Le_Error	    EXCEPTION;
BEGIN
  --
----------------------------- INI - Parametro UM FTTX ------------------------------------------
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (	
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval ,
      'PRODUCTOS_ESPECIALES_UM',
      'ULTIMA MILLA PARA PRODUCTOS ESPECIALES COORPORATIVOS',
      'COMERCIAL',
      'NULL',
      'Activo',
      'rcoello',
      sysdate,
      '127.0.0.0',
      NULL,
      NULL,
      NULL
    );
  --
  OPEN C_GetIdParametroCab('PRODUCTOS_ESPECIALES_UM', 'COMERCIAL', 'Activo', 'rcoello');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'UM FTTX',
    'INTERNET SMALL BUSINESS',
    'FTTx',
    'MD' ,
    '18' ,
    'Activo',
    'rcoello',
    sysdate,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'PYMETN',
    '10'
  );
--
-- FIN - ISB con UM FTTX
--
  COMMIT;
  Lv_IdParametroCab := '';
----------------------------- FIN - Parametro UM FTTX------------------------------------------
END;
/
---
---INI - ULTIMA MILLAS - EXCEPCIONES
---

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
  --
  OPEN C_GetIdParametroCab('ULTIMAS_MILLLAS_EXCEPCIONES', 'COMERCIAL', 'Activo', 'arsuarez');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'UM FTTX',
    'FTTx',
    'NULL',
    'NULL' ,
    'NULL' ,
    'Activo',
    'rcoello',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
--
 INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'UM FTTX',
    'FTTx',
    'NULL',
    'NULL' ,
    'NULL' ,
    'Activo',
    'rcoello',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
--
--
  COMMIT;
  Lv_IdParametroCab := '';
----------------------------- FIN - Parametro Restriccion de envio de Correo ------------------------------------------
END;
/
---
---FIN - ULTIMA MILLAS - EXCEPCIONES
---

---
---INI - TIPO DE NEGOCIOS
---

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
----------------------------- INI - Parametro UM FTTX ------------------------------------------
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
    (	
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.nextval ,
      'RESTRICCION_TIPO_NEGOCIO',
      'RESTRINGIR TIPO DE NEGOCIOS PARA ISB',
      'COMERCIAL',
      'NULL',
      'Activo',
      'rcoello',
      sysdate,
      '127.0.0.0',
      NULL,
      NULL,
      NULL
    );
  --
  OPEN C_GetIdParametroCab('RESTRICCION_TIPO_NEGOCIO', 'COMERCIAL', 'Activo', 'rcoello');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'TIPO_NEGOCIO',
    'ISP / Carrier',
    'NULL',
    'TN' ,
    '10' ,
    'Activo',
    'rcoello',
    sysdate,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    NULL
  );
--
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'TIPO_NEGOCIO',
    'Cyber',
    'NULL',
    'TN' ,
    '10' ,
    'Activo',
    'rcoello',
    sysdate,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    NULL
  );
--
--
  COMMIT;
  Lv_IdParametroCab := '';
----------------------------- FIN - Parametro UM FTTX------------------------------------------
END;
/
--
--FIN - TIPO DE NEGOCIOS
--

--
--INI - VELOCIDAD
--

DECLARE
  CURSOR C_GetIdParametroCab(Cv_NombreParametro DB_GENERAL.ADMI_PARAMETRO_CAB.NOMBRE_PARAMETRO%TYPE,
	                     Cv_Modulo          DB_GENERAL.ADMI_PARAMETRO_CAB.MODULO%TYPE, 
	                     Cv_Estado          DB_GENERAL.ADMI_PARAMETRO_CAB.ESTADO%TYPE)
  IS
    SELECT APC.ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB APC
    WHERE APC.NOMBRE_PARAMETRO = Cv_NombreParametro
    AND APC.MODULO             = Cv_Modulo
    AND APC.ESTADO             = Cv_Estado;
  
  Lv_IdParametroCab DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE;
BEGIN
---
  --
  OPEN C_GetIdParametroCab('PROD_VELOCIDAD', 'COMERCIAL', 'Activo');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --
  IF Lv_IdParametroCab > 0 THEN
  --  
    UPDATE ADMI_PARAMETRO_DET
    SET VALOR2 = 'MB'
    WHERE PARAMETRO_ID = Lv_IdParametroCab;
  --
  END IF;
  --
  COMMIT;
  --
  Lv_IdParametroCab := '';
---
END;

--
--FIN - VELOCIDAD
--