<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiTipoSolicitud;
use telconet\schemaBundle\Form\AdmiTipoSolicitudType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiTipoSolicitudController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_35-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("35", "1");

        $entities = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findAll();

        return $this->render('administracionBundle:AdmiTipoSolicitud:index.html.twig', array(
            'item' => $entityItemMenu,
            'tiposolicitud' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_35-6")
    */
    public function showAction($id)
    {
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("35", "1");
        if (null == $tiposolicitud = $em->find('schemaBundle:AdmiTipoSolicitud', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoSolicitud que se quiere mostrar');
        }

        $nombreProceso = "";
        $nombreTarea = "";
        $nombreItemMenu = "";
        if($tiposolicitud->getProcesoId() && $tiposolicitud->getProcesoId()!="")
        {
            $EntityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneById($tiposolicitud->getProcesoId()); 
            $nombreProceso = $EntityProceso ? $EntityProceso->getNombreProceso() : "";
        }
        if($tiposolicitud->getTareaId() && $tiposolicitud->getTareaId()!="")
        {
            $EntityTarea = $em_soporte->getRepository('schemaBundle:AdmiTarea')->findOneById($tiposolicitud->getTareaId()); 
            $nombreTarea = $EntityTarea ? $EntityTarea->getNombreTarea() : ""; 
        }
        if($tiposolicitud->getItemMenuId() && $tiposolicitud->getItemMenuId()!="")
        {
            $EntityItemMenu = $em_seguridad->getRepository('schemaBundle:SistItemMenu')->findOneById($tiposolicitud->getItemMenuId());
            $nombreItemMenu = $EntityItemMenu ? $EntityItemMenu->getNombreItemMenu() : "";
        }

        return $this->render('administracionBundle:AdmiTipoSolicitud:show.html.twig', array(
            'item' => $entityItemMenu,
            'tiposolicitud'   => $tiposolicitud,
            'nombreProceso'   => $nombreProceso,
            'nombreTarea'   => $nombreTarea,
            'nombreItemMenu'   => $nombreItemMenu,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
    * @Secure(roles="ROLE_35-2")
    */
    public function newAction()
    {        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("35", "1");
        $entity = new AdmiTipoSolicitud();
        $form   = $this->createForm(new AdmiTipoSolicitudType(), $entity);

        return $this->render('administracionBundle:AdmiTipoSolicitud:new.html.twig', array(
            'item' => $entityItemMenu,
            'tiposolicitud' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_35-3")
    */
    public function createAction()
    {        
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("35", "1");
        $entity  = new AdmiTipoSolicitud();
        $form    = $this->createForm(new AdmiTipoSolicitudType(), $entity);        
        $form->bind($request);
        
        $peticion = $this->get('request');
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();
            try {  
                $escogido_proceso_id = $peticion->get('escogido_proceso_id');
                $escogido_tarea_id = $peticion->get('escogido_tarea_id');
                $escogido_itemmenu_id = $peticion->get('escogido_itemmenu_id');

                if($escogido_proceso_id && $escogido_proceso_id>0)
                {
                    $entity->setProcesoId($escogido_proceso_id);
                }
                if($escogido_tarea_id && $escogido_tarea_id>0)
                {
                    $entity->setTareaId($escogido_tarea_id);
                }
                if($escogido_itemmenu_id && $escogido_itemmenu_id>0)
                {
                    $entity->setItemMenuId($escogido_itemmenu_id);
                }

                $entity->setEstado('Activo');
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($request->getSession()->get('user'));
                $entity->setFeUltMod(new \DateTime('now'));
                $entity->setUsrUltMod($request->getSession()->get('user'));

                $em->persist($entity);
                $em->flush();
                $em->getConnection()->commit();
            
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
            }
            return $this->redirect($this->generateUrl('admitiposolicitud_show', array('id' => $entity->getId())));
        }
        
        return $this->render('administracionBundle:AdmiTipoSolicitud:new.html.twig', array(
            'item' => $entityItemMenu,
            'tiposolicitud' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
    * @Secure(roles="ROLE_35-4")
    */
    public function editAction($id)
    {                
        $em = $this->getDoctrine()->getManager("telconet");
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("35", "1");

        if (null == $tiposolicitud = $em->find('schemaBundle:AdmiTipoSolicitud', $id)) {
            throw new NotFoundHttpException('No existe el AdmiTipoSolicitud que se quiere modificar');
        }

        $nombreProceso = "";
        $nombreTarea = "";
        $nombreItemMenu = "";
        if($tiposolicitud->getProcesoId() && $tiposolicitud->getProcesoId()!="")
        {
            $EntityProceso = $em_soporte->getRepository('schemaBundle:AdmiProceso')->findOneById($tiposolicitud->getProcesoId()); 
            $nombreProceso = $EntityProceso->getNombreProceso(); 
        }
        if($tiposolicitud->getTareaId() && $tiposolicitud->getTareaId()!="")
        {
            $EntityTarea = $em_soporte->getRepository('schemaBundle:AdmiTarea')->findOneById($tiposolicitud->getTareaId()); 
            $nombreTarea = $EntityTarea->getNombreTarea(); 
        }
        if($tiposolicitud->getItemMenuId() && $tiposolicitud->getItemMenuId()!="")
        {
            $EntityItemMenu = $em_seguridad->getRepository('schemaBundle:SistItemMenu')->findOneById($tiposolicitud->getItemMenuId());
            $nombreItemMenu = $EntityItemMenu->getNombreItemMenu(); 
        }
        
        $formulario =$this->createForm(new AdmiTipoSolicitudType(), $tiposolicitud);
        return $this->render('administracionBundle:AdmiTipoSolicitud:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'tiposolicitud'   => $tiposolicitud,
			'nombreProceso'   => $nombreProceso,
			'nombreTarea'   => $nombreTarea,
			'nombreItemMenu'   => $nombreItemMenu));
    }
    
    /**
    * @Secure(roles="ROLE_35-5")
    */
    public function updateAction($id)
    {        
        $em = $this->getDoctrine()->getManager('telconet');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("35", "1");
        $entity = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTipoSolicitud entity.');
        }

        $editForm   = $this->createForm(new AdmiTipoSolicitudType(), $entity);
        $request = $this->getRequest();
        $editForm->bind($request);

        $peticion = $this->get('request');
        if ($editForm->isValid()) {            
            try {  
                $escogido_proceso_id = $peticion->get('escogido_proceso_id');
                $escogido_tarea_id = $peticion->get('escogido_tarea_id');
                $escogido_itemmenu_id = $peticion->get('escogido_itemmenu_id');

                if($escogido_proceso_id && $escogido_proceso_id>0)
                {
                    $entity->setProcesoId($escogido_proceso_id);
                }
                if($escogido_tarea_id && $escogido_tarea_id>0)
                {
                    $entity->setTareaId($escogido_tarea_id);
                }
                if($escogido_itemmenu_id && $escogido_itemmenu_id>0)
                {
                    $entity->setItemMenuId($escogido_itemmenu_id);
                }

                /*Para que guarde la fecha y el usuario correspondiente*/
                $entity->setFeUltMod(new \DateTime('now'));
                //$entity->setIdUsuarioModificacion($user->getUsername());
                $entity->setUsrUltMod($request->getSession()->get('user'));
                /*Para que guarde la fecha y el usuario correspondiente*/

                $em->persist($entity);
                $em->flush();            
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
            }            
            
            return $this->redirect($this->generateUrl('admitiposolicitud_show', array('id' => $id)));
        }

        return $this->render('administracionBundle:AdmiTipoSolicitud:edit.html.twig',array(
            'item' => $entityItemMenu,
            'tiposolicitud'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
    * @Secure(roles="ROLE_35-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet');
            $entity = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTipoSolicitud entity.');
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

        return $this->redirect($this->generateUrl('admitiposolicitud'));
    }

    /**
    * @Secure(roles="ROLE_35-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTipoSolicitud', $id)) {
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
    * @Secure(roles="ROLE_35-7")
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
        
        $em_soporte = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:AdmiTipoSolicitud')
            ->generarJson($em_soporte, $em_seguridad, $nombre,$estado,$start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_35-146")
    */
    public function getListadoProcesosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $session    = $peticion->getSession();
        $codEmpresa = $session->get('idEmpresa');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $procesos = $em->getRepository('schemaBundle:AdmiProceso')->getRegistros("","", "Activo", 0, 300,$codEmpresa,"Todos");

        if($procesos && count($procesos)>0)
        {
            $num = count($procesos);
            
            $arr_encontrados[]=array('id_proceso' =>0, 'nombre_proceso' =>"Seleccion un proceso");
            foreach($procesos as $key => $proceso)
            {                
                $arr_encontrados[]=array('id_proceso' =>$proceso->getId(),
                                         'nombre_proceso' =>trim($proceso->getNombreProceso()));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_proceso' => 0 , 'nombre_proceso' => 'Ninguno','proceso_id' => 0 , 'proceso_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $resultado);
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_35-147")
    */
    public function getListadoTareasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
    
        $peticion = $this->get('request');
        $idProceso = $peticion->query->get('id_proceso');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        if($idProceso!=0)
        {
            $tareas = $em->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($idProceso);
            if($tareas && count($tareas)>0)
            {
                $num = count($tareas);

                $arr_encontrados[]=array('id_tarea' =>0, 'nombre_tarea' =>"Seleccion una tarea");
                foreach($tareas as $key => $tarea)
                {                
                    $arr_encontrados[]=array('id_tarea' =>$tarea->getId(),
                                            'nombre_tarea' =>trim($tarea->getNombreTarea()));
                }

                if($num == 0)
                {
                    $resultado= array('total' => 1 ,
                                    'encontrados' => array('id_tarea' => 0 , 'nombre_tarea' => 'Ninguno','tarea_id' => 0 , 'tarea_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                    $objJson = json_encode( $resultado);
                }
                else
                {
                    $data=json_encode($arr_encontrados);
                    $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
                }
            }
            else
            {
                $objJson= '{"total":"0","encontrados":[]}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_35-13")
    */
    public function getListadoItemsMenuAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
    
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        $items = $em->getRepository('schemaBundle:SistItemMenu')->getItemMenus("", "Activo", 0, 300);
        if($items && count($items)>0)
        {
            $num = count($items);

            $arr_encontrados[]=array('id_item' =>0, 'nombre_item' =>"Seleccion un item menu");
            foreach($items as $key => $item)
            {                
                $arr_encontrados[]=array('id_item' =>$item->getId(),
                                        'nombre_item' =>trim($item->getNombreItemMenu()));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                'encontrados' => array('id_item' => 0 , 'nombre_item' => 'Ninguno','item_id' => 0 , 'item_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
                $objJson = json_encode( $resultado);
            }
            else
            {
                $data=json_encode($arr_encontrados);
                $objJson= '{"total":"'.$num.'","encontrados":'.$data.'}';
            }
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }

    /**
     * getFormaPagoJsonAction, obtiene los tipos de solicitud
     * @version 1.0 Alexander Samaniego <awsamaniego@telconet.ec>
     * @since 1.0 19-01.2015
     * @return Response retorna un json con los tipos de solicitud
     */
    public function getTipoSolicitudJsonAction()
    {
        $intTotal             = 0;
        $objAdmiTipoSolicitud = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                            ->getRegistros('', 'Activo', 0, 100);

        $arrayTipoSolicitudes = array();
        foreach($objAdmiTipoSolicitud as $objAdmiTipoSolicitud)
        {
            $intTotal               = $intTotal +1;
            $arrayTipoSolicitudes[] = array('intIdTipoSolcitud'     => $objAdmiTipoSolicitud->getId(), 
                                            'strDescTipoSolicitud'  => $objAdmiTipoSolicitud->getDescripcionSolicitud());
        }

        $objResponse = new Response(json_encode(array('jsonTipoSolicitudes' => $arrayTipoSolicitudes, 
                                                      'intTotal'            => $intTotal)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }//getTipoSolicitudJsonAction

    /**
     * getEstadosTipoSolicitudesJsonAction, obtiene el json de los estados de las solicitudes
     * @version 1.0 Alexander Samaniego <awsamaniego@telconet.ec>
     * @since 1.0 19-01.2015
     * @return Response retorna un json con los estados de las solicitudes
     */
    public function getEstadosTipoSolicitudesJsonAction()
    {
        $em = $this->getDoctrine()->getManager();
        $jsonOficinas = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->getEstadosTipoSolicitudesJson();
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRespuesta->setContent($jsonOficinas);
        return $objRespuesta;
    }//getEstadosTipoSolicitudesJsonAction

}