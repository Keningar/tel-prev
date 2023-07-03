-- Nueva marca de Tecnologias Permitidas para GPON, ZTE modelo C650
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (
        SELECT
            id_parametro
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'Lista de marcas de las tecnologías permitidas para la red GPON',
    'MARCA_TECNOLOGIA_PERMITIDA_GPON',
    'ZTE',
    'C650',
    'Activo',
    'jmujca',
    SYSDATE,
    '127.0.0.0',
    '10'
);
--Modelo Ont Zte permitido
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (
        SELECT
            id_parametro
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'Modelo Ont Zte permitido para tecnologías permitidas para la red GPON',
    'MODELO_ONT_ZTE',
    'ZXHN F670L V9.0',
    'Activo',
    'jmujca',
    SYSDATE,
    '127.0.0.0',
    '10'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (
        SELECT
            id_parametro
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'Modelo Ont Zte permitido para tecnologías permitidas para la red GPON',
    'MODELO_ONT_ZTE',
    'ZXHN F670L V1.1',
    'Activo',
    'jmujca',
    SYSDATE,
    '127.0.0.0',
    '10'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.nextval,
    (
        SELECT
            id_parametro
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'Modelo Ont Zte permitido para tecnologías permitidas para la red GPON',
    'MODELO_ONT_ZTE',
    'EG8M8145V5G06',
    'Activo',
    'jmujca',
    SYSDATE,
    '127.0.0.0',
    '10'
);
-- INGRESO DE LA CARACTERISTICA PARA LA T-CONT
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'T-CONT-ADMIN',
        'N',
        'Activo',
        SYSDATE,
        'jmujica',
        NULL,
        NULL,
        'TECNICA'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            nombre_tecnico = 'SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-ADMIN'
    ),
    SYSDATE,
    NULL,
    'jmujica',
    NULL,
    'Activo',
    'NO'
);
-- INGRESO DE LA CARACTERISTICA PARA LA GEM-PORT-ADMIN
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'GEM-PORT-ADMIN',
        'N',
        'Activo',
        SYSDATE,
        'jmujica',
        NULL,
        NULL,
        'TECNICA'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            nombre_tecnico = 'SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-ADMIN'
    ),
    SYSDATE,
    NULL,
    'jmujica',
    NULL,
    'Activo',
    'NO'
);
-- INGRESO DE LA CARACTERISTICA PARA EL TRAFFIC-TABLE-ADMIN
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'TRAFFIC-TABLE-ADMIN',
        'N',
        'Activo',
        SYSDATE,
        'jmujica',
        NULL,
        NULL,
        'TECNICA'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            nombre_tecnico = 'SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-ADMIN'
    ),
    SYSDATE,
    NULL,
    'jmujica',
    NULL,
    'Activo',
    'NO'
);
COMMIT;
/