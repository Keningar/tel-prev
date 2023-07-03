--=======================================================================
--      Se crea cabecera de parámetro para manejo de validaciones Técnicas
--=======================================================================
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
    'VALIDACIONES TELCOS TECNICO',
    'Permitir ignorar o utilizar ciertas validaciones necesarias en procesos Técnicos.',
    'TECNICO',
    'TECNICO',
    'Activo',
    'jbozada',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--      Se crea detalle con información a utilizar
--=======================================================================
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
    (SELECT ID_PARAMETRO
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'VALIDACIONES TELCOS TECNICO'
    ),
    'CAMBIO PUERTO LOGICO',
    'SI',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jbozada',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

COMMIT;