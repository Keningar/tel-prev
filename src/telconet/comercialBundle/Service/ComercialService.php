<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\ReturnResponse;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoVisualizacionDocHist;

class ComercialService {
    
    private $emGeneral;
    private $emComercial;
    private $emFinanciero;
    private $emBiFinanciero;
    private $serviceUtil;
    private $serviceTecnico;

    /**
     * setDependencies
     *
     * Función encargada de setear los entities manager de los esquemas de base de datos
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-06-2017
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 26-03-2018
     * Se modifica función para que setee entity manager para BI_FINANCIERO
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 28-10-2019
     * Se modifica función para que setee usrComercial, passwdComercial y databaseDsn
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $objContainer - objeto contenedor
     *
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer )
    {
        $this->emComercial     = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral       = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emFinanciero    = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emBiFinanciero  = $objContainer->get('doctrine.orm.telconet_bifinanciero_entity_manager');
        $this->serviceUtil     = $objContainer->get('schema.Util');
        $this->serviceTecnico  = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->usrComercial    = $objContainer->getParameter('user_comercial');
        $this->passwdComercial = $objContainer->getParameter('passwd_comercial');
        $this->databaseDsn     = $objContainer->getParameter('database_dsn');
    }


    /**
     * getCaracteristicasPersonalEmpresaRol
     *
     * Función encargada para retornar el cargo en TELCOS del personal a consultar
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 12-06-2017
     *
     * @param array $arrayParametros [intIdPersonEmpresaRol => Id del personal a consultar en sessión
     *                                strUsrCreacion        => Usuario en sessión
     *                                strIpCreacion         => Ip del usuario en sessión ]
     *
     * @return array $arrayResultados ['strCargoPersonal' => 'Cargo del personal en session',
     *                                 'strTipoVendedor'  => 'Tipo de Vendedor del personal en session',
     *
     */
    public function getCaracteristicasPersonalEmpresaRol($arrayParametros)
    {   
        $arrayResultados  = array('strCargoPersonal' => '', 'strTipoVendedor' => '');
        $strUsrCreacion   = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                            ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion    = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                            ? $arrayParametros['strIpCreacion'] : '127.0.0.1';

        try
        {
            $intIdPersonEmpresaRol = ( isset($arrayParametros['intIdPersonEmpresaRol']) && !empty($arrayParametros['intIdPersonEmpresaRol']) )
                                     ? $arrayParametros['intIdPersonEmpresaRol'] : 0;

            if( $intIdPersonEmpresaRol > 0 )
            {
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                 ->findOneById($intIdPersonEmpresaRol);

                if( !is_object($objInfoPersonaEmpresaRol) )
                {
                    throw new \Exception("No se ha encontrado personal en session");
                }//( !is_object($objInfoPersonaEmpresaRol) )


                $objAdmiCaracteristica = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                              ->findOneBy( array('estado'                    => 'Activo',
                                                                 'descripcionCaracteristica' => 'CARGO_GRUPO_ROLES_PERSONAL') );

                if( !is_object($objAdmiCaracteristica) )
                {
                    throw new \Exception("No se ha encontrado la caracteristica por Cargo Grupo Roles Personal");
                }//( !is_object($objInfoPersonaEmpresaRol) )


                $objAdmiCaracteristicaTipoVendedor = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                          ->findOneBy( array('estado'                    => 'Activo',
                                                                             'descripcionCaracteristica' => 'TIPO_VENDEDOR') );

                if( !is_object($objAdmiCaracteristicaTipoVendedor) )
                {
                    throw new \Exception("No se ha encontrado la caracteristica por Tipo de Vendedor");
                }//( !is_object($objInfoPersonaEmpresaRol) )


                //BLOQUE QUE OBTIENE EL CARGO DEL PERSONAL EN SESSION
                $objInfoPersonaEmpresaRolCarac = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                      ->findOneBy( array('personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                         'caracteristicaId'    => $objAdmiCaracteristica,
                                                                         'estado'              => 'Activo') );

                if( is_object($objInfoPersonaEmpresaRolCarac) )
                {
                    $intIdParametroDet = $objInfoPersonaEmpresaRolCarac->getValor();

                    if( intval($intIdParametroDet) > 0 )
                    {
                        $objAdmiParametroDet = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")->findOneById($intIdParametroDet);

                        if( is_object($objAdmiParametroDet) )
                        {
                            $arrayResultados['strCargoPersonal'] = $objAdmiParametroDet->getValor3();
                            
                            if( $arrayResultados['strCargoPersonal'] == "VENDEDOR" )
                            {
                                $intIdReportaEmpresaRol = $objInfoPersonaEmpresaRol->getReportaPersonaEmpresaRolId();
                                
                                if( empty($intIdReportaEmpresaRol) )
                                {
                                    $arrayResultados['strCargoPersonal'] = '';
                                }//( $intIdReportaEmpresaRol > 0 )
                            }//( $strCargoPersonal == "VENDEDOR" )
                        }//( is_object($objAdmiParametroDet) )
                    }//( intval($intIdParametroDet) > 0 )
                }//( !is_object($objInfoPersonaEmpresaRol) )
                

                //BLOQUE QUE OBTIENE EL TIPO DE VENDEDOR O SUBGERENTE DEL PERSONAL EN SESSION
                $objInfoPersonaEmpresaRolCarac = null;
                $objInfoPersonaEmpresaRolCarac = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRolCarac")
                                                      ->findOneBy( array('personaEmpresaRolId' => $objInfoPersonaEmpresaRol,
                                                                         'caracteristicaId'    => $objAdmiCaracteristicaTipoVendedor,
                                                                         'estado'              => 'Activo') );

                if( is_object($objInfoPersonaEmpresaRolCarac) )
                {
                    $arrayResultados['strTipoVendedor'] = $objInfoPersonaEmpresaRolCarac->getValor();
                }//( !is_object($objInfoPersonaEmpresaRol) )
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar el cargo del personal en TELCOS+ - '.
                                     'intIdPersonEmpresaRol('.$intIdPersonEmpresaRol.')'); 
            }//( $intIdPersonEmpresaRol > 0 )
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getCaracteristicasPersonalEmpresaRol',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }

        return $arrayResultados;
    }

    /**
     * Documentación para la función aplicaCicloFacturacion
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 27-11-2017 - Versión inicial.
     * @param string $arrayParametros
     */
    public function aplicaCicloFacturacion($arrayParametros)
    {
        $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'] ? $arrayParametros['strPrefijoEmpresa'] : null;
        $objDQL            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getDql('CICLO_FACTURACION_EMPRESA',
                                                                                                      'FINANCIERO',
                                                                                                      'CICLO_FACTURACION',
                                                                                                      null,
                                                                                                      null,
                                                                                                      $strPrefijoEmpresa,
                                                                                                      null,
                                                                                                      null,
                                                                                                      null,
                                                                                                      strval($arrayParametros['strEmpresaCod']));
        $arrayParamtroDet  = $objDQL->getOneOrNullResult();
        $strRespuesta      = $arrayParamtroDet['valor1'] ? $arrayParamtroDet['valor1'] : 'N';
        return $strRespuesta;
    }

    /**
     * getInformacionDashboard
     *
     * Función encargada para retornar la información necesaria para la presentación del dashboard comercial
     *
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.0 06-06-2017
     * @author Edson Franco <efranco@telconet.ec>
     * @version 1.1 12-06-2017 - Se añade las consultas de 'Ordenes Eliminadas', 'Ordenes Anuladas', 'Ordenes Eliminadas' y 'Ordenes Rechazadas'
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.2 06-02-2018 - Se modifica la función para obtener clientes cancelados de años anteriores según criterios enviados por parametros
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.3 26-03-2018 - Se modifica la función para obtener información y llenar cuadros de MRC y NRC
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.4 11-09-2018 - Se modifica la función para obtener información y llenar cuadros Cumplimiento de presupuesto MRC, Nrc y Clientes
     * @author David León <mdleon@telconet.ec>
     * @version 1.5 11-11-2021 - Se modifica la función para obtener información de Internet/Datos y Business solutions.
     * @param array $arrayParametros [strPrefijoEmpresa    => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio       => Fecha de inicio de la búsqueda
     *                                strFechaFin          => Fecha final de la búsqueda
     *                                strCategoria         => Categoría de los productos a buscar
     *                                strGrupo             => Grupo de los productos a buscar
     *                                strSubgrupo          => Subgrupo de los productos a buscar
     *                                strUsrCreacion       => Usuario en sessión
     *                                strIpCreacion        => Ip del usuario en sessión
     *                                strDatabaseDsn       => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserComercial     => Usuario del esquema comercial 'DB_COMERCIAL'
     *                                strPasswordComercial => Password del esquema comercial 'DB_COMERCIAL' ]
     *
     * @return array $arrayInformacionDashboard
     *
     */
    public function getInformacionDashboard($arrayParametros)
    {
        $arrayInformacionDashboard                                 = array();
        $arrayInformacionDashboard['strDiaSemana']                 = date('D');
        $arrayInformacionDashboard['strDiaMes']                    = date('d');
        $arrayInformacionDashboard['strMesActual']                 = date('M');
        $arrayInformacionDashboard['strAnioActual']                = date('Y');
        $arrayInformacionDashboard['strTrimestre']                 = '';
        $arrayInformacionDashboard['strMesesTrimestre']            = '';
        $arrayInformacionDashboard['strMedicionCategoria']         = '';
        $arrayInformacionDashboard['intTotalVentasNoConcretadas']  = 0;
        $arrayInformacionDashboard['floatVentasNoConcretadas']     = 0;
        $arrayInformacionDashboard['intTotalVentasCancelados']     = 0;
        $arrayInformacionDashboard['floatVentasCancelados']        = 0;
        $arrayInformacionDashboard['intTotalVentasAnuladas']       = 0;
        $arrayInformacionDashboard['floatVentasAnuladas']          = 0;
        $arrayInformacionDashboard['intTotalVentasRechazadas']     = 0;
        $arrayInformacionDashboard['floatVentasRechazadas']        = 0;
        $arrayInformacionDashboard['intTotalVentasEliminadas']     = 0;
        $arrayInformacionDashboard['floatVentasEliminadas']        = 0;
        $arrayInformacionDashboard['floatFacturacionUnica']        = 0;
        $arrayInformacionDashboard['floatFacturacionMensual']      = 0;
        $arrayInformacionDashboard['floatFacturacionNoMensual']    = 0;
        $arrayInformacionDashboard['floatBuenasVentas']            = 0;
        $arrayInformacionDashboard['intTotalBuenasVentas']         = 0;
        $arrayInformacionDashboard['floatMalasVentas']             = 0;
        $arrayInformacionDashboard['arrayDataComercial']           = null;
        $arrayInformacionDashboard['arrayDataDrilldown']           = array();
        //
        $arrayMeses                                                = array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio',
                                                                           'Agosto','Septiembre','Octubre','Noviembre','Diciembre');
        
        $strUsrCreacion            = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                     ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion             = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                     ? $arrayParametros['strIpCreacion'] : '127.0.0.1';

