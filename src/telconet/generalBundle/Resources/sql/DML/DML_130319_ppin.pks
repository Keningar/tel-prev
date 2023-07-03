SET DEFINE OFF;

-- Parametrizacion de servicios tradicionales, para determinar tipo de esquema en Internet WIFI
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
    'SERVICIOS_TRADICIONALES',
    'PARAMETROS DE SERVICIOS TRADICIONALES PARA EL TIPO DE ESQUEMA INTERNET WIFI',
    'TECNICO',
    'INTERNET WIFI',
    'Activo',
    'ppin',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);

-- Parametrizacion de servicios tradicionales, para determinar tipo de esquema en Internet WIFI

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
            admi_parametro_cab
        WHERE
            nombre_parametro = 'SERVICIOS_TRADICIONALES'
    ),
    'SERVICIOS_TRADICIONALES',
    '236,237,238,242',
    NULL,
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