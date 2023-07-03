<?php

namespace telconet\seguridadBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\schemaBundle\Entity\SistModulo;
use telconet\schemaBundle\Form\SistModuloType;
use telconet\schemaBundle\Entity\SeguRelacionSistema;
use Symfony\Component\HttpFoundation\Response;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
/**
 * SistModulo controller.
 *
 */
class SistModuloController extends Controller implements TokenAuthenticatedController
{
    /**
    * @Secure(roles="ROLE_74-1")
    */     
    public function indexAction()
    {
        $request  = $this->get('request');
        $session  = $request->getSession();
        
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("74", "1");
        
        return $this->render('seguridadBundle:SistModulo:index.html.twig', array(
             'item' => $entityItemMenu
        ));
        
    }

    /**
    * @Secure(roles="ROLE_74-6")
    */ 
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("74", "1");
        $entity = $em->getRepository('schemaBundle:SistModulo')->find($id);
		
		if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistModulo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
		
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
			//'acc_relaciondas'=>$acc_relacionadas,
			//'img_opcion_menu'=>$img_opcion
        );
        
        return $this->render('seguridadBundle:SistModulo:show.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_74-2")
    */ 
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("74", "1");
        $entity = new SistModulo();
        $form   = $this->createForm(new SistModuloType($options), $entity);
        
        return $this->render('seguridadBundle:SistModulo:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }

    /**
    * @Secure(roles="ROLE_74-3")
    */ 
    public function createAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("74", "1");
        $entity  = new SistModulo();
        $request = $this->getRequest();
        
        $form    = $this->createForm(new SistModuloType($options), $entity);
        $form->handleRequest($request);
        $peticion = $this->get('request');
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            try {
                $entity->setEstado("Activo");

                /*Para que guarde la fecha y el usuario correspondiente*/
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($request->getSession()->get('user'));
                // Save
                $em->persist($entity);
                $em->flush();
				
                $json_relacion_sistema = json_decode($peticion->get('relaciones'));
                $array_relacion_sistema = $json_relacion_sistema->relaciones;
                foreach($array_relacion_sistema as $relacion)
                {
                    $tmp_accion = null;
                    $tmp_item_menu = null;
                    
                    
                    if($relacion->accion_id)
                    {
                        $tmp_accion = $em->getRepository('schemaBundle:SistAccion')->find($relacion->accion_id);
                    }
                    if($relacion->item_menu_id)
                    {
                        $tmp_item_menu = $em->getRepository('schemaBundle:SistItemMenu')->find($relacion->item_menu_id);
                    }
                    $tarea_id = $relacion->tarea_id;
                    $relacion = new SeguRelacionSistema();

                    $relacion->setModuloId($entity);
                    $relacion->setTareaInterfaceModeloTrId($tarea_id);
                    //$relacion->setAccion($tmp_accion);
                    $relacion->setAccionId($tmp_accion);
                    if($tmp_item_menu)
                    {
                        $relacion->setItemMenuId($tmp_item_menu);
                    }
					$relacion->setFeCreacion(new \DateTime('now'));
                    $relacion->setUsrCreacion($request->getSession()->get('user'));
                    $relacion->setIpCreacion($peticion->getClientIp());

                    // Save
                    $em->persist($relacion);
                    $em->flush();

                }
                //$em->getRepository('AdministracionBundle:SeguRelacionSistema')
                   //->borrarDistintosEleccion($array_accion_id, $admi_modulo->getIdModulo());
                
                $em->getConnection()->commit();
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            return $this->redirect($this->generateUrl('sistmodulo_show', array('id' => $entity->getId())));
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
		
        return $this->render('schemaBundle:SistModulo:new.html.twig', $parametros);
    }

    /**
    * @Secure(roles="ROLE_74-4")
    */ 
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("74", "1");
        $entity = $em->getRepository('schemaBundle:SistModulo')->find($id);
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistModulo entity.');
        }
        
        $editForm = $this->createForm(new SistModuloType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $parametros=array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        );

        return $this->render('seguridadBundle:SistModulo:edit.html.twig',$parametros);
    }

    /**
    * @Secure(roles="ROLE_74-5")
    */ 
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("74", "1");
        $entity = $em->getRepository('schemaBundle:SistModulo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find SistModulo entity.');
        }
        
		
        $editForm   = $this->createForm(new SistModuloType($options), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->handleRequest($request);
        $peticion = $this->get('request');
        if ($editForm->isValid()) {
            $em->getConnection()->beginTransaction();
            try {
                $entity->setEstado("Modificado");

                /*Para que guarde la fecha y el usuario correspondiente*/
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($request->getSession()->get('user'));
                // Save
                $em->persist($entity);
                $em->flush();
				
                $json_relacion_sistema = json_decode($peticion->get('relaciones'));
                //print_r($peticion);die;
                $array_relacion_sistema = $json_relacion_sistema->relaciones;
                $array_relaciones = array();
                //print_r($relaciones);die;
                
                foreach($array_relacion_sistema as $relacion)
                {
                    $tmp_accion = null;
                    $tmp_item_menu = null;
                    
                    $tarea_id=$relacion->tarea_id;
                    if($relacion->accion_id)
                    {
                        $tmp_accion = $em->getRepository('schemaBundle:SistAccion')->find($relacion->accion_id);
                    }
                    if($relacion->item_menu_id)
                    {
                        $tmp_item_menu = $em->getRepository('schemaBundle:SistItemMenu')->find($relacion->item_menu_id);
                        $relacion = $em->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array('moduloId' => $entity->getId(),'accionId' => $tmp_accion->getId(),'itemMenuId' => $tmp_item_menu->getId()));
                    }
                    else
                        $relacion = $em->getRepository('schemaBundle:SeguRelacionSistema')->findOneBy(array('moduloId' => $entity->getId(),'accionId' => $tmp_accion->getId()));
                    
                    if(!is_object($relacion))
                        $relacion = new SeguRelacionSistema();
						
                    $relacion->setTareaInterfaceModeloTrId($tarea_id);
                    $relacion->setModuloId($entity);
                    //$relacion->setAccion($tmp_accion);
                    $relacion->setAccionId($tmp_accion);
                    if($tmp_item_menu)
                    {
                        $relacion->setItemMenuId($tmp_item_menu);
                    }
                    $relacion->setFeCreacion(new \DateTime('now'));
                    $relacion->setUsrCreacion($request->getSession()->get('user'));
                    $relacion->setIpCreacion($peticion->getClientIp());

                    // Save
                    $em->persist($relacion);
                    $em->flush();
                    
                    $array_relaciones[] = $relacion->getId();
                }
                
                $em->getRepository('schemaBundle:SeguRelacionSistema')
                   ->borrarDistintosEleccion($array_relaciones, $entity->getId());
                
                $em->getConnection()->commit();
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
            }
            return $this->redirect($this->generateUrl('sistmodulo_show', array('id' => $entity->getId())));
        }

        $parametros=array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        );
        
        if ($error)
            $parametros['error']=$error;
		
        return $this->render('seguridadBundle:SistModulo:edit.html.twig', $parametros );
    }

    /**
    * @Secure(roles="ROLE_74-8")
    */ 
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_seguridad');
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->handleRequest($request);

        if ($form->isValid()) {
			
            $entity = $em->getRepository('schemaBundle:SistModulo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find SistModulo entity.');
            }
			
            $entity->setEstado("Eliminado");
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($request->getSession()->get('user'));
            //$em->remove($entity);
			$em->persist($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('sistmodulo'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
    * @Secure(roles="ROLE_74-7")
    */ 
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
		$queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado = $peticion->get('estado');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_seguridad")
            ->getRepository('schemaBundle:SistModulo')
            ->getJsonModulo($nombre,$estado,$start,$limit);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
    * @Secure(roles="ROLE_74-7")
    */ 
    public function gridRelacionesAction($id)
    {
        $request = $this->getRequest();		    
        
        //print_r($estado);die();
        $em = $this->get('doctrine')->getManager('telconet_seguridad');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emSoporte = $this->get('doctrine')->getManager('telconet_soporte');

        $datos= $em->getRepository('schemaBundle:SeguRelacionSistema')->getRelaciones($id);
        $i=1;
        foreach ($datos as $dato):
                        if($i % 2==0)
                                $clase='k-alt';
                        else
                                $clase='';

            if($i % 2==0)
                    $clase='k-alt';
            else
                    $clase='';

            $urlVer = $this->generateUrl('sistmodulo_show', array('id' => $dato->getId()));
            $urlEditar = $this->generateUrl('sistmodulo_edit', array('id' => $dato->getId()));

            if($dato->getTareaInterfaceModeloTrId()){
                $tareaInterface = $emSoporte->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')->find($dato->getTareaInterfaceModeloTrId());
                $tarea = $tareaInterface->getTareaId();
                $modelo = $emInfraestructura->find('schemaBundle:AdmiModeloElemento', $tareaInterface->getModeloElementoId());
                $tareaNombre = $tarea->getNombreTarea().'/'.$modelo->getNombreModeloElemento();
            }
            else
                $tareaNombre="";
            
            $linkVer = $urlVer;

            $arreglo[]= array(
                'id' =>$dato->getId(),
                'accion_id'=>$dato->getAccionId()->getId(),
                'item_menu_id'=>($dato->getItemMenuId())?$dato->getItemMenuId()->getId():"",
                'tarea_id'=>($dato->getTareaInterfaceModeloTrId())?$dato->getTareaInterfaceModeloTrId():"",
                'accion_nombre'=> $dato->getAccionId()->getNombreAccion(),
                'item_menu_nombre'=> ($dato->getItemMenuId())?$dato->getItemMenuId()->getNombreItemMenu():"",
                'tarea_nombre' => $tareaNombre,
             );             

         $i++;     
        endforeach;
        $results = array();
        if (!empty($arreglo)){
            $data=json_encode($arreglo);
            $resultado= '{"total":"'.count($arreglo).'","encontrados":'.$data.'}';
        }
        else
            $resultado= '{"total":"0","encontrados":""}';
        $response = new Response($resultado);
        //print_r($response);

        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    /**
    * @Secure(roles="ROLE_74-8")
    */ 
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        
        $parametro = $request->get('param');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:SistModulo', $id)) {
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
    
    /**
    * @Secure(roles="ROLE_74-19")
    */ 
    public function getTareasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $nombre = $peticion->get('nombre');
        $estado = $peticion->get('estado');
        
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
        
        $emTecnico = $this->getDoctrine()->getManager("telconet_infraestructura");
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')
            ->getJsonTareasModulo($nombre,$estado,$start,100,$emTecnico);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
     
    /**
     * Valida que el nombre del modulo a crear sea unico
    * @param parametros variables del controlador
    * @return mensaje de validacion
    * @author Jesus Bozada <jbozada@telconet.ec>
    * @version 1.0 15-09-2014
    */
    /**
    * @Secure(roles="ROLE_74-3")
    */
    public function validaUnicoModuloAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $request = $this->get('request');
        $nombreModulo = $request->get('nombreModulo');
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        $arraySistModulo = $em->getRepository('schemaBundle:SistModulo')->findBy(array('nombreModulo' => $nombreModulo));
        if(count($arraySistModulo) > 0)
        {
            $respuesta->setContent("El modulo ya existe registrado");
        }
        else
        {
            $respuesta->setContent("ok");
        }
        return $respuesta;
    }

}
