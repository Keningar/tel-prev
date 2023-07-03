-- INGRESO DE LA CABECERA DE PARAMETROS DE 'VISUALIZAR_PANTALLA_FIBRA'
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
        'VISUALIZAR_PANTALLA_FIBRA',
        'Lista de los productos que deben visualizar en la pantalla de Fibra',
        'TECNICO',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'VISUALIZAR_PANTALLA_FIBRA'
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
            WHERE NOMBRE_PARAMETRO = 'VISUALIZAR_PANTALLA_FIBRA'
            AND ESTADO = 'Activo'
        ),
        'Productos que deben visualizar la pantalla de fibra',
        'Cableado Estructurado',
        'Activo',
        'afayala',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);

COMMIT;
