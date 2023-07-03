<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Form\CallActivityType;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;

use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

class NotificacionesController extends Controller implements TokenAuthenticatedController
{
    /**
     * Lists all InfoCaso entities.
     *
     */
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();
        $em = $this->getDoctrine()->getManager('telconet_comunicacion');    
        
        if (true === $this->get('security.context')->isGranted('ROLE_83-1'))
        {
            return $this->render('soporteBundle:Default:notificaciones.html.twig', array(
                        ));
        }
        return $this->render('seguridadBundle:Exception:errorDeny.html.twig', array(
                                        'mensaje' => 'No tiene permisos para usar la aplicacion.'));
    }
    
}

