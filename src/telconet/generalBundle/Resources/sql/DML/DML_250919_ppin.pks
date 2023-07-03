SET DEFINE OFF;

INSERT INTO db_general.admi_parametro_cab VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'VALIDACIONES_WIFI_ALQUILER_EQUIPOS',
    'PARAMETRO QUE CONTIENE LAS VALIDACIONES PARA WIFI ALQUILER EQUIPOS',
    'TECNICO',
    null,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1'
);

INSERT INTO db_general.admi_parametro_det VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'VALIDACIONES_WIFI_ALQUILER_EQUIPOS'
            AND estado = 'Activo'
    ),
    'PARAMETROS_VALIDACIONES_WIFI_ALQUILER_EQUIPOS',
    '{"descripcionProducto": "WIFI", "nombreProducto": "WIFI Alquiler Equipos"}',
    NULL,
    NULL,
    NULL,
    'Activo',
    'ppin',
    sysdate,
    '127.0.0.1',
    'ppin',
    sysdate,
    '127.0.0.1',
    NULL,
    10,
    null,
    null,
    null
);

COMMIT;
/