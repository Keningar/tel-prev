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
  
  Lv_IdParametroCabRenovaOffice   DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE;
  Lv_IdParametroCabFormContact DB_GENERAL.ADMI_PARAMETRO_CAB.ID_PARAMETRO%TYPE;
  Le_Exception        EXCEPTION;
BEGIN
  --
  --
  IF C_GetIdParametroCab%ISOPEN THEN CLOSE C_GetIdParametroCab; END IF;

  OPEN C_GetIdParametroCab('RENOVAR_LIC_OFFICE365', 'COMERCIAL', 'Activo', 'eholguin');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCabRenovaOffice;
  IF C_GetIdParametroCab%NOTFOUND THEN
    RAISE Le_Exception;
  END IF;
  CLOSE C_GetIdParametroCab;
  --
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCabRenovaOffice,
    'PARAMETRO DONDE SE CONFIGURA NUMERO DE INTENTOS DE REVOVACION DE LICENCIA OFFICE 365',
    '3',
    'NumeroIntentosRenovacion',
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
    '18',
    NULL,
    NULL,
    NULL
  );
--
  

  --
  Lv_IdParametroCabRenovaOffice:= '';
  --
  
COMMIT;
EXCEPTION
WHEN Le_Exception THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
WHEN OTHERS THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
END;
/
