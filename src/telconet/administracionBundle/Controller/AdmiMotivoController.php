<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Form\AdmiMotivoType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

class AdmiMotivoController extends Controller implements TokenAuthenticatedController
{ 
    /**
    * @Secure(roles="ROLE_21-1")
    */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("21", "1");

        $entities = $em->getRepository('schemaBundle:AdmiMotivo')->findAll();

        return $this->render('administracionBundle:AdmiMotivo:index.html.twig', array(
            'item' => $entityItemMenu,
            'motivo' => $entities
        ));
    }
    
    /**
    * @Secure(roles="ROLE_21-6")
    */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("21", "1");

        $arrayRelacionSistema = array();
        
        $RelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneById($id);
        if($RelacionSistema && count($RelacionSistema)>0)
        {
            $id_relacionsistema = $id;
            $id_modulo = ($RelacionSistema->getModuloId() ? ($RelacionSistema->getModuloId()->getId() ? $RelacionSistema->getModuloId()->getId() : 0) : 0);
            $nombre_modulo = ($RelacionSistema->getModuloId() ? ($RelacionSistema->getModuloId()->getNombreModulo() ? $RelacionSistema->getModuloId()->getNombreModulo() : "") : "");
            $id_itemmenu = ($RelacionSistema->getItemMenuId() ? ($RelacionSistema->getItemMenuId()->getId() ? $RelacionSistema->getItemMenuId()->getId() : 0) : 0);
            $nombre_itemmenu = ($RelacionSistema->getItemMenuId() ? ($RelacionSistema->getItemMenuId()->getNombreItemMenu() ? $RelacionSistema->getItemMenuId()->getNombreItemMenu() : "") : "");
            $id_accion = ($RelacionSistema->getAccionId() ? ($RelacionSistema->getAccionId()->getId() ? $RelacionSistema->getAccionId()->getId() : 0) : 0);
            $nombre_accion = ($RelacionSistema->getAccionId() ? ($RelacionSistema->getAccionId()->getNombreAccion() ? $RelacionSistema->getAccionId()->getNombreAccion() : "") : "");

            $arrayRelacionSistema["id_relacionsistema"] = $id_relacionsistema;
            $arrayRelacionSistema["id_modulo"] = $id_modulo;
            $arrayRelacionSistema["id_itemmenu"] = $id_itemmenu;
            $arrayRelacionSistema["id_accion"] = $id_accion;
            $arrayRelacionSistema["nombre_modulo"] = $nombre_modulo;
            $arrayRelacionSistema["nombre_itemmenu"] = $nombre_itemmenu;
            $arrayRelacionSistema["nombre_accion"] = $nombre_accion;


            $motivos = $em->getRepository('schemaBundle:AdmiMotivo')->findOneByRelacionSistemaId($id);
            $num = count($motivos);
            if($num>0)
            {
                if (null == $motivo = $em->find('schemaBundle:AdmiMotivo', $motivos->getId())) {
                    throw new NotFoundHttpException('No existe el AdmiMotivo que se quiere mostrar');
                }
            }
            else
            {
                if (null == $modulo = $em->find('schemaBundle:AdmiMotivo', $id)) {
                    throw new NotFoundHttpException('No existe el AdmiMotivo que se quiere modificar');
                }
            }
        }
        else
        {
            throw new NotFoundHttpException('No existe la Relacion Sistema que se quiere modificar');
        }
        
        $peticion = $this->get('request');
        return $this->render('administracionBundle:AdmiMotivo:show.html.twig', array(
            'item' => $entityItemMenu,
            'motivo'   => $motivo,
            'flag' =>$peticion->get('flag'),
            'relacionsistema'   => $arrayRelacionSistema
        ));
    }
    
    /**
    * @Secure(roles="ROLE_21-2")
    */
    public function newAction()
    {        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("21", "1");
        return $this->render('administracionBundle:AdmiMotivo:new.html.twig', array(
            'item' => $entityItemMenu            
        ));
    }
    /**
     * 
     * Metodo que sirve para crear un nuevo motivo
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 10-12-2015
     * 
     * @version 1.0 Inicial     
     * 
    * @Secure(roles="ROLE_21-3")
    */
    public function createAction()
    {
        $request = $this->get('request');
        $em = $this->get('doctrine')->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("21", "1");

        $entity = new AdmiMotivo();
        $form = $this->createForm(new AdmiMotivoType(), $entity);
        $form->bind($request);

        $peticion = $this->get('request');

        $em->getConnection()->beginTransaction();

        try
        {
            $escogido_modulo_id = $peticion->get('escogido_modulo_id');
            $escogido_itemmenu_id = $peticion->get('escogido_itemmenu_id');
            $escogido_accion_id = $peticion->get('escogido_accion_id');
            $json_motivos = json_decode($peticion->get('motivos'));
            if($json_motivos && $escogido_modulo_id && $escogido_modulo_id > 0 && $escogido_itemmenu_id && 
                        $escogido_itemmenu_id > 0 && $escogido_accion_id && $escogido_accion_id > 0)
            {
                $relacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')
                                    ->searchOneRelacionSistema($escogido_modulo_id, $escogido_itemmenu_id, $escogido_accion_id);
                
                $relacionsistema_id = $relacionSistema["id"];
                $arrayIdsMotivos = array();

                $array_motivos = $json_motivos->motivos;
                foreach($array_motivos as $motivo)
                {
                    if($motivo->id_motivo != "" && $motivo->id_motivo > 0)
                    {
                        $arrayIdsMotivos[] = $motivo->id_motivo;

                        $entityMotivo = $em->getRepository('schemaBundle:AdmiMotivo')->findOneById($motivo->id_motivo);
                        if($entityMotivo->getNombreMotivo() != $motivo->nombre_motivo)
                        {
                            $entityMotivo->setNombreMotivo($motivo->nombre_motivo);
                            $entityMotivo->setRelacionSistemaId($relacionsistema_id);
                            $entityMotivo->setEstado('Modificado');
                            $entityMotivo->setFeUltMod(new \DateTime('now'));
                            $entityMotivo->setUsrUltMod($request->getSession()->get('user'));

                            // Save
                            $em->persist($entityMotivo);
                            $em->flush();
                        }
                    }
                    else
                    {
                        $entityMotivo = new AdmiMotivo();
                        $entityMotivo->setNombreMotivo($motivo->nombre_motivo);
                        $entityMotivo->setRelacionSistemaId($relacionsistema_id);
                        $entityMotivo->setEstado('Activo');
                        $entityMotivo->setFeCreacion(new \DateTime('now'));
                        $entityMotivo->setUsrCreacion($request->getSession()->get('user'));
                        $entityMotivo->setFeUltMod(new \DateTime('now'));
                        $entityMotivo->setUsrUltMod($request->getSession()->get('user'));

                        // Save
                        $em->persist($entityMotivo);
                        $em->flush();

                        $arrayIdsMotivos[] = $entityMotivo->getId();
                    }
                }

                //*****  PROCESO BORRAR LOS NO SELECCIONADOS...
                $ArrayBorrarDistintos = $em->getRepository('schemaBundle:AdmiMotivo')
                                        ->retornaDistintosEleccion($arrayIdsMotivos, $relacionsistema_id);
                if($ArrayBorrarDistintos && count($ArrayBorrarDistintos) > 0)
                {
                    foreach($ArrayBorrarDistintos as $key => $entityBorrar)
                    {
                        $entityBorrar->setEstado('Eliminado');
                        $entityBorrar->setFeUltMod(new \DateTime('now'));
                        $entityBorrar->setUsrUltMod($request->getSession()->get('user'));
                        // Save
                        $em->persist($entityBorrar);
                        $em->flush();
                    }
                }
            }
            $em->getConnection()->commit();

            return $this->redirect($this->generateUrl('admimotivo_show', array('id' => $relacionSistema["id"])));
        }
        catch(Exception $e)
        {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback();

            return $this->render('administracionBundle:AdmiMotivo:new.html.twig', array(
                    'item' => $entityItemMenu,
                    'motivo' => $entity,
                    'form' => $form->createView()
            ));
        }
    }
    
    /**
    * @Secure(roles="ROLE_21-4")
    */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_general");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("21", "1");

        $arrayRelacionSistema = array();
        
        $RelacionSistema = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->findOneById($id);
        if($RelacionSistema && count($RelacionSistema)>0)
        {
            $id_relacionsistema = $id;
            $id_modulo = ($RelacionSistema->getModuloId() ? ($RelacionSistema->getModuloId()->getId() ? $RelacionSistema->getModuloId()->getId() : 0) : 0);
            $nombre_modulo = ($RelacionSistema->getModuloId() ? ($RelacionSistema->getModuloId()->getNombreModulo() ? $RelacionSistema->getModuloId()->getNombreModulo() : "") : "");
            $id_itemmenu = ($RelacionSistema->getItemMenuId() ? ($RelacionSistema->getItemMenuId()->getId() ? $RelacionSistema->getItemMenuId()->getId() : 0) : 0);
            $nombre_itemmenu = ($RelacionSistema->getItemMenuId() ? ($RelacionSistema->getItemMenuId()->getNombreItemMenu() ? $RelacionSistema->getItemMenuId()->getNombreItemMenu() : "") : "");
            $id_accion = ($RelacionSistema->getAccionId() ? ($RelacionSistema->getAccionId()->getId() ? $RelacionSistema->getAccionId()->getId() : 0) : 0);
            $nombre_accion = ($RelacionSistema->getAccionId() ? ($RelacionSistema->getAccionId()->getNombreAccion() ? $RelacionSistema->getAccionId()->getNombreAccion() : "") : "");

            $arrayRelacionSistema["id_relacionsistema"] = $id_relacionsistema;
            $arrayRelacionSistema["id_modulo"] = $id_modulo;
            $arrayRelacionSistema["id_itemmenu"] = $id_itemmenu;
            $arrayRelacionSistema["id_accion"] = $id_accion;
            $arrayRelacionSistema["nombre_modulo"] = $nombre_modulo;
            $arrayRelacionSistema["nombre_itemmenu"] = $nombre_itemmenu;
            $arrayRelacionSistema["nombre_accion"] = $nombre_accion;

            $motivos = $em->getRepository('schemaBundle:AdmiMotivo')->findOneByRelacionSistemaId($id);
            $num = count($motivos);
            if($num>0)
            {
                if (null == $motivo = $em->find('schemaBundle:AdmiMotivo', $motivos->getId())) {
                    throw new NotFoundHttpException('No existe el AdmiMotivo que se quiere modificar');
                }
            }
            else
            {
                if (null == $modulo = $em->find('schemaBundle:AdmiMotivo', $id)) {
                    throw new NotFoundHttpException('No existe el AdmiMotivo que se quiere modificar');
                }
            }
        }
        else
        {
            throw new NotFoundHttpException('No existe la Relacion Sistema que se quiere modificar');
        }
        
        $formulario =$this->createForm(new AdmiMotivoType(), $motivo);
        return $this->render('administracionBundle:AdmiMotivo:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'motivo'   => $motivo,
			'relacionsistema'   => $arrayRelacionSistema
		));
    }
    
    /**
     * 
     * Metodo que sirve para realizar la actualizacion de los motivos creados en la administracion
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 10-12-2015
     * 
     * @version 1.0 Inicial     
     * 
    * @Secure(roles="ROLE_21-5")
    */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("21", "1");                
        $entity = $em->getRepository('schemaBundle:AdmiMotivo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiMotivo entity.');
        }
        
        $peticion = $this->get('request');
        
        $escogido_modulo_id       = $peticion->get('escogido_modulo_id');            
        $escogido_accion_id       = $peticion->get('escogido_accion_id');
        $relacionsistema_id       = $peticion->get('relacionsistema_id');
        $escogido_itemmenu_id     = $peticion->get('escogido_itemmenu_id');
        $escogido_nombre_modulo   = $peticion->get('escogido_nombre_modulo');
        $escogido_nombre_itemmenu = $peticion->get('escogido_nombre_itemmenu');
        $escogido_nombre_accion   = $peticion->get('escogido_nombre_accion');
      
        $em->getConnection()->beginTransaction();
        
        try {                        
                        
            $json_motivos = json_decode($peticion->get('motivos'));
            
            if($json_motivos && $escogido_modulo_id && $escogido_modulo_id>0 && $escogido_accion_id && $escogido_accion_id>0 )
            {
                $arrayIdsMotivos = array();

                $array_motivos = $json_motivos->motivos;
                
                foreach($array_motivos as $motivo)
                {               
                    if($motivo->id_motivo != "" && $motivo->id_motivo >0)
                    {
                        $arrayIdsMotivos[] = $motivo->id_motivo;

                        $entityMotivo = $em->getRepository('schemaBundle:AdmiMotivo')->findOneById($motivo->id_motivo);
                        if($entityMotivo->getNombreMotivo() != $motivo->nombre_motivo)
                        {
                            $entityMotivo->setNombreMotivo($motivo->nombre_motivo);
                            $entityMotivo->setRelacionSistemaId($relacionsistema_id);
                            $entityMotivo->setEstado('Modificado');
                            $entityMotivo->setFeUltMod(new \DateTime('now'));
                            $entityMotivo->setUsrUltMod($peticion->getSession()->get('user'));

                            // Save
                            $em->persist($entityMotivo);
                            $em->flush();
                        }
                    }
                    else
                    {
                        $entityMotivo  = new AdmiMotivo();
                        $entityMotivo->setNombreMotivo($motivo->nombre_motivo);
                        $entityMotivo->setRelacionSistemaId($relacionsistema_id);
                        $entityMotivo->setEstado('Activo');
                        $entityMotivo->setFeCreacion(new \DateTime('now'));
                        $entityMotivo->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityMotivo->setFeUltMod(new \DateTime('now'));
                        $entityMotivo->setUsrUltMod($peticion->getSession()->get('user'));

                        // Save
                        $em->persist($entityMotivo);
                        $em->flush();

                        $arrayIdsMotivos[] = $entityMotivo->getId();
                    }
                }

                //*****  PROCESO BORRAR LOS NO SELECCIONADOS...
                $ArrayBorrarDistintos = $em->getRepository('schemaBundle:AdmiMotivo')
                                        ->retornaDistintosEleccion($arrayIdsMotivos, $relacionsistema_id);
                if($ArrayBorrarDistintos && count($ArrayBorrarDistintos)>0)
                {
                    foreach($ArrayBorrarDistintos as $key => $entityBorrar)
                    {
                        $entityBorrar->setEstado('Eliminado');
                        $entityBorrar->setFeUltMod(new \DateTime('now'));
                        $entityBorrar->setUsrUltMod($peticion->getSession()->get('user'));
                        // Save
                        $em->persist($entityBorrar);
                        $em->flush();
                    }
                }
            }            
            
            $em->getConnection()->commit();
            
            return $this->redirect($this->generateUrl('admimotivo_show', array('id' => $relacionsistema_id)));
            
        } catch (\Exception $e) {
            // Rollback the failed transaction attempt
            $em->getConnection()->rollback(); 
                        
            //Obteniendo valores de la reacion sistemas para devolver a la pantalla en caso de fallo
            
            $arrayRelacionSistema["id_relacionsistema"] = $relacionsistema_id;
            $arrayRelacionSistema["id_modulo"]       = $escogido_modulo_id;
            $arrayRelacionSistema["id_itemmenu"]     = $escogido_itemmenu_id;
            $arrayRelacionSistema["id_accion"]       = $escogido_accion_id;
            $arrayRelacionSistema["nombre_modulo"]   = $escogido_nombre_modulo;
            $arrayRelacionSistema["nombre_itemmenu"] = $escogido_nombre_itemmenu;
            $arrayRelacionSistema["nombre_accion"]   = $escogido_nombre_accion;
            
            $formulario =$this->createForm(new AdmiMotivoType(), $entity);
            
            return $this->render('administracionBundle:AdmiMotivo:edit.html.twig',array(
                'item'        => $entityItemMenu,
                'motivo'      => $entity,
                'edit_form'   => $formulario->createView(),
                'relacionsistema'   => $arrayRelacionSistema
            ));
        }
    }
    
    /**
    * @Secure(roles="ROLE_21-8")
    */
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_general');
            $entity = $em->getRepository('schemaBundle:AdmiMotivo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiMotivo entity.');
            }
            $estado = 'Eliminado';
            $entity->setEstado($estado);
            /*Para que guarde la fecha y el usuario correspondiente*/
            $entity->setFeUltMod(new \DateTime('now'));
            //$entity->setIdUsuarioModificacion($user->getUsername());
            $entity->setUsrUltMod($request->getSession()->get('user'));
            /*Para que guarde la fecha y el usuario correspondiente*/
			
            //$em->remove($entity);
            $em->flush();
