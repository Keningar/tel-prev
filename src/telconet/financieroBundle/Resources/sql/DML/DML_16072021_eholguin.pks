/**
 * Scpit para creación de parámetros necesarios para lectura de formatos adicionales. 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0 16-07-2021 
 */
SET DEFINE OFF


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
    'FORMATO_E',
    '&gt;   &lt;',
    '&lt;/',
    '&gt;/',
    '&gt;',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    '&#xD;',
    '10',
    '<',
    '>',
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
    'FORMATO_F',
    '11000',
    'infoAdicional',
    'infoFactura',
    'xml version',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'razonSocialComprador',
    '10',
    'codigoRetenc',
    'agenteRetencion',
    NULL
  );