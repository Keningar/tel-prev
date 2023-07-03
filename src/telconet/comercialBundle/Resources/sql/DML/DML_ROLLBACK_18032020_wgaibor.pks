DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id = (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'TELEWORKER'
    );

DELETE FROM db_comercial.admi_producto_caracteristica
WHERE
    producto_id = (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'IP TELEWORKER'
    );

DELETE FROM db_comercial.admi_producto
WHERE
    descripcion_producto = 'TELEWORKER';

DELETE FROM db_comercial.admi_producto
WHERE
    descripcion_producto = 'IP TELEWORKER';

DELETE FROM db_comercial.admi_comision_det
WHERE
    comision_id = (
        SELECT
            acca.id_comision
        FROM
            db_comercial.admi_comision_cab   acca,
            db_comercial.admi_producto       adpr
        WHERE
            acca.producto_id = adpr.id_producto
            AND adpr.descripcion_producto = 'TELEWORKER'
            AND acca.usr_creacion = 'wgaibor'
    );

DELETE FROM db_comercial.admi_comision_det
WHERE
    comision_id = (
        SELECT
            acca.id_comision
        FROM
            db_comercial.admi_comision_cab   acca,
            db_comercial.admi_producto       adpr
        WHERE
            acca.producto_id = adpr.id_producto
            AND adpr.descripcion_producto = 'IP TELEWORKER'
            AND acca.usr_creacion = 'wgaibor'
    );

DELETE FROM db_comercial.admi_comision_cab
WHERE
    producto_id = (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'TELEWORKER'
    );

DELETE FROM db_comercial.admi_comision_cab
WHERE
    producto_id = (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'IP TELEWORKER'
    );

DELETE FROM db_general.admi_parametro_det
WHERE
    parametro_id = (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PRODUCTO_RELACIONADO_SMB'
    )
    AND valor1 = (
        SELECT
            adpr.descripcion_producto
        FROM
            db_comercial.admi_producto adpr
        WHERE
            adpr.descripcion_producto = 'TELEWORKER'
            AND adpr.empresa_cod = 10
    );


COMMIT;

/
