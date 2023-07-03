--Bloque anónimo para crear nuevos planes para Small Business
SET SERVEROUTPUT ON
--Creación de parámetros con detalles para mapeo de perfiles de acuerdo a la velocidad
DECLARE
  Ln_IdParamVelocidadSb NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamVelocidadSb
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PROD_VELOCIDAD';
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
    Ln_IdParamVelocidadSb,
    'PROD_VELOCIDAD',
    '10',
    'MB',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
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
    Ln_IdParamVelocidadSb,
    'PROD_VELOCIDAD',
    '35',
    'MB',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro PROD_VELOCIDAD para flujo con producto Internet Small Business');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros con detalles para mapeo de perfiles de acuerdo a la velocidad
DECLARE
  Ln_IdParamMapeoVelocidadPerfil NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamMapeoVelocidadPerfil
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='MAPEO_VELOCIDAD_PERFIL';
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
    Ln_IdParamMapeoVelocidadPerfil,
    'Mapeo de perfiles de acuerdo a la velocidad del producto Internet Small Business',
    '10',
    'TN_fijo_10M_5',
    NULL,
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
    Ln_IdParamMapeoVelocidadPerfil,
    'Mapeo de perfiles de acuerdo a la velocidad del producto Internet Small Business',
    '35',
    'TN_fijo_35M_5',
    NULL,
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MAPEO_VELOCIDAD_PERFIL para flujo con producto Internet Small Business');
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
  WHERE NOMBRE_PARAMETRO='MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2';
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'TN_fijo_10M_5',
    'PERFIL_H_PYME_TN_DEFAULT',
    'TN_PLAN_10M',
    'NO',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'TN_fijo_35M_5',
    'PERFIL_H_PYME_TN_DEFAULT',
    'TN_PLAN_35M',
    'NO',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con producto Internet Small Business - Huawei');
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'TN_fijo_10M_5',
    'PERFIL_T_PYME_TN_DEFAULT',
    'TN_fijo_10M_1',
    'NO',
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
    Ln_IdParamMigraPerfilEquiV2,
    'EQUIVALENCIA_PERFIL',
    'TN_fijo_35M_5',
    'PERFIL_T_PYME_TN_DEFAULT',
    'TN_fijo_35M_1',
    'NO',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con producto Internet Small Business - Tellion');
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
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamPerfilClientPck
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='CNR_PERFIL_CLIENT_PCK';
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
    Ln_IdParamPerfilClientPck,
    'TN_PLAN_10M',
    'TN_PLAN_10M',--detalle valor del olt, perfil jar
    'TN_PLAN_10M',--valor del perfil equivalente
    '18',--package id
    'PLAN_10M',--client class
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NO',
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
    Ln_IdParamPerfilClientPck,
    'TN_PLAN_35M',
    'TN_PLAN_35M',--detalle valor del olt, perfil jar
    'TN_PLAN_35M',--valor del perfil equivalente
    '41',--package id
    'PLAN_35M',--client class
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NO',
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro CNR_PERFIL_CLIENT_PCK para flujo con producto Internet Small Business');
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
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamPerfilClientPck
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='EQ_NUEVOS_PLANES_NO_ACEPTACION';
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
    Ln_IdParamPerfilClientPck,
    'TN_fijo_10M_1',
    'TN_fijo_10M_1',--valor del perfil equivalente
    'PLAN_10M',--client class
    '18',--package id
    'PYMETN',--client class
    'Activo',
    'mlcruz',
    sysdate,
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
    Ln_IdParamPerfilClientPck,
    'TN_fijo_35M_1',
    'TN_fijo_35M_1',--valor del perfil equivalente
    'PLAN_35M',--client class
    '41',--package id
    'PYMETN',--client class
    'Activo',
    'mlcruz',
    sysdate,
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
    Ln_IdParamPerfilClientPck,
    'TN_fijo_10M_5',
    'TN_fijo_10M_5',--valor del perfil equivalente
    'PLAN_10M',--client class
    '18',--package id
    'PYMETN',--tipo de Negocio
    'Activo',
    'mlcruz',
    sysdate,
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
    Ln_IdParamPerfilClientPck,
    'TN_fijo_35M_5',
    'TN_fijo_35M_5',--valor del perfil equivalente
    'PLAN_35M',--client class
    '41',--package id
    'PYMETN',--client class
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_NO_ACEPTACION para flujo con producto Internet Small Business');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros para IP_MAX_PERMITIDAS_PRODUCTO con las nuevas velocidades
DECLARE
  Ln_IdParamMapeoIpMax NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamMapeoIpMax
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='IP_MAX_PERMITIDAS_PRODUCTO';
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
    Ln_IdParamMapeoIpMax,
    'Mapeo de Ips máximas permitidas por punto y por producto',
    'INTERNET SMALL BUSINESS',
    'IPSB',
    '10',
    '3',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'SI',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro IP_MAX_PERMITIDAS_PROD para flujo con producto IPSB 10MB');
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
    Ln_IdParamMapeoIpMax,
    'Mapeo de Ips máximas permitidas por punto y por producto',
    'INTERNET SMALL BUSINESS',
    'IPSB',
    '35',
    '3',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'SI',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro IP_MAX_PERMITIDAS_PROD para flujo con producto IPSB 30MB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Script para actualizar detalles en los OLTs HUAWEI con MIDDLEWARE
