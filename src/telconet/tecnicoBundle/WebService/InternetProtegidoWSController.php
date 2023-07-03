<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Clase que contiene las funciones necesarias para el funcionamiento de los servicios Internet Protegido ejecutados desde los procesos masivos
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 22-08-2019
 */
class InternetProtegidoWSController extends BaseWSController
{
    /**
     * Función que sirve para procesar las opciones de Netlife Defense que vienen desde los procesos masivos de Megadatos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-05-2017
     * 
     * @param \Symfony\Component\HttpFoundation\Request $objRequest
     * @return \Symfony\Component\HttpFoundation\Response $objResponse
     * 
     */
    public function procesarAction(Request $objRequest)
    {
        $arrayDataWs    = json_decode($objRequest->getContent(),true);
        $arrayResponse  = null;
        $strToken       = "";
        $objResponse    = new Response();
        $strOp          = $arrayDataWs['op'];
        
        if(isset($arrayDataWs['source']) && !empty($arrayDataWs['source']))
        {
            $strToken = $this->validateGenerateToken($arrayDataWs['token'], $arrayDataWs['source'], $arrayDataWs['user']);
        }
        if(!isset($strToken) || empty($strToken))
        {
            return new Response(json_encode(array(
                    'status'    => 403,
                    'mensaje'   => "token invalido",
                    'token'     => "",
                    'data'      => array()
                    )
                )
            );
        }
        $serviceInternetProtegido = $this->get('tecnico.InternetProtegido');
        if($strOp)
        {
            switch($strOp)
            {
                case 'cortarLicencias':
                    $arrayResponse = $serviceInternetProtegido->cortarLicencias($arrayDataWs);
                    break;
                
                case 'reconectarLicencias':
                    $arrayResponse = $serviceInternetProtegido->reconectarLicencias($arrayDataWs);
                    break;
                
                case 'cambiarPlanLicencias':
                    $arrayResponse = $serviceInternetProtegido->cambiarPlanLicencias($arrayDataWs);
                    break;
                
                case 'cancelarLicencias':
                    $arrayResponse = $serviceInternetProtegido->cancelarLicencias($arrayDataWs);
                    break;
                
                default:
                    $arrayResponse['status']    = $this->status['METODO'];
                    $arrayResponse['mensaje']   = $this->mensaje['METODO'];
                    $arrayResponse['token']     = "";
                    $arrayResponse['data']      = array();
            }
        }
        if(isset($arrayResponse))
        {
            $arrayResponse['token'] = $strToken;
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponse));
        }
        return $objResponse;
    }
}
