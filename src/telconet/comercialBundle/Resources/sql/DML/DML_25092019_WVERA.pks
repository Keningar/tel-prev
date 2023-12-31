INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    USR_CREACION,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
            FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE NOMBRE_TECNICO = 'INTERNET SMALL BUSINESS'
            AND DESCRIPCION_PRODUCTO = 'Internet Small Business' 
            AND EMPRESA_COD = 10), 
        (SELECT ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_FACTIBILIDAD'),
        CURRENT_TIMESTAMP,
        'wvera',
        'Activo',
        'SI');

INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    USR_CREACION,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
        FROM DB_COMERCIAL.ADMI_PRODUCTO
        WHERE DESCRIPCION_PRODUCTO = 'Internet SMB Centros Comercial'),--Telcos Home / SMB Centros Comerciales. 
        (SELECT ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_FACTIBILIDAD'),
        CURRENT_TIMESTAMP,
        'wvera',
        'Activo',
        'SI');

INSERT
  INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
    (
    ID_PRODUCTO_CARACTERISITICA,
    PRODUCTO_ID,
    CARACTERISTICA_ID,
    FE_CREACION,
    USR_CREACION,
    ESTADO,
    VISIBLE_COMERCIAL
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
        (SELECT ID_PRODUCTO
        FROM DB_COMERCIAL.ADMI_PRODUCTO
        WHERE DESCRIPCION_PRODUCTO = 'TelcoHome'), 
        (SELECT ID_CARACTERISTICA
        FROM
            DB_COMERCIAL.ADMI_CARACTERISTICA
        WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_FACTIBILIDAD'),
        CURRENT_TIMESTAMP,
        'wvera',
        'Activo',
        'SI');

COMMIT;
