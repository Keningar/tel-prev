<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

class AutorizarHorasSoporteController extends Controller implements TokenAuthenticatedController
{

    /**
     * Función que muestra la pantalla para autorizar o rechazar solicitudes
     * de modificación de horas a un soporte.
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 1.0
     * @since 22-12-2022
     */
    public function indexAction()
    {
        return $this->render('comercialBundle:AutorizarHorasSoporte:index.html.twig');
    }
}