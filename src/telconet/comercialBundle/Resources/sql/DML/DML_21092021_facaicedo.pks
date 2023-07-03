--INSERT PRODUCTO
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO VALUES (
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'SPoEGPON',
    'SWITCH PoE GPON',
    NULL,
    NULL,
    'Activo',
    SYSDATE,
    'facaicedo',
    '127.0.0.1',
    NULL,
    NULL,
    'NO',
    'SI',
    'SI',
    'NO',
    'SAFECITYSWPOE',
    NULL,
    NULL,
    'NO',
    'PRECIO=20',
    'S',
    'Pendiente',
    'SEGURIDAD ELECTRONICA Y FISICA',
    '0',
    '0',
    NULL,
    NULL,
    'NO',
    'COU VENTA DE EQUIPOS',
    'COLLABORATION',
    NULL,
    NULL
);

--INSERT CARACTERISTICAS
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_SERVICIOS_GPON_SAFECITY' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SERVICIO_ADICIONAL' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'ID_DETALLE_TAREA_INSTALACION' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_RED' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'MAC CLIENTE' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'PUERTO_ONT' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT CARACTERISTICA 'RELACION_CAMARA_PRINCIPAL'
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
    'RELACION_CAMARA_PRINCIPAL',
    'N',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE NOMBRE_TECNICO = 'SAFECITYSWPOE' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_CAMARA_PRINCIPAL' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--DEFINIR TIPO DE RED - SERVICIO SW POE
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
            NOMBRE_TECNICO='SAFECITYSWPOE'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            NOMBRE_TECNICO='SAFECITYSWPOE'
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

--PRODUCTOS NO PERMITIDOS EN TIPO RED MPLS - SERVICIO SW POE
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
            NOMBRE_TECNICO='SAFECITYSWPOE'
            AND empresa_cod = 10
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            NOMBRE_TECNICO='SAFECITYSWPOE'
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

--Ingresamos el detalle para el producto requerido datos safe city para el producto SW POE
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
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
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
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' ),
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

-- SERVICIO ADICIONAL SW POE
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
        '[SWITCH PoE GPON]',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' ),
        'SWITCH PoE GPON',
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

-- DETALLE PARAMETROS PARA SW POE SAFECITY
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
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'COORDINAR_OBSERVACION',
        'SWITCH PoE SAFECITY',
        'SWITCH PoE SAFECITY',
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
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
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

--Ingresamos el detalle de la actualización del estado de la tarea del servicio SW POE
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
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
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

-- DETALLE PARAMETRO PARA EL SW POE
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
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
        'PRODUCTO_ADICIONAL_SW_POE',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' ),
        'SWITCH PoE GPON',
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

INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,  
    valor2,
    valor3,
    valor5,
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
    (select id_producto from db_comercial.admi_producto where NOMBRE_TECNICO = 'SAFECITYSWPOE'),
    (select id_producto from db_comercial.admi_producto where NOMBRE_TECNICO = 'DATOS SAFECITY'),
    'AsignadoTarea',
    'SWITCHPOE',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

--Creacion de Proceso: SOLICITAR NUEVO SERVICIO SAFE CITY
INSERT INTO DB_SOPORTE.ADMI_PROCESO VALUES (DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,NULL,'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE','TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE',null,'Activo','facaicedo',SYSDATE,'facaicedo',SYSDATE,NULL,NULL);

--Asociar Proceso a la empresa.
INSERT INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA VALUES (DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,(SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE'),'10','Activo','facaicedo',SYSDATE);

--Creacion de Tarea: FIBRA: INSTALACION CAMARAS
INSERT INTO DB_SOPORTE.ADMI_TAREA VALUES (DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,(SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE'),NULL,NULL,NULL,1,0,'INSTALACION SWITCH PoE GPON','INSTALACION SWITCH PoE GPON',1,'MINUTOS',1,1,'Activo','facaicedo',SYSDATE,'facaicedo',SYSDATE,NULL,NULL,NULL,'N','N');

--Ingresamos el detalle para el proceso y tarea de instalación de SW POE
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
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' ),
        'TAREAS DE ELECTRICO - PROYECTOS SAFE CITY SWITCH PoE',
        'INSTALACION SWITCH PoE GPON',
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

--Ingresamos el detalle de los modelos de SW POE permitidos
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
        'MODELOS_SWITCH_POE',
        'DS-3E0109P-E/M',
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

--Ingresamos el detalle de los puertos del ONT permitidos para el producto SW POE
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'
            AND ESTADO = 'Activo'
        ),
        'PUERTOS_ONT_PERMITIDOS_POR_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' ),
        '2',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'
            AND ESTADO = 'Activo'
        ),
        'PUERTOS_ONT_PERMITIDOS_POR_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' ),
        '3',
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

--Ingresamos el detalle de parametro para no ingresar fibra en los productos SW POE GPON
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'VISUALIZAR_PANTALLA_FIBRA'
            AND ESTADO = 'Activo'
        ),
        'Productos que deben visualizar la pantalla de fibra',
        ( SELECT DESCRIPCION_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE NOMBRE_TECNICO='SAFECITYSWPOE' AND ESTADO = 'Activo' ),
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

--ACTUALIZAR PARAMETRO PARA AGREGAR EL PORCENTAJE PARA LOS SERVICIOS SW POE GPON
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR2 = PDE.VALOR2||',70' WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'IDS_PROGRESOS_TAREAS'
    ) AND DET.VALOR1 = 'PROG_INSTALACION_TN_MATERIALES'
);

COMMIT;
/
