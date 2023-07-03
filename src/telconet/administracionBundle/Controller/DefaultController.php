<?php

namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller implements TokenAuthenticatedController
{
    
    public function ajaxConsultarErrorLogAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        if ($this->container->hasParameter('error_log_path'))
        {
            $error_log_path = $this->container->getParameter('error_log_path');
        }
        else
        {
            $hostname = gethostname();
            $error_log_path = '/var/log/httpd/'.$hostname.'-error_log';
        }
        $cmd = 'tail -5 '.$error_log_path;
        $output = shell_exec($cmd);
        //print_r($output);
        $respuesta->setContent(nl2br($output));
        return $respuesta;
    }
    
    public function menuAction($opcion_menu)
    {		
        if (true === $this->get('security.context')->isGranted('ROLE_1-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'administracion','opcion_menu' =>$opcion_menu));
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
}
