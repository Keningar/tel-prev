<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Vendedor controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Reportes del Vendedor
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 25-11-2015
 */
class VendedorController extends Controller
{
    const CARACTERISTICA_META_BRUTA  = 'META BRUTA';
    const CARACTERISTICA_META_ACTIVA = 'META ACTIVA';
    

    /**
     * @Secure(roles="ROLE_301-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra la pantalla inicial con el reporte del vendedor en sessión.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 25-11-2015
     */
    public function indexAction()
    {
        return $this->render( 'comercialBundle:Vendedor:index.html.twig' );
    }
    
    
    /**
     * @Secure(roles="ROLE_301-7")
     *
     * Documentación para el método 'gridVentasAction'.
     *
     * Retorna la información sobre las ventas 'Brutas', 'Activas', 'Rechazadas' y 'Canceladas' del vendedor en sessión.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 25-11-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 30-06-2016 - Se agrega que se busque por tipo de rol 'Empleado' y 'Personal Externo'
     * @author Jorge Veliz <jlveliz@telconet.ec>
     * @version 1.2 20-10-2021 - Cambia el metodo que extrae los reportes de los  vendedores
     */
    public function gridVentasAction()
    {
        $response               = new JsonResponse();
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $emComercial            = $this->getDoctrine()->getManager('telconet');
        $emGeneral              = $this->getDoctrine()->getManager('telconet_general');
        $serviceComercial       = $this->get('comercial.InfoPersonaEmpresaRol');
        $intIdPersonEmpresaRol  = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa           = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento      = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strUserSession         = $objSession->get('user') ? $objSession->get('user') : '';
        $strFechaBusqueda       = $objRequest->query->get('fecha') ? $objRequest->query->get('fecha') : '';
        $arrayInformacionVentas = array();
        
        $arrayTipoVentas = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                     ->get('REPORTE_VENTAS_VENDEDOR', 'COMERCIAL', '', '', '', '', '', '');
        
        $objVendedor   = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findOneById($intIdPersonEmpresaRol);
        $intIdReportaA = $objVendedor->getReportaPersonaEmpresaRolId() ? $objVendedor->getReportaPersonaEmpresaRolId() : $intIdPersonEmpresaRol;
        
        $intContador = 0;
        
        foreach( $arrayTipoVentas  as $arrayTipoVenta )
        {
            $intContador++;
            
            $strTipoVenta = $arrayTipoVenta['valor1'];
            
            $arrayItem                 = array();
            $arrayItem['strTipoVenta'] = ucwords(strtolower($strTipoVenta)); 
            
            $arrayParametros = array(
                                        'empresa'                 => $intIdEmpresa,
                                        'strTipoVentas'         => $strTipoVenta,
                                        'strFechaBusqueda'        => $strFechaBusqueda,
                                        'strCaracteristicaBruta'  => self::CARACTERISTICA_META_BRUTA,
                                        'strCaracteristicaActiva' => self::CARACTERISTICA_META_ACTIVA,
                                        'arrayParametrosBusqueda' => array(
                                                                              'usuario'      => $intIdReportaA,
                                                                              'departamento' => $intIdDepartamento,
                                                                              'empresa'      => $intIdEmpresa,
                                                                              'strTipoRol'   => array('Empleado', 'Personal Externo'),
                                                                              'criterios'    => array( 'login' => $strUserSession )
                                                                          )
                                    );

           
           $arrayResultados = $serviceComercial->getReporteTipoVentasVendedores($arrayParametros);

            if( isset($arrayResultados['encontrados']) )
            {
                if( $arrayResultados['encontrados'] )
                {
                    $arrayTmpEncontrado = $arrayResultados['encontrados'];
                    
                    $arrayItem['strMeta']         = $arrayTmpEncontrado['strMeta'.$strTipoVenta]; 
                    $arrayItem['strVendido']      = $arrayTmpEncontrado['intCumplimiento'.$strTipoVenta];
                    $arrayItem['intSumaVendido']  = $arrayTmpEncontrado['floatMontoTotal'.$strTipoVenta];
                    $arrayItem['intCumplimiento'] = $arrayTmpEncontrado['intPorcentaje'.$strTipoVenta];
                }//( $arrayResultados['encontrados'] )
            }//( isset($arrayResultados['encontrados']) )
            
            $arrayInformacionVentas[] = $arrayItem;
            
        }//( $arrayTipoVentas  as $strTipoVenta )
        
        $response->setData( array('total' => $intContador, 'encontrados' => $arrayInformacionVentas) );
        
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_301-2938")
     *
     * Documentación para el método 'reporteVentasAction'.
     *
     * Muestra el reporte de ventas del vendedor.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 30-11-2015
     */
    public function reporteVentasAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : 0;
        
        //Id que corresponde a la acción de buscar por empresa entre los filtros del reporte del vendedor
        $boolPermisoFiltroEmpresa = $this->get('security.context')->isGranted('ROLE_301-3017');
        
        return $this->render( 'comercialBundle:Vendedor:reporteVentas.html.twig', 
                               array('boolFiltroEmpresa' => $boolPermisoFiltroEmpresa, 'strPrefijoEmpresa' => $strPrefijoEmpresa) );
    }
    
    
    /**
     * @Secure(roles="ROLE_301-2939")
     *
     * Documentación para el método 'gridReporteVentasAction'.
     *
     * Retorna la información detallada sobre las ventas que ha tenido el vendedor en sessión.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 30-11-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 08-03-2016 - Se modifica para que cuando no retorne información en el nombre cliente muestra la información de razón
     *                           social.
     */
    public function gridReporteVentasAction()
    {
        $response             = new JsonResponse();
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $arrayVentasTotales   = array();
        $strCodEmpresa        = '';
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $emFinanciero         = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceComercial     = $this->get('comercial.InfoPersonaEmpresaRol');

        $intIdPersonEmpresaRol        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa                 = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strUserSession               = $objSession->get('user') ? $objSession->get('user') : '';
        $intStart                     = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                     = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $intIdPunto                   = $objRequest->query->get('idPunto') ? $objRequest->query->get('idPunto') : 0;
        $strEstadoServicio            = $objRequest->query->get('estadoServicio') ? $objRequest->query->get('estadoServicio') : '';
        $intPlan                      = $objRequest->query->get('idPlan') ? $objRequest->query->get('idPlan') : 0;
        $strNombreCliente             = $objRequest->query->get('nombreCliente') ? $objRequest->query->get('nombreCliente') : '';
        $strApellidoCliente           = $objRequest->query->get('apellidoCliente') ? $objRequest->query->get('apellidoCliente') : '';
        $strPrefijoEmpresa            = $objRequest->query->get('empresa') ? $objRequest->query->get('empresa') : '';
        $intIdJurisdiccion            = $objRequest->query->get('idJurisdiccion') ? $objRequest->query->get('idJurisdiccion') : 0;
        $intIdSector                  = $objRequest->query->get('idSector') ? $objRequest->query->get('idSector') : 0; 
        $strIdentificacionCliente     = $objRequest->query->get('identificacionCliente') ? $objRequest->query->get('identificacionCliente') : 0; 
        $strFechaAprobacionDesde      = $objRequest->query->get('feAprobacionDesde') ? $objRequest->query->get('feAprobacionDesde') : '';
        $arrayFechaAprobacionDesde    = explode('T', $strFechaAprobacionDesde);
        $strFechaAprobacionHasta      = $objRequest->query->get('feAprobacionHasta') ? $objRequest->query->get('feAprobacionHasta') : '';
        $arrayFechaAprobacionHasta    = explode('T', $strFechaAprobacionHasta);
        $strFechaCreacionPuntoDesde   = $objRequest->query->get('feCreacionPuntoDesde') ? $objRequest->query->get('feCreacionPuntoDesde') : '';
        $arrayFechaCreacionPuntoDesde = explode('T', $strFechaCreacionPuntoDesde);
        $strFechaCreacionPuntoHasta   = $objRequest->query->get('feCreacionPuntoHasta') ? $objRequest->query->get('feCreacionPuntoHasta') : '';
        $arrayFechaCreacionPuntoHasta = explode('T', $strFechaCreacionPuntoHasta);
        $strFechaActivacionDesde      = $objRequest->query->get('feActivacionDesde') ? $objRequest->query->get('feActivacionDesde') : '';
        $arrayFechaActivacionDesde    = explode('T', $strFechaActivacionDesde);
        $strFechaActivacionHasta      = $objRequest->query->get('feActivacionHasta') ? $objRequest->query->get('feActivacionHasta') : '';
        $arrayFechaActivacionHasta    = explode('T', $strFechaActivacionHasta);
        $strFechaPlanificacionDesde   = $objRequest->query->get('fePlanificacionDesde') ? $objRequest->query->get('fePlanificacionDesde') : '';
        $arrayFechaPlanificacionDesde = explode('T', $strFechaPlanificacionDesde);
        $strFechaPlanificacionHasta   = $objRequest->query->get('fePlanificacionHasta') ? $objRequest->query->get('fePlanificacionHasta') : '';
        $arrayFechaPlanificacionHasta = explode('T', $strFechaPlanificacionHasta);
                
        if( $strPrefijoEmpresa )
        {
            $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strPrefijoEmpresa);
            
            if( $objEmpresa )
            {
                $strCodEmpresa = $objEmpresa->getId();
            }
        }
        else
        {
            $strCodEmpresa = $intIdEmpresa;
        }
        
