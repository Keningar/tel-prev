UPDATE DB_GENERAL.admi_parametro_det 
set estado = 'Inactivo',
usr_ult_mod = 'wgaibor',
fe_ult_mod = sysdate
WHERE parametro_id = (SELECT
    id_parametro
FROM db_general.admi_parametro_cab
WHERE nombre_parametro = 'CONTACTOS_L1');

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'CONTACTOS_L1'
    ),
    'CONTACTO DEL DPTO. L1 NACIONAL',
    'IPCCL1',
    'soporte@telconet.ec',
    NULL,
    'IPCCL1',
    'Activo',
    'wgaibor',
    SYSDATE,
    '127.0.0.0', 
    NULL,
    NULL,
    NULL,
    'Soporte Nacional',
    NULL,
    NULL,
    NULL,
    NULL
);
--Número del dpto L1 Nacional
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
    'NUMERO_CONTACTO_L1',
    'NÚMEROS DE CONTACTO DEL DPTO L1',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
--
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUMERO_CONTACTO_L1'
    ),
    'NÚMERO DE CONTACTO DEL DPTO. L1 NACIONAL',
    '0998154732',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
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
--
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'NUMERO_CONTACTO_L1'
    ),
    'NÚMERO DE CONTACTO DEL DPTO. L1 NACIONAL',
    '0984980189',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
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
--SINTOMAS DE CASOS QUE NO SE DEBEN MOSTRAR EN LA APP TELCO MANAGER
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
    'SINTOMAS_CASO_TELCO_MANAGER',
    'CASOS QUE NO SE DEBEN VISUALIZAR EN LA APP TELCO MANAGER',
    'COMERCIAL',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
--
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'SINTOMAS_CASO_TELCO_MANAGER'
    ),
    'SINTOMA: Ip reportada por ECUCERT no debe visualizarse este tipo de casos',
    '436',
    'Ip reportada por ECUCERT',
    NULL,
    NULL,
    'Activo',
    'wgaibor',
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
--MOSTRAR SOLO LOS CASOS DEL DÍA ACTUAL EN LA APP TELCO MANAGER
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
    'VISUALIZAR_CASOS_DEL_DIA_TM',
    'BANDERA PARA SABER SI SE DEBE MOSTRAR LOS CASOS DEL DÍA',
    'SOPORTE',
    'Activo',
    'wgaibor',
    sysdate,
    '127.0.0.1'
);
--
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'VISUALIZAR_CASOS_DEL_DIA_TM'
    ),
    'BANDERA PARA SABER SI SE DEBE MOSTRAR LOS CASOS DEL DÍA DE LA APP TELCO MANAGER',
    'S',
    NULL,
    NULL,
    NULL,
    'Activo',
    'wgaibor',
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
--
COMMIT;

/
