GRANT READ, WRITE ON DIRECTORY SYS.DIR_MIGRACION_I_PROTEGIDO TO DB_INFRAESTRUCTURA;
GRANT READ, WRITE ON DIRECTORY SYS.DIR_MIGRACION_I_PROTEGIDO TO DB_GENERAL;
GRANT EXECUTE ON DB_FINANCIERO.FNKG_TYPES TO DB_INFRAESTRUCTURA;
GRANT EXECUTE ON SYS.UTL_FILE to DB_INFRAESTRUCTURA;
GRANT EXECUTE ON DB_INFRAESTRUCTURA.INKG_TYPES TO DB_COMERCIAL;
GRANT UPDATE ON DB_COMERCIAL.ADMI_NUMERACION TO DB_INFRAESTRUCTURA;

--Creación de la ACL para la comunicación con el web service de GMS desde la base de producción 
BEGIN
    DBMS_NETWORK_ACL_ADMIN.CREATE_ACL ( acl         => 'sysdba-ws_gms.xml', 
                                        description => 'Permisos para el ws de GMS', 
                                        principal   => 'DB_INFRAESTRUCTURA', 
                                        is_grant    => TRUE, 
                                        privilege   => 'connect');
    DBMS_NETWORK_acl_ADMIN.ADD_PRIVILEGE(
                                         acl        => 'sysdba-ws_gms.xml',
                                         principal  => 'DB_INFRAESTRUCTURA',
                                         is_grant   => TRUE,
                                         privilege  => 'resolve'
                                        );
    DBMS_NETWORK_ACL_ADMIN.ASSIGN_ACL(
                                        acl         => 'sysdba-ws_gms.xml',
                                        host        => '34.211.179.77',
                                        lower_port  => 80,
                                        upper_port  => 80
                                     );
    COMMIT;
END;
/