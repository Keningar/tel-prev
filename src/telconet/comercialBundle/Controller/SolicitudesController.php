<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\schemaBundle\Entity\InfoDocumentoFinancieroCab;
use telconet\schemaBundle\Entity\AdmiCaracteristica;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Form\InfoDetalleSolicitudType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * Solicitudes controller.
 *
 */
class SolicitudesController extends Controller
{
    const RANGO_APROBACION_SOLICITUDES      = 'RANGO_APROBACION_SOLICITUDES';
    const ADMINISTRACION_CARGOS_SOLICITUDES = 'ADMINISTRACION_CARGOS_SOLICITUDES';
    const COMERCIAL                         = 'COMERCIAL';
    const CARGO_GRUPO_ROLES_PERSONAL        = 'CARGO_GRUPO_ROLES_PERSONAL';
    const GRUPO_ROLES_PERSONAL              = 'GRUPO_ROLES_PERSONAL';
    const GERENTE_VENTAS                    = 'GERENTE_VENTAS';
    const ROLES_NO_PERMITIDOS               = 'ROLES_NO_PERMITIDOS'; 
    const VALOR_INICIAL_BUSQUEDA = 0;
    const VALOR_LIMITE_BUSQUEDA  = 10;

    /**
     * indexAction
     * 
     * Documentación para el método 'indexAction'.
     *
     * Método que redirige a pantalla principal de consulta de solicitudes
     *     
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 22-01-2018
     * @since   1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 03-07-2018 - Se agrega el perfil:  	Aprobar Traslado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 28-10-2018 - Se agrega el perfil para aprobar o rechazar solicitudes del servicio Internet Small Business
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.4 17-03-2021 - Se envia parametro de la empresa que se inicia sesion.
     */
    public function indexAction() 
    {
        $em                = $this->getDoctrine()->getManager();
        $request           = $this->getRequest();
        $session           = $request->getSession();
		$puntoIdSesion     = null;
        $ptoCliente_sesion = $session->get('ptoCliente');
		if($ptoCliente_sesion)
        {  
			$puntoIdSesion = $ptoCliente_sesion['id'];
		}
//        $entities = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findAll();
		
        $em_seguridad        = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu      = $em_seguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("191", "1"); 
        $entityItemMenuPadre = $entityItemMenu->getItemMenuId(); 
		$session->set('menu_modulo_activo'       , $entityItemMenuPadre->getNombreItemMenu());
		$session->set('nombre_menu_modulo_activo', $entityItemMenuPadre->getTitleHtml());
		$session->set('id_menu_modulo_activo'    , $entityItemMenuPadre->getId());
		$session->set('imagen_menu_modulo_activo', $entityItemMenuPadre->getUrlImagen());  
        
        //se agrega control de roles permitidos
        $arrayRolesPermitidos = array();
        //MODULO 404 - TRASLADO REUBICACION
        if(true === $this->get('security.context')->isGranted('ROLE_404-5639'))
        {
            $arrayRolesPermitidos[] = 'ROLE_404-5639'; //CAMBIO PRECIO TRASLADO
        }
        //MODULO 404 - TRASLADO REUBICACION
        if(true === $this->get('security.context')->isGranted('ROLE_404-5638'))
        {
            $arrayRolesPermitidos[] = 'ROLE_404-5638'; //APROBAR REUBICACION
        }
        //MODULO 404 - TRASLADO REUBICACION
        if(true === $this->get('security.context')->isGranted('ROLE_404-5917'))
        {
            $arrayRolesPermitidos[] = 'ROLE_404-5917'; //APROBAR TRASLADO
        }
        //MODULO 420 - Solicitud Servicio TelcoHome
        if(true === $this->get('security.context')->isGranted('ROLE_420-6117'))
        {
            $arrayRolesPermitidos[] = 'ROLE_420-6117'; //APROBAR/RECHAZAR SOLICITUD SERVICIO
        }
        //Aprobar o rechazar contratos para cloudforms
        if(true === $this->get('security.context')->isGranted('ROLE_413-5937'))
        {
            $arrayRolesPermitidos[] = 'ROLE_413-5937'; //APROBAR CONTRATO CLOUD PUBLIC
        }
        //Aprobar o rechazar solicitudes de servicios con tipo de red MPLS
        if(true === $this->get('security.context')->isGranted('ROLE_440-6857'))
        {
            $arrayRolesPermitidos[] = 'ROLE_440-6857';
        }
        return $this->render('comercialBundle:solicitudes:index.html.twig',
                             array(
                                    'item'            => $entityItemMenu,
                                    'rolesPermitidos' => $arrayRolesPermitidos,
                                    'empresaCod' => $session->get('prefijoEmpresa')
                                   )
                            );
    }
	
    /**
     * 
     * Método encargado para mostrar la información de las diferentes tipo de solicitudes
     * 
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1 - 27-07-2017 - Se valida la variable $arrayFechaDesdePlanif por defecto la fecha actual controlando el rango de valores por fecha.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 06-02-2019 Se verifica que si es una SOLICITUD APROBACION SERVICIO, sólo se filtre por fechas cuando el usuario 
     *                          lo haya seleccionado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 - 25-07-2018 - Se envía información del objeto router para generar ruta de descarga de documentos para cloudpublic
     * @since 1.2
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 23-10-2019 Se verifica que si es una SOLICITUD APROBACION SERVICIO TIPO RED MPLS, sólo se filtre por fechas cuando el usuario 
     *                          lo haya seleccionado.
     *
     */
    public function gridAction()
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
        
        $arrayFechaDesdePlanif = ($peticion->query->get("fechaDesdePlanif")) ?
                explode('T', $peticion->query->get("fechaDesdePlanif")) : array(0 => date("Y-m-d"));
        $arrayFechaHastaPlanif = explode('T',$peticion->query->get('fechaHastaPlanif'));
		
        $login2 = $peticion->query->get('login2');
        $descripcionPunto = $peticion->query->get('descripcionPunto');
        $vendedor = $peticion->query->get('vendedor');
        $ciudad = $peticion->query->get('ciudad');
        $estadoTipoSolicitud = $peticion->query->get('estadoTipoSolicitud');
        $idTipoSolicitud = $peticion->query->get('tipoSolicitud');
        
        $emComercial = $this->getDoctrine()->getManager("telconet");
        if(isset($idTipoSolicitud) && !empty($idTipoSolicitud) && intval($idTipoSolicitud) > 0)
        {
            $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')->find($idTipoSolicitud);

            if(is_object($objTipoSolicitud) && ($objTipoSolicitud->getDescripcionSolicitud() === "SOLICITUD APROBACION SERVICIO" || 
                $objTipoSolicitud->getDescripcionSolicitud() === "SOLICITUD APROBACION SERVICIO TIPO RED MPLS"))
            {
                $strFechaDesdeIngreso   = $peticion->query->get("fechaDesdeIngOrd");
                $strFechaHastaIngreso   = $peticion->query->get("fechaHastaIngOrd");
                
                $arrayFechaDesdePlanif = ($strFechaDesdeIngreso) ? explode('T', $strFechaDesdeIngreso) : "";
                $arrayFechaHastaPlanif = explode('T',$strFechaHastaIngreso);
            }
        }
        
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet_infraestructura");
        $em_general = $this->getDoctrine()->getManager("telconet_general");
        
