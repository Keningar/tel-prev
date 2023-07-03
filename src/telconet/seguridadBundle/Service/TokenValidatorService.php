<?php

namespace telconet\seguridadBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Clase para validar tokens de seguridad mediante REST
 * Requiere configuracion en app/config/parameters.yml, ejemplo:
 * # Tokens de Seguridad
 *     # rutas de generacion/validacion locales de prueba, se debe indicar rutas validas
 *     seguridad.token_generate_url: https://dev-telcos-developer.telconet.ec/rs/seguridad/notoken/nogenerate
 *     seguridad.token_validate_url: https://dev-telcos-developer.telconet.ec/rs/seguridad/notoken/novalidate
 *     seguridad.token_validate_generate_url: https://dev-telcos-developer.telconet.ec/rs/seguridad/notoken/novalidategenerate
 *     # emails para notificar errores, se muestra valores por defecto
 *     seguridad.token_mail_error:
 *       - notificaciones_telcos@telconet.ec
 *       - telcos@telconet.ec
 *     # true por defecto, usar false solo si se desea probar validacion/generacion de tokens contra un servidor sin certificado SSL valido
 *     seguridad.token_ssl_verify: true
 * @author ltama
 */
class TokenValidatorService
{
    
    /**
     * Codigo de respuesta: Token valido
     */
    public static $TOKEN_OK = 200;
    
    /**
     * Codigo de respuesta: Token no valido
     */
    public static $TOKEN_INVALID = 403;
    
    /**
     * Codigo de respuesta: Error en token
     */
    public static $TOKEN_ERROR = 500;
    
    /**
     *
     * @var string
     */
    private $tokenGenerateURL;
    
    /**
     *
     * @var string
     */
    private $tokenValidateURL;

    /**
     *
     * @var string
     */
    private $tokenFinalizarURL;

    /**
     *
     * @var string
     */
    private $tokenValidateGenerateURL;
    
    /**
     *
     * @var string
     */
    private $tokenMailError;
    
    /**
     *
     * @var boolean
     */
    private $tokenSslVerify;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\MailerService
     */
    private $mailer;

    function setDependencies(ContainerInterface $container)
    {
        $this->tokenGenerateURL = $container->getParameter('seguridad.token_generate_url');
        $this->tokenValidateURL = $container->getParameter('seguridad.token_validate_url');
        $this->tokenOnlyURL     = $container->getParameter('seguridad.token_only_url');
        $this->tokenFinalizarURL= $container->getParameter('seguridad.token_expired');
        $this->tokenValidateGenerateURL = $container->getParameter('seguridad.token_validate_generate_url');
        $this->tokenMailError = ($container->hasParameter('seguridad.token_mail_error') ?
            $container->getParameter('seguridad.token_mail_error') : array('notificaciones_telcos@telconet.ec', 'telcos@telconet.ec'));
        $this->tokenSslVerify = ($container->hasParameter('seguridad.token_ssl_verify') ? $container->getParameter('seguridad.token_ssl_verify') : true);
        $this->restClient = $container->get('schema.RestClient');
        $this->mailer = $container->get('schema.Mailer');
    }

    /**
     * Funcion para envio de correos de error de comunicacion con servidor de tokens
     * @param string $url
     * @param string $data_string
     * @param array $options
     * @param array $response
     * @param string $msg
     */
    private function sendMailError($url, $data_string, $options, $response, $msg)
    {
        // error de comunicacion con servidor de tokens
        $subject = 'Inconvenientes en Token Server';
        $from = 'notificaciones-telcos@telconet.ec';
        $to = $this->tokenMailError;
        $twig = 'seguridadBundle:token:mailerErrorTokenServer.html.twig';
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
        catch (\Exception $e)
        {
        }
    }

