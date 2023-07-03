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
    'ENMASCARA TARJETA CUENTA',
    'PARAMETRO PARA CONFIGURAR EL NÚMERO DE DIGITOS Y EL CARACTER A UTILIZAR PARA ENMASCARAR EL NÚMERO DE TARJETA O CUENTA ',
    'FINANCIERO',
    'CAMBIO FORMA PAGO',
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
      WHERE NOMBRE_PARAMETRO = 'ENMASCARA TARJETA CUENTA'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'ENMASCARA NUMERO TARJETA CUENTA',
    '3',
    '3',
    'X',
    NULL,
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
    'V1 es el número de dígitos a enmascarar de izquierda a derecha, V2 es el número de dígitos a enmascarar de derecha a izquierda, V3 caracter utilizado para enmascarar '
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
      WHERE NOMBRE_PARAMETRO = 'ENMASCARA TARJETA CUENTA'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'ENMASCARA NUMERO TARJETA CUENTA',
    '3',
    '3',
    'X',
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    NULL,
    NULL,
    'V1 es el número de dígitos a enmascarar de izquierda a derecha, V2 es el número de dígitos a enmascarar de derecha a izquierda, V3 caracter utilizado para enmascarar '
  );


COMMIT;
/
