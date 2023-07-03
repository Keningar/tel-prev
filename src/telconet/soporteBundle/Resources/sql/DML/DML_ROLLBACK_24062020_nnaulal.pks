--Eliminar parámetro del token en WS Controller
DELETE db_tokensecurity.web_service 
WHERE id_application = ( SELECT id_application FROM db_tokensecurity.application WHERE NAME = 'APP.TELCOGRAPH' )
AND service = 'SoporteWSController' AND method='procesarAction' AND generator=1 AND status='ACTIVO';

COMMIT;

/
