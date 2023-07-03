<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\planificacionBundle\Service\RecursosDeRedService;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class MigracionTunelIpAL3mpls
{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emNaf;
    private $servicioGeneral;
    private $recursoRedService;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $networkingScripts;
    
    public function __construct(Container $container)
    {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
    }  
  
    public function setDependencies(InfoServicioTecnicoService  $servicioGeneral, 
                                    RecursosDeRedService        $recursosdeRedService,
                                    NetworkingScriptsService    $networkingScript)
    {
        $this->servicioGeneral      = $servicioGeneral;     
        $this->recursoRedService    = $recursosdeRedService;     
        $this->networkingScripts    = $networkingScript;
    }
    
    /**
     * 
     * Funcion encargada de realizar la ejecución del cambio de TunelIp a MPLS , ejecutar script con
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 26-06-2019 - Se agregan parámetros de razon_social y route_target export e import en el método configPE, con el objetivo de
     *                           enviar a configurar una lineas adicionales que permitan al cliente el monitoreo sus enlaces de datos
     * @since 1.2
     *
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 - se corrige array enviado a configurar a PE en los campos tipoenlace y login_aux, los cuales se enviaban nulos
     * @since 02-12-2016
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 - Se agrega bloque de código necesario para realizar la comunicación con el WebService de NW para ejecucipon
     *                en Switch y PE y configurar el servicio con el nuevo producto
     * @since 26-09-2016
     * 
     * @since 1.0
     * 
     * @param Array $arrayPeticiones
     * @return Array $arrayRespuesta [ status , mensaje ]
     */
    public function ejecutarMigracionTunelIpAL3mpls($arrayPeticiones)
    {
        $arrayRespuesta         = array();
        $strVlanAnterior        = null;
        $strBanderaLineasBravco = "N";

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
            
            if(!$objServicio)
            {
                return array('status' => "ERROR", 'mensaje' => "No existe descripcion de Servicio a configurar");
            }                        
            
            $objTipoSolicitud       = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                           ->findOneBy(array("descripcionSolicitud" => "SOLICITUD MIGRACION TUNEL IP A L3MPLS", 
                                                             "estado"               => "Activo"));
            $objDetalleSolicitud    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->findOneBy(array("servicioId"      => $objServicio->getId(), 
                                                             "tipoSolicitudId" => $objTipoSolicitud->getId()));
            
            if($objDetalleSolicitud)
            {            
                //Obteniendo elemento Padre ( Pe )
                $objElementoPe     = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($arrayPeticiones['idElementoPadre']);
                
                if(!$objElementoPe)
                {
                    return array('status' => "ERROR", 'mensaje' => "No se encuentra PE a configurar");
                }
                
                //Obtener la información del Switch
                $objElementoSwitch = $this->emInfraestructura->getRepository("schemaBundle:InfoElemento")->find($arrayPeticiones['idElemento']);
                
                if($objElementoSwitch)
                {
                    $objDetEleAnillo = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                            ->findOneBy(array('elementoId'    => $arrayPeticiones['idElemento'],
                                                              'detalleNombre' => 'ANILLO', 
                                                              'estado'        => 'Activo')
                                                       );
                    if(!$objDetEleAnillo)
                    {
                        return array('status' => "ERROR", 'mensaje' => "El Switch no contiene información de ANILLO");
                    }
                    
                    $objInterfaceElemento = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                                    ->find($arrayPeticiones['idInterfaceElemento']);
                    
                    if(!$objInterfaceElemento)
                    {
                        return array('status' => "ERROR", 'mensaje' => "Interface requerida para ser configurada no existe");
                    }
                    
                    //Obteniendo informacion de VLAN anterior para que sea eliminada del SW
                    $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                               ->findOneByDescripcionCaracteristica('VLAN');
                    
                    if($objAdmiCaracteristica)
                    {
                        $objDetalleSolicitudCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                       ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                                         'caracteristicaId'   => $objAdmiCaracteristica->getId(),
                                                                                         'estado'             => 'Asignada')
                                                                                  );
                        if($objDetalleSolicitudCaract)
                        {
                            $objDetalleVlanAnterior = $this->emInfraestructura->getRepository("schemaBundle:InfoDetalleElemento")
                                                                              ->find($objDetalleSolicitudCaract->getValor());
                            
                            if($objDetalleVlanAnterior)
                            {
                                $strVlanAnterior = $objDetalleVlanAnterior->getDetalleValor();
                            }
                        }
                    }
                    
                    if(!$strVlanAnterior)
                    {
                        return array('status' => "ERROR", 'mensaje' => "No se puede obtener la VLAN anterior del Servicio");
                    }
                    
                    //Se elimina la mac de la vlan anterior para configurar en la nueva vlan
                    $arrayVlanMacAnterior   = array($strVlanAnterior => array($arrayPeticiones['mac']));
                                                         
                    //------------------------------------------------------------------------------------------
                    //          ELIMINAR LA MAC DEL SWITCH ANTERIOR PARA CONFIGURAR EN EL SW DE ANILLO
                    //------------------------------------------------------------------------------------------
                    
                    $arrayRequestWS                = array();
                    $arrayRequestWS['url']         = 'configMAC';
                    $arrayRequestWS['accion']      = 'eliminar';                    
                    $arrayRequestWS['sw']          = $objElementoSwitch->getNombreElemento();
                    $arrayRequestWS['anillo']      = $objDetEleAnillo->getDetalleValor();
                    $arrayRequestWS['pto']         = $objInterfaceElemento->getNombreInterfaceElemento();
                    $arrayRequestWS['macVlan']     = $arrayVlanMacAnterior;
                    $arrayRequestWS['descripcion'] = 'cce_'.$objServicio->getLoginAux();                    
                    $arrayRequestWS['servicio']    = $objServicio->getProductoId()->getNombreTecnico();
                    $arrayRequestWS['login_aux']   = $objServicio->getLoginAux();
                    $arrayRequestWS['user_name']   = $arrayPeticiones['usrCreacion'];
                    $arrayRequestWS['user_ip']     = $arrayPeticiones['ipCreacion'];                                       
                    
                    // Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuestaWs = $this->networkingScripts->callNetworkingWebService($arrayRequestWS);

                    if($arrayRespuestaWs['status'] != 'OK')
                    {
                        return array('status' => "ERROR", 
                                    'mensaje' => "Error al Eliminar MAC de la Vlan Anterior: ".$arrayRespuestaWs['mensaje']);
                    }
                    
                    //------------------------------------------------------------------------------------------
                    //                               ACTIVAR LA MAC EN EL SWITCH
                    //------------------------------------------------------------------------------------------

                    //Se determina la nueva VLAN 
                    $arrayVlanMacActual   = array($arrayPeticiones['vlan'] => array($arrayPeticiones['mac']));
                    
                    $arrayRequestWS['accion']      = 'activar';                   
                    $arrayRequestWS['macVlan']     = $arrayVlanMacActual;
                   
                    // Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuestaWs = $this->networkingScripts->callNetworkingWebService($arrayRequestWS);
                    
                    if($arrayRespuestaWs['status'] != 'OK')
                    {
                        return array('status' => "ERROR", 
                                    'mensaje' => "Error al Activar MAC con la Vlan Nueva: ".$arrayRespuestaWs['mensaje']);
                    }
                    
                    $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                            ->findOneByServicioId($arrayPeticiones['idServicio']);
                    
                    if(!is_object($objServicioTecnico))
                    {
                        return array('status'  => "ERROR", 
                                     'mensaje' => "No existe informacion Tecnica del Servicio");
                    }
                                        
                    //------------------------------------------------------------------------------------------
                    //                               ACTIVAR EN EL PE
                    //------------------------------------------------------------------------------------------
                    if(is_object($objServicio))
                    {
                        //Consultar Razon Social
                        $objInfoPersona = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();

                        if(is_object($objInfoPersona))
                        {
                            $strRazonSocial = $objInfoPersona->getRazonSocial();
                        }

                        if(!empty($strRazonSocial))
                        {
                            $arrayRazonesSociales = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                      ->getOne('PROYECTO MONITOREO CLIENTES GRUPO BRAVCO',
                                                                               'INFRAESTRUCTURA',
                                                                               'ACTIVAR SERVICIO',
                                                                               'RAZON SOCIAL GRUPO BRAVCO',
                                                                               $strRazonSocial,
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '',
                                                                               '');
                        }

                        if(isset($arrayRazonesSociales["valor1"]) && !empty($arrayRazonesSociales["valor1"]))
                        {
                            $strBanderaLineasBravco = "S";
                            $strRouteTargetExport   = $arrayRazonesSociales["valor2"];
                            $strRouteTargetImport   = $arrayRazonesSociales["valor3"];
                            $strRazonSocial         = $arrayRazonesSociales["valor4"];
                        }
                    }

                    $arrayPeticionesWS = array();
                    //accion a ejecutar
                    $arrayPeticionesWS['url']                   = 'configPE';
                    $arrayPeticionesWS['accion']                = 'Activar';        
                    $arrayPeticionesWS['sw']                    = $objElementoSwitch->getNombreElemento();
                    $arrayPeticionesWS['clase_servicio']        = $objServicio->getProductoId()->getNombreTecnico();
                    $arrayPeticionesWS['vrf']                   = $arrayPeticiones['vrf'];
                    $arrayPeticionesWS['pe']                    = $objElementoPe->getNombreElemento();
                    $arrayPeticionesWS['anillo']                = $objDetEleAnillo->getDetalleValor();
                    $arrayPeticionesWS['vlan']                  = $arrayPeticiones['vlan'];
                    $arrayPeticionesWS['subred']                = $arrayPeticiones['subredServicio'];
                    $arrayPeticionesWS['mascara']               = $arrayPeticiones['mascaraSubredServicio'];
                    $arrayPeticionesWS['gateway']               = $arrayPeticiones['gwSubredServicio'];
                    $arrayPeticionesWS['rd_id']                 = $arrayPeticiones['rdId'];
                    $arrayPeticionesWS['descripcion_interface'] = $objServicio->getLoginAux();
                    $arrayPeticionesWS['ip_bgp']                = $arrayPeticiones['ipServicio'];
                    $arrayPeticionesWS['asprivado']             = $arrayPeticiones['asPrivado'];
                    $arrayPeticionesWS['nombre_sesion_bgp']     = $objServicio->getLoginAux();
                    $arrayPeticionesWS['default_gw']            = $arrayPeticiones['defaultGateway'];
                    $arrayPeticionesWS['protocolo']             = $arrayPeticiones['protocolo'];
                    $arrayPeticionesWS['servicio']              = $objServicio->getProductoId()->getNombreTecnico();
                    $arrayPeticionesWS['login_aux']             = $objServicio->getLoginAux();
                    $arrayPeticionesWS['tipo_enlace']           = $objServicioTecnico->getTipoEnlace();
                    $arrayPeticionesWS['banderaBravco']         = 'NO';
                    $arrayPeticionesWS['weight']                = null;

                    $arrayPeticionesWS['user_name']             = $arrayPeticiones['usrCreacion'];
                    $arrayPeticionesWS['user_ip']               = $arrayPeticiones['ipCeacion'];

                    //Se envian a configurar lineas de monitoreo de enlaces de datos
                    if($strBanderaLineasBravco === "S")
                    {
                        $arrayPeticionesWS['razon_social'] = $strRazonSocial;
                        $arrayPeticionesWS['rt_export']    = $strRouteTargetExport;
                        $arrayPeticionesWS['rt_import']    = $strRouteTargetImport;
                    }

                    //Ejecucion del metodo via WS para realizar la configuracion en el Pe
                    $arrayRespuestaWs = $this->networkingScripts->callNetworkingWebService($arrayPeticionesWS);
                    
                    if($arrayRespuestaWs['status'] != 'OK')
                    {
                        return array('status' => "ERROR", 
                                    'mensaje' => "Error al configurar Pe : ".$arrayRespuestaWs['mensaje']);
                    }
                    
                     //actualizar solicitud
                    $objDetalleSolicitud->setEstado("Finalizada");
                    $this->emComercial->persist($objDetalleSolicitud);
                    $this->emComercial->flush();

                    //agregar historial a la solicitud
                    $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                    $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolicitudHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
                    $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolicitudHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
                    $objDetalleSolicitudHistorial->setEstado("Finalizada");
                    $this->emComercial->persist($objDetalleSolicitudHistorial);
                    $this->emComercial->flush();

                    //agregar servicio historial
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setObservacion('Se finalizo la solicitud de migración de Tunel Ip a L3MPLS');
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    
                    $objAdmiCaracteristicaCap1 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneByDescripcionCaracteristica('CAPACIDAD1');
                    
                    if($objAdmiCaracteristicaCap1)
                    {
                        $objDetalleSolicitudCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                       ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                                         'caracteristicaId'   => $objAdmiCaracteristicaCap1->getId(),
                                                                                         'estado'             => 'Asignada')
                                                                                  );
                        
                        if($objDetalleSolicitudCaract)
                        {
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                           $objServicio->getProductoId(), 
                                                                                           "CAPACIDAD1", 
                                                                                           $objDetalleSolicitudCaract->getValor(), 
                                                                                           $arrayPeticiones['usrCreacion']);
                        }                                                
                    }
                    
                    $objAdmiCaracteristicaCap2 = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneByDescripcionCaracteristica('CAPACIDAD2');
                    
                    if($objAdmiCaracteristicaCap2)
                    {
                        $objDetalleSolicitudCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                       ->findOneBy(array('detalleSolicitudId' => $objDetalleSolicitud->getId(),
                                                                                         'caracteristicaId'   => $objAdmiCaracteristicaCap2->getId(),
                                                                                         'estado'             => 'Asignada')
                                                                                  );
                        
                        if($objDetalleSolicitudCaract)
                        {
                            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, 
                                                                                           $objServicio->getProductoId(), 
                                                                                           "CAPACIDAD2", 
                                                                                           $objDetalleSolicitudCaract->getValor(), 
                                                                                           $arrayPeticiones['usrCreacion']);
                        }                                                
                    }
                                        
                    //actualizar las solicitudes caract
                    $arrayDetalleSolicitudCarac = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                       ->findBy(array("detalleSolicitudId"  => $objDetalleSolicitud->getId(),
                                                                      "estado"              => "Asignada"));
                    foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                    {
                        $objDetalleSolCarac->setEstado("Finalizada");
                        $this->emComercial->persist($objDetalleSolCarac);
                        $this->emComercial->flush();
                    }                                             
                
                    $arrayRespuesta = array('status' => "OK", 'mensaje' => "OK");
                }
                else
                {
                    return array('status' => "ERROR", 'mensaje' => "No se encuentra el Switch relacionado al Servicio");
                }                                               
            }
            else
            {
                return array('status' => "ERROR", 'mensaje' => "No se encuentra la Solicitud relacionada al Servicio");
            }
            
            $this->emComercial->commit();
        }
        catch (\Exception $e)
        {          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }
                                   
            $arrayRespuesta = array('status' => "ERROR", 'mensaje' => "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage());
                       
        }      
        
        return $arrayRespuesta;
    }
    
    public function migrarTunelIpAL3mpls($arrayPeticiones)
    {
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        try
        {
            //crear la solicitud con sus caracteristicas
            $objDetalleSolicitud = $this->crearSolicitudYCaracteristicas($arrayPeticiones);

            //eliminar las caracteristicas del servicio
            $this->eliminarDatosTunelIpLogico($arrayPeticiones);

            //actualizar id del producto al servicio
            $boolFlag = $this->updateProductoIdByServicio($arrayPeticiones);
            
            if($boolFlag)
            {
                $objServicioTecnico      = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                ->findOneBy(array("servicioId" => $arrayPeticiones['idServicio']));
                
                $arrayPeticiones['hilo']                = $objServicioTecnico->getInterfaceElementoConectorId();
                $arrayPeticiones['idDetalleSolicitud']  = $objDetalleSolicitud->getId();
                $arrayPeticiones['flagTransaccion']     = false;
                $arrayPeticiones['flagServicio']        = false;
                
                //grabar nuevas caracteristicas
                $arrayRespuestaRecursosRed = $this->recursoRedService->asignarRecursosRedL3mpls($arrayPeticiones);
                
                if($arrayRespuestaRecursosRed['status'] != "OK")
                {
                    throw new \Exception($arrayRespuestaRecursosRed['mensaje']);
                }
            }
            else
            {
                throw new \Exception("No se actualizo el Producto del Servicio, Fallo Migración");
            }
            
            $this->emComercial->getConnection()->commit();
            
            $status  = "OK";
            $mensaje = "OK";
        }
        catch (\Exception $e)
        {          
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
                        
            $status             = "ERROR";
            $mensaje            = "ERROR EN LA LOGICA DE NEGOCIO: <br> ".$e->getMessage();
            
            error_log($mensaje);
        }
        //*----------------------------------------------------------------------*/
        
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->close();
        }
        
        //*----------------------------------------------------------------------*/
        
        return array('status' => $status, 'mensaje' => $mensaje);
    }
    
    public function updateProductoIdByServicio($arrayPeticiones)
    {
        $objProducto    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                            ->findOneBy(array("nombreTecnico" => "L3MPLS", "esConcentrador" => "NO"));

        if($objProducto)
        {
            $objServicio      = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
            $objServicio->setProductoId($objProducto);
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    public function crearSolicitudYCaracteristicas($arrayPeticiones)
    {
        $objServicio      = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                 ->findOneBy(array("descripcionSolicitud" => "SOLICITUD MIGRACION TUNEL IP A L3MPLS", "estado" => "Activo"));
        
        //crear solicitud
        $objDetalleSolicitud = new InfoDetalleSolicitud();
        $objDetalleSolicitud->setServicioId($objServicio);
        $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
        $objDetalleSolicitud->setUsrCreacion($arrayPeticiones['usrCreacion']);
        $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
        $objDetalleSolicitud->setEstado("Pendiente");
        $this->emComercial->persist($objDetalleSolicitud);
        $this->emComercial->flush();
        
        //agregar historial a la solicitud
        $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
        $objDetalleSolicitudHistorial->setDetalleSolicitudId($objDetalleSolicitud);
        $objDetalleSolicitudHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
        $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
        $objDetalleSolicitudHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
        $objDetalleSolicitudHistorial->setEstado("Pendiente");
        $this->emComercial->persist($objDetalleSolicitudHistorial);
        $this->emComercial->flush();
        
        //agregar servicio historial
        $objServicioHistorial = new InfoServicioHistorial();
        $objServicioHistorial->setServicioId($objServicio);
        $objServicioHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
        $objServicioHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
        $objServicioHistorial->setEstado('Activo');
        $objServicioHistorial->setObservacion('Se creo una solicitud de migración de Tunel Ip a L3MPLS');
        $this->emComercial->persist($objServicioHistorial);
        $this->emComercial->flush();
        
        //obtener los servicios prod caract
        $arrServicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                      ->findBy(array("servicioId" => $objServicio->getId(), "estado" => "Activo"));
        
        //grabar las caracteristicas en la solicitud
        foreach($arrServicioProdCaract as $objServicioProdCaract)
        {
            $objCaracteristica = $this->servicioGeneral->getCaracteristicaByInfoServicioProdCaract($objServicioProdCaract);
            
            $arrayParametros = array(
                                        'objDetalleSolicitudId' => $objDetalleSolicitud,
                                        'objCaracteristica'     => $objCaracteristica,
                                        'estado'                => "Asignada",
                                        'valor'                 => $objServicioProdCaract->getValor(),
                                        'usrCreacion'           => $arrayPeticiones['usrCreacion']
                                    );
            
            $this->servicioGeneral->insertarInfoDetalleSolCaract($arrayParametros);
        }
        
        return $objDetalleSolicitud;
    }
    
    public function eliminarDatosTunelIpLogico($arrayPeticiones)
    {
        $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        
        //obtener los servicios prod caract
        $arrServicioProdCaract  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                       ->findBy(array("servicioId" => $objServicio->getId(), "estado" => "Activo"));
        
        //grabar las caracteristicas en la solicitud
        foreach($arrServicioProdCaract as $objServicioProdCaract)
        {
            $this->servicioGeneral->setEstadoServicioProductoCaracteristica($objServicioProdCaract,"Eliminado");
        }
    }
}