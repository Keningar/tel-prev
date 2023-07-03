<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiFuncion;
use telconet\schemaBundle\Form\AdmiFuncionType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiFuncionController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_50-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("50", "1");

        $entities = $em->getRepository('schemaBundle:AdmiFuncion')->findAll();

        return $this->render('administracionBundle:AdmiFuncion:index.html.twig', array(
            'item' => $entityItemMenu,
            'funcion' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_50-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_comercial = $this->get('doctrine')->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("50", "1");

        if (null == $funcion = $em->find('schemaBundle:AdmiFuncion', $id)) {
            throw new NotFoundHttpException('No existe el AdmiFuncion que se quiere mostrar');
        }
        
        $EmpresaRol = $em_comercial->find('schemaBundle:InfoEmpresaRol', $funcion->getEmpresaRolId());
        
        return $this->render('administracionBundle:AdmiFuncion:show.html.twig', array(
            'item' => $entityItemMenu,
            'funcion'   => $funcion,
            'empresaRol'   => $EmpresaRol,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_50-2")
    */
    public function newAction()
    {
        $arrayEmpresaRoles =  $this->retornaArrayEmpresaRoles();
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("50", "1");
        $entity = new AdmiFuncion();
        $form   = $this->createForm(new AdmiFuncionType(array('arrayEmpresaRoles'=>$arrayEmpresaRoles)), $entity);

        return $this->render('administracionBundle:AdmiFuncion:new.html.twig', array(
            'item' => $entityItemMenu,
            'funcion' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_50-3")
    */
    public function createAction()
    {
        $arrayEmpresaRoles =  $this->retornaArrayEmpresaRoles();
        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("50", "1");
        
        $entity  = new AdmiFuncion();
        $form    = $this->createForm(new AdmiFuncionType(array('arrayEmpresaRoles'=>$arrayEmpresaRoles)), $entity);
        
        $formVariables = $request->request->get('telconet_schemabundle_admifunciontype');
        $empresaRolId = $formVariables["empresaRolId"];
        //$form->setData($entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
                    
            $entity->setEstado('Activo');
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($request->getSession()->get('user'));
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            $entity->setEmpresaRolId($empresaRolId);
            
            $em->persist($entity);
            $em->flush();
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admifuncion_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiFuncion:new.html.twig', array(
            'item' => $entityItemMenu,
            'funcion' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_50-4")
    */
    public function editAction($id)
    {
        $arrayEmpresaRoles =  $this->retornaArrayEmpresaRoles();
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("50", "1");

        if (null == $funcion = $em->find('schemaBundle:AdmiFuncion', $id)) {
            throw new NotFoundHttpException('No existe el AdmiFuncion que se quiere modificar');
        }
        echo  $funcion->getEmpresaRolId();

        $formulario =$this->createForm(new AdmiFuncionType(array('arrayEmpresaRoles'=>$arrayEmpresaRoles)), $funcion);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiFuncion:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'funcion'   => $funcion));
    }
    
    /**
    * @Secure(roles="ROLE_50-5")
    */
    public function updateAction($id)
    {
        $arrayEmpresaRoles =  $this->retornaArrayEmpresaRoles();
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("50", "1");        
        $request = $this->getRequest();

        $entity = $em->getRepository('schemaBundle:AdmiFuncion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiFuncion entity.');
        }

        $formVariables = $request->request->get('telconet_schemabundle_admifunciontype');
        $empresaRolId = $formVariables["empresaRolId"];
        
        $editForm   = $this->createForm(new AdmiFuncionType(array('arrayEmpresaRoles'=>$arrayEmpresaRoles)), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $entity->setEmpresaRolId($empresaRolId);
            
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admifuncion_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiFuncion:edit.html.twig',array(
            'item' => $entityItemMenu,
            'funcion'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_50-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiFuncion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiFuncion entity.');
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

        return $this->redirect($this->generateUrl('admifuncion'));
    }

    /**
    * @Secure(roles="ROLE_50-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiFuncion', $id)) {
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
    
    public function retornaArrayEmpresaRoles()
    {
        $em = $this->getDoctrine()->getManager();
        $EmpresaRoles = $em->getRepository('schemaBundle:InfoEmpresaRol')->findByEstado("Activo");
        $arrayEmpresaRoles = false;
        if($EmpresaRoles && count($EmpresaRoles)>0)
        {
            foreach($EmpresaRoles as $key => $valueRol)
            {
                $arrayEmpresaRol["id"] = $valueRol->getId();
                $arrayEmpresaRol["rol"] = $valueRol->getRolId()->getDescripcionRol();
                $arrayEmpresaRol["empresa"] = $valueRol->getEmpresaId()->getNombreEmpresa();
                $arrayEmpresaRoles[] = $arrayEmpresaRol;
            }
        }
        return $arrayEmpresaRoles;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_50-7")
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
        
        $em_comercial = $this->get('doctrine')->getManager('telconet');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiFuncion')
            ->generarJson($em_comercial, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}