<?php

namespace telconet\comercialBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;
use \PHPExcel_Cell_DataType;
use \PHPExcel_Style_Fill;
use \PHPExcel_Style_Alignment;
use \PHPExcel_Style_Border;
use \PHPExcel_Chart_DataSeriesValues;
use \PHPExcel_Chart_DataSeries;
use \PHPExcel_Chart_PlotArea;
use \PHPExcel_Chart_Legend;
use \PHPExcel_Chart_Title;
use \PHPExcel_Chart;
use \PHPExcel_Chart_Layout;
use \PHPExcel_Worksheet_Drawing;
use \PHPExcel_Style_NumberFormat;
        
/**
 * Documentación para la clase 'ReportesJefesController'.
 *
 * Clase utilizada para manejar metodos que permiten realizar la consulta y generación de reportes de ventas a nivel de jefatura.
 *
 * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
 * @version 1.0 26-10-2015
 */
class ReportesJefesController extends Controller implements TokenAuthenticatedController
{
    /**
     * @Secure(roles="ROLE_312-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * Método inicial que renderiza la interfaz index y carga la data principal para el procesamiento de los reportes.
     * 
     * @return Response retorna la renderización de la ventana principal
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function indexAction()
    {
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $emInfraestructura = $this->get('doctrine')->getManager('telconet_infraestructura');
        
        $objSesion     = $this->get('request')->getSession();
        $strModulo     = 'COMERCIAL';
        $intJefePer    = $objSesion->get('idPersonaEmpresaRol');
        $strEmpresaCod = $objSesion->get('idEmpresa');
        
        $strParamJurisdiccion    = 'JEFES_JURISDICCIONES';
        $strParamVentasPorAsesor = 'VENTAS_POR_ASESOR';
        
        // Carga de datos globales de los reportes.
        $listaSupervisores       = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getResultadoSupervisoresACargo($intJefePer);
        
        $listaParamResultadosMes = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                               ->get($strParamJurisdiccion, $strModulo, '', '', '', '', '', '', '', $strEmpresaCod, 'valor3');
        $listaParamVentasXAsesor = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                              ->get($strParamVentasPorAsesor, $strModulo, '', '', '', '', '', '', '', $strEmpresaCod, 'valor4');
        $listaJurisdicciones     = $emInfraestructura->getRepository('schemaBundle:AdmiJurisdiccion')
                                                     ->getResultadoJurisdiccionesPorEmpresa($strEmpresaCod);
        
        $objSesion->set('listaParametrosResultadosMes', $listaParamResultadosMes);
        $objSesion->set('listaParametrosVentasXAsesor', $listaParamVentasXAsesor);
        $objSesion->set('listaSupervisores',            $listaSupervisores);
        $objSesion->set('listaAsesores',                null);
        $objSesion->set('listaJurisdicciones',          $listaJurisdicciones);
        $objSesion->set('listaResultadoMes',            null);

        //MODULO 312 - REPORTES_JEFES
        if(true === $this->get('security.context')->isGranted('ROLE_312-1'))
        {
            $rolesPermitidos[] = 'ROLE_312-1'; //Index Reportes Jefatura
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3437'))
        {
            $rolesPermitidos[] = 'ROLE_312-3437'; //Consultar Reporte Jefatura Resultados Mes
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3438'))
        {
            $rolesPermitidos[] = 'ROLE_312-3438'; //Consultar Reporte Jefatura Resultados Supervisor
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3439'))
        {
            $rolesPermitidos[] = 'ROLE_312-3439'; //Consultar Reporte Jefatura Resultados Consolidados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3440'))
        {
            $rolesPermitidos[] = 'ROLE_312-3440'; //Consultar Reporte Jefatura Rechazos Ventas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3441'))
        {
            $rolesPermitidos[] = 'ROLE_312-3441'; //Consultar Reporte Jefatura Ventas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3442'))
        {
            $rolesPermitidos[] = 'ROLE_312-3442'; //Generar Reporte Jefatura Resultados Mes
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3443'))
        {
            $rolesPermitidos[] = 'ROLE_312-3443'; //Generar Reporte Jefatura Resultados Supervisor
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3444'))
        {
            $rolesPermitidos[] = 'ROLE_312-3444'; //Generar Reporte Jefatura Resultados Consolidados
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3445'))
        {
            $rolesPermitidos[] = 'ROLE_312-3445'; //Generar Reporte Jefatura Rechazos Ventas
        }
        if(true === $this->get('security.context')->isGranted('ROLE_312-3446'))
        {
            $rolesPermitidos[] = 'ROLE_312-3446'; //Generar Reporte Jefatura Ventas
        }

        $arrayParametros = array('rolesPermitidos' => $rolesPermitidos,
                                 'tamanio'         => count($listaParamResultadosMes) + 1,
                                 'estado'          => 'Eliminado');
        return $this->render('comercialBundle:ReportesJefes:index.html.twig', $arrayParametros);
    }

    /**
     * @Secure(roles="ROLE_312-3437")
     * 
     * Documentación para el método 'gridResultadosMesAction'.
     * 
     * Método que realiza la consulta de los resultados de ventas mensuales Brutas y Activas por Jurisdicción filtrando por la Fecha Mensual.
     * 
     * @return Response Retorna la lista de Ventas Mensuales
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function gridResultadosMesAction()
    {
        $objPeticion           = $this->get('request');
        $objSesion             = $objPeticion->getSession();
        $listaParametrosResMes = $objSesion->get('listaParametrosResultadosMes');
        $arrayParametros       = $this->getParametrosVentas($objPeticion);
        $emComercial           = $this->get('doctrine')->getManager('telconet');
        $arrayResponse         = $emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->getJsonReporteJefaturaResultadosMes($listaParametrosResMes, $arrayParametros);

        $objSesion->set('listaResultadoMes', $arrayResponse['RESULTADOS_MES']);
        $objSesion->set('mesResultadoMes',   $this->getFecha($arrayParametros['FECHA']));

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($arrayResponse['JSON']);
        
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_312-3438")
     * 
     * Documentación para el método 'gridResultadosPorSupervisorAction'.
     * 
     * Método que realiza la consulta de los resultados de las mentas, ventas y cumplimiento(Indicador) de los supervisores a cargo 
     * del jefe en sesión filtrando por la Fecha Mensual.
     * 
     * @return Response Retorna la lista de Resultados de Supervisores.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function gridResultadosPorSupervisorAction()
    {
        $objPeticion = $this->get('request');
        $objSesion   = $objPeticion->getSession();
        $emComercial = $this->get('doctrine')->getManager('telconet');
        
        $objRepository            = $emComercial->getRepository('schemaBundle:InfoServicio');
        $objInfoPersonaRepository = $emComercial->getRepository('schemaBundle:InfoPersona');
        
        $arrayParametros                 = $this->getParametrosVentas($objPeticion);
        $arrayParametros['ESTADO_SH']    = 'Activo';
        $arrayParametros['JURISDICCION'] = null;
        $arrayParametros['ACTIVAS']      = true;
        $arrayParametros['ESTADO']       = 'Activo';
        $listaSupervisores               = $objSesion->get('listaSupervisores');
        
        $arrayResponse = $objRepository->getJsonReporteJefaturaResultadosSupervisor($listaSupervisores, $arrayParametros, $objInfoPersonaRepository);
        
        $objSesion->set('listaResultadosSupervisores',  $arrayResponse['RESULTADOS_SUP']);
        $objSesion->set('listaIndicadoresSupervisores', $arrayResponse['INDICADORES_SUP']);
        $objSesion->set('arraySupervisoresId',          $arrayResponse['INDENTIFICADORES_SUP']);
        $objSesion->set('mesResultadoSupervisor',       $this->getFecha($arrayParametros['FECHA']));
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($arrayResponse['JSON']);
        
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_312-3438")
     * 
     * Documentación para el método 'gridVentasPorAsesorAction'.
     * 
     * Método que realiza la consulta de los rangos de ventas por Asesor filtrado por la Fecha Mensual
     * Este método se ejecuta siempre después de consultar los resultados por supervisor.
     * 
     * @return Response Retorna la lista de Cantidad de Asesores por Rango de Ventas.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function gridVentasPorAsesorAction()
    {
        $objPeticion                 = $this->get('request');
        $objSesion                   = $objPeticion->getSession();
        $arrayParametrosVentasAsesor = $objSesion->get('listaParametrosVentasXAsesor');
        $arraySupervisoresId         = $objSesion->get('arraySupervisoresId');
        $arrayParametros             = $this->getParametrosVentas($objPeticion);
        $emComercial                 = $this->get('doctrine')->getManager('telconet');
        $arrayResponse               = $emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->getJsonReporteJefaturaVentasPorAsesor($arrayParametrosVentasAsesor, 
                                                                                           $arrayParametros, 
                                                                                           $arraySupervisoresId);
        
        $objSesion->set('listaVentasAsesor',    $arrayResponse['VENTAS_ASESOR']);
        $objSesion->set('listaVentasAsesorInd', $arrayResponse['VENTAS_ASESOR_IND']);
        $objSesion->set('mesVentasAsesor',      $this->getFecha($arrayParametros['FECHA']));

        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($arrayResponse['JSON']);
        
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_312-3439")
     * 
     * Documentación para el método 'gridResultadosConsolidadosAction'.
     * 
     * Método que realiza la consulta de los resultados consolidados por supervisores y sus asesores
     * resumiendo las ventas de cada asesor y su indicador de cumplimiento filtrados por 
     * Fecha Mensual, Estado del Contrato, Jurisdicción y estado del Servicio.
     * 
     * @return Response Retorna la lista de Resultados de Supervisores.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function gridResultadosConsolidadosAction()
    {
        $objPeticion       = $this->get('request');
        $objSesion         = $objPeticion->getSession();
        $listaSupervisores = $objSesion->get('listaSupervisores');
        $emComercial       = $this->get('doctrine')->getManager('telconet');
        $arrayParametros   = $this->getParametrosVentas($objPeticion);
        
        // Se eliminan Espacios y Guiones del estado del servicio
        $arrayParametros['ESTADO_SH']           = str_replace(array(' ', '-'), '', $objPeticion->get('servicio'));
        $arrayParametros['JURISDICCION']        = $objPeticion->get('jurisdiccion') != 'Todos' ? $objPeticion->get('jurisdiccion') : null;
        $arrayParametros['CONTRATO']            = $objPeticion->get('contrato');
        $arrayParametros['VALOR']               = 'Supervisor';
        $arrayParametros['ESTADO']              = 'Activo';
        $arrayParametros['CARACTERISTICA']      = 'CARGO';
        $arrayParametros['PERSONAEMPRESAROLID'] = $objSesion->get('idPersonaEmpresaRol');
        $arrayParametros['ACTIVAS']             = $arrayParametros['ESTADO_SH'] == 'Activo';
        
        $arraySupervisoresId = array();
        $arraySupervisores   = array();
        
        foreach($listaSupervisores as $strSupervisorColumn)
        {
            $arraySupervisoresId[] = $strSupervisorColumn['id'];
            // Se concatenan los nombres y apellidos de los supervisores y se remueven los espacios para formar el identificador de columna
            $arraySupervisores[]   = substr(preg_replace('/\s+/', '', $strSupervisorColumn['apellidos'] . $strSupervisorColumn['nombres']), 0, 30);
        }
        
        $arrayParametros['SUPERVISORES_ID'] = $arraySupervisoresId;
        $arrayParametros['SUPERVISORES']    = $arraySupervisores;
        
        $arrayReporteConsolidado = $emComercial->getRepository('schemaBundle:InfoServicio')
                                               ->getJsonReporteConsolidadoVentasAsesoresPorSupervisor($arrayParametros, $arraySupervisores);
                                  
        $objSesion->set('listaResultadosConsolidados', $arrayReporteConsolidado['CONSOLIDADO_REPORTE']);
        $objSesion->set('listaIndicadorAsesores',      $arrayReporteConsolidado['INDICADORES']);
        $objSesion->set('mesResultadosConsolidados',   $this->getFecha($arrayParametros['FECHA']));
        
        $objSesion->set('rcContrato', $objPeticion->get('contratoDesc'));
        $objSesion->set('rcJurisdiccion', $objPeticion->get('jurisdiccionDesc'));
        $objSesion->set('rcServicio', $objPeticion->get('servicioDesc'));
        
        $objRespuesta = new Response();
        $objRespuesta->headers->set('Content-type', 'text/json');
        $objRespuesta->setContent($arrayReporteConsolidado['JSON']);
        return $objRespuesta;
    }

    /**
     * @Secure(roles="ROLE_312-3440")
     * 
     * Documentación para el método 'gridRechazosVentasAction'.
     * 
     * Método que realiza la consulta de las cantidades de rechazo por motivo y su porcentaje respecto a las ventas brutas 
     * por Fecha Mensual, Supervisor, Asesor y Jurisdicción.
     * 
     * @return Response Retorna la lista de Rechazos en Ventas Brutas.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function gridRechazosVentasAction()
    {
        $objPeticion     = $this->get('request');
        $objSesion       = $objPeticion->getSession();
        $arrayParametros = $this->getParametrosVentas($objPeticion);
        $intSupervisor   = $objPeticion->get('supervisor');
        $strLoginAsesor  = $objPeticion->get('asesor');
        $strJurisdiccion = $objPeticion->get('jurisdiccion');
        $emComercial     = $this->get('doctrine')->getManager('telconet');
        
        $arrayParametros['JURISDICCION'] = $strJurisdiccion == 'Todos' ? null : $strJurisdiccion;
        $arrayParametros['ESTADO_SH']    = 'Factible';
        $arrayParametros['ASESORES']     = $this->getListaAsesores($intSupervisor, $strLoginAsesor, $objSesion);
        
        $arrayResponse = $emComercial->getRepository('schemaBundle:InfoServicio')->getJsonReporteJefaturaRechazosVentas($arrayParametros);
        
        $objSesion->set('listaRechazosVentas', $arrayResponse['RECHAZOS_VENTAS']);
        $objSesion->set('mesRechazosVentas',   $this->getFecha($arrayParametros['FECHA']));
        
        // Parámetros adicionales necesarios para la generación del reporte en excel.
        $objSesion->set('rvSupervisor',   $objPeticion->get('supervisorDesc'));
        $objSesion->set('rvAsesor',       $objPeticion->get('asesorDesc'));
        $objSesion->set('rvJurisdiccion', $objPeticion->get('jurisdiccionDesc'));
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($arrayResponse['JSON']);
        
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_312-3441")
     * 
     * Documentación para el método 'gridVentasJefaturaAction'.
     * 
     * Método que realiza la consulta de las ventas de la jefatura a nivel detallado 
     * por Fecha Mensual, Canal, Punto de Venta, supervisor, Asesor.
     * 
     * @return Response retorna la Lista de Ventas detallada de la Jefatura.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function gridVentasJefaturaAction()
    {
        $emComercial     = $this->get('doctrine')->getManager('telconet');
        $objPeticion     = $this->get('request');
        $objSesion       = $objPeticion->getSession();        
        $arrayParametros = $this->getParametrosVentas($objPeticion);

        $arrayParametros['PARAMETROCANAL']      = 'CANALES_PUNTO_VENTA';
        $arrayParametros['CARACTERISTICACANAL'] = 'PUNTO_DE_VENTA_CANAL';
        
        $intSupervisor  = $objPeticion->get('supervisor');
        $strLoginAsesor = $objPeticion->get('asesor');
        
        $arrayParametros['ASESORES']     = $this->getListaAsesores($intSupervisor, $strLoginAsesor, $objSesion);
        $arrayParametros['ESTADO_SH']    = 'Activo';
        $arrayParametros['CANALVENTA']   = $objPeticion->get('canal');
        $arrayParametros['PUNTOVENTA']   = $objPeticion->get('puntoVenta');

        $arrayResponse = $emComercial->getRepository('schemaBundle:InfoServicio')->getJsonReporteJefaturaVentas($arrayParametros);

        $objSesion->set('listaVentasJefatura', $arrayResponse['VENTAS_JEFATURA']);
        $objSesion->set('mesVentasJefatura',   $this->getFecha($arrayParametros['FECHA']));

        $objSesion->set('vjCanal',      $objPeticion->get('canalDesc'));
        $objSesion->set('vjPuntoVenta', $objPeticion->get('puntoVentaDesc'));
        $objSesion->set('vjSupervisor', $objPeticion->get('supervisorDesc'));
        $objSesion->set('vjAsesor',     $objPeticion->get('asesorDesc'));
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($arrayResponse['JSON']);
        
        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_312-3439")
     * 
     * Documentación para el método 'metaDataResultadosConsolidadosAction'.
     * 
     * Método que carga la data necesaria para la renderización de los componentes visuales del reporte de resultados consolidados.
     * 
     * @return Response retorna la lista con la definición de los componentes del grid.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function metaDataResultadosConsolidadosAction()
    {
        $objPeticion         = $this->get('request');
        $objSesion           = $objPeticion->getSession();
        $listaSupervisores   = $objSesion->get('listaSupervisores');
        $listaJurisdicciones = $objSesion->get('listaJurisdicciones');
        $arrayOficinas       = array('oficinaId'  => $objSesion->get('idOficina'));
        $arrayOrdenamiento   = array('feCreacion' => 'ASC');
        $objRepositorio      = $this->get('doctrine')->getManager('telconet')->getRepository('schemaBundle:AdmiJurisdiccion');
        $listaJurisdiccion   = $objRepositorio->findBy($arrayOficinas, $arrayOrdenamiento);
        $strJurisdiccion     = count($listaJurisdiccion) > 0 ? $listaJurisdiccion[0]->getNombreJurisdiccion() : '';
        
        $strMapI       = "asesores";
        $intGridWidth  = 1204;
        $intSizeAsesor = 230;
        $intSize       = 0;
        
        // Data de los filtros de búsqueda
        $arrayMetaData         = array();
        $arrayServicio       = array();
        $arrayContrato[]     = array("id" => "%",      "display" => "Todos");
        $arrayJurisdiccion[] = array("id" => "Todos",  "display" => "Todas");
        
        $arrayContratoEstados = array('Activo', 'Cancelado', 'Pendiente', 'Rechazado');

        $arrayServicioEstados = array('Activo', 
                                      'Asignada',
                                      'Asignado Tarea',
                                      'Detenido',
                                      'En Pruebas',
                                      'En Verificacion',
                                      'Inactivo',
                                      'Pre-planificada',
                                      'Planificada',
                                      'Re-planificada');

        // Estructuración de la data de los filtros de búsqueda.
        foreach($arrayContratoEstados as $strContratoEstado)
        {
            $arrayContrato[] = array("id" => $strContratoEstado, "display" => $strContratoEstado);
        }
        
        foreach($listaJurisdicciones as $entityJurisdiccion)
        {
            $arrayJurisdiccion[] = array("id"      => $entityJurisdiccion->getNombreJurisdiccion(), 
                                         "display" => $entityJurisdiccion->getNombreJurisdiccion());
        }
        
        foreach($arrayServicioEstados as $strServicioEstado)
        {
            $arrayServicio[] = array("id" => $strServicioEstado, "display" => $strServicioEstado);
        }
        
        $objSesion->set('arrayJurisdicciones', $arrayJurisdiccion);

        $arrayMetaData["contrato"]["list"]      = $arrayContrato;
        $arrayMetaData["contrato"]["value"]     = "%";
        
        $arrayMetaData["jurisdiccion"]["list"]  = $arrayJurisdiccion;
        $arrayMetaData["jurisdiccion"]["value"] = $strJurisdiccion;
        
        $arrayMetaData["servicio"]["list"]      = $arrayServicio;
        $arrayMetaData["servicio"]["value"]     = "Activo";
        
        $arrayMetaData["fields"][]  = array("name"    => $strMapI,      
                                            "mapping" => $strMapI, 
                                            "type"    => "string");

        $arrayMetaData["columns"][] = array("text"  => "ASESORES", 
                                            "width" => $intSizeAsesor,   
                                            "align" => "left", 
                                            "sort"  => true, 
                                            "type"  => 'count', 
                                            "style" => 'padding-left: 10px;',
                                            "index" => $strMapI);
        $intTotalWidth = $intSizeAsesor;
        // Definición de la data estructural de las columnas del reporte, dinámicamente creado en base a la cantidad de supervisores asignados al jefe
        foreach($listaSupervisores as $entitySupervisor)
        {
            $strApellidos = explode(' ', $entitySupervisor['apellidos'])[0].explode(' ', $entitySupervisor['apellidos'])[1];
            $strNombre    = explode(' ', $entitySupervisor['nombres'])[0].explode(' ', $entitySupervisor['nombres'])[1];
            $strIndex     = substr($strApellidos . $strNombre, 0, 30);
            $strText      = explode(' ', $entitySupervisor['apellidos'])[0] . ' ' . explode(' ', $entitySupervisor['nombres'])[0];
            $intLenght    = strlen($strText);
            $intSize      = (($intLenght * 10) - ((($intLenght / 3) * 5) / 2));
            
            $arrayMetaData["fields"][]  = array("name"    => $strIndex, 
                                                "mapping" => $strIndex, 
                                                "type"    => "integer");
            $intTotalWidth += $intSize;
            $arrayMetaData["columns"][] = array("text"  => $strText,  
                                                "width" => $intSize - 6,  
                                                "align" => "center", 
                                                "sort"  => true,
                                                "type"  => 'sum', 
                                                "style" => '', 
                                                "index" => $strIndex);
        }
        
        $strMapF   = "total_general";
        $strText   = "TOTAL GENERAL";
        $intSizeTG = 70;
        
        $arrayMetaData["fields"][]  = array("name"    => $strMapF,  
                                            "mapping" => $strMapF, 
                                            "type"    => "string");
        
        $arrayMetaData["columns"][] = array("text"  => $strText, 
                                            "width" => $intSizeTG,     
                                            "align" => "right", 
                                            "sort"  => true, 
                                            "type"  => 'count',
                                            "style" => 'padding-right: 20px;',
                                            "index" => $strMapF);
        $intTotalWidth += $intSize;
        
        if($intTotalWidth < $intGridWidth)
        {
            $intDiff = $intGridWidth - $intTotalWidth;
            $intGrow = (($intDiff / count($arrayMetaData["columns"])));
            
            for($i = 0; $i < count($arrayMetaData["columns"]); $i++)
            {
                $arrayMetaData["columns"][$i]['width'] += ($intGrow + 10); 
            }
        }
        
        $arrayMetaData["cantColumnas"] = count($arrayMetaData["columns"]);
        
        $objRespuesta = new Response(json_encode(array('metaData' => $arrayMetaData)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'getMetasActivasSupervisoresAction'.
     * 
     * Método que carga las metas activas de los supervisores.
     * 
     * @return Response retorna la lista con las metas activas.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function getMetasActivasSupervisoresAction()
    {
        $objPeticion       = $this->get('request');
        $objSesion         = $objPeticion->getSession();
        $listaSupervisores = $objSesion->get('listaSupervisores');
        
        
        $strTotales   = $objPeticion->get('totales');
        $arrayTotales = explode(',', $strTotales);
        
        $emComercial              = $this->get('doctrine')->getManager('telconet');
        $objInfoPersonaRepository = $emComercial->getRepository('schemaBundle:InfoPersona');
        
        $arrayParametros['MES']   = $this->getParametrosVentas($objPeticion)['MES'];
        
        $arrayResponse = $emComercial->getRepository('schemaBundle:InfoServicio')
                                     ->getJsonMetasActivasSupervisores($listaSupervisores, $arrayParametros, $objInfoPersonaRepository);
        
        $objSesion->set('listaTotalesConsolidados', $arrayTotales);
        $objSesion->set('listaMetasConsolidados',   $arrayResponse['METAS']);
        
        $objResponse = new Response();
        $objResponse->headers->set('Content-type', 'text/json');
        $objResponse->setContent($arrayResponse['JSON']);
        
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getAjaxComboSupervisoresAction'.
     * 
     * Método que retorna los supervisores asignados al jefe que se encuentran guardados en sesión para presentar en un combo.
     * 
     * @return Response retorna la lista de supervisores.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function getAjaxComboSupervisoresAction()
    {
        $listaSupervisores   = $this->get('request')->getSession()->get('listaSupervisores');
        $arraySupervisores[] = array('id_persona_sup' => 'Todos', 'nombre_sup' => 'Todos');
        
        foreach($listaSupervisores as $entitySupervisor)
        {
            $strSupervisor       = $entitySupervisor['apellidos'] . ' ' . explode(' ', $entitySupervisor['nombres'])[0];
            $arraySupervisores[] = array('id_persona_sup' => $entitySupervisor['id'], 'nombre_sup' => $strSupervisor);
        }
        
        $objRespuesta = new Response(json_encode(array('total' => count($arraySupervisores), 'supervisores' => $arraySupervisores)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }

    /**
     * Documentación para el método 'getAjaxComboAsesoresAction'.
     * 
     * Método que obtiene los asesores que pertenecen a un supervisor
     * 
     * @return Response retorna la lista de asesores por supervisor.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function getAjaxComboAsesoresAction()
    {
        $objPeticion     = $this->get('request');
        $objSesion       = $objPeticion->getSession();
        $intIdSupervisor = $objPeticion->get('supervisor');
        $emComercial     = $this->get('doctrine')->getManager('telconet');
        $arrayAsesores[] = array('login_asesor' => 'Todos', 
                                 'nombre_ase'   => 'Todos');
        
        $objSesion->set('listaAsesores',  null);
        
        if($intIdSupervisor != 'Todos')
        {
            $strPrefijoEmpresa = $objSesion->get('prefijoEmpresa');
            $listaAsesores     = $emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->getResultadoAsesoresPorSupervisor($intIdSupervisor, $strPrefijoEmpresa);
            
            $objSesion->set('listaAsesores',  $listaAsesores);
           
            foreach($listaAsesores as $entityAsesor)
            {
                $arrayAsesores[] = array('login_asesor' => $entityAsesor['login'], 'nombre_ase' => $entityAsesor['asesor']);
            }
        }
        
        $objRespuesta = new Response(json_encode(array('total' => count($arrayAsesores), 'asesores' => $arrayAsesores)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'getAjaxComboJurisdiccionesAction'.
     * 
     * Método que obtiene las jurisdicciones guardadas en sesión.
     * 
     * @return Response retorna la lista de jurisdicciones .
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    public function getAjaxComboJurisdiccionesAction()
    {
        $objPeticion = $this->get('request');
        $objSesion   = $objPeticion->getSession();
        
        $arrayJurisdicciones = $objSesion->get('arrayJurisdicciones');

        $objRespuesta = new Response(json_encode(array('total' => count($arrayJurisdicciones), 'encontrados' => $arrayJurisdicciones)));
        $objRespuesta->headers->set('Content-type', 'text/json');
        return $objRespuesta;
    }

    /**
     * Documentación para el método 'getListaAsesores'.
     * 
     * Método que obtiene los asesores por supervisor.
     * 
     * @param String     $strSupervisor  Identificador del Supervisor.
     * @param String     $strLoginAsesor Identificador del Asesor.
     * @param Repository $objRepository  Objeto repository para realizar la consulta.
     * @param Session    $objSesion      Objeto sesión para la obtención de los supervisores.
     * 
     * @return Response Listado de asesores .
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     */
    private function getListaAsesores($strSupervisor, $strLoginAsesor, $objSesion)
    {
        if($strSupervisor == 'Todos')
        {
            $listaSupervisores = $objSesion->get('listaSupervisores');
            $strPrefijoEmpresa = $objSesion->get('prefijoEmpresa');
            $listaAsesores     = array();
            $emComercial       = $this->get('doctrine')->getManager('telconet');

            foreach($listaSupervisores as $entitySupervisor)
            {
                $arrayAsesores = $emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->getResultadoAsesoresPorSupervisor($entitySupervisor['id'], $strPrefijoEmpresa);
                $listaAsesores = array_merge($listaAsesores, $arrayAsesores);
            }
            if(count($listaAsesores) > 0)
            {
                foreach($listaAsesores as $entityAsesor)
                {
                    $arrayLogins[] = $entityAsesor['login'];
                }
                return $arrayLogins;
            }
            else
            {
                return 'NoTieneAsesores';
            }
        }
        else
        {
            if($strLoginAsesor != 'Todos')
            {
                return array($strLoginAsesor);
            }
            else
            {
                $listaAsesores = $objSesion->get('listaAsesores');
                
                if(count($listaAsesores) > 0)
                {
                    foreach($listaAsesores as $entityAsesor)
                    {
                        $arrayAsesores[] = $entityAsesor['login'];
                    }
                    return $arrayAsesores;
                }
                else
                {
                    return 'NoTieneAsesores';
                }
            }
        }
    }
    
