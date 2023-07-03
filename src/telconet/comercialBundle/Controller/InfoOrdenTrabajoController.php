<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoPersona;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Form\InfoOrdenTrabajoType;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * InfoOrdenTrabajo controller.
 *
 */
class InfoOrdenTrabajoController extends Controller
{
    /**
     * Lists all InfoOrdenTrabajo entities.
     *
     */
    public function indexAction()
    {
		$request=$this->get('request');
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		
        $em = $this->getDoctrine()->getManager();

        //$entities = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->findAll();
        
        if($ptocliente)
			$presentar="S";
		else
			$presentar="N";
		
		$parametros=array(
            //'entities' => $entities,
            'orden_servicio'=>$presentar
        );
        
        if($ptocliente)
        {
			$parametros['punto_id']=$ptocliente;
			$parametros['cliente']=$cliente;
		}
		
        return $this->render('comercialBundle:infoordentrabajo:index.html.twig',$parametros);
    }

    /**
     * Finds and displays a InfoOrdenTrabajo entity.
     *
     */
    public function showAction($id)
    {
		$request=$this->get('request');
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
		$estado="Activo";
		
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->findOneById($id);

        //Presentacion del listado de servicios si los mismo estan enlazados
        //se debe presentar los diferentes a Anulado
        //funcion q devuelve dif al estado enviado
        $estado="Inactivo";
        $servicios=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoIdYEstado($id,$estado);
      
        //$servicios=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($id);
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
        }
		
		
		//$oficina=$em->getRepository('schemaBundle:InfoOficinaGrupo')->findNombrePorOficinaYEmnpresa($entity->getOficinaId(),$idEmpresa,$estado);
        $deleteForm = $this->createDeleteForm($id);

