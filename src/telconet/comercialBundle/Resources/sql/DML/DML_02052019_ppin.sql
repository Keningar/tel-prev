SET DEFINE OFF;

-- Crear Característica RELACION INTERNET WIFI
INSERT INTO db_comercial.admi_caracteristica
    (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
    )
VALUES
    (
        db_comercial.seq_admi_caracteristica.NEXTVAL,
        'RELACION_INTERNET_WIFI',
        'N',
        'Activo',
        SYSDATE,
        'ppin',
        NULL,
        NULL,
        'TECNICA'
);

-- Crear Relacion entre Producto y Caracteristica
INSERT INTO db_comercial.admi_producto_caracteristica
    (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
    )
VALUES
    (
        db_comercial.seq_admi_producto_carac.NEXTVAL,
        237,
        (
        SELECT
            id_caracteristica
        FROM
            admi_caracteristica
        WHERE
            descripcion_caracteristica = 'RELACION_INTERNET_WIFI'
    ),
        SYSDATE,
        NULL,
        'ppin',
        NULL,
        'Activo',
        'SI'
);


-- Crear Característica Instalacion Simultanea
INSERT INTO db_comercial.admi_caracteristica
    (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
    )
VALUES
    (
        db_comercial.seq_admi_caracteristica.NEXTVAL,
        'INSTALACION_SIMULTANEA_WIFI',
        'N',
        'Activo',
        SYSDATE,
        'ppin',
        NULL,
        NULL,
        'TECNICA'
);

-- Crear Relacion entre Producto y Caracteristica
INSERT INTO db_comercial.admi_producto_caracteristica
    (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
    )
VALUES
    (
        db_comercial.seq_admi_producto_carac.NEXTVAL,
        261,
        (
        SELECT
            id_caracteristica
        FROM
            admi_caracteristica
        WHERE
            descripcion_caracteristica = 'INSTALACION_SIMULTANEA_WIFI'
    ),
        SYSDATE,
        NULL,
        'ppin',
        NULL,
        'Activo',
        'SI'
);

COMMIT;
/