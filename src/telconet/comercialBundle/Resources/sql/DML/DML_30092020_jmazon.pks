--UPDATE NOMBRE TECNICO PARAMOUNT
UPDATE db_comercial.admi_producto
SET nombre_tecnico = 'PARAMOUNT'
WHERE codigo_producto = 'PA01';

--UPDATE NOMBRE TECNICO NOGGIN
UPDATE db_comercial.admi_producto
SET nombre_tecnico = 'NOGGIN'
WHERE codigo_producto = 'NO01';

--INSERT EN ADMI_CARACTERISTICA PARAMOUNT
--PARAMOUNT
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'PARAMOUNT',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'COMERCIAL'
);

--SSID_PARAMOUNT
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'SSID_PARAMOUNT',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'TECNICA'
);

--USUARIO_PARAMOUNT
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'USUARIO_PARAMOUNT',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'TECNICA'
);

--PASWORD_PARAMOUNT
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'PASSWORD_PARAMOUNT',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'TECNICA'
);

--MIGRACION-PARAMOUNT
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'MIGRADO_PARAMOUNT',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'COMERCIAL'
);

--INSERT ADMI CARACTERISTICA NOGGIN
--NOGGIN
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'NOGGIN',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'COMERCIAL'
);

--SSID_NOGGIN
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'SSID_NOGGIN',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'TECNICA'
);

--USUARIO_NOGGIN
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'USUARIO_NOGGIN',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'TECNICA'
);

--PASWORD_NOGGIN
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'PASSWORD_NOGGIN',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'TECNICA'
);

--MIGRACION-NOGGIN
INSERT INTO db_comercial.admi_caracteristica (
    id_caracteristica,
    descripcion_caracteristica,
    tipo_ingreso,
    estado,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    tipo
) VALUES (
    db_comercial.seq_admi_caracteristica.nextval,
    'MIGRADO_NOGGIN',
    'T',
    'Activo',
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'COMERCIAL'
);

-- INSERT EN ADMI_PRODUCTO_CARACTERISTICA
--PARAMOUNT 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'PA01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'PARAMOUNT'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--SSID_PARAMOUNT 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'PA01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'SSID_PARAMOUNT'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--USUARIO_PARAMOUNT
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'PA01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'USUARIO_PARAMOUNT'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--PASSWORD_PARAMOUNT 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'PA01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'PASSWORD_PARAMOUNT'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--MIGRADO_PARAMOUNT 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'PA01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'MIGRADO_PARAMOUNT'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--INSERT ADMI PRODUCTO CARACTERISTICA NOGGIN
--NOGGIN 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'NO01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'NOGGIN'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--SSID_NOGGIN 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'NO01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'SSID_NOGGIN'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--USUARIO_NOGGIN 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'NO01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'USUARIO_NOGGIN'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--PASSWORD_NOGGIN 
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'NO01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'PASSWORD_NOGGIN'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--MIGRADO_NOGGIN
INSERT INTO db_comercial.admi_producto_caracteristica (
    id_producto_caracterisitica,
    producto_id,
    caracteristica_id,
    fe_creacion,
    fe_ult_mod,
    usr_creacion,
    usr_ult_mod,
    estado,
    visible_comercial
) VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'NO01'
            AND estado = 'Activo'
    ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
                descripcion_caracteristica = 'MIGRADO_NOGGIN'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'NO'
);

--IMPUESTO PARAMOUNT
INSERT INTO db_comercial.info_producto_impuesto (
    id_producto_impuesto,
    producto_id,
    impuesto_id,
    porcentaje_impuesto,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    estado
) VALUES (
    db_comercial.seq_info_producto_impuesto.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'PA01'
            AND estado = 'Activo'
    ),
    1,
    12,
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'Activo'
);

--IMPUESTO NOGGIN
INSERT INTO db_comercial.info_producto_impuesto (
    id_producto_impuesto,
    producto_id,
    impuesto_id,
    porcentaje_impuesto,
    fe_creacion,
    usr_creacion,
    fe_ult_mod,
    usr_ult_mod,
    estado
) VALUES (
    db_comercial.seq_info_producto_impuesto.nextval,
    (
        SELECT
            id_producto
        FROM
            db_comercial.admi_producto
        WHERE
                codigo_producto = 'NO01'
            AND estado = 'Activo'
    ),
    1,
    12,
    sysdate,
    'jmazon',
    NULL,
    NULL,
    'Activo'
);

--INSERT ADMI_PARAMETRO_DET ULTIMA MILLA FIBRA OPTICA PARAMOUNT
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
                nombre_parametro = 'INFO_SERVICIO'
            AND proceso = 'ACTIVACION_SERVICIO'
            AND estado = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE PARAMOUNT',
    'ULTIMAS_MILLAS_INTERNET_PARAMOUNT',
    '1',
    'Fibra Optica',
    'NULL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    'NULL',
    'NULL',
    'NULL'
);

