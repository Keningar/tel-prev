SET SERVEROUTPUT ON
--Creación de parámetros para servicios de MD
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
  Ln_IdParamModelos         NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';
  SELECT ID_PARAMETRO
  INTO Ln_IdParamModelos
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='MODELOS_EQUIPOS_NUEVOS_PLANES_MD';
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos parametrizados por tecnología y por equipo',
    'MODELOS_EQUIPOS',
    'HUAWEI',
    'MA5608T',
    'EXTENDER DUAL BAND',
    'WA8011V',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
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
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamModelos,
    'Nombres de modelos permitidos para los nuevos planes de MD',
    'EXTENDER_DUAL_BAND',
    'HUAWEI',
    'MA5608T',
    'WA8011V',
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de Extender Dual Band');


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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Modelos de olt parametrizados que tienen asociados equipos Dual Band',
    'MODELOS_OLT_EQUIPOS_DUAL_BAND',
    'HUAWEI',
    'MA5608T',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de olt permitidos para funcionar con equipos dual band');

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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valores parametrizados de producto que no se pueden evaluar por función precio',
    'PRECIOS_PRODUCTOS',
    '1231',
    '5',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );

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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Valores parametrizados de producto que no se pueden evaluar por función precio',
    'PRECIOS_PRODUCTOS',
    '1232',
    '5',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los precios de los servicios dual band');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Se ejecuta nuevamente el proceso de regularización de servicios con equipo dual band por solicitud del usuario Glenda Tixi
DECLARE
BEGIN
  DB_INFRAESTRUCTURA.INFRK_TRANSACCIONES.P_REGULA_EQUIPOS_W_Y_EXTENDER;
  SYS.DBMS_OUTPUT.PUT_LINE('PROCESO DE REGULARIZACIÓN EJECUTADO CORRECTAMENTE');
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
END;
/
