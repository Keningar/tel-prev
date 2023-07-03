--=======================================================================
--   Se crea parámetro "LIMITES DE US DE RACK DC" para que telcos 
--   dibuje las Us de rack según las posiciones correspondientes a cada 
--   marca de rack.
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
    DB_GENERAL.seq_ADMI_PARAMETRO_CAB.nextval,
    'LIMITES DE US DE RACK DC',
    'LIMITES DE US DE RACK DC',
    'TECNICO',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--   Se crea detalle de parámetro "LIMITES DE US DE RACK DC" para la
--   marca RINORACK. valor2 corresponde a la U inicial, valor3 
--   corresponde a la U final.
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.seq_ADMI_PARAMETRO_DET.nextval,
    (
        select ID_PARAMETRO
        from DB_GENERAL.ADMI_PARAMETRO_CAB
        where NOMBRE_PARAMETRO='LIMITES DE US DE RACK DC'
    ),
    'LIMITES DE US DE RACK DC',
    'RINORACK',
    '42',
    '1',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--   Se crea detalle de parámetro "LIMITES DE US DE RACK DC" para la
--   marca APC . valor2 corresponde a la U inicial, valor3 
--   corresponde a la U final.
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.seq_ADMI_PARAMETRO_DET.nextval,
    (
        select ID_PARAMETRO
        from DB_GENERAL.ADMI_PARAMETRO_CAB
        where NOMBRE_PARAMETRO='LIMITES DE US DE RACK DC'
    ),
    'LIMITES DE US DE RACK DC',
    'APC',
    '1',
    '42',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--   Se crea detalle de parámetro "LIMITES DE US DE RACK DC" para la
--   marca SIEMON. valor2 corresponde a la U inicial, valor3 
--   corresponde a la U final.
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.seq_ADMI_PARAMETRO_DET.nextval,
    (
        select ID_PARAMETRO
        from DB_GENERAL.ADMI_PARAMETRO_CAB
        where NOMBRE_PARAMETRO='LIMITES DE US DE RACK DC'
    ),
    'LIMITES DE US DE RACK DC',
    'SIEMON',
    '1',
    '42',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );

--=======================================================================
--   Se crea detalle de parámetro "LIMITES DE US DE RACK DC" para la
--   marca GENERICO. valor2 corresponde a la U inicial, valor3 
--   corresponde a la U final.
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
  )
  VALUES
  (
    DB_GENERAL.seq_ADMI_PARAMETRO_DET.nextval,
    (select ID_PARAMETRO
     from DB_GENERAL.ADMI_PARAMETRO_CAB
     where NOMBRE_PARAMETRO='LIMITES DE US DE RACK DC'),
    'LIMITES DE US DE RACK DC',
    'GENERICO',
    '1',
    '42',
    NULL,
    'Activo',
    'kyrodriguez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );

commit;
/