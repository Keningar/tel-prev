--UPDATE VALOR PRODUCTO PARAMOUNT
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET apd
SET VALOR2 = (SELECT ap.NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO ap 
                WHERE ap.CODIGO_PRODUCTO = 'PA01' AND ESTADO='Activo')
WHERE apd.PARAMETRO_ID = (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc 
                            WHERE apc.NOMBRE_PARAMETRO = 'CODIGO_URN_PARAMOUNT' AND ESTADO = 'Activo');

--UPDATE VALOR PRODUCTO FOX
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET apd
SET VALOR2 = (SELECT ap.NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO ap 
                WHERE ap.CODIGO_PRODUCTO = 'FOXP' AND ESTADO='Activo')
WHERE apd.PARAMETRO_ID = (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc 
                            WHERE apc.NOMBRE_PARAMETRO = 'CODIGO_URN_FOX' AND ESTADO = 'Activo');

--UPDATE VALOR PRODUCTO NOGGIN
UPDATE DB_GENERAL.ADMI_PARAMETRO_DET apd
SET VALOR2 = (SELECT ap.NOMBRE_TECNICO FROM DB_COMERCIAL.ADMI_PRODUCTO ap 
                WHERE ap.CODIGO_PRODUCTO = 'NO01' AND ESTADO='Activo')
WHERE apd.PARAMETRO_ID = (SELECT apc.ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB apc 
                            WHERE apc.NOMBRE_PARAMETRO = 'CODIGO_URN_NOGGIN' AND ESTADO = 'Activo');

--INSERT PARAMETER CAB
INSERT INTO db_general.ADMI_PARAMETRO_CAB 
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
VALUES (db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,'NOMBRE_TECNICO_PRODUCTOS_TV',
        'NOMBRE_TECNICO_PRODUCTOS_TV','COMERCIAL',NULL,'Activo','jmazon',SYSDATE,'127.0.0.1',NULL,NULL,NULL);

--INSERT PARAMETER DET
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
    WHERE NOMBRE_PARAMETRO = 'NOMBRE_TECNICO_PRODUCTOS_TV'),
    'NOMBRES_TECNICOS_PRODUCTOS_TV',
    'NOGGIN',
    'PARAMOUNT',
    '',
    '',
    '',  
    '',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    '18'
); 

--INSERT PARAMETER CAB MENSAJES
INSERT INTO db_general.ADMI_PARAMETRO_CAB 
(ID_PARAMETRO,NOMBRE_PARAMETRO,DESCRIPCION,MODULO,PROCESO,ESTADO,USR_CREACION,FE_CREACION,IP_CREACION,USR_ULT_MOD,FE_ULT_MOD,IP_ULT_MOD) 
VALUES (db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,'MENSAJE_TEXTO',
        'ACTIVACION Y DESACTIVACION DE MENSAJES DE TEXTO','ADMINISTRACION',NULL,'Activo','jmazon',SYSDATE,'127.0.0.1',NULL,NULL,NULL);

--INSERT PARAMETER DET MENSAJES
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
    WHERE NOMBRE_PARAMETRO = 'MENSAJE_TEXTO'),
    'MENSAJE_TEXTO',
    'Activado',
    '',
    '',
    '',
    '',  
    '',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    '18'
); 
--INSERT PARAMETER DET MENSAJES
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
    WHERE NOMBRE_PARAMETRO = 'MENSAJE_TEXTO'),
    'MENSAJE_TEXTO_ESTADO',
    'Activado',
    'Desactivado',
    '',
    '',
    '',  
    '',
    'Activo',
    'jmazon',
    SYSDATE,
    '127.0.0.1',
    '18'
); 

--INSERT DE CARACTERISTICA AL PRODUCTO PARAMOUNT
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
                descripcion_caracteristica = 'CORREO ELECTRONICO'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'SI'
);
--INSERT DE CARACTERISTICA AL PRODUCTO NOGGIN
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
                descripcion_caracteristica = 'CORREO ELECTRONICO'
            AND estado = 'Activo'
    ),
    sysdate,
    NULL,
    'jmazon',
    NULL,
    'Activo',
    'SI'
);




COMMIT;

/