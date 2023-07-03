INSERT 
INTO 
  DB_GENERAL.ADMI_PARAMETRO_CAB 
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
    'PROM_TENTATIVA_MENSAJES',
    'PARAMETRO PADRE PARA MANEJAR MENSAJES PREVIOS A LA EVALUACIÓN DE PROMOCIONES TANTATIVAS DE TIPO INST.',
    'COMERCIAL',
    'PROM_TENTATIVA_MENSAJES',
    'Activo',
    'jcandelario',
    SYSDATE,
    '127.0.0.1',
    'jcandelario',
    SYSDATE,
    '127.0.0.1');
  
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
      WHERE NOMBRE_PARAMETRO = 'PROM_TENTATIVA_MENSAJES'
      AND ESTADO             = 'Activo'
    ),
    'CLIENTE_CANAL',
    'Cliente Canal no genera facturas de instalación.',
    '100',
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_TENTATIVA_MENSAJES'
      AND ESTADO             = 'Activo'
    ),
    'MIGRACION_TECNOLOGIA',
    'El tipo de origen del punto no genera facturas de instalación.',
    '100',
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
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
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROM_TENTATIVA_MENSAJES'
      AND ESTADO             = 'Activo'
    ),
    'EXISTE_FACTURA',
    'El punto ya tiene generada una factura de instalación.',
    '0',
    NULL,
    NULL,
    'Activo',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    'jcandelario',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    NULL
  );
  
COMMIT;
/