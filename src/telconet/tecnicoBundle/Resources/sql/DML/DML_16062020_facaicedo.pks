--=======================================================================
-- Ingreso el tipo de solicitud para el control del BW de la interface
-- Ingreso de la caracteristica para el tipo de proceso en la ejecución del masivo
-- Ingreso de la caracteristica para el id del historial del elemento
-- Ingreso de la caracteristica para el nombre de la ciudad
-- Ingreso los detalles de parámetros para los id de los tipos de elementos para el control del BW de la interface
-- Ingreso los detalles de parámetros para los id de los clientes para el control del BW de la interface
-- Ingreso los detalles de parámetros para los rangos de capacidad de la interface para el control del BW
-- Ingreso los detalles de parámetros para los id de los elementos para el control del BW de la interface
-- Ingreso los detalles de parámetros para los id de las interfaces para el control del BW de la interface
-- Ingreso los detalles de parámetros para los id de las regiones para el control del BW de la interface
-- Ingreso los detalles de parámetros para los id de las provincias para el control del BW de la interface
-- Ingreso los detalles de parámetros para los id de los cantones para el control del BW de la interface
-- Ingreso los detalles de parámetros para los id de las parroquias para el control del BW de la interface
-- Ingreso los detalles de parámetros para crear la tarea interna del elemento y la interface para el control del BW de la interface
-- Ingreso los detalles del departamento para la tarea interna en el parámetro WEB SERVICE TAREAS
-- Ingreso los detalles del nombre de la tarea para la tarea interna en el parámetro WEB SERVICE TAREAS
-- Ingreso los detalles de parámetros para los servicios que no deben poseer las interfaces para el control BW
-- Ingreso los detalles de parámetros para los correos de reportes de las interfaces para el control BW
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'SOLICITUD CONTROL BW MASIVO'
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
        'SOLICITUD CONTROL BW MASIVO',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL TIPO PROCESO
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
        'TIPO_PROCESO',
        'S',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL ID DEL HISTORIAL DEL ELEMENTO
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
        'HISTORIAL_ELEMENTO_ID',
        'N',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO LA CARACTERISTICA PARA EL NOMBRE DE LA CIUDAD
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
        'NOMBRE_CIUDAD',
        'S',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'TIPOS_ELEMENTOS_BW_INTERFACE'
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
        'TIPOS_ELEMENTOS_BW_INTERFACE',
        'Lista de los id de los tipos de los elementos para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'TIPOS_ELEMENTOS_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'TIPOS_ELEMENTOS_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1',
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
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CLIENTES_CONTROL_BW_INTERFACE'
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
        'CLIENTES_CONTROL_BW_INTERFACE',
        'Lista de los id de los clientes para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'RANGO_CONTROL_BW_INTERFACE'
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
        'RANGO_CONTROL_BW_INTERFACE',
        'Lista de los rangos de capacidad de la interface para el control del BW',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'RANGO_CONTROL_BW_INTERFACE'
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
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'RANGO_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'Fa',
        'Fast Ethernet',
        '0',
        '96200',
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
            WHERE NOMBRE_PARAMETRO = 'RANGO_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'Et',
        'Ethernet',
        '0',
        '96200',
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
            WHERE NOMBRE_PARAMETRO = 'RANGO_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'Gi',
        'Gigabit Ethernet',
        '0',
        '96200',
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
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'ELEMENTOS_CONTROL_BW_INTERFACE'
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
        'ELEMENTOS_CONTROL_BW_INTERFACE',
        'Lista de los id de los elementos para el control del BW',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'INTERFACE_CONTROL_BW_INTERFACE'
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
        'INTERFACE_CONTROL_BW_INTERFACE',
        'Lista de los id de las interfaces para el control del BW',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'REGIONES_CONTROL_BW_INTERFACE'
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
        'REGIONES_CONTROL_BW_INTERFACE',
        'Lista de los id de las regiones para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PROVINCIAS_CONTROL_BW_INTERFACE'
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
        'PROVINCIAS_CONTROL_BW_INTERFACE',
        'Lista de los id de las provincias para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CANTONES_CONTROL_BW_INTERFACE'
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
        'CANTONES_CONTROL_BW_INTERFACE',
        'Lista de los id de los cantones para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PARROQUIAS_CONTROL_BW_INTERFACE'
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
        'PARROQUIAS_CONTROL_BW_INTERFACE',
        'Lista de los id de las parroquias para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE'
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
        'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE',
        'Lista de los valores para crear la tarea interna del elemento y la interface para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'ELEMENTO',
        'GESTION CAMBIO DE UM',
        'IPCCL2',
        'aaaa',
        'Control BW Interface (PM)',
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
        VALOR4,
        VALOR5,
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
            WHERE NOMBRE_PARAMETRO = 'CREAR_TAREA_INTERNA_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'INTERFACE',
        'GESTION CAMBIO DE UM',
        'IPCCL2',
        'aaaa',
        'Control BW Interface (PM)',
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
-- INGRESO EL DEPARTAMENTO EN EL PARAMETRO DE TAREAS 'WEB SERVICE TAREAS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'WEB SERVICE TAREAS' AND MODULO = 'SOPORTE' AND PROCESO = 'TAREAS'
            AND ESTADO = 'Activo'
        ),
        'IPCCL2',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        ),
        'IPCCL2',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO EL NOMBRE DE LA TAREA EN EL PARAMETRO DE TAREAS 'WEB SERVICE TAREAS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'WEB SERVICE TAREAS' AND MODULO = 'SOPORTE' AND PROCESO = 'TAREAS'
            AND ESTADO = 'Activo'
        ),
        'Tarea de app de Cert',
        'GESTION CAMBIO DE UM',
        'GESTION CAMBIO DE UM',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
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
        'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE',
        'Lista de los servicios que no deben poseer las interfaces para el control BW',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
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
            WHERE NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '261',
        NULL,
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
            WHERE NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '237',
        'Concentrador L3MPLS Administracion',
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
            WHERE NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '237',
        'Concentrador L3MPLS Navegacion',
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
            WHERE NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '238',
        'Concentrador L3MPLS Administracion',
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
            WHERE NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '238',
        'Concentrador L3MPLS Navegacion',
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
            WHERE NOMBRE_PARAMETRO = 'SERVICIOS_NO_PERMITIDOS_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '242',
        'Concentrador L3MPLS Navegacion',
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
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
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
        'CORREOS_REPORTE_CONTROL_BW_INTERFACE',
        'Correos de reportes que se les enviarán a los usuarios cuando se actualicen la capacidad de la interface.',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'aaaa@telconet.ec',
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'rrubio@telconet.ec',
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA CORREOS',
        'jcastillo@telconet.ec',
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'CORREOS_REPORTE_CONTROL_BW_INTERFACE'
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
COMMIT;
/
