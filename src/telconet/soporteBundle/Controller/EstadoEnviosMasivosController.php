<?php

namespace telconet\soporteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Clase que maneja las acciones relacionadas a los estados de los envío de notificaciones masivas en TN
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 27-09-2017 
 */
class EstadoEnviosMasivosController extends Controller
{

    /**
     * indexAction
     * 
     * @Secure(roles="ROLE_398-1")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-09-2017
     * 
     */
    public function indexAction()
    {
        $objRequest        = $this->getRequest();
        $objSession        = $objRequest->getSession();
        $strEmpresaCod     = $objSession->get('prefijoEmpresa');
        $emSeguridad       = $this->getDoctrine()->getManager("telconet_seguridad");
        $objItemMenu       = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("398", "1");
        $objItemMenuPadre  = $objItemMenu->getItemMenuId();
        $objSession->set('menu_modulo_activo', $objItemMenuPadre->getNombreItemMenu());
        $objSession->set('nombre_menu_modulo_activo', $objItemMenuPadre->getTitleHtml());
        $objSession->set('id_menu_modulo_activo', $objItemMenuPadre->getId());
        $objSession->set('imagen_menu_modulo_activo', $objItemMenuPadre->getUrlImagen());

        return $this->render('soporteBundle:EstadoEnviosMasivos:index.html.twig', array(
                'objItem'    => $objItemMenu,
                'strEmpresa' => $strEmpresaCod
        ));
    }

    /**
     * gridAction
     * 
     * Función que obtiene el resultado de la consulta de todos los envíos masivos configurados
     *  
     * @Secure(roles="ROLE_398-7")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 26-09-2017 
     * 
     */
    public function gridAction()
    {
        $objRequest     = $this->getRequest();
        $emComunicacion = $this->get('doctrine')->getManager('telconet_comunicacion');

        $objResponse    = new JsonResponse();
        $strTipoEnvio   = $objRequest->get('strTipoEnvio') ? $objRequest->get('strTipoEnvio') : "";
        $intIdPlantilla = $objRequest->get('intIdPlantilla') ? $objRequest->get('intIdPlantilla') : 0;
        $strEstado      = $objRequest->get('strEstado') ? $objRequest->get('strEstado') : '';
        $intStart       = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit       = $objRequest->get('limit') ? $objRequest->get('limit') : 0;

        $arrayParametros = array();
        $arrayParametros['intStart']        = $intStart;
        $arrayParametros['intLimit']        = $intLimit;
        $arrayParametros['strTipoEnvio']    = $strTipoEnvio;
        $arrayParametros['intIdPlantilla']  = $intIdPlantilla;
        $arrayParametros['strEstado']       = $strEstado;

        $strJsonRespuesta = $emComunicacion->getRepository('schemaBundle:InfoNotifMasiva')->getJSONNotificacionesMasivas($arrayParametros);
        $objResponse->setContent($strJsonRespuesta);
        return $objResponse;
    }

    /**
     * showInfoEnvioMasivoAction
     * 
     * Función que muestra la información de los filtros del envío masivo
     *  
     * @Secure(roles="ROLE_398-6")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017 
     * 
     */
    public function showInfoEnvioMasivoAction($intIdNotifMasiva)
    {
        $emComunicacion = $this->getDoctrine()->getManager("telconet_comunicacion");
        if(null == $objNotifMasiva = $emComunicacion->find('schemaBundle:InfoNotifMasiva', $intIdNotifMasiva))
        {
            throw new NotFoundHttpException('No existe la notificación masiva que se quiere mostrar');
        }
        $arrayDataNotificacionMasiva = array();
        $arrayRespuestaNotificacionMasiva = $emComunicacion->getRepository('schemaBundle:InfoNotifMasiva')
                                                           ->getNotificacionesMasivas(array("intIdNotifMasiva" => $intIdNotifMasiva));
        if($arrayRespuestaNotificacionMasiva['intTotal'] > 0)
        {
            $arrayDataNotificacionMasiva = $arrayRespuestaNotificacionMasiva["arrayResultado"][0];
        }

        $objInfoNotifMasivaHist = $emComunicacion->getRepository('schemaBundle:InfoNotifMasivaHist')
                                                 ->findOneBy(array( "intNotifMasivaId"  => $objNotifMasiva,
                                                                    "strAccion"         => "crear",
                                                                    "strEstado"         => "Pendiente"));
        $strObservacionFiltros = "";
        if(is_object($objInfoNotifMasivaHist))
        {
            $strObservacion = $objInfoNotifMasivaHist->getObservacion();
            if(($intPosParam = strpos($strObservacion, "PARAMETROS")) !== false)
            {
                $strObservacionFiltros = substr($strObservacion, $intPosParam + 10);
            }
        }

        return $this->render('soporteBundle:EstadoEnviosMasivos:show.html.twig', array(
                'strObservacionFiltros'         => $strObservacionFiltros,
                'objNotifMasiva'                => $objNotifMasiva,
                'arrayDataNotificacionMasiva'   => $arrayDataNotificacionMasiva
        ));
    }

