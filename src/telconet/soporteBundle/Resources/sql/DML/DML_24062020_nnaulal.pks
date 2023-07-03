--Insertar parámetro del token en WS Controller
INSERT INTO db_tokensecurity.web_service VALUES 
( ( SELECT MAX(id_web_service) + 1 FROM db_tokensecurity.web_service ),
'SoporteWSController', 'procesarAction', 1, 'ACTIVO', 
( SELECT id_application FROM db_tokensecurity.application WHERE NAME = 'APP.TELCOGRAPH' ) );

COMMIT;

/
