-- INSERTING into DB_GENERAL.ADMI_PARAMETRO_CAB

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
    'MENSAJES_ADMIN_NOTIF_PUSH',
    'Mensajes por pantalla Admin. Noti Push',
    'ADMINISTRACION',
    NULL,
    'Activo',
    'adorellana',
    SYSDATE,
    '127.0.0.1',
    NULL,
    NULL,
    NULL
);

COMMIT;
