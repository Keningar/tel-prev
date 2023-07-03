/**
 * Scpit para creación de parámetros necesarios para lectura de formatos adicionales. 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 28-07-2021 
 */

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
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADO CLIENTE',
    'Activo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
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
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADO CLIENTE',
    'Cancelado',
    NULL,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
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
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'ESTADO CLIENTE',
    'Inactivo',
    NULL,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL
  );

  UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET VALOR1='0.03' WHERE DESCRIPCION = 'MARGEN ERROR';
  COMMIT;
/
