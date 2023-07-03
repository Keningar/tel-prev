<?php

namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
		/*if (true === $this->get('security.context')->isGranted('ROLE_135-1'))
        {
			return $this->render('planificacionBundle:Factibilidad:index.html.twig');
		}*/	
		
        if (true === $this->get('security.context')->isGranted('ROLE_133-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'planificacion','opcion_menu' =>$opcion_menu));
        }
		
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
}
