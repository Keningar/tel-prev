<?php

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\seguridadBundle\Service\TokenValidatorService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Clase que implementa metodos a utilizarse para el producto Wifi
 * 
 * @author Veronica Carrasco <vcarrasco@telconet.ec>
 */
class WifiService
{
 
    /**
     * Código de respuesta: Respuesta valida.
     */
    private static $strStatusOK = 200;
    
    /**
     * Bandera true
     */
    private static $booleanTrue = true;
    
    /**
     * Bandera false
     */
    private static $booleanFalse = false;
    
    /**
     * Nombre de la app a consumir
     */
    private static $strNombreAppWsToken = "Telcos";
    
    /**
     * Nombre de la app que consume el Web Service
     */
    private static $strGatewayWsToken = "Telcos";
    
    /**
     * Nombre del archivo que contiene el web service para las consultas de Netlifecam  
     */
    private static $strServiceWsToken = "TecnicoWSController";
    
    /**
     * Nombre de la función principal del archivo del Web Service usado para Netlifecam 
     */
    private static $strMethodWsToken  = "procesarAction";
    
    /**
     * Nombre del usuario para realizar consultas al Web Service
     */
    private static $strUserWsToken   = "Telcos";
    
    /**
     * Objeto rest client
     */
    private $restClient;
    
    /**
     * Objeto Mailer
     */
    private $mailer;
    
    /**
     * Objeto Entity Manager Comercial
     */
    private $emComercial;
        
    /**
     * Objeto Entity Manager General
     */
    private $emGeneral;
    
    /**
     * Objeto referencia esquema comunicaciones
     */
    private $emComunicacion;
    
    /**
     * Objeto referencia esquema seguridad
     */
    private $emSeguridad;
    
    /**
     * String URL Carga de Usuarios para Wifi
     */
    private $strURLWifiCertNetlifezone;
    
    /**
     * Bandera que indica si debe verificarse el dominio SSL
     */
    private $boolCURLOPT_SSL_VERIFYPEER;
    
    /**
     *
     * @var serviceUtil 
     */
    private $serviceUtil; 
    
    /**
     *
     * @var serviceTecnico 
     */
    private $serviceTecnico;
    
    /**
     * service $serviceTokenValidator
     */
    private $serviceTokenValidator;
    
    /**
     * service $strUsrComercial
     */
    private $strUsrComercial;
    
    /**
     * service $strPassComercial
     */
    private $strPassComercial;
    
    /**
     * service $strDns
     */
    private $strDns;
    
    function setDependencies(ContainerInterface $container)
    {
        $this->container        = $container;
        $this->emComercial      = $this->container->get('doctrine')->getManager('telconet');
        $this->emGeneral        = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emComunicacion   = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emSeguridad      = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->restClient       = $container->get('schema.RestClient');
        $this->mailer           = $container->get('mailer');
        $this->templating       = $container->get('templating');
        $this->strURLWifiCertNetlifezone  = $container->getParameter('wifi.ws_cert_netlifezone');
        $this->boolCURLOPT_SSL_VERIFYPEER = $container->getParameter('wifi.CURLOPT_SSL_VERIFYPEER');
        $this->serviceUtil                = $container->get('schema.Util');
        $this->serviceTecnico             = $container->get('tecnico.InfoServicioTecnico');
        $this->serviceTokenValidator      = $container->get('seguridad.TokenValidator');
        $this->strUsrComercial            = $container->getParameter('user_comercial');
        $this->strPassComercial           = $container->getParameter('passwd_comercial');
        $this->strDns                     = $container->getParameter('database_dsn');
    }
    
    /**
     * processUserWifi
     * 
     * Metodo que llama al servicio para Habilitar/Deshabilitar usuarios en Portal Cautivo
     * 
     * @param type $ssid SSID del servicio texto plano
     * @param type $user Usuario del Servicio en texto plano
     * @param type $password Clave del servcio en texto plano
     * @param type $tipoServicio Entero que indica el tipo de servicio a ejecutar
     *                           0 : Actualizar/Cancelar usuario (Cambio de password)
     *                           1 : Deshabilitar un usuario (No cambiar password)
     * @return type JSON de respuesta del servicio para cargar usuarios en portal cautivo
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.0 12-06-2016
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 20-08-2018   se cambia proceso, se utiliza web service de IT para las transacciones NETLIFEZONE
     * @since 1.0
     */
    public function processUserWifi($strData)
    {   
        $arrayOptions   = array(CURLOPT_SSL_VERIFYPEER => $this->boolCURLOPT_SSL_VERIFYPEER);
        $arrayResponse  = $this->restClient->postJSON($this->strURLWifiCertNetlifezone, $strData, $arrayOptions);
        $arrayRespuesta = array();
        if($arrayResponse['status'] == static::$strStatusOK && $arrayResponse['result'] != static::$booleanFalse)
        {
            $arrayResult    = json_decode($arrayResponse['result'],true);
            if ($arrayResult['success'] == static::$booleanTrue)
            {
                $arrayRespuesta['status']  = "OK";
                $arrayRespuesta['mensaje'] = $arrayResult['msg'];
            }
            else
            {
                $arrayRespuesta['status']  = "ERROR";
                $arrayRespuesta['mensaje'] = "Error: ".$arrayResult['errors']['msg'];
            }
        }
        else
        {
            $arrayRespuesta['status'] = "ERROR";
            if($arrayResponse['status'] == "0")
            {
                $arrayRespuesta['mensaje'] = "No Existe Conectividad con el WS IT.";
            }
            else
            {
                $strMensajeError = 'ERROR';
                if(isset($arrayResponse['mensaje']) && !empty($arrayResponse['mensaje']))
                {
                    $strMensajeError = $arrayResponse['mensaje'];
                }

                $arrayRespuesta['mensaje']  = "Error de WS IT :".$strMensajeError;
            }
        }
        return $arrayRespuesta;
    }
    
    /**
     * generarPassword
     * 
     * Metodo que genera un string aleatorio que cumplen las siguientes condiciones:
     *                   Debe tener máximo 8 caracteres y máximo 15 caracteres
     *                   Debe incluir por lo menos 1 caracter en minúscula
     *                   Debe incluir por lo menos 1 caracter en mayúscula
     *                   Debe incluir por lo menos 1 de estos caracteres num&eacute;ricos: 0 1 2 3 4 5 6 7 8 9
     *                   Debe incluir por lo menos 1 de estos caracteres especiales:
     *                                   ! ~ # @ % &amp; * _ - + = { [ } ] | \ ; : < , > . ? / 
     * 
     * @param type $arrayParametros  
     *                              [
     *                                 strUsrCreacion    Usuario que genera la password
     *                                 strIpCreacion     Ip del usuario que genera la password
     *                              ]
     * @return Array $arrayRespuesta  Array con estado, mensaje y password como respuesta del proceso
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 17-08-2018
     * @since 1.0
     */
    public function generarPassword($arrayParametros)
    {
        $arrayRespuesta                = array();
        $arrayRespuesta['strStatus']   = "ERROR";
        $arrayRespuesta['strMensaje']  = "Error al generar la password";
        $arrayRespuesta['strPassword'] = "";
        $arrayCaracteres               = array();
        $strPassGene                   = "";
        $intLength                     = 4;
        $strChars                      = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        try
        {
            $strPassGene       = substr( str_shuffle( $strChars ), 0, $intLength );
            $arrayCaracteres[] = $strPassGene;
            $strChars          = "abcdefghijklmnopqrstuvwxyz";
            $intLength         = 1;
            $strPassGene       = substr( str_shuffle( $strChars ), 0, $intLength );
            $arrayCaracteres[] = $strPassGene;
            $strChars          = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $strPassGene       = substr( str_shuffle( $strChars ), 0, $intLength );
            $arrayCaracteres[] = $strPassGene;
            $strChars          = "0123456789";
            $strPassGene       = substr( str_shuffle( $strChars ), 0, $intLength );
            $arrayCaracteres[] = $strPassGene;
            $strChars          = "!#@%*_-+={}|:<,>.?";
            $strPassGene       = substr( str_shuffle( $strChars ), 0, $intLength );
            $arrayCaracteres[] = $strPassGene;
            shuffle($arrayCaracteres);
            $strPassGene       = implode("", $arrayCaracteres);
            $arrayRespuesta['strStatus']   = "OK";
            $arrayRespuesta['strMensaje']  = "Password generada correctamente";
            $arrayRespuesta['strPassword'] = $strPassGene;
        }
        catch (\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->generarPassword', 
                                            $objEx->getMessage(),
                                            $arrayParametros['strUsrCreacion'], 
                                            $arrayParametros['strIpCreacion']
                                           );
        }
        
