<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use telconet\schemaBundle\Entity\AdmiEmpresa;
use telconet\schemaBundle\Entity\AdmiOficina;
use telconet\schemaBundle\Entity\SistItemMenu;
use telconet\schemaBundle\Entity\SeguMenuPersona;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DefaultController extends Controller implements TokenAuthenticatedController
{    
    public function menuAction($opcion_menu)
    {
		/*
		if (true === $this->get('security.context')->isGranted('ROLE_78-1'))
        {
			return $this->render('soporteBundle:InfoCaso:index.html.twig');
		}	
		*/
        if (true === $this->get('security.context')->isGranted('ROLE_76-1'))
        {
            return $this->forward('seguridadBundle:Default:dashboard', array('modulo' =>'soporte','opcion_menu' =>$opcion_menu));
        }
		
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
    
    /**
     * inicio
     *
     * Método que retorna la pantalla del Dashboard de Soporte                        
     * 
     * @return Response        
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 08-09-2015
     */  
    public function inicioAction()
    {
        return $this->render('soporteBundle:Default:inicio.html.twig');
    }
}