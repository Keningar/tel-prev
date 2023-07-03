<?php

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase para llamar a metodos ws provisionados por ambiente CloudForm para generación de eventos de registro de usuario y de aprovisionamiento
 * y desaprovisionamiento de Máquinas Virtuales
 * # URL de WS
 *     # rutas de ws para ejecucion de los scripts
 *     url: http:{{url}}/wscloudpublicauiod/webresources/entidad.registrousuario/  
 * 
 * @author Allan Suarez <arsuarez@telconet.ec>
 * @since  26-08-2018
 */
class CloudFormsService
{
    
    /**
     * Codigo de respuesta: Respuesta valida
     */
    public static $intStatusOk = 200;        
        
    public static $intStatusComunicationError = 404;
        
    public static $intStatusScriptError = 403;
        
    public static $intStatusTokenError = 500;        
        
    private $webServiceCloudFormRestURL;      
    
    private $webServiceTokenGeneratorURL;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
        
    private $ambienteEjecuta;
    
    //Entity Manager
    private $emComercial;
    private $emInfraestructura; 
    
    //Variables staticas
    private static $strWsAppName          = 'APP.CLOUDFORM';
    private static $strWsServiceCloudform = 'CloudFormsWSController';
    private static $strWsGatewayCloudForm = 'Telcos';
    
    private $strPathTelcos;
    private $strHostScripts;
    
    private $serviceUtil;
    private $strPathJava; 

    public function __construct(Container $container) 
    {                
        $this->emInfraestructura           = $container->get('doctrine')->getManager('telconet_infraestructura');        
        $this->emComercial                 = $container->get('doctrine')->getManager('telconet');        
        $this->webServiceCloudFormRestURL  = $container->getParameter('cloudforms_webService_url');       
        $this->webServiceTokenGeneratorURL = $container->getParameter('seguridad.token_generate_url');       
        $this->ambienteEjecuta             = $container->getParameter('cloudforms_webService_ambienteEjecuta'); 
        $this->restClient                  = $container->get('schema.RestClient');
        $this->strPathTelcos               = $container->getParameter('path_telcos');       
        $this->strHostScripts              = $container->getParameter('host_scripts'); 
        $this->serviceUtil                 = $container->get('schema.Util');
        $this->strPathJava                 = $container->getParameter('path_java_soporte');
    }            
    
    /**
     * callCloudFormWebService
     * 
     * Metodo llamado por los servicio que requieren realizar ejecuciones sobre la plataforma de Cloudform
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 26-07-2018
     *    
     * @param  Array $arrayPeticiones [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return Array $arrayRespuesta  [ status , mensaje ]
     */
    public function callCloudFormWebService($arrayPeticiones)
    {
        $arrayRespuesta = $this->executeScript($arrayPeticiones);
        return $arrayRespuesta;
    }
    
    /**
     * generateJson
     * 
     * Metodo que se encargado de generar el array a enviarle al método establecido para ejecución de script en el
     * Rest Web Service de CloudForm
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>    
     * @version 1.0
     * @since 26-07-2018
     *     
     * @param  Array $arrayParametros [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return json {   
     *                  op     : 'accion',     
     *                  data   : {arrayData},
     *                  audit  : {arrayDataAuditoria}
     *              }
     */
    private function generateJson($arrayParametros)
    {        
        $strToken = '';
        
        //Generación Token de seguridad        
        $arrayJsonToken = array(
            'user'    => $arrayParametros['usrCreacion'],
            'gateway' => static::$strWsGatewayCloudForm,
            'service' => static::$strWsServiceCloudform,
            'method'  => 'procesarAction',
            'source'  => array(
                'name'         => static::$strWsAppName,
                'originID'     => $arrayParametros['ipCreacion'],
                'tipoOriginID' => 'IP'
            )
        );
        
        $arrayToken = $this->generateToken($arrayJsonToken);
        
        if(!empty($arrayToken))
        {
            if($arrayToken['status'] != static::$intStatusTokenError)
            {
                $strToken = $arrayToken['token'];
            }
            else
            {               
                return 'ERROR : Error en generación de Token, notificar a Sistemas';
            }
        }
        
        $arrayDataAuditoria = array
                                (
                                    'usrCreacion' => $arrayParametros['usrCreacion'],
                                    'ipCreacion'  => $arrayParametros['ipCreacion'],
                                    'token'       => $strToken
                                );    
        
        $arrayData = array();
        
        /* Variable que contiene la URL de acuerdo al metodo a invocar en el WS
         * 
         * - Consultas en aplicativos de cloudform
         *   - registrarusuario
         * 
         */
        $strUrl = $arrayParametros['accion'];
        
        if($strUrl == 'registrousuario')
        {
            $arrayData = array(
                            'login'          => $arrayParametros['login'],
                            'nombre'         => $arrayParametros['nombres'],
                            'apellido'       => $arrayParametros['apellidos'],
                            'correo'         => $arrayParametros['correo'],
                            'razonSocial'    => $arrayParametros['razonSocial'],
                            'telefono'       => $arrayParametros['telefono'],
                            'direccion'      => $arrayParametros['direccion']
                          );
        }                
        
        $objJsonArray = array
                        (
                            "op"    => $strUrl,                                                    
                            "data"  => $arrayData,
                            "audit" => $arrayDataAuditoria
                        );
        
        return json_encode($objJsonArray);
    }
    
