<?php

/*
 * This file is part of the FOSFacebookBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace TelconetSSO\TelconetSSOBundle\Security\Firewall;

use Symfony\Component\Security\Http\Firewall\ListenerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

use Symfony\Component\Security\Http\RememberMe\RememberMeServicesInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\SessionUnavailableException;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TelconetSSO\TelconetSSOBundle\Security\Authentication\Token\UserSSOToken;
use TelconetSSO\TelconetSSOBundle\Entity\User;

/**
 * Telconet SSO authentication listener.
 */
class TelconetSSOListener /*extends AbstractAuthenticationListener */implements ListenerInterface
{

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager,
            SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey,
            AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler,
            array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null)
    {
        if (empty($providerKey)) {
            throw new \InvalidArgumentException('$providerKey must not be empty.');
        }

        $this->securityContext = $securityContext;
        $this->authenticationManager = $authenticationManager;
        $this->sessionStrategy = $sessionStrategy;
        $this->providerKey = $providerKey;
        $this->successHandler = $successHandler;
        $this->failureHandler = $failureHandler;
        $this->options = array_merge(array(
            'check_path'                     => '/login_check',
            'login_path'                     => '/login',
            'always_use_default_target_path' => false,
            'default_target_path'            => '/',
            'target_path_parameter'          => '_target_path',
            'use_referer'                    => false,
            'failure_path'                   => null,
            'failure_forward'                => false,
            'require_previous_session'       => true,
        ), $options);
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
        $this->httpUtils = $httpUtils;
    }

    /**
     * Maneja los eventos de autenticacion considerando canales
     *
     * @see \Symfony\Component\Security\Http\Firewall\ListenerInterface::handle()
     *
     * @version 1.0 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 Uso de option sso_ips_canales parametrizado en lugar de arreglo en codigo
     */
    public function handle(GetResponseEvent $event)
    {
	//bypass session validation idp
        if ($this->securityContext->getToken())
        {
            if ($this->securityContext->getToken()->isAuthenticated())
            {
               return; // pasar-->peticion
            }
       }//fin bypass

        //esto es necesario para obtener el objeto Session
        $request = $event->getRequest();
        $session = $request->getSession();

        $canales = $session->get('ips_canales');
        if (!$canales)
        {
            $canales = $this->options['sso_ips_canales'];
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

        $gateway_login_url      = $this->options['gateway_login_url'];
        $gateway_logout_url     = $this->options['gateway_logout_url'];
        $service_endpoint_url   = $this->options['service_endpoint_url'];
        $proxy_host             = $this->options['proxy_host'];
        $proxy_port             = $this->options['proxy_port'];
        $proxy_username         = $this->options['proxy_username'];
        $proxy_password         = $this->options['proxy_password'];

        $security_check_module  = $this->options['security_check_module'];

        //Instancio un objeto Josso, necesario para verificar en el IDP si existe una session valida.
        $agent = \Josso_Josso::getNewInstanceWithParams(
                $gateway_login_url, $gateway_logout_url, $service_endpoint_url,
                $proxy_host, $proxy_port, $proxy_username, $proxy_password);
        $josso_user = $agent->getUserInSession();    //verificando la Cookie JOSSO_SESSIONID

        if (!is_null($josso_user))
        {
            //Verificar si la session local es valida.
            if($this->securityContext->getToken())
            {
                if($this->securityContext->getToken()->isAuthenticated())
                {
                    return;    //pasar-->peticion
                }
            }
            else
            {
                $properties     = $josso_user->getProperties();
                $mail           = $josso_user->getProperty('mail');
                $descripcion    = $josso_user->getProperty('description');
                $cedula         = $josso_user->getProperty('cedula');
                $username       = $josso_user->getUsername();

                $user           = new User($cedula, $username, $descripcion, $mail);

                $session->set('user_sso',$user);
                $tokensen = new UserSSOToken($username,$user->getRoles()); //('IS_AUTHENTICATED_FULLY','ROLE_USER'));
                $this->securityContext->setToken($tokensen);

                $this->securityContext->getToken()->setUser($user);
                $this->securityContext->getToken()->setAuthenticated(true);
                return;
            }
        }
        else
        {
            $session->set('JOSSO_ORIGINAL_URL' , $request->getRequestUri() );
            $host       = $_SERVER['HTTP_HOST'];
            $params     = '&josso_partnerapp_host=' . $host;
            $loginUrl   = $this->options['gateway_login_url'.$sufijoSso];
            //$securityCheckUrl=$this->get('router')->generate($security_check_module,array(''=>''),true);
            $loginUrl   = $loginUrl."?josso_back_to=".$request->getUriForPath('/'.$security_check_module).$params;
            //$loginUrl = $loginUrl."?josso_back_to=".$security_check_module.$params;
            $event->setResponse(new RedirectResponse($loginUrl));
        }
    }

}
