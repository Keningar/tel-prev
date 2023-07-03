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
 * @author John Vera <javera@telconet.ec>
 */
class CallGeneralWebService
{
    /**
     * Codigo de respuesta: Respuesta valida
     */
    public static $intStatusOk = 200;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    
    //Variables para el Middleware
    private $urlNetvoice;
    private $ambiente;
    private $ejecutaScripts;
    
    //Entity Manager
    private $emComercial;
    private $emInfraestructura;
    
    public function __construct(Container $container)
    {
        $this->emInfraestructura    = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emComercial          = $container->get('doctrine')->getManager('telconet');
        $this->urlNetvoice          = $container->getParameter('ws_netvoice_url');
        $this->ejecutaScripts       = $container->getParameter('ws_netvoice_ejecuta_scripts');
        $this->ambiente             = $container->getParameter('ws_netvoice_ambiente');
        $this->restClient           = $container->get('schema.RestClient');
        $this->servicioGeneral      = $container->get('tecnico.InfoServicioTecnico');

    }
    
    /**
     * callWebService
     * Función que consulta un número en el service de netvoice
     *
     * @params $arrayDatos
     * 
     * @return $arrayResultado
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */       
    public function callWebService($arrayDatos)
    {       
        
        $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
        
        $strUrl = $this->urlNetvoice.'/'.$arrayDatos['strUrl'];
        $strToken = $arrayDatos['strToken'];
        
        unset($arrayDatos['strUrl']);
        unset($arrayDatos['strToken']);
        
        $strDatosMiddleware  = json_encode($arrayDatos);
        
        $arrayResponseJson = $this->restClient->postJSONToken($strUrl, $strDatosMiddleware , $strToken, $arrayOptions );
        
        if($arrayResponseJson['status'] == static::$intStatusOk && $arrayResponseJson['result'])
        {        
            $arrayResponse = json_decode($arrayResponseJson['result'],true);

            $arrayResultado = $arrayResponse;
        }
        else
        {
            $arrayResultado['status']      = "ERROR";
            if($arrayResponseJson['status'] == "0")
            {
                $arrayResultado['msgerroruser']  = "No Existe Conectividad con el WS.";
            }
            else
            {
                $strMensajeError = 'ERROR';

                if(isset($arrayResponseJson['msgerroruser']) && !empty($arrayResponseJson['msgerroruser']))
                {
                    $strMensajeError = $arrayResponseJson['msgerroruser'];
                }

                $arrayResultado['msgerroruser']  = "Error de WS :".$strMensajeError;
            }
        }

        return $arrayResultado ;
    }
    
    /**
     * consultarNumero
     * Función que consulta un número en el service de netvoice
     *
     * @return $strToken
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */           
    public function getToken()
    {
        
        $objParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                          ->findOneBy(array("nombreParametro"   => 'PARAMETROS_LINEAS_TELEFONIA',
                                                            "estado"            => 'Activo'));

        if(is_object($objParametro))
        {
            $objParametroPrefijo = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                        ->findOneBy(array(  "descripcion" => 'WEBSERVICE_GET_TOKEN',
                                                            "parametroId" => $objParametro->getId(),
                                                            "estado"      => 'Activo'));

            if(is_object($objParametroPrefijo))
            {
                $strUser    = $objParametroPrefijo->getValor1();
                $strClave   = $objParametroPrefijo->getValor2();
            }
        }
        
        $arrayDatos = array(
            'usuario'   => $strUser,
            'password'  => $strClave,
            'strUrl'    => 'login');

        $arrayResultado = $this->callWebService($arrayDatos);

        $strToken = $arrayResultado['token'];

        return $strToken;
    }
    
    /**
     * getCuentaNetvoice
     * service que permite obtener la cuenta de netvoice
     *
     * @params $arrayPeticiones [intServicio, strPrefijoEmpresa ]
     * 
     * @return $intCuentaNetvoice
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-12-2018 se aumentó el parámetro empresa para el aprovisionamiento de líneas netvoice en MD  
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 14-04-2019 Se agregó el parámetro de celular a $arrayData con su respectiva validación.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.3 19-06-2019 - Se modifica parámetro de respuesta en caso de error, para que retorne el mensaje del web service.
     * 
     */     
    
