--Envío de correo debe ser ejecutado por el DBA
GRANT ALL ON utl_mail TO db_SOPORTE;

--Creación de la ACL para la comunicación con middleware desde la base
BEGIN
    DBMS_NETWORK_acl_ADMIN.CREATE_ACL(
                                      acl => 'midd.xml',
                                      description => 'middleware Prod',
                                      principal => 'DB_SOPORTE',
                                      is_grant => true,
                                      privilege => 'connect'
                                     );
    DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl => 'midd.xml',
                                         principal => 'DB_SOPORTE',
                                         is_grant => true,
                                         privilege => 'resolve'
                                        );
    DBMS_NETWORK_acl_ADMIN.ASSIGN_ACL(
                                      acl => 'midd.xml',
                                      host => 'middleware.netlife.net.ec'
                                     );
    COMMIT;
END;

--Creación de la ACL para la comunicación con telcos desde la base
BEGIN

    DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl => 'utl_http.xml',
                                         principal => 'DB_SOPORTE',
                                         is_grant => true,
                                         privilege => 'resolve'
                                        );
	DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl => 'utl_http.xml',
                                         principal => 'DB_SOPORTE',
                                         is_grant => true,
                                         privilege => 'connect'
                                        );

    COMMIT;
END;

--Creación de la ACL para la comunicacion als Soc.i de CERT desde la base
BEGIN
    DBMS_NETWORK_acl_ADMIN.CREATE_ACL(
                                      acl => 'soc.xml',
                                      description => 'Soc Prod ',
                                      principal => 'DB_SOPORTE',
                                      is_grant => true,
                                      privilege => 'connect'
                                     );
    DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl => 'soc.xml',
                                         principal => 'DB_SOPORTE',
                                         is_grant => true,
                                         privilege => 'resolve'
                                        );
    DBMS_NETWORK_acl_ADMIN.ASSIGN_ACL(
                                      acl => 'soc.xml',
                                      host => 'soc.i.telconet.net'
                                     );
    COMMIT;
END;

--Creación de la ACL para el envío de correo desde la DB_SOPORTE
BEGIN

    DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl => 'Resolve_Access.xml',
                                         principal => 'DB_SOPORTE',
                                         is_grant => true,
                                         privilege => 'resolve'
                                        );
    DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl => 'Resolve_Access.xml',
                                         principal => 'DB_SOPORTE',
                                         is_grant => true,
                                         privilege => 'connect'
                                        );
    COMMIT;
END;

/