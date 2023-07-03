--Cabecera de Productos megadatos para ser activados
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'ACTIVACION_PRODUCTOS_MEGADATOS',
                'Productos megadatos para ser activados',
                'TECNICO',
				'ACTIVACION_PRODUCTOS_MEGADATOS',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

-- PRODUCTO Wifi DB Premium + Extender DB
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='ACTIVACION_PRODUCTOS_MEGADATOS'),
        'Wifi DB Premium + Extender DB',
        'WADB',
        'SERIE_EXTENDER,MAC_EXTENDER,MODELO_EXTENDER,DESCRIPCION_EXTENDER',
        (select ID_PROGRESOS_TAREA from DB_SOPORTE.admi_progresos_tarea where NOMBRE_TAREA = 'INSTALACION_WIFI_AP'),
        'NO',
        'Activo',
        'rmoranc',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'SI',
        NULL,
        NULL,
        NULL,
        'En Valor1 se coloca el código del producto, valor2 son los equipos a ingresar, valor3 es el id del flujo del progreso, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);

-- PRODUCTO CABLEADO ETHERNET
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='ACTIVACION_PRODUCTOS_MEGADATOS'),
        'CABLEADO ETHERNET',
        'CABL',
        '',
        (select ID_PROGRESOS_TAREA from DB_SOPORTE.admi_progresos_tarea where NOMBRE_TAREA = 'INSTALACION_MD_CABLEADO_ETHERNET'),
        'NO',
        'Activo',
        'rmoranc',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'NO',
        NULL,
        NULL,
        NULL,
        'En Valor1 se coloca el código del producto, valor3 es el id del flujo del progreso, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);

-- PRODUCTO NOGGIN
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='ACTIVACION_PRODUCTOS_MEGADATOS'),
        'NOGGIN',
        'NO01',
        '',
        NULL,
        'NO',
        'Activo',
        'rmoranc',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'NO',
        NULL,
        NULL,
        NULL,
        'En Valor1 se coloca el código del producto, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);

-- PRODUCTO PARAMOUNT+
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='ACTIVACION_PRODUCTOS_MEGADATOS'),
        'PARAMOUNT+',
        'PA01',
        '',
        NULL,
        'NO',
        'Activo',
        'rmoranc',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'NO',
        NULL,
        NULL,
        NULL,
        'En Valor1 se coloca el código del producto, valor4 es una bandera para realizar la activación del producto en tarea  de traslado, valor5 es una bandera para presentar los equipos'
);

--Id del producto de cableado megadatos
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'Id del producto de cableado megadatos',
    'ID_PRODUCTO_CABLEADO_MD',
    '1332',
    NULL,
    NULL,
    'Activo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--Id del producto de Wifi+Ap Megadatos
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'Id del producto de Wifi+Ap Megadatos',
    'ID_PRODUCTO_WIFI+AP',
    '1357',
    NULL,
    NULL,
    'Activo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

COMMIT ;