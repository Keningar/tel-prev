<?php
namespace telconet\comercialBundle\Service;

use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoServicioCaracteristica;

class ConvertirOrdenTrabajoService 
{ 
    private $emcom;
    private $emInfraestructura;
    private $emGeneral;
    private $emSoporte;
    private $emFinanciero;
    private $serviceUtil;
    private $servicePlanificar;
    private $serviceCambiarPlanService;
    private $serviceGeneral;
    private $serviceInfoDocumentoFinancieroCab;
    private $serviceInfoServicioService;
    public $serviceSolicitud;
    public $serviceAutorizaciones;
    public $serviceInfoContrato;
    
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
    {
        $this->emcom                             = $objContainer->get('doctrine.orm.telconet_entity_manager');
        $this->emInfraestructura                 = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral                         = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
        $this->emSoporte                         = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emFinanciero                      = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->serviceUtil                       = $objContainer->get('schema.Util');
        $this->servicePlanificar                 = $objContainer->get('planificacion.planificar');
        $this->serviceCambiarPlanService         = $objContainer->get('tecnico.InfoCambiarPlan');
        $this->serviceGeneral                    = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceInfoDocumentoFinancieroCab = $objContainer->get('financiero.InfoDocumentoFinancieroCab');
        $this->serviceInfoServicioService        = $objContainer->get('comercial.InfoServicio');
        $this->serviceSolicitud                  = $objContainer->get('comercial.Solicitudes'); 
        $this->serviceAutorizaciones             = $objContainer->get('comercial.Autorizaciones'); 
        $this->serviceEnvioPlantilla             = $objContainer->get('soporte.EnvioPlantilla');  
        $this->serviceInfoContrato               = $objContainer->get('comercial.InfoContrato');
    }
    
