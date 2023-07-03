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
    'RPT_PTOS_FACT_INSTALACION',
    'PARAMETRO PARA CONFIGURAR LOS VALORES NECESARIOS PARA GENERACION DE REPORTE DE PUNTOS A FACTURAR POR INSTALACION',
    'FINANCIERO',
    'NOTIFICACIONES',
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
      WHERE NOMBRE_PARAMETRO = 'RPT_PTOS_FACT_INSTALACION'
      AND ESTADO             = 'Activo'
    ),
    'Configura emisor, receptor(es) y Asunto de la notificación de puntos a facturar por instalación.',    
    'PTOS_FACT_INST',
    'notificaciones_telcos@telconet.ec',
    'Reporte de Puntos a  Facturar por Instalación',
    'sistemas-qa@telconet.ec',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'N',
    '10',
    NULL,
    NULL,
    NULL
  );

COMMIT;
/
