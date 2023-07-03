<?php


namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SolicitudInstalacionController
 *
 * @author taty
 */
class SolicitudDescuentoUnicoController extends Controller implements TokenAuthenticatedController {
   
     private $tipoSolicitud='SOLICITUD DESCUENTO UNICO';


    /**
    * @Secure(roles="ROLE_62-1")
    */ 
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $session  = $request->getSession();
		$puntoIdSesion=null;
        $ptoCliente_sesion=$session->get('ptoCliente');
		if($ptoCliente_sesion){  
			$puntoIdSesion=$ptoCliente_sesion['id'];
		}
//        $entities = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findAll();
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("62", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());  
		
        return $this->render('comercialBundle:solicituddescuentounico:index.html.twig', array(
             'item' => $entityItemMenu,
            'entities' => '',
            'puntoId' => $puntoIdSesion
        ));
    }

    /**
     * grabaSolicitudDesc_ajaxAction, Se agrega relacion entre la solictud de descuento unico y la caracteristica
     *                                relacionada al tipo de descuento, UNITARIO o TOTALIZADO
     *                                                       
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.1 04-05-2017
     */
    public function grabaSolicitudDesc_ajaxAction() {
        $request            = $this->getRequest();
        $session            = $request->getSession();         
        $idEmpresa          = $session->get('idEmpresa');
        $usrCreacion        = $session->get('user');
        $strPrefijoEmpresa  = $session->get('prefijoEmpresa');
        $respuesta          = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        $respuesta->setContent("error del Form");
        $em                 = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion           = $this->get('request');
        $parametro          = $peticion->get('param');
        $array_valor        = explode("|", $parametro);
        $relacionSistemaId  = $peticion->get('rs');
        $motivoId           = $peticion->get('motivoId');
        $tipoSolicitudId    = $peticion->get('ts');
        $tipoValor          = $peticion->get('tValor');
        $valor              = $peticion->get('v');
        $obs                = $peticion->get('obs');
        
        $strTipoDescuento   = $peticion->get('tipoDescuento');
        $strDescripcionCarac= '';
        $serviceComercial   = $this->get('comercial.Comercial');
        $strMsnError        = '';
        $intCantidad        = 0;
        $precioDctoTotal    = 0;
        $precioDctoUni      = 0;
        
        $em->getConnection()->beginTransaction();
        try {
            
            if($strTipoDescuento =='DESCUENTO_UNITARIO')
            {
                $strDescripcionCarac = 'DESCUENTO UNITARIO FACT';
            }
            else if ($strTipoDescuento == 'DESCUENTO_TOTALIZADO')
            {
                $strDescripcionCarac = 'DESCUENTO TOTALIZADO FACT';
            }
            
            foreach ($array_valor as $id):
                $array                 = explode("-", $id);
                $idservicio            = $array[0];
                $precioVenta           = $array[1];
                $descuentoFijo         = $array[2]; //SI o NO
                $precioDescuentoFijo   = $descuentoFijo= $array[3];
           
                $entity              = new InfoDetalleSolicitud();
                $entity->setMotivoId($motivoId);
                $entityServicio      = $em->getRepository('schemaBundle:InfoServicio')->find($idservicio);
                $intCantidad         = $entityServicio->getCantidad();
                
                if($strTipoDescuento =='DESCUENTO_TOTALIZADO' && $strPrefijoEmpresa === 'TN')
                {
                    
                    $precioDctoTotal            = ($intCantidad * $precioVenta) * ($valor / 100);
                    $precioDctoTotal            = number_format( $precioDctoTotal, 2  );
                    $obs                        .= ' Descuento Totalizado: '.  $precioDctoTotal.'|';
                    $precioDctoUni              = number_format( ($precioDctoTotal / $intCantidad), 2) ; 
                    $obs                        .= ' Descuento Unitario: '.  $precioDctoUni;
                }
                
                if($precioDescuentoFijo==0)
                 {
                   $precioDescuento = $precioDctoTotal;
                 }
                 else
                 {
                     $precioDescuento =  number_format(  ($intCantidad * $precioDescuentoFijo) * ( $valor / 100) , 2) ; 
                 }
                
                $entity->setServicioId($entityServicio);
                $entityTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
                $entity->setTipoSolicitudId($entityTipoSolicitud);
                
                $entity->setPrecioDescuento(number_format( $precioDescuento, 2));
                $entity->setPorcentajeDescuento(number_format( $valor , 2));
                $entity->setObservacion($obs);
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($usrCreacion);
                $entity->setEstado('Pendiente');
                $em->persist($entity);
                $em->flush();
                
                //Busca la caracteristica asociada al descuento.
                $entityAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array('descripcionCaracteristica' => $strDescripcionCarac,
                                                                 'estado'                    => 'Activo'));
                
                if( !is_object($entityAdmiCaracteristica) )
                {
                    $strMsnError = 'No se pudo generar solicitud de descuento unico, no existe caracteristica asociada a la empresa.';
                    throw new \Exception( $strMsnError );
                }
                
                //Crea array para generar el objeto detalle solicitud caracteristica
                $arrayRequestDetalleSolCaract = array();
                $arrayRequestDetalleSolCaract['entityAdmiCaracteristica']   = $entityAdmiCaracteristica;
                $arrayRequestDetalleSolCaract['floatValor']                 = number_format( $precioDescuento  , 2);
                $arrayRequestDetalleSolCaract['entityDetalleSolicitud']     = $entity;
                $arrayRequestDetalleSolCaract['strEstado']                  = 'Pendiente';
                $arrayRequestDetalleSolCaract['strUsrCreacion']             = $usrCreacion;
                
                //Crea el objeto InfoDetalleSolCaract
                $entityDetalleSolCaract = $serviceComercial->creaObjetoInfoDetalleSolCaract($arrayRequestDetalleSolCaract);
                
                $em->persist($entityDetalleSolCaract);
                $em->flush();
                
                //Grabamos en la tabla de historial de la solicitud
				$entityHistorial= new InfoDetalleSolHist();
				$entityHistorial->setEstado('Pendiente');
				$entityHistorial->setDetalleSolicitudId($entity);
				$entityHistorial->setUsrCreacion($usrCreacion);
				$entityHistorial->setFeCreacion(new \DateTime('now'));
				$entityHistorial->setIpCreacion($request->getClientIp());
				$entityHistorial->setMotivoId($motivoId);
				$entityHistorial->setObservacion($obs);
                $em->persist($entityHistorial);
                $em->flush();
				
            endforeach;
            $em->getConnection()->commit();
            $respuesta->setContent("Se registro solicitud con exito.");
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            error_log('SolicitudDescuentoUnicoController->grabaSolicitudDesc_ajaxAction '  . $e->getMessage());
            
            if(empty( $strMsnError )) {
                $strMsnError = 'Ocurrio un error al generar la solicitud de descuento unico. ';
            }
            
            $respuesta->setContent( $strMsnError );
        }
        return $respuesta;
    } 
    
    public function getMotivos_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')->findMotivosPorDescripcionTipoSolicitud($this->tipoSolicitud);
        $entityAdmiTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findByDescripcionSolicitud($this->tipoSolicitud);
	$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId(),
                'idTipoSolicitud'=> $entityAdmiTipoSolicitud[0]->getId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }      
    
    /**
    * Documentación para funcion 'getServiciosParaSolicitudDesc_ajaxAction'.
    * Funcion que envia los datos de los servicios para el listado de solicitudes 
    * de descuento unico.
    * Se agrega nombre del producto al listado de solicitudes de descuento unico.
    * 
    * @author <rcoello@telconet.ec>
    * @version 1.1 04-05-201
    * @return objeto - response
    * 
    * @Secure(roles="ROLE_62-161")
    */
    public function getServiciosParaSolicitudDesc_ajaxAction($id) {
        $request    = $this->getRequest();
        $filter     = $request->request->get("filter");
        $estado     = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado     = $request->get("estado");
        $nombre     = $request->get("nombre");
        $limit      = $request->get("limit");
        $page       = $request->get("page");
        $start      = $request->get("start");
        $tipo       = $request->query->get('tipo');
        $idEmpresa  = $request->getSession()->get('idEmpresa');

        $em         = $this->get('doctrine')->getManager('telconet');
        
        if($tipo=='servicios')
        {
            $resultado = $em->getRepository('schemaBundle:InfoServicio')
                            ->findServiciosPoraSolicitadDescuentoUnico($idEmpresa,
                                                                       $id,
                                                                       $limit,
                                                                       $page,
                                                                       $start);
            $datos     = $resultado['registros'];
            $total     = $resultado['total'];
       
            foreach ($datos as $dato):
                //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
                $detalleSolicitud   =   $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->findSolicDescuentoPorServicio($dato->getId(),
                                                                           $this->tipoSolicitud,
                                                                           'Pendiente');
                if ($detalleSolicitud)
                    $yaFueSolicitada='S';
                else
                    $yaFueSolicitada='N';

                $idProducto             = '';
                $descripcionProducto    = '';
                $strNombreProducto      = '';

                if ($dato->getProductoId()){
                    $idProducto             = $dato->getProductoId()->getId();
                    $descripcionProducto    = $dato->getProductoId()->getDescripcionProducto();
                    $strNombreProducto      = $dato->getProductoId()->getDescripcionProducto();
                    $tipo                   = 'producto';
                }
                elseif($dato->getPlanId())
                {
                    $tipo                   = 'plan';
                    $idProducto             = $dato->getPlanId()->getId();
                    $descripcionProducto    = $dato->getPlanId()->getDescripcionPlan();
                    $strNombreProducto      = $dato->getPlanId()->getNombrePlan();
                }


                $tieneDescuento             =   false;
                $precioDescuento            =   0;

                if($dato->getPorcentajeDescuento())
                {
                    $precioDescuento        =   (($dato->getPrecioVenta())*($dato->getPorcentajeDescuento()))/100;
                    $tieneDescuento         =   true;   
                }

                if($dato->getValorDescuento()){
                    $precioDescuento        =   ($dato->getPrecioVenta()) - ($dato->getValorDescuento());
                    $tieneDescuento         =   true;   
                }

                $descuentoFijo      =   "NO";
                
                if($tieneDescuento){
                    $descuentoFijo  =   "SI";
                }
                
                $arreglo[] = array(
                    'idServicio'            => $dato->getId(),
                    'tipo'                  => $tipo,
                    'idPunto'               => $dato->getPuntoId()->getId(),
                    'descripcionPunto'      => $dato->getPuntoId()->getDescripcionPunto(),
                    'idProducto'            => $idProducto,
                    'descripcionProducto'   => $descripcionProducto,
                    'cantidad'              => $dato->getCantidad(),
                    'fechaCreacion'         => strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")),
                    'precioVenta'           => $dato->getPrecioVenta(),
                    'descuentoFijo'         => $descuentoFijo,
                    'precioDescuentoFijo'   => $precioDescuento,
                    'estado'                => $dato->getEstado(),
                    'yaFueSolicitada'       => $yaFueSolicitada,
                    'strNombreProducto'     => $strNombreProducto
                );

            endforeach;
        }//fin si es solicitud de tipo servicio
        
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total'      => $total, 
                                                       'servicios'  => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total'      => $total, 
                                                       'servicios'  => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    


    public function aprobarDescuentoAction()
    {
     return $this->render('comercialBundle:solicituddescuentounico:aprobarDescuento.html.twig', array());
    }

    /**
    * Documentación para funcion 'gridAprobarDescuentoAction'.
    * funcion que envia los datos para el listado de solicitudes de descuento unico
    * 
    * @author Edgar Holguín <eholguin@telconet.ec>
    * @version 1.1 27-12-2017 Se agrega envío de array de parámetros.
    * 
    * @author <amontero@telconet.ec>
    * @since 12/12/2014
    * @return objeto - response
    */     
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarDescuentoAction()
    {
		$request    = $this->getRequest();
		$request    = $this->get('request');
		$session    = $request->getSession();
		$idEmpresa  = $session->get('idEmpresa');		       
		$fechaDesde = explode('T',$request->get("fechaDesde"));
		$fechaHasta = explode('T',$request->get("fechaHasta"));
		$limit      = $request->get("limit");
		$start      = $request->get("start");
		$em         = $this->get('doctrine')->getManager('telconet');

        $arrayParametros['strEstado']          = 'PENDIENTE';
        $arrayParametros['strTipoSolicitud']   = $this->tipoSolicitud;
        $arrayParametros['intIdEmpresa']       = $idEmpresa;
        $arrayParametros['strFechaDesde']      = $fechaDesde;
        $arrayParametros['strFechaHasta']      = $fechaHasta;              
        $arrayParametros['intStart']           = $start;
        $arrayParametros['intLimit']           = $limit;
        $resultado = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findSolicDescuentoPorCriterios($arrayParametros);       
        $datos=$resultado['registros'];
        $total=$resultado['total'];

		foreach ($datos as $datos)
        {    
				$linkVer = '#';
				$descuento='';
				if($datos->getPrecioDescuento())
                {    
					$descuento=$datos->getPrecioDescuento();
                }    
				else
                {    
					$descuento=$datos->getPorcentajeDescuento().'%';
                }    
				$entityMotivo=$em->getRepository('schemaBundle:AdmiMotivo')->find($datos->getMotivoId());
				$producto='';
				if($datos->getServicioId()->getProductoId())
                {
					$entityProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($datos->getServicioId()->getProductoId()->getId());
					$producto=$entityProducto->getDescripcionProducto();
				}
                elseif($datos->getServicioId()->getPlanId())
                {
					$entityProducto=$em->getRepository('schemaBundle:InfoPlanCab')->find($datos->getServicioId()->getPlanId()->getId());
					$producto=$entityProducto->getNombrePlan();
				}
                if ($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId())
                {
                    if ($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                    {    
                        $cliente=$datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                    }    
                    else
                    {    
                        $cliente=$datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getNombres()." ".
                        $datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                    }    
                }
				$arreglo[]= array(
				'id'          => $datos->getId(),
				'servicio'    => $producto,
                'cliente'     => $cliente,    
				'login'       => $datos->getServicioId()->getPuntoId()->getLogin(),
				'motivo'      => $entityMotivo->getNombreMotivo(),
				'descuento'   => $descuento,
				'observacion' => $datos->getObservacion(),
				'feCreacion'  => strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
				'usrCreacion' => $datos->getUsrCreacion(),
				'linkVer'     => $linkVer
                 );    
        }
		if (!empty($arreglo))
        {    
			$response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));
        }    
		else
		{
			$arreglo[]= array();
			$response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));
		}		
		$response->headers->set('Content-type', 'text/json');
		return $response;
    }


    /*
    * @Secure(roles="ROLE_")
    */
    public function aprobarDescuentoAjaxAction()
    {
        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $session  = $request->getSession();         
        $usrCreacion=$session->get('user');		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id):             
                $entity = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entity->setEstado('Aprobado');
                $em->persist($entity);
                $em->flush();
				
				//Grabamos en la tabla de historial de la solicitud
				$entityHistorial= new InfoDetalleSolHist();
				$entityHistorial->setEstado('Aprobado');
				$entityHistorial->setDetalleSolicitudId($entity);
				$entityHistorial->setUsrCreacion($usrCreacion);
				$entityHistorial->setFeCreacion(new \DateTime('now'));
				$entityHistorial->setIpCreacion($request->getClientIp());
                $em->persist($entityHistorial);
                $em->flush();					
                
				//CAMBIA PRECIO AL SERVICIO
				/*$entityServicio=$em->getRepository('schemaBundle:InfoServicio')->find($entity->getServicioId()->getId());
				if($entity->getPrecioDescuento()){
					$entityServicio->setValorDescuento($entity->getPrecioDescuento());
				}elseif($entity->getPorcentajeDescuento()){
					$entityServicio->setPorcentajeDescuento($entity->getPorcentajeDescuento());
				}
                                
                $em->persist($entityServicio);
                $em->flush();	
                                 
                                 */				
				
           endforeach;
             
           $em->getConnection()->commit();   
           $respuesta->setContent("Se aprobaron las solicitudes con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       

       return $respuesta;
    }
    
    public function rechazarSolicitudDescuentoAjaxAction(){

        $request=$this->getRequest();
        $idEmpresa=$request->getSession()->get('idEmpresa');
        $session  = $request->getSession();         
        $usrCreacion=$session->get('user');		
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $motivoId = $peticion->get('motivoId');
        $array_valor = explode("|",$parametro);       
        
        $em->getConnection()->beginTransaction();
 	try{  
            foreach($array_valor as $id){            
                $entity = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
                if (!$entity) {
                        throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $entity->setEstado('Rechazado');
                $entity->setUsrRechazo($usrCreacion);
                $entity->setFeRechazo(new \DateTime('now'));                
                $em->persist($entity);
                $em->flush();					

                //Grabamos en la tabla de historial de la solicitud
                $entityHistorial= new InfoDetalleSolHist();
                $entityHistorial->setEstado('Rechazado');
                $entityHistorial->setDetalleSolicitudId($entity);
                $entityHistorial->setUsrCreacion($usrCreacion);
                $entityHistorial->setFeCreacion(new \DateTime('now'));
                $entityHistorial->setIpCreacion($request->getClientIp());
                $entityHistorial->setMotivoId($motivoId);
                //$entityHistorial->setObservacion($obs);
                $em->persist($entityHistorial);
                $em->flush();							
            }
             
           $em->getConnection()->commit();   
           $respuesta->setContent("Se aprobaron las solicitudes con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       

       return $respuesta;        

    }


    public function getMotivosRechazoDescuento_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobaciondescuentounico','AutorizacionDescuentoUnico','rechazarSolicitudDescuentoUnicoAjax');
		$arreglo=array();
    //print_r($datos);die;
    foreach($datos as $valor):
        //print_r($entityAdmiTipoSolicitud[0]->getId());
            $arreglo[] = array(
                'idMotivo' => $valor->getId(),
                'descripcion' => $valor->getNombreMotivo(),
                'idRelacionSistema'=>$valor->getRelacionSistemaId()
            );
    endforeach;
    //die;

        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
}

?>