    /**
    * convertirOrdenTrabajo, Proceso que convierte las oordenes de trabajo.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 05-03-2020
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.1 03-03-2021 - Se realiza un ajuste de un mal uso de una variable, debido a que da un error y no esta permitiendo la generacion de
    *                           la solicitud de planificacion adicional para el producto cableado_ethernet.
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.2 26-04-2021 - Se verifica si el servicio posee servicios adicionales y fueron seleccionados para el producto Datos SafeCity.
    *
    * @author Richard Cabrera <rcabrera@telconet.ec>
    * @version 1.3 05-05-2021 - Se realizan ajustes para generar solicitud de instalacion para servicios configurados sin Solicitud de Factibilidad,
    *                           por ejemplo: productos CAMARA-SAFECITY
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.4 01-10-2021 - Se verifica si el servicio es SW POE GPON se convierte las órdenes de trabajo de los servicios adicionales GPON_MPLS
    *  
    * @author Liseth Candelario <lcandelario@telconet.ec>
    * @version 1.4 14-12-2021 - Se registra datos en la tabla InfoSolicitudMaterial por materiales excedentes
    *
    * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
    * @version 1.4 08-12-2022 - Se solicita cambiar el estado de Pendiente a Activo para los servicios que tengan un adendum de contrato.
    *
    * @author Jefferson Leon <jlleona@telconet.ec>
    * @version 1.4 24-02-2023 - Se inserta caracteristica de solicitud de planificacion solo si existe metraje
    *
    * @author Alex Gómez <algomez@telconet.ec>
    * @version 1.8 25-10-2022 - Se invoca ms para la preplanificación de servicios CIH adicionales con estado Pendiente
    */
    public function convertirOrdenTrabajo($arrayParametros)
    {
        $arrayValor                         = $arrayParametros['array_valor'];
        $strMensajeObservacion              = $arrayParametros['strMensajeObservacion'];
        $strOTClienteConDeuda               = $arrayParametros['strOTClienteConDeuda'];
        $intIdPunto                         = $arrayParametros['intIdPunto'];
        $strCodEmpresa                      = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa                  = $arrayParametros['strPrefijoEmpresa'];
        $strOficina                         = $arrayParametros['strOficina'];
        $strUser                            = $arrayParametros['strUser'];
        $strIp                              = $arrayParametros['strIp'];
        $strEstado                          = "";
        $strObservacion                     = "";
        $objSolicitudFactibilidadAnticipada = null;
        $boolEsCloud                        = false;
        $strBanderaGenerarSolInstalacion    = "N";
        $this->emcom->beginTransaction();
        $floatTotalExcedente                 = 0;
        $floatTotalPagar                    = 0;
        $serviceSolicitudes                 = $this->serviceSolicitud;
        $serviceAutorizacion                = $this->serviceAutorizaciones;
        $arrayParamsPreplanificaCIH         = array();

        try
        {            
            $arrayServiciosAdicionales  = array();
            foreach ($arrayValor as $intIdServicio)
            {
                $objInfoServicio        = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if(is_object($objInfoServicio) && is_object($objInfoServicio->getProductoId()))
                {
                    /***OBTENER LOS SERVICIOS ADICIONALES***/
                    //seteo variable
                    $objServicioPrincipal    = $objInfoServicio;
                    $arrayParametrosDetSwPoe = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                      ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                               'INFRAESTRUCTURA',
                                                               'PARAMETROS',
                                                               'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                               $objInfoServicio->getProductoId()->getId(),
                                                               '',
                                                               '',
                                                               '',
                                                               '',
                                                               $strCodEmpresa);
                    if(!empty($arrayParametrosDetSwPoe) && isset($arrayParametrosDetSwPoe["valor5"])
                       && $arrayParametrosDetSwPoe["valor5"] == "SWITCHPOE")
                    {
                        $objCaractServicioPrincipal = $this->serviceGeneral->getServicioProductoCaracteristica($objInfoServicio,
                                                                'RELACION_SERVICIOS_GPON_SAFECITY',$objInfoServicio->getProductoId());
                        if(is_object($objCaractServicioPrincipal))
                        {
                            $objServicioPrincipal = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                                                    ->find($objCaractServicioPrincipal->getValor());
                            if(!is_object($objServicioPrincipal))
                            {
                                $objServicioPrincipal = $objInfoServicio;
                            }
                        }
                    }
                    $arrayParServAdd = array(
                        "intIdProducto"      => $objServicioPrincipal->getProductoId()->getId(),
                        "intIdServicio"      => $objServicioPrincipal->getId(),
                        "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                        "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
                    );
                    $arrayProdCaracConfProAdd  = $this->emcom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                            ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
                    if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
                    {
                        foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                        {
                            $objServicioConfProAdd = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                        ->findOneBy(array("id"     => $arrayServicioConfProAdd['idServicio'],
                                                                          "estado" => "Factible"));
                            if(is_object($objServicioConfProAdd) && $objServicioConfProAdd->getId() != $objInfoServicio->getId())
                            {
                                $arrayServiciosAdicionales[] = $objServicioConfProAdd->getId();
                            }
                        }
                    }
                }
            }

            //se une los arreglos de los id de los servicios
            $arrayValorServicios     = array_unique(array_merge($arrayValor,$arrayServiciosAdicionales));
            $entityNumeracion        = $this->emcom->getRepository('schemaBundle:AdmiNumeracion')
                                                   ->findByEmpresaYOficina($strCodEmpresa, (int)$strOficina, "ORD");
            $intSecuenciaAsig        = str_pad($entityNumeracion->getSecuencia(), 7, "0", STR_PAD_LEFT);
            $intNumeroDeContrato     = $entityNumeracion->getNumeracionUno() . "-" . $entityNumeracion->getNumeracionDos() . "-" . $intSecuenciaAsig;
            $entityInfoPunto         = $this->emcom->getRepository('schemaBundle:InfoPunto')
                                              ->find($intIdPunto);

            $entityInfoOT            = new InfoOrdenTrabajo();
            $entityInfoOT->setPuntoId($entityInfoPunto);
            $entityInfoOT->setTipoOrden('N');
            $entityInfoOT->setNumeroOrdenTrabajo($intNumeroDeContrato);
            $entityInfoOT->setFeCreacion(new \DateTime('now'));
            $entityInfoOT->setUsrCreacion($strUser);
            $entityInfoOT->setIpCreacion($strIp);
            $entityInfoOT->setOficinaId((int)$strOficina);
            $entityInfoOT->setEstado("Activa");
            $this->emcom->persist($entityInfoOT);
            $this->emcom->flush();   

            if($entityInfoOT)
            {
                //Actualizo la numeracion en la tabla
                $intNumeroAct = ($entityNumeracion->getSecuencia() + 1);
                $entityNumeracion->setSecuencia($intNumeroAct);
                $this->emcom->persist($entityNumeracion);
                $this->emcom->flush();
            }
                        
            foreach ($arrayValorServicios as $intIdServicio):
                $strEstado                     = "PrePlanificada";
                $strObservacion                = "Se solicito planificacion";
                $arrayServiciosWifiPreServicio = array();
                $entityInfoServicio            = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                             ->find($intIdServicio);
                if ($entityInfoServicio)
                {
                    if($strPrefijoEmpresa === 'TN')
                    {
                        $strNombreTecnico               = $entityInfoServicio->getProductoId()->getNombreTecnico();
                        $arrayServiciosWifiRelacionados = $this->servicePlanificar->getIdWifiInstSim($entityInfoServicio->getId());

                        if ($arrayServiciosWifiRelacionados)
                        {
                            foreach ($arrayServiciosWifiRelacionados as $intServicioWifi)
                            {
                                $objServicioWifi = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                               ->find($intServicioWifi);
                                if ($objServicioWifi && $objServicioWifi->getEstado() == 'Pre-servicio')
                                {
                                    array_push($arrayServiciosWifiPreServicio, 
                                               array('id'     => $objServicioWifi->getId(),
                                                     'estado' => $objServicioWifi->getEstado()));
                                }
                            }

                            if (count($arrayServiciosWifiPreServicio) >= 1)
                            {
                                throw new \Exception("No puedes aprobar esta orden, porque tiene «".
                                                     count($arrayServiciosWifiPreServicio) .
                                                     "» servicios Internet Wifi relacionados sin solicitar factibilidad.");
                            }

                        }

                        //Si es HOsting Pool de recursos quedara en estado asignado el servicio ( MVs )
                        if($strNombreTecnico == 'HOSTING')
                        {
                            $boolEsPool = $this->serviceGeneral->isContieneCaracteristica($entityInfoServicio->getProductoId(),
                                                                                          'ES_POOL_RECURSOS');
                            
                            if($boolEsPool)
                            {
                                //pool de recursos
                                $strEstado      = 'PrePlanificada';
                                $strObservacion = "Se solicitó planificación";
                            }
                        }
                        
                        if($strNombreTecnico == 'CONCINTER')
                        {
                            $strEstado      = 'AsignadoTarea';
                            $strObservacion = 'Se Solicita Asignación de Recursos de Red';
                        }
                        
                        $objServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneByServicioId($entityInfoServicio->getId());
                        
                        if (is_object($objServicioTecnico) && ($objServicioTecnico->getUltimaMillaId() > 0))
                        {
                            $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                      ->find($objServicioTecnico->getUltimaMillaId());
                            if(is_object($objUltimaMilla))
                            {
                                $strUltimaMilla = $objUltimaMilla->getNombreTipoMedio();
                                if ($strUltimaMilla == 'Radio' && $entityInfoServicio->getEstado() == "Factibilidad-anticipada")
                                {
                                    $strEstado      = "Asignar-factibilidad";
                                    $strObservacion = "Se solicita asignar factibilidad de servicio Radio";
                                }
                            }
                        }
                    }
                    
                    $entityInfoServicio->setOrdenTrabajoId($entityInfoOT);
                    $entityInfoServicio->setEstado($strEstado);
                    $this->emcom->persist($entityInfoServicio);
                    $this->emcom->flush();

                    if ($entityInfoServicio->getTipoOrden())
                    {
                        $entityInfoOT->setTipoOrden($entityInfoServicio->getTipoOrden());
                        $this->emcom->persist($entityInfoOT);
                        $this->emcom->flush();
                    }
                    
                    $entityServicioHist = new InfoServicioHistorial();
                    $entityServicioHist->setServicioId($entityInfoServicio);
                    $entityServicioHist->setObservacion($strObservacion);
                    $entityServicioHist->setIpCreacion($strIp);
                    $entityServicioHist->setFeCreacion(new \DateTime('now'));
                    $entityServicioHist->setUsrCreacion($strUser);
                    $entityServicioHist->setEstado($entityInfoServicio->getEstado());
                    $this->emcom->persist($entityServicioHist);
                    $this->emcom->flush();
                    if ($strPrefijoEmpresa === 'MD')
                    {
                        $arrayAdendumContrato = $this->emcom->getRepository('schemaBundle:InfoAdendum')
                                                            ->findBy(array('servicioId'     => $intIdServicio,
                                                                           'estado'         => 'Pendiente',
                                                                           'formaContrato'  => 'FISICO'));

                        if (count($arrayAdendumContrato) > 0)
                        {
                            foreach ($arrayAdendumContrato as $objAdendumContrato)
                            {
                                $objAdendumContrato->setEstado('Activo');
                                $objAdendumContrato->setFeModifica(new \DateTime('now'));
                                $this->emcom->persist($objAdendumContrato);
                                $this->emcom->flush();
                            }
                        }
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($entityInfoServicio);
                        $entityServicioHist->setAccion('Planificacion Comercial');
                        $entityServicioHist->setObservacion("Se envía la solicitud a Planificación comercial");
                        $entityServicioHist->setIpCreacion($strIp);
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($strUser);
                        $entityServicioHist->setEstado($entityInfoServicio->getEstado());
                        $this->emcom->persist($entityServicioHist);
                        $this->emcom->flush();    
                    }

                    if ('S' === $strOTClienteConDeuda && !empty($strMensajeObservacion))
                    {
                        $entityServicioHistDeuda = new InfoServicioHistorial();
                        $entityServicioHistDeuda->setServicioId($entityInfoServicio);
                        $entityServicioHistDeuda->setObservacion($strMensajeObservacion);
                        $entityServicioHistDeuda->setIpCreacion($strIp);
                        $entityServicioHistDeuda->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistDeuda->setUsrCreacion($strUser);
                        $entityServicioHistDeuda->setEstado($entityInfoServicio->getEstado());
                        $this->emcom->persist($entityServicioHistDeuda);
                        $this->emcom->flush();
                    }
                }
            endforeach;

            if ($entityInfoOT)
            {
                $entityServicios     = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                                   ->findByOrdenTrabajoId($entityInfoOT->getId());
                $entityTipoSolicitud = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                   ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");
                
                if ($entityServicios && count($entityServicios) > 0)
                {
                    foreach ($entityServicios as $entityServicio)
                    {
                        $strEstado                          = "PrePlanificada";                        
                        $objSolicitudFactibilidadAnticipada = null;
                        
                        if($strPrefijoEmpresa == 'TN')
                        {
                            $strNombreTecnico = $entityServicio->getProductoId()->getNombreTecnico();
                            
                            if($strNombreTecnico == 'HOSTING')
                            {
                                $boolEsPool = $this->serviceGeneral->isContieneCaracteristica($entityServicio->getProductoId(),
                                                                                              'ES_POOL_RECURSOS');

                                if($boolEsPool)
                                {
                                    //pool de recursos
                                    $strEstado = 'PrePlanificada';
                                }
                            }
                            
                            if($strNombreTecnico == 'CONCINTER')
                            {
                                $strEstado = 'AsignadoTarea';                                
                            }
                        
                            $objServicioTecnico = $this->emcom->getRepository('schemaBundle:InfoServicioTecnico')
                                                              ->findOneByServicioId($entityServicio->getId());
                            if (is_object($objServicioTecnico) && ($objServicioTecnico->getUltimaMillaId() > 0))
                            {
                                $objUltimaMilla = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                          ->find($objServicioTecnico->getUltimaMillaId());
                                if(is_object($objUltimaMilla))
                                {
                                    $strUltimaMilla = $objUltimaMilla->getNombreTipoMedio();
                                    if ($strUltimaMilla == 'Radio')
                                    {
                                        $objSolicitudFactibilidadAnticipada = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                                   ->findOneBy(array("servicioId" => $entityServicio->getId(), 
                                                                                                     "estado"     => "Factibilidad-anticipada"));
                                        if (is_object($objSolicitudFactibilidadAnticipada))
                                        {
                                            $strEstado = "Asignar-factibilidad";
                                            $objSolicitudFactibilidadAnticipada->setEstado($strEstado);
                                            $this->emcom->persist($objSolicitudFactibilidadAnticipada);
                                            $this->emcom->flush();

                                            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                            $objDetalleSolHist = new InfoDetalleSolHist();
                                            $objDetalleSolHist->setDetalleSolicitudId($objSolicitudFactibilidadAnticipada);
                                            $objDetalleSolHist->setIpCreacion($strIp);
                                            $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                            $objDetalleSolHist->setUsrCreacion($strUser);
                                            $objDetalleSolHist->setEstado($strEstado);
                                            $this->emcom->persist($objDetalleSolHist);
                                            $this->emcom->flush();
                                        }
                                    }
                                }
                            }
                        }

                        //Para servicios SAFECITY, servicios adicionales DATOS GPON, se envia crear solicitud de PLANIFICACION
                        $strBanderaGenerarSolInstalacion = "N";
                        if( is_object($entityServicio) && is_object($entityServicio->getProductoId()))
                        {
                            $arrayParametrosDet              = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                                                           ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                                    'INFRAESTRUCTURA',
                                                                                    'PARAMETROS',
                                                                                    'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                                                     $entityServicio->getProductoId()->getId(),
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     '',
                                                                                     $strCodEmpresa);

                            if(!empty($arrayParametrosDet["valor1"]) && isset($arrayParametrosDet["valor1"]))
                            {
                                $strBanderaGenerarSolInstalacion = "S";
                            }
                        }

                        //validacion que si tuvo orden de factibilidad se crea la orden de planificacion
                        $objSolicitudFactibilidad = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneBy(array("servicioId" => $entityServicio->getId(), 
                                                                                  "estado"     => "Factible"));
                        if ($objSolicitudFactibilidad || $entityServicio->getEsVenta() == "E" || is_object($objSolicitudFactibilidadAnticipada)
                            || $strBanderaGenerarSolInstalacion == "S")
                        {
                            $entityDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                  ->findCountDetalleSolicitudByIds($entityServicio->getId(), 
                                                                                                   $entityTipoSolicitud->getId());
                            if (!$entityDetalleSolicitud || $entityDetalleSolicitud["cont"] <= 0)
                            {                                                                
                                $entitySolicitud = new InfoDetalleSolicitud();
                                $entitySolicitud->setServicioId($entityServicio);
                                $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);
                                $entitySolicitud->setEstado($strEstado);
                                $entitySolicitud->setUsrCreacion($strUser);
                                $entitySolicitud->setFeCreacion(new \DateTime('now'));
                                $this->emcom->persist($entitySolicitud);
                                $this->emcom->flush();

                                //se finaliza la solicitud cuando es un wifi
                                if ($entityServicio->getProductoId())
                                {
                                    //Si el Producto es de CLOUD finaliza la solicitud de PLANIFICACION
                                    if ($boolEsCloud)
                                    {
                                        $objSolicitudFactibilidad->setEstado('Finalizada');
                                        $this->emcom->persist($objSolicitudFactibilidad);
                                        $this->emcom->flush();
                                    }

                                    /*Si el producto es Wifi Alquiler de Equipos se finaliza la solicitud de Factibilidad.*/
                                    if ($entityServicio->getProductoId()->getDescripcionProducto() == "WIFI Alquiler Equipos")
                                    {
                                        /*Finalizo la solicitud de factibilidad.*/
                                        $objSolicitudFactibilidad->setEstado('Finalizada');
                                        $this->emcom->persist($objSolicitudFactibilidad);
                                        $this->emcom->flush();
                                    }
                                }
                                if($strPrefijoEmpresa === 'TN')
                                {
                                    $objValorMetraje = $this->serviceGeneral
                                                                    ->getServicioProductoCaracteristica($entityServicio,
                                                                                                        'METRAJE FACTIBILIDAD',
                                                                                                        $entityServicio->getProductoId());   
                                    $objAdmiCaracteristica = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                    ->findOneBy(array('estado'                    => 'Activo',
                                                        'descripcionCaracteristica' => 'METRAJE FACTIBILIDAD'));
                                    
                                    if(is_object($objValorMetraje))
                                    {

                                        $objInfoDetalleSolCaractMetraje         = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                        ->findOneBy(array('caracteristicaId' => $objAdmiCaracteristica,
                                                        'detalleSolicitudId' => $entitySolicitud  ));
                                                        
                                        if(!is_object($objInfoDetalleSolCaractMetraje))
                                        {
                                            $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
                                            $objInfoDetalleSolCaract->setCaracteristicaId($objAdmiCaracteristica);
                                            $objInfoDetalleSolCaract->setValor($objValorMetraje->getValor());
                                            $objInfoDetalleSolCaract->setDetalleSolicitudId($entitySolicitud);
                                            $objInfoDetalleSolCaract->setEstado('Activo');
                                            $objInfoDetalleSolCaract->setUsrCreacion($strUser);
                                            $objInfoDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                                            $this->emcom->persist($objInfoDetalleSolCaract);
                                            $this->emcom->flush();
                                        }               
                                    }
                                }

                                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                $entityDetalleSolHist = new InfoDetalleSolHist();
                                $entityDetalleSolHist->setDetalleSolicitudId($entitySolicitud);
                                $entityDetalleSolHist->setIpCreacion($strIp);
                                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                $entityDetalleSolHist->setUsrCreacion($strUser);
                                $entityDetalleSolHist->setEstado($strEstado);
                                $this->emcom->persist($entityDetalleSolHist);
                                $this->emcom->flush();

                                /*  Inicio del proceso que  registra datos en la infoSolicitudMaterial para valores de excedente de materiales
                                Si existe una solicitud de materiales excedentes */
                                $entityTipoSolicitudExce = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                         ->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");

                                /* Validamos si existe solicitud de excedente de materiales, para luego preguntar sus estados */
                                $objDetalleSolicitudExc = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                       ->findOneBy(array( "servicioId"      => $entityInfoServicio->getId(),
                                                                       "tipoSolicitudId" => $entityTipoSolicitudExce->getId()));
                                
                                // Inicio: consulta si hay archivos como evidencia de excedente
                                $strNombreDocumento = 'Adjunto Archivo de Evidencia';
                                $strEvidencia       = null;
                        
                                $strDocumentoRelacionC  = $this->emcom->getRepository('schemaBundle:InfoDocumentoRelacion')
                                                                        ->findBy(array("servicioId"    => $entityInfoServicio->getId(),
                                                                                    "estado"        => "Activo"));        
                                if(count($strDocumentoRelacionC) > 0)
                                {
                                    foreach($strDocumentoRelacionC as $documento)
                                    {
                                        if ($strNombreDocumento)
                                        {
                                            $strArchivoC = $this->emcom->getRepository('schemaBundle:InfoDocumento')
                                                                        ->findOneBy(array(
                                                                            'id' => $documento->getDocumentoId(),
                                                                            'nombreDocumento' => $strNombreDocumento  ));
                                        }
                                        else
                                        {
                                            $strArchivoC = $this->emcom->getRepository('schemaBundle:InfoDocumento')
                                                                        ->find($documento->getDocumentoId());
                                        }
                        
                                        if (is_object($strArchivoC))
                                        {
                                            $arrayEncontrados[] = array('ubicacionLogica' => $strArchivoC->getUbicacionLogicaDocumento(),
                                                                            'feCreacion' => ($strArchivoC->getFeCreacion() ? 
                                                                                            date_format($strArchivoC->getFeCreacion(),
                                                                                            "d-m-Y H:i") : ""),
                                                                            'linkVerDocumento' => $strArchivoC->getUbicacionFisicaDocumento(),
                                                                            'idDocumento' => $strArchivoC->getId());
                                        }
                        
                                    }
                                    $objData        = json_encode($arrayEncontrados);
                                    $objResultado   = '{"total":"' . count($arrayEncontrados) . '","encontrados":' . $objData . '}';
                                    $strEvidencia = 'Cliente tiene documento(s) de evidencia';                    
                                }               
                                // Fin: consulta si hay archivos como evidencia de excedente

                                if(is_object($objDetalleSolicitudExc))
                                {
                                    $strMetrajeC                       = 0;
                                    $floatValorCaractOCivil            = 0;
                                    $floatValorCaractFibraMetros       = 0;
                                    $floatValorCaractOtrosMateriales   = 0;
                                    $floatTotalPagar                   = 0;
                                    $intPrecioFibra                    = 0;
                                    $intMetrosDeDistancia              = 0;
                               
                                    $objCaracteristicaFibraMetros     = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD');
                                    $objCaracteristicaOCivil          = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
                                    $objCaracteristicaOtrosMateriales = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
                                    $objCaracteristicaCancPorCli      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE');
                                    $objCaracteristicaAsumeCli        = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS ASUME EL CLIENTE PRECIO');
                                    $objCaracteristicaAsumeEmpresa    = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('COPAGOS ASUME LA EMPRESA PRECIO');

                                if(($objDetalleSolicitudExc->getEstado()=='Pendiente')||$objDetalleSolicitudExc->getEstado()=='Aprobado')
                                {
                                    $emTipoSolicitudFact = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                     ->findOneBy(array("descripcionSolicitud" => "SOLICITUD FACTIBILIDAD",
                                                                       "estado"               => "Activo"));
                                    if($emTipoSolicitudFact)
                                    {
                                    // Busca el tipo de solicitud factibilidad con el id_servicio 
                                    $emDetalleSolicitudFac = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                ->findOneBy(array("servicioId"      => $intIdServicio,
                                                                                "tipoSolicitudId" => $emTipoSolicitudFact->getId()));
                                    if($emDetalleSolicitudFac)
                                    {
                                    $intIdSolFactibilidad =  $emDetalleSolicitudFac->getId();
                                    //Detalle sol_caractiristica, el tipo de sol factibilidad con el id_servicio
                                    $entityMetraje = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->getSolicitudCaractPorTipoCaracteristica($intIdSolFactibilidad,'METRAJE FACTIBILIDAD');

                                    /* Valores precio de obra civil, Cancela el cliente, asume el cliente, etc  */
                                    $objInfoDetalleSolCaractOCivil = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaOCivil,
                                                            'detalleSolicitudId' => $intIdSolFactibilidad  ));
                                    $objInfoDetalleSolCaractOtrosMateriales = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaOtrosMateriales,
                                                            'detalleSolicitudId' => $intIdSolFactibilidad  ));
                                    $objInfoDetalleSolCaractCanceladoCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaCancPorCli,
                                                            'detalleSolicitudId' => $intIdSolFactibilidad  ));
                                    $objInfoDetalleSolCaractAsumeCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaAsumeCli,
                                                            'detalleSolicitudId' => $intIdSolFactibilidad  ));
                                    $objInfoDetalleSolCaractAsumeEmpresa = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                        ->findOneBy(array('caracteristicaId' => $objCaracteristicaAsumeEmpresa,
                                                            'detalleSolicitudId' => $intIdSolFactibilidad  ));

