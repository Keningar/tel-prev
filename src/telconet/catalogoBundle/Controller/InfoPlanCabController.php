<?php

namespace telconet\catalogoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoPlanCab;
use telconet\schemaBundle\Entity\InfoPlanDet;
use telconet\schemaBundle\Entity\InfoPlanProductoCaract;

use telconet\schemaBundle\Form\InfoPlanCabType;
use telconet\schemaBundle\Form\InfoPlanDetType;
use telconet\schemaBundle\Form\InfoPlanProductoCaractType;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
/**
 * InfoPlanCab controller.
 *
 */
class InfoPlanCabController extends Controller
{
    /**
     * Lists all InfoPlanCab entities.
     *
     */
    public function indexAction()
    {
        //$em = $this->getDoctrine()->getManager('telconet');

        //$entities = $em->getRepository('schemaBundle:InfoPlanCab')->findAll();

        /*return $this->render('catalogoBundle:InfoPlanCab:index.html.twig', array(
            'entities' => $entities
        ));*/
        return $this->render('catalogoBundle:InfoPlanCab:index.html.twig');
        
	}
	
     /**
     * Documentación para el método 'listadoPlanesAction'.
     * 
     * Método que obtiene un listado de planes.
     * 
     * 
     * @return Response Listado detallado de planes.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 17-01-2017 - Se realiza modificación para obtener el listado de planes y por su nombre.
     *  
     */
	public function listadoPlanesAction()
	{
        $request        = $this->getRequest();		    
		$filter         = $request->request->get("filter");    
		$estado_post    = $filter['filters'][0]['value'];
		
		$fechaDesde     = explode('T',$request->get("fechaDesde"));
		$fechaHasta     = explode('T',$request->get("fechaHasta"));
		$strEstado      = $request->get("estado");
		$strEstado      = $request->get("estado");
		$intLimit       = $request->get("limit");
        $strPage        = $request->get("page");
        $intStart       = $request->get("start");
        $strNombrePlan  = $request->get("strNombrePlan");
        $arrayParametros= array();
        
		$em = $this->getDoctrine()->getManager('telconet');
		$session  = $request->getSession();
		$intIdEmpresa = $session->get('idEmpresa');
		
        $em = $this->get('doctrine')->getManager('telconet');
		
		if($strEstado)
			$strEstado=$strEstado;
		else
			$strEstado='Activo';
 
		if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
		{
            $arrayParametros['strEstado']       = $strEstado;
            $arrayParametros['intIdEmpresa']    = $intIdEmpresa;
            $arrayParametros['intLimit']        = $intLimit;
            $arrayParametros['intStart']        = $intStart;
            $arrayParametros['strNombrePlan']   = $strNombrePlan;
            
			$resultado = $em->getRepository('schemaBundle:InfoPlanCab')->find30PlanesPorEmpresaPorEstado( $arrayParametros );
			$datos = $resultado['registros'];
			$total = $resultado['total'];
		}
		else
		{
			$resultado= $em->getRepository('schemaBundle:InfoPlanCab')->findPlanesPorCriterios($strEstado,$intIdEmpresa,$fechaDesde[0],$fechaHasta[0],$intLimit, $strPage, $intStart);
			$datos = $resultado['registros'];
			$total = $resultado['total'];
		}
		
		
		$i=1;
		foreach ($datos as $datos):
				if($i % 2==0)
					$clase='k-alt';
				else
					$clase='';
					
				$urlVer = $this->generateUrl('infoplancab_show', array('id' => $datos->getId()));
				$urlEditar = $this->generateUrl('infoplancab_edit', array('id' => $datos->getId()));
				$ulrEliminar= $this->generateUrl('infoplancab_delete', array('id' => $datos->getId()));
						
				$linkVer = $urlVer;
				$linkEliminar= $ulrEliminar;
				$linkEditar = $urlEditar;
				
				$arreglo[]= array(
				'IdPlan'=> $datos->getId(),
				'Codigo'=> $datos->getCodigoPlan(),
				'Nombre'=> $datos->getNombrePlan(),
				'Descripcion'=> $datos->getDescripcionPlan(),
				'Fecreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
				'estado'=> $datos->getEstado(),
				'linkVer'=> $linkVer,
				'linkEditar'=> $linkEditar,
				'linkEliminar'=> $linkEliminar,
                 );             
                 
                 $i++;     
		endforeach;
		if (!empty($arreglo))
			$response = new Response(json_encode(array('total' => $total, 'arreglo' => $arreglo)));
		else
		{
			$arreglo[]= array(
				'IdPlan'=> "",
				'Codigo'=> "",
				'Nombre'=> "",
				'Descripcion'=> "",
				'Fecreacion'=> "",
				'estado'=> "",
				'linkVer'=> "",
				'linkEditar'=> "",
				'linkEliminar'=> ""
			);
			$response = new Response(json_encode(array('total' => 0, 'arreglo' => $arreglo)));
		}
		
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    }

