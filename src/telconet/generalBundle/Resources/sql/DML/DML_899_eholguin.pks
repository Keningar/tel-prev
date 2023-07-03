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
      'MAX_NUMERO_CONTACTOS',
      'PARAMETRO QUE CONTIENE EL VALOR DEL NUMERO DE CONTACTOS A VISUALIZAR EN LA BARRA INFORMATIVA',
      'COMERCIAL',
      'CONTACTOS',
      'Activo',
      'eholguin',
      sysdate,
      '0.0.0.0',
      NULL,
      NULL,
      NULL
    );
  --
  OPEN C_GetIdParametroCab('MAX_NUMERO_CONTACTOS', 'COMERCIAL', 'Activo', 'eholguin');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'PARAMETRO DONDE SE CONFIGURA EL VALOR DEL NUMERO DE CONTACTOS A VISUALIZAR EN LA BARRA INFORMATIVA',
    'MAX_CONTACTOS',
    '5',
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    sysdate,
    '0.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
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
