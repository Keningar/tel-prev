--detalles de parametros para el formato de la vrf de camaras safecity
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'FORMATO_VRF_SERVICIOS',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYDATOS' AND ESTADO = 'Activo' ),
        'vrf_video',
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

--detalles de parametros para la cantidad maxima de la vrf por elemento en los servicios camaras safecity
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'MAXIMO_VRF_ELEMENTO_POR_SERVICIOS',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYDATOS' AND ESTADO = 'Activo' ),
        '63',
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

-- DETALLE PARAMETRO PARA EL PRODUCTO CAMARA
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        'PRODUCTO_ADICIONAL_CAMARA',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO = 'SAFECITYDATOS' AND ESTADO = 'Activo' ),
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

--detalles de parametros para los datos del ws rda para la verificacion de la vrf
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'PARAMETROS PARA WS de RDA - Verificacion VRF',
        'VERIFICACION_VRF',
        'consultas',
        'S',
        'consultarOLT',
        'totalVrfsConfiguradas',
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

--detalles de parametros para los nombres tecnicos de los productos
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Datos para el cambio de plan de los productos de datos e internet en la red GPON_MPLS',
        'WS_CAMBIO_PLAN_DATOS',
        'TN_CAMBIAR_PLAN_DATOS',
        '["L3MPLS","SAFECITYDATOS","SAFECITYWIFI"]',
        'DATOS',
        'Activa',
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

--detalles de parametros para los nombres tecnicos de los productos
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Datos para el cambio de plan de los productos de datos e internet en la red GPON_MPLS',
        'WS_CAMBIO_PLAN_INTERNET',
        'TN_CAMBIAR_PLAN_INTERNET',
        '["INTERNET","INTMPLS"]',
        'INTERNET',
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

--detalles de parametros para los estados de los servicios en la sumatoria de las capacidades
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Estados de los servicios en la sumatoria de las capacidades en la red GPON_MPLS',
        'ESTADOS_SERVICIOS_PERMITIDOS_TOTAL_BW',
        'Activo',
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

--detalles de parametros para los estados de los servicios en la sumatoria de las capacidades
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Estados de los servicios en la sumatoria de las capacidades en la red GPON_MPLS',
        'ESTADOS_SERVICIOS_PERMITIDOS_TOTAL_BW',
        'EnPruebas',
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

--detalles de parametros para la lista de los id de los productos para la sumatoria de las capacidades en la red GPON_MPLS
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'Lista de los id de los productos para la sumatoria de las capacidades en la red GPON_MPLS',
        'PRODUCTO_TOTAL_BW_WS',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO = 'SAFECITYDATOS' AND ESTADO = 'Activo' ),
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

--INSERT CARACTERISTICA 'SPID ADMIN'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
(
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    FE_CREACION,
    USR_CREACION,
    TIPO,
    ESTADO
)
VALUES
(
    DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'SPID ADMIN',
    'N',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'SPID ADMIN'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SPID ADMIN' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--detalles de parametros para el id del producto de camara en la red mpls
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        'PRODUCTO_CAMARA_MPLS',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SEGURIDAD ELECTRONICA   VIDEO VIGILANCIA' AND ESTADO = 'Activo' ),
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
