<?php

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;
use telconet\seguridadBundle\Service\TokenValidatorService;
/**
 * Clase para llamar a metodos ws provisionados por BOC para la ejecucion de script sobre el App de TelcoGraph 
 * 
 * @author Jesús Bozada <jbozada@telconet.ec>
 */
class TelcoGraphService
{
    /**
     * Código de respuesta: Respuesta valida
     */
    private static $strStatusOK = 200;
    
    /**
     * Nombre de la app a consumir
     */
    private static $strNombreAppWsToken = "APP.LDAP";
    
    /**
     * Nombre de la app que consume el Web Service
     */
    private static $strGatewayWsToken = "Telcos";
    
    /**
     * Nombre del archivo que contiene el web service para las consultas de Netlifecam  
     */
    private static $strServiceWsToken = "GestionLdapWSController";
    
    /**
     * Nombre de la función principal del archivo del Web Service usado para Netlifecam 
     */
    private static $strMethodWsToken  = "procesarAction";
    
    /**
     * Nombre del usuario para realizar consultas al Web Service
     */
    private static $strUserWsToken   = "LDAP";
    
    /**
     * Nombre de la aplicación registrada en la base que guarda la configuración de LDAP
     */
    private static $strNombreAppLdap = "LDAP_CLIENTES";
    
    /**
     * mensaje general mostrado en el portal cuando ocurre un error
     */
    private static $strMsjGeneralPortal = "Ha ocurrido un error. Por favor notificar al Administrador";
    
    /**
     *
     * @var string
     */
    private $strUrlWebServicePortalLdap;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    private $urlTelcoGraph;

    private $serviceUtil;

    private $emInfraestructura;

    private $emComercial;

    private $emSoporte;
    
    private $emComunicacion;

    private $emGeneral;

