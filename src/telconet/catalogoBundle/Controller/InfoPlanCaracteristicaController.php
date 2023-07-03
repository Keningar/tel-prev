<?php

namespace telconet\catalogoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoPlanCab;
use telconet\schemaBundle\Entity\InfoPlanDet;
use telconet\schemaBundle\Entity\InfoPlanProductoCaract;
use telconet\schemaBundle\Entity\InfoPlanCaracteristica;
use telconet\schemaBundle\Entity\InfoPlanCondicion;

use telconet\schemaBundle\Form\InfoPlanCabType;
//use telconet\schemaBundle\Form\InfoPlanCondicionType;
use telconet\schemaBundle\Form\InfoPlanCaracteristicaType;
use telconet\schemaBundle\Entity\InfoPlanHistorial;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * InfoPlanCaracteristica controller.
 *
 */
class InfoPlanCaracteristicaController extends Controller
{
    /**
     * Lists all InfoPlanCaracteristica entities.
     *
     */
    public function indexAction(){
        return $this->render('catalogoBundle:InfoPlanCaracteristica:index.html.twig');
    }
   /**
    * Funcion en Ajax que lista los planes existentes en el catalogo
    * @author : telcos
    * @version 1.0 23-05-2014
    * @param date $fechaDesde
    * @param date $fechaHasta
    * @param string $estado
    * @param string $nombre
    * @param integer $limit
    * @param integer $page
    * @param integer $start
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function listadoPlanesAction(){
    $request        = $this->getRequest();
	$filter         = $request->request->get("filter");
	$estado_post    = $filter['filters'][0]['value'];
	$fechaDesde     = explode('T',$request->get("fechaDesde"));
	$fechaHasta     = explode('T',$request->get("fechaHasta"));
	$estado         = $request->get("estado");
	$nombre         = $request->get("nombre");
	$limit          = $request->get("limit");
    $page           = $request->get("page");
    $start          = $request->get("start");
    $em             = $this->getDoctrine()->getManager('telconet');
	$session        = $request->getSession();
	$idEmpresa      = $session->get('idEmpresa');
    $arrayParametros= array();

        $em = $this->get('doctrine')->getManager('telconet');

	if(empty($estado))
    {
        $estado = 'Activo';
    }

   	if ( ( !$fechaDesde[0] ) && ( !$fechaHasta[0] ) && !$nombre )
	{
        $arrayParametros['strEstado']       = $estado;
        $arrayParametros['intIdEmpresa']    = $idEmpresa;
        $arrayParametros['intLimit']        = $limit;
        $arrayParametros['intStart']        = $start;
        $arrayParametros['strNombrePlan']   = NULL;

        //Cuando sea inicio puedo sacar los 30 registros
	    $resultado = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->find30PlanesPorEmpresaPorEstado($arrayParametros);
	    $datos     = $resultado['registros'];
	    $total     = $resultado['total'];
        }
	else
	{
	    $resultado = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findPlanesPorCriterios($estado,$idEmpresa,$fechaDesde[0],$fechaHasta[0],$nombre,$limit, $page, $start);
	    $datos     = $resultado['registros'];
	    $total     = $resultado['total'];
	}


	$i=1;
	foreach ( $datos as $datos )
        {
	    if( $i % 2 == 0 )
		$clase = 'k-alt';
	    else
		$clase = '';

	    $urlVer              = $this->generateUrl('infoplancaracteristicas_show', array('id' => $datos->getId()));
            $urlEditar           = $this->generateUrl('infoplancaracteristicas_edit', array('id' => $datos->getId()));
            $urlCaracteristicas  = $this->generateUrl('infoplancaracteristicas_show_caract_plan', array('id' => $datos->getId()));
	    $ulrEliminar         = $this->generateUrl('infoplancaracteristicas_delete', array('id' => $datos->getId()));
	    $ulrCondiciones      = $this->generateUrl('infoplancaracteristicas_condiciones', array('id' => $datos->getId()));
            $ulrClonar           = $this->generateUrl('infoplancaracteristicas_clonar', array('id' => $datos->getId()));
            $ulrActivar          = $this->generateUrl('infoplancaracteristicas_activar', array('id' => $datos->getId()));
            $ulrReactivar        = $this->generateUrl('infoplancaracteristicas_reactivar', array('id' => $datos->getId()));

	    $linkVer             = $urlVer;
	    $linkEliminar        = $ulrEliminar;
	    $linkEditar          = $urlEditar;
	    $linkCaracteristicas = $urlCaracteristicas;
            $linkCondiciones     = $ulrCondiciones;
            $linkClonar          = $ulrClonar;
            $linkActivar         = $ulrActivar;
            $linkReactivar       = $ulrReactivar;

	    $arreglo[]= array(
	                        'IdPlan'              => $datos->getId(),
				'Codigo'              => $datos->getCodigoPlan(),
				'Nombre'              => $datos->getNombrePlan(),
				'Descripcion'         => $datos->getDescripcionPlan(),
				'Fecreacion'          => strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
				'estado'              => $datos->getEstado(),
				'linkVer'             => $linkVer,
				'linkEditar'          => $linkEditar,
                                'linkCaracteristicas' => $linkCaracteristicas,
                                'linkCondiciones'     => $linkCondiciones,
                                'linkClonar'          => $linkClonar,
                                'linkActivar'         => $linkActivar,
                                'linkReactivar'       => $linkReactivar,
                                'linkEliminar'        => $linkEliminar
                 );

            $i++;
        }
	if ( !empty($arreglo) )
	    $response = new Response(json_encode(array('total' => $total, 'arreglo' => $arreglo)));
	else
	{
	    $arreglo[]= array(
				'IdPlan'              => "",
				'Codigo'              => "",
				'Nombre'              => "",
				'Descripcion'         => "",
				'Fecreacion'          => "",
				'estado'              => "",
				'linkVer'             => "",
				'linkEditar'          => "",
                                'linkCaracteristicas' => "",
                                'linkCondiciones'     => "",
                                'linkClonar'          => "",
                                'linkActivar'         => "",
                                'linkReactivar'         => "",
                                'linkEliminar'        => "",
			);
	    $response = new Response(json_encode(array('total' => 0, 'arreglo' => $arreglo)));
	}

	$response->headers->set('Content-type', 'text/json');
	return $response;
    }

   /**
    * Funcion que muestra Show de Planes con sus productos y caracteristicas
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-05-2014
    * @param integer $id   //Id del plan
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return Renders a view.
    */
    public function showAction($id)
    {
        $em                         = $this->getDoctrine()->getManager('telconet');
        $entity                     = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
        $items                      = $em->getRepository('schemaBundle:InfoPlanDet')->getPlanIdYEstados($id);
        $arregloCaracteristicasPlan = array();
        $arregloCaracteristicasDet  = array();
        $arregloCondicionesPlan     = array();
        $objPlanProvieneRegClonado  = null;
        $strEstadoInactivo          = "Inactivo";
        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        else
        {
            if( $entity->getEstado() == "Inactivo" )
            {
                $items = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$strEstadoInactivo);
            }
            //Busco el plan del que proviene si se trata de un plan clonado
            if($entity && $entity->getPlanId())
            {
                $objPlanProvieneRegClonado = $em->getRepository('schemaBundle:InfoPlanCab')->find($entity->getPlanId());
            }
            //Leo Caracteristicas promocionales del plan
            $caracteristicasPlan = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->getCaracteristicasPlan($id);
            foreach( $caracteristicasPlan as $caracteristica )
            {
                $caractPlan['nombre']         = $caracteristica['nombre'];
		$caractPlan['valor']          = $caracteristica['valor'];
		$caractPlan['estado']         = $caracteristica['estado'];
		$arregloCaracteristicasPlan[] = $caractPlan;
	    }
            //Leo Condiciones del Plan
            $condicionesPlan = $em->getRepository('schemaBundle:InfoPlanCondicion')->getCondicionesPlanXPlan($id);
	    foreach( $condicionesPlan as $condiciones )
            {
                $condicPlan['tipoNegocio'] = $condiciones['nombre_tipo_negocio'];
                $condicPlan['formaPago']   = $condiciones['descripcion_forma_pago'];
                $condicPlan['tipoCuenta']  = $condiciones['descripcion_cuenta'];
                $condicPlan['banco']       = $condiciones['descripcion_banco'];
                $arregloCondicionesPlan[]  = $condicPlan;
            }
        }

        $deleteForm = $this->createDeleteForm($id);

	if( $items )
	{
            $i = 0;
            //Leo productos
            foreach( $items as $item )
            {
                 //Productos por planes
                $producto                   = $em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
                $arreglo[$i]['idproducto']  = $producto->getId();
                $arreglo[$i]['producto']    = $producto->getDescripcionProducto();
                $arreglo[$i]['instalacion'] = $producto->getInstalacion();
                $arreglo[$i]['cantidad']    = $item->getCantidadDetalle();
                $arreglo[$i]['precio']      = $item->getPrecioItem();
                $i++;
                //caracteristicas por productos
                $caracteristicasDet = $em->getRepository('schemaBundle:InfoPlanDet')->getCaracteristicas($item->getId());
                foreach( $caracteristicasDet as $caracteristica )
                {
                    $caractDet['idproducto']     = $producto->getId();
                    $caractDet['nombre']         = $caracteristica['nombre'];
                    $caractDet['valor']          = $caracteristica['valor'];
                    $caractDet['estado']         = $caracteristica['estado'];
                    $arregloCaracteristicasDet[] = $caractDet;
                }
            }
       }
       else
       {
            $arreglo = "";
       }
       $parametros = array( 'entity'      => $entity,
                             'delete_form' => $deleteForm->createView(),
                         );

       $parametros['objPlanProvieneRegClonado'] = $objPlanProvieneRegClonado;
       if( $arreglo!= "" )
           $parametros['items'] = $arreglo;
       if( $arregloCaracteristicasDet!= "" )
           $parametros['caracteristicasDet'] = $arregloCaracteristicasDet;
       if( $arregloCaracteristicasPlan!= "" )
           $parametros['caracteristicasPlan'] = $arregloCaracteristicasPlan;
       if( $arregloCondicionesPlan!= "" )
           $parametros['condicionesPlan'] = $arregloCondicionesPlan;