    public function getCuentaNetvoice($arrayParametroInfo)
    {
        $intCuentaNetvoice = 0;

        $strToken = $this->getToken();
        
        $arrayInfo = $this->emComercial->getRepository("schemaBundle:InfoPersona")->getDataUsuario($arrayParametroInfo);
        
        if($arrayInfo[0]['apellidos'] == '')
        {
            $strApellido = $arrayInfo[0]['razonSocial'];
        }
        else
        {
            $strApellido = $arrayInfo[0]['apellidos'];
        }

        if(is_array($arrayInfo))
        {
            $arrayData = array(                
                                "tipondi"           => substr($arrayInfo[0]['tipoIdentificacion'],0),
                                "ndi"               => $arrayInfo[0]['identificacionCliente'] ,
                                "nombres"           => ($arrayInfo[0]['nombres']) ? $arrayInfo[0]['nombres']:'', 
                                "apellidos"         => $strApellido,
                                "genero"            => ($arrayInfo[0]['genero']) ? $arrayInfo[0]['genero']:'',
                                "estadocivil"       => ($arrayInfo[0]['estadoCivil']) ? $arrayInfo[0]['estadoCivil']:'',
                                "fechanacimiento"   => ($arrayInfo[0]['fechanacimiento']) ? $arrayInfo[0]['fechanacimiento']:'',
                                "codigoorigen"      => ($arrayParametroInfo['strPrefijoEmpresa']) ? $arrayParametroInfo['strPrefijoEmpresa']:'',
                                "tipocuentadenwa"   => 'Postpaid',
                                "telefono1"         => ($arrayInfo[0]['telefono']) ? $arrayInfo[0]['telefono']:'',
                                "telefono2"         => "", 
                                "celular"           => ($arrayInfo[0]['celular']) ? $arrayInfo[0]['celular']:'',
                                "email1"            => ($arrayInfo[0]['correo']) ? $arrayInfo[0]['correo']:'',
                                "email2"            => "",
                                "direccion"         => ($arrayInfo[0]['direccion']) ? $arrayInfo[0]['direccion']: '',
                                "pais"              => $arrayInfo[0]['pais'] , 
                                "provincia"         => $arrayInfo[0]['provincia'], 
                                "parroquia"         => $arrayInfo[0]['parroquia'],
                                "ciudad"            => $arrayInfo[0]['canton'],
                                "login"             => $arrayInfo[0]['login'], 
                                "latitud"           => ($arrayInfo[0]['latitud']) ? $arrayInfo[0]['latitud']:'',
                                "longitud"          => ($arrayInfo[0]['longitud']) ? $arrayInfo[0]['longitud']:'',
                                'fechaingresoos'    => ($arrayInfo[0]['feCreacion']) ? $arrayInfo[0]['feCreacion']:''
                );
            
            $arrayJson = array(  "data"   => $arrayData,
                                "op"     => 'creacionclientecontrato',
                                "user"   => 'usrtelco',
                                "ip"     => '127.0.0.1',
                
                                'strUrl' => 'procesar',
                                'strToken' => $strToken);
            
            $arrayService = $this->callWebService($arrayJson);
            
            if($arrayService['status'] == 'ok')
            {
                $intCuentaNetvoice = $arrayService['datarespuesta'];
            }
            else
            {
                $intCuentaNetvoice = $arrayService['msgerroruser'];
            }
            
        }
        return $intCuentaNetvoice;
    }
    
    /**
     * asignarPlanNetvoice
     * service que permite asignar plan de netvoice a una cuenta de netvoice
     *
     * @params $arrayPeticiones [intServicio, intCuentaNetvoice ]
     * 
     * @return $strResult
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-12-2018 Se realiza validación por empresa y se asigna un tipo de plan cuando es MD
     * 
     */        
    public function asignarPlanNetvoice($arrayParametro)
    {

        $strToken = $this->getToken();
        
        $intServicio        = $arrayParametro['intServicio'];
        $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
        $strPrefijoEmpresa  = $arrayParametro['strPrefijoEmpresa'];       
        
        
        $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intServicio);
        
