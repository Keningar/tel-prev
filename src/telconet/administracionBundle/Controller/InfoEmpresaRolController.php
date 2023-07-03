<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoEmpresaRol;
use telconet\schemaBundle\Form\InfoEmpresaRolType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class InfoEmpresaRolController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_38-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("38", "1");

        $entities = $em->getRepository('schemaBundle:InfoEmpresaRol')->findAll();

        return $this->render('administracionBundle:InfoEmpresaRol:index.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresarol' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_38-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_general = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("38", "1");

        if (null == $infoempresarol = $em->find('schemaBundle:InfoEmpresaRol', $id)) {
            throw new NotFoundHttpException('No existe el InfoEmpresaRol que se quiere mostrar');
        }

        $nombreRol = "";
        if($infoempresarol->getRolId())
        {    
            $objRol = $em_general->getRepository('schemaBundle:AdmiRol')->findOneById($infoempresarol->getRolId());
            $nombreRol = $objRol ? $objRol->getDescripcionRol() : "";
        }

        return $this->render('administracionBundle:InfoEmpresaRol:show.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresarol'   => $infoempresarol,
            'nombreRol'   => $nombreRol,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_38-2")
    */
    public function newAction()
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("38", "1");
        $entity = new InfoEmpresaRol();
        $form   = $this->createForm(new InfoEmpresaRolType(array('arrayRoles'=>$arrayRoles)), $entity);

        return $this->render('administracionBundle:InfoEmpresaRol:new.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresarol' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_38-3")
    */
    public function createAction()
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet');
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("38", "1");
                
        $entity  = new InfoEmpresaRol();
        $form    = $this->createForm(new InfoEmpresaRolType(array('arrayRoles'=>$arrayRoles)), $entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setIpCreacion($request->getClientIp());
            $em->persist($entity);
            $em->flush();
            
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('infoempresarol_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:InfoEmpresaRol:new.html.twig', array(
            'item' => $entityItemMenu,
            'infoempresarol' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_38-4")
    */
    public function editAction($id)
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("38", "1");

        if (null == $infoempresarol = $em->find('schemaBundle:InfoEmpresaRol', $id)) {
            throw new NotFoundHttpException('No existe el InfoEmpresaRol que se quiere modificar');
        }

        $formulario =$this->createForm(new InfoEmpresaRolType(array('arrayRoles'=>$arrayRoles)), $infoempresarol);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:InfoEmpresaRol:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'infoempresarol'   => $infoempresarol));
    }
    
    /**
    * @Secure(roles="ROLE_38-5")
    */
    public function updateAction($id)
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("38", "1");
        $entity = $em->getRepository('schemaBundle:InfoEmpresaRol')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoEmpresaRol entity.');
        }
        
        $editForm   = $this->createForm(new InfoEmpresaRolType(array('arrayRoles'=>$arrayRoles)), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        if ($editForm->isValid()) {			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('infoempresarol_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:InfoEmpresaRol:edit.html.twig',array(
            'item' => $entityItemMenu,
            'infoempresarol'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_38-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:InfoEmpresaRol')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoEmpresaRol entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);			
			$em->persist($entity);
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('infoempresarol'));
    }

    /**
    * @Secure(roles="ROLE_38-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $em = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:InfoEmpresaRol', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
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
    * @Secure(roles="ROLE_38-7")
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->query->get('estado');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoEmpresaRol')
            ->generarJson($em_general, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    public function retornaArrayRoles()
    {
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        $em = $this->getDoctrine()->getManager();
        
        $roles = $em_general->getRepository('schemaBundle:AdmiRol')->findAll();
        $arrayRoles = false;
        if($roles && count($roles)>0)
        {
            foreach($roles as $key => $valueRol)
            {
                $arrayRol["id"] = $valueRol->getId();
                $arrayRol["nombre"] = $valueRol->getDescripcionRol();
                $arrayRoles[] = $arrayRol;
            }
        }
        return $arrayRoles;
    }
}