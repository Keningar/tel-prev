--=======================================================================
-- Ingreso los detalles de parámetros para los datos del ws de networking para el control del BW de la interface
-- Ingreso los detalles de parámetros para el rango del intervalo de porcentaje para control del BW de la interface
--=======================================================================

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE'
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
        'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE',
        'Datos del ws de networking para el control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        'http://sites.telconet.ec/ws/telcos/consultas/getInterfaceBw',
        'prod',
        'consultar',
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
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE'
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
        'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE',
        'Rango del intervalo de porcentaje para control del BW de la interface',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'DATOS_WS_NETWORKING_CONTROL_BW_INTERFACE'
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
            WHERE NOMBRE_PARAMETRO = 'RANGO_INTERVALO_PORCENTAJE_CONTROL_BW_INTERFACE'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '2',
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
