<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;

use telconet\schemaBundle\Entity\AdmiTarea;
use telconet\schemaBundle\Form\AdmiTareaType;
use telconet\schemaBundle\Entity\AdmiTareaInterfaceModeloTr;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoVariable;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class AdmiTareaController extends Controller implements TokenAuthenticatedController
{ 
    /**
	* @Secure(roles="ROLE_53-1")
	*/
    public function indexAction(){
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("53", "1");

        $entities = $em->getRepository('schemaBundle:AdmiTarea')->findAll();

        return $this->render('administracionBundle:AdmiTarea:index.html.twig', array(
            'item' => $entityItemMenu,
            'entities' => $entities
        ));
    }
    
    /**
	* @Secure(roles="ROLE_53-6")
	*/
    public function showAction($id){
        $peticion = $this->get('request');
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_general = $this->getDoctrine()->getManager('telconet_general');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("53", "1");
 
        if (null == $tarea = $em->find('schemaBundle:AdmiTarea', $id)) {
            throw new NotFoundHttpException('No existe la Tarea que se quiere mostrar');
        }

        $nombreRol = "";
        if($tarea->getRolAutorizaId())
        {    
            $objRol = $em_general->getRepository('schemaBundle:AdmiRol')->findOneById($tarea->getRolAutorizaId());
            $nombreRol = $objRol ? $objRol->getDescripcionRol() : "";
        }
        
        return $this->render('administracionBundle:AdmiTarea:show.html.twig', array(
            'item' => $entityItemMenu,
            'tarea'   => $tarea,
            'idTarea'   => $id,
            'nombreRol'   => $nombreRol,
            'flag' =>$peticion->get('flag')
        ));
    }
    
    /**
	* @Secure(roles="ROLE_53-2")
	*/
    public function newAction(){
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("53", "1");
        $entity = new AdmiTarea();
        $form   = $this->createForm(new AdmiTareaType(array('arrayRoles'=>$arrayRoles)), $entity);

        return $this->render('administracionBundle:AdmiTarea:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
    }
    
    /**
	* @Secure(roles="ROLE_53-3")
	*/
    public function createAction(){
        $arrayRoles =  $this->retornaArrayRoles();
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
        
        $parametros = $peticion->request->get('telconet_schemabundle_admitareatype');
        $em = $this->get('doctrine')->getManager('telconet_soporte');
        $emIn = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("53", "1");
  
        $entity  = new AdmiTarea();
        $form    = $this->createForm(new AdmiTareaType(array('arrayRoles'=>$arrayRoles)), $entity);
        $form->bind($peticion);
        
        if ($form->isValid()) {
            $em->getConnection()->beginTransaction();	
            $emCom->getConnection()->beginTransaction();	
            try {   
			       
				$escogido_proceso_id = $peticion->get('escogido_proceso_id');	
				if($escogido_proceso_id != null && $escogido_proceso_id != "")
				{
			        $entityProceso = $em->find('schemaBundle:AdmiProceso', $escogido_proceso_id);	
					if($entityProceso != null)
					{
						$entity->setProcesoId($entityProceso);
					}
				}
				$escogido_tarea_ant_id = $peticion->get('escogido_tarea_ant_id');	
				if($escogido_tarea_ant_id != null && $escogido_tarea_ant_id != "")
				{
			        $entityTareaAnt = $em->find('schemaBundle:AdmiTarea', $escogido_tarea_ant_id);	
					if($entityTareaAnt != null)
					{
						$entity->setTareaAnteriorId($entityTareaAnt);
					}
				}
				$escogido_tarea_sig_id = $peticion->get('escogido_tarea_sig_id');	
				if($escogido_tarea_sig_id != null && $escogido_tarea_sig_id != "")
				{
			        $entityTareaSig = $em->find('schemaBundle:AdmiTarea', $escogido_tarea_sig_id);	
					if($entityTareaSig != null)
					{
						$entity->setTareaSiguienteId($entityTareaSig);
					}
				}
				
		        $entity->setEstado('Activo');
		        $entity->setFeCreacion(new \DateTime('now'));
		        $entity->setUsrCreacion($session->get('user'));
		        $entity->setFeUltMod(new \DateTime('now'));
		        $entity->setUsrUltMod($session->get('user'));
				
			
	            $json_tareaInterfaceModeloTramo = json_decode($parametros['tareasInterfacesModelosTramos']);
	            $array_tareaInterfaceModeloTramo = $json_tareaInterfaceModeloTramo->tareasInterfacesModelosTramos;
	        
	            $em->persist($entity);
	            
	            foreach($array_tareaInterfaceModeloTramo as $tarea)
	            {
	                $script=null;
	                $tareaInterfaceModeloTramo = new AdmiTareaInterfaceModeloTr;
	                
	                if($tarea->script)
	                {
	                    $script = ($tarea->script);
	                }
	                	                
	                
	                //Admi_tareaInterfaceModeloTramo
	                if($tarea->tipoElementoNombre)
	                {
	                    $opcion = ($tarea->tipoElementoNombre);
	                }
	                	               

	                if($opcion!="Tramo"){
	                    $interfaceModeloId=null;
	                    
	                    if($tarea->idCombo)
	                    {
	                        $idModeloElemento = ($tarea->idCombo);
	                    }
	                    if($tarea->interfaceModeloId)
	                    {
	                        $interfaceModeloId = ($tarea->interfaceModeloId);
	                    }
	                    
	                    $modeloElemento = $emIn->getRepository('schemaBundle:AdmiModeloElemento')->find($idModeloElemento);
	                    $tareaInterfaceModeloTramo->setModeloElementoId($modeloElemento->getId());
	                    
	                    if($interfaceModeloId!=null){
	                        $interfaceModelo = $emIn->getRepository('schemaBundle:AdmiInterfaceModelo')->find($interfaceModeloId);
	                        $tareaInterfaceModeloTramo->setInterfaceModeloId($interfaceModelo->getId());
	                    }
	                    
	                }
	                else if($opcion=="Tramo"){
	                    if($tarea->nombreCombo)
	                    {
	                        $nombreTramo = ($tarea->nombreCombo);
	                    }
	                    if($tarea->idCombo)
	                    {
	                        $tramoId = ($tarea->idCombo);
	                    }
	                    
	                    $tramo = $emIn->getRepository('schemaBundle:InfoTramo')->find($tramoId);
	                    $tareaInterfaceModeloTramo->setTramoId($tramo->getId());
	                    
	                }
	                
	                $tareaInterfaceModeloTramo->setTareaId($entity);
	                $tareaInterfaceModeloTramo->setEstado("Activo");
	                $tareaInterfaceModeloTramo->setUsrCreacion($session->get('user'));
	                $tareaInterfaceModeloTramo->setFeCreacion(new \DateTime('now'));
	                $tareaInterfaceModeloTramo->setUsrUltMod($session->get('user'));
	                $tareaInterfaceModeloTramo->setFeUltMod(new \DateTime('now'));
	                $em->persist($tareaInterfaceModeloTramo);
	                //Info_documento
	                
	                
	                if($tarea->script)
	                {
	                    $script = ($tarea->script);
	                }
	                $claseDocumento=$emCom->getRepository('schemaBundle:AdmiClaseDocumento')->findBy(array( "nombreClaseDocumento" =>"SCRIPT"));
	                $tipoDocumento=$emCom->getRepository('schemaBundle:AdmiTipoDocumento')->findBy(array( "extensionTipoDocumento" =>"TXT"));
	                $documento = new InfoDocumento();
	                $documento->setClaseDocumentoId($claseDocumento[0]);
	                $documento->setTipoDocumentoId($tipoDocumento[0]);
	                $documento->setTareaInterfaceModeloTraId($tareaInterfaceModeloTramo->getId());
	                $documento->setMensaje($script);
	                $documento->setEstado("Activo");
	                $documento->setUsrCreacion($session->get('user'));
	                $documento->setFeCreacion(new \DateTime('now'));
					$documento->setIpCreacion($peticion->getClientIp());
	                $emCom->persist($documento);
	                

	                
	                if($script!=null){
	                    $arreglo = str_split($script);
	                    $cadena = "";
	                    $cont = 0;
	                    for($i=0;$i<count($arreglo);$i++){
	                        if($arreglo[$i]=='('){
	                            for($j=($i+1);$j<count($arreglo);$j++){
	                                if($arreglo[$j]==')'){
	                                    $i=$j+1;
	                                    $cont++;
	                                    break;
	                                }
	                                else{
	                                    $cadena = $cadena."".$arreglo[$j];
	                                }
	                                
	                            }
	                            
	                            $docVariable = new InfoDocumentoVariable();
	                            $docVariable->setDocumentoId($documento);
	                            $docVariable->setNombreDocumentoVariable($cadena);
	                            $docVariable->setPosicionDocumentoVariable($cont);
	                            $docVariable->setEstado("Activo");
	                            $docVariable->setUsrCreacion($session->get('user'));
	                            $docVariable->setFeCreacion(new \DateTime('now'));
								$docVariable->setIpCreacion($peticion->getClientIp());
	                            $emCom->persist($docVariable);
//                            print("cadena:".$cadena."<br>");
//                            print("posicion:".$cont."<br>");
	                            
	                            $cadena="";
	                        }//cierre if
	                    }//cierre for
	                }//cierre if script
	                
	   
	            }
				
	            $em->flush();     
	            $em->getConnection()->commit();
	            
	            $emCom->flush();
	            $emCom->getConnection()->commit();
	             
				return $this->redirect($this->generateUrl('admitarea_show', array('id' => $entity->getId())));
					
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
                $em->getConnection()->close();
				
                $emCom->getConnection()->rollback(); 
                $emCom->getConnection()->close();
            }
        }
        else{
            print_r($form->getErrors());
            
        }
        
        return $this->render('administracionBundle:AdmiTarea:new.html.twig', array(
            'item' => $entityItemMenu,
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        
    }
    
    /**
	* @Secure(roles="ROLE_53-4")
	*/
    public function editAction($id){
        $arrayRoles =  $this->retornaArrayRoles();
        
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("53", "1");

        if (null == $tarea = $em->find('schemaBundle:AdmiTarea', $id)) {
            throw new NotFoundHttpException('No existe la Tarea que se quiere modificar');
        }

        $formulario =$this->createForm(new AdmiTareaType(array('arrayRoles'=>$arrayRoles)), $tarea);
//        $formulario->setData($tarea);

        return $this->render('administracionBundle:AdmiTarea:edit.html.twig', array(
            'item' => $entityItemMenu,
			'edit_form' => $formulario->createView(),
			'entity'   => $tarea));
    }
    
    /**
	* @Secure(roles="ROLE_53-5")
	*/
    public function updateAction($id){
        $arrayRoles =  $this->retornaArrayRoles();
        
        $peticion = $this->get('request');
        $session  = $peticion->getSession();
		
        $em = $this->getDoctrine()->getManager('telconet_soporte');
        $em = $this->get('doctrine')->getManager('telconet_soporte');
        $emIn = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("53", "1");		
        $entity = $em->getRepository('schemaBundle:AdmiTarea')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find AdmiTarea entity.');
        }
		
        $editForm   = $this->createForm(new AdmiTareaType(array('arrayRoles'=>$arrayRoles)), $entity);
        $parametros = $peticion->request->get('telconet_schemabundle_admitareatype');
        $editForm->bind($peticion);

        if ($editForm->isValid()) {	
            $em->getConnection()->beginTransaction();	
            $emCom->getConnection()->beginTransaction();	
            try {   
			       
				$escogido_proceso_id = $peticion->get('escogido_proceso_id');	
				if($escogido_proceso_id != null && $escogido_proceso_id != "")
				{					
			        $entityProceso = $em->find('schemaBundle:AdmiProceso', $escogido_proceso_id);	
					if($entityProceso != null)
					{
						$entity->setProcesoId($entityProceso);
					}
				}
				$escogido_tarea_ant_id = $peticion->get('escogido_tarea_ant_id');	
				if($escogido_tarea_ant_id != null && $escogido_tarea_ant_id != "")
				{
			        $entityTareaAnt = $em->find('schemaBundle:AdmiTarea', $escogido_tarea_ant_id);	
					if($entityTareaAnt != null)
					{
						$entity->setTareaAnteriorId($entityTareaAnt);
					}
				}
				$escogido_tarea_sig_id = $peticion->get('escogido_tarea_sig_id');	
				if($escogido_tarea_sig_id != null && $escogido_tarea_sig_id != "")
				{
			        $entityTareaSig = $em->find('schemaBundle:AdmiTarea', $escogido_tarea_sig_id);	
					if($entityTareaSig != null)
					{
						$entity->setTareaSiguienteId($entityTareaSig);
					}
				}
				
	            $entity->setFeUltMod(new \DateTime('now'));
	            $entity->setUsrUltMod($peticion->getSession()->get('user'));
	            $em->persist($entity);
	            
	            $json_tareaInterfaceModeloTramo = json_decode($parametros['tareasInterfacesModelosTramos']);
	            $array_tareaInterfaceModeloTramo = $json_tareaInterfaceModeloTramo->tareasInterfacesModelosTramos;
	            
	            foreach($array_tareaInterfaceModeloTramo as $tarea)
	            {
	                $script=null;
	                
	                if($tarea->id)
	                {
	                    $idTareaInterfaceModeloTramo = ($tarea->id);
	                }
	                
	                if($idTareaInterfaceModeloTramo==""){
	                    $tareaInterfaceModeloTramo = new AdmiTareaInterfaceModeloTr;
	                    $documento = new InfoDocumento();
	                    print("nuevo doc");
	                }
	                else{
	                    $tareaInterfaceModeloTramo = $em->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')->find($idTareaInterfaceModeloTramo);
	                    $documentoArreglo = $emCom->getRepository('schemaBundle:InfoDocumento')->findBy(array( "tareaInterfaceModeloTraId" =>$idTareaInterfaceModeloTramo));
	                    $documento = $documentoArreglo[0];
	                    print("obj documento");
	                }
	                
	                //Admi_tareaInterfaceModeloTramo
	                if($tarea->opcion)
	                {
	                    $opcion = ($tarea->opcion);
	                }

	                if($opcion=="Elemento"){
	                    $interfaceModeloId=null;
	                    
	                    if($tarea->comboId)
	                    {
	                        $idModeloElemento = ($tarea->comboId);
	                    }
	                    if($tarea->interfaceModeloId)
	                    {
	                        $interfaceModeloId = ($tarea->interfaceModeloId);
	                    }
	                    
	                    $modeloElemento = $emIn->getRepository('schemaBundle:AdmiModeloElemento')->find($idModeloElemento);
	                    $tareaInterfaceModeloTramo->setModeloElementoId($modeloElemento->getId());
	                    if($interfaceModeloId!=null){
	                        $interfaceModelo = $emIn->getRepository('schemaBundle:AdmiInterfaceModelo')->find($interfaceModeloId);
	                        $tareaInterfaceModeloTramo->setInterfaceModeloId($interfaceModelo->getId());
	                    }
	                    
	                }
	                else if($opcion=="Tramo"){
	                    if($tarea->nombreCombo)
	                    {
	                        $nombreTramo = ($tarea->nombreCombo);
	                    }
	                    if($tarea->comboId)
	                    {
	                        $tramoId = ($tarea->comboId);
	                    }
	                    
	                    $tramo = $emIn->getRepository('schemaBundle:InfoTramo')->find($tramoId);
	                    $tareaInterfaceModeloTramo->setTramoId($tramo->getId());
	                    
	                }
	                $tareaInterfaceModeloTramo->setTareaId($entity);
					//                $tareaInterfaceModeloTramo->setEstado("Activo");
					//                $tareaInterfaceModeloTramo->setUsrCreacion($session->get('user'));
					//                $tareaInterfaceModeloTramo->setFeCreacion(new \DateTime('now'));
	                $tareaInterfaceModeloTramo->setUsrUltMod($session->get('user'));
	                $tareaInterfaceModeloTramo->setFeUltMod(new \DateTime('now'));
	                $em->persist($tareaInterfaceModeloTramo);
	                //Info_documento
	                
	                
	                if($tarea->script)
	                {
	                    $script = ($tarea->script);
	                }
	                $claseDocumento=$emCom->getRepository('schemaBundle:AdmiClaseDocumento')->findBy(array( "nombreClaseDocumento" =>"SCRIPT"));
	                $tipoDocumento=$emCom->getRepository('schemaBundle:AdmiTipoDocumento')->findBy(array( "extensionTipoDocumento" =>"TXT"));
	                
	                $documento->setClaseDocumentoId($claseDocumento[0]);
	                $documento->setTipoDocumentoId($tipoDocumento[0]);
	                $documento->setTareaInterfaceModeloTraId($tareaInterfaceModeloTramo->getId());
	                $documento->setMensaje($script);
					//                $documento->setEstado("Activo");
	                $documento->setUsrCreacion($session->get('user'));
	                $documento->setFeCreacion(new \DateTime('now'));
					$documento->setIpCreacion($peticion->getClientIp());
	                $emCom->persist($documento);
	            }
	            
	            $em->flush();
	            $em->getConnection()->commit();
	            
	            $emCom->flush();
	            $emCom->getConnection()->commit();

	            return $this->redirect($this->generateUrl('admitarea_show', array('id' => $id)));				
            } catch (Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback(); 
                $em->getConnection()->close();
				
                $emCom->getConnection()->rollback(); 
                $emCom->getConnection()->close();
            }
        }
        else{
            print_r($editForm->getErrors());
            die();
            
        }

        return $this->render('administracionBundle:AdmiTarea:edit.html.twig',array(
            'item' => $entityItemMenu,
            'entity'      => $entity,
            'edit_form'   => $editForm->createView()
        ));
    }
    
    /**
	* @Secure(roles="ROLE_53-8")
	*/
    public function deleteAction($id){
//        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

//        $form->bind($request);
		//$user = $this->get('security.context')->getToken()->getUser();
//        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager('telconet_soporte');
            $entity = $em->getRepository('schemaBundle:AdmiTarea')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find AdmiTarea entity.');
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

        return $this->redirect($this->generateUrl('admitarea'));
    }

    /**
	* @Secure(roles="ROLE_53-9")
	*/
    public function deleteAjaxAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $em = $this->getDoctrine()->getManager("telconet_soporte");
        
        $array_valor = explode("|",$parametro);
        foreach($array_valor as $id):
            if (null == $entity = $em->find('schemaBundle:AdmiTarea', $id)) {
                $respuesta->setContent("No existe la entidad");
            }
            else{
				if(strtolower($entity->getEstado()) != "eliminado")
				{
					$entity->setEstado("Eliminado");
					$entity->setFeUltMod(new \DateTime('now'));
					$entity->setUsrUltMod($peticion->getSession()->get('user'));
					$em->persist($entity);
					$em->flush();
                }
				
                $respuesta->setContent("Se elimino la entidad");
            }
        endforeach;
        //        $respuesta->setContent($id);
        
        return $respuesta;
    }
    
    public function retornaArrayRoles(){
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

    /**
    * gridAction
    *
    * Esta funcion Llena el grid de consulta de las tareas en general
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 18-12-2015 Se realizan ajustes para presentar las tareas en base al tipo de caso seleccionado en la
    *                         creacion de los casos
    * 
    * @author Lizbeth Cruz <mlcruz@telconet.ec>
    * @version 1.2 07-06-2016 Se inicializa variable $tipoCaso
    *
    * @version 1.0
    *
    * @return array $respuesta
    *
    */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $em       = $this->getDoctrine()->getManager("telconet_soporte");
        
        $queryNombre = $peticion->query->get('query') ? $peticion->query->get('query') : "";
        $nombre      = ($queryNombre != '' ? $queryNombre : $peticion->query->get('nombre'));
        $estado      = $peticion->query->get('estado');
        $visible     = $peticion->query->get('visible')?$peticion->query->get('visible'):'Todos';
        $tipoCaso    = "";
                
        $session     = $peticion->getSession();                
        
        $parametros              = array();
        $parametros["idProceso"] = $peticion->query->get('idProceso') ? $peticion->query->get('idProceso') : "";
        
        //Se obtiene el id del caso en el escenario de que se gestione con uno y asi poder obtener la empresa 
        //del mismo y obtener las tareas relacionadas a esta
        //de lo contrario se obtiene
        //informacion de las taraes con la empresa en sesion
        $caso        = $peticion->query->get('caso')?$peticion->query->get('caso'):'';
        
        //Se obtiene el id detalle de la tarea individual para poder obtener la empresa de donde proviene
        //y poder mostrar las taraes de finalizacion ligadas a esta
        $detalle = $peticion->query->get('detalle')?$peticion->query->get('detalle'):'';
        $start   = $peticion->query->get('start');
        $limit   = $peticion->query->get('limit');
        
        $em_general = $this->get('doctrine')->getManager('telconet_general');
        
        $codEmpresa = $session->get('idEmpresa');                
        
        //Se verifica que si se quiere obtener las tareas para relacionar un caso
        //Se valide con la empresa de la cual proviene el mismo y así mostrar sus
        //tareas segun la empresa en el que fue creado
        if($caso != '')
        {
            $caso = $em->getRepository('schemaBundle:InfoCaso')->find($caso);
            if($caso)
            {
                $codEmpresa = $caso->getEmpresaCod();
                $tipoCaso   = $caso->getTipoCasoId()->getid();
            }
        }
        else if($detalle!='')
        {
            //Ser verifica que es una tarea Individual por tanto se verificara origen de
            //la empresa de proviene la misma
            $tarea = $em->getRepository('schemaBundle:AdmiTarea')->getEmpresaTarea($detalle);

            if(count($tarea)>0)
            {
                $codEmpresa = $tarea[0]['empresaCod'];
            }
        }

        $parametros["em_general"] = $em_general;
        $parametros["nombre"]     = $nombre;
        $parametros["estado"]     = $estado;
        $parametros["codEmpresa"] = $codEmpresa;
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;
        $parametros["visible"]    = $visible;
        $parametros["tipoCaso"]   = $tipoCaso;
               
        $objJson = $em->getRepository('schemaBundle:AdmiTarea')->generarJson($parametros);
                       
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /*combo estado llenado ajax*/
    public function estadosAction(){
        /*Modificacion a utilizacion de estados por modulos*/
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        //$em = $this->get('doctrine')->getManager('telconet');
        //$datos = $em->getRepository('schemaBundle:AdmiEstadoDat')->findEstadosXModulos($modulo_activo,"COM-PROSL");

        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Convertido','codigo'=> 'ACT','descripcion'=> 'Convertido');

        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;	
    }
    
    /**
	* @Secure(roles="ROLE_53-21")
	*/
    public function getJsonPorOpcionAction(){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $em = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $peticion = $this->get('request');
        
        $opcion = $peticion->query->get('opcion');
        
        $start = $peticion->query->get('start');
        $limit = 500;                
        
        $tipoElemento = $em->getRepository('schemaBundle:AdmiTipoElemento')->find($opcion);                
        
        if(is_object($tipoElemento) && ($tipoElemento->get!='Tramo')){
           
            $objJson = $this->getDoctrine()
                            ->getManager("telconet_infraestructura")
                            ->getRepository('schemaBundle:AdmiModeloElemento')
                            ->generarJsonModelosElementosParaTarea("","",$tipoElemento->getId(),"Activo",$start,$limit);
            $respuesta->setContent($objJson);
        }
        else if($opcion==1){
            $objJson = $this->getDoctrine()
                            ->getManager("telconet_infraestructura")
                            ->getRepository('schemaBundle:InfoTramo')
                            ->generarJsonTramosParaTareas();
            $respuesta->setContent($objJson);
        }
        
        return $respuesta;
    }
    
	/**
	* @Secure(roles="ROLE_53-22")
	*/
    public function getDatosTareaInterfaceModeloTramoAction($id){
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $emIn = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emCom = $this->get('doctrine')->getManager('telconet_comunicacion');
        
        $peticion = $this->get('request');
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $objJson = $this->getDoctrine()
                        ->getManager("telconet_soporte")
                        ->getRepository('schemaBundle:AdmiTareaInterfaceModeloTr')
                        ->generarJsonTareasInterfacesModelosTramosScripts($id,"Activo",$start,$limit,$emIn,$emCom);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
	
	/**
	* @Secure(roles="ROLE_53-251")
	*/
    public function getProcesosAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
        
        $session    = $peticion->getSession();
        $codEmpresa = $session->get('idEmpresa');
		
        $parametros = array();
		$parametros["idProcesoActual"]=$peticion->query->get('idProcesoActual') ? $peticion->query->get('idProcesoActual') : "";
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiProceso')
            ->generarJson(null, $nombre, "Activo", $start, $limit,$codEmpresa);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    } 
	
	/**
	* @Secure(roles="ROLE_53-19")
	*/
    public function getTareasAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
		
        $peticion = $this->get('request');
        $nombre = $peticion->get('query');
		
        $parametros = array();
		$parametros["idTareaActual"]=$peticion->query->get('idTareaActual') ? $peticion->query->get('idTareaActual') : "";
		
        $start = $peticion->get('start');
        $limit = $peticion->get('limit');
		
        $em_general = $this->get('doctrine')->getManager('telconet_general');
		
        $parametros["em_general"] = $em_general;
        $parametros["nombre"]     = $nombre;
        $parametros["estado"]     = 'Activo';
        $parametros["start"]      = $start;
        $parametros["limit"]      = $limit;

        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiTarea')
            ->generarJson($parametros);
                
        $respuesta->setContent($objJson);
        
        return $respuesta;
    } 

    protected function enviarMail(){
        //------- COMUNICACIONES --- NOTIFICACIONES
        $emComercial->getConnection()->beginTransaction();
        //equipo
        $equipo = array();
        $equipo['nombreElemento'] = $elemento->getNombreElemento();
        $equipo['nombreInterfaceElemento'] = $nombreInterfaceElemento;
        $equipo['nombreInterfaceElementoAnterior'] = "";
        $infoIpElemento = $emInfraestructura->getRepository('schemaBundle:InfoIpElemento')->findOneBy(array( "elementoId" =>$elementoId));
        $equipo['ipElemento'] = ($infoIpElemento)?$infoIpElemento->getIpElemento():null;

        //servicio->Obj, 
        $mensaje = $this->renderView('tecnicoBundle:InfoServicio:notificacion.html.twig', 
                                                                array('servicio' => $servicio, 'equipo'=>$equipo,'servicioHistorial'=>$servicioHistorial,'motivo'=> null));

        $asunto  ="Activacion de Puerto para ".$servicio->getPuntoId()->getLogin()." : ".$servicio->getPlanId()->getNombrePlan();

        $infoDocumento = new InfoDocumento();
        $infoDocumento->setMensaje($mensaje);
        $infoDocumento->setEstado('Activo');
        $infoDocumento->setNombreDocumento($asunto);
        $infoDocumento->setFeCreacion(new \DateTime('now'));
        $infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
        $infoDocumento->setIpCreacion($peticion->getClientIp());
        $emComunicacion->persist($infoDocumento);
        $emComunicacion->flush();

        $infoComunicacion = new InfoComunicacion();
        $infoComunicacion->setFeCreacion(new \DateTime('now'));
        $infoComunicacion->setEstado('Activo');
        $infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
        $infoComunicacion->setIpCreacion($peticion->getClientIp());
        $emComunicacion->persist($infoComunicacion);
        $emComunicacion->flush();

        $infoDocumentoComunicacion = new InfoDocumentoComunicacion();
        $infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
        $infoDocumentoComunicacion->setDocumentoId($infoDocumento);
        $infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
        $infoDocumentoComunicacion->setEstado('Activo');
        $infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
        $infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
        $emComunicacion->persist($infoDocumentoComunicacion);
        $emComunicacion->flush();

        //DESTINATARIOS.... 
        $formasContacto = $emComercial->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($servicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
        $to = array();
        $to[] = 'pnaula@trans-telco.com';
        $to[] = 'sactecnico@trans-telco.com';
        $cc = array();
        $cc[] = 'kjimenez@telconet.ec';
        $cc[] = 'vrodriguez@telconet.ec';
        $cc[] = 'fadum@telconet.ec';

        if($formasContacto){
                foreach($formasContacto as $formaContacto){
                        $to[] = $formaContacto['valor'];
                }
        }

        //ENVIO DE MAIL
        $message = \Swift_Message::newInstance()
                ->setSubject($asunto)
                ->setFrom('telcos@telconet.ec')
                ->setTo($to)
                ->setCc($cc)
                ->setBody($mensaje,'text/html')
        ;

        $this->get('mailer')->send($message);
    }

    
    /** 
     * Metodo encargado de obtener empresas parametrizadas
     * 
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     * 
     */
    public function getEmpresaIndisponibilidadAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objEm = $this->getDoctrine()->getManager("telconet_soporte");
 
        $objJson = $objEm->getRepository('schemaBundle:AdmiTarea')->getEmpresaIndisponibilidad();
                       
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }


    /** 
     * Metodo encargado de obtener los elementos olt
     * 
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     * 
     */
    public function getElementosPorTipoAction()
    {

        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objPeticion = $this->get('request');
        $objQueryNombre = $objPeticion->query->get('query') ? $objPeticion->query->get('query') : "";
        $strNombre      = ($objQueryNombre != '' ? $objQueryNombre : $objPeticion->query->get('nombre'));


        $arrayEmpresas =  $this->getDoctrine()
                            ->getManager("telconet_infraestructura")
                            ->getRepository('schemaBundle:AdmiParametroDet')
                            ->get("INDISPONIBILIDAD_TAREAS_EMPRESAS", 
                                    "SOPORTE", 
                                    "TAREAS", 
                                    "",
                                    "", 
                                    "", 
                                    "", 
                                    "",
                                    "",
                                    "",
                                    "",
                                    "",
                                    "");
        
        $strEmpresasIndisponibilidad = $arrayEmpresas[0]['valor1'].','.$arrayEmpresas[1]['valor1'];

        
        $arrayParametros    = array(
                                    'nombreElemento'        => $strNombre,
                                    'nombreMarcaElemento'   => '',
                                    'nombreModeloElemento'  => '',
                                    'tipoElemento'          => 'OLT',
                                    'empresa'               => null,
                                    'estado'                => 'Todos',
                                    'start'                 => 0,
                                    'limit'                 => 100,
                                    'strEmpresasIndisponibilidad' => $strEmpresasIndisponibilidad
                                );
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet_infraestructura")
            ->getRepository('schemaBundle:InfoServicioTecnico')
            ->generarJsonElementosPorTipo($arrayParametros);
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
        
    }
    
    /** 
    * Metodo encargado de obtener los elementos puerto
    *
    * @author Jose Daniel Giler <jdgiler@telconet.ec>
    * @version 1.0 15-11-2021 
    * 
    */
    public function getInterfacesPorElementoAction()
    {

        ini_set('max_execution_time', 400000);
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');
        
        $strNombreOlt = $objRequest->get('nombreOlt');

        if($strNombreOlt != null)
        {
        
            $objEm = $this->getDoctrine()->getManager("telconet_infraestructura");

            $intIdOlt = $objEm->getRepository('schemaBundle:AdmiTarea')->getIdOltPorNombre($strNombreOlt);

            $arrayParametros = array(
                        'intIdCliente'              => null,
                        'intIdInterfaceNot'         => null,
                        'intIdElemento'             => $intIdOlt,
                        'strEstado'                 => 'Todos',
                        'intStart'                  => 0,
                        'intLimit'                  => 500,
                        'strTipoInterface'          => null
                    );
            
            
            $objJson = $objEm->getRepository('schemaBundle:InfoServicioTecnico')
                ->generarJsonPuertosPorDslam($arrayParametros);

            $objRespuesta->setContent($objJson);
        
        }

        return $objRespuesta;
    }


    /** 
     * Metodo encargado de obtener los elementos caja
     * 
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     * 
     */
    public function getElementosContenedoresPorPuertoAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');

        $objRequest = $this->get('request');
        $strNombreOlt = $objRequest->query->get('nombreOlt');
        $strIdPuerto = $objRequest->query->get('idPuerto');
        
        if($strIdPuerto != null)
        {

            $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
            
            $intIdOlt = $emInfraestructura->getRepository('schemaBundle:AdmiTarea')->getIdOltPorNombre($strNombreOlt);

            $arrayElementos = $emInfraestructura->getRepository('schemaBundle:AdmiTarea')->getCajas($intIdOlt, $strIdPuerto);

            $objRespuesta->setContent($arrayElementos);

        }

        return $objRespuesta;
    }


    /** 
     * Metodo encargado de obtener los elementos splitter
     * 
     * @author Jose Daniel Giler <jdgiler@telconet.ec>
     * @version 1.0 15-11-2021 
     * 
     */
    public function getElementosConectorPorElementoContenedorAction()
    {
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $objRequest = $this->get('request');

        $strIdPuerto    = $objRequest->get('idPuerto');
        $strNombreOlt   = $objRequest->get('nombreOlt');
        $strIdCaja      = $objRequest->get('idCaja');

        if($strIdCaja != null)
        {

            $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
            
            $intIdOlt = $emInfraestructura->getRepository('schemaBundle:AdmiTarea')->getIdOltPorNombre($strNombreOlt);

            $arrayElementos = $emInfraestructura->getRepository('schemaBundle:AdmiTarea')->getSplitter($intIdOlt, $strIdPuerto, $strIdCaja);

            $objRespuesta->setContent($arrayElementos);
            
        }

        return $objRespuesta;
    }


    public function getTiempoAfectacionIndisponibilidadTareaAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objRequest = $this->get('request');
        $strIdDetalle = $objRequest->get("strIdDetalle");

        $objEm = $this->getDoctrine()->getManager("telconet_soporte");
 
        $objJson = $objEm->getRepository('schemaBundle:AdmiTarea')->getTiempoAfectacionIndisponibilidad($strIdDetalle);
                       
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }


    public function getTiempoAfectacionIndisponibilidadCasoAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objRequest = $this->get('request');
        $strIdDetalle = $objRequest->get("strIdDetalle");

        $objEm = $this->getDoctrine()->getManager("telconet_soporte");
 

        $objJson = $objEm->getRepository('schemaBundle:AdmiTarea')->getTiempoAfectacionIndisponibilidadCaso($strIdDetalle);
                       
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

    public function verificarRolTapAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion        = $this->getRequest();
        $objSession         = $objPeticion->getSession();
        
        $emComercial = $this->get('doctrine')->getManager('telconet');
 
        $objJson = $emComercial->getRepository('schemaBundle:AdmiTarea')->verificarRolTap($objSession->get('user'));
                       
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }


    public function getClientesAfectadosAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion        = $this->getRequest();
        $strNombreOlt = $objPeticion->get("nombreOlt");
        $strIdPuerto    = $objPeticion->get('idPuerto');
        $strIdCaja      = $objPeticion->get('idCaja');
        $strIdSplitter   = $objPeticion->get('idSplitter');
        
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');

        $objJson = $emInfraestructura->getRepository('schemaBundle:AdmiTarea');
        $objJson = $objJson->getClientesAfectados($strNombreOlt, $strIdPuerto, $strIdCaja, $strIdSplitter);
                       
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }


    public function getArbolHipotesisAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion        = $this->getRequest();
        $intCasoId   = $objPeticion->get('intCasoId');
        
        $objEm = $this->getDoctrine()->getManager("telconet_soporte");

        $objJson = $objEm->getRepository('schemaBundle:AdmiTarea');
        $objJson = $objJson->getArbolHipotesis($intCasoId);
                       
        $objRespuesta->setContent($objJson);
        
        return $objRespuesta;
    }

    
}