    /**
     * Genera un token contra el servidor de tokens configurado
     * @param string $source
     * @param string $gateway
     * @param string $service
     * @param string $method
     * @param string $user
     * @return array
     */
    public function generateToken($source, $gateway, $service, $method, $user)
    {
        $data_string = json_encode(array('source' => $source, 'gateway' => $gateway, 'service' => $service, 'method' => $method, 'user' => $user));
        $options = array(CURLOPT_SSL_VERIFYPEER => $this->tokenSslVerify);
        $response = $this->restClient->postJSON($this->tokenGenerateURL, $data_string, $options);
        if ($response['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $result = json_decode($response['result'], true);
            return $result;
        }
        else
        {
            // error de comunicacion con servidor de tokens
            $this->sendMailError($this->tokenGenerateURL, $data_string, $options, $response,
                'Ha fallado la comunicacion con el servidor de generacion de tokens de seguridad');
            return array(
                'token' => null,'status' => static::$TOKEN_ERROR
            );
        }
    }

    /**
     * Valida un token dado contra el servidor de tokens configurado
     * @param string $token
     * @return array
     */
    public function validateToken($token)
    {
        $data_string = json_encode(array('token' => $token));
        $options = array(CURLOPT_SSL_VERIFYPEER => $this->tokenSslVerify);
        $response = $this->restClient->postJSON($this->tokenValidateURL, $data_string, $options);
        if ($response['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $result = json_decode($response['result'], true);
            return $result;
        }
        else
        {
            // error de comunicacion con servidor de tokens
            $this->sendMailError($this->tokenValidateURL, $data_string, $options, $response,
                'Ha fallado la comunicacion con el servidor de validacion de tokens de seguridad');
            return array(
            	'status' => static::$TOKEN_ERROR
            );
        }
    }

    /**
     * Valida un token dado contra el servidor de tokens configurado y
     * genera un token contra el servidor de tokens configurado
     * @param string $token
     * @param string $source
     * @param string $gateway
     * @param string $service
     * @param string $method
     * @param string $user
     * @return array
     */
    public function validateGenerateToken($token, $source, $gateway, $service, $method, $user)
    {
        $data_string = json_encode(array('token' => $token,
            'source' => $source, 'gateway' => $gateway, 'service' => $service, 'method' => $method, 'user' => $user));
        $options = array(CURLOPT_SSL_VERIFYPEER => $this->tokenSslVerify);
        $response = $this->restClient->postJSON($this->tokenValidateGenerateURL, $data_string, $options);
        if ($response['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $result = json_decode($response['result'], true);
            return $result;
        }
        else
        {
            // error de comunicacion con servidor de tokens
            $this->sendMailError($this->tokenValidateURL, $data_string, $options, $response,
                    'Ha fallado la comunicacion con el servidor de validacion y generacion de tokens de seguridad');
            return array(
                'token' => null,'status' => static::$TOKEN_ERROR
            );
        }
    }

    /**
     * Método que llama al gateway de token para validar si el mismo aún no caduca.
     *
     * @author Walther Joao Gaibor C. <mailto: wgaibor@gmail.com>
     * @version 1.0
     * @since 03-09-2018
     *
     * @param array $arrayParametroToken
     * @return array
     */
    public function validateOnlyToken($arrayParametroToken)
    {
        $strData       = json_encode($arrayParametroToken);
        $objOption     = array(CURLOPT_SSL_VERIFYPEER => $this->tokenSslVerify);
        $objResponse   = $this->restClient->postJSON($this->tokenOnlyURL, $strData, $objOption);
        if ($objResponse['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $objResult = json_decode($objResponse['result'], true);
            return $objResult;
        }
        else
        {
            // error de comunicacion con servidor de tokens
            $this->sendMailError($this->tokenGenerateURL, $strData, $objOption, $objResponse,
                'Ha fallado la comunicación con el servidor de generación de tokens de seguridad');
            return array(
                'token' => null,'status' => static::$TOKEN_ERROR
            );
        }
    }

    /**
     * Método que llama al gateway de token para finalizar un token.
     *
     * @author Walther Joao Gaibor C. <mailto: wgaibor@gmail.com>
     * @version 1.0
     * @since 03-09-2018
     *
     * @param array $arrayParametroToken
     * @return array
     */
    public function finalizarToken($arrayParametroToken)
    {
        $strData       = json_encode($arrayParametroToken);
        $objOption     = array(CURLOPT_SSL_VERIFYPEER => $this->tokenSslVerify);
        $objResponse   = $this->restClient->postJSON($this->tokenFinalizarURL, $strData, $objOption);
        if ($objResponse['status'] == 200)
        {
            // HTTP Status 200 OK - comunicacion correcta con servidor de tokens
            $objResult = json_decode($objResponse['result'], true);
            return $objResult;
        }
        else
        {
            // error de comunicacion con servidor de tokens
            $this->sendMailError($this->tokenGenerateURL, $strData, $objOption, $objResponse,
                'Ha fallado la comunicación con el servidor de generación de tokens de seguridad');
            return array(
                'token' => null,'status' => static::$TOKEN_ERROR
            );
        }
    }
}
