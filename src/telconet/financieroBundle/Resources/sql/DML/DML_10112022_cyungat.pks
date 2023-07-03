INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'ACT_TAR_ABU',
    'Notificacion REPORTE PROCESO MASIVO POR ACTUALIZACION DE TARJETAS ABU', 'notificaciones_telcos@telconet.ec',
     NULL, NULL, NULL, NULL, NULL, 'Activo', 'cyungat', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Mensajes parametrizados para el proceso de tarjetas ABU; VALOR1: Asunto del e-mail, VALOR2: Remitente del e-mail'); 
commit;

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'ACTUALIZACION_TC_ABU_MD',
    'JOB_EJECUTA_PMA_ACTUALIZA_ABU', '',
     NULL, NULL, NULL, NULL, NULL, 'Activo', 'cyungat', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Parametro para el control de ejecucion del JOB EJECUTA PMA ACTUALIZA ABU '); 

commit;

    INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
    VALOR7,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PARAM_TARJETAS_ABU'
      AND ESTADO             = 'Activo'
    ),
    'ESTADO_ARCHIVO',
    'PROCESANDO', 'ERROR',
     'FINALIZADO', NULL, NULL, NULL, NULL, 'Activo', 'cyungat', SYSDATE, '127.0.0.1', '18',
    'DESCRIPCION: Estado de los archivos: PROCESANDO cuando el JOB aun esta generando el reporte del archivo, ERROR si el archivo esta corrupto, Finalizado el reporte ha sido generado y enviado via e-mail');
commit;
