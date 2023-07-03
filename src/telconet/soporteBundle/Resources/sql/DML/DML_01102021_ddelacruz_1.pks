/**
 * DEBE EJECUTARSE EN DB_TOKENSECURITY
 * Script para que la aplicacion "Extranet TN" se integre con generacion y validacion de Token
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 
 * @since 01-10-2021
 */

--Crear Aplicacion 
INSERT INTO db_tokensecurity.application (
    id_application,
    name,
    status,
    expired_time
) VALUES (
    db_tokensecurity.seq_application.nextval,
    'APP.EXTRANET',
    'ACTIVO',
    30
);

--Configurar clase GestionLdapWSController y relacionarlo con el APP.EXTRANET
INSERT INTO db_tokensecurity.web_service (
    id_web_service,
    service,
    method,
    generator,
    status,
    id_application
) VALUES (
    db_tokensecurity.seq_web_service.nextval,
    'GestionLdapWSController',
    'procesarAction',
    1,
    'ACTIVO',
    (
        SELECT
            id_application
        FROM
            db_tokensecurity.application
        WHERE
                name = 'APP.EXTRANET'
            AND status = 'ACTIVO'
    )
);

--Configurar Usuario/Clave EXTRANET/EXTRANET(sha256)
INSERT INTO db_tokensecurity.user_token (
    id_user_token,
    username,
    password,
    estado,
    application_id
) VALUES (
    db_tokensecurity.seq_user_token.nextval,
    'EXTRANET',
    '1863FA8BAE23F10FD76F298D431D9C7B0C9B26797D3FF153723B44F61A940C5F',
    'Activo',
    (
        SELECT
            id_application
        FROM
            db_tokensecurity.application
        WHERE
                name = 'APP.EXTRANET'
            AND status = 'ACTIVO'
    )
);

COMMIT;
/
