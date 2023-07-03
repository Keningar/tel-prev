/*
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.0
 * @since 01-03-2021
 * Se crean las sentencias DML para insertar parámetros  relacionados con la facturación de solicitudes Netlifecam.
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'PromoNetlifecam',
    NULL,
    (SELECT IPC.ID_PLAN
      FROM  DB_COMERCIAL.INFO_PLAN_CAB IPC
      JOIN  DB_COMERCIAL.INFO_EMPRESA_GRUPO IEG ON IEG.COD_EMPRESA = IPC.EMPRESA_COD
      WHERE IPC.NOMBRE_PLAN  = 'PROMO SUSCRIPCIONX3M'
      AND   IPC.EMPRESA_COD  = '18'
      AND   IPC.ESTADO       = 'Activo'),
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'DESCUENTO NETLIFECAM'
      AND ESTADO             = 'Activo'
      AND USR_CREACION       = 'eholguin'
    ),
    NULL,
    'Activo',
    'telcos_cancel_volun',
    SYSDATE,
    '127.0.0.1',
    'telcos_cancel_volun',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    'N'
  );   


INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CAMARA EZVIZ CS-C1C-D0-1D1WFR', --NOMBRE DEL EQUIPO
        '', --TECNOLOGÍA
        '60', --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C1C-D0-1D1WFR'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'VISEG',
        'Activo',
        'eholguin',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18',
        '60'
    );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'CAMARA EZVIZ CS-CV206 (MINI-O)', --NOMBRE DEL EQUIPO
        '', --TECNOLOGÍA
        60, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-CV206 (MINI-O)'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'VISEG',
        'Activo',
        'eholguin',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18',
        '60'
    );

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
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
        VALOR6
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'RETIRO_EQUIPOS_SOPORTE'
           AND MODULO = 'FINANCIERO'
           AND PROCESO = 'FACTURACION_RETIRO_EQUIPOS'
           AND ESTADO = 'Activo'
        ),
        'TARJETA MICRO SD 32 GB KINGSTON', --NOMBRE DEL EQUIPO
        '', --TECNOLOGÍA
        20, --PRECIO
        (SELECT ID_CARACTERISTICA
           FROM DB_COMERCIAL.ADMI_CARACTERISTICA
          WHERE DESCRIPCION_CARACTERISTICA = 'TARJETA MICRO SD 32 GB KINGSTON'
            AND ESTADO = 'Activo'
            AND TIPO = 'COMERCIAL'
        ), --CARACTERISTICA_ID
        'VISEG',
        'Activo',
        'eholguin',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S', --Si se presenta en la plantilla de equipos.
        '18',
        '20'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'TARJETA MICRO SD',
    (SELECT PROD.ID_PRODUCTO
      FROM  DB_COMERCIAL.ADMI_PRODUCTO PROD
      WHERE PROD.DESCRIPCION_PRODUCTO  = 'TARJETA MICRO SD'
      AND   PROD.EMPRESA_COD  = '18'
      AND   PROD.ESTADO       = 'Inactivo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'TARJETA MICRO SD 32 GB KINGSTON'
      AND ESTADO             = 'Activo'
      AND TIPO = 'COMERCIAL'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'CAMARA EZVIZ CS-C1C-D0-1D1WFR',
    (SELECT PROD.ID_PRODUCTO
      FROM  DB_COMERCIAL.ADMI_PRODUCTO PROD
      WHERE PROD.DESCRIPCION_PRODUCTO  = 'CAMARA EZVIZ CS-C1C-D0-1D1WFR'
      AND   PROD.EMPRESA_COD  = '18'
      AND   PROD.ESTADO       = 'Inactivo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-C1C-D0-1D1WFR'
      AND ESTADO             = 'Activo'
      AND TIPO = 'COMERCIAL'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    'N'
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
    VALOR6
  )
  VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
      SELECT ID_PARAMETRO
      FROM DB_GENERAL.ADMI_PARAMETRO_CAB
      WHERE NOMBRE_PARAMETRO = 'FACTURACION SOLICITUD DETALLADA'
      AND ESTADO             = 'Activo'
    ),
    'CAMARA EZVIZ CS-CV206 (MINI-O)',
    (SELECT PROD.ID_PRODUCTO
      FROM  DB_COMERCIAL.ADMI_PRODUCTO PROD
      WHERE PROD.DESCRIPCION_PRODUCTO  = 'CAMARA EZVIZ CS-CV206 (MINI-O)' 
      AND   PROD.EMPRESA_COD  = '18'
      AND   PROD.ESTADO       = 'Inactivo'),
    NULL,
    (
      SELECT ID_CARACTERISTICA
      FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CAMARA EZVIZ CS-CV206 (MINI-O)'
      AND ESTADO             = 'Activo'
      AND TIPO = 'COMERCIAL'
    ),
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    '18',
    'N'
  );   


-- Se Inserta parámetro de permanencia mínima 24 Meses.
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
      WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND    ESTADO           = 'Activo' ),
    'Tiempo en meses de permanencia mínima del servicio Netlifecam ',
    'PERMANENCIA MINIMA NETLIFECAM',
    24,
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

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
      WHERE  NOMBRE_PARAMETRO = 'CANCELACION VOLUNTARIA'
      AND    ESTADO           = 'Activo' ),
    'Mensaje de alerta para selección de equipos a facturar por cancelación del servicio Netlifecam ',
    'MENSAJE NETLIFECAM',
    'El cliente tiene contratado NetlifeCam como producto adicional. Revisar entrega de equipos. ',
    NULL,
    NULL,
    'Activo',
    'eholguin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
  );

COMMIT;
/