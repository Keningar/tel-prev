<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicioComisionHisto;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoLog;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;

/**
 * AutorizarSolicitudesClientes controller.
 * 
 * Controlador que contiene las funciones correspondientes al manejo de las solicitudes creadas de los clientes
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 27-04-2017
 */
class AutorizarSolicitudesClientesController extends Controller
{
    /**
     * @Secure(roles="ROLE_384-1")
     * 
     * Documentación para la función 'indexAction'
     * 
     * Método que permite presentar las solicitudes relacionadas al punto del cliente en sessión
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-04-2017
     * 
     * @return view
     */   
    public function indexAction()
    {
        return $this->render( 'comercialBundle:AutorizarSolicitudesClientes:index.html.twig' );
    }


    /**
     * Documentación para la función 'getTiposSolicitudesClientesAction'
     * 
     * Método que retorna los tipos de solicitudes de los clientes
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-04-2017
     * 
     * @return JsonResponse $objJsonResponse Json que contiene los tipos de solicitudes de clientes que el usuario puede seleccionar
     */   
    public function getTipoSolicitudesClientesAction()
    {
        $objJsonResponse               = new JsonResponse();
        $emGeneral                     = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                    = $this->get('request');
        $objSession                    = $objRequest->getSession();
        $strIpCreacion                 = $objRequest->getClientIp();
        $strUsuario                    = $objSession->get('user');
        $serviceUtil                   = $this->get('schema.Util');
        $arrayTiposSolicitudesClientes = array('total' => 0, 'encontrados' => array());

        try
        {
            $arrayParametros                    = array('strNombreParametroCab' => 'SOLICITUDES_CLIENTES', 'estado' => 'Activo');
            $arrayResultadosSolicitudesClientes = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getArrayDetalleParametros($arrayParametros);

            if( isset($arrayResultadosSolicitudesClientes['total']) && $arrayResultadosSolicitudesClientes['total'] > 0
                && isset($arrayResultadosSolicitudesClientes['encontrados']) && !empty($arrayResultadosSolicitudesClientes['encontrados']) )
            {
                $intContador = 0;

                foreach($arrayResultadosSolicitudesClientes['encontrados'] as $arrayItem)
                {
                    if( isset($arrayItem['valor1']) && !empty($arrayItem['valor1']) )
                    {
                        if( true === $this->get('security.context')->isGranted($arrayItem['valor1']) )
                        {
                            $intContador++;
                            $arrayTiposSolicitudesClientes['encontrados'][] = $arrayItem;
                        }//( true === $this->get('security.context')->isGranted($arrayItem['valor1']) )
                    }//( isset($arrayItem['valor1']) && !empty($arrayItem['valor1']) )
                }//foreach($arrayResultadosSolicitudesClientes['encontrados'] as $arrayItem)

                $arrayTiposSolicitudesClientes['total'] = $intContador;
            }//( isset($arrayResultadosSolicitudesClientes['total']) && $arrayResultadosSolicitudesClientes['total'] > 0...
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.AutorizarSolicitudesClientesController.getTipoSolicitudesClientesAction', 
                                       'Error al consultar los tipos de solicitudes a aprobar. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setData($arrayTiposSolicitudesClientes);
        
        return $objJsonResponse;
    }


    /**
     * @Secure(roles="ROLE_384-7")
     * 
     * Documentación para la función 'gridAction'
     * 
     * Método que retorna todas las solicitudes dependiendo de las opciones seleccionadas por el usuario
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 27-04-2017
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 29-11-2019 Se agrega compatibilidad para solicitudes de cambio masivo de vendedor
     * 
     * return JsonResponse $objJsonResponse  Json con las solicitudes que el usuario podrá aprobar o rechazar.
     */   
    public function gridAction()
    {
        $objJsonResponse            = new JsonResponse();
        $emComercial                = $this->getDoctrine()->getManager('telconet');
        $serviceUtil                = $this->get('schema.Util');
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $strIpCreacion              = $objRequest->getClientIp();
        $strUsuario                 = $objSession->get('user');
        $strCambioMasivoVendedor    = $objRequest->get('strCambioMasivoVendedor', 'N');
        $strCaracteristicaSolicitud = $objRequest->get('strCaracteristicaSolicitud', '');
        $arraySolicitudes           = array('total' => 0, 'registros' => array());
        
        try
        {
            $intInicio               = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
            $intLimite               = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
            $strEstadoSolicitud      = $objRequest->query->get('strEstadoSolicitud') ? $objRequest->query->get('strEstadoSolicitud') 
                                       : 'Pendiente';
            $intTipoSolicitudCliente = $objRequest->query->get('intTipoSolicitudCliente') ? $objRequest->query->get('intTipoSolicitudCliente')
                                       : 0;
            $strFechaCreacionDesde   = $objRequest->query->get('strFechaCreacionDesde') ? $objRequest->query->get('strFechaCreacionDesde') : '';
            $strFechaCreacionHasta   = $objRequest->query->get('strFechaCreacionHasta') ? $objRequest->query->get('strFechaCreacionHasta') : '';
            $strFechaCreacionDesde   = ( !empty($strFechaCreacionDesde) ) ? date_format(date_create($strFechaCreacionDesde), 'y-m-d').'' : '';
            $strFechaCreacionHasta   = ( !empty($strFechaCreacionHasta) ) ? date_format(date_create($strFechaCreacionHasta), 'y-m-d').'' : '';

            if($strCambioMasivoVendedor == 'S' && empty($strCaracteristicaSolicitud))
            {
                throw new \Exception('No se ha definido la característica de la solicitud.');
            }

            $arrayParametrosSolicitudes = array('intInicio'                  => $intInicio,
                                                'intLimite'                  => $intLimite,
                                                'intIdTipoSolicitud'         => $intTipoSolicitudCliente,
                                                'strFechaCreacionDesde'      => $strFechaCreacionDesde,
                                                'strFechaCreacionHasta'      => $strFechaCreacionHasta,
                                                'strEstadoSolicitud'         => $strEstadoSolicitud,
                                                'strCambioMasivoVendedor'    => $strCambioMasivoVendedor,
                                                'strCaracteristicaSolicitud' => $strCaracteristicaSolicitud);
            
            $arraySolicitudes = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                            ->getDetalleSolicitudesByCriterios( $arrayParametrosSolicitudes );
        }
        catch(\Exception $e) 
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.AutorizarSolicitudesClientesController.gridAction', 
                                       'Error al consultar las solicitudes a aprobar. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setData($arraySolicitudes);
        
        return $objJsonResponse;
    }


    /**
     * Documentación para la función 'getMotivosRechazoAction'
     * 
     * Método que retorna los motivos de rechazo de las solicitudes pendientes por aprobar
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 02-05-2017
     * 
     * @return JsonResponse $objJsonResponse  Retorna un string en formato json con los motivos de rechazo que el usuario podrá seleccionar.
     */
    public function getMotivosRechazoAction()
    {
        $objJsonResponse       = new JsonResponse();
        $emComercial           = $this->getDoctrine()->getManager("telconet");
        $objRequest            = $this->get('request');
        $objSession            = $objRequest->getSession();
        $strIpCreacion         = $objRequest->getClientIp();
        $strUsuario            = $objSession->get('user');
        $serviceUtil           = $this->get('schema.Util');
        $strJsonMotivosRechazo = json_encode(array('total' => 0, 'encontrados' => array()));

        try
        {
            $intInicio               = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
            $intLimite               = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
            $strEstadoSolicitud      = $objRequest->query->get('strEstadoSolicitud') ? $objRequest->query->get('strEstadoSolicitud') 
                                       : 'Pendiente';
            $intTipoSolicitudCliente = $objRequest->query->get('intTipoSolicitudCliente') ? $objRequest->query->get('intTipoSolicitudCliente')
                                       : 0;
            
            $arrayParametros       = array( 'nombreModulo' => 'autorizarsolicitudesclientes', 
                                            'nombreAccion' => 'index',
                                            'estados'      => array('Activo') );
            $strJsonMotivosRechazo = $emComercial->getRepository('schemaBundle:AdmiMotivo')
                                                 ->getJSONMotivosPorModuloYPorAccion($arrayParametros);
        }
        catch(\Exception $e)
        {
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.AutorizarSolicitudesClientesController.getMotivosRechazoAction', 
                                       'Error al consultar los motivos de rechazo de las solicitudes. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objJsonResponse->setContent($strJsonMotivosRechazo);
        
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para la función 'aprobarRechazarSolicitudesClienteAction'
     * 
     * Método que realiza la acción de aprobar y/o rechazar las solicitudes de los clientes que el usuario en sessión ha seleccionado.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 02-05-2017
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 26-11-2019 Implementación de aprobación rechazo de solicitudes de cambio de vendedor masivo
     * 
     * @return JsonResponse $objJsonResponse  Retorna un string en formato json con los motivos de rechazo que el usuario podrá seleccionar.
     */
    public function aprobarRechazarSolicitudesClienteAction()
    {
        $objJsonResponse         = new JsonResponse();
        $emComercial             = $this->getDoctrine()->getManager("telconet");
        $emGeneral               = $this->getDoctrine()->getManager("telconet_general");
        $objRequest              = $this->get('request');
        $objSession              = $objRequest->getSession();
        $strIpCreacion           = $objRequest->getClientIp();
        $strUsuario              = $objSession->get('user');
        $serviceUtil             = $this->get('schema.Util');
        $strMensajeUsuario       = '';
        $strMensajeError         = '';
        $arrayRespuesta          = array('strMensajeConfirmacion' => 'OK', 'strMensajeError' => '');

        $emComercial->getConnection()->beginTransaction();

        try
        {
            $strAccion                   = $objRequest->request->get('strAccion') ? $objRequest->request->get('strAccion') : null;
            $intIdMotivoRechazo          = $objRequest->request->get('intIdMotivoRechazo') ? $objRequest->request->get('intIdMotivoRechazo') 
                                           : 0;
            $strSolicitudesSeleccionadas = $objRequest->request->get('strSolicitudesSeleccionadas') 
                                           ? $objRequest->request->get('strSolicitudesSeleccionadas') : null;
            
            //SE VERIFICA QUE SE ENVIE LA ACCION Y LAS SOLICITUDES QUE SE VAN A PROCESAR
            if( !empty($strAccion) && !empty($strSolicitudesSeleccionadas) )
            {
                if( $strAccion == "rechazar" && empty($intIdMotivoRechazo) )
                {
                    throw new \Exception('No se ha selecionado el motivo de rechazo de las solicitudes.');
                }//( $strAccion == "rechazar" )
                
                $arraySolicitudesSeleccionadas = explode('|', $strSolicitudesSeleccionadas);
                
                if( !empty($arraySolicitudesSeleccionadas) )
                {
                    foreach($arraySolicitudesSeleccionadas as $intIdSolicitud)
                    {
                        if( $intIdSolicitud > 0 )
                        {
                            $strObservacionHistorial = "";
                            $objInfoSolicitudDetalle = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                   ->findOneById($intIdSolicitud);

                            if( !is_object($objInfoSolicitudDetalle) )
                            {
                                throw new \Exception('No se ha encontrado la solicitud seleccionada.');
                            }//( !is_object($objInfoSolicitudDetalle) )
                            
                            $objInfoServicioSolicitud = $objInfoSolicitudDetalle->getServicioId();
                            
                            if( !is_object($objInfoServicioSolicitud) )
                            {
                                throw new \Exception('No se ha encontrado el servicio asociado a la solicitud.');
                            }//( !is_object($objInfoServicioSolicitud) )
                            
                            $intIdServicioSolicitud = $objInfoServicioSolicitud->getId();
                            
                            if( empty($intIdServicioSolicitud) )
                            {
                                throw new \Exception('No se ha encontrado el id del servicio asociado a la solicitud.');
                            }//( empty($intIdServicioSolicitud) )
                            
                            $objAdmiTipoSolicitud = $objInfoSolicitudDetalle->getTipoSolicitudId();
                            
                            if( !is_object($objAdmiTipoSolicitud) )
                            {
                                throw new \Exception('No se ha encontrado el tipo de la solicitud.');
                            }//( !is_object($objInfoSolicitudDetalle) )
                            
                            $strDescripcionSolicitud = $objAdmiTipoSolicitud->getDescripcionSolicitud();
                            
                            $arrayInfoDetSolCaracteristicas = $emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                          ->findBy(array('estado'             => 'Activo',
                                                                                         'detalleSolicitudId' => $objInfoSolicitudDetalle));

                            if( $strAccion == "aprobar" )
                            {
                                $strObservacionHistorial = "Se ha aprobado la solicitud ".$strDescripcionSolicitud;

                                $objInfoSolicitudDetalle->setEstado('Aprobada');

                                if( $strDescripcionSolicitud == "SOLICITUD CAMBIO PERSONAL PLANTILLA" )
                                {
                                    if( !empty($arrayInfoDetSolCaracteristicas) )
                                    {
                                        foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                                        {
                                            if( is_object($objInfoDetalleSolCaract) )
                                            {
                                                //SE CAMBIAN DE ESTADO LAS CARACTERISTICAS ASIGNADAS A LA SOLICITUD
                                                $objInfoDetalleSolCaract->setEstado('Aprobada');
                                                $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                                $objInfoDetalleSolCaract->setUsrUltMod($strUsuario);
                                                $emComercial->persist($objInfoDetalleSolCaract);

                                                $objAdmiCaracteristica = $objInfoDetalleSolCaract->getCaracteristicaId();

                                                if( !is_object($objAdmiCaracteristica) )
                                                {
                                                    throw new \Exception('No se ha encontrado característica asociada al detalle de la solicitud'.
                                                                         $objInfoSolicitudDetalle->getId());
                                                }


                                                //SE OBTIENE EL NUEVO PERSONAL ASIGNADO A LA PLANTILLA DE COMISIONISTAS
                                                $intValorPersonaEmpresaRol     = $objInfoDetalleSolCaract->getValor();
                                                $objInfoPersonaEmpresaRolNuevo = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                             ->findOneById($intValorPersonaEmpresaRol);

                                                if( !is_object($objInfoPersonaEmpresaRolNuevo) )
                                                {
                                                    throw new \Exception('No se ha encontrado el nuevo personal asignado a la plantilla de '.
                                                                         'comisionistas');
                                                }

                                                $objInfoPersonaNuevo = $objInfoPersonaEmpresaRolNuevo->getPersonaId();

                                                if( !is_object($objInfoPersonaNuevo) )
                                                {
                                                    throw new \Exception('No se ha encontrado la información del nuevo personal asignado a la '.
                                                                         'plantilla de comisionistas');
                                                }

                                                $strNombreNuevoPersonalComisionista = trim($objInfoPersonaNuevo->__toString());
                                                $strNombreNuevoPersonalComisionista = strtolower($strNombreNuevoPersonalComisionista);
                                                $strNombreNuevoPersonalComisionista = ucwords($strNombreNuevoPersonalComisionista);
                                                
                                                
                                                //SE REEMPLAZA LA PALABRA 'CAMBIO_' PARA PODER OBTENER EL CARGO DEL PERSONAL QUE SE REQUIERE ASIGNAR
                                                $strDescripcionCaracteristica = $objAdmiCaracteristica->getDescripcionCaracteristica();
                                                $strDescripcionCaracteristica = str_replace('CAMBIO_', '', trim($strDescripcionCaracteristica));
                                                $strDescripcionCaracteristica = trim($strDescripcionCaracteristica);
                                                
                                                
                                                //SE OBTIENE EL REGISTRO DE LA INFOR_SERVICIO_COMISION QUE SE EDITARÁ
                                                $arrayParametrosComision = array('arrayEstados'       => array('Activo'),
                                                                                 'intIdServicio'      => $intIdServicioSolicitud,
                                                                                 'strRolComisionista' => $strDescripcionCaracteristica);
                                                $arrayResultadoComision  = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                       ->getServicioComision($arrayParametrosComision);

                                                $strMensajeError = 'No se obtuvo la comisión asociada al servicio. Rechace la solicitud.<br/><br/>' .
                                                    'Login:&ensp;&ensp;&ensp;<b>' .
                                                    $objInfoServicioSolicitud->getPuntoId()->getLogin() .
                                                    '</b><br/>Servicio:&ensp;<b>' .
                                                    $objInfoServicioSolicitud->getDescripcionPresentaFactura() . '</b>';

                                                if( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] == 1
                                                    && isset($arrayResultadoComision['arrayRegistros']) 
                                                    && !empty($arrayResultadoComision['arrayRegistros']) )
                                                {
                                                    $arrayInfoServicioComision = $arrayResultadoComision['arrayRegistros'][0];
                                                    
                                                    if( isset($arrayInfoServicioComision['idServicioComision']) 
                                                        && $arrayInfoServicioComision['idServicioComision'] > 0 )
                                                    {
                                                        //SE OBTIENE EL OBJETO INFO_SERVICIO_COMISION
                                                        $intIdServicioComision   = $arrayInfoServicioComision['idServicioComision'];
                                                        $objInfoServicioComision = $emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                                                               ->findOneById($intIdServicioComision);
                                                        if( !is_object($objInfoServicioComision) )
                                                        {
                                                            throw new \Exception('No se encontró el objeto servicio comision para ser modificado');
                                                        }//( is_object($objInfoServicioComision) )
                                                        
                                                        $floatComisionMantenimiento = $objInfoServicioComision->getComisionMantenimiento();
                                                        $floatComisionVenta         = $objInfoServicioComision->getComisionVenta();
                                                        $strEstadoServicioComision  = $objInfoServicioComision->getEstado();
                                                        $objInfoServicio            = $objInfoServicioComision->getServicioId();
                                                        $objInfoPersonaEmpresaRol   = $objInfoServicioComision->getPersonaEmpresaRolId();
                                                        $objAdmiComisionDet         = $objInfoServicioComision->getComisionDetId();
                                                        $strObservacionComision     = 'Se modifica el '.$strDescripcionCaracteristica.' de la '.
                                                                                      'plantilla de comisinistas.';
                                                        
                                                        /**
                                                         * SE GUARDA HISTORIAL EN LA INFO_SERVICIO_COMISION_HISTORIAL DEL CAMBIO DE PERSONAL DE LA
                                                         * PLANTILLA DE COMISIONISTAS
                                                         */
                                                        $objInfoServicioComisionHisto = new InfoServicioComisionHisto();
                                                        $objInfoServicioComisionHisto->setComisionDetId($objAdmiComisionDet);
                                                        $objInfoServicioComisionHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                                        $objInfoServicioComisionHisto->setServicioComisionId($objInfoServicioComision);
                                                        $objInfoServicioComisionHisto->setServicioId($objInfoServicio);
                                                        $objInfoServicioComisionHisto->setComisionMantenimiento($floatComisionMantenimiento);
                                                        $objInfoServicioComisionHisto->setComisionVenta($floatComisionVenta);
                                                        $objInfoServicioComisionHisto->setEstado($strEstadoServicioComision);
                                                        $objInfoServicioComisionHisto->setUsrCreacion($strUsuario);
                                                        $objInfoServicioComisionHisto->setFeCreacion(new \DateTime('now'));
                                                        $objInfoServicioComisionHisto->setIpCreacion($strIpCreacion);
                                                        $objInfoServicioComisionHisto->setObservacion($strObservacionComision);
                                                        $emComercial->persist($objInfoServicioComisionHisto);
                                                        
                                                        $strNombreAntiguoComisionista = "NO TIENE PERSONAL ASIGNADO";
                                                        
                                                        if( is_object($objInfoPersonaEmpresaRol) )
                                                        {
                                                            $objInfoPersonaAntiguo = $objInfoPersonaEmpresaRol->getPersonaId();

                                                            if( !is_object($objInfoPersonaAntiguo) )
                                                            {
                                                                throw new \Exception('No se ha encontrado la información del antiguo personal '.
                                                                                     'asignado a la plantilla de comisionistas');
                                                            }

                                                            $strNombreAntiguoComisionista = trim($objInfoPersonaAntiguo->__toString());
                                                            $strNombreAntiguoComisionista = strtolower($strNombreAntiguoComisionista);
                                                            $strNombreAntiguoComisionista = ucwords($strNombreAntiguoComisionista);
                                                        }//( is_object($objInfoPersonaEmpresaRol) )


                                                        //SE ACTUALIZA LA INFO_SERVICIO_COMISION CON EL NUEVO PERSONAL ASIGNADO A LA PLANTILLA
                                                        $objInfoServicioComision->setPersonaEmpresaRolId($objInfoPersonaEmpresaRolNuevo);
                                                        $objInfoServicioComision->setUsrUltMod($strUsuario);
                                                        $objInfoServicioComision->setFeUltMod(new \DateTime('now'));
                                                        $objInfoServicioComision->setIpUltMod($strIpCreacion);
                                                        $emComercial->persist($objInfoServicioComision);


                                                        /**
                                                         * SE CREA UN HISTORIAL EN LA INFO_SERVICIO_HISTORIAL EN EL CUAL SERA VISIBLE EL CAMBIO
                                                         * REALIZADO EN LA PLANTILLA DE COMISIONISTAS DEL SERVICIO
                                                         */
                                                        $strObservacionServicio   = "Se realiza el siguiente cambio en la plantilla de ".
                                                                                    "comisionistas:<br/>Se cambia de <b>".
                                                                                    $strDescripcionCaracteristica."</b><br/>Antiguo: ".
                                                                                    $strNombreAntiguoComisionista."<br/>Nuevo: ".
                                                                                    $strNombreNuevoPersonalComisionista;
                                                        $objInfoServicioHistorial = new InfoServicioHistorial();
                                                        $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                                                        $objInfoServicioHistorial->setUsrCreacion($strUsuario);
                                                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                                                        $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                                                        $objInfoServicioHistorial->setServicioId($objInfoServicio);
                                                        $objInfoServicioHistorial->setObservacion($strObservacionServicio);
                                                        $objInfoServicioHistorial->setAccion('aprobacionComisionista');
                                                        $emComercial->persist($objInfoServicioHistorial);
                                                        
                                                        
                                                        //SE ACTUALIZA EL USR VENDEDOR NUEVO A NIVEL DEL SERVICIO
                                                        if( $strDescripcionCaracteristica == "VENDEDOR" )
                                                        {
                                                            $strLoginVendedorNuevo = $objInfoPersonaNuevo->getLogin();
                                                            
                                                            if( empty($strLoginVendedorNuevo) )
                                                            {
                                                                throw new \Exception('No se ha encontrado el login del vendedor nuevo.');
                                                            }//( !empty($strLoginVendedorNuevo) )
                                                            
                                                            $objInfoServicio->setUsrVendedor($strLoginVendedorNuevo);
                                                            $emComercial->persist($objInfoServicio);
                                                        }//( $strDescripcionCaracteristica == "CAMBIO_VENDEDOR" )
                                                    }
                                                    else
                                                    {
                                                        $strMensajeUsuario = $strMensajeError;
                                                        throw new \Exception('No se obtuvo plantilla de comisionista para ser editada.');
                                                    }//( isset($arrayInfoServicioComision['idServicioComision'])...
                                                }
                                                else
                                                {
                                                    $strMensajeUsuario = $strMensajeError;
                                                    throw new \Exception('No se obtuvo plantilla de comisionista para ser editada.');
                                                }////( isset($arrayResultadoComision['intTotal']) && $arrayResultadoComision['intTotal'] == 1...
                                            }//( is_object($objInfoDetalleSolCaract) )
                                        }//foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                                    }
                                    else
                                    {
                                        throw new \Exception('No se han encontrado las características necesarias para procesar la solicitud: '.
                                                              $strDescripcionSolicitud);
                                    }//( !empty($arrayInfoDetSolCaracteristicas) )
                                }//( $strDescripcionSolicitud == "SOLICITUD CAMBIO PERSONAL PLANTILLA" )
                                else if( $strDescripcionSolicitud == "SOLICITUD CAMBIO COMISION" )
                                {
                                    $floatComisionNueva = $objInfoSolicitudDetalle->getPrecioDescuento();
                                    
                                    if( floatval($floatComisionNueva) > 0 )
                                    {
                                        if( !empty($arrayInfoDetSolCaracteristicas) )
                                        {
                                            foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                                            {
                                                if( is_object($objInfoDetalleSolCaract) )
                                                {
                                                    $intIdServicioComision = $objInfoDetalleSolCaract->getValor();
                                                    
                                                    if( $intIdServicioComision > 0 )
                                                    {
                                                        //SE CAMBIAN DE ESTADO LAS CARACTERISTICAS ASIGNADAS A LA SOLICITUD
                                                        $objInfoDetalleSolCaract->setEstado('Aprobada');
                                                        $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                                        $objInfoDetalleSolCaract->setUsrUltMod($strUsuario);
                                                        $emComercial->persist($objInfoDetalleSolCaract);
                                                        
                                                        $objInfoServicioComision = $emComercial->getRepository('schemaBundle:InfoServicioComision')
                                                                                               ->findOneById($intIdServicioComision);
                                                        if( !is_object($objInfoServicioComision) )
                                                        {
                                                            throw new \Exception('No se encontró el objeto servicio comision para ser modificado');
                                                        }//( is_object($objInfoServicioComision) )
                                                        
                                                        $floatComisionMantenimiento = $objInfoServicioComision->getComisionMantenimiento();
                                                        $floatComisionVenta         = $objInfoServicioComision->getComisionVenta();
                                                        $strEstadoServicioComision  = $objInfoServicioComision->getEstado();
                                                        $objInfoServicio            = $objInfoServicioComision->getServicioId();
                                                        $objInfoPersonaEmpresaRol   = $objInfoServicioComision->getPersonaEmpresaRolId();
                                                        $objAdmiComisionDet         = $objInfoServicioComision->getComisionDetId();
                                                        $strObservacionComision     = 'Se modifica la comisión de la plantilla de comisinistas.';
                                                        
                                                        /**
                                                         * SE GUARDA HISTORIAL EN LA INFO_SERVICIO_COMISION_HISTORIAL DEL CAMBIO DE COMISION DE LA
                                                         * PLANTILLA DE COMISIONISTAS
                                                         */
                                                        $objInfoServicioComisionHisto = new InfoServicioComisionHisto();
                                                        $objInfoServicioComisionHisto->setComisionDetId($objAdmiComisionDet);
                                                        $objInfoServicioComisionHisto->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                                                        $objInfoServicioComisionHisto->setServicioComisionId($objInfoServicioComision);
                                                        $objInfoServicioComisionHisto->setServicioId($objInfoServicio);
                                                        $objInfoServicioComisionHisto->setComisionMantenimiento($floatComisionMantenimiento);
                                                        $objInfoServicioComisionHisto->setComisionVenta($floatComisionVenta);
                                                        $objInfoServicioComisionHisto->setEstado($strEstadoServicioComision);
                                                        $objInfoServicioComisionHisto->setUsrCreacion($strUsuario);
                                                        $objInfoServicioComisionHisto->setFeCreacion(new \DateTime('now'));
                                                        $objInfoServicioComisionHisto->setIpCreacion($strIpCreacion);
                                                        $objInfoServicioComisionHisto->setObservacion($strObservacionComision);
                                                        $emComercial->persist($objInfoServicioComisionHisto);
                                                        
                                                        
                                                        //SE ACTUALIZA LA COMISION DE VENTA
                                                        $objInfoServicioComision->setComisionVenta($floatComisionNueva);
                                                        $emComercial->persist($objInfoServicioComision);


                                                        /**
                                                         * SE CREA UN HISTORIAL EN LA INFO_SERVICIO_HISTORIAL EN EL CUAL SERA VISIBLE EL CAMBIO
                                                         * REALIZADO EN LA PLANTILLA DE COMISIONISTAS DEL SERVICIO
                                                         */
                                                        $strObservacionServicio   = "Se realiza el siguiente cambio en la plantilla de ".
                                                                                    "comisionistas:<br/>Se cambia la <b>Comisión de Venta</b><br/>".
                                                                                    "Antigua: ".$floatComisionVenta."<br/>Nuevo: ".
                                                                                    $floatComisionNueva;
                                                        $objInfoServicioHistorial = new InfoServicioHistorial();
                                                        $objInfoServicioHistorial->setEstado($objInfoServicio->getEstado());
                                                        $objInfoServicioHistorial->setUsrCreacion($strUsuario);
                                                        $objInfoServicioHistorial->setFeCreacion(new \DateTime('now'));
                                                        $objInfoServicioHistorial->setIpCreacion($strIpCreacion);
                                                        $objInfoServicioHistorial->setServicioId($objInfoServicio);
                                                        $objInfoServicioHistorial->setObservacion($strObservacionServicio);
                                                        $objInfoServicioHistorial->setAccion('aprobacionComisionista');
                                                        $emComercial->persist($objInfoServicioHistorial);
                                                    }
                                                    else
                                                    {
                                                        throw new \Exception('No se ha encontrado el id de la plantilla de comisionista a editar');
                                                    }//( $intIdServicioComision > 0 )
                                                }//( is_object($objInfoDetalleSolCaract) )
                                            }//foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                                        }
                                        else
                                        {
                                            throw new \Exception('No se han encontrado las características necesarias para procesar la solicitud: '.
                                                                  $strDescripcionSolicitud);
                                        }//( !empty($arrayInfoDetSolCaracteristicas) )
                                    }
                                    else
                                    {
                                        throw new \Exception('La comisión ha actualizar no puede ser cero.');
                                    }//( floatval($floatComisionNueva) > 0 )
                                }//( $strDescripcionSolicitud == "SOLICITUD CAMBIO COMISION" )
                            }//( $strAccion == "aprobar" )
                            else if( $strAccion == "rechazar" )
                            {
                                $objAdmiMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivoRechazo);

                                if( !is_object($objAdmiMotivo) )
                                {
                                    throw new \Exception('No se ha encontrado el motivo de rechazo de la solicitud.');
                                }//( !is_object($objAdmiMotivo) )

                                $strObservacionHistorial = "Se ha rechazado la solicitud ".$strDescripcionSolicitud.". Motivo: ".
                                                           $objAdmiMotivo->getNombreMotivo();

                                $objInfoSolicitudDetalle->setEstado('Rechazada');
                                $objInfoSolicitudDetalle->setFeRechazo(new \DateTime('now'));
                                $objInfoSolicitudDetalle->setUsrRechazo($strUsuario);
                                
                                if( !empty($arrayInfoDetSolCaracteristicas) )
                                {
                                    foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                                    {
                                        if( is_object($objInfoDetalleSolCaract) )
                                        {
                                            //SE CAMBIAN DE ESTADO LAS CARACTERISTICAS ASIGNADAS A LA SOLICITUD
                                            $objInfoDetalleSolCaract->setEstado('Rechazada');
                                            $objInfoDetalleSolCaract->setFeUltMod(new \DateTime('now'));
                                            $objInfoDetalleSolCaract->setUsrUltMod($strUsuario);
                                            
                                            $emComercial->persist($objInfoDetalleSolCaract);
                                        }//( is_object($objInfoDetalleSolCaract) )
                                    }//foreach($arrayInfoDetSolCaracteristicas as $objInfoDetalleSolCaract)
                                }//( !empty($arrayInfoDetSolCaracteristicas) )
                            }//( $strAccion == "rechazar" )

                            $emComercial->persist($objInfoSolicitudDetalle);

                            $objInfoDetalleSolicitudHistorial = new InfoDetalleSolHist();
                            $objInfoDetalleSolicitudHistorial->setDetalleSolicitudId($objInfoSolicitudDetalle);
                            $objInfoDetalleSolicitudHistorial->setEstado($objInfoSolicitudDetalle->getEstado());
                            $objInfoDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalleSolicitudHistorial->setIpCreacion($strIpCreacion);
                            $objInfoDetalleSolicitudHistorial->setObservacion($strObservacionHistorial);
                            $objInfoDetalleSolicitudHistorial->setUsrCreacion($strUsuario);

                            if( $strAccion == "rechazar" )
                            {
                                $objInfoDetalleSolicitudHistorial->setMotivoId($intIdMotivoRechazo);
                            }//( $strAccion == "rechazar" )
                            
                            $emComercial->persist($objInfoDetalleSolicitudHistorial);
                        }//( $intIdSolicitud > 0 )
                    }//foreach($arraySolicitudesSeleccionadas as $intIdSolicitud)
                }
                else
                {
                    throw new \Exception('No se han seleccionado solicitudes para ser procesadas.');
                }//( !empty($arraySolicitudesSeleccionadas) )
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros necesarios para procesar las solicitudes. Accion('.$strAccion.'), '.
                                     'Solicitudes('.$strSolicitudesSeleccionadas.').');
            }//( !empty($strAccion) && !empty($strSolicitudesSeleccionadas) )
            
            $emComercial->flush();
            $emComercial->getConnection()->commit();
            
            if( $strAccion == "rechazar" )
            {
                $arrayRespuesta['strMensajeConfirmacion'] = 'Se han rechazado con éxito las solicitudes seleccionadas.';
            }
            else if( $strAccion == "aprobar" )
            {
                $arrayRespuesta['strMensajeConfirmacion'] = 'Se han aprobado con éxito las solicitudes seleccionadas.';
            }

            $arrayRespuesta['strMensajeError']        = null;
        }
        catch(\Exception $e)
        {
            if(!empty($strMensajeUsuario))
            {
                $arrayRespuesta['strMensajeConfirmacion'] = $strMensajeUsuario;
            }
            else
            {
                $arrayRespuesta['strMensajeConfirmacion'] = null;
            }

            $arrayRespuesta['strMensajeError'] = 'Error al procesar las solicitudes seleccionadas por el usuario.';

            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.AutorizarSolicitudesClientesController.aprobarRechazarSolicitudesClienteAction', 
                                       'Error al aprobar/rechazar las solicitudes seleccionadas por el usuario. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );

            if($emComercial->getConnection()->isTransactionActive())
            {
               $emComercial->getConnection()->rollback();
            }

            $emComercial->getConnection()->close();
        }

        $objJsonResponse->setData($arrayRespuesta);

        return $objJsonResponse;
    }


    /**
     * Documentación para la función 'validarSolicitudesClientesAction'
     * 
     * Método que valida que si ya existen solicitudes en estado pendiente para un servicio.
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 04-05-2017
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.1 26-11-2019 Se agrega compatibilidad para validación de solictudes de cambio masivo de vendedor
     * 
     * @return Response $objResponse  Retorna un string con el mensaje de error en caso de existir o con un 'OK' en caso de no existir ninguna
     *                                    solicitud creada.
     */
    public function validarSolicitudesClientesAction()
    {
        $objResponse                   = new Response();
        $emComercial                   = $this->getDoctrine()->getManager('telconet');
        $serviceUtil                   = $this->get('schema.Util');
        $objRequest                    = $this->get('request');
        $objSession                    = $objRequest->getSession();
        $strIpCreacion                 = $objRequest->getClientIp();
        $strUsuario                    = $objSession->get('user');
        $intIdPersonaEmpresaRolOrigen  = $objSession->get('intIdPersonaEmpresaRolOrigen', 0);
        $intIdPersonaEmpresaRolDestino = $objSession->get('intIdPersonaEmpresaRolDestino', 0);
        $strSolicitudCambioMasivo      = $objSession->get('strSolicitudCambioMasivo', 'N');
        $strMensaje                    = "OK";
        
        try
        {
            $strServiciosSelected       = $objRequest->request->get('strServiciosSelected') ? $objRequest->request->get('strServiciosSelected') 
                                          : null;
            $strNombresSolicitudes      = $objRequest->request->get('strNombreSolicitud') ? $objRequest->request->get('strNombreSolicitud') : null;
            $strCaracteristicaSolicitud = $objRequest->request->get('strCaracteristicaSolicitud') 
                                          ? $objRequest->request->get('strCaracteristicaSolicitud') : null;
            $intIdServicioComision      = $objRequest->request->get('intIdServicioComision')?$objRequest->request->get('intIdServicioComision'):0;

            if((!empty($strSolicitudCambioMasivo) && $strSolicitudCambioMasivo === 'S') && (empty($strNombresSolicitudes) ||
                    empty($strCaracteristicaSolicitud) || empty($intIdPersonaEmpresaRolOrigen) || empty($intIdPersonaEmpresaRolDestino)))
            {
                    throw new \Exception('No se han enviado todos los parámetros necesarios para 
                        validar las solicitudes en estado pendiente, Solicitudes('.$strNombresSolicitudes.'),
                        Caracteristica('.$strCaracteristicaSolicitud.').');
            }

            if( !empty($strServiciosSelected) && !empty($strNombresSolicitudes) && !empty($strCaracteristicaSolicitud) )
            {
                $arrayServiciosSelected = explode('|', $strServiciosSelected);
                
                if( !empty($arrayServiciosSelected) )
                {
                    $intServiciosConSolicitudesCreadas = 0;
                    $strServiciosConSolicitudesCreadas = "Los siguientes servicios ya tienen solicitud en estado <b>Pendiente</b>:<br/><br/>";
                    $arrayNombreSolicitudes            = explode('|', $strNombresSolicitudes);
                    $arrayCaracteristicaSolicitud      = explode('|', $strCaracteristicaSolicitud);
                    
                    foreach($arrayServiciosSelected as $intIdServicio)
                    {
                        if( !empty($arrayNombreSolicitudes) )
                        {
                            foreach($arrayNombreSolicitudes as $strNombreSolicitud)
                            {
                                if( !empty($strNombreSolicitud) )
                                {
                                    if( !empty($arrayCaracteristicaSolicitud) )
                                    {
                                        foreach($arrayCaracteristicaSolicitud as $strCaracteristicaSolicitud)
                                        {
                                            if( !empty($strCaracteristicaSolicitud) )
                                            {
                                                $arrayParametrosSolicitudes = array('strEstadoSolicitud'         => 'Pendiente',
                                                                                    'intIdServicio'              => $intIdServicio,
                                                                                    'strTipoSolicitud'           => $strNombreSolicitud,
                                                                                    'strCaracteristicaSolicitud' => $strCaracteristicaSolicitud);
                                                
                                                if( $strCaracteristicaSolicitud == "CAMBIO_COMISION" )
                                                {
                                                    $arrayParametrosSolicitudes['intValor'] = $intIdServicioComision;
                                                }//( $strCaracteristicaSolicitud == "CAMBIO_COMISION" )

                                                $arraySolicitudes = $emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                                                ->getDetalleSolicitudesByCriterios( $arrayParametrosSolicitudes );

                                                if( isset($arraySolicitudes['total']) && $arraySolicitudes['total'] > 0 )
                                                {
                                                    $intServiciosConSolicitudesCreadas++;

                                                    $objInfoServicio = $emComercial->getRepository("schemaBundle:InfoServicio")
                                                                                   ->findOneById($intIdServicio);

                                                    if( !is_object($objInfoServicio) )
                                                    {
                                                        throw new \Exception('No se han encontrado la información del servicio seleccionado.('.
                                                                             $intIdServicio.')');
                                                    }//( !is_object($objInfoServicio) )

                                                    $objInfoPunto = $objInfoServicio->getPuntoId();

                                                    if( !is_object($objInfoPunto) )
                                                    {
                                                        throw new \Exception('No se han encontrado la información del punto del servicio '.
                                                                             'seleccionado.('.$intIdServicio.')');
                                                    }//( !is_object($objInfoPunto) )

                                                    $strServiciosConSolicitudesCreadas .= "<div style='border-bottom: 1px solid #000;'> Tipo de ".
                                                                                          "solicitud: <b>".$strNombreSolicitud."</b><br/>".
                                                                                          "Cambio a realizar: <b>".$strCaracteristicaSolicitud.
                                                                                          "</b><br/>Login: <b>".$objInfoPunto->getLogin()."</b>".
                                                                                          "<br/>Descripcion Servicio: <b>".
                                                                                          $objInfoServicio->getDescripcionPresentaFactura().
                                                                                          "</b></div><br/>";
                                                }//( isset($arraySolicitudes['total']) && $arraySolicitudes['total'] > 0 )
                                            }//( !empty($strCaracteristicaSolicitud) )
                                        }//foreach($arrayCaracteristicaSolicitud as $strCaracteristicaSolicitud)
                                    }
                                    else
                                    {
                                        throw new \Exception('No se han encontrado las características para validar las solicitudes.');
                                    }//( !empty($arrayCaracteristicaSolicitud) )
                                }//( !empty($strNombreSolicitud) )
                            }//foreach($arrayNombreSolicitudes as $strNombreSolicitud)
                        }
                        else
                        {
                            throw new \Exception('No se ha enviado el nombre de la solicitud a validar.');
                        }//( !empty($arrayNombreSolicitudes) )
                    }//foreach($arrayServiciosSelected as $intIdServicio)
                    
                    if( $intServiciosConSolicitudesCreadas > 0 )
                    {
                        $strMensaje = $strServiciosConSolicitudesCreadas;
                    }//( $intServiciosConSolicitudesCreadas > 0 )
                }
                else
                {
                    throw new \Exception('No se han encontrado servicios para verificar las solicitudes creadas en estado pendiente.');
                }//( !empty($arrayServiciosSelected) )
            }
            else
            {
                throw new \Exception('No se han enviado todos los parámetros necesarios para validar las solicitudes en estado pendiente de los '.
                                     'servicios. Servicios('.$strServiciosSelected.'), Solicitudes('.$strNombresSolicitudes.'), Caracteristica('.
                                     $strCaracteristicaSolicitud.').');
            }//( !empty($strServiciosSelected) && !empty($strNombresSolicitudes) && !empty($strCaracteristicaSolicitud) )
        }
        catch(\Exception $e) 
        {
            $strMensaje = "Error al validar si existen solicitudes en estado pendiente del servicio.";
            
            $serviceUtil->insertError( 'Telcos+', 
                                       'ComercialBundle.AutorizarSolicitudesClientesController.validarSolicitudesClientesAction', 
                                       'Error al validar las solicitudes a aprobar/rechazar. '.$e->getMessage(), 
                                       $strUsuario, 
                                       $strIpCreacion );
        }
        
        $objResponse->setContent($strMensaje);
        
        return $objResponse;
    }

    /**
     * Documentación para la función 'aprobarRechazarSolicitudesClienteMasivoAction'
     *
     * Método que realiza la acción de aprobar y/o rechazar las solicitudes de cambio masivo de vendedor
     *
     * @author Christian Jaramillo <cjaramilloe@telconet.ec>
     * @version 1.0 30-11-2019
     *
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.1 - 24-02-2021 - Se agrega lógica para sincronizar los cambio en TelcoCRM.
     *
     * @author Brayan Ordoñez Oñate <boordonez@telconet.ec>
     * @version 1.2 - 10-04-2023 - Se agrega lógica para obtener respuesta de sincronizacion en TelcoCRM con la finalidad de:
     *                             -Guardar por cada cliente el estatus en historial cliente.
     *                             -Enviar un correo con el status de todos lo clientes a vendedores y subgerentes respectivos.
     * 
     * @return JsonResponse $objJsonResponse  Retorna un string en formato json con los motivos de rechazo que el usuario podrá seleccionar.
     */
    public function aprobarRechazarSolicitudesClienteMasivoAction()
    {
        $objJsonResponse             = new JsonResponse();
        $emComercial                 = $this->getDoctrine()->getManager("telconet");
        $emGeneral                   = $this->getDoctrine()->getManager("telconet_general");
        $serviceTelcoCrm             = $this->get('comercial.ComercialCrm');
        $objRequest                  = $this->get('request');
        $objDateActual               = new \DateTime('now');
        $strHostScripts              = $this->container->getParameter('host_scripts');
        $serviceEnvioPlantilla       = $this->get('soporte.EnvioPlantilla');
        $objSession                  = $objRequest->getSession();
        $strIpCreacion               = $objRequest->getClientIp();
        $strUsuario                  = $objSession->get('user');
        $serviceUtil                 = $this->get('schema.Util');
        $strCodEmpresa               = $objSession->get('idEmpresa');
        $intIdOficina                = $objSession->get('idOficina');
        $strAccion                   = $objRequest->get('strAccion', '');
        $intIdMotivoRechazo          = $objRequest->get('intIdMotivoRechazo', 0);
        $strSolicitudesSeleccionadas = $objRequest->get('strSolicitudesSeleccionadas', '');
        $strPrefijoEmpresa           = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa'):"";
        $arrayRespuesta              = array('strMensajeConfirmacion' => '', 'strMensajeError' => '');
        $strEstadoPersonaEmpresaRol  = "";
        $arrayDestinatarios          = array();
        $strLoginVendOrigen          = "";
        $strLoginVendDestino         = "";
        $strVendOrigen               = "";
        $strVendDestino              = "";
        $strUsuarioSolicitud         = "";
        $arrayResponse               = array();
        $arrayClientes               = array();
        $emComercial->getConnection()->beginTransaction();

        try
        {
            //Se verifica que se envie la accion y las solicitudes que se van a procesar
            if(!empty($strAccion) && !empty($strSolicitudesSeleccionadas))
            {
                if($strAccion == "rechazar" && empty($intIdMotivoRechazo))
                {
                    throw new \Exception('No se ha selecionado el motivo de rechazo de las solicitudes.');
                }

                //Se obtiene la solicitud por cambio de vendedor
                $objCambioVendedorSolicitud = $emComercial->getRepository("schemaBundle:AdmiTipoSolicitud")
                    ->findOneBy(array('estado'               => 'Activo',
                                      'descripcionSolicitud' => 'SOLICITUD CAMBIO MASIVO CLIENTES VENDEDOR'));

                if(!is_object($objCambioVendedorSolicitud))
                {
                    throw new \Exception('No se encontró el tipo de solicitud por cambio masivo de vendedor.');
                }

                $strSolicitudEstado = ($strAccion == 'aprobar') ? 'aprobado' : 'rechazado';
                $arrayIdSolicitudes = explode('|', $strSolicitudesSeleccionadas);

                $arraySolicitudesPendientes = $emGeneral->getRepository("schemaBundle:InfoLog")
                    ->findBy(array('aplicacion' =>  $arrayIdSolicitudes,
                                    'accion' =>     $objCambioVendedorSolicitud->getId(),
                                    'estado' =>     'Pendiente'));

                if(is_null($arraySolicitudesPendientes) || !is_array($arraySolicitudesPendientes) ||
                    count($arraySolicitudesPendientes) <= 0)
                {
                    throw new \Exception('No se encontraron las solicitudes pendientes por cambio masivo de vendedor.');
                }

                foreach($arraySolicitudesPendientes as $objSolicitudPendiente)
                {
                    $arrayRespuestaMasiva = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                        ->aprobarRechazarSolicitudesMasivo(array('strAccion'                => $strAccion,
                                                                 'intIdSolicitud'           => $objSolicitudPendiente->getAplicacion(),
                                                                 'arrayIdClientes'          => explode(',', $objSolicitudPendiente->getDescripcion()),
                                                                 'intIdVendedorOrigen'      => $objSolicitudPendiente->getClase(),
                                                                 'intIdVendedorDestino'     => $objSolicitudPendiente->getMetodo(),
                                                                 'intIdMotivoRechazo'       => $intIdMotivoRechazo,
                                                                 'strCambiarANivelPunto'    => 'S',
                                                                 'strCambiarANivelServicio' => 'S',
                                                                 'intIdOficina'             => $intIdOficina,
                                                                 'strCodEmpresa'            => $strCodEmpresa,
                                                                 'strUsuario'               => $strUsuario,
                                                                 'strIp'                    => $strIpCreacion));
                    $arrayIdClientes        = explode(',', $objSolicitudPendiente->getDescripcion());
                    $arrayIdentificacionClt = array();
                    $strUsuarioSolicitud    = $objSolicitudPendiente->getUsrCreacion();
                    if(!empty($arrayIdClientes) && is_array($arrayIdClientes))
                    {
                        foreach($arrayIdClientes as $intIdClt)
                        {
                            if(!empty($intIdClt))
                            {
                                $objInfoPersonaRolClt = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->find($intIdClt);
                                if(is_object($objInfoPersonaRolClt) && !empty($objInfoPersonaRolClt))
                                {
                                    $objInfoPersonaClt = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                     ->find($objInfoPersonaRolClt->getPersonaId()->getId());
                                    if(is_object($objInfoPersonaClt) && !empty($objInfoPersonaClt))
                                    {
                                        array_push($arrayIdentificacionClt, array("identificacionCliente" => 
                                                                                  $objInfoPersonaClt->getIdentificacionCliente(),
                                                                                  "razonSocial" => 
                                                                                  $objInfoPersonaClt->getRazonSocial()?
                                                                                  $objInfoPersonaClt->getRazonSocial():
                                                                                  $objInfoPersonaClt->getNombres()." ".
                                                                                  $objInfoPersonaClt->getApellidos(),
                                                                                  "idCliente" => $intIdClt));
                                    }
                                }
                            }
                        }
                        $objInfoPersonaRolVendOrigen  = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->find($objSolicitudPendiente->getClase());
                        if(is_object($objInfoPersonaRolVendOrigen) && !empty($objInfoPersonaRolVendOrigen))
                        {
                            $objInfoPersonaVendOrigen = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                    ->find($objInfoPersonaRolVendOrigen->getPersonaId()->getId());
                        }
                        $objInfoPersonaRolVendDestino = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->find($objSolicitudPendiente->getMetodo());
                        if(is_object($objInfoPersonaRolVendDestino) && !empty($objInfoPersonaRolVendDestino))
                        {
                            $objInfoPersonaVendDestino = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                     ->find($objInfoPersonaRolVendDestino->getPersonaId()->getId());
                        }
                        if((!empty($objInfoPersonaVendOrigen)  && is_object($objInfoPersonaVendOrigen))  &&
                           (!empty($objInfoPersonaVendDestino) && is_object($objInfoPersonaVendDestino)) &&
                           (!empty($arrayIdentificacionClt)    && is_array($arrayIdentificacionClt)))
                        {
                            $strLoginVendOrigen   = $objInfoPersonaVendOrigen->getLogin();
                            $strLoginVendDestino  = $objInfoPersonaVendDestino->getLogin();
                            $strVendOrigen        = $objInfoPersonaVendOrigen->getNombres()." ".$objInfoPersonaVendOrigen->getApellidos();
                            $strVendDestino       = $objInfoPersonaVendDestino->getNombres()." ".$objInfoPersonaVendDestino->getApellidos();

                            $arrayParametrosCrm   = array("strLoginVendOrigen"     => $strLoginVendOrigen,
                                                          "strLoginVendDestino"    => $strLoginVendDestino,
                                                          "arrayIdentificacionClt" => $arrayIdentificacionClt,
                                                          "strPrefijoEmpresa"      => $strPrefijoEmpresa, 
                                                          "strCodEmpresa"          => $strCodEmpresa);
                            $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametrosCrm,
                                                          "strOp"                  => 'editVendedor',
                                                          "strFuncion"             => 'procesar');
                        }
                    }
                    if(isset($arrayRespuestaMasiva['intStatusError']))
                    {
                        switch($arrayRespuestaMasiva['intStatusError'])
                        {
                            case 0:
                            {   

                                //Correo del vendedor origen
                                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($strLoginVendOrigen,
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

                                //Correo del subgerente vendedor origen
                                $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->getSubgerentePorLoginVendedor(array("strLogin"=>$strLoginVendOrigen));
                                if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                                {
                                    $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                                    $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                        ->getContactosByLoginPersonaAndFormaContacto(
                                                                            $arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],"Correo Electronico");
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

                                //Correo del vendedor destino
                                $arrayCorreos = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($strLoginVendDestino,
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


                                //Correo del subgerente vendedor destino
                                $arrayResultadoCorreo    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                        ->getSubgerentePorLoginVendedor(array("strLogin"=>$strLoginVendDestino));
                                if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                                {
                                    $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                                    $arrayCorreos         = $emComercial->getRepository('schemaBundle:InfoPersona')
                                                                        ->getContactosByLoginPersonaAndFormaContacto(
                                                                            $arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],"Correo Electronico");
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
                                                            ->getContactosByLoginPersonaAndFormaContacto($strUsuarioSolicitud,
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

                                if($strAccion == 'aprobar')
                                {
                                    //Receptamos la respuesta de la sincronizacion de clientes en el TelcoCRM
                                    $arrayResponse = $serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                                    if(isset($arrayResponse["resultado"]) && !empty($arrayResponse["resultado"]))
                                    {
                                        $arrayClientes = $arrayResponse['resultado']->clientes;                                           
                                        if(!empty($arrayClientes))
                                        {
                                            foreach($arrayClientes as $client)
                                            {
                                                $objPersonaEmpresaRol = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                                    ->find($client->idCliente);

                                                if(!is_object($objPersonaEmpresaRol) && empty($objPersonaEmpresaRol))
                                                {
                                                    throw new \Exception("No se encuentra el rol del empleado, favor comunicarse con sistemas.");
                                                }

                                                if(is_object($objPersonaEmpresaRol))
                                                {
                                                    $strEstadoPersonaEmpresaRol = $objPersonaEmpresaRol->getEstado();
                                                }

                                                //Grabamos el estado en la tabla de historial de cliente
                                                $objInfoPersonaEmpresaRolHistorial = new InfoPersonaEmpresaRolHisto();
                                                $objInfoPersonaEmpresaRolHistorial->setEstado($strEstadoPersonaEmpresaRol);
                                                $objInfoPersonaEmpresaRolHistorial->setFeCreacion($objDateActual);
                                                $objInfoPersonaEmpresaRolHistorial->setIpCreacion($strIpCreacion);
                                                $objInfoPersonaEmpresaRolHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                                                $objInfoPersonaEmpresaRolHistorial->setUsrCreacion($strUsuario);
                                                $objInfoPersonaEmpresaRolHistorial->setObservacion($client->mensaje);
                                                $emComercial->persist($objInfoPersonaEmpresaRolHistorial);
                                                $emComercial->flush();
                                            }

                                            $emComercial->getConnection()->commit();
                                            $emComercial->getConnection()->close();	

                                            $strCuerpoCorreo      = "El presente correo es para indicarle el estado de sincronización".
                                                " de los clientes en TelcoCRM con la solicitud de cambio de vendedor masivo:";
                                            $arrayParametrosMail  = array("arrayClientes"       => $arrayClientes,
                                                                        "strUrl"              => $strHostScripts,
                                                                        "strVendOrigen"       => ucwords(strtolower($strVendOrigen)),
                                                                        "strVendDestino"      => ucwords(strtolower($strVendDestino)),
                                                                        "strCuerpoCorreo"     => $strCuerpoCorreo);
        
                                            //Envio de correo
                                            $serviceEnvioPlantilla->generarEnvioPlantilla("NOTIFICACIÓN DE CAMBIO DE VENDEDOR MASIVO POR CLIENTE",
                                                                                        $arrayDestinatarios,
                                                                                        "NOTIFICACION2",
                                                                                        $arrayParametrosMail,
                                                                                        $strPrefijoEmpresa,
                                                                                        "",
                                                                                        "",
                                                                                        null,
                                                                                        true,
                                                                                        "notificaciones_telcos@telconet.ec");   
                                        }
                                        else
                                        {
                                            throw new \Exception('No se encontró clientes en la respuesta de getRequestCRM para ' . $strAccion .
                                            ' las solicitudes (' . $strSolicitudesSeleccionadas . ').');
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception('La respuesta de getRequestCRM esta vacia para ' . $strAccion .
                                        ' las solicitudes (' . $strSolicitudesSeleccionadas . ').');
                                    }
                                }
                                else
                                {

                                    $objAdmiMotivo = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivoRechazo);

                                    $strCuerpoCorreo      = "El presente correo es para indicarle el rechazo de la solicitud de cambio".
                                        " de vendedor masivo por cliente";
                                    $strMotivoRechazo     = $objAdmiMotivo->getNombreMotivo();
                                    $arrayParametrosMail  = array("arrayClientes"       => $arrayIdentificacionClt,
                                                                  "strCuerpoCorreo"     => $strCuerpoCorreo,
                                                                  "strVendOrigen"       => ucwords(strtolower($strVendOrigen)),
                                                                  "strVendDestino"      => ucwords(strtolower($strVendDestino)),
                                                                  "strMotivoRechazo"    => $strMotivoRechazo);

                                    //Envio de correo
                                    $serviceEnvioPlantilla->generarEnvioPlantilla("NOTIFICACIÓN DE CAMBIO DE VENDEDOR MASIVO POR CLIENTE",
                                                                                $arrayDestinatarios,
                                                                                "NOTIFICACION3",
                                                                                $arrayParametrosMail,
                                                                                $strPrefijoEmpresa,
                                                                                "",
                                                                                "",
                                                                                null,
                                                                                true,
                                                                                "notificaciones_telcos@telconet.ec");  

                                }//($strAccion == 'aprobar')
                                break;
                            }
                            case 1:
                            case 2:
                            {
                                $arrayRespuesta['strMensajeConfirmacion'] .= 'Error al procesar la solicitud <b>'.
                                    $objSolicitudPendiente->getId() . '</b>.<br/><br/>';
                                break;
                            }

                            default:
                            {
                                $arrayRespuesta['strMensajeConfirmacion'] = '';
                                break;
                            }
                        }
                    }
                    else
                    {
                        $arrayRespuesta['strMensajeConfirmacion'] .= 'Error al procesar la solicitud <b>'. $objSolicitudPendiente->getId() .
                            '</b>.<br/><br/>';
                    }
                }

                if(empty($arrayRespuesta['strMensajeConfirmacion'] ))
                {
                    $arrayRespuesta['strMensajeConfirmacion'] = 'Se han ' . $strSolicitudEstado . ' con éxito las solicitudes seleccionadas.';
                }

            }
            else
            {
                throw new \Exception('No se ha enviado los parámetros necesarios para ' . $strAccion .
                    ' las solicitudes (' . $strSolicitudesSeleccionadas . ').');
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta['strMensajeConfirmacion'] = 'Error al procesar las solicitudes seleccionadas por el usuario.';

            $serviceUtil->insertError('Telcos+',
                                      __METHOD__,
                                      'Error al ' . $strAccion . ' las solicitudes '. $strSolicitudesSeleccionadas .
                                      'seleccionadas por el usuario. '. $e->getMessage(),
                                      $strUsuario,
                                      $strIpCreacion);

            if($emComercial->getConnection()->isTransactionActive())
            {
                $emComercial->getConnection()->rollback();
            }

        }

        $emComercial->getConnection()->close();
        $emGeneral->getConnection()->close();

        $objJsonResponse->setData($arrayRespuesta);

        return $objJsonResponse;
    }

    /**
     * Documentación para la función 'getClientesPorSolicitudAjaxAction'
     *
     * Obtiene los clientes de la solicitud a ser reasignados de vendedor
     *
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.0 26-11-2019
     * @since 1.0
     * @throws $objException
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     */
    public function getClientesPorSolicitudAjaxAction()
    {
        $objJsonResponse        = new JsonResponse();
        $objReturnResponse      = new ReturnResponse();

        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $serviceUtil            = $this->get('schema.Util');
        $intIdSolicitud         = intval($objRequest->get('intIdSolicitud', 0));
        $arrayParametros        = array();

        $emComercial->getConnection()->beginTransaction();

        try
        {
            //Termina el metodo si no se envía el id de la solicitud
            if(!isset($intIdSolicitud) || empty($intIdSolicitud) || $intIdSolicitud <= 0)
            {
                throw new \Exception(" No se está enviando la información de la solicitud.");
            }

            $arrayParametros['intIdSolicitud'] = $intIdSolicitud;
            $arrayRespuesta = $emComercial->getRepository('schemaBundle:InfoPersona')
                ->getClientesPorSolicitud($arrayParametros);

            if(is_array($arrayRespuesta) && is_array($arrayRespuesta['registros']) && count($arrayRespuesta['registros']) > 0)
            {
                $objReturnResponse->setRegistros($arrayRespuesta['registros']);
                $objReturnResponse->setTotal($arrayRespuesta['total']);
                $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_PROCESS_SUCCESS);
            }
            else
            {
                $objReturnResponse->setRegistros(array());
                $objReturnResponse->setTotal(0);
                $objReturnResponse->setStrStatus($objReturnResponse::NOT_RESULT);
                $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_NOT_RESULT .
                    ' El vendedor no tiene clientes asginados');
            }
        }
        catch(\Exception $objException)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . " " . $objException->getMessage());
            $serviceUtil->insertError('Telcos+',
                __METHOD__,
                $objReturnResponse->getStrMessageStatus(),
                $objSession->get('user'),
                $objRequest->getClientIp());
        }

        $emComercial->getConnection()->close();
        $objJsonResponse->setData($objReturnResponse);

        return $objJsonResponse;
    }
}