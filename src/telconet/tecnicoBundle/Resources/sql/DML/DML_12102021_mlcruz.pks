SET SERVEROUTPUT ON
--Creación de parámetros para web services técnicos
DECLARE
  Ln_IdParamsWsTecnico NUMBER;
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'PARAMS_WEB_SERVICES_TECNICO',
      'Parámetro usado para las diferentes parametizaciones generales de los web services técnicos',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsWsTecnico
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMS_WEB_SERVICES_TECNICO';

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
    Ln_IdParamsWsTecnico,
    'Parámetro con los ids del producto principal con el producto relacionado',
    'NOMBRES_TECNICOS_PRODS_INTERNET_OSS',
    'PRODUCTO_PLAN',
    'INTERNET',
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
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
    Ln_IdParamsWsTecnico,
    'Parámetro con los ids del producto principal con el producto relacionado',
    'NOMBRES_TECNICOS_PRODS_INTERNET_OSS',
    'PRODUCTO',
    'INTERNET SMALL BUSINESS',
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
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
    Ln_IdParamsWsTecnico,
    'Parámetro con los ids del producto principal con el producto relacionado',
    'NOMBRES_TECNICOS_PRODS_INTERNET_OSS',
    'PRODUCTO',
    'TELCOHOME',
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
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
    Ln_IdParamsWsTecnico,
    'Parámetro con los ids del producto principal con el producto relacionado',
    'NOMBRES_TECNICOS_PRODS_INTERNET_OSS',
    'PRODUCTO',
    'DATOS SAFECITY',
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los nombres técnicos de productos');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
