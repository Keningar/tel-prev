--INSERT PRODUCTO
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO 
        SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
            AP.EMPRESA_COD,
            'WIFISAFE',
            'WIFI GPON',
            AP.FUNCION_COSTO,
            AP.INSTALACION,
            AP.ESTADO,
            SYSDATE,
            'facaicedo',
            AP.IP_CREACION,
            AP.CTA_CONTABLE_PROD,
            AP.CTA_CONTABLE_PROD_NC,
            AP.ES_PREFERENCIA,
            AP.ES_ENLACE,
            AP.REQUIERE_PLANIFICACION,
            AP.REQUIERE_INFO_TECNICA,
            'SAFECITYWIFI',
            AP.CTA_CONTABLE_DESC,
            AP.TIPO,
            AP.ES_CONCENTRADOR,
            'PRECIO=20',
            AP.SOPORTE_MASIVO,
            'Pendiente',
            'SEGURIDAD ELECTRONICA Y FISICA',
            AP.COMISION_VENTA,
            AP.COMISION_MANTENIMIENTO,
            AP.USR_GERENTE,
            AP.CLASIFICACION,
            AP.REQUIERE_COMISIONAR,
            'SEGURIDAD ELECTRONICA',
            AP.LINEA_NEGOCIO,
            AP.TERMINO_CONDICION,
            AP.FRECUENCIA
        FROM DB_COMERCIAL.ADMI_PRODUCTO AP 
        WHERE AP.DESCRIPCION_PRODUCTO='SAFE VIDEO ANALYTICS CAM' 
            AND AP.ESTADO='Activo' 
            AND AP.NOMBRE_TECNICO='SAFECITYDATOS' AND AP.EMPRESA_COD=10;

--INSERT CARACTERISTICAS
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
        SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
            (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO='Activo' AND EMPRESA_COD=10),
            APC.CARACTERISTICA_ID,
            SYSDATE,
            APC.FE_ULT_MOD,
            'facaicedo',
            APC.USR_ULT_MOD,
            APC.ESTADO,
            APC.VISIBLE_COMERCIAL
        FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA APC 
        WHERE APC.PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
                                 WHERE ESTADO='Activo' AND NOMBRE_TECNICO='SAFECITYDATOS' AND EMPRESA_COD=10)
            AND APC.ESTADO = 'Activo'
            AND APC.CARACTERISTICA_ID NOT IN (SELECT ID_CARACTERISTICA FROM ADMI_CARACTERISTICA
                            WHERE DESCRIPCION_CARACTERISTICA IN ('RELACION_MASCARILLA_CAMARA_SAFECITY','USUARIO_CAMARA','CLAVE_CAMARA',
                                                                 'URL_CAMARA') );

--INSERT CARACTERISTICA 'Servicio Wi-Fi'
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
    'Servicio Wi-Fi',
    'S',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT CARACTERISTICA 'Precio Wi-Fi'
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
    'Precio Wi-Fi',
    'N',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Servicio Wi-Fi'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Servicio Wi-Fi' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Precio Wi-Fi'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Precio Wi-Fi' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INSERT CARACTERISTICA 'VLAN SSID'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'VLAN SSID',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INSERT CARACTERISTICA 'VLAN ADMIN'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'VLAN ADMIN',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INSERT CARACTERISTICA 'VRF SSID'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'VRF SSID',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

-- INSERT CARACTERISTICA 'VRF ADMIN'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA
    (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    FE_ULT_MOD,
    USR_ULT_MOD,
    TIPO
    )
VALUES
    (
        DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
        'VRF ADMIN',
        'N',
        'Activo',
        SYSDATE,
        'facaicedo',
        NULL,
        NULL,
        'TECNICA'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VLAN SSID'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VLAN SSID' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VLAN ADMIN'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VLAN ADMIN' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VRF SSID'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VRF SSID' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'VRF ADMIN'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'VRF ADMIN' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'MAC CLIENTE'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'MAC CLIENTE' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'PRODUCTO_CARACTERISTICA_RELACION_PRODUCTO'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
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
            WHERE NOMBRE_PARAMETRO = 'PRODUCTO_CARACTERISTICA_RELACION_PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Detalles de las relaciones de los productos características.',
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
          AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo')
          AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Servicio Wi-Fi' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
          AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo')
          AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Precio Wi-Fi' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'SAFECITYWIFI' AND ESTADO = 'Activo'),
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

-- SERVICIO ADICIONAL WI-FI
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
        VALOR6,
        VALOR7,
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
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='DATOS SAFECITY' AND ESTADO = 'Activo' ),
        'AGREGAR_SERVICIO_ADICIONAL',
        '[Servicio Wi-Fi]',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'SERVICIO WIFI 10',
        'RELACION_SERVICIOS_GPON_SAFECITY',
        'GPON_MPLS',
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

