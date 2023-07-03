--------------------------------------------------------------------------------
---- FIN CREACION DE DETALLE PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS

INSERT INTO db_comunicacion.admi_tipo_documento (
    id_tipo_documento,
    extension_tipo_documento,
    tipo_mime,
    descripcion_tipo_documento,
    estado,
    usr_creacion,
    fe_creacion
) VALUES (
    db_comunicacion.seq_admi_tipo_documento.nextval,
    'p12',
    'p12',
    'ARCHIVO FORMATO p12',
    'Activo',
    'wgaibor',
    sysdate
);

--
INSERT INTO db_general.admi_tipo_documento_general (
    id_tipo_documento,
    codigo_tipo_documento,
    descripcion_tipo_documento,
    estado,
    usr_creacion,
    ip_creacion,
    fe_creacion,
    visible,
    persona,
    elemento
) VALUES (
    db_general.seq_admi_tipo_document_general.nextval,
    'CDI',
    'CERTIFICADO DIGITAL',
    'Activo',
    'wgaibor',
    '127.0.0.1',
    sysdate,
    'N',
    'N',
    'N'
);
--
/* CREACIÓN DEL PARÁMETRO CAB  - CONTRATO_ARCHIVOS_NO_VISIBLE*/
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
    'CONTRATO_ARCHIVOS_NO_VISIBLE',
    'ARCHIVOS QUE NO SE DEBE VISUALIZAR EN EL CONTRATO DE UN CLIENTE',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
/* ComparacionImagen - Regularización de los archivos de imagenes de la app movil operaciones*/
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
    valor6,
    valor7
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'CONTRATO_ARCHIVOS_NO_VISIBLE'
            AND estado = 'Activo'
    ),
    'ARCHIVOS CON EXTENSIONES QUE NO SE DEBEN VISUALIZAR AL MOMENTO DE VER LOS DOCUMENTOS DEL CONTRATO.',
    'p12,PFX',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);
--
INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    usr_ult_mod,
    fe_ult_mod,
    ip_ult_mod
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'BANDERA_NFS',
    'BANDERA QUE INDICA SI SE DEBE ESCRIBIR LOS ARCHIVOS EN EL SERVIDOR NFS',
    'COMERCIAL',
    NULL,
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
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
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion,
    valor5,
    valor6,
    valor7
) VALUES (
    db_general.seq_admi_parametro_det.nextval,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'BANDERA_NFS'
            AND estado = 'Activo'
    ),
    'BANDERA QUE INDICA SI SE DEBE ESCRIBIR LOS ARCHIVOS EN EL SERVIDOR NFS',
    'S',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);
--======================================================

COMMIT;
/
