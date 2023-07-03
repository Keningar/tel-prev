<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * Clase para invocar a métodos para la activación, supensión, reactivación y cancelación de licencias Kaspersky.
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 25-07-2019
 */
class InternetProtegidoService
{
    private $objContainer;
    private $emComercial;
    private $emGeneral;
    private $emSeguridad;
    private $serviceUtil;
    private $serviceEnvioPlantilla;
    private $serviceLicenciasKaspersky;
    private $serviceRestClient;
    private $serviceActivarPuerto;
    private $serviceCortarServicio;
    private $serviceReconectarServicio;
    private $serviceCancelarServicio;
    private $serviceLicenciasMcAfee;
    public static $strOpcionMasivo = "MASIVO";
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer                 = $objContainer;
        $this->emComercial                  = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral                    = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->emSeguridad                  = $objContainer->get('doctrine')->getManager('telconet_seguridad');
        $this->serviceUtil                  = $objContainer->get('schema.Util');
        $this->serviceEnvioPlantilla        = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceRestClient            = $objContainer->get('schema.RestClient');
        $this->serviceLicenciasKaspersky    = $objContainer->get('tecnico.LicenciasKaspersky');
        $this->serviceLicenciasMcAfee       = $objContainer->get('tecnico.LicenciasMcAfee');
        $this->serviceActivarPuerto         = $objContainer->get('tecnico.InfoActivarPuerto');
        $this->serviceCortarServicio        = $objContainer->get('tecnico.InfoCortarServicio');
        $this->serviceReconectarServicio    = $objContainer->get('tecnico.InfoReconectarServicio');
        $this->serviceCancelarServicio      = $objContainer->get('tecnico.InfoCancelarServicio');
    }
    
    /**
     * Función que realiza el corte de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     * @param array $arrayParametrosWs [
     *                                      "op"            => Opción que fue ejecutada desde el web service,
     *                                      "token"         => Token enviado al web service,
     *                                      "user"          => Usuario usado en la generación de tokens,
     *                                      "usrCreacion"   => Usuario de creación,
     *                                      "source"        => Arreglo con los parámetros necesarios para generar un token,
     *                                      "ipCreacion"    => Ip de creación,
     *                                      "data"          => [
     *                                                          "idPunto"               => Id del punto,
     *                                                          "idServicioInternet"    => Id del servicio de Internet,
     *                                                          "servicios"             => Arreglo con los servicios con tecnología Kaspersky,
     *                                                          "tipoProceso"           => "CORTE MASIVO",
     *                                                          "codEmpresa"            => Id de la empresa
     *                                                         ]
     *                                  ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => Mensaje de error,
     *                                  "data"      => [
     *                                                  "status"                        => OK o ERROR del proceso por servicio,
     *                                                  "mensaje"                       => Mensaje de error del proceso por servicio,
     *                                                  "idServicio"                    => id del servicio procesado
     *                                                  "idProducto"                    => id del producto Internet Protegido asociado al servicio,
     *                                                  "tipoServicio"                  => "PLAN" o "PRODUCTO",
     *                                                  "tipoTecnologiaInicial"         => "NUEVA",
     *                                                  "permiteEjecutarProcesoLogico"  => "SI" o "NO" permite ingresar historiales del servicio
     *                                                                                     desde el proceso que lo invoca
     *                                                 ]
     *                                ]
     * 
     */
    public function cortarLicencias($arrayParametrosWs)
    {
        $strUsrCreacion             = $arrayParametrosWs['usrCreacion'] ? $arrayParametrosWs['usrCreacion'] : "procesosmasivos";
        $strIpCreacion              = $arrayParametrosWs['ipCreacion'] ? $arrayParametrosWs['ipCreacion'] : "127.0.0.1";
        $strMensaje                 = "";
        $arrayRespuestaServicios    = array();
        try
        {
            if(!empty($arrayParametrosWs['data']['servicios']) && !empty($arrayParametrosWs['data']['tipoProceso'])
                && !empty($arrayParametrosWs['data']['idPunto']) && !empty($arrayParametrosWs['data']['codEmpresa']))
            {
                $arrayServiciosWs           = $arrayParametrosWs['data']['servicios'];
                $strCodEmpresa              = $arrayParametrosWs['data']['codEmpresa'];
                $objProductoIPMP            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findOneByDescripcionProducto("I. PROTEGIDO MULTI PAID");
                $arrayParametroDetAntivirus = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                              ->getOne( 'ANTIVIRUS_PLANES_Y_PRODS_MD',
                                                                        '',
                                                                        '', 
                                                                        '', 
                                                                        static::$strOpcionMasivo,
                                                                        '',
                                                                        '', 
                                                                        '',
                                                                        '',
                                                                        $strCodEmpresa);
                if(isset($arrayParametroDetAntivirus["valor2"]) && !empty($arrayParametroDetAntivirus["valor2"]) && is_object($objProductoIPMP))
                {
                    $strMsjHistoServicioAdicional   = "Se cortó el servicio ".$objProductoIPMP->getDescripcionProducto().
                                                      " con tecnología ".$arrayParametroDetAntivirus["valor2"];
                }
                else
                {
                    $strMsjHistoServicioAdicional   = "";
                }
            
                foreach($arrayServiciosWs as $arrayServicioWs)
                {
                    $intIdServicio      = $arrayServicioWs["idServicio"];
                    $intIdProdServicio  = $arrayServicioWs["idProducto"];
                    $strTipoServicio    = $arrayServicioWs["tipoServicio"];
                    $strMostrarError    = "NO";
                    $strMensajeServicio = "";
                    try
                    {
                        $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                        if(is_object($objServicio))
                        {
                            if(is_object($objServicio->getPlanId()))
                            {
                                $arrayParamsLicencias           = array("strProceso"                => "CORTE_ANTIVIRUS",
                                                                        "strEscenario"              => "CORTE_PROD_EN_PLAN",
                                                                        "objServicio"               => $objServicio,
                                                                        "objPunto"                  => $objServicio->getPuntoId(),
                                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                                        "objProductoIPMP"           => $objProductoIPMP,
                                                                        "strUsrCreacion"            => $strUsrCreacion,
                                                                        "strIpCreacion"             => $strIpCreacion,
                                                                        "strEstadoServicioInicial"  => $objServicio->getEstado()
                                                                        );
                                $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                                $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                                $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                                $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                                if($strStatusGestionLicencias === "ERROR")
                                {
                                    $strMostrarError = "SI";
                                    throw new \Exception($strMensajeGestionLicencias);
                                }
                                else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] !== "OK")
                                {
                                    $strMostrarError = "SI";
                                    throw new \Exception($arrayRespuestaWs["mensaje"]);
                                }
                                $strStatusServicio = "OK";
                            }
                            else if(is_object($objServicio->getProductoId()))
                            {
                                $arrayRespuestaAdicional    = $this->serviceCortarServicio
                                                                   ->cortarServiciosOtros(array("idServicio"        => $intIdServicio,
                                                                                                "usrCreacion"       => $strUsrCreacion,
                                                                                                "clientIp"          => $strIpCreacion,
                                                                                                "strCodEmpresa"     => $strCodEmpresa,
                                                                                                "idAccion"          => 311,
                                                                                                "strMsjHistorial"   => $strMsjHistoServicioAdicional
                                                                                         ));
                                $strStatusServicio          = $arrayRespuestaAdicional["status"];
                                $strMensajeServicio         = $arrayRespuestaAdicional["mensaje"];
                            }
                            else
                            {
                                $strMostrarError = "SI";
                                throw new \Exception("No se pudo obtener el plan o producto del servicio con ID ".$intIdServicio);
                            }
                        }
                        else
                        {
                            $strMostrarError = "SI";
                            throw new \Exception("No se pudo obtener el objeto del servicio con ID ".$intIdServicio);
                        }
                    }
                    catch(\Exception $e)
                    {
                        $strStatusServicio = "ERROR";
                        if($strMostrarError === "SI")
                        {
                            $strMensajeServicio = $e->getMessage();
                        }
                        else
                        {
                            $strMensajeServicio = "No se ha podido realizar el corte del Internet Protegido";
                        }
                    }
                    $arrayRespuestaServicios[]  = array("status"                        => $strStatusServicio,
                                                        "mensaje"                       => $strMensajeServicio,
                                                        "idServicio"                    => $intIdServicio,
                                                        "idProducto"                    => $intIdProdServicio,
                                                        "tipoServicio"                  => $strTipoServicio,
                                                        "tipoTecnologiaInicial"         => "NUEVA",
                                                        "permiteEjecutarProcesoLogico"  => "SI"
                        );
                }
                $strStatus  = "OK";
            }
            else 
            {
                throw new \Exception("No se han enviado todos los parámetros necesarios para ejecutar el corte");
            }
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->cortarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"    => $strStatus, 
                                "mensaje"   => $strMensaje,
                                "data"      => $arrayRespuestaServicios);
        return $arrayRespuesta;
    }
    
    
    /**
     * Función que obtiene los servicios Internet Protegido
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     * 
     * 
     */
    public function obtenerInfoServiciosInternetProtegido($arrayParametros)
    {
        $strUsrCreacion                             = $arrayParametros['usrCreacion'] ? $arrayParametros['usrCreacion'] : "procesosmasivos";
        $strIpCreacion                              = $arrayParametros['ipCreacion'] ? $arrayParametros['ipCreacion'] : "127.0.0.1";
        $strCodEmpresa                              = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa'] : "18";
        $objPunto                                   = $arrayParametros["objPunto"];
        $strValor1ParamAntivirus                    = $arrayParametros['strValor1ParamAntivirus'] 
                                                      ? $arrayParametros['strValor1ParamAntivirus'] : "";
        $strValor2LoginesAntivirus                  = $arrayParametros['strValor2LoginesAntivirus'] 
                                                      ? $arrayParametros['strValor2LoginesAntivirus'] : "";
        $arrayServiciosWs                           = $arrayParametros["arrayServiciosWs"] ? $arrayParametros["arrayServiciosWs"] : array();
        $strProcesoEjecuta                          = $arrayParametros["strProcesoEjecuta"] ? $arrayParametros["strProcesoEjecuta"] : "";
        $strTieneAntivirusEnPlan                    = "NO";
        $strTieneAntivirusAdicional                 = "NO";
        $boolFalse                                  = false;
        $strMensaje                                 = "";
        $arrayServicioProdEnPlan                    = array();
        $arrayServiciosAdicAntivirusNuevo           = array();
        $arrayServiciosAdicAntivirusAnterior        = array();
        $arrayServiciosInternetProtegidoAdicionales = array();
        $strConsultaServiciosAdicionales            = "NO";
        $arrayEstadosServiciosAdicionales           = array();
        try
        {
            $arrayValidaFlujoAntivirusMasivos   = $this->serviceLicenciasKaspersky
                                                       ->validaFlujoAntivirus(array( 
                                                                                    "intIdPunto"                => $objPunto->getId(),
                                                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                                                    "strValor1ParamAntivirus"   => $strValor1ParamAntivirus,
                                                                                    "strValor2LoginesAntivirus" => $strValor2LoginesAntivirus
                                                                             ));
            $strFlujoAntivirus                  = $arrayValidaFlujoAntivirusMasivos["strFlujoAntivirus"];
            $strValorAntivirus                  = $arrayValidaFlujoAntivirusMasivos["strValorAntivirus"];
            
            if($strProcesoEjecuta === "REACTIVACION MASIVA")
            {
                //Desde el web service sólo se envian los In-Corte, por lo que hay que consultar los servicios en estado Pendiente
                $strConsultaServiciosAdicionales    = "SI";
                $arrayEstadosServiciosAdicionales   = array('Pendiente');
            }
            else if($strProcesoEjecuta === "CAMBIO DE PLAN MASIVO")
            {
                $strConsultaServiciosAdicionales    = "SI";
                $arrayEstadosServiciosAdicionales   = array('Activo', 'Pendiente', 'In-Corte');
                
            }
            
            if($strConsultaServiciosAdicionales === "SI" && isset($arrayEstadosServiciosAdicionales) && !empty($arrayEstadosServiciosAdicionales))
            {
                $arrayServiciosAdicionales  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findBy(array( 'puntoId' => $objPunto,
                                                                                'estado'  => $arrayEstadosServiciosAdicionales));
                
                foreach($arrayServiciosAdicionales as $objServicioAdicional)
                {
                    $objProdServicioAdicional   = $objServicioAdicional->getProductoId();
                    if (is_object($objProdServicioAdicional))
                    {
                        $boolEsIProtegido   = strpos($objProdServicioAdicional->getDescripcionProducto(), 'I. PROTEGIDO');
                        $boolEsIProteccion  = strpos($objProdServicioAdicional->getDescripcionProducto(), 'I. PROTECCION');
                        if ($boolEsIProtegido !== $boolFalse || $boolEsIProteccion !== $boolFalse)
                        {
                            $arrayServiciosInternetProtegidoAdicionales[]   = array("idServicio"            => $objServicioAdicional->getId(),
                                                                                    "idProducto"            => $objProdServicioAdicional->getId(),
                                                                                    "descripcionProducto"   => 
                                                                                    $objProdServicioAdicional->getDescripcionProducto(),
                                                                                    "tipoServicio"          => "PRODUCTO");
                        }
                    }
                }
            }
            
            $arrayServiciosInternetProtegido = array_merge($arrayServiciosWs, $arrayServiciosInternetProtegidoAdicionales);
            if(isset($arrayServiciosInternetProtegido) && !empty($arrayServiciosInternetProtegido))
            {
                foreach($arrayServiciosInternetProtegido as $arrayServicioInternetProtegido)
                {
                    $intIdServicio          = $arrayServicioInternetProtegido["idServicio"];
                    $intIdProdServicio      = $arrayServicioInternetProtegido["idProducto"];
                    $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                    if(is_object($objServicio))
                    {
                        if(is_object($objServicio->getPlanId()))
                        {
                            $objProdEnPlan                  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                                ->find($intIdProdServicio);
                            $strTieneAntivirusEnPlan        = "SI";
                            $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                                    "objProducto"       => $objProdEnPlan,
                                                                    "strCaracteristica" => "SUSCRIBER_ID");
                            $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                            if($arrayRespuestaSpcSuscriberId["status"] === 'OK' 
                                && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
                            {
                                $strTieneNuevoAntivirus = "SI";
                            }
                            else
                            {
                                $strTieneNuevoAntivirus = "NO";
                            }
                            $arrayServicioProdEnPlan = array(   "objServicioProdEnPlan"     => $objServicio,
                                                                "objProdEnPlan"             => $objProdEnPlan,
                                                                "strTieneNuevoAntivirus"    => $strTieneNuevoAntivirus);
                        }
                        else if(is_object($objServicio->getProductoId()))
                        {
                            $strTieneAntivirusAdicional     = "SI";
                            $strTieneNuevoAntivirus         = "NO";
                            $objProductoServicio            = $objServicio->getProductoId();
                            $boolIProtegido                 = strpos($objProductoServicio->getDescripcionProducto(), 'I. PROTEGIDO');
                            $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                                    "strCaracteristica" => "SUSCRIBER_ID");
                            $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                            if($arrayRespuestaSpcSuscriberId["status"] === 'OK' 
                                && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
                            {
                                $strTieneNuevoAntivirus = "SI";
                            }
                            else
                            {
                                $strTieneNuevoAntivirus = "NO";
                            }

                            if($boolIProtegido !== $boolFalse)
                            {
                                $arrayParamsGetSpcCantDisp  = array("objServicio"       => $objServicio,
                                                                    "strCaracteristica" => "CANTIDAD DISPOSITIVOS");
                                $arrayRespuestaSpcCantDisp  = $this->serviceLicenciasKaspersky
                                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCantDisp);
                                if($arrayRespuestaSpcCantDisp["status"] === 'OK' 
                                    && is_object($arrayRespuestaSpcCantDisp["objServicioProdCaract"]))
                                {
                                    $intCantidadDispositivos = (int) $arrayRespuestaSpcCantDisp["objServicioProdCaract"]->getValor();
                                }
                                else
                                {
                                   $intCantidadDispositivos = 1;
                                }
                            }
                            else
                            {
                                $intCantidadDispositivos = 1;
                            }
                            $arrayServicioAdicional = array("intPrecioVenta"            => $objServicio->getPrecioVenta(),
                                                            "objServicioAdicional"      => $objServicio,
                                                            "strTieneNuevoAntivirus"    => $strTieneNuevoAntivirus,
                                                            "intCantidadDispositivos"   => $intCantidadDispositivos);
                            if($strTieneNuevoAntivirus == "SI")
                            {
                                $arrayServiciosAdicAntivirusNuevo[]     = $arrayServicioAdicional;
                            }
                            else
                            {
                                $arrayServiciosAdicAntivirusAnterior[]  = $arrayServicioAdicional;
                            }
                        }
                    }
                }
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'InternetProtegidoService.obtenerInfoServiciosInternetProtegido',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array(
                                "status"                                => $strStatus,
                                "mensaje"                               => $strMensaje,
                                "strFlujoAntivirus"                     => $strFlujoAntivirus,
                                "strValorAntivirus"                     => $strValorAntivirus,
                                "strTieneAntivirusEnPlan"               => $strTieneAntivirusEnPlan,
                                "strTieneAntivirusAdicional"            => $strTieneAntivirusAdicional,
                                "arrayServicioProdEnPlan"               => $arrayServicioProdEnPlan,
                                "arrayServiciosAdicAntivirusNuevo"      => $arrayServiciosAdicAntivirusNuevo,
                                "arrayServiciosAdicAntivirusAnterior"   => $arrayServiciosAdicAntivirusAnterior);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la reactivación de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología KASPERSKY 
     * o realiza el proceso de migración de los servicios  I. PROTEGIDO TRIAL, I. PROTEGIDO MULTI PAID, 
     * I. PROTECCION TOTAL TRIAL, I. PROTECCION TOTAL PAID con tecnología McAfee
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     * @param array $arrayParametrosWs [
     *                                      "op"            => Opción que fue ejecutada desde el web service,
     *                                      "token"         => Token enviado al web service,
     *                                      "user"          => Usuario usado en la generación de tokens,
     *                                      "usrCreacion"   => Usuario de creación,
     *                                      "source"        => Arreglo con los parámetros necesarios para generar un token,
     *                                      "ipCreacion"    => Escenario enviado por cada proceso,
     *                                      "data"          => [
     *                                                          "idPunto"               => Id del punto,
     *                                                          "idServicioInternet"    => Id del servicio de Internet,
     *                                                          "servicios"             => Arreglo con los servicios Internet Protegido,
     *                                                          "tipoProceso"           => "REACTIVACION MASIVA",
     *                                                          "codEmpresa"            => Id de la empresa
     *                                                         ]
     *                                  ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => Mensaje de error,
     *                                  "data"      => [
     *                                                  "status"                        => OK o ERROR del proceso por servicio,
     *                                                  "mensaje"                       => Mensaje de error del proceso por servicio,
     *                                                  "idServicio"                    => id del servicio procesado
     *                                                  "idProducto"                    => id del producto Internet Protegido asociado al servicio,
     *                                                  "tipoServicio"                  => "PLAN" o "PRODUCTO",
     *                                                  "tipoTecnologiaInicial"         => "ANTERIOR" o "NUEVA",
     *                                                  "permiteEjecutarProcesoLogico"  => "SI" o "NO" permite ingresar historiales del servicio
     *                                                                                     desde el proceso que lo invoca
     *                                                 ]
     *                                ]
     * 
     */
    public function reconectarLicencias($arrayParametrosWs)
    {
        $strUsrCreacion                         = $arrayParametrosWs['usrCreacion'] ? $arrayParametrosWs['usrCreacion'] : "procesosmasivos";
        $strIpCreacion                          = $arrayParametrosWs['ipCreacion'] ? $arrayParametrosWs['ipCreacion'] : "127.0.0.1";
        $strMensaje                             = "";
        $arrayRespuestaServicios                = array();
        $arrayServiciosAdicAntivirusNuevo       = array();
        $arrayServiciosAdicAntivirusAnterior    = array();
        try
        {
            if(!empty($arrayParametrosWs['data']['servicios']) && !empty($arrayParametrosWs['data']['tipoProceso'])
                && !empty($arrayParametrosWs['data']['idPunto']) && !empty($arrayParametrosWs['data']['codEmpresa'])
                && !empty($arrayParametrosWs['data']['idServicioInternet']))
            {
                $strCodEmpresa                  = $arrayParametrosWs['data']['codEmpresa'];
                $intIdPunto                     = $arrayParametrosWs['data']['idPunto'];
                $intIdServicioInternet          = $arrayParametrosWs['data']['idServicioInternet'];
                $arrayServiciosWs               = $arrayParametrosWs['data']['servicios'];
                $objPunto                       = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                $objPersonaEmpresaRol           = $objPunto->getPersonaEmpresaRolId();
                $objPersona                     = $objPersonaEmpresaRol->getPersonaId();
                $objJurisdiccion                = $objPunto->getPuntoCoberturaId();
                $strLogin                       = $objPunto->getLogin();
                $strNombreCliente               = sprintf("%s",$objPersona);
                if(is_object($objJurisdiccion))
                {
                    $strNombreJurisdiccion  = $objJurisdiccion->getNombreJurisdiccion();
                }
                else
                {
                    $strNombreJurisdiccion  = "";
                }
                
                $arrayInfoServiciosInternetProtegido        = $this->obtenerInfoServiciosInternetProtegido(array(
                                                                        "usrCreacion"               => $strUsrCreacion,
                                                                        "ipCreacion"                => $strIpCreacion,
                                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                                        "objPunto"                  => $objPunto,
                                                                        "arrayServiciosWs"          => $arrayServiciosWs,
                                                                        "strProcesoEjecuta"         => $arrayParametrosWs['data']['tipoProceso'],
                                                                        "strValor1ParamAntivirus"   => static::$strOpcionMasivo,
                                                                        "strValor2LoginesAntivirus" => static::$strOpcionMasivo
                                                                ));
                $strStatusInfoServiciosInternetProtegido    = $arrayInfoServiciosInternetProtegido["status"];
                if($strStatusInfoServiciosInternetProtegido === "OK")
                {
                    $strFlujoAntivirus                      = $arrayInfoServiciosInternetProtegido["strFlujoAntivirus"];
                    $strValorAntivirus                      = $arrayInfoServiciosInternetProtegido["strValorAntivirus"];
                    $strTieneAntivirusEnPlan                = $arrayInfoServiciosInternetProtegido["strTieneAntivirusEnPlan"];
                    $strTieneAntivirusAdicional             = $arrayInfoServiciosInternetProtegido["strTieneAntivirusAdicional"];
                    $arrayServicioProdEnPlan                = $arrayInfoServiciosInternetProtegido["arrayServicioProdEnPlan"];
                    $arrayServiciosAdicAntivirusNuevo       = $arrayInfoServiciosInternetProtegido["arrayServiciosAdicAntivirusNuevo"];
                    $arrayServiciosAdicAntivirusAnterior    = $arrayInfoServiciosInternetProtegido["arrayServiciosAdicAntivirusAnterior"];
                    
                    //Licencias dentro del plan
                    if(isset($arrayServicioProdEnPlan) && !empty($arrayServicioProdEnPlan) 
                        && $strTieneAntivirusEnPlan === "SI")
                    {
                        $objServicioProdEnPlan  = $arrayServicioProdEnPlan["objServicioProdEnPlan"];
                        $objProdEnPlan          = $arrayServicioProdEnPlan["objProdEnPlan"];
                        //El servicio ya posee la característica SUSCRIBER_ID, por ende tiene tecnología Kaspersky
                        if(isset($arrayServicioProdEnPlan["strTieneNuevoAntivirus"])
                            && !empty($arrayServicioProdEnPlan["strTieneNuevoAntivirus"])
                            && $arrayServicioProdEnPlan["strTieneNuevoAntivirus"] === "SI")
                        {
                            //Reactivación de antivirus con nueva tecnología Kaspersky
                            $arrayParamsLicencias           = array("strProceso"                => "REACTIVACION_ANTIVIRUS",
                                                                    "strEscenario"              => "REACTIVACION_PROD_EN_PLAN",
                                                                    "objServicio"               => $objServicioProdEnPlan,
                                                                    "objPunto"                  => $objServicioProdEnPlan->getPuntoId(),
                                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                                    "objProductoIPMP"           => $objProdEnPlan,
                                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                                    "strIpCreacion"             => $strIpCreacion,
                                                                    "strEstadoServicioInicial"  => $objServicioProdEnPlan->getEstado(),
                                                                    "strValor1ParamAntivirus"   => static::$strOpcionMasivo
                                                                    );
                            $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                            if($arrayRespuestaGestionLicencias["status"] === "ERROR")
                            {
                                /**
                                 * Envío de correo inmediato para q se realice el proceso de reactivación de manera manual,
                                 * ya que desde el proceso masivo de reactivación se realizará de manera lógica
                                 */
                                try
                                {
                                    $arrayParametrosErrorIPMP   = array( 
                                                                        "nombreProducto"        => $objProdEnPlan->getDescripcionProducto(),
                                                                        "descripcionServicio"   => "incluido en el plan con tecnología KASPERSKY",
                                                                        "cliente"               => $strNombreCliente,
                                                                        "login"                 => $strLogin,
                                                                        "nombreJurisdiccion"    => $strNombreJurisdiccion,
                                                                        "tipoServicio"          => "Plan",
                                                                        "nombreServicio"        => 
                                                                        $objServicioProdEnPlan->getPlanId()->getNombrePlan(),
                                                                        "observacion"           => "No se ha podido reactivar el servicio con el "
                                                                                                   ."proveedor",
                                                                        "estadoServicio"        => $objServicioProdEnPlan->getEstado()
                                                                        );
                                    /**
                                     * Se envía notificación indicando que no se ha podido reactivar producto I. Protegido Multi Paid 
                                     * incluido en el plan
                                     */
                                    $this->serviceEnvioPlantilla->generarEnvioPlantilla('Error en Reactivacion de '.
                                                                                        $objProdEnPlan->getDescripcionProducto()." - ".$strLogin, 
                                                                                        array(), 
                                                                                        'ERRORREACTIPMP', 
                                                                                        $arrayParametrosErrorIPMP, 
                                                                                        '','','', null, false,
                                                                                        'notificacionesnetlife@netlife.info.ec');
                                }
                                catch (\Exception $e)
                                {
                                    error_log("No se ha podido enviar el correo con código ERRORREACTIPMP ".$e->getMessage());
                                }
                            }
                            $arrayRespuestaServicios[]      = array("status"                        => $arrayRespuestaGestionLicencias["status"],
                                                                    "mensaje"                       => $arrayRespuestaGestionLicencias["mensaje"],
                                                                    "idServicio"                    => $objServicioProdEnPlan->getId(),
                                                                    "idProducto"                    => $objProdEnPlan->getId(),
                                                                    "tipoServicio"                  => "PLAN",
                                                                    "tipoTecnologiaInicial"         => "NUEVA",
                                                                    "permiteEjecutarProcesoLogico"  => "NO"
                                                                    );
                        }
                        //El punto forma parte del piloto o ya está activada la bandera de los masivos a PRODUCCION
                        else if($strFlujoAntivirus === "NUEVO")
                        {
                            //Realizar Proceso de migración de licencias, es decir se cancelan las licencias McAfee y se activa con Kaspersky
                            $arrayRespuestaMigrarLicencias  = $this->migrarLicenciasEnPlan(array(
                                                                                                "codEmpresa"                => $strCodEmpresa,
                                                                                                "usrCreacion"               => $strUsrCreacion,
                                                                                                "ipCreacion"                => $strIpCreacion,
                                                                                                "objServicioProdEnPlan"     => $objServicioProdEnPlan,
                                                                                                "objProdEnPlan"             => $objProdEnPlan,
                                                                                                "strValor1ParamAntivirus"   => 
                                                                                                static::$strOpcionMasivo,
                                                                                                "strValor2LoginesAntivirus" =>
                                                                                                static::$strOpcionMasivo));
                            $arrayRespuestaServicios[]      = array("status"                        => $arrayRespuestaMigrarLicencias["status"],
                                                                    "mensaje"                       => $arrayRespuestaMigrarLicencias["mensaje"],
                                                                    "idServicio"                    => $objServicioProdEnPlan->getId(),
                                                                    "idProducto"                    => $objProdEnPlan->getId(),
                                                                    "tipoServicio"                  => "PLAN",
                                                                    "tipoTecnologiaInicial"         => "ANTERIOR",
                                                                    "permiteEjecutarProcesoLogico"  => "NO"
                                                                    );
                        }
                        //El servicio tiene tecnología McAfee
                        else
                        {
                            /**
                             * Al ser un servicio asociado a un plan que incluye Internet Protegido con tecnología McAfee, 
                             * no se necesita actualizar el estado del servicio ni realizar el ingreso de un historial
                             * porque ya fue realizado desde el proceso masivo
                             */
                            $arrayRespuestaServicios[]  = array("status"                        => "OK",
                                                                "mensaje"                       => "",
                                                                "idServicio"                    => $objServicioProdEnPlan->getId(),
                                                                "idProducto"                    => $objProdEnPlan->getId(),
                                                                "tipoServicio"                  => "PLAN",
                                                                "tipoTecnologiaInicial"         => "ANTERIOR",
                                                                "permiteEjecutarProcesoLogico"  => "NO"
                                                            );
                        }
                    }
                    
                    //Licencias como producto adicional
                    if($strTieneAntivirusAdicional === "SI")
                    {
                        if(isset($arrayServiciosAdicAntivirusNuevo) && !empty($arrayServiciosAdicAntivirusNuevo))
                        {
                            foreach($arrayServiciosAdicAntivirusNuevo as $arrayServicioAntivirusNuevo)
                            {
                                //Reactivación de antivirus con nueva tecnología Kaspersky
                                $objServicioAdicAntivirusNuevo      = $arrayServicioAntivirusNuevo["objServicioAdicional"];
                                $objProdServicioAdicAntivirusNuevo  = $objServicioAdicAntivirusNuevo->getProductoId();
                                $arrayRespuestaAdicAntivirusNuevo   = $this->serviceReconectarServicio
                                                                           ->reactivarServiciosOtros(
                                                                            array(  "idServicio"        => $objServicioAdicAntivirusNuevo->getId(),
                                                                                    "usrCreacion"       => $strUsrCreacion,
                                                                                    "clientIp"          => $strIpCreacion,
                                                                                    "strCodEmpresa"     => $strCodEmpresa,
                                                                                    "idAccion"          => 315,
                                                                                    "strMsjHistorial"   => 
                                                                                    "Se reactivó el servicio "
                                                                                    .$objProdServicioAdicAntivirusNuevo->getDescripcionProducto().
                                                                                    " con tecnología ".$strValorAntivirus
                                                                                
                                                                             ));
                                if($arrayRespuestaAdicAntivirusNuevo["status"] === "OK")
                                {
                                    $strPermiteEjecutarProcesoLogico    = "NO";
                                    //Envío de correo en caso de error
                                    
                                    try
                                    {
                                        $arrayParametrosErrorIPMP   = array( 
                                                                            "nombreProducto"        => 
                                                                            $objProdServicioAdicAntivirusNuevo->getDescripcionProducto(),
                                                                            "descripcionServicio"   => 
                                                                            "como producto adicional con tecnología KASPERSKY",
                                                                            "cliente"               => $strNombreCliente,
                                                                            "login"                 => $strLogin,
                                                                            "nombreJurisdiccion"    => $strNombreJurisdiccion,
                                                                            "tipoServicio"          => "Producto",
                                                                            "nombreServicio"        => 
                                                                            $objProdServicioAdicAntivirusNuevo->getDescripcionProducto(),
                                                                            "observacion"           => "No se ha podido reactivar el servicio con el "
                                                                                                       ."proveedor",
                                                                            "estadoServicio"        => $objServicioAdicAntivirusNuevo->getEstado()
                                                                            );
                                        /**
                                         * Se envía notificación indicando que no se ha podido reactivar producto I. Protegido Multi Paid 
                                         * incluido en el plan
                                         */
                                        $this->serviceEnvioPlantilla->generarEnvioPlantilla(
                                                                                        'Error en Reactivacion de '.
                                                                                        $objProdServicioAdicAntivirusNuevo->getDescripcionProducto()
                                                                                        ." - ".$strLogin,
                                                                                        array(), 
                                                                                        'ERRORREACTIPMP', 
                                                                                        $arrayParametrosErrorIPMP, 
                                                                                        '','','', null, false,
                                                                                        'notificacionesnetlife@netlife.info.ec');
                                    }
                                    catch (\Exception $e)
                                    {
                                        error_log("PRODUCTO No se ha podido enviar el correo con código ERRORREACTIPMP ".$e->getMessage());
                                    }
                                }
                                else
                                {
                                    $strPermiteEjecutarProcesoLogico    = "SI";
                                }
                                
                                $arrayRespuestaServicios[]  = array("status"                        => $arrayRespuestaAdicAntivirusNuevo["status"],
                                                                    "mensaje"                       => $arrayRespuestaAdicAntivirusNuevo["mensaje"],
                                                                    "idServicio"                    => $objServicioAdicAntivirusNuevo->getId(),
                                                                    "idProducto"                    => 
                                                                    $objProdServicioAdicAntivirusNuevo->getId(),
                                                                    "tipoServicio"                  => "PRODUCTO",
                                                                    "tipoTecnologiaInicial"         => "NUEVA",
                                                                    "permiteEjecutarProcesoLogico"  => $strPermiteEjecutarProcesoLogico
                                                                );
                            }
                        }
                        else if($strFlujoAntivirus === "NUEVO")
                        {
                            $objServicioInternet        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                            ->find($intIdServicioInternet);
                            $objServicioTecnicoInternet = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                            ->findOneByServicioId($intIdServicioInternet);
                            
                            //Se realiza el proceso de migración de licencias, es decir se cancelan las licencias McAfee y se activa con Kaspersky
                            $arrayMigracionLicenciasAdic    = $this->migrarLicenciasAdicionales(array(
                                                                    "codEmpresa"                            => $strCodEmpresa,
                                                                    "usrCreacion"                           => $strUsrCreacion,
                                                                    "ipCreacion"                            => $strIpCreacion,
                                                                    "arrayServiciosAdicAntivirusAnterior"   => $arrayServiciosAdicAntivirusAnterior,
                                                                    "strActivacionLicenciasNuevasEnPlan"    => "NO",
                                                                    "intCantidadLicenciasNuevasEnPlan"      => 0,
                                                                    "objProductoInternetProtegido"          => null,
                                                                    "objServicioInternet"                   => $objServicioInternet,
                                                                    "objServicioTecnicoInternet"            => $objServicioTecnicoInternet,
                                                                    "strValorAntivirus"                     => $strValorAntivirus,
                                                                    "strProcesoEjecuta"                     => 
                                                                    $arrayParametrosWs['data']['tipoProceso'],
                                                                    "strValor1ParamAntivirus"               => static::$strOpcionMasivo,
                                                                    "strValor2LoginesAntivirus"             => static::$strOpcionMasivo));
                            
                            if($arrayMigracionLicenciasAdic["status"] === "OK")
                            {
                                $strPermiteEjecutarProcesoLogico    = "NO";
                                $strMsjServicio                     = "";
                            }
                            else
                            {
                                $strPermiteEjecutarProcesoLogico    = "SI";
                                $strMsjServicio                     = "El servicio no pudo ser migrado de tecnología";
                            }
                            if(isset($arrayServiciosAdicAntivirusAnterior) && !empty($arrayServiciosAdicAntivirusAnterior))
                            {
                                
                                foreach($arrayServiciosAdicAntivirusAnterior as $arrayServicioAntivirusAnterior)
                                {
                                    
                                    $objServicioMigrado         = $arrayServicioAntivirusAnterior["objServicioAdicional"];
                                    $arrayRespuestaServicios[]  = array("status"                        => $arrayMigracionLicenciasAdic["status"],
                                                                        "mensaje"                       => $strMsjServicio,
                                                                        "idServicio"                    => $objServicioMigrado->getId(),
                                                                        "idProducto"                    => 
                                                                        $objServicioMigrado->getProductoId()->getId(),
                                                                        "tipoServicio"                  => "PRODUCTO",
                                                                        "tipoTecnologiaInicial"         => "ANTERIOR",
                                                                        "permiteEjecutarProcesoLogico"  => $strPermiteEjecutarProcesoLogico
                                                                    );
                                }
                            }
                        }
                        else
                        {
                            /**
                             * Al ser servicios Internet Protegido adicionales con tecnología McAfee, se realiza 
                             * la reactivación del servicio de manera lógica y se ingresa el historial tal cual 
                             * como lo hace el proceso masivo de reactivación
                             */
                            if(isset($arrayServiciosAdicAntivirusAnterior) && !empty($arrayServiciosAdicAntivirusAnterior))
                            {
                                foreach($arrayServiciosAdicAntivirusAnterior as $arrayServicioAntivirusAnterior)
                                {
                                    $objServicioMcAfee          = $arrayServicioAntivirusAnterior["objServicioAdicional"];
                                    $arrayRespuestaServicios[]  = array("status"                        => "OK",
                                                                        "mensaje"                       => "El servicio se reactivo exitosamente",
                                                                        "idServicio"                    => $objServicioMcAfee->getId(),
                                                                        "idProducto"                    => 
                                                                        $objServicioMcAfee->getProductoId()->getId(),
                                                                        "tipoServicio"                  => "PRODUCTO",
                                                                        "tipoTecnologiaInicial"         => "ANTERIOR",
                                                                        "permiteEjecutarProcesoLogico"  => "SI"
                                                                    );
                                }
                            }
                        }
                    }
                }
                else
                {
                    foreach($arrayServiciosWs as $arrayServicioWs)
                    {
                        $arrayRespuestaServicios[]  = array("status"                        => "ERROR",
                                                            "mensaje"                       => "No se ha podido obtener la información de los "
                                                                                               ."servicios",
                                                            "idServicio"                    => $arrayServicioWs["idServicio"],
                                                            "idProducto"                    => $arrayServicioWs["idProducto"],
                                                            "tipoServicio"                  => $arrayServicioWs["tipoServicio"],
                                                            "tipoTecnologiaInicial"         => "",
                                                            "permiteEjecutarProcesoLogico"  => "SI"
                                                        );
                    }
                }
            }
            else 
            {
                throw new \Exception("No se han enviado todos los parámetros necesarios para ejecutar la reactivación");
            }
            $strStatus  = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->reconectarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        
        $arrayRespuesta = array("status"    => $strStatus, 
                                "mensaje"   => $strMensaje,
                                "data"      => $arrayRespuestaServicios);
        return $arrayRespuesta;
    }
    
    /**
     * Función que verifica si un plan incluye un producto determinado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 09-09-2019
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 19-09-2020       Se agrega nombre técnico como parámetro de la función para validar productos
     *                               incluidos en el plan con esta información en caso de necesitarlo
     * 
     * @param array $arrayParametros [
     *                                      "intIdPlan"                 => Id del plan,
     *                                      "strDescripcionProducto"    => Descripción del producto
     *                                      "strNombreTecnicoProducto"  => Nombre Técnico del producto
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "strPlanTieneProducto"      => "SI" o "NO" el plan incluye el producto,
     *                                  "objProductoEnPlan"         => objeto del producto buscado,
     *                                  "objDetallePlanProducto"    => objeto del detalle del plan asociado al producto buscado
     *                                ]
     * 
     */
    public function verificaProductosEnPlan($arrayParametros)
    {
        $intIdPlan              = $arrayParametros["intIdPlan"];
        $boolFalse              = false;
        $strPlanTieneProducto   = "NO";
        $objProductoEnPlan      = null;
        $objDetallePlanProducto = null;
        $boolVerificaProductoEnPlan = false;
        try
        {
            $arrayDetallesPlan  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                    ->findBy(array("planId" => $intIdPlan));
            foreach($arrayDetallesPlan as $objDetallePlan)
            {
                $objProductoDetallePlan = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->find($objDetallePlan->getProductoId());
                if(is_object($objProductoDetallePlan))
                {
                    if(isset($arrayParametros["strDescripcionProducto"]) && !empty($arrayParametros["strDescripcionProducto"]))
                    {
                        $boolVerificaProductoEnPlan = strpos($objProductoDetallePlan->getDescripcionProducto(),
                                                             $arrayParametros["strDescripcionProducto"]);
                    }
                    else if(isset($arrayParametros["strNombreTecnicoProducto"]) && !empty($arrayParametros["strNombreTecnicoProducto"]))
                    {
                        $boolVerificaProductoEnPlan = strpos($objProductoDetallePlan->getNombreTecnico(),
                                                             $arrayParametros["strNombreTecnicoProducto"]);
                    }
                    if($boolVerificaProductoEnPlan !== $boolFalse)
                    {
                        $objProductoEnPlan      = $objProductoDetallePlan;
                        $objDetallePlanProducto = $objDetallePlan;
                        $strPlanTieneProducto   = "SI";
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            error_log("No se ha podido verificar si el producto se encuentra dentro del plan ".$e->getMessage());
        }
        $arrayRespuesta = array("strPlanTieneProducto"      => $strPlanTieneProducto,
                                "objProductoEnPlan"         => $objProductoEnPlan,
                                "objDetallePlanProducto"    => $objDetallePlanProducto);
        return $arrayRespuesta;
        
    }
    
    /**
     * Función que realiza la gestión de las licencias Internet Protegido al realizar un cambio de plan desde el proceso masivo de MD
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-09-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 22-01-2020 Se agrega validación adicional para verificar que el servicio Internet Protegido si fue activado con McAfee,
     *                          caso contrario se contará como que era un plan sin Internet Protegido y no se realizará la migración de licencias,
     *                          sino que se realizaría la activación con Kaspersky
     * 
     * @param array $arrayParametrosWs [
     *                                      "op"            => Opción que fue ejecutada desde el web service,
     *                                      "token"         => Token enviado al web service,
     *                                      "user"          => Usuario usado en la generación de tokens,
     *                                      "usrCreacion"   => Usuario de creación,
     *                                      "ipCreacion"    => Escenario enviado por cada proceso,
     *                                      "source"        => Arreglo con los parámetros necesarios para generar un token,
     *                                      "data"          => [
     *                                                          "idPunto"               => Id del punto,
     *                                                          "idServicioInternet"    => Id del servicio de Internet,
     *                                                          "idPlanAnterior"        => Id del plan anterior,
     *                                                          "idPlanNuevo"           => Id del plan nuevo,
     *                                                          "tipoProceso"           => "CAMBIO DE PLAN MASIVO",
     *                                                          "codEmpresa"            => Id de la empresa
     *                                                         ]
     *                                  ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => Mensaje de error
     *                                ]
     * 
     */
    public function cambiarPlanLicencias($arrayParametrosWs)
    {
        $strUsrCreacion                         = $arrayParametrosWs['usrCreacion'] ? $arrayParametrosWs['usrCreacion'] : "procesosmasivos";
        $strIpCreacion                          = $arrayParametrosWs['ipCreacion'] ? $arrayParametrosWs['ipCreacion'] : "127.0.0.1";
        $strMensaje                             = "";
        $arrayServiciosAdicAntivirusAnterior    = array();
        try
        {
            if(!empty($arrayParametrosWs['data']['idPunto']) && !empty($arrayParametrosWs['data']['idServicioInternet'])
                && !empty($arrayParametrosWs['data']['idPlanAnterior']) && !empty($arrayParametrosWs['data']['idPlanNuevo'])
                && !empty($arrayParametrosWs['data']['tipoProceso']) && !empty($arrayParametrosWs['data']['codEmpresa'])
                )
            {
                $strActivacionLicenciasNuevasEnPlan = "NO";
                $strTieneNuevoAntivirus             = "NO";
                $intCantidadLicenciasNuevasEnPlan   = 0;
                $strTipoProceso                     = $arrayParametrosWs['data']['tipoProceso'];
                $strCodEmpresa                      = $arrayParametrosWs['data']['codEmpresa'];
                $intIdPunto                         = $arrayParametrosWs['data']['idPunto'];
                $intIdServicioInternet              = $arrayParametrosWs['data']['idServicioInternet'];
                $intIdPlanAnteriorServicio          = $arrayParametrosWs['data']['idPlanAnterior'];
                $intIdPlanNuevoServicio             = $arrayParametrosWs['data']['idPlanNuevo'];
                $objServicioInternet                = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioInternet);
                $objPunto                           = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
                
                //Se verifica si el plan anterior incluía Internet Protegido
                $arrayRespuestaVerificaPlanAnterior = $this->verificaProductosEnPlan(array( "intIdPlan"                 => $intIdPlanAnteriorServicio,
                                                                                            "strDescripcionProducto"    => "I. PROTEGIDO MULTI PAID")
                                                                                    );
                $strPlanAnteriorTieneMcAfee         = $arrayRespuestaVerificaPlanAnterior["strPlanTieneProducto"];
                $objProdInternetProtegidoAnterior   = $arrayRespuestaVerificaPlanAnterior["objProductoEnPlan"];
                if($strPlanAnteriorTieneMcAfee === "SI")
                {
                    if(is_object($objProdInternetProtegidoAnterior))
                    {
                        //En caso de que el plan ya incluyera Internet Protegido, se verifica si ya tiene la nueva tecnología de licencias
                        $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicioInternet,
                                                                "objProducto"       => $objProdInternetProtegidoAnterior,
                                                                "strCaracteristica" => "SUSCRIBER_ID");
                        $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                               ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                        if($arrayRespuestaSpcSuscriberId["status"] === 'OK' && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
                        {
                            $strTieneNuevoAntivirus = "SI";
                        }
                        else
                        {
                            $strTieneNuevoAntivirus = "NO";
                            $arrayParamsGetSpcSku   = array("objServicio"       => $objServicioInternet,
                                                            "objProducto"       => $objProdInternetProtegidoAnterior,
                                                            "strCaracteristica" => "SKU");
                            $arrayRespuestaSpcSku   = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSku);
                            if($arrayRespuestaSpcSku["status"] === 'OK' && !is_object($arrayRespuestaSpcSku["objServicioProdCaract"]))
                            {
                                $strPlanAnteriorTieneMcAfee = "NO";
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("No se ha podido obtener el Producto Internet Protegido dentro del plan anterior");
                    }
                }
                
                //Se verifica si el plan nuevo incluye Internet Protegido
                $arrayRespuestaVerificaPlanNuevo    = $this->verificaProductosEnPlan(array( "intIdPlan"                 => $intIdPlanNuevoServicio,
                                                                                            "strDescripcionProducto"    => "I. PROTEGIDO MULTI PAID")
                                                                                    );
                $strPlanNuevoTieneMcAfee            = $arrayRespuestaVerificaPlanNuevo["strPlanTieneProducto"];
                $objProdInternetProtegidoNuevo      = $arrayRespuestaVerificaPlanNuevo["objProductoEnPlan"];
                $objDetallePlanProductoNuevo        = $arrayRespuestaVerificaPlanNuevo["objDetallePlanProducto"];
                if($strPlanNuevoTieneMcAfee === "SI")
                {
                    if(is_object($objProdInternetProtegidoNuevo) && is_object($objDetallePlanProductoNuevo))
                    {
                        //Si el plan nuevo incluye Internet Protegido, se obtiene la cantidad de licencias asociadas al Internet Protegido del plan
                        $arrayCaracteristicasPlanProdNuevo  = $this->emComercial->getRepository('schemaBundle:InfoPlanProductoCaract')
                                                                   ->getCaracteristicaByParametros( $objDetallePlanProductoNuevo->getId(),
                                                                                                    $objProdInternetProtegidoNuevo->getId(),
                                                                                                    'CANTIDAD DISPOSITIVOS');
                        if(is_array($arrayCaracteristicasPlanProdNuevo) && count($arrayCaracteristicasPlanProdNuevo)>0)
                        {
                            $intCantidadLicenciasNuevasEnPlan   = (int) $arrayCaracteristicasPlanProdNuevo[0]["valor"];
                        }
                        else
                        {
                            $intCantidadLicenciasNuevasEnPlan   = 0;
                        }
                    }
                    else
                    {
                        throw new \Exception("No se ha podido obtener el Producto o el detalle del Internet Protegido dentro del plan nuevo");
                    }
                }
                
                /**
                 * Se obtienen los servicios adicionales Internet Protegido en estado Activo, Pendiente e In-Corte
                 */
                $arrayInfoServiciosInternetProtegido        = $this->obtenerInfoServiciosInternetProtegido(array(
                                                                        "usrCreacion"           => $strUsrCreacion,
                                                                        "ipCreacion"            => $strIpCreacion,
                                                                        "strCodEmpresa"         => $strCodEmpresa,
                                                                        "objPunto"              => $objPunto,
                                                                        "strProcesoEjecuta"     => $strTipoProceso
                                                                    ));
                $strStatusInfoServiciosInternetProtegido    = $arrayInfoServiciosInternetProtegido["status"];
                $strTieneAntivirusAdicional                 = $arrayInfoServiciosInternetProtegido["strTieneAntivirusAdicional"];
                $arrayServiciosAdicAntivirusAnterior        = $arrayInfoServiciosInternetProtegido["arrayServiciosAdicAntivirusAnterior"];
                $strValorAntivirus                          = $arrayInfoServiciosInternetProtegido["strValorAntivirus"];
                
                /**
                 * Si el plan anterior no incluye Internet Protegido y el nuevo plan si lo incluye, se procede a realizar la activación de
                 * las licencias incluidas en el nuevo plan con la tecnología Kaspersky
                 */
                if($strPlanAnteriorTieneMcAfee === "NO" && $strPlanNuevoTieneMcAfee === "SI")
                {
                    $strActivacionLicenciasNuevasEnPlan = "SI";
                    $this->serviceActivarPuerto->activarProductosAdicionalesEnPlan(array(   "intIdServicio"     => $intIdServicioInternet,
                                                                                            "strTipoProceso"    => $strTipoProceso,
                                                                                            "strOpcion"         => "ACTIVACION",
                                                                                            "strCodEmpresa"     => $strCodEmpresa,
                                                                                            "strUsrCreacion"    => $strUsrCreacion,
                                                                                            "strClientIp"       => $strIpCreacion));
                }
                
                /**
                 * Se verifica que se haya obtenido correctamente los servicios adicionales
                 */
                if($strStatusInfoServiciosInternetProtegido === "OK")
                {
                    /**
                     * Si el plan anterior y el plan nuevo incluían Internet Protegido, pero el servicio no posee la nueva tecnología,
                     * se procede a migrar las licencias de McAfee a Kaspersky 
                     */
                    if($strPlanAnteriorTieneMcAfee === "SI" && $strPlanNuevoTieneMcAfee === "SI" && $strTieneNuevoAntivirus === "NO")
                    {
                        if(is_object($objProdInternetProtegidoAnterior))
                        {
                            $this->migrarLicenciasEnPlan(array(
                                                                "codEmpresa"                => $strCodEmpresa,
                                                                "usrCreacion"               => $strUsrCreacion,
                                                                "ipCreacion"                => $strIpCreacion,
                                                                "objServicioProdEnPlan"     => $objServicioInternet,
                                                                "objProdEnPlan"             => $objProdInternetProtegidoAnterior));
                        }
                        else
                        {
                            throw new \Exception("No se ha podido obtener el Producto Internet Protegido dentro del plan anterior al intentar "
                                                 + "migrar licencias");
                        }
                    }
                    
                    /**
                     * Si existen servicios adicionales de Internet Protegido, se procede a migrar dichas licencias adicionales de McAfee a Kaspersky
                     */
                    if($strTieneAntivirusAdicional === "SI" && count($arrayServiciosAdicAntivirusAnterior) > 0)
                    {
                        $objServicioTecnicoInternet = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                        ->findOneByServicioId($intIdServicioInternet);
                        $this->migrarLicenciasAdicionales(array(
                                                                "codEmpresa"                            => $strCodEmpresa,
                                                                "usrCreacion"                           => $strUsrCreacion,
                                                                "ipCreacion"                            => $strIpCreacion,
                                                                "arrayServiciosAdicAntivirusAnterior"   => $arrayServiciosAdicAntivirusAnterior,
                                                                "strActivacionLicenciasNuevasEnPlan"    => $strActivacionLicenciasNuevasEnPlan,
                                                                "intCantidadLicenciasNuevasEnPlan"      => $intCantidadLicenciasNuevasEnPlan,
                                                                "objProductoInternetProtegido"          => null,
                                                                "objServicioInternet"                   => $objServicioInternet,
                                                                "objServicioTecnicoInternet"            => $objServicioTecnicoInternet,
                                                                "strValorAntivirus"                     => $strValorAntivirus,
                                                                "strProcesoEjecuta"                     => $strTipoProceso));
                    }
                }
                else
                {
                    $objServHistServicio    = new InfoServicioHistorial();
                    $objServHistServicio->setServicioId($objServicioInternet);
                    $objServHistServicio->setObservacion("Los servicios Internet Protegido no pudieron ser migrados ya que el proceso presentó ".
                                                         "problemas, por favor comuníquese con Sistemas.");
                    $objServHistServicio->setEstado($objServicioInternet->getEstado());
                    $objServHistServicio->setUsrCreacion($strUsrCreacion);
                    $objServHistServicio->setFeCreacion(new \DateTime('now'));
                    $objServHistServicio->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServHistServicio);
                    $this->emComercial->flush();
                }
            }
            else 
            {
                throw new \Exception("No se han enviado todos los parámetros necesarios para ejecutar la reactivación");
            }
            $strStatus  = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->cambiarPlanLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        
        $arrayRespuesta = array("status"    => $strStatus, 
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la migración de las licencias de Internet Protegido dentro de un plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-09-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 10-10-2019 Se agrega validación para no realizar el proceso de cancelación de licencias McAfee cuando el servicio ya ha 
     *                          pasado por el proceso de migración de tecnología de Internet Protegido 
     * 
     * @param array $arrayParametros [
     *                                      "codEmpresa"                => Id de la empresa,
     *                                      "usrCreacion"               => Usuario de creación,
     *                                      "ipCreacion"                => Ip de creación,
     *                                      "intIdServicio"             => Id del servicio,
     *                                      "objServicioProdEnPlan"     => Objeto del servicio,
     *                                      "objProdEnPlan"             => Objeto del producto Internet Protegido dentro del plan,
     *                                      "strValor1ParamAntivirus"   => "NUEVO" o "MASIVO".
     *                                                                     Por defecto la función que lo usa toma el valor de "NUEVO",
     *                                      "strValor2LoginesAntivirus" => "INDIVIDUAL" o "MASIVO".
     *                                                                     Por defecto la función que lo usa toma el valor de "INDIVIDUAL",
     *                                      "arrayInfoClienteMcAfee"    => Información del cliente necesaria para realizar la cancelación de McAfee
     *                                  ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => Mensaje de error
     *                                ]
     */
    public function migrarLicenciasEnPlan($arrayParametros)
    {
        $strCodEmpresa              = $arrayParametros['codEmpresa'] ? $arrayParametros['codEmpresa'] : "18";
        $strUsrCreacion             = $arrayParametros['usrCreacion'] ? $arrayParametros['usrCreacion'] : "procesosmasivos";
        $strIpCreacion              = $arrayParametros['ipCreacion'] ? $arrayParametros['ipCreacion'] : "127.0.0.1";
        $objServicioProdEnPlan      = $arrayParametros['objServicioProdEnPlan'];
        $objProdEnPlan              = $arrayParametros['objProdEnPlan'];
        $strValor1ParamAntivirus    = $arrayParametros['strValor1ParamAntivirus']  ? $arrayParametros['strValor1ParamAntivirus'] : "";
        $strValor2LoginesAntivirus  = $arrayParametros['strValor2LoginesAntivirus'] ? $arrayParametros['strValor2LoginesAntivirus'] : "";
        $strMensaje                 = "";
        try
        {
            if(isset($arrayParametros['intIdServicio']) && !empty($arrayParametros['intIdServicio']) && !is_object($objServicioProdEnPlan))
            {
                $objServicioProdEnPlan  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->find($arrayParametros['intIdServicio']);
            }
            $intIdServicioProdEnPlan = $objServicioProdEnPlan->getId();
            
            
            $arrayParamsGetSpcMigrado   = array("objServicio"       => $objServicioProdEnPlan,
                                                "objProducto"       => $objProdEnPlan,
                                                "strCaracteristica" => "MIGRADO_A_KASPERSKY");
            $arrayRespuestaSpcMigrado   = $this->serviceLicenciasKaspersky->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcMigrado);
            if($arrayRespuestaSpcMigrado["status"] === 'OK' 
                && is_object($arrayRespuestaSpcMigrado["objServicioProdCaract"]))
            {
                $strServicioMigrado = $arrayRespuestaSpcMigrado["objServicioProdCaract"]->getValor();
            }
            else
            {
                $strServicioMigrado = 'NO';
            }
            
            if($strServicioMigrado === 'NO')
            {
                if(isset($arrayParametros["arrayInfoClienteMcAfee"]) && !empty($arrayParametros["arrayInfoClienteMcAfee"]))
                {
                    $arrayInfoClienteMcAfee = $arrayParametros["arrayInfoClienteMcAfee"];
                }
                else
                {
                    $arrayDatosCliente      = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                ->getDatosClientePorIdServicio($intIdServicioProdEnPlan, false);
                    $arrayInfoClienteMcAfee = $this->serviceLicenciasMcAfee->obtenerInformacionClienteMcAffe(array( 
                                                                                                    "intIdPersona"      => 
                                                                                                    $arrayDatosCliente['ID_PERSONA'],
                                                                                                    "intIdServicio"     => $intIdServicioProdEnPlan,
                                                                                                    "strNombrePlan"     => "",
                                                                                                    "strEsActivacion"   => "NO",
                                                                                                    "objProductoMcAfee" => $objProdEnPlan));
                }
                //Se debe cancelar el servicio McAfee anterior
                if(isset($arrayInfoClienteMcAfee) && !empty($arrayInfoClienteMcAfee))
                {
                    $arrayRespuestaCancelarLicenciasMcAfee  = $this->serviceCancelarServicio
                                                                   ->cancelarProductosAdicionalesEnPlan(array(
                                                                                                "intIdServicio"             => 
                                                                                                $intIdServicioProdEnPlan,
                                                                                                "arrayInfoClienteMcAfee"    => 
                                                                                                $arrayInfoClienteMcAfee,
                                                                                                "strUsrCreacion"            => $strUsrCreacion,
                                                                                                "strClientIp"               => $strIpCreacion));
                    $strStatusCancelarLicenciasMcAfee       = $arrayRespuestaCancelarLicenciasMcAfee["status"];
                }
                else
                {
                    $strStatusCancelarLicenciasMcAfee       = "ERROR";
                }
            
                $this->serviceLicenciasKaspersky->guardaServicioProductoCaracteristica(array(   "objServicio"       => $objServicioProdEnPlan,
                                                                                                "objProducto"       => $objProdEnPlan,
                                                                                                "strUsrCreacion"    => $strUsrCreacion,
                                                                                                "strCaracteristica" => "MIGRADO_A_KASPERSKY",
                                                                                                "strValor"          => "SI"));
                //Se eliminan las características Mcafee asociadas al servicio
                $strDataCaracteristicasMcAfee   = $this->serviceLicenciasMcAfee
                                                       ->eliminarCaracteristicasLicenciasMcAfee(array(  "objServicio"       => $objServicioProdEnPlan,
                                                                                                        "objProducto"       => $objProdEnPlan,
                                                                                                        "strUsrCreacion"    => $strUsrCreacion));
                        
                if($strStatusCancelarLicenciasMcAfee === "ERROR")
                {
                    $this->serviceLicenciasKaspersky->guardaServicioProductoCaracteristica(array(   "objServicio"       => $objServicioProdEnPlan,
                                                                                                    "objProducto"       => $objProdEnPlan,
                                                                                                    "strUsrCreacion"    => $strUsrCreacion,
                                                                                                    "strCaracteristica" => "ERROR_CANCELACION",
                                                                                                    "strValor"          => 
                                                                                                    $strDataCaracteristicasMcAfee
                                                                                                ));
                }
            }
            
            $this->serviceActivarPuerto->activarProductosAdicionalesEnPlan(array(   "intIdServicio"             => $objServicioProdEnPlan->getId(),
                                                                                    "strTipoProceso"            => "INDIVIDUAL",
                                                                                    "strOpcion"                 => "ACTIVACION",
                                                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                                                    "strClientIp"               => $strIpCreacion,
                                                                                    "strValor1ParamAntivirus"   => $strValor1ParamAntivirus,
                                                                                    "strValor2LoginesAntivirus" => $strValor2LoginesAntivirus,
                                                                                    "strPermitePlanNoVigente"   => "SI"));
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->migrarLicenciasEnPlan', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la migración de las licencias de Internet Protegido adicionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-09-2019
     * 
     * @param array $arrayParametros [
     *                                      "codEmpresa"                            => Id de la empresa,
     *                                      "usrCreacion"                           => Usuario de creación,
     *                                      "ipCreacion"                            => Ip de creación,
     *                                      "arrayServiciosAdicAntivirusAnterior"   => arreglo de servicios Internet Protegido adicionales con
     *                                                                                 tecnología McAfee,
     *                                      "strActivacionLicenciasNuevasEnPlan"    => "SI" o "NO" se han activado nuevas licencias dentro del plan,
     *                                      "intCantidadLicenciasNuevasEnPlan"      => cantidad de licencias nuevas que se activaron dentro del plan,
     *                                      "objServicioInternet"                   => Objeto del servicio de Internet,
     *                                      "objProductoInternetProtegido"          => Objeto del producto Internet Protegido,
     *                                      "strValorAntivirus"                     => "KASPERSKY",
     *                                      "strProcesoEjecuta"                     => Proceso que invoca la función: 
     *                                                                                 "CAMBIO DE PLAN MASIVO", "REACTIVACION MASIVA"
     *                                  ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => Mensaje de error
     *                                ]
     */
    public function migrarLicenciasAdicionales($arrayParametros)
    {
        $strCodEmpresa                              = $arrayParametros['codEmpresa'] ? $arrayParametros['codEmpresa'] : "18";
        $strUsrCreacion                             = $arrayParametros['usrCreacion'] ? $arrayParametros['usrCreacion'] : "procesosmasivos";
        $strIpCreacion                              = $arrayParametros['ipCreacion'] ? $arrayParametros['ipCreacion'] : "127.0.0.1";
        $arrayServiciosAdicAntivirusAnterior        = $arrayParametros['arrayServiciosAdicAntivirusAnterior'];
        $strActivacionLicenciasNuevasEnPlan         = $arrayParametros['strActivacionLicenciasNuevasEnPlan'] 
                                                      ? $arrayParametros['strActivacionLicenciasNuevasEnPlan'] : "NO";
        $intCantidadLicenciasNuevasEnPlan           = $arrayParametros['intCantidadLicenciasNuevasEnPlan'] 
                                                      ? $arrayParametros['intCantidadLicenciasNuevasEnPlan'] : 0;
        $objServicioInternet                        = is_object($arrayParametros['objServicioInternet']) 
                                                      ? $arrayParametros['objServicioInternet'] : null;
        $objServicioTecnicoInternet                 = is_object($arrayParametros['objServicioTecnicoInternet']) 
                                                      ? $arrayParametros['objServicioTecnicoInternet'] : null;
        $strValorAntivirus                          = $arrayParametros['strValorAntivirus'] ? $arrayParametros['strValorAntivirus'] : "";
        $strProcesoEjecuta                          = $arrayParametros['strProcesoEjecuta'] ? $arrayParametros['strProcesoEjecuta'] : "";
        $arrayServiciosInternetProtegidoXActivar    = array();
        $strMensaje                                 = "";
        try
        {
            /**
             * Cancelar todas las licencias adicionales de I. Protegido y marcar como error las licencias 
             * que no pudieron ser canceladas correctamente para realizarlo de forma manual
             */
            if(isset($arrayParametros['objProductoInternetProtegido']) && !empty($arrayParametros['objProductoInternetProtegido'])
               && is_object($arrayParametros['objProductoInternetProtegido']))
            {
                $objProductoInternetProtegido   = $arrayParametros['objProductoInternetProtegido'];
            }
            else
            {
                $objProductoInternetProtegido   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                    ->findOneByDescripcionProducto("I. PROTEGIDO MULTI PAID");
            }
            $objAccionCancelar          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find(313);
            $objAdmiMotivoCancelarRegu  = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                          ->findOneBy(array("nombreMotivo" => "Cancelacion por Regularizacion"));

            if(is_object($objAdmiMotivoCancelarRegu) && is_object($objAccionCancelar))
            {
                foreach ($arrayServiciosAdicAntivirusAnterior as $arrayServicioAdicAntivirusAnterior)
                {
                    $objServicioAdicional   = $arrayServicioAdicAntivirusAnterior['objServicioAdicional'];
                    $intIdServicioAdicional = $objServicioAdicional->getId();
                    $objProductoAdicional   = $objServicioAdicional->getProductoId();
                    
                    $arrayRespuestaCancelServicioAdicional  = $this->serviceCancelarServicio
                                                                   ->cancelarServiciosOtros(array(  'idServicio'        => $intIdServicioAdicional,
                                                                                                    'idEmpresa'         => $strCodEmpresa,
                                                                                                    'idAccion'          => 313,
                                                                                                    'idMotivo'          => 
                                                                                                    $objAdmiMotivoCancelarRegu->getId(),
                                                                                                    'usrCreacion'       => $strUsrCreacion,
                                                                                                    'clientIp'          => $strIpCreacion,
                                                                                                    "strMsjHistorial"   => 
                                                                                                    "Se canceló el servicio ".
                                                                                                    $objProductoAdicional->getDescripcionProducto().
                                                                                                    " con tecnología MCAFEE"));
                    
                    $strStatusCancelServicioAdicional       = $arrayRespuestaCancelServicioAdicional['status'];
                    
                    $this->serviceLicenciasKaspersky->guardaServicioProductoCaracteristica(array(   "objServicio"       => $objServicioAdicional,
                                                                                                    "strUsrCreacion"    => $strUsrCreacion,
                                                                                                    "strCaracteristica" => "MIGRADO_A_KASPERSKY",
                                                                                                    "strValor"          => "SI"));
                    //Se eliminan características Mcafee asociadas al servicio
                    $strDataCaracteristicasMcAfee   = $this->serviceLicenciasMcAfee
                                                           ->eliminarCaracteristicasLicenciasMcAfee(array(  "objServicio"       => 
                                                                                                            $objServicioAdicional,
                                                                                                            "objProducto"       => 
                                                                                                            $objServicioAdicional->getProductoId(),
                                                                                                            "strUsrCreacion"    => $strUsrCreacion,
                                                                                                            "strProcesoEjecuta" => 
                                                                                                            $strProcesoEjecuta));

                    if($strStatusCancelServicioAdicional === "ERROR")
                    {
                        $this->serviceLicenciasKaspersky
                             ->guardaServicioProductoCaracteristica(array(  "objServicio"       => $objServicioAdicional,
                                                                            "strUsrCreacion"    => $strUsrCreacion,
                                                                            "strCaracteristica" => "ERROR_CANCELACION",
                                                                            "strValor"          => $strDataCaracteristicasMcAfee));
                        //Se cancela el servicio lógicamente
                        $objServicioAdicional->setEstado("Activo");
                        $this->emComercial->persist($objServicioAdicional);
                        $this->emComercial->flush();

                        $objServicioAdicional->setEstado("Cancel");
                        $this->emComercial->persist($objServicioAdicional);
                        $this->emComercial->flush();

                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicioAdicional);
                        $objServicioHistorial->setObservacion("Se canceló el servicio ".$objProductoAdicional->getDescripcionProducto().
                                                              " con tecnología MCAFEE de manera lógica");
                        $objServicioHistorial->setEstado("Cancel");
                        $objServicioHistorial->setMotivoId($objAdmiMotivoCancelarRegu->getId());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $objServicioHistorial->setAccion($objAccionCancelar->getNombreAccion());
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                    }
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioAdicional);
                    $objServicioHistorial->setObservacion("Servicio cancelado por migración de tecnología de Internet Protegido");
                    $objServicioHistorial->setEstado("Cancel");
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
            
            /**
             * Activar licencias adicionales:
             * - Si el cambio de plan incluye activación de licencias dentro del plan se deben restar 
             *   las licencias incluidas en el plan con las licencias adicionales que tenia el cliente
             * - Se deben activar la mayor cantidad de licencias que alcancen con lo que actualmente  
             *   estaba pagando el cliente, ir restando el dinero que pagaba el cliente y  restar las 
             *   licencias activadas contra las licencias que tenía pendiente de activar y validar si aún 
             *   sobran licencias a activar, si aún sobran licencias a activar y aún queda dinero se deben
             *   activar la licencias restantes, en caso de que el dinero restante no alcance para activar las 
             *   opciones de licencias pendientes el cliente deberá solicitar luego del cambio de plan la
             *   activación de nuevas licencias Kaspersky
             *   
             */
            $intTotalLicenciasAdicionales = 0;
            $intPrecioVentaTotalLicencias = 0;
            foreach ($arrayServiciosAdicAntivirusAnterior as $arrayServicioAdicAntivirusAnterior)
            {
                $intTotalLicenciasAdicionales += $arrayServicioAdicAntivirusAnterior['intCantidadDispositivos'];
                $intPrecioVentaTotalLicencias += $arrayServicioAdicAntivirusAnterior['intPrecioVenta'];
            }
            $intLicenciasRestantes = $intTotalLicenciasAdicionales;
            if ($strActivacionLicenciasNuevasEnPlan === "SI")
            {
                $intLicenciasRestantes -= $intCantidadLicenciasNuevasEnPlan;
            }
            
            $strNombreParametro         = 'ANTIVIRUS_KASPERSKY_LICENCIAS_MD';
            $strModuloParametro         = 'TECNICO';
            $strProcesoParametro        = 'LICENCIAS_PERMITIDAS';
            $arrayCantidadLicencias     = array();
            $arrayNumLicenciasKaspersky = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->get($strNombreParametro, 
                                                                $strModuloParametro, 
                                                                $strProcesoParametro, 
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $strCodEmpresa);
            if(is_array($arrayNumLicenciasKaspersky) && count($arrayNumLicenciasKaspersky) > 0)
            {
                foreach($arrayNumLicenciasKaspersky as $arrayNumLicenciaKaspersky)
                {
                    $arrayCantidadLicencias[] = $arrayNumLicenciaKaspersky['valor1'];
                }
            }
            //Se ordenan las licencias de mayor a menor
            rsort($arrayCantidadLicencias);
            
            $strAgregaProducto              = "SI";
            $intPrecioLicenciasRestantes    = $intPrecioVentaTotalLicencias;
            $arrayServiciosKasperskyActivar = array();
            while($intLicenciasRestantes       >  0 &&
                  $intPrecioLicenciasRestantes >  0 &&
                  $strAgregaProducto           == "SI")
            {
                foreach($arrayCantidadLicencias as $intCantidadLicencias)
                {
                    $strAgregaProducto                          = "NO";
                    $intPrecioServicioInternetProtegido         = 0;
                    $strFuncionPrecioProductoInternetProtegido  = $objProductoInternetProtegido->getFuncionPrecio();
                    $arrayParamsReemplazar                      = array('[TIENE INTERNET]','[CANTIDAD DISPOSITIVOS]','PRECIO');
                    $arrayValoresReemplazar                     = array('"SI"',$intCantidadLicencias, '$intPrecioServicioInternetProtegido');
                    $strFuncionPrecioProductoInternetProtegido  = str_replace($arrayParamsReemplazar, $arrayValoresReemplazar, 
                                                                              $strFuncionPrecioProductoInternetProtegido);
                    $strDigitoVerificacion                      = substr($strFuncionPrecioProductoInternetProtegido, -1, 1);
                    if(is_numeric($strDigitoVerificacion))
                    {
                        $strFuncionPrecioProductoInternetProtegido = $strFuncionPrecioProductoInternetProtegido . ";";
                    }
                    eval($strFuncionPrecioProductoInternetProtegido);
                    if ($intPrecioServicioInternetProtegido <= $intPrecioLicenciasRestantes)
                    {
                        $intLicenciasRestantes       -= $intCantidadLicencias;
                        $intPrecioLicenciasRestantes -= $intPrecioServicioInternetProtegido;
                        $arrayServiciosKasperskyActivar[] = array(
                                                                   "intCantidadLicenciasInternetProtegido"  => $intCantidadLicencias,
                                                                   "objProductoInternetProtegido"           => $objProductoInternetProtegido,
                                                                   "intPrecioServicioInternetProtegido"     => $intPrecioServicioInternetProtegido
                                                                 );
                        $strAgregaProducto = "SI";
                        break;
                    }
                }
            }
            
            $strUsrCreacionMigraMcAfee          = "MigraMcAfee";
            //se realizan las activaciones de kaspersky que aplican al cliente luego de toda la revisión previa realizada.
            foreach($arrayServiciosKasperskyActivar as $arrayLicenciaKasperskyActivar)
            {
                //crear servicio adicional
                $arrayRespuestaCreaServicio = $this->creaServicioInternetProtegido(array(
                                        "objPunto"                      => $objServicioInternet->getPuntoId(),
                                        "objServicioInternet"           => $objServicioInternet,
                                        "objServicioTecnicoInternet"    => $objServicioTecnicoInternet,
                                        "usrCreacion"                   => $strUsrCreacionMigraMcAfee,
                                        "ipCreacion"                    => $strIpCreacion,
                                        "codEmpresa"                    => $strCodEmpresa,
                                        "intPrecioServicio"             => $arrayLicenciaKasperskyActivar['intPrecioServicioInternetProtegido'],
                                        "objProdInternetProtegido"      => $arrayLicenciaKasperskyActivar['objProductoInternetProtegido']
                                    ));
                
                if ($arrayRespuestaCreaServicio['strStatus'] == "OK")
                {
                    $objServicioInternetProtegidoNuevo      = $arrayRespuestaCreaServicio['objServicio'];
                    $objOrdenTrabajoInternetProtegidoNuevo  = $arrayRespuestaCreaServicio['objOrdenTrabajo'];
                    $this->serviceLicenciasKaspersky
                         ->guardaServicioProductoCaracteristica(array(  "objServicio"       => $objServicioInternetProtegidoNuevo,
                                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                                        "strCaracteristica" => "MIGRADO_A_KASPERSKY",
                                                                        "strValor"          => "SI"
                                                                ));
                    
                    $arrayServiciosInternetProtegidoXActivar[]  = array("objServicio"       => $objServicioInternetProtegidoNuevo,
                                                                        "objOrdenTrabajo"   => $objOrdenTrabajoInternetProtegidoNuevo);
                    //Crear caracteristicas: correo, tiene_internet, cantidad_licencias
                    $strCorreoLicencias = $this->serviceLicenciasKaspersky
                                               ->getCorreoLicencias(array("intIdPunto"     => $objServicioInternet->getPuntoId()->getId(),
                                                                          "strUsrCreacion" => $strUsrCreacionMigraMcAfee,
                                                                          "strIpCreacion"  => $strIpCreacion));
                    if(!empty($strCorreoLicencias))
                    {
                        $this->serviceLicenciasKaspersky
                             ->guardaServicioProductoCaracteristica(array( "objServicio"       => $objServicioInternetProtegidoNuevo,
                                                                           "strUsrCreacion"    => $strUsrCreacionMigraMcAfee,
                                                                           "strCaracteristica" => "CORREO ELECTRONICO",
                                                                           "strValor"          => $strCorreoLicencias
                                                                   ));
                    }
                    
                    $this->serviceLicenciasKaspersky
                         ->guardaServicioProductoCaracteristica(array( "objServicio"       => $objServicioInternetProtegidoNuevo,
                                                                       "strUsrCreacion"    => $strUsrCreacionMigraMcAfee,
                                                                       "strCaracteristica" => "TIENE INTERNET",
                                                                       "strValor"          => "SI"
                                                               ));
                    $this->serviceLicenciasKaspersky
                         ->guardaServicioProductoCaracteristica(array( "objServicio"       => $objServicioInternetProtegidoNuevo,
                                                                       "strUsrCreacion"    => $strUsrCreacionMigraMcAfee,
                                                                       "strCaracteristica" => "CANTIDAD DISPOSITIVOS",
                                                                       "strValor"          => 
                                                                       $arrayLicenciaKasperskyActivar["intCantidadLicenciasInternetProtegido"]
                                                               ));
                    
                    $this->serviceLicenciasKaspersky
                         ->guardaServicioProductoCaracteristica(array( "objServicio"       => $objServicioInternetProtegidoNuevo,
                                                                       "strUsrCreacion"    => $strUsrCreacionMigraMcAfee,
                                                                       "strCaracteristica" => "ANTIVIRUS",
                                                                       "strValor"          => $strValorAntivirus
                                                               ));
                    
                }
                else
                {
                    $strObservacionHist  = "No se logró crear servicio de I. Protegido, ".
                                           "los datos a usar para crear manualmente son los siguientes:<br>";
                    $strObservacionHist .= "CantidadLicencias: ".$arrayLicenciaKasperskyActivar['intCantidadLicenciasInternetProtegido']."<br>";
                    $strObservacionHist .= "PrecioServicio   : ".$arrayLicenciaKasperskyActivar['intPrecioServicioInternetProtegido']."<br>";
                    $strObservacionHist .= "TieneInternet    :  SI<br>";
                    //Crear historial en internet para indicar que no se pudo crear el servicio adicional migrado
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioInternet);
                    $objServicioHistorial->setObservacion($strObservacionHist);
                    $objServicioHistorial->setEstado($objServicioInternet->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }
            }
            if(isset($arrayServiciosInternetProtegidoXActivar) && !empty($arrayServiciosInternetProtegidoXActivar))
            {
                foreach($arrayServiciosInternetProtegidoXActivar as $arrayServicioNuevoXActivar)
                {
                    $objServicioNuevoXActivar               = $arrayServicioNuevoXActivar["objServicio"];
                    $objProdServicioNuevoXActivar           = $objServicioNuevoXActivar->getProductoId();
                    $objOrdenTrabajoServicioNuevoXActivar   = $arrayServicioNuevoXActivar["objOrdenTrabajo"];
                    $arrayRespuestaActivaServiciodicional   = $this->serviceActivarPuerto->activarServiciosOtros(
                                                                        array(  "idServicio"        => $objServicioNuevoXActivar->getId(),
                                                                                "idEmpresa"         => $strCodEmpresa,
                                                                                "idOficina"         => 0,
                                                                                "idAccion"          => 847,
                                                                                "usrCreacion"       => $strUsrCreacionMigraMcAfee,
                                                                                "clientIp"          => $strIpCreacion,
                                                                                "objOrdenTrabajo"   => $objOrdenTrabajoServicioNuevoXActivar,
                                                                                "strMsjHistorial"   => 
                                                                                "Se activó el servicio ".
                                                                                $objProductoInternetProtegido->getDescripcionProducto().
                                                                                " con tecnología ".$strValorAntivirus
                                                                            ));
                    if($objServicioInternet->getEstado() === "In-Corte" 
                        && ($strProcesoEjecuta === "CAMBIO DE PLAN" || $strProcesoEjecuta === "CAMBIO DE PLAN MASIVO"))
                    {
                        if($arrayRespuestaActivaServiciodicional['status'] === "OK")
                        {
                            $arrayRespuestaCorteServicioAdicional   = $this->serviceCortarServicio
                                                                           ->cortarServiciosOtros(array(
                                                                                     "idServicio"        => $objServicioNuevoXActivar->getId(),
                                                                                     "usrCreacion"       => $strUsrCreacionMigraMcAfee,
                                                                                     "clientIp"          => $strIpCreacion,
                                                                                     "strCodEmpresa"     => $strCodEmpresa,
                                                                                     "idAccion"          => 311,
                                                                                     "strMsjHistorial"   => 
                                                                                     "Se cortó el servicio "
                                                                                     .$objProdServicioNuevoXActivar->getDescripcionProducto()
                                                                                     ." con tecnología ".$strValorAntivirus
                                                                                                       ));
                            if($arrayRespuestaCorteServicioAdicional["status"] === "ERROR")
                            {
                                $objServicioNuevoXActivar->setEstado("In-Corte");
                                $this->emComercial->persist($objServicioNuevoXActivar);
                                $this->emComercial->flush();

                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicioNuevoXActivar);
                                $objServicioHistorial->setObservacion("Se ha realizado el corte del Internet Protegido de manera lógica");
                                $objServicioHistorial->setEstado($objServicioNuevoXActivar->getEstado());
                                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServicioHistorial);
                                $this->emComercial->flush();
                            }
                        }
                        else
                        {
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicioNuevoXActivar);
                            $objServicioHistorial->setObservacion("Servicio requiere realizar corte luego de la activación");
                            $objServicioHistorial->setEstado($objServicioNuevoXActivar->getEstado());
                            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setIpCreacion($strIpCreacion);
                            $this->emComercial->persist($objServicioHistorial);
                            $this->emComercial->flush();
                        }
                    }
                }
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->migrarLicenciasAdicionales', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje );
        return $arrayRespuesta;
    }
    
    /**
     * creaServicioIntProtegido
     * 
     * Crea un servicio Internet Protegido por migración de cliente a tecnología Kaspersky
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 12-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 03-09-2019 Se traspasa la función a este service ya que no sólo va a ser usado en cambio de plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 14-10-2019 Se agregan los meses restantes asociado al servicio
     * 
     * @param array $arrayParametros [ "objPunto"                      => Objeto del punto,
     *                                  "objServicioInternet"           => Objeto del servicio del Internet,
     *                                  "objServicioTecnicoInternet"    => Objeto del servicio técnico del Internet,
     *                                  "usrCreacion"                   => Usuario de creación,
     *                                  "ipCreacion"                    => Ip de creación,
     *                                  "codEmpresa"                    => Id de la empresa,
     *                                  "intPrecioServicio"             => Precio del servicio a crear,
     *                                  "objProdInternetProtegido"      => Objeto de producto internet protegido
     *                               ]
     * @return array $arrayRespuesta[
     *                                  "status"            => OK o ERROR
     *                                  "mensaje"           => mensaje de error
     *                                  "objServicio"       => objeto creado,
     *                                  "objOrdenTrabajo"   => objeto de la orden de trabajo asociada al servicio creado
     *                              ]
     */
    public function creaServicioInternetProtegido($arrayParametros)
    {
        $objPunto                       = $arrayParametros['objPunto'];
        $objServicioInternet            = $arrayParametros['objServicioInternet'];
        $objServicioTecnicoInternet     = $arrayParametros['objServicioTecnicoInternet'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];       
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $strCodEmpresa                  = $arrayParametros['codEmpresa'];
        $intPrecioServicio              = $arrayParametros['intPrecioServicio'];
        $objProdInternetProtegido       = $arrayParametros['objProdInternetProtegido'];
        $strMensaje                     = "";
        try
        {
            if(!is_object($objServicioTecnicoInternet))
            {
                throw new \Exception("No se ha enviado el servicio técnico del Internet");
            }
            
            $strUsrVendedor = $objServicioInternet->getUsrVendedor();
            if(empty($strUsrVendedor))
            {
                $strUsrVendedor = $objPunto->getUsrVendedor();
            }
            $intIdOficinaVendedor                           = 0;
            $arrayParametrosVendedor['empresa']             = $strCodEmpresa;
            $arrayParametrosVendedor['criterios']['login']  = $strUsrVendedor;
            $arrayPerVendedor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->findPersonalByCriterios($arrayParametrosVendedor);
            if( isset($arrayPerVendedor['registros']) && !empty($arrayPerVendedor['registros']) 
                && isset($arrayPerVendedor['total']) && $arrayPerVendedor['total'] > 0 )
            {
                $arrayInfoVendedor  = $arrayPerVendedor['registros'][0];
                $intIdPerVendedor   = $arrayInfoVendedor['idPersonaEmpresaRol'];
                if($intIdPerVendedor > 0)
                {
                    $objPerVendedor = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerVendedor);
                    if(is_object($objPerVendedor))
                    {
                        $objOficinaVendedor = $objPerVendedor->getOficinaId();
                        if(is_object($objOficinaVendedor))
                        {
                            $intIdOficinaVendedor = $objOficinaVendedor->getId();
                        }
                    }
                }
            }
            $objAdmiNumeracion = null;
            if($intIdOficinaVendedor > 0)
            {
                $objAdmiNumeracion  = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                        ->findByEmpresaYOficina($strCodEmpresa, $intIdOficinaVendedor, 'ORD');
            }
            
            if(!is_object($objAdmiNumeracion))
            {
                $intIdOficinaVendedor   = 58;
                $objAdmiNumeracion      = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                            ->findByEmpresaYOficina($strCodEmpresa, $intIdOficinaVendedor, 'ORD');
            }
            $strSecuenciaAsig = str_pad($objAdmiNumeracion->getSecuencia(),7, '0', STR_PAD_LEFT);
            $strNumeroOt      = $objAdmiNumeracion->getNumeracionUno().'-'.$objAdmiNumeracion->getNumeracionDos().'-'.$strSecuenciaAsig;
        
            $objInfoOrdenTrabajo  = new InfoOrdenTrabajo();
            $objInfoOrdenTrabajo->setPuntoId($objPunto);
            $objInfoOrdenTrabajo->setTipoOrden('N');
            $objInfoOrdenTrabajo->setNumeroOrdenTrabajo($strNumeroOt);
            $objInfoOrdenTrabajo->setFeCreacion(new \DateTime('now'));
            $objInfoOrdenTrabajo->setUsrCreacion($strUsrCreacion);
            $objInfoOrdenTrabajo->setIpCreacion($strIpCreacion);
            $objInfoOrdenTrabajo->setOficinaId($intIdOficinaVendedor);
            $objInfoOrdenTrabajo->setEstado('Activa');
            $this->emComercial->persist($objInfoOrdenTrabajo);
            $this->emComercial->flush();
        
            //Actualizo la numeracion en la tabla
            $strNumeracionActual = ($objAdmiNumeracion->getSecuencia()+1);
            $objAdmiNumeracion->setSecuencia($strNumeracionActual);
            $this->emComercial->persist($objAdmiNumeracion);
            $this->emComercial->flush();

            $objServicioInternetProtegido = new InfoServicio();
            $objServicioInternetProtegido->setPuntoId($objPunto);
            $objServicioInternetProtegido->setProductoId($objProdInternetProtegido);
            $objServicioInternetProtegido->setEsVenta('S');
            $objServicioInternetProtegido->setPrecioVenta($intPrecioServicio);
            $objServicioInternetProtegido->setCantidad(1);
            $objServicioInternetProtegido->setTipoOrden('N');
            $objServicioInternetProtegido->setOrdenTrabajoId($objInfoOrdenTrabajo);
            $objServicioInternetProtegido->setPuntoFacturacionId($objServicioInternet->getPuntoFacturacionId());
            $objServicioInternetProtegido->setUsrVendedor($strUsrVendedor);
            $objServicioInternetProtegido->setEstado("Pendiente");
            $objServicioInternetProtegido->setFrecuenciaProducto(1);
            $objServicioInternetProtegido->setMesesRestantes(1);
            $objServicioInternetProtegido->setDescripcionPresentaFactura($objProdInternetProtegido->getDescripcionProducto());
            $objServicioInternetProtegido->setUsrCreacion($strUsrCreacion);
            $objServicioInternetProtegido->setFeCreacion(new \DateTime('now'));
            $objServicioInternetProtegido->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioInternetProtegido);
            $this->emComercial->flush();

            //historial de creación
            $objServHistCreacionInternetProtegido = new InfoServicioHistorial();
            $objServHistCreacionInternetProtegido->setServicioId($objServicioInternetProtegido);
            $objServHistCreacionInternetProtegido->setObservacion("Se creó el servicio por migración de tecnología");
            $objServHistCreacionInternetProtegido->setEstado("Pendiente");
            $objServHistCreacionInternetProtegido->setUsrCreacion($strUsrCreacion);
            $objServHistCreacionInternetProtegido->setFeCreacion(new \DateTime('now'));
            $objServHistCreacionInternetProtegido->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServHistCreacionInternetProtegido);
            $this->emComercial->flush();
            
            $objServicioTecnicoInternetProtegido = new InfoServicioTecnico();
            $objServicioTecnicoInternetProtegido->setServicioId($objServicioInternetProtegido);
            $objServicioTecnicoInternetProtegido->setTipoEnlace('PRINCIPAL');
            $objServicioTecnicoInternetProtegido->setUltimaMillaId($objServicioTecnicoInternet->getUltimaMillaId());
            $this->emComercial->persist($objServicioTecnicoInternetProtegido);
            $this->emComercial->flush();
            
            $strStatus  = "OK";
            $strMensaje = "<br>Se ha creado el servicio ".$objProdInternetProtegido->getDescripcionProducto()." como producto adicional";
        }
        catch (\Exception $e) 
        {
            $strStatus                      = "ERROR";
            $objServicioInternetProtegido   = null;
            $objInfoOrdenTrabajo            = null;
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->creaServicioInternetProtegido', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strMensaje = " No se ha podido crear el producto de manera automática";
        }
        $arrayRespuesta = array("strStatus"         => $strStatus,
                                "strMensaje"        => $strMensaje,
                                "objServicio"       => $objServicioInternetProtegido,
                                "objOrdenTrabajo"   => $objInfoOrdenTrabajo);
        return $arrayRespuesta;
    }
    
    
    
    
    /**
     * Función que realiza la cancelación de licencias de servicios I. PROTEGIDO MULTI PAID dentro de planes o
     * servicios adicionales I. PROTEGIDO TRIAL, I. PROTEGIDO MULTI PAID, I. PROTECCION TOTAL TRIAL, I. PROTECCION TOTAL PAID 
     * con tecnología MCAFEE y KASPERSKY
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-09-2019
     * 
     * @param array $arrayParametrosWs [
     *                                      "op"            => Opción que fue ejecutada desde el web service,
     *                                      "token"         => Token enviado al web service,
     *                                      "user"          => Usuario usado en la generación de tokens,
     *                                      "source"        => Arreglo con los parámetros necesarios para generar un token,
     *                                      "ipCreacion"    => Escenario enviado por cada proceso,
     *                                      "data"          => [
     *                                                          "idPunto"       => Id del punto,
     *                                                          "idServicio"    => Id del servicio,
     *                                                          "tipoProceso"   => "CANCELACION MASIVA",
     *                                                          "codEmpresa"    => Id de la empresa
     *                                                         ]
     *                                  ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => Mensaje de error
     *                                ]
     * 
     */
    public function cancelarLicencias($arrayParametrosWs)
    {
        $strUsrCreacion             = $arrayParametrosWs['usrCreacion'] ? $arrayParametrosWs['usrCreacion'] : "procesosmasivos";
        $strIpCreacion              = $arrayParametrosWs['ipCreacion'] ? $arrayParametrosWs['ipCreacion'] : "127.0.0.1";
        $strMensaje                 = "";
        try
        {
            if(!empty($arrayParametrosWs['data']['idServicio']) && !empty($arrayParametrosWs['data']['tipoProceso'])
                && !empty($arrayParametrosWs['data']['codEmpresa']))
            {
                $intIdServicio  = $arrayParametrosWs['data']['idServicio'];
                if(isset($arrayParametrosWs['data']['idMotivo']) && !empty($arrayParametrosWs['data']['idMotivo']))
                {
                    $intIdMotivo    = $arrayParametrosWs['data']['idMotivo'];
                }
                else
                {
                    $objAdmiMotivoCancel    = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                              ->findOneBy(array("nombreMotivo" => "CANCELACION AUTOMATICA"));
                    $intIdMotivo            = $objAdmiMotivoCancel->getId();
                    
                }
                $strTipoProceso = $arrayParametrosWs['data']['tipoProceso'];
                $strCodEmpresa  = $arrayParametrosWs['data']['codEmpresa'];
                $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if(is_object($objServicio))
                {
                    if(is_object($objServicio->getPlanId()))
                    {
                        $arrayRespuestaVerificaProdEnPlan   = $this->verificaProductosEnPlan(array( "intIdPlan" => $objServicio->getPlanId()->getId(),
                                                                                                    "strDescripcionProducto"    => 
                                                                                                    "I. PROTEGIDO MULTI PAID")
                                                                                    );
                        $strPlanTieneProductoInternetProtegido  = $arrayRespuestaVerificaProdEnPlan["strPlanTieneProducto"];
                        $objProdInternetProtegido               = $arrayRespuestaVerificaProdEnPlan["objProductoEnPlan"];
                        if($strPlanTieneProductoInternetProtegido === "SI")
                        {
                            $arrayParamsGetSpcCorreo        = array("objServicio"       => $objServicio,
                                                                    "objProducto"       => $objProdInternetProtegido,
                                                                    "strCaracteristica" => "CORREO ELECTRONICO");
                            $arrayRespuestaSpcCorreo        = $this->serviceLicenciasKaspersky
                                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCorreo);
                            if($arrayRespuestaSpcCorreo["status"] === 'OK' 
                                && is_object($arrayRespuestaSpcCorreo["objServicioProdCaract"]))
                            {
                                $strCorreoSuscripcion   = $arrayRespuestaSpcCorreo["objServicioProdCaract"]->getValor();
                            }
                            else
                            {
                                $strCorreoSuscripcion   = "";
                            }
                            
                            $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                                    "objProducto"       => $objProdInternetProtegido,
                                                                    "strCaracteristica" => "SUSCRIBER_ID");
                            $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                            if($arrayRespuestaSpcSuscriberId["status"] === 'OK' 
                                && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
                            {
                                $strTieneSuscriberId    = "SI";
                                $intSuscriberId         = intval($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]->getValor());
                                $arrayInfoClienteMcAfee = array();
                            }
                            else
                            {
                                $strTieneSuscriberId    = "NO";
                                $intSuscriberId         = 0;
                                $arrayDatosCliente      = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                            ->getDatosClientePorIdServicio($intIdServicio, false);
                                $arrayInfoClienteMcAfee = $this->serviceLicenciasMcAfee->obtenerInformacionClienteMcAffe(array( 
                                                                                                "intIdPersona"      => 
                                                                                                $arrayDatosCliente['ID_PERSONA'],
                                                                                                "intIdServicio"     => $intIdServicio,
                                                                                                "strNombrePlan"     => "",
                                                                                                "strEsActivacion"   => "NO",
                                                                                                "objProductoMcAfee" => $objProdInternetProtegido));
                            }
                            
                            $arrayParamsCancelarProdsAdicEnPlan     = array(
                                                                        "intIdServicio"             => $intIdServicio,
                                                                        "arrayInfoClienteMcAfee"    => $arrayInfoClienteMcAfee,
                                                                        "strTieneSuscriberId"       => $strTieneSuscriberId,
                                                                        "intSuscriberId"            => $intSuscriberId,
                                                                        "strCorreoSuscripcion"      => $strCorreoSuscripcion,
                                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                                        "strUsrCreacion"            => $strUsrCreacion,
                                                                        "strClientIp"               => $strIpCreacion,
                                                                        "strPermitePlanNoVigente"   => "SI",
                                                                        "strPermiteEnvioCorreoError"=> "NO"
                                                                    );
                            $arrayRespuestaCancelarProdsAdicEnPlan  = $this->serviceCancelarServicio
                                                                           ->cancelarProductosAdicionalesEnPlan($arrayParamsCancelarProdsAdicEnPlan);
                            $strStatusCancelServicio                = $arrayRespuestaCancelarProdsAdicEnPlan["status"];
                            if($strTieneSuscriberId === "SI")
                            {
                                //Se eliminan características Kaspersky asociadas al servicio
                                $strDataCaracteristicasLicencias    = $this->eliminarCaracteristicasLicenciasKaspersky(array( 
                                                                                                                            "objServicio"       => 
                                                                                                                            $objServicio,
                                                                                                                            "objProducto"       => 
                                                                                                                            $objProdInternetProtegido,
                                                                                                                            "strUsrCreacion"    => 
                                                                                                                            $strUsrCreacion,
                                                                                                                            "strProcesoEjecuta" => 
                                                                                                                            $strTipoProceso));
                                $strDescripcionCaractError          = "ERROR_CANCELACION_INTERNET_PROTEGIDO";

                            }
                            else
                            {
                                //Se eliminan características Mcafee asociadas al servicio
                                $strDataCaracteristicasLicencias    = $this->serviceLicenciasMcAfee
                                                                           ->eliminarCaracteristicasLicenciasMcAfee(array(  "objServicio"       => 
                                                                                                                            $objServicio,
                                                                                                                            "objProducto"       => 
                                                                                                                            $objProdInternetProtegido,
                                                                                                                            "strUsrCreacion"    => 
                                                                                                                            $strUsrCreacion,
                                                                                                                            "strProcesoEjecuta" => 
                                                                                                                            $strTipoProceso));
                                $strDescripcionCaractError          = "ERROR_CANCELACION";
                                
                            }
                            
                            if($strStatusCancelServicio === "ERROR")
                            {
                                $this->serviceLicenciasKaspersky
                                     ->guardaServicioProductoCaracteristica(array(  "objServicio"       => $objServicio,
                                                                                    "objProducto"       => $objProdInternetProtegido,
                                                                                    "strUsrCreacion"    => $strUsrCreacion,
                                                                                    "strCaracteristica" => $strDescripcionCaractError,
                                                                                    "strValor"          => $strDataCaracteristicasLicencias
                                                                                ));
                            }
                        }                        
                    }
                    else if(is_object($objServicio->getProductoId()))
                    {
                        $objProdInternetProtegido       = $objServicio->getProductoId();
                        $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                                "strCaracteristica" => "SUSCRIBER_ID");
                        $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                               ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                        if($arrayRespuestaSpcSuscriberId["status"] === 'OK' 
                            && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
                        {
                            $strTieneSuscriberId        = "SI";
                            $strMsjHistorial            = "Se canceló el servicio ".$objProdInternetProtegido->getDescripcionProducto()
                                                          ." con tecnología KASPERSKY";
                            $strDescripcionCaractError  = "ERROR_CANCELACION_INTERNET_PROTEGIDO";
                        }
                        else
                        {
                            $strTieneSuscriberId        = "NO";
                            $strMsjHistorial            = "Se canceló el servicio ".$objProdInternetProtegido->getDescripcionProducto()
                                                          ." con tecnología MCAFEE";
                            $strDescripcionCaractError  = "ERROR_CANCELACION";
                        }
 
                        $arrayRespuestaCancelServicioAdicional  = $this->serviceCancelarServicio
                                                                       ->cancelarServiciosOtros(array(  
                                                                                                'idServicio'                    => $intIdServicio,
                                                                                                'idEmpresa'                     => $strCodEmpresa,
                                                                                                'idAccion'                      => 313,
                                                                                                'idMotivo'                      => $intIdMotivo,
                                                                                                'usrCreacion'                   => $strUsrCreacion,
                                                                                                'clientIp'                      => $strIpCreacion,
                                                                                                "strMsjHistorial"               => $strMsjHistorial,
                                                                                                "strPermiteEnvioCorreoError"    => "NO"));

                        $strStatusCancelServicio                = $arrayRespuestaCancelServicioAdicional['status'];
                        if($strTieneSuscriberId === "SI")
                        {
                            //Se eliminan características Kaspersky asociadas al servicio
                            $strDataCaracteristicasLicencias    = $this->eliminarCaracteristicasLicenciasKaspersky(array( 
                                                                                                                        "objServicio"       => 
                                                                                                                        $objServicio,
                                                                                                                        "objProducto"       => 
                                                                                                                        $objProdInternetProtegido,
                                                                                                                        "strUsrCreacion"    => 
                                                                                                                        $strUsrCreacion,
                                                                                                                        "strProcesoEjecuta" => 
                                                                                                                        $strTipoProceso));
                            $strDescripcionCaractError          = "ERROR_CANCELACION_INTERNET_PROTEGIDO";
                        }
                        else
                        {
                            //Se eliminan características Mcafee asociadas al servicio
                            $strDataCaracteristicasLicencias    = $this->serviceLicenciasMcAfee
                                                                       ->eliminarCaracteristicasLicenciasMcAfee(array(  "objServicio"       => 
                                                                                                                        $objServicio,
                                                                                                                        "objProducto"       => 
                                                                                                                        $objProdInternetProtegido,
                                                                                                                        "strUsrCreacion"    => 
                                                                                                                        $strUsrCreacion,
                                                                                                                        "strProcesoEjecuta" => 
                                                                                                                        $strTipoProceso));
                            $strDescripcionCaractError          = "ERROR_CANCELACION";

                        }
                        
                        if($strStatusCancelServicio === "ERROR")
                        {
                            $this->serviceLicenciasKaspersky
                                 ->guardaServicioProductoCaracteristica(array(  "objServicio"       => $objServicio,
                                                                                "strUsrCreacion"    => $strUsrCreacion,
                                                                                "strCaracteristica" => "$strDescripcionCaractError",
                                                                                "strValor"          => $strDataCaracteristicasLicencias));
                        }
                    }
                    else
                    {
                        throw new \Exception("No se pudo obtener el plan o producto del servicio con ID ".$intIdServicio);
                    }
                }
                else
                {
                    throw new \Exception("No se pudo obtener el objeto del servicio con ID ".$intIdServicio);
                }
            }
            else
            {
                throw new \Exception("No se han enviado todos los parámetros necesarios ");
            }
            $strStatus = "OK";
        }
        catch (\Exception $e) 
        {
            $strStatus = "ERROR";
            $this->serviceUtil->insertError('Telcos+', 
                                            'InternetProtegidoService->cancelarLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strMensaje = "No se ha podido cancelar las licencias de Internet Protegido";
        }
        $arrayRespuesta = array("status"         => $strStatus,
                                "mensaje"        => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * eliminarCaracteristicasLicenciasKaspersky
     * 
     * Función que sirve para eliminar las características de internet protegido Kaspersky y devolver string con valores de característica
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 15-09-2019
     * 
     * @param $arrayParametros
     * @return String $strValoresCaract
     */
    public function eliminarCaracteristicasLicenciasKaspersky($arrayParametros)
    {
        $objServicio        = $arrayParametros['objServicio'];
        $objProducto        = $arrayParametros['objProducto'];
        $strUsrCreacion     = $arrayParametros['strUsrCreacion'];
        $strValoresCaract   = "";
        try
        {
            $arrayParamsGetSpc                      = array("objServicio"       => $objServicio,
                                                            "objProducto"       => $objProducto);
            $arrayParamsGetSpc["strCaracteristica"] = "CORREO ELECTRONICO";
            $arrayRespuestaSpcCorreo        = $this->serviceLicenciasKaspersky
                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaSpcCorreo["status"] === 'OK' 
                && is_object($arrayRespuestaSpcCorreo["objServicioProdCaract"]))
            {
                $arrayRespuestaSpcCorreo["objServicioProdCaract"]->setEstado('Eliminado');
                $arrayRespuestaSpcCorreo["objServicioProdCaract"]->setUsrUltMod($strUsrCreacion);
                $arrayRespuestaSpcCorreo["objServicioProdCaract"]->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($arrayRespuestaSpcCorreo["objServicioProdCaract"]);
                $this->emComercial->flush();
                $strValoresCaract .= "CORREO ELECTRONICO: ".$arrayRespuestaSpcCorreo["objServicioProdCaract"]->getValor()."<br>";
            }

            $arrayParamsGetSpc["strCaracteristica"] = "TIENE INTERNET";
            $arrayRespuestaSpcTieneInternet         = $this->serviceLicenciasKaspersky
                                                            ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaSpcTieneInternet["status"] === 'OK' 
                && is_object($arrayRespuestaSpcTieneInternet["objServicioProdCaract"]))
            {
                $arrayRespuestaSpcTieneInternet["objServicioProdCaract"]->setEstado('Eliminado');
                $arrayRespuestaSpcTieneInternet["objServicioProdCaract"]->setUsrUltMod($strUsrCreacion);
                $arrayRespuestaSpcTieneInternet["objServicioProdCaract"]->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($arrayRespuestaSpcTieneInternet["objServicioProdCaract"]);
                $this->emComercial->flush();
                $strValoresCaract .= "TIENE INTERNET: ".$arrayRespuestaSpcTieneInternet["objServicioProdCaract"]->getValor()."<br>";
            }

            $arrayParamsGetSpc["strCaracteristica"] = "CANTIDAD DISPOSITIVOS";
            $arrayRespuestaSpcCantDisp              = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaSpcCantDisp["status"] === 'OK' 
                && is_object($arrayRespuestaSpcCantDisp["objServicioProdCaract"]))
            {
                $arrayRespuestaSpcCantDisp["objServicioProdCaract"]->setEstado('Eliminado');
                $arrayRespuestaSpcCantDisp["objServicioProdCaract"]->setUsrUltMod($strUsrCreacion);
                $arrayRespuestaSpcCantDisp["objServicioProdCaract"]->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($arrayRespuestaSpcCantDisp["objServicioProdCaract"]);
                $this->emComercial->flush();
                $strValoresCaract .= "CANTIDAD DISPOSITIVOS: ".$arrayRespuestaSpcCantDisp["objServicioProdCaract"]->getValor()."<br>";
            }

            $arrayParamsGetSpc["strCaracteristica"] = "SUSCRIBER_ID";
            $arrayRespuestaSpcSuscriberId           = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaSpcSuscriberId["status"] === 'OK' 
                && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
            {
                $arrayRespuestaSpcSuscriberId["objServicioProdCaract"]->setEstado('Eliminado');
                $arrayRespuestaSpcSuscriberId["objServicioProdCaract"]->setUsrUltMod($strUsrCreacion);
                $arrayRespuestaSpcSuscriberId["objServicioProdCaract"]->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]);
                $this->emComercial->flush();
                $strValoresCaract .= "SUSCRIBER ID".$arrayRespuestaSpcSuscriberId["objServicioProdCaract"]->getValor()."<br>";
            }

            $arrayParamsGetSpc["strCaracteristica"] = "NUMERO REINTENTOS";
            $arrayRespuestaSpcNumReintentos         = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaSpcNumReintentos["status"] === 'OK' 
                && is_object($arrayRespuestaSpcNumReintentos["objServicioProdCaract"]))
            {
                $arrayRespuestaSpcNumReintentos["objServicioProdCaract"]->setEstado('Eliminado');
                $arrayRespuestaSpcNumReintentos["objServicioProdCaract"]->setUsrUltMod($strUsrCreacion);
                $arrayRespuestaSpcNumReintentos["objServicioProdCaract"]->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($arrayRespuestaSpcNumReintentos["objServicioProdCaract"]);
                $this->emComercial->flush();
                $strValoresCaract .= "NUMERO REINTENTOS: ".$arrayRespuestaSpcNumReintentos["objServicioProdCaract"]->getValor()."<br>";
            }

            if(!empty($strValoresCaract) && isset($arrayParametros['strProcesoEjecuta']) && !empty($arrayParametros['strProcesoEjecuta']))
            {
                $strValoresCaract = "PROCESO: ".$arrayParametros['strProcesoEjecuta']."<br>".$strValoresCaract;
            }
        }
        catch (\Exception $e)
        {
            error_log("No se han podido eliminar las características relacionadas con Kaspersky".$e->getMessage());
            $strValoresCaract = "";
        }
        return $strValoresCaract;
    }
    
    
}
