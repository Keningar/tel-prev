<?php

/* 
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace telconet\seguridadBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
* Clase TokenCasService
*
* Clase que maneja la generación de Token en Cast.
*
* @author Nestor Naula <nnaulal@telconet.ec>
* @version 1.0 25-10-2020
*/
class TokenCasService
{
    /**
    *
    * @var \telconet\schemaBundle\Service\RestClientService
    */
    private $objRestClient;
    
    //Variable para Token Cas
    private $strUrlTokenCas;
    public static $intStatusOk = 200; 
    public static $intStatusTokenError = 500; 
    
    public function setDependencies(Container $objContainer)
    {
        $this->strUrlTokenCas               = $objContainer->getParameter('ws_token_cas_url');
        $this->strUsernameCas               = $objContainer->getParameter('username_cas');
        $this->strPasswordCas               = $objContainer->getParameter('password_cas');
        $this->strApiKeyCas                 = $objContainer->getParameter('apiKey_cas');
        $this->objRestClient                = $objContainer->get('schema.RestClient');
        $this->serviceUtil                  = $objContainer->get('schema.Util');
    }
    
    /**
     * generarTokenCas
     * Función que sirve para generar el token Cas
     * 
     * @author Nestor Naula <nnaulal@telconet.ec>
     * @version 1.0 25-10-2020
     * 
     * @author Alex Gómez <algomez@telconet.ec>
     * @version 1.1 15-11-2022  Se añade CURLOPT_HTTPHEADER en objOptions por error en ejecuciones desde consola
     * 
     * @return array $arrayResultado [
     *      strStatus  - Estado de la consulta
     *      strEstado  - Estado de la incidencia si sigue o no vulnerable
     *      strMensaje - Mensaje que indica si se realizó o no la consulta
     * ]
     */
    public function generarTokenCas()
    {   
        $arrayDatosToken = array("username" => $this->strUsernameCas,
                                 "password" => $this->strPasswordCas,
                                 "apiKey"   => $this->strApiKeyCas);

        $objOptions         = array(CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_HTTPHEADER => array('Content-Type: application/json'));
        $strJsonData        = json_encode($arrayDatosToken);
        $arrayResponseJson  = $this->objRestClient->postJSON($this->strUrlTokenCas, $strJsonData , $objOptions); 
        $strJsonRespuesta   = json_decode($arrayResponseJson['result'],true);
        if( $strJsonRespuesta['code'] == 0 && isset($strJsonRespuesta['data']) && !empty($strJsonRespuesta['data'])
        && isset($strJsonRespuesta['message']) && !empty($strJsonRespuesta['message'])
        && isset($strJsonRespuesta['status']) && !empty($strJsonRespuesta['status']))
        {   
            $arrayResponse = array('strStatus' => $strJsonRespuesta['status'],
                                   'strToken'  => $strJsonRespuesta['data'],
                                   'strMensaje'=> $strJsonRespuesta['message']);
            
            $arrayResultado = $arrayResponse;
        }
        else
        {
            $arrayResultado['strStatus']      = "ERROR";
            $arrayResultado['strToken']       = "";
            if(isset($strJsonRespuesta['message']) && !empty($strJsonRespuesta['message']))
            {
                $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];
            }
            else
            {
                $arrayResultado['strMensaje']  = "ERROR: Sin conexion";
            }
        }
        return $arrayResultado ;
    }
}
