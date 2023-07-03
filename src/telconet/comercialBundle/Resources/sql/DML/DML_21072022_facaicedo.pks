
--actualizo el estado a eliminado el parametro de estado repetido de AsignadoTarea
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET SET ESTADO = 'Eliminado' WHERE ID_PARAMETRO_DET = 17861;

--SE ACTUALIZA EL PRODUCTO A REQUERIR INFORMACION TECNICA Y PLANIFICACION
UPDATE DB_COMERCIAL.ADMI_PRODUCTO SET REQUIERE_INFO_TECNICA = 'SI', REQUIERE_PLANIFICACION = 'SI' WHERE ID_PRODUCTO = 1452;

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Cantidad Camaras'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Cantidad Camaras' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Precio Camaras'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Precio Camaras' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
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
      AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Cantidad Camaras' AND ESTADO = 'Activo')),
    'servicios-add-null-internetsafe',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
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
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Cantidad Camaras' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Precio Camaras' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo'),
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

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'RELACION_SERVICIOS_GPON_SAFECITY'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_SERVICIOS_GPON_SAFECITY' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'TIPO_RED'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_RED' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'ID_DETALLE_TAREA_INSTALACION'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'ID_DETALLE_TAREA_INSTALACION' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'SERVICIO_EN_SWITCH_POE'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SERVICIO_EN_SWITCH_POE' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'SERVICIO_ADICIONAL'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SERVICIO_ADICIONAL' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'MIGRACION_SWITCH_POE'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'MIGRACION_SWITCH_POE' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'CLAVE_CAMARA'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CLAVE_CAMARA' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'CODEC'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'CODEC' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'FPS'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'FPS' AND ESTADO = 'Activo' ),
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
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'MAC CLIENTE' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'RESOLUCION'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'RESOLUCION' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'URL_CAMARA'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'URL_CAMARA' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'USUARIO_CAMARA'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'USUARIO_CAMARA' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

-- INGRESO LOS DETALLES DE LA CABECERA 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
-- DETALLE CAMARA
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
          WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
        'AGREGAR_SERVICIO_ADICIONAL',
        '[Cantidad Camaras]',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'SAFE ANALYTICS CAM GPON',
        'RELACION_SERVICIOS_GPON_SAFECITY',
        'GPON',
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

--actualizar producto id en el parámetro de máximo números de cámaras
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR3 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
               WHERE NOMBRE_TECNICO = 'SAFECITYDATOS' AND ESTADO = 'Activo' )
WHERE ID_PARAMETRO_DET = (
  SELECT DET.ID_PARAMETRO_DET FROM DB_GENERAL.ADMI_PARAMETRO_DET DET
  INNER JOIN DB_GENERAL.ADMI_PARAMETRO_CAB CAB ON CAB.ID_PARAMETRO = DET.PARAMETRO_ID
  WHERE CAB.NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'
  AND DET.DESCRIPCION = 'MAXIMO_CAMARAS_POR_PUNTO'
  AND DET.ESTADO = 'Activo'
);

--ingresar parámetro de máximo números de cámaras
INSERT INTO db_general.admi_parametro_det (
    id_parametro_det,
    parametro_id,
    descripcion,
    valor1,
    valor2,
    valor3,
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
    'MAXIMO_CAMARAS_POR_PUNTO',
    '6',
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'INTERNET VPNoGPON' AND ESTADO = 'Activo'),
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo'),
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

--actualizar el parámetro cantidad de cámaras por el producto id
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR2 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
               WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo' )
WHERE PARAMETRO_ID = (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROD_Cantidad Camaras');

--detalle del parámetro cantidad de cámaras por el producto id
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
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '1',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
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
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '2',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
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
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '3',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
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
            nombre_parametro = 'PROD_Cantidad Camaras'
    ),
    'PROD_Cantidad Camaras',
    '4',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
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

--
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
    valor7,
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
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'SAFE ANALYTICS CAM'),
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'INTERNET VPNoGPON'),
    'AsignadoTarea',
    'STANDARD',
    'CAMARAVPN',
    'MIGRAR',
    'PRODUCTO_SECURITY_NG_FIREWALL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

-- DETALLE PARAMETROS PARA COORDINAR LA ORDEN SERVICIO AUTOMATICA DEL PRODCUTO SAFE ANALYTICS CAM
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'COORDINAR_OBSERVACION',
        'SAFE ANALYTICS CAM',
        'SAFE ANALYTICS CAM',
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

--Ingresamos el detalle para el proceso y tarea de instalación de la camara internet vpnogpon
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
        'TAREA DE INSTALACION DEL SERVICIO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'SOLICITAR NUEVO SERVICIO SAFE CITY',
        'FIBRA: INSTALACION CAMARAS',
        ( SELECT ID_TAREA FROM DB_SOPORTE.ADMI_TAREA WHERE NOMBRE_TAREA = 'FIBRA: INSTALACION CAMARAS' ),
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