        $parametros=array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'nombre_oficina'=>"TELCONET - Gye",
            'ultimaMillaId'=>"Fibra Optica");
        
        if($servicios)
        {
            $i=0;
            foreach($servicios as $servicio)
            {
				$variable_plan="";
                if($servicio->getProductoId()!="")
                {
                    $info_plan_prod=$servicio->getProductoId()->getDescripcionProducto();
                    $arreglo[$i]['tienedetalle']="N";
				}
                else
                {
                    $info_plan_prod=$servicio->getPlanId()->getNombrePlan();
                    $variable_plan="S";
                }
                
                $arreglo[$i]['producto']=$info_plan_prod;
                $arreglo[$i]['cantidad']=$servicio->getCantidad();
                $arreglo[$i]['precio']=$servicio->getPrecioVenta();
                
                if($variable_plan=="S")
                {
					$detalle=$this->listarDetallePlan($servicio->getPlanId());
					if($detalle)
					{
						$arreglo[$i]['detalle']=$detalle;
						$arreglo[$i]['tienedetalle']="S";
					}
					else
						$arreglo[$i]['tienedetalle']="N";
				}
                $i++;
            }
            $parametros['servicios']=$arreglo;
        }
        
        return $this->render('comercialBundle:infoordentrabajo:show.html.twig', $parametros);
    }

	public function listarDetallePlan($idPlan)
	{
		$em = $this->getDoctrine()->getManager();
		$listado_productos=$em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($idPlan);
	
		$arreglo=array();
		$i=0;
		foreach($listado_productos as $prod)
		{
			$infoProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($prod->getProductoId());
			$arreglo[$i]['producto']=$infoProducto->getDescripcionProducto();
			$i++;
		}
		
		return $arreglo;
	}
	
    /**
     * Displays a form to create a new InfoOrdenTrabajo entity.
     *
     */
    public function newAction()
    {
		$request=$this->get('request');
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		
        $entity = new InfoOrdenTrabajo();
        $form   = $this->createForm(new InfoOrdenTrabajoType(), $entity);
        
        $parametros=array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
        
        if($ptocliente)
        {
			$parametros['punto_id']=$ptocliente;
			$parametros['cliente']=$cliente;
		}
        return $this->render('comercialBundle:infoordentrabajo:new.html.twig', $parametros);
    }

    /**
     * Creates a new InfoOrdenTrabajo entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoOrdenTrabajo();
        $form = $this->createForm(new InfoOrdenTrabajoType(), $entity);
		$formulario=$request->request->get('telconet_schemabundle_infoordentrabajotype');
        //print_r ($request->request->get('telconet_schemabundle_infoordentrabajotype'));
        //die();
        //$form->bind($request);
        
        $datos=$request->get('valores');
        $valores=json_decode($datos);	
        //print_r($formulario);	
        //die();
        
        /*
         * Verificar:
         * Si el pto esta en session
         * Sino tomarlo del formulario
         * */
        
		$session=$request->getSession();
		$prefijoEmpresa = $session->get('prefijoEmpresa');
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		$empresa=$session->get('idEmpresa');
		$oficina=$session->get('idOficina');
		$user=$session->get('user');
                $idEmpresa = $session->get('idEmpresa');
		
       // if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
			$em_comunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
            //Obtener la numeracion de la tabla Admi_numeracion
            //$empresa="10";
            //$oficina=2;
		$em->getConnection()->beginTransaction();
		$em_comunicacion->getConnection()->beginTransaction();
		
		try {	
            $estado="Pendiente";
            $datosNumeracion = $em->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($empresa,$oficina,"ORD");
            $secuencia_asig=str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
            $numero_de_contrato=$datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
            
            //preguntamos si el pto esta en session
            if($ptocliente)
			{
				$entidad_pto=$em->getRepository('schemaBundle:InfoPunto')->find($ptocliente['id']);
				$entity->setPuntoId($entidad_pto);
			}
			else
			{
				$formulario_puntoid=$request->request->get('puntoid');
				if($formulario)
				{
					$entidad_pto=$em->getRepository('schemaBundle:InfoPunto')->find($formulario_puntoid);
					$entity->setPuntoId($entidad_pto);
				}
			}
			$entity->setTipoOrden($formulario['tipoOrden']);
			$entity->setUltimaMillaId($formulario['ultimaMillaId']);			
            $entity->setNumeroOrdenTrabajo($numero_de_contrato);
            $entity->setFeCreacion(new \DateTime('now'));
            $entity->setUsrCreacion($user);
            $entity->setIpCreacion($request->getClientIp());
            $entity->setOficinaId($oficina);
            $entity->setEstado($estado);
            $em->persist($entity);
            $em->flush();
            
            if($entity)
            {
                //Actualizo la numeracion en la tabla
                $numero_act=($datosNumeracion->getSecuencia()+1);
                $datosNumeracion->setSecuencia($numero_act);
                $em->persist($datosNumeracion);
                $em->flush();
            }
            
            foreach($valores as $valor):
                $id=$valor->producto;
                $cantidad=$valor->cantidad;
                $precio=$valor->precio_total;
                $info=$valor->info;
                if($info=='C')
                {
                    $prod_caract=$valor->prod_caract;
                    $valor_caract=$valor->valor_caract;
                    $producto=$em->getRepository('schemaBundle:AdmiProducto')->findOneById($id);
                }

                
                $entityservicio  = new InfoServicio();	
                if($info=='C')
                    $entityservicio->setProductoId($producto);
                if($info=='P')
                {
                    $plan=$em->getRepository('schemaBundle:InfoPlanCab')->findOneById($id);
                    $entityservicio->setPlanId($plan);
                }
                $entityservicio->setPuntoId($entity->getPuntoId());
                $entityservicio->setOrdenTrabajoId($entity);
                $entityservicio->setEsVenta("S");
                $entityservicio->setPrecioVenta($precio);
                $entityservicio->setCantidad($cantidad);
				$entityservicio->setTipoOrden($formulario['tipoOrden']);				
                $entityservicio->setUsrCreacion($user);	
                $entityservicio->setIpCreacion($request->getClientIp());			
                $entityservicio->setFeCreacion(new \DateTime('now'));
                
				if($info=='P')
                {
					$entityservicio->setEstado("Pendiente");
					$em->persist($entityservicio);
					$em->flush();
				}
				if($info=='C')
				{
					if($producto->getEsEnlace()=='SI'){
						$entityservicio->setEstado("Pre-servicio");
						$em->persist($entityservicio);
						$em->flush();
					}else{
						if($producto->getRequierePlanificacion()=="SI"){
			
							  $entityservicio->setEstado("PrePlanificada");
							  $em->persist($entityservicio);
							  $em->flush();
							  
							  $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
			
							  $entitySolicitud  = new InfoDetalleSolicitud();
							  $entitySolicitud->setServicioId($entityservicio);
							  $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
							  $entitySolicitud->setEstado("PrePlanificada");	
							  $entitySolicitud->setUsrCreacion($session->get('user'));		
							  $entitySolicitud->setFeCreacion(new \DateTime('now'));

							  $em->persist($entitySolicitud);
							  $em->flush();  
							  
							  //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
							  $entityDetalleSolHist = new InfoDetalleSolHist();
							  $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
							  
							  $entityDetalleSolHist->setIpCreacion($request->getClientIp());
							  $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
							  $entityDetalleSolHist->setUsrCreacion($session->get('user'));
							  $entityDetalleSolHist->setEstado('PrePlanificada');  

							  $em->persist($entityDetalleSolHist);
							  $em->flush(); 
						}else if($producto->getRequiereInfoTecnica()=="SI"){
			
						
							  $entityservicio->setEstado("PreAsignacionInfoTecnica");
							  $em->persist($entityservicio);
							  $em->flush();
							  
							  $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD INFO TECNICA");
			
							  $entitySolicitud  = new InfoDetalleSolicitud();
							  $entitySolicitud->setServicioId($entityservicio);
							  $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
							  $entitySolicitud->setEstado("PreAsignacionInfoTecnica");	
							  $entitySolicitud->setUsrCreacion($session->get('user'));		
							  $entitySolicitud->setFeCreacion(new \DateTime('now'));

							  $em->persist($entitySolicitud);
							  $em->flush();  
							  
							  //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
							  $entityDetalleSolHist = new InfoDetalleSolHist();
							  $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
							  
							  $entityDetalleSolHist->setIpCreacion($request->getClientIp());
							  $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
							  $entityDetalleSolHist->setUsrCreacion($session->get('user'));
							  $entityDetalleSolHist->setEstado('PreAsignacionInfoTecnica');  

							  $em->persist($entityDetalleSolHist);
							  $em->flush(); 
							
						}else{
							$entityservicio->setEstado("Pendiente");
							$em->persist($entityservicio);
							$em->flush();
						}
					}
					
				}
                if($entityservicio)
				{
					$newInfoServicioTecnico  = new InfoServicioTecnico();
					$newInfoServicioTecnico->setUltimaMillaId($formulario['ultimaMillaId']);
					$newInfoServicioTecnico->setServicioId($entityservicio);
					$em->persist($newInfoServicioTecnico);
					$em->flush();
					
					$entityServicioHist = new InfoServicioHistorial();
					$entityServicioHist->setServicioId($entityservicio);
					$entityServicioHist->setObservacion('Se creo el servicio');
					$entityServicioHist->setIpCreacion($request->getClientIp());
					$entityServicioHist->setFeCreacion(new \DateTime('now'));
					$entityServicioHist->setUsrCreacion($user);
					$entityServicioHist->setEstado($entityservicio->getEstado());
					$em->persist($entityServicioHist);
					$em->flush();
				}

                if(isset($prod_caract))
                {
                    if(sizeof($prod_caract)>0)
                    {
                        for($i=0;$i<sizeof($prod_caract);$i++)
                        {
                            //print_r($prod_caract[$i]);
                            //Guardar informacion de la caracteristica del producto
                            $entityservproductocaract  = new InfoServicioProdCaract();	
                            $entityservproductocaract->setServicioId($entityservicio->getId());
                            $entityservproductocaract->setProductoCaracterisiticaId($prod_caract[$i]);
                            $entityservproductocaract->setValor($valor_caract[$i]);
                            $entityservproductocaract->setEstado("Activo");	
                            $entityservproductocaract->setUsrCreacion($user);	
                            $entityservproductocaract->setFeCreacion(new \DateTime('now'));	
                            $em->persist($entityservproductocaract);
                            $em->flush();
                        }
                    }
                }
            endforeach;
            
			//RONALD 
			$peticion = $this->get('request');
			if($entity){            
	            $entityServicios =$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($entity->getId());
	            $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
	                        
	            if($entityServicios && count($entityServicios)>0)
	            {
	                $boolGrabo = false;
	                
	                foreach($entityServicios as $key => $entityServicio)
	                {    
						$solicitudFactibilidad = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneBy(array("servicioId"=>$entityServicio->getId(), "estado"=>"Factible"));
						/////
						if($solicitudFactibilidad || $formulario['tipoOrden']=="R"){	
							$entityDetalleSolicitud =$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findCountDetalleSolicitudByIds($entityServicio->getId(), $entityTipoSolicitud->getId());                    
							if(!$entityDetalleSolicitud || $entityDetalleSolicitud["cont"]<=0)
							{
								$entitySolicitud  = new InfoDetalleSolicitud();
								$entitySolicitud->setServicioId($entityServicio);
								$entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
								$entitySolicitud->setEstado("PrePlanificada");	
								$entitySolicitud->setUsrCreacion($peticion->getSession()->get('user'));		
								$entitySolicitud->setFeCreacion(new \DateTime('now'));

								$em->persist($entitySolicitud);
								$em->flush();  
								
								//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
								$entityDetalleSolHist = new InfoDetalleSolHist();
								$entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
								
								$entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
								$entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
								$entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
								$entityDetalleSolHist->setEstado('PrePlanificada');  

								$em->persist($entityDetalleSolHist);
								$em->flush();  	
								
								//------- COMUNICACIONES --- NOTIFICACIONES 
								$mensaje = $this->renderView('planificacionBundle:Coordinar:notificacion.html.twig',
															array('detalleSolicitud' => $entitySolicitud,'detalleSolicitudHist' => null ,'motivo'=> null));
								
								$asunto  ="Solicitud de Instalacion #".$entitySolicitud->getId();
								
								$infoDocumento = new InfoDocumento();
								$infoDocumento->setMensaje($mensaje);
								$infoDocumento->setEstado('Activo');
								$infoDocumento->setNombreDocumento($asunto);
								$infoDocumento->setFeCreacion(new \DateTime('now'));
								$infoDocumento->setUsrCreacion($peticion->getSession()->get('user'));
								$infoDocumento->setIpCreacion($peticion->getClientIp());
								$em_comunicacion->persist($infoDocumento);
								$em_comunicacion->flush();

								$infoComunicacion = new InfoComunicacion();
								$infoComunicacion->setFeCreacion(new \DateTime('now'));
								$infoComunicacion->setEstado('Activo');
								$infoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
								$infoComunicacion->setIpCreacion($peticion->getClientIp());
								$em_comunicacion->persist($infoComunicacion);
								$em_comunicacion->flush();

								$infoDocumentoComunicacion = new InfoDocumentoComunicacion();
								$infoDocumentoComunicacion->setComunicacionId($infoComunicacion);
								$infoDocumentoComunicacion->setDocumentoId($infoDocumento);
								$infoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
								$infoDocumentoComunicacion->setEstado('Activo');
								$infoDocumentoComunicacion->setUsrCreacion($peticion->getSession()->get('user'));
								$infoDocumentoComunicacion->setIpCreacion($peticion->getClientIp());
								$em_comunicacion->persist($infoDocumentoComunicacion);
								$em_comunicacion->flush();
									
								//DESTINATARIOS.... 
								$formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
								$to = array();
								$cc = array();
								$cc[] = 'notificaciones_telcos@telconet.ec';
				
								if($prefijoEmpresa=="TTCO"){
									$to[] = 'rortega@trans-telco.com';

									$cc[] = 'sac@trans-telco.com';
								}
								else if($prefijoEmpresa=="MD"){
									$to[] = 'notificaciones_telcos@telconet.ec';
								}		
								
								//ENVIO DE MAIL
								$message = \Swift_Message::newInstance()
									->setSubject($asunto)
									->setFrom('notificaciones_telcos@telconet.ec')
									->setTo($to)
									->setCc($cc)
									->setBody($mensaje,'text/html')
								;
								
								$this->get('mailer')->send($message);		
									
								$boolGrabo = true;                    
							}
						}
	                }
	            }          
	        }
			
			$em->getConnection()->commit();
			$em_comunicacion->getConnection()->commit();
		}catch (\Exception $e) {
            $em->getConnection()->rollback();
			$em_comunicacion->getConnection()->rollback();
            
			$mensajeError = "Error: ".$e->getMessage();
			error_log($mensajeError);
			
			return $this->render('comercialBundle:infoordentrabajo:new.html.twig', array(
				'entity' => $entity,
				'form'   => $form->createView(),
			));
		}
            
		return $this->redirect($this->generateUrl('infoordentrabajo_show', array('id' => $entity->getId())));
     
    }

    /**
     * Displays a form to edit an existing InfoOrdenTrabajo entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
        }

		//estado para los detalles es Anulado
        $estado="Inactivo";
        $items=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoIdYEstado($id,$estado);
        
        if($items)
        {
            $i=0;
            foreach($items as $item)
            {
                $descripcion="";
                $id_info_plan_prod="";
                $info="";
                
                if($item->getProductoId()!="")
                {
                    $info_plan_prod=$em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId()->getId());
                    $descripcion=$info_plan_prod->getDescripcionProducto();
                    $id_info_plan_prod=$info_plan_prod->getId();
                    $info="C";
                }
                
                if($item->getPlanId()!="")
                {
                    $info_plan_prod=$em->getRepository('schemaBundle:InfoPlanCab')->find($item->getPlanId()->getId());
                    $descripcion=$info_plan_prod->getNombrePlan();
                    $id_info_plan_prod=$info_plan_prod->getId();
                    $info="P";
                }
                
                $info_servicio[$i]['producto']=$descripcion;
                $info_servicio[$i]['cantidad']=$item->getCantidad();
                $info_servicio[$i]['precio_total']=$item->getPrecioVenta();
                $info_servicio[$i]['producto_id']=$id_info_plan_prod;
                $i++;
                
                
                $info_detalle[] = array('producto' =>$id_info_plan_prod,
                                        'cantidad' => $item->getCantidad(),
                                        'precio_total' => $item->getPrecioVenta(),
                                        'id_det'=>$item->getId(),
                                        'info'=>$info);
                
            }
            
            //$obj_item = (object)$plandet;
        }
        
        if(isset($info_detalle))
            $arreglo_encode= json_encode($info_detalle);
        
        $editForm = $this->createForm(new InfoOrdenTrabajoType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
            
        $parametros=array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
        
        if(isset($arreglo_encode))
            $parametros['arreglo']=$arreglo_encode;
        
        if(isset($info_servicio))
            $parametros['items_detalle']=$info_servicio;
        
        return $this->render('comercialBundle:infoordentrabajo:edit.html.twig', $parametros);
    }

    /**
     * Edits an existing InfoOrdenTrabajo entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
		//informacion del pto cliente
		$session=$request->getSession();
		$user=$session->get('user');
		
        $datos=$request->get('valores');
        $valores=json_decode($datos);
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoOrdenTrabajoType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('infoplancab_edit', array('id' => $id)));
            $items=$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($id);

            //Verificacion de items existentes
            if($items)
            {
                $band=0;
                foreach($items as $item)
                {
                    foreach($valores as $valor)
                    {    
                        if($item->getId()==$valor->id_det && $valor->id_det!="")
                        {
                            $band=1;
                            break;
                        }
                        else
                            $band=2;
                    }
                    if($band==2)
                    {
                        $estado="Inactivo";
                        $item->setEstado($estado);
                        $em->persist($item);
                        $em->flush();
                    }
                }
            }


            foreach($valores as $valor):
                $id_producto=$valor->producto;
                $cantidad=$valor->cantidad;
                $precio=$valor->precio_total;
                $id_det=$valor->id_det;
                $info=$valor->info;
                if($id_det=="" && $info=="C")
                {
                    $prod_caract=$valor->prod_caract;
                    $valor_caract=$valor->valor_caract;
                }

                //echo $id_det;

                $entityservicio  = new InfoServicio();	
                
                if($id_det=="")
                {
                    if($info=="C")
                    {
                        $producto=$em->getRepository('schemaBundle:AdmiProducto')->findOneById($id_producto);
                        $entityservicio->setProductoId($producto);
                    }
                    else
                    {
                        $plan=$em->getRepository('schemaBundle:InfoPlanCab')->findOneById($id_producto);
                        $entityservicio->setPlanId($plan);
                    }               
                    $entityservicio->setPuntoId($entity->getPuntoId());
                    $entityservicio->setOrdenTrabajoId($entity);
                    $entityservicio->setEsVenta("S");
                    $entityservicio->setPrecioVenta($precio);
                    $entityservicio->setCantidad($cantidad);
                    $entityservicio->setEstado("Pendiente");	
                    $entityservicio->setUsrCreacion($user);	
                    $entityservicio->setIpCreacion($request->getClientIp());			
                    $entityservicio->setFeCreacion(new \DateTime('now'));	
                    $em->persist($entityservicio);
                    $em->flush();

                //print_r($prod_caract);

                    if($id_det=="" && $info=="C")
                    {
                        if(isset($prod_caract))
                        {
                            if(sizeof($prod_caract)>0)
                            {
                                for($i=0;$i<sizeof($prod_caract);$i++)
                                {
                                    //print_r($prod_caract[$i]);
                                    //Guardar informacion de la caracteristica del producto
                                    $entityservproductocaract  = new InfoServicioProdCaract();	
                                    $entityservproductocaract->setServicioId($entityservicio->getId());
                                    $entityservproductocaract->setProductoCaracterisiticaId($prod_caract[$i]);
                                    $entityservproductocaract->setValor($valor_caract[$i]);
                                    $entityservproductocaract->setEstado("Activo");	
                                    $entityservproductocaract->setUsrCreacion($user);	
                                    $entityservproductocaract->setFeCreacion(new \DateTime('now'));	
                                    $em->persist($entityservproductocaract);
                                    $em->flush();
                                }
                            }
                        }
                    }
                }
            endforeach;
            
            return $this->redirect($this->generateUrl('infoordentrabajo_edit', array('id' => $id)));
        }

        return $this->redirect($this->generateUrl('infoordentrabajo_edit', array('id' => $id)));
        /*
        return $this->render('comercialBundle:infoordentrabajo:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));*/
    }

    /**
     * Deletes a InfoOrdenTrabajo entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        $estado="Inactivo";
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoOrdenTrabajo entity.');
            }
            $entity->setEstado($estado);
            $em->persist($entity);
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infoordentrabajo'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /*
    *
    * @author Emmanuel Martillo Siavichay <emartillo@telconet.ec> 
    * @version 1.0 14-03-2023 - Se agrega bandera de Prefijo Empresa EN para Ecuanet, envio de notificacion Reubicacion.
    *
    */
    public function crearReubicacionAction()
    {
		
	//Actualizar en los servicios el numero
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        
	$peticion = $this->get('request');
        $servicios = $peticion->get('servicios');
        $ArrayServicios = explode("|",$servicios);
	$tipoOrden = 'R';
        
        $em = $this->getDoctrine()->getManager();
        
        
	$session=$peticion->getSession();
	$prefijoEmpresa = $session->get('prefijoEmpresa');
	$cliente=$session->get('cliente');
	$ptocliente=$session->get('ptoCliente');
	$empresa=$session->get('idEmpresa');
	$oficina=$session->get('idOficina');
	$user=$session->get('user');
	
	$em = $this->getDoctrine()->getManager();
	$em_comunicacion = $this->getDoctrine()->getManager('telconet_comunicacion');
	
	$em->getConnection()->beginTransaction();
	$em_comunicacion->getConnection()->beginTransaction();
	
	try {
		  //creacion de la OT
		  $datosNumeracion = $em->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($empresa,$oficina,"ORD");
		  $secuencia_asig=str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
		  $numero_de_contrato=$datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuencia_asig;
		  
		  $entidad_pto=$em->getRepository('schemaBundle:InfoPunto')->find($ptocliente['id']);
		  
		  //Debo obtener la ultima milla del un servicio para ponerle a la ot
		  $entity  = new InfoOrdenTrabajo();
		  $entity->setPuntoId($entidad_pto);
		  $entity->setTipoOrden('R');		
		  $entity->setNumeroOrdenTrabajo($numero_de_contrato);
		  $entity->setFeCreacion(new \DateTime('now'));
		  $entity->setUsrCreacion($user);
		  $entity->setIpCreacion($peticion->getClientIp());
		  $entity->setOficinaId($oficina);
		  $entity->setEstado("PrePlanificada");
		  $em->persist($entity);
		  $em->flush();
		  
		  if($entity)
		  {
			  //Actualizo la numeracion en la tabla
			  $numero_act=($datosNumeracion->getSecuencia()+1);
			  $datosNumeracion->setSecuencia($numero_act);
			  $em->persist($datosNumeracion);
			  $em->flush();
		  }
		  
		  foreach($ArrayServicios as $idServicio):
			  
			  $entityInfoServicio=$em->getRepository('schemaBundle:InfoServicio')->find($idServicio);
			  $entityInfoServicioTecnico=$em->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);
			  if($entityInfoServicio){
				  $entityInfoServicioCopy = new InfoServicio();
				  $entityInfoServicioCopy = clone $entityInfoServicio;
				  $entityInfoServicioCopy->setOrdenTrabajoId($entity);
				  $entityInfoServicioCopy->setTipoOrden('R');
				  $entityInfoServicioCopy->setIpCreacion($peticion->getClientIp());
				  $entityInfoServicioCopy->setFeCreacion(new \DateTime('now'));
				  $entityInfoServicioCopy->setUsrCreacion($peticion->getSession()->get('user'));
				  $entityInfoServicioCopy->setEstado('PrePlanificada');
				  $em->persist($entityInfoServicioCopy);
				  $em->flush(); 
				  
				  if($entityInfoServicioTecnico){
				      $entityInfoServicioTecnicoCopy = new InfoServicioTecnico();
				      $entityInfoServicioTecnicoCopy = clone $entityInfoServicioTecnico;
				      $entityInfoServicioTecnicoCopy->setServicioId($entityInfoServicioCopy);
				      $em->persist($entityInfoServicioTecnicoCopy);
				      $em->flush(); 
				  }
				  $entityServicioHist = new InfoServicioHistorial();
				  $entityServicioHist->setServicioId($entityInfoServicioCopy);
				  $entityServicioHist->setObservacion('Se creo el servicio por Reubicacion');
				  $entityServicioHist->setIpCreacion($peticion->getClientIp());
				  $entityServicioHist->setFeCreacion(new \DateTime('now'));
				  $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
				  $entityServicioHist->setEstado('PrePlanificada');
				  $em->persist($entityServicioHist);
				  $em->flush();
				  
				  $productoInternetDedicado = $em->getRepository('schemaBundle:AdmiProducto')->findOneBy(array( "descripcionProducto" => "INTERNET DEDICADO","empresaCod"=>$empresa, "estado"=>"Activo"));				
				  $entityReubicacion = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "REUBICACION", "estado"=>"Activo"));
				  $prodCaractReubicacion = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $productoInternetDedicado->getId(),"caracteristicaId" => $entityReubicacion->getId() , "estado"=>"Activo"));
				  
				  $infoServProdCaractReubicacion = new InfoServicioProdCaract();
				  $infoServProdCaractReubicacion->setServicioId($entityInfoServicioCopy->getId());
				  $infoServProdCaractReubicacion->setProductoCaracterisiticaId($prodCaractReubicacion->getId());
				  $infoServProdCaractReubicacion->setValor($entityInfoServicio->getId());
				  $infoServProdCaractReubicacion->setFeCreacion(new \DateTime('now'));
				  $infoServProdCaractReubicacion->setUsrCreacion($peticion->getSession()->get('user'));
				  $infoServProdCaractReubicacion->setEstado("Activo");
				  $em->persist($infoServProdCaractReubicacion);
				  $em->flush();
				  
				  //se crea la solicitud de planificacion
				  $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
				  
				  $entitySolicitud  = new InfoDetalleSolicitud();
				  $entitySolicitud->setServicioId($entityInfoServicioCopy);
				  $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
				  $entitySolicitud->setEstado("PrePlanificada");	
				  $entitySolicitud->setUsrCreacion($peticion->getSession()->get('user'));		
				  $entitySolicitud->setFeCreacion(new \DateTime('now'));
				  //$entitySolicitud->setIpCreacion($peticion->getClientIp());

				  $em->persist($entitySolicitud);
				  $em->flush();  
				  
				  //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
				  $entityDetalleSolHist = new InfoDetalleSolHist();
				  $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
				  
				  $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
				  $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
				  $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
				  $entityDetalleSolHist->setEstado('PrePlanificada');  

				  $em->persist($entityDetalleSolHist);
				  $em->flush();  						
				  
				  //------- COMUNICACIONES --- NOTIFICACIONES 
				  $mensaje = $this->renderView('planificacionBundle:Coordinar:notificacion.html.twig',
											  array('detalleSolicitud' => $entitySolicitud,'detalleSolicitudHist' => null ,'motivo'=> null));
				  
				  $asunto  ="Solicitud de Instalacion por Reubicacion #".$entitySolicitud->getId();
				  
				  //DESTINATARIOS.... 
				  $formasContacto = $em->getRepository('schemaBundle:InfoPersona')->getContactosByLoginPersonaAndFormaContacto($entityInfoServicioCopy->getPuntoId()->getUsrVendedor(),'Correo Electronico');
				  $to = array();
				  $cc = array();
				  $cc[] = 'notificaciones_telcos@telconet.ec';
				
					if($prefijoEmpresa=="TTCO"){
						$to[] = 'rortega@trans-telco.com';

						$cc[] = 'sac@trans-telco.com';
					}
					else if($prefijoEmpresa=="MD"  || $prefijoEmpresa=="EN")
                    {
						$to[] = 'notificaciones_telcos@telconet.ec';
					}	
				  
				  
				  if($formasContacto){
					  foreach($formasContacto as $formaContacto){
						  $to[] = $formaContacto['valor'];
					  }
				  }
				  
				  //ENVIO DE MAIL
				  $message = \Swift_Message::newInstance()
					  ->setSubject($asunto)
					  ->setFrom('notificaciones_telcos@telconet.ec')
					  ->setTo($to)
					  ->setCc($cc)
					  ->setBody($mensaje,'text/html')
				  ;
				  
