
--INSERT PRODUCTO
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO 
        SELECT DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL,
            AP.EMPRESA_COD,
            'MOBBUS',
            'MOBILE BUS',
            AP.FUNCION_COSTO,
            AP.INSTALACION,
            AP.ESTADO,
            SYSDATE,
            'facaicedo',
            AP.IP_CREACION,
            AP.CTA_CONTABLE_PROD,
            AP.CTA_CONTABLE_PROD_NC,
            'SI',
            'NO',
            AP.REQUIERE_PLANIFICACION,
            AP.REQUIERE_INFO_TECNICA,
            'SEG_VEHICULO',
            AP.CTA_CONTABLE_DESC,
            AP.TIPO,
            AP.ES_CONCENTRADOR,
            'PRECIO=29.99',
            AP.SOPORTE_MASIVO,
            'PrePlanificada',
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

--INSERT CARACTERISTICA 'PLACA'
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
    'PLACA',
    'T',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Relacionar Proyecto'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Relacionar Proyecto' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'PLACA'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'PLACA' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--INSERT CARACTERISTICA 'COOPERATIVA'
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
    'COOPERATIVA',
    'T',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT CARACTERISTICA 'TIPO TRANSPORTE'
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
    'TIPO TRANSPORTE',
    'S',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'COOPERATIVA'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'COOPERATIVA' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'TIPO TRANSPORTE'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'TIPO TRANSPORTE' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--INSERT CARACTERISTICA 'NUMERO CELULAR'
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
    'NUMERO CELULAR',
    'T',
     SYSDATE,
    'facaicedo',
    'TECNICO',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'NUMERO CELULAR'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'NUMERO CELULAR' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Registramos los tipos de transportes con los que podrá elegir al momento de crear un servicio.
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
    'PROD_TIPO TRANSPORTE',
    'PROD_TIPO TRANSPORTE',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'METROVIA',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'AUTOBUS',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'CAMIONETA',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'AUTO',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'TAXI',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'EXPRESO ESCOLAR',
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
            nombre_parametro = 'PROD_TIPO TRANSPORTE'
    ),
    'PROD_TIPO TRANSPORTE',
    'FURGONETA',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--Creacion de Proceso: SOLICITAR NUEVO SERVICIO MOBILE BUS
INSERT INTO DB_SOPORTE.ADMI_PROCESO VALUES (DB_SOPORTE.SEQ_ADMI_PROCESO.NEXTVAL,NULL,'SOLICITAR NUEVO SERVICIO MOBILE BUS','PROCESO PARA SERVICIO WIFI SAFECITY',null,'Activo','facaicedo',SYSDATE,'facaicedo',SYSDATE,NULL,NULL);

