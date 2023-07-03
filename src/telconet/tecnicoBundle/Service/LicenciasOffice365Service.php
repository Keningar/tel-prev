<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Documentación para la clase 'LicenciasOffice365Service'.
 * 
 * Se realiza consumo Rest en este service
 *
 * Clase utilizada para manejar metodos que permiten realizar la generacion de licencias de productod Office 365
 *
 * @author Walther Joao Gaibor <wgaibor@telconet.ec>
 * @version 1.0 05-08-2016
 * 
 * @author Edgar Holguín <eholguin@telconet.ec>
 * @version 1.1 17-07-2018 Se agrega variable para envío de sms
 */
class LicenciasOffice365Service
{
    
    /**
     * Codigo de respuesta: Error en consumo
     */
    public static $TOKEN_ERROR = 500;
    
    private $emComercial;
    private $emInfraestructura;
    private $envioPlantilla;
    private $strUrlREST;
    /**
     * @var envioSMS
     */
    private $envioSMS;    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;  
    /**
     *
     * @var telconet\schemaBundle\Service\UtilService 
     */
    private $serviceUtil;
    /**
     *
     * @var boolean
     */
    private $officeSslVerify;
    
    public function setDependencies(Container $container)
    {
        $this->envioSMS          = $container->get('comunicaciones.SMS');
        $this->envioPlantilla    = $container->get('soporte.EnvioPlantilla');
        $this->emInfraestructura = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComercial       = $container->get('doctrine')->getManager('telconet');
        $this->restClient        = $container->get('schema.RestClient');
        $this->serviceUtil       = $container->get('schema.Util');
        $this->strUrlREST        = $container->getParameter('tecnico.ws_office365_url'); 
        $this->officeSslVerify   = ($container->hasParameter('tecnico.ws_office365_ssl_verify') ? 
                                   $container->getParameter('tecnico.ws_office365_ssl_verify') : true);
    }

