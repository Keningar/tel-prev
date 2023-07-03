--CREACION DE PARAMETRO PARA EL ORIGEN DE UNA ACCION DESDE LA WEB
INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'ORIGEN_WEB',
    'PARAMETRO PARA EL ORIGEN DE UNA ACCION DESDE LA WEB',
    'WEB',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'ORIGEN_WEB'
            ),
    'PARAMETRO PARA EL ORIGEN DE UNA ACCION DESDE LA WEB',
    'WEB',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--CREACION DE PARAMETRO PARA EL NOMBRE DE PROGRESO DE REGULARIZACION
INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'PROGRESO_REGULARIZACION',
    'PARAMETRO PARA EL NOMBRE DE PROGRESO DE REGULARIZACION',
    'WEB',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1',
    NULL,
    NULL,
    NULL
);

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'PROGRESO_REGULARIZACION'
            ),
    'PARAMETRO PARA EL NOMBRE DE PROGRESO DE REGULARIZACION',
    'REGULARIZACION',
    NULL,
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

COMMIT;