//        }

        return $this->redirect($this->generateUrl('admimotivo'));
    }

    /**
    * @Secure(roles="ROLE_21-9")
    */
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $request = $this->get('request');
        $parametro = $request->get('param');
        $em = $this->getDoctrine()->getManager("telconet_general");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):            
            $motivos = $em->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($id);
            if($motivos && count($motivos)>0)
            {
                foreach($motivos as $key => $entityBorrar)
                {        
                    $entityBorrar->setEstado('Eliminado');
                    $entityBorrar->setFeUltMod(new \DateTime('now'));
                    $entityBorrar->setUsrUltMod($request->getSession()->get('user'));
                    // Save
                    $em->persist($entityBorrar);
                    $em->flush();
                }    
                
                $respuesta->setContent("Se eliminaron los motivos");
            }
            else
            {
                $respuesta->setContent("No existen los motivos");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    /*
     * Llena el grid de consulta.
     */
    /**
    * @Secure(roles="ROLE_21-7")
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
        
        $em_seguridad = $this->getDoctrine()->getManager("telconet_seguridad");
        $objJson = $this->getDoctrine()
            ->getManager("telconet_general")
            ->getRepository('schemaBundle:AdmiMotivo')
            ->generarJson2($em_seguridad, $start,$limit);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_21-12")
    */
    public function getListadoModulosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        $modulos = $em->getRepository('schemaBundle:SeguRelacionSistema')->loadModulos();

        if($modulos && count($modulos)>0)
        {
            $num = count($modulos);
            
            $arr_encontrados[]=array('id_modulo' =>0, 'nombre_modulo' =>"Seleccion un modulo");
            foreach($modulos as $key => $modulo)
            {                
                $arr_encontrados[]=array('id_modulo' =>$modulo["id"],
                                         'nombre_modulo' =>trim($modulo["nombreModulo"]));
            }

            if($num == 0)
            {
                $resultado= array('total' => 1 ,
                                 'encontrados' => array('id_modulo' => 0 , 'nombre_modulo' => 'Ninguno','modulo_id' => 0 , 'modulo_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    * @Secure(roles="ROLE_21-13")
    */
    public function getListadoItemsMenuAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
    
        $peticion = $this->get('request');
        $id_modulo = $peticion->query->get('id_modulo');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        if($id_modulo!=0)
        {
            $items = $em->getRepository('schemaBundle:SeguRelacionSistema')->loadItemsMenu($id_modulo);
            if($items && count($items)>0)
            {
                $num = count($items);

                $arr_encontrados[]=array('id_item' =>0, 'nombre_item' =>"Seleccion un item menu");
                foreach($items as $key => $item)
                {                
                    $arr_encontrados[]=array('id_item' =>$item["id"],
                                            'nombre_item' =>trim($item["nombreItemMenu"]));
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
        }
        else
        {
            $objJson= '{"total":"0","encontrados":[]}';
        }
        
        $respuesta->setContent($objJson);
        return $respuesta;
    }
    
    /**
    * @Secure(roles="ROLE_21-14")
    */
    public function getListadoAccionesAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
    
        $peticion = $this->get('request');
        $id_modulo = $peticion->query->get('id_modulo');
        $id_itemmenu = $peticion->query->get('id_itemmenu');
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        
        if($id_itemmenu != 0 && $id_modulo!=0)
        {
            $acciones = $em->getRepository('schemaBundle:SeguRelacionSistema')->loadAcciones($id_modulo, $id_itemmenu);
            if($acciones && count($acciones)>0)
            {
                $num = count($acciones);

                $arr_encontrados[]=array('id_accion' =>0, 'nombre_accion' =>"Seleccion una accion");
                foreach($acciones as $key => $accion)
                {                
                    $arr_encontrados[]=array('id_accion' =>$accion["id"],
                                            'nombre_accion' =>trim($accion["nombreAccion"]));
                }

                if($num == 0)
                {
                    $resultado= array('total' => 1 ,
                                    'encontrados' => array('id_accion' => 0 , 'nombre_accion' => 'Ninguno','accion_id' => 0 , 'accion_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
    * @Secure(roles="ROLE_21-15")
    */
    public function getListadoMotivosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
    
        $peticion = $this->get('request');
        $id_modulo = ($peticion->query->get('id_modulo') >0 ? $peticion->query->get('id_modulo') : "");
        $id_itemmenu = ($peticion->query->get('id_itemmenu') >0 ? $peticion->query->get('id_itemmenu') : "");
        $id_accion = ($peticion->query->get('id_accion') >0 ? $peticion->query->get('id_accion') : "");
        
        $em = $this->getDoctrine()->getManager("telconet_seguridad");
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        
        if($id_accion != 0 && $id_accion != "" && $id_modulo!=0 && $id_modulo != "")
        {
            $relacionSistema = $em->getRepository('schemaBundle:SeguRelacionSistema')->searchOneRelacionSistema($id_modulo, $id_itemmenu, $id_accion);
            $motivos = $em_general->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($relacionSistema["id"]);
            if($motivos && count($motivos)>0)
            {
                $num = count($motivos);

                //$arr_encontrados[]=array('id_motivo' =>0, 'nombre_motivo' =>"Seleccion un motivo");
                foreach($motivos as $key => $motivo)
                {                
                    $arr_encontrados[]=array('id_motivo' =>$motivo->getId(),//["id"],
                                            'nombre_motivo' =>trim($motivo->getNombreMotivo()));//["nombreMotivo"]));
                }

                if($num == 0)
                {
                    $resultado= array('total' => 1 ,
                                    'encontrados' => array('id_motivo' => 0 , 'nombre_motivo' => 'Ninguno','accion_id' => 0 , 'accion_nombre' => 'Ninguno', 'estado' => 'Ninguno'));
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
}