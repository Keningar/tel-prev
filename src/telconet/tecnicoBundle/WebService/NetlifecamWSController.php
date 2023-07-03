<?php

namespace telconet\tecnicoBundle\WebService;

use telconet\schemaBundle\DependencyInjection\BaseWSController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Clase que contiene las funciones necesarias para el funcionamiento del Portal Netlifecam.
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 19-05-2017
 */
class NetlifecamWSController extends BaseWSController
{
    /**
     * Función que sirve para procesar las opciones que vienen desde el portal de netlifecam
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
        $objResponse    = new Response();
        $strOpcion      = $arrayDataWs['opcion'];
        $strToken       = $this->validateGenerateToken($arrayDataWs['token'], $arrayDataWs['source'], $arrayDataWs['user']);
        if(!$strToken)
        {
            return new Response(json_encode(array(
                    'status' => 403,
                    'mensaje' => "token invalido"
                    )
                )
            );
        }
        
        $servicePortalNetlifecam    = $this->get('tecnico.PortalNetlifeCamService');
        $arrayDataWs['app']         = "NETLIFECAM";
        
        $arrayData =  $arrayDataWs['data'];
        
        if($strOpcion)
        {
            switch($strOpcion)
            {
                case 'getInfoClienteCodVer':
                    $arrayResponse          = $servicePortalNetlifecam->getInfoClienteCodVer($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'getCamarasCliente':
                    $arrayResponse          = $servicePortalNetlifecam->getCamarasCliente($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'editarNombreCam':
                    $arrayResponse          = $servicePortalNetlifecam->editarNombreCam($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'guardarClaveCliente':
                    $arrayDataWs['token']   = $strToken;
                    $arrayResponse          = $servicePortalNetlifecam->guardarClaveCliente($arrayDataWs);
                    break;
                
                case 'autenticarCliente':
                    $arrayDataWs["op"]      = "autenticar";
                    $arrayDataWs['token']   = $strToken;
                    $arrayResponse          = $servicePortalNetlifecam->autenticarCliente($arrayDataWs);
                    break;
                
                case 'actualizarPassCliente':
                    $arrayDataWs['token']   = $strToken;
                    $arrayResponse          = $servicePortalNetlifecam->actualizarPassCliente($arrayDataWs);
                    break;
                
                case 'actualizarInfoCliente':
                    $arrayDataWs["op"]      = "actualizar";
                    $arrayDataWs['token']   = $strToken;
                    $arrayResponse          = $servicePortalNetlifecam->callWsLdapPortalNetlifecam($arrayDataWs);
                    break;
                
                case 'getInfoProdAdic':
                    $arrayResponse          = $servicePortalNetlifecam->getInfoProdAdic();
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'comprarProdAdic':
                    $arrayData["orig"]      = "PORTAL";
                    $arrayResponse          = $servicePortalNetlifecam->comprarProdAdic($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'getCamarasHrs':
                    $arrayResponse          = $servicePortalNetlifecam->getCalculoHorasCamaras($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'getConfigAlarma':
                    $arrayResponse          = $servicePortalNetlifecam->getConfigAlarma($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'guardarConfigAlarma':
                    $arrayResponse          = $servicePortalNetlifecam->guardarConfigAlarma($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                case 'olvidoPassCliente':
                    $arrayDataWs["op"]      = "actualizar";
                    $arrayDataWs['token']   = $strToken;
                    $arrayResponse          = $servicePortalNetlifecam->olvidoPassCliente($arrayDataWs);
                    break;
                
                case 'actualizarEstadoGrabacion':
                    $arrayResponse          = $servicePortalNetlifecam->actualizarEstadoGrabacion($arrayData);
                    $arrayResponse['token'] = $strToken;
                    break;
                
                default:
                    $arrayResponse['status']    = $this->status['METODO'];
                    $arrayResponse['msj']       = $this->mensaje['METODO'];
            }
        }
        if(isset($arrayResponse))
        {
            $objResponse = new Response();
            $objResponse->headers->set('Content-Type', 'text/json');
            $objResponse->setContent(json_encode($arrayResponse));
        }
        return $objResponse;
    }
}
