SET DEFINE OFF;

-- Crear Característica REQUIERE_INSPECCION
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
    'REQUIERE_INSPECCION',
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
    276,
    (
        SELECT
            id_caracteristica
        FROM
            admi_caracteristica
        WHERE
                descripcion_caracteristica = 'REQUIERE_INSPECCION'
    ),
    SYSDATE,
    NULL,
    'ppin',
    NULL,
    'Activo',
    'SI'
);

--CREAR RELACION ENTRE PRODUCTO WIFI Alquiler de Equipos y la caracteristica REGISTRO_UNITARIO
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
    276,
    (
        SELECT
            id_caracteristica
        FROM
            admi_caracteristica
        WHERE
                descripcion_caracteristica = 'REGISTRO_UNITARIO'
    ),
    SYSDATE,
    NULL,
    'ppin',
    NULL,
    'Activo',
    'NO'
);

COMMIT;
/