                                        // Si existe la entidad obtiene el valor.
                                        if($entityMetraje)
                                        {
                                            $strMetrajeC = $entityMetraje[0]->getValor();
                                        }

                                        // Buscar el parámetro del precio de fibra
                                        $arrayParametrosFibra =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->getOne("Precio de fibra", 
                                                                                            "SOPORTE", 
                                                                                            "", 
                                                                                            "Precio de fibra", 
                                                                                            "", 
                                                                                            "", 
                                                                                            "",
                                                                                            "",
                                                                                            "",
                                                                                            10
                                                                                        );
                                        if(is_array($arrayParametrosFibra) && !empty($arrayParametrosFibra) && $strMetrajeC)
                                        {
                                            $intPrecioFibra             = $arrayParametrosFibra['valor1'];
                                            $arrayParametrosMaximoFibra =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                            ->getOne('Metraje que cubre el precio de instalación',
                                                                                        "COMERCIAL",
                                                                                        "",
                                                                                        'Metraje que cubre el precio de instalación',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        '',
                                                                                        10);
                                            if(isset($arrayParametrosMaximoFibra["valor1"]) && !empty($arrayParametrosMaximoFibra["valor1"]))
                                            {
                                                $intMetrosDeDistancia = $arrayParametrosMaximoFibra["valor1"];
                                            }
                                        }                                             
                                