    /**
     * Funcion que sirve para crear Licencia de productos Office 365 a un cliente
     * @param array $arrayParametros Parametros necesarios para la activacion y cancelacion de suscripciones Office
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 10-10-2016
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 28-11-2016 Se agrega la funcion urldecode al crear las url usadas para office365
     * @since 1.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.2 19-04-2018 Se cambian nombres de métodos de activación de nuevas suscripciones netlifecloud por
     *                         cambios en ws de Intcomex
     * @since 1.1
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.3 27-08-2018 - Se envía la versión CURLOPT_SSLVERSION en el ws de Intcomex.
     *
     * @param array $arrayParametros ["strApiKey"]       String: Llave pública de INTCOMEX
     *                               ["strAccessKey"]    String: Llave privada de INTCOMEX
     *                               ["strSKU"]          String: Fecha y Hora en formato UTC
     *                               ["orderNumber"]     int:    Numero de Orden generada por INTCOMEX
     *                               ["strMetodo"]       String: Nombre del método a ejecutarse : Si llega vacio
     *                                                   no se ejecuta ningun método
     *                               ["strUser"]         String: Usuario de session
     *                               ["strIpClient"]     String: Ip session
     */
    public function operacionesSuscripcionCliente($arrayParametros)
    {
        ini_set('default_socket_timeout', 400000);
        $strApiKey            = $arrayParametros["strApiKey"];
        $strAccessKey         = $arrayParametros["strAccessKey"];        
        $strLocale            = 'es';        
        $strSKU               = $arrayParametros["strSKU"];
        $intOrderNumber       = $arrayParametros["orderNumber"];
        $strMetodo            = $arrayParametros["strMetodo"];
        $strUser              = $arrayParametros["strUser"];
        $strIpClient          = $arrayParametros["strIpClient"];
        $arrayRespuesta       = array();
        $arrayInformeError    = array();  
        
        $arrayInformeError["strProceso"]    = "operacionesSuscripcionCliente";
        $arrayInformeError["strUser"]       = $strUser;
        $arrayInformeError["strIpClient"]   = $strIpClient;
        
        try
        {               
            $arrayFirma["strApiKey"]            = $strApiKey;     
            $arrayFirma["strAccesKey"]          = $strAccessKey;     
            $arrayFirma["strUtcTimeStamp"]      = $this->obtenerFechaUTC();
            $strUrlintcomex                     = $this->strUrlREST;
            $strUrlAccion                       = $strUrlintcomex.$strMetodo.'?';            
            
            $arrayConsulta                      = array('apiKey'        => $strApiKey,
                                                        'utcTimeStamp'  => $this->obtenerFechaUTC(),
                                                        'signature'     => $this->generarFirmaIntComex($arrayFirma));

            if ($strMetodo == 'placeorder')
            {
                $strQuery     = http_build_query($arrayConsulta);
                $strUrlAccion = urldecode($strUrlAccion.$strQuery);
                
                $strDetallePedido= json_encode(array(
                                                  array('Sku'          => $strSKU,
                                                        'Quantity'     => 1)
                                                 )
                                           );

                $arrayOptions  = array(
                                        CURLOPT_SSL_VERIFYPEER => $this->officeSslVerify,
                                        CURLOPT_SSLVERSION     => 6
                                      );

                $arrayResponse = $this->restClient->postJSON($strUrlAccion,
                                                             $strDetallePedido,
                                                             $arrayOptions);

                if ($arrayResponse['status'] == 200)
                {
                    // HTTP Status 200 OK - comunicacion correcta con servidor de intcomex
                    $arrayRespuesta                   = json_decode($arrayResponse['result'], true);
                    $arrayRespuesta['procesoExitoso'] = true;
                }
                else
                {

                    $arrayResult = json_decode($arrayResponse['result'], true);
                    //Grabar en log error de Type
                    $arrayInformeError["strDetalleError"] = $arrayResult['Message'];
                    $this->guardarInformeError($arrayInformeError);
                    
                    //Grabar en log error de Mensaje
                    if(isset($arrayResult['MessageDetail']))
                    {
                        $arrayInformeError["strDetalleError"] = $arrayResult['MessageDetail'];
                        $this->guardarInformeError($arrayInformeError);
                    }
                    //Error 500
                    if(isset($arrayResult['StackTrace']))
                    {
                       $arrayInformeError["strDetalleError"] = $arrayResult['StackTrace'];
                       $this->guardarInformeError($arrayInformeError);
                    }
                    // error de comunicacion con servidor de tokens
                    return array(
                                 'error_log'       => "Error en el consumo del metodo ".$strMetodo." Informar a sistemas" ,
                                 'status'          => static::$TOKEN_ERROR,
                                 'procesoExitoso'  => false,
                                 'mensajeRespuesta'=> " Problemas de conexión con MICROSOFT, no se genera una orden de Producto IntComex."
                                );
                }
            }
            else if($strMetodo == 'purchaseesdproducts')
            {
                $strQuery          = http_build_query($arrayConsulta);
                $strUrlAccion      = urldecode($strUrlAccion.$strQuery);
                
                $strDetallePedido  = json_encode(array('OrderNumber'  => $intOrderNumber));

                $arrayOptions      = array(
                                            CURLOPT_SSL_VERIFYPEER => false,
                                            CURLOPT_SSLVERSION     => 6
                                          );

                $arrayResponse     = $this->restClient->postJSON($strUrlAccion,
                                                                 $strDetallePedido,
                                                                 $arrayOptions);

                if ($arrayResponse['status'] == 200)
                {
                    // HTTP Status 200 OK - comunicacion correcta con servidor de intcomex
                    $arrayRespuesta                   = json_decode($arrayResponse['result'], true);
                    
                    $arrayRespuesta['procesoExitoso'] = true;
                }
                else
                {   
                    $arrayResult = json_decode($arrayResponse['result'], true);
                    //Grabar en log error de Type
                    $arrayInformeError["strDetalleError"] = $arrayResult['Message'];
                    $this->guardarInformeError($arrayInformeError);
                    //Grabar en log error de Mensaje
                    if(isset($arrayResult['MessageDetail']))
                    {
                        $arrayInformeError["strDetalleError"] = $arrayResult['MessageDetail'];
                        $this->guardarInformeError($arrayInformeError);
                    }
                    //Error 500
                    if(isset($arrayResult['StackTrace']))
                    {
                        $arrayInformeError["strDetalleError"] = $arrayResult['StackTrace'];
                        $this->guardarInformeError($arrayInformeError);
                    }
                    
                    // error de comunicacion con servidor de tokens
                    return array(
                                 'error_log'       => "Error en el consumo del metodo ".$strMetodo." Informar a sistemas" ,
                                 'status'          => static::$TOKEN_ERROR,
                                 'procesoExitoso'  => false,
                                 'mensajeRespuesta'=> " Problemas de conexión con MICROSOFT. No se genera clave del producto"
                                );
                } 
            }
            else if($strMetodo == 'getcatalog')
            {
                $arrayConsulta = array('apiKey'        => $strApiKey,
                                       'utcTimeStamp'  => $this->obtenerFechaUTC(),
                                       'signature'     => $this->generarFirmaIntComex($arrayFirma),
                                       'locale'        => $strLocale);

                $arrayOptions  = array(
                                        CURLOPT_SSL_VERIFYPEER => false,
                                        CURLOPT_SSLVERSION     => 6
                                      );

                $arrayResponse = $this->restClient->get($strUrlAccion, $arrayConsulta);

                if ($arrayResponse['status'] == 200)
                {
                    // HTTP Status 200 OK - comunicacion correcta con servidor de intcomex
                    $arrayRespuesta                   = json_decode($arrayResponse['result'], true);
                    $arrayRespuesta['procesoExitoso'] = true;
                }
                else
                {
                    $arrayResult = json_decode($arrayResponse['result'], true);
                    //Grabar en log error de Type
                    $arrayInformeError["strDetalleError"] = $arrayResult['Message'];
                    $this->guardarInformeError($arrayInformeError);
                    //Grabar en log error de Mensaje
                    if($arrayResult['MessageDetail'])
                    {
                        $arrayInformeError["strDetalleError"] = $arrayResult['MessageDetail'];
                        $this->guardarInformeError($arrayInformeError); 
                    }
                    //Error 500
                    if($arrayResult['StackTrace'])
                    {
                        $arrayInformeError["strDetalleError"] = $arrayResult['StackTrace'];
                        $this->guardarInformeError($arrayInformeError); 
                    }
                    // error de comunicacion con servidor de tokens
                    return array(
                                 'error_log'       => "Error en el consumo del metodo ".$strMetodo." Informar a sistemas" ,
                                 'status'          => static::$TOKEN_ERROR,
                                 'procesoExitoso'  => false,
                                 'mensajeRespuesta'=> " Problemas de conexión con MICROSOFT. No se puede mostrar el catalogo en estos momentos"
                                );
                }
            }
            else
            {
                return array(
                             'error_log'       => "No existe el método consultado: " . $strMetodo,
                             'status'          => static::$TOKEN_ERROR,
                             'procesoExitoso'  => false,
                             'mensajeRespuesta'=> "No existe el método consultado: " . $strMetodo
                            );
            }
        }
        catch(\Exception $ex)
        {
            $booleanValidaErrorREST               = strpos($ex->getMessage(), 'REST-ERROR');
            $arrayInformeError["strDetalleError"] = $ex->getMessage();
            $this->guardarInformeError($arrayInformeError);
            if($booleanValidaErrorREST !== false)
            {
                $arrayRespuesta["mensajeRespuesta"] = " Problemas de conexión con MICROSOFT. Reportar a sistemas";
            }
            else
            {
                $arrayRespuesta["mensajeRespuesta"] = " Problemas al ejecutar Operación";
            }
            $arrayRespuesta["procesoExitoso"]   = "false";
        }
        return $arrayRespuesta;
    }
    
    /**
     * Función que me retorna la fecha UTC
     * 
     * @author Creado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 10-08-2016
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.1 28-11-2016 Se modifica modo de obtener la fecha, se setea por default el timezone UTC
     * @since 1.0
     * 
     * @return date Fecha y Hora Actual
     */
    public function obtenerFechaUTC()
    {
        date_default_timezone_set('UTC');
        $objTimeStamp = date('Y-m-d\TH:i:s\Z', time());
        date_default_timezone_set(ini_get('date.timezone'));
	return $objTimeStamp;
    }
    
