----CREACION DE CABECERA PARA PARAMETROS DEL MOVIL

INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'PARAMETROS_GENERALES_MOVIL',
    'PARAMETROS QUE SE REQUIEREN PARA EL USO DEL APLICATIVO MOVIL',
    'MOVIL',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1',
    NULL,
    NULL,
    NULL
);

---- INICIO CREACION DE DETALLE PARA PARAMETROS DEL MOVIL

--NUM_FOTOS_MIN
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'PARAMETROS QUE SE REQUIEREN PARA EL USO DEL APLICATIVO MOVIL',
    'NUM_FOTOS_MIN',
    '3',
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

--NUM_FOTOS_MAX
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_GENERALES_MOVIL'
    ),
    'PARAMETROS QUE SE REQUIEREN PARA EL USO DEL APLICATIVO MOVIL',
    'NUM_FOTOS_MAX',
    '6',
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

--------------------------------------------------------------------------------

---- FIN CREACION DE DETALLE PARA PARAMETROS DEL MOVIL

/
COMMIT;
/
