<?php

namespace telconet\administracionBundle\Service;

class PersonalExternoService
{
    private $restClient;
    private $strUrlMiddleware;
    private $strUrlMiddlewareToken;
    private $strTokenUsername;
    private $strTokenPassword;
    private $strTokenSource;
    private $emComercial;
    private $serviceUtil;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->restClient             = $container->get('schema.RestClient');
        $this->strUrlMiddleware       = $container->getParameter('ws_ldap_personal_externo_url');
        $this->strUrlMiddlewareToken  = $container->getParameter('seguridad.token_authentication_url');
        $this->strTokenUsername       = $container->getParameter('seguridad.token_username_telcos');
        $this->strTokenPassword       = $container->getParameter('seguridad.token_password_telcos');
        $this->strTokenSource         = $container->getParameter('seguridad.token_source_telcos');
        $this->emComercial            = $container->get('doctrine')->getManager('telconet');
        $this->serviceUtil            = $container->get('schema.Util');
    }
    
    /**
     * Funcion que sirve para ejecutar la llamadas a ws
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.0 03-01-2018
     * @return arrayResultado 
     */
    public function middlewareLdapEmpleadoExterno($arrayDatosMiddleware)
    {
        $strUrl              = $arrayDatosMiddleware['url'];
        $jsonDatosMiddleware = json_encode($arrayDatosMiddleware);
        $objOptions = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResultado = $this->restClient->postJSON($strUrl, $jsonDatosMiddleware , $objOptions);
        $arrayResponse = array();
        if(isset($arrayResultado['result']))
        {
        $arrayResponse = json_decode($arrayResultado['result'], true);
        }
        return $arrayResponse;
    }
    /**
     * Funcion que sirve para crear login para personal externo
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.0 08-03-2018
     * @return arrayResultado 
     */
     public function generaLoginPersonaExterno($arrayData)
    {
        $strLogin      = "";
        $strUsrCreacion = $arrayData['strUsrCreacion'];
        
        if (!empty($arrayData['strPrimerNombre']))
        {
           $strPrimerNombre    = substr($arrayData['strPrimerNombre'], 0, 1);
        }
        if (!empty($arrayData['strSegundoNombre']))
        {
           $strSegundoNombre   = substr($arrayData['strSegundoNombre'], 0, 1);
        }
        $strPrimerApellido  = $arrayData['strPrimerApellido'];
        $strSegundoApellido = substr($arrayData['strSegundoApellido'], 0, 1);
        $strIdentificacion  = $arrayData['strIdentificacion'];
        $arrayLogin         = [strtolower($strPrimerNombre.$strPrimerApellido),
                               strtolower($strPrimerNombre.$strSegundoNombre.$strPrimerApellido),
                               strtolower($strPrimerNombre.$strSegundoNombre.$strPrimerApellido.$strSegundoApellido)];
        $intContador        = 0;
        $intContadorLogin   = 0;
        
        do
        {
            $boolExisteLogin = false;
            $strLogin = $arrayLogin[2];
            if($intContador <= 3)
            {
                $strLogin = $arrayLogin[$intContador];
            }
            else
            {
                $intContadorLogin++;
                $strLogin= $strLogin.$intContadorLogin;
            }
            
            $arrayRequestLoginGenerado = ['strLogin'          => $strLogin, 
                                          'strUsrCreacion'    => $strUsrCreacion,
                                          'strIdentificacion' => $strIdentificacion];
            $arrayLoginGenerado = $this->validaLoginLdapPersona($arrayRequestLoginGenerado);
            if($arrayLoginGenerado['status'] != '200' )
            {
                if($arrayLoginGenerado['status'] != '500' )
                {
                    return ['login'  => $strLogin,
                            'status' => $arrayLoginGenerado['status'],
                            'msj'    => $arrayLoginGenerado['msj'] . 'ssssssssssssssss'];
                }
                $boolExisteLogin= true;
            }
            $intContador++;
        }
        while($boolExisteLogin);
        return ['login'  => $strLogin,
                'status' => $arrayLoginGenerado['status'],
                'msj'    => $arrayLoginGenerado['msj']];
    }
    
     /**
     * Funcion que sirve para validar el login generado en ldap.
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.0 08-03-2018
     * @return arrayResultado 
     */
    public  function sendRequestLdapLogin($arrayRequest)
    {
        $arrayParametrosToken['username']       = $this->strTokenUsername;
        $arrayParametrosToken['password']       = $this->strTokenPassword;
        $arrayParametrosToken['source']['name'] = $this->strTokenSource;
        $arrayParametrosToken['url']            = $this->strUrlMiddlewareToken;
        $arrayToken = $this->middlewareLdapEmpleadoExterno($arrayParametrosToken);
        if(isset($arrayToken['status']) && $arrayToken['status'] != '200')
        {
            return ['login'  => '',
                    'status' => $arrayToken['status'],
                    'msj'    => 'Existio un error al consultar token para generar el login.'];
        }
        $arrayParametrosLogin['token']                   = $arrayToken['token'];
        $arrayParametrosLogin['strLogin']                = $arrayRequest['strLogin'];
        $arrayParametrosLogin['op']                      = 'validaLogin';
        $arrayParametrosLogin['app']                     = 'PERSONA_EXTERNO';
        $arrayParametrosLogin['url']                     = $this->strUrlMiddleware;
        $arrayParametrosLogin['source']['name']          = 'TELCOS';
        $arrayParametrosLogin['source']['originID']      = 'nuevoEmpleadoExterno';
        $arrayParametrosLogin['source']['tipoOriginID']  = 'nuevoEmpleadoExterno';
        $arrayParametrosLogin['user']                    = $arrayRequest['strUsrCreacion'];        
        
        $arrayLogin = $this->middlewareLdapEmpleadoExterno($arrayParametrosLogin);
        
        if(isset($arrayLogin['status']) && $arrayLogin['status'] != '200')
        {
            return ['login'  => '',
                    'status' => $arrayLogin['status'],
                    'msj'    => $arrayLogin['mensaje']];
        }
        return ['login'  => $arrayLogin['login'],
                'status' => $arrayLogin['status'],
                'msj'    => $arrayLogin['mensaje']];
    }
    
     /**
     * Funcion que sirve para validar el login generado por persona
     * 
     * @author Sofia Fernandez <sfernandez@telconet.ec>
     * @version 1.0 09-03-2018
     * @return arrayResultado 
     */
    public function validaLoginLdapPersona($arrayRequest)
    {
        $strLogin       = $arrayRequest['strLogin'];
        $strUsrCreacion = $arrayRequest['strUsrCreacion'];
        $strIdentificacion = $arrayRequest['strIdentificacion'];
        $strStatus     = "500";
        $strMsj        = "No existen coincidencias.";
        $strEx         = "No se pudo generar el login";
        
        $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                        ->getMiniPersonaPorLogin($strLogin);
        
        if (!$objInfoPersona)
        {
            $arrayRequestLogin = ['strLogin' => $strLogin, 'strUsrCreacion' => $strUsrCreacion];
            $arrayLoginLdap = $this->sendRequestLdapLogin($arrayRequestLogin);
            return ['login' => $arrayLoginLdap['login'],
                   'status' => $arrayLoginLdap['status'],
                   'msj'    => $arrayLoginLdap['msj'],
                   'msjEx'  => ''];
        }
        else
        {
            $strLoginPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                         ->validarInfoPersonaByIdentificacionYLogin($strLogin,$strIdentificacion);
            if($strLoginPersona=='Existe')
            {
                $arrayRequestLogin = ['strLogin' => $strLogin, 'strUsrCreacion' => $strUsrCreacion];
                $arrayLoginLdap = $this->sendRequestLdapLogin($arrayRequestLogin);
                return ['login'  => $arrayLoginLdap['login'],
                        'status' => $arrayLoginLdap['status'],
                        'msj'    => $arrayLoginLdap['msj'],
                        'msjEx'  => ''];
             }            
        }
        return ['login'  => "",
                'status' => $strStatus,
                'msj'    => $strMsj,
                'msjEx'  => $strEx];         
    }

    /**
     * Documentación para la función 'eliminarPersonalExterno'.
     *
     * Función encargada de eliminar personal externo en el ldap.
     *
     * @param array $arrayParametros [
     *                                  "strUsrCreacion"  => Usuario en sesión.
     *                                  "strIpCreacion"   => Ip del usuario en sesión,.
     *                                  "strLogin"        => Login del personal externo.
     *                               ]
     *
     * @return array $arrayResultado [
     *                                 status =>  Estado de la respuesta.
     *                                 msj    =>  mensaje de la respuesta.
     *                               ]
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.0 25-11-2021
     *
     */
    public  function eliminarPersonalExterno($arrayParametros)
    {
        $strUsrCreacion      = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                                ? $arrayParametros['strUsrCreacion'] : 'TELCOS +';
        $strIpCreacion       = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                                ? $arrayParametros['strIpCreacion'] : '127.0.0.1';
        $arrayResultado      = array();
        try
        {
            $arrayParametrosToken['username']       = $this->strTokenUsername;
            $arrayParametrosToken['password']       = $this->strTokenPassword;
            $arrayParametrosToken['source']['name'] = $this->strTokenSource;
            $arrayParametrosToken['url']            = $this->strUrlMiddlewareToken;
            $arrayToken = $this->middlewareLdapEmpleadoExterno($arrayParametrosToken);
            if(empty($arrayToken) || !is_array($arrayToken))
            {
                throw new \Exception("Falló la comunicación entre TelcoS+ y Token MiddlewareLdap.");
            }
            if(isset($arrayToken['status']) && $arrayToken['status'] != '200')
            {
                throw new \Exception("Error para generar Token MiddlewareLdap");
            }
            $arrayParametrosLdap['token']                  = $arrayToken['token'];
            $arrayParametrosLdap['data']                   = array('strLogin' => $arrayParametros['strLogin']);
            $arrayParametrosLdap['op']                     = 'eliminarPersonalExterno';
            $arrayParametrosLdap['app']                    = 'PERSONA_EXTERNO';
            $arrayParametrosLdap['url']                    = $this->strUrlMiddleware;
            $arrayParametrosLdap['source']['name']         = 'TELCOS';
            $arrayParametrosLdap['source']['originID']     = 'nuevoEmpleadoExterno';
            $arrayParametrosLdap['source']['tipoOriginID'] = 'nuevoEmpleadoExterno';
            $arrayParametrosLdap['user']                   = $arrayParametros['strUsrCreacion'];
            $arrayResultado                                = $this->middlewareLdapEmpleadoExterno($arrayParametrosLdap);
            if(empty($arrayResultado) || !is_array($arrayResultado))
            {
                throw new \Exception("Falló la comunicación entre TelcoS+ y MiddlewareLdap.");
            }
            if(isset($arrayResultado["status"]) && ($arrayResultado["status"] != "200" && $arrayResultado["status"] != "204"))
            {
                throw new \Exception($arrayResultado['msj']);
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje               = $ex->getMessage();
            $arrayResultado["status"] = 500;
            $arrayResultado["msj"]    = $ex->getMessage();
            $this->serviceUtil->insertError('TelcoS+',
                                            'PersonalExternoService.eliminarPersonalExterno',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $arrayResultado;
    }
}