    /**
     * Finds and displays a InfoPlanCab entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager('telconet');

        $entity = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        $items = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,"Activo");
		
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

		if($items)
		{
			$i=0;
			foreach($items as $item):
				$producto = $em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
				$arreglo[$i]['producto']=$producto->getDescripcionProducto();
				$arreglo[$i]['cantidad']=$item->getCantidadDetalle();
				$arreglo[$i]['precio']=$item->getPrecioItem();
				$i++;
				//caracteristicas
				$caracteristicasDet = $em->getRepository('schemaBundle:InfoPlanDet')->getCaracteristicas($item->getId());
				foreach($caracteristicasDet as $caracteristica){
				    $caract['nombre'] = $caracteristica['nombre'];
				    $caract['valor'] = $caracteristica['valor'];
				    $caract['estado'] = $caracteristica['estado'];
				    $arregloCaracteristicas[] = $caract;
				}
			endforeach;
		}
		else
			$arreglo="";
		
		$parametros=array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            );
            
		if($arreglo!="")
			$parametros['items']=$arreglo;
		if($arregloCaracteristicas!="")
			$parametros['caracteristicas']=$arregloCaracteristicas;
            
        return $this->render('catalogoBundle:InfoPlanCab:show.html.twig', $parametros);
    }

    /**
     * Displays a form to create a new InfoPlanCab entity.
     *
     */
    public function newAction()
    {
        $entity = new InfoPlanCab();
        $form   = $this->createForm(new InfoPlanCabType(), $entity);
        
        $listado_detalle=array(""=>"Seleccione","P"=>"Paquete","S"=>"Solucion");

        return $this->render('catalogoBundle:InfoPlanCab:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'listado_detalle'=>$listado_detalle,
        ));
    }

    /**
     * Creates a new InfoPlanCab entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new InfoPlanCab();
        $request = $this->getRequest();
        $form    = $this->createForm(new InfoPlanCabType(), $entity);
        $form->bind($request);
        
        $datos=$request->get('valores');
        $valores=json_decode($datos);	
        //print_r($valores);	
        //die();
        
        if ($form->isValid()) {
            $estado = 'Activo';
			$session  = $request->getSession();
			$idEmpresa = $session->get('idEmpresa');
            //$empresa="10";
            $em = $this->getDoctrine()->getManager();
            $entity->setFeCreacion(new \DateTime('now'));
            $usrCreacion = $request->getSession()->get('user');
            $entity->setUsrCreacion($usrCreacion);
            $entity->setIpCreacion($request->getClientIp());			
            $entity->setEstado($estado);
            $entity->setEmpresaCod($idEmpresa);
            $entity->setIva("S");
            $em->persist($entity);
            $em->flush();
            
            foreach($valores as $valor):
                    $id=$valor->producto;
                    $cantidad=$valor->cantidad;
                    $precio=$valor->precio_total;
                    $prod_caract=$valor->prod_caract;
                    $valor_caract=$valor->valor_caract;

                    $producto=$em->getRepository('schemaBundle:AdmiProducto')->findOneById($id);
                    //print_r($producto);
                    //echo $producto->getCodigoProducto();

                    $entityproducto  = new InfoPlanDet();	
                    $entityproducto->setProductoId($id);
                    $entityproducto->setCantidadDetalle($cantidad);
                    $entityproducto->setPrecioItem($precio);
                    $entityproducto->setCostoItem($precio);
                    $entityproducto->setDescuentoItem(0);
                    $entityproducto->setEstado("Activo");	
                    $entityproducto->setPlanId($entity);
                    $usrCreacion = $request->getSession()->get('user');
                    $entityproducto->setUsrCreacion($usrCreacion);	
                    $entityproducto->setIpCreacion($request->getClientIp());			
                    //$entityproducto->setCodigoItem($producto->getCodigoProducto());			
                    //$entityproducto->setNombreItem($producto->getDescripcionProducto());
                    $entityproducto->setFeCreacion(new \DateTime('now'));	
                    $em->persist($entityproducto);
                    $em->flush();
                    
                    //print_r($prod_caract);
                    
                    for($i=0;$i<sizeof($prod_caract);$i++)
                    {
                        //print_r($prod_caract[$i]);
                        //Guardar informacion de la caracteristica del producto
                        $entityplanproductocaract  = new InfoPlanProductoCaract();	
                        $entityplanproductocaract->setPlanDetId($entityproducto->getId());
                        $entityplanproductocaract->setProductoCaracterisiticaId($prod_caract[$i]);
                        $entityplanproductocaract->setValor($valor_caract[$i]);
                        $entityplanproductocaract->setEstado("Activo");	
                        $usrCreacion = $request->getSession()->get('user');
                        $entityplanproductocaract->setUsrCreacion($usrCreacion);	
                        $entityplanproductocaract->setFeCreacion(new \DateTime('now'));	
                        $em->persist($entityplanproductocaract);
                        $em->flush();
                    }
            endforeach;
            
            
	    //die();	
			
            return $this->redirect($this->generateUrl('infoplancab_show', array('id' => $entity->getId())));
            
        }

		/*
        return $this->render('catalogoBundle:InfoPlanCab:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView()
        ));
        */
    }

    /**
     * Displays a form to edit an existing InfoPlanCab entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        
        $listado_detalle=array(""=>"Seleccione","P"=>"Paquete","S"=>"Solucion");

        //obtener listado de items ya ingresados
        $estado="Activo";
        $items=$em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$estado);
        if($items)
        {
            $i=0;
            foreach($items as $item)
            {
                $producto=$em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
                $plandet[$i]['producto']=$producto->getDescripcionProducto();
                $plandet[$i]['cantidad']=$item->getCantidadDetalle();
                $plandet[$i]['precio_total']=$item->getPrecioItem();
                $plandet[$i]['producto_id']=$producto->getId();
                $i++;
                
                
                $info_detalle[] = array('producto' =>$producto->getId(),
                                        'cantidad' => $item->getCantidadDetalle(),
                                        'precio_total' => $item->getPrecioItem(),
                                        'id_det'=>$item->getId());
                
            }
            
            //$obj_item = (object)$plandet;
        }
        
        $arreglo_encode= json_encode($info_detalle);
        
       //print_r($arreglo_encode);
        //die();
        
        $editForm = $this->createForm(new InfoPlanCabType(), $entity);
        $deleteForm = $this->createDeleteForm($id);
        
        return $this->render('catalogoBundle:InfoPlanCab:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'listado_detalle'=>$listado_detalle,
            'items_detalle'=>$plandet,
            'arreglo'=>$arreglo_encode
        ));
    }

    /**
     * Edits an existing InfoPlanCab entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
	
        $datos=$request->get('valores');
        $valores=json_decode($datos);	
        
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }

        $editForm   = $this->createForm(new InfoPlanCabType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('infoplancab_edit', array('id' => $id)));
            $items=$em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($id);
            //print_r($valores);	
            //die();
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
                if($id_det=="")
                {
                    $prod_caract=$valor->prod_caract;
                    $valor_caract=$valor->valor_caract;
                }

                //echo $id_det;

                if($id_det=="")
                {
                    $producto=$em->getRepository('schemaBundle:AdmiProducto')->findOneById($id_producto);
                    //print_r($producto);
                    //echo $producto->getCodigoProducto();

                    $entityproducto  = new InfoPlanDet();	
                    $entityproducto->setProductoId($id_producto);
                    $entityproducto->setCantidadDetalle($cantidad);
                    $entityproducto->setPrecioItem($precio);
                    $entityproducto->setCostoItem($precio);
                    $entityproducto->setDescuentoItem($entity->getDescuentoPlan());
                    $entityproducto->setEstado("Activo");	
                    $entityproducto->setPlanId($entity);
                    $usrCreacion = $request->getSession()->get('user');
                    $entityproducto->setUsrCreacion($usrCreacion);	
                    $entityproducto->setIpCreacion($request->getClientIp());			
                    //$entityproducto->setCodigoItem($producto->getCodigoProducto());			
                    //$entityproducto->setNombreItem($producto->getDescripcionProducto());
                    $entityproducto->setFeCreacion(new \DateTime('now'));	
                    $em->persist($entityproducto);
                    $em->flush();

                    //print_r($prod_caract);

                    for($i=0;$i<sizeof($prod_caract);$i++)
                    {
                        //print_r($prod_caract[$i]);
                        //Guardar informacion de la caracteristica del producto
                        $entityplanproductocaract  = new InfoPlanProductoCaract();	
                        $entityplanproductocaract->setPlanDetId($entityproducto->getId());
                        $entityplanproductocaract->setProductoCaracterisiticaId($prod_caract[$i]);
                        $entityplanproductocaract->setValor($valor_caract[$i]);
                        $entityplanproductocaract->setEstado("Activo");	
                        $usrCreacion = $request->getSession()->get('user');
                        $entityplanproductocaract->setUsrCreacion($usrCreacion);	
                        $entityplanproductocaract->setFeCreacion(new \DateTime('now'));	
                        $em->persist($entityplanproductocaract);
                        $em->flush();
                    }
                }
            endforeach;

            /*return $this->render('catalogoBundle:InfoPlanCab:edit.html.twig', array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));*/

            return $this->redirect($this->generateUrl('infoplancab_edit', array('id' => $id)));
        }
    }
	
	public function actualizarAction()
	{
        $request = $this->get('request');
		$request->isXmlHttpRequest(); // is it an Ajax request?
		$request->getPreferredLanguage(array('en', 'fr'));
		$datos=$request->get('productos'); // get a $_GET parameter
		$array_datos=explode (",",$datos);
		//$session  = $request->getSession();
		//$user = $this->get('security.context')->getToken()->getUser();
		$a=0;
		$x=0;
		for($i=0;$i<count($array_datos);$i++){
			if($a==4)
			{
				$a=0;
				$x++;
			}
			if($a==0)
				$productos[$x]['Nombre']=$array_datos[$i];
			if($a==1)
				$productos[$x]['Cantidad']=$array_datos[$i];
			if($a==2)
				$productos[$x]['Precio']=$array_datos[$i];
			if($a==3)
				$productos[$x]['Descuento']=$array_datos[$i];
			$a++;
		} 
		//print_r($telefonos);
		$em = $this->getDoctrine()->getManager('telconet');
		//$em->getConnection()->beginTransaction();
		//try{			
			//$entity = new InfoPlanCab();
			$entity  = $em->getRepository('schemaBundle:InfoPlanCab')->find($request->get('idPlan'));
			$entity->setNombrePlan($request->get('nombre'));
			$entity->setDescripcionPlan($request->get('descripcion'));
			$entity->setCodigoPlan($request->get('codigo'));
			$entity->setFrecuenciaPlan($request->get('frecuencia'));
			$entity->setTipoItem("PL");
			$entity->setMultiplesPrecios("N");
			$empresa=$em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByNombreEmpresa("Telconet");
			$entity->setEmpresaId($empresa->getId());
			$promocion="";
			if($request->get('aplicaPromocion')=="si")
				$promocion="S";
			if($request->get('aplicaPromocion')=="no")
				$promocion="N";
			$entity->setAplicaPromocion($promocion);
			$entity->setDescuentoPlan($request->get('descuento'));
			//Para poner las vigencias
			$start=$request->get('fechaInicio');
			$end=$request->get('fechaFin');
			$start_exp=explode("/",$start);
			$end_exp=explode("/",$end);
			/*$fecha_inicio_promocion=date("Y-m-d", strtotime($start_exp[2]."-".$start_exp[0]."-".$start_exp[1]));
			$fecha_fin_promocion=date("Y-m-d", strtotime($end_exp[2]."-".$end_exp[0]."-".$end_exp[1]));
			$entity->setFechaHoraVigenciaInicio("20-01-2012");
			$entity->setFechaHoraVigenciaFin("20-01-2012");*/
			$entity->setFeCreacion(new \DateTime('now'));
			$usrCreacion = $request->getSession()->get('user');
			$entity->setUsrCreacion($usrCreacion);	
			$entity->setIpCreacion($request->getClientIp());			
			$entity->setEstado("Activo");
			$em->persist($entity);
	        $em->flush();
			
			//print_r($entity);
			$listadoDetalle = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($request->get('idPlan'));
			if($listadoDetalle)
			{
				foreach($listadoDetalle as $detalle){
					$detallePlanAnular=$em->getRepository('schemaBundle:InfoPlanDet')->find($detalle->getId());
					$detallePlanAnular->setEstado("Inactivo");
					$em->persist($detallePlanAnular);
					$em->flush();	
				}
			}	
			//GRABAR DETALLES
			for($i=0;$i<count($productos);$i++)
			{
				$producto=$em->getRepository('schemaBundle:AdmiProducto')->findOneByDescripcionProducto($productos[$i]['Nombre']);
				//print_r($producto);
				$entityproducto  = new InfoPlanDet();	
				$entityproducto->setProductoId($producto);
				$entityproducto->setCantidadDetalle($productos[$i]['Cantidad']);
				$entityproducto->setPrecioItem($productos[$i]['Precio']);
				$entityproducto->setCostoItem($productos[$i]['Precio']);
				$entityproducto->setDescuentoItem($productos[$i]['Descuento']);
				$entityproducto->setEstado("Activo");	
				$entityproducto->setPlanId($entity);
				$usrCreacion = $request->getSession()->get('user');
				$entityproducto->setUsrCreacion($usrCreacion);	
				$entityproducto->setIpCreacion($request->getClientIp());			
				$entityproducto->setCodigoItem($producto->getCodigoProducto());			
				$entityproducto->setNombreItem($producto->getDescripcionProducto());
				$entityproducto->setFeCreacion(new \DateTime('now'));	
				$em->persist($entityproducto);
				$em->flush();		
			}
		/*	$em->getConnection()->commit();	
            
			
		}
		catch (\Exception $e) {
                // Rollback the failed transaction attempt
                $em->getConnection()->rollback();
                $em->getConnection()->close();
                //aqu? alg?n mensaje con la excepci?n concatenada     
		}*/
		$response = new Response(json_encode(array('msg'=>'ok','id'=>$entity->getId())));
		$response->headers->set('Content-type', 'text/json');
		return $response;
    }

    /**
     * Deletes a InfoPlanCab entity.
     *
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('infoplancab'));
    }
    
    public function listarProductosAction()
    {
        //aki mi codigo json
        $request = $this->getRequest();		    
        $tipo_plan = $request->request->get("tipo_plan");  
        $em = $this->get('doctrine')->getManager('telconet'); 
		$session  = $request->getSession();
		$empresaId = $session->get('idEmpresa');
        //$empresaId="10";
        $estado="Activo";
        if($tipo_plan=="Paquete")
            $productos = $em->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYEstado($empresaId,$estado);
        else
            $productos = $em->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYEstado("",$estado);
        
        if(!$productos){
                $arreglo=array('msg'=>'No existen datos');
        }else{
                $variable_combo="<option>Seleccione</option>";
                foreach($productos as $producto){
                    $variable_combo.="<option value='".$producto->getId()."-".$producto->getDescripcionProducto()."'>".$producto->getDescripcionProducto()."</option>";
                }
                $arreglo=array('msg'=>'ok','div'=>$variable_combo);
        }
        
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;
    }
    
    public function listarProductosExistentesAction()
    {
		$request = $this->getRequest();		    
		$idPlan = $request->request->get("idPlan"); 
		$em = $this->get('doctrine')->getManager('telconet');   
		$items = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($idPlan,"Activo");
		
		if(!$items)
		{
			$arr_items[] = array("ProductoId"=>"","Nombre"=>"","Cantidad"=>"","Precio"=>"","Descuento"=>"");
		}
		else
		{
			
			foreach($items as $items){
				$item['ProductoId'] = $items->getProductoId();
				$item['Nombre'] = $items->getNombreItem();
				$item['Cantidad'] = $items->getCantidadDetalle();
				$item['Precio'] = $items->getPrecioItem();
				$item['Descuento'] = $items->getDescuentoItem();
				$arr_items[] = $item;
			}
		}
		$response = new Response(json_encode($arr_items));
		$response->headers->set('Content-type', 'text/json');		
		return $response;
	}
    
    public function listarProductosTAction()
    {
        //aki mi codigo json
        //filter[filters][0][value]
        $em = $this->getDoctrine()->getManager();

        //$datos = $em->getRepository('schemaBundle:AdmiProducto')->findAllXNombre($producto_nome);
        $datos = $em->getRepository('schemaBundle:AdmiProducto')->findAll();
        
        foreach ($datos as $datos):
				$arreglo[]= array(
				"ProductID"=> $datos->getId(),
				"ProductName"=> $datos->getDescripcionProducto(),
				"UnitPrice"=> 10,
				"Discontinued"=> false,
				"UnitsInStock"=> 1,
                 );             
		endforeach;
		if (!empty($arreglo))
			$response = new Response(json_encode($arreglo));
		else
		{
			$arreglo[]= array(
				"ProductID"=> "",
				"ProductName"=> "",
				"UnitPrice"=> "",
				"Discontinued"=> "",
				"UnitsInStock"=> "",
			);
			$response = new Response(json_encode($arreglo));
		}
		
		//print_r($response);
		
		$response->headers->set('Content-type', 'text/json');
		//die();
		//$this->get('request')->setHttpHeader('Content-type', 'text/json');
		return $response;
    }
    
    public function listarDetallesAction()
    {
        //aki mi codigo json
				
		$arreglo[]= array(
		'ProductID'=> "123",
		'ProductName'=> "xxx",
		'UnitPrice'=> "12",
		 );             
		 
		$response = new Response(json_encode($arreglo));
		
		//print_r($response);
		
		$response->headers->set('Content-type', 'text/json');
		//die();
		//$this->get('request')->setHttpHeader('Content-type', 'text/json');
		return $response;
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    public function listarCaracteristicasPorProductoAction()
    {
        $request = $this->getRequest();		    
        $productoId = $request->request->get("producto");  
        $em = $this->get('doctrine')->getManager('telconet');   
        //$items = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoId($productoId);
        $estado="Activo";
        $items = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($productoId,$estado);
        $producto = $em->getRepository('schemaBundle:AdmiProducto')->find($productoId);
        //echo $producto->getFuncionPrecio();
        //die();
        if($producto->getFuncionPrecio()!="")
        {
            $presentar_div="";
            if($items)
            {
                $presentar_div="<table class='formulario'>";
                $presentar_div.="<tr><td>Funcion precio:";
                $presentar_div.=$producto->getFuncionPrecio()."</td></tr>";
                $presentar_div.="<tr><td>Cantidad:";
                $presentar_div.="<input type='text' value='' name='cantidad' id='cantidad' onkeypress='validate(event)'/></td></tr>";
                foreach ($items as $item):
                    $presentar_div.="<tr><td>";
                    $presentar_div.=$item->getCaracteristicaId()->getDescripcionCaracteristica().": <input type='text' value='' name='caracteristicas[]' id='caracteristicas'/>";
                    $presentar_div.="<input type='hidden' value='[".$item->getCaracteristicaId()->getDescripcionCaracteristica()."]' name='caracteristica_nombre[]' id='caracteristica_nombre'/>";
                    $presentar_div.="<input type='hidden' value='".$item->getId()."' name='producto_caracteristica[]' id='producto_caracteristica'/>";
                    $presentar_div.="</td></tr>";
                endforeach;
                $presentar_div.="</table>";
                $presentar_div.="<button type='button' class='button-crud' onClick='agregar_detalle();'>Agregar</button>";
                $presentar_div.=" ";
                $presentar_div.="<button type='button' class='button-crud' onClick='limpiar_detalle();'>Limpiar</button>";
                $presentar_div.="<input type='hidden' value='".$producto->getFuncionPrecio()."' name='funcion_precio' id='funcion_precio'/>";
                $arreglo=array('msg'=>'ok','div'=>$presentar_div);
            }
            else {
                $arreglo=array('msg'=>'No existen registro');
            }
        }
        else
            $arreglo=array('msg'=>'Funcion precio no esta definida para este producto');
        
        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');		
        return $response;
        
        
    }
    
    /*combo estado llenado ajax*/
    public function estadosAction()
    {
		/*Modificacion a utilizacion de estados por modulos*/
		
		$arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
		$arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'ACT','descripcion'=> 'Inactivo');                
		$arreglo[]= array('idEstado'=>'Convertido','codigo'=> 'ACT','descripcion'=> 'Convertido');
		
		$response = new Response(json_encode(array('estados'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;
		
    }
    
    public function deleteAjaxAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion = $this->get('request');
        $parametro = $peticion->get('id');
        if(isset($parametro))
			$array_valor = explode("|",$parametro);
		else
		{
			$parametro = $peticion->get('param');
			$array_valor = explode("|",$parametro);
		}
        //print_r($array_valor);
        $em = $this->getDoctrine()->getManager();
        foreach($array_valor as $id):
            //echo $id;
            $entity=$em->getRepository('schemaBundle:InfoPlanCab')->find($id);
            if($entity){
                $entity->setEstado("Inactivo");
                $em->persist($entity);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
                
                
                $entitydet=$em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($id);
                if(isset($entitydet))
                {
					foreach($entitydet as $det)
					{
						$det->setEstado("Inactivo");
						$em->persist($det);
						$em->flush();
					}
				}
            }
            else
                $respuesta->setContent("No existe el registro");
        endforeach;
        //die();
        return $respuesta;
    }
    
    
}
