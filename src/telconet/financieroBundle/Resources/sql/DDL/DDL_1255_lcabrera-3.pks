/**
 * Script facilitado por Diana López necesario para la configuración de la ACL para Fox Premium.
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * @version 1.0
 */
BEGIN
    --dbms_network_acl_admin.create_acl(acl => 'resolve.xml',description => 'resolve acl', principal =>'TELCONET', is_grant => true, privilege => 'resolve');
    DBMS_NETWORK_ACL_ADMIN.assign_acl (acl          => '/sys/acls/utl_smtp.xml',
                                       HOST         => 'idp-cache.tbxapis.com');
    COMMIT;
END;
/
