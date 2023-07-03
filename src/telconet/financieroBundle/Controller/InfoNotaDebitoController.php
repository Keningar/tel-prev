<?php

namespace telconet\financieroBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Form\InfoDocumentoFinancieroCabType;

use telconet\schemaBundle\Entity\InfoDocumentoFinancieroDet;
use telconet\schemaBundle\Entity\InfoDocumentoHistorial;

use Symfony\Component\HttpFoundation\Response;
use JMS\Security\ExtraBundle\Annotation\Secure;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;

use telconet\soporteBundle\Service\EnvioPlantillaService;
use telconet\financieroBundle\Service\InfoDevolucionService;
/**
 * InfoNotaDebito controller.
 *
 */
class InfoNotaDebitoController extends Controller implements TokenAuthenticatedController
{ 
    /**
     * Lists all InfoNotaDebito entities.
     *
     */
    public function indexAction()
    {
		/*$request=$this->get('request');
		$session=$request->getSession();
		$cliente=$session->get('cliente');
		$ptocliente=$session->get('ptoCliente');
		
		print_r($cliente);
		echo "<br />";
		print_r($ptocliente);*/
		
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        //$entities = $em->getRepository('schemaBundle:InfoPagoCab')->findAll();

        /*return $this->render('financieroBundle:InfoNotaDebito:index.html.twig', array(
            'entities' => $entities,
        ));*/
        
        return $this->render('financieroBundle:InfoNotaDebito:index.html.twig');
    }