    /**
     * Documentación para el método 'getParametrosVentas'.
     * 
     * Método que obtiene los parámetros generales para la consulta y generación de los reportes de jefatura.
     * 
     * @param Request $objPeticion  Objeto request para obtener el mes y el idEmpresa.
     * 
     * @return Array retorna la lista de parámetros generales para las consultas de los reportes.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 26-10-2015
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 17-01-2016 - Se agrega parametros, mesCreacionPunto, mesAprobacion,
     *                           idPunto, idCliente, direccionPunto, idPlan, idPtoCobertura, idSector,
     *                           EstadoServicio para la consulta de reportes por MD. 
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 25-04-2023 - Se agrega empresa EN para devolver los parámetros correspondientes
     */
    private function getParametrosVentas($objPeticion)
    {
        $strFecha                               = $objPeticion->get('mes');
        $arrayFecha                             = explode('-', $strFecha);
        $arrayParametros['FECHA']               = $arrayFecha;
        $arrayParametros['ES_VENTA']            = 'S';
        $arrayParametros['CMDN']                = '%';
        $arrayParametros['ESTADOS_AM']          = array('Modificado', 'Activo');
        $arrayParametros['ROLES']               = array('Cliente', 'Pre-cliente', 'Cliente Canal');
        $arrayParametros['EMPRESA']             = $objPeticion->getSession()->get('prefijoEmpresa');
        $arrayParametros['OBSERVACION']         = 'Se confirmo el servicio';
        $arrayParametros['OBSRV_BRUTA']         = 'Se solicito planificacion';
        $arrayParametros['ACCION']              = 'confirmarServicio';
        $arrayParametros['ESTADO_BRU']          = array('PLANIFICADA', 'PREPLANIFICADA');
        $arrayParametros['ESTADO_ACT']          = 'Activo';
        $arrayParametros['MES']                 = $arrayFecha[1].$arrayFecha[0];
        
        $arrayParametros['PRODUCTO']    = 'INTERNET DEDICADO';
        $arrayParametros['ESTADOS_PER'] = array('Activo',     'Anulado',         'Cancelado',      'Eliminado', 'Inactivo', 
                                                'Modificado', 'PendAprobSolctd', 'Pend-convertir', 'Pendiente');
        $arrayParametros['ESTADOS_P']   = array('Activo',  'Anulado',        'Cancelado',      'Eliminado', 'In-Corte',
                                                'In-Temp', 'Migracion-ttco', 'migracion_ttco', 'Pendiente', 'Trasladado');
        $arrayParametros['ESTADOS_SH']  = array('Activo',         'Anulado',   'Asignada',   'PreAprobacionMateriales', 
                                                'AsignadoTarea',  'Detenido',  'EnPruebas',  'FactibilidadEnProceso', 
                                                'EnVerificacion', 'Factible',  'In-Corte',   'PreAsignacionInfoTecnica',    
                                                'Planificada',    'In-Temp',   'Rechazado',  'PreFactibilidad',    
                                                'Pendiente',      'Cancel',    'Trasladado', 'Preplanificada',  
                                                'Pre-servicio',   'Eliminado', 'Reubicado',  'In-Corte-SinEje', 
                                                'AnuladoMigra',   'Cancelado', 'Inactivo',   'migracion_ttco', 
                                                'Pre-cancelado',  'Rechazada', 'RePlanificada');
            sort($arrayParametros['ESTADOS_SH']);
        
        if ( $arrayParametros['EMPRESA'] = 'MD' || $arrayParametros['EMPRESA'] = 'EN' )
        {    
            if ( $objPeticion->get('EstadoServicio') != NULL) 
            {   
                $arrayParametros['ESTADO_SERVICIO'] = $objPeticion->get('EstadoServicio');
            }
            
            if ( $objPeticion->get('idPunto') != NULL) 
            {
                $arrayParametros['ID_PUNTO']         = $objPeticion->get('idPunto');
            }
            
            if ( $objPeticion->get('idCliente') != NULL) 
            {
                $arrayParametros['ID_CLIENTE']          = $objPeticion->get('idCliente');
            }
            
            if ( $objPeticion->get('direccionPunto') != NULL) 
            {
                $arrayParametros['DIRECCION_PTO']       = $objPeticion->get('direccionPunto');
            }
            
            if ( $objPeticion->get('idPlan') != NULL) 
            {
                $arrayParametros['ID_PLAN']             = $objPeticion->get('idPlan');
            }
            
            if ( $objPeticion->get('idPtoCobertura') != NULL) 
            {
                $arrayParametros['ID_PTO_COBERTURA']    = $objPeticion->get('idPtoCobertura');
            }
            
            if ( $objPeticion->get('idSector') != NULL) 
            {
                $arrayParametros['ID_SECTOR']           = $objPeticion->get('idSector');
            }
            
            if ( $objPeticion->get('mesCreacionPunto') != NULL) 
            {
                $strFechaCreacionPunto                  = $objPeticion->get('mesCreacionPunto');
                $arrayFechaCreacionPto                  = explode('-', $strFechaCreacionPunto);
                $arrayParametros['FECHA_CREACION_PTO']  = $arrayFechaCreacionPto;
                $arrayParametros['MES_CREACION_PTO']    = $arrayFechaCreacionPto[1].$arrayFechaCreacionPto[0];
            }
            
            if ( $objPeticion->get('mesAprobacion') != NULL) 
            {
                $strFechaAprobacion                     = $objPeticion->get('mesAprobacion');
                $arrayFechaAprobacion                   = explode('-', $strFechaAprobacion);
                $arrayParametros['FECHA_APROBACION_PTO']= $arrayFechaAprobacion;
                $arrayParametros['MES_APROBACION']      = $arrayFechaAprobacion[1].$arrayFechaAprobacion[0];
            }
        }
            
        return $arrayParametros;
    }	
    