    /**
     * Funcion que genera firma para INTCOMEX
     * 
     * @author Creado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 02-09-2016
     * @param array $arrayParametros["strApiKey"]       String: Llave pública de INTCOMEX
     *                              ["strAccesKey"]     String: Llave privada de INTCOMEX
     *                              ["strUtcTimeStamp"] String: Fecha y Hora en formato UTC
     * @return string firma para consumo de método de Office 365
     */
    public function generarFirmaIntComex($arrayParametros)
    {
        $strApiKey       = $arrayParametros["strApiKey"];     
        $strAccessKey    = $arrayParametros["strAccesKey"];     
        $strUtcTimeStamp = $arrayParametros["strUtcTimeStamp"];
        
        return hash('sha256', $strApiKey.','.$strAccessKey.','.$strUtcTimeStamp);
    }

    /**
     * Funcion que registra las caracteristicas del producto utilizado en un servicio
     * 
     * @author  Creado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 05-08-2016
     * @param  ['servicio']             Object:        Objeto de tipo servicio
     *         ['nombreCaracteristica'] String:        Nombre de la caracteristica a insertar
     *         ['valor']                String:        Valor de la caracterisitica
     *         ['strUser']              String:        Usuario de creacion del registro
     * 
     * @return array $arrayRespuesta respuesta del insert a la tabla InfoServicioProdCaract
     */
    public function guardaServicioProductoCaracteristicaPorServicio($arrayParams)
    {
        try
        {
            $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array("descripcionCaracteristica" => $arrayParams['nombreCaracteristica'],
                                                                            "estado"                    => "Activo")
                                                                     );
            if ($arrayParams['servicio']->getProductoId())
            {
                $entityAdmiProductoCaracteristica = $this->emComercial
                                                         ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                         ->findOneBy(array("productoId"       => $arrayParams['servicio']->getProductoId(),
                                                                           "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                           "estado"           => "Activo"
                                                                          )
                                                                    );
            }
            else
            {
                $entityPlanDet                    = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                         ->findOneByPlanId($arrayParams['servicio']->getPlanId()->getId());

                $entityAdmiProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                      ->findOneBy(array("productoId"       => $entityPlanDet->getProductoId(),
                                                                                        "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                                        "estado"           => "Activo"
                                                                                       )
                                                                                 );

            }

            //Guardar informacion de la caracteristica del producto
            $entityServicioProdCaract = new InfoServicioProdCaract();
            $entityServicioProdCaract->setServicioId($arrayParams['servicio']->getId());
            $entityServicioProdCaract->setProductoCaracterisiticaId($entityAdmiProductoCaracteristica->getId());
            $entityServicioProdCaract->setValor($arrayParams['valor']);
            $entityServicioProdCaract->setEstado('Activo');
            $entityServicioProdCaract->setUsrCreacion($arrayParams['strUser']);
            $entityServicioProdCaract->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($entityServicioProdCaract);
            
            $arrayRespuesta = array("status"  => "OK",
                                    "mensaje" => "Caracteristica Guardada exitosamente");
        }
        catch(\Exception $ex)
        {
            $arrayInformeError                    = array();
            $arrayInformeError["strProceso"]      = "guardaServicioProductoCaracteristicaPorServicio";
            $arrayInformeError["strUser"]         = $arrayParams["strUser"];
            $arrayInformeError["strIpClient"]     = $arrayParams["strIpClient"];
            $arrayInformeError["strDetalleError"] = $ex->getMessage();
            $this->guardarInformeError($arrayInformeError);
            $arrayRespuesta = array("status"  => "ERROR",
                                    "mensaje" => "Problemas al guardar caracteristicas: ".$arrayParams['nombreCaracteristica']);
        }
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que retorna información de cliente Office 365
     * 
     * @author  Creado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 20-01-2015
     * @param   array $arrayParametro ["strCaracteristica"]  String:  Nombre de la caracteristica
     *                                ["entityInfoServicio"] Entity:  Entidad que contiene la información de un servicio  
     *                                ["strUser"]            String:  Usuario de session
     *                                ["strIpClient"]        String:  Ip session
     * @return  array $arrayParametros
     * 
     */
    public function obtenerValorServicioProductoCaracteristicaPorServicio($arrayParametro)
    {
        try
        {
            $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneBy(array("descripcionCaracteristica" => $arrayParametro["strCaracteristica"],
                                                                            "estado"                    => "Activo"
                                                                           )
                                                                     );
            if ($arrayParametro["entityInfoServicio"]->getProductoId())
            {
                $entityAdmiProductoCaracteristica= $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"      => $arrayParametro["entityInfoServicio"]->getProductoId(),
                                                                          "caracteristicaId"=> $entityAdmiCaracteristica->getId(),
                                                                          "estado"          => "Activo"
                                                                         )
                                                                    );
            }
            else
            {
                $entityPlanDet                    = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                         ->findOneByPlanId($arrayParametro["entityInfoServicio"]->getPlanId()->getId());
                
                $entityAdmiProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                         ->findOneBy(array("productoId"       => $entityPlanDet->getProductoId(),
                                                                           "caracteristicaId" => $entityAdmiCaracteristica->getId(),
                                                                           "estado"           => "Activo"
                                                                          )
                                                                     );
                
            }

            if ($entityAdmiProductoCaracteristica)
            {
                $entityInfoServicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                     ->findOneBy(array("productoCaracterisiticaId" => $entityAdmiProductoCaracteristica->getId(),
                                                                       "servicioId"                => $arrayParametro["entityInfoServicio"]->getId(),
                                                                       "estado"                    => "Activo"
                                                                      )
                                                                );
            }
            else
            {
                $entityInfoServicioProdCaract = null;
            }
            
