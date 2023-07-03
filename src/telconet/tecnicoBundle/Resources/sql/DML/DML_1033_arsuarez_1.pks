grant SELECT on "DB_GENERAL"."ADMI_TIPO_DOCUMENTO_GENERAL" to "DB_INFRAESTRUCTURA" ;

INSERT
INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (SELECT ID_PRODUCTO
    FROM DB_COMERCIAL.ADMI_PRODUCTO
    WHERE DESCRIPCION_PRODUCTO = 'CLOUD IAAS PUBLIC'
    AND EMPRESA_COD            = 10
    AND ESTADO                 = 'Activo'
    ),
    (SELECT ID_CARACTERISTICA
    FROM DB_COMERCIAL.ADMI_CARACTERISTICA
    WHERE DESCRIPCION_CARACTERISTICA = 'FACTURACION POR CONSUMO'
    ),
    SYSDATE,
    NULL,
    'arsuarez',
    NULL,
    'Activo',
    'NO'
  ); 

-- TIPO DE SOLICITUD

INSERT
INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD APROBACION CLOUDFORM',
    SYSDATE,
    'arsuarez',
    SYSDATE,
    'arsuarez',
    'Activo',
    NULL,
    NULL,
    NULL
  );

--TIPO DOCUMENTO DE CONTRATO

INSERT
INTO DB_GENERAL.ADMI_TIPO_DOCUMENTO_GENERAL VALUES
  (
    DB_GENERAL.SEQ_ADMI_TIPO_DOCUMENT_GENERAL.NEXTVAL,
    'CLOUD',
    'CONTRATO CLOUDFORM',
    'Activo',
    'arsuarez',
    '127.0.0.1',
    sysdate,
    NULL,
    NULL,
    'S',
    'N',
    'S'
  );

-- tareas por departamento para generación automática

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CLOUDFORM TAREAS POR DEPARTAMENTO',
    'CLOUDFORM TAREAS POR DEPARTAMENTO',
    'SOPORTE',
    NULL,
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
  );
  
--TAREA DE AUTORIZACION DE CONTRATO

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CLOUDFORM TAREAS POR DEPARTAMENTO'),
    'AUTORIZACION DE SOLICITUD DE CONTRATO CLOUDFORM',
    'GUAYAQUIL',
    'cobranzas_gye@telconet.ec',
    'Aprobacion de Contrato',
    'COBRANZAS',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'TAREACERT',
    10,
    NULL,
    NULL,
    NULL
  );


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CLOUDFORM TAREAS POR DEPARTAMENTO'),
    'AUTORIZACION DE SOLICITUD DE CONTRATO CLOUDFORM',
    'QUITO',
    'cobranzas_uio@telconet.ec',
    'Aprobacion de Contrato',
    'COBRANZAS',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'TAREACERT',
    10,
    NULL,
    NULL,
    NULL
  );
 
--TAREA DE ENVIO DE OBSERVACION A TI POR FUNCIONAMIENTO ERRÓNEO DE WS CLOUDFORM

INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CLOUDFORM TAREAS POR DEPARTAMENTO'),
    'REVISION FUNCIONAMIENTO CLOUDFORMS',
    'GUAYAQUIL',
    'datacenter_gyeit@telconet.ec',
    'DATA CENTER - TI : Revisión funcionamiento plataforma Cloudforms',
    'Data Center Ti',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'TAREACERT',
    10,
    NULL,
    NULL,
    NULL
  );


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_DET VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CLOUDFORM TAREAS POR DEPARTAMENTO'),
    'REVISION FUNCIONAMIENTO CLOUDFORMS',
    'QUITO',
    'datacenter_uioit@telconet.ec',
    'DATA CENTER - TI : Revisión funcionamiento plataforma Cloudforms',
    'Data Center Ti',
    'Activo',
    'arsuarez',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'TAREACERT',
    10,
    NULL,
    NULL,
    NULL
  );

--motivo de rechaza

INSERT
INTO DB_GENERAL.ADMI_MOTIVO VALUES
  (
    DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
    9751,
    'Información Incorrecta',
    'Activo',
    'arsuarez',
    sysdate,
    'arsuarez',
    sysdate,
    NULL,
    NULL
  );

-- caracteristicas consumo

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'LICENSE_COST',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'FINANCIERO'
  );

INSERT
INTO DB_COMERCIAL.ADMI_CARACTERISTICA VALUES
  (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TOTAL_COST',
    'N',
    'Activo',
    sysdate,
    'arsuarez',
    NULL,
    NULL,
    'FINANCIERO'
  );

--Tareas automaticas

INSERT
INTO DB_SOPORTE.ADMI_TAREA VALUES
  (
    DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,
    (SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO DATA CENTER' AND ESTADO = 'Activo'),
    NULL,
    NULL,
    NULL,
    '1',
    '0',
    'DATA CENTER - TI : Revisión funcionamiento plataforma Cloudforms',
    'Revisión de inconveniente en ejecuciones de WebServices en el Orquestador',
    '1',
    'MINUTOS',
    '1',
    '1',
    'Activo',
    'arsuarez',
    SYSDATE,
    'arsuarez',
    SYSDATE,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
  );

/