    /**
     * service $serviceTokenValidator
     */
    private $serviceTokenValidator;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                  = $container;
        $this->emInfraestructura          = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComercial                = $container->get('doctrine')->getManager('telconet');
        $this->emSoporte                  = $container->get('doctrine')->getManager('telconet_soporte');
        $this->emComunicacion             = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral                  = $container->get('doctrine')->getManager('telconet_general');
        $this->urlTelcoGraph              = $container->getParameter('ws_telcoGraph_url');
        $this->serviceTokenValidator      = $container->get('seguridad.TokenValidator');
        $this->serviceUtil                = $container->get('schema.Util');
        $this->restClient                 = $container->get('schema.RestClient');
        $this->strUrlWebServicePortalLdap = $container->getParameter('tecnico.ws_ldap_url');
    }

    /**
     * telcoGraphWs
     * 
     * Funcion que sirve para ejecutar la llamada al ws de TelcoGraph
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 19-03-2018
     * 
     * @return arrayResultado 
     */
    public function telcoGraphWs($strJsonDatosTelcoGraph)
    {
        $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponseJson = $this->restClient->postJSON($this->urlTelcoGraph, $strJsonDatosTelcoGraph , $arrayOptions);
        
        if($arrayResponseJson['status'] == static::$strStatusOK && $arrayResponseJson['result'] !== false)
        {        
            $arrayResponse = json_decode($arrayResponseJson['result'],true);
            $arrayResultado = $arrayResponse;
        }
        else
        {
            $arrayResultado['status'] = "ERROR";
            if($arrayResponseJson['status'] == "0")
            {
                $arrayResultado['mensaje']  = "No Existe Conectividad con el WS TELCOGRAPH.";
            }
            else
            {
                $strMensajeError = 'ERROR';
                if(isset($arrayResponseJson['mensaje']) && !empty($arrayResponseJson['mensaje']))
                {
                    $strMensajeError = $arrayResponseJson['mensaje'];
                }
                $arrayResultado['mensaje']  = "Error de TELCOGRAPH :".$strMensajeError;
            }
        }

        return $arrayResultado ;
    }

    /**
     * validarUsuarioLdap
     * 
     * Función que sirve para realizar la validación de un cliente en LDAP
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 11-06-2020 - Se modifica el mensaje cuando el Ldap no responde
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 11-02-2021 - Se realiza un control de errores cuando ocurre un excepción en el proceso.
     *
     * @param Array $arrayParametros [
     *                                strCorreo        : Correo del usuario a validar.
     *                                strloginServicio : Login y id del servicio concatenado.
     *                                strUsrCreacion   : Usuario quien realiza la acción.
     *                                strIpClient      : Ip del usuario quien realizal a acción.
     *                               ]
     *
     * @return Array $arrayRespuesta [
     *                                status   : OK o ERROR
     *                                msj      : Mensaje de informativo o de error.
     *                                strMsjEx : Excepción generada.
     *                               ]
     */
    public function validarUsuarioLdap($arrayParametros)
    {
        $arrayParametrosWs =  array();
        $strCorreo         =  $arrayParametros["strCorreo"];
        $strLoginServicio  =  $arrayParametros['strloginServicio'];
        $strUsuario        =  $arrayParametros['strUsrCreacion'];
        $strIpUsuario      =  $arrayParametros['strIpClient'];
        $strOpcion         = 'validarUsuario';

        try
        {
            $arrayRespuestaToken = $this->generateTokenRequestWs(array('strIpClient' => $strIpUsuario));
            if ($arrayRespuestaToken["strStatus"] !== "OK")
            {
                throw new \Exception("No se pudo generar el token de manera correcta.");
            }

            $strToken    = $arrayRespuestaToken['strToken'];
            $arrayData   = array("uid" => $strCorreo);
            $arraySource = array("name"         =>  self::$strGatewayWsToken,
                                 "originID"     =>  $strIpUsuario,
                                 "tipoOriginID" => "IP");

            $arrayParametrosWs = $this->getRequestWs(array("strOpcion"  => $strOpcion,
                                                           "strToken"   => $strToken,
                                                           "strUser"    => $strUsuario,
                                                           "strSource"  => $arraySource,
                                                           "arrayData"  => $arrayData));

            $arrayRespuesta = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            if ($arrayRespuesta["strStatus"] === "ERROR")
            {
                throw new \Exception('Mensaje: '  .$arrayRespuesta["strMsj"].', '.
                                     'Excepcion: '.$arrayRespuesta["strMsjEx"]);
            }
        }
        catch (\Exception $objException)
        {
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'validarUsuarioLdap',
                                             $strCodigo.'-1-'.$objException->getMessage().$strLoginServicio,
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'validarUsuarioLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'validarUsuarioLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $strUsuario,
                                             $strIpUsuario);

            if (empty($arrayRespuesta))
            {
                $arrayRespuesta = array('strStatus' => 'ERROR',
                                        'strMsj'    =>  self::$strMsjGeneralPortal,
                                        'strMsjEx'  =>  $objException->getMessage());
            }
        }
        return $arrayRespuesta;
    }

    /**
     * crearClienteLdap
     * 
     * Documentación para el método 'crearClienteLdap'.
     *
     * Función que crea un cliente nuevo en el árbol LDAP de clientes TelcoGraph llamado desde Telcos
     *
     * @param Array $arrayParametros [
     *                                strNombre        : Nombres del cliente.
     *                                strApellido      : Apellidos del cliente.
     *                                strMail          : Correo seleccionado como usuario del portal.
     *                                strPass          : Contraseña aleatoria generada.
     *                                strloginServicio : Login y id del servicio concatenado.
     *                                strUsrCreacion   : Usuario quien realiza la acción.
     *                                strIpClient      : ip del usuario quien realizal a acción.
     *                               ]
     *
     * @return Array $arrayRespuesta [
     *                                status   : OK o ERROR
     *                                msj      : Mensaje de informativo o de error.
     *                                strMsjEx : Excepción generada.
     *                               ]
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 11-02-2021 - Se realiza un control de errores cuando ocurre un excepción en el proceso.
     */
    public function crearClienteLdap($arrayParametros)
    {
        $arrayParametrosWs =  array();
        $strLoginServicio  =  $arrayParametros['strloginServicio'];
        $strUsuario        =  $arrayParametros['strUsrCreacion'];
        $strIpUsuario      =  $arrayParametros['strIpClient'];
        $strOpcion         = 'nuevo';

        try
        {
            $arrayRespuestaToken = $this->generateTokenRequestWs(array('strIpClient' => $strIpUsuario));
            if ($arrayRespuestaToken["strStatus"] !== "OK")
            {
                throw new \Exception("No se pudo generar el token de manera correcta.");
            }

            $strToken    = $arrayRespuestaToken['strToken'];
            $arrayData   = array("uid"      => $arrayParametros["strMail"],
                                 "nombre"   => $arrayParametros["strNombre"],
                                 "apellido" => $arrayParametros["strApellido"],
                                 "mail"     => $arrayParametros["strMail"],
                                 "pass"     => $arrayParametros["strPass"]);
            $arraySource = array("name"         =>  self::$strGatewayWsToken,
                                 "originID"     =>  $strIpUsuario,
                                 "tipoOriginID" => "IP");

            $arrayParametrosWs = $this->getRequestWs(array("strOpcion"  => $strOpcion,
                                                           "strToken"   => $strToken,
                                                           "strUser"    => $strUsuario,
                                                           "strSource"  => $arraySource,
                                                           "arrayData"  => $arrayData));

            $arrayRespuesta = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            if ($arrayRespuesta["strStatus"] === "ERROR")
            {
                throw new \Exception('Mensaje: '  .$arrayRespuesta["strMsj"].', '.
                                     'Excepcion: '.$arrayRespuesta["strMsjEx"]);
            }
        }
        catch (\Exception $objException)
        {
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearClienteLdap',
                                             $strCodigo.'-1-'.$objException->getMessage().$strLoginServicio,
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearClienteLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearClienteLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $strUsuario,
                                             $strIpUsuario);

            if (empty($arrayRespuesta))
            {
                $arrayRespuesta = array('strStatus' => 'ERROR',
                                        'strMsj'    =>  self::$strMsjGeneralPortal,
                                        'strMsjEx'  =>  $objException->getMessage());
            }
        }
        return $arrayRespuesta;
    }

    /**
     * cambioPassUsuarioLdap
     * 
     * Función que sirve para realizar la actualización de password de un cliente en LDAP
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 29-08-2018
     * 
     * @param array $arrayParametrosLdap
     *                          [
     *                              "strCorreo"      => correo del usuario a modificar
     *                              "strPass"        => pass del usuario a modificar
     *                              "strIpClient"    => ip creacion a registrar
     *                              "strUsrCreacion" => usuario creacion a utilizar en el proceso
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function cambioPassUsuarioLdap($arrayParametrosLdap)
    {
        $arrayRespuesta                   = array();
        $arrayRespuesta["strStatus"]      = "ERROR";
        $arrayRespuesta["strMsj"]         = "";
        $arrayRespuesta["arrayResultado"] = array();
        $arrayAtributos                   = array();
        try
        {
            $arrayAtributoPass =  array(
                                        "nombre" => "userPassword",
                                        "valor"  => $arrayParametrosLdap["strPass"]
                                       );
            $arrayAtributos[] = $arrayAtributoPass;
            $arrayDataWsAutenticarCliente = array(
                                                  "uid" => $arrayParametrosLdap["strCorreo"],
                                                  "atributos" => $arrayAtributos
                                                 );
            $arraySource = array(
                                    "name"          => self::$strGatewayWsToken,
                                    "originID"      => $arrayParametrosLdap["strIpClient"],
                                    "tipoOriginID"  => "IP"
                                );
            $arrayParametrosTokenWs                = array();
            $arrayParametrosTokenWs['strIpClient'] = $arrayParametrosLdap['strIpClient'];
            $arrayRespuestaToken                   = $this->generateTokenRequestWs($arrayParametrosTokenWs);
            if ($arrayRespuestaToken['strStatus'] != "OK")
            {
                throw new \Exception("Existieron problemas al generar Token para el proceso, favor notificar al administrador.");
            }
            $arrayParametrosWsValidarCliente = $this->getRequestWs(array(
                                                                            "arrayData"     => $arrayDataWsAutenticarCliente,
                                                                            "strOpcion"     => "actualizar",
                                                                            "strSource"     => $arraySource,
                                                                            "strToken"      => $arrayRespuestaToken['strToken'],
                                                                            "strUser"       => $arrayParametrosLdap["strUsrCreacion"]
                                                                        )
                                                                  );
            
            $arrayRespuestaWs = $this->callWsLdapPortalTelcoGraph($arrayParametrosWsValidarCliente);
            if($arrayRespuestaWs["strStatus"] == "OK")
            {
                $arrayRespuesta = $arrayRespuestaWs;
            }
            else if( isset($arrayRespuestaWs["strMsjEx"]) && !empty($arrayRespuestaWs["strMsjEx"]) )
            {
                throw new \Exception($arrayRespuestaWs["strMsjEx"]);
            }
            else if( isset($arrayRespuestaWs["strMsj"]) && !empty($arrayRespuestaWs["strMsj"]) )
            {
                $arrayRespuesta = $arrayRespuestaWs;
                $arrayRespuesta["arrayResultado"]    = array();
            }
            else
            {
                throw new \Exception("Ha ocurrido un error inesperado (No se tiene respuesta del LDAP)");
            }
        } 
        catch (\Exception $objEx)
        {
            $arrayRespuesta["strMsj"]  = self::$strMsjGeneralPortal;
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'cambioPassUsuarioLdap',
                                             $strCodigo.'-1-'.$objEx->getMessage(),
                                             $arrayParametrosLdap["strUsrCreacion"],
                                             $arrayParametrosLdap["strIpClient"]);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'cambioPassUsuarioLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametrosLdap),
                                             $arrayParametrosLdap["strUsrCreacion"],
                                             $arrayParametrosLdap["strIpClient"]);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'cambioPassUsuarioLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWsValidarCliente),
                                             $arrayParametrosLdap["strUsrCreacion"],
                                             $arrayParametrosLdap["strIpClient"]);
        }
        return $arrayRespuesta;
    }

    /**
     * eliminarUsuarioLdap
     * 
     * Función que sirve para realizar la eliminación de un cliente en LDAP
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 22-05-2018
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 11-06-2020 - Se modifica el mensaje cuando el Ldap no responde
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 11-02-2021 - Se realiza un control de errores cuando ocurre un excepción en el proceso.
     *
     * @param Array $arrayParametros [
     *                                strCorreo        : Correo del usuario a eliminar.
     *                                strloginServicio : Login y id del servicio concatenado.
     *                                strUsrCreacion"  : Usuario quien realiza la acción.
     *                                strIpClient      : Ip del usuario quien realizal a acción.
     *                               ]
     *
     * @return Array $arrayRespuesta [
     *                                status   : OK o ERROR
     *                                msj      : Mensaje de informativo o de error.
     *                                strMsjEx : Excepción generada.
     *                               ]
     * 
     */
    public function eliminarUsuarioLdap($arrayParametros)
    {
        $arrayParametrosWs =  array();
        $strCorreo         =  $arrayParametros["strCorreo"];
        $strLoginServicio  =  $arrayParametros['strloginServicio'];
        $strUsuario        =  $arrayParametros['strUsrCreacion'];
        $strIpUsuario      =  $arrayParametros['strIpClient'];
        $strOpcion         = 'eliminar';

        try
        {
            $arrayRespuestaToken = $this->generateTokenRequestWs(array('strIpClient' => $strIpUsuario));
            if ($arrayRespuestaToken["strStatus"] !== "OK")
            {
                throw new \Exception("No se pudo generar el token de manera correcta.");
            }

            $strToken    = $arrayRespuestaToken['strToken'];
            $arrayData   = array("uid" => $strCorreo);
            $arraySource = array("name"         =>  self::$strGatewayWsToken,
                                 "originID"     =>  $strIpUsuario,
                                 "tipoOriginID" => "IP");

            $arrayParametrosWs = $this->getRequestWs(array("strOpcion"  => $strOpcion,
                                                           "strToken"   => $strToken,
                                                           "strUser"    => $strUsuario,
                                                           "strSource"  => $arraySource,
                                                           "arrayData"  => $arrayData));

            $arrayRespuesta = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            if ($arrayRespuesta["strStatus"] === "ERROR")
            {
                throw new \Exception('Mensaje: '  .$arrayRespuesta["strMsj"].', '.
                                     'Excepcion: '.$arrayRespuesta["strMsjEx"]);
            }
        }
        catch (\Exception $objException)
        {
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'eliminarUsuarioLdap',
                                             $strCodigo.'-1-'.$objException->getMessage().$strLoginServicio,
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'eliminarUsuarioLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'eliminarUsuarioLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $strUsuario,
                                             $strIpUsuario);

            if (empty($arrayRespuesta))
            {
                $arrayRespuesta = array('strStatus' => 'ERROR',
                                        'strMsj'    =>  self::$strMsjGeneralPortal,
                                        'strMsjEx'  =>  $objException->getMessage());
            }
        }
        return $arrayRespuesta;
    }

    /**
     * crearGrupoLdap
     * 
     * Función que sirve para agregar un cliente a un nuevo grupo de LDAP que sera creado en ese servicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 24-05-2018
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.1 11-06-2020 - Se modifica el mensaje cuando el Ldap no responde
     * @since 1.0
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 11-02-2021 - Se realiza un control de errores cuando ocurre un excepción en el proceso.
     *
     * @param array $arrayParametros [
     *                                arrayUser        : Array con información de usuarios a crear.
     *                                arrayNivel       : Array con información de niveles a crear.
     *                                strloginServicio : Login y id del servicio concatenado.
     *                                strUsrCreacion   : Usuario quien realiza la acción.
     *                                strIpClient      : Ip del usuario quien realizal a acción.
     *                               ]
     *
     * @return Array $arrayRespuesta [
     *                                status   : OK o ERROR
     *                                msj      : Mensaje de informativo o de error.
     *                                strMsjEx : Excepción generada.
     *                               ]
     * 
     */
    public function crearGrupoLdap($arrayParametros)
    {
        $arrayParametrosWs =  array();
        $arrayUser         =  $arrayParametros["arrayUser"];
        $arrayNivel        =  $arrayParametros["arrayNivel"];
        $strLoginServicio  =  $arrayParametros['strloginServicio'];
        $strUsuario        =  $arrayParametros['strUsrCreacion'];
        $strIpUsuario      =  $arrayParametros['strIpClient'];
        $strOpcion         = 'crearGrupoTelcograph';

        try
        {
            $arrayRespuestaToken = $this->generateTokenRequestWs(array('strIpClient' => $strIpUsuario));
            if ($arrayRespuestaToken["strStatus"] !== "OK")
            {
                throw new \Exception("No se pudo generar el token de manera correcta.");
            }

            $strToken    = $arrayRespuestaToken['strToken'];
            $arraySource = array("name"         =>  self::$strGatewayWsToken,
                                 "originID"     =>  $strIpUsuario,
                                 "tipoOriginID" => "IP");
            $arrayData   = array("user" => $arrayUser, "nivel" => $arrayNivel, "proceso" => $strOpcion);

            $arrayParametrosWs = $this->getRequestWs(array("strOpcion" => $strOpcion,
                                                           "strToken"  => $strToken,
                                                           "strUser"   => $strUsuario,
                                                           "strSource" => $arraySource,
                                                           "arrayData" => $arrayData));

            $arrayRespuesta = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            if ($arrayRespuesta["strStatus"] === "ERROR")
            {
                throw new \Exception('Mensaje: '  .$arrayRespuesta["strMsj"].', '.
                                     'Excepcion: '.$arrayRespuesta["strMsjEx"]);
            }
        } 
        catch (\Exception $objException)
        {
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearGrupoLdap',
                                             $strCodigo.'-1-'.$objException->getMessage().$strLoginServicio,
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearGrupoLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearGrupoLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $strUsuario,
                                             $strIpUsuario);

            if (empty($arrayRespuesta))
            {
                $arrayRespuesta = array('strStatus' => 'ERROR',
                                        'strMsj'    =>  self::$strMsjGeneralPortal,
                                        'strMsjEx'  =>  $objException->getMessage());
            }
        }
        return $arrayRespuesta;
    }

    /**
     * eliminarGrupoLdap
     * 
     * Documentación para el método 'eliminarGrupoLdap'.
     *
     * Función que elimina un grupo del Ldap
     *
     * @param Array $arrayParametros [
     *                                strGrupoLdap     : Grupo a eliminar.
     *                                strloginServicio : Login y id del servicio concatenado.
     *                                strUsrCreacion"  : Usuario quien realiza la acción.
     *                                strIpClient      : Ip del usuario quien realizal a acción.
     *                               ]
     *
     * @return Array $arrayRespuesta [
     *                                status   : OK o ERROR
     *                                msj      : Mensaje de informativo o de error.
     *                                strMsjEx : Excepción generada.
     *                               ]
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 11-02-2021 - Se realiza un control de errores cuando ocurre un excepción en el proceso.
     */
    public function eliminarGrupoLdap($arrayParametros)
    {
        $arrayParametrosWs =  array();
        $strGrupoLdap      =  $arrayParametros["strGrupoLdap"];
        $strLoginServicio  =  $arrayParametros['strloginServicio'];
        $strUsuario        =  $arrayParametros['strUsrCreacion'];
        $strIpUsuario      =  $arrayParametros['strIpClient'];
        $strOpcion         = 'eliminarGrupoLdap';

        try
        {
            $arrayRespuestaToken = $this->generateTokenRequestWs(array('strIpClient' => $strIpUsuario));
            if ($arrayRespuestaToken["strStatus"] !== "OK")
            {
                throw new \Exception("No se pudo generar el token de manera correcta.");
            }

            $strToken    = $arrayRespuestaToken['strToken'];
            $arrayData   = array("grupoLdap"    => $strGrupoLdap);
            $arraySource = array("name"         =>  self::$strGatewayWsToken,
                                 "originID"     =>  $strIpUsuario,
                                 "tipoOriginID" => "IP");

            $arrayParametrosWs = $this->getRequestWs(array("strOpcion"  => $strOpcion,
                                                           "strToken"   => $strToken,
                                                           "strUser"    => $strUsuario,
                                                           "strSource"  => $arraySource,
                                                           "arrayData"  => $arrayData));

            $arrayRespuesta = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            if ($arrayRespuesta["strStatus"] === "ERROR")
            {
                throw new \Exception('Mensaje: '  .$arrayRespuesta["strMsj"].', '.
                                     'Excepcion: '.$arrayRespuesta["strMsjEx"]);
            }
        }
        catch (\Exception $objException)
        {
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'eliminarGrupoLdap',
                                             $strCodigo.'-1-'.$objException->getMessage().$strLoginServicio,
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'eliminarGrupoLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $strUsuario,
                                             $strIpUsuario);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'eliminarGrupoLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $strUsuario,
                                             $strIpUsuario);

            if (empty($arrayRespuesta))
            {
                $arrayRespuesta = array('strStatus' => 'ERROR',
                                        'strMsj'    =>  self::$strMsjGeneralPortal,
                                        'strMsjEx'  =>  $objException->getMessage());
            }
        }
        return $arrayRespuesta;
    }

    /**
     * agregarUsuarioAGrupoLdap
     * 
     * Documentación para el método 'agregarUsuarioAGrupoLdap'.
     *
     * Función que crea un usuario a un grupo del Ldap
     * 
     * 
     * @param type array $arrayParametros [
     *                                      "strUid"         => Identificador de usuario,
     *                                      "strGrupoLdap"   => Nombre del grupo al cual va a ser agregado
     *                                    ]
     * 
     * @return array $arrayRespuestaWsCrearClienteLdap
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     */
    public function agregarUsuarioAGrupoLdap($arrayParametros)
    {
        $arrayRespuestaWsAgregarUsuarioLdap = array();
        $arrayRespuestaToken                = $this->generateTokenRequestWs($arrayParametros);
        if($arrayRespuestaToken["strStatus"] == "OK")
        {
            $strTokenConsultaWs     = $arrayRespuestaToken["strToken"];
            
            $arrayData              = array(
                                            "uid"                   => $arrayParametros["strUid"],
                                            "grupoLdap"             => $arrayParametros["strGrupoLdap"],
                                           );

            $arrayParametrosWs      = $this->getRequestWs(array(
                                                                "arrayData"     => $arrayData,
                                                                "strIpClient"   => $arrayParametros["strIpClient"],
                                                                "strUser"       => $arrayParametros["strUser"],
                                                                "strOpcion"     => "agregarUsuarioAGrupo",
                                                                "strToken"      => $strTokenConsultaWs
                                                         ));
            $arrayRespuestaWsAgregarUsuarioLdap   = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
        }
        else
        {
            $arrayRespuestaWsAgregarUsuarioLdap['strStatus'] = "ERROR";
            $arrayRespuestaWsAgregarUsuarioLdap['strMsj']    = "No se pudo generar el token de manera correcta";
        }
        return $arrayRespuestaWsAgregarUsuarioLdap;
    }

    /**
     * removerUsuarioAGrupoLdap
     * 
     * Documentación para el método 'removerUsuarioAGrupoLdap'.
     *
     * Función que remover un usuario a un grupo del Ldap
     * 
     * 
     * @param type array $arrayParametros [
     *                                      "strUid"         => Identificador de usuario,
     *                                      "strGrupoLdap"   => Nombre del grupo al cual va a ser agregado
     *                                    ]
     * 
     * @return array $arrayRespuestaWsRemoverClienteLdap
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 29-06-2020
     */
    public function removerUsuarioAGrupoLdap($arrayParametros)
    {
        $arrayRespuestaWsRemoverClienteLdap = array();

        try
        {
            $arrayRespuestaToken                = $this->generateTokenRequestWs($arrayParametros);
            if($arrayRespuestaToken["strStatus"] == "OK")
            {
                $strTokenConsultaWs     = $arrayRespuestaToken["strToken"];
                
                $arrayData              = array(
                                                "uid"                   => $arrayParametros["strUid"],
                                                "grupoLdap"             => $arrayParametros["strGrupoLdap"],
                                            );

                $arrayParametrosWs      = $this->getRequestWs(array(
                                                                    "arrayData"     => $arrayData,
                                                                    "strIpClient"   => $arrayParametros["strIpClient"],
                                                                    "strUser"       => $arrayParametros["strUser"],
                                                                    "strOpcion"     => "removerUsuarioDeGrupo",
                                                                    "strToken"      => $strTokenConsultaWs
                                                            ));
                $arrayRespuestaWsRemoverClienteLdap   = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            }
            else
            {
                $arrayRespuestaWsRemoverClienteLdap['strStatus'] = "ERROR";
                $arrayRespuestaWsRemoverClienteLdap['strMsj']    = "No se pudo generar el token de manera correcta";
            }
        }
        catch(\Exception $objEx)
        {
            $arrayRespuestaWsRemoverClienteLdap['strStatus'] = "ERROR";
            $arrayRespuestaWsRemoverClienteLdap['strMsj']    = "No se pudo realizar el proceso de manera correcta";
            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'removerUsuarioAGrupoLdap',
                                             $strCodigo.'-1-'.$objEx->getMessage(),
                                             $arrayParametros["strUser"],
                                             $arrayParametros["strIpClient"]);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'removerUsuarioAGrupoLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $arrayParametros["strUser"],
                                             $arrayParametros["strIpClient"]);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'removerUsuarioAGrupoLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $arrayParametros["strUser"],
                                             $arrayParametros["strIpClient"]);
        }
        return $arrayRespuestaWsRemoverClienteLdap;
    }

    /**
     * validaUsuarioAGrupoLdap
     * 
     * Documentación para el método 'validaUsuarioAGrupoLdap'.
     *
     * Función que valida un usuario en un grupo del Ldap
     * 
     * 
     * @param type array $arrayParametros [
     *                                      "strUid"         => Identificador de usuario,
     *                                      "strGrupoLdap"   => Nombre del grupo al cual va a ser agregado
     *                                    ]
     * 
     * @return array $arrayRespuestaWsValidaUsuarioLdap
     * 
     * @author Néstor Naula <nnaulal@telconet.ec>
     * @version 1.0 30-06-2020
     */
    public function validaUsuarioAGrupoLdap($arrayParametros)
    {
        $arrayRespuestaWsValidaUsuarioLdap = array();

        try
        {
            $arrayRespuestaToken                = $this->generateTokenRequestWs($arrayParametros);
            if($arrayRespuestaToken["strStatus"] == "OK")
            {
                $strTokenConsultaWs     = $arrayRespuestaToken["strToken"];
                
                $arrayData              = array(
                                                "uid"                   => $arrayParametros["strUid"],
                                                "grupoLdap"             => $arrayParametros["strGrupoLdap"],
                                            );

                $arrayParametrosWs      = $this->getRequestWs(array(
                                                                    "arrayData"     => $arrayData,
                                                                    "strIpClient"   => $arrayParametros["strIpClient"],
                                                                    "strUser"       => $arrayParametros["strUser"],
                                                                    "strOpcion"     => "validarUsuarioEnGrupo",
                                                                    "strToken"      => $strTokenConsultaWs
                                                            ));
                $arrayRespuestaWsValidaUsuarioLdap   = $this->callWsLdapPortalTelcoGraph($arrayParametrosWs);
            }
            else
            {
                $arrayRespuestaWsValidaUsuarioLdap['strStatus'] = "ERROR";
                $arrayRespuestaWsValidaUsuarioLdap['strMsj']    = "No se pudo generar el token de manera correcta";
            }
        }
        catch(\Exception $objEx)
        {
            $arrayRespuestaWsValidaUsuarioLdap['strStatus'] = "ERROR";
            $arrayRespuestaWsValidaUsuarioLdap['strMsj']    = "No se pudo realizar el proceso de manera correcta";

            $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
            $this->serviceUtil->insertError('TelcoGraphService',
                                            'validaUsuarioAGrupoLdap',
                                             $strCodigo.'-1-'.$objEx->getMessage(),
                                             $arrayParametros["strUser"],
                                             $arrayParametros["strIpClient"]);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'validaUsuarioAGrupoLdap',
                                             $strCodigo.'-2-'.json_encode($arrayParametros),
                                             $arrayParametros["strUser"],
                                             $arrayParametros["strIpClient"]);

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'validaUsuarioAGrupoLdap',
                                             $strCodigo.'-3-'.json_encode($arrayParametrosWs),
                                             $arrayParametros["strUser"],
                                             $arrayParametros["strIpClient"]);
        }
        return $arrayRespuestaWsValidaUsuarioLdap;
    }
    
    /**
     * callWsLdapPortalTelcoGraph
     * 
     * Documentación para el método 'callWsLdapPortalTelcoGraph'.
     *
     * Función que realiza el llamado a cualquier función del web service para gestionar el Ldap
     * 
     * @param type array $arrayParametrosWs array con diferentes estructuras dependiendo de la opción que se invoca en la petición al web service
     * 
     * @return string $arrayResultado["status", "msj", "token"]
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 20-03-2018    Se cambia validacion SSL
     * @since 1.0
     */
    public function callWsLdapPortalTelcoGraph($arrayParametrosWs)
    {
        $arrayResultado     = array();
        $strStatus          = "";
        $strMensaje         = "";
        $strMsjEx           = "";
        $strTokenResultado  = "";
        //Se genera el json a enviar al ws por tipo de proceso a ejecutar
        $strDataWs = json_encode($arrayParametrosWs);

        //Se obtiene el resultado de la ejecucion via rest hacia el ws  
        $arrayOptionsRest = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayResponseJsonWS = $this->restClient->postJSON($this->strUrlWebServicePortalLdap, $strDataWs, $arrayOptionsRest);

        if($arrayResponseJsonWS['status'] == self::$strStatusOK && $arrayResponseJsonWS['result'])
        {
            $arrayResponseWs    = json_decode($arrayResponseJsonWS['result'], true);
            $strMensaje         = $arrayResponseWs['msj'];
            $strTokenResultado  = $arrayResponseWs['token'];
            $strMsjEx           = $arrayResponseWs['msjEx'];

            if($arrayResponseWs['status'] == self::$strStatusOK)
            {
                $strStatus = "OK";
            }
            else
            {
                $strStatus = "ERROR";
            }
        }
        else
        {
            $strStatus  = "ERROR";
            $strMensaje = $arrayResponseJsonWS['error'];
        }

        $arrayResultado['strStatus'] = $strStatus;
        $arrayResultado['strMsj']    = $strMensaje;
        $arrayResultado['strMsjEx']  = $strMsjEx;
        $arrayResultado['strToken']  = $strTokenResultado;
        
        return $arrayResultado;
    }
    
    /**
     * generateTokenRequestWs
     * 
     * Documentación para el método 'generateTokenRequestWs'.
     *
     * Función que genera el token que será enviado como parámetro al web service usado para las consultas al arbol LDAP de clientes
     * 
     * @param type array $arrayParametros [
     *                                      "strIpClient"       => IP que solicitará la petición al web service
     *                                    ]
     * 
     * @return string $arrayResult['token']
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     */
    public function generateTokenRequestWs($arrayParametros)
    {
        $arrayRespuesta    = array();
        $strToken          = "";
        $strStatus         = "";
        $strNombreApp      = (isset($arrayParametros["nombreApp"])?
                              (!empty($arrayParametros['nombreApp'])?$arrayParametros['nombreApp']:self::$strNombreAppWsToken):
                              self::$strNombreAppWsToken);
        $strGatewayWsTk    = (isset($arrayParametros["gatewayWs"])?
                              (!empty($arrayParametros['gatewayWs'])?$arrayParametros['gatewayWs']:self::$strGatewayWsToken):
                              self::$strGatewayWsToken);
        $strServiceWsTk    = (isset($arrayParametros["serviceWs"])?
                              (!empty($arrayParametros['serviceWs'])?$arrayParametros['serviceWs']:self::$strServiceWsToken):
                              self::$strServiceWsToken);
        $strMethodWsTk     = (isset($arrayParametros["methodWs"])?
                              (!empty($arrayParametros['methodWs'])?$arrayParametros['methodWs']:self::$strMethodWsToken):
                              self::$strMethodWsToken);
        $strUserWsTk       = (isset($arrayParametros["userWs"])?
                              (!empty($arrayParametros['userWs'])?$arrayParametros['userWs']:self::$strUserWsToken):
                              self::$strUserWsToken);
        $arraySource       = array("name"          => $strNombreApp,
                                   "originID"      => $arrayParametros["strIpClient"],
                                   "tipoOriginID"  => "IP");
        $arrayResult       = $this->serviceTokenValidator
                                  ->generateToken($arraySource, 
                                                  $strGatewayWsTk, 
                                                  $strServiceWsTk, 
                                                  $strMethodWsTk,
                                                  $strUserWsTk);
        if($arrayResult['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            $strStatus = "ERROR";
        }
        else
        {
            $strStatus  = "OK";
            $strToken   = $arrayResult['token'];
        }
        $arrayRespuesta["strStatus"]   = $strStatus;
        $arrayRespuesta["strToken"]    = $strToken;
        $arrayRespuesta["arraySource"] = $arraySource;
        $arrayRespuesta["strUser"]     = $strUserWsTk;
        return $arrayRespuesta;
    }
    
    /**
     * getRequestWs
     * 
     * Documentación para el método 'getRequestWs'.
     *
     * Función que genera el request que se enviará al web service que gestiona a los clientes en LDAP
     * 
     * @param type array $arrayParametros [
     *                                      "arrayData"     => array con la data propia de la consulta enviada al WS
     *                                      "strOpcion"     => metodo a consultarse
     *                                      "source"        => array con la información del source
     *                                      "strIpClient"   => IP
     *                                      "strToken"      => token
     *                                      "strUser"       => usuario
     *                                    ]
     * 
     * @return string $arrayResult['token']
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     */
    private function getRequestWs($arrayParametros)
    {
        $arrayRequestWs = array("app"    => self::$strNombreAppLdap,
                                "data"   => $arrayParametros["arrayData"],
                                "op"     => $arrayParametros["strOpcion"],
                                "source" => $arrayParametros["strSource"] ? $arrayParametros["strSource"] :
                                                array("name"         => self::$strGatewayWsToken,
                                                      "originID"     => $arrayParametros["strIpClient"],
                                                      "tipoOriginID" => "IP"),
                                "token"  => $arrayParametros["strToken"],
                                "user"   => $arrayParametros["strUser"] ? $arrayParametros["strUser"] : 'Telcograf');
        return $arrayRequestWs;
    }

    /**
     * generarClaveAleatoria
     * 
     * Documentación para el método 'generarClaveAleatoria'.
     *
     * Función que genera una clave aleatoria de longitud X, donde X se encuentra en un rango entre intMinLongitudClave y intMaxLongitudClave.
     * La clave generada tendrá al menos 1 letra minúscula, 1 letra mayúscula, 1 número y un caracter especial.
     * 
     * 
     * @param type array $arrayParametros [
     *                                      "intMinLongitudClave"   => longitud mínima de la clave que se desea generar
     *                                      "intMaxLongitudClave"   => longitud máxima de la clave que se desea generar
     *                                    ]
     * 
     * @return string $strClaveGeneradaFinal
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-03-2018
     */
    public function generarClaveAleatoria($arrayParametros)
    {
        //Generar la longitud de la clave de manera aleatoria
        $intMinLongitudClave = $arrayParametros["intMinLongitudClave"];
        $intMaxLongitudClave = $arrayParametros["intMaxLongitudClave"];
        $intLongitudClave    = $arrayParametros["intLongitudClave"];
        
        if(isset($intLongitudClave) && !empty($intLongitudClave))
        {
            $intLongitudFinalClave = $intLongitudClave;
        }
        else
        {
            $arrayLongsPosibleClave = array();
        
            for($intContLongClave = $intMinLongitudClave; $intContLongClave <= $intMaxLongitudClave; $intContLongClave++)
            {
                $arrayLongsPosibleClave[] = $intContLongClave;
            }

            $intLongitudFinalClave = $arrayLongsPosibleClave[array_rand($arrayLongsPosibleClave)];
        }
        
        $arrayCaracteres                      = array();
        $arrayCaracteres["lower_case"]        = 'abcdefghijklmnopqrstuvwxyz';
        $arrayCaracteres["upper_case"]        = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $arrayCaracteres["numbers"]           = '1234567890';
        
        $strTodosCaracteres = '';
        $strClaveGenerada   = '';
        foreach($arrayCaracteres as $strCaracteresPorTipo)
        {
            $arrayCaracteresPorTipo = str_split($strCaracteresPorTipo);
            $strClaveGenerada       .= $strCaracteresPorTipo[array_rand($arrayCaracteresPorTipo)];
            $strTodosCaracteres     .= $strCaracteresPorTipo;
        }
        
        $arrayTodosCaracteres = str_split($strTodosCaracteres);
        for($i = 0; $i < $intLongitudFinalClave - count($arrayCaracteres); $i++)
        {
            $strClaveGenerada .= $arrayTodosCaracteres[array_rand($arrayTodosCaracteres)];
        }
        $strClaveGeneradaFinal = str_shuffle($strClaveGenerada);
        
        return $strClaveGeneradaFinal;
    }
    
    /**
     * Función que insertar un registro en la info_persona_empresa_rol_carac
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 28-04-2018
     * 
     * @param array $arrayParametros
     *                              [
     *                                  "objPersonaEmpresaRol"  => objeto persona empresa rol
     *                                  "objCaracteristica"     => objeto característica
     *                                  "strValor"              => valor de la característica asociada a la persona empresa rol
     *                                  "strUsrCreacion"        => usuario de creación
     *                                  "strIpClient"           => ip
     *                                  "intPerEmpRolCaractId"  => Id de caracteristica padre
     *                              ]
     * 
     * @return Object $objPersonaEmpresaRolCaract
     * 
     */
    public function ingresarPerCaracteristica($arrayParametros)
    {
        $objPersonaEmpresaRol       = $arrayParametros["objPersonaEmpresaRol"];
        $objCaracteristica          = $arrayParametros["objCaracteristica"];
        $strValorPerCaracteristica  = $arrayParametros["strValor"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strIpClient                = $arrayParametros["strIpClient"];
        
        $objPersonaEmpresaRolCaract = new InfoPersonaEmpresaRolCarac();
        $objPersonaEmpresaRolCaract->setPersonaEmpresaRolId($objPersonaEmpresaRol);
        $objPersonaEmpresaRolCaract->setCaracteristicaId($objCaracteristica);
        $objPersonaEmpresaRolCaract->setFeCreacion(new \DateTime('now'));
        $objPersonaEmpresaRolCaract->setUsrCreacion($strUsrCreacion);
        $objPersonaEmpresaRolCaract->setIpCreacion($strIpClient);
        $objPersonaEmpresaRolCaract->setValor($strValorPerCaracteristica);
        $objPersonaEmpresaRolCaract->setEstado('Activo');
        
        if (isset($arrayParametros["intPerEmpRolCaractId"]) && !empty ($arrayParametros["intPerEmpRolCaractId"]))
        {
            $objPersonaEmpresaRolCaract->setPersonaEmpresaRolCaracId($arrayParametros["intPerEmpRolCaractId"]);
        }
        
        $this->emComercial->persist($objPersonaEmpresaRolCaract);
        $this->emComercial->flush();
        
        return $objPersonaEmpresaRolCaract;
    }

    /**
     * Función que crea la tarea automática a IPCCL2, para la validación del correcto funcionamiento
     * del monitoreo de Telcograf que se activó para un cliente.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 18-12-2019
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 17-01-2020  - Se envía el service 'InfoCambiarPlan' por parámetro
     *                            para la creación de la tarea automática.
     *
     * @param array $arrayParametros [
     *                                intIdServicio         : Id del servicio del cliente.
     *                                strCodEmpresa         : Código de la empresa.
     *                                strNombreDepartamento : Nombre del departamento.
     *                                strObservacionTarea   : Observación de la tarea.
     *                                strNombreProceso      : Nombre del proceso de la tarea.
     *                                strNombreTarea        : Nombre de la tarea.
     *                                strUsrCreacion        : Usuario creación de la tarea.
     *                                strIpCliente          : Ip del usuario quien crea la tarea.
     *                               ]
     *
     * @return array $arrayRespuesta
     */
    public function crearTareaTelcograf($arrayParametros)
    {
        $intIdServicio          = $arrayParametros['intIdServicio'];
        $strCodEmpresa          = $arrayParametros['strCodEmpresa'];
        $strNombreDepartamento  = $arrayParametros['strNombreDepartamento'];
        $strObservacionTarea    = $arrayParametros['strObservacionTarea'];
        $strNombreProceso       = $arrayParametros['strNombreProceso'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCliente           = $arrayParametros['strIpCliente'];
        $strNombreTarea         = $arrayParametros['strNombreTarea'];
        $serviceInfoCambiarPlan = $arrayParametros['serviceInfoCambiarPlan'];

        try
        {
            $objInfoEmpresaGrupo = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);

            if (!is_object($objInfoEmpresaGrupo))
            {
                throw new \Exception('Error : La empresa ['.$strCodEmpresa.'] no existe');
            }

            $strPrefijoEmpresa = $objInfoEmpresaGrupo->getPrefijo();

            $objInfoServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);

            if (!is_object($objInfoServicio))
            {
                throw new \Exception('Error : El servicio ['.$intIdServicio.'] no existe..!!');
            }

            $objAdmiDepartamento = $this->emGeneral->getRepository("schemaBundle:AdmiDepartamento")
                    ->findOneBy(array('nombreDepartamento' => $strNombreDepartamento,
                                      'empresaCod'         => $strCodEmpresa,
                                      'estado'             => 'Activo'));

            if (! is_object($objAdmiDepartamento))
            {
                throw new \Exception('Error : El departamento ['.$strNombreDepartamento.'] no existe o no se encuentra Activo..!!');
            }

            $strNombreCanton = $objInfoServicio->getPuntoId()->getSectorId()->getParroquiaId()->getCantonId()->getJurisdiccion();
            $strNombreCanton = $strNombreCanton ? $strNombreCanton : 'QUITO';

            $objAdmiCanton = $this->emGeneral->getRepository("schemaBundle:AdmiCanton")
                    ->findOneBy(array('nombreCanton' => $strNombreCanton,'estado' => 'Activo'));

            if (!is_object($objAdmiCanton))
            {
                throw new \Exception('Error : El cantón ['.$strNombreCanton.'] no existe o no se encuentra Activo..!!');
            }

            //En caso que el proceso sea nulo, obtenemos la información del parámetro.
            if (empty($strNombreProceso) || $strNombreProceso === '')
            {
                $arrayParametroCabTarea = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('PARAMETROS_TELCOGRAF','TECNICO','TELCOGRAF','','PROCESO_TAREA','','','','','');

                if (empty($arrayParametroCabTarea) || count($arrayParametroCabTarea) < 1)
                {
                    throw new \Exception('Error : Información incompleta para poder crear la tarea automática..!!');
                }

                $strNombreProceso = $arrayParametroCabTarea['valor2'];
                $strNombreTarea   = $arrayParametroCabTarea['valor3'];
            }

            $objAdmiProceso = $this->emSoporte->getRepository("schemaBundle:AdmiProceso")
                    ->findOneBy(array('nombreProceso' => $strNombreProceso,'estado' => 'Activo'));

            if (!is_object($objAdmiProceso))
            {
                throw new \Exception('Error : El proceso ['.$strNombreProceso.'] no existe o no se encuentra Activo..!!');
            }

            $objAdmiTarea = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")
                    ->findOneBy(array('nombreTarea' => $strNombreTarea,
                                      'procesoId'   => $objAdmiProceso,
                                      'estado'      => 'Activo'));

            if (!is_object($objAdmiTarea))
            {
                throw new \Exception('Error : La tarea ['.$strNombreTarea.'] con el proceso ['.$strNombreProceso.'] '.
                                     'no existe o no se encuentra Activa..!!');
            }

            $arrayParametrosTarea = array('strUsrCreacion'     => $strUsrCreacion,
                                          'strIpCreacion'      => $strIpCliente,
                                          'strEmpresaCod'      => $strCodEmpresa,
                                          'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                          'objDepartamento'    => $objAdmiDepartamento,
                                          'strCantonId'        => $objAdmiCanton->getId(),
                                          'objPunto'           => $objInfoServicio->getPuntoId(),
                                          'strObservacion'     => $strObservacionTarea,
                                          'intTarea'           => $objAdmiTarea->getId(),
                                          'strTipoAfectado'    => "Cliente",
                                          'strObtenerArray'    => 'SI');

            if (!is_object($serviceInfoCambiarPlan))
            {
                throw new \Exception('Error : El serviceInfoCambiarPlan no es un objeto..!!');
            }

            $arrayRespuesta = $serviceInfoCambiarPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosTarea);

            if (empty($arrayRespuesta))
            {
                throw new \Exception('Error : Error al crear la tarea de validación del monitoreo de Telcograf..!!');
            }
        }
        catch (\Exception $objException)
        {
            $strMessage = "Error en el método que crea la tarea de validación del monitoreo de Telcograf..!!";

            if (strpos($objException->getMessage(),'Error : ') !== false)
            {
                $strMessage = explode("Error : ", $objException->getMessage())[1];
            }

            $this->serviceUtil->insertError('TelcoGraphService',
                                            'crearTareaTelcograf',
                                             $objException->getMessage(),
                                             $strUsrCreacion,
                                             $strIpCliente);

           $arrayRespuesta = array('status'  => false,
                                   'message' => $strMessage);
        }
        return $arrayRespuesta;
    }
}
