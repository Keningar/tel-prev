<?php
/*
 * To change this template, choose Tools | Templates and open the template in the editor.
 */
namespace telconet\tecnicoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoServicio;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\tecnicoBundle\Utils\LZCompressor\LZString;


// ...
// ... Services
// ...
// use telconet\tecnicoBundle\Service\ProcesoMasivoService;
class InfoServicioCorteController extends Controller implements TokenAuthenticatedController {

    /**
     * Documentación para el método 'corteMasivoClienteAction'.
     *
     * Método para redireccionar a la pantalla de corte masivo de clientes
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.1 21-09-2017   Se validan los permisos del ciclo de Facturacion para la empresa MD
     * @since 1.0 Versión Inicial
     *
     * @author Jorge Guerrero<jguerrerop@telconet.ec>
     * @version 1.2 01-12-2017
     * Se agrega el parametro por empresa configurado en la admi_parametro
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 02-10-2021 Se parametrizan los ids de las formas de pago que muestran las opciones de Cuenta/Tarjeta, Tipos de Cuenta/Tarjeta,
     *                         y Bancos
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.8 07-03-2023     Se incluye el prefijo de la empresa para considerar a Ecuanet en la busqueda de los parametros asociados a EN.
     *
     */
    public function corteMasivoClienteAction() {
        $emGeneral  = $this->getDoctrine()->getManager('telconet_general');
        $request = $this->getRequest();
        $session = $request->getSession();
        $strEmpresaCod     = $session->get('idEmpresa');
        $strPrefijoEmpresa = $session->get('prefijoEmpresa');

        $arrayParametros['strPrefijoEmpresa'] = $strPrefijoEmpresa;
        $arrayParametros['strEmpresaCod']     = $strEmpresaCod;

        $serviceComercial   = $this->get('comercial.Comercial');
        $strAplicaCiclosFac = $serviceComercial->aplicaCicloFacturacion($arrayParametros);
        
        if ($strAplicaCiclosFac == 'S' )
        {
            $arrayEmpresaPermitida['PERMISOS_EMPRESA'] = true;
        }
        else
        {
            $arrayEmpresaPermitida['PERMISOS_EMPRESA'] = false;
        }
        $strIdsFormasPagoCuentaTarjetaBancos            = "";
        $arrayIdsFormasPagoCuentaTarjetaBancos          = array();
        $arrayIdsFormasPagoCuentaTarjetaBancosParams    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get(  'PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoEmpresa, 
                                                                            '', 
                                                                            '', 
                                                                            '',
                                                                            'CORTE_MASIVO',
                                                                            'IDS_FORMAS_PAGO_CUENTA_TARJETA_BANCOS',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            $strEmpresaCod);
        if(is_array($arrayIdsFormasPagoCuentaTarjetaBancosParams) && count($arrayIdsFormasPagoCuentaTarjetaBancosParams) > 0)
        {
            foreach($arrayIdsFormasPagoCuentaTarjetaBancosParams as $arrayIdFormaPagoCuentaTarjetaBanco)
            {   
                $arrayIdsFormasPagoCuentaTarjetaBancos[] = $arrayIdFormaPagoCuentaTarjetaBanco['valor3'];
            }
            
            $strIdsFormasPagoCuentaTarjetaBancos = implode(",", $arrayIdsFormasPagoCuentaTarjetaBancos);
        }
        return $this->render(   'tecnicoBundle:InfoServicioCorte:corteMasivoCliente.html.twig',
                                array('boolPermisoEmpresa'                  => $arrayEmpresaPermitida,
                                      'strIdsFormasPagoCuentaTarjetaBancos' => $strIdsFormasPagoCuentaTarjetaBancos));
    }

    /**
     * Función que sirve para obtener el json con las oficinas y formas de pago concatenados
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-08-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-09-2021 Se agrega envío de parámetro procesoEjecutante para ocultar formas de pago que no deben mostrarse en la pantalla
     *                         de corte masivo
     * 
     * @return JsonResponse $objJsonResponse
     */
    public function getOficinasYFormasPagoAction()
    {
        $emComercial            = $this->getDoctrine()->getManager("telconet");
        $objJsonResponse        = new JsonResponse();
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $intStart               = $objRequest->query->get('start');
        $strProcesoEjecutante   = $objRequest->get('procesoEjecutante');
        $strJsonOficinas        = $emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                              ->generarJsonOficinaGrupoPorEmpresa($strCodEmpresa, "Activo", $intStart, 100);
        
        $arrayFormasPagoParaContrato    = $emComercial->getRepository('schemaBundle:AdmiFormaPago')
                                                      ->getFormasPagoParaContrato(array("strEmpresaCod"         => $strCodEmpresa,
                                                                                        "strEstadoFp"           => "Activo",
                                                                                        "strProcesoEjecutante"  => $strProcesoEjecutante));
        $arrayRegistrosFormasPago       = $arrayFormasPagoParaContrato['objRegistros'];
        $intTotalFormasPago             = $arrayFormasPagoParaContrato['intTotal'];
        $arrayDataFormasPago            = array();
        foreach($arrayRegistrosFormasPago as $arrayFpParaContrato)
        {
            $arrayDataFormasPago[]  = array('idFormaPago'           => $arrayFpParaContrato['intIdFormaPago'],
                                            'descripcionFormaPago'  => $arrayFpParaContrato['strDescripcionFormaPago']);                          
        }
        $arrayResultadoFormasPago   = array("total"         => $intTotalFormasPago,
                                            "encontrados"   => $arrayDataFormasPago);
        $strJsonFormasPago          = json_encode($arrayResultadoFormasPago);
        $objJsonResponse->setContent($strJsonOficinas . "&" . $strJsonFormasPago);
        return $objJsonResponse;
    }
    
