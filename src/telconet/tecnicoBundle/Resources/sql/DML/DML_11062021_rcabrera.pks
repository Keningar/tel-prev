--Creacion de Proceso: SOLICITAR NUEVO SERVICIO SAFE CITY
insert into db_soporte.admi_proceso values(db_soporte.SEQ_ADMI_PROCESO.nextval,null,'SOLICITAR NUEVO SERVICIO SAFE CITY','PROCESO PARA SERVICIO SAFECITY',null,'Activo','rcabrera',sysdate,'rcabrera',sysdate,null,null);
--Asociar Proceso a la empresa.
insert into DB_SOPORTE.admi_proceso_empresa values(db_soporte.SEQ_ADMI_PROCESO_EMPRESA.nextval,(select id_proceso from db_soporte.admi_proceso where nombre_proceso = 'SOLICITAR NUEVO SERVICIO SAFE CITY'),'10','Active','rcabrera',sysdate);

--Creacion de Tarea: FIBRA: INSTALACION CAMARAS
insert into db_soporte.admi_tarea values(db_soporte.SEQ_ADMI_TAREA.nextval,(select id_proceso from db_soporte.admi_proceso where nombre_proceso = 'SOLICITAR NUEVO SERVICIO SAFE CITY'),null,null,null,1,0,'FIBRA: INSTALACION CAMARAS','TAREA DE INSTALACION DE CAMARAS',1,'MINUTOS',1,1,'Activo','rcabrera',sysdate,'rcabrera',sysdate,null,null,null,'N','N');

--Creacion de Proceso: SOLICITAR NUEVO COU VIDEOS ANALYTICS MASK DETECTION
insert into db_soporte.admi_proceso values(db_soporte.SEQ_ADMI_PROCESO.nextval,null,'SOLICITAR NUEVO COU VIDEOS ANALYTICS MASK DETECTION','PROCESO PARA SERVICIO SAFECITY',null,'Activo','rcabrera',sysdate,'rcabrera',sysdate,null,null);

--Asociar Proceso a la empresa.
insert into DB_SOPORTE.admi_proceso_empresa values(db_soporte.SEQ_ADMI_PROCESO_EMPRESA.nextval,(select id_proceso from db_soporte.admi_proceso where nombre_proceso = 'SOLICITAR NUEVO COU VIDEOS ANALYTICS MASK DETECTION'),'10','Active','rcabrera',sysdate);


--Creacion de Tarea: CONFIGURACION SERVICIO MASCARILLA
insert into db_soporte.admi_tarea values(db_soporte.SEQ_ADMI_TAREA.nextval,(select id_proceso from db_soporte.admi_proceso where nombre_proceso = 'SOLICITAR NUEVO COU VIDEOS ANALYTICS MASK DETECTION'),null,null,null,1,0,'CONFIGURACION SERVICIO MASCARILLA','TAREA DE CONFIGURACION SERVICIO MASCARILLA',1,'MINUTOS',1,1,'Activo','rcabrera',sysdate,'rcabrera',sysdate,null,null,null,'N','N');

--Crear CARACTERISTICA 'RELACION_CAMARA_DATOS_SAFECITY'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'RELACION_CAMARA_DATOS_SAFECITY','N',SYSDATE,'facaicedo','TECNICO','Activo');
--
--Crear CARACTERISTICA 'RELACION_MASCARILLA_CAMARA_SAFECITY'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'RELACION_MASCARILLA_CAMARA_SAFECITY','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'SERVICIO_ADICIONAL'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'SERVICIO_ADICIONAL','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'USUARIO_CAMARA'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'USUARIO_CAMARA','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'CLAVE_CAMARA'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'CLAVE_CAMARA','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'URL_CAMARA'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'URL_CAMARA','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'TAREA_CONFIGURACION_MASCARILLA'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'TAREA_CONFIGURACION_MASCARILLA','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'ID_DETALLE_CONFIGURACION_MASCARILLA'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'ID_DETALLE_CONFIGURACION_MASCARILLA','N',SYSDATE,'rcabrera','TECNICO','Activo');

--Crear CARACTERISTICA 'PUERTO_ONT'
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA(ID_CARACTERISTICA,DESCRIPCION_CARACTERISTICA,TIPO_INGRESO,FE_CREACION,USR_CREACION,TIPO,ESTADO)
VALUES(DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,'PUERTO_ONT','N',SYSDATE,'rcabrera','TECNICO','Activo');


