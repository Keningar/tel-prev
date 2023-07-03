<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiProceso;
use telconet\schemaBundle\Entity\AdmiProcesoEmpresa;
use telconet\schemaBundle\Form\AdmiProcesoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiProcesoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_51-1")
    */
    public function indexAction()
    {
    
    $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_51-8'))
		{
	$rolesPermitidos[] = 'ROLE_51-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_51-9'))
		{
	$rolesPermitidos[] = 'ROLE_51-9';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_51-6'))
		{
	$rolesPermitidos[] = 'ROLE_51-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_51-4'))
		{
	$rolesPermitidos[] = 'ROLE_51-4';
	}
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");

        $entities = $em->getRepository('schemaBundle:AdmiProceso')->findAll();

        return $this->render('administracionBundle:AdmiProceso:index.html.twig', array(
            'item' => $entityItemMenu,
            'entities' => $entities,
            'rolesPermitidos' => $rolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_51-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        $rolesPermitidos = array();
	if (true === $this->get('security.context')->isGranted('ROLE_51-8'))
		{
	$rolesPermitidos[] = 'ROLE_51-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_51-9'))
		{
	$rolesPermitidos[] = 'ROLE_51-9';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_51-6'))
		{
	$rolesPermitidos[] = 'ROLE_51-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_51-4'))
		{
	$rolesPermitidos[] = 'ROLE_51-4';
	}       
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");

     
        if (null == $proceso = $em->find('schemaBundle:AdmiProceso', $id)) {
            throw new NotFoundHttpException('No existe el Proceso que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiProceso:show.html.twig', array(
            'item' => $entityItemMenu,
            'proceso'   => $proceso,
            'rolesPermitidos'   => $rolesPermitidos,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_51-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");
        $entity = new AdmiProceso();
        $form   = $this->createForm(new AdmiProcesoType(), $entity);

        return $this->render('administracionBundle:AdmiProceso:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_51-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        
        $session    = $request->getSession();
        $codEmpresa = $session->get('idEmpresa');
        
        $em = $this->get('doctrine')->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");
        
        $entity  = new AdmiProceso();
        $form    = $this->createForm(new AdmiProcesoType(), $entity);        
        $form->bind($request);
        
        $peticion = $this->get('request');
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();	
            try {           
				$escogido_procesopadre_id = $peticion->get('escogido_procesopadre_id');									
				
				if($escogido_procesopadre_id && $escogido_procesopadre_id != "")
				{
					$entityProcesoPadre = $em->find('schemaBundle:AdmiProceso', $escogido_procesopadre_id);	
										
					if($entityProcesoPadre != null)
					{
						$entity->setProcesoPadreId($entityProcesoPadre);
					}
				}
				
		        $entity->setEstado('Activo');
		        $entity->setFeCreacion(new \DateTime('now'));
		        $entity->setUsrCreacion($request->getSession()->get('user'));
		        $entity->setFeUltMod(new \DateTime('now'));
		        $entity->setUsrUltMod($request->getSession()->get('user'));
				
	            $em->persist($entity);
	            $em->flush();	            	           
	            
	            /******************************************/
	            
	            $entityProEmp = new AdmiProcesoEmpresa();
	            
	            $entityProEmp->setEstado('Activo');
	            $entityProEmp->setUsrCreacion($request->getSession()->get('user'));
	            $entityProEmp->setFeCreacion(new \DateTime('now'));
	            $entityProEmp->setEmpresaCod($codEmpresa);
	            $entityProEmp->setProcesoId($entity);
	            
	            $em->persist($entityProEmp);
	            $em->flush();
	            
	            /******************************************/
	            	            	            
	            $em->getConnection()->commit();
            
		    return $this->redirect($this->generateUrl('admiproceso_show', array('id' => $entity->getId())));		
		    
		    
		    
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
                $em->getConnection()->close();
            }
        }
        
        return $this->render('administracionBundle:AdmiProceso:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_51-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");

        if (null == $proceso = $em->find('schemaBundle:AdmiProceso', $id)) {
            throw new NotFoundHttpException('No existe el Conector que se quiere modificar');
        }
               
        $formulario =$this->createForm(new AdmiProcesoType(), $proceso);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiProceso:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'proceso'   => $proceso));
    }
    
    /**
    * @Secure(roles="ROLE_51-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("51", "1");
        
        $entity = $em->getRepository('schemaBundle:AdmiProceso')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiProceso entity.');
        }               
        
        $editForm   = $this->createForm(new AdmiProcesoType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        $peticion = $this->get('request');
        
        $session    = $peticion->getSession();
        $codEmpresa = $session->get('idEmpresa');
        
        
        if ($editForm->isValid()) {
            $em->getConnection()->beginTransaction();	
            try {           
				$escogido_procesopadre_id = $peticion->get('escogido_procesopadre_id');	
				if($escogido_procesopadre_id && $escogido_procesopadre_id != "")
				{
			        $entityProcesoPadre = $em->find('schemaBundle:AdmiProceso', $escogido_procesopadre_id);	
					if($entityProcesoPadre != null)
					{
						$entity->setProcesoPadreId($entityProcesoPadre);
					}
				}
				
	            /*Para que guarde la fecha y el usuario correspondiente*/
	            $entity->setFeUltMod(new \DateTime('now'));
	            //$entity->setIdUsuarioModificacion($user->getUsername());
	            $entity->setUsrUltMod($request->getSession()->get('user'));
	            /*Para que guarde la fecha y el usuario correspondiente*/
	            	            				
	            $em->persist($entity);
	            $em->flush();	
	            	           
	            $em->getConnection()->commit();

				return $this->redirect($this->generateUrl('admiproceso_show', array('id' => $id)));
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
                $em->getConnection()->close();
            }
        }
  
        echo $proceso->getVisible();

        return $this->render('administracionBundle:AdmiProceso:edit.html.twig',array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_51-8")
    * 
    * @author John Vera R. <javera@telconet.ec>
    * @version 1.1 24-11-2017
    * 
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiProceso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiProceso entity.');
            }
            
        $arrayParametros['objCaso'] = $entity ;
        $arrayParametros['strUser'] = $request->getSession()->get('user') ;

        $this->eliminarProceso($arrayParametros);

        return $this->redirect($this->generateUrl('admiproceso'));
    }

    /**
    * @Secure(roles="ROLE_51-9")
    * 
    * @author John Vera R. <javera@telconet.ec>
    * @version 1.1 24-11-2017
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiProceso', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
                    $arrayParametros['objCaso'] = $entity ;
                    $arrayParametros['strUser'] = $request->getSession()->get('user') ;
                    
                    $strMensaje = $this->eliminarProceso($arrayParametros);
                    if($strMensaje != 'OK')
                    {
                        return $respuesta->setContent($strMensaje);
                    }
                    
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    

    /* eliminarCaso
     * elimina el caso y las tareas relacionadas
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.0 24-11-2017
     * 
     */
    
    public function eliminarProceso($arrayParametros)
    {        
        try
        {
            $objProceso = $arrayParametros['objCaso'];
            $strUser = $arrayParametros['strUser'];

            $emSoporte = $this->getDoctrine()->getManager("telconet_soporte");

            $objProceso->setEstado("Eliminado");
            $objProceso->setFeUltMod(new \DateTime('now'));
            $objProceso->setUsrUltMod($strUser);
            $emSoporte->persist($objProceso);
            $emSoporte->flush();

            $arrayTareas = $emSoporte->getRepository('schemaBundle:AdmiTarea')->findBy(array('procesoId' => $objProceso->getId()));

            foreach($arrayTareas as $objTarea)
            {
                if(is_object($objTarea))
                {
                    $objTarea->setEstado("Eliminado");
                    $objTarea->setFeUltMod(new \DateTime('now'));
                    $objTarea->setUsrUltMod($strUser);
                    $emSoporte->persist($objTarea);
                    $emSoporte->flush();
                }

            }
            
            return "OK";
        
        } 
        catch (Exception $ex) 
        {
            return "Error: ".$ex->getMessage();            
        }
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_51-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $session    = $peticion->getSession();
        $codEmpresa = $session->get('idEmpresa');
                
	$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $visible = $peticion->query->get('visible');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiProceso')
            ->generarJson(null, $nombre,$estado,$start,$limit,$codEmpresa,$visible);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_51-19")
    */
    public function getTareasAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiTarea')
            ->generarJsonTareasPorProceso($id,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
    /**
    * @Secure(roles="ROLE_51-251")
    */
    public function getProcesosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $session    = $peticion->getSession();
        $codEmpresa = $session->get('idEmpresa');
		
        $parametros = array();
		$parametros["idProcesoActual"]=$peticion->query->get('idProcesoActual') ? $peticion->query->get('idProcesoActual') : "";
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiProceso')
            ->generarJson($parametros, $nombre, "Activo", $start, $limit,$codEmpresa);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }  

}