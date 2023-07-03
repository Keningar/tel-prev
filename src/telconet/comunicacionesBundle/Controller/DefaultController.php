<?php

namespace telconet\comunicacionesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

/**
 * Default controller.
 *
 * Controlador que se encargará de administrar las opciones del menú
 * de Comunicaciones
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 27-07-2015
 */
class DefaultController extends Controller
{
    /**
     * Documentación para el método 'menuAction'.
     *
     * Muestra el menu de tercer nivel que contiene la sección de
     * Comunicaciones.
     *
     * @param string $strOpcionMenu Opción activa del menu.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-07-2015
     */
    public function menuAction($strOpcionMenu)
    {
        if (true === $this->get('security.context')->isGranted('ROLE_292-1'))
        {
            return $this->forward( 'seguridadBundle:Default:dashboard', array('modulo' => 'comunicaciones', 'opcion_menu' => $strOpcionMenu));
        }
		
        return $this->render( 'seguridadBundle:Exception:errorDeny.html.twig', array('mensaje' => 'No tiene permisos para usar la aplicacion.') );
    }
}
