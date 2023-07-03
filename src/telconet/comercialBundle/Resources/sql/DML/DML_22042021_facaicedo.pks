-- DETALLE PARAMETROS PARA LOS ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO
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
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Eliminado',
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
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Cancel',
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
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Rechazada',
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
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Anulado',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Se relaciona la característica INDICE CLIENTE con el producto.
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
            descripcion_caracteristica = 'INDICE CLIENTE'
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
            descripcion_caracteristica = 'INDICE CLIENTE'
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
            descripcion_caracteristica = 'INDICE CLIENTE'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica SPID con el producto.
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
            descripcion_caracteristica = 'SPID'
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
            descripcion_caracteristica = 'SPID'
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
            descripcion_caracteristica = 'SPID'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INGRESO DE LA CARACTERISTICA PARA EL SPID MONITOREO
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
        'SPID MONITOREO',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

--Se relaciona la característica SPID MONITOREO con el producto.
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
            descripcion_caracteristica = 'SPID MONITOREO'
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
            descripcion_caracteristica = 'SPID MONITOREO'
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
            descripcion_caracteristica = 'SPID MONITOREO'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica SERVICE-PROFILE con el producto.
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
            descripcion_caracteristica = 'SERVICE-PROFILE'
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
            descripcion_caracteristica = 'SERVICE-PROFILE'
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
            descripcion_caracteristica = 'SERVICE-PROFILE'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica MAC CLIENTE con el producto.
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
            descripcion_caracteristica = 'MAC CLIENTE'
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
            descripcion_caracteristica = 'MAC CLIENTE'
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
            descripcion_caracteristica = 'MAC CLIENTE'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
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
        'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO',
        'Lista de parámetros para la asignación de recursos de red para los servicios por producto y ciudades.',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- DETALLE PARAMETROS PARA LOS PARAMETROS DE 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Lista de parámetros para la asignación de recursos de red.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        '75',
        'MASCARA',
        '255.255.0.0',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Lista de parámetros para la asignación de recursos de red.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        '178',
        'MASCARA',
        '255.255.0.0',
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
-- DETALLE PARAMETROS PARA LOS PARAMETROS DE 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Lista de parámetros para la asignación de recursos de red.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        '75',
        'PREFIJOS',
        '10.245',
        'Ocupado',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Lista de parámetros para la asignación de recursos de red.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        '178',
        'PREFIJOS',
        '10.246',
        'Ocupado',
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

--inserto el detalle para la validación del maximo metraje para las cajas en la red GPON
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
            nombre_parametro = 'PROYECTO PARAMETRIZAR DISTANCIA DE CAJAS'
    ),
    'VALOR DE LA DISTANCIA USADO PARA LAS CAJAS RED GPON',
    '250',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
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
        'PROYECTO PARAMETRIZAR FILTRO DE CAJAS',
        'Lista de parámetros para filtrar las cajas por provincia o cantón',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
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
            WHERE NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
            AND ESTADO = 'Activo'
        ),
        'Lista de valores para filtrar las cajas PROVINCIA, CANTON y TODAS',
        'PROVINCIA',
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
            WHERE NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
            AND ESTADO = 'Activo'
        ),
        'Lista de valores para filtrar las cajas PROVINCIA, CANTON y TODAS',
        'PROVINCIA',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'MD'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PROYECTO PARAMETRIZAR FILTRO DE CAJAS'
            AND ESTADO = 'Activo'
        ),
        'Lista de valores para filtrar las cajas PROVINCIA, CANTON y TODAS',
        'PROVINCIA',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

-- INSERTAR DETALLES PARA VALIDAR LAS MARCAS DE TECNOLOGIAS PERMITIDAS PARA GPON
INSERT INTO db_general.admi_parametro_det (
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
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'Lista de marcas de las tecnologías permitidas para la red GPON',
    'MARCA_TECNOLOGIA_PERMITIDA_GPON',
    'HUAWEI',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.0',
    '10'
);

