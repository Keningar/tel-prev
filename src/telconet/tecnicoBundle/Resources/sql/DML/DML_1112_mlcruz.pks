SET SERVEROUTPUT ON
--Creación de parámetros con coordenadas por país
DECLARE
  Ln_IdParamCoordElem NUMBER(5,0);
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
      'LIMITES_COORDENADAS_ELEMENTO',
      'Límites de coordenadas para un elemento de acuerdo al país',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamCoordElem
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='LIMITES_COORDENADAS_ELEMENTO';
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
    Ln_IdParamCoordElem,
    'ECUADOR',
    '1.40',
    '-5.036',
    '-75.25',
    '-95',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'ecuatoriano',
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los límites de ECUADOR');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
