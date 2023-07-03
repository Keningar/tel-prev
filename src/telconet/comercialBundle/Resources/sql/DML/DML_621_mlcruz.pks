--Se agrega una nueva última milla FTTx para el producto Internet Small Business
INSERT
INTO DB_INFRAESTRUCTURA.ADMI_TIPO_MEDIO
  (
    ID_TIPO_MEDIO,
    CODIGO_TIPO_MEDIO,
    NOMBRE_TIPO_MEDIO,
    DESCRIPCION_TIPO_MEDIO,
    ESTADO,
    USR_CREACION,
    FE_CREACION
  )
  VALUES
  (
    DB_INFRAESTRUCTURA.SEQ_ADMI_TIPO_MEDIO.NEXTVAL,
    'FTTx',
    'FTTx',
    'Tipo de fibra óptica',
    'Activo',
    'mlcruz',
    CURRENT_TIMESTAMP
  );
COMMIT;

--Bloque anónimo para crear un nuevo proceso con una nueva tarea para la última milla FTTx
SET SERVEROUTPUT ON
DECLARE
  Ln_IdProceso NUMBER(5,0);
BEGIN
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO
    (
      ID_PROCESO,
      NOMBRE_PROCESO,
      DESCRIPCION_PROCESO,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD,
      VISIBLE
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,
      'SOLICITAR NUEVO SERVICIO FTTx',
      'SOLICITAR NUEVO SERVICIO FTTx',
      'Activo',
      'mlcruz',
      'mlcruz',
      SYSDATE,
      SYSDATE,
      'NO'
    );
  SELECT ID_PROCESO
  INTO Ln_IdProceso
  FROM DB_SOPORTE.ADMI_PROCESO
  WHERE NOMBRE_PROCESO='SOLICITAR NUEVO SERVICIO FTTx';
  INSERT
  INTO DB_SOPORTE.ADMI_TAREA
    (
      ID_TAREA,
      PROCESO_ID,
      NOMBRE_TAREA,
      DESCRIPCION_TAREA,
      ESTADO,
      USR_CREACION,
      USR_ULT_MOD,
      FE_CREACION,
      FE_ULT_MOD
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
      Ln_IdProceso,
      'FTTx: INSTALACION UM',
      'Tarea que ejecuta el tendido de Ultima Milla del Servicio con Fibra Óptica y tecnología FTTx.',
      'Activo',
      'mlcruz',
      'mlcruz',
      SYSDATE,
      SYSDATE
    );
  INSERT
  INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA
    (
      ID_PROCESO_EMPRESA,
      PROCESO_ID,
      EMPRESA_COD,
      ESTADO,
      USR_CREACION,
      FE_CREACION
    )
    VALUES
    (
      DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,
      Ln_IdProceso,
      '10',
      'Activo',
      'mlcruz',
      SYSDATE
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Registros de proceso y tarea ingresados correctamente');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
--Creación de la asociación de características al producto Internet Small Business
DECLARE
  Ln_IdCaractTrafficTable       NUMBER(5,0);
  Ln_IdCaractGemPort            NUMBER(5,0);
  Ln_IdCaractLineProfileName    NUMBER(5,0);
  Ln_IdCaractServiceProfile     NUMBER(5,0);
  Ln_IdCaractVlan               NUMBER(5,0);
  Ln_IdCaractGrupoNegocio       NUMBER(5,0);
  Ln_IdCaractScope              NUMBER(5,0);
  Ln_IdCaractIndiceCliente	    NUMBER(5,0);
  Ln_IdCaractSpid               NUMBER(5,0);
  Ln_IdCaractSsid               NUMBER(5,0);
  Ln_IdCaractPasswSsid          NUMBER(5,0);
  Ln_IdCaractNumeroPc           NUMBER(5,0);
  Ln_IdCaractModoOperacion      NUMBER(5,0);
  Ln_IdCaractMacOnt             NUMBER(5,0);
  Ln_IdCaractPotencia           NUMBER(5,0);
  Ln_IdProdInternetLite         NUMBER(5,0);
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractTrafficTable
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='TRAFFIC-TABLE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractGemPort
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='GEM-PORT';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractLineProfileName
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='LINE-PROFILE-NAME';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractServiceProfile
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SERVICE-PROFILE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractVlan
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='VLAN';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractGrupoNegocio
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='Grupo Negocio';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractScope
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SCOPE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractIndiceCliente
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='INDICE CLIENTE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractSpid
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SPID';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractSsid
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SSID';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPasswSsid
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='PASSWORD SSID';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractNumeroPc
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='NUMERO PC';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractModoOperacion
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MODO OPERACION';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMacOnt
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MAC ONT';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractPotencia
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='POTENCIA';

  SELECT ID_PRODUCTO
  INTO Ln_IdProdInternetLite
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='INTERNET SMALL BUSINESS';
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
      Ln_IdProdInternetLite,
      Ln_IdCaractTrafficTable,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica TRAFFIC-TABLE');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractGemPort,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica GEM-PORT');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractLineProfileName,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica LINE-PROFILE-NAME');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractServiceProfile,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica SERVICE-PROFILE');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractVlan,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica VLAN');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractGrupoNegocio,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'SI'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica Grupo Negocio');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractScope,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica SCOPE');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractIndiceCliente,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica INDICE CLIENTE');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractSpid,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica SPID');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractSsid,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica SSID');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractPasswSsid,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica PASSWORD SSID');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractNumeroPc,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica NUMERO PC');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractModoOperacion,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica MODO OPERACION');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractMacOnt,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica MAC ONT');
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
      Ln_IdProdInternetLite,
      Ln_IdCaractPotencia,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto INTERNET SMALL BUSINESS Caracteristica POTENCIA');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de VLAN para el nuevo tipo de negocio PYMETN
