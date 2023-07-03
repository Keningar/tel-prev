--Cabecera para parámetros utilizados en actualización de coordenadas desde el móvil
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL',
                'Cabecera para parámetros utilizados en actualización de coordenadas desde el móvil',
                'TECNICO',
				'INSTALACION',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);

--Primer umbral de tiempo en minutos utilizado en la actualización de coordenadas
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL'
    ),
    'Primer umbral de tiempo en minutos utilizado en la actualización de coordenadas',
    'PRIMER_UMBRAL_ACTUALIZA_COORDENADAS',
    '30',
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


--Segundo umbral de tiempo en minutos utilizado en la actualización de coordenadas

INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL'
    ),
    'Segundo umbral de tiempo en minutos utilizado en la actualización de coordenadas',
    'SEGUNDO_UMBRAL_ACTUALIZA_COORDENADAS',
    '240',
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

--Alias nacional de GIS
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL'
    ),
    'Alias nacional de GIS utilizado para enviar correo',
    'ALIAS_NACIONAL_GIS',
    'gis_uio@telconet.ec;gis_gye@telconet.ec;gis_provincias@telconet.ec',
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


--Alias nacional de Megadatos
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL'
    ),
    'Alias nacional de Megadatos utilizado para enviar correo',
    'ALIAS_NACIONAL_MD',
    'supervisoresventasgye@netlife.net.ec;supervisoresventasuio@netlife.net.ec',
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


--Tiempo en horas para buscar servicios en estado de PreFactibilidad por la funcionalidad de actualización de coordenadas desde el móvil
INSERT INTO db_general.admi_parametro_det VALUES 
(
    db_general.seq_admi_parametro_det.NEXTVAL,
    (
        SELECT
            id_parametro
        FROM
            db_general.admi_parametro_cab
        WHERE
            nombre_parametro = 'PARAMETROS_ACTUALIZACION_COORDENADAS_MOVIL'
    ),
    'Tiempo en horas para buscar servicios en estado de PreFactibilidad gestionado desde el móvil',
    'HORAS_BUSQUEDA_JOB_PREFACTIB_MOVIL',
    '24',
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



--Cabecera para mapeo de mensajes de errores originados en Telcos+ al realizar una activación MD desde el móvil.
Insert 
into DB_GENERAL.ADMI_PARAMETRO_CAB
values         
(
                DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
                'MAPEO_ERRORES_ACTIVACION_MD',
                'Mensajes de errores originados en Telcos+ al realizar una activación MD',
                'SOPORTE',
				'MAPEO_ERRORES_ACTIVACION_MD',
				'Activo',
				'rmoranc',
				SYSDATE,
				'127.0.0.1',
				null,
				null,
				null
);


--Mapeo de error originado en Telcos+ al realizar una activación Md desde el móvil
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MAPEO_ERRORES_ACTIVACION_MD'),
        'Mapeo de error originado en Telcos+ al realizar una activación Md desde el móvil',
        'Error de RDA',
        'Ocurrió un error interno en servicio de RDA, favor reportar a soporte sistemas.',
        'SI',
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
        'En Valor1 se coloca el error o parte del error original que se recibe del ws de GDA. En valor2 se coloca el mensaje que se quiere mostrar al usuario. En Valor3; SI = Se muestra al usuario el mensaje configurado; NO = Se muestra al usuario el mensaje original.'
);

--Mapeo de error originado en Telcos+ al realizar una activación Md desde el móvil
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MAPEO_ERRORES_ACTIVACION_MD'),
        'Mapeo de error originado en Telcos+ al realizar una activación Md desde el móvil',
        'java.lang.ArrayIndexOutOfBoundsException',
        'Ocurrió un error interno en servicio de RDA, favor reportar a soporte sistemas.',
        'SI',
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
        'En Valor1 se coloca el error o parte del error original que se recibe del ws de GDA. En valor2 se coloca el mensaje que se quiere mostrar al usuario. En Valor3; SI = Se muestra al usuario el mensaje configurado; NO = Se muestra al usuario el mensaje original.'
);

--Mapeo de error originado en Telcos+ al realizar una activación Md desde el móvil
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO = 'MAPEO_ERRORES_ACTIVACION_MD'),
        'Mapeo de error originado en Telcos+ al realizar una activación Md desde el móvil',
        'com.jcraft.jsch.JSchException',
        'Ocurrió un error interno en servicio de RDA, favor reportar a soporte sistemas.',
        'SI',
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
        'En Valor1 se coloca el error o parte del error original que se recibe del ws de GDA. En valor2 se coloca el mensaje que se quiere mostrar al usuario. En Valor3; SI = Se muestra al usuario el mensaje configurado; NO = Se muestra al usuario el mensaje original.'
);

--PARAMETRO QUE SE UTILIZARA PARA VALIDAR LA ACTUALIZACION AUTOMATICA  DE LAS COORDENADAS DEL CLIENTE
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
    'PARAMETRO QUE SE UTILIZARA PARA VALIDAR LA ACTUALIZACION AUTOMATICA  DE LAS COORDENADAS DEL CLIENTE',
    'DISTANCIA_ACTUALIZA_COORDENADAS_CLIENTE',
    '30',
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


--PARÁMETRO DE REMITENTE DE CORREO UTILIZADO EN NOTIFICACIONES VÍA CORREO.
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
    'PARAMETRO DE REMITENTE DE CORREO UTILIZADO EN NOTIFICACIONES TELCOS+.',
    'REMITENTE_CORREO_NOTIFICACION_TELCOS',
    'notificaciones_telcos@telconet.ec',
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


COMMIT;