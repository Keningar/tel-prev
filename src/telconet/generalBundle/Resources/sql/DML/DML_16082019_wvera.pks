
--INSERT NUEVO MOTIVO

INSERT INTO DB_GENERAL.ADMI_MOTIVO VALUES(
DB_GENERAL.SEQ_ADMI_MOTIVO.NEXTVAL,
NULL,
'FISCALIZACIÓN DE CUADRILLAS',
'Activo',
'wvera',
sysdate,
'wvera',
sysdate,
null,
null); 

--DPT_OPCION_FISCALIZAR

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
    'PERFIL PARA LOS USUARIOS QUE PUEDEN HACER USO DEL APARTADO DE FISCALIZAR.',
    'PERFIL_FISCALIZAR',
    'TMO Fiscalizar',
    NULL,
    NULL,
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
    'MOTIVO_REGISTRO_HISTORIAL_CUADRILLA',
    'FISCALIZACIÓN DE CUADRILLAS',
    NULL,
    NULL,
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



COMMIT;
