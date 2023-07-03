--Característica para distinguir los nuevos planes
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PLANES NUEVOS VIGENTES',
    'C',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PERMITIDO_SOLO_MIGRACION',
    'C',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'NUMERO REINTENTOS',
    'N',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ACTIVACION POR MASIVO',
    'T',
    'Activo',
    SYSDATE,
    'mlcruz',
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
      210,
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'NUMERO REINTENTOS'),
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
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
      210,
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ACTIVACION POR MASIVO'),
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'APLICA_CPM',
    'C',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_GRATIS',
    'C',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'WIFI DUAL BAND',
    'T',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA
  (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'EXTENDER DUAL BAND',
    'T',
    'Activo',
    SYSDATE,
    'mlcruz',
    'TECNICA'
  );
COMMIT;
/
--Creación de parámetros con modelos de ont permitidos para activación con Wifi Dual Band y de Extender
DECLARE
  Ln_IdParamModelos NUMBER;
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
      'MODELOS_EQUIPOS_NUEVOS_PLANES_MD',
      'Nombres de modelos permitidos para los nuevos planes de MD',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
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
    'WIFI_DUAL_BAND',
    'HUAWEI',
    'MA5608T',
    'HS8M8245WG04',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de ont');
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
    'WA8M8011VW09',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente los modelos de extender');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetro con número de reintentos permitidos para activación de McAfee
DECLARE
  Ln_IdParamReintentos NUMBER;
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
      'NUMERO_MAX_REINTENTOS_MCAFEE',
      'Número de reintentos máximos permitidos para activar McAfee en plan',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamReintentos
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='NUMERO_MAX_REINTENTOS_MCAFEE';
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
    Ln_IdParamReintentos,
    'Número de reintentos máximos permitidos para activar McAfee en plan',
    'PLAN',
    '3',
    '',
    '',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado correctamente el parámetro');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de perfiles equivalentes
DECLARE
  Ln_IdParamMigraPerfilEquiV2 NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamMigraPerfilEquiV2
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2';--190
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_6M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_6M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_6M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_6M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_6M_1');
  
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_15M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_15M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_15M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_15M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_15M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_30M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_30M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_30M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_30M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_30M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_50M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_50M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_50M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_50M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_50M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_60M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_60M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_60M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_60M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_60M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_100M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_100M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_100M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_100M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_100M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_200M_1',
    'PERFIL_H_HOME_DEFAULT',
    'PLAN_200M',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'dinamico_200M_1',
    'PERFIL_T_HOME_DEFAULT',
    'dinamico_200M_1',
    'SI',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con dinamico_200M_1');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de valores requeridos para el LDAP
DECLARE
  Ln_IdParamPerfilClientPck NUMBER(5,0);
  Ln_IdParamEqPlanesSi NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamPerfilClientPck
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='CNR_PERFIL_CLIENT_PCK';--103
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamPerfilClientPck,
    'PLAN_6M',
    'PLAN_6M',
    'PLAN_6M',
    '12',
    'PLAN_6M',
    'SI',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro CNR_PERFIL_CLIENT_PCK para PLAN_6M');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamPerfilClientPck,
    'PLAN_60M',
    'PLAN_60M',
    'PLAN_60M',
    '39',
    'PLAN_60M',
    'SI',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro CNR_PERFIL_CLIENT_PCK para PLAN_60M');

  SELECT ID_PARAMETRO
  INTO Ln_IdParamEqPlanesSi
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='EQ_NUEVOS_PLANES_SI_ACEPTACION';--135
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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'PLAN_6M',
    'PLAN_6M',
    '12',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para PLAN_6M');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'PLAN_15M',
    'PLAN_15M',
    '14',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para PLAN_15M');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'PLAN_30M',
    'PLAN_30M',
    '41',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para PLAN_30M');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'PLAN_60M',
    'PLAN_60M',
    '39',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para PLAN_60M');


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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'dinamico_6M_1',
    'PLAN_6M',
    '12',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para dinamico_6M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'dinamico_15M_1',
    'PLAN_15M',
    '14',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para dinamico_15M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'dinamico_30M_1',
    'PLAN_30M',
    '41',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para dinamico_30M_1');

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
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD,
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    Ln_IdParamEqPlanesSi,
    NULL,
    'dinamico_60M_1',
    'PLAN_60M',
    '39',
    'HOME',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_SI_ACEPTACION para dinamico_60M_1');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Script para crear detalles con service profile name en los OLTs con MIDDLEWARE