        if(is_object($objServicio))
        {            
            if($strPrefijoEmpresa == 'MD')
            {
                $strCaracteristica  = 'TIPO PLAN';
                $strParametro       = 'PROD_TIPO PLAN';
            }
            else
            {               
                $strCaracteristica  = 'PLAN TELEFONIA';
                $strParametro       = 'PROD_PLAN TELEFONIA';                
            }

            $objCodigoPlan = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                       $strCaracteristica, 
                                                                                       $objServicio->getProductoId());
            if(!is_object($objCodigoPlan))
            {
                return 'No se puede obtener el plan telefonía del servicio.';
            }

            $objParametroDominio = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                         ->findOneBy(array( "descripcion" => $strParametro,
                                                            'valor1'      => $objCodigoPlan->getValor(),
                                                            "estado"      => 'Activo'));

            if( is_object($objParametroDominio))
            {
                $strCodigoPlan = $objParametroDominio->getValor2();                
            }            

            $arrayData = array(                
                                    "idcuentanetvoice"  => $intCuentaNetvoice,
                                    "codigoplan"        => $strCodigoPlan
                                );

            $arrayJson = array(  "data"   => $arrayData, 
                                "op"     => 'asignarplancliente', 
                                "user"   => 'usrtelco',
                                "ip"     => '127.0.0.1',
                                'strUrl' => 'procesar',
                                'strToken' => $strToken);

            $arrayService = $this->callWebService($arrayJson);

