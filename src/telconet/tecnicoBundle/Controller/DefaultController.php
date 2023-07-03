<?php

namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller implements TokenAuthenticatedController
{    
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
	
    public function menuAction($opcion_menu)
    {
        if (true === $this->get('security.context')->isGranted('ROLE_152-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'tecnico','opcion_menu' =>$opcion_menu));
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
}