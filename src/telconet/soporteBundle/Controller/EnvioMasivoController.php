<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use \PHPExcel;
use \PHPExcel_IOFactory;
use \PHPExcel_Shared_Date;
use \PHPExcel_Style_NumberFormat;
use \PHPExcel_Worksheet_PageSetup;
use \PHPExcel_CachedObjectStorageFactory;
use \PHPExcel_Settings;

/**
 * Clase que maneja las acciones relacionadas al envío de notificaciones masivas en TN
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 08-09-2017 
 */
class EnvioMasivoController extends Controller
{
    /**
     * indexAction
     * 
     * @Secure(roles="ROLE_362-1")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 08-09-2017
     * 
     */
    public function indexAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strEmpresaCod      = $objSession->get('prefijoEmpresa');
        $emSeguridad        = $this->getDoctrine()->getManager("telconet_seguridad");
        $objItemMenu        = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("362", "1");
        $objItemMenuPadre   = $objItemMenu->getItemMenuId();
        $objSession->set('menu_modulo_activo', $objItemMenuPadre->getNombreItemMenu());
        $objSession->set('nombre_menu_modulo_activo', $objItemMenuPadre->getTitleHtml());
        $objSession->set('id_menu_modulo_activo', $objItemMenuPadre->getId());
        $objSession->set('imagen_menu_modulo_activo', $objItemMenuPadre->getUrlImagen());

