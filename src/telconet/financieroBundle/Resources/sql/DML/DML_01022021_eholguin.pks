SET DEFINE OFF

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
    'AUTOMATIZACION_RETENCIONES',
    'PARAMETRO PARA CONFIGURAR LOS VALORES CORRESPONDIENTES A UNA RETENCION',
    'FINANCIERO',
    'AUTOMATIZACION_RETENCIONES',
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
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION_RETENCIONES'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'TAG COMPROBANTE',
    'autorizacion',
    'estado',
    'numeroAutorizacion',
    'fechaAutorizacion',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'ambiente',
    '10',
    'comprobante',
    'comprobanteRetencion',
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
    'TAG INFO TRIBUTARIA',
    'infoTributaria',
    'razonSocial',
    'ruc',
    'codDoc',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'estab',
    '10',
    'ptoEmi',
    'secuencial',
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
    'TAG INFO COMP RETENCION',
    'infoCompRetencion',
    'fechaEmision',
    'obligadoContabilidad',
    'tipoIdentificacionSujetoRetenido',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'razonSocialSujetoRetenido',
    '10',
    'identificacionSujetoRetenido',
    'periodoFiscal',
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
    'TAG IMPUESTOS',
    'impuestos',
    'impuesto',
    'codigo',
    'baseImponible',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'porcentajeRetener',
    '10',
    'valorRetenido',
    'numDocSustento',
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
    'TAG DOC SUSTENTO',
    'docsSustento',
    'docSustento',
    'codSustento',
    'numDocSustento',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'fechaEmisionDocSustento',
    '10',
    'numAutDocSustento',
    'totalSinImpuestos',
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
    'TAG IMPUESTOS DOC SUSTENTO',
    'impuestosDocSustento',
    'impuestoDocSustento',
    'codImpuestoDocSustento',
    'codigoPorcentaje',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'baseImponible',
    '10',
    'tarifa',
    'valorImpuesto',
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
    'TAG RETENCIONES',
    'retenciones',
    'retencion',
    'codigo',
    'codigoRetencion',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'baseImponible',
    '10',
    'porcentajeRetener',
    'valorRetenido',
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
    'TAG FECHA',
    'year',
    'month',
    'day',
    'fechaAutorizacion',
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
    'FORMATO_A',
    '&lt;![CDATA[',
    '&gt;]]',
    '"</comprobante>',
    '</comprobante>',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    '[<?xml version="1.0" encoding="UTF-8"?>',
    '10',
    '[',
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
    'FORMATO_B',
    '&lt;![CDATA[&lt;',
    '&lt;',
    '&gt;]]&gt;',
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    '[<?xml version="1.0" encoding="utf-16" standalone="no"?>',
    '10',
    '[',
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
    'FORMATO_C',
    'respuestaComprobante',
    'claveAccesoConsultada',
    'numeroComprobantes',
    'autorizaciones',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'autorizacion',
    '10',
    '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/"><soap:Body><ns2:autorizacionComprobanteResponse xmlns:ns2="http://ec.gob.sri.ws.autorizacion">',
    '</ns2:autorizacionComprobanteResponse></soap:Body></soap:Envelope>',
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
    'FORMATO_D',
    '<![CDATA[<?xml version="1.0" encoding="utf-8" standalone="yes"?>',
    ']]',
    'UTF-16',
    'utf-16',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'utf-8',
    '10',
    '<autorizacion>',
    '</autorizaciones>',
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
    EMPRESA_COD
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
    'CONFIGURACION MOTIVO',
    'pagos',
    'Automatización Retenciones',
    'automatizacionRetenciones',
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    '10'
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
    'ESTADO XML',
    'PENDIENTE',
    'AUTORIZADO',
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
    'NUMERO DOCUMENTO',
    '0',
    '3',
    '6',
    '-',
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
    'Configura las posiciones necesarias para formatear el valor  que contiene el tag numDocSustento del xml acorde al formato del número de factura'
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
    'CONFIGURACION NFS',
    'AutomatizacionRetenciones',
    'TelcosWeb',
    'Pagos',
    'ReporteTributacion',
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
    'Configura los parámetros enviados al NFS donde se almacenarán los archivos xml de retención'
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
    'FORMA PAGO',
    '1',
    '1.00',
    'RF1',
    'RETENCION FUENTE 1%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '1',
    '2.00',
    'RF2',
    'RETENCION FUENTE 2%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '1',
    '8.00',
    'RF8',
    'RETENCION FUENTE 8%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '1',
    '1.75',
    'RF17',
    'RETENCION FUENTE 1.75%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '1',
    '2.75',
    'RF27',
    'RETENCION FUENTE 2.75%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '2',
    '10.00',
    'RI1',
    'RETENCION IVA 10%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '2',
    '20.00',
    'RI20',
    'RETENCION IVA 20%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '2',
    '30.00',
    'RTIV',
    'RETENCION IVA 30%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '2',
    '70.00',
    'RI70',
    'RETENCION IVA 70%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '2',
    '50.00',
    'RI50',
    'RETENCION IVA 50%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'FORMA PAGO',
    '2',
    '100.00',
    'RI10',
    'RETENCION IVA 100%',
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
    'Formato del número de documento, valor1 codigo xml,valor2 porcentajeRetener xml,valor3 codigo forma pago'
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
    'TIPO FORMA PAGO',
    'RETENCION',
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
    'Especifica el tipo de forma de pago.'
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
    'COD_RET_FUENTE',
    '1',
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
    'Valor correspondiente al CODIGO RETENCION FUENTE'
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
    'COD_RET_IVA',
    '2',
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
    'Valor correspondiente al CODIGO RETENCION IVA'
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
    'MARGEN ERROR',
    '0.01',
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
    'Valor correspondiente al margen de error permitido en las diferencias entre valores de base imponible.'
  );

    INSERT INTO DB_GENERAL.ADMI_GESTION_DIRECTORIOS
    (
      ID_GESTION_DIRECTORIO,
      CODIGO_APP,
      CODIGO_PATH,
      APLICACION,
      PAIS,
      EMPRESA,
      MODULO,
      SUBMODULO,
      ESTADO,
      FE_CREACION,
      USR_CREACION)
    VALUES
    (
      DB_GENERAL.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
      4,
      (SELECT MAX(CODIGO_PATH) +1 FROM DB_GENERAL.ADMI_GESTION_DIRECTORIOS WHERE CODIGO_APP=4 AND APLICACION='TelcosWeb'),
      'TelcosWeb',
      '593',
      'TN',
      'Financiero',
      'Pagos',
      'Activo',
      sysdate,
      'eholguin'
    );

COMMIT;
/


