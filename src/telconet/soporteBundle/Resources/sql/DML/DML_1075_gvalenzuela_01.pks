--Insert de la característica TAREA_SYS_CLOUD_CENTER.
INSERT INTO DB_COMERCIAL.ADMI_CARACTERISTICA (
    ID_CARACTERISTICA,
    DESCRIPCION_CARACTERISTICA,
    TIPO_INGRESO,
    ESTADO,
    FE_CREACION,
    USR_CREACION,
    TIPO
) VALUES (
     DB_COMERCIAL.SEQ_ADMI_CARACTERISTICA.NEXTVAL,
    'TAREA_SYS_CLOUD_CENTER',
    'T',
    'Activo',
     SYSDATE,
    'gvalenzuela',
    'SOPORTE'
);

--CREACIÓN DEL PÁRAMETRO CAB PARA LIMITAR LA GESTIÓN DE LAS TAREAS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_CAB (
    ID_PARAMETRO,
    NOMBRE_PARAMETRO,
    DESCRIPCION,
    MODULO,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_CAB.NEXTVAL,
    'USUARIOS LIMITADORES DE GESTION DE TAREAS',
    'USUARIOS LIMITADORES DE GESTION DE TAREAS',
    'SOPORTE',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
);

--CREACIÓN DEL PÁRAMETRO DET PARA LIMITAR LA GESTIÓN DE LAS TAREAS
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'USUARIOS LIMITADORES DE GESTION DE TAREAS'
    ),
    'DEPARTAMENTO LIMITADO DE GESTIÓN DE TAREAS',
    (
        SELECT ID_DEPARTAMENTO
            FROM DB_GENERAL.ADMI_DEPARTAMENTO
        WHERE ESTADO = 'Activo' AND NOMBRE_DEPARTAMENTO = 'Data Center Pac'
    ),
    'Data Center Pac',
    'telcoSys',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'USUARIOS LIMITADORES DE GESTION DE TAREAS'
    ),
    'DEPARTAMENTO LIMITADO DE GESTIÓN DE TAREAS',
    (
        SELECT ID_DEPARTAMENTO
            FROM DB_GENERAL.ADMI_DEPARTAMENTO
        WHERE ESTADO = 'Activo' AND NOMBRE_DEPARTAMENTO = 'Data Center Ti'
    ),
    'Data Center Ti',
    'telcoSys',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'USUARIOS LIMITADORES DE GESTION DE TAREAS'
    ),
    'DEPARTAMENTO LIMITADO DE GESTIÓN DE TAREAS',
    (
        SELECT ID_DEPARTAMENTO
            FROM DB_GENERAL.ADMI_DEPARTAMENTO
        WHERE ESTADO = 'Activo' AND NOMBRE_DEPARTAMENTO = 'Data Center Boc'
    ),
    'Data Center Boc',
    'telcoSys',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
);

INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET (
    ID_PARAMETRO_DET,
    PARAMETRO_ID,
    DESCRIPCION,
    VALOR1,
    VALOR2,
    VALOR3,
    ESTADO,
    USR_CREACION,
    FE_CREACION,
    IP_CREACION
) VALUES (
    DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
    (
        SELECT ID_PARAMETRO
            FROM DB_GENERAL.ADMI_PARAMETRO_CAB
        WHERE NOMBRE_PARAMETRO = 'USUARIOS LIMITADORES DE GESTION DE TAREAS'
    ),
    'DEPARTAMENTO LIMITADO DE GESTIÓN DE TAREAS',
    (
        SELECT ID_DEPARTAMENTO
            FROM DB_GENERAL.ADMI_DEPARTAMENTO
        WHERE ESTADO = 'Activo' AND NOMBRE_DEPARTAMENTO = 'Data Center Administracion'
    ),
    'Data Center Administracion',
    'telcoSys',
    'Activo',
    'gvalenzuela',
     SYSDATE,
    '127.0.0.1'
);

COMMIT;
/
