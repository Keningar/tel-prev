/**
 *
 * Se crean parametros para el proyecto de almacenamiento NFS
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 10-03-2021
 */


INSERT INTO db_general.ADMI_GESTION_DIRECTORIOS
(
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
)
VALUES
(
    db_general.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
    4,
    5,
    'TelcosWeb',
    '593',
    'TN',
    'Tecnico',
    'Activaciones',
    'Activo',
    sysdate,
    'jmazon'
);

INSERT INTO db_general.ADMI_GESTION_DIRECTORIOS
(
    ID_GESTION_DIRECTORIO,
    CODIGO_APP,
    CODIGO_PATH,
    APLICACION,
    PAIS,
    EMPRESA,
    MODULO,
    SUBMODULO,
    ESTADO,
    FE_CREACION,
    USR_CREACION
)
VALUES
(
    db_general.SEQ_ADMI_GESTION_DIRECTORIOS.nextval,
    4,
    6,
    'TelcosWeb',
    '593',
    'MD',
    'Tecnico',
    'Activaciones',
    'Activo',
    sysdate,
    'jmazon'
);

INSERT INTO db_general.admi_parametro_cab (
    id_parametro,
    nombre_parametro,
    descripcion,
    modulo,
    proceso,
    estado,
    usr_creacion,
    fe_creacion,
    ip_creacion
) VALUES (
    db_general.seq_admi_parametro_cab.nextval,
    'GESTION_DIRECTORIOS',
    'Parametros a usar para la activacion y guardado de imagenes al gluster',
    'TECNICO',
    'ACTIVACION',
    'Activo',
    'jmazon',
    sysdate,
    '127.0.0.1'
);
INSERT INTO DB_GENERAL.ADMI_PARAMETRO_DET
    (
        ID_PARAMETRO_DET,
        PARAMETRO_ID,
        DESCRIPCION,
        VALOR1,
        VALOR2,
        VALOR3,
        VALOR4,
        VALOR5,
        ESTADO,
        USR_CREACION,
        FE_CREACION,
        IP_CREACION,
        USR_ULT_MOD,
        FE_ULT_MOD,
        IP_ULT_MOD,
        EMPRESA_COD
    )
    VALUES
    (
        DB_GENERAL.SEQ_ADMI_PARAMETRO_DET.NEXTVAL,
        (
        SELECT ID_PARAMETRO
          FROM DB_GENERAL.ADMI_PARAMETRO_CAB
         WHERE NOMBRE_PARAMETRO = 'GESTION_DIRECTORIOS'
           AND MODULO = 'TECNICO'
           AND PROCESO = 'ACTIVACION'
           AND ESTADO = 'Activo'
        ),
        'ACTIVACIONES',
        'Tecnico',--Modulo
        'Activaciones',--SubModulo
        'TelcosWeb',--aplicacion
        '593',--pais
        'guardarArchivo',--operacion
        'Activo',
        'jmazon',
        SYSDATE,
        '127.0.0.1',
        NULL,
        NULL,
        NULL,
        NULL
    );



COMMIT;

/
