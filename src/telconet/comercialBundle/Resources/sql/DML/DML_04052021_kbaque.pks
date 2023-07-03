/**
 * Creación de caracteristica.
 *
 * @author Kevin Baque <kbaque@telconet.ec>
 * @version 1.0 04-05-2021
 */
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'ES_DISTRIBUIDOR',
    'T',
    'Activo',
    SYSDATE,
    'kbaque',
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'RAZON_SOCIAL_CLT_DISTRIBUIDOR',
    'C',
    'Activo',
    SYSDATE,
    'kbaque',
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'IDENTIFICACION_CLT_DISTRIBUIDOR',
    'C',
    'Activo',
    SYSDATE,
    'kbaque',
    'COMERCIAL'
);

INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'VENDEDOR_CLT_DISTRIBUIDOR',
    'C',
    'Activo',
    SYSDATE,
    'kbaque',
    'COMERCIAL'
);
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'PRODUCTOS_DISTRIBUIDOR',
    'C',
    'Activo',
    SYSDATE,
    'kbaque',
    'COMERCIAL'
);
--AGREGAMOS A TODOS LOS PRODUCTOS DE TN LA CARACTERISTICA

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    USR_CREACION,
    ESTADO,
    VISIBLE_COMERCIAL
)
    SELECT
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        ID_PRODUCTO,
        (
            SELECT
                ID_CARACTERISTICA
            FROM
                DB_COMERCIAL.ADMI_CARACTERISTICA
            WHERE
                DESCRIPCION_CARACTERISTICA = 'RAZON_SOCIAL_CLT_DISTRIBUIDOR'
        ),
        SYSDATE,
        'kbaque',
        'Activo',
        'NO'
    FROM
        (
            SELECT
                ID_PRODUCTO
            FROM
                DB_COMERCIAL.ADMI_PRODUCTO
            WHERE
                ESTADO = 'Activo'
                AND EMPRESA_COD = 10
        );
--AGREGAMOS A TODOS LOS PRODUCTOS DE TN LA CARACTERISTICA

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    USR_CREACION,
    ESTADO,
    VISIBLE_COMERCIAL
)
    SELECT
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        ID_PRODUCTO,
        (
            SELECT
                ID_CARACTERISTICA
            FROM
                DB_COMERCIAL.ADMI_CARACTERISTICA
            WHERE
                DESCRIPCION_CARACTERISTICA = 'IDENTIFICACION_CLT_DISTRIBUIDOR'
        ),
        SYSDATE,
        'kbaque',
        'Activo',
        'NO'
    FROM
        (
            SELECT
                ID_PRODUCTO
            FROM
                DB_COMERCIAL.ADMI_PRODUCTO
            WHERE
                ESTADO = 'Activo'
                AND EMPRESA_COD = 10
        );

INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD (
    ID_TIPO_SOLICITUD,
    DESCRIPCION_SOLICITUD,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    ESTADO,
    TAREA_ID,
    ITEM_MENU_ID,
    PROCESO_ID
) VALUES (
    DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
    'SOLICITUD DE DISTRIBUIDOR',
    SYSDATE,
    'kbaque',
    SYSDATE,
    'kbaque',
    'Activo',
    NULL,
    NULL,
    NULL
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD,
    IP_ULT_MOD
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS_SOLICITUD_DISTRIBUIDOR',
    'PARAMETROS AUXILIARES A SOLICITUD DE DISTRIBUIDOR',
    'COMERCIAL',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    'kbaque',
    SYSDATE,
    '127.0.0.1'
);

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
    EMPRESA_COD
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
            AND MODULO = 'COMERCIAL'
    ),
    'LISTA_USUARIO_APROBACION',
    'fvillacreses',
    'R1',
    'Pendiente Gerente',
    'GERENTE_VENTAS',
    'Activo',
    'kbaque',
    SYSDATE,
    '127.0.0.1',
    10
);

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
    EMPRESA_COD
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
            AND MODULO = 'COMERCIAL'
    ),
    'LISTA_USUARIO_APROBACION',
    'mescobar',
    'R2',
    'Pendiente Gerente',
    'GERENTE_VENTAS',
    'Activo',
    'kbaque',
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
            nombre_parametro = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
            AND modulo = 'COMERCIAL'
    ),
    'ESTADO',
    'Pendiente',
    'Activo',
    'kbaque',
    sysdate,
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
            nombre_parametro = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
            AND modulo = 'COMERCIAL'
    ),
    'ESTADO',
    'Pendiente Gerente',
    'Activo',
    'kbaque',
    sysdate,
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
            nombre_parametro = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
            AND modulo = 'COMERCIAL'
    ),
    'ESTADO',
    'Aprobada',
    'Activo',
    'kbaque',
    sysdate,
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
            nombre_parametro = 'PARAMETROS_SOLICITUD_DISTRIBUIDOR'
            AND modulo = 'COMERCIAL'
    ),
    'ESTADO',
    'Rechazado',
    'Activo',
    'kbaque',
    sysdate,
    '127.0.0.1',
    10
);
COMMIT;
/