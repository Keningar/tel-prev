--Bloque anónimo para crear un nuevo proceso para registro de traslados md
SET SERVEROUTPUT ON
DECLARE
  Ln_IdProceso NUMBER(5,0);
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
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS_PROCESOS_MASIVOS_TELCOS',
    'Parámetros para el procesamiento masivo de cortes para clientes MD',
    'COMERCIAL',
    NULL,
    'Activo',
    'jbozada',
    SYSDATE,
    '127.0.0.1',
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT Id_Parametro
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE Nombre_Parametro='PARAMETROS_PROCESOS_MASIVOS_TELCOS'
     AND estado            ='Activo'
    ),
    'DATOS_WS_TOKEN',
    'PROCESOS_MASIVOS_MD',
    'InternetProtegidoWSController',
    'procesarAction',
    'PROCESOS_MASIVOS_MD',
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'http://172.24.15.60/ws/token-security/rest/token/generate',
    '18',
    '127.0.0.1',
    'IP',
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT Id_Parametro
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE Nombre_Parametro='PARAMETROS_PROCESOS_MASIVOS_TELCOS'
     AND estado            ='Activo'
    ),
    'DATOS_WS_TELCOS',
    'http://telcos-ws-lb.telconet.ec/rs/tecnico/ws/rest/internetProtegido',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT Id_Parametro
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE Nombre_Parametro='PARAMETROS_PROCESOS_MASIVOS_TELCOS'
     AND estado            ='Activo'
    ),
    'DATOS_MW',
    'http://middleware.netlife.net.ec/ws/process',
    'SI',
    'SI',
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT Id_Parametro
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE Nombre_Parametro='PARAMETROS_PROCESOS_MASIVOS_TELCOS'
     AND estado            ='Activo'
    ),
    'CANTIDAD_INTENTOS_MAX',
    '3',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT Id_Parametro
     FROM DB_GENERAL.ADMI_PARAMETRO_CAB
     WHERE Nombre_Parametro='PARAMETROS_PROCESOS_MASIVOS_TELCOS'
     AND estado            ='Activo'
    ),
    'NUEVO_PROCESAMIENTO_MASIVO',
    'SI',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jbozada',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );


  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
