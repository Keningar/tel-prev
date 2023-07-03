<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Form\InfoPersonaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class PersonaProveedorController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_172-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("172", "1");

        return $this->render('administracionBundle:PersonaProveedor:index.html.twig', array(
            'item' => $entityItemMenu
        ));
    }
    
    /**
    * @Secure(roles="ROLE_172-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager();
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("172", "1");

        if (null == $proveedor = $em->find('schemaBundle:InfoPersonaEmpresaRol', $id)) {
            throw new NotFoundHttpException('No existe el InfoPersonaEmpresaRol que se quiere mostrar');
        }

        return $this->render('administracionBundle:PersonaProveedor:show.html.twig', array(
            'item' => $entityItemMenu,
            'proveedor'   => $proveedor,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_172-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $razon_social = $peticion->query->get('razon_social');
        $representante_legal = $peticion->query->get('representante_legal');
        $identificacion = $peticion->query->get('identificacion');
        $tipo_empresa = $peticion->query->get('tipo_empresa');
        $estado = $peticion->query->get('estado');	
	
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager()
            ->getRepository('schemaBundle:InfoPersona')
            ->generarJsonPersonaProveedor($razon_social, $representante_legal, $identificacion, $tipo_empresa, $estado, $start, $limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}