-- CREACIÓN DEL PARÁMETRO CAB  - ESTADO_CONT_ADEN_COMERCIAL
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
    'ESTADO_AUTORIZADO_CONTRATO_ADENDUM',
    'Configuración para la validación de estados en contrato digital',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);

-- DB_GENERAL.ADMI_PARAMETRO_DET 
-- FLUJO DE REGULARIZACIÓN (CONTRATO/ADENDUM) ACTIVO AUTORIZADOS

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
            nombre_parametro = 'ESTADO_AUTORIZADO_CONTRATO_ADENDUM'
            AND estado = 'Activo'
    ),
    'Estados de contrato y adendum luego de autorizar',
    'Activo,Pendiente',
    'El {{tipo}} ya fue autorizado. Por favor volver a consultar cliente',
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

COMMIT;
/