--Crear Producto SAFE VIDEO ANALYTICS CAM
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO
(ID_PRODUCTO, EMPRESA_COD, CODIGO_PRODUCTO, DESCRIPCION_PRODUCTO, INSTALACION, ESTADO, FE_CREACION, USR_CREACION, IP_CREACION, CTA_CONTABLE_PROD, CTA_CONTABLE_PROD_NC, ES_PREFERENCIA, ES_ENLACE, REQUIERE_PLANIFICACION, REQUIERE_INFO_TECNICA, NOMBRE_TECNICO, TIPO, ES_CONCENTRADOR, FUNCION_PRECIO, SOPORTE_MASIVO, ESTADO_INICIAL, GRUPO, COMISION_VENTA, COMISION_MANTENIMIENTO, CLASIFICACION, REQUIERE_COMISIONAR, SUBGRUPO, LINEA_NEGOCIO)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL, '10', 'CAM-SAFE', 'SAFE VIDEO ANALYTICS CAM', 0, 'Activo', sysdate, 'rcabrera', '127.0.0.1', 0, 0, 'NO', 'SI', 'SI', 'SI', 'SAFECITYDATOS', 'S', 'NO', 'PRECIO=20', 'S', 'Pendiente', 'SEGURIDAD ELECTRONICA Y FISICA', 0, 0, NULL, 'NO', 'SEGURIDAD ELECTRONICA', 'SECURITY');

--Crear las caracteristicas para el producto: SAFE VIDEO ANALYTICS CAM 
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '5', sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '666', sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '669', sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '781', sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '1621', sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '1622', sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '1', sysdate, 'rcabrera', 'Activo', 'SI');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'), '2', sysdate, 'rcabrera', 'Activo', 'SI');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_RED'), sysdate, 'rcabrera', 'Activo', 'SI');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SERVICIO_ADICIONAL'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'USUARIO_CAMARA'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'CLAVE_CAMARA'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'URL_CAMARA'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'PUERTO_ONT'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA 
VALUES(DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'RELACION_CAMARA_DATOS_SAFECITY' AND ESTADO = 'Activo'),SYSDATE,NULL,'facaicedo',NULL,'Activo','NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(select ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA = 'RELACION_MASCARILLA_CAMARA_SAFECITY'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),(select ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA = 'MAC ONT'), sysdate, 'rcabrera', 'Activo', 'NO');


--Crear Producto COU VIDEOS ANALYTICS MASK DETECTION
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO
(ID_PRODUCTO, EMPRESA_COD, CODIGO_PRODUCTO, DESCRIPCION_PRODUCTO, INSTALACION, ESTADO, FE_CREACION, USR_CREACION, IP_CREACION, CTA_CONTABLE_PROD, CTA_CONTABLE_PROD_NC, ES_PREFERENCIA, ES_ENLACE, REQUIERE_PLANIFICACION, REQUIERE_INFO_TECNICA, NOMBRE_TECNICO, TIPO, ES_CONCENTRADOR, FUNCION_PRECIO, SOPORTE_MASIVO, ESTADO_INICIAL, GRUPO, COMISION_VENTA, COMISION_MANTENIMIENTO, CLASIFICACION, REQUIERE_COMISIONAR, SUBGRUPO, LINEA_NEGOCIO)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO.NEXTVAL, '10', 'MASK-CAM','COU VIDEOS ANALYTICS MASK DETECTION', 0, 'Activo', sysdate, 'rcabrera', '127.0.0.1', 0, 0, 'NO', 'NO', 'NO', 'NO', 'SERVICIOS-CAMARA-SAFECITY', 'S', 'NO', 'PRECIO=20', 'S', 'Pendiente', 'COMUNICACIONES UNIFICADAS', 0, 0,NULL, 'NO', 'COU VIDEO ANALYTICS', 'COLLABORATION');

--Crear las caracteristicas para el producto: COU VIDEOS ANALYTICS MASK DETECTION
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL) 
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'COU VIDEOS ANALYTICS MASK DETECTION' AND ESTADO = 'Activo'),
(select ID_CARACTERISTICA from DB_COMERCIAL.ADMI_CARACTERISTICA where DESCRIPCION_CARACTERISTICA = 'RELACION_MASCARILLA_CAMARA_SAFECITY'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'COU VIDEOS ANALYTICS MASK DETECTION' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TIPO_RED'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'COU VIDEOS ANALYTICS MASK DETECTION' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TAREA_CONFIGURACION_MASCARILLA'), sysdate, 'rcabrera', 'Activo', 'NO');
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'COU VIDEOS ANALYTICS MASK DETECTION' AND ESTADO = 'Activo'),(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ID_DETALLE_CONFIGURACION_MASCARILLA'), sysdate, 'rcabrera', 'Activo', 'NO');

------------------------------------------------------------------------------------------------------------------------------
--Agregar el producto a GPON: SAFE VIDEO ANALYTICS CAM
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
            descripcion_producto = 'SAFE VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'SAFE VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'GPON',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);

