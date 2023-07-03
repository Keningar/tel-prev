/**
 * DEBE EJECUTARSE EN DB_TOKENSECURITY
 * Script para eliminar registros creados para que la aplicacion "Extranet TN"
 * se integre con generacion y validacion de Token
 * @author David De La Cruz <ddelacruz@telconet.ec>
 * @version 1.0 
 * @since 01-10-2021
 */

--Configurar Usuario/Clave EXTRANET/EXTRANET(sha256)
DELETE FROM db_tokensecurity.user_token
WHERE
    application_id = (
        SELECT
            id_application
        FROM
            db_tokensecurity.application
        WHERE
                name = 'APP.EXTRANET'
            AND status = 'ACTIVO'
    );

--Eliminar clase GestionLdapWSController relacionada con el APP.EXTRANET
DELETE FROM db_tokensecurity.web_service
WHERE
    id_application = (
        SELECT
            id_application
        FROM
            db_tokensecurity.application
        WHERE
                name = 'APP.EXTRANET'
            AND status = 'ACTIVO'
    );

--Eliminar Aplicacion 
DELETE FROM db_tokensecurity.application
WHERE
        name = 'APP.EXTRANET'
    AND status = 'ACTIVO';

COMMIT;
/