            $arrayRespuesta = array("status"  => "OK",
                                    "mensaje" => $entityInfoServicioProdCaract);
        }
        catch(\Exception $ex)
        {
            $arrayInformeError                    = array();
            $arrayInformeError["strProceso"]      = "obtenerValorServicioProductoCaracteristicaPorServicio";
            $arrayInformeError["strUser"]         = $arrayParametro["strUser"];
            $arrayInformeError["strIpClient"]     = $arrayParametro["strIpClient"];
            $arrayInformeError["strDetalleError"] = $ex->getMessage();
            $this->guardarInformeError($arrayInformeError);
            $arrayRespuesta = array("status"  => "ERROR",
                                    "mensaje" => "Problemas al obtener valor de caracteristica.");
        }
        return $arrayRespuesta;
    }
    
    /**
     * Función que almacena informe de error en la tabla info_error
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 02-09-2016
     * @param array $arrayParams["strProceso"]      String: Nombre del proceso
     *                          ["strDetalleError"] String: Mensaje de Error
     *                          ["strUser"]         String: Usuario de Session
     *                          ["strIpClient"]     String: Ip de Session
     * 
     */
    public function guardarInformeError($arrayParams)
    {
        $arrayRespuesta = array();
        try
        {
            $this->serviceUtil->insertError('TELCOS+',
                                            'LicenciasOffice365Service->'.$arrayParams["strProceso"],
                                            $arrayParams["strDetalleError"],
                                            $arrayParams["strUser"],
                                            $arrayParams["strIpClient"]);
            $arrayRespuesta = array("status"   => "OK",
                                    "mensaje"  => "Se inserta correctamente.");
        }
        catch(\Exception $ex)
        {
            $arrayRespuesta = array("status"   => "ERROR",
                                    "mensaje"  => "Problemas al obtener valor de caracteristica.");
        }
        return $arrayRespuesta;
    }

    /**
     * Funcion que genera orden de trabajo de Office 365
     * 
     * @author  Creado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 08-08-2016
     * @param  ['strCodEmpresa'] String:        Codigo Empresa
     *         ['intIdOficina']  String:        Id Oficina
     *         ['entityPunto']   Object:        Id Punto
     *         ['strIpClient']   String:        Ip session
     *         ['strUser']       String:        Usuario session
     * 
     * @param  array $arrayRespuesta Insert de la orden de trabajo
     * 
    */
    public function generaOrdenDeTrabajo($arrayParams)
    {
        try
        {
            $entityDatosNumeracion = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                       ->findByEmpresaYOficina($arrayParams['strCodEmpresa'],
                                                                               $arrayParams['intIdOficina'],
                                                                               'ORD');
            $strSecuenciaAsig      = str_pad($entityDatosNumeracion->getSecuencia(),7, '0', STR_PAD_LEFT);
            $strNumeroContrato     = $entityDatosNumeracion->getNumeracionUno().'-'.$entityDatosNumeracion->getNumeracionDos().'-'.$strSecuenciaAsig;

            $entityOrdenTrabajo    = new InfoOrdenTrabajo();
            $entityOrdenTrabajo->setPuntoId($arrayParams['entityPunto']);
            $entityOrdenTrabajo->setTipoOrden('N');
            $entityOrdenTrabajo->setNumeroOrdenTrabajo($strNumeroContrato);
            $entityOrdenTrabajo->setFeCreacion(new \DateTime('now'));
            $entityOrdenTrabajo->setUsrCreacion($arrayParams['strUser']);
            $entityOrdenTrabajo->setIpCreacion($arrayParams['strIpClient']);
            $entityOrdenTrabajo->setOficinaId($arrayParams['intIdOficina']);
            $entityOrdenTrabajo->setEstado('Activo');
            $this->emComercial->persist($entityOrdenTrabajo);
            $arrayRespuesta = array("status"  => "OK",
                                    "mensaje" => $entityOrdenTrabajo);
        }
        catch(\Exception $ex)
        {
            $arrayInformeError                    = array();
            $arrayInformeError["strProceso"]      = "generaOrdenDeTrabajo";
            $arrayInformeError["strUser"]         = $arrayParams["strUser"];
            $arrayInformeError["strIpClient"]     = $arrayParams["strIpClient"];
            $arrayInformeError["strDetalleError"] = $ex->getMessage();
            $this->guardarInformeError($arrayInformeError);
            $arrayRespuesta = array("status"  => "ERROR",
                                    "mensaje" => "Problemas al generar orden de trabajo.");
        }
        return $arrayRespuesta;
    }
    
    /** Funcion que retorna informacion de clientes para suscripciones Office 365
     * 
     * @author  Creado: Walther Gaibor <wgaibor@telconet.ec>
     * @version 1.0 08-05-2016
     * 
     * 
     * @param array $arrayParametro["intIdPersona"]  Integer  id de la persona
     *                             ["intIdServicio"] String   id del servicio
     *                             ["strUser"]       String   Usuario de session
     *                             ["strIpClient"]   String   Ip de session
     * 
     * @return array $arrayParametros
     */
    public function obtenerInformacionClienteOffice365($arrayParametro)
    {
        $arrayRespuesta         = array();
        $strNombre               = "";
        $strApellido             = "";        
        $strCorreo               = "";
        $strApiKey               = "";
        $strAccessKey            = "";
        $strSku                  = "";        
        $em                      = $this->emComercial;
        $entityInfoPersona       = $em->getRepository('schemaBundle:InfoPersona')->findOneById($arrayParametro["intIdPersona"]);
        $arrayProdCaract         = array();
        try
        {
            if($entityInfoPersona->getRazonSocial())
            {
                $strNombre   = $entityInfoPersona->getRazonSocial();
                $strApellido = $entityInfoPersona->getRazonSocial();
            }
            else
            {
                $strNombre   = $entityInfoPersona->getNombres();
                $strApellido = $entityInfoPersona->getApellidos();
                
            }
            
            $entityInfoServicio = $em->getRepository('schemaBundle:InfoServicio')
                                     ->find($arrayParametro["intIdServicio"]);
            
            $strIdentificacion = $entityInfoPersona->getIdentificacionCliente();
            ///------------------------------
            $entityAdmiParametroDet = "";
            $entityAdmiParametroDet = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->findOneBy(array("descripcion" => "SKUOFFICE",
                                                                           "estado"      => "Activo"
                                                                          )
                                                                    );
            if (!$entityAdmiParametroDet)
            {
                throw new \Exception("problemas al obtener informacion del SKUOFFICE, producto NetlifeCloud.");
            }
            else
            {
                $strSku  = $entityAdmiParametroDet->getValor1();
            }

            ///------------------------------            

            $entityAdmiParametroDet = "";
            $entityAdmiParametroDet = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->findOneBy(array("descripcion" => "APIKEY",
                                                                           "estado"      => "Activo"
                                                                          )
                                                                    );
            if (!$entityAdmiParametroDet)
            {
                throw new \Exception("problemas al obtener informacion del APIKEY, producto NetlifeCloud.");
            }
            else
            {
                $strApiKey  = $entityAdmiParametroDet->getValor1();
            }
                        
            ///------------------------------            

            $entityAdmiParametroDet = "";
            $entityAdmiParametroDet = $em->getRepository('schemaBundle:AdmiParametroDet')
                                                         ->findOneBy(array("descripcion" => "ACCESSKEY",
                                                                           "estado"      => "Activo"
                                                                          )
                                                                    );
            if (!$entityAdmiParametroDet)
            {
                throw new \Exception("problemas al obtener informacion del ACCESSKEY, producto NetlifeCloud.");
            }
            else
            {
                $strAccessKey  = $entityAdmiParametroDet->getValor1();
            }            
            
            ///------------------------------  
            $entityInfoServicioProdCaract = "";
            $arrayProdCaract["strCaracteristica"]  = "CORREO ELECTRONICO";
            $arrayProdCaract["entityInfoServicio"] = $entityInfoServicio;
            $arrayProdCaract["strUser"]            = $arrayParametro["strUser"];
            $arrayProdCaract["strIpClient"]        = $arrayParametro["strIpClient"];
                
            $arrayResultado = $this->obtenerValorServicioProductoCaracteristicaPorServicio($arrayProdCaract);
            if($arrayResultado["status"] == 'ERROR')
            {
                throw new \Exception($arrayResultado["mensaje"]);
            }
            $entityInfoServicioProdCaract = $arrayResultado["mensaje"];
                
            if ($entityInfoServicioProdCaract)
            {
                $strCorreo = $entityInfoServicioProdCaract->getValor();
            }
            else
            {
                $entityAdmiFormaContacto = $em->getRepository('schemaBundle:AdmiFormaContacto')
                                              ->findOneBy(array( "descripcionFormaContacto" => "Correo Electronico",
                                                                 "estado" => 'Activo'));
                //Office365 - se obtiene correo en caso de que el producto sea Internet Protegido
                $entityPuntoFormaContacto = $em->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                               ->findOneBy(array( "puntoId"         => $entityInfoServicio->getPuntoId()->getId(), 
                                                                  "formaContactoId" => $entityAdmiFormaContacto->getId()));
                if ($entityPuntoFormaContacto)
                {
                    $strCorreo = $entityPuntoFormaContacto->getValor();
                }
            }                                    
            
            $arrayRespuesta["strNombre"]               = $strNombre;
            $arrayRespuesta["strApellido"]             = $strApellido;
            $arrayRespuesta["strIdentificacion"]       = $strIdentificacion;
            $arrayRespuesta["strSKU"]                  = $strSku;
            $arrayRespuesta["strApiKey"]               = $strApiKey;
            $arrayRespuesta["strAccessKey"]            = $strAccessKey;
            $arrayRespuesta["strCorreo"]               = $strCorreo;
            $arrayRespuesta["strError"]                = "false";            
            $arrayRespuesta["orderNumber"]             = "";
            $arrayRespuesta["productKey"]              = "";
            
        }
        catch(\Exception $ex)
        {
            $arrayInformeError                    = array();
            $arrayInformeError["strProceso"]      = "obtenerInformacionClienteOffice365";
            $arrayInformeError["strUser"]         = $arrayParametro["strUser"];
            $arrayInformeError["strIpClient"]     = $arrayParametro["strIpClient"];
            $arrayInformeError["strDetalleError"] = $ex->getMessage();
            $this->guardarInformeError($arrayInformeError);
            $arrayRespuesta = array("status"  => "ERROR",
                                    "strError"=> "true",
                                    "mensaje" => "Problemas al generar orden de trabajo.");
        }
        return $arrayRespuesta;
    }

     /** Funcion que genera la compra - renovación de una suscripción Office 365 a un cliente específico
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 27-06-2018
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.1 27-02-2019  Se agrega funcionalidad para que se realicen n número de intentos al realizar la renovación de licencia, se grega 
     *                          funcionalidad para la inactivacion de caracteristicas existentes y el registro de historiales para la renovación
     *                          y en caso de que falle la conexión con el proveedor.
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.2 09-03-2023 Se realiza validación para verificar nueva caracteristica PRODUCTKEY existente y no generar renovación.
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.3 09-03-2023 Se realiza reverso de validación debido a que ya se implementa desde proceso en base y causa afectación.
     * 
     * @param array $arrayParametrosWs["intIdPersona"]       Integer  id de la persona
     *                                ["intIdServicio"]      String   id del servicio
     *                                ["strUser"]            String   Usuario de session
     *                                ["strIpClient"]        String   Ip de session
     *                                ["strAccion"]          String   Accion.
     * 
     * @return array $arrayParametros
     */
    public function renovarLicenciaOffice365($arrayParametrosWs)  
    {
        $em                          = $this->emComercial;
        $objRepoPuntoFormaContacto   = $em->getRepository('schemaBundle:InfoPuntoFormaContacto');
        $objRepoPersonaFormaContacto = $em->getRepository('schemaBundle:InfoPersonaFormaContacto');
        
        $strPrefijoEmpresa           = $arrayParametrosWs['strPrefijoEmpresa'];
        $strCodEmpresa               = (int)$arrayParametrosWs['strEmpresaCod'];
        $strUsrCreacion              = $arrayParametrosWs['strUsuarioCreacion'];
        $strClientIp                 = $arrayParametrosWs['strIp'];
        $intIdServicio               = (int)$arrayParametrosWs['intServicioId'];
        $strAccion                   = $arrayParametrosWs['strAccion'];
        $strPlantillaMail            = '';
        $arrayRespuestaServicio      = array();
        $boolEjecutaRenovacion       = true;
        
        $objParametroCabRenovacion = $em->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy( array('nombreParametro' => 'RENOVAR_LIC_OFFICE365','estado' => 'Activo') );

        if(is_object($objParametroCabRenovacion))
        {
            $objParamDetRenovacion = $em->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findOneBy( array('parametroId' => $objParametroCabRenovacion,
                                                           'valor2'      => 'NumeroIntentosRenovacion',
                                                           'estado'      => 'Activo'));
        }
        $intNumIntentosRenovacion  = intval($objParamDetRenovacion->getValor1());
        
        $em->getConnection()->beginTransaction();
        
        try
        {
            
            $objServicio = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $objLogin    = $objServicio->getPuntoId()->getLogin();
            
            $arrayGuardarProdCarac                   = array();
            $arrayGuardarProdCarac['servicio']       = $objServicio;
            $arrayGuardarProdCarac['strUser']        = $strUsrCreacion;
            $arrayGuardarProdCarac['strIpClient']    = $strClientIp;

            //--SE GRABARAN LAS CARACTERISTICAS EN EL SERVICIO

            $objDatosCliente         = $em->getRepository("schemaBundle:InfoPersona")
                                          ->getDatosClientePorIdServicio($intIdServicio,
                                                                         "esProducto");
            if(!$objDatosCliente['ID_PERSONA'])
            {
                $objDatosCliente = $em->getRepository("schemaBundle:InfoPersona")
                                      ->getDatosClientePorIdServicio($intIdServicio,false);
            }

            $arrayObtenerInformacion                  = array();
            $arrayObtenerInformacion["intIdPersona"]  = $objDatosCliente['ID_PERSONA'];
            $arrayObtenerInformacion["intIdServicio"] = $intIdServicio;
            $arrayObtenerInformacion["strUser"]       = $strUsrCreacion;
            $arrayObtenerInformacion["strIpClient"]   = $strClientIp;
            $arrayParametros = $this->obtenerInformacionClienteOffice365($arrayObtenerInformacion);

            $arrayParametros["strMetodo"] = 'placeorder';
            if($arrayParametros["strError"] == 'true')
            {
                $arrayRespuestaServicio['status'] = 'ERROR';
                throw new \Exception("Problemas al obtener informacion del cliente");
            }
            $arrayParametros["strUser"]     = $strUsrCreacion;
            $arrayParametros["strIpClient"] = $strClientIp;

            while($boolEjecutaRenovacion)
            {
                $arrayRespuestaServicio  = $this->operacionesSuscripcionCliente($arrayParametros);

                if($arrayRespuestaServicio["procesoExitoso"] || $intNumIntentosRenovacion <= 0)
                {
                    $boolEjecutaRenovacion = false;
                }
                else
                {
                    $intNumIntentosRenovacion = $intNumIntentosRenovacion - 1;
                }
            }

            if(!$arrayRespuestaServicio["procesoExitoso"])
            {
                if($em->getConnection()->isTransactionActive())
                {
                    $em->getConnection()->rollback();

                    if($intNumIntentosRenovacion <= 0)
                    {
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now -1 day'));
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setIpCreacion($strClientIp);
                        $objServicioHistorial->setEstado('Activo');
                        $objServicioHistorial->setAccion('errorRenovarLicOffice365');
                        $objServicioHistorial->setObservacion('Error de conectividad con el proveedor.');
                        $em->persist($objServicioHistorial);
                        $em->flush();
                    }
                }

                $arrayRespuestaServicio['status']  = 'ERROR';
                $arrayRespuestaServicio['mensaje'] = $arrayRespuestaServicio["mensajeRespuesta"];
                return $arrayRespuestaServicio;
            }


            $strUsrModifica    = 'telcos_renova';
            $strEstadoInactivo = 'Inactivo';
            $strMessageError   = "";

            //grabar caracteristica ORDERNUMBER
            $arrayGuardarProdCarac['nombreCaracteristica'] = "ORDERNUMBER";
            $arrayGuardarProdCarac['valor']                = $arrayRespuestaServicio["OrderNumber"];

            $strSql = "BEGIN DB_COMERCIAL.COMEK_TRANSACTION.P_SET_ESTADO_INFO_SERV_CARAC(:Pn_IdServicio,"
                                                                                      . " :Pv_DescripcionCaract,"
                                                                                      . " :Pv_NuevoEstado,"
                                                                                      . " :Pv_UsrModifica,"
                                                                                      . " :Pv_Mensaje); END;";
            $objStmt = $em->getConnection()->prepare($strSql);
            $objStmt->bindParam('Pn_IdServicio', $intIdServicio);
            $objStmt->bindParam('Pv_DescripcionCaract', $arrayGuardarProdCarac['nombreCaracteristica']);
            $objStmt->bindParam('Pv_NuevoEstado', $strEstadoInactivo);
            $objStmt->bindParam('Pv_UsrModifica', $strUsrModifica);
            $objStmt->bindParam('Pv_Mensaje', $strMessageError);
            $objStmt->execute();

            $arrayRespuesta      = $this->guardaServicioProductoCaracteristicaPorServicio($arrayGuardarProdCarac);

            if($arrayRespuesta["status"] == 'ERROR')
            {
                $arrayRespuestaServicio['status'] = 'ERROR';
                throw new \Exception($arrayRespuesta["mensaje"]);
            }

            //Buscar el product key en base al ordernumber
            $arrayParametros["strMetodo"]   = 'purchaseesdproducts';
            $arrayParametros["orderNumber"] = $arrayRespuestaServicio["OrderNumber"];

            if($arrayParametros["orderNumber"])
            {
                $arrayParametros["strUser"]     = $strUsrCreacion;
                $arrayParametros["strIpClient"] = $strClientIp;
                $arrayRespuestaServicio = $this->operacionesSuscripcionCliente($arrayParametros);

                if(!$arrayRespuestaServicio["procesoExitoso"])
                {
                    $booleanEnvioDeCorreo = false;
                    if($em->getConnection()->isTransactionActive())
                    {
                        $em->getConnection()->rollback();
                    }
                    $arrayRespuestaServicio['status']  = 'ERROR';
                    $arrayRespuestaServicio['mensaje'] = $arrayRespuestaServicio["mensajeRespuesta"];
                    return $arrayRespuestaServicio;
                }
                // Clave del Producto NetlifeCloud
                $strSuccesfulKeyProduct = $arrayRespuestaServicio[0]['EsdFulfillmentStatus'];

                // Descripción del producto que se compra
                $strDescripcion         = $arrayRespuestaServicio[0]['Description'];

                if((strtoupper($strSuccesfulKeyProduct) != 'OK') && (strtoupper($strSuccesfulKeyProduct) != 'ORDERQUANTITYALREADYFULFILLED'))
                {
                    $arrayRespuestaServicio['status'] = 'ERROR';
                    $arrayRespuesta["mensaje"]             = 'No se ha podido activar el servicio problemas de conexión con WebService '
                                                        . 'informar a sistemas';
                    throw new \Exception($arrayRespuesta["mensaje"]);
                }
                else
                {
                    $strKeyProduct           = $arrayRespuestaServicio[0]['EsdFulfillments'][0]['Products'][0]['Tokens'][0]['ProductKey'];
                    $strUrlDownLoadProduct   = $arrayRespuestaServicio[0]['EsdFulfillments'][0]['Products'][0]['Links'][0]['Uri'];
        
                    if(isset($strKeyProduct) && $strKeyProduct !== '')
                    {
                        $booleanEnvioDeCorreo  = true;
                    }
                }

            }
            else
            {
                $arrayRespuestaServicio['status']  = 'ERROR';
                $arrayRespuestaServicio['mensaje'] = 'No existe un número de orden generado, reportar a sistema.';
                return $arrayRespuestaServicio;
            }

            //grabar caracteristica PRODUCTKEY
            if($booleanEnvioDeCorreo)
            {
                //Grabar la clave del producto
                $arrayGuardarProdCarac['nombreCaracteristica'] = "PRODUCTKEY";
                $arrayGuardarProdCarac['valor']                = $strKeyProduct;

                $objStmt = $em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pn_IdServicio', $intIdServicio);
                $objStmt->bindParam('Pv_DescripcionCaract', $arrayGuardarProdCarac['nombreCaracteristica']);
                $objStmt->bindParam('Pv_NuevoEstado', $strEstadoInactivo);
                $objStmt->bindParam('Pv_UsrModifica', $strUsrModifica);
                $objStmt->bindParam('Pv_Mensaje', $strMessageError);
                $objStmt->execute();

                $arrayRespuesta      = $this->guardaServicioProductoCaracteristicaPorServicio($arrayGuardarProdCarac);
                if($arrayRespuesta["status"] == 'ERROR')
                {
                    $arrayRespuestaServicio['status'] = 'ERROR';
                    throw new \Exception($arrayRespuesta["mensaje"]);
                }

                //Grabar la descripcion del producto
                $arrayGuardarProdCarac['nombreCaracteristica'] = "DESCRIPCIONOFFICE";
                $arrayGuardarProdCarac['valor']                = $strDescripcion;

                $objStmt = $em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pn_IdServicio', $intIdServicio);
                $objStmt->bindParam('Pv_DescripcionCaract', $arrayGuardarProdCarac['nombreCaracteristica']);
                $objStmt->bindParam('Pv_NuevoEstado', $strEstadoInactivo);
                $objStmt->bindParam('Pv_UsrModifica', $strUsrModifica);
                $objStmt->bindParam('Pv_Mensaje', $strMessageError);
                $objStmt->execute();

                $arrayRespuesta      = $this->guardaServicioProductoCaracteristicaPorServicio($arrayGuardarProdCarac);
                if($arrayRespuesta["status"] == 'ERROR')
                {
                    $arrayRespuestaServicio['status'] = 'ERROR';
                    throw new \Exception($arrayRespuesta["mensaje"]);
                }

                //Grabar la url del producto
                $arrayGuardarProdCarac['nombreCaracteristica'] = "URLOFFICE";
                $arrayGuardarProdCarac['valor']                = $strUrlDownLoadProduct;

                $objStmt = $em->getConnection()->prepare($strSql);
                $objStmt->bindParam('Pn_IdServicio', $intIdServicio);
                $objStmt->bindParam('Pv_DescripcionCaract', $arrayGuardarProdCarac['nombreCaracteristica']);
                $objStmt->bindParam('Pv_NuevoEstado', $strEstadoInactivo);
                $objStmt->bindParam('Pv_UsrModifica', $strUsrModifica);
                $objStmt->bindParam('Pv_Mensaje', $strMessageError);
                $objStmt->execute();

                $arrayRespuesta      = $this->guardaServicioProductoCaracteristicaPorServicio($arrayGuardarProdCarac);
                if($arrayRespuesta["status"] == 'ERROR')
                {
                    $arrayRespuestaServicio['status'] = 'ERROR';
                    throw new \Exception($arrayRespuesta["mensaje"]);
                }


                //Obtenemos los datos de contacto del cliente al cual se enviaran las notificaciones
                
                if($strAccion === 'renovarLicenciaOffice365')
                {
                    
                    $strPlantillaMail = 'renLicOffice365';
                    
                    $strMensaje             = "Se ha renovado su servicio de NetlifeCloud. La clave del producto es: ".$strKeyProduct.
                                              " , favor revise su correo para activar el producto. ";
                
                    
                    $arrayContactosTelefonosMovilClaroPunto    = $objRepoPuntoFormaContacto->findContactosByPunto($objLogin ,
                                                                                                                  'Telefono Movil Claro');
                    $arrayContactosTelefonosMovilMovistarPunto = $objRepoPuntoFormaContacto->findContactosByPunto($objLogin ,
                                                                                                                  'Telefono Movil Movistar');
                    $arrayContactosTelefonosMovilCntPunto      = $objRepoPuntoFormaContacto->findContactosByPunto($objLogin ,
                                                                                                                  'Telefono Movil CNT');
                    $arrayContactosCorreosPunto                = $objRepoPuntoFormaContacto->findContactosByPunto($objLogin ,
                                                                                                                  'Correo Electronico');                    
                    
                    
                }
                else // Cambio de razón social
                {
                    $strPlantillaMail = 'office365';
                    
                    $strMensaje             = "Se ha contratado su servicio de NetlifeCloud. La clave del producto es: ".$strKeyProduct.
                                              " , favor revise su correo para activar el producto. ";                    

                    $arrayContactosTelefonosMovilClaroPunto    = $objRepoPersonaFormaContacto->findContactosByLoginAndFormaContacto($objLogin ,
                                                                                                              'Telefono Movil Claro');
                    $arrayContactosTelefonosMovilMovistarPunto = $objRepoPersonaFormaContacto->findContactosByLoginAndFormaContacto($objLogin ,
                                                                                                              'Telefono Movil Movistar');
                    $arrayContactosTelefonosMovilCntPunto      = $objRepoPersonaFormaContacto->findContactosByLoginAndFormaContacto($objLogin ,
                                                                                                              'Telefono Movil CNT');
                    $arrayContactosCorreosPunto                = $objRepoPersonaFormaContacto->findContactosByLoginAndFormaContacto($objLogin ,
                                                                                                              'Correo Electronico');                    
                }                



                foreach ($arrayContactosCorreosPunto as $contacto4)
                {
                    $arrayContactosCorreosPuntoMail[]               = $contacto4['valor'];
                }

                /** ****************************************************************
                  USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE CORREOS
                 * **************************************************************** */

                $strCliente           = $objDatosCliente['NOMBRES'];
                $arrayParametrosTarea = array('cliente'      => $strCliente,
                                              'descripcion'  => $strDescripcion,
                                              'keyproduct'   => $strKeyProduct,
                                              'urlproduct'   => $strUrlDownLoadProduct);

                $this->envioPlantilla->generarEnvioPlantilla("Licencia NetlifeCloud ",
                                                              $arrayContactosCorreosPuntoMail,
                                                              $strPlantillaMail,
                                                              $arrayParametrosTarea,
                                                              $strCodEmpresa,
                                                              '',
                                                              null,
                                                              '',
                                                              false,
                                                              'notificacionesnetlife@netlife.info.ec');

                //Se inserta historial de renovación

                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setFeCreacion(new \DateTime('now -1 day'));
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setIpCreacion($strClientIp);
                $objServicioHistorial->setEstado('Activo');
                $objServicioHistorial->setAccion('renovarLicenciaOffice365');
                $objServicioHistorial->setObservacion('Se renovo licencia Office365.');
                $em->persist($objServicioHistorial);
                $em->flush();
                /** ****************************************************************
                  USO DE SERVICE ENVIOPLANTILLA PARA GESTION DE ENVIO DE SMS
                 * **************************************************************** */

                $strProceso             = 'RENOVAOFF365';

                //Generamos los arreglos con la informacion de conctacto con el cliente
                foreach ($arrayContactosTelefonosMovilClaroPunto as $contacto1)
                {
                    $strNumeroTlf = $contacto1['valor'];

                    if($strNumeroTlf)
                    {
                        $arrayParametros                = array();
                        $arrayParametros['mensaje']     = $strMensaje;
                        $arrayParametros['numero']      = $strNumeroTlf;
                        $arrayParametros['user']        = $strUsrCreacion;
                        $arrayParametros['codEmpresa']  = $strCodEmpresa;
                        $arrayParametros['strProceso']  = $strProceso;

                        $arrayResponseSMS  = (array) $this->envioSMS->sendAPISMS($arrayParametros);

                        if ($arrayResponseSMS['salida'] !== '200')                         
                        {
                            $arrayRespuestaServicio['status']  = 'ERROR';
                            $arrayRespuesta["mensaje"]         = 'SMS No enviado: '.$strNumeroTlf.' Detalle: '.$arrayResponseSMS['detail'];
                            error_log($arrayRespuesta["mensaje"]);
                        }
                    }                
                }

                foreach ($arrayContactosTelefonosMovilMovistarPunto as $contacto2)
                {
                    $strNumeroTlf = $contacto2['valor'];

                    if($strNumeroTlf)
                    {
                        $arrayParametros                = array();
                        $arrayParametros['mensaje']     = $strMensaje;
                        $arrayParametros['numero']      = $strNumeroTlf;
                        $arrayParametros['user']        = $strUsrCreacion;
                        $arrayParametros['codEmpresa']  = $strCodEmpresa;

                        $arrayResponseSMS  = (array) $this->envioSMS->sendAPISMS($arrayParametros);

                        if ($arrayResponseSMS['salida'] !== '200')                         
                        {
                            $arrayRespuestaServicio['status']  = 'ERROR';
                            $arrayRespuesta["mensaje"]         = 'SMS No enviado: '.$strNumeroTlf.' Detalle: '.$arrayResponseSMS['detail'];
                            error_log($arrayRespuesta["mensaje"]);
                        }
                    }
                }

                foreach ($arrayContactosTelefonosMovilCntPunto as $contacto3)
                {
                    $strNumeroTlf = $contacto3['valor'];

                    if($strNumeroTlf)
                    {
                        $arrayParametros                = array();
                        $arrayParametros['mensaje']     = $strMensaje;
                        $arrayParametros['numero']      = $strNumeroTlf;
                        $arrayParametros['user']        = $strUsrCreacion;
                        $arrayParametros['codEmpresa']  = $strCodEmpresa;

                        $arrayResponseSMS  = (array) $this->envioSMS->sendAPISMS($arrayParametros);

                        if ($arrayResponseSMS['salida'] !== '200')                         
                        {
                            $arrayRespuestaServicio['status']  = 'ERROR';
                            $arrayRespuesta["mensaje"]         = 'SMS No enviado: '.$strNumeroTlf.' Detalle: '.$arrayResponseSMS['detail'];
                            error_log($arrayRespuesta["mensaje"]);
                        }
                    }
                }
            }
            $arrayRespuestaServicio['status']  = 'OK';
            $arrayRespuestaServicio['mensaje'] = $strMensaje;
            $em->flush();
            $em->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            
            if ($arrayRespuestaServicio['status']  == 'ERROR')
            {
                $arrayRespuestaServicio['mensaje'] = $ex->getMessage();    
            }
            else
            {
                $arrayRespuestaServicio['status']  = 'ERROR';
                $arrayRespuestaServicio['mensaje'] = '';  
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'renovarLicenciaOffice365',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        return $arrayRespuestaServicio;
    }
}

