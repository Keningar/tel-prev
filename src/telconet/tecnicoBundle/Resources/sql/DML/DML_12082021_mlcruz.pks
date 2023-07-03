SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamsServiciosMd NUMBER;
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamsServiciosMd
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PARAMETROS_ASOCIADOS_A_SERVICIOS_MD';

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
    'Estados de servicio para consultar en un IN de acuerdo al nombre técnico de un producto',
    'ESTADOS_SERVICIOS_IN',
    'ECDF',
    'Pendiente',
    NULL,
    NULL,
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
    'Estados de servicio para consultar en un IN de acuerdo al nombre técnico de un producto',
    'ESTADOS_SERVICIOS_IN',
    'ECDF',
    'Activo',
    NULL,
    NULL,
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
    VALOR5,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos de adicionales que se eliminarán con el Internet desde el grid comercial',
    'NOMBRES_TECNICOS_ELIMINACION_SIMULTANEA_X_INTERNET',
    'ECDF',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18',
    NULL
  );

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
  SET DESCRIPCION = 'Nombres técnicos de adicionales que se eliminarán con el Internet desde el grid comercial'
  WHERE PARAMETRO_ID = 1253
  AND VALOR1= 'NOMBRES_TECNICOS_ELIMINACION_SIMULTANEA_X_INTERNET';

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
    EMPRESA_COD,
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamsServiciosMd,
    'Nombres técnicos de adicionales que se anularán con el Internet desde el grid comercial',
    'NOMBRES_TECNICOS_ANULACION_SIMULTANEA_X_INTERNET',
    'ECDF',
    NULL,
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    '18',
    NULL
  ); 
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los parámetros para el ECDF');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/