    /**
     * Documentación para el método 'getAjaxComboCanalesAction'.
     * 
     * Método para obtener la lista de canales de venta disponibles.
     * Los Canales almacenan en los campos:
     * Valor1 => Identificador del Canal.
     * Valor2 => Descriptivo del Canal.
     * Valor3 => Identificador de grupo 'CANAL' .
     * 
     * @return Response retorna la lista de Canales de venta.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 11-12-2015
     */
    public function getAjaxComboCanalesAction()
    {
        $intEmpresaId = $this->get('request')->getSession()->get('idEmpresa');
        $emComercial  = $this->get('doctrine')->getManager('telconet');
        $strCanales   = 'CANALES_PUNTO_VENTA';
        $strModulo    = 'COMERCIAL';
        $strVal3      = 'CANAL';
        $listaCanales = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get($strCanales, $strModulo, '', '', '', '', $strVal3, '', '', $intEmpresaId);
        
        $arregloCanales[] = array('canal' => '%', 'descripcion' => 'Todos');
        
        foreach($listaCanales as $entityCanal)
        {
            $arregloCanales[] = array('canal' => $entityCanal['valor1'], 'descripcion' => $entityCanal['valor2']);
        }
        
        $objResponse = new Response(json_encode(array('canales' => $arregloCanales)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getAjaxComboPuntoVentaAction'.
     * 
     * Método para obtener la lista de Puntos de Venta por Canal.
     * Los Puntos de Venta almacenan en los campos
     * Valor1 => Identificador del Punto de Venta.
     * Valor2 => Descriptivo del Punto de Venta.
     * Valor3 => Identificador del Canal.
     * Valor4 => Descriptivo del Canal.
     * Valor5 => Nombre de la oficina asociada al punto de venta.
     * 
     * @return Response Lista de Puntos de Venta.
     * 
     * @author Alejandro Domínguez Vargas<adominguez@telconet.ec>
     * @version 1.0 11-12-2015
     */
    public function getAjaxComboPuntoVentaAction()
    {
        $objRequest   = $this->get('request');
        $intEmpresaId = $objRequest->getSession()->get('idEmpresa');
        $emComercial  = $this->get('doctrine')->getManager('telconet');
        $strCanales   = 'CANALES_PUNTO_VENTA';
        $strModulo    = 'COMERCIAL';
        $strVal3      = $objRequest->get('canal');
        
        $arregloPuntosVenta[] = array('punto_venta' => '%', 'descripcion' => 'Todos');
        
        if($strVal3 && $strVal3 != '%')
        {
            $listaPuntosVenta = $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get($strCanales, $strModulo, '', '', '', '', $strVal3, '', '', $intEmpresaId);
            foreach($listaPuntosVenta as $entityPuntoVenta)
            {
                $arregloPuntosVenta[] = array('punto_venta' => $entityPuntoVenta['valor1'], 'descripcion' => $entityPuntoVenta['valor2']);
            }
        }
        
        $objResponse = new Response(json_encode(array('puntos_venta' => $arregloPuntosVenta)));
        $objResponse->headers->set('Content-type', 'text/json');
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_312-3442")
     * 
     * Documentación para el método 'generarReporteResultadosMesAction'.
     * 
     * Genera el reporte excel basado en la consulta de Resultados Mensuales.
     * 
     * @return Excel reporte de Resultados Mensuales.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function generarReporteResultadosMesAction()
    {
        $objSesion         = $this->get('request')->getSession();
        $strEmpresa        = $objSesion->get('empresa');
        $strPrefijo        = $objSesion->get('prefijoEmpresa');
        $strUsuario        = $objSesion->get('user');
        $arrayResultadoMes = $objSesion->get('listaResultadoMes');
        $strMes            = $objSesion->get('mesResultadoMes')[0];
        $strAnio           = $objSesion->get('mesResultadoMes')[1];
        $listaResultadoMes = array();
        $arrayAlignSumary  = array(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        
        foreach($arrayResultadoMes as $objResultadoMes)
        {
            $listaResultadoMes[] = array('JURISDICCION' => $objResultadoMes['jurisdiccion'], 
                                         'VENTABRUTA'   => $objResultadoMes['brutas'],
                                         'VENTAACTIVA'  => $objResultadoMes['activas']) ;
        }
        
        if(!empty($listaResultadoMes))
        {
            $arrayParametros['USUARIO']      = $strUsuario;
            $arrayParametros['EMPRESA']      = $strEmpresa;
            $arrayParametros['PREFIJO']      = $strPrefijo;
            $arrayParametros['TITULO']       = "RESULTADOS DEL MES";
            $arrayParametros['ASUNTO']       = $arrayParametros['TITULO'];
            $arrayParametros['DESC']         = "Resultado de consulta de Resultados del Mes $strMes DE $strAnio";
            $arrayParametros['KEYWORDS']     = "Resultados,Mes";
            $arrayParametros['CATEGORIA']    = "Reporte Resultados del Mes";
            $arrayParametros['FIN']          = 4;
            $arrayParametros['COLFREEZE']    = $arrayParametros['FIN'];
            $arrayParametros['NOMBRE']       = "ResultadosMes_$strMes$strAnio" . "_";
            $arrayParametros['CABECERAS']    = array('Jurisdicción', 'Ventas Brutas', 'Ventas Activas');
            $arrayParametros['RIGHTALIGN']   = array('C', 'D');
            $arrayParametros['INDICECAB']    = 7;
            $arrayParametros['ALTOSUB']      = 5;
            $arrayParametros['ANCHOSUB']     = 10;
            $arrayParametros['SIZESUB']      = $arrayParametros['ALTOSUB'];
            $arrayParametros['SUMMARY']      = array('C', 'D');
            $arrayParametros['ALIGNSUMMARY'] = $arrayAlignSumary;
            $arrayParametros['RANGOSUMMARY'] = array(0, $arrayParametros['FIN']);
            $arrayParametros['SUBTITULO']    = array();
            $arrayParametros['SUBDATA']      = array("$strMes, $strAnio", $arrayParametros['USUARIO'], $arrayParametros['EMPRESA']);
            $arrayParametros['TIPOSDATOS']   = array(PHPExcel_Cell_DataType::TYPE_STRING, 
                                                    PHPExcel_Cell_DataType::TYPE_NUMERIC,
                                                    PHPExcel_Cell_DataType::TYPE_NUMERIC);
            // Ancho de las columnas
            $intAnchoJurisdiccion     = 40;
            $intAnchoVentasBrutas     = 25;
            $intAnchoVentasActivas    = 25;
            $arrayParametros['ANCHO'] = array($intAnchoJurisdiccion, $intAnchoVentasBrutas, $intAnchoVentasActivas);
            
            $this->exportarReporte($listaResultadoMes, $arrayParametros);
        }
        exit;
    }
     
    /**
     * @Secure(roles="ROLE_312-3443")
     * 
     * Documentación para el método 'generarReporteResultadosSupervisorAction'.
     * 
     * Genera el reporte excel basado en la consulta de Resultados por Supervisor.
     * 
     * @return Excel reporte de Resultados por Supervisor.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function generarReporteResultadosSupervisorAction()
    {
        $objSesion  = $this->get('request')->getSession();
        $strEmpresa = $objSesion->get('empresa');
        $strPrefijo = $objSesion->get('prefijoEmpresa');
        $strUsuario = $objSesion->get('user');
        
        $arrayResultadosSup  = $objSesion->get('listaResultadosSupervisores');
        $arrayIndicadoresSup = $objSesion->get('listaIndicadoresSupervisores');
        $arrayResultadosVA   = $objSesion->get('listaVentasAsesor');
        $arrayIndicadoresVA  = $objSesion->get('listaVentasAsesorInd');
        $strMes              = $objSesion->get('mesResultadoSupervisor')[0];
        $strAnio             = $objSesion->get('mesResultadoSupervisor')[1];
        $arrayAlignSumary    = array(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                     PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                                     PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $arrayAlignSumary2   = array(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        
        if(!empty($arrayResultadosSup))
        {
            $arrayParametros['USUARIO']      = $strUsuario;
            $arrayParametros['EMPRESA']      = $strEmpresa;
            $arrayParametros['PREFIJO']      = $strPrefijo;
            $arrayParametros['TITULO']       = "RESULTADOS POR SUPERVISOR";
            $arrayParametros['ASUNTO']       = $arrayParametros['TITULO'];
            $arrayParametros['DESC']         = "Resultado de consulta de Resultados por Supervisor";
            $arrayParametros['KEYWORDS']     = "Resultados,Supervisor";
            $arrayParametros['CATEGORIA']    = "Reporte Resultados por Supervisor";
            $arrayParametros['FIN']          = 5;
            $arrayParametros['INDICECOL']    = 7; // Columna indicadores Ventas por Asesor
            $arrayParametros['NOMBRE']       = "ResultadosSupervisor_$strMes$strAnio" . "_";
            $arrayParametros['CABECERAS']    = array('Supervisor', 'Meta', 'Ventas Activas', '% Cumplimiento');
            $arrayParametros['RIGHTALIGN']   = array('C', 'D', 'E', 'I');
            $arrayParametros['COLUMNAIND']   = 4;
            $arrayParametros['TITULOGRAF']   = 'Cumplimiento';
            $arrayParametros['INDICECAB']    = 7;
            $arrayParametros['ALTOSUB']      = 5;
            $arrayParametros['REGISTROIND']  = $arrayIndicadoresSup;
            $arrayParametros['REGISTROIND2'] = $arrayIndicadoresVA;
            $arrayParametros['ANCHOSUB']     = 10;
            $arrayParametros['SIZESUB']      = $arrayParametros['ALTOSUB'];
            $arrayParametros['SUMMARY']      = array('C', 'D', 'E;=D@/C#'); // Artificio para establecer función secundaria (@ y # Tokens Numéricos)
            $arrayParametros['ALIGNSUMMARY'] = $arrayAlignSumary;
            $arrayParametros['RANGOSUMMARY'] = array(0, $arrayParametros['FIN']);
            // Etiquetas de los datos de origen del reporte (Por defecto en todos: Mes, Usuario, Empresa, Fecha)
            $arrayParametros['SUBTITULO']    = array();
            // Data de informativa del origen del reporte
            $arrayParametros['SUBDATA']      = array("$strMes, $strAnio", $arrayParametros['USUARIO'], $arrayParametros['EMPRESA']);
            // Tipos de datos de las columnas en el órden que aparecen en el listado.
            $arrayParametros['TIPOSDATOS']   = array(PHPExcel_Cell_DataType::TYPE_STRING, 
                                                     PHPExcel_Cell_DataType::TYPE_NUMERIC,
                                                     PHPExcel_Cell_DataType::TYPE_NUMERIC,
                                                     'PERCENTAGE');
            $arrayParametros['LISTA2']        = $arrayResultadosVA;
            $arrayParametros['TITULOGRAF2']   = 'Ventas por Asesor';
            $arrayParametros['CABECERAS2']    = array('Ventas', '', 'Asesores');
            $arrayParametros['COLUMNAIND2']   = 7;
            $arrayParametros['RIGHTALIGN2']   = array();
            $arrayParametros['CENTERALIGN2']  = array('G','H', 'I');
            $arrayParametros['FIN2']          = 9;
            $arrayParametros['ANCHO2']        = array(13, 13, 13);
            $arrayParametros['SUMMARY2']      = array('I');
            $arrayParametros['ALIGNSUMMARY2'] = $arrayAlignSumary2;
            $arrayParametros['RANGOSUMMARY2'] = array(6, 8);
            $arrayParametros['GRAFICO']       = true;
            $arrayParametros['TIPOSDATOS2']   = array(PHPExcel_Cell_DataType::TYPE_STRING, 
                                                      PHPExcel_Cell_DataType::TYPE_STRING,
                                                      PHPExcel_Cell_DataType::TYPE_NUMERIC);
            // Ancho de las columnas
            $intAnchoSupervisor       = 25;
            $intAnchoMeta             = 13;
            $intAnchoVentas           = 13;
            $intAnchoCumplimiento     = 15;
            $arrayParametros['ANCHO'] = array($intAnchoSupervisor, $intAnchoMeta, $intAnchoVentas, $intAnchoCumplimiento);
            
            $this->exportarReporte($arrayResultadosSup, $arrayParametros);
        }
    }
     
    /**
     * @Secure(roles="ROLE_312-3444")
     * 
     * Documentación para el método 'generarReporteResultadosConsolidadosAction'.
     * 
     * Genera el reporte excel basado en la consulta de Resultados Consolidados.
     * 
     * @return Excel reporte de Resultados Consolidados.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function generarReporteResultadosConsolidadosAction()
    {
        $objSesion         = $this->get('request')->getSession();
        $strEmpresa        = $objSesion->get('empresa');
        $strPrefijo        = $objSesion->get('prefijoEmpresa');
        $strUsuario        = $objSesion->get('user');
        $listaSupervisores = $objSesion->get('listaSupervisores');
        $listaTotales      = $objSesion->get('listaTotalesConsolidados');
        $listaMetas        = $objSesion->get('listaMetasConsolidados');
        $strMes            = $objSesion->get('mesResultadosConsolidados')[0];
        $strAnio           = $objSesion->get('mesResultadosConsolidados')[1];
        $strContrato       = $objSesion->get('rcContrato');
        $strJurisdiccion   = $objSesion->get('rcJurisdiccion');
        $strServicio       = $objSesion->get('rcServicio');
        
        $arrayResultadosConsolidados = $objSesion->get('listaResultadosConsolidados');
        $arrayIndicadorAsesores      = $objSesion->get('listaIndicadorAsesores');
        $arrayIndicadorSupervisores  = array();

        for($i = 0; $i < count($listaTotales); $i++)
        {
            $cumplimiento = round(($listaTotales[$i] / $listaMetas[$i]) * 100, 2);
            if($cumplimiento < 70)
            {
                $arrayIndicadorSupervisores[] = "red";
            }
            else if($cumplimiento >= 70 && $cumplimiento < 99)
            {
                $arrayIndicadorSupervisores[] = "yellow";
            }
            else
            {
                $arrayIndicadorSupervisores[] = "green";
            }
        }

        if(!empty($arrayResultadosConsolidados))
        {
            $arrayCabecera    = array();
            $arraySummary     = array();
            $arrayAlignSumary = array();
            $i                = 2;
            
            foreach($listaSupervisores as $entitySupervisor)
            {
                $strText         = explode(' ', $entitySupervisor['apellidos'])[0]. ' ' .explode(' ', $entitySupervisor['nombres'])[0];
                $arrayCabecera[] = ucwords(strtolower($strText));
                $arraySummary[]  = $this->getLetras()[$i];
                $i++;
            }
            $arraySummary[]   = $this->getLetras()[$i];
            $arrayLetras      = $this->getLetras();
            $arrayCenterAlign = array();
            $arrayAncho       = array();
            
            array_push($arrayCabecera, 'Total General');
            
            for($i = 0; $i < count($arrayCabecera); $i++)
            {
                $arrayCenterAlign[] = $arrayLetras[$i + 2];
                $arrayAncho[]       = strlen($arrayCabecera[$i]) + 3; // Ancho de la columna del supervisor
            }
            
            array_unshift($arrayCabecera, 'Asesores');
            
            $intColumnas    = count($arrayCabecera);
            $arrayTipoDatos = array(PHPExcel_Cell_DataType::TYPE_STRING);
            
            for($i = 0; $i < $intColumnas - 1 ; $i++)
            {
                $arrayTipoDatos[]   = 'INTSTR';
                $arrayAlignSumary[] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
            }
            
            $arrayParametros['USUARIO']      = $strUsuario;
            $arrayParametros['EMPRESA']      = $strEmpresa;
            $arrayParametros['PREFIJO']      = $strPrefijo;
            $arrayParametros['TITULO']       = "REPORTE RESULTADOS CONSOLIDADOS";
            $arrayParametros['ASUNTO']       = $arrayParametros['TITULO'];
            $arrayParametros['DESC']         = "Resultado de consulta de Listado Resultados Consolidados.";
            $arrayParametros['KEYWORDS']     = "Consolidado";
            $arrayParametros['CATEGORIA']    = "Reporte Resultados Consolidados";
            $arrayParametros['FIN']          = ++$intColumnas;
            $arrayParametros['COLFREEZE']    = $arrayParametros['FIN'];
            $arrayParametros['NOMBRE']       = "ResultadosConsolidados_";
            $arrayParametros['CABECERAS']    = $arrayCabecera;
            $arrayParametros['RIGHTALIGN']   = array();
            $arrayParametros['COLUMNAIND']   = $arrayParametros['FIN'] - 1;
            $arrayParametros['CENTERALIGN']  = $arrayCenterAlign;
            $arrayParametros['INDICECAB']    = 10;
            $arrayParametros['ALTOSUB']      = 8;
            $arrayParametros['CABECERAIND']  = $arrayIndicadorSupervisores;
            $arrayParametros['REGISTROIND']  = $arrayIndicadorAsesores;
            $arrayParametros['ANCHOSUB']     = 12;
            $arrayParametros['SIZESUB']      = $arrayParametros['ALTOSUB'];
            $arrayParametros['SUMMARY']      = $arraySummary;
            $arrayParametros['ALIGNSUMMARY'] = $arrayAlignSumary;
            $arrayParametros['RANGOSUMMARY'] = array(0, $arrayParametros['FIN']);
            $arrayParametros['SUBTITULO']    = array('Contrato:', 'Jurisdicción:', 'Servicio:');
            $arrayParametros['SUBDATA']      = array("$strMes, $strAnio", 
                                                      $strContrato, 
                                                      $strJurisdiccion, 
                                                      $strServicio, 
                                                      $arrayParametros['USUARIO'], 
                                                      $arrayParametros['EMPRESA']);
            // Se agregan los tipos de datos, desde la segunda columna todos son numéricos.
            $arrayParametros['TIPOSDATOS']   = $arrayTipoDatos;
            
            $intMayorAsesor = 0;  
            
            foreach($arrayResultadosConsolidados as $entityConsolidado)
            {
                $intMayorAsesor = $this->calcularMayor($entityConsolidado["asesores"], $intMayorAsesor); // Ancho de la columna del Asesor
            }
            
            $intMayorTotalGeneral = 15;
            
            array_unshift($arrayAncho, $intMayorAsesor);
            array_push($arrayAncho, $intMayorTotalGeneral);
            
            $arrayParametros['ANCHO'] = $arrayAncho;
            $this->exportarReporte($arrayResultadosConsolidados, $arrayParametros);
        }
    }
     
    /**
     * @Secure(roles="ROLE_312-3445")
     * 
     * Documentación para el método 'generarReporteRechazosVentasAction'.
     * 
     * Genera el reporte excel basado en la consulta de Rechazos en Ventas.
     * 
     * @return Excel reporte de Rechazos en Ventas.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function generarReporteRechazosVentasAction()
    {
        $objSesion           = $this->get('request')->getSession();
        $strEmpresa          = $objSesion->get('empresa');
        $strPrefijo          = $objSesion->get('prefijoEmpresa');
        $strUsuario          = $objSesion->get('user');
        $arrayRechazosVentas = $objSesion->get('listaRechazosVentas');
        $strMes              = $objSesion->get('mesRechazosVentas')[0];
        $strAnio             = $objSesion->get('mesRechazosVentas')[1];
        $strJurisdiccion     = $objSesion->get('rvJurisdiccion');
        $strSupervisor       = $objSesion->get('rvSupervisor');
        $strAsesor           = $objSesion->get('rvAsesor');
        
        $listaRechazosVentas = array();
        $arrayAlignSumary    = array(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT, PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        
        foreach($arrayRechazosVentas as $objResultadoMes)
        {
            $listaRechazosVentas[] = array('MOTIVO'     => $objResultadoMes['motivo_desc'], 
                                           'RECHAZO'    => $objResultadoMes['cant_rechazos'],
                                           'PORCENTAJE' => $objResultadoMes['porc_rechazos'] . " %") ;
        }
        
        if(!empty($listaRechazosVentas))
        {
            $arrayParametros['USUARIO']      = $strUsuario;
            $arrayParametros['EMPRESA']      = $strEmpresa;
            $arrayParametros['PREFIJO']      = $strPrefijo;
            $arrayParametros['TITULO']       = "REPORTE DE RECHAZOS EN VENTAS";
            $arrayParametros['ASUNTO']       = $arrayParametros['TITULO'];
            $arrayParametros['DESC']         = "Resultado de consulta de Listado Rechazos en Ventas.";
            $arrayParametros['KEYWORDS']     = "Canales";
            $arrayParametros['CATEGORIA']    = "Reporte Rechazos en Ventas";
            $arrayParametros['FIN']          = 4;
            $arrayParametros['COLFREEZE']    = $arrayParametros['FIN'];
            $arrayParametros['NOMBRE']       = "RechazosVentas_";
            $arrayParametros['CABECERAS']    = array('Motivo', ucwords(strtolower($strJurisdiccion)), '% de Rechazo');
            $arrayParametros['RIGHTALIGN']   = array('C', 'D');
            $arrayParametros['INDICECAB']    = 10;
            $arrayParametros['ALTOSUB']      = 8;
            $arrayParametros['ANCHOSUB']     = 12;
            $arrayParametros['SIZESUB']      = $arrayParametros['ALTOSUB'];
            $arrayParametros['SUMMARY']      = array('C', 'D');
            $arrayParametros['ALIGNSUMMARY'] = $arrayAlignSumary;
            $arrayParametros['RANGOSUMMARY'] = array(0, $arrayParametros['FIN']);
            $arrayParametros['SUBTITULO']    = array('Supervisor:', 'Asesor:', 'Jurisdicción:');
            $arrayParametros['SUBDATA']      = array("$strMes, $strAnio", 
                                                     $strSupervisor, 
                                                     $strAsesor, 
                                                     $strJurisdiccion, 
                                                     $arrayParametros['USUARIO'], 
                                                     $arrayParametros['EMPRESA']);
            $arrayParametros['TIPOSDATOS']  = array(PHPExcel_Cell_DataType::TYPE_STRING,
                                                    PHPExcel_Cell_DataType::TYPE_NUMERIC,
                                                    'PERCENTAGE');
            
            $intMayorMotivo       = 0;  
            $intMayorJurisdiccion = strlen($strJurisdiccion) + 8; 
            $intMayorPorRechazo   = 15;
            foreach($listaRechazosVentas as $entityRechazo)
            {
                $intMayorMotivo = $this->calcularMayor($entityRechazo['MOTIVO'], $intMayorMotivo);
            }
            $arrayParametros['ANCHO'] = array($intMayorMotivo, $intMayorJurisdiccion, $intMayorPorRechazo);
            $this->exportarReporte($listaRechazosVentas, $arrayParametros);
        }
    }
    
    /**
     * @Secure(roles="ROLE_312-3446")
     * 
     * Documentación para el método 'generarReporteVentasJefaturaAction'.
     * 
     * Genera el reporte excel basado en la consulta de Ventas de la Jefatura.
     * 
     * @return Excel reporte de Ventas de la Jefatura.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    public function generarReporteVentasJefaturaAction()
    {
        ini_set('max_execution_time', 3000000);
        $objSesion           = $this->get('request')->getSession();
        $strEmpresa          = $objSesion->get('empresa');
        $strPrefijo          = $objSesion->get('prefijoEmpresa');
        $strUsuario          = $objSesion->get('user');
        $arrayVentasJefatura = $objSesion->get('listaVentasJefatura');
        $strMes              = $objSesion->get('mesVentasJefatura')[0];
        $strAnio             = $objSesion->get('mesVentasJefatura')[1];
        $strCanal            = $objSesion->get('vjCanal');
        $strPuntoVenta       = $objSesion->get('vjPuntoVenta');
        $strSupervisor       = $objSesion->get('vjSupervisor');
        $strAsesor           = $objSesion->get('vjAsesor');
        
        if(!empty($arrayVentasJefatura))
        {
            $arrayParametros['USUARIO']      = $strUsuario;
            $arrayParametros['EMPRESA']      = $strEmpresa;
            $arrayParametros['PREFIJO']      = $strPrefijo;
            $arrayParametros['TITULO']       = "REPORTE DE VENTAS DE JEFATURA";
            $arrayParametros['ASUNTO']       = $arrayParametros['TITULO'];
            $arrayParametros['DESC']         = "Resultado de consulta de Listado Ventas de la Jefatura.";
            $arrayParametros['KEYWORDS']     = "Ventas, Jefatura";
            $arrayParametros['CATEGORIA']    = "Reporte Ventas de la Jefatura";
            $arrayParametros['FIN']          = 19;
            $arrayParametros['COLFREEZE']    = $arrayParametros['FIN'];
            $arrayParametros['NOMBRE']       = "VentasJefatura_";
            $arrayParametros['CABECERAS']    = array('Login',   'Servicio',       'Dirección',      'Plan',     'Jurisdicción', 'Sector', 
                                                    'Cliente', 'Empresa',        'Identificación', 'Asesor',   'Usuario',      'Supervisor', 
                                                    'Canal',   'Punto de Venta', 'Aprobación',     'Creación', 'Activación',   'Precio');
            $arrayParametros['RIGHTALIGN']   = array('S');
            $arrayParametros['INDICECAB']    = 11;
            $arrayParametros['ALTOSUB']      = 9;
            $arrayParametros['ANCHOSUB']     = 12;
            $arrayParametros['SIZESUB']      = $arrayParametros['ALTOSUB'];
            $arrayParametros['SUMMARY']      = array('S');
            $arrayParametros['ALIGNSUMMARY'] = array(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $arrayParametros['RANGOSUMMARY'] = array(0, $arrayParametros['FIN']);
            $arrayParametros['SUBTITULO']    = array('Canal:', 'Punto:', 'Supervisor:', 'Asesor:');
            $arrayParametros['SUBDATA']      = array("$strMes, $strAnio", 
                                                      $strCanal, 
                                                      $strPuntoVenta, 
                                                      $strSupervisor, 
                                                      $strAsesor, 
                                                      $arrayParametros['USUARIO'], 
                                                      $arrayParametros['EMPRESA']);
            for($i = 0; $i < 17; $i++)
            {
                $arrayTipoDatos[] = PHPExcel_Cell_DataType::TYPE_STRING;
            }
            
            $arrayTipoDatos[]              = 'FLOAT';
            $arrayParametros['TIPOSDATOS'] = $arrayTipoDatos;
            
            $intMayorLogin  = 0;  $intMayorServicio   = 0;  $intMayorDireccion  = 0; $intMayorPlan     = 0;  $intMayorJurisdiccion   = 0; 
            $intMayorSector = 0;  $intMayorCliente    = 0;  $intMayorVendedor   = 0; $intMayorUsuario  = 0;  $intMayorIdentificacion = 0;
            $intMayorCanal  = 0;  $intMayorSupervisor = 0;  $intMayorPuntoVenta = 0; $intMayorCreacion = 15; $intMayorActivacion     = 15; 
            $intMayorPrecio = 15; $intMayorAprobacion = 15; $intMayorEmpresa    = 0;
            
            // En base a la data se determina el ancho de cada columna.
            foreach($arrayVentasJefatura as $entityVenta)
            {
                $intMayorLogin          = $this->calcularMayor($entityVenta['login'],           $intMayorLogin);
                $intMayorServicio       = $this->calcularMayor($entityVenta['servicio'],        $intMayorServicio);
                $intMayorDireccion      = $this->calcularMayor($entityVenta['direccion'],       $intMayorDireccion);
                $intMayorPlan           = $this->calcularMayor($entityVenta['nombre_servicio'], $intMayorPlan);
                $intMayorJurisdiccion   = $this->calcularMayor($entityVenta['jurisdiccion_'],   $intMayorJurisdiccion);
                $intMayorSector         = $this->calcularMayor($entityVenta['sector'],          $intMayorSector);
                $intMayorCliente        = $this->calcularMayor($entityVenta['cliente'],         $intMayorCliente);
                $intMayorEmpresa        = $this->calcularMayor($entityVenta['empresa'],         $intMayorEmpresa);
                $intMayorIdentificacion = $this->calcularMayor($entityVenta['identificacion'],  $intMayorIdentificacion);
                $intMayorVendedor       = $this->calcularMayor($entityVenta['vendedor'],        $intMayorVendedor);
                $intMayorUsuario        = $this->calcularMayor($entityVenta['usuario'],         $intMayorUsuario);
                $intMayorSupervisor     = $this->calcularMayor($entityVenta['supervisor'],      $intMayorSupervisor);
                $intMayorCanal          = $this->calcularMayor($entityVenta['canal'],           $intMayorCanal);
                $intMayorPuntoVenta     = $this->calcularMayor($entityVenta['punto_venta'],     $intMayorPuntoVenta);
            }
            $arrayParametros['ANCHO'] = array($intMayorLogin,          $intMayorServicio,   $intMayorDireccion,  $intMayorPlan, 
                                              $intMayorJurisdiccion,   $intMayorSector,     $intMayorCliente,    $intMayorEmpresa, 
                                              $intMayorIdentificacion, $intMayorVendedor,   $intMayorUsuario,    $intMayorSupervisor, 
                                              $intMayorCanal,          $intMayorPuntoVenta, $intMayorAprobacion, $intMayorCreacion,   
                                              $intMayorActivacion,     $intMayorPrecio );
                                          
            $this->exportarReporte($arrayVentasJefatura, $arrayParametros);
        }
    }
    
    /**
     * Documentación para el método 'exportarReporte'.
     * 
     * Crea la serie de reportes basado los parámetros ingresados.
     * 
     * @param Array  $arrayListadoReporte   Lista con los registros que conforman la información del reporte.
     * @param Array  $arrayParametros
     *               $arrayParametros['USUARIO']      = STRING: Usuario sesión.
     *               $arrayParametros['EMPRESA']      = STRING: Empresa sesión.
     *               $arrayParametros['PREFIJO']      = STRING: Prefijo Empresa sesión.
     *               $arrayParametros['TITULO']       = STRING: Título del Reporte.
     *               $arrayParametros['ASUNTO']       = STRING: Asunto del reporte, es igual al título.
     *               $arrayParametros['DESC']         = STRING: Descriptivo del reporte.
     *               $arrayParametros['KEYWORDS']     = STRING: Palabras clave del reporte.
     *               $arrayParametros['CATEGORIA']    = STRING: Categoría del Reporte.
     *               $arrayParametros['FIN']          = INT:    índice de la última columna del reporte.
     *               $arrayParametros['COLFREEZE']    = INT:    indicador para congelar filas y columnas.
     *               $arrayParametros['NOMBRE']       = Prefijo del nombre del archivo excel.
     *               $arrayParametros['CABECERAS']    = ARRAY:  Nombres de la cabeceras de columna del reporte.
     *               $arrayParametros['RIGHTALIGN']   = ARRAY:  Columnas que se alinean a la derecha.
     *               $arrayParametros['CENTERALIGN']  = ARRAY:  Columnas que se alinean al centro.
     *               $arrayParametros['COLUMNAIND']   = STRING: Columna donde se mostrará el indicador cumplimiento(Sólo reportes donde se necesiten).
     *               $arrayParametros['GRAFICO']      = BOOL:   Define si el reporte genera o no gráfico.
     *               $arrayParametros['TITULOGRAF']   = STRING: Título del gráfico(Si lo requiere).
     *               $arrayParametros['INDICECAB']    = INT:    Índice de fila donde irá la cabecera de las columnas del reporte.
     *               $arrayParametros['ALTOSUB']      = INT:    Cantidad de filas con alto fijo #14, se basa en la cantidad de filtros de consulta.
     *               $arrayParametros['ANCHOSUB']     = INT:    determina el ancho de la columna A.
     *               $arrayParametros['SIZESUB']      = INT:    Cantidad de filas de datos descriptivos del reporte, siempre igual a ['ALTOSUB'].
     *               $arrayParametros['SUMMARY']      = ARRAY:  Columnas que sumarizan.
     *               $arrayParametros['ALIGNSUMMARY'] = ARRAY:  Estilo de alineamiento de las columnas que sumarizan, se debe respetar el orden.
     *               $arrayParametros['RANGOSUMMARY'] = ARRAY:  rango de incio y fin de la fila donde se presenta el resumen.
     *               $arrayParametros['SUBTITULO']    = ARRAY:  Nombres de los Datos de filtrado del reporte.
     *               $arrayParametros['SUBDATA']      = ARRAY:  Valore de los Datos de filtrado del reporte.
     *               $arrayParametros['TIPOSDATOS']   = ARRAY:  Tipo de dato para cada columna del reporte.
     *               $arrayParametros['ANCHO']        = ARRAY:  Tamaño del ancho de las columnas del reporte.
     * 
     * @return Documento Excel.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     * 
     * @author Edgar Pin villavicencio <epin@telconet.ec>
     * @version 1.1 24-04-2023 - Se agrega la empresa EN para obtener el logo
     * 
     */
    private function exportarReporte($arrayListadoReporte, $arrayParametros)
    {
        $objPHPExcel   = new PHPExcel();
        $cacheMethod   = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $cacheSettings = array(' memoryCacheSize ' => '2048MB');

        PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
        $objWorkSheet = $objPHPExcel->createSheet();
        $objPHPExcel->setActiveSheetIndex(0);
        
        // MetaData del archivo a exportar.
        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($arrayParametros['USUARIO']);
        $objPHPExcel->getProperties()->setTitle($arrayParametros['TITULO']);
        $objPHPExcel->getProperties()->setSubject($arrayParametros['ASUNTO']);
        $objPHPExcel->getProperties()->setDescription( $arrayParametros['DESC']);
        $objPHPExcel->getProperties()->setKeywords($arrayParametros['KEYWORDS']);
        $objPHPExcel->getProperties()->setCategory($arrayParametros['CATEGORIA']);
        
        // Se inserta logo de la empresa.
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        
        if($arrayParametros['PREFIJO'] == 'MD' )
        {
            $objDrawing->setPath("./public/images/rep_md.png");
        }
        elseif ($arrayParametros['PREFIJO'] == 'EN')
        {
            $objDrawing->setPath("./public/images/rep_en.png");
        }
        else
        {
            $objDrawing->setPath("./public/images/rep_tn.png");
        }
        
        $objDrawing->setCoordinates("A1");
        $objDrawing->setWorksheet($objWorkSheet);
        
        $arrayStyleSubTitulo       = $this->getEstiloSubTitulo();
        $arrayStyleBorderCompletos = $this->getEstiloBordesCompletos();
        
        $intIndiceCab     = $arrayParametros['INDICECAB'];
        $indiceAnterior   = $intIndiceCab - 1;
        $strLastChar      = $this->getLetras()[$arrayParametros['FIN'] - 1];
        $arrayCenterAlign = array();
        
        // Se combinan las celdas que alojorán la data de origen del reporte.
        $objWorkSheet->mergeCells('B1:' . $strLastChar . '1');
        $objWorkSheet->mergeCells("A$indiceAnterior:$strLastChar$indiceAnterior");
        // Título del Reporte
        $objWorkSheet->setCellValue('B1', $arrayParametros['TITULO']);
        // Formato del Título del Reporte
        $this->setEstiloCelda($objWorkSheet, $this->getEstiloTitulo(), array('B1'));
        // Bordes Completos de Fila principal del título del Reporte.
        $objWorkSheet->getStyle("A1:" . $strLastChar . "1")->applyFromArray($arrayStyleBorderCompletos);
        // Se usa el nombre del titulo para generar el nombre
        $arrayParametros['TITULO'] = str_replace(' ', '', ucwords(strtolower($arrayParametros['TITULO'])));
        // Bordes Completos del cuadro de Data de Origen del Reporte.
        $objWorkSheet->getStyle("A2:$strLastChar$intIndiceCab")->applyFromArray($arrayStyleBorderCompletos);
        
        // Arma el arreglo de celdas para las etiquetas de la data de origen del reporte
        $arraySubtituloStyle     = array();
        $arraySubtituloDataStyle = array();
        
        for($i = 2; $i <= $arrayParametros['SIZESUB']; $i++)
        {
            $arraySubtituloStyle[]     = "A$i";
            $arraySubtituloDataStyle[] = "B$i";
        }
        // Estilo para las etiquetas de la data de origen del reporte.
        $this->setEstiloCelda($objWorkSheet, $arrayStyleSubTitulo, $arraySubtituloStyle);
        // Se quita el estilo 'Negrita' a la fuente de la data informativa.
        $arrayStyleSubTitulo['font']['bold'] = false;
        // Estilo para la información de la data de origen del reporte.
        $this->setEstiloCelda($objWorkSheet, $arrayStyleSubTitulo, $arraySubtituloDataStyle);
        
        // Preparación de datos para el formato de celdas de la cabecera de registros del reporte.
        if(isset($arrayParametros['CENTERALIGN']))
        {
            $arrayCenterAlign = $arrayParametros['CENTERALIGN'];
        }
        $arrayParametros2 = array('RIGHTALIGN'  => $arrayParametros['RIGHTALIGN'], 
                                  'CENTERALIGN' => $arrayCenterAlign, 
                                  'FIN'         => $arrayParametros['FIN'], 
                                  'INDICE'      => $intIndiceCab);
        
        // Se establece el estilo para cada celda la cabecera de registros del reporte.
        $this->setEstiloXFila($objWorkSheet, $arrayParametros2);
        // Se setea el valor de cada celda la cabecera de registros del reporte.
        $this->setValorXCeldaCabecera($objWorkSheet, $intIndiceCab, $arrayParametros['CABECERAS']);
        
        $this->setAltoFila($objWorkSheet, $arrayParametros['ALTOSUB']);
        
        $this->setAnchoColumna($objWorkSheet, $arrayParametros['ANCHO'], $arrayParametros['ANCHOSUB']);

        array_unshift($arrayParametros['SUBTITULO'], 'Mes:');
        array_push($arrayParametros['SUBTITULO'], 'Usuario:', 'Empresa:', 'Fecha:');
        
        $i = 2;
        
        foreach($arrayParametros['SUBTITULO'] as $strSubTitulo)
        {
            $objWorkSheet->setCellValue("A$i", $strSubTitulo);
            $i++;
        }
        
        $j = 2;
        
        // Procesa los valores de la data de filtrado del reporte
        foreach($arrayParametros['SUBDATA'] as $strValorSub)
        {
            $objWorkSheet->setCellValue("B$j", $strValorSub); 
            $objWorkSheet->mergeCells("B$j:$strLastChar$j");
            $j++;
        }
        $objWorkSheet->mergeCells("B$j:$strLastChar$j");
        $objWorkSheet->setCellValue("B$j", date('d') . "/" . $this->getMeses()[date('n') - 1] . "/" . date('Y'));

        $intIndiceListado  = $intIndiceCab + 1;
        
        // Inmoviliza columnas y filas si se lo especifica.
        if(isset($arrayParametros['COLFREEZE']))
        {
            $objWorkSheet->freezePaneByColumnAndRow($arrayParametros['COLFREEZE'], $intIndiceListado);
        }
        
        if(isset($arrayParametros['CABECERAIND']))
        {
            $this->agregarIndicadoresCabecera($objWorkSheet, $arrayParametros['CABECERAIND'], $intIndiceCab);
        }
        
        $arrayParametrosRegistro['LISTADOREPORTE']   = $arrayListadoReporte;
        $arrayParametrosRegistro['ARRAYPARAMETROS']  = $arrayParametros;
        $arrayParametrosRegistro['ARRAYPARAMETROS2'] = $arrayParametros2;
        
        if(isset($arrayParametros['TITULOGRAF']))
        {
            $arrayParametrosRegistro['TITULOGRAF'] = $arrayParametros['TITULOGRAF'];
        }

        if(isset($arrayParametros['GRAFICO']))
        {
            $arrayParametrosRegistro['GRAFICO'] = $arrayParametros['GRAFICO'];
        }
        else
        {
            $arrayParametrosRegistro['GRAFICO'] = false;
        }
        
        $arrayLastInd['FIRSTCHAR'] = 'A';
        $arrayLastInd['LASTCHAR']  = $strLastChar;
        
        // Se Insertan los registros del listado principal del reporte
        $this->insertarRegistrosListado($objWorkSheet, $intIndiceListado, $arrayParametrosRegistro, $arrayLastInd);
        
        // Se insertan los registros del listado secundario, de tener uno.
        if(isset($arrayParametros['LISTA2']))
        {
            //Preparación de datos del listado secundario
            $intIndiceListado2 = $intIndiceCab + 1; // Fila de incio del listado secundario.
            
            $arrayParametros['RIGHTALIGN']  = array('I');
            $arrayParametros['CENTERALIGN'] = array('H');
            
            $arrayParametrosRegistro['LISTADOREPORTE'] = $arrayParametros['LISTA2'];

            $arrayLetras  = $this->getLetras();
            $intInicioReg = $arrayParametros['FIN'] + 1;
            $c            = $intInicioReg;
            
            // Se inserta los nombres de las cabeceras del listado secundario
            foreach($arrayParametros['CABECERAS2'] as $strValor)
            {
                $objWorkSheet->setCellValue($arrayLetras[$c] . $intIndiceCab, $strValor);
                $c++;
            }
            $arrayParametros2['FIN'] = $arrayParametros['FIN2'];
            $this->setEstiloXFila($objWorkSheet, $arrayParametros2, 6);
            
            $arrayParametrosRegistro['ARRAYPARAMETROS']['ESADICIONAL']  = true;
            $arrayParametrosRegistro['ARRAYPARAMETROS']['PORCENTAJE']   = false;
            $arrayParametrosRegistro['ARRAYPARAMETROS']['ESLISTA2']     = true;
            
            $arrayParametrosRegistro['ARRAYPARAMETROS']['SUMMARY']      = $arrayParametrosRegistro['ARRAYPARAMETROS']['SUMMARY2'];
            $arrayParametrosRegistro['ARRAYPARAMETROS']['ALIGNSUMMARY'] = $arrayParametrosRegistro['ARRAYPARAMETROS']['ALIGNSUMMARY2'];
            $arrayParametrosRegistro['ARRAYPARAMETROS']['RANGOSUMMARY'] = $arrayParametrosRegistro['ARRAYPARAMETROS']['RANGOSUMMARY2'];
            
            $arrayParametrosRegistro['GRAFICO']    = true;
            $arrayParametrosRegistro['TITULOGRAF'] = $arrayParametros['TITULOGRAF2'];
            
            $arrayLastInd['FIRSTCHAR'] = 'G';
            $arrayLastInd['LASTCHAR']  = 'I';
           
            $intIndiceListado = $intIndiceListado2 > $intIndiceListado ? $intIndiceListado2 : $intIndiceListado;
            $this->insertarRegistrosListado($objWorkSheet, $intIndiceListado2, $arrayParametrosRegistro, $arrayLastInd);
        }
        
        $objWorkSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objWorkSheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        $objWorkSheet->setTitle($arrayParametros['TITULO']);

        // Se elimina la hoja de trabajo por defecto
        $objPHPExcel->removeSheetByIndex(0);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $arrayParametros['NOMBRE'] . date('dMY_His') . '.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->setIncludeCharts(TRUE);
        $objWriter->save('php://output');
        
        exit;
    }
    
    /**
     * Documentación para el método 'getEstiloTitulo'.
     * 
     * Estructura el arreglo que define el estílo para Título del reporte en excel.
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloTitulo()
    {
        return array('font'      => array('bold'   => true,
                                          'color'  => array('rgb' => '000000'),
                                          'size'   => 14,
                                          'name'   => 'Droid Sans'),
                     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                          'indent'     => 6),
                     'fill'      => array('type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                          'color' => array('rgb' => 'FFFFFF')));
    }
    
    /**
     * Documentación para el método 'getEstiloSubTitulo'.
     * 
     * Estructura el arreglo que define el estílo para Subtítulo del reporte en excel.
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloSubTitulo()
    {
        return array('font'      => array('bold'  => true,
                                          'size'  => 10,
                                          'name'  => 'Droid Sans'),
                     'alignment' => array('vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER),
                     'fill'      => array('type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                          'color' => array('rgb' => 'FFFFFF')));
    }
    
    /**
     * Documentación para el método 'getEstiloCabecera'.
     * 
     * Estructura el arreglo que define el estílo para la cabecera las columnas del reporte en excel.
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloCabecera()
    {
        return array('font'      => array('bold'  => false,
                                          'color' => array('rgb' => '000000'),
                                          'size'  => 12,
                                          'name'  => 'Droid Sans'),
                     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                                          'indent'     => 0),
                     'borders'   => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                          'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                          'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                          'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
                     'fill'      => array('type'   => PHPExcel_Style_Fill::FILL_SOLID,
                                          'color'  => array('rgb' => '75BEF1')));
    }
    
    /**
     * Documentación para el método 'getEstiloLineaPar'.
     * 
     * Estructura el arreglo que define el estílo para los registro pares reporte en excel.
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloLineaPar()
    {
        return array('font'      => array('bold'  => false,
                                          'size'  => 10,
                                          'name'  => 'Droid Sans'),
                     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                                          'indent'     => 0),
                     'borders'   => array('right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                          'left'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
                     'fill'      => array('type'  => PHPExcel_Style_Fill::FILL_SOLID, 
                                          'color' => array('rgb' => 'CEE3F6')));
    }
    
    /**
     * Documentación para el método 'getEstiloLineaImpar'.
     * 
     * Estructura el arreglo que define el estílo para los registro impares reporte en excel.
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloLineaImpar()
    {
        return array('font'      => array('bold'  => false,
                                          'size'  => 10,
                                          'name'  => 'Droid Sans'),
                     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
                                          'indent'     => 0),
                     'borders'   => array('right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                          'left'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)),
                     'fill'      => array('type'  => PHPExcel_Style_Fill::FILL_SOLID, 
                                          'color' => array('rgb' => 'FFFFFF')));
    }
    
    /**
     * Documentación para el método 'getEstiloBordesCompletos'.
     * 
     * Estructura el arreglo que define el estílo para las celdas del reporte en excel que llevan todos los bordes marcados .
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloBordesCompletos()
    {
        return array('borders' => array('top'    => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'left'   => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                                        'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN)));
    }
    
    /**
     * Documentación para el método 'getEstiloSumario'.
     * 
     * Estructura el arreglo que define el estílo para la línea del sumarizado del reporte en excel.
     * 
     * @return Array definición de estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getEstiloSumario()
    {
        return array('font'      => array('bold'   => true,
                                          'color'  => array('rgb' => 'FFFFFF'),
                                          'size'   => 10,
                                          'name'   => 'Droid Sans'),
                     'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                          'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                          'indent'     => 0),
                     'fill'      => array('type'  => PHPExcel_Style_Fill::FILL_SOLID,
                                          'color' => array('rgb' => '000000')));
    }
    
    /**
     * Documentación para el método 'insertarRegistrosListado'.
     * 
     * Método que inserta los registros del listado que conforman el reporte en excel.
     * 
     * @param WorkSheet $objWorkSheet            Hoja activa del documento excel.
     * @param Integer   $intIndiceListado        Índice donde inicia el listado.
     * @param Array     $arrayParametros['INDICE']      : Integer Indicador de la posición del registro.
     *                  $arrayParametros['ESTILO']      : Array   Estilo de la celda.
     *                  $arrayParametros['FIN']         : Integer Indicador de ultima columna.
     *                  $arrayParametros['TIPOSDATOS']  : Array   Tipo de datos por culumna.
     *                  $arrayParametros['RIGHTALIGN']  : Array   Columnas que se alinean a la derecha.
     *                  $arrayParametros['CENTERALIGN'] : Array   Columnas que se alinean al centro.
     *                  $arrayParametros['ESADICIONAL'] : Bool    Indica si hay una segunda lista.
     *                  $arrayParametros['ALIGNSUMMARY']: Array   Alinación de cada línea del sumario en orden de inserción.
     *                  $arrayParametros['REGISTROIND'] : Array   Lista de datos indicadores (Verde, Amarillo, Rojo) en orden del listado
     *                  $arrayParametros['COLUMNAIND']  : Array   Columnas de los datos indicadores.
     *                  $arrayParametros['RANGOSUMMARY']: Array   Indice inicial y final de las columnas que forman la linea del sumario.
     *                  $arrayParametros['SUMMARY']     : Array   Columnas que sumarizan.
     * 
     *                  $arrayParametrosRegistro['LISTADOREPORTE']: Listado con los registros del reporte
     * @param Array     $arrayLastInd            Arreglo con la primera y última columna del reporte.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function insertarRegistrosListado($objWorkSheet, $intIndiceListado, $arrayParametrosRegistro, $arrayLastInd)
    {
        $arrayParametros     = $arrayParametrosRegistro['ARRAYPARAMETROS'];
        $arrayListadoReporte = $arrayParametrosRegistro['LISTADOREPORTE'];
        $intRegistro         = 1;
        $intCountReg         = 0;
        $intIndRangoIni      = $intIndiceListado;
        $strFirstChar        = $arrayLastInd['FIRSTCHAR'];
        $strLastChar         = $arrayLastInd['LASTCHAR'];
        $intCountCol         = count($arrayListadoReporte);

        foreach($arrayListadoReporte as $entityRegistro)
        {
            $arrayParametros2['ENTITYREGISTRO'] = $entityRegistro;
            $arrayParametros2['INDICE']         = $intIndiceListado;
            $arrayParametros2['CELLDATATYPE']   = $arrayParametros['TIPOSDATOS'];
            
            $intCountReg               = count($entityRegistro);
            $arrayParametros['INDICE'] = $intIndiceListado;
            
            if($intRegistro % 2 == 0)
            {
                $arrayParametros['ESTILO'] = $this->getEstiloLineaPar();
            }
            else
            {
                $arrayParametros['ESTILO'] = $this->getEstiloLineaImpar();
            }
            
            if(!isset($arrayParametros['ESADICIONAL'])) // Primer Listado
            {
                $intIndLetrasIniGrafic = 1;
                if($arrayParametrosRegistro['GRAFICO'])
                {
                    $arrayParametrosRegistro['INDLETCATEGORIAS'] = $intIndLetrasIniGrafic;
                    $arrayParametrosRegistro['INDLETVALORES']    = $intCountReg;
                }
                //Enumeración de los registros del listado
                $objWorkSheet->setCellValue('A' . $intIndiceListado, strval($intRegistro));
                
                $this->insertarValoresRegistro($objWorkSheet, $arrayParametros2);
                $this->setEstiloXFila($objWorkSheet, $arrayParametros);

                $arrayParametrosSumario['ALIGNSUMMARY']   = $arrayParametros['ALIGNSUMMARY'];
            }
            else // Segundo listado
            {
                $arrayParametrosRegistro['INDLETCATEGORIAS'] = $intCountReg + 3;
                $arrayParametrosRegistro['INDLETVALORES']    = $intCountReg + $intCountReg + 2;
                
                $arrayParametros['FIN'] = $arrayParametros['FIN2'];
                $intIndex               = intval($arrayParametros['FIN2']) - $intCountReg;
                $intIndLetrasIniGrafic  = $intIndex;
                
                $arrayParametros2['CELLDATATYPE'] = $arrayParametros['TIPOSDATOS2'];
                
                $this->insertarValoresRegistro($objWorkSheet, $arrayParametros2, $intIndex);
                $this->setEstiloXFila($objWorkSheet, $arrayParametros, $intIndex);
                
                if(isset($arrayParametros['REGISTROIND2']))
                {
                    $arrayParametros['REGISTROIND'] = $arrayParametros['REGISTROIND2'];
                    $intOffSetX = 28;
                }
                
                $arrayParametros['SUMMARY']             = $arrayParametros['SUMMARY2'];
                $arrayParametros['COLUMNAIND']          = $arrayParametros['COLUMNAIND2'];
                $arrayParametrosSumario['ALIGNSUMMARY'] = $arrayParametros['ALIGNSUMMARY2'];
            }
            
            // Alto para todas las filas del listado: 20
            $objWorkSheet->getRowDimension(strval($intIndiceListado))->setRowHeight(20);
            // Se agregan Indicadores de los registros (Rojo, Amarillo, Verde)
            if(isset($arrayParametros['REGISTROIND']))
            {
                if(!isset($intOffSetX))
                {
                    $intOffSetX = 10;
                }
                $strIndicador        = $arrayParametros['REGISTROIND'][$intRegistro - 1];
                $arrayParametrosChar = array('INDICADOR'  => $strIndicador, 
                                             'INDICELIST' => $intIndiceListado, 
                                             'INDICECOL'  => $arrayParametros['COLUMNAIND']);
                $this->agregarIndicadorRegistro($objWorkSheet, $arrayParametrosChar, $intOffSetX);
            }
            $intIndiceListado++;
            $intRegistro++;
        }
        
        $arrayParametrosSumario['INDICEINICIO']  = $intIndRangoIni;
        $arrayParametrosSumario['INDICESUMARIO'] = $intIndiceListado;
        $arrayParametrosSumario['RANGOSUMMARY']  = $arrayParametros['RANGOSUMMARY'];
        $arrayParametrosSumario['ESLISTA2']      = isset($arrayParametros['ESLISTA2']);
        
        $this->insertarSumario($objWorkSheet, $arrayParametrosSumario, $arrayParametros['SUMMARY']);

        // Se cierra el cuadro del reporte con una línea al final.
        $objWorkSheet->getStyle("$strFirstChar$intIndiceListado:$strLastChar$intIndiceListado")
                     ->applyFromArray(array('borders' => array('top' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));

        if($arrayParametrosRegistro['GRAFICO'])
        {
            $arrayLetras       = $this->getLetras();
            $strLetraCategoria = $arrayLetras[$arrayParametrosRegistro['INDLETCATEGORIAS']];
            $strLetraValor     = $arrayLetras[$arrayParametrosRegistro['INDLETVALORES']];
            $strLetraIniGrafic = $arrayLetras[$intIndLetrasIniGrafic];
            $strLetraFinGrafic = $arrayLetras[$arrayParametrosRegistro['INDLETVALORES'] + 1];
            
            $intIndiceListado--;
            
            $strIndLabelLeg = "$". ($intIndRangoIni - 1);
            $strIndRangoIni = "$$intIndRangoIni";
            $strIndRangoFin = "$$intIndiceListado";
            $intFinGrafico  = $intIndiceListado + ($intCountCol * 5) ;
            $strWorkSheet   = $arrayParametros['TITULO'];
            
            $intIndiceListado += 2;
            
            $arrayParametrosGrafico['ETIQUETAS']  = "$strWorkSheet!$$strLetraCategoria$strIndLabelLeg";
            $arrayParametrosGrafico['CATEGORIAS'] = "$strWorkSheet!$$strLetraCategoria$strIndRangoIni:$$strLetraCategoria$strIndRangoFin";
            $arrayParametrosGrafico['VALORES']    = "$strWorkSheet!$$strLetraValor$strIndRangoIni:$$strLetraValor$strIndRangoFin";
            $arrayParametrosGrafico['SIZE']       = $arrayParametrosRegistro['INDLETVALORES'] + 2;
            $arrayParametrosGrafico['TITULO']     = $arrayParametrosRegistro['TITULOGRAF'];
            $arrayParametrosGrafico['INICIO']     = "$strLetraIniGrafic$intIndiceListado"  ;
            $arrayParametrosGrafico['FIN']        = "$strLetraFinGrafic$intFinGrafico";
            
            $this->insertarGrafico($objWorkSheet, $arrayParametrosGrafico);
        }
    }
    
    /**
     * Documentación para el método 'insertarSumario'.
     * 
     * Método que inserta el registro sumario del reporte en excel.
     * 
     * @param WorkSheet $objWorkSheet    Hoja activa del documento excel.
     * @param Array     $arrayParametros['INDICEINICIO'] : Integer Indice de la primera línea de los registros del reporte
     *                  $arrayParametros['INDICESUMARIO']: Integer Indice de la línea donde se ubica el sumario.
     *                  $arrayParametros['ALIGNSUMMARY'] : Array   Tipo de alineación de cada columna en el orden de inserción.
     *                  $arrayParametros['RANGOSUMMARY'] : Array   Indice inicial y final de las columnas que forman la linea del sumario.
     * @param Array     $arraySummary    Arreglo con las columnas que sumarizan en el reporte.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function insertarSumario($objWorkSheet, $arrayParametros, $arraySummary)
    {
        $intIndiceInicio  = $arrayParametros['INDICEINICIO'];
        $intIndiceSumario = $arrayParametros['INDICESUMARIO'];
        $arrayAlignSumary = $arrayParametros['ALIGNSUMMARY'];
        $arrayRangoSumary = $arrayParametros['RANGOSUMMARY'];
        $intIndiceFin     = $intIndiceSumario - 1;
        $arrayEstilo      = $this->getEstiloSumario();
        $arrayLetras      = $this->getLetras();
        
        // Se aplica estilo a tota la fila y columnas del sumario
        // $arrayRangoSumary[0] -> Incio del rango
        // $arrayRangoSumary[1] -> Fin del rango
        for($i = $arrayRangoSumary[0]; $i < $arrayRangoSumary[1]; $i ++)
        {
            $strLetra = $arrayLetras[$i];
            $strCelda = "$strLetra$intIndiceSumario";
            
            $objWorkSheet->getStyle($strCelda)->applyFromArray($arrayEstilo);
        }

        // Lista Principal
        if(!$arrayParametros['ESLISTA2'])
        {
            $objWorkSheet->setCellValue("B$intIndiceSumario", 'TOTAL');
            $objWorkSheet->getStyle("A$intIndiceSumario")->applyFromArray($arrayEstilo);
            $objWorkSheet->getStyle("B$intIndiceSumario")->applyFromArray($arrayEstilo);
        }
        else // Lista secundaria
        {
            $strLetra = $arrayLetras[$arrayRangoSumary[0]];
            $strCelda = "$strLetra$intIndiceSumario";
            
            $objWorkSheet->setCellValue($strCelda, 'TOTAL');
        }
        
        $i = 0;
        // Se cambia el estilo a las celdas que sumarizan y se agrega su función.
        foreach($arraySummary as $strSumCell)
        {
            $objFormula = explode(';', $strSumCell);
            
            if(count($objFormula) > 1)
            {
                $strFormula = str_replace('#', $intIndiceSumario, str_replace('@', $intIndiceSumario, $objFormula[1]));
                $strSumCell = $objFormula[0];
            }
            else
            {
                $strFormula = "=SUM($strSumCell$intIndiceInicio:$strSumCell$intIndiceFin)";
            }
            
            $strCelda = "$strSumCell$intIndiceSumario";
            
            $arrayEstilo['alignment']['horizontal'] = $arrayAlignSumary[$i++];
            
            // Obtengo el formato del todo el rango superior (numérico, porcentual, texto, etc)
            $objWorkSheet->duplicateStyle($objWorkSheet->getStyle("$strSumCell$intIndiceInicio:$strSumCell$intIndiceFin"), "$strCelda:$strCelda");
            $objWorkSheet->setCellValue($strCelda, $strFormula);
            $objWorkSheet->getStyle($strCelda)->applyFromArray($arrayEstilo);
        }
    }
    
    /**
     * Documentación para el método 'insertarGrafico'.
     * 
     * Método que inserta un gráfico tipo pastel en el reporte de excel.
     * 
     * @param WorkSheet $objWorkSheet    Hoja activa del documento excel.
     * @param Array     $arrayParametros['ETIQUETAS'] : String Celda de la cabecera del rangon de valores.
     *                  $arrayParametros['CATEGORIAS']: String Rango de donde se obtienen las leyendas del gráfico
     *                  $arrayParametros['VALORES']   : String Rango de donde se toman los valores su presentación en el gráfico
     *                  $arrayParametros['SIZE']      : String Tamaño de registros que conforman el gráfico
     *                  $arrayParametros['TITULO']    : String Título del gráfico.
     *                  $arrayParametros['INICIO']    : String Celda donde inicia la presentación del gráfico.
     *                  $arrayParametros['FIN']       : String Celda donde termina la presentación del gráfico.
     * @param Array     $arraySummary    Arreglo con la primera y última columna del reporte.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function insertarGrafico($objWorkSheet, $arrayParametros)
    {
        $arrayEtiquetas  = array(new PHPExcel_Chart_DataSeriesValues('String', $arrayParametros['ETIQUETAS'],  null, 1));
        $arrayCategorias = array(new PHPExcel_Chart_DataSeriesValues('String', $arrayParametros['CATEGORIAS'], null, $arrayParametros['SIZE']));
        $arrayValores    = array(new PHPExcel_Chart_DataSeriesValues('Number', $arrayParametros['VALORES'],    null, $arrayParametros['SIZE']));
        $objSeries       = new PHPExcel_Chart_DataSeries(PHPExcel_Chart_DataSeries::TYPE_PIECHART,      // plotType
                                                         PHPExcel_Chart_DataSeries::GROUPING_CLUSTERED, // plotGrouping
                                                         range(0, count($arrayValores)-1),              // plotOrder
                                                         $arrayEtiquetas,                               // plotLabel
                                                         $arrayCategorias,                              // plotCategory
                                                         $arrayValores                                  // plotValues
                                                         );
        //	Set up a layout object for the Pie chart
        $objLayout = new PHPExcel_Chart_Layout();
        $objLayout->setShowVal(TRUE);
        $objLayout->setShowPercent(FALSE);
        
        $objPlotArea = new PHPExcel_Chart_PlotArea($objLayout, array($objSeries));
        $objLeyenda  = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, null, false);
        $objTitulo   = new PHPExcel_Chart_Title($arrayParametros['TITULO']);
        $objChart    = new PHPExcel_Chart('Chart',		// name
                                          $objTitulo,	// title
                                          $objLeyenda,	// legend
                                          $objPlotArea,	// plotArea
                                          true,			// plotVisibleOnly
                                          0,			// displayBlanksAs
                                          null,			// xAxisLabel
                                          null			// yAxisLabel		- Pie charts don't have a Y-Axis
                                          );
        
        $objChart->setTopLeftPosition($arrayParametros['INICIO']);
        $objChart->setBottomRightPosition($arrayParametros['FIN']);
        
        $objWorkSheet->addChart($objChart);
    }
    
    /**
     * Documentación para el método 'agregarIndicadoresCabecera'.
     * 
     * Método que inserta los indicadores de color(Verde, Amarillo, Rojo) en la cabecera de cada columna del reporte en excel.
     * 
     * @param WorkSheet $objWorkSheet             Hoja activa del documento excel.
     * @param Array     $arrayIndicadoresCabecera Listado string con los indicadores de color en el mismo orden del listado
     * @param Integer   $intIndiceCab             Indice de línea donde está la cabecera del reporte
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function agregarIndicadoresCabecera($objWorkSheet, $arrayIndicadoresCabecera, $intIndiceCab)
    {
        $arrayLetras = $this->getLetras();
        $i           = 2;
        
        foreach($arrayIndicadoresCabecera as $strIndicador)
        {
            $strColumna  = $arrayLetras[$i];
            $objDrawing  = new PHPExcel_Worksheet_Drawing();
            $strLogoPath = "./public/images/ind$strIndicador.png";
            
            $objDrawing->setPath($strLogoPath);
            $objDrawing->setCoordinates("$strColumna$intIndiceCab");
            $objDrawing->setOffsetY(7);
            $objDrawing->setWorksheet($objWorkSheet);
            
            $i++;
        }
    }
    
    /**
     * Documentación para el método 'agregarIndicadorRegistro'.
     * 
     * Método que inserta los indicadores de color(Verde, Amarillo, Rojo) en los registros del reporte en excel según la columna indicada.
     * 
     * @param WorkSheet $objWorkSheet        Hoja activa del documento excel.
     * @param Array     $arrayParametrosChar['INDICADOR'] : String  Valor del indicador('V', 'A', 'R') a insertar.
     *                  $arrayParametrosChar['INDICELIST']: Integer Indice de la fila donde se ubicará el indicador
     *                  $arrayParametrosChar['INDICECOL'] : String  Columna donde se ubicará el indicador.
     * @param Integer   $intOffsetX          Distancia desde el borde izquierdo
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function agregarIndicadorRegistro($objWorkSheet, $arrayParametrosChar, $intOffsetX = 10)
    {
        $strIndicador     = $arrayParametrosChar['INDICADOR'];
        $intIndiceListado = $arrayParametrosChar['INDICELIST'];
        $strLogoPath      = "./public/images/ind$strIndicador.png";
        $arrayLetras      = $this->getLetras();
        $strColumna       = $arrayLetras[$arrayParametrosChar['INDICECOL']];
        $objDrawing       = new PHPExcel_Worksheet_Drawing();
        
        $objDrawing->setPath($strLogoPath);
        $objDrawing->setCoordinates("$strColumna$intIndiceListado");
        $objDrawing->setOffsetY(6); // Distancia desde el borde superior
        $objDrawing->setOffsetX($intOffsetX);
        $objDrawing->setWorksheet($objWorkSheet);
    }

    /**
     * Documentación para el método 'setEstiloXFila'.
     * 
     * Formatea el estilo $arrayEstilo a las celdas según las coordenadas evaluadas en [$arrayLetras][$intIndice].
     * 
     * @param WorkSheet $objWorkSheet Hoja activa del documento excel.
     * @param Array     $arrayParametros['ESTILO']      : Array   Estilo de cada registro.
     *                  $arrayParametros['RIGHTALIGN']  : Array   Columnas que se alinean a la derecha.
     *                  $arrayParametros['CENTERALIGN'] : Array   Columnas que se alinean al centro.
     *                  $arrayParametros['INDICE']      : Integer Indice de la fila del registro.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function setEstiloXFila($objWorkSheet, $arrayParametros, $intLetraIni = 0)
    {
        $arrayLetras = $this->getLetras();
        
        if(!isset($arrayParametros['ESTILO']))
        {
            $arrayParametros['ESTILO'] = $this->getEstiloCabecera();
        }
        
        for($i = $intLetraIni; $i < $arrayParametros['FIN']; $i++)
        {
            $strAlinear = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
            
            foreach($arrayParametros['RIGHTALIGN'] as $strLetraAlign)
            {
                if($strLetraAlign == $arrayLetras[$i])
                {
                    $strAlinear = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                    break;
                }
            }
            
            if(isset($arrayParametros['CENTERALIGN']))
            {
                foreach($arrayParametros['CENTERALIGN'] as $strLetraAlign)
                {
                    if($strLetraAlign == $arrayLetras[$i])
                    {
                        $strAlinear = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                        break;
                    }
                }
            }
            
            if($i == 0)
            {
                $strAlinear = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
            }
            
            $arrayParametros['ESTILO']['alignment']['horizontal'] = $strAlinear;
           
            $objWorkSheet->getStyle($arrayLetras[$i] . $arrayParametros['INDICE'])->applyFromArray($arrayParametros['ESTILO']);
        }
    }

    /**
     * Documentación para el método 'setEstiloCelda'.
     * 
     * Formatea el estilo $arrayEstilo a las celdas dentro de $arrayCeldas.
     * 
     * @param WorkSheet $objWorkSheet Hoja activa del documento excel
     * @param Array     $arrayEstilo  Estilo de la celda a aplicar.
     * @param Array     $arrayCeldas  Listado de celdas que se les aplicará el estilo.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function setEstiloCelda($objWorkSheet, $arrayEstilo, $arrayCeldas)
    {
        foreach($arrayCeldas as $strCelda)
        {
            $objWorkSheet->getStyle($strCelda)->applyFromArray($arrayEstilo);
        }
    }

    /**
     * Documentación para el método 'setValorXCeldaCabecera'.
     * 
     * Setea el valor del contenido de cada celda de la cabecera del reporte desde la columna B a 'FIN según los valores dentro de $arrayValores.
     * 
     * @param WorkSheet $objWorkSheet Hoja activa del documento excel
     * @param Integer   $intIndice
     * @param Array     $arrayValores
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function setValorXCeldaCabecera($objWorkSheet, $intIndice, $arrayValores)
    {
        $i = 0;
        
        array_unshift($arrayValores, "#"); // Valor por defecto en todos los reportes. se inserta al principio del array.
        
        $arrayLetras = $this->getLetras();
        
        foreach($arrayValores as $strValor)
        {
            $objWorkSheet->setCellValue($arrayLetras[$i] . $intIndice, $strValor);
            $i++;
        }
    }

    /**
     * Documentación para el método 'insertarValoresRegistro'.
     * 
     * Establece el valor del contenido de cada celda según las coordenadas evaluadas, desde la columna B a 'FIN' y las filas según el orden y valor 
     * dentro de $arrayDatos, por defecto el tipo de valor dentro de la celda será PHPExcel_Cell_DataType::TYPE_STRING.
     * 
     * @param WorkSheet $objWorkSheet Hoja activa del documento excel.
     * @param Array     $arrayParametros['INDICE']        : Integer indice de la fila del registro a insertar.
     *                  $arrayParametros['ENTITYREGISTRO']: Objeto con los datos a insertar.
     *                  $arrayParametros['CELLDATATYPE']  : Tipo de Formato de celda a aplicar.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function insertarValoresRegistro($objWorkSheet, $arrayParametros, $intLetraIni = 1)
    {
        $intIndice         = $arrayParametros['INDICE'];
        $entityRegistro    = $arrayParametros['ENTITYREGISTRO'];
        $arrayCellDataType = $arrayParametros['CELLDATATYPE'];
        
        $i            = $intLetraIni;
        $arrayLetras  = $this->getLetras();
        $arrayValores = array_values($entityRegistro);
        $x            = 0;
        
        foreach($arrayValores as $strValor)
        {
            $strTypeStr = $arrayCellDataType[$x++];
            
            if($strTypeStr == 'FLOAT')
            {
                $strTypeStr = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                $objWorkSheet->getStyle("$arrayLetras[$i]$intIndice")->getNumberFormat()
                                                                     ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }
            else if($strTypeStr == 'PERCENTAGE')
            {
                $strTypeStr = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                $objWorkSheet->getStyle("$arrayLetras[$i]$intIndice")->getNumberFormat()
                                                                     ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
            }
            else if($strTypeStr == 'INTSTR')
            {
                if($strValor == null && strval($strValor) != '0')
                {
                    $strTypeStr = PHPExcel_Cell_DataType::TYPE_NULL;
                }
                else
                {
                    $strTypeStr = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                }
            }
            
            $objWorkSheet->setCellValueExplicit("$arrayLetras[$i]$intIndice", $strValor, $strTypeStr);
            $i++;
        }
    }
    
    /**
     * Documentación para el método 'setAnchoColumna'.
     * 
     * Setea el tamaño del ancho de la columna según el valor de cada elemento dentro de $arrayValores,
     * el seteo comienza desde la columna B hasta la R. La columna A tiene el tamaño predefinido 8.
     * 
     * @param WorkSheet $objWorkSheet Hoja activa del documento excel
     * @param Array     $arrayValores Tamaño del ancho de las columnas.
     * @param Integer   $intWidth     Tamaño del ancho de la columna 'A'
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function setAnchoColumna($objWorkSheet, $arrayValores, $intWidth)
    {
        $i           = 1;
        $arrayLetras = $this->getLetras();
        
        $objWorkSheet->getColumnDimension('A')->setWidth($intWidth);
        
        foreach($arrayValores as $intValor)
        {
            $objWorkSheet->getColumnDimension($arrayLetras[$i])->setWidth($this->ajustaMayor($intValor) + 2);
            $i++;
        }
    }

    /**
     * Documentación para el método 'setAltoFila'.
     * 
     * Setea el tamaño del alto de la fila según el valor de cada elemento dentro de $arrayValores,
     * el seteo comienza desde la primera línea y avanza en función de la cantidad de elementos dentro del arreglo.
     * 
     * @param WorkSheet $objWorkSheet
     * @param Array    $intCantidadFilas
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function setAltoFila($objWorkSheet, $intCantidadFilas)
    {
        $objWorkSheet->getRowDimension('1')->setRowHeight(36);
        
        for($i = 2; $i <= $intCantidadFilas + 1; $i++)
        {
            $objWorkSheet->getRowDimension("$i")->setRowHeight(14);
        }
        
        $objWorkSheet->getRowDimension("$i")->setRowHeight(20);
    }
   
    /**
     * Documentación para el método 'calcularMayor'.
     * 
     * Calcular el tamaño máximo de longitud de cada valor de cada una de las columnas para determinar el mayor tamaño y así establecer el ancho de 
     * las columnas del reporte de televentas.
     * 
     * @param String $strValor
     * @param int    $intMayor
     * 
     * @return int $intMayor.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function calcularMayor($strValor, $intMayor)
    {
        $intTamanio = strlen($strValor);
        
        if($intTamanio > $intMayor)
        {
            return $intTamanio;
        }
        
        return $intMayor;
    }
   
    /**
     * Documentación para el método 'ajustaMayor'.
     * 
     * Evalúa $intMayor y si es menor que 12 el valor devuelto es 12, si es mayor que 80 retorna 80, en este punto si es mayor que 40 responde el 
     * valor de $intMayor reducido en 5 y si no cumple ningún criterio anterior se devuelve el mismo valor de $intMayor a fin de ajustar el 
     * tamaño del ancho de las columnas del reporte de televentas.
     * 
     * @param int $intMayor
     * 
     * @return int $intMayor.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function ajustaMayor($intMayor)
    {
        return $intMayor < 12 ? 12 : ($intMayor > 80 ? 80 : ($intMayor > 40 ? ($intMayor-5) : $intMayor));
    }
   
    /**
     * Documentación para el método 'getFecha'.
     * 
     * @param Array $arrayFecha
     * 
     * @return Array Fecha Mes-Año.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getFecha($arrayFecha)
    {
        return array($this->getMeses()[$arrayFecha[1]-1], $arrayFecha[0]);
    }
    
    /**
     * Documentación para el método 'getMeses'.
     * 
     * @return array Meses del año.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getMeses()
    {
        return array('Enero', 'Febrero', 'Marzo',      'Abril',   'Mayo',      'Junio', 
                     'Julio', 'Agosto',  'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    }
    
    /**
     * Documentación para el método 'getLetras'.
     * 
     * @return array Listado de letras de las columnas con que trabajan los reportes.
     * 
     * @author Alejandro Domínguez Vargas <adominguez@telconet.ec>
     * @version 1.0 21-12-2015
     */
    private function getLetras()
    {
        return array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S');
    }
}
