--Creación de la asociación de las características al producto TelcoHome e Ip TelcoHome
SET SERVEROUTPUT ON
DECLARE
  Ln_IdCaractCategoria1 NUMBER(5,0);
  Ln_IdCaractTraslado   NUMBER(5,0);
  Ln_IdCaractMrc        NUMBER(5,0);
  Ln_IdProdTelcoHome    NUMBER(5,0);
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractCategoria1
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='CATEGORIA 1';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractTraslado
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='TRASLADO';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMrc
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MRC';
  SELECT ID_PRODUCTO
  INTO Ln_IdProdTelcoHome
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='TELCOHOME';
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractCategoria1,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto TELCOHOME Caracteristica CATEGORIA 1');
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractTraslado,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto TELCOHOME Caracteristica TRASLADO');
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
      Ln_IdProdTelcoHome,
      Ln_IdCaractMrc,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creación correctamente de registro Producto TELCOHOME Caracteristica MRC');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
DECLARE
  Ln_IdParamProMasivosTelcoHome NUMBER(5,0);
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
      'PROCESOS_MASIVOS_TELCOHOME',
      'Procesos que se ejecutarán de manera masiva a los servicios TelcoHome de un cliente',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );

  SELECT ID_PARAMETRO
  INTO Ln_IdParamProMasivosTelcoHome
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PROCESOS_MASIVOS_TELCOHOME'
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
    Ln_IdParamProMasivosTelcoHome,
    'Procesos que se ejecutarán de manera masiva a los servicios TelcoHome de un cliente',
    'CortarTelcoHome',
    'Cortar',
    'Activo',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
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
    Ln_IdParamProMasivosTelcoHome,
    'Procesos que se ejecutarán de manera masiva a los servicios TelcoHome de un cliente',
    'ReconectarTelcoHome',
    'Reactivar',
    'In-Corte',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se crearon correctamente el detalle del parámetro PROCESOS_MASIVOS_TELCOHOME');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET SOPORTE_MASIVO = 'S'
WHERE NOMBRE_TECNICO = 'TELCOHOME';
COMMIT;