        return $arrayRespuesta;
    }
        
    /**
     * activarServicioWifi
     * 
     * Metodo que permite activar el servicio de Netlife Zone
     * @param type array(empresaId, servicioId, cliente, ssid, strUsuario, strClave, usrCreacion, ipCreacion)
     * @return type array(success, msj)
     * 
     * @author Veronica Carrsco <vcarrasco@telconet.ec>
     * @version 1.0 02/07/2016
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 20/08/2018   Se reestructura proceso por nueva versión de netlifezone
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-12-2020 Se reutiliza metodo para nuevo producto SSID_MOVIL y se genera login_aux
     * @since 1.0
     */
    public function activarServicioWifi($arrayParametros)
    {
        $empresaId      = $arrayParametros["empresaId"];
        $servicioId     = $arrayParametros["servicioId"];
        $strUsuario     = $arrayParametros["strUsuario"];
        $strClave       = $arrayParametros["strClave"];
        $usrCreacion    = $arrayParametros["usrCreacion"];
        $ipCreacion     = $arrayParametros["ipCreacion"];
        $strNombres     = "";
        $strApellidos   = "";
        $strEmail       = "";
        $strCiudad      = "";
        $intIdPunto     = "";
        $strProductoAsunto = "";
        $strNombreCompleto = "";
        $arrayRespuesta    = array("strStatus"  => 'ERROR',
                                   "strMensaje" => 'Usuario nuevo no pudo ser procesado, favor notificar a sistemas.');
        $arrayContactosTelefonosMovilClaroPuntoSMS    = array();
        $arrayContactosTelefonosMovilMovistarPuntoSMS = array();
        $arrayContactosTelefonosMovilCntPuntoSMS      = array();
        $arrayContactosCorreosPuntoMail               = array();
        
        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            $strClaveCod = hash('sha256',$strClave,false);
            $objServicio = $this->emComercial
                                ->getRepository('schemaBundle:InfoServicio')
                                ->find($servicioId);
            if (!is_object($objServicio))
            {
                throw new \Exception("No se encontro información del servicio");
            }
            $objPunto      = $objServicio->getPuntoId();
            $objLogin      = $objPunto->getLogin();
            $objProducto   = $this->emComercial
                                  ->getRepository('schemaBundle:AdmiProducto')
                                  ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                    "empresaCod"    => $empresaId));
            if (!is_object($objProducto))
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");

            }
            
            if(is_object($objPunto))
            {
                $intIdPunto    = $objPunto->getId();
                $objPersonaRol = $objPunto->getPersonaEmpresaRolId();
                if(is_object($objPersonaRol))
                {
                    $objPersona = $objPersonaRol->getPersonaId();
                    if(is_object($objPersona))
                    {
                        $strNombres        = $objPersona->getNombres();
                        $strApellidos      = $objPersona->getApellidos();
                        $strNombreCompleto = $objPersona->__toString();
                    }
                }
                $objPuntoCobertura = $objPunto->getPuntoCoberturaId();
                if(is_object($objPuntoCobertura))
                {
                    $intIdOficina = $objPuntoCobertura->getOficinaId();
                    $objOficina   = $this->emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
                    if(is_object($objOficina))
                    {
                        $objCanton = $this->emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                        if(is_object($objCanton))
                        {
                            $strCiudad = $objCanton->getNombreCanton();
                        }
                    }
                }
            }

            //Consultar el tipo de contacto
            if($empresaId == "10")
            {
                $strTipoContacto   = "Contacto Tecnico";
                $arrayTipoContacto = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('PROYECTO SSID MOVIL',
                                                                'INFRAESTRUCTURA',
                                                                'SSID MOVIL',
                                                                'TIPO_CONTACTO_NOTIFICACIONES',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $empresaId);

                if(isset($arrayTipoContacto["valor1"]) && !empty($arrayTipoContacto["valor1"]))
                {
                    $strTipoContacto = $arrayTipoContacto["valor1"];
                }
            }

            $arrayContactosPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                     ->getArrayContactosPorPuntoYTipo($intIdPunto,$strTipoContacto);

            foreach($arrayContactosPunto as $arrayContacto)
            {
                $strEmail  = $arrayContacto['valor'];
                $arrayContactosCorreosPuntoMail[] = $arrayContacto['valor'];
            }

            $arrayParametrosToken                        = array();
            $arrayParametrosToken['strNombreAppWsToken'] = self::$strNombreAppWsToken;
            $arrayParametrosToken['strIpClient']         = $ipCreacion;
            $arrayParametrosToken['strGatewayWsToken']   = self::$strGatewayWsToken;
            $arrayParametrosToken['strServiceWsToken']   = self::$strServiceWsToken;
            $arrayParametrosToken['strMethodWsToken']    = self::$strMethodWsToken;
            $arrayParametrosToken['strUserWsToken']      = self::$strUserWsToken;            
            $arrayRespuestaToken                         = $this->generateTokenRequestWs($arrayParametrosToken);
            if($arrayRespuestaToken["strStatus"] != "OK")
            {
                throw new \Exception("No se logró generar token");
            }
            $arrayData     = array();
            $arrayData['username']   = $strUsuario;
            $arrayData['password']   = $strClave;
            $arrayData['email']      = $strEmail;
            $arrayData['first_name'] = $strNombres;
            $arrayData['last_name']  = $strApellidos;
            $arrayData['city']       = $strCiudad;

            if($empresaId == "10")
            {
                $strFirstName = "";
                $strLastName  = "";

                if(substr($strNombreCompleto, 0, 28))
                {
                    $strFirstName = substr($strNombreCompleto, 0, 28);
                }

                if(substr($strNombreCompleto, 29,57))
                {
                    $strLastName = substr($strNombreCompleto, 29,57);
                }

                $arrayData['first_name'] = $strFirstName;
                $arrayData['last_name']  = $strLastName;
            }

            $strData = json_encode(array('data'  => $arrayData,
                                         'op'    => 'create_user',
                                         'token' => $arrayRespuestaToken["strToken"]
                                        ));
   
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayRespuestaProcesarWifi = $this->processUserWifi($strData);
            if($arrayRespuestaProcesarWifi['status'] == "OK")
            {
                $this->serviceTecnico->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              'USUARIO_NZ', 
                                                                              $strUsuario, 
                                                                              $usrCreacion);
                $this->serviceTecnico->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              'PASSWORD_NZ', 
                                                                              $strClaveCod, 
                                                                              $usrCreacion);
                $objServicio->setEstado("Activo");
                $this->emComercial->persist($objServicio);            
                //Objeto historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setAccion("confirmarServicio");
                $objServicioHistorial->setObservacion("Se confirmo el servicio");
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($usrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($ipCreacion);
                $this->emComercial->persist($objServicioHistorial);

                $strProductoAsunto = "Netlife Zone";
                $strEmpresa        = "Netlife";
                $strPlantillaEmail = "ACTIVACION_NETW";
                $strRemitenteEmail = "notificacionesnetlife@netlife.info.ec";

                if($empresaId == "10")
                {
                    //Se genera login aux
                    $this->serviceTecnico->generarLoginAuxiliar($objServicio->getId());
                    $strProductoAsunto = "SSID_MOVIL";
                    $strEmpresa        = "Telconet";
                    $strPlantillaEmail = "ACT_SSID_MOVIL";
                    $strRemitenteEmail = "notificaciones_telcos@telconet.ec";
                }
                else
                {
                    //Obtenemos los datos de contacto del cliente al cual se enviaran las notificaciones
                    $arrayContactosTelefonosMovilClaroPunto    = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil Claro');
                    $arrayContactosTelefonosMovilMovistarPunto = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil Movistar');
                    $arrayContactosTelefonosMovilCntPunto      = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil CNT');

                    //Generamos los arreglos con la informacion de conctacto con el cliente
                    foreach ($arrayContactosTelefonosMovilClaroPunto as $contacto1) 
                    {
                        $arrayContactosTelefonosMovilClaroPuntoSMS[] = array('value'=>$contacto1['valor'],'smsbox'=>0);
                    }

                    foreach ($arrayContactosTelefonosMovilMovistarPunto as $contacto2) 
                    {
                        $arrayContactosTelefonosMovilMovistarPuntoSMS[] = array('value'=>$contacto2['valor'],'smsbox'=>1);
                    }

                    foreach ($arrayContactosTelefonosMovilCntPunto as $contacto3) 
                    {
                        $arrayContactosTelefonosMovilCntPuntoSMS[] = array('value'=>$contacto3['valor'],'smsbox'=>0);
                    }
                }
                
                // Enviamos correo
                $arrayParametros    = array('nombres'=>$strNombreCompleto,'usuario'=>$strUsuario,'clave'=>$strClave);
                $objEnvioPlantilla  = $this->container->get('soporte.EnvioPlantilla');
                $objEnvioPlantilla->generarEnvioPlantilla($strEmpresa.' ha activado tu servicio '.".$strProductoAsunto.".'. Bienvenido', 
                                                          $arrayContactosCorreosPuntoMail, 
                                                          $strPlantillaEmail, 
                                                          $arrayParametros , 
                                                          '',
                                                          '',
                                                          '',
                                                          null,
                                                          false,
                                                          $strRemitenteEmail);

                if($empresaId == "18")
                {
                    // Enviamos SMS
                    $strMensaje             = "Se ha activado su servicio ".$strProductoAsunto.". Sus credenciales son Usuario: ".
                                               $strUsuario . " Clave: ".$strClave;
                    $objServiceSMS          = $this->container->get('comunicaciones.SMS'); 
                    $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilClaroPuntoSMS, 3, 5);
                    $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilMovistarPuntoSMS, 3, 5);
                    $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilCntPuntoSMS, 3, 5);
                }

                // Guardamos la informacion de activacion
                $this->emComercial->flush();
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                }
                $arrayRespuesta['strStatus']  = "OK";
                $arrayRespuesta['strMensaje'] = "OK";
            }
            else
            {
                $arrayRespuesta['strMensaje']  = "Usuario nuevo no pudo ser procesado. ". $arrayRespuestaProcesarWifi['mensaje'];
            }
        }
        catch(\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->activarServicioWifi', 
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'], 
                                            $arrayParametros['ipCreacion']
                                           );
            $arrayRespuesta['strMensaje'] = 'Usuario nuevo no pudo ser procesado, favor notificar a sistemas.';
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
        }
        $this->emComercial->getConnection()->close();
        return $arrayRespuesta;
    }
    
    /**
     * generarParametrosTokenWifi
     * 
     * Metodo que permite generar array con información utilizada en la generación de Tokens utilizado en el consumo de webService de IT
     * @return type array $arrayParametrosToken
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20/08/2018
     * @since 1.0
     */
    public function generarParametrosTokenWifi()
    {
        $arrayParametrosToken                        = array();
        $arrayParametrosToken['strNombreAppWsToken'] = self::$strNombreAppWsToken;
        $arrayParametrosToken['strIpClient']         = '127.0.0.1';
        $arrayParametrosToken['strGatewayWsToken']   = self::$strGatewayWsToken;
        $arrayParametrosToken['strServiceWsToken']   = self::$strServiceWsToken;
        $arrayParametrosToken['strMethodWsToken']    = self::$strMethodWsToken;
        $arrayParametrosToken['strUserWsToken']      = self::$strUserWsToken;   
        return $arrayParametrosToken;
    }
    
    /**
     * cambiarPasswordWifi
     * 
     * Metodo que permite cambiar las credenciales para el servicio de Netlife Zone
     * 
     * @param type $arrayParametros [
     *                                strEmpresaCod  Código de empresa
     *                                passA          Clave anterior del servicio en SHA256
     *                                passN          Clave nueva a utilizar en el servicio
     *                                usuario        Usuario de servicio
     *                                usrCreacion    Usuario de ejecución del proceso
     *                                ipCreacion     Ip de ejecución del proceso
     *                              ]
     * @return type array(success, msj)
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20/08/2018
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-10-2019 Se corrige el envío de la clave en el asunto del correo
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 04-12-2020 Se reutiliza metodo para nuevo producto SSID_MOVIL
     * @since 1.1
     */
    public function cambiarPasswordWifi($arrayParametros)
    {
        $strEmpresaCod      = $arrayParametros["strEmpresaCod"];
        $strPasswordA       = $arrayParametros["passA"];
        $strPasswordN       = $arrayParametros["passN"];
        $strUsuario         = $arrayParametros["usuario"];
        $strUsrCreacion     = $arrayParametros["usrCreacion"];
        $strIpCreacion      = $arrayParametros["ipCreacion"];
        $strAsuntoProducto  = "";
        $intIdPunto         = "";
        $strNombreCompleto  = "";
        $arrayRespuesta = array("strStatus"  => 'ERROR',
                                "strMensaje" => 'Usuario nuevo no pudo ser procesado, favor notificar a sistemas.');
        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                               "empresaCod"    => $strEmpresaCod));
            if (!is_object($objProducto))
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");

            }
            
            $arrayParametrosProdCaract = array("strProceso" => 'NETLIFEZONE',
                                               "strValor"   => $strUsuario);
            
            $objServicioProdCaract = $this->serviceTecnico
                                          ->getServicioProductoCaracteristica(null,
                                                                              'USUARIO_NZ',
                                                                              $objProducto,
                                                                              $arrayParametrosProdCaract);
            if(!is_object($objServicioProdCaract))
            {
                throw new \Exception("Error: El usuario ó password ingresados son incorrectos.");
            }
            
            $strClaveCod   = hash('sha256',$strPasswordN,false);
            $intServicioId = $objServicioProdCaract->getServicioId();
            $objServicio   = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
            if (!is_object($objServicio))
            {
                throw new \Exception("No se encontro información del servicio");
            }
            if ($objServicio->getEstado() != "Activo")
            {
                throw new \Exception("Error: El usuario ó password ingresados son incorrectos.");
            }
            $objServicioInternet = $this->emComercial
                                        ->getRepository('schemaBundle:InfoServicio')
                                        ->obtieneProductoInternetxPunto($objServicio->getPuntoId()->getId());
            if($objServicioInternet == null)
            {
                throw new \Exception("Error: El cliente no se encuentra Activo actualemente, no es posible procesar cambio de clave.");
            }
            else
            {
                if ($objServicioInternet->getEstado()!='Activo')
                {
                    throw new \Exception("Error: El usuario ó password ingresados son incorrectos.");
                }
            }
            $objPunto    = $objServicio->getPuntoId();
            $objLogin    = $objPunto->getLogin();
            
            if(is_object($objPunto))
            {
                $intIdPunto    = $objPunto->getId();
                $objPersonaRol = $objPunto->getPersonaEmpresaRolId();
                if(is_object($objPersonaRol))
                {
                    $objPersona = $objPersonaRol->getPersonaId();
                    if(is_object($objPersona))
                    {
                        $strNombres        = $objPersona->getNombres();
                        $strApellidos      = $objPersona->getApellidos();
                        $strNombreCompleto = $objPersona->__toString();
                    }
                }
            }
            
            $objServicioProdCaractPw = $this->serviceTecnico
                                          ->getServicioProductoCaracteristica($objServicio,
                                                                              'PASSWORD_NZ',
                                                                              $objProducto);
            if(!is_object($objServicioProdCaractPw))
            {
                throw new \Exception("No existe la caracteristica Password en Servicio Producto Caracteristica");
            }
            if ($strPasswordA != $objServicioProdCaractPw->getValor())
            {
                throw new \Exception("Error: El usuario ó password ingresados son incorrectos.");
            }
            
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayParametrosToken                        = array();
            $arrayParametrosToken['strNombreAppWsToken'] = self::$strNombreAppWsToken;
            $arrayParametrosToken['strIpClient']         = $strIpCreacion;
            $arrayParametrosToken['strGatewayWsToken']   = self::$strGatewayWsToken;
            $arrayParametrosToken['strServiceWsToken']   = self::$strServiceWsToken;
            $arrayParametrosToken['strMethodWsToken']    = self::$strMethodWsToken;
            $arrayParametrosToken['strUserWsToken']      = self::$strUserWsToken;            
            $arrayRespuestaToken                         = $this->generateTokenRequestWs($arrayParametrosToken);
            if($arrayRespuestaToken["strStatus"] != "OK")
            {
                throw new \Exception("No se logró generar token");
            }
            $strData = json_encode(array('data'   => array('username' => $strUsuario, 
                                                           'password' => $strPasswordN),
                                         'op'     => 'change_password',
                                         'token'  => $arrayRespuestaToken["strToken"]
                                        ));
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayRespuestaProcesarWifi = $this->processUserWifi($strData);

            $strAsuntoProducto = "Netlife Zone";
            $strPlantillaEmail = "CAMBIOPA_NETW";
            $strRemitenteEmail = "notificacionesnetlife@netlife.info.ec";

            if($strEmpresaCod == "10")
            {
                $strAsuntoProducto = "SSID_MOVIL";
                $strPlantillaEmail = "ACT_SSID_MOVIL";
                $strRemitenteEmail = "notificaciones_telcos@telconet.ec";
            }

            if($arrayRespuestaProcesarWifi['status'] == "OK")
            {
                $objServicioProdCaractPw->setEstado('Eliminado');
                $objServicioProdCaractPw->setUsrUltMod($strUsrCreacion);
                $objServicioProdCaractPw->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($objServicioProdCaractPw);
                //Objeto historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion("Se actualizo la clave del servicio ".$strAsuntoProducto);
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->serviceTecnico->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              'PASSWORD_NZ', 
                                                                              $strClaveCod, 
                                                                              $strUsrCreacion);

                if($strEmpresaCod == "18")
                {
                    //Obtenemos los datos de contacto del cliente al cual se enviaran las notificaciones
                    $arrayContactosTelefonosMovilClaroPunto    = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil Claro');
                    $arrayContactosTelefonosMovilMovistarPunto = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil Movistar');
                    $arrayContactosTelefonosMovilCntPunto      = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil CNT');
                

                    $arrayContactosTelefonosMovilClaroPuntoSMS    = array();
                    $arrayContactosTelefonosMovilMovistarPuntoSMS = array();
                    $arrayContactosTelefonosMovilCntPuntoSMS      = array();

                    //Generamos los arreglos con la informacion de conctacto con el cliente
                    foreach ($arrayContactosTelefonosMovilClaroPunto as $contacto1) 
                    {
                        $arrayContactosTelefonosMovilClaroPuntoSMS[] = array('value'=>$contacto1['valor'],'smsbox'=>0);
                    }

                    foreach ($arrayContactosTelefonosMovilMovistarPunto as $contacto2) 
                    {
                        $arrayContactosTelefonosMovilMovistarPuntoSMS[] = array('value'=>$contacto2['valor'],'smsbox'=>1);
                    }

                    foreach ($arrayContactosTelefonosMovilCntPunto as $contacto3) 
                    {
                        $arrayContactosTelefonosMovilCntPuntoSMS[] = array('value'=>$contacto3['valor'],'smsbox'=>0);
                    }
                }

                $arrayContactosCorreosPuntoMail = array();

                //Consultar el tipo de contacto
                if($strEmpresaCod == "10")
                {
                    $strTipoContacto   = "Contacto Tecnico";
                    $arrayTipoContacto = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne('PROYECTO SSID MOVIL',
                                                                    'INFRAESTRUCTURA',
                                                                    'SSID MOVIL',
                                                                    'TIPO_CONTACTO_NOTIFICACIONES',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $strEmpresaCod);

                    if(isset($arrayTipoContacto["valor1"]) && !empty($arrayTipoContacto["valor1"]))
                    {
                        $strTipoContacto = $arrayTipoContacto["valor1"];
                    }
                }

                $arrayContactosPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                         ->getArrayContactosPorPuntoYTipo($intIdPunto,$strTipoContacto);

                foreach($arrayContactosPunto as $arrayContacto)
                {
                    $arrayContactosCorreosPuntoMail[] = $arrayContacto['valor'];
                }

                // Enviamos correo
                $arrayParametros    = array('nombres' => $strNombreCompleto,
                                            'usuario' => $strUsuario,
                                            'clave'   => $strPasswordN);

                $objEnvioPlantilla  = $this->container->get('soporte.EnvioPlantilla'); 
                $objEnvioPlantilla->generarEnvioPlantilla(  'Cambio de Clave : Servicio '.$strAsuntoProducto, 
                                                            $arrayContactosCorreosPuntoMail, 
                                                            $strPlantillaEmail, 
                                                            $arrayParametros , 
                                                            '','','', null, false,
                                                            $strRemitenteEmail);
                
                if($strEmpresaCod == "18")
                {
                    // Enviamos SMS
                    $strMensaje = "Se ha generado una nueva clave para su servicio ".$strAsuntoProducto.". Sus credenciales son Usuario: "
                                   .$strUsuario . " Clave: ".$strPasswordN;

                    $objServiceSMS          = $this->container->get('comunicaciones.SMS');
                    $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilClaroPuntoSMS, 3, 5);
                    $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilMovistarPuntoSMS, 3, 5);
                    $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilCntPuntoSMS, 3, 5);
                }

                // Guardamos la informacion de activacion
                $this->emComercial->flush();
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                }
                $arrayRespuesta['strStatus']  = "OK";
                $arrayRespuesta['strMensaje'] = "Se realizó la actualización de la clave correctamente.";
            }
            else
            {
                $booleanValidaRespuesta = strpos($arrayRespuestaProcesarWifi['mensaje'], 'Error:');
                if ($booleanValidaRespuesta !== false)
                {
                    $arrayRespuesta['strMensaje'] = $arrayRespuestaProcesarWifi['mensaje'];
                }
                else
                {
                    throw new \Exception($arrayRespuestaProcesarWifi['mensaje']);
                }
            }
        }
        catch(\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->recuperarCredencialesServicioWifi', 
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'], 
                                            $arrayParametros['ipCreacion']
                                           );
            $booleanValidaRespuesta = strpos($objEx->getMessage(), 'Error:');
            if ($booleanValidaRespuesta !== false)
            {
                $arrayRespuesta['strMensaje'] = $objEx->getMessage();
            }
            else
            {
                $arrayRespuesta['strMensaje'] = 'Error: El proceso no se pudo completar correctamente, por favor intentelo mas tarde.';
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
        }
        
        $this->emComercial->getConnection()->close();
        
        return $arrayRespuesta;
    }
    
    /**
     * resetearPasswordWifi
     * 
     * Metodo que permite resetear las credenciales para el servicio de Netlife Zone
     * 
     * @param type $arrayParametros [
     *                                strEmpresaCod          Código de empresa
     *                                numeroIdentificacion   Número de identificación de cliente
     *                                correo                 Correo electronico del cliente
     *                                usrCreacion            Usuario de ejecución del proceso
     *                                ipCreacion             Ip de ejecución del proceso
     *                              ]
     * @return type array(success, msj)
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20/08/2018
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 21-12-2020 - Se agrega logica de activacion de SSID_MOVIL
     * @since 1.10
     */
    public function resetearPasswordWifi($arrayParametros)
    {
        $strEmpresaCod  = $arrayParametros["strEmpresaCod"];
        $strUsrCreacion = $arrayParametros["usrCreacion"];
        $strIpCreacion  = $arrayParametros["ipCreacion"];
        $arrayRespuesta = array("strStatus"  => 'ERROR',
                                "strMensaje" => 'El reseteo no pudo ser procesado, favor notificar a sistemas.');
        try
        {
            //validar información
            $arrayRespuestaValidaInfo = $this->validarInformacionReseteoPassword($arrayParametros);
            if ($arrayRespuestaValidaInfo['strStatus'] != "OK")
            {
                $arrayRespuesta['strMensaje'] = $arrayRespuestaValidaInfo['strMensaje'];
                throw new \Exception($arrayRespuestaValidaInfo['strMensaje']);
            }
            //generar credenciales
            $arrayParamGenerarCredenciales = array();
            $arrayParamGenerarCredenciales['strUsrCreacion'] = $strUsrCreacion;
            $arrayParamGenerarCredenciales['strIpCreacion']  = $strIpCreacion;
            $arrayParamGenerarCredenciales['intEmpresaId']   = $strEmpresaCod;
            $arrayParamGenerarCredenciales['strTipo']        = 'Resetear';
            $arrayParamGenerarCredenciales['intServicioId']  = $arrayRespuestaValidaInfo['objServicio']->getId();
            $arrayParamGenerarCredenciales['strActivacion']  = "N";
            $arrayRespGenerarCredenciales = $this->generarCredencialesWifi($arrayParamGenerarCredenciales);
            if ($arrayRespGenerarCredenciales['strStatus'] != "OK")
            {
                $arrayRespuesta['strMensaje'] = "Existieron problemas al generar las nuevas credenciales, por favor comuniquese con el administrador.";
                throw new \Exception($arrayRespuesta['strMensaje']);
            }
            //resetear password
            $arrayParamResetearPass = array();
            $arrayParamResetearPass["cliente"]     = array('nombres' => $arrayRespuestaValidaInfo['strCliente']);
            $arrayParamResetearPass["strClave"]    = $arrayRespGenerarCredenciales['arrayData']['strClave'];
            $arrayParamResetearPass["empresaId"]   = $strEmpresaCod;
            $arrayParamResetearPass["servicioId"]  = $arrayRespuestaValidaInfo['objServicio']->getId();
            $arrayParamResetearPass["strUsuario"]  = $arrayRespGenerarCredenciales['arrayData']['strUsuario'];
            $arrayParamResetearPass["ipCreacion"]  = $strIpCreacion;
            $arrayParamResetearPass["usrCreacion"] = $strUsrCreacion;
            $arrayRespuestaReseteo = $this->recuperarCredencialesServicioWifi($arrayParamResetearPass);
            if ($arrayRespuestaReseteo['strStatus'] != "OK")
            {
                $arrayRespuesta['strMensaje'] = "Existieron problemas al resetear las credenciales, por favor comuniquese con el administrador.";
                throw new \Exception($arrayRespuesta['strMensaje']);
            }
            $arrayRespuesta['strStatus'] = "OK";
        }
        catch(\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->resetearPasswordWifi', 
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'], 
                                            $arrayParametros['ipCreacion']
                                           );
        }
        return $arrayRespuesta;
    }
    
    /**
     * recuperarCredencialesServicioWifi
     * 
     * Metodo que permite recuperar las credenciales para el servicio de Netlife Zone
     * @param type $arrayParametros( empresaId, servicioId, cliente, strSsid, strUsuario, strClave, usrCreacion, ipCreacion)
     * @return type array(success, msj)
     * 
     * @author Veronica Carrsco <vcarrasco@telconet.ec>
     * @version 1.0 02/07/2016
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 20/08/2018    Se reestructura proceso por nueva versión de NETLIFEZONE
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 04-12-2020 Se reutiliza metodo para nuevo producto SSID_MOVIL.
     * @since 1.1
     */
    public function recuperarCredencialesServicioWifi($arrayParametros)
    {
        $empresaId          = $arrayParametros["empresaId"];
        $servicioId         = $arrayParametros["servicioId"];
        $cliente            = $arrayParametros["cliente"];
        $strUsuario         = $arrayParametros["strUsuario"];
        $strClave           = $arrayParametros["strClave"];
        $usrCreacion        = $arrayParametros["usrCreacion"];
        $ipCreacion         = $arrayParametros["ipCreacion"];
        $strAsuntoProducto  = "";
        $intIdPunto         = "";
        $strNombreCompleto  = "";
        $strNombres         = $cliente['nombres']." ".$cliente['apellidos'];
        $arrayRespuesta     = array("strStatus"  => 'ERROR',
                                    "strMensaje" => 'Usuario nuevo no pudo ser procesado, favor notificar a sistemas.');
        
        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            $strClaveCod = hash('sha256',$strClave,false);
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($servicioId);
            if (!is_object($objServicio))
            {
                throw new \Exception("No se encontro información del servicio");
            }
            $objPunto    = $objServicio->getPuntoId();

            if(is_object($objPunto))
            {
                $intIdPunto    = $objPunto->getId();
                $objPersonaRol = $objPunto->getPersonaEmpresaRolId();
                if(is_object($objPersonaRol))
                {
                    $objPersona = $objPersonaRol->getPersonaId();
                    if(is_object($objPersona))
                    {
                        $strNombreCompleto = $objPersona->__toString();
                    }
                }
            }

            $objLogin    = $objPunto->getLogin();
            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                               "empresaCod"    => $empresaId));
            if (!is_object($objProducto))
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");

            }
            $objServicioProdCaract = $this->serviceTecnico
                                          ->getServicioProductoCaracteristica($objServicio,
                                                                              'PASSWORD_NZ',
                                                                              $objProducto);
            if(!is_object($objServicioProdCaract))
            {
                $arrayRespuesta["strMensaje"] = "Error,No existe la caracteristica Password en Servicio Producto Caracteristica";
                return $arrayRespuesta;
            }        
        
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayParametrosToken                        = array();
            $arrayParametrosToken['strNombreAppWsToken'] = self::$strNombreAppWsToken;
            $arrayParametrosToken['strIpClient']         = $ipCreacion;
            $arrayParametrosToken['strGatewayWsToken']   = self::$strGatewayWsToken;
            $arrayParametrosToken['strServiceWsToken']   = self::$strServiceWsToken;
            $arrayParametrosToken['strMethodWsToken']    = self::$strMethodWsToken;
            $arrayParametrosToken['strUserWsToken']      = self::$strUserWsToken;            
            $arrayRespuestaToken                         = $this->generateTokenRequestWs($arrayParametrosToken);
            if($arrayRespuestaToken["strStatus"] != "OK")
            {
                throw new \Exception("No se logró generar token");
            }
            $strData = json_encode(array('data'   => array('username' => $strUsuario, 
                                                           'password' => $strClave),
                                         'op'    => 'change_password',
                                         'token' => $arrayRespuestaToken["strToken"]
                                        ));
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayRespuestaProcesarWifi = $this->processUserWifi($strData);
            if($arrayRespuestaProcesarWifi['status'] == "OK")
            {
                $strAsuntoProducto = "Netlife Zone";
                $strPlantillaEmail = "RECOVERPA_NETW";
                $strRemitenteEmail = "notificacionesnetlife@netlife.info.ec";

                if($empresaId == "10")
                {
                    $strAsuntoProducto = "SSID_MOVIL";
                    $strPlantillaEmail = "ACT_SSID_MOVIL";
                    $strRemitenteEmail = "notificaciones_telcos@telconet.ec";
                }

                $objServicioProdCaract->setEstado('Eliminado');
                $objServicioProdCaract->setUsrUltMod($usrCreacion);
                $objServicioProdCaract->setFeUltMod(new \DateTime('now'));
                $this->emComercial->persist($objServicioProdCaract);
                //Objeto historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion("Se actualizo la clave del servicio ".$strAsuntoProducto);
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($usrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($ipCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->serviceTecnico->ingresarServicioProductoCaracteristica($objServicio, 
                                                                              $objProducto, 
                                                                              'PASSWORD_NZ', 
                                                                              $strClaveCod, 
                                                                              $usrCreacion);

                if($empresaId == "18")
                {
                    //Obtenemos los datos de contacto del cliente al cual se enviaran las notificaciones
                    $arrayContactosTelefonosMovilClaroPunto    = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil Claro');
                    $arrayContactosTelefonosMovilMovistarPunto = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil Movistar');
                    $arrayContactosTelefonosMovilCntPunto      = $this->emComercial
                                                                      ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                      ->findContactosByPunto($objLogin , 'Telefono Movil CNT');
                
                    $arrayContactosTelefonosMovilClaroPuntoSMS    = array();
                    $arrayContactosTelefonosMovilMovistarPuntoSMS = array();
                    $arrayContactosTelefonosMovilCntPuntoSMS      = array();
                

                    //Generamos los arreglos con la informacion de conctacto con el cliente
                    foreach ($arrayContactosTelefonosMovilClaroPunto as $contacto1) 
                    {
                        $arrayContactosTelefonosMovilClaroPuntoSMS[] = array('value'=>$contacto1['valor'],'smsbox'=>0);
                    }

                    foreach ($arrayContactosTelefonosMovilMovistarPunto as $contacto2) 
                    {
                        $arrayContactosTelefonosMovilMovistarPuntoSMS[] = array('value'=>$contacto2['valor'],'smsbox'=>1);
                    }

                    foreach ($arrayContactosTelefonosMovilCntPunto as $contacto3) 
                    {
                        $arrayContactosTelefonosMovilCntPuntoSMS[] = array('value'=>$contacto3['valor'],'smsbox'=>0);
                    }
                }

                $arrayContactosCorreosPuntoMail = array();

                if($empresaId == "10")
                {
                    $strTipoContacto   = "Contacto Tecnico";
                    $arrayTipoContacto = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->getOne('PROYECTO SSID MOVIL',
                                                                    'INFRAESTRUCTURA',
                                                                    'SSID MOVIL',
                                                                    'TIPO_CONTACTO_NOTIFICACIONES',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $empresaId);

                    if(isset($arrayTipoContacto["valor1"]) && !empty($arrayTipoContacto["valor1"]))
                    {
                        $strTipoContacto = $arrayTipoContacto["valor1"];
                    }
                }

                $arrayContactosPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                         ->getArrayContactosPorPuntoYTipo($intIdPunto,$strTipoContacto);

                foreach($arrayContactosPunto as $arrayContacto)
                {
                    $arrayContactosCorreosPuntoMail[] = $arrayContacto['valor'];
                }

                // Enviamos correo
                $arrayParametros    = array('nombres' => $strNombreCompleto,
                                            'usuario' => $strUsuario,
                                            'clave'   => $strClave);

                $objEnvioPlantilla  = $this->container->get('soporte.EnvioPlantilla'); 
                $objEnvioPlantilla->generarEnvioPlantilla(  'Recuperación Clave : Servicio '.$strAsuntoProducto, 
                                                            $arrayContactosCorreosPuntoMail, 
                                                            $strPlantillaEmail, 
                                                            $arrayParametros , 
                                                            '','','', null, false,
                                                            $strRemitenteEmail);

                if($empresaId == "18")
                {
                    // Enviamos SMS
                    $strMensaje             = "Se ha generado una nueva clave para su servicio ".$strAsuntoProducto.". Sus credenciales son Usuario: "
                                              .$strUsuario . " Clave: ".$strClave;
                    $objServiceSMS          = $this->container->get('comunicaciones.SMS');
                    $strRespSendSMSClaro    = $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilClaroPuntoSMS, 3, 5);
                    $strRespSendSMSMovistar = $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilMovistarPuntoSMS, 3, 5);
                    $strRespSendSMSCNT      = $objServiceSMS->sendSMS($strMensaje, $arrayContactosTelefonosMovilCntPuntoSMS, 3, 5);
                }

                // Guardamos la informacion de activacion
                $this->emComercial->flush();
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->getConnection()->commit();
                }
                $arrayRespuesta['strStatus']  = "OK";
                $arrayRespuesta['strMensaje'] = "Se ejecutó el proceso correctamente.";
            }
            else
            {
                $arrayRespuesta['strMensaje']  = "Usuario no pudo ser procesado. ". $arrayRespuestaProcesarWifi['mensaje'];
            }
        }
        catch(\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->recuperarCredencialesServicioWifi', 
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'], 
                                            $arrayParametros['ipCreacion']
                                           );
            $arrayRespuesta['strMensaje'] = 'Usuario nuevo no pudo ser procesado, favor notificar a sistemas.';
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
        }
        
        $this->emComercial->getConnection()->close();
        
        return $arrayRespuesta;
    }
    
    /**
     * procesarOperacionesNetlifeWifi
     * 
     * Se reestructura proceso por nueva version de producto NETLIFEZONE,
     * método para gestionar operaciones de corte, reactivación y cancelación de servicio
     * 
     * @param  Array $arrayParametros [ 
     *                                  - intIdEmpresa      Identificador de empresa
     *                                  - intIdServicio     Identificador del servicio
     *                                  - intIdAccion       Identificador de la acción a ejecutar
     *                                  - strUsuario        Usuario que ejecuta proceso
     *                                  - strIpCliente      Ip de usuario que ejecuta el proceso
     *                                  - strEstado         Estado a registrar en el servicio procesado
     *                                  - strObservacion    Observación a registrar en el servicio procesado
     *                                  - strMetodoWs
     *                                ] 
     * @return String $strRespuesta
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-10-2019   
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-12-2020 Se reutiliza metodo para nuevo producto SSID_MOVIL
     * @since 1.1
     */
    public function procesarOperacionesNetlifeWifi($arrayParametros)
    {
        $intIdEmpresa   = $arrayParametros['intIdEmpresa'];
        $intIdServicio  = $arrayParametros['intIdServicio'];
        $intIdAccion    = $arrayParametros['intIdAccion'];
        $strUsrCreacion = $arrayParametros['strUsuario'];
        $strIpCreacion  = $arrayParametros['strIpCliente'];
        $strEstado      = $arrayParametros['strEstado'];
        $strObservacion = $arrayParametros['strObservacion'];
        $strMetodoWs    = $arrayParametros['strMetodoWs'];
        $strRespuesta   = "";
        $strProducto    = "";
        try
        {
            $strProducto = "Netlife Zone";

            if($intIdEmpresa == "10")
            {
                $strProducto = "SSID_MOVIL";
            }

            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!is_object($objServicio))
            {
                throw new \Exception("No se logró encontrar información del servicio");
            }
            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                  "empresaCod"    => $intIdEmpresa));
            if (!is_object($objProducto))
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");
            }
            $objAccion = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
            if (!is_object($objAccion) && $strMetodoWs != 'delete_user')
            {
                throw new \Exception("No se logró encontrar información de la acción a ejecutar");

            }
            $objServicioProdCaract   = $this->serviceTecnico->getServicioProductoCaracteristica($objServicio, 'USUARIO_NZ', $objProducto);
            if (!is_object($objServicioProdCaract))
            {
                throw new \Exception("No existe la caracteristica Usuario en Servicio Producto Caracteristica");

            }
            $strUsuario = $objServicioProdCaract->getValor();
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayParametrosToken                = $this->generarParametrosTokenWifi();
            $arrayParametrosToken['strIpClient'] = $strIpCreacion;
            $arrayRespuestaToken                 = $this->generateTokenRequestWs($arrayParametrosToken);
            if($arrayRespuestaToken["strStatus"] != "OK")
            {
                throw new \Exception("No se logró generar token");
            }
            $strData = json_encode(array('data'   => array('username' => $strUsuario),
                                         'op'     => $strMetodoWs,
                                         'token'  => $arrayRespuestaToken["strToken"]
                                        ));
            // Conexion al WS para carga de Usuarios provisto por Portal Cautivo
            $arrayRespuestaProcesarWifi = $this->processUserWifi($strData);
            if($arrayRespuestaProcesarWifi['status'] == "OK")
            {
                //servicio
                $objServicio->setEstado($strEstado);
                $this->emComercial->persist($objServicio);
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacion);
                $objServicioHistorial->setEstado($strEstado);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                if (is_object($objAccion))
                {
                    $objServicioHistorial->setAccion ($objAccion->getNombreAccion());
                }
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $strRespuesta = "OK";
            }
            else
            {
                throw new \Exception("Existieron errores al ejecutar Web Service: ".$arrayRespuestaProcesarWifi['mensaje']);
            }
        }
        catch(\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->procesarOperacionesNetlifeWifi', 
                                            $objEx->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strRespuesta = "Error : Se presentaron errores al ejecutar el proceso para servicios ".$strProducto.", favor comunicar a Sistemas.";
        }
        return $strRespuesta;
    }
    
    /**
     * generarCredencialesWifi
     * 
     * Metodo que permite generar credenciales para un usuario nuevo y/o 
     * actualizar credenciales para un usuario ya existente
     * 
     * @param type Array $arrayParametros
     *                                     [
     *                                       intEmpresaId      ID de empresa
     *                                       intServicioId     ID de Servicio
     *                                       arrayCliente      Objeto Cliente
     *                                       strTipo           Tipo de generacion de credenciales
     *                                       strUsrCreacion    Usuario de creación de las credenciales
     *                                       strIpCreacion     Ip de usuario de creación de las credenciales
     *                                     ]
     * 
     * @return array $arrayRespuesta
     * 
     * @author Veronica Carrsco <vcarrasco@telconet.ec>
     * @version 1.0 02/07/2016
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 17/08/2018    Se reestructura método por nueva versión de producto NETLIFEZONE
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 21-12-2020 - Se agrega logica de activacion de SSID_MOVIL
     * @since 1.1
     */
    public function generarCredencialesWifi($arrayParametros)
    {
        $strUsuario      = '';
        $strErrorArchivo = "N";
        $arrayRespuesta  = array();
        $arrayRespuesta  = array('strStatus' => 'ERROR', 
                                 'arrayData' => array('strUsuario' => '', 
                                                      'strClave'   => ''));
        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['intServicioId']);
            if (!is_object($objServicio))
            {
                throw new \Exception("No se logró encontrar información del servicio");
            }

            if($arrayParametros['intEmpresaId'] == "10" && $arrayParametros['strActivacion'] == "S")
            {
                $objDocumentoRelacion = $this->emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                             ->findOneBy(array('servicioId' => $arrayParametros['intServicioId'],
                                                                               'estado'     => "Activo"));

                if(!is_object($objDocumentoRelacion))
                {
                    $strErrorArchivo = "N";
                }
            }

            $objProducto = $this->emComercial
                                ->getRepository('schemaBundle:AdmiProducto')
                                ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                  "empresaCod"    => $arrayParametros['intEmpresaId']));
            if (!is_object($objProducto))
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");

            }
            switch($arrayParametros['strTipo'])
            {
                case 'Crear':
                    $objPunto = $objServicio->getPuntoId();
                    if(is_object($objPunto))
                    {
                        $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
                        if(is_object($objPersonaEmpresaRol))
                        {
                            $objPersona = $objPersonaEmpresaRol->getPersonaId();
                            if(is_object($objPersona))
                            {
                                $strIdentificacionCliente = $objPersona->getIdentificacionCliente();
                            }
                        }
                    }
                    
                    $strUsuario = $strIdentificacionCliente;
                    break;
                case 'Resetear':
                    $objServicioProdCaract   = $this->serviceTecnico
                                                   ->getServicioProductoCaracteristica($objServicio,
                                                                                       'USUARIO_NZ',
                                                                                       $objProducto);
                    if (!is_object($objServicioProdCaract))
                    {
                        throw new \Exception("No se logró encontrar caracteristica USUARIO");

                    }
                    $strUsuario = $objServicioProdCaract->getValor();
                    break;
                default:
                    $strUsuario = "";
                    break;
            }
            
            if (!empty($strUsuario))
            {
                $arrayRespuesta = array('strStatus' => 'OK', 
                                        'arrayData' => array('strUsuario' => $strUsuario, 
                                                             'strClave'   => ''));
            }
            
            if ($arrayRespuesta['strStatus'] == 'OK')
            {
                //Generamos una clave aleatoria que cumple con los parametros de claves de telcos
                $arrayRespuestaPassword = $this->generarPassword($arrayParametros);
                if ($arrayRespuestaPassword['strStatus'] == 'OK')
                {
                    $arrayRespuesta = array('strStatus' => 'OK',
                                            'arrayData'   => array('strUsuario' => $strUsuario, 
                                                                 'strClave'   => $arrayRespuestaPassword['strPassword']));
                }
                else
                {
                    $arrayRespuesta = array('strStatus' => 'ERROR',
                                            'arrayData' => array('strUsuario' => $strUsuario, 
                                                                 'strClave'   => ""));
                }
            }
        }
        catch (\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->generarCredencialesWifi', 
                                            $objEx->getMessage(),
                                            $arrayParametros['strUsrCreacion'], 
                                            $arrayParametros['strIpCreacion']
                                           );

            if($strErrorArchivo == "S")
            {
                $arrayRespuesta = array('strStatus'  => 'DOC-ERROR',
                                        'strMensaje' => 'Debe adjuntar el mapa de recorrido del cliente, para poder realizar la activación.',
                                        'arrayData'  => array('strUsuario' => '',
                                                             'strClave'   => ''));
            }
            else
            {
                $arrayRespuesta = array('strStatus' => 'ERROR',
                                        'arrayData' => array('strUsuario' => '',
                                                             'strClave'   => ''));
            }
        }
        return $arrayRespuesta;
    }
 
    /**
     * generateTokenRequestWs
     * 
     * Documentación para el método 'generateTokenRequestWs'.
     *
     * Función que genera el token que será enviado como parámetro al web service usado para las operaciones con el ws de TI
     * 
     * @param type array $arrayParametros [
     *                                      strNombreAppWsToken   => Nombre de app registrada en el sistema de token
     *                                      strIpClient           => IP que solicitará la petición al web service
     *                                      strGatewayWsToken     => Gateway registrado en el sistema de token
     *                                      strServiceWsToken     => Service registrado en el sistema de token
     *                                      strMethodWsToken      => Method registrado en el sistema de token
     *                                      strUserWsToken        => User registrado en el sistema de token
     *                                    ]
     * 
     * @return string $arrayResult['token']
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 20-08-2018
     */
    public function generateTokenRequestWs($arrayParametros)
    {
        $arrayRespuesta = array();
        $strToken       = "";
        $strStatus      = "";
        $arraySource    = array("name"          => $arrayParametros["strNombreAppWsToken"],//self::$strNombreAppWsToken
                                "originID"      => $arrayParametros["strIpClient"],
                                "tipoOriginID"  => "IP");
        $arrayResult    = $this->serviceTokenValidator->generateToken(  $arraySource, 
                                                                        $arrayParametros["strGatewayWsToken"],//self::$strGatewayWsToken
                                                                        $arrayParametros["strServiceWsToken"],//self::$strServiceWsToken
                                                                        $arrayParametros["strMethodWsToken"],//self::$strMethodWsToken
                                                                        $arrayParametros["strUserWsToken"]);//self::$strUserWsToken
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
     * validarInformacionReseteoPassword
     * 
     * Metodo que permite validar la información ingresada por el usuario NZ en la opción de reseteo de password
     * @param type array $arrayParametros 
     *                             [
     *                              strEmpresaCod     Código de la empresa
     *                              cedula            Número de identificacion ingresado por el usuario en portal NZ
     *                              correo            Correo electronico ingresado por usuario en portal NZ
     *                              strUsrCreacion    Usuario de creación
     *                              strIpCreacion     Ip de creación
     *                             ]
     * @return type array $arrayRespuesta 
     *                             [
     *                              strStatus    Estado de respuesta de validaciones de información
     *                              strMensaje   Mensaje de respuesta de validaciones de información
     *                             ]
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 03/09/2018
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 04-12-2020 Se reutiliza metodo para nuevo producto SSID_MOVIL
     * @since 1.0
     */
    public function validarInformacionReseteoPassword($arrayParametros)
    {
        $strNumeroIdentificacion = $arrayParametros["cedula"];
        $strCorreoElectronico    = $arrayParametros["correo"];
        $strEmpresaCod           = $arrayParametros["strEmpresaCod"];
        $strCorreValido          = "NO";
        $objServicioNetlifezone  = null;
        $strNombreCliente        = "";
        $strNombreProducto       = "";
        $arrayRespuesta          = array();
        $arrayRespuesta['strStatus']   = 'ERROR';
        $arrayRespuesta['strMensaje']  = 'La información ingresada no es correcta.';
        $arrayRespuesta['objServicio'] = null;
        try
        {
            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findOneBy(array("nombreTecnico" => "NETWIFI", 
                                                               "empresaCod"    => $strEmpresaCod));
            
            if (!is_object($objProducto))
            {
                throw new \Exception("No se logró encontrar información del producto NETWIFI");

            }
            
            //variables para conexion a la base de datos mediante conexion OCI
            $arrayOciCon = array();
            $arrayOciCon['user_comercial']   = $this->strUsrComercial;
            $arrayOciCon['passwd_comercial'] = $this->strPassComercial;
            $arrayOciCon['dsn']              = $this->strDns;
            $arrayParametrosCliente                      = array();
            $arrayParametrosCliente['strEmpresaCod']     = $strEmpresaCod;
            $arrayParametrosCliente['strIdentificacion'] = $strNumeroIdentificacion;
            $arrayParametrosCliente['ociCon']            = $arrayOciCon;
            $arrayRespuestaCliente = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->getRolClienteWs($arrayParametrosCliente);
            if(!empty($arrayRespuestaCliente['idPersonaRol']))
            {
                $objPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($arrayRespuestaCliente['idPersonaRol']);
                if (is_object($objPersonaEmpresaRol))
                {
                    $objPuntoCliente = $this->emComercial
                                            ->getRepository('schemaBundle:InfoPunto')
                                            ->findOneBy(array('personaEmpresaRolId' => $objPersonaEmpresaRol,
                                                              'estado'              => 'Activo'));
                    if (is_object($objPuntoCliente))
                    {
                        $arrayServiciosPunto = $this->emComercial
                                                    ->getRepository('schemaBundle:InfoServicio')
                                                    ->findBy(array( "puntoId" => $objPuntoCliente->getId(), "estado"=>"Activo"));
                        foreach($arrayServiciosPunto as $objServicioPunto)
                        {
                            if( is_object($objServicioPunto->getProductoId()) && $objServicioPunto->getProductoId()->getId() == $objProducto->getId())
                            {
                                $objServicioNetlifezone = $objServicioPunto;
                                $objPersona             = $objPersonaEmpresaRol->getPersonaId();
                                if(is_object($objPersona))
                                {
                                    $strNombreCliente = sprintf('%s', $objPersona);
                                }
                            }
                        }
                    }
                }
            }

            $strNombreProducto = "NETLIFEZONE";
            if($strEmpresaCod == "10")
            {
                $strNombreProducto = "SSID_MOVIL";
            }
            
            if (!is_object($objServicioNetlifezone))
            {
                throw new \Exception("No existe servicio ".$strNombreProducto." en el cliente consultado.");
            }
            
            $arrayContactosCorreosPunto = $this->emComercial
                                               ->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                               ->findContactosByPunto($objServicioNetlifezone->getPuntoId()->getLogin() , 'Correo Electronico');
            foreach ($arrayContactosCorreosPunto as $contacto4)
            {
                if ($contacto4['valor'] == $strCorreoElectronico)
                {
                    $strCorreValido = "SI";
                }
            }
            
            if ($strCorreValido == "SI")
            {
                $arrayRespuesta['strStatus']   = 'OK';
                $arrayRespuesta['strMensaje']  = 'La información ingresada es correcta.';
                $arrayRespuesta['objServicio'] = $objServicioNetlifezone;
                $arrayRespuesta['strCliente']  = $strNombreCliente;
            }
            
            
        }
        catch(\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'WifiService->validarInformacionReseteoPassword', 
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'], 
                                            $arrayParametros['ipCreacion']
                                           );
        }
        
        return $arrayRespuesta;
    }
}
