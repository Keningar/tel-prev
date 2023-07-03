<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiProvincia;
use telconet\schemaBundle\Form\AdmiProvinciaType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiProvinciaController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_24-1")
    */
    public function indexAction()
    {
		$rolesPermitidos = array();
		if (true === $this->get('security.context')->isGranted('ROLE_24-6'))
			{
		$rolesPermitidos[] = 'ROLE_24-6';
		}
		if (true === $this->get('security.context')->isGranted('ROLE_24-4'))
			{
		$rolesPermitidos[] = 'ROLE_24-4';
		}
		if (true === $this->get('security.context')->isGranted('ROLE_24-8'))
			{
		$rolesPermitidos[] = 'ROLE_24-8';
		}
		if (true === $this->get('security.context')->isGranted('ROLE_24-9'))
			{
		$rolesPermitidos[] = 'ROLE_24-9';
		}
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("24", "1");

        $entities = $em->getRepository('schemaBundle:AdmiProvincia')->findAll();

        return $this->render('administracionBundle:AdmiProvincia:index.html.twig', array(
            'item' => $entityItemMenu,
            'provincia' => $entities,
            'rolesPermitidos'=>$rolesPermitidos
        ));
    }
    
    /**
    * @Secure(roles="ROLE_24-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("24", "1");

        if (null == $provincia = $em->find('schemaBundle:AdmiProvincia', $id)) {
            throw new NotFoundHttpException('No existe el AdmiProvincia que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiProvincia:show.html.twig', array(
            'item' => $entityItemMenu,
            'provincia'   => $provincia,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_24-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("24", "1");
        $entity = new AdmiProvincia();
        $form   = $this->createForm(new AdmiProvinciaType(), $entity);

        return $this->render('administracionBundle:AdmiProvincia:new.html.twig', array(
            'item' => $entityItemMenu,
            'provincia' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_24-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("24", "1");
        $entity  = new AdmiProvincia();
        $form    = $this->createForm(new AdmiProvinciaType(), $entity);
        
        
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
            
            return $this->redirect($this->generateUrl('admiprovincia_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiProvincia:new.html.twig', array(
            'item' => $entityItemMenu,
            'provincia' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_24-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("24", "1");

        if (null == $provincia = $em->find('schemaBundle:AdmiProvincia', $id)) {
            throw new NotFoundHttpException('No existe el AdmiProvincia que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiProvinciaType(), $provincia);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiProvincia:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'provincia'   => $provincia));
    }
    
    /**
    * @Secure(roles="ROLE_24-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("24", "1");
        $entity = $em->getRepository('schemaBundle:AdmiProvincia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiProvincia entity.');
        }

        $editForm   = $this->createForm(new AdmiProvinciaType(), $entity);

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

            return $this->redirect($this->generateUrl('admiprovincia_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiProvincia:edit.html.twig',array(
            'item' => $entityItemMenu,
            'provincia'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_24-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiProvincia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiProvincia entity.');
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

        return $this->redirect($this->generateUrl('admiprovincia'));
    }

    /**
    * @Secure(roles="ROLE_24-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiProvincia', $id)) {
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
    * @Secure(roles="ROLE_24-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $parametros = array();
        $parametros["idPais"]=$peticion->query->get('idPais') ? $peticion->query->get('idPais') : "";
        $parametros["idRegion"]=$peticion->query->get('idRegion') ? $peticion->query->get('idRegion') : "";	
		
        $nombre = $peticion->query->get('nombre');
        $estado = $peticion->query->get('estado');        
		
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiProvincia')
            ->generarJson($parametros, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
		
	/**
    * @Secure(roles="ROLE_24-246")
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
    * @Secure(roles="ROLE_24-247")
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
     * ajaxGetProvinciasPorRegion
     *
     * Metodo encargado de obtener las provincias de acuerdo a una region establecida en Type           
     *
     * @return json
     *
     * @author Allan Suárez <arsuarez@telconet.ec>
     * @version 1.0 13-02-2015
     */ 
    public function ajaxGetProvinciasPorRegionAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');

        $idRegion = $peticion->get('idRegion');

        $parametros = array();
        $parametros["idRegion"] = $idRegion ? $idRegion : "";

        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiProvincia')
            ->generarJson($parametros, '', 'Activo', '', '');
        $respuesta->setContent($objJson);

        return $respuesta;
    }

}