<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiParroquia;
use telconet\schemaBundle\Form\AdmiParroquiaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiParroquiaController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_27-1")
    * indexAction
    * 
    * @version 1.0 
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * 
    * @version 1.1 18-07-2021 Se agregan los permisos y renderiza el index 
    *                         de la administración
    */ 
    public function indexAction()
    {
        $arrayRolesPermitidos = array();
        if (true === $this->get('security.context')->isGranted('ROLE_27-6'))
	{
            $arrayRolesPermitidos[] = 'ROLE_27-6';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_27-4'))
	{
            $arrayRolesPermitidos[] = 'ROLE_27-4';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_27-8'))
	{
            $arrayRolesPermitidos[] = 'ROLE_27-8';
	}
	if (true === $this->get('security.context')->isGranted('ROLE_27-9'))
	{
            $arrayRolesPermitidos[] = 'ROLE_27-9';
	}
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("27", "1");

        $entities = $em->getRepository('schemaBundle:AdmiParroquia')->findAll();

        return $this->render('administracionBundle:AdmiParroquia:index.html.twig', array(
            'item' => $entityItemMenu,
            'parroquia' => $entities,
            'rolesPermitidos' => $arrayRolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_27-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("27", "1");

        if (null == $parroquia = $em->find('schemaBundle:AdmiParroquia', $id)) {
            throw new NotFoundHttpException('No existe el AdmiParroquia que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiParroquia:show.html.twig', array(
            'item' => $entityItemMenu,
            'parroquia'   => $parroquia,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_27-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("27", "1");
        $entity = new AdmiParroquia();
        $form   = $this->createForm(new AdmiParroquiaType(), $entity);

        return $this->render('administracionBundle:AdmiParroquia:new.html.twig', array(
            'item' => $entityItemMenu,
            'parroquia' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_27-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("27", "1");
        $entity  = new AdmiParroquia();
        $form    = $this->createForm(new AdmiParroquiaType(), $entity);
        
        
        $entity->setEstado('Activo');
        $entity->setFeCreacion(new \DateTime('now'));
        $entity->setUsrCreacion($request->getSession()->get('user'));
        $entity->setFeUltMod(new \DateTime('now'));
        $entity->setUsrUltMod($request->getSession()->get('user'));
        
//        $form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admiparroquia_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiParroquia:new.html.twig', array(
            'item' => $entityItemMenu,
            'parroquia' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_27-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("27", "1");

        if (null == $parroquia = $em->find('schemaBundle:AdmiParroquia', $id)) {
            throw new NotFoundHttpException('No existe el AdmiParroquia que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiParroquiaType(), $parroquia);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiParroquia:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'parroquia'   => $parroquia));
    }
    
    /**
    * @Secure(roles="ROLE_27-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("27", "1");
        $entity = $em->getRepository('schemaBundle:AdmiParroquia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiParroquia entity.');
        }

        $editForm   = $this->createForm(new AdmiParroquiaType(), $entity);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admiparroquia_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiParroquia:edit.html.twig',array(
            'item' => $entityItemMenu,
            'parroquia'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_27-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiParroquia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiParroquia entity.');
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

        return $this->redirect($this->generateUrl('admiparroquia'));
    }

    /**
    * @Secure(roles="ROLE_27-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiParroquia', $id)) {
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
    * @Secure(roles="ROLE_27-7")
    *
    * gridAction
    * @version 1.0
    *
    * @author Antonio Ayala <afayala@telconet.ec>
    * @version 1.1 18-07-2021 Se agrega la empresa para que funcione el pagineo 
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $strRequest = $this->get('request');
        $intIdEmpresa = $strRequest->getSession()->get('idEmpresa');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
        $parametros["idProvincia"]=$peticion->query->get('idProvincia') ? $peticion->query->get('idProvincia') : "";
        $parametros["idCanton"]=$peticion->query->get('idCanton') ? $peticion->query->get('idCanton') : "";
        $parametros["idTipoParroquia"]=$peticion->query->get('idTipoParroquia') ? $peticion->query->get('idTipoParroquia') : "";
	$parametros["idEmpresa"] = $intIdEmpresa;
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiParroquia')
            ->generarJson($parametros, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_27-18")
    */
    public function buscarParroquiasAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $idCanton = $peticion->get('idCanton');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiParroquia')
            ->generarJsonParroquiasPorCanton($idCanton,"Activo",$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
		
    /**
    * @Secure(roles="ROLE_27-246")
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
    * @Secure(roles="ROLE_27-247")
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
    * @Secure(roles="ROLE_27-248")
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
    * @Secure(roles="ROLE_27-249")
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
    * @Secure(roles="ROLE_27-10")
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
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiCanton')
            ->generarJson($parametros, $nombre, "Activo-Todos", $start, $limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }  

}