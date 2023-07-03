/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 11-12-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) relacionados a los estados permitidos para limpiar cache en toolbox
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
        'ESTADOS_PERMITIDOS_CLEAR_CACHE',
        'ESTADOS PERMITIDOS PARA REALIZAR EL CONSUMO EN TOOLBOX (FOX PREMIUM) V1= :NEW.ESTADO V2 = :OLD.ESTADO',
        'COMERCIAL',
        'FOX_PREMIUM',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--Telcos
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_PERMITIDOS_CLEAR_CACHE' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FOX_PREMIUM'
           AND ESTADO = 'Activo'
        ),
        'Estado permitido por ejecución manual desde el telcos.',
        'Telcos1', --Estado Anterior
        'Telcos2', --Estado Nuevo
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

--Corte del servicio
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_PERMITIDOS_CLEAR_CACHE' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FOX_PREMIUM'
           AND ESTADO = 'Activo'
        ),
        'Estado permitido por ejecución desde el trigger por corte del servicio.',
        'Activo', --Estado Anterior
        'In-Corte', --Estado Nuevo
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

--Cancelación del servicio
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_PERMITIDOS_CLEAR_CACHE' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FOX_PREMIUM'
           AND ESTADO = 'Activo'
        ),
        'Estado permitido por ejecución desde el trigger por cancelación del servicio.',
        'Activo', --Estado Anterior
        'Cancel', --Estado Nuevo
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

--Reactivación del servicio
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
         WHERE NOMBRE_PARAMETRO = 'ESTADOS_PERMITIDOS_CLEAR_CACHE' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FOX_PREMIUM'
           AND ESTADO = 'Activo'
        ),
        'Estado permitido por ejecución desde el trigger por reactivación del servicio.',
        'In-Corte', --Estado Anterior
        'Activo', --Estado Nuevo
        NULL,
        NULL,
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL,
        '18',
        NULL
    );

COMMIT;

/**
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 * @since 11-12-2018
 * Se crean las sentencias DML para insertar parámetros (CAB y DET) Configuración webService ClearCache (FOX)
 */
  INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
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
        'CONFIGURACION_WS_CLEAR_CACHE',
        'Valores para la configuración del WS a consumir en FOX',
        'COMERCIAL',
        'FOX_PREMIUM',
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL
    );


--Ambiente de CERT
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_CLEAR_CACHE' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FOX_PREMIUM'
           AND ESTADO = 'Activo'
        ),
        'Configuración Ambiente de CERT',
        'https://idp-cache-cert.tbxapis.com/v1/cache/clear/EC/{subscriber_id}', --URL VALOR1
        'IUlBmY5m4mIJQWUth3AYrHOQhnoBCHoA', --HEADER AUTORIZATION VALOR2
        'DELETE', --Método VALOR3
        'fp.status', --Validador de estado VALOR4
        'fp.data=SUCCESS', --Validador de mensaje VALOR5
        'fp.error.errorDetails.errorCode|fp.error.errorDetails.errorMessage|cloudpass.error.error.error.errorCode|cloudpass.error.error.error.message', --Mensajes de error.VALOR6
        NULL, --ORACLE_WALLET Y CONTRASEÑA VALOR7
        'Inactivo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '18'
    );

--Ambiente de producción
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        VALOR6,
        VALOR7,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'CONFIGURACION_WS_CLEAR_CACHE' 
           AND MODULO = 'COMERCIAL'
           AND PROCESO = 'FOX_PREMIUM'
           AND ESTADO = 'Activo'
        ),
        'Configuración Ambiente de PRODUCCIÓN',
        'https://idp-cache.tbxapis.com/v1/cache/clear/EC/{subscriber_id}', --URL VALOR1
        'LPtfY59xLCWQTf25Ha5u44x8QaRc141g', --HEADER AUTORIZATION VALOR2
        'DELETE', --Método VALOR3
        'fp.status', --Validador de estado VALOR4
        'fp.data=SUCCESS', --Validador de mensaje VALOR5
        'fp.error.errorDetails.errorCode|fp.error.errorDetails.errorMessage|cloudpass.error.error.error.errorCode|cloudpass.error.error.error.message', --Mensajes de error.VALOR6
        NULL, --ORACLE_WALLET Y CONTRASEÑA VALOR7
        'Activo',
        'lcabrera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        '18'
    );

COMMIT;