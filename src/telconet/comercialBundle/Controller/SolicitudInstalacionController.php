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
use telconet\schemaBundle\Entity\InfoServicioHistorial;
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SolicitudInstalacionController
 *
 * @author taty
 */
class SolicitudInstalacionController extends Controller implements TokenAuthenticatedController {
   
     private $tipoSolicitud='SOLICITUD INSTALACION GRATIS';
     const RANGO_APROBACION_SOLICITUDES      = 'RANGO_APROBACION_SOLICITUDES';
     const ADMINISTRACION_CARGOS_SOLICITUDES = 'ADMINISTRACION_CARGOS_SOLICITUDES';
     const COMERCIAL                         = 'COMERCIAL';
     const CARGO_GRUPO_ROLES_PERSONAL        = 'CARGO_GRUPO_ROLES_PERSONAL';
     const GRUPO_ROLES_PERSONAL              = 'GRUPO_ROLES_PERSONAL';
     const GERENTE_VENTAS                    = 'GERENTE_VENTAS';
     const ROLES_NO_PERMITIDOS               = 'ROLES_NO_PERMITIDOS';
     const VALOR_INICIAL_BUSQUEDA            = 0;
     const VALOR_LIMITE_BUSQUEDA             = 10;
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
		
        return $this->render('comercialBundle:solicitudinstalacion:index.html.twig', array(
             'item' => $entityItemMenu,
            'entities' => '',
            'puntoId' => $puntoIdSesion
        ));
    }

    public function grabaSolicitudDesc_ajaxAction() {
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
        $tipoValor=$peticion->get('tValor');
        $valor=$peticion->get('v');
       $observacion=$peticion->get('obs');
    
       /* print_r($array_valor);  die();
        print_r($relacionSistemaId);
        print_r($motivoId);
        print_r($valor);
         print_r($observacion); die();
       
       
        die();*/
        $obs=$peticion->get('obs');        
        //print_r($array_valor);echo "<br><br>";echo $relacionSistemaId;echo "<br><br>";echo $motivoId;die;
        $em->getConnection()->beginTransaction();
        try {
            
            foreach ($array_valor as $id):
           $array= explode("-", $id);
            $idservicio=$array[0];
            $precioInstalacion=$array[1];
           
           // print_r($precioInstalacion); die();
            
                $entity = new InfoDetalleSolicitud();
                $entity->setMotivoId($motivoId);
                $entityServicio= $em->getRepository('schemaBundle:InfoServicio')->find($idservicio);
                $entity->setServicioId($entityServicio);
                $entityTipoSolicitud= $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
                $entity->setTipoSolicitudId($entityTipoSolicitud);
              
                 
                 
                $entity->setPrecioDescuento($precioInstalacion);
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
    * @Secure(roles="ROLE_62-161")
    */     
    public function getServiciosParaSolicitudDesc_ajaxAction($id) {
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
       // $tipo=$request->query->get('tipo');
       
        $idEmpresa = $request->getSession()->get('idEmpresa');

        $em = $this->get('doctrine')->getManager('telconet');
        //si es solicitud de tipo servicio
        
        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPoraSolicitadInstalacion($idEmpresa,$id,$limit,$page,$start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        foreach ($datos as $dato):
            //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
            $detalleSolicitud=$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findSolicDescuentoPorServicio($dato['id'],$this->tipoSolicitud,'Pendiente');
            if ($detalleSolicitud)
                $yaFueSolicitada='S';
            else
                $yaFueSolicitada='N';
            
            $idProducto='';
            $descripcionProducto='';
            if ($dato['productoId']){
                $idProducto=$dato['productoId'];
                $objAdmiProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($dato['productoId']);
                $descripcionProducto=$objAdmiProducto->getDescripcionProducto();
                $tipo='producto';
            }elseif($dato['planId'])
            {
                $tipo='plan';
                $objInfoPlanCab=$em->getRepository('schemaBundle:InfoPlanCab')->find($dato['planId']);
                //el id 
                $idProducto=$objInfoPlanCab->getId();
                $descripcionProducto=$objInfoPlanCab->getDescripcionPlan();                
            }
            $objInfoPunto=$em->getRepository('schemaBundle:InfoPunto')->find($dato['puntoId']);
            $arreglo[] = array(
                'idServicio' => $dato['id'],
                'tipo'=>$tipo,
                'idPunto' => $objInfoPunto->getId(),
                'descripcionPunto' => $objInfoPunto->getDescripcionPunto(),
                'idProducto' => $idProducto,
                'descripcionProducto' => $descripcionProducto,
                'cantidad' => $dato['cantidad'],
                'instalacion'=>$dato['instalacion'],
                'fechaCreacion' => strval(date_format($dato['feCreacion'], "d/m/Y G:i")),
                'precioVenta' => $dato['precioVenta'],
                'estado' => $dato['estado'],
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


    public function aprobarDescuentoAction()
    {
     return $this->render('comercialBundle:solicitudinstalacion:aprobarDescuento.html.twig', array());
    }

    /**
    * Documentación para funcion 'gridAprobarDescuentoAction'.
    * funcion que envia los datos para el listado de solicitudes de descuento unico
    * @author <amontero@telconet.ec>
    * @since 12/12/2014
    * @return objeto - response
    
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.1 13-12-2017 Se adicciona los parametros nombre, apellido, razon social, usuario creacion,
    *                         y login, el método findSolicDescuentoPorCriterios recibirá por parametro un array
    *
    * Se extrae del array $datos por medio del id del servicio, el precio instalación y el asesor del servicio.
    * y en caso que el $arreglo este vacio se retorna ese valor.
    * @author Douglas Natha <dnatha@telconet.ec>
    * @version 1.2 20-11-2019
    * @since 1.1 
    *
    * @author Kevin Baque <kbaque@telconet.ec>
    * @version 1.3 03-01-2020 - Se envía un arreglo vacío en caso de que no existan solicitudes pendientes por aprobar,
    *                           para visualizar mensaje al usuario en el grid.
    *
    * @author : Kevin Baque <kbaque@telconet.ec>
    * @version 1.4 16-06-2021 - Se realiza cambio para que la consulta de solicitudes se realice a través de la persona en sesión
    *                           en caso de ser asistente solo tendrá acceso a las solicitudes de los vendedores asignados al asistente
    *                           en caso de ser vendedor solo tendrá acceso a sus solicitudes
    *                           en caso de ser subgerente solo tendrá acceso a solicitudes de los vendedores que reportan al subgerente
    *                           en caso de ser gerente u otro cargo no aplican los cambios
    *                           Se agrega la capacidad1, capacidad2 y una variable booleana la cual mostrará dichas capacidades en caso
    *                           que el producto sea Internet Dedicado o L3MPLS, se valida que el vicepresidente comercial apruebe sus
    *                           sus solicitudes hasta su rango máximo permitido y se valida si el vendedor del servicio es un vendedor
    *                           kam.
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.5 17-08-2022 - Se agregan nuevos parámetros de búsqueda.
    */     
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarDescuentoAction()
    {
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $intIdEmpresa            = $objSession->get('idEmpresa');
        $intIdCanton             = $objSession->get('intIdCanton') ? $objSession->get('intIdCanton') : "";
        $strFechaDesde           = explode('T',$objRequest->get("fechaDesde"));
        $strFechaHasta           = explode('T',$objRequest->get("fechaHasta"));
        $strNombre               = $objRequest->get("nombre");
        $strApellido             = $objRequest->get("apellido");
        $strRazonSocial          = $objRequest->get("razonSocial");
        $strUsuarioCreacion      = $objRequest->get("usuarioCreacion");
        $strLogin                = $objRequest->get("login");
        $strIdentificacion       = $objRequest->get("identificacion");
        $boolVerTodo             = $objRequest->get("boolVerTodo") ? $objRequest->get("boolVerTodo"): "NO";
        $intLimit                = $objRequest->get("limit") ? $objRequest->get("limit"):self::VALOR_LIMITE_BUSQUEDA;
        $intStart                = $objRequest->get("start") ? $objRequest->get("start"):self::VALOR_INICIAL_BUSQUEDA;
        $strDraw                 = $objRequest->get("draw")  ? $objRequest->get("draw"):"1";
        $strEstadoCargo          = $objRequest->get("strEstadoFiltro");
        $strIpCreacion           = $objRequest->getClientIp();
        $em                      = $this->get('doctrine')->getManager('telconet');
        $emGeneral               = $this->getDoctrine()->getManager('telconet_general');
        $serviceUtilidades       = $this->get('administracion.Utilidades');
        $arrayParametros         = array();
        $intIdEmpRol             = $objSession->get('idPersonaEmpresaRol');
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $strCodEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa'):"";
        $strUsrCreacion          = $objSession->get('user') ? $objSession->get('user') : '';
        $serviceUtil             = $this->get('schema.Util');
        $arrayCargoPersona       = "Otros";
        $strCargosAdicionales    = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
        $strMensajeCargo         = '';
        $arrayLoginVendedoresKam = array();
        $arrayRolesNoIncluidos   = array();
        $arrayTipoPersonal       = ["Otros","VENDEDOR","ASISTENTE"];
        $strRegionSesion         = "";
        $floatDescPorAprobarIni  = 0;
        $floatDescPorAprobarFin  = 0;
        $intTotal                = 0;
        $strTipoPersonal         = "Otros";
        $floatTotalCobrado       = 0;
        $floatTotalInstalacion   = 0;
        $floatTotalDescuento     = 0;
        try
        {
            /**
             * BLOQUE QUE VALIDA LA CARACTERISTICA 'CARGO_GRUPO_ROLES_PERSONAL','ASISTENTE_POR_CARGO' ASOCIADA A EL USUARIO LOGONEADO 
             */
            if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
            {
                $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                          ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                            'modulo'          => self::COMERCIAL,
                                                            'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                            'estado'          => 'Activo'));
                if(!is_object($objCargosCab) || empty($objCargosCab))
                {
                    throw new \Exception('No se encontraron datos con los parámetros enviados.');
                }
                $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                           'valor4'      => 'ES_JEFE',
                                                           'valor7'      => 'SI',
                                                           'estado'      => 'Activo'));
                foreach($arrayCargosDet as $objItem)
                {
                    $arrayCargos = $arrayCargos.''.ucwords(strtolower(str_replace("_"," ",$objItem->getValor3()))).'|';
                }
                $arrayCargoPersona = $em->getRepository('schemaBundle:InfoPersona')
                                        ->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
                $strTipoPersonal   = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona)) ? $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';

                if($strTipoPersonal == '' || is_null($strTipoPersonal))
                {
                    $strMensajeCargo = 'El usuario no tiene un cargo definido, por favor consultar con sistemas.';
                }

                if(!empty($strTipoPersonal) && $strTipoPersonal!='Otros')
                {
                    $objCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->findOneBy(array('parametroId' => $objCargosCab->getId(),
                                                                'valor3'      => $strTipoPersonal,
                                                                'valor4'      => 'ES_JEFE',
                                                                'estado'      => 'Activo'));
                    $strEstadoCargoAprobar   = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor6():'';
                    $floatDescPorAprobarIni  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor1():'';
                    $floatDescPorAprobarFin  = (!empty($objCargosDet) && is_object($objCargosDet)) ? $objCargosDet->getValor2():'';
                    $arrayParametros['strTipoPersonal']       = $strTipoPersonal;
                    $arrayParametros['intIdPersonEmpresaRol'] = $intIdEmpRol;
                    $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                }
                else
                {
                    $objCargosCabAux = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                 ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                   'modulo'          => self::COMERCIAL,
                                                                   'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                   'estado'          => 'Activo'));
                    if(!empty($objCargosCabAux) && is_object($objCargosCabAux))
                    {
                        $objCargosDetAux = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                     ->findOneBy(array('parametroId' => $objCargosCabAux->getId(),
                                                                       'valor4'        => 'ES_JEFE',
                                                                       'estado'        => 'Activo',
                                                                       'observacion'   => $strUsrCreacion));
                        if(!empty($objCargosDetAux) && is_object($objCargosDetAux))
                        {
                            $strTipoPersonalAux    = $objCargosDetAux->getValor3();
                            $strEstadoCargoAprobar = $objCargosDetAux->getValor6();
                            if($strTipoPersonalAux == "GERENTE_VENTAS")
                            {
                                $arrayParametros['strTipoPersonal']       = $strTipoPersonalAux;
                                $arrayParametros['intIdPersonEmpresaRol'] = $intIdEmpRol;
                                $arrayParametros['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                            }
                        }
                    }
                }
                /**
                 * BLOQUE QUE OBTIENE EL LISTADO DE VENDEDORES KAMS
                 */
                $arrayParametrosKam                          = array();
                $arrayResultadoVendedoresKam                 = array();
                $arrayParametrosKam['strPrefijoEmpresa']     = $strPrefijoEmpresa;
                $arrayParametrosKam['strCodEmpresa']         = $strCodEmpresa;
                $arrayParametrosKam['strEstadoActivo']       = 'Activo';
                $arrayParametrosKam['strDescCaracteristica'] = self::CARGO_GRUPO_ROLES_PERSONAL;
                $arrayParametrosKam['strNombreParametro']    = self::GRUPO_ROLES_PERSONAL;
                $arrayParametrosKam['strDescCargo']          = self::GERENTE_VENTAS;
                $arrayParametrosKam['strDescRolNoPermitido'] = self::ROLES_NO_PERMITIDOS;
                $arrayResultadoVendedoresKam                 = $em->getRepository('schemaBundle:InfoPersona')
                                                                  ->getVendedoresKams($arrayParametrosKam);
                if(isset($arrayResultadoVendedoresKam['error']) && !empty($arrayResultadoVendedoresKam['error']))
                {
                    throw new \Exception($arrayResultadoVendedoresKam['error']);
                }
                if(!empty($arrayResultadoVendedoresKam['vendedoresKam']) && is_array($arrayResultadoVendedoresKam['vendedoresKam']))
                {
                    foreach($arrayResultadoVendedoresKam['vendedoresKam'] as $arrayItem)
                    {
                        $arrayLoginVendedoresKam[] = $arrayItem['LOGIN'];
                    }
                }
                /**
                 * BLOQUE QUE OBTIENE LA REGIÓN EN SESIÓN Y LOS PARÁMETROS NECESARIOS PARA FILTRAR POR REGIÓN
                 */
                if(empty($intIdCanton))
                {
                    throw new \Exception('Error al obtener el cantón del usuario en sesión.');
                }
                $objCanton = $em->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
                if(empty($objCanton) || !is_object($objCanton))
                {
                    throw new \Exception('Error al obtener el cantón del usuario en sesión.');
                }
                $strRegionSesion       = $objCanton->getRegion();
                $arrayParametrosRoles  = array( 'strCodEmpresa'     => $intIdEmpresa,
                                                'strValorRetornar'  => 'descripcion',
                                                'strNombreProceso'  => 'JEFES',
                                                'strNombreModulo'   => 'COMERCIAL',
                                                'strNombreCabecera' => 'ROLES_NO_PERMITIDOS',
                                                'strUsrCreacion'    => $strUsrCreacion,
                                                'strIpCreacion'     => $strIpCreacion );
                $arrayResultadosRolesNoIncluidos = $serviceUtilidades->getDetallesParametrizables($arrayParametrosRoles);
                if( isset($arrayResultadosRolesNoIncluidos['resultado']) && !empty($arrayResultadosRolesNoIncluidos['resultado']) )
                {
                    foreach( $arrayResultadosRolesNoIncluidos['resultado'] as $strRolNoIncluido )
                    {
                        $arrayRolesNoIncluidos[] = $strRolNoIncluido;
                    }
                }
            }
            $arrayParametros['strEstado']               = (!empty($strEstadoCargo)) ? str_replace(" ","-",$strEstadoCargo):'PENDIENTE';
            $arrayParametros['strTipoSolicitud']        = $this->tipoSolicitud;
            $arrayParametros['intIdEmpresa']            = $intIdEmpresa;
            $arrayParametros['strFechaDesde']           = $strFechaDesde;
            $arrayParametros['strFechaHasta']           = $strFechaHasta;
            $arrayParametros['strNombre']               = $strNombre;
            $arrayParametros['strApellido']             = $strApellido;
            $arrayParametros['strRazonSocial']          = $strRazonSocial;
            $arrayParametros['strIdentificacion']       = $strIdentificacion;
            $arrayParametros['strUsuarioCreacion']      = $strUsuarioCreacion;
            $arrayParametros['strLogin']                = $strLogin;
            $arrayParametros['intStart']                = $intStart;
            $arrayParametros['intLimit']                = $intLimit;
            $arrayParametros['arrayLoginVendedoresKam'] = $arrayLoginVendedoresKam;
            $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
            $arrayParametros['strRegion']               = $strRegionSesion;
            $arrayParametros['arrayRolNoPermitido']     = (!empty($arrayRolesNoIncluidos) && is_array($arrayRolesNoIncluidos))?
                                                          $arrayRolesNoIncluidos:"";
            $arrayResultado                             = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                             ->findSolicDescuentoPorCriterios($arrayParametros);
            $arrayParametros['boolTotalInstalacion']    = "SI";
            $arrayResultadoTotalInstalacion             = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                             ->findSolicDescuentoPorCriterios($arrayParametros);
                                                             
            if(!empty($arrayResultadoTotalInstalacion) && is_array($arrayResultadoTotalInstalacion))
            {
                $arrayTotalInstalacion = $arrayResultadoTotalInstalacion["registros"][0];
                $floatTotalInstalacion = (isset($arrayTotalInstalacion["PRECIO_INSTALACION"]) && !empty($arrayTotalInstalacion["PRECIO_INSTALACION"]))
                                         ? floatval($arrayTotalInstalacion["PRECIO_INSTALACION"]):0;
                $floatTotalDescuento   = (isset($arrayTotalInstalacion["PRECIO_DESCUENTO"]) && !empty($arrayTotalInstalacion["PRECIO_DESCUENTO"]))
                                         ? floatval($arrayTotalInstalacion["PRECIO_DESCUENTO"]):0;
                $floatTotalCobrado     = $floatTotalInstalacion-$floatTotalDescuento;
            }
            if(!empty($arrayResultado) && is_array($arrayResultado))
            {
                $intTotal = $arrayResultado["total"] ? $arrayResultado["total"]:0;
                foreach($arrayResultado['registros'] as $objItem)
                {
                    $strNombreMotivo             = '';
                    $linkVer                     = '#';
                    $strNombresCompletos         = '';
                    $objIdServicio               = $objItem->getServicioId();
                    $objPunto                    = $objIdServicio->getPuntoId();
                    if(!empty($objIdServicio) && is_object($objIdServicio))
                    {
                        $strLoginVendedor    = $objIdServicio->getUsrVendedor();
                        $objPersonaVendedor  = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$strLoginVendedor));
                        $strNombresCompletos = (!empty($objPersonaVendedor) && is_object($objPersonaVendedor)) 
                                               ? $objPersonaVendedor->getNombres().' '.$objPersonaVendedor->getApellidos(): $strLoginVendedor;
                    }
                    else
                    {
                        throw new \Exception('No existe servicio asociado con la solicitud.');
                    }
                    $strTipoNegocioPto  = $objPunto->getTipoNegocioId()->getCodigoTipoNegocio();
                    $floatPrecioInstalacion   = $objIdServicio->getPrecioInstalacion() ? $objIdServicio->getPrecioInstalacion():0;
                    $floatPrecioDescuento     = $objItem->getPrecioDescuento() ? $objItem->getPrecioDescuento() : 0;
                    $floatPorcentajeDescuento = $objItem->getPorcentajeDescuento() ? $objItem->getPorcentajeDescuento() : 0;
                    $floatValorFinal = $floatPrecioInstalacion - $floatPrecioDescuento;
                    if((empty($floatPorcentajeDescuento)&&$floatPorcentajeDescuento==0)&&(!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                    {
                        $floatPorcentajeDescuento = ($floatPrecioDescuento * 100)/$floatPrecioInstalacion;
                    }
                    $strCargoAsignado = "";
                    if(is_array($arrayCargosDet) && $strPrefijoEmpresa == "TN")
                    {
                        //Se obtiene los datos del vendedor para saber si es de la región R1 o R2 y con ello se mostrará el cargo asignado
                        $arrayDatosVendedor = $em->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                 ->getInfoDatosPersona(array('strRol'                     => 'Empleado',
                                                                             'strPrefijo'                 => $strPrefijoEmpresa,
                                                                             'strEstadoPersona'           => array('Activo',
                                                                                                                   'Pendiente',
                                                                                                                   'Modificado'),
                                                                             'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                             'strLogin'                   => $strLoginVendedor));
                        if(empty($arrayDatosVendedor) || !is_array($arrayDatosVendedor) ||
                            (isset($arrayDatosVendedor['status']) && $arrayDatosVendedor['status'] === 'fail') ||
                            ($arrayDatosVendedor['status'] === 'ok' && empty($arrayDatosVendedor['result'])))
                        {
                            throw new \Exception('Error al obtener los datos del vendedor asignado, por favor comunicar a Sistemas.');
                        }
                        foreach($arrayCargosDet as $objCargosItem)
                        {
                            if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                               floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                            {
                                $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                if((!empty($strCargoAsignado) && $strCargoAsignado == "Gerente Ventas") || 
                                   (!empty($strCargoAsignado) && $strCargoAsignado == "Subgerente" && 
                                    in_array($strLoginVendedor,$arrayLoginVendedoresKam)))
                                {
                                    $strCargoAsignado = (!empty($arrayDatosVendedor['result'][0]['region'])) ? 
                                                        "Gerente Comercial ".$arrayDatosVendedor['result'][0]['region']:
                                                        "Gerente Comercial";
                                }
                                $strCargoAsignado = (!empty($strCargoAsignado) && $strCargoAsignado == "Subgerente" && $strTipoNegocioPto == "ISP") ?
                                                    "Aprobador ISP" : $strCargoAsignado;

                            }
                        }
                    }
                    if($objItem->getMotivoId()!= null && $objItem->getMotivoId() > 0)
                    {
                        $entityMotivo    = $em->getRepository('schemaBundle:AdmiMotivo')->find($objItem->getMotivoId());
                        $strNombreMotivo = (is_object($entityMotivo) && !empty($entityMotivo)) ? $entityMotivo->getNombreMotivo() : '';
                    }
                    $strProductoPlan ='';
                    $strCapacidadUno  = '';
                    $strCapacidadDos  = '';
                    if($objIdServicio->getProductoId())
                    {
                        $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD1',
                                                                      "estado"                    => "Activo"));

                        if( is_object($objAdmiCaracteristica) && !empty($objAdmiCaracteristica) )
                        {
                            $objProdCarac1 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objIdServicio->getProductoId(),
                                                                  "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                  "estado"           => "Activo"));
                            if( is_object($objProdCarac1) && !empty($objProdCarac1) )
                            {
                                $objServProdCarac1 = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                         ->findOneBy(array("servicioId"                => $objIdServicio->getId(),
                                                                           "productoCaracterisiticaId" => $objProdCarac1->getId(),
                                                                           "estado"                    => "Activo"));
                                if( is_object($objServProdCarac1) && !empty($objServProdCarac1) )
                                {
                                    $strCapacidadUno = $objServProdCarac1->getValor();
                                }
                            }
                        }

                        $objAdmiCaracteristica = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneBy(array("descripcionCaracteristica" => 'CAPACIDAD2',
                                                                      "estado"                    => "Activo"));

                        if( is_object($objAdmiCaracteristica) && !empty($objAdmiCaracteristica) )
                        {
                            $objProdCarac2 = $em->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy(array("productoId"       => $objIdServicio->getProductoId(),
                                                                  "caracteristicaId" => $objAdmiCaracteristica->getId(),
                                                                  "estado"           => "Activo"));

                            if( is_object($objProdCarac2) && !empty($objProdCarac2) )
                            {
                                $objServProdCarac2 = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findOneBy(array("servicioId"                => $objIdServicio->getId(),
                                                                          "productoCaracterisiticaId" => $objProdCarac2->getId(),
                                                                          "estado"                    => "Activo"));
                                if( is_object($objServProdCarac2) && !empty($objServProdCarac2) )
                                {
                                    $strCapacidadDos = $objServProdCarac2->getValor();
                                }
                            }
                        }

                        $entityProducto=$em->getRepository('schemaBundle:AdmiProducto')->find($objIdServicio->getProductoId()->getId());
                        $strProductoPlan = (is_object($entityProducto) && !empty($entityProducto)) ? $entityProducto->getDescripcionProducto():'';
                    }
                    elseif($objIdServicio->getPlanId())
                    {
                        $entityProducto=$em->getRepository('schemaBundle:InfoPlanCab')->find($objIdServicio->getPlanId()->getId());
                        $strProductoPlan = (is_object($entityProducto) && !empty($entityProducto)) ? $entityProducto->getNombrePlan():'';
                    }

                    $boolVelocidad = true;
                    if($strProductoPlan == 'Internet Dedicado' || $strProductoPlan == 'L3MPLS')
                    {
                        $boolVelocidad=false;
                    }

                    if($objPunto->getPersonaEmpresaRolId() && $objPunto->getPersonaEmpresaRolId()->getPersonaId())
                    {
                        if ($objPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                        {
                            $strCliente = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                            $strEstado  = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getEstado();
                        }
                        else
                        {    
                            $strCliente = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getNombres()." ".
                                          $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                            $strEstado  = $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getEstado();
                        }
                    }
                    if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN" && !in_array($strTipoPersonal,$arrayTipoPersonal)
                       && $strTipoPersonalAux != "Otros" && $boolVerTodo == "NO")
                    {
                        //Se valida el porcentaje de descuento de la solicitud y cargo, para poder ser presentada la solicitud en el grid.
                        if((floatval($floatPorcentajeDescuento) >= floatval($floatDescPorAprobarIni) && 
                            floatval($floatPorcentajeDescuento) <=  floatval($floatDescPorAprobarFin)) ||
                           (($strTipoPersonal == "GERENTE_VENTAS" || $strTipoPersonalAux == "GERENTE_VENTAS") &&
                            (in_array($strLoginVendedor,$arrayLoginVendedoresKam) || $strUsrCreacion == $strLoginVendedor) &&
                            ($floatPorcentajeDescuento <=  floatval($floatDescPorAprobarFin))))
                        {
                            $arraySolicitudes[]= array('id'             => $objItem->getId(),
                                                       'servicio'       => $strProductoPlan,
                                                       'cliente'        => $strCliente,
                                                       'estadoClt'      => $strEstado,
                                                       'asesor'         => $strNombresCompletos,
                                                       'login'          => $objPunto->getLogin(),
                                                       'motivo'         => $strNombreMotivo,
                                                       'vInstalacion'   => $strValorInstalacion,
                                                       'descuento'      => $descuento,
                                                       'vInstalacion'   => '$'.$floatPrecioInstalacion,
                                                       'descuento'      => '$'.$floatPrecioDescuento,
                                                       'vFinal'         => '$'.$floatValorFinal,
                                                       'observacion'    => $objItem->getObservacion(),
                                                       'feCreacion'     => strval(date_format($objItem->getFeCreacion(),"d/m/Y G:i")),
                                                       'usrCreacion'    => $objItem->getUsrCreacion(),
                                                       'arrayCargos'    => $arrayCargos ? $arrayCargos : '',
                                                       'strCargoActual' => $strCargoActual,
                                                       'intCantCargos'  => $intCantCargos+1,
                                                       'estadoSolicitud'=> $objItem->getEstado(),
                                                       'boolAprobar'    => $boolAprobar,
                                                       'linkVer'        => $linkVer,
                                                       'boolVelocidad'  => $boolVelocidad,
                                                       'strVelocidadUp' => $strCapacidadUno,
                                                       'strVelocidadDown'=> $strCapacidadDos,
                                                       'floatPorcentaje'  => round(floatval($floatPorcentajeDescuento),2).'%',
                                                       'strCargoAsignado' => $strCargoAsignado);
                        }
                    }
                    else
                    {
                        $arraySolicitudes[]= array('id'             => $objItem->getId(),
                                                    'servicio'       => $strProductoPlan,
                                                    'cliente'        => $strCliente,
                                                    'estadoClt'      => $strEstado,
                                                    'asesor'         => $strNombresCompletos,
                                                    'login'          => $objPunto->getLogin(),
                                                    'motivo'         => $strNombreMotivo,
                                                    'vInstalacion'   => $strValorInstalacion,
                                                    'descuento'      => $descuento,
                                                    'vInstalacion'   => '$'.$floatPrecioInstalacion,
                                                    'descuento'      => '$'.$floatPrecioDescuento,
                                                    'vFinal'         => '$'.$floatValorFinal,
                                                    'observacion'    => $objItem->getObservacion(),
                                                    'feCreacion'     => strval(date_format($objItem->getFeCreacion(),"d/m/Y G:i")),
                                                    'usrCreacion'    => $objItem->getUsrCreacion(),
                                                    'arrayCargos'    => $arrayCargos ? $arrayCargos : '',
                                                    'strCargoActual' => $strCargoActual,
                                                    'intCantCargos'  => $intCantCargos+1,
                                                    'estadoSolicitud'=> $objItem->getEstado(),
                                                    'boolAprobar'    => $boolAprobar,
                                                    'linkVer'        => $linkVer,
                                                    'boolVelocidad'  => $boolVelocidad,
                                                    'strVelocidadUp' => $strCapacidadUno,
                                                    'strVelocidadDown'=> $strCapacidadDos,
                                                    'floatPorcentaje'  => round(floatval($floatPorcentajeDescuento),2).'%',
                                                    'strCargoAsignado' => $strCargoAsignado);
                    }
                }
            }
            else
            {
                throw new \Exception('No existen solicitudes con las descripciones enviadas por parámetros.');
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                            'SolicitudDescuentoController->gridAprobarDescuentoAction', 
                                            $ex->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
        }
        if(!empty($arraySolicitudes) && is_array($arraySolicitudes))
        {
            if( empty($strMensajeCargo) )
            {
                $objResponse = new Response(json_encode(array("total"                 => $intTotal,
                                                              "solicitudes"           => $arraySolicitudes,
                                                              "draw"                  => $strDraw,
                                                              "recordsTotal"          => $intTotal,
                                                              "recordsFiltered"       => $intTotal,
                                                              "floatTotalCobrado"     => number_format($floatTotalCobrado, 2, '.', ','),
                                                              "floatTotalDescuento"   => number_format($floatTotalDescuento, 2, '.', ','),
                                                              "floatTotalInstalacion" => number_format($floatTotalInstalacion, 2, '.', ','))));
            }else
            {
                $objResponse = new Response(json_encode(array("total"                 => $intTotal,
                                                              "solicitudes"           => $arraySolicitudes,
                                                              "mensajeCargo"          => $strMensajeCargo,
                                                              "draw"                  => $strDraw,
                                                              "recordsTotal"          => $intTotal,
                                                              "recordsFiltered"       => $intTotal,
                                                              "floatTotalCobrado"     => number_format($floatTotalCobrado, 2, '.', ','),
                                                              "floatTotalDescuento"   => number_format($floatTotalDescuento, 2, '.', ','),
                                                              "floatTotalInstalacion" => number_format($floatTotalInstalacion, 2, '.', ','))));
            }
        }
        else
        {
            if( empty($strMensajeCargo) )
            {
                $objResponse = new Response(json_encode(array("total"                 => $intTotal,
                                                              "solicitudes"           => [],
                                                              "draw"                  => $strDraw,
                                                              "recordsTotal"          => $intTotal,
                                                              "recordsFiltered"       => $intTotal,
                                                              "floatTotalCobrado"     => number_format($floatTotalCobrado, 2, '.', ','),
                                                              "floatTotalDescuento"   => number_format($floatTotalDescuento, 2, '.', ','),
                                                              "floatTotalInstalacion" => number_format($floatTotalInstalacion, 2, '.', ','))));
            }else
            {
                $objResponse = new Response(json_encode(array("total"                 => $intTotal,
                                                              "solicitudes"           => [],
                                                              "mensajeCargo"          => $strMensajeCargo,
                                                              "draw"                  => $strDraw,
                                                              "recordsTotal"          => $intTotal,
                                                              "recordsFiltered"       => $intTotal,
                                                              "floatTotalCobrado"     => number_format($floatTotalCobrado, 2, '.', ','),
                                                              "floatTotalDescuento"   => number_format($floatTotalDescuento, 2, '.', ','),
                                                              "floatTotalInstalacion" => number_format($floatTotalInstalacion, 2, '.', ','))));
            }
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }


    /**
     * @Secure(roles="ROLE_163-1")
     * 
     * Documentación para el método 'aprobarDescuentoAjaxAction'
     * 
     * Función que aprueba todas las autorizaciones de instalaciones
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 04-07-2016 - Se cambia para TN el valor de instalacion del servicio dependiendo del valor de descuento de la solicitud
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 20-09-2016 - Se envía correo cuando se aprueba un descuento por instalación utilizando la respectiva plantilla 
     *                           y adjuntando un archivo con la información de la solicitudes
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 06-01-2020 - Se agrega lógica para guardar historial del servicio.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 17-06-2021 - Se agrega lógica para guardar el estado correspondiente según el cargo de la persona en sesión.
     *                           Se agrega validación que verifica el cargo de la persona auxiliar que aprobará la solicitud.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.5 17-09-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     *
     */
    public function aprobarDescuentoAjaxAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strUsrCreacion     = $objSession->get('user');
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strEmpleado        = $objSession->get('empleado');
        $strIpClient        = $objRequest->getClientIp();

        $objRespuesta       = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/plain');
        $objRespuesta->setContent("error del Form");

        $serviceUtil        = $this->get('schema.Util');

        //Obtiene parametros enviados desde el ajax
        $strParametro       = $objRequest->get('param');
        $arrayValor         = explode("|", $strParametro);
        $arrayData          = array();
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $strPrefijoEmpresa        = $objSession->get('prefijoEmpresa');
        $serviceEnvioPlantilla    = $this->get('soporte.EnvioPlantilla');
        $emComercial->getConnection()->beginTransaction();
        try
        {
            foreach($arrayValor as $intId)
            {
                $objInfoDetalleSolicitud    = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intId);
                $strCliente                 = "";
                $strProductoPlan            = "";
                $strDescuento               = "";
                $strLogin                   = "";
                if(!$objInfoDetalleSolicitud)
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $objInfoServicio = $objInfoDetalleSolicitud->getServicioId();
                if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
                {
                    $arrayDestinatarios     = array();
                    $objPunto               = $objInfoServicio->getPuntoId();
                    $strVendedor            = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                    $objPersona             = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                    $strCliente             = "";
                    $strIdentificacion      = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                    $strCliente             = (is_object($objPersona) && $objPersona->getRazonSocial()) ?
                                              $objPersona->getRazonSocial() : $objPersona->getNombres() . " " .$objPersona->getApellidos();
                    $floatPrecioInstalacion = $objInfoServicio->getPrecioInstalacion() ? $objInfoServicio->getPrecioInstalacion():0;
                    $floatPrecioDescuento   = $objInfoDetalleSolicitud->getPrecioDescuento() ? $objInfoDetalleSolicitud->getPrecioDescuento() : 0;
                    $floatPorcDesc          = $objInfoDetalleSolicitud->getPorcentajeDescuento() ? 
                                              $objInfoDetalleSolicitud->getPorcentajeDescuento() : 0;
                    if((empty($floatPorcDesc)&&$floatPorcDesc==0)&&(!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                    {
                        $floatPorcDesc = ($floatPrecioDescuento * 100)/$floatPrecioInstalacion;
                    }
                    $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                              ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                'modulo'          => self::COMERCIAL,
                                                                'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                'estado'          => 'Activo'));
                    if(!is_object($objCargosCab) || empty($objCargosCab))
                    {
                        throw new \Exception('No se encontraron datos con los parámetros enviados.');
                    }
                    $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                               'valor4'      => 'ES_JEFE',
                                                               'valor7'      => 'SI',
                                                               'estado'      => 'Activo'));
                    $strCargoAsignado = "";
                    if(is_array($arrayCargosDet))
                    {
                        foreach($arrayCargosDet as $objCargosItem)
                        {
                            if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                                floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                            {
                                $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                            }
                        }
                    }
                    //Correo del vendedor.
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                             "Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    //Correo del subgerente
                    $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                           ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                    if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                    {
                        $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                        $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                             "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                    }
                    //Correo de la persona quien crea la solicitud.
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($objInfoDetalleSolicitud->getUsrCreacion(),
                                                                                             "Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $strCuerpoCorreo      = "El presente correo es para indicarle que se aprobó una solicitud en TelcoS+ con los siguientes datos:";
                    $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                  "strIdentificacionCliente" => $strIdentificacion,
                                                  "strObservacion"           => $objInfoDetalleSolicitud->getObservacion(),
                                                  "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                  "strCargoAsignado"         => $strCargoAsignado);
                    $serviceEnvioPlantilla->generarEnvioPlantilla("APROBACIÓN DE SOLICITUD DE INSTALACIÓN",
                                                                  array_unique($arrayDestinatarios),
                                                                  "NOTIFICACION",
                                                                  $arrayParametrosMail,
                                                                  $strPrefijoEmpresa,
                                                                  "",
                                                                  "",
                                                                  null,
                                                                  true,
                                                                  "notificaciones_telcos@telconet.ec");
                }
                $strNombreSolicitud = $objInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud();
                $objInfoDetalleSolicitud->setEstado('Aprobado');
                $emComercial->persist($objInfoDetalleSolicitud);
                $emComercial->flush();


                //Grabamos en la tabla de historial de la solicitud
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setEstado('Aprobado');
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                $emComercial->persist($objInfoDetalleSolHist);
                $emComercial->flush();
                
                /*Se obtiene la información necesaria para el archivo que se enviará como adjunto del correo*/
                if($objInfoServicio)
                {
                    $objProducto    = $objInfoServicio->getProductoId();
                    $objPlan        = $objInfoServicio->getPlanId();
                    $objPunto       = $objInfoServicio->getPuntoId();
                    if($objProducto)
                    {
                        $strProductoPlan    = $objProducto->getDescripcionProducto();
                    }
                    elseif($objPlan)
                    {
                        $strProductoPlan    = $objPlan->getNombrePlan();
                    }

                    if($objInfoDetalleSolicitud->getPrecioDescuento())
                    {
                        $strDescuento       = $objInfoDetalleSolicitud->getPrecioDescuento();
                    }
                    elseif($objInfoDetalleSolicitud->getPorcentajeDescuento())
                    {
                        $strDescuento       = $objInfoDetalleSolicitud->getPorcentajeDescuento() . '%';
                    }
                    if($objPunto)
                    {
                        $objPersonaEmpresaRol= $objPunto->getPersonaEmpresaRolId();
                        if($objPersonaEmpresaRol)
                        {
                            $objPersona     = $objPersonaEmpresaRol->getPersonaId();
                            if($objPersona)
                            {
                                $strCliente = sprintf('%s', $objPersona);
                            }
                        }
                        $strLogin           = $objPunto->getLogin();
                    }

                    //SE CAMBIA PRECIO AL SERVICIO CUANDO ES TN
                    if($strPrefijoEmpresa == "TN")
                    {
                        $floatNuevoValorInstalacion     = 0;
                        $floatValorInstalacionServicio  = $objInfoServicio->getPrecioInstalacion() ? $objInfoServicio->getPrecioInstalacion() : 0;
                        $floatValorADescontar = $objInfoDetalleSolicitud->getPrecioDescuento() ? $objInfoDetalleSolicitud->getPrecioDescuento() : 0;

                        if(floatval($floatValorInstalacionServicio) > floatval($floatValorADescontar))
                        {
                            $floatNuevoValorInstalacion = floatval($floatValorInstalacionServicio) - floatval($floatValorADescontar);
                        }
                        else
                        {
                            $floatNuevoValorInstalacion = floatval($floatValorADescontar) - floatval($floatValorInstalacionServicio);
                        }


                        $objInfoServicio->setPrecioInstalacion($floatNuevoValorInstalacion);
                        $emComercial->persist($objInfoServicio);
                        $emComercial->flush();
                    }
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                    $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setIpCreacion($strIpClient);
                    $objInfoServicioHistorial->setServicioId($objInfoServicio);
                    $objInfoServicioHistorial->setObservacion('Se aprobó : '.$strNombreSolicitud);
                    $emComercial->persist($objInfoServicioHistorial);
                    $emComercial->flush();
                }
                else
                {
                    throw $this->createNotFoundException('No se encontro el servicio asociado a la solicitud buscada');
                }

                $strMotivo          = '';
                if($objInfoDetalleSolicitud->getMotivoId())
                {
                    $objMotivo      = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objInfoDetalleSolicitud->getMotivoId());
                    if($objMotivo)
                    {
                        $strMotivo = $objMotivo->getNombreMotivo();
                    }
                }
                $arrayData[] = array(
                                        "cliente"               => $strCliente,
                                        "login"                 => $strLogin,
                                        "servicio"              => $strProductoPlan,
                                        "motivo"                => $strMotivo,
                                        "descuento"             => $strDescuento,
                                        "observacionSolicitud"  => $objInfoDetalleSolicitud->getObservacion(),
                                        "fechaCreacion"         => strval(date_format($objInfoDetalleSolicitud->getFeCreacion(), "d/m/Y G:i")),
                                        "usuarioCreacion"       => $objInfoDetalleSolicitud->getUsrCreacion()
                );
            }
            
            /* Envío Correo Solicitudes de Instalación Aprobadas
             * Para los diferentes tipos de solicitudes que serán aprobadas por Gerencia, se utilizará la misma plantilla de aprobación,
             * con la diferencia de que dependiendo del tipo de solicitud que se desea aprobar, se adjuntará un PDF con la información de las 
             * distintas solicitudes que fueron aprobadas. Es por esta razón que se enviarán como parámetros los nombres de las cabeceras 
             * de las columnas de la tabla con el contenido de las solicitudes.
             * Además se envían los parámetros necesarios para el contenido del correo que se enviará utilizando plantillas.
             */
            $arrayNombresCabeceraAdjunto = array(   "Cliente",
                                                    "Login",
                                                    "Servicio",
                                                    "Motivo",
                                                    "Descuento",
                                                    "Observación Solicitud",
                                                    "Fecha Creación",
                                                    "Usuario Creación");

            $arrayParametrosMail = array(
                                            "idEmpresaSession"              => $strCodEmpresa,
                                            "prefijoEmpresaSession"         => $strPrefijoEmpresa,
                                            "codigoPlantilla"               => "APROB_AUTORIZAC",
                                            "usrCreacion"                   => $strUsrCreacion,
                                            "ipClient"                      => $strIpClient,
                                            "empleadoSession"               => $strEmpleado,
                                            "tituloAdjunto"                 => "APROBACIÓN DE SOLICITUDES DE DESCUENTO POR INSTALACIÓN",
                                            "tipoAutorizacion"              => "AUTORIZACIÓN DE DESCUENTO POR INSTALACIÓN",
                                            "tipoGestion"                   => "APROBACIÓN",
                                            "nombreTipoAutorizacionAdjunto" => "Aprobacion_Autorizacion_Descuento_Instalacion",
                                            "arrayNombresCabeceraAdjunto"   => $arrayNombresCabeceraAdjunto,
                                            "arrayDataAdjunto"              => $arrayData,
                                            "asunto"                        => "Gestion en Solicitudes de Descuento por Instalacion"
            );
            /* @var $serviceAutorizaciones \telconet\comercialBundle\Service\Autorizaciones */
            $serviceAutorizaciones = $this->get('comercial.Autorizaciones');
            $serviceAutorizaciones->envioMailAutorizaciones($arrayParametrosMail);

            $emComercial->getConnection()->commit();

            $objRespuesta->setContent("Se aprobaron las solicitudes con exito.");
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();
            $serviceUtil->insertError(  'Telcos+', 
                                        'SolicitudInstalacionController->aprobarDescuentoAjaxAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpClient
            );
            $objRespuesta->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }
        return $objRespuesta;
    }

    /**
     * 
     * 
     * Documentación para el método 'rechazarSolicitudDescuentoAjaxAction'
     * 
     * Función que rechaza todas las autorizaciones de instalaciones
     * 
     * @version 1.0 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 20-09-2016 - Se envía correo cuando se rechaza un descuento por instalación utilizando la respectiva plantilla 
     *                           y adjuntando un archivo con la información de la solicitudes
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.3 06-01-2020 - Se agrega lógica para guardar historial del servicio.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 17-09-2022 - Envío de notificación a la asistente, vendedor y subgerente.
     *
     */
    public function rechazarSolicitudDescuentoAjaxAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa');
        $strUsrCreacion         = $objSession->get('user');
        $strEmpleado            = $objSession->get('empleado');
        $strIpClient            = $objRequest->getClientIp();
        $objResponse            = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');
        $objResponse->setContent("error del Form");
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');

        $strParametro           = $objRequest->get('param');
        $arrayIdsSolicitudes    = explode("|", $strParametro);
        $intIdMotivo            = $objRequest->get('motivoId');
        $serviceUtil            = $this->get('schema.Util');
        $arrayData              = array();
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $emComercial->getConnection()->beginTransaction();
        try
        {
            foreach($arrayIdsSolicitudes as $intId)
            {
                $strCliente     = "";
                $strProductoPlan= "";
                $strDescuento   = "";
                $strLogin       = "";
                $objDetalleSol  = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intId);
                if(!$objDetalleSol)
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
                $strNombreSolicitud = $objDetalleSol->getTipoSolicitudId()->getDescripcionSolicitud();
                $objDetalleSol->setEstado('Rechazado');
                $objDetalleSol->setUsrRechazo($strUsrCreacion);
                $objDetalleSol->setFeRechazo(new \DateTime('now'));
                $emComercial->persist($objDetalleSol);
                $emComercial->flush();

                //Grabamos en la tabla de historial de la solicitud
                $objDetalleSolHistorial = new InfoDetalleSolHist();
                $objDetalleSolHistorial->setEstado('Rechazado');
                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
                $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorial->setIpCreacion($strIpClient);
                $objDetalleSolHistorial->setMotivoId($intIdMotivo);
                $emComercial->persist($objDetalleSolHistorial);
                $emComercial->flush();

                $objServicio        = $objDetalleSol->getServicioId();
                if($objServicio)
                {
                    $objProducto    = $objServicio->getProductoId();
                    $objPlan        = $objServicio->getPlanId();
                    $objPunto       = $objServicio->getPuntoId();
                    if($objProducto)
                    {
                        $strProductoPlan    = $objProducto->getDescripcionProducto();
                    }
                    elseif($objPlan)
                    {
                        $strProductoPlan    = $objPlan->getNombrePlan();
                    }

                    if($objDetalleSol->getPrecioDescuento())
                    {
                        $strDescuento       = $objDetalleSol->getPrecioDescuento();
                    }
                    elseif($objDetalleSol->getPorcentajeDescuento())
                    {
                        $strDescuento       = $objDetalleSol->getPorcentajeDescuento() . '%';
                    }

                    if($objPunto)
                    {
                        $objPersonaEmpresaRol= $objPunto->getPersonaEmpresaRolId();
                        if($objPersonaEmpresaRol)
                        {
                            $objPersona     = $objPersonaEmpresaRol->getPersonaId();
                            if($objPersona)
                            {
                                $strCliente = sprintf('%s', $objPersona);
                            }
                        }
                        $strLogin           = $objPunto->getLogin();
                    }
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setEstado($objServicio->getEstado());
                    $objInfoServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setIpCreacion($strIpClient);
                    $objInfoServicioHistorial->setServicioId($objServicio);
                    if(!empty($intIdMotivo))
                    {
                        $objMotivo = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                        $objInfoServicioHistorial->setMotivoId($objMotivo->getId());
                    }
                    $objInfoServicioHistorial->setObservacion('Se rechazó : '.$strNombreSolicitud);
                    $emComercial->persist($objInfoServicioHistorial);
                    $emComercial->flush();
                    if(!empty($strPrefijoEmpresa) && $strPrefijoEmpresa == "TN")
                    {
                        $arrayDestinatarios     = array();
                        $strVendedor            = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                        $objPersona             = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                        $strCliente             = "";
                        $strIdentificacion      = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                        $strCliente             = (is_object($objPersona) && $objPersona->getRazonSocial()) ?
                                                  $objPersona->getRazonSocial() : $objPersona->getNombres() . " " .$objPersona->getApellidos();
                        $floatPrecioInstalacion = $objServicio->getPrecioInstalacion() ? $objServicio->getPrecioInstalacion():0;
                        $floatPrecioDescuento   = $objDetalleSol->getPrecioDescuento() ? $objDetalleSol->getPrecioDescuento() : 0;
                        $floatPorcDesc          = $objDetalleSol->getPorcentajeDescuento() ? 
                                                  $objDetalleSol->getPorcentajeDescuento() : 0;
                        if((empty($floatPorcDesc)&&$floatPorcDesc==0)&&(!empty($floatPrecioDescuento)&&$floatPrecioDescuento>0))
                        {
                            $floatPorcDesc = ($floatPrecioDescuento * 100)/$floatPrecioInstalacion;
                        }
                        $objCargosCab = $emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findOneBy(array('nombreParametro' => self::RANGO_APROBACION_SOLICITUDES,
                                                                    'modulo'          => self::COMERCIAL,
                                                                    'proceso'         => self::ADMINISTRACION_CARGOS_SOLICITUDES,
                                                                    'estado'          => 'Activo'));
                        if(!is_object($objCargosCab) || empty($objCargosCab))
                        {
                            throw new \Exception('No se encontraron datos con los parámetros enviados.');
                        }
                        $arrayCargosDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findBy(array('parametroId' => $objCargosCab->getId(),
                                                                   'valor4'      => 'ES_JEFE',
                                                                   'valor7'      => 'SI',
                                                                   'estado'      => 'Activo'));
                        $strCargoAsignado = "";
                        if(is_array($arrayCargosDet))
                        {
                            foreach($arrayCargosDet as $objCargosItem)
                            {
                                if(floatval($floatPorcentajeDescuento) >= floatval($objCargosItem->getValor1()) && 
                                    floatval($floatPorcentajeDescuento) <= floatval($objCargosItem->getValor2()))
                                {
                                    $strCargoAsignado = ucwords(strtolower(str_replace("_"," ",$objCargosItem->getValor3())));
                                }
                            }
                        }
                        //Correo del vendedor.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                                 "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        //Correo del subgerente
                        $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                               ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                        if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                            $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                 "Correo Electronico");
                            if(!empty($arrayCorreos) && is_array($arrayCorreos))
                            {
                                foreach($arrayCorreos as $arrayItem)
                                {
                                    if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                    {
                                        $arrayDestinatarios[] = $arrayItem['valor'];
                                    }
                                }
                            }
                        }
                        //Correo de la persona quien crea la solicitud.
                        $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objDetalleSol->getUsrCreacion(),
                                                                                                 "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                        $strCuerpoCorreo  = "El presente correo es para indicarle que se rechazó una solicitud en TelcoS+ con los siguientes datos:";
                        $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                      "strIdentificacionCliente" => $strIdentificacion,
                                                      "strObservacion"           => $objDetalleSol->getObservacion(),
                                                      "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                      "strCargoAsignado"         => $strCargoAsignado);
                        $serviceEnvioPlantilla->generarEnvioPlantilla("RECHAZO DE SOLICITUD DE INSTALACIÓN",
                                                                      array_unique($arrayDestinatarios),
                                                                      "NOTIFICACION",
                                                                      $arrayParametrosMail,
                                                                      $strPrefijoEmpresa,
                                                                      "",
                                                                      "",
                                                                      null,
                                                                      true,
                                                                      "notificaciones_telcos@telconet.ec");
                    }
                }
                else
                {
                    throw $this->createNotFoundException('No se encontro el servicio asociado a la solicitud buscada');
                }


                if($objDetalleSol->getMotivoId())
                {
                    $objMotivo          = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                    if($objMotivo)
                    {
                        $strMotivo      = $objMotivo->getNombreMotivo();
                    }
                }

                $arrayData[] = array(
                                        "cliente"               => $strCliente,
                                        "login"                 => $strLogin,
                                        "servicio"              => $strProductoPlan,
                                        "motivo"                => $strMotivo,
                                        "descuento"             => $strDescuento,
                                        "observacionSolicitud"  => $objDetalleSol->getObservacion(),
                                        "fechaCreacion"         => strval(date_format($objDetalleSol->getFeCreacion(), "d/m/Y G:i")),
                                        "usuarioCreacion"       => $objDetalleSol->getUsrCreacion()
                );
            }

            $strMotivoRechazo = '';
            if($intIdMotivo)
            {
                $objMotivoRechazo       = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                if($objMotivoRechazo)
                {
                    $strMotivoRechazo   = $objMotivoRechazo->getNombreMotivo();
                }
            }
            
            /* Envío Correo Solicitudes Instalacion Rechazadas
             * Para los diferentes tipos de solicitudes que serán rechazadas por Gerencia, se utilizará la misma plantilla de rechazo,
             * con la diferencia de que dependiendo del tipo de solicitud que se desea rechazar, se adjuntará un PDF con la información de las 
             * distintas solicitudes que fueron rechazadas. Es por esta razón que se enviarán como parámetros los nombres de las cabeceras 
             * de las columnas de la tabla con el contenido de las solicitudes.
             * Además se envían los parámetros necesarios para el contenido del correo que se enviará utilizando plantillas.
             */
            $arrayNombresCabeceraAdjunto = array(   "Cliente",
                                                    "Login",
                                                    "Servicio",
                                                    "Motivo",
                                                    "Descuento",
                                                    "Observación Solicitud",
                                                    "Fecha Creación",
                                                    "Usuario Creación");

            $arrayParametrosMail = array(
                                            "idEmpresaSession"              => $strCodEmpresa,
                                            "prefijoEmpresaSession"         => $strPrefijoEmpresa,
                                            "codigoPlantilla"               => "RECHZ_AUTORIZAC",
                                            "usrCreacion"                   => $strUsrCreacion,
                                            "ipClient"                      => $strIpClient,
                                            "empleadoSession"               => $strEmpleado,
                                            "tituloAdjunto"                 => "RECHAZO DE SOLICITUDES DE DESCUENTO POR INSTALACIÓN",
                                            "tipoAutorizacion"              => "AUTORIZACIÓN DE DESCUENTO POR INSTALACIÓN",
                                            "tipoGestion"                   => "RECHAZO",
                                            "nombreTipoAutorizacionAdjunto" => "Rechazo_Autorizacion_Descuento_Instalacion",
                                            "arrayNombresCabeceraAdjunto"   => $arrayNombresCabeceraAdjunto,
                                            "arrayDataAdjunto"              => $arrayData,
                                            "asunto"                        => "Gestion en Solicitudes de Descuento por Instalacion",
                                            "motivoGestion"                 => $strMotivoRechazo
            );
            /* @var $serviceAutorizaciones \telconet\comercialBundle\Service\Autorizaciones */
            $serviceAutorizaciones = $this->get('comercial.Autorizaciones');
            $serviceAutorizaciones->envioMailAutorizaciones($arrayParametrosMail);

            $emComercial->getConnection()->commit();
            $objResponse->setContent("Se rechazaron las solicitudes de descuento por instalación con exito.");
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();
            $serviceUtil->insertError(  'Telcos+', 
                                        'SolicitudInstalacionController->rechazarSolicitudDescuentoAjaxAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpClient
            );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }
        return $objResponse;
    }

    public function getMotivosRechazoDescuento_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacioninstalacion','AutorizarInstalacion','rechazarSolicitudInstalacionAjax');
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