--Agregar el producto a GPON: SERVICIO_MASCARILLA
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
            descripcion_producto = 'COU VIDEOS ANALYTICS MASK DETECTION'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'COU VIDEOS ANALYTICS MASK DETECTION'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'GPON',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    'RELACION_PRODUCTO_CARACTERISTICA',
    10
);


--Configurar para el producto para que no salga para MPLS: SAFE VIDEO ANALYTICS CAM
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
            descripcion_producto = 'SAFE VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'SAFE VIDEO ANALYTICS CAM'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'MPLS',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    'PRODUCTO_NO_PERMITIDO_MPLS',
    10
);

--Configurar para el producto para que no salga para MPLS: COU VIDEOS ANALYTICS MASK DETECTION
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
            descripcion_producto = 'COU VIDEOS ANALYTICS MASK DETECTION'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    (
        SELECT
            descripcion_producto
        FROM
            db_comercial.admi_producto
        WHERE
            descripcion_producto = 'COU VIDEOS ANALYTICS MASK DETECTION'
            AND empresa_cod = 10
            AND nombre_tecnico <> 'FINANCIERO'
            AND estado = 'Activo'
    ),
    'MPLS',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    'PRODUCTO_NO_PERMITIDO_MPLS',
    10
);

-----------------------------------------------------------------------------------------------------------------------------


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PARAMETROS PROYECTO GPON SAFECITY',
    'PARAMETROS USADOS PARA EL GPON PROYECTO SAFECITY',
    'INFRAESTRUCTURA',
    'PARAMETROS',
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
    'VALIDAR RELACION CAMARA CON DATOS SAFECITY',
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'SAFE VIDEO ANALYTICS CAM'),
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'),
    'AsignadoTarea',
    'STANDARD',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    'VALIDAR RELACION MASCARILLA CON SAFE VIDEO ANALYTICS CAM',
    (SELECT id_producto FROM db_comercial.admi_producto WHERE descripcion_producto = 'COU VIDEOS ANALYTICS MASK DETECTION' AND estado = 'Activo'),
    (SELECT id_producto FROM db_comercial.admi_producto WHERE descripcion_producto = 'SAFE VIDEO ANALYTICS CAM' AND estado = 'Activo'),
    'Se realiza la finalización automática de la tarea de configuracion, por confirmacion del servicio',
    'ID_MOTIVO',
    '1457',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'Activo',
    'Factible',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'In-Corte',
    'Factible',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'Cancel',
    'Pendiente',
    'N',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'AsignadoTarea',
    'Factible',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'AsignadoTarea',
    'Factible',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'Asignada',
    'Factible',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'Pre-servicio',
    'Pendiente',
    'N',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAPEO_ESTADOS',
    'PrePlanificada',
    'Factible',
    'S',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'MAXIMO_CAMARAS_POR_PUNTO',
    '5',
    (select id_producto from db_comercial.admi_producto where descripcion_producto = 'DATOS GPON VIDEO ANALYTICS CAM'),
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'FORMATO_URL_CAMARA_SAFECITY',
    'rtsp://admin:{{password}}#@{{ipCamara}}:puertorstp/Streaming/Channels/PATH',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    'DEPARTAMENTO_QUE_CONFIGURA_LA_CAMARA',
    '1024',
    'Se reasigna automaticamente la tarea de instalacion para la configuracion de la CAMARA',
    'ID_PERSONA',
    '1829467',
    '2595970',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
);

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
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'TIPO_SERVICIO',
    'SERVICIO_MASCARILLA',
    'RELACION_MASCARILLA_CAMARA_SAFECITY',
    'CONFIGURACION SERVICIO MASCARILLA',
    'SOLICITAR NUEVO SERVICIO SAFE CITY',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'TAREA DE INSTALACION DE CAMARA',
    'FIBRA: INSTALACION CAMARAS',
    'SOLICITAR NUEVO SERVICIO SAFE CITY',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'RESOLUCION_CAMARA',
    '1080p',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'RESOLUCION_CAMARA',
    '720p',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'FPS',
    '60',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'FPS',
    '50',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
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
    (  SELECT id_parametro
       FROM DB_GENERAL.ADMI_PARAMETRO_CAB
       WHERE NOMBRE_PARAMETRO = 'PARAMETROS PROYECTO GPON SAFECITY'),
    'CODEC',
    'h265',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    '10'
);

