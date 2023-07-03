/**
 * Documentación DELETE admi_prod_carac_comportamiento
 *
 * Rollback de los parámetros de comportamiento de las caracteristicas de los productos de MD
 *
 * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
 * @version 1.0 08-07-2021
 */

DELETE FROM db_tokensecurity.user_token
where username = 'MOVIL_COMERCIAL';

DELETE FROM
db_tokensecurity.web_service
where service = 'ComercialMobileWSController'
and id_application = 462;

DELETE FROM
db_tokensecurity.web_service
where service = 'SmsWSController'
and id_application = 462;

COMMIT;
/
