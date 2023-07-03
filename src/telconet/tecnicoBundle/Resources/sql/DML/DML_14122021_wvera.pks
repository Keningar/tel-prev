
--Creación de parametros.
--Ingresamos la cabecera de parámetros
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    PROCESO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETRO_PRODUCTO_FACTIBILIDAD',
    'PARAMETRO QUE CONTIENE EL NOMBRE DEL PRODUCTO INTERNET Internet Dedicado BS',
    'TECNICO',
    'NUEVO_CLIENTE',
    'Activo',
    'wvera',
    SYSDATE,
    '127.0.0.1'
);

--Ingresamos el detalle de parámetros
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    EMPRESA_COD,
    OBSERVACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT
            ID_PARAMETRO
        FROM
            DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE
            NOMBRE_PARAMETRO = 'PARAMETRO_PRODUCTO_FACTIBILIDAD'
            AND ESTADO = 'Activo'
    ),
    'Producto parametrizado para la factibilidad',
    'Internet Dedicado BS',
    '',
    'Activo',
    'wvera',
    SYSDATE,
    '127.0.0.1',
    10,
    'Valor1: nombre del producto parametrizado para la factibilidad'
);


COMMIT;

/