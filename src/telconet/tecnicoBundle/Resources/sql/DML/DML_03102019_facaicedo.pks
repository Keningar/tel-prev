--=======================================================================
-- Ingreso de parametros para la validación de actualización de estados de 
-- los servicios de interfaces de los elementos SWITCH o STACKS
-- Ingreso de parametros de estados de interfaces permitidas de elementos SWITCH
-- Ingreso de parametros de cambio de ultima milla por proceso masivo
-- Ingreso de las caracteristicas para los Id de las interfaces anterior y nueva de los detalles de solicitud
-- Ingreso de parametros de estados de servicios permitidos para cambio ultima milla
-- Ingreso de parametros para los correos que se les enviarán los errores en el cambio de última milla masivo
--=======================================================================

-- INGRESO LAS CABECERAS DE PARAMETROS PARA SERVICIOS ACTUALIZACION
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
)
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION', 
        'Estados que no deben tener los servicios para ser actualizados por WS', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LOS ESTADOS DE LAS CABECERAS DE SERVICIOS ACTUALIZACION
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Activo', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Factible', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'In-Corte', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'EnPruebas', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Asignada', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'AsignadoTarea', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Preplanificado', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Planificada', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Replanificada', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'PreAsignacionInfoTecnica', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Pendiente', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_NO_PERMITIDOS_ACTUALIZACION' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Detenido', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        )
);

-- INGRESO LA CABECERA DE PARAMETROS DE ESTADOS PERMITIDOS DE INTERFACES DE ELEMENTOS SWITCH
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
)
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'ESTADOS_INTERFACES_PERMITIDAS', 
        'Estados que deben tener las interfaces de los elementos SWITCH', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LOS ESTADOS PERMITIDOS DE LA CABECERA DE ESTADOS_INTERFACES_PERMITIDAS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        VALOR3, 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_PERMITIDAS' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'connected', 
        'CAMBIO_UM', 
        'ESTADOS_INTERFACES', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        VALOR3, 
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_INTERFACES_PERMITIDAS' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'not connect', 
        'ACTUALIZAR_INTERFACES', 
        'ESTADOS_INTERFACES', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);

-- INGRESO LA CABECERA DE PARAMETROS DE CAMBIO DE ULTIMA MILLA MASIVO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
)
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'CAMBIO_ULTIMA_MILLA_MASIVO', 
        'Se ejecutarán los procesos masivos de cambio de ultima milla', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO EL DETALLE DE 'CambioUltimaMilla' y 'MinimoCambioUltimaMilla' DE LA CABECERA DE CAMBIO_ULTIMA_MILLA_MASIVO
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        VALOR3, 
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
            WHERE NOMBRE_PARAMETRO = 'CAMBIO_ULTIMA_MILLA_MASIVO' 
            AND ESTADO = 'Activo'
        ), 
        'Se ejecutarán los procesos masivos de cambio de ultima milla', 
        'CambioUltimaMilla', 
        'CambioUltimaMilla', 
        'TN', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET 
( 
        ID_PARAMETRO_DET, 
        PARAMETRO_ID, 
        DESCRIPCION, 
        VALOR1, 
        VALOR2, 
        VALOR3, 
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
            WHERE NOMBRE_PARAMETRO = 'CAMBIO_ULTIMA_MILLA_MASIVO' 
            AND ESTADO = 'Activo'
        ), 
        'Cantidad mínima de interfaces permitidas para Cambio Ultima Milla Masivo', 
        'MinimoCambioUltimaMilla', 
        '1', 
        'TN', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);

-- INGRESO DE LAS CARACTERISTICAS PARA LAS INTERFACES ANTERIOR Y NUEVA PARA LOS DETALLES DE SOLICITUD
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
        'INTERFACE_ELEMENTO_ANTERIOR_ID', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
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
        'INTERFACE_ELEMENTO_NUEVA_ID', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);
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
        'INTERFACE_ELEMENTO_NUEVA_NOMBRE', 
        'N', 
        'TECNICA', 
        'Activo', 
        'facaicedo', 
        SYSDATE
);

-- INGRESO LA CABECERA DE PARAMETROS DE ESTADOS PERMITIDOS DE LOS SERVICIOS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB 
( 
        ID_PARAMETRO, 
        NOMBRE_PARAMETRO, 
        DESCRIPCION, 
        ESTADO, 
        USR_CREACION, 
        FE_CREACION, 
        IP_CREACION 
)
VALUES
( 
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL, 
        'ESTADOS_SERVICIOS_PERMITIDOS', 
        'Estados permitidos que deben tener los servicios para cambio de ultima milla', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1' 
);
-- INGRESO LOS DETALLES DE LOS ESTADOS DE LA CABECERA ESTADOS_SERVICIOS_PERMITIDOS
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
            WHERE NOMBRE_PARAMETRO = 'ESTADOS_SERVICIOS_PERMITIDOS' 
            AND ESTADO = 'Activo'
        ), 
        'LISTA VALORES', 
        'Activo', 
        'Activo', 
        'facaicedo', 
        SYSDATE, 
        '127.0.0.1', 
        ( 
            SELECT COD_EMPRESA 
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO 
            WHERE PREFIJO = 'TN' 
        ) 
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
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
        'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO',
        'Correos que se les enviarán los errores en el cambio de última milla masivo',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'networking@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'noc@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'ipcc_l2@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'sistemas-soporte@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'facaicedo@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'amedina@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_RESPUESTA_CAMBIO_UM_MASIVO'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'fbermeo@telconet.ec',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
COMMIT;
/