--Asociar Proceso a la empresa.
INSERT INTO DB_SOPORTE.ADMI_PROCESO_EMPRESA VALUES (DB_SOPORTE.SEQ_ADMI_PROCESO_EMPRESA.NEXTVAL,(SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO MOBILE BUS'),'10','Activo','facaicedo',SYSDATE);

--Creacion de Tarea: INSTALACION MOBILE BUS
INSERT INTO DB_SOPORTE.ADMI_TAREA VALUES (DB_SOPORTE.SEQ_ADMI_TAREA.NEXTVAL,(SELECT ID_PROCESO FROM DB_SOPORTE.ADMI_PROCESO WHERE NOMBRE_PROCESO = 'SOLICITAR NUEVO SERVICIO MOBILE BUS'),NULL,NULL,NULL,1,0,'INSTALACION MOBILE BUS','TAREA DE INSTALACION DE MOBILE BUS',1,'MINUTOS',1,1,'Activo','facaicedo',SYSDATE,'facaicedo',SYSDATE,NULL,NULL,NULL,'N','N');

--Ingresamos el detalle para el proceso y tarea de instalación de MobileBus
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
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'SOLICITAR NUEVO SERVICIO MOBILE BUS',
        'INSTALACION MOBILE BUS',
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

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'PARAMETROS_SEG_VEHICULOS'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB
(
        ID_PARAMETRO,
        NOMBRE_PARAMETRO,
        DESCRIPCION,
        MODULO,
        PROCESO,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
        'PARAMETROS_SEG_VEHICULOS',
        'Lista de los parámetros para los productos de seguridad de los vehículos(MobileBus).',
        'TECNICO',
        'SEGURIDAD',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
--Ingresamos los detalle de parámetros para los productos de seguridad de los vehículos(MobileBus).
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        OBSERVACION,
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'VALIDAR_CARACTERISTICA_UNICA',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'PLACA',
        'La característica PLACA(:VALOR) es única ya está ingresado en otro servicio con estado :ESTADO.',
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Eliminado',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Cancel',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Rechazada',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
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
        IP_CREACION
)
VALUES
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
            SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ESTADOS SERVICIOS NO PERMITIDOS PARA FLUJO',
        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
        'Anulado',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'DVR',
        'DVR',
        'N',
        '0',
        'Dvr',
        'DVR',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'CAMARA;CAMARA IP',
        'CAMARA FRONTAL',
        'S',
        '1',
        'CamaraFrontal',
        'Camaras',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'CAMARA;CAMARA IP',
        'CAMARA CONDUCTOR',
        'S',
        '2',
        'CamaraConductor',
        'Camaras',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'CAMARA;CAMARA IP',
        'CAMARA PASAJEROS',
        'S',
        '3',
        'CamaraPasajeros',
        'Camaras',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'CAMARA;CAMARA IP',
        'CAMARA POSTERIOR',
        'S',
        '4',
        'CamaraPosterior',
        'Camaras',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'DISCO DURO',
        'DISCO DURO',
        'N',
        '5',
        'DiscoDuro',
        'Disco Duro',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS_SEG_VEHICULOS'
            AND ESTADO = 'Activo'
        ),
        'ELEMENTOS_PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'CHIP',
        'CHIP GSM',
        'N',
        '6',
        'ChipGsm',
        'CHIP',
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

--crear parametro para los nombres tecnicos permitidos con etiquetas definidas
INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'ETIQUETA_FOTO_NOMBRE_TECNICO',
    'PARAMETRO DE LOS NOMBRES TECNICOS DE PRODUCTOS PERMITIDOS EN LAS ETIQUETAS DE LAS FOTOS',
    'MOVIL',
    NULL,
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO_NOMBRE_TECNICO'
    ),
    'NOMBRE TECNICO DEL PRODUCTO PERMITIDO EN LAS ETIQUETAS PERSONALIZADAS',
    ( SELECT NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    NULL,
    NULL,
    NULL,
    'Activo',
    'facaicedo',
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

--Ingreso del admin progreso de la tarea
INSERT INTO DB_SOPORTE.ADMI_PROGRESOS_TAREA 
(
    ID_PROGRESOS_TAREA,
    CODIGO_TAREA ,
    NOMBRE_TAREA,
    DESCRIPCION_TAREA,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION,
    USR_ULT_MOD,
    FE_ULT_MOD
) 
values 	
(
    DB_SOPORTE.SEQ_ADMI_PROGRESOS_TAREA.NEXTVAL,
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), -- CODIGO_TAREA 
    'INSTALACION_MOBILE_BUS', 	                        -- NOMBRE_TAREA 
    'Tareas de instalación del producto Mobile Bus', 	-- DESCRIPCION_TAREA 
    'Activo', 					                        -- ESTADO
    'facaicedo', 				                        -- USR_CREACION
    SYSDATE, 					                        -- FE_CREACION 
    '127.0.0.1', 				                        -- IP_CREACION 
    NULL, 						                        -- USR_ULT_MOD 
    NULL 						                        -- FE_ULT_MOD
);

--Ingreso del tipo progreso FOTO_DESPUES
INSERT INTO DB_SOPORTE.ADMI_TIPO_PROGRESO 
(
    ID_TIPO_PROGRESO, 
    CODIGO, 
    NOMBRE_TIPO_PROGRESO, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION
) 
VALUES 
(
    DB_SOPORTE.SEQ_ADMI_TIPO_PROGRESO.NEXTVAL,
    'FOTO_DESPUES',
    'Foto Despues',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);

