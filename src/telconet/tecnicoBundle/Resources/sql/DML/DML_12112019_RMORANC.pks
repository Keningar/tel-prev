--Cabecera días de bloqueo de bobina segun la fecha de despacho
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'DIAS_BLOQUEO_BOBINA_DESPACHO',
                'Dias de bloqueo de bobina segun la fecha de despacho',
                'TECNICO',
				'INSTALACION_SOPORTE',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--Detalle de días de bloqueo de bobina segun la fecha de despacho
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'DIAS_BLOQUEO_BOBINA_DESPACHO'),
                'Dias de bloqueo de bobina segun la fecha de despacho',
                '7',
				NULL,
				NULL,
				NULL,
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL
);


--Cabecera para cantidad de bobina disponible para bloquearse.
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'CANTIDAD_BLOQUEO_BOBINA',
                'Cantidad de bobina mínima disponible para bloquearse',
                'TECNICO',
				'INSTALACION_SOPORTE',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--Detalle para cantidad de bobina disponible para bloquearse.
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
                (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'CANTIDAD_BLOQUEO_BOBINA'),
                'Cantidad de bobina mínima disponible para bloquearse',
                '80',
				NULL,
				NULL,
				NULL,
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL,
				NULL
);


--Detalle de número de bobinas a visualizar para tareas de instalación
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
    'Número de bobinas a visualizar en una tarea de instalación',
    'NUMERO_BOBINAS_INSTALACION',
    '2',
    NULL,
    NULL,
    'Inactivo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);


--Detalle del estado del número de bobinas a visualizar para tareas de instalación
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
    'Estado para el número de bobinas a visualizar en una tarea de instalación',
    'ESTADO_NUMERO_BOBINAS_INSTALACION',
    'Inactivo',
    NULL,
    NULL,
    'Activo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);


--Detalle de número de bobinas a visualizar para tareas de soporte
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
    'Número de bobinas a visualizar en una tarea de soporte',
    'NUMERO_BOBINAS_SOPORTE',
    '3',
    NULL,
    NULL,
    'Inactivo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);


--Detalle del estado del número de bobinas a visualizar para tareas de soporte
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
    'Estado para el número de bobinas a visualizar en una tarea de soporte',
    'ESTADO_NUMERO_BOBINAS_SOPORTE',
    'Activo',
    NULL,
    NULL,
    'Activo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);


--Detalle de cantidad de bobina a utilizar para una instalación Megadatos
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
    'Cantidad de bobina a utilizar para una instalación Megadatos',
    'CANTIDAD_BOBINA_INSTALACION_MD',
    '300',
    NULL,
    NULL,
    'Activo',
    'rmoranc',
    SYSDATE,
    '127.0.0.1', 
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL
);

commit;

/
