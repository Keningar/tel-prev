<?php
namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Alignment;

/**
 * Supervisor controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción
 * de Reportes del Supervisor
 *
 * @author Edson Franco <efranco@telconet.ec>
 * @version 1.0 03-09-2015
 */
class SupervisorController extends Controller
{
    const CARACTERISTICA_META_BRUTA  = 'META BRUTA';
    const CARACTERISTICA_META_ACTIVA = 'META ACTIVA';
    

    /**
     * @Secure(roles="ROLE_297-1")
     *
     * Documentación para el método 'indexAction'.
     *
     * Muestra los reportes iniciales correspondientes a las ventas brutas y activas de los
     * vendedores asignados al usuario logueado.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-09-2015
     */
    public function indexAction()
    {
        return $this->render( 'comercialBundle:Supervisor:index.html.twig' );
    }
    
    
    /**
     * @Secure(roles="ROLE_297-2937")
     *
     * Documentación para el método 'gridVentasAction'.
     *
     * Retorna la información sobre las ventas 'Brutas' y 'Activas' de los vendedores asignados
     * al usuario logueado.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-09-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 30-06-2016 - Se agrega que se busque por tipo de rol 'Empleado' y 'Personal Externo'
     */
    public function gridVentasAction()
    {
        $response         = new JsonResponse();
        $objRequest       = $this->get('request');
        $objSession       = $objRequest->getSession();
        $arrayUsuarios    = array();
        $strMeta          = '0';
        $emComercial      = $this->getDoctrine()->getManager('telconet');
        $serviceComercial = $this->get('comercial.InfoPersonaEmpresaRol');

        $intIdPersonEmpresaRol = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento     = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strFechaBusqueda      = $objRequest->query->get('fecha') ? $objRequest->query->get('fecha') : '';
        $strEmpleado           = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : '';
        $strTipoVentas         = $objRequest->query->get('strTipo') ? $objRequest->query->get('strTipo') : 'brutas';
        $intStart              = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit              = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
        $arrayTipoVentas = array($strTipoVentas);
        
        $arrayParametros = array(
                                    'arrayTipoVentas'         => $arrayTipoVentas,
                                    'strFechaBusqueda'        => $strFechaBusqueda,
                                    'strFechaActivacionDesde' => false,
                                    'strCaracteristicaBruta'  => self::CARACTERISTICA_META_BRUTA,
                                    'strCaracteristicaActiva' => self::CARACTERISTICA_META_ACTIVA,
                                    'arrayParametrosBusqueda' => array(
                                                                        'usuario'      => $intIdPersonEmpresaRol,
                                                                        'departamento' => $intIdDepartamento,
                                                                        'empresa'      => $intIdEmpresa,
                                                                        'asignadosA'   => $intIdPersonEmpresaRol,
                                                                        'inicio'       => $intStart,
                                                                        'limite'       => $intLimit,
                                                                        'strTipoRol'   => array('Empleado', 'Personal Externo'),
                                                                        'criterios'    => array( 'nombreEmpleado' => $strEmpleado )
                                                                      )
                                );
       
        $arrayResultados = $serviceComercial->getVentasBrutasYActivasVendedores($arrayParametros);
        
        $response->setData(
                            array(
                                    'total'       => $arrayResultados['intTotal'],
                                    'encontrados' => $arrayResultados['encontrados']
                                 )
                          );
        
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_297-2938")
     *
     * Documentación para el método 'reporteVentasSupervisorAction'.
     *
     * Muestra el reporte de ventas del supervisor.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 10-09-2015
     */
    public function reporteVentasSupervisorAction()
    {
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $strPrefijoEmpresa = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : 0;
        
        $boolPermisoFiltroEmpresa = $this->get('security.context')->isGranted('ROLE_297-3017');
        
        return $this->render( 'comercialBundle:Supervisor:reporteVentasSupervisor.html.twig', 
                               array('boolFiltroEmpresa' => $boolPermisoFiltroEmpresa, 'strPrefijoEmpresa' => $strPrefijoEmpresa) );
    }
    
    
    /**
     * @Secure(roles="ROLE_297-2939")
     *
     * Documentación para el método 'gridReporteVentasSupervisorAction'.
     *
     * Retorna la información detallada sobre las ventas que ha tenido los vendedores 
     * asignados al usuario logueado.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 03-09-2015
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 01-12-2015 - Se modifica para que envía como parámetro a la búsqueda el usuario vendedor y no el usuario de creación.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 08-03-2016 - Se modifica para que cuando no retorne información en el nombre cliente muestra la información de razón
     *                           social.
     * 
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.3 09-05-2016 - Se modifica para que retorne el punto y el canal de venta
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.4 30-06-2016 - Se agrega que se busque por tipo de rol 'Empleado' y 'Personal Externo'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.5 06-07-2016 - Se corrige que se envíe el total de las ventas que retorna el query en su variable "$arrayTmpVentas['total']".
     *                           Adicional se agregan lo filtros de fePlanificacionDesde y fePlanificacionHasta los cuales retorna las ventas Brutas
     *                           del vendedor consultado.
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.6 07-12-2018 - Se modifica para retornar las ventas con los nombres, apellidos de los vendedores y el canal, punto de venta con el fin 
     *                           de optimizar codigo para que consuma menos tiempo.
     */
    public function gridReporteVentasSupervisorAction()
    {
        $response             = new JsonResponse();
        $objRequest           = $this->get('request');
        $objSession           = $objRequest->getSession();
        $arrayLoginVendedores = array();
        $arrayVentasTotales   = array();
        $strCodEmpresa        = '';
        $emComercial          = $this->getDoctrine()->getManager('telconet');
        $emGeneral            = $this->getDoctrine()->getManager('telconet_general');
        $emFinanciero         = $this->getDoctrine()->getManager('telconet_financiero');
        $serviceComercial     = $this->get('comercial.InfoPersonaEmpresaRol');

        $intIdPersonEmpresaRol        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa                 = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento            = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strEmpleado                  = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : '';
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
        $strNombreVendedor            = $objRequest->query->get('nombreVendedor') ? $objRequest->query->get('nombreVendedor') : '';
        $strApellidoVendedor          = $objRequest->query->get('apellidoVendedor') ? $objRequest->query->get('apellidoVendedor') : '';
        $strUsuarioVendedor           = $objRequest->query->get('usuarioVendedor') ? $objRequest->query->get('usuarioVendedor') : '';
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
        $strCanalVenta                = $objRequest->query->get('strCanalVenta') ? $objRequest->query->get('strCanalVenta') : '';
        $strPuntoVenta                = $objRequest->query->get('strPuntoVenta') ? $objRequest->query->get('strPuntoVenta') : '';
                
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
                                    'usuario'       => $intIdPersonEmpresaRol,
                                    'departamento'  => $intIdDepartamento,
                                    'empresa'       => $intIdEmpresa,
                                    'asignadosA'    => $intIdPersonEmpresaRol,
                                    'strTipoRol'    => array('Empleado', 'Personal Externo'),
                                    'criterios'     => array( 'nombreEmpleado' => $strEmpleado )
                                );
        
        $arrayResultados = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->findPersonalByCriterios($arrayParametros);

        $arrayRegistros = $arrayResultados['registros'];

        if( $arrayRegistros )
        {
            foreach($arrayRegistros as $arrayDatos)
            {
                if( $arrayDatos['login'] )
                {
                    $arrayLoginVendedores[] = $arrayDatos['login'];
                }
            }
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
                                    'nombreVendedor'            => $strNombreVendedor,
                                    'apellidoVendedor'          => $strApellidoVendedor,
                                    'canalVenta'                => $strCanalVenta,
                                    'puntoVenta'                => $strPuntoVenta,
                                    'usuarioVendedor'           => $strUsuarioVendedor ? $strUsuarioVendedor : $arrayLoginVendedores,
                                    'banderaReporte'            => true
                                );
        
        
        $arrayTiposFechasConsulta = array('Aprobacion', 'CreacionPunto', 'Activacion', 'Planificacion');
        
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
            $strPuntoVentaTemp = substr($arrayVenta['strPuntoVenta'],0,strpos($arrayVenta['strPuntoVenta'],'|'));
            $strCanalVentaTemp = substr($arrayVenta['strPuntoVenta'],strpos($arrayVenta['strPuntoVenta'],'|')+1);
            
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
            $item['strVendedor']            = $arrayVenta['strVendedor'];
            $item['strPlan']                = '';
            $item['strFechaActivacion']     = '';
            $item['strPuntoVenta']          = $strPuntoVentaTemp ? $strPuntoVentaTemp:'';
            $item['strCanalVenta']          = $strCanalVentaTemp ? $strCanalVentaTemp:'';

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
            }
            //Fin Para saber el nombre del plan y la fecha de activación
            
            
            if( !empty($strCanalVenta) )
            {
                if( $strCanalVenta == $item['strCanalVenta'] )
                {
                    if( !empty($strPuntoVenta) )
                    {
                        if( $strPuntoVenta == $item['strPuntoVenta'] )
                        {
                            $arrayVentasTotales[] = $item;
                        }//( $strPuntoVenta == $item['strPuntoVenta'] )
                    }//( !empty($strPuntoVenta) )
                    else
                    {
                        $arrayVentasTotales[] = $item;
                    }
                }//( $strCanalVenta == $item['strCanalVenta'] )
            }
            else
            {
                $arrayVentasTotales[] = $item;
            }//( !empty($strCanalVenta) )
        }//foreach( $arrayTmpResultadosVentas as $arrayVenta )
        
        $response->setData(
                            array(
                                    'total'       => $intTotal,
                                    'encontrados' => $arrayVentasTotales
                                 )
                          );
        return $response;
    }
    
    
    /**
     * Documentación para el método 'getAdmiJurisdiccionAction'.
     *
     * Retorna las Jurisdicciones existentes dependiendo de los criterios ingresados.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 11-09-2015
     */
    public function getAdmiJurisdiccionAction()
    {
        $response          = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $objResultados     = null;
        $arrayResultados   = array();
        $strCodEmpresa     = '';
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        
        $strPrefijoEmpresa     = $objRequest->query->get('empresa') ? $objRequest->query->get('empresa') : '';
        $strEstadoJurisdiccion = 'Eliminado';
        
        if( $strPrefijoEmpresa )
        {
            $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strPrefijoEmpresa);
            
            if( $objEmpresa )
            {
                $strCodEmpresa = $objEmpresa->getId();
            }
        }
        
        $objResultados = $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion')
                                           ->getJurisdicciones('', $strCodEmpresa, $strEstadoJurisdiccion, '', '');
        
        $intTotal = count($objResultados);
        
        if( $objResultados )
        {
            foreach($objResultados as $objJurisdiccion)
            {
                $arrayItem                          = array();
                $arrayItem['intIdJurisdiccion']     = $objJurisdiccion->getId();
                $arrayItem['strNombreJurisdiccion'] = $objJurisdiccion->getNombreJurisdiccion();
                
                $arrayResultados[] = $arrayItem;
            }
        }
        
        $response->setData(
                            array(
                                    'total'       => $intTotal,
                                    'encontrados' => $arrayResultados
                                 )
                          );
        return $response;
    }
    
    
    /**
     * Documentación para el método 'getAdmiSectorAction'.
     *
     * Retorna los Sectores existentes dependiendo de los criterios ingresados.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 11-09-2015
     */
    public function getAdmiSectorAction()
    {
        $response          = new JsonResponse();
        $objRequest        = $this->get('request');
        $objSession        = $objRequest->getSession();
        $objResultados     = null;
        $arrayResultados   = array();
        $intIdOficina      = 0;
        $intIdCanton       = 0;
        $objIdParroquias   = null;
        $arrayParroquias   = array();
        $objSectores       = null;
        $strCodEmpresa     = '';
        $emInfraestructura = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emComercial       = $this->getDoctrine()->getManager('telconet');
        $emGeneral         = $this->getDoctrine()->getManager('telconet_general');
        
        $intIdJurisdiccion = $objRequest->query->get('jurisdiccion') ? $objRequest->query->get('jurisdiccion') : 0;
        $strPrefijoEmpresa = $objRequest->query->get('empresa') ? $objRequest->query->get('empresa') : '';
        $objJurisdiccion   = $emComercial->getRepository('schemaBundle:AdmiJurisdiccion')->findOneById($intIdJurisdiccion);
        
        if( $strPrefijoEmpresa )
        {
            $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($strPrefijoEmpresa);
            
            if( $objEmpresa )
            {
                $strCodEmpresa = $objEmpresa->getId();
            }
        }
        
        if( $objJurisdiccion )
        {
            $intIdOficina = $objJurisdiccion->getOficinaId();
        }
        
        $objInfoOficinaGrupo = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->findOneById($intIdOficina);
        
        if( $objInfoOficinaGrupo )
        {
            $intIdCanton = $objInfoOficinaGrupo->getCantonId();
        }
        
        $objIdParroquias = $emGeneral->getRepository('schemaBundle:AdmiParroquia')->findByCantonId($intIdCanton);
            
        if( $objIdParroquias )
        {
            foreach($objIdParroquias as $objParroquia)
            {
                $arrayParroquias[] = $objParroquia->getId();
            }
        }
        
        $arrayParametros = array('idParroquia' => $arrayParroquias, 'idEmpresa' => $strCodEmpresa);
        $strTmpEstado    = 'Activo';
        
        $objSectores = $emGeneral->getRepository('schemaBundle:AdmiSector')->getRegistros($arrayParametros, '', $strTmpEstado, '', '');
            
        if( $objSectores )
        {
            foreach($objSectores as $objSector)
            {
                $arrayItem                    = array();
                $arrayItem['intIdSector']     = $objSector->getId();
                $arrayItem['strNombreSector'] = $objSector->getNombreSector();

                $arrayResultados[] = $arrayItem;
            }
        }
        
        $intTotal = count($arrayResultados);
        
        $response->setData(
                            array(
                                    'total'       => $intTotal,
                                    'encontrados' => $arrayResultados
                                 )
                          );
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_297-2940")
     *
     * Documentación para el método 'reporteConsolidadoVentasBrutasActivasAction'.
     *
     * Muestra el reporte consolidado de ventas brutas y activas de los vendedores asignados al usuario
     * logueado.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-09-2015
     */
    public function reporteConsolidadoVentasBrutasActivasAction()
    {
        return $this->render( 'comercialBundle:Supervisor:reporteConsolidadoVentasBrutasActivas.html.twig' );
    }
    
    
    /**
     * @Secure(roles="ROLE_297-2941")
     *
     * Documentación para el método 'gridReporteConsolidadoVentasBrutasActivasAction'.
     *
     * Retorna la información consolidada sobre las ventas 'Brutas' y 'Activas' de los vendedores asignados
     * al usuario logueado.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 15-09-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 30-06-2016 - Se agrega que se busque por tipo de rol 'Empleado' y 'Personal Externo'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 06-07-2016 - Se agregan lo filtros de fePlanificacionDesde y fePlanificacionHasta los cuales retorna las ventas Brutas del 
     *                           vendedor consultado.
     */
    public function gridReporteConsolidadoVentasBrutasActivasAction()
    {
        $response         = new JsonResponse();
        $objRequest       = $this->get('request');
        $objSession       = $objRequest->getSession();
        $serviceComercial = $this->get('comercial.InfoPersonaEmpresaRol');

        $intIdPersonEmpresaRol        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa                 = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento            = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        $strEmpleado                  = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : '';
        $intStart                     = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                     = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $strFechaAprobacionDesde      = $objRequest->query->get('feAprobacionDesde') ? $objRequest->query->get('feAprobacionDesde') : '';
        $arrayFechaAprobacionDesde    = explode('T', $strFechaAprobacionDesde);
        $strFechaAprobacionHasta      = $objRequest->query->get('feAprobacionHasta') ? $objRequest->query->get('feAprobacionHasta') : '';
        $arrayFechaAprobacionHasta    = explode('T', $strFechaAprobacionHasta);
        $strFechaActivacionDesde      = $objRequest->query->get('feActivacionDesde') ? $objRequest->query->get('feActivacionDesde') : '';
        $arrayFechaActivacionDesde    = explode('T', $strFechaActivacionDesde);
        $strFechaActivacionHasta      = $objRequest->query->get('feActivacionHasta') ? $objRequest->query->get('feActivacionHasta') : '';
        $arrayFechaActivacionHasta    = explode('T', $strFechaActivacionHasta);
        $strFechaCreacionPuntoDesde   = $objRequest->query->get('feCreacionPuntoDesde') ? $objRequest->query->get('feCreacionPuntoDesde') : '';
        $arrayFechaCreacionPuntoDesde = explode('T', $strFechaCreacionPuntoDesde);
        $strFechaCreacionPuntoHasta   = $objRequest->query->get('feCreacionPuntoHasta') ? $objRequest->query->get('feCreacionPuntoHasta') : '';
        $arrayFechaCreacionPuntoHasta = explode('T', $strFechaCreacionPuntoHasta);
        $strFechaPlanificacionDesde   = $objRequest->query->get('fePlanificacionDesde') ? $objRequest->query->get('fePlanificacionDesde') : '';
        $arrayFechaPlanificacionDesde = explode('T', $strFechaPlanificacionDesde);
        $strFechaPlanificacionHasta   = $objRequest->query->get('fePlanificacionHasta') ? $objRequest->query->get('fePlanificacionHasta') : '';
        $arrayFechaPlanificacionHasta = explode('T', $strFechaPlanificacionHasta);
        $boolFechaActivacion          = $strFechaActivacionDesde ? true : false;

        $arrayTipoVentas = array('brutas', 'activas');
        $arrayParametros = array(
                                    'arrayTipoVentas'            => $arrayTipoVentas,
                                    'strFechaActivacionDesde'    => $boolFechaActivacion,
                                    'strCaracteristicaBruta'     => self::CARACTERISTICA_META_BRUTA,
                                    'strCaracteristicaActiva'    => self::CARACTERISTICA_META_ACTIVA,
                                    'strFechaAprobacionDesde'    => $arrayFechaAprobacionDesde[0],
                                    'strFechaAprobacionHasta'    => $arrayFechaAprobacionHasta[0],
                                    'strFechaCreacionPuntoDesde' => $arrayFechaCreacionPuntoDesde[0],
                                    'strFechaCreacionPuntoHasta' => $arrayFechaCreacionPuntoHasta[0],
                                    'strFechaActivacionDesde'    => $arrayFechaActivacionDesde[0],
                                    'strFechaActivacionHasta'    => $arrayFechaActivacionHasta[0],
                                    'strFechaPlanificacionDesde' => $arrayFechaPlanificacionDesde[0],
                                    'strFechaPlanificacionHasta' => $arrayFechaPlanificacionHasta[0],
                                    'arrayParametrosBusqueda'    => array(
                                                                            'usuario'      => $intIdPersonEmpresaRol,
                                                                            'departamento' => $intIdDepartamento,
                                                                            'empresa'      => $intIdEmpresa,
                                                                            'asignadosA'   => $intIdPersonEmpresaRol,
                                                                            'inicio'       => $intStart,
                                                                            'limite'       => $intLimit,
                                                                            'strTipoRol'   => array('Empleado', 'Personal Externo'),
                                                                            'criterios'    => array( 'nombreEmpleado' => $strEmpleado )
                                                                         )
                                );
       
        $arrayResultados = $serviceComercial->getVentasBrutasYActivasVendedores($arrayParametros);
        
        $response->setData(
                            array(
                                    'total'       => $arrayResultados['intTotal'],
                                    'encontrados' => $arrayResultados['encontrados']
                                 )
                          );
        
        return $response;
    }
    
    
    /**
     * @Secure(roles="ROLE_297-2942")
     *
     * Documentación para el método 'exportarReporteConsolidadoVentasBrutasActivasAction'.
     *
     * Retorna la información consolidada sobre las ventas 'Brutas' y 'Activas' de los vendedores asignados
     * al usuario logueado en formato Excel.
     *
     * @return Response 
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 16-09-2015
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 30-06-2016 - Se agrega que se busque por tipo de rol 'Empleado' y 'Personal Externo'
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.2 06-07-2016 - Se agregan lo filtros de fePlanificacionDesde y fePlanificacionHasta los cuales retorna las ventas Brutas del 
     *                           vendedor consultado.
     */
    public function exportarReporteConsolidadoVentasBrutasActivasAction()
    {
        error_reporting(E_ALL);
        ini_set('max_execution_time', 3000000);
        
        $objRequest       = $this->get('request');
        $objSession       = $objRequest->getSession();
        $serviceComercial = $this->get('comercial.InfoPersonaEmpresaRol');

        $strUsuarioSession            = $objSession->get('user') ? $objSession->get('user') : '';
        $intIdPersonEmpresaRol        = $objSession->get('idPersonaEmpresaRol') ? $objSession->get('idPersonaEmpresaRol') : 0;
        $intIdEmpresa                 = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $intIdDepartamento            = $objSession->get('idDepartamento') ? $objSession->get('idDepartamento') : 0;
        
        $strFechaAprobacionDesde      = $objRequest->query->get('expFeAprobacionDesde') ? $objRequest->query->get('expFeAprobacionDesde') : '';
        $arrayFechaAprobacionDesde    = explode('/', $strFechaAprobacionDesde);
        
        if( count($arrayFechaAprobacionDesde) > 1 )
        {
            $strFechaAprobacionDesde = $arrayFechaAprobacionDesde[2].'-'.$arrayFechaAprobacionDesde[0].'-'.$arrayFechaAprobacionDesde[1];
        }
        
        $strFechaAprobacionHasta      = $objRequest->query->get('expFeAprobacionHasta') ? $objRequest->query->get('expFeAprobacionHasta') : '';
        $arrayFechaAprobacionHasta    = explode('/', $strFechaAprobacionHasta);
        
        if( count($arrayFechaAprobacionHasta) > 1 )
        {
            $strFechaAprobacionHasta = $arrayFechaAprobacionHasta[2].'-'.$arrayFechaAprobacionHasta[0].'-'.$arrayFechaAprobacionHasta[1];
        }
        
        
        $strFechaActivacionDesde      = $objRequest->query->get('expFeActivacionDesde') ? $objRequest->query->get('expFeActivacionDesde') : '';
        $arrayFechaActivacionDesde    = explode('/', $strFechaActivacionDesde);
        
        if( count($arrayFechaActivacionDesde) > 1 )
        {
            $strFechaActivacionDesde = $arrayFechaActivacionDesde[2].'-'.$arrayFechaActivacionDesde[0].'-'.$arrayFechaActivacionDesde[1];
        }
        
        
        $strFechaActivacionHasta      = $objRequest->query->get('expFeActivacionHasta') ? $objRequest->query->get('expFeActivacionHasta') : '';
        $arrayFechaActivacionHasta    = explode('/', $strFechaActivacionHasta);
        
        if( count($arrayFechaActivacionHasta) > 1 )
        {
            $strFechaActivacionHasta = $arrayFechaActivacionHasta[2].'-'.$arrayFechaActivacionHasta[0].'-'.$arrayFechaActivacionHasta[1];
        }
        
        
        $strFechaCreacionPuntoDesde   = $objRequest->query->get('expFeCreacionPuntoDesde') 
                                        ? $objRequest->query->get('expFeCreacionPuntoDesde') : '';
        $arrayFechaCreacionPuntoDesde = explode('/', $strFechaCreacionPuntoDesde);
        
        if( count($arrayFechaCreacionPuntoDesde) > 1 )
        {
            $strFechaCreacionPuntoDesde = $arrayFechaCreacionPuntoDesde[2].'-'.$arrayFechaCreacionPuntoDesde[0].'-'.$arrayFechaCreacionPuntoDesde[1];
        }
        
        
        $strFechaCreacionPuntoHasta   = $objRequest->query->get('expFeCreacionPuntoHasta') 
                                        ? $objRequest->query->get('expFeCreacionPuntoHasta') : '';
        $arrayFechaCreacionPuntoHasta = explode('/', $strFechaCreacionPuntoHasta);
        
        if( count($arrayFechaCreacionPuntoHasta) > 1 )
        {
            $strFechaCreacionPuntoHasta = $arrayFechaCreacionPuntoHasta[2].'-'.$arrayFechaCreacionPuntoHasta[0].'-'.$arrayFechaCreacionPuntoHasta[1];
        }
        
        
        $strFechaPlanificacionDesde   = $objRequest->query->get('expFePlanificacionDesde') 
                                        ? $objRequest->query->get('expFePlanificacionDesde') : '';
        $arrayFechaPlanificacionDesde = explode('/', $strFechaPlanificacionDesde);
        
        if( count($arrayFechaPlanificacionDesde) > 1 )
        {
            $strFechaPlanificacionDesde = $arrayFechaPlanificacionDesde[2].'-'.$arrayFechaPlanificacionDesde[0].'-'.$arrayFechaPlanificacionDesde[1];
        }
        
        
        $strFechaPlanificacionHasta   = $objRequest->query->get('expFePlanificacionHasta') 
                                        ? $objRequest->query->get('expFePlanificacionHasta') : '';
        $arrayFechaPlanificacionHasta = explode('/', $strFechaPlanificacionHasta);
        
        if( count($arrayFechaPlanificacionHasta) > 1 )
        {
            $strFechaPlanificacionHasta = $arrayFechaPlanificacionHasta[2].'-'.$arrayFechaPlanificacionHasta[0].'-'.$arrayFechaPlanificacionHasta[1];
        }
        
        
        $boolFechaActivacion  = $strFechaActivacionDesde ? true : false;
        
        $arrayTipoVentas = array('brutas', 'activas');
        $arrayParametros = array(
                                    'arrayTipoVentas'            => $arrayTipoVentas,
                                    'strFechaActivacionDesde'    => $boolFechaActivacion,
                                    'strCaracteristicaBruta'     => self::CARACTERISTICA_META_BRUTA,
                                    'strCaracteristicaActiva'    => self::CARACTERISTICA_META_ACTIVA,
                                    'strFechaAprobacionDesde'    => $strFechaAprobacionDesde,
                                    'strFechaAprobacionHasta'    => $strFechaAprobacionHasta,
                                    'strFechaCreacionPuntoDesde' => $strFechaCreacionPuntoDesde,
                                    'strFechaCreacionPuntoHasta' => $strFechaCreacionPuntoHasta,
                                    'strFechaActivacionDesde'    => $strFechaActivacionDesde,
                                    'strFechaActivacionHasta'    => $strFechaActivacionHasta,
                                    'strFechaPlanificacionDesde' => $strFechaPlanificacionDesde,
                                    'strFechaPlanificacionHasta' => $strFechaPlanificacionHasta,
                                    'arrayParametrosBusqueda'    => array(
                                                                            'usuario'      => $intIdPersonEmpresaRol,
                                                                            'departamento' => $intIdDepartamento,
                                                                            'empresa'      => $intIdEmpresa,
                                                                            'strTipoRol'   => array('Empleado', 'Personal Externo'),
                                                                            'asignadosA'   => $intIdPersonEmpresaRol
                                                                         )
                                );
       
        $arrayResultados = $serviceComercial->getVentasBrutasYActivasVendedores($arrayParametros);

        $objPHPExcel   = new PHPExcel();
        $cacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '1024MB');
        
        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateConsolidadoVentasBrutasActivas.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsuarioSession);
        $objPHPExcel->getProperties()->setTitle("Tabla de Resultados por Vendedor");
        $objPHPExcel->getProperties()->setSubject("Tabla de Resultados por Vendedor");
        $objPHPExcel->getProperties()->setDescription("Consolidado de Ventas Brutas y Activas por Vendedor.");
        $objPHPExcel->getProperties()->setKeywords("Ventas");
        $objPHPExcel->getProperties()->setCategory("Reporte");
        
        $objPHPExcel->getActiveSheet()->setCellValue('C3', $strUsuarioSession);

        $objPHPExcel->getActiveSheet()->setCellValue('C4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('C4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $objPHPExcel->getActiveSheet()->setCellValue('C8', ''.$strFechaCreacionPuntoDesde);
        $objPHPExcel->getActiveSheet()->setCellValue('C9', ''.$strFechaCreacionPuntoHasta);
        $objPHPExcel->getActiveSheet()->setCellValue('C10', ''.$strFechaAprobacionDesde);
        $objPHPExcel->getActiveSheet()->setCellValue('C11', ''.$strFechaAprobacionHasta);        
        $objPHPExcel->getActiveSheet()->setCellValue('C12', ''.$strFechaActivacionDesde);
        $objPHPExcel->getActiveSheet()->setCellValue('C13', ''.$strFechaActivacionHasta);        
        $objPHPExcel->getActiveSheet()->setCellValue('C14', ''.$strFechaPlanificacionDesde);
        $objPHPExcel->getActiveSheet()->setCellValue('C15', ''.$strFechaPlanificacionHasta);

        $i = 21;
        $j = 1;
        
        $styleAlignCenter           = array( 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
        $styleBackgroundColorRed    = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FF0000') );
        $styleBackgroundColorYellow = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'FFFF00') );
        $styleBackgroundColorGreen  = array( 'type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => '008000') );
        
        foreach($arrayResultados['encontrados'] as $itemVendedor)
        {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $j);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $itemVendedor['strNombreVendedor']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $itemVendedor['intTiempoVendedor']);
            $objPHPExcel->getActiveSheet()->getStyle('C'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $itemVendedor['strMetabrutas']);
            $objPHPExcel->getActiveSheet()->getStyle('D'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $itemVendedor['intCumplimientobrutas']);
            $objPHPExcel->getActiveSheet()->getStyle('E'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $itemVendedor['intFaltabrutas']);
            $objPHPExcel->getActiveSheet()->getStyle('F'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $itemVendedor['intPorcentajebrutas'].'%');
            
            if( intval($itemVendedor['intPorcentajebrutas']) < 70 )
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorRed
                                                                                          ) 
                                                                                 );
            }
            elseif( intval($itemVendedor['intPorcentajebrutas']) >= 70 && intval($itemVendedor['intPorcentajeBruta']) < 99 )
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorYellow
                                                                                          ) 
                                                                                 );
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getStyle('G'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorGreen
                                                                                          ) 
                                                                                 );
            }
            
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $itemVendedor['strMetaactivas']);
            $objPHPExcel->getActiveSheet()->getStyle('H'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $itemVendedor['intCumplimientoactivas']);
            $objPHPExcel->getActiveSheet()->getStyle('I'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $itemVendedor['intFaltaactivas']);
            $objPHPExcel->getActiveSheet()->getStyle('J'.$i)->applyFromArray( array('alignment' => $styleAlignCenter) );
            
            $objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $itemVendedor['intPorcentajeactivas'].'%');
            
            if( floatval($itemVendedor['intPorcentajeactivas']) < 70 )
            {
                $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorRed
                                                                                          ) 
                                                                                 );
            }
            elseif( floatval($itemVendedor['intPorcentajeactivas']) >= 70 && floatval($itemVendedor['intPorcentajeactivas']) < 99 )
            {
                $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorYellow
                                                                                          ) 
                                                                                 );
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getStyle('K'.$i)->applyFromArray( 
                                                                                    array(
                                                                                            'alignment' => $styleAlignCenter, 
                                                                                            'fill'      => $styleBackgroundColorGreen
                                                                                          ) 
                                                                                 );
            }
            
            $j++;
            $i++;
        }
        
        
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        // Rename sheet
        $objPHPExcel->getActiveSheet()->setTitle('Reporte');

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //Redirect output to a clients web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Tabla_de_Resultados_por_Vendedor_'.date('d_M_Y').'.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        
        exit;
    }
}
