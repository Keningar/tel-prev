/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear detalle de parametros para bus de pagos de TN
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 
 * @since 14-02-2022 - Versión Inicial.
 */

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_CLIENTE_CONSULTA_PL'),
    'ESTADO_CLIENTE_CONSULTA_SALDOS_PL',
    'Activo',
    'SI',
    NULL,
    NULL,
    'Activo',
    'ddelacruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'TN',
    '10',
    NULL,
    NULL,
    'Estado permitido que tiene el cliente para consultar saldos en Bus de Pagos'
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'ESTADOS_CLIENTE_CONSULTA_PL'),
    'ESTADO_CLIENTE_CONSULTA_SALDOS_PL',
    'Cancelado',
    'SI',
    NULL,
    NULL,
    'Activo',
    'ddelacruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'TN',
    '10',
    NULL,
    NULL,
    'Estado permitido que tiene el cliente para consultar saldos en Bus de Pagos'
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
      WHERE NOMBRE_PARAMETRO = 'AUTOMATIZACION PAGOS'
      AND MODULO             = 'FINANCIERO'
      AND ESTADO             = 'Activo'
    ),
    'FORMA PAGO',
    'PAGO EN LINEA',
    '17',
    'PAL',
    NULL,
    'Activo',
    'ddelacruz',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '10',
    NULL,
    NULL,
    'CONFIGURA  FORMA PAGO DEPOSITO PARA OPCION DE AUTOMATIZACION DE DEPOSITOS Y TRANSFERENCIAS'
  );

  /**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear cabecera y detalle de parametro que devuelve el correo de el departamento de cobranzas segun oficina
 * @author Brenyx Giraldo <agiraldo@telconet.ec>
 * @version 1.0 22-04-2022 - Versión Inicial.
 */
Insert into DB_GENERAL.ADMI_PARAMETRO_CAB
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'CORREO_POR_DEPARTAMENTO',
    'RETORNA LOS CORREO SEGUN EL ID DEL DEPARTAMENTO',
    'FINANCIERO',
    'PAGOS',
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Guayaquil',
    'COBRANZAS',
    'cobranzas_gye@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Cuenca',
    'COBRANZAS',
    'cobranzas_cue@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Loja',
    'COBRANZAS',
    'cobranzas_lja@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Manta',
    'COBRANZAS',
    'cobranzas_mnt@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Salinas',
    'COBRANZAS',
    'cobranzas_sl@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);


Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - MILAGRO',
    'COBRANZAS',
    'cobranzas_gye@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);


Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Quito',
    'COBRANZAS',
    'cobranzas_uio@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);


Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
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
values         
(
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CORREO_POR_DEPARTAMENTO'),
    'RETORNA EL CORREO DEL DEPARTAMENTO DE COBRANZAS',
    'TELCONET - Quevedo',
    'COBRANZAS',
    'cobranzas_qvdo@telconet.ec',
    null,
    'Activo',
    'agiraldo',
    SYSDATE,
    '127.0.0.1',
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);

COMMIT;

/