    /**
     * Documentación para el método 'getPuntosACortarAction'
     *
     * Método para obtener los puntos a cortar
     *
     * @author Jorge Guerrero <jguerrerop@telconet.ec>
     * @version 1.1 21-09-2017   Se agrega el filtro del ciclo de facturacion
     * @since 1.0 Versión Inicial
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 11-08-2020 Se modifica función ya que la consulta principal se la obtendrá desde un procedimiento de la base de datos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 06-09-2021 Se agregan el envío de los parámetros de la fecha de creación del documento y los tipos de documentos a la consulta
     *                         de los servicios a cortar
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.4 12-09-2022 - Se agrega mas datos al arrayParamsBusqueda para mejora de Filtros en busqueda de puntos para corte masivo
     *                             (fechaLimActivacion e identificacionesExcluidas)
     * 
     * @return Json respuesta  Devuelve el JSON de los puntos a cortar
     */
    public function getPuntosACortarAction() 
    {
        $emComercial                        = $this->getDoctrine()->getManager('telconet');
        $objJsonResponse                    = new JsonResponse();
        $objLzString                        = new LZString();
        $objRequest                         = $this->getRequest();
        $objSession                         = $objRequest->getSession();
        $strCodEmpresa                      = $objSession->get('idEmpresa');
        $strFechaCreacionDocBusqueda        = $objRequest->get('fechaCreacionDocBusqueda');
        $strTiposDocumentosBusqueda         = $objRequest->get('tiposDocumentosBusqueda');
        $intNumDocsAbiertosBusqueda         = $objRequest->get('numDocsAbiertosBusqueda');
        $strValorMontoCarteraBusqueda       = $objRequest->get('valorMontoCarteraBusqueda');
        $intIdTipoNegocioBusqueda           = $objRequest->get('idTipoNegocioBusqueda');
        $strValorClienteCanalBusqueda       = $objRequest->get('valorClienteCanalBusqueda') ? $objRequest->get('valorClienteCanalBusqueda') : 'Todos';
        $strNombreUltimaMillaBusqueda       = $objRequest->get('nombreUltimaMillaBusqueda');
        $intIdCicloFacturacionBusqueda      = $objRequest->get('idCicloFacturacionBusqueda');
        $strIdsOficinasBusqueda             = $objRequest->get('idsOficinasBusqueda');
        $strIdsFormasPagoBusqueda           = $objRequest->get('idsFormasPagoBusqueda');
        $strValorCuentaTarjetaBusqueda      = $objRequest->get('valorCuentaTarjetaBusqueda');
        $strIdsTiposCuentaTarjetaBusqueda   = $objRequest->get('idsTiposCuentaTarjetaBusqueda');
        $strIdsBancosBusqueda               = $objRequest->get('idsBancosBusqueda');
        $intStart                           = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit                           = $objRequest->get('limit') ? $objRequest->get('limit') : 0;
        $strPermiteConsultar                = $objRequest->get('permiteConsultar') ? $objRequest->get('permiteConsultar') : 'NO';
        $strFechaLimActivacion              = $objRequest->get('fechaLimActivacion');
        $strIdentificacionesExcluidas       = $objRequest->get('identificacionesExcluidas');
        $strIdentificacionesExcluidas       = $objLzString->decompressFromBase64($strIdentificacionesExcluidas);

        if($strPermiteConsultar === "SI")
        {
            if(isset($strFechaCreacionDocBusqueda) && !empty($strFechaCreacionDocBusqueda))
            {
                $arrayFechaCreacionDocBusqueda = explode('T', $strFechaCreacionDocBusqueda);
                if(isset($arrayFechaCreacionDocBusqueda) && !empty($arrayFechaCreacionDocBusqueda))
                {
                    $strFechaCreacionDocBusqueda = $arrayFechaCreacionDocBusqueda[0];
                }
            }
            if(isset($strTiposDocumentosBusqueda) && !empty($strTiposDocumentosBusqueda))
            {
                $arrayTiposDocumentosBusqueda = array_unique(explode(',', $strTiposDocumentosBusqueda));
                if(isset($arrayTiposDocumentosBusqueda) && !empty($arrayTiposDocumentosBusqueda))
                {
                    $strTiposDocumentosBusqueda = implode(",", $arrayTiposDocumentosBusqueda);
                }
            }

            $arrayFinalIdExcluidas = array();

            if($strIdentificacionesExcluidas != "")
            {
                $arrayIdentificacionesExcluidas = json_decode($strIdentificacionesExcluidas);
                $strIdentificacionesExcluidas = "";

                foreach($arrayIdentificacionesExcluidas as $id) 
                {
                    if(is_null($id->IDENTIFICACION))
                    {
                        $strJsonResponse    = json_encode(array('status'            => "FORMAT_ERROR",
                                                    'mensaje'           => "Documento con formato Incorrecto, favor revisar.",
                                                    'intTotal'          => 0,
                                                    'arrayResultado'    => array()));
        
                        $objJsonResponse->setContent($strJsonResponse);
                        return $objJsonResponse;
                    }
                    else
                    {
                        $strIdentificacionesExcluidas = $strIdentificacionesExcluidas . $id->IDENTIFICACION .",";
                    }
                }

                $strIdentificacionesExcluidas = strtoupper(substr($strIdentificacionesExcluidas, 0, -1));
                $arrayFinalIdExcluidas = explode(',' ,$strIdentificacionesExcluidas);
            }        

            if($strFechaLimActivacion != "")
            {
                $strFechaLimActivacion = date('Y-m-d', strtotime($strFechaLimActivacion));
            }

            $arrayParametros    = array("strDatabaseDsn"        => $this->container->getParameter('database_dsn'),
                                        "strUserComercial"      => $this->container->getParameter('user_comercial'),
                                        "strPasswordComercial"  => $this->container->getParameter('passwd_comercial'),
                                        "arrayParamsBusqueda"   => array(
                                                                            "strCodEmpresa"             => $strCodEmpresa,
                                                                            "strFechaCreacionDoc"       => $strFechaCreacionDocBusqueda,
                                                                            "strTiposDocumentos"        => $strTiposDocumentosBusqueda,
                                                                            "strNumDocsAbiertos"        => $intNumDocsAbiertosBusqueda,
                                                                            "strValorMontoCartera"      => $strValorMontoCarteraBusqueda,
                                                                            "strIdTipoNegocio"          => $intIdTipoNegocioBusqueda,
                                                                            "strValorClienteCanal"      => $strValorClienteCanalBusqueda,
                                                                            "strNombreUltimaMilla"      => $strNombreUltimaMillaBusqueda,
                                                                            "strIdCicloFacturacion"     => $intIdCicloFacturacionBusqueda,
                                                                            "strIdsOficinas"            => $strIdsOficinasBusqueda,
                                                                            "strIdsFormasPago"          => $strIdsFormasPagoBusqueda,
                                                                            "strValorCuentaTarjeta"     => $strValorCuentaTarjetaBusqueda,
                                                                            "strIdsTiposCuentaTarjeta"  => $strIdsTiposCuentaTarjetaBusqueda,
                                                                            "strIdsBancos"              => $strIdsBancosBusqueda,
                                                                            "strStart"                  => $intStart,
                                                                            "strLimit"                  => $intLimit,
                                                                            "strFechaLimActivacion"     => $strFechaLimActivacion,
                                                                            "arrayFinalIdExcluidas"     => $arrayFinalIdExcluidas
                                                                            )
                                        );
            $strJsonResponse    = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getJsonPuntosCorteMasivo($arrayParametros);
        }
        else
        {
            $strJsonResponse    = json_encode(array('status'            => "OK",
                                                    'mensaje'           => "",
                                                    'intTotal'          => 0,
                                                    'arrayResultado'    => array()));
        }
        $objJsonResponse->setContent($strJsonResponse);
        return $objJsonResponse;
    }
    
