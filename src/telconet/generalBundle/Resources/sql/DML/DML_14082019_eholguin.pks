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
    'FECHAS_NOTIFICACION_PTOSNOFACT',
    'PARAMETRO PARA CONFIGURAR LOS RANGOS DE FECHAS A SER CONSIDERADOS EN CONSULTA DE PUNTOS NO FACTURADOS',
    'FINANCIERO',
    'NOTIFICACIONES',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0'
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FECHAS_NOTIFICACION_PTOSNOFACT'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'Permite especificar cuanto tiempo se le quitara al sysdate para considerarlo como fecha desde',
    'VALIDACION_FECHA_DESDE',
    'S',
    '72',
    'hour',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    '10',
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FECHAS_NOTIFICACION_PTOSNOFACT'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'Permite especificar cuanto tiempo se le quitara al sysdate para considerarlo como fecha hasta',
    'VALIDACION_FECHA_HASTA',
    'S',
    '0',
    'hour',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    '10',
    NULL,
    NULL,
    NULL
  );



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
    'NOTIFICACION_PTOS_NO_FACTURADOS',
    'PARAMETRO PARA CONFIGURAR LAS EMPRESAS QUE SE QUIERE NOTIFICAR.',
    'FINANCIERO',
    'NOTIFICACIONES',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0'
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'NOTIFICACION_PTOS_NO_FACTURADOS'
      AND ESTADO             = 'Activo'
    ),
    'Permite especificar el prefijo de la empresa md que se quiere notificar los puntos no facturados',
    'NOTIFICACION_PTOS_NO_FACTURADOS',
    'TN',
    'S',
    'telcos',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    'eholguin',
    NULL,
    NULL,
    'El punto ha sido notificado al usuario como no facturado. ',
    '10',
    NULL,
    NULL,
    NULL
  );



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
    'PTOS_NOFACT_HEADERS',
    'Permite especificar las cabeceras de la notificaciones para puntos no facturados de TN ',
    'FINANCIERO',
    'NOTIFICACIONES',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1'
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PTOS_NOFACT_HEADERS'
      AND ESTADO             = 'Activo'
    ),
    'Permite especifiar el emisor, receptor(es) y Asunto de la notificación de puntos no facturados. ',    
    'PTOS_NOFACT_HEADERS',
    'notificaciones_telcos@telconet.ec',
    'Puntos No Facturados',
    'eholguin@telconet.ec',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'NULL',
    '10',
    NULL,
    NULL,
    NULL
  );

COMMIT;
/