    /**
     * showGridLogsEnviosMasivosAction
     * 
     * Función que muestra la información del envío masivo con sus respectivas ejecuciones
     *  
     * @Secure(roles="ROLE_398-6")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017 
     * 
     */
    public function showGridLogsEnviosMasivosAction()
    {
        $objRequest     = $this->getRequest();
        $emComunicacion = $this->get('doctrine')->getManager('telconet_comunicacion');

        $objResponse        = new JsonResponse();
        $intIdNotifMasiva   = $objRequest->get('intIdNotifMasiva') ? $objRequest->get('intIdNotifMasiva') : 0;
        $strEstado          = $objRequest->get('strEstado') ? $objRequest->get('strEstado') : '';
        $intStart           = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit           = $objRequest->get('limit') ? $objRequest->get('limit') : 0;

        $arrayParametros                        = array();
        $arrayParametros['intStart']            = $intStart;
        $arrayParametros['intLimit']            = $intLimit;
        $arrayParametros['intIdNotifMasiva']    = $intIdNotifMasiva;
        $arrayParametros['strEstado']           = $strEstado;

        $strJsonRespuesta = $emComunicacion->getRepository('schemaBundle:InfoNotifMasivaLog')->getJSONNotificacionesMasivasLogs($arrayParametros);
        $objResponse->setContent($strJsonRespuesta);
        return $objResponse;
    }

    /**
     * showGridLogsDetsEnviosMasivosAction
     * 
     * Función que muestra la información del envío masivo con sus respectivas ejecuciones
     *  
     * @Secure(roles="ROLE_398-6")
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 27-09-2017 
     * 
     */
    public function showGridLogsDetsEnviosMasivosAction()
    {
        $objRequest             = $this->getRequest();
        $emComunicacion         = $this->get('doctrine')->getManager('telconet_comunicacion');

        $objResponse            = new JsonResponse();
        $intIdNotifMasivaLog    = $objRequest->get('intIdNotifMasivaLog') ? $objRequest->get('intIdNotifMasivaLog') : 0;
        $strLogin               = $objRequest->get('strLogin') ? $objRequest->get('strLogin') : '';
        $strNombres             = $objRequest->get('strNombres') ? $objRequest->get('strNombres') : '';
        $strEstado              = $objRequest->get('strEstado') ? $objRequest->get('strEstado') : '';
        $intStart               = $objRequest->get('start') ? $objRequest->get('start') : 0;
        $intLimit               = $objRequest->get('limit') ? $objRequest->get('limit') : 0;

        $arrayParametros                        = array();
        $arrayParametros['intStart']            = $intStart;
        $arrayParametros['intLimit']            = $intLimit;
        $arrayParametros['intIdNotifMasivaLog'] = $intIdNotifMasivaLog;
        $arrayParametros['strLogin']            = $strLogin;
        $arrayParametros['strNombres']          = $strNombres;
        $arrayParametros['strEstado']           = $strEstado;

        $strJsonRespuesta   = $emComunicacion->getRepository('schemaBundle:InfoNotifMasivaLogDet')
                                             ->getJSONNotificacionesMasivasLogsDets($arrayParametros);
        $objResponse->setContent($strJsonRespuesta);
        return $objResponse;
    }

    /**
     * eliminarEnvioMasivoAction
     * 
     * Función que elimina el envío masivo 
     * 
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-09-2017 
     * 
     */
    public function eliminarEnvioMasivoAction()
    {
        $objRequest     = $this->getRequest();
        $objSession     = $objRequest->getSession();
        $strUsrSesion   = $objSession->get('user');

        $objResponse        = new JsonResponse();
        $intIdNotifMasiva   = $objRequest->get('intIdNotifMasiva') ? $objRequest->get('intIdNotifMasiva') : 0;

        $arrayParametros = array();
        $arrayParametros['strUsrCreacion']          = $strUsrSesion;
        $arrayParametros['intIdNotifMasiva']        = $intIdNotifMasiva;

        $serviceNotifMasiva = $this->get('comunicaciones.NotifMasivaService');
        $strStatusEliminar  = $serviceNotifMasiva->eliminarNotificacionMasiva($arrayParametros);
        $strMensaje         = "";
        if($strStatusEliminar === "OK")
        {
            $strMensaje = "Se ha eliminado correctamente el envío masivo";
        }
        else
        {
            $strMensaje = "Ha ocurrido un problema al eliminar el envío masivo. Por favor notificar a Sistemas!";
        }

        $arrayResultado['strStatus']    = $strStatusEliminar;
        $arrayResultado['strMensaje']   = $strMensaje;

        $objResponse->setContent(json_encode($arrayResultado));
        return $objResponse;
    }
}

