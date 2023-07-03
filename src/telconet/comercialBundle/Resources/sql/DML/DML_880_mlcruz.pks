SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamProdUM NUMBER(5,0);
BEGIN
  SELECT ID_PARAMETRO
  INTO Ln_IdParamProdUM
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='PRODUCTOS_ESPECIALES_UM'
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
    Ln_IdParamProdUM,
    'UM FTTX',
    'IPSB',
    'FTTx',
    'TN',
    '10',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el detalle del parámetro UM: FTTx, Producto: IPSB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de parámetros para IP_MAX_PERMITIDAS_PROD
DECLARE
  Ln_IdParamMapeoIpMax NUMBER(5,0);
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
      'IP_MAX_PERMITIDAS_PRODUCTO',
      'Mapeo de Ips máximas permitidas por producto principal y producto IP',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
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
    '20',
    '1',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro IP_MAX_PERMITIDAS_PROD para flujo con producto IPSB 20MB');
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
    '50',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Detalle de parámetro IP_MAX_PERMITIDAS_PROD para flujo con producto IPSB 50MB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Creación de la asociación de características al producto IP Small Business
DECLARE
  Ln_IdCaractRegistroUnitario   NUMBER(5,0);
  Ln_IdCaractScope              NUMBER(5,0);
  Ln_IdCaractMac                NUMBER(5,0);
  Ln_IdCaractVelocidad          NUMBER(5,0);
  Ln_IdProdSmallBusiness        NUMBER(5,0);
  Ln_IdProdIpSB                 NUMBER(5,0);
BEGIN
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractRegistroUnitario
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='REGISTRO_UNITARIO';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractScope
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='SCOPE';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractMac
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='MAC';
  SELECT ID_CARACTERISTICA
  INTO Ln_IdCaractVelocidad
  FROM DB_COMERCIAL.ADMI_CARACTERISTICA
  WHERE DESCRIPCION_CARACTERISTICA='VELOCIDAD';
  SELECT ID_PRODUCTO
  INTO Ln_IdProdSmallBusiness
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='INTERNET SMALL BUSINESS';
  SELECT ID_PRODUCTO
  INTO Ln_IdProdIpSB
  FROM DB_COMERCIAL.ADMI_PRODUCTO
  WHERE NOMBRE_TECNICO='IPSB';
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
      Ln_IdProdIpSB,
      Ln_IdCaractRegistroUnitario,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      NULL
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto IP SMALL BUSINESS Caracteristica REGISTRO_UNITARIO');
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
      Ln_IdProdIpSB,
      Ln_IdCaractScope,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto IP SMALL BUSINESS Caracteristica SCOPE');
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
      Ln_IdProdIpSB,
      Ln_IdCaractVelocidad,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'SI'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto IP SMALL BUSINESS Caracteristica VELOCIDAD');
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
      Ln_IdProdSmallBusiness,
      Ln_IdCaractMac,
      CURRENT_TIMESTAMP,
      'mlcruz',
      'Activo',
      'NO'
    );
  SYS.DBMS_OUTPUT.PUT_LINE('Creacion correctamente de registro Producto SMALL BUSINESS Caracteristica MAC');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
--Bloque anónimo para crear un nuevo proceso con una nueva tarea para la asignación de tarea a IPPCL2 por asignación de recursos de red de Ips
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
      'TAREAS DE IPCCL2 - Small Business',
      'TAREAS DE IPCCL2 - Small Business',
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
  WHERE NOMBRE_PROCESO='TAREAS DE IPCCL2 - Small Business';
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
      'APROVISIONAMIENTO DE IP',
      'Tarea para la asignación de IPs Small Business con última milla FTTx',
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
DECLARE
  Ln_IdParamNotifIp NUMBER(5,0);
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
      'INFO_NOTIF_IPSB',
      'Información general de la notificación que se enviará en un servicio IP Small Business',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );

  SELECT ID_PARAMETRO
  INTO Ln_IdParamNotifIp
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='INFO_NOTIF_IPSB'
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
    Ln_IdParamNotifIp,
    'Parámetro con el departamento y la tarea que se asignará al agregar un servicio IP Small Business',
    'PreAsignacionInfoTecnica',
    'IPCCL2',
    'APROVISIONAMIENTO DE IP',
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el detalle del parámetro INFO_NOTIF_IPSB');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;