DECLARE
  CURSOR Lc_GetOlts
  IS
    SELECT DISTINCT OLT.ID_ELEMENTO
    FROM DB_INFRAESTRUCTURA.VISTA_ELEMENTOS OLT
    WHERE OLT.NOMBRE_TIPO_ELEMENTO = 'OLT'
    AND OLT.EMPRESA_COD            = '18'
    AND OLT.NOMBRE_MARCA_ELEMENTO  = 'HUAWEI'
    AND EXISTS
      (SELECT IDE_MIDDLEWARE.ID_DETALLE_ELEMENTO
      FROM DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO IDE_MIDDLEWARE
      WHERE OLT.ID_ELEMENTO             = IDE_MIDDLEWARE.ELEMENTO_ID
      AND IDE_MIDDLEWARE.DETALLE_NOMBRE = 'MIDDLEWARE'
      AND IDE_MIDDLEWARE.DETALLE_VALOR  = 'SI'
      AND IDE_MIDDLEWARE.ESTADO         = 'Activo'
      )
  AND (OLT.ESTADO = 'Activo'
  OR OLT.ESTADO   = 'Modificado');
  
BEGIN
  IF Lc_GetOlts%ISOPEN THEN
    CLOSE Lc_GetOlts;
  END IF;
  FOR I_GetOlts IN Lc_GetOlts
  LOOP
      INSERT
      INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO VALUES
        (
          DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.nextval,
          I_GetOlts.ID_ELEMENTO,
          'SERVICE-PROFILE-NAME',
          'HS8M8245WG04',
          'HS8M8245WG04',
          'mlcruz',
          SYSDATE,
          '127.0.0.1',
          NULL,
          'Activo'
        );
      SYS.DBMS_OUTPUT.PUT_LINE('OLT: '|| I_GetOlts.ID_ELEMENTO);
      COMMIT;
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
SET DEFINE OFF;
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar la creación automática de solicitudes de agregar equipo con equipos dual band
INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    PLANTILLA
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificación al crearse una solicitud de agregar equipo con equipos dual band',
    'AGREGAEQUIPOPYL',
    'TECNICO',
    'Activo',
    CURRENT_TIMESTAMP,
    'mlcruz',
    TO_CLOB('<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    </head>
    <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                    <img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid #6699CC;">
                    <table width="100%" cellspacing="0" cellpadding="5">
                        <tr>
                            <td colspan="2">Estimado personal,</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                El presente correo es para informarle que se ha creado una {{ descripcionTipoSolicitud }} pendiente de coordinar
                                del servicio detallado a continuaci&oacute;n: 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <strong>Datos Cliente</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ cliente }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login:</strong>
                            </td>
                            <td>{{ login }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td>
                                <strong>Jurisdicci&oacute;n:</strong>
                            </td>
                            <td>
                                {{ nombreJurisdiccion }}	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>{{ tipoPlanOProducto }}:</strong>
                            </td>
                            <td>
                                {{ nombrePlanOProducto }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ estadoServicio }}</label></strong></td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td colspan="2"><br/></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    
                </td>
            </tr>
            <tr> 
		<td><strong><font size="2" face="Tahoma">MegaDatos S.A.</font></strong></p>
		</td>      
            </tr>  
        </table>
    </body>