// 				  $this->get('mailer')->send($message);
					  
						  
			  }
		  endforeach; 
		  
		  $respuesta->setContent("Se Creo la Orden De Trabajo por Reubicacion");
		  
		  $em->getConnection()->commit();
		  $em_comunicacion->getConnection()->commit();
	  }catch (\Exception $e) {
	      $em->getConnection()->rollback();
	      $em_comunicacion->getConnection()->rollback();
  
	      $mensajeError = "Error: ".$e->getMessage();
	      error_log($mensajeError);
	      $respuesta->setContent($mensajeError);
	  }	
	  
	return $respuesta;
			
    }
    public function listadoServiciosByEstadoAction()
    {
		
	$em = $this->get('doctrine')->getManager('telconet');
	$request = $this->getRequest();
	$session=$request->getSession();
	
	$estado = $request->get("estado");
	$ptocliente=$session->get('ptoCliente');
	$cliente=$session->get('cliente');
	
	//KJ
	//vaildacion liberada por pedido de vrodriguez
// 	$tieneServiciosInCorteCliente = $em->getRepository('schemaBundle:InfoServicio')->tieneServiciosInCorteCliente($cliente['id_persona_empresa_rol']);
	$tieneServiciosInCorteCliente = false;
	//////////////////////////////////////////////
	if($tieneServiciosInCorteCliente){
	    $arreglo[]= array(
		      'id'=> "",
		      'descripcion'=> "Cliente con Servicios In-Corte, imposible realizar una Reubicacion",
		      'cantidad'=> "",
		      'estado'=> "",
		      'precio'=> "",
	      );
	      //$response = new Response(json_encode($arreglo));
	      $response = new Response(json_encode(array('total' => $total, 'listado' => $arreglo)));
	}else{
	    $resultado=$em->getRepository('schemaBundle:InfoServicio')->findServiciosEnlacesByPuntoAndEstado($ptocliente['id'],$estado);
	    $datos = $resultado['registros'];
	    $total = $resultado['total'];
	    
	    if($datos)
	    {
		    foreach ($datos as $datos):
			    
			    $descripcion="";
			    
			    if($datos->getProductoId()!=null)
				    $descripcion=$datos->getProductoId()->getNombreProducto();
			    
			    if($datos->getPlanId()!=null)
				    $descripcion=$datos->getPlanId()->getNombrePlan();
				    
			    $arreglo[]= array(
				    'id'=>$datos->getId(),
				    'descripcion'=>$descripcion,
				    'cantidad'=>$datos->getCantidad(),
				    'estado'=>$datos->getEstado(),
				    'precio'=>$datos->getPrecioVenta(),
			    );
		    endforeach;
	    }
	    
	    if (!empty($arreglo))
		$response = new Response(json_encode(array('total' => $total, 'listado' => $arreglo)));
	    else
	    {
		    $arreglo[]= array(
			    'id'=> "",
			    'descripcion'=> "",
			    'cantidad'=> "",
			    'estado'=> "",
			    'precio'=> "",
		    );
		    //$response = new Response(json_encode($arreglo));
		    $response = new Response(json_encode(array('total' => $total, 'listado' => $arreglo)));
	    }
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    
    /**
     * tipoOrdenAction, Obtiene todos los productos y/o planes para la facturación
     * 
     * Actualizacion: Se recibe parametro modulo en el request y 
     * se envia dicho parametro a funcion $serviceInfoServicio->obtenerProductos, esto es para 
     * poder cargar los productos segun el modulo Comercial/Otros o Financiero.
     * Si no se envia modulo por parametro el default sera 'Comercial'
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.2 21-06-2016
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 24-05-2016 - Se envían todos los porcentajes que tienen los productos
     * @since 1.0
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 22-06-2016 - Se cambia el orden de retorno de los impuestos y se concatena el tipo de impuesto que es
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 28-06-2017 - Se envía a la función de impuestos el parámetro 'intIdPais' para consultar los impuestos por país.
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.5 06-11-2018 - Se agrega la empresa Telconet Panama para que se habilite la venta de Paquete y Productos
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.6 03-09-2020 - Se agrega lógica para listar los productos en base a la propuesta selecionada.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.7 18-09-2020 - Se agrega la validación de ocultar el producto FastCloud al agregar servicio
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.8 28-10-2020 - Se corrige validación de ocultar el producto FastCloud al agregar servicio para que no existan duplicados
     *                           en el listado de los productos
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 09-03-2021 - Se escoge el tipo de red MPLS o GPON, para cargar los productos que soporten la red seleccionada.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 19-07-2021 - Se valida tipo red por deafult MPLS
     */
    public function tipoOrdenAction()
    {
        $objRequest        = $this->getRequest();
        $strTipo           = $objRequest->request->get("tipo");
        $intIdPropuesta    = $objRequest->request->get("intIdPropuesta") ? $objRequest->request->get("intIdPropuesta"):"";
        $strTipoRed        = $objRequest->request->get("strTipoRed") ? $objRequest->request->get("strTipoRed") : "MPLS";
        $objSession        = $objRequest->getSession();
        $ptocliente        = $objSession->get('ptoCliente');
        $strCodEmpresa     = $objSession->get('idEmpresa');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        $intIdPaisSession  = $objSession->get('intIdPais');
        $strModulo         = 'Comercial';
        $serviceTelcoCrm   = $this->get('comercial.ComercialCrm');
        if ($objRequest->request->get("modulo"))
        {
            $strModulo = $objRequest->request->get("modulo");
        }

        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        
        if ( $strTipo == 'portafolio' )
        {
            if ( ($strPrefijoEmpresa=='MD' || $strPrefijoEmpresa=='EN') || $strPrefijoEmpresa=='TNP' )
            {
                $listado_planes = $serviceInfoServicio->obtenerPlanesAplicablesPunto($strCodEmpresa, $ptocliente['id']);

                if(!$listado_planes){
                        $arreglo=array('msg'=>'No existen datos');
                }else{
                        $formulario_portafolio="";
                        $formulario_portafolio.="<option>Seleccione</option>";
                        foreach($listado_planes as $plan){

                            $formulario_portafolio.="<option value='".$plan['idPlan']."-".$plan['nombrePlan']."'>".$plan['nombrePlan']."</option>";
                        }
                        $arreglo=array('msg'=>'ok','div'=>$formulario_portafolio,'info'=>'portafolio');
                }

                $response = new Response(json_encode($arreglo));
                $response->headers->set('Content-type', 'text/json');  
                return $response;
            }
        }
        else
        {
            $emComercial           = $this->getDoctrine()->getManager();
            $emGeneral             = $this->getDoctrine()->getManager("telconet_general");
            $arrayParametroListProd = array(
                'strCodEmpresa' => $strCodEmpresa,
                'strModulo'     => $strModulo,
                'strTipoRed'    => $strTipoRed
            );
            $arrayListadoProductos = $serviceInfoServicio->obtenerProductos($arrayParametroListProd);

            if ( empty($arrayListadoProductos) )
            {
                $arreglo=array('msg'=>'No existen datos');
            }
            else
            {
                $arrayEstadosPermitidos = array();
                $arrayParametrosEstados = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                                        'COMERCIAL',
                                                                                                        '',
                                                                                                        '',
                                                                                                        'ESTADOS_SERVICIOS_NO_PERMITIDOS_FLUJO',
                                                                                                        '',
                                                                                                        '',
                                                                                                        '',
                                                                                                        '');
                foreach($arrayParametrosEstados as $arrayDetalles)
                {
                    $arrayEstadosPermitidos[] = $arrayDetalles['valor2'];
                }

                if(!empty($intIdPropuesta) && !empty($strPrefijoEmpresa) && $strPrefijoEmpresa == 'TN')
                {
                    $objInfoPunto = $emComercial->getRepository('schemaBundle:InfoPunto')
                                                ->find($ptocliente['id']);
                    if(!empty($objInfoPunto) && is_object($objInfoPunto))
                    {
                        $objPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                  ->find($objInfoPunto->getPersonaEmpresaRolId()
                                                                      ->getPersonaId()
                                                                      ->getId());
                        if(!empty($objPersona) && is_object($objPersona))
                        {
                            $arrayParametros      = array("strRuc"             => $objPersona->getIdentificacionCliente(),
                                                          "strPrefijoEmpresa"  => $strPrefijoEmpresa,
                                                          "strCodEmpresa"      => $strCodEmpresa,
                                                          "intIdPropuesta"     => $intIdPropuesta,
                                                          "strBandera"         => "DETALLE");
                            $arrayParametrosWSCrm = array("arrayParametrosCRM" => $arrayParametros,
                                                          "strOp"              => 'getPropuesta',
                                                          "strFuncion"         => 'procesar');
                            $arrayRespuestaWSCrm  = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                        }
                    }
                }
                $strFormularioCatalogo = "";
                $strFormularioCatalogo .= "<option>Seleccione</option>";
                
                foreach($arrayListadoProductos as $objProducto)
                {
                    $strPorcentaje = '';
                    
                    $arrayParametrosImpuestosPrioridad  = array( 'intIdProducto' => $objProducto->getId(),
                                                                 'strEstado'     => 'Activo',
                                                                 'intIdPais'     => $intIdPaisSession,
                                                                 'intPrioridad'  => 1 );
                    $arrayImpuestosPrioridad1           = $emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                      ->getInfoImpuestoByCriterios( $arrayParametrosImpuestosPrioridad );
                    $objInfoProductoImpuestosPrioridad1 = $arrayImpuestosPrioridad1['registros'];
                    
                    if($objInfoProductoImpuestosPrioridad1)
                    {
                        foreach($objInfoProductoImpuestosPrioridad1 as $objInfoProductoImp)
                        {
                            $entityAdmiImpuesto = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                            ->find($objInfoProductoImp->getImpuestoId()->getId());
                            
                            if($entityAdmiImpuesto)
                            {
                                $strPorcentaje .= $entityAdmiImpuesto->getTipoImpuesto().':'.$entityAdmiImpuesto->getPorcentajeImpuesto().'-';
                            }
                        }
                    }
                    
                    
                    $arrayParametrosImpuestosPrioridad['intPrioridad'] = 2;
                    $arrayImpuestosPrioridad2                          = $emComercial->getRepository('schemaBundle:InfoProductoImpuesto')
                                                                                  ->getInfoImpuestoByCriterios( $arrayParametrosImpuestosPrioridad );
                    $objInfoProductoImpuestosPrioridad2                = $arrayImpuestosPrioridad2['registros'];
                    
                    if($objInfoProductoImpuestosPrioridad2)
                    {
                        foreach($objInfoProductoImpuestosPrioridad2 as $objInfoProductoImp)
                        {
                            $entityAdmiImpuesto = $emGeneral->getRepository('schemaBundle:AdmiImpuesto')
                                                            ->find($objInfoProductoImp->getImpuestoId()->getId());
                            
                            if($entityAdmiImpuesto)
                            {
                                $strPorcentaje .= $entityAdmiImpuesto->getTipoImpuesto().':'.$entityAdmiImpuesto->getPorcentajeImpuesto().'-';
                            }
                        }
                    }

                    //seteo el parametro de agregar el producto
                    $strAgregarProducto = 'SI';
                    //se obtiene el parametro si se agrega el producto
                    $arrayParametroAgregarProducto = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('CONFIG_PRODUCTO_DIRECT_LINK_MPLS',
                                                                 'TECNICO',
                                                                 '',
                                                                 '',
                                                                 $objProducto->getId(),
                                                                 'VISIBLE_PRODUCTO_AGREGAR_SERVICIO',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $strCodEmpresa);
                    if( isset($arrayParametroAgregarProducto) && !empty($arrayParametroAgregarProducto) )
                    {
                        $strAgregarProducto = $arrayParametroAgregarProducto['valor3'];
                    }
                    //se valida el producto requerido para el nuevo producto
                    $arrayParProductoRequerido = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                 'COMERCIAL',
                                                                 '',
                                                                 '',
                                                                 $objProducto->getId(),
                                                                 'PRODUCTO_REQUERIDO',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $strCodEmpresa);
                    if( isset($arrayParProductoRequerido) && isset($arrayParProductoRequerido['valor3'])
                        && !empty($arrayParProductoRequerido['valor3']) && !empty($arrayEstadosPermitidos) )
                    {
                        $arrayServiciosRequerido = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                                ->createQueryBuilder('s')
                                                                ->where("s.estado NOT IN (:estadoNot)")
                                                                ->andWhere("s.puntoId = :puntoId")
                                                                ->andWhere("s.productoId = :productoId")
                                                                ->setParameter('puntoId', $ptocliente['id'])
                                                                ->setParameter('productoId', $arrayParProductoRequerido['valor3'])
                                                                ->setParameter('estadoNot', array_values($arrayEstadosPermitidos))
                                                                ->getQuery()
                                                                ->getResult();
                        if(!isset($arrayServiciosRequerido) || empty($arrayServiciosRequerido)
                           || !is_array($arrayServiciosRequerido) || count($arrayServiciosRequerido) == 0)
                        {
                            $strAgregarProducto = "NO";
                        }
                    }
                    //se valida cantidad permitida del servicio por punto
                    $arrayParProductoPermitido = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                 'COMERCIAL',
                                                                 '',
                                                                 '',
                                                                 $objProducto->getId(),
                                                                 'PRODUCTOS_PERMITIDOS',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $strCodEmpresa);
                    if( isset($arrayParProductoPermitido) && isset($arrayParProductoPermitido['valor3'])
                        && !empty($arrayParProductoPermitido['valor3']) && !empty($arrayEstadosPermitidos) )
                    {
                        $arrayServiciosElemento = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                                ->createQueryBuilder('s')
                                                                ->where("s.estado NOT IN (:estadoNot)")
                                                                ->andWhere("s.puntoId = :puntoId")
                                                                ->andWhere("s.productoId = :productoId")
                                                                ->setParameter('puntoId', $ptocliente['id'])
                                                                ->setParameter('productoId', $objProducto->getId())
                                                                ->setParameter('estadoNot', array_values($arrayEstadosPermitidos))
                                                                ->getQuery()
                                                                ->getResult();
                        if(is_array($arrayServiciosElemento) && count($arrayServiciosElemento) >= $arrayParProductoPermitido['valor3'])
                        {
                            $strAgregarProducto = "NO";
                        }
                    }

                    if(isset($arrayRespuestaWSCrm["resultado"]) && !empty($arrayRespuestaWSCrm["resultado"]) && $strAgregarProducto == 'SI')
                    {
                        foreach($arrayRespuestaWSCrm["resultado"] as $arrayItem)
                        {
                            if(strtoupper($arrayItem->Nombre_producto) == strtoupper($objProducto->getDescripcionProducto()))
                            {
                                $strFormularioCatalogo .= "<option value='".$objProducto->getId()."-".$objProducto->getDescripcionProducto()."-".
                                $strPorcentaje."'>".$objProducto->getDescripcionProducto()."</option>";
                            }
                        }
                    }
                    elseif( $strAgregarProducto == 'SI' )
                    {
                        $strFormularioCatalogo .= "<option value='".$objProducto->getId()."-".$objProducto->getDescripcionProducto()."-".
                        $strPorcentaje."'>".$objProducto->getDescripcionProducto()."</option>";
                    }
                }

                $arreglo=array('msg'=>'ok','div'=>$strFormularioCatalogo,'info'=>'catalogo');
            }

            $response = new Response(json_encode($arreglo));
            $response->headers->set('Content-type', 'text/json');  
            return $response;
        }
    }
    
    /**
     * Función utilizada para obtener la información de los planes.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 03-04-2017 Se agregan las respectivas validaciones adicionales para los planes de acuerdo a sus características.
     *                         Flujo agregado para servicios con planes de Netlifecam
     * @since 1.0
     *
     * @param Request $objRequest
     * 
     * @return JsonResponse $objResponse
     */
    public function informacionPlanAction()
    {
        $objRequest             = $this->getRequest();
        $objResponse            = new JsonResponse();
        $intIdPlan              = $objRequest->get('plan');
        $intIdPunto             = $objRequest->get('idPunto');
        $objSession             = $objRequest->getSession();
        $strEmpresaCod          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        
        /* @var $serviceInfoServicio \telconet\comercialBundle\Service\InfoServicioService */
        $serviceInfoServicio    = $this->get('comercial.InfoServicio');
		$arrayInfoPlan          = $serviceInfoServicio->obtenerPlanInformacionDetalles($intIdPlan, true, false);
        

        $strMsg = "";

        $arrayValidacionesPlan      = array();
		if (!empty($arrayInfoPlan))
		{
            $strMsg = "ok";
            
            if($strPrefijoEmpresa=="MD" || $strPrefijoEmpresa=="EN")
            {
                $arrayParamsValidaciones    = array("strTipoServicio"   => "PAQUETE",
                                                    "strEmpresaCod"     => $strEmpresaCod,
                                                    "strPrefijoEmpresa" => $strPrefijoEmpresa,
                                                    "intIdPtoCliente"   => $intIdPunto,
                                                    "intIdPlanProd"     => $intIdPlan
                                              );

                $arrayValidacionesPlan      = $serviceInfoServicio->validarServiciosByCaracteristicasPlanProd($arrayParamsValidaciones);
            }
		}
		else
		{
            $strMsg = "No existen datos";
		}

        $arrayRespuestaPlan = array("msg" => $strMsg) + $arrayInfoPlan + $arrayValidacionesPlan;
        
        $objResponse->setData($arrayRespuestaPlan);	
        return $objResponse;
    }
    
    public function gridAction()
    {
        $objRespuesta   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json'); 
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $intIdEmpresa          = $objSession->get('idEmpresa');
        $intIdOficina          = $objSession->get('idOficina');
        $strEstado             = 'Activo';
        $em                    = $this->get('doctrine')->getManager('telconet');
        $objOficinaOrden       = $em->getRepository('schemaBundle:InfoOficinaGrupo')->findNombrePorOficinaYEmnpresa($intIdOficina,$intIdEmpresa,$strEstado);
        $arrayFechaDesde       = explode('T',$objRequest->get("fechaDesde")) ? explode('T',$objRequest->get("fechaDesde")):'';
        $arrayFechaHasta       = explode('T',$objRequest->get("fechaHasta")) ? explode('T',$objRequest->get("fechaHasta")):'';
        $strEstado             = $objRequest->get("estado") ? $objRequest->get("estado"): "Pendiente";
        $intLimit              = $objRequest->get("limit");
        $intStart              = $objRequest->get("start");
        $strTipoPersonal       = 'Otros';
        $strUsrCreacion        = $objSession->get('user');
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');

        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $em->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        $arrayParametros                          = array();
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;
        $arrayParametros['intIdOficina']          = $intIdOficina;
        $arrayParametros['intIdEmpresa']          = $intIdEmpresa;
        $arrayParametros['strEstado']             = $strEstado;
        $arrayParametros['strFechaInicio']        = $arrayFechaDesde[0];
        $arrayParametros['strFechaFin']           = $arrayFechaHasta[0];
        $arrayParametros['intLimit']              = $intLimit;
        $arrayParametros['intStart']              = $intStart;

        $arrayResultado = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->getOrdenes($arrayParametros);
        $arrayDatos     = $arrayResultado['registros'];
        $intTotal       = $arrayResultado['total'];
        $i=1;
        foreach ($arrayDatos as $datos):
            if($i % 2==0)
                    $clase='k-alt';
            else
                    $clase='';

            $urlPlanificacion = $this->generateUrl('infoordentrabajo_planificacion_ajax', array('id' => $datos->getId()));
            $urlVer = $this->generateUrl('infoordentrabajo_show', array('id' => $datos->getId()));
            $urlEditar = $this->generateUrl('infoordentrabajo_edit', array('id' => $datos->getId()));
            $urlEliminar = $this->generateUrl('infoordentrabajo_delete_ajax', array('id' => $datos->getId()));
            $linkPlanificacion = $urlPlanificacion;
            $linkVer = $urlVer;
            $linkEditar = $urlEditar;
            $linkEliminar=$urlEliminar;

            //Obtener el tipo de orden
            if($datos->getTipoOrden()=="N")
                $tipo_orden="Nueva";
            elseif($datos->getTipoOrden()=="T")
                $tipo_orden="Traslado";
            elseif($datos->getTipoOrden()=="R")
                $tipo_orden="Reubicacion";
            else    
		$tipo_orden="Nueva";
            
                    
            $arrayRespuesta[]= array(
				'idOrden'=>$datos->getId(),
                'Numeroorden'=>$datos->getNumeroOrdenTrabajo(),
                'Tipoorden'=> $tipo_orden,
                'Punto'=> $datos->getPuntoId()->getDescripcionPunto(),
                'Oficina'=> $objOficinaOrden->getNombreOficina(),
                'estado'=> $datos->getEstado(),
                'Fecreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
                'linkPlanificacion'=> $linkPlanificacion,
                'linkVer'=> $linkVer,
                'linkEditar'=> $linkEditar,
                'linkEliminar'=> $linkEliminar,
                'clase'=>$clase,
                'boton'=>""
            );              

            $i++;     
        endforeach;
        
        if (!empty($arrayRespuesta))
        {
            $objResponse = new Response(json_encode(array('total' => $intTotal, 'tickets' => $arrayRespuesta)));
        }
        else
        {
            $objResponse = new Response(json_encode(array('total' =>0, 'tickets' => $arrayRespuesta)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    public function estadosAction()
    {
        /*Modificacion a utilizacion de estados por modulos*/
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        //$em = $this->get('doctrine')->getManager('telconet');
        //$datos = $em->getRepository('schemaBundle:AdmiEstadoDat')->findEstadosXModulos($modulo_activo,"COM-PROSL");

        $arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
        $arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
        $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'ACT','descripcion'=> 'Pendiente');
        $arreglo[]= array('idEstado'=>'PrePlanificada','codigo'=> 'PRE','descripcion'=> 'PrePlanificada');

        $response = new Response(json_encode(array('estados'=>$arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
		
    }
    
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            //echo $id;
            $entity=$em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
            if($entity){
                $entity->setEstado("Inactivo");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        return $respuesta;
    }
    
    public function planificacionAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $id = $peticion->get('id');
        
        $em = $this->getDoctrine()->getManager();

        $entity=$em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
        if($entity){            
            $entityServicios =$em->getRepository('schemaBundle:InfoServicio')->findByOrdenTrabajoId($id);
            $entityTipoSolicitud =$em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
                        
            if($entityServicios && count($entityServicios)>0)
            {
                $boolGrabo = false;
                
                foreach($entityServicios as $key => $entityServicio)
                {       
                    $entityDetalleSolicitud =$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findCountDetalleSolicitudByIds($entityServicio->getId(), $entityTipoSolicitud->getId());                    
                    if(!$entityDetalleSolicitud || $entityDetalleSolicitud["cont"]<=0)
                    {
                        $entitySolicitud  = new InfoDetalleSolicitud();
                        $entitySolicitud->setServicioId($entityServicio);
                        $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
                        $entitySolicitud->setEstado("PrePlanificada");	
                        $entitySolicitud->setUsrCreacion($peticion->getSession()->get('user'));		
                        $entitySolicitud->setFeCreacion(new \DateTime('now'));

                        $em->persist($entitySolicitud);
                        $em->flush();  
						
			            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
			            $entityDetalleSolHist = new InfoDetalleSolHist();
			            $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
			            
			            $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
			            $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
			            $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
			            $entityDetalleSolHist->setEstado('PrePlanificada');  

			            $em->persist($entityDetalleSolHist);
			            $em->flush();  						
	                        
                        $boolGrabo = true;                    
                    }
                }
                
                if(!$boolGrabo)
                {
                    $respuesta->setContent("Estos datos ya fueron ingresados."); 
                }
                else
                {
                    $respuesta->setContent("Se ingreso los detalles de solicitud");  
                }
            }
            else
            {
                $respuesta->setContent("No se ingreso los detalles de solicitud");
            }            
        }
        else
            $respuesta->setContent("No existe el registro");
        
        return $respuesta;
    }
    
    public function ptoClientesAjaxAction()
	{
		$em = $this->get('doctrine')->getManager('telconet');
		$request = $this->getRequest();
		//informacion del pto cliente
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
		
		//$idEmpresa="10";
		$limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        $nombre="";
		$resultado=$em->getRepository('schemaBundle:InfoPunto')->findPtosPorEmpresaParaOrden($idEmpresa,$nombre,$limit,$page,$start);
		$datos = $resultado['registros'];
		$total = $resultado['total'];
		foreach ($datos as $datos):
			$arreglo[]= array(
                'id'=>$datos['id'],
                'login'=>$datos['login'],
                'descripcionPunto'=>$datos['descripcionPunto'],
                'razonSocial'=>$datos['razonSocial'],
                'nombres'=>$datos['nombres'],
                'apellidos'=> $datos['apellidos'],
            );              
		endforeach;
		
		if (!empty($arreglo))
                //$response = new Response(json_encode($arreglo));
                $response = new Response(json_encode(array('total' => $total, 'listado_ptos' => $arreglo)));
        else
        {
                $arreglo[]= array(
                        'id'=> "",
                        'login'=> "",
                        'descripcionPunto'=> "",
                        'razonSocial'=> "",
                        'nombres'=> "",
                        'apellidos'=> "",
                );
                //$response = new Response(json_encode($arreglo));
                $response = new Response(json_encode(array('total' => $total, 'listado_ptos' => $arreglo)));
        }		
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}

    /**
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.1
     * @since 05-03-2020
     * presentarListadoConvertirAction, Se envía la bandera strMuestraGridOT. "S" => 
     * Presenta el grid para continuar el flujo; "N" => Muestra un mensaje de bloqueo.
     *
     */
    public function presentarListadoConvertirAction()
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $objCliente              = $objSession->get('cliente');
        $objPtocliente           = $objSession->get('ptoCliente');
        $strCodEmpresa           = $objSession->get('idEmpresa');
        $strUser                 = $objSession->get('user');
        $strIp                   = $objRequest->getClientIp();
        $strOpcion               = 'WEB';
        $booleanProcesaNoPagados = $this->get('security.context')->isGranted('ROLE_151-6297');
        $arrayParametros         = array('arrayCliente'            => $objCliente,
                                         'arrayPtoCliente'         => $objPtocliente,
                                         'strIp'                   => $strIp,
                                         'strUser'                 => $strUser,
                                         'strCodEmpresa'           => $strCodEmpresa,
                                         'booleanProcesaNoPagados' => $booleanProcesaNoPagados,
                                         'strOpcion'               => $strOpcion);

        $serviceConvertirOT      = $this->get('comercial.ConvertirOrdenTrabajo');
        $arrayParametrosTwing    = $serviceConvertirOT->validacionesPreviasConvertirOT($arrayParametros); 
        return $this->render('comercialBundle:infoordentrabajo:convertirOrdenTrabajo.html.twig',$arrayParametrosTwing);
                       
    }

    /**
     * convertirOrdenTrabajoAction, se encarga de convertir las ordenes de trabajo.
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 12-07-2015 Se incluyó la finalización de la solicitud de wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 15-09-2016 se estableció que se debe comparar por nombre tecnico del producto wifi
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 17-02-2017 - Se agrega validación para productos de venta externa que cree la solicitud de planificación.
     *                           Se cambia el mensaje de éxito al generar la orden de trabajo
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 07-09-2017 - Se agrega nuevo estado de solicitud de factibilidad y de servicio para proceder a asignar
     *                           factibilidad real de servicios UM RADIO
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 08-11-2017 - Se agrega validacion para productos pertenecientes al grupo CLOUD IAAS, en el cual se debe realizar un flujo
     *                           mas corto:
     *                             - INTERNET DC - Pasa a estado AsigandoTarea
     *                             - POOL RECURSOS - Pasa a estado Asignada
     *                           Adicional se envia notificacion y se genera Tarea automatica al area involucrada
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.6 09-03-2018 - Se ajusta proceso para que soporte para el caso de Soluciones que se tenga la combinacion ( NxN ) soluciones
     *              10-05-2018 - Se agrega funcionalidad para que soporte L2MPLS
     * @since 1.5
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 27-06-2018 - Se agrega parametro al llamado de la funcion crearTareaRetiroEquipoPorDemo
     * @since 1.6
     *
     * Se agrega la creación del historial cuando el usuario convierte a orden de trabajo los servicios de un cliente con Deuda.
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.7
     * @since 22-01-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 14-02-2019 Se envía una notificación a PYL cuando un servicio TelcoHome pase a PrePlanificada
     * 
     * @author José Alava <jialava@telconet.ec>
     * @version 1.9 1-04-2019 Se añadió que hosting/pool de recurso pase a estado factible a preplanificado
     * ya que el cliente solicitó poder coordinar ese producto
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.0 31-07-2019 | Se agrega funcionalidad para que se valide si un servicio tradicional tiene
     *                           servicios Internet Wifi relacionados en estado 'Pre-servicio' y no permita continuar.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.1 21-08-2019 - Se modifica función para que valide si el servicio es Wifi Alquiler de equipos y
     *                           de esta manera cree orden de trabdeajo única por todos los servicios a instalar.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 2.1 04-09-2019 Se agrega validación para generar orden de trabajo para los productos 
     *                         INTERNET DC SDWAN - DATOS DC SDWAN.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.2 13-01-2019 - Se elimina la agrupación de ordenes en 1 sola tarea de planificación, para en el caso
     *                           de los servicios Wifi Alquiler Equipos.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.3 04-09-2020 - Se elimina logica que finalizaba la solicitud SOLICITUD NODO WIFI.
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 2.4 17-09-2020 - Se agrega validación para productos adicionales de Md.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 2.5
     * @since 05-03-2020 Se pasa Logica de convertir OT a capa Service.     
     *
     */    
     public function convertirOrdenTrabajoAction()
    {   
        //Proceso, crear el numero de o/t, ponerle ese numero ot, Actualizar en los servicios el número
        $objPeticion            = $this->get('request');
        $arrayParametro         = $objPeticion->get('param');
        $arrayValor            = explode("|", $arrayParametro);

        //Convierte a Orden de trabajo desde Clientes con Deuda
        $strMensajeObservacion  = $objPeticion->get('strMensajeObservacion');
        $strOTClienteConDeuda   = $objPeticion->get('strOTClienteConDeuda');
        // Verificar: Si el pto esta en session, sino tomarlo del formulario
        $objSession             = $objPeticion->getSession();
        $arrayPtoCliente        = $objSession->get('ptoCliente');
        $intIdPunto             = $arrayPtoCliente['id'];
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strOficina             = $objSession->get('idOficina');
        $strUser                = $objSession->get('user');
        $strIp                  = $objPeticion->getClientIp();
        $emComercial            = $this->getDoctrine()->getManager(); 
        
        try
        {            
            $arrayParametros = array('strOficina'             => $strOficina,
                                     'strUser'                => $strUser,
                                     'strIp'                  => $strIp,
                                     'array_valor'            => $arrayValor,
                                     'strMensajeObservacion'  => $strMensajeObservacion,                                    
                                     'strOTClienteConDeuda'   => $strOTClienteConDeuda,
                                     'intIdPunto'             => $intIdPunto,
                                     'strCodEmpresa'          => $strCodEmpresa,
                                     'strPrefijoEmpresa'      => $strPrefijoEmpresa);
       
            $serviceConvertirOT = $this->get('comercial.ConvertirOrdenTrabajo');
            $strResponse        = $serviceConvertirOT->convertirOrdenTrabajo($arrayParametros); 

        }
        catch(\Exception $e)
        {
            
            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            $strResponse = "Ocurrió un error al convertir a Orden de Trabajo, por favor consulte con el Administrador";           
        }
        return new Response($strResponse);
    }
	    
    /**
     * Documentación para el método 'listadoServiciosAction'.
     *
     * Método utilizado para retornar el listado de servicios que se pueden convertir a orden de trabajo
     *
     * @return JsonResponse $obJsonResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 16-02-2017 - Se valida que exista un servicio de Internet en estado diferente de los ingresados en el parámetro 'INFO_SERVICIO'.
     *                           Adicional se valida si se puede convertir a orden de trabajo los servicios de 'VENTA_EXTERNA' marcados en el campo
     *                           'ES_VENTA' como 'E' sólo si todos los servicios del punto en sessión están en estado 'Factible', caso se enviará en
     *                           la variable 'strEsCheckeable' el valor de 'N'.
     * @since 1.0
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 28-02-2017 - Se valida con el parámetro 'strEsVenta' si se deben traer los servicios de 'VENTA_EXTERNA' o los demás servicios. 
     *                           Adicional con el mismo parámetro se verifica si se deben validar los servicios de 'VENTA_EXTERNA'.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3
     * @since 07-01-2019
     * Se agrega validación para no presentar registros en caso que no se haya pagado su última factura de instalación.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.4 21-08-2019 - Se modifica funcionamiento para que antes de devolver la respuesta, se efectué una validación de si los servicios
     *                           son "Wifi Alquiler de Equipos" y solo devuelva el que fue inspeccionado.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.5 13-01-2020 - Se modifica funcionamiento para permitir que las ordenes de Wifi Alquiler Equipos 
     *                           sean individuales, no resumidas en 1 sola.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 20-05-2021 - Se verifica si existen productos adicionales Camara con servicio principal DATOS SAFECITY,
     *                           las adicionales no deben aparecer en el grid solo el principal.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 01-10-2021 - Se verifica si el servicio es SW POE GPON se ocultan los servicios adicionales GPON_MPLS
     *
     */
	public function listadoServiciosAction()
	{
        $objJsonResponse     = new JsonResponse();
        $emComercial         = $this->get('doctrine')->getManager('telconet');
        $emGeneral           = $this->getDoctrine()->getManager("telconet_general");
        $serviceUtil         = $this->get('schema.Util');
        $serviceInfoServicio = $this->get('comercial.InfoServicio');
        $serviceTecnico      = $this->get('tecnico.InfoServicioTecnico');
        $objRequest          = $this->getRequest();
        $objSession          = $objRequest->getSession();
        $strIpCreacion       = $objRequest->getClientIp();
        $strUsuario          = $objSession->get('user');
        $intLimit            = $objRequest->get("limit");
        $intStart            = $objRequest->get("start");
        $arrayPtocliente     = $objSession->get('ptoCliente');
        $strEmpresaCod       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa   = $objSession->get('prefijoEmpresa');
        $intIdPtoCliente     = ( isset($arrayPtocliente['id']) && !empty($arrayPtocliente['id']) ) ? $arrayPtocliente['id'] : 0;
        $strEsVenta          = $objRequest->query->get("strEsVenta") ? $objRequest->query->get("strEsVenta") : '';
        
        try
        {
            //MODULO 151 - clientes/preplanificarServicioNoPagado
            $booleanProcesaNoPagados = $this->get('security.context')->isGranted('ROLE_151-6297');
            if (!$booleanProcesaNoPagados)
            {
                $arrayValidaInstalacion = $this->get("financiero.InfoDocumentoFinancieroCab")
                                               ->aplicaFlujoOrdenTrabajo(array( "intPuntoId"     => $intIdPtoCliente,
                                                                                "strEmpresaCod"  => $strEmpresaCod,
                                                                                "strIpCreacion"  => $strIpCreacion,
                                                                                "strUsrCreacion" => $strUsuario));
                if ("OK" != $arrayValidaInstalacion["status"])
                {
                    throw new \Exception($arrayValidaInstalacion["message"]);
                }
            }

            if( empty($strEsVenta) )
            {
                throw new \Exception('No se envió el parámetro adecuado para obtener los servicios dependiendo de la venta realizada');
            }
            
            if( $strEsVenta == 'EXTERNA' )
            {
                /**
                 * Bloque Verificacion Venta Externa
                 * 
                 * Bloque que verifica:
                 *   - Si existen servicios de venta externa (ES_VENTA: 'E') en estado 'Pre-servicio'.
                 *   - Si existe al menos un servicio de Internet en estado diferente de los ingresados en el parámetro 'INFO_SERVICIO'
                 */
                $arrayParametrosVentaExterna          = array('strEmpresaCod'     => $strEmpresaCod, 
                                                              'strPrefijoEmpresa' => $strPrefijoEmpresa, 
                                                              'intIdPtoCliente'   => $intIdPtoCliente);
                $arrayValidacionServiciosVentaExterna = $serviceInfoServicio->validarServiciosVentaExterna($arrayParametrosVentaExterna);
                $strExisteServicioInternet            = ( isset($arrayValidacionServiciosVentaExterna['strExisteServicioInternet']) 
                                                          && !empty($arrayValidacionServiciosVentaExterna['strExisteServicioInternet']) ) 
                                                        ? $arrayValidacionServiciosVentaExterna['strExisteServicioInternet'] : 'N';
                $strExistenServiciosVentaExterna      = ( isset($arrayValidacionServiciosVentaExterna['strExistenServiciosVentaExterna']) 
                                                          && !empty($arrayValidacionServiciosVentaExterna['strExistenServiciosVentaExterna']) ) 
                                                        ? $arrayValidacionServiciosVentaExterna['strExistenServiciosVentaExterna'] : 'N';
                /**
                 * Fin Bloque Verificacion Venta Externa
                 */
            }//( $strEsVenta == 'EXTERNA' )

            $arrayParametrosServicios = array('strEsVenta' => $strEsVenta);
            
            $arrayResultado  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                           ->findServiciosFactibles($intIdPtoCliente, $intStart, $intLimit, $arrayParametrosServicios);
            $arrayDatos      = ( isset($arrayResultado['registros']) && !empty($arrayResultado['registros']) ) ? $arrayResultado['registros']
                               : array();

            $arrayServicio   = array();
            
            if( !empty($arrayDatos) )
            {
                foreach($arrayDatos as $objServicio)
                {
                    $strDescripcion  = "";
                    $strEsCheckeable = "S";
                    $objAdmiProducto = $objServicio->getProductoId();
                    $objInfoPlanCab  = $objServicio->getPlanId();
                    $strEsVenta      = $objServicio->getEsVenta();

                    if( is_object($objAdmiProducto) )
                    {
                        $strDescripcion = $objAdmiProducto->getDescripcionProducto();
                    }

                    if( is_object($objInfoPlanCab) )
                    {
                        $strDescripcion = $objInfoPlanCab->getDescripcionPlan();
                    }
                    
                    if( !empty($strEsVenta) && $strEsVenta == "E" && ( $strExistenServiciosVentaExterna == "S" 
                                                                       || $strExisteServicioInternet == "N" ) )
                    {
                        $strEsCheckeable = "N";
                    }

                    //se setea la variable para agregar el servicio
                    $booleanAddServicio      = true;

                    $strTipoRed = "";
                    if(is_object($objServicio) && is_object($objServicio->getProductoId()))
                    {
                        //se verifica si el producto tambien pertenece a GPON para setear por default tipo red MPLS
                        $arrayParProductoGpon = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                             'COMERCIAL',
                                                             '',
                                                             'PARAMETRO PARA DEFINIR EL TIPO DE RED GPON DE UN PRODUCTO',
                                                             $objServicio->getProductoId()->getId(),
                                                             '',
                                                             '',
                                                             'S',
                                                             'RELACION_PRODUCTO_CARACTERISTICA',
                                                             $strEmpresaCod);
                        if(isset($arrayParProductoGpon) && !empty($arrayParProductoGpon))
                        {
                            $strTipoRed = "MPLS";
                        }
                        //Obtener caracteristica de tipo de red
                        $objServCaractTipoRed = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                   'TIPO_RED',
                                                                                                   $objServicio->getProductoId());
                        if(is_object($objServCaractTipoRed))
                        {
                            $strTipoRed = $objServCaractTipoRed->getValor();
                        }
                        //verifcar servicio adicional oculto
                        $arrayParProductoVisible = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('NUEVA_RED_GPON_TN',
                                                                     'COMERCIAL',
                                                                     '',
                                                                     '',
                                                                     $objServicio->getProductoId()->getId(),
                                                                     'FLUJO_OCULTO',
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     $strEmpresaCod);
                        if(isset($arrayParProductoVisible) && !empty($arrayParProductoVisible)
                           && isset($arrayParProductoVisible['valor3']) && !empty($arrayParProductoVisible['valor3'])
                           && isset($arrayParProductoVisible['valor4']) && !empty($arrayParProductoVisible['valor4']))
                        {
                            $strDescCaract    = $arrayParProductoVisible['valor3'];
                            $strPermiteCaract = $arrayParProductoVisible['valor4'];
                            
                            $objServProdCaract = $serviceTecnico->getServicioProductoCaracteristica($objServicio,
                                                                                                    $strDescCaract,
                                                                                                    $objServicio->getProductoId());
                            if((!is_object($objServProdCaract) && $strPermiteCaract == "NO") ||
                               (is_object($objServProdCaract) && $strPermiteCaract == "SI"))
                            {
                                $booleanAddServicio = false;
                            }
                            //validar si existe un SW POE adicional
                            if($booleanAddServicio)
                            {
                                //obtener servicio SW POE
                                $arrayParServSwPoe = array(
                                    "objPunto"      => $objServicio->getPuntoId(),
                                    "strParametro"  => "PRODUCTO_ADICIONAL_SW_POE",
                                    "strCodEmpresa" => $strEmpresaCod
                                );
                                $arrayResultServicioSwPoe = $serviceTecnico->getServicioGponPorProducto($arrayParServSwPoe);
                                if($arrayResultServicioSwPoe['status'] == "OK")
                                {
                                    $objServicioSwPoe = $arrayResultServicioSwPoe['objServicio'];
                                    if(is_object($objServicioSwPoe) && $objServicioSwPoe->getEstado() == $objServicio->getEstado()
                                       && $objServicioSwPoe->getId() != $objServicio->getId())
                                    {
                                        $booleanAddServicio = false;
                                    }
                                }
                            }
                        }
                    }

                    //se valida si se agrega el servicio
                    if($booleanAddServicio)
                    {
                        $arrayServicio[] = array( 'id'                              => $objServicio->getId(),
                                                  'descripcion'                     => $strDescripcion,
                                                  'cantidad'                        => $objServicio->getCantidad(),
                                                  'estado'                          => $objServicio->getEstado(),
                                                  'precio'                          => $objServicio->getPrecioVenta(),
                                                  'strTipoRed'                      => $strTipoRed,
                                                  'strEsVenta'                      => $strEsVenta,
                                                  'strEsCheckeable'                 => $strEsCheckeable,
                                                  'strExisteServicioInternet'       => $strExisteServicioInternet,
                                                  'strExistenServiciosVentaExterna' => $strExistenServiciosVentaExterna );
                    }
                }//foreach($arrayDatos as $objServicio)
            }//( !empty($arrayDatos) )
        }
        catch( \Exception $e )
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'Orden de Trabajo', 
                                       'Error al consultar los servicios a convertir en orden de trabajo. '.$e->getMessage()
                                       . " puntoId:" . $intIdPtoCliente,
                                       $strUsuario, 
                                       $strIpCreacion );
        }

        $arrayRespuesta = array('total' => count($arrayServicio), 'listado' => $arrayServicio);
        
        $objJsonResponse->setData($arrayRespuesta);
        
        return $objJsonResponse;
	}
    
    
    /**
     * Documentación para el método 'validarServiciosVentaExternaAction'.
     *
     * Método utilizado para validar los siguiente:
     * - Si el usuario seleccionó todos los servicios de venta externa en estado factible.
     * - Si existe algún servicio de venta externa en estado 'Pre-servicio'
     * - Si todos los servicios seleccionados por el usuario tiene al menos un contrato externo digital asociado.
     *
     * @return Response $obResponse
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 17-02-2017
     */
	public function validarServiciosConvertirOrdenTrabajoAction()
	{
        $objResponse                      = new Response();
        $emComercial                      = $this->get('doctrine')->getManager('telconet');
        $emComunicacion                   = $this->get('doctrine')->getManager('telconet_comunicacion');
        $serviceUtil                      = $this->get('schema.Util');
        $serviceInfoServicio              = $this->get('comercial.InfoServicio');
        $objRequest                       = $this->getRequest();
        $objSession                       = $objRequest->getSession();
        $strIpCreacion                    = $objRequest->getClientIp();
        $strUsuario                       = $objSession->get('user');
        $strEmpresaCod                    = $objSession->get('idEmpresa');
        $strPrefijoEmpresa                = $objSession->get('prefijoEmpresa');
        $strMensajeRespuesta              = "OK";
        $intIdPtoCliente                  = $objRequest->request->get('intIdPtoCliente') ? $objRequest->request->get('intIdPtoCliente') : 0;
        $intCantidadServiciosVentaExterna = $objRequest->request->get('intCantidadServiciosVentaExterna')
                                            ? $objRequest->request->get('intCantidadServiciosVentaExterna') : 0;
        
        try
        {
            /**
             * Bloque Verificacion Venta Externa
             * 
             * Bloque que verifica:
             *   - Si existen servicios de venta externa (ES_VENTA: 'E') en estado 'Pre-servicio'.
             *   - Si existe al menos un servicio de Internet en estado diferente de los ingresados en el parámetro 'INFO_SERVICIO'
             */
            
            $arrayParametrosVentaExterna          = array('strEmpresaCod'     => $strEmpresaCod, 
                                                          'strPrefijoEmpresa' => $strPrefijoEmpresa, 
                                                          'intIdPtoCliente'   => $intIdPtoCliente);
            $arrayValidacionServiciosVentaExterna = $serviceInfoServicio->validarServiciosVentaExterna($arrayParametrosVentaExterna);
            $strExisteServicioInternet            = ( isset($arrayValidacionServiciosVentaExterna['strExisteServicioInternet']) 
                                                      && !empty($arrayValidacionServiciosVentaExterna['strExisteServicioInternet']) ) 
                                                    ? $arrayValidacionServiciosVentaExterna['strExisteServicioInternet'] : 'N';
            $strExistenServiciosVentaExterna      = ( isset($arrayValidacionServiciosVentaExterna['strExistenServiciosVentaExterna']) 
                                                      && !empty($arrayValidacionServiciosVentaExterna['strExistenServiciosVentaExterna']) ) 
                                                    ? $arrayValidacionServiciosVentaExterna['strExistenServiciosVentaExterna'] : 'N';
            /**
             * Fin Bloque Verificacion Venta Externa
             */
            
            if( $strExisteServicioInternet == "N" )
            {
                $strMensajeRespuesta = "No tiene servicios de Internet contratados";
            }//( $strExisteServicioInternet == "N" )
            elseif( $strExistenServiciosVentaExterna == "S" )
            {
                $strMensajeRespuesta = "Existen servicios con productos de Venta Externa en estado Pre-servicio";
            }//( $strExistenServiciosVentaExterna == "S" )
            else
            {
                //Consulto los servicios de venta externa en estado 'Factible' del punto cliente
                $objInfoPuntoCliente = $emComercial->getRepository('schemaBundle:InfoPunto')->findOneById($intIdPtoCliente);
                
                if( !is_object($objInfoPuntoCliente) )
                {
                    throw new \Exception('No se encontró el punto para buscar los servicios de venta externa en estado Factible.');
                }//( is_object($objInfoPuntoCliente) )
                
                $arrayServiciosVentaExterna = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                          ->findBy( array('esVenta' => 'E', 
                                                                          'estado'  => 'Factible', 
                                                                          'puntoId' => $objInfoPuntoCliente) );
                
                if( !empty($arrayServiciosVentaExterna) )
                {
                    $intContadorServiciosSinDocumentos = 0;
                    $intContadorServiciosConDocumentos = 0;
                    
                    foreach($arrayServiciosVentaExterna as $objServicio)
                    {
                        if( is_object($objServicio) )
                        {
                            //Se consulta la relación del servicio con los documentos de tipo 'VENTA EXTERNA'
                            $arrayParametrosDocumentosVentaExterna = array( 'servicios'                  => array($objServicio->getId()),
                                                                            'empresa'                    => $strEmpresaCod,
                                                                            'strCodigoTipoDocumento'     => 'VTAEX',
                                                                            'estadoDocumento'            => array('Activo'),
                                                                            'estadoDocumentoRelacion'    => array('Activo'),
                                                                            'estadoTipoDocumentoGeneral' => array('Activo'),
                                                                            'intIdPuntoCliente'          => $intIdPtoCliente );
                            $arrayDocumentos = $emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                              ->getDocumentosByCriterios( $arrayParametrosDocumentosVentaExterna );
                            
                            if( isset($arrayDocumentos['total']) && !empty($arrayDocumentos['total']) && $arrayDocumentos['total'] > 0 )
                            {
                                $intContadorServiciosConDocumentos++;
                            }
                            else
                            {
                                $intContadorServiciosSinDocumentos++;
                            }//( isset($arrayDocumentos['total']) && !empty($arrayDocumentos['total']) && $arrayDocumentos['total'] > 0 )
                        }//( is_object($objServicio) )
                    }//foreach($arrayServiciosVentaExterna as $objServicio)
                    
                    
                    /**
                     * Se verifica que los servicios seleccionados por el usuario no contienen un documento externo digital asociado.
                     */
                    if( $intContadorServiciosSinDocumentos > 0 )
                    {
                        $strMensajeRespuesta = "Existen servicios que no tienen documento externo digital asociado";
                    }
                    /**
                     * Se verifica que los servicios seleccionados por el usuario corresponden a la misma cantidad de servicios de venta externa en
                     * estado factible
                     */
                    else if( $intContadorServiciosConDocumentos != $intCantidadServiciosVentaExterna )
                    {
                        $strMensajeRespuesta = "Debe seleccionar todos los servicios asociados con productos de venta externa";
                    }//( $intContadorServiciosConDocumentos != $intCantidadServiciosVentaExterna )
                }
                else
                {
                    $strMensajeRespuesta = "No se encontraron servicios asociados con productos de venta externa en estado Factible para convertir ".
                                           "a orden de trabajo";
                }//( !empty($arrayServiciosVentaExterna) )
            }//else
        }//try
        catch( \Exception $e )
        {
            $strMensajeRespuesta = "Error al validar los servicios a convertir en orden de trabajo";
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'Orden de Trabajo', 
                                       'Error al validar los servicios a convertir en orden de trabajo. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objResponse->setContent($strMensajeRespuesta);
        
        return $objResponse;
	}
}