--Ingresamo el detalle para el valor de valan de servicios adicioanles del datos safecity
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
        'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY',
        'VLAN_SAFECITY_GPON',
        '899',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VELOCIDAD_GPON'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (   SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet Dedicado'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VELOCIDAD_GPON' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VELOCIDAD_GPON'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (   SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'Internet MPLS'
            AND codigo_producto = 'SISCTN'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Inactivo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VELOCIDAD_GPON' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VELOCIDAD_GPON'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (   SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'L3MPLS'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VELOCIDAD_GPON' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Ingresamo el detalle para filtrar la velocidad del producto DATOS GPON VIDEO ANALYTICS CAM
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
        'Parámetro con los ids de los productos que se deben verificar las velocidades.',
        'PRODUCTOS_VERIFICA_VELOCIDAD',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamo el detalle para filtrar la velocidad del producto DATOS GPON VIDEO ANALYTICS CAM
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
        'Parámetro con los ids de los productos y sus velocidades disponibles.',
        'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        '5',
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

--Se relaciona la característica SSID con el producto.
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
            descripcion_caracteristica = 'SSID'
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
            descripcion_caracteristica = 'SSID'
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
            descripcion_caracteristica = 'SSID'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica PASSWORD SSID con el producto.
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
            descripcion_caracteristica = 'PASSWORD SSID'
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
            descripcion_caracteristica = 'PASSWORD SSID'
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
            descripcion_caracteristica = 'PASSWORD SSID'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica NUMERO PC con el producto.
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
            descripcion_caracteristica = 'NUMERO PC'
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
            descripcion_caracteristica = 'NUMERO PC'
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
            descripcion_caracteristica = 'NUMERO PC'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se relaciona la característica MODO OPERACION con el producto.
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
            descripcion_caracteristica = 'MODO OPERACION'
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
            descripcion_caracteristica = 'MODO OPERACION'
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
            descripcion_caracteristica = 'MODO OPERACION'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Se ingresa la caracteristicas de la vpn, vrf y rd para servicios camara
INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    665890, --PERSONA_EMPRESA_ROL_ID
    (SELECT id_caracteristica FROM db_comercial.admi_caracteristica WHERE descripcion_caracteristica = 'VPN'),
    'safecity-camaras',
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    '127.0.0.1',
    'Activo',
    NULL
);
INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    665890, --PERSONA_EMPRESA_ROL_ID
    (SELECT id_caracteristica FROM db_comercial.admi_caracteristica WHERE descripcion_caracteristica = 'VRF'),
    'safecity-camaras',
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    '127.0.0.1',
    'Activo',
    ( SELECT ID_PERSONA_EMPRESA_ROL_CARACT FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
      WHERE CARACTERISTICA_ID = (SELECT id_caracteristica FROM db_comercial.admi_caracteristica WHERE descripcion_caracteristica = 'VPN')
      AND PERSONA_EMPRESA_ROL_ID = '665890' AND VALOR = 'safecity-camaras' )
);
INSERT INTO DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC VALUES (
    DB_COMERCIAL.SEQ_INFO_PERSONA_EMP_ROL_CARAC.nextval, 
    665890, --PERSONA_EMPRESA_ROL_ID
    (SELECT id_caracteristica FROM db_comercial.admi_caracteristica WHERE descripcion_caracteristica = 'RD_ID'),
    ( SELECT '27947:' || ID_PERSONA_EMPRESA_ROL_CARACT FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
      WHERE CARACTERISTICA_ID = (SELECT id_caracteristica FROM db_comercial.admi_caracteristica WHERE descripcion_caracteristica = 'VPN')
      AND PERSONA_EMPRESA_ROL_ID = '665890' AND VALOR = 'safecity-camaras' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    '127.0.0.1',
    'Activo',
    ( SELECT ID_PERSONA_EMPRESA_ROL_CARACT FROM DB_COMERCIAL.INFO_PERSONA_EMPRESA_ROL_CARAC
      WHERE CARACTERISTICA_ID = (SELECT id_caracteristica FROM db_comercial.admi_caracteristica WHERE descripcion_caracteristica = 'VPN')
      AND PERSONA_EMPRESA_ROL_ID = '665890' AND VALOR = 'safecity-camaras' )
);

-- DETALLE PARAMETROS PARA VALIDAR OSS PARA FLUJO GPON
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
        'Detalle de parámetro para validar esquema de flujo GPON para el WS de OSS',
        'VALIDAR_ESQUEMA_FLUJO_GPON_WS_OSS',
        'ESQUEMA_1',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamo el detalle para reemplazar por modelo o utilizar formato del SERVICE-PROFILE para la red GPON-MPLS TN
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
        'REEMPLAZAR_FORMATO_SERVICE_PROFILE',
        'FORMATO_GENERAL',
        'TN-XXXXX',
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
