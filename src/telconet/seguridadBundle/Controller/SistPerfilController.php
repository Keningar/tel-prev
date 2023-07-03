<?php

namespace telconet\seguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\SistPerfil;
use telconet\schemaBundle\Entity\SeguAsignacion;
use telconet\schemaBundle\Form\SistPerfilType;
use Symfony\Component\HttpFoundation\Response;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * SistPerfil controller.
 *
 */
class SistPerfilController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_75-1")
    */  
    public function indexAction()
    {
        
        $request  = $this->get('request');
        $session  = $request->getSession();
        
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("75", "1");
        
        return $this->render('seguridadBundle:SistPerfil:index.html.twig', array(
             'item' => $entityItemMenu
        ));
        
    }

    /**
    * @Secure(roles="ROLE_75-6")
    */  
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("75", "1");
        $entity = $em->getRepository('schemaBundle:SistPerfil')->find($id);
		
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistPerfil entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			//'acc_relaciondas'=>$acc_relacionadas,
			//'img_opcion_menu'=>$img_opcion
        );
        
        return $this->render('seguridadBundle:SistPerfil:show.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_75-2")
    */  
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("75", "1");
		
        $entity = new SistPerfil();
        $modulos = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistModulo')
            ->getArrayModulos('Activo');
        
        $form   = $this->createForm(new SistPerfilType(array('modulos' => $modulos)), $entity);
        
        return $this->render('seguridadBundle:SistPerfil:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
    * @Secure(roles="ROLE_75-3")
    */  
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("75", "1");
		
		$peticion = $this->get('request');
        $entity  = new SistPerfil();
        $request = $this->getRequest();
        $modulos = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistModulo')
            ->getArrayModulos('Activo');
        $form    = $this->createForm(new SistPerfilType(array('modulos' => $modulos)), $entity);
        
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            try {
                $entity->setEstado("Activo");

                /*Para que guarde la fecha y el usuario correspondiente*/
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($request->getSession()->get('user'));

                $em->persist($entity);
                $em->flush();
                
                
                $json_asignaciones = json_decode($peticion->get('segu_asignaciones'));
                
                $array_asignaciones = $json_asignaciones->asignaciones;
                
                $array_perfil_asignacion = array();
                foreach($array_asignaciones as $asignacion)
                {
                    $tmp_relacion = null;
                     
                    if($asignacion->modulo_id&&$asignacion->accion_id)
                    {
                        
                        $tmp_relacion = $em->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array('moduloId' => $asignacion->modulo_id,'accionId' => $asignacion->accion_id));
                    }
                    
                    
                    $asignacion = new SeguAsignacion();
                    
                    $asignacion->setPerfilId($entity);
                    
                    $asignacion->setRelacionSistemaId($tmp_relacion);

                    $asignacion->setUsrCreacion($request->getSession()->get('user'));
                    $asignacion->setFeCreacion(new \DateTime('now'));
                    $asignacion->setIpCreacion($peticion->getClientIp());

                    // Save
                    $em->persist($asignacion);
                    $em->flush();
                    
                }
                $em->getConnection()->commit();
                return $this->redirect($this->generateUrl('sistperfil_show', array('id' => $entity->getId())));
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
		
        return $this->render('schemaBundle:SistPerfil:new.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_75-4")
    */  
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("75", "1");
        $entity = $em->getRepository('schemaBundle:SistPerfil')->find($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistPerfil entity.');
        }
        
        $modulos = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistModulo')
            ->getArrayModulos('Activo');
        
        $editForm   = $this->createForm(new SistPerfilType(array('modulos' => $modulos)), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );

        return $this->render('seguridadBundle:SistPerfil:edit.html.twig',$parametros);
    }

    /**
     * @Secure(roles="ROLE_75-5")
     *
     * Documentación para el método 'updateAction'.
     *
     * Actualiza la información de los perfiles.
     * 
     * @param integer $id Id del Perfil ha editar
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 23-09-2015
     * 
     * @version 1.0 Versión Inicial
     */
    public function updateAction($id)
    {
        $strMensaje = 'ERROR';
        
        $em     = $this->getDoctrine()->getManager('telconet_seguridad');
        $entity = $em->getRepository('schemaBundle:SistPerfil')->find($id);
        
        if (!$entity)
        {
            throw $this->createNotFoundException('Unable to find SistPerfil entity.');
        }
        
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strUserSession = $objSession->get('user');
        
        $em->getConnection()->beginTransaction();

        try 
        {
            $entity->setEstado("Modificado");
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($strUserSession);

            $em->persist($entity);
            $em->flush();

            $strAccion         = $objRequest->get('accion');
            $jsonAsignaciones  = json_decode($objRequest->get('data'));
            
            $arrayAsignaciones = $jsonAsignaciones->asignaciones;

            foreach($arrayAsignaciones as $objAsignacion)
            {
                $entityRelacion = null;

                if( $objAsignacion->modulo_id && $objAsignacion->accion_id )
                {

                    $entityRelacion = $em->getRepository('schemaBundle:SeguRelacionSistema')
                                         ->findOneBy(
                                                        array( 
                                                                'moduloId' => $objAsignacion->modulo_id,
                                                                'accionId' => $objAsignacion->accion_id
                                                             )
                                                    );
                }

                $objAsignacion = $em->getRepository('schemaBundle:SeguAsignacion')
                                    ->findOneBy(
                                                    array(
                                                            'perfilId'          => $entity->getId(),
                                                            'relacionSistemaId' => $entityRelacion->getId()
                                                         )
                                               );
                
                if($strAccion == 'Eliminar')
                {
                    if($objAsignacion)
                    {
                        $em->remove($objAsignacion);
                    }
                }
                else
                {
                    if(!is_object($objAsignacion))
                    {
                        $objAsignacion = new SeguAsignacion();
                    }

                    $objAsignacion->setPerfilId($entity);
                    $objAsignacion->setRelacionSistemaId($entityRelacion);
                    $objAsignacion->setUsrCreacion($strUserSession);
                    $objAsignacion->setFeCreacion(new \DateTime('now'));
                    $objAsignacion->setIpCreacion($objRequest->getClientIp());

                    // Save
                    $em->persist($objAsignacion);
                }
                
                $em->flush();
            }

            $strMensaje = "OK";
            
            $em->getConnection()->commit();
            $em->getConnection()->close();
        } 
        catch (Exception $e)
        {
            error_log($e->getMessage());
            
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();
            $em->getConnection()->close();
        }
        
        return new Response($strMensaje);
    }

    /**
    * @Secure(roles="ROLE_75-8")
    */  
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
			
            $entity = $em->getRepository('schemaBundle:SistPerfil')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SistPerfil entity.');
            }
			
            $entity->setEstado("Eliminado");
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            //$em->remove($entity);
			$em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sistperfil'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
    * @Secure(roles="ROLE_75-7")
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
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistPerfil')
            ->getJsonPerfiles($nombre,$estado,$start,$limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
	/**
    * @Secure(roles="ROLE_75-7")
    */  
    public function gridAccionesAction(){        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id_modulo = $peticion->query->get('id_modulo');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistModulo')
            ->getJsonAcciones($id_modulo);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
   
   /**
    * @Secure(roles="ROLE_75-7")
    */  
    public function gridAsignacionesAction($id){        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistPerfil')
            ->getJsonAsignacionesPerfil($id);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
	/**
    * @Secure(roles="ROLE_75-8")
    */  
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        
        $parametro = $request->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:SistPerfil', $id)) {
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

}
