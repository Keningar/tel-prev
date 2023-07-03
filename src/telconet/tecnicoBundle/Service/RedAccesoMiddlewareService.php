<?php

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Clase para llamar a metodos ws provisionados por RDA para la ejecucion de script sobre los equipos de MD.
 * 
 * @author Francisco Adum <fadum@netlife.net.ec>
 */
class RedAccesoMiddlewareService
{
    /**
     * Codigo de respuesta: Respuesta valida
     */
    public static $STATUS_OK      = 200;
    public static $strStatusError = 500;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    
    //Variables para el Middleware
    private $urlMiddleware;
    private $urlMicroservice;
    private $ambiente;
    private $ejecutaScripts;
    private $strRdaSinEsperaRespuesta;
    private $strRdaEmpresasSinEsperaRespuesta;
    private $strRdaConnectTimeoutSinEsperaRespuesta;
    private $strRdaTimeoutSinEsperaRespuesta; 
    
    //Entity Manager
    private $emComercial;
    private $emInfraestructura;
    private $serviceUtil;
    /**
    * service $strIpClient
    */
    private $strIpClient;
    /**
     *
     * @var type URL del WS de NOC para notificacion
     */
    private $strWsNocUrl;
    private $objWSTokenGeneratorURL;
    private $serviceTokenCas;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emInfraestructura    = $objContainer->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComercial          = $objContainer->get('doctrine')->getManager('telconet');
        $this->urlMiddleware        = $objContainer->getParameter('ws_rda_middleware_url');
        $this->urlMicroservice      = $objContainer->getParameter('ms_modelo_predictivo_url');
        $this->ejecutaScripts       = $objContainer->getParameter('ws_rda_ejecuta_scripts');
        $this->ambiente             = $objContainer->getParameter('ws_rda_ambiente');
        $this->restClient           = $objContainer->get('schema.RestClient');
        $this->serviceUtil          = $objContainer->get('schema.Util');
        $this->strRdaSinEsperaRespuesta                 = $objContainer->getParameter('ws_rda_sin_espera_respuesta');
        $this->strRdaEmpresasSinEsperaRespuesta         = $objContainer->getParameter('ws_rda_empresas_sin_espera_respuesta');
        $this->strRdaConnectTimeoutSinEsperaRespuesta   = $objContainer->getParameter('ws_rda_connecttimeout_sin_espera_respuesta');
        $this->strRdaTimeoutSinEsperaRespuesta          = $objContainer->getParameter('ws_rda_timeout_sin_espera_respuesta');
        $this->strIpClient                              = $objContainer->getParameter('ws_token_ip_url');
        $this->objWSTokenGeneratorURL                   = $objContainer->getParameter('seguridad.token_generate_url');
        $this->strWsNocUrl                              = $objContainer->getParameter('ws_noc_url');
        $this->serviceTokenCas            = $objContainer->get('seguridad.TokenCas');
    }
    
    /**
     * Funcion que sirve para ejecutar la llamada al ws del middleware de RDA
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.0 3-05-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-01-2018 Se agrega el campo empresa con el prefijo de la empresa. Por defecto se enviará "MD"
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 10-07-2019 Se borran líneas de código colocadas incorrectamente por pruebas
     * @since 1.1
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.3 19-03-2021 Se registra response del middleware en caso de error.
     * @since 1.2
     * 
     * @return arrayResultado 
     */
    public function middleware($jsonDatosMiddleware)
    {
        $arrayParametros                            = json_decode($jsonDatosMiddleware, true);
        $arrayParametros['comandoConfiguracion']    = "SI";
        if(empty($arrayParametros["empresa"]))
        {
            $arrayParametros["empresa"] = "MD";
        }
        $jsonDatosMiddleware                        = json_encode($arrayParametros);
        
        $options = array(CURLOPT_SSL_VERIFYPEER => false);
        
        $responseJson = $this->restClient->postJSON($this->urlMiddleware, $jsonDatosMiddleware , $options);
        if($responseJson['status'] == static::$STATUS_OK && $responseJson['result'] != false)
        {   
            $arrayResponse = json_decode($responseJson['result'],true);

            $arrayResultado = $arrayResponse;
        }
        else
        {
            $this->serviceUtil->insertLog(array(
                    'enterpriseCode'      => "10",
                    'logType'             => 1,
                    'logOrigin'           => 'TELCOS',
                    'application'         => 'TELCOS',
                    'appClass'            => basename(__CLASS__),
                    'appMethod'           => basename(__FUNCTION__),
                    'descriptionError'    => json_encode($responseJson),
                    'status'              => 'Fallido',
                    'appAction'           => 'Activación middleware',
                    'inParameters'        => $jsonDatosMiddleware,
                    'creationUser'        => $arrayParametros['usrCreacion']));
            
            $arrayResultado['status']      = "ERROR";
            if($responseJson['status'] == "0")
            {
                $arrayResultado['mensaje']  = "No Existe Conectividad con el WS RDA.";
            }
            else
            {
                $strMensajeError = 'ERROR';

                if(isset($responseJson['mensaje']) && !empty($responseJson['mensaje']))
                {
                    $strMensajeError = $responseJson['mensaje'];
                }

                $arrayResultado['mensaje']  = "Error de RDA :".$strMensajeError;
            }
        }

        return $arrayResultado ;
    }
    public function microservicio($arrayJsonDatosMS)
    {
        $arrayTokenCas   = $this->serviceTokenCas->generarTokenCas();
        $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_HTTPHEADER => array('Content-Type: application/json',
                                        'tokencas:'.$arrayTokenCas['strToken']));
        //Consumo de MS por metodo POST
        $arrayResponse = $this->restClient->postJSON($this->urlMicroservice, $arrayJsonDatosMS , $arrayOptions);

        if($arrayResponse['status'] == static::$STATUS_OK)
        {   
            $arrayResponse = json_decode($arrayResponse['result'],true);

            $arrayResultado = $arrayResponse;
        }
        else
        {
            $this->serviceUtil->insertLog(array(
                    'enterpriseCode'      => "10",
                    'logType'             => 1,
                    'logOrigin'           => 'TELCOS',
                    'application'         => 'TELCOS',
                    'appClass'            => basename(__CLASS__),
                    'appMethod'           => basename(__FUNCTION__),
                    'descriptionError'    => json_encode($arrayResponse),
                    'status'              => 'Fallido',
                    'appAction'           => 'Activación middleware',
                    'inParameters'        => $arrayJsonDatosMS
                    )
                );
            
            $arrayResultado['status']      = "ERROR";
            if($arrayResponse['status'] == "0")
            {
                $arrayResultado['mensaje']  = "No Existe Conectividad con el MS Modelo Predictivo.";
            }
            elseif($arrayResponse['status'] == "404")
            {  
                $arrayResultado['mensaje']  = "No existen registros.";
               
                
            }
        }

        return $arrayResultado ;
    }
    /**
     * Funcion que sirve para ejecutar la llamada al ws de NOC
     * para proceso de cambio de linea pon
     * 
     * @author Manuel Carpio  <mcarpio@telconet.ec>
     * @version 1.0 21-09-2022
     * 
     * 
     * @return arrayResultado 
     */
    public function notificacionNoc($arrayParametros)
    {
        $arrayDatosNoc      = array();
        $arrayResponseWSNoc = array();
        $arrayRespuestaFinal     = array();
        $strOptions         = "";
        try
        {
            $arrayDatosNoc      = json_encode($arrayParametros);
            $strOptions         = array(CURLOPT_SSL_VERIFYPEER => false);   
            $arrayResponseWSNoc = $this->restClient->postJSON($this->strWsNocUrl,
                                                                     $arrayDatosNoc,
                                                                     $strOptions);
                $arrayResult= json_decode($arrayResponseWSNoc['result'], true);
                if ($arrayResponseWSNoc['status'] == static::$STATUS_OK)
                {
                    $arrayRespuestaFinal = array('status' => static::$STATUS_OK, 
                                              'result' => $arrayResult);
                }
                else
                {
                    $this->serviceUtil->insertLog(array(
                        'enterpriseCode'      => "10",
                        'logType'             => 1,
                        'logOrigin'           => 'TELCOS',
                        'application'         => 'TELCOS',
                        'appClass'            => basename(__CLASS__),
                        'appMethod'           => basename(__FUNCTION__),
                        'descriptionError'    => json_encode($arrayResponseWSNoc),
                        'status'              => 'Fallido',
                        'appAction'           => 'Activación middleware',
                        'inParameters'        => json_encode($arrayParametros),
                        'creationUser'        => $arrayParametros['audit']['usrCreacion']));
                    $arrayRespuestaFinal = array('status' => static::$strStatusError, 
                                              'result' => 'Error en la respuesta al enviar notificaciòn a NOC');
                    error_log(print_R($arrayResponseWSNoc,true));
                }
        }
        catch(\Exception $e)
        {
            $arrayRespuestaFinal = array('status' => static::$strStatusError, 
                                      'result' => 'Error en la respuesta al enviar notificaciòn a NOC');

        }
        return $arrayRespuestaFinal;
    }

    /**
     * Funcion que sirve para generar el token para envio al ws de NOC
     * para proceso de cambio de linea pon
     * 
     * @author Manuel Carpio  <mcarpio@telconet.ec>
     * @version 1.0 21-09-2022
     * 
     * @param type array $arrayParametros [
     *             "strIpClient"           => IP origen de donde proviene la peticion
     *                                    ]
     * 
     * @return string $arrayResult['token']
     * 
     * @return arrayResultado 
     */
    public function generateToken($arrayJson)
    {
        $arrayRespuestaFinal = array();
        $strDataString = json_encode($arrayJson);
        $arrayOptions  = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponse = $this->restClient->postJSON($this->objWSTokenGeneratorURL, $strDataString, $arrayOptions);
        
        if ($arrayResponse['status'] == static::$STATUS_OK)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $arrayRespuestaFinal = array('status' => static::$STATUS_OK, 
                                      'result' => json_decode($arrayResponse['result'], true));
        }
        else
        {           
            $arrayRespuestaFinal = array('status' => static::$strStatusError, 
                                      'result' => 'Error en la respuesta al obtener Token Security');
        }
        return $arrayRespuestaFinal;
    }
    
    /**
     * Función que sirve para ejecutar la llamada al ws del middleware de RDA sin esperar una respuesta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-11-2021
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 09-12-2021 Se agregan comillas en los índices del arreglo $arrayOptions puesto que da problema al ejecutar un post desde el 
     *                         comand de php por un NOTICE
     * 
     */
    public function ejecutaWsSinEsperarRespuestaMiddleware($arrayParametros)
    {
        if(isset($arrayParametros) && !empty($arrayParametros))
        {
            $strStatusMiddleware                        = $arrayParametros['statusMiddleware'];
            $strPrefijoEmpresa                          = $arrayParametros['empresa'];
            $strRdaPermiteSinEsperaRespuesta            = $this->strRdaSinEsperaRespuesta;
            $strRdaPermiteEmpresasSinEsperaRespuesta    = $this->strRdaEmpresasSinEsperaRespuesta;
            $arrayEmpresasPermitidas                    = explode(",", $strRdaPermiteEmpresasSinEsperaRespuesta);
            $arrayDatos                                 = $arrayParametros['datos'];
            if($strStatusMiddleware === "OK" && $strRdaPermiteSinEsperaRespuesta === "SI"
                && isset($arrayEmpresasPermitidas) && !empty($arrayEmpresasPermitidas) && in_array($strPrefijoEmpresa,$arrayEmpresasPermitidas))
            {
                $arrayParametros['comandoConfiguracion'] = "SI";
                unset($arrayParametros['statusMiddleware']);
                $objJsonDatosMiddleware = json_encode($arrayParametros);
                $arrayOptions = array(  CURLOPT_SSL_VERIFYPEER              => false,
                                        'CURLOPT_CONNECTTIMEOUT_CUSTOM'     => $this->strRdaConnectTimeoutSinEsperaRespuesta,
                                        'CURLOPT_TIMEOUT_CUSTOM'            => $this->strRdaTimeoutSinEsperaRespuesta
                                      );
                $arrayResponseJson = $this->restClient->postJSON($this->urlMiddleware, $objJsonDatosMiddleware , $arrayOptions);
            }
        }
    }

    
}