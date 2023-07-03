----INICIO DE PARÁMETRO DE BANDERA PARA AUDITORÍA DE COORDINADORES

INSERT INTO db_general.admi_parametro_cab 
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
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'BANDERA_AUDITORIA_COORDINADOR',
    'PARÁMETRO DE BANDERA PARA AUDITORÍA DE COORDINADORES',
    'COMUNICACIONES',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
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
            nombre_parametro = 'BANDERA_AUDITORIA_COORDINADOR'
    ),
    'PARÁMETRO DE BANDERA PARA AUDITORÍA DE COORDINADORES',
    'S',
    NULL,
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

--FIN DE PARÁMETRO DE BANDERA PARA AUDITORÍA DE COORDINADORES

COMMIT;
/   