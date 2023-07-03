SET SERVEROUTPUT ON
--Se agrega la parametrización con las cantidades de licencias Kaspersky habilitadas para la venta del servicio
DECLARE
  Ln_IdParamAntivirus NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_CAB
    (
      ID_PARAMETRO,
      NOMBRE_PARAMETRO,
      DESCRIPCION,
      MODULO,
      PROCESO,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
      'ANTIVIRUS_KASPERSKY_LICENCIAS_MD',
      'Cantidad de licencias disponibles para la venta del producto adicional I. Protegido de MD',
      'TECNICO',
      'LICENCIAS_PERMITIDAS',
      'Activo',
      'jbozada',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamAntivirus
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='ANTIVIRUS_KASPERSKY_LICENCIAS_MD'
  AND ESTADO = 'Activo';
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
    VALOR6,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Licencias de MD',
    '1',
    '',
    '',
    '',
    '',
    '',
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
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
    VALOR6,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamAntivirus,
    'Licencias de MD',
    '3',
    '',
    '',
    '',
    '',
    '',
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se crearon correctamente los detalles del parámetro ANTIVIRUS_KASPERSKY_LICENCIAS_MD');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/

--Asociación de nueva característica para licencias McAfee que dieron error al cancelar
--El producto ya debe tener asociada la características ERROR_CANCELACION
DECLARE
  Ln_IdProductoUno              NUMBER(5,0) := 209;
  Ln_IdProductoDos              NUMBER(5,0) := 210;
  Ln_IdProductoTres             NUMBER(5,0) := 211;
  Ln_IdProductoCuatro           NUMBER(5,0) := 212;
  Ln_IdCaractErrorCancelacion   NUMBER(5,0);
  Lv_EstadoActivo               VARCHAR2(6) := 'Activo';
BEGIN
  Ln_IdCaractErrorCancelacion := DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL;
  INSERT
  INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
      ID_CARACTERISTICA,
      DESCRIPCION_CARACTERISTICA,
      TIPO_INGRESO,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      FE_ULT_MOD,
      USR_ULT_MOD,
      TIPO
    )
    VALUES
    (
      Ln_IdCaractErrorCancelacion,
      'ERROR_CANCELACION',
      'T',
      'Activo',
      SYSDATE,
      'jbozada',
      NULL,
      NULL,
      'TECNICA'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProductoUno,
      Ln_IdCaractErrorCancelacion,
      CURRENT_TIMESTAMP,
      'jbozada',
      Lv_EstadoActivo,
      'NO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProductoDos,
      Ln_IdCaractErrorCancelacion,
      CURRENT_TIMESTAMP,
      'jbozada',
      Lv_EstadoActivo,
      'NO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProductoTres,
      Ln_IdCaractErrorCancelacion,
      CURRENT_TIMESTAMP,
      'jbozada',
      Lv_EstadoActivo,
      'NO'
    );
  INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
      ID_PRODUCTO_CARACTERISITICA,
      PRODUCTO_ID,
      CARACTERISTICA_ID,
      FE_CREACION,
      USR_CREACION,
      ESTADO,
      VISIBLE_COMERCIAL
    )
    VALUES
    (
      DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
      Ln_IdProductoCuatro,
      Ln_IdCaractErrorCancelacion,
      CURRENT_TIMESTAMP,
      'jbozada',
      Lv_EstadoActivo,
      'NO'
    );

  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de la asociación de producto y la característica ERROR_CANCELACION');

  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