    /**
     * Documentación para el método 'getResumenCorteMasivoAction'
     *
     * Método para generar resumen previo de los clientes a cortar
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-10-2021
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.1 12-09-2022 - Se agrega mas datos al arrayParamsBusqueda para mejora de Filtros en busqueda de puntos para corte masivo
     *                             (fechaLimActivacion e identificacionesExcluidas)
     */
    public function getResumenCorteMasivoAction() 
    {
        $emComercial                        = $this->getDoctrine()->getManager('telconet');
        $objJsonResponse                    = new JsonResponse();
        $objLzString                        = new LZString();
        $objRequest                         = $this->getRequest();
        $objSession                         = $objRequest->getSession();
        $strCodEmpresa                      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa                  = $objSession->get('prefijoEmpresa');
        $strFechaCreacionDocBusqueda        = $objRequest->get('fechaCreacionDocBusqueda');
        $strTiposDocumentosBusqueda         = $objRequest->get('tiposDocumentosBusqueda');
        $intNumDocsAbiertosBusqueda         = $objRequest->get('numDocsAbiertosBusqueda');
        $strValorMontoCarteraBusqueda       = $objRequest->get('valorMontoCarteraBusqueda');
        $intIdTipoNegocioBusqueda           = $objRequest->get('idTipoNegocioBusqueda');
        $strValorClienteCanalBusqueda       = $objRequest->get('valorClienteCanalBusqueda') ? $objRequest->get('valorClienteCanalBusqueda') : 'Todos';
        $strNombreUltimaMillaBusqueda       = $objRequest->get('nombreUltimaMillaBusqueda');
        $intIdCicloFacturacionBusqueda      = $objRequest->get('idCicloFacturacionBusqueda');
        $strIdsOficinasBusqueda             = $objRequest->get('idsOficinasBusqueda');
        $strIdsFormasPagoBusqueda           = $objRequest->get('idsFormasPagoBusqueda');
        $strValorCuentaTarjetaBusqueda      = $objRequest->get('valorCuentaTarjetaBusqueda');
        $strIdsTiposCuentaTarjetaBusqueda   = $objRequest->get('idsTiposCuentaTarjetaBusqueda');
        $strIdsBancosBusqueda               = $objRequest->get('idsBancosBusqueda');
        $strPermiteConsultar                = $objRequest->get('permiteConsultar') ? $objRequest->get('permiteConsultar') : 'NO';
        $strFechaLimActivacion              = $objRequest->get('fechaLimActivacion');
        $strIdentificacionesExcluidas       = $objRequest->get('identificacionesExcluidas');
        $strIdentificacionesExcluidas       = $objLzString->decompressFromBase64($strIdentificacionesExcluidas);

        if($strPermiteConsultar === "SI")
        {
            if(isset($strFechaCreacionDocBusqueda) && !empty($strFechaCreacionDocBusqueda))
            {
                $arrayFechaCreacionDocBusqueda = explode('T', $strFechaCreacionDocBusqueda);
                if(isset($arrayFechaCreacionDocBusqueda) && !empty($arrayFechaCreacionDocBusqueda))
                {
                    $strFechaCreacionDocBusqueda = $arrayFechaCreacionDocBusqueda[0];
                }
            }
            if(isset($strTiposDocumentosBusqueda) && !empty($strTiposDocumentosBusqueda))
            {
                $arrayTiposDocumentosBusqueda = array_unique(explode(',', $strTiposDocumentosBusqueda));
                if(isset($arrayTiposDocumentosBusqueda) && !empty($arrayTiposDocumentosBusqueda))
                {
                    $strTiposDocumentosBusqueda = implode(",", $arrayTiposDocumentosBusqueda);
                }
            }

            $arrayFinalIdExcluidas = array();

            if($strIdentificacionesExcluidas != "")
            {
                $arrayIdentificacionesExcluidas = json_decode($strIdentificacionesExcluidas);
                $strIdentificacionesExcluidas = "";

                foreach($arrayIdentificacionesExcluidas as $id) 
                {
                    if(is_null($id->IDENTIFICACION))
                    {
                        $strJsonResponse    = json_encode(array('status'            => "FORMAT_ERROR",
                                                    'mensaje'           => "Documento con formato Incorrecto, favor revisar.",
                                                    'arrayResultado'    => array()));
        
                        $objJsonResponse->setContent($strJsonResponse);
                        return $objJsonResponse;
                    }
                    else
                    {
                        $strIdentificacionesExcluidas = $strIdentificacionesExcluidas . $id->IDENTIFICACION .",";
                    }
                }

                $strIdentificacionesExcluidas = strtoupper(substr($strIdentificacionesExcluidas, 0, -1));
                $arrayFinalIdExcluidas = explode(',' ,$strIdentificacionesExcluidas);
            }        

            if($strFechaLimActivacion != "")
            {
                $strFechaLimActivacion = date('Y-m-d', strtotime($strFechaLimActivacion));
            }

            $arrayParametros    = array("strDatabaseDsn"        => $this->container->getParameter('database_dsn'),
                                        "strUserComercial"      => $this->container->getParameter('user_comercial'),
                                        "strPasswordComercial"  => $this->container->getParameter('passwd_comercial'),
                                        "strCodEmpresa"         => $strCodEmpresa,
                                        "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                        "arrayParamsBusqueda"   => array(
                                                                            "strCodEmpresa"             => $strCodEmpresa,
                                                                            "strFechaCreacionDoc"       => $strFechaCreacionDocBusqueda,
                                                                            "strTiposDocumentos"        => $strTiposDocumentosBusqueda,
                                                                            "strNumDocsAbiertos"        => $intNumDocsAbiertosBusqueda,
                                                                            "strValorMontoCartera"      => $strValorMontoCarteraBusqueda,
                                                                            "strIdTipoNegocio"          => $intIdTipoNegocioBusqueda,
                                                                            "strValorClienteCanal"      => $strValorClienteCanalBusqueda,
                                                                            "strNombreUltimaMilla"      => $strNombreUltimaMillaBusqueda,
                                                                            "strIdCicloFacturacion"     => $intIdCicloFacturacionBusqueda,
                                                                            "strIdsOficinas"            => $strIdsOficinasBusqueda,
                                                                            "strIdsFormasPago"          => $strIdsFormasPagoBusqueda,
                                                                            "strValorCuentaTarjeta"     => $strValorCuentaTarjetaBusqueda,
                                                                            "strIdsTiposCuentaTarjeta"  => $strIdsTiposCuentaTarjetaBusqueda,
                                                                            "strIdsBancos"              => $strIdsBancosBusqueda,
                                                                            "strFechaLimActivacion"     => $strFechaLimActivacion,
                                                                            "arrayFinalIdExcluidas"     => $arrayFinalIdExcluidas
                                                                            )
                                        );
            $strJsonResponse    = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getJsonResumenCorteMasivo($arrayParametros);
        }
        else
        {
            $strJsonResponse    = json_encode(array('status'            => "OK",
                                                    'mensaje'           => "",
                                                    'arrayResultado'    => array()));
        }
        $objJsonResponse->setContent($strJsonResponse);
        return $objJsonResponse;
    }
    
    /**
     * Documentación para el método 'exportarCsvClientesCorteMasivoAction'
     *
     * Método para generar el archivo CSV con los clientes que se van a cortar
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 15-08-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-09-2021 Se agregan el envío de los parámetros de la fecha de creación del documento y los tipos de documentos a la acción
     *                         de exportar la consulta de servicios a cortar
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.2 14-09-2022 Se agregan al envío de los parámetros la fecha de activación del servicio, nombre de archivo adjunto y
     *                         arreglo de identificacion excluidas.
     */
    public function exportarCsvClientesCorteMasivoAction() 
    {
        $emComercial                        = $this->getDoctrine()->getManager('telconet');
        $objJsonResponse                    = new JsonResponse();
        $objLzString                        = new LZString();
        $objRequest                         = $this->getRequest();
        $objSession                         = $objRequest->getSession();
        $strCodEmpresa                      = $objSession->get('idEmpresa');
        $strUsrConsulta                     = $objSession->get('user');
        $strFechaCreacionDocExportar        = $objRequest->get('fechaCreacionDocExportar');
        $strTiposDocumentosExportar         = $objRequest->get('tiposDocumentosExportar');
        $intNumDocsAbiertosExportar         = $objRequest->get('numDocsAbiertosExportar');
        $strValorMontoCarteraExportar       = $objRequest->get('valorMontoCarteraExportar');
        $intIdTipoNegocioExportar           = $objRequest->get('idTipoNegocioExportar');
        $strValorClienteCanalExportar       = $objRequest->get('valorClienteCanalExportar') ? $objRequest->get('valorClienteCanalExportar') : 'Todos';
        $strNombreUltimaMillaExportar       = $objRequest->get('nombreUltimaMillaExportar');
        $intIdCicloFacturacionExportar      = $objRequest->get('idCicloFacturacionExportar');
        $strIdsOficinasExportar             = $objRequest->get('idsOficinasExportar');
        $strIdsFormasPagoExportar           = $objRequest->get('idsFormasPagoExportar');
        $strValorCuentaTarjetaExportar      = $objRequest->get('valorCuentaTarjetaExportar');
        $strIdsTiposCuentaTarjetaExportar   = $objRequest->get('idsTiposCuentaTarjetaExportar');
        $strIdsBancosExportar               = $objRequest->get('idsBancosExportar');
        $strFechaLimActivacion              = $objRequest->get('fechaLimActivacion');
        $strNombreArchivoAdjunto            = $objRequest->get('nombreArchivoAdjunto');
        $strProceso                         = 'EXPORTACION';
        $strIdentificacionesExcluidas       = $objRequest->get('identificacionesExcluidas');
        $strIdentificacionesExcluidas       = $objLzString->decompressFromBase64($strIdentificacionesExcluidas);

        if(isset($strFechaCreacionDocExportar) && !empty($strFechaCreacionDocExportar))
        {
            $arrayFechaCreacionDocExportar = explode('T', $strFechaCreacionDocExportar);
            if(isset($arrayFechaCreacionDocExportar) && !empty($arrayFechaCreacionDocExportar))
            {
                $strFechaCreacionDocExportar = $arrayFechaCreacionDocExportar[0];
            }
        }
        if(isset($strTiposDocumentosExportar) && !empty($strTiposDocumentosExportar))
        {
            $arrayTiposDocumentosExportar = array_unique(explode(',', $strTiposDocumentosExportar));
            if(isset($arrayTiposDocumentosExportar) && !empty($arrayTiposDocumentosExportar))
            {
                $strTiposDocumentosExportar = implode(",", $arrayTiposDocumentosExportar);
            }
        }

        $arrayFinalIdExcluidas = array();

        if($strIdentificacionesExcluidas != "")
        {
            $arrayIdentificacionesExcluidas = json_decode($strIdentificacionesExcluidas);
            $strIdentificacionesExcluidas = "";

            foreach($arrayIdentificacionesExcluidas as $id) 
            {
                if(is_null($id->IDENTIFICACION))
                {
                    $strJsonResponse    = json_encode(array('status' => "FORMAT_ERROR",
                                                            'mensaje' => "Documento con formato Incorrecto, favor revisar."));
    
                    $objJsonResponse->setContent($strJsonResponse);
                    return $objJsonResponse;
                }
                else
                {
                    $strIdentificacionesExcluidas = $strIdentificacionesExcluidas . $id->IDENTIFICACION .",";
                }
            }

            $strIdentificacionesExcluidas = strtoupper(substr($strIdentificacionesExcluidas, 0, -1));
            $arrayFinalIdExcluidas = explode(',' ,$strIdentificacionesExcluidas);
        }        

        if($strFechaLimActivacion != "")
        {
            $strFechaLimActivacion = date('Y-m-d', strtotime($strFechaLimActivacion));
        }
        
        $arrayParametros                    = array("strDatabaseDsn"                => $this->container->getParameter('database_dsn'),
                                                    "strUserInfraestructura"        => $this->container->getParameter('user_infraestructura'),
                                                    "strPasswordInfraestructura"    => $this->container->getParameter('passwd_infraestructura'),
                                                    "strUsrConsulta"                => $strUsrConsulta,
                                                    "arrayParamsExportar"           =>  array(
                                                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                                                        "strFechaCreacionDoc"       => $strFechaCreacionDocExportar,
                                                                                        "strTiposDocumentos"        => $strTiposDocumentosExportar,
                                                                                        "strNumDocsAbiertos"        => $intNumDocsAbiertosExportar,
                                                                                        "strValorMontoCartera"      => $strValorMontoCarteraExportar,
                                                                                        "strIdTipoNegocio"          => $intIdTipoNegocioExportar,
                                                                                        "strValorClienteCanal"      => $strValorClienteCanalExportar,
                                                                                        "strNombreUltimaMilla"      => $strNombreUltimaMillaExportar,
                                                                                        "strIdCicloFacturacion"     => $intIdCicloFacturacionExportar,
                                                                                        "strIdsOficinas"            => $strIdsOficinasExportar,
                                                                                        "strIdsFormasPago"          => $strIdsFormasPagoExportar,
                                                                                        "strValorCuentaTarjeta"     => $strValorCuentaTarjetaExportar,
                                                                                        "strIdsTiposCuentaTarjeta"  => 
                                                                                        $strIdsTiposCuentaTarjetaExportar,
                                                                                        "strIdsBancos"              => $strIdsBancosExportar,
                                                                                        "arrayFinalIdExcluidas"     => $arrayFinalIdExcluidas,
                                                                                        "strFechaLimActivacion"     => $strFechaLimActivacion,
                                                                                        "strNombreArchivoAdjunto"   => $strNombreArchivoAdjunto,
                                                                                        "strProceso"                => $strProceso
                                                                                        )
                                                    );
        $strJsonResponse = $emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getJsonRespuestaExportarCsvCorteMasivo($arrayParametros);
        $objJsonResponse->setContent($strJsonResponse);
        return $objJsonResponse;
    }
    
    /**
     * Documentación para el método 'descargarCsvProcesoMasivoAction'
     *
     * Método para descargar el archivo CSV con los clientes que se van a cortar masivamente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 15-08-2020
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 14-06-2021 Se modifica la programación para descargar el archivo CSV de un proceso masivo debido a que dichos documentos 
     *                         se guardarán en el NFS
     * 
     */
    public function descargarCsvProcesoMasivoAction($intIdDocumento)
    {
        $emComunicacion             = $this->getDoctrine()->getManager("telconet_comunicacion");
        $objDocProcesoMasivo        = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($intIdDocumento);
        $strNombreCsvProcesoMasivo  = $objDocProcesoMasivo->getUbicacionLogicaDocumento();
        $strUrlCsvProcesoMasivo     = $objDocProcesoMasivo->getUbicacionFisicaDocumento();
        $strContenidoArchivo        = file_get_contents($strUrlCsvProcesoMasivo);
        $objResponse                = new Response();
        $objResponse->headers->set('Content-Type', 'mime/type');
        $objResponse->headers->set('Content-Disposition', 'attachment;filename="' . $strNombreCsvProcesoMasivo );
        $objResponse->setContent($strContenidoArchivo);
        return $objResponse;
    }

    public function getOficinaGrupoConFormaPagoAction() {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');
        $respuestaFormaPago = new Response();
        $respuestaFormaPago->headers->set('Content-Type', 'text/json');
        $peticion = $this->get('request');
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');
        $start = $peticion->query->get('start');
        $limit = $peticion->query->get('limit');
        $objJson = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoOficinaGrupo')->generarJsonOficinaGrupoPorEmpresa($idEmpresa, "Activo", $start, 100);
        $objJsonFor = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:AdmiFormaPago')->generarJsonFormaGeneral("Activo", $start, 100, "", "", "S");
        return $respuesta->setContent($objJson . "&" . $objJsonFor);
    }

    public function cortarClientesMasivoAction() {
        $em = $this->get('doctrine')->getManager('telconet');
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/plain');
        // ...
        $peticion = $this->get('request');
        $session = $peticion->getSession();
        $clientIp = $peticion->getClientIp();
        // ...
        $idEmpresa = $session->get('idEmpresa');
        $prefijoEmpresaSesion = $session->get('prefijoEmpresa');
        $usrCreacion = $session->get('user');
        // ...
        // $numFacturasAbiertas = $peticion->get('facturas');
        $numFacturasAbiertas = $peticion->get('numFacturasAbiertas');
        $fechaEmisionFactura = $peticion->get('fechaEmisionFactura');
        $valorMontoDeuda = $peticion->get('valorMontoDeuda');
        $idFormaPago = $peticion->get('idFormaPago');
        $idsBancosTarjetas = $peticion->get('idsBancosTarjetas');
        $idsOficinas = $peticion->get('idsOficinas');
        // ...
        $idsPuntos = $peticion->get('idsPuntos');
        $cantidadPuntos = $peticion->get('cantidadPuntos');
        // ...
        // VERIFCAR POR EMPRESA REALIZAR ALGUNA ACCION
        //
       
        /*
        * <javera@telconet.ec> - 18/09/2014
        * Se segmentan los puntos por tipo de ultima milla
        * Fibra Optica : Ejecucion VIRGO (MD)
        * Cobre/Radio : Ejecucion Corte Masivo en Telcos (TTCO)
        */
        //Se instancia el Service
        /* @var $serviceProcesoMasivo \telconet\tecnicoBundle\Service\ProcesoMasivoService */
        $serviceProcesoMasivo = $this->get('tecnico.ProcesoMasivo');
        $arrayPuntossPorUltimaMilla = $serviceProcesoMasivo->obtenerPuntosPorUltimaMilla($idsPuntos);

        $strIdsPuntosFO = $arrayPuntossPorUltimaMilla['FO']; //Puntos con Fibra Optica
        $strIdsPuntosCR = $arrayPuntossPorUltimaMilla['CR']; //Puntos con Radio/Cobre

        $intTotalFO = $arrayPuntossPorUltimaMilla['totalFO']; //Total de puntos con Fibra a cortar
        $intTotalCoRa = $arrayPuntossPorUltimaMilla['totalCoRa']; //Total de puntos con Cobre/radio a cortar
        
        if($strIdsPuntosCR != '' && $prefijoEmpresaSesion != "EN") //Radio/Cobre
        {
            $fecha = date("Y-m-d");
            $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_corteMasivo.jar '" . $strIdsPuntosCR . "' "
                . " '" . $valorMontoDeuda . "|" . $numFacturasAbiertas . "|" . $idsOficinas . "|" . $idFormaPago . "' "
                . " '" . $session->get('user') . "' '" . $peticion->getClientIp() . "' "
                . " >> /home/telcos/src/telconet/tecnicoBundle/batch/corteMasivo-$fecha.txt &";

            shell_exec($comando);
            
            //realizo validacion para obtener la empresa para el flujo
            $parametros = $em->getRepository('schemaBundle:AdmiParametroDet')
                             ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'CO', "", "");
            if($parametros)
            {
                $prefijoEmpresa = $parametros['valor3'];
                $objEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                $idEmpresa = $objEmpresa->getId(); 
            }
            
            // Se setea TTCO ya que esto serán ingresado como puntos con Cobre/Radio
            // y ejecutará el script respectivo para el corte
            $serviceProcesoMasivo->guardarPuntosPorCorteMasivo($prefijoEmpresa, $idEmpresa, $numFacturasAbiertas, $fechaEmisionFactura, 
                                                               $valorMontoDeuda, $idFormaPago, $idsBancosTarjetas, $idsOficinas, $strIdsPuntosCR, 
                                                               $intTotalCoRa, $usrCreacion, $clientIp);
        }

        if($strIdsPuntosFO != '') //Fibra Optica
        {   
            if ($prefijoEmpresaSesion != "EN")
            {
                //realizo validacion para obtener la empresa para el flujo
                $parametros = $em->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, "FO", "", "");
                if($parametros)
                {                   
                        $prefijoEmpresa = $parametros['valor3'];
                        $objEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                        $idEmpresa = $objEmpresa->getId();     
                }
           }
           else
            {     
                        $prefijoEmpresa =  $prefijoEmpresaSesion;                   
            }
            // Se setea MD ya que esto serán registrado como puntos con Fibra Optica
            // luego serán ejecutados en el servidor VIRGO
            $serviceProcesoMasivo->guardarPuntosPorCorteMasivo($prefijoEmpresa, $idEmpresa, $numFacturasAbiertas, $fechaEmisionFactura, 
                                                               $valorMontoDeuda, $idFormaPago, $idsBancosTarjetas, $idsOficinas, $strIdsPuntosFO,   
                                                               $intTotalFO, $usrCreacion, $clientIp);
        }

        return $respuesta->setContent("OK");
    }
    
    /**
     * 
     * @Secure(roles="ROLE_151-6438")
     * 
     * Documentación para el método 'generarCorteReactivacionTelcoHomeAction'.
     *
     * Guarda los registros con la información de los servicios TelcoHome que se cortarán o reactivarán de manera masiva
     *
     * @return Response Lista de Puntos del cliente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-03-2019
     * 
     */
    public function generarCorteReactivacionTelcoHomeAction() 
    {
        $objResponse                = new JsonResponse();
        $objRequest                 = $this->getRequest();
        $objSesion                  = $objRequest->getSession();
        
        $strEstadoActualServicio    = $objRequest->get('estadoActualServicio');
        $intTotalServicios          = $objRequest->get('totalServicios');
        $strIpCreacion              = $objRequest->getClientIp();
        $strCodEmpresa              = $objSesion->get('idEmpresa');
        $strUsrCreacion             = $objSesion->get('user');
        $serviceProcesoMasivo       = $this->get('tecnico.ProcesoMasivo');
        $emGeneral                  = $this->get('doctrine')->getManager('telconet');
        $strMensaje                 = "";
        $strTipoProceso             = "";
        if(isset($strEstadoActualServicio) && !empty($strEstadoActualServicio))
        {
            $arrayAccionProceso = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')->getOne(   "PROCESOS_MASIVOS_TELCOHOME", 
                                                                                                        "", 
                                                                                                        "", 
                                                                                                        "", 
                                                                                                        "", 
                                                                                                        "",
                                                                                                        $strEstadoActualServicio,
                                                                                                        "",
                                                                                                        "",
                                                                                                   $objSesion->get('idEmpresa'));
            if(isset($arrayAccionProceso['valor1']) && !empty($arrayAccionProceso['valor1']))
            {
                $strTipoProceso = $arrayAccionProceso['valor1'];
            }
            
            if(empty($strTipoProceso))
            {
                $strMensaje = "No se ha podido obtener el proceso a ejecutar";
            }
        }
        else
        {
            $strMensaje = "No se ha podido obtener la acción a ejecutar";
        }
        
        if(empty($strMensaje))
        {
            $arrayParametros    = array('strServicios'      => $objRequest->get('servicios'),
                                        'strTipoProceso'    => $strTipoProceso,
                                        'intTotalServicios' => $intTotalServicios,
                                        'strIpCreacion'     => $strIpCreacion,
                                        'strCodEmpresa'     => $strCodEmpresa,
                                        'strUsrCreacion'    => $strUsrCreacion);
            $strMensaje         = $serviceProcesoMasivo->guardarServiciosCorteReactivacionTelcoHome($arrayParametros);
        }
        return $objResponse->setContent($strMensaje);
    }
    
    
    
    
    function getTipoNegocioAction() {
        $em = $this->get('doctrine')->getManager('telconet');
        $datos = $em->getRepository('schemaBundle:AdmiTipoNegocio')->findBy(array(
                        'estado' => 'Activo'
        ));
        // print_r($datos); die();
        foreach ($datos as $dato) {
            $arreglo[] = array(
                            'idTipoNegocio' => $dato->getId(),
                            'nombreTipoNegocio' => $dato->getNombreTipoNegocio()
            );
        }
        if (!empty($arreglo)) {
            $total = count($arreglo);
            $response = new Response(json_encode(array(
                            'total' => $total,
                            'registros' => $arreglo
            )));
        } else {
            $arreglo[] = array();
            $response = new Response(json_encode(array(
                            'total' => 0,
                            'registros' => $arreglo
            )));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    function getServiciosXPadreFacturacionAction() {
        $em = $this->get('doctrine')->getManager('telconet');
        $peticion = $this->get('request');
        // $intPadreFact = $peticion->query->get('idPuntoFacturacion');
        $intPadreFact = $peticion->get('idPuntoFacturacion');
        // echo($intPadreFact); die();
        // $objInfoPunto=$em->getRepository('schemaBundle:InfoPunto')->find($intPadreFact);
        $servicios = $em->getRepository('schemaBundle:InfoServicio')->getServiciosXpadreFacturacion($intPadreFact, "Activo");
        $datos = $servicios['registros'];
        $total = $servicios['total'];
        foreach ($datos as $dato) {
            $cliente = "";
            if ($dato['nombreCliente'] == "") {
                $cliente = $dato['razonSocial'];
            } else {
                $cliente = $dato['nombreCliente'];
            }
            $arreglo[] = array(
                            'idPuntoFacturacion' => $intPadreFact,
                            'idServicio' => $dato['idServicio'],
                            'puntoId' => $dato['puntoId'],
                            'login' => $dato['login'],
                            'cliente' => $cliente,
                            'planId' => $dato['planId'],
                            'nombrePlan' => $dato['nombrePlan'],
                            'estado' => $dato['estado']
            );
        }
        if (!empty($arreglo)) {
            $total = count($arreglo);
            $response = new Response(json_encode(array(
                            'total' => $total,
                            'registros' => $arreglo
            )));
        } else {
            $arreglo[] = array();
            $response = new Response(json_encode(array(
                            'total' => 0,
                            'registros' => $arreglo
            )));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    /**
     * Documentación para el método 'getBancosTarjetasAction'.
     *
     * Retorna la lista de bancos que son considerados en la ventana de envío de notificaciones
     *
     * @return $objResponse Listado de Bancos
     *
     * @version 1.0 - Desarrollo Inicial
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-08-2020 - Se agrega validación para restringir los bancos parametrizados 
     * 
     * agrega condición para descriminar una n cantidad de Bancos en la ventana de corte masivo y realizar validacion
     *                           por idPais
     */
    function getBancosTarjetasAction() {
        $em = $this->get('doctrine')->getManager('telconet');
        $peticion = $this->get('request');
        // ...
        $idFormaPago = $peticion->get('idFormaPagoSelected');
        /* @var $formaPago \telconet\schemaBundle\Entity\AdmiFormaPago */
        // $formaPago = $em->getRepository('schemaBundle:AdmiFormaPago')->findOneById($idFormaPago);
        // $datos;
        if ($idFormaPago == 3) {
            $datos = $em->getRepository('schemaBundle:AdmiBanco')->findAll();
            $strCodEmpresa                  = $this->getRequest()->getSession()->get('idEmpresa');
            $arrayBancosNoPermitidos        = array();
            $arrayBancosNoPermitidosParams  = $this->get('doctrine')->getManager('telconet_general')
                                                                    ->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                          '', 
                                                                          '', 
                                                                          '',
                                                                          'CORTE_MASIVO',
                                                                          'BANCOS_NO_PERMITIDOS',
                                                                          '',
                                                                          '',
                                                                          '',
                                                                          $strCodEmpresa);
            if(is_array($arrayBancosNoPermitidosParams) && count($arrayBancosNoPermitidosParams) > 0)
            {
                foreach($arrayBancosNoPermitidosParams as $arrayBancoNoPermitidoParam)
                {   
                    $arrayBancosNoPermitidos[] = $arrayBancoNoPermitidoParam['valor4'];
                }
            }
            
            /* @var $dato \telconet\schemaBundle\Entity\AdmiBanco */
            foreach ($datos as $dato) {
                if ($dato->getDescripcionBanco() != "TARJETAS" && (!in_array($dato->getDescripcionBanco(), $arrayBancosNoPermitidos)))
                {
                    $arreglo[] = array(
                                    'id' => $dato->getId(),
                                    'nombre' => $dato->getDescripcionBanco()
                    );
                }
            }
        }
        if ($idFormaPago == 10) {
            $datos = $em->getRepository('schemaBundle:AdmiTipoCuenta')->findAll();
            /* @var $dato \telconet\schemaBundle\Entity\AdmiTipoCuenta */
            foreach ($datos as $dato) {
                if ($dato->getEsTarjeta() == "S") {
                    $arreglo[] = array(
                                    'id' => $dato->getId(),
                                    'nombre' => $dato->getDescripcionCuenta()
                    );
                }
            }
        }
        if (!empty($arreglo)) {
            $total = count($arreglo);
            $response = new Response(json_encode(array(
                            'total' => $total,
                            'registros' => $arreglo
            )));
        } else {
            $arreglo[] = array();
            $response = new Response(json_encode(array(
                            'total' => 0,
                            'registros' => $arreglo
            )));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }

    public function getTipoNegocioPorEmpresaAction() {
//         $response = new Response();
//         $response->headers->set('Content-Type', 'text/json');

        $peticion = $this->get('request');
        $request = $this->getRequest();
        $session = $request->getSession();
        $idEmpresa = $session->get('idEmpresa');

        
        $em = $this->getDoctrine()->getManager("telconet");
        /* @var $repository \telconet\schemaBundle\Repository\AdmiTipoNegocioRepository */
        $repository = $em->getRepository('schemaBundle:AdmiTipoNegocio');
        
        
        $datos = $repository->findTiposNegocioActivosPorEmpresa($idEmpresa)->getQuery()->getResult();
        /* @var $dato \telconet\schemaBundle\Entity\AdmiTipoNegocio */
         foreach ($datos as $dato) {
                
                    $arreglo[] = array(
                                    'idTipoNegocio' => $dato->getId(),
                                    'nombreTipoNegocio' => $dato->getNombreTipoNegocio()
                    );
                
            }
            if (!empty($arreglo)) {
                $total = count($arreglo);
                $response = new Response(json_encode(array(
                                'total' => $total,
                                'registros' => $arreglo
                )));
            } else {
                $arreglo[] = array();
                $response = new Response(json_encode(array(
                                'total' => 0,
                                'registros' => $arreglo
                )));
            }
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }
    
/**
 * getUltimaMillaAction
 *
 * funcion que retorna los tipo de medio que se encuentran en la entidad AdmiTipoMedio para combo box
 * 
 * @author John Vera <javera@telconet.ec>
 * @return array con los tipos de medios
 * 
 * @author Ricardo Coello Quezada <rcoello@telconet.ec>
 * @version 1.0 07-02-2018 - Se valida que no se muestre UM FTTX para MD.
 */
    //funcion que retorna los tipo de medio
    function getUltimaMillaAction() {
        $arreglo = '';
        $em = $this->get('doctrine')->getManager('telconet');
        $objTipoMedio = $em->getRepository('schemaBundle:AdmiTipoMedio')->findBy(array(
                        'estado' => 'Activo' ));
                
        foreach ($objTipoMedio as $registro) {
            
            if($registro->getCodigoTipoMedio() !== 'FTTx')
            {
                $arreglo[] = array(
                            'idUltimaMilla' => $registro->getId(),
                            'codigoUltimaMilla' => $registro->getCodigoTipoMedio(),
                            'nombreUltimaMilla' => $registro->getNombreTipoMedio()
                );  
            }
        }
        
        if (!empty($arreglo)) {
            $total = count($arreglo);
            $response = new Response(json_encode(array(
                            'total' => $total,
                            'registros' => $arreglo
            )));
        } else {
            $arreglo[] = array();
            $response = new Response(json_encode(array(
                            'total' => 0,
                            'registros' => $arreglo
            )));
        }
        $response->headers->set('Content-type', 'text/json');
        return $response;
    }
    
/*
 * getCiclosAction
 *
 * Funcion que retorna los ciclos de facturacion Activos e Inactivos.
 *
 * @author Jorge Guerrero <jguerrerop@telconet.ec>
 * @since 1.0 Versión Inicial
 * 
 * @author  Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 19-06-2018 - Se agrega envío de  id de la empresa en sesión en la consulta de ciclos de facturación. 
 * 
 * @return array con los ciclos Activos e Inactivos
 */
    //funcion que retorna los tipo de medio
    public function getCiclosAction()
    {
        $arrayCiclos    = '';
        $objRespuesta   = new JsonResponse();
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strEmpresaCod  = $objSession->get('idEmpresa');        
        $emFinanciero   = $this->get('doctrine')->getManager('telconet_financiero');
        $objCiclos      = $emFinanciero->getRepository('schemaBundle:AdmiCiclo')->findBy(array(
                          'estado' => array('Activo','Inactivo'),'empresaCod' => $strEmpresaCod));

        foreach ($objCiclos as $objCiclo)
        {
            $arrayCiclos[] = array(
                            'intIdCiclo' => $objCiclo->getId(),
                            'strNombreCiclo' => $objCiclo->getNombreCiclo()
            );
        }

        if (!empty($arrayCiclos)) 
        {
            $intTotal = count($arrayCiclos);
            $objRespuesta ->setData(array(
                            'intTotal' => $intTotal,
                            'arrayRegistros' => $arrayCiclos
            ));
        }
        else
        {
            $arrayCiclos[] = array();
            $intTotal = 0;
            $objRespuesta ->setData(array(
                            'intTotal' => 0,
                            'arrayRegistros' => $arrayCiclos
            ));
        }

        return $objRespuesta;
    }
    
    /**
     * Documentación para el método 'getParametrosAsociadosAServiciosAction'.
     *
     * Retorna la lista de valores parametrizados que están asociados a los servicios
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-09-2021
     * 
     * @return JsonResponse $objJsonResponse
     * 
     */
    public function getParametrosAsociadosAServiciosCorteAction()
    {
        $emGeneral          = $this->getDoctrine()->getManager("telconet_general");
        $objJsonResponse    = new JsonResponse();
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strValor2Param     = $objRequest->get('valor2Param');
        
        $intTotalDataInformacionParametros  = 0;
        $arrayDataInformacionParametros     = array();
        $arrayInformacionParametros         = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get(  'PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoEmpresa, 
                                                                '', 
                                                                '', 
                                                                '',
                                                                'CORTE_MASIVO',
                                                                $strValor2Param,
                                                                '',
                                                                '',
                                                                '',
                                                                $strCodEmpresa);
        
        if(isset($arrayInformacionParametros) && !empty($arrayInformacionParametros))
        {
            foreach($arrayInformacionParametros as $arrayInformacionParametro)
            {
                $intTotalDataInformacionParametros++;
                $arrayDataInformacionParametros[]   = array('valor3Param'   => $arrayInformacionParametro['valor3'],
                                                            'valor4Param'   => $arrayInformacionParametro['valor4'],
                                                            'valor5Param'   => $arrayInformacionParametro['valor4']);                          
            }
        }
        $arrayResultado     = array("total"         => $intTotalDataInformacionParametros,
                                    "encontrados"   => $arrayDataInformacionParametros);
        $objJsonResponse->setData($arrayResultado);
        return $objJsonResponse;
    }
    
    
    /**
     * Documentación para el método 'cortarClientesMasivoPorLotesAction'.
     *
     * Retorna la lista de valores parametrizados que están asociados a los servicios
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-09-2021
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.1 15-11-2022 - Se agrega mas datos al arrayParametros para mejora de Filtros en busqueda de puntos para corte masivo
     *                             (fechaLimActivacion, identificacionesExcluidas y bandera de Exportacion/CorteMasivoPorLote)
     * 
     * @return JsonResponse $objJsonResponse
     * 
     */
    public function cortarClientesMasivoPorLotesAction()
    {
        $objJsonResponse                    = new JsonResponse();
        $objRequest                         = $this->getRequest();
        $objLzString                        = new LZString();
        $objSession                         = $objRequest->getSession();
        $strCodEmpresa                      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa                  = $objSession->get('prefijoEmpresa');
        $strUsrCreacion                     = $objSession->get('user');
        $strIpCreacion                      = $objRequest->getClientIp();
        $strFechaCreacionDocBusqueda        = $objRequest->get('fechaCreacionDocBusqueda');
        $strTiposDocumentosBusqueda         = $objRequest->get('tiposDocumentosBusqueda');
        $intNumDocsAbiertosBusqueda         = $objRequest->get('numDocsAbiertosBusqueda');
        $strValorMontoCarteraBusqueda       = $objRequest->get('valorMontoCarteraBusqueda');
        $intIdTipoNegocioBusqueda           = $objRequest->get('idTipoNegocioBusqueda');
        $strValorClienteCanalBusqueda       = $objRequest->get('valorClienteCanalBusqueda') ? $objRequest->get('valorClienteCanalBusqueda') : 'Todos';
        $strNombreUltimaMillaBusqueda       = $objRequest->get('nombreUltimaMillaBusqueda');
        $intIdCicloFacturacionBusqueda      = $objRequest->get('idCicloFacturacionBusqueda');
        $strIdsOficinasBusqueda             = $objRequest->get('idsOficinasBusqueda');
        $strIdsFormasPagoBusqueda           = $objRequest->get('idsFormasPagoBusqueda');
        $strValorCuentaTarjetaBusqueda      = $objRequest->get('valorCuentaTarjetaBusqueda');
        $strIdsTiposCuentaTarjetaBusqueda   = $objRequest->get('idsTiposCuentaTarjetaBusqueda');
        $strIdsBancosBusqueda               = $objRequest->get('idsBancosBusqueda');
        $strProceso                         = 'MASIVO';
        $strFechaLimActivacion              = $objRequest->get('fechaLimActivacion');
        $strIdentificacionesExcluidas       = $objRequest->get('identificacionesExcluidas');
        $strIdentificacionesExcluidas       = $objLzString->decompressFromBase64($strIdentificacionesExcluidas);

        if(isset($strFechaCreacionDocBusqueda) && !empty($strFechaCreacionDocBusqueda))
        {
            $arrayFechaCreacionDocBusqueda = explode('T', $strFechaCreacionDocBusqueda);
            if(isset($arrayFechaCreacionDocBusqueda) && !empty($arrayFechaCreacionDocBusqueda))
            {
                $strFechaCreacionDocBusqueda = $arrayFechaCreacionDocBusqueda[0];
            }
        }
        if(isset($strTiposDocumentosBusqueda) && !empty($strTiposDocumentosBusqueda))
        {
            $arrayTiposDocumentosBusqueda = array_unique(explode(',', $strTiposDocumentosBusqueda));
            if(isset($arrayTiposDocumentosBusqueda) && !empty($arrayTiposDocumentosBusqueda))
            {
                $strTiposDocumentosBusqueda = implode(",", $arrayTiposDocumentosBusqueda);
            }
        }

        $arrayFinalIdExcluidas = array();

        if($strIdentificacionesExcluidas != "")
        {
            $arrayIdentificacionesExcluidas = json_decode($strIdentificacionesExcluidas);
            $strIdentificacionesExcluidas = "";

            foreach($arrayIdentificacionesExcluidas as $id) 
            {
                if(is_null($id->IDENTIFICACION))
                {
                    $strJsonResponse    = json_encode(array('status' => "FORMAT_ERROR",
                                                            'mensaje' => "Documento con formato Incorrecto, favor revisar."));
    
                    $objJsonResponse->setContent($strJsonResponse);
                    return $objJsonResponse;
                }
                else
                {
                    $strIdentificacionesExcluidas = $strIdentificacionesExcluidas . $id->IDENTIFICACION .",";
                }
            }

            $strIdentificacionesExcluidas = strtoupper(substr($strIdentificacionesExcluidas, 0, -1));
            $arrayFinalIdExcluidas = explode(',' ,$strIdentificacionesExcluidas);
        }        

        if($strFechaLimActivacion != "")
        {
            $strFechaLimActivacion = date('Y-m-d', strtotime($strFechaLimActivacion));
        }

        $arrayParametros                = array("strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                "strUsrCreacion"            => $strUsrCreacion,
                                                "strIpCreacion"             => $strIpCreacion,
                                                "strCodEmpresa"             => $strCodEmpresa,
                                                "strFechaCreacionDoc"       => $strFechaCreacionDocBusqueda,
                                                "strTiposDocumentos"        => $strTiposDocumentosBusqueda,
                                                "strNumDocsAbiertos"        => $intNumDocsAbiertosBusqueda,
                                                "strValorMontoCartera"      => $strValorMontoCarteraBusqueda,
                                                "strIdTipoNegocio"          => $intIdTipoNegocioBusqueda,
                                                "strValorClienteCanal"      => $strValorClienteCanalBusqueda,
                                                "strNombreUltimaMilla"      => $strNombreUltimaMillaBusqueda,
                                                "strIdCicloFacturacion"     => $intIdCicloFacturacionBusqueda,
                                                "strIdsOficinas"            => $strIdsOficinasBusqueda,
                                                "strIdsFormasPago"          => $strIdsFormasPagoBusqueda,
                                                "strValorCuentaTarjeta"     => $strValorCuentaTarjetaBusqueda,
                                                "strIdsTiposCuentaTarjeta"  => $strIdsTiposCuentaTarjetaBusqueda,
                                                "strIdsBancos"              => $strIdsBancosBusqueda,
                                                "strFechaLimActivacion"     => $strFechaLimActivacion,
                                                "arrayFinalIdExcluidas"     => $arrayFinalIdExcluidas,
                                                "strProceso"                => $strProceso
                                            );
        $serviceProcesoMasivo               = $this->get('tecnico.ProcesoMasivo');
        $arrayRespuestaCorteMasivoPorLotes  = $serviceProcesoMasivo->cortarClientesMasivoPorLotes($arrayParametros);
        $strStatusRespuestaCorteMasivoPorLotes  = $arrayRespuestaCorteMasivoPorLotes['status'];
        if($strStatusRespuestaCorteMasivoPorLotes == "OK")
        {
            $intIdSolCortePorLotes = $arrayRespuestaCorteMasivoPorLotes["intIdSolCortePorLotes"];
            $serviceProcesoMasivo->ejecutaCorteClientesMasivoCoRad(array(   "intIdSolCortePorLotes" => $intIdSolCortePorLotes,
                                                                            "strNumDocsAbiertos"    => $arrayParametros["strNumDocsAbiertos"],
                                                                            "strValorMontoCartera"  => $arrayParametros["strValorMontoCartera"],
                                                                            "strIdsOficinas"        => $arrayParametros["strIdsOficinas"],
                                                                            "strIdsFormasPago"      => $arrayParametros["strIdsFormasPago"],
                                                                            "strUsrCreacion"        => $arrayParametros["strUsrCreacion"],
                                                                            "strIpCreacion"         => $arrayParametros["strIpCreacion"]
                                                                    ));
        }
        $objJsonResponse->setData($arrayRespuestaCorteMasivoPorLotes);
        return $objJsonResponse;
    }
}
