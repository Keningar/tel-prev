<?php

namespace telconet\tecnicoBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;

/**
 * Clase para llamar a metodos ws provisionados por networking para la ejecucion de script sobre los equipos de TN
 * Requiere configuracion en service.yml de tecnicoBundle, ejemplo:
 * # URL de WS
 *     # rutas de ws para ejecucion de los scripts
 *     ws_networking_scripts_url: https://ws-tecnico.telconet.ec/ws/telcos/servicios  
 * @author Allan Suarez <arsuarez@telconet.ec>
 */
class NetworkingScriptsService
{
    
    /**
     * Codigo de respuesta: Respuesta valida
     */
    public static $STATUS_OK = 200;    

    /**
     * Codigo de respuesta: Respuesta incorrecta por fallo en script
     */
    public static $STATUS_COMUNICATION_ERROR = 404;
    
    /**
     * Codigo de respuesta: Respuesta incorrecta por fallo en el equipo
     */
    public static $STATUS_SCRIPT_ERROR = 403;
    
    /**
     * Codigo de respuesta: Respuesta incorrecta por fallo en el equipo
     */
    public static $STATUS_TOKEN_ERROR = 402;
    
    /**
     *
     * @var string
     */
    private $webServiceNetworkingRestURL;              
    
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
    
    private $ejecutaScripts;
    
    private $tipoEjecucion;
    
    private $ambienteEjecuta;
    
    //Entity Manager
    private $emGeneral;
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $serviceUtil;
    private $serviceSoporte;
    private $container;

    public function __construct(Container $container) 
    {                
        $this->container                   = $container;
        $this->emInfraestructura           = $container->get('doctrine')->getManager('telconet_infraestructura');        
        $this->emComercial                 = $container->get('doctrine')->getManager('telconet');        
        $this->emGeneral                   = $container->get('doctrine')->getManager('telconet_general');
        $this->emSoporte                   = $container->get('doctrine')->getManager('telconet_soporte');
        $this->webServiceNetworkingRestURL = $container->getParameter('ws_networking_scripts_url');              
        $this->ejecutaScripts              = $container->getParameter('ws_networking_scripts_ejecuta');  
        $this->tipoEjecucion               = $container->getParameter('ws_networking_scripts_tipo_ejecucion'); 
        $this->ambienteEjecuta             = $container->getParameter('networking_scripts_ambiente_ejecuta'); 
        $this->restClient                  = $container->get('schema.RestClient');
        $this->mailer                      = $container->get('schema.Mailer');
        $this->serviceUtil                 = $container->get('schema.Util');
        $this->serviceSoporte              = $container->get('soporte.soporteservice');
        $this->webServiceMiddlewareCertURL = $container->getParameter('ws_middleware_cert_url'); 
        $this->webServiceTokenCertURL      = $container->getParameter('ws_token_cert_url'); 
        
    }

    /**
     * callNetworkingWebService
     * 
     * Metodo llamado por los services que realizan los diferentes procesos sobre los productos de TN y ejecutan scripts mediante WS
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0
     * @since 14-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1
     * @since 06-10-2017 - se adaṕta para consultas de servicios de INTERNETDC
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2
     * @since 19-04-2018 - se adaṕta para consultas de servicios de DATOSDC
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3
     * @since 22-10-2018 - cuando el servicio es NETVOICE-L3MPLS se requiere que los scripts se ejecuten en el webService de Networking
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.4
     * @since 05-08-2019 - Se agrega el producto L3MPLS SDWAN a las validaciones de ejecución del ws.
     *
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.5
     * @since 31-10-2019 - Se agrega validación de concentrador CONCINTER FWA, para poder llamar a los webservices
     *                     de networking.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6
     * @since 01-06-2020 - Se valida el máximo ancho de banda de la interface del elemento cuando se actualice el BW del servicio
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.7
     * @since 16-02-2022 - Se agrega al arreglo del servicio DIRECTLINK-L3MPLS $arrayServicio [] = "DIRECTLINK-L3MPLS"
     *
     * @param  Array $arrayPeticiones [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return Array $arrayRespuesta  [ status , mensaje ]
     */
    public function callNetworkingWebService($arrayPeticiones)
    {
        $arrayRespuesta  = array();        
        
        $servicio = $arrayPeticiones['servicio'];
                
        //Si no se entra en ninguna condicional tanto de accion o de tipo de producto la variable no es cambiada y solo termina la ejecucion
        $booleanEjecuta = false;
        $arrayServicio [] = "INTMPLS";
        $arrayServicio [] = "L3MPLS";
        $arrayServicio [] = "INTERNET";
        $arrayServicio [] = "GENERAL";
        $arrayServicio [] = "INTERNETDC";
        $arrayServicio [] = "CONCINTER";
        $arrayServicio [] = "DATOSDC";
        $arrayServicio [] = "L2MPLS";
        $arrayServicio [] = "NETVOICE-L3MPLS";
        $arrayServicio [] = "L3MPLS SDWAN";
        $arrayServicio [] = "CONCINTER FWA";
        $arrayServicio [] = "INTERNET SDWAN";
        $arrayServicio [] = "DIRECTLINK-L3MPLS";
        
        if(in_array($servicio , $arrayServicio))
        {                       
            $booleanEjecuta = true;
        }
        
        //Ejecucion del metodo via WS para realizar la configuracion del SW
        if($booleanEjecuta)
        {
            $arrayPeticiones['ejecuta']        = $this->ejecutaScripts;
            $arrayPeticiones['tipo_ejecucion'] = $this->tipoEjecucion;

            $arrayRespuesta = $this->executeScript($arrayPeticiones);
        }
        else
        {
            $arrayRespuesta['status']  = "OK";
            $arrayRespuesta['mensaje'] = "";
        }

        //verificar si ejecuto el ws networking
        if( $arrayRespuesta['status'] == "OK" )
        {
            //validar maximo ancho de banda de la interface del elemento
            $this->validarBwMaximoServiciosPorInterface($arrayPeticiones);
        }

        return $arrayRespuesta;
    }
    