</html>
    ')
  );
  SELECT ID_PLANTILLA
  INTO Ln_IdPlantilla
  FROM DB_COMUNICACION.ADMI_PLANTILLA
  WHERE CODIGO='AGREGAEQUIPOPYL';
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='pyl_corporativo@telconet.ec'
  AND ESTADO      = 'Activo'
  AND EMPRESA_COD = '10';
  INSERT
  INTO DB_COMUNICACION.INFO_ALIAS_PLANTILLA
    (
      ID_ALIAS_PLANTILLA,
      ALIAS_ID,
      PLANTILLA_ID,
      ESTADO,
      FE_CREACION,
      USR_CREACION,
      ES_COPIA
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_ALIAS.NEXTVAL,
      Ln_IdAlias,
      Ln_IdPlantilla,
      'Activo',
      SYSDATE,
      'mlcruz',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente AGREGAEQUIPOPYL');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
SET DEFINE OFF;
INSERT
INTO DB_COMUNICACION.ADMI_PLANTILLA
  (
    ID_PLANTILLA,
    NOMBRE_PLANTILLA,
    CODIGO,
    MODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    PLANTILLA
  )
  VALUES
  (
    DB_COMUNICACION.SEQ_ADMI_PLANTILLA.NEXTVAL,
    'Notificación al no activarse correctamente el McAfee dentro del plan',
    'ERROR_MCAFEE',
    'TECNICO',
    'Activo',
    CURRENT_TIMESTAMP,
    'mlcruz',
    TO_CLOB('<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=UTF-8">
    </head>
    <body>
        <table align="center" width="100%" cellspacing="0" cellpadding="5">
            <tr>
                <td align="center" style="border:1px solid #6699CC;background-color:#E5F2FF;">
                    <img alt=""  src="http://images.telconet.net/others/telcos/logo.png"/>
                </td>
            </tr>
            <tr>
                <td style="border:1px solid #6699CC;">
                    <table width="100%" cellspacing="0" cellpadding="5">
                        <tr>
                            <td colspan="2">Estimado personal,</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                El presente correo es para informarle que no se ha podido activar el {{ nombreProductoMcAfee }} incluido en el plan
                                del servicio detallado a continuaci&oacute;n: 
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <strong>Datos Cliente</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <hr />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Cliente:</strong>
                            </td>
                            <td>{{ cliente }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Login:</strong>
                            </td>
                            <td>{{ login }}</td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td>
                                <strong>Jurisdicci&oacute;n:</strong>
                            </td>
                            <td>
                                {{ nombreJurisdiccion }}	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Plan:</strong>
                            </td>
                            <td>
                                {{ nombrePlan }} 	
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Observaci&oacute;n:</strong>
                            </td>
                            <td>{{ observacion | raw }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Estado:</strong>
                            </td>
                            <td><strong><label style="color:red">{{ estadoServicio }}</label></strong></td>
                        </tr>') ||
                        TO_CLOB('<tr>
                            <td colspan="2"><br/></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    
                </td>
            </tr>
            <tr> 
		<td><strong><font size="2" face="Tahoma">MegaDatos S.A.</font></strong></p>
		</td>      
            </tr>  
        </table>
    </body>
</html>
    ')
  );
COMMIT;
/
--Se eliminan las interfaces del modelo creadas erróneamente ya que la herramienta en Telcos no permite la eliminación
DELETE DB_INFRAESTRUCTURA.ADMI_INTERFACE_MODELO
WHERE ID_INTERFACE_MODELO IN (1882,1921);

--Actualización de productos Wifi Dual Band y Extender Dual Band creados en producción
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET NOMBRE_TECNICO       = 'WIFI_DUAL_BAND',
  REQUIERE_PLANIFICACION = 'NO',
  FUNCION_PRECIO         = 'if( [ES_GRATIS] === "SI") {PRECIO=0.00;} else {PRECIO=5.00;}'
WHERE ID_PRODUCTO        = 1231;
UPDATE DB_COMERCIAL.ADMI_PRODUCTO
SET NOMBRE_TECNICO       = 'EXTENDER_DUAL_BAND',
  REQUIERE_PLANIFICACION = 'NO',
  FUNCION_PRECIO         = 'if( [ES_GRATIS] === "SI") {PRECIO=0.00;} else {PRECIO=5.00;}'
WHERE ID_PRODUCTO        = 1232;
UPDATE DB_COMERCIAL.INFO_PLAN_DET
SET COSTO_ITEM   = 0,
  PRECIO_ITEM    = 0,
  DESCUENTO_ITEM = 0
WHERE ID_ITEM   IN
  (SELECT ID_ITEM
  FROM DB_COMERCIAL.INFO_PLAN_DET
  WHERE PRODUCTO_ID IN (1231,1232)
  AND PLAN_ID IN (2451, 2452, 2453, 2449, 2454, 2455, 2456, 2457, 2458, 2459, 2460, 2461, 2462, 2464, 2465, 2466 )
  );
--Se actualiza la característica CAPACIDAD1 por ES_GRATIS asociada a los productos Wifi Dual Band y Extender Dual Band 
UPDATE DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
SET CARACTERISTICA_ID =
  (SELECT ID_CARACTERISTICA
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA = 'ES_GRATIS'
  )
WHERE ID_PRODUCTO_CARACTERISITICA IN (11338, 11339);

--Se asocia la característica MAC al producto Extender Dual Band
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
      1232,
      6,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );

--Actualizaciones de perfiles
UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_6M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID   IN (2437, 2438, 2439, 2440, 2441)
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );

UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_15M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID  IN (2442,2400,2402,2403,2404)
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );

UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_30M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID  IN (2443, 2405, 2406, 2444, 2407 )
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );

UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_50M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID  IN (2451,2452,2453,2449,2454)
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );

UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_60M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID  IN (2455 )
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );

UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_100M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID  IN (2456, 2457,2458,2459,2460 )
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );

UPDATE DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
SET VALOR                      = 'dinamico_200M_1'
WHERE ID_PLAN_PRODUCTO_CARACT IN
  (SELECT ID_PLAN_PRODUCTO_CARACT
  FROM DB_COMERCIAL.INFO_PLAN_PRODUCTO_CARACT
  WHERE PLAN_DET_ID IN
    (SELECT ID_ITEM
    FROM DB_COMERCIAL.INFO_PLAN_DET
    WHERE PLAN_ID  IN (2461, 2462, 2464, 2465, 2466 )
    AND PRODUCTO_ID = 63
    )
  AND PRODUCTO_CARACTERISITICA_ID = 406
  AND ESTADO                      = 'Activo'
  );
COMMIT;
/
--Se agregan características de PLANES NUEVOS VIGENTES y APLICA_CPM a los planes de 6, 15 y 30MB
DECLARE
  CURSOR Lc_GetPlanes
  IS
    SELECT PLANES.*
    FROM DB_COMERCIAL.INFO_PLAN_CAB PLANES
    WHERE PLANES.ID_PLAN IN (2437, 2438, 2439, 2440, 2441, 2442, 2400, 2402, 2403, 2404, 2443, 2405, 2406, 2444, 2407);