DECLARE
  Ln_IdParamVlanHuawei NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamVlanHuawei
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='VLAN_HUAWEI';
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
    Ln_IdParamVlanHuawei,
    'VLAN PYMETN',
    'PYMETN',
    '302',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro VLAN_HUAWEI con tipo de negocio PYMETN para flujo con producto Internet Small Business');
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
      'MAPEO_VELOCIDAD_PERFIL',
      'Mapeo de perfiles de acuerdo a la velocidad del producto Internet Small Business',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
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
    '20',
    'TN_fijo_20M_5',
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
    '50',
    'TN_fijo_50M_5',
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
--Creación de perfiles para el producto Internet Small Business con tipo de negocio PYMETN
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
    'PERFIL_H_PYME_TN_DEFAULT',
    'PYMETN',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalles de parámetro MIGRA_PLANES_MASIVOS_PERFIL_V2 para flujo con producto Internet Small Business');
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
    'TN_fijo_20M_5',
    'PERFIL_H_PYME_TN_DEFAULT',
    'TN_PLAN_20M',
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
    'TN_fijo_50M_5',
    'PERFIL_H_PYME_TN_DEFAULT',
    'TN_PLAN_50M',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro MIGRA_PLANES_MASIVOS_PERFIL_EQUI_V2 para flujo con producto Internet Small Business');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de valores requeridos para el LDAP(con los datos anteriores descritos para pruebas, se deberá crear este parámetro, AUN NO CREADO)
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
    'TN_PLAN_20M',
    'TN_PLAN_20M',--detalle valor del olt, perfil jar
    'TN_PLAN_20M',--valor del perfil equivalente
    '19',--package id
    'PLAN_20M',--client class
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
    'TN_PLAN_50M',
    'TN_PLAN_50M',--detalle valor del olt, perfil jar
    'TN_PLAN_50M',--valor del perfil equivalente
    '43',--package id
    'PLAN_50M',--client class
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
--Se agrega un nuevo detalle para el parámetro con la última milla FTTx
DECLARE
  Ln_IdParametroEmpresaEquiv NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParametroEmpresaEquiv
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='EMPRESA_EQUIVALENTE';
  INSERT
  INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
      ID_PARAMETRO_DET,
      PARAMETRO_ID,
      DESCRIPCION,
      VALOR1,
      VALOR2,
      VALOR3,
      ESTADO,
      USR_CREACION,
      FE_CREACION,
      IP_CREACION,
      EMPRESA_COD
    )
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
      Ln_IdParametroEmpresaEquiv,
      'TN Y FTTx RETORNA MD',
      'TN',
      'FTTx',
      'MD',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1',
      NULL
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro EMPRESA_EQUIVALENTE para flujo con última milla FTTx');
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
      (SELECT *
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
  T_PlanesInfoTecnica('TN_PLAN_20M') := '19';
  T_PlanesInfoTecnica('TN_PLAN_50M') := '43';
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
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
