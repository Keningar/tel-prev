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

class AgendaController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_82-1")
    */ 
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();        

        $em = $this->getDoctrine()->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("82", "1");  	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());      

        return $this->render('soporteBundle:Agenda:index.html.twig', array(
            'item' => $entityItemMenu,
		));        
    }
    
    public function newAction()
    {
        $session = $this->get('request')->getSession();
		
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");            
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("82", "1");   	
		$session->set('menu_modulo_activo', $entityItemMenu->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenu->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenu->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenu->getUrlImagen());
		
        $form   = $this->createForm(new CallActivityType());
        
        return $this->render('soporteBundle:CallActivity:new.html.twig', array(
            'item' => $entityItemMenu,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_82-152")
    */     
    public function getTareasAgendaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $page = $peticion->query->get('page');
        
        $startDate = $peticion->query->get('startDate');
        $endDate = $peticion->query->get('endDate');
        
        $estado = $peticion->query->get('estado');
        
        $em = $this->getDoctrine()->getManager("telconet");        
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleAsignacion')
           // ->generarJsonTareasXUsuario($start, $limit, $estado,$startDate, $endDate, $peticion->getSession()->get('idDepartamento'), $peticion->getSession()->get('id_empleado'), 'ByDepartamento', $em_soporte, $em);
            ->generarJsonTareasTodas($start, $limit, $origen, $startDate, $endDate, $peticion->getSession()->get('id_empleado'),$peticion->getSession()->get('idDepartamento'),"ByDepartamento",$em,$em_soporte);

        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    
     /////////////////////////////inicio: taty/////////////////////////////////////////
    
     /**
    * @Secure(roles="ROLE_78-42")
    */ 
    
    public function getCriteriosAgendaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        		
        $boolTodos = $peticion->query->get('todos') == "YES" ? true : false ;
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $id = $peticion->query->get('id') ? $peticion->query->get('id') : 0;
        
        
		if($boolTodos)
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
				            ->generarJsonCriteriosTotalXCaso($id);		
		}
		else
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
				            ->generarJsonCriteriosXCaso($id, "NO", "NO");
        }
		
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_78-39")
    */ 
    
    public function getAfectadosAgendaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        		
        $boolTodos = $peticion->query->get('todos') == "YES" ? true : false ;
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $id = $peticion->query->get('id') ? $peticion->query->get('id') : 0;
        //print_r($id); die();
		if($boolTodos)
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
				            ->generarJsonAfectadosTotalXCaso($id);		
		}
		else
		{
	        $objJson = $this->getDoctrine()
				            ->getManager("telconet_soporte")
				            ->getRepository('schemaBundle:InfoCaso')
				            ->generarJsonAfectadosXCaso($id, "NO", "NO");
        }
		
        $respuesta->setContent($objJson);        
        return $respuesta;
    }
   //////////////////fin: taty////////////////////////////////////////////////////////////////////////////////// 
    
    
    
}