       return $this->render('catalogoBundle:InfoPlanCaracteristica:show.html.twig', $parametros);
    }

   /**
    * Funcion para el ingreso de Nuevo Plan
    * @author : telcos
    * @version 1.0 23-05-2014
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 04-08-2021 - Se agrega modificación y se obtiene los valores de detalles de parámetros (BASICO y COMERCIAL)
    *                           para el combo de tipo categoría del plan al crear un nuevo plan para la empresa MD.
    *                           Se envía en el arreglo el prefijo de la empresa en sesión para validaciones.
    * 
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return Renders a view.
    */
    public function newAction()
    {
        $em           = $this->getDoctrine()->getManager('telconet');
        $peticion     = $this->get('request');
        $session      = $peticion->getSession();
        $empresaId    = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get("prefijoEmpresa") ? $session->get("prefijoEmpresa") : '';
        $arreglo_tipo = array();
        $listado_tipo = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findTiposNegocioPorEmpresa($empresaId);

        foreach ( $listado_tipo as $listado_tipo )
        {
          $arreglo_tipo[$listado_tipo->getNombreTipoNegocio()] = $listado_tipo->getNombreTipoNegocio();
        }

        $entity          = new InfoPlanCab();
        $form            = $this->createForm(new InfoPlanCabType(array('empresaId'=>$empresaId)), $entity);
        $listado_detalle = array(""=>"Seleccione","P"=>"Paquete","S"=>"Solucion");
        //codigo interno en base a la fecha de creacion sysdate formato yyyymm
        $dateFecha        = date("Y-m-d");
        $arrayFechaExp    = explode("-",$dateFecha);
        $strCodigoInterno = date ("Ym",strtotime($arrayFechaExp[0]."".$arrayFechaExp[1]));
        
        //Se agrega validación para enviar los valores de tipo categoría del plan.
        $arrayParamTipoPlan = array();
        $strCategoriaPorDefecto= "";
        if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN') && !empty($strPrefijoEmpresa))
        {    
            $arrayParamTipoPlanes = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get("PARAM_FLUJO_ADULTO_MAYOR", "COMERCIAL", "", 
                                                          "CATEGORIA_PLAN_ADULTO_MAYOR", "", "", "", "", "",
                                                          $empresaId);
            
            foreach($arrayParamTipoPlanes as $objParamTipoPlanes)
            {
                $arrayParamTipoPlan[$objParamTipoPlanes['valor1']] = $objParamTipoPlanes['valor1'];
                
                if($objParamTipoPlanes['valor2'] == 'default')
                {
                    $strCategoriaPorDefecto = $objParamTipoPlanes['valor1'];  
                }
            } 
        }
        $emGeneral                      = $this->get('doctrine')->getManager('telconet_general');
        $arrayParamEcucert  = array(
            'nombreParametro' => "VARIABLES_VELOCIDAD_PLANES",
            'estado'          => "Activo"
        );
        $entityParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
        ->findOneByNombreParametro($arrayParamEcucert);

        $intIdParametrosVeloc = 0;
        if( isset($entityParametroCab) && !empty($entityParametroCab) )
        {
        $intIdParametrosVeloc = $entityParametroCab->getId();
        }
            
        $arrayParametrosDet  = array( 
        'estado'      => "Activo", 
        'parametroId' => $intIdParametrosVeloc,
        'empresaCod'  => $empresaId
        );
        $arrayParametroDetVelocidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy($arrayParametrosDet);
        $arrayCaractVelocidad= array();   
        foreach( $arrayParametroDetVelocidad as $objParametroDetVelocidad )
        {    

             $arrayCaractVelocidad[]= $objParametroDetVelocidad->getValor1();        
        }  
        return $this->render('catalogoBundle:InfoPlanCaracteristica:new.html.twig', array(
            'entity'           => $entity,
            'form'             => $form->createView(),
            'listado_detalle'  => $listado_detalle,
            'arreglo_tipo'     => $arreglo_tipo,
            'strCodigoInterno' => $strCodigoInterno,
            'arrayParamTipoPlan'     => $arrayParamTipoPlan,
            'strPrefijoEmpresa'      => $strPrefijoEmpresa,
            'strCategoriaPorDefecto' => $strCategoriaPorDefecto,
            'arrayCaractVelocidad' => json_encode($arrayCaractVelocidad)
        ));
    }
   /**
    * Funcion que guarda formulario de ingreso de Nuevo Plan
    * @author : telcos
    * @param request $request
    * @version 1.0 23-05-2014
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 04-08-2021 - Se agrega modificación y se obtiene la característica TIPO_CATEGORIA_PLAN_ADULTO_MAYOR para 
    *                           asociarla al crear un nuevo plan para empresa MD.
    *
    * @author Edgar Pin <epin@telconet.ec> 
    * @version 1.2 21-03-2023 - No permitir que se duplique el código interno de un plan.
    *
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function createAction(Request $request)
    {
        $peticion  = $this->get('request');
        $session   = $peticion->getSession();
        $empresaId = $session->get('idEmpresa');
        $entity    = new InfoPlanCab();
        $request   = $this->getRequest();
        $form      = $this->createForm(new InfoPlanCabType(array('empresaId'=>$empresaId)), $entity);
        $form->bind($request);
        $datos     = $request->get('valores');
        $valores   = json_decode($datos);
        
        $strPrefijoEmpresa = $session->get("prefijoEmpresa") ? $session->get("prefijoEmpresa") : '';
        $emGeneral                      = $this->get('doctrine')->getManager('telconet_general');
        $arrayParamEcucert  = array(
            'nombreParametro' => "VARIABLES_VELOCIDAD_PLANES",
            'estado'          => "Activo"
        );

        $entityParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneByNombreParametro($arrayParamEcucert);

        $intIdParametrosVeloc = 0;
        if( isset($entityParametroCab) && !empty($entityParametroCab) )
        {
            $intIdParametrosVeloc = $entityParametroCab->getId();
        }
                                       
        $arrayParametrosDet  = array( 
            'estado'      => "Activo", 
            'parametroId' => $intIdParametrosVeloc,
            'empresaCod'  => $empresaId
        );
        $arrayParametroDetVelocidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy($arrayParametrosDet);   
        if ( $form->isValid() )
        {
            $estado      = 'Activo';
	        $session     = $request->getSession();
	        $idEmpresa   = $session->get('idEmpresa');
            $usrCreacion = $request->getSession()->get('user');
            $em          = $this->getDoctrine()->getManager();
            if($entity->getCodigoPlan())
            {
                $arrayPlanCab = $em->getRepository('schemaBundle:InfoPlanCab')
                                   ->findBy(array('codigoPlan' => $entity->getCodigoPlan(),
                                                  'empresaCod' => $idEmpresa));
                if(count($arrayPlanCab) > 0)
                {
                    throw $this->createNotFoundException('El código del plan ya existe.');
                }
            }
            $entity->setFeCreacion( new \DateTime('now') );
            $entity->setUsrCreacion( $usrCreacion );
            $entity->setIpCreacion( $request->getClientIp());
            $entity->setEstado( $estado );
            $entity->setEmpresaCod( $idEmpresa );
            $entity->setIva( "S" );
            $entity->setTipo( $request->get('tipo') );
            $entity->setCodigoInterno( $request->get('codigo_interno') );
            $em->persist( $entity );
            $em->flush();
            $boolBanderaIp = false;
            foreach( $valores as $valor )
            {
                 $id              = $valor->producto;
                 $cantidad        = $valor->cantidad;
                 $precio          = $valor->precio_total;
                 $prod_caract     = $valor->prod_caract;
                 $valor_caract    = $valor->valor_caract;
                 $producto        = $em->getRepository('schemaBundle:AdmiProducto')->findOneById($id);
                 if($producto->getNombreTecnico()=='IP')
                 {
                     $boolBanderaIp = true;
                 }
                 $arrayCaractVelocidad= array();   
                 foreach( $arrayParametroDetVelocidad as $objParametroDetVelocidad )
                 {    
                     $objProdCaracVel = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                     ->findOneBy(array("productoId"        => $producto->getId(),
                                       "caracteristicaId"  => $objParametroDetVelocidad->getValor1(),
                                       "estado"            => "Activo"));
                     if( isset($objProdCaracVel) && !empty($objProdCaracVel) )  
                     {
                        $arrayCaractVelocidad[]= $objProdCaracVel;   
                     }                                          
                 }   
                 $usrCreacion     = $request->getSession()->get( 'user' );
                 $entityproducto  = new InfoPlanDet();
                 $entityproducto->setProductoId( $id );
                 $entityproducto->setCantidadDetalle( $cantidad );
                 $entityproducto->setPrecioItem( $precio );
                 $entityproducto->setCostoItem( $precio );
                 $entityproducto->setDescuentoItem( 0 );
                 $entityproducto->setEstado( "Activo" );
                 $entityproducto->setPlanId( $entity );
                 $entityproducto->setUsrCreacion( $usrCreacion );
                 $entityproducto->setIpCreacion( $request->getClientIp() );
                 $entityproducto->setFeCreacion( new \DateTime('now') );
                 $em->persist( $entityproducto );
                 $em->flush();
                
                 for( $i=0; $i<sizeof($prod_caract); $i++ )
                 { 
                        $strPlanCracte="No";
                        if( isset($arrayCaractVelocidad) && !empty($arrayCaractVelocidad) )
                        {
                            foreach($arrayCaractVelocidad as $objProdCaracVel)
                            {
                                if($objProdCaracVel->getId()==$prod_caract[$i])
                                {
                                    $strPlanCracte="Si";
                                    break;
                                }
                            }
                        }
   
                        if($strPlanCracte=="Si")
                        {  
                            //Guardar informacion de la caracteristica en el plan
                             $id                  = $prod_caract;
                             $entityPlanC              = new InfoPlanCaracteristica();
                             $valor_caract               = str_replace('.',",",$valor_caract);
                             $entityPlanC->setFeCreacion(new \DateTime('now'));
                             $entityPlanC->setPlanId($entity);
                             $entityPlanC->setCaracteristicaId($objProdCaracVel->getCaracteristicaId());
                             $entityPlanC->setValor($valor_caract[$i]);
                             $entityPlanC->setUsrCreacion($usrCreacion);
                             $entityPlanC->setIpCreacion($request->getClientIp());
                             $entityPlanC->setEstado('Activo');
                             $em->persist($entityPlanC);
                             $em->flush();
                        }else{
                            //Guardar informacion de la caracteristica del producto
                            $entityplanproductocaract  = new InfoPlanProductoCaract();
                            $entityplanproductocaract->setPlanDetId( $entityproducto->getId() );
                            $entityplanproductocaract->setProductoCaracterisiticaId( $prod_caract[$i] );
                            $entityplanproductocaract->setValor( $valor_caract[$i] );
                            $entityplanproductocaract->setEstado( "Activo" );
                            $usrCreacion = $request->getSession()->get('user');
                            $entityplanproductocaract->setUsrCreacion( $usrCreacion );
                            $entityplanproductocaract->setFeCreacion( new \DateTime('now') );
                            $em->persist( $entityplanproductocaract );
                            $em->flush();
                        }

                    }
            }
            //Guardo Ips maximas
            $ips_max_permitidas = $request->get('ips_max_permitidas');
            $caracteristica     = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("IP_MAX_PERMITIDAS");
            if( $caracteristica && $ips_max_permitidas>0 )
            {
                $usrCreacion              = $request->getSession()->get( 'user' );
                $entityplancaracteristica = new InfoPlanCaracteristica();
                $entityplancaracteristica->setPlanId( $entity );
                $entityplancaracteristica->setCaracteristicaId( $caracteristica );
                $entityplancaracteristica->setValor( $ips_max_permitidas );
                $entityplancaracteristica->setEstado( "Activo" );
                $entityplancaracteristica->setFeCreacion( new \DateTime('now') );
                $entityplancaracteristica->setUsrCreacion( $usrCreacion );
                $entityplancaracteristica->setIpCreacion( $request->getClientIp() );
                $em->persist( $entityplancaracteristica );
                $em->flush();
           }
           //guardo Frecuencia
            $frecuencia      = $request->get('frecuencia');
            $caracteristica  = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("FRECUENCIA");
            if( $caracteristica && $frecuencia )
            {
                $usrCreacion              = $request->getSession()->get( 'user' );
                $entityplancaracteristica = new InfoPlanCaracteristica();
                $entityplancaracteristica->setPlanId( $entity );
                $entityplancaracteristica->setCaracteristicaId( $caracteristica );
                $entityplancaracteristica->setValor( $frecuencia );
                $entityplancaracteristica->setEstado( "Activo" );
                $entityplancaracteristica->setFeCreacion( new \DateTime('now') );
                $entityplancaracteristica->setUsrCreacion( $usrCreacion );
                $entityplancaracteristica->setIpCreacion( $request->getClientIp() );
                $em->persist( $entityplancaracteristica );
                $em->flush();
            }
            
            //Se agrega validación por empresa MD, se guarda característica de tipo categoría del plan.
            if(($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN') && !empty($strPrefijoEmpresa)) 
            {
                $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                             ->findOneByDescripcionCaracteristica("TIPO_CATEGORIA_PLAN_ADULTO_MAYOR");
                if($objAdmiCaracteristica)
                {
                    $strUsrCreacion        = $request->getSession()->get('user');
                    $objPlanCaracteristica = new InfoPlanCaracteristica();
                    $objPlanCaracteristica->setPlanId($entity);
                    $objPlanCaracteristica->setCaracteristicaId($objAdmiCaracteristica);
                    $objPlanCaracteristica->setValor($request->get('tipoCategoriaPlan')); 
                    $objPlanCaracteristica->setEstado("Activo");
                    $objPlanCaracteristica->setFeCreacion( new \DateTime('now') );
                    $objPlanCaracteristica->setUsrCreacion($strUsrCreacion);
                    $objPlanCaracteristica->setIpCreacion($request->getClientIp());
                    $em->persist($objPlanCaracteristica);
                    $em->flush();
                }
            }
            
            return $this->redirect($this->generateUrl('infoplancaracteristicas_show', array('id' => $entity->getId())));
        }
    }

    /**
    * Funcion que edita formulario de Registro de Plan
    * @author : telcos
    * @param integer $id // id de plan
    * @version 1.0 23-05-2014
    * 
    * @version 1.1 06-08-2021 - Se agrega validación por empresa MD y se obtiene la característica TIPO_CATEGORIA_PLAN_ADULTO_MAYOR 
    *                           del plan.
    * 
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return Renders a view.
    */
    public function editAction($id)
    {
        $peticion          = $this->get('request');
        $session           = $peticion->getSession();
        $empresaId         = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get("prefijoEmpresa") ? $session->get("prefijoEmpresa") : '';
        $em                = $this->getDoctrine()->getManager();

        $entity     = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }

        $arreglo_tipo = array();
        if( $entity )
        {
            $arreglo_tipo[$entity->getTipo()] = $entity->getTipo();
        }
        $listado_tipo = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findTiposNegocioPorEmpresa($empresaId);
        foreach ( $listado_tipo as $listado_tipo )
        {
            $arreglo_tipo[$listado_tipo->getNombreTipoNegocio()] = $listado_tipo->getNombreTipoNegocio();
        }

        $listado_detalle = array(""=>"Seleccione","P"=>"Paquete","S"=>"Solucion");

        //obtener listado de items ya ingresados
        $estado = "Activo";
        $items  = $em->getRepository('schemaBundle:InfoPlanDet')->getPlanIdYEstados($id);
        if( $items )
        {
            $i=0;
            foreach( $items as $item )
            {
                $producto                    = $em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
                $plandet[$i]['producto']     = $producto->getDescripcionProducto();
                $plandet[$i]['cantidad']     = $item->getCantidadDetalle();
                $plandet[$i]['precio_total'] = $item->getPrecioItem();
                $plandet[$i]['producto_id']  = $producto->getId();
                $i++;


                $info_detalle[] = array('producto' =>$producto->getId(),
                                        'cantidad' => $item->getCantidadDetalle(),
                                        'precio_total' => $item->getPrecioItem(),
                                        'id_det'=>$item->getId());

            }
        }
        else
        {
            $info_detalle[] = array();
            $plandet[]      = array();
        }
        
        //Se agrega validaciones para obtener y enviar el tipo de categoría plan por parámetro para empresa MD.
        $arrayParamTipoPlan = array();
        $strValorCaractPlan = "";
        if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN') && !empty($strPrefijoEmpresa))
        {    
            $objCaractCategoriaPlan = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica("TIPO_CATEGORIA_PLAN_ADULTO_MAYOR");
                                                          
            $objInfoPlanCaract    = $em->getRepository('schemaBundle:InfoPlanCaracteristica')
                                            ->findOneByIdPlanCaracteristicaEstados($entity,$objCaractCategoriaPlan);
            
            $strValorCaractPlan = is_object($objInfoPlanCaract) ? $objInfoPlanCaract->getValor() : "";
            
            $arrayParamTipoPlanes = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get("PARAM_FLUJO_ADULTO_MAYOR", "COMERCIAL", "", 
                                                          "CATEGORIA_PLAN_ADULTO_MAYOR", "", "", "", "", "",
                                                          $empresaId);
            
            foreach($arrayParamTipoPlanes as $objParamTipoPlanes)
            {
                $arrayParamTipoPlan[$objParamTipoPlanes['valor1']] = $objParamTipoPlanes['valor1'];
            } 
        }

        $arreglo_encode      = json_encode($info_detalle);
        $editForm            = $this->createForm(new InfoPlanCabType(array('empresaId'=>$empresaId)), $entity);
        $deleteForm          = $this->createDeleteForm($id);
        $caracteristica      = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("IP_MAX_PERMITIDAS");
        $plan_caracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristicaEstados($entity,$caracteristica);
        $ips_max_permitidas  = "";
        if( $plan_caracteristica )
        {
           $ips_max_permitidas = $plan_caracteristica->getValor();
        }
        $caracteristica      = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("FRECUENCIA");
        $plan_caracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristicaEstados($entity,$caracteristica);
        $frecuencia  = "";
        if( $plan_caracteristica )
        {
           $frecuencia = $plan_caracteristica->getValor();
        }
        //codigo interno en base a la fecha de creacion sysdate formato yyyymm
        $dateFecha        = date("Y-m-d");
        $arrayFechaExp    = explode("-",$dateFecha);
        $strCodigoInterno = date ("Ym",strtotime($arrayFechaExp[0]."".$arrayFechaExp[1]));
        if( $entity  && $entity->getCodigoInterno()!= null && $entity->getCodigoInterno()!="")
        {
           $strCodigoInterno = $entity->getCodigoInterno();
        }
        $parametros = array('entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            );
        $parametros['listado_detalle']    = $listado_detalle;
        $parametros['items_detalle']      = $plandet;
        $parametros['arreglo']            = $arreglo_encode;
        $parametros['arreglo_tipo']       = $arreglo_tipo;
        $parametros['ips_max_permitidas'] = $ips_max_permitidas;
        $parametros['frecuencia']         = $frecuencia;
        $parametros['strCodigoInterno']   = $strCodigoInterno;
        $parametros['strPrefijoEmpresa']  = $strPrefijoEmpresa;
        $parametros['strValorCaractPlan'] = $strValorCaractPlan;
        $parametros['arrayParamTipoPlan'] = $arrayParamTipoPlan;


        return $this->render('catalogoBundle:InfoPlanCaracteristica:edit.html.twig', $parametros);
    }

   /**
    * Funcion que guarda formulario de Edicion de Plan
    * @author : telcos
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 06-08-2021 - Se agrega validación por empresa MD y se obtiene la característica TIPO_CATEGORIA_PLAN_ADULTO_MAYOR para 
    *                           asociarla al plan.
    * 
    * @param request $request
    * @param integer $id // Id del Plan
    * @version 1.0 23-05-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function updateAction(Request $request, $id)
    {
        $peticion          = $this->get('request');
        $session           = $peticion->getSession();
        $empresaId         = $session->get('idEmpresa');
        $datos             = $request->get('valores');
        $valores           = json_decode($datos);
        $strPrefijoEmpresa = $session->get("prefijoEmpresa") ? $session->get("prefijoEmpresa") : '';

        $em        = $this->getDoctrine()->getManager();

        $entity    = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        $editForm   = $this->createForm(new InfoPlanCabType(array('empresaId'=>$empresaId)), $entity);
        $deleteForm = $this->createDeleteForm($id);
        $request    = $this->getRequest();
        $editForm->bind($request);

        if ( $editForm->isValid() )
        {
            $entity->setTipo($request->get('tipo'));
            if($entity->getCodigoInterno()==null || $entity->getCodigoInterno()=="")
            {
                $entity->setCodigoInterno($request->get('codigo_interno'));

            }
            $entity->setFeUltMod(new \DateTime('now'));
            $entity->setUsrUltMod($session->get('user'));

            $em->persist($entity);
            $em->flush();
            $items = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanId($id);
            //Verificacion de items existentes
            if( $items )
            {
                $band = 0;
                foreach( $items as $item )
                {
                    foreach($valores as $valor)
                    {
                        if( $item->getId() == $valor->id_det && $valor->id_det != "" )
                        {
                            $band = 1;
                            break;
                        }
                        else
                            $band = 2;
                    }
                    if( $band == 2 )
                    {
                        $estado = "Eliminado";
                        $item->setEstado($estado);
                        $em->persist($item);
                        $em->flush();
                        //Inactivo las caracteristicas del Producto
                        $objInfoPlanProductoCaract = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->findByPlanDetId($item->getId());
                        if(isset($objInfoPlanProductoCaract))
                        {
	                    foreach($objInfoPlanProductoCaract as $objInfoPlanProductoCaract)
	                    {
                                $objInfoPlanProductoCaract->setEstado( $estado );
                                $objInfoPlanProductoCaract->setFeUltMod( new \DateTime('now') );
                                $objInfoPlanProductoCaract->setUsrUltMod( $session->get('user') );
		                $em->persist($objInfoPlanProductoCaract);
		                $em->flush();
                            }
                        }
                    }
                }
            }

            $boolBanderaIp = false;
            foreach( $valores as $valor )
            {
                $id_producto = $valor->producto;
                $cantidad    = $valor->cantidad;
                $precio      = $valor->precio_total;
                $id_det      = $valor->id_det;
                if( $id_det == "" )
                {
                    $prod_caract  = $valor->prod_caract;
                    $valor_caract = $valor->valor_caract;
                }
                if( $id_det == "" )
                {
                    $producto        = $em->getRepository('schemaBundle:AdmiProducto')->findOneById($id_producto);
                    if($producto->getNombreTecnico()=='IP')
                    {
                        $boolBanderaIp = true;
                    }
                    $usrCreacion     = $request->getSession()->get('user');
                    $entityproducto  = new InfoPlanDet();
                    $entityproducto->setProductoId($id_producto);
                    $entityproducto->setCantidadDetalle($cantidad);
                    $entityproducto->setPrecioItem($precio);
                    $entityproducto->setCostoItem($precio);
                    $entityproducto->setDescuentoItem($entity->getDescuentoPlan());
                    $entityproducto->setEstado("Activo");
                    $entityproducto->setPlanId($entity);
                    $entityproducto->setUsrCreacion($usrCreacion);
                    $entityproducto->setIpCreacion($request->getClientIp());
                    $entityproducto->setFeCreacion(new \DateTime('now'));
                    $em->persist($entityproducto);
                    $em->flush();

                    for( $i=0; $i<sizeof($prod_caract); $i++ )
                    {
                        $entityplanproductocaract  = new InfoPlanProductoCaract();
                        $usrCreacion               = $request->getSession()->get('user');
                        $entityplanproductocaract->setPlanDetId($entityproducto->getId());
                        $entityplanproductocaract->setProductoCaracterisiticaId($prod_caract[$i]);
                        $entityplanproductocaract->setValor($valor_caract[$i]);
                        $entityplanproductocaract->setEstado("Activo");
                        $entityplanproductocaract->setUsrCreacion($usrCreacion);
                        $entityplanproductocaract->setFeCreacion(new \DateTime('now'));
                        $em->persist($entityplanproductocaract);
                        $em->flush();
                    }
                }
            }
            //Guardo IPS maximas permitidas
            $bandera_inserta    = true;
            $ips_max_permitidas = $request->get('ips_max_permitidas');
            $caracteristica     = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("IP_MAX_PERMITIDAS");
            if( $caracteristica && ($ips_max_permitidas>=0 || $ips_max_permitidas=='') )
            {
                $plan_caracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristicaEstados($entity,$caracteristica);
                if( $plan_caracteristica )
                {
                    if( $plan_caracteristica->getValor() != $ips_max_permitidas )
                    {
                        $plan_caracteristica->setEstado("Inactivo");
                        $em->persist($plan_caracteristica);
                        $em->flush();
                    }
                    else
                    {
                        $bandera_inserta = false;
                    }
               }

               if( $bandera_inserta && $ips_max_permitidas>0 )
               {
                   $usrCreacion              = $request->getSession()->get('user');
                   $entityplancaracteristica = new InfoPlanCaracteristica();
                   $entityplancaracteristica->setPlanId($entity);
                   $entityplancaracteristica->setCaracteristicaId($caracteristica);
                   $entityplancaracteristica->setValor($ips_max_permitidas);
                   $entityplancaracteristica->setEstado("Activo");
                   $entityplancaracteristica->setFeCreacion(new \DateTime('now'));
                   $entityplancaracteristica->setUsrCreacion($usrCreacion);
                   $entityplancaracteristica->setIpCreacion($request->getClientIp());
                   $em->persist($entityplancaracteristica);
                   $em->flush();
               }
            }

             //Guardo Frecuencia del Plan
            $bandera_inserta    = true;
            $frecuencia         = $request->get('frecuencia');
            $caracteristica     = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("FRECUENCIA");
            if( $caracteristica && $frecuencia>0 )
            {
                $plan_caracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristicaEstados($entity,$caracteristica);
                if( $plan_caracteristica )
                {
                    if( $plan_caracteristica->getValor() != $frecuencia )
                    {
                        $plan_caracteristica->setEstado("Inactivo");
                        $em->persist($plan_caracteristica);
                        $em->flush();
                    }
                    else
                    {
                        $bandera_inserta = false;
                    }
               }

               if( $bandera_inserta )
               {
                   $usrCreacion              = $request->getSession()->get('user');
                   $entityplancaracteristica = new InfoPlanCaracteristica();
                   $entityplancaracteristica->setPlanId($entity);
                   $entityplancaracteristica->setCaracteristicaId($caracteristica);
                   $entityplancaracteristica->setValor($frecuencia);
                   $entityplancaracteristica->setEstado("Activo");
                   $entityplancaracteristica->setFeCreacion(new \DateTime('now'));
                   $entityplancaracteristica->setUsrCreacion($usrCreacion);
                   $entityplancaracteristica->setIpCreacion($request->getClientIp());
                   $em->persist($entityplancaracteristica);
                   $em->flush();
               }
            }
            
            //Se agrega validaciones para el tipo de categoría plan a empresa MD. 
            if(($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN') && !empty($strPrefijoEmpresa)) 
            {    
                $boolBanderaInserta   = true;
                $strTipoCategoriaPlan = $request->get('tipoCategoriaPlan');
                $objCaracteristica    = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica("TIPO_CATEGORIA_PLAN_ADULTO_MAYOR");
                if($objCaracteristica)
                {
                    $objPlanCaracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')
                                                    ->findOneByIdPlanCaracteristicaEstados($entity,$objCaracteristica);
                    if($objPlanCaracteristica)
                    {
                        if( $objPlanCaracteristica->getValor() != $strTipoCategoriaPlan )
                        {
                            $objPlanCaracteristica->setEstado("Inactivo");
                            $em->persist($objPlanCaracteristica);
                            $em->flush();
                        }
                        else
                        {
                            $boolBanderaInserta = false;
                        }
                    }

                    if($boolBanderaInserta && ($strTipoCategoriaPlan != null || $strTipoCategoriaPlan != ""))
                    {
                        $strUsrCreacion        = $request->getSession()->get('user'); 
                        $objPlanCaracteristica = new InfoPlanCaracteristica();
                        $objPlanCaracteristica->setPlanId($entity);
                        $objPlanCaracteristica->setCaracteristicaId($objCaracteristica);
                        $objPlanCaracteristica->setValor($strTipoCategoriaPlan);
                        $objPlanCaracteristica->setEstado("Activo");
                        $objPlanCaracteristica->setFeCreacion(new \DateTime('now'));
                        $objPlanCaracteristica->setUsrCreacion($strUsrCreacion);
                        $objPlanCaracteristica->setIpCreacion($request->getClientIp());
                        $em->persist($objPlanCaracteristica);
                        $em->flush();
                    }
                }
            }    
            
           // return $this->redirect($this->generateUrl('infoplancaracteristicas_edit', array('id' => $id)));
            return $this->redirect($this->generateUrl('infoplancaracteristicas_show', array('id' => $id)));
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
    * Funcion que inactiva Planes
    * Consideraciones: Solo se podra Inactivar Planes en estado Activo
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param request $request
    * @param integer $id // Id del Plan
    * @version 1.0 07-08-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function deleteAction($id)
    {
        $request              = $this->getRequest();
        $session              = $request->getSession();
        $em                   = $this->getDoctrine()->getManager();
        $strEstadoInactivo    = "Inactivo";
        $strEstadoActivo      = "Activo";
        $objFechaCreacion     = new \DateTime('now');
        $strUsuarioCreacion   = $session->get('user');
        $objInfoPlanCab       = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
        if ( !$objInfoPlanCab )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        if( $objInfoPlanCab && $objInfoPlanCab->getEstado()=="Activo" )
        {
            $objInfoPlanCab->setEstado( $strEstadoInactivo );
            $objInfoPlanCab->setFeUltMod( $objFechaCreacion );
            $objInfoPlanCab->setUsrUltMod( $strUsuarioCreacion );
            $em->persist($objInfoPlanCab);
            $em->flush();

            $objInfoPlanDet = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$strEstadoActivo);
            if( isset($objInfoPlanDet) )
            {
                foreach(($objInfoPlanDet)  as $det)
                {
                    $det->setEstado( $strEstadoInactivo );
                    $em->persist($det);
                    $em->flush();
                }
            }
            //GUARDAR INFO PLAN HISTORIAL
            $objPlanHistorial = new InfoPlanHistorial();
            $objPlanHistorial->setPlanId( $objInfoPlanCab );
            $objPlanHistorial->setIpCreacion( $request->getClientIp() );
            $objPlanHistorial->setFeCreacion( $objFechaCreacion );
            $objPlanHistorial->setUsrCreacion( $strUsuarioCreacion );
            $objPlanHistorial->setObservacion( 'Se realiza Inactivacion de Plan' );
            $objPlanHistorial->setEstado( $strEstadoInactivo );
            $em->persist($objPlanHistorial);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('infoplancaracteristicas'));
    }

    /**
     * 
     * Función usada para listar los productos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec> 
     * @version 1.1 23-09-2020 Se restringe el producto W + AP para que no se permita agregar dicho producto al crear plan y clonar plan
     * 
     */
    public function listarProductosAction()
    {
        $request = $this->getRequest();
        $tipo_plan = $request->request->get("tipo_plan");
        $em = $this->get('doctrine')->getManager('telconet');
		$session  = $request->getSession();
		$empresaId = $session->get('idEmpresa');
        $emGeneral                      = $this->get('doctrine')->getManager('telconet_general');
        $strOpcionConsulta              = $this->getRequest()->get('opcionConsulta');
        $arrayProdsRestringidosEnPlan   = array();
        $estado="Activo";
        $strModulo="Administracion";
        if($tipo_plan=="Paquete")
        {
            $arrayProductos = $em->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYEstado($empresaId,$estado,$strModulo);
            
            if(isset($strOpcionConsulta) && !empty($strOpcionConsulta) 
                && ($strOpcionConsulta === "CREAR_PLAN" || $strOpcionConsulta === "CLONAR_PLAN"))
            {
                $arrayParamsProdsRestringidosEnPlan = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get(  'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        'NOMBRES_TECNICOS_PRODS_RESTRINGIDOS_EN_PLAN',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $empresaId);
                if(isset($arrayParamsProdsRestringidosEnPlan) && !empty($arrayParamsProdsRestringidosEnPlan))
                {
                    foreach($arrayParamsProdsRestringidosEnPlan as $arrayParamProdRestringidoEnPlan)
                    {
                        $arrayProdsRestringidosEnPlan[] = $arrayParamProdRestringidoEnPlan["valor2"];
                    }
                }
            }
        }
        else
        {
            $arrayProductos = $em->getRepository('schemaBundle:AdmiProducto')->findPorEmpresaYEstado("",$estado,$strModulo);
        }

        if(!$arrayProductos)
        {
            $arrayArreglo = array('msg'=>'No existen datos');
        }
        else
        {
            $strVariableCombo = "<option>Seleccione</option>";
            foreach($arrayProductos as $objProducto)
            {
                if(isset($arrayProdsRestringidosEnPlan) &&!empty($arrayProdsRestringidosEnPlan))
                {
                    if(!in_array($objProducto->getNombreTecnico(), $arrayProdsRestringidosEnPlan))
                    {
                        $strVariableCombo.="<option value='".$objProducto->getId()."-".$objProducto->getDescripcionProducto()."'>".
                                            $objProducto->getDescripcionProducto()."</option>";
                    }
                }
                else
                {
                    $strVariableCombo.="<option value='".$objProducto->getId()."-".$objProducto->getDescripcionProducto()."'>".
                                        $objProducto->getDescripcionProducto()."</option>";
                }
            }
            $arrayArreglo = array('msg'=>'ok','div'=>$strVariableCombo);
        }

        $objResponse = new Response(json_encode($arrayArreglo));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
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

    /**
     * Funcion que devuelve un div para presentar informacion de las caracteristicas y su funcion Precio de los productos
     * agregados a un plan
     * @author telcos
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 08-12-2014
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 16-07-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 10-01-2016 - Se modifica para que acepte características que pueden ser combo box y se tenga que seleccionar las opciones para
     *                           validar la función ingresada por el usuario.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 22-03-2016 - Se corrige que se muestren los valores por defecto a los planes con caracteristica 'TIENE INTERNET'.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.4 18-04-2016
     * Se elimina la validación de obligatoriedad del servicio de poseer una característica para agregar el servicio.
     * Se presenta la función abreviada y se crea una opción para mostrar de forma legible la misma.
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.5 20-05-2016
     * Se agregan campos para el ingreso de servicios para TN
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.6 19-06-2016 - Se verifica si hay productos con nombre tecnico 'FINANCIERO' para solo mostrar un campo de texto para que el usuario
     *                           escriba el valor a cobrar.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.7 21-06-2016
     * Ajuste de componentes visuales y botón "ver función precio".
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.8 22-06-2016 - Se agrega campo cantidad cuando se muestran los productos de nombre técnico 'FINANCIERO'.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.9 12-07-2016
     * TN: Combo última milla se renderiza siempre que el servicio se de tipo_enlace = 'SI'
     * Se eliminan id de etiquetas que servían para ocultarlos desde el .js ya que no son requeridos.
     * Se envían las características como filas(tr) de tabla(table) para agregar a la tabla "tbProducto" para corregir la desorganización de los
     * componentes visuales.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.10 04-08-2016
     * Se valida que solo TN verifique si el producto es enlace o no
     *
     * @author Anabelle Penaherrera <apenaherrera@telconet.ec>
     * @version 1.11  06-09-2016
     * Se define campo oculto con el valor del estado inicial del producto.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.12 15-02-2017 - Se verifica si la característica 'REGISTRO_UNITARIO' está asociada al producto. Si se encuentra asociada al
     *                            producto se envía como campo oculto al formulario de productos con el valor de 'S' caso contrario se envía 'N'
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.13 26-02-2017 - Se verifica si la característica 'VALIDO_CIERTAS_PROVINCIAS' está asociada al producto. Si se encuentra asociada al
     *                            producto se verifica si la provincia a la que pertenece el punto en sessión es válida para la venta del producto.
     *                            Adicional se verifica si el producto tiene asociado la característica de 'VENTA_EXTERNA' para validar si existe un
     *                            producto de internet contratado, para poder agregar un producto de venta externa.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.14  28-03-2017
     * Se quita creacion de select de Tipo de Enlace ya que dicha información será usada una vez creado el servicio
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.15 09-05-2017 - Se agrega informacion que enliste tercerizadoras que seran mostradas cuando la Ultima Milla sea TERCERIZADA
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.16 22-05-2017 - Se agrega informacion para mostrar por parametros las ultimas millas de acuerdo al flujo pseudope
     *                            Se omite UMs que son tratadas en flujos especiales en activacion de Servicios convencionales
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.0 06-06-2017 - Se agregan la validaciones respectiva para los productos adicionales para el portal de NETLIFECAM, es decir
     *                           se agrega la característica NO_REQUIERE_ULTIMA_MILLA a los productos adicionales, usada para quitar la
     *                           obligatoriedad de escoger la última milla y para que no se presente el combo de última milla al agregar servicio
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.1 06-11-2017 - Se agrega validacion que cuando sea CLOUD IAAS para todos los parametros muestre la información de cada tipo
     *                           seleccionable : PROCESADOR / MEMORIA RAM / STORAGE
     *
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 2.2 23-01-2018 - Se agrega validación para TN y por el producto "INTERNET SMALL BUSINESS" para que muestre por parametro las
     *                           ultima milla FTTX.
     *                           Se añade condicional para TN y producto "INTERNET SMALL BUSINESS" a su caracteristica Grupo Negocio - "PYMETN"
     *                           Se obtiene los tipos de negocios restringidos para la creacion del producto "INTERNET SMALL BUSINESS".
     *                           Se modifica lista desplegable de velocidad para el producto "INTERNET SMALL BUSINESS", con la finalidad de mostrar
     *                           el ancho de banda + MB.
     *                           Se envia grupo de negocio del punto para su posterior validacion de restriccion por grupo de negocio para la creacion
     *                           del producto "INTERNET SMALL BUSINESS".
     *                           Se añade validacion para  bloquear los campos: precio negociación y precio instalación pactado únicamente para el
     *                           producto INTERNET SMALL BUSINESS ya que según el requerimiento, estos son fijos de acuerdo a la velocidad contratada
     *                           por el cliente.
     *
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 2.3 06-04-2018 - Se habilita campo precio de negociacón para Telconet Panamá
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.4 08-02-2018 - Cuando existen servicios configurados como multi carateristicas se envia al formulario un contenedor para
     *                           renderizar el grid de seleccion de estas caracteristicas
     *                           Adicional se envia bandera indicando tipo de producto con sus caracteristicas en forma de array
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.5 17-04-2018 - Se adapta producto DATOSDC para que pueda generar flujo y mostrar la ultima milla con la que se puede configurar
     *
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 27-04-2018 - Se agregan validaciones necesarias para agregar servicios de producto IP Small Business
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 2.7
     * @since 05-06-018
     * Se agrega un espacio por problemas al renderizar las características seleccionables para TNP.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.8 21-06-2018
     * - Se Valida para el ingreso de servicios FOX_PREMIUM que el punto posea al menos un servicio de internet en estado activo para dicho login
     *   y que la ultima milla del servicio de internet sea Fibra o Cobre
     * - No debe permitirse el ingreso de mas de 1 servicio Fox Premium por Punto o Login.
     * - Se muestra Div de mensaje informativo que el Cliente ya posee un Servicio FoxPremium en estado Cancel y que se procede a tomar la informacion
     *   del LOGIN (USUARIO_FOX) y SUSCRIBER_ID (SSID_FOX) existente para el nuevo servicio FOX ingresado.
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 2.6 17-07-2018 - Se agrega envío de forma de pago del cliente para realizar validación de ingreso de servicios
     *                           con producto  CLOUD IAAS PUBLIC.
     *
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 2.9 27-08-2018  Se agrega parametro para empresa Panama.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.10 27-11-2018  Se agregan validaciones para permitir vender servicios Small Business en empresa TNP
     * @since 2.9
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 23-10-2018 Se obtiene la velocidad de los servicios Small Business que necesiten aprobación.
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.9 22-08-2018
     * - Se Valida para el ingreso de servicios NETFIBER que el punto posea al menos un servicio de internet en estado activo para dicho login
     *   y que la ultima milla del servicio de internet sea Fibra, Cobre o Radio
     *   Obtengo para el producto NETFIBER la cantidad de metraje inicial que se cobra al cliente y que se encuentra incluido
     *   en el KIT del producto NETFIBER, y obtengo el costo por metraje adicional para mostrar mensaje por pantalla.
     *   Se parametrizan los estados y Ultimas millas permitidas en la Validación.
     * - Se Valida para el ingreso de servicios APWIFI, NETHOME que el punto posea servicio de internet Activo con ultima milla Fibra Optica,
     *   se parametrizan estado del servicio de internet y las UM permitidas en la validación
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.10 18-12-2018  Se modifica validación para poder cambiar el valor de la caracteristica TIENE INTERNET
     * @since 2.9
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.11 28-11-2018 Se agrega validación para que no se muestren las características asociadas a los productos
     *                         con nombre técnico WIFI_DUAL_BAND y EXTENDER_DUAL_BAND
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.12 04-02-2019 Se agregan validaciones para el flujo del producto TELCOHOME y sus ips adicionales
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.13 01-04-2019 Se edita la forma de obtener el correo de productos mcafee con base a nuevas definiciones comerciales
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.14 14-03-2019 Se agrega HTML para generar combo-box de selección de esquema en producto Internet Wifi
     *
     *
     * @author Edgar Holguín <eholguin@telconet.ec>
     * @version 2.15 14-03-2019 - Se habilita campo precio de negociacón para Telconet Guatemala
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.16 07-05-2019   Se agrega parámetro de entrada "strTipoProceso" para controlar que la caracteristica ES_GRATIS para productos DUAL BAND si
     *                            se muestra en ciertos escenarios y en otros sea ocultada
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.17 09-05-2019 Se modifica HTML de respuesta para que no presente la característica RELACION_INTERNET_WIFI en los
     * servicios de L3MPLS, ya que es exclusiva de los concentradores de Internet Wifi.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.16 30-05-2019 - Lógica para poder instalar SMALL BUSINESS CENTROS COMERCIALES
     *
     * @author Allan Suárez C <arsuarez@telconet.ec>
     * @version 2.17 04-07-2019 - Validar que sólo los productos que tengan la característica CLOUD PUBLIC IAAS se valide a nivel de cliente la forma
     *                            de pago.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.18 19-06-2019   Se modifican las validaciones para productos dual band según las definiciones establecidas por
     *                            los usuarios respecto a su venta y activación
     * @since 2.16
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 2.19 17-07-2019 - Lógica para poder instalar SMALL BUSINESS RAZÓN SOCIAL.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.20 29-07-2019 Se agregan validaciones para permitir el piloto de las licencias Kaspersky, en donde sólo se permiten que ciertos
     *                           logines con planes o productos I. PROTEGIDO MULTI PAID se activen con licencias Kaspersky.
     *                           Además, se agrega la obtención del nuevo parámetro opcionConsulta, para diferenciar cuando se está accediendo
     *                           desde la creación o clonación de los planes
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.21 13-08-2019 | Se agrega funcionalidad para que no se permita ingresar un servicio 'Wifi Alquiler de Equipos'
     *                            si no existe un servicio tradicional en estado 'Activo' o 'Factible'. Además la opción de poder
     *                            ingresar servicios Wifi Alquiler de Equipos adicionales, luego de una inspección.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 2.22 04-09-2019 - Se agrega INTERNET DC SDWAN Y DATOS DC SDWAN como productos complemantario a HOUSING y HOSTING.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 2.23 17-09-2019 - Se agrega lógica para listar las caracteristica de los productos de acuerdo al tipo de red(MPLS/GPON)
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.24 19-09-2019 | Se agrega un ajuste para que la característica INSTALACION_SIMULTANEA también sea presentada
     *                            para el producto Wifi Alquiler Equipos.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.25 07-11-2019 | Se realiza validacion de caracteristica INSTALACION_SIMULTANEA para que solo se presente cuando
     *                            el servicio tradicional este en estado factible.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.26 04-12-2019 | Se agrega validación de variable $intIdPersonaEmpresaRol que corresponde al identificador del rol del cliente
     *                            que se encuentre en sesión, en caso de no tener un cliente en sesión presentaba problemas la opción al ejecutar
     *                            el proceso correspondiente
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.27 03-02-2020 | Se agrega parametro necesario $arrayEstado al llamado del metodo 'validarServicioTradicional'.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 2.28 19-03-2020 | Activación del producto TELEWORKER e IP TELEWORKER.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.28 23-03-2020 | Se agregaron en el filtro de Velocidad 5 y 20 MB.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.29 24-03-2020 | Se realiza validacion de caracteristica INSTALACION_SIMULTANEA para que solo se presente cuando
     *                            el servicio tradicional este en estado factible para productos COU LINEAS TELEFONIA FIJA . 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.30 16-04-2020 Se agrega programación para el producto TELCOTEACHER e IP TELCOTEACHER de acuerdo a la programación ya agregada
     *                           para TELCOWORKER e IP TELCOWORKER
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.31 27-04-2020 Se elimina código que ya no es necesario por la reestructuración de programación para servicios Small Business
     *                           y se invoca a la función obtenerParametrosProductosTnGpon en lugar de obtenerInfoProdPrincipalConIp
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.32 30-05-2020 Se agrega logica correspondiente para soportar servicios simultaneos con o sin flujo 
     *                          en parametro 'CARACTERISTICAS_SERVICIOS_SIMULTANEOS'.
     *
     * @author David León <mdleon@telconet.ec>
     * @version 2.33 13-04-2020 | Se agrega campo de Cotización para los productos de Tn .
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.34 19-07-2020 Se agrega programación de característica PUNTO MD ASOCIADO que realiza la búsqueda de logines MD
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.35 24-07-2020 - Se presenta advertencia para el usuario al momento de elegir una cotización.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.36 25-07-2020 Se agrega campo de Requiere Trabajo para el producto Cableado Estructurado (TN).
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 2.37 03-09-2020 - Se agrega lógica para listar las cotizaciones en base a la propuesta selecionada.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.38 12-09-2020 Se agrega programación para flujo de producto W + AP con nombre técnico WDB_Y_EDB
     *
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 2.39 04-09-2020 - Se agrega lógica de programación para validar si los servicios 
     *                            PARAMOUNT Y NOGGIN tienen activado el servicio de internet y si no se agregan como pendiente
     *                          - se permite el ingreso de mas de 1 servicio PARAMOUNT Y NOGGIN.
     *                          - se agrega validacion para cuando el estado esta en incorte no se puedan agregar productos adicionales
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 3.0 11-11-2020 - Se retorna el nuevo valor esCore para los productos de DC.
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 3.1 20-09-2020   Se agregan validaciones para productos de la empresa MD con nombre técnico IP que tengan
     *                            asociada la característica IP WAN, solicitado en requerimiento de planes Pyme sin Ip
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.2 07-1-2021 -Se valida Mostrar las caracteristicas de los productos PARAMOUNT Y NOGGIN cuando no tengan punto ID
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.3 11-02-2021 Se agrega restricción para crear más de un servicio W+AP
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 3.4 02-12-2020 - Se agrega caja para el ingreso de código promocional en los productos adicionales nuevos para la empresa MD
     *                           que sean de un punto adicional o que el punto tenga un servicio de internet en los estados parametrizados.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.4 22-04-2021 Se elimina restricción para agregar servicios Extender dual band sólo con Wifi Dual Band, ya que ahora se permitirá
     *                         agregar dichos servicios sin importar el Ont Huawei que estuviera conectado
     *
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 3.5 18-05-2021 - Se elimina restricción de edición de precio de negociación para productos que
     *                           funcionan sobre la red GPON de MD por requerimiento de Erika Intriago.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.6 26-05-2021 Se realiza las validacios para las características de los productos con tipo de red GPON
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.7 09-08-2021 Se realiza validaciones con el producto ECDF respecto al estado del internet permitido para dicho producto.
     *
     * @author Byron Antón <banton@telconet.ec>
     * @version 3.8 09-07-2021 Se agrega caracteristica de proyecto
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 3.9 26-05-2021 - Se adiciona la frecuencia del producto
     * 
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 4.0 06-08-2021 - Se valida los valores del comportamiento/producto 
     * @since 3.9
     * 
     * @author Alex Arreaga <atarreaga@telconet.ec>
     * @version 4.1 05-08-2021 Se corrige validación verificando punto existente para el ingreso de código promocional debido a problemas
     *                         en interfaz creación de nuevo plan donde no se utiliza ese proceso.
     * 
     * @author Jean Pierre Nazareno Martinez <jnazareno@telconet.ec>
     * @version 4.1 12-10-2021 - Se agrega nueva caracteristica Migración de Tecnología SDWAN
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 4.2 02-08-2021 Se realiza las validacios para las características para SECURE CPE
     *
     * @author Antonio Ayala<afayala@telconet.ec>
     * @version 4.3 16-09-2021 - Se agrega característica de velocidad para Internet Safe 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.4 24-11-2021 Se modifica el nombre de la variable $strValidaEstadoInternet a $strServicioInternetEnEstadoOK para aclarar 
     *                         que dicha variable valida si un servicio de Internet se encuentra en un estado permitido para los productos
     *                         que tienen parametrizados los servicios de Internet como el Canal del Fútbol y Gol Tv
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.5 06-12-2021 - Se agrega el método para presentar div de los productos características relacionados al mismo producto
     *
     * @param integer $producto  // id del producto
     * @see \telconet\schemaBundle\Entity\AdmiProducto
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 4.4 13-12-2021 - Se realiza validación por descripción del producto 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 4.5 28-01-2022 Se modifican laa validaciones de equipos extenders debido a que se permitirá para tecnología ZTE
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 4.5 11-03-2022 - Se agrega validacion para velocidades de productos
     *                           que se encuentran en el parametro
     *  
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.6 08-07-2022 Se valida el producto IP INTERNET VPNoGPON, con la característica de la relación
     *                         del servicio principal INTERNET VPNoGPON.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 4.7 01-08-2022 Se valida en los productos DATOS GPON y INTERNET VPNoGPON con la característica de cámara,
     *                         obtener los valores del parámetro por filtro del id del producto.
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 4.8 08-08-2022 Se valida en MD que si el producto adicional requiere intervencion de opu la cantidad sea 1
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 4.8 17-09-2022 - Envío de listado de motivos para las solicitudes de instalación automáticas.
     *
     * @author Jonathan Quintana <jiquintana@telconet.ec>
     * @version 4.8 20-01-2023 - Se valida que no se pueda crear dos paquetes de hora para la misma razón social y que 
     *                           exista un paquete de horas para poder realizar una recarga.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 4.9 03-02-2023 Se agregaron variables qlos cuales tienen el id y nombre del producto paquetes de soporte.
     *                         Se envìa los valores en el response con etiquetas ya existentes.
     *                         Se realiza validaciòn adicionales cuando el paquete de soporte recarga no tiene un uui cargado
     *                         Se agrega una condiciòn donde llena la variable  $strCaracteristicaObligatoria para las caracterìsticas 
     *                         obligadas a registrar en el producto paquetes de soporte y recarga
     * 
     * @author Steven Ruano <sruano@telconet.ec>
     * @version 4.9 31-01-2023 - Permitir que se muestren mas de una velocidad + mb en el combo de
     *                           las caracteristicas de producto para IP SMALL BUSINESS.
     * 
     * @param integer $strDescripcionProducto  // descripcion del producto
     * @see \telconet\schemaBundle\Entity\AdmiProducto
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     */
    public function listarCaracteristicasPorProductoAction()
    {
        $objPeticion                 = $this->getRequest();
        $productoId                  = $objPeticion->request->get("producto");
        $strTipoRed                  = $objPeticion->request->get("strTipoRed") ? $objPeticion->request->get("strTipoRed"):'MPLS';
        $boolMostarCaracteristicas   = $objPeticion->request->get("verCaracteristicas");
        $strTipoProceso              = $objPeticion->request->get("strTipoProceso");
        $strInfoAdicionalProds       = $objPeticion->request->get("infoAdicionalProductos");
        $intIdPropuesta              = $objPeticion->request->get("intIdPropuesta") ? $objPeticion->request->get("intIdPropuesta"):"";
        $serviceUtilidades           = $this->get('administracion.Utilidades');
        $boolContinuar               = true;
        $objSession                  = $objPeticion->getSession();
        $strUsrCreacion              = $objSession->get('user') ? $objSession->get('user') : 'telcos';
        $strIpCreacion               = $objPeticion->getClientIp();
        $serviceUtil                 = $this->get('schema.Util');
        $serviceInfoServicio         = $this->get('comercial.InfoServicio');   
        $serviceTecnico              = $this->get('tecnico.InfoServicioTecnico');
        $serviceFoxPremium           = $this->get('tecnico.FoxPremium');
        $emComercial                 = $this->get('doctrine')->getManager('telconet');
        $emGeneral                   = $this->get('doctrine')->getManager('telconet_general');
        $emInfraestructura           = $this->get('doctrine')->getManager('telconet_infraestructura');
        $emFinanciero                = $this->get('doctrine')->getManager('telconet_financiero');
        $emSoporte                   = $this->get('doctrine')->getManager('telconet_soporte');
        $strPrefijoEmpresa           = $objSession->get("prefijoEmpresa") ? $objSession->get("prefijoEmpresa") : '';
        $strEmpresaCod               = $objSession->get("idEmpresa") ? $objSession->get("idEmpresa") : '';
        $arrayClienteSesion          = $objSession->get('cliente');
        $arrayPtoClte                = $objSession->get('ptoCliente') ? $objSession->get('ptoCliente'):'';
        $intIdPersonaEmpresaRol      = $arrayClienteSesion['id_persona_empresa_rol'];
        $strMsg                      = 'ok';
        $srtDivMsjFoxExisteCancel    = "";
        $strDivMsjNetFiber           = "";
        $strPresentarDiv             = "";
        $boolEval                    = false;
        $strNombreTecnico            = '';
        $strEsIsB                    = 'NO';
        $strEsIpWanPyme              = 'N';
        $strTipoNegociosRestringidos = '';
        $strNombreTipoNegocioPto     = '';
        $strValidaValoresCaracts     = '';
        $strValorCaractPlanProducto  = '';
        $strFormaPagoCliente         = "";
        $boolClienteTieneDeuda       = false;
        $boolBusiness                = false;
        $esbusiness                  = $objPeticion->get('esbusiness') ? $objPeticion->get('esbusiness') : $boolBusiness;
        $serviceLicenciasKaspersky   = $this->get('tecnico.LicenciasKaspersky');
        $strOpcionConsulta           = $objPeticion->request->get("opcionConsulta");
        $strRequiereTrabajo          = 'REQUIERE TRABAJO';
        $strCaractMigraTecSdwan      = "Migración de Tecnología SDWAN";

        //Cambio McAfee - se recibe id del punto para obtener correo en caso de que el producto sea Internet Protegido
        $puntoId     = $objPeticion->request->get("idPunto");
        $em          = $this->get('doctrine')->getManager('telconet');
        $estado      = "Activo";
        $items       = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')->findByProductoIdyEstado($productoId, $estado);
        $arrayProductosCaract = [];
        foreach( $items as $objItem )
        {
            if($objItem->getCaracteristicaId()->getDescripcionCaracteristica()!="VISUALIZAR_EN_MOVIL")
            {
                $arrayProductosCaract[] = $objItem;
            }
        }
        $items = $arrayProductosCaract;

        if (!empty($intIdPersonaEmpresaRol))
        {
            $objInfoPersonaEmpresaRol = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonaEmpresaRol);
        }
        if(is_object($objInfoPersonaEmpresaRol) && $strPrefijoEmpresa==='TN')
        {

            $arrayParametros     = array( 'strTipoInformacion' => "DESCRIPCION_FORMA_PAGO",
                                          'intIdPersonaRol'    => $objInfoPersonaEmpresaRol->getId(),
                                          'strEstado'          => $objInfoPersonaEmpresaRol->getEstado() );

            $strFormaPagoCliente = $em->getRepository('schemaBundle:InfoContrato')->getFormaPagoContrato($arrayParametros);
        }
        /**
         * BLOQUE VERIFICAR PRODUCTO
         *
         * Bloque que verifica lo siguiente:
         *   - Verifica si existe la característica 'VALIDO_CIERTAS_PROVINCIAS' asociada al producto, para validar si la provincia asociada al punto
         *     en sessión es válida para el ingreso del producto. (Restrincción por provincias)
         *   - Verifica si el producto tiene asociado la característica de 'VENTA_EXTERNA' para validar si existe un producto de internet contratado,
         *     para poder agregar un producto de venta externa
         */
        try
       {
            if( !empty($strEmpresaCod) && !empty($strPrefijoEmpresa) )
            {
                $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                         'strDescCaracteristica' => 'VENTA_EXTERNA',
                                                         'strEstado'             => 'Activo' );
                $strEsVentaExterna              = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                if( !empty($strEsVentaExterna) && $strEsVentaExterna == "S" )
                {
                    $arrayParametrosVentaExterna          = array('strEmpresaCod'               => $strEmpresaCod,
                                                                  'strPrefijoEmpresa'           => $strPrefijoEmpresa,
                                                                  'intIdPtoCliente'             => $puntoId,
                                                                  'strNombreEstadosInternet'    => 'ESTADOS_INTERNET_NOT_IN',
                                                                  'strParametroEstadosInternet' => 'estadosServiciosNotIn');
                    $arrayValidacionServiciosVentaExterna = $serviceInfoServicio->validarServiciosVentaExterna($arrayParametrosVentaExterna);
                    $strExisteServicioInternet            = ( isset($arrayValidacionServiciosVentaExterna['strExisteServicioInternet'])
                                                              && !empty($arrayValidacionServiciosVentaExterna['strExisteServicioInternet']) )
                                                             ? $arrayValidacionServiciosVentaExterna['strExisteServicioInternet'] : 'N';

                    if( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se pueden agregar productos de venta ".
                                           "externa puesto que el punto en sessión no tiene un <b>Servicio de Internet </b>contratado</div></td>".
                                           "</tr>";
                    }//( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                }//( !empty($strEsVentaExterna) && $strEsVentaExterna == "S" )
                // Se agregan Validaciones para el ingreso del Servicio Fox Premium, Paramount, Noggin y ECDF
                $arrayProducto = $serviceFoxPremium->determinarProducto(array('intIdProducto'=>$productoId));
                if ($arrayProducto['Status'] == 'OK')
                {
                    $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                             'strDescCaracteristica' => $arrayProducto['strDescCaracteristica'],
                                                             'strEstado'             => 'Activo' );
                    $strEsFoxPremium = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
                }
                if( !empty($strEsFoxPremium) && $strEsFoxPremium == "S" )
                {
                    $arrayParametrosFoxPremium = array('strEmpresaCod'               => $strEmpresaCod,
                                                       'strPrefijoEmpresa'           => $strPrefijoEmpresa,
                                                       'intIdPtoCliente'             => $puntoId,
                                                       'strNombreEstadosInternet'    => 'ESTADOS_INTERNET_'.$arrayProducto['strDescCaracteristica'],
                                                       'strParametroEstadosInternet' => 'estadosServicios',
                                                       'strDescCaracteristica'       =>  $arrayProducto['strDescCaracteristica'],
                                                       'strMigrar'                   =>  $arrayProducto['strMigrar'],
                                                       'strMensaje'                  =>  $arrayProducto['strMensaje'],
                                                       'strNombreTecnico'            =>  $arrayProducto['strNombreTecnico']);
                    //Si tiene punto valida los servicios del mismo
                    if($puntoId != null)
                    {
                        $arrayValidacionServiciosFoxPremium   = $serviceFoxPremium->validarServiciosFoxPremium($arrayParametrosFoxPremium);
    
                        $strExisteServicioInternet               = ( isset($arrayValidacionServiciosFoxPremium['strExisteServicioInternet'])
                                                                  && !empty($arrayValidacionServiciosFoxPremium['strExisteServicioInternet']) )
                                                                 ? $arrayValidacionServiciosFoxPremium['strExisteServicioInternet'] : 'N';
    
                        $strExisteServicio                       = ( isset($arrayValidacionServiciosFoxPremium['strExisteServicio'])
                                                                  && !empty($arrayValidacionServiciosFoxPremium['strExisteServicio']) )
                                                                 ? $arrayValidacionServiciosFoxPremium['strExisteServicio'] : 'N';
    
                        $strExisteServCancel                     = ( isset($arrayValidacionServiciosFoxPremium['strExisteServCancel'])
                                                                  && !empty($arrayValidacionServiciosFoxPremium['strExisteServCancel']) )
                                                                 ? $arrayValidacionServiciosFoxPremium['strExisteServCancel'] : 'N';
    
                        $strExisteServicioPaNo                   = ( isset($arrayValidacionServiciosFoxPremium['strExisteServicioPaNo'])
                                                                  && !empty($arrayValidacionServiciosFoxPremium['strExisteServicioPaNo']) )
                                                                 ? $arrayValidacionServiciosFoxPremium['strExisteServicioPaNo'] : 'N';

                        $strServicioInternetEnEstadoOK           = (isset($arrayValidacionServiciosFoxPremium['strServicioInternetEnEstadoOK'])
                                                                    && !empty($arrayValidacionServiciosFoxPremium['strServicioInternetEnEstadoOK']) )
                                                                       ? $arrayValidacionServiciosFoxPremium['strServicioInternetEnEstadoOK'] : 'S';
                    }
                    else 
                    {
                        $strExisteServicioInternet      =   'S';
                        $strExisteServicio              =   'N';
                        $strExisteServCancel            =   'N';
                        $strExisteServicioPaNo          =   'N';
                        $strServicioInternetEnEstadoOK  =   'S';
                    }
                    if( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se pueden agregar productos de ".
                                           $arrayProducto['strMensaje'] . " puesto que el punto en sessión no tiene un ".
                                           "<b>Servicio de Internet Activo </b>con FO o CO contratado</div></td>".
                                           "</tr>";
                    }//( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                    if( empty($strExisteServicio) || $strExisteServicio == 'S' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se pueden agregar productos de ".
                                           $arrayProducto['strMensaje'] . " puesto que el punto en sessión ya posee un ".
                                           "<b>Servicio ".$arrayProducto['strMensaje']." </b>registrado</div></td></tr>";
                    }//( empty($strExisteServicio) || $strExisteServicio == 'S' )
                    if( $strExisteServCancel == 'S' )
                    {
                        $srtDivMsjFoxExisteCancel = "<tr name='caracts'><td colspan='4'><div id='mensajeFox' class='infomessage' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>El Cliente posee un Servicio ".
                                           $arrayProducto['strMensaje'] . " Cancelado, se procede a tomar la informacion del ".
                                           "<b>LOGIN</b> y <b>SUSCRIBER_ID</b> existente.</div></td>".
                                           "</tr>";
                    }// ( $strExisteServCancel == 'S' )
                    if( empty($strExisteServicioPaNo) || $strExisteServicioPaNo == 'S' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se pueden agregar productos de ".
                                           $arrayProducto['strMensaje'] . " puesto que el punto en sessión posee ".
                                           "<b>Servicios en estado In-Corte </b></div></td></tr>";
                    }//( empty($strExisteServicioPaNo) || $strExisteServicioPaNo == 'S' )
                    if( $strServicioInternetEnEstadoOK == 'N' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se pueden agregar productos de <b>".
                                           $arrayProducto['strMensaje']. "</b> puesto que el punto en sessión no posee un ".
                                           " <b>Servicio de Internet</b> en estado permitido.</div></td></tr>";
                    }//( empty($strServicioInternetEnEstadoOK) || $strServicioInternetEnEstadoOK == 'S' )
                }//( !empty($strEsFoxPremium) && $strEsFoxPremium == "S" )
                $objAdmiProducto = $em->getRepository('schemaBundle:AdmiProducto')->findOneById($productoId);

                $strTipoProducto = $objAdmiProducto->getDescripcionProducto();

                /* VALIDACIÓN PARA PAQUETE HORAS DE SOPORTE */

                $objParametroDetValProd =   $em->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne("VALIDA_PRODUCTO_PAQUETE_HORAS_SOPORTE", //nombre parametro cab
                        "SOPORTE", "", 
                        "VALORES QUE AYUDAN A IDENTIFICAR QUE PRODUCTO ES PARA LA MUESTRA DE OPCIONES EN LA VISTA", //descripcion det
                        "", "", "", "", "", $strEmpresaCod
                    );
                if(( $strEmpresaCod == '10' && $strPrefijoEmpresa == 'TN') && ($objParametroDetValProd) )
                {
                    $strValorProductoPaqHoras       = $objParametroDetValProd['valor1'];
                    $strValorProductoPaqHorasRec    = $objParametroDetValProd['valor2'];
                    $objProductoPaquetePrincipal    = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array("descripcionProducto" => $strValorProductoPaqHoras));
                    $objProductoPaqueteRec          = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findOneBy(array("descripcionProducto" => $strValorProductoPaqHorasRec));
                    $intIdProductoPaquetePrincipal  = $objProductoPaquetePrincipal->getId();
                    $intIdProductoPaqueteRec        = $objProductoPaqueteRec->getId();

                    
                    /* Validación para producto PAQUETE HORAS SOPORTE. */
                    if ($strTipoProducto == $strValorProductoPaqHoras)
                    {
                        $arrayServiciosExistentes = $emComercial->getRepository('schemaBundle:InfoServicio')
                        ->obtenerServiciosPorDescripcionProducto($strValorProductoPaqHoras, $puntoId, $strEmpresaCod);

                        if ($arrayServiciosExistentes['total'] > 0)
                        {
                            $boolContinuar = false;
                            $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                            . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                            . "Ya existe un servicio de ".$strValorProductoPaqHoras." creado para "  
                                                            . "esta razón social </div></td> </tr>";

                        }
                    }
                    /* Validación para producto PAQUETE HORAS SOPORTE RECARGA. */
                    if ($strTipoProducto == $strValorProductoPaqHorasRec)
                    {
                        $arrayServiciosExistentes = $emComercial->getRepository('schemaBundle:InfoServicio')
                        ->obtenerServiciosPorDescripcionProducto($strValorProductoPaqHoras, $puntoId, $strEmpresaCod);

                        if ($arrayServiciosExistentes['total'] == 0)
                        {
                            $boolContinuar   = false;
                            $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                        . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                        . "El servicio ".$strValorProductoPaqHorasRec.
                                                        " requiere que se agregue previamente un servicio "
                                                        . $strValorProductoPaqHoras."</div></td> </tr>";
                        }

                        if ($arrayServiciosExistentes['total'] > 0)
                        {
                            $objPrimerServicio              = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->findOneBy(array("puntoId"    => $puntoId,
                                                                                    "productoId"   => $intIdProductoPaquetePrincipal
                                                                            ), array("feCreacion"  => 'ASC'));
                            $intPrimerServicioId            = $objPrimerServicio->getId();                         
                            $arrayObtenerUuid               = $emSoporte->getRepository('schemaBundle:GestionPaqueteSoporte')
                                                                    ->obtenerUuidPaquete($intPrimerServicioId);
                            if (!$arrayObtenerUuid) 
                            {
                                    $boolContinuar          = false;
                                    $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                            . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                            . "El servicio ".$strValorProductoPaqHorasRec.
                                                            " requiere tener un valor que depende del "
                                                            . $strValorProductoPaqHoras." (el UUid no se encuentra)</div></td> </tr>";
                            }
                        }
                    }
                }

                /*Valido que el producto a listar sea un alquiler de equipos.*/
                if ($objAdmiProducto->getDescripcionProducto() == "WIFI Alquiler Equipos")
                {
                    /*Valido que tenga un servicio tradicional activo o factible.*/
                    $arrayServicioTradicionalValidado = $serviceInfoServicio->validarServicioTradicional(
                                                                                        $puntoId,
                                                                                        $productoId, 
                                                                                        array('Activo', 'Factible'));
                    if (!$arrayServicioTradicionalValidado['boolInstalacionSimultanea'])
                    {
                        /*Lanzo una excepción y presento un mensaje al usuario.*/
                        throw new \Exception(serialize(array(
                            'msg'=>"No puede ingresar este servicio si no existe un servicio tradicional 'Activo' o 'Factible'.",
                            'code'=>'NO_TRAD'
                        )));
                    }
                }
                // Se agregan Validaciones para el ingreso del Servicio NETFIBER
                $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                         'strDescCaracteristica' => 'NETFIBER',
                                                         'strEstado'             => 'Activo' );
                $strEsNetFiber                  = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                if( !empty($strEsNetFiber) && $strEsNetFiber == "S" )
                {
                    $arrayParametrosNetFiber = array(  'strEmpresaCod'                => $strEmpresaCod,
                                                       'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                                                       'intIdPtoCliente'              => $puntoId,
                                                       'strNombreEstadosInternet'     => 'ESTADOS_INTERNET_NETFIBER',
                                                       'strParametroValorUltimaMilla' => 'ULTIMAS_MILLAS_INTERNET_NETFIBER',
                                                       'strParametroEstadosInternet'  => 'estadosServicios');

                    $arrayValidacionServiciosNetFiber  = $serviceInfoServicio->validarServicioPorEstadoServUm($arrayParametrosNetFiber);

                    $strExisteServicioInternet            = ( isset($arrayValidacionServiciosNetFiber['strExisteServicioInternet'])
                                                              && !empty($arrayValidacionServiciosNetFiber['strExisteServicioInternet']) )
                                                             ? $arrayValidacionServiciosNetFiber['strExisteServicioInternet'] : 'N';

                    if( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto ".
                                           $objAdmiProducto->getDescripcionProducto().
                                           " puesto que el punto en sessión no tiene un <b>Servicio de Internet Activo ".
                                           "</b>con FO, CO, RA contratado</div></td>".
                                           "</tr>";
                    }//( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                }//( !empty($strEsNetFiber) && $strEsNetFiber == "S" )

                 // Se agregan Validaciones para el ingreso del Servicio APWIFI
                $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                         'strDescCaracteristica' => 'APWIFI',
                                                         'strEstado'             => 'Activo' );
                $strEsApWifi                    = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                if( !empty($strEsApWifi) && $strEsApWifi == "S" )
                {
                    $arrayParametrosApWifi = array(  'strEmpresaCod'                => $strEmpresaCod,
                                                     'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                                                     'intIdPtoCliente'              => $puntoId,
                                                     'strNombreEstadosInternet'     => 'ESTADOS_INTERNET_APWIFI',
                                                     'strParametroValorUltimaMilla' => 'ULTIMAS_MILLAS_INTERNET_APWIFI',
                                                     'strParametroEstadosInternet'  => 'estadosServicios');

                    $arrayValidacionServiciosApWifi  = $serviceInfoServicio->validarServicioPorEstadoServUm($arrayParametrosApWifi);

                    $strExisteServicioInternet            = ( isset($arrayValidacionServiciosApWifi['strExisteServicioInternet'])
                                                              && !empty($arrayValidacionServiciosApWifi['strExisteServicioInternet']) )
                                                             ? $arrayValidacionServiciosApWifi['strExisteServicioInternet'] : 'N';

                    if( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto ".
                                           $objAdmiProducto->getDescripcionProducto().
                                           " puesto que el punto en sessión no tiene un <b>Servicio de Internet Activo ".
                                           "</b>con FO contratado</div></td>".
                                           "</tr>";
                    }//( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                }//( !empty($strEsApWifi) && $strEsApWifi == "S" )

                 // Se agregan Validaciones para el ingreso del Servicio NETHOME
                $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                         'strDescCaracteristica' => 'NETHOME',
                                                         'strEstado'             => 'Activo' );
                $strEsNetHome                   = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                if( !empty($strEsNetHome) && $strEsNetHome == "S" )
                {
                    $arrayParametrosNetHome = array( 'strEmpresaCod'                => $strEmpresaCod,
                                                     'strPrefijoEmpresa'            => $strPrefijoEmpresa,
                                                     'intIdPtoCliente'              => $puntoId,
                                                     'strNombreEstadosInternet'     => 'ESTADOS_INTERNET_NETHOME',
                                                     'strParametroValorUltimaMilla' => 'ULTIMAS_MILLAS_INTERNET_NETHOME',
                                                     'strParametroEstadosInternet'  => 'estadosServicios');

                    $arrayValidacionServiciosNetHome  = $serviceInfoServicio->validarServicioPorEstadoServUm($arrayParametrosNetHome);

                    $strExisteServicioInternet            = ( isset($arrayValidacionServiciosNetHome['strExisteServicioInternet'])
                                                              && !empty($arrayValidacionServiciosNetHome['strExisteServicioInternet']) )
                                                             ? $arrayValidacionServiciosNetHome['strExisteServicioInternet'] : 'N';

                    if( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                    {
                        $boolContinuar   = false;
                        $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                           "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto ".
                                           $objAdmiProducto->getDescripcionProducto().
                                           " puesto que el punto en sessión no tiene un <b>Servicio de Internet Activo ".
                                           "</b>con FO contratado</div></td>".
                                           "</tr>";
                    }//( empty($strExisteServicioInternet) || $strExisteServicioInternet == 'N' )
                }//( !empty($strEsNetHome) && $strEsNetHome == "S" )

                //Validaciones para Producto Wifi Dual Band, Extender Dual Band e Ip Fija para Pyme MD
                $objProductoValidar   = $em->getRepository('schemaBundle:AdmiProducto')->find($productoId);
                if($strPrefijoEmpresa === 'MD' &&
                   is_object($objProductoValidar) && 
                   $objProductoValidar->getNombreTecnico() === 'IP')
                { 
                    $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                             'strDescCaracteristica' => 'IP WAN',
                                                             'strEstado'             => 'Activo' );
                    $strEsIpWanPyme                 = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
                }
                if(is_object($objProductoValidar) && ($objProductoValidar->getNombreTecnico() === "WIFI_DUAL_BAND"
                    || $objProductoValidar->getNombreTecnico() === "EXTENDER_DUAL_BAND"
                    || $objProductoValidar->getNombreTecnico() === "WDB_Y_EDB"
                    || $strEsIpWanPyme === "S"))
                {
                    if(isset($puntoId) && !empty($puntoId) && $puntoId > 0)
                    {
                        $arrayRespuestaServInternetValido   = $serviceTecnico->obtieneServicioInternetValido(array( "intIdPunto"    => $puntoId,
                                                                                                                    "strCodEmpresa" => $strEmpresaCod
                                                                                                                  ));
                        $strStatusServInternetValido    = $arrayRespuestaServInternetValido["status"];
                        $objServicioInternet            = $arrayRespuestaServInternetValido["objServicioInternet"];
                        if($strStatusServInternetValido === "OK")
                        {
                            if(!is_object($objServicioInternet) && $strEsIpWanPyme !== "S")
                            {
                                $boolContinuar  = false;
                                $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                    "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto <b>"
                                                    .$objProductoValidar->getDescripcionProducto().
                                                    "</b> puesto que el punto en sesión no tiene un <b>Servicio de Internet </b>contratado</div>".
                                                    "</td></tr>";
                            }
                        }
                        else
                        {
                            if ($strEsIpWanPyme !== "S")
                            {
                                $boolContinuar  = false;
                                $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                    "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto <b>"
                                                    .$objProductoValidar->getDescripcionProducto().
                                                    "</b> puesto que no se ha podido obtener el <b>Servicio de Internet </b>contratado para el ".
                                                    "punto en sesión</div></td></tr>";
                            }
                        }
                    }
                    if(is_object($objServicioInternet) && $strEsIpWanPyme === "N" && $boolContinuar)
                    {
                        $arrayVerifTecnologiaDualBand           = $serviceTecnico->verificaTecnologiaDualBand(
                                                                                array("intIdServicioInternet" => $objServicioInternet->getId()));
                        $strStatusVerifTecnologiaDualBand       = $arrayVerifTecnologiaDualBand["status"];
                        $strMensajeVerifTecnologiaDualBand      = $arrayVerifTecnologiaDualBand["mensaje"];
                        $strModelosEquiposWdbTecnologiaDualBand = $arrayVerifTecnologiaDualBand["modelosEquiposWdb"];
                        if($strStatusVerifTecnologiaDualBand === "OK")
                        {
                            $arrayInfoVerifVerifTecnologiaDualBand  = explode('|', $strMensajeVerifTecnologiaDualBand);
                            $strMarcaOltTecnologiaDualBand          = $arrayInfoVerifVerifTecnologiaDualBand[0];
                            $strModeloOltTecnologiaDualBand         = $arrayInfoVerifVerifTecnologiaDualBand[1];
                            $strEsPermitidoWYExtenderEnPlanes       = $arrayInfoVerifVerifTecnologiaDualBand[2];
                            $objPlanServicioInternet = $objServicioInternet->getPlanId();
                            if(is_object($objPlanServicioInternet))
                            {
                                if($objProductoValidar->getNombreTecnico() === "WDB_Y_EDB" || 
                                   $objProductoValidar->getNombreTecnico() === "WIFI_DUAL_BAND")
                                {
                                    if(isset($strModelosEquiposWdbTecnologiaDualBand) && !empty($strModelosEquiposWdbTecnologiaDualBand))
                                    {
                                        if($objProductoValidar->getNombreTecnico() === "WIFI_DUAL_BAND" && $strEsPermitidoWYExtenderEnPlanes === "SI")
                                        {
                                            $arrayRespuestaProdWdbEnPlan    = $serviceTecnico->obtieneProductoEnPlan(
                                                                                                    array(  "intIdPlan"                 => 
                                                                                                            $objPlanServicioInternet->getId(),
                                                                                                            "strNombreTecnicoProducto"  => 
                                                                                                            $objProductoValidar->getNombreTecnico()));
                                            $strProductoWdbEnPlan           = $arrayRespuestaProdWdbEnPlan["strProductoEnPlan"];
                                            if($strProductoWdbEnPlan === "SI")
                                            {
                                                $boolContinuar  = false;
                                                $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                                   "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede ".
                                                                   "agregar producto <b>".$objProductoValidar->getDescripcionProducto().
                                                                   "</b> debido a que el punto ya posee dicho producto incluido en el plan</div>".
                                                                   "</td></tr>";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $boolContinuar = false;
                                        $strMsg        = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                            "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto".
                                                            " <b>".$objProductoValidar->getDescripcionProducto().
                                                            "</b> puesto que no está permitido para la tecnología ".$strMarcaOltTecnologiaDualBand.
                                                            "con modelo de olt ".$strModeloOltTecnologiaDualBand.
                                                            " del servicio de Internet contratado</div></td></tr>";
                                    }
                                    
                                    if($boolContinuar)
                                    {
                                        $arrayRespuestaServiciosWdb = $serviceTecnico->obtenerServiciosPorProducto(
                                                                                        array(  "intIdPunto"                    => $puntoId,
                                                                                                "arrayNombresTecnicoProducto"   => 
                                                                                                array("WIFI_DUAL_BAND", "WDB_Y_EDB"),
                                                                                                "strCodEmpresa"                 => 
                                                                                                $strEmpresaCod));
                                        $intContadorServiciosWdb    = $arrayRespuestaServiciosWdb["intContadorServiciosPorProducto"];
                                        if(intval($intContadorServiciosWdb) > 0)
                                        {
                                            $boolContinuar = false;
                                            $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' ".
                                                              "class='info-error' style='clear: both;overflow: hidden; ".
                                                              "padding-bottom: 5px;'>No se puede agregar producto <b>".
                                                              $objProductoValidar->getDescripcionProducto().
                                                              "</b> debido a que el punto ya posee dicho servicio</div>".
                                                              "</td></tr>";
                                        }
                                        else
                                        {
                                            $arrayRespuestaWdbEnlazado  = $serviceTecnico->verificaEquipoEnlazado(
                                                                                            array(  "intIdServicioInternet" => 
                                                                                                    $objServicioInternet->getId(),
                                                                                                    "strTipoEquipoABuscar"  => "WIFI DUAL BAND"));
                                            $strStatusWdbEnlazado       = $arrayRespuestaWdbEnlazado["status"];
                                            $strMensajeWdbEnlazado      = $arrayRespuestaWdbEnlazado["mensaje"];
                                            $strInfoEquipoWdbEnlazado   = $arrayRespuestaWdbEnlazado["infoEquipoEnlazado"];
                                            if($strStatusWdbEnlazado === "OK")
                                            {
                                                if($objProductoValidar->getNombreTecnico() === "WDB_Y_EDB")
                                                {
                                                    $arrayRespuestaEdbEnlazado  = $serviceTecnico->verificaEquipoEnlazado(
                                                                                    array(  "intIdServicioInternet" => $objServicioInternet->getId(),
                                                                                            "strTipoEquipoABuscar"  => "EXTENDER DUAL BAND"));
                                                    $strStatusEdbEnlazado       = $arrayRespuestaEdbEnlazado["status"];
                                                    $strMensajeEdbEnlazado      = $arrayRespuestaEdbEnlazado["mensaje"];
                                                    $strInfoEquipoEdbEnlazado   = $arrayRespuestaEdbEnlazado["infoEquipoEnlazado"];
                                                    if($strStatusEdbEnlazado === "OK")
                                                    {
                                                        if(!empty($strInfoEquipoWdbEnlazado) && !empty($strInfoEquipoEdbEnlazado))
                                                        {
                                                            $boolContinuar  = false;
                                                            $strMsg         = "<tr name='caracts'><td colspan='4'>"
                                                                                ."<div id='mensajeError' class='info-error' ".
                                                                                "style='clear: both;overflow: hidden; padding-bottom: 5px;'>"
                                                                                ."No se puede agregar producto <b>"
                                                                                .$objProductoValidar->getDescripcionProducto().
                                                                                "</b> ya que el cliente ya tiene dichos equipos</div></td></tr>";
                                                        }
                                                        else if($objServicioInternet->getTipoOrden() === "T" 
                                                            && $objServicioInternet->getEstado() !== "Activo")
                                                        {
                                                            $boolContinuar  = false;
                                                            $strMsg         = "<tr name='caracts'><td colspan='4'>"
                                                                                ."<div id='mensajeError' class='info-error' ".
                                                                                "style='clear: both;overflow: hidden; padding-bottom: 5px;'>"
                                                                                ."No se puede agregar producto <b>"
                                                                                .$objProductoValidar->getDescripcionProducto().
                                                                                "</b> ya que no existe un flujo definido para este tipo de orden"
                                                                                ."</div></td></tr>";
                                                        }
                                                    }
                                                    else
                                                    {
                                                        $boolContinuar  = false;
                                                        $strMsg         = "<tr name='caracts'><td colspan='4'>"
                                                                            ."<div id='mensajeError' class='info-error' ".
                                                                            "style='clear: both;overflow: hidden; padding-bottom: 5px;'>".
                                                                            "No se puede agregar producto <b>"
                                                                            .$objProductoValidar->getDescripcionProducto().
                                                                            "</b> ya que ".$strMensajeEdbEnlazado."</div></td></tr>";
                                                    }
                                                }
                                                else
                                                {
                                                    if(!empty($strInfoEquipoWdbEnlazado))
                                                    {
                                                        $boolContinuar = false;
                                                        $strMsg         = "<tr name='caracts'><td colspan='4'>".
                                                                          "<div id='mensajeError' class='info-error' ".
                                                                          "style='clear: both;overflow: hidden; padding-bottom: 5px;'>"
                                                                          ."No se puede agregar producto <b>"
                                                                          .$objProductoValidar->getDescripcionProducto().
                                                                          "</b> debido a que el punto ya posee el equipo asociado a este servicio".
                                                                          "</div></td></tr>";
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $boolContinuar  = false;
                                                $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                                    "style='clear: both;overflow: hidden; padding-bottom: 5px;'>"
                                                                    ."No se puede agregar producto "
                                                                    ."<b>".$objProductoValidar->getDescripcionProducto().
                                                                    "</b> ya que ".$strMensajeWdbEnlazado."</div></td></tr>";
                                            }
                                        }
                                    }
                                }
                                else
                                {
                                    //Validar el número de extenders
                                    $arrayRespuestaServiciosEdb = $serviceTecnico->obtenerServiciosPorProducto(
                                                                            array(  "intIdPunto"                    => $puntoId,
                                                                                    "arrayNombresTecnicoProducto"   => 
                                                                                    array($objProductoValidar->getNombreTecnico()),
                                                                                    "strCodEmpresa"             => $strEmpresaCod));
                                    $intContadorServiciosEdb    = $arrayRespuestaServiciosEdb["intContadorServiciosPorProducto"];
                                    if(intval($intContadorServiciosEdb) > 0)
                                    {
                                        $arrayNumMaxServAdicsEdb    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne(   'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            'NUM_MAX_SERVICIOS_ADICIONALES_X_PUNTO',
                                                                                            $objProductoValidar->getNombreTecnico(),
                                                                                            '',
                                                                                            '',
                                                                                            '',
                                                                                            $strEmpresaCod);
                                        if(isset($arrayNumMaxServAdicsEdb) && !empty($arrayNumMaxServAdicsEdb)
                                            && intval($intContadorServiciosEdb) >= intval($arrayNumMaxServAdicsEdb['valor3']))
                                        {
                                            $boolContinuar = false;
                                            $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' ".
                                                              "class='info-error' style='clear: both;overflow: hidden; ".
                                                              "padding-bottom: 5px;'>No se puede agregar producto <b>".
                                                              $objProductoValidar->getDescripcionProducto().
                                                              "</b> debido a que el punto ya posee ". intval($intContadorServiciosEdb) .
                                                              " servicios de este tipo y la cantidad máxima permitida es ".
                                                              intval($arrayNumMaxServAdicsEdb['valor3']). "</div>".
                                                              "</td></tr>";
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $boolContinuar  = false;
                                $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                  "style='clear: both;overflow: hidden; padding-bottom: 5px;'>".
                                                  "No se puede agregar producto <b>".
                                                  $objProductoValidar->getDescripcionProducto().
                                                  "</b> ya que no existe un flujo definido para el ".
                                                  "<b>Servicio de Internet </b>contratado</div></td></tr>";
                            }
                        }
                        else
                        {
                            $boolContinuar = false;
                            $strMsg         = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar producto <b>"
                                                .$objProductoValidar->getDescripcionProducto().
                                                "</b> puesto que ".$strMensajeVerifTecnologiaDualBand."</div></td></tr>";
                        }
                    }
                    if(is_object($objServicioInternet) && $strEsIpWanPyme === "S" && $boolContinuar)
                    {
                        $serviceInternetProtegido = $this->get('tecnico.InternetProtegido');
                        $arrayRespuestaProdIp     = $serviceInternetProtegido->verificaProductosEnPlan(
                                                        array( "intIdPlan" => $objServicioInternet->getPlanId()->getId(),
                                                               "strNombreTecnicoProducto" => "IP"));
                        $strProductoExistente = $arrayRespuestaProdIp["strPlanTieneProducto"];
                        if ($strProductoExistente === "NO")
                        {
                            $arrayEstadosIpFijaWan = array ('PreAsignacionInfoTecnica',
                                                            'Detenido',
                                                            'Detenida',
                                                            'Asignada',
                                                            'Activo',
                                                            'In-Corte');

                            $arrayServiciosIpFijaWan = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                   ->findBy(array("puntoId"    => $objServicioInternet->getPuntoId(),
                                                                                  "productoId" => $objProductoValidar,
                                                                                  "estado"     => $arrayEstadosIpFijaWan));
                            if(!empty($arrayServiciosIpFijaWan))
                            {
                                $strProductoExistente = "SI";
                            }
                        }
                        if ($strProductoExistente === 'SI')
                        {
                            $boolContinuar = false;
                            $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                      "style='clear: both;overflow: hidden; padding-bottom: 5px;'>No se puede agregar ".
                                      "producto <b>".$objProductoValidar->getDescripcionProducto().
                                      "</b> debido a que el punto ya posee dicho servicio</div>".
                                      "</td></tr>";
                        }
                    }
                }

                if(is_object($objProductoValidar))
                {
                    $arrayParamsVerificaRazonSocial = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMS_PRODS_TN_GPON',
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         'PRODUCTOS_VERIFICA_RAZON_SOCIAL',
                                                                         $objProductoValidar->getId(),
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         $strEmpresaCod);
                    if(isset($arrayParamsVerificaRazonSocial) && !empty($arrayParamsVerificaRazonSocial))
                    {
                        $objPuntoSMB = $em->getRepository('schemaBundle:InfoPunto')->findOneById($puntoId);
                        if(is_object($objPuntoSMB) && is_object($objPuntoSMB->getPersonaEmpresaRolId()))
                        {
                            //Se obtienen los tipos de negocio restringidos para el producto 'INTERNET SMALL BUSINESS'
                            $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->get('LISTA_RAZON_SOCIAL_SMB',
                                                                     'COMERCIAL',
                                                                     '',
                                                                     '',
                                                                     $objPuntoSMB->getPersonaEmpresaRolId()->getId(), //persona_empresa_rol_id
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     '');
                            if(empty($arrayAdmiParametroDet))
                            {
                                $boolContinuar = false;
                                $strMsg        = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                 "style='clear: both;overflow: hidden; padding-bottom: 5px;'>"
                                                 ."No se puede agregar el producto ".$objProductoValidar->getDescripcionProducto().
                                                 ", debido a que la razón social del punto no esta considerada para la activación".
                                                 " de este tipo de producto</div></td></tr>";
                            }
                        }
                        
                    }
                }
                
                if( $boolContinuar )
                {
                    $arrayParametrosCaracteristicas['strDescCaracteristica'] = 'VALIDO_CIERTAS_PROVINCIAS';
                    $strValidoCiertasProvincias                              = $serviceUtilidades
                                                                               ->validarCaracteristicaProducto($arrayParametrosCaracteristicas);

                    //Verifico si la venta del producto está restringuido por provincias
                    if( !empty($strValidoCiertasProvincias) && $strValidoCiertasProvincias == "S" )
                    {
                        $objAdmiProducto = $em->getRepository('schemaBundle:AdmiProducto')->findOneById($productoId);

                        if( is_object($objAdmiProducto) )
                        {
                            $strCodigoProducto = $objAdmiProducto->getCodigoProducto();

                            if( !empty($strCodigoProducto) )
                            {
                                //Obtengo las provincias válidas para la venta del producto
                                $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PRODUCTOS_VALIDO_CIERTAS_PROVINCIAS',
                                                                      'COMERCIAL',
                                                                      'INGRESO_SERVICIO',
                                                                      '',
                                                                      $strCodigoProducto,
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      $strEmpresaCod);

                                if( !empty($arrayParametrosDet) )
                                {
                                    $strProvincias   = "";
                                    $arrayProvincias = array();

                                    foreach( $arrayParametrosDet as $arrayParametro )
                                    {
                                        if( isset($arrayParametro['valor2']) && !empty($arrayParametro['valor2']) )
                                        {
                                            if( !empty($strProvincias) )
                                            {
                                                $strProvincias .= ','; trim($arrayParametro['valor2']);
                                            }//( !empty($strProvincias) )

                                            $strProvincias     .= trim($arrayParametro['valor2']);
                                            $arrayProvincias[] = trim($arrayParametro['valor2']);
                                        }//( isset($arrayParametro['valor2']) && !empty($arrayParametro['valor2']) )
                                    }//foreach( $arrayParametrosDet as $arrayParametro )

                                    if( !empty($arrayProvincias) )
                                    {
                                        $arrayParametrosPuntoVentaExterna = array('arrayNombreProvincias' => $arrayProvincias,
                                                                                  'intIdPunto'            => $puntoId);

                                        $objInfoPunto = $em->getRepository('schemaBundle:InfoPunto')
                                                           ->validarProvinciaPuntoVentaExterna($arrayParametrosPuntoVentaExterna);

                                        if( !is_object($objInfoPunto) )
                                        {
                                            $boolContinuar   = false;
                                            $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' ".
                                                               "style='clear: both;overflow: hidden; padding-bottom: 5px;'>Este producto no puede ".
                                                               "ser vendido al cliente en sessi&oacute;n, puesto que existe restrincci&oacute;n de ".
                                                               "venta en la provincia a la que pertenece el punto.<br/>Las provincias permitidas ".
                                                               "para la venta del producto son: <b>".$strProvincias."</b></div></td></tr>";
                                        }//( !is_object($objInfoPunto) )
                                    }
                                    else
                                    {
                                        throw new \Exception('No se encontraron las provincias en las cuales se puede vender el producto.');
                                    }//( !empty($arrayProvincias) )
                                }
                                else
                                {
                                    throw new \Exception('No se encontraron los parámetros adecuados para validar la venta del producto.');
                                }//( !empty($arrayParametrosDet) )
                            }
                            else
                            {
                                throw new \Exception('El producto ('.$productoId.') no tiene un código asociado.');
                            }//( !empty($strCodigoProducto) )
                        }
                        else
                        {
                            throw new \Exception('No se encontró el producto con id ('.$productoId.')');
                        }//( is_object($objAdmiProducto) )
                    }//( !empty($strValidoCiertasProvincias) && $strValidoCiertasProvincias == "S" )
                }//( $boolContinuar )
            }
            else
            {
                throw new \Exception('No se encontró una empresa en sessión.');
            }//( !empty($strEmpresaCod) )
        }
        catch(\Exception $e)
        {
            $boolContinuar = false;

            if (is_array(unserialize($e->getMessage())))
            {
                $arrayError = unserialize($e->getMessage());
                $strMsg = "<tr>
                                <td colspan='4'>
                                    <div id=\"mensajeError\" class=\"alert alert-danger animated fadeIn\" role=\"alert\">
                                        <strong>ALERTA!</strong> ". $arrayError['msg']."
                                    </div>
                                </td>
                          </tr>";

            }
            else
            {
                $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' style='clear: both; overflow: hidden; " .
                    "padding-bottom: 5px;'>Hubo un problema al validar las caracter&iacute;sticas del producto</div></td>" .
                    "</tr>";
                $serviceUtil->insertError('Telcos+',
                    'InfoPlanCaracteristicaController:listarCaracteristicasPorProductoAction',
                    'Error al verificar las características asociadas al producto. - ' . $e->getMessage(),
                    $strUsrCreacion,
                    $strIpCreacion);
            }
        }
        /**
         * FIN BLOQUE VERIFICAR PRODUCTO
         */


        if( $boolContinuar )
        {
            $strRequiereUltimaMillaProd         = "SI";
            $arrayParamsCaractSinUltimaMilla    = array('intIdProducto'         => $productoId,
                                                        'strDescCaracteristica' => 'NO_REQUIERE_ULTIMA_MILLA',
                                                        'strEstado'             => 'Activo' );
            $strNoRequiereUltimaMillaProd       = $serviceUtilidades->validarCaracteristicaProducto($arrayParamsCaractSinUltimaMilla);

            if( !empty($strNoRequiereUltimaMillaProd) && $strNoRequiereUltimaMillaProd == "S" )
            {
                $strRequiereUltimaMillaProd = "NO";
            }

            $producto   = $em->getRepository('schemaBundle:AdmiProducto')->find($productoId);

            if(is_object($producto))
            {
                $strNombreTecnico = $producto->getNombreTecnico();
            }

            $i          = 0;
            $intJ       = 0;

            $strFuncionAux     = "";

            $strPresentarDiv = "<tr name='caracts'><td colspan='4'></td></tr>"; // Definción del ancho por defecto para la primera columna

            // Se muestra Div de mensaje informativo que el Cliente ya posee un Servicio FoxPremium en estado Cancel y que se procede
            // a tomar la informacion del LOGIN (USUARIO_FOX) y SUSCRIBER_ID (SSID_FOX) existente para el nuevo servicio FOX ingresado.
            if($srtDivMsjFoxExisteCancel!='')
            {
                $strPresentarDiv .= $srtDivMsjFoxExisteCancel;
            }
            //Se define campo oculto con el valor del estado inicial del producto.
            $strPresentarDiv .= "<tr name='caracts'><td><input type='hidden' "
                                ."value=" . $producto->getEstadoInicial() . " name='estadoInicial' id='estadoInicial'/>";

            $boolEsPesudoPe       = false;
            $objTipoMedioPseudoPe = null;
            $strUltimaMilla       = '';
            $boolEsDCHosting      = false;

            if($strPrefijoEmpresa == 'TN')
            {
                $objInfoPuntoDatoAdicional  = $em->getRepository("schemaBundle:InfoPuntoDatoAdicional")->findOneByPuntoId($puntoId);

                if(is_object($objInfoPuntoDatoAdicional) && $objInfoPuntoDatoAdicional->getElementoId())
                {
                    $intElementoEdificio = $objInfoPuntoDatoAdicional->getElementoId()->getId();

                    if($objInfoPuntoDatoAdicional->getDependeDeEdificio() == 'S' && $intElementoEdificio )
                    {
                        $objDetalleElementoAministra =    $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                            ->findOneBy(array('detalleNombre'  =>  'ADMINISTRA',
                                                                                              'estado'         =>  'Activo',
                                                                                              'elementoId'     =>  $intElementoEdificio
                                                                                             )
                                                                                       );

                        $objDetalleElementoEsPseudoPe =   $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                            ->findOneBy(array('detalleNombre'  =>  'TIPO_ELEMENTO_RED',
                                                                                              'estado'         =>  'Activo',
                                                                                              'elementoId'     =>  $intElementoEdificio
                                                                                             )
                                                                                       );
                        //Si existe registro de elemento red siginifica que es de tipo pseudoPe
                        if( (is_object($objDetalleElementoEsPseudoPe) && is_object($objDetalleElementoAministra)) &&
                            $objDetalleElementoEsPseudoPe->getDetalleValor() == 'PSEUDO_PE' &&
                            $objDetalleElementoAministra->getDetalleValor()  == 'CLIENTE')
                        {
                            $boolEsPesudoPe = true;
                        }

                        //Se busca que tipo de Ultima milla corresponde al flujo de acuerdo a la configuracion del edificio creado
                        if($boolEsPesudoPe)
                        {
                            //Se determina la Ultima Milla a ser usada para uno de los 2 esquemas de pseudope
                            $objDetalleElementoTipoAdmin  = $emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                              ->findOneBy(array('detalleNombre'  =>  'TIPO_ADMINISTRACION',
                                                                                                'estado'         =>  'Activo',
                                                                                                'elementoId'     =>  $intElementoEdificio
                                                                                               )
                                                                                         );
                            //Si es PseudoPe se muestra la ultima milla de acuerdo al tipo de administracion que tenga el edificio
                            $strPresentarDiv .= "<tr name='caracts'><td><label>* Última Milla:</label></td><td style='padding-left: 5px;'>"
                                             .  "<select name='ultimaMillaIdProd' id='ultimaMillaIdProd'>";
                            if(is_object($objDetalleElementoTipoAdmin))
                            {
                                //Obtengo la ultima milla de acuerdo al tipo de flujo pseudope que requiera ejecutarse
                                $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('FLUJO_PSEUDOPE',
                                                                      'COMERCIAL',
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      $objDetalleElementoTipoAdmin->getDetalleValor(),
                                                                      '',
                                                                      '',
                                                                      '',
                                                                      $strEmpresaCod);

                                if(!empty($arrayParametrosDet))
                                {
                                    foreach( $arrayParametrosDet as $arrayParametro )
                                    {
                                        if( isset($arrayParametro['valor1']) && !empty($arrayParametro['valor1']) )
                                        {
                                            $strUltimaMilla = $arrayParametro['valor1'];

                                            $objTipoMedioPseudoPe = $emInfraestructura->getRepository("schemaBundle:AdmiTipoMedio")
                                                                                      ->findOneByNombreTipoMedio($strUltimaMilla);
                                            if(is_object($objTipoMedioPseudoPe))
                                            {
                                                $strPresentarDiv .= "<option value='".$objTipoMedioPseudoPe->getId()."'>"
                                                                 .  "".$objTipoMedioPseudoPe->getNombreTipoMedio()."</option>";
                                            }
                                            else
                                            {
                                                $strPresentarDiv .= "<option value='0'>NA</option>";
                                            }
                                        }
                                        else
                                        {
                                            $strPresentarDiv .= "<option value='0'>NA</option>";
                                        }
                                    }
                                }
                                else
                                {
                                    $strPresentarDiv .= "<option value='0'>NA</option>";
                                }
                                
                                $arrayParamsVerificaUmTercerizada   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne('PARAMS_PRODS_TN_GPON',
                                                                                         '',
                                                                                         '',
                                                                                         '',
                                                                                         'PRODUCTOS_VERIFICA_UM_TERCERIZADA',
                                                                                         $producto->getId(),
                                                                                         '',
                                                                                         '',
                                                                                         '',
                                                                                         $strEmpresaCod);
                                if(isset($arrayParamsVerificaUmTercerizada) && !empty($arrayParamsVerificaUmTercerizada)
                                    && $objDetalleElementoTipoAdmin->getDetalleValor() === 'TERCERIZADA')
                                {
                                    $boolContinuar   = false;
                                    $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                        . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                        . "El producto no pudo ser instalado debido a que el punto su"
                                                        . " última milla es TERCERIZADO </div></td> </tr>";
                                    $strPresentarDiv = "";
                                }
                            }
                            $strPresentarDiv .= "</select>";
                        }
                    }
                }

                //Determinar tipo de Subgrupo del producto CLOUD IAAS
                if($strNombreTecnico == 'HOSTING' )
                {
                    //Determinar si el producto es el destinado a realizar Flujo
                    $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PRODUCTOS HOSTING RECURSOS',
                                                          'COMERCIAL',
                                                          '',
                                                          '',
                                                          $producto->getId(),
                                                          '',
                                                          '',
                                                          '',
                                                          '',
                                                          $strEmpresaCod);

                    if(!empty($arrayParametrosDet))
                    {
                        $boolEsDCHosting = true;
                    }
                }
            }

            //se verifica si el servicio es tipo de red GPON
            $booleanTipoRedGpon = false;
            if($strPrefijoEmpresa == 'TN' && !empty($strTipoRed))
            {
                $arrayParVerTipoRed = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne('NUEVA_RED_GPON_TN',
                                                                                                        'COMERCIAL',
                                                                                                        '',
                                                                                                        'VERIFICAR TIPO RED',
                                                                                                        'VERIFICAR_GPON',
                                                                                                        $strTipoRed,
                                                                                                        '',
                                                                                                        '',
                                                                                                        '');
                if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                {
                    $booleanTipoRedGpon = true;
                }
            }

            //Si el edificio tiene configuracion pseudope la ultima milla sera asignada automaticamente en la creacion del Servicio
            if(!$boolEsPesudoPe)
            {
                //  Se define el combo con las últimas millas para el servicio Enlace.
                if(($strPrefijoEmpresa !== 'TN' || ($producto->getEsEnlace() && $producto->getEsEnlace() == 'SI'))
                    || ($strPrefijoEmpresa === 'TN' && $producto->getNombreTecnico() === "IPSB")
                    || ($strPrefijoEmpresa === 'TNP' && $producto->getNombreTecnico() === "IPSB")
                  )
                {
                    $strCssVerFilaUltimaMilla   = "";
                    $strPresentarDivOption      = "";

                    if($strRequiereUltimaMillaProd == "NO")
                    {
                        $strCssVerFilaUltimaMilla = " style='display:none;' ";
                    }
                    else
                    {
                        //1- Se comprueba si es producto especial definido en la parametros con el objetivo de formar combo de UM.
                        $strPresentarDivOption      = "";
                        $strUltimaMilla             = "";
                        $strTipoNegocioIl           = "";
                        $boolEsProductoEspecial     = false;
                        $em_infraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
                        $arrayTipoMedio     = $em_infraestructura->getRepository('schemaBundle:AdmiTipoMedio')->findByEstado('Activo');

                        //Se obtienen la ultimas milla correspondiente al producto especial.
                        $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PRODUCTOS_ESPECIALES_UM',
                                                                 'COMERCIAL',
                                                                 '',
                                                                 '',
                                                                 strtoupper(trim($producto->getNombreTecnico())),
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $strEmpresaCod);

                        foreach($arrayTipoMedio as $entityTipoMedio)
                        {
                            $intIdTipoMedio     = $entityTipoMedio->getId();
                            $strNombreTipoMedio = $entityTipoMedio->getNombreTipoMedio();

                            if($arrayParametrosDet && count($arrayParametrosDet) > 0)
                            {
                                if(isset($arrayParametrosDet['valor2']) && !empty($arrayParametrosDet['valor2']) &&
                                    $arrayParametrosDet['valor2'] == $strNombreTipoMedio)
                                {
                                    $strUltimaMilla = $arrayParametrosDet['valor2'];
                                    $strTipoNegocioIl = $arrayParametrosDet['valor5'];
                                    $strPresentarDivOption = "<option value='$intIdTipoMedio'>$strUltimaMilla</option>";
                                }

                                if(!empty($strPresentarDivOption))
                                {
                                    $boolEsProductoEspecial = true;
                                    break;
                                }
                            }
                            else
                            {
                                break;
                            }
                        }

                        if(is_object($producto)
                            && ($producto->getNombreTecnico() === 'INTERNET SMALL BUSINESS' || $producto->getNombreTecnico() === 'TELCOHOME'))
                        {
                            $strEsIsB = 'SI';

                            //Obtengo el tipo de negocio del punto.
                            $objGrupoNegocio= $em->getRepository('schemaBundle:InfoPunto')->getGrupoNegocioByPuntoId($puntoId);

                            if(is_object($objGrupoNegocio))
                            {
                                $strNombreTipoNegocioPto = $objGrupoNegocio->getNombreTipoNegocio();
                            }

                            //Se obtienen los tipos de negocio restringidos para el producto 'INTERNET SMALL BUSINESS'
                            $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('RESTRICCION_TIPO_NEGOCIO',
                                                                      'COMERCIAL',
                                                                      '',
                                                                      'TIPO_NEGOCIO',
                                                                      '',
                                                                      '',
                                                                      $strPrefijoEmpresa,
                                                                      $strEmpresaCod,
                                                                      '',
                                                                      '');

                            if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
                            {
                                foreach($arrayAdmiParametroDet as $arrayParametro)
                                {
                                    if (isset($arrayParametro['valor1']) && ($arrayParametro['valor1'] != null))
                                    {
                                        $strTipoNegociosRestringidos .= $arrayParametro['valor1'] .'|';
                                    }
                                }//( $arrayAdmiParametroDet as $arrayParametro )

                                $strTipoNegociosRestringidos = (isset($strTipoNegociosRestringidos) && ($strTipoNegociosRestringidos != null)) ?
                                                                substr($strTipoNegociosRestringidos, 0, strlen($strTipoNegociosRestringidos)-1 ) : '';
                            }//($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
                            
                            //Validación del producto SMALL BUSINESS CENTROS COMERCIAL el punto sea un centro comercial.
                            $arrayParamsVerificaCouFijaSmb  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('PARAMS_PRODS_TN_GPON',
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 'PRODUCTOS_VERIFICA_FACTIB_CC',
                                                                                 $producto->getId(),
                                                                                 '',
                                                                                 '',
                                                                                 '',
                                                                                 $strEmpresaCod);
                            if(isset($arrayParamsVerificaCouFijaSmb) && !empty($arrayParamsVerificaCouFijaSmb))
                            {
                                $boolValidacionEdificio     = false;
                                $objInfoPuntoDatoAdicional  = $em->getRepository("schemaBundle:InfoPuntoDatoAdicional")->findOneByPuntoId($puntoId);

                                if(is_object($objInfoPuntoDatoAdicional) && $objInfoPuntoDatoAdicional->getDependeDeEdificio() == 'S'
                                   && is_object($objInfoPuntoDatoAdicional->getElementoId()))
                                {
                                    $strModeloElemento  = $objInfoPuntoDatoAdicional
                                                           ->getElementoId()
                                                           ->getModeloElementoId()
                                                           ->getNombreModeloElemento();
                                    if(isset($strModeloElemento) || $strModeloElemento === 'CENTRO COMERCIAL')
                                    {
                                        $boolValidacionEdificio = true;
                                    }
                                }
                                else
                                {
                                    $boolValidacionEdificio = false;
                                }
                                if(!$boolValidacionEdificio)
                                {
                                    $boolContinuar   = false;
                                    $strMsg          = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                        . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                        . "El producto no pudo ser instalado debido a que el punto no es"
                                                        . " un centro comercial</div></td> </tr>";
                                    $strPresentarDiv = "";
                                }
                            }
                        }

                        if(!$boolEsProductoEspecial)
                        {
                            $boolEsFlujoDC = false;

                        //Se obtienen las ultimas millas que no seran mostradas dentro de un Flujo Normal
                            $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('ULTIMAS_MILLLAS_EXCEPCIONES',
                                                                  'COMERCIAL',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $strEmpresaCod);

                            //Verificar si es producto complemantario a HOUSING y HOSTING
                            if($producto->getGrupo()    && 
                               strpos($producto->getGrupo(),'DATACENTER')!==false && 
                               $strNombreTecnico == 'INTERNETDC' || $strNombreTecnico == 'DATOSDC' || $strNombreTecnico == 'INTERNET DC SDWAN'
                               || $strNombreTecnico == 'DATOS DC SDWAN' )
                            {
                                $strPresentarDiv .= "<tr name='caracts'><td><label>* Tipo Solución:</label></td>"
                                                 .  "<td style='padding-left: 5px;'>";
                                $strPresentarDiv .= "<select class='tipoSolucion' name='tipoSolucion' id='tipoSolucion' onchange='setUM()'>"
                                                  . "<option value='0'>Seleccione</option>";
                                $strPresentarDiv .= "<option value='HOUSING'>HOUSING</option><option value='HOSTING'>HOSTING</option>";
                                $strPresentarDiv .= "</select></td></tr>";

                                $boolEsFlujoDC        = true;

                                $arrayParametrosDetDC = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->get('ULTIMAS MILLAS INTERNET Y DATOS',
                                                                        'COMERCIAL',
                                                                        '',
                                                                        $producto->getDescripcionProducto(),
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        '',
                                                                        $strEmpresaCod);
                            }
                            if($booleanTipoRedGpon)
                            {
                                $arrayParametrosTipoRed = array('intIdProducto'         => $productoId,
                                                                'strDescCaracteristica' => 'TIPO_RED',
                                                                'strEstado'             => 'Activo');
                                $strEsProductoGpon      = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosTipoRed);
                                if(!empty($strEsProductoGpon) && $strEsProductoGpon == 'S')
                                {
                                    $arrayParametrosDetGPON = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('NUEVA_RED_GPON_TN',
                                                                              'COMERCIAL',
                                                                              '',
                                                                              '',
                                                                              $producto->getNombreTecnico(),
                                                                              '',
                                                                              '',
                                                                              '',
                                                                              'ULTIMA_MILLA_GPON_TN',
                                                                              $strEmpresaCod);
                                }
                            }
                            foreach($arrayTipoMedio as $entityTipoMedio)
                            {
                                $idTipoMedio      = $entityTipoMedio->getId();
                                $strNombre        = $entityTipoMedio->getNombreTipoMedio();

                                /*
                                 * Se valida para que los productos con tipo de Red GPON, la lista de últma milla solo sean con las que
                                 * se encuentran disponibles en el listado del detalle del parametro 'ULTIMA_MILLA_GPON_TN'.
                                */
                                if(isset($arrayParametrosDetGPON) && !empty($arrayParametrosDetGPON) && is_array($arrayParametrosDetGPON))
                                {
                                    foreach($arrayParametrosDetGPON as $arrayItemGPON)
                                    {
                                        if($arrayItemGPON['valor2'] == $strNombre && $arrayItemGPON['estado']=='Activo')
                                        {
                                            $strTempPresentarDivOption .= "<option value='$idTipoMedio'>$strNombre</option>";
                                        }
                                    }
                                    if(!empty($strTempPresentarDivOption))
                                    {
                                        $strPresentarDivOption = $strTempPresentarDivOption;
                                    }
                                }
                                if(!$boolEsFlujoDC)
                                {
                                    $boolSeMuestraUm  = true;

                                    if(!empty($arrayParametrosDet))
                                    {
                                        foreach($arrayParametrosDet as $arrayParametro)
                                        {
                                            if(isset($arrayParametro['valor1']) && !empty($arrayParametro['valor1']))
                                            {
                                                if($arrayParametro['valor1'] == $strNombre)
                                                {
                                                    $boolSeMuestraUm = false;
                                                }
                                            }
                                        }
                                    }
                                }
                                else//si el flujo es de DC ( internet o datos ) solo se mostrara las ultimas millas configuradas en los parametros
                                {
                                    $boolSeMuestraUm = false;

                                    if(!empty($arrayParametrosDetDC))
                                    {
                                        foreach($arrayParametrosDetDC as $arrayParametro)
                                        {
                                            if(isset($arrayParametro['valor1'])  &&
                                               !empty($arrayParametro['valor1']) &&
                                               $arrayParametro['valor1'] == $strNombre
                                              )
                                            {
                                                if($arrayParametro['valor1'] == $strNombre)
                                                {
                                                    $boolSeMuestraUm = true;
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }

                                if($boolSeMuestraUm)
                                {
                                    $strPresentarDivOption .= "<option value='$idTipoMedio'>$strNombre</option>";
                                }
                            }
                        }//(!$boolEsProductoEspecial)
                    }

                    $strCaractDisabled = '';

                    if($boolEsFlujoDC)
                    {
                        $strCaractDisabled = 'disabled';
                    }

                    $strPresentarDiv .= "<tr".$strCssVerFilaUltimaMilla." name='caracts'><td><label>* Última Milla:</label></td>"
                                        ."<td style='padding-left: 5px;'>";
                    $strPresentarDiv .= "<select  $strCaractDisabled name='ultimaMillaIdProd' id='ultimaMillaIdProd'><option value='0'>Seleccione</option>";
                    $strPresentarDiv .= $strPresentarDivOption;
                    $strPresentarDiv .= "</select>";
                    if(($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN'  ) 
                        && true === $this->get('security.context')->isGranted('ROLE_431-7759') && ($puntoId != null || !empty($puntoId))  )
                    {
                        $intPuntoAdicional = $emComercial->getRepository('schemaBundle:AdmiGrupoPromocion')
                                                         ->validaPuntoAdicional(array( "intIdPunto" => $puntoId));
                        
                        if ($intPuntoAdicional > 0)
                        {
                            $strPresentarDiv .= '<tr name = "caracts">'.
                                                            '<td>'.
                                                            '<label for="cantidad">Código por mensualidad:</label>'.
                                                            '</td>'.
                                                            '<td style="padding-left: 5px;" >'.
                                                            '<input type="text" name="PROM_MPRO" id="PROM_MPRO" onchange="validaCodigo(this.id)">'.
                                                            '</td>'.
                                                            '</tr>'.
                                                            '<script>'.
                                                            ' strCodigoPromocion = ""; strNombrePromocion = ""; strTipoPromocion = "";</script>';
                        }
                        else
                        {
                            $objParametroCabCod = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                            ->findOneBy( array('nombreParametro' => 'PROM_PARAMETROS',
                                                                               'estado'          => 'Activo',
                                                                               'modulo'          => 'COMERCIAL'));

                            if(is_object($objParametroCabCod))
                            {
                                $objServicioInternetCod = $em->getRepository('schemaBundle:InfoServicio')
                                                             ->obtieneServicioInternetxPunto($puntoId);
                                if (is_object($objServicioInternetCod))
                                {
                                    $arrayParametrosDetCod = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                       ->findOneBy( array('parametroId' => $objParametroCabCod,
                                                                                          'descripcion' => 'PROM_ESTADOS_PROD_INTD',
                                                                                          'valor1'      => $objServicioInternetCod->getEstado(),
                                                                                          'estado'      => 'Activo'));
                                    if (is_object($arrayParametrosDetCod))
                                    {
                                        $strPresentarDiv .= '<tr name = "caracts">'.
                                                            '<td>'.
                                                            '<label for="cantidad">Código por mensualidad:</label>'.
                                                            '</td>'.
                                                            '<td style="padding-left: 5px;" >'.
                                                            '<input type="text" name="PROM_MPRO" id="PROM_MPRO" onchange="validaCodigo(this.id)">'.
                                                            '</td>'.
                                                            '</tr>'.
                                                            '<script>'.
                                                            ' strCodigoPromocion = ""; strNombrePromocion = ""; strTipoPromocion = "";</script>';
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    // Valor null para el dato última milla en producto que no requiere enlace
                    $strPresentarDiv .= '<tr name="caracts"><td colspan="4"><input type="hidden" value="" name="ultimaMillaIdProd" '.
                                        'id="ultimaMillaIdProd"/>';
                }
            }

            $strPresentarDiv .= "</td><td></td><td></td></tr>";

            if( $producto->getNombreTecnico() == "FINANCIERO" )
            {
                $strPresentarDiv .= '<tr id="lb_cantidad" name="caracts">'
                                    .'<td>'
                                        .'<label class="required" >* Cantidad:</label></td><td>'
                                        .'<input type="text" class="campo-obligatorio" name="cantidad" id="cantidad" '
                                                .'onkeypress="return validaSoloNumeros(event, this);" value="1"/></td><td>'
                                        .'<input type="hidden" value="0" name="cantidad_caracteristicas" id="cantidad_caracteristicas"/>'
                                    .'</td><td></td><td></td>'
                                    .'</tr>'.
                                    '<tr id="lb_precio" name="caracts">'
                                    .'<td>'
                                        .'<label class="required" >* Precio:</label></td><td>'
                                        .'<input type="text" class="campo-obligatorio" name="precio_unitario" id="precio_unitario" '
                                                .'onkeypress="return validaNumerosConDecimales(event, this);"/></td><td>'
                                    .'</td><td></td><td></td>'
                                    .'</tr>';
            }
            else
            {
                $strFuncionPrecio = $producto->getFuncionPrecio();

                if($strFuncionPrecio != "" )
                {
                    // Se procesa la indentación de la Función precio para mostrarla en una ventana modal.
                    $strFuncionAux    = $strFuncionPrecio;
                    $strFuncionPrecio = ' '.preg_replace('/\s+/', ' ', $strFuncionPrecio); // Se ajustan los espacios dobles a un espacio

                    $strPresentarDiv.= "<tr name='caracts'><td><label>Funci&oacute;n precio:</label></td><td style='padding-left: 5px;'>";
                    $strPresentarDiv.= '<textarea  id = "textarea" readonly="readonly" rows="1" cols="45" style="overflow:hidden; resize:none">';

                    if(strlen($strFuncionAux) > 55)
                    {
                        // Se muestran solo 70 caracteres
                        $strPresentarDiv.= substr(preg_replace('/\r\n/', ' ', preg_replace('/\s+/', ' ', $strFuncionAux)), 0, 51) . ' ...';
                    }
                    else
                    {
                        $strPresentarDiv.= $strFuncionAux;
                    }

                    $strPresentarDiv.= "</textarea></td><td style='vertical-align:top;'>";
                    // Componentes para la presentación en modo popup de la función precio organizada
                    $strPresentarDiv.=  '<a class="btn btn-default btn-sm" href="#openModal" role="button">
                                        <i class="fa fa-search" aria-hidden="true"></i> Ver Función</a>'
                                        . ' <div id="openModal" class="modalDialog"> '
                                        . '     <div>'
                                        . '         <a href="#close" title="Close" class="close">X</a> '
                                        . '         <textarea readonly="readonly" rows="25" cols="80"> '
                                        .           $this->tabularFuncionPrecio($strFuncionPrecio)
                                        . '         </textarea> '
                                        . '     </div> '
                                        . ' </div> ';
                    $strPresentarDiv.= "</td><td></td></tr>";
                }

                //se valida cantidad permitida del servicio por punto
                $intMaximoProdPermitido = 0;
                if(is_object($producto))
                {
                    $arrayParProductoPermitido = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                 'COMERCIAL',
                                                                 '',
                                                                 '',
                                                                 $producto->getId(),
                                                                 'PRODUCTOS_PERMITIDOS',
                                                                 '',
                                                                 '',
                                                                 '',
                                                                 $strEmpresaCod);
                    if(isset($arrayParProductoPermitido) && !empty($arrayParProductoPermitido) && isset($arrayParProductoPermitido['valor3']))
                    {
                        $intMaximoProdPermitido = $arrayParProductoPermitido['valor3'];
                    }
                }
                $strPresentarDiv.= "<tr name='caracts'><td><label>Cantidad:</label></td><td>";

                if($strPrefijoEmpresa === 'TN' || $strPrefijoEmpresa === 'TNP' || $strPrefijoEmpresa === 'TNG')
                {
                    if($intMaximoProdPermitido == 1)
                    {
                        $strPresentarDiv .= "<input type='text' value='1' name='cantidad' id='cantidad' readonly/></td><td></td><td></td></tr>";
                    }
                    elseif($intMaximoProdPermitido > 1)
                    {
                        $strPresentarDiv.= "<input type='number' value='1' name='cantidad' id='cantidad' min='1' max='$intMaximoProdPermitido' ".
                                           "step='1' onchange='actualizaTotal()' onkeypress='return validaSoloNumeros(event);'/>".
                                           "</td><td></td><td></td></tr>";
                    }
                    else
                    {
                        $strPresentarDiv.= "<input type='text' value='1' name='cantidad' id='cantidad' onkeypress='return validaSoloNumeros(event);'
                                                   onchange='actualizaTotal()'/></td><td></td><td></td></tr>";
                    }
                }
                else
                {
                    //Se valida si el producto pertenece al grupo KONIBIT.
                    $strProdKonibit = "NO";
                    $strCaracMaxKonibit = "NO";
                    $strProCaracMaxKonibit = "NO";
                    $strProCaracCompMaxKonibit = "NO";
                    $intMaximoProdPermitido = 0;
                    $emGeneral = $this->get('doctrine')->getManager('telconet_general');
                    $arrayListadoServicios = array();
                    
                    $arrayListadoServicios = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                             'Lista de productos adicionales automaticos',
                                                             '','','','','',$strEmpresaCod);
                    
                    foreach($arrayListadoServicios as $objListado)
                    {
                        // Activacion primero en Konibit si el productos tiene esa caracteristica
                        if ($productoId == $objListado['valor1'] && 
                            $objListado['valor3'] == "SI")
                        {
                            $strProdKonibit = "SI";
                            break;
                        }
                    }
                    if($strProdKonibit == "SI")
                    {
                        $objCaractMaxKonibit = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array("descripcionCaracteristica" => "CANT MAX KONIBIT",
                                                                             "estado" => "Activo"));
                        if(is_object($objCaractMaxKonibit))
                        {
                            $strCaracMaxKonibit = "SI";
                        }
                        
                        if($strCaracMaxKonibit == "SI")
                        {
                            $objProdCaracMaxkonibit = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array("productoId"        => $producto->getId(),
                                                                                  "caracteristicaId"  => $objCaractMaxKonibit->getId(),
                                                                                  "visibleComercial"  => "SI",
                                                                                  "estado"            => "Activo"));
                            if(is_object($objProdCaracMaxkonibit))
                            {
                                $strProCaracMaxKonibit= "SI";
                            }
                        }
                        
                        if ($strProCaracMaxKonibit == "SI")
                        {
                            $objProdCaractCompMaxKonibit = $em->getRepository('schemaBundle:AdmiProdCaracComp')
                                                              ->findOneBy(array('productoCaracteristicaId'   => $objProdCaracMaxkonibit->getId(),
                                                                                'estado'                     => 'Activo'));
                            
                            if(is_object($objProdCaractCompMaxKonibit) && ($objProdCaractCompMaxKonibit->getValoresDefault()))
                            {
                                $strProCaracCompMaxKonibit = "SI"; 
                            }
                        }
                        
                        if ($strProCaracCompMaxKonibit == "SI")
                        {
                            $intMaximoProdPermitido = $objProdCaractCompMaxKonibit->getValoresDefault();
                            $strPresentarDiv.= "<input type='number' value='1' name='cantidad' id='cantidad' min='1' max='$intMaximoProdPermitido' ".
                                               "step='1' onKeyup='cantMaxKonibit()' onkeypress='return validaSoloNumeros(event);'/>".
                                               "</td><td></td><td></td></tr>";
                        }else
                        {
                            $strPresentarDiv.= "<input type='text' value='1' name='cantidad' id='cantidad'/></td><td></td><td></td></tr>";
                        }
                    }
                    else
                    {
                        $strAtributoReadOnly = "";
                        //Verificamos si el producto es de tipo NETWIFI entonces, agregamos atributo readonly en el campo cantidad
                        if( $strPrefijoEmpresa == 'MD' && ($producto->getNombreTecnico() == 'NETWIFI' || $producto->getRequierePlanificacion() == "SI"))
                        {
                            $strAtributoReadOnly = " readonly='readonly' ";
                        }

                        $strPresentarDiv.= "<input type='text' value='1' name='cantidad' id='cantidad' /*onkeypress='validate(event)*/'
                                            $strAtributoReadOnly /></td><td></td><td></td></tr>";
                    }
                }

                //Determinar si un producto es multi-caracteristica y requiere una configuración especial
                $boolMultipleCaracteristica = $serviceTecnico->isContieneCaracteristica($producto,'ES_MULTIPLE_CARACTERISTICAS');
                $strEsPoolCompleto          = 'NO';

                //Validar que tipo de elemento para POOL de Recursos es el Producto
                if($boolMultipleCaracteristica)
                {
                    $boolEsPoolRecursos = $serviceTecnico->isContieneCaracteristica($producto,'ES_POOL_RECURSOS');
                    $boolEsLicenciamiento = $serviceTecnico->isContieneCaracteristica($producto,'ES_LICENCIAMIENTO_SO');

                    if($boolEsPoolRecursos)
                    {
                        $strEsPoolCompleto = 'SI';
                    }
                }
                else
                {
                    $boolEsCoreHousing = $serviceTecnico->isContieneCaracteristica($producto,'ES_HOUSING');
                }

                $strEsCore = $boolEsPoolRecursos || $boolEsCoreHousing ? "S" : "N";

                $arrayValidaFlujoAntivirus  = $serviceLicenciasKaspersky->validaFlujoAntivirus(array(   "intIdPunto"        => $puntoId,
                                                                                                        "strOpcionConsulta" => $strOpcionConsulta,
                                                                                                        "strCodEmpresa"     => $strEmpresaCod
                                                                                                    ));
                $strFlujoAntivirus          = $arrayValidaFlujoAntivirus["strFlujoAntivirus"];
                $strValorAntivirus          = $arrayValidaFlujoAntivirus["strValorAntivirus"];
                $arrayCaracteristica = array();

                //verifico si es gpon y agrego la característica velocidad gpon
                $booleanValidVelCapGpon = false;
                if($booleanTipoRedGpon)
                {
                    $objCaractVelGpon = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                           ->findOneBy(array("descripcionCaracteristica" => "VELOCIDAD_GPON",
                                                                             "estado" => "Activo"));
                    if(is_object($objCaractVelGpon))
                    {
                        $objProdCaracVelGpon = $emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findOneBy(array("productoId"        => $producto->getId(),
                                                                            "caracteristicaId"  => $objCaractVelGpon->getId(),
                                                                            "visibleComercial"  => "NO",
                                                                            "estado"            => "Activo"));
                        if(is_object($objProdCaracVelGpon))
                        {
                            $items[] = $objProdCaracVelGpon;
                            $booleanValidVelCapGpon = true;
                        }
                    }
                }
                //extraemos información del producto parametrizado. 
                $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                             ->findOneBy( array( 'nombreParametro' => 'PARAMETRO_PRODUCTO_FACTIBILIDAD',
                                                                                 'estado' => 'Activo' ) );
                $strDescripcionProducto  = "";
                if( is_object($objParametroCab))
                {
                    $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy( array(  'parametroId' => $objParametroCab->getId(),
                                                                         'estado'      => 'Activo' ));
                    if($arrayParametrosDet)
                    {
                        $strDescripcionProducto = $arrayParametrosDet->getValor1();
                    }
                }
                foreach( $items as $item )
                {
                    $strStyleRow = "";
                    //Cambio McAfee - Para caracteristica de correo electronico se carga automaticamente el correo registrado del cliente
                    $strValorCaracteristica = "";
                    $strCampoHabilitado     = "";
                    $strClassOpcion         = "";
                    $booleanProdCaractRel   = false;
                    //validar class opcion - permite el ingreso de cualquiera de la característica que contenga esa clase
                    $arrayValidarClassOpcion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('PRODUCTO_CARACTERISTICA_CLASS_RELACION',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                '',
                                                                                $item->getId(),
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $strEmpresaCod);
                    if(isset($arrayValidarClassOpcion) && !empty($arrayValidarClassOpcion) && isset($arrayValidarClassOpcion['valor2']))
                    {
                        $strClassOpcion = "class='".$arrayValidarClassOpcion['valor2']."'";
                    }
                    //validar función por relación de producto características
                    $arrayProdCaractRelacion = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get('PRODUCTO_CARACTERISTICA_RELACION_PRODUCTO',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                '',
                                                                                $item->getId(),
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $strEmpresaCod);
                    if(isset($arrayProdCaractRelacion) && !empty($arrayProdCaractRelacion)
                        && is_array($arrayProdCaractRelacion))
                    {
                        $booleanProdCaractRel = true;
                    }

                    if ($item->getCaracteristicaId()->getDescripcionCaracteristica()=="CORREO ELECTRONICO")
                    {
                        if (!empty($puntoId))
                        {
                            if($strFlujoAntivirus === "NUEVO")
                            {
                                $strValorCaractPlanProducto = $serviceLicenciasKaspersky->getCorreoLicencias(array( "intIdPunto"        =>  
                                                                                                                    $puntoId,
                                                                                                                    "strUsrCreacion"    => 
                                                                                                                    $strUsrCreacion,
                                                                                                                    "strIpCreacion"     =>
                                                                                                                    $strIpCreacion
                                                                                                                  ));
                            }
                            else
                            {
                                //Cambio McAfee - se obtiene correo en caso de que el producto sea Internet Protegido
                                $strValorCaractPlanProducto = $serviceTecnico->getCorreoDatosEnvioMd(array("intIdPunto"            => 
                                                                                                           $puntoId,
                                                                                                           "strValidaCorreoMcAfee" => 
                                                                                                           "SI",
                                                                                                           "strUsrCreacion"       =>
                                                                                                           $strUsrCreacion,
                                                                                                           "strIpCreacion"       =>
                                                                                                           $strIpCreacion
                                                                                                          ));
                            }
                            $strValorCaracteristica = $strValorCaractPlanProducto;
                        }
                        $strStyleRow    = " style = 'margin-top: 5px; margin-bottom: 5px; ' ";
                    }
                    else if ($item->getCaracteristicaId()->getDescripcionCaracteristica()=="TIENE INTERNET")
                    {
                        $strValorCaracteristica = "\"SI\"";
                        $strCampoHabilitado     = " ";
                    }
                    else if ($item->getCaracteristicaId()->getDescripcionCaracteristica() === "ANTIVIRUS")
                    {
                        if((isset($strOpcionConsulta) && !empty($strOpcionConsulta) 
                            && ($strOpcionConsulta === "CREAR_PLAN" || $strOpcionConsulta === "CLONAR_PLAN"))
                            || $strFlujoAntivirus === "NUEVO")
                        {
                            $strCampoHabilitado     = " disabled";
                            $strStyleRow            = " style='display:none;' ";
                            $strValorCaracteristica = $strValorAntivirus;
                        }
                        else
                        {
                            continue;
                        }
                    }
                    else if($item->getCaracteristicaId()->getDescripcionCaracteristica()=='RELACION_INTERNET_WIFI')
                    {
                        /* Se valida que la característica sea RELACION_INTERNET_WIFI, para no presentarla, debido a que es una característica
                           exclusiva de los L3MPLS que se crean para navegación y administración del producto Internet Wifi en esquema 2. */
                        continue;
                    }
                    else if ($item->getCaracteristicaId()->getDescripcionCaracteristica()=="ES_GRATIS"
                        && ($producto->getNombreTecnico() === "EXTENDER_DUAL_BAND" || $producto->getNombreTecnico() === "WIFI_DUAL_BAND"
                            || $producto->getNombreTecnico() === "WDB_Y_EDB"))
                    {
                        $strValorCaracteristica = "\"NO\"";
                        if (!empty($strTipoProceso) && $strTipoProceso == "CrearServicio")
                        {
                            $strCampoHabilitado     = " disabled";
                        }
                    }
                    else if ($item->getCaracteristicaId()->getDescripcionCaracteristica()=="MAC"
                        && $producto->getNombreTecnico() === "EXTENDER_DUAL_BAND")
                    {
                        $strStyleRow            = " style='display:none;' ";
                    }
                    else if ($item->getCaracteristicaId()->getDescripcionCaracteristica()=="TIPO_FACTIBILIDAD"
                            && ($producto->getNombreTecnico() === "INTERNET SMALL BUSINESS" 
                            || $producto->getNombreTecnico()  === "TELCOHOME"
                            || $producto->getNombreTecnico()  === "DATOS SAFECITY"
                            || $producto->getDescripcionProducto() === $strDescripcionProducto))
                    {
                        $strValorCaracteristica = "RUTA";
                        $strStyleRow            = " style='enable:false;' ";
                        $strCampoHabilitado     = " disabled ";
                    }
                    else if ($item->getCaracteristicaId()->getDescripcionCaracteristica()=="VELOCIDAD" && $producto->getNombreTecnico() === "IPSB")
                    {
                        $strValorCaracteristica = "\"SI\"";
                        $strCampoHabilitado     = " disabled ";
                    }
                    else if($item->getCaracteristicaId()->getDescripcionCaracteristica() == "METRAJE_NETFIBER"
                        && $producto->getNombreTecnico() === "NETFIBER")
                    {
                        //Obtengo para el producto NETFIBER la cantidad de metraje inicial que se cobra al cliente y que se encuentra incluido
                        // en el KIT del producto NETFIBER, y obtengo el costo por metraje adicional para mostrar mensaje por pantalla.
                        $arrayParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->getOne('FACTURABLES_FACTURACION_UNICA',
                                                                  'FACTURACION',
                                                                  'FACTURACION_UNICA',
                                                                  '',
                                                                  $item->getId(),
                                                                  'METRAJE_NETFIBER',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $strEmpresaCod);
                        //Obtengo el Valor a Cobrarse por metro adicional que se encuentra parametrizado para el producto NETFIBER
                        $strValorMetroAdicional     = ( isset($arrayParametroDet["valor4"]) && !empty($arrayParametroDet["valor4"]) )
                                                       ? $arrayParametroDet["valor4"] : "";
                        //Obtengo la cantidad de metraje inicial que no se cobra al cliente y que esta incluido en el KIT del Producto NETFIBER
                        $strValorCaracteristica     = ( isset($arrayParametroDet["valor5"]) && !empty($arrayParametroDet["valor5"]) )
                                                       ? $arrayParametroDet["valor5"] : "";

                        $strCaractMetrajeNetFiber  = $item->getCaracteristicaId()->getDescripcionCaracteristica();
                        $strDivMsjNetFiber = "<tr name='caracts'><td colspan='2'>".
                                           "<table border='0' width='50%'><tr><td><div id='mensajeNetfiber' class='successmessage'> ".
                                           "Producto <b>[".$producto->getDescripcionProducto(). "]</b> ".
                                           "posee metraje inicial de [".$strValorCaracteristica."]</b> metros incluidos en el Kit, ".
                                           "el valor a facturarse por metro adicional es <b>[".$strValorMetroAdicional."]</b>".
                                           "</div></td></tr>".
                                           "<tr><td>&nbsp;&nbsp;</td></tr>".
                                           "<tr><td><input type='hidden' value='".$strValorCaracteristica."' ".
                                           "name='c_$strCaractMetrajeNetFiber' id='c_$strCaractMetrajeNetFiber'/>".
                                           "</td></tr></table>".
                                           "</td></tr>";
                    }

                    //validar si la característica posee otra relacionada
                    if($booleanProdCaractRel)
                    {
                        $strPresentarDiv .= "<tr class='tr-prod-caract-relacion' name='caracts' ".$strStyleRow."><td>";
                    }
                    else
                    {
                        $strPresentarDiv .= "<tr name='caracts' ".$strStyleRow."><td>";
                    }

                    //Verificar si el producto es de tipo CLOUD PUBLIC para poder validar que el cliente o punto contenga una
                    //forma de pago de tipo DEBITO o TARJETA
                    $boolFacturaPorConsumo = $serviceTecnico->isContieneCaracteristica($producto,'ES_IAAS_PUBLIC');

                    //Validar para productos de Facturación por consumo que verifique si el cliente tiene deuda o no para poder continuar
                    if($boolFacturaPorConsumo)
                    {
                        //Verificación de deuda pendiente
                        $arrayParametrosVerificaDeuda               = array();
                        $arrayParametrosVerificaDeuda['intIdPunto'] = $puntoId;
                        $arraySaldoCliente = $emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                          ->obtieneDeudaPorCliente($arrayParametrosVerificaDeuda);

                        if(!empty($arraySaldoCliente) && $arraySaldoCliente['saldoTotal']>0)
                        {
                            $boolClienteTieneDeuda = true;
                        }
                    }
                    if($boolClienteTieneDeuda)
                    {
                        $arrayValidacionCLoudIaas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->get('VALIDACION_CLOUD_IAAS', 
                                                                    'COMERCIAL',
                                                                    '',
                                                                    'PERMITIR_CLT_DEUDA',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strEmpresaCod);
                        $boolClienteTieneDeuda = (!empty($arrayValidacionCLoudIaas) && is_array($arrayValidacionCLoudIaas)
                                                 && $arrayValidacionCLoudIaas[0]["valor1"] == "SI") ? false:true;
                    }
                    $strDescripcionCaracteristica = $item->getCaracteristicaId()->getDescripcionCaracteristica();
                    $strCaracteristicaObligatoria = '';
                    if(  (($objParametroDetValProd) 
                        && ($strTipoProducto == $strValorProductoPaqHoras) || ($strTipoProducto == $strValorProductoPaqHorasRec))
                    
                        && (  ($strDescripcionCaracteristica == "Cantidad de meses")|| ($strDescripcionCaracteristica == "Cantidad de horas") 
                           || ($strDescripcionCaracteristica === "Forma de soporte") || ($strDescripcionCaracteristica === "Acceso a soporte")
                            ) 
                        )
                        {
                            $strCaracteristicaObligatoria = '* ';
                        
                    }

                    if($item->getCaracteristicaId()->getTipoIngreso() == 'S' )
                    {
                        // Si tengo el Punto Login y posee las caracteristicas Zona o Grupo de Negocio debo cargar los valores de la
                        // Caracteristica en base a la informacion del Punto
                        if(($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP' || $strPrefijoEmpresa == 'TNG') && $puntoId
                            && ($strDescripcionCaracteristica == "Grupo Negocio" || $strDescripcionCaracteristica == "Zona"))
                        {
                            if($strDescripcionCaracteristica == "Grupo Negocio")
                            {
                                $objGrupoNegocio= $em->getRepository('schemaBundle:InfoPunto')->getGrupoNegocioByPuntoId($puntoId);
                                if($objGrupoNegocio && $producto->getNombreTecnico() != 'INTERNET SMALL BUSINESS'
                                    && $producto->getNombreTecnico() != 'TELCOHOME')
                                {

                                    $strCampoHabilitado     = " disabled ";
                                    $strPresentarDiv .= "<label id='lb_caracteristicas_$i'>" . $strDescripcionCaracteristica
                                                        .":</label> </td><td> <input type='text' value='".$objGrupoNegocio->getGrupoNegocio()."' "
                                                        .$strCampoHabilitado."name='caracteristicas_$i' id='caracteristicas_$i'/>";
                                }
                                else
                                {
                                    $strPresentarDiv .= "</td><td> <input type='hidden' value='".$strTipoNegocioIl."' "
                                                        ."name='caracteristicas_$i' id='caracteristicas_$i'/>";
                                }
                            }
                            if($strDescripcionCaracteristica == "Zona")
                            {
                                $objZona= $em->getRepository('schemaBundle:InfoPunto')->getZonaByPuntoId($puntoId);
                                 if($objZona)
                                {
                                    $strCampoHabilitado     = " disabled ";
                                    $strPresentarDiv .= "<label id='lb_caracteristicas_$i'>" . $strDescripcionCaracteristica
                                                        .":</label> </td><td> <input type='text' value='". $objZona->getZona()."' "
                                                        .$strCampoHabilitado."name='caracteristicas_$i' id='caracteristicas_$i'/>";
                                }
                            }
                        }
                        else
                        {
                            //consulta a la tabla de parametro
                            $strDescripcionParametro = 'DESCRIPCION_CARACT_VELOCIDAD_X_NOMBRE_TECNICO';
                            $arrayAdmiParametroProducto = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAM_CARACT_VELOCIDAD_X_PRODUCTO',
                                                           '',
                                                           '',
                                                           '',
                                                           $producto->getDescripcionProducto(),
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           $strEmpresaCod
                                                          );
                            if (isset($arrayAdmiParametroProducto['valor2']) && !empty($arrayAdmiParametroProducto['valor2']))
                            {
                                $strDescripcionParametro = $arrayAdmiParametroProducto['valor2'];
                            }
                            
                            $arrayParamsCaractsVelocidad    = array("strValor1ParamsProdsTnGpon"    => 
                                                                    $strDescripcionParametro,
                                                                    "strCodEmpresa"                 => $strEmpresaCod,
                                                                    "strValor2NombreTecnico"        => $producto->getNombreTecnico(),
                                                                    "strValor3DescripcionCaract"    => $strDescripcionCaracteristica,
                                                                    "strValor4EsProductoIp"         => "SI");
                            $arrayInfoCaractsVelocidad      = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->obtenerParametrosProductosTnGpon($arrayParamsCaractsVelocidad);
                            if(isset($arrayInfoCaractsVelocidad) && !empty($arrayInfoCaractsVelocidad))
                            {
                                $strValorCaracteristica         = "";
                                $arrayParams                    = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                                        "strCodEmpresa"                 => $strEmpresaCod,
                                                                        "intIdProductoIp"               => $producto->getId());
                                $arrayInfoProdsPrincipalConIp   = $em->getRepository('schemaBundle:InfoServicio')
                                                                     ->obtenerParametrosProductosTnGpon($arrayParams);
                                //Se verifica que es un servicio Ip Small Business
                                if(isset($arrayInfoProdsPrincipalConIp) && !empty($arrayInfoProdsPrincipalConIp))
                                {
                                    $arrayInfoProdPrincipalConIp    = $arrayInfoProdsPrincipalConIp[0];
                                    $intIdProductoPrincipal         = $arrayInfoProdPrincipalConIp["intIdProdInternet"];
                                    $strDescripcionProdPrincipal    = $arrayInfoProdPrincipalConIp["strDescripcionProdInternet"];
                                    $intIdProductoIp                = $arrayInfoProdPrincipalConIp["intIdProdIp"];
                                    $strDescripcionProdIp           = $arrayInfoProdPrincipalConIp["strDescripcionProdIp"];
                                    $strNombreTecnicoProdPrincipal  = $arrayInfoProdPrincipalConIp["strNombreTecnicoProdIp"];
                                    $strCaractRelProdPrincipal      = $arrayInfoProdPrincipalConIp["strCaractRelProdIp"];
                                    $arrayParams["intIdPunto"]                      = $puntoId;
                                    $arrayParams["intIdProductoPrincipal"]          = $intIdProductoPrincipal;
                                    $arrayParams["strDescripcionProdPrincipal"]     = $strDescripcionProdPrincipal;
                                    $arrayParams["intIdProductoIp"]                 = $intIdProductoIp;
                                    $arrayParams["strDescripcionProdIp"]            = $strDescripcionProdIp;
                                    $arrayParams["strNombreTecnicoProdPrincipal"]   = $strNombreTecnicoProdPrincipal;
                                    $arrayParams["strCaractRelProdPrincipal"]       = $strCaractRelProdPrincipal;
                                    $arrayValidarIpMaxPermitidas                    = $em->getRepository('schemaBundle:InfoServicio')
                                                                                         ->validarIpsMaxPermitidasProducto($arrayParams);
                                    $strValorCaracteristica = array();
                                    
                                    if($arrayValidarIpMaxPermitidas["strStatus"] === "OK")
                                    {
                                        $arrayServicioValidarIpsMax     = $arrayValidarIpMaxPermitidas["arrayServicioValidarIpsMax"];
                                        if(isset($arrayServicioValidarIpsMax) && !empty($arrayServicioValidarIpsMax))
                                        {
                                            

                                            foreach($arrayServicioValidarIpsMax as $strValorvelocidad)
                                            {
                                                
                                                array_push($strValorCaracteristica,$strValorvelocidad["strVelocidadServicio"]);
                                                
                                            }
           
                                        }
                                        else if(empty($strValorCaracteristica) && !empty($strInfoAdicionalProds))
                                        {
                                            list($intIdProdCaractVelocidadSb, $strValorProdCaractVelocidadSb) = explode('||',$strInfoAdicionalProds);
                                            if(isset($intIdProdCaractVelocidadSb) && !empty($intIdProdCaractVelocidadSb) 
                                                && $intIdProdCaractVelocidadSb > 0 && isset($strValorProdCaractVelocidadSb) 
                                                && !empty($strValorProdCaractVelocidadSb))
                                            {
                                                $objProdCaractVelocidadSb   = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                                 ->find($intIdProdCaractVelocidadSb);
                                                if(is_object($objProdCaractVelocidadSb) && is_object($objProdCaractVelocidadSb->getProductoId()))
                                                {
                                                    if( $objProdCaractVelocidadSb->getProductoId()->getId() === $intIdProductoPrincipal)
                                                    {
                                                        error_log("valor de velocidad a considerar".$strValorProdCaractVelocidadSb);
                                                        $strValorCaracteristica = $strValorProdCaractVelocidadSb;
                                                    }
                                                    else
                                                    {
                                                        error_log("error por no existir servicio Small Business mapeado");
                                                        $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                        . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                        . "El servicio ".$strDescripcionProdIp." requiere que se agregue previamente un servicio "
                                                        . $strDescripcionProdPrincipal."</div></td> </tr>";
                                                    }
                                                }
                                            }
                                        }
                                        else
                                        {
                                            error_log("error por no existir servicio Small Business agregado");
                                            $strMsg = "<tr name='caracts'><td colspan='4'><div id='mensajeError' class='info-error' "
                                                        . "style='clear: both; overflow: hidden; padding-bottom: 5px;'>"
                                                        . "El servicio ".$strDescripcionProdIp." requiere que se agregue previamente un servicio "
                                                        . $strDescripcionProdPrincipal."</div></td> </tr>";
                                        }
                                    }
                                }
                                $strCampoHabilitado             = ' disabled ';
                                $boolValidarServicioPrincipal   = true;
                            }
                            else
                            {
                                $strCampoHabilitado             = '';
                                $boolValidarServicioPrincipal   = false;
                                $strValorCaracteristica         = '';
                            }
                            
                            $strOpciones        = '';
                            $arrayParametrosDet = array();
                            $strNombreParametro = 'PROD_'.$strDescripcionCaracteristica;
                            $strNombreParametroDefaultValue = 'PROD_CARACTERISTICA_SELECCIONE_VALUE';

                            $objParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                             ->findOneBy( array( 'descripcion' => $strNombreParametro,
                                                                                 'estado'      => 'Activo' ) );
                            $objParCabDefaultValue = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                             ->findOneBy( array( 'nombreParametro' => $strNombreParametroDefaultValue,
                                                                                 'estado'          => 'Activo' ) );

                            if( $objParametroCab )
                            {
                                if($strNombreParametro === 'PROD_VELOCIDAD' || $strNombreParametro === 'PROD_VELOCIDAD_TELCOHOME'
                                   || $strNombreParametro === 'PROD_VELOCIDAD_GPON' 
                                   || $strNombreParametro === $objParametroCab->getNombreParametro())
                                {
                                    $arrayOrderBy   = array('valor7' => 'ASC');
                                }
                                else
                                {
                                    $arrayOrderBy   = array();
                                }
                                //validar caracteristica cantidad camaras
                                if($strNombreParametro === 'PROD_Cantidad Camaras')
                                {
                                    $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->findBy( array( 'parametroId' => $objParametroCab,
                                                                                     'valor2'      => $producto->getId(),
                                                                                     'estado'      => 'Activo' ),
                                                                                     $arrayOrderBy);
                                }
                                else
                                {
                                    $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->findBy( array( 'parametroId' => $objParametroCab,
                                                                                     'estado'      => 'Activo' ),
                                                                                     $arrayOrderBy);
                                }
                            }

                            //Se mostraran las caracteristicas de manera convencional todos los productos los cuales no esten configurados
                            //como multi-caracteristicas dado que serán tratados de manera distinta
                            if(!$boolMultipleCaracteristica)
                            {
                                $booleanFunctionCap = false;
                                $strOcultarOpcion   = "";
                                $strOpciones        = '';

                                if( $arrayParametrosDet )
                                {
                                    if( !$boolValidarServicioPrincipal)
                                    {
                                        $objParProCarDefValue = null;
                                        if(is_object($objParCabDefaultValue))
                                        {
                                            $objParProCarDefValue = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->findOneBy(array('parametroId' => $objParCabDefaultValue,
                                                                                              'valor1'      => $strNombreParametro,
                                                                                              'estado'      => 'Activo'));
                                        }
                                        if(is_object($objParProCarDefValue))
                                        {
                                            $strOpciones .= '<option value="'.$objParProCarDefValue->getValor2().'">Seleccione</option>';
                                        }
                                        else
                                        {
                                            $strOpciones .= '<option value="">Seleccione</option>';
                                        }
                                    }

                                    foreach( $arrayParametrosDet as $objParametro )
                                    {
                                        if($strEsIsB === 'SI' && $strNombreParametro === 'PROD_VELOCIDAD')
                                        {
                                            $arrayVerificaProdsVelocidades  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                        ->getOne('PARAMS_PRODS_TN_GPON',
                                                                                                 '',
                                                                                                 '',
                                                                                                 '',
                                                                                                 'PRODUCTOS_VERIFICA_VELOCIDAD',
                                                                                                 $producto->getId(),
                                                                                                 '',
                                                                                                 '',
                                                                                                 '',
                                                                                                 $strEmpresaCod);
                                            if(isset($arrayVerificaProdsVelocidades) && !empty($arrayVerificaProdsVelocidades))
                                            {
                                                $arrayVerificaVelocidadesPermitidas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                                ->getOne('PARAMS_PRODS_TN_GPON',
                                                                                                         '',
                                                                                                         '',
                                                                                                         '',
                                                                                                         'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
                                                                                                         $producto->getId(),
                                                                                                         $objParametro->getValor1(),
                                                                                                         '',
                                                                                                         '',
                                                                                                         $strEmpresaCod);
                                                
                                                if(isset($arrayVerificaVelocidadesPermitidas) && !empty($arrayVerificaVelocidadesPermitidas))
                                                {
                                                    $strOpciones .= '<option value="'.$objParametro->getValor1().'">'.
                                                                        $objParametro->getValor1().' '.$objParametro->getValor2().
                                                                    '</option>';
                                                }
                                            }
                                            else
                                            {
                                                $strOpciones    .=  '<option value="'.$objParametro->getValor1().'">'.
                                                                        $objParametro->getValor1().' '.$objParametro->getValor2().
                                                                    '</option>';
                                            }
                                        }
                                        else if($strEsIsB === 'SI' && $strNombreParametro === 'PROD_VELOCIDAD_TELCOHOME')
                                        {
                                            $strNecesitaAprobacion = $objParametro->getValor3();
                                            $strOpciones .= '<option value="'.$objParametro->getValor1().'">'.
                                                                $objParametro->getValor1().' '.$objParametro->getValor2().
                                                            '</option>';
                                            if(!empty($strNecesitaAprobacion) && $strNecesitaAprobacion === "SI")
                                            {
                                                $strValidaValoresCaracts .= $objParametro->getValor1().'|';
                                            }
                                        }
                                        else if($boolValidarServicioPrincipal)
                                        {
                                                                                       
                                            foreach($strValorCaracteristica as $strVelocidad)
                                            {
                                               if(!empty($strVelocidad) && ($strVelocidad === $objParametro->getValor1()))
                                                {
                                                    $strOpciones .= '<option selected value="'.$objParametro->getValor1().'">'.
                                                                        $objParametro->getValor1().' '.$objParametro->getValor2().
                                                                    '</option>';
                                                } 
                                            }
                                            
                                        }
                                        else if($strNombreParametro === 'PROD_VELOCIDAD_GPON')
                                        {
                                            if($booleanValidVelCapGpon)
                                            {
                                                $booleanFunctionCap = true;
                                                $strValueParametro  = $objParametro->getValor3();
                                            }
                                            else
                                            {
                                                $strValueParametro = $objParametro->getValor1();
                                            }
                                            $arrayVerificaProdsVelocidades  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                                 'COMERCIAL',
                                                                                                 '',
                                                                                                 '',
                                                                                                 'PRODUCTOS_VERIFICA_VELOCIDAD',
                                                                                                 $producto->getId(),
                                                                                                 '',
                                                                                                 '',
                                                                                                 '',
                                                                                                 $strEmpresaCod);
                                            if(isset($arrayVerificaProdsVelocidades) && !empty($arrayVerificaProdsVelocidades))
                                            {
                                                $arrayVerificaVelocidadesPermitidas  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                                    'COMERCIAL',
                                                                                                    '',
                                                                                                    '',
                                                                                                    'PRODUCTOS_VERIFICA_VELOCIDADES_DISPONIBLES',
                                                                                                    $producto->getId(),
                                                                                                    $objParametro->getValor1(),
                                                                                                    '',
                                                                                                    '',
                                                                                                    $strEmpresaCod);
                                                if(isset($arrayVerificaVelocidadesPermitidas) && !empty($arrayVerificaVelocidadesPermitidas))
                                                {
                                                    $strOpciones .= '<option value="'.$strValueParametro.'">'.
                                                                        $objParametro->getValor1().' '.$objParametro->getValor2().
                                                                    '</option>';
                                                }
                                            }
                                            else
                                            {
                                                $strOpciones .= '<option value="'.$strValueParametro.'">'.
                                                                    $objParametro->getValor1().' '.$objParametro->getValor2().
                                                                '</option>';
                                            }
                                        }
                                        else
                                        {
                                            $strOpciones .= '<option value="'.$objParametro->getValor1().'">'.$objParametro->getValor1().'</option>';
                                        }
                                    }//foreach( $objParametrosDet as $objParametro )
                                }//( $objParametrosDet )
                                else
                                {
                                    //se llena select item de proyecto
                                                        
                                    if ($strDescripcionCaracteristica == 'Relacionar Proyecto')
                                    {   
                                        $intIdEmpresa                         = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
                                        $arrayParametros['intIdEmpresa']      = $intIdEmpresa;
                                        $arrayProyectos                       = $this->getDoctrine()->getManager("telconet_naf")
                                                                                ->getRepository('schemaBundle:admiProyectos')
                                                                                ->getProyectos($arrayParametros);
                                        
                                        $arrayRegistros   = $arrayProyectos['registros'];
                                        if ($arrayProyectos)
                                        { 
                                            $strOpciones = '';  
                                            $strOpciones .= '<option value="">Seleccione</option>';
                                            $strOpciones .= '<option value="0">Sin Proyecto</option>';
                                            foreach( $arrayRegistros as $objProyectos )
                                            {
                                                $strOpciones    .=  '<option value="'.$objProyectos["ID_PROYECTO"].'">'.
                                                                                      $objProyectos["NOMBRE"].
                                                                                 '</option>';
                                            } 
                                            
                                       }  
                                    }
                                    else if ($strDescripcionCaracteristica == $strCaractMigraTecSdwan)
                                    {   
                                        $strOpciones = '';  
                                        $strOpciones .= '<option value="S">SI</option>';
                                        $strOpciones .= '<option value="N">NO</option>';
                                    }
                                }
                                if($strPrefijoEmpresa == 'TN' || $strPrefijoEmpresa == 'TNP' || $strPrefijoEmpresa == 'TNG')
                                {
                                    //verificar function
                                    if($booleanFunctionCap)
                                    {
                                        $strChangeFunction = "actualizaCapacidadGpon(this.value)";
                                    }
                                    elseif($booleanProdCaractRel)
                                    {
                                        $strChangeFunction = "verificarProdCaractRelacion(this)";
                                    }
                                    else
                                    {
                                        $strChangeFunction = "actualizaDescripcion(this.value)";
                                    }
                                    //verificar label
                                    if($booleanProdCaractRel)
                                    {
                                        $strPresentarDiv .= "<label style='margin-left:10px;' id='lb_caracteristicas_$i' ".$strOcultarOpcion.">"
                                                .$strDescripcionCaracteristica . ":</label></td><td>"
                                                ."<select style='margin-top:10px;' align='left' name='caracteristicas_$i' id='caracteristicas_$i' "
                                                ."onchange='".$strChangeFunction."' ".$strOcultarOpcion." ".$strClassOpcion.">"
                                                .$strOpciones."</select></td>";
                                    }
                                    else
                                    {
                                        $strPresentarDiv .= "<label id='lb_caracteristicas_$i' ".$strOcultarOpcion.">"
                                                            .$strCaracteristicaObligatoria.$strDescripcionCaracteristica . ":</label></td><td>"
                                                            ."<select align='left' name='caracteristicas_$i' id='caracteristicas_$i' "
                                                            ."onchange='".$strChangeFunction."' ".$strOcultarOpcion." ".$strClassOpcion.">"
                                                            .$strOpciones."</select></td>";
                                    }
                                }
                                else
                                {
                                    $strPresentarDiv .= "<label>" . $strDescripcionCaracteristica . ":</label> </td><td>"
                                                        ."<select align='left' name='caracteristicas_$i' id='caracteristicas_$i'>"
                                                        .$strOpciones."</select></td>";

                                }
                            }
                            else
                            {
                                $arrayCaracteristicasMultiples = array();
                                //Devolver en json la informacion de los valores de las caracteristicas para escenario multi-caracteristicas

                                foreach( $arrayParametrosDet as $objParametro )
                                {
                                    $arrayCaracteristicasMultiples[] = array('id'    => $objParametro->getValor1(),
                                                                             'value' => $objParametro->getValor1());
                                }

                                $arrayCaracteristica[] = array('tipoCaracteristica'  => $strDescripcionCaracteristica,
                                                               'arrayCaracteristica' => $arrayCaracteristicasMultiples);
                            }
                        }
                    }
                    else
                    {
                        if($strPrefijoEmpresa === 'TN'|| $strPrefijoEmpresa === 'TNP'|| $strPrefijoEmpresa === 'TNG')
                        {
                            /*Obtenemos el array del parámetro CARACTERISTICAS_SERVICIOS_SIMULTANEOS.*/
                            $objParamsDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->get('CARACTERISTICAS_SERVICIOS_SIMULTANEOS',
                                    'TECNICO',
                                    'INSTALACION_SIMULTANEA',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $strEmpresaCod);

                            /*Validamos que el arreglo no este vacío.*/
                            if (is_array($objParamsDet) && !empty($objParamsDet))
                            {
                                $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);

                                $arrayParams['strNeedle'] = $strDescripcionCaracteristica;
                                $arrayParams['strKey'] = 'DESCRIPCION_CARACTERISTICA';
                                $arrayParams['arrayToSearch'] = $objCaracteristicasServiciosSimultaneos;
                                
                                $objCaracteristicasServicioSimultaneo = $serviceTecnico->searchByKeyInArray($arrayParams);
                            }

                            // Se valida si la característica es TIPO_ESQUEMA para generar un ComboBox.
                            if ($strDescripcionCaracteristica == 'TIPO_ESQUEMA')
                            {
                                $strHtml            = "<label id='lb_caracteristicas_$i'>* Tipo de Esquema:</label></td><td>";
                                $strHtml           .= "<div id='div-tipo-esquema'>";
                                $strHtml           .= "<select onchange='actualizaDescripcion(this.value)' align='left' id='caracteristicas_$i' 
                                name='caracteristicas_$i'>";
                                $strHtml           .= "<option disabled selected value=''>Seleccione</option>";
                                $strHtml           .= "<option id='e1' value=\"1\">Esquema 1</option>";
                                $strHtml           .= "<option id='e2' value=\"2\">Esquema 2</option>";
                                $strHtml           .= "</select>";
                                $strHtml           .= "</div>";

                                $strPresentarDiv .= $strHtml;

                            }
                            elseif($strDescripcionCaracteristica == 'INSTALACION_SIMULTANEA_WIFI' || 
                                   $strDescripcionCaracteristica == 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA')
                            {
                                $arrayValServTradFact = $serviceInfoServicio->validarServicioTradicional($puntoId, $productoId, array('Factible'));
                                
                                if ($arrayValServTradFact['boolInstalacionSimultanea'] && 
                                $arrayValServTradFact['objInfoServTrad']->getEstado() == 'Factible')
                                {
                                    $intIdProductoTrad      = $arrayValServTradFact['objInfoServTrad']->getId();
                                    $strDescProdTrad        = $arrayValServTradFact['objInfoServTrad']->getDescripcionPresentaFactura();
                                    $strDescripcionProducto = '"'.$objAdmiProducto->getDescripcionProducto().'"';
                                    if ($strDescripcionCaracteristica == 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA')
                                    {
                                        $strHelper = 'Instalación simultánea COU';
                                    }
                                    else
                                    {
                                        $strHelper = $objAdmiProducto->getDescripcionProducto() == 'WIFI Alquiler Equipos' ?
                                                                                                   'Instalación simultánea WAE' :
                                                                                                   'Instalación simultánea en esquema 2';
                                    }
                                                                        
                                    $strHtml = "<label id='lb_caracteristicas_$i'>
                                                <span class='required-dot'>*</span> Instalación Simultánea:
                                                </label></td><td>";
                                    $strHtml .= "<div id='div-instalacion-simultanea'>";
                                    $strHtml .= "<input title='$strDescProdTrad' style='width: 2rem;margin: 15px 2px;'
                                                type='checkbox' id='caracteristicas_$i' name='caracteristicas_$i'
                                                value='null' onclick='instalacionSimultanea(this, $intIdProductoTrad, $strDescripcionProducto )'>";
                                    $strHtml .= "<span class='ins-sim-helper'>($strHelper)</span>";

                                    $strHtml .= "</div>";
                                    $strPresentarDiv .= $strHtml;
                                }
                                else
                                {
                                    continue;
                                }
                            }
                            /* Validamos que este definida y no sea nula la variable, por lo tanto significa 
                               que el servicio es instalacion simultanea. */
                            elseif (isset($objCaracteristicasServicioSimultaneo) && !is_null($objCaracteristicasServicioSimultaneo))
                            {
                                /* Se valida los servicios tradicionales. */
                                $arrayValServTradFact = $serviceInfoServicio->validarServicioTradicional($puntoId, $productoId, array('Factible'));
                                
                                $arrayParams['strNeedle'] = $objAdmiProducto->getDescripcionProducto();
                                $arrayParams['strKey'] = 'DESCRIPCION_PRODUCTO';
                                $arrayParams['arrayToSearch'] = $objCaracteristicasServiciosSimultaneos;

                                /* Buscamos dentro del arreglo asociativo el producto. */
                                $objCaracteristicasServicioSimultaneo = $serviceTecnico->searchByKeyInArray($arrayParams);

                                /* Se realiza la validacion correspondiente. */
                                if ($arrayValServTradFact['boolInstalacionSimultanea'] &&
                                    $arrayValServTradFact['objInfoServTrad']->getEstado() == 'Factible' &&
                                    !is_null($objCaracteristicasServicioSimultaneo))
                                {

                                    $intIdProductoTrad = $arrayValServTradFact['objInfoServTrad']->getId();
                                    $strDescProdTrad = $arrayValServTradFact['objInfoServTrad']->getDescripcionPresentaFactura();
                                    $strDescripcionProducto = '"' . $objAdmiProducto->getDescripcionProducto() . '"';

                                    /* Se define variable helper para mostrar en la pantalla de ingreso de servicio. */
                                    if (!is_null($objCaracteristicasServicioSimultaneo))
                                    {
                                        $strHelper = $objCaracteristicasServicioSimultaneo['HELPER'];
                                    }
                                    else
                                    {
                                        $strHelper = 'Instalación Simultanea';
                                    }

                                    /* Se construye el HTML con los campos necesaios. */
                                    $strHtml = "<label id='lb_caracteristicas_$i'>
                                                <span class='required-dot'>*</span> Instalación Simultánea:
                                                </label></td><td>";
                                    $strHtml .= "<div id='div-instalacion-simultanea'>";
                                    $strHtml .= "<input title='$strDescProdTrad' style='width: 2rem;margin: 15px 2px;'
                                                type='checkbox' id='caracteristicas_$i' name='caracteristicas_$i'
                                                value='null' onclick='instalacionSimultanea(this, $intIdProductoTrad, $strDescripcionProducto )'>";
                                    $strHtml .= "<span class='ins-sim-helper'>($strHelper)</span>";

                                    $strHtml .= "</div>";
                                    $strPresentarDiv .= $strHtml;

                                }
                                else
                                {
                                    continue;
                                }

                            }
                            elseif($strDescripcionCaracteristica == 'REQUIERE_INSPECCION')
                            {
                                $arrayValServTradFact               = $serviceInfoServicio->validarServicioTradicional(
                                                                                            $puntoId, 
                                                                                            $productoId, 
                                                                                            array('Activo', 'Factible'));
                                $arrayValServicioAlquilerFactible   = $serviceInfoServicio->validarInspeccionRealizada($puntoId);

                                if ($arrayValServTradFact['boolInstalacionSimultanea'] &&
                                    !$arrayValServicioAlquilerFactible['boolValidacion'])
                                {

                                    $strHtml = "<label id='lb_caracteristicas_$i'>
                                                Requiere Inspección: </label></td><td>";
                                    $strHtml .= "<div id='div-requiere-inspeccion'>";

                                    $strHtml .= "<input style='width: 2rem;margin: 15px 2px;'
                                                type='checkbox' id='caracteristicas_$i' name='caracteristicas_$i'
                                                value='N' onclick='requiereInstalacionCheckboxHandler(this)'>";
                                    $strHtml .= "<span class='ins-sim-helper'>(Solo aplica para el primer servicio a ingresar)</span>";

                                    $strHtml .= "</div>";
                                    $strPresentarDiv .= $strHtml;
                                }else
                                {
                                    if ($arrayValServicioAlquilerFactible['boolValidacion'])
                                    {
                                        $objServicioFactible = $arrayValServicioAlquilerFactible['objServicioAlquilerFactible'];

                                        $strHtml = "<label id='lb_caracteristicas_$i' hidden>
                                                    Requiere Inspección: </label></td><td>";
                                        $strHtml .= "<div id='div-requiere-inspeccion' hidden>";

                                        $strHtml .= "<input style='width: 2rem;margin: 15px 2px;'
                                                type='checkbox' id='caracteristicas_$i' name='caracteristicas_$i'
                                                value='".$objServicioFactible->getId()."' hidden>";
                                        $strHtml .= "<span class='ins-sim-helper' hidden>(Solo aplica para el primer servicio a ingresar)</span>";

                                        $strHtml .= "</div>";
                                        $strPresentarDiv .= $strHtml;
                                    }

                                }
                            }
                            elseif($strDescripcionCaracteristica == 'ES PARA MIGRACION')
                            {
                                $boolVerificaCpe                           = false;
                                $arrayParametrosVerifica                   = array();
                                $arrayParametrosVerifica['intIdPunto']     = $puntoId;
                                $arrayParametrosVerifica['intIdEmpresa']   = $strEmpresaCod;
                                $arrayParametrosVerifica['strUsrCreacion'] = $strUsrCreacion;
                                $arrayParametrosVerifica['strIpCreacion']  = $strIpCreacion;
                                
                                $strMarcaCpe = $serviceTecnico->getVerificaCpe($arrayParametrosVerifica);
                                
                                $arrayMarcasPermitidas  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('MARCAS_PERMITIDAS_MIGRACION',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          $objAdmiProducto->getDescripcionProducto(),
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          $strEmpresaCod);
                        
                                if(isset($arrayMarcasPermitidas) && !empty($arrayMarcasPermitidas))
                                {
                                    foreach( $arrayMarcasPermitidas as $arrayParametroDet )
                                    {
                                        if ($arrayParametroDet['valor2'] == $strMarcaCpe)
                                        {
                                            $boolVerificaCpe = true;
                                        }
                                    }
                                }
                                
                                if ($boolVerificaCpe)
                                {
                                    $strHtml = "<label id='lb_caracteristicas_$i'>
                                               $strDescripcionCaracteristica: </label></td><td>";
                                    $strHtml .= "<div id='div-requiere-migracion'>";

                                    $strHtml .= "<input style='width: 2rem;margin: 15px 2px;'
                                                type='checkbox' id='caracteristicas_$i' name='caracteristicas_$i'
                                                value='N' onclick='requiereMigracion(this)'>";
                                    $strHtml .= "<span class='ins-sim-helper'>(Debe tener servicio Secure NG FIREWALL Activo)</span>";

                                    $strHtml .= "</div>";
                                    $strPresentarDiv .= $strHtml;
                                }
                                else
                                {
                                    $strHtml = "<label id='lb_caracteristicas_$i' hidden>
                                               $strDescripcionCaracteristica: </label></td><td>";
                                    $strHtml .= "<div id='div-requiere-migracion'>";

                                    $strHtml .= "<input style='width: 2rem;margin: 15px 2px;'
                                                type='hidden' id='caracteristicas_$i' name='caracteristicas_$i'
                                                value='N'";
                                    $strHtml .= "</div>";
                                    $strPresentarDiv .= $strHtml;
                                }
                            }
                            elseif($strDescripcionCaracteristica == 'PUNTO MD ASOCIADO')
                            {
                                $strCampoHabilitado = " disabled ";
                                $strPresentarDiv    .= "<label>" . $strDescripcionCaracteristica . ":</label> </td><td><input type='text' value='".
                                                        $strValorCaracteristica."' ". $strCampoHabilitado ."name='caracteristicas_$i' ".
                                                    "id='caracteristicas_$i' />";
                                $strPresentarDiv.=  '<a class="btn btn-default btn-sm" role="button" '
                                                    . 'onclick="getPuntosMdAsociados(\'caracteristicas_'.$i.'\')">'
                                                    .'<i class="fa fa-search" aria-hidden="true"></i> Buscar Punto MD</a>';
                            }    
                            elseif($strDescripcionCaracteristica == $strRequiereTrabajo)
                            {
                                $strPresentarDiv .= "<label id='lb_caracteristicas_$i'>" . $strDescripcionCaracteristica
                                    .":</label></td><td><input type='hidden' value='".$strDescripcionCaracteristica."' "
                                    ."name='caracteristicas_$i' id='caracteristicas_$i'/>"
                                    ."<div id='div-requiere-trabajo'>";
                                
                                //Genero los checkbox para cada departamento que va a interactuar con Eléctrico
                                $arrayParametrosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('REQUIERE_TRABAJO_CABLEADO_ESTRUCTURADO',
                                                                  'TECNICO',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $strEmpresaCod);
                                
                                foreach($arrayParametrosDet as $arrayParametro)
                                {
                                    if(isset($arrayParametro['descripcion']) && !empty($arrayParametro['descripcion']))
                                    {
                                        $strDescripcionRequiereTrabajo = $arrayParametro['descripcion'];
                                        
                                        $strPresentarDiv .= "<input style='width: 2rem;margin: 15px 2px;'
                                                type='checkbox' id='requiere_trabajo_$intJ' 
                                                name='requiere_trabajo_$intJ'
                                                value='".$strDescripcionRequiereTrabajo."'>"." ".$strDescripcionRequiereTrabajo." "."</>";
                                    }
                                    $intJ++;
                                }
                                $strPresentarDiv .= "<tr name='caracts_departamento'><td><input type='hidden' value='" . $intJ 
                                                 . "' name='cantidad_departamento'"
                                                 .  " id='cantidad_departamento'/></td></tr>";                                
                                $strPresentarDiv .= "</div>";
                            }
                            elseif($strDescripcionCaracteristica == 'TIPO_RED')
                            {
                                $strCampoHabilitado = " readonly ";
                                $strPresentarDiv .= "<label id='lb_caracteristicas_$i'>" . $strDescripcionCaracteristica
                                    .":</label> </td><td><input type='text' value='".$strTipoRed."' ". $strCampoHabilitado
                                    ."name='caracteristicas_$i' id='caracteristicas_$i' "
                                    ."onchange='actualizaDescripcion(this.value)'/>";
                            }
                            elseif( ($strDescripcionCaracteristica == 'CAPACIDAD1' || $strDescripcionCaracteristica == 'CAPACIDAD2')
                                     && $booleanTipoRedGpon )
                            {
                                $strPresentarDiv .= "<input type='hidden' value='".$strValorCaracteristica."' name='caracteristicas_$i'"
                                                 ." class='update_capacidades_gpon' id='caracteristicas_$i'"
                                                 ." onchange='actualizaDescripcion(this.value)'/>";
                            }
                            else
                            {
                                $strPresentarDiv .= "<label id='lb_caracteristicas_$i'>" .$strCaracteristicaObligatoria. $strDescripcionCaracteristica
                                    .":</label> </td><td><input type='text' value='".$strValorCaracteristica."' ". $strCampoHabilitado
                                    ."name='caracteristicas_$i' id='caracteristicas_$i' "
                                    ."onchange='actualizaDescripcion(this.value)'/>";
                            }
                        }
                        else
                        {
                            $objProdCaractComp   = $em->getRepository('schemaBundle:AdmiProdCaracComp')
                                                             ->findOneBy(array('productoCaracteristicaId'   => $item->getId(),
                                                                               'estado'                     => 'Activo'));
                            if(is_object($objProdCaractComp) && ($objProdCaractComp->getValoresDefault()))
                            {
                                $strValorCaracteristica = $objProdCaractComp->getValoresDefault();
                                $strCampoHabilitado     = "";
                                $strStyleRow            = "";
                            
                                if($objProdCaractComp->getEditable() == 0)
                                {
                                    $strCampoHabilitado    .= " disabled ";
                                }

                                if($objProdCaractComp->getEsVisible() == 0)
                                {
                                    $strStyleRow           .= " style='display:none;' ";
                                    $strCampoHabilitado    .= $strStyleRow;
                                }

                                $strPresentarDiv .= "<label". $strStyleRow.">" . $strDescripcionCaracteristica 
                                                . ":</label> </td><td><input type='text' value=".
                                                $strValorCaracteristica." ". $strCampoHabilitado ."name='caracteristicas_$i' ".
                                                "id='caracteristicas_$i'/>";
                            }
                            else
                            {
                                $strPresentarDiv .= "<label>" . $strDescripcionCaracteristica . ":</label> </td><td><input type='text' value='".
                                                    $strValorCaracteristica."' ". $strCampoHabilitado ."name='caracteristicas_$i' ".
                                                    "id='caracteristicas_$i'/>";
                            }
                        }
                    }
                    
                    $strPresentarDiv .= "<input type='hidden' value='[".$item->getCaracteristicaId()->getDescripcionCaracteristica()."]' "
                                        ."name='caracteristica_nombre_".$i."' id='caracteristica_nombre_".$i."'/>";
                    $strPresentarDiv .= "<input type='hidden' value='".$item->getCaracteristicaId()->getId()."' "
                                        ."name='caracteristica_id_".$i."' id='caracteristica_id_".$i."'/>";
                    $strPresentarDiv .= "<input type='hidden' value='".$item->getId()."' name='producto_caracteristica_".$i."' "
                                        ."id='producto_caracteristica_".$i."'/>";

                    //verificar si existe característica relacionada
                    $arrayResultadoProdCaractRel = $serviceTecnico->getDivHtmlProdCaractRelacionado(
                                                                        array("intIdProductoCaracteristica" => $item->getId(),
                                                                              "strEmpresaCod"               => $strEmpresaCod,
                                                                              "intContador"                 => $i));
                    //seteo el contador
                    $i = $arrayResultadoProdCaractRel['intContador'];
                    //seteo el div html
                    $strPresentarDiv .= $arrayResultadoProdCaractRel['strPresentarDiv'];

                    //aumentar contador
                    $i++;
                }
                $strPresentarDiv .= "<tr name='caracts'><td><input type='hidden' value='" . $i . "' name='cantidad_caracteristicas'"
                               .  " id='cantidad_caracteristicas'/></td></tr>";

                //Mensaje informativo si el producto es Netfiber muestro metraje inicial que no se cobra al cliente y el valor a cobrarse
                //por metro adicional
                if($strDivMsjNetFiber!="")
                {
                    $strPresentarDiv .= $strDivMsjNetFiber;
                }

                //Si es producto configurado como multi-caracteristica se crea un div donde ira la logica para obtener informacion de las
                //caracteristicas son sus respectivos valores e informacion


                if($strPrefijoEmpresa === 'TN' || $strPrefijoEmpresa === 'TNP' && $boolMostarCaracteristicas
                                               || $strPrefijoEmpresa === 'TNG' && $boolMostarCaracteristicas)
                {
                    $intPrecioInstalacion=$producto->getInstalacion();


                    if(($strPrefijoEmpresa === 'TN' || $strPrefijoEmpresa === 'TNP') && $producto->getNombreTecnico() === "IPSB")
                    {
                        $strCssStylePrecios  =  " style='display:none;' ";
                    }

                    $strPresentarDiv .= "<tr ".$strCssStylePrecios."name='caracts'><td ><label style='white-space: nowrap;'>"
                                        ."* Precio Unitario (F&oacute;rmula):</label>"
                                        ."</td><td><input type='text' readonly='readonly'  class='campo-obligatorio' name='precio_unitario' "
                                        ."id='precio_unitario'/></td><td width='230px'><label style='white-space: nowrap;'>* Precio de "
                                        ."Negociaci&oacute;n:</label></td><td><input type='text' ".$strAtributoReadOnly
                                        ." class='campo-obligatorio' name='precio_venta' "
                                        ."id='precio_venta' disabled='disabled' onkeypress='return validaNumerosConDecimales(event, this);' "
                                        ."onchange='actualizaTotal() '/></td></tr>";

                    if($intPrecioInstalacion>0)
                    {
                        $arrayMotivosInstalacion = array();
                        $objAdmiMotivos          = $emGeneral->getRepository("schemaBundle:AdmiMotivo")
                                                             ->findBy(array("estado"            => "Activo",
                                                                            "relacionSistemaId" => "11054"));
                        foreach ($objAdmiMotivos as $objItemMotivo)
                        {
                            $arrayMotivosInstalacion[] = array("intIdMotivos" => $objItemMotivo->getId(),
                                                               "strMotivos"   => $objItemMotivo->getNombreMotivo());
                        }
                        $strPresentarDiv .= "<tr ".$strCssStylePrecios."name='caracts' id='lb_precio_ins'>
                                           <td><label style='white-space: nowrap;'>* Precio de Instalación (Fórmula):</label></td>
                                           <td><input type='text' readonly='readonly' class='campo-obligatorio' name='precio_instalacionf' 
                                           id='precio_instalacionf' value='".$intPrecioInstalacion."'></td><td>
                                           <label style='white-space: nowrap;'>* Precio de Instalación (Pactado):</label></td><td>
                                           <input type='text' class='campo-obligatorio' name='precio_instalacion' id='precio_instalacion' 
                                           onkeypress='return validaNumerosConDecimales(event, this);' onchange = 'validarSolicitudInstalacion();' 
                                           value='".$intPrecioInstalacion."' ".
                                           $strAtributoReadOnly." ></td></tr>";
                    }
                    else
                    {
                        $strPresentarDiv .= "<tr ".$strCssStylePrecios."name='caracts'><td><input type='hidden' name='precio_instalacionf' "
                                            ." id='precio_instalacionf' value='".$intPrecioInstalacion."'></td><td>"
                                            ."<input type='hidden' class='campo-obligatorio' name='precio_instalacion' id='precio_instalacion' "
                                            ."value='".$intPrecioInstalacion."' ></td></tr>";
                    }

                    $strPresentarDiv  .= "<tr name='caracts'><td><label class='required' >* Precio Total:</label></td>
                                          <td><input type='text'
                                          readonly='readonly' class='campo-obligatorio' name='precio_total' id='precio_total'></td><td></td><td>
                                          </td></tr>";

                    $strPresentarDiv .= "<tr><td>&nbsp;</td></tr>
                                         <tr name='caracts'><td><label style='white-space: nowrap;'>* Descripción Producto:</label></td>
                                         <td style='padding-left: 5px;'><textarea  class='campo-obligatorio' rows='3' cols='45'
                                         name='descripcion_producto' id='descripcion_producto'>" . $producto->getDescripcionProducto().
                                         "</textarea></td><td></td><td></td></tr>";
                }

                if(count($items) == 0 || $producto->getNombreTecnico() === "IPSB")
                {
                    $boolEval = true;
                }
            }//( $producto->getNombreTecnico() == "FINANCIERO" )
            if($strPrefijoEmpresa === 'TN')
            {
                $serviceTelcoCrm = $this->get('comercial.ComercialCrm');
                if (!empty($objInfoPersonaEmpresaRol))
                {
                    $objInfoPersona = $em->getRepository('schemaBundle:InfoPersona')->find($objInfoPersonaEmpresaRol->getPersonaId());
                }
                if(is_object($objInfoPersona))
                {
                    $arrayParametros = array("strRuc"               => $objInfoPersona->getIdentificacionCliente(),
                                             "strPrefijoEmpresa"    => $strPrefijoEmpresa, 
                                             "strCodEmpresa"        => $strEmpresaCod,
                                             "strIdPropuesta"       => $intIdPropuesta,
                                             "strBanderaCotizacion" => "PROPUESTA-COTIZACION-TELCOS");

                    $arrayParametrosWSCrm = array(
                                                  "arrayParametrosCRM"   => $arrayParametros,
                                                  "strOp"                => 'getCotizacion',
                                                  "strFuncion"           => 'procesar'
                                                 );

                    $arrayRespuestaWSCrm = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                 
                    if(empty($arrayRespuestaWSCrm["error"]) && $arrayRespuestaWSCrm["error"]=="")
                    {
                        foreach($arrayRespuestaWSCrm["resultado"] as $arrayItem)
                        {
                                $intCotizacionId    = $arrayItem->id_cotizacion;
                                $strNombreCot       = $arrayItem->name_cotizacion;
                                $strPresentarDivOptionCot .= "<option value='$intCotizacionId'>$strNombreCot</option>";
                        }
                        if(!empty($intCotizacionId))
                        {
                            $strPresentarDiv .= "<tr name='caracts'><td colspan='4'><div id='mensajeAdvertenciaCRM' class='info-error' ".
                                                "style='clear: both;overflow: hidden; padding-bottom: 5px;'>Cliente en sesión tiene cotizaciones ".
                                                "asignadas en TelcoCRM. Al momento de elegir se va a crear automáticamente una solicitud ".
                                                "de proyecto.</div></td>".
                                                "</tr>";

                            $strPresentarDiv .= "<tr".$strCssVerFilaUltimaMilla." name='caracts'><td><label> Cotización:</label></td>"
                                                     ."<td style='padding-left: 5px;'>";
                                $strPresentarDiv .= "<select  $strCaractDisabled name='cotizacionIdProd' id='cotizacionIdProd'>";
                                $strPresentarDiv .= $strPresentarDivOptionCot;
                                $strPresentarDiv .= "</select>";

                            $strPresentarDiv .= "</td><td></td><td></td></tr>";
                        }
                    }
                }
            }
            /**
             * BLOQUE VERIFICAR CARACTERISTICA
             *
             * Bloque que verifica si existe la característica 'REGISTRO_UNITARIO' asociada al producto
             */
            $arrayParametrosCaracteristicas = array( 'intIdProducto'         => $productoId,
                                                     'strDescCaracteristica' => 'REGISTRO_UNITARIO',
                                                     'strEstado'             => 'Activo' );
            $strRegistroUnitario            = $serviceUtilidades->validarCaracteristicaProducto($arrayParametrosCaracteristicas);
            /**
             * FIN BLOQUE VERIFICAR CARACTERISTICA
             */
            if(!empty($strValidaValoresCaracts))
            {
                $strValidaValoresCaracts = substr($strValidaValoresCaracts, 0, strlen($strValidaValoresCaracts)-1 );
            }
            $strPresentarDiv .= "<tr name='caracts'><td colspan='4' align='left'>";
            if(!$esbusiness)
            {
                $strFuncion = 'agregar_detalle();';
                if(!empty($strTipoRed) && $strPrefijoEmpresa == 'TN')
                {
                    $arrayValidaProducto   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            '',
                                                                                            $productoId,
                                                                                            '',
                                                                                            '',
                                                                                            'S',
                                                                                            'RELACION_PRODUCTO_CARACTERISTICA',
                                                                                            $strEmpresaCod);
                    $arrayCiudadDisponible = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->get('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            '',
                                                                                            $arrayPtoClte['id_cobertura'],
                                                                                            $arrayPtoClte['cobertura'],
                                                                                            '',
                                                                                            'S',
                                                                                            'CIUDADES_DISPONIBLES',
                                                                                            $strEmpresaCod);
                    $arrayValidaProductoSolicitud = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->getOne('NUEVA_RED_GPON_TN',
                                                                                            'COMERCIAL',
                                                                                            '',
                                                                                            'VALIDAR_PRODUCTO_SOLICITUD_APROBACION',
                                                                                            $productoId,
                                                                                            $arrayPtoClte['id_cobertura'],
                                                                                            $strTipoRed,
                                                                                            '',
                                                                                            '',
                                                                                            $strEmpresaCod);
                    if( !empty($arrayValidaProducto) && !empty($arrayCiudadDisponible)
                        && isset($arrayValidaProductoSolicitud) && !empty($arrayValidaProductoSolicitud)
                        && isset($arrayValidaProductoSolicitud['valor4']) && $arrayValidaProductoSolicitud['valor4'] == "SI" )
                    {
                        $strFuncion = "validaInformacion();";
                    }
                }
                $strPresentarDiv .= "<button type='button' class='button-crud' onClick=".$strFuncion.">Agregar</button> &nbsp;&nbsp;&nbsp;&nbsp;"
                                    ."<button type='button' class='button-crud' onClick='limpiar_detalle();'>Limpiar</button>";

            }
            //permite cambio precio instalacion
            $strPermiteCambioPreInstalacion = "N";
            if(is_object($objAdmiProducto) && !empty($strEmpresaCod))
            {
                $arrayPermiteCamPrecioInst  =  $em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->getOne("PERMITE_CAMBIO_PRECIO_INSTALACION",
                                                            "COMERCIAL",
                                                            "",
                                                            "",
                                                            $objAdmiProducto->getId(),
                                                            "",
                                                            "",
                                                            "",
                                                            "",
                                                            $strEmpresaCod);
                if(isset($arrayPermiteCamPrecioInst) && !empty($arrayPermiteCamPrecioInst) && !empty($arrayPermiteCamPrecioInst['valor2']))
                {
                    $strPermiteCambioPreInstalacion = $arrayPermiteCamPrecioInst['valor2'];
                }
            }
            $strPresentarDiv .= "<input type='hidden' value='$strFuncionAux' name='funcion_precio' id='funcion_precio'/>"
                                ."<input type='hidden' value='".$producto->getDescripcionProducto()."' "
                                ."name='strDescripcionProd' id='strDescripcionProd'/>"
                                ."<input type='hidden' value='".$producto->getNombreTecnico()."' name='strNombreTecnico' id='strNombreTecnico'/>"
                                ."<input type='hidden' value='".$strRegistroUnitario."' name='strRegistroUnitario' id='strRegistroUnitario'/>"
                                ."<input type='hidden' value='".$strTipoNegociosRestringidos."' name='strTipoNegociosRestringidos' "
                                ."id='strTipoNegociosRestringidos'/>"
                                ."<input type='hidden' value='".$strValidaValoresCaracts."' name='strValidaValoresCaracts' "
                                ."id='strValidaValoresCaracts'/>"
                                ."<input type='hidden' value='".$strEsIsB."' name='$strEsIsB' id='strEsIsB'/>"
                                ."<input type='hidden' value='".$strEsIpWanPyme."' name='strEsIpWanPyme' id='strEsIpWanPyme'/>"
                                ."<input type='hidden' value='".$strNombreTipoNegocioPto."' name='$strNombreTipoNegocioPto' "
                                . "id='strNombreTipoNegocioPto'/>"
                                ."<input type='hidden' value='".$strRequiereUltimaMillaProd."' name='strRequiereUltimaMillaProducto' "
                                ."id='strRequiereUltimaMillaProducto'/>"
                                ."<input type='hidden' value='".$strFormaPagoCliente."' name='strFormaPago' "
                                ."id='strFormaPago'/>"
                                ."<input type='hidden' value='".($boolFacturaPorConsumo?'S':'N')."' name='strEsProductoCloud' "
                                ."id='strEsProductoCloud'/>"
                                ."<input type='hidden' value='".($boolClienteTieneDeuda?'S':'N')."' name='strContieneDeuda' "
                                ."id='strContieneDeuda'/>"
                                ."<input type='hidden' value='".$strPermiteCambioPreInstalacion."' name='strPermiteCambioPreInstalacion' "
                                ."id='strPermiteCambioPreInstalacion'/>"
                                ."<input type='hidden' value='".$strValorProductoPaqHoras."' name='strValorProductoPaqHoras' "
                                ."id='strValorProductoPaqHoras'/>"
                                ."<input type='hidden' value='".$strValorProductoPaqHorasRec."' name='strValorProductoPaqHorasRec' "
                                ."id='strValorProductoPaqHorasRec'/>"
                                ."<input type='hidden' value='".$intIdProductoPaquetePrincipal."' name='intIdProductoPaquetePrincipal' "
                                ."id='intIdProductoPaquetePrincipal'/>"
                                ."<input type='hidden' value='".$intIdProductoPaqueteRec."' name='intIdProductoPaqueteRec' "
                                ."id='intIdProductoPaqueteRec'/>"
                                ."</td></tr>";
        }//( $boolContinuar )
        
        if(isset($strOpcionConsulta) && !empty($strOpcionConsulta) && $strOpcionConsulta === "CLONAR_PLAN")
        {
            $strPresentarDiv = "<table>".$strPresentarDiv."</table>";
        }
        
        $strFrecuencia  = is_object($objAdmiProducto) ? $objAdmiProducto->getFrecuencia() : null;
        if(isset($strFrecuencia))
        {
            $arrayFrecuenciaItem  =  $em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->getOne("FRECUENCIA_FACTURACION",
                                                "",
                                                "",
                                                "",
                                                "",
                                                $strFrecuencia,
                                                "",
                                                "",
                                                "",
                                                $strEmpresaCod);
        }

        $arrayRespuesta = array('msg'                     => $strMsg, 
                                'div'                     => $strPresentarDiv, 
                                'eval'                    => $boolEval , 
                                //Informacion para escenario de multi-caracteristicas
                                'esCore'                  => $strEsCore,
                                'esMultiCaracteristica'   => $boolMultipleCaracteristica,
                                'esPoolCompleto'          => $strEsPoolCompleto,
                                'arrayJsonCaractMultiple' => $arrayCaracteristica,
                                'esLicencia'              => ($boolEsLicenciamiento) ? $boolEsLicenciamiento : '',
                                'arrayMotivosInstalacion' => (!empty($arrayMotivosInstalacion) && is_array($arrayMotivosInstalacion)) ? 
                                                             $arrayMotivosInstalacion:array()
                               );
        if(isset($arrayFrecuenciaItem) && !empty($arrayFrecuenciaItem))
        {
            $arrayRespuesta['strFrecuencia'] = $arrayFrecuenciaItem['valor1'];
        }
        $response = new Response(json_encode($arrayRespuesta));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * Documentación para el método tabularFuncionPrecio
     *
     * Función que procesa la fórmula del precio y la tabula para su mejor visualización.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 18-04-2016
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.2 21-07-2016
     * Se controla la posición del array $arrayChar para evitar excepción "Undefined offset".
     *
     * @param String $strExpresion Cadena de la Función Precio.
     * @return String Función tabulada.
     */
    private function tabularFuncionPrecio($strExpresion)
    {
        if(!$strExpresion)
        {
            return $strExpresion;
        }

        $strEnter     = "&#10"; // Representación del Salto de Línea para el componente TextArea.
        $strExpresion = preg_replace('/\s+/',     ' ',       $strExpresion); // Se ajustan los espacios dobles a un espacio
        $strExpresion = preg_replace('/else if/', 'els@-i@', $strExpresion); // Se ajusta "else if" por "els@-i@" para distinguir de "if" y "else"
        $strExpresion = preg_replace('/if\(/',    'if (',    $strExpresion); // Se ajustan los espacios en la expresión "if(".
        $strExpresion = preg_replace('/else/',    'e15e',    $strExpresion); // Se ajusta "else" por "3ls3" para distinguir de "else if"

        // Agrego Saltos de línea antes y después de las llaves
        $strExpresion = str_replace("{", $strEnter . '{', str_replace("}", $strEnter . '}', $strExpresion));
        $strExpresion = str_replace('if', $strEnter . 'if', $strExpresion);
        $strExpresion = str_replace('els@-i@', $strEnter . 'els@-i@', $strExpresion);
        $strExpresion = str_replace('e15e', $strEnter . 'e15e', $strExpresion);
        $strExpresion = str_replace(';', ';'.$strEnter, $strExpresion);
        $strExpresion = str_replace(']=="', '] == "', $strExpresion);
        $strExpresion = str_replace(']"=="', ']" == "', $strExpresion);

        $arrayChar        = str_split($strExpresion);
        $intTotal         = count($arrayChar);
        $strFuncionPrecio = '';

        $x = 0;
        $f = 0;

        for($i = 0; $i < $intTotal; $i++)
        {
            $if    = $i;
            $ife   = $i;
            $e     = $i;
            $strIf = '';

            $strChar  = $arrayChar[$i];
            if($strChar == ';')
            {
                $f++;
            }
            if($if < ($intTotal - 1))
            {
                $strIf = $strChar . $arrayChar[++$if];
            }
            $strElseIfA = '';
            for($c = 0; $c < 6; $c++)
            {
                if((++$ife) < $intTotal)
                {
                    $strElseIfA .= $arrayChar[$ife];
                }
            }

            $strElseIf = $strChar . $strElseIfA;

            $strElseA = '';
            for($c = 0; $c < 3; $c++)
            {
                if((++$e) < $intTotal)
                {
                    $strElseA .= $arrayChar[$e];
                }
            }
            $strElse = $strChar . $strElseA;

            // Analiso los tokens que deban llevar indentación principal: {, if, else, esle if.
            if($strChar == '{' || strtolower($strIf) == 'if' || strtolower($strElse) == 'e15e' || strtolower($strElseIf) == 'els@-i@')
            {
                $strLine = $this->addTabs($x, $strChar); // Agrego los N Tabs

                if($strChar == '{')
                {
                    $x++; // Al final incremento índice para el siguiente nivel de llave {, solo si el token es una llave.
                }
            }
            else if($strChar == '}')
            {
                $x--; // Primero disminuyo el índice para el mismo nivel de llave }

                $strLine = $this->addTabs($x, $strChar); // Agrego los N Tabs
            }
            else
            {
                $strLine = $strChar;
            }

            $strFuncionPrecio .= $strLine; // Se va armando la nueva cadena de la función precio.
        }

        $arrayMatch = array();

        preg_match('/;(.*?)}/', $strFuncionPrecio, $arrayMatch); // Obtengo el valor para reemplazar al final de cada línea de código.

        if(count($arrayMatch) > 1 && $arrayMatch[1] && strlen($arrayMatch[1]) > 4 )
        {
            $strToClean = substr($arrayMatch[1], 4, strlen($arrayMatch[1])); // de la posición 0 a 3 está un "Enter", y obvio de la cadena limpia.
            $strFuncionPrecio = preg_replace('/;(.*?)}/', ";$strToClean}", $strFuncionPrecio); // Ajusto la última línea de código de cada bloque.
        }

        $strFuncionPrecio = preg_replace('/¡/', '&nbsp;', $strFuncionPrecio); // Agrego los espacios que representan la indentación de casa fila.

        $strFuncionPrecio = preg_replace('/e15e/', 'else', $strFuncionPrecio); //  Reemplazo los "Else"
        return preg_replace('/els@-i@/', 'else if', $strFuncionPrecio); // Reemplazo los "If Else"
    }

    /**
     * Documentación para el método addTabs
     *
     * Funcion agrega los Tabs a cada inicio de línea dependiendo del nivel al que le corresponda.
     *
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 18-04-2016
     *
     * @param String $strExpresion Cadena de la Función Precio.
     * @return String Función tabulada.
     */
    private function addTabs($x, $strChar)
    {
        $strTab    = '¡¡¡¡¡¡'; // El token "¡" será la representación de una espacio. 6 espacios representarán a una tabulación.
        $strTabAux = '';

        for($j = 0; $j < $x; $j++)
        {
            $strTabAux .= $strTab; // Acumulo los Tabs
        }
        if($strChar == '{' || $strChar == '}')
        {
            return $strTabAux . $strChar . '&#10' . $strTabAux . $strTab; // &#10 representa un Enter en TextArea
        }
        else
        {
            return $strTabAux . $strChar;
        }
    }

    /*combo estado llenado ajax*/
    public function estadosAction()
    {
		/*Modificacion a utilizacion de estados por modulos*/

		$arreglo[]= array('idEstado'=>'Activo','codigo'=> 'ACT','descripcion'=> 'Activo');
		$arreglo[]= array('idEstado'=>'Inactivo','codigo'=> 'INT','descripcion'=> 'Inactivo');
                $arreglo[]= array('idEstado'=>'Pendiente','codigo'=> 'PEN','descripcion'=> 'Pendiente');
                $arreglo[]= array('idEstado'=>'Clonado','codigo'=> 'CLO','descripcion'=> 'Clonado');

		$response = new Response(json_encode(array('estados'=>$arreglo)));
		$response->headers->set('Content-type', 'text/json');
		return $response;

    }
     /**
    * Funcion que inactiva Planes
    * Consideraciones: Solo se podra Inactivar Planes en estado Activo
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param request $request
    * @param integer $id // Id del Plan
    * @version 1.0 07-08-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function deleteAjaxAction()
    {
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $peticion           = $this->get('request');
        $parametro          = $peticion->get('id');
        $strEstadoActivo    = "Activo";
        $strEstadoInactivo  = "Inactivo";
        $objFechaCreacion   = new \DateTime('now');
        $request            = $this->getRequest();
        $session            = $request->getSession();
        $strUsuarioCreacion = $session->get('user');
        if( isset($parametro) )
            $arrayValor = explode("|",$parametro);
        else
        {
            $parametro  = $peticion->get('param');
            $arrayValor = explode("|",$parametro);
        }

        $em = $this->getDoctrine()->getManager();
        foreach( $arrayValor as $id )
        {
            $objInfoPlanCab = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
            if( $objInfoPlanCab && $objInfoPlanCab->getEstado()=="Activo" )
            {
                $objInfoPlanCab->setEstado( $strEstadoInactivo );
                $objInfoPlanCab->setFeUltMod( $objFechaCreacion );
                $objInfoPlanCab->setUsrUltMod( $strUsuarioCreacion );
                $em->persist($objInfoPlanCab);
                $em->flush();
                $respuesta->setContent("Se elimino la entidad");
                $objInfoPlanDet = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$strEstadoActivo);
                if( isset($objInfoPlanDet) )
                {
                    foreach($objInfoPlanDet as $det)
                    {
                        $det->setEstado( $strEstadoInactivo );
                        $em->persist($det);
                        $em->flush();
                    }
                }
                //GUARDAR INFO PLAN HISTORIAL
                $objPlanHistorial = new InfoPlanHistorial();
                $objPlanHistorial->setPlanId( $objInfoPlanCab );
                $objPlanHistorial->setIpCreacion( $request->getClientIp() );
                $objPlanHistorial->setFeCreacion( $objFechaCreacion );
                $objPlanHistorial->setUsrCreacion( $strUsuarioCreacion );
                $objPlanHistorial->setObservacion( 'Se realiza Inactivacion de Plan' );
                $objPlanHistorial->setEstado( $strEstadoInactivo );
                $em->persist($objPlanHistorial);
                $em->flush();
            }
            else
                $respuesta->setContent("No existe el registro");
        }
        return $respuesta;
    }
    /*
     * Llena combo con las caracteristicas dependiendo del tipo : Comercial, Tecnico, Financiero, Promocion
     */
    public function llenaComboCaracteristicasPorTipoAction()
    {
        $request = $this->getRequest();
        $tipo_caracteristica = $request->request->get("tipo_caracteristica");
        $em = $this->get('doctrine')->getManager('telconet');

        $estado="Activo";
        $caracteristicas = $em->getRepository('schemaBundle:AdmiCaracteristica')->findCaracteristicasPorTipoYEstado($tipo_caracteristica,$estado);

        if(!$caracteristicas){
                $variable_combo="<option>Seleccione</option>";
                $arreglo=array('msg'=>'No existen datos','div'=>$variable_combo);
        }else{
                $variable_combo="<option>Seleccione</option>";
                foreach($caracteristicas as $caracteristica){
                    $variable_combo.="<option value='".$caracteristica->getId()."-".$caracteristica->getDescripcionCaracteristica()."'>".$caracteristica->getDescripcionCaracteristica()."</option>";
                }
                $presentar_div="";
                $presentar_div.="<tr><td><label class='required'>Valor:</label></td>";
                $presentar_div.="<td><input type='text' value='' name='valor' id='valor' /></td></tr>";

                $div_button="";
                $div_button.="<button type='button' class='button-crud' onClick='agregar_detalle();'>Agregar</button>";
                $div_button.=" ";
                $div_button.="<button type='button' class='button-crud' onClick='limpiar_detalle();'>Limpiar</button>";

                $arreglo=array('msg'=>'ok','div'=>$variable_combo,'div_button'=>$div_button,'presentar_div'=>$presentar_div);
        }

        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

   /**
    * Funcion que muestra Show de Planes con sus productos y caracteristicas y Agrega caracteristicas para los planes existentes
    * @author : telcos
    * @version 1.0 23-05-2014
    * @param integer $id   //Id del plan
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return Renders a view.
    */
     public function newCaractPlanesAction($id)
    {
        $objPeticion                   = $this->get('request');
        $objSession                    = $objPeticion->getSession();
        $strEmpresaId                  = $objSession->get('idEmpresa');
        $em                         = $this->getDoctrine()->getManager('telconet');
        $listado_detalle            = array(""=>"Seleccione","COMERCIAL" => "COMERCIAL", "TECNICA" => "TECNICA", "FINANCIERA" => "FINANCIERA","PROMOCION" => "PROMOCION");
        $entity                     = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
        $arregloCaracteristicasPlan = array();
        $objPlanProvieneRegClonado  = null;

        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        else
        {
            $info_detalle = array();
            $caractPlan   = array();
            //Busco el plan del que proviene si se trata de un plan clonado
            if($entity && $entity->getPlanId())
            {
                $objPlanProvieneRegClonado = $em->getRepository('schemaBundle:InfoPlanCab')->find($entity->getPlanId());
            }
            //Leo Caracteristicas de los planes
            $caracteristicasPlan = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->getCaracteristicasPlan($id);
            if( $caracteristicasPlan )
            {

                $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
                $arrayParamEcucert  = array(
                    'nombreParametro' => "VARIABLES_VELOCIDAD_PLANES",
                    'estado'          => "Activo"
                );
                $entityParametroCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                ->findOneByNombreParametro($arrayParamEcucert);
            
                $intIdParametrosVeloc = 0;
                if( isset($entityParametroCab) && !empty($entityParametroCab) )
                {
                $intIdParametrosVeloc = $entityParametroCab->getId();
                }
                    
                $arrayParametrosDet  = array( 
                                            'estado'      => "Activo", 
                                            'parametroId' => $intIdParametrosVeloc,
                                            'empresaCod'  => $strEmpresaId
                                            );
                $arrayParametroDetVelocidad = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy($arrayParametrosDet);
                $arrayCaractVelocidad= array();   
                foreach( $arrayParametroDetVelocidad as $objParametroDetVelocidad )
                {    
            
                        $arrayCaractVelocidad[]= $objParametroDetVelocidad->getValor1();        
                }  
                $i = 0;
	        foreach( $caracteristicasPlan as $caracteristica )
                {
                    $caractPlan[$i]['idCaract']     = $caracteristica['idCaract'];
                    $caractPlan[$i]['nombre']       = $caracteristica['nombre'];
                    $caractPlan[$i]['tipo']         = $caracteristica['tipo'];
		            $caractPlan[$i]['valor']        = $caracteristica['valor'];
                    $caractPlan[$i]['idPlanCaract'] = $caracteristica['idPlanCaract'];
                    $caractPlan[$i]['btnEditar']="N";
                    $caractPlan[$i]['editar']="N";
                    foreach( $arrayCaractVelocidad as $intCaractVelo )
                    {
                        if($intCaractVelo==$caractPlan[$i]['idCaract'])
                        {
                            $caractPlan[$i]['btnEditar']="S";  
                        }
                    }
                    $info_detalle[] = array('tipo_caracteristica' => $caracteristica['tipo'],
                                            'caracteristica'      =>  $caracteristica['idCaract'],
                                            'valor'               => $caracteristica['valor'],
                                            'idPlanCaract'        => $caracteristica['idPlanCaract'],
                                            'nombreCaracteristica'=> $caracteristica['nombre'],
                                            'btnEditar'=> $caractPlan[$i]['btnEditar'],
                                            'editar'=> $caractPlan[$i]['editar']);
                    $i++;

   
                }
	    }
            $arreglo_encode = json_encode($info_detalle);
        }
        $items                      = $em->getRepository('schemaBundle:InfoPlanDet')->getPlanIdYEstados($id);
	$arregloCaracteristicasPlan = array();
        $arregloCaracteristicasDet  = array();
        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

	if( $items )
	{
            $i = 0;
            //Leo productos
	    foreach( $items as $item )
            {
                //Productos por planes
		$producto                   = $em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
                $arreglo[$i]['idproducto']  = $producto->getId();
		$arreglo[$i]['producto']    = $producto->getDescripcionProducto();
                $arreglo[$i]['instalacion'] = $producto->getInstalacion();
		$arreglo[$i]['cantidad']    = $item->getCantidadDetalle();
		$arreglo[$i]['precio']      = $item->getPrecioItem();
		$i++;

                //caracteristicas por productos
		$caracteristicasDet = $em->getRepository('schemaBundle:InfoPlanDet')->getCaracteristicas($item->getId());
		foreach( $caracteristicasDet as $caracteristica ){
                    $caractDet['idproducto']     = $producto->getId();
                    $caractDet['nombre']         = $caracteristica['nombre'];
		    $caractDet['valor']          = $caracteristica['valor'];
		    $caractDet['estado']         = $caracteristica['estado'];
		    $arregloCaracteristicasDet[] = $caractDet;
		 }
            }
	}
	else
            $arreglo="";

    $parametros = array('entity'      => $entity,
                        'delete_form' => $deleteForm->createView(),
                        );

    $parametros['listado_detalle'] = $listado_detalle;

	if( $arreglo != "" )
            $parametros['items'] = $arreglo;
	if( $arregloCaracteristicasDet != "" )
	    $parametros['caracteristicasDet'] = $arregloCaracteristicasDet;

        if( $arregloCaracteristicasPlan != "" )
	    $parametros['caracteristicasPlan'] = $arregloCaracteristicasPlan;

        $parametros['items_detalle'] = $caractPlan;
        $parametros['arreglo']       = $arreglo_encode;
        $parametros['objPlanProvieneRegClonado'] = $objPlanProvieneRegClonado;
        return $this->render('catalogoBundle:InfoPlanCaracteristica:newCaractPlanes.html.twig', $parametros);

    }

   /**
    * Funcion que Guarda caracteristicas agregados a los planes
    * @author : telcos
    * @param request $request
    * @version 1.0 23-05-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function createCaracteristicasPlanAction(Request $request)
    {
        $entity          = new InfoPlanCaracteristica();
        $request         = $this->getRequest();
        $id_plan         = $request->get('id_plan');
        $datos           = $request->get('valores');
        $valores         = json_decode($datos);
        $em              = $this->getDoctrine()->getManager('telconet');
        $entityPlan      = $em->getRepository('schemaBundle:InfoPlanCab')->findOneById($id_plan);
        $entityPlanCarac = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->getPlanCaractEstados($id_plan);
        //Verificacion de items existentes
        $band = 2;
        foreach( $entityPlanCarac as $item )
        {
            foreach( $valores as $valor )
            {
                if( $item->getId() == $valor->idPlanCaract && $valor->idPlanCaract != "" )
                {
                    $band = 1;
                    break;
                }
                else
                {
                    $band = 2;
                }
             }
             if( $band == 2 )
             {
                 $estado = "Inactivo";
                 $item->setEstado($estado);
                 $em->persist($item);
                 $em->flush();
             }
        }

        if ( $id_plan != null )
        {
            foreach( $valores as $valor )
            {
                $encontro = false;
                $actualzarCat=false;
                foreach( $entityPlanCarac as $item )
                {
                    if( $valor->idPlanCaract == $item->getId() )
                    {
                        $encontro = true;
                        if($valor->editar=="S")
                        {
                            $actualzarCat=true;
                        }
                        
                         break;
                    }
                }
                if( $encontro == false )
                {
                    $id                  = $valor->caracteristica;
                    $tipo_caracteristica = $valor->tipo_caracteristica;
                    $valor               = $valor->valor;
                    $caracteristica      = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneById($id);
                    $estado              = 'Activo';
                    $session             = $request->getSession();
                    $usrCreacion         = $request->getSession()->get('user');
                    $entity              = new InfoPlanCaracteristica();
                    $valor               = str_replace('.',",",$valor);                    
                    $entity->setPlanId($entityPlan);
                    $entity->setCaracteristicaId($caracteristica);
                    $entity->setValor($valor);
                    $entity->setEstado($estado);
                    $entity->setFeCreacion(new \DateTime('now'));
                    $entity->setUsrCreacion($usrCreacion);
                    $entity->setIpCreacion($request->getClientIp());
                    $em->persist($entity);
                    $em->flush();
                }
                if($actualzarCat)
                {
                    $objInfoPlanCaracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneById($valor->idPlanCaract );
                    $valorCaract               = str_replace('.',",",$valor->valor);  
                    $objInfoPlanCaracteristica->setValor($valorCaract);
                    $em->persist($objInfoPlanCaracteristica);
                    $em->flush();
                }
             }
        }
        return $this->redirect($this->generateUrl('infoplancaracteristicas_show_caract_plan', array('id' => $id_plan)));
    }
    /*
     *  Funcion que valida Campo "Valor" a ser ingresado de la caracteristica deacuerdo al tipo de Dato  : N: Numerico, T: Texto, O: Opcional (S/N)
     */
     public function validaValorCaractAction()
    {
        $request = $this->getRequest();
        $id_caracteristica = $request->request->get("caracteristica");
        $valor = $request->request->get("valor");
        $em = $this->get('doctrine')->getManager('telconet');
	$session  = $request->getSession();
        $caracteristica=false;
        if($id_caracteristica!='Seleccione')
           $caracteristica=$em->getRepository('schemaBundle:AdmiCaracteristica')->findOneById($id_caracteristica);
        $div_valida_valor="";
        if(!$caracteristica){
                $div_valida_valor='No existen datos para esa caracteristica';
                $arreglo=array('msg'=>'ok','div_valida_valor'=>$div_valida_valor);
        }else{
                if($caracteristica->getTipoIngreso()=="N" && !is_numeric($valor)){
                    $div_valida_valor='Campo debe ser un valor numerico';
                    $arreglo=array('msg'=>'ok','div_valida_valor'=>$div_valida_valor);
                }else{
                    if($caracteristica->getTipoIngreso()=="T" && !is_string($valor)){
                       $div_valida_valor='Campo debe ser un valor texto';
                       $arreglo=array('msg'=>'ok','div_valida_valor'=>$div_valida_valor);
                    }else{
                         if($caracteristica->getTipoIngreso()=="O" && ($valor!="SI" && $valor!="NO")){
                             $div_valida_valor='Campo debe ser un valor opcional SI/NO';
                             $arreglo=array('msg'=>'ok','div_valida_valor'=>$div_valida_valor);
                         }else{
                            $arreglo=array('msg'=>'','div_valida_valor'=>$div_valida_valor);
                         }
                    }
                }
        }

        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
    * Funcion que muestra Show de Planes con sus productos y caracteristicas y Agrega Condiciones para los planes existentes
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 23-05-2014
    * @param integer $id   //Id del plan
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return Renders a view.
    */
     public function newCondicionesPlanesAction($id)
    {
        $peticion                   = $this->get('request');
        $session                    = $peticion->getSession();
        $empresaId                  = $session->get('idEmpresa');
        $em                         = $this->getDoctrine()->getManager();
        $entity                     = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
        $objPlanProvieneRegClonado  = null;
        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        //Tipo Negocio
        $arreglo_tipo_negocio = array();
        if( $entity )
        {
            //Busco el plan del que proviene si se trata de un plan clonado
            if($entity && $entity->getPlanId())
            {
                $objPlanProvieneRegClonado = $em->getRepository('schemaBundle:InfoPlanCab')->find($entity->getPlanId());
            }
            $tipoNegocio = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findOneBy(
                    array( "nombreTipoNegocio" => $entity->getTipo(),"empresaCod" =>$empresaId ));

            if ( $tipoNegocio != null )
                $arreglo_tipo_negocio[$tipoNegocio->getId()."-".$tipoNegocio->getNombreTipoNegocio()] = $tipoNegocio->getNombreTipoNegocio();
        }
        $listado_tipo = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findTiposNegocioPorEmpresa($empresaId);
        foreach ( $listado_tipo as $listado_tipo )
        {
            $arreglo_tipo_negocio[$listado_tipo->getId()."-".$listado_tipo->getNombreTipoNegocio()]=$listado_tipo->getNombreTipoNegocio();
        }
        //Forma Pago
        $arreglo_forma_pago         = array();
        $arreglo_forma_pago["null"] = "Seleccione";
        $listado_forma_pago         = $em->getRepository('schemaBundle:AdmiFormaPago')->findFormasPagoParaContrato();
        foreach ( $listado_forma_pago as $listado_forma_pago )
        {
            $arreglo_forma_pago[$listado_forma_pago->getId()."-".$listado_forma_pago->getDescripcionFormaPago()."-".$listado_forma_pago->getCodigoFormaPago()]=$listado_forma_pago->getDescripcionFormaPago();
        }
        //Tipo de cuenta
        $arreglo_tipo_cuenta         = array();
        $arreglo_tipo_cuenta["null"] = "Seleccione";
        $listado_tipo_cuenta         = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findByEstado("Activo");
        foreach ( $listado_tipo_cuenta as $listado_tipo_cuenta ){
        //   $arreglo_tipo_cuenta[$listado_tipo_cuenta->getId()."-".$listado_tipo_cuenta->getDescripcionCuenta()]=$listado_tipo_cuenta->getDescripcionCuenta();
        }

        /*$entityInfoPlanCondicion = new InfoPlanCondicion();
        $formInfoPlanCondic      = $this->createForm(new InfoPlanCondicionType(array('empresaId'=>$empresaId)), $entityInfoPlanCondicion);
        $formInfoPlanCondicion   = $formInfoPlanCondic->createView();   */
        $request                 = $this->getRequest();
        $prefijoEmpresa          = $request->getSession()->get('prefijoEmpresa');

        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        else
        {
            $info_detalle    = array();
            $condicPlan      = array();
            $estadoActivo    = "Activo";
            //Leo Condiciones de los planes
            $condicionesPlan = $em->getRepository('schemaBundle:InfoPlanCondicion')->getCondicionesPlanXPlan($id);
            $i               = 0;
            foreach( $condicionesPlan as $condiciones )
            {
                $condicPlan[$i]['idPlanCondic']      = $condiciones['id_plan_condicion'];
                $condicPlan[$i]['tipoNegocio']       = $condiciones['nombre_tipo_negocio'];
                $condicPlan[$i]['formaPago']         = $condiciones['descripcion_forma_pago'];
		$condicPlan[$i]['tipoCuenta']        = $condiciones['descripcion_cuenta'];
                $condicPlan[$i]['bancoTipoCuenta']   = $condiciones['descripcion_banco'];
                $condicPlan[$i]['tipoNegocioid']     = $condiciones['tipo_negocio_id'];
                $condicPlan[$i]['formaPagoId']       = $condiciones['forma_pago_id'];
		$condicPlan[$i]['tipoCuentaId']      = $condiciones['tipo_cuenta_id'];
                $condicPlan[$i]['bancoTipoCuentaId'] = $condiciones['banco_tipo_cuenta_id'];
                $i++;

                $info_detalle[]     = array('idPlanCondic'      => $condiciones['id_plan_condicion'],
                                            'tipoNegocioId'     => $condiciones['tipo_negocio_id'],
                                            'formaPagoId'       => $condiciones['forma_pago_id'],
                                            'tipoCuentaId'      => $condiciones['tipo_cuenta_id'],
                                            'bancoTipoCuentaId' => $condiciones['banco_tipo_cuenta_id']);
             }
             $arreglo_encode = json_encode($info_detalle);
        }
        $items                      = $em->getRepository('schemaBundle:InfoPlanDet')->getPlanIdYEstados($id);
	$arregloCaracteristicasPlan = array();
        $arregloCaracteristicasDet  = array();
        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

	if( $items )
	{
            $i = 0;
            //Leo productos
	    foreach($items as $item)
            {
                //Productos por planes
		$producto                   = $em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
                $arreglo[$i]['idproducto']  = $producto->getId();
		$arreglo[$i]['producto']    = $producto->getDescripcionProducto();
                $arreglo[$i]['instalacion'] = $producto->getInstalacion();
		$arreglo[$i]['cantidad']    = $item->getCantidadDetalle();
		$arreglo[$i]['precio']      = $item->getPrecioItem();
                $arreglo[$i]['instalacion'] = $producto->getInstalacion();
		$i++;

                //caracteristicas por productos
		$caracteristicasDet = $em->getRepository('schemaBundle:InfoPlanDet')->getCaracteristicas($item->getId());
		foreach( $caracteristicasDet as $caracteristica )
                {
                    $caractDet['idproducto'] = $producto->getId();
                    $caractDet['nombre'] = $caracteristica['nombre'];
		    $caractDet['valor'] = $caracteristica['valor'];
		    $caractDet['estado'] = $caracteristica['estado'];
		    $arregloCaracteristicasDet[] = $caractDet;
		}
            }
	}
	else
            $arreglo="";

	$parametros = array('entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            );

	if( $arreglo != "" )
            $parametros['items']=$arreglo;
	if( $arregloCaracteristicasDet != "" )
	    $parametros['caracteristicasDet'] = $arregloCaracteristicasDet;
        if( $arregloCaracteristicasPlan != "" )
	    $parametros['caracteristicasPlan'] = $arregloCaracteristicasPlan;

        $parametros['items_detalle']          = $condicPlan;
        $parametros['arreglo']                = $arreglo_encode;
        //$parametros['formInfoPlanCondicion']  = $formInfoPlanCondicion;
        $parametros['clase']                  = "campo-oculto";
        $parametros['arreglo_tipo_negocio']   = $arreglo_tipo_negocio;
        $parametros['arreglo_forma_pago']     = $arreglo_forma_pago;
        $parametros['arreglo_tipo_cuenta']    = $arreglo_tipo_cuenta;
        $parametros['objPlanProvieneRegClonado'] = $objPlanProvieneRegClonado;
        return $this->render('catalogoBundle:InfoPlanCaracteristica:newCondicionesPlanes.html.twig', $parametros);

    }

   /**
    * Funcion que Guarda condiciones agregadas a los planes
    * @author : telcos
    * @param request $request
    * @version 1.0 23-05-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function createCondicionesPlanAction(Request $request)
    {
        $entity           = new InfoPlanCondicion();
        $request          = $this->getRequest();
        $id_plan          = $request->get('id_plan');
        $datos            = $request->get('valores');
        $valores          = json_decode($datos);
        $em               = $this->getDoctrine()->getManager('telconet');
        $entityPlan       = $em->getRepository('schemaBundle:InfoPlanCab')->findOneById($id_plan);
        $estado           = 'Activo';
        $entityPlanCondic = $em->getRepository('schemaBundle:InfoPlanCondicion')->getPlanCondicion($id_plan);
        //Verificacion de items existentes
        $band =2;
        foreach( $entityPlanCondic as $item )
        {
            foreach( $valores as $valor )
            {
                if( $item->getId() == $valor->idPlanCondic && $valor->idPlanCondic != "" )
                {
                    $band = 1;
                    break;
                }
                else
                {
                    $band=2;
                }
            }
            if( $band == 2 )
            {
                $estado = "Inactivo";
                $item->setEstado($estado);
                $em->persist($item);
                $em->flush();
            }
        }

        if ( $id_plan != null )
        {
            foreach( $valores as $valor )
            {
                $encontro = false;
                foreach( $entityPlanCondic as $item )
                {
                    if( $valor->idPlanCondic == $item->getId() )
                    {
                        $encontro = true;
                        break;
                    }
                }
                if( $encontro == false )
                {
                    $idTipoNegocio     = $valor->tipoNegocioId;
                    $idFormaPago       = $valor->formaPagoId;
                    $idTipoCuenta      = $valor->tipoCuentaId;
                    $idBancoTipoCuenta = $valor->bancoTipoCuentaId;
                    $tipoNegocio       = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findOneById($idTipoNegocio);
                    if( $idFormaPago != "null" )
                        $formaPago=$em->getRepository('schemaBundle:AdmiFormaPago')->findOneById($idFormaPago);
                    if( $idTipoCuenta != "null")
                        $tipoCuenta=$em->getRepository('schemaBundle:AdmiTipoCuenta')->findOneById($idTipoCuenta);
                    if( $idBancoTipoCuenta != "null" )
                        $bancoTipoCuenta=$em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findOneById($idBancoTipoCuenta);

                    $estado  = 'Activo';
                    $entity  = new InfoPlanCondicion();
                    $entity->setPlanId($entityPlan->getId());
                    if( $idFormaPago != "null" )
                    {
                        $entity->setFormaPagoId($formaPago->getId());
                    }
                    if( $idTipoCuenta != "null" )
                    {
                        $entity->setTipoCuentaId($tipoCuenta->getId());
                    }
                    if( $idBancoTipoCuenta != "null" )
                    {
                        $entity->setBancoTipoCuentaId($bancoTipoCuenta->getId());
                    }

                    $entity->setTipoNegocioId($tipoNegocio->getId());
                    $entity->setFeCreacion(new \DateTime('now'));
                    $usrCreacion = $request->getSession()->get('user');
                    $entity->setUsrCreacion($usrCreacion);
                    $entity->setIpCreacion($request->getClientIp());
                    $entity->setEstado($estado);
                    $entity->setEmpresaCod($entityPlan->getEmpresaCod());
                    $em->persist($entity);
                    $em->flush();
                }
            }
        }
        return $this->redirect($this->generateUrl('infoplancaracteristicas_condiciones', array('id' => $id_plan)));
    }
     /**
     * Funcion que devuelve combo de lista de bancos asociados'
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2014
     * @param integer $tipoCuenta
     * @param integer $bcoTipoCtaId
     * @see \telconet\schemaBundle\Entity\AdmiBancoTipoCuenta
     * @return \Symfony\Component\HttpFoundation\Response
     *
     *
     * @author Andrés Montero<amontero@telconet.ec>
     * @version 1.1 29-06-2017
     * Se usa la nueva funcion para obtener bancos findBancosTipoCuentaPorCriterio
     */
     public function listaBancosAsociadosAction()
    {
        $request                          = $this->getRequest();
        $session                          = $request->getSession();
        $tipoCuenta                       = $request->request->get("tipoCuenta");
        $bancoTipoCuentaId                = $request->request->get("bcoTipoCtaId");
        $arrayParametros                  = array();
        $arrayParametros['strTipoCuenta'] = $tipoCuenta;
        $arrayParametros['arrayEstados']  = array('Activo','Activo-debitos');
        $arrayParametros['intPaisId']     = $session->get('intIdPais');
        $em                = $this->getDoctrine()->getManager('telconet');
        $arrayListadoBancos    = $em->getRepository('schemaBundle:AdmiBancoTipoCuenta')->findBancosTipoCuentaPorCriterio($arrayParametros);
        $presentacion_div  = "";
        $i                 = 0;
        if( $arrayListadoBancos )
        {
            $tam = 10;
            foreach ( $arrayListadoBancos as $bancos )
            {
	        if( $bancos->getEsTarjeta() == 'S' )
                {
                    $tam      = 16;
                    $es_banco = 'N';
                }
                else
                {
                    $es_banco = 'S';
		    $tam      = 15;
                }
		if( $bancoTipoCuentaId )
                {
	            if( $bancoTipoCuentaId == $bancos->getId() )
                    {
		        $presentacion_div.="<option value='".$bancos->getId()."-".$bancos->getBancoId()->getDescripcionBanco()."' selected>".$bancos->getBancoId()->getDescripcionBanco()."</option>";
		    }
                    else
                    {
			$presentacion_div.="<option value='".$bancos->getId()."-".$bancos->getBancoId()->getDescripcionBanco()."'>".$bancos->getBancoId()->getDescripcionBanco()."</option>";
		    }
		}
                else
                {
		    $presentacion_div.="<option value='".$bancos->getId()."-".$bancos->getBancoId()->getDescripcionBanco()."'>".$bancos->getBancoId()->getDescripcionBanco()."</option>";
		}
            }
            $arreglo = array('msg'=>'ok','tam'=>$tam,'div'=>$presentacion_div,'es_banco'=>$es_banco);
        }
        else
        {
            $arreglo = array('msg'=>'No existen bancos asociados');
        }

        $response = new Response(json_encode($arreglo));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
     /**
     * Funcion que devuelve combo de lista de tarjetas asociados'
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2014
     * @param integer $tipo
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.1 16-11-2018 - Se agrega a la funcion que verifique el tipo de cuenta por Pais, se verfica por prefijo empresa si se obtiene los
     *                           tipos de cuenta por el id del pais.
     *
     * @see \telconet\schemaBundle\Entity\AdmiTipoCuenta
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listarTarjetasCuentasAction()
    {
        $request           = $this->getRequest();
        $tipoTarjetaCuenta = $request->request->get("tipo");
        $objSession        = $request->getSession();
        $intIdPais         = $objSession->get('intIdPais');
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa');
        if( $tipoTarjetaCuenta == "tarjeta" )
        {
            $tipo = 'S';
        }
        else
        {
            $tipo = 'N';
        }
        $arrayParametros     = array('strTipo'           => $tipo,
                                     'intIdPais'         => $intIdPais,
                                     'strPrefijoEmpresa' => $strPrefijoEmpresa);

        $em                  = $this->getDoctrine()->getManager('telconet');
        $arrayListadoTipoCuenta = $em->getRepository('schemaBundle:AdmiTipoCuenta')->getTiposCuentaPorEsTarjetaActivos($arrayParametros);
        $strPresentacionDiv     = "";
        $i                   = 0;
        if( $tipo == 'N' )
        {
            $strPresentacionDiv = "<option value='null'>Seleccione</option>";
        }
        if( $arrayListadoTipoCuenta )
        {
            foreach ( $arrayListadoTipoCuenta as $objListadoTipoCuenta )
            {
     	       $strPresentacionDiv.="<option value='".$objListadoTipoCuenta->getId()."-".$objListadoTipoCuenta->getDescripcionCuenta()."'>".
                   $objListadoTipoCuenta->getDescripcionCuenta()."</option>";
            }
            $arrayArreglo = array('msg'=>'ok','div'=>$strPresentacionDiv);
        }
        else
        {
            $arrayArreglo = array('msg'=>'No existen tarjetas o cuentas asociadas');
        }

        $objResponse = new Response(json_encode($arrayArreglo));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
     /**
     * Funcion que verifica si el plan a ingresar posee un producto IP, si es el caso valida que se ingrese el numero de IPS maximas permitidas.'
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 1.0 23-05-2014
     * @param integer $ìntIpsMaxPermitidas
     * @param array   $arrayValores
     * @see \telconet\schemaBundle\Entity\AdmiProducto
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function validaTieneProductoIpAction()
    {
        $request                  = $this->getRequest();
        $ìntIpsMaxPermitidas      = $request->get("ips_max_permitidas");
        $arrayValores             = $request->get("valores");
        $em                       = $this->get('doctrine')->getManager('telconet');
        $arrayDetalles            = json_decode($arrayValores);
        $booltiene_ip             = false;
        foreach( $arrayDetalles as $valor )
        {
            $objProductoIP            = $em->getRepository('schemaBundle:AdmiProducto')->validaTieneProductoIp($valor->producto);
            if($objProductoIP!=null && $objProductoIP->getId())
            {
                $booltiene_ip=true;
            }
        }
        if($booltiene_ip)
        {
            if($ìntIpsMaxPermitidas<=0 || $ìntIpsMaxPermitidas=="" || !is_numeric($ìntIpsMaxPermitidas) || preg_match('/[^\d]/',$ìntIpsMaxPermitidas))
            {
                $response = new Response(json_encode(array('msg' =>'ok','mensaje_validaciones' => 'Debe ingresar numero Ips Maximas Permitidas para el Plan')));
            }
            else
            {
                $response = new Response(json_encode(array('msg' => '','mensaje_validaciones' =>'')));
            }
        }
        else
        {
            $response = new Response(json_encode(array('msg' => '','mensaje_validaciones' =>'')));
        }

        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
    * Funcion que presenta el formulario del registro del Plan a clonar en base a un plan existente
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 06-08-2021 - Se agrega modificación y se obtiene los valores parametrizados para el combo de tipo
    *                           categoría del plan para la empresa MD.
    * 
    * @param integer $id // id de plan
    * @version 1.0 14-07-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return Renders a view.
    */
    public function newClonarPlanesAction($id)
    {
        $peticion   = $this->get('request');
        $session    = $peticion->getSession();
        $empresaId  = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get("prefijoEmpresa") ? $session->get("prefijoEmpresa") : '';
        $em         = $this->getDoctrine()->getManager();

        $entity     = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        if ( !$entity )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }

        $arreglo_tipo = array();
        if( $entity )
        {
            $arreglo_tipo[$entity->getTipo()] = $entity->getTipo();
        }
        $listado_tipo = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findTiposNegocioPorEmpresa($empresaId);
        foreach ( $listado_tipo as $listado_tipo )
        {
            $arreglo_tipo[$listado_tipo->getNombreTipoNegocio()] = $listado_tipo->getNombreTipoNegocio();
        }

        $listado_detalle = array(""=>"Seleccione","P"=>"Paquete","S"=>"Solucion");

        //obtener listado de items ya ingresados
        $estado = "Activo";
        $items  = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$estado);
        if( $items )
        {
            $i=0;
            foreach( $items as $item )
            {
                $producto                    = $em->getRepository('schemaBundle:AdmiProducto')->find($item->getProductoId());
                $plandet[$i]['producto']     = $producto->getDescripcionProducto();
                $plandet[$i]['cantidad']     = $item->getCantidadDetalle();
                $plandet[$i]['precio_total'] = $item->getPrecioItem();
                $plandet[$i]['producto_id']  = $producto->getId();
                $i++;


                $info_detalle[] = array('producto' =>$producto->getId(),
                                        'cantidad' => $item->getCantidadDetalle(),
                                        'precio_total' => $item->getPrecioItem(),
                                        'id_det'=>$item->getId());

            }
        }
        else
        {
            $info_detalle[] = array();
            $plandet[]      = array();
        }
        $arreglo_encode      = json_encode($info_detalle);
        $editForm            = $this->createForm(new InfoPlanCabType(array('empresaId'=>$empresaId)), $entity);
        $deleteForm          = $this->createDeleteForm($id);
        $caracteristica      = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("IP_MAX_PERMITIDAS");
        $plan_caracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristicaEstados($entity,$caracteristica);
        $ips_max_permitidas  = "";
        if( $plan_caracteristica )
        {
           $ips_max_permitidas = $plan_caracteristica->getValor();
        }
        $caracteristica      = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("FRECUENCIA");
        $plan_caracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristicaEstados($entity,$caracteristica);
        $frecuencia  = "";
        if( $plan_caracteristica )
        {
           $frecuencia = $plan_caracteristica->getValor();
        }
        //codigo interno en base a la fecha de creacion sysdate formato yyyymm
        $dateFecha        = date("Y-m-d");
        $arrayFechaExp    = explode("-",$dateFecha);
        $strCodigoInterno = date ("Ym",strtotime($arrayFechaExp[0]."".$arrayFechaExp[1]));
        
        //Se agrega validaciones para el tipo de categoría plan a empresa MD.
        $arrayParamTipoPlan = array();
        $strValorCaractPlan = "";
        if(($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN') && !empty($strPrefijoEmpresa))
        {  
            $objCaractCategoriaPlan = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica("TIPO_CATEGORIA_PLAN_ADULTO_MAYOR");

            $objInfoPlanCaract      = $em->getRepository('schemaBundle:InfoPlanCaracteristica')
                                            ->findOneByIdPlanCaracteristicaEstados($entity,$objCaractCategoriaPlan);
            
            $strValorCaractPlan = is_object($objInfoPlanCaract) ? $objInfoPlanCaract->getValor() : "";
            
            $arrayParamTipoPlanes = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get("PARAM_FLUJO_ADULTO_MAYOR", "COMERCIAL", "", 
                                                          "CATEGORIA_PLAN_ADULTO_MAYOR", "", "", "", "", "",
                                                          $empresaId);
            
            foreach($arrayParamTipoPlanes as $objParamTipoPlanes)
            {
                $arrayParamTipoPlan[$objParamTipoPlanes['valor1']] = $objParamTipoPlanes['valor1'];
            } 
        }

        $parametros = array('entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            );
        $parametros['listado_detalle']    = $listado_detalle;
        $parametros['items_detalle']      = $plandet;
        $parametros['arreglo']            = $arreglo_encode;
        $parametros['arreglo_tipo']       = $arreglo_tipo;
        $parametros['ips_max_permitidas'] = $ips_max_permitidas;
        $parametros['frecuencia']         = $frecuencia;
        $parametros['strCodigoInterno']   = $strCodigoInterno;
        $parametros['strPrefijoEmpresa']  = $strPrefijoEmpresa;
        $parametros['strValorCaractPlan'] = $strValorCaractPlan;
        $parametros['arrayParamTipoPlan'] = $arrayParamTipoPlan;

        return $this->render('catalogoBundle:InfoPlanCaracteristica:newClonarPlanes.html.twig', $parametros);

    }
   /**
    * Funcion que verifica si el codigo y nombre del plan a clonar no se encuentran ya ingresados en un Plan en estado Activo, en
    * ese caso no permitira el clonado del plan hasta que ingresen codigo y nombre Unicos
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @version 1.0 16-07-2014
    * @param string $strCodigoPlan
    * @param string $strNombrePlan
    * @param integer $intPlanId
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return \Symfony\Component\HttpFoundation\Response
    */
    public function validaCodigoNombrePlanAction()
    {
        $objPeticion   = $this->get('request');
        $objSession    = $objPeticion->getSession();
        $strCodigoEmpresa  = $objSession->get('idEmpresa');
        
        $request         = $this->getRequest();
        $strCodigoPlan   = $request->get("codigo_plan");
        $strNombrePlan   = $request->get("nombre_plan");
        $intPlanId       = $request->get("plan_id");
        $em              = $this->get('doctrine')->getManager('telconet');
        $boolRepetido    = false;

        $objInfoPlanCab  = $em->getRepository('schemaBundle:InfoPlanCab')->find($intPlanId);
        if( $objInfoPlanCab )
        {
            $intPlanIdClon = $objInfoPlanCab->getPlanId();
        }
        else
        {
            $intPlanIdClon = -1;
        }
        $arrayParam[] = array("intIdPlan" => $intPlanId, 
                            "strCodigoPlan" => trim($strCodigoPlan), 
                            "strCodigoEmpresa" => $strCodigoEmpresa, 
                            "intPlanId" => $intPlanIdClon,
                            "strNombrePlan" => trim($strNombrePlan));
        
        $objInfoPlanCabCodigo  = $em->getRepository('schemaBundle:InfoPlanCab')->validaNombrePlan($arrayParam);
        $objInfoPlanCabNombre  = $em->getRepository('schemaBundle:InfoPlanCab')->validaNombrePlan($arrayParam);
        if( $objInfoPlanCabCodigo > 0 || $objInfoPlanCabNombre > 0 )
        {
            $boolRepetido = true;
        }

        if( !$boolRepetido )
        {
            if( $strNombrePlan == "" )
            {
                $response = new Response(json_encode(array('msg' =>'ok','mensaje_validaciones' => 'Debe ingresar nombre del plan')));
            }
            else
            {
                $response = new Response(json_encode(array('msg' => '','mensaje_validaciones' =>'')));
            }
        }
        else
        {
            if( $objInfoPlanCabCodigo > 0 && $objInfoPlanCabNombre > 0 )
            {
                 $response = new Response(json_encode(array('msg' => 'ok','mensaje_validaciones' =>'El codigo y nombre del plan debe ser unico, ya existen ['.$objInfoPlanCabCodigo.'] Codigo de Plan(es) ['.$strCodigoPlan.'] y ['.$objInfoPlanCabNombre.']  Nombre de Plan(es) ['.$strNombrePlan.'] en estado Activo')));
            }
            else
            {
                if( $objInfoPlanCabCodigo > 0 && $objInfoPlanCabNombre == 0 )
                {
                    $response = new Response(json_encode(array('msg' => 'ok','mensaje_validaciones' =>'El codigo del plan debe ser unico, ya existen ['.$objInfoPlanCabCodigo.'] Codigo de Plan(es) ['.$strCodigoPlan.'] en estado Activo')));
                }
                else
                {
                   if( $objInfoPlanCabCodigo == 0 && $objInfoPlanCabNombre > 0 )
                   {
                       $response = new Response(json_encode(array('msg' => 'ok','mensaje_validaciones' =>'El nombre del plan debe ser unico, ya existen ['.$objInfoPlanCabNombre.']  Nombre de Plan(es) ['.$strNombrePlan.'] en estado Activo')));
                   }
                }
            }
        }

        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
    * Funcion que guarda formulario de Plan Clonado
    * Consideraciones: Se guarda informacion del nuevo Plan clonado en base al plan Activo escogido,
    * El nuevo plan y sus registros se guardaran en estado pendiente
    * Para que pueda comercializarse debera pasar por la opcion de ->Activar o Liberar Plan
    * Los registros del plan que contienen la informacion de las condiciones comerciales(info_plan_condicion), caracteristicas de Plan(info_plan_caracteristica)
    * caracteristicas por promociones en instalacion por forma de pago (info_plan_caract_forma_pago) seran clonadas en base al plan del cual proviene la clonacion.
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param request $request
    * @param integer $id // Id del Plan del cual proviene el registro clonado
    * @version 1.0 23-05-2014
    * 
    * @author Alex Arreaga <atarreaga@telconet.ec> 
    * @version 1.1 06-08-2021 - Se agrega validación por empresa MD y se obtiene la característica TIPO_CATEGORIA_PLAN_ADULTO_MAYOR para 
    *                           asociarla al clonar plan.
    * 
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function updateClonarPlanesAction(Request $request, $id)
    {
        $request             = $this->getRequest();
        $datos               = $request->get('valores');
        $valores             = json_decode($datos);
        $request             = $this->getRequest();
        $session             = $request->getSession();
        $idEmpresa           = $session->get('idEmpresa');
        $usrCreacion         = $request->getSession()->get('user');
        $strEstadoPendiente  = 'Pendiente';
        $strEstadoActivo     = 'Activo';
        $strEstadoInactivo   = 'Clonado';
        $objFechaCreacion    = new \DateTime('now');
        $strPrefijoEmpresa   = $session->get("prefijoEmpresa") ? $session->get("prefijoEmpresa") : '';

        $em                  = $this->getDoctrine()->getManager();
        $em->getConnection()->beginTransaction();
        try
        {
        //Plan del cual proviene el registro a clonarse
        $objInfoPlanCabProviene    = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
        if ( !$objInfoPlanCabProviene )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        //GUARDAR INFO PLAN HISTORIAL DEL PLAN QUE ORIGINO LA CLONACION
        $objPlanHistorial = new InfoPlanHistorial();
        $objPlanHistorial->setPlanId( $objInfoPlanCabProviene );
        $objPlanHistorial->setIpCreacion( $request->getClientIp() );
        $objPlanHistorial->setFeCreacion( $objFechaCreacion );
        $objPlanHistorial->setUsrCreacion( $usrCreacion );
        $objPlanHistorial->setObservacion( 'Se realizó Clonacion del Plan : '.$objInfoPlanCabProviene->getNombrePlan() );
        $objPlanHistorial->setEstado( $strEstadoActivo );
        $em->persist($objPlanHistorial);
        $em->flush();
        //Creo nuevo registro de plan Clonado en estado Pendiente (Solo podrá comercializarse el plan en estado Activo es decir una vez Liberado el Plan
        $objInfoPlanCabClonado  = new InfoPlanCab();
        $objInfoPlanCabClonado->setCodigoPlan( $request->get('codigo_plan') );
        $objInfoPlanCabClonado->setNombrePlan( $request->get('nombre_plan') );
        $objInfoPlanCabClonado->setDescripcionPlan( $request->get('descripcion_plan_n') );
        $objInfoPlanCabClonado->setEmpresaCod( $idEmpresa );
        $objInfoPlanCabClonado->setDescuentoPlan( $request->get('descuento_plan') );
        $objInfoPlanCabClonado->setEstado( $strEstadoPendiente );
        $objInfoPlanCabClonado->setIpCreacion( $request->getClientIp());
        $objInfoPlanCabClonado->setFeCreacion( new \DateTime('now') );
        $objInfoPlanCabClonado->setUsrCreacion( $usrCreacion );
        $objInfoPlanCabClonado->setIva( "S" );
        $objInfoPlanCabClonado->setTipo( $request->get('tipo') );
        $objInfoPlanCabClonado->setPlanId( $request->get('plan_id') );
        $objInfoPlanCabClonado->setCodigoInterno( $request->get('codigo_interno') );
        $em->persist( $objInfoPlanCabClonado );
        $em->flush();

         //GUARDAR INFO PLAN HISTORIAL DEL REGISTRO CREADO DEL PLAN CLONADO EN ESTADO PENDIENTE
        $objPlanHistorial = new InfoPlanHistorial();
        $objPlanHistorial->setPlanId( $objInfoPlanCabClonado );
        $objPlanHistorial->setIpCreacion( $request->getClientIp() );
        $objPlanHistorial->setFeCreacion( $objFechaCreacion );
        $objPlanHistorial->setUsrCreacion( $usrCreacion );
        $objPlanHistorial->setObservacion( 'Se genero registro de Plan por Clonacion, Plan Clonado : '.$objInfoPlanCabProviene->getNombrePlan() );
        $objPlanHistorial->setEstado( $strEstadoPendiente );
        $em->persist($objPlanHistorial);
        $em->flush();

        //Guardo detalles del nuevo plan Clonado
        foreach( $valores as $valor )
        {
            $id_producto     = $valor->producto;
            $cantidad        = $valor->cantidad;
            $precio          = $valor->precio_total;
            $id_det          = $valor->id_det;
            if( $id_det == "" )
            {
                $prod_caract  = $valor->prod_caract;
                $valor_caract = $valor->valor_caract;
            }
            else
            {
                $prod_caract  = array();
                $valor_caract = array();
            }
            $objInfoPlanDetClonado  = new InfoPlanDet();
            $objInfoPlanDetClonado->setProductoId( $id_producto );
            $objInfoPlanDetClonado->setPlanId( $objInfoPlanCabClonado );
            $objInfoPlanDetClonado->setCantidadDetalle( $cantidad );
            $objInfoPlanDetClonado->setCostoItem( $precio );
            $objInfoPlanDetClonado->setPrecioItem( $precio );
            $objInfoPlanDetClonado->setDescuentoItem( 0 );
            $objInfoPlanDetClonado->setEstado( $strEstadoPendiente );
            $objInfoPlanDetClonado->setFeCreacion( new \DateTime('now') );
            $objInfoPlanDetClonado->setUsrCreacion( $usrCreacion );
            $objInfoPlanDetClonado->setIpCreacion( $request->getClientIp() );
            $em->persist( $objInfoPlanDetClonado );
            $em->flush();

            if( $id_det != "" )
            {
                //Clono las caracteristicas de los productos del plan que proviene
                $objInfoPlanProductoCaractProviene = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->getCaractPlanDetIdYEstado($id_det, $strEstadoActivo);
                foreach ($objInfoPlanProductoCaractProviene as $objInfoPlanProductoCaractProviene)
                {
                    $objInfoPlanProductoCaractClonado = new InfoPlanProductoCaract();
                    $objInfoPlanProductoCaractClonado = clone $objInfoPlanProductoCaractProviene;
                    $objInfoPlanProductoCaractClonado->setPlanDetId( $objInfoPlanDetClonado->getId() );
                    $objInfoPlanProductoCaractClonado->setProductoCaracterisiticaId( $objInfoPlanProductoCaractProviene->getProductoCaracterisiticaId() );
                    $objInfoPlanProductoCaractClonado->setValor( $objInfoPlanProductoCaractProviene->getValor() );
                    $objInfoPlanProductoCaractClonado->setFeCreacion( new \DateTime('now') );
                    $objInfoPlanProductoCaractClonado->setUsrCreacion( $usrCreacion );
                    $objInfoPlanProductoCaractClonado->setEstado( $strEstadoPendiente );
                    $em->persist( $objInfoPlanProductoCaractClonado );
                    $em->flush();
                }
            }
            //Guardo las caracteristicas sociadas a los productos agregados por pantalla
            for( $i=0; $i<sizeof($prod_caract); $i++ )
            {
                //Guardar informacion de la caracteristica del producto
                $objInfoPlanProductoCaractClonado  = new InfoPlanProductoCaract();
                $objInfoPlanProductoCaractClonado->setPlanDetId( $objInfoPlanDetClonado->getId() );
                $objInfoPlanProductoCaractClonado->setProductoCaracterisiticaId( $prod_caract[$i] );
                $objInfoPlanProductoCaractClonado->setValor( $valor_caract[$i] );
                $objInfoPlanProductoCaractClonado->setFeCreacion( new \DateTime('now') );
                $objInfoPlanProductoCaractClonado->setUsrCreacion( $usrCreacion );
                $objInfoPlanProductoCaractClonado->setEstado( $strEstadoPendiente );
                $em->persist( $objInfoPlanProductoCaractClonado );
                $em->flush();
            }
        }
        //Se toma solo los registros Activos de los detalles del Plan que seran Inactivados
        $objInfoPlanDetProviene = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$strEstadoActivo);
        //Guardo Ips maximas permitidas
        $intIpsMaxPermitidas     = $request->get('ips_max_permitidas');
        $objCaracteristicaIp     = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("IP_MAX_PERMITIDAS");
        if( $objCaracteristicaIp && $intIpsMaxPermitidas>0 )
        {
            $objInfoPlanCaracteristicaClonado = new InfoPlanCaracteristica();
            $objInfoPlanCaracteristicaClonado->setPlanId( $objInfoPlanCabClonado );
            $objInfoPlanCaracteristicaClonado->setCaracteristicaId( $objCaracteristicaIp );
            $objInfoPlanCaracteristicaClonado->setValor( $intIpsMaxPermitidas );
            $objInfoPlanCaracteristicaClonado->setEstado( $strEstadoPendiente );
            $objInfoPlanCaracteristicaClonado->setFeCreacion( new \DateTime('now') );
            $objInfoPlanCaracteristicaClonado->setUsrCreacion( $usrCreacion );
            $objInfoPlanCaracteristicaClonado->setIpCreacion( $request->getClientIp() );
            $em->persist( $objInfoPlanCaracteristicaClonado );
            $em->flush();
        }
        //Guardo Frecuencia o ciclo de facturacion
        $intFrecuencia        = $request->get('frecuencia');
        $objCaractFrecuencia  = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("FRECUENCIA");
        if( $objCaractFrecuencia && $intFrecuencia )
        {
            $objInfoPlanCaracteristicaClonado = new InfoPlanCaracteristica();
            $objInfoPlanCaracteristicaClonado->setPlanId( $objInfoPlanCabClonado );
            $objInfoPlanCaracteristicaClonado->setCaracteristicaId( $objCaractFrecuencia );
            $objInfoPlanCaracteristicaClonado->setValor( $intFrecuencia );
            $objInfoPlanCaracteristicaClonado->setEstado( $strEstadoPendiente );
            $objInfoPlanCaracteristicaClonado->setFeCreacion( new \DateTime('now') );
            $objInfoPlanCaracteristicaClonado->setUsrCreacion( $usrCreacion );
            $objInfoPlanCaracteristicaClonado->setIpCreacion( $request->getClientIp() );
            $em->persist( $objInfoPlanCaracteristicaClonado );
            $em->flush();
        }
        //Clono las Caracteristicas asociadas al Plan, solo las que esten en estado Activo y las guardo Pendiente hasta su Liberacion
        $objInfoPlanCaractProviene = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->getPlanCaractProviene($id);
        foreach ($objInfoPlanCaractProviene as $objInfoPlanCaractProviene)
        {

            $objInfoPlanCaracteristicaClonado = new InfoPlanCaracteristica();
            $objInfoPlanCaracteristicaClonado = clone $objInfoPlanCaractProviene;
            $objInfoPlanCaracteristicaClonado->setPlanId($objInfoPlanCabClonado);
            $objInfoPlanCaracteristicaClonado->setFeCreacion( new \DateTime('now') );
            $objInfoPlanCaracteristicaClonado->setUsrCreacion( $usrCreacion );
            $objInfoPlanCaracteristicaClonado->setIpCreacion( $request->getClientIp() );
            $objInfoPlanCaracteristicaClonado->setEstado( $strEstadoPendiente );
            $em->persist($objInfoPlanCaracteristicaClonado);
            $em->flush();
        }

        //Clono los registros de las condiciones comerciales del Plan solo las que esten en estado Activo y las guardo Pendiente hasta su Liberacion
        $objInfoPlanCondicionProviene = $em->getRepository('schemaBundle:InfoPlanCondicion')->getPlanCondicion($id);
        foreach($objInfoPlanCondicionProviene as $objInfoPlanCondicionProviene){
            $objInfoPlanCondicionClonado = new InfoPlanCondicion();
            $objInfoPlanCondicionClonado = clone $objInfoPlanCondicionProviene;
            $objInfoPlanCondicionClonado->setPlanId($objInfoPlanCabClonado->getId());
            $objInfoPlanCondicionClonado->setFeCreacion( new \DateTime('now') );
            $objInfoPlanCondicionClonado->setUsrCreacion( $usrCreacion );
            $objInfoPlanCondicionClonado->setIpCreacion( $request->getClientIp() );
            $objInfoPlanCondicionClonado->setEstado( $strEstadoPendiente );
            $em->persist($objInfoPlanCondicionClonado);
            $em->flush();
        }
        //Clono los registros de las caracteristicas del plan por forma de Pago
        $objCaractAplicaPromoInst  = $em->getRepository('schemaBundle:AdmiCaracteristica')->findOneByDescripcionCaracteristica("PINST APLICA PROMOCION");
        if($objCaractAplicaPromoInst)
        {
            $objPlanCaracteristicaActual = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristica($i,$objCaractAplicaPromoInst->getId(),$strEstadoActivo);
            if( $objPlanCaracteristicaActual )
            {
                $objInfoPlanCaractFormaPagoActual    = $em->getRepository('schemaBundle:InfoPlanCaractFormaPago')->findOneByPlanCaracteristicaId($objPlanCaracteristicaActual->getId());
                if( $objInfoPlanCaractFormaPagoActual )
                {
                    $objPlanCaracteristicaClonado  = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findOneByIdPlanCaracteristica($objInfoPlanCabClonado->getId(),$objCaractAplicaPromoInst->getId(),$strEstadoActivo);
                    if( $objPlanCaracteristicaClonado )
                    {
                        foreach($objInfoPlanCaractFormaPagoActual as $objInfoPlanCaractFormaPagoActual)
                        {
                            $objInfoPlanCaractFormaPagoClonado = new InfoPlanCaractFormaPago();
                            $objInfoPlanCaractFormaPagoClonado = clone $objInfoPlanCaractFormaPagoActual;
                            $objInfoPlanCaractFormaPagoClonado->setPlanCaracteristicaId($objPlanCaracteristicaClonado->getId());
                            $objInfoPlanCaractFormaPagoClonado->setFeCreacion( new \DateTime('now') );
                            $objInfoPlanCaractFormaPagoClonado->setUsrCreacion( $usrCreacion );
                            $objInfoPlanCaractFormaPagoClonado->setIpCreacion( $request->getClientIp() );
                            $objInfoPlanCaractFormaPagoClonado->setEstado( $strEstadoPendiente );
                            $em->persist( $objInfoPlanCaractFormaPagoClonado );
                            $em->flush();
                        }
                    }
                }
            }
        }
        
        //Se agrega validación por empresa MD, se guarda característica de tipo categoría del plan.
        if(($strPrefijoEmpresa === 'MD' || $strPrefijoEmpresa === 'EN') && !empty($strPrefijoEmpresa)) 
        {
            $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica("TIPO_CATEGORIA_PLAN_ADULTO_MAYOR");
            if($objAdmiCaracteristica)
            {
                $objPlanCaracteristica = new InfoPlanCaracteristica();
                $objPlanCaracteristica->setPlanId($objInfoPlanCabClonado);
                $objPlanCaracteristica->setCaracteristicaId($objAdmiCaracteristica);
                $objPlanCaracteristica->setValor($request->get('tipoCategoriaPlan')); 
                $objPlanCaracteristica->setEstado($strEstadoPendiente);
                $objPlanCaracteristica->setFeCreacion(new \DateTime('now'));
                $objPlanCaracteristica->setUsrCreacion($usrCreacion);
                $objPlanCaracteristica->setIpCreacion($request->getClientIp());
                $em->persist($objPlanCaracteristica);
                $em->flush();
            }
        }
        
        $em->commit();
        //redirecciono al show del plan Clonado
        return $this->redirect($this->generateUrl('infoplancaracteristicas_show', array('id' => $objInfoPlanCabClonado->getId())));

        } catch (\Exception $e) {
             $em->rollback();
             $em->close();
             $this->get('session')->getFlashBag()->add('notice', $e->getMessage());
             //redirecciono a pantalla de plana clonar con mensaje de error
             return $this->redirect($this->generateUrl('infoplancaracteristicas_clonar', array('id' => $id)));
        }
    }
    /**
    * Funcion que pasa a estado Activo un plan Clonado
    * Consideraciones: Solo se podra activar Planes en estado Pendiente
    * El Plan del cual proviene el nuevo registro de plan Clonado quedara en estado CLONADO (deshabilitado para su comercializacion)
    * Para que pueda comercializarse el Plan este debera pasar por la opcion de ->Activar o Liberar Plan el cual cambia el estado del
    * plan y sus registros relacionados a Activo
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param request $request
    * @param integer $id // Id del Plan
    * @version 1.0 23-05-2014
    * @version 1.1 14-08-2015
    * Se realiza cambio para que las caracteristicas ligadas al plan origen de la clonacion
    * y a sus productos no se alteren en la Activacion del Plan Clonado
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function activarAction($id)
    {
        $request             = $this->getRequest();
        $session             = $request->getSession();
        $em                  = $this->getDoctrine()->getManager();
        $strEstadoInactivo   = 'Clonado';
        $strEstadoActivo     = 'Activo';
        $objFechaCreacion    = new \DateTime('now');
        $strUsrCreacion      = $session->get('user');
        $objInfoPlanCab  = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);

        if (!$objInfoPlanCab)
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        if($objInfoPlanCab && $objInfoPlanCab->getPlanId() && $objInfoPlanCab->getEstado()=="Pendiente" )
        {
            //Plan del cual proviene el registro a clonarse
            $objInfoPlanCabProviene    = $em->getRepository('schemaBundle:InfoPlanCab')->find($objInfoPlanCab->getPlanId());
            if ( !$objInfoPlanCabProviene )
            {
                throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
            }
            //Inactivo registro del plan del cual proviene el Plan clonado
            $objInfoPlanCabProviene->setEstado( $strEstadoInactivo );
            $objInfoPlanCabProviene->setFeUltMod( $objFechaCreacion );
            $objInfoPlanCabProviene->setUsrUltMod( $strUsrCreacion );
            $em->persist( $objInfoPlanCabProviene );
            $em->flush();
            //Se toma solo los registros Activos de los detalles del Plan que seran Inactivados
            $objInfoPlanDetProviene = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($objInfoPlanCab->getPlanId(),$strEstadoActivo);
            //Verificacion de items(productos) existentes en el Plan que pasaran a estado Inactivo
            if( $objInfoPlanDetProviene )
            {
                foreach ($objInfoPlanDetProviene as $objInfoPlanDetProviene)
                {
                    $objInfoPlanDetProviene->setEstado( $strEstadoInactivo );
                    $em->persist( $objInfoPlanDetProviene );
                    $em->flush();
                }
            }

            //Inactivo registros de las condiciones del plan del cual proviene el clonado
            $objInfoPlanCondicionProviene = $em->getRepository('schemaBundle:InfoPlanCondicion')->getPlanCondicion($objInfoPlanCab->getPlanId());
            foreach($objInfoPlanCondicionProviene as $objInfoPlanCondicionProviene)
            {
                $objInfoPlanCondicionProviene->setEstado( $strEstadoInactivo );
                $objInfoPlanCondicionProviene->setFeUltMod( $objFechaCreacion );
                $objInfoPlanCondicionProviene->setUsrUltMod( $strUsrCreacion );
                $em->persist($objInfoPlanCondicionProviene);
                $em->flush();
            }
            //Guardo INFO PLAN HISTORIAL del plan origen del cual proviene el reg. clonado que pasara a estado "CLONADO"
            $objPlanHistorial = new InfoPlanHistorial();
            $objPlanHistorial->setPlanId( $objInfoPlanCabProviene );
            $objPlanHistorial->setIpCreacion( $request->getClientIp() );
            $objPlanHistorial->setFeCreacion( $objFechaCreacion );
            $objPlanHistorial->setUsrCreacion( $strUsrCreacion );
            $objPlanHistorial->setObservacion( 'Se deshabilita el plan origen de Clonacion para su venta-> Plan Origen:'.$objInfoPlanCabProviene->getNombrePlan().', 
                Plan Clonado: '.$objInfoPlanCab->getNombrePlan() );
            $objPlanHistorial->setEstado( $strEstadoInactivo );
            $em->persist($objPlanHistorial);
            $em->flush();

            //Paso a Activo los registros del Plan Clonado
            $objInfoPlanCab->setEstado( $strEstadoActivo );
            $objInfoPlanCab->setFeUltMod( $objFechaCreacion );
            $objInfoPlanCab->setUsrUltMod( $strUsrCreacion );
            $em->persist($objInfoPlanCab);
            $em->flush();

            $objInfoPlanDet = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,"Pendiente");
            if(isset($objInfoPlanDet))
            {
                foreach($objInfoPlanDet as $objInfoPlanDet)
                {
                    $objInfoPlanDet->setEstado( $strEstadoActivo );
                    $em->persist($objInfoPlanDet);
                    $em->flush();
                    $objInfoPlanProductoCaract = $em->getRepository('schemaBundle:InfoPlanProductoCaract')->getCaractPlanDetIdYEstado($objInfoPlanDet->getId(),"Pendiente");
                    if(isset($objInfoPlanProductoCaract))
                    {
                        foreach($objInfoPlanProductoCaract as $objInfoPlanProductoCaract)
                        {
                            $objInfoPlanProductoCaract->setEstado( $strEstadoActivo );
                            $objInfoPlanProductoCaract->setFeUltMod( $objFechaCreacion );
                            $objInfoPlanProductoCaract->setUsrUltMod( $strUsrCreacion );
                            $em->persist($objInfoPlanProductoCaract);
                            $em->flush();
                        }
                    }
                }
            }
            $objInfoPlanCaracteristica = $em->getRepository('schemaBundle:InfoPlanCaracteristica')->findPlanIdYEstado($id,"Pendiente");
            if(isset($objInfoPlanCaracteristica))
            {
                foreach($objInfoPlanCaracteristica as $objInfoPlanCaracteristica)
                {
                    $objInfoPlanCaracteristica->setEstado( $strEstadoActivo );
                    $em->persist($objInfoPlanCaracteristica);
                    $em->flush();
                }
            }
            $objInfoPlanCondicion = $em->getRepository('schemaBundle:InfoPlanCondicion')->findPlanIdYEstado($id,"Pendiente");
            if(isset($objInfoPlanCondicion))
            {
                foreach($objInfoPlanCondicion as $objInfoPlanCondicion)
                {
                    $objInfoPlanCondicion->setEstado( $strEstadoActivo );
                    $objInfoPlanCondicion->setFeUltMod( $objFechaCreacion );
                    $objInfoPlanCondicion->setUsrUltMod( $strUsrCreacion );
                    $em->persist($objInfoPlanCondicion);
                    $em->flush();
                }
            }
            //Guardo INFO PLAN HISTORIAL del plan liberado o activado
            $objPlanHistorial = new InfoPlanHistorial();
            $objPlanHistorial->setPlanId( $objInfoPlanCab );
            $objPlanHistorial->setIpCreacion( $request->getClientIp() );
            $objPlanHistorial->setFeCreacion( $objFechaCreacion );
            $objPlanHistorial->setUsrCreacion( $strUsrCreacion );
            $objPlanHistorial->setObservacion( 'Se Libera Plan Clonado para su venta -> Plan Clonado: '.$objInfoPlanCab->getNombrePlan() );
            $objPlanHistorial->setEstado( $strEstadoActivo );
            $em->persist($objPlanHistorial);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('infoplancaracteristicas_show', array('id' => $id)));
    }

   /**
    * Funcion que pasa a estado Activo un plan Inactivado
    * Consideraciones: Solo se podra Reactivar Planes en estado Inactivo
    * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
    * @param request $request
    * @param integer $id // Id del Plan
    * @version 1.0 07-08-2014
    * @see \telconet\schemaBundle\Entity\InfoPlanCab
    * @return a RedirectResponse to the given URL.
    */
    public function reactivarAction($id)
    {
        $request              = $this->getRequest();
        $session              = $request->getSession();
        $em                   = $this->getDoctrine()->getManager();
        $strEstadoActivo      = "Activo";
        $strEstadoInactivo    = "Inactivo";
        $objFechaCreacion   = new \DateTime('now');
        $strUsuarioCreacion = $session->get('user');
        $objInfoPlanCab     = $em->getRepository('schemaBundle:InfoPlanCab')->find($id);
        if (!$objInfoPlanCab )
        {
            throw $this->createNotFoundException('Unable to find InfoPlanCab entity.');
        }
        if($objInfoPlanCab && $objInfoPlanCab->getEstado()=="Inactivo" )
        {
            $objInfoPlanCab->setEstado( $strEstadoActivo );
            $objInfoPlanCab->setFeUltMod( $objFechaCreacion );
            $objInfoPlanCab->setUsrUltMod( $strUsuarioCreacion );
            $em->persist($objInfoPlanCab);
            $em->flush();

            $objInfoPlanDet = $em->getRepository('schemaBundle:InfoPlanDet')->findByPlanIdYEstado($id,$strEstadoInactivo);
            if( isset($objInfoPlanDet) )
            {
                foreach(($objInfoPlanDet)  as $det)
                {
                    $det->setEstado( $strEstadoActivo );
                    $em->persist($det);
                    $em->flush();
                }
            }
            //GUARDAR INFO PLAN HISTORIAL
            $objPlanHistorial = new InfoPlanHistorial();
            $objPlanHistorial->setPlanId( $objInfoPlanCab );
            $objPlanHistorial->setIpCreacion( $request->getClientIp() );
            $objPlanHistorial->setFeCreacion( $objFechaCreacion );
            $objPlanHistorial->setUsrCreacion( $strUsuarioCreacion );
            $objPlanHistorial->setObservacion( 'Se realiza Reactivacion de Plan' );
            $objPlanHistorial->setEstado( $strEstadoActivo );
            $em->persist($objPlanHistorial);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('infoplancaracteristicas'));
    }
}
