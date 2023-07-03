SET SERVEROUTPUT ON
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR5         = 'HOMETN'
WHERE PARAMETRO_ID =
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'PRODUCTOS_ESPECIALES_UM'
  AND ESTADO             = 'Activo'
  )
AND VALOR1 = 'TELCOHOME'
AND ESTADO = 'Activo';
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR3         = 'INTERNET SMALL BUSINESS'
WHERE PARAMETRO_ID =
  (SELECT ID_PARAMETRO
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO = 'MAPEO_VELOCIDAD_PERFIL'
  AND ESTADO             = 'Activo'
  )
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
    EMPRESA_COD,
    VALOR6,
    VALOR7
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    3,
    'VLAN HOMETN',
    'HOMETN',
    '301',
    NULL,
    NULL,
    'Activo',
    'mlcruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );
COMMIT;
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
    'Mapeo de perfiles de acuerdo a la velocidad del producto TelcoHome',
    '10',
    'TN_home_10M_1',
    'TELCOHOME',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MAPEO_VELOCIDAD_PERFIL para flujo con producto TelcoHome');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de perfiles para el producto TelcoHome con tipo de negocio HOMETN
DECLARE
  Ln_IdParamMigraPerfilV2 NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamMigraPerfilV2
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='MIGRA_PLANES_MASIVOS_PERFIL_V2';
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
    Ln_IdParamMigraPerfilV2,
    'EQUIVALENCIA_PERFIL',
    'CNR',
    'HUAWEI',
    'PERFIL_H_HOME_TN_DEFAULT',
    'HOMETN',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NO',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_V2 para flujo con producto TelcoHome');
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
    Ln_IdParamMigraPerfilV2,
    'EQUIVALENCIA_PERFIL',
    'CNR',
    'TELLION',
    'PERFIL_T_HOME_TN_DEFAULT',
    'HOMETN',
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NO',
    '10'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_V2 para flujo con producto TelcoHome');
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
    'TN_home_10M_1',
    'PERFIL_H_HOME_TN_DEFAULT',
    'TN_HOME_10M',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con producto TelcoHome - Huawei');
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
    'TN_home_10M_1',
    'PERFIL_T_HOME_TN_DEFAULT',
    'TN_home_10M_1',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con producto TelcoHome - Tellion');
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
    'TN_HOME_10M',
    'TN_HOME_10M',--detalle valor del olt, perfil jar
    'TN_HOME_10M',--valor del perfil equivalente
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro CNR_PERFIL_CLIENT_PCK para flujo con producto TelcoHome');
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
  Ln_IdParamEqNoAcept NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamEqNoAcept
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
    Ln_IdParamEqNoAcept,
    'TN_home_10M_1',
    'TN_home_10M_1',--valor del perfil equivalente
    'PLAN_10M',--client class
    '18',--package id
    'HOMETN',--tipo de Negocio
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EQ_NUEVOS_PLANES_NO_ACEPTACION para flujo con producto TelcoHome');
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
  T_PlanesInfoTecnica('TN_HOME_10M') := '12';
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
      SYS.DBMS_OUTPUT.PUT_LINE('OLT: '|| Ln_IdElementoOlt || ' , PLAN: ' || Lv_ValorLineProfileName 
                               || ' , LINE-PROFILE-ID, GEM-PORT, TRAFFIC-TABLE: ' || Lv_ValorLineGemTraffic);
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