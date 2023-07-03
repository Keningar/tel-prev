--=======================================================================
-- Ingreso el tipo de solicitud RPA licencia
-- Ingreso de la característica para la licencia Fortigate
-- Ingreso del producto característica para la licencia Fortigate
-- Ingreso los detalles de parámetros de los id de las marcas de los elementos para los productos que requieran licenciamiento por el RPA
--=======================================================================

-- INGRESO EL TIPO DE SOLICITUD DE 'SOLICITUD RPA LICENCIA'
INSERT INTO DB_COMERCIAL.ADMI_TIPO_SOLICITUD
(
        ID_TIPO_SOLICITUD,
        DESCRIPCION_SOLICITUD,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_TIPO_SOLICITUD.NEXTVAL,
        'SOLICITUD RPA LICENCIA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DE LA CARACTERISTICA PARA LA LICENCIA FORTIGATE
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
        ID_CARACTERISTICA,
        DESCRIPCION_CARACTERISTICA,
        TIPO_INGRESO,
        TIPO,
        ESTADO,
        USR_CREACION,
        FE_CREACION
)
VALUES
(
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'LICENCIA_FORTIGATE',
        'T',
        'TECNICA',
        'Activo',
        'facaicedo',
        SYSDATE
);
-- INGRESO DEL PRODUCTO CARACTERISTICA PARA DE LA LICENCIA FORTIGATE
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
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
        '1074',
        ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'LICENCIA_FORTIGATE' ),
        SYSDATE,
        'facaicedo',
        'Activo',
        'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
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
        '1258',
        ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'LICENCIA_FORTIGATE' ),
        SYSDATE,
        'facaicedo',
        'Activo',
        'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
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
        '1246',
        ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'LICENCIA_FORTIGATE' ),
        SYSDATE,
        'facaicedo',
        'Activo',
        'NO'
);
-- INGRESO DE LA CABECERA DE PARAMETROS DE 'RPA_MARCA_ELEMENTOS_LICENCIA'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'RPA_MARCA_ELEMENTOS_LICENCIA',
        'Lista de los id de las marcas de los elementos para los productos que requieran licenciamiento por el RPA',
        'TECNICO',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
-- INGRESO LOS DETALLES DE LA CABECERA 'RPA_MARCA_ELEMENTOS_LICENCIA'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'RPA_MARCA_ELEMENTOS_LICENCIA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1074',
        '4645',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        EMPRESA_COD
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'RPA_MARCA_ELEMENTOS_LICENCIA'
            AND ESTADO = 'Activo'
        ),
        'LISTA VALORES',
        '1244',
        '4645',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1',
        (
            SELECT COD_EMPRESA
            FROM DB_COMERCIAL.INFO_EMPRESA_GRUPO
            WHERE PREFIJO = 'TN'
        )
);
COMMIT;
/
