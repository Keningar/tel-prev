<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiRol;
use telconet\schemaBundle\Form\AdmiRolType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiRolController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_48-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("48", "1");

        $entities = $em->getRepository('schemaBundle:AdmiRol')->findAll();

        return $this->render('administracionBundle:AdmiRol:index.html.twig', array(
            'item' => $entityItemMenu,
            'rol' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_48-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("48", "1");

        if (null == $rol = $em->find('schemaBundle:AdmiRol', $id)) {
            throw new NotFoundHttpException('No existe el AdmiRol que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiRol:show.html.twig', array(
            'item' => $entityItemMenu,
            'rol'   => $rol,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_48-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("48", "1");
        $entity = new AdmiRol();
        $form   = $this->createForm(new AdmiRolType(), $entity);

        return $this->render('administracionBundle:AdmiRol:new.html.twig', array(
            'item' => $entityItemMenu,
            'rol' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_48-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("48", "1");
        $entity  = new AdmiRol();
        $form    = $this->createForm(new AdmiRolType(), $entity);
        
        
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
            
            return $this->redirect($this->generateUrl('admirol_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiRol:new.html.twig', array(
            'item' => $entityItemMenu,
            'rol' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_48-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("48", "1");

        if (null == $rol = $em->find('schemaBundle:AdmiRol', $id)) {
            throw new NotFoundHttpException('No existe el AdmiRol que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiRolType(), $rol);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiRol:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'rol'   => $rol));
    }
    
    /**
    * @Secure(roles="ROLE_48-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("48", "1");

        $entity = $em->getRepository('schemaBundle:AdmiRol')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiRol entity.');
        }

        $editForm   = $this->createForm(new AdmiRolType(), $entity);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $estado = 'Modificado';
            $entity->setEstado($estado);
            
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admirol_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiRol:edit.html.twig',array(
            'item' => $entityItemMenu,
            'rol'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_48-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiRol')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiRol entity.');
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

        return $this->redirect($this->generateUrl('admirol'));
    }

    /**
    * @Secure(roles="ROLE_48-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiRol', $id)) {
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
    * @Secure(roles="ROLE_48-7")
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
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiRol')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}