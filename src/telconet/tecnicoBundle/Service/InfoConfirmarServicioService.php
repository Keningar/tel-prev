<?php

namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;
use Exception;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion; 
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoTareaTiempoAsignacion;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
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
use telconet\schemaBundle\Entity\InfoElementoTrazabilidad;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Service\UtilService;

class InfoConfirmarServicioService {
    private $emComercial;
    private $emcom;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emGeneral;
    private $emSeguridad;
    private $emNaf;
    private $servicioGeneral;
    private $cancelarServicio;
    private $serviceElementoWifi;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $utilService;
    private $envioPlantillaService;
    private $serviceSoporte;
    private $serviceActivarPuerto;
    private $strEjecutaComando;
    private $serviceRdaMiddleware;
    private $serviceCliente;
    private $serviceProceso;
    private $serviceCrypt;
    private $serviceFoxPremium;
    private $strSmsNombreTecnicoFoxPremium;
    private $strSmsNombreTecnicoParamount;
    private $strSmsNombreTecnicoNoggin;
    private $serviceLicKaspersky;
    private $serviceOrquestador;
    private $serviceTokenCas;
    private $serviceKonibit;
    private $serviceInfoElemento;
    private $serviceInvestigacionDesarrollo;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                = $container;
        $this->emSoporte                = $container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura        = $container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad              = $container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial              = $container->get('doctrine')->getManager('telconet');
        $this->emComunicacion           = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral                = $container->get('doctrine')->getManager('telconet_general');
        $this->emNaf                    = $container->get('doctrine')->getManager('telconet_naf');
        $this->host                     = $container->getParameter('host');
        $this->pathTelcos               = $container->getParameter('path_telcos');
        $this->pathParameters           = $container->getParameter('path_parameters');
        $this->servicioGeneral          = $container->get('tecnico.InfoServicioTecnico');
        $this->serviceInfoElemento      = $container->get('tecnico.InfoElemento');
        $this->cancelarServicio         = $container->get('tecnico.InfoCancelarServicio');
        $this->envioPlantillaService    = $container->get('soporte.EnvioPlantilla');
        $this->serviceElementoWifi      = $container->get('tecnico.InfoElementoWifi');
        $this->utilService              = $container->get('schema.Util');
        $this->serviceSoporte           = $container->get('soporte.SoporteService');
        $this->serviceActivarPuerto     = $container->get('tecnico.InfoActivarPuerto');
        $this->strEjecutaComando        = $container->getParameter('ws_rda_ejecuta_scripts');
        $this->serviceRdaMiddleware     = $container->get('tecnico.RedAccesoMiddleware');
        $this->serviceCliente           = $container->get('comercial.Cliente');
        $this->serviceProceso           = $container->get('soporte.ProcesoService');
        $this->serviceCrypt         = $container->get('seguridad.Crypt');
        $this->serviceFoxPremium    = $container->get('tecnico.FoxPremium');
        $this->strSmsNombreTecnicoFoxPremium  = $container->getParameter('fox.producto.nombre_tecnico');
        $this->strSmsNombreTecnicoParamount   = $container->getParameter('paramount.producto.nombre_tecnico');
        $this->strSmsNombreTecnicoNoggin      = $container->getParameter('noggin.producto.nombre_tecnico');
        $this->emcom                          = $container->get('doctrine.orm.telconet_entity_manager');
        $this->serviceLicKaspersky = $container->get('tecnico.LicenciasKaspersky');
        $this->serviceOrquestador  = $container->get('comercial.Orquestador');
        $this->serviceTokenCas      = $container->get('seguridad.TokenCas');
        $this->serviceKonibit       = $container->get('comercial.ConsumoKonibit');
        $this->serviceInvestigacionDesarrollo = $container->get('tecnico.InvestigacionDesarrolloWs');

    }
    
    /**
     * confirmarServicio
     * 
     * Funcion que sirve realizar la confirmación de servicios en Tn, Md y Ttco
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 23-02-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 23-06-2016  validacion de servicio tecnico
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 13-09-2016 Se agrega el envío de correo si la confirmación es OK
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 24-01-2017 Se agregan nuevos parametros para activación de servicios SmartWifi
     * @since 1.3
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.5 05-07-2017 Se agrega en la validación que si es Telconet Panamá (TNP) que ejecute confirmarServicioTn 
     * 
     * @author Jesús Bozada <jbozadfa@telconet.ec>
     * @version 1.6 01-02-2018    Se agrega validacion para no finalizar tareas para tn automaticamente
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 22-01-2018 Se agrega programación para confirmar servicios originados por traslado de servicios
     *
     * @since 1.5
     * 
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.7 21-02-2018 Se adiciona el tipo orden.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 22-01-2018 Se agrega programación por integración de app Telcograph con procesos de Telcos
     * @since 1.7
     *
     * @author Wilmer Vera. <wvera@telconet.ec>
     * @version 1.8 20-06-2018 Se adiciona el tipo orden.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 22-07-2018 Se agrega flujo para la confirmación de servicios Small Business
     * 
     * @author Jesus Banchen <jbanchen@telconet.ec>
     * @version 2.0 28-03-2019 Se agregan validaciones para el sirvicio/producto de la empresa TNG
     * 
     * @author David Leon    <mdleon@telconet.ec>
     * @version 2.1 05-08-2019 Se agrega el producto L3MPLS SDWAN a los nombres tecnicos permitidos para monitoreo.
     * 
     * @author Modificado: Néstor Naula López <nnaulal@telconet.ec>
     * @version 2.2 21-05-2020 - Se realiza el envío de información cliente cancelado al Zabbix.
     * @since 2.1
     *
     * @author Modificado: Néstor Naula López <nnaulal@telconet.ec>
     * @version 2.3 20-07-2020 - Se realiza cambio del services de soporte a inforServicio para el proceso del Zabbix.
     * @since 2.2
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.4 20-10-2020 - Se agrega programación para consultar si un producto sin flujo esta hablitado para que se realice la activacion y
     *                           registro de elementos
     * @since 2.3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.5 19-11-2020 - Se agrega atributos capacidad y marca para el registro de la tarjeta de memoria en las camaras de netlifecam
     * @since 2.4
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.6 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     * @since 2.5
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.7 23-03-2022 - No se realiza la validación del valor de facturación para los servicios TN en la red GPON_MPLS.
     * @since 2.6
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.8 01-08-2022 - Se agrega en la validación del monitoreo los servicios cámaras vpn gpon safecity.
     * @since 2.7
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.9 03-10-2022 - Se agrega método confirmarServicioSegVehiculo para la confirmación de los servicio SEG VEHICULOS.
     * 
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 3.0 09-12-2022 - Se agregan la validacion para el producto SAFE ENTRY
     * 
     * @since 2.8
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 3.0 20/03/2023 - Se agrega empresa EN para confirmar el servicio desde TMO.
     * @since 2.9
     *
     * @author Josue Valencia <ajvalencia@telconet.ec>
     * @version 3.1 13-02-2023 - Se agrega validación para descartar el monitoreo por CLEAR CHANNEL PUNTO A PUNTO.
     * @since 3.0
     * 
     * @author Rafael Vera <rsvera@telconet.ec>
     * @version 3.2 26-06-2023 - Se corrige la validación de activación de MobileBus.
     * @since 3.1
     * 
     * @param Array $arrayPeticiones [
     *                                 - idEmpresa                     Identificador de Empresa
     *                                 - prefijoEmpresa                Prefijo de empresa
     *                                 - idServicio                    Identificador de servicio
     *                                 - idProducto                    Identificador de producto
     *                                 - serieCpe                      Cadena de caracteres que indica la serie del CPE 
     *                                 - codigoArticulo                Cadena de caracteres que indica el codigo del articulo
     *                                 - macCpe                        Cadena de caracteres que indica la mac del CPE
     *                                 - ssid                          Cadena de caracteres que indica el ssid a registrar
     *                                 - password                      Cadena de caracteres que indica la password a registrar
     *                                 - numeroPc                      Cadena de caracteres que indica el numeroPC a registrar
     *                                 - modoOperacion                 Cadena de caracteres que indica el modo de operacion a registrar
     *                                 - observacionCliente            Cadena de caracteres que indica la observación del cliente a registrar
     *                                 - observacionActivarServicio    Cadena de caracteres que indica la observación de activacion de servicios
     *                                 - jsonCaracteristicas           Json con caracteristicas a procesar
     *                                 - usrCreacion                   Cadena de caracteres que indica el usuario de creacion a utilizar.
     *                                 - ipCreacion                    Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - serNaf                        Cadena de caracteres que indica la serie NAF
     *                                 - ptoNaf                        Cadena de caracteres que indica el pto NAF
     *                                 - sidNaf                        Cadena de caracteres que indica el sid NAF
     *                                 - usrNaf                        Cadena de caracteres que indica el usr NAF
     *                                 - pswNaf                        Cadena de caracteres que indica el psw NAF
     *                                 - idAccion                      Identificador de accion 
     *                                 - empleadoSesion                Objeto del empleado en sesion
     *                                 - strSerieSmartWifi             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloSmartWifi            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strMacSmartWifi               Cadena de caracteres que indica la mac del equipo SmartWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                                 - strNombreTecnico              Nombre técnico del producto del servicio procesado
     *                                 - strIdPersonaEmpresaRol        Identificador del persona empresa rol del usuario en sesión
     *                                 - strIdDepartamento             Identificador del departamento del usuario en sesión
     *                                 - origen                        Origen de la activación.
     *                               ]
     * @return Array $result [
     *                          - status   Estado de la transaccion ejecutada
     *                          - mensaje  Mensaje de la transaccion ejecutada
     *                       ]
     */
    public function confirmarServicio($arrayPeticiones)
    {
        $idEmpresa                  = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa             = $arrayPeticiones['prefijoEmpresa'];
        $idServicio                 = $arrayPeticiones['idServicio'];
        $idProducto                 = $arrayPeticiones['idProducto'];
        $serieCpe                   = $arrayPeticiones['serieCpe'];
        $codigoArticulo             = $arrayPeticiones['codigoArticulo'];
        $macCpe                     = $arrayPeticiones['macCpe'];
        $ssid                       = $arrayPeticiones['ssid'];
        $password                   = $arrayPeticiones['password'];
        $numeroPc                   = $arrayPeticiones['numeroPc'];
        $modoOperacion              = $arrayPeticiones['modoOperacion'];
        $observacionCliente         = $arrayPeticiones['observacionCliente'];
        $observacionActivarServicio = $arrayPeticiones['observacionActivarServicio'];
        $strProductoPermitidoReg    = $arrayPeticiones['productoPermitidoRegistroEle']?$arrayPeticiones['productoPermitidoRegistroEle']:"N";
        $strCapacidadTarjeta        = $arrayPeticiones['capacidadTarjeta']?$arrayPeticiones['capacidadTarjeta']:"";
        $strMarcaTarjeta            = $arrayPeticiones['marcaTarjeta']?$arrayPeticiones['marcaTarjeta']:"";
        $strModeloTarjeta           = $arrayPeticiones['modeloTarjeta']?$arrayPeticiones['modeloTarjeta']:"";
        $strSerieTarjeta            = $arrayPeticiones['serieTarjeta']?$arrayPeticiones['serieTarjeta']:"";
        $strTipoElemento            = $arrayPeticiones['strTipoElemento'];
        $jsonCaracteristicas        = $arrayPeticiones['jsonCaracteristicas'];
        $usrCreacion                = $arrayPeticiones['usrCreacion'];
        $ipCreacion                 = $arrayPeticiones['ipCreacion'];
        $serNaf                     = $arrayPeticiones['serNaf'];
        $ptoNaf                     = $arrayPeticiones['ptoNaf'];
        $sidNaf                     = $arrayPeticiones['sidNaf'];
        $usrNaf                     = $arrayPeticiones['usrNaf'];
        $pswNaf                     = $arrayPeticiones['pswNaf'];
        $idAccion                   = $arrayPeticiones['idAccion'];
        $empleadoSesion             = $arrayPeticiones['empleadoSesion'];
        $strSerieSmartWifi          = $arrayPeticiones['strSerieSmartWifi'];
        $strModeloSmartWifi         = $arrayPeticiones['strModeloSmartWifi'];
        $strMacSmartWifi            = $arrayPeticiones['strMacSmartWifi'];
        $intIdServicioInternet      = $arrayPeticiones['intIdServicioInternet'];
        $strOrigen                  = $arrayPeticiones['origen'];
        $strEsIsb                   = $arrayPeticiones['strEsIsb'] ? $arrayPeticiones['strEsIsb'] : "NO";
        $modeloElemento             = null;
        $interfaceElemento          = null;
        $servicio                   = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico            = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneBy(array( "servicioId" => $servicio->getId()));
        $producto                   = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($idProducto);
        if($servicioTecnico)
        {
            if($servicioTecnico->getInterfaceElementoId())
            {
                $interfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                ->find($servicioTecnico->getInterfaceElementoId());                
                $elementoId         = $interfaceElemento->getElementoId();
                $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
                $modeloElementoId   = $elemento->getModeloElementoId();
                $modeloElemento     = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($modeloElementoId);                
            }
        }
        $accionObj          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
            ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);
        
        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $idEmpresa= $arrayEmpresaMigra['id'];
                 $prefijoEmpresa= $arrayEmpresaMigra['prefijo'];
            }
        }
       
        if($prefijoEmpresa=="TTCO")
        {
            $result = $this->confirmarServicioTtco($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion,
                                                   $jsonCaracteristicas, $observacionCliente, $serieCpe, $codigoArticulo, 
                                                   $macCpe, $ssid, $numeroPc, $password, $modoOperacion, $idEmpresa,
                                                   $serNaf,$ptoNaf,$sidNaf,$usrNaf,$pswNaf,$prefijoEmpresa,$accionObj);
        }
        else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
        {
            $arrayParametrosConfirmarServiciosMd = array(
                                                          'ojbServicio'           => $servicio,
                                                          'objServicioTecnico'    => $servicioTecnico,
                                                          'objModeloElemento'     => $modeloElemento,
                                                          'objProducto'           => $producto,
                                                          'objInterfaceElemento'  => $interfaceElemento,
                                                          'strUsrCreacion'        => $usrCreacion,
                                                          'strIpCreacion'         => $ipCreacion,
                                                          'strIdEmpresa'          => $idEmpresa,
                                                          'objAccion'             => $accionObj,
                                                          'productoPermitidoReg'  => $strProductoPermitidoReg,
                                                          'capacidadTarjeta'      => $strCapacidadTarjeta,
                                                          'marcaTarjeta'          => $strMarcaTarjeta,
                                                          'modeloTarjeta'         => $strModeloTarjeta,
                                                          'serieTarjeta'          => $strSerieTarjeta,
                                                          'strTipoElemento'       => $strTipoElemento,
                                                          'strSerieSmartWifi'     => $strSerieSmartWifi,
                                                          'strModeloSmartWifi'    => $strModeloSmartWifi,
                                                          'strMacSmartWifi'       => $strMacSmartWifi,
                                                          'strOrigen'             => $strOrigen,
                                                          'intIdServicioInternet' => $intIdServicioInternet
                                                        );
        
            $result = $this->confirmarServicioMd( $arrayParametrosConfirmarServiciosMd );
        }
        else if($prefijoEmpresa == "TN" || $prefijoEmpresa == "TNP" || $prefijoEmpresa == "TNG")
        {
            if(is_object($servicio->getProductoId()) && $servicio->getProductoId()->getNombreTecnico() == "SEG_VEHICULO")
            {
                $result = $this->confirmarServicioSegVehiculo($arrayPeticiones);
            }
            elseif(is_object($servicio->getProductoId()) && $servicio->getProductoId()->getNombreTecnico() == "SAFE ENTRY")
            {
                $result = $this->confirmarServicioSafeEntry($arrayPeticiones);
            }
            elseif ($prefijoEmpresa == "TN" && $servicio->getTipoOrden() == "C")
            {
                $result = $this->confirmarServicioPorCambioTipoMedioTn($arrayPeticiones);
            }
            else if ($prefijoEmpresa == "TN" && $servicio->getTipoOrden() == "T" && $strEsIsb === "SI")
            {
                $result = $this->confirmarIsbPorTraslado($arrayPeticiones);
            }
            else if ($prefijoEmpresa == "TN" && $servicio->getTipoOrden() == "T")
            {
                $result = $this->confirmarServicioPorTrasladoTn($arrayPeticiones);
            }
            else
            {
                $result = $this->confirmarServicioTn($arrayPeticiones);
            }
        }
        else
        {
            //PENDIENTE DE PROGRAMAR
        }
        
        $status = $result[0]['status'];
        if($status == "OK")
        {
            $strPermiteFinalizarTarea = "SI";
            $strTipoOrden             = $servicio->getTipoOrden();
            if ($prefijoEmpresa == 'TN' && 
                (strpos($servicio->getProductoId()->getGrupo(),'DATACENTER') === false) &&
                $strTipoOrden == "N"
               )
            {
                $strPermiteFinalizarTarea = "NO";
            }
            //finalizar tareas generadas en solicitudes
            $objTipoSolicitudPlanficacion = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                              ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                                "estado"               => "Activo"));

            $objSolicitudPlanficacion     = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                              ->findOneBy(array("servicioId"      => $idServicio,
                                                                                "tipoSolicitudId" => $objTipoSolicitudPlanficacion->getId(),
                                                                                "estado"          => "Finalizada"),
                                                                          array('id'              => 'DESC'));
            
            if ($objSolicitudPlanficacion && $strPermiteFinalizarTarea == "SI")
            {           
                $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudPlanficacion->getId();
                $arrayParametros['strProceso']            = 'Activar';
                $strMensajeResponse                       = $this->emInfraestructura
                                                                 ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                 ->cerrarTareasPorSolicitud($arrayParametros);
            }
                        
            $arrayParametrosEnvioMail=array("servicio"                      => $servicio,
                                            "observacionActivarServicio"    => $observacionActivarServicio,
                                            "idEmpresa"                     => $idEmpresa,
                                            "prefijoEmpresa"                => $prefijoEmpresa,
                                            "empleadoSesion"                => $empleadoSesion,
                                            'user'                          => $usrCreacion,
                                            'ipClient'                      => $ipCreacion);
            $this->envioMailConfirmarServicio($arrayParametrosEnvioMail);
            
            if ($prefijoEmpresa == 'TN')
            {
                $strPermiteCrearMonitoreo = "NO";
                if(is_object($producto))
                {
                    $strNombreTecnico              = $producto->getNombreTecnico();
                    $arrayNombresTecnicoPermitidos = array("INTERNET","L3MPLS","INTMPLS","L3MPLS SDWAN","INTERNET SDWAN",
                                                           "SAFECITYDATOS","SAFECITYWIFI");

                    $arrayParametroClear = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('ESTADO_CLEAR_CHANNEL','COMERCIAL','','ESTADO_CLEAR_CHANNEL','','','','','',$idEmpresa);
                    $strDescripcionClear = $arrayParametroClear["valor1"];
                    if (in_array($strNombreTecnico, $arrayNombresTecnicoPermitidos) 
                      && $producto->getDescripcionProducto()!= $strDescripcionClear)
                    {
                        $strPermiteCrearMonitoreo = "SI";
                    }
                    //verificar si es servicio safecity
                    $arrayParSerAdd = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                         'INFRAESTRUCTURA',
                                                                         'PARAMETROS',
                                                                         'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                                         $producto->getId(),
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         $idEmpresa);
                    if(isset($arrayParSerAdd) && !empty($arrayParSerAdd) &&
                       isset($arrayParSerAdd["valor5"]) && $arrayParSerAdd["valor5"] == "CAMARAVPN")
                    {
                        $strPermiteCrearMonitoreo = "SI";
                    }
                }
                if ($strPermiteCrearMonitoreo == "SI")
                {                                                            
                    //Verifica si es un servicios SAFECITY
                    $strIngresoCamarasZabbix = null;
                    $strIngresoWifiZabbix    = null;
                    $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                     'INFRAESTRUCTURA',
                                                                     'PARAMETROS',
                                                                     'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                                     $servicio->getProductoId()->getId(),
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     '',
                                                                     $idEmpresa);
                    if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"])
                       && ($arrayParametrosDet["valor5"] == "CAMARA" || $arrayParametrosDet["valor5"] == "CAMARAVPN"))
                    {
                        $strIngresoCamarasZabbix = "S";
                    }
                    else if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"])
                       && $arrayParametrosDet["valor5"] == "WIFI")
                    {
                        $strIngresoWifiZabbix = "S";
                    }

                    //Generar creación de nuevo host para monitoreo de equipos en app TelcoGraph
                    $arrayParametrosTelcoGraph                    = array();
                    $arrayParametrosTelcoGraph['objInfoServicio'] = $servicio;
                    $arrayParametrosTelcoGraph['strUsrCreacion']  = $usrCreacion;
                    $arrayParametrosTelcoGraph['strIpCreacion']   = $ipCreacion;
                    $arrayParametrosTelcoGraph['strProceso']      = "crear";
                    if($strIngresoCamarasZabbix == "S" || $strIngresoWifiZabbix == "S")
                    {
                        $arrayParametrosTelcoGraph['strValidarFacturacion'] = "NO";
                    }
                    $this->servicioGeneral->procesaHostTelcoGraph($arrayParametrosTelcoGraph);

                    //Generar monitoreo del Zabbix
                    $arrayParametrosZabbix                      = array();
                    $arrayParametrosZabbix['objInfoServicio']   = $servicio;
                    $arrayParametrosZabbix['strIngresoCamaras'] = $strIngresoCamarasZabbix;
                    $arrayParametrosZabbix['strIngresoWifi']    = $strIngresoWifiZabbix;
                    $arrayParametrosZabbix['strUsrCreacion']    = $usrCreacion;
                    $arrayParametrosZabbix['strIpCreacion']     = $ipCreacion;
                    $arrayParametrosZabbix['strProceso']        = "crear";
                    if($strIngresoCamarasZabbix == "S" || $strIngresoWifiZabbix == "S")
                    {
                        $arrayParametrosZabbix['strValidarFact'] = "NO";
                    }
                    $this->servicioGeneral->enviarInfoClienteZabbix($arrayParametrosZabbix);
  
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Funcion que sirve para confirmar los servicios de TN
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 12-09-2017 - Se recibe array como respuesta a funcion confirmarServicioPorNuevoTn que contiene status y mensaje
     *                           Designar a la funcion interior el inicio y cierre de las Transacciones requeridas
     * 
     */
    public function confirmarServicioTn($arrayParametros)
    {
        try
        {
            $arrayRespuesta = $this->confirmarServicioPorNuevoTn($arrayParametros);
            
            $strStatus      = $arrayRespuesta['status'];
            if($strStatus!="OK")
            {
                throw new \Exception($arrayRespuesta['mensaje']);
            }
            else
            {
                $strMensaje="OK";
            }
        }
        catch(\Exception $e)
        {
            $strStatus             = "ERROR";
            $strMensaje            = $e->getMessage();
        }
        
        $respuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $respuestaFinal;
    }
    
        /**
     * confirmarServicioPorCambioTipoMedioTn
     * 
     * Funcion que sirve para confirmar los servicios de TN
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.0 21-01-2018
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-04-2018 Se agrega el envío del parámetro del código de la empresa a la función cancelarServicioTn
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 01-04-2019 - Se envía el parámetro $serviceCliente a la función generarJsonClientes
     *
     * @author David Leon <rcabrera@telconet.ec>
     * @version 1.3 05-08-2019 - Se valida el producto L3MPLS SDWAN realice la misma gestión del producto L3MPLS.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.4 18-08-2020 - Se agrega el objeto del service planificar al generar el json de los clientes.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.5 03-05-2021 - Se ajustan los parametros para el llamado del metodo generarJsonDatosBackbone, para que 
     *                           envie los parametros como un arreglo.
     *
     * @param arrayParametros
     */
    public function confirmarServicioPorCambioTipoMedioTn($arrayParametros)
    {
        $arrayNombresTecnicoIntDatos     = array("INTERNET", "L3MPLS", "INTMPLS", "L3MPLS SDWAN", "INTERNET SDWAN");
        $arrayNombresTecnicoIntWifi      = array("INTERNET WIFI");
        $arrayNombresTecnicoOtrosSmart   = array("OTROS", "SMARTSPACE");
        $arrayEstadosServicios           = array('Activo','In-Corte');
        $arrayOciCon                     = array();
        $arrayOciCon['user_comercial']   = $this->container->getParameter('user_comercial');
        $arrayOciCon['passwd_comercial'] = $this->container->getParameter('passwd_comercial');
        $arrayOciCon['dsn']              = $this->container->getParameter('database_dsn');
        $intIdServicio                   = $arrayParametros['idServicio'];
        $strMensajeCancelacionTraslado   = "";
        $strIdServicioOrigTraslado       = "";
        $strLoginOrigenTraslado          = "";
        $arrayParametrosJson             = array();
        $arrayParametrosCancelacion      = array();
        $objServicioOrigTraslado         = null;
        $objServicio                     = $this->emComercial
                                                ->getRepository('schemaBundle:InfoServicio')
                                                ->find($intIdServicio);
        $objServicioTecnico              = $this->emComercial
                                                ->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneByServicioId($intIdServicio);
        $strNombreTecnico                = $arrayParametros['strNombreTecnico'];
        $servicePlanificar               = $this->container->get('planificacion.planificar');
        if (empty($strNombreTecnico))
        {
            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                             ->findOneById($objServicio->getProductoId()->getId());
            if(is_object($objProducto))
            {
                $strNombreTecnico = $objProducto->getNombreTecnico();
            }
        }
        if (is_object($objServicio))
        {
            $objServProdCaractTraslado = $this->servicioGeneral
                                              ->getServicioProductoCaracteristica($objServicio, 
                                                                                  "ID_CAMBIO_TIPO_MEDIO", 
                                                                                  $objServicio->getProductoId());
            if (is_object($objServProdCaractTraslado))
            {
                $strIdServicioOrigTraslado = $objServProdCaractTraslado->getValor();
                $objServicioOrigenTraslado = $this->emComercial
                                                  ->getRepository('schemaBundle:InfoServicio')
                                                  ->find($strIdServicioOrigTraslado);
                if (is_object($objServicioOrigenTraslado))
                {
                    $strLoginOrigenTraslado = $objServicioOrigenTraslado->getPuntoId()->getLogin();
                }
            }
        }
        //se cancela el servicio Origen por tipo de nombre tecnico
        for ($j = 0; $j < count($arrayEstadosServicios); $j++)
        {
            $arrayParametrosJson = array(
                                        "plan"           => null,
                                        "producto"       => null,
                                        "login"          => $strLoginOrigenTraslado,
                                        "loginForma"     => null,
                                        "tipoServicio"   => null,
                                        "punto"          => null,
                                        "estado"         => $arrayEstadosServicios[$j],
                                        "start"          => null,
                                        "empresa"        => $arrayParametros['idEmpresa'],
                                        "ultimaMilla"    => null,
                                        "elemento"       => null,
                                        "interface"      => null,
                                        "ociCon"         => $arrayOciCon,
                                        "serviceTecnico" => $this->servicioGeneral,
                                        "serviceCliente" => $this->serviceCliente,
                                        "planificarService" => $servicePlanificar
                                        );
            $objJson = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->generarJsonClientes($arrayParametrosJson);
            $data=json_decode($objJson);
            for ($k = 0; $k < intval($data->total); $k++)
            {        
                if ($data->encontrados[$k]->idServicio >0 )
                {
                    if (strval($data->encontrados[$k]->idServicio) == $strIdServicioOrigTraslado)
                    {
                        $objJsonClienteOrigTraslado = $data->encontrados[$k];
                        $objServicioOrigTraslado    = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoServicio')
                                                           ->find($data->encontrados[$k]->idServicio);
                        $objServicioTecnicoOrigTraslado = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->findOneBy(array( "servicioId" => $data->encontrados[$k]->idServicio));
                        $objAdmiMotivoCancelarTraslado = $this->emGeneral
                                                              ->getRepository('schemaBundle:AdmiMotivo')
                                                              ->findOneBy(array("nombreMotivo" => "Cambio de Tipo Medio", 
                                                                                "estado"       => "Activo"));
                        break;
                    }
                }
            }
        }
        //si encontramos el servicio origen del traslado (Activo ó In-Corte) procedemos a cancelarlo
        if ($objJsonClienteOrigTraslado != null && 
            is_object($objServicioOrigTraslado) &&
            is_object($objAdmiMotivoCancelarTraslado) &&
            is_object($objServicioTecnicoOrigTraslado))
        {
            if (in_array($strNombreTecnico, $arrayNombresTecnicoIntDatos))
            {
                if ($strNombreTecnico == "L3MPLS" || $strNombreTecnico == "L3MPLS SDWAN")
                {
                    $arrayInfoBackbone = $this->emComercial
                                              ->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->getArrayInfoBackboneL3mpls($objServicioOrigTraslado,
                                                                           $objServicioTecnicoOrigTraslado,
                                                                           $this->emInfraestructura,
                                                                           $this->servicioGeneral);
                    $strMacOrigenTraslado = $arrayInfoBackbone["mac"];
                }
                else
                {

                    $jsonInfoBackbone = $this->emComercial
                                             ->getRepository('schemaBundle:InfoServicioTecnico')
                                             ->generarJsonDatosBackbone(
                                                 array(
                                                     'idServicio' => $objServicioOrigTraslado->getId(),
                                                     'empresa' => $arrayParametros['idEmpresa'],
                                                     'serviceTecnico' => $this->servicioGeneral,
                                                     'tipoElementoPadre' => "ROUTER",
                                                     'emComercial' => $this->emComercial,
                                                     'emInfraestructura' => $this->emInfraestructura
                                                 ));
                    $arrayInfoBackbone = json_decode($jsonInfoBackbone);
                    for ($k = 0; $k < intval($arrayInfoBackbone->total); $k++)
                    {        
                        $strMacOrigenTraslado = $arrayInfoBackbone->encontrados[$k]->mac;
                        break;
                    }
                }
                $arrayParametrosCancelacion = array(
                                                    'idEmpresa'             => $arrayParametros['idEmpresa'],
                                                    'prefijoEmpresa'        => $arrayParametros['prefijoEmpresa'],
                                                    'usrCreacion'           => $arrayParametros['usrCreacion'],
                                                    'idServicio'            => $objServicioOrigTraslado->getId(),
                                                    'objNuevoServicio'      => $objServicioTecnico,
                                                    'idProducto'            => $objServicioOrigTraslado->getProductoId()->getId(),
                                                    'idMotivo'              => $objAdmiMotivoCancelarTraslado->getId(),
                                                    'idAccion'              => 313,
                                                    'vlan'                  => $objJsonClienteOrigTraslado->vlan,
                                                    'mac'                   => $strMacOrigenTraslado,
                                                    'anillo'                => $objJsonClienteOrigTraslado->anillo,
                                                    'capacidadUno'          => $objJsonClienteOrigTraslado->capacidadUno,
                                                    'capacidadDos'          => $objJsonClienteOrigTraslado->capacidadDos,
                                                    'ipCreacion'            => $arrayParametros['ipCreacion'],
                                                    'idPersonaEmpresaRol'   => $arrayParametros['strIdPersonaEmpresaRol'],
                                                    'strTipoOrden'          => 'C'
                                                   );
                $arrayRespuesta = $this->cancelarServicio->cancelarServicioTn($arrayParametrosCancelacion);
                $strMensajeCancelOrigTraslado = $arrayRespuesta[0]['mensaje'];
                $strStatusCancelOrigTraslado  = $arrayRespuesta[0]['status'];
            }
            else if (in_array($strNombreTecnico, $arrayNombresTecnicoIntWifi))
            {
                $arrayParametrosCancelacion = array('intIdDepartamento'     => $arrayParametros['strIdDepartamento'],
                                                    'idEmpresa'             => $arrayParametros['idEmpresa'],
                                                    'prefijoEmpresa'        => $arrayParametros['prefijoEmpresa'],
                                                    'idServicio'            => $objServicioOrigTraslado->getId(),
                                                    'idProducto'            => $objServicioOrigTraslado->getProductoId()->getId(),
                                                    'motivo'                => $objAdmiMotivoCancelarTraslado->getId(),
                                                    'login'                 => $objServicioOrigTraslado->getPuntoId()->getLogin(),
                                                    'idAccion'              => 313,
                                                    'usrCreacion'           => $arrayParametros['usrCreacion'],
                                                    'ipCreacion'            => $arrayParametros['ipCreacion'],
                                                    'idPersonaEmpresaRol'   => $arrayParametros['strIdPersonaEmpresaRol']
                                                   );
                $arrayRespuesta               = $this->serviceElementoWifi->cancelarServicio($arrayParametrosCancelacion);
                $strMensajeCancelOrigTraslado = $arrayRespuesta[0]['mensaje'];
                $strStatusCancelOrigTraslado  = $arrayRespuesta[0]['status'];
            }
            else if (in_array($strNombreTecnico, $arrayNombresTecnicoOtrosSmart))
            {
                $arrayParametrosCancelacion = array(
                                                    'idServicio'  => $objServicioOrigTraslado->getId(),
                                                    'idEmpresa'   => $arrayParametros['idEmpresa'],
                                                    'idOficina'   => $arrayParametros['strIdOficina'],
                                                    'idAccion'    => 313,
                                                    'idMotivo'    => $objAdmiMotivoCancelarTraslado->getId(),
                                                    'usrCreacion' => $arrayParametros['usrCreacion'],
                                                    'clientIp'    => $arrayParametros['ipCreacion']
                                                   );
                $arrayRespuesta = $this->cancelarServicio->cancelarServiciosOtros($arrayParametrosCancelacion);
                $strMensajeCancelOrigTraslado = $arrayRespuesta['mensaje'];
                $strStatusCancelOrigTraslado  = $arrayRespuesta['status'];
            }
        }
        else
        {
            $strMensajeCancelOrigTraslado = "El servicio origen del cambio tipo medio no se encontro con estado ".
                                             "Activo ó In-Corte en el punto origen del traslado por eso no pudo ser cancelado";
        }
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            if($strStatusCancelOrigTraslado == "OK")
            {
                $arrayRespConfirmar = $this->confirmarServicioPorNuevoTn($arrayParametros);
            }
            else
            {
                throw new \Exception($strMensajeCancelOrigTraslado);
            }
            
            if($arrayRespConfirmar['status'] !="OK")
            {
                throw new \Exception('ERROR AL MOMENTO DE CONFIRMAR EL SERVICIO!');
            }
            else
            {
                $status  = $arrayRespConfirmar['status'];
                $mensaje = "OK";
            }
            $this->emComercial->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $status             = "ERROR";
            $mensaje            = $e->getMessage();
            $respuestaFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
            return $respuestaFinal;
        }
        
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->close();
        }

        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal[] = array('status' => $status, 'mensaje' => $mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * confirmarServicioPorTrasladoTn
     * 
     * Funcion que sirve para confirmar los servicios de TN originados por traslados
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 09-11-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 03-04-2018 Se regularizan cambios realizados en caliente
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 04-04-2018 Se agrega el envío del parámetro del código de la empresa a la función cancelarServicioTn
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 29-10-2018 Se modifica variable en validación de servicio origen de traslado para poder procesar estas solicitudes correctamente
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 01-04-2019 Se envía el parámetro $serviceCliente a la función generarJsonClientes
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.5 12-04-2021 Se declara el parámetro $servicePlanificar para llamar a la función generarJsonClientes
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 22-03-2021 Se abre la programacion para servicios Internet SDWAN
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.6 03-05-2021 - Se ajustan los parametros para el llamado del metodo generarJsonDatosBackbone, para que 
     *                           envie los parametros como un arreglo.
     *
     * @param arrayParametros ['idServicio', 'idAccion']
     */
    public function confirmarServicioPorTrasladoTn($arrayParametros)
    {
        $strNombreTecnico                = $arrayParametros['strNombreTecnico'];
        $arrayNombresTecnicoIntDatos     = array("INTERNET", "L3MPLS", "INTMPLS", "INTERNET SDWAN");
        $arrayNombresTecnicoIntWifi      = array("INTERNET WIFI");
        $arrayNombresTecnicoOtrosSmart   = array("OTROS", "SMARTSPACE");
        $arrayEstadosServicios           = array('Activo','In-Corte');
        $arrayOciCon                     = array();
        $arrayOciCon['user_comercial']   = $this->container->getParameter('user_comercial');
        $arrayOciCon['passwd_comercial'] = $this->container->getParameter('passwd_comercial');
        $arrayOciCon['dsn']              = $this->container->getParameter('database_dsn');
        $intIdServicio                   = $arrayParametros['idServicio'];
        $strMensajeCancelOrigTraslado    = "";
        $strIdServicioOrigTraslado       = "";
        $strLoginOrigenTraslado          = "";
        $arrayParametrosJson             = array();
        $arrayParametrosCancelacion      = array();
        $arrayParametrosActSolTraslado   = array();
        $objJsonClienteOrigTraslado      = null;
        $objServicioOrigTraslado         = null;
        $objServicio                     = null;
        $objServProdCaractTraslado       = null;
        $servicePlanificar               = $this->container->get('planificacion.planificar');
        try
        {
            $objServicio = $this->emComercial
                                ->getRepository('schemaBundle:InfoServicio')
                                ->find($intIdServicio);
            if (is_object($objServicio))
            {
                if (empty($strNombreTecnico))
                {
                    $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                     ->findOneById($objServicio->getProductoId()->getId());
                    if(is_object($objProducto))
                    {
                        $strNombreTecnico = $objProducto->getNombreTecnico();
                    }
                }
                $objServProdCaractTraslado = $this->servicioGeneral
                                                  ->getServicioProductoCaracteristica($objServicio, 
                                                                                      "TRASLADO", 
                                                                                      $objServicio->getProductoId());
                if (is_object($objServProdCaractTraslado))
                {
                    $strIdServicioOrigTraslado = $objServProdCaractTraslado->getValor();
                    $objServicioOrigenTraslado = $this->emComercial
                                                      ->getRepository('schemaBundle:InfoServicio')
                                                      ->find($strIdServicioOrigTraslado);
                    if (is_object($objServicioOrigenTraslado))
                    {
                        $strLoginOrigenTraslado = $objServicioOrigenTraslado->getPuntoId()->getLogin();
                    }
                }
            }
            if (!empty($strLoginOrigenTraslado))
            {    
                //se cancela el servicio Origen por tipo de nombre tecnico
                for ($j = 0; $j < count($arrayEstadosServicios); $j++)
                {
                    if($objJsonClienteOrigTraslado != null)
                    {
                        break;
                    }
                    $arrayParametrosJson = array(
                                                "plan"              => null,
                                                "producto"          => null,
                                                "login"             => $strLoginOrigenTraslado,
                                                "loginForma"        => null,
                                                "tipoServicio"      => null,
                                                "punto"             => null,
                                                "estado"            => $arrayEstadosServicios[$j],
                                                "start"             => null,
                                                "empresa"           => $arrayParametros['idEmpresa'],
                                                "ultimaMilla"       => null,
                                                "elemento"          => null,
                                                "interface"         => null,
                                                "ociCon"            => $arrayOciCon,
                                                "serviceTecnico"    => $this->servicioGeneral,
                                                "serviceCliente"    => $this->serviceCliente,
                                                "planificarService" => $servicePlanificar
                                                );

                    $objJson = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->generarJsonClientes($arrayParametrosJson);

                    $data=json_decode($objJson);
                    for ($k = 0; $k < intval($data->total); $k++)
                    {        
                        if ($data->encontrados[$k]->idServicio >0 )
                        {      
                            if (strval($data->encontrados[$k]->idServicio) == $strIdServicioOrigTraslado)
                            {
                                $objJsonClienteOrigTraslado = $data->encontrados[$k];
                                $objServicioOrigTraslado    = $this->emComercial
                                                                   ->getRepository('schemaBundle:InfoServicio')
                                                                   ->find($data->encontrados[$k]->idServicio);
                                $objServicioTecnicoOrigTraslado = $this->emComercial
                                                                       ->getRepository('schemaBundle:InfoServicioTecnico')
                                                                       ->findOneBy(array( "servicioId" => $data->encontrados[$k]->idServicio));
                                $objAdmiMotivoCancelarTraslado = $this->emGeneral
                                                                      ->getRepository('schemaBundle:AdmiMotivo')
                                                                      ->findOneBy(array("nombreMotivo" => "Traslado, cambio de dirección ".
                                                                                                          "o reubicación", 
                                                                                        "estado"       => "Activo"));
                                break;
                            }
                        }
                    }
                }
            }
            
            //si encontramos el servicio origen del traslado (Activo ó In-Corte) procedemos a cancelarlo
            if ($objJsonClienteOrigTraslado != null && 
                is_object($objServicioOrigTraslado) &&
                is_object($objAdmiMotivoCancelarTraslado) &&
                is_object($objServicioTecnicoOrigTraslado))
            {
                if (in_array($strNombreTecnico, $arrayNombresTecnicoIntDatos))
                {
                    if ($strNombreTecnico == "L3MPLS")
                    {
                        $arrayInfoBackbone = $this->emComercial
                                                  ->getRepository('schemaBundle:InfoServicioTecnico')
                                                  ->getArrayInfoBackboneL3mpls($objServicioOrigTraslado,
                                                                               $objServicioTecnicoOrigTraslado,
                                                                               $this->emInfraestructura,
                                                                               $this->servicioGeneral);
                        $strMacOrigenTraslado = $arrayInfoBackbone["mac"];
                    }
                    else
                    {
                        $jsonInfoBackbone = $this->emComercial
                                                 ->getRepository('schemaBundle:InfoServicioTecnico')
                                                 ->generarJsonDatosBackbone(
                                                     array(
                                                     'idServicio' => $objServicioOrigTraslado->getId(),
                                                     'empresa' => $arrayParametros['idEmpresa'],
                                                     'serviceTecnico' => $this->servicioGeneral,
                                                     'tipoElementoPadre' => "ROUTER",
                                                     'emComercial' => $this->emComercial,
                                                     'emInfraestructura' => $this->emInfraestructura
                                                 ));
                        $arrayInfoBackbone = json_decode($jsonInfoBackbone);
                        for ($k = 0; $k < intval($arrayInfoBackbone->total); $k++)
                        {        
                            $strMacOrigenTraslado = $arrayInfoBackbone->encontrados[$k]->mac;
                            break;
                        }
                    }

                    $arrayParametrosCancelacion = array(
                                                        'idEmpresa'             => $arrayParametros['idEmpresa'],
                                                        'prefijoEmpresa'        => $arrayParametros['prefijoEmpresa'],
                                                        'usrCreacion'           => $arrayParametros['usrCreacion'],
                                                        'idServicio'            => $objServicioOrigTraslado->getId(),
                                                        'idProducto'            => $objServicioOrigTraslado->getProductoId()->getId(),
                                                        'idMotivo'              => $objAdmiMotivoCancelarTraslado->getId(),
                                                        'idAccion'              => 313,
                                                        'vlan'                  => $objJsonClienteOrigTraslado->vlan,
                                                        'mac'                   => $strMacOrigenTraslado,
                                                        'anillo'                => $objJsonClienteOrigTraslado->anillo,
                                                        'capacidadUno'          => $objJsonClienteOrigTraslado->capacidadUno,
                                                        'capacidadDos'          => $objJsonClienteOrigTraslado->capacidadDos,
                                                        'ipCreacion'            => $arrayParametros['ipCreacion'],
                                                        'idPersonaEmpresaRol'   => $arrayParametros['strIdPersonaEmpresaRol'],
                                                        'strOrigen'             => 'T',
                                                        'strObservacion'        => 'Se Traslado el Servicio'
                                                       );
                    $arrayRespuestaCancelTn = $this->cancelarServicio->cancelarServicioTn($arrayParametrosCancelacion);
                    $strMensajeCancelOrigTraslado = $arrayRespuestaCancelTn[0]['mensaje'];
                    $strStatusCancelOrigTraslado  = $arrayRespuestaCancelTn[0]['status'];
                }
                else if (in_array($strNombreTecnico, $arrayNombresTecnicoIntWifi))
                {
                    $arrayParametrosCancelacion = array('intIdDepartamento'     => $arrayParametros['strIdDepartamento'],
                                                        'idEmpresa'             => $arrayParametros['idEmpresa'],
                                                        'prefijoEmpresa'        => $arrayParametros['prefijoEmpresa'],
                                                        'idServicio'            => $objServicioOrigTraslado->getId(),
                                                        'idProducto'            => $objServicioOrigTraslado->getProductoId()->getId(),
                                                        'motivo'                => $objAdmiMotivoCancelarTraslado->getId(),
                                                        'login'                 => $objServicioOrigTraslado->getPuntoId()->getLogin(),
                                                        'idAccion'              => 313,
                                                        'usrCreacion'           => $arrayParametros['usrCreacion'],
                                                        'ipCreacion'            => $arrayParametros['ipCreacion'],
                                                        'idPersonaEmpresaRol'   => $arrayParametros['strIdPersonaEmpresaRol'],
                                                        'strOrigen'             => 'T',
                                                        'strObservacion'        => 'Se Traslado el Servicio'
                                                       );
                    $arrayRespuestaCancelTn = $this->serviceElementoWifi->cancelarServicio($arrayParametrosCancelacion);
                    $strMensajeCancelOrigTraslado = $arrayRespuestaCancelTn[0]['mensaje'];
                    $strStatusCancelOrigTraslado  = $arrayRespuestaCancelTn[0]['status'];
                }
                else if (in_array($strNombreTecnico, $arrayNombresTecnicoOtrosSmart))
                {
                    $arrayParametrosCancelacion = array(
                                                        'idServicio'  => $objServicioOrigTraslado->getId(),
                                                        'idEmpresa'   => $arrayParametros['idEmpresa'],
                                                        'idOficina'   => $arrayParametros['strIdOficina'],
                                                        'idAccion'    => 313,
                                                        'idMotivo'    => $objAdmiMotivoCancelarTraslado->getId(),
                                                        'usrCreacion' => $arrayParametros['usrCreacion'],
                                                        'clientIp'    => $arrayParametros['ipCreacion'],
                                                        'strOrigen'      => 'T',
                                                        'strObservacion' => 'Se Traslado el Servicio'
                                                       );
                    $arrayRespuestaCancelTn = $this->cancelarServicio->cancelarServiciosOtros($arrayParametrosCancelacion);
                    $strMensajeCancelOrigTraslado = $arrayRespuestaCancelTn['mensaje'];
                    $strStatusCancelOrigTraslado  = $arrayRespuestaCancelTn['status'];
                }
            }
            else
            {
                if (is_object($objServProdCaractTraslado))
                {
                    if(is_object($objServicioOrigenTraslado) && 
                      ($objServicioOrigenTraslado->getEstado() == 'Cancel' || $objServicioOrigenTraslado->getEstado() == 'Trasladado'))
                    {
                        $strStatusCancelOrigTraslado = "OK";
                        $strMensajeCancelOrigTraslado = "El servicio origen del traslado ya se encontraba Cancelado anteriormente.";
                    }
                    else
                    {
                        $strMensajeCancelOrigTraslado = "El servicio origen del traslado no se encontro con estado " .
                            "Activo ó In-Corte en el punto origen del traslado por eso no pudo ser cancelado.";
                        $strStatusCancelOrigTraslado = "ERROR";
                    }
                }
                else
                {
                    $strStatusCancelOrigTraslado  = "OK";
                    $strMensajeCancelOrigTraslado = "El servicio traslado pertenece al flujo anterior.";
                }
            }
        } 
        catch (Exception $objEx) 
        {
            $status             = "ERROR";
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioPorTrasladoTn',
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'],
                                            $arrayParametros['ipCreacion']);
            $respuestaFinal[]   = array('status'  => $status, 
                                        'mensaje' => "Ocurrió un error general en la cancelación del " .
                                                     "servicio origen del traslado, favor notificar a sistemas.");
            return $respuestaFinal;
        }
        
        if ($strStatusCancelOrigTraslado != "OK" )
        {
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioPorTrasladoTn',
                                            $strMensajeCancelOrigTraslado,
                                            $arrayParametros['usrCreacion'],
                                            $arrayParametros['ipCreacion']);
            $respuestaFinal[]   = array('status'  => $status, 
                                        'mensaje' => "Ocurrió un error en la cancelación del " .
                                                     "servicio origen del traslado, favor notificar a sistemas.");
            return $respuestaFinal;
        }
        
        try
        {
            $arrayRespuesta = $this->confirmarServicioPorNuevoTn($arrayParametros);
            $strStatus      = $arrayRespuesta['status'];
            if($strStatus != "OK")
            {
                throw new \Exception('ERROR AL MOMENTO DE CONFIRMAR EL SERVICIO!');
            }
            else
            {
                //se debe cambiar el estado de la solicitud de traslado para que pueda facturarse
                $arrayParametrosActSolTraslado['objServicio']    = $objServicio;
                $arrayParametrosActSolTraslado['strUsrCreacion'] = $arrayParametros['usrCreacion'];
                $arrayParametrosActSolTraslado['strIpCreacion']  = $arrayParametros['ipCreacion'];
                $this->actualizarSolicitudTrasladoTN($arrayParametrosActSolTraslado);
                $status  = $strStatus;
                $mensaje = "OK";
            }
            
        }
        catch(\Exception $objEx)
        {
            $status = "ERROR";
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioPorTrasladoTn',
                                            $objEx->getMessage(),
                                            $arrayParametros['usrCreacion'],
                                            $arrayParametros['ipCreacion']);
            
            $respuestaFinal[]   = array('status'  => $status, 
                                        'mensaje' => "Ocurrió un error general en la confirmación del servicio, favor notificar a sistemas.");
            return $respuestaFinal;
        }
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal[] = array('status' => $status, 'mensaje' => $mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * actualizarSolicitudTrasladoTN
     * 
     * Funcion que sirve para actualizar solicitud de traslado TN para que entre en proceso de facturación
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 10-01-2018
     * @since 1.0
     * 
     * @param arrayParametros ['objServicio','strUsrCreacion','strIpCreacion']
     */
    public function actualizarSolicitudTrasladoTN($arrayParametros)
    {
        $this->emComercial->getConnection()->beginTransaction();
        
        $objTipoSolicitud = $this->emComercial
                                 ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                 ->findOneByDescripcionSolicitud("SOLICITUD TRASLADO");
        
        $arrayCaracteristicasParametros = array('estado'                    => "Activo", 
                                                'descripcionCaracteristica' => "ID_PUNTO");
        
        $objCaracteristicaIdPunto       = $this->emComercial
                                               ->getRepository("schemaBundle:AdmiCaracteristica")
                                               ->findOneBy( $arrayCaracteristicasParametros );

        if( is_object($objCaracteristicaIdPunto) )
        {
            $objDetSolCaract = $this->emComercial
                                    ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                    ->findOneBy(array("valor"            => $arrayParametros ['objServicio']->getPuntoId()->getId(),
                                                      "caracteristicaId" => $objCaracteristicaIdPunto->getId(),
                                                      "estado"           => "Pendiente"));
            if (is_object($objDetSolCaract) && is_object($objTipoSolicitud))
            {
                if ($objDetSolCaract->getDetalleSolicitudId()->getTipoSolicitudId()->getId() == $objTipoSolicitud->getId())
                {
                    $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objDetSolCaract, 'PendienteFact');
                    $objDetSolTraslado = $objDetSolCaract->getDetalleSolicitudId();
                    $objDetSolTraslado->setEstado("PendienteFact");
                    $this->emComercial->persist($objDetSolTraslado);
                    $this->emComercial->flush();
                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objDetSolTraslado);
                    $objDetalleSolHist->setObservacion("Se actualiza solicitud para ser facturada.");
                    $objDetalleSolHist->setIpCreacion($arrayParametros ['strIpCreacion']);
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($arrayParametros ['strUsrCreacion']);
                    $objDetalleSolHist->setEstado("PendienteFact");
                    $this->emComercial->persist($objDetalleSolHist);
                    $this->emComercial->flush();
                    
                }
            }
        }
        $this->emComercial->getConnection()->commit();
        $this->emComercial->getConnection()->close();
    }
    
    /**
     * Funcion que sirve para confirmar servicio por tipo de orden nueva
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 17-01-2017  Se agrega programación para almacenamiento de caracteristicas
     *                          de producto Smart Space
     * @since 1.0
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 12-09-2017  Se agrega programación para poder activar automaticamente productos que pertenecen a Grupo/Solucion de Servicios
     *                          cuando se confirme servicios Preferentes de cada grupo/solucion
     *                          Se agrega bloque de codigo para poder finalizar las tareas automaticas generadas
     * @since 1.1
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 07-03-2018  Se ajusta confirmacion para que funcione bajo esquema Multi Solución ( NxN )     
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 21-01-2019 - Se realizan ajustes para ingresar información de los equipos de seguridad logica
     * @since 1.3
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 24-04-2019 - En el registro de equipos de seguridad lógica se agrega opción que permite ingresar la propiedad del equipo, si es
     *                           Cliente o Telconet
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.6 01-08-2019 - Se agrega funcionalidad para que se cree un login auxiliar a los servicios WIFI Alquiler Equipos.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.7 02-05-2020 - Se agrega funcionalidad para que se cree login auxiliar a los servicios que no posean la caracteristica
     *                           'TIENE_FLUJO'.
     *
     * @since 1.4
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.8 14-07-2020 - Para los servicios que son de una solución, se procede a obtener la información de las nuevas
     *                           estructuras.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.9 22-09-2020 - Se consulta si el servicio tiene características adicionales.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.0 22-12-2020 - Se consulta si el producto tiene enlace para generar el login auxiliar.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 09-12-2020 - Se ingresa la solicitud de RPA licenciamiento para los equipos que requieran licencia.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.1 25-02-2021 - Se agrega el proceso encargado de actualizar la información de la actividad en la info_tarea.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.2 06-04-2021 - Se modifica la validación para actualizar en la info tarea las actividades finalizadas.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.3 01-08-2022 - Se valida el parámetro booleanEliminarEnlaces para eliminar los enlaces
     *                           de las interfaces del elemento.
     * 
     * @author Joel Muñoz M <gvalenzuela@telconet.ec>
     * @version 2.4 03-10-2022 - Se agregan parámetros y validaciones para recibir y procesar datos de
     * productos SECURITY NG FIREWALL
     *
     * @author Andre Lazo V <alazo@telconet.ec>
     * @version 2.5 23-12-2022 - Se agregan parámetros y validaciones para recibir y procesar datos de
     * productos CLEAR CHANNEL PUNTO A PUNTO
     * 
     * @param arrayParametros ['idServicio', 'idAccion']
     * 
     * @return Array arrayRespuesta [ status , mensaje ]
     */
    public function confirmarServicioPorNuevoTn($arrayParametros)
    {
        $idServicio                 = $arrayParametros['idServicio'];
        $idAccion                   = $arrayParametros['idAccion'];
        $usrCreacion                = $arrayParametros['usrCreacion'];
        $ipCreacion                 = $arrayParametros['ipCreacion'];
        $strEsSmartSpace            = $arrayParametros['strEsSmartSpace'];
        $strCircuitoL1              = $arrayParametros['strCircuitoL1'];
        $strCircuitoL2              = $arrayParametros['strCircuitoL2'];
        $observacionActivarServicio = $arrayParametros['observacionActivarServicio'];
        $strSerieEquipo             = strtoupper($arrayParametros['serieEquipo']);
        $strDescEquipo              = $arrayParametros['descEquipo'];
        $intSubredId                   = $arrayParametros['idSubred'];
        $strRegistroEquipo          = $arrayParametros['registroEquipo']?$arrayParametros['registroEquipo']:"N";
        $strPropiedadEquipo         = $arrayParametros['propiedadEquipo']?$arrayParametros['propiedadEquipo']:"";
        $strPropiedadLan            = $arrayParametros['propiedadLan']?$arrayParametros['propiedadLan']:"";
        $strPropietario             = "";
        $strModeloEquipo            = $arrayParametros['modeloEquipo'];
        $strMacEquipo               = $arrayParametros['macEquipo'];
        $strIpEquipo                = $arrayParametros['ipEquipo'];
        $strEmpresaCod              = $arrayParametros['idEmpresa'];
        $arrayCaractAdicionales     = isset($arrayParametros['arrayCaractAdicionales']) ? $arrayParametros['arrayCaractAdicionales'] : null;
        $strObservacionServicio     = "";
        $arrayRespuesta             = array();
        $strStatus                  = 'OK';
        $strMensaje                 = 'OK';
        $arrayIdDetalle             = array();
        $strIdDetalle               = "";
        $strEsServicioMascarilla    = "N";
        $strAdministracionNGF     = !empty($arrayParametros['administracionNGF']) ? $arrayParametros['administracionNGF'] : null;
        $strPuertoAdminWebNGF     = !empty($arrayParametros['puertoAdminWebNGF']) ? $arrayParametros['puertoAdminWebNGF'] : null;
        $strSerialNGF     = !empty($arrayParametros['serialNGF']) ? $arrayParametros['serialNGF'] : null;
        $strNGFNubePublica     = !empty($arrayParametros['strNGFNubePublica']) ? $arrayParametros['strNGFNubePublica'] : null;
        $strSubred                 =  $arrayParametros['strSubred'];
        $strInterface              =  $arrayParametros['strInterface'];
        $strLoginUno                   = $arrayParametros['loginMonitoreo1'];
        $strLoginDos                   = $arrayParametros['loginMonitoreo2'];
        $boolBackUpUM                  = $arrayParametros['boolBackUpUM'];
        $boolPrincipalClearChannel       = $arrayParametros['boolPrincipalClearChannel'];
        $boolBackUpClearChannel         =$arrayParametros['boolBackUpClearChannel'];
        $strRequiereTransporte         =$arrayParametros['requiereTransporte'];
        $strPrefijoEmpresa             = $arrayParametros['prefijoEmpresa'];
        $boolPermisoActivarServiciotNGF = !empty($arrayParametros['permisoActivarServiciotNGF']) ? 
        $arrayParametros['permisoActivarServiciotNGF'] : null;
        $strIdPersonaEmpresaRol=$arrayParametros['strIdPersonaEmpresaRol'];
        $boolTieneNubePublicaNGF = !empty($arrayParametros['tieneNubePublica']) ? 
        $arrayParametros['tieneNubePublica'] : null;
        $strObservacionIp = "OBSERVACION: No Existen IPs disponibles para la Subred Pública requerida, escoja otra Subred";



        $this->emComercial->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $strDescripcionInterface="";
        $strDescripcionPropietario= $strPropiedadEquipo=='T'?'TELCONET':'CLIENTE';
        try
        {
            $objServicio= $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
            $objAccion  = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
            $objInterfaceConsulta=!is_numeric($strInterface)?null:$this->emInfraestructura->getRepository('schemaBundle:AdmiTipoInterface')
                                    ->findOneBy(['id'=> $strInterface]);
            if($objInterfaceConsulta!=null)
            {
                $strDescripcionInterface=$objInterfaceConsulta->getNombreTipoInterface();
            }
            else 
            {
                if($strInterface!="" && $strInterface!=null)
                {
                    $strDescripcionInterface=$strInterface;
                }
                else
                {
                    $strDescripcionInterface='N/A';
                }
            }
                //Obtengo el Producto
            $objProducto  = $objServicio->getProductoId();
            //Obtengo la Descripcion del Producto Clear a Channel Parametrizado
            $arrayParDet= $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->getOne('ESTADO_CLEAR_CHANNEL','COMERCIAL','','ESTADO_CLEAR_CHANNEL','','','','','',$strEmpresaCod);
            $strDescripProductoParamet = $arrayParDet["valor1"];
            $strTipoSubred="";
            //validamos que sea producto clear channel
            if($strPrefijoEmpresa == "TN")
            {
                $strDescripProducto = $objServicio->getProductoId()->getDescripcionProducto();
                $arrayHabilitarClearChannel= $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne('HABILITAR_APROVISIO_CLEAR_CHANNEL',
                                    'COMERCIAL',
                                    '',
                                    $strDescripProducto,
                                    '',
                                    '',
                                    '',
                                    '',
                                    '',
                                    $strEmpresaCod);
                $strHabilitarClearChannel = $arrayHabilitarClearChannel["valor1"];
                if( $strDescripProducto == $strDescripProductoParamet
                  && $strHabilitarClearChannel == 'SI')
                {
                    //REGISTRO DE TRAZABILIDAD 
                    //Se calcula el responsable de la trazabilidad de asignar responsable de retiro de equipo
                    $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->find($objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId());
                    if(is_object($objPersonaEmpresaRolUsr))
                    {
                        $intIdPersona = $objPersonaEmpresaRolUsr->getPersonaId()->getId();

                        $objInfoPersona = $this->emInfraestructura->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);

                        if(is_object($objInfoPersona))
                        {
                            if($objInfoPersona->getRazonSocial() != "")
                            {
                                $strResponsableTrazabilidad = $objInfoPersona->getRazonSocial();
                            }
                            else if($objInfoPersona->getNombres() != "" && $objInfoPersona->getApellidos() != "")
                            {
                                $strResponsableTrazabilidad = $objInfoPersona->getApellidos() . " " . $objInfoPersona->getNombres();
                            }
                            else if($objInfoPersona->getRepresentanteLegal() != "")
                            {
                                $strResponsableTrazabilidad = $objInfoPersona->getRepresentanteLegal();
                            }
                            else
                            {
                                $strResponsableTrazabilidad = "";
                            }
                        }
                    }


                    $objInfoElementoTrazabilidad = new InfoElementoTrazabilidad();
                    $objInfoElementoTrazabilidad->setNumeroSerie($strSerieEquipo);
                    $strLoginTrazabilidad="";
                    //Se obtiene el login asociado
                    if(is_object($objServicio))
                    {
                        $strLoginTrazabilidad = $objServicio->getPuntoId()->getLogin();
                    }

                    $objInfoElementoTrazabilidad->setEstadoTelcos("Activo");
                    $objInfoElementoTrazabilidad->setEstadoNaf("Instalado");
                    $objInfoElementoTrazabilidad->setEstadoActivo("Activo");
                    $objInfoElementoTrazabilidad->setLogin($strLoginTrazabilidad);
                    $objInfoElementoTrazabilidad->setUbicacion("Cliente");
                    $objInfoElementoTrazabilidad->setResponsable($strResponsableTrazabilidad);
                    $objInfoElementoTrazabilidad->setCodEmpresa($strEmpresaCod);
                    $objInfoElementoTrazabilidad->setObservacion("Confirmacion de Servicio");
                    $objInfoElementoTrazabilidad->setUsrCreacion($usrCreacion);
                    $objInfoElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
                    $objInfoElementoTrazabilidad->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($objInfoElementoTrazabilidad);
                    $this->emInfraestructura->flush();

             
                    $objInArticulosInstalacion = $this->emNaf->getRepository('schemaBundle:InArticulosInstalacion')
                        ->findOneBy([
                            'numeroSerie' => $strSerieEquipo,
                            'estado' => 'PI'
                        ]);

                    if($boolPrincipalClearChannel=="true")
                    {

                        //Creamos el nuevo elemento.
                        $arrayParDetTipoProducto= $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                            ->getOne('VALIDACIONES_CLEAR_CHANNEL','COMERCIAL','','TIPO_PRODUCTO_MODELO','','','','','',$strEmpresaCod);
                        if(count($arrayParDetTipoProducto)>0)
                        {
                            $strTipoProducto = $arrayParDetTipoProducto["valor1"]; 

                            $objIdTipoElemento=$this->emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                ->findOneBy(['nombreTipoElemento'=>$strTipoProducto]);

                            $objIdModeloElemento=$objIdTipoElemento==null?null:$this->emInfraestructura
                                ->getRepository('schemaBundle:AdmiModeloElemento')
                                ->findOneBy(['tipoElementoId'=>$objIdTipoElemento->getId(),
                                        'nombreModeloElemento'=>$strModeloEquipo]);

                            if( $objIdModeloElemento==null)
                            {
                                $objIdModeloElemento=$this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                ->findOneBy(['id'=>2675]);
                            }
                        } 

                        if( $objIdModeloElemento!=null)
                        {

                            $strNombreElemento=$objServicio->getPuntoId()->getLogin().'-'.$strDescripProducto.'-cpe';
                            $objInfoElemento = new InfoElemento();
                            $objInfoElemento->setModeloElementoId($objIdModeloElemento);
                            $objInfoElemento->setNombreElemento($strNombreElemento);
                            $objInfoElemento->setSerieFisica($strSerieEquipo);
                            $objInfoElemento->setUsrCreacion($usrCreacion);
                            $objInfoElemento->setFeCreacion(new \DateTime('now'));
                            $objInfoElemento->setIpCreacion($ipCreacion);
                            $objInfoElemento->setEstado('Activo');
                            $this->emInfraestructura->persist($objInfoElemento);
                            $this->emInfraestructura->flush();

                            //REGISTRO DE LA INTERFACE ELEMENTO
                            if($objInfoElemento!=null)
                            {

                                $objInterfaceElemento = new InfoInterfaceElemento();
                                $objInterfaceElemento->setNombreInterfaceElemento($strDescripcionInterface);
                                $objInterfaceElemento->setElementoId($objInfoElemento);
                                $objInterfaceElemento->setEstado("connect");
                                $objInterfaceElemento->setUsrCreacion($usrCreacion);
                                $objInterfaceElemento->setFeCreacion(new \DateTime('now'));
                                $objInterfaceElemento->setIpCreacion($ipCreacion);
                                $this->emInfraestructura->persist($objInterfaceElemento);
                                $this->emInfraestructura->flush();


                                //ASOCIAR A LA INFOTECNICO
                                $objServicioTecnicoActual = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneBy(array( "servicioId" => $idServicio));
                                if($objServicioTecnicoActual!=null)
                                {
                                    $objServicioTecnicoActual->setElementoClienteId($objInfoElemento->getId());
                                    $objServicioTecnicoActual->setInterfaceElementoClienteId($objInterfaceElemento->getId());
                                    $this->emComercial->persist($objServicioTecnicoActual);
                                    $this->emComercial->flush();
                                }
                            }
                        }

                        if($objInArticulosInstalacion!=null)
                        {
                    
                            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->find($strIdPersonaEmpresaRol);

                            $intIdPersona = $objPersonaEmpresaRolUsr->getPersonaId()->getId();
                            $arrayInfoActivo= $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                            ->obtenerEquiposAsignados(array('strIdEmpresa'   => $strEmpresaCod,
                                                            'intIdPersona'   => $intIdPersona,
                                                            'strNumeroSerie' => $strSerieEquipo));
                            
                            $arrayControlCustodio[] = array('numeroSerie'      => $strSerieEquipo,
                                                        'caracteristicaId' => 0,
                                                        'empresaId'        => $strEmpresaCod,
                                                        'cantidadEnt'      => $objServicio->getCantidad(),
                                                        'cantidadRec'      => $objServicio->getCantidad(),
                                                        'tipoTransaccion'  => 'NUEVO',
                                                        'transaccionId'    => 0,
                                                        'tareaId'          => 0,
                                                        'login'            => $objServicio->getPuntoId()->getLogin(),
                                                        'loginEmpleado'    => $usrCreacion,
                                                        'idControl'        =>  0,
                                                        'tipoArticulo'     => 'Equipos');
                        
                                                        
                            //Parámetros para realizar la carga y descarga del Activo.
                            $arrayCargaDescarga['intidPersonaEntrega'] = $strIdPersonaEmpresaRol;
                            $arrayCargaDescarga['intidPersonaRecibe']  = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId();
                            $arrayCargaDescarga['tipoActividad']       = 'Instalacion';
                            $arrayCargaDescarga['observacion']         = null;
                            $arrayCargaDescarga['arrayControlCusto']   = $arrayControlCustodio;

                            $strResultado = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                    ->registrarCargaDescargaActivos($arrayCargaDescarga);

                            //ACTUALIZAMOS EL ARTICULO EN NAF
                            $objInArticulosInstalacion->setEstado('IN');
                            $objInArticulosInstalacion->setFeUltMod(new \DateTime('now'));
                            $objInArticulosInstalacion->setUsrUltMod($usrCreacion);
                            $objInArticulosInstalacion->setSaldo(0);
                                $this->emNaf->persist($objInArticulosInstalacion);
                                $this->emNaf->flush();
                        }
                    }
               
                    //consultamos la subred en la tabla infossubred
                    if($strPropiedadLan =='T'&&$boolPrincipalClearChannel=="true")
                    {
                        $objSubred=$this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intSubredId);
                        //validamos si la subred no se encuentra registrada
                        if(is_null($objSubred))
                        {
                            $arrayRespuesta['status']  = "ERROR";
                            $arrayRespuesta['mensaje'] = "Error Subred no registrada";
                            
                            return $arrayRespuesta;
                        }
                        else
                        {
                            $strTipoSubred=$objSubred->getTipo();
                        }
                    }

                    $objInfoServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneByServicioId($idServicio);

                    
                    
                    $strObservacionServicio .= "<b>Propiedad:</b> ".$strDescripcionPropietario."<br/>";
                    $strObservacionServicio .= "<b>Serie Equipo:</b> ".$strSerieEquipo."<br/>";
                    $strObservacionServicio .= "<b>Modelo:</b> ".$strModeloEquipo."<br/>";
                    $strObservacionServicio .= "<b>Mac:</b> ".$strMacEquipo."<br/>";
                    $strObservacionServicio .= "<b>Ip:</b> ".$strIpEquipo."<br/>";
                    $strObservacionServicio .= "<b>Interface:</b> ".$strDescripcionInterface."<br/>";
                    if($strSubred!=null&&$strSubred!="")
                    {
                        $strObservacionServicio .= "<b>Subred LAN:</b> ".$strSubred."<br/>";
                    }
                    
                    $observacionActivarServicio = $observacionActivarServicio ."<br/>".$strObservacionServicio;

                    
                    $strElementoID=null;
                    /*SE VALIDA LA PROPIEDAD DEL EQUIPO Y SE SUSTRAE EL OBJETO SUBRED Y ID*/
                    if($strPropiedadEquipo=='T')
                    {
                        $strElementoID=$objInfoServicioTecnico->getelementoId();
                                    /**REGISTRO DE LA IP DEL EQUIPO  EN BASE AL TIPO DE ENLACE PRINCIPAL/BAKUP*/
                        if($boolPrincipalClearChannel=="true")
                        {
                            
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                                                                                            $objProducto,  
                                                                                            "SERIE_EQUIPO", 
                                                                                            $strSerieEquipo,  
                                                                                            $arrayParametros['usrCreacion']);
                            
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "MODELO_EQUIPO", 
                            $strModeloEquipo,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "PROPIETARIO DEL EQUIPO", 
                            $strDescripcionPropietario,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "IP_EQUIPO", 
                            $strIpEquipo,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "DESCRIPCION_EQUIPO", 
                            $strDescEquipo,  
                            $arrayParametros['usrCreacion']);
                            
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "MAC", 
                            $strMacEquipo,  
                            $arrayParametros['usrCreacion']);
                            
                            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                            if($strPropiedadLan =='T')
                            {
                                $objSubred=$this->emInfraestructura->getRepository('schemaBundle:InfoSubred')->find($intSubredId);
                            
                                if(!is_null($objSubred))
                                {
                                    //Se obtiene la ip siguiente disponible de la subred
                                    $strIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp') 
                                    ->getIpDisponibleBySubred($intSubredId); 

                                    if($strIp != 'NoDisponible')
                                    {
                                        // Se Almacena la IP Disponible para la 
                                        $objInfoIp = new InfoIp();
                                        $objInfoIp->setIp($strIp);
                                        $objInfoIp->setEstado("Activo");
                                        $objInfoIp->setServicioId($idServicio);
                                        $objInfoIp->setSubredId($intSubredId);
                                        $objInfoIp->setTipoIp($objSubred->getTipo());
                                        $objInfoIp->setMascara($objSubred->getMascara()); 
                                        $objInfoIp->setGateway($objSubred->getGateway()); 
                                        $objInfoIp->setVersionIp($objSubred->getVersionIp());
                                        $objInfoIp->setUsrCreacion($arrayParametros['usrCreacion']);
                                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                                        $objInfoIp->setIpCreacion($arrayParametros['ipCreacion']);
                                        $this->emInfraestructura->persist($objInfoIp);  
                                        $this->emInfraestructura->flush();
                                        

                                        //Se verifica que existan IP Disponibles en el rango
                                        $strVerificadorIp = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoIp') 
                                            ->getIpDisponibleBySubred($intSubredId); 
                                        
                                        if($strVerificadorIp == 'NoDisponible')
                                        {
                                            $objSubred->setEstado('Ocupado');
                                            $this->emInfraestructura->persist($objSubred);
                                            $this->emInfraestructura->flush();
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception($strObservacionIp);
                                    }
                                }
                            }
                        }
                                        
                        $intSubredId=null;
                        $intSubredId2=null;
                        if(!is_null($objSubred))
                        {
                            $intSubredId=$objSubred->getId();
                        }

                        if($boolBackUpClearChannel=="true")
                        {
                            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);

                            $objServicioProductoCarac= $this->servicioGeneral->getServicioProductoCaracteristica($objInfoServicio, 
                            "ES_BACKUP", 
                            $objInfoServicio->getProductoId());

                            $intIdPrincipal=$objServicioProductoCarac->getValor();


                                //ASOCIAR A LA INFOTECNICO
                            $objServicioTecnicoActualBackup = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                            ->findOneBy(array( "servicioId" => $idServicio));
                            $objServicioTecnicoAnteriorBackup = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                            ->findOneBy(array( "servicioId" => $intIdPrincipal));
                            
                            if($objServicioTecnicoActualBackup!=null&&$objServicioTecnicoAnteriorBackup!=null)
                            {
                                $objServicioTecnicoActualBackup->setElementoClienteId($objServicioTecnicoAnteriorBackup->getElementoClienteId());
                                $this->emComercial->persist($objServicioTecnicoActualBackup);
                                $this->emComercial->flush();
                            }
                            
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objInfoServicio,  
                                                                            $objProducto,  
                                                                            "IP_EQUIPO", 
                                                                            $strIpEquipo,  
                                                                            $arrayParametros['usrCreacion']);

                        }
                    }
                    else
                    {
                        if($boolPrincipalClearChannel=="true")
                        {
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "SERIE_EQUIPO", 
                            $strSerieEquipo,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "PROPIETARIO DEL EQUIPO", 
                            $strDescripcionPropietario,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "MODELO_EQUIPO", 
                            $strModeloEquipo,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "IP_EQUIPO", 
                            $strIpEquipo,  
                            $arrayParametros['usrCreacion']);

                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "DESCRIPCION_EQUIPO", 
                            $strDescEquipo,  
                            $arrayParametros['usrCreacion']);
                            
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,  
                            $objProducto,  
                            "MAC", 
                            $strMacEquipo,  
                            $arrayParametros['usrCreacion']);


                            $objInfoServicioCliente = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                            if($strPropiedadLan =='T')
                            {
                                $objSubredCliente=$this->emInfraestructura
                                        ->getRepository('schemaBundle:InfoSubred')->find($intSubredId);
                                
                            if(!is_null($objSubredCliente))
                            {
                                //Se obtiene la ip siguiente disponible de la subred
                                $strIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp') 
                                        ->getIpDisponibleBySubred($intSubredId); 

                                    if($strIp != 'NoDisponible')
                                    {
                                        // Se Almacena la IP Disponible para la 
                                        $objInfoIp = new InfoIp();
                                        $objInfoIp->setIp($strIp);
                                        $objInfoIp->setEstado("Activo");
                                        $objInfoIp->setServicioId($idServicio);
                                        $objInfoIp->setSubredId($intSubredId);
                                        $objInfoIp->setTipoIp($objSubredCliente->getTipo());
                                        $objInfoIp->setMascara($objSubredCliente->getMascara()); 
                                        $objInfoIp->setGateway($objSubredCliente->getGateway()); 
                                        $objInfoIp->setVersionIp($objSubredCliente->getVersionIp());
                                        $objInfoIp->setUsrCreacion($arrayParametros['usrCreacion']);
                                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                                        $objInfoIp->setIpCreacion($arrayParametros['ipCreacion']);
                                        $this->emInfraestructura->persist($objInfoIp);  
                                        $this->emInfraestructura->flush();
                                        

                                        //Se verifica que existan IP Disponibles en el rango
                                        $strVerificadorIp = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoIp') 
                                            ->getIpDisponibleBySubred($intSubredId); 
                                        
                                        if($strVerificadorIp == 'NoDisponible')
                                        {
                                            $objSubredCliente->setEstado('Ocupado');
                                            $this->emInfraestructura->persist($objSubredCliente);
                                            $this->emInfraestructura->flush();
                                        }
                                    }
                                    else
                                    {
                                        throw new \Exception($strObservacionIp);
                                    }
                                }
                            }

                        }

                        if($boolBackUpClearChannel=="true")
                        {
                            $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);

                            $objServicioProductoCarac= $this->servicioGeneral->getServicioProductoCaracteristica($objInfoServicio, 
                            "ES_BACKUP", 
                            $objInfoServicio->getProductoId());

                            $intIdPrincipal=$objServicioProductoCarac->getValor();


                                //ASOCIAR A LA INFOTECNICO
                            $objServicioTecnicoActualBackup = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                            ->findOneBy(array( "servicioId" => $idServicio));
                            $objServicioTecnicoAnteriorBackup = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                            ->findOneBy(array( "servicioId" => $intIdPrincipal));
                            
                            if($objServicioTecnicoActualBackup!=null&&$objServicioTecnicoAnteriorBackup!=null)
                            {
                                $objServicioTecnicoActualBackup->setElementoClienteId($objServicioTecnicoAnteriorBackup->getElementoClienteId());
                                $this->emComercial->persist($objServicioTecnicoActualBackup);
                                $this->emComercial->flush();
                            }
                            
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objInfoServicio,  
                                                                            $objProducto,  
                                                                            "IP_EQUIPO", 
                                                                            $strIpEquipo,  
                                                                            $arrayParametros['usrCreacion']);

                        }
                    }
    
            
                    /**REGISTRO DE LOGIN DE MONITOREO */
                    if($boolBackUpUM=="false")
                    {
                        $objInfoServicioMonitoreo = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);

                        $objServicioProductoCaracMonitoreo= $this->servicioGeneral->getServicioProductoCaracteristica($objInfoServicioMonitoreo, 
                        "ES_BACKUP", 
                        $objInfoServicioMonitoreo->getProductoId());
                        $intIdPrincipalMonitoreo=$objServicioProductoCaracMonitoreo->getValor();
                    
                        $objServicioTecnicoActualMonitoreo = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                        ->findOneBy(array( "servicioId" => $intIdPrincipalMonitoreo));
                        if($objServicioTecnicoActualMonitoreo!=null)
                        {
                            $objDetalleElementoLoginMonitoreoUno = new InfoDetalleElemento();
                            $objDetalleElementoLoginMonitoreoUno->setDetalleNombre("LOGIN MONITOREO UNO");
                            $objDetalleElementoLoginMonitoreoUno->setElementoId($objServicioTecnicoActualMonitoreo->getElementoClienteId());
                            $objDetalleElementoLoginMonitoreoUno->setDetalleValor($strLoginUno);
                            $objDetalleElementoLoginMonitoreoUno->setDetalleDescripcion("LOGIN MONITOREO UNO");
                            $objDetalleElementoLoginMonitoreoUno->setFeCreacion(new \DateTime('now'));
                            $objDetalleElementoLoginMonitoreoUno->setUsrCreacion($arrayParametros['usrCreacion']);
                            $objDetalleElementoLoginMonitoreoUno->setIpCreacion($arrayParametros['ipCreacion']);
                            $objDetalleElementoLoginMonitoreoUno->setEstado('Activo');
                            $this->emInfraestructura->persist($objDetalleElementoLoginMonitoreoUno);

                            $objDetalleElementoLoginMonitoreoDos = new InfoDetalleElemento();
                            $objDetalleElementoLoginMonitoreoDos->setDetalleNombre("LOGIN MONITOREO DOS");
                            $objDetalleElementoLoginMonitoreoDos->setElementoId($objServicioTecnicoActualMonitoreo
                                                                ->getElementoClienteId());
                            $objDetalleElementoLoginMonitoreoDos->setDetalleValor($strLoginDos);
                            $objDetalleElementoLoginMonitoreoDos->setDetalleDescripcion("LOGIN MONITOREO DOS");
                            $objDetalleElementoLoginMonitoreoDos->setFeCreacion(new \DateTime('now'));
                            $objDetalleElementoLoginMonitoreoDos->setUsrCreacion($arrayParametros['usrCreacion']);
                            $objDetalleElementoLoginMonitoreoDos->setIpCreacion($arrayParametros['ipCreacion']);
                            $objDetalleElementoLoginMonitoreoDos->setEstado('Activo');
                            $this->emInfraestructura->persist($objDetalleElementoLoginMonitoreoDos);
                            $this->emInfraestructura->flush();

                            //Generacion de Login Auxiliar al Servicio             
                            $this->servicioGeneral->generarLoginAuxiliar($idServicio);

                            $observacionActivarServicio .= "<b>Login Monitoreo Uno: </b>".$strLoginUno."<br/>";  
                            $observacionActivarServicio .= "<b>Login Monitoreo Dos: </b>".$strLoginDos."<br/>";
                        }
                    }
                            /**REGISTRO DE MAC DEL EQUIPO */
                    if($objInfoElemento!=null&&$boolPrincipalClearChannel=="true")
                    {
                        $objDetalleElementoMac = new InfoDetalleElemento();
                        $objDetalleElementoMac->setDetalleNombre("MAC");
                        $objDetalleElementoMac->setElementoId($objInfoElemento->getId());
                        $objDetalleElementoMac->setDetalleValor($strMacEquipo);
                        $objDetalleElementoMac->setDetalleDescripcion("MAC");
                        $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoMac->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElementoMac->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElementoMac->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoMac);
                        //REGISTRO DE LA PROPIEDAD DEL EQUIPO
                        $objDetalleElementoPropiedad = new InfoDetalleElemento();
                        $objDetalleElementoPropiedad->setDetalleNombre("PROPIEDAD");
                        $objDetalleElementoPropiedad->setElementoId($objInfoElemento->getId());
                        $objDetalleElementoPropiedad->setDetalleValor($strDescripcionPropietario);
                        $objDetalleElementoPropiedad->setDetalleDescripcion("PROPIEDAD");
                        $objDetalleElementoPropiedad->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoPropiedad->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElementoPropiedad->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElementoPropiedad->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoPropiedad);

                               //REGISTRO DE LA ADMINISTRA DEL EQUIPO
                        $objDetalleElementoAdministra = new InfoDetalleElemento();
                        $objDetalleElementoAdministra->setDetalleNombre("ADMINISTRA");
                        $objDetalleElementoAdministra->setElementoId($objInfoElemento->getId());
                        $objDetalleElementoAdministra->setDetalleValor("CLIENTE");
                        $objDetalleElementoAdministra->setDetalleDescripcion("ADMINISTRA");
                        $objDetalleElementoAdministra->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoAdministra->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElementoAdministra->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElementoAdministra->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoAdministra);


                        /**REGISTRO DE MODELO DEL EQUIPO */
                        $objDetalleElementoModelo = new InfoDetalleElemento();
                        $objDetalleElementoModelo->setDetalleNombre("MODELO");
                        $objDetalleElementoModelo->setElementoId($objInfoElemento->getId());
                        $objDetalleElementoModelo->setDetalleValor($strModeloEquipo);
                        $objDetalleElementoModelo->setDetalleDescripcion("MODELO");
                        $objDetalleElementoModelo->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoModelo->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElementoModelo->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElementoModelo->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoModelo);
                        /**REGISTRO DE SERIE DEL EQUIPO */
                        $objDetalleElementoSerie = new InfoDetalleElemento();
                        $objDetalleElementoSerie->setDetalleNombre("SERIE");
                        $objDetalleElementoSerie->setElementoId($objInfoElemento->getId());
                        $objDetalleElementoSerie->setDetalleValor($strSerieEquipo);
                        $objDetalleElementoSerie->setDetalleDescripcion("SERIE");
                        $objDetalleElementoSerie->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoSerie->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElementoSerie->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElementoSerie->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoSerie);
                        /**REGISTRO DE SUBRED DEL EQUIPO */
                        $objDetalleElementoSubred = new InfoDetalleElemento();
                        $objDetalleElementoSubred->setDetalleNombre("SUBRED");
                        $objDetalleElementoSubred->setElementoId($objInfoElemento->getId());
                        $objDetalleElementoSubred->setDetalleValor($strSubred);
                        $objDetalleElementoSubred->setDetalleDescripcion("SUBRED");
                        $objDetalleElementoSubred->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoSubred->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objDetalleElementoSubred->setIpCreacion($arrayParametros['ipCreacion']);
                        $objDetalleElementoSubred->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoSubred);

                        $this->emInfraestructura->flush();
                    }

                        /**Obtenemos el id de la caracteristica del producto */

                        $objInfoServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                        $objAdmiProducto = $objInfoServicio->getProductoId();
                        $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy([
                            'descripcionCaracteristica'=>'INTERFACE_EQUIPO'
                        ]);
                        $objProductoCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy([
                            'caracteristicaId'=>$objCaracteristica->getId(),
                            'productoId'=>$objAdmiProducto->getId()
                        ]);
                        
                        /**REGISTRO DE LA INTERFACE DEL EQUIPO */
                        $objServicioProductoCaracteristicaMac = new InfoServicioProdCaract();
                        $objServicioProductoCaracteristicaMac->setServicioId($idServicio);
                        $objServicioProductoCaracteristicaMac->setProductoCaracterisiticaId($objProductoCaracteristica->getId());
                        $objServicioProductoCaracteristicaMac->setValor($strInterface);
                        $objServicioProductoCaracteristicaMac->setEstado("Activo");
                        $objServicioProductoCaracteristicaMac->setUsrCreacion($usrCreacion);
                        $objServicioProductoCaracteristicaMac->setFeCreacion(new \DateTime('now'));
                        $this->emComercial->persist($objServicioProductoCaracteristicaMac);
                        $this->emComercial->flush();

                    
                        if($strRequiereTransporte=="SI")
                        {
                            //Generacion de Login Auxiliar al Servicio             
                            $this->servicioGeneral->generarLoginAuxiliar($idServicio);
                        }
                }
            }
            $objServicio->setEstado("Activo");
            $this->emComercial->persist($objServicio);

            
            /*Obtenemos el array del parámetro INSTALACIÓN SIMULTANEA.*/
            $objParamsDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('CARACTERISTICAS_SERVICIOS_SIMULTANEOS',
                    'TECNICO',
                    'INSTALACION_SIMULTANEA',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    10);

            /*Obtengo el objeto del producto con su Id.*/
            $objProductoAlquiler = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                     ->find($objServicio->getProductoId());

            /*Si la operación anterior haya traido data.*/
            if (is_array($objParamsDet) && !empty($objParamsDet))
            {
                /*Convertimos el contenido de VALOR1 a un objeto asociativo.*/
                $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);

                $arrayParams['strNeedle'] = $objProductoAlquiler->getDescripcionProducto();
                $arrayParams['strKey'] = 'DESCRIPCION_PRODUCTO';
                $arrayParams['arrayToSearch'] = $objCaracteristicasServiciosSimultaneos;

                /*Realizamos uns búsqueda del producto obtenido en el objeto de características simultaneo.*/
                $arrayCaracteristicasServicioSimultaneo = $this->servicioGeneral->searchByKeyInArray($arrayParams);
                
            }

            /*Valido que sea un objeto y que la descripcion sea igual a "WIFI Alquiler Equipos".*/
            if ((is_object($objProductoAlquiler) && $objProductoAlquiler->getDescripcionProducto() == "WIFI Alquiler Equipos") ||
                (isset($arrayCaracteristicasServicioSimultaneo) && !is_null($arrayCaracteristicasServicioSimultaneo) && 
                !$arrayCaracteristicasServicioSimultaneo['TIENE_FLUJO']))
            {
                /*Si cumple con la condición, creará un login auxiliar.*/
                $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());
            }
            else
            {
                //Consultamos si el producto tiene enlace para generar login auxiliar
                if (is_object($objProductoAlquiler) && $objProductoAlquiler->getEsEnlace() == "SI")
                {
                    $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());
                }
            }
            
            
            
            //Consultamos si el servicio que se va activar tiene características adicionales
            if ($arrayCaractAdicionales)
            {
                $strObservacionActivarFastcloud = 'Se agregaron las siguientes características del servicio: ';
                                                
                foreach ($arrayCaractAdicionales as $arrayCaractAdicional)
                {
                    if(isset($arrayCaractAdicional['FIELD_VALUE']) && !empty($arrayCaractAdicional['FIELD_VALUE']))
                    {
                        //consulto si el campo es textarea para separarlas por el enter
                        if ($arrayCaractAdicional['XTYPE'] == 'textarea')
                        {
                            $strTextArea = preg_replace('/\n$/','',preg_replace('/^\n/','',preg_replace('/[\r\n]+/',"\n",
                                                        $arrayCaractAdicional['FIELD_VALUE'])));
                            $arrayTextArea = explode("\n",$strTextArea);
                            $strObservacionActivarFastcloud = $strObservacionActivarFastcloud
                                                               .'<br> <b>'.$arrayCaractAdicional['DESCRIPCION_CARACTERISTICA'].':'.'</b>';
                            foreach($arrayTextArea as $arrayValorTextArea)
                            {
                                $strValorTextArea = $arrayValorTextArea;
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,
                                                                                        $objProducto,
                                                                                        $arrayCaractAdicional['DESCRIPCION_CARACTERISTICA'],
                                                                                        $strValorTextArea,
                                                                                        $usrCreacion);
                                $strObservacionActivarFastcloud = $strObservacionActivarFastcloud.$strValorTextArea.' - ';
                            }
                        }
                        else
                        {
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,
                                                                                        $objProducto,
                                                                                        $arrayCaractAdicional['DESCRIPCION_CARACTERISTICA'],
                                                                                        $arrayCaractAdicional['FIELD_VALUE'],
                                                                                        $usrCreacion);
                            
                            $strObservacionActivarFastcloud = $strObservacionActivarFastcloud
                                                           .'<br> <b>'.$arrayCaractAdicional['DESCRIPCION_CARACTERISTICA'].':'.'</b>'
                                                           .$arrayCaractAdicional['FIELD_VALUE'];
                        }
                    }
                }
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionActivarFastcloud);
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($usrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($ipCreacion);
                $objServicioHistorial->setAccion($objAccion->getNombreAccion());
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
            }

            //Para servicios de Seguridad Lógica
            if( $strRegistroEquipo == "S" )
            {
                $strCodigoArticulo        = "";
                $strTipoArticulo          = "AF";
                $strIdentificacionCliente = "";

                if(is_object($objServicio))
                {
                    //Se genera el login aux para el servicio
                    $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());
                    //verificamos si tiene la caracteristica de instancia para llamar al orquestador
                    $objServCaractInstancia = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                            'INSTANCIA_ID_ORQ',
                                                                                            $objServicio->getProductoId()
                                                                                            );
                    if(is_object($objServCaractInstancia) && !empty($objServCaractInstancia))
                    {
                        $arrayDatos = array('idServicio' => $objServicio->getId());
                        $arrayRespOrq = $this->serviceOrquestador->putProcesoTareaOrq($arrayDatos);
                    }
                }

                if($strPropiedadEquipo === "T")
                {
                    $strPropietario = "TELCONET";
                }
                else
                {
                    $strPropietario = "CLIENTE";
                }

                //Se arma el historial del servicio
                // SE VERIFICAR SI ES NG FIREWALL PARA REGISTRAR FLUJO NUBE PUBLICA
                if($objServicio && $objServicio->getProductoId() && is_object($objServicio->getProductoId()) 
                && $objServicio->getProductoId()->getDescripcionProducto() === 'SECURITY NG FIREWALL')
                {
                    $strObservacionServicio .= "<b>Informaci&oacute;n del elemento Cliente</b><br/>";
                    $strObservacionServicio .= "<b>Nube P&#250;blica:</b> {$strNGFNubePublica}<br/>";


                    if($strNGFNubePublica && $strNGFNubePublica != 'NINGUNO')
                    {
                        $strObservacionServicio .= "Ip/FQDN: ".$strIpEquipo."<br/>";
                        $strObservacionServicio .= "Puerto Administraci&#243;n Web: ".$strPuertoAdminWebNGF."<br/>";
                        $strObservacionServicio .= "Serial Licencia: ".$strSerialNGF."<br/>";
                    }
                    else
                    {
                        $strObservacionServicio .= "Propiedad: ".$strPropietario."<br/>";
                        $strObservacionServicio .= "Serie Equipo: ".$strSerieEquipo."<br/>";
                        $strObservacionServicio .= "Modelo: ".$strModeloEquipo."<br/>";
                        $strObservacionServicio .= "Mac: ".$strMacEquipo."<br/>";
                        $strObservacionServicio .= "Ip: ".$strIpEquipo."<br/>";
                    }
                }
                else
                {
                    
                    $strObservacionServicio .= "<b>Informaci&oacute;n del elemento Cliente</b><br/>";
                    $strObservacionServicio .= "Propiedad: ".$strPropietario."<br/>";
                    $strObservacionServicio .= "Serie Equipo: ".$strSerieEquipo."<br/>";
                    $strObservacionServicio .= "Modelo: ".$strModeloEquipo."<br/>";
                    $strObservacionServicio .= "Mac: ".$strMacEquipo."<br/>";
                    $strObservacionServicio .= "Ip: ".$strIpEquipo."<br/>";
                }

                $observacionActivarServicio = $observacionActivarServicio ."<br/>".$strObservacionServicio;

                //Se crea el elemento en el Telcos
                $arrayParametrosCpe = array(
                                            'nombreElementoCliente'         => $objServicio->getLoginAux(),
                                            'modeloElementoNuevo'           => $strModeloEquipo,
                                            'serieElementoNuevo'            => $strSerieEquipo,
                                            'descEquipo'                    => $strDescEquipo,
                                            'tipoElementoNuevo'             => 'CPE',
                                            'macElementoNuevo'              => null,
                                            'macElementoRegistrar'          => $strMacEquipo,
                                            'objServicio'                   => $objServicio,
                                            'macNuevo'                      => $strMacEquipo,
                                            'idEmpresa'                     => $arrayParametros['idEmpresa'],
                                            'usrCreacion'                   => $arrayParametros['usrCreacion'],
                                            'ipCreacion'                    => $arrayParametros['ipCreacion'],
                                            'bandRegistroEquipo'            => $strRegistroEquipo,
                                            'propiedadEquipo'               => $strPropietario
                                        );
                
                $objElementoCliente = $this->servicioGeneral->ingresarElementoClienteTNSinEnlace($arrayParametrosCpe);

                $objInterfaceElementoConector = null;
                if(is_object($objServicio) && is_object($objProducto)
                   && isset($arrayParametros['esServicioRequeridoSafeCity']) && $arrayParametros['esServicioRequeridoSafeCity'] == "S"
                   && isset($arrayParametros['idOnt']) && !empty($arrayParametros['idOnt'])
                   && isset($arrayParametros['puertosOnt']) && !empty($arrayParametros['puertosOnt']))
                {
                    $objElementoOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($arrayParametros['idOnt']);
                    if(is_object($objElementoOnt))
                    {
                        $objInterfaceElementoConector = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findOneBy(array("elementoId"              => $objElementoOnt,
                                                                       "nombreInterfaceElemento" => $arrayParametros['puertosOnt']));
                    }
                }
                //verificar interface conector
                if(is_object($objInterfaceElementoConector) && is_object($objElementoCliente))
                {
                    
                    //Obtiene por primera vez el puerto wan1 para activar el servicio
                    $objInterfaceElementoClienteIn = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                            ->findOneBy(array("elementoId" => $objElementoCliente->getId(),
                                                                              "estado"     => "connected"));
                    if(is_object($objInterfaceElementoClienteIn))
                    {
                        //obtengo el tipo medio
                        $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                    ->findOneByCodigoTipoMedio("FTTx");
                        if(is_object($objUltimaMilla))
                        {
                            //ingresar enlace
                            $objEnlace = new InfoEnlace();
                            $objEnlace->setInterfaceElementoIniId($objInterfaceElementoConector);
                            $objEnlace->setInterfaceElementoFinId($objInterfaceElementoClienteIn);
                            $objEnlace->setTipoMedioId($objUltimaMilla);
                            $objEnlace->setTipoEnlace("PRINCIPAL");
                            $objEnlace->setEstado("Activo");
                            $objEnlace->setUsrCreacion($arrayParametros['usrCreacion']);
                            $objEnlace->setFeCreacion(new \DateTime('now'));
                            $objEnlace->setIpCreacion($arrayParametros['ipCreacion']);
                            $this->emInfraestructura->persist($objEnlace);
                            $this->emInfraestructura->flush();
                        }
                    }
                }

                // SE VERIFICAR SI ES NG FIREWALL PARA REGISTRAR LA DATA EN INFO_SERV_PROD_CARAC
                
                if($objServicio && $objServicio->getProductoId() && is_object($objServicio->getProductoId()) 
                && $objServicio->getProductoId()->getDescripcionProducto() === 'SECURITY NG FIREWALL' 
                && $boolPermisoActivarServiciotNGF === '1' && $boolTieneNubePublicaNGF === '1')
                {
                    if(isset($strPuertoAdminWebNGF))
                    {
                        //Insertar nuevas caracteristicas: usuario y password
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                        $objProducto,
                        'PUERTO ADMINISTRACION WEB NG FIREWALL',
                        $strPuertoAdminWebNGF,
                        $arrayParametros['usrCreacion']);
                    }
  

                    if(isset($strSerialNGF))
                    {
                        //Insertar nuevas caracteristicas: usuario y password
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                        $objProducto,
                        'SERIAL LICENCIA NG FIREWALL',
                        $strSerialNGF,
                        $arrayParametros['usrCreacion']);
                    }

                    if(isset($strAdministracionNGF))
                    {
                        //Insertar nuevas caracteristicas: usuario y password
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                        $objProducto,
                        'ADMINISTRACION NG FIREWALL',
                        $strAdministracionNGF,
                        $arrayParametros['usrCreacion']);
                    }
                }



                if($strPropiedadEquipo === "T")
                {
                    //Se actualiza el registro en el NAF
                    $strMensajeError = str_repeat(' ', 1000);
                    $strSql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                                . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
                                . ":cantidad, :pv_mensajeerror); END;";
                    $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                    $objStmt->bindParam('codigoEmpresaNaf',        $arrayParametros['idEmpresa']);
                    $objStmt->bindParam('codigoArticulo',          $strCodigoArticulo);
                    $objStmt->bindParam('tipoArticulo',            $strTipoArticulo);
                    $objStmt->bindParam('identificacionCliente',   $strIdentificacionCliente);
                    $objStmt->bindParam('serieCpe',                $strSerieEquipo);
                    $objStmt->bindParam('cantidad',                intval(1));
                    $objStmt->bindParam('pv_mensajeerror',         $strMensajeError);
                    $objStmt->execute();

                    if(strlen(trim($strMensajeError)) > 0)
                    {
                        $strStatus  = 'ERROR';
                        $strMensaje = "ERROR CPE NAF: ".$strMensajeError;
                    }
                }

                //Se registra la ip asociada al servicio
                if(!empty($strIpEquipo))
                {
                    if($objServicio && $objServicio->getProductoId() && is_object($objServicio->getProductoId()) 
                    && $objServicio->getProductoId()->getDescripcionProducto() === 'SECURITY NG FIREWALL' 
                    && $boolPermisoActivarServiciotNGF === '1' && $boolTieneNubePublicaNGF === '1')
                    {
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                        $objProducto,
                        'IP/FQDN NG FIREWALL',
                        $strIpEquipo,
                        $arrayParametros['usrCreacion']);
                    }
                    else
                    {
                        $objInfoIp = new InfoIp();
                        $objInfoIp->setIp($strIpEquipo);
                        $objInfoIp->setEstado("Activo");
                        $objInfoIp->setTipoIp("IPV4");
                        $objInfoIp->setServicioId($idServicio);
                        $objInfoIp->setUsrCreacion($arrayParametros['usrCreacion']);
                        $objInfoIp->setFeCreacion(new \DateTime('now'));
                        $objInfoIp->setIpCreacion($arrayParametros['ipCreacion']);
                        $this->emInfraestructura->persist($objInfoIp);
                        $this->emInfraestructura->flush();
                    }
                }

                //Registrar la mac del elemento
                if(!empty($strMacEquipo) && is_object($objElementoCliente))
                {
                    $objDetalleElementoMac = new InfoDetalleElemento();
                    $objDetalleElementoMac->setElementoId($objElementoCliente->getId());
                    $objDetalleElementoMac->setDetalleNombre("MAC");
                    $objDetalleElementoMac->setDetalleValor($strMacEquipo);
                    $objDetalleElementoMac->setDetalleDescripcion("Mac del equipo del cliente");
                    $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                    $objDetalleElementoMac->setUsrCreacion($arrayParametros['usrCreacion']);
                    $objDetalleElementoMac->setIpCreacion($arrayParametros['ipCreacion']);
                    $objDetalleElementoMac->setEstado('Activo');
                    $this->emInfraestructura->persist($objDetalleElementoMac);
                    $this->emInfraestructura->flush();
                }

                //CREAR SOLCITUD DE RPA LICENCIA
                if(is_object($objElementoCliente))
                {
                    //obtener el id de la marca del elemento
                    $intIdMarcaElemento     = $objElementoCliente->getModeloElementoId()->getMarcaElementoId()->getId();
                    //seteo el arreglo de los id de las marcas
                    $arrayIdMarcasLicencia  = array();
                    //obtengo las marcas de los elementos para licenciamiento
                    $arrayParamDetMarcas    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('RPA_MARCA_ELEMENTOS_LICENCIA',
                                                            'TECNICO',
                                                            '',
                                                            '',
                                                            $objProducto->getId(),
                                                            '',
                                                            '',
                                                            '',
                                                            '',
                                                            $arrayParametros['idEmpresa']);
                    if(is_array($arrayParamDetMarcas) && !empty($arrayParamDetMarcas))
                    {
                        foreach($arrayParamDetMarcas as $arrayDetParametro)
                        {
                            $arrayIdMarcasLicencia[] = $arrayDetParametro['valor2'];
                        }
                    }
                    //verifico si la marca requiere licenciamiento
                    if(in_array($intIdMarcaElemento, $arrayIdMarcasLicencia))
                    {
                        //obtengo el tipo de solicitud de rpa licencia
                        $objTipoSolicitudRpa    = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                        ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RPA LICENCIA",
                                                                                          "estado"               => "Activo"));
                        if(is_object($objTipoSolicitudRpa))
                        {
                            //ingreso la solicitud
                            $objDetalleSolicitudRpa = new InfoDetalleSolicitud();
                            $objDetalleSolicitudRpa->setServicioId($objServicio);
                            $objDetalleSolicitudRpa->setTipoSolicitudId($objTipoSolicitudRpa);
                            $objDetalleSolicitudRpa->setEstado("Pendiente");
                            $objDetalleSolicitudRpa->setObservacion("Se crea la solicitud de RPA licenciamiento.");
                            $objDetalleSolicitudRpa->setUsrCreacion($usrCreacion);
                            $objDetalleSolicitudRpa->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($objDetalleSolicitudRpa);
                            $this->emComercial->flush();
                            //crear historial para la solicitud
                            if(is_object($objDetalleSolicitudRpa))
                            {
                                $objHistorialSolicitudRpa = new InfoDetalleSolHist();
                                $objHistorialSolicitudRpa->setDetalleSolicitudId($objDetalleSolicitudRpa);
                                $objHistorialSolicitudRpa->setEstado("Pendiente");
                                $objHistorialSolicitudRpa->setObservacion("Se crea la solicitud de RPA licenciamiento.");
                                $objHistorialSolicitudRpa->setUsrCreacion($usrCreacion);
                                $objHistorialSolicitudRpa->setFeCreacion(new \DateTime('now'));
                                $objHistorialSolicitudRpa->setIpCreacion($ipCreacion);
                                $this->emComercial->persist($objHistorialSolicitudRpa);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
            }

            //Para productos SAFECITY - SERVICIO MASCARILLA: se consulta la tarea asociada y se la finaliza de forma automatica.
            if(is_object($objServicio) && is_object($objServicio->getProductoId()))
            {
                $arrayParametrosDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                  ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                           'INFRAESTRUCTURA',
                                                           'PARAMETROS',
                                                           'VALIDAR RELACION MASCARILLA CON SAFE VIDEO ANALYTICS CAM',
                                                           $objServicio->getProductoId()->getId(),
                                                           '',
                                                           '',
                                                           '',
                                                           '',
                                                           $arrayParametros['idEmpresa']);

                if(!empty($arrayParametrosDet["valor3"]) && isset($arrayParametrosDet["valor3"]))
                {
                    $strObservacionTarea             = $arrayParametrosDet["valor3"];
                    $strIdMotivo                     = $arrayParametrosDet["valor5"];

                    $objDetalleTareaConfiguracionMascarilla = $this->servicioGeneral
                                                                   ->getServicioProductoCaracteristica($objServicio,
                                                                                                       'ID_DETALLE_CONFIGURACION_MASCARILLA',
                                                                                                       $objServicio->getProductoId());

                    if(is_object($objDetalleTareaConfiguracionMascarilla))
                    {
                        $strIdDetalle = $objDetalleTareaConfiguracionMascarilla->getValor();
                    }

                    if(!empty($strIdDetalle))
                    {
                        $strEsServicioMascarilla                        = "S";
                        $arrayParametrosHist                            = array();
                        $arrayParametrosHist["strCodEmpresa"]           = $arrayParametros['idEmpresa'];
                        $arrayParametrosHist["strUsrCreacion"]          = $usrCreacion;
                        $arrayParametrosHist["strIpCreacion"]           = $ipCreacion;
                        $arrayParametrosHist["strOpcion"]               = "Historial";
                        $arrayParametrosHist["intIdDepartamentoOrigen"] = $arrayParametros['strIdDepartamento'];
                        $arrayParametrosHist["intDetalleId"]            = $strIdDetalle;

                        $arrayParametrosHist["intIdMotivo"]       = $strIdMotivo;
                        $arrayParametrosHist["strMotivoFinTarea"] = $strObservacionTarea;

                        $arrayParametrosHist["strObservacion"]  = $strObservacionTarea;
                        $arrayParametrosHist["strEstadoActual"] = "Finalizada";
                        $arrayParametrosHist["strAccion"]       = "Finalizada";

                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                        $arrayParametrosHist["strObservacion"] = "Tarea fue Finalizada. Obs : " . $strObservacionTarea;
                        $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                    }

                    //Se genera el login aux para el servicio
                    $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());
                }
            }

            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($observacionActivarServicio);
            $objServicioHistorial->setEstado("Activo");
            $objServicioHistorial->setUsrCreacion($usrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($ipCreacion);
            $objServicioHistorial->setAccion($objAccion->getNombreAccion());
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            /* validaciones para almacenamiento de caracteristicas de producto 
               Smart Space */
            if(!empty($strEsSmartSpace))
            {
                if($strEsSmartSpace == "SI")
                {
                    if(!empty($strCircuitoL1))
                    {
                        //servicio prod caract ssid
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,
                                                                                        $objProducto, 
                                                                                        "CIRCUITO_L1", 
                                                                                        $strCircuitoL1, 
                                                                                        $usrCreacion);
                        if(!empty($strCircuitoL2))
                        {
                            //servicio prod caract ssid
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,
                                                                                            $objProducto, 
                                                                                            "CIRCUITO_L2", 
                                                                                            $strCircuitoL2, 
                                                                                            $usrCreacion );
                        }
                    }
                }
            }

            //===============================================================================================================
            //           Se verifica que si el producto pertenece a un Grupo o Solucion y este es Preferente
            //                 busque los otros servicios para poder activar los mismos en simultaneo
            //===============================================================================================================

            $objInfoSolucionDet = $this->emComercial->getRepository('schemaBundle:InfoSolucionDet')
                    ->findOneBy(array('servicioId' => $objServicio->getId(),'estado'=>'Activo'));
            $objInfoSolucionCab = is_object($objInfoSolucionDet) ? $objInfoSolucionDet->getSolucionCabId() : null;

            if (is_object($objInfoSolucionCab))
            {
                $arrayParametrosFinalizarTareas                     = array();
                $arrayParametrosFinalizarTareas['strUsrCreacion']   = $usrCreacion;
                $arrayParametrosFinalizarTareas['strIpCreacion']    = $ipCreacion;
                $arrayParametrosFinalizarTareas['idEmpresa']        = $arrayParametros['idEmpresa'];
                $arrayParametrosFinalizarTareas['empleadoSesion']   = $arrayParametros['empleadoSesion'];
                $arrayParametrosFinalizarTareas['prefijoEmpresa']   = $arrayParametros['prefijoEmpresa'];

                $intIdCanton       = 0;
                $arrayProductos    = array();
                $boolNotificarBoc  = true;
                $strNumeroSolucion = $objInfoSolucionCab->getNumeroSolucion();

                //Validar si el servicio pertenece a un grupo de productos y es Preferente
                if ($objInfoSolucionDet->getEsPreferencial() === 'SI')
                {
                    $arrayServiciosGrupo = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                            ->getArrayServiciosPorGrupoSolucion(array('intSecuencial' => $strNumeroSolucion));

                    foreach ($arrayServiciosGrupo as $objServicioGrupo)
                    {
                        $objInfoSolucionDetSub = $this->emComercial->getRepository('schemaBundle:InfoSolucionDet')
                                ->findOneBy(array('servicioId' => $objServicioGrupo->getId(),'estado'=>'Activo'));

                        $strEsPreferencial = is_object($objInfoSolucionDetSub) ? $objInfoSolucionDetSub->getEsPreferencial() : 'NO';

                        //Si no es preferencial y el servicio es distinto al gestionado
                        if ($strEsPreferencial !== 'SI' && $objServicioGrupo != $objServicio)
                        {
                            $arrayProductos[]  = array('producto'    =>  $objServicioGrupo->getProductoId()->getDescripcionProducto(),
                                                       'descripcion' =>  $objServicioGrupo->getDescripcionPresentaFactura());

                            //Si los servicios estan en estado Pendiente ( Sin Flujo ) o Asignada ( Flujo establecido ) se procede
                            //a realizar la activacion automatica
                            if($objServicioGrupo->getEstado() == 'Pendiente' || $objServicioGrupo->getEstado() == 'Asignada' ||
                               $objServicioGrupo->getEstado() == 'PreAsignacionInfoTecnica' ||
                               $objServicioGrupo->getEstado() == 'Activo' )
                            {
                                //Solo si no estan Activos se cambia el estado caso contrario continua
                                if($objServicioGrupo->getEstado() != 'Activo')
                                {
                                    $objServicioGrupo->setEstado("Activo");
                                    $this->emComercial->persist($objServicioGrupo);

                                    $strObservacion = 'Se confirma Servicio por Activación de Servicio Preferencial del Grupo/Solución : '.
                                                      '<br/><b><i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'.
                                                       $objProducto->getDescripcionProducto().'</b>';

                                    //historial del servicio
                                    $objServicioHistorial = new InfoServicioHistorial();
                                    $objServicioHistorial->setServicioId($objServicioGrupo);
                                    $objServicioHistorial->setObservacion($strObservacion);
                                    $objServicioHistorial->setEstado("Activo");
                                    $objServicioHistorial->setUsrCreacion($usrCreacion);
                                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorial->setIpCreacion($ipCreacion);
                                    $objServicioHistorial->setAccion($objAccion->getNombreAccion());
                                    $this->emComercial->persist($objServicioHistorial);
                                    $this->emComercial->flush();
                                }
                            }//endif verificacion de estado de servicio
                            else
                            {
                                $strStatus  = 'ERROR';
                                $strMensaje = 'No se pude Activar Servicios del Grupo/Solución porque aun faltan Servicio de culminar su Flujo';
                                break;
                            }

                            $arrayParametrosFinalizarTareas['objServicioGrupo'] = $objServicioGrupo;
                            $arrayIdDetalleFinalizada = $this->finalizarTareasAutomaticasSolucion($arrayParametrosFinalizarTareas);
                            $arrayIdDetalle = array_merge($arrayIdDetalle, $arrayIdDetalleFinalizada);
                        }
                    }//end foreach productos de grupo
                }
                else//en caso de que se requeira activar un producto de solucion fuera del esquema normal se verificar que sea un CORE
                    //este activará el resto de servicios ligados al mismo
                {
                    $boolNotificarBoc = false;
                    
                    //Verificar si el producto es CORE o NO
                    $boolEsHousing       = $this->servicioGeneral->isContieneCaracteristica($objProducto,'ES_HOUSING');
                    $boolEsPoolRecursos  = $this->servicioGeneral->isContieneCaracteristica($objProducto,'ES_POOL_RECURSOS');
                    
                    if($boolEsHousing || $boolEsPoolRecursos)
                    {
                        $boolNotificarBoc = true;
                        $strTipoSolucion  = $objInfoSolucionDet->getTipoSolucion();

                        if ($strTipoSolucion)
                        {
                             $arrayServicios = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                     ->getArrayServiciosPorSolucionYTipoSolucion($strNumeroSolucion,$strTipoSolucion);

                            foreach($arrayServicios as $objServicioSubSolucion)
                            {
                                if($objServicioSubSolucion != $objServicio)
                                {
                                    if($objServicioSubSolucion->getEstado() == 'Pendiente' || 
                                       $objServicioSubSolucion->getEstado() == 'Asignada'  ||
                                       $objServicioSubSolucion->getEstado() == 'PreAsignacionInfoTecnica' )
                                    {
                                        $objServicioSubSolucion->setEstado("Activo");
                                        $this->emComercial->persist($objServicioSubSolucion);

                                        $strObservacion = 'Se confirma Servicio por Activación de Servicio Preferencial del Grupo/Solución : '.
                                                          '<br/><b><i class="fa fa-tag" aria-hidden="true"></i>&nbsp;'.
                                                          $objProducto->getDescripcionProducto().'</b>';

                                        //historial del servicio
                                        $objServicioHistorial = new InfoServicioHistorial();
                                        $objServicioHistorial->setServicioId($objServicioSubSolucion);
                                        $objServicioHistorial->setObservacion($strObservacion);
                                        $objServicioHistorial->setEstado("Activo");
                                        $objServicioHistorial->setUsrCreacion($usrCreacion);
                                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                        $objServicioHistorial->setIpCreacion($ipCreacion);
                                        $objServicioHistorial->setAccion($objAccion->getNombreAccion());
                                        $this->emComercial->persist($objServicioHistorial);
                                        $this->emComercial->flush();
                                    }
                                }
                                
                                //Se activaran todos los productos dependientes del CORE
                                $arrayParametrosFinalizarTareas['objServicioGrupo'] = $objServicioSubSolucion;
                                $arrayIdDetalleFinalizada = $this->finalizarTareasAutomaticasSolucion($arrayParametrosFinalizarTareas);
                                $arrayIdDetalle = array_merge($arrayIdDetalle, $arrayIdDetalleFinalizada);
                            }
                        }
                    }
                }
                
                if($boolNotificarBoc)
                {
                    //Se envia notificacion al BOC indicando que login se acabo de Activar
                    $arrayParametrosSolucion                  = array();
                    $arrayParametrosSolucion['objServicio']   = $objServicio;
                    $arrayParametrosSolucion['strCodEmpresa'] = $arrayParametros['idEmpresa'];
                    $strSolucion       = $this->servicioGeneral->getNombreGrupoSolucionServicios($arrayParametrosSolucion);

                    if(!empty($strSolucion))
                    {
                        //Razon social
                        $intIdPersonaRol = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId();

                        $objPersonaRol   = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")->find($intIdPersonaRol);

                        if(is_object($objPersonaRol))
                        {
                            $strRazonSocial = $objPersonaRol->getPersonaId()->getInformacionPersona();
                        }

                        $strEjecutante = '';
                        //Obtener usuario ejecutante
                        $objPersonaEjecutante = $this->emComercial->getRepository("schemaBundle:InfoPersona")->findOneByLogin($usrCreacion);

                        if(is_object($objPersonaEjecutante))
                        {
                            $strEjecutante = $objPersonaEjecutante->getInformacionPersona();
                        }

                        $strLogin = $objServicio->getPuntoId()->getLogin();

                        $arrayNotificacion                        = array();                    
                        $arrayNotificacion['login']               = $strLogin;
                        $arrayNotificacion['razonSocial']         = $strRazonSocial;
                        $arrayNotificacion['solucion']            = $strSolucion;
                        $arrayNotificacion['ejecutante']          = $strEjecutante;
                        $arrayNotificacion['fecha']               = new \DateTime('now');
                        $arrayNotificacion['arrayInformacion']    = $arrayProductos;

                        $this->envioPlantillaService->generarEnvioPlantilla("Activación de Solución DATA CENTER para :".$strLogin, 
                                                                            array(), 
                                                                            'ACT-SOLUCION-DC', 
                                                                            $arrayNotificacion, 
                                                                            $arrayParametros['idEmpresa'], 
                                                                            $intIdCanton, 
                                                                            ''
                                                                           );
                    }
                }
                
            }//if obtener secuencial de grupo
                                    
            if($strStatus == 'OK')
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                     $this->emComercial->commit();
                }

                if($this->emSoporte->getConnection()->isTransactionActive())
                {
                     $this->emSoporte->commit();
                }

                if($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                     $this->emInfraestructura->commit();
                }

                if (!empty($arrayIdDetalle) && count($arrayIdDetalle) > 0)
                {
                    foreach ($arrayIdDetalle as $intIdDetalle)
                    {
                        $arrayParametrosInfoTarea['intDetalleId'] = $intIdDetalle;
                        $arrayParametrosInfoTarea['strUsrUltMod'] = $usrCreacion;
                        $this->serviceSoporte->actualizarInfoTarea($arrayParametrosInfoTarea);
                    }
                }
            }
        }
        catch(\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                 $this->emSoporte->rollback();
            }

            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                 $this->emInfraestructura->rollback();
            }

            $strStatus  = "ERROR";
            $strMensaje = "ERROR AL MOMENTO DE CONFIRMAR EL SERVICIO!";

            //Se valida que el modelo de equipo este ingresado primero en el Telcos
            if(strpos($ex->getMessage(),"Antes de confirmar el servicio es obligatorio que el modelo") !== false)
            {
                $strMensaje = $ex->getMessage();
            }

            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioPorNuevoTn',
                                            $ex->getMessage(),
                                            $usrCreacion,
                                            $ipCreacion
                                           );
            
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->close();
            }

            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->close();
            }

            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->close();
            }
        }
                
        if($strEsServicioMascarilla == "S")
        {
            //Se crea registro en la INFO_TAREA
            $arrayParametrosInfoTarea['intDetalleId']   = $strIdDetalle;
            $arrayParametrosInfoTarea['strUsrCreacion'] = $usrCreacion;
            $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);        
        }        
        
        $arrayRespuesta['status']  = $strStatus;
        $arrayRespuesta['mensaje'] = $strMensaje;
        
        return $arrayRespuesta;
    }
    
    /**
     * 
     * Metodo encargado de finalizar las tareas automaticas generadas en un flujo de Solucion 
     * cuando se activa un producto Preferencial o CORE ( post activacion )
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.0 23-03-2018
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.1 10-06-2019 - Se agrega el llamado al proceso que notifica a SysCloud la finalización de la tarea.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.2 25-02-2021 - Se returna el id detalle de las tareas a finalizar.
     *
     * @param Array $arrayParametros [ objServicioGrupo , strUsrCreacion , strIpCreacion ]
     */
    private function finalizarTareasAutomaticasSolucion($arrayParametros)
    {
        $objServicioGrupo = $arrayParametros['objServicioGrupo'];
        $strUsrCreacion   = $arrayParametros['strUsrCreacion'];
        $strIpCreacion    = $arrayParametros['strIpCreacion'];
        $arrayIdDetalle   = array();

        $arrayDetalleSolicitud = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                ->findBy(array('estado'     => array('Asignada','Finalizada','Factible'),
                               'servicioId' => $objServicioGrupo->getId()));

        foreach ($arrayDetalleSolicitud as $objDetalleSolicitud)
        {
            $strObservacion = "Se finaliza Tarea de manera Automática";
            $arrayDetalle   = $this->emSoporte->getRepository("schemaBundle:InfoDetalle")
                    ->findByDetalleSolicitudId($objDetalleSolicitud->getId());

            //Se buscan todas las tareas ligadas adicionales a esta solicitud de servicio
            foreach ($arrayDetalle as $objDetalle)
            {
                $intIdComunicacion = $this->emComunicacion->getRepository("schemaBundle:InfoComunicacion")
                        ->getMinimaComunicacionPorDetalleId($objDetalle->getId());
                $arrayTareaEstado  = $this->serviceSoporte->obtenerEstadoTarea($intIdComunicacion);

                if ($arrayTareaEstado['estado'] == 'Abierta')
                {
                    $arrayIdDetalle[] = $objDetalle->getId();

                    $arrayParametrosTiempos = array('fechaInicio' => $objDetalle->getFeCreacion()->format('d-m-Y'),
                                                    'horaInicio'  => $objDetalle->getFeCreacion()->format('h:m'));

                    $arrayHoraServer = $this->serviceSoporte->obtenerHoraTiempoTranscurrido($arrayParametrosTiempos);

                    $intTiempoTotal    = $arrayHoraServer['tiempoTotal'];
                    $strFechaCierre    = $arrayHoraServer['fechaFin'];
                    $strHoraCierre     = $arrayHoraServer['horaFin'];
                    $strFechaEjecucion = $arrayHoraServer['fechaInicio'];
                    $strHoraEjecucion  = $arrayHoraServer['horaInicio'];

                    //Finalizar Tareas automaticas
                    $objDate       = date_create(date('Y-m-d H:i', strtotime($strFechaEjecucion.' '.$strHoraEjecucion)));
                    $objDateCierre = date_create(date('Y-m-d H:i', strtotime($strFechaCierre.' '.$strHoraCierre)));

                    $objInfoTareaTiempoAsignacion = new InfoTareaTiempoAsignacion();
                    $objInfoTareaTiempoAsignacion->setDetalleId($objDetalle->getId());
                    $objInfoTareaTiempoAsignacion->setTiempoCliente(0);
                    $objInfoTareaTiempoAsignacion->setTiempoEmpresa($intTiempoTotal);
                    $objInfoTareaTiempoAsignacion->setObservacion($strObservacion);
                    $objInfoTareaTiempoAsignacion->setFeCreacion(new \DateTime('now'));
                    $objInfoTareaTiempoAsignacion->setUsrCreacion($strUsrCreacion);
                    $objInfoTareaTiempoAsignacion->setFeEjecucion($objDate);
                    $objInfoTareaTiempoAsignacion->setFeFinalizacion($objDateCierre);
                    $this->emSoporte->persist($objInfoTareaTiempoAsignacion);
                    $this->emSoporte->flush();

                    $objInfoDetalleHistorial = new InfoDetalleHistorial();
                    $objInfoDetalleHistorial->setDetalleId($objDetalle);
                    $objInfoDetalleHistorial->setObservacion($strObservacion);
                    $objInfoDetalleHistorial->setEstado("Finalizada");
                    $objInfoDetalleHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleHistorial->setIpCreacion($strIpCreacion);
                    $this->emSoporte->persist($objInfoDetalleHistorial);
                    $this->emSoporte->flush();

                    $infoTareaSeguimiento = new InfoTareaSeguimiento();
                    $infoTareaSeguimiento->setDetalleId($objDetalle->getId());
                    $infoTareaSeguimiento->setObservacion("Tarea fue Finalizada. Obs : " . $strObservacion);
                    $infoTareaSeguimiento->setUsrCreacion($strUsrCreacion);
                    $infoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                    $infoTareaSeguimiento->setEmpresaCod($arrayParametros['idEmpresa']);
                    $infoTareaSeguimiento->setEstadoTarea("Finalizada");
                    $this->emSoporte->persist($infoTareaSeguimiento);
                    $this->emSoporte->flush();

                    $intNumeroTarea = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                            ->getMinimaComunicacionPorDetalleId($objDetalle->getId());

                    $objTarea        = $objDetalle->getTareaId();
                    $intIdCanton     = 0;
                    $arrayCorreos    = array();
                    $objUsuario      = null;

                    //Asignado
                    $arrayInfoDetalleAsignacion = $this->emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                            ->findByDetalleId($objDetalle->getId());

                    if (!empty($arrayInfoDetalleAsignacion))
                    {
                        $objInfoDetalleAsignacion = $arrayInfoDetalleAsignacion[count($arrayInfoDetalleAsignacion) - 1];

                        //Si existe departamento asignado
                        if ($objInfoDetalleAsignacion->getAsignadoId())
                        {
                            //Usuario de creacion del evento
                            $objUsuario = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                    ->findOneByLogin($objInfoDetalleAsignacion->getUsrCreacion());

                            $objInfoPersonaUsuarioFc = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                    ->findOneBy(array('personaId'       => $objUsuario->getId(),
                                                      'formaContactoId' => 5,
                                                      'estado'          => "Activo"));

                            //Asignado de la Tarea
                            $objInfoPersonaAsignadoFc =$this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                    ->findOneBy(array('personaId'       => $objInfoDetalleAsignacion->getRefAsignadoId(),
                                                      'formaContactoId' => 5,
                                                      'estado'          => "Activo"));

                            if (is_object($objInfoPersonaUsuarioFc))
                            {
                                $arrayCorreos[] = $objInfoPersonaUsuarioFc->getValor();
                            }

                            if (is_object($objInfoPersonaAsignadoFc))
                            {
                                $arrayCorreos[] = $objInfoPersonaAsignadoFc->getValor();
                            }
                        }

                        //Obtener canton
                        $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                         ->find($objInfoDetalleAsignacion->getPersonaEmpresaRolId());

                        if(is_object($objInfoPersonaEmpresaRol))
                        {
                            $objInfoOficina = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                                   ->find($objInfoPersonaEmpresaRol->getOficinaId()->getId());
                            $intIdCanton    = is_object($objInfoOficina)?$objInfoOficina->getCantonId():0;
                        }

                        //Notificar
                        $arrayParametrosNotificacion = array(
                                            'idCaso'            => '',
                                            'idDetalle'         => $objDetalle->getId(),
                                            'perteneceACaso'    => 'NO',
                                            'numeracion'        => $intNumeroTarea,
                                            'referencia'        => ' a la Actividad #' . $intNumeroTarea,
                                            'asignacion'        => $objInfoDetalleAsignacion,
                                            'persona'           => $objUsuario,
                                            'nombreTarea'       => $objTarea->getNombreTarea(),
                                            'estado'            => 'Finalizada',
                                            'empleadoLogeado'   => $arrayParametros['empleadoSesion'],
                                            'empresa'           => $arrayParametros['prefijoEmpresa'],
                                            'clientes'          => '',
                                            'observacion'       => $strObservacion,
                                            'bandCoordenadas'   => "N",
                                            'obsCoordenadas'    => ""
                                           );

                        $this->envioPlantillaService->generarEnvioPlantilla("Finalizacion de Tarea", 
                                                                            $arrayCorreos, 
                                                                            'TAREAFINALIZA', 
                                                                            $arrayParametrosNotificacion, 
                                                                            $arrayParametros['idEmpresa'], 
                                                                            $intIdCanton, 
                                                                            $objInfoDetalleAsignacion->getAsignadoId()
                                                                           );
                    }

                    //Proceso para indicar la finalización de la tarea en Sys Cloud-Center.
                    $this->serviceProceso->notificarCambioEstadoSysCloud(array('intIdComunicacion' => $intIdComunicacion,
                                                                               'strObservacion'    => $strObservacion,
                                                                               'strCodEmpresa'     => $arrayParametros['idEmpresa'],
                                                                               'strFechaFinaliza'  => $strFechaCierre,
                                                                               'strHoraFinaliza'   => $strHoraCierre,
                                                                               'strUser'           => $strUsrCreacion,
                                                                               'strIp'             => $strIpCreacion));
                }
            }

            if ($objDetalleSolicitud->getEstado() == 'Asignada')
            {
                $objDetalleSolicitud->setEstado('Finalizada');
                $this->emComercial->persist($objDetalleSolicitud);
                $this->emComercial->flush();
            }
        }

        return $arrayIdDetalle;
    }
    
    /**
     * confirmarServicioMd
     * 
     * Funcion que sirve realizar la confirmación de servicios en Md
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 24-01-2017   Se agregan parametros para activación de servicios SmartWifi
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 20-10-2020 - Se agrega programación para consultar si un producto sin flujo esta hablitado para que se realice la activacion y
     *                            registro de elementos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 19-11-2020 - Se agrega atributos capacidad y marca para el registro de la tarjeta de memoria en las camaras de netlifecam
     *
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objModeloElemento             Objeto del modelo elemento del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - objInterfaceElemento          Objeto de la interface elemento del servicio
     *                                 - strIdEmpresa                  Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - strSerieSmartWifi             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloSmartWifi            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strMacSmartWifi               Cadena de caracteres que indica la mac del equipo SmartWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                               ]
     * @return Array $respuestaFinal [
     *                                 - status   Estado de la transaccion ejecutada
     *                                 - mensaje  Mensaje de la transaccion ejecutada
     *                               ]
     */
    public function confirmarServicioMd( $arrayParametros )
    {
        $objServicio           = $arrayParametros['ojbServicio'];
        $objServicioTecnico    = $arrayParametros['objServicioTecnico'];
        $objModeloElemento     = $arrayParametros['objModeloElemento'];
        $objProducto           = $arrayParametros['objProducto'];
        $objInterfaceElemento  = $arrayParametros['objInterfaceElemento'];
        $strUsrCreacion        = $arrayParametros['strUsrCreacion'];
        $strIpCreacion         = $arrayParametros['strIpCreacion'];
        $strIdEmpresa          = $arrayParametros['strIdEmpresa'];
        $objAccion             = $arrayParametros['objAccion'];
        $strSerieSmartWifi     = $arrayParametros['strSerieSmartWifi'];
        $strModeloSmartWifi    = $arrayParametros['strModeloSmartWifi'];
        $strMacSmartWifi       = $arrayParametros['strMacSmartWifi'];
        $strPoductoPermitido   = $arrayParametros['productoPermitidoReg'];
        $strTipoElemento       = $arrayParametros['strTipoElemento'];
        $intIdServicioInternet = $arrayParametros['intIdServicioInternet'];
        $strCapacidadTarjeta   = $arrayParametros['capacidadTarjeta'];
        $strMarcaTarjeta       = $arrayParametros['marcaTarjeta'];
        $strModeloTarjeta      = $arrayParametros['modeloTarjeta'];
        $strSerieTarjeta       = $arrayParametros['serieTarjeta'];

        //*DECLARACION DE VARIABLES----------------------------------------------*/
        $status  = "NA";
        $mensaje = "NA";
        //*----------------------------------------------------------------------*/
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        //*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {
            if($objServicio->getTipoOrden()=='R' && $strPoductoPermitido != "S")
            {
                $status = $this->confirmarServicioPorReubicacion( $objServicio,
                                                                  $objServicioTecnico, 
                                                                  $objProducto, 
                                                                  $strUsrCreacion, 
                                                                  $strIpCreacion, 
                                                                  $objAccion );
            }
            else if($objServicio->getTipoOrden()=='T' && $strPoductoPermitido != "S")
            {
                $status = $this->confirmarServicioPorTrasladoMd( $objServicio, 
                                                                 $objServicioTecnico, 
                                                                 $objInterfaceElemento, 
                                                                 $objModeloElemento, 
                                                                 $objProducto, 
                                                                 $strUsrCreacion, 
                                                                 $strIpCreacion, 
                                                                 $strIdEmpresa, 
                                                                 $objAccion );
            }
            else if($objServicio->getTipoOrden()=='N' || $strPoductoPermitido == "S")
            {
                $arrayParametrosConfirmarNuevoMd = array (
                                                            'ojbServicio'           => $objServicio,
                                                            'objServicioTecnico'    => $objServicioTecnico,
                                                            'objModeloElemento'     => $objModeloElemento,
                                                            'objProducto'           => $objProducto,
                                                            'objInterfaceElemento'  => $objInterfaceElemento,
                                                            'strUsrCreacion'        => $strUsrCreacion,
                                                            'strIpCreacion'         => $strIpCreacion,
                                                            'objAccion'             => $objAccion,
                                                            'strSerieSmartWifi'     => $strSerieSmartWifi,
                                                            'strModeloSmartWifi'    => $strModeloSmartWifi,
                                                            'strMacSmartWifi'       => $strMacSmartWifi,
                                                            'strPoductoPermitido'   => $strPoductoPermitido,
                                                            'strTipoElemento'       => $strTipoElemento,
                                                            'intIdServicioInternet' => $intIdServicioInternet,
                                                            'strOrigen'             => $arrayParametros['strOrigen'],
                                                            'strEmpresaCod'         => $strIdEmpresa,
                                                            'strCapacidadTarjeta'   => $strCapacidadTarjeta,
                                                            'strMarcaTarjeta'       => $strMarcaTarjeta,
                                                            'strModeloTarjeta'      => $strModeloTarjeta,
                                                            'strSerieTarjeta'       => $strSerieTarjeta
                                                         );
                $status = $this->confirmarServicioPorNuevoMd( $arrayParametrosConfirmarNuevoMd );
            }
            
            if($status!="OK")
            {
                throw new \Exception('ERROR');
            }
            else
            {
                $mensaje="OK";
            }
            
        }
        catch (\Exception $e) 
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $status           = "ERROR";
            $mensaje          = $e->getMessage();
            $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        //*----------------------------------------------------------------------*/
        
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }
        
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * confirmarServicioPorNuevoMd
     * 
     * Funcion que sirve para confirmar el servicio de tipo de orden Nueva
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 25-03-2015
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 24-01-2017   Se agregan parametros y validaciones para activación de servicios SmartWifi
     * @since 1.1
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.3 14-02-2017   Se modifica validación de producto SmartWifi para registro de equipo, se utiliza nombre tecnico del producto
     * @since 1.2
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 24-07-2018   Se agrega validación para gestionar servicios con nueva tecnología ZTE
     * @since 1.3
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6 20-10-2020 - Se agrega programación para consultar si un producto sin flujo esta hablitado para que se realice la activacion y
     *                           registro de elementos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7 19-11-2020 - Se registra la tarjeta micro sd en las camaras de netlifecam
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.8 17-05-2021 - Se anexa validacion para que al activar un servicio de internet, se activen tambien los servicios
     *                          adicionales validando que primero se activen en konibit
     *
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.9 19-10-2022 - Se agrega parametrizacion de  los productos NetlifeCam.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 2.0 12-12-2022 - Se realiza el cambio de mensaje al momento de renovar una NetlifeCam.
     *  
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objModeloElemento             Objeto del modelo elemento del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - objInterfaceElemento          Objeto de la interface elemento del servicio
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - strSerieSmartWifi             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloSmartWifi            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strMacSmartWifi               Cadena de caracteres que indica la mac del equipo SmartWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                               ]
     * @return Array $respuestaFinal [
     *                                 - status   Estado de la transaccion ejecutada
     *                                 - mensaje  Mensaje de la transaccion ejecutada
     *                               ]
     */
    public function confirmarServicioPorNuevoMd( $arrayParametros )
    {
        try
        {
            $objServicio           = $arrayParametros['ojbServicio'];
            $objServicioTecnico    = $arrayParametros['objServicioTecnico'];
            $objModeloElemento     = $arrayParametros['objModeloElemento'];
            $objProducto           = $arrayParametros['objProducto'];
            $objInterfaceElemento  = $arrayParametros['objInterfaceElemento'];
            $strUsrCreacion        = $arrayParametros['strUsrCreacion'];
            $strIpCreacion         = $arrayParametros['strIpCreacion'];
            $objAccion             = $arrayParametros['objAccion'];
            $strSerieSmartWifi     = $arrayParametros['strSerieSmartWifi'];
            $strModeloSmartWifi    = $arrayParametros['strModeloSmartWifi'];
            $strMacSmartWifi       = $arrayParametros['strMacSmartWifi'];
            $strPoductoPermitido   = $arrayParametros['strPoductoPermitido']?$arrayParametros['strPoductoPermitido']:"N";
            $strTipoElemento       = $arrayParametros['strTipoElemento']?$arrayParametros['strTipoElemento']:"N";
            $intIdServicioInternet = $arrayParametros['intIdServicioInternet'];
            $strEmpresaCod         = $arrayParametros['strEmpresaCod'];
            $strCapacidadTarjeta   = $arrayParametros['strCapacidadTarjeta']?$arrayParametros['strCapacidadTarjeta']:"";
            $strMarcaTarjeta       = $arrayParametros['strMarcaTarjeta']?$arrayParametros['strMarcaTarjeta']:"";
            $strModeloTarjeta      = $arrayParametros['strModeloTarjeta']?$arrayParametros['strModeloTarjeta']:"";
            $strSerieTarjeta       = $arrayParametros['strSerieTarjeta']?$arrayParametros['strSerieTarjeta']:"";
            $status                = "ERROR";
            $strObservacion        = "Se confirmo el servicio";
            $arrayParamProducNetCam   = $this->servicioGeneral->paramProductosNetlifeCam();
            //indice cliente olt
            $caracIndex     = $this->emComercial
                                   ->getRepository('schemaBundle:AdmiCaracteristica')
                                   ->findOneBy(array( "descripcionCaracteristica" => "INDICE CLIENTE", "estado" => "Activo"));
            if (is_object($caracIndex))
            {
                $prodCaracIndex = $this->emComercial
                                       ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                       ->findOneBy(array("productoId" => $objProducto->getId(),"caracteristicaId"=>$caracIndex->getId(),
                                                                "estado" => "Activo"));
                if (is_object($prodCaracIndex))
                {
                    $servProdCaractIndex = $this->emComercial
                                                ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findOneBy(array("productoCaracterisiticaId" => $prodCaracIndex->getId(),
                                                                  "servicioId"                => $objServicio->getId(), "estado" => "Activo"));
                }
            }

            if($strPoductoPermitido == "S")
            {
                if (is_object($objServicioTecnico))
                {
                    $objTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                            ->find($objServicioTecnico->getUltimaMillaId());

                    if (is_object($objTipoMedio))
                    {
                        $strUltimaMilla = $objTipoMedio->getNombreTipoMedio();
                    }
                }

                $arrayParametrosSmartWifi = array (
                                                    'strSerieSmartWifi'     => $strSerieSmartWifi,
                                                    'strModeloSmartWifi'    => $strModeloSmartWifi,
                                                    'strMacSmartWifi'       => $strMacSmartWifi,
                                                    'intIdServicioInternet' => $intIdServicioInternet,
                                                    'strUsrCreacion'        => $strUsrCreacion,
                                                    'strIpCreacion'         => $strIpCreacion,
                                                    'strEmpresaCod'         => $strEmpresaCod,
                                                    'objServicio'           => $objServicio,
                                                    'strUltimaMilla'        => $strUltimaMilla,
                                                    'strPoductoPermitido'   => $strPoductoPermitido,
                                                    'strCapacidadTarjeta'   => $strCapacidadTarjeta,
                                                    'strMarcaTarjeta'       => $strMarcaTarjeta,
                                                    'strModeloTarjeta'      => $strModeloTarjeta,
                                                    'strSerieTarjeta'       => $strSerieTarjeta,
                                                    'strTipoElemento'       => $strTipoElemento
                                                  );

                $status = $this->ingresarElementoSmartWifi($arrayParametrosSmartWifi);
            }
            else
            {
                if (is_object($objModeloElemento))
                {
                    $strMarcaOlt    = $objModeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
                    if($strMarcaOlt == "TELLION")
                    {
                        $scriptArray    = $this->servicioGeneral->obtenerArregloScript("verificarActivacion",$objModeloElemento);
                        $idDocumento    = $scriptArray[0]->idDocumento;
                        $usuario        = $scriptArray[0]->usuario;
                        $protocolo      = $scriptArray[0]->protocolo;

                        if($idDocumento==0)
                        {
                            return "NO EXISTE TAREA";
                        }

                        $datos          = $objInterfaceElemento->getNombreInterfaceElemento().",".$servProdCaractIndex->getValor();
                        $resultadoJson  = $this->verificarActivacion($idDocumento, $usuario, $protocolo, $objServicioTecnico, $datos);
                        $status         = $resultadoJson->status;
                    }
                    else if($strMarcaOlt == "HUAWEI" || $strMarcaOlt == "ZTE")
                    {
                        $status = "OK";
                    }
                }
                else
                {
                    //se agregan validaciones para realizar activación de servicios SmartWifi
                    if (is_object($objProducto))
                    {
                        if ($objProducto->getNombreTecnico() == 'SMARTWIFI')
                        {
                            if (is_object($objServicioTecnico))
                            {
                                $objTipoMedio       = $this->emInfraestructura
                                                           ->getRepository('schemaBundle:AdmiTipoMedio')
                                                           ->find($objServicioTecnico->getUltimaMillaId());
                                if (is_object($objTipoMedio))
                                {
                                    $strUltimaMilla = $objTipoMedio->getNombreTipoMedio();
                                }
                            }
                            $arrayParametrosSmartWifi = array (
                                                                'strSerieSmartWifi'     => $strSerieSmartWifi,
                                                                'strModeloSmartWifi'    => $strModeloSmartWifi,
                                                                'strMacSmartWifi'       => $strMacSmartWifi,
                                                                'intIdServicioInternet' => $intIdServicioInternet,
                                                                'strUsrCreacion'        => $strUsrCreacion,
                                                                'strIpCreacion'         => $strIpCreacion,
                                                                'strEmpresaCod'         => $strEmpresaCod,
                                                                'objServicio'           => $objServicio,
                                                                'strUltimaMilla'        => $strUltimaMilla
                                                              );
                            $status = $this->ingresarElementoSmartWifi( $arrayParametrosSmartWifi );
                        }
                    }
                }
            }

            if($status=="OK")
            {
                $objServicio->setEstado("Activo");
                $this->emComercial->persist($objServicio);

                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacion);
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setAccion($objAccion->getNombreAccion());
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                if (in_array($objProducto->getNombreTecnico(), $arrayParamProducNetCam))
                {
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica(
                                    $objServicio,
                                    $objProducto,
                                    'MAC',
                                    $strMacSmartWifi,
                                    $strUsrCreacion);
                    
                    $objServAntProdCar   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                    "ID_SERV_ANTERIOR_RENOVACION",  
                                                                                    $objProducto);
                    if(is_object($objServAntProdCar))
                    {
                        $strServAnt = $objServAntProdCar->getValor();
                        $objServicioAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strServAnt);
                
                        //actualizar servicio anterior a estado a Renovado  
                        $objServicioAnterior->setEstado("Renovado");
                        $this->emComercial->persist($objServicioAnterior);
                        $this->emComercial->flush();

                        //log de servicio historial
                        $arrayProductoParam = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PROYECTO NETLIFECAM', 
                                                        'INFRAESTRUCTURA', '',
                                                    'PARAMETROS NETLIFECAM OUTDOOR','RENOVACION',
                                                    'OBSERVACION RENOVACION','','','',$strEmpresaCod);
                        if (is_array($arrayProductoParam) && !empty($arrayProductoParam))
                        {
                            $objProdParametro = $arrayProductoParam[0];
                            $strObservacionRenova = $objProdParametro['valor3'];
                        }

                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicioAnterior);
                        $objServicioHistorial->setObservacion($strObservacionRenova);
                        $objServicioHistorial->setEstado("Renovado");
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $objServicioHistorial->setAccion("");
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();

                        //actualizar estado a "eliminado" a las caracteristica que asocian  
                        //el servicio anterior al servicio nuevo por renovación de camara.
                        $this->serviceLicKaspersky
                                        ->actualizarServicioProductoCaracteristica(
                                                        array("objServicio" => $objServicio,
                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                        "objProducto"       => $objProducto,
                                                        "strCaracteristica" => "ID_SERV_ANTERIOR_RENOVACION",
                                                        "strEstadoNuevo"     => "Eliminado"));
                        
                        $this->serviceLicKaspersky
                        ->actualizarServicioProductoCaracteristica(
                                                        array("objServicio" => $objServicioAnterior,
                                                        "strUsrCreacion"    => $strUsrCreacion,
                                                        "objProducto"       => $objProducto,
                                                        "strCaracteristica" => "ID_SERV_NUEVA_RENOVACION",
                                                        "strEstadoNuevo"     => "Eliminado"));    
                    }
                }

                // Realiza la activacion de servicios automaticos solo para servicios de internet
                $objPlanServicio = $objServicio->getPlanId();
                $arrayProductoParam = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('VALIDA_PROD_ADICIONAL', 
                                                    'COMERCIAL', '',
                                                    'Verifica Producto Internet',
                                                    '','','','','',$strEmpresaCod);
                if (is_array($arrayProductoParam) && !empty($arrayProductoParam))
                {
                    $objProdParametro = $arrayProductoParam[0];
                }
                if (!empty($objPlanServicio) && 
                    $objProducto->getDescripcionProducto() == $objProdParametro['valor3'])
                {
                    // Activamos los servicios adicionales
                    $arrayDatosParametros = array(
                        "intIdPunto"      => $objServicio->getPuntoId()->getId(),
                        "intCodEmpresa"   => $strEmpresaCod,
                        "strIpCreacion"   => $strIpCreacion,
                        "strUserCreacion" => $strUsrCreacion,
                        "strAccion"       => $objAccion->getNombreAccion()
                    );
                    $this->activarProductosAdicionales($arrayDatosParametros);
                    // Activamos los servicios incluidos
                    $arrayDatosParametros = array(
                        "objServicio"     => $objServicio,
                        "intCodEmpresa"   => $strEmpresaCod,
                        "strIpCreacion"   => $strIpCreacion,
                        "strUserCreacion" => $strUsrCreacion
                    );
                    $this->activarProdKonitIncluidos($arrayDatosParametros);
                }

                if($strPoductoPermitido == "S")
                {
                    //Finalizar solicitud de planificacion
                    $objTipoSolicitudPlanficacion = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                      ->findOneBy(array("descripcionSolicitud" => "SOLICITUD PLANIFICACION",
                                                                                        "estado"               => "Activo"));
                    
                    if(is_object($objTipoSolicitudPlanficacion))
                    {
                        $objSolicitudPlanficacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                      ->findOneBy(array("servicioId"      => $objServicio->getId(),
                                                                                        "tipoSolicitudId" => $objTipoSolicitudPlanficacion->getId(),
                                                                                        "estado"          => "Asignada"));

                        if(is_object($objSolicitudPlanficacion))
                        {
                            $objSolicitudPlanficacion->setEstado("Finalizada");
                            $this->emComercial->persist($objSolicitudPlanficacion);
                            $this->emComercial->flush();

                            //crear historial para la solicitud
                            $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                            $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudPlanficacion);
                            $objHistorialSolicitudPlani->setEstado("Finalizada");
                            $objHistorialSolicitudPlani->setObservacion("Cliente instalado");
                            $objHistorialSolicitudPlani->setUsrCreacion($strUsrCreacion);
                            $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                            $objHistorialSolicitudPlani->setIpCreacion($strIpCreacion);
                            $this->emComercial->persist($objHistorialSolicitudPlani);
                            $this->emComercial->flush();
                        }
                    }
                }

                $status = "OK";
            }
            else
            {
                $status = "ERROR";
            }
        }
        catch (\Exception $e)
        {
            $status = "ERROR";
        }
        
        return $status;
    }
    
    /**
     * confirmarServicioNetHome
     * 
     * Función que sirve para confirmar el servicio de tipo de orden Nueva
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 21-09-2018
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - jsonDatosElementos            Identificador del Servicio de Internet Activo del punto
     *                                 - intIdSolicitudServicio        Identificador de solicitud de planificación del servicio
     *                               ]
     * @return Array $respuestaFinal [
     *                                 - status   Estado de la transaccion ejecutada
     *                                 - mensaje  Mensaje de la transaccion ejecutada
     *                               ]
     */
    public function confirmarServicioNetHome( $arrayParametros )
    {
        $objServicio                    = !empty($arrayParametros['ojbServicio'])?$arrayParametros['ojbServicio']:null;
        $objServicioTecnico             = !empty($arrayParametros['objServicioTecnico'])?$arrayParametros['objServicioTecnico']:null;
        $strUsrCreacion                 = !empty($arrayParametros['strUsrCreacion'])?$arrayParametros['strUsrCreacion']:"";
        $strIpCreacion                  = !empty($arrayParametros['strIpCreacion'])?$arrayParametros['strIpCreacion']:"";
        $objAccion                      = !empty($arrayParametros['objAccion'])?$arrayParametros['objAccion']:null;
        $strJsonDatosElementos          = !empty($arrayParametros['strJsonDatosElementos'])?$arrayParametros['strJsonDatosElementos']:null;
        $intIdSolicitudServicio         = !empty($arrayParametros['intIdSolicitudServicio'])?$arrayParametros['intIdSolicitudServicio']:0;
        $strEmpresaCod                  = !empty($arrayParametros['strEmpresaCod'])?$arrayParametros['strEmpresaCod']:"";
        $strStatus                      = "ERROR";
        $strUltimaMilla                 = "";
        $this->emComercial->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try            
        {
            if (!is_object($objServicio))
            {
                throw new \Exception("No se encontro información acerca del servicio que se está procesando");
            }
            
            if (!is_object($objAccion))
            {
                throw new \Exception("No se encontro información acerca de la acción ejecutada");
            }
            
            //se agregan validaciones para realizar activación de servicios SmartWifi
            if (is_object($objServicioTecnico))
            {
                $objTipoMedio       = $this->emInfraestructura
                                           ->getRepository('schemaBundle:AdmiTipoMedio')
                                           ->find($objServicioTecnico->getUltimaMillaId());
                if (is_object($objTipoMedio))
                {
                    $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
                }
            }

            $arrayElementosNetHome  = json_decode($strJsonDatosElementos,true);
            $strEsPrimeElemento     = "SI";
            $intContadorElemento    = 0;
            foreach($arrayElementosNetHome as $arrayElementoNetHome)
            {
                $intContadorElemento = $intContadorElemento + 1;
                $objProductoNh = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiProducto')
                                      ->find($arrayElementoNetHome['idProducto']);
                if (!is_object($objProductoNh))
                {
                    throw new \Exception("No se encontro información acerca del producto NetHome");
                }
                $arrayParametrosNetHome = array (
                                                'strSerieNetHome'     => $arrayElementoNetHome['serieElemento'],
                                                'strModeloNetHome'    => $arrayElementoNetHome['modeloElemento'],
                                                'strUsrCreacion'      => $strUsrCreacion,
                                                'strIpCreacion'       => $strIpCreacion,
                                                'strEmpresaCod'       => $strEmpresaCod,
                                                'objServicio'         => $objServicio,
                                                'strUltimaMilla'      => $strUltimaMilla,
                                                'strTipoNetHome'      => $objProductoNh->getCodigoProducto(),
                                                'strEsPrimeElemento'  => $strEsPrimeElemento,
                                                'strSecuencial'       => $intContadorElemento
                                                );
                $strStatus = $this->ingresarElementoNetHome( $arrayParametrosNetHome );
                if ($strStatus != "OK")
                {
                    throw new \Exception("Existieron problemas al registrar los equipos");
                }
                $strEsPrimeElemento = "NO";
            }

            if($strStatus == "OK")
            {
                $strEstadoServicio       = "";
                $strNombreAccion         = "";
                $strObservacionHistorial = "";
                //se agregan validaciones para generar los historiales de servicio según sea el escenario
                if ($strEsCambioPlan == 'SI')
                {
                    $strEstadoServicio       = $objServicio->getEstado();
                    $strNombreAccion         = "";
                    $strObservacionHistorial = "Se agrego el equipo NetHome";
                }
                else
                {
                    if (is_object($objServicio->getPlanId()))
                    {
                        $strEstadoServicio       = $objServicio->getEstado();
                        $strNombreAccion         = "";
                        $strObservacionHistorial = "Se agrego el equipo NetHome";
                    }
                    else
                    {
                        $strEstadoServicio       = "Activo";
                        $strNombreAccion         = $objAccion->getNombreAccion();
                        $strObservacionHistorial = "Se confirmo el servicio";
                    }
                    
                }
                
                $objServicio->setEstado($strEstadoServicio);
                $this->emComercial->persist($objServicio);

                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionHistorial);
                $objServicioHistorial->setEstado($strEstadoServicio);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setAccion($strNombreAccion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                
                //finalizar solicitud 
                $objSolicitudServicio = $this->emComercial
                                             ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                             ->find($intIdSolicitudServicio);

                if (is_object($objSolicitudServicio))
                {
                    $objSolicitudServicio->setEstado("Finalizada");
                    $this->emComercial->persist($objSolicitudServicio);
                    $this->emComercial->flush();

                    //crear historial para la solicitud
                    $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                    $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudServicio);
                    $objHistorialSolicitudPlani->setEstado("Finalizada");
                    $objHistorialSolicitudPlani->setObservacion("Cliente instalado");
                    $objHistorialSolicitudPlani->setUsrCreacion($strUsrCreacion);
                    $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                    $objHistorialSolicitudPlani->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objHistorialSolicitudPlani);
                    $this->emComercial->flush();
                    
                    $arrayParametros = array();
                    $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudServicio->getId();
                    $arrayParametros['strProceso']            = 'Activar';
                    $this->emInfraestructura
                         ->getRepository('schemaBundle:InfoDetalleSolicitud')
                         ->cerrarTareasPorSolicitud($arrayParametros);
                }
                
                $this->emComercial->getConnection()->commit();
                $this->emNaf->getConnection()->commit();
                $this->emInfraestructura->getConnection()->commit();
                $strStatus = "OK";
            }
            else
            {
                $strStatus = "ERROR";
            }
        }
        catch (\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            $strStatus = "ERROR";
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioNetHome',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $strStatus;
    }
    
    /**
     * confirmarServicioPorNuevoMd
     * 
     * Funcion que sirve para confirmar el servicio de tipo de orden Nueva
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 19-02-2017
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 24-05-2017      Se agregan validaciones para generar los historiales de servicio según sea el escenario
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - strSerieSmartWifi             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloSmartWifi            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strMacSmartWifi               Cadena de caracteres que indica la mac del equipo SmartWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                               ]
     * @return Array $respuestaFinal [
     *                                 - status   Estado de la transaccion ejecutada
     *                                 - mensaje  Mensaje de la transaccion ejecutada
     *                               ]
     */
    public function confirmarServicioSmartWifi( $arrayParametros )
    {
        $objServicio                    = !empty($arrayParametros['ojbServicio'])?$arrayParametros['ojbServicio']:null;
        $objServicioTecnico             = !empty($arrayParametros['objServicioTecnico'])?$arrayParametros['objServicioTecnico']:null;
        $objProducto                    = !empty($arrayParametros['objProducto'])?$arrayParametros['objProducto']:null;
        $strUsrCreacion                 = !empty($arrayParametros['strUsrCreacion'])?$arrayParametros['strUsrCreacion']:"";
        $strIpCreacion                  = !empty($arrayParametros['strIpCreacion'])?$arrayParametros['strIpCreacion']:"";
        $objAccion                      = !empty($arrayParametros['objAccion'])?$arrayParametros['objAccion']:null;
        $strSerieSmartWifi              = !empty($arrayParametros['strSerieSmartWifi'])?$arrayParametros['strSerieSmartWifi']:"";
        $strModeloSmartWifi             = !empty($arrayParametros['strModeloSmartWifi'])?$arrayParametros['strModeloSmartWifi']:"";
        $strMacSmartWifi                = !empty($arrayParametros['strMacSmartWifi'])?$arrayParametros['strMacSmartWifi']:"";
        $intIdServicioInternet          = !empty($arrayParametros['intIdServicioInternet'])?$arrayParametros['intIdServicioInternet']:0;
        $intIdSolicitudServicio         = !empty($arrayParametros['intIdSolicitudServicio'])?$arrayParametros['intIdSolicitudServicio']:0;
        $strEmpresaCod                  = !empty($arrayParametros['strEmpresaCod'])?$arrayParametros['strEmpresaCod']:"";
        $strEsCambioPlan                = !empty($arrayParametros['strEsCambioPlan'])?$arrayParametros['strEsCambioPlan']:"";
        $strStatus                      = "ERROR";
        $strUltimaMilla                 = "";
        $strTipoSmartWifi               = "";
        $this->emComercial->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try            
        {
            if (!is_object($objServicio))
            {
                throw new \Exception("No se encontro información acerca del servicio que se está procesando");
            }
            
            if (!is_object($objAccion))
            {
                throw new \Exception("No se encontro información acerca de la acción ejecutada");
            }
            
            //se agregan validaciones para realizar activación de servicios SmartWifi
            if (is_object($objServicioTecnico))
            {
                $objTipoMedio       = $this->emInfraestructura
                                           ->getRepository('schemaBundle:AdmiTipoMedio')
                                           ->find($objServicioTecnico->getUltimaMillaId());
                if (is_object($objTipoMedio))
                {
                    $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
                }
            }

            if (strpos($objProducto->getDescripcionProducto(), 'Renta') !== false)
            {
                $strTipoSmartWifi = "-RentaSmartWifi";
            }
            else
            {
                $strTipoSmartWifi = "-SmartWifi";
            }
            $arrayParametrosSmartWifi = array (
                                                'strSerieSmartWifi'     => $strSerieSmartWifi,
                                                'strModeloSmartWifi'    => $strModeloSmartWifi,
                                                'strMacSmartWifi'       => $strMacSmartWifi,
                                                'intIdServicioInternet' => $intIdServicioInternet,
                                                'strUsrCreacion'        => $strUsrCreacion,
                                                'strIpCreacion'         => $strIpCreacion,
                                                'strEmpresaCod'         => $strEmpresaCod,
                                                'objServicio'           => $objServicio,
                                                'strUltimaMilla'        => $strUltimaMilla,
                                                'strTipoSmartWifi'      => $strTipoSmartWifi,
                                                'strEsCambioPlan'       => $strEsCambioPlan
                                              );
            $strStatus = $this->ingresarElementoSmartWifi( $arrayParametrosSmartWifi );
            
            if($strStatus == "OK")
            {
                $strEstadoServicio       = "";
                $strNombreAccion         = "";
                $strObservacionHistorial = "";
                //se agregan validaciones para generar los historiales de servicio según sea el escenario
                if ($strEsCambioPlan == 'SI')
                {
                    $strEstadoServicio       = $objServicio->getEstado();
                    $strNombreAccion         = "";
                    $strObservacionHistorial = "Se agrego el equipo SmartWifi";
                }
                else
                {
                    if (is_object($objServicio->getPlanId()))
                    {
                        $strEstadoServicio       = $objServicio->getEstado();
                        $strNombreAccion         = "";
                        $strObservacionHistorial = "Se agrego el equipo SmartWifi";
                    }
                    else
                    {
                        $strEstadoServicio       = "Activo";
                        $strNombreAccion         = $objAccion->getNombreAccion();
                        $strObservacionHistorial = "Se confirmo el servicio";
                    }
                    
                }
                
                $objServicio->setEstado($strEstadoServicio);
                $this->emComercial->persist($objServicio);

                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionHistorial);
                $objServicioHistorial->setEstado($strEstadoServicio);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setAccion($strNombreAccion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                
                //finalizar solicitud 
                $objSolicitudServicio = $this->emComercial
                                             ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                             ->find($intIdSolicitudServicio);

                if (is_object($objSolicitudServicio))
                {
                    $objSolicitudServicio->setEstado("Finalizada");
                    $this->emComercial->persist($objSolicitudServicio);
                    $this->emComercial->flush();

                    //crear historial para la solicitud
                    $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                    $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudServicio);
                    $objHistorialSolicitudPlani->setEstado("Finalizada");
                    $objHistorialSolicitudPlani->setObservacion("Cliente instalado");
                    $objHistorialSolicitudPlani->setUsrCreacion($strUsrCreacion);
                    $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                    $objHistorialSolicitudPlani->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objHistorialSolicitudPlani);
                    $this->emComercial->flush();
                    
                    $arrayParametros = array();
                    $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudServicio->getId();
                    $arrayParametros['strProceso']            = 'Activar';
                    $strMensajeResponse                       = $this->emInfraestructura
                                                                     ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                     ->cerrarTareasPorSolicitud($arrayParametros);
                }
                
                $this->emComercial->getConnection()->commit();
                $this->emNaf->getConnection()->commit();
                $this->emInfraestructura->getConnection()->commit();
                $strStatus = "OK";
            }
            else
            {
                $strStatus = "ERROR";
            }
        }
        catch (\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            $strStatus = "ERROR";
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioSmartWifi',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $strStatus;
    }
    
    /**
     * confirmarServicioApWifi
     * 
     * Funcion que sirve para confirmar el servicio de tipo de orden Nueva
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-09-2018
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - strSerieApWifi                Cadena de caracteres que indica la serie del equipo ApWifi a registrar
     *                                 - strModeloApWifi               Cadena de caracteres que indica el modelo del equipo ApWifi a registrar
     *                                 - strMacApWifi                  Cadena de caracteres que indica la mac del equipo ApWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                               ]
     * @return Array $respuestaFinal [
     *                                 - status   Estado de la transaccion ejecutada
     *                                 - mensaje  Mensaje de la transaccion ejecutada
     *                               ]
     */
    public function confirmarServicioApWifi( $arrayParametros )
    {
        $objServicio                    = !empty($arrayParametros['ojbServicio'])?$arrayParametros['ojbServicio']:null;
        $objServicioTecnico             = !empty($arrayParametros['objServicioTecnico'])?$arrayParametros['objServicioTecnico']:null;
        $objProducto                    = !empty($arrayParametros['objProducto'])?$arrayParametros['objProducto']:null;
        $strUsrCreacion                 = !empty($arrayParametros['strUsrCreacion'])?$arrayParametros['strUsrCreacion']:"";
        $strIpCreacion                  = !empty($arrayParametros['strIpCreacion'])?$arrayParametros['strIpCreacion']:"";
        $objAccion                      = !empty($arrayParametros['objAccion'])?$arrayParametros['objAccion']:null;
        $strSerieApWifi                 = !empty($arrayParametros['strSerieApWifi'])?$arrayParametros['strSerieApWifi']:"";
        $strModeloApWifi                = !empty($arrayParametros['strModeloApWifi'])?$arrayParametros['strModeloApWifi']:"";
        $strMacApWifi                   = !empty($arrayParametros['strMacApWifi'])?$arrayParametros['strMacApWifi']:"";
        $intIdServicioInternet          = !empty($arrayParametros['intIdServicioInternet'])?$arrayParametros['intIdServicioInternet']:0;
        $intIdSolicitudServicio         = !empty($arrayParametros['intIdSolicitudServicio'])?$arrayParametros['intIdSolicitudServicio']:0;
        $strEmpresaCod                  = !empty($arrayParametros['strEmpresaCod'])?$arrayParametros['strEmpresaCod']:"";
        $strEsCambioPlan                = !empty($arrayParametros['strEsCambioPlan'])?$arrayParametros['strEsCambioPlan']:"";
        $strStatus                      = "ERROR";
        $strUltimaMilla                 = "";
        $strTipoApWifi                  = "";
        $this->emComercial->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try            
        {
            if (!is_object($objServicio))
            {
                throw new \Exception("No se encontro información acerca del servicio que se está procesando");
            }
            
            if (!is_object($objAccion))
            {
                throw new \Exception("No se encontro información acerca de la acción ejecutada");
            }
            
            //se agregan validaciones para realizar activación de servicios ApWifi
            if (is_object($objServicioTecnico))
            {
                $objTipoMedio       = $this->emInfraestructura
                                           ->getRepository('schemaBundle:AdmiTipoMedio')
                                           ->find($objServicioTecnico->getUltimaMillaId());
                if (is_object($objTipoMedio))
                {
                    $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
                }
            }

            if (strpos($objProducto->getDescripcionProducto(), 'Renta') !== false)
            {
                $strTipoApWifi = "-RentaApWifi";
            }
            else
            {
                $strTipoApWifi = "-ApWifi";
            }
            $arrayParametrosApWifi = array (
                                            'strSerieApWifi'        => $strSerieApWifi,
                                            'strModeloApWifi'       => $strModeloApWifi,
                                            'strMacApWifi'          => $strMacApWifi,
                                            'intIdServicioInternet' => $intIdServicioInternet,
                                            'strUsrCreacion'        => $strUsrCreacion,
                                            'strIpCreacion'         => $strIpCreacion,
                                            'strEmpresaCod'         => $strEmpresaCod,
                                            'objServicio'           => $objServicio,
                                            'strUltimaMilla'        => $strUltimaMilla,
                                            'strTipoApWifi'         => $strTipoApWifi,
                                            'strEsCambioPlan'       => $strEsCambioPlan
                                           );
            $strStatus = $this->ingresarElementoApWifi( $arrayParametrosApWifi );
            
            if($strStatus == "OK")
            {
                $strEstadoServicio       = "";
                $strNombreAccion         = "";
                $strObservacionHistorial = "";
                //se agregan validaciones para generar los historiales de servicio según sea el escenario
                if ($strEsCambioPlan == 'SI')
                {
                    $strEstadoServicio       = $objServicio->getEstado();
                    $strNombreAccion         = "";
                    $strObservacionHistorial = "Se agrego el equipo ApWifi";
                }
                else
                {
                    if (is_object($objServicio->getPlanId()))
                    {
                        $strEstadoServicio       = $objServicio->getEstado();
                        $strNombreAccion         = "";
                        $strObservacionHistorial = "Se agrego el equipo ApWifi";
                    }
                    else
                    {
                        $strEstadoServicio       = "Activo";
                        $strNombreAccion         = $objAccion->getNombreAccion();
                        $strObservacionHistorial = "Se confirmo el servicio";
                    }
                    
                }
                
                $objServicio->setEstado($strEstadoServicio);
                $this->emComercial->persist($objServicio);

                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionHistorial);
                $objServicioHistorial->setEstado($strEstadoServicio);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setAccion($strNombreAccion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                
                //finalizar solicitud 
                $objSolicitudServicio = $this->emComercial
                                             ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                             ->find($intIdSolicitudServicio);

                if (is_object($objSolicitudServicio))
                {
                    $objSolicitudServicio->setEstado("Finalizada");
                    $this->emComercial->persist($objSolicitudServicio);
                    $this->emComercial->flush();

                    //crear historial para la solicitud
                    $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                    $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudServicio);
                    $objHistorialSolicitudPlani->setEstado("Finalizada");
                    $objHistorialSolicitudPlani->setObservacion("Cliente instalado");
                    $objHistorialSolicitudPlani->setUsrCreacion($strUsrCreacion);
                    $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                    $objHistorialSolicitudPlani->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objHistorialSolicitudPlani);
                    $this->emComercial->flush();
                    
                    $arrayParametros = array();
                    $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudServicio->getId();
                    $arrayParametros['strProceso']            = 'Activar';
                    $this->emInfraestructura
                         ->getRepository('schemaBundle:InfoDetalleSolicitud')
                         ->cerrarTareasPorSolicitud($arrayParametros);
                }
                
                $this->emComercial->getConnection()->commit();
                $this->emNaf->getConnection()->commit();
                $this->emInfraestructura->getConnection()->commit();
                $strStatus = "OK";
            }
            else
            {
                $strStatus = "ERROR";
            }
        }
        catch (\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            $strStatus = "ERROR";
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioApWifi',
                                            $ex->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $strStatus;
    }
    
    /**
     * ingresarElementoApWifi
     * 
     * Funcion que genera realizar el ingreso de elementos APWIFI
     * 
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 12-09-2018
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - strSerieApWifi                Cadena de caracteres que indica la serie del equipo ApWifi a registrar
     *                                 - strModeloApWifi               Cadena de caracteres que indica el modelo del equipo ApWifi a registrar
     *                                 - strMacApWifi                  Cadena de caracteres que indica la mac del equipo ApWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - strUltimaMilla                Cadena de caracteres que indica la ultima milla del servicio procesado
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                               ]
     * @return String  $status  Estado de la transaccion ejecutada
     * 
     */
    public function ingresarElementoApWifi( $arrayParametros )
    {
        $strSerieApWifi           = $arrayParametros['strSerieApWifi'];
        $strModeloApWifi          = $arrayParametros['strModeloApWifi'];
        $strMacApWifi             = $arrayParametros['strMacApWifi'];
        $intIdServicioInternet    = $arrayParametros['intIdServicioInternet'];
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];
        $objServicio              = $arrayParametros['objServicio'];
        $strUltimaMilla           = $arrayParametros['strUltimaMilla'];
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
        $strEmpresaCod            = $arrayParametros['strEmpresaCod'];
        $strTipoApWifi            = $arrayParametros['strTipoApWifi'];
        $strEsCambioPlan          = $arrayParametros['strEsCambioPlan'];
        $objInterfaceElementoFin  = null;
        $strStatus                = 'OK';
        $strMensaje               = '';
        $strTipoArticulo          = 'AF';
        $strIdentificacionCliente = "";
        try
        {
            //se recupera ultimo elemento enlazado en el servicio para poder ingresar el nuevo elemento AP WIFI
            $objServicioInternet = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioInternet);
            if (is_object($objServicioInternet))
            {
                $objServicioTecnicoInternet = $this->emComercial
                                                   ->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneBy(array( "servicioId" => $objServicioInternet->getId()));
                if (is_object($objServicioTecnicoInternet))
                {
                    if($objServicioTecnicoInternet->getInterfaceElementoClienteId())
                    {
                        $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnicoInternet->getInterfaceElementoClienteId();
                        $arrayParams['arrayData']                      = array();
                        $arrayParams['strBanderaReturn']               = 'INTERFACE';
                        $arrayParams['strTipoApWifi']                  = 'ApWifi';
                        if ($strEsCambioPlan == "SI")
                        {
                            $objInterfaceElementoSt = $this->emInfraestructura
                                                           ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->find($objServicioTecnicoInternet->getInterfaceElementoClienteId());
                            $arrayParams['strRetornaUltElePlan']        = 'SI';
                            $arrayParams['objInterfaceElementoFinPlan'] = $objInterfaceElementoSt;
                        }
                        $objInterfaceElementoFin = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoElemento')
                                                        ->getElementosApWifiByInterface($arrayParams);
                        $objIntElePlanFin        = $objInterfaceElementoFin;
                    }
                }
                else
                {
                    $strMensaje = 'Se presentaron errores al recuperar información técnica del servicio de internet.';
                    $strStatus  = 'ERROR';
                }
            }
            else
            {
                $strMensaje = 'Se presentaron errores al recuperar información del servicio de internet.';
                $strStatus  = 'ERROR';
            }

            if(!is_object($objInterfaceElementoFin))
            {
                $strMensaje = 'Se presentaron errores al recuperar información para crear elemento Ap Wifi.';  
                $strStatus  = 'ERROR';
            }

            if ($strStatus != 'ERROR')
            {
                //se procede a realizar el ingreso del elemento Ap Wifi y despacharlo en el NAF
                $arrayWifiNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieApWifi, 
                                                                            $strModeloApWifi, 
                                                                            "PI", 
                                                                            "ActivarServicio");
                $strWifiNaf            = $arrayWifiNaf[0]['status'];
                $strCodigoArticuloWifi = "";
                if($strWifiNaf == "OK")
                {
                    if ($strEsCambioPlan == "SI")
                    {
                        $objInterfaceElementoFin = null;
                    }
                    $objInterfaceElementoApWifi = $this->servicioGeneral
                                                       ->ingresarElementoCliente(  $objServicio->getPuntoId()->getLogin(), 
                                                                                   $strSerieApWifi, 
                                                                                   $strModeloApWifi,
                                                                                   '-'.$objServicio->getId().$strTipoApWifi, 
                                                                                   $objInterfaceElementoFin, 
                                                                                   $strUltimaMilla,
                                                                                   $objServicio, 
                                                                                   $strUsrCreacion, 
                                                                                   $strIpCreacion, 
                                                                                   $strEmpresaCod );
                    if(is_object($objInterfaceElementoApWifi))
                    {
                        $objElementoApWifi = $objInterfaceElementoApWifi->getElementoId();
                        
                        if (!is_object($objElementoApWifi))
                        {
                            throw new \Exception("No se encontro información del elemento ApWifi creado");
                        }
            
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                              $objElementoApWifi->getNombreElemento().
                                                              ", Serie: ".
                                                              $strSerieApWifi.
                                                              ", Modelo: ".
                                                              $strModeloApWifi.
                                                              ", Mac: ".
                                                              $strMacApWifi
                                                             );
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                        
                        //actualizamos registro en el naf wifi
                        $strMensajeError = str_repeat(' ', 1000);                                                                  
                        $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                           ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                           ":cantidad, :pv_mensajeerror); END;";
                        $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                        $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                        $objStmt->bindParam('codigoArticulo',        $strCodigoArticuloWifi);
                        $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
                        $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                        $objStmt->bindParam('serieCpe',              $strSerieApWifi);
                        $objStmt->bindParam('cantidad',              intval(1));
                        $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
                        $objStmt->execute();

                        if(strlen(trim($strMensajeError))>0)
                        {
                            $strMensaje = "ERROR WIFI NAF: ".$strMensajeError; 
                            $strStatus  = 'ERROR';
                        }
                        else
                        {
                            if ($strEsCambioPlan != "SI")
                            {
                                //servicio prod caract mac wifi
                                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                               $objServicio->getProductoId(), 
                                                                                               "MAC WIFI", 
                                                                                               $strMacApWifi, 
                                                                                               $strUsrCreacion);
                                $objServicioTecnico = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneBy(array( "servicioId" => $objServicio->getId()));
                                if (is_object($objServicioTecnico))
                                {
                                    //guardar ont en servicio tecnico
                                    $objServicioTecnico->setElementoClienteId($objElementoApWifi->getId());
                                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoApWifi->getId());
                                    $this->emComercial->persist($objServicioTecnico);
                                    $this->emComercial->flush();
                                }
                            }
                            else
                            {
                                $objTipoMedio = $this->emInfraestructura
                                                     ->getRepository('schemaBundle:AdmiTipoMedio')
                                                     ->find($objServicioTecnicoInternet->getUltimaMillaId());
                                
                                if (!is_object($objTipoMedio))
                                {
                                    throw new \Exception("No se encontro información del tipo medio del servicio");
                                }
                                
                                if (!is_object($objIntElePlanFin))
                                {
                                    throw new \Exception("No se encontro información de la ultima interface de los elementos del cliente");
                                }

                                /* Se verifica si la interface del ultimo elemento del plan que no sea un equipo ApWifi existe como
                                 * inicio de un enlace, en caso de existir se elimina este enlace y se crea nuevo enlace teniendo
                                 * como inicio la interface del nuevo equipo ApWifi registrado en este proceso y como fin la interface
                                 * fin del enlace previamente eliminado */
                                $objEnlaceAnterior = $this->emInfraestructura
                                                          ->getRepository('schemaBundle:InfoEnlace')
                                                          ->findOneBy(array("interfaceElementoIniId" => $objIntElePlanFin->getId(),
                                                                            "estado"                 => 'Activo'));
                                if(is_object($objEnlaceAnterior))
                                {
                                    $objEnlaceAnterior->setEstado("Eliminado");
                                    $this->emInfraestructura->persist($objEnlaceAnterior);
                                    $this->emInfraestructura->flush();
                                    
                                    
                                    $objEnlaceSegundoNivel = new InfoEnlace();
                                    $objEnlaceSegundoNivel->setInterfaceElementoIniId($objInterfaceElementoApWifi);
                                    $objEnlaceSegundoNivel->setInterfaceElementoFinId($objEnlaceAnterior->getInterfaceElementoFinId());
                                    $objEnlaceSegundoNivel->setTipoMedioId($objEnlaceAnterior->getTipoMedioId());
                                    $objEnlaceSegundoNivel->setTipoEnlace("PRINCIPAL");
                                    $objEnlaceSegundoNivel->setEstado("Activo");
                                    $objEnlaceSegundoNivel->setUsrCreacion($strUsrCreacion);
                                    $objEnlaceSegundoNivel->setFeCreacion(new \DateTime('now'));
                                    $objEnlaceSegundoNivel->setIpCreacion($strIpCreacion);
                                    $this->emInfraestructura->persist($objEnlaceSegundoNivel);
                                    $this->emInfraestructura->flush();
                                }
                                
                                /* se crea nuevo enlace teniendo como inicio la interface del ultimo elemento del plan que no sea un equipo ApWifi 
                                   y como fin la interface del nuevo equipo ApWifi registrado en este proceso*/
                                $objEnlacePrimerNivel = new InfoEnlace();
                                $objEnlacePrimerNivel->setInterfaceElementoIniId($objIntElePlanFin);
                                $objEnlacePrimerNivel->setInterfaceElementoFinId($objInterfaceElementoApWifi);
                                $objEnlacePrimerNivel->setTipoMedioId($objTipoMedio);
                                $objEnlacePrimerNivel->setTipoEnlace("PRINCIPAL");
                                $objEnlacePrimerNivel->setEstado("Activo");
                                $objEnlacePrimerNivel->setUsrCreacion($strUsrCreacion);
                                $objEnlacePrimerNivel->setFeCreacion(new \DateTime('now'));
                                $objEnlacePrimerNivel->setIpCreacion($strIpCreacion);
                                $this->emInfraestructura->persist($objEnlacePrimerNivel);
                                $this->emInfraestructura->flush();
                            }
                            //info_detalle_elemento gestion remota
                            $this->servicioGeneral->ingresarDetalleElemento($objElementoApWifi,
                                                                            "MAC WIFI",
                                                                            "MAC WIFI",
                                                                            $strMacApWifi,
                                                                            $strUsrCreacion,
                                                                            $strIpCreacion); 
                            
                            
                        }
                    }
                    else
                    {
                        $strMensaje = 'Se presentaron errores al ingresar el elemento Ap Wifi.'; 
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = "ERROR WIFI NAF: ".$arrayWifiNaf[0]['mensaje']; 
                    $strStatus  = 'ERROR';
                }        
            }
            if ($strStatus == 'ERROR')
            {
                $this->utilService->insertError('Telcos+', 
                                                'InfoConfirmarServicioService.ingresarElementoApWifi', 
                                                $strMensaje,
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );

            }
        } 
        catch (\Exception $e) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoConfirmarServicioService.ingresarElementoApWifi', 
                                            $e->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strStatus = 'ERROR';
        }
        
        return $strStatus;
    }
    
    /**
     * ingresarElementoSmartWifi
     * 
     * Funcion que genera realizar el corte de servicios OTROS
     * 
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 24-01-2017
     * @since 1.0
     * 
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 01-03-2017     Se agregaron validaciones para procesar activación de servicios SmartWifi 
     *                             generados mediante de cambios de plan y confirmación de servicios de tipo
     *                             producto
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 20-10-2020 - Se agrega programación para consultar si un producto sin flujo esta hablitado para que se realice la activacion y
     *                           registro de elementos
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 10-11-2020 - Para servicios netlifecam se relaciona el elemento con el servicio que se activa.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 19-11-2020 - Se registra la tarjeta micro sd en las camaras de netlifecam
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 25-07-2021 - Se realizar modificaciones para enlazar elementos al activar Netlifecam Md
     * 
     * @param Array $arrayParametros [
     *                                 - strSerieSmartWifi             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloSmartWifi            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strMacSmartWifi               Cadena de caracteres que indica la mac del equipo SmartWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - strUltimaMilla                Cadena de caracteres que indica la ultima milla del servicio procesado
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                               ]
     * @return String  $status  Estado de la transaccion ejecutada
     * 
     */
    public function ingresarElementoSmartWifi( $arrayParametros )
    {
        $strSerieSmartWifi        = $arrayParametros['strSerieSmartWifi'];
        $strModeloSmartWifi       = $arrayParametros['strModeloSmartWifi'];
        $strMacSmartWifi          = $arrayParametros['strMacSmartWifi'];
        $intIdServicioInternet    = $arrayParametros['intIdServicioInternet'];
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];
        $objServicio              = $arrayParametros['objServicio'];
        $strUltimaMilla           = $arrayParametros['strUltimaMilla'];
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
        $strEmpresaCod            = $arrayParametros['strEmpresaCod'];
        $strTipoSmartWifi         = $arrayParametros['strTipoSmartWifi'];
        $strEsCambioPlan          = $arrayParametros['strEsCambioPlan'];
        $strPoductoPermitido      = $arrayParametros['strPoductoPermitido']?$arrayParametros['strPoductoPermitido']:"N";
        $strCapacidadTarjeta      = $arrayParametros['strCapacidadTarjeta']?$arrayParametros['strCapacidadTarjeta']:"";
        $strMarcaTarjeta          = $arrayParametros['strMarcaTarjeta']?$arrayParametros['strMarcaTarjeta']:"";
        $strModeloTarjeta         = $arrayParametros['strModeloTarjeta']?$arrayParametros['strModeloTarjeta']:"";
        $strSerieTarjeta          = $arrayParametros['strSerieTarjeta']?$arrayParametros['strSerieTarjeta']:"";
        $strTipoElemento          = $arrayParametros['strTipoElemento'];
        $strTipoElementoAux       = "micro-SD";
        $objInterfaceElementoFin  = null;
        $strStatus                = 'OK';
        $strMensaje               = '';
        $strTipoArticulo          = 'AF';
        $strIdentificacionCliente = "";
        try
        {
            
                //se recupera ultimo elemento enlazado en el servicio para poder ingresar el nuevo elemento Smart Space
                $objServicioInternet = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioInternet);
                if (is_object($objServicioInternet))
                {
                    $objServicioTecnicoInternet = $this->emComercial
                                                       ->getRepository('schemaBundle:InfoServicioTecnico')
                                                       ->findOneBy(array( "servicioId" => $objServicioInternet->getId()));
                    if (is_object($objServicioTecnicoInternet))
                    {
                        if($objServicioTecnicoInternet->getInterfaceElementoClienteId())
                        {
                            $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnicoInternet->getInterfaceElementoClienteId();
                            $arrayParams['arrayData']                      = array();
                            $arrayParams['strBanderaReturn']               = 'INTERFACE';
                            if($strPoductoPermitido != "S")
                            {
                                $arrayParams['strTipoSmartWifi']               = 'SmartWifi';
                            }
                            if ($strEsCambioPlan == "SI")
                            {
                                $objInterfaceElementoSt = $this->emInfraestructura
                                                               ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                               ->find($objServicioTecnicoInternet->getInterfaceElementoClienteId());
                                $arrayParams['strRetornaUltElePlan']        = 'SI';
                                $arrayParams['objInterfaceElementoFinPlan'] = $objInterfaceElementoSt;
                            }
                            $objInterfaceElementoFin = $this->emInfraestructura
                                                            ->getRepository('schemaBundle:InfoElemento')
                                                            ->getElementosSmartWifiByInterface($arrayParams);
                            $objIntElePlanFin        = $objInterfaceElementoFin;
                        }
                    }
                    else
                    {
                        $strMensaje = 'Se presentaron errores al recuperar información técnica del servicio de internet.';  
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = 'Se presentaron errores al recuperar información del servicio de internet.';  
                    $strStatus  = 'ERROR';
                }

                if(!is_object($objInterfaceElementoFin))
                {
                    $strStatus  = 'ERROR';
                    if($strPoductoPermitido != "S")
                    {
                        $strMensaje = 'Se presentaron errores al recuperar información para crear elemento Smart Wifi.';
                    }
                    else
                    {
                        $strMensaje = 'Se presentaron errores al recuperar información para crear elemento.';
                    }
                }
            

            if ($strStatus != 'ERROR')
            {
                //se procede a realizar el ingreso del elemento Smart Space y despacharlo en el NAF
                $arrayWifiNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieSmartWifi, 
                                                                            $strModeloSmartWifi, 
                                                                            "PI", 
                                                                            "ActivarServicio");
                $strWifiNaf            = $arrayWifiNaf[0]['status'];
                $strCodigoArticuloWifi = "";
                if($strWifiNaf == "OK")
                {
                    $strNombreModelo = '-'.$objServicio->getId().$strTipoSmartWifi;

                    if ($strEsCambioPlan == "SI")
                    {
                        $objInterfaceElementoFin = null;
                    }

                    if($strPoductoPermitido == "S")
                    {
                        $strNombreModelo         = $strTipoElemento;
                        $strNombreModeloAux      = $strTipoElementoAux;
                    }

                    $objInterfaceElementoSmartWifi = $this->servicioGeneral
                                                          ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(), 
                                                                                     $strSerieSmartWifi, 
                                                                                     $strModeloSmartWifi,
                                                                                     $strNombreModelo, 
                                                                                     $objInterfaceElementoFin, 
                                                                                     $strUltimaMilla,
                                                                                     $objServicio, 
                                                                                     $strUsrCreacion, 
                                                                                     $strIpCreacion, 
                                                                                     $strEmpresaCod );
                    if(is_object($objInterfaceElementoSmartWifi))
                    {
                        $objElementoSmartWifi = $objInterfaceElementoSmartWifi->getElementoId();

                        if (!is_object($objElementoSmartWifi))
                        {
                            if($strPoductoPermitido != "S")
                            {
                                $strMensajeError = "SmartWifi ";
                            }

                            throw new \Exception("No se encontro información del elemento ".$strMensajeError."creado");
                        }
                        else
                        {
                            if($strPoductoPermitido == "S")
                            {
                                $objServicioTecnico = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneBy(array( "servicioId" => $objServicio->getId()));
                                if (is_object($objServicioTecnico))
                                {
                                    //guardar ont en servicio tecnico
                                    $objServicioTecnico->setElementoClienteId($objElementoSmartWifi->getId());
                                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoSmartWifi->getId());
                                    $this->emComercial->persist($objServicioTecnico);
                                    $this->emComercial->flush();
                                }
                                if (!empty($strMacSmartWifi))
                                {
                                    $this->servicioGeneral->ingresarDetalleElemento($objElementoSmartWifi,
                                                    "MAC","MAC",
                                                    $strMacSmartWifi,
                                                    $strUsrCreacion,
                                                    $strIpCreacion);
                                }
                            }
                        }

                        if($strPoductoPermitido == "S")
                        {
                            if(!is_object($objInterfaceElementoSmartWifi))
                            {
                                throw new \Exception("No se ha podido obtener correctamente la interface del elemento.");
                            }
                            //Para productos NETLIFECAM se registra la tarjeta micro SD
                            $objInterfaceElementoTarjeta = $this->servicioGeneral
                                                                  ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(),
                                                                                             $strSerieTarjeta,
                                                                                             $strModeloTarjeta,
                                                                                             $strNombreModeloAux,
                                                                                             $objInterfaceElementoSmartWifi,
                                                                                             $strUltimaMilla,
                                                                                             $objServicio,
                                                                                             $strUsrCreacion,
                                                                                             $strIpCreacion,
                                                                                             $strEmpresaCod);

                            if(!is_object($objInterfaceElementoTarjeta))
                            {
                                throw new \Exception("Ocurrió un error en la creación de la micro SD, favor notificar a Sistemas");
                            }
                        }

                        if($strPoductoPermitido == "S")
                        {
                            $strObservacionHistorial.= "<b>Informaci&oacute;n de los Elementos del Cliente</b><br/>";
                            $strObservacionHistorial.= "<b>CAMARA</b><br/>";
                            $strObservacionHistorial.= "<b>Elemento:</b> ".$objElementoSmartWifi->getNombreElemento()."<br/>";
                            $strObservacionHistorial.= "<b>Serie:</b> ".$strSerieSmartWifi."<br/>";
                            $strObservacionHistorial.= "<b>Modelo:</b> ".$strModeloSmartWifi."<br/>";
                            $strObservacionHistorial.= "<b>Mac:</b> ".$strMacSmartWifi."<br/><br/>";
                            $strObservacionHistorial.= "<b>Tarjeta micro SD</b><br/>";
                            $strObservacionHistorial.= "<b>Serie Tarjeta:</b> ".$strSerieTarjeta."<br/>";
                            $strObservacionHistorial.= "<b>Capacidad Tarjeta:</b> ".$strCapacidadTarjeta."<br/>";
                            $strObservacionHistorial.= "<b>Modelo Tarjeta:</b> ".$strModeloTarjeta."<br/>";
                            $strObservacionHistorial.= "<b>Marca Tarjeta:</b> ".$strMarcaTarjeta."<br/>";
                        }
                        else
                        {
                            $strObservacionHistorial = "Se registro el elemento con nombre: ".
                                                              $objElementoSmartWifi->getNombreElemento().
                                                              ", Serie: ".
                                                              $strSerieSmartWifi.
                                                              ", Modelo: ".
                                                              $strModeloSmartWifi.
                                                              ", Mac: ".
                                                              $strMacSmartWifi;
                        }

                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion($strObservacionHistorial);
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                        
                        //actualizamos registro en el naf wifi
                        $strMensajeError = str_repeat(' ', 1000);                                                                  
                        $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                           ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                           ":cantidad, :pv_mensajeerror); END;";
                        $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                        $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                        $objStmt->bindParam('codigoArticulo',        $strCodigoArticuloWifi);
                        $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
                        $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                        $objStmt->bindParam('serieCpe',              $strSerieSmartWifi);
                        $objStmt->bindParam('cantidad',              intval(1));
                        $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
                        $objStmt->execute();

                        if(strlen(trim($strMensajeError))>0)
                        {
                            $strMensaje = "ERROR WIFI NAF: ".$strMensajeError; 
                            $strStatus  = 'ERROR';
                        }
                        else
                        {
                            if($strPoductoPermitido != "S")
                            {
                                if ($strEsCambioPlan != "SI")
                                {
                                    //servicio prod caract mac wifi
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                                   $objServicio->getProductoId(),
                                                                                                   "MAC WIFI",
                                                                                                   $strMacSmartWifi,
                                                                                                   $strUsrCreacion);
                                    $objServicioTecnico = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->findOneBy(array( "servicioId" => $objServicio->getId()));
                                    if (is_object($objServicioTecnico))
                                    {
                                        //guardar ont en servicio tecnico
                                        $objServicioTecnico->setElementoClienteId($objElementoSmartWifi->getId());
                                        $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoSmartWifi->getId());
                                        $this->emComercial->persist($objServicioTecnico);
                                        $this->emComercial->flush();
                                    }
                                }
                                else
                                {
                                    $objTipoMedio = $this->emInfraestructura
                                                         ->getRepository('schemaBundle:AdmiTipoMedio')
                                                         ->find($objServicioTecnicoInternet->getUltimaMillaId());

                                    if (!is_object($objTipoMedio))
                                    {
                                        throw new \Exception("No se encontro información del tipo medio del servicio");
                                    }

                                    if (!is_object($objIntElePlanFin))
                                    {
                                        throw new \Exception("No se encontro información de la ultima interface de los elementos del cliente");
                                    }

                                    /* Se verifica si la interface del ultimo elemento del plan que no sea un equipo SmartWifi existe como 
                                     * inicio de un enlace, en caso de existir se elimina este enlace y se crea nuevo enlace teniendo como
                                     * inicio la interface del nuevo equipo SmartWifi registrado en este proceso y como fin la 
                                     * interface fin del enlace previamente eliminado */
                                    $objEnlaceAnterior = $this->emInfraestructura
                                                              ->getRepository('schemaBundle:InfoEnlace')
                                                              ->findOneBy(array("interfaceElementoIniId" => $objIntElePlanFin->getId(),
                                                                                "estado"                 => 'Activo'));
                                    if(is_object($objEnlaceAnterior))
                                    {
                                        $objEnlaceAnterior->setEstado("Eliminado");
                                        $this->emInfraestructura->persist($objEnlaceAnterior);
                                        $this->emInfraestructura->flush();


                                        $objEnlaceSegundoNivel = new InfoEnlace();
                                        $objEnlaceSegundoNivel->setInterfaceElementoIniId($objInterfaceElementoSmartWifi);
                                        $objEnlaceSegundoNivel->setInterfaceElementoFinId($objEnlaceAnterior->getInterfaceElementoFinId());
                                        $objEnlaceSegundoNivel->setTipoMedioId($objEnlaceAnterior->getTipoMedioId());
                                        $objEnlaceSegundoNivel->setTipoEnlace("PRINCIPAL");
                                        $objEnlaceSegundoNivel->setEstado("Activo");
                                        $objEnlaceSegundoNivel->setUsrCreacion($strUsrCreacion);
                                        $objEnlaceSegundoNivel->setFeCreacion(new \DateTime('now'));
                                        $objEnlaceSegundoNivel->setIpCreacion($strIpCreacion);
                                        $this->emInfraestructura->persist($objEnlaceSegundoNivel);
                                        $this->emInfraestructura->flush();
                                    }

                                    /* se crea nuevo enlace teniendo como inicio la interface del ultimo elemento del plan que no sea un equipo 
                                     * SmartWifi y como fin la interface del nuevo equipo SmartWifi registrado en este proceso*/
                                    $objEnlacePrimerNivel = new InfoEnlace();
                                    $objEnlacePrimerNivel->setInterfaceElementoIniId($objIntElePlanFin);
                                    $objEnlacePrimerNivel->setInterfaceElementoFinId($objInterfaceElementoSmartWifi);
                                    $objEnlacePrimerNivel->setTipoMedioId($objTipoMedio);
                                    $objEnlacePrimerNivel->setTipoEnlace("PRINCIPAL");
                                    $objEnlacePrimerNivel->setEstado("Activo");
                                    $objEnlacePrimerNivel->setUsrCreacion($strUsrCreacion);
                                    $objEnlacePrimerNivel->setFeCreacion(new \DateTime('now'));
                                    $objEnlacePrimerNivel->setIpCreacion($strIpCreacion);
                                    $this->emInfraestructura->persist($objEnlacePrimerNivel);
                                    $this->emInfraestructura->flush();
                                }
                                //info_detalle_elemento gestion remota
                                $this->servicioGeneral->ingresarDetalleElemento($objElementoSmartWifi,
                                                                                "MAC WIFI",
                                                                                "MAC WIFI",
                                                                                $strMacSmartWifi,
                                                                                $strUsrCreacion,
                                                                                $strIpCreacion);
                            }
                        }

                        if($strPoductoPermitido == "S")
                        {
                            //actualizamos registro de la tarjeta micro SD en el NAF
                            $strMensajeError = str_repeat(' ', 1000);
                            $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                               ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                               ":cantidad, :pv_mensajeerror); END;";
                            $objStmt2 = $this->emNaf->getConnection()->prepare($strSql);
                            $objStmt2->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                            $objStmt2->bindParam('codigoArticulo',        $strCodigoArticuloWifi);
                            $objStmt2->bindParam('tipoArticulo',          $strTipoArticulo);
                            $objStmt2->bindParam('identificacionCliente', $strIdentificacionCliente);
                            $objStmt2->bindParam('serieCpe',              $strSerieTarjeta);
                            $objStmt2->bindParam('cantidad',              intval(1));
                            $objStmt2->bindParam('pv_mensajeerror',       $strMensajeError);
                            $objStmt2->execute();

                            if(strlen(trim($strMensajeError))>0)
                            {
                                $strMensaje = "ERROR MICRO SD NAF: ".$strMensajeError;
                                $strStatus  = 'ERROR';
                            }
                        }
                    }
                    else
                    {
                        $strMsgError = "";
                        if($strPoductoPermitido != "S")
                        {
                            $strMsgError = " Smart Wifi.";
                        }

                        $strMensaje = 'Se presentaron errores al ingresar el elemento'.$strMsgError;
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = "ERROR WIFI NAF: ".$arrayWifiNaf[0]['mensaje']; 
                    $strStatus  = 'ERROR';
                }        
            }
            if ($strStatus == 'ERROR')
            {
                $this->utilService->insertError('Telcos+', 
                                                'InfoConfirmarServicioService.ingresarElementoSmartWifi', 
                                                $strMensaje,
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );

            }
        } 
        catch (\Exception $e) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoConfirmarServicioService.ingresarElementoSmartWifi', 
                                            $e->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strStatus = 'ERROR';
        }
        
        return $strStatus;
    }
    
    /**
     * ingresarElementoNetHome
     * 
     * Función que registra un elemento NetHome
     * 
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 24-09-2018
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - strSerieNetHome             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloNetHome            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strUsrCreacion              Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - ojbServicio                 Objeto de servicio 
     *                                 - strUltimaMilla              Cadena de caracteres que indica la ultima milla del servicio procesado
     *                                 - strIpCreacion               Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - strEmpresaCod               Identificador de Empresa
     *                                 - strTipoNetHome              Tipo de nethome
     *                                 - strEsPrimeElemento          Bandera que indica si el elemento a crear es el primero del servicio   
     *                               ]
     * @return String  $status  Estado de la transaccion ejecutada
     * 
     */
    public function ingresarElementoNetHome($arrayParametros)
    {
        $strSerieNetHome          = $arrayParametros['strSerieNetHome'];
        $strModeloNetHome         = $arrayParametros['strModeloNetHome'];
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];
        $objServicio              = $arrayParametros['objServicio'];
        $strUltimaMilla           = $arrayParametros['strUltimaMilla'];
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
        $strEmpresaCod            = $arrayParametros['strEmpresaCod'];
        $strTipoNetHome           = $arrayParametros['strTipoNetHome'];
        $strEsPrimeElemento       = $arrayParametros['strEsPrimeElemento'];
        $strSecuencial            = $arrayParametros['strSecuencial'];
        $objInterfaceElementoFin  = null;
        $strStatus                = 'ERROR';
        $strMensaje               = '';
        $strTipoArticulo          = 'AF';
        $strIdentificacionCliente = "";
        try
        {
            if (is_object($objServicio))
            {
                $objServicioTecnico = $this->emComercial
                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->findOneBy(array( "servicioId" => $objServicio->getId()));
                if (is_object($objServicioTecnico))
                {
                    $strStatus = 'OK';
                    if($objServicioTecnico->getInterfaceElementoClienteId())
                    {
                        $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoClienteId();
                        $arrayParams['arrayData']                      = array();
                        $arrayParams['strBanderaReturn']               = 'INTERFACE';
                        $objInterfaceElementoFin = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoElemento')
                                                        ->getElementosNetHomeFiberByInterface($arrayParams);
                        
                    }
                }
                else
                {
                    $strMensaje = 'Se presentaron errores al recuperar información técnica del servicio.';  
                }
            }
            else
            {
                $strMensaje = 'Se presentaron errores al recuperar información del servicio.';  
            }
            
            if((!is_object($objInterfaceElementoFin) && $strEsPrimeElemento == "NO") ||
               (is_object($objInterfaceElementoFin) && $strEsPrimeElemento == "SI")
              )
            {
                $strMensaje = 'Se presentaron errores al recuperar información para crear elemento NETHOME.';  
            }

            if ($strStatus != 'ERROR')
            {
                //se procede a realizar el ingreso del elemento Smart Space y despacharlo en el NAF
                $arrayElementoNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieNetHome, 
                                                                                $strModeloNetHome, 
                                                                                "PI", 
                                                                                "ActivarServicio");
                $strStatusNaf      = $arrayElementoNaf[0]['status'];
                $strCodigoArticulo = "";
                if($strStatusNaf == "OK")
                {
                    $objInterfaceElementoNetHome = $this->servicioGeneral
                                                        ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(), 
                                                                                   $strSerieNetHome, 
                                                                                   $strModeloNetHome,
                                                                                   '-'.$strSecuencial.'-'.$strTipoNetHome, 
                                                                                   $objInterfaceElementoFin, 
                                                                                   $strUltimaMilla,
                                                                                   $objServicio, 
                                                                                   $strUsrCreacion, 
                                                                                   $strIpCreacion, 
                                                                                   $strEmpresaCod );
                    if(is_object($objInterfaceElementoNetHome))
                    {
                        $objElementoNetHome = $objInterfaceElementoNetHome->getElementoId();
                        
                        if (!is_object($objElementoNetHome))
                        {
                            throw new \Exception("No se encontro información del elemento NetHome creado");
                        }
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                              $objElementoNetHome->getNombreElemento().
                                                              ", Serie: ".
                                                              $strSerieNetHome.
                                                              ", Modelo: ".
                                                              $strModeloNetHome
                                                             );
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                        
                        //actualizamos registro en el naf wifi
                        $strMensajeError = str_repeat(' ', 1000);                                                                  
                        $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                           ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                           ":cantidad, :pv_mensajeerror); END;";
                        $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                        $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                        $objStmt->bindParam('codigoArticulo',        $strCodigoArticulo);
                        $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
                        $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                        $objStmt->bindParam('serieCpe',              $strSerieNetHome);
                        $objStmt->bindParam('cantidad',              intval(1));
                        $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
                        $objStmt->execute();

                        if(strlen(trim($strMensajeError))>0)
                        {
                            $strMensaje = "ERROR ELEMENTO NAF: ".$strMensajeError; 
                            $strStatus  = 'ERROR';
                        }
                        else
                        {
                            if ($strEsPrimeElemento == "SI")
                            {
                                $objServicioTecnico = $this->emComercial
                                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneBy(array( "servicioId" => $objServicio->getId()));
                                if (is_object($objServicioTecnico))
                                {
                                    //guardar ont en servicio tecnico
                                    $objServicioTecnico->setElementoClienteId($objElementoNetHome->getId());
                                    $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoNetHome->getId());
                                    $this->emComercial->persist($objServicioTecnico);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                    else
                    {
                        $strMensaje = 'Se presentaron errores al ingresar el elemento NetHome.'; 
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = "ERROR NAF: ".$arrayElementoNaf[0]['mensaje']; 
                    $strStatus  = 'ERROR';
                }        
            }
            if ($strStatus == 'ERROR')
            {
                $this->utilService->insertError('Telcos+', 
                                                'InfoConfirmarServicioService.ingresarElementoNetHome', 
                                                $strMensaje,
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );

            }
        } 
        catch (\Exception $e) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoConfirmarServicioService.ingresarElementoNetHome', 
                                            $e->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strStatus = 'ERROR';
        }
        
        return $strStatus;
    }
    
    /**
     * ingresarElementoNetFiber
     * 
     * Función que registra un elemento NetHome
     * 
     * @author  Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 24-09-2018
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - strSerieNetHome             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloNetHome            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strUsrCreacion              Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - ojbServicio                 Objeto de servicio 
     *                                 - strUltimaMilla              Cadena de caracteres que indica la ultima milla del servicio procesado
     *                                 - strIpCreacion               Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - strEmpresaCod               Identificador de Empresa
     *                               ]
     * @return String  $status  Estado de la transaccion ejecutada
     * 
     */
    public function ingresarElementoNetFiber($arrayParametros)
    {
        $strSerieNetFiber         = $arrayParametros['strSerieNetFiber'];
        $strModeloNetFiber        = $arrayParametros['strModeloNetFiber'];
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];
        $objServicio              = $arrayParametros['objServicio'];
        $strUltimaMilla           = $arrayParametros['strUltimaMilla'];
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
        $strEmpresaCod            = $arrayParametros['strEmpresaCod'];
        $strSecuencial            = $arrayParametros['strSecuencial'];
        $objInterfaceElementoFin  = null;
        $strStatus                = 'ERROR';
        $strMensaje               = '';
        $strTipoArticulo          = 'AF';
        $strIdentificacionCliente = "";
        try
        {
            if (is_object($objServicio))
            {
                $objServicioTecnico = $this->emComercial
                                           ->getRepository('schemaBundle:InfoServicioTecnico')
                                           ->findOneBy(array( "servicioId" => $objServicio->getId()));
                if (is_object($objServicioTecnico))
                {
                    $strStatus = 'OK';
                    if($objServicioTecnico->getInterfaceElementoClienteId())
                    {
                        $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnico->getInterfaceElementoClienteId();
                        $arrayParams['arrayData']                      = array();
                        $arrayParams['strBanderaReturn']               = 'INTERFACE';
                        $objInterfaceElementoFin = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoElemento')
                                                        ->getElementosNetHomeFiberByInterface($arrayParams);
                        
                    }
                }
                else
                {
                    $strMensaje = 'Se presentaron errores al recuperar información técnica del servicio.';  
                }
            }
            else
            {
                $strMensaje = 'Se presentaron errores al recuperar información del servicio.';  
            }
            
            if(!is_object($objInterfaceElementoFin))
            {
                $strMensaje = 'Se presentaron errores al recuperar información para crear elemento NETHOME.';  
            }

            if ($strStatus != 'ERROR')
            {
                //se procede a realizar el ingreso del elemento Smart Space y despacharlo en el NAF
                $arrayElementoNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieNetFiber, 
                                                                                $strModeloNetFiber, 
                                                                                "PI", 
                                                                                "ActivarServicio");
                $strStatusNaf      = $arrayElementoNaf[0]['status'];
                $strCodigoArticulo = "";
                if($strStatusNaf == "OK")
                {
                    $objInterfaceElementoNetFiber = $this->servicioGeneral
                                                        ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(), 
                                                                                   $strSerieNetFiber, 
                                                                                   $strModeloNetFiber,
                                                                                   '-'.$objServicio->getId().$strSecuencial.'- TRANS', 
                                                                                   $objInterfaceElementoFin, 
                                                                                   $strUltimaMilla,
                                                                                   $objServicio, 
                                                                                   $strUsrCreacion, 
                                                                                   $strIpCreacion, 
                                                                                   $strEmpresaCod );
                    if(is_object($objInterfaceElementoNetFiber))
                    {
                        $objElementoNetFiber = $objInterfaceElementoNetFiber->getElementoId();
                        
                        if (!is_object($objElementoNetFiber))
                        {
                            throw new \Exception("No se encontro información del elemento NetFiber creado");
                        }
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($objServicio);
                        $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                              $objElementoNetFiber->getNombreElemento().
                                                              ", Serie: ".
                                                              $strSerieNetFiber.
                                                              ", Modelo: ".
                                                              $strModeloNetFiber
                                                             );
                        $objServicioHistorial->setEstado($objServicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServicioHistorial);
                        $this->emComercial->flush();
                        
                        //actualizamos registro en el naf wifi
                        $strMensajeError = str_repeat(' ', 1000);                                                                  
                        $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                                           ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                                           ":cantidad, :pv_mensajeerror); END;";
                        $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                        $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
                        $objStmt->bindParam('codigoArticulo',        $strCodigoArticulo);
                        $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
                        $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                        $objStmt->bindParam('serieCpe',              $strSerieNetFiber);
                        $objStmt->bindParam('cantidad',              intval(1));
                        $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
                        $objStmt->execute();

                        if(strlen(trim($strMensajeError))>0)
                        {
                            $strMensaje = "ERROR ELEMENTO NAF: ".$strMensajeError; 
                            $strStatus  = 'ERROR';
                        }
                    }
                    else
                    {
                        $strMensaje = 'Se presentaron errores al ingresar el elemento NetFiber.'; 
                        $strStatus  = 'ERROR';
                    }
                }
                else
                {
                    $strMensaje = "ERROR NAF: ".$arrayElementoNaf[0]['mensaje']; 
                    $strStatus  = 'ERROR';
                }        
            }
            if ($strStatus == 'ERROR')
            {
                $this->utilService->insertError('Telcos+', 
                                                'InfoConfirmarServicioService.ingresarElementoNetFiber', 
                                                $strMensaje,
                                                $strUsrCreacion, 
                                                $strIpCreacion
                                               );

            }
        } 
        catch (\Exception $e) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'InfoConfirmarServicioService.ingresarElementoNetFiber', 
                                            $e->getMessage(),
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strStatus = 'ERROR';
        }
        
        return $strStatus;
    }
    
    /**
     * Funcion que sirve para realizar la confirmacion de servicio por traslados MD
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1  09-05-2016   Se agrega parametro empresa en metodo confirmarServicioPorTrasladoMd por conflictos de 
     *                            producto INTERNET DEDICADO
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.2 17-05-2021 - Se anexa validacion para que al activar un servicio de internet, se activen tambien los servicios
     *                          adicionales validando que primero se activen en konibit
     * 
     * @param Objeto $servicio
     * @param Objeto $servicioTecnico
     * @param Objeto $interfaceElemento
     * @param Objeto modeloElemento
     * @param Objeto $producto
     * @param String $usrCreacion
     * @param String $ipCreacion
     * @param String $idEmpresa
     * @param Objeto $accionObj
     * 
     * @since 1.0
     */
    public function confirmarServicioPorTrasladoMd( $servicio, 
                                                    $servicioTecnico, 
                                                    $interfaceElemento, 
                                                    $modeloElemento, 
                                                    $producto, 
                                                    $usrCreacion, 
                                                    $ipCreacion, 
                                                    $idEmpresa, 
                                                    $accionObj)
    {
        $servicioTraslado          = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRASLADO", $producto);
        $servicioTrasladoId        = $servicioTraslado->getValor();
        $servicioAnterior          = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($servicioTrasladoId);
        $servicioTecnicoAnterior   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                       ->findOneBy(array("servicioId"=>$servicioAnterior->getId()));
        $interfaceElementoAnterior = $this->emComercial->getRepository('schemaBundle:InfoInterfaceElemento')
                                                       ->find($servicioTecnicoAnterior->getInterfaceElementoId());
        
        //servicio anterior
        $servicioAnterior->setEstado("Trasladado");
        $this->emComercial->persist($servicioAnterior);
        $this->emComercial->flush();

        //punto anterior
        $puntoAnterior = $servicioAnterior->getPuntoId();
        $puntoAnterior->setEstado("Trasladado");
        $this->emComercial->persist($puntoAnterior);
        $this->emComercial->flush();
        
        //revisar si el plan anterior tiene ip
        $planDet      = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                          ->findBy(array("planId"=>$servicioAnterior->getPlanId()->getId()));
        $prodIp       = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                             ->findOneBy(array("nombreTecnico"=>"IP","empresaCod"=>$idEmpresa, "estado"=>"Activo"));
        $prodInternet = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                          ->findBy(array("nombreTecnico"=>"INTERNET","empresaCod"=>$idEmpresa, "estado"=>"Activo"));
        $flagProd=0;
        for($i=0;$i<count($planDet);$i++)
        {
            if($planDet[$i]->getProductoId() == $prodIp->getId())
            {
                $flagProd=1;
                break;
            }

            for($j=0;$j<count($prodInternet);$j++)
            {
                if($planDet[$i]->getProductoId() == $prodInternet[$j]->getId())
                {
                    $producto = $prodInternet[$j];
                    break;
                }
            }
        }
        
        if($servicioTecnico->getElementoId() != $servicioTecnicoAnterior->getElementoId()  || 
           $servicioTecnico->getInterfaceElementoId() != $servicioTecnicoAnterior->getInterfaceElementoId())
        {
            //servicio prod caract de indice cliente
            $servProdCaractIndiceCliente = $this->servicioGeneral->getServicioProductoCaracteristica($servicioAnterior, "INDICE CLIENTE", $producto);
            
            if($flagProd==0)
            {
                //no tiene ip
                $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnicoAnterior,
                                        'interfaceElemento' => $interfaceElementoAnterior,
                                        'modeloElemento'    => $modeloElemento,
                                        'spcIndiceCliente'  => $servProdCaractIndiceCliente,
                                        'login'             => $puntoAnterior->getLogin(),
                                        'spcSpid'           => "",
                                        'spcMacOnt'         => "",
                                        'idEmpresa'         => $idEmpresa
                                        );
                $respuestaArray = $this->cancelarServicio->cancelarServicioMdSinIp($arrayParametros);
                $status = $respuestaArray[0]['status'];
            }
            else
            {
                //tiene ip
                $arrayParametros = array(
                                        'servicioTecnico'   => $servicioTecnicoAnterior,
                                        'interfaceElemento' => $interfaceElementoAnterior,
                                        'modeloElemento'    => $modeloElemento,
                                        'producto'          => $producto,
                                        'login'             => $puntoAnterior->getLogin(),
                                        'idEmpresa'         => $idEmpresa,
                                        'ipCreacion'        => $ipCreacion,
                                        'usrCreacion'       => $usrCreacion
                                        );
                $respuestaArray = $this->cancelarServicio->cancelarServicioMdConIp($arrayParametros);
                $status         = $respuestaArray[0]['status'];
                
                if($status=="OK")
                {
                    //historial del servicio, para eliminar ip del elemento anterior
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicioAnterior);
                    $servicioHistorial->setObservacion("Se Elimino Ip del Elemento");
                    $servicioHistorial->setEstado($servicioAnterior->getEstado());
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                }
            }
        }
        else
        {
            //utiliza los mismos recursos
            $status = "OK";
        }
        
        if($status=="OK")
        {
            //eliminar enlace del puerto del servicio anterior
            $enlaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                   ->findOneBy(array("interfaceElementoIniId"=>$servicioTecnicoAnterior->getInterfaceElementoId(),
                                                     "interfaceElementoFinId"=>$servicioTecnicoAnterior->getInterfaceElementoClienteId()));
            if($enlaceAnterior)
            {
                $enlaceAnterior->setEstado("Eliminado");
                $this->emInfraestructura->persist($enlaceAnterior);
                $this->emInfraestructura->flush();
            }
            
            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicioAnterior);
            $servicioHistorial->setObservacion("Se Traslado el Servicio");
            $servicioHistorial->setEstado("Trasladado");
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            //----------------------------------------------------------------------------------------

            //nuevo servicio
            $servicio->setEstado("Activo");
            $this->emComercial->persist($servicio);
            $this->emComercial->flush();

            //punto nuevo
            $punto = $servicio->getPuntoId();
            $punto->setEstado("Activo");
            $this->emComercial->persist($punto);
            $this->emComercial->flush();

            //historial del servicio
            $servicioHistorialNuevo = new InfoServicioHistorial();
            $servicioHistorialNuevo->setServicioId($servicio);
            $servicioHistorialNuevo->setObservacion("Se Activo el Servicio Traslado");
            $servicioHistorialNuevo->setEstado("Activo");
            $servicioHistorialNuevo->setUsrCreacion($usrCreacion);
            $servicioHistorialNuevo->setFeCreacion(new \DateTime('now'));
            $servicioHistorialNuevo->setIpCreacion($ipCreacion);
            $servicioHistorialNuevo->setAccion($accionObj->getNombreAccion());
            $this->emComercial->persist($servicioHistorialNuevo);
            $this->emComercial->flush();

            // Realiza la activacion de servicios adicionales automaticos solo para servicios de internet
            $objPlanServicio = $servicio->getPlanId();
            $arrayProductoParam = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('VALIDA_PROD_ADICIONAL', 
                                                  'COMERCIAL', '',
                                                  'Verifica Producto Internet',
                                                  '','','','','',$idEmpresa);
            if (is_array($arrayProductoParam) && !empty($arrayProductoParam))
            {
                $objProdParametro = $arrayProductoParam[0];
            }
            if (!empty($objPlanServicio) && 
                $producto->getDescripcionProducto() == $objProdParametro['valor3'])
            {
                // Activamos los servicios adicionales
                $arrayDatosParametros = array(
                    "intIdPunto"      => $servicio->getPuntoId()->getId(),
                    "intCodEmpresa"   => $idEmpresa,
                    "strIpCreacion"   => $ipCreacion,
                    "strUserCreacion" => $usrCreacion,
                    "strAccion"       => $accionObj->getNombreAccion()
                );
                $this->activarProductosAdicionales($arrayDatosParametros);

                // Activamos los servicios incluidos si no se activaron en el origen
                $intIdPlanKon = $objPlanServicio->getId();
                if (isset($intIdPlanKon) && !empty($intIdPlanKon))
                {
                    $arrayDetPlanesKon  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                    ->getPlanIdYEstados($intIdPlanKon);       
                    if(is_array($arrayDetPlanesKon) && !empty($arrayDetPlanesKon))
                    {
                        $arrayListadoServicios = array();
                        $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                        'Lista de productos adicionales automaticos',
                                                        '','','','','',$idEmpresa);
                        foreach($arrayDetPlanesKon as $objDetPlanKon)
                        {
                            $intIdProdKon = $objDetPlanKon->getProductoId();
                            foreach($arrayListadoServicios as $objListado)
                            {
                                // Si encuentra un producto konibit procede pasar la caracteristica
                                if ($intIdProdKon == $objListado['valor1'] && $objListado['valor3'] == "SI")
                                {
                                    $objProductoKon = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->find($intIdProdKon);
                                    
                                    $arrayProductoCarac  = array(
                                        "objServicio"       => $servicio,
                                        "objProducto"       => $objProductoKon,
                                        "strUsrCreacion"    => $usrCreacion,
                                        "strCaracteristica" => "ACTIVO KONIBIT"
                                    );
                                    $arrayResProductoCarac = $this->serviceLicKaspersky
                                                                ->obtenerValorServicioProductoCaracteristica($arrayProductoCarac);
                                    $arrayProductoCaracterKon = $arrayResProductoCarac["objServicioProdCaract"];
                                    if (!empty($arrayProductoCaracterKon))
                                    {
                                        $strValorKon = $arrayProductoCaracterKon->getValor();
                                        if ($strValorKon == 'NO')
                                        {
                                            $arrayDatosParametros = array(
                                                "objServicio"     => $servicio,
                                                "intCodEmpresa"   => $idEmpresa,
                                                "strIpCreacion"   => $ipCreacion,
                                                "strUserCreacion" => $usrCreacion
                                            );
                                            $this->activarProdKonitIncluidos($arrayDatosParametros);
                                        }
                                    }
                                }
                            }
                        }    
                    }
                }
            }
            
            return "OK";
        }
        else
        {
            return "ERROR EN CANCELAR SERVICIO ANTERIOR";
        }
    }
    
    public function confirmarServicioTtco($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion,$jsonCaracteristicas, 
                                          $observacionCliente, $serieCpe, $codigoArticulo, $macCpe, $ssid, $numPc, $password, 
                                          $modoOperacion, $idEmpresa,$serNaf,$ptoNaf,$sidNaf,$usrNaf,$pswNaf,$prefijoEmpresa,
                                          $accionObj){
        /*DECLARACION VARIABLES-------------------------------------------------*/
        $status="NA";
        $mensaje="NA";
        /*----------------------------------------------------------------------*/
        
        /*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        /*----------------------------------------------------------------------*/
        
        /*LOGICA DE NEGOCIO-----------------------------------------------------*/
        try{
            if($servicio->getTipoOrden()=='R'){
                $status = $this->confirmarServicioPorReubicacion($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion,
                                                                 $accionObj);
            }
            else if($servicio->getTipoOrden()=='T'){
                $status = $this->confirmarServicioPorTraslado($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion,
                                                              $accionObj);
            }
            else if($servicio->getTipoOrden()=='N'){
                $status = $this->confirmarServicioPorNuevoTtco($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion, 
                                                               $jsonCaracteristicas, $observacionCliente, $serieCpe, $codigoArticulo, 
                                                               $macCpe, $ssid, $numPc, $password, $modoOperacion, $idEmpresa,
                                                               $serNaf,$ptoNaf,$sidNaf,$usrNaf,$pswNaf,$prefijoEmpresa,$accionObj);
            }
            
            if($status!="OK"){
                throw new \Exception($status);
            }
            else{
                $mensaje = "OK";
            }
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emNaf->getConnection()->isTransactionActive()){
                $this->emNaf->getConnection()->rollback();
            }
            $status="ERROR";
            $mensaje = $e->getMessage();
            $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        /*----------------------------------------------------------------------*/
        
        
        /*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()){
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }
        
        if ($this->emNaf->getConnection()->isTransactionActive()){
            $this->emNaf->getConnection()->commit();
        }
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emNaf->getConnection()->close();
        /*----------------------------------------------------------------------*/
        
        
        /*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal[] = array('status'=>$status, 'mensaje'=>$mensaje);
        return $respuestaFinal;
        /*----------------------------------------------------------------------*/
        
    }
    
    public function confirmarServicioPorReubicacion($servicio,$servicioTecnico,$producto,$usrCreacion, $ipCreacion, $accionObj){
        //REUBICACION
        $caracteristicaReubicacion = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "REUBICACION", "estado"=>"Activo"));
        $productoCaracteristicaReubicacion = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaReubicacion->getId()));
        $servicioProductoCaracteristicaReubicacion = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->findOneBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$productoCaracteristicaReubicacion->getId()));

        $servicioAnteriorId="";
        if($servicioProductoCaracteristicaReubicacion!=null){
            $servicioAnteriorId = $servicioProductoCaracteristicaReubicacion->getValor();
        }

        $servicioAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($servicioAnteriorId);
        $servicioTecnicoAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneBy(array("servicioId"=>$servicioAnteriorId));

        //servicio anterior
        $servicioAnterior->setEstado("Reubicado");
        $this->emComercial->persist($servicioAnterior);
        $this->emComercial->flush();

        //historial del servicio
        $servicioHistorial = new InfoServicioHistorial();
        $servicioHistorial->setServicioId($servicioAnterior);
        $servicioHistorial->setObservacion("Se Reubico el Servicio");
        $servicioHistorial->setEstado("Reubicado");
        $servicioHistorial->setUsrCreacion($usrCreacion);
        $servicioHistorial->setFeCreacion(new \DateTime('now'));
        $servicioHistorial->setIpCreacion($ipCreacion);
        $this->emComercial->persist($servicioHistorial);
        $this->emComercial->flush();
        //----------------------------------------------------------------------------------------

        //nuevo servicio
        $servicio->setEstado("Activo");
        $this->emComercial->persist($servicio);
        $this->emComercial->flush();

        //nuevo servicio tecnico - agregar cpe
        $servicioTecnico->setElementoClienteId($servicioTecnicoAnterior->getElementoClienteId());
        $servicioTecnico->setInterfaceElementoClienteId($servicioTecnicoAnterior->getInterfaceElementoClienteId());
        $this->emComercial->persist($servicioTecnico);
        $this->emComercial->flush();

        //historial del servicio
        $servicioHistorialNuevo = new InfoServicioHistorial();
        $servicioHistorialNuevo->setServicioId($servicio);
        $servicioHistorialNuevo->setObservacion("Se Activo el Servicio Reubicado");
        $servicioHistorialNuevo->setEstado("Activo");
        $servicioHistorialNuevo->setUsrCreacion($usrCreacion);
        $servicioHistorialNuevo->setFeCreacion(new \DateTime('now'));
        $servicioHistorialNuevo->setIpCreacion($ipCreacion);
        $servicioHistorialNuevo->setAccion($accionObj->getNombreAccion());
        $this->emComercial->persist($servicioHistorialNuevo);
        $this->emComercial->flush();

        return "OK";
    }
    
    /**
     * Funcion que sirve para confirmar servicio por traslado
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-05-2016  Se agrega parametro empresa en metodo confirmarServicioPorTraslado por conflictos de producto INTERNET DEDICADO
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 18-05-2016  Se agrega recuperación de operatividad del elemento previo cancelación de servicio anterior
     * 
     * @since 1.0
     * @param Objeto $servicio
     * @param Objeto servicioTecnico
     * @param Objeto $producto
     * @param String $usrCreacion
     * @param String $ipCreacion 
     * @param Objeto $accionObj
     */
    public function confirmarServicioPorTraslado($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion, $accionObj)
    {
        $caracteristicaTraslado                 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy(array( "descripcionCaracteristica" => "TRASLADO", 
                                                                                       "estado"                    => "Activo"));
        $productoCaracteristicaTraslado         = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array( "productoId"       => $producto->getId(), 
                                                                                       "caracteristicaId" => $caracteristicaTraslado->getId()));
        $servicioProductoCaracteristicaTraslado = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                       ->findOneBy(array( "servicioId"                => $servicio->getId(), 
                                                                          "productoCaracterisiticaId" => $productoCaracteristicaTraslado->getId()));
 
        $servicioAnteriorId                     = "";
        if($servicioProductoCaracteristicaTraslado!=null)
        {
            $servicioAnteriorId = $servicioProductoCaracteristicaTraslado->getValor();
        }

        $servicioAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($servicioAnteriorId);

        //servicio anterior
        $servicioAnterior->setEstado("Trasladado");
        $this->emComercial->persist($servicioAnterior);
        $this->emComercial->flush();

        //punto anterior
        $puntoAnterior = $servicioAnterior->getPuntoId();
        $puntoAnterior->setEstado("Trasladado");
        $this->emComercial->persist($puntoAnterior);
        $this->emComercial->flush();

        $servicioTecnicoAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                     ->findOneBy(array( "servicioId" => $servicioAnterior->getId()));
        //nuevo servicio tecnico - agregar cpe
        $servicioTecnico->setElementoClienteId($servicioTecnicoAnterior->getElementoClienteId());
        $servicioTecnico->setInterfaceElementoClienteId($servicioTecnicoAnterior->getInterfaceElementoClienteId());
        $this->emComercial->persist($servicioTecnico);
        $this->emComercial->flush();

        if( $servicioTecnico->getElementoId() != $servicioTecnicoAnterior->getElementoId()  || 
            $servicioTecnico->getInterfaceElementoId() != $servicioTecnicoAnterior->getInterfaceElementoId())
        {
            //RECURSOS NUEVOS -- CANCELAR SERVICIO ANTERIOR QUE SEGUIA ACTIVO, NO SE LO HABIA CANCELADO PORQUE DESPUES EL CLIENTE
            //SE QUEDABA SIN INTERNET EN EL SERVICIO ANTERIOR
            $interfaceElementoId        = $servicioTecnicoAnterior->getInterfaceElementoId();
            $interfaceElementoClienteId = $servicioTecnico->getInterfaceElementoClienteId();

            //eliminar el enlace anterior
            $enlaceAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                      ->findOneBy(array( "interfaceElementoIniId" => $interfaceElementoId));
            $enlaceAnterior->setEstado("Eliminado");
            $this->emInfraestructura->persist($enlaceAnterior);
            $this->emInfraestructura->flush();

            $interfaceElemento       = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElementoId);
            $nombreInterfaceElemento = $interfaceElemento->getNombreInterfaceElemento();
            $elementoId              = $interfaceElemento->getElementoId();

            $elemento             = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
            $modeloElemento       = $elemento->getModeloElementoId();
            $nombreModeloElemento = $modeloElemento->getNombreModeloElemento();
            $reqAprovisionamiento = $modeloElemento->getReqAprovisionamiento();


            $interfaceElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                ->find($interfaceElementoClienteId);

            //buscar ultima milla (tipo)
            $ultimaMillaId = $servicioTecnico->getUltimaMillaId();
            $ultimaMilla   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($ultimaMillaId);

            //enlace nuevo
            $enlace = new InfoEnlace();
            $enlace->setInterfaceElementoIniId($interfaceElemento);
            $enlace->setInterfaceElementoFinId($interfaceElementoCliente);
            $enlace->setTipoMedioId($ultimaMilla);
            $enlace->setTipoEnlace("PRINCIPAL");
            $enlace->setEstado("Activo");
            $enlace->setUsrCreacion($usrCreacion);
            $enlace->setFeCreacion(new \DateTime('now'));
            $enlace->setIpCreacion($ipCreacion);
            $this->emInfraestructura->persist($enlace);
            $this->emInfraestructura->flush();
            
            //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
            $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                             ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                               "detalleNombre"  => "RADIO OPERATIVO"));

            if ($entitydetalleElemento)
            {
                if ($entitydetalleElemento->getDetalleValor() == "NO")
                {
                    $reqAprovisionamiento = "NO";
                }
            }

            if($reqAprovisionamiento=="SI")
            {
                /*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScript("cancelarCliente",$modeloElemento);
                $idDocumento = $scriptArray[0]->idDocumento;
                $usuario     = $scriptArray[0]->usuario;
                $protocolo   = $scriptArray[0]->protocolo;
                /*----------------------------------------------------------------------*/

                if($idDocumento==0)
                {
                    return "NO EXISTE TAREA";
                }
                
                $caracteristicaVci = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                       ->findBy(array( "descripcionCaracteristica" => "VCI", "estado"=>"Activo"));
                $pcVci             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                       ->findBy(array( "productoId"       => $producto->getId(), 
                                                                       "caracteristicaId" => $caracteristicaVci[0]->getId()));
                $ispcVci           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                       ->findBy(array( "servicioId"                => $servicio->getId(), 
                                                                       "productoCaracterisiticaId" => $pcVci[0]->getId()));

                if(count($ispcVci)>0)
                {
                    if($ispcVci[0]->getValor()>31 && $ispcVci[0]->getValor()<=100)
                        $vciValor = "0/".$ispcVci[0]->getValor();
                    else
                        $vciValor = "0/35";
                }
                else
                {
                    $vciValor = "0/35";
                }
                
                if($nombreModeloElemento=="A2024")
                {
                    $datos        = $nombreInterfaceElemento.",1";
                    $resultadJson = $this->cancelarServicio->cancelarClienteA2024($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="A2048")
                {
                    $datos        = $nombreInterfaceElemento.",1";
                    $resultadJson = $this->cancelarServicio->cancelarClienteA2048($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="R1AD24A")
                {
                    $datos        = $nombreInterfaceElemento.",".$nombreInterfaceElemento.",".$nombreInterfaceElemento.".1,".$nombreInterfaceElemento.
                                    ".1,".$nombreInterfaceElemento.".1,".$nombreInterfaceElemento;
                    $resultadJson = $this->cancelarServicio->cancelarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="R1AD48A")
                {
                    $datos        = $nombreInterfaceElemento.",".$nombreInterfaceElemento.",".$nombreInterfaceElemento.".1,".$nombreInterfaceElemento.
                                    ".1,".$nombreInterfaceElemento.".1,".$nombreInterfaceElemento;
                    $resultadJson = $this->cancelarServicio->cancelarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="6524")
                {
                    $datos        = $nombreInterfaceElemento.",".$nombreInterfaceElemento.",".$nombreInterfaceElemento;
                    $resultadJson = $this->cancelarServicio->cancelarCliente6524($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="7224")
                {
                    $datos        = $nombreInterfaceElemento.",".$nombreInterfaceElemento.",".$nombreInterfaceElemento.",".$nombreInterfaceElemento.
                                    ",".$nombreInterfaceElemento;
                    $resultadJson = $this->cancelarServicio->cancelarCliente7224($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="MEA1")
                {
                    $datos        = $nombreInterfaceElemento.",".$vciValor.",".$vciValor;
                    $resultadJson = $this->cancelarServicio->cancelarClienteMea1($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="MEA3")
                {
                    $datos        = $nombreInterfaceElemento.",".$vciValor.",".$vciValor;
                    $resultadJson = $this->cancelarServicio->cancelarClienteMea3($idDocumento, $usuario, $protocolo, $elementoId, $datos);
                    $status       = $resultadJson->status;
                }
                else if($nombreModeloElemento=="IPTECOM" || $nombreModeloElemento=="411AH" || $nombreModeloElemento=="433AH")
                {

                    $puntoId = $servicio->getPuntoId();
                    $punto   = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId->getId());
                    $login   = $punto->getLogin();

                    $producto           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array( "descripcionProducto" => "INTERNET DEDICADO", 
                                                                            "estado"              => "Activo",
                                                                            "empresaCod"          => '18'));
                    $caracteristica     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                            ->findBy(array( "descripcionCaracteristica" => "MAC"));
                    $prodCaract         = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findBy(array( "productoId"       => $producto[0]->getId(), 
                                                                            "caracteristicaId" => $caracteristica[0]->getId()));
                    $servicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->findBy(array( "servicioId"                => $servicio->getId(), 
                                                                            "productoCaracterisiticaId" => $prodCaract[0]->getId()));
                    $mac                = $servicioProdCaract[0]->getValor();

                    /*OBTENER SCRIPT--------------------------------------------------------*/
                    $scriptArray  = $this->servicioGeneral->obtenerArregloScript("encontrarNumbersMac",$modeloElemento);
                    $idDocumento1 = $scriptArray[0]->idDocumento;
                    $usuario1     = $scriptArray[0]->usuario;
                    $protocolo1   = $scriptArray[0]->protocolo;
                    /*----------------------------------------------------------------------*/
                    
                    //numbers de la mac
                    $datos2        = $mac;
                    $resultadJson2 = $this->cancelarServicio->cancelarClienteIPTECOM($idDocumento1, $usuario1, "radio", $elementoId, $datos2);
                    $resultado     = $resultadJson2->mensaje;
                    $numbers       = explode("\n", $resultado);
                    $flag          = 0;

                    for($i=0;$i<count($numbers);$i++)
                    {
                        if(stristr($numbers[$i], $mac) === FALSE) 
                        {

                        }
                        else
                        {

                            if($nombreModeloElemento=="411AH")
                            {
                                $numero = explode(" ", $numbers[$i]);
                            }
                            else
                            {
                                $numero = explode(" ", $numbers[$i-1]);
                            }
                            $flag = 1;
                            break;
                        }
                    }
                    
                    if($flag==0)
                    {
                        return "ERROR ELEMENTO";
                    }
                    //base
                    if($nombreModeloElemento=="411AH")
                    {
                        $datos = $mac.",".$numero[0];
                    }
                    else
                    {
                        $datos = $mac.",".$numero[1];
                    }
                    
                    $resultadJson1 = $this->cancelarServicio->cancelarClienteIPTECOM($idDocumento, $usuario, "radio", $elementoId, $datos);
                    
                    /*SERVIDOR RADIUS-------------------------------------------*/
                    $datos1           = $login;
                    $elementoIdRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->findOneBy(array( "nombreElemento" => "ttcoradius"));
                    
                    /*OBTENER SCRIPT--------------------------------------------------------*/
                    $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("cancelarClienteRadius",
                                                                                       $elementoIdRadius->getModeloElementoId());
                    $idDocumento = $scriptArray[0]->idDocumento;
                    $usuario     = $scriptArray[0]->usuario;
                    $protocolo   = $scriptArray[0]->protocolo;
                    /*----------------------------------------------------------------------*/

                    $resultadJson = $this->cancelarServicio->cancelarClienteRADIUS( $idDocumento, 
                                                                                    $usuario, 
                                                                                    "servidor", 
                                                                                    $elementoIdRadius->getId(), 
                                                                                    $datos1);

                    if($resultadJson->status=="OK" && $resultadJson1->status=="OK")
                    {
                        $status="OK";
                    }
                    else
                    {
                        $status = "ERROR";
                    }
                }
                
            }
            else
            {
                $status = "OK";
            }
            
            
            if($status!="OK")
            {
                return "ERROR EN CANCELAR SERVICIO ANTERIOR";
            }
        }

        //historial del servicio
        $servicioHistorial = new InfoServicioHistorial();
        $servicioHistorial->setServicioId($servicioAnterior);
        $servicioHistorial->setObservacion("Se Traslado el Servicio");
        $servicioHistorial->setEstado("Trasladado");
        $servicioHistorial->setUsrCreacion($usrCreacion);
        $servicioHistorial->setFeCreacion(new \DateTime('now'));
        $servicioHistorial->setIpCreacion($ipCreacion);
        $this->emComercial->persist($servicioHistorial);
        $this->emComercial->flush();
        //----------------------------------------------------------------------------------------

        //nuevo servicio
        $servicio->setEstado("Activo");
        $this->emComercial->persist($servicio);
        $this->emComercial->flush();

        //punto nuevo
        $punto = $servicio->getPuntoId();
        $punto->setEstado("Activo");
        $this->emComercial->persist($punto);
        $this->emComercial->flush();

        //historial del servicio
        $servicioHistorialNuevo = new InfoServicioHistorial();
        $servicioHistorialNuevo->setServicioId($servicio);
        $servicioHistorialNuevo->setObservacion("Se Activo el Servicio Traslado");
        $servicioHistorialNuevo->setEstado("Activo");
        $servicioHistorialNuevo->setUsrCreacion($usrCreacion);
        $servicioHistorialNuevo->setFeCreacion(new \DateTime('now'));
        $servicioHistorialNuevo->setIpCreacion($ipCreacion);
        $servicioHistorialNuevo->setAccion($accionObj->getNombreAccion());
        $this->emComercial->persist($servicioHistorialNuevo);
        $this->emComercial->flush();

        return "OK";
    }
    
    public function confirmarServicioPorNuevoTtco($servicio, $servicioTecnico, $producto, $usrCreacion, $ipCreacion, 
                                                  $jsonCaracteristicas, $observacionCliente, $serieCpe, $codigoArticulo, 
                                                  $macCpe, $ssid, $numPc, $password, $modoOperacion, $idEmpresa,
                                                  $serNaf,$ptoNaf,$sidNaf,$usrNaf,$pswNaf,$prefijoEmpresa, $accionObj){
        $interfaceElementoId = $servicioTecnico->getInterfaceElementoId();
        $interfaceElemento= $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElementoId);
        $elementoId = $servicioTecnico->getElementoId();
        $elemento= $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
        $ultimaMilla = $servicioTecnico->getUltimaMillaId();
        $punto = $servicio->getPuntoId();

        $personaEmpresaRol = $punto->getPersonaEmpresaRolId();
        $persona = $personaEmpresaRol->getPersonaId();

        //----------------------------------------------------------------------
        //validar que al menos escojan una ip
        $json_caracteristicas = json_decode($jsonCaracteristicas);
        $arrayCaracteristicas= $json_caracteristicas->caracteristicas;

        $ipCpeFlag=0;
        for($i=0;$i<count($arrayCaracteristicas);$i++){
            $ipCpeFlag = $arrayCaracteristicas[$i]->ipCpe;

            if($ipCpeFlag==1){
                $ipCpe = $arrayCaracteristicas[$i]->ip;
                break;
            }

        }

        if($ipCpeFlag==0){
            return "NO IP CPE";
        }
        
        //MAC-------------------------------------------------------------------
        $caracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                               ->findOneBy(array( "descripcionCaracteristica" => "MAC", "estado"=>"Activo"));
        $productoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                            ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaMac->getId()));
        $servicioProductoCaracteristicaMac = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                            ->findOneBy(array("servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$productoCaracteristicaMac->getId()));

        if($servicioProductoCaracteristicaMac!=null){
            $macValor = $servicioProductoCaracteristicaMac->getValor();

            if($macValor!=$macCpe){
                $servicioProductoCaracteristicaMac->setValor($macCpe);
                $this->emComercial->persist($servicioProductoCaracteristicaMac);
                $this->emComercial->flush();
            }
        }
        else{
            $servicioProductoCaracteristicaMac = new InfoServicioProdCaract();
            $servicioProductoCaracteristicaMac->setServicioId($servicio->getId());
            $servicioProductoCaracteristicaMac->setProductoCaracterisiticaId($productoCaracteristicaMac->getId());
            $servicioProductoCaracteristicaMac->setValor($macCpe);
            $servicioProductoCaracteristicaMac->setEstado("Activo");
            $servicioProductoCaracteristicaMac->setUsrCreacion($usrCreacion);
            $servicioProductoCaracteristicaMac->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($servicioProductoCaracteristicaMac);
            $this->emComercial->flush();
        }
        //----------------------------------------------------------------------

        //SSID------------------------------------------------------------------
        if($ssid!=""){
            $caracteristicaSsid = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array( "descripcionCaracteristica" => "SSID", "estado"=>"Activo"));
            $productoCaracteristicaSsid = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                    ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaSsid->getId()));

            $servicioProductoCaracteristicaSsid = new InfoServicioProdCaract();
            $servicioProductoCaracteristicaSsid->setServicioId($servicio->getId());
            $servicioProductoCaracteristicaSsid->setProductoCaracterisiticaId($productoCaracteristicaSsid->getId());
            $servicioProductoCaracteristicaSsid->setValor($ssid);
            $servicioProductoCaracteristicaSsid->setEstado("Activo");
            $servicioProductoCaracteristicaSsid->setUsrCreacion($usrCreacion);
            $servicioProductoCaracteristicaSsid->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($servicioProductoCaracteristicaSsid);
            $this->emComercial->flush();
        }
        //----------------------------------------------------------------------

        //----------------------------------------------------------------------
        //PASSWORD SSID
        if($password!=""){
            $caracteristicaPass = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "PASSWORD SSID", "estado"=>"Activo"));
            $productoCaracteristicaPass = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaPass->getId()));

            $servicioProductoCaracteristicaPass = new InfoServicioProdCaract();
            $servicioProductoCaracteristicaPass->setServicioId($servicio->getId());
            $servicioProductoCaracteristicaPass->setProductoCaracterisiticaId($productoCaracteristicaPass->getId());
            $servicioProductoCaracteristicaPass->setValor($password);
            $servicioProductoCaracteristicaPass->setEstado("Activo");
            $servicioProductoCaracteristicaPass->setUsrCreacion($usrCreacion);
            $servicioProductoCaracteristicaPass->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($servicioProductoCaracteristicaPass);
            $this->emComercial->flush();
        }
        //----------------------------------------------------------------------

        //----------------------------------------------------------------------
        //NUMERO PC
        if($numPc!=""){
            $caracteristicaNumPc = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array( "descripcionCaracteristica" => "NUMERO PC", "estado"=>"Activo"));
            $productoCaracteristicaNumPc = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                            ->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaNumPc->getId()));

            $servicioProductoCaracteristicaNumPc = new InfoServicioProdCaract();
            $servicioProductoCaracteristicaNumPc->setServicioId($servicio->getId());
            $servicioProductoCaracteristicaNumPc->setProductoCaracterisiticaId($productoCaracteristicaNumPc->getId());
            $servicioProductoCaracteristicaNumPc->setValor($numPc);
            $servicioProductoCaracteristicaNumPc->setEstado("Activo");
            $servicioProductoCaracteristicaNumPc->setUsrCreacion($usrCreacion);
            $servicioProductoCaracteristicaNumPc->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($servicioProductoCaracteristicaNumPc);
            $this->emComercial->flush();
        }
        //----------------------------------------------------------------------

        //----------------------------------------------------------------------
        //MODO OPERACION
        if($modoOperacion!=""){
            //$this->emComercial->getConnection()->beginTransaction();
            $caracteristicaModo = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "MODO OPERACION", "estado"=>"Activo"));
            $productoCaracteristicaModo = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracteristicaModo->getId()));

            $servicioProductoCaracteristicaModo = new InfoServicioProdCaract();
            $servicioProductoCaracteristicaModo->setServicioId($servicio->getId());
            $servicioProductoCaracteristicaModo->setProductoCaracterisiticaId($productoCaracteristicaModo->getId());
            $servicioProductoCaracteristicaModo->setValor($modoOperacion);
            $servicioProductoCaracteristicaModo->setEstado("Activo");
            $servicioProductoCaracteristicaModo->setUsrCreacion($usrCreacion);
            $servicioProductoCaracteristicaModo->setFeCreacion(new \DateTime('now'));
            $this->emComercial->persist($servicioProductoCaracteristicaModo);
            $this->emComercial->flush();
        }
        //----------------------------------------------------------------------
        
        //----------------------------------------------------------------------
        $caracIpLan= $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "IP LAN", "estado"=>"Activo"));
        $caracMascaraLan= $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "MASCARA LAN", "estado"=>"Activo"));
        $caracGatewayLan= $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(array( "descripcionCaracteristica" => "GATEWAY LAN", "estado"=>"Activo"));
        $productoCaracteristicaIpLan = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracIpLan->getId()));
        $productoCaracteristicaMascaraLan = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracMascaraLan->getId()));
        $productoCaracteristicaGatewayLan = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')->findOneBy(array( "productoId" => $producto->getId(), "caracteristicaId"=>$caracGatewayLan->getId()));
        
        //IPS DEL SERVICIO (MONITOREO, WAN)
        for($i=0;$i<count($arrayCaracteristicas);$i++){
            $tipoIp = $arrayCaracteristicas[$i]->tipo;
            $ip = $arrayCaracteristicas[$i]->ip;
            $mascara = $arrayCaracteristicas[$i]->mascara;
            $gateway = $arrayCaracteristicas[$i]->gateway;

            if($tipoIp=="MONITOREO" || $tipoIp=="WAN"){
                if($arrayCaracteristicas[$i]->id!=null || $arrayCaracteristicas[$i]->id!=""){
                    $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->find($arrayCaracteristicas[$i]->id);

                    if($infoIp->getIp() != $ip || $infoIp->getMascara() != $mascara || $infoIp->getGateway() != $gateway){
                        $infoIp->setEstado("Eliminado");
                        $this->emInfraestructura->persist($infoIp);
                        $this->emInfraestructura->flush();
                    }
                    else{
                        $infoIp = new InfoIp();
                        $infoIp->setIp($ip);
                        $infoIp->setMascara($mascara);
                        $infoIp->setGateway($gateway);
                        $infoIp->setTipoIp($tipoIp);
                        $infoIp->setVersionIp("IPV4");
                        $infoIp->setEstado("Activo");
                        $infoIp->setServicioId($servicio->getId());
                        $infoIp->setUsrCreacion($usrCreacion);
                        $infoIp->setFeCreacion(new \DateTime('now'));
                        $infoIp->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($infoIp);
                        $this->emInfraestructura->flush();
                    }
                }
                else{
                    $infoIp = new InfoIp();
                    $infoIp->setIp($ip);
                    $infoIp->setMascara($mascara);
                    $infoIp->setGateway($gateway);
                    $infoIp->setTipoIp($tipoIp);
                    $infoIp->setVersionIp("IPV4");
                    $infoIp->setEstado("Activo");
                    $infoIp->setServicioId($servicio->getId());
                    $infoIp->setUsrCreacion($usrCreacion);
                    $infoIp->setFeCreacion(new \DateTime('now'));
                    $infoIp->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($infoIp);
                    $this->emInfraestructura->flush();
                }
            }


        }
        
        //IP LAN------------------------------------------------------------
        $spcIpLanBase = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                             ->findBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$productoCaracteristicaIpLan->getId()));

        for($i=0;$i<count($spcIpLanBase);$i++){
            $ipLanBase = $spcIpLanBase[$i];
            $flag=0;//0 no encontrado - 1 encontrado

            for($j=0;$j<count($arrayCaracteristicas);$j++){
                $tipo=$arrayCaracteristicas[$j]->tipo;

                if($tipo=="LAN"){
                    $ipLanInterface=$arrayCaracteristicas[$j]->ip;

                    if($ipLanBase->getValor()==$ipLanInterface){
                        $flag=1;
                        break;
                    }
                }
            }

            if($flag==0){
                $ipLanBase->setEstado("Eliminado");
                $this->emComercial->persist($ipLanBase);
                $this->emComercial->flush();
            }
        }

        for($i=0;$i<count($arrayCaracteristicas);$i++){
            $tipo = $arrayCaracteristicas[$i]->tipo;
            $flag=0;
            if($tipo=="LAN"){
                $ipCpeFlag = $arrayCaracteristicas[$i]->ipCpe;
                if($ipCpeFlag==1){
                    $ipCpe = $arrayCaracteristicas[$i]->ip;
                }

                $ipLanInterface = $arrayCaracteristicas[$i]->ip;

                for($j=0;$j<count($spcIpLanBase);$j++){
                    $ipLanBase = $spcIpLanBase[$j]->getValor();

                    if($ipLanInterface==$ipLanBase){
                        $flag=1;
                        break;
                    }
                }

                if($flag==0){
                    $spcIpLan1 = new InfoServicioProdCaract();
                    $spcIpLan1->setServicioId($servicio->getId());
                    $spcIpLan1->setProductoCaracterisiticaId($productoCaracteristicaIpLan->getId());
                    $spcIpLan1->setValor($ipLanInterface);
                    $spcIpLan1->setEstado("Activo");
                    $this->emComercial->persist($spcIpLan1);
                    $this->emComercial->flush();
                }
            }
        }
        //IP LAN------------------------------------------------------------
        
        //MASCARA LAN------------------------------------------------------------
        $spcMascaraLanBase = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$productoCaracteristicaMascaraLan->getId()));

        for($i=0;$i<count($spcMascaraLanBase);$i++){
            $mascaraLanBase = $spcMascaraLanBase[$i];
            $flag=0;//0 no encontrado - 1 encontrado

            for($j=0;$j<count($arrayCaracteristicas);$j++){
                $tipo=$arrayCaracteristicas[$j]->tipo;

                if($tipo=="LAN"){
                    $mascaraLanInterface=$arrayCaracteristicas[$j]->mascara;

                    if($mascaraLanBase->getValor()==$mascaraLanInterface){
                        $flag=1;
                        break;
                    }
                }
            }

            if($flag==0){
                $mascaraLanBase->setEstado("Eliminado");
                $this->emComercial->persist($mascaraLanBase);
                $this->emComercial->flush();
            }
        }

        for($i=0;$i<count($arrayCaracteristicas);$i++){
            $tipo = $arrayCaracteristicas[$i]->tipo;
            $flag=0;
            if($tipo=="LAN"){
                $mascaraLanInterface = $arrayCaracteristicas[$i]->mascara;

                for($j=0;$j<count($spcMascaraLanBase);$j++){
                    $mascaraLanBase = $spcMascaraLanBase[$j]->getValor();

                    if($mascaraLanInterface==$mascaraLanBase){
                        $flag=1;
                        break;
                    }
                }

                if($flag==0){
                    $spcmascaraLan1 = new InfoServicioProdCaract();
                    $spcmascaraLan1->setServicioId($servicio->getId());
                    $spcmascaraLan1->setProductoCaracterisiticaId($productoCaracteristicaMascaraLan->getId());
                    $spcmascaraLan1->setValor($mascaraLanInterface);
                    $spcmascaraLan1->setEstado("Activo");
                    $this->emComercial->persist($spcmascaraLan1);
                    $this->emComercial->flush();
                }
            }
        }
        //MASCARA LAN------------------------------------------------------------
        
        //gateway LAN------------------------------------------------------------
        $spcGatewayLanBase = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')->findBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$productoCaracteristicaGatewayLan->getId()));

        for($i=0;$i<count($spcGatewayLanBase);$i++){
            $gatewayLanBase = $spcGatewayLanBase[$i];
            $flag=0;//0 no encontrado - 1 encontrado

            for($j=0;$j<count($arrayCaracteristicas);$j++){
                $tipo=$arrayCaracteristicas[$j]->tipo;

                if($tipo=="LAN"){
                    $gatewayLanInterface=$arrayCaracteristicas[$j]->gateway;

                    if($gatewayLanBase->getValor()==$gatewayLanInterface){
                        $flag=1;
                        break;
                    }
                }
            }

            if($flag==0){
                $gatewayLanBase->setEstado("Eliminado");
                $this->emComercial->persist($gatewayLanBase);
                $this->emComercial->flush();
            }
        }

        for($i=0;$i<count($arrayCaracteristicas);$i++){
            $tipo = $arrayCaracteristicas[$i]->tipo;
            $flag=0;
            if($tipo=="LAN"){
                $gatewayLanInterface = $arrayCaracteristicas[$i]->gateway;

                for($j=0;$j<count($spcGatewayLanBase);$j++){
                    $gatewayLanBase = $spcGatewayLanBase[$j]->getValor();

                    if($gatewayLanInterface==$gatewayLanBase){
                        $flag=1;
                        break;
                    }
                }

                if($flag==0){
                    $spcgatewayLan1 = new InfoServicioProdCaract();
                    $spcgatewayLan1->setServicioId($servicio->getId());
                    $spcgatewayLan1->setProductoCaracterisiticaId($productoCaracteristicaGatewayLan->getId());
                    $spcgatewayLan1->setValor($gatewayLanInterface);
                    $spcgatewayLan1->setEstado("Activo");
                    $this->emComercial->persist($spcgatewayLan1);
                    $this->emComercial->flush();
                }
            }
        }
        //GATEWAY LAN------------------------------------------------------------
        
        //----------------------------------------------------------------------
        //grabamos elemento en telcos
        $modeloElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                    ->findBy(array( "nombreModeloElemento" => $codigoArticulo, "estado"=>"Activo"));
        
        if(count($modeloElementoCpe)<=0){
            return "NO EXISTE PRODUCTO";
        }
        
        $elementoCpe  = new InfoElemento();
        $elementoCpe->setNombreElemento($servicio->getPuntoId()->getLogin()."-cpe");
        $elementoCpe->setDescripcionElemento("dispositivo ttco");
        $elementoCpe->setModeloElementoId($modeloElementoCpe[0]);
        $elementoCpe->setSerieFisica($serieCpe);
        $elementoCpe->setEstado("Activo");
        $elementoCpe->setUsrResponsable($usrCreacion);
        $elementoCpe->setUsrCreacion($usrCreacion);
        $elementoCpe->setFeCreacion(new \DateTime('now'));
        $elementoCpe->setIpCreacion($ipCreacion);       
        $this->emInfraestructura->persist($elementoCpe);
        $this->emInfraestructura->flush(); 

        //buscar el interface Modelo
        $interfaceModelo = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                ->findBy(array( "modeloElementoId" =>$modeloElementoCpe[0]->getId()));
        foreach($interfaceModelo as $im){
            $cantidadInterfaces = $im->getCantidadInterface();
            $formato = $im->getFormatoInterface();

            for($i=1;$i<=$cantidadInterfaces;$i++){
                $interfaceCpe = new InfoInterfaceElemento();

                $format = explode("?", $formato);
                $nombreInterfaceElemento = $format[0].$i;

                $interfaceCpe->setNombreInterfaceElemento($nombreInterfaceElemento);
                $interfaceCpe->setElementoId($elementoCpe);
                $interfaceCpe->setEstado("not connect");
                $interfaceCpe->setUsrCreacion($usrCreacion);
                $interfaceCpe->setFeCreacion(new \DateTime('now'));
                $interfaceCpe->setIpCreacion($ipCreacion);
                $this->emInfraestructura->persist($interfaceCpe);
                $this->emInfraestructura->flush();

                if($elemento->getModeloElementoId()->getNombreModeloElemento()!="TERCERIZADO"){
                    if($i==1){

                        $enlace1 = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                           ->findBy(array( "interfaceElementoIniId" =>$interfaceElementoId));
                        if(count($enlace1)>0){
                            for($j=0;$j<count($enlace1);$j++){
                                $enlace1[$j]->setEstado("Eliminado");
                                $this->emInfraestructura->persist($enlace1[$j]);
                                $this->emInfraestructura->flush();
                            }
                        }

                        $enlace = new InfoEnlace();
                        $enlace->setInterfaceElementoIniId($interfaceElemento);
                        $enlace->setInterfaceElementoFinId($interfaceCpe);
                        $enlace->setTipoMedioId($this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')->find($ultimaMilla));
                        $enlace->setTipoEnlace("PRINCIPAL");
                        $enlace->setEstado("Activo");
                        $enlace->setUsrCreacion($usrCreacion);
                        $enlace->setFeCreacion(new \DateTime('now'));
                        $enlace->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($enlace);
                        $this->emInfraestructura->flush();
                    }
                }

            }
        }

        //DATOS PARA SERVICIO TECNICO
        $elementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                ->findOneBy(array( "nombreElemento" =>$servicio->getPuntoId()->getLogin()."-cpe", "serieFisica"=>$serieCpe));
        $interfaceCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                    ->findOneBy(array( "elementoId" =>$elementoCliente));

        //ip elemento
        $ipElemento = new InfoIp();
        $ipElemento->setElementoId($elementoCliente->getId());
        if($ultimaMilla=="RADIO"){
            $infoIp = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                              ->findOneBy(array( "servicioId" =>$servicio->getId(), "tipoIp"=>"RADIO"));
            $ipCpe = $infoIp->getIp();
        }
        $ipElemento->setIp($ipCpe);
        $ipElemento->setVersionIp("IPV4");
        $ipElemento->setUsrCreacion($usrCreacion);
        $ipElemento->setFeCreacion(new \DateTime('now'));
        $ipElemento->setIpCreacion($ipCreacion);
        $ipElemento->setEstado("Activo");
        $this->emInfraestructura->persist($ipElemento);
        $this->emInfraestructura->flush(); 

        //historial elemento
        $historialElemento = new InfoHistorialElemento();
        $historialElemento->setElementoId($elementoCpe);
        $historialElemento->setEstadoElemento("Activo");
        $historialElemento->setObservacion("Se ingreso un cpe");
        $historialElemento->setUsrCreacion($usrCreacion);
        $historialElemento->setFeCreacion(new \DateTime('now'));
        $historialElemento->setIpCreacion($ipCreacion);
        $this->emInfraestructura->persist($historialElemento);
        $this->emInfraestructura->flush(); 

        //info ubicacion
        $sector = $punto->getSectorId();
        $parroquia = $sector->getParroquiaId();
        $parroquiaObj = $this->emInfraestructura->find('schemaBundle:AdmiParroquia', $parroquia->getId());

        $ubicacionElemento = new InfoUbicacion();
        if($punto->getLatitud()==null){
            $ubicacionElemento->setLatitudUbicacion(1);
        }
        else{
            $ubicacionElemento->setLatitudUbicacion($punto->getLatitud());
        }

        if($punto->getLongitud()==null){
            $ubicacionElemento->setLongitudUbicacion(1);
        }
        else{
            $ubicacionElemento->setLongitudUbicacion($punto->getLongitud());
        }


        $ubicacionElemento->setDireccionUbicacion($punto->getDireccion());
        $ubicacionElemento->setAlturaSnm(1.0);
        $ubicacionElemento->setParroquiaId($parroquiaObj);
        $ubicacionElemento->setUsrCreacion($usrCreacion);
        $ubicacionElemento->setFeCreacion(new \DateTime('now'));
        $ubicacionElemento->setIpCreacion($ipCreacion);
        $this->emInfraestructura->persist($ubicacionElemento);
        $this->emInfraestructura->flush(); 

        //empresa elemento ubicacion
        $empresaElementoUbica = new InfoEmpresaElementoUbica();
        $empresaElementoUbica->setEmpresaCod($idEmpresa);
        $empresaElementoUbica->setElementoId($elementoCpe);
        $empresaElementoUbica->setUbicacionId($ubicacionElemento);
        $empresaElementoUbica->setUsrCreacion($usrCreacion);
        $empresaElementoUbica->setFeCreacion(new \DateTime('now'));
        $empresaElementoUbica->setIpCreacion($ipCreacion);
        $this->emInfraestructura->persist($empresaElementoUbica);
        $this->emInfraestructura->flush(); 

        //empresa elemento
        $empresaElemento = new InfoEmpresaElemento();
        $empresaElemento->setElementoId($elementoCpe);
        $empresaElemento->setEmpresaCod($idEmpresa);
        $empresaElemento->setEstado("Activo");
        $empresaElemento->setUsrCreacion($usrCreacion);
        $empresaElemento->setIpCreacion($ipCreacion);
        $empresaElemento->setFeCreacion(new \DateTime('now'));
        $this->emInfraestructura->persist($empresaElemento);
        $this->emInfraestructura->flush();

        $interfaceElemento->setEstado("connected");
        $this->emInfraestructura->persist($interfaceElemento);
        $this->emInfraestructura->flush(); 

        $status="OK";
        $verificarActivacion="OK";
        $tipoArticulo= "AF";
        $identificacionCliente="";
        if($status=="OK" && $verificarActivacion=="OK"){
            //actualizamos registro en el naf ont
            $pv_mensajeerror = str_repeat(' ', 1000);                                                                    
            $sql = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
            . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
            . ":cantidad, :pv_mensajeerror); END;";
            $stmt = $this->emNaf->getConnection()->prepare($sql);
            $stmt->bindParam('codigoEmpresaNaf', $idEmpresa);
            $stmt->bindParam('codigoArticulo', $codigoArticulo);
            $stmt->bindParam('tipoArticulo',$tipoArticulo);
            $stmt->bindParam('identificacionCliente', $identificacionCliente);
            $stmt->bindParam('serieCpe', $serieCpe);
            $stmt->bindParam('cantidad', intval(1));
            $stmt->bindParam('pv_mensajeerror', $pv_mensajeerror);
            $stmt->execute();

            if(strlen(trim($pv_mensajeerror))>0)
            {
                $result = "ERROR NAF: ".$pv_mensajeerror;
                return $result;
            }
            
            $servicio->setEstado("Activo");

            if($observacionCliente!=""){
                $servicio->setObservacion($observacionCliente);    
            }

            $punto->setEstado("Activo");
            $this->emComercial->persist($punto);
            $this->emComercial->flush();

            $this->emComercial->persist($servicio);
            $this->emComercial->flush();

            $servicioTecnico->setElementoClienteId($elementoCliente->getId());
            $servicioTecnico->setInterfaceElementoClienteId($interfaceCliente->getId());

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion("Se Confirmo el Servicio");
            $servicioHistorial->setEstado("Activo");
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $servicioHistorial->setAccion($accionObj->getNombreAccion());
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();
            

            $result = "OK";

        }
        else if($status=="ERROR"){
            $result = "ERROR";
        }
        else{
            $result = "ERROR";
        }
        return $result;
    }
    
    public function verificarActivacion($idDocumento, $usuario, $protocolo, $servicioTecnico, $datos){
        if($this->host=="localhost")
        {
            $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".$this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".$servicioTecnico->getElementoId()."' '".$datos."' '".$this->pathParameters."'";
        }
        else
        {
            $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".$this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".$servicioTecnico->getElementoId()."' '".$datos."'";
        }
        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    /**
     * Función que sirve para realizar la llamada a la correspondiente plantilla de envío de mail con los parámetros enviados
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-09-2016 Se agregan como destinatarios a los correos electrónicos asociados al punto y a los correos de los contactos de tipo
     *                         Contacto Tecnico y Contacto Notificacion 
     * 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 13-06-2017 Se modifican los destinatarios para la plantilla "ACTIVARSERVICIO", ya que ésta sólo debe ser visualizada por los
     *                         empleados y se agrega otra plantilla "ACTIVARSERVCLI" que será enviada a las formas de contacto y a los contactos 
     *                         técnicos y contactos notificación del punto 
     * 
     * @author Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 21-01-2018
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.4 13-10-2020 -Se agrega flujo de envio de notificaciones para productos Paramount, Noggin y Fox dentro de un plan.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.5 07-12-2020 -Se Modifica del metodo determinarProducto() para los productos Paramount y Noggin
     *                         -Se agrega envio de parametros en los metodos notificacion sms y correo para los producto paramount y noggin
     * 
     */
    public function envioMailConfirmarServicio($arrayParametros)
    {
        try
        {
            $strNombreProductoOplan     = '';
            $strLoginPuntoCliente       = '';
            $strDireccionPuntoCliente   = '';
            $strCliente                 = '';
            $strTipo                    = '';
            $strNombreJurisdiccion      = '';
            $strEstadoServicio          = '';
            $strFechaCreacionServicio   = '';
            $strTipoOrden               = '';
            $elemento                   = null;
            $strLoginOrigenTraslado     = '';
            $strLoginAuxOrigenTraslado  = '';
            $servicio                   = $arrayParametros["servicio"];
            $observacionActivarServicio = $arrayParametros["observacionActivarServicio"] ? $arrayParametros["observacionActivarServicio"] : '';
            $prefijoEmpresa             = $arrayParametros["prefijoEmpresa"];
            $empleadoSesion             = $arrayParametros["empleadoSesion"];
            $idEmpresa                  = $arrayParametros["idEmpresa"];
            
            $equipo                     = array();
            $to                         = array();
            $arrayDestinatariosPunto    = array();
            
            if($servicio)
            {
                $strEstadoServicio          = $servicio->getEstado();
                $objProducto                = $servicio->getProductoId();
                $objPlan                    = $servicio->getPlanId();
                $objPunto                   = $servicio->getPuntoId();
                $strFechaCreacionServicio   = strval(date_format($servicio->getFeCreacion(), "d-m-Y"));
                $strTipoOrden               = "";
                
                if($servicio->getTipoOrden())
                {
                    $ordenServicio  = $servicio->getTipoOrden();
                    if($ordenServicio=='R')
                    {
                        $strTipoOrden   = "Reubicación";
                    }
                    else if($ordenServicio=='T')
                    {
                        $strTipoOrden   = "Traslado";
                    }
                    else if($ordenServicio=='N')
                    {
                        $strTipoOrden   = "Nueva";
                    }
                    else if($ordenServicio=='C')
                    {
                        $strTipoOrden   = "Cambio Tipo Medio";
                    }
                }
                
                $servicioTecnico                        = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->findOneBy(array( "servicioId" => $servicio->getId()));
                
                if($servicioTecnico)
                {
                    $interfaceElementoId                = $servicioTecnico->getInterfaceElementoId();
                    if($interfaceElementoId)
                    {
                        $interfaceElemento                  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                   ->find($interfaceElementoId);
                        $equipo['nombreInterfaceElemento']  = $interfaceElemento ? $interfaceElemento->getNombreInterfaceElemento():"";
                        
                    }
                    
                    $elementoId                         = $servicioTecnico->getElementoId();
                    if($elementoId)
                    {
                        $elemento                       = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                               ->find($elementoId);
                        if($elemento)
                        {
                            $equipo['nombreElemento']       = $elemento ? $elemento->getNombreElemento() : "";
                            $infoIpElemento                 = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                   ->findOneBy(array( "elementoId" =>$elemento->getId()));
                            $equipo['ipElemento']           = ($infoIpElemento) ? $infoIpElemento->getIp(): "";
                        }
                    }
                    
                }
                
                if($objProducto)
                {
                    $strNombreProductoOplan = $objProducto->getDescripcionProducto();
                    $strTipo                = "Producto";
                }
                elseif($objPlan)
                {
                    $strNombreProductoOplan = $objPlan->getNombrePlan();
                    $strTipo                = "Plan";
                }

                if($objPunto)
                {
                    $strLoginPuntoCliente       = $objPunto->getLogin();
                    $strDireccionPuntoCliente   = $objPunto->getDireccion();
                    $objJurisdiccion            = $objPunto->getPuntoCoberturaId();
                    if($objJurisdiccion)
                    {
                        $strNombreJurisdiccion  = $objJurisdiccion->getNombreJurisdiccion();
                    }
                
                    $objPersonaEmpresaRol       = $objPunto->getPersonaEmpresaRolId();
                    if($objPersonaEmpresaRol)
                    {
                        $objPersona         = $objPersonaEmpresaRol->getPersonaId();
                        $strCliente         = sprintf("%s",$objPersona);
                    }
                    
                    /*Agregando como destinatarios los correos electrónicos que constan como formas de contacto del punto*/
                    if($strLoginPuntoCliente!="")
                    {
                        $arrayCorreosByPunto    = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                       ->findContactosByPunto($strLoginPuntoCliente,'Correo Electronico');
                        if($arrayCorreosByPunto)
                        {
                            foreach($arrayCorreosByPunto as $arrayCorreoPunto)
                            {
                                if($arrayCorreoPunto && !empty($arrayCorreoPunto['valor']))
                                {
                                    $arrayDestinatariosPunto[] = $arrayCorreoPunto['valor'];
                                }
                            } 
                        }
                    }
                    
                    /*
                     * Buscar si el punto tiene asociado contactos de tipo 'Contacto Tecnico' y a su vez buscar si dicho contacto 
                     * tiene una forma de contacto de tipo Correo electronico
                     */
                    $arrayContactosCorreoTecnicoPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                                          ->getArrayContactosPorPuntoYTipo( $objPunto->getId(),
                                                                                                            "Contacto Tecnico");

                    if($arrayContactosCorreoTecnicoPunto)
                    {
                        foreach($arrayContactosCorreoTecnicoPunto as $arrayContactoCorreoTecnicoPunto)
                        {
                            if($arrayContactoCorreoTecnicoPunto && !empty($arrayContactoCorreoTecnicoPunto['valor']))
                            {
                                $arrayDestinatariosPunto[] = $arrayContactoCorreoTecnicoPunto['valor'];
                            }
                        } 
                    }
                    
                    /*
                     * Buscar si el punto tiene asociado contactos de tipo 'Contacto Notificacion' y a su vez buscar si dicho contacto 
                     * tiene una forma de contacto de tipo Correo electronico
                     */
                    $arrayContactosCorreoNotificacionPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                                               ->getArrayContactosPorPuntoYTipo($objPunto->getId(),
                                                                                                                "Contacto Notificacion");

                    if($arrayContactosCorreoNotificacionPunto)
                    {
                        foreach($arrayContactosCorreoNotificacionPunto as $arrayContactoCorreoNotificacionPunto)
                        {
                            if($arrayContactoCorreoNotificacionPunto && !empty($arrayContactoCorreoNotificacionPunto['valor']))
                            {
                                $arrayDestinatariosPunto[] = $arrayContactoCorreoNotificacionPunto['valor'];
                            }
                        } 
                    }
                    
                    //Para servicios con tipo de orden traslado TN se agregan los contactos comerciales del punto segun lo definido por los usuarios
                    if ($strTipoOrden == "Traslado" && $prefijoEmpresa == "TN")
                    {
                        /*
                        * Buscar si el punto tiene asociado contactos de tipo 'Contacto Tecnico' y a su vez buscar si dicho contacto 
                        * tiene una forma de contacto de tipo Correo electronico
                        */
                        $arrayContactosCorreoTecnicoPunto = $this->emComercial->getRepository("schemaBundle:InfoPuntoContacto")
                                                                              ->getArrayContactosPorPuntoYTipo( $objPunto->getId(),
                                                                                                                "Contacto Comercial");
 
                        if($arrayContactosCorreoTecnicoPunto)
                        {
                            foreach($arrayContactosCorreoTecnicoPunto as $arrayContactoCorreoTecnicoPunto)
                            {
                                if($arrayContactoCorreoTecnicoPunto && !empty($arrayContactoCorreoTecnicoPunto['valor']))
                                {
                                    $arrayDestinatariosPunto[] = $arrayContactoCorreoTecnicoPunto['valor'];
                                }
                            } 
                        }
                    }
                }
                
                if($servicio->getUsrVendedor())
                {
                    $formasContacto             =  $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto(   $servicio->getUsrVendedor(),
                                                                                                        'Correo Electronico' );
                    if($formasContacto)
                    {
                        foreach($formasContacto as $formaContacto)
                        {
                            if($formaContacto && !empty($formaContacto['valor']))
                            {
                                $to[] = $formaContacto['valor'];
                            }
                        }
                    }
                }
                
                $asunto             = "Activacion de Servicio de ".$strLoginPuntoCliente." : ".$strNombreProductoOplan;
                
                if ($strTipoOrden == "Traslado" && $prefijoEmpresa == "TN")
                {
                    $objServProdCaractTraslado = $this->servicioGeneral
                                                      ->getServicioProductoCaracteristica($servicio, 
                                                                                          "TRASLADO", 
                                                                                          $servicio->getProductoId());
                    if (is_object($objServProdCaractTraslado))
                    {
                        $strIdServicioOrigTraslado = $objServProdCaractTraslado->getValor();
                        $objServicioOrigenTraslado = $this->emComercial
                                                          ->getRepository('schemaBundle:InfoServicio')
                                                          ->find($strIdServicioOrigTraslado);
                        if (is_object($objServicioOrigenTraslado))
                        {
                            $strLoginOrigenTraslado    = $objServicioOrigenTraslado->getPuntoId()->getLogin();
                            $strLoginAuxOrigenTraslado = $objServicioOrigenTraslado->getLoginAux();
                        }
                    }
                    
                    $asunto                     .= " por traslado";
                    $observacionActivarServicio .=  " Se canceló el servicio con login auxiliar ".$strLoginAuxOrigenTraslado.
                                                    " perteneciente al punto ".$strLoginOrigenTraslado." el cual es origen del traslado.";
                }
                
                /*Envío de Correo al cliente por Confirmación-Activación de Servicio*/
                $arrayParametrosMail    = array( 
                                                    "cliente"               => $strCliente,
                                                    "loginPuntoCliente"     => $strLoginPuntoCliente,
                                                    "nombreJurisdiccion"    => $strNombreJurisdiccion,
                                                    "direccionPuntoCliente" => $strDireccionPuntoCliente,
                                                    "tipoProductoOPlan"     => $strTipo,
                                                    "nombreProductoOPlan"   => $strNombreProductoOplan,
                                                    "observacion"           => $observacionActivarServicio,
                                                    "estadoServicio"        => $strEstadoServicio,
                                                    "prefijoEmpresa"        => $prefijoEmpresa,
                                                    "empleadoSesion"        => $empleadoSesion,
                                                    "fechaCreacionServicio" => $strFechaCreacionServicio,
                                                    "tipoOrden"             => $strTipoOrden
                                                    );
                
                $this->envioPlantillaService->generarEnvioPlantilla(    $asunto, 
                                                                        $arrayDestinatariosPunto, 
                                                                        'ACTIVARSERVCLI', 
                                                                        $arrayParametrosMail, 
                                                                        $idEmpresa, 
                                                                        '', 
                                                                        '',
                                                                        null,
                                                                        false,
                                                                        'notificaciones_telcos@telconet.ec');
                $arrayParametrosMail["equipo"]              = $equipo;
                $arrayParametrosMail["verDatosTecnicos"]    = (empty($equipo) ? false : true);
                
                /*Envío de Correo a los empleados por Confirmación-Activación de Servicio*/
                $this->envioPlantillaService->generarEnvioPlantilla(    $asunto, 
                                                                        $to, 
                                                                        'ACTIVARSERVICIO', 
                                                                        $arrayParametrosMail, 
                                                                        $idEmpresa, 
                                                                        '', 
                                                                        '',
                                                                        null,
                                                                        false,
                                                                        'notificaciones_telcos@telconet.ec');
                //Notificacion de paramount, noggin y fox dentro de un plan
                if($strTipo == "Plan")
                {
                    $objServicio = $arrayParametros["servicio"];
                    //Se determina si el plan contiene Paramount, Noggin o Fox
                    $arrayPlan = $this->emcom->getRepository('schemaBundle:InfoPlanDet')
                                                ->findBy(array('planId' => $objServicio->getPlanId()));
                    if(!empty($arrayPlan))
                    {
                        foreach($arrayPlan as $objProducto)
                        {
                            $objProducto = $this->emcom->getRepository('schemaBundle:AdmiProducto')
                                                        ->findOneById($objProducto->getProductoId());
                            
                            if (is_object($objProducto) &&
                                ($objProducto->getNombreTecnico() == $this->strSmsNombreTecnicoFoxPremium ||
                                 $objProducto->getNombreTecnico() == $this->strSmsNombreTecnicoParamount   ||
                                 $objProducto->getNombreTecnico() == $this->strSmsNombreTecnicoNoggin))
                            {
                                $arrayProducto = $this->serviceFoxPremium->determinarProducto(array('intIdProducto' => $objProducto->getId()));
                                if($arrayProducto['Status']=='OK')
                                {
                                    //Obtenemos caracteristicas del Servicio
                                    $arrayServicioFox = $this->serviceFoxPremium->obtieneArrayCaracteristicas(
                                                                array('intIdServicio' => $objServicio->getId()));
                                    $objServProdCaracContrasenia = $arrayServicioFox[$arrayProducto['strPass']];
                                    $objServProdCaracUsuario     = $arrayServicioFox[$arrayProducto['strUser']];
                                    $strContraseniaActual        = $this->serviceCrypt->descencriptar($objServProdCaracContrasenia->getValor());
                                    $arrayParamHistorial         = array('strUsrCreacion'  => $objServicio->getUsrCreacion(), 
                                        'strClientIp'     => $objServicio->getIpCreacion(), 
                                        'objInfoServicio' => $objServicio,
                                        'strTipoAccion'   => $arrayProducto['strAccionActivo'],
                                        'strMensaje'      => $arrayProducto['strMensaje']);
                                    //Notificación al cliente por Correo y SMS
                                    $this->serviceFoxPremium->notificaCorreoServicioFox(array(
                                    "strDescripcionAsunto"   => $arrayProducto['strAsuntoNuevo'],
                                    "strCodigoPlantilla"     => $arrayProducto['strCodPlantNuevo'],
                                    "strEmpresaCod"          => $idEmpresa,
                                    "intPuntoId"             => $objServicio->getPuntoId()->getId(),
                                    "intIdServicio"          => $objServicio->getId(),
                                    "strEsPlan"              => 'SI',
                                    "strNombreTecnico"       => $arrayProducto['strNombreTecnico'],
                                    "intPersonaEmpresaRolId" => $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                                    "arrayParametros"        => array("contrasenia" => $strContraseniaActual,
                                    "usuario"                => $objServProdCaracUsuario->getValor()),
                                    "arrayParamHistorial"    => $arrayParamHistorial));
                        
                                    //Se reemplaza la contraseña del mensaje del parámetro
                                    $strMensajeSMS = str_replace("{{USUARIO}}",
                                    $objServProdCaracUsuario->getValor(),
                                    str_replace("{{CONTRASENIA}}",
                                    $strContraseniaActual,
                                    $arrayProducto['strSmsNuevo']));
                        
                                    $this->serviceFoxPremium->notificaSMSServicioFox(array(
                                    "strMensaje"             => $strMensajeSMS,
                                    "strTipoEvento"          => "enviar_infobip",
                                    "strEmpresaCod"          => $idEmpresa,
                                    "intPuntoId"             => $objServicio->getPuntoId()->getId(),
                                    "intPersonaEmpresaRolId" => $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getId(),
                                    "arrayParamHistorial"    => $arrayParamHistorial,
                                    "strNombreTecnico"       => $arrayProducto['strNombreTecnico']));
                                }
                                
                            }
                        }
                    }
                    
                }
            }
        }
        catch (\Exception $ex) 
        {
            $this->utilService->insertError('Telcos+', 
                                            'envioMailConfirmarServicioError', 
                                            $ex->getMessage(), 
                                            $arrayParametros['user'], 
                                            $arrayParametros['ipClient']
                                           );
        }
        
    }
    
    /**
     * confirmarServicioPorNuevoMd
     * 
     * Funcion que sirve para confirmar el servicio de tipo de orden Nueva
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 19-02-2017
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 24-05-2017      Se agregan validaciones para generar los historiales de servicio según sea el escenario
     * @since 1.0
     * 
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - strSerieSmartWifi             Cadena de caracteres que indica la serie del equipo SmartWifi a registrar
     *                                 - strModeloSmartWifi            Cadena de caracteres que indica el modelo del equipo SmartWifi a registrar
     *                                 - strMacSmartWifi               Cadena de caracteres que indica la mac del equipo SmartWifi a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                               ]
     * @return Array $respuestaFinal [
     *                                 - status   Estado de la transaccion ejecutada
     *                                 - mensaje  Mensaje de la transaccion ejecutada
     *                               ]
     */
    public function confirmarServicioNetfiber( $arrayParametros )
    {
        $objServicio                    = !empty($arrayParametros['ojbServicio']) ? $arrayParametros['ojbServicio'] : null;
        $objServicioTecnico             = !empty($arrayParametros['objServicioTecnico']) ? $arrayParametros['objServicioTecnico'] : null;
        $strUsrCreacion                 = !empty($arrayParametros['strUsrCreacion']) ? $arrayParametros['strUsrCreacion'] : "";
        $strIpCreacion                  = !empty($arrayParametros['strIpCreacion']) ? $arrayParametros['strIpCreacion'] : "";
        $objAccion                      = !empty($arrayParametros['objAccion']) ? $arrayParametros['objAccion'] : null;
        $strSerieNetfiber               = !empty($arrayParametros['strSerieNetfiber']) ? $arrayParametros['strSerieNetfiber'] : "";
        $intIdSolicitudServicio         = !empty($arrayParametros['intIdSolicitudServicio']) ? $arrayParametros['intIdSolicitudServicio'] : 0;
        $strEmpresaCod                  = !empty($arrayParametros['strEmpresaCod']) ? $arrayParametros['strEmpresaCod'] : "";
        $strJsonDatosElementos          = !empty($arrayParametros['strJsonDatosElementos'])?$arrayParametros['strJsonDatosElementos']:null;
        $strUltimaMilla                 = "";
        $strTipoNetfiber                = "-FIBRA";
        $strTipoArticulo                = 'AF';
        $strIdentificacionCliente       = "";
        $this->emComercial->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try            
        {
            if(!is_object($objServicio))
            {
                throw new \Exception("No se encontró información acerca del servicio que se está procesando");
            }
            
            if(!is_object($objAccion))
            {
                throw new \Exception("No se encontró información acerca de la acción ejecutada");
            }
            
            $objTipoElemento    = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoElemento')
                                                          ->findOneBy(array('nombreTipoElemento' => 'FIBRA',
                                                                            'estado'             => 'Activo'));
            if(!is_object($objTipoElemento))
            {
                throw new \Exception("No se encontró el tipo del elemento");
            }
            
            $objModeloElemento  = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                          ->findOneBy(array('tipoElementoId'    => $objTipoElemento->getId(),
                                                                            'estado'            => 'Activo'));
            if(!is_object($objModeloElemento))
            {
                throw new \Exception("No se encontró el modelo del elemento");
            }
            $strModeloNetfiber = $objModeloElemento->getNombreModeloElemento();
            
            if(!is_object($objServicioTecnico))
            {
                throw new \Exception("No se encontró el servicio técnico del elemento");
            }

            $objTipoMedio   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                      ->find($objServicioTecnico->getUltimaMillaId());
            if (is_object($objTipoMedio))
            {
                $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
            }
            
            $arrayNetfiberNaf       = $this->servicioGeneral->buscarElementoEnNaf($strSerieNetfiber, "", "PI", "ActivarServicio");
            $strStatusNetfiberNaf   = $arrayNetfiberNaf[0]['status'];
            if($strStatusNetfiberNaf !== "OK")
            {
                throw new \Exception("ERROR NAF: ".$arrayNetfiberNaf[0]['mensaje']);
            }

            $strCodigoArticuloNetfiber = ""; 
            $objInterfaceElemento = $this->servicioGeneral->ingresarElementoCliente($objServicio->getPuntoId()->getLogin(), 
                                                                                    $strSerieNetfiber, 
                                                                                    $strModeloNetfiber,
                                                                                    '-'.$objServicio->getId().$strTipoNetfiber, 
                                                                                    null, 
                                                                                    $strUltimaMilla,
                                                                                    $objServicio, 
                                                                                    $strUsrCreacion, 
                                                                                    $strIpCreacion, 
                                                                                    $strEmpresaCod );
            if (!is_object($objInterfaceElemento))
            {
                throw new \Exception("No se encontró información del elemento creado");
            }
            
            $objElementoNetfiber = $objInterfaceElemento->getElementoId();
            
            //actualizamos registro en el naf wifi
            $strMensajeError = str_repeat(' ', 1000);                                                                  
            $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                               ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                               ":cantidad, :pv_mensajeerror); END;";
            $objStmt = $this->emNaf->getConnection()->prepare($strSql);
            $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
            $objStmt->bindParam('codigoArticulo',        $strCodigoArticuloNetfiber);
            $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
            $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
            $objStmt->bindParam('serieCpe',              $strSerieNetfiber);
            $objStmt->bindParam('cantidad',              intval(1));
            $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
            $objStmt->execute();

            if(strlen(trim($strMensajeError))>0)
            {
                throw new \Exception("ERROR WIFI NAF: ".$strMensajeError);
            }
            
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                  $objElementoNetfiber->getNombreElemento().
                                                  ", Serie: ".
                                                  $strSerieNetfiber.
                                                  ", Modelo: ".
                                                  $strModeloNetfiber
                                                 );
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            $objServicioTecnico->setElementoClienteId($objElementoNetfiber->getId());
            $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElemento->getId());
            $this->emComercial->persist($objServicioTecnico);
            $this->emComercial->flush();
            $arrayElementosNetFiber = json_decode($strJsonDatosElementos,true);
            $intContadorElemento    = 0;
            foreach($arrayElementosNetFiber as $arrayElementoNetFiber)
            {
                $intContadorElemento = $intContadorElemento + 1;
                
                $arrayParametrosNetFiber = array (
                                                  'strSerieNetFiber'    => $arrayElementoNetFiber['serieElemento'],
                                                  'strModeloNetFiber'   => $arrayElementoNetFiber['modeloElemento'],
                                                  'strUsrCreacion'      => $strUsrCreacion,
                                                  'strIpCreacion'       => $strIpCreacion,
                                                  'strEmpresaCod'       => $strEmpresaCod,
                                                  'objServicio'         => $objServicio,
                                                  'strUltimaMilla'      => $strUltimaMilla,
                                                  'strSecuencial'       => $intContadorElemento
                                                 );
                $strStatus = $this->ingresarElementoNetFiber( $arrayParametrosNetFiber );
                if ($strStatus != "OK")
                {
                    throw new \Exception("Existieron problemas al registrar los equipos");
                }
            }
            
            if($strStatus == "OK")
            {
                $objServicio->setEstado("Activo");
                $this->emComercial->persist($objServicio);

                

                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion("Se confirmo el servicio");
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setAccion($objAccion->getNombreAccion());
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                $objSolicitudServicio = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitudServicio);

                if (is_object($objSolicitudServicio))
                {
                    $objSolicitudServicio->setEstado("Finalizada");
                    $this->emComercial->persist($objSolicitudServicio);
                    $this->emComercial->flush();

                    //crear historial para la solicitud
                    $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                    $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudServicio);
                    $objHistorialSolicitudPlani->setEstado("Finalizada");
                    $objHistorialSolicitudPlani->setObservacion("Cliente instalado");
                    $objHistorialSolicitudPlani->setUsrCreacion($strUsrCreacion);
                    $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                    $objHistorialSolicitudPlani->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objHistorialSolicitudPlani);
                    $this->emComercial->flush();

                    $arrayParametros = array();
                    $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudServicio->getId();
                    $arrayParametros['strProceso']            = 'Activar';
                    $this->emInfraestructura
                         ->getRepository('schemaBundle:InfoDetalleSolicitud')
                         ->cerrarTareasPorSolicitud($arrayParametros);
                }
                $this->emComercial->getConnection()->commit();
                $this->emNaf->getConnection()->commit();
                $this->emInfraestructura->getConnection()->commit();
                $strStatus = "OK";
            }
            else
            {
                $strStatus = "ERROR";
            }
        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            $strStatus = "ERROR";
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService.confirmarServicioNetfiber',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        return $strStatus;
    }
  
    /**
     * Función que sirve para realizar la confirmación de un servicio Small Business junto con sus ips adicionales en un traslado.
     * Este proceso incluye activar tanto el Small Business como las ips adicionales en el nuevo punto, así como también se procede 
     * con la cancelación del servicio Small Business y las ips del punto anterior.
     * Además se genera la respectiva solicitud de retiro de equipo y se elimina el ldap del servicio Small Business anterior
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 27-02-2019 Se realiza el mapeo de productos ip asociados a servicios Internet Small Business o TelcoHome
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 04-05-2020 Se usa la función obtenerParametrosProductosTnGpon por reestructuración de servicios Small Business
     * 
     * @param Array $arrayParametros [
     *                                  "idServicio"              => id del servicio
     *                                  "idProducto"              => id del producto
     *                                  "idAccion"                => id de la acción a realizar
     *                                  "idEmpresa"               => código de la empresa
     *                                  "usrCreacion"             => usuario de creación 
     *                                  "ipCreacion"              => ip de creación
     *                                  "prefijoEmpresa"          => prefijo de la empresa
     *                                  "strIdPersonaEmpresaRol"  => id persona empresa rol en sesión
     *                                  "strIdDepartamento"       => id del departamento en sesión
     *                               ]
     * @return Array $arrayRespuestaFinal [
     *                                      "status"  => estado de la transaccion ejecutada
     *                                      "mensaje" => mensaje de la transaccion ejecutada
     *                                    ]
     * 
     */
    public function confirmarIsbPorTraslado($arrayParametros)
    {
        $intIdServicioSB            = $arrayParametros['idServicio'];
        $intIdProductoSB            = $arrayParametros['idProducto'];
        $intIdAccion                = $arrayParametros['idAccion'];
        $strCodEmpresa              = $arrayParametros['idEmpresa'];
        $strUsrCreacion             = $arrayParametros['usrCreacion'];
        $strIpCreacion              = $arrayParametros['ipCreacion'];
        $strPrefijoEmpresa          = $arrayParametros['prefijoEmpresa'];
        $intIdPersonaEmpresaRol     = $arrayParametros['strIdPersonaEmpresaRol'];
        $intIdDepartamento          = $arrayParametros['strIdDepartamento'];
        $arrayProdIpSb              = array();
        try
        {
            $objAccion  = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
            if(!is_object($objAccion))
            {
                throw new \Exception('No existe acción de activación');
            }
            $objServicioSB  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioSB);
            if(!is_object($objServicioSB))
            {
                throw new \Exception('No existe servicio Small Business que desea activar');
            }
            
            $strTipoOrden   = $objServicioSB->getTipoOrden();
            if($strTipoOrden !== "T")
            {
                throw new \Exception('La orden del servicio no es un Traslado Small Business');
            }

            $objProductoSB          = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoSB);
            if(!is_object($objProductoSB))
            {
                throw new \Exception("No existe producto asociado al servicio, favor revisar!");
            }
            $strDescripcionProdPref = $objProductoSB->getDescripcionProducto();
            
            $objServicioTecnicoSB   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneBy(array( "servicioId" => $objServicioSB->getId()));
            if(!is_object($objServicioTecnicoSB))
            {
                throw new \Exception('No existe servicio técnico del '.$strDescripcionProdPref.' para realizar el traslado');
            }
            
            $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                            "strCodEmpresa"                 => $strCodEmpresa,
                                            "intIdProductoInternet"         => $objProductoSB->getId());
            $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
            if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
            {
                foreach($arrayInfoMapeoProds as $arrayInfoProd)
                {
                    $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                    $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                    $arrayProdIpSb[]    = $objProdIPSB;
                }
            }

            $objElementoOlt = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnicoSB->getElementoId());
            if(!is_object($objElementoOlt))
            {
                throw new \Exception("No existe el olt asociado al servicio ".$strDescripcionProdPref);
            }
            $strMarcaOlt                = $objElementoOlt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
            $objInterfaceElementoOlt    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                  ->find($objServicioTecnicoSB->getInterfaceElementoId());
            if(!is_object($objInterfaceElementoOlt))
            {
                throw new \Exception("No existe la interface del olt asociado al servicio ".$strDescripcionProdPref);
            }
            
            $objSpcTrasladoSB   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioSB, 
                                                                                            "TRASLADO", 
                                                                                            $objProductoSB);
            if(!is_object($objSpcTrasladoSB))
            {
                throw new \Exception("No existe un servicio asociado al traslado de este servicio");
            }
            $intIdServicioSBAnterior  = $objSpcTrasladoSB->getValor();
            $objServicioSBAnterior    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioSBAnterior);
            if(!is_object($objServicioSBAnterior))
            {
                throw new \Exception("No existe servicio ".$strDescripcionProdPref." para realizar el traslado");
            }
            $objPuntoAnterior               = $objServicioSBAnterior->getPuntoId();
            $objServicioTecnicoSBAnterior   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array("servicioId"=>$objServicioSBAnterior->getId()));
            if(!is_object($objServicioTecnicoSBAnterior))
            {
                throw new \Exception("No existe servicio técnico ".$strDescripcionProdPref." del servicio anterior para realizar el traslado");
            }
            $objElementoOltAnterior = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                              ->find($objServicioTecnicoSBAnterior->getElementoId());
            if(!is_object($objElementoOltAnterior))
            {
                throw new \Exception("No existe el olt asociado al servicio ".$strDescripcionProdPref." que se desea trasladar");
            }
            $objInterfaceElementoOltAnterior    = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                          ->find($objServicioTecnicoSBAnterior->getInterfaceElementoId());
            if(!is_object($objInterfaceElementoOltAnterior))
            {
                throw new \Exception("No existe la interface del olt asociado al servicio anterior ".$strDescripcionProdPref);
            }
            $strMarcaOltAnterior    = $objElementoOltAnterior->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
            
            $objAccionCancelar      = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find(313);
            if(!is_object($objAccionCancelar))
            {
                throw new \Exception('No existe acción de cancelación');
            }
            $objMotivoCancelarTraslado  = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                          ->findOneBy(array("nombreMotivo" => "Traslado, cambio de dirección ".
                                                                                            "o reubicación", 
                                                                            "estado"       => "Activo"));
            if(!is_object($objMotivoCancelarTraslado))
            {
                throw new \Exception('No existe motivo para realizar la cancelación por traslado');
            }
            
            if($objServicioSBAnterior->getEstado() == 'Cancel' || $objServicioSBAnterior->getEstado() == 'Trasladado')
            {
                $strStatusCancelarTrasladoSB    = "OK";
                $intIpsServiciosAnterior        = 0;
                $boolRealizarCancelacionIsb     = false;
            }
            else if($objServicioSBAnterior->getEstado() !== 'Activo' && $objServicioSBAnterior->getEstado() !== 'In-Corte')
            {
                throw new \Exception('El servicio origen del traslado no se encontró con estado ' .
                                     'Activo ó In-Corte, por este motivo no puede ser cancelado.');
            }
            else
            {
                $boolRealizarCancelacionIsb = true;
            }
            
            if($boolRealizarCancelacionIsb)
            {
                //1.Cancelar Ips adicionales y Servicio Small Business del punto anterior
                $arrayDataCancelacionTrasladoSB     = array("arrayProdIpSb"                     => $arrayProdIpSb,
                                                            "objProductoSB"                     => $objProductoSB,
                                                            "objServicioSBAnterior"             => $objServicioSBAnterior,
                                                            "objServicioTecnicoSBAnterior"      => $objServicioTecnicoSBAnterior,
                                                            "objOltAnterior"                    => $objElementoOltAnterior,
                                                            "objInterfaceElementoOltAnterior"   => $objInterfaceElementoOltAnterior,
                                                            "objModeloOltAnterior"              => $objElementoOltAnterior->getModeloElementoId(),
                                                            "strMarcaOltAnterior"               => $strMarcaOltAnterior,
                                                            "strUsrCreacion"                    => $strUsrCreacion,
                                                            "strIpCreacion"                     => $strIpCreacion,
                                                            "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                            "strCodEmpresa"                     => $strCodEmpresa,
                                                            "strLoginPuntoAnterior"             => $objPuntoAnterior->getLogin(),
                                                            "intIdDepartamento"                 => $intIdDepartamento,
                                                            "intIdPersonaEmpresaRol"            => $intIdPersonaEmpresaRol,
                                                            "objAccionCancelar"                 => $objAccionCancelar,
                                                            "objMotivoCancelarTraslado"         => $objMotivoCancelarTraslado,
                                                            "strDescripcionProdPref"            => $strDescripcionProdPref
                                                        );
                $arrayRespuestaCancelarTrasladoSB   = $this->cancelarServicio->cancelarTrasladoSB($arrayDataCancelacionTrasladoSB);
                $strStatusCancelarTrasladoSB        = $arrayRespuestaCancelarTrasladoSB["strStatus"];
                if($strStatusCancelarTrasladoSB !== "OK")
                {
                    throw new \Exception($arrayRespuestaCancelarTrasladoSB["strMensaje"]);
                }
                $intIpsServiciosAnterior    = $arrayRespuestaCancelarTrasladoSB['intIpsServiciosAnterior'];
            }
            //2.Activación Lógica de un servicio Small Business por Traslado
            $arrayDataActivacionLogicaSB    = array(
                                                    'idServicio'                    => $intIdServicioSB,
                                                    'idAccion'                      => $intIdAccion,
                                                    'usrCreacion'                   => $strUsrCreacion,
                                                    'ipCreacion'                    => $strIpCreacion,
                                                    'strEsSmartSpace'               => 'NO',
                                                    'observacionActivarServicio'    => 'Se confirmo el servicio'
                                                  );
            
            $arrayRespuestaActivacionLogicaSB   = $this->confirmarServicioPorNuevoTn($arrayDataActivacionLogicaSB);
            $strStatusActivacionTrasladoSB      = $arrayRespuestaActivacionLogicaSB["status"];
            if($strStatusActivacionTrasladoSB !== "OK")
            {
                throw new \Exception($arrayRespuestaActivacionLogicaSB["mensaje"]);
            }
            $strStatus  = "OK";
            $strMensaje = "OK-Se realizó el proceso de traslado correctamente";

            $this->actualizarSolicitudTrasladoTN(array( 'objServicio'       => $objServicioSB,
                                                        'strUsrCreacion'    => $strUsrCreacion,
                                                        'strIpCreacion'     => $strIpCreacion));
            
            if($intIpsServiciosAnterior > 0)
            {
                //3. Activar ips adicionales Small Business del punto nuevo
                $arrayDataActivacionTrasladoIpsSB   = array(
                                                                "strCodEmpresa"             => $strCodEmpresa,
                                                                "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                                "objServicioSB"             => $objServicioSB,
                                                                "objServicioTecnicoSB"      => $objServicioTecnicoSB,
                                                                "objElementoOlt"            => $objElementoOlt,
                                                                "strMarcaOlt"               => $strMarcaOlt,
                                                                "objInterfaceElementoOlt"   => $objInterfaceElementoOlt,
                                                                "objProductoSB"             => $objProductoSB,
                                                                "strUsrCreacion"            => $strUsrCreacion,
                                                                "strIpCreacion"             => $strIpCreacion,
                                                                "intIpsServiciosAnterior"   => $intIpsServiciosAnterior,
                                                                "arrayProdIpSb"             => $arrayProdIpSb);

                $arrayRespuestaActivarTrasladoIpsSB = $this->serviceActivarPuerto->activarIpsTrasladoSB($arrayDataActivacionTrasladoIpsSB);
                $strStatusActivarTrasladoIpsSB      = $arrayRespuestaActivarTrasladoIpsSB["strStatus"];
                /**
                 * En caso de que exista algún error al activar las ips, no se reversará algún proceso y las ips 
                 * se procederán con la activación por flujo normal
                 */
                if($strStatusActivarTrasladoIpsSB !== "OK")
                {
                    $strMensaje .= ", pero ".$arrayRespuestaActivarTrasladoIpsSB["strMensaje"];
                }
            }
        } 
        catch (\Exception $e)
        {
            $strMensaje = $e->getMessage();
            $strStatus  = "ERROR";
        }
        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
    }

    /**
     * Funcion que permite activar el producto netlifecam trasladado
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 27-10-2021 - Version inicial
     * 
     * @param array $arrayDatosParametros
     * 
    */
    public function trasladarNetlifeCam($arrayParametros)
    {
        $intIdServicio         = $arrayParametros['intIdServicio'];
        $strNombreTecnico      = $arrayParametros['strNombreTecnico'];
        $strSerieTarjeta       = $arrayParametros['strSerieTarjeta'];
        $strModeloTarjeta      = $arrayParametros['strModeloTarjeta'];
        $strSerieCamara        = $arrayParametros['strSerieCamara'];
        $strModeloCamara       = $arrayParametros['strModeloCamara'];
        $intIdServicioInternet = $arrayParametros['intIdServicioInternet'];
        $strUsrCreacion        = $arrayParametros['strUsrCreacion'];
        $strIpCreacion         = $arrayParametros['strIpCreacion'];
        $strEmpresaCod         = $arrayParametros['strEmpresaCod'];

        $this->emComercial->getConnection()->beginTransaction();

        try            
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if (!empty($objServicio))
            {
                $objServicio->setEstado('Activo');
                $this->emComercial->persist($objServicio);

                $strObservacionHistorial = 'Se confirma producto NetlifeCam trasladado';
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($strObservacionHistorial);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

            }
            else
            {
                throw new \Exception("No se encontró información acerca del servicio que se está procesando");
            }

            $this->emComercial->getConnection()->commit();
            $strStatus  = "OK";
            $strMensaje = "Se traslado el producto NetlifeCam con exito";
        }
        catch (\Exception $objEx)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            $strStatus  = "ERROR";
            $strMensaje = $objEx->getMessage();
            $this->serviceUtil->insertError('Telcos+',
                                            'INFOConfirmarServicio->trasladarNetlifeCam',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayRespuesta = array("status" => $strStatus, "mensaje" => $strMensaje);
        return $arrayRespuesta;
    }
    
    
    /**
     * confirmarServicioExtenderDualBand
     * 
     * Función que sirve para confirmar el servicio de tipo de orden Nueva
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-11-2018
     * @since 1.0
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 20-06-2019  Se agregan validaciones para confirmar que el equipo WIFI DUAL BAND del cliente se encuentre
     *                          instalado para poder activar el EXTENDER DUAL BAND, se agrega generación de factura de visita
     *                          técnica de producto adicional Extender
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 09-07-2019 Se agregan validaciones para agregar el equipo Extender Dual Band por migración de servicios con planes nuevos
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 18-09-2020 Se modifican las validaciones respecto a equipos Dual Band para utilizar nuevas funciones implementadas.
     *                         Así también se obtiene el servicio de Internet en caso de que no se haya enviado como parámetro dicho id.
     *                         Dicha validación se la realiza para servicios W+AP que no tienen el servicio de Internet en estado Activo
     *
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 10-11-2020 Se agrega programación para activar el servicio W+AP que aún no estaba en estado Activo el punto origen, por lo que 
     *                         se convierte el tipo de orden de Traslado a Nueva para que se facturen los respectivos proporcionales
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.5 26-11-2020 Se agrega validación según origen, el cual permitirá finalizar la tarea de instalación con origen web. 
     *                         
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.6 06-04-2021 Se agrega validación para el servicio EXTENDER DUAL BAND para que se convierta el
     *                          tipo de orden de Traslado a Nueva y se facturen los respectivos proporcionales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 09-04-2021 Se realiza validación para verificar si el modelo del ont está permitida para extenders dual band, así como también
     *                         si el modelo del equipo está parametrizado como un extender dual band
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 03-01-2022 Se modifican validaciones para permitir la activación de un servicio extender dual band con tecnología ZTE 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 05-05-2022 Se finaliza la tarea automáticamente ya sea desde web o móvil
     * 
     * @param Array $arrayParametros [
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - objServicioTecnico            Objeto de información técnica del servicio
     *                                 - objProducto                   Objeto del producto del servicio
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creación a utilizar
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creación a utilizar
     *                                 - objAccion                     Objeto de accion ejecutada
     *                                 - strSerieExtenderDualBand      Cadena de caracteres que indica la serie del equipo a registrar
     *                                 - strModeloExtenderDualBand     Cadena de caracteres que indica el modelo del equipo a registrar
     *                                 - strMacExtenderDualBand        Cadena de caracteres que indica la mac del equipo a registrar
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                               ]
     * @return array $arrayRespuesta [
     *                                 - status   Estado de la transacción ejecutada
     *                                 - mensaje  Mensaje de la transacción ejecutada
     *                               ]
     */
    public function confirmarServicioExtenderDualBand($arrayParametros)
    {
        $objServicio                    = !empty($arrayParametros['objServicio'])?$arrayParametros['objServicio']:null;
        $objServicioTecnico             = !empty($arrayParametros['objServicioTecnico'])?$arrayParametros['objServicioTecnico']:null;
        $strUsrCreacion                 = !empty($arrayParametros['strUsrCreacion'])?$arrayParametros['strUsrCreacion']:"";
        $strIpCreacion                  = !empty($arrayParametros['strIpCreacion'])?$arrayParametros['strIpCreacion']:"";
        $objAccion                      = !empty($arrayParametros['objAccion'])?$arrayParametros['objAccion']:null;
        $strSerieExtenderDualBand       = !empty($arrayParametros['strSerieExtenderDualBand'])?$arrayParametros['strSerieExtenderDualBand']:"";
        $strModeloExtenderDualBand      = !empty($arrayParametros['strModeloExtenderDualBand'])?$arrayParametros['strModeloExtenderDualBand']:"";
        $strMacExtenderDualBand         = !empty($arrayParametros['strMacExtenderDualBand'])?$arrayParametros['strMacExtenderDualBand']:"";
        $intIdServicioInternet          = !empty($arrayParametros['intIdServicioInternet'])?$arrayParametros['intIdServicioInternet']:0;
        $strTipoServicio                = !empty($arrayParametros['strTipoServicio'])?$arrayParametros['strTipoServicio']:"";
        $intIdSolicitudServicio         = !empty($arrayParametros['intIdSolicitudServicio'])?$arrayParametros['intIdSolicitudServicio']:0;
        $strEmpresaCod                  = !empty($arrayParametros['strEmpresaCod'])?$arrayParametros['strEmpresaCod']:"";
        $strObservacion                 = !empty($arrayParametros['strObservacion'])?$arrayParametros['strObservacion']:"";
        $strPrefijoEmpresa              = !empty($arrayParametros['strPrefijoEmpresa'])?$arrayParametros['strPrefijoEmpresa']:"";
        $objEmpleadoSesion              = !empty($arrayParametros['objEmpleadoSesion'])?$arrayParametros['objEmpleadoSesion']:null;
        $strMostrarMensaje              = "NO";
        $strUltimaMilla                 = "";
        $strMsjEstadoInternetPorWyAp    = "";
        $strOrigenProceso               = !empty($arrayParametros['strOrigen'])?$arrayParametros['strOrigen']:"WEB";

        $this->emComercial->getConnection()->beginTransaction();
        $this->emNaf->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try            
        {
            if (!is_object($objServicio))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se encontró información acerca del servicio que se está procesando");
            }
            
            if (!is_object($objAccion))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se encontró información acerca de la acción ejecutada");
            }
            
            //se agregan validaciones para realizar activación de servicios Extender Dual Band
            if (is_object($objServicioTecnico))
            {
                $objTipoMedio       = $this->emInfraestructura
                                           ->getRepository('schemaBundle:AdmiTipoMedio')
                                           ->find($objServicioTecnico->getUltimaMillaId());
                if (is_object($objTipoMedio))
                {
                    $strUltimaMilla = $objTipoMedio->getNombreTipoMedio(); 
                }
            }
            if(is_object($objServicio->getPlanId()) && $intIdServicioInternet == 0)
            {
                $intIdServicioInternet = $objServicio->getId();
            }
            
            if(!isset($intIdServicioInternet) || empty($intIdServicioInternet))
            {
                if(is_object($objServicio) && is_object($objServicio->getProductoId()) 
                    && $objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                {
                    $arrayParamsServInternetValido  = array("intIdPunto"    => $objServicio->getPuntoId()->getId(),
                                                            "strCodEmpresa" => $strEmpresaCod);
                    if($objServicio->getTipoOrden() === "T")
                    {
                        $arrayParamsServInternetValido["arrayEstadosInternetIn"]    = array("Activo");
                        $strMsjEstadoInternetPorWyAp                                = " en estado Activo";          
                    }
                    $arrayRespuestaServInternetValido   = $this->servicioGeneral
                                                                ->obtieneServicioInternetValido($arrayParamsServInternetValido);
                    $objServicioInternet                = $arrayRespuestaServInternetValido["objServicioInternet"];
                    if(is_object($objServicioInternet))
                    {
                        $intIdServicioInternet = $objServicioInternet->getId();
                    }
                }
            }
            else
            {
                $objServicioInternet                = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioInternet);
            }
            
            if (!is_object($objServicioInternet))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se encontró el servicio de Internet".$strMsjEstadoInternetPorWyAp);
            }
            
            $objPunto = $objServicioInternet->getPuntoId();
            $objServicioTecnicoInternet = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneBy(array( "servicioId" => $intIdServicioInternet));
            if(!is_object($objServicioTecnicoInternet))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido obtener la información técnica del servicio de Internet");
            }
            
            if(!is_object($objServicioInternet->getPlanId()))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido obtener el plan asociado al servicio de Internet");
            }
            
            $intIdOltServicioInternet = $objServicioTecnicoInternet->getElementoId();
            if(!isset($intIdOltServicioInternet) || empty($intIdOltServicioInternet))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido obtener la información del olt del servicio de Internet");
            }
            $objOltServicioInternet         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                      ->find($intIdOltServicioInternet);
            if(!is_object($objOltServicioInternet))
            {
                throw new \Exception("No se ha podido obtener el olt asociado al servicio de Internet");
            }
            $objModeloOltServicioInternet   = $objOltServicioInternet->getModeloElementoId();
            $strModeloOltServicioInternet   = $objModeloOltServicioInternet->getNombreModeloElemento();
            $strMarcaOltServicioInternet    = $objModeloOltServicioInternet->getMarcaElementoId()->getNombreMarcaElemento();
            
            $intIdOntServicioInternet = $objServicioTecnicoInternet->getElementoClienteId();
            if(!isset($intIdOntServicioInternet) || empty($intIdOntServicioInternet))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido obtener la información del ont del servicio de Internet");
            }
            $objOntServicioInternet         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                      ->find($intIdOntServicioInternet);
            if(!is_object($objOntServicioInternet))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido obtener el olt asociado al servicio de Internet");
            }
            $objModeloOntServicioInternet   = $objOntServicioInternet->getModeloElementoId();
            $strModeloOntServicioInternet   = $objModeloOntServicioInternet->getNombreModeloElemento();
            
            $arrayVerifModeloOntParaExtender    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            'MODELOS_EXTENDERS_POR_ONT',
                                                                            $strMarcaOltServicioInternet,
                                                                            '',
                                                                            $strModeloOntServicioInternet,
                                                                            $strModeloExtenderDualBand,
                                                                            $strEmpresaCod);
            if(!isset($arrayVerifModeloOntParaExtender) || empty($arrayVerifModeloOntParaExtender))
            {
                $arrayVerifModeloWifiDualBand       = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                'MODELOS_EQUIPOS',
                                                                                $strMarcaOltServicioInternet,
                                                                                $strModeloOltServicioInternet,
                                                                                'WIFI DUAL BAND',
                                                                                $strModeloOntServicioInternet,
                                                                                $strEmpresaCod);
                if(isset($arrayVerifModeloWifiDualBand) && !empty($arrayVerifModeloWifiDualBand))
                {
                    $arrayVerifModeloExtenderDualBand   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                          ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    'MODELOS_EQUIPOS',
                                                                                    $strMarcaOltServicioInternet,
                                                                                    $strModeloOltServicioInternet,
                                                                                    'EXTENDER DUAL BAND',
                                                                                    $strModeloExtenderDualBand,
                                                                                    $strEmpresaCod);
                    
                    if(!isset($arrayVerifModeloExtenderDualBand) || empty($arrayVerifModeloExtenderDualBand))
                    {
                        $strMostrarMensaje = "SI";
                        throw new \Exception("No está permitido activar un extender con modelo ".$strModeloExtenderDualBand
                                            ." cuyo servicio de Internet fue activado con un modelo de ONT ".$strModeloOntServicioInternet);
                    }
                    
                }
                else
                {
                    $strMostrarMensaje = "SI";
                    throw new \Exception("No está permitido activar un extender con modelo ".$strModeloExtenderDualBand." cuyo servicio de Internet "
                                        ."fue activado con un modelo de ONT ".$strModeloOntServicioInternet);
                }
            }
            
            $objProductoInternetEnPlan  = null;
            $arrayProdInternet          = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->findBy(array( "nombreTecnico" => "INTERNET",
                                                                            "empresaCod"    => $strEmpresaCod, 
                                                                            "estado"        => "Activo"));
            
            $arrayDetallesPlan  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                    ->findBy(array("planId" => $objServicioInternet->getPlanId()->getId()));
            
            for($intContDetallePlan=0; $intContDetallePlan < count($arrayDetallesPlan); $intContDetallePlan++)
            {
                for($intContProdInternet = 0; $intContProdInternet < count($arrayProdInternet); $intContProdInternet++)
                {
                    if($arrayDetallesPlan[$intContDetallePlan]->getProductoId() === $arrayProdInternet[$intContProdInternet]->getId())
                    {
                        $objProductoInternetEnPlan = $arrayProdInternet[$intContProdInternet];
                        break;
                    }
                }
            }
            if(!is_object($objProductoInternetEnPlan))
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido obtener el producto de Internet dentro del plan");
            }
            $strServiceProfile      = "";
            $objSpcServiceProfile   = $this->servicioGeneral
                                           ->getServicioProductoCaracteristica($objServicioInternet, "SERVICE-PROFILE", $objProductoInternetEnPlan);
            if(is_object($objSpcServiceProfile))
            {
                $strServiceProfile = $objSpcServiceProfile->getValor();
            }
            $strModeloElementoOlt   = "";
            $strIpElementoOlt       = "";
            $intIdElementoOlt       = $objServicioTecnicoInternet->getElementoId();
            $intIdInterfaceOlt      = $objServicioTecnicoInternet->getInterfaceElementoId();
            $objElementoOlt         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($intIdElementoOlt);
            $objInterfaceOlt        = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($intIdInterfaceOlt);
            if(is_object($objElementoOlt))
            {
                $strModeloElementoOlt   = $objElementoOlt->getModeloElementoId()->getNombreModeloElemento();
            }
            
            $objIpElementoOlt   = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                          ->findOneBy(array("elementoId" => $intIdElementoOlt));
            if(is_object($objIpElementoOlt))
            {
                $strIpElementoOlt = $objIpElementoOlt->getIp();
            }
            
            $objPersonaEmpresaRol           = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->find($objPunto->getPersonaEmpresaRolId()->getId());
            $objPersona                     = $objPersonaEmpresaRol->getPersonaId();
            $strIdentificacion              = $objPersona->getIdentificacionCliente();
            $strNombreCliente               = $objPersona->__toString();
            $strTipoNegocio                 = $objPunto->getTipoNegocioId()->getNombreTipoNegocio();
            
            $objSpcIndiceCliente            = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica( $objServicioInternet, 
                                                                                        "INDICE CLIENTE", 
                                                                                        $objProductoInternetEnPlan);
            $strIndiceCliente   = "";
            if(is_object($objSpcIndiceCliente))
            {
                $strIndiceCliente = $objSpcIndiceCliente->getValor();
            }
            
            $arrayParams['intInterfaceElementoConectorId'] = $objServicioTecnicoInternet->getInterfaceElementoClienteId();
            $arrayParams['strTipoSmartWifi']               = 'ExtenderDualBand';
            $arrayParams['arrayData']                      = array();
            $arrayElementosExtenderDualBand                = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                     ->getElementosSmartWifiByInterface($arrayParams);
            $arrayDatosExtender     = array(
                                        'serie_extender'        => $strSerieExtenderDualBand,
                                        'mac_extender'          => $strMacExtenderDualBand,
                                        'estado_servicio'       => $objServicioInternet->getEstado(),
                                        'ip_olt'                => $strIpElementoOlt,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'numero_de_extender'    => count($arrayElementosExtenderDualBand),
                                        'puerto_olt'            => $objInterfaceOlt->getNombreInterfaceElemento(),
                                        'ont_id'                => $strIndiceCliente,
                                        'service_profile'       => $strServiceProfile,
                                        'modelo_olt'            => $strModeloElementoOlt
                                    );

            $arrayDatosMiddleware   = array(
                                            'nombre_cliente'        => $strNombreCliente,
                                            'login'                 => $objPunto->getLogin(),
                                            'identificacion'        => $strIdentificacion,
                                            'datos'                 => $arrayDatosExtender,
                                            'opcion'                => "ACTIVAR_EXTENDER",
                                            'ejecutaComando'        => $this->strEjecutaComando,
                                            'usrCreacion'           => $strUsrCreacion,
                                            'ipCreacion'            => $strIpCreacion
                                        );
            
            
            $arrayRespuestaMiddleware   = $this->serviceRdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
            $strStatusMiddleware        = $arrayRespuestaMiddleware['status'];
            $strMensajeMiddleware       = $arrayRespuestaMiddleware['mensaje'];
            
            if($strStatusMiddleware !== "OK")
            {
                $strMostrarMensaje = "SI";
                throw new \Exception($strMensajeMiddleware);
            }
            
            $strObservacionSolicitud    = "Cliente instalado";
            $strProcesoCierreTareas     = "Activar";
            
            
            
            if(is_object($objServicio->getProductoId()))
            {
                $strExtenderEnPlan  = "NO";
                if($objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                {
                    if($objServicio->getEstado() === "Activo")
                    {
                        $strNombreAccion            = "";
                        $strObservacionHistorial    = "Se agregó el equipo Extender Dual Band";
                        $strMensajeUsr              = "Se agregó el equipo Extender Dual Band correctamente";
                    }
                    else
                    {
                        $strNombreAccion            = $objAccion->getNombreAccion();
                        $strObservacionHistorial    = "Se confirmo el servicio";
                        $strMensajeUsr              = "Se confirmó el servicio correctamente";
                        if($strOrigenProceso == "ACTIVACION")
                        {
                            $objServicio->setEstado("EnVerificacion");
                        }
                        else
                        {
                            $objServicio->setEstado("Activo");
                        }
                        $this->emComercial->persist($objServicio);
                        $this->emComercial->flush();
                    }
                }
                else
                {
                    $strNombreAccion            = $objAccion->getNombreAccion();
                    $strObservacionHistorial    = "Se confirmo el servicio";
                    $strMensajeUsr              = "Se confirmó el servicio correctamente";
                    $objServicio->setEstado("Activo");
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();
                }
            }
            else if(is_object($objServicio->getPlanId()) && ($objServicioInternet->getId() === $objServicio->getId()))
            {
                $strExtenderEnPlan          = "SI";
                $strNombreAccion            = "";
                if(isset($strTipoServicio) && !empty($strTipoServicio) && $strTipoServicio === "MIGRACION")
                {
                    $strObservacionHistorial    = "Se agregó el equipo Extender Dual Band por migración";
                    $strMensajeUsr              = "Se agregó correctamente el equipo Extender Dual Band por migración";
                    $strObservacionSolicitud    = "SE FINALIZA LA SOLICITUD DE MIGRACION";
                    $strProcesoCierreTareas     = "Migrar";
                }
                else
                {
                    $strObservacionHistorial    = "Se agregó el equipo Extender Dual Band";
                    $strMensajeUsr              = "Se agregó el equipo Extender Dual Band correctamente";
                }
            }
            else
            {
                $strMostrarMensaje = "SI";
                throw new \Exception("No se ha podido verificar el servicio");
            }
            
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion($strObservacionHistorial);
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $objServicioHistorial->setAccion($strNombreAccion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $arrayParametrosExtenderDualBand    = array (
                                                        'strSerieExtenderDualBand'  => $strSerieExtenderDualBand,
                                                        'strModeloExtenderDualBand' => $strModeloExtenderDualBand,
                                                        'strMacExtenderDualBand'    => $strMacExtenderDualBand,
                                                        'intIdServicioInternet'     => $intIdServicioInternet,
                                                        'strUsrCreacion'            => $strUsrCreacion,
                                                        'strIpCreacion'             => $strIpCreacion,
                                                        'strEmpresaCod'             => $strEmpresaCod,
                                                        'objServicio'               => $objServicio,
                                                        'strUltimaMilla'            => $strUltimaMilla,
                                                        'strTipoExtenderDualBand'   => "-ExtenderDualBand",
                                                        'strExtenderEnPlan'         => $strExtenderEnPlan
                                                      );
            $arrayRespuestaIngresoExtender      = $this->ingresarElementoExtenderDualBand( $arrayParametrosExtenderDualBand );
            if($arrayRespuestaIngresoExtender["status"] !== "OK")
            {
                $strMostrarMensaje = "SI";
                throw new \Exception($arrayRespuestaIngresoExtender["mensaje"]);
            }
            
            //finalizar solicitud de agregar equipo
            $objSolicitudServicio = $this->emComercial
                                         ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                         ->find($intIdSolicitudServicio);

            if(is_object($objSolicitudServicio))
            {
                $objAdmiCaracteristicaExtenderDualBand  = $this->emComercial
                                                               ->getRepository("schemaBundle:AdmiCaracteristica")
                                                               ->findOneBy(array(   'descripcionCaracteristica' => 'EXTENDER DUAL BAND',
                                                                                    'estado'                    => 'Activo'));
                
                
                
                if (is_object($objAdmiCaracteristicaExtenderDualBand))
                {
                        $objDetalleSolCaractExtenderDualBand    = $this->emComercial
                                                                       ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                       ->findOneBy(
                                                                                array(
                                                                                      "detalleSolicitudId"=> $objSolicitudServicio,
                                                                                      "caracteristicaId"  => $objAdmiCaracteristicaExtenderDualBand,
                                                                                      "estado"            => "Asignada"
                                                                                      )
                                                                                  );
                        if(is_object($objDetalleSolCaractExtenderDualBand))
                        {
                            $objDetalleSolCaractExtenderDualBand->setEstado("Finalizada");
                            $objDetalleSolCaractExtenderDualBand->setUsrUltMod($strUsrCreacion);
                            $objDetalleSolCaractExtenderDualBand->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objDetalleSolCaractExtenderDualBand);
                            $this->emComercial->flush();
                        }
                    }
                    
                
                    $objSolicitudServicio->setEstado("Finalizada");
                    $this->emComercial->persist($objSolicitudServicio);
                    $this->emComercial->flush();

                    //crear historial para la solicitud
                    $objHistorialSolicitudPlani = new InfoDetalleSolHist();
                    $objHistorialSolicitudPlani->setDetalleSolicitudId($objSolicitudServicio);
                    $objHistorialSolicitudPlani->setEstado("Finalizada");
                    $objHistorialSolicitudPlani->setObservacion($strObservacionSolicitud);
                    $objHistorialSolicitudPlani->setUsrCreacion($strUsrCreacion);
                    $objHistorialSolicitudPlani->setFeCreacion(new \DateTime('now'));
                    $objHistorialSolicitudPlani->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objHistorialSolicitudPlani);
                    $this->emComercial->flush();

                if($strOrigenProceso == "WEB")
                {    
                    $arrayParametros = array();
                    $arrayParametros['intIdDetalleSolicitud'] = $objSolicitudServicio->getId();
                    $arrayParametros['strProceso']            = $strProcesoCierreTareas;
                    $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleSolicitud')->cerrarTareasPorSolicitud($arrayParametros);
                }
                
                if($strTipoServicio === "PRODUCTO" && is_object($objServicio->getProductoId()))
                {
                    if($objServicio->getTipoOrden() === "T" && 
                      ($objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB" || 
                       $objServicio->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND"))
                    {
                        $objSpcTrasladoServWdbYAp   = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, 
                                                                                                                "TRASLADO", 
                                                                                                                $objProductoInternetEnPlan);
                        if(is_object($objSpcTrasladoServWdbYAp))
                        {
                            $intIdServicioOrigenWdbYAp  = $objSpcTrasladoServWdbYAp->getValor();
                            if(isset($intIdServicioOrigenWdbYAp) && !empty($intIdServicioOrigenWdbYAp))
                            {
                                $objServicioOrigenWdbYAp    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                ->find($intIdServicioOrigenWdbYAp);
                                if(is_object($objServicioOrigenWdbYAp))
                                {
                                    $objServicioOrigenWdbYAp->setEstado('Trasladado');
                                    $this->emComercial->persist($objServicioOrigenWdbYAp);
                                    $this->emComercial->flush();

                                    $objServicioHistOrigenWdbYAp = new InfoServicioHistorial();
                                    $objServicioHistOrigenWdbYAp->setServicioId($objServicioOrigenWdbYAp);
                                    $objServicioHistOrigenWdbYAp->setObservacion('Se trasladó el servicio');
                                    $objServicioHistOrigenWdbYAp->setIpCreacion($strIpCreacion);
                                    $objServicioHistOrigenWdbYAp->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistOrigenWdbYAp->setUsrCreacion($strUsrCreacion);
                                    $objServicioHistOrigenWdbYAp->setEstado('Trasladado');
                                    $this->emComercial->persist($objServicioHistOrigenWdbYAp);
                                    $this->emComercial->flush();
                                }
                            }
                            $objSpcTrasladoServWdbYAp->setEstado('Eliminado');
                            $objSpcTrasladoServWdbYAp->setUsrUltMod($strUsrCreacion);
                            $objSpcTrasladoServWdbYAp->setFeUltMod(new \DateTime('now'));
                            $this->emComercial->persist($objSpcTrasladoServWdbYAp);
                            $this->emComercial->flush();
                        }
                        
                        $objServicio->setTipoOrden("N");
                        $this->emComercial->persist($objServicio);

                        $objServHistoTrasladoANueva = new InfoServicioHistorial();
                        $objServHistoTrasladoANueva->setServicioId($objServicio);
                        $objServHistoTrasladoANueva->setObservacion("Se modifica correctamente el tipo de orden del servicio de Traslado a Nueva "
                                                                    ."por activación del servicio en el punto destino");
                        $objServHistoTrasladoANueva->setEstado($objServicio->getEstado());
                        $objServHistoTrasladoANueva->setUsrCreacion($strUsrCreacion);
                        $objServHistoTrasladoANueva->setFeCreacion(new \DateTime('now'));
                        $objServHistoTrasladoANueva->setIpCreacion($strIpCreacion);
                        $this->emComercial->persist($objServHistoTrasladoANueva);
                        $this->emComercial->flush();
                    }
                    
                    // Consultar en los parametros si la bandera de facturar las visitas técnicas para activación de extenders esta encendida.
                    $arrayAdmiParametroDebeFacurar  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne('FACTURAR_VISITA_POR_INSTALACION',
                                                                               'TECNICO',
                                                                               '',
                                                                               $objServicio->getProductoId()->getNombreTecnico(),
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               $strEmpresaCod);
                    
                    if (isset($arrayAdmiParametroDebeFacurar['valor1']) &&
                        !empty($arrayAdmiParametroDebeFacurar['valor1']) &&
                        $arrayAdmiParametroDebeFacurar['valor1'] === 'SI')
                    {
                        $arrayParametrosSolicitudVisitaDB = array();
                        $arrayParametrosSolicitudVisitaDB['strUser']       = $strUsrCreacion;
                        $arrayParametrosSolicitudVisitaDB['objServicio']   = $objServicio;
                        $arrayParametrosSolicitudVisitaDB['strEmpresaCod'] = $strEmpresaCod;
                        $arrayParametrosSolicitudVisitaDB['floatValor']    = $arrayAdmiParametroDebeFacurar['valor2'];
                        $this->servicioGeneral->generarSolicitudVisitaTecnicaPorInstalacion($arrayParametrosSolicitudVisitaDB);
                    }
                }
            }
            if($strExtenderEnPlan === "NO")
            {
                $arrayParametrosMail =   array(
                                               "servicio"                      => $objServicio,
                                               "prefijoEmpresa"                => $strPrefijoEmpresa,
                                               "empleadoSesion"                => $objEmpleadoSesion,
                                               "observacionActivarServicio"    => $strObservacion,
                                               "idEmpresa"                     => $strEmpresaCod,
                                               "user"                          => $strUsrCreacion,
                                               "ipClient"                      => $strIpCreacion
                                              );
                
                $this->envioMailConfirmarServicio($arrayParametrosMail);
            }
            
            $this->emComercial->getConnection()->commit();
            $this->emNaf->getConnection()->commit();
            $this->emInfraestructura->getConnection()->commit();

            /*Buscamos el ID del detalle para poder cerrar la tarea de forma automatica.*/
            $objInfoDet = $this->emComercial->getRepository('schemaBundle:InfoDetalle')
                ->findOneByDetalleSolicitudId($objSolicitudServicio->getId());

            /*Armamos el parametro para realizar la peticion.*/
            $arrayParametrosFinTarea = array(
                'idEmpresa'               => $strEmpresaCod,
                'prefijoEmpresa'          => $strPrefijoEmpresa,
                'idDetalle'               => $objInfoDet->getId(),
                'idAsignado'              => null,
                'observacion'             => 'Se finaliza tarea de forma automatica.',
                'usrCreacion'             => $strUsrCreacion,
                'ipCreacion'              => $strIpCreacion,
                'accionTarea'             => 'finalizada'
            );

            /*Ejecutamos la petición de cerrar tarea.*/
            $this->serviceSoporte->finalizarTarea($arrayParametrosFinTarea);

            $strStatus  = "OK";
            $strMensaje = $strMensajeUsr;

        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if($this->emNaf->getConnection()->isTransactionActive())
            {
                $this->emNaf->getConnection()->rollback();
            }
            
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->utilService->insertError('Telcos+',
                                            'InfoConfirmarServicioService->confirmarServicioExtenderDualBand',
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayRespuesta = array("status"                => $strStatus, 
                                "mensaje"               => $strMensaje, 
                                "mostrarMensaje"        => $strMostrarMensaje,
                                "objServicioInternet"   => $objServicioInternet);
        return $arrayRespuesta;
    }
    
    
    /**
     * ingresarElementoExtenderDualBand
     * 
     * Función que ingresa un elemento Extender
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 24-12-2018
     * @since 1.0
     * 
     * 
     * @param Array $arrayParametros [
     *                                 - strSerieExtenderDualBand      Cadena de caracteres que indica la serie del equipo ExtenderDualBand
     *                                 - strModeloExtenderDualBand     Cadena de caracteres que indica el modelo del equipo ExtenderDualBand
     *                                 - strMacExtenderDualBand        Cadena de caracteres que indica la mac del equipo ExtenderDualBand
     *                                 - intIdServicioInternet         Identificador del Servicio de Internet Activo del punto
     *                                 - strUsrCreacion                Cadena de caracteres que indica el usuario de creacion a utilizar
     *                                 - ojbServicio                   Objeto de servicio 
     *                                 - strUltimaMilla                Cadena de caracteres que indica la ultima milla del servicio procesado
     *                                 - strIpCreacion                 Cadena de caracteres que indica la ip de creacion a utilizar
     *                                 - strEmpresaCod                 Identificador de Empresa
     *                               ]
     * @return String  $status  Estado de la transacción ejecutada
     * 
     */
    public function ingresarElementoExtenderDualBand( $arrayParametros )
    {
        $strSerieExtenderDualBand = $arrayParametros['strSerieExtenderDualBand'];
        $strModeloExtenderDualBand= $arrayParametros['strModeloExtenderDualBand'];
        $strMacExtenderDualBand   = $arrayParametros['strMacExtenderDualBand'];
        $intIdServicioInternet    = $arrayParametros['intIdServicioInternet'];
        $strUsrCreacion           = $arrayParametros['strUsrCreacion'];
        $objServicio              = $arrayParametros['objServicio'];
        $strUltimaMilla           = $arrayParametros['strUltimaMilla'];
        $strIpCreacion            = $arrayParametros['strIpCreacion'];
        $strEmpresaCod            = $arrayParametros['strEmpresaCod'];
        $strTipoExtenderDualBand  = $arrayParametros['strTipoExtenderDualBand'];
        $strExtenderEnPlan        = $arrayParametros['strExtenderEnPlan'];
        $objInterfaceElementoFin  = null;
        $strMensaje               = '';
        $strTipoArticulo          = 'AF';
        $strIdentificacionCliente = "";
        try
        {
            //se recupera ultimo elemento enlazado en el servicio para poder ingresar el nuevo elemento Smart Space
            $objServicioInternet = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioInternet);
            if (is_object($objServicioInternet))
            {
                $objServicioTecnicoInternet = $this->emComercial
                                                   ->getRepository('schemaBundle:InfoServicioTecnico')
                                                   ->findOneBy(array( "servicioId" => $objServicioInternet->getId()));
                if (is_object($objServicioTecnicoInternet))
                {
                    if($objServicioTecnicoInternet->getInterfaceElementoClienteId())
                    {
                        $arrayParams['intInterfaceElementoConectorId']  = $objServicioTecnicoInternet->getInterfaceElementoClienteId();
                        $arrayParams['arrayData']                       = array();
                        $arrayParams['strBanderaReturn']                = 'INTERFACE';
                        $arrayParams['strTipoSmartWifi']                = 'ExtenderDualBand';
                        if($strExtenderEnPlan === "SI")
                        {
                            $objInterfaceElementoSt = $this->emInfraestructura
                                                           ->getRepository('schemaBundle:InfoInterfaceElemento')
                                                           ->find($objServicioTecnicoInternet->getInterfaceElementoClienteId());
                            $arrayParams['strRetornaUltElePlan']        = 'SI';
                            $arrayParams['objInterfaceElementoFinPlan'] = $objInterfaceElementoSt;
                        }
                        $objInterfaceElementoFin = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoElemento')
                                                        ->getElementosSmartWifiByInterface($arrayParams);
                    }
                }
                else
                {
                    throw new \Exception('Se presentaron errores al recuperar información técnica del servicio de internet.');
                }
            }
            else
            {
                throw new \Exception('Se presentaron errores al recuperar información del servicio de internet.');
            }

            if(!is_object($objInterfaceElementoFin))
            {
                throw new \Exception('Se presentaron errores al recuperar información para crear elemento Extender Dual Band.');
            }

            //se procede a realizar el ingreso del elemento Smart Space y despacharlo en el NAF
            $arrayExtenderNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieExtenderDualBand, 
                                                                        $strModeloExtenderDualBand, 
                                                                        "PI", 
                                                                        "ActivarServicio");
            $strExtenderNaf = $arrayExtenderNaf[0]['status'];
            if($strExtenderNaf !== "OK")
            {
                throw new \Exception("ERROR WIFI NAF: ".$arrayExtenderNaf[0]['mensaje']);
            }
            $strCodigoArticuloWifi = "";
            $objInterfaceElementoExtenderDualBand = $this->servicioGeneral
                                                        ->ingresarElementoCliente( $objServicio->getPuntoId()->getLogin(), 
                                                                                   $strSerieExtenderDualBand, 
                                                                                   $strModeloExtenderDualBand,
                                                                                   '-'.$objServicio->getId().$strTipoExtenderDualBand, 
                                                                                   $objInterfaceElementoFin, 
                                                                                   $strUltimaMilla,
                                                                                   $objServicio, 
                                                                                   $strUsrCreacion, 
                                                                                   $strIpCreacion, 
                                                                                   $strEmpresaCod );
            if(!is_object($objInterfaceElementoExtenderDualBand))
            {
                throw new \Exception('Se presentaron errores al ingresar el elemento Extender Dual Band.');
            }
            
            $objElementoExtenderDualBand = $objInterfaceElementoExtenderDualBand->getElementoId();

            if (!is_object($objElementoExtenderDualBand))
            {
                throw new \Exception("No se encontro información del elemento ExtenderDualBand creado");
            }

            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se registro el elemento con nombre: ".
                                                  $objElementoExtenderDualBand->getNombreElemento().
                                                  ", Serie: ".
                                                  $strSerieExtenderDualBand.
                                                  ", Modelo: ".
                                                  $strModeloExtenderDualBand.
                                                  ", Mac: ".
                                                  $strMacExtenderDualBand
                                                 );
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            //actualizamos registro en el naf wifi
            $strMensajeError = str_repeat(' ', 1000);                                                                  
            $strSql          = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, ".
                               ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, ".
                               ":cantidad, :pv_mensajeerror); END;";
            $objStmt = $this->emNaf->getConnection()->prepare($strSql);
            $objStmt->bindParam('codigoEmpresaNaf',      $strEmpresaCod);
            $objStmt->bindParam('codigoArticulo',        $strCodigoArticuloWifi);
            $objStmt->bindParam('tipoArticulo',          $strTipoArticulo);
            $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
            $objStmt->bindParam('serieCpe',              $strSerieExtenderDualBand);
            $objStmt->bindParam('cantidad',              intval(1));
            $objStmt->bindParam('pv_mensajeerror',       $strMensajeError);
            $objStmt->execute();

            if(strlen(trim($strMensajeError))>0)
            {
                throw new \Exception("ERROR WIFI NAF: ".$strMensajeError);
            }
            else
            {
                if ($strExtenderEnPlan === "NO")
                {
                    //servicio prod caract mac wifi
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                   $objServicio->getProductoId(), 
                                                                                   "MAC", 
                                                                                   $strMacExtenderDualBand, 
                                                                                   $strUsrCreacion);
                    $objServicioTecnico = $this->emComercial
                                               ->getRepository('schemaBundle:InfoServicioTecnico')
                                               ->findOneBy(array( "servicioId" => $objServicio->getId()));
                    if (is_object($objServicioTecnico))
                    {
                        //guardar ont en servicio tecnico
                        $objServicioTecnico->setElementoClienteId($objElementoExtenderDualBand->getId());
                        $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoExtenderDualBand->getId());
                        $this->emComercial->persist($objServicioTecnico);
                        $this->emComercial->flush();
                    }
                }
                
                //info_detalle_elemento gestion remota
                $this->servicioGeneral->ingresarDetalleElemento($objElementoExtenderDualBand,
                                                                "MAC",
                                                                "MAC",
                                                                $strMacExtenderDualBand,
                                                                $strUsrCreacion,
                                                                $strIpCreacion);
            }
            $strStatus = 'OK';
        } 
        catch (\Exception $e) 
        {
            $strMensaje = $e->getMessage();
            $this->utilService->insertError('Telcos+', 
                                            'InfoConfirmarServicioService->ingresarElementoExtenderDualBand', 
                                            $strMensaje,
                                            $strUsrCreacion, 
                                            $strIpCreacion
                                           );
            $strStatus = 'ERROR';
        }
        $arrayResultado = array("status" => $strStatus, "mensaje" => $strMensaje);
        return $arrayResultado;
    }

    /**
     * Funcion que permite activar productos adicionales automaticos, que se
     * deberan activar con el servicio de internet de forma simultanea
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 27-07-2021 - Version inicial
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 14-03-2022 - Se crea validacion, para que si un servicio adicional trasladado activo, se detiene
     *              y se vuelva a activar ya no genere una doble facturacion.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.2 06-05-2022 - Se crea validacion en carga y activacion de los servicios adicionales, para evitar error
     *                           por array vacio cuando no encuentra adicionales.
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.3 27-07-2022 - Se modifica el ingreso y la actualizacion de las caracteristicas para productos konibit, es decir,
     *                           si el producto konibit se activa y ya existe la caracteristica se actualiza, si no existe se ingresa.
     * @param array $arrayDatosParametros
     * 
    */
    public function activarProductosAdicionales($arrayDatosParametros)
    {
        $intIdPunto      = $arrayDatosParametros['intIdPunto'];
        $intCodEmpresa   = $arrayDatosParametros['intCodEmpresa'];
        $strIpCreacion   = $arrayDatosParametros['strIpCreacion'];
        $strUserCreacion = $arrayDatosParametros['strUserCreacion'];
        $strAccion       = $arrayDatosParametros['strAccion'];
        // Seleccionamos los estados permitidos para activar los productos adicionales
        $objParamEstado = null;
        $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                            ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                'Estados permitidos para los productos adicionales',
                                '','','','','',$intCodEmpresa);
        if(is_array($arrayValoresParametros) && !empty($arrayValoresParametros))
        {
            $objParamEstado = $arrayValoresParametros[0];
        }
        // Obtenemos los productos adicionales permitidos
        $arrayListadoServicios = array();
        $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                        'Lista de productos adicionales automaticos',
                                        '','','','','',$intCodEmpresa);
        // Obtenemos la cantidad de reintentos permitidos
        $intMaxIntentos = 1;
        $arrayParamIntentos = array();
        $arrayParamIntentos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                        'Reintentos y delay para los productos adicionales',
                                        '','','','','',$intCodEmpresa);
        
        if(is_array($arrayParamIntentos) && !empty($arrayParamIntentos))
        {
            $arrayIntentos = $arrayParamIntentos[0];
            $intMaxIntentos = $arrayIntentos['valor1'];
        }
        // Obtenemos los servicios
        $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                ->findServiciosByPuntoAndEstado($intIdPunto,$objParamEstado['valor1'],null);
        
        if (!empty($arrayServiciosPunto))
        {
            foreach($arrayServiciosPunto['registros'] as $objServicioPunto)
            {
                //Extraer el obj_producto para guardar la caracteristica
                $intIdProducto = $objServicioPunto->getProductoId()->getId();
                $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                        ->find($intIdProducto);
                
                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                 ->find($objServicioPunto->getId());                       
                                        
                // Si es de los productos adicionales procedemos a activarlo
                $objProdServicio = $objServicioPunto->getProductoId();
               
                if (!empty($objProdServicio)) 
                {
                    foreach($arrayListadoServicios as $objListado)
                    {
                        // Activacion primero en Konibit si el productos tiene esa caracteristica
                        if ($objProdServicio->getId() == $objListado['valor1'])
                        {
                            $arrayResKonibit = array();
                            $strObsProdAdicional = "Se activa servicio adicional con servicio de internet";
                            if ($objListado['valor3'] == "SI")
                            {
                                $intContError = 0;
                                $strMensajeCorreo = "";
                                for ($intIntento = 1; $intIntento <= $intMaxIntentos; $intIntento++) 
                                {
                                    $arrayResKonibit = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->notificarKonibit(array ('intIdServicio'  => $objServicioPunto->getId(),
                                                                                    'strTipoProceso' => 'ACTIVAR',
                                                                                    'strTipoTrx'     => 'INDIVIDUAL',
                                                                                    'strUsuario'     => $strUserCreacion,
                                                                                    'strIp'          => $strIpCreacion,
                                                                                    'objUtilService' => $this->utilService));
                                    $strObsProdAdicional = "Se activa producto adicional en konibit";
                                    $strKonibit = $arrayResKonibit['status'];
                                    if ((!empty($strKonibit) && $strKonibit == "ok"))
                                    { 
                                        //Se obtiene el registro de la caracteristica del producto 
                                        $objProdCaractKonibit   = $this->servicioGeneral
                                                                  ->getServicioProductoCaracteristica
                                                                  ($objServicio,
                                                                   'ACTIVO KONIBIT',
                                                                    $objProducto);                                                      
                                        if (is_object($objProdCaractKonibit) && $objProdCaractKonibit->getValor() != "SI") 
                                        {
                                        //Se actualiza la caracteristica "ACTIVO KONIBIT SI" luego que el producto adicional se activa            
                                        $this->serviceLicKaspersky
                                        ->actualizarServicioProductoCaracteristica(
                                                        array("objServicio" => $objServicio,
                                                        "strUsrCreacion"    => $strUserCreacion,
                                                        "objProducto"       => $objProducto,
                                                        "strCaracteristica" => "ACTIVO KONIBIT",
                                                        "strValorNuevo"     => "SI"));    
                                        } 
                                        else if (!is_object($objProdCaractKonibit))
                                        {
                                        //Se guarda la caracteristica "ACTIVO KONIBIT SI" en caso que no exista  
                                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                                        $objProducto,
                                                                                                        "ACTIVO KONIBIT",
                                                                                                        "SI",
                                                                                                        $strUserCreacion);
                                        }

                                    }
                                    else
                                    {
                                        $objServHistKonibit = new InfoServicioHistorial();
                                        $objServHistKonibit->setServicioId($objServicioPunto);
                                        $objServHistKonibit->setObservacion("“El producto adicional no se activó en Konibit” ".$intIntento.
                                                                            " intento, motivo: ".$arrayResKonibit['message']);
                                        $objServHistKonibit->setEstado("Pendiente");
                                        $objServHistKonibit->setUsrCreacion($strUserCreacion);
                                        $objServHistKonibit->setFeCreacion(new \DateTime('now'));
                                        $objServHistKonibit->setIpCreacion($strIpCreacion);
                                        $this->emComercial->persist($objServHistKonibit);
                                        $this->emComercial->flush();
                                        
                                        //Se obtiene el registro de la caracteristica del producto para actualizar o ingresar en caso que no exista
                                        $objProdCaractKonibit   = $this->servicioGeneral
                                                                  ->getServicioProductoCaracteristica
                                                                  ($objServicio,
                                                                   'ACTIVO KONIBIT',
                                                                   $objProducto);                                                      
                                   
                                        if (is_object($objProdCaractKonibit) && $objProdCaractKonibit->getValor() != "NO")
                                        {
                                            //Se actualiza la caracteristica "ACTIVO KONIBIT a NO" luego que el producto adicional no se activa    
                                            $this->serviceLicKaspersky
                                            ->actualizarServicioProductoCaracteristica(
                                                                        array("objServicio" => $objServicio,
                                                                        "strUsrCreacion"    => $strUserCreacion,
                                                                        "objProducto"       => $objProducto,
                                                                        "strCaracteristica" => "ACTIVO KONIBIT",
                                                                        "strValorNuevo"     => "NO")); 

                                        }
                                        else if (!is_object($objProdCaractKonibit))
                                        { 
                                            //Se guarda la caracteristica "ACTIVO KONIBIT NO" 
                                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                                          $objProducto,
                                                                                                          "ACTIVO KONIBIT",
                                                                                                          "NO",
                                                                                                          $strUserCreacion);
                                        } 

                                        $intContError++;
                                        $strRegitro = date('d-m-Y h:i:s a', time()).' - '.$arrayResKonibit['message'];
                                        $strMensajeCorreo = $strMensajeCorreo.'<tr>'.'<td>'.$strRegitro.'</td>'.'</tr>';
                                        if ($intContError == $intMaxIntentos)
                                        {
                                            // Envio de mail cuando falla los reintentos
                                            $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                                ->findOneById($objServicioPunto->getPuntoId()->getId());
                                            $strAsunto = "Error al activar el producto ".$objProdServicio->getDescripcionProducto();
                                            $arrayDestinatarios = null;
                                            $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                                    'Lista de correos a enviar para error en konibit',
                                                                    '','','','','',$intCodEmpresa);
                                            if(is_array($arrayValoresParametros) && !empty($arrayValoresParametros))
                                            {
                                                $arrayDestinatarios = $this->utilService->obtenerValoresParametro($arrayValoresParametros);
                                            }
                                            // Enviamos correo para incluir el error
                                            $arrayParamClientes = array(
                                                'idServicio'        => $objServicioPunto->getId(),
                                                'booleanEsProducto' => true
                                            );
                                            $objCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                    ->getDatosClienteDelPuntoPorIdServicio($arrayParamClientes);
                                            $strCliente = $objCliente['NOMBRES']. " con identificacion ".$objCliente['IDENTIFICACION_CLIENTE'];
                                            $arrayParametrosMail = array(
                                                "cliente"  => $strCliente,
                                                "login"    => $objPunto->getLogin(),
                                                "producto" => $objProdServicio->getDescripcionProducto(),
                                                "mensaje"  => $strMensajeCorreo
                                            );

                                            $this->envioPlantillaService->generarEnvioPlantilla(
                                                $strAsunto,
                                                $arrayDestinatarios,
                                                'NOT_ERR_KON',
                                                $arrayParametrosMail,
                                                $intCodEmpresa,
                                                '',
                                                '',
                                                null,
                                                false,
                                                'notificaciones_telcos@telconet.ec'
                                            );
                                        }
                                    }
                                }
                            }
                            $strKonibit = $arrayResKonibit['status'];
                            if ((!empty($strKonibit) && $strKonibit == "ok") ||
                                $objListado['valor3'] == "NO")
                            {
                                $objServicioPunto->setEstado('Activo');
                                $this->emComercial->persist($objServicioPunto);

                                $objServHistorial = new InfoServicioHistorial();
                                $objServHistorial->setServicioId($objServicioPunto);
                                $objServHistorial->setObservacion($strObsProdAdicional);
                                $objServHistorial->setEstado("Activo");
                                // Se valida que si viene por traslado no cree la accion
                                if ($objServicioPunto->getTipoOrden() == 'T')
                                {
                                    $objServHistorial->setAccion("");
                                }
                                else
                                {
                                    $objServHistorial->setAccion($strAccion);
                                }
                                $objServHistorial->setUsrCreacion($strUserCreacion);
                                $objServHistorial->setFeCreacion(new \DateTime('now'));
                                $objServHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServHistorial);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Funcion que permite activar productos adicionales automaticos, que se
     * deberan activar con el servicio de internet de forma simultanea
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 29-07-2021 - Version inicial
     * 
     * @author Jessenia Piloso <jpiloso@telconet.ec>
     * @version 1.2 27-07-2022 - Se modifica el ingreso y la actualizacion de las caracteristicas para productos konibit, es decir,
     *                           si el producto konibit se activa y ya existe la caracteristica se actualiza, si no existe se ingresa.
     * @param array $arrayDatosParametros
     * 
    */
    public function activarProdKonitIncluidos($arrayDatosParametros)
    {
        $objServicio     = $arrayDatosParametros['objServicio'];
        $intCodEmpresa   = $arrayDatosParametros['intCodEmpresa'];
        $strIpCreacion   = $arrayDatosParametros['strIpCreacion'];
        $strUserCreacion = $arrayDatosParametros['strUserCreacion'];
        $strResultado = "Ok";
        $strMensaje   = "No existieron productos para activar";

        $intIdPlan = $objServicio->getPlanId()->getId();
        $arrayDetallesPlanes  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                            ->getPlanIdYEstados($intIdPlan);
        
        if(is_array($arrayDetallesPlanes) && !empty($arrayDetallesPlanes))
        {
            // Obtenemos los productos adicionales permitidos
            $arrayListadoServicios = array();
            $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                            'Lista de productos adicionales automaticos',
                                            '','','','','',$intCodEmpresa);
            // Obtenemos la cantidad de reintentos permitidos
            $intMaxIntentos = 1;
            $arrayParamIntentos = array();
            $arrayParamIntentos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                        ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                            'Reintentos y delay para los productos incluidos',
                                            '','','','','',$intCodEmpresa);
            if(is_array($arrayParamIntentos) && !empty($arrayParamIntentos))
            {
                $arrayIntentos = $arrayParamIntentos[0];
                $intMaxIntentos = $arrayIntentos['valor1'];
            }
            $arrayResKonibit = array();
            foreach($arrayDetallesPlanes as $objDetallePlan)
            {
                $intIdProducto = $objDetallePlan->getProductoId();
                foreach($arrayListadoServicios as $objListado)
                {
                    if ($intIdProducto == $objListado['valor1'] && $objListado['valor3'] == "SI")
                    {
                        $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                            ->find($intIdProducto);
                        $intContError = 0;
                        $strMensajeCorreo = "";
                        for ($intIntento = 1; $intIntento <= $intMaxIntentos; $intIntento++) 
                        {
                            $arrayResKonibit = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->notificarKonibit(array ('intIdServicio'  => $objServicio->getId(),
                                                                            'strTipoProceso' => 'ACTIVAR',
                                                                            'strTipoTrx'     => 'INDIVIDUAL',
                                                                            'strUsuario'     => $strUserCreacion,
                                                                            'strIp'          => $strIpCreacion,
                                                                            'objUtilService' => $this->utilService));
                            $strKonibit = $arrayResKonibit['status'];
                            if ((!empty($strKonibit) && $strKonibit == "ok"))
                            {
                                $strResultado = "Ok";
                                $strMensaje   = "Producto activado con exito";
                                $objServHistKonibit = new InfoServicioHistorial();
                                $objServHistKonibit->setServicioId($objServicio);
                                $objServHistKonibit->setObservacion("Se activa producto incluido en el servicio en konibit");
                                $objServHistKonibit->setEstado("Activo");
                                $objServHistKonibit->setUsrCreacion($strUserCreacion);
                                $objServHistKonibit->setFeCreacion(new \DateTime('now'));
                                $objServHistKonibit->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServHistKonibit);
                                $this->emComercial->flush();

                                //Se obtiene el registro de la caracteristica del producto para actualizar el ACTIVO KONIBIT SI 
                                $objProdCaractKonibit   = $this->servicioGeneral
                                                          ->getServicioProductoCaracteristica
                                                          ($objServicio,
                                                           'ACTIVO KONIBIT',
                                                           $objProducto);                                                      
                                if (is_object($objProdCaractKonibit) && $objProdCaractKonibit->getValor() != "SI") 
                                {
                                    //Se actualiza la caracteristica "ACTIVO KONIBIT SI" luego que el producto adicional se activa    
                                    $this->serviceLicKaspersky
                                    ->actualizarServicioProductoCaracteristica(
                                                        array("objServicio" => $objServicio,
                                                        "strUsrCreacion"    => $strUserCreacion,
                                                        "objProducto"       => $objProducto,
                                                        "strCaracteristica" => "ACTIVO KONIBIT",
                                                        "strValorNuevo"     => "SI"));  

                                }
                                else if (!is_object($objProdCaractKonibit))
                                {
                                    //Se guarda la caracteristica "ACTIVO KONIBIT SI"   
                                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                                    $objProducto,
                                                                                                    "ACTIVO KONIBIT",
                                                                                                    "SI",
                                                                                                    $strUserCreacion);
                                }

                                
                            }
                            else
                            {
                                $intContError++;
                                $strResultado = "Error";
                                $strMensaje = $arrayResKonibit['message'];

                                $objServHistKonibit = new InfoServicioHistorial();
                                $objServHistKonibit->setServicioId($objServicio);
                                $objServHistKonibit->setObservacion("El producto incluido no se activó en Konibit ".$intIntento.
                                                                    " intento, motivo: ".$strMensaje);
                                $objServHistKonibit->setEstado("Pendiente");
                                $objServHistKonibit->setUsrCreacion($strUserCreacion);
                                $objServHistKonibit->setFeCreacion(new \DateTime('now'));
                                $objServHistKonibit->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServHistKonibit);
                                $this->emComercial->flush();

                                $strRegitro = date('d-m-Y h:i:s a', time()).' - '.$arrayResKonibit['message'];
                                $strMensajeCorreo = $strMensajeCorreo.'<tr>'.'<td>'.$strRegitro.'</td>'.'</tr>';
                                if ($intContError == $intMaxIntentos)
                                {
                                    //Se obtiene el registro de la caracteristica del producto para actualizar o ingresar en caso que no exista
                                    $objProdCaractKonibit   = $this->servicioGeneral
                                                              ->getServicioProductoCaracteristica
                                                              ($objServicio,
                                                              'ACTIVO KONIBIT',
                                                              $objProducto);                                                      

                                    if (is_object($objProdCaractKonibit) && $objProdCaractKonibit->getValor() != "NO")
                                    {
                                    //Se actualiza la caracteristica "ACTIVO KONIBIT a NO" luego que el producto adicional no se activa    
                                    $this->serviceLicKaspersky
                                    ->actualizarServicioProductoCaracteristica(
                                                        array("objServicio" => $objServicio,
                                                        "strUsrCreacion"    => $strUserCreacion,
                                                        "objProducto"       => $objProducto,
                                                        "strCaracteristica" => "ACTIVO KONIBIT",
                                                        "strValorNuevo"     => "NO"));                                                       

                                    }
                                    else if (!is_object($objProdCaractKonibit))
                                    { 
                                        //Se guarda la caracteristica "ACTIVO KONIBIT NO" 
                                        $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                                                        $objProducto,
                                                                                                        "ACTIVO KONIBIT",
                                                                                                        "NO",
                                                                                                        $strUserCreacion);
                                    } 

                                    // Envio de mail cuando falla los reintentos
                                    $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                        ->findOneById($objServicio->getPuntoId()->getId());
                                    $strAsunto = "Error al activar el producto ".$objProducto->getDescripcionProducto();
                                    $arrayDestinatarios = null;
                                    $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                            'Lista de correos a enviar para error en konibit',
                                                            '','','','','',$intCodEmpresa);
                                    if(is_array($arrayValoresParametros) && !empty($arrayValoresParametros))
                                    {
                                        $arrayDestinatarios = $this->utilService->obtenerValoresParametro($arrayValoresParametros);
                                    }
                                    $arrayParamClientes = array(
                                        'idServicio'        => $objServicio->getId(),
                                        'booleanEsProducto' => true
                                    );
                                    $objCliente = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                           ->getDatosClienteDelPuntoPorIdServicio($arrayParamClientes);
                                    $strCliente = $objCliente['NOMBRES']. " con identificacion ".$objCliente['IDENTIFICACION_CLIENTE'];
                                    $arrayParametrosMail = array(
                                        "cliente"  => $strCliente,
                                        "login"    => $objPunto->getLogin(),
                                        "producto" => $objProducto->getDescripcionProducto(),
                                        "mensaje"    => $strMensajeCorreo
                                    );

                                    $this->envioPlantillaService->generarEnvioPlantilla(
                                        $strAsunto,
                                        $arrayDestinatarios,
                                        'NOT_ERR_KON',
                                        $arrayParametrosMail,
                                        $intCodEmpresa,
                                        '',
                                        '',
                                        null,
                                        false,
                                        'notificaciones_telcos@telconet.ec'
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        $arrayResultado = array('strResultado'  => $strResultado,
                                'strMensaje'    => $strMensaje);
        return $arrayResultado;
    }

    /**
     * Función que confirma los servicios con nombres tecnicos Seg Vehiculo.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.0 30-09-2022
     * 
     * @author Axel Auza <aauza@telconet.ec>
     * @version 1.1 07-06-2023 - Se agrega validación para obtener los elementos por clientes en el producto SEG_VEHICULO
     * 
     * @param Array $arrayPeticiones
     *
     * @return Array $arrayRespuestaFinal [
     *                                      'status'    => estado de respuesta de la operación 'OK' o 'ERROR',
     *                                      'mensaje'   => mensaje de la operación o de error
     *                                   ]
     */
    public function confirmarServicioSegVehiculo($arrayPeticiones)
    {
        $intIdAccion           = $arrayPeticiones['idAccion'];
        $intIdEmpresa          = $arrayPeticiones['idEmpresa'];
        $intIdServicio         = $arrayPeticiones['idServicio'];
        $intIdTecnicoEncargado = $arrayPeticiones['idTecnicoEncargado'];
        $strUsrCreacion        = $arrayPeticiones['usrCreacion'];
        $strIpCreacion         = $arrayPeticiones['ipCreacion'];
        $arrayEquipos          = array();

        $this->emComercial->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        try
        {
            //obtengo el servicio
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha podido obtener el servicio, por favor notificar a Sistemas.");
            }
            //obtengo los datos del servicio
            $objProducto        = $objServicio->getProductoId();
            $objPunto           = $objServicio->getPuntoId();
            
            //verificar elementos
            $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PARAMETROS_SEG_VEHICULOS',
                                                'TECNICO',
                                                '',
                                                'ELEMENTOS_PRODUCTO',
                                                $objProducto->getId(),
                                                '',
                                                '',
                                                '',
                                                '',
                                                $intIdEmpresa,
                                                'valor5',
                                                '',
                                                '',
                                                '',
                                                $objPunto->getPersonaEmpresaRolId()->getId());
            if(!isset($arrayParElementos) || empty($arrayParElementos))
            {
                $arrayParElementos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->get('PARAMETROS_SEG_VEHICULOS',
                                                                  'TECNICO',
                                                                  '',
                                                                  'ELEMENTOS_PRODUCTO',
                                                                  $objProducto->getId(),
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  $intIdEmpresa,
                                                                  'valor5',
                                                                  '',
                                                                  '',
                                                                  '',
                                                                  'GENERAL');
            }
            foreach($arrayParElementos as $arrayItemParEle)
            {
                $strTituloElemento  = $arrayItemParEle['valor3'];
                $strSerieElemento   = strtoupper($arrayPeticiones['serie'.$arrayItemParEle['valor6']]);
                $strModeloElemento  = $arrayPeticiones['modelo'.$arrayItemParEle['valor6']];
                $arrayTiposElemento = explode(";",$arrayItemParEle['valor2']);
                if(empty($strSerieElemento) || empty($strModeloElemento))
                {
                    throw new \Exception("Por favor ingrese el elemento ".$strTituloElemento.".");
                }
                //validar tipo elemento
                $arrayValidModeloElemento = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                ->createQueryBuilder('s')
                                                                ->join("s.tipoElementoId", "t")
                                                                ->where("t.nombreTipoElemento     IN (:nombresTipoElemento)")
                                                                ->andWhere("s.nombreModeloElemento = :nombreModeloElemento")
                                                                ->andWhere("s.estado               = :estado")
                                                                ->setParameter('nombresTipoElemento',  $arrayTiposElemento)
                                                                ->setParameter('nombreModeloElemento', $strModeloElemento)
                                                                ->setParameter('estado',               "Activo")
                                                                ->orderBy('s.id', 'ASC')
                                                                ->getQuery()
                                                                ->getResult();
                if(!isset($arrayValidModeloElemento) || empty($arrayValidModeloElemento) || !is_array($arrayValidModeloElemento))
                {
                    throw new \Exception("El elemento ".$strTituloElemento." no es de tipo (".
                                         implode(',',$arrayTiposElemento)."), favor verificar.");
                }
                //verifivar NAF
                $arrayVerifEleNaf   = $this->servicioGeneral->buscarEquipoEnNafPorParametros(
                                                    array("serieEquipo"        => $strSerieElemento,
                                                          "estadoEquipo"       => "PI",
                                                          "tipoArticuloEquipo" => "AF",
                                                          "modeloEquipo"       => $strModeloElemento));
                if($arrayVerifEleNaf["status"] === "ERROR")
                {
                    throw new \Exception("ERROR NAF: El elemento ".$strTituloElemento.", ".$arrayVerifEleNaf["mensaje"]);
                }
                //buscar elemento NAF
                $arrayElementoNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieElemento, $strModeloElemento, "PI", "ActivarServicio");
                if($arrayElementoNaf[0]['status'] != "OK")
                {
                    throw new \Exception("ERROR NAF: El elemento ".$strTituloElemento.", ".$arrayElementoNaf[0]['mensaje']);
                }
                //almacenamos la serie del dispositivo cpe y el id de control para realizar la carga y descarga.
                $arrayInfoActivo = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                        ->obtenerEquiposAsignados(array('strIdEmpresa'   => $intIdEmpresa,
                                                                        'intIdPersona'   => $intIdTecnicoEncargado,
                                                                        'strNumeroSerie' => $strSerieElemento));
                if($arrayInfoActivo['status'])
                {
                    $arrayEquipos[] = array('strNumeroSerie'  => $strSerieElemento,
                                            'intIdControl'    => $arrayInfoActivo["result"][0]['idControl'],
                                            'intCantidadEnt'  => 1,
                                            'intCantidadRec'  => 1,
                                            'strTipoArticulo' => 'Equipos');
                }
                else
                {
                    throw new \Exception("DISPOSITIVOS CLIENTE: No se encontró el equipo asignado con serie $strSerieElemento ".
                                         "del elemento ".$strTituloElemento.".");
                }
            }

            //Generacion de Login Auxiliar del Servicio Adicional
            $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());

            //ingresar elementos
            foreach($arrayParElementos as $arrayItemParEle)
            {
                $strTituloElemento  = $arrayItemParEle['valor3'];
                $strNombreElemento  = strtolower(str_replace(" ","-",$arrayItemParEle['valor3']))."-".$objServicio->getLoginAux();
                $strSerieElemento   = strtoupper($arrayPeticiones['serie'.$arrayItemParEle['valor6']]);
                $strModeloElemento  = $arrayPeticiones['modelo'.$arrayItemParEle['valor6']];
                $strMacElemento     = $arrayPeticiones['mac'.$arrayItemParEle['valor6']];
                $arrayTiposElemento = explode(";",$arrayItemParEle['valor2']);
                //obtener modelo elemento
                $objModeloElemento  = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                ->createQueryBuilder('s')
                                                                ->join("s.tipoElementoId", "t")
                                                                ->where("t.nombreTipoElemento     IN (:nombresTipoElemento)")
                                                                ->andWhere("s.nombreModeloElemento = :nombreModeloElemento")
                                                                ->andWhere("s.estado               = :estado")
                                                                ->setParameter('nombresTipoElemento',  $arrayTiposElemento)
                                                                ->setParameter('nombreModeloElemento', $strModeloElemento)
                                                                ->setParameter('estado',               "Activo")
                                                                ->orderBy('s.id', 'ASC')
                                                                ->setMaxResults(1)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                //ingresar elemento
                $arrayParIngresoElemento = array(
                                            'nombreElementoCliente' => $strNombreElemento,
                                            'modeloElementoNuevo'   => $strModeloElemento,
                                            'serieElementoNuevo'    => $strSerieElemento,
                                            'descEquipo'            => "",
                                            'tipoElementoNuevo'     => $objModeloElemento->getTipoElementoId()->getNombreTipoElemento(),
                                            'macElementoRegistrar'  => $strMacElemento,
                                            'objServicio'           => $objServicio,
                                            'idEmpresa'             => $intIdEmpresa,
                                            'usrCreacion'           => $strUsrCreacion,
                                            'ipCreacion'            => $strIpCreacion,
                                            'booleanSinInterface'   => $arrayItemParEle['valor4'] === "S" ? false : true,
                                            'booleanInterfaceNotWan' => $arrayItemParEle['valor4'] === "S",
                                            'bandRegistroEquipo'    => "S",
                                            'propiedadEquipo'       => "TELCONET"
                                        );
                $objElementoCliente = $this->servicioGeneral->ingresarElementoClienteTNSinEnlace($arrayParIngresoElemento);
                //insert caracteristica de relacion del cliente
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                               $objProducto,
                                                                               "ELEMENTO_CLIENTE_ID",
                                                                               $objElementoCliente->getId(),
                                                                               $strUsrCreacion);
                //ingresar elemento NAF
                $strIdentificacionCliente = "";
                $strCodigoArticuloOnt     = "";
                $strTipoArticulo          = "AF";
                $strMensajeError    = str_repeat(' ', 1000);
                $strSql             = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                                    . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
                                    . ":cantidad, :pv_mensajeerror); END;";
                $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                $objStmt->bindParam('codigoEmpresaNaf', $intIdEmpresa);
                $objStmt->bindParam('codigoArticulo', $strCodigoArticuloOnt);
                $objStmt->bindParam('tipoArticulo',$strTipoArticulo);
                $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                $objStmt->bindParam('serieCpe', $strSerieElemento);
                $objStmt->bindParam('cantidad', intval(1));
                $objStmt->bindParam('pv_mensajeerror', $strMensajeError);
                $objStmt->execute();
                if(strlen(trim($strMensajeError))>0)
                {
                    throw new \Exception("ERROR NAF: ".$strMensajeError);
                }
                //ingreso historial
                $strObservacionServicioOnt = "Informaci&oacute;n del Elemento ".$strTituloElemento."<br/>";
                $strObservacionServicioOnt .= "Nuevo ".$strTituloElemento." <br/>";
                $strObservacionServicioOnt .= "Nombre: ".$strNombreElemento."<br/>";
                $strObservacionServicioOnt .= "Serie: ".$strSerieElemento."<br/>";
                $strObservacionServicioOnt .= "Modelo: ".$strModeloElemento."<br/>";
                if($arrayItemParEle['valor4'] == "S")
                {
                    $strObservacionServicioOnt .= "Mac: ".$strMacElemento."<br/>";
                }
                $this->servicioGeneral->ingresarServicioHistorial($objServicio, "Activo", $strObservacionServicioOnt,
                                                                  $strUsrCreacion, $strIpCreacion);
            }

            //LLAMADA AL SERVICE PARA REALIZAR LA CARGA Y DESCARGA DEL ACTIVO.
            if (!empty($arrayEquipos) && count($arrayEquipos) > 0)
            {
                $arrayCargaDescarga['strUsuario']              =  $strUsrCreacion;
                $arrayCargaDescarga['strIpUsuario']            =  $strIpCreacion;
                $arrayCargaDescarga['strTipoRecibe']           = 'Cliente';
                $arrayCargaDescarga['intIdServicio']           =  $objServicio->getId();
                $arrayCargaDescarga['intIdEmpleado']           =  $intIdTecnicoEncargado;
                $arrayCargaDescarga['intIdEmpresa']            =  $intIdEmpresa;
                $arrayCargaDescarga['strTipoActividad']        = 'Instalacion';
                $arrayCargaDescarga['strTipoTransaccion']      = 'Nuevo';
                $arrayCargaDescarga['strObservacion']          = 'Instalacion del servicio';
                $arrayCargaDescarga['arrayEquipos']            =  $arrayEquipos;
                $arrayCargaDescarga['strEstadoSolicitud']      = 'Asignada';
                $arrayCargaDescarga['strDescripcionSolicitud'] = 'SOLICITUD PLANIFICACION';
                $this->serviceInfoElemento->cargaDescargaActivos($arrayCargaDescarga);
            }

            //actualizo el estado del servicio
            $objServicio->setEstado("Activo");
            $objServicio->setObservacion("Se Confirmo el Servicio");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            //actualizo el estado del punto
            $objPunto->setEstado("Activo");
            $this->emComercial->persist($objPunto);
            $this->emComercial->flush();
            //obtengo la accion
            $objAccion  = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se Confirmo el Servicio");
            $objServicioHistorial->setEstado("Activo");
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $objServicioHistorial->setAccion($objAccion->getNombreAccion());
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            //seteo punto facturacion
            $objPuntoFacturacion = $objServicio->getPuntoFacturacionId();
            if(!is_object($objPuntoFacturacion))
            {
                $objServicio->setPuntoFacturacionId($objPunto);
                $objPuntoAdicional  = $this->emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                                        ->findOneBy(array( "puntoId" => $objPunto->getId()));
                if(is_object($objPuntoAdicional))
                {
                    $objPuntoAdicional->setEsPadreFacturacion("S");
                    $this->emComercial->persist($objPuntoAdicional);
                    $this->emComercial->flush();
                }
            }
            //finalizar solicitud planificacion
            $objTipoSolicitudPlanficacion = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                              ->findOneBy(array("descripcionSolicitud"=>"SOLICITUD PLANIFICACION",
                                                                "estado"=>"Activo"));
            $objSolicitudPlanficacion     = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->findOneBy(array("servicioId"=>$objServicio->getId(),
                                                            "tipoSolicitudId"=>$objTipoSolicitudPlanficacion->getId(),
                                                            "estado"=>"AsignadoTarea"));
            if(is_object($objSolicitudPlanficacion))
            {
                $objSolicitudPlanficacion->setEstado("Finalizada");
                $this->emComercial->persist($objSolicitudPlanficacion);
                $this->emComercial->flush();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->flush();
                $this->emComercial->commit();
            }
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->flush();
                $this->emSoporte->commit();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->flush();
                $this->emInfraestructura->commit();
            }
            $strStatus  = "OK";
            $strMensaje = "OK";
        }
        catch (\Exception $e) 
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->rollback();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->utilService->insertError("Telcos+",
                                            "InfoActivarPuertoService->confirmarServicioSegVehiculo",
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );

        }

        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
    }
      

    /**
     * Funcion que permite activar productos adicionales automaticos, que se deberan activar con el servicio de internet de forma simultanea
     * y validar los productos Konibit que vienen por una activación por traslado para consumir el MS updatePunto.
     * 
     * @author José Candelario <jcandelario@telconet.ec>
     * @version 1.0 27-07-2021 - Version inicial
     * 
     * @param array $arrayDatosParametros
     * 
    */
    public function activarProductosAdicionalesActivos($arrayDatosParametros)
    {
        $intIdPunto       = $arrayDatosParametros['intIdPunto'];
        $intCodEmpresa    = $arrayDatosParametros['intCodEmpresa'];
        $strIpCreacion    = $arrayDatosParametros['strIpCreacion'];
        $strUserCreacion  = $arrayDatosParametros['strUserCreacion'];
        $strAccion        = $arrayDatosParametros['strAccion'];
        $strLoginOrigen   = $arrayDatosParametros['strLoginOrigen'];
        $intIdPuntoOrigen = $arrayDatosParametros['intIdPuntoOrigen'];
        $intContProdKon   = $arrayDatosParametros['intContProdKon'];
        // Seleccionamos los estados permitidos para activar los productos adicionales
        $objParamEstado = null;
        $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                            ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                'Estados permitidos para los productos adicionales',
                                '','','','','',$intCodEmpresa);
        if(is_array($arrayValoresParametros) && !empty($arrayValoresParametros))
        {
            $objParamEstado = $arrayValoresParametros[0];
        }
        // Obtenemos los productos adicionales permitidos
        $arrayListadoServicios = array();
        $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                        'Lista de productos adicionales automaticos',
                                        '','','','','',$intCodEmpresa);
        // Obtenemos los servicios
        $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findServiciosByPuntoAndEstado($intIdPunto,$objParamEstado['valor2'],null);
        
        if (!empty($arrayServiciosPunto))
        {
            foreach($arrayServiciosPunto['registros'] as $objServicioPunto)
            {
                $objServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->find($objServicioPunto->getId());
                if (is_object($objServicio->getProductoId()))
                {
                    $intIdProducto      = $objServicio->getProductoId()->getId();
                    $objProducto        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                            ->find($intIdProducto);
                    // Si es de los productos adicionales procedemos a activarlo

                    if (!empty($objProducto)) 
                    {
                        foreach($arrayListadoServicios as $objListado)
                        {
                            // Activacion primero en Konibit si el producto tiene esa caracteristica
                            if ($objProducto->getId() == $objListado['valor1'])
                            {
                                $strObsProdAdicional = "Se activa servicio adicional con servicio de internet";
                                //INI VALIDACIONES KONIBIT
                                $strTelefono           = "";
                                $strCorreo             = "";
                                $arrayTokenCas         = array();
                                $arrayEnvioKonibit     = array();
                                $arrayContratoProd     = array();
                                $arrayKonibit          = array();
                                if ($objListado['valor3'] == "SI")
                                {
                                    $intContProdKon             = $intContProdKon + 1;
                                    if ($intContProdKon > 1)
                                    {
                                        $strLoginOrigen   = $objServicio->getPuntoId()->getLogin();
                                        $intIdPuntoOrigen = $objServicio->getPuntoId()->getId();
                                    }
                                    $arrayCaracTraslado     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy(array("descripcionCaracteristica" => "TRASLADO", 
                                                                                     "estado"                    => "Activo"));
                                    $objProductoInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                   ->findOneBy(array("esPreferencia" => "SI",
                                                                                     "nombreTecnico" => "INTERNET",
                                                                                     "empresaCod"    => $intCodEmpresa,
                                                                                     "estado"        => "Activo"));
                                    $objCarTras             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                   ->findOneBy(array("productoId"       => $objProductoInternet->getId(), 
                                                                                     "caracteristicaId" => $arrayCaracTraslado->getId()));
                                    $objTrasladoServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                   ->findOneBy(array("servicioId"                => $objServicio->getId(), 
                                                                                     "productoCaracterisiticaId" => $objCarTras->getId()));

                                    if(is_object($objTrasladoServicio))
                                    {
                                        $intIdServicioOrigen    = $objTrasladoServicio->getValor();
                                        $objServicioOrigen      = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                    ->find($intIdServicioOrigen);
                                        if(is_object($objServicioOrigen))
                                        {
                                            $arrayParametroKnb          = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                          ->getOne('INVOCACION_KONIBIT_ACTUALIZACION', 
                                                                                                   'TECNICO', 
                                                                                                   'DEBITOS',
                                                                                                   'WS_KONIBIT', 
                                                                                                   '','','','','',$intCodEmpresa);
                                            $arrayTokenCas              = $this->serviceTokenCas->generarTokenCas();
                                            $arrayCorreoPto             = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                                            ->findContactosByPunto($objServicio->getPuntoId()
                                                                                                                               ->getLogin(), 
                                                                                                                   'Correo Electronico');
                                            foreach ($arrayCorreoPto as $arrayCorreo) 
                                            {
                                                $strCorreo = $arrayCorreo['valor'];
                                                break;
                                            }
                                            $arrayFpTelf                = array("Telefono Movil", 
                                                                                "Telefono Movil Claro", 
                                                                                "Telefono Movil Movistar", 
                                                                                "Telefono Movil CNT");
                                            foreach ($arrayFpTelf as $strFp)
                                            {
                                                $arrayContactosTelf     = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                                            ->findContactosByPunto($objServicio->getPuntoId()
                                                                                                                               ->getLogin(), 
                                                                                                                   $strFp);
                                                foreach ($arrayContactosTelf as $arrayContactoT) 
                                                {
                                                    $strTelefono = $arrayContactoT['valor'];
                                                    break;
                                                }
                                                if ($strTelefono)
                                                {
                                                    break;
                                                }
                                            }
                                            //PRODUCTOS
                                            $objProductos               = array('orderID'      => $objServicioOrigen->getId(),
                                                                                'productSKU'   => $objProducto->getCodigoProducto(),
                                                                                'productName'  => $objProducto->getDescripcionProducto(),
                                                                                'quantity'     => '1',
                                                                                'included'     => false,
                                                                                'productoId'   => $objProducto->getId(),
                                                                                'migrateTo'    => $objServicio->getId(),
                                                                                'status'       => 'active'
                                                                               );

                                            $arrayContratoProd[]        = $objProductos;
                                            //DATA
                                            $objDataProductos           = array('companyName'   => $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getRazonSocial() ?
                                                                                                   $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getRazonSocial() :
                                                                                                   $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getNombres().
                                                                                                   ' '.$objServicio->getPuntoId()
                                                                                                                   ->getPersonaEmpresaRolId()
                                                                                                                   ->getPersonaId()
                                                                                                                   ->getApellidos(),
                                                                                'companyCode'   => $objServicio->getPuntoId()->getId(),
                                                                                'companyID'     => $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getIdentificacionCliente(),
                                                                                'contactName'   => $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getRazonSocial() ?
                                                                                                   $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getRazonSocial() :
                                                                                                   $objServicio->getPuntoId()
                                                                                                               ->getPersonaEmpresaRolId()
                                                                                                               ->getPersonaId()
                                                                                                               ->getNombres().
                                                                                                   ' '.$objServicio->getPuntoId()
                                                                                                                   ->getPersonaEmpresaRolId()
                                                                                                                   ->getPersonaId()
                                                                                                                   ->getApellidos(),
                                                                                'email'         => $strCorreo,
                                                                                'phone'         => $strTelefono,
                                                                                'login'         => $objServicio->getPuntoId()->getLogin(),
                                                                                'plan'          => $objServicio->getProductoId()
                                                                                                               ->getDescripcionProducto(),
                                                                                'address'       => $objServicio->getPuntoId()->getDireccion(),
                                                                                'city'          => $objServicio->getPuntoId()
                                                                                                               ->getPuntoCoberturaId()
                                                                                                               ->getNombreJurisdiccion(),
                                                                                'sector'        => $objServicio->getPuntoId()
                                                                                                               ->getSectorId()
                                                                                                               ->getNombreSector(),
                                                                                'status'        => 'active',
                                                                                'products'      => $arrayContratoProd
                                                                               );
                                            //DATA
                                            $arrayData                  = array('action'        => ( isset($arrayParametroKnb["valor5"]) && 
                                                                                                     !empty($arrayParametroKnb["valor5"]) )
                                                                                                     ? $arrayParametroKnb["valor5"] : "",
                                                                                'partnerID'     => (isset($arrayParametroKnb["valor7"]) &&
                                                                                                    !empty($arrayParametroKnb["valor7"]) )
                                                                                                    ? $arrayParametroKnb["valor7"] : "001",
                                                                                'companyCode'   => $intIdPuntoOrigen,
                                                                                'companyID'     => $objServicioOrigen->getPuntoId()
                                                                                                                     ->getPersonaEmpresaRolId()
                                                                                                                     ->getPersonaId()
                                                                                                                     ->getIdentificacionCliente(),
                                                                                'contactName'   => $objServicioOrigen->getPuntoId()
                                                                                                                     ->getPersonaEmpresaRolId()
                                                                                                                     ->getPersonaId()
                                                                                                                     ->getRazonSocial() ?
                                                                                                   $objServicioOrigen->getPuntoId()
                                                                                                                     ->getPersonaEmpresaRolId()
                                                                                                                     ->getPersonaId()
                                                                                                                     ->getRazonSocial() :
                                                                                                   $objServicioOrigen->getPuntoId()
                                                                                                                     ->getPersonaEmpresaRolId()
                                                                                                                     ->getPersonaId()
                                                                                                                     ->getNombres().
                                                                                                   ' '.$objServicioOrigen->getPuntoId()
                                                                                                                         ->getPersonaEmpresaRolId()
                                                                                                                         ->getPersonaId()
                                                                                                                         ->getApellidos(),
                                                                                'login'         => $strLoginOrigen,
                                                                                'data'          => $objDataProductos,
                                                                                'requestNumber' => '1',
                                                                                'timestamp'     => ''
                                                                                );

                                            $arrayKonibit               = array('identifier'    => $objServicio->getId(),
                                                                                'type'          => ( isset($arrayParametroKnb["valor4"]) && 
                                                                                                     !empty($arrayParametroKnb["valor4"]) )
                                                                                                     ? $arrayParametroKnb["valor4"] : "",
                                                                                'retryRequered' => true,
                                                                                'process'       => ( isset($arrayParametroKnb["valor6"]) && 
                                                                                                     !empty($arrayParametroKnb["valor6"]) )
                                                                                                     ? $arrayParametroKnb["valor6"] : "",
                                                                                'origin'        => ( isset($arrayParametroKnb["valor2"]) && 
                                                                                                     !empty($arrayParametroKnb["valor2"]) )
                                                                                                     ? $arrayParametroKnb["valor2"] : "",
                                                                                'user'          => $strUserCreacion,
                                                                                'uri'           => ( isset($arrayParametroKnb["valor1"]) && 
                                                                                                     !empty($arrayParametroKnb["valor1"]) )
                                                                                                     ? $arrayParametroKnb["valor1"] : "",
                                                                                'executionIp'   => $strIpCreacion,
                                                                                'data'          => $arrayData
                                                                                );


                                            $arrayEnvioKonibit          = array('strToken'         => $arrayTokenCas['strToken'],
                                                                                'strUser'          => $strUserCreacion,
                                                                                'strIp'            => $strIpCreacion,
                                                                                'arrayPropiedades' => $arrayKonibit);
                                            $this->serviceKonibit->envioAKonibit($arrayEnvioKonibit);
                                        }
                                        else
                                        {
                                            $this->utilService->insertError('Telcos+', 
                                                                            'InfoConfirmarServicioService->activarProductosAdicionalesActivos', 
                                                                            'Error: No se ha podido obtener el servicio origen del servicio actual: '.
                                                                            $objServicioPunto->getId(),
                                                                            'envioKonibit', 
                                                                            $strIpCreacion);
                                        }
                                    }
                                    else
                                    {
                                        $this->utilService->insertError('Telcos+', 
                                                                        'InfoConfirmarServicioService->activarProductosAdicionalesActivos', 
                                                                        'Error: No existe la característica de Traslado para el servicio actual: '.
                                                                        $objServicioPunto->getId(),
                                                                        'envioKonibit', 
                                                                        $strIpCreacion);
                                    }

                                }

                                $objServicioPunto->setEstado('Activo');
                                $this->emComercial->persist($objServicioPunto);

                                $objServHistorial = new InfoServicioHistorial();
                                $objServHistorial->setServicioId($objServicioPunto);
                                $objServHistorial->setObservacion($strObsProdAdicional);
                                $objServHistorial->setEstado("Activo");
                                // Se valida que si viene por traslado no cree la accion
                                if ($objServicioPunto->getTipoOrden() == 'T')
                                {
                                    $objServHistorial->setAccion("");
                                }
                                else
                                {
                                    $objServHistorial->setAccion($strAccion);
                                }
                                $objServHistorial->setUsrCreacion($strUserCreacion);
                                $objServHistorial->setFeCreacion(new \DateTime('now'));
                                $objServHistorial->setIpCreacion($strIpCreacion);
                                $this->emComercial->persist($objServHistorial);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Funcion que permite confirmar el servicio del producto SAFE ENTRY
     * @param array [idAccion  => 'Identificador de la accion'
     *               idEmpresa => 'Codigo de la empresa'
     *               idServicio
     *               idTecnicoEncargado
     *               usrCreacion
     *               ipCreacion']
     * 
     * @return array [ status => 'OK | ERROR'
     *                 mensaje]
     * @author Leonardo Mero <lemero@telconet.ec>
     * @version 1.0 20-09-2022 - Version inicial
     */
    public function confirmarServicioSafeEntry($arrayParametros)
    {
        $intIdAccion           = $arrayParametros['idAccion'];
        $intIdEmpresa          = $arrayParametros['idEmpresa'];
        $intIdServicio         = $arrayParametros['idServicio'];
        $intIdTecnicoEncargado = $arrayParametros['idTecnicoEncargado'];
        $strUsrCreacion        = $arrayParametros['usrCreacion'];
        $strIpCreacion         = $arrayParametros['ipCreacion'];
        $strServiciosRequeridos   = "";
        $arrayEquipos          = array();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();

        try 
        {
            //Se obtiene el servicio
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha podido obtener el servicio, por favor notificar a Sistemas.");
            }


            //obtengo los datos del servicio
            $objProducto = $objServicio->getProductoId();
            $objPunto    = $objServicio->getPuntoId();

            $arrayParametrosSafe = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('CONFIG SAFE ENTRY',
                                                        'COMERCIAL',
                                                        '',
                                                        'SERVICIOS_REQUERIDOS',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $intIdEmpresa);
            
            if(!is_array($arrayParametrosSafe))
            {
                throw new \Exception('No se ha podido obtener el parametro para realizar la validacion del servicio.');
            }

            //Array de servicios requeridos
            $arrayServiciosRequeridos = array_diff(json_decode($arrayParametrosSafe['valor1']), array($objProducto->getDescripcionProducto()));

            foreach($arrayServiciosRequeridos as $strServicio)
            {
                //Se verfica que los servicio requeridos esten activos en el punto
                $objProductoRequerido = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                        ->findOneBy(array('descripcionProducto' => $strServicio,
                                                          'estado'        => 'Activo'),
                                                          array('id'=> 'ASC' ));
                
                $objServicioPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findOneBy(array('puntoId'    => $objPunto->getId(),
                                                      'productoId' => $objProductoRequerido->getId(),
                                                      'estado'     => 'Activo'));
                 
                if(!isset($objServicioPunto))
                {
                    $strServiciosRequeridos .='<li><b>'.$objProductoRequerido->getDescripcionProducto().'</b></li>';
                    $strMensaje = 'Para activar el servicio '.$objProducto->getNombreTecnico().' se requiere de los siguientes servicios: </b>'
                    .'<ul>'.$strServiciosRequeridos.'</ul> en estado: Activo.';
                    throw new \Exception($strMensaje);
                }

                //Reservamos la informacion del siguiente servicio
                if($strServicio == 'Internet Small Business')
                {
                    $objServicioISB = $objServicioPunto;
                    $objIpISB = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                               ->findOneBy(array( 'servicioId' => $objServicioISB->getId()));

                }
                
            }

            //CONSUMO DEL WS SEAPP
            $arrayDatosConsumo = array(
                'strProceso' => 'Creacion',
                'objServicio' => $objServicio,
                'strUser' => $strUsrCreacion,
                'strIpCreacion' => $strIpCreacion,
                'intIdEmpresa'=> $intIdEmpresa,
                'strIpISB' => $objIpISB->getIp());
                
            $arrayRespuestaWS = $this->serviceInvestigacionDesarrollo->consumoSafeEntryIDWs($arrayDatosConsumo);

            //Se ingresa la respuesta del WS
            $this->servicioGeneral->ingresarServicioHistorial( $objServicio,'Activo',
                                    'Consumo WS Investigación Desarrollo: '.$arrayRespuestaWS['status'].': '.$arrayRespuestaWS['mensaje'],
                                    $strUsrCreacion,$strIpCreacion);
            

            //Obtenemos los elementos del producto SAFE ENTRY
            $objParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                        ->findOneBy(array('nombreParametro' => 'CONFIG ELEMENTOS SAFE ENTRY',
                                                         'modulo' => 'TECNICO',
                                                         'estado' => 'Activo'));
            if(!is_object($objParametroCab))
            {   
                throw new \Exception('No se ha podido obtener el parametro de configuracion de los elementos SAFE ENTRY');
            }
            $arrayParElementos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                 ->findBy(array('parametroId'=>$objParametroCab->getId(),
                                                               'descripcion'=> 'ELEMENTOS_SAFE_ENTRY',
                                                               'estado'=> 'Activo',
                                                                ), array('valor7'=> 'ASC'));

            if(!is_array($arrayParElementos))
            {   
                throw new \Exception('No se ha podido obtener el parametro para realizar la validacion de los elementos Safe Entry.');
            }

            foreach($arrayParElementos as $objParElemento)
            {
                $strTituloElemento  = $objParElemento->getValor2();
                $strSerieElemento   = $arrayParametros['serie'.$objParElemento->getValor5()];
                $strModeloElemento  = $arrayParametros['modelo'.$objParElemento->getValor5()];
                $arrayTiposElemento = json_decode($objParElemento->getValor3());
                if(empty($strSerieElemento) || empty($strModeloElemento))
                {
                    throw new \Exception("Por favor ingrese el elemento ".$strTituloElemento.".");
                }
                //validar tipo elemento
                $arrayValidModeloElemento = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                ->createQueryBuilder('s')
                                                                ->join("s.tipoElementoId", "t")
                                                                ->where("t.nombreTipoElemento     IN (:nombresTipoElemento)")
                                                                ->andWhere("s.nombreModeloElemento = :nombreModeloElemento")
                                                                ->andWhere("s.estado               = :estado")
                                                                ->setParameter('nombresTipoElemento',  $arrayTiposElemento)
                                                                ->setParameter('nombreModeloElemento', $strModeloElemento)
                                                                ->setParameter('estado',               "Activo")
                                                                ->orderBy('s.id', 'ASC')
                                                                ->getQuery()
                                                                ->getResult();
                if(!isset($arrayValidModeloElemento) || empty($arrayValidModeloElemento) || !is_array($arrayValidModeloElemento))
                {
                    throw new \Exception("El elemento ".$strTituloElemento." no es de tipo (".
                                         implode(',',$arrayTiposElemento)."), favor verificar.");
                }
                //verifivar NAF
                $arrayVerifEleNaf   = $this->servicioGeneral->buscarEquipoEnNafPorParametros(
                                                    array("serieEquipo"        => $strSerieElemento,
                                                          "estadoEquipo"       => "PI",
                                                          "tipoArticuloEquipo" => "AF",
                                                          "modeloEquipo"       => $strModeloElemento));
                if($arrayVerifEleNaf["status"] === "ERROR")
                {
                    throw new \Exception("ERROR NAF: El elemento ".$strTituloElemento.", ".$arrayVerifEleNaf["mensaje"]);
                }
                //buscar elemento NAF
                $arrayElementoNaf = $this->servicioGeneral->buscarElementoEnNaf($strSerieElemento, $strModeloElemento, "PI", "ActivarServicio");
                if($arrayElementoNaf[0]['status'] != "OK")
                {
                    throw new \Exception("ERROR NAF: El elemento ".$strTituloElemento.", ".$arrayElementoNaf[0]['mensaje']);
                }
                //almacenamos la serie del dispositivo cpe y el id de control para realizar la carga y descarga.
                $arrayInfoActivo = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                                   ->obtenerEquiposAsignados(array('strIdEmpresa'   => $intIdEmpresa,
                                                                   'intIdPersona'   => $intIdTecnicoEncargado,
                                                                   'strNumeroSerie' => $strSerieElemento));
                if($arrayInfoActivo['status'])
                {
                    $arrayEquipos[] = array('strNumeroSerie'  => $strSerieElemento,
                                            'intIdControl'    => $arrayInfoActivo["result"][0]['idControl'],
                                            'intCantidadEnt'  => 1,
                                            'intCantidadRec'  => 1,
                                            'strTipoArticulo' => 'Equipos');
                }
                else
                {
                    throw new \Exception("DISPOSITIVOS CLIENTE: No se encontró el equipo asignado con serie $strSerieElemento ".
                                         "del elemento ".$strTituloElemento.".");
                }
            }

            //Generacion de Login Auxiliar del Servicio Adicional
            $this->servicioGeneral->generarLoginAuxiliar($objServicio->getId());

            //ingresar elementos
            foreach($arrayParElementos as $objParElemento)
            {
                $strTituloElemento  = $objParElemento->getValor2();
                $strNombreElemento  = strtolower(str_replace(" ","-",$objParElemento->getValor2()))."-".$objServicio->getLoginAux();
                $strSerieElemento   = $arrayParametros['serie'.$objParElemento->getValor5()];
                $strModeloElemento  = $arrayParametros['modelo'.$objParElemento->getValor5()];
                $strMacElemento     = $arrayParametros['mac'.$objParElemento->getValor5()];
                $strRequiereEnlace  = $objParElemento->getValor8();
                $arrayTiposElemento = json_decode($objParElemento->getValor3());
                //obtener modelo elemento
                $objModeloElemento  = $this->emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")
                                                                ->createQueryBuilder('s')
                                                                ->join("s.tipoElementoId", "t")
                                                                ->where("t.nombreTipoElemento     IN (:nombresTipoElemento)")
                                                                ->andWhere("s.nombreModeloElemento = :nombreModeloElemento")
                                                                ->andWhere("s.estado               = :estado")
                                                                ->setParameter('nombresTipoElemento',  $arrayTiposElemento)
                                                                ->setParameter('nombreModeloElemento', $strModeloElemento)
                                                                ->setParameter('estado',               "Activo")
                                                                ->orderBy('s.id', 'ASC')
                                                                ->setMaxResults(1)
                                                                ->getQuery()
                                                                ->getOneOrNullResult();
                if($strRequiereEnlace == 'N')
                {   
                    //Equipos que no requieren configurar enlaces
                    $arrayParIngresoElemento = array(
                        'nombreElementoCliente'  => $strNombreElemento,
                        'modeloElementoNuevo'    => $strModeloElemento,
                        'serieElementoNuevo'     => $strSerieElemento,
                        'descEquipo'             => 'dispositivo cliente',
                        'tipoElementoNuevo'      => $objModeloElemento->getTipoElementoId()->getNombreTipoElemento(),
                        'macElementoRegistrar'   => $strMacElemento,
                        'objServicio'            => $objServicio,
                        'idEmpresa'              => $intIdEmpresa,
                        'usrCreacion'            => $strUsrCreacion,
                        'ipCreacion'             => $strIpCreacion,
                        'booleanSinInterface'    => $objParElemento->getValor6() === "S" ? false : true,
                        'booleanInterfaceNotWan' => $objParElemento->getValor6() === "S",
                        'bandRegistroEquipo'     => "S",
                        'propiedadEquipo'        => "TELCONET"
                    );
                    $objElementoCliente = $this->servicioGeneral->ingresarElementoClienteTNSinEnlace($arrayParIngresoElemento);  

                    //Registrar la mac del elemento
                    if(!empty($strMacElemento) && is_object($objElementoCliente))
                    {
                        $objDetalleElementoMac = new InfoDetalleElemento();
                        $objDetalleElementoMac->setElementoId($objElementoCliente->getId());
                        $objDetalleElementoMac->setDetalleNombre("MAC");
                        $objDetalleElementoMac->setDetalleValor($strMacElemento);
                        $objDetalleElementoMac->setDetalleDescripcion("Mac del equipo del cliente");
                        $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoMac->setUsrCreacion($strUsrCreacion);
                        $objDetalleElementoMac->setIpCreacion($strIpCreacion);
                        $objDetalleElementoMac->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoMac);
                        $this->emInfraestructura->flush();
                    }
                }
                else
                {
                    //Equipos que requieren enlaces
                    $boolEquipoISB = false;
                    if (strtoupper($strTituloElemento) == 'SWITCH' || strtoupper($strTituloElemento) == 'NVR' )
                    {
                        //Para los equipos SWITCH y NVR se necesita los puetos de salida del router isb
                        $boolEquipoISB = true;
                        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                              ->findOneBy(array('servicioId' => $objServicioISB->getId()));
                        
                        if(!isset($objServicioTecnico))
                        {
                            throw new Exception('No se ha podido obtener el servicio tecnico');
                        }
                        $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                          ->find($objServicioTecnico->getUltimaMillaId());
                        
                        $objElementoISB = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                          ->findOneBy(array('elementoId' => $objServicioTecnico->getElementoClienteId(),
                                                            'estado' => 'not connect'));
                        
                        if(!isset($objElementoISB))
                        {
                            throw new \Exception('No se ha podido obtener el elemento del cliente');
                        }

                    }

                    $arrayElementoNodo = array(
                        'nombreElementoCliente'         => $strNombreElemento,
                        'nombreModeloElementoCliente'   => $strModeloElemento,
                        'serieElementoCliente'          => $strSerieElemento,
                        'boolEsUbicacionNodo'           => false,
                        'strMacDispositivo'             => $strMacElemento,
                        'objServicio'                   => $objServicio,
                        'intIdEmpresa'                  => $intIdEmpresa,
                        'usrCreacion'                   => $strUsrCreacion,
                        'ipCreacion'                    => $strIpCreacion,
                        'objInterfaceElementoVecinoOut' => $boolEquipoISB ? $objElementoISB : $objVecinoOutSW,
                        'banderaEquipoSafeEntry'        => 'S',
                        'objUltimaMilla'                => $objUltimaMilla);

                    $objInterfaceElementoIngresado = $this->servicioGeneral->ingresarElementoClienteTN($arrayElementoNodo);
                
                    $objElementoCliente = $objInterfaceElementoIngresado->getElementoId();
                    
                    //SE REGISTRA EL TRACKING DEL ELEMENTO
                    $arrayParametrosAuditoria["strNumeroSerie"]  = $strSerieElemento;
                    $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo';
                    $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
                    $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo';
                    $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
                    $arrayParametrosAuditoria["strCodEmpresa"]   = $intIdEmpresa;
                    $arrayParametrosAuditoria["strTransaccion"]  = 'Activacion Cliente';
                    $arrayParametrosAuditoria["intOficinaId"]    = 0;
                    if(is_object($objPunto))
                    {
                        $strCedulaCliente = is_object($objPunto->getPersonaEmpresaRolId())     ?
                                                is_object($objPunto->getPersonaEmpresaRolId()->getPersonaId()) ?
                                                $objPunto->getPersonaEmpresaRolId()->getPersonaId()->getIdentificacionCliente():"":"";
                                                
                        $arrayParametrosAuditoria["strLogin"] = $objPunto->getLogin();
                        $arrayParametrosAuditoria["strCedulaCliente"] = $strCedulaCliente;
                    }
                    $arrayParametrosAuditoria["strUsrCreacion"] = $strUsrCreacion;
                    $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
                    //Registrar la mac del elemento
                    if(!empty($strMacElemento) && is_object($objElementoCliente))
                    {
                        $objDetalleElementoMac = new InfoDetalleElemento();
                        $objDetalleElementoMac->setElementoId($objElementoCliente->getId());
                        $objDetalleElementoMac->setDetalleNombre("MAC");
                        $objDetalleElementoMac->setDetalleValor($strMacElemento);
                        $objDetalleElementoMac->setDetalleDescripcion("Mac del equipo del cliente");
                        $objDetalleElementoMac->setFeCreacion(new \DateTime('now'));
                        $objDetalleElementoMac->setUsrCreacion($strUsrCreacion);
                        $objDetalleElementoMac->setIpCreacion($strIpCreacion);
                        $objDetalleElementoMac->setEstado('Activo');
                        $this->emInfraestructura->persist($objDetalleElementoMac);
                        $this->emInfraestructura->flush();
                    }

                    if(strtoupper($strTituloElemento) == 'SWITCH' )
                    {
                        //Se busca y actualiza el servicio tecnico
                        $objVecinoOutSW = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                          ->findOneBy(array('elementoId' => $objInterfaceElementoIngresado->getElementoId(), 
                                                            'estado'     => 'not connect'),
                                                      array('id' => 'ASC'));

                        $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                ->findOneBy(array('servicioId' => $intIdServicio));

                        //Se anade la informacion del router isb y del elemento del cliente (SWITCH)
                        $objServicioTecnico->setElementoId($objElementoISB->getElementoId()->getId());
                        $objServicioTecnico->setInterfaceElementoId($objElementoISB->getId());
                        $objServicioTecnico->setElementoClienteId($objInterfaceElementoIngresado->getElementoId()->getId());
                        $objServicioTecnico->setInterfaceElementoClienteId($objInterfaceElementoIngresado->getId());
                        $this->emComercial->persist($objServicioTecnico);
                        $this->emComercial->flush();

                    }
                    else
                    {
                        //Interface del SWITCH para las camaras y las raspberry
                        $objVecinoOutSW = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                          ->findOneBy(array('elementoId' => $objVecinoOutSW->getElementoId(),
                                                            'estado' => 'not connect'),
                                                      array("id" => "ASC"));
                    }

                }
                //ingresar elemento
                //ingresar la caracteristica
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio,
                                                                               $objProducto,
                                                                               "ELEMENTO_CLIENTE_ID",
                                                                               $objElementoCliente->getId(),
                                                                               $strUsrCreacion);

                //ingresar elemento NAF
                $strIdentificacionCliente = "";
                $strCodigoArticuloOnt     = "";
                $strTipoArticulo          = "AF";
                $strMensajeError    = str_repeat(' ', 1000);
                $strSql             = "BEGIN AFK_PROCESOS.IN_P_PROCESA_INSTALACION(:codigoEmpresaNaf, "
                                    . ":codigoArticulo, :tipoArticulo, :identificacionCliente, :serieCpe, "
                                    . ":cantidad, :pv_mensajeerror); END;";
                $objStmt = $this->emNaf->getConnection()->prepare($strSql);
                $objStmt->bindParam('codigoEmpresaNaf', $intIdEmpresa);
                $objStmt->bindParam('codigoArticulo', $strCodigoArticuloOnt);
                $objStmt->bindParam('tipoArticulo',$strTipoArticulo);
                $objStmt->bindParam('identificacionCliente', $strIdentificacionCliente);
                $objStmt->bindParam('serieCpe', $strSerieElemento);
                $objStmt->bindParam('cantidad', intval(1));
                $objStmt->bindParam('pv_mensajeerror', $strMensajeError);
                $objStmt->execute();
                if(strlen(trim($strMensajeError))>0)
                {
                    throw new \Exception("ERROR NAF: ".$strMensajeError);
                }
                //ingreso historial
                $strObservacionServicioOnt = "Informaci&oacute;n del Elemento ".$strTituloElemento."<br/>";
                $strObservacionServicioOnt .= "Nuevo ".$strTituloElemento." <br/>";
                $strObservacionServicioOnt .= "Nombre: ".$strNombreElemento."<br/>";
                $strObservacionServicioOnt .= "Serie: ".$strSerieElemento."<br/>";
                $strObservacionServicioOnt .= "Modelo: ".$strModeloElemento."<br/>";
                if( $objParElemento->getValor6() == "S")
                {
                    $strObservacionServicioOnt .= "Mac: ".$strMacElemento."<br/>";
                }
                $this->servicioGeneral->ingresarServicioHistorial($objServicio, "Activo", $strObservacionServicioOnt,
                                                                  $strUsrCreacion, $strIpCreacion);
            }

            //LLAMADA AL SERVICE PARA REALIZAR LA CARGA Y DESCARGA DEL ACTIVO.
            if (!empty($arrayEquipos) && count($arrayEquipos) > 0)
            {
                $arrayCargaDescarga['strUsuario']              =  $strUsrCreacion;
                $arrayCargaDescarga['strIpUsuario']            =  $strIpCreacion;
                $arrayCargaDescarga['strTipoRecibe']           = 'Cliente';
                $arrayCargaDescarga['intIdServicio']           =  $objServicio->getId();
                $arrayCargaDescarga['intIdEmpleado']           =  $intIdTecnicoEncargado;
                $arrayCargaDescarga['intIdEmpresa']            =  $intIdEmpresa;
                $arrayCargaDescarga['strTipoActividad']        = 'Instalacion';
                $arrayCargaDescarga['strTipoTransaccion']      = 'Nuevo';
                $arrayCargaDescarga['strObservacion']          = 'Instalacion del servicio';
                $arrayCargaDescarga['arrayEquipos']            =  $arrayEquipos;
                $arrayCargaDescarga['strEstadoSolicitud']      = 'Asignada';
                $arrayCargaDescarga['strDescripcionSolicitud'] = 'SOLICITUD PLANIFICACION';
                $this->serviceInfoElemento->cargaDescargaActivos($arrayCargaDescarga);
            }

            //actualizo el estado del servicio
            $objServicio->setEstado("Activo");
            $objServicio->setObservacion("Se Confirmo el Servicio");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            //actualizo el estado del punto
            $objPunto->setEstado("Activo");
            $this->emComercial->persist($objPunto);
            $this->emComercial->flush();
            //obtengo la accion
            $objAccion  = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se Confirmo el Servicio");
            $objServicioHistorial->setEstado("Activo");
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $objServicioHistorial->setAccion($objAccion->getNombreAccion());
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            //seteo punto facturacion
            $objPuntoFacturacion = $objServicio->getPuntoFacturacionId();
            if(!is_object($objPuntoFacturacion))
            {
                $objServicio->setPuntoFacturacionId($objPunto);
                $objPuntoAdicional  = $this->emComercial->getRepository('schemaBundle:InfoPuntoDatoAdicional')
                                      ->findOneBy(array( "puntoId" => $objPunto->getId()));
                if(is_object($objPuntoAdicional))
                {
                    $objPuntoAdicional->setEsPadreFacturacion("S");
                    $this->emComercial->persist($objPuntoAdicional);
                    $this->emComercial->flush();
                }
            }

            //finalizar solicitud planificacion
            $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                            ->findOneBy(array('descripcionSolicitud' => 'SOLICITAR NUEVO SERVICIO',
                                                              'estado' => 'Activo'));
                                                                        
            $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->findOneBy(array('servicioId' => $objServicio->getId(),
                                                          'tipoSolicitudId' => $objTipoSolicitud->getId(),
                                                          'estado' => 'Pendiente'));
            if(is_object($objSolicitud))
            {
                $objSolicitud->setEstado("Finalizada");
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->flush();
                $this->emComercial->commit();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->flush();
                $this->emInfraestructura->commit();
            }
            $strStatus  = "OK";
            $strMensaje = "OK";

        }
            
        
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $this->utilService->insertError("Telcos+",
                                            "InfoActivarPuertoService->confirmarServicioSafeEntry",
                                            $strMensaje,
                                            $strUsrCreacion,
                                            $strIpCreacion
                                           );
        }

        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
    }

}

