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
    'FECHAS_NOTIFICACION_CAMBIOFORMAPAGO',
    'PARAMETRO PARA CONFIGURAR LOS RANGOS DE FECHAS A SER CONSIDERADOS EN CONSULTA DE CAMBIOS DE FORMA DE PAGO',
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
      WHERE NOMBRE_PARAMETRO = 'FECHAS_NOTIFICACION_CAMBIOFORMAPAGO'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'Permite especificar cuanto tiempo se le quitara al sysdate para considerarlo como fecha desde',
    'VALIDACION_FECHA_DESDE',
    'S',
    '24',
    'hour',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
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
      WHERE NOMBRE_PARAMETRO = 'FECHAS_NOTIFICACION_CAMBIOFORMAPAGO'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'Permite especificar cuanto tiempo se le adicionará al sysdate para considerarlo como fecha hasta',
    'VALIDACION_FECHA_HASTA',
    'S',
    '0',
    'hour',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    NULL,
    NULL,
    NULL
  );



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
    'NOTIFICACION_CAMBIO_FORMA_PAGO',
    'PARAMETRO PARA CONFIGURAR LAS EMPRESAS QUE SE QUIERE NOTIFICAR.',
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
      WHERE NOMBRE_PARAMETRO = 'NOTIFICACION_CAMBIO_FORMA_PAGO'
      AND ESTADO             = 'Activo'
    ),
    'Configura el prefijo de la empresa  que se quiere notificar.',
    'NOTIFICACION_CAMBIO_FORMA_PAGO',
    'MD',
    'S',
    'telcos',
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.0',
    'eholguin',
    NULL,
    NULL,
    'El punto ha sido notificado al usuario como afectado con camio de forma de pago. ',
    '18',
    NULL,
    NULL,
    NULL
  );



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
    'CAMB_FORMPAG_HEADERS',
    'Permite especificar las cabeceras de la notificaciones para cambios de forma de pago de MD ',
    'FINANCIERO',
    'NOTIFICACIONES',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
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
      WHERE NOMBRE_PARAMETRO = 'CAMB_FORMPAG_HEADERS'
      AND ESTADO             = 'Activo'
    ),
    'Permite especifiar el emisor, receptor(es) y Asunto de la notificación de cambio de forma de pago. ',    
    'CAMB_FORMPAG_HEADERS',
    'notificaciones_telcos@telconet.ec',
    'Cambio de forma de pago',
    'eholguin@telconet.ec',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'NULL',
    '18',
    NULL,
    NULL,
    NULL
  );

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
    'CAMBIO FORMA PAGO',
    'Define los parámetros necesarios para la facturación por cambio de forma de pago.',
    'FINANCIERO',
    'FACTURACION',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
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
      WHERE NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND ESTADO             = 'Activo'
    ),
    'FECHA ACTIVACION ORIGEN',
    'TRASLADAR SERVICIO',
    'feOrigenServicioTrasladado',
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'Strings de consulta para realizar búsqueda de fecha de activación de orígen de un servicio trasladado.'
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
      WHERE NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND ESTADO             = 'Activo'
    ),
    'FECHA ACTIVACION ORIGEN',
    'CAMBIO RAZON SOCIAL',
    'feOrigenCambioRazonSocial',
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'Strings de consulta para realizar búsqueda de fecha de activación de orígen de un servicio al aplicar cambio de razón soacial.'
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
      WHERE NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND ESTADO             = 'Activo'
    ),
    'FECHA ACTIVACION ORIGEN',
    'CONFIRMAR SERVICIO',
    'confirmarServicio',
    'SE CONFIRMO EL SERVICIO',
    'SE ACTIVO EL SERVICIO',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'Strings de consulta para realizar búsqueda de fecha de activación de un servicio.'
  );


-- Se Inserta parámetro de permanencia mínima 36 Meses.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND    ESTADO           = 'Activo' ),
    'Tiempo en meses de permanencia mínima del servicio mandatorio Internet ',
    'PERMANENCIA MINIMA 24 MESES',
    24,
    '30/04/2019',
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

-- Se Inserta parámetro de permanencia mínima 36 Meses.
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND    ESTADO           = 'Activo' ),
    'Tiempo en meses de permanencia mínima del servicio mandatorio Internet ',
    'PERMANENCIA MINIMA 36 MESES',
    36,
    '01/05/2019',
    NULL,
    'Activo',
    'mhaz',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

-- Parámetro para fórmula de valor promocional de instalación

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    ( SELECT ID_PARAMETRO
      FROM   DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE  NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND    ESTADO           = 'Activo' ),
    'Fórmula para calcular el valor promocional por instalación ',
    'FORMULA PROMOCIONAL INSTALACION',
    'ROUND(NVL(Ln_ValorInstalacion * Ln_DctoInstalacion/Ln_TiempoPermanencia * (Ln_TiempoPermanencia - (Ln_MesCambioFormaPago)),0),2)',
    'NULL',
    'NULL',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );


-- Parámetros para generación de tarea.
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
      WHERE NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND ESTADO             = 'Activo'
    ),
    'DESCRIPCION TAREA',
    'Titular se acerca a matriz a realizar cambio de su forma de pago a ',
    ', se firma acta No ',
    ' , se adjuntan documentos digitalizados.',
    ' Se emite la respectiva factura.',
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'Se finaliza por cambio de forma de pago',
    '18',
    'Tarea fue Finalizada Obs : Tarea por cambio de forma de pago',
    NULL,
    'Tarea por cambio de forma de pago.'
  );
-- Parámetros para envío de sms

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
      WHERE NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND ESTADO             = 'Activo'
    ),
    'NOTIFICACION SMS',
    'SI',
    'Netlife le informa el cambio de su forma de pago y su factura fue enviada a su correo electronico.',
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'V1 campo que indica si se facturó, V2 texto para envío de sms cuando el cambio de forma de pago genera factura.'
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
      WHERE NOMBRE_PARAMETRO = 'CAMBIO FORMA PAGO'
      AND ESTADO             = 'Activo'
    ),
    'NOTIFICACION SMS',
    'NO',
    'Netlife informa la ejecución a su solicitud de cambio de forma de pago.',
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    'eholguin',
    SYSDATE,
    '172.17.0.1',
    NULL,
    '18',
    NULL,
    NULL,
    'V1 campo que indica si se facturó, V2 texto para envío de sms cuando el cambio de forma de pago no genera factura.'
  );

COMMIT;
/
