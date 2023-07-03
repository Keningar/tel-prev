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
/**
 * InfoDetalleSolicitud controller.
 *
 */
class SolicitudSuspensionTemporalController extends Controller implements TokenAuthenticatedController
{
     private $tipoSolicitud='SOLICITUD SUSPENSION TEMPORAL';
     private $relacion_sistema_id=2521;

     
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
		
        return $this->render('comercialBundle:solicitudsuspensiontemporal:index.html.twig', array(
             'item' => $entityItemMenu,
            'entities' => '',
            'puntoId' => $puntoIdSesion
        ));
    }

    public function grabaSolicitudSuspTemporal_ajaxAction() {
        $request = $this->getRequest();
        $session  = $request->getSession();         
        $idEmpresa = $session->get('idEmpresa');
        $usrCreacion=$session->get('user');
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
                $entity = new InfoDetalleSolicitud();
                $entity->setMotivoId($motivoId);
                $entityServicio= $em->getRepository('schemaBundle:InfoServicio')->find($id);
                $entity->setServicioId($entityServicio);
                $entityTipoSolicitud= $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
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
                
                //$entityPunto=$em->getRepository('schemaBundle:InfoPunto')->find($entityServicio->getPuntoId());
                //$entityPunto->setEstado('In-Temp');
                //$em->persist($entityPunto);
                //$em->flush();
                
                
                //$entityServicio->setEstado('In-Temp');
                //$em->persist($entityServicio);
                //$em->flush();

                //Grabamos en la tabla de historial del servicio
                //$entityServicioHistorial= new InfoServicioHistorial();
                //$entityServicioHistorial->setServicioId($entityServicio);
                //$entityServicioHistorial->setEstado('In-Temp');
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
            $respuesta->setContent("error al tratar de guardar solicitud. Consulte con el Administrador.");
        }
        return $respuesta;
    } 
    
    public function getMotivos_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $peticion = $this->get('request');       
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

        
        
    public function getServiciosParaSolicitudSuspTemporal_ajaxAction($id) {
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

        $em = $this->get('doctrine')->getManager('telconet');
        
        $estados = array("Activo","Activa");
        
        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPuntoIdPorVariosEstados($idEmpresa,$id,$estados);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        //print_r($entityAdmiTipoSolicitudCancelacion);die;
        $entityAdmiTipoSolicitudSuspensionTemp = $em->getRepository('schemaBundle:AdmiTipoSolicitud')->findOneByDescripcionSolicitud('SOLICITUD SUSPENSION TEMPORAL');
        foreach ($datos as $dato):
            //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
            $detalleSolicitudSuspensionTemp=$em->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->findOneBy(array("servicioId"=>$dato->getId(),"tipoSolicitudId"=>$entityAdmiTipoSolicitudSuspensionTemp->getId(),"estado"=>'Pendiente'));
        
            if ($detalleSolicitudSuspensionTemp)
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

    
    public function aprobarSolicitudSuspensionTemporalAction()
    {
     return $this->render('comercialBundle:solicitudsuspensiontemporal:aprobarSolicitudSuspensionTemporal.html.twig', array());
    }    
    
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarSolicitudSuspensionTemporalAction()
    {
		$request = $this->getRequest();
		$request=$this->get('request');
		$session=$request->getSession();
		$idEmpresa=$session->get('idEmpresa');		       
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
    
    
    public function rechazarSolicitudSuspensionTemporalAjaxAction(){

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


    public function getMotivosRechazoSolicitudSuspensionTemporal_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacionsolicitudsuspensiontemporal','','rechazarsolicitudsuspensiontemporalajax');
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