--INSERT ADMI_PARAMETRO_DET ULTIMA MILLA COBRE PARAMOUNT
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
                nombre_parametro = 'INFO_SERVICIO'
            AND proceso = 'ACTIVACION_SERVICIO'
            AND estado = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE PARAMOUNT',
    'ULTIMAS_MILLAS_INTERNET_PARAMOUNT',
    '3',
    'Cobre',
    'NULL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    'NULL',
    'NULL',
    'NULL'
);
--INSERT ADMI_PARAMETRO_DET ESTADOS DE INTERNET PARAMOUNT
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
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
                nombre_parametro = 'INFO_SERVICIO'
            AND proceso = 'ACTIVACION_SERVICIO'
            AND estado = 'Activo'
    ),
    'ESTADOS PERMITIDOS DEL SERVICIO DE INTERNET PARA EL FLUJO DE PARAMOUNT',
    'ESTADOS_INTERNET_PARAMOUNT',
    'Activo',
    'NULL',
    'NULL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18'
);
--INSERT ADMI_PARAMETRO_DET NOMBRE TECNICO PARAMOUNT
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
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
                nombre_parametro = 'NOMBRE_TECNICO_PRODUCTO'
            AND proceso = 'NOMBRE_TECNICO_PRODUCTO'
            AND estado = 'Activo'
    ),
    'PARAMOUNT',
    'PARAMOUNT',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
);

--INSERT ADMI_PARAMETRO_DET ULTIMA MILLA FIBRA OPTICA NOGGIN
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
                nombre_parametro = 'INFO_SERVICIO'
            AND proceso = 'ACTIVACION_SERVICIO'
            AND estado = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE NOGGIN',
    'ULTIMAS_MILLAS_INTERNET_NOGGIN',
    '1',
    'Fibra Optica',
    'NULL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    'NULL',
    'NULL',
    'NULL'
);

--INSERT ADMI_PARAMETRO_DET ULTIMA MILLA COBRE NOGGIN
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
    valor5,
    empresa_cod,
    valor6,
    valor7,
    observacion
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
                nombre_parametro = 'INFO_SERVICIO'
            AND proceso = 'ACTIVACION_SERVICIO'
            AND estado = 'Activo'
    ),
    'ULTIMAS MILLAS PERMITIDAS DEL SERVICIO DE INTERNET PARA EL FLUJO DE NOGGIN',
    'ULTIMAS_MILLAS_INTERNET_NOGGIN',
    '3',
    'Cobre',
    'NULL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18',
    'NULL',
    'NULL',
    'NULL'
);
--INSERT ADMI_PARAMETRO_DET ESTADOS DE INTERNET NOGGIN
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
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
                nombre_parametro = 'INFO_SERVICIO'
            AND proceso = 'ACTIVACION_SERVICIO'
            AND estado = 'Activo'
    ),
    'ESTADOS PERMITIDOS DEL SERVICIO DE INTERNET PARA EL FLUJO DE NOGGIN',
    'ESTADOS_INTERNET_NOGGIN',
    'Activo',
    'NULL',
    'NULL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    'NULL',
    '18'
);
--INSERT ADMI_PARAMETRO_DET NOMBRE TECNICO NOGGIN
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
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod,
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
                nombre_parametro = 'NOMBRE_TECNICO_PRODUCTO'
            AND proceso = 'NOMBRE_TECNICO_PRODUCTO'
            AND estado = 'Activo'
    ),
    'NOGGIN',
    'NOGGIN',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL,
    NULL,
    '18'
);

--URN
--INSERT URN PARAMOUNT EN ADMI_PARAMETRO_CAB
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
    'CODIGO_URN_PARAMOUNT',
    'CODIGO_URN_PARAMOUNT',
    'COMERCIAL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1'
);

--INSERT URN NOGGIN EN ADMI_PARAMETRO_CAB
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
    'CODIGO_URN_NOGGIN',
    'CODIGO_URN_NOGGIN',
    'COMERCIAL',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1'
);

--INSERT URN PARAMOUNT EN ADMI_PARAMETRO_DET
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
            admi_parametro_cab
        WHERE
            nombre_parametro = 'CODIGO_URN_PARAMOUNT'
        AND estado = 'Activo'
    ),
    'CODIGO_URN_PARAMOUNT',
    'urn:tve:paramountplus',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    '18'
);

--INSERT URN NOGGIN EN ADMI_PARAMETRO_DET
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
            admi_parametro_cab
        WHERE
            nombre_parametro = 'CODIGO_URN_NOGGIN'
        AND estado = 'Activo'
    ),
    'CODIGO_URN_NOGGIN',
    'urn:tve:noggin',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1',
    '18'
);
--INSERT CAMBIO DE RAZON SOCIAL
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROYECTO INTEGRACION PARAMOUNT',
    'PROYECTO INTEGRACION PARAMOUNT',
    'INFRAESTRUCTURA',
    'GENERACION DE USUARIO Y CLAVE',
    'Activo',
    'rcabrera',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
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
    (SELECT id_parametro
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO INTEGRACION PARAMOUNT'),
    'PARAMOUNT+',
    'USUARIO_PARAMOUNT',
    'PASSWORD_PARAMOUNT',
    'PARAMOUNT',
    'PARAMOUNT+',
    'NOGGIN',
    'PARAMOUNT-NUEVO',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '18'
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
    (SELECT id_parametro
    FROM DB_GENERAL.ADMI_PARAMETRO_CAB
    WHERE NOMBRE_PARAMETRO = 'PROYECTO INTEGRACION PARAMOUNT'),
    'NOGGIN',
    'USUARIO_NOGGIN',
    'PASSWORD_NOGGIN',
    'NOGGIN',
    'PARAMOUNT+',
    'NOGGIN',  
    'NOGGIN-NUEVO',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '18'
); 


COMMIT;

/