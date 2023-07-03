/**
 * DEBE EJECUTARSE EN DB_COMERCIAL
 * Script para crear caracteristica elemento adiconales en el nodo y cliente servicios safe city
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 
 * @since 9-1-2023 - Versión Inicial.
 */
 
-- INGRESO DE LA CARACTERISTICA PARA EQUIPOS ADICIONALES EN CLIENTE
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
    'ELEMENTO_ADD_CLIENTE_ID',
    'S',
    'Activo',
    SYSDATE,
    'mcarpio',
    NULL,
    NULL,
    'COMERCIAL'
);

-- INGRESO DE LA CARACTERISTICA PARA EQUIPOS ADICIONALES EN CLIENTE
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
    'ELEMENTO_ADD_NODO_ID',
    'S',
    'Activo',
    SYSDATE,
    'mcarpio',
    NULL,
    NULL,
    'COMERCIAL'
);

--DATOS GPON
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'DATOS SAFECITY'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_CLIENTE_ID')),
      SYSDATE,SYSDATE,'mcarpio','mcarpio','Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'DATOS SAFECITY'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_NODO_ID')),
      SYSDATE,SYSDATE,'mcarpio','mcarpio','Activo','NO');

--WIFI GPON
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'WIFI GPON' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'SAFECITYWIFI'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_CLIENTE_ID')),
      SYSDATE,SYSDATE,'mcarpio','mcarpio','Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'WIFI GPON' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'SAFECITYWIFI'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_NODO_ID')),
      SYSDATE,SYSDATE,'mcarpio','mcarpio','Activo','NO');

--CAMARAS
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'SAFECITYDATOS'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_CLIENTE_ID')),
      SYSDATE,NULL,'mcarpio',NULL,'Activo','NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'SAFECITYDATOS'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_NODO_ID')),
      SYSDATE,NULL,'mcarpio',NULL,'Activo','NO');

--SW POE
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SWITCH PoE GPON' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'SAFECITYSWPOE'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_CLIENTE_ID')),
      SYSDATE,NULL,'mcarpio',NULL,'Activo','NO');


INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA
      VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SWITCH PoE GPON' AND EMPRESA_COD = 10 AND ESTADO = 'Activo' AND NOMBRE_TECNICO = 'SAFECITYSWPOE'),
      (SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA IN ('ELEMENTO_ADD_NODO_ID')),
      SYSDATE,NULL,'mcarpio',NULL,'Activo','NO');


/**
 * DEBE EJECUTARSE EN DB_GENERAL
 * Script para crear cabecera y detalle de parametros para tipos de elemento cliente y nodo
 * @author Manuel Carpio <mcarpio@telconet.ec>
 * @version 1.0 
 * @since 9-1-2023 - Versión Inicial.
 */
 
-------------------------------------------------------- PARAMETRO CABEZERA ---------------------------------------------------------------------------
--INGRESO DE PARAMETROS 

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
    'ELEMENTOS_ADICIONALES_CLIENTE_NODO',
    'Parametros para agregar elemento adicionale en el cliente y nodo en activacion',
    'TECNICO',
    'Activo',
    'mcarpio',
    SYSDATE,
    '127.0.0.1'
);

---------------------------------------------------------------DETALLE ELEMENTO ADD CLIENTE------------------------------------------------------------------
--ELEMENTO ADICIONALE EN CLIENTE

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
            nombre_parametro = 'ELEMENTOS_ADICIONALES_CLIENTE_NODO' AND estado = 'Activo'
    ),
    'ELEMENTO_ADICIONAL_CLIENTE',
    'UPS',
    '',
    '',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
    SYSDATE,
    '127.0.0.1',
    10
);

---------------------------------------------------------------DETALLE ELEMENTO ADD NODO------------------------------------------------------------------

--ELEMENTO ADICIONALE EN NODO

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
            nombre_parametro = 'ELEMENTOS_ADICIONALES_CLIENTE_NODO' AND estado = 'Activo'
    ),
    'ELEMENTO_ADICIONAL_NODO',
    'TRANSCEIVER',
    '',
    '',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    valor4,
    valor5,
    valor6,
    valor7,
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
            nombre_parametro = 'ELEMENTOS_ADICIONALES_CLIENTE_NODO' AND estado = 'Activo'
    ),
    'ELEMENTO_ADICIONAL_NODO',
    'INTERFACES',
    '',
    '',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
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
    valor4,
    valor5,
    valor6,
    valor7,
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
            nombre_parametro = 'ELEMENTOS_ADICIONALES_CLIENTE_NODO' AND estado = 'Activo'
    ),
    'ELEMENTO_ADICIONAL_NODO',
    'ROUTER',
    '',
    '',
    '',
    '',
    '', 
    '', 
    '',
    'Activo',
    'mcarpio',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;
/