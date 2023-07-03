/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear cabecera y detalle de parametros para registro de ips controladoras
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 
 * @since 10-1-2023 - Versión Inicial.
 */

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
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'Parametros para ip controladora por marca asociado a servicios WIFI GPON',
    'TECNICO',
    'Activo',
    'mcarpio',
    SYSDATE,
    '127.0.0.1'
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
    observacion,
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
            nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'HUAWEI',
    '181.198.110.139',
    '172.16.32.30',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    observacion,
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
            nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'HUAWEI',
    '157.100.14.129',
    '172.16.32.39',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    observacion,
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
            nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'HUAWEI',
    '157.100.14.131',
    '172.16.32.42',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    observacion,
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
            nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'HUAWEI',
    '181.39.24.111',
    '172.16.32.48',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    observacion,
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
            nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'RUCKUS',
    '181.39.24.101',
    '172.24.13.32:8443',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    observacion,
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
            nombre_parametro = 'IP_CONTROLADORA_GPON_MPLS_TN' AND estado = 'Activo'
    ),
    'IP_CONTROLADORA_GPON_MPLS_TN',
    'RUCKUS',
    '181.39.24.134',
    '172.24.13.38:8443',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
    SYSDATE,
    '127.0.0.1',
    10
);

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
    'IP CONTROLADORA',
    'N',
    'Activo',
    SYSDATE,
    'mcarpio',
    NULL,
    NULL,
    'COMERCIAL'
);

INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'WIFI GPON'
            AND empresa_cod = 10
            AND nombre_tecnico = 'SAFECITYWIFI'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'IP CONTROLADORA'
    ),
    SYSDATE,
    NULL,
    'mcarpio',
    NULL,
    'Activo',
    'NO'
);

commit;

/