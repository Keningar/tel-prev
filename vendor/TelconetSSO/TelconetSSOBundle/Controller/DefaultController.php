<?php

namespace TelconetSSO\TelconetSSOBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

class DefaultController extends Controller
{

    /**
     * Verifica autenticacion considerando canales y nodo para balanceo de carga
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @version 1.0 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 Uso de option sso_ips_canales parametrizado en lugar de arreglo en codigo
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.2 Uso de parameter sso_ips_canales porque las options del SSO no estan cargadas al hacer check
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.3 2017-03-18 Se almacena en cookie JOSSO_NODE el parametro de request 'josso_node' para consumir WS de IDP con balanceo de carga
     */
    public function checkAction()
    {
        $request = $this->get('request');
        $session = $request->getSession();

        $canales = $session->get('ips_canales');
        if (!$canales)
        {
            $canales = $this->container->getParameter('sso_ips_canales');
            $session->set('ips_canales', $canales);
        }

        $clientIp   = $_SERVER['REMOTE_ADDR'];
        $sufijoSso  = "";
        if (in_array($clientIp,$canales))
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

        $success_signin_url     = $this->container->getParameter('success_signin_url');

        try
        {
            $request        = $this->getRequest();
            
            $assertionId    = $request->query->get('josso_assertion_id');
            if (!$assertionId)
            {
                throw $this->createNotFoundException('The product does not exist');
            }
            
            // SQA: Se obtiene josso_node del request, se guarda en cookie
            $josso_node = $request->query->get('josso_node');
            setcookie("JOSSO_NODE", $josso_node, 0, "/");
            $_COOKIE['JOSSO_NODE'] = $josso_node;
            
            $agent = \Josso_Josso::getNewInstanceWithParams(
                            $gateway_login_url, $gateway_logout_url, $service_endpoint_url,
                            $proxy_host, $proxy_port, $proxy_username, $proxy_password);
            $ssoSessionId = $agent->resolveAuthenticationAssertion($assertionId);

            setcookie("JOSSO_SESSIONID", $ssoSessionId, 0, "/"); // session cookie ...

            // aqui se debe setear en sesion algun parametro para ponerlo en el token

            $request->query->set('ssoSessionId',$ssoSessionId);

            //$url = $request->getSession()->get('JOSSO_ORIGINAL_URL');
            $url_success_signin = $request->getUriForPath('/'.$success_signin_url);
            $sign_in_url = !empty($url) ? $url : $url_success_signin;

            return $this->redirect($sign_in_url);
        }
        catch(SoapFault $e)
        {
            setcookie("JOSSO_SESSIONID",'', 0, "/"); // session cookie ...
            setcookie("JOSSO_NODE", '', 0, "/"); // SQA: Se define cookie de josso_node en blanco
            return $this->redirect('_security_login');
        }
    }

    /**
     * Cierra sesion considerando canales
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @version 1.0 Version Inicial
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.1 Uso de option sso_ips_canales parametrizado en lugar de arreglo en codigo
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.2 Uso de parameter sso_ips_canales porque las options del SSO no estan cargadas al hacer check
     *
     * @author Luis Tama <ltama@telconet.ec>
     * @version 1.3  2017-03-18 Se limpia cookie JOSSO_NODE
     */
    public function logoutAction()
    {
        $request = $this->get('request');
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

        $gateway_logout_url     = $this->container->getParameter('gateway_logout_url'.$sufijoSso);
        $success_signout_url    = $this->container->getParameter('success_signout_url'.$sufijoSso);
        //if($this->get("security.context")->getToken()->isAuthenticated())
        //{ 
            $SignOutUrlAbs = $this->get('router')->generate($success_signout_url,array(''=>''),true);
            $logoutUrl = $gateway_logout_url. '?josso_back_to=' . $SignOutUrlAbs;
            $logoutUrl = $logoutUrl . $this->createFrontChannelParams();
            // Clear session cookie ...
            setcookie("JOSSO_SESSIONID", '', 0, "/"); 
            setcookie("JOSSO_NODE", '', 0, "/"); // SQA: Se define cookie de josso_node en blanco
            $this->get("request")->getSession()->invalidate();
            $this->get("security.context")->setToken(null);
            return $this->redirect($logoutUrl);
        //}
    }

    public function createFrontChannelParams()
    {
        // Add some request parameters like host name
        $host = $_SERVER['HTTP_HOST'];
        $params = '&josso_partnerapp_host=' . $host;
        return $params;
        // TODO : Support josso_partnerapp_ctx param too ?
    }

}
