----INICIO DE PARÁMETROS

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
    'TCSP',
    'TÉRMINOS Y CONDICIONES SERVICIOS/PRODUCTOS ADICIONALES',
    'Activo',
    'ccaguana',
    '127.0.0.1',
    sysdate,
    'N',
    'N',
    'N'
);


--FIN DE PARÁMETROS
COMMIT;
