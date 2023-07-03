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
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'VALIDA_PERMITE_PROCESO_INDIVIDUAL',
    'S',
    'Si es S pregunta por el parametro NUMERO_PROCESO_PERMITIDOS si es N no realiza ninguna acción. ',
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
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
      WHERE NOMBRE_PARAMETRO = 'PROCESO_EMER_SANITARIA'
      AND ESTADO             = 'Activo'
    ),
    'NUMERO_PROCESO_PERMITIDOS',
    '1',
    'Permite ejecutar Proceso de Diferidos luego de #(N) proceso(s) masivo(s) ejecutado(s) en estado Pendiente/Finalizado por punto cliente',
    NULL,
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
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