        try
        {
            $strPrefijoEmpresa = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                 ? $arrayParametros['strPrefijoEmpresa'] : null;
            $strFechaInicio    = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                                 ? $arrayParametros['strFechaInicio'] : null;
            $strFechaFin       = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']) )
                                 ? $arrayParametros['strFechaFin'] : null;
            $strCategoria      = ( isset($arrayParametros['strCategoria']) && !empty($arrayParametros['strCategoria']) )
                                 ? $arrayParametros['strCategoria'] : null;
            $strGrupo          = ( isset($arrayParametros['strGrupo']) && !empty($arrayParametros['strGrupo']) )
                                 ? $arrayParametros['strGrupo'] : null;
            $strSubgrupo       = ( isset($arrayParametros['strSubgrupo']) && !empty($arrayParametros['strSubgrupo']) )
                                 ? $arrayParametros['strSubgrupo'] : null;
            $strTipoPersonal   = ( isset($arrayParametros['strTipoPersonal']) && !empty($arrayParametros['strTipoPersonal']) )
                                 ? $arrayParametros['strTipoPersonal'] : null;

            if( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strFechaFin) )
            {
                //SE CONSULTA LA INFORMACION COMERCIAL DE ORDENES ACTIVAS, ORDENES NO CONCRETADAS, CLIENTES CANCELADOS, VENTAS Y FACTURACION DEL MES
                $cursorInformacionComercial = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                   ->getInformacionDashboard($arrayParametros);

                if( !empty($cursorInformacionComercial) )
                {
                    $arrayDataComercial = array();
                    
                    while( ($arrayResultadoCursor = oci_fetch_array($cursorInformacionComercial, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    {
                        $arrayInformacionDashboard['strDiaSemana']                = ( isset($arrayResultadoCursor['DIA_SEMANA']) 
                                                                                      && !empty($arrayResultadoCursor['DIA_SEMANA']) )
                                                                                    ? $arrayResultadoCursor['DIA_SEMANA'] : date('D');
                        $arrayInformacionDashboard['strDiaMes']                   = ( isset($arrayResultadoCursor['DIA_MES']) 
                                                                                      && !empty($arrayResultadoCursor['DIA_MES']) )
                                                                                    ? $arrayResultadoCursor['DIA_MES'] : date('d');
                        $arrayInformacionDashboard['strMesActual']                = ( isset($arrayResultadoCursor['MES']) 
                                                                                      && !empty($arrayResultadoCursor['MES']) )
                                                                                    ? $arrayResultadoCursor['MES'] : date('M');
                        $arrayInformacionDashboard['strAnioActual']               = ( isset($arrayResultadoCursor['ANIO']) 
                                                                                      && !empty($arrayResultadoCursor['ANIO']) )
                                                                                    ? $arrayResultadoCursor['ANIO'] : date('Y');
                        $arrayInformacionDashboard['strTrimestre']                = ( isset($arrayResultadoCursor['TRIMESTRE']) 
                                                                                      && !empty($arrayResultadoCursor['TRIMESTRE']) )
                                                                                    ? $arrayResultadoCursor['TRIMESTRE'] : '';
                        $arrayInformacionDashboard['strMesesTrimestre']           = ( isset($arrayResultadoCursor['MESES_TRIMESTRE']) 
                                                                                      && !empty($arrayResultadoCursor['MESES_TRIMESTRE']) )
                                                                                    ? $arrayResultadoCursor['MESES_TRIMESTRE'] : '';
                        $arrayInformacionDashboard['strMedicionCategoria']        = ( isset($arrayResultadoCursor['MEDICION_CATEGORIA']) 
                                                                                      && !empty($arrayResultadoCursor['MEDICION_CATEGORIA']) )
                                                                                    ? $arrayResultadoCursor['MEDICION_CATEGORIA'] : '';


                        //SE RECORRE LA INFORMACION QUE SERÁ PINTADA EN EL GRAFICO DE PASTEL
                        $arrayItemDataComercial = array();
                        
                        //Se le coloca 'drilldown', 'name' y 'y' porque es el formato usado por la libreria 'HIGHCHARTS JS'
                        $arrayItemDataComercial['drilldown'] = null;
                        $arrayItemDataComercial['name']      = '';
                        $arrayItemDataComercial['y']         = 0;
                        
                        if( empty($strCategoria) && empty($strGrupo) && empty($strSubgrupo) )
                        {
                            $arrayItemDataComercial['name'] = ( isset($arrayResultadoCursor['DESCRIPCION_CARACTERISTICA']) 
                                                                && !empty($arrayResultadoCursor['DESCRIPCION_CARACTERISTICA'])
                                                               ) ? ucwords(strtolower($arrayResultadoCursor['DESCRIPCION_CARACTERISTICA'])) 
                                                                    : null;
                        }//( empty($strCategoria) && empty($strCategoria) && empty($strCategoria) )
                        elseif( !empty($strCategoria) )
                        {
                            $arrayInformacionDashboard['strTituloGrafico'] = "";
                            
                            if( $arrayInformacionDashboard['strMedicionCategoria'] == "Trimestre" )
                            {
                                $arrayInformacionDashboard['strTituloGrafico'] = $arrayInformacionDashboard['strTrimestre']."° Trimestre ( ".
                                                                                 $arrayInformacionDashboard['strMesesTrimestre']." / ".
                                                                                 $arrayInformacionDashboard['strAnioActual']." )";
                                $arrayInformacionDashboard['strTituloGrafico'] = ucwords(strtolower($arrayInformacionDashboard['strTituloGrafico']));
                                $strGrupo = (isset($arrayResultadoCursor['GRUPO']) && !empty($arrayResultadoCursor['GRUPO']))
                                                ? $arrayResultadoCursor['GRUPO'] : 'SIN GRUPO';
                                $strGrupo = trim(str_replace('(Trimestre)','',$strGrupo));
                            }
                            elseif( $arrayInformacionDashboard['strMedicionCategoria'] == "Mensual" )
                            {
                                $arrayInformacionDashboard['strTituloGrafico'] = "Mensual ( ".$arrayInformacionDashboard['strMesActual']." ".
                                                                                 $arrayInformacionDashboard['strAnioActual']." )";
                                $arrayInformacionDashboard['strTituloGrafico'] = ucwords(strtolower($arrayInformacionDashboard['strTituloGrafico']));
                                $strGrupo = (isset($arrayResultadoCursor['GRUPO']) && !empty($arrayResultadoCursor['GRUPO']))
                                                ? $arrayResultadoCursor['GRUPO'] : 'SIN GRUPO';
                                $strGrupo = trim(str_replace('(Mensual)','',$strGrupo));                                
                            }
                            
                            $arrayItemDataComercial['name']      = (isset($arrayResultadoCursor['GRUPO']) && !empty($arrayResultadoCursor['GRUPO']))
                                                                   ? $arrayResultadoCursor['GRUPO'] : 'SIN GRUPO';
                            $arrayItemDataComercial['drilldown'] = $arrayItemDataComercial['name'];


                            //SE CONSULTA LOS SUBGRUPOS ASOCIADOS A LOS GRUPOS DE LAS CATEGORIAS
                            $arrayParametrosSubgrupos                 = $arrayParametros;
                            $arrayParametrosSubgrupos['strCategoria'] = null;
                            $arrayParametrosSubgrupos['strGrupo']     = $strGrupo;
                            $cursorInformacionSubgrupos               = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                             ->getInformacionDashboard($arrayParametrosSubgrupos);

                            if( !empty($cursorInformacionSubgrupos) )
                            {
                                $arrayDataSubgrupos                           = array();
                                $arrayDataSubgrupos['id']                     = $arrayItemDataComercial['name'];//NOMBRE DEL GRUPO
                                $arrayDataSubgrupos['name']                   = $arrayItemDataComercial['name'];//NOMBRE DEL GRUPO
                                $arrayDataSubgrupos['arrayItemDataDrilldown'] = array();
                                
                                while(($arraySubgruposCursor = oci_fetch_array($cursorInformacionSubgrupos, OCI_ASSOC + OCI_RETURN_NULLS)) != false)
                                {
                                    $arrayItemDataSubgrupos                  = array();
                                    $arrayItemDataSubgrupos['strSubgrupo']   = ( isset($arraySubgruposCursor['SUBGRUPO']) 
                                                                                 && !empty($arraySubgruposCursor['SUBGRUPO'])
                                                                               ) ? $arraySubgruposCursor['SUBGRUPO'] : 'SIN SUBGRUPO';
                                    $arrayItemDataSubgrupos['floatSubgrupo'] = ( isset($arraySubgruposCursor['VENTA_PARCIALES']) 
                                                                                 && !empty($arraySubgruposCursor['VENTA_PARCIALES'])
                                                                               ) ? $arraySubgruposCursor['VENTA_PARCIALES'] : 0;
                                    $arrayDataSubgrupos['arrayItemDataDrilldown'][] = $arrayItemDataSubgrupos;
                                }//while(($arraySubgruposCursor = oci_fetch_array($cursorInformacionSubgrupos, OCI_ASSOC + OCI_RETURN_NULLS))...

                                if( isset($arrayDataSubgrupos['arrayItemDataDrilldown']) && !empty($arrayDataSubgrupos['arrayItemDataDrilldown']) )
                                {
                                    $arrayInformacionDashboard['arrayDataDrilldown'][] = $arrayDataSubgrupos;
                                }//( isset($arrayDataSubgrupos['arrayItemDataDrilldown']) && !empty($arrayDataSubgrupos['arrayItemDataDrilldown']) )
                            }//( !empty($cursorInformacionSubgrupos) )
                        }//elseif( !empty($strCategoria) )

                        //Se le coloca 'y' porque es el formato usado por la libreria 'HIGHCHARTS JS'
                        $arrayItemDataComercial['y'] = ( isset($arrayResultadoCursor['VENTA_PARCIALES'])
                                                                  && !empty($arrayResultadoCursor['VENTA_PARCIALES']) )
                                                                ? $arrayResultadoCursor['VENTA_PARCIALES'] : 0;
                        
                        $arrayDataComercial[] = $arrayItemDataComercial;
                    }//while( ($arrayResultadoCursor = oci_fetch_array($cursorInformacionComercial, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    
                    if( !empty($arrayDataComercial) )
                    {
                        $arrayInformacionDashboard['arrayDataComercial'] = $arrayDataComercial;
                    }//( !empty($arrayDataComercial) )
                }//( !empty($cursorInformacionComercial) )


                //SE OBTIENE LA CANTIDAD Y VALOR DE VENTAS DE LAS BUENAS VENTAS
                $arrayParametrosBuenasVentas                   = $arrayParametros;
                $arrayParametrosBuenasVentas['strTipoOrdenes'] = 'ORDENES_ACTIVAS';
                $arrayResultadosBuenasVentas                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                       ->getInformacionVentas($arrayParametrosBuenasVentas);
                
                $arrayInformacionDashboard['intTotalBuenasVentas'] = ( isset($arrayResultadosBuenasVentas['intTotalVentas']) 
                                                                       && !empty($arrayResultadosBuenasVentas['intTotalVentas']) )
                                                                     ? $arrayResultadosBuenasVentas['intTotalVentas'] : 0;
                $arrayInformacionDashboard['floatBuenasVentas']    = ( isset($arrayResultadosBuenasVentas['floatValorVentas']) 
                                                                       && !empty($arrayResultadosBuenasVentas['floatValorVentas']) )
                                                                     ? $arrayResultadosBuenasVentas['floatValorVentas'] : 0;
                $arrayInformacionDashboard['floatBuenasVentas']    = number_format($arrayInformacionDashboard['floatBuenasVentas'], 2, '.', ',');



                //SE OBTIENE LA CANTIDAD Y VALOR DE VENTAS DE LAS ORDENES NO CONCRETADAS
                $arrayParametrosVentasNoConcretadas                   = $arrayParametros;
                $arrayParametrosVentasNoConcretadas['strTipoOrdenes'] = 'VENTAS_NO_CONCRETADAS';
                $arrayResultadosVentasNoConcretadas                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                             ->getInformacionVentas($arrayParametrosVentasNoConcretadas);
                
                $arrayInformacionDashboard['intTotalVentasNoConcretadas'] = ( isset($arrayResultadosVentasNoConcretadas['intTotalVentas'])
                                                                              && !empty($arrayResultadosVentasNoConcretadas['intTotalVentas']) )
                                                                            ? $arrayResultadosVentasNoConcretadas['intTotalVentas'] : 0;
                $arrayInformacionDashboard['floatVentasNoConcretadas']    = ( isset($arrayResultadosVentasNoConcretadas['floatValorVentas'])
                                                                              && !empty($arrayResultadosVentasNoConcretadas['floatValorVentas']) )
                                                                            ? number_format($arrayResultadosVentasNoConcretadas['floatValorVentas'],
                                                                                            2, '.', ',') : 0;


                //SE OBTIENE LA CANTIDAD Y VALOR DE VENTAS DE LAS MALAS VENTAS
                $arrayParametrosMalasVentas                   = $arrayParametros;
                $arrayParametrosMalasVentas['strTipoOrdenes'] = 'CLIENTES_CANCELADOS';
                $arrayResultadosMalasVentas                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                     ->getInformacionVentas($arrayParametrosMalasVentas);
                
                $arrayInformacionDashboard['intTotalMalasVentas'] = ( isset($arrayResultadosMalasVentas['intTotalVentas']) 
                                                                      && !empty($arrayResultadosMalasVentas['intTotalVentas']) )
                                                                     ? $arrayResultadosMalasVentas['intTotalVentas'] : 0;
                $arrayInformacionDashboard['floatMalasVentas']    = ( isset($arrayResultadosMalasVentas['floatValorVentas']) 
                                                                      && !empty($arrayResultadosMalasVentas['floatValorVentas']) )
                                                                     ? $arrayResultadosMalasVentas['floatValorVentas'] : 0;
                $arrayInformacionDashboard['floatMalasVentas']    = number_format($arrayInformacionDashboard['floatMalasVentas'], 2, '.', ',');



                //SE OBTIENE EL VALOR DE FACTURACION UNICA
                $arrayParametrosFacturacionUnica                   = $arrayParametros;
                $arrayParametrosFacturacionUnica['strTipoOrdenes'] = 'VENTAS_ACTIVAS';
                $arrayParametrosFacturacionUnica['strFrecuencia']  = 'UNICA';
                $arrayResultadosFacturacionUnica                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->getInformacionVentas($arrayParametrosFacturacionUnica);

                $arrayInformacionDashboard['floatFacturacionUnica'] = ( isset($arrayResultadosFacturacionUnica['floatValorVentas'])
                                                                        && !empty($arrayResultadosFacturacionUnica['floatValorVentas']) )
                                                                      ? number_format($arrayResultadosFacturacionUnica['floatValorVentas'],
                                                                                      2, '.', ',') : 0;


                //SE OBTIENE EL VALOR DE FACTURACION MENSUAL
                $arrayParametrosFacturacionMensual                   = $arrayParametros;
                $arrayParametrosFacturacionMensual['strTipoOrdenes'] = 'VENTAS_ACTIVAS';
                $arrayParametrosFacturacionMensual['strFrecuencia']  = 'MENSUAL';
                $arrayResultadosFacturacionMensual                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->getInformacionVentas($arrayParametrosFacturacionMensual);

                $arrayInformacionDashboard['floatFacturacionMensual'] = ( isset($arrayResultadosFacturacionMensual['floatValorVentas'])
                                                                          && !empty($arrayResultadosFacturacionMensual['floatValorVentas']) )
                                                                        ? number_format($arrayResultadosFacturacionMensual['floatValorVentas'],
                                                                                        2, '.', ',') : 0;


                //SE OBTIENE EL VALOR DE FACTURACION NO MENSUAL
                $arrayParametrosFacturacionNoMensual                   = $arrayParametros;
                $arrayParametrosFacturacionNoMensual['strTipoOrdenes'] = 'VENTAS_ACTIVAS';
                $arrayParametrosFacturacionNoMensual['strFrecuencia']  = 'NO_MENSUAL';
                $arrayResultadosFacturacionNoMensual                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                          ->getInformacionVentas($arrayParametrosFacturacionNoMensual);

                $arrayInformacionDashboard['floatFacturacionNoMensual'] = ( isset($arrayResultadosFacturacionNoMensual['floatValorVentas'])
                                                                            && !empty($arrayResultadosFacturacionNoMensual['floatValorVentas']) )
                                                                          ? number_format($arrayResultadosFacturacionNoMensual['floatValorVentas'],
                                                                                          2, '.', ',') : 0;
                //SE CONSULTA LA INFORMACIÓN DE LOS PRODUCTOS DESTACADOS
                $arrayInformacionDashboard['arrayProductosDestacados'] = array();
                
                $arrayParametrosProductos              = $arrayParametros;
                $arrayParametrosProductos['intRownum'] = 3;
                $cursorProductosDestacados             = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                              ->getListadoProductosDestacados($arrayParametrosProductos);
                if( !empty($cursorProductosDestacados) )
                {
                    $arrayProductosDestacados = array();
                    
                    while( ($arrayResultadoCursor = oci_fetch_array($cursorProductosDestacados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    {
                        $arrayItemProductoDestacado                = array();
                        $arrayItemProductoDestacado['strProducto'] = ( isset($arrayResultadoCursor['DESCRIPCION_PRODUCTO'])
                                                                       && !empty($arrayResultadoCursor['DESCRIPCION_PRODUCTO']) )
                                                                     ? ucwords(strtolower($arrayResultadoCursor['DESCRIPCION_PRODUCTO'])) : '';
                        $arrayItemProductoDestacado['floatVenta']  = ( isset($arrayResultadoCursor['VALOR_VENTA'])
                                                                       && !empty($arrayResultadoCursor['VALOR_VENTA']) )
                                                                     ? number_format($arrayResultadoCursor['VALOR_VENTA'], 2, '.', ',') : 0;
                        
                        $arrayProductosDestacados[] = $arrayItemProductoDestacado;
                    }//while( ($arrayResultadoCursor = oci_fetch_array($cursorProductosDestacados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    
                    if( !empty($arrayProductosDestacados) )
                    {
                        $arrayInformacionDashboard['arrayProductosDestacados'] = $arrayProductosDestacados;
                    }//( !empty($arrayProductosDestacados) )
                }//( !empty($cursorProductosDestacados) )


                //SE CONSULTA LA INFORMACIÓN DE LOS VENDEDORES DESTACADOS
                $arrayInformacionDashboard['arrayVendedoresDestacados'] = array();
                
                $arrayParametrosVendedores              = $arrayParametros;
                $arrayParametrosVendedores['intRownum'] = 3;
                $cursorVendedoresDestacados             = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                               ->getListadoVendedoresDestacados($arrayParametrosVendedores);
                if( !empty($cursorVendedoresDestacados) )
                {
                    $arrayVendedoresDestacados = array();
                    
                    while( ($arrayResultadoCursor = oci_fetch_array($cursorVendedoresDestacados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    {
                        $arrayItemVendedorDestacado                = array();
                        $arrayItemVendedorDestacado['strVendedor'] = ( isset($arrayResultadoCursor['VENDEDOR'])
                                                                       && !empty($arrayResultadoCursor['VENDEDOR']) )
                                                                     ? ucwords(strtolower($arrayResultadoCursor['VENDEDOR'])) : '';
                        $arrayItemVendedorDestacado['floatVenta']  = ( isset($arrayResultadoCursor['VALOR_VENTA'])
                                                                       && !empty($arrayResultadoCursor['VALOR_VENTA']) )
                                                                     ? number_format($arrayResultadoCursor['VALOR_VENTA'], 2, '.', ',') : 0;
                        
                        $arrayVendedoresDestacados[] = $arrayItemVendedorDestacado;
                    }//while( ($arrayResultadoCursor = oci_fetch_array($cursorVendedoresDestacados, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    
                    if( !empty($arrayVendedoresDestacados) )
                    {
                        $arrayInformacionDashboard['arrayVendedoresDestacados'] = $arrayVendedoresDestacados;
                    }//( !empty($arrayVendedoresDestacados) )
                }//( !empty($cursorVendedoresDestacados) )

                //SE CONSULTA LA INFORMACIÓN DE FACTURACION MRC Y NRC
                $arrayInformacionDashboard['arrayCarteraAsesor']   = array();
                $arrayParametrosCarteraAsesores                    = $arrayParametros;
                $arrayParametrosCarteraAsesores['strTipoPersonal'] = $strTipoPersonal;
                $arrayParametrosCarteraAsesores['intRownum']       = 3;
                $arrayParametrosCarteraAsesores['strTipoConsulta'] = 'TOTALIZADO';
                $cursorCarteraAsesoresMrc                          = $this->emFinanciero
                                                                          ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                          ->getDetalleFacturacionAsesor($arrayParametrosCarteraAsesores);
                $arrayCarteraAsesores = array();
                
                if( !empty($cursorCarteraAsesoresMrc) )
                {
                    $arrayItemAsesorMrc   = array();
                    //
                    $arrayItemAsesorMrc['strMes'] = "";
                    $arrayItemAsesorMrc['strAnio'] = "";
                    $arrayItemAsesorMrc['intClientesMrc']= "0";
                    $arrayItemAsesorMrc['floatFacMrc'] = "0.00";
                    $arrayItemAsesorMrc['floatFacMrcID'] = "0.00";
                    $arrayItemAsesorMrc['floatFacMrcBS'] = "0.00";
                    $arrayItemAsesorMrc['floatNcMrc'] = "0.00";
                    $arrayItemAsesorMrc['intClientesNrc'] = "0";
                    $arrayItemAsesorMrc['floatFacNrc'] = "0.00";
                    $arrayItemAsesorMrc['floatNcMrcID'] = "0.00";
                    $arrayItemAsesorMrc['floatNcMrcBS'] = "0.00";
                    $arrayItemAsesorMrc['floatNcNrc'] = "0.00";
                    //
                    while( ($arrayResultadoCursorMrc = oci_fetch_array($cursorCarteraAsesoresMrc, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    {
                        $arrayItemAsesorMrc['strMes']          = ( isset($arrayResultadoCursorMrc['MES']) && !empty($arrayResultadoCursorMrc['MES']) )
                                                              ? ucwords(strtolower($arrayMeses[$arrayResultadoCursorMrc['MES']-1])) : '';
                        $arrayItemAsesorMrc['strAnio']         = ( isset($arrayResultadoCursorMrc['ANIO']) 
                                                                   && !empty($arrayResultadoCursorMrc['ANIO']) )
                                                              ? ucwords(strtolower($arrayResultadoCursorMrc['ANIO'])) : '';
                        $arrayItemAsesorMrc['intClientesMrc']  = ( isset($arrayResultadoCursorMrc['CLIENTES_MRC'])
                                                               && !empty($arrayResultadoCursorMrc['CLIENTES_MRC']) )
                                                             ? $arrayResultadoCursorMrc['CLIENTES_MRC'] : 0;
                        $arrayItemAsesorMrc['floatFacMrc']     = ( isset($arrayResultadoCursorMrc['FAC_MRC']) 
                                                               && !empty($arrayResultadoCursorMrc['FAC_MRC']) )
                                                             ? number_format($arrayResultadoCursorMrc['FAC_MRC'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['floatFacMrcID']     = ( isset($arrayResultadoCursorMrc['FAC_MRCID']) 
                                                               && !empty($arrayResultadoCursorMrc['FAC_MRCID']) )
                                                             ? number_format($arrayResultadoCursorMrc['FAC_MRCID'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['floatFacMrcBS']     = ( isset($arrayResultadoCursorMrc['FAC_MRCBS']) 
                                                               && !empty($arrayResultadoCursorMrc['FAC_MRCBS']) )
                                                             ? number_format($arrayResultadoCursorMrc['FAC_MRCBS'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['floatNcMrc']      = ( isset($arrayResultadoCursorMrc['NC_MRC'])
                                                               && !empty($arrayResultadoCursorMrc['NC_MRC']) )
                                                             ? number_format($arrayResultadoCursorMrc['NC_MRC'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['floatNcMrcID']      = ( isset($arrayResultadoCursorMrc['NC_MRCID'])
                                                               && !empty($arrayResultadoCursorMrc['NC_MRCID']) )
                                                             ? number_format($arrayResultadoCursorMrc['NC_MRCID'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['floatNcMrcBS']      = ( isset($arrayResultadoCursorMrc['NC_MRCBS'])
                                                               && !empty($arrayResultadoCursorMrc['NC_MRCBS']) )
                                                             ? number_format($arrayResultadoCursorMrc['NC_MRCBS'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['intClientesNrc']  = ( isset($arrayResultadoCursorMrc['CLIENTES_NRC'])
                                                               && !empty($arrayResultadoCursorMrc['CLIENTES_NRC']) )
                                                             ? $arrayResultadoCursorMrc['CLIENTES_NRC'] : 0;
                        $arrayItemAsesorMrc['floatFacNrc']     = ( isset($arrayResultadoCursorMrc['FAC_NRC'])
                                                               && !empty($arrayResultadoCursorMrc['FAC_NRC']) )
                                                             ? number_format($arrayResultadoCursorMrc['FAC_NRC'], 2, '.', ',') : 0;
                        $arrayItemAsesorMrc['floatNcNrc']      = ( isset($arrayResultadoCursorMrc['NC_NRC'])
                                                               && !empty($arrayResultadoCursorMrc['NC_NRC']) )
                                                             ? number_format($arrayResultadoCursorMrc['NC_NRC'], 2, '.', ',') : 0;
                    }//while( ($arrayResultadoCursor = oci_fetch_array($cursorCarteraAsesores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                }//( !empty($arrayCarteraAsesores) )
                //
                $arrayCarteraAsesores[]  = $arrayItemAsesorMrc;
                if( !empty($arrayCarteraAsesores) )
                {
                    $arrayInformacionDashboard['arrayCarteraAsesor'] = $arrayCarteraAsesores;
                }//( !empty($arrayCarteraAsesores) )
                
                //SE CONSULTA LA INFORMACIÓN DE FACTURACION MRC Y NRC TRIMESTRAL
                $arrayInformacionDashboard['arrayCarteraAsesorTrimes']   = array();
                $arrayParametrosCarteraAsesores                    = $arrayParametros;
                $arrayParametrosCarteraAsesores['strTipoPersonal'] = $strTipoPersonal;
                $arrayParametrosCarteraAsesores['intRownum']       = 3;
                $arrayParametrosCarteraAsesores['strTipoConsulta'] = 'TOTALIZADO_TRIMESTRAL';
                $objCursorCarteraAsesoresMrcTri                    = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                          ->getDetalleFacturacionAsesor($arrayParametrosCarteraAsesores);
                $arrayCarteraAsesoresTrim = array();
                
                if( !empty($objCursorCarteraAsesoresMrcTri) )
                {
                    $arrayItemAsesorNrcTri   = array();
                    
                    $floatFactNrc                         =0.00;
                    $floatFactNcNrc                       =0.00;
                    $floatNrcTri                          =0.00;
                    $intClientesTri                       =0;
                    $arrayItemAsesorNrcTri['floatFacNrc'] = "0.00";
                    $arrayItemAsesorNrcTri['floatNcNrc']  = "0.00";
                    $arrayItemAsesorNrcTri['intClientesNrc'] = "0";
                    while( ($arrayResultadoCursorMrc = oci_fetch_array($objCursorCarteraAsesoresMrcTri, OCI_ASSOC + OCI_RETURN_NULLS)))
                    {
                        $floatFactNrc           += (isset($arrayResultadoCursorMrc['FAC_NRC']) && !empty($arrayResultadoCursorMrc['FAC_NRC']) )
                                                 ? ($arrayResultadoCursorMrc['FAC_NRC']) : 0;
                        $floatFactNcNrc         +=(isset($arrayResultadoCursorMrc['NC_NRC']) && !empty($arrayResultadoCursorMrc['NC_NRC']) )
                                                 ? ($arrayResultadoCursorMrc['NC_NRC']) : 0;
                        $intClientesTri         +=(isset($arrayResultadoCursorMrc['CLIENTES_NRC'])&& !empty($arrayResultadoCursorMrc['CLIENTES_NRC']))
                                                 ? $arrayResultadoCursorMrc['CLIENTES_NRC'] : 0;
                        $floatNrcTri = $floatFactNrc+$floatNrcTri;
                    }
                    $arrayItemAsesorNrcTri['floatFacNrcTri']    =   number_format($floatFactNrc, 2, '.', ',');
                    $arrayItemAsesorNrcTri['floatNcNrcTri']     =   number_format($floatFactNcNrc, 2, '.', ',');
                    $arrayItemAsesorNrcTri['intClientesNrcTri'] =   $intClientesTri;
                }
                $arrayCarteraAsesoresTrim[]  = $arrayItemAsesorNrcTri;
                if( !empty($arrayCarteraAsesoresTrim) )
                {
                    $arrayInformacionDashboard['arrayCarteraAsesorTrimes'] = $arrayCarteraAsesoresTrim;
                }

                //SE CONSULTA LA INFORMACIÓN DE FACTURACION MRC Y NRC DE UN MES ANTERIOS PARA PODER SABER LOS CLIENTES POR FACTURAR Y NUEVOS
                $arrayParametrosCltNuevos                    = $arrayParametros;
                $arrayParametrosCltNuevos['strTipo']         = 'MRC';
                $arrayParametrosCltNuevos['strTipoConsulta'] = 'AGRUPADO';
                $arrayClientesNuevos                         = $this->getComparacionFacturacionAsesor($arrayParametrosCltNuevos);
                $arrayParametrosFact                         = $arrayParametros;                         
                $arrayParametrosFact['strTipo']              = 'MRC';
                $arrayParametrosFact['strTipoConsulta']      = 'POR_FACTURAR';                      
                $arrayClientesFact                           = $this->getComparacionFacturacionAsesor($arrayParametrosFact);

                $arrayClientesCancel                         = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                                ->getClientesCancelados($arrayParametros);

                $arrayInformacionDashboard['clientesNuevos'] = array();               
                if( !empty($arrayClientesNuevos) )
                {                    
                    $arrayInformacionDashboard['clientesNuevos']   = $arrayClientesNuevos;
                }//( !empty($arrayClientesNuevos) )
                
                $arrayInformacionDashboard['clientesCancel']   = array();
                if( !empty($arrayClientesCancel) )
                {                    
                    $arrayInformacionDashboard['clientesCancel']   = $arrayClientesCancel;
                }//( !empty($arrayClientesCancel) )
                
                $arrayInformacionDashboard['clientesFact']   = array();
                if( !empty($arrayClientesFact) )
                {                    
                    $arrayInformacionDashboard['clientesFact']=$arrayClientesFact;
                }//( !empty($arrayClientesFact) )
                
                $arrayParametrosResultadoVentasMRC                    = $arrayParametros;
                $arrayParametrosResultadoVentasMRC['strTipo']         = 'MRC';
                $arrayParametrosResultadoVentasMRC['strTipoConsulta'] = 'CUMPLIMIENTO_MRC';
                
                $arrayFacturacionMRC   = $this->getDetalleFacturacionAsesor($arrayParametrosResultadoVentasMRC);
                
                if( !empty($arrayFacturacionMRC) )
                {
                    $floatAcumFac       = 0;
                    $floatAcumFacID     = 0;
                    $floatAcumFacBS     = 0;
                    $floatAcumMet       = 0;
                    $floatAcumMetID     = 0;
                    $floatAcumMetBS     = 0;
                    $floatAcumBas       = 0;
                    $floatAcumBasID     = 0;
                    $floatAcumBasBS     = 0;
                    $floatTotalPor      = 0;                    
                    $floatPresupuesto   = 0;

                    foreach( $arrayFacturacionMRC as $arrayItemCumplimiento )//vendedores
                    {
                        $floatAcumFac += floatval($arrayItemCumplimiento['FACTURACION']);
                        $floatAcumBas += floatval($arrayItemCumplimiento['BASE']);
                        $floatAcumMet += floatval($arrayItemCumplimiento['META']); 
                        $floatAcumFacID += floatval($arrayItemCumplimiento['FACTURACIONID']);
                        $floatAcumFacBS += floatval($arrayItemCumplimiento['FACTURACIONBS']);
                        $floatAcumBasID += floatval($arrayItemCumplimiento['BASEID']);
                        $floatAcumMetID += floatval($arrayItemCumplimiento['METAID']);
                        $floatAcumBasBS += floatval($arrayItemCumplimiento['BASEBS']);
                        $floatAcumMetBS += floatval($arrayItemCumplimiento['METABS']);
                    }
                    $floatTotalPor      = ((floatval($floatAcumFac)-floatval($floatAcumBas))/floatval($floatAcumMet))*100;                   
                    $floatPresupuesto   = $floatAcumBas+$floatAcumMet;
                    $floatTotalPorID    = ((floatval($floatAcumFacID)-floatval($floatAcumBasID))/floatval($floatAcumMetID))*100;                   
                    $floatPresupuestoID = $floatAcumBasID+$floatAcumMetID;
                    $floatTotalPorBS    = ((floatval($floatAcumFacBS)-floatval($floatAcumBasBS))/floatval($floatAcumMetBS))*100;                   
                    $floatPresupuestoBS = $floatAcumBasBS+$floatAcumMetBS;
                }
                $arrayInformacionDashboard['totalPorcentajeMrc']  = number_format(0, 2, '.', ',');
                $arrayInformacionDashboard['totalPresupuestoMrc'] = number_format(0, 2, '.', ',');
                
                $arrayInformacionDashboard['totalPorcentajeMrcID']  = number_format(0, 2, '.', ',');
                $arrayInformacionDashboard['totalPresupuestoMrcID'] = number_format(0, 2, '.', ',');
                
                $arrayInformacionDashboard['totalPorcentajeMrcBS']  = number_format(0, 2, '.', ',');
                $arrayInformacionDashboard['totalPresupuestoMrcBS'] = number_format(0, 2, '.', ',');
                if( !empty($floatTotalPor) && !empty($floatPresupuesto) )
                {
                    $arrayInformacionDashboard['totalPorcentajeMrc']  = number_format($floatTotalPor, 2, '.', ',');
                    $arrayInformacionDashboard['totalPresupuestoMrc'] = number_format($floatPresupuesto, 2, '.', ',');
                }
                if( !empty($floatTotalPorID) && !empty($floatPresupuestoID) )
                {
                    $arrayInformacionDashboard['totalPorcentajeMrcID']  = number_format($floatTotalPorID, 2, '.', ',');
                    $arrayInformacionDashboard['totalPresupuestoMrcID'] = number_format($floatPresupuestoID, 2, '.', ',');
                }  
                if( !empty($floatTotalPorBS) && !empty($floatPresupuestoBS) )
                {
                    $arrayInformacionDashboard['totalPorcentajeMrcBS']  = number_format($floatTotalPorBS, 2, '.', ',');
                    $arrayInformacionDashboard['totalPresupuestoMrcBS'] = number_format($floatPresupuestoBS, 2, '.', ',');
                }  

                $arrayParametrosResultadoVentasNRC                    = $arrayParametros;
                $arrayParametrosResultadoVentasNRC['strTipo']         = 'NRC';
                $arrayParametrosResultadoVentasNRC['strTipoConsulta'] = 'CUMPLIMIENTO_NRC';

                $arrayFacturacionNRC = $this->getDetalleFacturacionAsesor($arrayParametrosResultadoVentasNRC);
                if( !empty($arrayFacturacionNRC) )
                {
                    $floatTotalPor = 0;
                    $floatAcumFac  = 0;
                    $floatAcumMet  = 0;
    
                    foreach( $arrayFacturacionNRC as $arrayItemCumplimiento )
                    {
                        $floatAcumFac += floatval($arrayItemCumplimiento['FACTURACION']);
                        $floatAcumMet += floatval($arrayItemCumplimiento['META']);
                    }
                    $floatTotalPor = (floatval($floatAcumFac)/floatval($floatAcumMet))*100;                
                }
                $arrayInformacionDashboard['totalPorcentajeNrc']  = number_format(0, 2, '.', ',');
                $arrayInformacionDashboard['totalPresupuestoNrc'] = number_format(0, 2, '.', ',');
                if( !empty($floatTotalPor) && !empty($floatAcumMet) )
                {
                    $arrayInformacionDashboard['totalPorcentajeNrc']  = number_format($floatTotalPor, 2, '.', ',');
                    $arrayInformacionDashboard['totalPresupuestoNrc'] = number_format($floatAcumMet, 2, '.', ',');
                }
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información del dashboard comercial. - Prefijo('.
                                     $strPrefijoEmpresa.'), FechaInicio('.$strFechaInicio.'), FechaFin('.$strFechaFin.')'); 
            }//( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strFechaFin) )
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getInformacionDashboard',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayInformacionDashboard;
    }


    /**
     * Obtiene el catalogo de titulos
     * @return array de arrays clave/valor
     * @see \telconet\schemaBundle\Entity\AdmiTitulo
     * @author wsanchez
     */
    function obtenerCatalogoTitulos() {
        $list = $this->emComercial->getRepository('schemaBundle:AdmiTitulo')->getRegistros('', 'Activo', '', '');
        /* @var $value \telconet\schemaBundle\Entity\AdmiTitulo */
        $array = array_map ( function ($value) {return array (
                        'k' => $value->getId(),
                        'v' => $value->getDescripcionTitulo() 
        ); }, $list );
        return $array;
    }
    
    /**
     * validateDirExistCreate, metodo que valida si el directorio donde se creará el log de pagos en linea existe
     * si no existe, lo crea
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.0 21-04-2016
     * @return ReturnResponse Retorna objeto con un codigo de exito cuando no ha existido ningun error
     */
    public function validateDirExistCreate($arrayParametros)
    {
        $objReturnResponse = new ReturnResponse();
        $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);

        //Realiza el split del path enviado por el separador /
        $arrayPath = explode('/', $arrayParametros['strPath']);
        try
        {
            //Itera el array construido a partir del string enviado como strPathLog
            foreach($arrayPath as $strPath):
                $arrayParametros['strPathTelcos'] = $arrayParametros['strPathTelcos'] . '/' . $strPath;
                //Pregunta si no existe el directorio y envia a crear el directorio
                if(!file_exists($arrayParametros['strPathTelcos']) && !is_dir($arrayParametros['strPathTelcos']))
                {
                    mkdir($arrayParametros['strPathTelcos'], 0777, true);
                }
            endforeach;
            
            $objReturnResponse->setStrStatus($objReturnResponse::PROCESS_SUCCESS);
            $objReturnResponse->setStrMessageStatus("Se creó el directorio correctamente");
        }
        catch(\Exception $ex)
        {
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR . $e->getMessage());
        }
        return $objReturnResponse;
    }//validateDirExistCreate
    

     /**
     * validarFrecuenciaRecurrente
     *
     * Función encargada de validar si el producto tiene asociado la caracteristica de FACTURACION PROPORCIONAL
     *
     * @author Richard Cabrera  <rcabrera@telconet.ec>
     * @version 1.0 11-04-2017
     *
     *
     * @param array $arrayParametros [strCaracteristica => descripción de la caracteristica
     *                                intServicioId     => id del servicio
     *                                strUser           => usuario de ejecución
     *                                strIpUser         => ip de ejecución ]
     *
     * @return string $strExisteHistorial
     *
     */
    public function validarFrecuenciaRecurrente($arrayParametros)
    {   
        $strExisteHistorial       = "";
        $arrayParametrosProdCarac = array();
        $arrayParametrosHist      = array();

        try
        {
            //se valida si el producto tiene asociada la caracteristica de FACTURACION_PROPORCIONAL

            $arrayParametrosProdCarac["strCaracteristica"]  = $arrayParametros["strCaracteristica"];
            $arrayParametrosProdCarac["strEstado"]          = "Activo";
            $arrayParametrosProdCarac["intServicioId"]      = $arrayParametros["intServicioId"];

            $strExisteCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                         ->getCaracteristicaProducto($arrayParametrosProdCarac);
            
            //Se valida si ya existe ingresada la acción o la observación ingresadas, caso contrario se ingresa
            if($strExisteCaracteristica === "S")
            {
                $arrayParametrosHist["intServicioId"]  = $arrayParametros["intServicioId"];
                $arrayParametrosHist["strEstado"]      = "Activo";
                $arrayParametrosHist["strAccion"]      = "confirmarServicio";
                $arrayParametrosHist["strObservacion"] = "Se confirmo el servicio";

                $strExisteHistorial = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                        ->getServicioHistorial($arrayParametrosHist);
            }
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'SoporteService->validarFrecuenciaRecurrente',
                                            $e->getMessage(),
                                            $arrayParametros['strUser'],
                                            $arrayParametros['strIpUser']);
        }

        return $strExisteHistorial;
    }
    
     /**
     * creaObjetoInfoDetalleSolCaract, crea objeto InfoDetalleSolCaract
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.0 03-05-2017
     * 
     * @param array $arrayRequest[
     *                          $entityAdmiCaracteristica     => Recibe la entidad AdmiCaracteristica
     *                          floatValor                    => Recibe el valor del descuento
     *                          entityDetalleSolicitud        => Recibe la entidad InfoDetalleSolicitud
     *                          strEstado                     => Recibe el estado de la solicitud
     *                          strUsrCreacion                => Recibe el usuario de creacion
     *                          ]
     *  
     * @return entity Retorna el objeto creado de la entidad InfoDetalleSolCaract
     */
    public function creaObjetoInfoDetalleSolCaract($arrayRequest)
    {
        $entityDetalleSolCaract = new InfoDetalleSolCaract();
        $entityDetalleSolCaract->setCaracteristicaId($arrayRequest['entityAdmiCaracteristica']);
        $entityDetalleSolCaract->setValor($arrayRequest['floatValor']);
        $entityDetalleSolCaract->setDetalleSolicitudId($arrayRequest['entityDetalleSolicitud']);
        $entityDetalleSolCaract->setEstado($arrayRequest['strEstado']);
        $entityDetalleSolCaract->setUsrCreacion($arrayRequest['strUsrCreacion']);
        $entityDetalleSolCaract->setFeCreacion(new \DateTime('now'));
        
        return $entityDetalleSolCaract;
    }

    /**
     * getDetalleFacturacionAsesor
     *
     * Actualización: Se muestran los datos de Internet/Datos y Business Solutions en el detallado.
     * @author David León <mdleon@telconet.ec>
     * @version 1.2 11-12-2021
     * 
     * Actualización: Se valida si tipo de consulta es DETALLADO para que pueda recorrer el cursor con la data.
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 24-04-2018
     * 
     * Función encargada para retornar la información necesaria para la presentación de facturación del asesor
     *
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 19-03-2018
     * 
     * @param array $arrayParametros [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                strFechaFin             => Fecha final de la búsqueda
     *                                strUsrCreacion          => Usuario en sessión
     *                                strIpCreacion           => Ip del usuario en sessión
     *                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserComercial        => Usuario del esquema comercial 'DB_COMERCIAL'
     *                                strPasswordComercial    => Password del esquema comercial 'DB_COMERCIAL'
     *                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *
     * @return array $arrayCarteraAsesores
     * 
     */
    public function getDetalleFacturacionAsesor($arrayParametros)
    {
        $arrayCarteraAsesores = array();

        $strUsrCreacion       = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                              ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion        = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                              ? $arrayParametros['strIpCreacion'] : '127.0.0.1';

        try
        {
            $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                     ? $arrayParametros['strPrefijoEmpresa'] : null;
            $strFechaInicio        = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                                     ? $arrayParametros['strFechaInicio'] : null;
            $strFechaFin           = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']) )
                                     ? $arrayParametros['strFechaFin'] : null;
            $strTipoConsulta       = ( isset($arrayParametros['strTipoConsulta']) && !empty($arrayParametros['strTipoConsulta']) )
                                     ? $arrayParametros['strTipoConsulta'] : null;
            $intIdPersonEmpresaRol = ( isset($arrayParametros['intIdPersonEmpresaRol']) && !empty($arrayParametros['intIdPersonEmpresaRol']) )
                                     ? $arrayParametros['intIdPersonEmpresaRol'] : null;
            $strEmailUsrSession    = $strUsrCreacion.'@telconet.ec';
            $strValorFormaContacto = ""; 

            $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonEmpresaRol);

            if ( is_object($objInfoPersonaEmpresaRol) )
            {
                $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                if ( is_object($objInfoPersona) )
                {
                    $strValorFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                         ->getValorFormaContactoPorCodigo($objInfoPersona,'MAIL');

                    if ( !is_null($strValorFormaContacto))
                    {
                        $strEmailUsrSession = strtolower($strValorFormaContacto);
                    }
                }
            }
            if( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strFechaFin) )
            {

                //SE CONSULTA LA INFORMACIÓN DE CARTERA MRC Y NRC
                $arrayParametrosFactAsesores                       = $arrayParametros;
                $arrayParametrosFactAsesores['intRownum']          = 3;
                $arrayParametrosFactAsesores['strTipoConsulta']    = $strTipoConsulta;
                $arrayParametrosFactAsesores['strEmailUsrSession'] = $strEmailUsrSession;
                $cursorFactAsesores                                = $this->emFinanciero
                                                                          ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                          ->getDetalleFacturacionAsesor($arrayParametrosFactAsesores);
                if ($strTipoConsulta == 'DETALLADO' || $strTipoConsulta == 'DETALLADO_TRIMESTRAL')
                {
                    if( !empty($cursorFactAsesores) )
                    {
                        while( ($arrayResultadoCursor = oci_fetch_array($cursorFactAsesores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                        {
                            $arrayItemAsesor                   = array();
                            $arrayItemAsesor['strCliente']     = ( isset($arrayResultadoCursor['CLIENTE']) 
                                                                  && !empty($arrayResultadoCursor['CLIENTE']) )
                                                                ? ucwords(strtolower($arrayResultadoCursor['CLIENTE'])) : '';
                            $arrayItemAsesor['strLogin']       = ( isset($arrayResultadoCursor['LOGIN']) && !empty($arrayResultadoCursor['LOGIN']) )
                                                                  ? $arrayResultadoCursor['LOGIN'] : '';
                            $arrayItemAsesor['strUsrVendedor'] = ( isset($arrayResultadoCursor['USR_VENDEDOR']) 
                                                                  && !empty($arrayResultadoCursor['USR_VENDEDOR']) )
                                                                  ? $arrayResultadoCursor['USR_VENDEDOR'] : '';
                            $arrayItemAsesor['strProducto']    = ( isset($arrayResultadoCursor['DESCRIPCION_PRODUCTO']) 
                                                                  && !empty($arrayResultadoCursor['DESCRIPCION_PRODUCTO']) )
                                                                ? ucwords(strtolower($arrayResultadoCursor['DESCRIPCION_PRODUCTO'])) : '';
                            $arrayItemAsesor['strObservacion'] = ( isset($arrayResultadoCursor['OBSERVACION_PRODUCTO']) 
                                                                  && !empty($arrayResultadoCursor['OBSERVACION_PRODUCTO']) )
                                                                ? $arrayResultadoCursor['OBSERVACION_PRODUCTO'] : '';
                            $arrayItemAsesor['floatFacMrc']    = ( isset($arrayResultadoCursor['FAC_MRC'])&& !empty($arrayResultadoCursor['FAC_MRC']))
                                                                ? $arrayResultadoCursor['FAC_MRC'] : 0;
                            $arrayItemAsesor['floatNcMrc']     = ( isset($arrayResultadoCursor['NC_MRC']) && !empty($arrayResultadoCursor['NC_MRC']) )
                                                                ? $arrayResultadoCursor['NC_MRC'] : 0;
                            $arrayItemAsesor['floatFacNrc']    = ( isset($arrayResultadoCursor['FAC_NRC'])&& !empty($arrayResultadoCursor['FAC_NRC']))
                                                                ? $arrayResultadoCursor['FAC_NRC'] : 0;
                            $arrayItemAsesor['floatNcNrc']     = ( isset($arrayResultadoCursor['NC_NRC'])&& !empty($arrayResultadoCursor['NC_NRC']) )
                                                                ? $arrayResultadoCursor['NC_NRC'] : 0;
                            $arrayItemAsesor['floatFacMrcID']  = ( isset($arrayResultadoCursor['FAC_MRCID']) 
                                                                 && !empty($arrayResultadoCursor['FAC_MRCID']))? $arrayResultadoCursor['FAC_MRCID']:0;
                            $arrayItemAsesor['floatNcMrcID']   = ( isset($arrayResultadoCursor['NC_MRCID'])
                                                                 && !empty($arrayResultadoCursor['NC_MRCID']))? $arrayResultadoCursor['NC_MRCID'] : 0;
                            $arrayItemAsesor['floatFacMrcBS']  = ( isset($arrayResultadoCursor['FAC_MRCBS']) 
                                                                 && !empty($arrayResultadoCursor['FAC_MRCBS']))? $arrayResultadoCursor['FAC_MRCBS']:0;
                            $arrayItemAsesor['floatNcMrcBS']   = ( isset($arrayResultadoCursor['NC_MRCBS'])
                                                                 && !empty($arrayResultadoCursor['NC_MRCBS']))? $arrayResultadoCursor['NC_MRCBS'] : 0;
                            $arrayCarteraAsesores[] = $arrayItemAsesor;
                        }//while( ($arrayResultadoCursor = oci_fetch_array($cursorCarteraAsesores, OCI_ASSOC + OCI_RETURN_NULLS)) != false )
                    }//( !empty($cursorFactAsesores) )
                }
                else if ( ($strTipoConsulta == 'AGRUPADO')|| $strTipoConsulta == 'POR_FACTURAR' )
                {
                    if( !empty($cursorFactAsesores) )
                    {
                        while( ($arrayResultadoCursor = oci_fetch_array($cursorFactAsesores, OCI_ASSOC + OCI_RETURN_NULLS)) )
                        {
                            $arrayItemAsesor                    = array();
                            $arrayItemAsesor['MES']             = ( isset($arrayResultadoCursor['MES']) 
                                                                    && !empty($arrayResultadoCursor['MES']) )
                                                                    ? $arrayResultadoCursor['MES'] : 0;
                            $arrayItemAsesor['strCliente']  = ( isset($arrayResultadoCursor['CLIENTE']) 
                                                                  && !empty($arrayResultadoCursor['CLIENTE']) )
                                                                  ? $arrayResultadoCursor['CLIENTE'] : '';  
                            $arrayItemAsesor['strUsrVendedor']  = ( isset($arrayResultadoCursor['USR_VENDEDOR']) 
                                                                  && !empty($arrayResultadoCursor['USR_VENDEDOR']) )
                                                                  ? $arrayResultadoCursor['USR_VENDEDOR'] : '';                            
                            $arrayItemAsesor['floatFacMrc']     = ( isset($arrayResultadoCursor['FAC_MRC']) 
                                                                  && !empty($arrayResultadoCursor['FAC_MRC']) )
                                                                ? $arrayResultadoCursor['FAC_MRC'] : 0;
                            $arrayItemAsesor['floatNcMrc']      = ( isset($arrayResultadoCursor['NC_MRC'])
                                                                  && !empty($arrayResultadoCursor['NC_MRC']) )
                                                                ? $arrayResultadoCursor['NC_MRC'] : 0;
                            $arrayItemAsesor['floatFacNrc']     = ( isset($arrayResultadoCursor['FAC_NRC'])
                                                                  && !empty($arrayResultadoCursor['FAC_NRC']) )
                                                                ? $arrayResultadoCursor['FAC_NRC'] : 0;
                            $arrayItemAsesor['floatNcNrc']      = ( isset($arrayResultadoCursor['NC_NRC'])
                                                                  && !empty($arrayResultadoCursor['NC_NRC']) )
                                                                ? $arrayResultadoCursor['NC_NRC'] : 0;
                            $arrayCarteraAsesores[] = $arrayItemAsesor;
                        }
                    }//( !empty($cursorFactAsesores) )
                }
                else if ( $strTipoConsulta == "CUMPLIMIENTO_NRC"  || $strTipoConsulta == "CUMPLIMIENTO_MRC" )
                {
                    if( !empty($cursorFactAsesores) )
                    {
                        while( ($arrayResultadoCursor = oci_fetch_array($cursorFactAsesores, OCI_ASSOC + OCI_RETURN_NULLS)) )
                        {
                            $arrayItemAsesor                    = array();
                            $arrayItemAsesor['USR_VENDEDOR']  = ( isset($arrayResultadoCursor['USR_VENDEDOR']) 
                                                                  && !empty($arrayResultadoCursor['USR_VENDEDOR']) )
                                                                  ? $arrayResultadoCursor['USR_VENDEDOR'] : '';
                            $arrayItemAsesor['BASE']            = ( isset($arrayResultadoCursor['BASE'])  && !empty($arrayResultadoCursor['BASE']) )
                                                                ? $arrayResultadoCursor['BASE'] : '';
                            $arrayItemAsesor['META']            = ( isset($arrayResultadoCursor['META'])  && !empty($arrayResultadoCursor['META']) )
                                                                ? $arrayResultadoCursor['META'] : 0;
                            $arrayItemAsesor['FACTURACION']      = ( isset($arrayResultadoCursor['FACTURACION'])
                                                                  && !empty($arrayResultadoCursor['FACTURACION']) )
                                                                ? $arrayResultadoCursor['FACTURACION'] : 0;
                            $arrayItemAsesor['DIF_PRESUPUESTO']     = ( isset($arrayResultadoCursor['DIF_PRESUPUESTO'])
                                                                  && !empty($arrayResultadoCursor['DIF_PRESUPUESTO']) )
                                                                ? $arrayResultadoCursor['DIF_PRESUPUESTO'] : 0;
                            $arrayItemAsesor['CUMPLIMIENTO_META']      = ( isset($arrayResultadoCursor['CUMPLIMIENTO_META'])
                                                                  && !empty($arrayResultadoCursor['CUMPLIMIENTO_META']) )
                                                                ? $arrayResultadoCursor['CUMPLIMIENTO_META'] : 0;
                            $arrayItemAsesor['BASEID']        = ( isset($arrayResultadoCursor['BASEID']) && !empty($arrayResultadoCursor['BASEID']) )
                                                                ? $arrayResultadoCursor['BASEID'] : 0;
                            $arrayItemAsesor['FACTURACIONID'] = ( isset($arrayResultadoCursor['FACTURACIONID'])
                                                                  && !empty($arrayResultadoCursor['FACTURACIONID']) )
                                                                ? $arrayResultadoCursor['FACTURACIONID'] : 0;
                            $arrayItemAsesor['BASEBS']        = ( isset($arrayResultadoCursor['BASEBS']) && !empty($arrayResultadoCursor['BASEBS']) )
                                                                ? $arrayResultadoCursor['BASEBS'] : 0;
                            $arrayItemAsesor['METAID']        = ( isset($arrayResultadoCursor['METAID']) && !empty($arrayResultadoCursor['METAID']) )
                                                                ? $arrayResultadoCursor['METAID'] : 0;
                            $arrayItemAsesor['FACTURACIONBS'] = ( isset($arrayResultadoCursor['FACTURACIONBS'])
                                                                  && !empty($arrayResultadoCursor['FACTURACIONBS']) )
                                                                ? $arrayResultadoCursor['FACTURACIONBS'] : 0;
                            $arrayItemAsesor['METABS']      = ( isset($arrayResultadoCursor['METABS']) && !empty($arrayResultadoCursor['METABS']) )
                                                                ? $arrayResultadoCursor['METABS'] : 0;
                            $arrayItemAsesor['DIF_PRESUPUESTOID']   = ( isset($arrayResultadoCursor['DIF_PRESUPUESTOID'])
                                                                  && !empty($arrayResultadoCursor['DIF_PRESUPUESTOID']) )
                                                                ? $arrayResultadoCursor['DIF_PRESUPUESTOID'] : 0;
                            $arrayItemAsesor['DIF_PRESUPUESTOBS']   = ( isset($arrayResultadoCursor['DIF_PRESUPUESTOBS'])
                                                                  && !empty($arrayResultadoCursor['DIF_PRESUPUESTOBS']) )
                                                                ? $arrayResultadoCursor['DIF_PRESUPUESTOBS'] : 0;
                            $arrayItemAsesor['CUMPLIMIENTO_METAID'] = ( isset($arrayResultadoCursor['CUMPLIMIENTO_METAID'])
                                                                  && !empty($arrayResultadoCursor['CUMPLIMIENTO_METAID']) )
                                                                ? $arrayResultadoCursor['CUMPLIMIENTO_METAID'] : 0;
                            $arrayItemAsesor['CUMPLIMIENTO_METABS'] = ( isset($arrayResultadoCursor['CUMPLIMIENTO_METABS'])
                                                                  && !empty($arrayResultadoCursor['CUMPLIMIENTO_METABS']) )
                                                                ? $arrayResultadoCursor['CUMPLIMIENTO_METABS'] : 0;
                            $arrayCarteraAsesores[] = $arrayItemAsesor;
                        }
                    }//( !empty($cursorFactAsesores) )
                }                
            }
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información del dashboard comercial. - Prefijo('.
                                     $strPrefijoEmpresa.'), FechaInicio('.$strFechaInicio.'), FechaFin('.$strFechaFin.')'); 
            }//( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strFechaFin) )
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getInformacionFacturacionAsesor',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }

        return $arrayCarteraAsesores;
    }

    /**
     * getInformacionVentasAgrupadas
     *      
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 07-11-2018
     * 
     * Método que retorna los vendedores, la cantidad y total de las ventas dependiendo de los parámetros enviados por el usuario
     *
     * @param array $arrayParametros [strPrefijoEmpresa    => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio       => Fecha de inicio de la búsqueda
     *                                strFechaFin          => Fecha final de la búsqueda
     *                                strCategoria         => Categoría de los productos a buscar
     *                                strGrupo             => Grupo de los productos a buscar
     *                                strSubgrupo          => Subgrupo de los productos a buscar
     *                                strUsrCreacion       => Usuario en sessión
     *                                strIpCreacion        => Ip del usuario en sessión
     *                                strFrecuencia        => Frecuencia facturación del servicio
     *                                strDatabaseDsn       => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserComercial     => Usuario del esquema comercial 'DB_COMERCIAL'
     *                                strTipoPersonal      => El tipo del personal en sessión si es 'VENDEDOR' o 'SUBGERENTE'
     *                                intIdPersonEmpresaRol=> Id del usuario en sessión
     *                                strOpcionSelect      => Bandera que indica lo que se desea obtener del SELECT
     *                                strEmailUsrSession   => Email del usuario en sessión
     *                                strPasswordComercial => Password del esquema comercial 'DB_COMERCIAL' ]
     *
     * @return array $arrayVentasAsesores
     * 
     */
    public function getInformacionVentasAgrupadas($arrayParametros)
    {
        $arrayVentasAsesores = array();
        try
        {
            $objFactAsesores = $this->emComercial
                                        ->getRepository('schemaBundle:InfoServicio')
                                        ->getInformacionVentasAgrupadas($arrayParametros);
            if( !empty($objFactAsesores) )
            {
                while( ($arrayResultadoCursor = oci_fetch_array($objFactAsesores, OCI_ASSOC + OCI_RETURN_NULLS)) )
                {                    
                    $arrayItemAsesor                    = array();

                    $arrayItemAsesor['strUsrVendedor']  = ( isset($arrayResultadoCursor['USR_VENDEDOR']) 
                                                            && !empty($arrayResultadoCursor['USR_VENDEDOR']) )
                                                            ? $arrayResultadoCursor['USR_VENDEDOR'] : '';
                    $arrayItemAsesor['intTotalVentas']     = ( isset($arrayResultadoCursor['CANTIDAD_ORDENES']) 
                                                            && !empty($arrayResultadoCursor['CANTIDAD_ORDENES']) )
                                                        ? $arrayResultadoCursor['CANTIDAD_ORDENES'] : 0;
                    $arrayItemAsesor['floatValorVentas']      = ( isset($arrayResultadoCursor['TOTAL_VENTA'])
                                                            && !empty($arrayResultadoCursor['TOTAL_VENTA']) )
                                                        ? $arrayResultadoCursor['TOTAL_VENTA'] : 0;
                    $arrayVentasAsesores[] = $arrayItemAsesor;
                }
            }//( !empty($objFactAsesores) )               
            else
            {
                throw new \Exception('No se han enviado los parámetros adecuados para consultar la información del dashboard comercial. - Prefijo('.
                                     $strPrefijoEmpresa.'), FechaInicio('.$strFechaInicio.'), FechaFin('.$strFechaFin.')'); 
            }//( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strFechaFin) )
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getInformacionVentasAgrupadas',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayVentasAsesores;
    }

    /**
     * getOrdenesNuevas
     *
     * Función encargada para retornar todas las ordenes necesaria para la presentación de facturación del asesor
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @param array $arrayParametrosServiceComercial [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                                strFechaFin             => Fecha final de la búsqueda
     *                                                strUsrCreacion          => Usuario en sessión
     *                                                strIpCreacion           => Ip del usuario en sessión
     *                                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                                strTipo                 => Tipo
     *                                                strTipoConsulta         => Tipo de consulta
     *                                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *                                                strTipoPersonal         => Tipo del personal
     *                                                intIdPersonEmpresaRol   => Id de la persona
     *
     * @return array $arrayDatosOrdenesNuevas ['vendedor'     => Todos los vendedores de acuerdo al intIdPersonEmpresaRol recibido por parametro
     *                                         'TOTAL'       => cantidad de las órdenes nuevas
     *                                         'SUMATOTAL'   => suma total
     *                                        ] 
     * 
     */    
    public function getOrdenesNuevas($arrayParametrosServiceComercial)
    {
        try
        {        
            $arrayDatosOrdenesNuevas = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')->getOrdenesNuevas($arrayParametrosServiceComercial);        
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getOrdenesNuevas',
                                            $e->getMessage(),
                                            $arrayDatosOrdenesNuevas);
        }
        return $arrayDatosOrdenesNuevas;
    }
    
    /**
     * getListaOrdenes
     *
     * Función encargada para retornar todas las ordenes que tienen un cambio de plan
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @param array $arrayParametros [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                strFechaFin             => Fecha final de la búsqueda
     *                                strUsrCreacion          => Usuario en sessión
     *                                strIpCreacion           => Ip del usuario en sessión
     *                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                strTipo                 => Tipo
     *                                strTipoConsulta         => Tipo de consulta
     *                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *                                strTipoPersonal         => Tipo del personal
     *                                intIdPersonEmpresaRol   => Id de la persona
     * @return array $arrayListaOrdenes
     * 
     */        
    public function getListaOrdenes($arrayParametros)
    {
        try
        {                
            $arrayListaOrdenes = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->getListaOrdenes($arrayParametros);        
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getListaOrdenes',
                                            $e->getMessage(),
                                            $arrayListaOrdenes);
        }
        return $arrayListaOrdenes;        
    }

    /**
     * getDatosOrdenes
     *
     * Función encargada para retornar todas las ordenes upgrade agrupadas por vendedor
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @param mixed strFechaInicio => Fecha de inicio de la búsqueda
     * @param mixed strFechaFin    => Fecha final de la búsqueda
     * @param mixed $intIdServicio => Id del servicio
     * @return array $arrayDatosOrdenes ['USR_VENDEDOR'     => Todos los vendedores de acuerdo a los parametros
     *                                          'observacion'       => observacion donde se detallará las ordenes up y dow
     *                                         ]
     * 
     */          
    public function getDatosOrdenes($strFechaInicio,$strFechaFin,$intIdServicio)
    {
        try
        {          
            $arrayDatosOrdenes = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')->getDatosOrdenes($strFechaInicio,$strFechaFin,$intIdServicio);
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getDatosOrdenes',
                                            $e->getMessage(),
                                            $arrayDatosOrdenes);
        }        
        return $arrayDatosOrdenes;
    }
    
    /**
     * getVendedor
     *
     * Función encargada para retornar todos los vendedores que reportan segun el parametro.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @param  string $IdReportaPersona
     * @param  string $strPrefijoEmpresa
     * @return array $arrayListaVendedores
     * 
     */      
    public function getVendedor($intIdReporta,$strPrefijoEmpresa)
    {
        try
        { 
            $arrayListaVendedores = $this->emComercial->getRepository('schemaBundle:InfoPersona')->getVendedor($intIdReporta,$strPrefijoEmpresa);
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getVendedor',
                                            $e->getMessage(),
                                            $arrayListaVendedores);
        }         
        return $arrayListaVendedores;
    }   
    
    /**
     * getInfoVendedor
     *
     * Función encargada para retornar el cargo.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @param  string $strUser
     * @param  string $strPrefijoEmpresa
     * @return array $arrayInformacionVendedor
     * 
     */      
    public function getInfoVendedor($strUser,$strPrefijoEmpresa)
    {
        try
        {        
            $arrayInformacionVendedor = $this->emComercial->getRepository('schemaBundle:InfoPersona')->getInfoVendedor($strUser,$strPrefijoEmpresa);            
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getInfoVendedor',
                                            $e->getMessage(),$arrayInformacionVendedor);
        }         
        
        return $arrayInformacionVendedor;
    }
    
    /**
     * getClientesCancelados
     *
     * Función encargada para retornar los clientes cancelados segun el tipo de personal.
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 17-08-2018
     * 
     * @param array $arrayParametrosServiceComercial [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                                strFechaFin             => Fecha final de la búsqueda
     *                                                strUsrCreacion          => Usuario en sessión
     *                                                strIpCreacion           => Ip del usuario en sessión
     *                                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                                strTipo                 => Tipo
     *                                                strTipoConsulta         => Tipo de consulta
     *                                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *                                                strTipoPersonal         => Tipo del personal
     *                                                intIdPersonEmpresaRol   => Id de la persona
     * @return array $arrayClientesCancel   ['VENDEDOR'     => Todos los vendedores de acuerdo al intIdPersonEmpresaRol recibido por parametro
     *                                       'CANTIDAD'     => cantidad de las órdenes a facturar
     *                                       'TOTAL'        => cantidad
     *                                      ]
     * 
     */    
    public function getClientesCancelados($arrayParametrosServiceComercial)
    {
        try
        {         
            $arrayClientesCancel = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')->getClientesCancelados($arrayParametrosServiceComercial);                
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getClientesCancelados',
                                            $e->getMessage(),
                                            $arrayClientesCancel);
        }         
        return $arrayClientesCancel;
    }    
    
    public function getComparacionFacturacionAsesor($arrayParametros)
    {
        try
        {        
            $arrayFacturacion                   = $this->getDetalleFacturacionAsesor($arrayParametros);            
            $arrayParametros['strFechaInicio']  = date("d-M-Y", strtotime("-1 month", strtotime($arrayParametros['strFechaInicio'])));
            $arrayFacturacionAnt                = $this->getDetalleFacturacionAsesor($arrayParametros);        
            $arrayCltNuevos                     = array();
            $intCantidadFactAnt                 = count($arrayFacturacionAnt);

            if(!empty($arrayFacturacion) && !empty($arrayFacturacionAnt))
            {
                foreach($arrayFacturacion as $arrayItemFact)
                {
                    $boolExisteClt = false;
                    for( $intCont=0; $intCont<$intCantidadFactAnt; $intCont++ )
                    {                        
                        if( $arrayFacturacionAnt[$intCont]['strCliente']==$arrayItemFact['strCliente'] )
                        {                        
                            $boolExisteClt=true;
                        }
                    }
                    if(!$boolExisteClt)
                    {
                        $arrayCltNuevosAux = array('VENDEDOR'=> $arrayItemFact['strUsrVendedor'],
                                                   'CLIENTE' => $arrayItemFact['strCliente'],
                                                   'TOTAL'   => $arrayItemFact['floatFacMrc']+$arrayItemFact['floatNcMrc']);
                        array_push($arrayCltNuevos,$arrayCltNuevosAux);
                    }                
                    
                }
            }//if(!empty($arrayFacturacion) && !empty($arrayFacturacionAnt))       
        }
        catch( \Exception $e )
        {
            $this->serviceUtil->insertError('TELCOS+',
                                                'ComercialService.getComparacionFacturacionAsesor',
                                                $e->getMessage(),
                                                $arrayCltNuevosTotal);
        }
        return $arrayCltNuevos;
    }
    /**
     * getCumplimiento
     *
     * Función encargada para enviar correo de la información necesaria para la presentación de cumplimiento del asesor
     *
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 25-10-2018
     * 
     * @param array $arrayParametros [strPrefijoEmpresa       => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio          => Fecha de inicio de la búsqueda
     *                                strFechaFin             => Fecha final de la búsqueda
     *                                strUsrCreacion          => Usuario en sessión
     *                                strIpCreacion           => Ip del usuario en sessión
     *                                strDatabaseDsn          => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserComercial        => Usuario del esquema comercial 'DB_COMERCIAL'
     *                                strPasswordComercial    => Password del esquema comercial 'DB_COMERCIAL'
     *                                strUserBiFinanciero     => Usuario del esquema comercial 'BI_FINANCIERO'
     *                                strPasswordBiFinanciero => Password del esquema comercial 'BI_FINANCIERO' ]
     *
     * @return array $arrayCarteraAsesores
     * 
     */
    public function getCumplimiento($arrayParametros)
    {
        $strUsrCreacion       = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                              ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';

        try
        {
            $strPrefijoEmpresa     = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                                     ? $arrayParametros['strPrefijoEmpresa'] : null;
            $strFechaInicio        = ( isset($arrayParametros['strFechaInicio']) && !empty($arrayParametros['strFechaInicio']) )
                                     ? $arrayParametros['strFechaInicio'] : null;
            $strFechaFin           = ( isset($arrayParametros['strFechaFin']) && !empty($arrayParametros['strFechaFin']) )
                                     ? $arrayParametros['strFechaFin'] : null;
            $strTipoConsulta       = ( isset($arrayParametros['strTipoConsulta']) && !empty($arrayParametros['strTipoConsulta']) )
                                     ? $arrayParametros['strTipoConsulta'] : null;
            $intIdPersonEmpresaRol = ( isset($arrayParametros['intIdPersonEmpresaRol']) && !empty($arrayParametros['intIdPersonEmpresaRol']) )
                                     ? $arrayParametros['intIdPersonEmpresaRol'] : null;

            $strEmailUsrSession    = $strUsrCreacion.'@telconet.ec';
            $strValorFormaContacto = ""; 

            $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPersonEmpresaRol);

            if ( is_object($objInfoPersonaEmpresaRol) )
            {
                $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                if ( is_object($objInfoPersona) )
                {
                    $strValorFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                         ->getValorFormaContactoPorCodigo($objInfoPersona,'MAIL');

                    if ( !is_null($strValorFormaContacto))
                    {
                        $strEmailUsrSession = strtolower($strValorFormaContacto);
                    }
                }
            }
            if( !empty($strPrefijoEmpresa) && !empty($strFechaInicio) && !empty($strFechaFin) )
            {

                //SE CONSULTA LA INFORMACIÓN DE CARTERA MRC Y NRC
                $arrayParametrosFactAsesores                       = $arrayParametros;
                $arrayParametrosFactAsesores['strTipoConsulta']    = $strTipoConsulta;
                $arrayParametrosFactAsesores['strEmailUsrSession'] = $strEmailUsrSession;
                $strMensajeRespuesta                                = $this->emFinanciero
                                                                          ->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                          ->getCumplimientoAsesor($arrayParametrosFactAsesores);
            }
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getCumplimientoAsesor',
                                            $e->getMessage(),
                                            $strMensajeExito);
        }   
        return $strMensajeRespuesta;
    }
    
    /**
     * 
     * Método encargado de devolver un string HTML con el detalle de recursos contratados ( resumen ) para mostrar en el grid de cambio de recursos.
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.0
     * @since 21-03-2019
     * 
     * @param Array $arrayParametros [
     * intIdServicio : Id del servicio
     * strTipoRecurso : Tipo de recurso
     * intIdSolicitud : Id de la solicitud
     * ]
     * @return string
     */
    public function getHtmlResumenRecursosPorProducto($arrayParametros)
    {
        $objServicio      = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($arrayParametros['intIdServicio']);
        if(is_object($objServicio))
        {
            $objProducto      = $objServicio->getProductoId();
            if(is_object($objProducto))
            {
                $strNombreTecnico = $objProducto->getNombreTecnico();
                if(is_object($strNombreTecnico))
                {
                    $boolEsAlquilerServidores = $this->serviceTecnico->isContieneCaracteristica($objProducto,'ES_ALQUILER_SERVIDORES');
                    $boolEsPoolRecursos       = $this->serviceTecnico->isContieneCaracteristica($objProducto,'ES_POOL_RECURSOS');

                    if($arrayParametros['strTipoRecurso'] == 'actuales')
                    {
                        if($strNombreTecnico == 'HOSTING')
                        {
                            if($boolEsAlquilerServidores)
                            {
                                $arrayParametros['strTipoRecurso'] = 'TIPO ALQUILER SERVIDOR';

                                $strResumenHtml .= "<tr><td><table>";

                                $strResumenHtml .= $this->obtenerResumenPorRecurso($arrayParametros);

                                $strResumenHtml .= "</table></td></tr>";
                            }

                            if($boolEsPoolRecursos)
                            {
                                $strResumenHtml .= "<table>";

                                $arrayParametros['strTipoRecurso'] = 'DISCO';

                                $strResumenHtml .= $this->obtenerResumenPorRecurso($arrayParametros);

                                $arrayParametros['strTipoRecurso'] = 'PROCESADOR';

                                $strResumenHtml .= $this->obtenerResumenPorRecurso($arrayParametros);

                                $arrayParametros['strTipoRecurso'] = 'MEMORIA RAM';

                                $strResumenHtml .= $this->obtenerResumenPorRecurso($arrayParametros);

                                $strResumenHtml .= "</table>";
                            }
                        }
                    }
                    else//Recursos modificados por solicitud
                    {
                        $strResumenHtml .= '<table>';

                        if($boolEsPoolRecursos)
                        {
                            $objCaracteristica = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                  ->findOneByDescripcionCaracteristica('TIPO CAMBIO RECURSOS');

                            if(is_object($objCaracteristica))
                            {
                                $arraySolCaract = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                    ->findBy(array('detalleSolicitudId' => $arrayParametros['intIdSolicitud'],
                                                                                   'caracteristicaId'   => $objCaracteristica->getId()
                                                                                   ));

                                foreach($arraySolCaract as $objSolCaract)
                                {
                                    $strColor              = '';
                                    $strTipoCambioRecursos = $objSolCaract->getValor();

                                    if($strTipoCambioRecursos == 'RECURSOS EDITADOS')
                                    {
                                        $strColor = 'green';
                                    }
                                    else if($strTipoCambioRecursos == 'RECURSOS ELIMINADOS')
                                    {
                                        $strColor = 'red';
                                    }
                                    else
                                    {
                                        $strColor = 'blue';
                                    }

                                    $strResumenHtml .= '<tr><td><b style="color:'.$strColor.';"><i class="fa fa-square" aria-hidden="true"></i></b>&nbsp;'
                                                      .'<b style="text-decoration: underline;">'.$objSolCaract->getValor().'</b></td></tr>';

                                    $objCaracteristicaTipo = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                  ->findOneByDescripcionCaracteristica('TIPO RECURSO');

                                    if(is_object($objCaracteristicaTipo))
                                    {
                                        //Tipo de Recurso
                                        $arrayCaractTipo = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                             ->findBy(array('detalleSolCaractId' => $objSolCaract->getId(),
                                                                                            'caracteristicaId'   => $objCaracteristicaTipo->getId()));

                                        foreach($arrayCaractTipo as $objCaractTipo)
                                        {
                                            //Se coloca si el recurso fue agregado/editado/eliminado
                                            $strResumenHtml .= '<tr><td><b>'.$objCaractTipo->getValor().'</b></td></tr>';

                                            if(is_object($objCaractTipo))
                                            {
                                            $strUnidad = 'GB';

                                            if($objCaractTipo->getValor() == 'PROCESADOR')
                                            {
                                                $strUnidad = 'Cores';
                                            }

                                            //Descripción
                                            $objCaracteristicaDescripcion = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                                              ->findOneByDescripcionCaracteristica('DESCRIPCION RECURSO');
                                            //Valor
                                            $objCaracteristicaValor       = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                                                              ->findOneByDescripcionCaracteristica('VALOR RECURSO');

                                            if(is_object($objCaracteristicaDescripcion) && is_object($objCaracteristicaValor))
                                            {
                                                $arrayCaractDescripcion = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                               ->findBy(array('detalleSolCaractId' => $objCaractTipo->getId(),
                                                                                              'caracteristicaId'   => $objCaracteristicaDescripcion->getId()));

                                                foreach($arrayCaractDescripcion as $objCaract)
                                                {
                                                    $objCaractValor       = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolCaract")
                                                                                 ->findOneBy(array('detalleSolCaractId' => $objCaract->getId(),
                                                                                                   'caracteristicaId'   => $objCaracteristicaValor->getId()));
                                                    if(is_object($objCaractValor))
                                                    {
                                                        $strResumenHtml .= '<tr><td><i class="fa fa-angle-double-right" aria-hidden="true"></i>'
                                                                         . '&nbsp;'.$objCaract->getValor(). '&nbsp;'
                                                                         . '(<b b style="color:green;">'.$objCaractValor->getValor().' '.$strUnidad.'</b>)</td>';
                                                    }
                                                }
                                            }//validacion caracteristicas para buscar detalle de solicitud
                                        }//validacion tipo de recurso
                                        }
                                    } 
                                }//endforeach
                            }
                        }                        

                        $strResumenHtml .= '</table>';
                    }
                }
            }
        }                
        return $strResumenHtml;
    }
    
    /**
     * 
     * Método encargado de devolver un string con el detalle de recursos contratados ( resumen )
     * 
     * @author Karen Rodríguez <kyrodriguez@telconet.ec>
     * @version 1.0
     * @since 21-03-2019
     * 
     * @param Array $arrayParametros [
     * strTipoRecurso = Tipo de Recurso
     * ]
     * @return string
     */
    private function obtenerResumenPorRecurso($arrayParametros)
    {        
        $strTipoRecursoValue               = $arrayParametros['strTipoRecurso'];
        if(!empty($strTipoRecursoValue))
        {
            $arrayParametros['strTipoRecurso'] = $strTipoRecursoValue.'_VALUE';

            $arrayRecursos = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                               ->getArrayRecursosPoolPorTipo($arrayParametros);

            $arrayParametros['strTipoRecurso']          = $strTipoRecursoValue;
            $arrayDetalleRecursos  = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                       ->getArrayCaracteristicasPorTipoYServicio($arrayParametros);

            if(!empty($arrayRecursos) && isset($arrayRecursos['totalRecurso']))
            {
                $strUnidad = '(GB)';

                if($strTipoRecursoValue == 'PROCESADOR')
                {
                    $strUnidad = '(Cores)';
                }

                if($strTipoRecursoValue == 'TIPO ALQUILER SERVIDOR')
                {
                    $strUnidad = '';
                }

                $strResumenHtml .= 
                                    "<tr>"
                                      . "<td><b>".$arrayParametros['strTipoRecurso']."</b></td>"
                                      . "<td><i class='fa fa-long-arrow-right' aria-hidden='true'></i></td>"
                                      . "<td><b style='color:#46A0E2;'>".$arrayRecursos['totalRecurso']." ".$strUnidad."</b></td>".
                                    "</tr>";

                foreach($arrayDetalleRecursos as $array)
                {
                    $strResumenHtml .= "<tr>"
                                       . "<td colspan='7'><i class='fa fa-angle-double-right' aria-hidden='true'></i>&nbsp;".
                                           $array['nombreRecurso']." (<b style='color:green;'>".$array['valor']."</b>)"
                                       . "</td>"
                                     . "</tr>";
                }
            }
        }
        return $strResumenHtml;
    }

    /**
     * Obtiene puntos por forma de contacto enviado por parámetro.
     * arrayParametro: [
     *     strValorFormaContacto => Valor de forma de contacto para consultar.
     *     strTipoFormaContacto  => Tipo de forma de contacto que se desea consultar FONO, MAIL O MOVIL
     *     strCodEmpresa         => Código de la empresa.
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-10-2019
     * @since 1.0
     * @return JsonResponse
     */
    public function getPuntoByFormasContacto($arrayParametro)
    {
        $strValor                 = $arrayParametro['strValorFormaContacto'];
        $strTipo                  = $arrayParametro['strTipoFormaContacto'];
        $strCodEmpresa            = $arrayParametro['strCodEmpresa'];
        $arrayRespuesta           = array();
        try
        {
            //Consultamos los seguimientos por usuario
            $arrayParametros                           = array();
            $arrayParametros['strUserDbComercial']     = $this->usrComercial;
            $arrayParametros['strPasswordDbComercial'] = $this->passwdComercial;
            $arrayParametros['strDatabaseDsn']         = $this->databaseDsn;
            $arrayParametros['strCodEmpresa']          = $strCodEmpresa;
            $arrayParametros['strValorFormaContacto']  = $strValor;
            $arrayParametros['strTipoFormaContacto']   = $strTipo;
            $objCursor                                 = $this->emComercial->getRepository("schemaBundle:AdmiFormaContacto")
                                                                           ->getPuntoByFormasContacto($arrayParametros);
            if( !empty($objCursor) )
            {
                while( $arrayResultadoCursor = oci_fetch_array($objCursor, OCI_ASSOC + OCI_RETURN_NULLS) )
                {
                    $intIdPunto          = ( isset($arrayResultadoCursor['ID_PUNTO'])
                                           && !empty($arrayResultadoCursor['ID_PUNTO']) )
                                           ? $arrayResultadoCursor['ID_PUNTO'] : '';
                    $strLogin            = ( isset($arrayResultadoCursor['LOGIN'])
                                           && !empty($arrayResultadoCursor['LOGIN']) )
                                           ? $arrayResultadoCursor['LOGIN'] : '';
                    $arrayRespuesta[]    = array(
                                                 "idPunto" => $intIdPunto,
                                                 "login"   => $strLogin
                                                );
                }
            }
        }
        catch(\Exception $e)
        {
            error_log('ComercialBundle.ComercialService.getPuntoByFormasContacto: '.$e->getMessage());
            $this->serviceUtil->insertError( 'Telcos+',
                                          'ComercialBundle.ComercialService.getPuntoByFormasContacto',
                                          'Error al consultar los logins por forma de contacto. '.$e->getMessage(),
                                          'telcos',
                                          '127.0.0.1' );
        }
        return $arrayRespuesta;
    }

    /**
     * Método que permite actualizar el estado de uso del contacto del cliente o del punto cliente
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 07-02-2020
     *
     * @param  Array $arrayParametros [
     *                                  intIdContacto    : Id del contacto sea de la INFO_PERSONA_FORMA_CONTACTO o INFO_PUNTO_FORMA_CONTACTO.
     *                                  strTipoContacto  : Tipo de contacto de referencia para poder diferenciar la tabla a la
     *                                                     que se debe actualizar (personaFormaContacto/puntoFormaContacto).
     *                                  strEstadoWs      : Estado de usabilidad del contacto del cliente.
     *                                  strFechaEstadoWs : Fecha de creación en la que el estado de uso es creado o actualizado.
     *                                  strUser          : Usuario quien realiza la petición.
     *                                  strIp            : Ip del usuario quien realiza la petición.
     *                                ]
     * @return Array $arrayRespuesta
     */
    public function setNumeroMovilEstadoWhatsapp($arrayParametros)
    {
        $strUser          = $arrayParametros["strUser"] ? $arrayParametros["strUser"] : "Telcos+";
        $strIp            = $arrayParametros["strIp"]   ? $arrayParametros["strIp"]   : "127.0.0.1";
        $strTipoContacto  = $arrayParametros['strTipoContacto'];
        $intIdContacto    = $arrayParametros['intIdContacto'];
        $strEstadoWs      = $arrayParametros['strEstadoWs'];
        $strFechaEstadoWs = $arrayParametros['strFechaEstadoWs'];

        $this->emComercial->beginTransaction();

        try
        {
            if (empty($intIdContacto) || $intIdContacto === null)
            {
                throw new \Exception("Error : Valor nulo en idContacto");
            }

            $arrayFechaHora = explode(' ', $strFechaEstadoWs);
            $arrayFecha     = explode('-', $arrayFechaHora[0]);

            if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]))
            {
                throw new \Exception("Error : Formato de fecha incorrecto. Formato esperado: YYYY-MM-DD HH24:MI:SS");
            }

            if (strtotime($arrayFechaHora[1]) === false)
            {
                throw new \Exception("Error : Formato de hora incorrecto. Formato esperado: YYYY-MM-DD HH24:MI:SS");
            }

            $objFechaEstadoWs = new \DateTime($strFechaEstadoWs);

            if ($strTipoContacto === 'personaFormaContacto')
            {
                $objInfoPersonaFormaContacto = $this->emComercial->getRepository("schemaBundle:InfoPersonaFormaContacto")->find($intIdContacto);

                if (!is_object($objInfoPersonaFormaContacto))
                {
                    throw new \Exception("Error : No existe el idContacto");
                }

                $objInfoPersonaFormaContacto->setEstadoWs($strEstadoWs);
                $objInfoPersonaFormaContacto->setFeCreacionWs($objFechaEstadoWs);
                $this->emComercial->persist($objInfoPersonaFormaContacto);
            }
            elseif($strTipoContacto === 'puntoFormaContacto')
            {
                $objInfoPuntoFormaContacto = $this->emComercial->getRepository("schemaBundle:InfoPuntoFormaContacto")->find($intIdContacto);

                if (!is_object($objInfoPuntoFormaContacto))
                {
                    throw new \Exception("Error : No existe idContacto");
                }

                $objInfoPuntoFormaContacto->setEstadoWs($strEstadoWs);
                $objInfoPuntoFormaContacto->setFeCreacionWs($objFechaEstadoWs);
                $this->emComercial->persist($objInfoPuntoFormaContacto);
            }
            else
            {
                throw new \Exception("Error : Tipo de contacto incorrecto");
            }

            $this->emComercial->flush();
            $this->emComercial->commit();

            $arrayRespuesta = array ('status'       => 'ok',
                                     'message'      => 'Dato Actualizado',
                                     'idContacto'   =>  $intIdContacto,
                                     'tipoContacto' =>  $strTipoContacto);
        }
        catch(\Exception $objException)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->close();
            }

            $strMessage = 'Error al actualizar el estado del contacto del cliente o punto cliente.';

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode('Error : ',$objException->getMessage())[1];
            }

            $this->serviceUtil->insertError('Telcos+',
                                            'comercialBundle.ComercialService.setNumeroMovilEstadoWhatsapp',
                                             $objException->getMessage(),
                                             $strUser,
                                             $strIp);

            $arrayRespuesta = array ('status'       => 'fail',
                                     'message'      =>  $strMessage,
                                     'idContacto'   =>  $intIdContacto,
                                     'tipoContacto' =>  $strTipoContacto);
        }
        return $arrayRespuesta;
    }

    /**
     * Documentación para la función 'getInformacionVentasTelcosCRM'.
     * 
     * Función que retorna las ordenes de servicio consultadas.
     *
     * @param array $arrayParametros [strPrefijoEmpresa    => Prefijo de la empresa que realizará la consulta para la información comercial
     *                                strFechaInicio       => Fecha de inicio de la búsqueda
     *                                strFechaFin          => Fecha final de la búsqueda
     *                                strCategoria         => Categoría de los productos a buscar
     *                                strGrupo             => Grupo de los productos a buscar
     *                                strSubgrupo          => Subgrupo de los productos a buscar
     *                                strUsrCreacion       => Usuario en sessión
     *                                strIpCreacion        => Ip del usuario en sessión
     *                                strFrecuencia        => Frecuencia facturación del servicio
     *                                strDatabaseDsn       => Base de datos a la cual se conectará para realizar la consulta
     *                                strUserComercial     => Usuario del esquema comercial 'DB_COMERCIAL'
     *                                strTipoPersonal      => El tipo del personal en sessión si es 'VENDEDOR' o 'SUBGERENTE'
     *                                intIdPersonEmpresaRol=> Id del usuario en sessión
     *                                strOpcionSelect      => Bandera que indica lo que se desea obtener del SELECT
     *                                strEmailUsrSession   => Email del usuario en sessión
     *                                strPasswordComercial => Password del esquema comercial 'DB_COMERCIAL' ]
     * 
     * @return array $arrayResultado  ['arrayOrdenes  '      => 'Arreglo con el resultado del procedimiento',
     *                                 'strMensajeRespuesta' => 'Mensaje de respuesta devuelta por el procedimiento',
     *                                 'strMensajeError'     => 'Mensaje de error']
     * 
     * @author Kevin Baque <kbaque@telconet.ec>
     * @version 1.0 21-09-2020
     * 
     */
    public function getInformacionVentasTelcosCRM($arrayParametros)
    {
        $strIpCreacion         = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                    ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $strEmailUsrSession    = ( isset($arrayParametros['strEmailUsrSession']) && !empty($arrayParametros['strEmailUsrSession']) )
                                    ? $arrayParametros['strEmailUsrSession'] : 'TELCOS +';
        $strOpcionSelect       = ( isset($arrayParametros['strOpcionSelect']) && !empty($arrayParametros['strOpcionSelect']) )
                                    ? $arrayParametros['strOpcionSelect'] : '';
        $arrayOrdenes          = array();

        try
        {
            $arrayResultado = $this->emComercial
                                   ->getRepository('schemaBundle:InfoServicio')
                                   ->getInformacionVentasTelcosCRM($arrayParametros);
            if(isset($arrayResultado["strMensajeError"]) && !empty($arrayResultado["strMensajeError"]))
            {
                throw new \Exception($arrayResultado["strMensajeError"]); 
            }
            if(isset($arrayResultado["objOrdenes"]) && !empty($arrayResultado["objOrdenes"]))
            {
                if(!empty($strOpcionSelect) && $strOpcionSelect == "DETALLE")
                {
                    $strMensajeRespuesta = $arrayResultado["strMensajeRespuesta"];
                }
                else
                {
                    while( ($arrayResultadoCursor = oci_fetch_array($arrayResultado["objOrdenes"], OCI_ASSOC+OCI_RETURN_NULLS)) )
                    {
                        $arrayItemOrden                    = array();

                        $arrayItemOrden['strUsrVendedor']    = ( isset($arrayResultadoCursor['USR_VENDEDOR']) 
                                                                  && !empty($arrayResultadoCursor['USR_VENDEDOR']) )
                                                                    ? $arrayResultadoCursor['USR_VENDEDOR'] : '';

                        $arrayItemOrden['intCantPropuestas'] = ( isset($arrayResultadoCursor['CANTIDAD_PROPUESTA']) 
                                                                  && !empty($arrayResultadoCursor['CANTIDAD_PROPUESTA']) )
                                                                    ? $arrayResultadoCursor['CANTIDAD_PROPUESTA'] : 0;

                        $arrayItemOrden['intCantOrdenesCrm'] = ( isset($arrayResultadoCursor['CANTIDAD_ORDENES_CRM']) 
                                                                  && !empty($arrayResultadoCursor['CANTIDAD_ORDENES_CRM']) )
                                                                    ? $arrayResultadoCursor['CANTIDAD_ORDENES_CRM'] : 0;

                        $arrayItemOrden['intCantOrdenes']    = ( isset($arrayResultadoCursor['CANTIDAD_ORDENES']) 
                                                                  && !empty($arrayResultadoCursor['CANTIDAD_ORDENES']) )
                                                                    ? $arrayResultadoCursor['CANTIDAD_ORDENES'] : 0;

                        $arrayItemOrden['intTotalVentasMrc'] = ( isset($arrayResultadoCursor['TOTAL_VENTA_MRC']) 
                                                                  && !empty($arrayResultadoCursor['TOTAL_VENTA_MRC']) )
                                                                    ? $arrayResultadoCursor['TOTAL_VENTA_MRC'] : 0;

                        $arrayItemOrden['intTotalVentasNrc'] = ( isset($arrayResultadoCursor['TOTAL_VENTA_NRC']) 
                                                                  && !empty($arrayResultadoCursor['TOTAL_VENTA_NRC']) )
                                                                    ? $arrayResultadoCursor['TOTAL_VENTA_NRC'] : 0;

                        $arrayItemOrden['intCantSolicitud']  = ( isset($arrayResultadoCursor['CANTIDAD_SOLICITUDES']) 
                                                                  && !empty($arrayResultadoCursor['CANTIDAD_SOLICITUDES']) )
                                                                    ? $arrayResultadoCursor['CANTIDAD_SOLICITUDES'] : 0;

                        $arrayItemOrden['intTotalDescuento'] = ( isset($arrayResultadoCursor['TOTAL_DESCUENTOS']) 
                                                                  && !empty($arrayResultadoCursor['TOTAL_DESCUENTOS']) )
                                                                    ? $arrayResultadoCursor['TOTAL_DESCUENTOS'] : 0;
                        $arrayOrdenes[] = $arrayItemOrden;
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $strMensajeError = $e->getMessage();
            $this->serviceUtil->insertError('TELCOS+',
                                            'ComercialService.getInformacionVentasTelcosCRM',
                                            $e->getMessage(),
                                            $strEmailUsrSession,
                                            $strIpCreacion);
        }
        $arrayResultado['arrayOrdenes']        = $arrayOrdenes;
        $arrayResultado['strMensajeRespuesta'] = $strMensajeRespuesta;
        $arrayResultado['strMensajeError']     = $strMensajeError;

        return $arrayResultado;
    }

    /**
     * Función que permite insertar un registro en el el historial de visualizacion
     *
     * @author Francisco Cueva <facueva@telconet.ec>
     * @version 03-06-2021
     * @since 1.0
     *
     * @param array $arrayParametros  Recibe los parametros para guardar el registro.
     */
    public function insertInfoVisualizacionDoc($arrayParametros)
    {
        $strEmpresaCod         = !empty($arrayParametros['empresaCod'])
                                    ? $arrayParametros['empresaCod']
                                    : '10';
        $strAccion             = $arrayParametros['accion'];
        $strIdentificacion     = $arrayParametros['identificacion'];
        $strEstadoServicio     = $arrayParametros['estadoServicio'];
        $strObservacion        = $arrayParametros['observacion'];
        $strTipoDocumento      = $arrayParametros['tipoDocumento'];
        $strUsrCreacion        = !empty($arrayParametros['usrCreacion'])
                                   ? $arrayParametros['usrCreacion']
                                   : 'TELCOS';
        $strIpCreacion         = $arrayParametros['ipCreacion'];
        $strLoginCliente       = $arrayParametros['loginCliente'];
        $objReturnResponse     = new ReturnResponse();

        try
        {
            $entityHist = new InfoVisualizacionDocHist();
            $entityHist->setEmpresaCod($strEmpresaCod);
            $entityHist->setAccion($strAccion);
            $entityHist->setObservacion($strObservacion);
            $entityHist->setEstadoServicio($strEstadoServicio);
            $entityHist->setIdentificacion($strIdentificacion);
            $entityHist->setTipoDocumento($strTipoDocumento);
            $entityHist->setLoginCliente($strLoginCliente);
            $entityHist->setUsrCreacion($strUsrCreacion);
            $entityHist->setFeCreacion(new \DateTime('now'));
            $entityHist->setIpCreacion($strIpCreacion);

            $this->emComercial->persist($entityHist);
            $this->emComercial->flush();
            $objReturnResponse->setStrStatus("200");
            $objReturnResponse->setStrMessageStatus("OK");

        }
        catch (\Exception $ex)
        {
            error_log("InfoLog: " . $ex->getMessage());
            $objReturnResponse->setStrStatus($objReturnResponse::ERROR_TRANSACTION);
            $objReturnResponse->setStrMessageStatus($objReturnResponse::MSN_ERROR_TRANSACTION);

        }
        return $objReturnResponse;
    }
}