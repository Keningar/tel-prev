<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiArea;
use telconet\schemaBundle\Form\AdmiAreaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class VerPlanificacionController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_138-1")
    */
    public function indexAction()
    {           
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("138", "1");

        return $this->render('planificacionBundle:VerPlanificacion:index.html.twig', array(
             'item' => $entityItemMenu
        ));
    }
    
    /**
    * @Secure(roles="ROLE_138-107")
    */
    public function getEventosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $page = $peticion->query->get('page');
        
        $startDate = $peticion->query->get('startDate');
        $endDate = $peticion->query->get('endDate');    
        $estado = $peticion->query->get('estado');    
        $login = ($peticion->query->get('login2') ? $peticion->query->get('login2') : "");
        $cantonId = ($peticion->query->get('cantonId') ? $peticion->query->get('cantonId') : "");    
        $parroquiaId = ($peticion->query->get('parroquiaId') ? $peticion->query->get('parroquiaId') : "");    
        $sectorId = ($peticion->query->get('sectorId') ? $peticion->query->get('sectorId') : "");    
        $vendedorId = ($peticion->query->get('vendedorId') ? $peticion->query->get('vendedorId') : "");    

        $em = $this->getDoctrine()->getManager("telconet");        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonVerPlanificacion($em, $start, $limit, $startDate, $endDate, $estado, $login, $cantonId, $parroquiaId, $sectorId, $vendedorId,$codEmpresa);
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
}