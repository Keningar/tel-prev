----CREACION DE FILTRO PARA DEPARTAMENTOS

INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'DPT_SIN_FILTRO_FOTO',
    'PARAMETRO PARA RETORNAR LOS DEPARTAMENTOS QUE NO SE FILTRAN LAS ETIQUETAS DE FOTOS POR DEPARTAMENTO',
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

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
            (
                SELECT
                    id_parametro
                FROM
                    db_general.admi_parametro_cab
                WHERE
                    nombre_parametro = 'DPT_SIN_FILTRO_FOTO'
            ),
    'PARAMETRO DE ID DEPARTAMENTO QUE NO REQUIEREN FILTO EN ETIQUETAS DE FOTOS',
    '117',
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

----CREACION DE ETIQUETAS

INSERT INTO db_general.admi_parametro_cab VALUES 
(
    db_general.SEQ_ADMI_PARAMETRO_CAB.nextval,
    'ETIQUETA_FOTO',
    'PARAMETRO USADO PARA LA ESTRUCTURA DE ETIQUETAS DE LAS FOTOS',
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

--INICIO DEPARTAMENTO DE OPU
--OLT
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'OLT',
    'olt.png',
    '128',
    'N',
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

--ODF DE RUTA
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'ODF DE RUTA',
    'odf.png',
    '128',
    'N',
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

--ODF REFLEJO
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'ODF REFLEJO',
    'odf.png',
    '128',
    'N',
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

--SPLITTER PRIMER NIVEL
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'SPLITTER PRIMER NIVEL',
    'spliter.png',
    '128',
    'N',
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

--SPLITTER SEGUNDO NIVEL
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'SPLITTER SEGUNDO NIVEL',
    'spliter.png',
    '128',
    'N',
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

--CAJA BMX
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'CAJA BMX',
    'caja.png',
    '128',
    'N',
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

--CASSETTE
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'CASSETTE',
    'cassette.png',
    '128',
    'N',
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

--ROSETA
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'ROSETA',
    'roseta.png',
    '128',
    'N',
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

--PIGTAIL
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'PIGTAIL',
    'pigtail.png',
    '128',
    'N',
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

--PATCH DE FIBRA
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'PATCH DE FIBRA',
    'patch.png',
    '128',
    'N',
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

--ADAPTADOR DUPLEX
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'ADAPTADOR DUPLEX',
    'adaptador.png',
    '128',
    'N',
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

--ADAPTADOR SIMPLEX
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'ADAPTADOR SIMPLEX',
    'adaptador.png',
    '128',
    'N',
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

--ONT
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'ONT',
    'ont.png',
    '128',
    'N',
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

--MINI MANGA
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'MINI MANGA',
    'miniManga.png',
    '128',
    'N',
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

--MANGA 3M
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'MANGA 3M',
    'manga3M.png',
    '128',
    'N',
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

--TRANSCEIVER CLIENTE
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'TRANSCEIVER CLIENTE',
    'transceiver.png',
    '128',
    'S',
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL,
    NULL
);

--TRANSCEIVER NODO
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'TRANSCEIVER NODO',
    'transceiver.png',
    '128',
    'S',
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    '10',
    NULL,
    NULL,
    NULL,
    NULL
);

--CABLE UTP
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'CABLE UTP',
    'cableUtp.png',
    '128',
    'N',
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

--EXTENDER
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'EXTENDER',
    'extender.png',
    '128',
    'S',
    'Activo',
    'jnazareno',
    SYSDATE,
    '192.168.1.1', 
    NULL,
    NULL,
    NULL,
    '18',
    NULL,
    NULL,
    NULL,
    NULL
);

--INTERFAZ GIGA
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'INTERFAZ GIGA',
    'interfazGiga.png',
    '128',
    'N',
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

--MINI ODF
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'ETIQUETA_FOTO'
    ),
    'ETIQUETAS DE LAS FOTOS',
    'MINI ODF',
    'odf.png',
    '128',
    'N',
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

--FIN DEPARTAMENTO OPU

/
COMMIT;
/
