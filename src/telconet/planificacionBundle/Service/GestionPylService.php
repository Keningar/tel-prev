<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;

/**
 * Clase GestionPylService
 *
 * Clase que se encarga de realizar la gestión simultánea de las solicitudes
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 14-04-2021
 * 
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.1 25-06-2021 - Se aumenta variable "serviceUtil" y se instancia la dependencia
 * 
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.2 13-08-2021 - se agrega variable "serviceCoordinarService" y la instancia de su dependencia
 * 
 */
class GestionPylService
{
    private $objContainer;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComunicacion;
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
    private $emGeneral;
    private $serviceServicioTenico;
    private $serviceRecursosRed;
    private $serviceInfoServicio;
    private $serviceCoordinar;
    private $servicePlanificacion;
    private $serviceEnvioPlantilla;
    private $serviceEnvioSms;
    private $objTemplating;
    private $serviceSoporte;
    private $servicePlanificar;
    private $serviceInterfaceElemento;
    private $serviceInfoWifi;
    private $serviceFoxPremium;
    private $serviceUtil;
    private $serviceCoordinar2;
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer             = $objContainer;
        $this->emComercial              = $objContainer->get('doctrine')->getManager('telconet');
        $this->emComunicacion           = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->emInfraestructura        = $objContainer->get('doctrine')->getManager("telconet_infraestructura");
        $this->emSoporte                = $objContainer->get('doctrine')->getManager("telconet_soporte");
        $this->emGeneral                = $objContainer->get('doctrine')->getManager("telconet_general");
        $this->serviceServicioTenico    = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceRecursosRed       = $objContainer->get('planificacion.RecursosDeRed');
        $this->serviceInfoServicio      = $objContainer->get('comercial.InfoServicio');
        $this->serviceCoordinar         = $objContainer->get('planificacion.Coordinar');
        $this->servicePlanificacion     = $objContainer->get('planificacion.Planificar');
        $this->serviceEnvioPlantilla    = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceEnvioSms          = $objContainer->get('comunicaciones.SMS');
        $this->objTemplating            = $objContainer->get('templating');
        $this->serviceSoporte           = $objContainer->get('soporte.SoporteService');
        $this->servicePlanificar        = $objContainer->get('planificacion.Planificar');
        $this->serviceInterfaceElemento = $objContainer->get('tecnico.InfoInterfaceElemento');
        $this->serviceInfoWifi          = $objContainer->get('tecnico.InfoElementoWifi');
        $this->serviceFoxPremium        = $objContainer->get('tecnico.FoxPremium');
        $this->serviceUtil              = $objContainer->get('schema.Util');
        $this->serviceCoordinar2        = $objContainer->get('planificacion.Coordinar2');
    }
    
    /**
     * 
     * Función usada para replanificar una solicitud
     * 
     * @param array $arrayParametros [
     *                                  "intIdSolicitud"                    => id de la solicitud
     *                                  "intIdMotivo"                       => id del motivo
     *                                  "strBoolPerfilOpu"                  => 'SI' o 'NO' es el perfil de Opu
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strFechaReplanificacion"           => fecha de replanificación
     *                                  "strFechaHoraInicioReplanificacion" => hora inicio de la replanificación
     *                                  "strFechaHoraFinReplanificacion"    => hora fin de la replanificación
     *                                  "strObservacion"                    => observación de la solicitud
     *                                  "strFechaReserva"                   => fecha de reserva
     *                                  "strParamResponsables"              => cadena con formato para obtener los responsables
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                  "strEsHal"                          => 'S' en caso de ser Hal
     *                                  "objRequest"                        => Objeto con el request
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                                  "objServicio"           => objeto del servicio replanificado
     *                                  "objServicioHistorial"  => objeto del historial del servicio replanificado
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.2 06-07-2021 - Se valida que si es un servicio por traslado, realice la replanificacion automatica para los productos
     *                           con planificacion manual simultanea.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.3 20-03-2023 - Se agrega validacion por prefijo empresa Ecuanet (EN) para validar el cupo de planificacion disponible. 
     */
    public function reprogramarPlanificacion($arrayParametros)
    {
        $intIdSolicitud                     = $arrayParametros["intIdSolicitud"];
        $intIdMotivo                        = $arrayParametros["intIdMotivo"];
        $strBoolPerfilOpu                   = $arrayParametros["strBoolPerfilOpu"];
        $strPrefijoEmpresa                  = $arrayParametros["strPrefijoEmpresa"];
        $strFechaReplanificacion            = $arrayParametros["strFechaReplanificacion"];
        $strFechaHoraInicioReplanificacion  = $arrayParametros["strFechaHoraInicioReplanificacion"];
        $strFechaHoraFinReplanificacion     = $arrayParametros["strFechaHoraFinReplanificacion"];
        $strObservacion                     = $arrayParametros["strObservacion"];
        $strFechaReserva                    = $arrayParametros["strFechaReserva"];
        $strParamResponsables               = $arrayParametros["strParamResponsables"];
        $strIpCreacion                      = $arrayParametros["strIpCreacion"];
        $strUsrCreacion                     = $arrayParametros["strUsrCreacion"];
        $strEsHal                           = $arrayParametros["strEsHal"];
        $objRequest                         = $arrayParametros["objRequest"];
        $strEmail                           = $arrayParametros['strEmail'];
        $arrayFechaSms                      = $arrayParametros['arrayFechaSms'];
        $strCodEmpresa                      = $arrayParametros['strCodEmpresa'];
        $intHoraCierre                      = $this->objContainer->getParameter('planificacion.mobile.hora_cierre');
        $boolMostrarMsjErrorUsr             = false;
        $boolValidaCupo                     = true;
        $boolContinuaFlujo                  = true;
        $boolSigueFlujoPlanificacion        = false;
        $boolEliminaCaract                  = true;
        $objServicio                        = null;
        $objServicioHistorial               = null;
        $strStatus                          = "";
        $strMensaje                         = "";
        $boolPerfilOpu                      = $strBoolPerfilOpu === 'true' ? true: false;
        $strNombreTareaFacturacion          = "-";
        $strNombreTareaXDetalleId           = "";
        $strBanderaValidarNombreTarea       = "N";

        try
        {
            $objCaractProdControlaCupo              = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                        ->findOneByDescripcionCaracteristica('PRODUCTO CONTROLA CUPO');
            $objMotivo                              = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);
            
            $arrayFechaReplanificacion              = explode("T", $strFechaReplanificacion);
            $arrayDiaMesAnioFechaReplanificacion    = explode("-", $arrayFechaReplanificacion[0]);

            $arrayFechaHoraInicioReplanificacion    = explode("T", $strFechaHoraInicioReplanificacion);
            $arrayHoraMinSegInicioReplanificacion   = explode(":", $arrayFechaHoraInicioReplanificacion[1]);

            $strFechaHoraInicio                     = date("Y/m/d H:i", strtotime(  $arrayDiaMesAnioFechaReplanificacion[2] . "-" 
                                                                                    . $arrayDiaMesAnioFechaReplanificacion[1] . "-" 
                                                                                    . $arrayDiaMesAnioFechaReplanificacion[0] . " " 
                                                                                    . $arrayFechaHoraInicioReplanificacion[1]));

            $arrayFechaHoraFinReplanificacion       = explode("T", $strFechaHoraFinReplanificacion);
            $strFechaHoraFin                        = date("Y/m/d H:i", strtotime(  $arrayDiaMesAnioFechaReplanificacion[2] . "-" 
                                                                                    . $arrayDiaMesAnioFechaReplanificacion[1] . "-" 
                                                                                    . $arrayDiaMesAnioFechaReplanificacion[0] . " " 
                                                                                    . $arrayFechaHoraFinReplanificacion[1]));
            
            $objDateFechaReserva                    = new \DateTime(date('Y-m-d H:i:s',strtotime($strFechaReserva)));
            $objDateNow                             = new \DateTime('now');
            $strFechaInicioReplanificacionSql       = date("Y/m/d G:i:s", strtotime($arrayDiaMesAnioFechaReplanificacion[2] 
                                                                                    . "-" . $arrayDiaMesAnioFechaReplanificacion[1] 
                                                                                    . "-" . $arrayDiaMesAnioFechaReplanificacion[0] . " " 
                                                                                    . $arrayFechaHoraInicioReplanificacion[1]));
            $strFechaFinReplanificacionSql          = date("Y/m/d G:i:s", strtotime($arrayDiaMesAnioFechaReplanificacion[2] . "-" 
                                                                                    . $arrayDiaMesAnioFechaReplanificacion[1] . "-" 
                                                                                    . $arrayDiaMesAnioFechaReplanificacion[0] . " " 
                                                                                    . $arrayFechaHoraFinReplanificacion[1]));
            if(isset($strEsHal) && !empty($strEsHal) && $strEsHal === "S" && $objDateNow > $objDateFechaReserva)
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("El tiempo de reserva para la sugerencia escogida ha culminado..!!");
            }
            
            $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            if(!is_object($objSolicitud))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe el detalle de solicitud"); 
            }
            $strTipoSolicitud           = strtolower($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());
            $objServicio                = $this->emComercial->getRepository("schemaBundle:InfoServicio")
                                                            ->find($objSolicitud->getServicioId()->getId());
            $objProductoServicio        = $objServicio->getProductoId();
            $intIdJurisdiccionPunto     = $objSolicitud->getServicioId()->getPuntoId()->getPuntoCoberturaId()->getId();
            $intCupoJurisdiccionPunto   = $objSolicitud->getServicioId()->getPuntoId()->getPuntoCoberturaId()->getCupo();
            
            if(!isset($intCupoJurisdiccionPunto) || empty($intCupoJurisdiccionPunto) || $intCupoJurisdiccionPunto <= 0 || $strEsHal === 'S')
            {
                $boolValidaCupo = false;
            }
            else if(is_object($objProductoServicio) && is_object($objCaractProdControlaCupo))
            {
                $objProdCaractControlaCupo  = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                ->findOneBy(array( "productoId"        => $objProductoServicio->getId(),
                                                                                   "caracteristicaId"  => $objCaractProdControlaCupo));
                if(is_object($objProdCaractControlaCupo))
                {
                    $boolValidaCupo = false;
                }
            }
            
            //Nunca entra a este if, pero se replica tal cual como estaba en el Controller
            if(($strPrefijoEmpresa === "MD" || $strPrefijoEmpresa === "EN") && 
                $boolValidaCupo && !$boolPerfilOpu 
                && ($boolPerfilOpu && $strTipoSolicitud !== "solicitud planificacion"))
            {
                $strFechaPar    = substr($strFechaHoraInicio, 0, -1);
                $strFechaPar    .= "1";
                $strFechaPar    = str_replace("-", "/", $strFechaPar);

                $intNumCupoPlanificacion    = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                ->getCountDisponiblesWeb(array("strFecha"          => $strFechaPar,
                                                                                               "intJurisdiccion"   => 
                                                                                               $intIdJurisdiccionPunto,
                                                                                               "intHoraCierre"     => $intHoraCierre));

                if ($intNumCupoPlanificacion == 0)
                {
                    $boolMostrarMsjErrorUsr = true;
                    throw new \Exception("No hay cupo disponible para este horario, seleccione otro horario por favor!");
                }
            }
            
            if($objServicio->getEstado() === "Activo" 
                && ($strTipoSolicitud !== 'solicitud migracion' &&
                    $strTipoSolicitud !== 'solicitud agregar equipo' &&
                    $strTipoSolicitud !== 'solicitud agregar equipo masivo' &&
                    $strTipoSolicitud !== 'solicitud retiro equipo' &&
                    $strTipoSolicitud !== 'solicitud cambio equipo por soporte' &&
                    $strTipoSolicitud !== 'solicitud cambio equipo por soporte masivo' &&
                    $strTipoSolicitud !== 'solicitud de instalacion cableado ethernet' &&
                    $strTipoSolicitud !== 'solicitud reubicacion'))
                {
                    $boolMostrarMsjErrorUsr = true;
                    throw new \Exception("El servicio Actualmente se encuentra con estado Activo, no es posible replanificar.");
                }
        }
        catch (\Exception $e)
        {
            $boolContinuaFlujo = false;
            $strStatus = "ERROR";
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo realizar la replanificación. Comuníquese con el Dep. de Sistemas!";
            }
        }
        
        if($boolContinuaFlujo)
        {
            $this->emComunicacion->getConnection()->beginTransaction();
            $this->emInfraestructura->getConnection()->beginTransaction();
            $this->emComercial->getConnection()->beginTransaction();
            $this->emSoporte->getConnection()->beginTransaction();
            try
            {
                if((is_object($objProductoServicio) && $objProductoServicio->getNombreTecnico() === "EXTENDER_DUAL_BAND"
                        && ($strTipoSolicitud === "solicitud agregar equipo" || $strTipoSolicitud === "solicitud agregar equipo masivo")))
                {
                    $boolSigueFlujoPlanificacion = true;
                }
                
                if ($strTipoSolicitud == "solicitud planificacion" || $boolSigueFlujoPlanificacion)
                {
                    $arrayParamRespon = explode("|", $strParamResponsables);

                    foreach ($arrayParamRespon as $arrayResponsables)
                    {
                        $arrayVariablesR = explode("@@", $arrayResponsables);

                        if ($arrayVariablesR && count($arrayVariablesR) > 0)
                        {
                            $strBand   = $arrayVariablesR[1];
                            $strCodigo = $arrayVariablesR[2];
                        }
                    }
                    
                    if ($strBand == 'cuadrilla')
                    {
                        $arrayCuposPlanificacion    = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                        ->findBy(array('solicitudId' => $intIdSolicitud));
                        foreach ($arrayCuposPlanificacion as $objCupoPlanificacion)
                        {
                            $objCupoPlanificacion->setSolicitudId(null);
                            $objCupoPlanificacion->setCuadrillaId(null);
                            $this->emComercial->persist($objCupoPlanificacion);
                            $this->emComercial->flush();
                        }
                        
                        $arrayParametrosRango['strFeInicio']       = $strFechaHoraInicio;
                        $arrayParametrosRango['strFeFin']          = $strFechaHoraFin;
                        $arrayParametrosRango['intJurisdiccionId'] = $intIdJurisdiccionPunto;
                        $arrayRangosCupoPlanificacion              = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                                       ->getRangoFecha($arrayParametrosRango);
                        foreach ($arrayRangosCupoPlanificacion as $arrayRangoCupoPlanificacion)
                        {
                            $objCupoPlanificacion   = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                        ->find($arrayRangoCupoPlanificacion['id']);
                            $objCupoPlanificacion->setSolicitudId($intIdSolicitud);
                            $objCupoPlanificacion->setCuadrillaId($strCodigo);
                            $this->emComercial->persist($objCupoPlanificacion);
                            $this->emComercial->flush();
                        }
                    }
                }
                
                //Si tiene recursos de bb se eliminan
                if ($objSolicitud->getEstado() === "Asignada" && $strPrefijoEmpresa !== "TN")
                {
                    //Para TN no elimina las características técnicas
                    //si el producto es netvoice se libera el numero del pool
                    if(is_object($objProductoServicio))
                    {
                        $strNombreTecnico = $objProductoServicio->getNombreTecnico();

                        if ($strNombreTecnico === 'TELEFONIA_NETVOICE')
                        {
                            $boolEliminaCaract = false;
                            //consulto la capacidad del servicio nuevo
                            $objSpc = $this->serviceServicioTenico->getServicioProductoCaracteristica($objServicio, "NUMERO", 
                                                                                               $objServicio->getProductoId());
                            if (is_object($objSpc))
                            {
                                $intNumeroTelefonico = $objSpc->getValor();
                                if ($intNumeroTelefonico)
                                {
                                    $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                  ->findOneBy(array('detalleValor'  => $intNumeroTelefonico,
                                                                                                    'detalleNombre' => 'NUMERO TELEFONICO'));
                                    if (is_object($objDetalleElemento))
                                    {
                                        $objDetalleElemento->setEstado('Disponible');
                                        $this->emInfraestructura->persist($objDetalleElemento);
                                        $this->emInfraestructura->flush();
                                    }
                                }
                            }
                        }
                    }

                    if ($strTipoSolicitud !== 'solicitud agregar equipo' && 
                        $strTipoSolicitud !== 'solicitud agregar equipo masivo' &&
                        $strTipoSolicitud !== 'solicitud cambio equipo por soporte' &&
                        $strTipoSolicitud !== 'solicitud cambio equipo por soporte masivo' &&
                        $boolEliminaCaract)
                    {
                        //eliminar todas las caracteristicas tecnicas
                        $arrayServicioProdCaracts   = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                           ->findByServicioAndEstadoAndTipo(array(  "tipo"       => 'TECNICA',
                                                                                                    "servicioId" => $objServicio->getId(),
                                                                                                    "estado"     => 'Activo'));
                        foreach($arrayServicioProdCaracts as $objServicioProdCaract)
                        {
                            $objServicioProdCaract->setEstado('Eliminado');
                            $this->emComercial->persist($objServicioProdCaract);
                            $this->emComercial->flush();
                        }

                        //eliminar todas las Ips
                        $arrayInfoIps   = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                                                  ->getIpsPorServicioPorEstados(array(  "idServicio"    => $objServicio->getId(),
                                                                                                        "arrayEstados"  => array('Activo',
                                                                                                                                 'Reservada')));
                        foreach ($arrayInfoIps as $infoIp)
                        {
                            $infoIp->setEstado('Eliminado');
                            $this->emInfraestructura->persist($infoIp);
                            $this->emInfraestructura->flush();
                        }
                    }

                    $objServicioTecnico = $this->emComercial->getRepository("schemaBundle:InfoServicioTecnico")
                                                            ->findOneByServicioId($objServicio->getId());
                    if(is_object($objServicioTecnico) && $objServicioTecnico->getUltimaMillaId())
                    {
                        //eliminar la interface
                        $objTipoMedio   = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                                  ->find($objServicioTecnico->getUltimaMillaId());
                        if ($objServicioTecnico->getInterfaceElementoId())
                        {
                            if ($objTipoMedio->getNombreTipoMedio() == "Cobre")
                            {
                                $objInterface = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                                           ->findOneById($objServicioTecnico->getInterfaceElementoId());

                                $objInterface->setEstado('not connect');
                                $this->emInfraestructura->persist($objInterface);
                                $this->emInfraestructura->flush();
                                $objServicioTecnico->setInterfaceElementoId(null);
                            }
                            
                            if ($objTipoMedio->getNombreTipoMedio() == "Radio")
                            {
                                $objServicioTecnico->setInterfaceElementoId(null);
                            }
                            $this->emComercial->persist($objServicioTecnico);
                            $this->emComercial->flush();
                        }
                    }
                }
                
                // Obtenemos los parametros de productos adicionales
                $arrayProdPermitidos = array();
                $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                            'Productos adicionales manuales para planificar simultaneo',
                                            '','','','','',$strCodEmpresa);
                if (is_array($arrayParamValores) && !empty($arrayParamValores))
                {
                    $arrayProdPermitidos = $this->serviceUtil->obtenerValoresParametro($arrayParamValores);
                }
                $intIdPunto = $objServicio->getPuntoId()->getId();
                if ($objServicio->getEstado() == "AsignadoTarea")
                {
                    $strEstadoAnt = "Asignada";
                }
                else
                {
                    $strEstadoAnt = $objServicio->getEstado();
                }
                // Verificamos si es un proceso principal o simultaneo
                $strBanSimultaneo = $arrayParametros['strProcesoSimultaneo'];
                if (empty($strBanSimultaneo))
                {
                    $strBanSimultaneo = "NO";
                }

                if ($strTipoSolicitud == "solicitud planificacion" || $boolSigueFlujoPlanificacion)
                {
                    //cambio estados
                    $objServicio->setEstado("Replanificada");
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();

                    //GUARDAR INFO SERVICIO HISTORIAL
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $strObservacionServicio = $strObservacion;
                    $strObservacionServicio .= "<br>";
                    $strFechaReplanificada  = $arrayDiaMesAnioFechaReplanificacion[2] . "/" . $arrayDiaMesAnioFechaReplanificacion[1] 
                                              . "/" . $arrayDiaMesAnioFechaReplanificacion[0];

                    $arrayHoraInicioServicio = explode(':', $arrayFechaHoraInicioReplanificacion[1]);
                    $arrayHoraFinServicio    = explode(':', $arrayFechaHoraFinReplanificacion[1]);

                    $strObservacionServicio .= "<br>Fecha Replanificada: " . $strFechaReplanificada;
                    $strObservacionServicio .= "<br>Hora Inicio: " . $arrayHoraInicioServicio[0] . ":" . $arrayHoraInicioServicio[1];
                    $strObservacionServicio .= "<br>Hora Fin: " . $arrayHoraFinServicio[0] . ":" . $arrayHoraFinServicio[1];
                    $strObservacionServicio .= "<br><br>";

                    $objServicioHistorial->setObservacion($strObservacionServicio);
                    $objServicioHistorial->setMotivoId($intIdMotivo);
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setEstado('Replanificada');
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    
                    // Validamos que sea venga por replanificacion principal
                    if ($strBanSimultaneo == "NO")
                    {
                        // Realizamos la replanificación simultánea para productos adicionales
                        $strPlanServicio = $objServicio->getPlanId();
                        $strProdServicio = $objServicio->getProductoId();
                        if ((!empty($strPlanServicio) && $strTipoSolicitud == "solicitud planificacion") ||
                            (!empty($strProdServicio) && $strTipoSolicitud == "solicitud planificacion" &&
                                in_array($strProdServicio->getId(), $arrayProdPermitidos)))
                        {
                            $strNuevaObsDetSol = $objSolicitud->getEstado().': '
                                                 .$objSolicitud->getObservacion()."\n".'Replanificada: '
                                                 .$strObservacion;
                            $strEstadoActual   = "Replanificada";
                            $arrayDatosEstado  = array(
                                "intIdPunto" => $intIdPunto,
                                "intIdServicio" => $objServicio->getId(),
                                "strEstado"=>$strEstadoActual,
                                "strEstadoAnt"=>$strEstadoAnt,
                                "strFechaInicio" => $strFechaHoraInicio,
                                "strFechaFin" => $strFechaHoraFin,
                                "strObservacionServicio" => $strObservacionServicio,
                                "strObservacionSolicitud" => $strNuevaObsDetSol,
                                "strIpCreacion" => $strIpCreacion,
                                "strUsrCreacion" => $strUsrCreacion,
                                "strSolCableado" => "NO",
                                "intIdEmpresa" => $strCodEmpresa,
                                "objRequest" => $objRequest
                            );
                            $this->servicePlanificacion->actualizaEstProdAdiManuales($arrayDatosEstado, null);
                        }
                    }
                }
                
                // Validamos que sea replanificacion principal
                if ($strBanSimultaneo == "NO")
                {
                    // Creamos la solicitud simultanea para productos adicionales
                    $arrayTiposSolicitudes = array();
                    $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                                'Planificacion simultanea que no son de solicitudes de planificacion',
                                                '','','','','',$strCodEmpresa);
                    if (is_array($arrayParamValores) && !empty($arrayParamValores))
                    {
                        $arrayTiposSolicitudes = $this->serviceUtil->obtenerValoresParametro($arrayParamValores);
                    }
                    $strProdServicio = $objServicio->getProductoId();
                    if(!empty($strProdServicio) &&
                        in_array($strTipoSolicitud, $arrayTiposSolicitudes) &&
                        in_array($strProdServicio->getId(), $arrayProdPermitidos))
                    {
                        $strEstadoActual    = "Replanificada";
                        $objServicio->setEstado($strEstadoActual);
                        $this->emComercial->persist($objServicio);
                        $this->emComercial->flush();

                        // Ingresamos el historial del detalle de la solicitud
                        $objServicioHist = new InfoServicioHistorial();
                        $objServicioHist->setServicioId($objServicio);
                        $objServicioHist->setObservacion('Se Replanifica el producto');
                        $objServicioHist->setIpCreacion($strIpCreacion);
                        $objServicioHist->setUsrCreacion($strUsrCreacion);
                        $objServicioHist->setFeCreacion(new \DateTime('now'));
                        $objServicioHist->setEstado($strEstadoActual);
                        $this->emComercial->persist($objServicioHist);
                        $this->emComercial->flush();

                        // Realizamos la replanificación simultánea para los CE
                        $strNuevaObsDetSol  = $objSolicitud->getEstado().': '.$objSolicitud->getObservacion()."\n".'Replanificada: '.$strObservacion;
                        $arrayDatosEstado = array(
                            "intIdPunto" => $intIdPunto,
                            "intIdServicio" => $objServicio->getId(),
                            "strEstado"=>$strEstadoActual,
                            "strEstadoAnt"=>$strEstadoAnt,
                            "strFechaInicio" => $strFechaHoraInicio,
                            "strFechaFin" => $strFechaHoraFin,
                            "strObservacionServicio" => $strObservacionServicio,
                            "strObservacionSolicitud" => $strNuevaObsDetSol,
                            "strIpCreacion" => $strIpCreacion,
                            "strUsrCreacion" => $strUsrCreacion,
                            "strSolCableado" => "SI",
                            "intIdEmpresa" => $strCodEmpresa,
                            "objRequest" => $objRequest
                        );
                        $this->servicePlanificacion->actualizaEstProdAdiManuales($arrayDatosEstado, null);
                    }
                }
                
                $strNuevaObsDetalleSolicitud    = $objSolicitud->getEstado().': ' .$objSolicitud->getObservacion().
                                                  "\n".
                                                  'Replanificada: ' .$strObservacion;
                $objSolicitud->setMotivoId($intIdMotivo);
                $objSolicitud->setObservacion( substr($strNuevaObsDetalleSolicitud, 0, 1499) );
                $objSolicitud->setEstado("Replanificada");
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);

                if ($boolPerfilOpu && $strTipoSolicitud === "solicitud planificacion")  
                {
                    $objDetalleSolHist->setFeIniPlan(null);
                    $objDetalleSolHist->setFeFinPlan(null);                    
                }    
                else
                {
                    $objDetalleSolHist->setFeIniPlan(new \DateTime($strFechaInicioReplanificacionSql));
                    $objDetalleSolHist->setFeFinPlan(new \DateTime($strFechaFinReplanificacionSql));                    
                }
                $objDetalleSolHist->setObservacion(substr($strObservacion, 0, 1499));
                $objDetalleSolHist->setMotivoId($intIdMotivo);

                $objDetalleSolHist->setIpCreacion($strIpCreacion);
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHist->setEstado('Replanificada');

                $this->emComercial->persist($objDetalleSolHist);
                $this->emComercial->flush();
                
                //------- COMUNICACIONES --- NOTIFICACIONES
                $strContenidoCorreo = $this->objTemplating->render( 'planificacionBundle:Coordinar:notificacion.html.twig', array(
                                                                    'detalleSolicitud'     => $objSolicitud,
                                                                    'detalleSolicitudHist' => $objDetalleSolHist,
                                                                    'motivo'               => $objMotivo));
                if ($strTipoSolicitud === 'solicitud cambio equipo por soporte' 
                    || $strTipoSolicitud === 'solicitud cambio equipo por soporte masivo')
                {
                    $strAsunto = "Replanificacion de Solicitud de Cambio de Equipo por Soporte #" . $objSolicitud->getId();
                }
                else
                {
                    $strAsunto = "Replanificacion de Solicitud de Instalacion #" . $objSolicitud->getId();
                }

                //DESTINATARIOS....
                $arrayFormasContacto    = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()->getUsrVendedor(),
                                                                                                         'Correo Electronico');
                $arrayTo                = array();
                if ($arrayFormasContacto)
                {
                    foreach ($arrayFormasContacto as $arrayFormaContacto)
                    {
                        $arrayTo[] = $arrayFormaContacto['valor'];
                    }
                }
                else
                {
                    $arrayTo[] = 'notificaciones_telcos@telconet.ec';
                }
                $this->serviceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strContenidoCorreo);
                $objPunto           = $objServicio->getPuntoId();
                $objPer             = $objPunto->getPersonaEmpresaRolId();
                $objPersona         = $objPer->getPersonaId();
                $strFormaContacto   = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                        ->findContactosTelefonicosPorPunto($objPunto->getId());
                $arrayFormaContacto = explode(",", $strFormaContacto);
                foreach ($arrayFormaContacto as $strTelefono)
                {
                    if (substr(trim($strTelefono), 0, 2) == "09" || substr(trim($strTelefono), 0, 4) == "5939")
                    {
                        $strNumeroTelefonico = trim($strTelefono);
                    }
                }
                
                $arrayNumeroTelef   = array(array('value'  => $strNumeroTelefonico,
                                                  'smsbox' => 0));
                $strContenidoSms    = 'Se Replanifico la solicitud para el dia ' . $arrayFechaSms . ' a las  ' .
                                      $arrayFechaHoraInicioReplanificacion[1] .
                                      ' para la instalacion de tu servicio, si requieres mayor ' .
                                      'informacion contactate al 3731300 opcion 4';
                if(isset($strNumeroTelefonico) && !empty($strNumeroTelefonico))
                {
                    //$arrayResponseSMS = (array) $this->serviceEnvioSms->sendSMS($strContenidoSms, $arrayNumeroTelef, 3, 15);
                    $arrayResponseSMS['code'] = 202;
                    if($arrayResponseSMS['code'] == 202)
                    {
                        $arrayResponse['mensaje'] = "<br>SMS enviado correctamente";
                        $arrayData['status']      = 'OK';
                        $arrayData['mensaje']     = 'SMS enviado correctamente';
                    }
                    else
                    {
                        $arrayResponse['mensaje'] = "<br>SMS No enviado";
                        $arrayData['status']      = 'ERROR_SERVICE';
                        $arrayData['mensaje']     = $arrayResponseSMS['detail'];
                    }
                    //======================================================================
                    // Si el envio del SMS se encuentra OK se procede a registrar el PIN para el usuario
                }
                else
                {
                    $arrayData['status']  = 'ERROR_SERVICE';
                    $arrayData['mensaje'] = 'Inconvenientes con el envio del SMS';
                }
                
                if(isset($strEmail) && !empty($strEmail))
                {
                    $strDireccion = $objPunto->getDireccion();
                    $strNombres   = $objPersona->getNombres() . " " . $objPersona->getApellidos();
                    $strMensaje   = $this->objTemplating->render('planificacionBundle:Coordinar:notificacionPlanificacionMobile.html.twig', array(
                        'strFecha'     => $arrayFechaSms,
                        'strHora'      => $arrayHoraMinSegInicioReplanificacion[0] . ":" . $arrayHoraMinSegInicioReplanificacion[1],
                        'strDireccion' => $strDireccion,
                        'strNombres'   => $strNombres));

                    $strAsunto = "Instalación de Servicio";
                    $arrayTo   = array();
                    $arrayTo[] = $strEmail;
                    $this->serviceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje);
                }
                
                
                if($strPrefijoEmpresa == "TN")
                {
                    $strBanderaValidarNombreTarea = "S";
                    $arrayCodTareaFacturacion     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('PARAMETROS_PROYECTO_CAMBIO_PRECIO_TN',
                                                                             'COMERCIAL',
                                                                             'AUTORIZACION_EXCEDENTE',
                                                                             'NOMBRE_TAREA_FACTURACION_MATERIALES_EXCEDENTES',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             '',
                                                                             $strCodEmpresa);

                    if(isset($arrayCodTareaFacturacion["valor1"]) && !empty($arrayCodTareaFacturacion["valor1"]))
                    {
                        $strNombreTareaFacturacion = $arrayCodTareaFacturacion["valor1"];
                    }
                }
                
                $arrayDetalles  = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                  ->findBy(array('detalleSolicitudId' => $objSolicitud->getId()));
                if(isset($arrayDetalles) && !empty($arrayDetalles))
                {
                    foreach($arrayDetalles as $objDetalle)
                    {
                        $strEstadoActualTarea   = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($objDetalle->getId());
                        $arrayEstadosFinalzados = array("Finalizada","Cancelada","Anulada");
                        if (in_array($strEstadoActualTarea,$arrayEstadosFinalzados))
                        {
                            continue;
                        }
                        
                        if($strBanderaValidarNombreTarea == "S")
                        {
                            $objAdmiTarea = $objDetalle->getTareaId();

                            if(is_object($objAdmiTarea))
                            {
                                $strNombreTareaXDetalleId = $objAdmiTarea->getNombreTarea();
                            }
                        }

                        if($strNombreTareaXDetalleId != $strNombreTareaFacturacion)
                        {
                            $strRespuestaCancelacionTarea   = $this->serviceSoporte
                                                                   ->cambiarEstadoTarea(  
                                                                                    $objDetalle, 
                                                                                    null, 
                                                                                    $objRequest, 
                                                                                    array(  "observacion"   => "Replanificación de Orden de Trabajo",
                                                                                            "cargarTiempo"  => "cliente",
                                                                                            "estado"        => "Replanificada",
                                                                                            "esSolucion"    => "N"));
                            if($strRespuestaCancelacionTarea != "OK")
                            {
                                $boolMostrarMsjErrorUsr = true;
                                throw new \Exception("Ciertas tareas no pudieron ser replanificadas. Favor notificar a Sistemas.");
                            }
                        }
                    }
                }
                $this->emComercial->getConnection()->commit();
                $this->emInfraestructura->getConnection()->commit();
                $this->emComunicacion->getConnection()->commit();
                $this->emSoporte->getConnection()->commit();
                $strStatus  = "OK";
                $strMensaje = "Se replanifico la solicitud";
            }
            catch (\Exception $e)
            {
                $strStatus  = "ERROR";
                $strMensaje = "Error: " . $e->getMessage();
                if($boolMostrarMsjErrorUsr)
                {
                    $strMensaje = $e->getMessage();
                }
                else
                {
                    $strMensaje = "No se pudo realizar la replanificación. Comuníquese con el Dep. de Sistemas!";
                }
                
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }
                $this->emComercial->close();
                
                if($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                    $this->emInfraestructura->rollback();
                }
                $this->emInfraestructura->close();
                
                if($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->rollback();
                }
                $this->emComunicacion->close();

                if ($this->emSoporte->getConnection()->isTransactionActive())
                {
                    $this->emSoporte->rollback();
                }
                $this->emSoporte->close();
            }
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje,
                                "objServicio"           => $objServicio,
                                "objServicioHistorial"  => $objServicioHistorial);
        return $arrayRespuesta;
    }

    /**
     * 
     * Función usada para detener una solicitud
     * 
     * @param array $arrayParametros [
     *                                  "intIdSolicitud"            => id de la solicitud
     *                                  "intIdMotivo"               => id del motivo
     *                                  "strObservacion"            => observación de la solicitud
     *                                  "strCodEmpresa"             => id de la empresa
     *                                  "strPrefijoEmpresa"         => prefijo de la empresa
     *                                  "intIdDepartamentoSession"  => id del departamento en sesión
     *                                  "intIdEmpleadoSession"      => id del usuario en sesión
     *                                  "strIpCreacion"             => ip de creación
     *                                  "strUsrCreacion"            => usuario de creación
     *                                  "objRequest"                => Objeto con el request
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 22-05-2021 Se realiza invocacion del metodo para detener todos los servicios adicionales manuales
     *                          "cancelacionSimulServicios" cuando se detiene el servicio de internet
     * 
     */
    public function detenerPlanificacion($arrayParametros)
    {
        $intIdSolicitud             = $arrayParametros["intIdSolicitud"];
        $intIdMotivo                = $arrayParametros["intIdMotivo"];
        $strObservacion             = $arrayParametros["strObservacion"];
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa          = $arrayParametros["strPrefijoEmpresa"];
        $intIdDepartamentoSession   = $arrayParametros["intIdDepartamentoSession"];
        $intIdEmpleadoSession       = $arrayParametros["intIdEmpleadoSession"];
        $objRequest                 = $arrayParametros["objRequest"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $intIdPersonaEmpresaRol     = $arrayParametros["intIdPersonaEmpresaRol"];
        $boolMostrarMsjErrorUsr     = false;
        $this->emComercial->getConnection()->beginTransaction();
        $this->emComunicacion->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        try
        {
            $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            if(!is_object($objSolicitud))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe el detalle de solicitud");
            }
            $objMotivo          = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);
            $strTipoSolicitud   = strtolower($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());
            $objServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objSolicitud->getServicioId());
                // se agrega validacion del estado del servicio para bloquear operaciones incorrectas
            if ($objServicio->getEstado() === "Activo" && ($strTipoSolicitud != 'solicitud migracion' &&
                    $strTipoSolicitud !== 'solicitud agregar equipo' &&
                    $strTipoSolicitud !== 'solicitud agregar equipo masivo' &&
                    $strTipoSolicitud !== 'solicitud cambio equipo por soporte' &&
                    $strTipoSolicitud !== 'solicitud cambio equipo por soporte masivo' &&
                    $strTipoSolicitud != 'solicitud de instalacion cableado ethernet' &&
                    $strTipoSolicitud !== 'solicitud reubicacion'))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("El servicio Actualmente se encuentra con estado Activo, no es posible detener.");
            }
            if ($strTipoSolicitud === "solicitud planificacion")
            {
                $strEstadoActual = $objServicio->getEstado();
                $objServicio->setEstado("Detenido");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                // Invocamos al metodo de detencion simultaneo de productos adicionales
                $strPlanServicio = $objServicio->getPlanId();
                if (!empty($strPlanServicio) && $strTipoSolicitud == "solicitud planificacion")
                {
                    $arrayDatosDetener = array(
                        "idPunto"      => $objServicio->getPuntoId()->getId(),
                        "idServicio"   => $objServicio->getId(),
                        "estadoActual" => $strEstadoActual,
                        "estado"       => "Detenido",
                        "observacion"  => "Se detiene el producto en simultaneo con el servicio de internet",
                        "usuario"      => $strUsrCreacion,
                        "ipCreacion"   => $strIpCreacion,
                        "idEmpresa"    => $strCodEmpresa,
                        "idPersonaRol"   => $intIdPersonaEmpresaRol,
                        "idDepartamento" => $intIdDepartamentoSession
                    );
                    $this->serviceCoordinar2->cancelacionSimulServicios($arrayDatosDetener);

                    $arrayDatosParametros = array(
                        "objServicio"     => $objServicio,
                        "strEstado"       => "Detenido",
                        "strObservacion"  => "Se detiene el producto en simultaneo con el servicio de internet",
                        "intCodEmpresa"   => $strCodEmpresa,
                        "strIpCreacion"   => $strIpCreacion,
                        "strUserCreacion" => $strUsrCreacion
                    );
                    $this->serviceCoordinar2->cancelarProdAdicionalesAut($arrayDatosParametros);
                }

                //GUARDAR INFO SERVICIO HISTORIAL
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setMotivoId($intIdMotivo);
                $objServicioHistorial->setObservacion($strObservacion);
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setEstado('Detenido');
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                $arrayCupoPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                            ->findBy(array('solicitudId' => $intIdSolicitud));
                foreach ($arrayCupoPlanificacion as $objCupoPlanificacion)
                {
                    $objCupoPlanificacion->setSolicitudId(null);
                    $objCupoPlanificacion->setCuadrillaId(null);
                    $this->emComercial->persist($objCupoPlanificacion);
                    $this->emComercial->flush();
                }
            }
            
            if ($strTipoSolicitud == "solicitud de instalacion cableado ethernet")
            {
                $strEstDEtener = 'Detenido';
                // Validamos que solo sea para producto cableado ethernet
                $arrayParametroTipos    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->get('VALIDA_PROD_ADICIONAL',
                                                                'COMERCIAL',
                                                                '',
                                                                'Solicitud cableado ethernet',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                '18');
                if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                {
                    $objCableParametro = $arrayParametroTipos[0];
                }
                if ($objServicio->getProductoId() != null &&
                    $objServicio->getProductoId()->getId() == $objCableParametro['valor1'])
                {
                    $objServicio->setEstado($strEstDEtener);
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();
                }
                // Ingresamos el historial del detalle de la solicitud
                $objServicioHist = new InfoServicioHistorial();
                $objServicioHist->setServicioId($objServicio);
                $objServicioHist->setObservacion('Se detiene el producto cableado ethernet');
                $objServicioHist->setIpCreacion($strIpCreacion);
                $objServicioHist->setUsrCreacion($strUsrCreacion);
                $objServicioHist->setFeCreacion(new \DateTime('now'));
                $objServicioHist->setEstado($strEstDEtener);
                $this->emComercial->persist($objServicioHist);
                $this->emComercial->flush();
            }
            
            $strNuevaObsDetalleSolicitud = $objSolicitud->getEstado().': ' .$objSolicitud->getObservacion().
                                            "\n".
                                           'Detenido: ' .$strObservacion;
            $objSolicitud->setMotivoId($intIdMotivo);
            $objSolicitud->setObservacion($strNuevaObsDetalleSolicitud);
            $objSolicitud->setEstado("Detenido");
            $this->emComercial->persist($objSolicitud);
            $this->emComercial->flush();

            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
            $objSolicitudHistorial = new InfoDetalleSolHist();
            $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
            $objSolicitudHistorial->setObservacion($strObservacion);
            $objSolicitudHistorial->setMotivoId($intIdMotivo);
            $objSolicitudHistorial->setIpCreacion($strIpCreacion);
            $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
            $objSolicitudHistorial->setUsrCreacion($strUsrCreacion);
            $objSolicitudHistorial->setEstado('Detenido');
            $this->emComercial->persist($objSolicitudHistorial);
            $this->emComercial->flush();

            //Cambiar estado de la tarea a Detenido
            $arrayParamsCambiarEstadoTarea  = array("observacion"   => "Detención de Orden de Trabajo",
                                                    "cargarTiempo"  => "cliente",
                                                    "estado"        => "Detenido",
                                                    "esSolucion"    => "N");

            $arrayParametrosReasignacion    = array("idEmpresa"             => $strCodEmpresa,
                                                    "prefijoEmpresa"        => $strPrefijoEmpresa,
                                                    "motivo"                => "Detención de Orden de Trabajo",
                                                    "departamento_asignado" => $intIdDepartamentoSession,
                                                    "id_departamento"       => $intIdDepartamentoSession,
                                                    "empleado_asignado"     => $intIdEmpleadoSession,
                                                    "user"                  => $strUsrCreacion,
                                                    "empleado_logueado"     => $strUsrCreacion,
                                                    "clientIp"              => $strIpCreacion);
            $arrayDetalles                  = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                              ->findBy(array('detalleSolicitudId' => $objSolicitud->getId()));
            
            $intObjDetalle=0;
            if(isset($arrayDetalles) && !empty($arrayDetalles))
            {
                foreach($arrayDetalles as $objDetalle)
                {
                    $intObjDetalle=$objDetalle->getId();
                    $arrayParametrosReasignacion['id_detalle']      = $objDetalle->getId();
                    $arrayParametrosReasignacion['fecha_ejecucion'] = (new \DateTime('now'))->format('Y-m-d H:i');

                    $arrayResultadoReasignacion  = $this->serviceSoporte->reasignarTarea($arrayParametrosReasignacion);
                    if(!$arrayResultadoReasignacion["success"])
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser reasignadas. Favor notificar a Sistemas.");
                    }
                    $strRespuestaCancelacionTarea   = $this->serviceSoporte
                                                           ->cambiarEstadoTarea($objDetalle, null, $objRequest, $arrayParamsCambiarEstadoTarea);
                    if($strRespuestaCancelacionTarea != "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser detenidas. Favor notificar a Sistemas.");
                    }
                }
            }

            //------- COMUNICACIONES --- NOTIFICACIONES
            $strContenidoCorreo = $this->objTemplating->render('planificacionBundle:Coordinar:notificacion.html.twig', 
                                                                array(  'detalleSolicitud'     => $objSolicitud,
                                                                        'detalleSolicitudHist' => $objSolicitudHistorial,
                                                                        'motivo'               => $objMotivo));

            if ($strTipoSolicitud === 'solicitud cambio equipo por soporte' || $strTipoSolicitud === 'solicitud cambio equipo por soporte masivo')
            {
                $strAsunto  = "Planificacion de Solicitud Cambio de Equipo por Soporte Detenida #" . $objSolicitud->getId();
            }
            else
            {
                $strAsunto  = "Planificacion de Solicitud de Instalacion Detenida #" . $objSolicitud->getId();
            }

            //DESTINATARIOS....
            $arrayFormasContacto    = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()->getUsrVendedor(),
                                                                                                     'Correo Electronico');
            $arrayTo                = array();
            $arrayTo[]              = 'notificaciones_telcos@telconet.ec';
            if (isset($arrayFormasContacto) && !empty($arrayFormasContacto))
            {
                foreach ($arrayFormasContacto as $arrayFormaContacto)
                {
                    $arrayTo[] = $arrayFormaContacto['valor'];
                }
            }
            $this->serviceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strContenidoCorreo);
            

            $this->emComercial->getConnection()->commit();
            $this->emComunicacion->getConnection()->commit();
            $this->emSoporte->getConnection()->commit();

            $arrayParametrosActulizar = array();
            $arrayParametrosInfoTarea = array();
            $arrayParametrosActulizar["strUsrCreacion"]  =  $objRequest->getSession()->get('user');

            $arrayParametrosInfoTarea['intDetalleId'] = $intObjDetalle;
            $arrayParametrosInfoTarea['strUsrUltMod'] = isset($arrayParametrosActulizar["strUsrCreacion"])? 
                                                        $arrayParametrosActulizar["strUsrCreacion"] : '';
            $this->serviceSoporte->actualizarInfoTarea($arrayParametrosInfoTarea);
            $strStatus  = "OK";
            $strMensaje = "Se detuvo la solicitud";

        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Error: " . $e->getMessage();
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo detener la solicitud. Comuníquese con el Dep. de Sistemas!";
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            $this->emComercial->close();
            
            if($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->rollback();
            }
            $this->emComunicacion->close();
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->rollback();
            }
            $this->emSoporte->close();
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * 
     * Función usada para rechazar una solicitud
     * 
     * @param array $arrayParametros [
     *                                  "intIdSolicitud"            => id de la solicitud
     *                                  "intIdMotivo"               => id del motivo
     *                                  "strObservacion"            => observación de la solicitud
     *                                  "arraySimultaneos"          => arreglo con servicios simultáneos usados en TN
     *                                  "strCodEmpresa"             => id de la empresa
     *                                  "strPrefijoEmpresa"         => prefijo de la empresa
     *                                  "strIpCreacion"             => ip de creación
     *                                  "strUsrCreacion"            => usuario de creación
     *                                  "objRequest"                => Objeto con el request
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 1.1 06-04-2021 Flujo para Rechazar Orden de trabajo para equipo Extender Dual Band
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 1.2 17-05-2021 - Se anexa validacion para que al rechazar la OT de servicio de internet, se rechacen tambien
     *                            los servicios adicionales parametrizados.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.1 13-08-2021 Se agrega Anulación para producto adicional parametrizado ECDF.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.2 22-08-2021 Se realiza invocacion del metodo para rechazar todos los servicios adicionales manuales
     *                          "cancelacionSimulServicios" cuando se rechace el servicio de internet
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 03-01-2022 Se elimina envío de parámetro strValorCaractTipoOntNuevo a la función recreaSolicitudCambioOntTraslado por cambio 
     *                         en dicha función para permitir Extender para ZTE
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.4 02-03-2023 Se agrega validacion por Prefijo de Empresa para Ecuanet.
     * 
     */
    public function rechazarPlanificacion($arrayParametros)
    {
        $intIdSolicitud             = $arrayParametros["intIdSolicitud"];
        $intIdMotivo                = $arrayParametros["intIdMotivo"];
        $strObservacion             = $arrayParametros["strObservacion"];
        $arraySimultaneos           = $arrayParametros["arraySimultaneos"];
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa          = $arrayParametros["strPrefijoEmpresa"];
        $objRequest                 = $arrayParametros["objRequest"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $strEmpleadoSession         = $arrayParametros["strEmpleadoSession"];
        $intIdDepartamento          = $arrayParametros["intIdDepartamento"];
        $intIdPersonaEmpresaRol     = $arrayParametros["intIdPersonaEmpresaRol"];
        $boolFalse                  = false;
        $objProductoMcAfee          = null;
        $strTipoOrden               = "";
        $strRechazoPlanifInternetMd = "";
        $boolMostrarMsjErrorUsr     = false;
        $this->emComercial->getConnection()->beginTransaction();
        $this->emComunicacion->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        try
        {
            $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            if(!is_object($objSolicitud))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe el detalle de solicitud");
            }
            $objMotivo          = $this->emComercial->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);
            $strTipoSolicitud   = strtolower($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());
            $objServicio        = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objSolicitud->getServicioId());
                // se agrega validacion del estado del servicio para bloquear operaciones incorrectas
            if ($objServicio->getEstado() == "Activo" &&
                ($strTipoSolicitud !== 'solicitud migracion' &&
                 $strTipoSolicitud !== 'solicitud agregar equipo' &&
                 $strTipoSolicitud !== 'solicitud agregar equipo masivo' &&
                 $strTipoSolicitud !== 'solicitud cambio equipo por soporte' &&
                 $strTipoSolicitud !== 'solicitud cambio equipo por soporte masivo' &&
                 $strTipoSolicitud !== 'solicitud de instalacion cableado ethernet' &&
                 $strTipoSolicitud !== 'solicitud reubicacion'))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("El servicio Actualmente se encuentra con estado Activo, no es posible rechazar.");
            }
            
            $strTipoOrden = $objServicio->getTipoOrden();
            if($strPrefijoEmpresa === "MD" && $strTipoOrden === "T" 
               && is_object($objServicio->getPlanId()) 
               && ($strTipoSolicitud == "solicitud planificacion" || $strTipoSolicitud === "solicitud agregar equipo"))
            {
                $strValorCaractMotivoCambioOnt      = "CAMBIO ONT POR AGREGAR EXTENDER";
                $arrayRespuestaRecreaSolCambioOnt   = $this->serviceInfoServicio
                                                           ->recreaSolicitudCambioOntTraslado(
                                                                                array(  
                                                                                        "objServicioPlanDestinoEnPunto"         => $objServicio,
                                                                                        "strCodEmpresa"                         => $strCodEmpresa,
                                                                                        "strUsrCreacion"                        => $strUsrCreacion,
                                                                                        "strIpCreacion"                         => $strIpCreacion,
                                                                                        "strValorCaractMotivoCambioOnt"         => 
                                                                                        $strValorCaractMotivoCambioOnt));
                if($arrayRespuestaRecreaSolCambioOnt["status"] === "ERROR")
                {
                    $boolMostrarMsjErrorUsr = true;
                    throw new \Exception($arrayRespuestaRecreaSolCambioOnt["mensaje"]);
                }
            }
            
            if ($strTipoSolicitud == "solicitud planificacion" ||
                (($strTipoSolicitud == "solicitud agregar equipo" || $strTipoSolicitud == "solicitud agregar equipo masivo") &&
                is_object($objServicio->getProductoId()) &&
                ($objServicio->getProductoId()->getNombreTecnico() === "WIFI_DUAL_BAND" ||
                 $objServicio->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND" ||
                 $objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB") )  )
            {
                $strEstadoActual = $objServicio->getEstado();
                $objServicio->setEstado("Rechazada");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                // Rechazamos los servicios adicionales parametrizados
                if($strTipoSolicitud == "solicitud planificacion")
                {
                    $arrayDatosParametros = array(
                        "objServicio"     => $objServicio,
                        "strEstado"       => "Rechazada",
                        "strObservacion"  => "Se rechaza servicio adicional con servicio de internet",
                        "intCodEmpresa"   => $strCodEmpresa,
                        "strIpCreacion"   => $strIpCreacion,
                        "strUserCreacion" => $strUsrCreacion
                    );
                    $this->serviceCoordinar2->cancelarProdAdicionalesAut($arrayDatosParametros);
                }

                // Invocamos al metodo de rechazo simultaneo de productos adicionales
                $strPlanServicio = $objServicio->getPlanId();
                if (!empty($strPlanServicio) && $strTipoSolicitud == "solicitud planificacion")
                {
                    $arrayDatosRechazar = array(
                        "idPunto"      => $objServicio->getPuntoId()->getId(),
                        "idServicio"   => $objServicio->getId(),
                        "estadoActual" => $strEstadoActual,
                        "estado"       => "Rechazada",
                        "observacion"  => "Se rechaza el producto en simultaneo con el servicio de internet",
                        "usuario"      => $strUsrCreacion,
                        "ipCreacion"   => $strIpCreacion,
                        "idEmpresa"    => $strCodEmpresa,
                        "idPersonaRol"   => $intIdDepartamento,
                        "idDepartamento" => $intIdPersonaEmpresaRol
                    );
                    $this->serviceCoordinar2->cancelacionSimulServicios($arrayDatosRechazar);
                }
                
                //Productos adicionales supeditado al estado del internet
                //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA SUPEDITAR EL ESTADO DEL INTERNET AL RECHAZAR
                $arrayNombreTecnicoPermitido = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('NOMBRE_TECNICO_PROD_PERMITIDOS_FLUJO_RECHAZADA_Y_ANULADA',//nombre parametro cab
                                                    'PLANIFICACION', //modulo cab
                                                    'OBTENER_PROD',//proceso cab
                                                    'PRODUCTO_TV', //descripcion det
                                                    '','','','','',
                                                    $strCodEmpresa); //empresa
                foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
                {
                    $arrayProdNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
                }
                if(is_object($objServicio) && $objServicio->getPuntoId()->getId() && $strTipoOrden === "N")
                {
                    $arrayServiciosxPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                        ->findBy(array('puntoId' => $objServicio->getPuntoId()->getId()));
                    foreach($arrayServiciosxPunto as $objServicioxPunto)
                    {
                        if(is_object($objServicioxPunto) && is_object($objServicioxPunto->getProductoId()) && 
                                     in_array($objServicioxPunto->getProductoId()->getNombreTecnico(), $arrayProdNombreTecnico))
                        {
                            //Se cambia el estado del servicio adicional
                            $objServicioxPunto->setEstado("Rechazada");
                            $this->emComercial->persist($objServicioxPunto);
                            $this->emComercial->flush();

                            //GUARDAR INFO SERVICIO HISTORIAL
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicioxPunto);
                            $objServicioHistorial->setIpCreacion($strIpCreacion);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setObservacion('Se rechazó el servicio');
                            $objServicioHistorial->setMotivoId($intIdMotivo);
                            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioHistorial->setEstado('Rechazada');
                            $this->emComercial->persist($objServicioHistorial);
                            $this->emComercial->flush();
                            
                            //Se cambia el estado de la infoServProdCaract
                            $arrayServProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array('servicioId' => $objServicioxPunto->getId()));
                            foreach($arrayServProdCaract as $objServProdCaract)
                            {
                                if(is_object($objServProdCaract))
                                {
                                    $objServProdCaract->setEstado("Eliminado");
                                    $this->emComercial->persist($objServProdCaract);
                                    $this->emComercial->flush();
                            
                                }
                            }
                        }
                    }
                }

                //Preguntamos si es activación simultánea y consultamos el estado del servicio tradicional
                $arrayCouSim          = $this->servicePlanificar->getIdTradInstSim($objServicio->getId());
                $intIdServTradicional = $arrayCouSim[0];
                $intIdServCou         = $arrayCouSim[1];

                if ($intIdServTradicional !== null)
                {
                    $objServicioCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServCou);
                    if (!$objServicioCou)
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("No existe servicio COU LINEAS TELEFONIA FIJA relacionado");
                    }
                    else
                    {
                        $arrayPeticiones['strActSimu']        = 'S';
                        $arrayPeticiones['intIdServicio']     = $intIdServCou;
                        $arrayPeticiones['strUser']           = $strUsrCreacion;
                        $arrayPeticiones['strIpClient']       = $strIpCreacion;
                        $arrayPeticiones['strPrefijoEmpresa'] = $strPrefijoEmpresa;
                        $arrayPeticiones['strEstado']         = 'Rechazada';

                        $objServicioCou->setEstado("Rechazada");
                        $this->emComercial->persist($objServicioCou);
                        $this->emComercial->flush();

                        //GUARDAR INFO SERVICIO HISTORIAL
                        $objServicioHistorialCou = new InfoServicioHistorial();
                        $objServicioHistorialCou->setServicioId($objServicioCou);
                        $objServicioHistorialCou->setIpCreacion($strIpCreacion);
                        $objServicioHistorialCou->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorialCou->setObservacion($strObservacion.'-'.'Activación Simultánea');
                        $objServicioHistorialCou->setMotivoId($intIdMotivo);
                        $objServicioHistorialCou->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorialCou->setEstado('Rechazada');
                        $this->emComercial->persist($objServicioHistorialCou);
                        $this->emComercial->flush();

                        //Consulto si es FIJA ANALOGA o TRUNK
                        $objCaract  = $this->serviceServicioTenico->getServicioProductoCaracteristica(  $objServicioCou,
                                                                                                        'CATEGORIAS TELEFONIA',
                                                                                                        $objServicioCou->getProductoId());

                        if(is_object($objCaract))
                        {
                            $strCategoria                      = $objCaract->getValor();
                            $arrayPeticiones['strCategoria']   = $strCategoria;
                        }

                        if ($strCategoria == 'FIJA ANALOGA' || $strCategoria == 'FIJA SIP TRUNK')
                        {
                            //Consultamos el servicio L3MPLS relacionado con la activación simultánea
                            $arrayCouSimFija      = $this->servicePlanificar->getIdServInstSim($objServicio->getId());
                            $intIdServTradSim     = $arrayCouSimFija[0];

                            $objServicioCouFija = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServTradSim);
                            if (!$objServicioCouFija)
                            {
                                $boolMostrarMsjErrorUsr = true;
                                throw new \Exception("No existe servicio COU LINEAS TELEFONIA FIJA relacionado");
                            }
                            else
                            {
                                $objServicioCouFija->setEstado("Rechazada");
                                $this->emComercial->persist($objServicioCouFija);
                                $this->emComercial->flush();

                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objServicioHistorialCou = new InfoServicioHistorial();
                                $objServicioHistorialCou->setServicioId($objServicioCouFija);
                                $objServicioHistorialCou->setIpCreacion($strIpCreacion);
                                $objServicioHistorialCou->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorialCou->setObservacion($strObservacion.'-'.'Activación Simultánea');
                                $objServicioHistorialCou->setMotivoId($intIdMotivo);
                                $objServicioHistorialCou->setUsrCreacion($strUsrCreacion);
                                $objServicioHistorialCou->setEstado('Rechazada');
                                $this->emComercial->persist($objServicioHistorialCou);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
                    
                //liberar caracteristica de correo electronico para planes que incluyan mcafee
                if (is_object($objServicio->getPlanId()) && ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN"))
                {
                    if($strTipoSolicitud === "solicitud planificacion")
                    {
                        $strRechazoPlanifInternetMd = "SI";
                    }
                    $arrayDetallesPlanServicio  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                    ->findByPlanIdYEstado($objServicio->getPlanId()->getId(),"Activo");

                    foreach($arrayDetallesPlanServicio as $objDetallePlanServicio)
                    {
                        $objProductoDetallePlan     = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                        ->find($objDetallePlanServicio->getProductoId());
                        if(is_object($objProductoDetallePlan))
                        {
                            $boolVerificaMacAfeeEnPlan  = strpos($objProductoDetallePlan->getDescripcionProducto(), 'I. PROTEGIDO MULTI');

                            if($boolVerificaMacAfeeEnPlan !== $boolFalse)
                            {
                                $objProductoMcAfee = $objProductoDetallePlan;
                            }
                        }
                    }
                    if (is_object($objProductoMcAfee))
                    {
                        $objServProdCaractCorreo = $this->serviceServicioTenico->getServicioProductoCaracteristica( $objServicio, 
                                                                                                                    "CORREO ELECTRONICO",
                                                                                                                    $objProductoMcAfee
                                                                                                                    ); 
                        if (is_object($objServProdCaractCorreo))
                        {
                            $strValorAntesCorreo  = $objServProdCaractCorreo->getValor();
                            $strEstadoAntesCorreo = $objServProdCaractCorreo->getEstado();
                            $objServProdCaractCorreo->setValor('');
                            $objServProdCaractCorreo->setEstado('Eliminado');
                            $objServProdCaractCorreo->setFeUltMod(new \DateTime('now'));
                            $objServProdCaractCorreo->setUsrUltMod($strUsrCreacion);
                            $this->emComercial->persist($objServProdCaractCorreo);
                            $this->emComercial->flush();

                            //REGISTRA EN LA TABLA DE HISTORIAL
                            $entityServicioHistorial = new InfoServicioHistorial();
                            $entityServicioHistorial->setServicioId($objServicio);
                            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $entityServicioHistorial->setIpCreacion($strIpCreacion);
                            $entityServicioHistorial->setObservacion('Se actualizo caracteristica CORREO ELECTRONICO con ID '.
                                                                     $objServProdCaractCorreo->getId().' : <br>'.
                                                                     'Valores Anteriores: <br>'.  
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                     'Valores Actuales: <br>'.  
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                            $entityServicioHistorial->setAccion('actualizaCaracteristica');
                            $entityServicioHistorial->setEstado($objServicio->getEstado());
                            $this->emComercial->persist($entityServicioHistorial);
                            $this->emComercial->flush();
                        }
                    }
                }
                
                if ((($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") && is_object($objServicio->getPlanId())) 
                    || (is_object($objServicio->getProductoId()) 
                        && ($objServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                            || $objServicio->getProductoId()->getNombreTecnico() === "TELCOHOME")) 
                    || ($strPrefijoEmpresa == "TNP" && is_object($objServicio->getPlanId()))
                   )
                {
                    $arrayRespuestaLiberaSplitter   = $this->serviceInterfaceElemento
                                                           ->liberarInterfaceSplitter(array("objServicio"           => $objServicio,
                                                                                            "strUsrCreacion"        => $strUsrCreacion,
                                                                                            "strIpCreacion"         => $strIpCreacion,
                                                                                            "strProcesoLibera"      => " por rechazo del servicio",
                                                                                            "strPrefijoEmpresa"     => $strPrefijoEmpresa));
                    $strStatusLiberaSplitter        = $arrayRespuestaLiberaSplitter["status"];
                    $strMensajeLiberaSplitter       = $arrayRespuestaLiberaSplitter["mensaje"];
                    if($strStatusLiberaSplitter === "ERROR")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception($strMensajeLiberaSplitter);
                    }
                }
                elseif ($strPrefijoEmpresa == 'TN')
                {
                    if (is_object($objServicio->getProductoId()))
                    {
                        //verificar si es GPON_MPLS
                        $booleanTipoRedGpon = false;
                        $objCaractTipoRed   = $this->serviceServicioTenico->getServicioProductoCaracteristica($objServicio,
                                                                                                              "TIPO_RED",
                                                                                                              $objServicio->getProductoId());
                        if(is_object($objCaractTipoRed))
                        {
                            $arrayParVerTipoRed = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne('NUEVA_RED_GPON_TN',
                                                                                'COMERCIAL',
                                                                                '',
                                                                                'VERIFICAR TIPO RED',
                                                                                'VERIFICAR_GPON',
                                                                                $objCaractTipoRed->getValor(),
                                                                                '',
                                                                                '',
                                                                                '');
                            if(isset($arrayParVerTipoRed) && !empty($arrayParVerTipoRed))
                            {
                                $booleanTipoRedGpon = true;
                            }
                        }
                        //si es wifi se liberan los puertos
                        $strNombreProducto = $objServicio->getProductoId()->getDescripcionProducto();
                        if($booleanTipoRedGpon)
                        {
                            $arrayRespuestaLiberaSplitter = $this->serviceInterfaceElemento->liberarInterfaceSplitter(
                                                                                    array("objServicio"        => $objServicio,
                                                                                          "strUsrCreacion"     => $strUsrCreacion,
                                                                                          "strIpCreacion"      => $strIpCreacion,
                                                                                          "booleanTipoRedGpon" => $booleanTipoRedGpon,
                                                                                          "strVerificaLiberacion" => "SI",
                                                                                          "strProcesoLibera"   => " por rechazo del servicio",
                                                                                          "strPrefijoEmpresa"  => $strPrefijoEmpresa));
                            $strStatusLiberaSplitter    = $arrayRespuestaLiberaSplitter["status"];
                            $strMensajeLiberaSplitter   = $arrayRespuestaLiberaSplitter["mensaje"];
                            if($strStatusLiberaSplitter === "ERROR")
                            {
                                throw new \Exception($strMensajeLiberaSplitter);
                            }
                        }
                        elseif ($strNombreProducto == 'INTERNET WIFI')
                        {
                            $arrayParams                    = array();
                            $arrayParams['intIdServicio']   = $objServicio->getId();
                            $arrayParams['strUsrCreacion']  = $strUsrCreacion;
                            $arrayParams["strIpCreacion"]   = $strIpCreacion;
                            $arrayResultadoLiberaPuertoWifi = $this->serviceInfoWifi->liberarPuertoWifi($arrayParams);
                            if($arrayResultadoLiberaPuertoWifi['strStatus'] == 'ERROR')
                            {
                                $boolMostrarMsjErrorUsr = true;
                                throw new \Exception($arrayResultadoLiberaPuertoWifi['strMensaje']);
                            }
                        }
                        elseif ($strNombreProducto == 'L3MPLS' || $strNombreProducto == 'Internet Dedicado')
                        {
                            $arrayParams['intIdServicio']   = $objServicio->getId();
                            $strMensajeReversaFactib        = $this->serviceServicioTenico->reversaFactibilidad($arrayParams);
                            if($strMensajeReversaFactib)
                            {
                                $boolMostrarMsjErrorUsr = true;
                                throw new \Exception($strMensajeReversaFactib);
                            }
                        }
                        if ($objServicio->getProductoId()->getEsConcentrador() == 'SI')
                        {
                            $arrayParams['intIdServicio']   = $objServicio->getId();
                            $arrayResult                    = $this->serviceServicioTenico->getServiciosPorConcentrador($arrayParams);
                            if ($arrayResult['strMensaje'])
                            {
                                if ($arrayResult['strStatus'] == 'OK')
                                {
                                    $boolMostrarMsjErrorUsr = true;
                                    throw new \Exception('<b>No se puede Eliminar el servicio concentrador, debido a que tiene extremos '
                                                         .'enlazados:</b> <br><br>' . $arrayResult['strMensaje']);
                                }
                                else
                                {
                                    $boolMostrarMsjErrorUsr = true;
                                    throw new \Exception($arrayResult['strMensaje']);
                                }
                            }
                        }
                        //se procede a eliminar todas las caracteristicas SERVICIO_MISMA_ULTIMA_MILLA que dependan de este servicio
                        $this->serviceServicioTenico->eliminarDependenciaMismaUM($objServicio, $strUsrCreacion, $strIpCreacion);
                        
                
                        //INI VALIDAMOS SI EXISTEN TAREAS POR SOLICITUD DE EXCEDENTE DE MATERIAL Y SE FINALIZA
                        $objParametroTarea  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                              ->findOneBy(array('nombreParametro'=>'TAREA EXCESO DE MATERIAL'));

                        if(is_object($objParametroTarea) && !empty($objParametroTarea))
                        {
                            $objParametroTareaDet   = $this->emGeneral->getRepository("schemaBundle:AdmiParametroDet")
                                                                      ->findOneBy(array('descripcion'=>'TAREA A FACTURACIÓN',
                                                                                        'parametroId'=>$objParametroTarea->getId(),
                                                                                    ));
                            //Obtenemos las tareas anteriores
                            $arrayTareasxSol    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->getTareasxSolicitudxProceso(array(
                                                                        'detalleSolId'    => $objSolicitud->getId(),
                                                                        'nombreTarea'     => $objParametroTareaDet->getValor2(),
                                                                        'nombreProceso'   => $objParametroTareaDet->getValor1()));
                            if($arrayTareasxSol['estado'] === 'ERROR')
                            {
                                $boolMostrarMsjErrorUsr = true;
                                throw new \Exception('Error  al consultar tareas por solicitud, '
                                                   . 'por favor comunicar a Sistemas.'.$arrayTareasxSol['mensaje']);
                            }
                            if(!empty($arrayTareasxSol))
                            {   //Finalizamos las tareas anteriores
                                $objFechaEjecucion = new \DateTime('now');
                                $strFecha = $objFechaEjecucion->format('Y-m-d');
                                $strObservacionFinTarea = 'Tarea finalizada automáticamente por proceso '
                                                        . 'de revalidación de solicitud de exceso de material';
                                foreach($arrayTareasxSol as $tarea)
                                {
                                    $arrayParametrosFinTarea    = array(
                                                                        'idEmpresa'               => $strCodEmpresa,
                                                                        'prefijoEmpresa'          => $strPrefijoEmpresa,
                                                                        'idDetalle'               => $tarea['detalleId'],
                                                                        'fechaEjecucion'          => $strFecha,
                                                                        'horaEjecucion'           => $objFechaEjecucion->format('H:i:sP'),
                                                                        'idAsignado'              => null,
                                                                        'observacion'             => $strObservacionFinTarea,
                                                                        'empleado'                => $strEmpleadoSession,
                                                                        'usrCreacion'             => $strUsrCreacion,
                                                                        'ipCreacion'              => $strIpCreacion,
                                                                        'numeroTarea'             => $tarea['numeroTarea'],
                                                                        'accionTarea'             => 'finalizada'
                                                                       );
                                    $arrayRespTareaFin  = $this->serviceSoporte->finalizarTarea($arrayParametrosFinTarea);
                                    if($arrayRespTareaFin['status'] === 'ERROR')
                                    {
                                        throw new \Exception('Error  al finalizar tareas por solicitud, '
                                                           . 'por favor comunicar a Sistemas.'.$arrayRespTareaFin['mensaje']);
                                    }        
                                    /* Envío de Correo al asesor como seguimiento */
                                    $strAsunto = "Notificación de validación de excedente de material | ".$objServicio->getPuntoId()->getLogin() ;
                                    $arrayFormasContactoAsesor  = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                       ->getContactosByLoginPersonaAndFormaContacto(
                                                                           $objServicio->getPuntoId()->getUsrVendedor(),
                                                                           'Correo Electronico');

                                    if($arrayFormasContactoAsesor)
                                    {
                                        foreach($arrayFormasContactoAsesor as $arrayformaContacto)
                                        {
                                            $arrayDestinatario[] = $arrayformaContacto['valor'];
                                        }
                                    }
                                    $arrayParametrosMail = array(
                                                                "login"       => $objServicio->getPuntoId()->getLogin(),
                                                                "producto"    => $objServicio->getProductoId()->getDescripcionProducto(),
                                                                "mensaje"     => 'Tarea #'.$tarea['numeroTarea'].' '.$strObservacionFinTarea
                                                                );

                                    $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                                                        $arrayDestinatario,
                                                                                        'NOTIEXCMATASE',
                                                                                        $arrayParametrosMail,
                                                                                        $strCodEmpresa,
                                                                                        '',
                                                                                        '',
                                                                                        null,
                                                                                        false,
                                                                                        'notificaciones_telcos@telconet.ec'
                                                                                       );
                                }
                            }
                        }
                        //FIN VALIDAMOS SI EXISTEN TAREAS POR SOLICITUD DE EXCEDENTE DE MATERIAL Y SE FINALIZA
                        //INI VALIDAMOS SOLICITUD DE EXCEDENTE DE MATERIAL ATADA AL SERVICIO Y SE ANULA
                        $entityTipoSolicitud    = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                    ->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");
                        $objDetalleSolicitudExc = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneBy(array( "servicioId"      => $objServicio->getId(),
                                                                                       "estado"          => 'Pendiente',
                                                                                       "tipoSolicitudId" => $entityTipoSolicitud));
                        if($objDetalleSolicitudExc)
                        {
                            //ACTUALIZAR DE ESTADO DE SOLICITUD DE MATERIALES EXCEDENTES
                            $objDetalleSolicitudExc->setEstado('Anulado');
                            $this->emComercial->persist($objDetalleSolicitudExc);
                            $this->emComercial->flush();

                            $strSeguimiento =  'Se anula solicitud de excedente de material'
                                            .  ' #'.$objDetalleSolicitudExc->getId().'<br>'
                                            .  ' porque se rechaza solicitud de planificación';
                            $strMail        =  'Se anula solicitud de excedente de material'
                                            .  ' #'.$objDetalleSolicitudExc->getId()
                                            .  ' porque se rechaza solicitud de planificación';
                            //CREO HISTORIAL PARA SOLICITUD  DE MATERIALES EXCEDENTES
                            $entityDetSolHistM = new InfoDetalleSolHist();
                            $entityDetSolHistM->setDetalleSolicitudId($objDetalleSolicitudExc);
                            $entityDetSolHistM->setObservacion($strSeguimiento);
                            $entityDetSolHistM->setIpCreacion($strIpCreacion);
                            $entityDetSolHistM->setFeCreacion(new \DateTime('now'));
                            $entityDetSolHistM->setUsrCreacion($strUsrCreacion);
                            $entityDetSolHistM->setEstado('Anulado');  
                            $this->emComercial->persist($entityDetSolHistM);
                            $this->emComercial->flush();  

                            //INSERTAR INFO SERVICIO HISTORIAL
                            $entityServicioHist = new InfoServicioHistorial();
                            $entityServicioHist->setServicioId($objServicio);
                            $entityServicioHist->setObservacion('<b>Seguimiento:</b> '.$strSeguimiento);
                            $entityServicioHist->setIpCreacion($strIpCreacion);
                            $entityServicioHist->setFeCreacion(new \DateTime('now'));
                            $entityServicioHist->setUsrCreacion($strUsrCreacion);
                            $entityServicioHist->setEstado($objServicio->getEstado());
                            $entityServicioHist->setAccion('validaExcedenteMaterial');
                            $this->emComercial->persist($entityServicioHist);
                            $this->emComercial->flush();

                            /* Envío de Correo al asesor como seguimiento */
                            $strAsunto = "Notificación de validación de excedente de material | " . $objServicio->getPuntoId()->getLogin() ;

                            //Obtenemos la forma de contacto del asesor
                            $arrayFormasContactoAsesor  = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoPersona')
                                                               ->getContactosByLoginPersonaAndFormaContacto(
                                                                   $objServicio->getPuntoId()->getUsrVendedor(),'Correo Electronico');
                            if($arrayFormasContactoAsesor)
                            {
                                foreach($arrayFormasContactoAsesor as $arrayformaContacto)
                                {
                                    $arrayDestinatario[] = $arrayformaContacto['valor'];
                                }
                            }
                            $arrayParametrosMail = array(
                                                        "login"      => $objServicio->getPuntoId()->getLogin(),
                                                        "producto"   => $objServicio->getProductoId()->getDescripcionProducto(),
                                                        "mensaje"    => $strMail
                                                        );

                            $this->serviceEnvioPlantilla->generarEnvioPlantilla(
                                                         $strAsunto,
                                                         $arrayDestinatario,
                                                         'NOTIEXCMATASE',
                                                         $arrayParametrosMail,
                                                         $strCodEmpresa,
                                                         '',
                                                         '',
                                                         null,
                                                         false,
                                                         'notificaciones_telcos@telconet.ec'
                                                        );

                        }
                        //FIN VERIFICAMOS SI EXISTE SOLICITUD DE EXCEDENTE DE MATERIAL ATADA A LA DE PLANIFICACIÓN
                    }
                }
                //GUARDAR INFO SERVICIO HISTORIAL
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setObservacion($strObservacion);
                $objServicioHistorial->setMotivoId($intIdMotivo);
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setEstado('Rechazada');
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                if ($strPrefijoEmpresa === "MD" && $strTipoOrden === "T")
                {
                    if(is_object($objServicio->getProductoId()) 
                        && ($objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB"))
                    {
                        $arrayRespuestaRecreaSolWyAP = $this->serviceInfoServicio->recreaSolicitudWyApTraslado(
                                                                                        array(  "objServicioDestino"    => $objServicio,
                                                                                                "strOpcion"             => "TRASLADO",
                                                                                                "strCodEmpresa"         => $strCodEmpresa,
                                                                                                "strUsrCreacion"        => $strUsrCreacion,
                                                                                                "strIpCreacion"         => $strIpCreacion));
                        if($arrayRespuestaRecreaSolWyAP["status"] === "ERROR")
                        {
                            $boolMostrarMsjErrorUsr = true;
                            throw new \Exception($arrayRespuestaRecreaSolWyAP["mensaje"]);
                        }
                    }
                    else if(is_object($objServicio->getProductoId()) 
                            && ($objServicio->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND"))
                    {
                        $arrayRespuestaRecreaSolEDB = $this->serviceInfoServicio->recreaSolicitudEdbTraslado(
                                                                                        array(  "objServicioDestino"    => $objServicio,
                                                                                                "strOpcion"             => "TRASLADO",
                                                                                                "strCodEmpresa"         => $strCodEmpresa,
                                                                                                "strUsrCreacion"        => $strUsrCreacion,
                                                                                                "strIpCreacion"         => $strIpCreacion));
                        if($arrayRespuestaRecreaSolEDB["status"] === "ERROR")
                        {
                            throw new \Exception($arrayRespuestaRecreaSolEDB["mensaje"]);
                        }
                    }
                    else
                    {
                        /* Para servicios con tipo de orden traslados se debe validar si es que existe alguna solicitud
                         * de agregar equipo pendiente en el nuevo punto y de existir, se debe proceder a crearla nuevamente 
                         * en el punto origen del traslado en estado PrePlanificada
                         */
                        $arrayParametrosTrasladoSol = array();
                        $arrayParametrosTrasladoSol["objServicio"]    = $objServicio;
                        $arrayParametrosTrasladoSol["strUsrCreacion"] = $strUsrCreacion;
                        $arrayParametrosTrasladoSol["strIpCreacion"]  = $strIpCreacion;
                        $arrayParametrosTrasladoSol["strEmpresaCod"]  = $strCodEmpresa;
                        $this->serviceInfoServicio->recrearSolicitudesPorTraslado($arrayParametrosTrasladoSol);
                    }
                    $objProductoInternet    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                                                  "empresaCod"    => $strCodEmpresa, 
                                                                                  "estado"        => "Activo"));
                    /*
                     * se regulariza caracteristica TRASLADO en servicios adicionales a rechazar y
                     * en caso de que el servicio se encuentre Trasladado pase nuevamente a estado Activo
                     */
                    if (is_object($objProductoInternet))
                    {
                        $objSpcTraslado = $this->serviceServicioTenico->getServicioProductoCaracteristica(  $objServicio, 
                                                                                                            "TRASLADO", 
                                                                                                            $objProductoInternet);
                        if (is_object($objSpcTraslado))
                        {
                            $strValorAntesCorreo  = $objSpcTraslado->getValor();
                            $strEstadoAntesCorreo = $objSpcTraslado->getEstado();
                            $objSpcTraslado->setValor('');
                            $objSpcTraslado->setEstado('Eliminado');
                            $objSpcTraslado->setFeUltMod(new \DateTime('now'));
                            $objSpcTraslado->setUsrUltMod($strUsrCreacion);
                            $this->emComercial->persist($objSpcTraslado);
                            $this->emComercial->flush();
                            //REGISTRA EN LA TABLA DE HISTORIAL
                            $objServicioHistorial = new InfoServicioHistorial();
                            $objServicioHistorial->setServicioId($objServicio);
                            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $objServicioHistorial->setIpCreacion($strIpCreacion);
                            $objServicioHistorial->setObservacion('Se actualizó característica TRASLADO con ID '.
                                                                     $objSpcTraslado->getId().' : <br>'.
                                                                     'Valores Anteriores: <br>'.  
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                     'Valores Actuales: <br>'.  
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                     '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                            $objServicioHistorial->setAccion('actualizaCaracteristica');
                            $objServicioHistorial->setEstado($objServicio->getEstado());
                            $this->emComercial->persist($objServicioHistorial);
                            $this->emComercial->flush();
                            $objServicioOrigenTraslado = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strValorAntesCorreo);
                            if (is_object($objServicioOrigenTraslado) && $objServicioOrigenTraslado->getEstado() == "Trasladado")
                            {
                                $objServicioOrigenTraslado->setEstado("Activo");
                                $this->emComercial->persist($objServicioOrigenTraslado);
                                $this->emComercial->flush();
                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objHistorialServicioAdicional = new InfoServicioHistorial();
                                $objHistorialServicioAdicional->setServicioId($objServicioOrigenTraslado);
                                $objHistorialServicioAdicional->setIpCreacion($strIpCreacion);
                                $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                $objHistorialServicioAdicional->setObservacion("Se reactiva servicio por rechazo de Traslado del servicio ".
                                                                               "en el punto :".
                                                                               $objServicio->getPuntoId()->getLogin());
                                $objHistorialServicioAdicional->setMotivoId($intIdMotivo);
                                $objHistorialServicioAdicional->setUsrCreacion($strUsrCreacion);
                                $objHistorialServicioAdicional->setEstado('Activo');
                                $this->emComercial->persist($objHistorialServicioAdicional);
                                $this->emComercial->flush();
                            }
                        }
                    }
                    if($strRechazoPlanifInternetMd === "SI")
                    {
                        //si el tipo de orden es traslado, los servicios adicionales tb son rechazados automaticamente
                        $arrayServiciosAdicionalesPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                             ->findBy(array("puntoId" => $objServicio->getPuntoId()));
                        $arrayEstadosServicios = array('Rechazado','Rechazada','Anulado','Anulada','Eliminado',
                                                       'Eliminada','Cancel','Cancelado','Cancelada');
                        foreach ($arrayServiciosAdicionalesPunto as $objServicioAdicional)
                        {//AQUI HAY QUE CAMBIAR
                            if (!in_array($objServicioAdicional->getEstado(),$arrayEstadosServicios)
                                && (
                                    (is_object($objServicioAdicional->getPlanId())) 
                                    || (is_object($objServicioAdicional->getProductoId()) 
                                        && $objServicioAdicional->getProductoId()->getNombreTecnico() !== "EXTENDER_DUAL_BAND")
                                   )
                                )
                            {
                                if(is_object($objServicioAdicional->getProductoId()) 
                                   && ($objServicioAdicional->getProductoId()->getNombreTecnico() === "WDB_Y_EDB"))
                                {
                                    $arrayRespuestaRecreaSolWyAP = $this->serviceInfoServicio->recreaSolicitudWyApTraslado(
                                                                                        array(  "objServicioDestino"    => $objServicioAdicional,
                                                                                                "strOpcion"             => "TRASLADO",
                                                                                                "strCodEmpresa"         => $strCodEmpresa,
                                                                                                "strUsrCreacion"        => $strUsrCreacion,
                                                                                                "strIpCreacion"         => $strIpCreacion));
                                    if($arrayRespuestaRecreaSolWyAP["status"] === "ERROR")
                                    {
                                        $boolMostrarMsjErrorUsr = true;
                                        throw new \Exception($arrayRespuestaRecreaSolWyAP["mensaje"]);
                                    }
                                }
                                
                                $objServicioAdicional->setEstado("Rechazada");
                                $this->emComercial->persist($objServicioAdicional);
                                $this->emComercial->flush();
                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objHistorialServicioAdicional = new InfoServicioHistorial();
                                $objHistorialServicioAdicional->setServicioId($objServicioAdicional);
                                $objHistorialServicioAdicional->setIpCreacion($strIpCreacion);
                                $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                $objHistorialServicioAdicional->setObservacion($strObservacion);
                                $objHistorialServicioAdicional->setMotivoId($intIdMotivo);
                                $objHistorialServicioAdicional->setUsrCreacion($strUsrCreacion);
                                $objHistorialServicioAdicional->setEstado('Rechazada');
                                $this->emComercial->persist($objHistorialServicioAdicional);
                                $this->emComercial->flush();

                                //se regulariza caracteristica TRASLADO en servicios adicionales a rechazar
                                if (is_object($objProductoInternet))
                                {
                                    $objSpcTrasladoAdic = $this->serviceServicioTenico->getServicioProductoCaracteristica(  $objServicioAdicional, 
                                                                                                                            "TRASLADO", 
                                                                                                                            $objProductoInternet);
                                    if (is_object($objSpcTrasladoAdic))
                                    {
                                        $strValorAntesCorreo  = $objSpcTrasladoAdic->getValor();
                                        $strEstadoAntesCorreo = $objSpcTrasladoAdic->getEstado();
                                        $objSpcTrasladoAdic->setValor('');
                                        $objSpcTrasladoAdic->setEstado('Eliminado');
                                        $objSpcTrasladoAdic->setFeUltMod(new \DateTime('now'));
                                        $objSpcTrasladoAdic->setUsrUltMod($strUsrCreacion);
                                        $this->emComercial->persist($objSpcTrasladoAdic);
                                        $this->emComercial->flush();

                                        //REGISTRA EN LA TABLA DE HISTORIAL
                                        $entityServicioHistorial = new InfoServicioHistorial();
                                        $entityServicioHistorial->setServicioId($objServicioAdicional);
                                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                        $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                                        $entityServicioHistorial->setIpCreacion($strIpCreacion);
                                        $entityServicioHistorial->setObservacion('Se actualizó característica TRASLADO con ID '.
                                                                                 $objSpcTrasladoAdic->getId().' : <br>'.
                                                                                 'Valores Anteriores: <br>'.  
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                                 'Valores Actuales: <br>'.  
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                                 '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                                        $entityServicioHistorial->setAccion('actualizaCaracteristica');
                                        $entityServicioHistorial->setEstado($objServicioAdicional->getEstado());
                                        $this->emComercial->persist($entityServicioHistorial);
                                        $this->emComercial->flush();

                                        //Reactivar Servicios-prod-carac de productos Paramount y Noggin en el punto Origen.
                                        $arrayProdCaracts = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->findBy(array("servicioId" => $strValorAntesCorreo, "estado" => 'Cancelado'));
                                        if (is_array($arrayProdCaracts))
                                        {
                                            $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                ->findOneById($strValorAntesCorreo);
                                            $objParametroDet    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')->
                                                                            findOneBy(array('descripcion'=>'NOMBRES_TECNICOS_PRODUCTOS_TV'));
                                            $arrayProductosAdicionales  = array($objParametroDet->getValor1(),$objParametroDet->getValor2());
                                            if( is_object($objServicio->getProductoId())
                                                && in_array($objServicio->getProductoId()->getNombreTecnico(), $arrayProductosAdicionales))
                                            {
                                                $arrayProducto = $this->serviceFoxPremium->determinarProducto(array(
                                                                                    'intIdProducto' => $objServicio->getProductoId()->getId()));
                                                $arrayParametrosFox = array();
                                                $arrayParametrosFox["strDescripcionCaracteristica"] = $arrayProducto['strMigrar'];
                                                $arrayParametrosFox["strNombreTecnico"]             = $arrayProducto['strNombreTecnico'];
                                                $arrayParametrosFox["intIdServicio"]                = $objServicio->getId();
                                                $arrayParametrosFox["strEstadoSpc"]                 = 'Cancelado';

                                                $objRespuestaServProdCarac = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                  ->getCaracteristicaServicio($arrayParametrosFox);
                                                if (is_object($objRespuestaServProdCarac))
                                                {
                                                    $objRespuestaServProdCarac->setValor('N');
                                                    $this->emComercial->persist($objRespuestaServProdCarac);
                                                    $this->emComercial->flush(); 
                                                }
                                                foreach ($arrayProdCaracts as $servicioProdCaract)
                                                {
                                                    $servicioProdCaract->setEstado('Activo');
                                                    $servicioProdCaract->setFeUltMod(new \DateTime('now'));
                                                    $servicioProdCaract->setUsrUltMod($strUsrCreacion);
                                                    $this->emComercial->persist($servicioProdCaract);
                                                    $this->emComercial->flush(); 
                                                }
                                            }
                                        }

                                        //reactivar servicio en origen de traslado en caso de tener estado TRASLADADO
                                        $objServicioOrigenTraslado  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                        ->find($strValorAntesCorreo);
                                        if (is_object($objServicioOrigenTraslado) && $objServicioOrigenTraslado->getEstado() == "Trasladado")
                                        {
                                            $objServicioOrigenTraslado->setEstado("Activo");
                                            $this->emComercial->persist($objServicioOrigenTraslado);
                                            $this->emComercial->flush();

                                            //GUARDAR INFO SERVICIO HISTORIAL
                                            $objHistorialServicioAdicional = new InfoServicioHistorial();
                                            $objHistorialServicioAdicional->setServicioId($objServicioOrigenTraslado);
                                            $objHistorialServicioAdicional->setIpCreacion($strIpCreacion);
                                            $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                            $objHistorialServicioAdicional->setObservacion("Se reactiva servicio por rechazo de ".
                                                                                           "Traslado del servicio en el punto: ".
                                                                                           $objServicio->getPuntoId()->getLogin());
                                            $objHistorialServicioAdicional->setMotivoId($intIdMotivo);
                                            $objHistorialServicioAdicional->setUsrCreacion($strUsrCreacion);
                                            $objHistorialServicioAdicional->setEstado('Activo');
                                            $this->emComercial->persist($objHistorialServicioAdicional);
                                            $this->emComercial->flush();
                                        }
                                    }
                                }
                                ///Se eliminan las características asociadas al servicio de Internet del punto origen
                                $arraySpcServicioAdi    = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                            ->findBy(array( "servicioId"    => $objServicioAdicional->getId(),
                                                                                            "estado"        => "Activo"));
                                foreach($arraySpcServicioAdi as $objSpcServicioAdi)
                                {
                                    $objSpcServicioAdi->setEstado('Eliminado');
                                    $objSpcServicioAdi->setUsrUltMod($strUsrCreacion);
                                    $objSpcServicioAdi->setFeUltMod(new \DateTime('now'));
                                    $this->emComercial->persist($objSpcServicioAdi);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                }
                    
                $arrayCuposPlanificacion    = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                ->findBy(array('solicitudId' => $intIdSolicitud));
                foreach ($arrayCuposPlanificacion as $objCupoPlanificacion)
                {
                    $objCupoPlanificacion->setSolicitudId(null);
                    $objCupoPlanificacion->setCuadrillaId(null);
                    $this->emComercial->persist($objCupoPlanificacion);
                    $this->emComercial->flush();
                }
                    
                if ($strPrefijoEmpresa == 'TN' && is_object($objServicio->getProductoId()) 
                    && ($objServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                        || $objServicio->getProductoId()->getNombreTecnico() === "TELCOHOME"))
                {
                    $arrayParamsAdicionales     = array("objServicioPref"           => $objServicio,
                                                        "objProductoPref"           => $objServicio->getProductoId(),
                                                        "strUsrCreacion"            => $strUsrCreacion,
                                                        "strObservacionServicio"    => "Se rechaza el servicio por rechazo de ".
                                                                                       "servicio preferencial",
                                                        "strIpClient"               => $strIpCreacion,
                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                        "strNuevoEstadoSol"         => "Rechazada",
                                                        "strObservacionSol"         => "Se realizó el rechazo del servicio por rechazo de "
                                                                                       ."solicitud de planificación del servicio preferencial "
                                                                                       ."y por ende se rechaza la solicitud"
                                                        );
                    $arrayRespuestaAdicionales  = $this->serviceInfoServicio->gestionarServiciosAdicionales($arrayParamsAdicionales);
                    if($arrayRespuestaAdicionales["strStatus"] !== "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception($arrayRespuestaAdicionales["strMensaje"] );
                    }
                }
            }
            if ($strTipoSolicitud !== "solicitud migracion")
            {
                $strEstRechazo = 'Rechazada';
                if ($strTipoSolicitud == "solicitud de instalacion cableado ethernet")
                {
                    // Validamos que solo sea para producto cableado ethernet
                    $arrayParametroTipos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->get(   'VALIDA_PROD_ADICIONAL',
                                                                    'COMERCIAL',
                                                                    '',
                                                                    'Solicitud cableado ethernet',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '18');
                    if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                    {
                        $objCableParametro = $arrayParametroTipos[0];
                    }
                    if ($objServicio->getProductoId() != null &&
                        $objServicio->getProductoId()->getId() == $objCableParametro['valor1'])
                    {
                        $objServicio->setEstado($strEstRechazo);
                        $this->emComercial->persist($objServicio);
                        $this->emComercial->flush();
                    }
                    // Ingresamos el historial del detalle de la solicitud
                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($objServicio);
                    $objServicioHist->setObservacion('Se rechaza el producto cableado ethernet');
                    $objServicioHist->setIpCreacion($strIpCreacion);
                    $objServicioHist->setUsrCreacion($strUsrCreacion);
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setEstado($strEstRechazo);
                    $this->emComercial->persist($objServicioHist);
                    $this->emComercial->flush();
                }
                
                $objSolicitud->setMotivoId($intIdMotivo);
                $objSolicitud->setObservacion($strObservacion);
                $objSolicitud->setEstado($strEstRechazo);
                $objSolicitud->setUsrRechazo($strUsrCreacion);
                $objSolicitud->setFeRechazo(new \DateTime('now'));
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                $entityDetalleSolHist->setObservacion($strObservacion);
                $entityDetalleSolHist->setMotivoId($intIdMotivo);
                $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $entityDetalleSolHist->setEstado($strEstRechazo);
                $this->emComercial->persist($entityDetalleSolHist);
                $this->emComercial->flush();
            }
            else
            {
                $objSolicitud->setMotivoId($intIdMotivo);
                $objSolicitud->setObservacion($strObservacion);
                $objSolicitud->setEstado("Pendiente");
                $objSolicitud->setUsrRechazo($strUsrCreacion);
                $objSolicitud->setFeRechazo(new \DateTime('now'));
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $objSolicitudHistorial = new InfoDetalleSolHist();
                $objSolicitudHistorial->setDetalleSolicitudId($objSolicitud);
                $objSolicitudHistorial->setObservacion($strObservacion);
                $objSolicitudHistorial->setMotivoId($intIdMotivo);
                $objSolicitudHistorial->setIpCreacion($strIpCreacion);
                $objSolicitudHistorial->setFeCreacion(new \DateTime('now'));
                $objSolicitudHistorial->setUsrCreacion($strUsrCreacion);
                $objSolicitudHistorial->setEstado('Pendiente');
                $this->emComercial->persist($objSolicitudHistorial);
                $this->emComercial->flush();
            }

            //Rechazar y Cancelar tareas
            $arrayParametrosCambiarEstadoTarea                    = array();
            $arrayParametrosCambiarEstadoTarea['cargarTiempo']    = "cliente";
            $arrayParametrosCambiarEstadoTarea['esSolucion']      = "N";
            $arrayDetalles  = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                              ->findBy(array('detalleSolicitudId' => $objSolicitud->getId()));
            $intObjDetalle=0;
            if(isset($arrayDetalles) && !empty($arrayDetalles))
            {
                foreach($arrayDetalles as $objDetalle)
                {
                    //Cambiar estado a Rechazada
                    $intObjDetalle=$objDetalle->getId();
                    $arrayParametrosCambiarEstadoTarea['observacion']     = "Rechazo de Orden de Trabajo";
                    $arrayParametrosCambiarEstadoTarea['estado']          = "Rechazada";
                    $strRespuestaCancelacionTarea   = $this->serviceSoporte
                                                           ->cambiarEstadoTarea($objDetalle, null, $objRequest, $arrayParametrosCambiarEstadoTarea);
                    if($strRespuestaCancelacionTarea != "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser rechazadas. Favor notificar a Sistemas.");
                    }

                    //Cambiar estado a Cancelada
                    $arrayParametrosCambiarEstadoTarea['observacion']     = "Cancelación automática por rechazo de Orden de Trabajo";
                    $arrayParametrosCambiarEstadoTarea['estado']          = "Cancelada";
                    $strRespuestaCancelacionTarea   = $this->serviceSoporte
                                                           ->cambiarEstadoTarea($objDetalle, null, $objRequest, $arrayParametrosCambiarEstadoTarea);
                    if($strRespuestaCancelacionTarea !== "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser canceladas. Favor notificar a Sistemas.");
                    }                        
                }
            }

            //------- COMUNICACIONES --- NOTIFICACIONES
            $strContenidoCorreo = $this->objTemplating->render( 'planificacionBundle:Coordinar:notificacion.html.twig', array(
                                                                'detalleSolicitud'     => $objSolicitud,
                                                                'detalleSolicitudHist' => null,
                                                                'motivo'               => $objMotivo));

            if ($strTipoSolicitud === 'solicitud cambio equipo por soporte' || $strTipoSolicitud === 'solicitud cambio equipo por soporte masivo')
            {
                $strAsunto = "Rechazo de Planificacion de Solicitud Cambio de Equipo por Soporte #" . $objSolicitud->getId();
            }
            else
            {
                $strAsunto = "Rechazo de Planificacion de Solicitud de Instalacion #" . $objSolicitud->getId();
            }

            //DESTINATARIOS....
            $arrayFormasContacto    = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto($objServicio->getPuntoId()->getUsrVendedor(),
                                                                                                     'Correo Electronico');
            $arrayTo                = array();
            $arrayTo[]              = 'notificaciones_telcos@telconet.ec';
            if (isset($arrayFormasContacto) && !empty($arrayFormasContacto))
            {
                foreach ($arrayFormasContacto as $arrayFormaContacto)
                {
                    $arrayTo[] = $arrayFormaContacto['valor'];
                }
            }
            $this->serviceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strContenidoCorreo);
            
            
            /*Se valida si el parámetro de arreglos simultáneos esta definido y no es vacío.*/
            if (!empty($arraySimultaneos) && !is_null($arraySimultaneos) && is_array($arraySimultaneos))
            {
                /*Construimos el arreglo de parámetros necesarios.*/
                $arrayParams    = array(   
                                    'arraySimultaneos'  => $arraySimultaneos,
                                    'strEstadoSoli'     => 'Rechazada',
                                    'strEstadoServ'     => 'Rechazada',
                                    'strObsSolicitud'   => 'Se rechaza solicitud debido a que el servicio tradicional en simultaneo fue rechazado.',
                                    'strObsHistorial'   => 'Servicio rechazado en simultáneo.',
                                    'idMotivo'          => $objMotivo->getId()
                                  );
                
                /*Se llama al método encargado en el servicio asignado anteriormente.*/
                $arrayResponse  = $this->serviceServicioTenico->ejecutarRechazoSimultaneo($arrayParams);
                /*Validamos si la respuesta es correcta*/
                if ($arrayResponse['status'] == 'OK')
                {
                    $this->emComercial->getConnection()->commit();
                    $this->emComunicacion->getConnection()->commit();
                }
                else
                {
                    $boolMostrarMsjErrorUsr = true;
                    throw new \Exception("Ha ocurrido un error al intentar rechazar los servicios de instalación simultanea.");
                }
            }
            else
            {
                $this->emComercial->getConnection()->commit();
                $this->emComunicacion->getConnection()->commit();
                $this->emSoporte->getConnection()->commit();
            }

            $arrayParametrosActulizar = array();
            $arrayParametrosActulizar["strUsrCreacion"]  =  $objRequest->getSession()->get('user');

            $arrayParametrosInfoTarea['intDetalleId'] = $intObjDetalle;
            $arrayParametrosInfoTarea['strUsrUltMod'] = isset($arrayParametrosActulizar["strUsrCreacion"])? 
                                                        $arrayParametrosActulizar["strUsrCreacion"] : '';
            $this->serviceSoporte->actualizarInfoTarea($arrayParametrosInfoTarea);
            $strStatus  = "OK";
            $strMensaje = "Se rechazo la solicitud";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Error: " . $e->getMessage();
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar el rechazo. Comuníquese con el Dep. de Sistemas!";
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            $this->emComercial->close();
            
            if($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->rollback();
            }
            $this->emComunicacion->close();
            if($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->rollback();
            }
            $this->emSoporte->close();
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    
    
    /**
     * 
     * Función ejecutada después de coordinar una solicitud, ejecutando la coordinación de las solicitudes relacionadas
     *
     * @param array $arrayParametros [
     *                                  "intIdSolGestionada"                => id de la solicitud que se ha gestionado previamente
     *                                  "strOpcionGestionSimultanea"        => opción que se ejecuta desde el grid de PYL
     *                                  "strMensajeEjecucionSolGestionada"  => mensaje de la gestión de la solicitud gestionada proviamente
     *                                  "strOrigen"                         => opción enviada desde el el grid como 'local'
     *                                  "strParamResponsables"              => cadena con formato para obtener los responsables
     *                                  "strFechaProgramacion"              => fecha de programación
     *                                  "strFechaHoraInicioProgramacion"    => hora inicio de programación
     *                                  "strFechaHoraFinProgramacion"       => hora fin de programación
     *                                  "intIdPerSession"                   => id persona empresa rol del usuario en sesión
     *                                  "intIdDepartamentoSession"          => id del departamento del usuario en sesión
     *                                  "strCodEmpresa"                     => id de la empresa
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 03-01-2022 - Se anexan las variables de HAL y el idSugerencia para realizar
     *                           la planificacion simultanea de los productos EDB
     * 
     */
    public function programarPlanificacionSimultanea($arrayParametros)
    {
        $intIdSolGestionada                 = $arrayParametros['intIdSolGestionada'];
        $strOpcionGestionSimultanea         = $arrayParametros['strOpcionGestionSimultanea'];
        $strMensajeEjecucionSolGestionada   = $arrayParametros['strMensajeEjecucionSolGestionada'];
        $strOrigen                          = $arrayParametros['strOrigen'];
        $strParamResponsables               = $arrayParametros['strParamResponsables'];
        $strFechaProgramacion               = $arrayParametros['strFechaProgramacion'];
        $strFechaHoraInicioProgramacion     = $arrayParametros['strFechaHoraInicioProgramacion'];
        $strFechaHoraFinProgramacion        = $arrayParametros['strFechaHoraFinProgramacion'];
        $intIdPerSession                    = $arrayParametros['intIdPerSession'];
        $intIdDepartamentoSession           = $arrayParametros['intIdDepartamentoSession'];
        $strCodEmpresa                      = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa                  = $arrayParametros['strPrefijoEmpresa'];
        $strIpCreacion                      = $arrayParametros['strIpCreacion'];
        $strUsrCreacion                     = $arrayParametros['strUsrCreacion'];
        $strMensaje                         = "";
        $boolMostrarMensajeErrorUsr         = false;
        $strAtenderAntes                    = $arrayParametros['strAtenderAntes'];
        $strEsHal                           = $arrayParametros['strEsHal'];
        $intIdSugerenciaHal                 = "";
        try
        {
            if(!isset($intIdSolGestionada) || empty($intIdSolGestionada) || !isset($strOpcionGestionSimultanea) || empty($strOpcionGestionSimultanea))
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se han enviado correctamente los parámetros para realizar la coordinación simultánea");
            }
            
            $intHoraCierre                      = $this->objContainer->getParameter('planificacion.mobile.hora_cierre');

            $arrayFechaProgramacion             = explode("T", $strFechaProgramacion);
            $arrayDiaMesAnioFechaProgramacion   = explode("-", $arrayFechaProgramacion[0]);

            $arrayFechaHoraInicioProgramacion   = explode("T", $strFechaHoraInicioProgramacion);
            $arrayHoraMinSegInicioProgramacion  = explode(":", $arrayFechaHoraInicioProgramacion[1]);

            $strFechaHoraInicio                 = date("Y/m/d H:i", strtotime(  $arrayDiaMesAnioFechaProgramacion[2] . "-" 
                                                                                . $arrayDiaMesAnioFechaProgramacion[1] . "-" 
                                                                                . $arrayDiaMesAnioFechaProgramacion[0] . " " 
                                                                                . $arrayFechaHoraInicioProgramacion[1]));

            $arrayFechaI                        = date_create(date('Y/m/d', strtotime($arrayFechaHoraInicioProgramacion[0])));
            $arrayFechaI                        = $arrayFechaI->format("Y-m-d");

            $arrayFechaHoraFinProgramacion      = explode("T", $strFechaHoraFinProgramacion);
            $arrayHoraMinSegFinProgramacion     = explode(":", $arrayFechaHoraFinProgramacion[1]);
            $strFechaHoraFin                    = date("Y/m/d H:i", strtotime(  $arrayDiaMesAnioFechaProgramacion[2] . "-" 
                                                                                . $arrayDiaMesAnioFechaProgramacion[1] . "-" 
                                                                                . $arrayDiaMesAnioFechaProgramacion[0] . " " 
                                                                                . $arrayFechaHoraFinProgramacion[1]));
            
            $objCaractProdAdicional             = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneBy(array('descripcionCaracteristica' => 'PRODUCTO_ADICIONAL',
                                                                                      'estado'                    => 'Activo'));
            $objCaractProdControlaCupo          = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                    ->findOneByDescripcionCaracteristica('PRODUCTO CONTROLA CUPO');
            
            $arrayRespuestaGetInfoSimultanea        = $this->serviceServicioTenico->getInfoGestionSimultanea(array(
                                                                                    "intIdSolicitud"                => $intIdSolGestionada,
                                                                                    "strOpcionGestionSimultanea"    => $strOpcionGestionSimultanea));
            $arrayRegistrosInfoGestionSimultanea    = $arrayRespuestaGetInfoSimultanea["arrayRegistrosInfoGestionSimultanea"];
            
            if(isset($arrayRegistrosInfoGestionSimultanea) && !empty($arrayRegistrosInfoGestionSimultanea))
            {
                foreach($arrayRegistrosInfoGestionSimultanea as $arrayRegistroInfoGestionSimultanea)
                {
                    $strDescripSolGestionada                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_GESTIONADA"];
                    $intIdSolSimultanea                     = $arrayRegistroInfoGestionSimultanea["ID_SOL_SIMULTANEA"];
                    $strDescripSolSimultanea                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_SIMULTANEA"];
                    $intIdServicioSimultaneo                = $arrayRegistroInfoGestionSimultanea["ID_SERVICIO_SIMULTANEO"];
                    $intIdProdServicioSimultaneo            = $arrayRegistroInfoGestionSimultanea["ID_PROD_SERVICIO_SIMULTANEO"];
                    $intIdJurisdiccionPunto                 = $arrayRegistroInfoGestionSimultanea["ID_JURISDICCION_PUNTO"];
                    $intCupoJurisdiccionPunto               = $arrayRegistroInfoGestionSimultanea["CUPO_JURISDICCION_PUNTO"];
                    $strObservacionSolSimultanea            = "Solicitud gestionada simultáneamente por ".
                                                              $strDescripSolGestionada." #".$intIdSolGestionada.".";
                    $boolMostrarMsjErrorUsrSolSimultanea    = false;
                    $strMensajeSolGestionSimultanea         = "";
                    $boolValidaCupo                         = true;
                    // Si es por HAL realizamos la sugerencia para su planificacion
                    try
                    {
                        if($strEsHal === 'S')
                        {
                            $arrayParametrosHal = array (
                                'intIdDetalleSolicitud'  => intval($intIdSolSimultanea),
                                'intIdDetalle'           => '',
                                'intIdComunicacion'      => '',
                                'strEsInstalacion'       => 'S',
                                'intIdPersonaEmpresaRol' => intval($intIdPerSession),
                                'intNOpciones'           => 1,
                                'intNIntentos'           => 1,
                                'strFechaSugerida'       => '',
                                'strHoraSugerida'        => '',
                                'boolConfirmar'          => false,
                                'strSolicitante'         => 'NA',
                                'strUrl'                 => $this->objContainer->getParameter('ws_hal_solicitaSugerenciaInstalacion'));
                            // Establecemos la comunicacion con hal
                            $arrayRespuestaHal  = $this->serviceSoporte->getSolicitarConfirmarSugerenciasHal($arrayParametrosHal);
                            if (strtoupper($arrayRespuestaHal['mensaje']) == 'FAIL')
                            {
                                $this->serviceUtil->insertError('Telcos+',
                                                        'InfoCasoController.getIntervalosHalAction',
                                                        'getSolicitarConfirmarSugerenciasHal: '.$arrayRespuestaHal['descripcion'],
                                                        $strUsrCreacion,
                                                        $strIpCreacion);
                                // Devolvemos error si no logramos obtener la sugerencia
                                throw new \Exception("Se encontro problemas al obtener la fecha de planificacion!");
                            }
                            else
                            {
                                if ($arrayRespuestaHal['result']['respuesta'] === 'conSugerencias')
                                {
                                    foreach ($arrayRespuestaHal['result']['sugerencias'] as $arrayDatos)
                                    {
                                        $intIdSugerenciaHal = $arrayDatos['idSugerencia'];
                                        $strFechaHal        = $arrayDatos['fecha'];
                                        $strHoraHal         = $arrayDatos['horaIni'];
                                        $strTiempoVigencia  = $arrayDatos['segTiempoVigencia'];
                                    }
                                }
                            }
                        }

                        if(!isset($intCupoJurisdiccionPunto) || empty($intCupoJurisdiccionPunto) || 
                            $intCupoJurisdiccionPunto <= 0 || $strEsHal === 'S')
                        {
                            $boolValidaCupo = false;
                        }
                        else if(isset($intIdServicioSimultaneo) && !empty($intIdServicioSimultaneo)
                            && isset($intIdProdServicioSimultaneo) && !empty($intIdProdServicioSimultaneo)
                            && is_object($objCaractProdControlaCupo))
                        {
                            $objProdCaractControlaCupo  =  $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                             ->findOneBy(array( "productoId"        => 
                                                                                                $intIdProdServicioSimultaneo,
                                                                                                "caracteristicaId"  => 
                                                                                                $objCaractProdControlaCupo));
                            if(is_object($objProdCaractControlaCupo))
                            {
                                $boolValidaCupo = false;
                            }
                        }
                        if($boolValidaCupo)
                        {
                            $strFechaPar    = substr($strFechaHoraInicio, 0, -1);
                            $strFechaPar    .= "1";
                            $strFechaPar    = str_replace("-", "/", $strFechaPar);
                            $strFechaAgenda = str_replace("-", "/", $strFechaHoraInicio);
                            $intNumCupoPlanificacion    = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                            ->getCountDisponiblesWeb(array( "strFecha"          => $strFechaPar,
                                                                                                            "strFechaAgenda"    => $strFechaAgenda,
                                                                                                            "intJurisdiccion"   => 
                                                                                                            $intIdJurisdiccionPunto,
                                                                                                            "intHoraCierre"     => $intHoraCierre));
                            if ($intNumCupoPlanificacion == 0)
                            {
                                $strMensajeSolGestionSimultanea         .= "No se ha podido realizar correctamente la coordinación para la "
                                                                            .$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                                $boolMostrarMsjErrorUsrSolSimultanea    = true;
                                throw new \Exception("No hay cupo disponible para este horario, seleccione otro horario por favor!");
                            }
                        }
                        //Ejecuta coordinación
                        $strProcesoSimultaneo = "SI";
                        $arrayParamsGestionSimultanea = array(  "intIdPerEmpRolSesion"          => $intIdPerSession,
                                                                "strOrigen"                     => $strOrigen,
                                                                "intIdFactibilidad"             => $intIdSolSimultanea,
                                                                "strParametro"                  => $intIdSolSimultanea,
                                                                "strParamResponsables"          => $strParamResponsables,
                                                                "strCodEmpresa"                 => $strCodEmpresa,
                                                                "strPrefijoEmpresa"             => $strPrefijoEmpresa,
                                                                "intIdDepartamento"             => $intIdDepartamentoSession,
                                                                "serviceInfoServicio"           => $this->serviceInfoServicio,
                                                                "dateF"                         => $arrayDiaMesAnioFechaProgramacion,
                                                                "dateFecha"                     => $arrayFechaHoraInicioProgramacion,
                                                                "strFechaInicio"                => $strFechaHoraInicio,
                                                                "strFechaFin"                   => $strFechaHoraFin,
                                                                "strHoraInicioServicio"         => $arrayHoraMinSegInicioProgramacion,
                                                                "strHoraFinServicio"            => $arrayHoraMinSegFinProgramacion,
                                                                "dateFechaProgramacion"         => $arrayFechaI,
                                                                "strHoraInicio"                 => $arrayHoraMinSegInicioProgramacion,
                                                                "strHoraFin"                    => $arrayHoraMinSegFinProgramacion,
                                                                "strObservacionServicio"        => $strObservacionSolSimultanea,
                                                                "strIpCreacion"                 => $strIpCreacion,
                                                                "strUsrCreacion"                => $strUsrCreacion,
                                                                "strObservacionSolicitud"       => $strObservacionSolSimultanea,
                                                                "strProcesoSimultaneo"          => $strProcesoSimultaneo,
                                                                "strAtenderAntes"               => $strAtenderAntes,
                                                                "strEsHal"                      => $strEsHal,
                                                                "intIdSugerenciaHal"            => $intIdSugerenciaHal);
                        $arrayRespuestaCoordinacion = $this->servicePlanificacion->coordinarPlanificacion($arrayParamsGestionSimultanea);
                        if(isset($arrayRespuestaCoordinacion['codigoRespuesta']) && !empty($arrayRespuestaCoordinacion['codigoRespuesta']) 
                            && $arrayRespuestaCoordinacion['codigoRespuesta'] > 0)
                        {
                            $strMensajeSolGestionSimultanea .= "Se realiza correctamente la coordinación";
                        }
                        else
                        {
                            $strMensajeSolGestionSimultanea         .= "No se ha podido realizar correctamente la coordinación para la "
                                                                        .$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                            $boolMostrarMsjErrorUsrSolSimultanea    = true;
                            throw new \Exception($arrayRespuestaCoordinacion["mensaje"]); 
                        }

                        $objDetSolCaractProdAdicional = null;
                        if(is_object($objCaractProdAdicional))
                        {
                            $objDetSolCaractProdAdicional   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                                ->findOneBy(array('detalleSolicitudId' => $intIdSolSimultanea,
                                                                                                  'caracteristicaId'   => 
                                                                                                  $objCaractProdAdicional->getId()));
                        }
                        //Ejecuta asignación de responsable
                        if(($strParamResponsables <> "" ||  $strEsHal === "S") && !is_object($objDetSolCaractProdAdicional))
                        {
                            $arrayParamsGestionSimultanea['strObservacionSolSimultanea']    = $strObservacionSolSimultanea;
                            $arrayParamsGestionSimultanea['objDetalleSolHist']              = $arrayRespuestaCoordinacion['entityDetalleSolHist'];
                            $arrayParamsGestionSimultanea['serviceRecursosRed']             = $this->serviceRecursosRed;
                            $arrayParamsGestionSimultanea['objServicioHistorial']           = $arrayRespuestaCoordinacion['entityServicioHistorial'];
                            $arrayParamsGestionSimultanea['strEsGestionSimultanea']         = "SI";
                            $arrayRespuestaAsignarResponsable = $this->servicePlanificacion->asignarPlanificacion($arrayParamsGestionSimultanea);
                            if(isset($arrayRespuestaAsignarResponsable["mensaje"]) && !empty($arrayRespuestaAsignarResponsable["mensaje"])
                                && ($arrayRespuestaAsignarResponsable["mensaje"] == "Se asignaron la(s) Tarea(s) Correctamente."
                                    || $arrayRespuestaAsignarResponsable["mensaje"] == "Se coordinó la solicitud"
                                    || strrpos($arrayRespuestaAsignarResponsable["mensaje"], "Correctamente")))
                            {
                                $strMensajeSolGestionSimultanea .= " y asignación de responsable para la "
                                                                   .$strDescripSolSimultanea." # ".$intIdSolSimultanea.".";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea         .= ", pero no se realiza correctamente la asignación de responsable "
                                                                            ."para la ".$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                                $boolMostrarMsjErrorUsrSolSimultanea    = true;
                                throw new \Exception($arrayRespuestaAsignarResponsable["mensaje"]); 
                            }
                        }
                    }
                    catch (\Exception $e)
                    {
                        if($boolMostrarMsjErrorUsrSolSimultanea)
                        {
                            $strMensajeSolGestionSimultanea .= "Error: ".$e->getMessage();
                        }
                        else
                        {
                            if(empty($strMensajeSolGestionSimultanea))
                            {
                                $strMensajeSolGestionSimultanea .= "Ha";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea .= ", pero ha";
                            }
                            $strMensajeSolGestionSimultanea .= " ocurrido un error inesperado al coordinar la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea
                                                               .". Comuníquese con el Dep. de Sistemas para su revisión.";
                            error_log("Error solicitud #".$intIdSolSimultanea."-".$e->getMessage());
                        }
                    }
                    $strMensaje .= $strMensajeSolGestionSimultanea."<br>";
                }
                $strMensaje = $strMensajeEjecucionSolGestionada."<br><b>Gestión simultánea</b><br>".$strMensaje;
            }
            else
            {
                $strMensaje = $strMensajeEjecucionSolGestionada;
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if($boolMostrarMensajeErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar la coordinación simultánea por un error desconocido. Comuníquese con el Dep. de Sistemas!";
                error_log("Error en programarPlanificacionSimultanea ".$e->getMessage());
            }
            $strMensaje = $strMensajeEjecucionSolGestionada . "<br><b>Gestión simultánea</b><br>".$strMensaje;
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        
        return $arrayRespuesta;
    }
    
    /**
     * 
     * Función ejecutada después de replanificar una solicitud, ejecutando la replanificación de las solicitudes relacionadas
     *
     * @param array $arrayParametros [
     *                                  "intIdSolGestionada"                => id de la solicitud que se ha gestionado previamente
     *                                  "strOpcionGestionSimultanea"        => opción que se ejecuta desde el grid de PYL
     *                                  "strMensajeEjecucionSolGestionada"  => mensaje de la gestión de la solicitud gestionada proviamente
     *                                  "strOrigen"                         => opción enviada desde el el grid como 'local'
     *                                  "intIdMotivo"                       => id del motivo
     *                                  "strBoolPerfilOpu"                  => 'SI' o 'NO' es el perfil de Opu
     *                                  "strParamResponsables"              => cadena con formato para obtener los responsables
     *                                  "strFechaReplanificacion"           => fecha de replanificación
     *                                  "strFechaHoraInicioReplanificacion" => hora inicio de replanificación
     *                                  "strFechaHoraFinReplanificacion"    => hora fin de replanificación
     *                                  "intIdPerTecnico"                   => id del Ingeniero IPCCL
     *                                  "intIdPerSession"                   => id persona empresa rol del usuario en sesión
     *                                  "objRequest"                        => objeto del request
     *                                  "strCodEmpresa"                     => id de la empresa
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 10-01-2022 - Se agrega campos y llamado a sugerencia HAL cuando el proceso normal de
     *                           replanificacion viene desde HAL
     * 
     */
    public function reprogramarPlanificacionSimultanea($arrayParametros)
    {
        $intIdSolGestionada                 = $arrayParametros['intIdSolGestionada'];
        $strOpcionGestionSimultanea         = $arrayParametros['strOpcionGestionSimultanea'];
        $strMensajeEjecucionSolGestionada   = $arrayParametros['strMensajeEjecucionSolGestionada'];
        $strOrigen                          = $arrayParametros['strOrigen'];
        $intIdMotivo                        = $arrayParametros['intIdMotivo'];
        $strBoolPerfilOpu                   = $arrayParametros['strBoolPerfilOpu'];
        $strParamResponsables               = $arrayParametros['strParamResponsables'];
        $strFechaReplanificacion            = $arrayParametros['strFechaReplanificacion'];
        $strFechaHoraInicioReplanificacion  = $arrayParametros['strFechaHoraInicioReplanificacion'];
        $strFechaHoraFinReplanificacion     = $arrayParametros['strFechaHoraFinReplanificacion'];
        $intIdPerTecnico                    = $arrayParametros['intIdPerTecnico'];
        $intIdPerSession                    = $arrayParametros['intIdPerSession'];
        $objRequest                         = $arrayParametros['objRequest'];
        $strCodEmpresa                      = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa                  = $arrayParametros['strPrefijoEmpresa'];
        $strIpCreacion                      = $arrayParametros['strIpCreacion'];
        $strUsrCreacion                     = $arrayParametros['strUsrCreacion'];
        $strMensaje                         = "";
        $boolMostrarMensajeErrorUsr         = false;
        $strAtenderAntes                    = $arrayParametros['strAtenderAntes'];
        $strEsHal                           = $arrayParametros['strEsHal'];
        $intIdSugerenciaHal                 = "";
        
        try
        {
            if(!isset($intIdSolGestionada) || empty($intIdSolGestionada) || !isset($strOpcionGestionSimultanea) || empty($strOpcionGestionSimultanea))
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se han enviado correctamente los parámetros para realizar la replanificación simultánea");
            }
            
            $arrayRespuestaGetInfoSimultanea        = $this->serviceServicioTenico
                                                           ->getInfoGestionSimultanea(array(
                                                                                    "intIdSolicitud"                => $intIdSolGestionada,
                                                                                    "strOpcionGestionSimultanea"    => $strOpcionGestionSimultanea,
                                                                                    "strTipoOpcionGestionSimultanea"=> "EJECUCION"));
            $arrayRegistrosInfoGestionSimultanea    = $arrayRespuestaGetInfoSimultanea["arrayRegistrosInfoGestionSimultanea"];
            
            if(isset($arrayRegistrosInfoGestionSimultanea) && !empty($arrayRegistrosInfoGestionSimultanea))
            {
                foreach($arrayRegistrosInfoGestionSimultanea as $arrayRegistroInfoGestionSimultanea)
                {
                    $strDescripSolGestionada                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_GESTIONADA"];
                    $intIdSolSimultanea                     = $arrayRegistroInfoGestionSimultanea["ID_SOL_SIMULTANEA"];
                    $strDescripSolSimultanea                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_SIMULTANEA"];
                    $strObservacionSolSimultanea            = "Solicitud gestionada simultáneamente por ".
                                                              $strDescripSolGestionada." #".$intIdSolGestionada.".";
                    $boolMostrarMsjErrorUsrSolSimultanea    = false;
                    $strMensajeSolGestionSimultanea         = "";
                    try
                    {
                        if($strEsHal === 'S')
                        {
                            $arrayParametrosHal = array (
                                'intIdDetalleSolicitud'  => intval($intIdSolSimultanea),
                                'intIdDetalle'           => '',
                                'intIdComunicacion'      => '',
                                'strEsInstalacion'       => 'S',
                                'intIdPersonaEmpresaRol' => intval($intIdPerSession),
                                'intNOpciones'           => 1,
                                'intNIntentos'           => 1,
                                'strFechaSugerida'       => '',
                                'strHoraSugerida'        => '',
                                'boolConfirmar'          => false,
                                'strSolicitante'         => 'NA',
                                'strUrl'                 => $this->objContainer->getParameter('ws_hal_solicitaSugerenciaInstalacion'));
                            // Establecemos la comunicacion con hal
                            $arrayRespuestaHal  = $this->serviceSoporte->getSolicitarConfirmarSugerenciasHal($arrayParametrosHal);
                            if (strtoupper($arrayRespuestaHal['mensaje']) == 'FAIL')
                            {
                                $this->serviceUtil->insertError('Telcos+',
                                                        'InfoCasoController.getIntervalosHalAction',
                                                        'getSolicitarConfirmarSugerenciasHal: '.$arrayRespuestaHal['descripcion'],
                                                        $strUsrCreacion,
                                                        $strIpCreacion);
                                // Devolvemos error si no logramos obtener la sugerencia
                                throw new \Exception("Se encontro problemas al obtener la fecha de planificacion!");
                            }
                            else
                            {
                                if ($arrayRespuestaHal['result']['respuesta'] === 'conSugerencias')
                                {
                                    foreach ($arrayRespuestaHal['result']['sugerencias'] as $arrayDatos)
                                    {
                                        $intIdSugerenciaHal = $arrayDatos['idSugerencia'];
                                        $strFechaHal        = $arrayDatos['fecha'];
                                        $strHoraHal         = $arrayDatos['horaIni'];
                                        $strTiempoVigencia  = $arrayDatos['segTiempoVigencia'];
                                    }
                                }
                            }
                        }
                        //Ejecuta reprogramación
                        $strProcesoSimultaneo = "SI";
                        $arrayParamsGestionSimultanea = array(  "intIdSolicitud"                    => $intIdSolSimultanea,
                                                                "intIdMotivo"                       => $intIdMotivo,
                                                                "strBoolPerfilOpu"                  => $strBoolPerfilOpu,
                                                                "strPrefijoEmpresa"                 => $strPrefijoEmpresa,
                                                                "strCodEmpresa"                     => $strCodEmpresa,
                                                                "strFechaReplanificacion"           => $strFechaReplanificacion,
                                                                "strFechaHoraInicioReplanificacion" => $strFechaHoraInicioReplanificacion,
                                                                "strFechaHoraFinReplanificacion"    => $strFechaHoraFinReplanificacion,
                                                                "strObservacion"                    => $strObservacionSolSimultanea,
                                                                "strParamResponsables"              => $strParamResponsables,
                                                                "strIpCreacion"                     => $strIpCreacion,
                                                                "strUsrCreacion"                    => $strUsrCreacion,
                                                                "objRequest"                        => $objRequest,
                                                                "strProcesoSimultaneo"              => $strProcesoSimultaneo,
                                                                "strAtenderAntes"                   => $strAtenderAntes,
                                                                "strEsHal"                          => $strEsHal,
                                                                "intIdSugerenciaHal"                => $intIdSugerenciaHal);

                        $arrayRespuestaReprogramacion       = $this->reprogramarPlanificacion($arrayParamsGestionSimultanea);
                        $strStatusReprogramar               = $arrayRespuestaReprogramacion["status"];
                        $strMensajeReprogramar              = $arrayRespuestaReprogramacion["mensaje"];
                        $objServicioHistorialReprogramar    = $arrayRespuestaReprogramacion["objServicioHistorial"];
                        if($strStatusReprogramar === "OK")
                        {
                            $strMensajeSolGestionSimultanea .= "Se realiza correctamente la reprogramación";
                        }
                        else
                        {
                            $strMensajeSolGestionSimultanea         .= "No se ha podido realizar correctamente la reprogramación para la "
                                                                        .$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                            $boolMostrarMsjErrorUsrSolSimultanea    = true;
                            throw new \Exception($strMensajeReprogramar);
                        }

                        //Ejecuta asignación de responsable
                        if($strParamResponsables <> "")
                        {
                            $strProcesoSimultaneo = "SI";
                            $arrayParametrosAsignacion  = array(
                                                                "strOrigen"                     => $strOrigen,
                                                                "intIdFactibilidad"             => $intIdSolSimultanea,
                                                                "strParametro"                  => $intIdSolSimultanea,
                                                                "strParamResponsables"          => $strParamResponsables,
                                                                "intIdPerTecnico"               => $intIdPerTecnico,
                                                                "strIpCreacion"                 => $strIpCreacion,
                                                                "strUsrCreacion"                => $strUsrCreacion,
                                                                "strCodEmpresa"                 => $strCodEmpresa,
                                                                "strPrefijoEmpresa"             => $strPrefijoEmpresa,
                                                                "intIdPerEmpRolSesion"          => $intIdPerSession,
                                                                "objServicioHistorial"          => $objServicioHistorialReprogramar,
                                                                "strObservacionSolSimultanea"   => $strObservacionSolSimultanea,
                                                                "strEsGestionSimultanea"        => "SI",
                                                                "strProcesoSimultaneo"          => $strProcesoSimultaneo,
                                                                "strAtenderAntes"               => $strAtenderAntes,
                                                                "strEsHal"                      => $strEsHal,
                                                                "intIdSugerenciaHal"            => $intIdSugerenciaHal
                                                                );
                            $arrayRespuestaAsignarResponsable = $this->servicePlanificacion->asignarPlanificacion($arrayParametrosAsignacion);
                            if(isset($arrayRespuestaAsignarResponsable["mensaje"]) && !empty($arrayRespuestaAsignarResponsable["mensaje"])
                                && ($arrayRespuestaAsignarResponsable["mensaje"] == "Se asignaron la(s) Tarea(s) Correctamente."
                                    || $arrayRespuestaAsignarResponsable["mensaje"] == "Se replanifico la solicitud"
                                    || strrpos($arrayRespuestaAsignarResponsable["mensaje"], "Correctamente")))
                            {
                                $strMensajeSolGestionSimultanea .= " y asignación de responsable para la "
                                                                   .$strDescripSolSimultanea." # ".$intIdSolSimultanea.".";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea         .= ", pero no se realiza correctamente la asignación de responsable "
                                                                            ."para la ".$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                                $boolMostrarMsjErrorUsrSolSimultanea    = true;
                                throw new \Exception($arrayRespuestaAsignarResponsable["mensaje"]); 
                            }
                        }
                    }
                    catch (\Exception $e)
                    {
                        if($boolMostrarMsjErrorUsrSolSimultanea)
                        {
                            $strMensajeSolGestionSimultanea .= "Error: ".$e->getMessage();
                        }
                        else
                        {
                            if(empty($strMensajeSolGestionSimultanea))
                            {
                                $strMensajeSolGestionSimultanea .= "Ha";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea .= ", pero ha";
                            }
                            $strMensajeSolGestionSimultanea .= " ocurrido un error inesperado al reprogramar la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea
                                                               .". Comuníquese con el Dep. de Sistemas para su revisión.";
                            error_log("Error solicitud #".$intIdSolSimultanea."-".$e->getMessage());
                        }
                    }
                    $strMensaje .= $strMensajeSolGestionSimultanea."<br>";
                }
                $strMensaje = $strMensajeEjecucionSolGestionada."<br><b>Gestión simultánea</b><br>".$strMensaje;
            }
            else
            {
                $strMensaje = $strMensajeEjecucionSolGestionada;
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if($boolMostrarMensajeErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar la replanificación simultánea por un error desconocido. Comuníquese con el Dep. de Sistemas!";
                error_log("Error en reprogramarPlanificacionSimultanea ".$e->getMessage());
            }
            $strMensaje = $strMensajeEjecucionSolGestionada . "<br><b>Gestión simultánea</b><br>".$strMensaje;
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        
        return $arrayRespuesta;
    }
    
    /**
     * 
     * Función ejecutada después de detener una solicitud, ejecutando la detención de las solicitudes relacionadas
     *
     * @param array $arrayParametros [
     *                                  "intIdSolGestionada"                => id de la solicitud que se ha gestionado previamente
     *                                  "strOpcionGestionSimultanea"        => opción que se ejecuta desde el grid de PYL
     *                                  "strMensajeEjecucionSolGestionada"  => mensaje de la gestión de la solicitud gestionada proviamente
     *                                  "strOrigen"                         => opción enviada desde el el grid como 'local'
     *                                  "intIdMotivo"                       => id del motivo
     *                                  "intIdDepartamentoSession"          => id del departamento del usuario en sesión
     *                                  "objRequest"                        => objeto del request
     *                                  "strCodEmpresa"                     => id de la empresa
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "intIdDepartamentoSession"          => id del departamento en sesión
     *                                  "intIdEmpleadoSession"              => id del usuario en sesión
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     */
    public function detenerPlanificacionSimultanea($arrayParametros)
    {
        $intIdSolGestionada                 = $arrayParametros['intIdSolGestionada'];
        $strOpcionGestionSimultanea         = $arrayParametros['strOpcionGestionSimultanea'];
        $intIdMotivo                        = $arrayParametros['intIdMotivo'];
        $strMensajeEjecucionSolGestionada   = $arrayParametros['strMensajeEjecucionSolGestionada'];
        $strCodEmpresa                      = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa                  = $arrayParametros["strPrefijoEmpresa"];
        $intIdDepartamentoSession           = $arrayParametros["intIdDepartamentoSession"];
        $intIdEmpleadoSession               = $arrayParametros["intIdEmpleadoSession"];
        $objRequest                         = $arrayParametros["objRequest"];
        $strIpCreacion                      = $arrayParametros["strIpCreacion"];
        $strUsrCreacion                     = $arrayParametros["strUsrCreacion"];
        $strMensaje                         = "";
        $boolMostrarMensajeErrorUsr         = false;
        try
        {
            if(!isset($intIdSolGestionada) || empty($intIdSolGestionada) || !isset($strOpcionGestionSimultanea) || empty($strOpcionGestionSimultanea))
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se han enviado correctamente los parámetros para realizar la replanificación simultánea");
            }
            
            $arrayRespuestaGetInfoSimultanea        = $this->serviceServicioTenico
                                                           ->getInfoGestionSimultanea(array(
                                                                                    "intIdSolicitud"                => $intIdSolGestionada,
                                                                                    "strOpcionGestionSimultanea"    => $strOpcionGestionSimultanea,
                                                                                    "strTipoOpcionGestionSimultanea"=> "EJECUCION"));
            $arrayRegistrosInfoGestionSimultanea    = $arrayRespuestaGetInfoSimultanea["arrayRegistrosInfoGestionSimultanea"];
            
            if(isset($arrayRegistrosInfoGestionSimultanea) && !empty($arrayRegistrosInfoGestionSimultanea))
            {
                foreach($arrayRegistrosInfoGestionSimultanea as $arrayRegistroInfoGestionSimultanea)
                {
                    $strDescripSolGestionada                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_GESTIONADA"];
                    $intIdSolSimultanea                     = $arrayRegistroInfoGestionSimultanea["ID_SOL_SIMULTANEA"];
                    $strDescripSolSimultanea                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_SIMULTANEA"];
                    $strObservacionSolSimultanea            = "Solicitud gestionada simultáneamente por ".
                                                              $strDescripSolGestionada." #".$intIdSolGestionada.".";
                    $boolMostrarMsjErrorUsrSolSimultanea    = false;
                    $strMensajeSolGestionSimultanea         = "";
                    try
                    {
                        $arrayParametrosDetener = array("intIdSolicitud"            => $intIdSolSimultanea,
                                                        "intIdMotivo"               => $intIdMotivo,
                                                        "strObservacion"            => $strObservacionSolSimultanea,
                                                        "strCodEmpresa"             => $strCodEmpresa,
                                                        "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                        "intIdDepartamentoSession"  => $intIdDepartamentoSession,
                                                        "intIdEmpleadoSession"      => $intIdEmpleadoSession,
                                                        "objRequest"                => $objRequest,
                                                        "strIpCreacion"             => $strIpCreacion,
                                                        "strUsrCreacion"            => $strUsrCreacion);
                        $arrayResultadoDetener  = $this->detenerPlanificacion($arrayParametrosDetener);
                        $strStatusDetener       = $arrayResultadoDetener["status"];
                        $strMensajeDetener      = $arrayResultadoDetener["mensaje"];
                        if($strStatusDetener === "OK")
                        {
                            $strMensajeSolGestionSimultanea .= "Se detiene correctamente la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea.".";
                        }
                        else
                        {
                            $strMensajeSolGestionSimultanea         .= "No se ha podido detener la "
                                                                       .$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                            $boolMostrarMsjErrorUsrSolSimultanea    = true;
                            throw new \Exception($strMensajeDetener); 
                        }
                    }
                    catch (\Exception $e)
                    {
                        if($boolMostrarMsjErrorUsrSolSimultanea)
                        {
                            $strMensajeSolGestionSimultanea .= "Error: ".$e->getMessage();
                        }
                        else
                        {
                            if(empty($strMensajeSolGestionSimultanea))
                            {
                                $strMensajeSolGestionSimultanea .= "Ha";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea .= ", pero ha";
                            }
                            $strMensajeSolGestionSimultanea .= " ocurrido un error inesperado al detener la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea
                                                               .". Comuníquese con el Dep. de Sistemas para su revisión.";
                            error_log("Error solicitud #".$intIdSolSimultanea."-".$e->getMessage());
                        }
                    }
                    $strMensaje .= $strMensajeSolGestionSimultanea."<br>";
                }
                $strMensaje = $strMensajeEjecucionSolGestionada."<br><b>Gestión simultánea</b><br>".$strMensaje;
            }
            else
            {
                $strMensaje = $strMensajeEjecucionSolGestionada;
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if($boolMostrarMensajeErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido detener simultáneamente por un error desconocido. Comuníquese con el Dep. de Sistemas!";
                error_log("Error en detenerPlanificacionSimultanea ".$e->getMessage());
            }
            $strMensaje = $strMensajeEjecucionSolGestionada . "<br><b>Gestión simultánea</b><br>".$strMensaje;
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        
        return $arrayRespuesta;
    }
    
    
    /**
     * 
     * Función ejecutada después de rechazar una solicitud, ejecutando el rechazo de las solicitudes relacionadas
     *
     * @param array $arrayParametros [
     *                                  "intIdSolGestionada"                => id de la solicitud que se ha gestionado previamente
     *                                  "strOpcionGestionSimultanea"        => opción que se ejecuta desde el grid de PYL
     *                                  "intIdMotivo"                       => id del motivo
     *                                  "strMensajeEjecucionSolGestionada"  => mensaje de la gestión de la solicitud gestionada proviamente
     *                                  "objRequest"                        => objeto del request
     *                                  "strCodEmpresa"                     => id de la empresa
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     */
    public function rechazarPlanificacionSimultanea($arrayParametros)
    {
        $intIdSolGestionada                 = $arrayParametros['intIdSolGestionada'];
        $strOpcionGestionSimultanea         = $arrayParametros['strOpcionGestionSimultanea'];
        $intIdMotivo                        = $arrayParametros['intIdMotivo'];
        $strMensajeEjecucionSolGestionada   = $arrayParametros['strMensajeEjecucionSolGestionada'];
        $strCodEmpresa                      = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa                  = $arrayParametros["strPrefijoEmpresa"];
        $objRequest                         = $arrayParametros["objRequest"];
        $strIpCreacion                      = $arrayParametros["strIpCreacion"];
        $strUsrCreacion                     = $arrayParametros["strUsrCreacion"];
        $strMensaje                         = "";
        $boolMostrarMensajeErrorUsr         = false;
        try
        {
            if(!isset($intIdSolGestionada) || empty($intIdSolGestionada) || !isset($strOpcionGestionSimultanea) || empty($strOpcionGestionSimultanea))
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se han enviado correctamente los parámetros para realizar la replanificación simultánea");
            }
            
            $arrayRespuestaGetInfoSimultanea        = $this->serviceServicioTenico
                                                           ->getInfoGestionSimultanea(array(
                                                                                    "intIdSolicitud"                => $intIdSolGestionada,
                                                                                    "strOpcionGestionSimultanea"    => $strOpcionGestionSimultanea,
                                                                                    "strTipoOpcionGestionSimultanea"=> "EJECUCION"));
            $arrayRegistrosInfoGestionSimultanea    = $arrayRespuestaGetInfoSimultanea["arrayRegistrosInfoGestionSimultanea"];
            
            if(isset($arrayRegistrosInfoGestionSimultanea) && !empty($arrayRegistrosInfoGestionSimultanea))
            {
                foreach($arrayRegistrosInfoGestionSimultanea as $arrayRegistroInfoGestionSimultanea)
                {
                    $strDescripSolGestionada                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_GESTIONADA"];
                    $intIdSolSimultanea                     = $arrayRegistroInfoGestionSimultanea["ID_SOL_SIMULTANEA"];
                    $strDescripSolSimultanea                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_SIMULTANEA"];
                    $strObservacionSolSimultanea            = "Solicitud gestionada simultáneamente por ".
                                                              $strDescripSolGestionada." #".$intIdSolGestionada.".";
                    $boolMostrarMsjErrorUsrSolSimultanea    = false;
                    $strMensajeSolGestionSimultanea         = "";
                    try
                    {
                        $arrayParametrosRechazar    = array("intIdSolicitud"            => $intIdSolSimultanea,
                                                            "intIdMotivo"               => $intIdMotivo,
                                                            "strObservacion"            => $strObservacionSolSimultanea,
                                                            "strCodEmpresa"             => $strCodEmpresa,
                                                            "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                            "objRequest"                => $objRequest,
                                                            "strIpCreacion"             => $strIpCreacion,
                                                            "strUsrCreacion"            => $strUsrCreacion);
                        $arrayResultadoRechazar  = $this->rechazarPlanificacion($arrayParametrosRechazar);
                        $strStatusRechazar       = $arrayResultadoRechazar["status"];
                        $strMensajeRechazar      = $arrayResultadoRechazar["mensaje"];
                        if($strStatusRechazar === "OK")
                        {
                            $strMensajeSolGestionSimultanea .= "Se realiza correctamente el rechazo de la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea.".";
                        }
                        else
                        {
                            $strMensajeSolGestionSimultanea         .= "No se ha podido rechazar la "
                                                                       .$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                            $boolMostrarMsjErrorUsrSolSimultanea    = true;
                            throw new \Exception($strMensajeRechazar); 
                        }
                    }
                    catch (\Exception $e)
                    {
                        if($boolMostrarMsjErrorUsrSolSimultanea)
                        {
                            $strMensajeSolGestionSimultanea .= "Error: ".$e->getMessage();
                        }
                        else
                        {
                            if(empty($strMensajeSolGestionSimultanea))
                            {
                                $strMensajeSolGestionSimultanea .= "Ha";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea .= ", pero ha";
                            }
                            $strMensajeSolGestionSimultanea .= " ocurrido un error inesperado al rechazar la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea
                                                               .". Comuníquese con el Dep. de Sistemas para su revisión.";
                            error_log("Error solicitud #".$intIdSolSimultanea."-".$e->getMessage());
                        }
                    }
                    $strMensaje .= $strMensajeSolGestionSimultanea."<br>";
                }
                $strMensaje = $strMensajeEjecucionSolGestionada."<br><b>Gestión simultánea</b><br>".$strMensaje;
            }
            else
            {
                $strMensaje = $strMensajeEjecucionSolGestionada;
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if($boolMostrarMensajeErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar el rechazo simultáneo por un error desconocido. Comuníquese con el Dep. de Sistemas!";
                error_log("Error en rechazarPlanificacionSimultanea ".$e->getMessage());
            }
            $strMensaje = $strMensajeEjecucionSolGestionada . "<br><b>Gestión simultánea</b><br>".$strMensaje;
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    
    /**
     * 
     * Función ejecutada después de anular una solicitud, ejecutando la anulación de las solicitudes relacionadas
     *
     * @param array $arrayParametros [
     *                                  "intIdSolGestionada"                => id de la solicitud que se ha gestionado previamente
     *                                  "strOpcionGestionSimultanea"        => opción que se ejecuta desde el grid de PYL
     *                                  "intIdMotivo"                       => id del motivo
     *                                  "strMensajeEjecucionSolGestionada"  => mensaje de la gestión de la solicitud gestionada proviamente
     *                                  "objRequest"                        => objeto del request
     *                                  "strPrefijoEmpresa"                 => prefijo de la empresa
     *                                  "strIpCreacion"                     => ip de creación
     *                                  "strUsrCreacion"                    => usuario de creación
     *                                ]
     * 
     * @return array $arrayRespuesta[
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => mensaje de la ejecución de la función
     *                              ]
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2021
     * 
     */
    public function anularPlanificacionSimultanea($arrayParametros)
    {
        $intIdSolGestionada                 = $arrayParametros['intIdSolGestionada'];
        $strOpcionGestionSimultanea         = $arrayParametros['strOpcionGestionSimultanea'];
        $intIdMotivo                        = $arrayParametros['intIdMotivo'];
        $strMensajeEjecucionSolGestionada   = $arrayParametros['strMensajeEjecucionSolGestionada'];
        $strPrefijoEmpresa                  = $arrayParametros['strPrefijoEmpresa'];
        $objRequest                         = $arrayParametros['objRequest'];
        $strMensaje                         = "";
        $boolMostrarMensajeErrorUsr         = false;
        try
        {
            if(!isset($intIdSolGestionada) || empty($intIdSolGestionada) || !isset($strOpcionGestionSimultanea) || empty($strOpcionGestionSimultanea))
            {
                $boolMostrarMensajeErrorUsr = true;
                throw new \Exception("No se han enviado correctamente los parámetros para realizar la replanificación simultánea");
            }
            
            $arrayRespuestaGetInfoSimultanea        = $this->serviceServicioTenico
                                                           ->getInfoGestionSimultanea(array(
                                                                                    "intIdSolicitud"                => $intIdSolGestionada,
                                                                                    "strOpcionGestionSimultanea"    => $strOpcionGestionSimultanea,
                                                                                    "strTipoOpcionGestionSimultanea"=> "EJECUCION"));
            $arrayRegistrosInfoGestionSimultanea    = $arrayRespuestaGetInfoSimultanea["arrayRegistrosInfoGestionSimultanea"];
            
            if(isset($arrayRegistrosInfoGestionSimultanea) && !empty($arrayRegistrosInfoGestionSimultanea))
            {
                foreach($arrayRegistrosInfoGestionSimultanea as $arrayRegistroInfoGestionSimultanea)
                {
                    $strDescripSolGestionada                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_GESTIONADA"];
                    $intIdSolSimultanea                     = $arrayRegistroInfoGestionSimultanea["ID_SOL_SIMULTANEA"];
                    $strDescripSolSimultanea                = $arrayRegistroInfoGestionSimultanea["DESCRIP_TIPO_SOL_SIMULTANEA"];
                    $strObservacionSolSimultanea            = "Solicitud gestionada simultáneamente por ".
                                                              $strDescripSolGestionada." #".$intIdSolGestionada.".";
                    $boolMostrarMsjErrorUsrSolSimultanea    = false;
                    $strMensajeSolGestionSimultanea         = "";
                    try
                    {
                        $strRespuestaAnulacion  = $this->serviceCoordinar->anularOrdenDeTrabajo($intIdSolSimultanea,
                                                                                                $intIdMotivo,
                                                                                                $strObservacionSolSimultanea,
                                                                                                $strPrefijoEmpresa,
                                                                                                $objRequest
                                                                                               );
                        if(isset($strRespuestaAnulacion) && !empty($strRespuestaAnulacion) && $strRespuestaAnulacion === "Se anulo la solicitud")
                        {
                            $strMensajeSolGestionSimultanea .= "Se realiza correctamente la anulación de la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea.".";
                        }
                        else
                        {
                            $strMensajeSolGestionSimultanea         .= "No se ha podido realizar correctamente la anulación para la "
                                                                       .$strDescripSolSimultanea." # ".$intIdSolSimultanea.". ";
                            $boolMostrarMsjErrorUsrSolSimultanea    = true;
                            throw new \Exception($strRespuestaAnulacion); 
                        }
                    }
                    catch (\Exception $e)
                    {
                        if($boolMostrarMsjErrorUsrSolSimultanea)
                        {
                            $strMensajeSolGestionSimultanea .= "Error: ".$e->getMessage();
                        }
                        else
                        {
                            if(empty($strMensajeSolGestionSimultanea))
                            {
                                $strMensajeSolGestionSimultanea .= "Ha";
                            }
                            else
                            {
                                $strMensajeSolGestionSimultanea .= ", pero ha";
                            }
                            $strMensajeSolGestionSimultanea .= " ocurrido un error inesperado al anular la "
                                                               .$strDescripSolSimultanea." # ".$intIdSolSimultanea
                                                               .". Comuníquese con el Dep. de Sistemas para su revisión.";
                            error_log("Error solicitud #".$intIdSolSimultanea."-".$e->getMessage());
                        }
                    }
                    $strMensaje .= $strMensajeSolGestionSimultanea."<br>";
                }
                $strMensaje = $strMensajeEjecucionSolGestionada."<br><b>Gestión simultánea</b><br>".$strMensaje;
            }
            else
            {
                $strMensaje = $strMensajeEjecucionSolGestionada;
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            if($boolMostrarMensajeErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar la replanificación simultánea por un error desconocido. Comuníquese con el Dep. de Sistemas!";
                error_log("Error en anularPlanificacionSimultanea ".$e->getMessage());
            }
            $strMensaje = $strMensajeEjecucionSolGestionada . "<br><b>Gestión simultánea</b><br>".$strMensaje;
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        
        return $arrayRespuesta;
    }
    
}