--DEFINIR TIPO DE RED - SERVICIO WIFI
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
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            NOMBRE_TECNICO='SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            NOMBRE_TECNICO='SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    'GPON_MPLS',
    'S',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);

--PRODUCTOS NO PERMITIDOS EN TIPO RED MPLS - SERVICIO WIFI
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
    valor5,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'PARAMETRO PARA DEFINIR LOS PRODUCTOS NO PERMITIDOS EN TIPO RED MPLS',
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
            NOMBRE_TECNICO='SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            NOMBRE_TECNICO='SAFECITYWIFI'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    'MPLS',
    'S',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    'PRODUCTO_NO_PERMITIDO_MPLS',
    10
);

-- DETALLE PARAMETROS PARA CAMARA
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'COORDINAR_OBSERVACION',
        'WIFI SAFECITY',
        'WIFI SAFECITY',
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

--Ingresamos el detalle para no mostrar el producto adicional en la factibilidad y coordinacion con el datos safe city
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
        'Lista de la configuración del producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'FLUJO_OCULTO',
        'SERVICIO_ADICIONAL',
        'NO',
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

--Ingresamos el detalle de la actualización del estado de la tarea del servicio Wi-Fi
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
        'CAMBIAR ESTADO TAREA SERVICIO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'Pausada',
        'Se cambió el estado de la tarea a pausada, hasta que termine la tarea de instalación del servicio principal DATOS SAFE CITY',
        'Asignada',
        'Se cambió el estado de la tarea a asignada, porque el servicio principal DATOS SAFE CITY ya esta activado.',
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

--Ingresamos el detalle para el producto requerido datos safe city para el producto WIFI
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
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
        'Lista de la configuración del producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'PRODUCTO_REQUERIDO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo' ),
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

--Ingresamo el detalle para filtrar la velocidad del producto WIFI
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
        'Parámetro con los ids de los productos que se deben verificar las velocidades.',
        'PRODUCTOS_VERIFICA_VELOCIDAD',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
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

--Ingresamo el detalle para filtrar la velocidad del producto WIFI
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
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
        'Parámetro con los ids de los productos y sus velocidades disponibles.',
        'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        '10',
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

-- Detalle velocidad por default al generar el servicio wifi de forma automática.
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Velocidad por default al generar el servicio de forma automática.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'VELOCIDAD_SERVICIO',
        '10',
        '10240',
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

-- INSERTAR LA ULTIMA MILLA PARA PRODUCTO WIFI
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
    valor4,
    valor5,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUEVA_RED_GPON_TN'
    ),
    'UM FTTX',
    ( SELECT NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO
        WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
    'FTTx',
    'MD',
    '18',
    'ULTIMA_MILLA_GPON_TN',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.0',
    '10'
);

-- DETALLE PARAMETRO PARA EL PRODUCTO PRINCIPAL
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
        'PRODUCTO_PRINCIPAL',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo' ),
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

-- DETALLE PARAMETRO PARA LA CANTIDAD DE PUERTOS DISPONIBLES DEFAULT POR ONT
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
        'PUERTO_DISPONIBLE_DEFAULT_ONT',
        '4',
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

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2,
    valor3,
    valor4,
    valor5,
    valor6,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
    (select id_producto from db_comercial.admi_producto where NOMBRE_TECNICO = 'SAFECITYWIFI'),
    (select id_producto from db_comercial.admi_producto where NOMBRE_TECNICO = 'DATOS SAFECITY'),
    'AsignadoTarea',
    'STANDARD',
    'WIFI',
    'MIGRAR',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

--ACTUALIZAR PARAMETRO PARAMETROS PROYECTO GPON SAFECITY - VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.DESCRIPCION = 'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
    PDE.VALOR5 = 'CAMARA', PDE.VALOR6 = 'MIGRAR'
WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'
    ) AND DET.DESCRIPCION = 'VALIDAR RELACION CAMARA CON DATOS SAFECITY'
    AND DET.VALOR1 = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'SAFECITYDATOS')
);

