<?php

namespace telconet\comunicacionesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Clase para enviar SMS mediante REST
 * Requiere configuracion en app/config/parameters.yml, ejemplo:
 * # ruta del WS para envio de SMS
 * seguridad.sms_url: http://test-frontend.telconet.ec/ws/sms/rest/enviosms/procesar
 * 
 * # emails para notificar errores, se muestra valores por defecto
 * seguridad.token_mail_error:
 *  - notificaciones_telcos@telconet.ec
 *  - telcos@telconet.ec
 * 
 * # true por defecto, usar false solo si se desea probar contra un servidor sin certificado SSL valido
 * seguridad.sms_ssl_verify: false
 * @author jlafuente
 * @version 1.0 2016-01-07
 */
class SMSService
{

    /**
     * Codigo de respuesta: Requerimiento satistactorio
     */
    public static $SMS_OK = 200;

    /**
     * Codigo de respuesta: SMS entregado
     */
    public static $SMS_OK_DELIVERY = 202;

    /**
     * Codigo de respuesta: ERROR desconocido
     */
    public static $SMS_ERROR_UNKNOWN = 400;

    /**
     * Codigo de respuesta: Error por Recurso NO encontrado
     */
    public static $SMS_ERROR_NOT_FOUND = 404;

    /**
     * Codigo de respuesta: Error por Acceso denegado
     */
    public static $SMS_ERROR_ACCESS_DENIED = 401;

    /**
     * Codigo de respuesta: Error por Fallo Temporal
     */
    public static $SMS_ERROR_TEMPORAL_FAILURE = 503;

    /**
     * Codigo de respuesta: SMS no valido
     */
    public static $SMS_INVALID = 403;

    /**
     * Codigo de respuesta: Error en SMS
     */
    public static $SMS_ERROR = 500;
    
    /**
     * Origen de requerimiento
     * 
     * @var string
     */ 
    private $strSource;
    
    /**
     * Clave del source
     * 
     * @var string
     */ 
     private $strPassword;

    /**
     * URL del servicio SMS
     * @var string
     */
    private $smsURL;

    /**
     * Path de ruta para envio SMS
     * @var string
     */
    private $strRutaEnvio;

    /**
     * Path de ruta para visualizacion de estadisticas 
     * @var string
     */
    private $strRutaEstadisticas;

    /**
     * Path de ruta para ver reportes de envio
     * @var string
     */
    private $strRutaReporte;

    /**
     * Mail al que se enviarn mensajes de error en el servicio SMS
     * @var string
     */
    private $smsMailError;

    /**
     * Verificacion de SSL para el servicio SMS
     * @var boolean
     */
    private $smsSslVerify;

    /**
     * URL del servicio SMS
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;

    /**
     * Objeto para envio de correos
     * @var \telconet\schemaBundle\Service\MailerService
     */
    private $mailer;

    private $apiSmsTokenURL;
    
    private $apiSmsSenURL;
    
    private $strApiSmsUsername;
    
    private $strApiSmsPassword;
    
    private $strApiSmsSourceName;
    
    private $strUrlNotificacionSms;
    
    private $serviceUtil;    

    function setDependencies(ContainerInterface $container)
    {
        $this->strSource            = $container->getParameter('comunicacion.sms_source_origen');
        $this->strPassword          = $container->getParameter('comunicacion.sms_source_password');
        $this->smsURL               = $container->getParameter('comunicacion.sms_url');
        $this->strRutaEnvio         = $container->getParameter('comunicacion.sms_url_ruta_envio');
        $this->strRutaEstadisticas  = $container->getParameter('comunicacion.sms_url_ruta_estadisticas');
        $this->strRutaReporte       = $container->getParameter('comunicacion.sms_url_ruta_reporte');
        $this->smsMailError         = ($container->hasParameter('comunicacion.sms_mail_error') ?
                                      $container->getParameter('comunicacion.sms_mail_error') : array('notificaciones_telcos@telconet.ec', 'telcos@telconet.ec'));
        $this->smsSslVerify         = ($container->hasParameter('comunicacion.sms_ssl_verify') ?
                                      $container->getParameter('comunicacion.sms_ssl_verify') : true);
        $this->restClient           = $container->get('schema.RestClient');
        $this->mailer               = $container->get('schema.Mailer');        
        $this->apiSmsTokenURL       = $container->getParameter('comunicacion.api_sms_url_token');    
        $this->apiSmsSenURL         = $container->getParameter('comunicacion.api_sms_url_envio');
        $this->strApiSmsUsername    = $container->getParameter('comunicacion.api_sms_username');
        $this->strApiSmsPassword    = $container->getParameter('comunicacion.api_sms_password');
        $this->strApiSmsSourceName  = $container->getParameter('comunicacion.api_sms_source_name');
        $this->strUrlNotificacionSms = $container->getParameter('url_notificacion_envio_sms');
        $this->serviceUtil           = $container->get('schema.Util');           
    }

