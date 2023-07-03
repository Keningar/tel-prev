SET SERVEROUTPUT ON
DECLARE
  Ln_IdParamsServiciosMd    NUMBER;
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
    'Valor1: Nombre de parámetro, Valor2: Nombre de proceso, Valor3: Remitente, Valor4: Asunto',
    'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO',
    'EXPORTAR_ARCHIVO_CORTE_MASIVO',
    'notificaciones_telcos@telconet.ec',
    'Notificación de clientes que cambiarán de plan masivamente',
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
    'Valor1: Nombre de parámetro, Valor2: Nombre de proceso, Valor3: Remitente, Valor4: Asunto',
    'REMITENTES_Y_ASUNTOS_CORREOS_POR_PROCESO',
    'LIBERAR_RECURSOS_FACTIBILIDAD',
    'notificaciones_telcos@telconet.ec',
    'Notificacion Reporte de servicios liberados por Factibilidad',
    NULL,
    'Activo',
    'mlcruz',
    sysdate,
    '127.0.0.1',
    '18'
  );
  SYS.DBMS_OUTPUT.PUT_LINE('Se han ingresado los parámetros con los remitentes y asuntos de correos por proceso');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK 
                           || ' - ERROR_BACKTRACE: ' || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);  
  ROLLBACK;
END;
/
DECLARE
  Ln_IdParamDirBdArchivosTmp NUMBER(5,0);
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
      'DIRECTORIO_BD_ARCHIVOS_TEMPORALES',
      'Directorio de Base de Datos destinado para la escritura de archivos temporales',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamDirBdArchivosTmp
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='DIRECTORIO_BD_ARCHIVOS_TEMPORALES'
  AND ESTADO            = 'Activo';
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
      Ln_IdParamDirBdArchivosTmp,
      'Valor1: Nombre de dir en la BD, Valor2: Ruta del dir en la BD',
      'RESPSOLARIS',
      '/respaldo/export/',
      NULL,
      NULL,
      NULL,
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el directorio de base de datos para los archivos temporales');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
DECLARE
  Ln_IdParamUrlMicroservicioNfs NUMBER(5,0);
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
      'URL_MICROSERVICIO_NFS',
      'Url del NFS',
      'Activo',
      'mlcruz',
      CURRENT_TIMESTAMP,
      '127.0.0.1'
    );
  SELECT ID_PARAMETRO
  INTO Ln_IdParamUrlMicroservicioNfs
  FROM DB_GENERAL.ADMI_PARAMETRO_CAB
  WHERE NOMBRE_PARAMETRO='URL_MICROSERVICIO_NFS'
  AND ESTADO            = 'Activo';
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
      Ln_IdParamUrlMicroservicioNfs,
      'Valor1: Url del NFS',
      'https://microservicios.telconet.ec/nfs/procesar',
      NULL,
      NULL,
      NULL,
      NULL,
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
  SYS.DBMS_OUTPUT.PUT_LINE('Se creó correctamente el parámetro para la url del NFS');
  COMMIT;
EXCEPTION
WHEN OTHERS THEN
  SYS.DBMS_OUTPUT.PUT_LINE('Error: '|| SQLCODE || ' - ERROR_STACK: ' || DBMS_UTILITY.FORMAT_ERROR_STACK || ' - ERROR_BACKTRACE: ' 
                           || DBMS_UTILITY.FORMAT_ERROR_BACKTRACE);
  ROLLBACK;
END;
/
BEGIN
  DBMS_SCHEDULER.DROP_JOB(job_name => '"DB_INFRAESTRUCTURA"."JOB_ELIMINA_CSVS_CORTE_MASIVO"',
                          defer    => false,
                          force    => true);
EXCEPTION
  WHEN OTHERS THEN
    DBMS_OUTPUT.PUT_LINE('ERROR AL ELIMINAR JOB_ELIMINA_CSVS_CORTE_MASIVO ');
END;
/
INSERT
INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  (
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.NEXTVAL,
    4,
    35,
    'TelcosWeb',
    '593',
    'MD',
    'Tecnico',
    'CorteMasivo',
    'Activo',
    SYSDATE,
    'mlcruz'
  );

INSERT
INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
  (
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.NEXTVAL,
    4,
    36,
    'TelcosWeb',
    '593',
    'MD',
    'Tecnico',
    'CambioPlanMasivo',
    'Activo',
    SYSDATE,
    'mlcruz'
  );
COMMIT;
/