DECLARE
TYPE T_ArrayAsocPlan
IS
  TABLE OF VARCHAR2(2) INDEX BY VARCHAR2(15);
  T_PlanesInfoTecnica T_ArrayAsocPlan;
  Lv_NombrePlan VARCHAR2(15);
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
    AND (OLT.ESTADO = 'Activo' OR OLT.ESTADO = 'Modificado');
  Ln_IdElementoOlt               NUMBER;
  Ln_IdDetElemLineProfileId      NUMBER;
  Ln_IdDetElemLineProfileName    NUMBER;
  Ln_IdDetElemServiceProfileId   NUMBER;
  Ln_IdDetElemServiceProfileName NUMBER;
  Ln_IdDetElemGemPort            NUMBER;
  Ln_IdDetElemTrafficTable       NUMBER;
  Lv_ValorLineGemTraffic         VARCHAR2(2)  := '';
  Lv_ValorLineProfileName        VARCHAR2(15) := '';
BEGIN
  T_PlanesInfoTecnica('TN_PLAN_10M') := '11';
  T_PlanesInfoTecnica('TN_PLAN_35M') := '35';
  IF Lc_GetOlts%ISOPEN THEN
    CLOSE Lc_GetOlts;
  END IF;
  FOR I_GetOlts IN Lc_GetOlts
  LOOP
    Ln_IdElementoOlt     := I_GetOlts.ID_ELEMENTO;
    Lv_NombrePlan        := T_PlanesInfoTecnica.first;
    WHILE (Lv_NombrePlan IS NOT NULL)
    LOOP
      Lv_ValorLineProfileName := Lv_NombrePlan;
      Lv_ValorLineGemTraffic  := T_PlanesInfoTecnica(Lv_NombrePlan);
      SYS.DBMS_OUTPUT.PUT_LINE('OLT: '|| Ln_IdElementoOlt || ' , PLAN: ' || Lv_ValorLineProfileName || ' , LINE-PROFILE-ID, GEM-PORT, TRAFFIC-TABLE: ' || Lv_ValorLineGemTraffic);
      ---------------LINE-PROFILE-ID-----------------------
      Ln_IdDetElemLineProfileId := DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
      INSERT
      INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        (
          ID_DETALLE_ELEMENTO,
          ELEMENTO_ID,
          DETALLE_NOMBRE,
          DETALLE_VALOR,
          DETALLE_DESCRIPCION,
          USR_CREACION,
          FE_CREACION,
          IP_CREACION,
          REF_DETALLE_ELEMENTO_ID,
          ESTADO
        )
        VALUES
        (
          Ln_IdDetElemLineProfileId,
          Ln_IdElementoOlt,
          'LINE-PROFILE-ID',
          Lv_ValorLineGemTraffic,
          'LINE-PROFILE-ID',
          'mlcruz',
          CURRENT_TIMESTAMP,
          '127.0.0.1',
          NULL,
          'Activo'
        );
      SYS.DBMS_OUTPUT.PUT_LINE('LINE-PROFILE-ID ' || Ln_IdDetElemLineProfileId);
      ---------------LINE-PROFILE-NAME-----------------------
      Ln_IdDetElemLineProfileName := DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
      INSERT
      INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        (
          ID_DETALLE_ELEMENTO,
          ELEMENTO_ID,
          DETALLE_NOMBRE,
          DETALLE_VALOR,
          DETALLE_DESCRIPCION,
          USR_CREACION,
          FE_CREACION,
          IP_CREACION,
          REF_DETALLE_ELEMENTO_ID,
          ESTADO
        )
        VALUES
        (
          Ln_IdDetElemLineProfileName,
          Ln_IdElementoOlt,
          'LINE-PROFILE-NAME',
          Lv_ValorLineProfileName,
          Lv_ValorLineProfileName,
          'mlcruz',
          CURRENT_TIMESTAMP,
          '127.0.0.1',
          Ln_IdDetElemLineProfileId,
          'Activo'
        );
      SYS.DBMS_OUTPUT.PUT_LINE('LINE-PROFILE-NAME ' || Ln_IdDetElemLineProfileName);
      ---------------GEM-PORT-----------------------
      Ln_IdDetElemGemPort := DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
      INSERT
      INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        (
          ID_DETALLE_ELEMENTO,
          ELEMENTO_ID,
          DETALLE_NOMBRE,
          DETALLE_VALOR,
          DETALLE_DESCRIPCION,
          USR_CREACION,
          FE_CREACION,
          IP_CREACION,
          REF_DETALLE_ELEMENTO_ID,
          ESTADO
        )
        VALUES
        (
          Ln_IdDetElemGemPort,
          Ln_IdElementoOlt,
          'GEM-PORT',
          Lv_ValorLineGemTraffic,
          Lv_ValorLineGemTraffic,
          'mlcruz',
          CURRENT_TIMESTAMP,
          '127.0.0.1',
          Ln_IdDetElemLineProfileId,
          'Activo'
        );
      SYS.DBMS_OUTPUT.PUT_LINE('GEM-PORT ' || Ln_IdDetElemGemPort);
      ---------------TRAFFIC-TABLE-----------------------
      Ln_IdDetElemTrafficTable := DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL;
      INSERT
      INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
        (
          ID_DETALLE_ELEMENTO,
          ELEMENTO_ID,
          DETALLE_NOMBRE,
          DETALLE_VALOR,
          DETALLE_DESCRIPCION,
          USR_CREACION,
          FE_CREACION,
          IP_CREACION,
          REF_DETALLE_ELEMENTO_ID,
          ESTADO
        )
        VALUES
        (
          Ln_IdDetElemTrafficTable,
          Ln_IdElementoOlt,
          'TRAFFIC-TABLE',
          Lv_ValorLineGemTraffic,
          Lv_ValorLineGemTraffic,
          'mlcruz',
          CURRENT_TIMESTAMP,
          '127.0.0.1',
          Ln_IdDetElemLineProfileName,
          'Activo'
        );
      SYS.DBMS_OUTPUT.PUT_LINE('TRAFFIC-TABLE ' || Ln_IdDetElemTrafficTable);
      COMMIT;
      Lv_NombrePlan := T_PlanesInfoTecnica.next(Lv_NombrePlan);
    END LOOP;
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Creación de plantilla y alias para envío de notificación al crear una solicitud de aprobación para el plan de 25MB de Small Business(TELCOHOME)
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar el ingreso o activación de un servicio
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
      'Notificación que se envía a los gerentes y subgerentes al crear un servicio que necesita aprobación',
      'CREASOL_APROBSB',
      'TECNICO',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz',
      TO_CLOB(
      '     
<html>    
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
<td colspan="2">Estimado Subgerente/Gerente Comercial,</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
Por el presente se notifica la creación de las cuentas con producto {{ nombreProducto }} correspondiente al servicio detallado a continuación:                             
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
<td>{{ loginPuntoCliente }}</td>                        
</tr>'
      )
      || TO_CLOB(
      '                        
<tr>                            
<td>                                
<strong>Jurisdicción:</strong>                            
</td>                            
<td>                                
{{ nombreJurisdiccion }}                             
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Dirección:</strong>                            
</td>                            
<td>{{ direccionPuntoCliente }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Producto:</strong>                            
</td>                            
<td>{{ descripcionProducto }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Vendedor:</strong>                            
</td>                            
<td>{{ vendedor }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Subgerente:</strong>                            
</td>                            
<td>{{ subgerente }}</td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Tipo de Orden:</strong>                            
</td>                            
<td>    
{{ tipoOrden }}                              
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Fecha de Creación del Servicio:</strong>                            
</td>                            
<td>{{ fechaCreacionServicio }}</td>                        
</tr>'
      )
      || TO_CLOB(' 
<tr>                            
<td>                                
<strong>Tipo de Solicitud:</strong>                            
</td>                            
<td>{{ tipoSolicitud }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Velocidad:</strong>                            
</td>                            
<td><strong><label style="color:red">{{ velocidadIsb }}MB</label></strong></td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado de Solicitud:</strong>                            
</td>                            
<td><strong><label style="color:red">{{ estadoSolicitud }}</label></strong></td>                        
</tr>
{% if observacion!='''' %}                        
<tr>                            
<td>                                
<strong>Observación:</strong>                            
</td>                            
<td>{{ observacion | raw }}</td>                        
</tr>       
{% endif %}
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
{% if prefijoEmpresa == ''TN'' %}   
<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>   
{% endif %}   
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
  WHERE CODIGO='CREASOL_APROBSB';
  INSERT
  INTO DB_COMUNICACION.ADMI_ALIAS
    (
      ID_ALIAS,
      VALOR,
      ESTADO,
      EMPRESA_COD,
      CANTON_ID,
      DEPARTAMENTO_ID,
      FE_CREACION,
      USR_CREACION
    )
    VALUES
    (
      DB_COMUNICACION.SEQ_ADMI_ALIAS.NEXTVAL,
      'pvallejo@telconet.ec',
      'Activo',
      '10',
      NULL,
      NULL,
      SYSDATE,
      'mlcruz'
    );
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='pvallejo@telconet.ec'
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente CREASOL_APROBSB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Creación de plantilla y alias al aprobar o rechazar la solicitud de apaorbación de servicio del plan de 25MB de Small Business(TELCOHOME)
DECLARE
  Ln_IdPlantilla NUMBER(5,0);
  Ln_IdAlias     NUMBER(5,0);
BEGIN
  --Plantilla usada para notificar el ingreso o activación de un servicio
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
      'Notificación para los gerentes, subgerentes, asesores y asistentes al aprobar o rechazar un servicio',
      'APRB_RCHZ_SOLSB',
      'COMERCIAL',
      'Activo',
      CURRENT_TIMESTAMP,
      'mlcruz',
      TO_CLOB(
      '     
<html>    
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
<td colspan="2">Estimados,</td>                        
</tr>                        
<tr>                            
<td colspan="2">                                
Por el presente se notifica {{ accionMail }} de las cuentas con producto {{ nombreProducto }} correspondiente al servicio detallado a continuación:                             
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
<td>{{ loginPuntoCliente }}</td>                        
</tr>'
      )
      || TO_CLOB(
      '                        
<tr>                            
<td>                                
<strong>Jurisdicción:</strong>                            
</td>                            
<td>                                
{{ nombreJurisdiccion }}                             
</td>                        
</tr>                        
<tr>                            
<td>                                
<strong>Dirección:</strong>                            
</td>                            
<td>{{ direccionPuntoCliente }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Producto:</strong>                            
</td>                            
<td>{{ descripcionProducto }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Vendedor:</strong>                            
</td>                            
<td>{{ vendedor }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Subgerente:</strong>                            
</td>                            
<td>{{ subgerente }}</td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Tipo de Orden:</strong>                            
</td>                            
<td>    
{{ tipoOrden }}                              
</td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado del Servicio:</strong>                            
</td>                            
<td><strong>{{ estadoServicio }}</strong></td>                        
</tr>                  
<tr>                            
<td>                                
<strong>Fecha de Creación del Servicio:</strong>                            
</td>                            
<td>{{ fechaCreacionServicio }}</td>                        
</tr>'
      )
      || TO_CLOB(' 
<tr>                            
<td>                                
<strong>Tipo de Solicitud:</strong>                            
</td>                            
<td>{{ tipoSolicitud }}</td>                        
</tr>
<tr>                            
<td>                                
<strong>Velocidad:</strong>                            
</td>                            
<td>{{ velocidadIsb }}MB</td>                        
</tr>
<tr>                            
<td>                                
<strong>Estado de Solicitud:</strong>                            
</td>                            
<td><strong>{{ estadoSolicitud }}</strong></td>                        
</tr>
{% if observacion!='''' %}                        
<tr>                            
<td>                                
<strong>Observación:</strong>                            
</td>                            
<td>{{ observacion | raw }}</td>                        
</tr>       
{% endif %}
<tr>                            
<td>                                
<strong>Solicitud {{ accionUsuario }} por:</strong>                            
</td>                            
<td><strong>{{ nombreUsuarioGestion }}</strong></td>                        
</tr>
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
{% if prefijoEmpresa == ''TN'' %}   
<td><strong><font size="2" face="Tahoma">Telconet S.A.</font></strong></p>   
{% endif %}   
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
  WHERE CODIGO='APRB_RCHZ_SOLSB';
  SELECT ID_ALIAS
  INTO Ln_IdAlias
  FROM DB_COMUNICACION.ADMI_ALIAS
  WHERE VALOR     ='pvallejo@telconet.ec'
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó la plantilla correctamente APRB_RCHZ_SOLSB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                            || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Se crea un nuevo tipo de solicitud para los servicios que necesitan aprobación
INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
  (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
  )
  VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD APROBACION SERVICIO',
    SYSDATE,
    'mlcruz',
    NULL,
    NULL,
    'Activo',
    NULL,
    NULL,
    NULL
  );
--Se crea un nuevo tipo de solicitud para los servicios que necesitan autorización
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
    'ID_PERSONA_ROL',
    'N',
    'Activo',
    SYSDATE,
    'mlcruz',
    'COMERCIAL'
  );
COMMIT;
/