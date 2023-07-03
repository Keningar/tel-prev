<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoNotificacion;
use telconet\schemaBundle\Form\AdmiTipoNotificacionType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiTipoNotificacionController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_58-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("58", "1");

        $entities = $em->getRepository('schemaBundle:AdmiTipoNotificacion')->findAll();

        return $this->render('administracionBundle:AdmiTipoNotificacion:index.html.twig', array(
            'item' => $entityItemMenu,
            'tiponotificacion' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_58-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("58", "1");

        if (null == $tiponotificacion = $em->find('schemaBundle:AdmiTipoNotificacion', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoNotificacion que se quiere mostrar');
        }

        return $this->render('administracionBundle:AdmiTipoNotificacion:show.html.twig', array(
            'item' => $entityItemMenu,
            'tiponotificacion'   => $tiponotificacion,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_58-2")
    */
    public function newAction()
    {
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("58", "1");
        $entity = new AdmiTipoNotificacion();
        $form   = $this->createForm(new AdmiTipoNotificacionType(), $entity);

        return $this->render('administracionBundle:AdmiTipoNotificacion:new.html.twig', array(
            'item' => $entityItemMenu,
            'tiponotificacion' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_58-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("58", "1");
        $entity  = new AdmiTipoNotificacion();
        $form    = $this->createForm(new AdmiTipoNotificacionType(), $entity);
        
        
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
            
            return $this->redirect($this->generateUrl('admitiponotificacion_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiTipoNotificacion:new.html.twig', array(
            'item' => $entityItemMenu,
            'tiponotificacion' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_58-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("58", "1");

        if (null == $tiponotificacion = $em->find('schemaBundle:AdmiTipoNotificacion', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoNotificacion que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTipoNotificacionType(), $tiponotificacion);
//        $formulario->setData($proceso);

        return $this->render('administracionBundle:AdmiTipoNotificacion:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'tiponotificacion'   => $tiponotificacion));
    }
    
    /**
    * @Secure(roles="ROLE_58-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("58", "1");
        $entity = $em->getRepository('schemaBundle:AdmiTipoNotificacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoNotificacion entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoNotificacionType(), $entity);

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

            return $this->redirect($this->generateUrl('admitiponotificacion_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoNotificacion:edit.html.twig',array(
            'item' => $entityItemMenu,
            'tiponotificacion'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_58-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiTipoNotificacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoNotificacion entity.');
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

        return $this->redirect($this->generateUrl('admitiponotificacion'));
    }

    /**
    * @Secure(roles="ROLE_58-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoNotificacion', $id)) {
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
    * @Secure(roles="ROLE_58-7")
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
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiTipoNotificacion')
            ->generarJson($nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
}