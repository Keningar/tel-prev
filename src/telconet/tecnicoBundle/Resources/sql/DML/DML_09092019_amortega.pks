SET DEFINE OFF;

--=======================================================================
--      Se crea cabecera de parámetro para mantenimiento preventivo de Torres 
--=======================================================================
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
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
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'MANTENIMIENTO TORRES',
    'PARAMETROS PARA EL MANTENIMIENTO PREVENTIVO DE TORRES TN',
    'TECNICO',
    'RADIO',
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de clico de mantenimiento 
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'CICLO MANTENIMIENTO',
    '12',
    NULL,
    NULL,
    NULL,
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de clico de mantenimiento 
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'CICLO MANTENIMIENTO',
    '24',
    NULL,
    NULL,
    NULL,
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de los parametros para enviar notificaciones
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'PARAMETROS NOTIFICACIONES',
    'notificaciones_telcos@telconet.ec',
    'avelasco@telconet.ec',
    'MAIL',
    'TN',
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Mantenimiento Preventivo de Torres',
    NULL,
    NULL,
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de los parametros para crear tareas
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'PARAMETRO PARA CREAR TAREA',
    'Mantenimiento de Torre en mal estado',
    'Se requiere un registro de mantenimiento de las torres.',
    'TAREAS DE RADIOENLACE - TORRE  NODO',
    'Mantenimiento Preventivo para la Torre ubicada en el Nodo: ',
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'Mantenimiento programado',
    NULL,
    'empleado',
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de parametros para notificacion segun Region 1
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'PARAMETRO PARA ASIGNAR TAREA R1',
    'Coordinador',
    'empleado',
    'ctapia',
    'ctapia@telconet.ec',
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de parametros para notificacion segun Region 2
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'PARAMETRO PARA ASIGNAR TAREA R2',
    'Jefe Departamental',
    'empleado',
    'dvalle',
    'dvalle@telconet.ec',
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--=======================================================================
--      Se crea detalle de parametros de las rutas para acceder al WS para crear tareas
--=======================================================================
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
    EMPRESA_COD,
    VALOR6,
    VALOR7,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT 
            ID_PARAMETRO
        FROM
            ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'MANTENIMIENTO TORRES'
    ),
    'URL ECUCERT PARA CREAR TAREAS',
    'http://telcos.telconet.ec/rs/tecnico/ws/rest/procesar',
    'http://telcos.telconet.ec/rs/soporte/ws/rest/procesar',
    NULL,
    NULL,
    'Activo',
    'amortega',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

COMMIT;
/
