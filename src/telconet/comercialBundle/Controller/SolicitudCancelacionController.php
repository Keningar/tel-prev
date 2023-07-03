<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\Response;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Service\UtilService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * InfoDetalleSolicitud controller.
 *
 */
class SolicitudCancelacionController extends Controller implements TokenAuthenticatedController
{
     private $tipoSolicitud='SOLICITUD CANCELACION';
     private $relacion_sistema_id=1454;
     private $utilService;

     public function setDependencies(Container $objContainer)
     {  $this->container                = $objContainer;
        $this->utilService              = $objContainer->get('schema.Util');
     }

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
		
        return $this->render('comercialBundle:solicitudcancelacion:index.html.twig', array(
             'item' => $entityItemMenu,
            'entities' => '',
            'puntoId' => $puntoIdSesion
        ));
    }
    /**
     * Funcion que permite ingresar la solicitud de cancelacion para los servicios selecionados
     * @version 1.0 Version inicial
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.1 09-12-2022 - Se agrega la validacion cuado el producto es SE o ISB para generar la cancelacion
     *          de los demas servicios si es requerido
     */
    public function grabaSolicitudCancelacion_ajaxAction() 
    {
        $request = $this->getRequest();
        $session  = $request->getSession();         
        $idEmpresa = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $usrCreacion = $session->get('user');
        $strIpCreacion = $request->getClientIp();
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        //echo $id;die;
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $parametro = $peticion->get('param');
        $array_valor = explode("|", $parametro);
        $relacionSistemaId=$peticion->get('rs');
        $motivoId=$peticion->get('motivoId');
        $tipoSolicitudId=$peticion->get('ts');
        $fecha=$peticion->get('fecha');
        if ($fecha)
            $fechaarr=explode('T',$fecha);
        $obs=$peticion->get('obs');        
        //print_r($array_valor);echo "<br><br>";echo "relacionSistema:". $relacionSistemaId;echo "<br><br>";echo "fecha:".$fechaarr[0]."<br><br>";echo "motivoId:".$motivoId;die;
        $em->getConnection()->beginTransaction();
        try {
            foreach ($array_valor as $id):

                $entityServicio      = $em->getRepository('schemaBundle:InfoServicio')->find($id);
                $entityTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
                if($strPrefijoEmpresa == 'TN')
                {
                
                    $objProducto  = $em->getRepository('schemaBundle:AdmiProducto')->find($entityServicio->getProductoId());

                    if(!is_object($objProducto))
                    {
                        throw new \Exception('No se ha podido obtener el producto');
                    }
                    //Se agrega la validacion para determinar los servicios a cancelar del producto SAFE ENTRY
                    if ($objProducto->getNombreTecnico() == 'SAFE ENTRY' || $objProducto->getNombreTecnico() == 'INTERNET SMALL BUSINESS')
                    {   
                        $entityTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array('descripcionSolicitud' => 'CANCELACION', 'estado' =>'Activo' ));
                        if(!isset($entityTipoSolicitud))
                        {
                            throw new \Exception('No se ha podido obtener el tipo de solicitud');
                        }
                        $arrayParametros = array(
                            'objServicio'  => $entityServicio,
                            'objProducto'  => $objProducto,
                            'objSolicitud' => $entityTipoSolicitud,
                            'observacion'  => $obs,
                            'motivo'       => $motivoId,
                            'fecha'        => $fechaarr[0],
                            'intIdEmpresa' => $idEmpresa,
                            'usrCreacion'  => $usrCreacion,
                            'usrIp'        => $strIpCreacion);

                        $arrayRespuesta = $this->verificarServiciosCancelarSafeEntry($arrayParametros);

                        if($arrayRespuesta['status'] != 'OK')
                        {
                            throw new \Exception($arrayRespuesta['mensaje']);
                        }
                    }
                }


                $entity = new InfoDetalleSolicitud();
                $entity->setMotivoId($motivoId);
               
                $entity->setServicioId($entityServicio);

                $entity->setTipoSolicitudId($entityTipoSolicitud);
                $entity->setFeEjecucion(new \DateTime($fechaarr[0]));
                $entity->setObservacion($obs);
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($usrCreacion);
                $entity->setEstado('Pendiente');
                $em->persist($entity);
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
                
                //$entityServicio->setEstado('Pre-cancelado');
                //$em->persist($entityServicio);
                //$em->flush();

                //Grabamos en la tabla de historial del servicio
                //$entityServicioHistorial= new InfoServicioHistorial();
                //$entityServicioHistorial->setServicioId($entityServicio);                
                //$entityServicioHistorial->setEstado('Pre-cancelado');
                //$entityServicioHistorial->setUsrCreacion($usrCreacion);
                //$entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                //$entityServicioHistorial->setIpCreacion($request->getClientIp());
                //$em->persist($entityServicioHistorial);
                //$em->flush();                
				
            endforeach;
            $em->getConnection()->commit();
            $respuesta->setContent("Se registro solicitud con exito.");
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $strMensaje = $e ? $e->getMessage(): "Error al tratar de guardar solicitud. Consulte con el Administrador.";
            $respuesta->setContent($strMensaje);
        }
        return $respuesta;
    } 
    
    public function getMotivos_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')->loadMotivos($this->relacion_sistema_id);
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
     * Funcion para obtener los servicios para realizar al solicitud de cancelacion
     * 
     * @version 1.0 - Version inicial
     * 
     * @author Leonardo Mero <lemero@telconet.ec> 
     * @version 1.1 09-12-2022 - Para los servicios relacionados a SAFE ENTRY se agrega la validacion para la solicitud 
     *                           de CANCELACION
    * @Secure(roles="ROLE_62-161")
    */     
    public function getServiciosParaSolicitudCancelacion_ajaxAction($id) {
        $request = $this->getRequest();
        $filter = $request->request->get("filter");
        $estado = '';
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $estado = $request->get("estado");
        $nombre = $request->get("nombre");
        $limit = $request->get("limit");
        $page = $request->get("page");
        $start = $request->get("start");
        //$user = $this->get('security.context')->getToken()->getUser();
        $idEmpresa = $request->getSession()->get('idEmpresa');
        $strPrefijoEmpresa = $request->getSession()->get('prefijoEmpresa');
        $em = $this->get('doctrine')->getManager('telconet');
        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoIdPorEstado($idEmpresa,$id,'Activo');
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        foreach ($datos as $dato):
            //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
            $detalleSolicitud=$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findSolicDescuentoPorServicio($dato->getId(),$this->tipoSolicitud,'Pendiente');
            if(!is_object($detalleSolicitud) && $strPrefijoEmpresa == 'TN' && ($dato->getProductoId()->getNombreTecnico() == 'SAFE ENTRY' ||
               $dato->getProductoId()->getDescripcionProducto() == 'Cableado Estructurado'||
               $dato->getProductoId()->getNombreTecnico() == 'INTERNET SMALL BUSINESS'))
            {
                $detalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                       ->findSolicDescuentoPorServicio($dato->getId(),'CANCELACION','Pendiente');
            }
            if ($detalleSolicitud)
                $yaFueSolicitada='S';
            else
                $yaFueSolicitada='N';
            
            $idProducto='';
            $descripcionProducto='';
            if ($dato->getProductoId()){
                $idProducto=$dato->getProductoId()->getId();
                $descripcionProducto=$dato->getProductoId()->getDescripcionProducto();
                $tipo='producto';
            }elseif($dato->getPlanId())
            {
                $tipo='plan';
                $idProducto=$dato->getPlanId()->getId();
                $descripcionProducto=$dato->getPlanId()->getDescripcionPlan();                
            }            
            $arreglo[] = array(
                'idServicio' => $dato->getId(),
                'tipo'=>$tipo,
                'idPunto' => $dato->getPuntoId()->getId(),
                'descripcionPunto' => $dato->getPuntoId()->getDescripcionPunto(),
                'idProducto' => $idProducto,
                'descripcionProducto' => $descripcionProducto,
                'cantidad' => $dato->getCantidad(),
                'fechaCreacion' => strval(date_format($dato->getFeCreacion(), "d/m/Y G:i")),
                'precioVenta' => $dato->getPrecioVenta(),
                'estado' => $dato->getEstado(),
                'yaFueSolicitada' => $yaFueSolicitada
            );
            
        endforeach;
        if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'servicios' => $arreglo)));
        else {
            $arreglo[] = array();
            $response = new Response(json_encode(array('total' => $total, 'servicios' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }    

    
    public function aprobarSolicitudCancelacionAction()
    {
     return $this->render('comercialBundle:solicitudcancelacion:aprobarSolicitudCancelacion.html.twig', array());
    }    
    
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarSolicitudCancelacionAction()
    {
		$request = $this->getRequest();
		$request=$this->get('request');
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');		       
		$fechaDesde=explode('T',$request->get("fechaDesde"));
		$fechaHasta=explode('T',$request->get("fechaHasta"));
                $login=$request->get('login');
		$limit=$request->get("limit");
		$start=$request->get("start");
		$em = $this->get('doctrine')->getManager('telconet');
                //echo "fechaDesde:".$fechaDesde; die;
		//if ((!$fechaDesde[0])&&(!$fechaHasta[0]))
		//{
                //        $datos = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->find30SolicCancelacion($idEmpresa,$this->tipoSolicitud,'Pendiente');
		//}
		//else
		//{
                        $resultado= $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findSolicCancelacionPorCriterios('Pendiente',$this->tipoSolicitud,$idEmpresa,$start,$limit,$fechaDesde[0],$fechaHasta[0],$login);
		//}
        if($strPrefijoEmpresa == 'TN' && $resultado['total'] == 0)
        {
            $resultado = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->findSolicCancelacionPorCriterios('Pendiente','CANCELACION',$idEmpresa,$start,$limit,$fechaDesde[0],$fechaHasta[0],$login);
        }
            $datos = $resultado['registros'];
            $total = $resultado['total'];      
		foreach ($datos as $datos):
				$linkVer = '#';
				$descuento='';
				if($datos->getPrecioDescuento())
					$descuento=$datos->getPrecioDescuento();
				else
					$descuento=$datos->getPorcentajeDescuento().'%';
				$entityMotivo=$em->getRepository('schemaBundle:AdmiMotivo')->find($datos->getMotivoId());
				$producto='';
				if($datos->getServicioId()->getProductoId()){
					$entityProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($datos->getServicioId()->getProductoId()->getId());
					$producto=$entityProducto->getDescripcionProducto();
				}elseif($datos->getServicioId()->getPlanId()){
				//echo $datos->getServicioId()->getPlanId();
					$entityProducto=$em->getRepository('schemaBundle:InfoPlanCab')->find($datos->getServicioId()->getPlanId()->getId());
					$producto=$entityProducto->getNombrePlan();
				}
                                if ($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()){
                                    if ($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                                        $cliente=$datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                                    else
                                        $cliente=$datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getNombres()." ".
                                        $datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                                }
				$arreglo[]= array(
				'id'=>$datos->getId(),
				'servicio'=>$producto,
                                'cliente'=>$cliente,    
				'login'=> $datos->getServicioId()->getPuntoId()->getLogin(),
				'motivo'=> $entityMotivo->getNombreMotivo(),
				'descuento'=> $descuento,
				'observacion'=> $datos->getObservacion(),
				'feCreacion'=> strval(date_format($datos->getFeCreacion(),"d/m/Y G:i")),
				'usrCreacion'=> $datos->getUsrCreacion(),
				'linkVer'=> $linkVer
                 );    
		endforeach;
		if (!empty($arreglo))
            $response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));                    

		else
		{
			$arreglo[]= array();
			$response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));
		}		
		$response->headers->set('Content-type', 'text/json');
		return $response;
    }    
    
    
    public function rechazarSolicitudCancelacionAjaxAction(){

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
                $entity->setEstado('Anulado');
                $entity->setUsrRechazo($usrCreacion);
                $entity->setFeRechazo(new \DateTime('now'));                
                $em->persist($entity);
                $em->flush();					

                //Grabamos en la tabla de historial de la solicitud
                $entityHistorial= new InfoDetalleSolHist();
                $entityHistorial->setEstado('Anulado');
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
           $respuesta->setContent("Se anularon las solicitudes con exito.");            
       }       
	catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent($e->getMessage());            
	}
       

       return $respuesta;        

    }


    public function getMotivosRechazoSolicitudCancelacion_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacionsolicitudcancelacion','','rechazarsolicitudcancelacionajax');
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
    
    /**
     *  Funcion que permite el ingreso de una solicitud de cancelacion
     * 
     * @param array [objServicio,
     *               objSolicitud,
     *               usrCreacion,
     *               ipCreacion,
     *               observacion,
     *               motivo]
     * @return array [status => 'OK | ERROR',
     *                mensaje]
     * 
     * @author Leoanrdo Mero <lemero@telconet.ec>
     * @version 1.0 09-12-2022 - Version inicial
     */
    public function ingresarSolicitudCancelacion($arrayParametros)
    {   
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $objServicio    = $arrayParametros['objServicio'];
        $objSolicitud   = $arrayParametros['objSolicitud'];
        $strUsrCreacion = $arrayParametros['usrCreacion'];
        $strIpCreacion  = $arrayParametros['ipCreacion'];
        $strObservacion = $arrayParametros['observacion'];
        $intMotivoId    = $arrayParametros['motivo'];

        try 
        {

            $objDetalleSolicitud = new InfoDetalleSolicitud();
            $objDetalleSolicitud->setMotivoId($intMotivoId);
            $objDetalleSolicitud->setServicioId($objServicio);
            $objDetalleSolicitud->setTipoSolicitudId($objSolicitud);
            $objDetalleSolicitud->setFeEjecucion(new \DateTime($arrayParametros['fecha']));
            $objDetalleSolicitud->setObservacion($strObservacion);
            $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
            $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
            $objDetalleSolicitud->setEstado('Pendiente');
            $emComercial->persist($objDetalleSolicitud);
            $emComercial->flush();

            //Grabamos en la tabla de historial de la solicitud
            $objDetalleSolHistorial= new InfoDetalleSolHist();
            $objDetalleSolHistorial->setEstado('Pendiente');
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolicitud);
            $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setIpCreacion($strIpCreacion);
            $objDetalleSolHistorial->setMotivoId($intMotivoId);
            $objDetalleSolHistorial->setObservacion($strObservacion);
            $emComercial->persist($objDetalleSolHistorial);
            $emComercial->flush();

            $strStatus = 'OK';
            $strMensaje = 'Se realizo el registro de la solicitud de cancelacion correctamente';
            
        } 
        catch (\Exception $e) 
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->utilService->insertError("Telcos+",
                                            "SolicitudCancelacionController->ingresarSolicitudCancelacion",
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }
        return (array('status'=>$strStatus,'mensaje'=>$strMensaje));

    }

    /**
     * Funcion que permite verificar que servicios asosciados al SAFE ENTRY se enviaran a cancelar
     * 
     * @param array [objServicio,
     *               objSolicitud,
     *               usrCreacion,
     *               ipCreacion,
     * @return array [status => 'OK | ERROR',
     *                mensaje]
     * 
     * @author Leonardo Mero <lemero@telconeto.ec>
     * @version 1.0 09-12-2022 - Version inicial
     * 
     */
    public function verificarServiciosCancelarSafeEntry($arrayParametros)
    {
        $emGeneral   = $this->getDoctrine()->getManager('telconet_general');
        $emComercial = $this->getDoctrine()->getManager('telconet');

        $objProducto    = $arrayParametros['objProducto'];
        $objServicio    = $arrayParametros['objServicio'];
        $objSolicitud   = $arrayParametros['objSolicitud'];
        $strUsrCreacion = $arrayParametros['usrCreacion'];
        $strIpCreacion  = $arrayParametros['usrIp'];

        $strStatus  = 'OK';
        $strMensaje = '';
        try 
        {
            $arrayParametrosSafe = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('CONFIG SAFE ENTRY',
                                                        'COMERCIAL',
                                                        '',
                                                        'SERVICIOS_REQUERIDOS',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $arrayParametros["intIdEmpresa"]);
            if(!is_array($arrayParametrosSafe))
            {
                throw new \Exception('No se pudo encontrar los parametros para verificar la cancelacion del servicio Safe Entry');
            }
            //Verificamos los servicios a los que se enviara una solicitud de cancelacion
            $arrayServiciosCancelar = array_diff(json_decode($arrayParametrosSafe['valor1']), array($objProducto->getDescripcionProducto()));

            $arrayEstadosValidos = json_decode($arrayParametrosSafe['valor2']);
            
            if(!isset($arrayServiciosCancelar))
            {
                throw new \Exception('No se ha podido obtener los servicios a cancelar');
            }

            foreach($arrayServiciosCancelar as $strProducto)
            {
                //Se verifica que el servicio en el punto se encuentre activo
                $objProductoCancelar = $emComercial->getRepository('schemaBundle:AdmiProducto')
                                                   ->findOneBy(array('descripcionProducto' => $strProducto,
                                                                     'estado'        => 'Activo'),
                                                                     array('id'=> 'ASC' ));
                if(!isset($objProductoCancelar))
                {
                    throw new \Exception('No se ha podido obtener el producto');
                }

                $objServicioCancelar = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->findOneBy(array('productoId' => $objProductoCancelar->getId(),
                                                                     'puntoId'    => $objServicio->getPuntoId(),
                                                                     'estado'     => $arrayEstadosValidos));
                
                if(!isset($objServicioCancelar))
                {
                    $strMensaje .= 'El servicio '.$objProductoCancelar->getDescripcionProducto().' no se encuentra registrado en el punto.'.
                    ', se continua con el flujo normal';
                    break;
                }

                if($objServicioCancelar->getEstado() != 'Activo')
                {
                    throw new \Exception('El servicio '.$objProductoCancelar->getDescripcionProducto().' no se encuentra activo.'.
                    ' No se puede generar la solicitud de cancelacion automatica');
                }
                
                $objDetalleSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                   ->findOneBy(array('servicioId'      => $objServicio->getId(),
                                                                     'tipoSolicitudId' => $objSolicitud->getId(),
                                                                     'estado'          => 'Pendiente'));
            
                if(isset($objDetalleSolicitud))
                {
                    $strMensaje .= 'EL servicio '.$objProductoCancelar->getDescripcionProducto().' ya posee una solicitud registrada';
                    continue;
                }                
                //Se ingresa la solicitud del servicio para el servicio
                $arrayParametrosSolicitud = array(
                    'objServicio'  => $objServicioCancelar,
                    'objSolicitud' => $objSolicitud,
                    'observacion'  => $arrayParametros['observacion'],
                    'motivo'       => $arrayParametros['motivo'],
                    'fecha'        => $arrayParametros['fecha'],
                    'usrCreacion'  => $strUsrCreacion,
                    'ipCreacion'   => $strIpCreacion);

                $this->ingresarSolicitudCancelacion($arrayParametrosSolicitud);

                $strMensaje .= 'Se registro la solicitud de cancelacion para el servicio '.$objProductoCancelar->getDescripcionProducto().' \n';

            }
        } 
        catch (\Exception $e) 
        {
            $strStatus   = "ERROR";
            $strMensaje  = $e->getMessage();
            $serviceUtil = $this->get('schema.Util');
            $serviceUtil->insertError("Telcos+",
                                            "SolicitudCancelacionController->verificarServiciosCancelarSafeEntry",
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }
        return (array('status' => $strStatus, 'mensaje' => $strMensaje));

    }

    
}
