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
  OPEN C_GetIdParametroCab('FILTROS DE FACTURACION AUTOMATICA', 'FINANCIERO', 'Activo', 'eholguin');
  FETCH C_GetIdParametroCab INTO Lv_IdParametroCab;
  CLOSE C_GetIdParametroCab;
  --
INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET
  (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    VALOR4,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    VALOR5,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    Lv_IdParametroCab,
    'Facturacion Automatica de Consumo',
    'FAC',
    'telcos_consumo',
    NULL ,
    NULL ,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
--
  --
  Lv_IdParametroCab := '';
  --
COMMIT;
EXCEPTION
WHEN OTHERS THEN
  ROLLBACK;
  dbms_output.put_line(sqlerrm);
END;

--


INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'FACTURACION POR CONSUMO',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'COMERCIAL'
  );

  --INSERT DE PRODUCTO CARACTERISTICA FACTURACION POR CONSUMO
  INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
  (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    FE_ULT_MOD,
    USR_CREACION,
    USR_ULT_MOD,
    ESTADO,
    VISIBLE_COMERCIAL
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    1124,
    (select ID_CARACTERISTICA from db_comercial.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA = 'FACTURACION POR CONSUMO'),
    sysdate,
    NULL,
    'javera',
    NULL,
    'Activo',
    'NO'
  );