        $arrayParametros = array(
                                    'inicio'                    => $intStart,
                                    'limite'                    => $intLimit,
                                    'detalleVentas'             => true,
                                    'puntoCliente'              => $intIdPunto,
                                    'estadosServiciosIncluidos' => $strEstadoServicio,
                                    'idPlan'                    => $intPlan,
                                    'nombreCliente'             => $strNombreCliente,
                                    'apellidoCliente'           => $strApellidoCliente,
                                    'empresa'                   => $strCodEmpresa,
                                    'jurisdiccion'              => $intIdJurisdiccion,
                                    'sector'                    => $intIdSector,
                                    'identificacionCliente'     => $strIdentificacionCliente,
                                    'usuarioVendedor'           => $strUserSession
                                );
        
        
        $arrayTiposFechasConsulta = array('Aprobacion', 'CreacionPunto', 'Activacion', 'Planificacion');
        $arrayTmpFechas           = array();
        
        foreach( $arrayTiposFechasConsulta as $strTipoFecha )
        {
            switch($strTipoFecha)
            {
                case 'Aprobacion':
                    
                    $arrayTmpFechas = array('feInicio' => $arrayFechaAprobacionDesde[0], 'feFinal' => $arrayFechaAprobacionHasta[0]);
                    
                    break;
                
                
                case 'CreacionPunto':
                    
                    $arrayTmpFechas = array('feInicio' => $arrayFechaCreacionPuntoDesde[0], 'feFinal' => $arrayFechaCreacionPuntoHasta[0]);
                    
                    break;
                
                
                case 'Activacion':
                    
                    $arrayTmpFechas = array('feInicio' => $arrayFechaActivacionDesde[0], 'feFinal' => $arrayFechaActivacionHasta[0]);
                    
                    break;
                
                
                case 'Planificacion':
                    
                    $arrayTmpFechas = array('feInicio' => $arrayFechaPlanificacionDesde[0], 'feFinal' => $arrayFechaPlanificacionHasta[0]);
                    
                    break;
                
                
                default:
                    
                    $arrayTmpFechas = array();
            }
            
            $arrayFechasResultados = array();
            $arrayFechasResultados = $serviceComercial->getFechasParaConsultaBaseDatos($arrayTmpFechas);

            if( $arrayFechasResultados )
            {
                if( isset($arrayFechasResultados['feInicio']) )
                {
                    $arrayParametros['fe'.$strTipoFecha.'Inicio'] = $arrayFechasResultados['feInicio'];
                }

                if( isset($arrayFechasResultados['feFinal']) )
                {
                    $arrayParametros['fe'.$strTipoFecha.'Final'] = $arrayFechasResultados['feFinal'];
                }
            }
        }//foreach( $arrayTiposFechasConsulta as $strTipoFecha )
            