--Registramos la cantidad del servicio WIFI con los que podrá elegir al momento de crear un servicio.
--Cabecera
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PROD_Servicio Wi-Fi',
    'PROD_Servicio Wi-Fi',
    'COMERCIAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_Servicio Wi-Fi'
    ),
    'PROD_Servicio Wi-Fi',
    '1',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--INSERT DE LOS DETALLES DE PARAMETROS PARA LOS VALORES DEFAULT DE LOS PRODUCTOS CARACTERÍSTICAS
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PROD_CARACTERISTICA_SELECCIONE_VALUE',
    'Detalles de los valores por defecto en la opción *Seleccione* de los productos características',
    'COMERCIAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_CARACTERISTICA_SELECCIONE_VALUE'
    ),
    'Detalle del valor por defecto en la opción *Seleccione* del producto característica',
    'PROD_Cantidad Camaras',
    '0',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PROD_CARACTERISTICA_SELECCIONE_VALUE'
    ),
    'Detalle del valor por defecto en la opción *Seleccione* del producto característica',
    'PROD_Servicio Wi-Fi',
    '0',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--INSERT DE LOS DETALLES DE PARAMETROS PARA LOS PRODUCTO_CARACTERISTICA_CLASS_RELACION
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'PRODUCTO_CARACTERISTICA_CLASS_RELACION',
    'Detalles de las relaciones de los productos características por clases',
    'COMERCIAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
--detalle
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PRODUCTO_CARACTERISTICA_CLASS_RELACION'
    ),
    'Relación del producto característica por clase, permite el ingreso de una o más input por clases.',
    (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
      AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo')
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Cantidad Camaras' AND ESTADO = 'Activo')),
    'servicios-adicional-safecity',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PRODUCTO_CARACTERISTICA_CLASS_RELACION'
    ),
    'Relación del producto característica por clase, permite el ingreso de una o más input por clases.',
    (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
      AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo')
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Servicio Wi-Fi' AND ESTADO = 'Activo')),
    'servicios-adicional-safecity',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);

--Creacion de Proceso: SOLICITAR NUEVO SERVICIO SAFE CITY
INSERT INTO DB_SOPORTE.ADMI_PROCESO VALUES (DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,NULL,'SOLICITAR NUEVO SERVICIO WIFI SAFE CITY','PROCESO PARA SERVICIO WIFI SAFECITY',null,'Activo','facaicedo',SYSDATE,'facaicedo',SYSDATE,NULL,NULL);

--Asociar Proceso a la empresa.
INSERT INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA VALUES (DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,(SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO WIFI SAFE CITY'),'10','Activo','facaicedo',SYSDATE);

--Creacion de Tarea: FIBRA: INSTALACION CAMARAS
INSERT INTO DB_SOPORTE.ADMI_TAREA VALUES (DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,(SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO WIFI SAFE CITY'),NULL,NULL,NULL,1,0,'FIBRA: INSTALACION WIFI','TAREA DE INSTALACION DE WIFI',1,'MINUTOS',1,1,'Activo','facaicedo',SYSDATE,'facaicedo',SYSDATE,NULL,NULL,NULL,'N','N');

--Ingresamos el detalle para el proceso y tarea de instalación de wifi
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
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
        'TAREA DE INSTALACION DEL SERVICIO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'SOLICITAR NUEVO SERVICIO WIFI SAFE CITY',
        'FIBRA: INSTALACION WIFI',
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

--Ingresamos el detalle para el limite de productos permitidos para el WIFI
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
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
        'Lista de la configuración del producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'PRODUCTOS_PERMITIDOS',
        '1',
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

--Ingresamos los detalle para la vrf del producto
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
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
        'VRF PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'wireless_ssid5_1428',
        'wireless_admin_1429',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos el detalle para la vlan del producto 'VLAN SSID WIFI SAFECITY GPON'
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
        IP_CREACION
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
        'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY',
        'VLAN SSID WIFI SAFECITY GPON',
        '891',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'SSID',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos el detalle para la vlan del producto 'VLAN ADMIN WIFI SAFECITY GPON'
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
        IP_CREACION
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
        'PARAMETRO VLAN PARA SERVICIOS ADICIONALES SAFECITY',
        'VLAN ADMIN WIFI SAFECITY GPON',
        '890',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'ADMIN',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos el detalle para el nombre del uso de la subred del producto 'SAFECITYWIFI'
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
        IP_CREACION
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
        'PARAMETRO USO SUBRED PARA SERVICIOS ADICIONALES SAFECITY',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYWIFI' AND ESTADO = 'Activo' ),
        'WIFISSIDSAFECITY',
        'WIFIADMINSAFECITY',
        '255.255.255.128',
        'Activo',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--Ingresamos los tipos de elementos ap
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO TIPOS ELEMENTOS AP',
    'CPE WIFI',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

--Ingresamos el detalle de los datos para reasignar la tarea wifi
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'
            AND ESTADO = 'Activo'
        ),
        'DEPARTAMENTO_QUE_CONFIGURA_AP',
        '124',
        'Se reasigna automaticamente la tarea del servicio para la configuracion del AP',
        'ID_PERSONA',
        '237553',
        '616659',
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
