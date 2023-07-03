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
    'Mensaje a mostrar en TMO para validar que solo pause una tarea',
    'MSG_INFO_TAREA_PAUSADA',
    'Ya tiene una tarea pausada.',
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

UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET
    VALOR2 = '2',
    VALOR3 = 'N',
    USR_ULT_MOD = 'jnazareno',
    FE_ULT_MOD = SYSDATE
WHERE
    PARAMETRO_ID = (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_GENERALES_MOVIL'
    )
    AND VALOR1 = 'MAX_TAREAS_PERMITIDAS';

--------------------------------------------------------------------------------

--FIN DE PARÁMETROS PARA EL MOVIL

COMMIT;
