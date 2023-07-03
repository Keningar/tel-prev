<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiCanton;
use telconet\schemaBundle\Form\AdmiCantonType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class AdmiCantonJurisdiccionController extends Controller
{ 
    
    public function getCantonesJurisdiccionesAction($idJurisdiccion)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:AdmiCantonJurisdiccion')
            ->generarJsonCantonesJurisdicciones($idJurisdiccion,"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}