<?php

namespace telconet\comercialBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;

class SolicitudesService {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    private $emGeneral;
    private $serviceUtil;
    private $serviceEnvioPlantilla;
    private $serviceSoporte;
    public $serviceAutorizaciones;
    const FACTURACION_CAMBIO_MODEM_INMEDIATO  = 'FACTURACION_CAMBIO_MODEM_INMEDIATO';
    const SOLICITUD_FACTURACION_RETIRO_EQUIPO = 'SOLICITUD FACTURACION RETIRO EQUIPO';
    const SOLICITUD_CAMBIO_DE_MODEM_INMEDIATO = 'SOLICITUD CAMBIO DE MODEM INMEDIATO';
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom       = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emGeneral   = $container->get('doctrine.orm.telconet_general_entity_manager');
        $this->serviceUtil = $container->get('schema.Util');
        $this->serviceEnvioPlantilla = $container->get('soporte.EnvioPlantilla');
        $this->serviceSoporte        = $container->get('soporte.SoporteService');
        $this->serviceAutorizaciones = $container->get('comercial.Autorizaciones');
    }
    
    /**    
     * Documentación para el método 'anulaFinalizaSolcitud'.
     *
     * Descripcion: Permite anular o finalizar una solicitud.
     * 
     * @author Andres Montero <amontero@telconet.ec>
     * @version 1.0 18-02-2016     
     * 
     * @param array $params[intMotivoId (integer)     =>    id del motivo de finalizar o anular solicitud
     *                      strObservacion (string)   =>    observacion de finalizar o anular solicitud
     *                      intSolicitudId (integer)  =>    id de solicitud que se desea finalizar o anular
     *                      strAccion (string)        =>    accion que se desea realizar
     *                      strUsrCreacion (string)   =>    Usuario de creacion 
     *                      strIpCreacion (string)    =>    IP de creacion ]
     * 
     * @return obj json_encode (objeto respuesta json_encode)
     */
    public function anulaFinalizaSolicitud($params)
    {
        $intIdMotivo      = $params['intIdMotivo'];
        $strObservacion   = $params['strObservacion'];
        $intIdSolicitud   = $params['strIdSolicitud'];
        $strAccion        = $params['strAccion'];
        $strIp            = $params['strIpCreacion'];
        $strUsrCreacion   = $params['strUsrCreacion'];
        $strEstado        = '';
        $objRespuesta     = '';
        
        if($strAccion=='anular')
        {
            $strEstado='Anulado';
        }
        elseif($strAccion=='finalizar')
        {
            $strEstado='Finalizado';
        }    
       $this->emcom->getConnection()->beginTransaction();
        try
        {
            $objInfoDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            if (!$objInfoDetalleSolicitud) 
            {
                throw $this->createNotFoundException('No se encontro la solicitud buscada');
            }
            //SI LA SOLICITUD ES 'SOLICITUD DESCUENTO' se anula, se elimina valores de descuento del servicio
            if (strtoupper($objInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud())=='SOLICITUD DESCUENTO')
            {
                $objInfoServicio = $this->emcom->getRepository('schemaBundle:InfoServicio')->find($objInfoDetalleSolicitud->getServicioId()->getId());
                if (!$objInfoServicio) 
                {
                    throw $this->createNotFoundException('No se encontro servicio');
                } 
                $objInfoServicio->setValorDescuento(0);
                $objInfoServicio->setPorcentajeDescuento(0);
                $this->emcom->persist($objInfoServicio);
                $this->emcom->flush();
            }   
            $objInfoDetalleSolicitud->setEstado($strEstado);
            $objInfoDetalleSolicitud->setObservacion($strObservacion);
            $this->emcom->persist($objInfoDetalleSolicitud);
            $this->emcom->flush();

            //Grabamos en la tabla de historial de la solicitud
            $entityHistorial= new InfoDetalleSolHist();
            $entityHistorial->setEstado($strEstado);
            $entityHistorial->setDetalleSolicitudId($objInfoDetalleSolicitud);
            $entityHistorial->setUsrCreacion($strUsrCreacion);
            $entityHistorial->setFeCreacion(new \DateTime('now'));
            $entityHistorial->setIpCreacion($strIp);
            $entityHistorial->setMotivoId($intIdMotivo);
            $entityHistorial->setObservacion($strObservacion);
            $this->emcom->persist($entityHistorial);
            $this->emcom->flush();
            $this->emcom->getConnection()->commit();   
            $objRespuesta=json_encode(array('respuestaAnular' => "OK"));            
       }       
       catch (\Exception $e) 
       {
           $this->emcom->getConnection()->rollback();
           $this->emcom->getConnection()->close();
           $objRespuesta=json_encode(array('respuestaAnular' => $e->getMessage()));
       }
       return $objRespuesta;        
    }
    

    /**
     * Función que crea la SOLICITUD FACTURACION RETIRO EQUIPO en caso de ser necesario.
     * Si ya existe una solicitud de facturación creada en el mismo día para el mismo login, se agregan los equipos como características.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 04-09-2018
     * Versión inicial.
     *
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.1
     * @since 11-01-2019
     * Se agrega el envío de notificación en caso de error.
     *
     * @param array $arrayParametros Parámetros de entrada a la función.
     * @param array $arrayParametros["intElementoClienteId"] Elemento cliente del servicio.
     * @param array $arrayParametros["objInfoServicio"]        Servicio al que se le realiza el cambio de equipo.
     * @param array $arrayParametros["objInfoDetalleSolicitud"]
     * @param array $arrayParametros["strEmpresaCod"]
     * @param array $arrayParametros["strUsrCreacion"]
     * @param array $arrayParametros["strIpCreacion"]
     */
    public function creaSolicitudFacturacionEquipo($arrayParametros)
    {
        $arrayRespuesta                = array();
        $objInfoDetalleSolicitudOrigen = $arrayParametros["objInfoDetalleSolicitud"];
        $objInfoServicio               = $arrayParametros["objInfoServicio"];
        $strEmpresaCod                 = $arrayParametros["strEmpresaCod"];
        $intElementoClienteId          = $arrayParametros["intElementoClienteId"];
        $strUsrCreacion                = $arrayParametros["strUsrCreacion"];
        $strIpCreacion                 = $arrayParametros["strIpCreacion"];
        $floatValorAFacturar           = 0;
        $strLogin                      = null;
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            //Si no es proporcionado el objeto de la InfoDetalleSolicitud, finaliza el proceso.
            if (!is_object($objInfoDetalleSolicitudOrigen))
            {
                throw new \Exception("No se ha podido obtener la solicitud de cambio de modem inmediato.");
            }

            //Se obtiene si la empresa aplica o no al flujo de facturaicón de cambio de módem inmediato.
            $arrayParametrosAplicaFact       = array("strProcesoAccion" => self::FACTURACION_CAMBIO_MODEM_INMEDIATO,
                                                     "strEmpresaCod"    => $strEmpresaCod);
            $strAplicaFacturacionCambioModem = $this->serviceUtil->empresaAplicaProceso($arrayParametrosAplicaFact);

            //Si la solicitud es venta y si la empresa aplica al flujo de facturación de cambio de módem inmediato
            if ("S" == $strAplicaFacturacionCambioModem && "V" == $objInfoDetalleSolicitudOrigen->getTipoDocumento())
            {
                //Si no se obtiene el objeto del servicio enviado por parámetro, se obtiene el servicio de la solicitud.
                $objInfoServicio  = is_object($objInfoServicio) ? $objInfoServicio : $objInfoDetalleSolicitudOrigen->getServicioId();

                //Se obtiene el login del servicio
                $intPuntoId       = $objInfoServicio->getPuntoId();
                $strLogin         = $this->emcom->getRepository("schemaBundle:InfoPunto")->findOneById($intPuntoId)->getLogin();

                //Se obtiene la SOLICITUD FACTURACION RETIRO EQUIPO creada en el día.
                $arraySolicitudesFacturacionCreadas = $this->emcom
                                                           ->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                           ->getSolicitudPorPuntoFechaEstadoSolicitud
                                                            (
                                                                array("strEmpresaCod"      => $strEmpresaCod,
                                                                      "intIdPunto"         => $intPuntoId,
                                                                      "strEstadoSolicitud" => "Pendiente",
                                                                      "strEstadoTipoSol"   => "Activo",
                                                                      "intNumeroDias"      => 1,
                                                                      "arraySolicitudes"   => array(self::SOLICITUD_FACTURACION_RETIRO_EQUIPO))
                                                            );

                //Si no se obtiene la solicitud creada en el mismo día, se crea una nueva.
                //Si ya existe, se obtiene el objeto para seguir agregando las características.
                if (is_null($arraySolicitudesFacturacionCreadas))
                {
                    //Se obtiene el tipo de solicitud
                    $objSolicitudFactRetiroEquipo = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                         ->findOneBy(array("descripcionSolicitud" => self::SOLICITUD_FACTURACION_RETIRO_EQUIPO,
                                                                           "estado"               => "Activo"));

                    //Se inserta la solicitud
                    $objDetalleSolFacturacionEquipos = new InfoDetalleSolicitud();
                    $objDetalleSolFacturacionEquipos->setServicioId($objInfoServicio);
                    $objDetalleSolFacturacionEquipos->setTipoSolicitudId($objSolicitudFactRetiroEquipo);
                    $objDetalleSolFacturacionEquipos->setTipoDocumento($objInfoDetalleSolicitudOrigen->getTipoDocumento());
                    $objDetalleSolFacturacionEquipos->setObservacion("Se crea la " . self::SOLICITUD_FACTURACION_RETIRO_EQUIPO . " | Obs: " . 
                                                                     $objInfoDetalleSolicitudOrigen->getObservacion());
                    $objDetalleSolFacturacionEquipos->setPrecioDescuento(0);
                    $objDetalleSolFacturacionEquipos->setMotivoId($objInfoDetalleSolicitudOrigen->getMotivoId());
                    $objDetalleSolFacturacionEquipos->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolFacturacionEquipos->setUsrCreacion($strUsrCreacion);
                    $objDetalleSolFacturacionEquipos->setEstado('Pendiente');
                    $this->emcom->persist($objDetalleSolFacturacionEquipos);
                    $this->emcom->flush();

                    //Se inserta el historial por creación de la solicitud.
                    $objInfoDetalleSolFactHistorial = new InfoDetalleSolHist();
                    $objInfoDetalleSolFactHistorial->setDetalleSolicitudId($objDetalleSolFacturacionEquipos);
                    $objInfoDetalleSolFactHistorial->setEstado($objDetalleSolFacturacionEquipos->getEstado());
                    $objInfoDetalleSolFactHistorial->setFeCreacion(new \DateTime('now'));
                    $objInfoDetalleSolFactHistorial->setUsrCreacion($strUsrCreacion);
                    $objInfoDetalleSolFactHistorial->setObservacion($objDetalleSolFacturacionEquipos->getObservacion() .
                                                                    " | Se crea la solicitud con valor 0.");
                    $objInfoDetalleSolFactHistorial->setIpCreacion($strIpCreacion);
                    $this->emcom->persist($objInfoDetalleSolFactHistorial);
                    $this->emcom->flush();
                }
                else
                {
                    $objDetalleSolFacturacionEquipos = $this->emcom->getRepository("schemaBundle:InfoDetalleSolicitud")
                                                            ->findOneById($arraySolicitudesFacturacionCreadas["idDetalleSolicitud"]);
                }

                $arrayRespuestaEquipo = $this->getEquipoPorServicioId(array("intServicioId"        => $objInfoServicio->getId(),
                                                                            "intElementoClienteId" => $intElementoClienteId,
                                                                            "strEmpresaCod"        => $strEmpresaCod));
                $intContador          = count($arrayRespuestaEquipo);
                if($intContador != 1)
                {
                    throw new \Exception ('Error al obtener el equipo del elementoClienteId=' . $intElementoClienteId .
                                          " Cantidad de registros = " . $intContador);
                }

                //Se obtiene los elementos dependientes según el parámetro VALOR4 = 'D' y la tecnología del equipo principal.
                $objDQL            = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getDql('RETIRO_EQUIPOS_SOPORTE',
                                                       'FINANCIERO',
                                                       'FACTURACION_RETIRO_EQUIPOS',
                                                       null,
                                                       $arrayRespuestaEquipo[0]["strNombreMarcaElemento"] ,
                                                       null,
                                                       null,
                                                       'D',
                                                       null,
                                                       strval($strEmpresaCod));
                $arrayParamtroDet       = $objDQL->getResult();
                foreach($arrayParamtroDet as $arrayDetalle)
                {
                    $intIndice = count($arrayRespuestaEquipo);
                    $arrayRespuestaEquipo[$intIndice]["strNombreTipoElemento"]  = $arrayDetalle["descripcion"];
                    $arrayRespuestaEquipo[$intIndice]["strNombreMarcaElemento"] = $arrayDetalle["valor1"];
                    $arrayRespuestaEquipo[$intIndice]["floatPrecio"]            = $arrayDetalle["valor2"];
                    $arrayRespuestaEquipo[$intIndice]["intCaracteristicaId"]    = $arrayDetalle["valor3"];
                }

                //Se iteran todos los equipos a facturar en la presente solicitud de retiro de equipo.
                //Los equipos incluyen Dependientes e independientes.
                foreach($arrayRespuestaEquipo as $intIndice=>$arrayValorRespuestaEquipo)
                {
                    //Se incrementa el valor de la solicitud
                    $floatValorAFacturar = $objDetalleSolFacturacionEquipos->getPrecioDescuento() + floatval($arrayValorRespuestaEquipo["floatPrecio"]);
                    if ($arrayValorRespuestaEquipo["floatPrecio"] > 0)
                    {
                        $objDetalleSolFacturacionEquipos->setPrecioDescuento($floatValorAFacturar);
                        $this->emcom->persist($objDetalleSolFacturacionEquipos);
                        $this->emcom->flush();

                        //Se inserta la característica por la solicitud origen.
                        $objAdmiCaracteristicaSolicitud = $this->emcom
                                                               ->getRepository("schemaBundle:AdmiCaracteristica")
                                                               ->findOneBy
                                                                (
                                                                    array("descripcionCaracteristica" => self::SOLICITUD_CAMBIO_DE_MODEM_INMEDIATO,
                                                                          "estado"                    => "Activo")
                                                                );

                        //Se inserta la característica por el equipo
                        $objSolCaractSolicitud = new InfoDetalleSolCaract();
                        $objSolCaractSolicitud->setCaracteristicaId($objAdmiCaracteristicaSolicitud);
                        $objSolCaractSolicitud->setDetalleSolicitudId($objDetalleSolFacturacionEquipos);
                        $objSolCaractSolicitud->setValor($objInfoDetalleSolicitudOrigen->getId());
                        $objSolCaractSolicitud->setEstado("Activo");
                        $objSolCaractSolicitud->setUsrCreacion($strUsrCreacion);
                        $objSolCaractSolicitud->setFeCreacion(new \DateTime('now'));
                        $this->emcom->persist($objSolCaractSolicitud);
                        $this->emcom->flush();

                        //Se obtiene la característica asociada al equipo.
                        $objAdmiCaracteristicaEquipo = $this->emcom->getRepository("schemaBundle:AdmiCaracteristica")
                                                            ->findOneById($arrayValorRespuestaEquipo["intCaracteristicaId"]);

                        //Se inserta la característica por el equipo
                        $objSolCaractEquipo = new InfoDetalleSolCaract();
                        $objSolCaractEquipo->setCaracteristicaId($objAdmiCaracteristicaEquipo);
                        $objSolCaractEquipo->setDetalleSolicitudId($objDetalleSolFacturacionEquipos);
                        $objSolCaractEquipo->setValor($arrayValorRespuestaEquipo["floatPrecio"]);
                        $objSolCaractEquipo->setEstado("Facturable");
                        $objSolCaractEquipo->setUsrCreacion($strUsrCreacion);
                        $objSolCaractEquipo->setFeCreacion(new \DateTime('now'));
                        $objSolCaractEquipo->setDetalleSolCaractId($objSolCaractSolicitud->getId());
                        $this->emcom->persist($objSolCaractEquipo);
                        $this->emcom->flush();

                        //Se inserta el historial de la solicitud por el cambio de valor.
                        $objInfoDetalleSolFactHistorial = new InfoDetalleSolHist();
                        $objInfoDetalleSolFactHistorial->setDetalleSolicitudId($objDetalleSolFacturacionEquipos);
                        $objInfoDetalleSolFactHistorial->setEstado($objDetalleSolFacturacionEquipos->getEstado());
                        $objInfoDetalleSolFactHistorial->setFeCreacion(new \DateTime('now'));
                        $objInfoDetalleSolFactHistorial->setUsrCreacion($strUsrCreacion);
                        $objInfoDetalleSolFactHistorial->setObservacion("Se incrementa el valor de la solicitud en $" .
                                                                        $arrayValorRespuestaEquipo["floatPrecio"] .
                                                                        " por " . $arrayValorRespuestaEquipo["strNombreTipoElemento"] .
                                                                        " " . $arrayValorRespuestaEquipo["strNombreMarcaElemento"]);
                        $objInfoDetalleSolFactHistorial->setIpCreacion($strIpCreacion);
                        $this->emcom->persist($objInfoDetalleSolFactHistorial);
                        $this->emcom->flush();
                    }
                }
                //Si el total a facturar es mayor a 0, se agrega la característica de facturación detallada.
                if ($floatValorAFacturar > 0)
                {
                    $objAdmiCaracteristicaFactDet   = $this->emcom
                                                           ->getRepository("schemaBundle:AdmiCaracteristica")
                                                           ->findOneBy(array("descripcionCaracteristica" => "FACTURACION DETALLADA",
                                                                             "estado"                    => "Activo"));
                    //Se inserta la característica por FACTURACION DETALLADA
                    $objSolCaractFactDet = new InfoDetalleSolCaract();
                    $objSolCaractFactDet->setCaracteristicaId($objAdmiCaracteristicaFactDet);
                    $objSolCaractFactDet->setDetalleSolicitudId($objDetalleSolFacturacionEquipos);
                    $objSolCaractFactDet->setValor("S");
                    $objSolCaractFactDet->setEstado("Activo");
                    $objSolCaractFactDet->setUsrCreacion($strUsrCreacion);
                    $objSolCaractFactDet->setFeCreacion(new \DateTime('now'));
                    $this->emcom->persist($objSolCaractFactDet);
                    $this->emcom->flush();
                }
            }

            $this->emcom->getConnection()->commit();
            $arrayRespuesta["strMensaje"] = null;
            $arrayRespuesta["strEstado"]  = "OK";
        }
        catch(\Exception $objException)
        {
            if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                            'InfoElementoWifiService.creaSolicitudFacturacionEquipo', 
                            "Error al crear la " . self::SOLICITUD_FACTURACION_RETIRO_EQUIPO . ": " . $objException->getMessage(),
                            $strUsrCreacion, 
                            $strIpCreacion);
            $arrayRespuesta["strMensaje"] = $objException->getMessage();
            $arrayRespuesta["strEstado"]  = "ERROR";
            $this->serviceEnvioPlantilla->generarEnvioPlantilla("ERROR - Cambio de Módem inmediato",
                                                                null,
                                                                "FACTEQ",
                                                                array("strMensaje"           => $arrayRespuesta["strMensaje"],
                                                                      "strLogin"             => $strLogin,
                                                                      "intServicioId"        => $objInfoServicio->getId(),
                                                                      "intElementoClienteId" => $intElementoClienteId,
                                                                      "strEmpresaCod"        => $strEmpresaCod,
                                                                      "strUsrCreacion"       => $strUsrCreacion,
                                                                      "strIpCreacion"        => $strIpCreacion),
                                                                $strEmpresaCod,
                                                                null,
                                                                null);
        }
        return $arrayRespuesta;
    }

    /**
     * Obtiene las características y su valor de la SOLICITUD FACTURACION RETIRO EQUIPO que esté ligada a una SOLICITUD CAMBIO DE MODEM INMEDIATO<br>
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0
     * @since 29-10-2018
     * @param array $arrayParametros Parámetros del query (intServicioId)
     * @return array Devuelve un array con la siguiente información:<br>
     *               ['boolFacturacionEquipos'] => Si existe uno o más equipos a facturar por el servicio [true].
     *               ['arrayEquiposFacturados'] => Arreglo de equipos a facturar.
     *               ['floatTotalEquipos']      => Valor total a facturar por el/los equipo/s.
     */
    public function buscaInformacionSolicitudCambioModemPorFacturar($arrayParametros)
    {
        $arrayEquiposFacturados = array();
        $boolFacturacionEquipos = false;
        $floatValorTotal        = 0;
        $intServicioId          = $arrayParametros['intServicioId'];

        $arrayEquiposAFacturar = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                      ->obtieneSolicitudesCambioModemPorFacturar(array("intServicioId" => $intServicioId));

        //Se busca si no tiene una solicitud de facturación de equipos asociada en estado finalizada.
        if (!empty($arrayEquiposAFacturar))
        {
            $boolFacturacionEquipos = true;
            foreach ($arrayEquiposAFacturar as $arrayEquipos)
            {
                $floatValorTotal         += floatval($arrayEquipos["strValor"]);
                $arrayEquiposFacturados[] = $arrayEquipos["strDescripcionCaracteristica"];
            }
        }
        return array("boolFacturacionEquipos" => $boolFacturacionEquipos,
                     "floatTotalEquipos"      => $floatValorTotal,
                     "arrayEquiposFacturados" => $arrayEquiposFacturados);
    }

    /**
     * Función que selecciona el equipo a facturar según el servicioId y el elementoClienteid.<br>
     * Si el servicio técnico es ultima milla Cobre, selecciona por defecto CPE ADSL.<br>
     * Si el servicio es de un producto adicional Renta SMARTWIFI, se busca si es CPE HUAWEI o TELLION sino  se selecciona por defecto SMARTWIFI.<br>
     * Si el servicio es cualquier otro producto, se devuelve un arreglo vacío.<br>
     * Si el servicio no tiene un elementoClienteId, se devuelve un arreglo vacío.<br>
     * Si no cae en ningún caso anterior y tiene elementoClienteId, se busca la marca y el modelo para obtener equipos Huawei y Tellion.<br>
     * @author Luis Cabrera
     * @version 1.0
     * @since 03-10-2018
     */
    public function getEquipoPorServicioId($arrayParametros)
    {
        if(!isset($arrayParametros["intServicioId"]) || !$arrayParametros["intServicioId"] > 0)
        {
            throw new \Exception("El servicio proporcionado es incorrecto.");
        }

        //Si la ULTIMA_MILLA_ID de la INFO_SERVICIO_TECNICO es COBRE, se factura por equipo ADSL.
        $objInfoServicioTecnico = $this->emcom->getRepository("schemaBundle:InfoServicioTecnico")
                                       ->findOneBy(array("servicioId" => $arrayParametros["intServicioId"]));
        $objUltimaMilla         = $this->emcom->getRepository("schemaBundle:AdmiTipoMedio")
                                       ->findOneBy(array("codigoTipoMedio" => 'CO',
                                                         "estado"          => 'Activo'));
        if (intval($objInfoServicioTecnico->getUltimaMillaId()) == intval($objUltimaMilla->getId()))
        {
            $objDQL                 = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getDql('RETIRO_EQUIPOS_SOPORTE',
                                                    'FINANCIERO',
                                                    'FACTURACION_RETIRO_EQUIPOS',
                                                    'CPE ADSL',
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    null,
                                                    strval($arrayParametros["strEmpresaCod"]));
            $arrayParamtroDet       = $objDQL->getOneOrNullResult();
            $arrayRespuestaEquipo   = array();
            $arrayRespuestaEquipo[0]["strNombreTipoElemento"]  = $arrayParamtroDet["descripcion"];
            $arrayRespuestaEquipo[0]["strNombreMarcaElemento"] = $arrayParamtroDet["valor1"];
            $arrayRespuestaEquipo[0]["floatPrecio"]            = $arrayParamtroDet["valor2"];
            $arrayRespuestaEquipo[0]["intCaracteristicaId"]    = $arrayParamtroDet["valor3"];
        }
        else
        {
            //Se obtiene si el servicio es un producto adicional SMARTWIFI de Renta
            $objInfoServicio = $this->emcom->getRepository("schemaBundle:InfoServicio")->findOneById($arrayParametros["intServicioId"]);
            if (!is_null($objInfoServicio->getProductoId()) && 'SMARTWIFI' == $objInfoServicio->getProductoId()->getNombreTecnico() && 
                strpos(strtoupper($objInfoServicio->getProductoId()->getDescripcionProducto()), strtoupper('Renta')) !== false)
            {
                //Primero se verifica si el equipo es TELLION o HUAWEI
                if (intval($arrayParametros["intElementoClienteId"])>0)
                {
                    $arrayRespuestaEquipo  = $this->emcom
                                                  ->getRepository("schemaBundle:AdmiTipoElemento")
                                                  ->getTipoElementoPorElementoClienteId($arrayParametros);
                    if (count($arrayRespuestaEquipo)> 0)
                    {
                        //Si existe un equipo relacionado se devuelve el equipo.
                        return $arrayRespuestaEquipo;
                    }
                }
                //Si no existe un equipo relacionado, se carga por defecto SMARTWIFI de la plantilla de equipos.
                $objDQL                 = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                               ->getDql('RETIRO_EQUIPOS_SOPORTE',
                                                        'FINANCIERO',
                                                        'FACTURACION_RETIRO_EQUIPOS',
                                                        'SMARTWIFI',
                                                        null,
                                                        null,
                                                        null,
                                                        null,
                                                        null,
                                                        strval($arrayParametros["strEmpresaCod"]));
                $arrayParamtroDet       = $objDQL->getOneOrNullResult();
                $arrayRespuestaEquipo   = array();
                $arrayRespuestaEquipo[0]["strNombreTipoElemento"]  = $arrayParamtroDet["descripcion"];
                $arrayRespuestaEquipo[0]["strNombreMarcaElemento"] = $arrayParamtroDet["valor1"];
                $arrayRespuestaEquipo[0]["floatPrecio"]            = $arrayParamtroDet["valor2"];
                $arrayRespuestaEquipo[0]["intCaracteristicaId"]    = $arrayParamtroDet["valor3"];
            }
            else
            {
                //Si es otro producto que no sea SMARTWIFI Renta
                if($objInfoServicio->getProductoId())
                {
                    return array();
                }
                //Si el elementoClienteId es vacío.
                if(!intval($arrayParametros["intElementoClienteId"])>0)
                {
                    //Si es plan y no tiene elementoClienteId
                    //Se devuelve un arreglo vacío.
                    return array();
                }
                //Si no es ADSL o Producto adicional SMARTWIFI, se busca en la plantilla según su tecnología y modelo.
                $arrayRespuestaEquipo  = $this->emcom
                                              ->getRepository("schemaBundle:AdmiTipoElemento")
                                              ->getTipoElementoPorElementoClienteId($arrayParametros);
            }
        }
        return $arrayRespuestaEquipo;
    }

    /**
     * 
     * @param type $arrayParametros
     * @return \telconet\comercialBundle\Service\InfoDetalleSolCaract
     */
    public function creaObjetoInfoDetalleSolCaract($arrayParametros)
    {
        $objInfoDetalleSolCaract = new InfoDetalleSolCaract();
        $objInfoDetalleSolCaract->setCaracteristicaId($arrayParametros['entityAdmiCaracteristica']);
        $objInfoDetalleSolCaract->setValor($arrayParametros['strValor']);
        $objInfoDetalleSolCaract->setDetalleSolicitudId($arrayParametros['entityInfoDetalleSolicitud']);
        $objInfoDetalleSolCaract->setEstado($arrayParametros['strEstado']);
        $objInfoDetalleSolCaract->setUsrCreacion($arrayParametros['strUsrCreacion']);
        $objInfoDetalleSolCaract->setFeCreacion($arrayParametros['objFecha']);
        return $objInfoDetalleSolCaract;
    }
    
    /**    
     * Documentación para el método 'actualizaSolicitud'.
     *
     * Descripcion: Función que permite actualizar el estado de una solicitud y crea el respectivo historial.
     * 
     * @author  Edgar Holguín <eholguin@telconet.ec>
     * @version 1.0 04-03-2017     
     * 
     * @param array $arrayParametros[strObservacion (string)   =>    observacion de actualización de la solicitud.
     *                               intSolicitudId (integer)  =>    id de solicitud que se desea actualizar.
     *                               strUsrCreacion (string)   =>    Usuario de creacion 
     *                               strIpCreacion  (string)   =>    IP de creacion 
     *                               strEstado      (string)   =>    Nuevo estado de la solicitud]
     * 
     * @return obj json_encode (objeto respuesta json_encode)
     */
    public function actualizaSolicitud($arrayParametros)
    {
        $strObservacion   = $arrayParametros['strObservacion'];
        $intIdSolicitud   = $arrayParametros['intIdSolicitud'];
        $strIp            = $arrayParametros['strIpCreacion'];
        $strUsrCreacion   = $arrayParametros['strUsrCreacion'];
        $strEstado        = $arrayParametros['strEstado'];
        
        $this->emcom->getConnection()->beginTransaction();
        try
        {
     
            $objInfoDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            
            if (is_object($objInfoDetalleSolicitud) )
            {
                $objInfoDetalleSolicitud->setEstado($strEstado);
                $this->emcom->persist($objInfoDetalleSolicitud);
                $this->emcom->flush();

                //Grabamos en la tabla de historial de la solicitud
                $objInfoDetalleSolHist= new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setEstado($strEstado);
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setIpCreacion($strIp);
                $objInfoDetalleSolHist->setObservacion($strObservacion);
                $this->emcom->persist($objInfoDetalleSolHist);
                $this->emcom->flush();
                $this->emcom->getConnection()->commit();   
            }


            $objRespuesta=json_encode(array('strRespuesta' => "OK"));            
       }       
       catch (\Exception $e) 
       {
           $objRespuesta=json_encode(array('strRespuesta' => "ERROR"));
       }
       return $objRespuesta;        
    }    
    
    /**    
     * Documentación para el método 'creaSolicitudCambioFacturacion'.
     *
     * Descripcion: Función que permite crear la solicitud con sus respectivas caracteristicase historial para el proceso de cambio de facturación.
     * 
     * @author  David Leon <mdleon@telconet.ec>
     * @version 1.0 27-01-2021     
     * 
     * @param array $arrayParametros[strFactura         (string)   =>    Numero de factura.
     *                               objMotivo          (object)   =>    Objeto del motivo.
     *                               objTipoSolicitud   (object)   =>    Objeto del tipo de solicitud. 
     *                               objcaracNumeroFac  (object)   =>    Objeto de la característica.
     *                               objcaracTipoFac    (object)   =>    Objeto de la característica.
     *                               objCaracVendedor   (object)   =>    Objeto de la característica.
     *                               strVendedor        (string)   =>    Nombre del vendedor.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strTipoFac         (string)   =>    Mrc o Nrc.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strPrefijoEmpresa  (string)   =>    Prefijo de la empresa.
     *                               intEmpresaId       (integer)   =>   Id de le empresa.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     *                               
     * @return array $arrayRespuesta
     */
    public function creaSolicitudCambioFacturacion($arrayParametros)
    {
        $strFactura       = $arrayParametros['strFactura']; 
        $objMotivo        = $arrayParametros['objMotivo']; 
        $objTipoSolicitud = $arrayParametros['objTipoSolicitud'];
        $objCaracNumeroFac= $arrayParametros['objcaracNumeroFac'];
        $objCaracTipoFac  = $arrayParametros['objcaracTipoFac'];
        $objCaracVendedor = $arrayParametros['objCaracVendedor'];
        $strVendedor      = $arrayParametros['strVendedor'];
        $strObservacion   = $arrayParametros['strObservacion'];
        $strTipoFac       = $arrayParametros['strTipoFac'];  
        $strEstado        = $arrayParametros['strEstado'];
        $strPrefijoEmpresa= $arrayParametros['strPrefijoEmpresa'];
        $intEmpresaId     = $arrayParametros['intEmpresaId'];
        $strUsrCreacion   = $arrayParametros['strUsrCreacion'];
        $strIpCreacion    = $arrayParametros['strIpCreacion'];
        $strObservacionHis= 'Se crea la solicitud para el cambio en el tipo de facturación';
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            $objDetalleSolic = new InfoDetalleSolicitud();
            $objDetalleSolic->setMotivoId($objMotivo->getId());
            $objDetalleSolic->setTipoSolicitudId($objTipoSolicitud);
            $objDetalleSolic->setObservacion($strObservacion);
            $objDetalleSolic->setFeCreacion(new \DateTime('now'));
            $objDetalleSolic->setUsrCreacion($strUsrCreacion);
            $objDetalleSolic->setEstado($strEstado);
            $this->emcom->persist($objDetalleSolic);
            $this->emcom->flush();
            
            $objSolCaractFactNum = new InfoDetalleSolCaract();
            $objSolCaractFactNum->setCaracteristicaId($objCaracNumeroFac);
            $objSolCaractFactNum->setDetalleSolicitudId($objDetalleSolic);
            $objSolCaractFactNum->setValor($strFactura);
            $objSolCaractFactNum->setEstado($strEstado);
            $objSolCaractFactNum->setUsrCreacion($strUsrCreacion);
            $objSolCaractFactNum->setFeCreacion(new \DateTime('now'));
            $this->emcom->persist($objSolCaractFactNum);
            $this->emcom->flush();
            
            $objSolCaractFactTip = new InfoDetalleSolCaract();
            $objSolCaractFactTip->setCaracteristicaId($objCaracTipoFac);
            $objSolCaractFactTip->setDetalleSolicitudId($objDetalleSolic);
            $objSolCaractFactTip->setValor($strTipoFac);
            $objSolCaractFactTip->setEstado($strEstado);
            $objSolCaractFactTip->setUsrCreacion($strUsrCreacion);
            $objSolCaractFactTip->setFeCreacion(new \DateTime('now'));
            $this->emcom->persist($objSolCaractFactTip);
            $this->emcom->flush();
            
            $objSolCaractFactVen = new InfoDetalleSolCaract();
            $objSolCaractFactVen->setCaracteristicaId($objCaracVendedor);
            $objSolCaractFactVen->setDetalleSolicitudId($objDetalleSolic);
            $objSolCaractFactVen->setValor($strVendedor);
            $objSolCaractFactVen->setEstado($strEstado);
            $objSolCaractFactVen->setUsrCreacion($strUsrCreacion);
            $objSolCaractFactVen->setFeCreacion(new \DateTime('now'));
            $this->emcom->persist($objSolCaractFactVen);
            $this->emcom->flush();
            
            $objDetalleSolHistorial = new InfoDetalleSolHist();
            $objDetalleSolHistorial->setEstado($strEstado);
            $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSolic);
            $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
            $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
            $objDetalleSolHistorial->setObservacion($strObservacionHis);
            $objDetalleSolHistorial->setIpCreacion($strIpCreacion);
            $this->emcom->persist($objDetalleSolHistorial);
            $this->emcom->flush();
            
            $this->emcom->getConnection()->commit();

            $strStatus = "OK"; 
        }       
        catch (\Exception $e) 
        {
           $strStatus = "ERROR";
           $strMensaje = $e->getMessage();
           if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
           
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**    
     * Documentación para el método 'creaSolicitudCambioFacturacion'.
     *
     * Descripcion: Función que permite crear la solicitud con sus respectivas caracteristicase historial para el proceso de cambio de facturación.
     * 
     * @author  David Leon <mdleon@telconet.ec>
     * @version 1.0 27-01-2021     
     * 
     * @param array $arrayParametros[intIdSolicitud     (integer)  =>    Numero de solicitud.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     *                               
     * @return array $arrayRespuesta
     */
    public function actualizarSolicitudFact($arrayParametros)
    {
        $strObservacion      = $arrayParametros['strObservacion'];
        $intIdSolicitud      = $arrayParametros['intIdSolicitud'];
        $strIp               = $arrayParametros['strIpCreacion'];
        $strUsrCreacion      = $arrayParametros['strUsrCreacion'];
        $strEstado           = $arrayParametros['strEstado'];
        
        $this->emcom->getConnection()->beginTransaction();
        try
        {
     
            $objInfoDetalleSolicitud = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);
            
            if (is_object($objInfoDetalleSolicitud) )
            {
                $objInfoDetalleSolicitud->setEstado($strEstado);
                $this->emcom->persist($objInfoDetalleSolicitud);
                $this->emcom->flush();

                //Grabamos en la tabla de historial de la solicitud
                $objInfoDetalleSolHist= new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setEstado($strEstado);
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setIpCreacion($strIp);
                $objInfoDetalleSolHist->setObservacion($strObservacion);
                $this->emcom->persist($objInfoDetalleSolHist);
                $this->emcom->flush();
            
            
                $arrayCaracteristica  = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')->findBy(
                                                                                    array('detalleSolicitudId' => $objInfoDetalleSolicitud->getId()));

                if(!empty($arrayCaracteristica) && is_array($arrayCaracteristica))
                {
                    foreach($arrayCaracteristica as $objSolCaract):
                        $objSolCaract->setEstado($strEstado);
                        $objSolCaract->setFeUltMod(new \DateTime('now'));
                        $objSolCaract->setUsrUltMod($strUsrCreacion);
                        $this->emcom->persist($objSolCaract);
                        $this->emcom->flush();
                    endforeach;
                }
            }
            $this->emcom->getConnection()->commit();   
           $strStatus = "OK"; 
        }       
        catch (\Exception $e) 
        {
           $strStatus = "ERROR";
           $strMensaje = $e->getMessage();
           if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
           
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje);
        return $arrayRespuesta;     
    } 
    
    /**    
     * Documentación para el método 'aprobarSolicitudMateriales'.
     *
     * Descripcion: Función que apruba solicitud de excedente de material
     *              actualizando el estado de la solicitud, registra historial
     *              de solicitud y de servicio. Adicional envía mail de notificación
     *              al asesor y crea tarea a factucación para el descuento respectivo.
     * 
     * @author  Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 18-03-2021     
     * 
     * @param array $arrayParametros[intIdSolicitud     (integer)  =>    Numero de solicitud.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     *                               
     * @return array $arrayRespuesta
     *  
     * @author Liseth Candelario. <lcandelario@telconet.ec>
     * @version 1.1 14-11-2021    Se modifica las acciones de autorizar la solicitud
     *                            Se añadió las funciones para PrePlanificar, envío de notificaciones,
     *                            trazabilidad del servicio y de la solicitud .  
     */
    public function aprobarSolicitudMateriales($arrayParametros)
    {
        $strObservacion      = $arrayParametros['strObservacion'];
        $intIdSolicitudExce  = $arrayParametros['idSolicitudExce'];
        $strCodEmpresa       = $arrayParametros['codEmpresa'];
        $strUsuario          = $arrayParametros['usuario'];
        $strIP               = $arrayParametros['ip'];
        $intIdServicio       = $arrayParametros['intIdServicio'];
        $serviceAutorizacion = $this->serviceAutorizaciones;
        $floatSumaExcedente             = 0;
        $floatPrecioFibra               = 0;
        $floatPrecioObraCivil           = 0;
        $floatPrecioOtrosMate           = 0;
        $floatPorcentajeCanceladoCliente= 0;
        $floatPrecioAsumeCliente        = 0;
        $floatPrecioAsumeEmpresa        = 0;
        $boolFactura                    = false;
        $boolTrazabilidadSolicitud      = false;
        $strAccion                      = 'aprobarExcedenteMaterial';
        $boolBuscaValores               = false;
        $boolPrePlanifica               = false;

        try
        {
            $this->emcom->getConnection()->beginTransaction();
            $entityTipoSolicitudPla     = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud') 
                                        ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

            $objServicio                 = $this->emcom->getRepository('schemaBundle:InfoServicio')
                                            ->findOneById(array("id"       => $intIdServicio));
            
            $strEstadoServicio            = $objServicio->getEstado();

            $objInfoSolicitudExcedente    = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud') 
                                            ->findOneById(array("id"       => $intIdSolicitudExce));
                                            
            $entityTipoSolicitudFactibilidad = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud') 
                                                           ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");

            $objDetalleSolicitudFactibilidad = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findOneBy(array( "servicioId" => $objServicio->getId()  
                                                                ,"tipoSolicitudId" => $entityTipoSolicitudFactibilidad->getId()  ));
                                        
            //Obtenemos la forma de contacto del creador del servicio
            $arrayFormasContactoAsistente = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
                                                    ->getUsrCreacion(),'Correo Electronico');
        
            //Obtenemos la forma de contacto del aseso LOGIN_AUX
            $arrayFormasContactoAsesor = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
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

            $objRol                  = $this->emcom->getRepository('schemaBundle:AdmiRol')
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
            if(is_object($objDetalleSolicitudFactibilidad))
            {
                $intIdSolicitudFactibilidad = $objDetalleSolicitudFactibilidad->getId();
            }
            
            if(is_object($objServicio) && !empty($objServicio) &&is_object($entityTipoSolicitudPla) && !empty($entityTipoSolicitudPla)
                && is_object($objInfoSolicitudExcedente) && !empty($objInfoSolicitudExcedente))
            {
                //datos de la SOLICITUD DE PLANIFICACION del servicio
                $objDetalleSolicitudPla = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud') 
                                      ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                         "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));

                $strAsunto = "Notificación en Autorización de excedente de material | "
                            . "login: " . $objServicio->getPuntoId()->getLogin();
            }
            else
            {
                throw new \Exception("El servicio proporcionado es incorrecto.");
            }
            
            $objCaracteristicaFibra             = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD PRECIO');
            $objCaracteristicaOCivil            = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
            $objCaracteristicaOtrosMate         = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
            $objCaracteristicaCanceladoCliente  = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneByDescripcionCaracteristica('COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE');

            if(is_object(!$objCaracteristicaCanceladoCliente))
            {
                throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE</b>');
            }
            $objCaracteristicaAsumeCliente      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('COPAGOS ASUME EL CLIENTE PRECIO');   
            if(is_object(!$objCaracteristicaAsumeCliente))
            {
                throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME EL CLIENTE PRECIO</b>');
            }
            $objCaracteristicaAsumeEmpresa      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneByDescripcionCaracteristica('COPAGOS ASUME LA EMPRESA PRECIO');
            if(is_object(!$objCaracteristicaAsumeEmpresa))
            {
                throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME LA EMPRESA PRECIO</b>');
            }

            if(is_object($objDetalleSolicitudPla))
            {
                $intIdDetalleSolicitud              = $objDetalleSolicitudPla->getId();
                
                if(is_object($objCaracteristicaFibra)  && !empty($objCaracteristicaFibra) &&
                is_object($objCaracteristicaOCivil) && !empty($objCaracteristicaOCivil) &&
                is_object($objCaracteristicaOtrosMate) && !empty($objCaracteristicaOtrosMate))
                {
                    $objInfoDetalleSolCaractFibra = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla ));
                    $objInfoDetalleSolCaractOCivil = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla ));
                    $objInfoDetalleSolCaractOtrosMate = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMate,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla  ));
                    $objInfoDetalleSolCaractCanceladoCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla ));
                    $objInfoDetalleSolCaractAsumeCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla  ));
                    $objInfoDetalleSolCaractAsumeEmpresa = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla  ));
                    $objParametroCabCodigo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array("descripcion"=>'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN', 
                                                                                "modulo"=>'COMERCIAL',
                                                                                "estado"=>'Activo'));
                    //Variable del código del material para insertar a la infodetalleSolMaterial.
                    if(is_object($objParametroCabCodigo))
                    {
                        $objParamDetCodigo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->findOneBy(array("descripcion" => 'CODIGO DE MATERIAL DE FIBRA OPTICA',
                                                                            "parametroId" => $objParametroCabCodigo->getId(),
                                                                            "estado"      => 'Activo'));            
                        $strCodigoMaterial  = $objParamDetCodigo->getValor1();
                    }
                    else
                    {
                        throw new \Exception(': NO SE ENCONTRÓ UN PARÁMETRO <br> <b>INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN</b>');
                    }
                
                    if(is_object($objInfoDetalleSolCaractFibra) && !empty($objInfoDetalleSolCaractFibra) &&
                    is_object($objInfoDetalleSolCaractOCivil) && !empty($objInfoDetalleSolCaractOCivil) )
                    {
                        $floatPrecioFibra            =  floatval($objInfoDetalleSolCaractFibra->getValor());
                        $floatPrecioObraCivil        =  floatval($objInfoDetalleSolCaractOCivil->getValor());
                        $floatPrecioOtrosMate        =  floatval($objInfoDetalleSolCaractOtrosMate->getValor());
                        $floatSubTotalOtrosClientes  =  $floatPrecioFibra + $floatPrecioObraCivil + $floatPrecioOtrosMate;
                        $floatPorcentajeCanceladoCliente   =  floatval($objInfoDetalleSolCaractCanceladoCliente->getValor());
                        $floatPrecioAsumeCliente           =  floatval($objInfoDetalleSolCaractAsumeCliente->getValor());
                        $floatPrecioAsumeEmpresa           =  floatval($objInfoDetalleSolCaractAsumeEmpresa->getValor());
                        $floatTotalPagar                   =  $floatSubTotalOtrosClientes;
                        if($floatPorcentajeCanceladoCliente != 0)
                        {
                            $floatPorcentajeEmpresa            = 100 - $floatPorcentajeCanceladoCliente;
                            $floatTotalPagar                   = $floatPrecioAsumeCliente ;
                        }
                        else
                        {
                            $floatPorcentajeEmpresa            = 0;
                        }

                        $strTablaDeValores = '<br/> <table width="100%" cellspacing="4" cellpadding="4">
                                                <tr> <td colspan="4"><b>*Valores de excedente de materiales*</b></td></tr>
                                                <tr> <td colspan="4"><b>Valores de Otros Clientes:</b></td></tr>
                                                <tr><td>Precio de Fibra            </td><td>$'.+$floatPrecioFibra.            '</td> </tr>
                                                <tr><td>Precio de Obra Civil       </td> <td>$'.+$floatPrecioObraCivil.       '</td> </tr>
                                                <tr><td>Precio de Otros Materiales </td> <td>$'.+$floatPrecioOtrosMate .      '</td> </tr>
                                                <tr><td>Subtotal de Otros Clientes </td> <td><b>$'.+$floatSubTotalOtrosClientes.'<b></td> </tr> 
                                                <tr> <td colspan="3"><b>Valores de COPAGOS:</b></td></tr>
                                            <tr><td>% Cliente                     </td> <td>'.+$floatPorcentajeCanceladoCliente .'%</td> </tr>
                                            <tr><td>% Empresa                     </td> <td>'.+$floatPorcentajeEmpresa          .'% </td> </tr>
                                                <tr><td>Cliente cancela         </td> <td>$'.+$floatPrecioAsumeCliente       .'</td>  </tr>
                                                <tr><td>Empresa cancela         </td> <td>$'.+$floatPrecioAsumeEmpresa       .'</td>  </tr>
                                                <tr> <td colspan="1"><b>TOTAL:</b></td><td><b>$'.+$floatTotalPagar     .'<b></td></tr>
                                        </table> <br/>';
                        $strMail         = 'Solicitud Aprobada por GTN!.
                                            <br/>'  .'<b>Usuario que ejecutó la acción '.$strUsuario.'</b> <br/>'.
                                            ' con la siguiente observación: << '. $strObservacion . ' >>.'
                                            .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'.$strTablaDeValores ;

                        // Copagos en GTN.
                        if($floatPorcentajeCanceladoCliente!=0)
                        {
                            $strRespuesta     = 'Solicitud Aprobada!.<br/>' .
                            'Se validó la solicitud Excedentes de Materiales <br/> por Copagos, por el valor de $'.$floatPrecioAsumeEmpresa
                                                .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                                .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                                .' <b>Observación: </b> '.$strObservacion.' <br/>';

                            $strSeguimiento  = 'Solicitud Aprobada!.<br/>'.
                                    'Se validó la solicitud Excedentes de Materiales <br/> por Copagos, por el valor de $'.$floatPrecioAsumeEmpresa
                                                .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                                .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                                .' <b>Observación: </b> '.$strObservacion.' <br/>';
                            
                                $boolFactura           = true;
                                $boolPrePlanifica      = true;
                        }
                        else
                        {
                                $strRespuesta   = 'Solicitud Aprobada!.<br/>' .
                                'Se validó la solicitud Excedentes de Materiales, por el valor de $'.$floatTotalPagar
                                .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                .'<br/><b>#Solicitud Excedente</b>'.$intIdSolicitudExce.'<br/>'.'<b>Observación:</b>'.$strObservacion.'<br/>';

                                $strSeguimiento = 'Solicitud Aprobada por GTN!.<br/>'  .
                                'Se validó la solicitud Excedentes de Materiales, <br/>
                                por el valor de $'.$floatTotalPagar .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                .' <b>Observación: </b> '.$strObservacion.' <br/>';

                                $boolPrePlanifica      = true;
                        }

                        if($boolPrePlanifica)
                        {
                            if($strEstadoServicio !== 'Anulado') 
                            {
                                $strEstadoEnviado = "PrePlanificada";
                                $arrayParametrosPrePla = array(
                                                    "emComercial"                => $this->emcom,
                                                    "strEstadoEnviado"           => $strEstadoEnviado,
                                                    "objServicio"                => $objServicio,
                                                    "strClienteIp"               => $strIP,
                                                    "strUsrCreacion"             => $strUsuario);
                                $arrayVerificar = $serviceAutorizacion ->registroEstadoPrePlanificadaInfoDetalleSolicitud($arrayParametrosPrePla);
                                if($arrayVerificar['status'] == 'ERROR' )
                                {
                                throw new \Exception(': EN: registroEstadoPrePlanificadaInfoDetalleSolicitud 
                                                <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                                }
                            }
                        }
                        $strEstadoEnviado = "Aprobado";
                        $arrayParametrosInfoDetSol = array(
                                        "emComercial"               => $this->emcom,
                                        "objServicio"               => $objServicio,
                                        "strEstadoEnviado"          => $strEstadoEnviado,
                                        "strUsrCreacion"            => $strUsuario);
                        $arrayVerificar = $serviceAutorizacion->registroEstadoAprobadoInfoDetalleSolicitud($arrayParametrosInfoDetSol);
                        if($arrayVerificar['status'] == 'ERROR' )
                        {
                            throw new \Exception(': EN: registroEstadoAprobadoInfoDetalleSolicitud <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                        }                    
                        if($boolFactura)
                        {
                                        
                            //formatear a solo dos decimales
                            $floatPrecioAsumeCliente = number_format($floatPrecioAsumeCliente, 2,'.',''); 

                            $intCantidadEstimada    = 1;
                            $strCostoMaterial       = 0;
                            $intCantidadFacturada   = 1;
                            $intCantidadCliente     = 1;
                            $intCantidadUsada       = 0;
                            $strPrecioVentaMaterial = $floatPrecioAsumeCliente;
                            $strValorCobrado        = $floatPrecioAsumeCliente;
                            
                            //SI ES QUE NO HAY ENVÍO NUEVOS VALORES EN INFO DETALLE SOLICITUD MATERIAL
                            $arrayParametrosSolMat = array(
                                                    "emComercial"                => $this->emcom,
                                                    "strClienteIp"               => $strIP,
                                                    "intIdDetalleSolicitud"      => $intIdDetalleSolicitud,
                                                    "strUsrCreacion"             => $strUsuario,
                                                    "strCodigoMaterial"          => $strCodigoMaterial,
                                                    "strCostoMaterial"           => $strCostoMaterial,
                                                    "strPrecioVentaMaterial"     => $strPrecioVentaMaterial,
                                                    "intCantidadEstimada"        => $intCantidadEstimada,
                                                    "intCantidadCliente"         => $intCantidadCliente,
                                                    "intCantidadUsada"           => $intCantidadUsada,
                                                    "intCantidadFacturada"       => $intCantidadFacturada,
                                                    "strValorCobrado"            => $strValorCobrado);
                            $arrayVerificar = $serviceAutorizacion->registroSolicitudMaterial($arrayParametrosSolMat);
                            if($arrayVerificar['status'] == 'ERROR' )
                            {
                                throw new \Exception(': NO SE REALIZÓ: registroSolicitudMaterial<br/><b>'.$arrayVerificar['mensaje'].'</b>');
                            }
                        }
                    }
                }
                else
                {
                    throw new \Exception("Ocurrió un error con las características de la solicitud, alguna de ellas no existen creadas");
                }
           }
           else
           {
               $boolBuscaValores=true;
               //No hay valores con la solicitud de planificaciòn
           }
           // inicio, cuando no hay solicitud de Planificación, se va por los valores de factibilidad
           if(is_object($objDetalleSolicitudFactibilidad) && ($boolBuscaValores))
            {
                $objInfoDetalleSolCaractFibra = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                  'detalleSolicitudId'=>$intIdSolicitudFactibilidad ));
                $objInfoDetalleSolCaractOCivil = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                  'detalleSolicitudId'=>$intIdSolicitudFactibilidad ));
                $objInfoDetalleSolCaractOtrosMate = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMate,
                                                                  'detalleSolicitudId'=>$intIdSolicitudFactibilidad  ));
                $objInfoDetalleSolCaractCanceladoCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                  'detalleSolicitudId'=>$intIdSolicitudFactibilidad ));
                $objInfoDetalleSolCaractAsumeCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                  'detalleSolicitudId'=>$intIdSolicitudFactibilidad  ));
                $objInfoDetalleSolCaractAsumeEmpresa = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                                  'detalleSolicitudId'=>$intIdSolicitudFactibilidad  ));
                $objParametroCabCodigo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                ->findOneBy(array("descripcion"=>'INFORMACIÓN DEL MATERIAL PARA FACTURACIÓN', 
                                                                            "modulo"=>'COMERCIAL',
                                                                            "estado"=>'Activo'));
                
                if(is_object($objInfoDetalleSolCaractFibra) && !empty($objInfoDetalleSolCaractFibra) &&
                is_object($objInfoDetalleSolCaractOCivil) && !empty($objInfoDetalleSolCaractOCivil) )
                {
                    $floatPrecioFibra            =  floatval($objInfoDetalleSolCaractFibra->getValor());
                    $floatPrecioObraCivil        =  floatval($objInfoDetalleSolCaractOCivil->getValor());
                    $floatPrecioOtrosMate        =  floatval($objInfoDetalleSolCaractOtrosMate->getValor());
                    $floatSubTotalOtrosClientes  =  $floatPrecioFibra + $floatPrecioObraCivil + $floatPrecioOtrosMate;
                    $floatPorcentajeCanceladoCliente   =  floatval($objInfoDetalleSolCaractCanceladoCliente->getValor());
                    $floatPrecioAsumeCliente           =  floatval($objInfoDetalleSolCaractAsumeCliente->getValor());
                    $floatPrecioAsumeEmpresa           =  floatval($objInfoDetalleSolCaractAsumeEmpresa->getValor());
                    $floatTotalPagar                   =  $floatSubTotalOtrosClientes ;
                    if($floatPorcentajeCanceladoCliente != 0)
                    {
                        $floatPorcentajeEmpresa            = 100 - $floatPorcentajeCanceladoCliente;
                        $floatTotalPagar                   = $floatPrecioAsumeCliente ;
                    }
                    else
                    {
                        $floatPorcentajeEmpresa            = 0;
                    }                    
                    $strTablaDeValores = '<br/> <table width="100%" cellspacing="4" cellpadding="4">
                                            <tr> <td colspan="4"><b>*Valores de excedente de materiales*</b></td></tr>
                                            <tr> <td colspan="4"><b>Valores de Otros Clientes:</b></td></tr>
                                            <tr><td>Precio de Fibra            </td><td>$'.+$floatPrecioFibra.            '</td> </tr>
                                            <tr><td>Precio de Obra Civil       </td> <td>$'.+$floatPrecioObraCivil.       '</td> </tr>
                                            <tr><td>Precio de Otros Materiales </td> <td>$'.+$floatPrecioOtrosMate .      '</td> </tr>
                                            <tr><td>Subtotal de Otros Clientes </td> <td><b>$'.+$floatSubTotalOtrosClientes.'<b></td> </tr> 
                                            <tr> <td colspan="3"><b>Valores de COPAGOS:</b></td></tr>
                                        <tr><td>% Cliente                     </td> <td>'.+$floatPorcentajeCanceladoCliente .'%</td> </tr>
                                        <tr><td>% Empresa                     </td> <td>'.+$floatPorcentajeEmpresa          .'% </td> </tr>
                                            <tr><td>Cliente cancela         </td> <td>$'.+$floatPrecioAsumeCliente       .'</td>  </tr>
                                            <tr><td>Empresa cancela         </td> <td>$'.+$floatPrecioAsumeEmpresa       .'</td>  </tr>
                                            <tr> <td colspan="1"><b>TOTAL:</b></td><td><b>$'.+$floatTotalPagar     .'<b></td></tr>
                                    </table> <br/>';

                    $strMail         = 'Solicitud Aprobada por GTN!.
                                        <br/>'  .'<b>Usuario que ejecutó la acción '.$strUsuario.'</b> <br/>'.
                                        ' con la siguiente observación: << '. $strObservacion . ' >>.'
                                        .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                        .$strTablaDeValores ;
                    // GTN AUTORIZA el copagos.
                    if($floatPorcentajeCanceladoCliente!=0)
                    {
                        $strRespuesta     = 'Solicitud Aprobada!.<br/>' .
                        'Se validó la solicitud Excedentes de Materiales <br/> por Copagos, por el valor de $'.$floatPrecioAsumeEmpresa
                                            .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                            .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                            .' <b>Observación: </b> '.$strObservacion.' <br/>';

                        $strSeguimiento  = 'Solicitud Aprobada!.<br/>'.
                                'Se validó la solicitud Excedentes de Materiales <br/> por Copagos, por el valor de $'.$floatPrecioAsumeEmpresa
                                            .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                            .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                            .' <b>Observación: </b> '.$strObservacion.' <br/>';
                    }
                    else
                    {                    
                            $strRespuesta   = 'Solicitud Aprobada!.<br/>' .
                            'Se validó la solicitud Excedentes de Materiales, por el valor de $'.$floatTotalPagar
                            .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                            .'<br/><b>#Solicitud Excedente</b>'.$intIdSolicitudExce.'<br/>'.'<b>Observación:</b>'.$strObservacion.'<br/>';

                            $strSeguimiento = 'Solicitud Aprobada por GTN!.<br/>'  .
                            'Se validó la solicitud Excedentes de Materiales, <br/>
                            por el valor de $'.$floatTotalPagar.'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                            .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                            .' <b>Observación: </b> '.$strObservacion.' <br/>';
                    }

                    $strEstadoEnviado = "Aprobado";
                    $arrayParametrosInfoDetSol = array(
                                    "emComercial"               => $this->emcom,
                                    "objServicio"               => $objServicio,
                                    "strEstadoEnviado"          => $strEstadoEnviado,
                                    "strUsrCreacion"            => $strUsuario);
                    $arrayVerificar = $serviceAutorizacion->registroEstadoAprobadoInfoDetalleSolicitud($arrayParametrosInfoDetSol);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                        throw new \Exception(': EN: registroEstadoAprobadoInfoDetalleSolicitud <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }
                }
                else
                {
                    throw new \Exception("<br>Ocurrió un error en el detalle de las características de la solicitud de factibilidad");
                }
           } //fin, cuando no hay solicitud de Planificación, se va por los valores de factibilidad
        

                $strEstadoEnviado = "Aprobado";
                $arrayParametrosTraSol = array(
                                "emComercial"                => $this->emcom,
                                "strClienteIp"               => $strIP,
                                "objDetalleSolicitudExc"     => $objInfoSolicitudExcedente,
                                "strObservacion"             => $strSeguimiento,
                                "strUsrCreacion"             => $strUsuario,
                                "strEstadoEnviado"          => $strEstadoEnviado);
                $arrayVerificar = $serviceAutorizacion->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);  
                if($arrayVerificar['status'] == 'ERROR' )
                {
                    throw new \Exception(': NO SE REALIZÓ: registroTrazabilidadDeLaSolicitud<br><b>'.$arrayVerificar['mensaje'].'</b>');
                }
                $strEstadoEnviado =  $objServicio->getEstado();
                $arrayParametrosTraServ = array(
                                    "emComercial"                => $this->emcom,
                                    "strClienteIp"               => $strIP,
                                    "objServicio"                => $objServicio,
                                    "strSeguimiento"             => $strSeguimiento,
                                    "strUsrCreacion"             => $strUsuario,
                                    "strAccion"                  => $strAccion,
                                    "strEstadoEnviado"           => $strEstadoEnviado );
                $arrayVerificar = $serviceAutorizacion->registroTrazabilidadDelServicio($arrayParametrosTraServ);
                if($arrayVerificar['status'] == 'ERROR' )
                {
                    throw new \Exception(': NO SE REALIZÓ EL PROCESO: registroTrazabilidadDelServicio 
                                            <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                }

                // Cuando GTN autorice la solicitud: deberá llegar una notificación de alerta por aprobación
                $arrayParametrosMail = array( "login"      => $objServicio->getPuntoId()->getLogin(),
                                             "producto"    => $objServicio->getProductoId()->getDescripcionProducto(),
                                             "mensaje"     => $strMail );
                $arrayParametrosNotif = array("strAsunto"                         => $strAsunto,
                                            "arrayParametrosMail"               => $arrayParametrosMail,
                                            "arrayDestinatario"                 => 'Alias',
                                            "strCodEmpresa"                     => $strCodEmpresa,
                                            "serviceEnvioPlantilla"             => $this->serviceEnvioPlantilla,
                                            "arrayFormasContactoAsistente"      => $arrayFormasContactoAsistente,
                                            "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                            "arrayFormasContactoAGtn"           => $arrayFormasContactoAGtn );
                $arrayVerificar = $this->envioDeNotificaciones($arrayParametrosNotif);
                if($arrayVerificar['status'] == 'ERROR' )
                {
                    throw new \Exception(': NO SE REALIZÓ EL PROCESO: envioDeNotificaciones <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                }
            
            if($arrayVerificar['status'] != 'OK')
            {
                throw new \Exception(': NO SE PUDO REALIZAR EL REGISTRO EN LA APROBACIÓN</b>');
            }
            else
            {
                $this->emcom->getConnection()->commit();
                $strStatus = "OK";
            }
        }
        catch (\Exception $e) 
        {
           $strStatus    = "ERROR";
           $strRespuesta = $e->getMessage();
           if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }
            error_log($strRespuesta);
        }
        $arrayRespuesta = array("status"        => $strStatus,
                                "mensaje"       => $strRespuesta);
        return $arrayRespuesta;     
    } 
    
    /**    
     * Documentación para el método 'rechazarSolicitudMateriales'.
     *
     * Descripcion: Función que rechaza solicitud de excedente de material
     *              actualizando el estado de la solicitud, registra historial
     *              de solicitud y de servicio. Adicional envía mail de notificación
     *              al asesor.
     * 
     * @author  Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.0 18-03-2021     
     * 
     * @param array $arrayParametros[idSolicitudExce    (integer)  =>    Numero de solicitud.
     *                               strObservacion     (string)   =>    Observación de la solicitud.
     *                               strEstado          (string)   =>    Estado de inicio.
     *                               strUsrCreacion     (string)   =>    Usuario de creación.
     *                               strIpCreacion      (string)   =>    Ip de creación.
     *                               
     * @return array $arrayRespuesta
     * 
     * @author Liseth Candelario. <lcandelario@telconet.ec>
     * @version 1.1 25-10-2021    Se modifica las acciones de rechazar la solicitud
     *                            Se añadió las funciones para PrePlanificar,, envío de notificaciones,
     *                            Trazabilidad del servicio y de la solicitud .   
     */
    public function rechazarSolicitudMateriales($arrayParametros)
    {
        $strObservacion      = $arrayParametros['strObservacion'];
        $intIdSolicitudExce  = $arrayParametros['idSolicitudExce'];
        $strCodEmpresa       = $arrayParametros['codEmpresa'];
        $strUsuario          = $arrayParametros['usuario'];
        $strIP               = $arrayParametros['ip'];
        $intIdServicio       = $arrayParametros['intIdServicio'];
        $serviceAutorizacion = $this->serviceAutorizaciones;
        $strAccion           = 'rechazoExcedenteMaterial';
        $boolBuscaValores    = false;
        $this->emcom->getConnection()->beginTransaction();

        try
        {
            //datos de la SOLICITUD PLANIFICACION
            $entityTipoSolicitudPla = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud') 
                                   ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

            $objServicio         = $this->emcom->getRepository('schemaBundle:InfoServicio') ->findOneById(array("id" => $intIdServicio));  
                      
            //datos de la solicitud
            $objInfoSolicitudExcedente    = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud') 
                                            ->findOneById(array("id"       => $intIdSolicitudExce));
                                            
            $entityTipoSolicitudFactibilidad = $this->emcom->getRepository('schemaBundle:AdmiTipoSolicitud') 
                                        ->findOneByDescripcionSolicitud("SOLICITUD FACTIBILIDAD");

            $objDetalleSolicitudFactibilidad = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->findOneBy(array( "servicioId" => $objServicio->getId()  
                                                 ,"tipoSolicitudId" => $entityTipoSolicitudFactibilidad->getId()  ));

            //Obtenemos la forma de contacto del creador del servicio
            $arrayFormasContactoAsistente = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
                                                    ->getUsrCreacion(),'Correo Electronico');
        
            //Obtenemos la forma de contacto del aseso 
            $arrayFormasContactoAsesor = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                    ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()
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

            $objRol                  = $this->emcom->getRepository('schemaBundle:AdmiRol')
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
                                                        
            if(is_object($objServicio) && !empty($objServicio) &&
               is_object($entityTipoSolicitudPla) && !empty($entityTipoSolicitudPla)&&
               is_object($objInfoSolicitudExcedente) && !empty($objInfoSolicitudExcedente))
            {
                //datos de la SOLICITUD DE PLANIFICACION del servicio
                $objDetalleSolicitudPla = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud') 
                                      ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                         "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));

                $strAsunto = "Notificación de Rechazo de excedente de material | "  . "login: " . $objServicio->getPuntoId()->getLogin();
            }
            else
            {
                throw new \Exception("<br>El servicio proporcionado es incorrecto.");
            }

            $objCaracteristicaFibra             = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('METRAJE FACTIBILIDAD PRECIO');
            $objCaracteristicaOCivil            = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('OBRA CIVIL PRECIO');
            $objCaracteristicaOtrosMate         = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('OTROS MATERIALES PRECIO');
            $objCaracteristicaCanceladoCliente  = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneByDescripcionCaracteristica('COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE');
            if(is_object(!$objCaracteristicaCanceladoCliente))
            {
                throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS CANCELADO POR EL CLIENTE PORCENTAJE</b>');
            }
            $objCaracteristicaAsumeCliente      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica('COPAGOS ASUME EL CLIENTE PRECIO');
            if(is_object(!$objCaracteristicaAsumeCliente))
            { 
                throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME EL CLIENTE PRECIO</b>');
            }
            $objCaracteristicaAsumeEmpresa      = $this->emcom->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneByDescripcionCaracteristica('COPAGOS ASUME LA EMPRESA PRECIO');
            if(is_object(!$objCaracteristicaAsumeEmpresa))
            {
                throw new \Exception(': NO SE ENCONTRÓ <br> <b>COPAGOS ASUME LA EMPRESA PRECIO</b>');
            }

            if(is_object($objDetalleSolicitudPla))
            {
                if(is_object($objCaracteristicaFibra)  && !empty($objCaracteristicaFibra) &&
                is_object($objCaracteristicaOCivil) && !empty($objCaracteristicaOCivil) &&
                is_object($objCaracteristicaOtrosMate) && !empty($objCaracteristicaOtrosMate) &&
                is_object($objCaracteristicaAsumeCliente)  && !empty($objCaracteristicaAsumeCliente)&&
                is_object($objCaracteristicaAsumeEmpresa)  && !empty($objCaracteristicaAsumeEmpresa))
                {
                    //Consulta si existe detalleSolCaract de fibra y obra civil y otros materiales
                    $objInfoDetalleSolCaractFibra = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaFibra,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla  ));
                    $objInfoDetalleSolCaractOCivil = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOCivil,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla ));
                    $objInfoDetalleSolCaractOtrosMate = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaOtrosMate,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla   ));
                    $objInfoDetalleSolCaractCanceladoCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaCanceladoCliente,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla  ));
                    $objInfoDetalleSolCaractAsumeCliente = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeCliente,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla  ));
                    $objInfoDetalleSolCaractAsumeEmpresa = $this->emcom->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                    ->findOneBy(array('caracteristicaId'  =>$objCaracteristicaAsumeEmpresa,
                                                                    'detalleSolicitudId'=>$objDetalleSolicitudPla ));
                }
                else
                {
                    throw new \Exception("<br>Ocurrió un error con las características de la solicitud, alguna de ellas no existen creadas");
                }
            
                if(is_object($objInfoDetalleSolCaractFibra) && !empty($objInfoDetalleSolCaractFibra) &&
                is_object($objInfoDetalleSolCaractOCivil) && !empty($objInfoDetalleSolCaractOCivil) &&
                is_object($objInfoDetalleSolCaractOtrosMate) && !empty($objInfoDetalleSolCaractOtrosMate)
                )
                {
                    $floatPrecioFibra            =  floatval($objInfoDetalleSolCaractFibra->getValor());
                    $floatPrecioObraCivil        =  floatval($objInfoDetalleSolCaractOCivil->getValor());
                    $floatPrecioOtrosMate        =  floatval($objInfoDetalleSolCaractOtrosMate->getValor());
                    $floatPorcentajeCanceladoCliente   =  floatval($objInfoDetalleSolCaractCanceladoCliente->getValor());                
                    
                    $strMail         = 'Solicitud Rechazada por GTN!. <br/>'  .'<b>Usuario que ejecutó la acción '.$strUsuario.'</b> <br/>'.
                                    'con la siguiente observación: << '. $strObservacion . ' >>.'
                                    .'<br/><b>#Solicitud Excedente</b> '.$intIdSolicitudExce.' <br/>';
                    
                    //APLICATIVO  NO AUTORIZA EL PORCENTAJE ASUMIDO POR TELCONET, GTN NO AUTORIZA.
                    if($floatPorcentajeCanceladoCliente!=0)
                    {
                        $strRespuesta     = 'Solicitud rechazada!.<br/>'  .'Se validó la solicitud Excedentes de Materiales con Copagos.<br/>'
                                            .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                            .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                            .' <b>Observación: </b> '.$strObservacion.' <br/>';

                        $strSeguimiento  =  'Solicitud rechazada!.<br/>' .'Se validó la solicitud Excedentes de Materiales con Copagos.<br/>'
                                            .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                            .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                            .' <b>Observación: </b> '.$strObservacion.' <br/>';
                    }
                    else
                    {
                        $floatSumaExcedente     = $floatPrecioFibra + $floatPrecioObraCivil + $floatPrecioOtrosMate;
                        if(($floatPrecioObraCivil!=0) || ($floatPrecioOtrosMate!=0)) 
                        {
                            $strRespuesta     = 'Solicitud rechazada!.<br/>'.'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                                .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                                .' <b>Observación: </b> '.$strObservacion.' <br/>';

                            $strSeguimiento  = 'Solicitud rechazada!.<br/>' .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                                .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'.
                                                '<b>Fibra:</b> $'.$floatPrecioFibra.' <br/>'.
                                                '<b>Obra Civil:</b> $'.$floatPrecioObraCivil.' <br/>'.
                                                '<b>Otros Materiales:</b> $'.$floatPrecioOtrosMate.' <br/>'.
                                                '<b>Total   :</b> $'.$floatSumaExcedente.' <br/>';
                        }
                        else
                        {
                            $strRespuesta   = 'Solicitud rechazada!.<br/>' .'Se validó la solicitud Excedentes de Materiales.'
                                                .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                                .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                                .' <b>Observación: </b> '.$strObservacion.' <br/>';

                            $strSeguimiento = 'Solicitud rechazada por GTN!.<br/>' .' 
                                                <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'.
                                                '<b>Rechazada por '.$strUsuario.'</b> <br/>'.
                                                ' Observación: << '. $strObservacion . ' >>.<br/>';
                        }
                    }            
                    //GUARDAR INFO DETALLE SOLICICITUD EXCEDENTE HISTORIAL
                    $strEstadoEnviado = "Rechazada";
                    $arrayParametrosTraSol = array(
                                    "emComercial"                => $this->emcom,
                                    "strClienteIp"               => $strIP,
                                    "objDetalleSolicitudExc"     => $objInfoSolicitudExcedente,
                                    "strObservacion"             => $strSeguimiento,
                                    "strUsrCreacion"             => $strUsuario,
                                    "strEstadoEnviado"          => $strEstadoEnviado);
                    $arrayVerificar = $serviceAutorizacion->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                    throw new \Exception(': EN: registroTrazabilidadDeLaSolicitud <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }

                    $arrayParametrosInfoDetSol = array(
                                    "emComercial"               => $this->emcom,
                                    "objServicio"               => $objServicio,
                                    "strEstadoEnviado"          => $strEstadoEnviado,
                                    "strUsrCreacion"            => $strUsuario); 
                    $arrayVerificar = $serviceAutorizacion->registroEstadoRechazoInfoDetalleSolicitud($arrayParametrosInfoDetSol);
                    if($arrayVerificar['status'] == 'ERROR' )
                    {
                    throw new \Exception(': EN: registroEstadoRechazoInfoDetalleSolicitud <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                    }           
                }
            }  
            else
            {
                $boolBuscaValores=true;
                //No hay valores con la solicitud de planificaciòn
            }

            // inicio, cuando no hay solicitud de Planificación, se va por los valores de factibilidad
           if(is_object($objDetalleSolicitudFactibilidad) && ($boolBuscaValores))
           {
                $strMail         = 'Solicitud Rechazada por GTN!.  <br/>'  .'<b>Usuario que ejecutó la acción '.$strUsuario.'</b> <br/>'.
                                    'con la siguiente observación: << '. $strObservacion . ' >>.'
                                    .'<br/><b>#Solicitud Excedente</b> '.$intIdSolicitudExce;
                                    
                $strRespuesta   = 'Solicitud Rechazada por GTN!. <br/>' .'Se validó la solicitud Excedentes de Materiales.'
                                    .'<br/><b>Usuario que ejecutó la acción :</b>'.$strUsuario
                                    .' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'
                                    .' <b>Observación: </b> '.$strObservacion.' <br/>';

                $strSeguimiento = 'Solicitud Rechazada por GTN!.  <br/>'.' <br/><b>#Solicitud Excedente</b>: '.$intIdSolicitudExce.' <br/>'.
                                    '<b>Usuario que ejecutó la acción '.$strUsuario.'</b> <br/>'.
                                    'Observación: << '. $strObservacion . ' >>.<br/>';

                //GUARDAR INFO DETALLE SOLICICITUD EXCEDENTE HISTORIAL
                $strEstadoEnviado = "Rechazada";
                $arrayParametrosInfoDetSol = array(
                                "emComercial"               => $this->emcom,
                                "objServicio"               => $objServicio,
                                "strEstadoEnviado"          => $strEstadoEnviado,
                                "strUsrCreacion"            => $strUsuario);
                $arrayVerificar = $serviceAutorizacion->registroEstadoRechazoInfoDetalleSolicitud($arrayParametrosInfoDetSol);
                if($arrayVerificar['status'] == 'ERROR' )
                {
                    throw new \Exception(': EN: registroEstadoRechazoInfoDetalleSolicitud<br> <b>'.$arrayVerificar['mensaje'].'</b>');
                }
                $arrayParametrosTraSol = array(
                                "emComercial"                => $this->emcom,
                                "strClienteIp"               => $strIP,
                                "objDetalleSolicitudExc"     => $objInfoSolicitudExcedente,
                                "strObservacion"             => $strSeguimiento,
                                "strUsrCreacion"             => $strUsuario,
                                "strEstadoEnviado"          => $strEstadoEnviado);
                $arrayVerificar = $serviceAutorizacion->registroTrazabilidadDeLaSolicitud($arrayParametrosTraSol);  
                if($arrayVerificar['status'] == 'ERROR' )
                {
                    throw new \Exception(': NO SE REALIZÓ: registroTrazabilidadDeLaSolicitud <br> <b>'.$arrayVerificar['mensaje'].'</b>');
                } 
            } //fin, cuando no hay solicitud de Planificación, se va por los valores de factibilidad
            
            //INSERTAR INFO SERVICIO HISTORIAL      - InfoServicioHistorial
            $strEstadoEnviado = "Anulado";
            $arrayParametrosTraServ = array(
                                "emComercial"                => $this->emcom,
                                "strClienteIp"               => $strIP,
                                "objServicio"                => $objServicio,
                                "strSeguimiento"             => $strSeguimiento,
                                "strUsrCreacion"             => $strUsuario,
                                "strAccion"                  => $strAccion,
                                "strEstadoEnviado"           => $strEstadoEnviado  );
            $arrayVerificar = $serviceAutorizacion->registroTrazabilidadDelServicio($arrayParametrosTraServ);
            if($arrayVerificar['status'] == 'ERROR' )
            {
                throw new \Exception(': EN: registroTrazabilidadDelServicio <br> <b>'.$arrayVerificar['mensaje'].'</b>');
            }

            $arrayParametrosMail = array(
                                        "login"       => $objServicio->getPuntoId()->getLogin(),
                                        "producto"    => $objServicio->getProductoId()->getDescripcionProducto(),
                                        "mensaje"     => $strMail );

            // Cuando GTN rechaza la solicitud: deberá llegar una notificación de alerta por reechazo
            $arrayParametrosNotif = array("strAsunto"                         => $strAsunto,
                                                "arrayParametrosMail"               => $arrayParametrosMail,
                                                "arrayDestinatario"                 => 'Alias',
                                                "strCodEmpresa"                     => $strCodEmpresa,
                                                "serviceEnvioPlantilla"             => $this->serviceEnvioPlantilla,
                                                "arrayFormasContactoAsistente"      => $arrayFormasContactoAsistente,
                                                "arrayFormasContactoAsesor"         => $arrayFormasContactoAsesor,
                                                "arrayFormasContactoAGtn"           => $arrayFormasContactoAGtn );
            $arrayVerificar = $this->envioDeNotificaciones($arrayParametrosNotif);
            if($arrayVerificar['status'] == 'ERROR' )
            {
                throw new \Exception(': NO SE REALIZÓ EL PROCESO: envioDeNotificaciones <br> <b>'.$arrayVerificar['mensaje'].'</b>');
            }

            if($arrayVerificar['status'] != 'OK')
            {
                throw new \Exception(': NO SE PUDO REALIZAR EL REGISTRO EN EL RECHAZO</b>');
            }
            else
            {
                $this->emcom->getConnection()->commit();
                $strStatus = "OK";
            }

        }
        catch (\Exception $e) 
        {
           $strStatus    = "ERROR";
           $strRespuesta = $e->getMessage();
           if ($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
                $this->emcom->getConnection()->close();
            }           
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;     
    }

    /**
     * Función para crear solicitud de autorizacion de excedentes.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     * 
     */
    public function registroSolicitudDeExcedenteMateriales($arrayParametros)
    {        
        $emComercial            = $arrayParametros['emComercial'];
        $strClienteIp           = $arrayParametros['strClienteIp'];
        $entityTipoSolicitud    = $arrayParametros['entityTipoSolicitud'];
        $objServicio            = $arrayParametros['objServicio'];
        $strSeguimiento         = $arrayParametros['strSeguimiento'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strEstadoEnviado       = $arrayParametros['strEstadoEnviado'];
        $objRespuesta           = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
             //CREO LA INFO DETALLE *SOLICITUD DE MATERIALES EXCEDENTES*
           $entitySolicitud  = new InfoDetalleSolicitud();
           $entitySolicitud->setServicioId($objServicio);
           $entitySolicitud->setTipoSolicitudId($entityTipoSolicitud);	
           $entitySolicitud->setEstado($strEstadoEnviado);	
           $entitySolicitud->setObservacion($strSeguimiento);
           $entitySolicitud->setUsrCreacion($strUsrCreacion);		
           $entitySolicitud->setFeCreacion(new \DateTime('now'));
           $emComercial->persist($entitySolicitud);
           $emComercial->flush(); 

            //CREO LA INFO DETALLE SOLICITUD HISTORIAL DE MATERIALES EXCEDENTES
            $entityDetSolHistM = new InfoDetalleSolHist();
            $entityDetSolHistM->setDetalleSolicitudId($entitySolicitud);
            $entityDetSolHistM->setObservacion($strSeguimiento);
            $entityDetSolHistM->setIpCreacion($strClienteIp);
            $entityDetSolHistM->setFeCreacion(new \DateTime('now'));
            $entityDetSolHistM->setUsrCreacion($strUsrCreacion);
            $entityDetSolHistM->setEstado($strEstadoEnviado);            
            $emComercial->persist($entityDetSolHistM);
            $emComercial->flush();
            $strStatus = "OK";
            $strRespuesta = "Procesado con éxito";
            $intIdSolicitud = $entitySolicitud->getId();
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta,
                                "intIdSolicitud"        => $intIdSolicitud);
        return $arrayRespuesta;
    }

    /**
     * Función para envio de notificaciones por correo de materiales excedentes.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     * 
     */

    public function envioDeNotificaciones($arrayParametros)
    {
        $strAsunto                      = $arrayParametros['strAsunto'];
        $arrayParametrosMail            = $arrayParametros['arrayParametrosMail'];
        $arrayDestinatario              = $arrayParametros['arrayDestinatario'];
        $strCodEmpresa                  = $arrayParametros['strCodEmpresa'];
        $serviceEnvioPlantillaC         = $arrayParametros['serviceEnvioPlantilla'];
        $arrayFormasContactoAsistente   = $arrayParametros['arrayFormasContactoAsistente'];
        $arrayFormasContactoAsesor      = $arrayParametros['arrayFormasContactoAsesor'];
        $arrayFormasContactoAGtn        = $arrayParametros['arrayFormasContactoAGtn'];
        $objRespuesta                   = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        $arrayDestinatarios = array();		
        try
        {
            if($arrayDestinatario =='Alias')
            {
                //Obtenemos el correo del creador osea el vendedor del servicio
                if($arrayFormasContactoAsistente)
                {
                    foreach($arrayFormasContactoAsistente as $arrayformaContacto)
                    {
                        $arrayDestinatarios[] = $arrayformaContacto['valor'];
                    }
                }
                //Obtenemos el correo del asesor del servicio
                if($arrayFormasContactoAsesor)
                {
                    foreach($arrayFormasContactoAsesor as $arrayformaContacto)
                    {
                        $arrayDestinatarios[] = $arrayformaContacto['valor'];
                    }
                }
                //Obtenemos el o los correos de alias PYL 
                $objParametroCabCorreosExce = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                    ->findOneBy(array("descripcion"=>'CORREOS A ENVIAR LOS VALORES DE EXCEDENTES', 
                                                        "modulo"=>'COMERCIAL', "estado"=>'Activo'));

                if(is_object($objParametroCabCorreosExce))
                {
                    $arrayCorreosParaExcedente      = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('CORREO_EXCEDENTES', '', '', '', '','','', '', '', $strCodEmpresa);

                    if(isset($arrayCorreosParaExcedente) && !empty($arrayCorreosParaExcedente))
                    {
                        foreach($arrayCorreosParaExcedente as $arrayValores)
                        {
                                $arrayDestinatarios[] = $arrayValores["valor1"];
                        }
                    }
                    else
                    {
                        throw new \Exception(': NO SE ENCONTRÓ UN CORREO PARA ENVIAR LA NOTIFICACIÓN');
                    }         
                }
                else
                {
                    throw new \Exception(': NO SE ENCONTRÓ UN PARÁMETRO <br> <b> CORREOS A ENVIAR LOS VALORES DE EXCEDENTES </b>');
                }
            }
            else
            {
                // Obtenemos el Correo de GTN . 
                if($arrayFormasContactoAGtn)
                {
                    foreach($arrayFormasContactoAGtn as $arrayformaContacto)
                    {
                        $arrayDestinatarios[] = $arrayformaContacto['valor'];
                    }
                }
            }
            
            $serviceEnvioPlantillaC
                        ->generarEnvioPlantilla($strAsunto, $arrayDestinatarios,
                                                'NOTIEXCMATASE',
                                                $arrayParametrosMail, $strCodEmpresa,
                                                '',  '',  null, false,
                                                'notificaciones_telcos@telconet.ec');            
                 $strStatus = "OK";
                 $strRespuesta = "Procesado con éxito";
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }

}
