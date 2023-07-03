<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

class InfoServicioCambioPlanController extends Controller implements TokenAuthenticatedController
{ 
    
    public function cambioPlanAction(){
        $em = $this->getDoctrine()->getManager('telconet');
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
//        $entities = $this->getDoctrine()
//            ->getManager("telconet")
//            ->getRepository('schemaBundle:InfoServicioTecnico')
//            ->getServicios("","","","","",'Activo','0','1000',$idEmpresa,'');
        return $this->render('tecnicoBundle:InfoServicioCambioPlan:cambioPlan.html.twig', array(
//            'entities' => $entities
        ));
    }
    
    public function getServiciosCambioPlanAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $em = $this->get('doctrine')->getManager('telconet');
        
        $peticion = $this->get('request');
        
        $oficinaGrupo = $peticion->query->get('oficinaGrupo');        
        $plan = $peticion->query->get('plan');
        $estado = $peticion->query->get('estado');
        
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');

        // ...
        /* @var $serviceProcesoMasivo \telconet\tecnicoBundle\Service\ProcesoMasivoService */
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');
        $objJson = $serviceProcesoMasivo->generarJsonServiciosParaCambioPlan($idEmpresa, $oficinaGrupo, $plan, $start, $limit);
        $respuesta->setContent($objJson);
       
        return $respuesta;
        
    }
    
    public function getOficinaGrupoPorEmpresaAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoOficinaGrupo')
            ->generarJsonOficinaGrupoPorEmpresa($idEmpresa,"Activo",$start, 100);
        
        return $respuesta->setContent($objJson);
    }
    
    public function getPlanesAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $request = $this->get('request');
        $session  = $request->getSession();
        $em = $this->get('doctrine')->getManager('telconet');
        $empresa = $session->get('idEmpresa');
        
        $peticion = $this->get('request');
        
        $plan = $peticion->query->get('plan');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $estado= $peticion->query->get('estado');
        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoPlanCab')
            ->generarJsonPlanesPorEmpresa($plan,$empresa,$estado,$start, 200,$em);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    public function cambioPlanMasivoAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        // ...
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $clientIp = $peticion->getClientIp();
        // ...
        $idEmpresa = $session->get('idEmpresa');
        $prefijoEmpresa = $session->get('prefijoEmpresa');
        $usrCreacion = $session->get('user');
        
        $idsOficinas = $peticion->get('idsOficinas');
        
        $idsServicios = $peticion->get('idsServicios');
        $cantidadServicios = $peticion->get('cantidadServicios');
        
        $newPlanId = $peticion->get('planNuevo');
        $newPlanValor = $peticion->get('valorPlan');
        
        try {
            /* @var $serviceProcesoMasivo \telconet\tecnicoBundle\Service\ProcesoMasivoService */
            $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');
            $msg = $serviceProcesoMasivo->guardarServiciosPorCambioPlan($prefijoEmpresa, $idEmpresa, $idsOficinas, $newPlanId, $newPlanValor, $idsServicios, $cantidadServicios, $usrCreacion, $clientIp);
            return $respuesta->setContent("Se esta ejecutando el script de: <br> Cambio de Plan Masivo, favor espere el correo <br> de reporte general");
        } catch (\Exception $e) {
            return $respuesta->setContent("Error de Conexión con el Servidor");
        }      
        
        return $respuesta->setContent("OK");
    }
    
}