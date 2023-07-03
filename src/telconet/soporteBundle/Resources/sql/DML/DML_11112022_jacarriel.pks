/*
 *
 * Se crea nuevo parametro para codigo de trabajo en TN.
 *	 
 * @author Jeampier Carriel <jacarriel@telconet.ec>
 * @version 1.0 11-11-2022
 *
*/

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
    'VALOR QUE SE TOMA PARA VALIDAR O NO EMPRESA TN',
    'CODIGO_TRABAJO_TN',
    'N',
    NULL,
    NULL,
    'Activo',
    'jacarriel',
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
    'TIPOS DE SERVICIO PARA LOS QUE SE TOMAN EL CODIGO DE TRABAJO',
    'TIPOS_DE_SERVICIO',
    'TRASLADO,NUEVO',
    NULL,
    NULL,
    'Activo',
    'jacarriel',
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