<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\planificacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class VerAsignacionesController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_140-1")
    */
    public function indexAction()
    {
    $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_139-111'))
		{
	$rolesPermitidos[] = 'ROLE_139-111';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_139-112'))
		{
	$rolesPermitidos[] = 'ROLE_139-112';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_140-113'))
		{
	$rolesPermitidos[] = 'ROLE_140-113';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_140-114'))
		{
	$rolesPermitidos[] = 'ROLE_140-114';
	}
	
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("140", "1");
        
        return $this->render('planificacionBundle:VerAsignaciones:index.html.twig', array(
             'item' => $entityItemMenu,
             'rolesPermitidos' => $rolesPermitidos
        ));
    }
            
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_140-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');   
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        $fechaDesdeAsig = explode('T',$peticion->query->get('fechaDesdeAsig'));
        $fechaHastaAsig = explode('T',$peticion->query->get('fechaHastaAsig'));
        
        $login2 = ($peticion->query->get('login2') ? $peticion->query->get('login2') : "");
        $descripcionPunto = ($peticion->query->get('descripcionPunto') ? $peticion->query->get('descripcionPunto') : "");
        $vendedor = ($peticion->query->get('vendedor') ? $peticion->query->get('vendedor') : "");
        $ciudad = ($peticion->query->get('ciudad') ? $peticion->query->get('ciudad') : "");
        $numOrdenServicio = ($peticion->query->get('numOrdenServicio') ? $peticion->query->get('numOrdenServicio') : "");
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonVerAsignaciones($em, $start, $limit, $fechaDesdeAsig[0], $fechaHastaAsig[0], $login2, '',  
                                         $descripcionPunto, $vendedor, $numOrdenServicio, $ciudad,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
        
    /*
     * Llena el gridTareasAsignadas de consulta.
     */
    /**
    * @Secure(roles="ROLE_140-113")
    */
    public function gridTareasAsignadasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $id_detalle_solicitud = $peticion->query->get('id_detalle_solicitud');
                
        $em = $this->getDoctrine()->getManager("telconet_soporte");
            
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalle')
            ->generarJsonVerTareasAsignadas($em, $start, $limit, $id_detalle_solicitud);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
        
        
    /*
     * Llena el gridHistorialTareasAsignadas de consulta.
     */
    /**
    * @Secure(roles="ROLE_140-114")
    */
    public function gridHistorialTareasAsignadasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $id_detalle_solicitud = $peticion->query->get('id_detalle_solicitud');
                
        $em = $this->getDoctrine()->getManager("telconet_soporte");
            
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalle')
            ->generarJsonVerHistorialTareasAsignadas($em, $start, $limit, $id_detalle_solicitud);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}