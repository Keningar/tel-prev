----INICIO DE PARÁMETROS PARA EL MOVIL

INSERT INTO db_general.admi_parametro_det 
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
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'NOMBRE SOURCE MOVIL',
    'NOMBRE_SOURCE_MOVIL',
    'ec.telconet.mobile.telcos.operaciones',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det 
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
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'ORIGIN ID MOVIL',
    'ORIGIN_ID_MOVIL',
    '0',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det 
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
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'TIPO ORIGIN ID',
    'TIPO_ORIGIN_ID',
    'ID',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--------------------------------------------------------------------------------

--FIN DE PARÁMETROS PARA EL MOVIL

COMMIT;
