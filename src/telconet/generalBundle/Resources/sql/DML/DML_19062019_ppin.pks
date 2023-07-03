SET DEFINE OFF;

-- Parametrizacion nuevo parametro para definir el concentrador wifi para provincias.

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
            nombre_parametro = 'ENLACE_DATOS_WIFI'
        AND estado = 'Activo'
    ),
    'ENLACE DE DATOS PROVINCIAS',
    'PROVINCIAS',
    'telconet-wifioutdoor_5',
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
    10,
    NULL,
    NULL,
    NULL
);

COMMIT;
/