        $arrayTmpVentas = array();
        $arrayTmpVentas = $emComercial->getRepository('schemaBundle:InfoPunto')->getVentasByCriterios($arrayParametros);
        
        $intTotal = $arrayTmpVentas['total'] ? $arrayTmpVentas['total'] : 0;
        
        $arrayTmpResultadosVentas = $arrayTmpVentas['resultados'];
        
        $arrayParametrosFechaActivacion = array(
                                                    'intStart'     => 0,
                                                    'intLimit'     => 5000,
                                                    'emFinanciero' => $emFinanciero,
                                                    'emGeneral'    => $emGeneral
                                               );
        
        foreach( $arrayTmpResultadosVentas as $arrayVenta )
        {
            $intIdServicio = $arrayVenta['idServicio'];
            
            $item                           = array();
            $item['intIdServicio']          = $intIdServicio;
            $item['strLoginPunto']          = $arrayVenta['loginPunto'];
            $item['strEstadoServicio']      = $arrayVenta['estadoServicio'];
            $item['strDireccion']           = $arrayVenta['direccion'];
            $item['strNombreJurisdiccion']  = $arrayVenta['nombreJurisdiccion'];
            $item['strSector']              = $arrayVenta['nombreSector'];
            $item['strCliente']             = trim($arrayVenta['nombreCliente']) 
                                              ? ucwords(strtolower(trim($arrayVenta['nombreCliente'])))
                                              : ucwords(strtolower(trim($arrayVenta['razonSocial'])));
            $item['strEmpresa']             = $arrayVenta['nombreEmpresa'];
            $item['strIdentificacion']      = $arrayVenta['identificacionCliente'];
            $item['strUsuarioVendedor']     = $arrayVenta['loginVendedor'];
            $item['strFechaAprobacion']     = '';
            $item['strFechaCreacionPunto']  = $arrayVenta['fechaCreacionPunto'] ? $arrayVenta['fechaCreacionPunto']->format('d/m/Y H:i:s') : '';
            $item['strCoordenadas']         = $arrayVenta['coordenadasPunto'];
            $item['strPrecioVenta']         = $arrayVenta['precioVenta'];
            $item['strVendedor']            = '';
            $item['strPlan']                = '';
            $item['strFechaActivacion']     = '';
            
            $objTmpVendedor = null;
            $objTmpVendedor = $emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($item['strUsuarioVendedor']);
            
            if( $objTmpVendedor )
            {
                $item['strVendedor'] = ucwords(strtolower(trim($objTmpVendedor->getNombres().' '.$objTmpVendedor->getApellidos())));
            }
            
            
            //Para saber el nombre del plan y la fecha de activación
            $entityServicio = $emComercial->getRepository( 'schemaBundle:InfoServicio' )->findOneById( $intIdServicio );
            
            if( $entityServicio )
            {
                $objTmpPlan =  $entityServicio->getPlanId();
                        
                if( $objTmpPlan )
                {
                    $item['strPlan'] = $objTmpPlan->getNombrePlan();
                }
                else
                {
                    $objTmpProducto =  $entityServicio->getProductoId();
                    
                    if( $objTmpProducto )
                    {
                        $item['strPlan'] = $objTmpProducto->getDescripcionProducto();
                    }
                }
                
                if( $item['strEstadoServicio'] == 'Activo' )
                {
                    $arrayParametrosFechaActivacion['intIdServicio'] = $intIdServicio;
                            
                    $item['strFechaActivacion'] = $emComercial->getRepository( 'schemaBundle:InfoServicioTecnico' )
                                                              ->getFechaActivacionServicio($arrayParametrosFechaActivacion);
                    
                    $objInfoContrato = $emComercial->getRepository( 'schemaBundle:InfoContrato' )
                                                    ->findOneBy( array( 'estado'              => 'Activo', 
                                                                        'personaEmpresaRolId' => $arrayVenta['idPersonaEmpresaRol'] ) );
                    
                    if( $objInfoContrato )
                    {
                        $item['strFechaAprobacion'] = $objInfoContrato->getFeAprobacion() 
                                                      ? $objInfoContrato->getFeAprobacion()->format('d/m/Y H:i:s') : '';
                    }
                }
                
                
                /*
                 * Se obtiene el la fecha de aprobación del contrato
                 */
                if( $item['strEstadoServicio'] == 'Rechazada' || $item['strEstadoServicio'] == 'Anulado' )
                {
                    if( $objHistorialServicio )
                    {
                        $intIdMotivo = $objHistorialServicio->getMotivoId();
                        $objMotivo   = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findOneById($intIdMotivo);
                        
                        if( $objMotivo )
                        {
                            $item['strMotivoRechazo'] = $objMotivo->getNombreMotivo();
                        }//( $objMotivo )
                    }//( $objHistorialServicio )
                }//( $item['strEstadoServicio'] == 'Rechazada' || $item['strEstadoServicio'] == 'Anulado' )
                /*
                 * Fin Se obtiene el la fecha de aprobación del contrato
                 */
                
                
                $objHistorialServicio = $emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                    ->findMaxHistorialPorServicio($intIdServicio);
                
                /*
                 * Se Obtiene el motivo de rechazo del servicio
                 */
                if( $item['strEstadoServicio'] == 'Rechazada' || $item['strEstadoServicio'] == 'Anulado' )
                {
                    if( $objHistorialServicio )
                    {
                        $intIdMotivo = $objHistorialServicio->getMotivoId();
                        $objMotivo   = $emGeneral->getRepository("schemaBundle:AdmiMotivo")->findOneById($intIdMotivo);
                        
                        if( $objMotivo )
                        {
                            $item['strMotivoRechazo'] = $objMotivo->getNombreMotivo();
                        }//( $objMotivo )
                    }//( $objHistorialServicio )
                }//( $item['strEstadoServicio'] == 'Rechazada' || $item['strEstadoServicio'] == 'Anulado' )
                /*
                 * Fin de se Obtiene el motivo de rechazo del servicio
                 */
                
                
                /*
                 * Se obtiene la observación agregada en el historial servicio
                 */
                if( $objHistorialServicio )
                {
                    $item['strObservacion'] = $objHistorialServicio->getObservacion();
                }//( $objHistorialServicio )
                /*
                 * Fin de Se obtiene la observación agregada en el historial servicio
                 */
            }//( $entityServicio )
            //Fin Para saber el nombre del plan y la fecha de activación
            
            $arrayVentasTotales[] = $item;
            
        }//foreach( $arrayTmpResultadosVentas as $arrayVenta )
        
        $response->setData( array( 'total' => $intTotal, 'encontrados' => $arrayVentasTotales ) );
        
        return $response;
    }
}
