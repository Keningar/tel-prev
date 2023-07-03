/************************PROCEDIMIENTO BASE DE DATOS PARA PROCESO DE FACTURACION*********************************/

grant select, update, insert on DB_FINANCIERO.INFO_CONSUMO_CLOUD_DET to db_comercial;
grant select, update, insert on DB_FINANCIERO.INFO_CONSUMO_CLOUD_CAB to db_comercial;

grant select on DB_FINANCIERO.SEQ_INFO_CONSUMO_CLOUD_DET to db_comercial;
grant select on DB_FINANCIERO.SEQ_INFO_CONSUMO_CLOUD_CAB to db_comercial;  
/*************************************************IMPORTANTE!*************************************************
*********************PERMISOS PARA LA BASE DE PRODUCCIÓN PARA QUE CONSUMA LOS WS NETVOICE*********************
**************************************************************************************************************/
BEGIN
    --dbms_network_acl_admin.create_acl(acl => 'resolve.xml',description => 'resolve acl', principal =>'TELCONET', is_grant => true, privilege => 'resolve');
    DBMS_NETWORK_ACL_ADMIN.assign_acl (acl          => 'utl_http.xml',
                                       HOST         => '192.168.182.11',
                                       lower_port   => 8090,
                                       upper_port   => 8090);
    COMMIT;
END;
/
/* Formatted on 18/5/2018 10:00:26 (QP5 v5.318) */ 
BEGIN
    DBMS_NETWORK_ACL_ADMIN.add_privilege (acl          => 'utl_http.xml',
                                          principal    => 'DB_COMERCIAL',
                                          is_grant     => TRUE,
                                          privilege    => 'connect',
                                          start_date   => NULL,
                                          end_date     => NULL);
END;

BEGIN
    DBMS_NETWORK_ACL_ADMIN.add_privilege (acl          => 'utl_http.xml',
                                          principal    => 'DB_COMERCIAL',
                                          is_grant     => TRUE,
                                          privilege    => 'resolve',
                                          start_date   => NULL,
                                          end_date     => NULL);
END;
/
COMMIT;
