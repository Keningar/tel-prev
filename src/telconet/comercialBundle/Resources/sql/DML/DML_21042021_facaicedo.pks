-- INSERTAR LA ULTIMA MILLA PARA DATOS GPON VIDEO ANALYTICS CAM
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'UM FTTX',
    ( SELECT NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO
        WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
    'FTTx',
    'MD',
    '18',
    'ULTIMA_MILLA_GPON_TN',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.0',
    '10'
);
--Ingresamos la relación del producto para el tipo de red
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'GPON',
    'S',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);
--Ingresamos los productos no permitidos para el tipo de red MPLS
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR LOS PRODUCTOS NO PERMITIDOS EN TIPO RED MPLS',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'MPLS',
    'S',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    'PRODUCTO_NO_PERMITIDO_MPLS',
    10
);
--Registramos las cantidad de camaras con los que podrá elegir al momento de crear un servicio.
--Cabecera
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PROD_Cantidad Camaras',
    'PROD_Cantidad Camaras',
    'COMERCIAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '1',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '2',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '3',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '4',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
--Ingresamos la configuración del RDA para los servicios bajo red GPON
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETROS PARA WS de RDA - Activacion',
    'ACTIVAR_INTERNET',
    'TN_INTERNET_XXM',
    '1',
    '0',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETROS PARA WS de RDA - Activacion',
    'ACTIVAR_MONITOREO',
    '900',
    '10',
    '1',
    '0',
    '900',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETROS PARA WS de RDA - Activacion',
    'ACTIVAR_DATOS',
    '0',
    'ACTIVAR_TN_L3',
    'configurarOLT',
    'activar',
    'servicios',
    '0',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor6,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETROS PARA WS de RDA - Activacion',
    'CONFIG_RDA',
    'TN_ACTIVAR_INTERNET',
    'TN_ACTIVAR_DATOS',
    'CORPORATIVO',
    '2',
    '7',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

-- INGRESO DE LA CARACTERISTICA PARA LA VPN GPON
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
        'VPN_GPON',
        'T',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

--Se relaciona la característica MAC ONT con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'MAC ONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
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
        'T-CONT',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INGRESO DE LA CARACTERISTICA PARA LA ID-MAPPING
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
        'ID-MAPPING',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

--Se relaciona la característica GEM-PORT con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica TRAFFIC-TABLE con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica T-CONT con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica ID-MAPPING con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INGRESO DE LA CARACTERISTICA PARA LA GEM-PORT-MONITOREO
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
        'GEM-PORT-MONITOREO',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INGRESO DE LA CARACTERISTICA PARA LA TRAFFIC-TABLE-MONITOREO
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
        'TRAFFIC-TABLE-MONITOREO',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INGRESO DE LA CARACTERISTICA PARA LA T-CONT-MONITOREO
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
        'T-CONT-MONITOREO',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INGRESO DE LA CARACTERISTICA PARA LA ID-MAPPING-MONITOREO
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
        'ID-MAPPING-MONITOREO',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INGRESO DE LA CARACTERISTICA PARA LA VLAN-MONITOREO
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
        'VLAN-MONITOREO',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

--Se relaciona la característica GEM-PORT-MONITOREO con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'GEM-PORT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica TRAFFIC-TABLE-MONITOREO con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'TRAFFIC-TABLE-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica T-CONT-MONITOREO con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'T-CONT-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica ID-MAPPING-MONITOREO con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID-MAPPING-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica VLAN-MONITOREO con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'VLAN-MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Ingresamos el detalle para el limite de productos permitidos en datos safe city
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'PRODUCTOS_PERMITIDOS',
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

-- INGRESO DE LA CARACTERISTICA PARA EL ID_DETALLE_TAREA_INSTALACION
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
        'ID_DETALLE_TAREA_INSTALACION',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

--Se relaciona la característica LINE-PROFILE-NAME con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'LINE-PROFILE-NAME'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PROTOCOLOS_ENRUTAMIENTO_GPON'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'PROTOCOLOS_ENRUTAMIENTO_GPON',
        'Lista de los protocolos de enrutamientos para la red GPON',
        'TECNICO',
        'L3MPLS',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'PROTOCOLOS_ENRUTAMIENTO_GPON'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'PROTOCOLOS_ENRUTAMIENTO_GPON'
            AND ESTADO = 'Activo'
        ),
        'STANDARD',
        'Asignar',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
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
            WHERE NOMBRE_PARAMETRO = 'PROTOCOLOS_ENRUTAMIENTO_GPON'
            AND ESTADO = 'Activo'
        ),
        'BGP',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos el detalle para verificar los tipo de red GPON y MPLS
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VERIFICAR TIPO RED',
        'VERIFICAR_GPON',
        'GPON',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VERIFICAR TIPO RED',
        'VERIFICAR_MPLS',
        'MPLS',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos los detalle para obtener la característica de la VPN por tipo de red GPON y MPLS
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VPN TIPO RED',
        'GPON',
        'VPN_GPON',
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VPN TIPO RED',
        'MPLS',
        'VPN',
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

--Ingresamos los detalle para los nombres de los detalles de olt multiplataforma
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'NOMBRES PARAMETROS DETALLES MULTIPLATAFORMA',
        'MULTIPLATAFORMA',
        'PE_ASIGNADO',
        'NODO_ASIGNADO',
        'IPV6',
        'ELEMENTO_ACTIVO_POR_MULTIPLATAFORMA',
        'INTERFACES_PE',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos los detalle para los nombres de parámetros de las subredes y vlans
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'NOMBRES PARAMETROS SUBREDES Y VLANS',
        'INTPRIGPON',
        'INTBKGPON',
        'SAFECITYGPON',
        'VLAN INTERNET GPON PRINCIPAL',
        'VLAN INTERNET GPON BACKUP',
        'VLAN SAFECITY GPON',
        'DATOSGPON',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos los detalle para los nombres de parámetros de las subredes y vlans
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VRF PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'safecity-camaras',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

-- DETALLE PARAMETROS PARA ENLACE_DATOS NO REQUERIDO
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto si requiere enlace datos',
        ( SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo' ),
        'ENLACE_DATOS',
        'GPON',
        'SI',
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

-- DETALLE PARAMETROS PARA LIMITE_ASIGNAR_INTERFACES_OLT_MULTIPLATAFORMA
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Límite de interfaces en la asignación de recursos de red para Olt Multiplataforma',
        'LIMITE_ASIGNAR_INTERFACES_OLT_MULTIPLATAFORMA',
        '2',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Registramos las cantidad de velocidad con los que podrá elegir al momento de crear un servicio.
--Cabecera
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PROD_VELOCIDAD_GPON',
    'PROD_VELOCIDAD_GPON',
    'COMERCIAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '5',
    'MB',
    '5120',
    '1',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '10',
    'MB',
    '10240',
    '2',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '15',
    'MB',
    '15360',
    '3',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '20',
    'MB',
    '20480',
    '4',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '25',
    'MB',
    '25600',
    '5',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '30',
    'MB',
    '30720',
    '6',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '35',
    'MB',
    '35840',
    '7',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor7,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_VELOCIDAD_GPON'
    ),
    'PROD_VELOCIDAD_GPON',
    '40',
    'MB',
    '40960',
    '8',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;
/