    /**
     * Finds and displays a InfoNotaDebito entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoNotaDebito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        $oficina=$em->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        
        $em_comercial = $this->getDoctrine()->getManager("telconet");
        $pto_cliente=$em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        //$persona=$em_comercial->getRepository('schemaBundle:InfoPersona')->find($pto_cliente->getPersonaId());
        $persona=$em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($pto_cliente->getPersonaEmpresaRolId()->getId());
        
        $informacion_persona['puntoId']=$pto_cliente->getLogin();
        if($persona->getPersonaId()->getNombres()!="" && $persona->getPersonaId()->getApellidos()!="")
			$informacion_persona['cliente']=$persona->getPersonaId()->getNombres()." ".$persona->getPersonaId()->getApellidos();
		
		if($persona->getPersonaId()->getRepresentanteLegal()!="")
			$informacion_persona['cliente']=$persona->getPersonaId()->getRepresentanteLegal();
			
		if($persona->getPersonaId()->getRazonSocial()!="")
			$informacion_persona['cliente']=$persona->getPersonaId()->getRazonSocial();
	
         $rolesPermitidos = array();
        
         if (true === $this->get('security.context')->isGranted('ROLE_71-1067'))
        {
                $rolesPermitidos[] = 'ROLE_71-1067'; //editar depositos en financiero
        }
        
                
        return $this->render('financieroBundle:InfoNotaDebito:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'info_cliente' => $informacion_persona,
            'oficina'=> $oficina->getNombreOficina(),
             'rolesPermitidos' => $rolesPermitidos
            ));
    }

    /**
     * Displays a form to create a new InfoNotaDebito entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoDocumentoFinancieroCab();
        $form   = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
		
        //informacion del pto cliente
        $request=$this->get('request');
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');
		
        $parametros=array(
        'entity' => $entity,
        'form'   => $form->createView(),
        );
		
        if($ptocliente)
        {
            $parametros['punto_id']=$ptocliente;
            $parametros['cliente']=$cliente;
        }
        $em = $this->getDoctrine()->getManager("telconet_financiero");		
        //busqueda del documento
        $estados=array('Anulado','Anulada','Cruzado');  
        $listadoPagos=$em->getRepository('schemaBundle:InfoPagoDet')->listarDetallesDePagoPorPuntoNotIn($ptocliente['id'],$estados);
        foreach($listadoPagos as $pago){
            $objNotaDebitoDet=$em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findNotasDeDebitoPorPagoDetIdPorEstados($pago['id'],array('Activo','Activa','Pendiente'));
            //si la nota de debito no tiene pago asociado el pago esta disponible
            if(!$objNotaDebitoDet){
                $pagos[]=$pago;
            }
        }
        $parametros['listadosPagos']=$pagos;
        //busqueda del documento
        $em_general = $this->getDoctrine()->getManager();
        $listadoMotivos = $em_general->getRepository('schemaBundle:AdmiMotivo')->findMotivosPorModuloPorItemMenuPorAccion('nota_de_debito','','new');
        $parametros['listadoMotivos']=$listadoMotivos;
        return $this->render('financieroBundle:InfoNotaDebito:new.html.twig', $parametros);
    }

    /**
     * Creates a new InfoNotaDebito entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoDocumentoFinancieroCab();
        $form = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $form->bind($request);

        $informacionGrid=$request->get('listado_informacion');
        $informacionGrid=json_decode($informacionGrid);
        //informacion del pto cliente
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');
        $empresa_id=$session->get('idEmpresa');
        $oficina_id=$session->get('idOficina');
        $user=$session->get('user');

        $punto_id=$ptocliente['id'];
        $estado="Activo";
        $em = $this->getDoctrine()->getManager("telconet_financiero");        
        $em_comercial = $this->getDoctrine()->getManager("telconet");
        $em->getConnection()->beginTransaction();
        $em_comercial->getConnection()->beginTransaction();
        try{ 		
            if($punto_id)
            {
                //Obtener la numeracion de la tabla Admi_numeracion
                $estado="Pendiente";
                $em_comercial = $this->getDoctrine()->getManager();
                $datosNumeracion = $em_comercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($empresa_id,$oficina_id,"ND");
                $secuencia_asig=str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
                $numero_de_nota=$datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuencia_asig;

                //busqueda del documento
                $documento=$em->getRepository('schemaBundle:AdmiTipoDocumentoFinanciero')->findOneByCodigoTipoDocumento("ND");
                $entity->setTipoDocumentoId($documento);
                $entity->setPuntoId($punto_id);
                $entity->setEsAutomatica("N");
                $entity->setProrrateo("N");
                $entity->setReactivacion("N");
                $entity->setRecurrente("N");
                $entity->setComisiona("N");
                $entity->setOficinaId($oficina_id);
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setFeEmision(new \DateTime('now'));
                $entity->setUsrCreacion($user);
                $entity->setEstadoImpresionFact($estado);
                $entity->setNumeroFacturaSri($numero_de_nota);
                $em->persist($entity);
                $em->flush();

                if($entity)
                {
                    //Actualizo la numeracion en la tabla
                    $numero_act=($datosNumeracion->getSecuencia()+1);
                    $datosNumeracion->setSecuencia($numero_act);
                    $em_comercial->persist($datosNumeracion);
                    $em_comercial->flush();
                }
                //Guardando el detalle
                $acum=0;
                $acum_descu=0;
                $arreglo_porc=array();
                $acum_descu_total=0;
                if($informacionGrid)
                {
                    foreach($informacionGrid as $info)
                    {
                        //consulta si el detalle del pago ya tiene una nota de debito asignada
                        $objNotaDebitoDet=$em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')
                                ->findNotasDeDebitoPorPagoDetIdPorEstados($info->idpagodet,array('Activo','Activa','Pendiente'));
                        if ($objNotaDebitoDet) 
                        {
                            throw $this->createNotFoundException('El detalle del pago seleccionado ya esta asignado a una Nota de debito');
                        }                        
                        $entitydet  = new InfoDocumentoFinancieroDet();
                        $entitydet->setDocumentoId($entity);
                        $entitydet->setPuntoId($punto_id);
                        $entitydet->setCantidad(1);
                        //$entitydet->setPersonaId($persona->getId());
                        $entitydet->setEmpresaId($empresa_id);
                        $entitydet->setOficinaId($oficina_id);
                        //El precio ya incluye el descuento... en el caso de los planes
                        $entitydet->setPrecioVentaFacproDetalle($info->valor);
                        //El descuento debe ser informativo
                        $entitydet->setPorcetanjeDescuentoFacpro(0);
                        $entitydet->setFeCreacion(new \DateTime('now'));
                        $entitydet->setObservacionesFacturaDetalle($info->observacion);
                        $entitydet->setMotivoId($info->id);
                        $entitydet->setPagoDetId($info->idpagodet);
                        $entitydet->setUsrCreacion($user);
                        $em->persist($entitydet);
                        $em->flush();
                        $acum+=$entitydet->getPrecioVentaFacproDetalle();						
                    }
                }

                $entity_act=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($entity->getId());
                $entity_act->setSubtotal($acum);
                $entity_act->setSubtotalConImpuesto(0);
                $entity_act->setSubtotalDescuento(0);
                $entity_act->setValorTotal($acum);
                $em->persist($entity_act);
                $em->flush();
                $em->getConnection()->commit();
                $em_comercial->getConnection()->commit();
                return $this->redirect($this->generateUrl('infodocumentonotadebito_show', array('id' => $entity->getId())));
            }
            return $this->redirect($this->generateUrl('infodocumentonotadebito_new'));
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $em_comercial->getConnection()->rollback();
            $em_comercial->getConnection()->close();    
	    $this->get('session')->getFlashBag()->add('error', $e->getMessage());
            return $this->redirect($this->generateUrl('infodocumentonotadebito_new'));
        }
    }

    /**
     * Displays a form to edit an existing InfoNotaDebito entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoNotaDebito entity.');
        }

        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

		//tomar el punto de la session
        $punto="28";
        
        return $this->render('financieroBundle:InfoNotaDebito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'punto_id'=>$punto
        ));
    }

    /**
     * Edits an existing InfoNotaDebito entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
		$informacionGrid=$request->get('listado_informacion');
		$informacionGrid=json_decode($informacionGrid);
		
		$punto_id=$request->get('punto_id');
		//$empresa_id="10";
		//$oficina_id="1";
		$estado="Activo";
		
		$session=$request->getSession();
		$empresa_id=$session->get('idEmpresa');
		$oficina_id=$session->get('idOficina');
		$user=$session->get('user');
		
        $em = $this->getDoctrine()->getManager("telconet_financiero");

        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoNotaDebito entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

			//Guardando el detalle
			if($informacionGrid)
			{
				//busqueda de la persona
				$em_comercial = $this->getDoctrine()->getManager("telconet");
				$persona=$em_comercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($user);
				
				foreach($informacionGrid as $info)
				{
					$entitydet  = new InfoDocumentoFinancieroDet();
					
					if($info->tipo=='PR')
					{
						//$informacionCodigo=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find();
						$entitydet->setProductoId($info->codigo);
					}	
					if($info->tipo=='PL')
					{
						//$informacionCodigo=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($info->codigo);
						$entitydet->setPlanId($info->codigo);
					}	
					
					$entitydet->setDocumentoId($entity);
					$entitydet->setPuntoId($punto_id);
					$entitydet->setCantidad($info->cantidad);
					//$entitydet->setPersonaId($persona->getId());
					$entitydet->setEmpresaId($empresa_id);
					$entitydet->setOficinaId($oficina_id);
					//El precio ya incluye el descuento... en el caso de los planes
					$entitydet->setPrecioVentaFacproDetalle($info->precio);
					//El descuento debe ser informativo
					$entitydet->setPorcetanjeDescuentoFacpro($info->descuento);
					$entitydet->setFeCreacion(new \DateTime('now'));
					$entitydet->setUsrCreacion($user);
					$em->persist($entitydet);
					$em->flush();
					
				}
			}
            return $this->redirect($this->generateUrl('infodocumentonotacredito_edit', array('id' => $id)));
        }

        return $this->render('financieroBundle:InfoNotaDebito:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a InfoNotaDebito entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("telconet_financiero");
            $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoNotaDebito entity.');
            }

			//$entity->setEstadoImpresionFact("Inactivo");
			$entity->setEstadoImpresionFact("Anulado");
			$em->persist($entity);
            //$em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('InfoNotaDebito'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function infoOrdenesPtoClienteAction()
    {
        $request = $this->getRequest();
        $puntoid=$request->get('puntoid');
        
        //informacion presente en el grid
		$informacionGrid=$request->get('informacionGrid');
		$informacionGrid=json_decode($informacionGrid);
		
        //$idEmpresa = $session->get('idEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');	
        //$idProducto = $request->request->get("idProducto"); 	
        $estado="Pendiente";
        //$id_empresa="10";
        $session=$request->getSession();
		$id_empresa=$session->get('idEmpresa');
		
        $resultado= $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa,$puntoid,$estado);
        $listado_detalles_orden=$resultado['registros'];
        //$listado_detalles_orden = $em->getRepository('schemaBundle:InfoServicio')->findPorEstado($puntoid,$estado);
        //$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
        
        //print_r($listado_detalles_orden);
        if(!$listado_detalles_orden){
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
                $detalle_orden_l[] = array("codigo"=>"","informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"","tipo"=>"");
        }else{
                $detalle_orden_l = array();
                foreach($listado_detalles_orden as $ord){
                    if($ord->getProductoId())
                    {
						$tecn['codigo']=$ord->getProductoId()->getId();
                        $tecn['informacion'] = $ord->getProductoId()->getDescripcionProducto();
                        $tecn['tipo'] = 'PR';
                    }
                    if($ord->getPlanId())
                    {
						$tecn['codigo']=$ord->getPlanId()->getId();
                        $tecn['informacion'] = $ord->getPlanId()->getNombrePlan();
                        $tecn['tipo'] = 'PL';
                    }
                    $tecn['precio'] = $ord->getPrecioVenta();
                    $tecn['cantidad'] = $ord->getCantidad();
                    $tecn['descuento'] = $ord->getPorcentajeDescuento();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        if($informacionGrid)
        {
			foreach($informacionGrid as $info)
			{
				$tecdetalleFacturan['codigo']=$info->codigo;
				$tecn['informacion']=$info->informacion;
				$tecn['precio']=$info->precio;
				$tecn['cantidad']=$info->cantidad;
				$tecn['descuento']=$info->descuento;
				$tecn['tipo'] = $info->tipo;
				$detalle_orden_l[] = $tecn;
			}
		}
        
        //print_r($detalle_orden_l);
        //die();
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        //echo $response;
        //die();
        return $response;
    }
    
    public function detalleFacturaAction()
    {
		$request = $this->getRequest();
        $facturaid=$request->get('facturaid');
		
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
                $detalle_orden_l[] = array("informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"");
        }else{
				$em_comercial = $this->get('doctrine')->getManager('telconet');	
                $detalle_orden_l = array();
                foreach($resultado as $factdet){
                    if($factdet->getProductoId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                        $tecn['informacion'] = $informacion->getDescripcionProducto();
                    }
                    if($factdet->getPlanId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                        $tecn['informacion'] = $informacion->getNombrePlan();
                    }
                    $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                    $tecn['cantidad'] = $factdet->getCantidad();
                    $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
	public function detalleFacturaEditarAction()
	{
		$request = $this->getRequest();
        $facturaid=$request->get('facturaid');
        $precargado=$request->get('precargado');
		
        $em = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
                //$caracteristicas[] = array("idCaracteristica"=>"","caracteristica"=>"");
                $detalle_orden_l[] = array("codigo"=>"","informacion"=>"","precio"=>"","cantidad"=>"","descuento"=>"","tipo"=>"");
        }else{
				$em_comercial = $this->get('doctrine')->getManager('telconet');	
                $detalle_orden_l = array();
                foreach($resultado as $factdet){
                    if($factdet->getProductoId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
                        $tecn['codigo'] =$informacion->getId();
                        $tecn['informacion'] = $informacion->getDescripcionProducto();
                        $tecn['tipo'] ="PR";
                    }
                    if($factdet->getPlanId())
                    {
						$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
                        $tecn['codigo'] =$informacion->getId();
                        $tecn['informacion'] = $informacion->getNombrePlan();
                        $tecn['tipo'] ="PL";
                    }
                    $tecn['precio'] = $factdet->getPrecioVentaFacproDetalle();
                    $tecn['cantidad'] = $factdet->getCantidad();
                    $tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
                    $detalle_orden_l[] = $tecn;
                }
        }
        
        if(isset($precargado) && $precargado=="P")
        {
			$puntoid=$request->get('puntoid');
			
			//$idEmpresa = $session->get('idEmpresa');
			$em = $this->get('doctrine')->getManager('telconet');	
			//$idProducto = $request->request->get("idProducto"); 	
			$estado="Pendiente";
			//$id_empresa="10";
			$session=$request->getSession();
			$id_empresa=$session->get('idEmpresa');
			
			$resultado= $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoPorEstado($id_empresa,$puntoid,$estado);
			$listado_detalles_orden=$resultado['registros'];
			//$listado_detalles_orden = $em->getRepository('schemaBundle:InfoServicio')->findPorEstado($puntoid,$estado);
			//$clientes[] = array("idCl"=>"$telefono","loginClienteSucursal"=>"","descripcion"=>"");
			
			//print_r($listado_detalles_orden);
			if($listado_detalles_orden){
					$detalle_orden_l = array();
					foreach($listado_detalles_orden as $ord){
						if($ord->getProductoId())
						{
							$tecn['codigo']=$ord->getProductoId()->getId();
							$tecn['informacion'] = $ord->getProductoId()->getDescripcionProducto();
							$tecn['tipo'] = 'PR';
						}
						if($ord->getPlanId())
						{
							$tecn['codigo']=$ord->getPlanId()->getId();
							$tecn['informacion'] = $ord->getPlanId()->getNombrePlan();
							$tecn['tipo'] = 'PL';
						}
						$tecn['precio'] = $ord->getPrecioVenta();
						$tecn['cantidad'] = $ord->getCantidad();
						$tecn['descuento'] = $ord->getPorcentajeDescuento();
						$detalle_orden_l[] = $tecn;
					}
			}
		}
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
    public function listarTodasNDAction()
    {
        $objRequest            = $this->getRequest();
        $objSession            = $objRequest->getSession();
        $arrayPtocliente       = $objSession->get('ptoCliente');
        $intIdEmpresa          = $objSession->get('idEmpresa');
        $intIdOficina          = $objSession->get('idOficina');
        $emComercial           = $this->get('doctrine')->getManager('telconet');
        $strTipoPersonal       = 'Otros';
        $strPrefijoEmpresa     = $objSession->get('prefijoEmpresa');
        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol');
        $strUsrCreacion        = $objSession->get('user');
        /**
         * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
         */
        $arrayResultadoCaracteristicas = $emComercial->getRepository('schemaBundle:InfoPersona')->getCargosPersonas($strUsrCreacion);
        if( !empty($arrayResultadoCaracteristicas) )
        {
            $arrayResultadoCaracteristicas = $arrayResultadoCaracteristicas[0];
            $strTipoPersonal               = $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] ? $arrayResultadoCaracteristicas['STRCARGOPERSONAL'] : 'Otros';
        }
        $em            = $this->get('doctrine')->getManager('telconet_financiero');
        $strFechaDesde = explode('T',$objRequest->get("fechaDesde"));
        $strFechaHasta = explode('T',$objRequest->get("fechaHasta"));
        $strEstado     = $objRequest->get("estado") ? $objRequest->get("estado"):'Activo';
        $intLimit      = $objRequest->get("limit");
        $intStart      = $objRequest->get("start");

        if($arrayPtocliente)
        {
            $intIdPunto=$arrayPtocliente['id'];
        }
        else
        {
            $intIdPunto="";
        }
        $arrayParametros   = array();
        $arrayParametros['intIdOficina']          = $intIdOficina;
        $arrayParametros['strEstado']             = $strEstado;
        $arrayParametros['intIdPunto']            = $intIdPunto;
        $arrayParametros['intIdEmpresa']          = $intIdEmpresa;
        $arrayParametros['strFechaDesde']         = $strFechaDesde[0];
        $arrayParametros['strFechaHasta']         = $strFechaHasta[0];
        $arrayParametros['intStart']              = $intStart;
        $arrayParametros['intLimit']              = $intLimit;
        $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
        $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
        $arrayParametros['intIdPersonEmpresaRol'] = $intIdPersonEmpresaRol;

        $arrayResultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->getNotasDebito($arrayParametros);
        $arrayDatos     = $arrayResultado['registros'];
        $intTotal       = $arrayResultado['total'];

        $i=1;
        foreach ($arrayDatos as $datos):
            if($i % 2==0)
                    $clase='k-alt';
            else
                    $clase='';

            $urlVer = $this->generateUrl('infodocumentonotadebito_show', array('id' => $datos->getId()));
            //$urlEditar = $this->generateUrl('infodocumentonotacredito_edit', array('id' => $datos->getId()));
            $urlEliminar = $this->generateUrl('infodocumentonotadebito_delete_ajax', array('id' => $datos->getId()));
            $linkVer = $urlVer;
            //$linkEditar = $urlEditar;
            $linkEliminar=$urlEliminar;
            
            $em_comercial = $this->get('doctrine')->getManager('telconet');
            $pto_cliente=$em_comercial->getRepository('schemaBundle:InfoPunto')->find($datos->getPuntoId());
            //$persona=$em_comercial->getRepository('schemaBundle:InfoPersona')->find($pto_cliente->getPersonaId());
            $persona=$em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($pto_cliente->getPersonaEmpresaRolId()->getId());
            
            if($persona->getPersonaId()->getNombres()!="" && $persona->getPersonaId()->getApellidos()!="")
				$informacion_cliente=$persona->getPersonaId()->getNombres()." ".$persona->getPersonaId()->getApellidos();
			
			if($persona->getPersonaId()->getRazonSocial()!="")
				$informacion_cliente=$persona->getPersonaId()->getRazonSocial();
			
			if($datos->getEsAutomatica()=="S")
				$automatica="Si";
			else
				$automatica="No";
				
            $arreglo[]= array(
                'Numerofacturasri'=>$datos->getNumeroFacturaSri(),
                'Punto'=> $pto_cliente->getDescripcionPunto(),
                'Cliente'=>$informacion_cliente,
                'Esautomatica'=>$automatica,
                'Estado'=>$datos->getEstadoImpresionFact(),
                'Fecreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
                'linkVer'=> $linkVer,
                'linkEliminar'=> $linkEliminar,
                'clase'=>$clase,
                'boton'=>"",
                'id'=>$datos->getId(),
            );              

            $i++;     
        endforeach;

        $objResponse = new Response(json_encode(array('total' => $intTotal, 'documentos' => $arreglo)));
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
            $entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);
            if($entity){
                //$entity->setEstado("Inactivo");
                $entity->setEstado("Anulado");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        return $respuesta;
    }
	
	public function restarFechas($fechaDesde,$fechaHasta)
	{
		$fechaHastaFin=explode('-',$fechaHasta);
		$fechaDesdeFin=explode('-',$fechaDesde);
		
		$ano1 = $fechaHastaFin[0]; 
		$mes1 = $fechaHastaFin[1]; 
		$dia1 = $fechaHastaFin[2]; 

		//defino fecha 2 
		$ano2 = $fechaDesdeFin[0]; 
		$mes2 = $fechaDesdeFin[0]; 
		$dia2 = $fechaDesdeFin[0]; 

		//calculo timestam de las dos fechas 
		//$timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1); 
		$timestamp1 = strtotime($fechaHasta); 
		$timestamp2 = strtotime($fechaDesde); 
		//$timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2); 

		//resto a una fecha la otra 
		$segundos_diferencia = $timestamp1 - $timestamp2; 
		//echo $segundos_diferencia; 

		//convierto segundos en días 
		$dias_diferencia = $segundos_diferencia / 60 / 60 / 24; 

		//obtengo el valor absoulto de los días (quito el posible signo negativo) 
		$dias_diferencia = abs($dias_diferencia); 

		//quito los decimales a los días de diferencia 
		$dias_diferencia = floor($dias_diferencia); 

		$dias_diferencia++;

		return $dias_diferencia; 
	}
	
	public function ajaxGenerarDetallesPorDiasAction()
	{
		$request = $this->getRequest();		    
		$fechaDesde=$request->get("fechaDesde");
        $fechaHasta=$request->get("fechaHasta");
        $idFactura=$request->get("idFactura");
        $porcentaje=$request->get("porcentaje");
        $tipo=$request->get("tipo");
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($idFactura);
        //$resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId(57);
                
        
        if($tipo=="PD")
        {
			$cantidadDias=$this->restarFechas($fechaDesde,$fechaHasta);
			//$cantidadDias=5;
			
			//Segun calendario comercial 30 dias
			$diasTotales=30;
			if(!$resultado)
			{
					$detalle_orden_l[] = array("codigo"=>"","informacion"=>"","valor"=>"","cantidad"=>"","tipo"=>"");
			}else{
					$em_comercial = $this->get('doctrine')->getManager('telconet');	
					$detalle_orden_l = array();
					foreach($resultado as $factdet){
						if($factdet->getProductoId())
						{
							$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
							$tecn['codigo'] =$informacion->getId();
							$tecn['informacion'] = $informacion->getDescripcionProducto();
							$tecn['tipo'] ="PR";
						}
						if($factdet->getPlanId())
						{
							$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
							$tecn['codigo'] =$informacion->getId();
							$tecn['informacion'] = $informacion->getNombrePlan();
							$tecn['tipo'] ="PL";
						}
						
						$valor=(($factdet->getPrecioVentaFacproDetalle()* $cantidadDias)/$diasTotales);
						$tecn['valor'] = $valor;
						$tecn['cantidad'] = $factdet->getCantidad();
						$tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
						$detalle_orden_l[] = $tecn;
					}
			}
        }
        
        if($tipo=="PS")
        {
			if(!$resultado)
			{
					$detalle_orden_l[] = array("codigo"=>"","informacion"=>"","valor"=>"","cantidad"=>"","tipo"=>"");
			}else{
					$em_comercial = $this->get('doctrine')->getManager('telconet');	
					$detalle_orden_l = array();
					foreach($resultado as $factdet){
						if($factdet->getProductoId())
						{
							$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
							$tecn['codigo'] =$informacion->getId();
							$tecn['informacion'] = $informacion->getDescripcionProducto();
							$tecn['tipo'] ="PR";
						}
						if($factdet->getPlanId())
						{
							$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
							$tecn['codigo'] =$informacion->getId();
							$tecn['informacion'] = $informacion->getNombrePlan();
							$tecn['tipo'] ="PL";
						}
						
						$valor=(($factdet->getPrecioVentaFacproDetalle()* $porcentaje)/100);
						$tecn['valor'] = $valor;
						$tecn['cantidad'] = $factdet->getCantidad();
						$tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
						$detalle_orden_l[] = $tecn;
					}
			}
		}
		
		if($tipo=="VO")
		{
			if(!$resultado)
			{
					$detalle_orden_l[] = array("codigo"=>"","informacion"=>"","valor"=>"","cantidad"=>"","tipo"=>"");
			}else{
					$em_comercial = $this->get('doctrine')->getManager('telconet');	
					$detalle_orden_l = array();
					foreach($resultado as $factdet){
						if($factdet->getProductoId())
						{
							$informacion=$em_comercial->getRepository('schemaBundle:AdmiProducto')->find($factdet->getProductoId());
							$tecn['codigo'] =$informacion->getId();
							$tecn['informacion'] = $informacion->getDescripcionProducto();
							$tecn['tipo'] ="PR";
						}
						if($factdet->getPlanId())
						{
							$informacion=$em_comercial->getRepository('schemaBundle:InfoPlanCab')->find($factdet->getPlanId());
							$tecn['codigo'] =$informacion->getId();
							$tecn['informacion'] = $informacion->getNombrePlan();
							$tecn['tipo'] ="PL";
						}
						
						$valor=$factdet->getPrecioVentaFacproDetalle();
						$tecn['valor'] = $valor;
						$tecn['descuento'] = $factdet->getPorcetanjeDescuentoFacpro();
						$tecn['cantidad'] = $factdet->getCantidad();
						$detalle_orden_l[] = $tecn;
					}
			}
		}
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;

	}
	
    /**
     * Documentación para el método 'detalleNDAction'.
     *
     * Por medio de la funcion podemos obtener el detalle de la nota de debito interna
     *
     * @return json Listado detalle devolucion
     *
     * Se agrega la validacion para los motivos ya que existen NDI que se crean de manera automatica.
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.1 04-08-2016
     * @since 1.0
     * 
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.2 28-04-2020 - Se agrega detalle de la NDI cuando tiene caracteristica de Diferido.
     */
	public function detalleNDAction()
	{
        $request       = $this->getRequest();
        $facturaid     = $request->get('facturaid');
        $em            = $this->get('doctrine')->getManager('telconet_financiero');	
        $resultado     = $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        $strNumeroPago = "";
        
        $detalle_orden_l = array();
        if(!$resultado)
        {
            $detalle_orden_l[] = array("motivo"=>"","observacion"=>"","valor"=>"");
        }
        else
        {
            $em_comercial = $this->get('doctrine')->getManager('telconet');	
            foreach($resultado as $factdet)
            {
                if($factdet->getMotivoId())
                {
                    $strMotivo      = '';
                    $informacion    = $em_comercial->getRepository('schemaBundle:AdmiMotivo')->find($factdet->getMotivoId());
                    if($informacion)
                    {
                        $strMotivo  = $informacion->getNombreMotivo();
                    }
                    else
                    {
                        $strMotivo  = '';
                    }      
                }
                
                $strNumeroPago = "";
                
                if($factdet->getPagoDetId())
                {
                    $pago_det       = $em->getRepository('schemaBundle:InfoPagoDet')->find($factdet->getPagoDetId());                             
                    if($pago_det)
                    {
                        $strNumeroPago = $pago_det->getPagoId()->getNumeroPago();
                    }
                }
                
                $tecn['id']          = $factdet->getId();			
                $tecn['motivo']      = $strMotivo;
                $tecn['observacion'] = $factdet->getObservacionesFacturaDetalle();
                $tecn['valor']       = $factdet->getCantidad();
                $tecn['valor_total'] = $factdet->getPrecioVentaFacproDetalle();
                $tecn['numero_pago'] = $strNumeroPago;
                $detalle_orden_l[]   = $tecn;
            }
        }

        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
	}
	
	public function deleteSeleccionadasAjaxAction()
	{
	$respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $idfactura = $peticion->get('idfactura');
        $motivos = $peticion->get('motivos');
        
		$session=$peticion->getSession();
		$user=$session->get('user');
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager("telconet_financiero");
		$entity=$em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($idfactura);
		if($entity){
			//$entity->setEstadoImpresionFact("Inactivo");
			$entity->setEstadoImpresionFact("Anulado");
			$em->persist($entity);
			$em->flush();
			$entityHistorial  = new InfoDocumentoHistorial();
			$entityHistorial->setDocumentoId($entity);
			$entityHistorial->setMotivoId($motivos);
			$entityHistorial->setFeCreacion(new \DateTime('now'));
			$entityHistorial->setUsrCreacion($user);
			$entityHistorial->setEstado("Anulado");
			//$entityHistorial->setEstado("Inactivo");
			$em->persist($entityHistorial);
			$em->flush();
			$response = new Response(json_encode(array('success'=>true)));
		}
		else
			$response = new Response(json_encode(array('success'=>false)));
        $response->headers->set('Content-type', 'text/json');
		return $response;
	}
        
	public function editarNotadebitoAction(){
         $request = $this->getRequest();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $usuario=$request->getSession()->get('user');
        //echo $id;die;
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet_financiero');
        
         //Obtiene parametros enviados desde el ajax
          $peticion = $this->get('request');
          $iddebitoDet=$peticion->get('iddebito');
           //print_r($iddebitoDet); die();
           $observacion=$peticion->get('observacion');
        // print_r($observacion); die();
          
          $em->getConnection()->beginTransaction();
  try {
             $entityDebitoDet=$em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->find($iddebitoDet); 
            
             $mensajeDetalleAnt=$entityDebitoDet->getObservacionesFacturaDetalle();
             $edito=false;
             
        if(isset($entityDebitoDet) && $entityDebitoDet ){
           
             if($mensajeDetalleAnt!=$observacion){
                 
                 $entityDebitoDet->setObservacionesFacturaDetalle($observacion);
                 $em->persist($entityDebitoDet);
                 $em->flush();
                 $edito=true;
             }
             if($edito){
                  $entityHistorial = new InfoDocumentoHistorial();
                  $entityHistorial->setDocumentoId($entityDebitoDet->getDocumentoId());
                  $entityHistorial->setFeCreacion(new \DateTime('now'));
                  $entityHistorial->setUsrCreacion($usuario);
                  $entityHistorial->setEstado('Modificado');
                  $em->persist($entityHistorial);
                  $em->flush();    
                  
                  
               $em->getConnection()->commit();
               $respuesta->setContent("Se actualizo el  registro con exito.");
              $response = new Response(json_encode(array('success'=>true)));
              $response->headers->set('Content-type', 'text/json');
            
                  
             }else{
                   $respuesta->setContent("No se actualizaron los datos.");
                   $response = new Response(json_encode(array('success'=>false)));
                   $response->headers->set('Content-type', 'text/json');
                   
             }
       }else{
           $respuesta->setContent("No se actualizaron los datos.");
           $response = new Response(json_encode(array('success'=>false)));
           $response->headers->set('Content-type', 'text/json');
       }
       
    return $response;          
       } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
           
            $respuesta->setContent($e->getMessage());
            $response = new Response(json_encode(array('success'=>false)));
            $response->headers->set('Content-type', 'text/json');
            return $response;            
        
          }
            
      }

    /**
     * Documentación para el método 'indexDevolucionAction'.
     *
     * Por medio de la funcion se podra cargar la pantalla de devoluciones
     * realizadas a un punto cliente (login)
     *
     * @return twig Html para la presentacion de las Devoluciones.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function indexDevolucionAction()
    {
        
        return $this->render('financieroBundle:Devolucion:index.html.twig');
    }
    
    /**
     * Documentación para el método 'ajaxListarDevolucionAction'.
     *
     * Por medio de la funcion obtenemos el listado de las devoluciones
     * realizadas a un punto cliente (login)
     *
     * @return json Listado de Devoluciones.
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function ajaxListarDevolucionAction()
    {
        $em = $this->get('doctrine')->getManager('telconet_financiero');
        $em_comercial = $this->get('doctrine')->getManager('telconet');
        
        $request = $this->getRequest();
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');               
        $idEmpresa=$session->get('idEmpresa');               
        $idOficina=$session->get('idOficina');               
        $filter = $request->request->get("filter");    
        $estado_post=$filter['filters'][0]['value'];
        $estado='Activo';
        $fechaDesde=explode('T',$request->get("fechaDesde"));
        $fechaHasta=explode('T',$request->get("fechaHasta"));
        $estado=$request->get("estado");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        
        if($estado)
            $estado=$estado;
        else
            $estado='Activo';
                
        if($ptocliente)
            $punto=$ptocliente['id'];
        else
            $punto="";
            
        $parametros["estado"]=$estado;
        $parametros["idEmpresa"]=$idEmpresa;
        $parametros["limit"]=$limit;
        $parametros["page"]=$page;
        $parametros["start"]=$start;
        $parametros["punto"]=$punto;
        $parametros["idOficina"]=$idOficina;
        $parametros["codigoTipoDocumento"]="DEV";
                
        if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
        {
            $parametros["feDesde"]="";
            $parametros["feHasta"]="";
            
            $resultado = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findDevolucionPorCriterios($parametros);
            $datos = $resultado['registros'];
            $total = $resultado['total'];
        }
        else
        {
            $parametros["feDesde"]=$fechaDesde[0];
            $parametros["feHasta"]=$fechaHasta[0];
            
            $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->findDevolucionPorCriterios($parametros);
            $datos = $resultado['registros'];
            $total = $resultado['total'];
        }

        $i=1;
        foreach ($datos as $datos):
            if($i % 2==0)
                    $clase='k-alt';
            else
                    $clase='';

            $urlVer = $this->generateUrl('infodocumentonotadebito_devolucion_show', array('id' => $datos->getId()));
            $linkVer = $urlVer;
            
            
            $pto_cliente=$em_comercial->getRepository('schemaBundle:InfoPunto')->find($datos->getPuntoId());
            $persona=$em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($pto_cliente->getPersonaEmpresaRolId()->getId());
            
            if($persona->getPersonaId()->getNombres()!="" && $persona->getPersonaId()->getApellidos()!="")
                $informacion_cliente=$persona->getPersonaId()->getNombres()." ".$persona->getPersonaId()->getApellidos();
            
            if($persona->getPersonaId()->getRazonSocial()!="")
                $informacion_cliente=$persona->getPersonaId()->getRazonSocial();
            
            if($datos->getEsAutomatica()=="S")
                $automatica="Si";
            else
                $automatica="No";
                
            $arreglo[]= array(
                'Numerofacturasri'=>$datos->getNumeroFacturaSri(),
                'Punto'=> $pto_cliente->getLogin(),
                'Cliente'=>$informacion_cliente,
                'Esautomatica'=>$automatica,
                'Estado'=>$datos->getEstadoImpresionFact(),
                'Fecreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
                'linkVer'=> $linkVer,
                'linkEliminar'=> "",
                'clase'=>$clase,
                'boton'=>"",
                'id'=>$datos->getId(),
            );              

            $i++;     
        endforeach;
        
        if (!empty($arreglo))
                $response = new Response(json_encode(array('total' => $total, 'documentos' => $arreglo)));
        else
        {
                $arreglo[]= array(
                        'Numerofacturasri'=> "",
                        'Punto'=> "",
                        'Cliente'=> "",
                        'Esautomatica'=> "",
                        'Estado'=> "",
                        'Fecreacion'=> "",
                        'linkVer'=> "",
                        'linkEliminar'=> "",
                        'clase'=>"",
                        'boton'=>"display:none;",
                        'id'=>""
                );
                $response = new Response(json_encode(array('total' => $total, 'documentos' => $arreglo)));
        }        
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * Documentación para el método 'newDevolucionAction'.
     *
     * Por medio de la funcion cargamos la pantalla para el ingreso de informacion
     * relacionada a la devolucion asociado al punto cliente
     *
     * @return twig Html para visualizar la informacion ingresada
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function newDevolucionAction()
    {
        $entity = new InfoDocumentoFinancieroCab();
        $form   = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        
        //informacion del pto cliente
        $request=$this->get('request');
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');
        
        $parametros=array(
        'entity' => $entity,
        'form'   => $form->createView(),
        );
        
        if($ptocliente)
        {
            $parametros['punto_id']=$ptocliente;
            $parametros['cliente']=$cliente;
        }
        $em = $this->getDoctrine()->getManager("telconet_financiero");  
        $estados=array('Pendiente');      
        $listadoPagos=$em->getRepository('schemaBundle:InfoPagoDet')->listarDetallesDePagoPorPuntoIn($ptocliente['id'],$estados);
        foreach($listadoPagos as $pago){
            $objNotaDebitoDet=$em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findNotasDeDebitoPorPagoDetIdPorEstados($pago['id'],array('Activo','Activa','Pendiente'));
            if(!$objNotaDebitoDet){
                $pagos[]=$pago;
            }
        }
        if(isset($pagos))
            $parametros['listadosPagos']=$pagos;
        else
            $parametros['listadosPagos']="";
        //busqueda del documento
        $em_general = $this->getDoctrine()->getManager();
        $listadoMotivos = $em_general->getRepository('schemaBundle:AdmiMotivo')->findMotivosPorModuloPorItemMenuPorAccion('nota_de_debito','','devolucion');
        $parametros['listadoMotivos']=$listadoMotivos;
        
        return $this->render('financieroBundle:Devolucion:new.html.twig', $parametros);
    }
    
    /**
     * Documentación para el método 'obtenerSessionAction'.
     *
     * Por medio de la funcion podemos obtener id_punto y login en session
     *
     * @param Request $objRequest
     * @return json del id_punto
     *
     * @author Hector Lozano <hlozano@telconet.ec>
     * @version 1.0 23-01-2019
     */
    public function obtenerSessionAction(Request $objRequest)
    {
        $objSession    = $objRequest->getSession();
        $objPtocliente = $objSession->get('ptoCliente');
        $strPuntoId    = $objPtocliente['id'];
        $strPuntoLogin = $objPtocliente['login'];
        
        $objResponse = new Response(json_encode(array('puntoId'=>$strPuntoId,'puntoLogin'=>$strPuntoLogin)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }   

    /**
     * Documentación para el método 'createDevolucionAction'.
     *
     * Por medio de la funcion podemos guardar la informacion correspondiente a la devolucion
     *
     * @param Request $request
     * @return twig Html para el ingreso de informacion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function createDevolucionAction(Request $request)
    {
        $entity  = new InfoDocumentoFinancieroCab();
        $form = $this->createForm(new InfoDocumentoFinancieroCabType(), $entity);
        $form->bind($request);

        $informacionGrid=$request->get('listado_devolucion');
        $informacionGrid=json_decode($informacionGrid);
        //informacion del pto cliente
        $session=$request->getSession();
        $cliente=$session->get('cliente');
        $ptocliente=$session->get('ptoCliente');
        $empresa_id=$session->get('idEmpresa');
        $oficina_id=$session->get('idOficina');
        $user=$session->get('user');
        
        $punto_id=$ptocliente['id'];
        $estado="Activo";
        
        if($punto_id)
        {
            $parametros_dev['empresa_id']=$empresa_id;
            $parametros_dev['oficina_id']=$oficina_id;
            $parametros_dev['codigoTipoDocumento']="DEV";
            $parametros_dev['informacionGrid']=$informacionGrid;
            $parametros_dev['user']=$user;
            $parametros_dev['punto_id']=$punto_id;
            $parametros_dev['estado']=$estado;
            
            $devolucion = $this->get('financiero.InfoDevolucion'); 
            //Retorna el id de la devolucion
            $entityDevolucion=$devolucion->generarDevolucion($parametros_dev);
            
            if($entityDevolucion)
            {
                /*Si se crea la entidad hacemos el envio de la notificacion*/
                $envioPlantilla = $this->get('soporte.EnvioPlantilla');                                      
                $ruta=$this->container->getParameter('host')."/financiero/documentos/nota_de_debito/".$entityDevolucion->getId()."/show_devolucion";
                echo $ruta;
                $parametros = array('numeroDevolucion' => $entityDevolucion->getNumeroFacturaSri(),              
                      'login' => $ptocliente['login'], 
                      'feEmision' => $entityDevolucion->getFeEmision(),                       
                      'ruta' =>$ruta,
                      'id'=>$entityDevolucion->getId(),
                      'empresa'=>$session->get('prefijoEmpresa')
                  );

                $envioPlantilla->generarEnvioPlantilla('Creacion de devolucion' , '' , 'FINDEV', $parametros , $empresa_id ,'','');
            }
            
            //En caso de que la entidad no se cree, se redirecciona al new, caso contrario al show
            if($entityDevolucion)
                return $this->redirect($this->generateUrl('infodocumentonotadebito_devolucion_show', array('id' => $entityDevolucion->getId())));
            else
                return $this->redirect($this->generateUrl('infodocumentonotadebito_devolucion_new'));
        }
        
        return $this->redirect($this->generateUrl('infodocumentonotadebito_devolucion_new'));
    }

    /**
     * Documentación para el método 'showDevolucionAction'.
     *
     * Por medio de la funcion podemos obtener la informacion de la devolucion
     *
     * @param integer $id
     * @return twig Html para visualizar la informacion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function showDevolucionAction($id)
    {
        $em = $this->getDoctrine()->getManager("telconet_financiero");
        
        $entity = $em->getRepository('schemaBundle:InfoDocumentoFinancieroCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Devoluciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        $oficina=$em->getRepository('schemaBundle:InfoOficinaGrupo')->find($entity->getOficinaId());
        
        $em_comercial = $this->getDoctrine()->getManager("telconet");
        $pto_cliente=$em_comercial->getRepository('schemaBundle:InfoPunto')->find($entity->getPuntoId());
        $persona=$em_comercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($pto_cliente->getPersonaEmpresaRolId()->getId());
        
        $informacion_persona['puntoId']=$pto_cliente->getLogin();
        if($persona->getPersonaId()->getNombres()!="" && $persona->getPersonaId()->getApellidos()!="")
            $informacion_persona['cliente']=$persona->getPersonaId()->getNombres()." ".$persona->getPersonaId()->getApellidos();
        
        if($persona->getPersonaId()->getRepresentanteLegal()!="")
            $informacion_persona['cliente']=$persona->getPersonaId()->getRepresentanteLegal();
            
        if($persona->getPersonaId()->getRazonSocial()!="")
            $informacion_persona['cliente']=$persona->getPersonaId()->getRazonSocial();
    
        $rolesPermitidos = array();
        
        return $this->render('financieroBundle:Devolucion:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'info_cliente' => $informacion_persona,
            'oficina'=> $oficina->getNombreOficina(),
            'rolesPermitidos' => $rolesPermitidos
            ));
    }

    /**
     * Documentación para el método 'detalleDevolucionAction'.
     *
     * Por medio de la funcion podemos obtener el detalle de la devolucion consultada
     *
     * @return json Listado detalle devolucion
     *
     * @author Gina Villalba <gvillalba@telconet.ec>
     * @version 1.0 29-05-2014
     */
    public function detalleDevolucionAction()
    {
        $request = $this->getRequest();
        $facturaid=$request->get('facturaid');
        
        $em = $this->get('doctrine')->getManager('telconet_financiero');    
        $resultado= $em->getRepository('schemaBundle:InfoDocumentoFinancieroDet')->findByDocumentoId($facturaid);
        
        if(!$resultado){
            $detalle_orden_l[] = array("motivo"=>"","observacion"=>"","valor"=>"");
        }else{
            $em_comercial = $this->get('doctrine')->getManager('telconet');    
            $detalle_orden_l = array();
        
            foreach($resultado as $factdet)
            {
                $informacion=$em_comercial->getRepository('schemaBundle:AdmiMotivo')->find($factdet->getMotivoId());
                $pago_det=$em->getRepository('schemaBundle:InfoPagoDet')->find($factdet->getPagoDetId());
                $tecn['id'] = $factdet->getId();            
                $tecn['motivo'] = $informacion->getNombreMotivo();
                $tecn['observacion'] = $factdet->getObservacionesFacturaDetalle();
                $tecn['valor'] = $factdet->getCantidad();
                $tecn['valor_total'] = $factdet->getPrecioVentaFacproDetalle();
                $tecn['numero_pago'] = $pago_det->getPagoId()->getNumeroPago();
                $detalle_orden_l[] = $tecn;
            }
        }
        
        $response = new Response(json_encode(array('listadoInformacion'=>$detalle_orden_l)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }


}
?>
