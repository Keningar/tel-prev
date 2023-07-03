/* Se realiza una nueva configuración, se habilita un nuevo usuario para el departamento de NETWORKING*/

insert into DB_TOKENSECURITY.application values(461,'APP.NW','ACTIVO',30);
insert into DB_TOKENSECURITY.user_token values(361,'NETWORKING','DBE11521ECF870162FFB1EB06D74FE42A8F734F32CA5CB39E88A97E1353BF543','Activo',461);
insert into DB_TOKENSECURITY.web_service values(521,'SoporteWSController','procesarAction',1,'ACTIVO',461);

commit;