--Ingresamos el detalle para no mostrar el producto adicional en la factibilidad y coordinacion con el internet vpnogpon
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamos el detalle de la actualización del estado de la tarea del servicio SAFE ANALYTICS CAM
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'Pausada',
        'Se cambió el estado de la tarea a pausada, hasta que termine la tarea de instalación del servicio principal INTERNET VPNoGPON',
        'Asignada',
        'Se cambió el estado de la tarea a asignada, porque el servicio principal INTERNET VPNoGPON ya esta activado.',
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

--Ingresamos el detalle para el nombre del uso de la subred del producto 'SAFE ANALYTICS CAM'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR6,
        VALOR7,
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'SAFECITYCAMVPN',
        'NO',
        'SAFECITYCAMVPN',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

-- DETALLE PARAMETROS PARA LOS PARAMETROS DE 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Lista de parámetros para la asignación de recursos de red.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'MASCARA',
        '255.255.255.248',
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
            WHERE NOMBRE_PARAMETRO = 'PARAMETROS MASCARA Y PREFIJOS DE SUBREDES PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Lista de parámetros para la asignación de recursos de red.',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'PREFIJOS',
        '10.247',
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

-- DETALLE PARAMETRO PARA EL PRODUCTO SECURITY_NG_FIREWALL
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
(
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR5,
        VALOR6,
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
        'PRODUCTO_SECURITY_NG_FIREWALL',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'PRODUCTO_REQUERIDO',
        'PRODUCTO_INTERNET_VPNoGPON',
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

-- DETALLE PARAMETRO PARA EL PRODUCTO PRINCIPAL INTERNET VPN GPON
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
        'PRODUCTO_INTERNET_VPNoGPON',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
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

-- ACTUALIZAR PARAMETRO PARA LA CANTIDAD DE PUERTOS DISPONIBLES DEFAULT POR ONT DEL PRODUCTO DATOS SAFECITY
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET
SET VALOR3 = ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
               WHERE NOMBRE_TECNICO = 'DATOS SAFECITY' AND ESTADO = 'Activo' )
WHERE
    VALOR1 = 'PUERTO_DISPONIBLE_DEFAULT_ONT'
    AND PARAMETRO_ID = ( SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY' );

-- DETALLE PARAMETRO PARA LA CANTIDAD DE PUERTOS DISPONIBLES DEFAULT POR ONT DEL PRODUCTO INTERNET VPN GPON
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        'PUERTO_DISPONIBLE_DEFAULT_ONT',
        '4',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
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

--Ingresamos el detalle de parámetro para definir el tipo de red al SAFE ANALYTICS CAM
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Definir el tipo de red a los productos sin ingreso del tipo de red en la orden de servicio.',
        'TIPO_RED_NO_VISIBLE_ORDEN_SERVICIO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
            WHERE DESCRIPCION_PRODUCTO = 'SAFE ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'GPON',
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

--INSERT CARACTERISTICA 'Cantidad IPs'
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
    'Cantidad IPs',
    'S',
     SYSDATE,
    'facaicedo',
    'COMERCIAL',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Cantidad IPs'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Cantidad IPs' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--detalle class relacion de la caracteristica 'Cantidad IPs'
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
      AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Cantidad IPs' AND ESTADO = 'Activo')),
    'servicios-add-null-internetsafe',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);

--Registramos las cantidad de ips con los que podrá elegir al momento de crear un servicio.
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
    'PROD_Cantidad IPs',
    'PROD_Cantidad IPs',
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
            nombre_parametro = 'PROD_Cantidad IPs'
    ),
    'PROD_Cantidad IPs',
    '1',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--detalle del default valor de la caracteristica 'PROD_Cantidad IPs'
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
    'PROD_Cantidad IPs',
    '0',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--INSERT CARACTERISTICA 'Security NG Firewall'
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
    'Security NG Firewall',
    'S',
     SYSDATE,
    'facaicedo',
    'COMERCIAL',
    'Activo'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'Security NG Firewall'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'Security NG Firewall' AND ESTADO = 'Activo' ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'SI'
);

--Registramos las cantidad de ips con los que podrá elegir al momento de crear un servicio.
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
    'PROD_Security NG Firewall',
    'PROD_Security NG Firewall',
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
            nombre_parametro = 'PROD_Security NG Firewall'
    ),
    'PROD_Security NG Firewall',
    '1',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--detalle del default valor de la caracteristica 'PROD_Security NG Firewall'
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
    'PROD_Security NG Firewall',
    '0',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--detalle class relacion de la caracteristica 'Security NG Firewall'
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
      AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
      AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Security NG Firewall' AND ESTADO = 'Activo')),
    'servicios-add-null-internetsafe',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1'
);

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'SEC CAPACIDAD(Kbps)'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SEC CAPACIDAD(Kbps)' AND ESTADO = 'Activo' ),
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
            WHERE NOMBRE_PARAMETRO = 'PRODUCTO_CARACTERISTICA_RELACION_PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Detalles de las relaciones de los productos características.',
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Security NG Firewall' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SEC CAPACIDAD(Kbps)' AND ESTADO = 'Activo')),
        'SEC CAPACIDAD(Kbps)',
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

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'SEC PLAN NG FIREWALL'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SEC PLAN NG FIREWALL' AND ESTADO = 'Activo' ),
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
            WHERE NOMBRE_PARAMETRO = 'PRODUCTO_CARACTERISTICA_RELACION_PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Detalles de las relaciones de los productos características.',
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Security NG Firewall' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SEC PLAN NG FIREWALL' AND ESTADO = 'Activo')),
        'SEC PLAN NG FIREWALL',
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