BEGIN
  IF Lc_GetPlanes%ISOPEN THEN
    CLOSE Lc_GetPlanes;
  END IF;
  FOR I_GetPlanes IN Lc_GetPlanes
  LOOP
    INSERT
    INTO DB_COMERCIAL.INFO_PLAN_CARACTERISTICA
      (
        ID_PLAN_CARACTERISITCA,
        PLAN_ID,
        CARACTERISTICA_ID,
        VALOR,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_PLAN_CARACTERISTICA.NEXTVAL,
        I_GetPlanes.ID_PLAN,
        (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'PLANES NUEVOS VIGENTES'),
        'SI',
        'Activo',
        CURRENT_TIMESTAMP,
        'mlcruz',
        '127.0.0.1'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('ID_PLAN: '|| I_GetPlanes.ID_PLAN || ' PLANES NUEVOS VIGENTES');
    INSERT
    INTO DB_COMERCIAL.INFO_PLAN_CARACTERISTICA
      (
        ID_PLAN_CARACTERISITCA,
        PLAN_ID,
        CARACTERISTICA_ID,
        VALOR,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_PLAN_CARACTERISTICA.NEXTVAL,
        I_GetPlanes.ID_PLAN,
        (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'APLICA_CPM'),
        'SI',
        'Activo',
        CURRENT_TIMESTAMP,
        'mlcruz',
        '127.0.0.1'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('ID_PLAN: '|| I_GetPlanes.ID_PLAN || ' APLICA_CPM');
    COMMIT;
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se agregan características de PLANES NUEVOS VIGENTES a los planes de 50, 60, 100 y 200MB
DECLARE
  CURSOR Lc_GetPlanes
  IS
    SELECT PLANES.*
    FROM DB_COMERCIAL.INFO_PLAN_CAB PLANES
    WHERE PLANES.ID_PLAN IN (2451, 2452, 2453, 2449, 2454, 2455, 2456, 2457, 2458, 2459, 2460, 2461, 2462, 2464, 2465, 2466 );
BEGIN
  IF Lc_GetPlanes%ISOPEN THEN
    CLOSE Lc_GetPlanes;
  END IF;
  FOR I_GetPlanes IN Lc_GetPlanes
  LOOP
    INSERT
    INTO DB_COMERCIAL.INFO_PLAN_CARACTERISTICA
      (
        ID_PLAN_CARACTERISITCA,
        PLAN_ID,
        CARACTERISTICA_ID,
        VALOR,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_PLAN_CARACTERISTICA.NEXTVAL,
        I_GetPlanes.ID_PLAN,
        (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'PLANES NUEVOS VIGENTES'),
        'SI',
        'Activo',
        CURRENT_TIMESTAMP,
        'mlcruz',
        '127.0.0.1'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('ID_PLAN: '|| I_GetPlanes.ID_PLAN || ' PLANES NUEVOS VIGENTES');
    COMMIT;
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Agregar sólo a los planes de 60MB la característica PERMITIDO_SOLO_MIGRACION para que este plan no se encuentre disponible para la venta
DECLARE
  CURSOR Lc_GetPlanes
  IS
    SELECT PLANES.*
    FROM DB_COMERCIAL.INFO_PLAN_CAB PLANES
    WHERE PLANES.ID_PLAN IN (2455);
BEGIN
  IF Lc_GetPlanes%ISOPEN THEN
    CLOSE Lc_GetPlanes;
  END IF;
  FOR I_GetPlanes IN Lc_GetPlanes
  LOOP
    INSERT
    INTO DB_COMERCIAL.INFO_PLAN_CARACTERISTICA
      (
        ID_PLAN_CARACTERISITCA,
        PLAN_ID,
        CARACTERISTICA_ID,
        VALOR,
        ESTADO,
        FE_CREACION,
        USR_CREACION,
        IP_CREACION
      )
      VALUES
      (
        DB_COMERCIAL.SEQ_INFO_PLAN_CARACTERISTICA.NEXTVAL,
        I_GetPlanes.ID_PLAN,
        (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'PERMITIDO_SOLO_MIGRACION'),
        'SI',
        'Activo',
        CURRENT_TIMESTAMP,
        'mlcruz',
        '127.0.0.1'
      );
    SYS.DBMS_OUTPUT.PUT_LINE('ID_PLAN: '|| I_GetPlanes.ID_PLAN || ' PERMITIDO_SOLO_MIGRACION');
    COMMIT;
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                            || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se actualizan características de servicios McAfee 
DECLARE
BEGIN
  FOR SERVICIOS_REG IN
  (SELECT            *
  FROM
    (SELECT info_servicio.ID_SERVICIO,
      info_servicio.producto_id ,
      admi_producto.DESCRIPCION_PRODUCTO,
      (SELECT COUNT(*)
      FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
        DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
      AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = info_servicio.ID_SERVICIO
      AND ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID              = info_servicio.PRODUCTO_ID
      AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
      AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='PARTNERREF'
      AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
      GROUP BY ADMI_CARACTERISTICA.ID_CARACTERISTICA
      ) PARTNERREF,
      (SELECT COUNT(*)
      FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
        DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
      AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = info_servicio.ID_SERVICIO
      AND ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID              = info_servicio.PRODUCTO_ID
      AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
      AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='PASSWORD'
      AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
      GROUP BY ADMI_CARACTERISTICA.ID_CARACTERISTICA
      ) PASSWORD_MCAFEE,
      (SELECT COUNT(*)
      FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
        DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
      AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = info_servicio.ID_SERVICIO
      AND ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID              = info_servicio.PRODUCTO_ID
      AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
      AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='SKU'
      AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
      GROUP BY ADMI_CARACTERISTICA.ID_CARACTERISTICA
      ) SKU,
      (SELECT COUNT(*)
      FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
        DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
        DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
      AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = info_servicio.ID_SERVICIO
      AND ADMI_PRODUCTO_CARACTERISTICA.PRODUCTO_ID              = info_servicio.PRODUCTO_ID
      AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
      AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='CUSTOMERCONTEXTID'
      AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
      GROUP BY ADMI_CARACTERISTICA.ID_CARACTERISTICA
      ) CUSTOMERCONTEXTID,
      info_servicio.estado
    FROM DB_COMERCIAL.info_servicio,
      DB_COMERCIAL.admi_producto
    WHERE ID_PRODUCTO            IN (212,211,210,209)
    AND info_servicio.producto_id = admi_producto.id_producto
    AND info_servicio.estado     IN ('Activo','In-Corte')
    )
  WHERE (PASSWORD_MCAFEE >1
  OR PARTNERREF          >1
  OR CUSTOMERCONTEXTID   >1
  OR SKU                 >1)
  )
  LOOP
    DBMS_OUTPUT.put_line ( SERVICIOS_REG.DESCRIPCION_PRODUCTO || ' - '|| SERVICIOS_REG.ESTADO);
    --REGULARIZACION PARTNERREF
    IF (SERVICIOS_REG.PARTNERREF >1) THEN
      UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
      SET ESTADO                    ='Eliminado', USR_ULT_MOD='telcos_regu', FE_ULT_MOD = sysdate
      WHERE ID_SERVICIO_PROD_CARACT =
        (SELECT MIN(INFO_SERVICIO_PROD_CARACT.ID_SERVICIO_PROD_CARACT)
        FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
          DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
        AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = SERVICIOS_REG.ID_SERVICIO
        AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
        AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='PARTNERREF'
        AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
        );
      COMMIT;
    END IF;
    --REGULARIZACION PASSWORD
    IF (SERVICIOS_REG.PASSWORD_MCAFEE >1) THEN
      UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
      SET ESTADO                    ='Eliminado', USR_ULT_MOD='telcos_regu', FE_ULT_MOD = sysdate
      WHERE ID_SERVICIO_PROD_CARACT =
        (SELECT MIN(INFO_SERVICIO_PROD_CARACT.ID_SERVICIO_PROD_CARACT)
        FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
          DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
        AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = SERVICIOS_REG.ID_SERVICIO
        AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
        AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='PASSWORD'
        AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
        );
      COMMIT;
    END IF;
    --REGULARIZACION SKU
    IF (SERVICIOS_REG.SKU >1) THEN
      UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
      SET ESTADO                    ='Eliminado', USR_ULT_MOD='telcos_regu', FE_ULT_MOD = sysdate
      WHERE ID_SERVICIO_PROD_CARACT =
        (SELECT MIN(INFO_SERVICIO_PROD_CARACT.ID_SERVICIO_PROD_CARACT)
        FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
          DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
        AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = SERVICIOS_REG.ID_SERVICIO
        AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
        AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='SKU'
        AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
        );
      COMMIT;
    END IF;
    --REGULARIZACION CUSTOMERCONTEXTID
    IF (SERVICIOS_REG.CUSTOMERCONTEXTID >1) THEN
      UPDATE DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT
      SET ESTADO                    ='Eliminado', USR_ULT_MOD='telcos_regu', FE_ULT_MOD = sysdate
      WHERE ID_SERVICIO_PROD_CARACT =
        (SELECT MIN(INFO_SERVICIO_PROD_CARACT.ID_SERVICIO_PROD_CARACT)
        FROM DB_COMERCIAL.INFO_SERVICIO_PROD_CARACT,
          DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA,
          DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE ADMI_PRODUCTO_CARACTERISTICA.CARACTERISTICA_ID      = ADMI_CARACTERISTICA.ID_CARACTERISTICA
        AND INFO_SERVICIO_PROD_CARACT.SERVICIO_ID                 = SERVICIOS_REG.ID_SERVICIO
        AND INFO_SERVICIO_PROD_CARACT.PRODUCTO_CARACTERISITICA_ID = ADMI_PRODUCTO_CARACTERISTICA.ID_PRODUCTO_CARACTERISITICA
        AND ADMI_CARACTERISTICA.DESCRIPCION_CARACTERISTICA        ='CUSTOMERCONTEXTID'
        AND INFO_SERVICIO_PROD_CARACT.estado                      ='Activo'
        );
      COMMIT;
    END IF;
  
  END LOOP;
END;
/