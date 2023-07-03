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
    'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE',
    'Se parametriza el numero de días a considerar para la ejecucion del alcance de Promociones en base a los ciclos de Facturación, y el numero de días a restar a la fecha de procesamiento con la cual se registrarán los mapeos y aplicaciones de promociones',
    'COMERCIAL',
    'PROMOCIONES',
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE'
      AND ESTADO             = 'Activo'
    ),
    'NUMERO_DIAS_PROCESO_ALCANCE',
    '1',
    'ALCANCE',
    'Numero de días a considerar para la ejecucion del alcance de Promociones en base a las fechas de Inicio de Ciclo de Facturación se restara los dias a considerarse para el proceso de alcance',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
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
    EMPRESA_COD
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'PROMOCIONES_PARAMETROS_EJECUCION_DE_ALCANCE'
      AND ESTADO             = 'Activo'
    ),
    'NUMERO_DIAS_FECHA_PROCESA_ALCANCE',
    '1',
    'ALCANCE',
    'Numero de días a restar a la fecha de procesamiento (Sysdate) con la cual se registrarán los mapeos y aplicaciones de promociones por los procesos de Alcances de ciclo1 y ciclo2',
    NULL,
    'Activo',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    'apenaherrera',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18'
  );
COMMIT;
/
