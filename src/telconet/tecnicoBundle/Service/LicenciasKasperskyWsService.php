<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/**
 * Clase para invocar a métodos para la activación, supensión, reactivación y cancelación de licencias Kaspersky.
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 25-07-2019
 */
class LicenciasKasperskyWsService
{
    private $objContainer;
    private $strUrlWsKaspersky;
    private $strEjecutaCorreoTestWsKaspersky;
    private $strCorreoTestWsKaspersky;
    private $emComercial;
    private $serviceUtil;
    private $serviceEnvioPlantilla;
    private $serviceLicenciasKaspersky;
    private $serviceRestClient;
    public static $intStatusOk = 200;
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer                     = $objContainer;
        $this->strUrlWsKaspersky                = $objContainer->getParameter('ws_kaspersky_url');
        $this->strEjecutaCorreoTestWsKaspersky  = $objContainer->getParameter('ws_kaspersky_execute_correo_test');
        $this->strCorreoTestWsKaspersky         = $objContainer->getParameter('ws_kaspersky_correo_test');
        $this->emComercial                      = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral                        = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->serviceUtil                      = $objContainer->get('schema.Util');
        $this->serviceEnvioPlantilla            = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceRestClient                = $objContainer->get('schema.RestClient');
        $this->serviceLicenciasKaspersky        = $objContainer->get('tecnico.LicenciasKaspersky');
    }
    
    /**
     * Función que genera la orden de trabajo para servicios adicionales I. PROTEGIDO MULTI PAID
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 31-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-09-2019 Se actualiza el estado de la orden de trabajo, ya que la mayoría de proceso usa el estado Activa.
     *                          Sin embargo no existe afectación en los procesos de Facturación ya que no se consulta por estado
     * 
     * @param array $arrayParametros [
     *                                  "objPunto"          => objeto del punto,
     *                                  "strCodEmpresa"     => código de la empresa,
     *                                  "intIdOficina"      => id de la oficina
     *                                  "strUsrCreacion"    => usuario de la creación,
     *                                  "strIpCreacion"     => ip de la creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje para el usuario,
     *                                  "objOrdenTrabajo"   => ibjeto de la orden de trabajo
     *                                ]
     * 
     */
    public function generaOrdenDeTrabajo($arrayParametros)
    {
        $objPunto               = $arrayParametros['objPunto'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $intIdOficina           = $arrayParametros['intIdOficina'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $strMostrarError        = "NO";
        try
        {
            $objDatosNumeracion = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina(  $strCodEmpresa,
                                                                                                                            $intIdOficina,
                                                                                                                            'ORD');
            if(!is_object($objDatosNumeracion))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha podido obtener la numeración para generar la orden de trabajo");
            }
            $strSecuencia           = str_pad($objDatosNumeracion->getSecuencia(),7, '0', STR_PAD_LEFT);
            $strNumeroOrdenTrabajo  = $objDatosNumeracion->getNumeracionUno().'-'.$objDatosNumeracion->getNumeracionDos().'-'.$strSecuencia;
            $objOrdenTrabajo        = new InfoOrdenTrabajo();
            $objOrdenTrabajo->setPuntoId($objPunto);
            $objOrdenTrabajo->setTipoOrden('N');
            $objOrdenTrabajo->setNumeroOrdenTrabajo($strNumeroOrdenTrabajo);
            $objOrdenTrabajo->setFeCreacion(new \DateTime('now'));
            $objOrdenTrabajo->setUsrCreacion($strUsrCreacion);
            $objOrdenTrabajo->setIpCreacion($strIpCreacion);
            $objOrdenTrabajo->setOficinaId($intIdOficina);
            $objOrdenTrabajo->setEstado('Activa');
            $this->emComercial->persist($objOrdenTrabajo);
            $this->emComercial->flush();
            $strStatus  = "OK";
            $strMensaje = "Orden de Trabajo creada correctamente";
        }
        catch(\Exception $e)
        {
            $strStatus          = "ERROR";
            $objOrdenTrabajo    = null;
            if($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "Se produjo un problema al generar la orden de trabajo. Por favor notifique a Sistemas!";
            }
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKaspersky->serviceLicenciasKasperskyWs', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"            => $strStatus, 
                                "mensaje"           => $strMensaje,
                                "objOrdenTrabajo"   => $objOrdenTrabajo);
        return $arrayRespuesta;
    }
    
    /**
     * Función que envía la notificación al no poder cancelarse las licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strEstadoServicioInicial"  => estado actual del servicio al invocar a esta función,
     *                                  "strObservacion"            => observación enviada en el correo
     *                                ]
     * 
     */
    public function envioNotifErrorCancelacionLicencias($arrayParametros)
    {
        $objServicio                = $arrayParametros["objServicio"];
        $objProductoIPMP            = $arrayParametros["objProductoIPMP"];
        $strObservacion             = $arrayParametros["strObservacion"];
        $strEstadoServicioInicial   = $arrayParametros["strEstadoServicioInicial"];
        try
        {
            if(is_object($objServicio->getPlanId()))
            {
                $strTipoServicio        = "Plan";
                $strNombreServicio      = $objServicio->getPlanId()->getNombrePlan();
                $strDescripcionServicio = "incluido en el plan ";
            }
            else
            {
                $objProductoIPMP        = $objServicio->getProductoId();
                $strTipoServicio        = "Producto";
                $strNombreServicio      = $objProductoIPMP->getDescripcionProducto();
                $strDescripcionServicio = "como producto adicional ";
            }
            $objPunto               = $objServicio->getPuntoId();
            $objPersonaEmpresaRol   = $objPunto->getPersonaEmpresaRolId();
            $objPersona             = $objPersonaEmpresaRol->getPersonaId();
            $objJurisdiccion        = $objPunto->getPuntoCoberturaId();
            $strLogin               = $objPunto->getLogin();
            $strNombreCliente       = sprintf("%s",$objPersona);
            if(is_object($objJurisdiccion))
            {
                $strNombreJurisdiccion  = $objJurisdiccion->getNombreJurisdiccion();
            }
            else
            {
                $strNombreJurisdiccion  = "";
            }
            if(isset($arrayParametros["intSuscriberId"]) && !empty($arrayParametros["intSuscriberId"]))
            {
                $strSuscriberId = $arrayParametros["intSuscriberId"];
            }
            else
            {
                $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                        "objProducto"       => $objProductoIPMP,
                                                        "strCaracteristica" => "SUSCRIBER_ID");
                $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                       ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                if(isset($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]) && !empty($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]) 
                    && is_object($arrayRespuestaSpcSuscriberId["objServicioProdCaract"]))
                {
                    $strSuscriberId = $arrayRespuestaSpcSuscriberId["objServicioProdCaract"]->getValor();
                }
                else
                {
                    $strSuscriberId = "";
                }
            }
            if(isset($arrayParametros["strCorreoSuscripcion"]) && !empty($arrayParametros["strCorreoSuscripcion"]))
            {
                $strCorreoSuscripcion = $arrayParametros["strCorreoSuscripcion"];
            }
            else
            {
                $arrayParamsGetSpcCorreo    = array("objServicio"       => $objServicio,
                                                    "objProducto"       => $objProductoIPMP,
                                                    "strCaracteristica" => "CORREO ELECTRONICO");
                $arrayRespuestaSpcCorreo    = $this->serviceLicenciasKaspersky
                                                   ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCorreo);
                if(isset($arrayRespuestaSpcCorreo["objServicioProdCaract"]) && !empty($arrayRespuestaSpcCorreo["objServicioProdCaract"]) 
                    && is_object($arrayRespuestaSpcCorreo["objServicioProdCaract"]))
                {
                    $strCorreoSuscripcion = $arrayRespuestaSpcCorreo["objServicioProdCaract"]->getValor();
                }
                else
                {
                    $strCorreoSuscripcion = "";
                }
            }
            $strAsuntoCorreo        = "Error al cancelar licencias de ".$objProductoIPMP->getDescripcionProducto()." - ".$strLogin;
            $arrayParametrosMail    = array( 
                                                "nombreProducto"        => $objProductoIPMP->getDescripcionProducto(),
                                                "descripcionServicio"   => $strDescripcionServicio,
                                                "cliente"               => $strNombreCliente,
                                                "login"                 => $strLogin,
                                                "nombreJurisdiccion"    => $strNombreJurisdiccion,
                                                "tipoServicio"          => $strTipoServicio,
                                                "nombreServicio"        => $strNombreServicio,
                                                "observacion"           => $strObservacion,
                                                "estadoServicio"        => $strEstadoServicioInicial,
                                                "correoSuscripcion"     => $strCorreoSuscripcion,
                                                "suscriberId"           => $strSuscriberId
                                        );
            $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                                array(), 
                                                                'ERRORCANCELIPMP', 
                                                                $arrayParametrosMail,
                                                                '',
                                                                '',
                                                                '',
                                                                null, 
                                                                true,
                                                                'notificacionesnetlife@netlife.info.ec');
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKasperskyWs->envioNotifErrorCancelacionLicencias', 
                                            $e->getMessage(), 
                                            "telcos", 
                                            "127.0.0.1"
                                           );
        }
    }
    
    /**
     * Función que obtiene la respuesta del ws para la activación de Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2019 Se agrega la obtención de la variable strMsjTecnologia en dónde se especifica la tecnología del servicio
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"           => objeto del servicio,
     *                                  "objProducto"           => objeto del producto,
     *                                  "strTipoServicio"       => PLAN o PRODUCTO,
     *                                  "strCodEmpresa"         => id de la empresa en sesión,
     *                                  "strUsrCreacion"        => usuario de creación,
     *                                  "strClientIp"           => ip del cliente
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"        => OK o ERROR,
     *                                  "mensaje"       => mensaje de error,
     *                                  "arrayDataIPMP" => información del suscriber id, código del producto y el correo de la suscripción
     *                                ]
     * 
     */
    public function activacionWsProductoIPMP($arrayParametros)
    {
        $objServicio            = $arrayParametros["objServicio"];
        $objProductoIPMP        = $arrayParametros["objProducto"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strClientIp            = $arrayParametros['strClientIp'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'] ? $arrayParametros['strCodEmpresa'] : "18";
        $strTipoServicio        = $arrayParametros['strTipoServicio'] ? $arrayParametros['strTipoServicio'] : "PLAN";
        $strMsjTecnologia       = $arrayParametros['strMsjTecnologia'] ? $arrayParametros['strMsjTecnologia'] : "";
        $arrayDataIPMP          = array();
        $strMostrarError        = "NO";
        $strMensaje             = "";
        try
        {
            if ($strTipoServicio === "PLAN" 
                && ($objServicio->getEstado() !== 'Activo' &&  $objServicio->getEstado() !=='EnVerificacion' 
                    && $objServicio->getEstado() !=='EnPruebas'))
            {
                $strMostrarError = "SI";
                throw new \Exception("El punto no tiene un servicio de Internet en estado Activo");
            }
            
            if ($strTipoServicio === "PRODUCTO" && $objServicio->getEstado() !== 'Pendiente')
            {
                $strMostrarError = "SI";
                throw new \Exception("El servicio no se encuentra en estado Pendiente");
            }
            
            $arrayParamsGetSpc                      = array("objServicio"       => $objServicio,
                                                            "objProducto"       => $objProductoIPMP);
            $arrayParamsGetSpc["strCaracteristica"] = 'CORREO ELECTRONICO';
            $arrayRespuestaGetSpc                   = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpc);
            if($arrayRespuestaGetSpc["status"] === 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
            }
            $objSpcCorreoElectronico = $arrayRespuestaGetSpc["objServicioProdCaract"];
            if(is_object($objSpcCorreoElectronico))
            {
                $strCorreoLicencias                     = $objSpcCorreoElectronico->getValor();
                $arrayDataIPMP["strCorreoLicencias"]    = $strCorreoLicencias;
            }
            else
            {
                $strCorreoLicencias = "";
            }
            
            if (empty($strCorreoLicencias) || $strCorreoLicencias == "SIN CORREO")
            {
                $strMensajeErrorCorreo = "El correo debe ser actualizado para poder realizar la ".
                                         "activación del producto ".$objProductoIPMP->getDescripcionProducto().$strMsjTecnologia.
                                         ".<br /> Valor Actual: <b>".$strCorreoLicencias."</b>";
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strMensajeErrorCorreo);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strClientIp);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $strMostrarError = "SI";
                throw new \Exception($strMensajeErrorCorreo);
            }
            
            if($strTipoServicio === "PRODUCTO")
            {
                $strEscenarioXTipoServicio = "ACTIVACION_PROD_ADICIONAL_X_REINTENTO";
            }
            else
            {
                $strEscenarioXTipoServicio = "ACTIVACION_PROD_EN_PLAN";
            }
            
            $arrayParamsLicencias           = array("strProceso"                => "ACTIVACION_ANTIVIRUS",
                                                    "strEscenario"              => $strEscenarioXTipoServicio,
                                                    "objServicio"               => $objServicio,
                                                    "objPunto"                  => $objServicio->getPuntoId(),
                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                    "objProductoIPMP"           => $objProductoIPMP,
                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                    "strIpCreacion"             => $strClientIp,
                                                    "strEstadoServicioInicial"  => $objServicio->getEstado(),
                                                    "strMsjTecnologia"          => $strMsjTecnologia
                                                    );
            $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
            $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
            $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
            $strCodigoProducto              = $arrayRespuestaGestionLicencias["strCodigoProducto"];
            $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                        
            if($strStatusGestionLicencias === "ERROR")
            {
                $strMostrarError = "SI";
                throw new \Exception($strMensajeGestionLicencias);
            }
            else
            {
                if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] === "OK")
                {
                    $arrayDataIPMP["intSuscriberId"]    = $arrayRespuestaWs["SuscriberId"];
                    $arrayDataIPMP["strCodigoProducto"] = $strCodigoProducto;
                }
                else
                {
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion("Error en la ejecución del web service<br>".$arrayRespuestaWs["mensajeHtml"]);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strClientIp);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    $strMostrarError = "SI";
                    throw new \Exception($arrayRespuestaWs["mensaje"]);
                }
            }
            
            $strStatus = 'OK';
            /**
             * Envío de correo a cliente indicando a cual de todos sus contactos le llegará la notificación de I. Protegido Multi Paid
             * con la información de sus licencias contratadas
             */
            $objPuntoServicio   = $objServicio->getPuntoId();
            $objPerServicio     = $objPuntoServicio->getPersonaEmpresaRolId();
            $objPersonaServicio = $objPerServicio->getPersonaId();
            $strNombreCliente   = sprintf("%s",$objPersonaServicio);
            $arrayParamsCorreosSplit    = array("intIdPunto"        => $objServicio->getPuntoId()->getId(),
                                                "strUsrCreacion"    => $strUsrCreacion,
                                                "strIpCreacion"     => $strClientIp);
            $arrayCorreosCliente        = $this->serviceLicenciasKaspersky->getCorreosSplitLicencias($arrayParamsCorreosSplit);
            $arrayCorreosCliente[]      = $strCorreoLicencias;
            $arrayParametrosEnvio   = array('nombreCliente' => $strNombreCliente,
                                            'correo'        => $strCorreoLicencias);
            try
            {
                $this->serviceEnvioPlantilla->generarEnvioPlantilla('Bienvenido a NetlifeDefense, el sistema de '.
                                                                    'seguridad informática para proteger tu vida digital.', 
                                                                    $arrayCorreosCliente, 
                                                                    'ACTIVAIPMP', 
                                                                    $arrayParametrosEnvio, 
                                                                    '','','', null, false,
                                                                    'notificacionesnetlife@netlife.info.ec');
            }
            catch (\Exception $e)
            {
                error_log("No se ha podido enviar el correo con código ACTIVAIPMP ".$e->getMessage());
            }
        }
        catch (\Exception $e)
        {
            $strStatus      = 'ERROR';
            $arrayDataIPMP  = array();
            if($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "";
            }
            
            $this->serviceUtil->insertError('Telcos+',
                                            'LicenciasKasperskyWs->activacionWsProductoIPMP',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuestaServicio = array("status"        => $strStatus,
                                        "mensaje"       => $strMensaje,
                                        "arrayDataIPMP" => $arrayDataIPMP);
        return $arrayRespuestaServicio;
    }
    
    /**
     * Función que realiza el envío al web service para la gestión de licencias de servicios I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.1 10-10-2019 - Se agregó validación para el tipo de transacción 'Reenvio'
     * 
     * @param array $arrayParametros [
     *                                  "strPassword"               => clave enviada como parámetro del web service,
     *                                  "strTipoTransaccion"        => Activacion, Suspension, Reactivacion o Cancelacion
     *                                  "objServicio"               => objeto del servicio,
     *                                  "objProductoIPMP"           => objeto del producto I.PROTEGIDO MULTI PAID de servicios adicionales,
     *                                  "strCodigoProducto"         => código del producto enviado al web service,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "SuscriberId"       => suscriber id de la transacción
     *                                ]
     * 
     */
    public function procesaEnvioWsLicencias($arrayParametros)
    {
        $strPassword        = $arrayParametros["strPassword"];
        $strTipoTransaccion = $arrayParametros['strTipoTransaccion'];
        $objServicio        = $arrayParametros['objServicio'];
        $strCodigoProducto  = $arrayParametros['strCodigoProducto'];
        $objProductoIPMP    = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strUsrCreacion     = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion      = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $intSuscriberId     = $arrayParametros['intSuscriberId'] ? $arrayParametros['intSuscriberId'] : 0;
        
        try
        {
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha enviado el objeto del servicio para procesar el envío al ws para las licencias Kaspersky");
            }
            if(!isset($strPassword) || empty($strPassword)
                || !isset($strTipoTransaccion) || empty($strTipoTransaccion)
                || !isset($strCodigoProducto) || empty($strCodigoProducto))
            {
                throw new \Exception("No se han enviado todos los parámetros obligatorios para procesar el envío al ws para las licencias Kaspersky");
            }
            
            if($strTipoTransaccion === "Activacion")
            {
                $arrayRespuestaDataClienteWs    = $this->obtenerDataClienteWs(array("objServicio"       => $objServicio,
                                                                                    "objProductoIPMP"   => $objProductoIPMP,
                                                                                    "strUsrCreacion"    => $strUsrCreacion,
                                                                                    "strIpCreacion"     => $strIpCreacion));
                if($arrayRespuestaDataClienteWs["status"] === "ERROR")
                {
                    throw new \Exception($arrayRespuestaDataClienteWs["mensaje"]);
                }
                $arrayDataClienteWs = $arrayRespuestaDataClienteWs["arrayDataClienteWs"];

                $arrayRespuestaDataProductoWs   = $this->obtenerDataProductoWs(array(   "objServicio"       => $objServicio,
                                                                                        "objProductoIPMP"   => $objProductoIPMP,
                                                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                                                        "strIpCreacion"     => $strIpCreacion,
                                                                                        "strCodigoProducto" => $strCodigoProducto));
                if($arrayRespuestaDataProductoWs["status"] === "ERROR")
                {
                    throw new \Exception($arrayRespuestaDataProductoWs["mensaje"]);
                }
                $arrayDataProductoWs    = $arrayRespuestaDataProductoWs["arrayDataProductoWs"];
                
                $intSuscriberId = 0;
            }
            else if($strTipoTransaccion === "Suspension" || $strTipoTransaccion === "Reactivacion" || $strTipoTransaccion === "Cancelacion" || 
                    $strTipoTransaccion === "Reenvio")
            {
                if($intSuscriberId === 0)
                {
                    $arrayParamsGetSpcSuscriberId   = array("objServicio"       => $objServicio,
                                                            "objProducto"       => $objProductoIPMP,
                                                            "strCaracteristica" => "SUSCRIBER_ID");
                    $arrayRespuestaSpcSuscriberId   = $this->serviceLicenciasKaspersky
                                                           ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcSuscriberId);
                    if($arrayRespuestaSpcSuscriberId["status"] == 'ERROR')
                    {
                        throw new \Exception($arrayRespuestaSpcSuscriberId["mensaje"]);
                    }
                    $objSpcSuscriberId  = $arrayRespuestaSpcSuscriberId["objServicioProdCaract"];
                    if(!is_object($objSpcSuscriberId))
                    {
                        throw new \Exception("No se ha podido obtener el objeto con el SUSCRIBER ID asociada al servicio");
                    }
                    $intSuscriberId = intval($objSpcSuscriberId->getValor());
                }
                $arrayDataClienteWs     = array();
                $arrayDataProductoWs    = array();
                
            }
            else
            {
                throw new \Exception("Tipo de transacción no permitida para procesar el envío al ws para las licencias Kaspersky");
            }
            
            $arrayRequestWs     = array(
                                            "Password"          => $strPassword,
                                            "Cliente"           => $arrayDataClienteWs,
                                            "Producto"          => $arrayDataProductoWs,
                                            "TipoTransaccion"   => $strTipoTransaccion,
                                            "SuscriberId"       => $intSuscriberId);
            
            $arrayRespuestaWs   = $this->invocaWs($arrayRequestWs);
        }
        catch(\Exception $e)
        {
            $arrayRespuestaWs   = array("status"        => "ERROR",
                                        "mensaje"       => $e->getMessage(),
                                        "SuscriberId"   => 0);
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKasperskyWs->procesaEnvioWsLicencias', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        return $arrayRespuestaWs;
    }
    
    /**
     * Función que realiza el reintento de una activación de servicios adicionales I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 27-07-2020 Se agrega bandera para evitar activar licencias con el correo del usuario real
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "objProductoIPMP"   => objeto del producto I. PROTEGIDO MULTI PAID,
     *                                  "strUsrCreacion"    => usuario de creación,
     *                                  "strIpCreacion"     => ip del cliente
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => mensaje de error,
     *                                  "arrayDataClienteWs"    => arreglo con la información del cliente
     *                                ]
     * 
     */
    public function obtenerDataClienteWs($arrayParametros)
    {
        $objServicio            = $arrayParametros["objServicio"];
        $objProductoIPMP        = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion          = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $strMostrarError        = "NO";
        try
        {
            if(!is_object($objServicio))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha enviado correctamente el objeto del servicio por lo que no se puede obtener la data del cliente");
            }
            
            $arrayDatosCliente  = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                    ->getDatosClientePorIdServicio($objServicio->getId(), "esProducto");
            if (!$arrayDatosCliente['ID_PERSONA'])
            {
                $arrayDatosCliente  = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                        ->getDatosClientePorIdServicio($objServicio->getId(), false);
            }
            if(!isset($arrayDatosCliente['ID_PERSONA']) || empty($arrayDatosCliente['ID_PERSONA']))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha podido obtener el ID de la persona para obtener la data del cliente");
            }
            $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($arrayDatosCliente['ID_PERSONA']);
            if(!is_object($objPersona))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha podido obtener el objeto de la persona con ID ".$arrayDatosCliente['ID_PERSONA']);
            }
            if($objPersona->getRazonSocial())
            {
                $strNombres   = $objPersona->getRazonSocial();
                $strApellidos = $objPersona->getRazonSocial();
            }
            else
            {
                $strNombres   = $objPersona->getNombres();
                $strApellidos = $objPersona->getApellidos();
            }
            $strIdentificacion = $objPersona->getIdentificacionCliente();
            
            $arrayParamsGetSpcCorreoLicencias   = array("objServicio"       => $objServicio,
                                                        "objProducto"       => $objProductoIPMP,
                                                        "strCaracteristica" => "CORREO ELECTRONICO");
            $arrayRespuestaSpcCorreoLicencias   = $this->serviceLicenciasKaspersky
                                                       ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCorreoLicencias);
            if($arrayRespuestaSpcCorreoLicencias["status"] == 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaSpcCorreoLicencias["mensaje"]);
            }
            if($this->strEjecutaCorreoTestWsKaspersky === 'S')
            {
                $strCorreoLicencias = $this->strCorreoTestWsKaspersky;
            }
            else
            {
                $objSpcCorreoLicencias  = $arrayRespuestaSpcCorreoLicencias["objServicioProdCaract"];
                if (is_object($objSpcCorreoLicencias))
                {
                    $strCorreoLicencias = $objSpcCorreoLicencias->getValor();
                }
                else
                {

                    $strCorreoLicencias = $this->serviceLicenciasKaspersky
                                               ->getCorreoLicencias(array(  "intIdPunto"        => $objServicio->getPuntoId()->getId(),
                                                                            "strUsrCreacion"    => $strUsrCreacion,
                                                                            "strIpCreacion"     => $strIpCreacion));
                    if(empty($strCorreoLicencias))
                    {
                        $strMostrarError = "SI";
                        throw new \Exception('No se recuperó ningún correo permitido del cliente para la activación de su suscripción');
                    }
                }
            }
            $arrayDataClienteWs = array("Identificacion"    => $strIdentificacion,
                                        "Email"             => $strCorreoLicencias,
                                        "Nombres"           => $strNombres,
                                        "Apellidos"         => $strApellidos);
            $strStatus          = "OK";
            $strMensaje         = "";
        }
        catch(\Exception $e)
        {
            $arrayDataClienteWs = array();
            $strStatus          = "ERROR";
            if($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido obtener la información del cliente necesaria para la gestión de licencias. "
                              ."Por favor notifique a Sistemas!";
            }
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKasperskyWs->obtenerDataClienteWs', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje,
                                "arrayDataClienteWs"    => $arrayDataClienteWs);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza el reintento de una activación de servicios adicionales I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-07-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-08-2020 Se agrega validación para no permitir el envío al web service cuando el número de licencias está vacío 
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "objProductoIPMP"   => objeto del producto I. PROTEGIDO MULTI PAID,
     *                                  "strUsrCreacion"    => usuario de creación,
     *                                  "strIpCreacion"     => ip del cliente,
     *                                  "strCodigoProducto" => código del producto enviado al web service
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => mensaje de error,
     *                                  "arrayDataProductoWs"   => arreglo con la información del producto
     *                                ]
     * 
     */
    public function obtenerDataProductoWs($arrayParametros)
    {
        $objServicio            = $arrayParametros["objServicio"];
        $objProductoIPMP        = is_object($arrayParametros["objProductoIPMP"]) ? $arrayParametros["objProductoIPMP"] : null;
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'] ? $arrayParametros['strUsrCreacion'] : "telcos";
        $strIpCreacion          = $arrayParametros['strIpCreacion'] ? $arrayParametros['strIpCreacion'] : "127.0.0.1";
        $strCodigoProducto      = $arrayParametros['strCodigoProducto'];
        $strMostrarError        = "NO";
        try
        {
            if(!is_object($objServicio))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha enviado correctamente el objeto del servicio por lo que no se puede obtener la data del producto");
            }
            if(!isset($strCodigoProducto) || empty($strCodigoProducto))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha enviado el código del producto para gestionar las licencias");
            }
            $arrayParamsGetSpcCantDispLicencias = array("objServicio"       => $objServicio,
                                                        "objProducto"       => $objProductoIPMP,
                                                        "strCaracteristica" => "CANTIDAD DISPOSITIVOS");
            $arrayRespuestaSpcCantDispLicencias = $this->serviceLicenciasKaspersky
                                                       ->obtenerValorServicioProductoCaracteristica($arrayParamsGetSpcCantDispLicencias);
            if($arrayRespuestaSpcCantDispLicencias["status"] == 'ERROR')
            {
                $strMostrarError = "SI";
                throw new \Exception($arrayRespuestaSpcCantDispLicencias["mensaje"]);
            }
            $objSpcCantDispLicencias    = $arrayRespuestaSpcCantDispLicencias["objServicioProdCaract"];
            if(!is_object($objSpcCantDispLicencias))
            {
                $strMostrarError = "SI";
                throw new \Exception("No se ha podido obtener el objeto con el número de licencias del servicio");
            }
            
            $strCantDispLicencias = $objSpcCantDispLicencias->getValor();
            if(empty($strCantDispLicencias))
            {
                $strMostrarError = "SI";
                throw new \Exception("El valor del número de licencias del servicio está vacío");
            }
            
            $arrayDataProductoWs    = array("Cantidad"          => $strCantDispLicencias,
                                            "CodigoProducto"    => $strCodigoProducto);
            $strStatus              = "OK";
            $strMensaje             = "";
        }
        catch(\Exception $e)
        {
            $strStatus              = "ERROR";
            $arrayDataProductoWs    = array();
            if($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido obtener la información del producto necesaria para la gestión de licencias. "
                              ."Por favor notifique a Sistemas!";
            }
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKasperskyWs->obtenerDataProductoWs', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje,
                                "arrayDataProductoWs"   => $arrayDataProductoWs);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la cancelación del punto y cliente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2019
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"               => objeto del servicio,
     *                                  "strUsrCreacion"            => usuario de creación,
     *                                  "strIpCreacion"             => ip del creación
     *                                ]
     */
    public function cancelacionPuntoYCliente($arrayParametros)
    {
        $objServicio    = $arrayParametros["objServicio"];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        
        
        try
        {
            $objPunto               = $objServicio->getPuntoId();
            $arrayServicios         = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findBy(array( "puntoId" => $objPunto->getId()));
            $intNumServicios        = count($arrayServicios);
            $intContadorServicios   = 0;
            for($intIndiceServicio=0; $intIndiceServicio<count($arrayServicios); $intIndiceServicio++)
            {
                $strServicioEstado = $arrayServicios[$intIndiceServicio]->getEstado();
                if($strServicioEstado=="Cancel"        || 
                   $strServicioEstado=="Cancel-SinEje" || 
                   $strServicioEstado=="Anulado"       || 
                   $strServicioEstado=="Eliminado"     ||
                   $strServicioEstado=="Rechazada")
                {
                    $intContadorServicios++;
                }
            }
            
            if($intContadorServicios == ($intNumServicios))
            {
                $objPunto->setEstado("Cancelado");
                $this->emComercial->persist($objPunto);
                $this->emComercial->flush();
            }
            
            $objPersonaEmpresaRol   = $objPunto->getPersonaEmpresaRolId();
            $arrayPuntos            = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                        ->findBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId()));
            $intNumPuntos           = count($arrayPuntos);
            $intContPuntosCliente   = 0;
            for($intIndicePuntos=0; $intIndicePuntos<count($arrayPuntos); $intIndicePuntos++)
            {
                $objPuntoCliente = $arrayPuntos[$intIndicePuntos];
                if($objPuntoCliente->getEstado() == "Cancelado")
                {
                    $intContPuntosCliente++;
                }
            }
            if(($intNumPuntos) == $intContPuntosCliente)
            {
                //Se cancela el contrato
                $objContrato    = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                                    ->findOneBy(array( "personaEmpresaRolId" => $objPersonaEmpresaRol->getId(),
                                                                       "estado"              => "Activo"));
                if(is_object($objContrato))
                {
                    $objContrato->setEstado("Cancelado");
                    $this->emComercial->persist($objContrato);
                    $this->emComercial->flush();
                }
                
                //Se cancela el personaEmpresaRol
                $objPersonaEmpresaRol->setEstado("Cancelado");
                $this->emComercial->persist($objPersonaEmpresaRol);
                $this->emComercial->flush();
                //Se ingresa un registro en el historial empresa persona rol
                $objPerHistorial = new InfoPersonaEmpresaRolHisto();
                $objPerHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPerHistorial->setEstado("Cancelado");
                $objPerHistorial->setUsrCreacion($strUsrCreacion);
                $objPerHistorial->setFeCreacion(new \DateTime('now'));
                $objPerHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objPerHistorial);
                $this->emComercial->flush();
                
                //Se cancela el cliente
                $objPersona = $objPersonaEmpresaRol->getPersonaId();
                $objPersona->setEstado("Cancelado");
                $this->emComercial->persist($objPersona);
                $this->emComercial->flush();                
            }
        }
        catch (\Exception $e)
        {
            error_log("No se pudo realizar la cancelación de Punto y Cliente ".$e->getMessage());
            $this->serviceUtil->insertError('Telcos+', 
                                            'LicenciasKasperskyWs->cancelacionPuntoYCliente', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
        }
    }
    
    /**
     * Función que sirve para ejecutar la llamada al ws provisto por GMS para gestionar las licencias Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 29-07-2019
     * 
     * @param array $arrayRequestWs [
     *                                  "Password"          => clave enviada como parámetro del web service,
     *                                  "Cliente"           => arreglo con la información del cliente
     *                                  "Producto"          => arreglo con la información del producto,
     *                                  "TipoTransaccion"   => Activacion, Suspension, Reactivacion o Cancelacion
     *                                  "SuscriberId"       => suscriber id del servicio enviado como entero
     *                               ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"            => OK o ERROR,
     *                                  "mensaje"           => mensaje de error,
     *                                  "SuscriberId"       => suscriber id de la transacción
     *                                ]
     * 
     */
    public function invocaWs($arrayRequestWs)
    {
        $boolFalse              = false;
        $strMensajeError        = "";
        $strMensajeErrorHtml    = "";
        $strMensaje             = "";
        $strMensajeHtml         = "";
        $strJsonRequestWs       = json_encode($arrayRequestWs);
        $arrayOptions           = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponseWs        = $this->serviceRestClient->postJSON($this->strUrlWsKaspersky, $strJsonRequestWs, $arrayOptions);
        if($arrayResponseWs['status'] == static::$intStatusOk && $arrayResponseWs['result'] != $boolFalse)
        {
            $arrayInfoRespuestaWs   = json_decode($arrayResponseWs['result'], true);
            if($arrayInfoRespuestaWs["status"] === "ERROR")
            {
                $arrayResultInfoRespuestaWs = $arrayInfoRespuestaWs["result"];
                foreach($arrayResultInfoRespuestaWs as $arrayItemInfoError)
                {
                    $strMensajeError        .= $arrayItemInfoError["ErrorDescription"] . ", ";
                    $strMensajeErrorHtml    .= "<tr>".
                                                    "<td style='border: 1px solid; padding:4px;'>".$arrayItemInfoError["ErrorCode"]."</td>".
                                                    "<td style='border: 1px solid; padding:4px;'>".$arrayItemInfoError["ErrorDescription"]."</td>".
                                                "</tr>";
                }
                
                if(!empty($strMensajeError))
                {
                    $strMensaje         = " Detalle de errores: ".substr($strMensajeError, 0, (strlen($strMensajeError)- 2));
                    $strMensajeHtml     .= "<b>Detalle de errores</b><br />".
                                            "<table cellpadding='5'>".
                                                "<tr>".
                                                    "<td valign='center'>".
                                                        "<div>".
                                                            "<table style='font-size: 10px; border-collapse: collapse;' cellpadding='5'>".
                                                                "<thead>".
                                                                    "<tr>".
                                                                        "<th style='border: 1px solid; padding:4px;'><b>Código</b></th>".
                                                                        "<th style='border: 1px solid; padding:4px;'><b>Descripción</b></th>".
                                                                    "</tr>".
                                                                "</thead>".
                                                                "<tbody>".
                                                                    $strMensajeErrorHtml.
                                                                "</tbody>".
                                                            "</table>".
                                                        "</div>".
                                                    "</td>".
                                                "</tr>".
                                            "</table>";
                }
                $this->serviceUtil->insertError('Telcos+', 
                                                'LicenciasKasperskyWs->invocaWs', 
                                                "Request: ".$strJsonRequestWs."<br />"."Response: ".json_encode($arrayResultInfoRespuestaWs),
                                                'telcos', 
                                                '127.0.0.1'
                                               );
            }
            $arrayRespuesta                 = $arrayInfoRespuestaWs;
            $arrayRespuesta["mensaje"]      = $strMensaje;
            $arrayRespuesta["mensajeHtml"]  = $strMensajeHtml;
        }
        else
        {
            $arrayRespuesta['status'] = "ERROR";
            if($arrayResponseWs['status'] == "0")
            {
                $arrayRespuesta['mensaje'] = "No Existe Conectividad con el WS para gestionar las licencias";
            }
            else
            {
                $strMensajeError = 'ERROR';
                if(isset($arrayResponseWs['mensaje']) && !empty($arrayResponseWs['mensaje']))
                {
                    $strMensajeError = $arrayResponseWs['mensaje'];
                }
                $arrayRespuesta['mensaje'] = "Error de proveedor de Licencias :" . $strMensajeError;
            }
            $arrayRespuesta["mensajeHtml"]  = $arrayRespuesta['mensaje'];
        }
        return $arrayRespuesta;
    }
}