        return $this->render('soporteBundle:EnvioMasivo:index.html.twig', array(
                'objItem' => $objItemMenu,
                'strEmpresa' => $strEmpresaCod,
        ));
    }

    /**
     * getSwitchesEnNodoAction
     * 
     * Función que obtiene los switches que están contenidos en un nodo
     *  
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-09-2017 
     * 
     */
    public function getSwitchesEnNodoAction()
    {
        $objResponse        = new JsonResponse();
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $emInfraestructura  = $this->getDoctrine()->getManager("telconet_infraestructura");
        $strNombreSwitch    = $objRequest->get('query') ? $objRequest->get('query') : "";
        $intIdElementoNodo  = $objRequest->get('intIdElementoNodo') ? $objRequest->get('intIdElementoNodo') : 0;
        $strCodEmpresa      = $objSession->get('idEmpresa');

        $arrayParametros                        = array();
        $arrayParametros["strNombreSwitch"]     = $strNombreSwitch;
        $arrayParametros["intIdElementoNodo"]   = $intIdElementoNodo;
        $arrayParametros["strCodEmpresa"]       = $strCodEmpresa;


        $strJson = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->getJSONSwitchesEnNodo($arrayParametros);
        $objResponse->setContent($strJson);
        return $objResponse;
    }

    /**
     * gridAction
     * 
     * Función que obtiene el resultado de la consulta general de acuerdo a los parámetros enviados
     *  
     * @Secure(roles="ROLE_362-7")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-09-2017 
     * 
     */
    public function gridAction()
    {
        $objRequest = $this->getRequest();
        $objSession = $objRequest->getSession();

        $strCodEmpresa      = $objSession->get('idEmpresa');
        $strPrefijoEmpresa  = $objSession->get('prefijoEmpresa');
        $emComercial        = $this->get('doctrine')->getManager('telconet');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');

        $objResponse                    = new JsonResponse();
        $strGrupo                       = $objRequest->get('grupo') ? $objRequest->get('grupo') : "";
        $strSubgrupo                    = $objRequest->get('subgrupo') ? $objRequest->get('subgrupo') : "";
        $intIdElementoNodo              = $objRequest->get('idElementoNodo') ? $objRequest->get('idElementoNodo') : 0;
        $intIdElementoSwitch            = $objRequest->get('idElementoSwitch') ? $objRequest->get('idElementoSwitch') : 0;
        $strEstadoServicio              = $objRequest->get('estadoServicio') ? $objRequest->get('estadoServicio') : "";
        $strEstadoPunto                 = $objRequest->get('estadoPunto') ? $objRequest->get('estadoPunto') : "";
        $strEstadoCliente               = $objRequest->get('estadoCliente') ? $objRequest->get('estadoCliente') : "";
        $strClientesVIP                 = $objRequest->get('clientesVIP') ? $objRequest->get('clientesVIP') : "";
        $strUsrCreacionFactura          = $objRequest->get('usrCreacionFactura') ? $objRequest->get('usrCreacionFactura') : "";
        $intNumerosFactAbiertas         = $objRequest->get('numFacturasAbiertas') ? $objRequest->get('numFacturasAbiertas') : 0;
        $strPuntosFacturacion           = $objRequest->get('puntosFacturacion') ? $objRequest->get('puntosFacturacion') : "";
        $strIdsTiposNegocio             = $objRequest->get('idsTiposNegocio') ? $objRequest->get('idsTiposNegocio') : "";
        $strIdsOficinas                 = $objRequest->get('idsOficinas') ? $objRequest->get('idsOficinas') : "";
        $intIdFormaPago                 = $objRequest->get('idFormaPago') ? $objRequest->get('idFormaPago') : 0;
        $strFechaDesdeFactura           = $objRequest->get('fechaDesdeFactura') ? $objRequest->get('fechaDesdeFactura') : "";
        $strFechaHastaFactura           = $objRequest->get('fechaHastaFactura') ? $objRequest->get('fechaHastaFactura') : "";
        $strSaldoPendientePago          = $objRequest->get('saldoPendientePago') ? $objRequest->get('saldoPendientePago') : "";
        $floatValorSaldoPendientePago   = $objRequest->get('valorSaldoPendientePago') ? $objRequest->get('valorSaldoPendientePago') : "";
        $strNombreFormaPago             = "";
        $strIdsBancosTarjetas           = $objRequest->get('idsBancosTarjetas') ? $objRequest->get('idsBancosTarjetas') : "";
        $intStart                       = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit                       = $objRequest->get('limit') ? $objRequest->get('limit') : 0;
        $strUserComunicacion            = $this->container->getParameter('user_comunicacion');
        $strPasswordComunicacion        = $this->container->getParameter('passwd_comunicacion');
        $strDatabaseDsn = $this->container->getParameter('database_dsn');

        if($intIdFormaPago != 0)
        {
            $objFormaPago = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);
            if(is_object($objFormaPago))
            {
                $strNombreFormaPago = $objFormaPago->getDescripcionFormaPago();
            }
        }
        $arrayParametros = array();
        $arrayParametros['strUserComunicacion']             = $strUserComunicacion;
        $arrayParametros['strPasswordComunicacion']         = $strPasswordComunicacion;
        $arrayParametros['strDatabaseDsn']                  = $strDatabaseDsn;
        $arrayParametros['intStart']                        = $intStart;
        $arrayParametros['intLimit']                        = $intLimit;
        $arrayParametros['strGrupo']                        = $strGrupo;
        $arrayParametros['strSubgrupo']                     = $strSubgrupo;
        $arrayParametros['intIdElementoNodo']               = $intIdElementoNodo;
        $arrayParametros['intIdElementoSwitch']             = $intIdElementoSwitch;
        $arrayParametros['strEstadoServicio']               = $strEstadoServicio;
        $arrayParametros['strEstadoPunto']                  = $strEstadoPunto;
        $arrayParametros['strEstadoCliente']                = $strEstadoCliente;
        $arrayParametros['strClientesVIP']                  = $strClientesVIP;
        $arrayParametros['strUsrCreacionFactura']           = $strUsrCreacionFactura;
        $arrayParametros['intNumerosFactAbiertas']          = $intNumerosFactAbiertas;
        $arrayParametros['strPuntosFacturacion']            = $strPuntosFacturacion;
        $arrayParametros['strIdsTiposNegocio']              = $strIdsTiposNegocio;
        $arrayParametros['strIdsOficinas']                  = $strIdsOficinas;
        $arrayParametros['intIdFormaPago']                  = $intIdFormaPago;
        $arrayParametros['strNombreFormaPago']              = $strNombreFormaPago;
        $arrayParametros['strIdsBancosTarjetas']            = $strIdsBancosTarjetas;
        $arrayParametros['strCodEmpresa']                   = $strCodEmpresa;
        $arrayParametros['strPrefijoEmpresa']               = $strPrefijoEmpresa;
        $arrayParametros['strFechaDesdeFactura']            = $strFechaDesdeFactura;
        $arrayParametros['strFechaHastaFactura']            = $strFechaHastaFactura;
        $arrayParametros['strSaldoPendientePago']           = $strSaldoPendientePago;
        $arrayParametros['floatValorSaldoPendientePago']    = $floatValorSaldoPendientePago;
        $arrayParametros['page']    = $objRequest->get('page') ? $objRequest->get('page') : "";

        $strJsonRespuesta = $emComercial->getRepository('schemaBundle:InfoServicio')->getJSONServiciosNotifMasiva($arrayParametros);
        $objResponse->setContent($strJsonRespuesta);
        return $objResponse;
    }

    /**
     * getPlantillasAction
     * 
     * Función que obtiene las plantillas para el envío masivo
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017 
     * 
     */
    public function getPlantillasAction()
    {
        $objRequest         = $this->getRequest();
        $strNombrePlantilla = $objRequest->get('query') ? $objRequest->get('query') : "";
        $emComunicacion     = $this->get('doctrine')->getManager('telconet_comunicacion');
        $objResponse        = new JsonResponse();
        $strJson            = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')
                                             ->generarJson($strNombrePlantilla, 'Activo', '', '', '', '');
        $objResponse->setContent($strJson);
        return $objResponse;
    }

    /**
     * getPeriodicidadesAction
     * 
     * Función que obtiene las periodicidades para el envío masivo recurrente
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017 
     * 
     */
    public function getPeriodicidadesAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $emGeneral              = $this->get('doctrine')->getManager('telconet_general');
        $arrayPeriodicidades    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PERIODICIDAD_ENVIO_MASIVO_RECURRENTE', '', '', '', '', '', '', '', '', $strCodEmpresa, 'valor1');
        $objResponse = new JsonResponse();
        $objResponse->setData(array("arrayRegistros" => $arrayPeriodicidades));
        return $objResponse;
    }

    /**
     * getEstadosFiltrosAction
     * 
     * Función que obtiene los estados considerados tanto para servicio, punto y cliente 
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017 
     * 
     */
    public function getEstadosFiltrosAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strCodEmpresa  = $objSession->get('idEmpresa');
        $strValor1      = $objRequest->get('valor1') ? $objRequest->get('valor1') : "";
        $emGeneral      = $this->get('doctrine')->getManager('telconet_general');
        $arrayEstados   = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('ESTADOS_FILTROS_ENVIO_MASIVO', '', '', '', $strValor1, '', '', '', '', $strCodEmpresa, 'valor1');
        $objResponse = new JsonResponse();
        $objResponse->setData(array("arrayRegistros" => $arrayEstados));
        return $objResponse;
    }

    /**
     * guardarEnvioAction
     * 
     * Función que creará el JOB de acuerdo a los parámetros enviados al guardar el envío masivo
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017 
     * 
     */
    public function guardarEnvioAction()
    {
        $objRequest             = $this->getRequest();
        $objSession             = $objRequest->getSession();
        $strUsrCreacion         = $objSession->get('user');
        $strIpCreacion          = $objRequest->getClientIp();
        $strCodEmpresa          = $objSession->get('idEmpresa');
        $objResponse            = new JsonResponse();
        $emGeneral              = $this->get('doctrine')->getManager('telconet_general');
        $arrayTiposEnvio        = array();
        $arrayPeriodicidades    = array();
        $boolValidacionOK       = true;
        /**
         * Parametrós de envío
         */
        $intIdPlantilla         = $objRequest->get('idPlantilla');
        $strIdsTipoContacto     = $objRequest->get('idsTipoContacto');
        $strAsuntoEnvio         = $objRequest->get('asunto');
        $strTipoEnvio           = $objRequest->get('tipoEnvio');
        
        $strStatusCrear         = "";
        $strMensaje             = "";
        if((isset($intIdPlantilla) && !empty($intIdPlantilla))
            && (isset($strIdsTipoContacto) && !empty($strIdsTipoContacto))
            && (isset($strAsuntoEnvio) && !empty($strAsuntoEnvio))
            && (isset($strTipoEnvio) && !empty($strTipoEnvio))
            && (isset($strUsrCreacion) && !empty($strUsrCreacion))
            && (isset($strIpCreacion) && !empty($strIpCreacion))
            )
        {
            $strAsuntoEnvio         = json_encode($objRequest->get('asunto'));
            $strFechaHoraProgramada = $objRequest->get('fechaHoraProgramada') ? $objRequest->get('fechaHoraProgramada') : "";
            $strFechaEjecucionDesde = $objRequest->get('fechaEjecucionDesde') ? $objRequest->get('fechaEjecucionDesde') : "";
            $strHoraEjecucion       = $objRequest->get('horaEjecucion') ? $objRequest->get('horaEjecucion') : "";
            $strPeriodicidadYDias   = $objRequest->get('periodicidad') ? $objRequest->get('periodicidad') : "";
            $strPeriodicidad        = "";
            $intNumeroDia           = $objRequest->get('numeroDia') ? $objRequest->get('numeroDia') : 0;
            
            /**
             * Parametrós de búsqueda
             */
            $strGrupo                       = $objRequest->get('grupo') ? $objRequest->get('grupo') : "";
            $strSubgrupo                    = $objRequest->get('subgrupo') ? $objRequest->get('subgrupo') : "";
            $intIdElementoNodo              = $objRequest->get('idElementoNodo') ? $objRequest->get('idElementoNodo') : 0;
            $intIdElementoSwitch            = $objRequest->get('idElementoSwitch') ? $objRequest->get('idElementoSwitch') : 0;
            $strEstadoServicio              = $objRequest->get('estadoServicio') ? $objRequest->get('estadoServicio') : "";
            $strEstadoPunto                 = $objRequest->get('estadoPunto') ? $objRequest->get('estadoPunto') : "";
            $strEstadoCliente               = $objRequest->get('estadoCliente') ? $objRequest->get('estadoCliente') : "";
            $strClientesVIP                 = $objRequest->get('clientesVIP') ? $objRequest->get('clientesVIP') : "";
            $strUsrCreacionFactura          = $objRequest->get('usrCreacionFactura') ? $objRequest->get('usrCreacionFactura') : "";
            $intNumerosFactAbiertas         = $objRequest->get('numFacturasAbiertas') ? $objRequest->get('numFacturasAbiertas') : 0;
            $strPuntosFacturacion           = $objRequest->get('puntosFacturacion') ? $objRequest->get('puntosFacturacion') : "";
            $strIdsTiposNegocio             = $objRequest->get('idsTiposNegocio') ? $objRequest->get('idsTiposNegocio') : "";
            $strIdsOficinas                 = $objRequest->get('idsOficinas') ? $objRequest->get('idsOficinas') : "";
            $intIdFormaPago                 = $objRequest->get('idFormaPago') ? $objRequest->get('idFormaPago') : 0;
            $strIdsBancosTarjetas           = $objRequest->get('idsBancosTarjetas') ? $objRequest->get('idsBancosTarjetas') : "";
            $strFechaDesdeFactura           = $objRequest->get('fechaDesdeFactura') ? $objRequest->get('fechaDesdeFactura') : "";
            $strFechaHastaFactura           = $objRequest->get('fechaHastaFactura') ? $objRequest->get('fechaHastaFactura') : "";
            $strSaldoPendientePago          = $objRequest->get('saldoPendientePago') ? $objRequest->get('saldoPendientePago') : "";
            $floatValorSaldoPendientePago   = $objRequest->get('valorSaldoPendientePago') ? $objRequest->get('valorSaldoPendientePago') : "";

            /**
             * Info Adicional 
             */
            $strInfoFiltros = $objRequest->get('infoBusqueda') ? str_replace("\\/", "", json_encode($objRequest->get('infoBusqueda'))) : "";


            if(!empty($strIdsTipoContacto))
            {
                $arrayIdsTipoContacto = array_map('trim', explode(",", $strIdsTipoContacto));

                //Remueve el array con valor 0
                if(('0' === $arrayIdsTipoContacto[0]) && array_key_exists(0, $arrayIdsTipoContacto))
                {
                    unset($arrayIdsTipoContacto[0]);
                }
                $strIdsTipoContacto = implode(',', $arrayIdsTipoContacto);
            }

            if(isset($strPeriodicidadYDias) && !empty($strPeriodicidadYDias))
            {
                list($strPeriodicidad) = explode('_', $strPeriodicidadYDias);
            }

            $arrayRegTiposEnvio = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('TIPOS_ENVIO_MASIVO', '', '', '', '', '', '', '', '', $strCodEmpresa, '');
            foreach($arrayRegTiposEnvio as $arrayTipoEnvio)
            {
                $arrayTiposEnvio[] = $arrayTipoEnvio["valor1"];
            }

            $arrayRegPeriodicidades = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PERIODICIDAD_ENVIO_MASIVO_RECURRENTE', '', '', '', '', '', '', '', '', $strCodEmpresa, '');
            foreach($arrayRegPeriodicidades as $arrayPeriodicidad)
            {
                $arrayPeriodicidades[] = $arrayPeriodicidad["valor1"];
            }

            $strNombreFormaPago = "";
            if($intIdFormaPago != 0)
            {
                $objFormaPago = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);
                if(is_object($objFormaPago))
                {
                    $strNombreFormaPago = $objFormaPago->getDescripcionFormaPago();
                }
            }


            //Se validan los parámetros que se enviarán a la configuración y creación del job
            if(isset($strTipoEnvio) && !empty($strTipoEnvio) && in_array($strTipoEnvio, $arrayTiposEnvio))
            {
                if(($strTipoEnvio === "PROGRAMADO" && (empty($strFechaHoraProgramada) 
                    || (false === \DateTime::createFromFormat('Y-m-d H:i', $strFechaHoraProgramada)))) 
                    || ($strTipoEnvio === "RECURRENTE" && ((empty($strFechaEjecucionDesde) 
                        || empty($strHoraEjecucion) || empty($strPeriodicidad)) 
                        || (false === \DateTime::createFromFormat('Y-m-d H:i', $strFechaEjecucionDesde . ' ' . $strHoraEjecucion)) 
                        || (false === in_array($strPeriodicidad, $arrayPeriodicidades)) 
                        || ($strPeriodicidad === "MONTHLY" && empty($intNumeroDia)))))
                {
                    $boolValidacionOK = false;
                }
            }
            else
            {
                $boolValidacionOK = false;
            }
            
            if($boolValidacionOK)
            {
                $arrayParametros = array();
                $arrayParametros['strInfoFiltros']                  = $strInfoFiltros;
                $arrayParametros['strGrupo']                        = $strGrupo;
                $arrayParametros['strSubgrupo']                     = $strSubgrupo;
                $arrayParametros['intIdElementoNodo']               = $intIdElementoNodo;
                $arrayParametros['intIdElementoSwitch']             = $intIdElementoSwitch;
                $arrayParametros['strEstadoServicio']               = $strEstadoServicio;
                $arrayParametros['strEstadoPunto']                  = $strEstadoPunto;
                $arrayParametros['strEstadoCliente']                = $strEstadoCliente;
                $arrayParametros['strClientesVIP']                  = $strClientesVIP;
                $arrayParametros['strUsrCreacionFactura']           = $strUsrCreacionFactura;
                $arrayParametros['intNumerosFactAbiertas']          = $intNumerosFactAbiertas;
                $arrayParametros['strPuntosFacturacion']            = $strPuntosFacturacion;
                $arrayParametros['strIdsTiposNegocio']              = $strIdsTiposNegocio;
                $arrayParametros['strIdsOficinas']                  = $strIdsOficinas;
                $arrayParametros['intIdFormaPago']                  = $intIdFormaPago;
                $arrayParametros['strNombreFormaPago']              = $strNombreFormaPago;
                $arrayParametros['strIdsBancosTarjetas']            = $strIdsBancosTarjetas;
                $arrayParametros['strFechaDesdeFactura']            = $strFechaDesdeFactura;
                $arrayParametros['strFechaHastaFactura']            = $strFechaHastaFactura;
                $arrayParametros['strSaldoPendientePago']           = $strSaldoPendientePago;
                $arrayParametros['floatValorSaldoPendientePago']    = $floatValorSaldoPendientePago;
                $arrayParametros['intIdPlantilla']                  = $intIdPlantilla;
                $arrayParametros['strIdsTipoContacto']              = $strIdsTipoContacto;
                $arrayParametros['strAsuntoEnvio']                  = $strAsuntoEnvio;
                $arrayParametros['strTipoEnvio']                    = $strTipoEnvio;
                $arrayParametros['strFechaHoraProgramada']          = $strFechaHoraProgramada;
                $arrayParametros['strFechaEjecucionDesde']          = $strFechaEjecucionDesde;
                $arrayParametros['strHoraEjecucion']                = $strHoraEjecucion;
                $arrayParametros['strPeriodicidad']                 = $strPeriodicidad;
                $arrayParametros['intNumeroDia']                    = $intNumeroDia;
                $arrayParametros['strUsrCreacion']                  = $strUsrCreacion;
                $arrayParametros['strIpCreacion']                   = $strIpCreacion;

                $serviceNotifMasiva     = $this->get('comunicaciones.NotifMasivaService');
                $strStatusCrear         = $serviceNotifMasiva->crearEnvioMasivo($arrayParametros);
                
                if($strStatusCrear === "OK")
                {
                    $strMensaje = "Se ha configurado de manera correcta el envío masivo";
                }
                else
                {
                    $strMensaje = "Ha ocurrido un problema al configurar el envío masivo. Por favor notifique a Sistemas!";
                }
            }
            else
            {
                $strStatusCrear = "ERROR";
                $strMensaje     = "Ocurrió un error al validar la configuración del envío masivo. Por favor notifique a Sistemas!";
            }
        }
        else
        {
            $strStatusCrear = "ERROR";
            $strMensaje     = "No se han podido obtener los parámetros necesarios para la configuración del envío masivo. "
                              . "Por favor notifique a Sistemas!";
        }
    
        $arrayResultado                 = array();
        $arrayResultado['strStatus']    = $strStatusCrear;
        $arrayResultado['strMensaje']   = $strMensaje;
        $objResponse->setContent(json_encode($arrayResultado));
        return $objResponse;
    }

    /**
     * validarGuardarEnvioMasivoAction
     * 
     * Función que validará que un solo envío del mismo tipo se encuentre asociado a una misma plantilla 
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017 
     * 
     */
    public function validarGuardarEnvioMasivoAction()
    {
        $objRequest         = $this->getRequest();
        $emComunicacion     = $this->get('doctrine')->getManager('telconet_comunicacion');
        $strMensajeError    = "";

        $objResponse    = new JsonResponse();
        $strTipoEnvio   = $objRequest->get('strTipoEnvio');
        $intIdPlantilla = $objRequest->get('intIdPlantilla');
        $strStatus      = "ERROR";
        if((isset($strTipoEnvio) && !empty($strTipoEnvio))
            && isset($intIdPlantilla) && !empty($intIdPlantilla))
        {
            $arrayParametros                        = array();
            $arrayParametros['strTipoEnvio']        = $strTipoEnvio;
            $arrayParametros['intIdPlantilla']      = $intIdPlantilla;
            $arrayParametros['strEstado']           = 'Configurado';
            $arrayParametros['strSoloValidarEnvio'] = 'S';

            $arrayRespuestaNotifMasivas = $emComunicacion->getRepository('schemaBundle:InfoNotifMasiva')->getNotificacionesMasivas($arrayParametros);
            $intTotalNotifMasivas = $arrayRespuestaNotifMasivas["intTotal"];
            
            if($intTotalNotifMasivas == 0)
            {
                $strStatus = "OK";
            }
            else
            {
                $strMensajeError = "Ya existe otro envío masivo asociado a la plantilla seleccionada y del mismo tipo de envío";
            }
        }
        else
        {
            $strMensajeError = "No se han enviado los parámetros necesarios para realizar la validación de plantillas";
        }
            
        $arrayResultado['strStatus']    = $strStatus;
        $arrayResultado['strMensaje']   = $strMensajeError;

        $objResponse->setContent(json_encode($arrayResultado));
        return $objResponse;
    }

    /**
     * exportarConsultaAction
     *
     * Controlador que obtiene se encarga de ejecutar el método de generacion de excel de acuerdo a filtros enviados via POST
     *                                        
     *
     * @return null
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-09-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-12-2017 Se agrega información de filtros en el excel
     */
    public function exportarConsultaAction()
    {
        $objRequest                     = $this->getRequest();
        $objSession                     = $objRequest->getSession();
        $strCodEmpresa                  = $objSession->get('idEmpresa');
        $strPrefijoEmpresa              = $objSession->get('prefijoEmpresa');
        $emComercial                    = $this->get('doctrine')->getManager('telconet');
        $emGeneral                      = $this->get('doctrine')->getManager('telconet_general');
        $strUsrCreacion                 = $objSession->get('user');

        $strGrupo                       = $objRequest->get('grupoExcel') ? $objRequest->get('grupoExcel') : "";
        $strSubgrupo                    = $objRequest->get('subgrupoExcel') ? $objRequest->get('subgrupoExcel') : "";
        $intIdElementoNodo              = $objRequest->get('idElementoNodoExcel') ? $objRequest->get('idElementoNodoExcel') : 0;
        $intIdElementoSwitch            = $objRequest->get('idElementoSwitchExcel') ? $objRequest->get('idElementoSwitchExcel') : 0;
        $strEstadoServicio              = $objRequest->get('estadoServicioExcel') ? $objRequest->get('estadoServicioExcel') : "";
        $strEstadoPunto                 = $objRequest->get('estadoPuntoExcel') ? $objRequest->get('estadoPuntoExcel') : "";
        $strEstadoCliente               = $objRequest->get('estadoClienteExcel') ? $objRequest->get('estadoClienteExcel') : "";
        $strClientesVIP                 = $objRequest->get('clientesVIPExcel') ? $objRequest->get('clientesVIPExcel') : "";
        $strUsrCreacionFactura          = $objRequest->get('usrCreacionFacturaExcel') ? $objRequest->get('usrCreacionFacturaExcel') : "";
        $intNumerosFactAbiertas         = $objRequest->get('numFacturasAbiertasExcel') ? $objRequest->get('numFacturasAbiertasExcel') : 0;
        $strPuntosFacturacion           = $objRequest->get('puntosFacturacionExcel') ? $objRequest->get('puntosFacturacionExcel') : "";
        $strIdsTiposNegocio             = $objRequest->get('idsTiposNegocioExcel') ? $objRequest->get('idsTiposNegocioExcel') : "";
        $strIdsOficinas                 = $objRequest->get('idsOficinasExcel') ? $objRequest->get('idsOficinasExcel') : "";
        $intIdFormaPago                 = $objRequest->get('idFormaPagoExcel') ? $objRequest->get('idFormaPagoExcel') : 0;
        $strFechaDesdeFactura           = $objRequest->get('fechaDesdeFacturaExcel') ? $objRequest->get('fechaDesdeFacturaExcel') : "";
        $strFechaHastaFactura           = $objRequest->get('fechaHastaFacturaExcel') ? $objRequest->get('fechaHastaFacturaExcel') : "";
        $strSaldoPendientePago          = $objRequest->get('saldoPendientePagoExcel') ? $objRequest->get('saldoPendientePagoExcel') : "";
        $floatValorSaldoPendientePago   = $objRequest->get('valorSaldoPendientePagoExcel') ? $objRequest->get('valorSaldoPendientePagoExcel') : "";
        $strNombreFormaPago = "";
        $strIdsBancosTarjetas           = $objRequest->get('idsBancosTarjetasExcel') ? $objRequest->get('idsBancosTarjetasExcel') : "";
        $strUserComunicacion            = $this->container->getParameter('user_comunicacion');
        $strPasswordComunicacion        = $this->container->getParameter('passwd_comunicacion');
        $strDatabaseDsn                 = $this->container->getParameter('database_dsn');

        if($intIdFormaPago != 0)
        {
            $objFormaPago = $emGeneral->getRepository('schemaBundle:AdmiFormaPago')->find($intIdFormaPago);
            if(is_object($objFormaPago))
            {
                $strNombreFormaPago = $objFormaPago->getDescripcionFormaPago();
            }
        }
        $arrayParametros = array();
        $arrayParametros['strUserComunicacion']             = $strUserComunicacion;
        $arrayParametros['strPasswordComunicacion']         = $strPasswordComunicacion;
        $arrayParametros['strDatabaseDsn']                  = $strDatabaseDsn;
        $arrayParametros['strGrupo']                        = $strGrupo;
        $arrayParametros['strSubgrupo']                     = $strSubgrupo;
        $arrayParametros['intIdElementoNodo']               = $intIdElementoNodo;
        $arrayParametros['intIdElementoSwitch']             = $intIdElementoSwitch;
        $arrayParametros['strEstadoServicio']               = $strEstadoServicio;
        $arrayParametros['strEstadoPunto']                  = $strEstadoPunto;
        $arrayParametros['strEstadoCliente']                = $strEstadoCliente;
        $arrayParametros['strClientesVIP']                  = $strClientesVIP;
        $arrayParametros['strUsrCreacionFactura']           = $strUsrCreacionFactura;
        $arrayParametros['intNumerosFactAbiertas']          = $intNumerosFactAbiertas;
        $arrayParametros['strPuntosFacturacion']            = $strPuntosFacturacion;
        $arrayParametros['strIdsTiposNegocio']              = $strIdsTiposNegocio;
        $arrayParametros['strIdsOficinas']                  = $strIdsOficinas;
        $arrayParametros['intIdFormaPago']                  = $intIdFormaPago;
        $arrayParametros['strNombreFormaPago']              = $strNombreFormaPago;
        $arrayParametros['strIdsBancosTarjetas']            = $strIdsBancosTarjetas;
        $arrayParametros['strCodEmpresa']                   = $strCodEmpresa;
        $arrayParametros['strPrefijoEmpresa']               = $strPrefijoEmpresa;
        $arrayParametros['strFechaDesdeFactura']            = $strFechaDesdeFactura;
        $arrayParametros['strFechaHastaFactura']            = $strFechaHastaFactura;
        $arrayParametros['strSaldoPendientePago']           = $strSaldoPendientePago;
        $arrayParametros['floatValorSaldoPendientePago']    = $floatValorSaldoPendientePago;
        $arrayParametros['page']    = $objRequest->get('page') ? $objRequest->get('page') : "";
        
        $strFiltrosSeleccionadosExcel = $objRequest->get('filtrosSeleccionadosExcel') ? $objRequest->get('filtrosSeleccionadosExcel') : "";
        $arrayRespuestaEnvio = $emComercial->getRepository('schemaBundle:InfoServicio')->getServiciosNotifMasiva($arrayParametros);
        $arrayResultadoEnvio = $arrayRespuestaEnvio["arrayResultado"];

        $objPHPExcel = new PHPExcel();

        $objCacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
        $arrayCacheSettings = array(' memoryCacheSize ' => '1024MB');
        PHPExcel_Settings::setCacheStorageMethod($objCacheMethod, $arrayCacheSettings);
        $objReader = PHPExcel_IOFactory::createReader('Excel5');

        $objPHPExcel = $objReader->load(__DIR__ . "/../Resources/templatesExcel/templateConsultaNotificacionesMasivas.xls");

        $objPHPExcel->getProperties()->setCreator("TELCOS++");
        $objPHPExcel->getProperties()->setLastModifiedBy($strUsrCreacion);
        $objPHPExcel->getProperties()->setTitle("Consulta de Clientes a Notificar Envío Masivo");
        $objPHPExcel->getProperties()->setSubject("Consulta de Clientes");
        $objPHPExcel->getProperties()->setDescription("Resultado de búsqueda de Clientes.");
        $objPHPExcel->getProperties()->setKeywords("Clientes a Notificar Envio Masivo");
        $objPHPExcel->getProperties()->setCategory("Reporte");

        $objPHPExcel->getActiveSheet()->setCellValue('B3', $strUsrCreacion);

        $objPHPExcel->getActiveSheet()->setCellValue('B4', PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, date('m'), date('d'), date('Y'))));
        $objPHPExcel->getActiveSheet()->getStyle('B4')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_XLSX15);

        $intInicioFilaFiltros   = 6;
        $intContadorColumnas    = 0;
        $arrayColFiltros        = array("A", "B", "D", "E");      
        $arrayReplaceTd = array(
                                '<td>'      => '', 
                                '<b>'       => '',
                                '</b>'      => '',
                                '&nbsp;'    => ''
                          );
        if(!empty($strFiltrosSeleccionadosExcel))
        {
            $arrayTrsFiltrosSeleccionados = explode("</tr>",$strFiltrosSeleccionadosExcel, -1);
            if(!empty($arrayTrsFiltrosSeleccionados))
            {
                foreach($arrayTrsFiltrosSeleccionados as $strTrFiltro)
                {
                    $strTrFiltro = str_replace("<tr>", "", $strTrFiltro);
                    $arrayTdsTrFiltro = explode("</td>",$strTrFiltro, -1);
                    if(!empty($arrayTdsTrFiltro))
                    {
                        foreach($arrayTdsTrFiltro as $strTdTrFiltro)
                        {
                            $strTdTrFiltro = str_replace(array_keys($arrayReplaceTd), array_values($arrayReplaceTd), $strTdTrFiltro);
                            $objPHPExcel->getActiveSheet()->setCellValue($arrayColFiltros[$intContadorColumnas]. $intInicioFilaFiltros, 
                                                                         $strTdTrFiltro);
                            $intContadorColumnas++;
                        }
                    }
                    if($intContadorColumnas >= count($arrayColFiltros))
                    {
                        $intContadorColumnas = 0;
                        $intInicioFilaFiltros++;
                    }
                }
            }
        }
        $intIndice = 18;
        
        foreach($arrayResultadoEnvio as $arrayEnvio):
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $intIndice, $arrayEnvio['LOGIN']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $intIndice, $arrayEnvio['NOMBRES_CLIENTE']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $intIndice, $arrayEnvio['NOMBRE_OFICINA']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $intIndice, $arrayEnvio['NOMBRE_TIPO_NEGOCIO']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $intIndice, $arrayEnvio['DESCRIPCION_PRODUCTO']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $intIndice, $arrayEnvio['ESTADO']);
            $intIndice = $intIndice + 1;
        endforeach;

        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);


        $objPHPExcel->getActiveSheet()->setTitle('Reporte Envio Masivo');

        $objPHPExcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Consulta_de_Envio_Masivo_' . date('d_M_Y') . '.xls"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
    }

    /**
     * verPlantillaAction
     * 
     * Función que mostrará el contenido de la plantilla seleccionada
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-09-2017 
     * 
     */
    public function verPlantillaAction()
    {
        $objRequest     = $this->getRequest();
        $intIdPlantilla = $objRequest->get('intIdPlantilla') ? $objRequest->get('intIdPlantilla') : 0;
        $emComunicacion = $this->get('doctrine')->getManager('telconet_comunicacion');
        $objPlantilla   = $emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->find($intIdPlantilla);

        $strContenido = "";
        if(is_object($objPlantilla))
        {
            $strContenido = $objPlantilla->getPlantilla();
        }
        $objResponse = new JsonResponse();
        $objResponse->setContent(json_encode(array("strContenidoPlantilla" => $strContenido)));
        return $objResponse;
    }
    
    
    /**
     * getTiposFacturaFiltrosAction
     * 
     * Función que obtiene los tipos de facturas que se desea filtrar 
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-10-2017 
     * 
     */
    public function getTiposFacturaFiltrosAction()
    {
        $objRequest         = $this->getRequest();
        $objSession         = $objRequest->getSession();
        $strCodEmpresa      = $objSession->get('idEmpresa');
        $emGeneral          = $this->get('doctrine')->getManager('telconet_general');
        $arrayTiposFactura  = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('TIPOS_FACTURA_FILTROS_ENVIO_MASIVO', '', '', '', '', '', '', '', '', $strCodEmpresa, '');
        $objResponse = new JsonResponse();
        $objResponse->setData(array("arrayRegistros" => $arrayTiposFactura));
        return $objResponse;
    }

}
