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
    'END_POINT_VALIDAR_ENLACE',
    'api/ping',
    'N',
    'ping',
    'Activo',
    'wvera',
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

/

COMMIT;
