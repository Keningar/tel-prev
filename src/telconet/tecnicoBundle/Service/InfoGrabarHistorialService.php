<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion; 
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoGrabarHistorialService {
    private $emComercial;
    private $emGeneral;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $servicioGeneral;
    private $servicioConfirmar;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $rdaMiddleware;
    private $opcion                 = "POTENCIA";
    private $ejecutaComando;
    private $serviceFoxPremium;
    private $serviceUtil;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container            = $container;
        $this->emSoporte            = $container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $container->get('doctrine')->getManager('telconet');
        $this->emGeneral            = $container->get('doctrine')->getManager('telconet_general');
        $this->emComunicacion       = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $container->get('doctrine')->getManager('telconet_naf');
        $this->host                 = $container->getParameter('host');
        $this->pathTelcos           = $container->getParameter('path_telcos');
        $this->pathParameters       = $container->getParameter('path_parameters');
        $this->ejecutaComando       = $container->getParameter('ws_rda_ejecuta_scripts');
        $this->servicioGeneral      = $container->get('tecnico.InfoServicioTecnico');
        $this->servicioConfirmar    = $container->get('tecnico.InfoConfirmarServicio');
        $this->rdaMiddleware        = $container->get('tecnico.RedAccesoMiddleware');
        $this->serviceSoporte       = $container->get('soporte.SoporteService');
        $this->serviceFoxPremium    = $container->get('tecnico.FoxPremium');
        $this->serviceUtil          = $container->get('schema.Util');
    }
    
    /**
     * Funcion que graba los parametros iniciales y actualiza el servicio a EnPruebas
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 08-09-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 16-09-2016 Se agrega el envío de correo si la activación se ha realizado de manera correcta
     * 
     * @author Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 27-07-2017 Se corrige inicialización de variable producto para recuperar información solo en caso de que exista
     *                         un id_producto valido
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 19-11-2017 Se agrega validación para el flujo del producto Internet Small Business 
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.4 28-11-2018 Se agrega validación para gestionar los productos de la empresa TNP
     * @since 1.3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 10-02-2019 Se agrega nombre técnico para que los servicios TelcoHome sigan el mismo flujjo que los servicios
     *                          Small Business
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 26-11-2020 Se agrega variable con el origen de la petición.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.6 23-02-2023 - Se agrega validacion por prefijo de empresa para activar internet Ecuanet.
     * 
     */
    public function grabarHistorial($arrayPeticiones) {
        //*DECLARACION DE VARIABLES----------------------------------------------*/
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones['prefijoEmpresa'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $capacidad1     = $arrayPeticiones['capacidad1'];
        $capacidad2     = $arrayPeticiones['capacidad2'];
        $idProducto     = $arrayPeticiones['idProducto'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $idAccion       = $arrayPeticiones['idAccion'];
        $empleadoSesion = $arrayPeticiones['empleadoSesion'];
        $strEsISB       = $arrayPeticiones['esISB'];
        $status         = "";
        $strOrigen      = !empty($arrayPeticiones['origen']) ? $arrayPeticiones['origen'] : "WEB";
        
        $servicio          = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                  ->findOneBy(array( "servicioId" => $servicio->getId()));
        $interfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                 ->find($servicioTecnico->getInterfaceElementoId());
        $elemento          = $interfaceElemento->getElementoId();
        $modeloElemento    = $elemento->getModeloElementoId();
        $ultimaMilla       = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($servicioTecnico->getUltimaMillaId());
        $producto          = empty($idProducto)?"":$this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($idProducto);
        //*----------------------------------------------------------------------*/
        
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                  ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $idEmpresa      = $arrayEmpresaMigra['id'];
                 $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }
        $strNombreTecnicoProd = "";
        if(is_object($producto))
        {
            $strNombreTecnicoProd = $producto->getNombreTecnico();
        }
        
        //*LOGICA DE NEGOCIO - CAPA DE SERVICIO----------------------------------*/
        if($prefijoEmpresa=="TTCO")
        {
            $status = $this->grabarHistorialTtco($servicio, $elemento, $interfaceElemento, $modeloElemento, $ultimaMilla,
                                                            $capacidad1, $capacidad2, $usrCreacion, $ipCreacion);
        }
        else if($prefijoEmpresa=="MD" || $strNombreTecnicoProd === "INTERNET SMALL BUSINESS" 
                || $strNombreTecnicoProd === "TELCOHOME" || $prefijoEmpresa=="TNP"||$prefijoEmpresa=="EN")
        {		
            $arrayParametros = array(
                                        'servicio'          => $servicio,
                                        'interfaceElemento' => $interfaceElemento,
                                        'modeloElemento'    => $modeloElemento,
                                        'ultimaMilla'       => $ultimaMilla,
                                        'usrCreacion'       => $usrCreacion,
                                        'ipCreacion'        => $ipCreacion,
                                        'idEmpresa'         => $idEmpresa,
                                        'idAccion'          => $idAccion,
                                        'producto'          => $producto,
                                        'strPrefijoEmpresa' => $prefijoEmpresa,
                                        'esISB'             => $strEsISB,
                                        'origen'            => $strOrigen
                                    );
            $status = $this->grabarHistorialMd($arrayParametros);
        }
        else
        {
            $status = "Empresa no tiene para grabar parametros iniciales";
        }
        
        
        if($status=="OK" || $status=="OK1")
        {
            $arrayParametrosEnvioMail=array("servicio"                      => $servicio,
                                            "idEmpresa"                     => $idEmpresa,
                                            "prefijoEmpresa"                => $prefijoEmpresa,
                                            "empleadoSesion"                => $empleadoSesion,
                                            'user'                          => $usrCreacion,
                                            'ipClient'                      => $ipCreacion);
            $this->servicioConfirmar->envioMailConfirmarServicio($arrayParametrosEnvioMail);
        }
        
        //*----------------------------------------------------------------------*/
        
        return $status;
    }
    
    /**
     * Funcion que graba los parametros iniciales y actualiza el servicio a EnPruebas
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 25-03-2015
     * @version 1.2 08-09-2015
     * @version 1.3 23-02-2016   Se agrega bloque de codigo para realizar el cierre de tareas 
     *                           Generadas en una solicitud
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-01-2018 Se agrega el campo empresa con el prefijo de la empresa enviado al middleware y se pasa a estado Aceptada 
     *                         la tarea generada automáticamente por la instalación del producto INTERNET SMALL BUSINESS 
     *                         para que posteriormente se pueda finalizar la tarea.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 01-08-2018 Se agrega el envío de notificaciones por gestión de ip al activar el servicio Internet Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 13-07-2018 Se agrega validación para dejar los servicios Small Business en estado EnPruebas cuando el tipo de orden sea Traslado
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 10-02-2019 Se modifican los parámetros enviados a la función crearTareaYNotificacionIPSB por cambios en el flujo
     *                          de servicios TelcoHome
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 08-03-2019 Se agrega validación de nombre técnico para evitar que servicios telcoHome realicen el envío d enotificación de ip
     * 
     * @author David León <mdleon@telconet.ec>
     * @version 1.6 18-10-2019 Se recupera las capacidades y se envian a Rda para equipos ZTE.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 26-02-2020 Se elimina envío al middleware de parámetros(capacidades) que no son necesarios para el web service
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 02-05-2020 Se agrega el envío del parámetro objProducto a la función crearTareaYNotificacionIPSB por reestructuración de
     *                          servicios Small Business
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 1.9 19-10-2020 
     *          - Se agrega Activacion automatica en Productos adicionales paramount y noggin cuando el servicio de internet se active.
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.10 12-02-2021 
     *          - Se registra en la info_log en caso de error.
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.0 09-08-2021 
     *          - Se Parametriza la activación automatica con el servicio de internet para productos de tv permitidos.
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.1 27-08-2021 
     *          - Se agrega validacion para entrar por el flujo de activar servicios de tvs.
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.2 20-10-2021 
     *          - Se modifica los parámetros enviados en la activación del servicio ECDF.
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.3 27-08-2021 - Se anexa validacion para que al activar un servicio de internet, se activen tambien los servicios
     *                          adicionales validando que primero se activen en konibit
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 2.4 11-08-2022 - Se agrega validación para productos adicionales que no generan credenciales a servicios de Streaming
     * 
     * @since 1.0
     */
    private function grabarHistorialMd($arrayParametros)
    {
        $servicio           = $arrayParametros['servicio'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $modeloElemento     = $arrayParametros['modeloElemento'];
        $ultimaMilla        = $arrayParametros['ultimaMilla'];
        $usrCreacion        = $arrayParametros['usrCreacion'];
        $ipCreacion         = $arrayParametros['ipCreacion'];
        $idEmpresa          = $arrayParametros['idEmpresa'];
        $idAccion           = $arrayParametros['idAccion'];
        $producto           = $arrayParametros['producto'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strEsISB           = $arrayParametros['esISB'];
        $accionObj          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        $flagMiddleware     = false;
        $status             = "ERROR";
        $strMacWifi         = "";
        $strMacOnt          = "";
        $strIndiceCliente   = "";
        $strAccion          = "Entrando a función grabarHistorialMd";        
        $arrayParametrosIn  = $arrayParametros;
        
        /*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $arrayParametros    = array();
        /*----------------------------------------------------------------------*/
        
        /*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try{
            $servicioTecnico    = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $servicio->getId()));
            $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                            "detalleNombre"=> 'MIDDLEWARE',
                                                            "estado"       => 'Activo'));
            
            if($objDetalleElemento)
            {
                if($objDetalleElemento->getDetalleValor() == 'SI')
                {
                    $flagMiddleware = true;
                }
            }
            
            if($servicio->getTipoOrden() === "T" && $strEsISB === "SI")
            {
                $strEstadoServicio      = "EnPruebas";
                $boolConfirmarServicio  = false;
            }
            else
            {
                $strEstadoServicio      = "Activo";
                $boolConfirmarServicio  = true;
            }
            
            if($flagMiddleware)
            {
                $strAccion              = "Entrando a validación flagMiddleware";   
                $elemento               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());
                $objIpElemento          = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                            ->findOneBy(array('elementoId' => $elemento->getId(), 'estado' => 'Activo'));
                
                //OBTENER NOMBRE CLIENTE
                $objPersona             = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strNombreCliente       = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                                    $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER IDENTIFICACION
                $strIdentificacion      = $objPersona->getIdentificacionCliente();

                //OBTENER LOGIN
                $strLogin               = $servicio->getPuntoId()->getLogin();

                //OBTENER MAC ONT
                $spcMacOnt      = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
                if($spcMacOnt)
                {
                    $strMacOnt  = $spcMacOnt->getValor();
                }

                //OBTENER SERIE ONT
                $elementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->find($servicioTecnico->getElementoClienteId());
                $strSerieOnt        = $elementoCliente->getSerieFisica();

                if($modeloElemento->getNombreModeloElemento()=="EP-3116")
                {
                    //OBTENER MAC WIFI
                    $spcMacWifi   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                    if($spcMacWifi)
                    {
                        $strMacWifi = $spcMacWifi->getValor();
                    }
                }//if($modeloElemento->getNombreModeloElemento()=="EP-3116")
                
                //OBTENER INDICE CLIENTE
                $spcIndiceCliente       = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                if($spcIndiceCliente)
                {
                    $strIndiceCliente   = $spcIndiceCliente->getValor();
                }

                $arrayDatos = array(
                                        'serial_ont'        => $strSerieOnt,
                                        'mac_ont'           => $strMacOnt,
                                        'nombre_olt'        => $elemento->getNombreElemento(),
                                        'ip_olt'            => $objIpElemento->getIp(),
                                        'puerto_olt'        => $interfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'        => $modeloElemento->getNombreModeloElemento(),
                                        'ont_id'            => $strIndiceCliente,
                                        'estado_servicio'   => $servicio->getEstado(),
                                        'mac_wifi'          => $strMacWifi
                                    );

                $arrayDatosMiddleware = array(
                                                'empresa'               => $strPrefijoEmpresa,
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $strLogin,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion
                                            );
                
                $strAccion  = "Iniciando llamada a función de middleware";   
                $arrayFinal = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));

                $status     = $arrayFinal['status'];
                $mensaje    = $arrayFinal['mensaje'];
                if($status == "OK")
                {
                    //ingresar caracteristica POTENCIA en el servicio
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "POTENCIA", 
                                                                                   $mensaje, $usrCreacion);
                }
                else
                {
                    throw new \Exception("No se pudo obtener la potencia! <br>".$mensaje);
                }
            }
            else
            {
                $strAccion  = "Iniciando llamada a función de grabarHistorialOlt";   
                $status = $this->grabarHistorialOlt($usrCreacion, $servicio, $interfaceElemento, $modeloElemento, $idEmpresa);
            }
            

            if($status == "OK" || $status == "OK1")
            {
                    $strAccion  = "Actualizando estado de servicio a Activo";   
                    
                    $servicio->setEstado($strEstadoServicio);
                    $this->emComercial->persist($servicio);

                    //Activacion automatica en Productos adicionales paramount y noggin.
                    $arrayParametrosDetProdTV = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('FLUJO_INGRESO_POR_ESTADO_INTERNET',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'OBTENER_NOMBRE_TECNICO',//proceso cab
                                                              'FLUJO_ESTADO_PENDIENTE', //descripcion det
                                                              '','','','','',
                                                              $idEmpresa); //empresa
                    $arrayEstados = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('FLUJO_INGRESO_POR_ESTADO_INTERNET',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'OBTENER_NOMBRE_TECNICO',//proceso cab
                                                              'ESTADOS', //descripcion det
                                                              '','','','','',
                                                              $idEmpresa); //empresa
                    $arrayProductosTv       =   array();
                    $arrayEstadosPermitidos =   array();
                    
                    if(is_array($arrayParametrosDetProdTV) && !empty($arrayParametrosDetProdTV))
                    {
                        foreach($arrayParametrosDetProdTV as $arrayParamDetProd)
                        {
                            $arrayProductosTv[]   =   $arrayParamDetProd['valor1'];
                        }
                    }
                    if(is_array($arrayEstados) && !empty($arrayEstados))
                    {
                        foreach($arrayEstados as $arrayParamDetEstado)
                        {
                            $arrayEstadosPermitidos[]   =   $arrayParamDetEstado['valor1'];
                        }
                    }
                    if(is_array($arrayProductosTv)  && !empty($arrayProductosTv))
                    {
                        $arrayPeticiones = array('intIdPunto'           => $servicio->getPuntoId()->getId(),
                                                'arrayNombreTecnico'   => $arrayProductosTv);
                        $arrayServiciosxPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                    ->obtieneServiciosAdicionalesxPunto($arrayPeticiones);
                    }
                    if(isset($arrayServiciosxPunto) && !empty($arrayServiciosxPunto))
                    {
                        foreach($arrayServiciosxPunto as $objServicioxPunto)
                        {
                            if(is_object($objServicioxPunto->getProductoId()) 
                                && in_array($objServicioxPunto->getProductoId()->getNombreTecnico(),$arrayProductosTv) && 
                                in_array($objServicioxPunto->getEstado(), $arrayEstadosPermitidos))
                            {
                                $objServicioxPunto->setEstado('Activo');
                                $this->emComercial->persist($objServicioxPunto);
                                //Graba historial del servicio adicional
                                $objHistorialAdicional = new InfoServicioHistorial();
                                $objHistorialAdicional->setServicioId($objServicioxPunto);
                                $objHistorialAdicional->setObservacion("Otros: Se confirmo el servicio");
                                $objHistorialAdicional->setEstado("Activo");
                                $objHistorialAdicional->setAccion($accionObj->getNombreAccion());
                                $objHistorialAdicional->setUsrCreacion($usrCreacion);
                                $objHistorialAdicional->setFeCreacion(new \DateTime('now'));
                                $objHistorialAdicional->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($objHistorialAdicional);
                                $this->emComercial->flush();

                                // CONSULTAR PRODUCTOS QUE NO REQUIEREN CREAR CREDENCIALES
                                $objProdGenCred = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne('PRODUCTOS_STREAMING_SIN_CREDENCIALES',//nombre parametro cab
                                                              'COMERCIAL', //modulo cab
                                                              'OBTENER_NOMBRE_TECNICO',//proceso cab
                                                              'PRODUCTOS_STREAMING_SIN_CREDENCIALES', //descripcion det
                                                              $objServicioxPunto->getProductoId()->getNombreTecnico(),'','','','',
                                                              $idEmpresa); //empresa
                                if(is_array($objProdGenCred) && !empty($objProdGenCred))
                                {
                                    $arrayParam                   = array('intIdServicio' => $objServicioxPunto->getId(), 'strEstado' => "Activo");
                                    $arrayCaracteristica = $this->serviceFoxPremium->obtieneArrayCaracteristicas($arrayParam);
                                    $objCorreoElectronico         = $arrayCaracteristica["CORREO ELECTRONICO"];
                                    $arrayCaracteristicaCorreo[]  = array('caracteristica' => 'CORREO ELECTRONICO', 
                                                                          'valor' => $objCorreoElectronico->getValor());
                                    // OBTENER URL DE ACTIVACION
                                    $strNombreTecnico = $objServicioxPunto->getProductoId()->getNombreTecnico();
                                    $arrayParametrosUrlToken = array('strUsrCreacion'          => $usrCreacion,
                                                                          'arrayCaracteristicas'   => $arrayCaracteristicaCorreo,
                                                                          'strNombreTecnico'       => $strNombreTecnico,
                                                                          'strCrearPassword'       => "SI",
                                                                          'strEmpresaCod'          => $idEmpresa);
                                    $arrayUrlToken = $this->serviceFoxPremium->obtenerUrlActivarServicio($arrayParametrosUrlToken);
                                    if ($arrayUrlToken["status"] !== "OK") 
                                    {
                                        $objServicioxPunto->setEstado('Pendiente');
                                        $this->emComercial->persist($objServicioxPunto);
                                        //Graba historial del servicio adicional
                                        $objHistorialAdicional->setEstado("Pendiente");
                                        $this->emComercial->persist($objHistorialAdicional);
                                        $this->emComercial->flush();
                                    }
                                    else 
                                    {
                                        $this->serviceFoxPremium->activarServicio(array(
                                              "intIdProducto"=> $objServicioxPunto->getProductoId()->getId(),
                                              "strUsrCreacion" => $usrCreacion,
                                              "strClientIp"    => $ipCreacion,
                                              "strEmpresaCod"  => $idEmpresa,
                                              "intIdServicio"  => $objServicioxPunto->getId(),
                                              'arrayCaracteristicas'  => $arrayCaracteristicaCorreo,
                                              'strCliente'     => $strNombreCliente,
                                              'strUrlProducto' => $arrayUrlToken["url"]));
                                    }
                                }
                                else 
                                {
                                    $this->serviceFoxPremium->activarServicio(array(
                                                                                "intIdProducto"=> $objServicioxPunto->getProductoId()->getId(),
                                                                                "strUsrCreacion" => $usrCreacion,
                                                                                "strClientIp"    => $ipCreacion,
                                                                                "strEmpresaCod"  => $idEmpresa,
                                                                                "intIdServicio"  => $objServicioxPunto->getId()));
                                }
                                
                            }
                        }
                    }
                    //historial del servicio
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    if($ultimaMilla->getNombreTipoMedio()!="Radio")
                    {
                        $servicioHistorial->setObservacion("Se grabaron los parametros iniciales");
                    }
                    else
                    {
                        $servicioHistorial->setObservacion("Se Cambio de estado al servicio, UM:Radio no guarda parametros iniciales");
                    }
                    $servicioHistorial->setEstado("EnPruebas");
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($servicioHistorial);
                    
                    if($boolConfirmarServicio)
                    {
                        //historial del servicio Activo
                        $servicioHistorialActivo = new InfoServicioHistorial();
                        $servicioHistorialActivo->setServicioId($servicio);
                        $servicioHistorialActivo->setObservacion("Se confirmo el servicio");
                        $servicioHistorialActivo->setEstado("Activo");
                        $servicioHistorialActivo->setAccion($accionObj->getNombreAccion());
                        $servicioHistorialActivo->setUsrCreacion($usrCreacion);
                        $servicioHistorialActivo->setFeCreacion(new \DateTime('now'));
                        $servicioHistorialActivo->setIpCreacion($ipCreacion);
                        $this->emComercial->persist($servicioHistorialActivo);                
                    }
                    $this->emComercial->flush();  

                    // Realiza la activacion de servicios adicionales automaticos solo para servicios de internet
                    $objPlan = $servicio->getPlanId();
                    $arrayProductoParam = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('VALIDA_PROD_ADICIONAL', 
                                                          'COMERCIAL', '',
                                                          'Verifica Producto Internet',
                                                          '','','','','',$idEmpresa);
                    if (is_array($arrayProductoParam) && !empty($arrayProductoParam))
                    {
                        $objProdParametro = $arrayProductoParam[0];
                    }
                    if (!empty($objPlan) && $producto->getDescripcionProducto() == $objProdParametro['valor3'])
                    {
                        // Activamos los servicios adicionales
                        $arrayDatosParametros = array(
                            "intIdPunto"      => $servicio->getPuntoId()->getId(),
                            "intCodEmpresa"   => $idEmpresa,
                            "strIpCreacion"   => $ipCreacion,
                            "strUserCreacion" => $usrCreacion,
                            "strAccion"       => $accionObj->getNombreAccion()
                        );
                        $this->servicioConfirmar->activarProductosAdicionales($arrayDatosParametros);
                        // Activamos los servicios incluidos
                        $arrayDatosParametros = array(
                            "objServicio"     => $servicio,
                            "intCodEmpresa"   => $idEmpresa,
                            "strIpCreacion"   => $ipCreacion,
                            "strUserCreacion" => $usrCreacion
                        );
                        $this->servicioConfirmar->activarProdKonitIncluidos($arrayDatosParametros);
                    }
            }
            else
            {
                throw new \Exception("No se pudo obtener la potencia! <br>".$status);
            }
        }
        catch (\Exception $e) 
        {
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            $status = "ERROR EN LA LOGICA DE NEGOCIO, ".$e->getMessage();
            
            $this->serviceUtil->insertLog(array(
                'enterpriseCode'   => $idEmpresa,
                'logType'          => 1,
                'logOrigin'        => 'TELCOS',
                'application'      => 'TELCOS',
                'appClass'         => basename(__CLASS__),
                'appMethod'        => basename(__FUNCTION__),
                'appAction'        => $strAccion,
                'descriptionError' => $status,
                'status'           => 'Fallido',
                'inParameters'     => json_encode($arrayParametrosIn),
                'creationUser'     => $usrCreacion));
            
            return $status;
        }
        /*----------------------------------------------------------------------*/
        
        /*DECLARACION DE COMMITS*/
        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        $this->emComercial->getConnection()->close();
        /*----------------------------------------------------------------------*/
        
        if(($status == "OK" || $status == "OK1") && $boolConfirmarServicio)
        {
            //finalizar tareas generadas en solicitudes
            $strAccion  = "Finalizando tareas generadas en solicitudes";   
            $objTipoSolicitudPlanficacion = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                              ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                          "estado"               => "Activo"));
            $objSolicitudPlanficacion     = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                              ->findOneBy(array("servicioId"      => $servicio->getId(),
                                                                                "tipoSolicitudId" => $objTipoSolicitudPlanficacion->getId(),
                                                                                "estado"          => "Finalizada"),
                                                                          array('id'              => 'DESC'));

            if ($objSolicitudPlanficacion)
            {
                if($strEsISB === "SI")
                {
                    $intIdSolicitudPlanificacion    = $objSolicitudPlanficacion->getId();
                    $objDetalleTareaPlanif          = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                                      ->findOneBy(array("detalleSolicitudId" => $intIdSolicitudPlanificacion));
                    if(is_object($objDetalleTareaPlanif))
                    {
                        $arrayParametros['strTipo']              = "iniciar";
                        $arrayParametros['objDetalle']           = $objDetalleTareaPlanif;
                        $arrayParametros['strObservacion']       = "Tarea iniciada automáticamente por activación";
                        $arrayParametros['strCodEmpresa']        = $idEmpresa;
                        $arrayParametros['strUser']              = $usrCreacion;
                        $arrayParametros['strIpUser']            = $ipCreacion;  
                        $arrayParametros["intPersonaEmpresaRol"] = 0;
                        $this->serviceSoporte->administrarTarea($arrayParametros);
                    }
                    if($servicio->getTipoOrden() === "N" && is_object($servicio->getProductoId()) 
                        && $servicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS")
                    {
                        $this->serviceSoporte->crearTareaYNotificacionIPSB(array(
                                                                                    "objProducto"           => $servicio->getProductoId(),
                                                                                    "objPunto"              => $servicio->getPuntoId(),
                                                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresa,
                                                                                    "strCodEmpresa"         => $idEmpresa,
                                                                                    "strIpClient"           => $ipCreacion,
                                                                                    "strEstadoSolServicio"  => "PreAsignacionInfoTecnica",
                                                                                    "strOpcion"             => "ACTIVACION_"
                                                                                    .$servicio->getProductoId()->getNombreTecnico(),
                                                                                    "strUsrSession"         => $usrCreacion,
                                                                                    "strNombreTecnicoProd"  => 
                                                                                    $servicio->getProductoId()->getNombreTecnico()
                                                                          ));
                    }
                }
                
                $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudPlanficacion->getId();
                $arrayParametros['strProceso']            = 'Activar';
                $strMensajeResponse                       = $this->emInfraestructura
                                                                 ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                 ->cerrarTareasPorSolicitud($arrayParametros);
            }  
        }
        
        /*RESPUESTA-------------------------------------------------------------*/
        return $status;
        /*----------------------------------------------------------------------*/
        
    }
    
    private function grabarHistorialTtco($servicio, $elemento, $interfaceElemento, $modeloElemento, $ultimaMilla,
                                               $capacidad1, $capacidad2, $usrCreacion, $ipCreacion){
        /*DECLARACION DE VARIABLES----------------------------------------------*/
        $punto = $servicio->getPuntoId();
        /*----------------------------------------------------------------------*/
        
        /*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        /*----------------------------------------------------------------------*/
        
        /*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try{
            if($modeloElemento->getNombreModeloElemento()!="TERCERIZADO" && $modeloElemento->getReqAprovisionamiento()=="SI"){
                if($ultimaMilla->getNombreTipoMedio()!="Radio"){
                    if($modeloElemento->getNombreModeloElemento()=="6524"){
                        /*OBTENER SCRIPT - MOSTRAR ATENUACION DSLAM 6524*/
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarAtenuacionDslam6524",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorial6524($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "ATENUACION",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarSenalRuidoDslam6524",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorial6524($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "SENAL RUIDO",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarCrcDslam6524",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorial6524($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "CRC",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    }
                    else if($modeloElemento->getNombreModeloElemento()=="7224"){
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarParametrosLineaDslam7224",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorial7224($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "PARAMETROS LINEA",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarMonitorearPuertoDslam7224",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorial7224($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "MONITOREAR PUERTO",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    }
                    else if($modeloElemento->getNombreModeloElemento()=="R1AD24A"){
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarMonitoreoPuertoDataIDslamR1",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorialR1AD24A($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "MONITOREAR PUERTO I",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarMonitoreoPuertoDataIIDslamR1",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorialR1AD24A($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "MONITOREAR PUERTO II",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    }
                    else if($modeloElemento->getNombreModeloElemento()=="R1AD48A"){
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarMonitoreoPuertoDataIDslamR1",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorialR1AD48A($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "MONITOREAR PUERTO I",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarMonitoreoPuertoDataIIDslamR1",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorialR1AD48A($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "MONITOREAR PUERTO II",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    }
                    else if($modeloElemento->getNombreModeloElemento()=="A2024"){
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarConfiguracionInterfaceDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorialA2024($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "CONFIGURACION INTERFACE",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarVelocidadRealDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //VELOCIDAD REAL
                        $this->grabarHistorialA2024($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "VELOCIDAD REAL",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarNivelesSenalExtremoLejanoDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //NIVELES SENAL EXTRANO LEJANO
                        $this->grabarHistorialA2024($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "NIVELES SENAL EXTREMO LEJANO",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarNivelesSenalExtremoCercanoDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //NIVELES SENAL EXTREMO CERCANO
                        $this->grabarHistorialA2024($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "NIVELES SENAL EXTREMO CERCANO",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarDesempenoPuertoIntervaloDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //DESEMPENO PUERTO INTERVALO
                        $this->grabarHistorialA2024($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "DESEMPENO PUERTO INTERVALO",$usrCreacion,$ipCreacion);
                    }
                    else if($modeloElemento->getNombreModeloElemento()=="A2048"){
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarConfiguracionInterfaceDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //configuracion interface
                        $this->grabarHistorialA2048($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "CONFIGURACION INTERFACE",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarVelocidadRealDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //VELOCIDAD REAL
                        $this->grabarHistorialA2048($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "VELOCIDAD REAL",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarNivelesSenalExtremoLejanoDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //NIVELES SENAL EXTRANO LEJANO
                        $this->grabarHistorialA2048($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "NIVELES SENAL EXTREMO LEJANO",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarNivelesSenalExtremoCercanoDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //NIVELES SENAL EXTREMO CERCANO
                        $this->grabarHistorialA2048($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "NIVELES SENAL EXTREMO CERCANO",$usrCreacion,$ipCreacion);

                        //----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                        $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("mostrarDesempenoPuertoIntervaloDslamA2024",$modeloElemento);
                        $idDocumento= $scriptArray[0]->idDocumento;
                        $usuario= $scriptArray[0]->usuario;
                        $protocolo= $scriptArray[0]->protocolo;

                        //DESEMPENO PUERTO INTERVALO
                        $this->grabarHistorialA2048($idDocumento, $usuario, $protocolo, $elemento->getId(), $interfaceElemento, "DESEMPENO PUERTO INTERVALO",$usrCreacion,$ipCreacion);
                    }
                }
                else{
                $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findOneBy(array( "servicioId" =>$servicio->getId(), "tipoIp"=>"RADIO", "estado"=>"Activo"));

                if($infoIp){
                    $ipCpeRadio = $infoIp->getIp();
                }
                else{
                    $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findOneBy(array( "servicioId" =>$servicio->getId(), "tipoIp"=>"WAN", "estado"=>"Activo"));
                    $ipCpeRadio = $infoIp->getIp();
                }

                //radius
                $elementoIdRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->findBy(array( "nombreElemento" => "ttcoradius"));
                $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("activarClienteRADIUS",$elementoIdRadius[0]->getModeloElementoId());
                $idDocumento= $scriptArray[0]->idDocumento;
                $usuario= $scriptArray[0]->usuario;
                $protocolo= $scriptArray[0]->protocolo;

                //comando - servidor radius
                $datos1 = $punto->getLogin().",".$punto->getLogin()."123,".$ipCpeRadio.",".$capacidad1.",".$capacidad2;
                $this->activarClienteRADIUS($idDocumento, $usuario, "servidor", $elementoIdRadius[0], $datos1);
            }
            }
            
            $servicio->setEstado("EnPruebas");
            $this->emComercial->persist($servicio);

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            if($ultimaMilla->getNombreTipoMedio()!="Radio"){
                $servicioHistorial->setObservacion("Se grabaron los parametros iniciales");
            }
            else{
                $servicioHistorial->setObservacion("Se Cambio de estado al servicio, UM:Radio no guarda parametros iniciales");
            }
            $servicioHistorial->setEstado("EnPruebas");
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            
            $status="OK";
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            $status="ERROR EN LA LOGICA DE NEGOCIO, ".$e->getMessage();
            return $status;
        }
        /*----------------------------------------------------------------------*/
        
        /*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()){
            $this->emInfraestructura->getConnection()->commit();
        }
        $this->emInfraestructura->getConnection()->close();
        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        $this->emComercial->getConnection()->close();
        /*----------------------------------------------------------------------*/
        
        /*RESPUESTA-------------------------------------------------------------*/
        return $status;
        /*----------------------------------------------------------------------*/
    }
    
    //--TRANSTELCO
    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un dslam de modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function grabarHistorialA2024($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento, $nombreParametro, 
                                         $usrCreacion, $ipCreacion)
    {
        if($nombreParametro == "DESEMPENO PUERTO INTERVALO")
        {
            $interf = explode(" ", $interfaceElemento->getNombreInterfaceElemento());
            $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interf[1]);
        }
        else
        {
            $salida = $this->servicioGeneral
                ->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento->getNombreInterfaceElemento());
        }
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        $resultado = $resultadJson->mensaje;

        //grabar detalle
        $detalleInterface = new InfoDetalleInterface();
        $detalleInterface->setInterfaceElementoId($interfaceElemento);
        $detalleInterface->setDetalleNombre($nombreParametro);
        $detalleInterface->setDetalleValor($resultado);
        $detalleInterface->setFeCreacion(new \DateTime('now'));
        $detalleInterface->setIpCreacion($ipCreacion);
        $detalleInterface->setUsrCreacion($usrCreacion);
        $this->emInfraestructura->persist($detalleInterface);
        $this->emInfraestructura->flush();
    }

    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un dslam de modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function grabarHistorialA2048($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento, $nombreParametro, 
                                         $usrCreacion, $ipCreacion)
    {
        if($nombreParametro == "DESEMPENO PUERTO INTERVALO")
        {
            $interf = explode(" ", $interfaceElemento->getNombreInterfaceElemento());
            $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interf[1]);
        }
        else
        {
            $salida = $this->servicioGeneral
                           ->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento->getNombreInterfaceElemento());
        }
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        $resultado = $resultadJson->mensaje;

        //grabar detalle
        $detalleInterface = new InfoDetalleInterface();
        $detalleInterface->setInterfaceElementoId($interfaceElemento);
        $detalleInterface->setDetalleNombre($nombreParametro);
        $detalleInterface->setDetalleValor($resultado);
        $detalleInterface->setFeCreacion(new \DateTime('now'));
        $detalleInterface->setIpCreacion($ipCreacion);
        $detalleInterface->setUsrCreacion($usrCreacion);
        $this->emInfraestructura->persist($detalleInterface);
        $this->emInfraestructura->flush();
    }

    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un dslam de modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function grabarHistorialR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento, $nombreParametro, 
                                           $usrCreacion, $ipCreacion)
    {
        $salida = $this->servicioGeneral
                           ->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento->getNombreInterfaceElemento());
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        $resultado = $resultadJson->mensaje;

        //grabar detalle
        $detalleInterface = new InfoDetalleInterface();
        $detalleInterface->setInterfaceElementoId($interfaceElemento);
        $detalleInterface->setDetalleNombre($nombreParametro);
        $detalleInterface->setDetalleValor($resultado);
        $detalleInterface->setFeCreacion(new \DateTime('now'));
        $detalleInterface->setIpCreacion($ipCreacion);
        $detalleInterface->setUsrCreacion($usrCreacion);
        $this->emInfraestructura->persist($detalleInterface);
        $this->emInfraestructura->flush();
    }

    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un dslam de modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function grabarHistorialR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento, $nombreParametro, 
                                           $usrCreacion, $ipCreacion)
    {
        $salida = $this->servicioGeneral
                           ->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento->getNombreInterfaceElemento());
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        $resultado = $resultadJson->mensaje;

        //grabar detalle
        $detalleInterface = new InfoDetalleInterface();
        $detalleInterface->setInterfaceElementoId($interfaceElemento);
        $detalleInterface->setDetalleNombre($nombreParametro);
        $detalleInterface->setDetalleValor($resultado);
        $detalleInterface->setFeCreacion(new \DateTime('now'));
        $detalleInterface->setIpCreacion($ipCreacion);
        $detalleInterface->setUsrCreacion($usrCreacion);
        $this->emInfraestructura->persist($detalleInterface);
        $this->emInfraestructura->flush();
    }

    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un dslam de modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function grabarHistorial6524($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento, $nombreParametro, 
                                        $usrCreacion, $ipCreacion)
    {
        $salida = $this->servicioGeneral
                           ->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento->getNombreInterfaceElemento());
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        $resultado = $resultadJson->mensaje;

        //grabar detalle
        $detalleInterface = new InfoDetalleInterface();
        $detalleInterface->setInterfaceElementoId($interfaceElemento);
        $detalleInterface->setDetalleNombre($nombreParametro);
        $detalleInterface->setDetalleValor($resultado);
        $detalleInterface->setFeCreacion(new \DateTime('now'));
        $detalleInterface->setIpCreacion($ipCreacion);
        $detalleInterface->setUsrCreacion($usrCreacion);
        $this->emInfraestructura->persist($detalleInterface);
        $this->emInfraestructura->flush();
    }

    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un dslam de modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function grabarHistorial7224($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento, $nombreParametro, 
                                        $usrCreacion, $ipCreacion)
    {
        $salida = $this->servicioGeneral
                           ->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId, $interfaceElemento->getNombreInterfaceElemento());
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        $resultado = $resultadJson->mensaje;

        //grabar detalle
        $detalleInterface = new InfoDetalleInterface();
        $detalleInterface->setInterfaceElementoId($interfaceElemento);
        $detalleInterface->setDetalleNombre($nombreParametro);
        $detalleInterface->setDetalleValor($resultado);
        $detalleInterface->setFeCreacion(new \DateTime('now'));
        $detalleInterface->setIpCreacion($ipCreacion);
        $detalleInterface->setUsrCreacion($usrCreacion);
        $this->emInfraestructura->persist($detalleInterface);
        $this->emInfraestructura->flush();
    }

    /**
     * Funcion que sirve para grabar los parametros iniciales
     * de un cliente que se encuentra en un servidor RADIUS
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function activarClienteRADIUS($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    //----
    
    //--MEGADATOS
    /**
     * Funcion que sirve para grabar los parametros iniciales (potencia)
     * del olt, para un servicio de internet
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 25-03-2015
     * @since 1.0
     */
    private function grabarHistorialOlt($usrCreacion,$servicio,$interfaceElemento,$modeloElemento,$idEmpresa)
    {
        $potencia = "";
        $producto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                    ->findOneBy(array("esPreferencia"   =>"SI", 
                                                      "nombreTecnico"   => "INTERNET", 
                                                      "empresaCod"      =>$idEmpresa,
                                                      "estado"          =>"Activo")); 
        try{
            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
            //if($modeloElemento->getNombreModeloElemento()=="MA5608T")
            if($strMarcaOlt == "HUAWEI")
            {
                /*OBTENER SCRIPT*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScript("obtenerPotencia",$modeloElemento);
                $idDocumento= $scriptArray[0]->idDocumento;
                $usuario= $scriptArray[0]->usuario;
                $protocolo= $scriptArray[0]->protocolo;
                
                //dividir interface para obtener tarjeta y puerto pon
                list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
                
                //obtener caracteristica - indice cliente (ont id)
                $spcOntId = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                
                if($spcOntId)
                {
                    //datos
                    $datos = $tarjeta.",".$puertoPon.",".$spcOntId->getValor();

                    //ejecutar script
                    $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
                               $this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".
                               $interfaceElemento->getElementoId()->getId()."' '".$datos."' '".$this->pathParameters."'";

                    $salida= shell_exec($comando);
                    $pos = strpos($salida, "{"); 
                    $jsonObj= substr($salida, $pos);
                    $resultadJson = json_decode($jsonObj);
                    
                    $potencia = $resultadJson->mensaje;
                }
                else
                {
                    $mensaje = "Mensaje: No existe la caracteristica INDICE CLIENTE en el Servicio!";
                    throw new \Exception("No se pudo obtener la potencia! <br>".$mensaje);
                }
            }
            else if($modeloElemento->getNombreModeloElemento()=="EP-3116")
            {
                /*OBTENER SCRIPT*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("verificacionPotenciaNivelSeñal",$modeloElemento);
                $idDocumento= $scriptArray[0]->idDocumento;
                $usuario= $scriptArray[0]->usuario;
                $protocolo= $scriptArray[0]->protocolo;

                $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
                           $this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".
                           $interfaceElemento->getElementoId()->getId()."' '".
                           $interfaceElemento->getNombreInterfaceElemento()."' '".$this->pathParameters."'";

                $salida= shell_exec($comando);
                $pos = strpos($salida, "{"); 
                $jsonObj= substr($salida, $pos);
                $resultadJson = json_decode($jsonObj);

                $resultado = $resultadJson->mensaje;
                $potencia = substr($resultado, 0, 899);
            }
                       
            //ingresar caracteristica POTENCIA en el servicio
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "POTENCIA", 
                                                                           $potencia, $usrCreacion);
            
            if($potencia=="")
            {
                $status = "OK1";
            }
            else
            {
                $status = "OK";
            }
        }
        catch (\Exception $e) {
             return "ERROR: ".$e->getMessage();
        }
        
        return $status;
    }

}