    /**
     * Funcion permite el envio de mail en caso de error
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 04-01-2016
     *
     * @param string $url
     * @param string $data_string
     * @param array $options
     * @param array $response
     * @param string $msg
     */
    private function sendMailError($url, $data_string, $options, $response, $msg)
    {
        // error de comunicacion con servidor de sms
        $subject = 'Inconvenientes con el servidor SMS';
        $from = 'notificaciones_telcos@telconet.ec';
        $to = $this->smsMailError;
        $twig = 'seguridadBundle:sms:mailerErrorSmsServer.html.twig';
        $parameters = array(
                            'url' => $url,
                            'data_string' => $data_string,
                            'options' => json_encode($options),
                            'mensaje' => $msg,
                            'error' => $response['error'],
                            'status' => $response['status'],
                            'result' => $response['result'],
                            );
        try
        {
            $this->mailer->sendTwig($subject, $from, $to, $twig, $parameters);
        }
        catch(\Exception $e)
        {
            
        }
    }

    /**
     * Funcion permite el envio de SMS al telefono del cliente mediante una llamada al WS de envio de SMS
     * 
     * @author Juan Carlos Lafuente <jlafuente@telconet.ec>
     * @version 1.0 04-01-2016
     *
     * @author Modificado: Duval Medina <dmedina@telconet.ec>
     * @version 2.0 2016-04-27 16:30 Cambio de parámentros pra soportar nueva implentación de WS.
     *                               Envio de consumo de URL a una función adicional
     *
     * @param string $strMensaje            -> Mensaje de texto
     * @param array $arrayNumerosAndOper    -> Numero(s) al que se desea enviar el SMS con la operadora,
     *                                         array(array('value' => '<telefono>', 'smsbox' => <operadora>))
     *                                         la operadora es: 0 = Claro y 1 = Movistar
     * @param integer $intPriority          -> Prioridad del envio
     * @param integer $intValidity          -> Validez en minutos
     */
    
    public function sendSMS($strMensaje, $arrayNumerosAndOper, $intPriority, $intValidity)
    {
        $arrayParametros = json_encode(array(
                                        'source'   => $this->strSource,
                                        'password' => $this->strPassword,
                                        'smsList'  => [array(
                                                            'text' => $strMensaje,
                                                            'to'   => $arrayNumerosAndOper,
                                                            'priority' => $intPriority,
                                                            'validity' => $intValidity)]
                                        
                                    ));
        
        return $this->generarPeticion($this->smsURL.$this->strRutaEnvio, $arrayParametros);
    }

