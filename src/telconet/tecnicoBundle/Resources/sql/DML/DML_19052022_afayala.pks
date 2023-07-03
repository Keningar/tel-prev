/**
 * Documentación para crear submodulo para NFS
 *
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.0 19-05-2022
 */

-- Creación de parámetros para el proyecto de almacenamiento NFS
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
    61, --61
    'TelcosWeb',
    '593',
    'MD',
    'Tecnico',
    'SolicitudEquipo',
    'Activo',
    sysdate,
    'afayala'
);

COMMIT;

/

