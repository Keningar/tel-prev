--PRODUCTO SSID MOVIL
INSERT INTO db_comercial.admi_producto 
    (ID_PRODUCTO, EMPRESA_COD, CODIGO_PRODUCTO, DESCRIPCION_PRODUCTO, INSTALACION, ESTADO, FE_CREACION, USR_CREACION, IP_CREACION, ES_PREFERENCIA, ES_ENLACE, REQUIERE_PLANIFICACION, REQUIERE_INFO_TECNICA, NOMBRE_TECNICO, TIPO, ES_CONCENTRADOR, FUNCION_PRECIO, SOPORTE_MASIVO, ESTADO_INICIAL, GRUPO, SUBGRUPO, LINEA_NEGOCIO)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO.NEXTVAL,
    '10',
    'NL01',
    'SSID MOVIL',
    '0',
    'Activo',
    sysdate,
    'rcabrera',
    '127.0.0.1',
    'NO',
    'NO',
    'NO',
    'SI',
    'NETWIFI',
    'S',
    'NO',
    'PRECIO=2.50',
    'S',
    'PreAsignacionInfoTecnica',
    'NETWIFI',
    'OTROS',
    'OTROS'
);

--CARACTERISTICAS DEL PRODUCTO SSID MOVIL
INSERT INTO db_comercial.admi_producto_caracteristica
   (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL'),
    '1194',
    SYSDATE,
    'rcabrera',
    'Activo',
    'NO'
);

INSERT INTO db_comercial.admi_producto_caracteristica
    (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL'),
    '1195',
    SYSDATE,
    'rcabrera',
    'Activo',
    'NO'
);

INSERT INTO db_comercial.admi_producto_caracteristica
    (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL'),
    '22',
    SYSDATE,
    'rcabrera',
    'Activo',
    'NO'
);

INSERT INTO db_comercial.admi_producto_caracteristica
    (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL'),
    '652',
    SYSDATE,
    'rcabrera', 'Activo',
    'NO'
);

INSERT INTO db_comercial.admi_producto_caracteristica
    (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL'),
    '260',
    SYSDATE,
    'rcabrera', 'Activo',
    'NO'
);

INSERT INTO db_comercial.admi_producto_caracteristica 
    (ID_PRODUCTO_CARACTERISITICA, PRODUCTO_ID, CARACTERISTICA_ID, FE_CREACION, USR_CREACION, ESTADO, VISIBLE_COMERCIAL)
    VALUES (
    db_comercial.SEQ_ADMI_PRODUCTO_CARAC.NEXTVAL,
    (select ID_PRODUCTO from db_comercial.admi_producto where descripcion_producto = 'SSID MOVIL'),
    '750',
    SYSDATE,
    'rcabrera',
    'Activo',
    'SI'
);

update db_comercial.admi_producto set estado = 'Inactivo' where id_producto = 275;


INSERT
INTO DB_GENERAL.ADMI_PARAMETRO_CAB VALUES
  (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'PROYECTO SSID MOVIL',
    'PROYECTO SSID MOVIL',
    'INFRAESTRUCTURA',
    'SSID MOVIL',
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    empresa_cod
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (SELECT id_parametro FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'PROYECTO SSID MOVIL'),
    'TIPO_CONTACTO_NOTIFICACIONES',
    'Contacto Tecnico',
    'Activo',
    'rcabrera',
    SYSDATE,
    '127.0.0.1',
    10
);

COMMIT;

/
