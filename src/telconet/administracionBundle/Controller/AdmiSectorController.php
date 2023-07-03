<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiSector;
use telconet\schemaBundle\Form\AdmiSectorType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiSectorController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_28-1")
    */
    public function indexAction()
    {
		$rolesPermitidos = array();
		if (true === $this->get('security.context')->isGranted('ROLE_28-8'))
			{
		$rolesPermitidos[] = 'ROLE_28-8';
		}
		if (true === $this->get('security.context')->isGranted('ROLE_28-9'))
			{
		$rolesPermitidos[] = 'ROLE_28-9';
		}
		if (true === $this->get('security.context')->isGranted('ROLE_28-4'))
			{
		$rolesPermitidos[] = 'ROLE_28-4';
		}
		if (true === $this->get('security.context')->isGranted('ROLE_28-6'))
			{
		$rolesPermitidos[] = 'ROLE_28-6';
		}
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("28", "1");

        $entities = $em->getRepository('schemaBundle:AdmiSector')->findAll();

        return $this->render('administracionBundle:AdmiSector:index.html.twig', array(
            'item' => $entityItemMenu,
            'sector' => $entities,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_28-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("28", "1");

        if (null == $sector = $em->find('schemaBundle:AdmiSector', $id)) {
            throw new NotFoundHttpException('No existe el AdmiSector que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiSector:show.html.twig', array(
            'item' => $entityItemMenu,
            'sector'   => $sector,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_28-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("28", "1");
        $entity = new AdmiSector();
        $form   = $this->createForm(new AdmiSectorType(), $entity);

        return $this->render('administracionBundle:AdmiSector:new.html.twig', array(
            'item' => $entityItemMenu,
            'sector' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_28-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        
        $idEmpresa = $request->getSession()->get('idEmpresa');
        
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("28", "1");
        $entity  = new AdmiSector();
        $form    = $this->createForm(new AdmiSectorType(), $entity);
        $form->bind($request);
        
        $peticion = $this->get('request');
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();			
            try {           
				$escogido_parroquia_id = $peticion->get('escogido_parroquia_id');				
		        $entityParroquia = $em->find('schemaBundle:AdmiParroquia', $escogido_parroquia_id);
				
		        $entity->setParroquiaId($entityParroquia);
		        $entity->setEstado('Activo');
		        $entity->setFeCreacion(new \DateTime('now'));
		        $entity->setUsrCreacion($request->getSession()->get('user'));
		        $entity->setFeUltMod(new \DateTime('now'));
		        $entity->setUsrUltMod($request->getSession()->get('user'));		
                $entity->setEmpresaCod($idEmpresa);

	            $em->persist($entity);
	            $em->flush();            
                $em->getConnection()->commit();				
			
				return $this->redirect($this->generateUrl('admisector_show', array('id' => $entity->getId())));			
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
                $em->getConnection()->close();
            }
        }
        
        return $this->render('administracionBundle:AdmiSector:new.html.twig', array(
            'item' => $entityItemMenu,
            'sector' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_28-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("28", "1");

        if (null == $sector = $em->find('schemaBundle:AdmiSector', $id)) {
            throw new NotFoundHttpException('No existe el AdmiSector que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiSectorType(), $sector);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiSector:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'sector'   => $sector));
    }
    
    /**
    * @Secure(roles="ROLE_28-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("28", "1");
        $entity = $em->getRepository('schemaBundle:AdmiSector')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiSector entity.');
        }

        $editForm   = $this->createForm(new AdmiSectorType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);
		
        $peticion = $this->get('request');
        if ($editForm->isValid()) {		
            $em->getConnection()->beginTransaction();			
            try {    				
				$escogido_parroquia_id = $peticion->get('escogido_parroquia_id');				
				$entityParroquia = $em->find('schemaBundle:AdmiParroquia', $escogido_parroquia_id);
				
				$entity->setParroquiaId($entityParroquia);					
	            /*Para que guarde la fecha y el usuario correspondiente*/
	            $entity->setFeUltMod(new \DateTime('now'));
	            //$entity->setIdUsuarioModificacion($user->getUsername());
	            $entity->setUsrUltMod($request->getSession()->get('user'));
	            /*Para que guarde la fecha y el usuario correspondiente*/
				
	            $em->persist($entity);
	            $em->flush();
                $em->getConnection()->commit();	

	            return $this->redirect($this->generateUrl('admisector_show', array('id' => $id)));		
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
                $em->getConnection()->close();
            }
        }

        return $this->render('administracionBundle:AdmiSector:edit.html.twig',array(
            'item' => $entityItemMenu,
            'sector'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_28-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiSector')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiSector entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
			$em->persist($entity);	
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admisector'));
    }

    /**
    * @Secure(roles="ROLE_28-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiSector', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($request->getSession()->get('user'));
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_28-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $request = $this->get('request');
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
                
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
        $parametros["idProvincia"]=$peticion->query->get('idProvincia') ? $peticion->query->get('idProvincia') : "";
        $parametros["idCanton"]=$peticion->query->get('idCanton') ? $peticion->query->get('idCanton') : "";
        $parametros["idTipoParroquia"]=$peticion->query->get('idTipoParroquia') ? $peticion->query->get('idTipoParroquia') : "";
        $parametros["idParroquia"]=$peticion->query->get('idParroquia') ? $peticion->query->get('idParroquia') : "";
        $parametros["idEmpresa"] = $idEmpresa;
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiSector')
            ->generarJson($parametros, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_28-20")
    */
    public function buscarSectoresAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idParroquia = $peticion->get('idParroquia');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiSector')
            ->generarJsonSectoresPorParroquia($idParroquia,"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }	
	
	/**
    * @Secure(roles="ROLE_28-246")
    */
    public function getPaisesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiPais')
            ->generarJson($nombre, "Activo", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	/**
    * @Secure(roles="ROLE_28-247")
    */
    public function getRegionesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiRegion')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	/**
    * @Secure(roles="ROLE_28-248")
    */
    public function getProvinciasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
		$parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiProvincia')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }   
	
	/**
    * @Secure(roles="ROLE_28-249")
    */
    public function getTiposParroquiaAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiTipoParroquia')
            ->generarJson($nombre, "Activo", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }  
	
	/**
    * @Secure(roles="ROLE_28-10")
    */
    public function getCantonesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
		$parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
        $parametros["idProvincia"]=$peticion->query->get('idProvincia') ? $peticion->query->get('idProvincia') : "";
		
        $idEmpresa = $peticion->getSession()->get('idEmpresa');
        $parametros["idEmpresa"] = $idEmpresa;
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiCanton')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }  
	
	/**
    * @Secure(roles="ROLE_28-250")
    */
    public function getParroquiasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
        $parametros["idProvincia"]=$peticion->query->get('idProvincia') ? $peticion->query->get('idProvincia') : "";	
        $parametros["idCanton"]=$peticion->query->get('idCanton') ? $peticion->query->get('idCanton') : "";	
        $parametros["idTipoParroquia"]=$peticion->query->get('idTipoParroquia') ? $peticion->query->get('idTipoParroquia') : "";
		
        $idEmpresa = $peticion->getSession()->get('idEmpresa');
        $parametros["idEmpresa"] = $idEmpresa;
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiParroquia')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    } 

}