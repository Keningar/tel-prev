<?php

namespace telconet\tecnicoBundle\Service;

use telconet\seguridadBundle\Service\TokenValidatorService;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

/**
 * Documentación para la clase 'PortalNetlifeCamService'.
 *
 * Clase utilizada para manejar metodos que permiten realizar las consultas o transacciones relacionadas al portal de Netlifecam
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 23-03-2017
 */
class PortalNetlifeCamService
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
    private static $strNombreAppLdap = "NETLIFECAM";
    
    /**
     * Nombre del usuario del portal que modificará datos utilizados en Telcos 
     */
    private static $strUserPortal = "portal_ntlfcam";
    
    /**
     * Ip usada para los registros generados desde del portal de Netlifecam 
     */
    private static $strIpClientPortal = "127.0.0.1";
    
    /**
     * mensaje general mostrado en el portal cuando ocurre un error
     */
    private static $strMsjGeneralPortal = "Ha ocurrido un error. Por favor notificar al Administrador";
    
    /**
     * mensaje general cuando no se envían los parámetros mínimos requeridos para realizar una consulta
     */
    private static $strMsjParamsMinimos = "No se han enviado los parámetros mínimos requeridos para realizar la consulta: ";
    
    /**
     * estado Activo
     */
    private static $strEstadoActivo = "Activo";
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emSoporte;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    

    private $session;
    
    /**
     *
     * @var string
     */
    private $strUrlWebServicePortalNetlifecam;
    
    /**
     * service $serviceUtil
     */
    private $serviceUtil;
    
    
    /**
     * service $serviceTokenValidator
     */
    private $serviceTokenValidator;
    
    /**
     * service $serviceInfoServicio
     */
    private $serviceInfoServicio;
    
    /**
     * service $serviceInfoServicioTecnico
     */
    private $serviceInfoServicioTecnico;
    
    /**
     * service $serviceEnvioPlantilla
     */
    private $serviceEnvioPlantilla;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                        = $container;
        $this->emComercial                      = $container->get('doctrine.orm.telconet_entity_manager');     
        $this->emSoporte                        = $container->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emGeneral                        = $container->get('doctrine.orm.telconet_general_entity_manager');     
        $this->emInfraestructura                = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emSeguridad                      = $container->get('doctrine.orm.telconet_seguridad_entity_manager');
        $this->session                          = $container->get('session');
        $this->serviceUtil                      = $container->get('schema.Util');
        $this->restClient                       = $container->get('schema.RestClient');
        $this->serviceTokenValidator            = $container->get('seguridad.TokenValidator');
        $this->serviceInfoServicio              = $container->get('comercial.InfoServicio');
        $this->serviceInfoServicioTecnico       = $container->get('tecnico.InfoServicioTecnico');
        $this->serviceEnvioPlantilla            = $container->get('soporte.EnvioPlantilla');
        $this->strUrlWebServicePortalNetlifecam = $container->getParameter('tecnico.ws_ldap_url');
    }
    
    /**
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
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-03-2017
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
     * Documentación para el método 'generateTokenRequestWs'.
     *
     * Función que genera el token que será enviado como parámetro al web service usado para las consultas al arbol LDAP de clientes
     * 
     * @param type array $arrayParametros [
     *                                      "strIpClient"           => IP que solicitará la petición al web service
     *                                    ]
     * 
     * @return string $arrayResult['token']
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-05-2017
     */
    private function generateTokenRequestWs($arrayParametros)
    {
        $arrayRespuesta = array();
        $strToken       = "";
        $strStatus      = "";
        $arraySource    = array("name"          => self::$strNombreAppWsToken,
                                "originID"      => $arrayParametros["strIpClient"],
                                "tipoOriginID"  => "IP");
        $arrayResult    = $this->serviceTokenValidator->generateToken(  $arraySource, 
                                                                        self::$strGatewayWsToken, 
                                                                        self::$strServiceWsToken, 
                                                                        self::$strMethodWsToken,
                                                                        self::$strUserWsToken);
        if($arrayResult['status'] !== TokenValidatorService::$TOKEN_OK)
        {
            $strStatus = "ERROR";
        }
        else
        {
            $strStatus  = "OK";
            $strToken   = $arrayResult['token'];
        }
        $arrayRespuesta["strStatus"]    = $strStatus;
        $arrayRespuesta["strToken"]     = $strToken;
        return $arrayRespuesta;
    }
    
    /**
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
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-05-2017
     */
    private function getRequestWs($arrayParametros)
    {
        $arrayRequestWs = array("app"           => self::$strNombreAppLdap,
                                "data"          => $arrayParametros["arrayData"],
                                "op"            => $arrayParametros["strOpcion"],
                                "source"        => $arrayParametros["source"] ?
                                                   $arrayParametros["source"] :
                                                   array(
                                                            "name"          => self::$strGatewayWsToken,
                                                            "originID"      => $arrayParametros["strIpClient"],
                                                            "tipoOriginID"  => "IP"
                                                        ),
                                "token" => $arrayParametros["strToken"],
                                "user"  => $arrayParametros["strUser"]
                          );
        
        return $arrayRequestWs;
    }
    
    
    
    /**
     * Documentación para el método 'crearClienteLdapPortalNetlifecam'.
     *
     * Función que crea un cliente nuevo en el árbol LDAP de clientes Netlifecam llamado desde Telcos
     * 
     * 
     * @param type array $arrayParametros [
     *                                      "strNombre"     => nombres del cliente,
     *                                      "strApellido"   => apellidos del cliente,
     *                                      "strMail"       => correo seleccionado como usuario del portal,
     *                                      "strIpClient"   => IP desde donde se realiza la petición de creación,
     *                                      "strUser"       => usuario en sesión
     *                                    ]
     * 
     * @return array $arrayRespuestaWsCrearClienteLdap
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-05-2017
     */
    public function crearClienteLdapPortalNetlifecam($arrayParametros)
    {
        $arrayRespuestaWsCrearClienteLdap   = array();
        $arrayRespuestaToken                = $this->generateTokenRequestWs($arrayParametros);
        if($arrayRespuestaToken["strStatus"]=="OK")
        {
            $strTokenConsultaWs     = $arrayRespuestaToken["strToken"];
            $intIdPersonaEmpresaRol	= $arrayParametros["intIdPersonaEmpresaRol"];
            
            $arrayData              = array(
                                            "uid"                   => $intIdPersonaEmpresaRol,
                                            "nombre"                => $arrayParametros["strNombre"],
                                            "apellido"              => $arrayParametros["strApellido"],
                                            "mail"                  => $arrayParametros["strMail"],
                                            "pass"                  => ""
                                           );

            $arrayParametrosWs      = $this->getRequestWs(array(
                                                                "arrayData"     => $arrayData,
                                                                "strIpClient"   => $arrayParametros["strIpClient"],
                                                                "strUser"       => $arrayParametros["strUser"],
                                                                "strOpcion"     => "nuevo",
                                                                "strToken"      => $strTokenConsultaWs
                                                         ));
            $arrayRespuestaWsCrearClienteLdap   = $this->callWsLdapPortalNetlifecam($arrayParametrosWs);
        }
        else
        {
            $arrayRespuestaWsCrearClienteLdap['status'] = "ERROR";
            $arrayRespuestaWsCrearClienteLdap['msj']    = "No se pudo generar el token de manera correcta";
        }
        return $arrayRespuestaWsCrearClienteLdap;
    }
    
    /**
     * Documentación para el método 'eliminarClienteLdapPortalNetlifecam'.
     *
     * Función que elimina un cliente nuevo en el árbol LDAP de clientes Netlifecam llamado desde Telcos
     * 
     * 
     * @param type array $arrayParametros [
     *                                      "intIdPersonaEmpresaRol"    => id persona empresa rol del cliente,
     *                                      "strIpClient"               => IP desde donde se realiza la petición de creación,
     *                                      "strUser"                   => usuario en sesión,
     *                                      "strToken"                  => token
     *                                    ]
     * 
     * @return array $arrayRespuestaWsEliminar
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-05-2017
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 10-10-2017      Se corrige nombre de parametro  en array de metodo getRequestWs cambiado directamente en producción
     * @since 1.0
     */
    public function eliminarClienteLdapPortalNetlifecam($arrayParametros)
    {
        $arrayParametrosWs          = $this->getRequestWs(array(
                                                                "arrayData"     => array(
                                                                                            "uid" => $arrayParametros["intIdPersonaEmpresaRol"]
                                                                                    ),
                                                                "strIpClient"   => $arrayParametros["strIpClient"],
                                                                "strUser"       => $arrayParametros["strUser"],
                                                                "strOpcion"     => "eliminar",
                                                                "strToken"      => $arrayParametros["strToken"]
                                                         ));
        
        $arrayRespuestaWsEliminar   = $this->callWsLdapPortalNetlifecam($arrayParametrosWs);

        return $arrayRespuestaWsEliminar;
    }
    
    /**
     * Función que sirve para obtener la información de un cliente por medio del código temporal
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-05-2017
     * 
     * @param array $arrayData
     *                          [
     *                              "codVer"    => código de verificación
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "resultado" => array con la información del cliente
     *                                                  [
     *                                                      "idPerCar"  => id persona empresa rol caracteristica del código de verificación
     *                                                      "nomCli"    => nombre del cliente
     *                                                      "correo"    => correo seleccionado como usuario del portal
     *                                                      "idPer"     => id persona empresa rol
     *                                                  ]
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     */
    public function getInfoClienteCodVer($arrayData)
    {
        $arrayRespuesta     = array();
        $arrayInfoCliente   = array();
        $strStatus          = "ERROR";
        $strMsjPortal       = "";
        try
        {
            $strCodigoTmp   = $arrayData['codVer'] ? $arrayData['codVer'] : "";
            if(isset($strCodigoTmp) && !empty($strCodigoTmp))
            {
                $arrayParamsCaractCodTmp        = array('descripcionCaracteristica' => 'CODIGO_TMP_PORTAL',
                                                        'estado'                    => self::$strEstadoActivo);

                $objCaractCodTmp                = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy($arrayParamsCaractCodTmp);


                $arrayParamsCaractUserPortal    = array('descripcionCaracteristica' => 'USUARIO_PORTAL',
                                                        'estado'                    => self::$strEstadoActivo);

                $objCaractUserPortal            = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy($arrayParamsCaractUserPortal);

                if(is_object($objCaractCodTmp) && is_object($objCaractUserPortal))
                {
                    $objPerCaractCodTmp = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->findOneBy(array(
                                                                                  "caracteristicaId"    => $objCaractCodTmp,
                                                                                  "valor"               => $strCodigoTmp,
                                                                                  "estado"              => self::$strEstadoActivo
                                                                              ));
                    if(is_object($objPerCaractCodTmp))
                    {
                        $intIdPerCaractCodTmp   = $objPerCaractCodTmp->getId();
                        $objPersonaEmpresaRol   = $objPerCaractCodTmp->getPersonaEmpresaRolId();

                        if(is_object($objPersonaEmpresaRol))
                        {
                            $intIdPersonaEmpresaRol = $objPersonaEmpresaRol->getId();
                            $objPersona             = $objPersonaEmpresaRol->getPersonaId();

                            if(is_object($objPersona))
                            {
                                $strNombreCliente       = sprintf('%s', $objPersona);

                                $objPerCaractUserPortal = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                            ->findOneBy(array(
                                                                                              "personaEmpresaRolId"   => $objPersonaEmpresaRol,
                                                                                              "caracteristicaId"      => $objCaractUserPortal,
                                                                                              "estado"                => self::$strEstadoActivo
                                                                                       ));
                                if(is_object($objPerCaractUserPortal))
                                {
                                    $strCorreoUsuario               = $objPerCaractUserPortal->getValor();
                                    $arrayInfoCliente["idPerCar"]   = $intIdPerCaractCodTmp;
                                    $arrayInfoCliente["nomCli"]     = $strNombreCliente;
                                    $arrayInfoCliente["correo"]     = $strCorreoUsuario;
                                    $arrayInfoCliente["idPer"]      = $intIdPersonaEmpresaRol;
                                    $strStatus                      = "OK";
                                    $strMsjPortal                   = "Información del cliente obtenida correctamente";
                                }
                                else
                                {
                                    throw new \Exception("No se ha encontrado la característica del correo elegido como usuario del portal  "
                                                         ."USUARIO_PORTAL con id del cliente ".$intIdPersonaEmpresaRol);
                                }
                            }
                            else
                            {
                                throw new \Exception("No se pudo obtener el objeto de la persona para la INFO_PERSONA_EMPRESA_ROL con id "
                                                     .$intIdPersonaEmpresaRol);
                            }
                        }
                        else
                        {
                            throw new \Exception("No se pudo obtener el objeto del cliente para la INFO_PERSONA_EMPRESA_ROL_CARACT con id "
                                                 .$intIdPerCaractCodTmp);
                        }
                    }
                    else
                    {
                        $strMsjPortal = "El código de verificación ingresado no está asociado a ningún cliente.";
                    }
                }
                else
                {
                    throw new \Exception("No existe alguna de las siguientes características : CODIGO_TMP_PORTAL o USUARIO_PORTAL");
                }
            }
            else
            {
                throw new \Exception("No se ha enviado el código temporal de verificación codVer=".$strCodigoTmp);
            } 
        }
        catch (\Exception $e) 
        {
            $strMsjPortal = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->getInfoClienteCodVer',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        
        $arrayRespuesta['resultado']    = $arrayInfoCliente;
        $arrayRespuesta['status']       = $strStatus;
        $arrayRespuesta['msj']          = $strMsjPortal;

        return $arrayRespuesta;
    }
    
    /**
     * Función que obtiene la información de las cámaras
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-05-2017
     * 
     * @param array $arrayData
     *                          [
     *                              "estadosServ"   => array de estados del servicio
     *                              "idPer"         => id persona empresa rol del cliente
     *                              "estadoServ"    => estado del servicio
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "resultado" => array con la información de las cámaras
     *                                                  [
     *                                                      "total"         => total de cámaras de acuerdo a los parámetros enviados
     *                                                      "resultInfo"    => array con la información de las cámaras
     *                                                      [
     *                                                          "idServ"    => id del servicio
     *                                                          "idPunto"   => id del punto
     *                                                          "idElem"    => id elemento de la cámara
     *                                                          "idPer"     => id persona empresa rol del cliente
     *                                                          "serie"     => serie de la cámara
     *                                                          "ddns"      => DDNS de la cámara
     *                                                          "nombre"    => nombre de la cámara
     *                                                          "servidor"  => nombre del servidor donde se alojarán los videos
     *                                                      ]
     *                                                  ]
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     */
    public function getCamarasCliente($arrayData)
    {
        $arrayRespuesta                 = array();
        $arrayRespuesta['resultado']    = array();
        $arrayRespuesta['status']       = "ERROR";
        $arrayRespuesta['msj']          = "";
        try
        {
            $intIdPer       = $arrayData["idPer"] ? $arrayData["idPer"] : 0;
            $intIdServidor  = $arrayData["idServidor"] ? $arrayData["idServidor"] : 0;
            if((isset($arrayData["idPer"]) && !empty($arrayData["idPer"])) || (isset($arrayData["idServidor"]) && !empty($arrayData["idServidor"])))
            {
                $arrayInfoCamaras               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                          ->getResultadoCamarasPortal( $arrayData );
                $arrayRespuesta['resultado']    = $arrayInfoCamaras;
                $arrayRespuesta['status']       = "OK";
                $arrayRespuesta['msj']          = "Información de las cámaras obtenida correctamente";
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idPer=".$intIdPer." o idServidor=".$intIdServidor);
            }
        }
        catch (\Exception $e) 
        {
            $arrayRespuesta['msj'] = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->getCamarasCliente',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    /**
     * Función que sirve para obtener la información de un cliente por medio del código temporal
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-05-2017
     * 
     * @param array $arrayData
     *                          [
     *                              "nombre"    => array de estados del servicio
     *                              "idElem"    => id elemento de la cámara
     *                          ]
     * 
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     */
    public function editarNombreCam($arrayData)
    {
        $arrayRespuesta     = array();
        $strStatus          = "ERROR";
        $strMsjPortal       = "";

        $this->emInfraestructura->beginTransaction();
        try
        {
            $strNuevoNombreCam  = $arrayData['nombre'] ? $arrayData['nombre'] : "";
            $intIdElemento      = $arrayData['idElem'] ? $arrayData['idElem'] : 0;
            
            if((isset($strNuevoNombreCam) && !empty($strNuevoNombreCam)) && (isset($intIdElemento) && !empty($intIdElemento)))
            {
                $objElementoCam = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElemento);

                if(is_object($objElementoCam))
                {
                    $objDetNombreCam    = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                  ->findOneBy(array("elementoId"    => $intIdElemento,
                                                                                    "detalleNombre" => "NOMBRE_CAMARA",
                                                                                    "estado"        => self::$strEstadoActivo
                                                                             ));

                    if(is_object($objDetNombreCam))
                    {
                        $strObservHisto = "Se modifica el detalle con el nombre de la cámara desde el portal:<br>"
                                        . "Nombre anterior: ".$objDetNombreCam->getDetalleValor()."<br>"
                                        . "Nombre nuevo: ".$strNuevoNombreCam."<br>";

                        $objDetNombreCam->setDetalleValor($strNuevoNombreCam);
                        $this->emInfraestructura->persist($objDetNombreCam);

                        $objInfoHistorialElemento = new InfoHistorialElemento();
                        $objInfoHistorialElemento->setElementoId($objElementoCam);
                        $objInfoHistorialElemento->setObservacion($strObservHisto);
                        $objInfoHistorialElemento->setFeCreacion(new \DateTime('now'));
                        $objInfoHistorialElemento->setUsrCreacion(self::$strUserPortal);
                        $objInfoHistorialElemento->setIpCreacion(self::$strIpClientPortal);
                        $objInfoHistorialElemento->setEstadoElemento(self::$strEstadoActivo);
                        $this->emInfraestructura->persist($objInfoHistorialElemento);

                        $this->emInfraestructura->flush();
                        $this->emInfraestructura->commit();

                        $strStatus      = "OK";
                        $strMsjPortal   = "Se ha actualizado correctamente el nombre de la cámara";
                    }
                    else
                    {
                        throw new \Exception("No existe el detalle NOMBRE_CAMARA de la cámara con id ".$intIdElemento);
                    }
                }
                else
                {
                    throw new \Exception("No existe la cámara con el id del elemento ".$intIdElemento);
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."nombre=".$strNuevoNombreCam." y idElem".$intIdElemento);
            }
        }
        catch (\Exception $e) 
        {
            $strMsjPortal   = self::$strMsjGeneralPortal;
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
                $this->emInfraestructura->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->editarNombreCam',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        $arrayRespuesta['status']       = $strStatus;
        $arrayRespuesta['msj']          = $strMsjPortal;

        return $arrayRespuesta;
    }
    
    
    
    
    /**
     * Función que sirve para guardar la password del usuario del portal en LDAP
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 22-05-2017
     * 
     * @param array $arrayDataWs
     *                          [
     *                              "data"  
     *                              [
     *                                  "idPerCar"   => id persona empresa rol carac del código temporal
     *                              ],
     *                              "opcion"    => método invocado en el ws
     *                              "source"    => array con los parámetros usados para el token
     *                              "token"     => token
     *                              "user"      => usuario
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function guardarClaveCliente($arrayDataWs)
    {
        $arrayRespuesta             = array();
        $arrayRespuesta["status"]   = "ERROR";
        $arrayRespuesta["msj"]      = "";
        $this->emComercial->beginTransaction();
        try
        {
            $intIdPerCaractCodTmp   = $arrayDataWs["data"]["idPerCar"] ? $arrayDataWs["data"]["idPerCar"] : 0;
            $strUid                 = $arrayDataWs["data"]["uid"] ? $arrayDataWs["data"]["uid"] : "";
            $arrayAtributos         = $arrayDataWs["data"]["atributos"] ? $arrayDataWs["data"]["atributos"] : array();
            
            
            if((isset($intIdPerCaractCodTmp) && !empty($intIdPerCaractCodTmp)) && (isset($strUid) && !empty($strUid))
                && (isset($arrayAtributos) && !empty($arrayAtributos)))
            {
                $objPerCaractCodTmp     = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                            ->find($intIdPerCaractCodTmp);
                if(is_object($objPerCaractCodTmp))
                {
                    $objPerCaractCodTmp->setEstado("Eliminado");
                    $objPerCaractCodTmp->setFeUltMod(new \DateTime('now'));
                    $objPerCaractCodTmp->setUsrUltMod(self::$strUserPortal);
                    $this->emComercial->persist($objPerCaractCodTmp);
                    $this->emComercial->flush();

                    $arrayDataWs["op"]          = "actualizar";
                    $arrayRespuesta             = $this->callWsLdapPortalNetlifecam($arrayDataWs);

                    if($arrayRespuesta["status"]=="OK")
                    {
                        $this->emComercial->commit();
                    }
                    else if( isset($arrayRespuesta["msjEx"]) && !empty($arrayRespuesta["msjEx"]) )
                    {
                        throw new \Exception($arrayRespuesta["msjEx"]);
                    }
                    else if( isset($arrayRespuesta["msj"]) && !empty($arrayRespuesta["msj"]) )
                    {
                        throw new \Exception($arrayRespuesta["msj"]);
                    }
                    else
                    {
                        throw new \Exception("Ha ocurrido un error inesperado");
                    }
                }
                else
                {
                    throw new \Exception("No se ha podido encontrar el objeto de la característica del código temporal asociada al cliente "
                                         ."en la INFO_PERSONA_EMPRESA_ROL_CARACT con id ".$intIdPerCaractCodTmp);
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idPerCar=".$intIdPerCaractCodTmp.", uid=".$strUid." y atributos");
            }
        } 
        catch (\Exception $e) 
        {
            $arrayRespuesta["msj"]  = self::$strMsjGeneralPortal;
            
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->guardarClaveCliente',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    
    /**
     * Función que sirve para realizar la autenticacion de un cliente en LDAP
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-06-2017
     * 
     * @param array $arrayDataWs
     *                          [
     *                              "data"  
     *                              [
     *                                  "idPerCar"   => id persona empresa rol carac del código temporal
     *                              ],
     *                              "opcion"    => método invocado en el ws
     *                              "source"    => array con los parámetros usados para el token
     *                              "token"     => token
     *                              "user"      => usuario
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function autenticarCliente($arrayDataWs)
    {
        $arrayRespuesta                 = array();
        $arrayRespuesta["status"]       = "ERROR";
        $arrayRespuesta["msj"]          = "";
        $arrayRespuesta["resultado"]    = array();
        $this->emComercial->beginTransaction();
        try
        {
            $strCorreoUserPortal    = $arrayDataWs["data"]["correo"] ? $arrayDataWs["data"]["correo"] : "";
            $strPassUser            = $arrayDataWs["data"]["pass"] ? $arrayDataWs["data"]["pass"] : "";
            if((isset($strCorreoUserPortal) && !empty($strCorreoUserPortal)) && (isset($strPassUser) && !empty($strPassUser)))
            {
                $arrayParamsCaractUserPortal    = array('descripcionCaracteristica' => 'USUARIO_PORTAL',
                                                        'estado'                    => self::$strEstadoActivo);

                $objCaractUserPortal            = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy($arrayParamsCaractUserPortal);

                if(is_object($objCaractUserPortal))
                {
                    $objPerCaractUserPortal = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                ->findOneBy(array(
                                                                                  "caracteristicaId"      => $objCaractUserPortal,
                                                                                  "valor"                 => $strCorreoUserPortal,
                                                                                  "estado"                => self::$strEstadoActivo
                                                                           ));
                    if(is_object($objPerCaractUserPortal))
                    {
                        $objPersonaEmpresaRol           = $objPerCaractUserPortal->getPersonaEmpresaRolId();

                        if(is_object($objPersonaEmpresaRol))
                        {
                            $intIdPerCliente                    = $objPersonaEmpresaRol->getId();
                            $objPersonaCliente                  = $objPersonaEmpresaRol->getPersonaId();
                            if(is_object($objPersonaCliente))
                            {
                                $strNombreCliente               = sprintf('%s', $objPersonaCliente);
                                $arrayDataWs["data"]["uid"]     = $intIdPerCliente;
                                $arrayRespuestaWs               = $this->callWsLdapPortalNetlifecam($arrayDataWs);

                                if($arrayRespuestaWs["status"]=="OK")
                                {
                                    $arrayRespuesta                 = $arrayRespuestaWs;
                                    $arrayRespuesta["resultado"]    = array("nomCli"    => $strNombreCliente,
                                                                            "idPer"     => $intIdPerCliente
                                                                            );
                                }
                                else if( isset($arrayRespuestaWs["msjEx"]) && !empty($arrayRespuestaWs["msjEx"]) )
                                {
                                    throw new \Exception($arrayRespuestaWs["msjEx"]);
                                }
                                else if( isset($arrayRespuestaWs["msj"]) && !empty($arrayRespuestaWs["msj"]) )
                                {
                                    $arrayRespuesta                 = $arrayRespuestaWs;
                                    $arrayRespuesta["resultado"]    = array();
                                }
                                else
                                {
                                    throw new \Exception("Ha ocurrido un error inesperado");
                                }
                            }
                            else
                            {
                                throw new \Exception("No se ha encontrado la información en la INFO_PERSONA del cliente con id ".$intIdPerCliente);
                            }
                        }
                        else
                        {
                            throw new \Exception("No se ha podido encontrar el objeto de la característica del USUARIO_PORTAL asociada al cliente "
                                                ."en la INFO_PERSONA_EMPRESA_ROL_CARACT con el valor ".$strCorreoUserPortal);
                        }
                    }
                    else
                    {
                        $arrayRespuesta["msj"]  = "Credenciales Incorrectas, por favor intente nuevamente";
                    }
                }
                else
                {
                    throw new \Exception("No existe la característica USUARIO_PORTAL correspondiente al usuario del portal");
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."correo=".$strCorreoUserPortal." y pass=".$strPassUser);
            }
        } 
        catch (\Exception $e) 
        {
            $arrayRespuesta["msj"]  = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->autenticarCliente',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    
    /**
     * Función que sirve para realizar la actualización de la password de un cliente del portal 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-06-2017
     * 
     * @param array $arrayDataWs
     *                          [
     *                              "data"  
     *                              [
     *                                  "idPer"         => id persona empresa rol del cliente
     *                                  "passActual"    => password actual
     *                                  "passNueva"     => password nueva
     *                              ],
     *                              "opcion"    => método invocado en el ws
     *                              "source"    => array con los parámetros usados para el token
     *                              "token"     => token
     *                              "user"      => usuario
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function actualizarPassCliente($arrayDataWs)
    {
        $arrayRespuesta                 = array();
        $arrayRespuesta["status"]       = "ERROR";
        $arrayRespuesta["msj"]          = "";
        $arrayRespuesta["resultado"]    = array();
        $this->emComercial->beginTransaction();
        try
        {
            $strToken           = $arrayDataWs["token"];
            $strUser            = $arrayDataWs["user"];
            $arraySource        = $arrayDataWs["source"];
            $intIdPerCliente    = $arrayDataWs["data"]["idPer"] ? $arrayDataWs["data"]["idPer"] : 0;
            $strPassActual      = $arrayDataWs["data"]["passActual"] ? $arrayDataWs["data"]["passActual"] : "";
            $strPassNueva       = $arrayDataWs["data"]["passNueva"] ? $arrayDataWs["data"]["passNueva"] : "";
            
            if((isset($intIdPerCliente) && !empty($intIdPerCliente)) && (isset($strPassActual) && !empty($strPassActual)) 
                && (isset($strPassNueva) && !empty($strPassNueva)))
            {
            
                $arrayDataWsAutenticarCliente       = array(
                                                            "uid"   => $intIdPerCliente,
                                                            "pass"  => $strPassActual
                                                      );

                $arrayParametrosWsAutenticarCliente = $this->getRequestWs(array(
                                                                                "arrayData"     => $arrayDataWsAutenticarCliente,
                                                                                "strOpcion"     => "autenticar",
                                                                                "source"        => $arraySource,
                                                                                "strToken"      => $strToken,
                                                                                "strUser"       => $strUser
                                                                         ));
                $arrayRespuestaWsAutenticarCliente  = $this->callWsLdapPortalNetlifecam($arrayParametrosWsAutenticarCliente);

                if($arrayRespuestaWsAutenticarCliente["status"]=="OK")
                {
                    $strTokenRespuestaAutenticarCliente = $arrayRespuestaWsAutenticarCliente["token"];
                    /**
                     * Autenticación correcta, se procederá a realizar la actualización de la password del cliente
                     */
                    $arrayDataWsActualizarPassCliente       = array(
                                                                    "uid"       => $intIdPerCliente,
                                                                    "atributos" => array(
                                                                                            array("nombre"  => "userPassword",
                                                                                                   "valor"  => $strPassNueva
                                                                                            )
                                                                                        )
                                                              );

                    $arrayParametrosWsActualizarPassCliente = $this->getRequestWs(array(
                                                                                        "arrayData"     => $arrayDataWsActualizarPassCliente,
                                                                                        "strOpcion"     => "actualizar",
                                                                                        "source"        => $arraySource,
                                                                                        "strToken"      => $strTokenRespuestaAutenticarCliente,
                                                                                        "strUser"       => $strUser
                                                                                 ));


                    $arrayRespuestaActualizarPassCliente    = $this->callWsLdapPortalNetlifecam($arrayParametrosWsActualizarPassCliente);

                    if($arrayRespuestaActualizarPassCliente["status"]=="OK")
                    {
                        $arrayRespuesta = $arrayRespuestaActualizarPassCliente;
                    }
                    else if( isset($arrayRespuestaActualizarPassCliente["msjEx"]) && !empty($arrayRespuestaActualizarPassCliente["msjEx"]))
                    {
                        throw new \Exception($arrayRespuestaActualizarPassCliente["msjEx"]);
                    }
                    else if( isset($arrayRespuestaActualizarPassCliente["msj"]) && !empty($arrayRespuestaActualizarPassCliente["msj"]) )
                    {
                        throw new \Exception($arrayRespuestaActualizarPassCliente["msj"]);
                    }
                    else
                    {
                        throw new \Exception("Ha ocurrido un error inesperado");
                    }
                }
                else if( isset($arrayRespuestaWsAutenticarCliente["msjEx"]) && !empty($arrayRespuestaWsAutenticarCliente["msjEx"]) )
                {
                    throw new \Exception($arrayRespuestaWsAutenticarCliente["msjEx"]);      
                }
                else
                {
                    $arrayRespuesta = $arrayRespuestaWsAutenticarCliente;
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idPer=".$intIdPerCliente.", passActual=".$strPassActual
                                     ." y passNueva".$strPassNueva);
            }
        } 
        catch (\Exception $e) 
        {
            $arrayRespuesta["msj"]  = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->actualizarPassCliente',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
            
        return $arrayRespuesta;
    }
    
    
    

    
    
    /**
     * Documentación para el método 'callWsLdapPortalNetlifecam'.
     *
     * Función que realiza el llamado a cualquier función del web service para gestionar el Ldap
     * 
     * @param type array $arrayParametrosWs array con diferentes estructuras dependiendo de la opción que se invoca en la petición al web service
     * 
     * @return string $arrayResultado["status", "msj", "token"]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-05-2017
     */
    public function callWsLdapPortalNetlifecam($arrayParametrosWs)
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

        $arrayResponseJsonWS = $this->restClient->postJSON($this->strUrlWebServicePortalNetlifecam, $strDataWs, $arrayOptionsRest);
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
            $strStatus        = "ERROR";
            $strMensaje       = $arrayResponseJsonWS['error'];
        }

        $arrayResultado['status']   = $strStatus;
        $arrayResultado['msj']      = $strMensaje;
        $arrayResultado['msjEx']    = $strMsjEx;
        $arrayResultado['token']    = $strTokenResultado;
        
        return $arrayResultado;
    }
    
    
    /**
     * Documentación para el método 'getInfoProdAdic'.
     *
     * Función que obtiene la información de los productos adicionales que se visualizarán en el portal de NETLIFECAM
     * 
     * @return string $arrayResultado["status", "msj", "resultado"]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-06-2017
     */
    public function getInfoProdAdic()
    {
        $arrayRespuesta                 = array();
        $arrayRespuesta['resultado']    = array();
        $arrayRespuesta['status']       = "ERROR";
        $arrayRespuesta['msj']          = "";
        try
        {
            $arrayResProdAdic                = array();
            $arrayParamsDetProdAdicionales   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                               ->get(  'PRODUCTOS_ADICIONALES_PLAN_NETLIFECAM', 
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '', 
                                                                       '');
            if( !empty($arrayParamsDetProdAdicionales) )
            {
                foreach( $arrayParamsDetProdAdicionales as $arrayParamDetProdAdicional )
                {
                    $strNombreRespWs        = $arrayParamDetProdAdicional['valor2'];
                    $strNombreTecnicoProd   = $arrayParamDetProdAdicional['valor1'];
                    
                    $objProductoAdic        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findOneBy(array("nombreTecnico"    => $strNombreTecnicoProd,
                                                                                  "estado"           => self::$strEstadoActivo));
                    if(is_object($objProductoAdic))
                    {
                        $strFuncionPrecio                           = $objProductoAdic->getFuncionPrecio();
                        $arrayPrecio                                = explode("PRECIO=", $strFuncionPrecio );
                        $arrayResProdAdic[$strNombreRespWs]         = array("id"        => $objProductoAdic->getId(),
                                                                            "precio"    => $arrayPrecio[1] ? $arrayPrecio[1] : ""
                                                                      );
                        $arrayRespuesta['resultado']    = $arrayResProdAdic;
                        $arrayRespuesta['status']       = "OK";
                        $arrayRespuesta['msj']          = "Información de los productos adicionales obtenida correctamente";
                    }
                    else
                    {
                        throw new \Exception("No se ha podido encontrar un producto con nombre técnico ".$strNombreTecnicoProd);
                    }
                }
            }
            else
            {
                throw new \Exception("No se ha podido encontrar la información de los productos adicionales ");
            }
        }
        catch (\Exception $e) 
        {
            $arrayRespuesta["msj"]  = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->getInfoProdAdic',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    
    /**
     * Documentación para el método 'comprarProdAdic'.
     *
     * Función que permite comprar un producto adicional asociado a un servicio con el plan NETLIFECAM 
     * 
     * @param type array $arrayParametros[
     *                                      "strUsrCreacion"    => usuario de creación
     *                                      "strIpCreacion"     => ip de creación
     *                                      "intIdAccion"       => id de la acción confirmar servicio
     *                                   ]
     * @return string $arrayResultado["status", "msj"]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-06-2017
     */
    public function comprarProdAdic($arrayParametros)
    {
        $strStatus                  = "ERROR";
        $strMsj                     = "";                         
        $arrayRespuesta             = array();
        
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"] ? $arrayParametros["strUsrCreacion"] : self::$strUserPortal;
        $strIpCreacion              = $arrayParametros["strIpCreacion"] ? $arrayParametros["strIpCreacion"] : self::$strIpClientPortal;
        $intIdAccion                = $arrayParametros["intIdAccion"] ? $arrayParametros["intIdAccion"] : "847";
        
        $this->emComercial->beginTransaction();
        try
        {
            $strAliasProducto           = $arrayParametros["aliasProd"] ? $arrayParametros["aliasProd"]: "";
            $intIdServicio              = $arrayParametros["idServ"] ? $arrayParametros["idServ"] : 0;
            $strOrigen                  = $arrayParametros["orig"] ? $arrayParametros["orig"] : "";
            $intIdProducto              = $arrayParametros["idProd"] ? $arrayParametros["idProd"] : 0;
            $intIdServCamaraProdAdic    = $arrayParametros["idServCam"] ? $arrayParametros["idServCam"] : 0;
            $intCantidadProd            = $arrayParametros["cant"] ? $arrayParametros["cant"] : 0;
            $objServicio                = null;
            $arrayObjsServicios         = array();
            
            if((isset($strOrigen) && !empty($strOrigen)) && (isset($intIdServCamaraProdAdic) && !empty($intIdServCamaraProdAdic)) 
                && (isset($intIdProducto) && !empty($intIdProducto)) && (isset($intCantidadProd) && !empty($intCantidadProd))
                && (isset($strAliasProducto) && !empty($strAliasProducto)) && 
                ( (isset($intIdServicio) && !empty($intIdServicio) && $strOrigen=="TELCOS") || ($strOrigen=="PORTAL") ))
            {
                $objServicioCam = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServCamaraProdAdic);
                if(is_object($objServicioCam))
                {
                    $objProductoAdic    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                    if(is_object($objProductoAdic))
                    {
                        if($strOrigen=="PORTAL")
                        {
                            $intIdOficinaFact           = 0;
                            $intFrecuenciaProducto      = 0;
                            $objPuntoServicioCam        = $objServicioCam->getPuntoId();
                            $objPuntoFactServicioCam    = $objServicioCam->getPuntoFacturacionId();

                            if(is_object($objPuntoServicioCam))
                            {
                                if(is_object($objPuntoFactServicioCam))
                                {
                                    $objJurisdiccionFactServicioCam = $objPuntoFactServicioCam->getPuntoCoberturaId();
                                    if(is_object($objJurisdiccionFactServicioCam))
                                    {
                                        $intIdOficinaFact = $objJurisdiccionFactServicioCam->getOficinaId();
                                    }
                                    else
                                    {
                                        throw new \Exception("No se ha podido obtener la jurisdicción del servicio de la cámara");
                                    }
                                }
                                else
                                {
                                    throw new \Exception("No se ha podido obtener el punto de facturación del servicio de la cámara");
                                }
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el punto del servicio de la cámara");
                            }

                            $arrayFrecuenciaMensual   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne( "FRECUENCIA_FACTURACION", 
                                                                                  "", 
                                                                                  "", 
                                                                                  "", 
                                                                                  "", 
                                                                                  "Mensual", 
                                                                                  "", 
                                                                                  "", 
                                                                                  "", 
                                                                                  "18");
                            if( isset($arrayFrecuenciaMensual) && !empty($arrayFrecuenciaMensual) )
                            {
                                $intFrecuenciaProducto = $arrayFrecuenciaMensual["valor1"];
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener la frecuencia de facturación");
                            }
                            $strFuncionPrecioProdAdic   = $objProductoAdic->getFuncionPrecio();
                            $arrayPrecioProdAdic        = explode("PRECIO=", $strFuncionPrecioProdAdic );
                            
                            /**
                             * Si la petición es desde el portal, se deberán crear los servicios de acuerdo a la cantidad enviada
                             */
                            for ($intContadorServicios = 0; $intContadorServicios < $intCantidadProd; $intContadorServicios++) 
                            {
                                $strTipoOrden   = "N";
                                $strEsVenta     = "S";
                                $objServicio = new InfoServicio();
                                $objServicio->setPuntoId($objPuntoServicioCam);
                                $objServicio->setTipoOrden($strTipoOrden);
                                $objServicio->setEsVenta($strEsVenta);
                                $objServicio->setCantidad(1);
                                $objServicio->setPuntoFacturacionId($objPuntoFactServicioCam);
                                $objServicio->setUsrVendedor(self::$strUserPortal);
                                $objServicio->setPrecioVenta($arrayPrecioProdAdic[1]); // Para MD precio venta = precio formula (precio unitario)
                                $objServicio->setPrecioFormula($arrayPrecioProdAdic[1]);
                                $objServicio->setProductoId($objProductoAdic);
                                $objServicio->setEstado('Pendiente');
                                $objServicio->setFrecuenciaProducto($intFrecuenciaProducto);
                                $objServicio->setMesesRestantes($intFrecuenciaProducto);
                                $objServicio->setUsrCreacion($strUsrCreacion);
                                $objServicio->setIpCreacion($strIpCreacion);
                                $objServicio->setFeCreacion(new \DateTime('now'));
                                $this->emComercial->persist($objServicio);
                                $this->emComercial->flush();

                                $objServicioTecnico  = new InfoServicioTecnico();
                                $objServicioTecnico->setServicioId($objServicio);
                                $objServicioTecnico->setTipoEnlace('PRINCIPAL');
                                $this->emComercial->persist($objServicioTecnico);
                                $this->emComercial->flush();

                                $objServicioHist = new InfoServicioHistorial();
                                $objServicioHist->setServicioId($objServicio);
                                $objServicioHist->setObservacion('Se creo el servicio');
                                $objServicioHist->setIpCreacion($strIpCreacion);
                                $objServicioHist->setFeCreacion(new \DateTime('now'));
                                $objServicioHist->setUsrCreacion($strUsrCreacion);
                                $objServicioHist->setEstado($objServicio->getEstado());
                                $this->emComercial->persist($objServicioHist);
                                $this->emComercial->flush();
                                
                                $arrayObjsServicios[] = $objServicio;
                            }
                        }
                        else if($strOrigen=="TELCOS")
                        {
                            $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                            $arrayObjsServicios[]   = $objServicio;
                        }
                        else
                        {
                            throw new \Exception("No se ha definido un flujo para el origen ".$strOrigen);
                        }
                        
                        
                        if(isset($arrayObjsServicios) && !empty($arrayObjsServicios))
                        {
                            foreach($arrayObjsServicios as $objServicioPorActivar)
                            {
                                if(is_object($objServicioPorActivar))
                                {
                                    $objServProdCaractRefServicioId = $this->serviceInfoServicioTecnico
                                                                           ->ingresarServicioProductoCaracteristica(
                                                                                                                        $objServicioPorActivar, 
                                                                                                                        $objProductoAdic,
                                                                                                                        'REF_SERVICIO_ID',
                                                                                                                        $intIdServCamaraProdAdic,
                                                                                                                        $strUsrCreacion
                                                                                                                   );
                                    if(!is_object($objServProdCaractRefServicioId))
                                    {
                                        throw new \Exception("No se ha podido crear la característica REF_SERVICIO_ID "
                                                             ."asociada al producto en el servicio con id ".$intIdServicio);
                                    }

                                    $arrayAdmiParamDetAliasProducto = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                      ->getOne(  'PRODUCTOS_ADICIONALES_PLAN_NETLIFECAM',
                                                                                                  '',
                                                                                                  '',
                                                                                                  '',
                                                                                                  '',
                                                                                                  $strAliasProducto,
                                                                                                  '',
                                                                                                  '',
                                                                                                  '',
                                                                                                  ''
                                                                                               );
                                    if (isset($arrayAdmiParamDetAliasProducto['valor3']) && !empty($arrayAdmiParamDetAliasProducto['valor3']))
                                    {
                                        $intCantidadAgregada                    = $arrayAdmiParamDetAliasProducto['valor3'];
                                        $strCaracteristica                      = $arrayAdmiParamDetAliasProducto['valor4'];
                                        $objServProdCaractCamAdic               = $this->serviceInfoServicioTecnico
                                                                                       ->ingresarServicioProductoCaracteristica(
                                                                                                                               $objServicioPorActivar,
                                                                                                                               $objProductoAdic,
                                                                                                                               $strCaracteristica,
                                                                                                                               $intCantidadAgregada,
                                                                                                                               $strUsrCreacion
                                                                                                                             );

                                        if(!is_object($objServProdCaractCamAdic))
                                        {
                                            throw new \Exception("No se ha podido crear la característica ".$strCaracteristica
                                                                 ." asociada al producto en el servicio con id ".$intIdServicio);
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception("No existe un flujo válido para este tipo de productos con alias ".$strAliasProducto);
                                    }

                                    $objServicioPorActivar->setEstado(self::$strEstadoActivo);
                                    $this->emComercial->persist($objServicioPorActivar);
                                    $this->emComercial->flush();

                                    $objAccion              = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
                                    $objServicioHistorial   = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objServicioPorActivar);
                                    $objServicioHistorial->setObservacion("Se confirmo el servicio");
                                    $objServicioHistorial->setEstado(self::$strEstadoActivo);
                                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                                    $objServicioHistorial->setAccion ($objAccion->getNombreAccion());
                                    $this->emComercial->persist($objServicioHistorial);
                                    $this->emComercial->flush();
                                }
                                else
                                {
                                    throw new \Exception("No se ha podido obtener/generar el servicio");
                                }
                            }
                            
                            $this->emComercial->commit();
                            $strStatus  = "OK";
                            $strMsj     = "Se ha realizado la compra del producto adicional de manera correcta";
                        }
                        else
                        {
                            throw new \Exception("No existen servicios gestionados");
                        }
                    }
                    else
                    {
                        throw new \Exception("No se ha podido obtener el producto");
                    }
                }
                else
                {
                    throw new \Exception("No se ha podido obtener el servicio asociado a la cámara");
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos
                                    ."aliasProd=".$strAliasProducto
                                    .", idServ=".$intIdServicio
                                    .", orig=".$strOrigen
                                    .", idProd=".$intIdProducto
                                    .", idServCam=".$intIdServCamaraProdAdic
                                    . " y cant=".$intCantidadProd);
            }
        }
        catch (\Exception $e) 
        {
            $strMsj = self::$strMsjGeneralPortal;
            
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }
            
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->comprarProdAdic',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayRespuesta['status']       = $strStatus;
        $arrayRespuesta['msj']          = $strMsj;
        return $arrayRespuesta;
    }
    
    
    
    
    /**
     * Documentación para el método 'getCalculoHorasCamaras'.
     *
     * Se obtiene el número de horas por cámara
     * 
     * @param type array $arrayParametros
     * 
     * @return string $arrayResultado["status", "msj", "token"]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 02-06-2017
     */
    public function getCalculoHorasCamaras($arrayData)
    {
        $arrayRespuesta                 = array();
        $arrayRespuesta['resultado']    = array();
        $arrayRespuesta['status']       = "ERROR";
        $arrayRespuesta['msj']          = "";
        try
        {
            $intIdElem          = $arrayData["idElem"] ? $arrayData["idElem"] : 0;
            $strDescripServidor = $arrayData["descripServidor"] ? $arrayData["descripServidor"] : 0;
            $intIdServidor      = $arrayData["idServidor"] ? $arrayData["idServidor"] : 0;
            if( (isset($intIdElem) && !empty($intIdElem)) || (isset($intIdServidor) && !empty($intIdServidor))
                || (isset($strDescripServidor) && !empty($strDescripServidor)))
            {
                $arrayHorasCamara               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                          ->getCalculoHorasCamaras( $arrayData );
                $arrayRespuesta['resultado']    = $arrayHorasCamara;
                $arrayRespuesta['status']       = "OK";
                $arrayRespuesta['msj']          = "Información de las cámaras obtenida correctamente";
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idElem=".$intIdElem.", idServidor=".$intIdServidor
                                     .", descripServidor=".$strDescripServidor);
            }
        }
        catch (\Exception $e) 
        {
            $arrayRespuesta['msj']  = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->getCalculoHorasCamaras',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }

    /**
     * 
     * Función utilizada para saber si el cliente ya tiene un portal activo.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2017
     * 
     * @return JsonResponse $objResponse
     **/
    public function tienePortalActivoNetlifeCamStoragePortal($arrayParametros)
    {
        $intIdPersonaEmpresaRol     = $arrayParametros["intIdPersonaEmpresaRol"];
        $strMsjError                = "";
        $strStatus                  = "ERROR";
        $boolTienePortalActivo      = false;
        $strCorreoUsuario           = "";
        try
        {
            if($intIdPersonaEmpresaRol)
            {
                $objCaractPortalActivo  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findOneBy(array(  'descripcionCaracteristica' => 'PORTAL_ACTIVO',
                                                                                'estado'                    => self::$strEstadoActivo
                                                                       ));
                if(is_object($objCaractPortalActivo))
                {
                    $intIdCaractPortalActivo    = $objCaractPortalActivo->getId();
                    $objCaractPerPortalActivo   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                    ->findOneBy(
                                                                                  array(
                                                                                          'personaEmpresaRolId' => $intIdPersonaEmpresaRol,
                                                                                          'caracteristicaId'    => $intIdCaractPortalActivo,
                                                                                          'estado'              => self::$strEstadoActivo
                                                                            ));
                    if(is_object($objCaractPerPortalActivo))
                    {
                        $boolTienePortalActivo  = true;

                        $objCaractUserPortalActivo  = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array('descripcionCaracteristica' => 'USUARIO_PORTAL',
                                                                                          'estado'                    => self::$strEstadoActivo));
                        if(is_object($objCaractUserPortalActivo))
                        {
                            $intIdCaractUserPortalActivo    = $objCaractUserPortalActivo->getId();
                            $objCaractPerUserPortalActivo   = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                                ->findOneBy(
                                                                                          array(
                                                                                                'personaEmpresaRolId' => $intIdPersonaEmpresaRol,
                                                                                                'caracteristicaId'    => $intIdCaractUserPortalActivo,
                                                                                                'estado'              => self::$strEstadoActivo
                                                                                  ));
                            if(is_object($objCaractPerUserPortalActivo))
                            {
                                $strStatus          = "OK";
                                $strCorreoUsuario   = $objCaractPerUserPortalActivo->getValor();
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener la caracteristica del usuario del portal asociada al cliente");
                            }
                        }
                        else
                        {
                            throw new \Exception("No se ha podido obtener la caracteristica del usuario del portal");
                        }
                    }
                    else
                    {
                        $strStatus  = "OK";
                    }
                }
                else
                {
                    throw new \Exception("No se ha podido obtener la caracteristica del portal.");
                }
            }
            else
            {
                throw new \Exception("No se ha podido obtener el parámetro del cliente.");
            }
        }
        catch (\Exception $e) 
        {
            $strMsjError    = "Ha ocurrido un error. Por favor notifique a Sistemas!";
            $this->serviceUtil->insertError('Telcos+', 
                                            'PortalNetlifeCamService->tienePortalActivoNetlifeCamStoragePortal',
                                            $e->getMessage(),
                                            $arrayParametros["strUsrCreacion"],
                                            $arrayParametros["strIpClient"]);
        }

        $arrayRespuesta = array(
                                        "strStatus"             => $strStatus,
                                        "boolTienePortalActivo" => $boolTienePortalActivo,
                                        "strCorreoUsuario"      => $strCorreoUsuario,
                                        "strMensaje"            => $strMsjError
                          );
        
        return $arrayRespuesta;
    }

    
    /**
     * Función que sirve para enviar el correo al cliente al activar un servicio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-06-2017
     * 
     * @param array $arrayParametros
     *                          [
     *                              
     *                              "objPersonaEmpresaRol"  => objeto persona empresa rol del cliente
     *                              "strUsrCreacion"        => usuario de creación
     *                              "strIpClient"           => ip
     *                              "strCodPlantilla"       => código de la plantilla que se enviará al correo del cliente
     *                              "strAsunto"             => asunto del correo
     *                              "arrayDataMail"         => parámetros requeridos en la plantilla
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "strMsj"    => mensaje de información 
     *                              ]
     * 
     */
    public function enviarInformacionCorreoNetlifeCam($arrayParametros)
    {
        $arrayRespuesta             = array();
        $strStatus                  = "ERROR";
        $strMensaje                 = "";
        $arrayDestinatarios         = array();
        try
        {
            $intIdPersonaEmpresaRol = $arrayParametros["intIdPersonaEmpresaRol"];
            
            $arrayParamsContactos           = array(
                                                    "intIdPersonaEmpresaRol"            => $intIdPersonaEmpresaRol,
                                                    "strInnerJoinFormasContactoCliente" => "SI",
                                                    "strDescripcionFormaContacto"       => "Correo Electronico",
                                              );
            $arrayRespuestaFormasContacto   = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                ->getResultadoFormasContactoClienteByCriterios($arrayParamsContactos);
            $arrayResultadoFormasContacto   = $arrayRespuestaFormasContacto['arrayResultado'];
            $intTotalFormasContactos        = $arrayRespuestaFormasContacto['intTotal'];
            
            
            if($intTotalFormasContactos > 0)
            {
                foreach($arrayResultadoFormasContacto as $arrayDataFc)
                {
                    $strCorreo = $arrayDataFc["strValorFormaContacto"];
                    if(isset($strCorreo) && !empty($strCorreo))
                    {
                        $arrayDestinatarios[] = $strCorreo;
                    }
                }
            }

            if(!empty($arrayDestinatarios))
            {
                $this->serviceEnvioPlantilla->generarEnvioPlantilla($arrayParametros["strAsunto"],
                                                                    $arrayDestinatarios,
                                                                    $arrayParametros["strCodPlantilla"],
                                                                    $arrayParametros["arrayDataMail"],
                                                                    '',
                                                                    '', 
                                                                    '',
                                                                    null,
                                                                    false,
                                                                    'notificaciones_telcos@telconet.ec');
                $strStatus  = 'OK';
                $strMensaje = 'Información Enviada Exitosamente!!!';
            }
            else
            {
                throw new \Exception("No se han obtenido destinatarios");
            }
        }
        catch (\Exception $e) 
        {
            $strMensaje = 'No se envió la información al usuario';
            $this->serviceUtil->insertError('Telcos+', 
                                            'PortalNetlifeCamService->enviarInformacionCorreoNetlifeCam',
                                            $e->getMessage(),
                                            $arrayParametros["strUsrCreacion"],
                                            $arrayParametros["strIpClient"]);
        }
        $arrayRespuesta['strStatus']    = $strStatus;
        $arrayRespuesta['strMsj']       = $strMensaje;
            
        return $arrayRespuesta;
    }
    
    /**
     * Función que sirve para obtener la configuración de alarmas en una cámara
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-06-2017
     * 
     * @param array $arrayData
     *                          [
     *                              
     *                              "idElem"       => id del elemento cámara
     *                              "diasAlarm"    => dias de la alarma
     *                              "hrIniAlarm"   => hora inicio de la alarma
     *                              "hrFinAlarm"   => hora fin de la alarma
     *                              "numFotAlarm"  => número de fotos que se enviarán cuando se produzca un movimiento
     *                              "sensibAlarm"  => sensibilidad de detección de movimiento
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function getConfigAlarma($arrayData)
    {
        $arrayRespuesta['status']       = "ERROR";
        $arrayRespuesta['msj']          = "";
        $arrayRespuesta['resultado']    = array();
        $arrayDetallesAlarmaCam         = array();
        $arrayNombresDetalleConfigAlarm = array();             
        try
        {
            $intIdElementoCam               = $arrayData["idElem"] ? $arrayData["idElem"] : 0;
            
            if(isset($intIdElementoCam) && !empty($intIdElementoCam))
            {
                $arrayParamsDetallesAlarmaCam = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get(  'DETALLES_NOMBRES_ALARMA_CAM', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        '');
                if( isset($arrayParamsDetallesAlarmaCam) && !empty($arrayParamsDetallesAlarmaCam) )
                {
                    foreach( $arrayParamsDetallesAlarmaCam as $arrayParamDetalleAlarmaCam )
                    {
                        $strNombreDetalleAlarm                                      = $arrayParamDetalleAlarmaCam['valor1'];
                        $strNombreDetalleWsAlarm                                    = $arrayParamDetalleAlarmaCam['valor2'];
                        $arrayNombresDetalleConfigAlarm[$strNombreDetalleAlarm]     = $strNombreDetalleWsAlarm;
                    }
                    
                    $objDetallesCamActual   = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->findBy(array("elementoId"      => $intIdElementoCam,
                                                                                     "detalleNombre"   => array_keys($arrayNombresDetalleConfigAlarm),
                                                                                     "estado"          => self::$strEstadoActivo));
                    foreach($objDetallesCamActual as $objDetalleCamActual)
                    {
                        $strNombreDetalle                               = $objDetalleCamActual->getDetalleNombre();
                        $strNombreDetalleWs                             = $arrayNombresDetalleConfigAlarm[$strNombreDetalle];
                        $arrayDetallesAlarmaCam[$strNombreDetalleWs]    = $objDetalleCamActual->getDetalleValor();
                    }
                    $arrayRespuesta['status']       = "OK";
                    $arrayRespuesta['msj']          = "Configuración obtenida correctamente";
                    $arrayRespuesta['resultado']    = $arrayDetallesAlarmaCam;
                }
                else
                {
                    throw new \Exception("No se encontraron detalles asociados al parámetro DETALLES_NOMBRES_ALARMA_CAM");
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idElem=".$intIdElementoCam);
            }
        }
        catch (\Exception $e) 
        {
            $arrayRespuesta['msj'] = self::$strMsjGeneralPortal;
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->getConfigAlarma',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    
    
    /**
     * Función que sirve para obtener la configuración de alarmas en una cámara
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-06-2017
     * 
     * @param array $arrayData
     *                          [
     *                              
     *                              "idElem"       => id del elemento cámara
     *                              "diasAlarm"    => dias de la alarma
     *                              "hrIniAlarm"   => hora inicio de la alarma
     *                              "hrFinAlarm"   => hora fin de la alarma
     *                              "numFotAlarm"  => número de fotos que se enviarán cuando se produzca un movimiento
     *                              "sensibAlarm"  => sensibilidad de detección de movimiento
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function guardarConfigAlarma($arrayData)
    {
        $arrayRespuesta['status']       = "ERROR";
        $arrayRespuesta['msj']          = "";
        $arrayNombresDetalleConfigAlarm = array();
        $arrayValidaCeroConfigAlarm     = array();
        $this->emInfraestructura->beginTransaction();
        try
        {
            $intIdElementoCam               = $arrayData["idElem"] ? $arrayData["idElem"] : 0;
            $strEstadoAlarm                 = $arrayData["estadoAlarm"] ? $arrayData["estadoAlarm"] : "";
            
            if((isset($intIdElementoCam) && !empty($intIdElementoCam)) && (isset($strEstadoAlarm) && !empty($strEstadoAlarm)))
            {
                $objElementoCam = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoCam);
                
                if(is_object($objElementoCam))
                {
                    $arrayParamsDetallesAlarmaCam = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get(  'DETALLES_NOMBRES_ALARMA_CAM', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '', 
                                                                            '');

                    if(isset($arrayParamsDetallesAlarmaCam) && !empty($arrayParamsDetallesAlarmaCam))
                    {
                        foreach( $arrayParamsDetallesAlarmaCam as $arrayParamDetalleAlarmaCam )
                        {
                            $strNombreDetalleAlarm                                      = $arrayParamDetalleAlarmaCam['valor1'];
                            $strNombreDetalleWsAlarm                                    = $arrayParamDetalleAlarmaCam['valor2'];
                            $strValidaCeroDetalleAlarm                                  = $arrayParamDetalleAlarmaCam['valor3'];
                            $arrayValidaCeroConfigAlarm[$strNombreDetalleAlarm]         = $strValidaCeroDetalleAlarm;
                            $arrayNombresDetalleConfigAlarm[$strNombreDetalleAlarm]     = $strNombreDetalleWsAlarm;
                        }

                        $objDetallesCamActual     = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findBy(array("elementoId"         => $intIdElementoCam,
                                                                                            "detalleNombre"     => 
                                                                                            array_keys($arrayNombresDetalleConfigAlarm),
                                                                                            "estado"            => self::$strEstadoActivo));

                        /**
                         * Se eliminan todos los detalles anteriores de la configuración de la cámara
                         */
                        foreach($objDetallesCamActual as $objDetalleCamActual)
                        {
                            $objDetalleCamActual->setEstado("Eliminado");
                            $this->emInfraestructura->persist($objDetalleCamActual);
                            $this->emInfraestructura->flush();
                        }

                        foreach ($arrayNombresDetalleConfigAlarm as $strNombreDetalle => $strNombreDetalleWs)
                        {
                            $strValorDetalleConfig          = $arrayData[$strNombreDetalleWs];
                            $strValidaCeroDetalleNombre     = $arrayValidaCeroConfigAlarm[$strNombreDetalle];
                            
                            if($strEstadoAlarm=="Activada"
                                && (!isset($strValorDetalleConfig) || ( (empty($strValorDetalleConfig) && $strValidaCeroDetalleNombre =="NO") 
                                    || (empty($strValorDetalleConfig) && $strValorDetalleConfig !== "0" && $strValidaCeroDetalleNombre =="SI")))
                              )
                            {
                                throw new \Exception(self::$strMsjParamsMinimos.$strNombreDetalleWs);
                            }
                            else
                            {
                                $this->serviceInfoServicioTecnico->ingresarDetalleElemento( $objElementoCam, 
                                                                                            $strNombreDetalle, 
                                                                                            $strNombreDetalle, 
                                                                                            $strValorDetalleConfig, 
                                                                                            self::$strUserPortal, 
                                                                                            self::$strIpClientPortal);
                            }
                        }
                        $this->emInfraestructura->commit();
                        $arrayRespuesta['status']   = "OK";
                        $arrayRespuesta['msj']      = "Configuración de la cámara guardada correctamente";
                    }
                    else
                    {
                        throw new \Exception("No se encontraron detalles asociados al parámetro DETALLES_NOMBRES_ALARMA_CAM");
                    }
                }
                else
                {
                    throw new \Exception("No se encontro el objeto para el idElem enviado ".$intIdElementoCam);
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idElem=".$intIdElementoCam.", estadoAlarm=".$strEstadoAlarm  );
            }
        }
        catch (\Exception $e) 
        {
            $arrayRespuesta['msj'] = self::$strMsjGeneralPortal;
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
                $this->emInfraestructura->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->guardarConfigAlarma',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    
    
    /**
     * Función que sirve para enviar un código temporal cuando el cliente haya olvidado su contraseña
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-06-2017
     * 
     * @param array $arrayData
     *                          [
     *                              "idPer" => id persona empresa rol del cliente
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function olvidoPassCliente($arrayDataWs)
    {
        $arrayRespuesta = array();
        $strStatus      = "ERROR";
        $strMsjPortal   = "";
        $strToken       = "";
        $this->emComercial->beginTransaction();
        try
        {
            $strCorreoUserPortal    = $arrayDataWs["data"]["correo"] ? $arrayDataWs["data"]["correo"] : "";
            if(isset($strCorreoUserPortal) && !empty($strCorreoUserPortal))
            {
                $arrayParamsCaractUserPortal    = array('descripcionCaracteristica' => 'USUARIO_PORTAL',
                                                        'estado'                    => self::$strEstadoActivo);

                $objCaractUserPortal            = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy($arrayParamsCaractUserPortal);
                if(is_object($objCaractUserPortal))
                {
                    $objPerCaractUserPortal = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                ->findOneBy(array(
                                                                                  "caracteristicaId"      => $objCaractUserPortal,
                                                                                  "valor"                 => $strCorreoUserPortal,
                                                                                  "estado"                => self::$strEstadoActivo
                                                                           ));
                    if(is_object($objPerCaractUserPortal))
                    {
                        $objPersonaEmpresaRol   = $objPerCaractUserPortal->getPersonaEmpresaRolId();
                        if(is_object($objPersonaEmpresaRol))
                        {
                            $intIdPerCliente    = $objPersonaEmpresaRol->getId();
                            $objPersona         = $objPersonaEmpresaRol->getPersonaId();
                            if(is_object($objPersona))
                            {
                                $strNombreCliente   = sprintf("%s",$objPersona);
                                $objCaractCodTmp    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneBy(array(
                                                                                            'descripcionCaracteristica' => 'CODIGO_TMP_PORTAL',
                                                                                            'estado'                    => self::$strEstadoActivo
                                                                                   ));
                                if(is_object($objCaractCodTmp))
                                {
                                    $objPerCaractsCodTmp = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                                                                             ->findBy(array(
                                                                                                "caracteristicaId"    => $objCaractCodTmp,
                                                                                                "personaEmpresaRolId" => $objPersonaEmpresaRol,
                                                                                                "estado"              => self::$strEstadoActivo
                                                                                           ));
                                    /**
                                     * Se eliminan códigos temporales activos generados anteriormente.
                                     * Este escenario puede ocurrir cuando se ha generado un código y el usuario aún no se haya 
                                     * autenticado en el portal
                                     */
                                    foreach($objPerCaractsCodTmp as $objPerCaractCodTmp)
                                    {
                                        $objPerCaractCodTmp->setEstado("Eliminado");
                                        $objPerCaractCodTmp->setFeUltMod(new \DateTime('now'));
                                        $this->emComercial->persist($objPerCaractCodTmp);
                                        $this->emComercial->flush();
                                    }

                                    $arrayParamClaveAleat   = array("intMinLongitudClave"   => 8,
                                                                    "intMaxLongitudClave"   => 15);
                                    $strCodTmpOlvidoPass    = $this->generarClaveAleatoria($arrayParamClaveAleat);
                                    if($strCodTmpOlvidoPass)
                                    {
                                        
                                        $arrayParamsCaracPortal         = array(
                                                                            "objPersonaEmpresaRol"  => $objPersonaEmpresaRol,
                                                                            "objCaracteristica"     => $objCaractCodTmp,
                                                                            "strValor"              => $strCodTmpOlvidoPass,
                                                                            "strUsrCreacion"        => self::$strUserPortal,
                                                                            "strIpClient"           => self::$strIpClientPortal
                                                                          );
                                        $this->ingresarPerCaracteristica($arrayParamsCaracPortal);
                                        
                                        /*
                                         * Se resetea la password del cliente en LDAP
                                         */
                                        $arrayDataWsLdap            = $arrayDataWs;
                                        $arrayDataWsLdap["data"]    = array(
                                                                            "uid"       => $intIdPerCliente,
                                                                            "atributos" => array(
                                                                                                    array("nombre"  => "userPassword",
                                                                                                           "valor"  => ""
                                                                                                    )
                                                                                                )
                                                                      );
                                        
                                        $arrayRespuestaWsResetPass  = $this->callWsLdapPortalNetlifecam($arrayDataWsLdap);

                                        if($arrayRespuestaWsResetPass["status"]=="OK")
                                        {
                                            $strToken       = $arrayRespuestaWsResetPass["token"];
                                            $strStatus      = "OK";
                                            $strMsjPortal   = "Se ha enviado el correo con el nuevo código temporal para el ingreso al portal";
                                            
                                            $this->emComercial->commit();

                                            
                                            
                                            $arrayParametrosEnvioMail   = array(
                                                                                "strUsrCreacion"                => self::$strUserPortal,
                                                                                "strIpClient"                   => self::$strIpClientPortal,
                                                                                "intIdPersonaEmpresaRol"        => $intIdPerCliente,
                                                                                "strAsunto"                     => "Bienvenido a NetlifeCam. "
                                                                                                                   ."Sigue las instrucciones para "
                                                                                                                   ."ingresar en el"
                                                                                                                   ." portal.",
                                                                                "strCodPlantilla"               => 'OLVPASS_NTLFCAM',
                                                                                "arrayDataMail"                 => array(
                                                                                                                            "strNombreCliente"     =>
                                                                                                                            $strNombreCliente,
                                                                                                                            "strUserPortal"        =>
                                                                                                                            $strCorreoUserPortal,
                                                                                                                            "strCodigoTmp"         =>
                                                                                                                            $strCodTmpOlvidoPass,
                                                                                                                   )
                                                                      );
                                            $this->enviarInformacionCorreoNetlifeCam($arrayParametrosEnvioMail);
                                        }
                                        else if( isset($arrayRespuestaWsResetPass["msjEx"]) && !empty($arrayRespuestaWsResetPass["msjEx"]) )
                                        {
                                            throw new \Exception($arrayRespuestaWsResetPass["msjEx"]);
                                        }
                                        else if( isset($arrayRespuestaWsResetPass["msj"]) && !empty($arrayRespuestaWsResetPass["msj"]) )
                                        {
                                            throw new \Exception($arrayRespuestaWsResetPass["msj"]);
                                        }
                                        else
                                        {
                                            throw new \Exception("Ha ocurrido un error inesperado");
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception("No se ha podido generar el código temporal"); 
                                    }
                                }
                                else
                                {
                                    throw new \Exception("No se ha podido obtener la característica CODIGO_TMP_PORTAL"); 
                                }
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el objeto persona con el id ".$objPersonaEmpresaRol->getId()); 
                            }
                        }
                        else
                        {
                            throw new \Exception("No se ha podido obtener el objeto del cliente de la caracteristica con id"
                                                 .$objPerCaractUserPortal->getId()); 
                        }
                    }
                    else
                    {
                        $strMsjPortal = "El correo ingresado no está asociado a ningún cliente.";
                    }
                }
                else
                {
                    throw new \Exception("No se ha podido obtener la característica USUARIO_PORTAL");
                }
            }
            else
            {
                throw new \Exception("No se ha enviado el parámetro requerido idPer");
            } 
        } 
        catch (\Exception $e) 
        {
            $strMsjPortal = self::$strMsjGeneralPortal;
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->olvidoPassCliente',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        $arrayRespuesta["status"]   = $strStatus;
        $arrayRespuesta["msj"]      = $strMsjPortal;
        $arrayRespuesta["token"]    = $strToken;
        return $arrayRespuesta;
    }
    
    /**
     * Función que insertar un registro en la info_persona_empresa_rol_carac
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-06-2017
     * 
     * @param array $arrayParametros
     *                              [
     *                                  "objPersonaEmpresaRol"  => objeto persona empresa rol
     *                                  "objCaracteristica"     => objeto característica
     *                                  "strValor"              => valor de la característica asociada a la persona empresa rol
     *                                  "strUsrCreacion"        => usuario de creación
     *                                  "strIpClient"           => ip
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
        $this->emComercial->persist($objPersonaEmpresaRolCaract);
        $this->emComercial->flush();
        
        return $objPersonaEmpresaRolCaract;
    }
    
    
    /**
     * Función que sirve para actualizar el detalle que indica que la cámara ha empezado a grabar
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 02-07-2017
     * 
     * @param array $arrayData
     *                          [
     *                              "idElem"       => id del elemento cámara
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "status"    => "OK" o "ERROR"
     *                                  "msj"       => mensaje de información 
     *                              ]
     * 
     */
    public function actualizarEstadoGrabacion($arrayData)
    {
        $arrayRespuesta['status']       = "ERROR";
        $arrayRespuesta['msj']          = "";
        $this->emInfraestructura->beginTransaction();
        try
        {
            $intIdElementoCam               = $arrayData["idElem"] ? $arrayData["idElem"] : 0;
            $strEstadoActualGrabacionCam    = $arrayData["estadoActualGrab"] ? $arrayData["estadoActualGrab"] : 0;
            $strEstadoNuevoGrabacionCam     = $arrayData["estadoNuevoGrab"] ? $arrayData["estadoNuevoGrab"] : 0;
            
            if(isset($intIdElementoCam) && !empty($intIdElementoCam) && isset($strEstadoActualGrabacionCam) && !empty($strEstadoActualGrabacionCam) 
                && isset($strEstadoNuevoGrabacionCam) && !empty($strEstadoNuevoGrabacionCam))
            {
                $objElementoCam = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoCam);
                
                if(is_object($objElementoCam))
                {
                    $strNombreDetalle       = 'ESTADO_GRABACION_CAMARA';
                    $objDetalleCamGrabando  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->findOneBy(array("elementoId"     => $intIdElementoCam,
                                                                                        "detalleNombre"  => $strNombreDetalle,
                                                                                        "detalleValor"   => $strEstadoActualGrabacionCam,
                                                                                        "estado"         => self::$strEstadoActivo));
                    if(is_object($objDetalleCamGrabando))
                    {
                        $objDetalleCamGrabando->setDetalleValor($strEstadoNuevoGrabacionCam);
                        $this->emInfraestructura->persist($objDetalleCamGrabando);
                        $this->emInfraestructura->flush();
                        $arrayRespuesta['msj']  = "Detalle de estado de grabación de cámara actualizado correctamente";
                        
                    }
                    else
                    {
                        $arrayRespuesta['msj']  = "No existe detalle de estado de grabación de cámara con el valor ".$strEstadoActualGrabacionCam
                                                  ." por actualizar";
                    }        
                    $this->emInfraestructura->commit();
                    $arrayRespuesta['status']   = "OK";
                }
                else
                {
                    throw new \Exception("No se encontro el objeto para el idElem enviado ".$intIdElementoCam);
                }
            }
            else
            {
                throw new \Exception(self::$strMsjParamsMinimos."idElem=".$intIdElementoCam.
                                     " estadoActualGrabacion=".$strEstadoActualGrabacionCam.
                                     " estadoNuevoGrabacion=".$strEstadoNuevoGrabacionCam);
            }
        }
        catch (\Exception $e) 
        {
            $arrayRespuesta['msj'] = self::$strMsjGeneralPortal;
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
                $this->emInfraestructura->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'PortalNetlifeCamService->actualizarEstadoGrabacion',
                                            $e->getMessage(),
                                            self::$strUserPortal,
                                            self::$strIpClientPortal);
        }
        return $arrayRespuesta;
    }
    
    /**
     * Función que sirve para verificar si se puede acceder a la cámara.
     * Primero se verificará si se tiene acceso al ddns y luego se verificará si los usuarios con rol Administrator y Visitor 
     * fueron configurados correctamente en la cámara
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-07-2017
     * 
     * @param array $arrayParametros
     *                          [
     *                              "strDDNSCam"        => DDNS de la cámara,
     *                              "strUserAdminCam"   => usuario con rol Administrator de la cámara,
     *                              "strPassAdminCam"   => contraseña del usuario con rol Administrator de la cámara
     *                              "strUserVisitorCam" => usuario con rol Visitor de la cámara,
     *                              "strPassVisitorCam" => contraseña del usuario con rol Visitor de la cámara
     *                          ]
     * 
     * @return array $arrayRespuesta
     *                              [
     *                                  "strStatus"    => "OK" o "ERROR"
     *                                  "strMsj"       => mensaje de información 
     *                              ]
     * 
     */
    public function verificarConfiguracionCamara($arrayParametros)
    {
        $arrayRespuesta["strStatus"]    = "ERROR";
        $arrayRespuesta["strMsj"]       = "";
        
        $strDDNSCam         = $arrayParametros["strDDNSCam"];
        $strUserAdminCam    = $arrayParametros["strUserAdminCam"];
        $strPassAdminCam    = $arrayParametros["strPassAdminCam"];
        $strUserVisitorCam  = $arrayParametros["strUserVisitorCam"];
        $strPassVisitorCam  = $arrayParametros["strPassVisitorCam"];
        
        if(isset($strDDNSCam) && !empty($strDDNSCam) 
            && isset($strUserAdminCam) && !empty($strUserAdminCam) && isset($strPassAdminCam) && !empty($strPassAdminCam)
            && isset($strUserVisitorCam) && !empty($strUserVisitorCam) && isset($strPassVisitorCam) && !empty($strPassVisitorCam))
        {
            $boolUrlDDNSOK = $this->isUrlOK($strDDNSCam);
            if($boolUrlDDNSOK)
            {
                $strUrlAdmin    = $strDDNSCam."/check_user.cgi?user=".$strUserAdminCam."&pwd=".$strPassAdminCam;
                $boolUrlAdmin   = $this->isUrlOK($strUrlAdmin);
                
                $strUrlVisitor  = $strDDNSCam."/check_user.cgi?user=".$strUserVisitorCam."&pwd=".$strPassVisitorCam;
                $boolUrlVisitor = $this->isUrlOK($strUrlVisitor);
                
                if($boolUrlAdmin && $boolUrlVisitor)
                {
                    $arrayRespuesta["strStatus"]    = "OK";
                    $arrayRespuesta["strMsj"]       = "DDNS y Usuarios configurados en la cámara de manera correcta";
                }
                else
                {
                    if(!$boolUrlAdmin)
                    {
                        $arrayRespuesta["strMsj"]   = "No se ha configurado correctamente el usuario con rol Administrator";
                    }
                    else
                    {
                        $arrayRespuesta["strMsj"]   = "No se ha configurado correctamente el usuario con rol Visitor";
                    }
                }
            }
            else
            {
                $arrayRespuesta["strMsj"] = "No se ha configurado correctamente el DDNS en la cámara";
            }
        }
        else
        {
            $arrayRespuesta["strMsj"] = "No se han enviado todos los parámetros necesarios de verificación";
        }
        return $arrayRespuesta;
    }

    
    /**
     * Función que sirve para verificar si se puede o no acceder a una url
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-07-2017
     * 
     * @param array "$strUrl"   => url que desea probarse
     * 
     * @return boolean 
     * 
     */
    public function isUrlOK($strUrl = null)
    {
        $boolUrlOK = false;
        if(!empty($strUrl))
        {
            $curl = curl_init($strUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_exec($curl);

            //Obtener el código de respuesta
            $mixHttpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            //cerrar conexión
            curl_close($curl);

            //Aceptar sólo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
            $arrayAcceptedResponse = array(200, 301, 302);
            if(in_array($mixHttpcode, $arrayAcceptedResponse))
            {
                $boolUrlOK = true;
            }
        }
        return $boolUrlOK;
    }
}
