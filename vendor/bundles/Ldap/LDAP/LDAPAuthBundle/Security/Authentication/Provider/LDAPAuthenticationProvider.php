<?php 

namespace LDAP\LDAPAuthBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LDAPAuthenticationProvider extends DaoAuthenticationProvider
{
    /**
     * {@inheritdoc}
     */
    protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
    {   
	error_log('ldap check authentication');
	$username = $token->getUsername();
	$password = $token->getCredentials();
	
	// Application specific LDAP login
	$app_user = 'cn=admin,dc=telconet,dc=net';
	$app_pass = 's3rv3r*ld4p+';

        // connect to directory services
        $ldap_conn = ldap_connect('172.24.4.41', 389);
        if ($ldap_conn === FALSE){
		throw new BadCredentialsException("Couldn't connect to LDAP service");
	}

	// Bind as application
	$bind_status = ldap_bind($ldap_conn, $app_user, $app_pass);
	if ($bind_status === FALSE) {
            ldap_close($ldap_conn);
	    throw new BadCredentialsException("Couldn't bind to LDAP as application user");
	}
	
	// Find the user's DN
	// See the note above about the need to LDAP-escape $username!
	$query = "(&(uid=" . $username . ")(objectClass=telcoPerson))";
	$search_base = "ou=UsuarioInterno,dc=telconet,dc=net";
	$search_status = ldap_search(
	    $ldap_conn, $search_base, $query, array('dn')
	);
	if ($search_status === FALSE) {
            ldap_close($ldap_conn);
	   throw new BadCredentialsException("Search on LDAP failed");
	}
	 
	// Pull the search results
	$result = ldap_get_entries($ldap_conn, $search_status);
	if ($result === FALSE) {
            ldap_close($ldap_conn);
	    throw new BadCredentialsException("Couldn't pull search results from LDAP");
	}
	 
	if ((int) @$result['count'] > 0) {
	    // Definitely pulled something, we don't check here
	    //     for this example if it's more results than 1,
	    //     although you should.
	    $userdn = $result[0]['dn'];
	}
	 
	if (trim((string) $userdn) == '') {
            ldap_close($ldap_conn);
	    throw new BadCredentialsException("Empty DN. Something is wrong.");
	}
	 
	// Authenticate with the newly found DN and user-provided password
	$auth_status = ldap_bind($ldap_conn, $userdn, $password);
	if ($auth_status === FALSE) {
            ldap_close($ldap_conn);
	    throw new BadCredentialsException("Couldn't bind to LDAP as user!");
	}
	
        ldap_close($ldap_conn);
        
	return true;

        //if ($ldap_conn) {
            
            // attempt binding
            //$binding = @ldap_bind($ldap_conn, $token->getUsername(), $token->getCredentials());

            //if ($binding) {
                // authenticated
                //return true;
            //}
        //}
        
        //ldap_close($ldap_conn);

        // not authenticated
        //throw new BadCredentialsException("Incorrect username or password.");
    }
}