                                        if($objInfoDetalleSolCaractOCivil)
                                        {
                                            $floatValorCaractOCivil          = $objInfoDetalleSolCaractOCivil->getValor();
                                        }
                                        if($objInfoDetalleSolCaractOtrosMateriales)
                                        {
                                            $floatValorCaractOtrosMateriales = $objInfoDetalleSolCaractOtrosMateriales->getValor();
                                        }

                                        if($objInfoDetalleSolCaractCanceladoCliente)
                                        {
                                            $floatPorcentajeCanceladoCliente = $objInfoDetalleSolCaractCanceladoCliente->getValor();
                                        }
                                        if($objInfoDetalleSolCaractAsumeCliente)
                                        {
                                            $floatPrecioAsumeCliente = $objInfoDetalleSolCaractAsumeCliente->getValor();
                                        }
                                        if($objInfoDetalleSolCaractAsumeEmpresa)
                                        {
                                            $floatPrecioAsumeEmpresa = $objInfoDetalleSolCaractAsumeEmpresa->getValor();
                                        }

                                        if($strMetrajeC > $intMetrosDeDistancia)
                                        {
                                            if($floatPorcentajeCanceladoCliente)
                                            {
                                                $floatTotalPagar     = $floatPrecioAsumeCliente;
                                            }
                                            else
                                            {
                                                $floatTotalExcedente = $strMetrajeC - $intMetrosDeDistancia;
                                                $floatTotalPagar     = $intPrecioFibra * $floatTotalExcedente;

                                                if(($floatValorCaractOCivil!=0) || ($floatValorCaractOtrosMateriales!=0)) 
                                                {
                                                    $floatTotalPagar = $floatTotalPagar + $floatValorCaractOCivil 
                                                                        +  $floatValorCaractOtrosMateriales ;
                                                }
                                                else
                                                {
                                                    $floatTotalPagar;
                                                }
                                            }
                                        }
                                        elseif(($floatValorCaractOCivil!=0) || ($floatValorCaractOtrosMateriales!=0)) 
                                        {
                                            $floatTotalPagar = $floatTotalPagar + $floatValorCaractOCivil 
                                                                +  $floatValorCaractOtrosMateriales ;
                                        }
                                        else
                                        {
                                            throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                        }
                                      }
                                    }

                                    if($floatTotalPagar==0)
                                    {
                                        throw new \Exception(': EL CLIENTE NO TIENE UN VALOR A FACTURAR, VERIFIQUE EL EXCEDENTE');
                                    }

                                    // Es decir que ingresa aquí si se convirtió a PrePlanificada la OT y tiene evidencias
                                    // Caso de Comercial y es copagos
                                    if( (is_object($entitySolicitud) ) && $strEvidencia )
                                    {
                                        $objParametroCabCodigo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array("descripcion"=>'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN', 
                                                                        "modulo"=>'COMERCIAL',
                                                                        "estado"=>'Activo'));
                                        if(is_object($objParametroCabCodigo))
                                        {   
                                
                                            $objParamDetCodigo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findOneBy(array("descripcion" => 'CODIGO DE MATERIAL DE FIBRA OPTICA',
                                                                            "parametroId" => $objParametroCabCodigo->getId(),
                                                                            "estado"      => 'Activo'));
                                    
                                            //Variable del código del material para insertar a la infodetalleSolMaterial.
                                            $strCodigoMaterial  = $objParamDetCodigo->getValor1();
                                        }
                                        else
                                        {
                                            throw new \Exception(': NO HAY PARÁMETRO: <br> <b>INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN</b>');
                                        }
                                    
                                            //formatear a solo dos decimales
                                            $floatTotalPagar = number_format($floatTotalPagar, 2,'.','');

                                            $intCantidadEstimada    = 1;
                                            $strCostoMaterial       = 0;
                                            $intCantidadCliente     = 1;
                                            $intCantidadUsada       = 0;
                                            $intCantidadFacturada   = 1;
                                            $strPrecioVentaMaterial = $floatTotalPagar;
                                            $strValorCobrado        = $floatTotalPagar;
                                            /* Se debe generar facturación automática por el valor del excedente
                                            que cancelará el cliente, registra los valores en InfoDetalleSolMaterial*/
                                            $arrayParametrosSolMat = array(
                                                    "emComercial"                => $this->emcom,
                                                    "strClienteIp"               => $strIp,
                                                    "intIdDetalleSolicitud"      => $entitySolicitud->getId(),
                                                    "strUsrCreacion"             => $strUser,
                                                    "strCodigoMaterial"          => $strCodigoMaterial,
                                                    "strCostoMaterial"           => $strCostoMaterial,
                                                    "strPrecioVentaMaterial"     => $strPrecioVentaMaterial,
                                                    "intCantidadEstimada"        => $intCantidadEstimada,
                                                    "intCantidadCliente"         => $intCantidadCliente,
                                                    "intCantidadUsada"           => $intCantidadUsada,
                                                    "intCantidadFacturada"       => $intCantidadFacturada,
                                                    "strValorCobrado"            => $strValorCobrado);
                                            //ENVÍO VALORES EN INFO DETALLE SOLICITUD MATERIAL
                                            $arrayVerificar = $serviceAutorizacion->registroSolicitudMaterial($arrayParametrosSolMat);
                                            if ($arrayVerificar['status'] == 'ERROR')
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: registroSolicitudMaterial
                                                                    <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                                            }

                                            /* Se envía al asesor y asistente como seguimiento */
                                            $strAsunto          = "Autorización automática de materiales excedentes: "
                                                                        . $entityInfoServicio->getPuntoId()->getLogin() ;
        
                                            $strSeguimiento     = 'El cliente autorizó el excedente de materiales ';
                                            $arrayParametrosMail = array(
                                                        "login"      => $entityInfoServicio->getPuntoId()->getLogin(),
                                                        "producto"   => $entityInfoServicio->getProductoId()->getDescripcionProducto(),
                                                        "mensaje"    => $strSeguimiento
                                                                ); 
                                                                 //Obtenemos la forma de contacto del creador del servicio
                                          $arrayFormasContactoAsistente = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($entityInfoServicio->getPuntoId()
                                                            ->getUsrCreacion(),'Correo Electronico');            
                                                    
                                          //Obtenemos la forma de contacto del asistente
                                          $arrayFormasContactoAsesor = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                                ->getContactosByLoginPersonaAndFormaContacto($entityInfoServicio->getPuntoId()
                                                                ->getUsrVendedor(),'Correo Electronico');                        
                        
                                         // Obtenemos el Correo de GTN .
                                          $objParametroCargo      = $this->emcom->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array("descripcion"=>'Cargo que autoriza excedente de material', 
                                                                    "modulo"=>'PLANIFICACIÓN',  "estado"=>'Activo'));

                                             $objCargoAutoriza       = $this->emcom->getRepository('schemaBundle:AdmiParametroDet')
                                            ->findOneBy(array("descripcion"   => 'Cargo que recibirá solicitud de excedente de material', 
                                                            "parametroId" => $objParametroCargo->getId(), "estado"      => 'Activo'));

                                             $objDepartamento        = $this->emcom->getRepository('schemaBundle:AdmiDepartamento')
                                                        ->findOneBy(array("nombreDepartamento" =>$objCargoAutoriza->getValor2(),
                                                                        "estado"             =>'Activo'));

                                            $objRol = $this->emcom->getRepository('schemaBundle:AdmiRol')
                                                    ->findOneBy(array("descripcionRol" => $objCargoAutoriza->getValor1()));
                        
                                            $objEmpresaRol           = $this->emcom->getRepository('schemaBundle:InfoEmpresaRol')
                                                     ->findOneBy(array("rolId"      => $objRol->getId(),
                                                    "empresaCod" => $strCodEmpresa, "estado"     => 'Activo'));
                        
                                         $objPersonaEmpresaRol    = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                     ->findOneBy(array("empresaRolId"   => $objEmpresaRol->getId(),
                                                        "departamentoId" => $objDepartamento->getId(),
                                                        "estado"         => 'Activo'));

                                            $arrayFormasContactoAGtn = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($objPersonaEmpresaRol
                                                        ->getPersonaId()->getLogin(),'Correo Electronico');  
                                            $arrayParametrosNotif = array(
                                                                "strAsunto"                         => $strAsunto,
                                                                "arrayParametrosMail"               => $arrayParametrosMail,
                                                                "arrayDestinatario"                 => 'Alias',
                                                                "strCodEmpresa"                     => $strCodEmpresa,
                                                                "serviceEnvioPlantilla"             => $this->serviceEnvioPlantilla,
                                                                "arrayFormasContactoAsistente"      => $arrayFormasContactoAsistente,
                                                                "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                                                "arrayFormasContactoAGtn"           => $arrayFormasContactoAGtn);
                                            $arrayVerificar = $serviceSolicitudes->envioDeNotificaciones($arrayParametrosNotif);
                                            if($arrayVerificar['status'] == 'ERROR' )
                                            {
                                            throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: envioDeNotificaciones
                                                                    <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                                            }
        
                                            //INSERTAR INFO SERVICIO HISTORIAL      - InfoServicioHistorial
                                            $strEstadoEnviado = "PrePlanificada";
                                            $arrayParametrosTraServ = array(
                                                                "emComercial"                => $this->emcom,
                                                                "strClienteIp"               => $strIp,
                                                                "objServicio"                => $entityInfoServicio,
                                                                "strSeguimiento"             => $strSeguimiento,
                                                                "strUsrCreacion"             => $strUser,
                                                                "strAccion"                  => '',
                                                                "strEstadoEnviado"           => $strEstadoEnviado  );
                                             $arrayVerificar = $serviceAutorizacion->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                                             if($arrayVerificar['status'] == 'ERROR' )
                                             {
                                                throw new \Exception(': EN EL PROCESO: registroTrazabilidadDelServicio 
                                                                <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                                             }                                            
                                    }
                                }
                               }

                                $boolGeneraTarea                          = false;
                                //Flujo Soluciones
                                $strProceso                               = '';
                                $strObservacion                           = '';
                                $strLogin                                 = $entityServicio->getPuntoId()->getLogin();
                                
                                $arrayParametrosSolucion                  = array();
                                $arrayParametrosSolucion['objServicio']   = $entityServicio;
                                $arrayParametrosSolucion['strCodEmpresa'] = $strCodEmpresa;
                                $strSolucion                              = $this->serviceGeneral->getNombreGrupoSolucionServicios
                                                                                                   ($arrayParametrosSolucion);
                                
                                if(!empty($strSolucion))
                                {                                 
                                    $strNombreTecnico = $entityServicio->getProductoId()->getNombreTecnico();

                                    if($strNombreTecnico == 'HOUSING')
                                    {
                                        $strNombreParametro = 'HOUSING TAREAS POR DEPARTAMENTO';
                                        $boolGeneraTarea    = true;
                                        //Se levanta tarea para que IPCCL2 realice la coordinacion del servicio con el cliente
                                        $strProceso         = 'COORDINACION HOUSING';
                                        $strObservacion     = 'Tarea automática: Realizar Coordinación con el Cliente '
                                                            . 'que contrata Servicio HOUSING .'
                                                            . '<br><b>Login : </b> '.$strLogin
                                                            . '<br>'.$strSolucion;
                                    }

                                    if($strNombreTecnico == 'INTERNETDC' || $strNombreTecnico == 'DATOSDC' || 
                                       $strNombreTecnico == 'DATOS DC SDWAN' || $strNombreTecnico == 'INTERNET DC SDWAN')
                                    {
                                        $boolGeneraTarea    = true;
                                        $strNombreParametro = 'HOUSING TAREAS POR DEPARTAMENTO';
                                        
                                        if ($strNombreTecnico != 'INTERNETDC' && $strNombreTecnico!='DATOSDC')
                                        {
                                            $strNombreProducto  = $strNombreTecnico;
                                        }
                                        else
                                        {
                                            $strNombreProducto  = $strNombreTecnico=='INTERNETDC'?'INTERNET DC':'DATOS DC';
                                        }
                                        
                                        $strProceso         = 'COORDINACION INTERNET DC';
                                        $strObservacion     = 'Tarea automática: Realizar Coordinación de Producto '
                                                              . $strNombreProducto. ' sobre'
                                                              . ' solución HOUSING .'
                                                              . '<br><b>Login : </b> '.$strLogin
                                                              . '<br>'.$strSolucion;
                                    }

                                    if($strNombreTecnico == 'HOSTING')
                                    {
                                        $boolGeneraTarea    = true;
                                        $strNombreParametro = 'HOSTING TAREAS POR DEPARTAMENTO';
                                        $strProceso         = 'COORDINACION HOSTING';//ipccl2
                                        $strObservacion     = 'Se solicitó planificación '
                                                            . 'Producto POOL DE RECURSOS ( HOSTING ) . '
                                                            . '<br><b>Login : </b> '.$strLogin
                                                            . '<br>'.$strSolucion;
                                    }
                                }
                                else if($strNombreTecnico == 'L2MPLS')
                                {
                                    $strNombreParametro = 'TAREAS POR DEPARTAMENTO PARA L2MPLS';
                                    $strProceso         = 'COORDINACION L2';
                                    $strObservacion     = 'Tarea Automática: Se solicita coordinación para activación de Enlace en L2MPLS.<br/>'
                                                        . '<b>Login : </b> '.$strLogin.'<br>';
                                }
                                
                                $intIdCanton     = 0;
                                //Obtener el canton relacionado a una region para procesos DC
                                $strNombreCanton = $this->serviceGeneral->getCiudadRelacionadaPorRegion($entityServicio,$strCodEmpresa);
                                
                                if(!empty($strNombreCanton))
                                {
                                    $objCanton = $this->emcom->getRepository("schemaBundle:AdmiCanton")
                                                             ->findOneByNombreCanton($strNombreCanton);

                                    if(is_object($objCanton))
                                    {
                                        $intIdCanton = $objCanton->getId();
                                    }
                                }
                                
                                if($strNombreTecnico == 'CONCINTER' )
                                {
                                    $boolGeneraTarea = true;
                                }
                                
                                if($boolGeneraTarea)
                                {
                                    //Si se trata de flujo de Interconexion se envia tarea a L2 para que genere asignacion de recursos de red
                                    //para este servicio concentrador
                                    if($strNombreTecnico == 'CONCINTER')
                                    {
                                        $strObservacion  = '<b>Tarea Automática:</b> Realizar la Asignación de Recursos de Red del '
                                                         . 'Servicio Concentrador para Interconexión entre Clientes ';
                                        
                                        $objDepartamento = $this->emGeneral->getRepository("schemaBundle:AdmiDepartamento")
                                                                           ->findOneByNombreDepartamento('IPCCL2');
                                        
                                        $arrayParametrosEnvioPlantilla                      = array();
                                        $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;
                                        $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $strUser;
                                        $arrayParametrosEnvioPlantilla['strIpCreacion']     = $strIp;
                                        $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $entitySolicitud->getId();
                                        $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
                                        $arrayParametrosEnvioPlantilla['objPunto']          = $entityServicio->getPuntoId();
                                        $arrayParametrosEnvioPlantilla['objDepartamento']   = $objDepartamento;
                                        $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
                                        $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $strCodEmpresa;
                                        $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $strPrefijoEmpresa;

                                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";

                                        $objAdmiProceso   = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                                                                            ->findOneByNombreProceso($strNombreProceso);

                                        if(is_object($objAdmiProceso))
                                        {
                                            $arrayTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                           ->findTareasActivasByProceso($objAdmiProceso->getId());

                                            foreach($arrayTareas as $objTarea)
                                            {
                                                if(is_object($objTarea))
                                                {
                                                    $arrayParametrosEnvioPlantilla['intTarea'] = $objTarea->getId();
                                                }
                                            }

                                            $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";
                                            $this->serviceCambiarPlanService
                                                 ->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
                                        }
                                    }
                                    else
                                    {
                                        $arrayParametrosEnvioPlantilla                      = array();
                                        $arrayParametrosEnvioPlantilla['strUsrCreacion']    = $strUser;
                                        $arrayParametrosEnvioPlantilla['strIpCreacion']     = $strIp;
                                        $arrayParametrosEnvioPlantilla['intDetalleSolId']   = $entitySolicitud->getId();
                                        $arrayParametrosEnvioPlantilla['strTipoAfectado']   = 'Cliente';
                                        $arrayParametrosEnvioPlantilla['objPunto']          = $entityServicio->getPuntoId();
                                        $arrayParametrosEnvioPlantilla['strCantonId']       = $intIdCanton;
                                        $arrayParametrosEnvioPlantilla['strEmpresaCod']     = $strCodEmpresa;
                                        $arrayParametrosEnvioPlantilla['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                                        $arrayParametrosEnvioPlantilla['strObservacion']    = $strObservacion;

                                        $arrayInfoEnvio   =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->get($strNombreParametro, 
                                                                                    'SOPORTE', 
                                                                                    '',
                                                                                    $strProceso,
                                                                                    $strNombreCanton,
                                                                                    '',
                                                                                    '',
                                                                                    '', 
                                                                                    '', 
                                                                                    $strCodEmpresa);
                                        foreach($arrayInfoEnvio as $array)                    
                                        {
                                            $objTarea                                            = $this->emSoporte
                                                                                                        ->getRepository("schemaBundle:AdmiTarea")
                                                                                                        ->findOneByNombreTarea($array['valor3']);
                                            $arrayParametrosEnvioPlantilla['intTarea']           = is_object($objTarea)?$objTarea->getId():'';

                                            if(isset($array['valor2']) && !empty($array['valor2']))
                                            {
                                                $arrayParametrosEnvioPlantilla['arrayCorreos']   = array($array['valor2']);
                                            }

                                            $objDepart                                           = $this->emSoporte
                                                                                                      ->getRepository("schemaBundle:AdmiDepartamento"
                                                                                                                     )
                                                                                                      ->findOneByNombreDepartamento($array['valor4']
                                                                                                                                   );
                                            $arrayParametrosEnvioPlantilla['objDepartamento']    = $objDepart;
                                            $arrayParametrosEnvioPlantilla["strBanderaTraslado"] = "";

                                            $this->serviceCambiarPlanService
                                                 ->crearTareaRetiroEquipoPorDemo($arrayParametrosEnvioPlantilla);
                                        }
                                    }
                                }
                            }

                            if($strPrefijoEmpresa == "MD")
                            {
                                // Verificación y generación de solicitudes por preplanificación de productos CIH
                                $arrayParamsPreplanificaCIH = array('intIdServicioInternet'  => $entityServicio->getId(),
                                                                    'intIdPunto'             => $entityServicio->getPuntoId()->getId(),
                                                                    'strUsuarioCreacion'     => $strUser,
                                                                    'strIpCreacion'          => $strIp,
                                                                    'strOrigen'              => "REINGRESO_SERVICIOS",
                                                                    'strPrefijoEmpresa'      => $strPrefijoEmpresa,
                                                                    'strCodEmpresa'          => $strCodEmpresa);

                                $arrayResponseCIH = $this->serviceInfoContrato->preplanificaProductosCIH($arrayParamsPreplanificaCIH);

                                if ($arrayResponseCIH['status'] != 'OK')
                                {
                                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: preplanificaProductosCIH
                                                                        <br> <b>'.$arrayResponseCIH['mensaje'].'</b>');
                                }
                            }
                        }
                    }
                }
            }
            
            if($strPrefijoEmpresa == 'MD' && !empty($intIdPunto) && is_object($entityServicio))
            {
                $arrayParametrosValor = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                   ->get('VALIDA_PROD_ADICIONAL', 
                                                                         'COMERCIAL', 
                                                                         '',
                                                                         '',
                                                                         'PROD_ADIC_PLANIFICA',
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         '',
                                                                         $strCodEmpresa);
                if (is_array($arrayParametrosValor) && !empty($arrayParametrosValor))
                {
                    foreach($arrayParametrosValor as $arrayParametro)
                    {
                        $arrayParametros    =    array("Punto"      => $intIdPunto,
                                                       "Producto"   => $arrayParametro['valor2'],
                                                       "Servicio"   => $entityServicio->getId(),
                                                       "Estado"     => 'Todos');
                        $arrayResultado = $this->emcom->getRepository('schemaBundle:InfoServicio')->getProductoByPlanes($arrayParametros);
                        if($arrayResultado['total'] > 0)
                        {
                            $arrayDatos     =    array("Punto"          => $intIdPunto,
                                                       "Producto"       => $arrayParametro['valor2'],
                                                       "Servicio"       => $entityServicio->getId(),
                                                       "Observacion"    => $arrayParametro['valor3'],
                                                       "Caracteristica" => $arrayParametro['valor4'],
                                                       "Usuario"        => $strUser,
                                                       "Ip"             => $strIp,
                                                       "EmpresaId"      => $strCodEmpresa,
                                                       "OficinaId"      => $strOficina);

                            $this->serviceGeneral->generarOtServiciosAdicional($arrayDatos);
                        }
                    }
                }
            }
            
            $strRespuesta = "Se generaron las ordenes de trabajo de los servicios seleccionados.";
            $this->emcom->getConnection()->commit();
            
            // Verificación y generación de OT por solicitudes de preplanificación para productos CIH
            if(!empty($arrayParamsPreplanificaCIH) && $strPrefijoEmpresa == "MD")
            {
                $arrayResponseCIH = $this->serviceInfoContrato->generacionOtServicioCIH($arrayParamsPreplanificaCIH);

                if ($arrayResponseCIH['status'] != 'OK')
                {
                    throw new \Exception(': NO SE REALIZÓ EL PROCESO COMPLETO: generacionOtServicioCIH
                                    <br> <b>'.$arrayResponseCIH['mensaje'].'</b>');
                }
            }

        }
        catch (\Exception $e)
        {   
            $this->emcom->rollback();
            $this->emcom->close();
            $strRespuesta = "No se pudo generar la(s) orden(es) de pago(s) <br>". $e->getMessage() . ". Favor notificar a Sistemas.";
            $this->serviceUtil->insertError('Telcos+',
                                            'ConvertirOrdenTrabajoService.convertirOrdenTrabajo',
                                            'Error ConvertirOrdenTrabajoService.convertirOrdenTrabajo:'.$e->getMessage(),
                                            $strUser,
                                            $strIp); 

            // Verificación y reverso de solicitudes por preplanificación para productos CIH
            if(!empty($arrayParamsPreplanificaCIH) && $strPrefijoEmpresa == "MD")
            {
                $this->serviceInfoContrato->reversaPreplanificacionCIH($arrayParamsPreplanificaCIH);
            }

            return $strRespuesta;
        }
        return $strRespuesta;
    }

    /**
    * validacionesPreviasConvertirOT, se encarga de realizar validaciones previas para los flujos de convertir un
    * servicio a una orden de trabajo.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 05-03-2020    
    *
    * @param array $arrayParametros []
    *              'arrayPtoCliente'         => Id de la promoción.
    *              'arrayCliente'            => Estado de la promoción.
    *              'strOpcion'               => Opción de evaluación "WEB" ó "REINGRESO".
    *              'booleanProcesaNoPagados' => Variable de rool.
    *              'strCodEmpresa'           => Código de la empresa.
    *              'strUser'                 => Usuario transaccional.
    *              'strIp'                   => Ip transaccional.
    *
    * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
    * @version 1.1 28-10-2020   Se agrega PR-001-OP-004: Validar Documento de Devolución. Se validará que la Factura de Instalación de los servicios
    *                           que se tengan en el Punto se encuentre Pagada, es decir, la factura de Instalación Cerrada con Pago asociado sin 
    *                           NC y sin anticipo y sin documento DEV asociado 
    *
    * @author Anabelle Peñaherrera
    * @version 1.2 25-05-2021  -Se modifica validación PR-001-OP-004: Validar Documento de Devolución.
    *                           Se deberá permitir reingresar la OS cuando existe una devolución siempre que se tenga una nueva factura de 
    *                           instalación pagada.
    * @return Response lista de valores.
    */
    public function validacionesPreviasConvertirOT($arrayParametros)
    {
        
        $arrayPtoCliente         = $arrayParametros['arrayPtoCliente'];
        $arrayCliente            = $arrayParametros['arrayCliente'];
        $strOpcion               = $arrayParametros['strOpcion'];
        $booleanProcesaNoPagados = $arrayParametros['booleanProcesaNoPagados'];
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'];
        $strUser                 = $arrayParametros['strUser'];
        $strIp                   = $arrayParametros['strIp'];          
        
        $arrayParams['strNombreParametro'] = "PARAMETROS_REINGRESO_OS_AUTOMATICA";
        $arrayParams['strProceso']         = "REINGRESO AUTOMATICO";
        $strDetFacturaActiva               = "MENSAJE_ERROR_POSEE_FACTURA_INSTALACION";
        $strDetNoAnticipos                 = "MENSAJE_ERROR_SIN_ANTICIPOS_MAYOR_FACTURA";

        if ($strOpcion === 'WEB')
        {
            if($arrayPtoCliente)
            {
                $arrayParametro['punto_id'] = $arrayPtoCliente;
                $arrayParametro['cliente']  = $arrayCliente;
            }

            if (!$booleanProcesaNoPagados)
            {
                //SE OBTIENE SI LA EMPRESA APLICA O NO AL FLUJO DE FACTURAS DE INSTALACIÓN.
                $arrayRespuesta = $this->serviceInfoDocumentoFinancieroCab
                                       ->aplicaFlujoOrdenTrabajo(array("intPuntoId"     => $arrayPtoCliente['id'],
                                                                       "strEmpresaCod"  => $strCodEmpresa,
                                                                       "strIpCreacion"  => $strIp,
                                                                       "strUsrCreacion" => $strUser));
            }
            else
            {
                $arrayRespuesta["status"]  = "OK";
                $arrayRespuesta["message"] = null;
            }
            //SE OBTIENEN LAS DEUDAS DEL CLIENTE.
            $arrayRespDeudas                = $this->serviceInfoDocumentoFinancieroCab
                                                    ->obtieneDeudasCliente(array("intIdPersonaEmpresaRol" => $arrayCliente['id_persona_empresa_rol'],
                                                                                 "strEmpresaCod"          => $strCodEmpresa));

            $arrayParametro["arrayPuntosDeuda"]      = $arrayRespDeudas["arrayPuntosDeuda"];
            $arrayParametro["intPuntosDeuda"]        = count($arrayRespDeudas["arrayPuntosDeuda"]);
            $arrayParametro["strMensajeObservacion"] = $arrayRespDeudas["strMensajeObservacion"];
            $arrayParametro["strMuestraGridOT"]      = $arrayRespuesta["status"] == "OK" ? "S" : "N";
            $arrayParametro["strMensajeBloqueo"]     = !is_null($arrayRespuesta["message"]) ? $arrayRespuesta["message"] : null;

        }
        
        if ($strOpcion === 'REINGRESO')
        {   
            
            $entityServicio     = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                              ->find((int)$arrayParametros['intIdServicio']);

            if(!is_object($entityServicio))
            {
                throw new \Exception("Error : No existe el servicio.");
            }

            $objCaracteristica  = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                              ->findOneBy(array('descripcionCaracteristica' => $arrayParametros['strCaracteristica'],
                                                                'tipo'                      => 'COMERCIAL',
                                                                'estado'                    => 'Activo'));
            if(!is_object($objCaracteristica))
            {
                throw new \Exception("Error: Hubo un error al obtener la característica para validar facturación por instalación.");
            }

            $objInfoServCarac   = $this->emcom->getRepository("schemaBundle:InfoServicioCaracteristica")
                                              ->findOneBy(array("servicioId"       => (int)$arrayParametros['intIdServicio'],
                                                                "caracteristicaId" => $objCaracteristica,
                                                                "estado"           => "Inactivo"));

            //SE VALIDA QUE EL SERVICIO TERMINÓ CON ÉXITO SU EVALUCIÓN POR FACTURACIÓN.
            if(is_object($objInfoServCarac))
            {
                $objInfoPunto       = $this->emcom->getRepository("schemaBundle:InfoPunto")
                                                  ->find($arrayParametros["intIdPunto"]);
                
                $arrayListServicios = $this->emcom->getRepository("schemaBundle:InfoServicio")
                                                  ->findBy(array("estado"  => "Factible", 
                                                                 "puntoId" => $objInfoPunto));

                //Se agrega PR-001-OP-004: Validar Documento de Devolución.
                $strNombreParametro           = "PARAMETROS_REINGRESO_OS_AUTOMATICA";
                $strParamDetProceso           = "REINGRESO AUTOMATICO";
                $strParamDetDevolucionFactura = "MENSAJE_ERROR_DEVOLUCION_FACTURA";
                
                //Obtengo si existen Facturas de Instalacion cerradas con pagos a las cuales se les aplico NC , NDI , ANT y  DEV
                $arrayDocumentoConDevolucion  = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                     ->getDocumentoDevolucion(array('intIdPunto'         => $objInfoPunto->getId(),
                                                                                    'arrayEstadosPagos'  => array('Cerrado','Activo'),
                                                                                    'strEstadoActivo'    => 'Activo',
                                                                                    'strEstadoEliminado' => 'Eliminado',
                                                                                    'arrayEstadoFactura' => array('Pendiente','Activo','Cerrado'), 
                                                                                    'arrayTipoDocumento' => array('FACP','FAC'),
                                                                                    'strNombreParametro' => 'SOLICITUDES_DE_CONTRATO',
                                                                                    'strValor'           => 'S',
                                                                                    'strPagada'          => 'N'));
                
                //Obtengo si existen Facturas de Instalacion cerradas con pagos sin DEV
                $arrayDocumentoSinDevolucion  = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                     ->getDocumentoDevolucion(array('intIdPunto'         => $objInfoPunto->getId(),
                                                                                    'arrayEstadosPagos'  => array('Cerrado','Activo'),
                                                                                    'strEstadoActivo'    => 'Activo',
                                                                                    'strEstadoEliminado' => 'Eliminado',
                                                                                    'arrayEstadoFactura' => array('Pendiente','Activo','Cerrado'), 
                                                                                    'arrayTipoDocumento' => array('FACP','FAC'),
                                                                                    'strNombreParametro' => 'SOLICITUDES_DE_CONTRATO',
                                                                                    'strValor'           => 'S',
                                                                                    'strPagada'          => 'S'));
                
                if (!$arrayDocumentoConDevolucion['status'])
                {
                    throw new \Exception('Error al consultar Facturas de Instalación con documento devolución - '.
                        $arrayDocumentoConDevolucion['message']);
                }
                
                if (!$arrayDocumentoSinDevolucion['status'])
                {
                    throw new \Exception('Error al consultar Facturas de Instalación sin documento devolución - '.
                        $arrayDocumentoSinDevolucion['message']);
                }
                
                if ((!empty($arrayDocumentoConDevolucion['result']) && count($arrayDocumentoConDevolucion['result']) > 0)
                    && empty($arrayDocumentoSinDevolucion['result']))
                {
                    $objInfoServicioHistorial = $this->emcom->getRepository('schemaBundle:InfoServicioHistorial')
                                                     ->findOneBy(array('servicioId' => $entityServicio->getId()),
                                                                 array('id'         => 'DESC'));

                    if (is_object($objInfoServicioHistorial))
                    {
                        $strAccionHistorial = $objInfoServicioHistorial->getAccion();
                    }
                    $arrayParams = array('strNombreParametro' => $strNombreParametro, 
                                         'strProceso'         => $strParamDetProceso, 
                                         'strDescripcion'     => $strParamDetDevolucionFactura);
                    
                    $strMsgErrorDevolucionFactura = $this->serviceInfoServicioService->getMensajeReprocesoOS($arrayParams);

                    if(!is_null($strMsgErrorDevolucionFactura) )
                    {
                        $strObservacionHistorial = $strMsgErrorDevolucionFactura;
                    }
                    else
                    {
                        $strObservacionHistorial = "No se creo el servicio mediante Reingreso Automático, ".
                                               "motivo Cliente posee una devolución o no ha pagado la Factura de Instalación: ";
                    }
                    if ($strAccionHistorial !== 'devoluciones')
                    {
                        $this->serviceInfoServicioService->putHistorialServicio(array ('intIdServicio'      => $entityServicio->getId(),
                                                                                       'strObservacion'     => $strObservacionHistorial,
                                                                                       'strUsuarioCreacion' => "telcos_reingresos",
                                                                                       'strIpCreacion'      => $strIp,
                                                                                       'strAccion'          => 'devoluciones'));

                        /* =================== Envio de notificación ===================*/
                        $this->emcom->getRepository('schemaBundle:InfoPunto')->notificarProcesoReingresoOS(
                                array('intIdServicio' => $entityServicio->getId(),
                                      'strMensaje'    => $strObservacionHistorial,
                                      'strUsuario'    => $strUser,
                                      'strIp'         => $strIp));
                    }                                     
                    $arrayParametro ["strMuestraGridOT"] = "N";
                    return $arrayParametro;
                }                
                               
                $arrayServicios     = array();
                foreach ($arrayListServicios as $objServicio)
                {
                    $arrayServicios[] = $objServicio->getId();
                }

                if (count($arrayServicios) == 0)
                {
                    $arrayParametro ["strMuestraGridOT"] = "N";
                    return $arrayParametro;
                }

                $arrayEstadosTodos  = array("Pendiente","Activo","Cerrado");
                //SE PROCEDE A CONSULTAR SI EXISTE UNA FACTURA DE INSTALACIÓN ENTRE LOS ESTADOS VALIDOS.
                $strFactInstPagada  = $this->emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                         ->esUltimaFactInstalacionPagada(array("arrayServicios"     => $arrayServicios,
                                                                                               "strNombreParametro" => "SOLICITUDES_DE_CONTRATO",
                                                                                               "arrayEstadosFact"   => $arrayEstadosTodos));

                //SI NO EXISTE UNA FACTURA POR INSTALACIÓN SE PROCEDE A CONVERTIR A OT.
                if($strFactInstPagada === "S")
                {
                    $arrayParametro["strMuestraGridOT"] = "S" ;
                    return $arrayParametro;
                }
                else 
                {
                    
                    $arrayParams['strDescripcion'] = $strDetFacturaActiva;
                    $strMsgFactura = $this->serviceInfoServicioService->getMensajeReprocesoOS($arrayParams);
                    if(is_null($strMsgFactura))
                    {
                        $strMsgFactura = "Posee Factura de Instalación en estado Activo.";
                    }
                    
                }

                //SE OBTIENE EL ÚLTIMO ESTADO DE UNA FACTURA DE INSTALACIÓN PARA EVALUAR FACTURAS CERRADAS O PENDIENTES.
                $arrayDatosFactura  = $this->emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                         ->datosUltimaFactInstalacion(array("arrayServicios"     => $arrayServicios,
                                                                                            "strNombreParametro" => "SOLICITUDES_DE_CONTRATO"));
                //SI LA FACTURA ESTÁ CERRADA, SE VERIFICA QUE FUE CERRADA POR UN PAGO.
                if($arrayDatosFactura[0]['strEstado'] === "Cerrado")
                {
                    $arrayEstadosCerrado = array($arrayDatosFactura['strtEstado']);
                    $strFactInstPagada   = $this->emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                              ->esUltimaFactInstalacionPagada(array("arrayServicios"     => $arrayServicios,
                                                                                                    "strNombreParametro" => "SOLICITUDES_DE_CONTRATO",
                                                                                                    "arrayEstadosFact"   => $arrayEstadosCerrado));

                    if($strFactInstPagada === "S")
                    {
                        $arrayParametro["strMuestraGridOT"] = "S" ;
                        return $arrayParametro;
                    }
                }

                /*SI LA FACTURA ESTÁ PENDIENTE Ó ACTIVO, SE VERIFICA QUE EXISTA UN ANTICIPO CON UN MONTO IGUAL O MAYOR AL VALOR TOTAL
                 * DE LA FACTURA.
                */
                if($arrayDatosFactura[0]['strEstado'] === "Pendiente" || $arrayDatosFactura[0]['strEstado'] === "Activo")
                {
                    $intIdOficina                   = $entityServicio->getPuntoId()->getPuntoCoberturaId()->getOficinaId();
                    
                    $arrayAnticipos                 = $this->emFinanciero->getRepository('schemaBundle:InfoDocumentoFinancieroCab')
                                                                         ->findAnticiposEstadoDeCuenta($intIdOficina, 
                                                                                                       "", 
                                                                                                       "", 
                                                                                                       $arrayParametros["intIdPunto"], 
                                                                                                       "Pendiente");
                    $arrayAntPendientes = $arrayAnticipos['registros'];
                    
                    if(!empty($arrayAntPendientes))
                    {
                        foreach($arrayAntPendientes as $arrayInfoAntPendientes)
                        {
                            $intValorAnt      = $arrayInfoAntPendientes['valorTotal'];
                            $intValorTotalAnt = $intValorTotalAnt + $intValorAnt;
                        }
                    }
                    else
                    {
                        $intValorTotalAnt  = 0;
                    }
                    
                    $objInfoDocumentoFinancieroCab  = $this->emFinanciero->getRepository("schemaBundle:InfoDocumentoFinancieroCab")
                                                                         ->find($arrayDatosFactura[0]['idDocumento']);
                    $intSaldoFactura                = $objInfoDocumentoFinancieroCab->getValorTotal();
                    if($intValorTotalAnt >= $intSaldoFactura)
                    {
                        $arrayParametro ["strMuestraGridOT"] = "S";
                        $strMsgFactura = null;
                        return $arrayParametro;
                    }
                    else 
                    {    
                        $arrayParams['strDescripcion'] = $strDetNoAnticipos;
                        $strMsgAnticipo = $this->serviceInfoServicioService->getMensajeReprocesoOS($arrayParams);
                        if(is_null($strMsgAnticipo))
                        {
                            $strMsgFactura = $strMsgFactura.". No existen anticipos de valor mayor o igual a la factura";
                        }
                        else 
                        {
                            $strMsgFactura = $strMsgFactura .'<br>'. $strMsgAnticipo;
                        }
                    }                    
                }
            }
            else
            {
                $arrayParametro ["strMuestraGridOT"] = "N";
                return $arrayParametro;
            }
            $arrayParametro["strMsgFactura"] = $strMsgFactura;
            $arrayParametro["strMuestraGridOT"] = "N";
            return $arrayParametro;
        }
        
        return $arrayParametro;
    }

    /**
    * ingresarServicioCaracteristica, Se encarga de la ingresar una característica al servicio.
    *
    * @author José Candelario <jcandelario@telconet.ec>
    * @version 1.0 05-03-2020    
    *
    * @param array $arrayParametros []
    *              'intIdServicio'     => Id de la orden de servicio.
    *              'strCaracteristica' => Nombre de la Característica.
    *              'strObservacion'    => Observación de la transacción.
    *              'strUsuario'        => Usuario responsable de la transacción.
    *              'strIp'             => Ip transaccional.
    */
    public function ingresarServicioCaracteristica($arrayParametros)
    {
        $entityServicio   = $this->emcom->getRepository('schemaBundle:InfoServicio')->find((int)$arrayParametros['intIdServicio']);

        if(!is_object($entityServicio))
        {
            throw new \Exception("Error : No existe el servicio.");
        }
        
        $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                         ->findOneBy(array('descripcionCaracteristica' => $arrayParametros['strCaracteristica'],
                                                           'tipo'                      => 'COMERCIAL',
                                                           'estado'                    => 'Activo'));
        if(!is_object($objCaracteristica))
        {
            throw new \Exception("Error: Hubo un error al obtener la característica para convertir a orden de trabajo.");
        }
        
        $objInfoServCarac = $this->emcom->getRepository("schemaBundle:InfoServicioCaracteristica")
                                        ->findOneBy(array("servicioId"       => (int)$arrayParametros['intIdServicio'],
                                                          "caracteristicaId" => $objCaracteristica,
                                                          "estado"           => "Activo"));
        if (!is_object($objInfoServCarac))
        {
            $objInfoServicioCaracteristica = new InfoServicioCaracteristica();
            $objInfoServicioCaracteristica->setServicioId($entityServicio);
            $objInfoServicioCaracteristica->setCaracteristicaId($objCaracteristica);
            $objInfoServicioCaracteristica->setEstado("Activo");
            $objInfoServicioCaracteristica->setObservacion($arrayParametros['strObservacion']);
            $objInfoServicioCaracteristica->setUsrCreacion($arrayParametros['strUsuario']);
            $objInfoServicioCaracteristica->setFeCreacion(new \DateTime('now'));
            $objInfoServicioCaracteristica->setIpCreacion($arrayParametros['strIp']);
            $objInfoServicioCaracteristica->setValor(0);
            $this->emcom->persist($objInfoServicioCaracteristica);
            $this->emcom->flush();
        }
    }
    
   /**
    * actualizarServicioCaracteristica, Se encarga actualizar una característica al servicio.
    *
    * @author Anabelle Peñaherrera<apenaherrera@telconet.ec>
    * @version 1.0  25-11-2020    
    *
    * @param array $arrayParametros []
    *              'intIdServicio'     => Id de la orden de servicio.
    *              'strCaracteristica' => Nombre de la Característica.
    *              'strObservacion'    => Observación de la transacción.
    *              'strUsuario'        => Usuario responsable de la transacción.
    *              'strIp'             => Ip transaccional.
    */
    public function actualizarServicioCaracteristica($arrayParametros)
    {   
        
        $this->emcom->beginTransaction();

        try
        {
            $entityServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find((int) $arrayParametros['intIdServicio']);

            if(!is_object($entityServicio))
            {
                throw new \Exception("Error : No existe el servicio.");
            }

            $objCaracteristica = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array('descripcionCaracteristica' => $arrayParametros['strCaracteristica'],
                                                        'tipo' => 'COMERCIAL',
                                                        'estado' => 'Activo'));
            if(!is_object($objCaracteristica))
            {
                throw new \Exception("Error: Hubo un error al obtener la característica del servicio.");
            }
            $objInfoServCarac = $this->emcom->getRepository("schemaBundle:InfoServicioCaracteristica")
                                            ->findOneBy(array("servicioId" => (int) $arrayParametros['intIdServicio'],
                                                              "caracteristicaId" => $objCaracteristica,
                                                              "estado" => "Activo"));

            if(is_object($objInfoServCarac))
            {
                $objInfoServCarac->setEstado("Inactivo");
                $objInfoServCarac->setFeUltMod(new \DateTime('now'));
                $objInfoServCarac->setUsrUltMod($arrayParametros['strUsuario']);
                $objInfoServCarac->setIpUltMod($arrayParametros['strIp']);
                $objInfoServCarac->setObservacion("Se inactiva característica, el servicio ya ejecutó el flujo correspondiente");
                $this->emcom->persist($objInfoServCarac);
                $this->emcom->flush();
                $this->emcom->commit();
            }
        }
        catch(\Exception $objException)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }

            $strMessage = 'Error al actualizar la caracteristica del servicio';

            if(strpos($objException->getMessage(), 'Error : ') !== false)
            {
                $strMessage = $objException->getMessage();
            }

            $this->utilServicio->insertError('Telcos+', 'ConvertirOrdenTrabajoService->actualizarServicioCaracteristica', 
                                             substr($objException->getMessage(), 0, 4000),
                                             $arrayParametros['strUsuario'] ? $arrayParametros['strUsuario'] : 'Telcos+', 
                                             $arrayParametros['strIp'] ? $arrayParametros['strIp'] : '127.0.0.1');           
        }        
    }
}