--INSERT ADMI_PRODUCTO_CARACTERISTICA 'SEC MODELO FIREWALL'
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA VALUES
(
    DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA
      WHERE DESCRIPCION_CARACTERISTICA = 'SEC MODELO FIREWALL' AND ESTADO = 'Activo' ),
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
            WHERE NOMBRE_PARAMETRO = 'PRODUCTO_CARACTERISTICA_RELACION_PRODUCTO'
            AND ESTADO = 'Activo'
        ),
        'Detalles de las relaciones de los productos características.',
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'Security NG Firewall' AND ESTADO = 'Activo')),
        (SELECT ID_PRODUCTO_CARACTERISITICA FROM DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA WHERE ESTADO = 'Activo'
        AND PRODUCTO_ID = (SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo')
        AND CARACTERISTICA_ID = (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SEC MODELO FIREWALL' AND ESTADO = 'Activo')),
        'SEC MODELO FIREWALL',
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

-- INGRESO LOS DETALLES DE LA CABECERA 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
-- DETALLE IP INTERNET VPNoGPON
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
          WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
        'AGREGAR_SERVICIO_ADICIONAL',
        '[Cantidad IPs]',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'IP INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
        'IP INTERNET VPNoGPON',
        'RELACION_INTERNET_VPNoGPON',
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
-- DETALLE SECURITY NG FIREWALL
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
            WHERE NOMBRE_PARAMETRO = 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
            AND ESTADO = 'Activo'
        ),
        'Lista de la configuración del producto',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
        'AGREGAR_SERVICIO_ADICIONAL',
        '[Security NG Firewall]',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
        'SECURITY NG FIREWALL',
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

--Registramos los productos con su última milla.
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
    'PRODUCTO_AUTOMATICO_ULTIMA_MILLA',
    'Parámetro para relacionar los productos generados automáticamente con su última milla diferente al del servicio principal.',
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
            nombre_parametro = 'PRODUCTO_AUTOMATICO_ULTIMA_MILLA'
    ),
    'Detalle del id del producto con el id de la última milla(si el valor es null no posee).',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--detalle del default valor de la caracteristica 'PROD_SEC CAPACIDAD(Kbps)'
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
    'PROD_SEC CAPACIDAD(Kbps)',
    '20480',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--Registramos las características del producto principal a los productos adicionales.
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
    'CARACTERISTICA_PROD_INGRESAR_PRODUCTO',
    'Parámetro para ingresar las características del producto principal a los productos adicionales.',
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
            nombre_parametro = 'CARACTERISTICA_PROD_INGRESAR_PRODUCTO'
    ),
    'Detalle de parámetro del id del producto principal y el id producto adicional con la característica.',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    'FACTURACION_UNICA',
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
    valor3,
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
            nombre_parametro = 'CARACTERISTICA_PROD_INGRESAR_PRODUCTO'
    ),
    'Detalle de parámetro del id del producto principal y el id producto adicional con la característica.',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    'RENTA_MENSUAL',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--Registramos los productos requeridos en la activación de los servicios
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
    'PRODUCTO_REQUERIDO_ACTIVACION',
    'Parámetro para validar el producto requerido en la activación del servicio.',
    'TECNICO',
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
            nombre_parametro = 'PRODUCTO_REQUERIDO_ACTIVACION'
    ),
    'Detalle de parámetro del id del producto y el id producto requerido en la activación.',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'SECURITY NG FIREWALL' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'IP INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    'ONT',
    'Se debe activar el servicio IP INTERNET VPNoGPON.',
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
            nombre_parametro = 'PRODUCTO_REQUERIDO_ACTIVACION'
    ),
    'Detalle de parámetro del id del producto y el id producto requerido en la activación.',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'IP INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND ESTADO = 'Activo' ),
    'Se debe activar el servicio INTERNET VPNoGPON.',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

--Registramos los productos que permiten el cambio de precio de instalacion en la orden de servicio
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
    'PERMITE_CAMBIO_PRECIO_INSTALACION',
    'Parámetro para los productos que permiten el cambio de precio de instalación en la orden de servicio.',
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
            nombre_parametro = 'PERMITE_CAMBIO_PRECIO_INSTALACION'
    ),
    'Detalle de parámetro del id del producto y el valor S para el cambio de precio de instalación.',
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
      WHERE DESCRIPCION_PRODUCTO = 'INTERNET VPNoGPON' AND EMPRESA_COD = '10' AND ESTADO = 'Activo' ),
    'S',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;
/
