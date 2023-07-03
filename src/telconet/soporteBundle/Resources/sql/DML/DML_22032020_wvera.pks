INSERT INTO db_tokensecurity.web_service VALUES ( ( SELECT MAX(id_web_service) + 1 FROM db_tokensecurity.web_service ),
'ComercialWSController', 'procesarAction', 1, 'ACTIVO', ( SELECT id_application FROM db_tokensecurity.application WHERE NAME = 'ec.telconet.mobile.telcos.operaciones' ) );

COMMIT