        $objJson = $this->getDoctrine()
            ->getManager("telconet")
            ->getRepository('schemaBundle:InfoDetalleSolicitud')
            ->generarJsonSolicitudes($em, $em_general,$start, $limit, $arrayFechaDesdePlanif[0], $arrayFechaHastaPlanif[0],
										$login2, $descripcionPunto, $vendedor, $ciudad, $estadoTipoSolicitud,$idTipoSolicitud,$codEmpresa,
                                        $this->container->get('router'));
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }

    /**
     * Documentación para la función 'getSolicitudesAction'.
     *
     * Función que devuelve las solicitudes de acuerdo a los parámetros.
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 25-08-2022
     *
     * @return Response objeto JSON.
     *
     */
    public function getSolicitudesAction()
    {
        $objRequest              = $this->getRequest();
        $objSession              = $objRequest->getSession();
        $strPrefijoEmpresa       = $objSession->get('prefijoEmpresa');
        $intIdEmpresa            = $objSession->get('idEmpresa');
        $strUsrCreacion          = $objSession->get('user') ? $objSession->get('user') : '';
        $strIpCreacion           = $objRequest->getClientIp();
        $intLimit                = $objRequest->get("limit") ? $objRequest->get("limit"): self::VALOR_LIMITE_BUSQUEDA;
        $intStart                = $objRequest->get("start") ? $objRequest->get("start"): self::VALOR_INICIAL_BUSQUEDA;
        $strDraw                 = $objRequest->get("draw")  ? $objRequest->get("draw"):"1";
        $strTipoSolicitud        = $objRequest->get("strTipoSolicitud")  ? str_replace("_"," ",$objRequest->get("strTipoSolicitud")):"";
        $intIdCanton             = $objSession->get('intIdCanton') ? $objSession->get('intIdCanton') : "";
        $intIdEmpRol             = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol'):"";
        $strFechaDesde           = explode('T', $objRequest->get("strFechaDesde"));
        $strFechaHasta           = explode('T', $objRequest->get("strFechaHasta"));
        $strIdentificacion       = $objRequest->get("strIdentificacion");
        $strRazonSocial          = $objRequest->get("strRazonSocial");
        $strLogin                = $objRequest->get("strLogin");
        $strNombre               = $objRequest->get("strNombre");
        $strApellido             = $objRequest->get("strApellido");
        $strEstado               = $objRequest->get("strEstadoFiltro") ? $objRequest->get("strEstadoFiltro"):"pendiente";
        $emGeneral               = $this->get('doctrine')->getManager('telconet_general');
        $emComercial             = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura       = $this->get('doctrine')->getManager('telconet_infraestructura');
        $serviceUtilidades       = $this->get('administracion.Utilidades');
        $serviceUtil             = $this->get('schema.Util');
        $strEstadoActivo         = "Activo";
        $intTotal                = 0;
        $arraySolicitudes        = array();
        $arrayCargoPersona       = "Otros";
        $strCargosAdicionales    = ",'GERENTE_GENERAL_REGIONAL','GERENTE_GENERAL'";
        $arrayLoginVendedoresKam = array();
        $arrayRolesNoIncluidos   = array();
        $strRegionSesion         = "";
        $intTotal                = 0;
        try
        {
            if(empty($strTipoSolicitud))
            {
                throw new \Exception('Tipo de solicitud es un campo obligatorio para realizar la búsqueda.');
            }
            $objTipoSolicitud  = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                                             ->findOneBy(array("descripcionSolicitud" => $strTipoSolicitud,
                                                               "estado"               => "Activo"));
            if(empty($objTipoSolicitud) || !is_object($objTipoSolicitud))
            {
                throw new \Exception('El tipo de solicitud a buscar no existe.');
            }
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
                $arrayCargoPersona = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                 ->getCargosPersonas($strUsrCreacion,$strCargosAdicionales);
                $strTipoPersonal   = (!empty($arrayCargoPersona) && is_array($arrayCargoPersona)) ? 
                                      $arrayCargoPersona[0]['STRCARGOPERSONAL']:'Otros';

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
                $arrayParametrosKam['strCodEmpresa']         = $intIdEmpresa;
                $arrayParametrosKam['strEstadoActivo']       = 'Activo';
                $arrayParametrosKam['strDescCaracteristica'] = self::CARGO_GRUPO_ROLES_PERSONAL;
                $arrayParametrosKam['strNombreParametro']    = self::GRUPO_ROLES_PERSONAL;
                $arrayParametrosKam['strDescCargo']          = self::GERENTE_VENTAS;
                $arrayParametrosKam['strDescRolNoPermitido'] = self::ROLES_NO_PERMITIDOS;
                $arrayResultadoVendedoresKam                 = $emComercial->getRepository('schemaBundle:InfoPersona')
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
                $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($intIdCanton);
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
            $arrayParametros['strEstado']               = (!empty($strEstado)) ? str_replace(" ","-",$strEstado):'PENDIENTE';
            $arrayParametros['strTipoSolicitud']        = $objTipoSolicitud->getDescripcionSolicitud();
            $arrayParametros['intIdEmpresa']            = $intIdEmpresa;
            $arrayParametros['strFechaDesde']           = $strFechaDesde;
            $arrayParametros['strFechaHasta']           = $strFechaHasta;
            $arrayParametros['strNombre']               = $strNombre;
            $arrayParametros['strApellido']             = $strApellido;
            $arrayParametros['strRazonSocial']          = $strRazonSocial;
            $arrayParametros['strIdentificacion']       = $strIdentificacion;
            $arrayParametros['strLogin']                = $strLogin;
            $arrayParametros['intStart']                = $intStart;
            $arrayParametros['intLimit']                = $intLimit;
            $arrayParametros['arrayLoginVendedoresKam'] = $arrayLoginVendedoresKam;
            $arrayParametros['strRegion']               = $strRegionSesion;
            $arrayParametros['strUsrCreacion']          = $strUsrCreacion;
            $arrayParametros['arrayRolNoPermitido']     = (!empty($arrayRolesNoIncluidos) && is_array($arrayRolesNoIncluidos))?
                                                          $arrayRolesNoIncluidos:"";
            $arrayResultado                             = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->findSolicDescuentoPorCriterios($arrayParametros);
            if(!empty($arrayResultado) && is_array($arrayResultado))
            {
                $intTotal = $arrayResultado["total"] ? $arrayResultado["total"]:0;
                foreach($arrayResultado['registros'] as $objItem)
                {
                    $strNombreMotivo         = '';
                    $strNombresCompletos     = '';
                    $objIdServicio           =  $objItem->getServicioId();
                    if(!empty($objIdServicio) && is_object($objIdServicio))
                    {
                        $strLoginVendedor    = $objIdServicio->getUsrVendedor();
                        $objPersonaVendedor  = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneBy(array('login'=>$strLoginVendedor));
                        $strNombresCompletos = (!empty($objPersonaVendedor) && is_object($objPersonaVendedor)) 
                                               ? $objPersonaVendedor->getNombres().' '.$objPersonaVendedor->getApellidos(): $strLoginVendedor;
                    }
                    else
                    {
                        throw new \Exception('No existe servicio asociado con la solicitud.');
                    }

                    $floatPrecioVenta         = $objIdServicio->getPrecioVenta() ? $objIdServicio->getPrecioVenta():0;
                    $intCantidad              = $objIdServicio->getCantidad() ? $objIdServicio->getCantidad():0;
                    $floatPrecioDescuento     = $objItem->getPrecioDescuento() ? $objItem->getPrecioDescuento() : 0;
                    $floatPorcentajeDescuento = $objItem->getPorcentajeDescuento() ? $objItem->getPorcentajeDescuento() : 0;

                    if((!empty($floatPrecioVenta) && $floatPrecioVenta > 0) && (!empty($intCantidad) && $intCantidad > 0))
                    {
                        $floatValorTotal = $floatPrecioVenta * $intCantidad;
                    }
                    else
                    {
                        $floatValorTotal = 0;
                    }
                    if($objItem->getMotivoId()!= null && $objItem->getMotivoId() > 0)
                    {
                        $entityMotivo    = $emComercial->getRepository('schemaBundle:AdmiMotivo')->find($objItem->getMotivoId());
                        $strNombreMotivo = (is_object($entityMotivo) && !empty($entityMotivo)) ? $entityMotivo->getNombreMotivo() : '';
                    }
                    $strProductoPlan ='';
                    if($objIdServicio->getProductoId())
                    {
                        $entityProducto  = $emComercial->getRepository('schemaBundle:AdmiProducto')->find($objIdServicio->getProductoId()->getId());
                        $strProductoPlan = (is_object($entityProducto) && !empty($entityProducto)) ? $entityProducto->getDescripcionProducto():'';
                    }
                    if($objIdServicio->getPuntoId()->getPersonaEmpresaRolId() && 
                       $objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId())
                    {
                        if($objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial())
                        {
                            $strCliente = $objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getRazonSocial();
                            $strEstado  = $objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getEstado();
                        }
                        else
                        {
                            $strCliente = $objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getNombres() . " " .
                            $objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getApellidos();
                            $strEstado  = $objIdServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->getEstado();
                        }
                    }
                    //Obtener valor de caracteristica de Servicios a Traslador.
                    $objCaracteristicaServicios = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                              ->findOneBy(array("descripcionCaracteristica" => "SERVICIOS_TRASLADAR",
                                                                                "estado"                    => "Activo"));

                    $objCaractValorServicios = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                           ->findOneBy(array("detalleSolicitudId" => $objItem->getId(),
                                                                             "caracteristicaId"   => $objCaracteristicaServicios->getId()));
                    $strServiciosTrasladar   = (is_object($objCaractValorServicios) && !empty($objCaractValorServicios)) ? 
                                                $objCaractValorServicios->getValor() : "";
                    //Bloque que obtiene el saldo del cliente.
                    $objCaracteristicaPuntoT = $emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                           ->findOneBy(array("descripcionCaracteristica" => "ID_PUNTO_TRASLADO",
                                                                             "estado"                    => "Activo"));
                    $objCaractValorPuntoT    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                           ->findOneBy(array("detalleSolicitudId"   => $objItem->getId(),
                                                                             "caracteristicaId"     => $objCaracteristicaPuntoT->getId()));
                    if(is_object($objCaractValorPuntoT) && !empty($objCaractValorPuntoT))
                    {
                        $strCaracteristicaPuntoT = $objCaractValorPuntoT->getValor();
                        $objPuntoTrasladar       = $emComercial->getRepository("schemaBundle:InfoPunto")->find($strCaracteristicaPuntoT);
                        $strLoginTrasladar       = (is_object($objPuntoTrasladar)&&!empty($objPuntoTrasladar))?$objPuntoTrasladar->getLogin():"";
                        //Se obtiene el saldo del cliente.
                        $arraySaldoPunto         = $emComercial->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                               ->obtieneDeudaPorCliente(array("intIdPunto"=>$strCaracteristicaPuntoT));
                        $strSaldoPunto           = (!empty($arraySaldoPunto["saldoTotal"])&&isset($arraySaldoPunto["saldoTotal"]))?
                                                    $arraySaldoPunto["saldoTotal"]:0;
                        //Se obtiene el tiempo de espera en meses.
                        $arrayTiempoEsperaMeses  = $emComercial->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                               ->obtieneTiempoEsperaMeses(array("intIdPunto"=>$strCaracteristicaPuntoT));
                        $strTiempoEsperaMeses  = (!empty($arrayTiempoEsperaMeses["feEsperaMeses"])&&isset($arrayTiempoEsperaMeses["feEsperaMeses"]))?
                                                  $arrayTiempoEsperaMeses["feEsperaMeses"]:0;
                    }
                    $objInfoPunto       = is_object($objIdServicio) ? $objIdServicio->getPuntoId(): "";
                    $objAdmiSector      = is_object($objInfoPunto)  ? $objInfoPunto->getSectorId(): "";
                    $objAdmiCanton      = is_object($objAdmiSector->getParroquiaId()) ? $objAdmiSector->getParroquiaId()->getCantonId(): "";
                    $strSector          = is_object($objAdmiSector) ? $objAdmiSector->getNombreSector(): "";
                    $strDireccion       = is_object($objInfoPunto) ? $objInfoPunto->getDireccion(): "";
                    $strCiudad          = is_object($objAdmiCanton) ? $objAdmiCanton->getNombreCanton(): "";
                    $arraySolicitudes[] = array('id'                  => $objItem->getId(),
                                                'servicio'            => $strProductoPlan,
                                                'cliente'             => $strCliente,
                                                'estadoClt'           => $strEstado,
                                                'asesor'              => ucwords(strtolower($strNombresCompletos)),
                                                'login'               => is_object($objInfoPunto) ? $objInfoPunto->getLogin():"",
                                                'id_punto'            => is_object($objInfoPunto) ? $objInfoPunto->getId():"",
                                                'tipoNegocio'         => is_object($objInfoPunto) ? $objInfoPunto->getTipoNegocioId()
                                                                                                                 ->getNombreTipoNegocio():"",
                                                'idsServicioTraslado' => $strServiciosTrasladar != null ? $strServiciosTrasladar:"",
                                                'motivo'              => $strNombreMotivo,
                                                'vOriginal'           => '$'.$floatValorTotal,
                                                'descuento'           => '$'.$floatPrecioDescuento,
                                                'observacion'         => $objItem->getObservacion(),
                                                'feCreacion'          => strval(date_format($objItem->getFeCreacion(), "d/m/Y G:i")),
                                                'usrCreacion'         => $objItem->getUsrCreacion(),
                                                'estadoSolicitud'     => $objItem->getEstado(),
                                                'tiempoEsperaMeses'   => $strTiempoEsperaMeses,
                                                'saldoPunto'          => $strSaldoPunto,
                                                'direccion'           => ucwords(strtolower(trim($strDireccion))),
                                                'ciudad'              => ucwords(strtolower(trim($strCiudad))),
                                                'nombreSector'        => ucwords(strtolower(trim($strSector))),);
                }
            }
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudesController->getSolicitudesAction', 
                                      $ex->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion);
        }
        if(!empty($arraySolicitudes) && is_array($arraySolicitudes))
        {
            $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                          "solicitudes"     => $arraySolicitudes,
                                                          "draw"            => $strDraw,
                                                          "recordsTotal"    => $intTotal,
                                                          "recordsFiltered" => $intTotal)));
        }
        else
        {
            $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                          "solicitudes"     => [],
                                                          "draw"            => $strDraw,
                                                          "recordsTotal"    => $intTotal,
                                                          "recordsFiltered" => $intTotal)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }

    public function gridMaterialesUtilizadosAction()
    {
        
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        
        $peticion = $this->get('request');
        
        $id_solicitud = $peticion->query->get('id_solicitud');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        
        $em = $this->getDoctrine()->getManager("telconet");
        $em_naf = $this->getDoctrine()->getManager("telconet_naf");
        
        $codEmpresa = ($peticion->getSession()->get('idEmpresa') ? $peticion->getSession()->get('idEmpresa') : "");
                
        $objJson = $this->getDoctrine()
            ->getManager("telconet_soporte")
            ->getRepository('schemaBundle:InfoDetalleMaterial')
            ->generarJsonMaterialesUtilizados($em, $em_naf, $start, $limit, $id_solicitud,$codEmpresa);
        $respuesta->setContent($objJson);
        
        return $respuesta;
    }
    
    /**
     * getJsonHistorialSolicitudAction, Obtiene el Historial de la Solicitud
     * 
     * @version 1.0 version inicial
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 17-05-2016 - Se envía como parámetro el $emSoporte a la función generarJsonHistorialDetalleSolicitud
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 17-08-2022 - Se reestructura la función.
     *
     * @return json Retorna un json del historico de la Solicitud
     */
     public function getJsonHistorialSolicitudAction(){
        $em             = $this->get('doctrine')->getManager('telconet');
        $emGeneral      = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte      = $this->get('doctrine')->getManager('telconet_soporte');
        $serviceUtil    = $this->get('schema.Util');
        $objRequest     = $this->get('request');
        $objSession     = $objRequest->getSession();
        $strIpCreacion  = $objRequest->getClientIp() ? $objRequest->getClientIp():"127.0.0.1";
        $strUsrCreacion = $objSession->get('user') ? $objSession->get('user') : '';
        $intIdSolicitud = $objRequest->query->get('idSolicitud');
        $intStart       = $objRequest->query->get('start') ? $objRequest->query->get('start'): self::VALOR_INICIAL_BUSQUEDA;
        $intLimit       = $objRequest->query->get('limit') ? $objRequest->query->get('limit'): self::VALOR_LIMITE_BUSQUEDA;
        $strDraw        = $objRequest->query->get('draw') ? $objRequest->query->get('draw'):"1";
        $intTotal       = 0;
        try
        {
            $objJsonTotal = json_decode($this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->generarJsonHistorialDetalleSolicitud($intIdSolicitud,$intStart, "", $emGeneral,$emSoporte));
            $objJson = json_decode($this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->generarJsonHistorialDetalleSolicitud($intIdSolicitud,$intStart, $intLimit, $emGeneral,$emSoporte));
        }
        catch(\Exception $ex)
        {
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudesController->getJsonHistorialSolicitudAction', 
                                      $ex->getMessage(), 
                                      $strUsrCreacion, 
                                      $strIpCreacion);
        }
        if(!empty($objJsonTotal) && !empty($objJson))
        {
            $intTotal    = $objJsonTotal->total ? $objJsonTotal->total:0;
            $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                          "encontrados"     => $objJson->encontrados,
                                                          "draw"            => $strDraw,
                                                          "recordsTotal"    => $intTotal,
                                                          "recordsFiltered" => $intTotal)));
        }
        else
        {
            $objResponse = new Response(json_encode(array("total"           => $intTotal,
                                                          "solicitudes"     => [],
                                                          "draw"            => $strDraw,
                                                          "recordsTotal"    => $intTotal,
                                                          "recordsFiltered" => $intTotal)));
        }
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    public function ajaxGetTiposSolicitudAction()
    {
	$response = new Response();
        
        $response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$tiposSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')->findByEstado('Activo');
	
	if ($tiposSolicitud) {
            $tiposSolicitudesArray = array();
            
            
            foreach ($tiposSolicitud as $tipoSolicitud)
            {
                $tiposSolicitudesArray[] = array('id_tipo_solicitud' => $tipoSolicitud->getId(),'tipo_solicitud' => $tipoSolicitud->getDescripcionSolicitud());
            }
            
            $data = '{"total":"'.count($tiposSolicitud).'","encontrados":'.json_encode($tiposSolicitudesArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    /**
     * getModelosCpeOntPorSoporte
     * 
     * Función que obtiene los modelos de Cpe Ont que pueden ser asignados en una solicitud de cambio de equipo por soporte
     * 
     * @return Response $objResponse   json Retorna un json de los modelos de Cpe Ont 
     *                                 permitidos en cambios de equipo por soporte
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 12-05-2019
     * @since 1.0
     * 
     */
    public function getModelosCpeOntPorSoporteAction()
    {
        $objJsonResponse            = new JsonResponse();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');  
        $strNombreParametro         = 'EQUIPOS_PERMITIDOS_CAMBIO_EQUIPO_POR_SOPORTE';
        $strModulo                  = 'TECNICO';
        $strProceso                 = 'VALIDACION_DE_EQUIPOS';
        $strTipoEquipos             = ($objRequest->get('strTipoEquipos'))?$objRequest->get('strTipoEquipos'):'';
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $arrayResultado             =  array ( 'arrayModelosOnt'  => array() );
        $arrayAdmiParametroDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get($strNombreParametro, 
                                                      $strModulo, 
                                                      $strProceso, 
                                                      '',
                                                      '',
                                                      $strTipoEquipos,
                                                      '',
                                                      '',
                                                      '',
                                                      $intIdEmpresa);
        
        if(is_array($arrayAdmiParametroDet) && count($arrayAdmiParametroDet) > 0)
        {
            $arrayStoreModelos = array();
            foreach($arrayAdmiParametroDet as $arrayParametro)
            {   
                $arrayStoreModelos[] = array('strValueModelo'    => $arrayParametro['valor1']);
            }
            
            $arrayResultado =  array ( 'arrayModelosOnt'  => $arrayStoreModelos );
        }
        
        $objJsonResponse->setData($arrayResultado);
        return $objJsonResponse;
    }    
    
    /**
     * getMotivosRechazoTrasladoAction
     *
     * Metodo encargado de obtener los motivos asociados a la opcion de rechazar solicitud de traslado
     *
     * @return Json $objJsonResponse motivos de rechazo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0 11-07-2018
     */
    public function getMotivosRechazoTrasladoAction()
    {
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $strRelacionSistema = "9731";
        $objJsonResponse    = new JsonResponse();
        $arrayRegistros     = array();
        $arrayRespuesta     = array();

        $objAdmiMotivos = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findBy(array("estado"            => "Activo",
                                                                                             "relacionSistemaId" => $strRelacionSistema));
        $strNumeroMotivo = count($objAdmiMotivos);
        foreach ($objAdmiMotivos as $objIdxAdmiMotivo)
        {
            $arrayRegistros[] = array('id_motivo'     => $objIdxAdmiMotivo->getId(),
                                      'nombre_motivo' => $objIdxAdmiMotivo->getNombreMotivo());
        }

        $arrayRespuesta["total"]       = $strNumeroMotivo;
        $arrayRespuesta["encontrados"] = $arrayRegistros;

        $objJsonResponse->setData($arrayRespuesta);

        return $objJsonResponse;
    }

	public function ajaxGetEstadosTiposSolicitudAction()
    {
	$response = new Response();
        
	$response->headers->set('Content-Type', 'text/json');
	$emComercial = $this->getDoctrine()->getManager();
	$estadosTiposSolicitud = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getEstados();
	
	if ($estadosTiposSolicitud) {
            $estadosTiposSolicitudArray = array();
            
            
            foreach ($estadosTiposSolicitud as $estadoTiposSolicitud)
            {
                $estadosTiposSolicitudArray[] = array('estado_tipo_solicitud' => $estadoTiposSolicitud['estado']);
            }
            
            $data = '{"total":"'.count($estadosTiposSolicitud).'","encontrados":'.json_encode($estadosTiposSolicitudArray).'}';
            
        }
        else
        {
            $data = '{"total":"0","encontrados":[]}';
        }
        
        $response->setContent($data);
        
        return $response;
        
    }
    
    /**
     * Documentación para el método 'solicitudesPuntoAction'.
     * Metodo que muestra las solicitudes del punto que se encuentra en sesion
     *
     * @return object render
     *
     * @author amontero@telconet.ec
     * @version 1.0 25-06-2015
     */
    /**
    * @Secure(roles="ROLE_286-2697")
    */    
    public function solicitudesPuntoAction()
    {
        $request           = $this->getRequest();
        $session           = $request->getSession();
        $arrCliente        = $session->get('cliente');
        $puntoIdSesion     = null;
        $ptoClienteSesion  = $session->get('ptoCliente');
        $strRol            = null;
        if($ptoClienteSesion)
        {  
            $puntoIdSesion = $ptoClienteSesion['id'];
        }
        if( $arrCliente )
        {
            $strRol = $arrCliente['nombre_tipo_rol'];
        }        
        return $this->render('comercialBundle:solicitudes:solicitudesPunto.html.twig', array(
             'puntoId' =>$puntoIdSesion,
             'rol'     =>$strRol
        ));
    }
    
    /**
     * Documentación para el método 'gridSolicitudesPuntoAction'.
     * Metodo que presenta grid con informacion de las solicitudes del punto en sesion
     *
     * @return object response
     *
     * @author amontero@telconet.ec
     * @version 1.2 17-02-2016
     */
    /**
    * @Secure(roles="ROLE_286-2697")
    */     
    public function gridSolicitudesPuntoAction()
    {
        $request          = $this->get('request');
        $session          = $request->getSession();
        $limit            = $request->get("limit");
        $start            = $request->get("start");
		$idPunto          = null;
        $ptoClienteSesion = $session->get('ptoCliente');
        if($ptoClienteSesion)
        {  
            $idPunto = $ptoClienteSesion['id'];
        }        
        $em         = $this->get('doctrine')->getManager('telconet');
        $resultado  = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->getSolicitudPorPuntoPorEstado(
            $start,
            $limit,
            $idPunto, 
            array('Anulada','Anulado','Rechazado','Rechazada','Eliminado')
        );
        $datos = $resultado['registros'];
        $total = $resultado['total'];

        for($indiceDebitos=0;$indiceDebitos<count($datos);$indiceDebitos++)
        {
            $linkVer   = '#';
            $descuento = '';
            if($datos[$indiceDebitos]['precioDescuento'])
            {    
                $descuento = '$'.$datos[$indiceDebitos]['precioDescuento'];
            }    
            elseif($datos[$indiceDebitos]['porcentajeDescuento'])
            {    
                $descuento = $datos[$indiceDebitos]['porcentajeDescuento'] . '%';
            }
            else
            {
                $descuento = '0%';
            }
            $producto = '';
            if($datos[$indiceDebitos]['descripcionProducto'])
            {
                $producto       = $datos[$indiceDebitos]['descripcionProducto'];
            }
            elseif($datos[$indiceDebitos]['nombrePlan'])
            {
                $producto       = $datos[$indiceDebitos]['nombrePlan'];
            }
            $muestraBotonAnular='N';
            $muestraBotonFinalizar='N';
            //EL ICONO PARA ANULACION SOLO SE MUESTRA SI ES SOLICITUD DE DESCUENTO CON ESTADO PENDIENTE O
            //SI ES SOLICITUD DE CAMBIO DE EQUIPO CON ESTADO PrePlanificada o AsignadoTarea
            if (  (strtoupper($datos[$indiceDebitos]['descripcionSolicitud'])=='SOLICITUD DESCUENTO' 
                && strtoupper($datos[$indiceDebitos]['estado'])=='PENDIENTE')
                ||(strtoupper($datos[$indiceDebitos]['descripcionSolicitud'])=='SOLICITUD CAMBIO EQUIPO'
                && (strtoupper($datos[$indiceDebitos]['estado'])=='PREPLANIFICADA' || strtoupper($datos[$indiceDebitos]['estado'])=='ASIGNADOTAREA'))
                ||(strtoupper($datos[$indiceDebitos]['descripcionSolicitud'])=='SOLICITUD CAMBIO DE MODEM INMEDIATO' 
                && strtoupper($datos[$indiceDebitos]['estado'])=='ASIGNADOTAREA'))         
            {
                $muestraBotonAnular='S';
            }
            //EL ICONO PARA FINALIZAR SOLO SE MUESTRA SI ES SOLICITUD DE DESCUENTO CON ESTADO PENDIENTE
            if (  (strtoupper($datos[$indiceDebitos]['descripcionSolicitud'])=='SOLICITUD DESCUENTO' 
                && strtoupper($datos[$indiceDebitos]['estado'])=='APROBADO'))         
            {
                $muestraBotonFinalizar='S';
            }                
            $arreglo[] = array(
                'id'                    => $datos[$indiceDebitos]['id'],
                'servicio'              => $producto,
                'login'                 => $datos[$indiceDebitos]['login'],
                'descripcionSolicitud'  => $datos[$indiceDebitos]['descripcionSolicitud'], 
                'motivo'                => $datos[$indiceDebitos]['nombreMotivo'],
                'descuento'             => $descuento,
                'observacion'           => $datos[$indiceDebitos]['observacion'],
                'feCreacion'            => $datos[$indiceDebitos]['feCreacion'],
                'usrCreacion'           => $datos[$indiceDebitos]['usrCreacion'],
                'estado'                => $datos[$indiceDebitos]['estado'],  
                'linkVer'               => $linkVer,
                'muestraBotonAnular'    => $muestraBotonAnular,
                'muestraBotonFinalizar' => $muestraBotonFinalizar,
                'cantidadMateriales'    => $datos[$indiceDebitos]['cantidadMateriales']
            );
        }
        if(!empty($arreglo))
        {   
            $response = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));
        }    
        else
        {
            $arreglo[] = array();
            $response  = new Response(json_encode(array('total' => $total, 'solicitudes' => $arreglo)));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }   
    
    /**
     * Documentación para el método 'getMotivosAnulacionAction'.
     * Metodo que muestra los motivos de anulacion para solicitudes del punto en sesion
     *
     * @return object response
     *
     * @author amontero@telconet.ec
     * @version 1.0 25-06-2015
     */
    /**
    * @Secure(roles="ROLE_288-2699")
    */     
    public function getMotivosAnulacionAction() 
    {    
        $em      = $this->get('doctrine')->getManager('telconet');
	    $arreglo = array();        
        $datos   = $em->getRepository('schemaBundle:AdmiMotivo')
            ->findMotivosPorModuloPorItemMenuPorAccion('anular_solicitud_punto','','anularSolicitudPunto');

        foreach($datos as $valor)
        {    
            $arreglo[] = array(
                'idMotivo'         => $valor->getId(),
                'descripcion'      => $valor->getNombreMotivo(),
                'idRelacionSistema'=> $valor->getRelacionSistemaId()
            );
        }
        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    
    /**
     * Documentación para el método 'anularSolicitudPuntoAction'.
     * Metodo que anula la solicitud seleccionada del punto
     *
     * @return object response
     *
     * @author amontero@telconet.ec
     * @version 1.0 01-07-2015
     */
    /**
    * @Secure(roles="ROLE_287-2698")
    */ 
    public function anularSolicitudPuntoAction()
    {
        $request     = $this->getRequest();
        $session     = $request->getSession();         
        $usrCreacion = $session->get('user');		
        $respuesta   = new Response();
        $objDatos     = ''; 
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion    = $this->get('request');
        
        $params['intIdMotivo']    = $peticion->get('idMotivo');
        $params['strObservacion'] = $peticion->get('observacion');
        $params['strIdSolicitud'] = $peticion->get('idSolicitud');
        $params['strAccion']      = 'anular';
        $params['strIpCreacion']  = $request->getClientIp();
        $params['strUsrCreacion'] = $usrCreacion;
        $serviceSolicitudes       = $this->get('comercial.Solicitudes');
        
        $objDatos = $serviceSolicitudes->anulaFinalizaSolicitud($params);
        
       $respuesta->setContent($objDatos); 
       return $respuesta;
    }    
    
     /**
     * Documentación para el método 'getMotivosAnulacionAction'.
     * Metodo que muestra los motivos de anulacion para solicitudes del punto en sesion
     *
     * @return object response
     *
     * @author amontero@telconet.ec
     * @version 1.0 17-02-2015
     */
    /**
    * @Secure(roles="ROLE_336-3518")
    */     
    public function getMotivosFinalizarAction() 
    {    
        $em      = $this->get('doctrine')->getManager('telconet');
        $arreglo = array();        
        $datos   = $em->getRepository('schemaBundle:AdmiMotivo')
            ->findMotivosPorModuloPorItemMenuPorAccion('finalizar_solicitud_punto','','finalizarSolicitudPunto');
        foreach($datos as $valor)
        {    
            $arreglo[] = array(
                'idMotivo'         => $valor->getId(),
                'descripcion'      => $valor->getNombreMotivo(),
                'idRelacionSistema'=> $valor->getRelacionSistemaId()
            );
        }
        $response = new Response(json_encode(array('motivos' => $arreglo)));
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
    /**
     * Documentación para el método 'finalizarSolicitudPuntoAction'.
     * Metodo que finaliza la solicitud seleccionada del punto
     *
     * @return object response
     *
     * @author amontero@telconet.ec
     * @version 1.0 17-02-2016
     */
    /**
    * @Secure(roles="ROLE_335-3517")
    */ 
    public function finalizarSolicitudPuntoAction()
    {
        $request     = $this->getRequest();
        $session     = $request->getSession();         
        $usrCreacion = $session->get('user');		
        $respuesta   = new Response();
        $objDatos     = ''; 
        $respuesta->headers->set('Content-Type', 'text/plain');        
        $respuesta->setContent("error del Form");  
        $em = $this->getDoctrine()->getManager('telconet');

        //Obtiene parametros enviados desde el ajax
        $peticion    = $this->get('request');
        
        $params['intIdMotivo']    = $peticion->get('idMotivo');
        $params['strObservacion'] = $peticion->get('observacion');
        $params['strIdSolicitud'] = $peticion->get('idSolicitud');
        $params['strAccion']      = 'finalizar';
        $params['strIpCreacion']  = $request->getClientIp();
        $params['strUsrCreacion'] = $usrCreacion;
        $serviceSolicitudes       = $this->get('comercial.Solicitudes');
        $objDatos                 = $serviceSolicitudes->anulaFinalizaSolicitud($params);
        
       $respuesta->setContent($objDatos); 
       return $respuesta;
    }     
    
    /**
    * Documentación para la funcion getMaterialesPuntoAction().
    * 
    * Esta funcion es la encargada de llenar el grid de la consulta.
    * 
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.0 07-09-2015
    * 
    */    
    public function getMaterialesPuntoAction()
    {
       
        $strRespuesta        = new Response();
        $strRespuesta        ->headers->set('Content-Type', 'text/json');        
        $strPeticion         = $this->get('request');        
        $strIdSolicitud      = $strPeticion->query->get('idSolicitud');
        $strStart            = $strPeticion->query->get('start');
        $strLimit            = $strPeticion->query->get('limit');           
        $arrayParametros     = array();

        $arrayParametros["start"]            = $strStart;
        $arrayParametros["limit"]            = $strLimit;   
        $arrayParametros["idSolicitud"]      = $strIdSolicitud;   
        
        $emComercial                         = $this->getDoctrine()->getManager("telconet");
        $objJson                             = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                           ->generarJsonMaterialesPunto($arrayParametros);        
        $strRespuesta  ->setContent($objJson);
        
        return $strRespuesta;
    }

    /**
     * getTipoDescuentoAction, Obtiene los tipos de descuentos para las Solicitudes de Descuento 
     *                         y Solicitud de Descuento unico, segun la empresa en sesion
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 02-05-2017
     * @return json Retorna un json de los tipos de Descuentos de las Solicitudes de Descuento y Solicitud
     *              de Descuento unico.
     */
    public function getTipoDescuentoAction()
    {
        $objResponse                   = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');  
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        $strNombreParametro         = 'DESCUENTOS_FACTURAS';
        $strModulo                  = 'COMERCIAL';
        $strProceso                 = 'FACTURACION';
        $strValueSelected           = '';
        $strTipoSolicitud           = $objRequest->get('strTipoSolicitud');
        
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');

        $arrayAdmiParametroDet      = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get($strNombreParametro, 
                                                      $strModulo, 
                                                      $strProceso, 
                                                      '',
                                                      '',
                                                      '',
                                                      $strTipoSolicitud,
                                                      '',
                                                      $strPrefijoEmpresa,
                                                      $intIdEmpresa);
        
        $strJsonResult = json_encode(array('arrayTipoDescuento'  => []));

        if($arrayAdmiParametroDet && count($arrayAdmiParametroDet) > 0)
        {
            $arrayStoreTipoDescuento    = array();
            foreach($arrayAdmiParametroDet as $arrayParametro)
            {   
                $strValueSelected          = ('SELECT' === $arrayParametro['valor4']) ? $arrayParametro['valor2'] : '';
                $arrayStoreTipoDescuento[] = array( 'strDisplayTipoDescuento'  => $arrayParametro['valor1'] , 
                                                    'strValueTipoDescuento'    => $arrayParametro['valor2'],
                                                    'strValueSelected'         => $strValueSelected);
                
                
                

            }//( $arrayAdmiParametroDet as $arrayParametro )
            
            $strJsonResult =  json_encode(array ( 'arrayTipoDescuento'  => $arrayStoreTipoDescuento ));
        }//( $arrayAdmiParametroDet )
        
        $objResponse->setContent((string) $strJsonResult);
        return $objResponse;
    }

    /**
     * Función que obtiene los puntos asociados a una solicitud de servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-10-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-02-2019 Se modifica el nombre de la característica usada para los servicios TELCOHOME
     * 
     * @return JsonResponse
     */
    public function getInfoSolicitudesServicioAction()
    {
        $objResponse                = new JsonResponse();
        $objRequest                 = $this->get('request');
        $emComercial                = $this->getDoctrine()->getManager("telconet");
        $intIdDetalleSolicitud      = $objRequest->get('idSolicitud');
        $intStart                   = $objRequest->query->get('start');
	    $intLimit                   = $objRequest->query->get('limit');	
        $arrayParamsSolicitud       = array(
                                            "intIdDetalleSolicitud"         => $intIdDetalleSolicitud,
                                            "strEstadoSolicitud"            => "Pendiente",
                                            "strDescripcionCaracteristica"  => "VELOCIDAD_TELCOHOME",
                                            "strConServicio"                => "SI",
                                            "strBuscarServiciosAsociados"   => "SI",
                                            "strTieneEstadoServicioSol"     => "SI",
                                            "intStart"                      => $intStart,
                                            "intLimit"                      => $intLimit);  

        $strJsonDataSolicitud       = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                  ->getJSONSolicitudesPorDetSolCaracts($arrayParamsSolicitud);
        $objResponse->setContent($strJsonDataSolicitud);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_420-6117")
     * 
     * Función que aprueba o rechaza una solicitud de servicio de acuerdo a los parámetros enviados
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-10-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-02-2019 Se guarda valor ingresado por el usuario con el número total de cuentas TelcoHome contratadas por el cliente 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 15-10-2019 Se agrega el return respectivo, borrado por resolución de conflictos erróneo de otro pase(MR 6838 CloudForm)
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 15-10-2019 Se elimina programación para rechazo de ips telcohome, ya que este producto no tiene flujo válido, ya que su servicio
     *                          preferencial es un HOME
     * 
     */
    public function gestionarSolicitudesServicioAction()
    {
        $objResponse                = new JsonResponse();
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $objRequest                 = $this->getRequest();
        $intIdSolicitud             = $objRequest->get("idSolicitud");
        $strObservacion             = $objRequest->get("observacion");
        $strAccion                  = $objRequest->get("accion");
        $objSession                 = $objRequest->getSession();
        $strIpClient                = $objRequest->getClientIp();
        $strUsrSesion               = $objSession->get('user');
        $strCodEmpresa              = $objSession->get('idEmpresa');
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        $serviceUtil                = $this->get('schema.Util');
        $serviceInfoServicio        = $this->get('comercial.InfoServicio');
        $serviceEnvio               = $this->get('soporte.EnvioPlantilla');
        $serviceServicioTecnico     = $this->get('tecnico.InfoServicioTecnico');
        $boolMostrarMsjUser         = false;
        $intTotalServiciosTelcoHome = 0;
        $emComercial->beginTransaction();
        try
        {
            if($intIdSolicitud > 0 && !empty($strAccion))
            {
                $objSolicitudServicio   = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
                if(!is_object($objSolicitudServicio))
                {
                    $boolMostrarMsjUser = true;
                    throw new \Exception("No se ha podido obtener el objeto de la solicitud con id: ".$intIdSolicitud);
                }
                $objServicioSolicitud   = $objSolicitudServicio->getServicioId();
                if(!is_object($objServicioSolicitud))
                {
                    $boolMostrarMsjUser = true;
                    throw new \Exception("No se ha podido obtener el objeto del servicio asociado a la solicitud ".$intIdSolicitud);
                }
                $objServicio        = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objServicioSolicitud->getId());
                $objPunto           = $objServicio->getPuntoId();
                $objPerCliente      = $objPunto->getPersonaEmpresaRolId(); 
                $objProducto        = $objServicio->getProductoId();
                $strNombreProducto  = $objProducto->getDescripcionProducto();
                
                if($strAccion === "aprobar")
                {
                    $arrayNumMinServiciosTelcohome  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne( 'NUM_MIN_SERVICIOS_TELCOHOME', 
                                                                          '', 
                                                                          '', 
                                                                          '', 
                                                                          '',
                                                                          '', 
                                                                          '', 
                                                                          '', 
                                                                          '', 
                                                                          $strCodEmpresa);
                    if(!empty($arrayNumMinServiciosTelcohome) && intval($arrayNumMinServiciosTelcohome['valor1']) > 0)
                    {
                        $intNumMinServiciosTelcoHome        = intval($arrayNumMinServiciosTelcohome['valor1']);
                        $arrayParamsServiciosTelcoHome      = array(
                                                                    "intIdDetalleSolicitud"         => $intIdSolicitud,
                                                                    "strEstadoSolicitud"            => "Pendiente",
                                                                    "strDescripcionCaracteristica"  => "VELOCIDAD_TELCOHOME",
                                                                    "strConServicio"                => "SI",
                                                                    "strBuscarServiciosAsociados"   => "SI",
                                                                    "strTieneEstadoServicioSol"     => "SI");  

                        $arrayRespuestaServiciosTelcoHome   = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                          ->getSolicitudesPorDetSolCaracts($arrayParamsServiciosTelcoHome);
                        $intTotalServiciosIngrTelcoHome     = $arrayRespuestaServiciosTelcoHome['intTotal'];
                        if($intTotalServiciosIngrTelcoHome < $intNumMinServiciosTelcoHome)
                        {
                            $boolMostrarMsjUser = true;
                            throw new \Exception("No se han ingresado las ".$intNumMinServiciosTelcoHome." cuentas mínimas necesarias para gestionar".
                                                 " esta solicitud. Actualmente existen ".$intTotalServiciosIngrTelcoHome. " cuentas ingresadas.");
                        }
                    }
                    else
                    {
                        $boolMostrarMsjUser = true;
                        throw new \Exception("No se ha podido obtener el parámetro con el número mínimo de cuentas TelcoHome");
                    }
                }
                $objSpcVelocidad    = $serviceServicioTecnico->getServicioProductoCaracteristica(   $objServicioSolicitud,
                                                                                                    'VELOCIDAD_TELCOHOME',
                                                                                                    $objServicioSolicitud->getProductoId()
                                                                                                );
                if(is_object($objSpcVelocidad))
                {
                    $strVelocidadIsb = $objSpcVelocidad->getValor();
                }
                else
                {
                    $boolMostrarMsjUser = true;
                    throw new \Exception("No se encuentra asociada la característica VELOCIDAD_TELCOHOME al servicio");
                }
                
                $objPersonaGestion = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($strUsrSesion);
                if(!is_object($objPersonaGestion))
                {
                    throw new \Exception("No se ha podido obtener el objeto de la persona en sesión");
                }
                $strNombreUsuarioGestion = sprintf("%s", $objPersonaGestion);
                if($strAccion === "aprobar")
                {
                    $strNuevoEstadoSolicitud        = "Aprobada";
                    $strNuevoEstadoServicio         = "Pre-servicio";
                    $strAccionMail                  = "la aprobación";
                    $strAccionAsunto                = "APROBACION";
                    $strAccionUsuario               = "aprobada";
                    $strNumTotalServiciosTelcoHome  = $objRequest->get("numTotalServiciosTelcoHome");
                    if(intval($strNumTotalServiciosTelcoHome) > 0)
                    {
                        $objCaractNumTotalServTelcoHome = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                      ->findOneBy(array('descripcionCaracteristica'     => 
                                                                                        'NUM_TOTAL_SERVICIOS_TELCOHOME',
                                                                                        'estado'                        =>
                                                                                        'Activo'));
                        if(is_object($objCaractNumTotalServTelcoHome))
                        {
                            $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                            $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objPerCliente);
                            $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objCaractNumTotalServTelcoHome);
                            $objInfoPersonaEmpresaRolCarac->setValor($strNumTotalServiciosTelcoHome);
                            $objInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                            $objInfoPersonaEmpresaRolCarac->setUsrCreacion($strUsrSesion);
                            $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpClient);
                            $objInfoPersonaEmpresaRolCarac->setEstado('Activo');
                            $emComercial->persist($objInfoPersonaEmpresaRolCarac);
                            $emComercial->flush();
                        }
                        else
                        {
                            $boolMostrarMsjUser = true;
                            throw new \Exception("No existe la característica NUM_TOTAL_SERVICIOS_TELCOHOME");
                        }
                    }
                    else
                    {
                        $boolMostrarMsjUser = true;
                        throw new \Exception("No se ha ingresado correctamente el número total de cuentas del cliente");
                    }
                }
                else if($strAccion === "rechazar")
                {
                    $strNuevoEstadoSolicitud    = "Rechazada";
                    $strNuevoEstadoServicio     = "Rechazada";
                    $strAccionMail              = "el rechazo";
                    $strAccionAsunto            = "RECHAZO";
                    $strAccionUsuario           = "rechazada";
                }
                else
                {
                    $boolMostrarMsjUser = true;
                    throw new \Exception("No existe flujo para la acción enviada: ".$strAccion);
                }
                $strNombreCompletoVendedor  = "";
                $strNombreCompletoSubgerente= "";
                $arrayLoginesDestinatarios  = array();
                
                $strEstadoPendiente         = "Pendiente";
                $arrayParamsSolicitud       = array(
                                                    "intIdDetalleSolicitud"         => $intIdSolicitud,
                                                    "strEstadoSolicitud"            => $strEstadoPendiente,
                                                    "strDescripcionCaracteristica"  => "VELOCIDAD_TELCOHOME",
                                                    "strConServicio"                => "SI",
                                                    "strBuscarServiciosAsociados"   => "SI",
                                                    "strTieneEstadoServicioSol"     => "SI");
            
                $arrayRespuestaServicios    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                          ->getSolicitudesPorDetSolCaracts($arrayParamsSolicitud);
                $intTotalServiciosTelcoHome = $arrayRespuestaServicios["intTotal"];
                $arrayServiciosTelcoHome    = $arrayRespuestaServicios["arrayResultado"];
                
                if($intTotalServiciosTelcoHome > 0)
                {
                    foreach($arrayServiciosTelcoHome as $arrayServicioTelcoHome)
                    {
                        $intIdServicioAsociado  = $arrayServicioTelcoHome["idServicioAsociado"];
                        $objServicioAsociado    = $emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioAsociado);
                        if(is_object($objServicioAsociado))
                        {
                            $objServicioAsociado->setEstado($strNuevoEstadoServicio);
                            $emComercial->persist($objServicioAsociado);
                            $emComercial->flush();
                            
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicioAsociado);
                            $objServicioHistorial->setObservacion($strObservacion);
                            $objServicioHistorial->setIpCreacion($strIpClient);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setUsrCreacion($strUsrSesion);
                            $objServicioHistorial->setEstado($strNuevoEstadoServicio);
                            $emComercial->persist($objServicioHistorial);
                            $emComercial->flush();
                            
                            $arrayLoginesDestinatarios[]        = $objServicioAsociado->getUsrVendedor();
                            $arrayLoginesDestinatarios[]        = $objServicioAsociado->getUsrCreacion();
                            $arrayResultadoVendedorSubgerente   = $serviceInfoServicio->getInfoVendedorSubgerente(
                                                                                                    array(  "strIpClient"   => $strIpClient,
                                                                                                            "strUsrCreacion"=> $strUsrSesion,
                                                                                                            "strCodEmpresa" => $strCodEmpresa,
                                                                                                            "strLimite"     => 1, 
                                                                                                            "objServicio"   => $objServicioAsociado));
                            if($arrayResultadoVendedorSubgerente["strStatus"] === "OK" 
                                && !empty($arrayResultadoVendedorSubgerente["arrayInfoVendedorSubg"]["vendedor"])
                                && !empty($arrayResultadoVendedorSubgerente["arrayInfoVendedorSubg"]["subgerente"]))
                            {
                                $arrayInfoVendedorSubg          = $arrayResultadoVendedorSubgerente["arrayInfoVendedorSubg"];
                                if($objServicioSolicitud->getId() === $objServicioAsociado->getId())
                                {
                                    $strNombreCompletoVendedor      = $arrayInfoVendedorSubg["vendedor"]["nombreCompleto"];
                                    $strNombreCompletoSubgerente    = $arrayInfoVendedorSubg["subgerente"]["nombreCompleto"];
                                }
                                $arrayLoginesDestinatarios[] = $arrayInfoVendedorSubg["subgerente"]["login"];
                            }
                        }
                    }
                }
                $objSolicitudServicio->setEstado($strNuevoEstadoSolicitud);
                $emComercial->persist($objSolicitudServicio);
                $emComercial->flush();
                
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitudServicio);
                $objSolicitudHistorial->setUsrCreacion($strUsrSesion);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setIpCreacion($strIpClient);
                $objSolicitudHistorial->setEstado($strNuevoEstadoSolicitud);
                $objSolicitudHistorial->setObservacion($strObservacion);        
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();
                
                $arraySolCaracts    = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                  ->findBy(array(   "detalleSolicitudId"  => $intIdSolicitud, 
                                                                    "estado"              => $strEstadoPendiente));
                foreach($arraySolCaracts as $objDetalleSolCaract)
                {
                    $objDetalleSolCaract->setEstado($strNuevoEstadoSolicitud);
                    $emComercial->persist($objDetalleSolCaract);
                    $emComercial->flush();
                }
            }
            else
            {
                $boolMostrarMsjUser = true;
                throw new \Exception("No se han enviado todos los parámetros necesarios");
            }
            $arrayDestinatarios         = array();
            $arrayLoginesDestinatarios  = array_unique($arrayLoginesDestinatarios);
            foreach($arrayLoginesDestinatarios as $strLoginDestinatario)
            {
                if(!empty($strLoginDestinatario))
                {
                    $arrayCorreosDestinatarios  = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                              ->getContactosByLoginPersonaAndFormaContacto( $strLoginDestinatario,
                                                                                                            'Correo Electronico');
                    if(!empty($arrayCorreosDestinatarios))
                    {
                        foreach($arrayCorreosDestinatarios as $arrayCorreoDestinatario)
                        {
                            if($arrayCorreoDestinatario && !empty($arrayCorreoDestinatario['valor']))
                            {
                                $arrayDestinatarios[] = $arrayCorreoDestinatario['valor'];
                            }
                        }
                    }
                }
            }
            $objPunto                   = $objServicioSolicitud->getPuntoId();
            $strLoginPunto              = $objPunto->getLogin();
            $strDireccionPunto          = $objPunto->getDireccion();
            $objJurisdiccionPunto       = $objPunto->getPuntoCoberturaId();
            $strNombreJurisdiccionPunto = $objJurisdiccionPunto->getNombreJurisdiccion();
            $objPerCliente              = $objPunto->getPersonaEmpresaRolId();
            $objPersonaCliente          = $objPerCliente->getPersonaId();
            $strCliente                 = sprintf("%s",$objPersonaCliente);
            $strNombreTipoOrdenServicio = "";
            $strTipoOrdenServicio       = $objServicioSolicitud->getTipoOrden();
            $strDescripcionProducto     = $objServicioSolicitud->getProductoId()->getDescripcionProducto();
            if($strTipoOrdenServicio === 'T')
            {
                $strNombreTipoOrdenServicio = "Traslado";
            }
            else if($strTipoOrdenServicio=='N')
            {
                $strNombreTipoOrdenServicio = "Nueva";
            }
            $arrayParametrosMail    = array( 
                                            "accionMail"            => $strAccionMail,
                                            "accionUsuario"         => $strAccionUsuario,
                                            "nombreUsuarioGestion"  => $strNombreUsuarioGestion,
                                            "cliente"               => $strCliente,
                                            "loginPuntoCliente"     => $strLoginPunto,
                                            "nombreJurisdiccion"    => $strNombreJurisdiccionPunto,
                                            "direccionPuntoCliente" => $strDireccionPunto,
                                            "nombreProducto"        => $strNombreProducto,
                                            "descripcionProducto"   => $strDescripcionProducto,
                                            "observacion"           => $strObservacion,
                                            "tipoSolicitud"         => $objSolicitudServicio->getTipoSolicitudId()->getDescripcionSolicitud(),
                                            "estadoSolicitud"       => $objSolicitudServicio->getEstado(),
                                            "estadoServicio"        => $objServicioSolicitud->getEstado(),
                                            "prefijoEmpresa"        => $strPrefijoEmpresa,
                                            "fechaCreacionServicio" => 
                                            strval(date_format($objServicioSolicitud->getFeCreacion(), "d-m-Y")),
                                            "tipoOrden"             => $strNombreTipoOrdenServicio,
                                            "velocidadIsb"          => $strVelocidadIsb,
                                            "vendedor"              => $strNombreCompletoVendedor,
                                            "subgerente"            => $strNombreCompletoSubgerente,
                                            "numCuentasIngresadas"  => $intTotalServiciosTelcoHome
                                    );

            $serviceEnvio->generarEnvioPlantilla(   $strAccionAsunto." SOLICITUD DE AUTORIZACION ".$strNombreProducto, 
                                                    array_unique($arrayDestinatarios), 
                                                    'APRB_RCHZ_SOLSB', 
                                                    $arrayParametrosMail,
                                                    $strCodEmpresa,
                                                    '',
                                                    '',
                                                    null, 
                                                    true,
                                                    'notificaciones_telcos@telconet.ec');
            $strStatus  = "OK";
            $strMensaje = "Se procesó la solicitud exitosamente!";
            $emComercial->commit();
            
        }
        catch (\Exception $e) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            $emComercial->close();
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudesController->gestionarSolicitudesServicioAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient
                                     );
            $strStatus  = "ERROR";
            if($boolMostrarMsjUser)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Existieron problemas al ejecutar transacción, notificar a Sistemas";
            }
        }
        $arrayRespuesta = array('strStatus'     => $strStatus,
                                'strMensaje'    => $strMensaje);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }
    /**
     * @Secure(roles="ROLE_440-6857")
     * 
     * Documentación para la función 'gestionarSolicitudesServicioMplsAction'.
     * 
     * Función que aprueba o rechaza una solicitud de servicio con tipo de red MPLS de acuerdo a los parámetros enviados
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 14-10-2019
     * 
     */
    public function gestionarSolicitudesServicioMplsAction()
    {
        $objResponse               = new JsonResponse();
        $emComercial               = $this->get('doctrine')->getManager('telconet');
        $objRequest                = $this->getRequest();
        $intIdSolicitud            = $objRequest->get("idSolicitud");
        $strObservacion            = $objRequest->get("observacion");
        $strAccion                 = $objRequest->get("accion");
        $objSession                = $objRequest->getSession();
        $strIpClient               = $objRequest->getClientIp();
        $strUsrSesion              = $objSession->get('user');
        $strCodEmpresa             = $objSession->get('idEmpresa');
        $strPrefijoEmpresa         = $objSession->get('prefijoEmpresa');
        $serviceUtil               = $this->get('schema.Util');
        $serviceInfoServicio       = $this->get('comercial.InfoServicio');
        $serviceEnvio              = $this->get('soporte.EnvioPlantilla');
        $serviceServicioTecnico    = $this->get('tecnico.InfoServicioTecnico');
        try
        {
            if(!empty($intIdSolicitud) && !empty($strAccion))
            {
                $emComercial->beginTransaction();
                $objSolicitudServicio = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
                if(!is_object($objSolicitudServicio) || empty($objSolicitudServicio))
                {
                    throw new \Exception("No se ha podido obtener el objeto de la solicitud con id: ".$intIdSolicitud);
                }
                $objServicioSolicitud = $objSolicitudServicio->getServicioId();
                if(!is_object($objServicioSolicitud) || empty($objServicioSolicitud))
                {
                    throw new \Exception("No se ha podido obtener el objeto del servicio asociado a la solicitud ".$intIdSolicitud);
                }
                $objPersonaGestion = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($strUsrSesion);
                if(!is_object($objPersonaGestion) || empty($objPersonaGestion))
                {
                    throw new \Exception("No se ha podido obtener el objeto de la persona en sesión");
                }
                $strNombreUsuarioGestion = sprintf("%s", $objPersonaGestion);
                $objServicio             = $emComercial->getRepository('schemaBundle:InfoServicio')->find($objServicioSolicitud->getId());
                $objPunto                = $objServicio->getPuntoId();
                $objPerCliente           = $objPunto->getPersonaEmpresaRolId(); 
                $objProducto             = $objServicio->getProductoId();
                $strNombreProducto       = $objProducto->getDescripcionProducto();

                if($strAccion === "aprobar")
                {
                    $strNuevoEstadoSolicitud = "Aprobada";
                    $strNuevoEstadoServicio  = "Pre-servicio";
                    $strAccionMail           = "la aprobación";
                    $strAccionAsunto         = "APROBACION";
                    $strAccionUsuario        = "aprobada";
                }
                else if($strAccion === "rechazar")
                {
                    $strNuevoEstadoSolicitud = "Rechazada";
                    $strNuevoEstadoServicio  = "Eliminado";
                    $strAccionMail           = "el rechazo";
                    $strAccionAsunto         = "RECHAZO";
                    $strAccionUsuario        = "rechazada";
                }
                else
                {
                    throw new \Exception("No existe flujo para la acción enviada: ".$strAccion);
                }
                $strNombreCompletoVendedor   = "";
                $strNombreCompletoSubgerente = "";
                $arrayLoginesDestinatarios   = array();
                if(is_object($objServicio) && !empty($objServicio))
                {
                    $objServicio->setEstado($strNuevoEstadoServicio);
                    $emComercial->persist($objServicio);
                    $emComercial->flush();
                    $arrayLoginesDestinatarios[]      = $objServicio->getUsrVendedor();
                    $arrayLoginesDestinatarios[]      = $objServicio->getUsrCreacion();
                    $arrayResultadoVendedorSubgerente = $serviceInfoServicio->getInfoVendedorSubgerente(array("strIpClient"   => $strIpClient,
                                                                                                              "strUsrCreacion"=> $strUsrSesion,
                                                                                                              "strCodEmpresa" => $strCodEmpresa,
                                                                                                              "strLimite"     => 1, 
                                                                                                              "objServicio"   => $objServicio));
                    if($arrayResultadoVendedorSubgerente["strStatus"] === "OK" 
                        && !empty($arrayResultadoVendedorSubgerente["arrayInfoVendedorSubg"]["vendedor"])
                        && !empty($arrayResultadoVendedorSubgerente["arrayInfoVendedorSubg"]["subgerente"]))
                    {
                        $arrayInfoVendedorSubg      = $arrayResultadoVendedorSubgerente["arrayInfoVendedorSubg"];
                        $strNombreCompletoVendedor   = $arrayInfoVendedorSubg["vendedor"]["nombreCompleto"];
                        $strNombreCompletoSubgerente = $arrayInfoVendedorSubg["subgerente"]["nombreCompleto"];
                        $arrayLoginesDestinatarios[] = $arrayInfoVendedorSubg["subgerente"]["login"];
                    }
                }
                $objSolicitudServicio->setEstado($strNuevoEstadoSolicitud);
                $emComercial->persist($objSolicitudServicio);
                $emComercial->flush();

                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitudServicio);
                $objSolicitudHistorial->setUsrCreacion($strUsrSesion);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setIpCreacion($strIpClient);
                $objSolicitudHistorial->setEstado($strNuevoEstadoSolicitud);
                $objSolicitudHistorial->setObservacion($strObservacion);
                $emComercial->persist($objSolicitudHistorial);
                $emComercial->flush();

                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacion);
                $objServicioHistorial->setIpCreacion($strIpClient);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($strUsrSesion);
                $objServicioHistorial->setEstado($strNuevoEstadoServicio);
                $emComercial->persist($objServicioHistorial);
                $emComercial->flush();
            }
            else
            {
                throw new \Exception("No se han enviado todos los parámetros necesarios");
            }
            $arrayDestinatarios        = array();
            $arrayLoginesDestinatarios = array_unique($arrayLoginesDestinatarios);
            foreach($arrayLoginesDestinatarios as $strLoginDestinatario)
            {
                if(!empty($strLoginDestinatario))
                {
                    $arrayCorreosDestinatarios = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                             ->getContactosByLoginPersonaAndFormaContacto( $strLoginDestinatario,
                                                                                                          'Correo Electronico');
                    if(!empty($arrayCorreosDestinatarios) && is_array($arrayCorreosDestinatarios))
                    {
                        foreach($arrayCorreosDestinatarios as $arrayItemCorreoDestinatario)
                        {
                            if(!empty($arrayItemCorreoDestinatario['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItemCorreoDestinatario['valor'];
                            }
                        }
                    }
                }
            }
            $objServCaracteristica = $serviceServicioTecnico->getServicioProductoCaracteristica($objServicioSolicitud,
                                                                                                'TIPO_RED',
                                                                                                $objServicioSolicitud->getProductoId());
            if(is_object($objServCaracteristica) && !empty($objServCaracteristica))
            {
                $strTipoRed = $objServCaracteristica->getValor();
            }
            $objPunto                   = $objServicioSolicitud->getPuntoId();
            $strLoginPunto              = $objPunto->getLogin();
            $strDireccionPunto          = $objPunto->getDireccion();
            $objJurisdiccionPunto       = $objPunto->getPuntoCoberturaId();
            $strNombreJurisdiccionPunto = $objJurisdiccionPunto->getNombreJurisdiccion();
            $objPerCliente              = $objPunto->getPersonaEmpresaRolId();
            $objPersonaCliente          = $objPerCliente->getPersonaId();
            $strCliente                 = sprintf("%s",$objPersonaCliente);
            $strNombreTipoOrdenServicio = "";
            $strTipoOrdenServicio       = $objServicioSolicitud->getTipoOrden();
            $strDescripcionProducto     = $objServicioSolicitud->getProductoId()->getDescripcionProducto();
            if($strTipoOrdenServicio === 'T')
            {
                $strNombreTipoOrdenServicio = "Traslado";
            }
            else if($strTipoOrdenServicio=='N')
            {
                $strNombreTipoOrdenServicio = "Nueva";
            }
            $arrayParametrosMail    = array("accionMail"            => $strAccionMail,
                                            "accionUsuario"         => $strAccionUsuario,
                                            "nombreUsuarioGestion"  => $strNombreUsuarioGestion,
                                            "cliente"               => $strCliente,
                                            "loginPuntoCliente"     => $strLoginPunto,
                                            "nombreJurisdiccion"    => $strNombreJurisdiccionPunto,
                                            "direccionPuntoCliente" => $strDireccionPunto,
                                            "nombreProducto"        => $strNombreProducto,
                                            "descripcionProducto"   => $strDescripcionProducto,
                                            "observacion"           => $strObservacion,
                                            "tipoSolicitud"         => $objSolicitudServicio->getTipoSolicitudId()->getDescripcionSolicitud(),
                                            "estadoSolicitud"       => $objSolicitudServicio->getEstado(),
                                            "estadoServicio"        => $objServicioSolicitud->getEstado(),
                                            "prefijoEmpresa"        => $strPrefijoEmpresa,
                                            "fechaCreacionServicio" => strval(date_format($objServicioSolicitud->getFeCreacion(), "d-m-Y")),
                                            "tipoOrden"             => $strNombreTipoOrdenServicio,
                                            "tipoRed"               => $strTipoRed,
                                            "vendedor"              => $strNombreCompletoVendedor,
                                            "subgerente"            => $strNombreCompletoSubgerente);

            $serviceEnvio->generarEnvioPlantilla(   $strAccionAsunto." SOLICITUD DE AUTORIZACION ".$strNombreProducto, 
                                                    array_unique($arrayDestinatarios), 
                                                    'AprbRchzSolMpls', 
                                                    $arrayParametrosMail,
                                                    $strCodEmpresa,
                                                    '',
                                                    '',
                                                    null, 
                                                    true,
                                                    'notificaciones_telcos@telconet.ec');
            $strStatus  = "OK";
            $strMensaje = "Se procesó la solicitud exitosamente!";
            $emComercial->commit();
            
        }
        catch (\Exception $e) 
        {
            if ($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            $emComercial->close();
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudesController->gestionarSolicitudesServicioMplsAction',
                                      $e->getMessage(), 
                                      $strUsrSesion, 
                                      $strIpClient);
            $strStatus  = "ERROR";
            $strMensaje = "Existieron problemas al ejecutar transacción, notificar a Sistemas";
        }
        $arrayRespuesta = array('strStatus'     => $strStatus,
                                'strMensaje'    => $strMensaje);
        $objResponse->setData($arrayRespuesta);
        return $objResponse;
    }
    
    /**
     * ajaxAprobarSolicitudContratoCloudPublicAction
     *
     * Metodo encargado de aprobar las solicitudes de aprobación de contrato cloudpublic
     *
     * @return Json $objJsonResponse motivos de rechazo
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 25-07-2018
     */
    public function ajaxAprobarSolicitudContratoCloudPublicAction()
    {
        $objResponse                = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayResultado             = array();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();
        $intIdEmpresa               = $objSession->get('idEmpresa');  
        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');       
        $intIdDetalleSolicitud      = $objRequest->get('idDetalleSolicitud');
        $intIdPunto                 = $objRequest->get('idPunto');
        $strDescripcion             = $objRequest->get('descripcion');
        $serviceUtil                = $this->get('schema.Util');
        $serviceTecnico             = $this->get('tecnico.InfoServicioTecnico');
        $serviceCambiarPlan         = $this->get('tecnico.InfoCambiarPlan');
        $serviceCloudform           = $this->get('tecnico.CloudFormsService');
        $serviceEnvioPlantilla      = $this->get('soporte.EnvioPlantilla');
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        $emSoporte                  = $this->get('doctrine')->getManager('telconet_soporte');
        $emSeguridad                = $this->get('doctrine')->getManager('telconet_seguridad');
        
        $emComercial->getConnection()->beginTransaction();
        
        try
        {
            $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdDetalleSolicitud);
            $objPunto     = $emComercial->getRepository("schemaBundle:InfoPunto")->find($intIdPunto);
            
            if(is_object($objSolicitud) && is_object($objPunto))
            {
                $objSolicitud->setEstado('Aprobada');
                $objSolicitud->setObservacion('Se Aprobó la Solicitud de contrato con la siguiente observación:<br>'.$strDescripcion);
                $emComercial->persist($objSolicitud);
                $emComercial->flush();
                                    
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolHist->setObservacion('Se Aprobó la Solicitud de contrato con la siguiente observación:<br>'.$strDescripcion);
                $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                $objDetalleSolHist->setEstado('Aprobada');
                $emComercial->persist($objDetalleSolHist);
                $emComercial->flush();
                
                //Generación de correo de confirmación de servicio 
                //Activación automática del servicio y generación de
                $objServicioCloud = null;
                
                $arrayServicios   = $emComercial->getRepository("schemaBundle:InfoServicio")->findByPuntoId($objPunto->getId());
                
                foreach($arrayServicios as $objServicio)
                {
                    if($objServicio->getEstado() == 'Pendiente')
                    {
                        $objProducto = $objServicio->getProductoId();
                    
                        //Verificar si el producto tiene FACTURACION POR CONSUMO ( CLOUD PUBLIC )
                        $boolEsConsumo = $serviceTecnico->isContieneCaracteristica($objProducto,'FACTURACION POR CONSUMO');

                        if($boolEsConsumo)
                        {
                            $objServicioCloud = $objServicio;
                            break;
                        }
                    }
                }

                if(is_object($objServicioCloud))
                {
                    $strObservacionError     = '';
                    //Accion confirmar Servicio
                    $objAccion               = $emSeguridad->getRepository('schemaBundle:SistAccion')->findOneByNombreAccion('confirmarServicio');
                    
                    $objServicioCloud->setEstado('Activo');
                    $emComercial->persist($objServicioCloud);
                    $emComercial->flush();
                    
                    $objInfoServicioHistorial = new InfoServicioHistorial();
                    $objInfoServicioHistorial->setEstado('Activo');
                    $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoServicioHistorial->setIpCreacion($objRequest->getClientIp());
                    $objInfoServicioHistorial->setObservacion('Se confirma servicio automáticamente y se realiza generación de usuario del cliente '
                                                            . 'en la plataforma Coudforms');
                    $objInfoServicioHistorial->setServicioId($objServicioCloud);
                    $objInfoServicioHistorial->setUsrCreacion($objSession->get('user'));
                    $objInfoServicioHistorial->setAccion(is_object($objAccion)?$objAccion->getNombreAccion():null);
                    $emComercial->persist($objInfoServicioHistorial);
                    $emComercial->flush();
                    
                    //llamado a WS para inyectar usuario
                    $arrayDatosPunto = $emComercial->getRepository("schemaBundle:InfoPunto")
                                                   ->getArrayDatosPuntoCloudPublic($objServicioCloud->getPuntoId()->getId());

                    if(!empty($arrayDatosPunto))
                    {
                        if(empty($arrayDatosPunto['correo']))
                        {                            
                            throw new \Exception('ERROR : Punto Cliente no posee un Correo asociado al <b>Contacto Técnico</b>, '
                                                 . 'por favor gestionar el ingreso para poder generar la activación');
                        }

                        //Envio de información al WS de CloudForm
                        $arrayDatosPunto['usrCreacion'] = $objSession->get('user');
                        $arrayDatosPunto['ipCreacion']  = $objRequest->getClientIp();
                        $arrayDatosPunto['accion']      = 'registrousuario';
                        
                        $arrayRespuestaWS = $serviceCloudform->callCloudFormWebService($arrayDatosPunto);

                        if($arrayRespuestaWS['status'] == 'ERROR')
                        {
                            $strObservacionError = $arrayRespuestaWS['mensaje'];
                        }
                    }
                    
                    //Si existe error del WS, se envia correo y tarea automática a IT para verificación de información
                    if(!empty($strObservacionError))
                    {
                        $strNumeroTarea  = '';
                        $strNombreCanton = '';
                        $intIdCanton     = 0;
                        $intIdOficina    = $objPunto->getPuntoCoberturaId()->getOficinaId();

                        $objOficina      = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);

                        if(is_object($objOficina))
                        {
                            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                            if(is_object($objCanton))
                            {
                                $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                            }
                        }

                        $arrayParametros =  $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('RELACION REGION CON CIUDAD PARA DATACENTER', 
                                                               'COMERCIAL', 
                                                               '',
                                                               $strRegion,
                                                               '', 
                                                               '',
                                                               '',
                                                               '', 
                                                               '', 
                                                               $intIdEmpresa);
                        if(!empty($arrayParametros))
                        {
                            $strNombreCanton = $arrayParametros['valor1'];

                            $objCanton = $emGeneral->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);

                            if(is_object($objCanton))
                            {
                                $intIdCanton = $objCanton->getId();
                            }
                        }


                        $arrayInfoEnvio   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->get('CLOUDFORM TAREAS POR DEPARTAMENTO', 
                                                            'SOPORTE', 
                                                            '',
                                                            'REVISION FUNCIONAMIENTO CLOUDFORMS',
                                                            $strNombreCanton, 
                                                            '',
                                                            '',
                                                            '', 
                                                            '', 
                                                            $intIdEmpresa);

                        $strObservacion = '<b>Tarea Automática :</b><br>Se generó tarea para revisión de observación generada en la creación de'
                                        . ' usuario en la plataforma CLOUDFORM'
                                        . '<br><b>Login : </b> '.$objPunto->getLogin().'<br>'
                                        . '<br><b>Observación : </b> '.$strObservacionError.'<br>'
                            ;

                        $arrayParametrosEnvioPlantilla                      = array();
                        $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
                        $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $objSession->get('user');
                        $arrayParametrosEnvioPlantilla['strIpCreacion']     = $objRequest->getClientIp();
                        $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $objSolicitud->getId();
                        $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
                        $arrayParametrosEnvioPlantilla['objPunto']          = $objPunto;
                        $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
                        $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $intIdEmpresa;
                        $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $strPrefijoEmpresa;

                        foreach($arrayInfoEnvio as $array)
                        {
                            $objTarea  = $emSoporte->getRepository("schemaBundle:AdmiTarea")->findOneByNombreTarea($array['valor3']);

                            $arrayParametrosEnvioPlantilla['arrayCorreos']   = array($array['valor2']);
                            $arrayParametrosEnvioPlantilla['intTarea']       = is_object($objTarea)?$objTarea->getId():'';

                            //Se obtiene el departamento
                            $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                         ->findOneByNombreDepartamento($array['valor4']);

                            $arrayParametrosEnvioPlantilla['objDepartamento']    = $objDepartamento;
                            $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                            $strNumeroTarea = $serviceCambiarPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
                        }
                        
                        //Determinar envio de error
                        if(!empty($strNumeroTarea))
                        {
                            throw new \Exception($strObservacionError
                                              . '<br>Se generó Tarea de revisión al Departamento <b>Data Center IT</b> con número #'.$strNumeroTarea);
                        }
                        else
                        {
                            throw new \Exception('Error al generar la tarea al Departamento respectivo, por favor notificar a Sistemas');
                        }
                    }
                    else
                    {
                        //Envio correo de confirmación de creación de usuario y activación del cliente en el cloudforms
                        $arrayNotificacion                        = array();                    
                        $arrayNotificacion['login']               = $objPunto->getLogin();
                        $arrayNotificacion['solicitud']           = $objSolicitud->getId();                        

                        $arrayContactos = array();

                        //Correo del asesor que generó la solicitud
                        $objPersona = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objSession->get('user'));

                        if(is_object($objPersona))
                        {
                            $arrayNotificacion['usuario'] = $objPersona->getInformacionPersona();
                            
                            $objTipoContacto  = $emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                            ->findOneByDescripcionFormaContacto('Correo Electronico');

                            if(is_object($objTipoContacto))
                            {   
                                $objFormaContacto = $emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                                ->findOneBy(array('personaId'       => $objPersona->getId(),
                                                                                  'formaContactoId' => $objTipoContacto->getId(),
                                                                                  'estado'          => 'Activo'));
                                if(is_object($objFormaContacto))
                                {
                                    $arrayContactos[] = $objFormaContacto->getValor();
                                }
                            }
                        }

                        //Canton
                        $intIdOficina    = $objPunto->getPuntoCoberturaId()->getOficinaId();
                        $objOficina      = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
                        $intCanton       = '';

                        if(is_object($objOficina))
                        {
                            $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                            if(is_object($objCanton))
                            {
                                $intCanton = $objCanton->getId();
                            }
                        }

                        $serviceEnvioPlantilla->generarEnvioPlantilla( "APROBACIÓN SOLICITUD CONTRATO CLOUD PUBLIC: ".$objPunto->getLogin(), 
                                                                        $arrayContactos, 
                                                                        'APROBAR-CLOUD', 
                                                                        $arrayNotificacion, 
                                                                        $objSession->get('idEmpresa'), 
                                                                        $intCanton, 
                                                                        ''
                                                                       );
                    }                                        
                }
                
                $arrayResultado = array('strStatus' => 'OK', 'strMensaje' => 'Solicitud fue aprobada correctamente y se '
                                                                           . 'confirma activación del Servicio CLOUD');
                
                $emComercial->commit();
            }
            else
            {
                $arrayResultado = array('strStatus' => 'ERROR', 'strMensaje' => 'No se encuentra información de la solicitud a aprobar, '
                                                                              . 'notificar a sistemas');
            }
        } 
        catch (\Exception $ex) 
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            $strMensaje = 'Error al aprobar contrato para el Punto, notificar a Sistemas';
            
            if(strpos($ex->getMessage(), 'ERROR :')!== false)
            {
                $strMensaje = $ex->getMessage();
            }
            
            $emComercial->close();
            
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudesController.ajaxAprobarSolicitudContratoCloudPublicAction',
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                     ); 
            
            $arrayResultado = array('strStatus' => 'ERROR', 'strMensaje' => $strMensaje);
        }
        
        $objResponse->setData($arrayResultado);
        return $objResponse;
    }
    
    /**
     * getMotivosRechazoCloudPublicAction
     *
     * Metodo encargado de obtener los motivos asociados a la opcion de rechazar solicitud por aprobación
     *
     * @return Json $objJsonResponse motivos de rechazo
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 25-07-2018
     */
    public function ajaxGetMotivosRechazoCloudPublicAction()
    {
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $strRelacionSistema = "9751";
        $objJsonResponse    = new JsonResponse();
        $arrayRegistros     = array();
        $arrayRespuesta     = array();

        $objAdmiMotivos = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findBy(array("estado"            => "Activo",
                                                                                             "relacionSistemaId" => $strRelacionSistema));
        $strNumeroMotivo = count($objAdmiMotivos);
        foreach ($objAdmiMotivos as $objIdxAdmiMotivo)
        {
            $arrayRegistros[] = array('id_motivo'     => $objIdxAdmiMotivo->getId(),
                                      'nombre_motivo' => $objIdxAdmiMotivo->getNombreMotivo());
        }

        $arrayRespuesta["total"]       = $strNumeroMotivo;
        $arrayRespuesta["encontrados"] = $arrayRegistros;

        $objJsonResponse->setData($arrayRespuesta);

        return $objJsonResponse;
    }
    
    /**
     * ajaxRechazarSolicitudContratoCloudPublicAction
     *
     * Metodo encargado de rechazar las solicitudes de aprobación de contrato cloudpublic
     *
     * @return Json $objJsonResponse motivos de rechazo
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 25-07-2018
     */
    public function ajaxRechazarSolicitudContratoCloudPublicAction()
    {
        $objResponse                = new JsonResponse();
        $objResponse->headers->set('Content-Type', 'text/json');
        $arrayResultado             = array();
        $objRequest                 = $this->getRequest();
        $objSession                 = $objRequest->getSession();  
        $intIdDetalleSolicitud      = $objRequest->get('idDetalleSolicitud');
        $intMotivo                  = $objRequest->get('motivo');
        $intIdPunto                 = $objRequest->get('idPunto');
        $strDescripcion             = $objRequest->get('descripcion');
        $serviceUtil                = $this->get('schema.Util');
        $serviceEnvioPlantilla      = $this->get('soporte.EnvioPlantilla');
        $emComercial                = $this->get('doctrine')->getManager('telconet');
        $emComunicacion             = $this->get('doctrine')->getManager('telconet_comunicacion');
        $emGeneral                  = $this->get('doctrine')->getManager('telconet_general');
        
        $emComercial->getConnection()->beginTransaction();
        $emComunicacion->getConnection()->beginTransaction();
        
        try
        {
            $objSolicitud = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")->find($intIdDetalleSolicitud);
            $objPunto     = $emComercial->getRepository("schemaBundle:InfoPunto")->find($intIdPunto);
            
            if(is_object($objSolicitud) && is_object($objPunto))
            {
                $objMotivo = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->find($intMotivo);
                $strMotivo = 'N/A';
                
                if(is_object($objMotivo))
                {
                    $strMotivo = $objMotivo->getNombreMotivo();
                }
                
                $objSolicitud->setEstado('Rechazada');
                $objSolicitud->setObservacion('Se rechazó la Solicitud de contrato con el siguiente motivo:'.$strMotivo);
                $emComercial->persist($objSolicitud);
                $emComercial->flush();
                                    
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                $objDetalleSolHist->setObservacion('Se rechazó la Solicitud de contrato con el siguiente motivo: '.$strMotivo);
                $objDetalleSolHist->setIpCreacion($objRequest->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                $objDetalleSolHist->setEstado('Rechazada');
                $emComercial->persist($objDetalleSolHist);
                $emComercial->flush();
                
                //Liberar el documento subido al sistema
                $arrayDocumento = $emComercial->getRepository("schemaBundle:InfoPunto")->getArrayDocumentosPorPunto($objPunto->getLogin());
                
                foreach($arrayDocumento as $array)
                {
                    $objDocumento = $emComunicacion->getRepository("schemaBundle:InfoDocumento")->find($array['idDocumento']);
                    
                    if(is_object($objDocumento))
                    {
                        $objDocumento->setEstado('Eliminado');
                        $emComunicacion->persist($objDocumento);
                        $emComunicacion->flush();
                    }
                }                                
                
                //-------------------------------------
                //Generación de correo de información de Rechazo de documento a las áreas involucradas
                // - asesor comercial
                // - gerentes de producto
                // - alias ventas
                $arrayNotificacion                        = array();                    
                $arrayNotificacion['login']               = $objPunto->getLogin();
                $arrayNotificacion['solicitud']           = $objSolicitud->getId();
                $arrayNotificacion['motivo']              = $strMotivo;
                $arrayNotificacion['observacion']         = $strDescripcion;
                
                $arrayContactos = array();
                
                //Correo del asesor que generó la solicitud
                $objPersona = $emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($objSolicitud->getUsrCreacion());
                
                if(is_object($objPersona))
                {
                    $objTipoContacto  = $emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                    ->findOneByDescripcionFormaContacto('Correo Electronico');
                    
                    if(is_object($objTipoContacto))
                    {   
                        $objFormaContacto = $emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")
                                                        ->findOneBy(array('personaId'       => $objPersona->getId(),
                                                                          'formaContactoId' => $objTipoContacto->getId(),
                                                                          'estado'          => 'Activo'));
                        if(is_object($objFormaContacto))
                        {
                            $arrayContactos[] = $objFormaContacto->getValor();
                        }
                    }
                }
                
                //Canton
                $intIdOficina    = $objPunto->getPuntoCoberturaId()->getOficinaId();
                $objOficina      = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
                $intCanton       = '';

                if(is_object($objOficina))
                {
                    $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                    if(is_object($objCanton))
                    {
                        $intCanton = $objCanton->getId();
                    }
                }

                $serviceEnvioPlantilla->generarEnvioPlantilla( "RECHAZO SOLICITUD APROBACIÓN CONTRATO CLOUD PUBLIC: ".$objPunto->getLogin(), 
                                                                $arrayContactos, 
                                                                'RECHAZO-CLOUD', 
                                                                $arrayNotificacion, 
                                                                $objSession->get('idEmpresa'), 
                                                                $intCanton, 
                                                                ''
                                                               );
                
                //-------------------------------------
                
                $arrayResultado = array('strStatus'  => 'OK', 
                                        'strMensaje' => 'Solicitud fue rechazada con éxito');
                                
                $emComercial->commit();
                $emComunicacion->commit();
            }
            else
            {
                $arrayResultado = array('strStatus' => 'ERROR', 'strMensaje' => 'No se encuentra información de la solicitud a aprobar, '
                                                                        . 'notificar a sistemas');
            }
        } 
        catch (\Exception $ex) 
        {
            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->rollback();
            }
            
            if($emComunicacion->getConnection()->isTransactionActive())
            {
                $emComunicacion->rollback();
            }
            
            $emComercial->close();
            $emComunicacion->close();
            
            $serviceUtil->insertError('Telcos+', 
                                      'SolicitudesController.ajaxRechazarSolicitudContratoCloudPublicAction',
                                      $ex->getMessage(), 
                                      $objSession->get('user'), 
                                      $objRequest->getClientIp()
                                     ); 
            
            $arrayResultado = array('strStatus' => 'ERROR', 'strMensaje' => 'Error al rechazar contrato para el Punto, notificar a Sistemas');
        }
        
        $objResponse->setData($arrayResultado);
        return $objResponse;
    }

	 /**     
     * 
     * Documentación para el método 'getSeguimientoSolicitudesMaterialesAction'.
     * 
     * Obtiene los detalles de un determinado mantenimiento
     * 
     * @return Response.
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 01-03-2021
     * 
     */ 
    public function getSeguimientoSolicitudesMaterialesAction()
    {
		$objResponse    = new Response();
		$objResponse->headers->set('Content-Type', 'text/json');
        $objPeticion             = $this->get('request');
		$intIdDetalle            = $objPeticion->query->get('idDetalleSolicitud');
        $emSoporte   = $this->getDoctrine()->getManager('telconet_soporte');
        $arrayParametros = array();
        
        try
        {    

            $arrayParametros["idDetalleSolicitud"]           = $intIdDetalle;
        
            $objJson = $emSoporte->getRepository('schemaBundle:InfoDetalleSolHist')
            ->getDetalleSolicitudHistorial($arrayParametros["idDetalleSolicitud"]);
        }
        catch(\Exception $e)
        {
        $strContent = $e->getMessage();
        return $strContent;
        }
        $objRespuesta->setContent($objJson);

        return $objRespuesta;
	}
	



	/**
     * 
     * Funcion que sirve para ingresar el seguimiento de Solicitudes de Materiales
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @since 1.03
     * @version 1.1 04-03-2021
     *
     */
    public function IngresarSeguimientoSolicitudMaterialesAction()
    {
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        
        $objPeticion        = $this->get('request');
        $objSession         = $objPeticion->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $intDepartamento    = $objSession->get('idDepartamento');
		$intIdDetalle       = $objPeticion->get('id_factibilidad');
        $strSeguimiento     = $objPeticion->get('seguimiento');
        $strUsrCreacion     = $objSession->get('user');
        $strIpCreacion      = $objPeticion->getClientIp();
    try
    {
        $arrayParametros = array(
                                    'idEmpresa'     => $intIdEmpresa,
                                    'prefijoEmpresa'=> $strPrefijoEmpresa,
                                    'departamento'  => $intDepartamento,
                                    'idDetalle'     => $intIdDetalle,
                                    'seguimiento'   => $strSeguimiento,
                                    'usrCreacion'   => $strUsrCreacion,
                                    'ipCreacion'    => $strIpCreacion
                                );
        $serviceMateriales = $this->get('service.MaterialesService');
        //---------------------------------------------------------------------*/
        
        //COMUNICACION CON LA CAPA DE NEGOCIO (SERVICE)-------------------------*/
		$arrayRespuesta = $serviceMateriales->ingresarSeguimientoMaterialesExcedentes($arrayParametros);
        //----------------------------------------------------------------------*/
    }
    catch(\Exception $e)
    {
        $arrayRespuesta['error'] = $e->getMessage();
    }
        //--------RESPUESTA-----------------------------------------------------*/
        $objResultado = json_encode($arrayRespuesta);
        //----------------------------------------------------------------------*/
        $objRespuesta->setContent($objResultado);
        
        return $objRespuesta;
    }

}
