<?php

namespace telconet\tecnicoBundle\Service;

use mysql_xdevapi\Exception;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoServicioTecnico;
use telconet\schemaBundle\Service\UtilService;

use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoElementoWifiService {
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
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
    private $emComunicacion;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emSeguridad;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emNaf;
    private $servicioGeneral;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $serviceInfoElemento;
    private $serviceUtil;
    private $serviceSolicitudes;
    private $envioPlantilla;

  
    public function __construct(Container $container)
    {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
        $this->serviceSoporte       = $this->container->get('soporte.SoporteService');
        $this->serviceInfoElemento  = $container->get('tecnico.InfoElemento');
        $this->serviceSolicitudes   = $this->container->get('comercial.Solicitudes');
        $this->envioPlantilla       = $container->get('soporte.EnvioPlantilla');
    }
  
    public function setDependencies(InfoServicioTecnicoService  $servicioGeneral, 
                                    InfoCancelarServicioService $cancelarServicio,
                                    NetworkingScriptsService    $networkingScript,
                                    UtilService                 $serviceUtil,
                                    InfoCambioElementoService   $cambioElemento)
    {
        $this->servicioGeneral      = $servicioGeneral;
        $this->cancelarServicio     = $cancelarServicio;
        $this->networkingScripts    = $networkingScript;
        $this->serviceUtil          = $serviceUtil;
        $this->cambioElemento       = $cambioElemento;

        
    }
    
    /**
    * configurarBwMasivo
    * reconfigura el BW masivamente
    *
    * @params $strBw
    * @params $strIdCliente
    *
    * @return $strMEnsaje
    *
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 03-10-2016
    */
    public function configurarBwMasivo($strBw, $strIdCliente)
    {
        $strMEnsaje = 'OK';

        try
        {
            if($strBw && $strIdCliente)
            {
                $arrayInterfaces = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                    ->getPuertosPorProductoCliente('INTERNET WIFI', $strIdCliente);

                if($arrayInterfaces)
                {
                    foreach($arrayInterfaces as $value)
                    {
                        $arrayService['url'] = 'configBW';
                        $arrayService['accion'] = 'reconectar';
                        $arrayService['sw'] = $value['nombreElemento'];
                        $arrayService['user_name'] = 'regulaBwWifi';
                        $arrayService['user_ip'] = '0.0.0.0';
                        $arrayService['bw_up'] = $value['anchoBanda'];
                        $arrayService['bw_down'] = $value['anchoBanda'];
                        $arrayService['servicio'] = 'L3MPLS';
                        $arrayService['login_aux'] = '';
                        $arrayService['pto'] = $value['nombreInterface'];
                        $arrayService['anillo'] = $value['anillo'];

                        //Ejecucion del metodo via WS para realizar la configuracion del SW
                        $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayService);
                        $status = $arrayRespuestaBw['status'];
                        $mensaje = $arrayRespuestaBw['mensaje'];
                        echo 'Parámetros: Elemento-> ' . $value['nombreElemento'] . ', Interface-> ' . $value['nombreInterface'] . ', Anillo-> '
                        . $value['anillo'] . ', Bw-> ' . $value['anchoBanda'] . "\n";
                        if($status == "OK")
                        {
                            $strMensaje = $status;
                        }
                        else
                        {
                            $strMensaje = $mensaje;
                        }
                        echo 'Respuesta: ' . $strMensaje . "\n";
                    }
                }
                else
                {
                    $strMEnsaje = "La consulta no obtuvo ningún resultado.";
                }
            }
            else
            {
                $strMEnsaje = "Debe enviar todos los parámetros.";
            }
        }
        catch(\Exception $e)
        {
            $strMEnsaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
        }
        return $strMEnsaje;
    }

    /**
     * ingresoElemento
     * funcion que permite el ingreso del elemento
     *
     * @params $arrayPeticiones [$idEmpresa, $idServicio, $interfaceElementoId, $strInterfaceElementoSplitter, $idProducto, $ultimaMilla, $login,
     *                           $macWifi, $serieWifi, $modeloWifi, $ssid, $password, $numeroPc, $modoOperacion, $observacion, $usrCreacion,
     *                           $ipCreacion]
     * 
     * @return result
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-05-2016
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 27-08-2019 | Se modifica lógica, para que cuando sea un Wifi Alquiler de Equipos, agregue la ultima milla
     *                           y finalice la tarea de planificación de existir una.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.2 01-06-2020 - Se modifica funcionalidad para permitir que los servicios con caracteristica INSTALACION_SIMULTANEA
     *                           registre su equipo.
     * 
     * @author Jubert Goya <jgoya@telconet.ec>
     * @version 1.2 06-02-2023 - Se modifica funcionalidad que crea nombre de elemento cliente con login aux de servicio y tipo de
     *                           elemento, se agrega logica que registra trazabilidad en naf de productos sin flujo
     * 
     */
    public function ingresoElemento($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $intIdEmpresa                  = $arrayPeticiones['intIdEmpresa'];
        $intIdServicio                 = $arrayPeticiones['intIdServicio'];
        $intIdProducto                 = $arrayPeticiones['intIdProducto'];
        $strUltimaMilla                = $arrayPeticiones['strUltimaMilla'];
        $strLogin                      = $arrayPeticiones['strLogin'];
        $strMacWifi                    = $arrayPeticiones['strMacWifi'];
        $strSerieWifi                  = strtoupper($arrayPeticiones['strSerieWifi']);
        $strModeloWifi                 = $arrayPeticiones['strModeloWifi'];
        $strSsid                       = $arrayPeticiones['strSsid'];
        $strPassword                   = $arrayPeticiones['strPassword'];
        $intNumeroPc                   = $arrayPeticiones['intNumeroPc'];
        $strModoOperacion              = $arrayPeticiones['strModoOperacion'];
        $strObservacion                = $arrayPeticiones['strObservacion'];
        $strUsrCreacion                = $arrayPeticiones['strUsrCreacion'];
        $intIdUsrCreacion              = $arrayPeticiones['intIdUsrCreacion'];
        $strIpCreacion                 = $arrayPeticiones['strIpCreacion'];
        $strIpElemento                 = $arrayPeticiones['ipElementoCliente'];
        $intVlan                       = $arrayPeticiones['intVlan'];
        $intCapacidad1                 = $arrayPeticiones['intCapacidad1'];
        $boolRequiereRegistro          = isset($arrayPeticiones['boolRequiereRegistro']) ? $arrayPeticiones['boolRequiereRegistro'] : null;

        $boolValidaNaf                 = isset($arrayPeticiones['boolValidaNaf']) ? $arrayPeticiones['boolValidaNaf'] : 'SI';
        $arrayCaractAdicionales        = isset($arrayPeticiones['arrayCaractAdicionales']) ? $arrayPeticiones['arrayCaractAdicionales'] : null;

        if(is_string($boolValidaNaf) && $boolValidaNaf == 'SI')
        {
            $boolValidaNaf = true; 
        }
        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!$objServicio)
            {
                $arrayResult[] = array("status" => "ERROR", "mensaje" => "No existe informacion del servicio.");
            }

            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));
            if($objServicioTecnico)
            {
                if($objServicioTecnico->getInterfaceElementoConectorId())
                {
                    $objInterfaceConector = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                    ->find($objServicioTecnico->getInterfaceElementoConectorId());
                }

            }
            else
            {
                //creo la nueva info tecnica
                $objServicioTecnico = new InfoServicioTecnico();
                $objServicioTecnico->setServicioId($objServicio);
                $objServicioTecnico->setTipoEnlace('PRINCIPAL');

                if($strUltimaMilla)
                {
                    $objTipoMedio = $this->emComercial->getRepository('schemaBundle:AdmiTipoMedio')
                                                      ->findOneBy(array("nombreTipoMedio"   => $strUltimaMilla,
                                                                        "estado"            => 'Activo'));
                    if($objTipoMedio)
                    {
                        $objServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
                    }
                }

                $this->emComercial->persist($objServicioTecnico);
                $this->emComercial->flush();            
            }

            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto); 

            if(!$objProducto)
            {
                $arrayResult[] = array("status" => "ERROR", "mensaje" => "No existe información del producto.");
            }

            //si es WIFI Alquiler Equipos le doy de baja en el naf
            if(($objProducto->getDescripcionProducto() == 'WIFI Alquiler Equipos' || $boolRequiereRegistro) && $boolValidaNaf)
            {
                //actualizamos registro en el naf del cpe
                $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                            'identificacionCliente' => '',
                                            'empresaCod'            => $intIdEmpresa,
                                            'modeloCpe'             => '',
                                            'serieCpe'              => $strSerieWifi,
                                            'cantidad'              => '1');

                $strMensajeError = $this->cambioElemento->procesaInstalacionElemento($arrayParametrosNaf);
                if(strlen(trim($strMensajeError)) > 0)
                {
                    $arrayRespuestaFinal[] = array("status" => "ERROR", "mensaje" => "ERROR WIFI NAF: " . $strMensajeError);
                    return $arrayRespuestaFinal;
                }

                /*Agrego el tipo de medio para que se guarde en la InfoServicioTecnico.*/
                $objTipoMedio = $this->emComercial->getRepository('schemaBundle:AdmiTipoMedio')
                    ->findOneBy(array(
                        "nombreTipoMedio"   => $strUltimaMilla,
                        "estado"            => 'Activo'
                    ));
                if($objTipoMedio)
                {
                    $objServicioTecnico->setUltimaMillaId($objTipoMedio->getId());
                }

                /* Busca el tipo de solicitud Planificacion. */
                $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                    ->findOneBy(array('descripcionSolicitud'=>'SOLICITUD PLANIFICACION'));

                /* Busca una solicitud en estado asignada. */
                $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->findOneBy(array(
                    'servicioId' => $intIdServicio,
                    'estado' => 'Asignada',
                    'servicioId' => $objTipoSolicitud->getId()
                ));
                
                /*Si existe una solicitud de planificación pendiente se finaliza y guarda historial.*/
                if (is_object($objDetalleSolicitud))
                {
                    $objDetalleSolicitud->setEstado('Finalizada');
                    $this->emComercial->persist($objDetalleSolicitud);
                    /*Ingreso la mac como detalle del elemento.*/
                    $this->emComercial->flush();
                    $objInfoDetalleSolHist = new InfoDetalleSolHist();
                    $objInfoDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud->getId());
                    $objInfoDetalleSolHist->setEstado('Finalizada');
                    $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                    $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objInfoDetalleSolHist);
                    $this->emComercial->flush();
                }

            }

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
                
            if (is_array($objParamsDet) && !empty($objParamsDet))
            {
                $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);

                $arrayParams['strNeedle'] = intval($intIdProducto);
                $arrayParams['strKey'] = 'PRODUCTO_ID';
                $arrayParams['arrayToSearch'] = $objCaracteristicasServiciosSimultaneos;

                $arrayCaracteristicasServicioSimultaneo = $this->servicioGeneral->searchByKeyInArray($arrayParams); 
            }

            //si producto se encuentra dentro de parametro y valida con naf se cambia el login con el que guarda elemento 
            if(isset($arrayCaracteristicasServicioSimultaneo) && !is_null($arrayCaracteristicasServicioSimultaneo) && $boolValidaNaf)
            { 
                $strLoginAux = $objServicio->getLoginAux();
                $strLoginAux = (!empty($strLoginAux) && isset($strLoginAux)) ? $strLoginAux : $strLogin;

                $boolTipoElementoEncontrado = false;
                $strSufijoElemento = '-cpe';

                $arrayModelosElemento = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                    ->findBy(array("nombreModeloElemento" => $strModeloWifi, "estado" => "Activo"));
                          
                foreach ($arrayModelosElemento as $objModelo) 
                {
                    $strTipoElemento = $objModelo->getTipoElementoId()->getNombreTipoElemento();
                    if(strpos($strTipoElemento, "CPE") !== false)
                    {
                        $strTipoLowerCase = strtolower($strTipoElemento);
                        $strSufijoElemento = '-'.str_replace(" ", "-", $strTipoLowerCase);
                        $boolTipoElementoEncontrado = true;
                    }
                }
                    
                if(!$boolTipoElementoEncontrado && isset($arrayModelosElemento) && !is_null($arrayModelosElemento))
                {
                    $objPrimerModelo = reset($arrayModelosElemento);
                    $strTipoElemento = $objPrimerModelo->getTipoElementoId()->getNombreTipoElemento();
                    $strTipoLowerCase = strtolower($strTipoElemento);
                    $strSufijoElemento = '-'.str_replace(" ", "-", $strTipoLowerCase);
                }                                    
            } 
            else 
            {
                $strLoginAux = $strLogin;
                $strSufijoElemento = '-ont';
            }

            $intInterfaceOnt = $this->servicioGeneral->ingresarElementoCliente($strLoginAux, 
                                                                            $strSerieWifi, 
                                                                            $strModeloWifi, 
                                                                            $strSufijoElemento, 
                                                                            $objInterfaceConector,
                                                                            $strUltimaMilla, 
                                                                            $objServicio, 
                                                                            $strUsrCreacion, 
                                                                            $strIpCreacion, 
                                                                            $intIdEmpresa);

            //consulto la capacidad ante de insertarla
            $objCapacidad = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1", $objProducto);

            if(!$objCapacidad)
            {
                if($intCapacidad1)
                {
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "CAPACIDAD1", 
                                                                                   $intCapacidad1, $strUsrCreacion);

                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "CAPACIDAD2", $intCapacidad1, 
                                                                                   $strUsrCreacion);
                }
            }

            /* Se valida si la opcion llega para registrar MAC y SERIE. */
            if ($boolRequiereRegistro)
            {
                if($strMacWifi != "")
                {
                    //servicio prod caract mac
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "MAC", $strMacWifi, $strUsrCreacion);
                }

                if($strSerieWifi != "")
                {
                    //servicio prod caract mac
                    $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio, 
                                                                                    $objProducto, 
                                                                                    "CPE SERIAL NUMBER", 
                                                                                    $strSerieWifi, 
                                                                                    $strUsrCreacion);
                }
            }

            $strObservacionAdic = '';
            if ($arrayCaractAdicionales)
            {       
                foreach ($arrayCaractAdicionales as $arrayCaractAdicional)
                {
                    if(isset($arrayCaractAdicional['FIELD_VALUE']) && !empty($arrayCaractAdicional['FIELD_VALUE']))
                    {
                        $this->servicioGeneral->ingresarServicioProductoCaracteristica( $objServicio,
                                                                                        $objProducto,
                                                                                        $arrayCaractAdicional['DESCRIPCION_CARACTERISTICA'],
                                                                                        $arrayCaractAdicional['FIELD_VALUE'],
                                                                                        $strUsrCreacion);
                    }

                    $strLabel = ucfirst(mb_strtolower($arrayCaractAdicional['LABEL'], "UTF-8"));
                    $strValue = $arrayCaractAdicional['FIELD_VALUE'];
                    if ($strValue !== "") 
                    {
                        $strObservacionAdic .= '<br> <b>'.$strLabel.':</b> '.$strValue;
                    }          
                }

            }

            if($strSsid != "")
            {
                //servicio prod caract strSsid
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "SSID", $strSsid, $strUsrCreacion);
            }

            if($strPassword != "")
            {
                //servicio prod caract strPassword
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "PASSWORD SSID", 
                                                                               $strPassword, $strUsrCreacion);
            }

            if($intNumeroPc != "")
            {
                //servicio prod caract numPc
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "NUMERO PC", $intNumeroPc, $strUsrCreacion);
            }

            if($strModoOperacion != "")
            {
                //servicio prod caract modo operacion
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "MODO OPERACION", $strModoOperacion, 
                                                                               $strUsrCreacion);
            }

            if($strMacWifi != "")
            {
                //servicio prod caract mac wifi
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "MAC WIFI", $strMacWifi, $strUsrCreacion);
            }

            if($intVlan != "")
            {
                //servicio prod caract mac wifi
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "VLAN", $intVlan, $strUsrCreacion);
            }

            //ingreso la mac como detalle del elemento
            $objInfoDetalleElemento = new InfoDetalleElemento();
            $objInfoDetalleElemento->setElementoId($intInterfaceOnt->getElementoId()->getId());
            $objInfoDetalleElemento->setDetalleNombre('MAC');
            $objInfoDetalleElemento->setDetalleValor($strMacWifi);
            $objInfoDetalleElemento->setDetalleDescripcion('MAC DEL EQUIPO WIFI');     
            $objInfoDetalleElemento->setEstado("Activo");
            $objInfoDetalleElemento->setUsrCreacion($strUsrCreacion);
            $objInfoDetalleElemento->setIpCreacion($strIpCreacion);
            $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
            $this->emInfraestructura->persist($objInfoDetalleElemento);
            $this->emInfraestructura->flush();

            if($strIpElemento != "")
            {
                //crear InfoIp
                $infoIp = new InfoIp();
                $infoIp->setIp($strIpElemento);
                $infoIp->setEstado("Activo");
                $infoIp->setTipoIp("FIJA");
                $infoIp->setVersionIp("IPV4");
                $infoIp->setServicioId($objServicio->getId());
                $infoIp->setElementoId($intInterfaceOnt->getElementoId()->getId());
                $infoIp->setUsrCreacion($strUsrCreacion);
                $infoIp->setFeCreacion(new \DateTime('now'));
                $infoIp->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($infoIp);
                $this->emInfraestructura->flush();
            }

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityServicioHist = new InfoServicioHistorial();
            $entityServicioHist->setServicioId($objServicio);
            $entityServicioHist->setIpCreacion($strIpCreacion);
            $entityServicioHist->setFeCreacion(new \DateTime('now'));
            $entityServicioHist->setUsrCreacion($strUsrCreacion);
            $entityServicioHist->setEstado($objServicio->getEstado());
            $entityServicioHist->setObservacion('Se regularizó información del elemento cliente: '
                                                .'<br> <b>Modelo:</b> '.$strModeloWifi
                                                .'<br> <b>Serie:</b> '.$strSerieWifi
                                                .'<br> <b>Mac:</b> '.$strMacWifi
                                                .$strObservacionAdic
                                                .'<br> <b>Observación:</b> '.$strObservacion);
            $this->emComercial->persist($entityServicioHist);
            $this->emComercial->flush();

            //guardar ont en servicio tecnico
            $objServicioTecnico->setElementoClienteId($intInterfaceOnt->getElementoId()->getId());
            $objServicioTecnico->setInterfaceElementoClienteId($intInterfaceOnt->getId());
            $this->emComercial->persist($objServicioTecnico);
            $this->emComercial->flush();

            //valida que el producto se encuentre dentro del parametro INSTALACIÓN SIMULTANEA, luego se actualiza a custodio cliente en naf
            if(isset($arrayCaracteristicasServicioSimultaneo) && !is_null($arrayCaracteristicasServicioSimultaneo) && $boolValidaNaf)
            {
                $arrayInfoActivos  = $this->emNaf->getRepository('schemaBundle:InfoDetalleMaterial')
                        ->obtenerEquiposAsignados(array('strEstadoEquipo'         => 'IN',                                            
                                                        'booleanNoValidarPersona' => true,
                                                        'strNumeroSerie'          => $strSerieWifi));

                if ($arrayInfoActivos['status'])
                {
                    $intIdControl = $arrayInfoActivos['result'][0]["idControl"];
                }

                $arrayEquipos        = array();
                $arrayEquipos[]      = array('strNumeroSerie' => $strSerieWifi,
                                            'intIdControl'    => $intIdControl,
                                            'intCantidadEnt'  => 1,
                                            'intCantidadRec'  => 1,
                                            'strTipoArticulo' => 'Equipos');
    
                $arrayCargaDescarga = array();
                $arrayCargaDescarga['boolRegistrarTraking']     =  false;
                $arrayCargaDescarga['intIdServicio']            =  $intIdServicio;
                $arrayCargaDescarga['strTipoRecibe']            = 'Cliente';
                $arrayCargaDescarga['intIdEmpleado']            =  $intIdUsrCreacion;
                $arrayCargaDescarga['intIdEmpresa']             =  $intIdEmpresa;
                $arrayCargaDescarga['strTipoActividad']         = 'Instalacion';
                $arrayCargaDescarga['strTipoTransaccion']       = 'Nuevo';
                $arrayCargaDescarga['strObservacion']           = 'Instalacion del servicio';
                $arrayCargaDescarga['arrayEquipos']             =  $arrayEquipos;
                $arrayCargaDescarga['strEstadoSolicitud']       = 'Asignado';
                $arrayCargaDescarga['strUsuario']               =  $strUsrCreacion;
                $arrayCargaDescarga['strIpUsuario']             =  $strIpCreacion;           
                            
                $this->serviceInfoElemento->cargaDescargaActivos($arrayCargaDescarga);
            }
            
            $this->emInfraestructura->getConnection()->commit();
            $this->emComercial->getConnection()->commit();
            $arrayResult[] = array("status" => "OK", "mensaje" => "OK");
        
        }
        catch(\Exception $ex)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();        
            }
            $arrayResult[] = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'ingresoElemento', 
                                            $ex->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);   
        }        
        
        return $arrayResult;
    }
    
    /**
     * cambioAnchoBanda
     * Función que se encarga de actualizar el ancho de banda en los concentradores de los servicios wifi
     *
     *
     * @params $arrayPeticiones [$objServicio, $strUsrCreacion, $strIpCreacion, $strOperacion, $intCapacidadNueva]
     * 
     * @return array
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-04-2017
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 11-05-2020 | Se realiza ajuste para evitar los numeros negativos en los calculos de BandWidth.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.2 01-06-2020 - Se agrega el id del servicio a la url 'configBW' del ws de networking para la validación del BW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.3 31-07-2020 - Se realizo el cambio de enviar el elemento y la interface del concentrador a ws de Networking
     *
     */    
    
    public function cambioAnchoBanda($arrayPeticiones)
    {
        $objServicio        = $arrayPeticiones['objServicio'];
        $objProducto        = $objServicio->getProductoId();
        $strUsrCreacion     = $arrayPeticiones['usrCreacion'];
        $strIpCreacion      = $arrayPeticiones['ipCreacion'];        
        $strOperacion       = $arrayPeticiones['strOperacion'];
        $intCapacidadNueva  = $arrayPeticiones['intCapacidadNueva'];
        
        try
        {
            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $objServicio->getId()));        

            $objInterfaceElemento  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->find($objServicioTecnico->getInterfaceElementoId());
            if(!$objInterfaceElemento)
            {
                $result = array("status" => "ERROR", "mensaje" => "No existe la interface del servicio wifi.");
                return $result;
            }        

            //consulto la capacidad del servicio de administracion
            $strLoginAux = '';
            $objSpcEnlaceDatos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "ENLACE_DATOS", $objProducto);
            if($objSpcEnlaceDatos)
            {
                $intIdServicioNavegacion  = $objSpcEnlaceDatos->getValor();
                $objServicioNavegacion = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicioNavegacion);
                if(!$objServicioNavegacion)
                {
                    $result = array("status" => "ERROR", "mensaje" => "No existe el servicio de navegación.");
                    return $result;
                }
                
                $objServicioTecnicoNav = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                           ->findOneByServicioId($objServicioNavegacion->getId()); 
                if(!is_object($objServicioTecnicoNav))
                {
                    $result = array("status" => "ERROR", "mensaje" => "No existe el servicio Tecnico de navegación.");
                    return $result;
                }                
                
                //verifico si en el punto tienes mas servicios
                $objServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findByPuntoId($objServicioNavegacion->getPuntoId());
                $intCapacidadTotalOtros = 0;
                foreach($objServicios as $objServicioKey)
                {                    
                    $objServicioTecnicoKey = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                               ->findOneByServicioId($objServicioKey->getId());
                    if(is_object($objServicioTecnicoKey))
                    {
                        if(is_object($objServicioKey->getProductoId()))
                        {
                            if($objServicioKey->getEstado() == 'Activo' 
                               &&  $objServicioKey->getDescripcionPresentaFactura() != 'Concentrador L3MPLS Administracion' 
                               && $objServicioKey->getDescripcionPresentaFactura() != 'Concentrador L3MPLS Navegacion' 
                               && $objServicioKey->getProductoId()->getNombreTecnico() != 'INTERNET WIFI'
                               && $objServicioTecnicoKey->getInterfaceElementoId() == $objServicioTecnicoNav->getInterfaceElementoId())
                            {
                                $objSpcCapacidadServ = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioKey, "CAPACIDAD1", 
                                                                                                                 $objServicioKey->getProductoId());
                                if(is_object($objSpcCapacidadServ))
                                {
                                    $intCapacidadTotalOtros = $intCapacidadTotalOtros + $objSpcCapacidadServ->getValor();                            
                                }
                            }
                        }
                    }
                }
                
                
                $strLoginAux              = $objServicioNavegacion->getLoginAux();

                if($objServicioNavegacion->getProductoId())
                {
                    $objSpcCapacidadNavega = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioNavegacion, "CAPACIDAD1", 
                                                                                                       $objServicioNavegacion->getProductoId());
                    $objSpcCapacidad2Navega = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioNavegacion, "CAPACIDAD2", 
                                                                                                        $objServicioNavegacion->getProductoId());
                }
                else
                {
                    $result = array("status" => "ERROR", "mensaje" => "No existe el producto del servicio de navegación.");
                    return $result;
                }
                $strNombreProducto        = $objServicioNavegacion->getProductoId()->getNombreTecnico();                
                $intCapacidadNavegacion = 0;
                if($objSpcCapacidadNavega)
                {
                      $intCapacidadNavegacion = $objSpcCapacidadNavega->getValor();
                }
                //sumo las capacidades del L3 con la del Internet Wifi
                if($strOperacion == 'SUMA')
                {
                    $intCapacidadWifi = $intCapacidadNavegacion + $intCapacidadNueva;
                }
                else
                {
                    /* Se realiza ajuste para que cuando la resta sea menor a 0, se establezca como 0. */
                    $intCapacidadWifi = ($intCapacidadNavegacion - $intCapacidadNueva) < 0 ? 0 : $intCapacidadNavegacion - $intCapacidadNueva;
                }
                //historial del servicio
                $objServicioHistorialNavega = new InfoServicioHistorial();
                $objServicioHistorialNavega->setServicioId($objServicioNavegacion);
                $objServicioHistorialNavega->setObservacion(
                                   '<b>Cambio de Velocidad Realizado<br> Cliente: </b>'.$objServicio->getLoginAux()." <b>BW:</b> ".$intCapacidadNueva.
                                   "<br> <b>Velocidad Up anterior  : </b>" . $objSpcCapacidadNavega->getValor().
                                   "<br> <b>Velocidad Down anterior: </b>" . $objSpcCapacidad2Navega->getValor().
                                   "<br> <b>Velocidad Up Nuevo  : </b>" . $intCapacidadWifi .
                                   "<br> <b>Velocidad Down Nuevo: </b>" . $intCapacidadWifi                                                                                                  
                                   );
                $objServicioHistorialNavega->setEstado($objServicioNavegacion->getEstado());
                $objServicioHistorialNavega->setUsrCreacion($strUsrCreacion);
                $objServicioHistorialNavega->setFeCreacion(new \DateTime('now'));
                $objServicioHistorialNavega->setIpCreacion($strIpCreacion);                    
                $this->emComercial->persist($objServicioHistorialNavega);
                $this->emComercial->flush();

                if($objSpcCapacidadNavega)
                {
                    //sumo la capacidad nueva mas la de todos los ap
                    $objSpcCapacidadNavega->setValor($intCapacidadWifi);
                    $this->emComercial->persist($objSpcCapacidadNavega);
                    $this->emComercial->flush();

                }


                if($objSpcCapacidad2Navega)
                {
                    //sumo la capacidad nueva mas la de todos los ap
                    $objSpcCapacidad2Navega->setValor($intCapacidadWifi);
                    $this->emComercial->persist($objSpcCapacidad2Navega);
                    $this->emComercial->flush();
                }

                //obtengo el servicio de administracion
                $objServicioAdmin = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                      ->findOneBy(array('puntoId'                   => $objServicioNavegacion->getPuntoId(),
                                                                        'descripcionPresentaFactura'=> 'Concentrador L3MPLS Administracion'));
                if($objServicioAdmin)
                {
                    //consulto la capacidad del servicio
                    $objSpcCapacidadAdmin = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioAdmin, "CAPACIDAD1", 
                                                                                                      $objServicioAdmin->getProductoId());

                    if($objSpcCapacidadAdmin)
                    {
                        $intCapacidadAdmin = $objSpcCapacidadAdmin->getValor();
                    }    
                }
            }
            else
            {
                throw new \Exception ("El servicio no tiene la caracteristica ENLACE DATOS.");
            }

            if($strOperacion == 'SUMA')
            {
                $intTotalCapacidad = $intCapacidadNavegacion + $intCapacidadNueva + $intCapacidadAdmin;
            }
            else
            {
                $intTotalCapacidad = ($intCapacidadNavegacion + $intCapacidadAdmin - $intCapacidadNueva) < 0 ? 0 :
                                      $intCapacidadNavegacion + $intCapacidadAdmin - $intCapacidadNueva;
            }


            $objDetalleAnilloSw = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                          ->findOneBy(array(  "elementoId"    => $objServicioTecnicoNav->getElementoId(),
                                                                              "detalleNombre" => "ANILLO",
                                                                              "estado"        => "Activo"));
            $strAnilloSw = '';
            if(is_object($objDetalleAnilloSw))
            {
                $strAnilloSw = $objDetalleAnilloSw->getDetalleValor();
            }
            else
            {
                $result = array("status" => "ERROR", "mensaje" => "No existe el anillo del sw del servicio.");
                return $result;
            }

            $objInterfaceElementoNav = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                             ->find($objServicioTecnicoNav->getInterfaceElementoId());
            if(!is_object($objInterfaceElementoNav))
            {
                $result = array("status" => "ERROR", "mensaje" => "No existe la interface del servicio de navegación wifi.");
                return $result;
            }

            if( is_object($objInterfaceElementoNav->getElementoId()) )
            {
                $strNombreElemento = $objInterfaceElementoNav->getElementoId()->getNombreElemento();
            }
            else
            {
                $result = array("status" => "ERROR", "mensaje" => "El servicio técnico no tiene asignado elemento.");
                return $result;   
            }

            if($intCapacidadTotalOtros > 0)
            {
                $intTotalCapacidad = $intTotalCapacidad + $intCapacidadTotalOtros;
            }

            //obtengo la capacidad del extremo
            $objCapServExtUno = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD1",
                                                                                              $objServicio->getProductoId());
            $objCapServExtDos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "CAPACIDAD2",
                                                                                              $objServicio->getProductoId());
            $intCapServExtUno = is_object($objCapServExtUno) ? $objCapServExtUno->getValor() : 0;
            $intCapServExtDos = is_object($objCapServExtDos) ? $objCapServExtDos->getValor() : 0;
            $intCapServTotalExtremo = $intCapServExtUno >= $intCapServExtDos ? $intCapServExtUno : $intCapServExtDos;

            //accion a ejecuta        
            $arrayService['url']         = 'configBW';
            $arrayService['accion']      = 'reconectar';
            $arrayService['id_servicio'] = $objServicioNavegacion->getId();
            $arrayService['nombreMetodo'] = 'InfoElementoWifiService.cambioAnchoBanda';
            $arrayService['loginAuxExtremo'] = $objServicio->getLoginAux();
            $arrayService['bwAuxExtremo']    = $intCapServTotalExtremo;
            $arrayService['sw']          = $strNombreElemento;
            $arrayService['user_name']   = $strUsrCreacion;
            $arrayService['user_ip']     = $strIpCreacion;
            $arrayService['bw_up']       = $intTotalCapacidad;
            $arrayService['bw_down']     = $intTotalCapacidad;
            $arrayService['servicio']    = $strNombreProducto;
            $arrayService['login_aux']   = $strLoginAux;
            $arrayService['pto']         = $objInterfaceElementoNav->getNombreInterfaceElemento();
            $arrayService['anillo']      = $strAnilloSw;
            
            //Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayService);

            if($arrayRespuestaBw['status'] == 'ERROR')
            {
                throw new \Exception('Error: '.$arrayRespuestaBw['mensaje'].' codigo: '.$arrayRespuestaBw['statusCode']);
            }

            //obtengo la capacidad del extremo
            $objCapacidadExtUno = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioNavegacion, "CAPACIDAD1",
                                                                                              $objServicioNavegacion->getProductoId());
            $objCapacidadExtDos = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioNavegacion, "CAPACIDAD2",
                                                                                              $objServicioNavegacion->getProductoId());
            $intCapacidadExtUno = is_object($objCapacidadExtUno) ? $objCapacidadExtUno->getValor() : 0;
            $intCapacidadExtDos = is_object($objCapacidadExtDos) ? $objCapacidadExtDos->getValor() : 0;
            $intCapacidadTotalExtremo = $intCapacidadExtUno >= $intCapacidadExtDos ? $intCapacidadExtUno : $intCapacidadExtDos;

            //validacion de concentrador
            $objSpcEnlaceDatosConcentrador = $this->servicioGeneral->getServicioProductoCaracteristica( $objServicioNavegacion,
                                                                                                        "ENLACE_DATOS", 
                                                                                                        $objServicioNavegacion->getProductoId());        
            //aumentamos en el concentrador
            if(is_object($objSpcEnlaceDatosConcentrador))
            {
                $objServicioConcentrador    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->findOneBy(array('id'     =>  intval($objSpcEnlaceDatosConcentrador->getValor()),
                                                                                  'estado' => 'Activo') );
                if(is_object($objServicioConcentrador))
                {
                    $objServicioTecConcentrador = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                                    ->findOneBy(array("servicioId" => $objServicioConcentrador->getId()));
                    if(!is_object($objServicioTecConcentrador))
                    {
                        $result = array("status" => "ERROR", "mensaje" => "No existe el servicio técnico del concentrador.");
                        return $result;
                    }
                    
                    if($objServicioTecConcentrador->getElementoId())
                    {
                        $objElementoConcentrador = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                           ->find($objServicioTecConcentrador->getElementoId());
                    }
                    else
                    {
                        $result = array("status" => "ERROR", "mensaje" => "El servicio tecnico del concentrador no tiene elemento. ".$objServicioTecConcentrador->getId());
                        return $result;                        
                    }
                    if(!is_object($objElementoConcentrador))
                    {
                        $result = array("status" => "ERROR", "mensaje" => "No existe el elemento del concentrador.");
                        return $result;
                    }                
                                        
                    if($objServicioTecConcentrador->getInterfaceElementoId())
                    {
                        $objInterfaceElementoConcentrador = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                                    ->find($objServicioTecConcentrador->getInterfaceElementoId());
                    }
                    else
                    {
                        $result = array("status" => "ERROR", "mensaje" => "El servicio tecnico del concentrador no tiene interface.");
                        return $result;                        
                    }
                    if(!is_object($objInterfaceElementoConcentrador))
                    {
                        $result = array("status" => "ERROR", "mensaje" => "No existe la interface del elemento del concentrador.");
                        return $result;
                    }
                    if(is_object($objServicioConcentrador->getProductoId()))
                    {                    
                        $objSpcConCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioConcentrador, 
                                                                                                         "CAPACIDAD1", 
                                                                                                         $objServicioConcentrador->getProductoId());
                        $objSpcConCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($objServicioConcentrador, 
                                                                                                         "CAPACIDAD2", 
                                                                                                         $objServicioConcentrador->getProductoId());
                    }
                    else
                    {
                        $result = array("status" => "ERROR", "mensaje" => "No existe el producto del servicio concentrador.");
                        return $result;
                    }

                    $objDetalleAnillo = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy(array(  "elementoId"    => $objElementoConcentrador->getId(),
                                                                                    "detalleNombre" => "ANILLO",
                                                                                    "estado"        => "Activo"));                
                    $strAnillo = '';
                    if(is_object($objDetalleAnillo))
                    {
                        $strAnillo = $objDetalleAnillo->getDetalleValor();
                    }
                    else
                    {
                        $result = array("status" => "ERROR", "mensaje" => "No existe el anillo del sw del concentrador.");
                        return $result;
                    }                

                    if(is_object($objSpcConCapacidad1) && is_object($objSpcConCapacidad2))
                    {
                        //Cambiando las capacidades del concentrador                            
                        $bwConcentradorAnteriorUp   = $objSpcConCapacidad1->getValor();
                        $bwConcentradorAnteriorDown = $objSpcConCapacidad2->getValor();
                    }

                    //Capcidades mas nueva capacidad a configurar
                    if($strOperacion == 'SUMA')
                    {
                        $bwConcentradorNuevoUp   = intval($bwConcentradorAnteriorUp)   + intval($intCapacidadNueva);
                        $bwConcentradorNuevoDown = intval($bwConcentradorAnteriorDown) + intval($intCapacidadNueva);
                    }
                    else
                    {
                        /* Se realiza un ajuste para evitar que se registren valores negativos. */
                        $bwConcentradorNuevoUp   = (intval($bwConcentradorAnteriorUp) - intval($intCapacidadNueva)) < 0 ? 0 :
                                                    intval($bwConcentradorAnteriorUp) - intval($intCapacidadNueva);

                        /* Se realiza un ajuste para evitar que se registren valores negativos. */
                        $bwConcentradorNuevoDown = (intval($bwConcentradorAnteriorDown) - intval($intCapacidadNueva)) < 0 ? 0 :
                                                    intval($bwConcentradorAnteriorDown) - intval($intCapacidadNueva);
                    }

                    //ejecutar script para bw de concentrador
                    $arrayServiceBw = array();
                    $arrayServiceBw['url']       = 'configBW';
                    $arrayServiceBw['accion']    = 'reconectar';
                    $arrayServiceBw['id_servicio'] = $objServicioConcentrador->getId();
                    $arrayServiceBw['nombreMetodo'] = 'InfoElementoWifiService.cambioAnchoBanda';
                    $arrayServiceBw['loginAuxExtremo'] = $objServicioNavegacion->getLoginAux();
                    $arrayServiceBw['bwAuxExtremo']    = $intCapacidadTotalExtremo;
                    $arrayServiceBw['sw']        = $objElementoConcentrador->getNombreElemento();
                    $arrayServiceBw['pto']       = $objInterfaceElementoConcentrador->getNombreInterfaceElemento();
                    $arrayServiceBw['anillo']    = $strAnillo;
                    $arrayServiceBw['bw_up']     = $bwConcentradorNuevoUp;
                    $arrayServiceBw['bw_down']   = $bwConcentradorNuevoDown;
                    $arrayServiceBw['servicio']  = $objServicioConcentrador->getProductoId()->getNombreTecnico();
                    $arrayServiceBw['login_aux'] = $objServicioConcentrador->getLoginAux();
                    $arrayServiceBw['user_name'] = $strUsrCreacion;
                    $arrayServiceBw['user_ip']   = $strIpCreacion;
                    
                    //Ejecucion del metodo via WS para realizar la configuracion del SW
                    $arrayRespuestaBw = $this->networkingScripts->callNetworkingWebService($arrayServiceBw);

                    if($arrayRespuestaBw['status'] != 'OK')
                    {
                        throw new \Exception('Error: '.$arrayRespuestaBw['mensaje'].' codigo: '.$arrayRespuestaBw['statusCode']);
                    }                   

                    //Se actualiza las nuevas capacidades al servicio                                                                                                       
                    $objSpcConCapacidad1->setEstado("Eliminado");
                    $objSpcConCapacidad1->setUsrUltMod($strUsrCreacion);
                    $objSpcConCapacidad1->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcConCapacidad1);
                    $this->emComercial->flush();

                    $objSpcConCapacidad2->setEstado("Eliminado");
                    $objSpcConCapacidad2->setUsrUltMod($strUsrCreacion);
                    $objSpcConCapacidad2->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcConCapacidad2);
                    $this->emComercial->flush();

                    //ingresar las nuevas caracteristicas del concentrador
                    $arrayParams['entityAdmiProducto'] = $objServicioConcentrador->getProductoId();
                    $arrayParams['entityInfoServicio'] = $objServicioConcentrador;
                    $arrayParams['strEstado']          = 'Activo';
                    $arrayParams['strUsrCreacion']     = $strUsrCreacion;

                    $arrayParams['strCaracteristica']  = 'CAPACIDAD1';                    
                    $arrayParams['strValor']           = $bwConcentradorNuevoUp;                                                            
                    $objServProdCaractCap1 = $this->servicioGeneral->insertarInfoServicioProdCaract($arrayParams);
                    $this->emComercial->persist($objServProdCaractCap1);
                    $this->emComercial->flush();

                    $arrayParams['strCaracteristica']  = 'CAPACIDAD2';                    
                    $arrayParams['strValor']           = $bwConcentradorNuevoDown;                                                            
                    $objServProdCaractCap2 = $this->servicioGeneral->insertarInfoServicioProdCaract($arrayParams);
                    $this->emComercial->persist($objServProdCaractCap2);
                    $this->emComercial->flush();

                    $strObservacionConcentrador =   "<b><b>Cambio de Velocidad Realizado en Concentrador:</b>".
                                                    "<br><b>Elemento: </b>".$objElementoConcentrador->getNombreElemento().                                        
                                                    "<br><b>Puerto  : </b>".$objInterfaceElementoConcentrador->getNombreInterfaceElemento(). 
                                                    "<br><b> Velocidad Up anterior  :</b>" . $bwConcentradorAnteriorUp.
                                                    "<br><b> Velocidad Down anterior:</b>" . $bwConcentradorAnteriorDown.
                                                    "<br><b> Velocidad Up Nuevo  :</b>" . $bwConcentradorNuevoUp.
                                                    "<br><b> Velocidad Down Nuevo:</b>" . $bwConcentradorNuevoDown;

                    //historial del servicio
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicioConcentrador);
                    $objServicioHistorial->setObservacion($strObservacionConcentrador);
                    $objServicioHistorial->setEstado($objServicioConcentrador->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);                    
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                }                        
            }   
        }
        catch(\Exception $ex)
        {
            $result = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            return $result;

        }        
        
        $result = array("status" => "OK", "mensaje" => "OK");
        return $result;
        
    }
    
    
    /**
     * activarClienteAction
     * funcion que activa el servicio al cliente
     *
     * @params $arrayPeticiones [$idEmpresa, $idServicio, $interfaceElementoId, $strInterfaceElementoSplitter, $idProducto, $ultimaMilla, $login,
     *                           $macWifi, $serieWifi, $modeloWifi, $ssid, $password, $numeroPc, $modoOperacion, $observacion, $usrCreacion,
     *                           $ipCreacion]
     * 
     * @return result
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 05-05-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 18-06-2016  ingresar la mac como detalle del elemento
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 31-08-2016  activacion de servicio wifi con web service de networking
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.3 22-01-2018  Se agregan validaciones para reutilizar equipos en activación de servicios con tipo de orden T
     * @since 1.2
     *
     */
    public function activarCliente($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa                  = $arrayPeticiones['idEmpresa'];
        $idServicio                 = $arrayPeticiones['idServicio'];
        $interfaceElementoId        = $arrayPeticiones['interfaceElementoId'];
        $strInterfaceElementoSplitter = $arrayPeticiones['interfaceElementoSplitterId'];
        $idProducto                 = $arrayPeticiones['idProducto'];
        $ultimaMilla                = $arrayPeticiones['ultimaMilla'];
        $login                      = $arrayPeticiones['login'];
        $macWifi                    = $arrayPeticiones['macWifi'];
        $serieWifi                  = strtoupper($arrayPeticiones['serieWifi']);
        $modeloWifi                 = $arrayPeticiones['modeloWifi'];
        $ssid                       = $arrayPeticiones['ssid'];
        $password                   = $arrayPeticiones['password'];
        $numeroPc                   = $arrayPeticiones['numeroPc'];
        $modoOperacion              = $arrayPeticiones['modoOperacion'];
        $observacion                = $arrayPeticiones['observacion'];
        $usrCreacion                = $arrayPeticiones['usrCreacion'];
        $ipCreacion                 = $arrayPeticiones['ipCreacion'];
        $ipElemento                 = $arrayPeticiones['ipElementoCliente'];
        $vlan                       = $arrayPeticiones['vlan'];
        $strEsWifiExistente         = $arrayPeticiones['strEsWifiExistente'];

        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();

        try
        {
            $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
            $servicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                 ->findOneBy(array("servicioId" => $servicio->getId()));
            $objInterfaceElementoSplitter = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                      ->findOneBy(array('elementoId'=> $servicioTecnico->getElementoConectorId(),
                                                                                        'nombreInterfaceElemento' => $strInterfaceElementoSplitter));
            $producto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($idProducto);

            //consulto la capacidad del servicio nuevo
            $objSpcCapacidadNueva = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto);
            $intCapacidadNueva = 0;
            if($objSpcCapacidadNueva)
            {
                $intCapacidadNueva = $objSpcCapacidadNueva->getValor();
            }
            
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array ();
            
            $arrayCambioBw['objServicio']       = $servicio;
            $arrayCambioBw['intCapacidadNueva'] = $intCapacidadNueva;
            $arrayCambioBw['strOperacion']      = 'SUMA';
            $arrayCambioBw['usrCreacion']       = $arrayPeticiones['usrCreacion'];
            $arrayCambioBw['ipCreacion']        = $arrayPeticiones['ipCreacion'];
            
            $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }            
            
            //ingresar elemento ont
            $ontNafArray = $this->servicioGeneral->buscarElementoEnNaf($serieWifi, $modeloWifi, "PI", "ActivarServicio");
            $ontNaf = $ontNafArray[0]['status'];

            if($ontNaf == "OK" || $strEsWifiExistente == "SI")
            {
                $interfaceOnt = $this->servicioGeneral->ingresarElementoCliente($login, 
                                                                                $serieWifi, 
                                                                                $modeloWifi, 
                                                                                "-ont", 
                                                                                $objInterfaceElementoSplitter, 
                                                                                $ultimaMilla, 
                                                                                $servicio, 
                                                                                $usrCreacion, 
                                                                                $ipCreacion, 
                                                                                $idEmpresa);

                if ($strEsWifiExistente == "NO")
                {
                    //actualizamos registro en el naf del cpe
                    $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                                'identificacionCliente' => '',
                                                'empresaCod'            => $idEmpresa,
                                                'modeloCpe'             => '',
                                                'serieCpe'              => $serieWifi,
                                                'cantidad'              => '1');

                    $mensajeError = $this->cambioElemento->procesaInstalacionElemento($arrayParametrosNaf);

                    if(strlen(trim($mensajeError)) > 0)
                    {
                        $respuestaFinal[] = array("status" => "NAF", "mensaje" => "ERROR WIFI NAF: " . $mensajeError);
                        return $respuestaFinal;
                    }
                }
            }
            else
            {
                $respuestaFinal[] = array('status' => $ontNafArray[0]['status'], 'mensaje' => $ontNafArray[0]['mensaje']);
                return $respuestaFinal;
            }

            if($ssid != "")
            {
                //servicio prod caract ssid
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "SSID", $ssid, $usrCreacion);
            }

            if($password != "")
            {
                //servicio prod caract password
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "PASSWORD SSID", $password, $usrCreacion);
            }

            if($numeroPc != "")
            {
                //servicio prod caract numPc
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "NUMERO PC", $numeroPc, $usrCreacion);
            }

            if($modoOperacion != "")
            {
                //servicio prod caract modo operacion
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "MODO OPERACION", $modoOperacion, $usrCreacion);
            }

            if($macWifi != "")
            {
                //servicio prod caract mac wifi
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "MAC WIFI", $macWifi, $usrCreacion);
            }

            if($vlan != "")
            {
                //servicio prod caract mac wifi
                $this->servicioGeneral->ingresarServicioProductoCaracteristica($servicio, $producto, "VLAN", $vlan, $usrCreacion);
            }

            //ingreso la mac como detalle del elemento
            $objInfoDetalleElemento = new InfoDetalleElemento();
            $objInfoDetalleElemento->setElementoId($interfaceOnt->getElementoId()->getId());
            $objInfoDetalleElemento->setDetalleNombre('MAC');
            $objInfoDetalleElemento->setDetalleValor($macWifi);
            $objInfoDetalleElemento->setDetalleDescripcion('MAC DEL EQUIPO WIFI');     
            $objInfoDetalleElemento->setEstado("Activo");
            $objInfoDetalleElemento->setUsrCreacion($usrCreacion);
            $objInfoDetalleElemento->setIpCreacion($ipCreacion);
            $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
            $this->emInfraestructura->persist($objInfoDetalleElemento);
            $this->emInfraestructura->flush();

            if($ipElemento != "")
            {
                //crear InfoIp
                $infoIp = new InfoIp();
                $infoIp->setIp($ipElemento);
                $infoIp->setEstado("Activo");
                $infoIp->setTipoIp("FIJA");
                $infoIp->setVersionIp("IPV4");
                $infoIp->setServicioId($servicio->getId());
                $infoIp->setElementoId($interfaceOnt->getElementoId()->getId());
                $infoIp->setUsrCreacion($usrCreacion);
                $infoIp->setFeCreacion(new \DateTime('now'));
                $infoIp->setIpCreacion($ipCreacion);
                $this->emInfraestructura->persist($infoIp);
                $this->emInfraestructura->flush();
            }

            //actualizo el servicio
            $servicio->setEstado('EnPruebas');
            $servicio->setObservacion($observacion);
            $this->emComercial->persist($servicioTecnico);
            $this->emComercial->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $entityServicioHist = new InfoServicioHistorial();
            $entityServicioHist->setServicioId($servicio);
            $entityServicioHist->setIpCreacion($ipCreacion);
            $entityServicioHist->setFeCreacion(new \DateTime('now'));
            $entityServicioHist->setUsrCreacion($usrCreacion);
            $entityServicioHist->setEstado('EnPruebas');
            $entityServicioHist->setObservacion('Se activo el Servicio Wifi.');
            $this->emComercial->persist($entityServicioHist);
            $this->emComercial->flush();

            //guardar ont en servicio tecnico
            $servicioTecnico->setElementoClienteId($interfaceOnt->getElementoId()->getId());
            $servicioTecnico->setInterfaceElementoClienteId($interfaceOnt->getId());
            $this->emComercial->persist($servicioTecnico);
            $this->emComercial->flush();

            $result[] = array("status" => "OK", "mensaje" => "OK");
            
            $this->emInfraestructura->getConnection()->commit();
            $this->emComercial->getConnection()->commit();          
        }
        catch(\Exception $ex)
        {
            $this->emComercial->getConnection()->rollback();
            $this->emInfraestructura->getConnection()->rollback();
            $result[] = array("status" => "ERROR", "mensaje" => $ex->getMessage());
            
            $this->serviceUtil->insertError('Telcos+', 
                                            'activarClienteWifi', 
                                            $ex->getMessage(), 
                                            $usrCreacion, 
                                            $ipCreacion);    
        }
        
        $this->emComercial->getConnection()->close();
        $this->emInfraestructura->getConnection()->close();
        return $result;
    }

    /**
     * cortarServicio
     * funcion que corta el servicio al cliente
     *
     * @params $arrayPeticiones [$idEmpresa, $prefijoEmpresa, $idServicio, $idProducto, $idAccion,  $usrCreacion,  $ipCreacion]
     * 
     * @return result
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 06-04-2017 Se aumenta la modificacion de los bw en los concentradores.
     * 
     */
    
    public function cortarServicio($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones['prefijoEmpresa'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $idProducto     = $arrayPeticiones['idProducto'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $motivo         = $arrayPeticiones['motivo'];
        $idAccion       = $arrayPeticiones['idAccion'];

        $motivoObj = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($motivo);
        $accionObj = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {
            
            if(is_object($servicio))
            {
                $objProducto = $servicio->getProductoId();
                if(!is_object($objProducto))
                {
                    throw new \Exception('No existe el producto en el servicio.');
                }
            }
            else
            {
                throw new \Exception ('No existe el servicio.');
            }

            //consulto la capacidad del servicio nuevo
            $objSpcCapacidad = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $objProducto);
            $intCapacidad = 0;
            if(is_object($objSpcCapacidad))
            {
                $intCapacidad = $objSpcCapacidad->getValor();
            }
            else
            {
                throw new \Exception('El servicio no tiene capacidad.');
            }            

            if($servicio->getEstado() == "Activo")
            {
                $servicio->setEstado("In-Corte");
                $this->emComercial->persist($servicio);
                $this->emComercial->flush();

                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($servicio);
                $servicioHistorial->setObservacion("Se corto el Servicio");
                $servicioHistorial->setEstado("In-Corte");
                $servicioHistorial->setMotivoId($motivoObj->getId());
                $servicioHistorial->setUsrCreacion($usrCreacion);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($ipCreacion);
                $servicioHistorial->setAccion($accionObj->getNombreAccion());
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();
            }
            
            $punto = $servicio->getPuntoId();
            $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $punto->getId()));
            //si el punto solo tiene un servicio lo corto
            if(count($serviciosPunto) == 1)
            {
                $punto->setEstado("In-Corte");
                $this->emComercial->persist($punto);
                $this->emComercial->flush();
            }
                        
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array ();
            
            $arrayCambioBw['objServicio']       = $servicio;
            $arrayCambioBw['intCapacidadNueva'] = $intCapacidad;
            $arrayCambioBw['strOperacion']      = 'RESTA';
            $arrayCambioBw['usrCreacion']       = $arrayPeticiones['usrCreacion'];
            $arrayCambioBw['ipCreacion']        = $arrayPeticiones['ipCreacion'];
            
            $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }   
            
            $mensaje = 'OK';

        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $mensaje = $e->getMessage();
            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $mensaje);
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal[] = array('status' => "OK", 'mensaje' => $mensaje);

        return $arrayFinal;
    }
    
    /**
     * reconectarServicio
     * funcion que reconecta el servicio al cliente
     *
     * @params $arrayPeticiones [$idEmpresa, $prefijoEmpresa, $idServicio, $idProducto, $idAccion,  $usrCreacion,  $ipCreacion]
     * 
     * @return result
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 06-04-2017 Se aumenta la modificacion de los bw en los concentradores.
     */
    
    public function reconectarServicio($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones['prefijoEmpresa'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $idProducto     = $arrayPeticiones['idProducto'];
        $idAccion       = $arrayPeticiones['idAccion'];

        $accionObj      = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        $servicio       = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {
            
            if(is_object($servicio))
            {
                $objProducto = $servicio->getProductoId();
                if(!is_object($objProducto))
                {
                    throw new \Exception('No existe el producto en el servicio.');
                }
            }
            else
            {
                throw new \Exception ('No existe el servicio.');
            }

            //consulto la capacidad del servicio nuevo
            $objSpcCapacidad = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $objProducto);
            $intCapacidad = 0;
            if(is_object($objSpcCapacidad))
            {
                $intCapacidad = $objSpcCapacidad->getValor();
            }
            else
            {
                throw new \Exception('El servicio no tiene capacidad.');
            }               

            $punto = $servicio->getPuntoId();
            $serviciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $punto->getId()));

            //si el punto solo tiene un servicio lo corto
            if(count($serviciosPunto) == 1)
            {
                $punto->setEstado("Activo");
                $this->emComercial->persist($punto);
                $this->emComercial->flush();
            }

            if($servicio->getEstado() == "In-Corte")
            {
                $servicio->setEstado("Activo");
                $this->emComercial->persist($servicio);
                $this->emComercial->flush();

                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($servicio);
                $servicioHistorial->setObservacion("Se reactivo el Servicio");
                $servicioHistorial->setEstado("Activo");
                $servicioHistorial->setUsrCreacion($usrCreacion);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($ipCreacion);
                $servicioHistorial->setAccion($accionObj->getNombreAccion());
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();
            }
            
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array ();
            
            $arrayCambioBw['objServicio']       = $servicio;
            $arrayCambioBw['intCapacidadNueva'] = $intCapacidad;
            $arrayCambioBw['strOperacion']      = 'SUMA';
            $arrayCambioBw['usrCreacion']       = $arrayPeticiones['usrCreacion'];
            $arrayCambioBw['ipCreacion']        = $arrayPeticiones['ipCreacion'];
            
            $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }               
            
            $status = "OK";
            $mensaje = "Se Reactivo el Cliente!";

        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $status = "ERROR";
            $mensaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $mensaje);
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        //*----------------------------------------------------------------------*/

        $arrayFinal[] = array('status' => $status, 'mensaje' => $mensaje);

        return $arrayFinal;
    }
    
    /**
     * cancelarServicio
     * funcion que cancela el servicio al cliente
     *
     * @params $arrayPeticiones [$idEmpresa, $idServicio, $prefijoEmpresa, $idProducto, $idProducto, $login,
     *                           $motivo, $idPersonaEmpresaRol, $idAccion, $usrCreacion, $ipCreacion]
     * 
     * @return result
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 25-10-2016 Se modifica para que se libere el puerto correctamente
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.2 06-04-2017 Se aumenta la modificacion de los bw en los concentradores.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 09-08-2017 -  En la tabla INFO_DETALLE_HISTORIAL se registra el id_persona_empresa_rol del responsable de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 15-09-2017 -  Se realizan ajustes porque ahora todos las tareas nacen en estado Asignada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 03-01-2018 - Se agrega finalización de solicitud padre y envío de notificación si se cancela el servicio 
     *                           desde una solicitud masiva
     *
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 22-01-2018 -  Se agrega programación para realizar cancelación del servicios por motivo de traslado
     * @since 1.4
     *
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 03-04-2018 - Se regularizan cambios realizados en caliente
     * @since 1.7
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.9 01-03-2018 Se registra tracking del elemento
     * 
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.0 22-05-2018 Se agregan los estados: "Cancel-SinEje","Anulado","Anulada","Eliminado","Eliminada","Eliminado-Migra","Rechazada","Trasladado" 
     * en las condiciones de los estados de los servicios para la correcta Cancelacion a nivel Comercial de estados de Clientes y Contratos, debido a que reportan
     * "ERROR EN ESTATUS - REPORTE DE CARTERA"
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.1 07-01-2020 Se elimina fragmento de código que generaba tarea innecesaria de retiro de equipos
     *                         para la persona que ejecutara la acción.
     * 
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 2.2 28-07-2022 Se llama a una función para actualizar el estado de UM en ARCGIS
     */
    
    public function cancelarServicio($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones['prefijoEmpresa'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $idProducto     = $arrayPeticiones['idProducto'];
        $login          = $arrayPeticiones['login'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $motivo         = $arrayPeticiones['motivo'];
        $idAccion            = $arrayPeticiones['idAccion'];
        $intIdDepartamento   = $arrayPeticiones['intIdDepartamento'];
        $arrayParametrosHist = array();
        $arrayParametrosAuditoria = array();
        $strObservacionTraslado = "";

        $arrayParametrosHist["strCodEmpresa"]           = $idEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $usrCreacion;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $ipCreacion;        
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;  

        $servicio           = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico    = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                            ->findOneBy(array("servicioId" => $servicio->getId()));
        $interfaceElementoId= $servicioTecnico->getInterfaceElementoId();
        $interfaceElemento  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')->find($interfaceElementoId);
        $elementoId         = $interfaceElemento->getElementoId();
        $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
        $motivoObj          = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($motivo);
        $accionObj          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        
        //Se verifica si existe alguna solicitud de cancelación
        $arrayResultadoSolicCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->getArrayInfoCambioPlanPorSolicitud(array( "idServicio"        => $idServicio,
                                                                                                        "tipoProceso"       => "",
                                                                                                        "strTipoSolicitud"  => "CANCELACION"));
        if(isset($arrayResultadoSolicCancelacion) && !empty($arrayResultadoSolicCancelacion))
        {
            $intIdSolicCancelacion      = $arrayResultadoSolicCancelacion['idSolicitud'];
            $intIdSolicCancelacionPadre = $arrayResultadoSolicCancelacion['idSolicitudPadre'];

            $objDetalleSolicCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->find($intIdSolicCancelacion);
        }
        //*----------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {
            
            if(is_object($servicio))
            {
                $objProducto = $servicio->getProductoId();
                if(!is_object($objProducto))
                {
                    throw new \Exception('No existe el producto en el servicio.');
                }
            }
            else
            {
                throw new \Exception ('No existe el servicio.');
            }
            
            if ($arrayPeticiones['strOrigen'] == "T")
            {
                $strObservacionTraslado      = "Este servicio cancelado registra la siguiente información técnica : <br>";
                $arrayServicioProductoCaract = $this->emComercial
                                                    ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                    ->findBy(array("servicioId" => $servicio->getId(), 
                                                                   "estado"     => "Activo"));
                if($arrayServicioProductoCaract)
                {
                    foreach($arrayServicioProductoCaract as $objServicioProductoCarac)
                    {
                        $objAdmiProdCaract = $this->emComercial
                                                  ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                  ->find($objServicioProductoCarac->getProductoCaracterisiticaId());
                        if (is_object($objAdmiProdCaract))
                        {
                            $objCaracteristica = $objAdmiProdCaract->getCaracteristicaId();
                            if (is_object($objCaracteristica))
                            {
                                $strObservacionTraslado .= "<b>".$objCaracteristica->getDescripcionCaracteristica().":</b> ".
                                                           $objServicioProductoCarac->getValor() ."<br>";
                            }
                        }
                    }
                }
                $arrayIPs = $this->emInfraestructura
                                 ->getRepository('schemaBundle:InfoIp')
                                 ->findBy(array("servicioId" => $servicio->getId(),
                                                "estado"     => "Activo"));
                if($arrayIPs)
                {
                    foreach($arrayIPs as $objIp)
                    {
                        $strSubRedIp  = "";
                        $strGetSubred = $objIp->getSubredId();
                        if (!empty($strGetSubred))
                        {
                          $objSubRed = $this->emInfraestructura
                                            ->getRepository('schemaBundle:InfoSubRed')
                                            ->find($objIp->getSubredId());  
                          if (is_object($objSubRed))
                          {
                              $strSubRedIp = $objSubRed->getSubred();
                          }
                        }
                        $strObservacionTraslado .= "<b>IP: </b> <br>"; 
                        $strObservacionTraslado .= "    <b>Ip:</b>      " . $objIp->getIp()     . "<br>";
                        $strObservacionTraslado .= "    <b>Mascara:</b> " . $objIp->getMascara(). "<br>";
                        $strObservacionTraslado .= "    <b>Subred:</b>  " . $strSubRedIp        . "<br>";
                        $strObservacionTraslado .= "    <b>Gateway:</b> " . $objIp->getGateway(). "<br>";
                        $strObservacionTraslado .= "    <b>Tipo:</b>    " . $objIp->getTipo()   . "<br>";
                        $strObservacionTraslado .= "    <b>Estado:</b>  " . $objIp->getEstado() . "<br>";
                    }
                }
                //obtener cpe del servicio 
                $objElementoCpe = $this->emInfraestructura
                                       ->getRepository('schemaBundle:InfoElemento')
                                       ->find($servicioTecnico->getElementoClienteId());
                
                
                //validar si otro servicio usa el mismo cpe
                if(is_object($objElementoCpe))
                {
                    $strMacServicio = $this->servicioGeneral->getMacPorServicio($arrayPeticiones['idServicio']); 
                    
                    $strObservacionTraslado .= "<b>CPE: </b> <br>"; 
                    $strObservacionTraslado .= "    <b>Nombre:</b>   " . $objElementoCpe->getNombreElemento()     . "<br>";
                    $strObservacionTraslado .= "    <b>Modelo:</b>   " . $objElementoCpe->getModeloElementoId()
                                                                                        ->getNombreModeloElemento()."<br>";
                    $strObservacionTraslado .= "    <b>Marca:</b>    " . $objElementoCpe->getModeloElementoId()
                                                                                        ->getMarcaElementoId()
                                                                                        ->getNombreMarcaElemento(). "<br>";
                    $strObservacionTraslado .= "    <b>Serie:</b>    " . $objElementoCpe->getSerieFisica()     . "<br>";
                    $strObservacionTraslado .= "    <b>Mac:</b>      " . $strMacServicio     . "<br>";
                    $arrayDetalleElementoCpe = $this->emInfraestructura
                                                    ->getRepository('schemaBundle:InfoDetalleElemento')
                                                    ->findBy(array("elementoId"    => $objElementoCpe->getId(),
                                                                   "estado"        => "Activo"));
                    
                    foreach($arrayDetalleElementoCpe as $objDetalleElementoCpe)
                    {
                        $strObservacionTraslado .= "    <b>".$objDetalleElementoCpe->getDetalleNombre().":</b> " . 
                                                   $objDetalleElementoCpe->getDetalleValor(). "<br>";
                    }
                }
                
                $arrayRutas = $this->emInfraestructura
                                   ->getRepository("schemaBundle:InfoRutaElemento")
                                   ->findBy(array("servicioId"    => $servicio->getId(),
                                                  "estado"        => "Activo"));
                if($arrayRutas && count($arrayRutas)>0)
                {
                    $strObservacionTraslado .= "<b>Rutas: </b> <br>"; 
                }
                foreach($arrayRutas as $objRuta)
                {
                    $strObservacionTraslado .= "    <b>Ruta </b> <br>";
                    $strObservacionTraslado .= "    <b>Nombre:</b>          " . $objRuta->getNombre(). "<br>";
                    $strObservacionTraslado .= "    <b>Red lan:</b>         " . $objRuta->getRedLan(). "<br>";
                    $strObservacionTraslado .= "    <b>Mascara red lan:</b> " . $objRuta->getMascaraRedLan(). "<br>";
                    $objRuta->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objRuta);
                    $this->emInfraestructura->flush();
                }
                // Creacion del historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($servicio);
                $objServicioHistorial->setObservacion($strObservacionTraslado);
                $objServicioHistorial->setEstado($servicio->getEstado());
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
                $objServicioHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                
            }

            //consulto la capacidad del servicio nuevo
            $objSpcCapacidad = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $objProducto);
            $intCapacidad = 0;
            if(is_object($objSpcCapacidad))
            {
                $intCapacidad = $objSpcCapacidad->getValor();
            }
            else
            {
                throw new \Exception('El servicio no tiene capacidad.');
            }                

            
            //cambio el anchos de banda de los concentradores
            $arrayCambioBw = array ();
            
            $arrayCambioBw['objServicio']       = $servicio;
            $arrayCambioBw['intCapacidadNueva'] = $intCapacidad;
            $arrayCambioBw['strOperacion']      = 'RESTA';
            $arrayCambioBw['usrCreacion']       = $arrayPeticiones['usrCreacion'];
            $arrayCambioBw['ipCreacion']        = $arrayPeticiones['ipCreacion'];
            
            $arrayCambio = $this->cambioAnchoBanda($arrayCambioBw);
            
            if($arrayCambio['status'] == 'ERROR')
            {
                throw new \Exception($arrayCambio['mensaje']);
            }           
            
            
            $status     = 'OK';
            $mensaje    = 'OK';
            
            $strElementoReutilizado = "NO";
            
            if ($arrayPeticiones['strOrigen'] == "T")
            {
                $objCaractTraslado = $this->emComercial
                                          ->getRepository('schemaBundle:AdmiCaracteristica')
                                          ->findOneBy(array( "descripcionCaracteristica" => "TRASLADO", 
                                                             "estado"                    => "Activo"));
                $objProdCaractTraslado = $this->emComercial
                                              ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                              ->findOneBy(array( "productoId"       => $servicio->getProductoId()
                                                                                                ->getId(), 
                                                                 "caracteristicaId" => $objCaractTraslado->getId()));

                $objServProdCaractTraslado = $this->emComercial
                                                  ->getRepository('schemaBundle:InfoServicioProdCaract')
                                                  ->findOneBy(array( "estado" => "Activo", 
                                                                     "valor"  => $servicio->getId(),
                                                                     "productoCaracterisiticaId" => $objProdCaractTraslado->getId()));

                if (is_object($objServProdCaractTraslado))
                {
                    $intServicioNuevoId = $objServProdCaractTraslado->getServicioId();
                    $objServicioNuevo   = $this->emComercial
                                               ->getRepository('schemaBundle:InfoServicio')
                                               ->find($intServicioNuevoId);
  
                    $objServicioTecnico   = $this->emComercial
                                                 ->getRepository('schemaBundle:InfoServicioTecnico')
                                                 ->findOneByServicioId($objServicioNuevo->getId());
  
                    if (is_object($objServicioTecnico))
                    {
                        $objElementoClienteNuevo = $this->emInfraestructura
                                                        ->getRepository('schemaBundle:InfoElemento')
                                                        ->find($objServicioTecnico->getElementoClienteId());
                        
                        if ($objElementoCpe->getSerieFisica() == $objElementoClienteNuevo->getSerieFisica())
                        {
                            $strElementoReutilizado = "SI";
                        }
                    }
                }
            }    
            if ($strElementoReutilizado == "NO")
            {
                //crear solicitud para retiro de equipo (ont y wifi)
                $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado" => "Activo"));
                $detalleSolicitud = new InfoDetalleSolicitud();
                $detalleSolicitud->setServicioId($servicio);
                $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
                $detalleSolicitud->setEstado("AsignadoTarea");
                $detalleSolicitud->setUsrCreacion($usrCreacion);
                $detalleSolicitud->setFeCreacion(new \DateTime('now'));
                $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $this->emComercial->persist($detalleSolicitud);

                //crear las caract para la solicitud de retiro de equipo
                $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneByDescripcionCaracteristica('ELEMENTO CLIENTE');

                //valor del ont
                $entityCaract = new InfoDetalleSolCaract();
                $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                $entityCaract->setValor($servicioTecnico->getElementoClienteId());
                $entityCaract->setEstado("AsignadoTarea");
                $entityCaract->setUsrCreacion($usrCreacion);
                $entityCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($entityCaract);
                $this->emComercial->flush();

            

                //crear historial para la solicitud
                $historialSolicitud = new InfoDetalleSolHist();
                $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
                $historialSolicitud->setEstado("AsignadoTarea");
                $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $historialSolicitud->setUsrCreacion($usrCreacion);
                $historialSolicitud->setFeCreacion(new \DateTime('now'));
                $historialSolicitud->setIpCreacion($ipCreacion);
                $this->emComercial->persist($historialSolicitud);
            }
            //------------------------------------------------------------------------------------------------
            
            $servicio->setEstado("Cancel");
            $this->emComercial->persist($servicio);
            $this->emComercial->flush();

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion("Se cancelo el Servicio");
            $servicioHistorial->setMotivoId($motivoObj->getId());
            $servicioHistorial->setEstado("Cancel");
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $servicioHistorial->setAccion($accionObj->getNombreAccion());
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();

            $arrayParametros = array();
            $arrayParametros['intIdServicio']   = $idServicio;
            $arrayParametros['strUsrCreacion']  = $usrCreacion;
            $arrayParametros["strIpCreacion"]   = $ipCreacion;
           
            //liberar puerto de elemento wifi
            $arrayResultado = $this->liberarPuertoWifi($arrayParametros);

            if($arrayResultado['strStatus'] == 'ERROR')
            {
                throw new \Exception($arrayResultado['strMensaje'] );
            }
            
            //eliminar las caracteristicas del servicio
            $servProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findBy(array("servicioId" => $servicio->getId(), "estado" => "Activo"));
            for($x = 0; $x < count($servProdCaract); $x++)
            {
                $spc = $servProdCaract[$x];
                $spc->setEstado("Eliminado");
                $this->emComercial->persist($spc);
                $this->emComercial->flush();
            }

            //revisar si es el ultimo servicio en el punto
            $puntoObj = $servicio->getPuntoId();
            $servicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $puntoObj->getId()));
            $numServicios = count($servicios);
            $cont = 0;
            for($i = 0; $i < count($servicios); $i++)
            {
                $servicioEstado = $servicios[$i]->getEstado();
                if($servicioEstado == "Cancel"           ||
                   $servicioEstado == "Cancel-SinEje"    ||
                   $servicioEstado == "Anulado"          ||
                   $servicioEstado == "Anulada"          ||
                   $servicioEstado == "Eliminado"        ||     
                   $servicioEstado == "Eliminada"        ||     
                   $servicioEstado == "Eliminado-Migra"  ||     
                   $servicioEstado == "Rechazada"        ||     
                   $servicioEstado == "Trasladado")
                {
                    $cont++;
                }
            }
            if($cont == ($numServicios))
            {
                $puntoObj->setEstado("Cancelado");
                $this->emComercial->persist($puntoObj);
                $this->emComercial->flush();
            }

            //revisar los puntos si estan todos Cancelados
            $personaEmpresaRol = $puntoObj->getPersonaEmpresaRolId();
            $puntos = $this->emComercial->getRepository('schemaBundle:InfoPunto')->findBy(array("personaEmpresaRolId" => $personaEmpresaRol->getId()));
            $numPuntos = count($puntos);
            $contPuntos = 0;
            for($i = 0; $i < count($puntos); $i++)
            {
                $punto1 = $puntos[$i];

                if($punto1->getEstado() == "Cancelado")
                {
                    $contPuntos++;
                }
            }
            if(($numPuntos) == $contPuntos)
            {
                //se cancela el contrato
                $contrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')->findOneBy(array("personaEmpresaRolId" => $personaEmpresaRol->getId()));
                $contrato->setEstado("Cancelado");
                $this->emComercial->persist($contrato);
                $this->emComercial->flush();

                //se cancela el personaEmpresaRol
                $personaEmpresaRol->setEstado("Cancelado");
                $this->emComercial->persist($personaEmpresaRol);
                $this->emComercial->flush();

                //se ingresa un registro en el historial empresa persona rol
                $personaHistorial = new InfoPersonaEmpresaRolHisto();
                $personaHistorial->setPersonaEmpresaRolId($personaEmpresaRol);
                $personaHistorial->setEstado("Cancelado");
                $personaHistorial->setUsrCreacion($usrCreacion);
                $personaHistorial->setFeCreacion(new \DateTime('now'));
                $personaHistorial->setIpCreacion($ipCreacion);
                $this->emComercial->persist($personaHistorial);
                $this->emComercial->flush();

                //se cancela el cliente
                $intPersonaId = $personaEmpresaRol->getPersonaId();
                $intPersonaId->setEstado("Cancelado");
                $this->emComercial->persist($intPersonaId);
                $this->emComercial->flush();
            }
            
            //eliminar ont
            $elementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                ->find($servicioTecnico->getElementoClienteId());
            $elementoCliente->setEstado("Eliminado");
            $this->emInfraestructura->persist($elementoCliente);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO
            $arrayParametrosAuditoria["strNumeroSerie"]  = $elementoCliente->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
            $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
            $arrayParametrosAuditoria["strCodEmpresa"]   = $idEmpresa;
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;

            //Se consulta el login del cliente
            if(is_object($servicioTecnico->getServicioId()))
            {
                $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                        ->find($servicioTecnico->getServicioId()->getPuntoId()->getId());
                if(is_object($objInfoPunto))
                {
                    $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                }
            }

            $arrayParametrosAuditoria["strUsrCreacion"] = $usrCreacion;

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);
            ////

            //historial ont
            $historial = $this->emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                ->findBy(array("elementoId" => $elementoCliente->getId(), "estadoElemento" => "Eliminado"));

            if(count($historial) == 0)
            {
                //historial del elemento
                $historialElemento = new InfoHistorialElemento();
                $historialElemento->setElementoId($elementoCliente);
                $historialElemento->setObservacion("Se elimino el ont por cancelacion de Servicio");
                $historialElemento->setEstadoElemento("Eliminado");
                $historialElemento->setUsrCreacion($usrCreacion);
                $historialElemento->setFeCreacion(new \DateTime('now'));
                $historialElemento->setIpCreacion($ipCreacion);
                $this->emInfraestructura->persist($historialElemento);
                $this->emInfraestructura->flush();
            }

            //eliminar puertos ont
            $interfacesOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                     ->findBy(array("elementoId" => $elementoCliente->getId()));
            for($i = 0; $i < count($interfacesOnt); $i++)
            {
                $interfacesCliente = $interfacesOnt[$i];
                $interfacesCliente->setEstado("Eliminado");
                $this->emInfraestructura->persist($interfacesCliente);
                $this->emInfraestructura->flush();
            }
            
            if(is_object($objDetalleSolicCancelacion))
            {
                //Finalizar la solicitud de cancelación               
                $objDetalleSolicCancelacion->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolicCancelacion);
                $this->emComercial->flush();

                //Finalizar detalle cabeceras
                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo(array(  'idSolicitudPadre'     => $intIdSolicCancelacionPadre,
                                                                                        'usrCreacion'          => $usrCreacion,
                                                                                        'ipCreacion'           => $ipCreacion ));
                //Se crea Historial de Servicio
                $objDetalleSolsHist = new InfoDetalleSolHist();
                $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicCancelacion);
                $objDetalleSolsHist->setEstado($objDetalleSolicCancelacion->getEstado());
                $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolsHist->setUsrCreacion($usrCreacion);
                $objDetalleSolsHist->setIpCreacion($ipCreacion);
                $objDetalleSolsHist->setObservacion("Se Realizo Cancelacion exitosamente");
                $this->emComercial->persist($objDetalleSolsHist);
                $this->emComercial->flush();

                //Enviar Notificacion
                $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva(array(  'idSolicitudPadre' => $intIdSolicCancelacionPadre,
                                                                                            'usrCreacion'      => $usrCreacion,
                                                                                            'idServicio'       => $idServicio ));
            }

            //------------------------------------------INICIO: ACTUALIZACIÓN DEL ESTADO EN ARCGIS
            $strUsrCancelacion    = $arrayPeticiones['usrCreacion'];
            $strIpCreacion        = $arrayPeticiones['ipCreacion'];
            $objProducto          = $servicio->getProductoId();
            if(is_object($objProducto))
            {
                $objServicioTecnico   = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($servicio->getId());
                $arrayParametrosConsulta                       = array();
                $arrayParametrosConsulta['strUsrCreacion']     = $strUsrCancelacion;
                $arrayParametrosConsulta['strIpCreacion']      = $strIpCreacion;
                $arrayParametrosConsulta['objServicioTecnico'] = $objServicioTecnico;
                $arrayParametrosConsulta['objProducto']        = $objProducto;
                // Pregunta si coincide con un producto parametrizado
                $arrayRespuestaProductoCancelacion        = $this->cancelarServicio->validarCondicionesProductos($arrayParametrosConsulta);
                if(($arrayResultado['strStatus']) && ($arrayRespuestaProductoCancelacion['status']=='OK'))
                {
                    // Solo si coincide ingresa a preguntar por los estados  de condiciòn
                    $arrayRespuestaEstados                = $this->cancelarServicio->validarCondicionesEstados($arrayParametrosConsulta);

                    $objUltimaMilla    = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                ->find($servicioTecnico->getUltimaMillaId());
                    $strUltimaMilla    = $objUltimaMilla->getNombreTipoMedio();
                    if((($strUltimaMilla == 'Fibra Optica')||($strUltimaMilla == 'FTTx')||($strUltimaMilla == 'FO'))
                        &&($arrayRespuestaEstados['status']=='OK'))
                    {
                        //solo para servicios que tienen fibra o fttx como última milla
                        $strLoginPunto     = $servicio->getPuntoId()->getLogin();
                        $objEmpresaGrupo   = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($idEmpresa);
                        $strPrefijoEmpresa = is_object($objEmpresaGrupo) ? $objEmpresaGrupo->getPrefijo() : "";

                        // PuertoSwitch y  NombreSwitch
                        $strNombreSwitch  = $elemento->getNombreElemento();
                        $strPuertoSwitch  = $interfaceElemento->getNombreInterfaceElemento();

                        $arrayParamServicio   = array(
                                            "strUsrCancelacion" => $strUsrCancelacion,
                                            "strNombreSwitch"   => $strNombreSwitch,
                                            "strPuertoSwitch"   => $strPuertoSwitch,
                                            "strLoginPunto"     => $strLoginPunto,
                                            "strPrefijo"        => $strPrefijoEmpresa,
                                            "strIpCreacion"     => $strIpCreacion,
                                            "objServicioPunto"  => $servicio
                                            );
                        //Se llama al procedimiento en la base 
                        $this->cancelarServicio->inactivarUmARCGIS($arrayParamServicio);
                    }
                }
            }
            //------------------------------------------FIN: ACTUALIZACIÓN DEL ESTADO EN ARCGIS
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }

            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }
            
            if(is_object($objDetalleSolicCancelacion))
            {
                $objDetalleSolicCancelacion->setEstado("Fallo");
                $this->emComercial->persist($objDetalleSolicCancelacion);
                $this->emComercial->flush();   

                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);

                //Se crea Historial de Servicio
                $objDetalleSolicHist = new InfoDetalleSolHist();
                $objDetalleSolicHist->setDetalleSolicitudId($objDetalleSolicCancelacion);
                $objDetalleSolicHist->setEstado($objDetalleSolicCancelacion->getEstado());
                $objDetalleSolicHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicHist->setUsrCreacion($usrCreacion);
                $objDetalleSolicHist->setIpCreacion($ipCreacion);
                $objDetalleSolicHist->setObservacion($e->getMessage());
                $this->emComercial->persist($objDetalleSolicHist);
                $this->emComercial->flush();
            }
            
            $status = "ERROR";
            $mensaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            $arrayFinal[] = array('status' => $status, 'mensaje' => $mensaje);
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        if($this->emSoporte->getConnection()->isTransactionActive())
        {
            $this->emSoporte->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        //*----------------------------------------------------------------------*/
        $arrayFinal[] = array('status' => $status, 'mensaje' => $mensaje);

        return $arrayFinal;
    }


    
    /**
     * Funcion que permite cancelar los servicios Wifi Alquiler Equipos desde el grid tecnico.
     *
     * @param [array] $arrayPeticiones
     * @return void
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0.0 10-12-2019 - Versión Inicial. 
     * 
     * 
     */
    public function cancelarWifiAlquilerEquipos($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $intIdEmpresa               = $arrayPeticiones['idEmpresa'];
        $intIdServicio              = $arrayPeticiones['idServicio'];
        $strUsrCreacion             = $arrayPeticiones['usrCreacion'];
        $strIpCreacion              = $arrayPeticiones['ipCreacion'];
        $strMotivo                  = $arrayPeticiones['motivo'];
        $intIdAccion                = $arrayPeticiones['idAccion'];
        $intIdDepartamento          = $arrayPeticiones['intIdDepartamento'];
        $arrayParametrosHist        = array();
        $arrayParametrosAuditoria   = array();

        $arrayParametrosHist["strCodEmpresa"]           = $intIdEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;

        $objServicio           = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
        $objServicioTecnico    = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
            ->findOneBy(array("servicioId" => $objServicio->getId()));

        $objDetalleElemento  = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                           ->findOneBy(array(
                                                               "elementoId" => $objServicioTecnico->getElementoClienteId(),
                                                               "estado" => "Activo"
                                                           ));
        $objMotivo          = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($strMotivo);
        $objAccion          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);

        //Se verifica si existe alguna solicitud de cancelación
        $arrayResultadoSolicCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                            ->getArrayInfoCambioPlanPorSolicitud(array(
                                                                "idServicio"        => $intIdServicio,
                                                                "tipoProceso"       => "",
                                                                "strTipoSolicitud"  => "CANCELACION"
                                                            ));
        if(isset($arrayResultadoSolicCancelacion) && !empty($arrayResultadoSolicCancelacion))
        {
            $intIdSolicCancelacion      = $arrayResultadoSolicCancelacion['idSolicitud'];
            $intIdSolicCancelacionPadre = $arrayResultadoSolicCancelacion['idSolicitudPadre'];

            $objDetalleSolicCancelacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                ->find($intIdSolicCancelacion);
        }
        //*----------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try
        {

            if(is_object($objServicio))
            {
                $objProducto = $objServicio->getProductoId();
                if(!is_object($objProducto))
                {
                    throw new \Exception('No existe el producto en el servicio.');
                }
            }
            else
            {
                throw new \Exception ('No existe el servicio.');
            }

            $strStatus     = 'OK';
            $strMensaje    = 'OK';

            $strElementoReutilizado = "NO";

            if ($strElementoReutilizado == "NO")
            {
                //crear solicitud para retiro de equipo (Wifi)
                $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                    ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO", "estado" => "Activo"));
                $objDetalleSolicitud = new InfoDetalleSolicitud();
                $objDetalleSolicitud->setServicioId($objServicio);
                $objDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objDetalleSolicitud->setEstado("AsignadoTarea");
                $objDetalleSolicitud->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicitud->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $this->emComercial->persist($objDetalleSolicitud);

                //crear las caract para la solicitud de retiro de equipo
                $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneByDescripcionCaracteristica('ELEMENTO CLIENTE');

                //valor del ont
                $entityCaract = new InfoDetalleSolCaract();
                $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                $entityCaract->setDetalleSolicitudId($objDetalleSolicitud);
                $entityCaract->setValor($objServicioTecnico->getElementoClienteId());
                $entityCaract->setEstado("AsignadoTarea");
                $entityCaract->setUsrCreacion($strUsrCreacion);
                $entityCaract->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($entityCaract);
                $this->emComercial->flush();

                //crear historial para la solicitud
                $objHistorialSolicitud = new InfoDetalleSolHist();
                $objHistorialSolicitud->setDetalleSolicitudId($objDetalleSolicitud);
                $objHistorialSolicitud->setEstado("AsignadoTarea");
                $objHistorialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CANCELACION DEL SERVICIO");
                $objHistorialSolicitud->setUsrCreacion($strUsrCreacion);
                $objHistorialSolicitud->setFeCreacion(new \DateTime('now'));
                $objHistorialSolicitud->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objHistorialSolicitud);
            }
            //------------------------------------------------------------------------------------------------

            $objServicio->setEstado("Cancel");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se cancelo el Servicio");
            $objServicioHistorial->setMotivoId($objMotivo->getId());
            $objServicioHistorial->setEstado("Cancel");
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $objServicioHistorial->setAccion($objAccion->getNombreAccion());
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            $arrayParametros = array();
            $arrayParametros['intIdServicio']   = $intIdServicio;
            $arrayParametros['strUsrCreacion']  = $strUsrCreacion;
            $arrayParametros["strIpCreacion"]   = $strIpCreacion;

            //eliminar las caracteristicas del servicio
            $objServProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                   ->findBy(array("servicioId" => $objServicio->getId(), "estado" => "Activo"));

            for($intX = 0; $intX < count($objServProdCaract); $intX++)
            {
                $intSpc = $objServProdCaract[$intX];
                $intSpc->setEstado("Eliminado");
                $this->emComercial->persist($intSpc);
                $this->emComercial->flush();
            }

            //revisar si es el ultimo servicio en el punto
            $objPunto = $objServicio->getPuntoId();
            $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $objPunto->getId()));
            $intNumServicios = count($arrayServicios);
            $intCont = 0;
            for($intI = 0; $intI < count($arrayServicios); $intI++)
            {
                $strServicioEstado = $arrayServicios[$intI]->getEstado();
                if($strServicioEstado   == "Cancel"           ||
                    $strServicioEstado  == "Cancel-SinEje"    ||
                    $strServicioEstado  == "Anulado"          ||
                    $strServicioEstado  == "Anulada"          ||
                    $strServicioEstado  == "Eliminado"        ||
                    $strServicioEstado  == "Eliminada"        ||
                    $strServicioEstado  == "Eliminado-Migra"  ||
                    $strServicioEstado  == "Rechazada"        ||
                    $strServicioEstado  == "Trasladado")
                {
                    $intCont++;
                }
            }
            if($intCont == ($intNumServicios))
            {
                $objPunto->setEstado("Cancelado");
                $this->emComercial->persist($objPunto);
                $this->emComercial->flush();
            }

            //revisar los puntos si estan todos Cancelados
            $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
            $arrayPuntos = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                             ->findBy(array("personaEmpresaRolId" => $objPersonaEmpresaRol->getId()));
            $intNumPuntos = count($arrayPuntos);
            $intContPuntos = 0;
            for($intI = 0; $intI < count($arrayPuntos); $intI++)
            {
                $objPunto1 = $arrayPuntos[$intI];

                if($objPunto1->getEstado() == "Cancelado")
                {
                    $intContPuntos++;
                }
            }
            if(($intNumPuntos) == $intContPuntos)
            {
                //se cancela el contrato
                $objContrato = $this->emComercial->getRepository('schemaBundle:InfoContrato')
                                              ->findOneBy(array("personaEmpresaRolId" => $objPersonaEmpresaRol->getId()));
                $objContrato->setEstado("Cancelado");
                $this->emComercial->persist($objContrato);
                $this->emComercial->flush();

                //se cancela el personaEmpresaRol
                $objPersonaEmpresaRol->setEstado("Cancelado");
                $this->emComercial->persist($objPersonaEmpresaRol);
                $this->emComercial->flush();

                //se ingresa un registro en el historial empresa persona rol
                $objPersonaHistorial = new InfoPersonaEmpresaRolHisto();
                $objPersonaHistorial->setPersonaEmpresaRolId($objPersonaEmpresaRol);
                $objPersonaHistorial->setEstado("Cancelado");
                $objPersonaHistorial->setUsrCreacion($strUsrCreacion);
                $objPersonaHistorial->setFeCreacion(new \DateTime('now'));
                $objPersonaHistorial->setIpCreacion($strIpCreacion);
                $this->emComercial->persist($objPersonaHistorial);
                $this->emComercial->flush();

                //se cancela el cliente
                $objPersona = $objPersonaEmpresaRol->getPersonaId();
                $objPersona->setEstado("Cancelado");
                $this->emComercial->persist($objPersona);
                $this->emComercial->flush();
            }

            //eliminar ont
            $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                ->find($objServicioTecnico->getElementoClienteId());
            $objElementoCliente->setEstado("Eliminado");
            $this->emInfraestructura->persist($objElementoCliente);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO
            $arrayParametrosAuditoria["strNumeroSerie"]  = $objElementoCliente->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'Cancelado';
            $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
            $arrayParametrosAuditoria["strCodEmpresa"]   = $intIdEmpresa;
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cancelacion Servicio';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;

            /*Eliminar detalle del elemento.*/
            $objDetalleElemento->setEstado('Eliminado');
            $this->emInfraestructura->persist($objDetalleElemento);
            $this->emInfraestructura->flush();

            //Se consulta el login del cliente
            if(is_object($objServicioTecnico->getServicioId()))
            {
                $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                    ->find($objServicioTecnico->getServicioId()->getPuntoId()->getId());
                if(is_object($objInfoPunto))
                {
                    $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();
                }
            }

            $arrayParametrosAuditoria["strUsrCreacion"] = $strUsrCreacion;

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);


            //historial ont
            $objHistorial = $this->emInfraestructura->getRepository('schemaBundle:InfoHistorialElemento')
                                                 ->findBy(array(
                                                     "elementoId" => $objElementoCliente->getId(),
                                                     "estadoElemento" => "Eliminado"
                                                 ));

            if(count($objHistorial) == 0)
            {
                //historial del elemento
                $objHistorialElemento = new InfoHistorialElemento();
                $objHistorialElemento->setElementoId($objElementoCliente);
                $objHistorialElemento->setObservacion("Se elimino el elemento por cancelación de Servicio");
                $objHistorialElemento->setEstadoElemento("Eliminado");
                $objHistorialElemento->setUsrCreacion($strUsrCreacion);
                $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                $objHistorialElemento->setIpCreacion($strIpCreacion);
                $this->emInfraestructura->persist($objHistorialElemento);
                $this->emInfraestructura->flush();
            }

            //eliminar puertos ont
            $arrayInterfacesOnt = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                ->findBy(array("elementoId" => $objElementoCliente->getId()));
            for($intI = 0; $intI < count($arrayInterfacesOnt); $intI++)
            {
                $objInterfacesCliente = $arrayInterfacesOnt[$intI];
                $objInterfacesCliente->setEstado("Eliminado");
                $this->emInfraestructura->persist($objInterfacesCliente);
                $this->emInfraestructura->flush();
            }

            if(is_object($objDetalleSolicCancelacion))
            {
                //Finalizar la solicitud de cancelación
                $objDetalleSolicCancelacion->setEstado("Finalizada");
                $this->emComercial->persist($objDetalleSolicCancelacion);
                $this->emComercial->flush();

                //Finalizar detalle cabeceras
                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo(array(  'idSolicitudPadre'     => $intIdSolicCancelacionPadre,
                    'usrCreacion'          => $strUsrCreacion,
                    'ipCreacion'           => $strIpCreacion ));
                //Se crea Historial de Servicio
                $objDetalleSolsHist = new InfoDetalleSolHist();
                $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicCancelacion);
                $objDetalleSolsHist->setEstado($objDetalleSolicCancelacion->getEstado());
                $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolsHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolsHist->setIpCreacion($strIpCreacion);
                $objDetalleSolsHist->setObservacion("Se Realizo Cancelacion exitosamente");
                $this->emComercial->persist($objDetalleSolsHist);
                $this->emComercial->flush();

                //Enviar Notificacion
                $this->servicioGeneral->enviarNotificacionFinalizadoSolicitudMasiva(array(
                    'idSolicitudPadre' => $intIdSolicCancelacionPadre,
                    'usrCreacion'      => $strUsrCreacion,
                    'idServicio'       => $intIdServicio ));
            }
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }

            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }

            if(is_object($objDetalleSolicCancelacion))
            {
                $objDetalleSolicCancelacion->setEstado("Fallo");
                $this->emComercial->persist($objDetalleSolicCancelacion);
                $this->emComercial->flush();

                $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);

                //Se crea Historial de Servicio
                $objDetalleSolicHist = new InfoDetalleSolHist();
                $objDetalleSolicHist->setDetalleSolicitudId($objDetalleSolicCancelacion);
                $objDetalleSolicHist->setEstado($objDetalleSolicCancelacion->getEstado());
                $objDetalleSolicHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolicHist->setIpCreacion($strIpCreacion);
                $objDetalleSolicHist->setObservacion($e->getMessage());
                $this->emComercial->persist($objDetalleSolicHist);
                $this->emComercial->flush();
            }

            $strStatus = "ERROR";
            $strMensaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            $arrayFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        if($this->emSoporte->getConnection()->isTransactionActive())
        {
            $this->emSoporte->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        //*----------------------------------------------------------------------*/
        $arrayFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayFinal;
    }

    /**
     * cambioElemento
     * funcion que cambio el elemento al cliente
     *
     * @params $arrayPeticiones [$prefijoEmpresa, $idServicio, $idElementoCliente, $modeloCpe, $nombreCpe, $macCpe, $serieCpe, $descripcionCpe
     *                           $tipoElementoCpe, $tipoElementoCpe, $usrCreacion, $ipCreacion ]
     * 
     * @return result
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.0 14-01-2016
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 09-08-2017 -  En la tabla INFO_DETALLE_HISTORIAL se registra el id_persona_empresa_rol del responsable de la tarea
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 06-09-2017 -  En la tabla INFO_DETALLE_ASIGNACION se guarda el PersonaEmpresaRolId del responsable de la tarea de retiro de
     *                            equipo
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.3 15-09-2017 -  Se realizan ajustes porque ahora todos las tareas nacen en estado Asignada
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 05-10-2017 - Se realizan ajustes debido a que reportan que al realizar el cambio de elemento se sigue visualizando en la
     *                           info tecnica, la MAC del equipo anterior.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.5 21-12-2017 - En la tabla INFO_DETALLE_ASIGNACION se registra el campo tipo asignado 'EMPLEADO'
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.6
     * @since 12-09-2018
     * Se agrega la funcionalidad para facturar el equipo en caso que sea facturado al cliente.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.7
     * @since 11-01-2019
     * Se modifica el orden de ejecución de la facturación de equipos.
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 22-05-2019 - Se agrega el ingreso de trazabilidad y creacion de tarea de retiro de equipo al responsable del retiro de equipo
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.9 12-11-2019 - Se modifica el estado en el que van a quedar las interfaces del elemento retirado, quedarian en estado 'Eliminado'.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.0 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 2.1 29-05-2023 - Se captura y envía el Id del técnico que realiza el cambio de equipo en nodos Wifi por solicitudes de cambio
     *                          de modem inmediato.
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 2.2 09-06-2023 - Se agrega una bandera para identificar que es un Nodo Wifi en el registro de trazabilidad.
     * 
     */
    public function cambioElemento($arrayParametros)
    {
        $intIdEmpresa        = $arrayParametros['idEmpresa'];
        $prefijoEmpresa      = $arrayParametros['prefijoEmpresa'];
        $idServicio          = $arrayParametros['idServicio'];
        $idElementoCliente   = $arrayParametros['idElemento'];
        $modeloCpe           = $arrayParametros['modeloCpe'];
        $nombreCpe           = $arrayParametros['nombreCpe'];
        $macCpe              = $arrayParametros['macCpe'];
        $serieCpe            = $arrayParametros['serieCpe'];
        $intIdResponsable    = $arrayParametros['idResponsable'];
        $strTipoResponsable  = $arrayParametros['tipoResponsable'];
        $descripcionCpe      = $arrayParametros['descripcionCpe'];
        $usrCreacion         = $arrayParametros['usrCreacion'];
        $ipCreacion          = $arrayParametros['ipCreacion'];
        $intIdDepartamento   = $arrayParametros['intIdDepartamento'];
        $arrayParametrosHist = array();
        $intIdPunto          = "";
        $strlogin            = "";
        $strCodEmpresa       = "10";
        $strPrefijoEmpresa   = "TN";

        $arrayParametrosAuditoria                       = array();
        $arrayParametrosHist["strCodEmpresa"]           = $intIdEmpresa;
        $arrayParametrosHist["strUsrCreacion"]          = $usrCreacion;
        $arrayParametrosHist["strOpcion"]               = "Historial";
        $arrayParametrosHist["strIpCreacion"]           = $ipCreacion;
        $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;

        $intIdTecnicoEncargado  = "";

        try
        {

            //*DECLARACION DE TRANSACCIONES------------------------------------------*/
            $this->emSoporte->getConnection()->beginTransaction();
            $this->emComercial->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();

            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($idServicio);

            $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findOneById($objServicio->getProductoId());
            $servicioProdCaractMac = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "MAC WIFI", $objProducto);
            $servicioTecnico = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($idServicio);

            $status = 'OK';
            $mensaje = 'OK';

            //buscar elemento cpe
            $cpeNafArray = $this->servicioGeneral->buscarElementoEnNaf($serieCpe, $modeloCpe, "PI", "ActivarServicio");
            $cpeNaf = $cpeNafArray[0]['status'];
            $codigoArticuloCpe = $cpeNafArray[0]['mensaje'];
            if($cpeNaf == "OK")
            {
                //actualizamos registro en el naf del cpe
                $arrayParametrosNaf = array('tipoArticulo'          => 'AF',
                                            'identificacionCliente' => '',
                                            'empresaCod'            => '',
                                            'modeloCpe'             => $modeloCpe,
                                            'serieCpe'              => $serieCpe,
                                            'cantidad'              => '1');

                $mensajeError = $this->cambioElemento->procesaInstalacionElemento($arrayParametrosNaf);

                if(strlen(trim($mensajeError)) > 0)
                {
                    $respuestaFinal[] = array("status" => "NAF", "mensaje" => "ERROR WIFI NAF: " . $mensajeError);
                    return $respuestaFinal;
                }
            }
            else
            {
                $respuestaFinal[] = array('status' => 'NAF', 'mensaje' => $codigoArticuloCpe);
                return $respuestaFinal;
            }


            $strEstadoDetalleSol = "Finalizada";
            $tipoSolicitudCambio = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                     ->findOneBy(array("descripcionSolicitud" => "SOLICITUD CAMBIO DE MODEM INMEDIATO",
                                                                       "estado" => "Activo"));
            $solicitudCambioCpe = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneBy(array("servicioId"      => $objServicio->getId(),
                                                                      "tipoSolicitudId" => $tipoSolicitudCambio,
                                                                      "estado"          => "AsignadoTarea"));
            //eliminar las caracteristicas de la solicitud (elementos escogidos)
            $caracteristicaSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                         ->findOneBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(),
                                                                           "valor"              => $idElementoCliente,
                                                                           "estado"             => "AsignadoTarea"));
            if($caracteristicaSolicitud)
            {
                $caracteristicaSolicitud->setEstado($strEstadoDetalleSol);
                $caracteristicaSolicitud->setUsrCreacion($usrCreacion);
                $caracteristicaSolicitud->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($caracteristicaSolicitud);
                $this->emComercial->flush();
            }

            $solicitudCambioCpe->setEstado($strEstadoDetalleSol);
            $this->emComercial->persist($solicitudCambioCpe);
            $this->emComercial->flush();



            if($solicitudCambioCpe->getEstado() == "Finalizada")
            {
                //crear solicitud para retiro de equipo (cpe)
                $tipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                   ->findOneBy(array("descripcionSolicitud" => "SOLICITUD RETIRO EQUIPO",
                                                                     "estado" => "Activo"));

                $detalleSolicitud = new InfoDetalleSolicitud();
                $detalleSolicitud->setServicioId($objServicio);
                $detalleSolicitud->setTipoSolicitudId($tipoSolicitud);
                $detalleSolicitud->setEstado("Asignada");
                $detalleSolicitud->setUsrCreacion($usrCreacion);
                $detalleSolicitud->setFeCreacion(new \DateTime('now'));
                $detalleSolicitud->setObservacion("SOLICITA RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $this->emComercial->persist($detalleSolicitud);
                $this->emComercial->flush();

                //crear las caract para la solicitud de retiro de equipo
                $entityAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                              ->findOneBy(array('descripcionCaracteristica' => 'ELEMENTO CLIENTE',
                                                                                'estado' => 'Activo'));
                $caractSolicitudCambioElemento = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                   ->findBy(array("detalleSolicitudId" => $solicitudCambioCpe->getId(),
                                                                                  "estado" => $strEstadoDetalleSol));

                for($i = 0; $i < count($caractSolicitudCambioElemento); $i++)
                {
                    $entityCaract = new InfoDetalleSolCaract();
                    $entityCaract->setCaracteristicaId($entityAdmiCaracteristica);
                    $entityCaract->setDetalleSolicitudId($detalleSolicitud);
                    $entityCaract->setValor($caractSolicitudCambioElemento[$i]->getValor());
                    $entityCaract->setEstado("Asignada");
                    $entityCaract->setUsrCreacion($usrCreacion);
                    $entityCaract->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($entityCaract);
                    $this->emComercial->flush();
                }

                //obtener tarea
                $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("SOLICITAR RETIRO EQUIPO");
                $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->findTareasActivasByProceso($entityProceso->getId());
                $entityTarea = $entityTareas[0];

                //grabar nuevo info_detalle para la solicitud de retiro de equipo
                $entityDetalle = new InfoDetalle();
                $entityDetalle->setDetalleSolicitudId($detalleSolicitud->getId());
                $entityDetalle->setTareaId($entityTarea);
                $entityDetalle->setObservacion("Tarea de Retiro de Equipo");
                $entityDetalle->setLongitud($objServicio->getPuntoId()->getLongitud());
                $entityDetalle->setLatitud($objServicio->getPuntoId()->getLatitud());
                $entityDetalle->setPesoPresupuestado(0);
                $entityDetalle->setValorPresupuestado(0);
                $entityDetalle->setIpCreacion($ipCreacion);
                $entityDetalle->setFeSolicitada(new \DateTime('now'));
                $entityDetalle->setFeCreacion(new \DateTime('now'));
                $entityDetalle->setUsrCreacion($usrCreacion);
                $this->emSoporte->persist($entityDetalle);
                $this->emSoporte->flush();

                //Se consulta el login del cliente
                if(is_object($objServicio))
                {
                    $objInfoPunto = $this->emInfraestructura->getRepository('schemaBundle:InfoPunto')
                                                            ->find($objServicio->getPuntoId()->getId());
                    if(is_object($objInfoPunto))
                    {
                        $arrayParametrosAuditoria["strLogin"] = $objInfoPunto->getLogin();

                        $intIdPunto = $objInfoPunto->getId();
                        $strlogin   = $arrayParametrosAuditoria["strLogin"];

                        $objInfoPersonaEmpresaRol = $objInfoPunto->getPersonaEmpresaRolId();

                        if(is_object($objInfoPersonaEmpresaRol))
                        {
                            $intPersonaId = $objInfoPersonaEmpresaRol->getPersonaId();

                            if(!empty($intPersonaId))
                            {
                                $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->find($intPersonaId);

                                if(is_object($objInfoPersona))
                                {
                                    $strNombreAfectado = $objInfoPersona->__toString();
                                }
                            }
                        }
                    }
                }

                $objInfoCriterioAfectado = new InfoCriterioAfectado();
                $objInfoCriterioAfectado->setId("1");
                $objInfoCriterioAfectado->setDetalleId($entityDetalle);
                $objInfoCriterioAfectado->setCriterio("Clientes");
                $objInfoCriterioAfectado->setOpcion("Cliente: " . $strNombreAfectado . " | OPCION: Punto Cliente");
                $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                $objInfoCriterioAfectado->setUsrCreacion($usrCreacion);
                $objInfoCriterioAfectado->setIpCreacion($ipCreacion);
                $this->emSoporte->persist($objInfoCriterioAfectado);
                $this->emSoporte->flush();

                $infoParteAfectada = new InfoParteAfectada();
                $infoParteAfectada->setTipoAfectado("Cliente");
                $infoParteAfectada->setDetalleId($entityDetalle->getId());
                $infoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
                $infoParteAfectada->setAfectadoId($intIdPunto);
                $infoParteAfectada->setFeIniIncidencia(new \DateTime('now'));
                $infoParteAfectada->setAfectadoNombre($strlogin);
                $infoParteAfectada->setAfectadoDescripcion($strNombreAfectado);
                $infoParteAfectada->setFeCreacion(new \DateTime('now'));
                $infoParteAfectada->setUsrCreacion($usrCreacion);
                $infoParteAfectada->setIpCreacion($ipCreacion);
                $this->emSoporte->persist($infoParteAfectada);
                $this->emSoporte->flush();

                //crear historial para la solicitud
                $historialSolicitud = new InfoDetalleSolHist();
                $historialSolicitud->setDetalleSolicitudId($detalleSolicitud);
                $historialSolicitud->setEstado("AsignadoTarea");
                $historialSolicitud->setObservacion("GENERACION AUTOMATICA DE SOLICITUD RETIRO DE EQUIPO POR CAMBIO DE MODEM");
                $historialSolicitud->setUsrCreacion($usrCreacion);
                $historialSolicitud->setFeCreacion(new \DateTime('now'));
                $historialSolicitud->setIpCreacion($ipCreacion);
                $this->emComercial->persist($historialSolicitud);
                $this->emComercial->flush();

                //crear historial para la solicitud
                $objDetalleSolicitudHistorial = new InfoDetalleSolHist();
                $objDetalleSolicitudHistorial->setDetalleSolicitudId($detalleSolicitud);
                $objDetalleSolicitudHistorial->setEstado("Asignada");
                $objDetalleSolicitudHistorial->setObservacion("Se crea tarea de retiro de equipo y la solicitud queda en estado Asignada");
                $objDetalleSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolicitudHistorial->setIpCreacion($ipCreacion);
                $objDetalleSolicitudHistorial->setUsrCreacion($usrCreacion);
                $this->emComercial->persist($objDetalleSolicitudHistorial);
                $this->emComercial->flush();
            }

            //buscamos modelo
            $modeloElementoCpe = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                                                         ->findOneBy(array("nombreModeloElemento" => $modeloCpe, "estado" => "Activo"));

            //SE OBTIENE EL ELEMENTO ANTERIOR
            $intIdElementoAnterior = $servicioTecnico->getElementoClienteId();

            //SE CREA EL NUEVO ELEMENTO
            $objInfoElementoWifi = new InfoElemento();
            $objInfoElementoWifi->setNombreElemento($nombreCpe);
            $objInfoElementoWifi->setDescripcionElemento($descripcionCpe);
            $objInfoElementoWifi->setModeloElementoId($modeloElementoCpe);
            $objInfoElementoWifi->setSerieFisica($serieCpe);
            $objInfoElementoWifi->setEstado("Activo");
            $objInfoElementoWifi->setUsrResponsable($usrCreacion);
            $objInfoElementoWifi->setUsrCreacion($usrCreacion);
            $objInfoElementoWifi->setFeCreacion(new \DateTime('now'));
            $objInfoElementoWifi->setIpCreacion($ipCreacion);
            $this->emInfraestructura->persist($objInfoElementoWifi);
            $this->emInfraestructura->flush();

            //SE ASOCIA EL NUEVO ELEMENTO A LA IP ASIGNADA
            $objInfoIpCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')->findOneByElementoId($intIdElementoAnterior);

            if(is_object($objInfoIpCliente))
            {
                $objInfoIpCliente->setElementoId($objInfoElementoWifi->getId());
                $this->emInfraestructura->persist($objInfoIpCliente);
                $this->emInfraestructura->flush();
            }


            //SE ASOCIA EL NUEVO ELEMENTO A LA INFO_EMPRESA_ELEMENTO_UBICA
            $objInfoEmpresaElementoUbica = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElementoUbica')
                                                                   ->findOneByElementoId($intIdElementoAnterior);

            if(is_object($objInfoEmpresaElementoUbica))
            {
                $objInfoEmpresaElementoUbica->setElementoId($objInfoElementoWifi);
                $this->emInfraestructura->persist($objInfoEmpresaElementoUbica);
                $this->emInfraestructura->flush();
            }


            //SE ASOCIA EL NUEVO ELEMENTO A LA INFO_EMPRESA_ELEMENTO
            $objInfoEmpresaElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                                                   ->findOneByElementoId($intIdElementoAnterior);

            if(is_object($objInfoEmpresaElemento))
            {
                $objInfoEmpresaElemento->setElementoId($objInfoElementoWifi);
                $this->emInfraestructura->persist($objInfoEmpresaElemento);
                $this->emInfraestructura->flush();
            }

            //SE GENERAN LAS INTERFACES DEL NUEVO ELEMENTO
            $arrayInterfaceModelo = $this->emInfraestructura->getRepository('schemaBundle:AdmiInterfaceModelo')
                                                            ->findBy(array("modeloElementoId" => $modeloElementoCpe->getId()));

            foreach($arrayInterfaceModelo as $objArrayInterfaceModelo)
            {
                $intCantidadInterfaces = $objArrayInterfaceModelo->getCantidadInterface();
                $strFormato            = $objArrayInterfaceModelo->getFormatoInterface();

                for($i = 1; $i <= $intCantidadInterfaces; $i++)
                {
                    $objInterfacesElementoWifi = new InfoInterfaceElemento();
                    $arrayFormat = explode("?", $strFormato);

                    $strNombreInterfaceElemento = $arrayFormat[0] . $i;

                    $objInterfacesElementoWifi->setNombreInterfaceElemento($strNombreInterfaceElemento);
                    $objInterfacesElementoWifi->setElementoId($objInfoElementoWifi);
                    $objInterfacesElementoWifi->setEstado("not connect");
                    $objInterfacesElementoWifi->setUsrCreacion($usrCreacion);
                    $objInterfacesElementoWifi->setFeCreacion(new \DateTime('now'));
                    $objInterfacesElementoWifi->setIpCreacion($ipCreacion);
                    $this->emInfraestructura->persist($objInterfacesElementoWifi);
                    $this->emInfraestructura->flush();
                }
            }

            //SE REGISTRA LA MAC DEL NUEVO EQUIPO
            $objInfoDetalleElemento = new InfoDetalleElemento();
            $objInfoDetalleElemento->setElementoId($objInfoElementoWifi->getId());
            $objInfoDetalleElemento->setDetalleNombre('MAC');
            $objInfoDetalleElemento->setDetalleValor($macCpe);
            $objInfoDetalleElemento->setDetalleDescripcion('MAC DEL EQUIPO WIFI');
            $objInfoDetalleElemento->setEstado("Activo");
            $objInfoDetalleElemento->setUsrCreacion($usrCreacion);
            $objInfoDetalleElemento->setIpCreacion($ipCreacion);
            $objInfoDetalleElemento->setFeCreacion(new \DateTime('now'));
            $this->emInfraestructura->persist($objInfoDetalleElemento);
            $this->emInfraestructura->flush();

            //SE REGISTRA EL TRACKING DEL ELEMENTO NUEVO
            $arrayParametrosAuditoria["strNumeroSerie"]  = $serieCpe;
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Activo';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'Activo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'Cliente';
            $arrayParametrosAuditoria["strCodEmpresa"]   = '10';
            $arrayParametrosAuditoria["strTransaccion"]  = 'Activacion Cliente';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;
            $arrayParametrosAuditoria["strUsrCreacion"]  = $usrCreacion;

            if(!empty($intIdResponsable) && !empty($strTipoResponsable))
            {
                if(is_object($solicitudCambioCpe))
                {
                     $intMotivoId = $solicitudCambioCpe->getMotivoId();

                     if(!empty($intMotivoId))
                     {
                          $objAdmiMotivo = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->find($intMotivoId);

                          if(is_object($objAdmiMotivo))
                          {
                              $arrayParametrosAuditoria["strObservacion"] = $objAdmiMotivo->getNombreMotivo();
                          }
                     }
                }

                if($strTipoResponsable == "C" )
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($intIdResponsable);

                    if(!empty($arrayDatos) && isset($arrayDatos))
                    {
                        $intAsignadoId                            = $intIdResponsable;
                        $arrayParametrosAuditoria["intIdPersona"] = $arrayDatos['idPersona'];
                        $intIdPersona                             = $arrayParametrosAuditoria["intIdPersona"];
                        $intInfoPersonaEmpresaRolId               = $arrayDatos['idPersonaEmpresaRol'];

                        $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                      ->find($intInfoPersonaEmpresaRolId);

                        if(is_object($objInfoPersonaEmpresaRol))
                        {
                            $intIdDepartamento = $objInfoPersonaEmpresaRol->getDepartamentoId();

                            $objAdmiDepartamento = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamento);

                            if(is_object($objAdmiDepartamento))
                            {
                                //Nombre del departamento
                                $intIdDepartamentoNotif = $objAdmiDepartamento->getId();
                                $strDepartamentoNotif   = $objAdmiDepartamento->getNombreDepartamento();
                            }
                        }

                        $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
                        if(is_object($objInfoPersona))
                        {
                            //nombres de afectado
                            $strNombreRefAsignado = $objInfoPersona->__toString();
                        }
                    }

                    //se obtiene nombre de la cuadrilla
                    $objAdmiCuadrilla = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdResponsable);

                    if(is_object($objAdmiCuadrilla))
                    {
                        //Nombre de la cuadrilla
                        $strAsignadoNombre = $objAdmiCuadrilla->getNombreCuadrilla();
                    }

                    $strTipoAsignado = "CUADRILLA";

                    //asigno el id del técnico responsable del retiro
                    $intIdTecnicoEncargado = $intIdPersona;

                }
                else if($strTipoResponsable == "E" )
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                  ->find($arrayParametros["idResponsable"]);

                    if(is_object($objInfoPersonaEmpresaRol))
                    {
                        //Id persona empresa rol
                        $intInfoPersonaEmpresaRolId = $objInfoPersonaEmpresaRol->getId();

                        //Id del departamento
                        $intAsignadoId = $objInfoPersonaEmpresaRol->getDepartamentoId();

                        $objAdmiDepartamento = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')->find($intAsignadoId);

                        if(is_object($objAdmiDepartamento))
                        {
                            $intIdDepartamentoNotif = $objAdmiDepartamento->getId();
                            $strAsignadoNombre      = $objAdmiDepartamento->getNombreDepartamento();
                            $strDepartamentoNotif   = $strAsignadoNombre;
                        }

                        //Id persona
                        $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                        if(is_object($objInfoPersona))
                        {
                            //nombres de afectado
                            $intIdPersona         = $objInfoPersona->getId();
                            $strNombreRefAsignado = $objInfoPersona->__toString();

                             //técnico encargado del retiro del equipo
                             $intIdTecnicoEncargado = $objInfoPersona->getId();
                        }

                        $arrayParametrosAuditoria["intIdPersona"] = $intIdPersona;
                    }

                    $strTipoAsignado = "EMPLEADO";
                }
            }

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);


            //Se obtiene datos del responsable de la tarea
            $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                          ->find($intInfoPersonaEmpresaRolId);

            //asignar los mismos responsables a la solicitud de retiro de equipo
            $entityDetalleAsignacion = new InfoDetalleAsignacion();
            $entityDetalleAsignacion->setDetalleId($entityDetalle);
            $entityDetalleAsignacion->setAsignadoId($intAsignadoId);
            $entityDetalleAsignacion->setAsignadoNombre($strAsignadoNombre);
            $entityDetalleAsignacion->setRefAsignadoId($intIdPersona);
            $entityDetalleAsignacion->setRefAsignadoNombre($strNombreRefAsignado);
            $entityDetalleAsignacion->setPersonaEmpresaRolId($intInfoPersonaEmpresaRolId);
            $entityDetalleAsignacion->setTipoAsignado($strTipoAsignado);
            $entityDetalleAsignacion->setIpCreacion($ipCreacion);
            $entityDetalleAsignacion->setFeCreacion(new \DateTime('now'));
            $entityDetalleAsignacion->setUsrCreacion($usrCreacion);
            $this->emSoporte->persist($entityDetalleAsignacion);
            $this->emSoporte->flush();

            //Se ingresa el historial de la tarea
            if(is_object($entityDetalle))
            {
                $arrayParametrosHist["intDetalleId"] = $entityDetalle->getId();
            }
            $arrayParametrosHist["strObservacion"]  = "Tarea Asignada";
            $arrayParametrosHist["strEstadoActual"] = "Asignada";
            $arrayParametrosHist["strAccion"]       = "Asignada";

            $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

            //Se ingresa el seguimiento de la tarea
            $arrayParametrosHist["strObservacion"] = "Tarea fue Asignada a ".$strNombreRefAsignado;
            $arrayParametrosHist["strOpcion"]      = "Seguimiento";

            $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

            //Se genera el número de la tarea
            $arrayParametrosTarea["objInfoDetalle"]   = $entityDetalle;
            $arrayParametrosTarea["strMensaje"]       = "Tarea de retiro de equipo";
            $arrayParametrosTarea["strObservacion"]   = "Tarea de retiro de equipo";
            $arrayParametrosTarea["strCodigoEmpresa"] = $intIdEmpresa;
            $arrayParametrosTarea["strUser"]          = $usrCreacion;
            $arrayParametrosTarea["strIpCreacion"]    = $ipCreacion;

            $arrayRespuesta = $this->serviceSoporte->generarNumeroTareaPorDetalleId($arrayParametrosTarea);

            if($arrayRespuesta["strStatus"] === "ERROR")
            {
                throw new \Exception($arrayRespuesta["strMensaje"]);
            }

            //******************************************Envio de NOTIFICACION***************************************************//
            //Se obtiene la ciudad del responsable
            if(!empty($intInfoPersonaEmpresaRolId))
            {
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                              ->find($intInfoPersonaEmpresaRolId);

                if(is_object($objInfoPersonaEmpresaRol))
                {
                    $intOficina = $objInfoPersonaEmpresaRol->getOficinaId();
                }

                if(!empty($intOficina))
                {
                    $objInfoOficinaGrupo = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')->find($intOficina);

                    if(is_object($objInfoOficinaGrupo))
                    {
                        $intCantonId = $objInfoOficinaGrupo->getCantonId();
                    }
                }
            }

            //Se obtiene el correo del responsable
            $objPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                         ->findOneBy(array('personaId'       => $intIdPersona,
                                                                           'formaContactoId' => 5,
                                                                           'estado'          => "Activo"));

            if($objPersonaFormaContacto)
            {
                $arrayTo[] = $objPersonaFormaContacto->getValor(); //Correo Persona Asignada
            }

            $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneByLogin($usrCreacion);

            if(is_object($objInfoPersona))
            {
                $strNombreLogeado = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
            }

            if(is_object($entityTarea))
            {
                $strNombreProceso = $entityTarea->getProcesoId()->getNombreProceso();
                $strNombreTarea   = $entityTarea->getNombreTarea();
            }

            $strAsunto = "Asignacion de Tarea | PROCESO: ".$strNombreProceso;

            $arrayParametros = array('detalle'            => $entityDetalle,
                                     'numeroTarea'        => $arrayRespuesta["intNumeroTarea"],
                                     'nombreProceso'      => $strNombreProceso,
                                     'nombreTarea'        => $strNombreTarea,
                                     'nombreDepartamento' => $strDepartamentoNotif." - ".$strNombreRefAsignado,
                                     'observacion'        => "Tarea de retiro de equipo generada automáticamente",
                                     'empleadoLogeado'    => $arrayParametros['usrCreacion']." - ".$strNombreLogeado,
                                     'empresa'            => $strPrefijoEmpresa,
                                     'loginProcesado'     => $strlogin);

            $this->envioPlantilla->generarEnvioPlantilla($strAsunto,
                                                         $arrayTo,
                                                         'TAREACERT',
                                                         $arrayParametros,
                                                         $strCodEmpresa,
                                                         $intCantonId,
                                                         $intIdDepartamentoNotif);
            //******************************************Envio de NOTIFICACION**************************************************//

            //SE CAMBIA A ESTADO ELIMINADO EL ELEMENTO ANTERIOR
            $InfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->find($intIdElementoAnterior);

            if(is_object($InfoElemento))
            {
                $InfoElemento->setEstado("Eliminado");
                $this->emInfraestructura->persist($InfoElemento);
                $this->emInfraestructura->flush();
            }

            //SE REGISTRA EL TRACKING DEL ELEMENTO QUE SE ELIMINA
            $arrayParametrosAuditoria["strNumeroSerie"]  = $InfoElemento->getSerieFisica();
            $arrayParametrosAuditoria["strEstadoTelcos"] = 'Eliminado';
            $arrayParametrosAuditoria["strEstadoNaf"]    = 'Instalado';
            $arrayParametrosAuditoria["strEstadoActivo"] = 'CambioEquipo';
            $arrayParametrosAuditoria["strUbicacion"]    = 'EnTransito';
            $arrayParametrosAuditoria["strCodEmpresa"]   = "10";
            $arrayParametrosAuditoria["strTransaccion"]  = 'Cambio de Elemento';
            $arrayParametrosAuditoria["intOficinaId"]    = 0;
            //técnico responsable
            $arrayParametrosAuditoria["intIdPersona"] = $intIdTecnicoEncargado;
            $arrayParametrosAuditoria["strNodoWifi"] = 'SI';

            $this->serviceInfoElemento->ingresaAuditoriaElementos($arrayParametrosAuditoria);

            //historial elemento
            $objHistorialElemento = new InfoHistorialElemento();
            $objHistorialElemento->setElementoId($InfoElemento);
            $objHistorialElemento->setEstadoElemento($InfoElemento->getEstado());
            $objHistorialElemento->setObservacion("Se Cambio de Modelo, modelo anterior:" . $InfoElemento->getModeloElementoId()
                                                                                                         ->getNombreModeloElemento()
                                                 . ", serie anterior:" . $InfoElemento->getSerieFisica());
            $objHistorialElemento->setUsrCreacion($usrCreacion);
            $objHistorialElemento->setFeCreacion(new \DateTime('now'));
            $objHistorialElemento->setIpCreacion($ipCreacion);
            $this->emInfraestructura->persist($objHistorialElemento);
            $this->emInfraestructura->flush();

            //SE DESCONECTAN LAS INTERFACES DEL ELEMENTO WIFI Y SE ELIMINAN LOS ENLACES ASOCIADOS
            $arrayInterfaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->findBy(array("elementoId" => $intIdElementoAnterior));

            foreach($arrayInterfaceElemento as $objInterfaceElemento)
            {
                $objInterfaceElemento->setEstado("Eliminado");
                $objInterfaceElemento->setUsrUltMod($usrCreacion);
                $objInterfaceElemento->setFeUltMod(new \DateTime('now'));
                $this->emInfraestructura->persist($objInterfaceElemento);

                //SE ELIMINAN LOS ENLACES INI
                $arrayInfoEnlacesIni = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                               ->findBy(array("interfaceElementoIniId" => $objInterfaceElemento->getId()));

                foreach($arrayInfoEnlacesIni as $objInfoEnlaceIni)
                {
                    $objInfoEnlaceIni->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoEnlaceIni);
                }

                //SE ELIMINAN LOS ENLACES FIN
                $arrayInfoEnlacesFin = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                               ->findBy(array("interfaceElementoFinId" => $objInterfaceElemento->getId()));

                foreach($arrayInfoEnlacesFin as $objInfoEnlaceFin)
                {
                    $objInfoEnlaceFin->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objInfoEnlaceFin);

                    //Se recrea el enlace Eliminado, asociando a la nueva interfaz generada

                    //Se obtiene la interface Ini
                    $objInfoInterfaceElementoIni = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->find($objInfoEnlaceFin->getInterfaceElementoIniId()->getId());

                    //Se obtiene la interface Fin, del elemento recien generado
                    $objInfoInterfaceElementoFin = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->findOneBy(array("elementoId" => $objInfoElementoWifi->getId(),
                                                                                             "nombreInterfaceElemento" => "wan1"));

                    //Se obtiene el tipo de interface
                    $objAdmiTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                ->find($objInfoEnlaceFin->getTipoMedioId()->getId());

                    if(is_object($objInfoInterfaceElementoIni) && is_object($objInfoInterfaceElementoFin)
                       && is_object($objAdmiTipoMedio))
                    {

                        //Se pone en estado connect la interface
                        $objInfoInterfaceElementoFin->setEstado("connected");
                        $this->emInfraestructura->persist($objInfoInterfaceElementoFin);

                        //Se crea el nuevo enlace
                        $objInfoEnlace = new InfoEnlace();
                        $objInfoEnlace->setInterfaceElementoIniId($objInfoInterfaceElementoIni);
                        $objInfoEnlace->setInterfaceElementoFinId($objInfoInterfaceElementoFin);
                        $objInfoEnlace->setTipoMedioId($objAdmiTipoMedio);
                        $objInfoEnlace->setTipoEnlace($objInfoEnlaceFin->getTipoEnlace());
                        $objInfoEnlace->setEstado("Activo");
                        $objInfoEnlace->setUsrCreacion($usrCreacion);
                        $objInfoEnlace->setFeCreacion(new \DateTime('now'));
                        $objInfoEnlace->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objInfoEnlace);

                        //Se crea el detalle interface
                        $objInfoDetalleInterface = new InfoDetalleInterface();
                        $objInfoDetalleInterface->setInterfaceElementoId($objInfoInterfaceElementoFin);
                        $objInfoDetalleInterface->setDetalleNombre("servicio");
                        $objInfoDetalleInterface->setDetalleValor($idServicio);
                        $objInfoDetalleInterface->setUsrCreacion($usrCreacion);
                        $objInfoDetalleInterface->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleInterface->setIpCreacion($ipCreacion);
                        $this->emInfraestructura->persist($objInfoDetalleInterface);
                    }
                }
            }

            $this->emInfraestructura->flush();

            //SE ACTUALIZA EL ELEMENTO DEL CLIENTE EN LA INFO_SERVICIO_TECNICO
            $servicioTecnico->setElementoClienteId($objInfoElementoWifi->getId());
            $this->emInfraestructura->persist($servicioTecnico);
            $this->emInfraestructura->flush();

            //MAC VIEJA
            if($servicioProdCaractMac)
            {
                $servicioProdCaractMac->setEstado("Eliminado");
                $this->emComercial->persist($servicioProdCaractMac);
                $this->emComercial->flush();
            }

            //servicio prod caract service-profile
            $this->servicioGeneral->ingresarServicioProductoCaracteristica($objServicio, $objProducto, "MAC WIFI", $macCpe, $usrCreacion);

            $objElementoClienteActual   = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->findOneById($intIdElementoAnterior);
            $objElementoCpeNuevo        = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                  ->findOneById($objInfoElementoWifi->getId());

            $strHistorialPorServicio = "<b>Se realizo un Cambio de Elemento Cliente:</b><br>"
                . "<b style='color:red'>CPE Anterior : </b><br>"
                . "<b>Nombre CPE : </b> ".$objElementoClienteActual->getNombreElemento()."<br>"
                . "<b>Serie CPE  : </b> ".$objElementoClienteActual->getSerieFisica()."<br>"
                . "<b>Modelo CPE : </b> ".$objElementoClienteActual->getModeloElementoId()->getNombreModeloElemento()."<br>"
                . "<b style='color:red'>CPE Actual : </b><br>"
                . "<b>Nombre CPE : </b> ".$objElementoCpeNuevo->getNombreElemento()."<br>"
                . "<b>Serie  CPE : </b> ".$objElementoCpeNuevo->getSerieFisica()."<br>"
                . "<b>Modelo CPE : </b> ".$objElementoCpeNuevo->getModeloElementoId()->getNombreModeloElemento()."<br>";

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($objServicio);
            $servicioHistorial->setObservacion($strHistorialPorServicio);
            $servicioHistorial->setEstado($objServicio->getEstado());
            $servicioHistorial->setUsrCreacion($usrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($ipCreacion);
            $this->emComercial->persist($servicioHistorial);
            $this->emComercial->flush();

        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }

            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->getConnection()->rollback();
            }
            $status = "ERROR";
            $mensaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            $arrayFinal[] = array('status' => $status, 'mensaje' => $mensaje);
            return $arrayFinal;
        }
        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        if($this->emSoporte->getConnection()->isTransactionActive())
        {
            $this->emSoporte->getConnection()->commit();
        }

        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->emSoporte->getConnection()->close();
        $arrayFinal[] = array('status' => $status, 'mensaje' => $mensaje);

        //Proceso que graba tarea en INFO_TAREA
        $arrayParametrosInfoTarea['intDetalleId']   = $arrayParametrosHist["intDetalleId"];
        $arrayParametrosInfoTarea['strUsrCreacion'] = $arrayParametrosHist["strUsrCreacion"];
        $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);

        //Se crea la solicitud de FACTURACION RETIRO DE EQUIPO en caso de ser necesario.
        $this->serviceSolicitudes
             ->creaSolicitudFacturacionEquipo(array("intElementoClienteId"    => $idElementoCliente,
                                                    "objInfoDetalleSolicitud" => $solicitudCambioCpe,
                                                    "objInfoServicio"         => $objServicio,
                                                    "strEmpresaCod"           => strval($intIdEmpresa),
                                                    "strUsrCreacion"          => $usrCreacion,
                                                    "strIpCreacion"           => $ipCreacion));
        return $arrayFinal;
    }

    
    
    /**
    * liberarPuerto
    * Funcion que libera el puerto del elemento wifi
    *
    * @params $arrayParametros intIdServicio  strUsrCreacion  strIpCreacion
    * @return $arrayResultado 
    * 
    * @author John Vera <javera@telconet.ec>
    * @version 1.0 24-10-2016
    */  
    
    public function liberarPuertoWifi($arrayParametros)
    {
        $intIdServicio  = $arrayParametros['intIdServicio'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
     
        try
        {
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            
            if(!$objServicio)
            {
                throw new \Exception("No existe el servicio.");
            }

            $objProducto = $objServicio->getProductoId();

            $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());
            if(!$objServicioTecnico)
            {
                throw new \Exception('No existe el servicio técnico.');
            }

            //elimino el enlace y libero las interfaces
            if($objServicioTecnico->getInterfaceElementoConectorId())
            {
                $objEnlaceEdit =$this->emInfraestructura
                                     ->getRepository('schemaBundle:InfoEnlace')
                                     ->findOneBy(array('interfaceElementoIniId' => $objServicioTecnico->getInterfaceElementoConectorId(),
                                                                       'estado' => 'Activo'));
                if($objEnlaceEdit)
                {
                    $objInterfaceIni = $objEnlaceEdit->getInterfaceElementoIniId();
                    $objInterfaceFin = $objEnlaceEdit->getInterfaceElementoFinId();

                    $objEnlaceEdit->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlaceEdit);
                    $this->emInfraestructura->flush();

                    $objInterfaceIni->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceIni);
                    $this->emInfraestructura->flush();

                    $objInterfaceFin->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceFin);
                    $this->emInfraestructura->flush();
                }
                else
                {
                    //no tiene enlace pero si debo reversar el puerto del router wifi
                    $objInterfaceElementoEdit = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                        ->find($objServicioTecnico->getInterfaceElementoConectorId());

                    $objInterfaceElementoEdit->setEstado('not connect');
                    $this->emInfraestructura->persist($objInterfaceElementoEdit);
                    $this->emInfraestructura->flush();
                    
                }
            }

            $objSpcEnlace = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "ENLACE_DATOS", $objProducto);

            if($objSpcEnlace)
            {
                $objSpcEnlace->setEstado("Eliminado");
                $this->emComercial->persist($objSpcEnlace);
                $this->emComercial->flush();
            }

            $objSpc = $this->servicioGeneral->getServicioProductoCaracteristica($objServicio, "INTERFACE_ELEMENTO_ID", $objProducto);

            //elimino el enlace del elemento wifi a el odf (caso backbone)
            if($objSpc)
            {
                //elimino la caracteristica
                $objSpc->setEstado("Eliminado");
                $this->emComercial->persist($objSpc);
                $this->emComercial->flush();
                //eliminamos el enlace
                $objEnlaceElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoEnlace')
                                                             ->findOneBy(array('interfaceElementoIniId' => $objSpc->getValor(),
                                                                               'estado' => 'Activo'));

                if($objEnlaceElemento)
                {
                    $objInterfaceIni = $objEnlaceElemento->getInterfaceElementoIniId();
                    $objInterfaceFin = $objEnlaceElemento->getInterfaceElementoFinId();

                    $objEnlaceElemento->setEstado("Eliminado");
                    $this->emInfraestructura->persist($objEnlaceElemento);
                    $this->emInfraestructura->flush();

                    $objInterfaceIni->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceIni);
                    $this->emInfraestructura->flush();

                    $objInterfaceFin->setEstado("not connect");
                    $this->emInfraestructura->persist($objInterfaceFin);
                    $this->emInfraestructura->flush();
                }
                else
                {
                    if($objSpc->getValor())
                    {
                        //no tiene enlace pero si debo reversar el puerto del router wifi
                        $objInterfaceElementoEdit = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                            ->find($objSpc->getValor());

                        $objInterfaceElementoEdit->setEstado('not connect');
                        $this->emInfraestructura->persist($objInterfaceElementoEdit);
                        $this->emInfraestructura->flush();
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoElementoWifiService.liberarPuertoWifi', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
                        
            $arrayResultado = array('strStatus' => "ERROR", 'strMensaje' => "No se liberó el puerto. ");
            return $arrayResultado;
            
        }
        
        $arrayResultado = array('strStatus' => 'OK', 'strMensaje' => 'OK');
        return $arrayResultado;
    }

}

