<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Clase Controlador InfoElementoTransceiver
 * 
 * Clase donde se implementara toda consulta y acciones
 * para los elementos transceiver
 * 
 * @author Duval Medina C. <dmedina@telconet.ec>
 * @version 1.0 2016-06-23 - Basada en InfoElementoTransceiverController
 */
class InfoElementoTransceiverController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Funcion que sirve para que el grid del index 
     * cargue.
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-06-23
     */    
    public function indexTransceiverAction(){
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad"); 
        
        $rolesPermitidos = array();
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                       ->searchItemMenuByNombreModulo("transceiver", "1");
        return $this->render('tecnicoBundle:InfoElementoTransceiver:index.html.twig', array(
            'rolesPermitidos' => $rolesPermitidos,
            'item'            => $entityItemMenu
        ));
    }
    
    /**
     * Funcion que consulta los cpes tanto en 
     * Telcos como en el Naf, con sus respectivos estados.
     * 
     * @author Duval Medina C. <dmedina@telconet.ec>
     * @version 1.0 2016-06-23
     */
    public function getEncontradosTransceiverAction(){
       $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $session = $this->get('session');
        $session->save();
        session_write_close();
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emNaf = $this->getDoctrine()->getManager('telconet_naf');
        $emComercial = $this->getDoctrine()->getManager('telconet');
        $tipoActivo = "AF";//Activo fijo "TRANSCEIVER";
        
        $peticion = $this->get('request');
        
        $serial = $peticion->query->get('serial');
        $modeloElemento = $peticion->query->get('modeloElemento');
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                         ->generarJsonElementoTransceiver(array(
                                                        'modeloElemento' => $modeloElemento,
                                                        'tipoActivo'     => $tipoActivo,
                                                        'serial'         => strtoupper($serial),
                                                        'start'          => $start,
                                                        'limit'          => $limit,
                                                        'idEmpresa'      => $idEmpresa,
                                                        'emComercial'    => $emComercial
                                                        ));
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
}