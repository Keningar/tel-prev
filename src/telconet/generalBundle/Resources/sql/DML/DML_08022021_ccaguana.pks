--INICIO PARAMETROS WEB



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
    'Valor con el cual se filtrara días anteriores para el cierre de eventos abiertos TM OPERACIONES',
    'DIAS_VALIDACION_EVENTOS_CIERRE',
    '15',
    'N',
    NULL,
    'Activo',
    'ccaguana',
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
    'Valor con el cual se filtrara las horas en un rango para los eventos actuales TM OPERACIONES',
    'HORAS_VALIDACION_EVENTOS_ACTUALES',
    '12',
    'N',
    NULL,
    'Activo',
    'ccaguana',
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





--FIN DE PARÁMETROS PARA LA WEB
COMMIT;
