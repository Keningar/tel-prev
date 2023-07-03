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
/**
 * InfoDetalleSolicitud controller.
 *
 */
class SolicitudCambioDocumentoController extends Controller implements TokenAuthenticatedController
{
    
     private $tipoSolicitud='SOLICITUD CAMBIO DOCUMENTO';
     const VALOR_INICIAL_BUSQUEDA = 0;
     const VALOR_LIMITE_BUSQUEDA  = 10;
    /**
    * @Secure(roles="ROLE_63-1")
    */ 
    public function indexAction()
    {
        $request = $this->getRequest();
        $session  = $request->getSession();      
        $em = $this->getDoctrine()->getManager();
	$puntoIdSesion=null;
        $entity = '';
        $ptoCliente_sesion=$session->get('ptoCliente');
        if($ptoCliente_sesion)
        {  
            $puntoIdSesion=$ptoCliente_sesion['id'];
            $entity = $em->getRepository('schemaBundle:InfoPunto')->find($puntoIdSesion);    
        }
        
        //$entities = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findAll();
		
        $em_seguridad = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("63", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
        $session->set('menu_modulo_activo', $entityItemMenuPadre->getNombreItemMenu());
        $session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
        $session->set('id_menu_modulo_activo', $entityItemMenuPadre->getId());
        $session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());  
		
        return $this->render('comercialBundle:solicitudcambiodocumento:index.html.twig', array(
            'item'      => $entityItemMenu,
            'entities'  => '',
            'entity'    => $entity,
            'puntoId'   => $puntoIdSesion
        ));
    }