-- INGRESO DE LA CABECERA DE PARAMETROS DE 'CONFIG_PRODUCTO_DATOS_SAFE_CITY'
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
        'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
        'Lista de la configuración del productos para la orden de servicio',
        'COMERCIAL',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
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
          WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'AGREGAR_SERVICIO_ADICIONAL',
        '[Cantidad Camaras]',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'SAFE VIDEO ANALYTICS CAM GPON 10',
        'RELACION_CAMARA_DATOS_SAFECITY',
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'COORDINAR_OBSERVACION',
        'CAMARA DATOS SAFECITY',
        'CAMARA DATOS SAFECITY',
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamos el detalle para el proceso y tarea de instalación de la camara safe city
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamos el detalle de la actualización del estado de la tarea del servicio camara safe city
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Se relaciona la característica ID_DETALLE_TAREA_INSTALACION con el producto.
INSERT INTO db_comercial.admi_producto_caracteristica VALUES (
    db_comercial.seq_admi_producto_carac.nextval,
    ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
        WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
    (
        SELECT
            id_caracteristica
        FROM
            db_comercial.admi_caracteristica
        WHERE
            descripcion_caracteristica = 'ID_DETALLE_TAREA_INSTALACION'
    ),
    SYSDATE,
    NULL,
    'facaicedo',
    NULL,
    'Activo',
    'NO'
);

--Ingresamos el detalle para el producto requerido datos safe city para la camara
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'PRODUCTO_REQUERIDO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'DATOS GPON VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamos el detalle para el producto requerido camara para servicio mascarilla
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
          WHERE DESCRIPCION_PRODUCTO = 'COU VIDEOS ANALYTICS MASK DETECTION' AND ESTADO = 'Activo' ),
        'PRODUCTO_REQUERIDO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamos los tipos de elementos camara
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
    'MAPEO TIPOS ELEMENTOS CAMARA',
    'CAMARA IP',
    'Activo',
    'facaicedo',
    SYSDATE,
    '127.0.0.1',
    '10'
);

--Ingresamo el detalle para filtrar la velocidad del producto CAMARA
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamo el detalle para filtrar la velocidad del producto CAMARA
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

-- Detalle velocidad por default al generar el servicio cámara de forma automática.
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
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

--Ingresamos los detalle para la vrf del producto
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
            WHERE NOMBRE_PARAMETRO = 'NUEVA_RED_GPON_TN'
            AND ESTADO = 'Activo'
        ),
        'VRF PRODUCTO',
        ( SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO
          WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
        'safecity-camaras',
        'Activo',
        'facaicedo',
        SYSDATE,
        '127.0.0.1'
);

--insertar caracteristicas
INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'VELOCIDAD_GPON'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'MAC CLIENTE'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SPID'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'INDICE CLIENTE'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'LINE-PROFILE-NAME'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'VLAN-MONITOREO'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ID-MAPPING-MONITOREO'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'T-CONT-MONITOREO'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TRAFFIC-TABLE-MONITOREO'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'GEM-PORT-MONITOREO'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'ID-MAPPING'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'T-CONT'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'TRAFFIC-TABLE'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'GEM-PORT'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SPID MONITOREO'),
sysdate, 'rcabrera', 'Activo', 'NO');

INSERT INTO DB_COMERCIAL.ADMI_PRODUCTO_CARACTERISTICA (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
VALUES (DB_COMERCIAL.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
(SELECT ID_PRODUCTO FROM DB_COMERCIAL.ADMI_PRODUCTO WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo'),
(SELECT ID_CARACTERISTICA FROM DB_COMERCIAL.ADMI_CARACTERISTICA WHERE DESCRIPCION_CARACTERISTICA = 'SERVICE-PROFILE'),
sysdate, 'rcabrera', 'Activo', 'NO');

-- INSERTAR LA ULTIMA MILLA PARA SAFE VIDEO ANALYTICS CAM
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
        WHERE DESCRIPCION_PRODUCTO = 'SAFE VIDEO ANALYTICS CAM' AND ESTADO = 'Activo' ),
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

COMMIT;
/
