-- Insert de cabecera de parámetro ESTADOS_DOCUMENTO_FINANCIERO
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
    'ESTADOS_DOCUMENTO_FINANCIERO',
    'Define los estados necesarios para excluir documentos financieros, en recibo por caja',
    'FINANCIERO',
    'IMPRIMIR_RECIBO_PAGO',
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null
  );


-- Insert de detalles de parámetro ESTADOS_DOCUMENTO_FINANCIERO
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Pendiente',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Anulado',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Anulada',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Inactivo',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Inactiva',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Rechazada',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Rechazado',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'null',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'PendienteError',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'PendienteSri',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
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
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_DOCUMENTO_FINANCIERO'),
    'Estado necesario para excluir documentos financieros, en recibo por caja',
    'Eliminado',
    null,
    null,
    null,
    'Activo',
    'hlozano',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
  );

COMMIT;