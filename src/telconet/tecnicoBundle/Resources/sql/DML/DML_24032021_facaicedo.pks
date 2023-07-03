--=======================================================================
-- Ingreso del tipo de solicitud para olt multiplataforma
-- Ingreso de la característica para los id del elemento del olt y nodo para el olt multiplataforma
-- Ingreso de la característica para el nombre del PE
-- Ingreso los detalles de parámetros de los datos para la creación de de las tareas inter-departamentales del Olt Multiplataforma
-- Ingreso los detalles de parámetros de los id de jurisdicción para los Olt Multiplataforma
-- Ingreso los detalles de elemento para los Nodos Multiplataforma
--=======================================================================

-- INGRESO DEL TIPO SOLICITUD 'SOLICITUD OLT MULTIPLATAFORMA'
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
(
        ID_TIPO_SOLICITUD,
        DESCRIPCION_SOLICITUD,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
        'SOLICITUD OLT MULTIPLATAFORMA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CARACTERISTICA PARA EL ELEMENTO ID DEL OLT
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ELEMENTO_OLT_ID',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CARACTERISTICA PARA EL ELEMENTO ID DEL NODO
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'ELEMENTO_NODO_ID',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CARACTERISTICA PARA EL NOMBRE DEL PE
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'NOMBRE PE',
        'T',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA',
        'Lista de los datos para la creación de las tareas inter-departamentales del Olt Multiplataforma.',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA'
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'AGREGAR',
        'NETWORKING',
        'Elaboración de SMOPs, logística y simulación',
        'TN',
        '10',
        '419164',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL
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
        VALOR5,
        VALOR6,
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_TAREAS_OLT_MULTIPLATAFORMA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'ASIGNAR',
        'Gepon/Tap',
        'ASIGNAR RECURSOS OLT MULTIPLATAFORMA',
        'MD',
        '18',
        '291352',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA',
        'Lista de los id de jurisdicción para los Olt Multiplataforma.',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '257',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '258',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '54',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'CIUDADES_DISPONIBLES_OLT_MULTIPLATAFORMA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '55',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL
);
-- INGRESO LOS DETALLES DEL ELEMENTO PARA NODOS GYE PERMITE_MULTIPLATAFORMA
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35015,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35056,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35434,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        141449,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        250522,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35019,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
-- INGRESO LOS DETALLES DEL ELEMENTO PARA NODOS UIO PERMITE_MULTIPLATAFORMA
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35677,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35314,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35399,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35418,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35427,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        987299,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        35597,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
INSERT INTO DB_INFRAESTRUCTURA.INFO_DETALLE_ELEMENTO
(
        ID_DETALLE_ELEMENTO,
        ELEMENTO_ID,
        DETALLE_NOMBRE,
        DETALLE_VALOR,
        DETALLE_DESCRIPCION,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        REF_DETALLE_ELEMENTO_ID,
        ESTADO
)
VALUES
(
        DB_INFRAESTRUCTURA.SEQ_INFO_DETALLE_ELEMENTO.NEXTVAL,
        885242,
        'PERMITE_MULTIPLATAFORMA',
        'SI',
        'PERMITE_MULTIPLATAFORMA',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        NULL,
        'Activo'
);
COMMIT;
/
