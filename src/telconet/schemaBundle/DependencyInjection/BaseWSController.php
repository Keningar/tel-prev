<?php

namespace telconet\schemaBundle\DependencyInjection;

use telconet\seguridadBundle\Service\TokenValidatorService;
use telconet\schemaBundle\Service\UtilService;

/**
 * Clase base para todos los controllers de web service,
 * cuyo nombre debe terminar con el sufijo "WSController",
 * deben ubicarse en la carpeta WebService del Bundle,
 * y deben configurarse en el archivo app/config/config.yml.
 * @author ltama
 */
class BaseWSController extends BaseController {
    
    /**
     * Variable $status que sirve para controlar los tipos de error.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 3-06-2015
     * 
     * nuevo status CLAVE_EXPIRADA
     * @author  Robinson Salgado
     * @version 1.1 17-05-2018
     */
    protected $status = array ( 'OK'               => 200, 
                                'ERROR'            => 500, 
                                'TOKEN'            => 403, 
                                'NULL'             => 204, 
                                'METODO'           => 404, 
                                'CONSULTA'         => 400, 
                                'ERROR_PARCIAL'    => 206,
                                'CLAVE_EXPIRADA'   => 300,
                                'DATOS_NO_VALIDOS' => 505);
    
    /**
     * Variable $mensaje que sirve para controlar los mensajes de los diferentes tipos de error.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 3-06-2015
     * 
     * nuevo mensaje CLAVE_EXPIRADA
     * @author  Robinson Salgado
     * @version 1.1 17-05-2018
     * 
     * nuevo mensaje DISPOSITIVO_NO_ENCONTRADO
     * @author  Robinson Salgado
     * @version 1.1 17-05-2018
     * 
     */
    protected $mensaje = array ('OK'                        => 'CONSULTA EXITOSA', 
                                'ERROR'                     => 'INTERNAL ERROR', 
                                'TOKEN'                     => 'TOKEN INVALIDO', 
                                'NULL'                      => 'SIN CONTENIDO', 
                                'METODO'                    => 'METODO NO EXISTE', 
                                'CONSULTA'                  => 'ERROR EN CONSULTA',
                                'CLAVE_EXPIRADA'            => 'Su clave ha caducado, 
                                                                por favor cambiarla en Telcos Web. - https://telcos.telconet.ec',
                                'DATOS_NO_VALIDOS'          => 'Sus datos no fueron validados correctamente',
                                'DISPOSITIVO_NO_ENCONTRADO' => 'Este dispositivo no se encuentra registrado en el 
                                                                sistema o se encuentra inhabilitado.',
                                'DISPOSITIVO_INCONSISTENCIA'=> 'Este dispositivo presenta inconsistencias con la información registrada.
                                                                Comuníquese con sistemas para su revisión.');
     
    /**
     * Metodo de ayuda:
     * Define el parametro dado como respuesta SOAP del metodo del web service actual
     */
    protected function soapReturn($returnValue)
    {
        return $this->get('besimple.soap.response')->setReturnValue($returnValue);
    }

    /**
     * Genera un token contra el servidor de tokens configurado
     * @param string $source
     * @param string $user
     * @return boolean|string false si el status de la generacion del token no es valido, string del token si se genero exitosamente
     */
    public function generateToken($source, $user)
    {
        // mediante back trace determinar que clase y funcion esta llamando a este metodo
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $gateway = 'Telcos';
        $service = substr($trace[1]['class'], strrpos($trace[1]['class'], '\\') + 1);
        $method = $trace[1]['function'];
        $result = $this->get('seguridad.TokenValidator')->generateToken($source, $gateway, $service, $method, $user);
        if ($result['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            return false;
        }
        return $result['token'];
    }

    /**
     * Genera un token contra el servidor de tokens configurado
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0
     * @since 07-09-2018
     *
     * @param array $arrayParametroToken[
     *                                      source[
     *                                              name          string      Nombre de la app,
     *                                              originID      string      id generado por la app
     *                                              tipoOriginID  string      Código de la app
     *                                            ],
     *                                      gateway      string      Cliente que hace el consumo del token security,
     *                                      service      string      Clase desde donde se usa el consumo del token security,
     *                                      method       string      Nombre del método que llama al momento de realizar el consumo del token security,
     *                                      user         string      Usuario sessión
     *                                  ]
     * @return boolean|string false si el status de la generación del token no es válido, string del token si se género exitosamente
     */
    public function generateTokenAppCliente($arrayParametroToken)
    {
        // mediante back trace determinar que clase y función esta llamando a este método
        $objTrace                       = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $strMethod                      = $objTrace[1]['function'];
        $arrayParametroToken['gateway'] = 'Telcos';
        $arrayParametroToken['service'] = 'AppCliente';
        $arrayParametroToken['method']  = $strMethod;
        $arrayResult                    = $this->get('seguridad.TokenValidator')
                                               ->generateToken($arrayParametroToken['source'],
                                                               $arrayParametroToken['gateway'],
                                                               $arrayParametroToken['service'],
                                                               $arrayParametroToken['method'],
                                                               $arrayParametroToken['user']);
        if ($arrayResult['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            return false;
        }
        return $arrayResult['token'];
    }

    /**
     * Valida un token dado contra el servidor de tokens de seguridad configurado
     * @param string $token
     * @return boolean false si el status de la validacion del token no es valido, true si es valido
     */
    protected function validateToken($token)
    {
        $result = $this->get('seguridad.TokenValidator')->validateToken($token);
        if ($result['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            return false;
        }
        return true;
    }

    /**
     * Valida un token dado contra el servidor de tokens de seguridad configurado y
     * genera un nuevo token de seguridad
     * @param string $token
     * @param string $source
     * @param string $user
     * @return boolean|string false si el status de la generacion del token no es valido, string del token si se genero exitosamente
     * 
     * Actualización .- Se graba en info_error cuando el token devuelve false
     * @author: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version: 1.2 24-09-2018
     */
    protected function validateGenerateToken($token, $source, $user)
    {
        // mediante back trace determinar que clase y funcion esta llamando a este metodo
        $serviceUtil              = $this->get('schema.Util');
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $gateway = 'Telcos';
        $service = substr($trace[1]['class'], strrpos($trace[1]['class'], '\\') + 1);
        $method = $trace[1]['function'];
        $result = $this->get('seguridad.TokenValidator')->validateGenerateToken($token, $source, $gateway, $service, $method, $user);
        if ($result['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            $serviceUtil->insertError(
                                      'Telcos+', 
                                      'BaseWSController->validateGenerateToken', 
                                      'Ha fallado la comunicacion con el servidor de validacion de tokens de seguridad Error: ' . $result['status'] ,
                                      $user,
                                      '127.0.0.1'
                                     );
            
            return false;
        }
        return $result['token'];
    }

    /**
     * Valida un token dado contra el servidor de tokens de seguridad configurado y
     * genera un nuevo token de seguridad
     * 
     * @param array $arrayParametroToken[
     *                                    token        string      Hash enviado a validar.
     *                                    source[
     *                                              name          string      Nombre de la app,
     *                                              originID      string      id generado por la app
     *                                              tipoOriginID  string      Código de la app
     *                                            ],
     *                                      user         string      Usuario sessión,
     *                                  ]
     * 
     * @author: Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version: 1.0 06-03-2020
     * 
     * Se modifica el formato de archivo a guardar en la InfoLog. 
     * @author: Wilmer Vera <wvera@telconet.ec>
     * @version: 1.1 01-04-2020
     *
     * Se modifica el campo "application" para guardar en la InfoLog. 
     * @author: Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version: 1.2 05-06-2020
     * 
     * @author: Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version: 1.3 04-01-2021
     * 
     * Se módifíca lógica para guardar de mejor manera los mensajes de error.
     * 
     * @return array [token, status]
     */
    protected function validateGenerateTokenMobile($arrayParametroToken)
    {
        // mediante back trace determinar que clase y funcion esta llamando a este metodo
        $serviceUtil    = $this->get('schema.Util');
        $arrayTrace     = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $strGateway     = 'Telcos';
        $strService     = substr($arrayTrace[1]['class'], strrpos($arrayTrace[1]['class'], '\\') + 1);
        $strMethod      = $arrayTrace[1]['function'];
        $arrayResult    = $this->get('seguridad.TokenValidator')->validateGenerateToken(
                                                                                            $arrayParametroToken['token'], 
                                                                                            $arrayParametroToken['source'], 
                                                                                            $strGateway, 
                                                                                            $strService, 
                                                                                            $strMethod, 
                                                                                            $arrayParametroToken['user']
                                                                                        );
        if ($arrayResult['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            $serviceUtil->insertLog(array('enterpriseCode'   => '10',
                              'logType'          => 1,
                              'logOrigin'        => 'TELCOS',
                              'application'      => 'TELCOS',
                              'appClass'         => 'BaseWSController',
                              'appMethod'        => 'validateGenerateTokenMobile',
                              'descriptionError' => 'Ha fallado la comunicacion con el servidor de validacion' . 
                              'de tokens de seguridad Error: ' . $arrayResult['status'],
                              'status'           => 'Fallido',
                              'inParameters'     => json_encode($arrayParametroToken, 128),
                              'creationUser'     => $arrayParametroToken['user']));
        }

        return array(
                        'token'     => $arrayResult['token'],
                        'status'    => $arrayResult['status'],
                        'mensaje'   => $arrayResult['message']
                    );
    }

    /**
     * Método que valida un token y de ser necesario generaría uno nuevo siempre y cuando el que se encuentre
     * validando este caducado
     *
     * @author Walther Joao Gaibor C. <mailto: wgaibor@gmail.com>
     * @version 1.0
     * @since 03-09-2018
     *
     * @param array $arrayParametroToken[
     *                                      source[
     *                                              name          string      Nombre de la app,
     *                                              originID      string      id generado por la app
     *                                              tipoOriginID  string      Código de la app
     *                                            ],
     *                                      gateway      string      Cliente que hace el consumo del token security,
     *                                      service      string      Clase desde donde se usa el consumo del token security,
     *                                      method       string      Nombre del método que llama al momento de realizar el consumo del token security,
     *                                      user         string      Usuario sessión,
     *                                      token        string      Hash creado por el token security.
     *                                  ]
     * @return boolean
     */
    public function isValidateOnlyToken($arrayParametroToken)
    {
        // mediante back trace determinar que clase y función esta llamando a este metodo
        $objTrace                       = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $strMethod                      = $objTrace[1]['function'];
        $arrayParametroToken['gateway'] = 'Telcos';
        $arrayParametroToken['service'] = 'AppCliente';
        $arrayParametroToken['method']  = $strMethod;
        $arrayRespuesta                 = $this->get('seguridad.TokenValidator')
                                               ->validateOnlyToken($arrayParametroToken);
        if ($arrayRespuesta['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            return false;
        }
        return !empty($arrayRespuesta['token']) ? $arrayRespuesta['token'] : $arrayParametroToken['token'];
    }

    /**
     * Método que finaliza un token.
     *
     * @author Walther Joao Gaibor C. <mailto: wgaibor@gmail.com>
     * @version 1.0
     * @since 22-11-2018
     *
     * @param array $arrayParametroToken[
     *                                      source[
     *                                              name          string      Nombre de la app,
     *                                              originID      string      id generado por la app
     *                                              tipoOriginID  string      Código de la app
     *                                            ],
     *                                      gateway      string      Cliente que hace el consumo del token security,
     *                                      service      string      Clase desde donde se usa el consumo del token security,
     *                                      method       string      Nombre del método que llama al momento de realizar el consumo del token security,
     *                                      user         string      Usuario sessión,
     *                                      token        string      Hash creado por el token security.
     *                                  ]
     * @return boolean
     */
    public function isfinalizarToken($arrayParametroToken)
    {
        // mediante back trace determinar que clase y función esta llamando a este metodo
        $objTrace                       = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        $strMethod                      = $objTrace[1]['function'];
        $arrayParametroToken['gateway'] = 'Telcos';
        $arrayParametroToken['service'] = 'AppCliente';
        $arrayParametroToken['method']  = $strMethod;
        $arrayRespuesta                 = $this->get('seguridad.TokenValidator')
                                               ->finalizarToken($arrayParametroToken);
        if ($arrayRespuesta['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            return false;
        }
        return !empty($arrayRespuesta['token']) ? $arrayRespuesta['token'] : $arrayParametroToken['token'];
    }
}