            if($arrayService['status'] == 'ok')
            {
                $strResult = $arrayService['status'];
            }
            else
            {
                $strResult = $arrayResultado['msgerroruser'];
            } 
        
        }
        
        return $strResult;
    }
    
    /**
     * getNumero
     * service que permite obtener un número telefonico
     *
     * @params $arrayPeticiones [intCuentaNetvoice, intNumero, intPrefijoCiudad, strBusqueda, strPrefijoEmpresa ]
     * 
     * @return $arrayResult[status, numero]
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 19-12-2018 Envío el nombre del cantón en la solicitud del número
     *
     */   
    public function getNumero($arrayParametro)
    {

        try
        {
            $strToken = $this->getToken();

            $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
            $intNumero          = $arrayParametro['intNumero'];
            $intPrefijo         = $arrayParametro['intPrefijoCiudad'];
            $strPatronBusqueda  = $arrayParametro['strBusqueda'];
            $strEmpresa         = $arrayParametro['strPrefijoEmpresa'];
            $strCanton          = $arrayParametro['strCanton'];
           
            $arrayData = array(
                "idcuentanetvoice"  => ($intCuentaNetvoice) ? $intCuentaNetvoice : '0',
                "numeronetvoice"    => ($intNumero) ? $intNumero : '',
                "prefijociudad"     => ($intPrefijo) ? $intPrefijo : '',
                "patronbusqueda"    => ($strPatronBusqueda) ? $strPatronBusqueda : '',
                "empresasolicita"   => ($strEmpresa) ? $strEmpresa : '',
                "canton"            => ($strCanton) ? $strCanton : ''
            );

            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'reservarnumero',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
                $arrayRespuesta = $arrayService['datarespuesta'];
                $strNumero = $intPrefijo.'-'.$arrayRespuesta[0]['numero'];
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }

        $arrayResult = array('mensaje' => $strMensaje,
                             'numero' => $strNumero);

        return $arrayResult;
    }
    
    
    /**
     * removerNumero
     * service que permite remover un número telefonico
     *
     * @params $arrayPeticiones [intCuentaNetvoice, intNumero ]
     * 
     * @return $strMensaje
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */  
    
    public function removerNumero($arrayParametro)
    {
        try
        {
            $strToken = $this->getToken();

            $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
            $arrayNumero        = explode("-",$arrayParametro['intNumero']);
            
            $intPrefijo         = $arrayNumero[0];
            $intNumero          = $arrayNumero[1];     

            $arrayData = array(
                "idcuentanetvoice"      => ($intCuentaNetvoice) ? $intCuentaNetvoice : '0',
                "numeronetvoice"        => ($intNumero) ? $intNumero : '',
                "prefijociudad"         => $intPrefijo
            );

            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'removernumero',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }

        return $strMensaje;
    }
    
    /**
     * activarLineaNetvoice
     * service que permite activar una linea telefonica
     *
     * @params $arrayPeticiones [intCuentaNetvoice, intNumero, strClave, intCanales ]
     * 
     * @return $strMensaje
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 21-12-2018 se cambiaron los parámetros forwardtime y checksbc para la activación de líneas de megadatos
     *
     */     
    public function activarLineaNetvoice($arrayParametro)
    {

        try
        {
            $strToken = $this->getToken();

            $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
            $strClave           = $arrayParametro['strClave'];
            $arrayNumero        = explode("-",$arrayParametro['intNumero']);
            $intCanales         = strval ( $arrayParametro['intCanales']);
            $strChecksbc        = ($arrayParametro['checksbc']) ? $arrayParametro['checksbc']:'0';
            $strForwardtime     = ($arrayParametro['forwardtime']) ? $arrayParametro['forwardtime']:'1';


            $intPrefijo         = $arrayNumero[0];
            $intNumero          = $arrayNumero[1];

            $arrayData = array(
                "idcuentanetvoice"      => ($intCuentaNetvoice) ? $intCuentaNetvoice : '0',
                "numeronetvoice"        => ($intNumero) ? $intNumero : '',
                "prefijociudad"         => $intPrefijo,
                "passwordnetvoice"      => $strClave,
                "flagaccion"            => "1", //enviar 1 para inserción o  enviar 0 para actualización
                "aniprofileid"          => "1",
                "busycallforwarding"    => "1",
                "noanswercallforwarding"=> "1",
                "forwardtime"           => $strForwardtime,
                "callwaiting"           => "1",
                "callerid"              => "1",
                "calllocal"             => "1",
                "callndd"               => "1",
                "callidd"               => "1",
                "callmobiles"           => "1",
                "callespecials"         => "1",
                "concurrentcalls"       => $intCanales,
                "monitorear"            => "0",
                "callinternet"          => "1",
                "checksbc"              => $strChecksbc,
                "forwarding"            => "1",
                "callservices"          => "1",
                "suplementaryservices"  => "1",
                "tipolinea"             => "PRI"
            );

            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'asignarlineanetvoice',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }

        return $strMensaje;
    }
    
    /**
     * editarLineaNetvoice
     * service que permite editar los parametros de una línea netvoice
     *
     * @params $arrayPeticiones [intCuentaNetvoice, intNumero, strClave, intCanales ]
     * 
     * @return $strMensaje
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */       
    public function editarLineaNetvoice($arrayParametro)
    {

        try
        {
            $strToken = $this->getToken();

            $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
            $strClave           = $arrayParametro['strClave'];
            $arrayNumero        = explode("-",$arrayParametro['intNumero']);
            $intCanales         = $arrayParametro['intCanales'];

            $intPrefijo         = $arrayNumero[0];
            $intNumero          = $arrayNumero[1];

            $arrayData = array(
                "idcuentanetvoice"      => ($intCuentaNetvoice) ? $intCuentaNetvoice : '0',
                "numeronetvoice"        => ($intNumero) ? $intNumero : '',
                "prefijociudad"         => $intPrefijo,
                "passwordnetvoice"      => $strClave,
                "flagaccion"            => "0", //enviar 1 para inserción o  enviar 0 para actualización
                "aniprofileid"          => "1",
                "busycallforwarding"    => "1",
                "noanswercallforwarding"=> "1",
                "forwardtime"           => "1",
                "callwaiting"           => "1",
                "callerid"              => "1",
                "calllocal"             => "1",
                "callndd"               => "1",
                "callidd"               => "1",
                "callmobiles"           => "1",
                "callespecials"         => "1",
                "concurrentcalls"       => $intCanales,
                "monitorear"            => "0",
                "callinternet"          => "1",
                "checksbc"              => "0",
                "forwarding"            => "1",
                "callservices"          => "1",
                "suplementaryservices"  => "1",
                "tipolinea"             => "PRI"
            );

            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'asignarlineanetvoice',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }

        return $strMensaje;
    }    
    

    /**
     * getDetalleLLamadas
     * service que permite obtener el detalle de llamadas de un número mediante un rango de fechas
     *
     * @params $arrayPeticiones [intCuentaNetvoice, intNumero, strFechaIni, strFechaFin ]
     * 
     * @return $strMensaje
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */       
    public function getDetalleLLamadas($arrayParametro)
    {
        
        try
        {
            $strToken = $this->getToken();
            
            $arrayData          = null;
            $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
            $arrayNumero        = explode("-",$arrayParametro['intNumero']);
            $strFechaIni        = $arrayParametro['strFechaIni'];
            $strFechaFin        = $arrayParametro['strFechaFin'];

            $intPrefijo         = $arrayNumero[0];
            $intNumero          = $arrayNumero[1];

            $arrayData = array(
                "idcuentanetvoice"      => ($intCuentaNetvoice) ? $intCuentaNetvoice : '0',
                "numeronetvoice"        => ($intNumero) ? $intNumero : '',
                "prefijociudad"         => $intPrefijo,
                "tiporeporte"           => 'S',
                "fechainicio"           => str_replace("T"," ",$strFechaIni),
                "fechafin"              => str_replace("T"," ",$strFechaFin)
            );
            
            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'detalledellamada',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
                $arrayData = $arrayService['datarespuesta'] ;
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }

        $arrayResult = array('mensaje' => $strMensaje, 'data' => $arrayData );
        
        return $arrayResult;        
    }
    
    /**
     * getDetalleLLamadas
     * service que permite obtener el detalle de llamadas de un número mediante un rango de fechas
     *
     * @params $arrayPeticiones [intCuentaNetvoice, intNumero, strStatus]
     * 
     * @return $strMensaje
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */       
    public function cambiarEstadoNumero($arrayParametro)
    {
        
        try
        {
            $strToken = $this->getToken();
            
            $intCuentaNetvoice  = $arrayParametro['intCuentaNetvoice'];
            $arrayNumero        = explode("-",$arrayParametro['intNumero']);
            //Posibles valores: 'Enabled', 'Suspended'= Cortadas Llamadas Salientes  o 'Disabled'= Cortadas llamadas salientes y entrantes
            $strStatus          = $arrayParametro['strStatus'];

            $intPrefijo         = $arrayNumero[0];
            $intNumero          = $arrayNumero[1];
            
            $arrayData = array(
                "idcuentanetvoice"      => ($intCuentaNetvoice) ? $intCuentaNetvoice : '0',
                "numeronetvoice"        => ($intNumero) ? $intNumero : '',
                "prefijociudad"         => $intPrefijo,
                "nuevostatus"           => $strStatus
            );
            
            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'cambiostatusnumero',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }
        
        return $strMensaje;        
    }
    
     /**
     * configuracionTroncal
     * service para configurar la ip cuando es una categoria SIP TRUNK
     *
     * @params $arrayPeticiones [strIp, intNumero, strAccion]
     * 
     * @return $strMensaje
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     */       
    public function configuracionTroncal($arrayParametro)
    {
        
        try
        {
            $strToken = $this->getToken();
            
            $strIp              = $arrayParametro['strIp'];
            $arrayNumero        = explode("-",$arrayParametro['intNumero']);
            $strAccion          = $arrayParametro['strAccion'];//opciones: SET o REMOVE

            $intPrefijo         = $arrayNumero[0];
            $intNumero          = $arrayNumero[1];
            
            $arrayData = array(
                "numeronetvoice"    => ($intNumero) ? $intNumero : '',
                "prefijociudad"     => $intPrefijo,
                "iptroncal"         => $strIp,
                "accion"            => $strAccion                
            );
            
            $arrayJson = array(  "data"      => $arrayData,
                                "op"        => 'configurartroncal',
                                "user"      => 'usrtelco',
                                "ip"        => '127.0.0.1',
                                'strUrl'    => 'procesar',
                                'strToken'  => $strToken);

            $arrayService = $this->callWebService($arrayJson);
            if($arrayService['status'] == 'ok')
            {
                $strMensaje = 'OK';
            }
            else
            {
                $strMensaje = $arrayService['msgerroruser'];
            }
        }
        catch(\Exception $ex)
        {
            $strMensaje = $ex->getMessage();
        }
        
        return $strMensaje;        
    }
    
    
}
