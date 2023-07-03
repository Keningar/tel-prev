<?php
namespace TelconetSSO\TelconetSSOBundle\Security\User\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use TelconetSSO\TelconetSSOBundle\Entity\User;

class UserSsoProvider implements UserProviderInterface
{
    private $container; 

    public function __construct ($container)
    {
        $this->container = $container; 
    }

    /**
     * Obtiene informacion del usuario considerando canales
     *
     * @see \Symfony\Component\Security\Core\User\UserProviderInterface::loadUserByUsername()
     *
     * @version 1.0 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 Uso de option sso_ips_canales parametrizado en lugar de arreglo en codigo
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.2 Uso de parameter sso_ips_canales porque las options del SSO no estan cargadas al hacer check
     */
    public function loadUserByUsername($username)
    {
        $request = $this->container->get('request');
        $session = $request->getSession();

        $canales = $session->get('ips_canales');
        if (!$canales)
        {
            $canales = $this->container->getParameter('sso_ips_canales');
            $session->set('ips_canales', $canales);
        }

        $clientIp = $_SERVER['REMOTE_ADDR'];
        $xffIp    = "";
       
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $xffIp    = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        $sufijoSso = "";
        if (in_array($clientIp,$canales) || in_array($xffIp,$canales))
        {
            $sufijoSso = "_canales";
        }
        $gateway_login_url      = $this->container->getParameter('gateway_login_url');
        $gateway_logout_url     = $this->container->getParameter('gateway_logout_url');
        $service_endpoint_url   = $this->container->getParameter('service_endpoint_url');
        $proxy_host             = $this->container->getParameter('proxy_host');
        $proxy_port             = $this->container->getParameter('proxy_port');
        $proxy_username         = $this->container->getParameter('proxy_username');
        $proxy_password         = $this->container->getParameter('proxy_password');

        try
        {
            $agent = \Josso_Josso::getNewInstanceWithParams(
                    $gateway_login_url, $gateway_logout_url, $service_endpoint_url,
                    $proxy_host, $proxy_port, $proxy_username, $proxy_password);

            $josso_user = $agent->getUserInSession();
        }
        catch (Exception $e)
        {
            $josso_user = null;
        }

        if(!empty($josso_user))
        {
            $properties     = $josso_user->getProperties();
            $mail           = $josso_user->getProperty('mail');
            $descripcion    = $josso_user->getProperty('description');
            $cedula         = $josso_user->getProperty('cedula');
            $username       = $josso_user->getUsername();
            $user           = new User($cedula, $username, $descripcion, $mail);
        }
        else
        {
            throw new UsernameNotFoundException('The user is not authenticated on telconet');
        }
        return $user;

    }

    /*public function findUser($telcoId)
    {
        return $this->userManager->findUserBy(array('telconetID' => $telcoId));
    }*/

    public function refreshUser (UserInterface $user)
    {
       if (!$this->supportsClass(get_class($user)) || !$user->getUsername()) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername( $user->getUsername() );

        return $user;
    }

    public function supportsClass($class)
    {
        return $class ===  'TelconetSSO\TelconetSSOBundle\Entity\User';//$this->getClass();
    }
}