    /**
     * generateToken
     * 
     * Método encargado de obtener la información del token de seguridad de acuerdo a la APP.CLOUDFORM registrada
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 26-07-2018
     * 
     * @param type $arrayJson
     * @return Array
     */
    private function generateToken($arrayJson)
    {
        $strDataString = json_encode($arrayJson);
        $arrayOptions  = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponse = $this->restClient->postJSON($this->webServiceTokenGeneratorURL, $strDataString, $arrayOptions);
        
        if ($arrayResponse['status'] == static::$intStatusOk)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $arrayResult = json_decode($arrayResponse['result'], true);
            return $arrayResult;
        }
        else
        {           
            return array(
                'token' => null,'status' => static::$intStatusTokenError
            );
        }
    }
    
    /**
     * executeScript
     * 
     * Método que se encarga de realizar la conexión contra el WS de Cloudform
     *     
     * @author Allan Suarez <arsuarez@telconet.ec>     
     * @version 1.0
     * @since 26-07-2018
     * 
     * @param  Array $arrayParametros [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return Array $arrayResultado [ status , mensaje ]
     */
    private function executeScript($arrayParametros)
    {
        //Se genera el json a enviar al ws por tipo de proceso a ejecutar
        $strDataString    = $this->generateJson($arrayParametros);   
        
        if(strpos($strDataString, 'ERROR')!== false)
        {
            $arrayResultado                = array();
            $arrayResultado['status']      = "ERROR";
            $arrayResultado['mensaje']     = $strDataString;                    
            $arrayResultado['statusCode']  = static::$intStatusTokenError; 
            return $arrayResultado;
        }
               
        $strUrl = $this->webServiceCloudFormRestURL;              

        if($this->ambienteEjecuta == "S")
        {        
            //Se obtiene el resultado de la ejecucion via rest hacia el ws  
            $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
            
            $arrayResponseJson = $this->restClient->postJSON($strUrl, $strDataString , $arrayOptions);
            
            if($arrayResponseJson['status'] == static::$intStatusOk)
            {
                $arrayResponse = json_decode($arrayResponseJson['result'],true);
                
                $arrayResultado               = array();
                $arrayResultado['status']     = $arrayResponse['status'];
                $arrayResultado['mensaje']    = $arrayResponse['status']=='ERROR'?'ERROR : '.$arrayResponse['message']:$arrayResponse['message'];
                $arrayResultado['statusCode'] = static::$intStatusOk;
            }
            else
            {
                $arrayResultado['status']      = "ERROR";
                if($arrayResponseJson['status'] == "0")
                {
                    $arrayResultado['mensaje']     = "ERROR : No Existe Conectividad con el WS de CloudForm.";
                }
                else
                {
                    $strMensajeError = 'ERROR';
                    
                    if(isset($arrayResponseJson['mensaje']) && !empty($arrayResponseJson['mensaje']))
                    {
                        $strMensajeError = 'ERROR : '.$arrayResponseJson['mensaje'];
                    }
                    
                    $arrayResultado['mensaje']     = "ERROR : Error de CloudForm :".$strMensajeError;
                }
                
                $arrayResultado['statusCode']  = 500;
            }
                        
            return $arrayResultado;
        }
        else
        {
            $arrayResultado['status']     = "OK";
            $arrayResultado['mensaje']    = $strDataString;
            $arrayResultado['statusCode'] = static::$intStatusOk;
        }
        
        return $arrayResultado;
    }
    
    /**
     * 
     * Método encargado de ejecutar la carga del consumo enviado desde el Cloudform
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 - 31-07-2018     
     * 
     * @param  Array $arrayData
     * @return Array $arrayResponse
     */
    public function guardarConsumoCloudForms($arrayData)
    {
        $arrayResponse = array();
        
        try
        {
            $strDataString = json_encode($arrayData);

            $strParametros = "jsonConsumo" . "|" . $strDataString ;

            $strRutaScript      = "/home/scripts-telcos/tn/tecnico/sources/ec.telconet.consumoCloudforms/dist/ec.telconet.consumoCloudforms.jar";
            $strEsperaRespuesta = "NO";

            //Se llama a Script que comunica via SSH       
            $strComando = "nohup ".$this->strPathJava." -jar -Djava.security.egd=file:/dev/./urandom " . $this->strPathTelcos .
                       "telcos/app/Resources/scripts/TelcosComunicacionScripts.jar '" . $strRutaScript . "' ".
                       " '" . $strParametros . "' '" . $strEsperaRespuesta . "' '" . $this->strHostScripts . "' '" . $this->strPathJava . "' ".
                       " >> ".$this->strPathTelcos ."telcos/app/Resources/scripts/log/log.txt &";  
                    
            shell_exec($strComando);
                       
            $arrayResponse['status']  = 'OK';
            $arrayResponse['mensaje'] = 'Se envio a procesar el consumo';
            
        }
        catch(\Exception $ex)
        {
             $this->utilService->insertError('Telcos+', 
                                            'CloudFormService -> guardarConsumoCloudForms', 
                                            $ex->getMessage(), 
                                            'cloudforms', 
                                            '127.0.0.1'
                                           );
        }
        
        return $arrayResponse;
    }
}
