----CREACION DE CABECERA PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS

INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS',
    'PARAMETROS PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS',
    'TELCOS',
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0',
    NULL,
    NULL,
    NULL
);

----CREACION DE CABECERA PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS

---- INICIO CREACION DE DETALLE PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS

--RUTA_BASE
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS'
    ),
    'PARAMETROS QUE SE REQUIEREN PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS',
    'RUTA_BASE',
    'public/uploads/',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

--ec.telconet.mobile.telcos.operaciones
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ESTRUCTURA_RUTAS_TELCOS'
    ),
    'CARPETA PARA ARCHIVOS DE APP TMO',
    'ec.telconet.mobile.telcos.operaciones',
    'TmOperaciones',
    NULL,
    NULL,
    'Activo',
    'jnazareno',
    SYSDATE,
    '127.0.0.0', 
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
---- FIN CREACION DE DETALLE PARA NUEVA ESTRUCTURA DE RUTAS EN TELCOS

COMMIT;
/