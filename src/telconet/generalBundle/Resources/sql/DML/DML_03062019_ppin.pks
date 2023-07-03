SET DEFINE OFF;

-- Parametrización de empresa que tiene restricción de factibilidad.
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'RESTRICCION_FACTIBILIDAD_EMPRESA',
    'PARAMETRO QUE ESPECIFICA LA EMPRESA CON RESTRICCION DE FACTIBILIDAD AUTOMATICA',
    'PLANIFICACION',
    'FACTIBILIDAD',
    'Activo',
    'ppin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);

-- Parametrización de empresa que tiene restricción de factibilidad.

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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'RESTRICCION_FACTIBILIDAD_EMPRESA'
        AND estado = 'Activo'
    ),
    'RESTRICCION_FACTIBILIDAD_EMPRESA',
    '18',
    'MD',
    NULL,
    NULL,
    'Activo',
    'ppin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    'TN',
    NULL,
    NULL,
    NULL
);

COMMIT;
/