    /**
     * generateJson
     * 
     * Metodo que se encargado de generar el array a enviarle al metodo establecido para ejecucion de script en el
     * Rest Web Service Tecnicos Telcos de Networking
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.0
     * @since 12-04-2016
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 14-06-2016
     * Se agrega case para crear cacti
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 06-01-2017
     * Se agrega parametros y modifica el metodo adminEnrutamientoPe para ejecucion de enrutamiento
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3
     * @since 06-10-2017 - se adaṕta para consultas de servicios de INTERNETDC para verificar Subred ( checkSubnet )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4
     * @since 28-05-2018 - Se agrega metodo para configuracion de enlaces L2mpls DC
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 18-07-2018 - Se envia nuevo parametro: ( vrf )  al metodo getPeBySwitch
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 06-03-2019 - Se agrega la opción: consultarPeViaJurisdiccion
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 19-06-2019 - Se agregan los parametros: razon_social,rt_export,rt_import al método -> configPE
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.8 05-12-2019 - Se agrega las opciones: cambio_cpe, cambio_um y migracion_anillo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.9 31-03-2020 - Se agrega la configuración del protocolo a la opción de migracion_anillo
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.10 12-05-2020 - Se agrega la llave 'clase_servicio' a la opción configSW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 22-06-2020 - Se agrega la opción: getInterfaceBw
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.1 10-07-2020 - Se agrega la opción: validarEnrutamientoEstaticoPe, que sirve para validar si una subred no esta siendo usada
     *                            por otro cliente
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.2 03-05-2021 - Se agrega la opción: configswipv6, que sirve para activar temporalmente un servicio del flujo zerotouch.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 24-03-2021 - Se agrega las opciones: manageIPv6, getInterfacesPE, assignResources, setupPE, getResources y rollBack
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.3 27-07-2021 - Se modifica la opción: manageIPv6
     *
     * @param  Array $arrayParametros [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return json {   
     *                  accion : 'accion',
     *                  token  : 'token',
     *                  data   : {arrayData},
     *                  data-auditoria : {arrayDataAuditoria}
     *              }
     */
    private function generateJson($arrayParametros)
    {        
        $arrayDataAuditoria = array
                                (
                                    'servicio' => $arrayParametros['servicio'],
                                    'login_aux'=> $arrayParametros['login_aux'],
                                    'user_name'=> $arrayParametros['user_name'],
                                    'user_ip'  => $arrayParametros['user_ip']
                                );    
        
        $arrayData = array();
        
        /* Variable que contiene la URL de acuerdo al metodo a invocar en el WS
         * 
         * - Consultas en aplicativos de networking
         *   - getPeBySwitch, showRunInterface, showRunPE
         * - Tecnico provisioning:
         *   - configBW, configMAC, configSW, configPE, provisioning, cambio_cpe, cambio_um, migracion_anillo
         * - Tecnico enrutamientos dinamicos y estaticos:
         *   - enrutamientoDinamicoPe, adminEnrutamientoPe, enrutamientoEstaticoPe, validarEnrutamientoEstaticoPe
         * 
         */
        $strUrl = $arrayParametros['url'];

        switch($strUrl)
        {
            // Consultas en aplicativos de networking
            case 'ejecutarCacti':
                $arrayData = array(
                                    'login_aux'                 => $arrayParametros['login_aux'],
                                    'login_master'              => $arrayParametros['login_master'],
                                    'password_master'           => $arrayParametros['password_master'],
                                    'city'                      => $arrayParametros['city'],
                                    'email_contacto_tecnico'    => $arrayParametros['email_contacto_tecnico'],
                                    'hostname'                  => $arrayParametros['hostname'],
                                    'ip'                        => $arrayParametros['ip'],
                                    'sw'                        => $arrayParametros['sw'],
                                    'pto'                       => $arrayParametros['pto'],
                                    'propiedad_cpe'             => $arrayParametros['propiedad_cpe']
                                  );
                break;
            case 'getPeBySwitch':
                $arrayData = array(
                                    'sw'  => $arrayParametros['sw'],
                                    'vrf' => $arrayParametros['vrf']
                                  );
                break;
            case 'consultarPeViaJurisdiccion':
                $arrayData = array(
                                    'jurisdiccion'  => $arrayParametros['jurisdiccion'],
                                    'user'          => $arrayParametros['user']
                                  );
                break;
            case 'getSwitchesByPe':
                $arrayData = array(
                                    'pe' => $arrayParametros['pe']
                                  );
                break;
            case 'showRunInterface':
                $arrayData = array(
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'dispositivo' => $arrayParametros['dispositivo'],
                                    'pto'         => $arrayParametros['pto']
                                  );
                break;
            case 'showRunSW':
                $arrayData = array(
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'dispositivo' => $arrayParametros['dispositivo'],
                                    'pto'         => $arrayParametros['pto']
                                  );
                break;
            case 'showRunPE':
                $arrayData = array(
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'dispositivo'=> $arrayParametros['dispositivo'],
                                    'pto'        => $arrayParametros['pto'],
                                    'vrf'        => $arrayParametros['vrf']
                                  );
                break;
            // Tecnico provisioning
            case 'configBW':
                $arrayData = array(
                                    'ejecuta'        => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'sw'             => $arrayParametros['sw'],
                                    'anillo'         => $arrayParametros['anillo'],
                                    'pto'            => $arrayParametros['pto'],                                    
                                    'bw'             => array(
                                                                'bw_up'   => $arrayParametros['bw_up']<0?0:$arrayParametros['bw_up'],
                                                                'bw_down' => $arrayParametros['bw_down']<0?0:$arrayParametros['bw_down'],
                                                             )                                    
                                  );
                break;            
            case 'configMAC':
                $arrayData = array(
                                    'ejecuta'        => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'sw'             => $arrayParametros['sw'],
                                    'anillo'         => $arrayParametros['anillo'],
                                    'pto'            => $arrayParametros['pto'],
                                    'macVlan'        => $arrayParametros['macVlan'],
                                    'descripcion'    => $arrayParametros['descripcion']                                                                      
                                  );
                break;
            case 'configSW':                
                $arrayData = array(
                                    'ejecuta'        => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'clase_servicio' => $arrayParametros['servicio'],
                                    'sw'             => $arrayParametros['sw'],
                                    'anillo'         => $arrayParametros['anillo'],                                    
                                    'pto'            => $arrayParametros['pto'],
                                    'macVlan'        => $arrayParametros['macVlan'],
                                    'descripcion'    => $arrayParametros['descripcion'],                                    
                                    'bw'             => array(
                                                                'bw_up'   => $arrayParametros['bw_up']<0?0:$arrayParametros['bw_up'],
                                                                'bw_down' => $arrayParametros['bw_down']<0?0:$arrayParametros['bw_down'],
                                                             )                                    
                                  );                
                break;
            case 'configPE':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],
                                    'sw'                   => $arrayParametros['sw'],
                                    'clase_servicio'       => $arrayParametros['clase_servicio'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'pe'                   => $arrayParametros['pe'],
                                    'anillo'               => $arrayParametros['anillo'],
                                    'vlan'                 => $arrayParametros['vlan'],                                   
                                    'subred'               => $arrayParametros['subred'],
                                    'mascara'              => $arrayParametros['mascara'],
                                    'gateway'              => $arrayParametros['gateway'],
                                    'rd_id'                => $arrayParametros['rd_id'],
                                    'descripcion_interface'=> $arrayParametros['descripcion_interface'],
                                    'ip_bgp'               => $arrayParametros['ip_bgp'],
                                    'asprivado'            => $arrayParametros['asprivado'],
                                    'nombre_sesion_bgp'    => $arrayParametros['nombre_sesion_bgp'],
                                    'default_gw'           => $arrayParametros['default_gw'],
                                    'protocolo'            => $arrayParametros['protocolo'],
                                    'tipo_enlace'          => $arrayParametros['tipo_enlace'],
                                    'weight'               => $arrayParametros['weight'],
                                    'banderaBravco'        => $arrayParametros['banderaBravco'],
                                    'razon_social'         => $arrayParametros['razon_social'],
                                    'rt_export'            => $arrayParametros['rt_export'],
                                    'rt_import'            => $arrayParametros['rt_import']
                                  );
                break;
            case 'provisioning':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],
                                    'sw'                   => $arrayParametros['sw'],
                                    'pto'                  => $arrayParametros['pto'],
                                    'macVlan'              => $arrayParametros['macVlan'],
                                    'descripcion'          => $arrayParametros['descripcion'],
                                    'bw'                   => array(
                                                                    'bw_up'   => $arrayParametros['bw_up']<0?0:$arrayParametros['bw_up'],
                                                                    'bw_down' => $arrayParametros['bw_down']<0?0:$arrayParametros['bw_down'],
                                                                   ), 
                                    'clase_servicio'       => $arrayParametros['clase_servicio'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'pe'                   => $arrayParametros['pe'],
                                    'anillo'               => $arrayParametros['anillo'],
                                    'vlan'                 => $arrayParametros['vlan'],                                   
                                    'subred'               => $arrayParametros['subred'],
                                    'mascara'              => $arrayParametros['mascara'],
                                    'gateway'              => $arrayParametros['gateway'],
                                    'numero_vrf'           => $arrayParametros['numero_vrf'],
                                    'descripcion_pe'       => $arrayParametros['descripcion_pe'],
                                    'ip_bgp'               => $arrayParametros['ip_bgp'],
                                    'asprivado'            => $arrayParametros['asprivado'],
                                    'nombre_sesion_bgp'    => $arrayParametros['nombre_sesion_bgp'],
                                    'default_gw'           => $arrayParametros['default_gw'],
                                    'protocolo'            => $arrayParametros['protocolo'],
                                    'tipo_enlace'          => $arrayParametros['tipo_enlace'],
                                    'weight'               => $arrayParametros['weight'],
                                    'routemap_name'        => $arrayParametros['routemap_name'],
                                    'routemap_prefix_name' => $arrayParametros['routemap_prefix_name'],
                                    'banderaBravco'        => $arrayParametros['banderaBravco']
                                  );
                break;
            case 'configL2':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],                                                                        
                                    'pe'                   => $arrayParametros['pe'],                                    
                                    'ip_loopback'          => $arrayParametros['ip_loopback'],                                                                       
                                    'puerto'               => $arrayParametros['puerto'],                                   
                                    'vc'                   => $arrayParametros['vc'],
                                    'l2_ip'                => $arrayParametros['l2_ip'],
                                    'concentrador'         => $arrayParametros['concentrador'],
                                    'desc'                 => $arrayParametros['desc']
                                  );
                break;
            case 'cambio_cpe':
                $arrayData = array(
                                    'ejecuta'        => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'dispositivo'    => $arrayParametros['sw'],
                                    'puerto'         => $arrayParametros['pto'],
                                    'anillo'         => $arrayParametros['anillo'],
                                    'cpe_anterior'   => $arrayParametros['cpe_anterior'],
                                    'cpe_nuevo'      => $arrayParametros['cpe_nuevo'],
                                    'descripcion'    => $arrayParametros['descripcion'],
                                    'ambiente'       => $arrayParametros['tipo_ejecucion']
                                  );
                break;
            case 'cambio_um':
                $arrayData = array(
                                    'ejecuta'           => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'    => $arrayParametros['tipo_ejecucion'],
                                    'anillo'            => $arrayParametros['anillo'],
                                    'sw_anterior'       => $arrayParametros['sw_anterior'],
                                    'puerto_anterior'   => $arrayParametros['pt_anterior'],
                                    'sw_nuevo'          => $arrayParametros['sw_nuevo'],
                                    'puerto_nuevo'      => $arrayParametros['pt_nuevo'],
                                    'cpe_anterior'      => $arrayParametros['cpe_anterior'],
                                    'cpe_nuevo'         => $arrayParametros['cpe_nuevo'],
                                    'bw_anterior_um'    => $arrayParametros['bw_anterior'],
                                    'bw_nueva_um'       => $arrayParametros['bw_nueva'],
                                    'descripcion'       => $arrayParametros['descripcion'],
                                    'ambiente'          => $arrayParametros['tipo_ejecucion']
                                  );
                break;
            case 'migracion_anillo':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],
                                    'servicio'             => $arrayParametros['servicio'],
                                    'sw_anterior'          => $arrayParametros['sw_anterior'],
                                    'puerto_anterior'      => $arrayParametros['pt_anterior'],
                                    'sw_nuevo'             => $arrayParametros['sw_nuevo'],
                                    'puerto_nuevo'         => $arrayParametros['pt_nuevo'],
                                    'cpe_anterior'         => $arrayParametros['cpe_anterior'],
                                    'cpe_nuevo'            => $arrayParametros['cpe_nuevo'],
                                    'bw_anterior_um'       => $arrayParametros['bw_anterior'],
                                    'bw_nueva_um'          => $arrayParametros['bw_nueva'],
                                    'interfaz_pe_anterior' => $arrayParametros['interfaz_pe_anterior'],
                                    'interfaz_pe_nueva'    => $arrayParametros['interfaz_pe_nueva'],
                                    'rutas_anteriores'     => $arrayParametros['rutas_anteriores'],
                                    'rutas_nuevas'         => $arrayParametros['rutas_nuevas'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'razon_social'         => $arrayParametros['razon_social'],
                                    'login'                => $arrayParametros['login'],
                                    'descripcion'          => $arrayParametros['descripcion'],
                                    'ambiente'             => $arrayParametros['tipo_ejecucion'],
                                    'protocolo'            => $arrayParametros['protocolo'],
                                    'ip_bgp_anterior'      => $arrayParametros['ip_bgp_anterior'],
                                    'ip_bgp_nueva'         => $arrayParametros['ip_bgp_nueva']
                                  );
                break;
            // Tecnico enrutamientos dinamicos y estaticos
            case 'enrutamientoDinamicoPe':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],                                    
                                    'clase_servicio'       => $arrayParametros['clase_servicio'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'pe'                   => $arrayParametros['pe'],                                    
                                    'vlan'                 => $arrayParametros['vlan'],                                                                       
                                    'gateway'              => $arrayParametros['gateway'],                                   
                                    'ip_bgp'               => $arrayParametros['ip_bgp'],
                                    'asprivado'            => $arrayParametros['asprivado'],
                                    'nombre_sesion_bgp'    => $arrayParametros['nombre_sesion_bgp'],
                                    'default_gw'           => $arrayParametros['default_gw'],
                                    'protocolo'            => $arrayParametros['protocolo'],
                                    'tipo_enlace'          => $arrayParametros['tipo_enlace'],
                                    'banderaBravco'        => $arrayParametros['banderaBravco'],
                                    'razon_social'         => $arrayParametros['razon_social'],
                                    'rt_export'            => $arrayParametros['rt_export'],
                                    'rt_import'            => $arrayParametros['rt_import']
                                  );
                break;
            case 'adminEnrutamientoPe':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],                                    
                                    'clase_servicio'       => $arrayParametros['clase_servicio'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'pe'                   => $arrayParametros['pe'],
                                    'ip_bgp'               => $arrayParametros['ip_bgp'],
                                    'asprivado'            => $arrayParametros['asprivado'],                                  
                                    'protocolo'            => $arrayParametros['protocolo'],
                                    'weight'               => $arrayParametros['weight'],                                  
                                    'redistribute_bgp_protocolo' => $arrayParametros['redistribute_bgp_protocolo'],
                                    'redistribute_bgp_type'      => $arrayParametros['redistribute_bgp_type'],  
                                    'redistribute_bgp_metric'    => $arrayParametros['redistribute_bgp_metric'],
                                    'redistribute_bgp_routemap'  => $arrayParametros['redistribute_bgp_routemap'],
                                    'redistribute_bgp_match'     => $arrayParametros['redistribute_bgp_match'],
                                    'routemap_name'        => $arrayParametros['routemap_name'],
                                    'routemap_type'        => $arrayParametros['routemap_type'],
                                    'routemap_prefix_name' => $arrayParametros['routemap_prefix_name'],
                                    'routemap_prefix_list' => $arrayParametros['routemap_prefix_list']
                                  );
                break;            
            case 'enrutamientoEstaticoPe':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],                                    
                                    'clase_servicio'       => $arrayParametros['clase_servicio'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'pe'                   => $arrayParametros['pe'],
                                    'sw'                   => $arrayParametros['sw'],    
                                    'olt'                  => $arrayParametros['olt'],
                                    'name_route'           => $arrayParametros['name_route'],               
                                    'net_lan'              => $arrayParametros['net_lan'], 
                                    'mask_lan'             => $arrayParametros['mask_lan'],
                                    'ip_destino'           => $arrayParametros['ip_destino'],                                    
                                    'distance_admin'       => $arrayParametros['distance_admin']                               
                                  );
                break;
            case 'validarEnrutamientoEstaticoPe':
                $arrayData = array(
                                    'ejecuta'              => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],
                                    'clase_servicio'       => $arrayParametros['clase_servicio'],
                                    'vrf'                  => $arrayParametros['vrf'],
                                    'pe'                   => $arrayParametros['pe'],
                                    'sw'                   => $arrayParametros['sw'],
                                    'olt'                  => $arrayParametros['olt'],
                                    'net_lan'              => $arrayParametros['net_lan'],
                                    'mask_lan'             => $arrayParametros['mask_lan'],
                                    'ip_destino'           => $arrayParametros['ip_destino']
                                  );
                break;
            case 'checkSubnet':
                $arrayData = array(
                                    'servicio'             => $arrayParametros['servicio'],                                    
                                    'subred'               => $arrayParametros['subred'],
                                    'mask'                 => $arrayParametros['mask'],
                                    'login'                => $arrayParametros['login']
                                  );
                break;
            case 'configswipv6':
                $arrayData = array(
                    'ejecuta'              => $arrayParametros['ejecuta'],
                    'tipo_ejecucion'       => $arrayParametros['tipo_ejecucion'],
                    'sw'                   => $arrayParametros['sw'],
                    'anillo'               => $arrayParametros['anillo'],
                    'pto'                  => $arrayParametros['pto'],
                    'mac'                  => $arrayParametros['mac'],
                    'descripcion'          => $arrayParametros['descripcion']
                );
                break;
            case 'getInterfaceBw':
                $arrayData = array(
                                    'ejecuta'        => $arrayParametros['ejecuta'],
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'sw'             => $arrayParametros['sw'],
                                    'interfaces'     => $arrayParametros['interfaces'],
                                  );
                break;
            // Tecnico provisioning GPON
            case 'manageIPv6':
                $arrayData = array(
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'pe'             => $arrayParametros['pe'],
                                    'id_agregador'   => $arrayParametros['id_agregador'],
                                    'agregador'      => $arrayParametros['agregador'],
                                    'id_olt'         => $arrayParametros['id_olt'],
                                    'olt'            => $arrayParametros['olt'],
                                    'ipv6'           => $arrayParametros['ipv6']
                                  );
                break;
            case 'getInterfacesPE':
            case 'setupPE':
            case 'getResources':
            case 'rollBack':
                $arrayData = array(
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'pe'             => $arrayParametros['pe'],
                                    'id_olt'         => $arrayParametros['id_olt'],
                                    'olt'            => $arrayParametros['olt']
                                  );
                break;
            case 'assignResources':
                $arrayData = array(
                                    'tipo_ejecucion' => $arrayParametros['tipo_ejecucion'],
                                    'pe'             => $arrayParametros['pe'],
                                    'id_olt'         => $arrayParametros['id_olt'],
                                    'olt'            => $arrayParametros['olt'],
                                    'interfaces'     => $arrayParametros['interfaces']
                                  );
                break;
        }
        
        $jsonArray = array
                        (
                            "accion"         => $arrayParametros['accion'],                                                    
                            "data"           => $arrayData,
                            "data-auditoria" => $arrayDataAuditoria
                        );
        
        return json_encode($jsonArray);
    }
    
    /**
     * executeScript
     * 
     * Metodo que se encargado de enviarle via rest la informacion requerida al WS establecido por networking para la ejecucion
     * de los script hacia los equipos de TN
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 24-03-2021
     * Se agrega las configuraciones para las url: manageIPv6, getInterfacesPE, assignResources, setupPE, getResources y rollBack
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 22-06-2020
     * Se verifica si la url es getInterfaceBw para obtener el switch y los datos de las interfaces
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 09-03-2017
     * Se modifica array de envio de error de NW enviado mensaje generado en Ws
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 14-11-2016  
     * Se modifica string de mensaje que se devuelve al usuario cuando el error proviene de networking
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.2 8-06-2016  
     * Se actualiza funcion para que reciba dentro de mensaje el pe y los sw
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se envia [ statusCode ] para validar respuesta en WS que provee el Telcos
     * @since 21-05-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @author Eduardo Plua <eplua@telconet.ec>
     * @version 1.0
     * @since 12-04-2016
     *
     * @param  Array $arrayParametros [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return Array $arrayResultado [ status , mensaje , band , comandos, nombre_pe ]
     */
    private function executeScript($arrayParametros)
    {
        //Se genera el json a enviar al ws por tipo de proceso a ejecutar
        $data_string    = $this->generateJson($arrayParametros);

        $strUrl = $this->webServiceNetworkingRestURL.'/'.$this->getUrl($arrayParametros['url']);

        if($this->ambienteEjecuta == "S")
        {        
            //Se obtiene el resultado de la ejecucion via rest hacia el ws  
            $options = array(CURLOPT_SSL_VERIFYPEER => false);
            
            $responseJson = $this->restClient->postJSON($strUrl, $data_string , $options);
            
            //Validamos que se haya realizado el proceso correctamente
            if($responseJson['status'] == self::$STATUS_OK && $responseJson['result'] != false)
            {        
                $arrayResponse = json_decode($responseJson['result'],true);

                $arrayResultado = array();

                $resultStatus                = $arrayResponse['status'];
                $arrayResultado['mensaje']   = isset($arrayResponse['msg']) ? $arrayResponse['msg'] : $arrayResponse['message'];
                $arrayResultado['band']      = $arrayResponse['band'];
                $arrayResultado['raw']       = $arrayResponse;
                if(count($arrayResponse) > 3)
                {
                    $arrayResultado['comandos']  = $arrayResponse['comandos'];
                }                
                if( $arrayParametros['url'] == 'getInterfaceBw' )
                {
                    $arrayResultado['sw']         = $arrayResponse['sw'];
                    $arrayResultado['interfaces'] = $arrayResponse['interfaces'];
                }
                if($resultStatus==self::$STATUS_OK || $resultStatus == 'ok')
                {
                    $arrayResultado['status']      = "OK";     
                    $arrayResultado['statusCode']  = self::$STATUS_OK;
                }
                else // status 404, 403, 402 ( Errores de script o validaciones )
                {
                    $arrayResultado['status']  = "ERROR";
                    $arrayResultado['mensaje'] = "Error de Networking : [".$resultStatus."] ".$arrayResultado['mensaje'];                    
                    $arrayResultado['statusCode']  = 500;                                        
                }        
                if( $arrayParametros['url'] == 'manageIPv6' )
                {
                    $arrayResultado['pe'] = $arrayResponse['pe'];
                }
                if( $arrayParametros['url'] == 'getInterfacesPE' || $arrayParametros['url'] == 'assignResources'
                    || $arrayParametros['url'] == 'setupPE' || $arrayParametros['url'] == 'getResources'
                    || $arrayParametros['url'] == 'rollBack' )
                {
                    $arrayResultado['data'] = $arrayResponse['data'];
                }
                if( $arrayResponse['message'] == "ERROR" && ( $arrayParametros['url'] == 'manageIPv6'
                    || $arrayParametros['url'] == 'getInterfacesPE' || $arrayParametros['url'] == 'assignResources'
                    || $arrayParametros['url'] == 'setupPE' || $arrayParametros['url'] == 'getResources'
                    || $arrayParametros['url'] == 'rollBack' ) )
                {
                    $arrayResultado['status']  = "ERROR";
                    $arrayResultado['mensaje'] = $arrayResponse['data'];
                    $arrayResultado['data']    = array();
                }
            }
            else
            {
                $arrayResultado['status']      = "ERROR";
                if($responseJson['status'] == "0")
                {
                    $arrayResultado['mensaje']     = "No Existe Conectividad con el WS Networking.";
                }
                else
                {
                    $strMensajeError = 'ERROR';
                    
                    if(isset($responseJson['mensaje']) && !empty($responseJson['mensaje']))
                    {
                        $strMensajeError = $responseJson['mensaje'];
                    }
                    
                    $arrayResultado['mensaje']     = "Error de Networking :".$strMensajeError;
                }
                
                $arrayResultado['statusCode']  = 500;
            }
        }
        else
        {
            $arrayResultado['status']     = "OK";
            $arrayResultado['mensaje']    = $data_string;
            $arrayResultado['statusCode'] = self::$STATUS_OK;
        }

        return $arrayResultado;
    }
    
    /**
    * Obtiene el context path prefix que se debe utilizar en la llamada a ws de networking por medio del metodo/accion que se desea ejecutar
    *
    * @author Eduardo Plua <eplua@telconet.ec>
    * @version 1.1 26-05-2016 - Se recupera elementoPe desde ws networking
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.2 06-10-2017 - Se agregar opcion de consulta de verificaciones de Subredes publicas para producto de Internet DC
    * 
    * @author Allan Suarez <arsuarez@telconet.ec>
    * @version 1.3 28-05-2017 - Se agregar opcion de aprovisionamiento para servicios de datos l2mpls DC
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.4 06-03-2019 - Se agrega la opción: consultarPeViaJurisdiccion
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.5 05-12-2019 - Se agrega las opciones: cambio_cpe, cambio_um y migracion_anillo
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.6 22-06-2020 - Se agrega la opción: getInterfaceBw
    * 
    * @author Pablo Pin <ppin@telconet.ec>
    * @version 1.7 03-05-2021 - Se agrega la opción: zerotouch => configswipv6 para el flujo de ZeroTouch.
    *
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.7 24-03-2021 - Se agrega las opciones: manageIPv6, getInterfacesPE, assignResources, setupPE, getResources y rollBack
    *
    * @param $strUrl nombre de metodo/accion rest a ejecutar
    */
    private function getUrl($strUrl)
    {
        $arrayUrls = array(
            'cacti'         => array('ejecutarCacti'),
            'consultas'     => array('getSwitchesByPe','getPeBySwitch','showRunInterface','showRunPE','showRunSW','checkSubnet',
                                     'consultarPeViaJurisdiccion','getInterfaceBw'),
            'servicios'     => array('configBW','configMAC','configSW','configPE','provisioning','cambio_cpe','cambio_um','migracion_anillo'),
            'enrutamientos' => array('enrutamientoDinamicoPe','adminEnrutamientoPe','enrutamientoEstaticoPe','validarEnrutamientoEstaticoPe'),
            'provisioning'  => array('configL2'),
            'olt'           => array('manageIPv6','getInterfacesPE','assignResources','setupPE','getResources','rollBack'),
            'zerotouch'     => array('configswipv6')
        );
        
        $prefixUrl = "";
        
        foreach($arrayUrls as $prefix => $urls)
        {            
            foreach($urls as $url)
            {
                if($url == $strUrl)
                {
                    $prefixUrl = $prefix;
                    break;
                }
            }
        }
        
        return $prefixUrl."/".$strUrl;
    }

    /**
     * Método que sirve para validar el ancho de banda máximo permitido de todos los servicios de una interface del elemento switch.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 28-05-2020
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.1 23-07-2020 - Se agrega filtro de las capacidades de las interfaces por el modelo de equipo.
     *
     * @param  Array $arrayDatos
     * @return Array $arrayRespuesta [ status , mensaje ]
    */
    private function validarBwMaximoServiciosPorInterface($arrayDatos)
    {
        try
        {
            //obtengo el request y la sesion
            $objRequest = $this->container->get('request');
            $objSession = $objRequest->getSession();
            //verifico si recibo el usuario y la ip
            if( !isset($arrayDatos['user_name']) || empty($arrayDatos['user_name'])
                || !isset($arrayDatos['user_ip']) || empty($arrayDatos['user_ip']) )
            {
                $arrayDatos['user_name'] = $objSession->get('user') ? $objSession->get('user') : $arrayDatos['url'];
                $arrayDatos['user_ip']   = $objRequest->getClientIp() ? $objRequest->getClientIp() : "127.0.0.1";
            }
            //seteo la variable de la accion del metodo
            $strAccionMetodo             = $objSession->get('nombreAccionBw') ? $objSession->get('nombreAccionBw') : $arrayDatos['accion'];
            if(isset($arrayDatos['nombreAccionBw']) && !empty($arrayDatos['nombreAccionBw']))
            {
                $strAccionMetodo = $arrayDatos['nombreAccionBw'];
            }
            $objSession->remove('nombreAccionBw');
            //arreglo de las url permitidas para validación del ancho de banda de la interface del elemento switch
            $arrayUrlPermitidas = array();
            //obtengo la cabecera de las url permitidas para hacer el control máximo de ancho de banda de la interface
            $objAdmiParametroCabUrl = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'URL_NETWORKING_VALIDAR_MAXIMO_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
            if( is_object($objAdmiParametroCabUrl) )
            {
                $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array("parametroId" => $objAdmiParametroCabUrl->getId(),
                                                              "estado"      => "Activo"));
                foreach($arrayParametrosDet as $objParametro)
                {
                    $arrayUrlPermitidas[] = $objParametro->getValor1();
                }
            }

            //arreglo de las acciones permitidas para validación del ancho de banda de la interface del elemento switch
            $arrayAccionesPermitidas    = array();
            //obtengo la cabecera de las acciones permitidas para hacer el control máximo de ancho de banda de la interface
            $objAdmiParametroCabAccion  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'URL_NETWORKING_ACCION_VALIDAR_MAXIMO_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
            if( is_object($objAdmiParametroCabAccion) )
            {
                $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array("parametroId" => $objAdmiParametroCabAccion->getId(),
                                                              "valor1"      => $arrayDatos['url'],
                                                              "estado"      => "Activo"));
                foreach($arrayParametrosDet as $objParametro)
                {
                    $arrayAccionesPermitidas[] = $objParametro->getValor2();
                }
            }

            //arreglo de los metodos permitidos para validación del ancho de banda de la interface del elemento switch
            $arrayMetodosPermitidas  = array();
            //obtengo la cabecera de los metodos permitidos para hacer el control máximo de ancho de banda de la interface
            $objAdmiParametroMetodos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                    array('nombreParametro' => 'URL_NETWORKING_METODOS_VALIDAR_MAXIMO_BW_INTERFACE',
                                                          'estado'          => 'Activo'));
            if( is_object($objAdmiParametroMetodos) )
            {
                $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                        array("parametroId" => $objAdmiParametroMetodos->getId(),
                                                              "estado"      => "Activo"));
                foreach($arrayParametrosDet as $objParametro)
                {
                    $arrayMetodosPermitidas[] = $objParametro->getValor1();
                }
            }

            //verifico si la url está vacía y la url de la ejecución debe validar el ancho de banda de la interface
            if( isset($arrayDatos['id_servicio']) && !empty($arrayUrlPermitidas) && !empty($arrayAccionesPermitidas) &&
                in_array($arrayDatos['url'], $arrayUrlPermitidas) && in_array($arrayDatos['accion'], $arrayAccionesPermitidas) &&
                isset($arrayDatos['nombreMetodo']) && in_array($arrayDatos['nombreMetodo'], $arrayMetodosPermitidas) )
            {
                //variables del nombre del elemeno switch y la interface del elemento
                $strNombreElemento  = '';
                $strNombreInterface = '';
                //variables de las nuevas capacidades de la interface
                $intBwUpInterface   = 0;
                $intBwDownInterface = 0;
                //variable de la capacidad del servicio
                $intCapacidadServicio = 0;

                //obtengo el nombre del elemento switch, la interface del elemento y ancho de banda nuevo del servicio
                $objParametrosUrl = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                        array("parametroId" => $objAdmiParametroCabUrl->getId(),
                                                              "valor1"      => $arrayDatos['url'],
                                                              "estado"      => "Activo"));
                if( is_object($objParametrosUrl) )
                {
                    $strKeyBw        = $objParametrosUrl->getValor2();
                    $strKeyBwUp      = $objParametrosUrl->getValor3();
                    $strKeyBwDown    = $objParametrosUrl->getValor4();
                    $strKeyElemento  = $objParametrosUrl->getValor5();
                    $strKeyInterface = $objParametrosUrl->getValor6();
                    //obtengo el nombre del elemento switch y la interface del elemento del servicio
                    if( !empty($strKeyElemento) && !empty($strKeyInterface) &&
                        isset($arrayDatos[$strKeyElemento]) && isset($arrayDatos[$strKeyInterface]) &&
                        !empty($arrayDatos[$strKeyElemento]) && !empty($arrayDatos[$strKeyInterface]) )
                    {
                        $strNombreElemento  = $arrayDatos[$strKeyElemento];
                        $strNombreInterface = $arrayDatos[$strKeyInterface];
                    }
                    else
                    {
                        throw new \Exception("No se pudo obtener el nombre del elemento switch y la interface del elemento del servicio ".
                                             "para la validación del máximo ancho de banda de la interface, por favor notificar a Sistemas.");
                    }
                    //obtengo las nuevas capacidades de la interface
                    if( !empty($strKeyBw) && !empty($strKeyBwUp) && !empty($strKeyBwDown) && isset($arrayDatos[$strKeyBw]) &&
                        isset($arrayDatos[$strKeyBw][$strKeyBwUp]) && isset($arrayDatos[$strKeyBw][$strKeyBwDown]) )
                    {
                        $intBwUpInterface   = $arrayDatos[$strKeyBw][$strKeyBwUp];
                        $intBwDownInterface = $arrayDatos[$strKeyBw][$strKeyBwDown];
                    }
                    elseif( empty($strKeyBw) && !empty($strKeyBwUp) && !empty($strKeyBwDown) &&
                            isset($arrayDatos[$strKeyBwUp]) && isset($arrayDatos[$strKeyBwDown]) )
                    {
                        $intBwUpInterface   = $arrayDatos[$strKeyBwUp];
                        $intBwDownInterface = $arrayDatos[$strKeyBwDown];
                    }
                    else
                    {
                        throw new \Exception("No se pudo obtener el ancho de banda del servicio para la validación ".
                                             "del máximo ancho de banda de la interface, por favor notificar a Sistemas.");
                    }
                }

                //obtengo el identificado si envio correo al usuario dependiendo la accion
                $objParametroAccion = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                        array("parametroId" => $objAdmiParametroCabAccion->getId(),
                                                              "valor1"      => $arrayDatos['url'],
                                                              "valor2"      => $arrayDatos['accion'],
                                                              "valor3"      => $arrayDatos['nombreMetodo'],
                                                              "estado"      => "Activo"));
                if( is_object($objParametroAccion) )
                {
                    $strValorAccionMetodo        = $objParametroAccion->getValor4();
                    //verifico si esta vacia la variable
                    if(!empty($strValorAccionMetodo))
                    {
                        $strAccionMetodo = $strValorAccionMetodo;
                    }
                }
                else
                {
                    //obtengo el identificado si envio correo al usuario dependiendo la accion
                    $objParametroAccionGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                            array("parametroId" => $objAdmiParametroCabAccion->getId(),
                                                                  "valor1"      => $arrayDatos['url'],
                                                                  "valor2"      => $arrayDatos['accion'],
                                                                  "valor3"      => null,
                                                                  "estado"      => "Activo"));
                    if( is_object($objParametroAccionGeneral) )
                    {
                        $strValorAccionMetodo        = $objParametroAccionGeneral->getValor4();
                        //verifico si esta vacia la variable
                        if(!empty($strValorAccionMetodo))
                        {
                            $strAccionMetodo = $strValorAccionMetodo;
                        }
                    }
                }

                //obtengo el objeto del servicio
                $objInfoServicio  = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($arrayDatos['id_servicio']);
                //obtengo la capacidad del servicio
                $objCaractUno     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD1",
                                                     "estado"                     => "Activo"));
                $objCaractDos     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneBy(array("descripcionCaracteristica" => "CAPACIDAD2",
                                                     "estado"                     => "Activo"));
                if(is_object($objCaractUno))
                {
                    $objProdCarac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                         ->findOneBy(array("productoId"       => $objInfoServicio->getProductoId()->getId(),
                                                           "caracteristicaId" => $objCaractUno->getId(),
                                                           "estado"           => "Activo"));
                    if(is_object($objProdCarac))
                    {
                        $objCapacidadUno = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                  ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                                    "productoCaracterisiticaId" => $objProdCarac->getId(),
                                                                    "estado"                    => "Activo"));
                    }
                }
                if(is_object($objCaractDos))
                {
                    $objProdCarac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                         ->findOneBy(array("productoId"       => $objInfoServicio->getProductoId()->getId(),
                                                           "caracteristicaId" => $objCaractDos->getId(),
                                                           "estado"           => "Activo"));
                    if(is_object($objProdCarac))
                    {
                        $objCapacidadDos = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                  ->findOneBy(array("servicioId"                => $objInfoServicio->getId(),
                                                                    "productoCaracterisiticaId" => $objProdCarac->getId(),
                                                                    "estado"                    => "Activo"));
                    }
                }
                if(isset($objCapacidadUno) && is_object($objCapacidadUno))
                {
                    $intCapacidadServicio = $objCapacidadUno->getValor();
                }
                if(isset($objCapacidadDos) && is_object($objCapacidadDos) && $objCapacidadDos->getValor() > $intCapacidadServicio)
                {
                    $intCapacidadServicio = $objCapacidadDos->getValor();
                }

                //obtengo el objeto del elemento
                $objInfoElemento  = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")
                                                    ->findOneBy(array('nombreElemento' => $strNombreElemento,
                                                                      'estado'         => 'Activo'));
                if( !is_object($objInfoElemento) )
                {
                    throw new \Exception("No se encontró el elemento $strNombreElemento, por favor notificar a Sistemas.");
                }

                //verifico si es switch virtual
                $objInfoDetalleElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                    ->findOneBy(array('elementoId'    => $objInfoElemento->getId(),
                                                                      'detalleNombre' => 'ES_SWITCH_VIRTUAL',
                                                                      'detalleValor'  => 'SI',
                                                                      'estado'        => 'Activo'));

                //si el switch no es virtual continuo con el proceso
                if( !is_object($objInfoDetalleElemento) )
                {
                    //variable del nombre del tipo de la interface
                    $strTipoInterface = '';
                    //variable del máximo ancho de banda de la interface
                    $intBwInterface   = 0;

                    //obtengo el identificador de la interface
                    $strIdentificadorInterface  = substr($strNombreInterface, 0, 2);
                    //obtengo el máximo de ancho de banda de la interface
                    $objParametroCabBwInterface = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                array('nombreParametro' => 'VALOR_MAXIMO_BW_INTERFACE',
                                                                      'estado'          => 'Activo'));
                    if( is_object($objParametroCabBwInterface) )
                    {
                        //obtengo el detalle de la solicitud
                        $objParametroBwInterface = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                        ->createQueryBuilder('p')
                                                        ->where('p.parametroId = :parametroId')
                                                        ->andWhere("UPPER(p.valor1) LIKE :valor1")
                                                        ->andWhere("p.estado = :estado")
                                                        ->setParameter('parametroId', $objParametroCabBwInterface->getId())
                                                        ->setParameter('valor1',      "%". strtoupper($strIdentificadorInterface)."%")
                                                        ->setParameter('estado',      'Activo')
                                                        ->setMaxResults(1)
                                                        ->getQuery()
                                                        ->getOneOrNullResult();
                        if( is_object($objParametroBwInterface) )
                        {
                            $strTipoInterface = $objParametroBwInterface->getValor2();
                            $intBwInterface   = intval($objParametroBwInterface->getValor3());
                            //obtengo el objeto del modelo del elemento
                            $objInfoModeloElemento = $objInfoElemento->getModeloElementoId();
                            //obtengo la cabecera del máximo ancho de banda de la interface por modelo
                            $objParametroCabBwModeloInterface = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                    array('nombreParametro' => 'MODELO_INTERFACE_VALIDAR_MAXIMO_BW_INTERFACE',
                                                                          'estado'          => 'Activo'));
                            if( is_object($objInfoModeloElemento) && is_object($objParametroCabBwModeloInterface))
                            {
                                //obtengo el máximo de ancho de banda de la interface por modelo
                                $objParametroBwInterface = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                                ->createQueryBuilder('p')
                                                                ->where('p.parametroId = :parametroId')
                                                                ->andWhere("UPPER(p.valor1) LIKE :valor1")
                                                                ->andWhere("p.valor3 = :valor3")
                                                                ->andWhere("p.estado = :estado")
                                                                ->setParameter('parametroId', $objParametroCabBwModeloInterface->getId())
                                                                ->setParameter('valor1',      "%". strtoupper($strIdentificadorInterface)."%")
                                                                ->setParameter('valor3',      $objInfoModeloElemento->getId())
                                                                ->setParameter('estado',      'Activo')
                                                                ->setMaxResults(1)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                                if( is_object($objParametroBwInterface) )
                                {
                                    $strTipoInterface = $objParametroBwInterface->getValor2();
                                    $intBwInterface   = intval($objParametroBwInterface->getValor4());
                                }
                            }
                            if( $intBwUpInterface > $intBwInterface )
                            {
                                $intExcesoCapacidad = $intBwUpInterface - $intBwInterface;
                                $arrayRespuesta = array(
                                    'status'  => 'ERROR',
                                    'mensaje' => "Se ha excedido la Capacidad de la Interface ".$strNombreInterface." del ".$strNombreElemento."<br>".
                                                 "<b>Capacidad del Servicio:</b> ".$intCapacidadServicio." Kbps<br>".
                                                 "<b>Capacidad total de interface en Telcos:</b> ".$intBwUpInterface." Kbps<br>".
                                                 "<b>Exceso de capacidad de la interface:</b> ".$intExcesoCapacidad." Kbps"
                                );
                            }
                            elseif( $intBwDownInterface > $intBwInterface )
                            {
                                $intExcesoCapacidad = $intBwDownInterface - $intBwInterface;
                                $arrayRespuesta = array(
                                    'status'  => 'ERROR',
                                    'mensaje' => "Se ha excedido la Capacidad de la Interface ".$strNombreInterface." del ".$strNombreElemento."<br>".
                                                 "<b>Capacidad del Servicio:</b> ".$intCapacidadServicio." Kbps<br>".
                                                 "<b>Capacidad total de interface en Telcos:</b> ".$intBwDownInterface." Kbps<br>".
                                                 "<b>Exceso de capacidad de la interface:</b> ".$intExcesoCapacidad." Kbps"
                                );
                            }
                            else
                            {
                                $arrayRespuesta = array(
                                    'status'  => 'OK',
                                    'mensaje' => 'La operación no supera la capacidad máxima de la interface del elemento switch.'
                                );
                            }

                            //verifico si el resultado es error para enviar los correos
                            if( $arrayRespuesta['status'] == 'ERROR' )
                            {
                                try
                                {
                                    //creación de la tarea interna
                                    $objAdmiParametroCabTarea = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                            array('nombreParametro' => 'CREAR_TAREA_INTERNA_MAXIMO_BW_INTERFACE',
                                                                  'estado'          => 'Activo'));
                                    if(is_object($objAdmiParametroCabTarea))
                                    {
                                        $objParametrosTarea   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findOneBy(
                                                                                array(  "parametroId" => $objAdmiParametroCabTarea->getId(),
                                                                                        "estado"      => "Activo"));
                                        if(is_object($objParametrosTarea))
                                        {
                                            //obtengo los destinatarios de la tarea
                                            $arrayDestinatarios = array();
                                            $objParametroDestinatarios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                    array('nombreParametro' => 'DESTINATARIOS_TAREA_INTERNA_MAXIMO_BW_INTERFACE',
                                                                          'estado'          => 'Activo'));
                                            if(is_object($objParametroDestinatarios))
                                            {
                                                $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                            array("parametroId" => $objParametroDestinatarios->getId(),
                                                                                  "estado"      => "Activo"));
                                                foreach($arrayParametrosDet as $objParametro)
                                                {
                                                    $arrayDestinatarios[] = $objParametro->getValor1();
                                                }
                                            }

                                            $objPunto               = $objInfoServicio->getPuntoId();
                                            $objPersona             = $objPunto->getPersonaEmpresaRolId()->getPersonaId();
                                            $objEmpresa             = $objPunto->getPersonaEmpresaRolId()->getEmpresaRolId()->getEmpresaCod();
                                            $objCanton              = $objPunto->getSectorId()->getParroquiaId()->getCantonId();
                                            $objCreationUser        = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                        ->findOneBy(array('login'=>$arrayDatos['user_name']));
                                            $strEmpleado            = "";
                                            if(is_object($objCreationUser))
                                            {
                                                $strEmpleado        = $objCreationUser->getNombres().' '.$objCreationUser->getApellidos();
                                            }

                                            $intBwNuevaInterface    = $intBwUpInterface >= $intBwDownInterface ?
                                                                      $intBwUpInterface : $intBwDownInterface;
                                            $strNombreTarea         = $objParametrosTarea->getValor1();
                                            $strNombreDepartamento  = $objParametrosTarea->getValor2();
                                            $strNombreCliente       = $objPersona->getNombres() ? $objPersona->getNombres().' '.
                                                                            $objPersona->getApellidos() :
                                                                            $objPersona->getRazonSocial();

                                            //reemplazo la capacidad y los nombres de la interface y del elemento
                                            $intExcesoCapacidad     = $intBwNuevaInterface - $intBwInterface;
                                            $strObservacion         = "Se ha excedido la Capacidad de la Interface ".$strNombreInterface.
                                                                      " del ".$strNombreElemento."<br><b>Acción:</b> ".$strAccionMetodo.
                                                                      "<br><b>Login del Servicio:</b> ".$objPunto->getLogin().
                                                                      "<br><b>Login Auxiliar del Servicio:</b> ".$objInfoServicio->getLoginAux().
                                                                      "<br><b>Capacidad del Servicio:</b> ".$intCapacidadServicio." Kbps".
                                                                      "<br><b>Capacidad total de interface en Telcos:</b> ".$intBwNuevaInterface.
                                                                      " Kbps<br><b>Exceso de capacidad de la interface:</b> ".$intExcesoCapacidad.
                                                                      " Kbps";
                                            //id de la tarea generada
                                            $intIdTarea             = null;

                                            //verifico si la tarea no este creada o finalizada para esta interface del elemento
                                            $booleanExisteTarea     = false;
                                            //obtengo el objeto de la tarea
                                            $objAdmiTarea           = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")
                                                                                    ->findOneByNombreTarea($strNombreTarea);
                                            if( !is_object($objAdmiTarea) )
                                            {
                                                throw new \Exception("No se encontró la tarea $strNombreTarea, por favor notificar a Sistemas.");
                                            }
                                            $arrayVerificarTarea    = array(
                                                'strEstado'          => 'Activo',
                                                'strIdEmpresa'       => $objEmpresa->getId(),
                                                'intIdTarea'         => $objAdmiTarea->getId(),
                                                'intIdPunto'         => $objPunto->getId(),
                                                'strNombreElemento'  => $strNombreElemento,
                                                'strNombreInterface' => $strNombreInterface,
                                                'strNombreParametro' => 'ESTADOS_TAREAS_FINALIZADAS_VALIDAR_MAXIMO_BW_INTERFACE'
                                            );
                                            $arrayResulVerificarTarea = $this->emComercial->getRepository("schemaBundle:InfoPunto")
                                                                            ->getVerificarEstadoTareaInternaPorInterface($arrayVerificarTarea);
                                            if($arrayResulVerificarTarea['status'] == "OK" && !empty($arrayResulVerificarTarea['result']))
                                            {
                                                $booleanExisteTarea = true;
                                                $intIdTarea         = $arrayResulVerificarTarea['result']['idComunicacion'];
                                                $intIdDetalle       = $arrayResulVerificarTarea['result']['idDetalle'];
                                                //obtengo la ultimo seguimiento de la tarea
                                                $objSeguimientoTarea = $this->emSoporte->getRepository("schemaBundle:InfoTareaSeguimiento")
                                                                                ->createQueryBuilder('p')
                                                                                ->where('p.detalleId = :detalleId')
                                                                                ->setParameter('detalleId', $intIdDetalle)
                                                                                ->orderBy('p.id', 'DESC')
                                                                                ->setMaxResults(1)
                                                                                ->getQuery()
                                                                                ->getOneOrNullResult();
                                                if(is_object($objSeguimientoTarea))
                                                {
                                                    //ingreso el nuevo seguimiento de la tarea
                                                    $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                                                    $objInfoTareaSeguimiento->setDetalleId($intIdDetalle);
                                                    $objInfoTareaSeguimiento->setObservacion($strObservacion);
                                                    $objInfoTareaSeguimiento->setEmpresaCod($objSeguimientoTarea->getEmpresaCod());
                                                    $objInfoTareaSeguimiento->setEstadoTarea($objSeguimientoTarea->getEstadoTarea());
                                                    $objInfoTareaSeguimiento->setInterno($objSeguimientoTarea->getInterno());
                                                    $objInfoTareaSeguimiento->setDepartamentoId($objSeguimientoTarea->getDepartamentoId());
                                                    $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objSeguimientoTarea->getPersonaEmpresaRolId());
                                                    $objInfoTareaSeguimiento->setUsrCreacion($arrayDatos['user_name']);
                                                    $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                                                    $this->emSoporte->persist($objInfoTareaSeguimiento);
                                                    $this->emSoporte->flush();
                                                }
                                            }

                                            if( !$booleanExisteTarea )
                                            {
                                                //seteo la variable del responsable de la asignación de la tarea
                                                $arrayPersonaResponsable = array();

                                                //Se definen los parámetros necesarios para la creación de la tarea
                                                $arrayParametrosTarea = array(
                                                    'strIdEmpresa'          => $objEmpresa->getId(),
                                                    'strPrefijoEmpresa'     => $objEmpresa->getPrefijo(),
                                                    'strNombreTarea'        => $strNombreTarea,
                                                    'strObservacion'        => $strObservacion,
                                                    'strNombreDepartamento' => $strNombreDepartamento,
                                                    'strCiudad'             => $objCanton->getNombreCanton(),
                                                    'strRegion'             => $objCanton->getRegion(),
                                                    'strNombreCliente'      => $strNombreCliente,
                                                    'strEmpleado'           => $strEmpleado,
                                                    'strUsrCreacion'        => $arrayDatos['user_name'],
                                                    'strIp'                 => $arrayDatos['user_ip'],
                                                    'strOrigen'             => 'WEB-TN',
                                                    'arrayJefeResponsable'  => $arrayPersonaResponsable,
                                                    'arrayDestinatarios'    => $arrayDestinatarios,
                                                    'strLogin'              => $objPunto->getLogin(),
                                                    'intPuntoId'            => $objPunto->getId(),
                                                    'strValidacionTags'     => 'NO'
                                                );
                                                $arrayResultadoTarea = $this->serviceSoporte->ingresarTareaInterna($arrayParametrosTarea);
                                                if($arrayResultadoTarea['status'] == "OK")
                                                {
                                                    $intIdTarea = $arrayResultadoTarea['id'];
                                                }
                                            }

                                            //verifico que no este vacio el id de la tarea
                                            if(!empty($intIdTarea))
                                            {
                                                if( $booleanExisteTarea )
                                                {
                                                    $arrayRespuesta['mensaje'] = $arrayRespuesta['mensaje'].
                                                                                 "<br>Se agrega seguimiento en tarea Nro <b>".$intIdTarea."</b>";
                                                }
                                                else
                                                {
                                                    $arrayRespuesta['mensaje'] = $arrayRespuesta['mensaje'].
                                                                                 "<br>Se genera tarea Nro <b>".$intIdTarea."</b>";
                                                }
                                            }
                                        }
                                    }
                                }
                                catch(\Exception $ex)
                                {
                                    $this->serviceUtil->insertError('Telcos+',
                                                            'NetworkingScriptsService.validarBwMaximoServiciosPorInterface',
                                                            $ex->getMessage(),
                                                            $arrayDatos['user_name'],
                                                            $arrayDatos['user_ip']);
                                }
                                try
                                {
                                    //enviar notificación del error por correo electrónico
                                    $arrayToMail    = array();
                                    $strTwigMail    = 'tecnicoBundle:InfoServicio:mailerErrorMaximoBwInterface.html.twig';
                                    $strAsuntoMail  = "Notificación: Se ha excedido la Capacidad de la Interface ".$strNombreInterface." del ".
                                                      $strNombreElemento;
                                    $strFromMail    = "notificaciones_telcos@telconet.ec";
                                    //verifico y obtengo el login auxiliar del extremo
                                    $strLoginAuxExtremo = "";
                                    if(isset($arrayDatos['loginAuxExtremo']))
                                    {
                                        $strLoginAuxExtremo = $arrayDatos['loginAuxExtremo'];
                                    }
                                    //verifico y obtengo la capacidad del extremo
                                    $strCapacidadExtremo = "";
                                    if(isset($arrayDatos['bwAuxExtremo']))
                                    {
                                        $strCapacidadExtremo = $arrayDatos['bwAuxExtremo']." Kbps";
                                    }
                                    $arrayDatosMail = array(
                                        'strLogin'           => $objInfoServicio->getPuntoId()->getLogin(),
                                        'strLoginAux'        => $objInfoServicio->getLoginAux(),
                                        'strNombreElemento'  => $strNombreElemento,
                                        'strNombreInterface' => $strNombreInterface,
                                        'strLoginAuxExtremo' => $strLoginAuxExtremo,
                                        'strCapacidadExtremo' => $strCapacidadExtremo,
                                        'strAccion'          => $strAccionMetodo,
                                        'strMensaje'         => $arrayRespuesta['mensaje'],
                                    );

                                    //obtengo la region del servicio
                                    $strRegionServivio = '';
                                    $objSector = $objInfoServicio->getPuntoId()->getSectorId();
                                    if(is_object($objSector))
                                    {
                                        //obtengo la parroquia
                                        $objParroquia = $objSector->getParroquiaId();
                                        if(is_object($objParroquia))
                                        {
                                            //obtengo el canton
                                            $objCanton = $objParroquia->getCantonId();
                                            if(is_object($objCanton))
                                            {
                                                $strRegionServivio = $objCanton->getRegion();
                                            }
                                        }
                                    }

                                    //obtengo el mail del usuario
                                    $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->findOneByLogin($arrayDatos['user_name']);
                                    if( is_object($objInfoPersona) )
                                    {
                                        $strMailUsuario        = '';
                                        $strNombreDepartamento = '';
                                        $objEmpresa = $objInfoServicio->getPuntoId()->getPersonaEmpresaRolId()->getEmpresaRolId()->getEmpresaCod();
                                        $arrayParametroUser                   = array();
                                        $arrayParametroUser['strLogin']       = $objInfoPersona->getLogin();
                                        $arrayParametroUser['intIdEmp']       = $objEmpresa->getId();
                                        $arrayParametroUser['objUtilService'] = $this->serviceUtil;
                                        $arrayDatosUsuario = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                        ->getDatosUsuarioNaf($arrayParametroUser);
                                        if($arrayDatosUsuario['status'] == 'OK')
                                        {
                                            $strMailUsuario        = $arrayDatosUsuario['result']['mailCia'];
                                            $strNombreDepartamento = $arrayDatosUsuario['result']['nombreDepto'];
                                        }
                                        //verifico que el departamento sea IPCCL2
                                        if( $strNombreDepartamento == 'IPCCL2' && !empty($strMailUsuario) )
                                        {
                                            $arrayToMail[] = $strMailUsuario;
                                        }
                                    }

                                    //obtengo los correos para el envío de la notificación
                                    $objAdmiParametroCabMail = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')->findOneBy(
                                                                        array('nombreParametro' => 'CORREOS_RESPUESTA_MAXIMO_BW_INTERFACE',
                                                                              'estado'          => 'Activo'));
                                    if( is_object($objAdmiParametroCabMail) )
                                    {
                                        $arrayParametrosDistrito = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                                array(  "parametroId"   => $objAdmiParametroCabMail->getId(),
                                                                                        "valor2"        => $strRegionServivio,
                                                                                        "estado"        => "Activo"));
                                        foreach($arrayParametrosDistrito as $objParametro)
                                        {
                                            $arrayToMail[] = $objParametro->getValor1();
                                        }
                                        $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->findBy(
                                                                                array(  "parametroId"   => $objAdmiParametroCabMail->getId(),
                                                                                        "valor2"        => "GENERAL",
                                                                                        "estado"        => "Activo"));
                                        foreach($arrayParametrosDet as $objParametro)
                                        {
                                            $arrayToMail[] = $objParametro->getValor1();
                                        }
                                    }

                                    //verifico si hay correos para el envio de los correos
                                    if( !empty($arrayToMail) )
                                    {
                                        //enviar correos de notificaciones
                                        $this->mailer->sendTwig($strAsuntoMail,
                                                             $strFromMail,
                                                             $arrayToMail,
                                                             $strTwigMail,
                                                             $arrayDatosMail);
                                    }
                                }
                                catch(\Exception $ex)
                                {
                                    $this->serviceUtil->insertError('Telcos+',
                                                            'NetworkingScriptsService.validarBwMaximoServiciosPorInterface',
                                                            $ex->getMessage(),
                                                            $arrayDatos['user_name'],
                                                            $arrayDatos['user_ip']);
                                }
                                try
                                {
                                    //registro servicio historial
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objInfoServicio);
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setUsrCreacion($arrayDatos['user_name']);
                                    $objServicioHistorial->setIpCreacion($arrayDatos['user_ip']);
                                    $objServicioHistorial->setEstado($objInfoServicio->getEstado());
                                    $objServicioHistorial->setObservacion($arrayRespuesta['mensaje']);
                                    $this->emComercial->persist($objServicioHistorial);
                                    $this->emComercial->flush();

                                    //registro historial elemento
                                    $objHistorialElemento = new InfoHistorialElemento();
                                    $objHistorialElemento->setElementoId($objInfoElemento);
                                    $objHistorialElemento->setEstadoElemento($objInfoElemento->getEstado());
                                    $objHistorialElemento->setObservacion($arrayRespuesta['mensaje']);
                                    $objHistorialElemento->setUsrCreacion($arrayDatos['user_name']);
                                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                                    $objHistorialElemento->setIpCreacion($arrayDatos['user_ip']);
                                    $this->emInfraestructura->persist($objHistorialElemento);
                                    $this->emInfraestructura->flush();
                                }
                                catch(\Exception $ex)
                                {
                                    $this->serviceUtil->insertError('Telcos+',
                                                            'NetworkingScriptsService.validarBwMaximoServiciosPorInterface',
                                                            $ex->getMessage(),
                                                            $arrayDatos['user_name'],
                                                            $arrayDatos['user_ip']);
                                }
                            }
                        }
                        else
                        {
                            $arrayRespuesta = array(
                                'status'  => 'OK',
                                'mensaje' => 'No existe validación de la capacidad máxima de la interface del elemento switch.'
                            );
                        }
                    }
                    else
                    {
                        $arrayRespuesta = array(
                            'status'  => 'OK',
                            'mensaje' => 'No existe validación de la capacidad máxima de la interface del elemento switch.'
                        );
                    }
                }
                else
                {
                    $arrayRespuesta = array(
                        'status'  => 'OK',
                        'mensaje' => 'No se realiza la validación de la capacidad de la interface ya que el elemento switch es virtual.'
                    );
                }
            }
            else
            {
                $arrayRespuesta = array(
                    'status'  => 'OK',
                    'mensaje' => 'La acción de networking que se está realizando es permitida.'
                );
            }
        }
        catch(\Exception $e)
        {
            $arrayRespuesta = array(
                'status'  => 'ERROR',
                'mensaje' => $e->getMessage()
            );
            $this->serviceUtil->insertError('Telcos+',
                                      'NetworkingScriptsService.validarBwMaximoServiciosPorInterface',
                                       $e->getMessage(),
                                       $arrayDatos['user_name'],
                                       $arrayDatos['user_ip']);
        }

        return $arrayRespuesta;
    }
    
    /**
     * callSecureCpeWebService
     *
     * Función para llamar a los web services de secure cpe
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 31-08-2021
     *
     * @param  Array $arrayPeticiones [ información requerida por el WS segun el tipo del método a ejecutar ]
     *
     * @return Array $arrayRespuesta  [ información que retorna el método llamado ]
     */
    public function callSecureCpeWebService($arrayPeticiones)
    {
        $arrayParamUsuario  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get(
                                                              'PARAMETROS_BASE_64', 
                                                              '', 
                                                              '', 
                                                              '', 
                                                              'USUARIO', 
                                                              '',
                                                              '', 
                                                              '',
                                                              '',
                                                              '10'
                                                             );
        if(!empty($arrayParamUsuario) && !empty($arrayParamUsuario[0]["valor2"]))
        {
            $strUsuario = (!empty($arrayParamUsuario[0]["valor2"])) ? $arrayParamUsuario[0]["valor2"] : 
                                                         $strUsuario;
        }
        $arrayParamPassword  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get(
                                                              'PARAMETROS_BASE_64', 
                                                              '', 
                                                              '', 
                                                              '', 
                                                              'PASSWORD', 
                                                              '',
                                                              '', 
                                                              '',
                                                              '',
                                                              '10'
                                                             );
        if(!empty($arrayParamPassword) && !empty($arrayParamPassword[0]["valor2"]))
        {
            $strPassword = (!empty($arrayParamPassword[0]["valor2"])) ? $arrayParamPassword[0]["valor2"] :$strPassword;
        }
        $strBase64 = base64_encode($strUsuario.':'.$strPassword);
        $arrayDatos = array(
            'strCredenciales'  => $strBase64,
            'strUrl'           => $this->webServiceTokenCertURL);
        $arrayResultado = $this->callWebServiceToken($arrayDatos);
        $strToken       = $arrayResultado['token'];
        $arrayRespuesta = $this->executeScriptSecureCpe($arrayPeticiones, $strToken);
        return $arrayRespuesta;
    }
    
    /**
     * executeScriptSecureCpe
     * Método que se encarga de realizar la conexión contra el WS de secure cpe
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0
     * @since 31-08-2021
     * @param  Array $objJsonDatosWs [ información requerida por el WS segun el tipo del método a ejecutar ]
     * @return Array $arrayResultado [ información que retorna el método llamado ]
     */
    private function executeScriptSecureCpe($objJsonDatosWs, $strToken)
    {
        $arrayResultado = array();

        $strDataString = $objJsonDatosWs;
        $strUrl        = $this->webServiceMiddlewareCertURL;
        
        $arrayParametros ['strToken']       = $strToken; 
        $arrayParametros ['strUrl']         = $strUrl; 
        $arrayParametros ['strDataString']  = $strDataString; 
        
        if($this->ambienteEjecuta == "S")
        {
            $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
            $arrayResponseJson = $this->restClient->postJSONSecure($arrayParametros, $arrayOptions);
            $arrayResponse     = json_decode($arrayResponseJson['result'],true);
            
            if($arrayResponseJson['status'] == static::$STATUS_OK)
            {
                $arrayResultado['data']       = $arrayResponse;
                $arrayResultado['mensaje']    = "Llamada realizada con exito!";
                $arrayResultado['status']     = "OK";
                $arrayResultado['statusCode'] = static::$STATUS_OK;
            }
            else
            {
                $arrayResultado['status']     = "ERROR";
                $arrayResultado['statusCode'] = 500;
                $arrayResultado['data']       = "";
                if($arrayResponseJson['status'] == "0")
                {
                    $arrayResultado['mensaje'] = "No Existe Conectividad con el WS Secure Cpe.";
                }
                else
                {
                    if(isset($arrayResponseJson['error']) && !empty($arrayResponseJson['error']))
                    {
                        $strMensajeError = $arrayResponseJson['error'];
                    }
                    $arrayResultado['mensaje'] = "Error de comunicación con Web service Secure Cpe : ".$strMensajeError;
                }
            }
        }
        else
        {
            $arrayResultado['data']       = "";
            $arrayResultado['status']     = "ERROR";
            $arrayResultado['mensaje']    = "La variable de ambiente se encuentra desactivada";
            $arrayResultado['statusCode'] = static::$STATUS_OK;
        }
        return $arrayResultado;
    }
    
    /**
     * callWebService
     * Función que consulta token
     * @params $arrayDatos
     * @return $arrayResultado
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 14-09-2021
     */       
    public function callWebServiceToken($arrayDatos)
    {       
        $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
        $arrayParametros['strUrl']          = $arrayDatos['strUrl'];
        $arrayParametros['strCredenciales'] = $arrayDatos['strCredenciales'];
        unset($arrayDatos['strUrl']);
        unset($arrayDatos['$strCredenciales']);
        $strDatosMiddleware  = json_encode($arrayDatos);
        $arrayResponseJson   = $this->restClient->postJSONTokenSecure($arrayParametros, $arrayOptions );
        if($arrayResponseJson['status'] == static::$STATUS_OK && $arrayResponseJson['result'])
        {        
            $arrayResponse  = json_decode($arrayResponseJson['result'],true);
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
}
