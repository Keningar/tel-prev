<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoTramo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class InfoTramoController extends Controller
{ 
    public function getTramosAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoTramo')
            ->generarJsonTramosParaTareas();
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
}