--Ingreso de los progresos porcentajes de la tarea
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FORMULARIO_EPP'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '1', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '5', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'SEGUIMIENTO'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '2', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FOTO'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '3', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'INGRESO_MATERIALES'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '4', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '30', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'ACTIVACION_SERVICIO'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '5', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '10', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FOTO_DESPUES'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '6', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '15', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'ACTAS'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '7', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '5', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'ENCUESTAS'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '8', 
    '10'
);
INSERT INTO DB_SOPORTE.INFO_PROGRESO_PORCENTAJE 
(
    ID_PROGRESO_PORCENTAJE, 
    PORCENTAJE, 
    TIPO_PROGRESO_ID, 
    TAREA_ID, 
    ESTADO, 
    USR_CREACION, 
    FE_CREACION, 
    IP_CREACION, 
    FE_ULT_MOD, 
    ORDEN, 
    EMPRESA_ID
) 
VALUES 
(
    DB_SOPORTE.SEQ_INFO_PROGRESO_PORCENTAJE.NEXTVAL, 
    '5', 
    (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'FINALIZAR'), 
    (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS'), 
    'Activo', 
    'facaicedo', 
    SYSDATE, 
    '127.0.0.1', 
    SYSDATE, 
    '9', 
    '10'
);

--Ingreso el detalle para los dispositivos sin mac
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    '1563',
    'EQUIPO PERMITIDO QUE NO REQUIERE MAC PARA REALIZAR UNA INSTALACION O CAMBIO DE EQUIPO',
    'CHIP',
    NULL,
    NULL,
    NULL,
    'Activo',
    'facaicedo',
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
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    '1563',
    'EQUIPO PERMITIDO QUE NO REQUIERE MAC PARA REALIZAR UNA INSTALACION O CAMBIO DE EQUIPO',
    'DVR',
    NULL,
    NULL,
    NULL,
    'Activo',
    'facaicedo',
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
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    '1563',
    'EQUIPO PERMITIDO QUE NO REQUIERE MAC PARA REALIZAR UNA INSTALACION O CAMBIO DE EQUIPO',
    'DISCO DURO',
    NULL,
    NULL,
    NULL,
    'Activo',
    'facaicedo',
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

-- INGRESO LOS DETALLES DE LA CABECERA 'VISUALIZAR_PANTALLA_FIBRA'
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
        'MOBILE BUS',
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

--ACTUALIZAR PARAMETRO PARA AGREGAR EL PORCENTAJE PARA LOS SERVICIOS SEG VEHICULO
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET PDE SET PDE.VALOR2 = PDE.VALOR2||','||
    (SELECT ID_PROGRESO_PORCENTAJE FROM DB_SOPORTE.INFO_PROGRESO_PORCENTAJE
        WHERE TIPO_PROGRESO_ID = (SELECT ID_TIPO_PROGRESO FROM DB_SOPORTE.ADMI_TIPO_PROGRESO atp WHERE CODIGO = 'INGRESO_MATERIALES')
        AND TAREA_ID = (SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'INSTALACION MOBILE BUS')
        AND ESTADO = 'Activo')
  WHERE PDE.ID_PARAMETRO_DET = (
    SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET WHERE DET.PARAMETRO_ID = (
      SELECT CAB.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB CAB WHERE CAB.NOMBRE_PARAMETRO = 'IDS_PROGRESOS_TAREAS'
    ) AND DET.VALOR1 = 'PROG_INSTALACION_TN_MATERIALES'
  );

--Registramos los parámetro para validar las características del producto en la orden de servicio por regex.
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
    'VALIDAR_PRODUCTO_CARACTERISTICA_REGEX',
    'VALIDAR_PRODUCTO_CARACTERISTICA_REGEX',
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
    valor3,
    valor4,
    observacion,
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
            nombre_parametro = 'VALIDAR_PRODUCTO_CARACTERISTICA_REGEX'
    ),
    'Parámetro para validar las características del producto en la orden de servicio por regex.',
    '{"METROVIA":{"regex":"/^[A-Z]{1}[0-9]{4,8}$/","mensaje":"P1234"},"default":{"regex":"/^[A-Z]{3}[0-9]{3,4}$/","mensaje":"GRW2354"}}',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'MOBILE BUS' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    'PLACA',
    'TIPO TRANSPORTE',
    'La característica PLACA(:VALOR) no es válida, por favor ingrese una correcta, Eje.(:MENSAJE).',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;
/