    /**
     * @author Luis Cabrera <lcabrera@telconect.ec>
     * @version 1.1 01-08-2017 Se autorizan automáticamente las solicitudes de Cortesía a venta.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 17-08-2022 - Se envía notificación a la asistente, vendedor y subgerente.
     *
     */
    public function grabaSolicitud_ajaxAction() {
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $strCodEmpresa     = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');
        $strEmpleado       = $session->get('empleado');
        $strIpClient       = $request->getClientIp();
        $strUsrCreacion    = $session->get('user');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        //echo $id;die;
        $respuesta->setContent("error del Form");
        $em = $this->getDoctrine()->getManager('telconet');

        /* @var $serviceSolicitudCambioDocumento \telconet\comercialBundle\Service\SolicitudCambioDocumentoService */
        $serviceSolicitudCambioDocumento = $this->get('comercial.SolicitudCambioDocumento');
        $serviceEnvioPlantilla           = $this->get('soporte.EnvioPlantilla');
        $arrayDestinatarios              = array();
        //echo $peticion->get('tdoc');die;
		
        //Obtiene parametros enviados desde el ajax
        $peticion = $this->get('request');
        $strParametro = $peticion->get('param');
        $arrayValor = explode("|", $strParametro);
        $motivoId=$peticion->get('motivoId');
        $tipoSolicitudId=$peticion->get('ts');
        $strTipoDoc = $peticion->get('tdoc');
        $obs=$peticion->get('obs');
        //print_r($array_valor);echo "<br><br>";echo $relacionSistemaId;echo "<br><br>";echo $motivoId;die;
        $em->getConnection()->beginTransaction();
        try {
            foreach($arrayValor as $intId):
                $entity = new InfoDetalleSolicitud();
                $entity->setMotivoId($motivoId);
                $entityServicio = $em->getRepository('schemaBundle:InfoServicio')->find($intId);
                $entity->setServicioId($entityServicio);
                $entityTipoSolicitud= $em->getRepository('schemaBundle:AdmiTipoSolicitud')->find($tipoSolicitudId);
                $entity->setTipoSolicitudId($entityTipoSolicitud);
                $entity->setTipoDocumento($strTipoDoc);
                $entity->setObservacion($obs);
                $entity->setFeCreacion(new \DateTime('now'));
                $entity->setUsrCreacion($strUsrCreacion);
                $entity->setEstado('Pendiente');
                $em->persist($entity);
                $em->flush();

                //De cortesía a venta se autoriza automáticamente
                if($entityServicio->getEsVenta() == 'N' && $strTipoDoc == 'V')
                {
                    $arrayParametros = array(
                        'strCodEmpresa'     => $strCodEmpresa,
                        'strPrefijoEmpresa' => $strPrefijoEmpresa,
                        'strIpClient'       => $strIpClient,
                        'empleado'          => $strEmpleado,
                        'strUsrCreacion'    => $strUsrCreacion,
                        'param'             => $entity->getId(),
                        'tipoDoc'           => $strTipoDoc
                    );
                    $arrayRespuesta = $serviceSolicitudCambioDocumento->aprobarCambioDocumento($arrayParametros);
                    if($arrayRespuesta['intRespuesta'] == 0)
                    {
                        throw new \Exception($arrayRespuesta['strMensaje']);
                    }
                }
                else
                {
                    //Grabamos en la tabla de historial de la solicitud
                    $entityHistorial= new InfoDetalleSolHist();
                    $entityHistorial->setEstado('Pendiente');
                    $entityHistorial->setDetalleSolicitudId($entity);
                    $entityHistorial->setUsrCreacion($strUsrCreacion);
                    $entityHistorial->setFeCreacion(new \DateTime('now'));
                    $entityHistorial->setIpCreacion($request->getClientIp());
                    $entityHistorial->setMotivoId($motivoId);
                    $entityHistorial->setObservacion($obs);
                    $em->persist($entityHistorial);
                    $em->flush();				
                    //Bloque que obtiene los correos de la persona que crea la solicitud, vendedor asociado y subgerente.
                    if($strPrefijoEmpresa == 'TN')
                    {
                        $objPunto           = (is_object($entityServicio)) ? $entityServicio->getPuntoId():"";
                        $strVendedor        = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                        $objPersona         = (is_object($objPunto)) ? $objPunto->getPersonaEmpresaRolId()->getPersonaId():"";
                        $strCliente         = "";
                        $strIdentificacion  = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                        $strCliente         = (is_object($objPersona) && $objPersona->getRazonSocial()) ? 
                                               $objPersona->getRazonSocial(): $objPersona->getNombres() . " " .$objPersona->getApellidos();
                        //Correo del vendedor.
                        $arrayCorreos = $em->getRepository('schemaBundle:InfoPersona')
                                        ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
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
                        //Correo del subgerente
                        $arrayResultadoCorreo    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                   ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                        if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                        {
                            $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                            $arrayCorreos         = $em->getRepository('schemaBundle:InfoPersona')
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
                    }
                }
            endforeach;
            if($strPrefijoEmpresa == "TN")
            {
                //Correo de la persona quien crea la solicitud.
                $arrayCorreos = $em->getRepository('schemaBundle:InfoPersona')
                                   ->getContactosByLoginPersonaAndFormaContacto($strUsrCreacion,"Correo Electronico");
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
                $strCuerpoCorreo = "El presente correo es para indicarle que se creó una solicitud en TelcoS+ con los siguientes datos:";
                $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                              "strIdentificacionCliente" => $strIdentificacion,
                                              "strObservacion"           => $obs,
                                              "strCuerpoCorreo"          => $strCuerpoCorreo,
                                              "strCargoAsignado"         => "Gerente General");
                $serviceEnvioPlantilla->generarEnvioPlantilla("CREACIÓN DE SOLICITUD DE CORTESÍA",
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
            $em->getConnection()->commit();
            $respuesta->setContent("Se registró la solicitud requerida con éxito.");
        } catch (\Exception $e) {
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            $respuesta->setContent("Error al tratar de guardar solicitud. Consulte con el Administrador.");
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

    /*
    * @Secure(roles="ROLE_63-162")
    */     
    public function getServiciosParaSolicitudCambioDoc_ajaxAction($id) {
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
        $resultado = $em->getRepository('schemaBundle:InfoServicio')->findServiciosPorEmpresaPorPunto($idEmpresa,$id,$limit,$page,$start);
        $datos = $resultado['registros'];
        $total = $resultado['total'];
        foreach ($datos as $dato):
            //Verifica si existe ya una solicitud de descuento solicitado y que este pendiente  
            $detalleSolicitud=$em->getRepository('schemaBundle:InfoDetalleSolicitud')->findSolicDescuentoPorServicio($dato->getId(),$this->tipoSolicitud,'Pendiente');
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
    
    /**
    * Documentación para funcion 'aprobarCambioDocumentoAction'.
    * Se establece la cantidad máxima de decimales de la variable totalAnioPasado a 2.
    * @author Douglas Natha <dnatha@telconet.ec>
    * @version 1.0 26-11-2019
    */ 
    public function aprobarCambioDocumentoAction()
    {
        $request    = $this->getRequest();
        $idEmpresa  = $request->getSession()->get('idEmpresa'); 
        $em         = $this->get('doctrine')->getManager('telconet');
        $resultado  = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->totalServiciosPorSolicitud($this->tipoSolicitud,'Pendiente', $idEmpresa);
        $anioActual = $em->getRepository('schemaBundle:InfoServicio')->totalServicioCortesiaAnual(date("Y"),'Activo',$idEmpresa);
        $anioPasado = $em->getRepository('schemaBundle:InfoServicio')->totalServicioCortesiaAnual(date("Y")-1,'Activo',$idEmpresa);
        
        return $this->render('comercialBundle:solicitudcambiodocumento:aprobarCambioDocumento.html.twig', array(
         'totalAprobar'    => '$'.$resultado[0]['total'],
         'totalAnioActual' => '$'.$anioActual[0]['total'],
         'totalAnioPasado' => '$'.(round($anioPasado[0]['total'], 2))
         ));
    }
    
    /**
    * Documentación para funcion 'gridAprobarCambioDocumentoAction'.
    * funcion que envia los datos para el listado de solicitudes de cambio de documento
    * @author <amontero@telconet.ec>
    * @since 12/12/2014
    * @return objeto - response
    * 
    * @author Sofia Fernandez <sfernandez@telconet.ec>
    * @version 1.1 13-12-2017 Se adicciona los parametros nombre, apellido, razon social, usuario creacion,
    *                         y login, el método findSolicDescuentoPorCriterios recibirá por parametro un array 
    *
    * Se le concatena al precio de venta (valor) el signo de dolar y en caso que el $arreglo este vacio se 
    * retorna ese valor.
    * @author Douglas Natha <dnatha@telconet.ec>
    * @version 1.2 26-11-2019
    * @since 1.1  
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.3 03-01-2020 - Se envía un arreglo vacío en caso de que no existan solicitudes pendientes por aprobar,
    *                           para visualizar mensaje al usuario en el grid.
    *
    * @author David León <mdleon@telconet.ec>
    * @version 1.4 13-10-2022 - Se valida si existe el vendedor en la info_persona.
    *
    * @author Kevin Baque Puya <kbaque@telconet.ec>
    * @version 1.5 17-10-2022 - Se agregan nuevos parámetros de búsqueda.
    */ 
    /*
    * @Secure(roles="ROLE_")
    */
    public function gridAprobarCambioDocumentoAction()
    {
        $request    = $this->get('request');
        $session    = $request->getSession();
        $idEmpresa  = $session->get('idEmpresa');
        $fechaDesde = explode('T', $request->get("fechaDesde"));
        $fechaHasta = explode('T', $request->get("fechaHasta"));
        $strNombre           = $request->get("nombre");
        $strApellido         = $request->get("apellido");
        $strRazonSocial      = $request->get("razonSocial");
        $strIdentificacion   = $request->get("identificacion");
        $strEstado           = $request->get("strEstadoFiltro");
        $strUsuarioCreacion  = $request->get("usuarioCreacion");
        $strLogin            = $request->get("login");
        $intLimit            = $request->get("limit") ? $request->get("limit"):self::VALOR_LIMITE_BUSQUEDA;
        $intStart            = $request->get("start") ? $request->get("start"):self::VALOR_INICIAL_BUSQUEDA;
        $strDraw             = $request->get("draw")  ? $request->get("draw"):"1";
        $em         = $this->get('doctrine')->getManager('telconet');
        $arrayParametros['strEstado']          = !empty($strEstado) ? $strEstado :"PENDIENTE";
        $arrayParametros['strTipoSolicitud']   = $this->tipoSolicitud;
        $arrayParametros['intIdEmpresa']       = $idEmpresa;
        $arrayParametros['strFechaDesde']      = $fechaDesde;
        $arrayParametros['strFechaHasta']      = $fechaHasta;        
        $arrayParametros['strNombre']          = $strNombre;
        $arrayParametros['strApellido']        = $strApellido;
        $arrayParametros['strRazonSocial']     = $strRazonSocial;
        $arrayParametros['strIdentificacion']  = $strIdentificacion;
        $arrayParametros['strUsuarioCreacion'] = $strUsuarioCreacion;
        $arrayParametros['strLogin']           = $strLogin;        
        $arrayParametros['intStart']           = $intStart;
        $arrayParametros['intLimit']           = $intLimit;

        $resultado = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                     ->findSolicDescuentoPorCriterios($arrayParametros);
        $datos=$resultado['registros'];
        $total=$resultado['total'];

        foreach($datos as $datos)
        {
            $linkVer        = '#';
            $entityMotivo   = $em->getRepository('schemaBundle:AdmiMotivo')->find($datos->getMotivoId());
            $objServicio    = $em->getRepository('schemaBundle:InfoServicio')->find($datos->getServicioId()->getId());
            $producto       = '';
            $strUsrVendedor = '';
            $strPrecioVenta = '';
            if(is_object($objServicio))
            {
                $strPrecioVenta = $objServicio->getPrecioVenta();
            }
            if(is_object($datos->getServicioId()))
            {
                $strNomApVendedor = "";
                $strUsrVendedor    = $datos->getServicioId()->getUsrVendedor();
                $objPersona        = $em->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$strUsrVendedor));
                if(is_object($objPersona))
                {
                    $strNomApVendedor  = $objPersona->getNombres() . ' ' . $objPersona->getApellidos();
                }
            }
            if($datos->getServicioId()->getProductoId())
            {
                $entityProducto = $em->getRepository('schemaBundle:AdmiProducto')->find($datos->getServicioId()->getProductoId()->getId());
                $producto       = $entityProducto->getDescripcionProducto();
            }
            else
            {
                $entityProducto = $em->getRepository('schemaBundle:InfoPlanCab')->find($datos->getServicioId()->getPlanId()->getId());
                $producto       = $entityProducto->getNombrePlan();
            }
            $tipodoc = "";
            if($datos->getTipoDocumento() == "C")
            {    
                $tipodoc = "Cortesia";
            }    
            elseif($datos->getTipoDocumento() == "V")
            {    
                $tipodoc = "Venta";
            }   
            elseif($datos->getTipoDocumento() == "D")
            {    
                $tipodoc = "Demo";
            }
            $objPersonaEmpresaRol = null;
            $strCliente = "";
            if(is_object($datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId()))
            {
                $objPersonaEmpresaRol = $datos->getServicioId()->getPuntoId()->getPersonaEmpresaRolId();
            }
            if(is_object($objPersonaEmpresaRol->getPersonaId()))
            {
                $strCliente = sprintf('%s', $objPersonaEmpresaRol->getPersonaId()); 
            }
            $arreglo[] = array(
                'id'          => $datos->getId(),
                'cliente'     => $strCliente,
                'asesor'      => $strNomApVendedor,
                'servicio'    => $producto,
                'login'       => $datos->getServicioId()->getPuntoId()->getLogin(),
                'motivo'      => $entityMotivo->getNombreMotivo(),
                'estadoSolicitud'=> $datos->getEstado(),
                'descuento'   => $tipodoc,
                'observacion' => $datos->getObservacion(),
                'feCreacion'  => strval(date_format($datos->getFeCreacion(), "d/m/Y G:i")),
                'usrCreacion' => $datos->getUsrCreacion(),
                'valor'       => '$'.$strPrecioVenta,
                'linkVer'     => $linkVer
            );
        }
        if(!empty($arreglo))
        {    
            $objResponse = new Response(json_encode(array("total"           => $total,
                                                          "solicitudes"     => $arreglo,
                                                          "draw"            => $strDraw, 
                                                          "recordsTotal"    => $total,
                                                          "recordsFiltered" => $total)));
        }    
        else
        {
            $objResponse = new Response(json_encode(array("total"           => $total,
                                                          "solicitudes"     => [],
                                                          "draw"            => $strDraw, 
                                                          "recordsTotal"    => $total,
                                                          "recordsFiltered" => $total)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    /**    
     * Documentación para el método 'aprobarCambioDocumentoAjaxAction'.
     *
     * Descripcion: Permite aprobar una o más solicitudes de descuento.
     * 
     * version 1.0 Versión Inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-09-2016 - Se envía correo cuando se aprueba un cambio de documento utilizando la respectiva plantilla 
     *                           y adjuntando un archivo con la información de la solicitudes
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.2 27-07-2017 - Se agrega la lógica para almacenar el historial del servicio cuando se aprueba una solicitud.
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.3 01-08-2017 - Se realiza toda la lógica para aprobar el cambio de documento dentro del service SolicitudCambioDocumentoService.
     * 
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.4 11-12-2019 - Se obtiene la observación de la aprobación de la solicitud y se la pasa al arreglo arrayParametros.
     * 
     * @return Response $objResponse
     * 
     */
    public function aprobarCambioDocumentoAjaxAction()
    {       
        $objRequest                     = $this->getRequest();
        $objSession                     = $objRequest->getSession();
        $strCodEmpresa                  = $objSession->get('idEmpresa');
        $strPrefijoEmpresa              = $objSession->get('prefijoEmpresa');
        $strUsrCreacion                 = $objSession->get('user');
        $strObservacion                 = $objRequest->get('obs');
        $strEmpleado                    = $objSession->get('empleado');
        $strIpClient                    = $objRequest->getClientIp();
        $objResponse                    = new Response();
        $objResponse->headers->set('Content-Type', 'text/plain');        
        $objResponse->setContent("error del Form");  
        $emComercial                    = $this->getDoctrine()->getManager('telconet');
        /* @var $serviceServicioHistorial \telconet\comercialBundle\Service\InfoServicioHistorialService */
        $serviceServicioHistorial       = $this->get('comercial.InfoServicioHistorial');
        
        //Obtiene parametros enviados desde el ajax
        $strParametro                   = $objRequest->get('param');
        $strTipoDoc                     = $objRequest->get('tipoDoc');
        $serviceUtil                    = $this->get('schema.Util');
        /* @var $serviceSolicitudCambioDocumento \telconet\comercialBundle\Service\SolicitudCambioDocumentoService */
        $serviceSolicitudCambioDocumento = $this->get('comercial.SolicitudCambioDocumento');
        
        try
        {
                $arrayParametros = array(
                'strCodEmpresa'     => $strCodEmpresa,
                'strPrefijoEmpresa' => $strPrefijoEmpresa,
                'strIpClient'       => $strIpClient,
                'empleado'          => $strEmpleado,
                'strUsrCreacion'    => $strUsrCreacion,
                'param'             => $strParametro,
                'tipoDoc'           => $strTipoDoc,
                'obs'               => $strObservacion
            );
            $arrayRespuesta = $serviceSolicitudCambioDocumento->aprobarCambioDocumento($arrayParametros);
            if($arrayRespuesta['intRespuesta'] == 0)
            {
                throw new \Exception($arrayRespuesta['strMensaje']);
            }
            else
            {
                $objResponse->setContent($arrayRespuesta['strMensaje']);
            }
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError(  'Telcos+', 
                                        'SolicitudCambioDocumentoController->aprobarCambioDocumentoAjaxAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpClient
            );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }
        
        return $objResponse;
    }

    /**    
     * Documentación para el método 'rechazarSolicitudCambioDocumentoAjaxAction'.
     *
     * Descripcion: Permite rechazar una o más solicitudes de cambio de documento.
     * 
     * version 1.0 Versión Inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-09-2016 - Se envía correo cuando se rechaza un cambio de documento utilizando la respectiva plantilla 
     *                           y adjuntando un archivo con la información de la solicitudes
     * 
     * @author Modificado: Douglas Natha <dnatha@telconet.ec>
     * @version 1.2 11-12-2019 - Se guarda el registro de la solicitud rechazada junto con su observación en el historial
     * de servicio.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.3 06-01-2020 - Se agrega lógica para guardar historial del servicio.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.4 11-10-2022 - Se envía notificación a la asistente, vendedor y subgerente.
     *
     * @return Response $objResponse
     * 
     */
    public function rechazarSolicitudCambioDocumentoAjaxAction()
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

        /* @var $serviceServicioHistorial \telconet\comercialBundle\Service\InfoServicioHistorialService */
        $serviceServicioHistorial       = $this->get('comercial.InfoServicioHistorial');

        $strParametro = $objRequest->get('param');
        $arrayIdsSolicitudes    = explode("|", $strParametro);
        $intIdMotivo            = $objRequest->get('motivoId');
        $strObservacion         = $objRequest->get('obs');
        $serviceUtil            = $this->get('schema.Util');
        $arrayData              = array();
        $serviceEnvioPlantilla  = $this->get('soporte.EnvioPlantilla');
        $emComercial->getConnection()->beginTransaction();
        try
        {
            foreach($arrayIdsSolicitudes as $intId)
            {
                $strCliente         = '';
                $strLogin           = '';
                $strProductoPlan    = '';
                $strMotivo          = '';
                $strValor           = '';
                $objDetalleSol      = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intId);
                if(!$objDetalleSol)
                {
                    throw $this->createNotFoundException('No se encontro la solicitud buscada');
                }
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
                $objDetalleSolHistorial->setObservacion($strObservacion);
                $emComercial->persist($objDetalleSolHistorial);
                $emComercial->flush();

                /*Se obtiene la información necesaria para el archivo que se enviará como adjunto del correo*/
                $objServicio = $objDetalleSol->getServicioId();
                if($objServicio)
                {
                    $objProducto    = $objServicio->getProductoId();
                    $objPlan        = $objServicio->getPlanId();
                    $objPunto       = $objServicio->getPuntoId();
                    $strValor       = $objServicio->getPrecioVenta();
                    if($objProducto)
                    {
                        $strProductoPlan = $objProducto->getDescripcionProducto();
                    }
                    elseif($objPlan)
                    {
                        $strProductoPlan = $objPlan->getNombrePlan();
                    }

                    if($objPunto)
                    {
                        $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
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
                    //SE ALMACENA LA INFORMACION EN EL HISTORIAL DEL SERVICIO.
                    $arrayParametros      = array(
                        'objServicio'     => $objServicio,
                        'strIpClient'     => $strIpClient,
                        'strUsrCreacion'  => $strUsrCreacion,
                        'strObservacion'  => $strObservacion . " - SOLICITUD RECHAZADA",
                        'strAccion'       => 'cambioDocumento'
                    );
                    if(!empty($intIdMotivo))
                    {
                        $objMotivo                    = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                        $arrayParametros['intMotivo'] = $objMotivo->getId();
                    }
                    $objServicioHistorial = $serviceServicioHistorial->crearHistorialServicio($arrayParametros);
                    $emComercial->persist($objServicioHistorial);
                    $emComercial->flush();
                }
                else
                {
                    throw $this->createNotFoundException('No se encontro el servicio asociado a la solicitud buscada');
                }

                $strTipoDocAbrev    = $objDetalleSol->getTipoDocumento();
                if($strTipoDocAbrev == "C")
                {
                    $strTipodoc     = "Cortesia";
                }
                elseif($strTipoDocAbrev == "V")
                {
                    $strTipodoc     = "Venta";
                }
                elseif($strTipoDocAbrev == "D")
                {
                    $strTipodoc     = "Demo";
                }

                if($objDetalleSol->getMotivoId())
                {
                    $objMotivo      = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                    if($objMotivo)
                    {
                        $strMotivo  = $objMotivo->getNombreMotivo();
                    }
                }

                $arrayData[] = array(
                    "servicio"              => $strProductoPlan,
                    "login"                 => $strLogin,
                    "cliente"               => $strCliente,
                    "motivo"                => $strMotivo,
                    "valor"                 => $strValor,
                    "tipo_doc"              => $strTipodoc,
                    "observacionSolicitud"  => $objDetalleSol->getObservacion(),
                    "fechaCreacion"         => strval(date_format($objDetalleSol->getFeCreacion(), "d/m/Y G:i")),
                    "usuarioCreacion"       => $objDetalleSol->getUsrCreacion()
                );
                if($strPrefijoEmpresa == "TN")
                {
                    $arrayDestinatarios = array();
                    $strVendedor        = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                    $strCliente         = "";
                    $strIdentificacion  = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                    $strCliente         = (is_object($objPersona) && $objPersona->getRazonSocial()) ? 
                                           $objPersona->getRazonSocial(): $objPersona->getNombres() . " " .$objPersona->getApellidos();
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
                                if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                    }
                    //Correo de la persona quien crea la solicitud.
                    $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getContactosByLoginPersonaAndFormaContacto($strUsrCreacion,"Correo Electronico");
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
                    $strCuerpoCorreo      = "El presente correo es para indicarle que se rechazó una solicitud en TelcoS+ con los siguientes datos:";
                    $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                  "strIdentificacionCliente" => $strIdentificacion,
                                                  "strObservacion"           => $objDetalleSol->getObservacion(),
                                                  "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                  "strCargoAsignado"         => "Gerente General");
                    $serviceEnvioPlantilla->generarEnvioPlantilla("RECHAZO DE SOLICITUD DE CORTESÍA",
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

            $strMotivoRechazo           = '';
            if($intIdMotivo)
            {
                $objMotivoRechazo       = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
                if($objMotivoRechazo)
                {
                    $strMotivoRechazo   = $objMotivoRechazo->getNombreMotivo();
                }
            }

            /* Envío Correo Solicitudes Cambio de Documento Rechazadas
             * Para los diferentes tipos de solicitudes que serán rechazadas por Gerencia, se utilizará la misma plantilla de rechazo,
             * con la diferencia de que dependiendo del tipo de solicitud que se desea rechazar, se adjuntará un PDF con la información de las 
             * distintas solicitudes que fueron rechazadas. Es por esta razón que se enviarán como parámetros los nombres de las cabeceras 
             * de las columnas de la tabla con el contenido de las solicitudes.
             * Además se envían los parámetros necesarios para el contenido del correo que se enviará utilizando plantillas.
             */
            $arrayNombresCabeceraAdjunto = array(   "Servicio",
                                                    "Login",
                                                    "Cliente",
                                                    "Motivo",
                                                    "Valor",
                                                    "Tipo Doc",
                                                    "Observación",
                                                    "Fecha Creación",
                                                    "Usuario Creación");

            $arrayParametrosMail = array(
                                            "idEmpresaSession"              => $strCodEmpresa,
                                            "prefijoEmpresaSession"         => $strPrefijoEmpresa,
                                            "codigoPlantilla"               => "RECHZ_AUTORIZAC",
                                            "usrCreacion"                   => $strUsrCreacion,
                                            "ipClient"                      => $strIpClient,
                                            "empleadoSession"               => $strEmpleado,
                                            "tituloAdjunto"                 => "RECHAZO DE SOLICITUDES DE CAMBIO DE DOCUMENTO",
                                            "tipoAutorizacion"              => "AUTORIZACIÓN DE CAMBIO DE DOCUMENTO",
                                            "tipoGestion"                   => "RECHAZO",
                                            "nombreTipoAutorizacionAdjunto" => "Rechazo_Autorizacion_Cambio_Documento",
                                            "arrayNombresCabeceraAdjunto"   => $arrayNombresCabeceraAdjunto,
                                            "arrayDataAdjunto"              => $arrayData,
                                            "asunto"                        => "Gestion en Solicitudes de Cambio de Documento",
                                            "motivoGestion"                 => $strMotivoRechazo,
                                            "observacion"                   => $strObservacion
                                    );
            /* @var $serviceAutorizaciones \telconet\comercialBundle\Service\Autorizaciones */
            $serviceAutorizaciones = $this->get('comercial.Autorizaciones');
            $serviceAutorizaciones->envioMailAutorizaciones($arrayParametrosMail);

            $emComercial->getConnection()->commit();
            $objResponse->setContent("Se rechazaron las solicitudes de cambio de documento con exito.");
        }
        catch(\Exception $e)
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }
            $emComercial->getConnection()->close();

            $serviceUtil->insertError(  'Telcos+', 
                                        'SolicitudCambioDocumentoController->rechazarSolicitudCambioDocumentoAjaxAction', 
                                        $e->getMessage(), 
                                        $strUsrCreacion, 
                                        $strIpClient
            );
            $objResponse->setContent("Ha ocurrido un problema. Por favor informe a Sistemas");
        }

        return $objResponse;
    }

    public function getMotivosRechazoCambioDocumento_ajaxAction() {
        /* Modificacion a utilizacion de estados por modulos */
        //$session = $this->get('request')->getSession();
        //$modulo_activo=$session->get("modulo_activo");
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiMotivo')
		->findMotivosPorModuloPorItemMenuPorAccion('aprobacioncambiodocumento','AutorizacionCambioDocumento','rechazarSolicitudCambioDocumentoAjax');
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
