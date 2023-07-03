--==================================================================================================
--========================== Insert a la tabla de aplicacion =======================================
--==================================================================================================
INSERT
INTO DB_TOKENSECURITY.APPLICATION
  (
    ID_APPLICATION,
    NAME,
    STATUS,
    EXPIRED_TIME
  )
  VALUES
  (
    DB_TOKENSECURITY.SEQ_APPLICATION.NEXTVAL,
    'TELCOCRM',
    'ACTIVO',
    '240'
  );
--==================================================================================================
--==========================Insert a la tabla de web services=======================================
--==================================================================================================
INSERT
INTO DB_TOKENSECURITY.WEB_SERVICE
  (
    ID_WEB_SERVICE,
    SERVICE,
    METHOD,
    GENERATOR,
    STATUS,
    ID_APPLICATION
  )
  VALUES
  (
    DB_TOKENSECURITY.SEQ_WEB_SERVICE.NEXTVAL,
    'clienteTelcoCRM',
    'procesarAction',
    '1',
    'ACTIVO',
    (SELECT ID_APPLICATION
    FROM DB_TOKENSECURITY.APPLICATION
    WHERE NAME = 'TELCOCRM'
    )
  );


commit;
