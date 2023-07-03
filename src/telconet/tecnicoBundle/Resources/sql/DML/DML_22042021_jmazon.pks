/**
 *
 * Se crean parametros para el proyecto de almacenamiento NFS
 *	 
 * @author Jonathan Mazon <jmazon@telconet.ec>
 * @version 1.0 22-04-2021
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
    21,
    'TelcosWeb',
    '593',
    'TN',
    'Tecnico',
    'InspeccionRadio',
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
    20,
    'TelcosWeb',
    '593',
    'MD',
    'Tecnico',
    'InspeccionRadio',
    'Activo',
    sysdate,
    'jmazon'
);

COMMIT;

/
