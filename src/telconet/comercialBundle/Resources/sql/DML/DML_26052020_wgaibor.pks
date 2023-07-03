/* CREACIÓN DEL PARÁMETRO CAB  - REGULARIZACIÓN_CONTRATO_MD*/
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
    'REGULARIZACION_CONTRATO_TM_COMERCIAL',
    'CONFIGURACIÓN PARA LA REGULARIZACIÓN DE LOS CONTRATOS DEL TM COMERCIAL',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);

/* DB_GENERAL.ADMI_PARAMETRO_DET */
/* FLUJO DE REGULARIZACIÓN (CONTRATO/ADENDUM) ACTIVO - PUNTO TRASLADADO - SERVICIO CANCELADO*/

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
            nombre_parametro = 'REGULARIZACION_CONTRATO_TM_COMERCIAL'
            AND estado = 'Activo'
    ),
    'CONFIGURACIÓN PARA LA REGULARIZACIÓN DE LOS CONTRATOS DEL TM COMERCIAL',
    'Activo,Pendiente,PorAutorizar',
    'Existe inconsistencia en la data. Por favor comuníquese con soporte para su revisión ',
    'Activo,Pendiente',
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