    /**
     * Actualización: Se agregan en caso de error, se envie a info_error el detalle del error
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 13-09-2018
     * 
     * Actualización: Si al enviar SMS da error, el arreglo que retorna se lo modifica de la siguiente forma:
     * array(
     *       detail => detalle de error,
     *       code   => codigo de error
     *      )
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.1 07-03-2017
     * 
     * 
     * Funcion que realiza cominicación con el API REST para envio de SMS
     * 
     * @author Duval Medina <dmedina@telconet.ec>
     * @version 1.0 2016-04-27 15:27
     *
     * @param string $strUrl            -> URL del API Rest a consumir
     * @param array $arrayParametros    -> Parámetros necesarios para funcionalidad
     * 
     * @return array $result
     */
    public function generarPeticion($strUrl, $arrayParametros)
    {
        $options    = array(CURLOPT_SSL_VERIFYPEER => $this->smsSslVerify);
        $response   = $this->restClient->postJSON($strUrl, $arrayParametros, $options);

        if($response['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con el ws de SMS
            $result = json_decode($response['result'], true);
            return $result;
        }
        else
        {
            $strMensaje = "Ha fallado la comunicacion con el ws de SMS";
            $this->serviceUtil->insertError( 'Telcos+', 
                                 'SMSService.generarPeticion', 
                                 $strMensaje, 
                                 'telcos', 
                                 '127.0.0.1' );
            
            // error de comunicacion con el ws de SMS
            $this->sendMailError($strUrl, $arrayParametros, $options, $response, $strMensaje);            
            return array(
                        'detail' => $strMensaje, 'code' => static::$SMS_ERROR
                    );
        }
    }
    
    /**
     * Funcion permite el generar el tokeb de seguridad para poder enviar SMS
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 13-12-2017
     * 
     * Se pone el metodo dentro del bloque try .. catch para grabar en info_error
     *      
     */
    public function tokenAPISMS()
    {
        try
        {
            $arrayParametros = json_encode(array(
                                            'username' => $this->strApiSmsUsername,
                                            'password' => $this->strApiSmsPassword,
                                            'source'   => array('name' => $this->strApiSmsSourceName)));

            $arrayResponse = $this->generarApiSmsPeticion($this->apiSmsTokenURL, $arrayParametros);
            $strToken = $arrayResponse['token'];
            
            
        } catch (Exception $ex) {
            $this->serviceUtil->insertError( 'Telcos+', 
                                 'SMSService.generarPeticion', 
                                 $ex->getMessage(), 
                                 'telcos', 
                                 '127.0.0.1' );
        }
        
        return $strToken;
    }
    
    /**
     * Funcion permite el envio de SMS al telefono del cliente mediante una llamada al WS de envio de APISMS
     * esta apisms necesita un token el cual se se genera en la funcion tokenAPISMS
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 08-07-2018
     * 
     * Regularizacion .- Se cambia a parameters.yml la url de notificacion de envio de sms
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 11-07-2018
     *
     * @author Edgar Holguin <eholguin@telconet.ec>
     * @version 1.2 25-07-2018  Se agrega parametro para identificar el proceso que realiza el envío.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 13-09-2018 Se agrega en bloque try.. catch para grabar en info_error si se registra algun error
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.4 30-05-2019 Se modifica para que se conforme el json para el envío por infobip
     * 
     * @param string  $strMensaje      -> Mensaje de texto
     * @param array   $strNumeroTlf    -> Numero al que se desea enviar el SMS 
     * @param integer $strUser         -> Usuario que va a enviar el SMS
     * @param integer $strCodEmpresa   -> Codigo de la empresa que envia el SMS
     */
    public function sendAPISMS($arrayParametros)
    { 
        $strProceso      = 'CONTRATODIGITAL';
        $strMensaje      = $arrayParametros['mensaje'];
        $strNumeroTlf    = $arrayParametros['numero'];
        $strUser         = $arrayParametros['user'];
        $strCodEmpresa   = $arrayParametros['codEmpresa'];
        
        $strToken        = $this->tokenAPISMS();   
        if (!$strToken)
        {
            $this->serviceUtil->insertError( 'Telcos+', 
                                 'SMSService.generarPeticion', 
                                 "Error en generación de token", 
                                 'telcos', 
                                 '127.0.0.1' );
            
            $arrayResponse['salida']  = 500;
            $arrayResponse['mensaje'] = "Error en generación de Token";
            
        }
        $strNumeroTlfAux = substr($strNumeroTlf, 0, 1);
        if($strNumeroTlfAux === "0")
        {
            $strNumeroTlf = '593'.substr($strNumeroTlf, 1, 9);
        }
        if(isset($arrayParametros['strProceso']))
        {
            $strProceso  = $arrayParametros['strProceso'];
        }
        $objFechaEnvio = new \DateTime('now'); 
        $arrayParametrosEnvio = json_encode(array('token' => $strToken,
                                                  'user'  => $this->strApiSmsUsername,
                                                  'accion'=> 'enviarIndividual',
                                                  'source'=> array('name'         => $this->strApiSmsSourceName,
                                                                   'originID'     => '127.0.0.1',
                                                                   'tipoOriginID' => 'IP'),
                                                  'data'  => array('proceso'         => $strProceso,
                                                                   'noCia'           => $strCodEmpresa,
                                                                   'usuarioCreacion' => $strUser,
                                                                   'noSalida'        => '',
                                                                   'mensajeSalida'   => '',
                                                                   'bulkId'          => $objFechaEnvio->format('Ymd-').$strToken,
                                                                   'jsonMensaje'     => "",
                                                                   'messages'=> [array('from'               => 'InfoSMS',
                                                                                       'destinations'       => [array ('to'=> $strNumeroTlf,
                                                                                       'messageId'          => $objFechaEnvio
                                                                                                                 ->format('YmdHis').rand(1,100))],
                                                                                       'text'               => $strMensaje,
                                                                                       'sendAt'             => $objFechaEnvio->format('Y-m-d'),
                                                                                       'flash'              => true,
                                                                                       'intermediateReport' => true,
                                                                                       'notifyUrl'          => $this->strUrlNotificacionSms,
                                                                                       'notifyContentType'  => 'application/json',
                                                                                       'callbackData'       => 'DLR callback data',
                                                                                       'validityPeriod'     => 720)])));
        try
        {
            $arrayResponse = $this->generarApiSmsPeticion($this->apiSmsSenURL, $arrayParametrosEnvio);        
        } catch (Exception $ex) {
            $this->serviceUtil->insertError( 'Telcos+', 
                                 'SMSService.SendApiSms', 
                                 $ex->getMessage(), 
                                 'telcos', 
                                 '127.0.0.1' );            
            $arrayResponse['salida']  = 500;
            $arrayResponse['mensaje'] = $ex->getMessage();
        }
        
        return $arrayResponse;
    }

    /**
     * Funcion permite el generar el envio para el consumo de WS API SMS
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 09-07-2018
     *      
     */
    
    public function generarApiSmsPeticion($strUrl, $arrayParametros)
    {
        $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponse   = $this->restClient->postJSON($strUrl, $arrayParametros, $arrayOptions);
        if($arrayResponse['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con el ws de SMS
            $arrayResult = json_decode($arrayResponse['result'], true);
            return $arrayResult;
        }
        else
        {
            // error de comunicacion con el ws de SMS
            
            $this->sendMailError($strUrl, $arrayParametros, $arrayOptions, $arrayResponse, 'Ha fallado la comunicacion con el ws de SMS');
            return array(
                        'detail' => 'Ha fallado la comunicacion con el ws de SMS', 'code' => static::$SMS_ERROR
                    );
        }
    }

   /**
     * Funcion permite el envio de SMS al telefono del cliente mediante una llamada al WS de envio de APISMS
     * esta apisms necesita un token el cual se se genera en la funcion tokenAPISMS
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 29-05-2019
     * 
     * @param string  $strMensaje      -> Mensaje de texto
     * @param array   $strNumeroTlf    -> Numero al que se desea enviar el SMS 
     * @param integer $strUser         -> Usuario que va a enviar el SMS
     * @param integer $strCodEmpresa   -> Codigo de la empresa que envia el SMS
     */
    public function sendAPISMSMassend($arrayParametros)
    { 
        $strProceso      = 'CONTRATODIGITAL';
        $strMensaje      = $arrayParametros['mensaje'];
        $strNumeroTlf    = $arrayParametros['numero'];
        $strUser         = $arrayParametros['user'];
        $strCodEmpresa   = $arrayParametros['codEmpresa'];
        
        $strToken        = $this->tokenAPISMS();   
        if (!$strToken)
        {
            $this->serviceUtil->insertError( 'Telcos+', 
                                 'SMSService.generarPeticion', 
                                 "Error en generación de token", 
                                 'telcos', 
                                 '127.0.0.1' );
            
            $arrayResponse['salida']  = 500;
            $arrayResponse['mensaje'] = "Error en generación de Token";
            
        }
        if(isset($arrayParametros['strProceso']))
        {
            $strProceso  = $arrayParametros['strProceso'];
        }
        $arrayParametrosEnvio = json_encode(array('token' => $strToken,
                                                  'user'  => $this->strApiSmsUsername,
                                                  'accion'=> 'enviarMassend',
                                                  'source'=> array('name'        => $this->strApiSmsSourceName,
                                                                   'originID'    => '127.0.0.1',
                                                                   'tipoOriginID'=> 'IP'),
                                                  'data'  => array('strCamp'    => $strProceso,
                                                                   'strCia'     => $strCodEmpresa,
                                                                   'strUsuario' => $strUser,
                                                                   'strMsg'     => $strMensaje,
                                                                   'strNum'     => $strNumeroTlf,
                                                                   'strRuta'    => 'S',
                                                                  )
                                                  )
                                            );
        try
        {
            $arrayResponse = $this->generarApiSmsPeticion($this->apiSmsSenURL, $arrayParametrosEnvio);        
        } 
        catch (Exception $ex) 
        {
            $this->serviceUtil->insertError( 'Telcos+', 
                                 'SMSService.SendApiSmsMassend', 
                                 $ex->getMessage(), 
                                 'telcos', 
                                 '127.0.0.1' );
            $arrayResponse['salida']  = 500;
            $arrayResponse['mensaje'] = $ex->getMessage();
        }
        
        return $arrayResponse;
    }    
}

