--wvera
--Modelo de equipo EXTENDER ONT ZXHN F680 V6
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='MODELOS_EQUIPOS_VALIDAR_MOVIL'),
        'Equipo ONT usado para la tarea INSTALAR EQUIPO',
        'ONT ZXHN',
        'ZXHN F680 V6',
        'CPE ONT',
        'EXTENDER_DUAL_BAND',
        'Activo',
        'wvera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S',
        NULL,
        NULL,
        NULL,
        'valor1 = descripción corta del equipo, valor2 = modelo del equipo, valor3 = tipo de equipo, valor4 = producto a validar, '
        || 'valor5 = disponible para registro'
);
--Modelo de equipo EXTENDER producto EXTENDER_DUAL_BAND -MODELO EXTENDER ZXHN H196A V9
Insert 
into DB_GENERAL.ADMI_PARAMETRO_DET 
values         
(
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (SELECT ID_PARAMETRO FROM DB_GENERAL.ADMI_PARAMETRO_CAB WHERE NOMBRE_PARAMETRO='MODELOS_EQUIPOS_VALIDAR_MOVIL'),
        'Equipo EXTENDER usado para la tarea INSTALAR EQUIPO',
        'EXTENDER ZXHN',
        'ZXHN H196A V9',
        'EXTENDER',
        'EXTENDER_DUAL_BAND',
        'Activo',
        'wvera',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        'S',
        NULL,
        NULL,
        NULL,
        'valor1 = descripción corta del equipo, valor2 = modelo del equipo, valor3 = tipo de equipo, valor4 = producto a validar, '
        || 'valor5 = disponible para registro'
);

COMMIT;

/