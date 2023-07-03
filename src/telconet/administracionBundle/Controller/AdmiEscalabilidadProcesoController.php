<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiEscalabilidadProceso;
use telconet\schemaBundle\Form\AdmiEscalabilidadProcesoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiEscalabilidadProcesoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_52-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("52", "1");

        $entities = $em->getRepository('schemaBundle:AdmiEscalabilidadProceso')->findAll();

        return $this->render('administracionBundle:AdmiEscalabilidadProceso:index.html.twig', array(
            'item' => $entityItemMenu,
            'entities' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_52-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_general = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("52", "1");
 
        if (null == $escalabilidad = $em->find('schemaBundle:AdmiEscalabilidadProceso', $id)) {
            throw new NotFoundHttpException('No existe la Escalabilidad Proceso que se quiere mostrar');
        }

        $nombreRol = "";
        if($escalabilidad->getRolId())
        {    
            $objRol = $em_general->getRepository('schemaBundle:AdmiRol')->findOneById($escalabilidad->getRolId());
            $nombreRol = $objRol ? $objRol->getDescripcionRol() : "";
        }
        
        return $this->render('administracionBundle:AdmiEscalabilidadProceso:show.html.twig', array(
            'item' => $entityItemMenu,
            'escalabilidad'   => $escalabilidad,
            'nombreRol'   => $nombreRol,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_52-2")
    */
    public function newAction()
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("52", "1");
        $entity = new AdmiEscalabilidadProceso();
        $form   = $this->createForm(new AdmiEscalabilidadProcesoType(array('arrayRoles'=>$arrayRoles)), $entity);

        return $this->render('administracionBundle:AdmiEscalabilidadProceso:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_52-3")
    */
    public function createAction()
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("52", "1");
        $entity  = new AdmiEscalabilidadProceso();
        $form    = $this->createForm(new AdmiEscalabilidadProcesoType(array('arrayRoles'=>$arrayRoles)), $entity);
        
        
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
            
            return $this->redirect($this->generateUrl('admiescalabilidadproceso_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiEscalabilidadProceso:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_52-4")
    */
    public function editAction($id)
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("52", "1");

        if (null == $escalabilidad = $em->find('schemaBundle:AdmiEscalabilidadProceso', $id)) {
            throw new NotFoundHttpException('No existe la Escalabilidad Proceso que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiEscalabilidadProcesoType(array('arrayRoles'=>$arrayRoles)), $escalabilidad);
//        $formulario->setData($escalabilidad);

        return $this->render('administracionBundle:AdmiEscalabilidadProceso:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'escalabilidad'   => $escalabilidad));
    }
    
    /**
    * @Secure(roles="ROLE_52-5")
    */
    public function updateAction($id)
    {
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("52", "1");
        $entity = $em->getRepository('schemaBundle:AdmiEscalabilidadProceso')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiEscalabilidadProceso entity.');
        }

        $editForm   = $this->createForm(new AdmiEscalabilidadProcesoType(array('arrayRoles'=>$arrayRoles)), $entity);

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

            return $this->redirect($this->generateUrl('admiescalabilidadproceso_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiEscalabilidadProceso:edit.html.twig',array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_52-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiEscalabilidadProceso')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiEscalabilidadProceso entity.');
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

        return $this->redirect($this->generateUrl('admiescalabilidadproceso'));
    }

    /**
    * @Secure(roles="ROLE_52-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiEscalabilidadProceso', $id)) {
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
    
    public function retornaArrayRoles()
    {
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        $em = $this->getDoctrine()->getManager();
        $Roles = $em_general->getRepository('schemaBundle:AdmiRol')->findByEstado("Activo");
        $arrayRoles = false;
        if($Roles && count($Roles)>0)
        {
            foreach($Roles as $key => $valueRol)
            {
                $arrayRol["id"] = $valueRol->getId();
                $arrayRol["descripcion"] = $valueRol->getDescripcionRol();
                $arrayRoles[] = $arrayRol;
            }
        }
        return $arrayRoles;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_52-7")
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
        
        $em_general = $this->get('doctrine')->getManager('telconet_general');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiEscalabilidadProceso')
            ->generarJson($em_general, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}