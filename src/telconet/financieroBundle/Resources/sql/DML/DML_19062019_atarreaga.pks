/**
 * @author Alex Arreaga <atarreaga@telconet.ec>
 * @version 1.0
 * @since 17-06-2019    
 * Se crea la sentencia DML para asociar el nuevo ws con el usuario 'WS-RDA'.
 */

 --Se asocia 'FinancieroWSController' con el usuario 'WS-RDA'.
INSERT INTO DB_TOKENSECURITY.WEB_SERVICE (
  ID_WEB_SERVICE,
  SERVICE,
  METHOD,
  GENERATOR,
  STATUS,
  ID_APPLICATION
) VALUES (
    DB_TOKENSECURITY.SEQ_WEB_SERVICE.NEXTVAL,
    'FinancieroWSController',
    'procesarAction',
    1,
    'ACTIVO',
    (SELECT ID_APPLICATION 
    FROM DB_TOKENSECURITY.APPLICATION 
    WHERE NAME='WS-RDA')
);

COMMIT;
/  
