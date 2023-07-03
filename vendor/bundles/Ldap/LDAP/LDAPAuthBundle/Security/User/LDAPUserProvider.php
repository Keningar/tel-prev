<?php

namespace LDAP\LDAPAuthBundle\Security\User;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Session;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class LDAPUserProvider implements UserProviderInterface
{
    /** @var \Symfony\Component\HttpFoundation\Session\Session */
    private $session;
    
    public function __construct(Session $session)
    {
      $this->session = $session;
    }
    public function loadUserByUsername($username)
    {
        error_log('ldap user provider');
//         if(isset($_POST['_password'])){
// 	    $this->session->set('user_password',$_POST['_password']);
//         }
//         $password = $this->session->get('user_password');
	return new LDAPUser($username, '');
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof LDAPUser) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'LDAP\LDAPAuthBundle\Security\User\LDAPUser';
    }
}