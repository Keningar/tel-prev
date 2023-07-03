<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoCuadrillaTarea;
use telconet\schemaBundle\Entity\InfoDetalleColaborador;
use telconet\schemaBundle\Entity\InfoElementoTrazabilidad;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoUbicacion;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Service\UtilService;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoTareaCaracteristica;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;


/**
 * Clase PlanificarService
 *
 * Clase que se encarga de realizar las acciones de Planificacion en linea
 *
 * @author Edgar Pin Villavicencio <epin@telconet.ec>
 * @version 1.0 02-02-2018
 * 
 * @author John Vera <javera@telconet.ec>
 * @version 1.1 19-12-2018 se aumenta en el método construct la llamada al service infoTelefoniaService
 */
class PlanificarService
{
    /**
     * Codigo de respuesta: Respuesta valida
     */
    public static $strStatusOK = 200;

    private $container;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComunicacion;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emSoporte;
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emSeguridad;
    private $templating;
    private $mailer;
    private $restClient;
    private $ambienteEjecuta;
    private $microServicioRestURL;
    private $mailerSend;
    private $serviceSoporte;
    private $serviceGeneral;
    private $envioPlantilla;
    private $serviceEnvioSms;
    private $infoElementoService;
    private $utilServicio;
    private $restClientPedidos;
    private $serviceRecursosRed;
    private $serviceCoordinar2;
    private $serviceTelcoCrm;
    /**
     * string $strApiSmsUserNameMd
     */
    private $strApiSmsUserNameMd;

    /**
     * string $strApiSmsPasswordMd
     */
    private $strApiSmsPasswordMd;

    /**
     * string $strApiSmsSourceNameMd
     */
    private $strApiSmsSourceNameMd;
    
        /**
     * string $serviceTokenCas
     */
    private $serviceTokenCas;

    /**
     * string $strMassSendUrl
     */
    private $strMassSendUrl;

    /*
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 15-04-2021 Se inicializa de manera correcta los services y entities manager usados en este service, cambiándolo de construct 
     *                         a dependencies
     */
    public function setDependencies(Container $objContainer)
    {
        $this->container            = $objContainer;
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager("telconet_infraestructura");
        $this->emSoporte            = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager("telconet_seguridad");
        $this->templating           = $objContainer->get('templating');
        $this->mailer               = $objContainer->get('mailer');
        $this->mailerSend           = $objContainer->getParameter('mailer_send');
        $this->restClient           = $objContainer->get('schema.RestClient');
        $this->microServicioRestURL = $objContainer->getParameter('microServicio_webService_url');
        $this->ambienteEjecuta      = $objContainer->getParameter('microServicio_webService_ambiente_ejecuta');
        $this->serviceSoporte       = $objContainer->get('soporte.SoporteService');
        $this->serviceGeneral       = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->envioPlantilla       = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceActivar       = $objContainer->get('tecnico.InfoActivarPuerto');
        $this->serviceRecursosRed   = $objContainer->get('planificacion.RecursosDeRed');
        $this->serviceEnvioSms      = $this->container->get('comunicaciones.SMS');
        $this->serviceTelefonia     = $objContainer->get('tecnico.InfoTelefonia');
        $this->infoElementoService  = $objContainer->get('tecnico.infoelemento');
        $this->utilServicio         = $objContainer->get('schema.util');
        $this->restClientPedidos    = $objContainer->get('schema.RestClient');
        $this->strMicroServUrl      = $objContainer->getParameter('microservicio_webservice_url');
        $this->serviceCoordinar2     = $objContainer->get('planificacion.Coordinar2');
        $this->strApiSmsUserNameMd   = $objContainer->getParameter('comunicacion.api_sms_username_MD');
        $this->strApiSmsPasswordMd   = $objContainer->getParameter('comunicacion.api_sms_password_MD');
        $this->strApiSmsSourceNameMd = $objContainer->getParameter('comunicacion.api_sms_source_name_MD');
        $this->serviceTokenCas     =  $this->container->get('seguridad.TokenCas');
        $this->strMassSendUrl       = $objContainer->getParameter('notification.sms_ms_url_send');
        $this->serviceTelcoCrm      = $objContainer->get('comercial.ComercialCrm');   
        $this->emNaf                 = $objContainer->get('doctrine.orm.telconet_naf_entity_manager');

    }
    
    /*
     * Service para coordinar Solicitudes
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 02-02-2018
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 10-06-2018 bug.- se modifica para que no se guarde la cuadrilla en los cupos solo se guarda el id de solicitud
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 15-11-2018 Se corrige error al realizar commit luego de un rollback cuando se lanza alguna exception
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 28-11-2018 Se realiza validación para que la SOLICITUD AGREGAR EQUIPO asociada a un
     *                         a un producto Extender Dual Band siga el flujo de una SOLICITUD PLANIFICACION
     * @author José Alava <jialava@telconet.ec>
     * @version 1.4 1-04-2019 Se añadió que hosting pase a estado asignado ya que se solicitó que hosting pase de preplanificado a asignado
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 19-06-2019 Se agrega programación para gestionar solicitudes de cambio de equipo soporte
     * @since 1.4
     * 
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.5 28-11-2019 Se modifica método para agregar retornar el objeto de los hitoriales de solicitud y servicio.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.6 14-11-2019 Se añade tilde a mensaje que se muestra al usuario
     * @since 1.6
     * 
     * @author Christian Jaramillo Espinoza <cjaramilloe@telconet.ec>
     * @version 1.7 07-02-2020  Cambio de proveedor a infobip para envio de sms.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.9 19-06-2020 - Se agrega validación para el producto Cableado Estructurado para que el estado
     *                           pase a 'Asignada'
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 19-09-2020 Se agrega validación para flujo de coordinación de servicios W+AP con solicitudes de planificación o de agregar equipo
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.11 23-09-2020 - Se valida para productos adicionales permitir gestion despues de la gestion del servicio principal.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.12 10-03-2021 - Se anexa invocacion para cambiar los estados de productos adicionales con el servicios principal
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.13 12-05-2021 - Se correge bug que creaba tareas erroneas en OT que no eran de solicitudes de planificacion
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.13 12-05-2021 - Se corrige bug que creaba tareas erroneas en OT que no eran de solicitudes de planificacion
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.14 05-07-2021 - Se invoca a metodo para realizar planifiacion simultanea de productos manuales
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.15 29-06-2022 - Se incluye el parametro de HAL para los procesos simultaneos de EDB
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.9 20-03-2023 - Se agrega validacion por prefijo empresa Ecuanet (EN) para validar el cupo de planificacion disponible. 
     * 
     */
    public function coordinarPlanificacion($arrayParametros)
    {
        $intIdFactibilidad    = $arrayParametros['intIdFactibilidad'];
        $strParamResponsables = $arrayParametros['strParamResponsables'];
        $strOrigen            = $arrayParametros['strOrigen'];
        $arrayF               = $arrayParametros['dateF'];

        $arrayFechaInicio        = $arrayParametros['strFechaInicio'];
        $arrayFechaFin           = $arrayParametros['strFechaFin'];
        $strHoraInicioServicio   = $arrayParametros['strHoraInicioServicio'];
        $strHoraFinServicio      = $arrayParametros['strHoraFinServicio'];
        $strObservacionServicio  = $arrayParametros['strObservacionServicio'];
        $strIpCreacion           = $arrayParametros['strIpCreacion'];
        $strUsrCreacion          = $arrayParametros['strUsrCreacion'];
        $strObservacionSolicitud = $arrayParametros['strObservacionSolicitud'];
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'];
        $strCodEmpresa           = $arrayParametros['strCodEmpresa'];
        
        $objSolicitud     = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdFactibilidad);
        $strTipoSolicitud = strtolower($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());
        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        try
        {
            $objCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')->findOneBy(
                                                                                    array('descripcionCaracteristica' => 'PRODUCTO_ADICIONAL',
                                                                                          'estado' => 'Activo'));
            if(is_object($objCaracteristica) && !empty($objCaracteristica))
            {
                
                $objDetalleSol    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')->findOneBy(
                                                                                        array('detalleSolicitudId' => $objSolicitud->getId(),
                                                                                              'caracteristicaId'   => $objCaracteristica->getId()));
            }
            if($objSolicitud)
            {
                $entityServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objSolicitud->getServicioId());
                // se agrega validacion del estado del servicio para bloquear operaciones incorrectas
                if($entityServicio->getEstado() == "Activo" &&
                   ($strTipoSolicitud != 'solicitud migracion' &&
                    $strTipoSolicitud != 'solicitud agregar equipo' &&
                    $strTipoSolicitud != 'solicitud agregar equipo masivo' &&
                    $strTipoSolicitud != 'solicitud cambio equipo por soporte' &&
                    $strTipoSolicitud != 'solicitud cambio equipo por soporte masivo' &&
                    $strTipoSolicitud != 'solicitud de instalacion cableado ethernet' &&
                    $strTipoSolicitud != 'solicitud reubicacion') && !is_object($objDetalleSol)

                  )
                {
                    $arrayResultado['codigoRespuesta'] = 0; 
                    $arrayResultado['mensaje'] = "El servicio Actualmente se encuentra con estado Activo, no es posible Coordinar.";
                    return $arrayResultado;
                }

                //Verificar si el subgrupo es HOUSING
                $objProducto = $entityServicio->getProductoId();
                                
                if (is_object($objProducto))
                {
                    //Consultamos si el producto requiere flujo ya que antes no lo tenia
                    $arrayParametrosRequiereFlujo =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne("REQUIERE_FLUJO", 
                                                                             "TECNICO", 
                                                                             "", 
                                                                             "", 
                                                                             $objProducto->getDescripcionProducto(), 
                                                                             "", 
                                                                             "",
                                                                             "",
                                                                             "",
                                                                             10
                                                                           );
                    if(!is_array($arrayParametrosRequiereFlujo) && empty($arrayParametrosRequiereFlujo))
                    {
                        $boolRequiereFlujo = false;
                    }
                    else
                    {
                        $boolRequiereFlujo = true;
                    }
                }
                                       
                if(is_object($objProducto))
                {
                    //verificar si tiene la caracteristica VENTA_EXTERNA
                    $objCaracteristicaExterna = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                   ->findOneBy(array("descripcionCaracteristica" => "VENTA_EXTERNA",
                                                                                     "estado"                    => "Activo"));
                    if(is_object($objCaracteristicaExterna))
                    {
                        $objProCaract =   $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                            ->findOneBy(array(  "productoId"       => $objProducto->getId(),
                                                                                "caracteristicaId" => $objCaracteristicaExterna->getId()));
                        if(is_object($objProCaract))
                        {
                            $strParametroDominio    = '';

                            //obtengo los parametros
                            $objParametro =   $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                                ->findOneBy(array("nombreParametro" => 'PARAMETROS NETVOICE',
                                                                                  "estado"          => 'Activo'));
                            if(is_object($objParametro))
                            {
                                $objParametroDominio =$this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->findOneBy(array(  "descripcion" => 'DOMINIO',
                                                                                            "parametroId" => $objParametro->getId(),
                                                                                            "estado"      => 'Activo'));

                                if(is_object($objParametroDominio))
                                {
                                    $strParametroDominio = $objParametroDominio->getValor1();
                                }

                            }

                            $arrayParametrosLinea = array();

                            $arrayParametrosLinea['intServicio']        = $entityServicio->getId();
                            $arrayParametrosLinea['strPrefijoEmpresa']  = $strPrefijoEmpresa;
                            $arrayParametrosLinea['strUser']            = $strUsrCreacion;
                            $arrayParametrosLinea['strIp']              = $strIpCreacion ;
                            $arrayResultadoLinea = $this->serviceTelefonia->asignarLineaMD($arrayParametrosLinea);                                   

                            if($arrayResultadoLinea['status'] == 'OK')
                            {
                                $strDatosTelefonia = 'Línea en proceso de activación: '
                                    . '<br> Número Telefónico: ' . $arrayResultadoLinea['strTelefono']
                                    . '<br> Contraseña: ' . $arrayResultadoLinea['strContrasena']
                                    . '<br> Dominio: ' . $strParametroDominio.'<br><br>';

                                $entityServicio->setObservacion($strDatosTelefonia);
                                $this->emComercial->persist($entityServicio);
                                $this->emComercial->flush();
                                
                            }
                            else
                            {
                                throw new \Exception(' No se asigno la línea ' . $arrayResultadoLinea['mensaje']);
                            }
                        }
                    }
                }

                $strEstadoPlanificacion = 'Planificada';

                if(is_object($objProducto) && ($objProducto->getNombreTecnico() == 'HOUSING' || $objProducto->getNombreTecnico() == 'HOSTING') )
                {
                    $strEstadoPlanificacion = 'Asignada';
                }
               
                //Si el producto requiere Flujo el estado de planificación pasa a 'Asignada'
                if (is_object($objProducto) && $boolRequiereFlujo)
                {
                    $strEstadoPlanificacion = 'AsignadoTarea';
                }
                else
                {
                    if(is_object($objProducto) && $objProducto->getNombreTecnico() === "WDB_Y_EDB" 
                        && $strTipoSolicitud == "solicitud agregar equipo")
                    {
                        $strPermiteSolAgregarEquipoWyAp = "SI";
                    }
                    else
                    {
                        $strPermiteSolAgregarEquipoWyAp = "NO";
                    }
                    // Obtenemos los parametros de productos adicionales
                    $arrayProdPermitidos = array();
                    $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                                'Productos adicionales manuales para planificar simultaneo',
                                                '','','','','',$strCodEmpresa);
                    if (is_array($arrayParamValores) && !empty($arrayParamValores))
                    {
                        $arrayProdPermitidos = $this->utilServicio->obtenerValoresParametro($arrayParamValores);
                    }
                    $intIdPunto = $entityServicio->getPuntoId()->getId();
                    $strEstadoAnt = $entityServicio->getEstado();
                    // Verificamos si es un proceso principal o simultaneo
                    $strBanSimultaneo = $arrayParametros['strProcesoSimultaneo'];
                    if (empty($strBanSimultaneo))
                    {
                        $strBanSimultaneo = "NO";
                    }
                    if($strTipoSolicitud == "solicitud planificacion" 
                    || (is_object($objProducto) && $objProducto->getNombreTecnico() === "EXTENDER_DUAL_BAND"
                        && ($strTipoSolicitud == "solicitud agregar equipo" || $strTipoSolicitud == "solicitud agregar equipo masivo"))
                    || (is_object($objProducto) && $objProducto->getNombreTecnico() === "WIFI_DUAL_BAND"
                        && ($strTipoSolicitud == "solicitud agregar equipo" || $strTipoSolicitud == "solicitud agregar equipo masivo"))
                    || $strPermiteSolAgregarEquipoWyAp === "SI"
                    )
                    {
                        // Se invoca metodo para cambiar el estado de los productos adicionales
                        if(!is_object($objDetalleSol))
                        {
                            $entityServicio->setEstado($strEstadoPlanificacion);
                            $this->emComercial->persist($entityServicio);
                            $this->emComercial->flush();
                        }
                        $strObservacionServicio   .= "<br>";
                        $arrayFechaIniPlanificada = $arrayF[2] . "/" . $arrayF[1] . "/" . $arrayF[0];
                        $strObservacionServicio   .= "<br>Fecha Planificada: " . $arrayFechaIniPlanificada;
                        $strObservacionServicio   .= "<br>Hora Inicio: " . $strHoraInicioServicio[0] . ":" . $strHoraInicioServicio[1];
                        $strObservacionServicio   .= "<br>Hora Fin: " . $strHoraFinServicio[0] . ":" . $strHoraFinServicio[1];
                        $strObservacionServicio   .= "<br><br>";

                        //GUARDAR INFO SERVICIO HISTORIAL
                        $entityServicioHistorial = new InfoServicioHistorial();
                        $entityServicioHistorial->setServicioId($entityServicio);
                        $entityServicioHistorial->setIpCreacion($strIpCreacion);
                        $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $entityServicioHistorial->setEstado($strEstadoPlanificacion);
                        $entityServicioHistorial->setObservacion($strObservacionServicio);
                        $this->emComercial->persist($entityServicioHistorial);
                        $this->emComercial->flush();

                        // Solo ingresa por planificacion principal
                        if ($strBanSimultaneo == "NO")
                        {
                            // Se invoca metodo para cambiar el estado de los productos adicionales
                            $strPlanServicio = $entityServicio->getPlanId();
                            $strProdServicio = $entityServicio->getProductoId();
                            if ((!empty($strPlanServicio) && $strTipoSolicitud == "solicitud planificacion") ||
                                (!empty($strProdServicio) && $strTipoSolicitud == "solicitud planificacion" &&
                                in_array($strProdServicio->getId(), $arrayProdPermitidos)))
                            {
                                $arrayDatosEstado = array(
                                    "intIdPunto" => $intIdPunto,
                                    "intIdServicio" => $entityServicio->getId(),
                                    "strEstado"=>$strEstadoPlanificacion,
                                    "strEstadoAnt"=>$strEstadoAnt,
                                    "strFechaInicio" => $arrayFechaInicio,
                                    "strFechaFin" => $arrayFechaFin,
                                    "strObservacionServicio" => $strObservacionServicio,
                                    "strObservacionSolicitud" => $strObservacionSolicitud,
                                    "strIpCreacion" => $strIpCreacion,
                                    "strUsrCreacion" => $strUsrCreacion,
                                    "strSolCableado" => "NO",
                                    "intIdEmpresa" => $strCodEmpresa,
                                    "strEsHal" => $arrayParametros['strEsHal']
                                );
                                $this->actualizaEstProdAdiManuales($arrayDatosEstado, null);
                            }

                            if (!empty($strPlanServicio) && $strTipoSolicitud == "solicitud planificacion")
                            {
                                $arrayDatosParametros = array(
                                    "objServicio"     => $entityServicio,
                                    "strEstActual"    => $strEstadoAnt,
                                    "strEstNuevo"    => "Pendiente",
                                    "strObservacion"  => "Se prepara servicio nuevamente para activacion",
                                    "intCodEmpresa"   => $strCodEmpresa,
                                    "strIpCreacion"   => $strIpCreacion,
                                    "strUserCreacion" => $strUsrCreacion
                                );
                                $this->serviceCoordinar2->cambioEstadosProdAdicionalesAut($arrayDatosParametros);
                            }
                        }

                        if($strOrigen == 'MOVIL')
                        {
                            $intJurisdicionId                          = $objSolicitud->getServicioId()
                                            ->getPuntoId()
                                            ->getPuntoCoberturaId()->getId();
                            $arrayParametrosRango['strFeInicio']       = $arrayFechaInicio;
                            $arrayParametrosRango['strFeFin']          = $arrayFechaFin;
                            $arrayParametrosRango['intJurisdiccionId'] = $intJurisdicionId;
                            $arrayRangos                               = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                    ->getRangoFecha($arrayParametrosRango);
                            $objCaracteristicaMobil                    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                    ->findOneByDescripcionCaracteristica("Planificacion desde Mobile");

                            $entityCaract = new InfoDetalleSolCaract();
                            $entityCaract->setCaracteristicaId($objCaracteristicaMobil);
                            $entityCaract->setValor("S");
                            $entityCaract->setDetalleSolicitudId($objSolicitud);
                            $entityCaract->setEstado("Activo");
                            $entityCaract->setUsrCreacion($strUsrCreacion);
                            $entityCaract->setFeCreacion(new \DateTime('now'));
                            $this->emComercial->persist($entityCaract);
                            $this->emComercial->flush();

                            foreach($arrayRangos as $arrayRango)
                            {
                                $entityCupoPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                        ->find($arrayRango['id']);
                                $entityCupoPlanificacion->setSolicitudId($intIdFactibilidad);
                                $this->emComercial->persist($entityCupoPlanificacion);
                                $this->emComercial->flush();
                            }
                        }
                    }

                    // Solo ingresa por planificacion principal
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
                            $arrayTiposSolicitudes = $this->utilServicio->obtenerValoresParametro($arrayParamValores);
                        }
                        $strProdServicio = $entityServicio->getProductoId();
                        if(!empty($strProdServicio) &&
                            in_array($strTipoSolicitud, $arrayTiposSolicitudes) &&
                            in_array($strProdServicio->getId(), $arrayProdPermitidos))
                        {
                            if ($entityServicio->getEstado() != 'Activo')
                            {
                                $entityServicio->setEstado($strEstadoPlanificacion);
                                $this->emComercial->persist($entityServicio);
                                $this->emComercial->flush();
                            }
                            else
                            {
                                $strEstadoAnt = 'PrePlanificada';
                            }

                            $strObservacionServicio   .= "<br>";
                            $arrayFechaIniPlanificada = $arrayF[2] . "/" . $arrayF[1] . "/" . $arrayF[0];
                            $strObservacionServicio   .= "<br>Fecha Planificada: " . $arrayFechaIniPlanificada;
                            $strObservacionServicio   .= "<br>Hora Inicio: " . $strHoraInicioServicio[0] . ":" . $strHoraInicioServicio[1];
                            $strObservacionServicio   .= "<br>Hora Fin: " . $strHoraFinServicio[0] . ":" . $strHoraFinServicio[1];
                            $strObservacionServicio   .= "<br><br>";

                            //GUARDAR INFO SERVICIO HISTORIAL
                            $entityServicioHistorial = new InfoServicioHistorial();
                            $entityServicioHistorial->setServicioId($entityServicio);
                            $entityServicioHistorial->setIpCreacion($strIpCreacion);
                            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                            $entityServicioHistorial->setEstado($strEstadoPlanificacion);
                            $entityServicioHistorial->setObservacion($strObservacionServicio);
                            $this->emComercial->persist($entityServicioHistorial);
                            $this->emComercial->flush();

                            $arrayDatosEstado = array(
                                "intIdPunto" => $intIdPunto,
                                "intIdServicio" => $entityServicio->getId(),
                                "strEstado"=>$strEstadoPlanificacion,
                                "strEstadoAnt"=>$strEstadoAnt,
                                "strFechaInicio" => $arrayFechaInicio,
                                "strFechaFin" => $arrayFechaFin,
                                "strObservacionServicio" => $strObservacionServicio,
                                "strObservacionSolicitud" => $strObservacionSolicitud,
                                "strIpCreacion" => $strIpCreacion,
                                "strUsrCreacion" => $strUsrCreacion,
                                "strSolCableado" => "SI",
                                "intIdEmpresa" => $strCodEmpresa,
                                "strEsHal" => $arrayParametros['strEsHal']
                            );
                            $this->actualizaEstProdAdiManuales($arrayDatosEstado, null);
                        }
                    }
                }

                if(is_object($objDetalleSol))
                {
                    $strEstadoPlanificacion = 'Asignada';
                }
                //ACTUALIZAR INFO DETALLE SOLICITUD
                $objSolicitud->setEstado($strEstadoPlanificacion);
                $objSolicitud->setObservacion(substr($strObservacionSolicitud, 0, 1499));
                $this->emComercial->persist($objSolicitud);
                $this->emComercial->flush();
                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist                   = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                $entityDetalleSolHist->setFeIniPlan(new \DateTime($arrayFechaInicio));
                $entityDetalleSolHist->setFeFinPlan(new \DateTime($arrayFechaFin));
                $entityDetalleSolHist->setObservacion(substr($strObservacionSolicitud, 0, 1499));
                $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $entityDetalleSolHist->setEstado($strEstadoPlanificacion);
                $this->emComercial->persist($entityDetalleSolHist);
                $this->emComercial->flush();
                $arrayResultado['objSolicitud']            = $objSolicitud;
                $arrayResultado['entityDetalleSolHist']    = $entityDetalleSolHist;
                $arrayResultado['entityServicio']          = $entityServicio;
                $arrayResultado['entityServicioHistorial'] = $entityServicioHistorial;
                $intIdMotivoSolicitud = $objSolicitud->getMotivoId();
                $objMotivoSolicitud = null;
                if($intIdMotivoSolicitud > 0)
                {
                    $objMotivoSolicitud = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivoSolicitud);
                }
                $strMensaje = $this->templating->render('planificacionBundle:Coordinar:notificacion.html.twig', array(
                    'detalleSolicitud'     => $objSolicitud,
                    'detalleSolicitudHist' => $entityDetalleSolHist,
                    'motivo'               => $objMotivoSolicitud));
                
                if ($strTipoSolicitud == 'solicitud cambio equipo por soporte' || $strTipoSolicitud == 'solicitud cambio equipo por soporte masivo')
                {
                    $strAsunto = "Planificacion de Solicitud Cambio de Equipo por Soporte #" . $objSolicitud->getId();
                }
                else
                {
                    $strAsunto = "Planificacion de Solicitud de Instalacion #" . $objSolicitud->getId();

                    if($strTipoSolicitud == "solicitud reubicacion")
                    {
                        $strAsunto = "Planificacion de Solicitud de reubicacion #" . $objSolicitud->getId();
                    }
                }

                //DESTINATARIOS....
                $arrayFormasContacto = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                        ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(), 'Correo Electronico');
                $arrayTo             = array();
                $arrayTo[]           = 'notificaciones_telcos@telconet.ec';
                
                if($arrayFormasContacto)
                {
                    foreach($arrayFormasContacto as $arrayFormaContacto)
                    {
                        $arrayTo[] = $arrayFormaContacto['valor'];
                    }
                }
                $arrayParamRespon = explode("|", $strParamResponsables);
                foreach($arrayParamRespon as $arrayResponsables)
                {
                    $arrayVariablesR = explode("@@", $arrayResponsables);

                    if($arrayVariablesR && count($arrayVariablesR) > 0)
                    {
                        $strBand   = $arrayVariablesR[1];
                        $strCodigo = $arrayVariablesR[2];
                    }
                }
                $this->envioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje);
                if($strPrefijoEmpresa == 'MD' || $strPrefijoEmpresa == 'EN')
                {
                    $intJurisdicionId                          = $objSolicitud->getServicioId()
                                    ->getPuntoId()
                                    ->getPuntoCoberturaId()->getId();
                    $arrayParametrosRango['strFeInicio']       = $arrayFechaInicio;
                    $arrayParametrosRango['strFeFin']          = $arrayFechaFin;
                    $arrayParametrosRango['intJurisdiccionId'] = $intJurisdicionId;
                    $arrayRangos                               = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                                   ->getRangoFecha($arrayParametrosRango);

                    foreach($arrayRangos as $arrayRango)
                    {                        
                        $entityCupoPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                ->find($arrayRango['id']);
                        $entityCupoPlanificacion->setSolicitudId($intIdFactibilidad);
                        $this->emComercial->persist($entityCupoPlanificacion);
                        $this->emComercial->flush();
                    }
                }
                $arrayResultado['mensaje'] = 'Se coordinó la solicitud';
                $arrayResultado['codigoRespuesta'] = 2; 
                //Envio el sms y el mail al usuario

                if($strOrigen == 'MOVIL')
                {

                    $strMensaje = 'Bienvenido a Netlife elegiste el dia ' . $arrayParametros['arrayFechaSms'] . ' a las  ' .
                            $strHoraInicioServicio[0] . ":" . $strHoraInicioServicio[1] .
                            ' para la instalacion de tu servicio, si requieres mayor ' .
                            'informacion contactate al 3920000.';

                    $arrayParametrosSMS               = array();
                    $arrayParametrosSMS['mensaje']    = $strMensaje;
                    $arrayParametrosSMS['numero']     = $arrayParametros['strNumeroTelefonico'];
                    $arrayParametrosSMS['user']       = $arrayParametros['strUsrCreacion'];
                    $arrayParametrosSMS['codEmpresa'] = isset($arrayParametros['strCodEmpresa']) ? $arrayParametros['strCodEmpresa'] : '18';

                    if($arrayParametrosSMS['numero'])
                    {
                        $arrayResponseSMS  = (array) $this->serviceEnvioSms->sendAPISMS($arrayParametrosSMS);

                        switch($arrayResponseSMS['code'])
                        {
                            case 202:
                                $arrayResponse['mensaje'] = "<br>SMS enviado correctamente";
                                $arrayData['status']      = 'OK';
                                $arrayData['mensaje']     = 'SMS enviado correctamente';

                                break;
                            default :
                                $arrayResponse['mensaje'] = "<br>SMS No enviado";
                                $arrayData['status']      = 'ERROR_SERVICE';
                                $arrayData['mensaje']     = $arrayResponseSMS['detail'];
                                break;
                        }
                        //======================================================================
                        // Si el envio del SMS se encuentra OK se procede a registrar el PIN para el usuario
                    }
                    else
                    {
                        $arrayData['status']  = 'ERROR_SERVICE';
                        $arrayData['mensaje'] = 'Inconvenientes con el envio del SMS';
                    }

                    $arrayParametros['strEmail'] = 'afayala@telconet.ec';
                    if($arrayParametros['strEmail'])
                    {

                        $strDireccion = $objSolicitud->getServicioId()
                                        ->getPuntoId()->getDireccion();
                        $strNombres   = $objSolicitud->getServicioId()
                                        ->getPuntoId()->getPersonaEmpresaRolId()
                                        ->getPersonaId()->getNombres() . " " .
                                        $objSolicitud->getServicioId()
                                        ->getPuntoId()->getPersonaEmpresaRolId()
                                        ->getPersonaId()->getApellidos();
                        $strMensaje   = $this->templating->render('planificacionBundle:Coordinar:notificacionPlanificacionMobile.html.twig', array(
                            'strFecha'     => $arrayParametros['arrayFechaSms'],
                            'strHora'      => $strHoraInicioServicio[0] . ":" . $strHoraInicioServicio[1],
                            'strDireccion' => $strDireccion,
                            'strNombres'   => $strNombres));

                        $strAsunto = "Visita para intalación del servicio";
                        $arrayTo   = array();
                        $arrayTo[] = $arrayParametros['strEmail'];

                        $this->envioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje);
                    }
                }
            }
            else
            {
                $arrayResultado['mensaje'] = 'No se pudo Coordinar';
            }
        }
        catch(\Exception $e)
        {
            $strMensajeError           = "Error: " . $e->getMessage();
            $arrayResultado['mensaje'] = $strMensajeError;
            $arrayResultado['codigoRespuesta'] = 0; 
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
            }
            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->rollback();
            }
            $this->emComercial->close();
            $this->emInfraestructura->close();
            $this->emSoporte->close();
            return $arrayResultado;
        }
        catch(DBALException $e)
        {
            $strMensajeError           = "Error dbal: " . $e->getMessage();
            $arrayResultado['mensaje'] = $strMensajeError;
            $arrayResultado['codigoRespuesta'] = 0; 
            return $arrayResultado;
        }
        $this->emComercial->getConnection()->commit();
        $this->emInfraestructura->getConnection()->commit();
        $this->emSoporte->getConnection()->commit();
        return $arrayResultado;
    }

    /**
     * Service para Asignar responsable a Solicitudes
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 02-02-2018
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 30-05-2018 - Se registra trazabilidad del elemento para las solicitudes de retiro de equipo
     * 
     * @author Modificado: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 10-06-2018 - Se asigna el id de cuadrilla en el cupo por el id de la solicitud 
     *
     * @version 1.1 30-05-2018 - Se registra trazabilidad del elemento para las solicitudes de retiro de equipo     
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 -11-06-2018  Se agrega metodo para asignacion de recursos automaticos para L2MPLS
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 -18-06-2018  Se valida existencia de objeto servicio cuando se requiera realizar una planificacion de retiro de equipo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 -22-06-2018  Se agrega en la observación de la tarea por solicitud de planificación el cambio de equipo de cpe wifi 
     *                           en la instalación de un servicio Small Business
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.6 -31-07-2018  Se agrega una nueva validación para detectar si ya exite una tarea Activa y en caso de no estarlo no se reutilizará
     *                           el mismo idDetalle permitiendo crear uno nuevo.
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.7 -25-08-2018  Se corrige para que no grabe la fecha null en info_detalle, cuando se replanifica una solicitud de instalacion
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 27-11-2018  Se corrige para que no grabe la fecha null en info_detalle, cuando se replanifica una solicitud de instalacion
     * @since 1.7
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 29-11-2018 Se realiza validación para que la SOLICITUD AGREGAR EQUIPO asociada a un
     *                         a un producto Extender Dual Band siga el flujo de una SOLICITUD PLANIFICACION
     * 
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.0 12-11-2018 Se realizan ajustes en el registro de la tarea con el fin de mostrar los números asignados por la empresa y no los
     *                         personales
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 2.1 19-12-2018 se llama a nueva función asignarLineaMD para el aprovisionamiento de las líneas de netvoice  
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 07-02-2019  Se agrega en la observación de la tarea por solicitud de planificación el cambio de equipo de cpe wifi 
     *                           en la instalación de un servicio TelcoHome
     *
     * @since 1.9
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 07-03-2019  Se elimina validación para servicios TelcoHome, ya que el producto no contará con ips adicionales
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.4 14-03-2019  Se agrega funcionalidad para agregar el tipo de esquema seleccionado
     * únicamente cuando el servicio seleccionado sea "Internet Wifi".
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.5 14-04-2019 Se agrega funcionalidad para que dependiendo del tipo de esquema se asigne al departamento
     * de RADIO (esquema 1) o IPCCL2 (esquema 2).
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.6 21-05-2019 - Se agrega funcionalidad para que cuando exista el párametro intIdIntWifiSim
     * se agregue en la observación de la tarea, un texto informativo que diga que es una instalación simultánea,
     * además la cantidad de AP's que deben ser instalados y la descripcion de la factura por si es necesario algun
     * equipo en particular.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 26-06-2019 Se agrega validación para guardar el ingeniero IPCCL2 para la migración de los servicios Small Business y TelcoHome
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.7 19-06-2019 Se agrega programación para poder gestionar solicitudes de cambio de equipo soporte
     * @since 2.5
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 03-07-2019 Se agrega validación para obtener la marca del olt al que se va a migrar, sólo cuando se trate de planes nuevos 
     *                          que necesiten equipos dual band en la migración, por lo que se agregará dicho detalle en la tarea asociada. 
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.9 27-08-2019 - Se agrega funcionalidad para que cuando el producto sea "Wifi Alquiler de Equipos", la tarea contenga la información
     *                           de todos los AP's a instalar en la misma orden.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.9 03-09-2019 Se modifica método para validar que si ya existe una tarea asociada al idDetalle no cree otra, 
     * y cambie el estado a replanificada
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.10 20-09-2019 Se modifica método para validar que si ya existe un registro de parte afectada
     * de igual manera si existe un registro en la tabla INFO_DOCUMENTO_COMUNICACION no cree otro.
     * 
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 2.11 28-11-2019 Se modifica método para agregar las llamadas a proceso HAL.
     *                          Se reestructura el proceso creando una nueva función llamada 
     *                          crearTareaAsignarPlanificacion() en donde se realiza creación de tarea(s) 
     *                          de asignación y el llamado a procesos HAL(notificarSeleccionSugerencia y confirmarSugerencia) 
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.12 23-01-2020 - Se ajusta logica para que pueda generarse tarea con el proceso INSTALACION WIFI ALQUILER DE EQUIPOS.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.13 15-04-2020 - Se implementa llamada al microServicio que actualiza el responsable en el pedido.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.14 28-04-2020 Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                          debido a los cambios realizados por la reestructuración de servicios Small Business
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.15 04-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.16 30-05-2020 - Se agrega logica para validar si es recibido el parametro $arraySimultaneos, con el fin de agrupar ordenes
     *                            de servicio para isntalacion simultanea.  
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 2.17 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.18 09-07-2020 - Se agrega la tarea para el producto que requiera Flujo.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.19 19-09-2020 Se agrega validaciones para flujo de planificación de servicios W+AP con solicitudes de planificación
     *                          o de agregar equipo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.20 12-10-2020 Se agrega parámetro con el proceso ejecutante para obtener modelos dual band como si fuera una activación 
     *                          al realizar una migración
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.21 15-10-2020 Se modifica validación para validar modelos de cpe wifi cuando la tecnología sea TELLION, ya que actualmente
     *                          la tecnología ZTE ya está disponible para Small Bsuiness y Telcohome y en esta no se debe validar modelos de CPE WIFI
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.22 18-11-2020 Se verifica si es cableado estructurado para grabar la fecha de planificación por el estado AsignadoTarea
     * 
     * @author Andrés Montero H. <amontero@telconet.ec>
     * @version 2.23 27-11-2020 Se agrega que retorne el id_detalle y el id_comunicacion de la tarea.
     *                          Se elimina código donde se valida que el service comercial.InfoServicio sea un object
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.23 23-11-2020 Se agrega programación para permitir flujo de traslados de servicios W+AP
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.22 10-03-2021 - Se anexa invocacion para cambiar los estados de productos adicionales con el servicios principal 
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 2.24 12-04-2021 - Actualización: Se establece valor "No tiene" en caso de no tener Ingeniero IPCCL2 Asignado.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.25 12-05-2021 - Se correge bug que creaba tareas erroneas en OT que no eran de solicitudes de planificacion
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.24 31-03-2021 Se agregan los modelos de extender para clientes existentes al coordinar una solicitud de agregar equipo por extender
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.25 26-04-2021 - Se valida si el servicio posee en los detalles de parámetro de la red GPON el
     *                           nombre de proceso para la generación tarea.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.26 05-07-2021 - Se invoca a metodo para realizar planifiacion simultanea de productos manuales
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.27 11-04-2022 Se agregan validaciones para permitir asignación de tareas de productos parametrizados a personal de MD
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 2.28 29-06-2022 - Se incluye el parametro de HAL para los procesos simultaneos de EDB
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 2.29 19-10-2022 - Se agrega parametrizacion de  los productos NetlifeCam.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.9 20-03-2023 - Se agrega validacion por prefijo empresa Ecuanet (EN)  obtener informacion de equipos tecnicos,
     *                           se agrega envio de notificacion SMS por medio de Massend.
     * @author David León <mdleon@telconet.ec>
     * @version 2.31 24-03-2023 - Se agrega validaciones para proyecto TN.
     * 
     */

    public function asignarPlanificacion($arrayParametros)
    {
        $intIdPerEmpRolSesion   = $arrayParametros['intIdPerEmpRolSesion'];
        $strEsHal               = $arrayParametros['strEsHal'];
        $strAtenderAntes        = $arrayParametros['strAtenderAntes'];
        $intIdSugerenciaHal     = $arrayParametros['intIdSugerenciaHal'];
        $objServHistCoordina    = $arrayParametros['objServicioHistorial'];
        $strObservacionCoordina = $arrayParametros['strObservacionServicio'];
        $intIdDetalleExistente  = $arrayParametros['intIdDetalleExistente'];
        $boolEsReplanifHal      = $arrayParametros['boolEsReplanifHal'];
        $strOrigen              = $arrayParametros['strOrigen'];
        $intIdFactibilidad      = $arrayParametros['intIdFactibilidad'];
        $strParametro           = $arrayParametros['strParametro'];
        $strParamResponsables   = $arrayParametros['strParamResponsables'];
        $intIdPerTecnico        = $arrayParametros['intIdPerTecnico'];
        $intIdPersona           = 0;
        $intIdPersonaEmpresaRol = 0;
        $strObservacion         = "";
        $strObservacionServicio = "";
        $arrayParametrosPer     = array();
        $arrayParametrosHist    = array();
        $intPersonaEmpresaRol   = 0;
        $boolGuardoGlobal       = false;
        $intIdEmpresa           = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa      = $arrayParametros['strPrefijoEmpresa'];
        $intIdDepartamento      = $arrayParametros['intIdDepartamento'];
        $strCodEmpresa          = $intIdEmpresa;
        $strObservacionData     = "";
        $strMensajeWs           = "";
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strIpCreacion          = $arrayParametros['strIpCreacion'];
        $arrayIdIntWifiSim      = $arrayParametros['idIntWifiSim'] ? json_decode(stripslashes($arrayParametros['idIntWifiSim']), true) : null;
        $arrayIdIntCouSim       = $arrayParametros['idIntCouSim'] ? json_decode(stripslashes($arrayParametros['idIntCouSim']), true) : null;
        $arraySimultaneos       = $arrayParametros['arraySimultaneos'] ? $arrayParametros['arraySimultaneos'] : null;
        $intTipoEsquema         = null;
        
        $intIdPerEmpRolSesion       = $arrayParametros['intIdPerEmpRolSesion'];
        $strEsHal                   = $arrayParametros['strEsHal'];
        $strAtenderAntes            = $arrayParametros['strAtenderAntes'];
        $intIdSugerenciaHal         = $arrayParametros['intIdSugerenciaHal'];
        $objServHistCoordina        = $arrayParametros['objServicioHistorial'];
        $strObservacionCoordina     = $arrayParametros['strObservacionServicio'];
        $intIdDetalleExistente      = $arrayParametros['intIdDetalleExistente'];
        $boolEsReplanifHal          = $arrayParametros['boolEsReplanifHal'];
        $strOrigen                  = $arrayParametros['strOrigen'];
        $intIdFactibilidad          = $arrayParametros['intIdFactibilidad'];
        $strParametro               = $arrayParametros['strParametro'];
        $strParamResponsables       = $arrayParametros['strParamResponsables'];
        $intIdPerTecnico            = $arrayParametros['intIdPerTecnico'];
        $intIdPersona               = 0;
        $intIdPersonaEmpresaRol     = 0;
        $strObservacion             = "";
        $strObservacionServicio     = "";
        $arrayParametrosPer         = array();
        $arrayParametrosHist        = array();
        $intPersonaEmpresaRol       = 0;
        $boolGuardoGlobal           = false;
        $intIdEmpresa               = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa          = $arrayParametros['strPrefijoEmpresa'];
        $intIdDepartamento          = $arrayParametros['intIdDepartamento'];
        $strCodEmpresa              = $intIdEmpresa;
        $strObservacionData         = "";
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
        $strIpCreacion              = $arrayParametros['strIpCreacion'];
        $arrayIdIntWifiSim          = $arrayParametros['idIntWifiSim'] ? json_decode(stripslashes($arrayParametros['idIntWifiSim']), true) : null;
        $arrayIdIntCouSim           = $arrayParametros['idIntCouSim'] ? json_decode(stripslashes($arrayParametros['idIntCouSim']), true) : null;
        $intTipoEsquema             = null;
        $intIdDepartamentoTarea     = null;
        $strResponsableTrazabilidad = "";
        $strRequiereTrabajo         = 'REQUIERE TRABAJO';
        $boolRequiereFlujo          = false;
        $strTienePersonalizacionOpcionesGridCoordinar = "NO";
        $strProcesoSms              = 'CONDIG-PIN';
        $strEsPlan = 'S';
        $strTipoProceso = $arrayParametros['strTipoProceso'];
        $strMensajeSms = 'Netlife te comparte los datos de tu visita tecnica <numeroTarea> '.
                         'el <fecha> desde las <horaIni> a <horaFin>, codigo:<codigoTrabajo>, '. 
                         'Tecnico: <nombreTecnico>, CI:<cedula>.';
        $arrayParamProducNetCam   = $this->serviceGeneral->paramProductosNetlifeCam();


        $arrayParamConfigSms = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                              ->getOne('CONFIG_SMS_MASSEND',
                                                    'PLANIFICACION',
                                                    '',
                                                    'CONFIG_SMS_MASSEND_'.$strPrefijoEmpresa,
                                                    '',
                                                    '',
                                                    $strProcesoSms,
                                                    '',
                                                    '',
                                                    $intIdEmpresa);
        if(isset($arrayParametros['tienePersonalizacionOpcionesGridCoordinar']) 
            && !empty($arrayParametros['tienePersonalizacionOpcionesGridCoordinar']))
        {
            $strTienePersonalizacionOpcionesGridCoordinar = $arrayParametros['tienePersonalizacionOpcionesGridCoordinar'];
        }

        $intCriterioAfectado        = 0;
        
        $strSolicitudCableadoEstructurado = 'SOLICITUD CABLEADO ESTRUCTURADO';
        
        $strObservacionSolSimultanea    = $arrayParametros['strObservacionSolSimultanea'];
        $strEsGestionSimultanea         = $arrayParametros['strEsGestionSimultanea'];
        $strObservacionAdicional        = "";
        
        if($intIdEmpresa == 18 && $strTienePersonalizacionOpcionesGridCoordinar === "NO")
        {
            $intIdEmpresa = 10;
        }
        $arrayParamRespon = explode("|", $strParamResponsables);
        $objSolicitud     = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdFactibilidad);
        $strTipoSolicitud = strtolower($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());
        
        $intIdServicio    = $objSolicitud->getServicioId()->getId();

        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        $this->emComunicacion->getConnection()->beginTransaction();
        $this->emComercial->getConnection()->beginTransaction();
        try
        {
            if ($boolEsReplanifHal)
            {
                $strTipoProceso = "Programar";
            }

            $arrayParametrosHist["strCodEmpresa"]           = $strCodEmpresa;
            $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
            $arrayParametrosHist["strEstadoActual"]         = "Asignada";
            $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
            $arrayParametrosHist["strOpcion"]               = "Seguimiento";
            $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;

// se agrega validacion del estado del servicio para bloquear operaciones incorrectas
            if($objSolicitud->getServicioId()->getEstado() == "Activo" &&
                    ($strTipoSolicitud != 'solicitud migracion' &&
                     $strTipoSolicitud != 'solicitud agregar equipo' &&
                     $strTipoSolicitud != 'solicitud agregar equipo masivo' &&
                     $strTipoSolicitud != 'solicitud retiro equipo' &&
                     $strTipoSolicitud != 'solicitud cambio equipo por soporte' &&
                     $strTipoSolicitud != 'solicitud cambio equipo por soporte masivo' &&
                     $strTipoSolicitud != 'solicitud de instalacion cableado ethernet' &&
                     $strTipoSolicitud != 'solicitud reubicacion'))
            {
                $arrayRespuesta['mensaje'] = 'El servicio Actualmente se encuentra con estado Activo, no es posible Asignar Responsable.';
                return $arrayRespuesta;
            }
            $boolSigueFlujoPlanificacion = false;
            if(is_object($objSolicitud->getServicioId()) && is_object($objSolicitud->getServicioId()->getProductoId())
                && (
                    $objSolicitud->getServicioId()->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND" ||
                    $objSolicitud->getServicioId()->getProductoId()->getNombreTecnico() === "WIFI_DUAL_BAND" ||
                    ($objSolicitud->getServicioId()->getProductoId()->getNombreTecnico() === "WDB_Y_EDB" 
                        && $objSolicitud->getServicioId()->getEstado() != "Activo")
                   )
                && ($strTipoSolicitud == "solicitud agregar equipo" || $strTipoSolicitud == "solicitud agregar equipo masivo"))
            {
                $boolSigueFlujoPlanificacion = true;
            }
            
            $arrayOrigen = array(
                "local",
                "otro",
                "otro2",
                "MOVIL");
            if(in_array($strOrigen, $arrayOrigen))
            {
                $boolGuardoGlobal = true;

                if(($strOrigen == "otro" || $strOrigen == "otro2") && !$strParametro)
                {
                    $strParametro = $intIdFactibilidad;
                }

                $arrayValor = explode("|", $strParametro);
                foreach($arrayValor as $id):
                    $objDataSolicitada = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($id);
                    if($objDataSolicitada == null || $objSolicitud == null)
                    {
                        $strMensajeError = "No existe la entidad";
                    }
                    else
                    {
                        $intIdPlan       = 0;
                        $objInfoServicio = $objSolicitud->getServicioId();
                        
                        if(is_object($objInfoServicio) && is_object($objInfoServicio->getProductoId()) && $strCodEmpresa == '10' )
                        {
                            //Consultamos si el producto requiere flujo ya que antes no lo tenia
                            $arrayParametrosRequiereFlujo =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne("REQUIERE_FLUJO", 
                                                                                         "TECNICO", 
                                                                                         "", 
                                                                                         "", 
                                                                                         $objInfoServicio->getProductoId()->getDescripcionProducto(), 
                                                                                         "", 
                                                                                         "",
                                                                                         "",
                                                                                         "",
                                                                                         $intIdEmpresa
                                                                                        );
                            if(!is_array($arrayParametrosRequiereFlujo) && empty($arrayParametrosRequiereFlujo))
                            {
                                $boolRequiereFlujo = false;
                            }
                            else
                            {
                                $boolRequiereFlujo = true;
                            }
                        
                            $strProductoFlujo = $objInfoServicio->getProductoId()->getDescripcionProducto();
                        }
                                                
                        //SE VALIDA QUE EXISTA ID_PLAN EN LA TABLA INFO_SERVICIO PARA CONTINUAR CON FLUJO NETLIFECAM
                        if($objInfoServicio->getPlanId())
                        {
                            $intIdPlan     = $objInfoServicio->getPlanId()->getId();
                            $strNombrePlan = $objInfoServicio->getPlanId()->getNombrePlan();
                        }
                        else
                        {
                            $strNombrePlan = '';
                        }
                        
                        $objInfoPuntoServicio  = $objInfoServicio->getPuntoId();
                        if(is_object($objInfoPuntoServicio) && is_object($objInfoPuntoServicio->getPersonaEmpresaRolId()))
                        {
                            $objInfoPersonaCliente = $objInfoPuntoServicio->getPersonaEmpresaRolId()->getPersonaId();
                            if(is_object($objInfoPersonaCliente))
                            {
                                $strNombreCliente      = sprintf("%s", $objInfoPersonaCliente);
                            }
                        }                           
                        
                        $objAdmiProducto = $objInfoServicio->getProductoId();
                        if(is_object($objAdmiProducto))
                        {
                            $strEsPlan            = 'N';
                            $strEsVentaExterna        = 'NO';
                            //verificar si tiene la caracteristica VENTA_EXTERNA
                            $objCaracteristicaExterna = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                           ->findOneBy(array("descripcionCaracteristica" => "VENTA_EXTERNA",
                                                                                             "estado"                    => "Activo"));
                            if(is_object($objCaracteristicaExterna))
                            {
                                $objProCaract =   $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                                    ->findOneBy(array(  "productoId"       => $objAdmiProducto->getId(),
                                                                                        "caracteristicaId" => $objCaracteristicaExterna->getId()));
                                if(is_object($objProCaract))
                                {
                                    $strEsVentaExterna      = 'SI';
                                    $strTarea               = '';
                                    $strParametroTareaDepar = '';

                                    //obtengo los parametros
                                    $objParametro =   $this->emComercial->getRepository('schemaBundle:AdmiParametroCab')
                                                                        ->findOneBy(array("nombreParametro" => 'PARAMETROS NETVOICE',
                                                                                          "estado"          => 'Activo'));
                                    if(is_object($objParametro))
                                    {

                                        $objParametroTarea =  $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->findOneBy(array(  "descripcion" => 'TAREA',
                                                                                                    "parametroId" => $objParametro->getId(),
                                                                                                    "estado"      => 'Activo'));
                                        if(is_object($objParametroTarea))
                                        {
                                            $strTarea = $objParametroTarea->getValor1();
                                        }

                                        $objParametroTareaDepar = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->findOneBy(array(  "descripcion" => 'TAREA_DEPARTAMENTO',
                                                                                                        "parametroId" => $objParametro->getId(),
                                                                                                        "estado"      => 'Activo'));
                                        if(is_object($objParametroTareaDepar))
                                        {
                                            $strParametroTareaDepar = $objParametroTareaDepar->getValor1();
                                        }
                                    }
                                    
                                    $strDatosTelefonia = $objInfoServicio->getObservacion();

                                    //creo la tarea llamando al service de soporte
                                    $arrayParametrosSoporte = array(
                                        'strIdEmpresa'          => $strCodEmpresa,
                                        'strPrefijoEmpresa'     => $strPrefijoEmpresa,
                                        'strNombreTarea'        => $strTarea,
                                        'strObservacion'        => $strDatosTelefonia,
                                        'strNombreDepartamento' => $strParametroTareaDepar,
                                        'strCiudad'             => '',
                                        'strEmpleado'           => '',
                                        'strUsrCreacion'        => $strUsrCreacion,
                                        'strIp'                 => $strIpCreacion,
                                        'strOrigen'             => 'WEB-TN',
                                        'strLogin'              => $objInfoServicio->getPuntoId()->getLogin(),
                                        'intPuntoId'            => $objInfoServicio->getPuntoId()->getId(),
                                        'strNombreCliente'      => $strNombreCliente
                                    );
                                    /* @var $recursosDeRedService \telconet\soporteBundle\Service\SoporteService */

                                    $arrayTarea = $this->serviceSoporte->ingresarTareaInterna($arrayParametrosSoporte);

                                    if($arrayTarea['status'] == "ERROR")
                                    {
                                        $arrayRespuesta['mensaje'] = 'No se creó tarea Netvoice:  ' . $arrayTarea['mensaje'];
                                        return $arrayRespuesta;
                                    }
                                }
                            }

                        }
                    
                        $objInfoServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneByServicioId($objInfoServicio->getId());
                        $strNombreProceso       = "";

                        if($strTipoSolicitud == "solicitud cambio equipo")
                        {
                            $strNombreProceso = "SOLICITAR CAMBIO EQUIPO";
                        }

                        if($strTipoSolicitud == "solicitud retiro equipo")
                        {
                            $strNombreProceso = "SOLICITAR RETIRO EQUIPO";
                        }

                        if($strTipoSolicitud == "solicitud agregar equipo")
                        {
                            $strNombreProceso = "SOLICITUD AGREGAR EQUIPO";
                        }

                        if($strTipoSolicitud == "solicitud agregar equipo masivo")
                        {
                            $strNombreProceso = "SOLICITUD AGREGAR EQUIPO";
                        }

                        if($strTipoSolicitud == "solicitud cambio equipo por soporte")
                        {
                            $strNombreProceso = "SOLICITUD CAMBIO EQUIPO POR SOPORTE";
                        }

                        if($strTipoSolicitud == "solicitud cambio equipo por soporte masivo")
                        {
                            $strNombreProceso = "SOLICITUD CAMBIO EQUIPO POR SOPORTE";
                        }

                        $strSolucion = '';

                        if($strTipoSolicitud == "solicitud reubicacion")
                        {
                            $strNombreProceso = "SOLICITUD REUBICACION";
                        }

                        //Flujo para Data Center
                        if($strPrefijoEmpresa == 'TN' && strpos($objInfoServicio->getProductoId()->getGrupo(), 'DATACENTER') !== false)
                        {
                            $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";

                            $arrayParametrosSolucion                = array();
                            $arrayParametrosSolucion['objServicio'] = $objInfoServicio;
                            $strSolucion                            = $this->serviceGeneral
                                                                           ->getNombreGrupoSolucionServicios($arrayParametrosSolucion);
                        }

                        //obtengo el nombre del proceso del servicio por detalles del parametro de la red GPON
                        if(is_object($objAdmiProducto))
                        {
                            $arrayParDetCamSafeCity = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                       ->getOne('NUEVA_RED_GPON_TN',
                                                                'COMERCIAL',
                                                                '',
                                                                'TAREA DE INSTALACION DEL SERVICIO',
                                                                $objAdmiProducto->getId(),
                                                                '',
                                                                '',
                                                                '',
                                                                '',
                                                                $intIdEmpresa);
                            if( isset($arrayParDetCamSafeCity) && !empty($arrayParDetCamSafeCity)
                                && isset($arrayParDetCamSafeCity['valor2'])&& !empty($arrayParDetCamSafeCity['valor2']) )
                            {
                                $strNombreProceso = $arrayParDetCamSafeCity['valor2'];
                            }
                        }
                        //Se agrega variable para validar si el producto pertenece a NetlifeCam
                        $strNombreTecnico = is_object($objInfoServicio->getProductoId()) ? 
                                            $objInfoServicio->getProductoId()->getNombreTecnico() : null;
                        if(!$strNombreProceso && $objInfoServicioTecnico)
                        {
                            if($objInfoServicioTecnico->getUltimaMillaId() && !$boolRequiereFlujo)
                            {
                                $objTipoMedio = $this->emInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                        ->find($objInfoServicioTecnico->getUltimaMillaId());
                                if($objTipoMedio->getNombreTipoMedio() == "Cobre")
                                {
                                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
                                    if(strrpos($strNombrePlan, "ADSL"))
                                    {
                                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL";
                                    }
                                }
                                else if($objTipoMedio->getNombreTipoMedio() == "Fibra Optica")
                                {
                                    /* Si el producto es NetlifeCam, la tarea sera la siguiente*/
                                    if(in_array($strNombreTecnico, $arrayParamProducNetCam))
                                    {
                                        //OBTENER EL NOMBRE DEL PROCESO
                                        $objNombreProceso =  $this->emComercial
                                                                    ->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('PROYECTO NETLIFECAM',
                                                                            'INFRAESTRUCTURA',
                                                                            '',
                                                                            'PARAMETRIZACION DE NOMBRES TECNICOS DE PRODUCTOS NETLIFE CAM',
                                                                            $strNombreTecnico,
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '18');
                                        $strNombreProceso = $objNombreProceso['valor2'];
                                    } 
                                    else 
                                    {
                                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                                    }
                                }
                                else if($objTipoMedio->getNombreTipoMedio() == "FTTx")
                                {           
                                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO FTTx";
                                }
                                else
                                {
                                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";
                                }
                            }
                            else if (is_object($objInfoServicio->getProductoId()) &&
                                $objInfoServicio->getProductoId()->getDescripcionProducto() == "WIFI Alquiler Equipos")
                            {
                                /*Si el producto es Wifi Alquiler Equipos, la tarea sera la siguiente.*/
                                $strNombreProceso = 'TAREA DE RADIOENLACE - INSTALACION WIFI ALQUILER DE EQUIPOS';
                            }
                            else if (is_object($objInfoServicio->getProductoId()) && $boolRequiereFlujo)
                            {
                                /*Si el producto es Cableado Estructurado, la tarea sera la siguiente.*/
                                $strNombreProceso = $strSolicitudCableadoEstructurado;
                            }
                            else
                            {
                                if($intIdPlan && $strTipoSolicitud == "solicitud planificacion")
                                {  
                                    $arrayResultadoNetlifecam = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                            ->getProductosPlan(array(
                                        "intIdPlan"        => $intIdPlan,
                                        "strNombreTecnico" => "CAMARA IP"));

                                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";                           
                                    if(!empty($arrayResultadoNetlifecam))
                                    {
                                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO NETLIFECAM";
                                    }                           
                                }
                                else
                                {
                                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                                }
                            }
                        }

                        $entityProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($strNombreProceso);
                        if($entityProceso != null)
                        {
                            $strObservacionTecnico = "";
                            if(($strPrefijoEmpresa == "TN" 
                                && ($strTipoSolicitud == "solicitud planificacion" || $strTipoSolicitud == "solicitud migracion")) ||
                               ($strPrefijoEmpresa == "TNP" && is_object($objInfoServicio->getProductoId()) 
                                    && $objInfoServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS" 
                                    && is_object($objInfoServicioTecnico))
                              )
                            {
                                // Envio el objeto de servicio para que me devuelva el esquema seleccionado.
                                $intTipoEsquema = isset($arrayParametros['tipoEsquema']) ?
                                    $arrayParametros['tipoEsquema'] : $this->getTipoEsquema($objInfoServicio);

                                $strNombreCompletoTecnico   = "";
                                $strTelefonosMovil          = "";
                                $strTipoTecnico             = !is_null($intTipoEsquema) && $intTipoEsquema == 1 ? "RADIO" : "IPCCL2";
                                $strObservacionTecnico     .= "<b>Ingeniero $strTipoTecnico Asignado:</b><br>";
                                if($intIdPerTecnico)
                                {
                                    $objPerTecnico = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerTecnico);
                                    if(is_object($objPerTecnico))
                                    {
                                        $arrayParametros["intIdPersonaEmpresaRol"] = $intIdPerTecnico;
                                        $strTelefonosMovil =  $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                                                                ->getNumerosAsignados($arrayParametros);

                                        $objPersonaTecnico = $objPerTecnico->getPersonaId();
                                        if(is_object($objPersonaTecnico))
                                        {
                                            $strNombreCompletoTecnico = sprintf("%s", $objPersonaTecnico);
                                        }
                                    }
                                }
                                $strObservacionTecnico .= !empty($strNombreCompletoTecnico) ? $strNombreCompletoTecnico : 'No Aplica';

                                if ($intIdEmpresa == 10 &&
                                    !empty($strNombreCompletoTecnico) &&
                                    !empty($objPersonaTecnico) &&
                                    !empty($objPerTecnico))
                                {
                                    $arrayIpccl2 = array(
                                        'idPersona'=> $objPerTecnico->getPersonaId()->getId(),
                                        'idPersonaEmpresaRol' => $objPerTecnico->getId(),
                                        'nombres' => $objPersonaTecnico->getNombres(),
                                        'apellidos'=> $objPersonaTecnico->getApellidos(),
                                        'nombreCompleto'=>$strNombreCompletoTecnico
                                    );

                                    /*Registramos el usuario IPCCL2 dentro del servicio para poderlo obtener posteriormente.*/
                                    $this->serviceGeneral->actualizarServicioProductoCaracteristica(
                                        array(
                                            'objServicio' => $objInfoServicio,
                                            'objProducto' => $objInfoServicio->getProductoId(),
                                            'strCaracteristica' => 'IPCCL2_ASIGNADO',
                                            'strValor' => json_encode($arrayIpccl2),
                                            'strUsrCreacion' => $strUsrCreacion
                                        ));
                                }


                                // Envio el objeto de servicio para que me devuelva el esquema seleccionado.
                                $intTipoEsquema = $this->getTipoEsquema($objInfoServicio);

                                // Valido que el tipo de esquema no este vacío.
                                $strObservacionTecnico .= !is_null($intTipoEsquema) ? "<br/><b>Tipo Esquema: </b>" . $intTipoEsquema: '';

                                if ($arrayIdIntWifiSim)
                                {
                                    $intTotalServiciosWifi = count($arrayIdIntWifiSim);

                                    $strObservacionTecnico .= "<br/><b>Total AP's Instalación Simultánea: </b> «".$intTotalServiciosWifi . "»";

                                    foreach ($arrayIdIntWifiSim as $key=>$intIdInternetWifi)
                                    {
                                        $objIntWifi = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->find($intIdInternetWifi);
                                        $strDescFactura = is_object($objIntWifi) ?
                                                          $objIntWifi->getDescripcionPresentaFactura() : null;
                                        $strObservacionTecnico .= !is_null($strDescFactura) ?
                                            "<br/><b>(". ($key+1) .")</b> " . $strDescFactura : '';
                                    }

                                }

                                /*Validamos que este definida esta variable, eso implica que es un simultáneo.*/
                                if (isset($arraySimultaneos) && is_array($arraySimultaneos) && count($arraySimultaneos) >= 1 )
                                {
                                    /*Se contabilizan los servicios simultaneos.*/
                                    $intTotalServiciosSim = count($arraySimultaneos);

                                    /*Agregamos la cantidad de servicios al comentario.*/
                                    $strObservacionTecnico .= "<br/><b>Instalación Simultánea: </b> «"
                                        .$intTotalServiciosSim . "»";

                                    /*Obtenemos el array del parámetro.*/
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

                                    /*Recorremos el arreglo de los servicios simultaneos.*/
                                    foreach ($arraySimultaneos as $key=>$intIdServicioSim)
                                    {
                                        /*Obtenemos el objeto del servicio.*/
                                        $objInfoServicioSim = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->find($intIdServicioSim);

                                        /*Obtenemos el producto del servicio.*/
                                        $objProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                            ->find($objInfoServicioSim->getProductoId());
                                        
                                        /*Validamos que el arreglo no este vacío.*/
                                        if (is_array($objParamsDet) &&
                                            !empty($objParamsDet) &&
                                            is_object($objInfoServicioSim) &&
                                            is_object($objProducto))
                                        {
                                            /*Convertimos en objeto el contenido de valor1.*/
                                            $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);

                                            $arrayParams['strNeedle'] = $objProducto->getDescripcionProducto();
                                            $arrayParams['strKey'] = 'DESCRIPCION_PRODUCTO';
                                            $arrayParams['arrayToSearch'] = $objCaracteristicasServiciosSimultaneos;

                                            /*Buscamos el producto en el objeto.*/
                                            $objCaracteristicasServicioSimultaneo = $this->serviceGeneral->searchByKeyInArray($arrayParams);
                                            
                                        }

                                        /*Validamos si la variable esta definida.*/
                                        if (isset($objCaracteristicasServicioSimultaneo) && !empty($objCaracteristicasServicioSimultaneo))
                                        {
                                            if ($objCaracteristicasServicioSimultaneo['OS_AGRUPADAS'])
                                            {
                                                $strDescFactura = is_object($objInfoServicioSim) ?
                                                    $objInfoServicioSim->getDescripcionPresentaFactura() : null;

                                                $strObservacionTecnico .= !is_null($strDescFactura) ?
                                                    "<br/><b>(". ($key+1) .")</b> " . $strDescFactura : '';
                                            }
                                            else
                                            {
                                                $strDescFactura = is_object($objInfoServicioSim) ?
                                                    $objInfoServicioSim->getDescripcionPresentaFactura() : null;

                                                $strObservacionTecnico .= !is_null($strDescFactura) ?
                                                    "<br/><b>(". ($key+1) .")</b> " . $strDescFactura . ": Revisar su respectiva solicitud.": '';

                                            }
                                        }
                                        
                                        //Consultamos el producto requiere flujo para generar las tareas por departamento
                                        $arrayParametrosRequiereFlujo =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                ->getOne("REQUIERE_FLUJO", 
                                                                                         "TECNICO", 
                                                                                         "", 
                                                                                         "", 
                                                                                         $objProducto->getDescripcionProducto(), 
                                                                                         "", 
                                                                                         "",
                                                                                         "",
                                                                                         "",
                                                                                         $intIdEmpresa
                                                                                        );
                                        if(!is_array($arrayParametrosRequiereFlujo) && empty($arrayParametrosRequiereFlujo))
                                        {
                                            $boolRequiereFlujoSim = false;
                                        }
                                        else
                                        {
                                            $boolRequiereFlujoSim  = true;
                                            $strProductoFlujoSim   = $objProducto->getDescripcionProducto();
                                        }
                        
                                        if ($boolRequiereFlujoSim)
                                        {   
                                            $objProceso         = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                                                                                  ->findOneByNombreProceso($strSolicitudCableadoEstructurado);
                                            if (is_object($objProceso))
                                            {
                                                $objSolicitudSim    = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                           ->findOneBy(array(
                                                                                        'servicioId'       => $intIdServicioSim,
                                                                                        'estado'           => "PrePlanificada"
                                                                            ));
                                                                                               
                                                if (is_object($objSolicitudSim))
                                                {
                                                    $strTipoSolicitudSim  = 
                                                                       strtolower($objSolicitudSim->getTipoSolicitudId()->getDescripcionSolicitud());
                                                    $intIdFactibilidadSim = $objSolicitudSim->getId();
                                                }
                                            }
                                            if($arrayParametros['boolProyecto'])
                                            {
                                                $objTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                             ->findTareasActivasByProcesoTarea(array(
                                                                                    'procesoId'       => $objProceso->getId(),
                                                                                    'idTarea'         => $arrayParametros['tarea_id']
                                                                        ));
                                                                           
                                            }
                                            else
                                            {
                                                $objTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                                             ->findTareasActivasByProceso($objProceso->getId());
                                            }
                                            
                                            
                                            if($objProceso != null && ($objTareas != null && count($objTareas) > 0))
                                            {
                                                    $boolGuardo = true;

                                                    foreach($objTareas as $strKey => $objTarea)
                                                    {
                                                        $strDescripcionTarea    = $objTarea->getDescripcionTarea();
                                                        
                                                        //Busco el id del departamento
                                                        $objAdmiDepartamento    = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                                                   ->findOneBy(array('nombreDepartamento'  => $strDescripcionTarea, 
                                                                                                     'estado'              => 'Activo', 
                                                                                                     'empresaCod'          => $strCodEmpresa ));
                                                        if(is_object($objAdmiDepartamento) && $objAdmiDepartamento->getId() != null)
                                                        {
                                                            //Id del departamento
                                                            $intIdDepartamentoTarea  = $objAdmiDepartamento->getId();
                                                                $arrayParamProducto["servicioId"]       = $intIdServicioSim;
                                                                $arrayParamProducto["producto"]         = $strProductoFlujoSim;
                                                                $arrayParamProducto["estado"]           = 'Activo';
                                                                $arrayParamProducto["empresa"]          = $strCodEmpresa;
                                                                $arrayParamProducto["caracteristica"]   = $strRequiereTrabajo;
                                                                $arrayParamProducto["idDepartamento"]   = $intIdDepartamentoTarea;
                                                                                           
                                                                $arrayProductoResult = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")
                                                                                            ->getProductosRequiereTrabajo($arrayParamProducto);
                                                                if($arrayProductoResult)
                                                                {
                                                                 $arrayParametrosTareaSim['intIdPerEmpRolSesion']       = $intIdPerEmpRolSesion;
                                                                 $arrayParametrosTareaSim['intIdFactibilidad']          = $intIdFactibilidadSim;
                                                                 $arrayParametrosTareaSim['intIdPersona']               = $intIdPersona;
                                                                 $arrayParametrosTareaSim['intIdPersonaEmpresaRol']     = $intIdPersonaEmpresaRol;
                                                                 $arrayParametrosTareaSim['strObservacion']             = $strObservacion;
                                                                 $arrayParametrosTareaSim['strObservacionServicio']     = $strObservacionServicio;
                                                                 $arrayParametrosTareaSim['arrayParametrosPer']         = $arrayParametrosPer;
                                                                 $arrayParametrosTareaSim['arrayParametrosHist']        = $arrayParametrosHist;
                                                                 $arrayParametrosTareaSim['intPersonaEmpresaRol']       = $intPersonaEmpresaRol;
                                                                 $arrayParametrosTareaSim['intIdEmpresa']               = $intIdEmpresa;
                                                                 $arrayParametrosTareaSim['strPrefijoEmpresa']          = $strPrefijoEmpresa;
                                                                 $arrayParametrosTareaSim['strCodEmpresa']              = $strCodEmpresa;
                                                                 $arrayParametrosTareaSim['strUsrCreacion']             = $strUsrCreacion;
                                                                 $arrayParametrosTareaSim['strIpCreacion']              = $strIpCreacion;
                                                                 $arrayParametrosTareaSim['strResponsableTrazabilidad'] = $strResponsableTrazabilidad;
                                                                 $arrayParametrosTareaSim['id']                         = $intIdFactibilidadSim;
                                                                 $arrayParametrosTareaSim['entitytarea']                = $objTarea;
                                                                 $arrayParametrosTareaSim['strObservacionTecnico']      = $strObservacionTecnico;
                                                                 $arrayParametrosTareaSim['strSolucion']                = $strSolucion;
                                                                 $arrayParametrosTareaSim['strDatosTelefonia']          = $strDatosTelefonia;
                                                                 $arrayParametrosTareaSim['objInfoServicio']            = $objInfoServicioSim;
                                                                 $arrayParametrosTareaSim['strTipoSolicitud']           = $strTipoSolicitudSim;
                                                                 $arrayParametrosTareaSim['boolGuardo']                 = $boolGuardo;
                                                                 $arrayParametrosTareaSim['arrayParamRespon']           = $arrayParamRespon;
                                                                 $arrayParametrosTareaSim['objSolicitud']               = $objSolicitudSim;
                                                                 $arrayParametrosTareaSim['strEsHal']                   = $strEsHal;
                                                                 $arrayParametrosTareaSim['strAtenderAntes']            = $strAtenderAntes;
                                                                 $arrayParametrosTareaSim['intIdSugerenciaHal']         = $intIdSugerenciaHal;
                                                                 $arrayParametrosTareaSim['boolEsReplanifHal']          = $boolEsReplanifHal;
                                                                 $arrayParametrosTareaSim['intIdDetalleExistente']      = $intIdDetalleExistente;
                                                                 $arrayParametrosTareaSim['boolRequiereFlujoSim']       = $boolRequiereFlujoSim;
                                                                 $arrayParametrosTareaSim['idFlujoFactibilidad']        = $intIdFactibilidad;
                                                                 $arrayParametrosTareaSim['strEsPlan']                  = $strEsPlan;
                                                                 $arrayParametrosTareaSim['strTipoProceso']             = $strTipoProceso; 
                                                                 $arrayRespuestaCreaTareaAsig  = 
                                                                                      $this->crearTareaAsignarPlanificacion($arrayParametrosTareaSim);

                                                                 $strMensajeWs                 = $arrayRespuestaCreaTareaAsig['strMensajeWs'];
                                                                 $boolGuardo                   = $arrayRespuestaCreaTareaAsig['boolGuardo'];
                                                                 $objEntityDetalle             = $arrayRespuestaCreaTareaAsig['objInfoDetalle'];
                                                                 $objInfoDocumentoComunicacion = 
                                                                                      $arrayRespuestaCreaTareaAsig['objInfoDocumentoComunicacion'];
                                                                 $objInfoComunicacion          = 
                                                                                      $arrayRespuestaCreaTareaAsig['objInfoComunicacion'];
                                                                 $objInfoDocumento             = $arrayRespuestaCreaTareaAsig['objInfoDocumento'];
                                                                 $arrayParametrosHist          = 
                                                                                      $arrayRespuestaCreaTareaAsig['arrayParametrosHist'];
                                                                 $strObservacionServicio       = 
                                                                                      $arrayRespuestaCreaTareaAsig['observacionServicio'];
                                                                 $strMensajeCreaTareaAsig      = $arrayRespuestaCreaTareaAsig['mensaje'];
                                                                 $strFechaHal                  = $arrayRespuestaCreaTareaAsig['fechaHal'];
                                                                 $strHoraIniHal                = $arrayRespuestaCreaTareaAsig['horaIniHal'];
                                                                 $strHoraFinHal                = $arrayRespuestaCreaTareaAsig['horaFinHal'];
                                                                 
                                                                 $objLastDetalleSolhist = $this->emComercial
                                                                                               ->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                                               ->findOneDetalleSolicitudHistorial($id, 'Planificada');
                                                
                                                                //Grabar en la infoCriterioAfectado y en la InfoParteAfectada 
                                                                //cuando se requiere trabajo
                                                                $strOpcion = 'Cliente: ' . $objInfoServicioSim->getPuntoId()->getNombrePunto() 
                                                                                         . ' | OPCION: Punto Cliente';
                                                
                                                                $objInfoCriterioA = $this->emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                                                         ->getInfoParteAfectadaExistente($objEntityDetalle->getId());

                                                                $intIdCA = 1;
                                                                if($objInfoCriterioA == null)
                                                                {

                                                                    $objInfoCriterioAfectado = new InfoCriterioAfectado();
                                                                    
                                                                    $objInfoCriterioAfectado->setId($intIdCA);
                                                                    $objInfoCriterioAfectado->setDetalleId($objEntityDetalle);
                                                                    $objInfoCriterioAfectado->setCriterio("Clientes");
                                                                    $objInfoCriterioAfectado->setOpcion($strOpcion);
                                                                    $objInfoCriterioAfectado->setUsrCreacion($strUsrCreacion);
                                                                    $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                                                                    $objInfoCriterioAfectado->setIpCreacion($strIpCreacion);
    
                                                                    $this->emSoporte->persist($objInfoCriterioAfectado);
                                                                    $this->emSoporte->flush();
                                                                        
                                                                    $entityInfoParteAfectada = new InfoParteAfectada();
    
                                                                    $entityInfoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado
                                                                                            ->getId());
                                                                    $entityInfoParteAfectada->setDetalleId($objEntityDetalle->getId());
                                                                    $entityInfoParteAfectada->setAfectadoId($objInfoServicioSim
                                                                                            ->getPuntoId()->getId());
                                                                    $entityInfoParteAfectada->setTipoAfectado("Cliente");
                                                                    $entityInfoParteAfectada->setAfectadoNombre($objInfoServicioSim->getPuntoId()
                                                                                            ->getLogin());
                                                                    $entityInfoParteAfectada->setAfectadoDescripcion($objInfoServicioSim->getPuntoId()
                                                                                            ->getNombrePunto());
                                                                    if($objLastDetalleSolhist)
                                                                    {
                                                                        $entityInfoParteAfectada->setFeIniIncidencia($objLastDetalleSolhist
                                                                                                ->getFeCreacion());
                                                                    }
                                                                    else
                                                                    {
                                                                        $entityInfoParteAfectada->setFeIniIncidencia($objSolicitudSim
                                                                                                ->getFeCreacion());
                                                                    }
                                                                    $entityInfoParteAfectada->setUsrCreacion($strUsrCreacion);
                                                                    $entityInfoParteAfectada->setFeCreacion(new \DateTime('now'));
                                                                    $entityInfoParteAfectada->setIpCreacion($strIpCreacion);
    
                                                                    $this->emSoporte->persist($entityInfoParteAfectada);
                                                                    $this->emSoporte->flush();
                                                    
                                                                    $intCriterioAfectado = 1;
                                                                 }
                                                                }
                                                        }
                                                        
                                                    }
                                                    
                                                    if (!$boolGuardo)
                                                    {
                                                        throw new \Exception($strMensajeCreaTareaAsig);
                                                    }
                                                    
                                                    $objSolicitudSim->setEstado("Asignada");
                                                    $this->emComercial->persist($objSolicitudSim);
                                                    $this->emComercial->flush();
                                            }
                                            
                                        }
                                        //FIN DE CONSULTA
                                    }
                                }
                                
                                if ($arrayIdIntCouSim)
                                {
                                    $intTotalServiciosCou = count($arrayIdIntCouSim);

                                    $strObservacionTecnico .= "<br/><b>COU LINEAS TELEFONIA FIJA Instalación Simultánea: </b> «"
                                                           .$intTotalServiciosCou . "»";

                                    foreach ($arrayIdIntCouSim as $key=>$intIdCouLineas)
                                    {
                                        $objIntCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->find($intIdCouLineas);
                                        $strDescFactura = is_object($objIntCou) ?
                                                          $objIntCou->getDescripcionPresentaFactura() : null;
                                        $strObservacionTecnico .= !is_null($strDescFactura) ?
                                            "<br/><b>(". ($key+1) .")</b> " . $strDescFactura : '';
                                    }

                                }

                                if (
                                    is_object($objInfoServicio->getProductoId()) &&
                                    $objInfoServicio->getProductoId()->getDescripcionProducto() == "WIFI Alquiler Equipos")
                                {
                                    $strObservacionTecnico .= "<br/><b>Descripción para Instalación Wifi Alquiler: </b>";

                                    $strDescFactura = is_object($objInfoServicio) ?
                                        $objInfoServicio->getDescripcionPresentaFactura() : null;

                                    $strObservacionTecnico .= !is_null($strDescFactura) ?
                                        "<br /> &#10140; " . $strDescFactura  : '';

                                }

                                if($strTelefonosMovil != "")
                                {
                                    $strObservacionTecnico .= "<br/><b>Teléfonos:</b><br>" . $strTelefonosMovil;
                                }
                                
                                if(is_object($objInfoServicio->getProductoId()) 
                                    && $objInfoServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                                    && is_object($objInfoServicioTecnico))
                                {
                                    $objElementoOlt         = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                                      ->find($objInfoServicioTecnico->getElementoId());
                                    $strMarcaOlt            = $objElementoOlt->getModeloElementoId()->getMarcaElementoId()->getNombreMarcaElemento();
                                    if($strMarcaOlt === "TELLION")
                                    {
                                        $boolValidaModeloCpeWifi    = false;
                                        $arrayParamsInfoProds       = array(
                                                                            "strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                                            "strCodEmpresa"                 => $strCodEmpresa,
                                                                            "intIdProductoInternet"         => 
                                                                            $objInfoServicio->getProductoId()->getId());
                                        $arrayInfoMapeoProds        = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                        ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                                        if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                                        {
                                            $strDescripcionProdPref = $arrayInfoMapeoProds[0]["strDescripcionProdInternet"];
                                            foreach($arrayInfoMapeoProds as $arrayInfoProd)
                                            {
                                                $intIdProductoIp            = $arrayInfoProd["intIdProdIp"];
                                                $objProdIPSB                = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                                                ->find($intIdProductoIp);
                                                $arrayServiciosPuntoIPSB    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                                                ->findBy(array( "puntoId"       => 
                                                                                                                $objInfoServicio->getPuntoId(),
                                                                                                                "productoId"    => $objProdIPSB,
                                                                                                                "estado"        => 
                                                                                                                array("PreAsignacionInfoTecnica")
                                                                                                        ));
                                                if(isset($arrayServiciosPuntoIPSB) && !empty($arrayServiciosPuntoIPSB))
                                                {
                                                    $boolValidaModeloCpeWifi = true;
                                                }
                                            }
                                        }
                                        else
                                        {
                                            throw new \Exception("No se ha podido obtener el correcto mapeo de servicios Small Business con su Ip");
                                        }
                                        
                                        if($boolValidaModeloCpeWifi)
                                        {
                                            $strModelosPermitidos           = "";
                                            $arrayModelosCpeWifiPermitidos  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                              ->get('MODELOS_CPE_WIFI_ACTIVACION_SB_TELLION', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    "CON_IP", 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    '', 
                                                                                                    $strCodEmpresa,
                                                                                                    'valor2');
                                            foreach($arrayModelosCpeWifiPermitidos as $arrayModeloCpeWifiPermitido)
                                            {
                                                if(empty($strModelosPermitidos))
                                                {
                                                    $strModelosPermitidos = $arrayModeloCpeWifiPermitido["valor2"];
                                                }
                                                else
                                                {
                                                    $strModelosPermitidos = $strModelosPermitidos.", ".$arrayModeloCpeWifiPermitido["valor2"];
                                                }
                                            }
                                            $strObservacionTecnico .= "<div style ='color:red'>"
                                                                      ."Servicio ".$strDescripcionProdPref." con ips<br>"
                                                                      ."adicionales, se requiere la instalación<br>"
                                                                      ."con cualquiera de los siguientes<br>"
                                                                      ."modelos de CPE WIFI:<br>"
                                                                      .$strModelosPermitidos."</div>";
                                        }
                                    }
                                }
                            }
                            else if($strPrefijoEmpresa == "TN" && $strTipoSolicitud == "solicitud reubicacion")
                            {
                                $strObservacionTecnico = $objSolicitud->getObservacion();
                            }
                            else if($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN")
                            {
                                $arrayRespuestaInfoEquiposTecnico   = $this->serviceGeneral
                                                                           ->obtieneRespuestaInfoEquiposTecnico(
                                                                                array(  
                                                                                        "objSolicitud"          => $objSolicitud,
                                                                                        "objServicio"           => $objInfoServicio,
                                                                                        "strTipoSolicitud"      => $strTipoSolicitud,
                                                                                        "objServicioTecnico"    => $objInfoServicioTecnico,
                                                                                        "strCodEmpresa"         => $strCodEmpresa));
                                $strStatusInfoEquiposTecnico        = $arrayRespuestaInfoEquiposTecnico["status"];
                                $strMensajeInfoEquiposTecnico       = $arrayRespuestaInfoEquiposTecnico["mensaje"];
                                if($strStatusInfoEquiposTecnico === "ERROR")
                                {
                                    throw new \Exception($strMensajeInfoEquiposTecnico);
                                }
                                $strObservacionTecnico      = $arrayRespuestaInfoEquiposTecnico["infoEquiposTecnicoTarea"];
                                $strObservacionAdicional    = $arrayRespuestaInfoEquiposTecnico["infoObservacionAdicional"];
                            }
                            else
                            {
                                $strObservacionTecnico = "";
                            }
                            if($arrayParametros['boolProyecto'])
                            {
                                $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                                             ->findTareasActivasByProcesoTarea(array(
                                                                        'procesoId'       => $entityProceso->getId(),
                                                                        'idTarea'         => $arrayParametros['tarea_id']
                                                            ));
                            }
                            else
                            {
                                $entityTareas = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                    ->findTareasActivasByProceso($entityProceso->getId());
                            }
                            if($entityTareas != null && count($entityTareas) > 0)
                            {
                                $boolGuardo = true;

                                foreach($entityTareas as $strKey => $entityTarea)
                                {
                                    //Consultamos si el producto requiere flujo
                                    if ($boolRequiereFlujo)
                                    {
                                        $strDescripcionTarea    = $entityTarea->getDescripcionTarea();
                                        
                                        //Busco el id del departamento
                                        $objAdmiDepartamento    = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                                                       ->findOneBy(array('nombreDepartamento'  => $strDescripcionTarea, 
                                                                                         'estado'              => 'Activo', 
                                                                                         'empresaCod'          => $strCodEmpresa ));
                                        if(is_object($objAdmiDepartamento))
                                        {
                                            //Id del departamento
                                            $intIdDepartamentoTarea  = $objAdmiDepartamento->getId();
                                        }
                                        
                                        //Consultamos si existen las caracteristicas para el deparatamento seleccionado
                                        if ($intIdDepartamentoTarea != null)
                                        {
                                            $arrayParamProducto["servicioId"]       = $intIdServicio;
                                            $arrayParamProducto["producto"]         = $strProductoFlujo;
                                            $arrayParamProducto["estado"]           = 'Activo';
                                            $arrayParamProducto["empresa"]          = $strCodEmpresa;
                                            $arrayParamProducto["caracteristica"]   = $strRequiereTrabajo;
                                            $arrayParamProducto["idDepartamento"]   = $intIdDepartamentoTarea;
                                                                                           
                                            $arrayProductoResult     = $this->emSoporte->getRepository("schemaBundle:AdmiTarea")
                                                                                       ->getProductosRequiereTrabajo($arrayParamProducto);
                                            if($arrayProductoResult)
                                            {
                                                $arrayParametrosTarea['intIdPerEmpRolSesion']       = $intIdPerEmpRolSesion;
                                                $arrayParametrosTarea['intIdFactibilidad']          = $intIdFactibilidad;
                                                $arrayParametrosTarea['intIdPersona']               = $intIdPersona;
                                                $arrayParametrosTarea['intIdPersonaEmpresaRol']     = $intIdPersonaEmpresaRol;
                                                $arrayParametrosTarea['strObservacion']             = $strObservacion;
                                                $arrayParametrosTarea['strObservacionServicio']     = $strObservacionServicio;
                                                $arrayParametrosTarea['arrayParametrosPer']         = $arrayParametrosPer;
                                                $arrayParametrosTarea['arrayParametrosHist']        = $arrayParametrosHist;
                                                $arrayParametrosTarea['intPersonaEmpresaRol']       = $intPersonaEmpresaRol;
                                                $arrayParametrosTarea['intIdEmpresa']               = $intIdEmpresa;
                                                $arrayParametrosTarea['strPrefijoEmpresa']          = $strPrefijoEmpresa;
                                                $arrayParametrosTarea['strCodEmpresa']              = $strCodEmpresa;
                                                $arrayParametrosTarea['strUsrCreacion']             = $strUsrCreacion;
                                                $arrayParametrosTarea['strIpCreacion']              = $strIpCreacion;
                                                $arrayParametrosTarea['strResponsableTrazabilidad'] = $strResponsableTrazabilidad;
                                                $arrayParametrosTarea['id']                         = $id;
                                                $arrayParametrosTarea['entitytarea']                = $entityTarea;
                                                $arrayParametrosTarea['strObservacionTecnico']      = $strObservacionTecnico;
                                                $arrayParametrosTarea['strSolucion']                = $strSolucion;
                                                $arrayParametrosTarea['strDatosTelefonia']          = $strDatosTelefonia;
                                                $arrayParametrosTarea['objInfoServicio']            = $objInfoServicio;
                                                $arrayParametrosTarea['strTipoSolicitud']           = $strTipoSolicitud;
                                                $arrayParametrosTarea['boolGuardo']                 = $boolGuardo;
                                                $arrayParametrosTarea['arrayParamRespon']           = $arrayParamRespon;
                                                $arrayParametrosTarea['objSolicitud']               = $objSolicitud;
                                                $arrayParametrosTarea['strEsHal']                   = $strEsHal;
                                                $arrayParametrosTarea['strAtenderAntes']            = $strAtenderAntes;
                                                $arrayParametrosTarea['intIdSugerenciaHal']         = $intIdSugerenciaHal;
                                                $arrayParametrosTarea['boolEsReplanifHal']          = $boolEsReplanifHal;
                                                $arrayParametrosTarea['intIdDetalleExistente']      = $intIdDetalleExistente;
                                                $arrayParametrosTarea['strEsPlan']                  = $strEsPlan;
                                                $arrayParametrosTarea['strTipoProceso']             = $strTipoProceso;
                                                $arrayRespuestaCreaTareaAsig  = $this->crearTareaAsignarPlanificacion($arrayParametrosTarea);

                                                $strMensajeWs                 = $arrayRespuestaCreaTareaAsig['strMensajeWs'];
                                                $boolGuardo                   = $arrayRespuestaCreaTareaAsig['boolGuardo'];
                                                $objEntityDetalle             = $arrayRespuestaCreaTareaAsig['objInfoDetalle'];
                                                $objInfoDocumentoComunicacion = $arrayRespuestaCreaTareaAsig['objInfoDocumentoComunicacion'];
                                                $objInfoComunicacion          = $arrayRespuestaCreaTareaAsig['objInfoComunicacion'];
                                                $objInfoDocumento             = $arrayRespuestaCreaTareaAsig['objInfoDocumento'];
                                                $arrayParametrosHist          = $arrayRespuestaCreaTareaAsig['arrayParametrosHist'];
                                                $strObservacionServicio       = $arrayRespuestaCreaTareaAsig['observacionServicio'];
                                                $strMensajeCreaTareaAsig      = $arrayRespuestaCreaTareaAsig['mensaje'];
                                                $strFechaHal                  = $arrayRespuestaCreaTareaAsig['fechaHal'];
                                                $strHoraIniHal                = $arrayRespuestaCreaTareaAsig['horaIniHal'];
                                                $strHoraFinHal                = $arrayRespuestaCreaTareaAsig['horaFinHal'];
                                                
                                                $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                               ->findOneDetalleSolicitudHistorial($id, 'Planificada');
                                                
                                                //Grabar en la infoCriterioAfectado y en la InfoParteAfectada cuando se requiere trabajo
                                                $strOpcion = 'Cliente: ' . $objInfoServicio->getPuntoId()->getNombrePunto() 
                                                                         . ' | OPCION: Punto Cliente';
                                                
                                                $objInfoCriterioA = $this->emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                                         ->getInfoParteAfectadaExistente($objEntityDetalle->getId());

                                                $intIdCA = 1;
                                                if($objInfoCriterioA == null)
                                                {
                                                    $objInfoCriterioAfectado = new InfoCriterioAfectado();

                                                    $objInfoCriterioAfectado->setId($intIdCA);
                                                    $objInfoCriterioAfectado->setDetalleId($objEntityDetalle);
                                                    $objInfoCriterioAfectado->setCriterio("Clientes");
                                                    $objInfoCriterioAfectado->setOpcion($strOpcion);
                                                    $objInfoCriterioAfectado->setUsrCreacion($strUsrCreacion);
                                                    $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                                                    $objInfoCriterioAfectado->setIpCreacion($strIpCreacion);
    
                                                    $this->emSoporte->persist($objInfoCriterioAfectado);
                                                    $this->emSoporte->flush();
    
                                                    $entityInfoParteAfectada = new InfoParteAfectada();
    
                                                    $entityInfoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
                                                    $entityInfoParteAfectada->setDetalleId($objEntityDetalle->getId());
                                                    $entityInfoParteAfectada->setAfectadoId($objInfoServicio->getPuntoId()->getId());
                                                    $entityInfoParteAfectada->setTipoAfectado("Cliente");
                                                    $entityInfoParteAfectada->setAfectadoNombre($objInfoServicio->getPuntoId()->getLogin());
                                                    $entityInfoParteAfectada->setAfectadoDescripcion($objInfoServicio->getPuntoId()
                                                                            ->getNombrePunto());
                                                    if($objLastDetalleSolhist)
                                                    {
                                                        $entityInfoParteAfectada->setFeIniIncidencia($objLastDetalleSolhist->getFeCreacion());
                                                    }
                                                    else
                                                    {
                                                        $entityInfoParteAfectada->setFeIniIncidencia($objSolicitud->getFeCreacion());
                                                    }
                                                    $entityInfoParteAfectada->setUsrCreacion($strUsrCreacion);
                                                    $entityInfoParteAfectada->setFeCreacion(new \DateTime('now'));
                                                    $entityInfoParteAfectada->setIpCreacion($strIpCreacion);
    
                                                    $this->emSoporte->persist($entityInfoParteAfectada);
                                                    $this->emSoporte->flush();
                                                    
                                                    $intCriterioAfectado = 1;
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        //###########################################
                                        //Crear Tarea y asignar a responsables
                                        //###########################################
                                        $arrayParametrosTarea['intIdPerEmpRolSesion']       = $intIdPerEmpRolSesion;
                                        $arrayParametrosTarea['intIdFactibilidad']          = $intIdFactibilidad;
                                        $arrayParametrosTarea['intIdPersona']               = $intIdPersona;
                                        $arrayParametrosTarea['intIdPersonaEmpresaRol']     = $intIdPersonaEmpresaRol;
                                        $arrayParametrosTarea['strObservacion']             = $strObservacion;
                                        $arrayParametrosTarea['strObservacionServicio']     = $strObservacionServicio;
                                        $arrayParametrosTarea['arrayParametrosPer']         = $arrayParametrosPer;
                                        $arrayParametrosTarea['arrayParametrosHist']        = $arrayParametrosHist;
                                        $arrayParametrosTarea['intPersonaEmpresaRol']       = $intPersonaEmpresaRol;
                                        $arrayParametrosTarea['intIdEmpresa']               = $intIdEmpresa;
                                        $arrayParametrosTarea['strPrefijoEmpresa']          = $strPrefijoEmpresa;
                                        $arrayParametrosTarea['strCodEmpresa']              = $strCodEmpresa;
                                        $arrayParametrosTarea['strUsrCreacion']             = $strUsrCreacion;
                                        $arrayParametrosTarea['strIpCreacion']              = $strIpCreacion;
                                        $arrayParametrosTarea['strResponsableTrazabilidad'] = $strResponsableTrazabilidad;
                                        $arrayParametrosTarea['id']                         = $id;
                                        $arrayParametrosTarea['entitytarea']                = $entityTarea;
                                        $arrayParametrosTarea['strObservacionTecnico']      = $strObservacionTecnico;
                                        $arrayParametrosTarea['strSolucion']                = $strSolucion;
                                        $arrayParametrosTarea['strDatosTelefonia']          = $strDatosTelefonia;
                                        $arrayParametrosTarea['objInfoServicio']            = $objInfoServicio;
                                        $arrayParametrosTarea['strTipoSolicitud']           = $strTipoSolicitud;
                                        $arrayParametrosTarea['boolGuardo']                 = $boolGuardo;
                                        $arrayParametrosTarea['arrayParamRespon']           = $arrayParamRespon;
                                        $arrayParametrosTarea['objSolicitud']               = $objSolicitud;
                                        $arrayParametrosTarea['strEsHal']                   = $strEsHal;
                                        $arrayParametrosTarea['strAtenderAntes']            = $strAtenderAntes;
                                        $arrayParametrosTarea['intIdSugerenciaHal']         = $intIdSugerenciaHal;
                                        $arrayParametrosTarea['boolEsReplanifHal']          = $boolEsReplanifHal;
                                        $arrayParametrosTarea['intIdDetalleExistente']      = $intIdDetalleExistente;
                                        $arrayParametrosTarea['strEsGestionSimultanea']     = $strEsGestionSimultanea;
                                        $arrayParametrosTarea['strEsPlan']                  = $strEsPlan;
                                        $arrayParametrosTarea['strTipoProceso']             = $strTipoProceso;
                                        $arrayRespuestaCreaTareaAsig  = $this->crearTareaAsignarPlanificacion($arrayParametrosTarea);
                                        $strMensajeWs                 = $arrayRespuestaCreaTareaAsig['strMensajeWs'];
                                        $boolGuardo                   = $arrayRespuestaCreaTareaAsig['boolGuardo'];
                                        $objEntityDetalle             = $arrayRespuestaCreaTareaAsig['objInfoDetalle'];
                                        $objInfoDocumentoComunicacion = $arrayRespuestaCreaTareaAsig['objInfoDocumentoComunicacion'];
                                        $objInfoComunicacion          = $arrayRespuestaCreaTareaAsig['objInfoComunicacion'];
                                        $objInfoDocumento             = $arrayRespuestaCreaTareaAsig['objInfoDocumento'];
                                        $arrayParametrosHist          = $arrayRespuestaCreaTareaAsig['arrayParametrosHist'];
                                        $strObservacionServicio       = $arrayRespuestaCreaTareaAsig['observacionServicio'];
                                        $strMensajeCreaTareaAsig      = $arrayRespuestaCreaTareaAsig['mensaje'];
                                        $strFechaHal                  = $arrayRespuestaCreaTareaAsig['fechaHal'];
                                        $strHoraIniHal                = $arrayRespuestaCreaTareaAsig['horaIniHal'];
                                        $strHoraFinHal                = $arrayRespuestaCreaTareaAsig['horaFinHal'];
                                        $arrayParametrosTarea['arrayRespuestaAsignaHal'] = $arrayRespuestaCreaTareaAsig["arrayRespuestaAsignaHal"];
                                    }
                                }

                                if (!$boolGuardo)
                                {
                                    throw new \Exception($strMensajeCreaTareaAsig);
                                }
                                //agregar lo de adjuntar los documentos de crm a las tareas
                                if($arrayParametros['boolProyecto'])
                                {
                                    $objInfoServicioProdCaract = $this->serviceGeneral->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                        'ID_PROPUESTA',
                                                                                                         $objInfoServicio->getProductoId());
                                    $arrayParametrosTarea      = array("strIdPropuesta"           => $objInfoServicioProdCaract->getValor(),
                                                                  );
                                    $arrayParametrosWSCrm = array("arrayParametrosCRM"     => $arrayParametrosTarea,
                                                                  "strOp"                  => 'consultaDocumento',
                                                                  "strFuncion"             => 'procesar');
                                    $arrayRespuestaWSCrm  = $this->serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                                    if(!empty($arrayRespuestaWSCrm["error"]) && isset($arrayRespuestaWSCrm["error"]))
                                    {
                                        throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrm["error"]);
                                    }
                                    $arrayResultados = json_decode(json_encode($arrayRespuestaWSCrm['resultado']), true);
                                    //sacamos datos de la tareas
                                    $arrayParametrosTarea      = array("strIdPropuesta"           => $objInfoServicioProdCaract->getValor(),
                                                                  );
                                    $arrayParametrosWSCrm  = array("arrayParametrosCRM"     => $arrayParametrosTarea,
                                                                    "strOp"                  => 'consultaTarea',
                                                                    "strFuncion"             => 'procesar');
                                    $arrayRespuestaWSCrmT  = $this->serviceTelcoCrm->getRequestCRM($arrayParametrosWSCrm);
                                    if(!empty($arrayRespuestaWSCrmT["error"]) && isset($arrayRespuestaWSCrmT["error"]))
                                    {
                                        throw new \Exception('Error al Obtener el documento en TelcoCrm: '.$arrayRespuestaWSCrmT["error"]);
                                    }
                                    $arrayRegistrosTarea = json_decode(json_encode($arrayRespuestaWSCrmT['resultado']), true);   
                                    if ($arrayRegistrosTarea && $arrayRegistrosTarea != 'not_found')
                                    {
                                        foreach ($arrayRegistrosTarea as $arrayData)
                                        {
                                            $strTareaId                                 = $arrayData['idTarea'];
                                            if($strTareaId != null)
                                            {
                                                $objTareaDetalle = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                                ->find($strTareaId);
                                            }
                                            if(is_object($objTareaDetalle))
                                            {
                                                $arrayParametrosDoc                         = array();
                                                $arrayParametrosDoc["intIdDetalle"]         = $objTareaDetalle->getDetalleId();
                                                $strPathTelcos                              = $this->container->getParameter('path_telcos');
                                                $arrayParametrosDoc["strPathTelcos"]        = $strPathTelcos."telcos/web";

                                                $objJson = $this->emComunicacion->getRepository('schemaBundle:InfoCaso')
                                                                                        ->getJsonDocumentosCaso($arrayParametrosDoc,
                                                                                                $this->emInfraestructura,
                                                                                                $strUsrCreacion
                                                                                            );
                                                $arrayListadoD      = json_decode($objJson, true);
                                                foreach ($arrayListadoD['encontrados'] as $arrayListado)
                                                {
                                                    $intCantidad = $intCantidad+1;
                                                    $arrayDoc = array(
                                                                            'strNombreDocumento'       => $arrayListado["ubicacionLogica"],
                                                                            'strFechaCreacion'         => ($arrayListado["feCreacion"]),
                                                                            'strPathFile'              => $arrayListado["linkVerDocumento"]);

                                                    array_push($arrayResultados,$arrayDoc);
                                                }
                                            }
                                        }
                                    }
                                    foreach($arrayResultados as $arrayResultado)
                                    {
                                        $strNombreDocumento = $arrayResultado['strNombreDocumento'];
                                        $strFechaDoc      = $arrayResultado['strFechaCreacion'];
                                        $strPath          = $arrayResultado['strPathFile'];

                                        if(!empty($strPath))
                                        {
                                            $arrayArchivo   = explode('/', $strPath);
                                            $arrayCount     = count($arrayArchivo);
                                            $strNuevoNombre = $arrayArchivo[$arrayCount - 1];
                                            $arrayTipoDoc   = explode('.', $strNuevoNombre);
                                            $arrayCountT    = count($arrayTipoDoc);
                                            $strTipoDoc     = $arrayTipoDoc[$arrayCountT - 1];
                                        }
                                        $objAdmiTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                            ->findOneByExtensionTipoDocumento(strtoupper($strTipoDoc));
                                        if(!is_object($objAdmiTipoDocumento))
                                        {
                                            throw new \Exception("No se encuentra el tipo de documento, favor verificar.");
                                        }

                                        $objInfoDocumento = new InfoDocumento();
                                        $objInfoDocumento->setMensaje("Tarea generada automáticamente por el sistema Telcos");
                                        $objInfoDocumento->setNombreDocumento($strNombreDocumento);
                                        $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                                        $objInfoDocumento->setUbicacionFisicaDocumento($strPath);//url
                                        $objInfoDocumento->setUbicacionLogicaDocumento($strNombreDocumento);
                                        $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                        $objInfoDocumento->setEstado("Activo");
                                        $objInfoDocumento->setUsrCreacion($strUsrCreacion);
                                        $objInfoDocumento->setIpCreacion($strIpCreacion);
                                        $objInfoDocumento->setEmpresaCod($strCodEmpresa);
                                        $this->emComunicacion->persist($objInfoDocumento);
                                        $this->emComunicacion->flush();
                                        
                                        //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                                        $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                                        $objInfoDocumentoRelacion->setModulo('SOPORTE');
                                        $objInfoDocumentoRelacion->setEstado('Activo');
                                        $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                                        $objInfoDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                                        $objInfoDocumentoRelacion->setDetalleId($objEntityDetalle->getId());
                                        $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());

                                        $this->emComunicacion->persist($objInfoDocumentoRelacion);
                                        $this->emComunicacion->flush();
                                        
                                        $objCaracteristicaServicio = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                     ->findOneBy(array('descripcionCaracteristica' => 'PROYECTO_CRM', 
                                                                                       'tipo'                      => 'COMERCIAL')
                                                                               );
                                        $objInfoServicioProdCaractP = $this->serviceGeneral->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                        'PROYECTO_CRM',
                                                                                                         $objInfoServicio->getProductoId());
    
                                        if(is_object($objCaracteristicaServicio) && is_object($objInfoComunicacion) 
                                            && is_object($objInfoServicioProdCaractP))
                                        {
                                            //Agregamos la instancia
                                            $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                                            $objInfoTareaCaracteristica->setTareaId($objInfoComunicacion->getId());
                                            $objInfoTareaCaracteristica->setDetalleId($objEntityDetalle->getId());
                                            $objInfoTareaCaracteristica->setCaracteristicaId($objCaracteristicaServicio->getId());
                                            $objInfoTareaCaracteristica->setValor($objInfoServicioProdCaractP->getValor());
                                            $objInfoTareaCaracteristica->setEstado('Activo');
                                            $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                                            $objInfoTareaCaracteristica->setUsrCreacion($objInfoServicio->getUsrCreacion());
                                            $objInfoTareaCaracteristica->setIpCreacion($objInfoServicio->getIpCreacion());
                                            $this->emSoporte->persist($objInfoTareaCaracteristica);
                                            $this->emSoporte->flush();
                                        }
                                    }
                                    //Agregamos seguimiento con el pedido
                                    $objInfoServicioProdCaractPedido = $this->serviceGeneral->getServicioProductoCaracteristica($objInfoServicio,
                                                                                                        'PEDIDO_ID',
                                                                                                         $objInfoServicio->getProductoId());
                                    $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                                ->find($arrayParametros['intIdPersona']);
                                    if(is_object($objInfoServicioProdCaractPedido) && is_object($objInfoPersona))
                                    {
                                        $intNumPedido      = $objInfoServicioProdCaractPedido->getValor();
                                        $strResponsable = $objInfoPersona->getApellidos() .' '.$objInfoPersona->getNombres();

                                        $objInfoTareaSeg = $this->emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                                                    ->findOneBy(array("detalleId" => $objEntityDetalle->getId()));
                                        if(!is_object($objInfoTareaSeg))
                                        {
                                            throw new \Exception("Tarea en Info Comunicación no encontrada, favor verificar.");
                                        }
                                        $objInfoTareaSeguimiento = new InfoTareaSeguimiento();
                                        $objInfoTareaSeguimiento->setDetalleId($objInfoTareaSeg->getDetalleId());
                                        $objInfoTareaSeguimiento->setObservacion('Número de pedido '.$intNumPedido.' asignado a .'.$strResponsable);
                                        $objInfoTareaSeguimiento->setUsrCreacion('TELCOS+');
                                        $objInfoTareaSeguimiento->setFeCreacion(new \DateTime('now'));
                                        $objInfoTareaSeguimiento->setEmpresaCod($strCodEmpresa);
                                        $objInfoTareaSeguimiento->setEstadoTarea('Activo');
                                        $objInfoTareaSeguimiento->setInterno($objInfoTareaSeg->getInterno());
                                        $objInfoTareaSeguimiento->setDepartamentoId($objInfoTareaSeg->getDepartamentoId());
                                        $objInfoTareaSeguimiento->setPersonaEmpresaRolId($objInfoTareaSeg->getPersonaEmpresaRolId());
                                        $this->emSoporte->persist($objInfoTareaSeguimiento);
                                        $this->emSoporte->flush();
                                     }
                                    //Actualizamos quien retira
                                    $objInfoPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                                                                ->find($arrayParametros['intIdPersona']);
                                    if(!is_object($objInfoPersona))
                                    {
                                        throw new \Exception("No se encuentra el id de la persona, favor verificar.");
                                    }
                                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                    ->findByIdentificacionTipoRolEmpresa($objInfoPersona->getIdentificacionCliente(), 'Empleado', $strCodEmpresa);
                                    if(!is_object($objInfoPersonaEmpresaRol))
                                    {
                                        throw new \Exception("No se encuentra el rol de la persona, favor verificar.");
                                    }
                                    $objDepartamento = $this->emComercial->getRepository('schemaBundle:AdmiDepartamento')
                                                            ->find($objInfoPersonaEmpresaRol->getDepartamentoId());
                                    if(!is_object($objDepartamento))
                                    {
                                        throw new \Exception("No se encuentra el departamento, favor verificar.");
                                    }
                                    $arrayParametrosPro = array("intServicio"     => $objInfoServicio->getId(),
                                                             "strDepartNombre" => $objDepartamento->getNombreDepartamento());
                                    $arrayResultado   = $this->emNaf->getRepository('schemaBundle:admiProyectos')
                                                         ->getPedidoResponsable($arrayParametrosPro);  
                                    if($arrayResultado['total'] >= 1)
                                    {
                                        $arrayRegistros = $arrayResultado['registros'];
                                        $arrayUpdate = array();
                                        foreach($arrayRegistros as $arrayRegistro)
                                        {
                                            array_push($arrayUpdate,$arrayRegistro['detalleId']);
                                        }
                                        $arrayParametrosPro = array("strLogin"     => $objInfoPersona->getLogin());
                                        $arrayResultadoPersonaNaf   = $this->emNaf->getRepository('schemaBundle:admiProyectos')
                                                                ->getPersonaByNaf($arrayParametrosPro);  
                                        if($arrayResultadoPersonaNaf['total'] >= 1)
                                        {
                                            $arrayRegistrosPersonaNaf = $arrayResultadoPersonaNaf['registros'][0];
                                            $intEmpleado = $arrayRegistrosPersonaNaf['empleadoid'];
                                            $arrayDatosActualizar = array('intUsuarioId'   => $intEmpleado,
                                                                            'strLoginUsu'    => $objInfoPersona->getLogin(),
                                                                            'arrayDetalleId' => $arrayUpdate);
                                            $arrayResultado   = $this->emNaf->getRepository('schemaBundle:admiProyectos')
                                                                            ->getActualizaResponsable($arrayDatosActualizar); 
                                        }
                                    }
                                
                                }
                                
                                if ($strEsHal === 'S')
                                {
                                    $this->emSoporte->getConnection()->beginTransaction();
                                    $this->emComunicacion->getConnection()->beginTransaction();
                                }
                                $arrayOrigen = array(
                                    "local",
                                    "MOVIL");
                                if(in_array($strOrigen, $arrayOrigen) && $boolGuardo)
                                {
                                    $boolGuardoGlobal = true;
                                    //SE ACTUALIZA EL ESTADO DE LA SOLICITUD
                                    $objSolicitud->setEstado("AsignadoTarea");
                                    $this->emComercial->persist($objSolicitud);
                                    $this->emComercial->flush();

                                    //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                    if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                                    {
                                        $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                               ->findOneDetalleSolicitudHistorial($id, 'AsignadoTarea');
                                        
                                    }
                                    else
                                    {
                                        $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                                               ->findOneDetalleSolicitudHistorial($id, 'Planificada');
                                    }
                                    
                                    $entityDetalleSolHist = new InfoDetalleSolHist();
                                    $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);

                                    if ($strEsHal === 'S')
                                    {
                                        $objDateFechaIniHal = date_create(date('Y-m-d H:i', strtotime($strFechaHal.' '.$strHoraIniHal)));
                                        $objDateFechaFinHal = date_create(date('Y-m-d H:i', strtotime($strFechaHal.' '.$strHoraFinHal)));
                                        if (is_object($objLastDetalleSolhist))
                                        {
                                            $objLastDetalleSolhist->setFeIniPlan($objDateFechaIniHal);
                                            $objLastDetalleSolhist->setFeFinPlan($objDateFechaFinHal);
                                            $this->emComercial->persist($objLastDetalleSolhist);
                                            $this->emComercial->flush();
                                        }
                                        else
                                        {
                                            $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                            ->findOneDetalleSolicitudHistorial($id, 'Replanificada');

                                            if(is_object($objLastDetalleSolhist) 
                                            && 
                                            strtoupper($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud()) <> 'SOLICITUD PLANIFICACION')
                                            {
                                                $objLastDetalleSolhist->setFeIniPlan($objDateFechaIniHal);
                                                $objLastDetalleSolhist->setFeFinPlan($objDateFechaIniHal);
                                                $this->emComercial->persist($objLastDetalleSolhist);
                                                $this->emComercial->flush();
                                            }
                                        }

                                        if (is_object($objServHistCoordina))
                                        {
                                            $strObservacionCoordina   .= "<br>";
                                            $strObservacionCoordina   .= "<br>Fecha Planificada: " . str_replace("-","/",$strFechaHal);
                                            $strObservacionCoordina   .= "<br>Hora Inicio: " . $strHoraIniHal;
                                            $strObservacionCoordina   .= "<br>Hora Fin: " . $strHoraFinHal;
                                            $strObservacionCoordina   .= "<br><br>";
                        
                                            $objServHistCoordina->setObservacion($strObservacionCoordina);
                                            $this->emComercial->persist($objServHistCoordina);
                                            $this->emComercial->flush();
                                        }
                                    }


                                    if($objLastDetalleSolhist)
                                    {
                                        $entityDetalleSolHist->setFeIniPlan($objLastDetalleSolhist->getFeIniPlan());
                                        $entityDetalleSolHist->setFeFinPlan($objLastDetalleSolhist->getFeFinPlan());
                                        if(isset($strEsGestionSimultanea) && !empty($strEsGestionSimultanea) && $strEsGestionSimultanea === "SI"
                                            && isset($strObservacionSolSimultanea) && !empty($strObservacionSolSimultanea) 
                                            && isset($strObservacionAdicional) && !empty($strObservacionAdicional)
                                            && $strEsHal !== 'S')
                                        {
                                            $strObservacion .= $strObservacionSolSimultanea."<br>".$strObservacionAdicional;
                                        }
                                        else
                                        {
                                            $strObservacion = $objLastDetalleSolhist->getObservacion();
                                        }
                                        $entityDetalleSolHist->setObservacion($strObservacion);
                                    }
                                    $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                                    $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                                    $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                                    $entityDetalleSolHist->setEstado('AsignadoTarea');
                                    $this->emComercial->persist($entityDetalleSolHist);
                                    $this->emComercial->flush();
                                    /******************************************************************* */

                                    $strOpcion = 'Cliente: ' . $objInfoServicio->getPuntoId()->getNombrePunto() . ' | OPCION: Punto Cliente';
                                    
                                    $objInfoCriterioA = $this->emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                                        ->getInfoParteAfectadaExistente($objEntityDetalle->getId());

                                    $intIdCA = 1;

                                    if($objInfoCriterioA == null)
                                    {
                                        $objInfoCriterioAfectado = new InfoCriterioAfectado();

                                        $objInfoCriterioAfectado->setId($intIdCA);
                                        $objInfoCriterioAfectado->setDetalleId($objEntityDetalle);
                                        $objInfoCriterioAfectado->setCriterio("Clientes");
                                        $objInfoCriterioAfectado->setOpcion($strOpcion);
                                        $objInfoCriterioAfectado->setUsrCreacion($strUsrCreacion);
                                        $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                                        $objInfoCriterioAfectado->setIpCreacion($strIpCreacion);
    
                                        $this->emSoporte->persist($objInfoCriterioAfectado);
                                        $this->emSoporte->flush();
    
                                        $entityInfoParteAfectada = new InfoParteAfectada();
    
                                        $entityInfoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
                                        $entityInfoParteAfectada->setDetalleId($objEntityDetalle->getId());
                                        $entityInfoParteAfectada->setAfectadoId($objInfoServicio->getPuntoId()->getId());
                                        $entityInfoParteAfectada->setTipoAfectado("Cliente");
                                        $entityInfoParteAfectada->setAfectadoNombre($objInfoServicio->getPuntoId()->getLogin());
                                        $entityInfoParteAfectada->setAfectadoDescripcion($objInfoServicio->getPuntoId()->getNombrePunto());
                                        if($objLastDetalleSolhist)
                                        {
                                            $entityInfoParteAfectada->setFeIniIncidencia($objLastDetalleSolhist->getFeCreacion());
                                        }
                                        else
                                        {
                                            $entityInfoParteAfectada->setFeIniIncidencia($objSolicitud->getFeCreacion());
                                        }
                                        $entityInfoParteAfectada->setUsrCreacion($strUsrCreacion);
                                        $entityInfoParteAfectada->setFeCreacion(new \DateTime('now'));
                                        $entityInfoParteAfectada->setIpCreacion($strIpCreacion);
    
                                        $this->emSoporte->persist($entityInfoParteAfectada);
                                        $this->emSoporte->flush();

                                    }

                                    /******************************************************************* */
                                    $arrayProdPermitidos = array();
                                    $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                                                'Productos adicionales manuales para planificar simultaneo',
                                                                '','','','','',$strCodEmpresa);
                                    if (is_array($arrayParamValores) && !empty($arrayParamValores))
                                    {
                                        $arrayProdPermitidos = $this->utilServicio->obtenerValoresParametro($arrayParamValores);
                                    }
                                    // Verificamos si es un proceso principal o simultaneo
                                    $strBanSimultaneo = $arrayParametros['strProcesoSimultaneo'];
                                    if (empty($strBanSimultaneo))
                                    {
                                        $strBanSimultaneo = "NO";
                                    }
                                    if($strTipoSolicitud == "solicitud planificacion" || $boolSigueFlujoPlanificacion)
                                    {
                                        //SE ACTUALIZA EL ESTADO DEL SERVICIO
                                        $entityServicio = $objSolicitud->getServicioId();
                                        $intIdPunto = $entityServicio->getPuntoId()->getId();
                                        $strEstadoAnt = $entityServicio->getEstado();
                                        $entityServicio->setEstado("AsignadoTarea");
                                        $this->emComercial->persist($entityServicio);
                                        $this->emComercial->flush();
                                        
                                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                        $entityServicioHist = new InfoServicioHistorial();
                                        $entityServicioHist->setServicioId($entityServicio);
                                        $entityServicioHist->setIpCreacion($strIpCreacion);
                                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                                        $entityServicioHist->setUsrCreacion($strUsrCreacion);
                                        $entityServicioHist->setEstado('AsignadoTarea');
                                        $entityServicioHist->setObservacion($strObservacion . "<br>" . $strObservacionServicio);
                                        $this->emComercial->persist($entityServicioHist);
                                        $this->emComercial->flush();

                                        // Validamos que solo sea por planificacion principal
                                        if ($strBanSimultaneo == "NO")
                                        {
                                            // Se invoca metodo para cambiar el estado de los productos adicionales
                                            $strPlanServicio = $entityServicio->getPlanId();
                                            $strProdServicio = $entityServicio->getProductoId();
                                            if ((!empty($strPlanServicio) && $strTipoSolicitud == "solicitud planificacion") ||
                                                (!empty($strProdServicio) && $strTipoSolicitud == "solicitud planificacion" &&
                                                in_array($strProdServicio->getId(), $arrayProdPermitidos)))
                                            {
                                                $strFeInicio = $arrayParametros['strFechaInicio'];
                                                $strFeFin = $arrayParametros['strFechaFin'];
                                                $strObsAsigna = $strObservacion ."<br>". $strObservacionServicio;
                                                $arrayDatosEstado = array(
                                                    "intIdPunto" => $intIdPunto,
                                                    "intIdServicio" => $entityServicio->getId(),
                                                    "strEstado" => "Asignada",
                                                    "strEstadoAnt" => $strEstadoAnt,
                                                    "strFechaInicio" => $strFeInicio,
                                                    "strFechaFin" => $strFeFin,
                                                    "strObservacionServicio" => $strObsAsigna,
                                                    "strObservacionSolicitud" => $arrayParametros['strObservacionSolicitud'],
                                                    "strIpCreacion" => $arrayParametros['strIpCreacion'],
                                                    "strUsrCreacion" => $arrayParametros['strUsrCreacion'],
                                                    "strSolCableado" => "NO",
                                                    "intIdEmpresa" => $strCodEmpresa,
                                                    "strEsHal" => $arrayParametros['strEsHal']
                                                );
                                                if ($strEsHal === 'S')
                                                {
                                                    $arrayDatosEstado['strFechaHal']       = $strFechaHal;
                                                    $arrayDatosEstado['strHoraIniHal']     = $strHoraIniHal;
                                                    $arrayDatosEstado['strHoraFinHal']     = $strHoraFinHal;
                                                    $arrayDatosEstado['strObservacionHal'] = $strObservacionCoordina;
                                                }
                                                $objResultActProdAd = $this->actualizaEstProdAdiManuales($arrayDatosEstado, $arrayParametrosTarea);
                                                if($strEsHal === 'S' && !empty($objResultActProdAd) &&
                                                (strtoupper($objResultActProdAd['mensaje']) !== 'OK'
                                                || strtoupper($objResultActProdAd['result']['estado']) === false))
                                                {
                                                    throw new \Exception($objResultActProdAd["mensaje"]);
                                                }
                                            }
                                        }

                                        $strObservacionData    .= "<b>Informaci&oacute;n del Cliente</b><br/>";
                                        $strObservacionData    .= "Nombre: " . $strNombreCliente . "<br/>";
                                        $strObservacionData    .= "Direcci&oacute;n: " . $objInfoPersonaCliente->getDireccionTributaria() . "<br/>";


                                        $strObservacionData .= "<br/><b>Informaci&oacute;n del Punto</b><br/>";
                                        $strObservacionData .= "Nombre: " . $objInfoPuntoServicio->getNombrePunto() . "<br/>";

                                        $strObservacionData       .= "Direcci&oacute;n: " . $objInfoPuntoServicio->getDireccion() . "<br/>";
                                        $strObservacionData       .= "Referencia: " . $objInfoPuntoServicio->getDescripcionPunto() . "<br/>";
                                        $strObservacionData       .= "Latitud: " . $objInfoPuntoServicio->getLatitud() . "<br/>";
                                        $strObservacionData       .= "Longitud: " . $objInfoPuntoServicio->getLongitud() . "<br/><br/>";
                                        $arrayFormasContactoPunto = $this->emComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                ->findPorEstadoPorPunto($objInfoPuntoServicio->getId(), 'Activo', 6, 0);

                                        if($arrayFormasContactoPunto['registros'])
                                        {
                                            $arrayFormasContactoPunto = $arrayFormasContactoPunto['registros'];
                                            $strObservacionData       .= "Contactos<br/>";
                                            foreach($arrayFormasContactoPunto as $arrayFormaContactoPunto)
                                            {
                                                $strDescripcionFormaContactoPunto = $arrayFormaContactoPunto->getFormaContactoId()
                                                        ->getDescripcionFormaContacto();
                                                $strObservacionData               .= $strDescripcionFormaContactoPunto . ": " .
                                                        $arrayFormaContactoPunto->getValor() . "<br/>";
                                            }
                                        }

                                        $objTipoSolFactible = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneBy(array(
                                            "descripcionSolicitud" => "SOLICITUD FACTIBILIDAD"));

                                        $objSolFactibilidad = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->findOneBy(array(
                                            "servicioId"      => $objSolicitud->getServicioId(),
                                            "tipoSolicitudId" => $objTipoSolFactible
                                                )
                                        );
                                        if($objSolFactibilidad)
                                        {
                                            $strObservacionData              .= "<br/><b>Datos de Factibilidad</b><br/>";
                                            $arrayDetalleSolLastFactibilidad = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                    ->findLastDetalleSolHistByIdYEstado($objSolFactibilidad->getId(), "Factible");
                                            if($arrayDetalleSolLastFactibilidad)
                                            {
                                                $strObservacionFactibilidad = $arrayDetalleSolLastFactibilidad[0]['observacion'];
                                                $strObservacionData         .= $strObservacionFactibilidad;
                                            }
                                        }


                                        $arrayDetalleSolLastPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                ->findLastDetalleSolHistByIdYEstado($id, "Planificada");
                                        if($arrayDetalleSolLastPlanificacion)
                                        {
                                            $strEstadoSolLastPlanificacion = $arrayDetalleSolLastPlanificacion[0]['estado'];

                                            if($strEstadoSolLastPlanificacion == "Planificada")
                                            {
                                                $strObservacionData .= "<br/><b>Datos de Planificaci&oacute;n</b><br/>";
                                            }
                                            else if($strEstadoSolLastPlanificacion == "Replanificada")
                                            {
                                                $strObservacionData         .= "<br/><b>Datos de Replanificaci&oacute;n</b><br/>";
                                                $intIdMotivoLastPlanif      = $arrayDetalleSolLastPlanificacion[0]['motivoId'];
                                                $objMotivoLastPlanificacion = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')
                                                        ->find($intIdMotivoLastPlanif);
                                                $strObservacionData         .= "Motivo: " . $objMotivoLastPlanificacion->getNombreMotivo() . "<br/>";
                                            }
                                            else
                                            {
                                                $strObservacionData .= "";
                                            }
                                            $strObservacionPlanificacion = "Observaci&oacute;n: " . 
                                                                           $arrayDetalleSolLastPlanificacion[0]['observacion'];
                                            $objFeIniPlanificada         = $arrayDetalleSolLastPlanificacion[0]['feIniPlan'];
                                            $objFeFinPlanificada         = $arrayDetalleSolLastPlanificacion[0]['feFinPlan'];
                                            if (is_object($objFeIniPlanificada) && is_object($objFeFinPlanificada))
                                            {
                                                $strFechaIniPlanificada      = strval(date_format($objFeIniPlanificada, "d/m/Y"));
                                                $strHoraIniPlanificada       = strval(date_format($objFeIniPlanificada, "H:i"));
                                                $strHoraFinPlanificada       = strval(date_format($objFeFinPlanificada, "H:i"));
                                            }
                                            $strObservacionPlanificacion .= "<br>Fecha: " . $strFechaIniPlanificada;
                                            $strObservacionPlanificacion .= "<br>Hora Inicio: " . $strHoraIniPlanificada;
                                            $strObservacionPlanificacion .= "<br>Hora Fin: " . $strHoraFinPlanificada;
                                            $strObservacionData          .= $strObservacionPlanificacion;
                                        }

                                        //Se ingresa el historial de la info_detalle
                                        $arrayParametrosHist["strObservacion"] = $strObservacionData;

                                        $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                                    }
                                    if($strTipoSolicitud == "solicitud retiro equipo")
                                    {
                                    //actualizar las solicitudes caract
                                        $arrayDetalleSolicitudCarac = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findBy(array(
                                            "detalleSolicitudId" => $objSolicitud->getId(),
                                            "estado"             => "PrePlanificada"));
                                        foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                                        {
                                            $objDetalleSolCarac->setEstado("AsignadoTarea");
                                            $this->emComercial->persist($objDetalleSolCarac);
                                            $this->emComercial->flush();
                                        }
                                    }

                                    // Validamos que solo sea por planificacion principal
                                    if ($strBanSimultaneo == "NO")
                                    {
                                        // Se crea planificacion simultanea para los productos adicionales CE o netlifecam
                                        $arrayTiposSolicitudes = array();
                                        $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                                                    'Planificacion simultanea que no son de solicitudes de planificacion',
                                                                    '','','','','',$strCodEmpresa);
                                        if (is_array($arrayParamValores) && !empty($arrayParamValores))
                                        {
                                            $arrayTiposSolicitudes = $this->utilServicio->obtenerValoresParametro($arrayParamValores);
                                        }
                                        $objServAdicional = $objSolicitud->getServicioId();
                                        $strProdServicio  = $objServAdicional->getProductoId();
                                        if(!empty($strProdServicio) &&
                                        in_array($strTipoSolicitud, $arrayTiposSolicitudes) &&
                                        in_array($strProdServicio->getId(), $arrayProdPermitidos))
                                        {
                                            //SE ACTUALIZA EL ESTADO DEL SERVICIO
                                            $strEstadoActual = "Asignada";
                                            $intIdPunto = $objServAdicional->getPuntoId()->getId();
                                            if ($objServAdicional->getEstado() == 'Planificada' || 
                                                $objServAdicional->getEstado() == 'PrePlanificada' ||
                                                $objServAdicional->getEstado() == 'Replanificada')
                                            {
                                                $strEstadoAnt = $objServAdicional->getEstado();
                                                $objServAdicional->setEstado($strEstadoActual);
                                                $this->emComercial->persist($objServAdicional);
                                                $this->emComercial->flush();
                                            }
                                            else if ($objServAdicional->getEstado() == 'Activo')
                                            {
                                                $strEstadoAnt = 'Planificada';
                                            }
                                            //GUARDAR INFO SERVICIO HISTORIAL
                                            $objServAdicionalHist = new InfoServicioHistorial();
                                            $objServAdicionalHist->setServicioId($objServAdicional);
                                            $objServAdicionalHist->setIpCreacion($strIpCreacion);
                                            $objServAdicionalHist->setFeCreacion(new \DateTime('now'));
                                            $objServAdicionalHist->setUsrCreacion($strUsrCreacion);
                                            $objServAdicionalHist->setEstado($strEstadoActual);
                                            $objServAdicionalHist->setObservacion($strObservacion . "<br>" . $strObservacionServicio);
                                            $this->emComercial->persist($objServAdicionalHist);
                                            $this->emComercial->flush();
                                            // Actualizar los servicios adicionales
                                            $strFeInicio = $arrayParametros['strFechaInicio'];
                                            $strFeFin = $arrayParametros['strFechaFin'];
                                            $strObsAsigna = $strObservacion ."<br>". $strObservacionServicio;
                                            $arrayDatosEstado = array(
                                                "intIdPunto" => $intIdPunto,
                                                "intIdServicio" => $objServAdicional->getId(),
                                                "strEstado" => $strEstadoActual,
                                                "strEstadoAnt" => $strEstadoAnt,
                                                "strFechaInicio" => $strFeInicio,
                                                "strFechaFin" => $strFeFin,
                                                "strObservacionServicio" => $strObsAsigna,
                                                "strObservacionSolicitud" => $arrayParametros['strObservacionSolicitud'],
                                                "strIpCreacion" => $arrayParametros['strIpCreacion'],
                                                "strUsrCreacion" => $arrayParametros['strUsrCreacion'],
                                                "strSolCableado" => "SI",
                                                "intIdEmpresa" => $strCodEmpresa,
                                                "strEsHal" => $arrayParametros['strEsHal']
                                            );
                                            if ($strEsHal === 'S')
                                            {
                                                $arrayDatosEstado['strFechaHal']       = $strFechaHal;
                                                $arrayDatosEstado['strHoraIniHal']     = $strHoraIniHal;
                                                $arrayDatosEstado['strHoraFinHal']     = $strHoraFinHal;
                                                $arrayDatosEstado['strObservacionHal'] = $strObservacionCoordina;
                                            }
                                            $this->actualizaEstProdAdiManuales($arrayDatosEstado, $arrayParametrosTarea);

                                            // SE ACTUALIZA EL ESTADO DE LA SOLICITUD
                                            $objSolicitud->setEstado($strEstadoActual);
                                            $this->emComercial->persist($objSolicitud);
                                            $this->emComercial->flush();
                                            //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                                            $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                                            $entityDetalleSolHist->setEstado($strEstadoActual);
                                            $this->emComercial->persist($entityDetalleSolHist);
                                            $this->emComercial->flush();
                                                
                                        }
                                    }
                                }
                                else
                                {
                                    $strMensajeError = "No guardo";
                                }
                            }
                            else
                            {
                                $strMensajeError = "No existe tareas predefinidas";
                            }
                        }
                        else
                        {
                            $strMensajeError = "No existe el proceso predefinido";
                        }
                    }

                    if(!$boolGuardo)
                    {
                        $this->emComercial->getConnection()->rollback();
                        $this->emInfraestructura->getConnection()->rollback();
                        $this->emSoporte->getConnection()->rollback();
                        $this->emComunicacion->getConnection()->rollback();
                        $this->emComercial->getConnection()->rollback();
                        $boolGuardoGlobal = false;
                        $arrayRespuesta['mensaje'] = $strMensajeError;
                    }

                endforeach;
//validacion si es reubicacion copiar mismos recursos de red
                if($strOrigen == "local" && $boolGuardoGlobal && ($strTipoSolicitud == "solicitud planificacion" || $boolSigueFlujoPlanificacion))
                {
                    $objServicio     = $objSolicitud->getServicioId();
                    $strTipoOrden    = "";
                    $objAdmiProducto = null;
                    $objPlanServicio = null;
                    if(is_object($objServicio))
                    {
                        $strTipoOrden    = $objServicio->getTipoOrden();
                        $objAdmiProducto = $objServicio->getProductoId();
                        $objPlanServicio = $objServicio->getPlanId();
                    }

                    if($strTipoOrden == 'R')
                    {
                        $objServicio   = $objSolicitud->getServicioId();
                        $intIdServicio = $objServicio->getId();

                        $objProductoInternetDedicado  = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                ->findOneBy(array(
                            "empresaCod"          => $strCodEmpresa,
                            "descripcionProducto" => "INTERNET DEDICADO",
                            "estado"              => "Activo")
                        );
//obtengo el servicio anterior
                        $objCaracteristicaReubicacion = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy(array(
                            "descripcionCaracteristica" => "REUBICACION",
                            "estado"                    => "Activo"));
                        $objPcReubicacion             = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                ->findOneBy(array(
                            "productoId"       => $objProductoInternetDedicado->getId(),
                            "caracteristicaId" => $objCaracteristicaReubicacion->getId()));
                        $objIsPcReubicacion           = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                ->findOneBy(array(
                            "servicioId"                => $intIdServicio,
                            "productoCaracterisiticaId" => $objPcReubicacion->getId())
                        );

                        $objServicioAnterior   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                   ->find($objIsPcReubicacion->getValor());
                        $intIdServicioAnterior = $objServicioAnterior->getId();

//copiar elemento e interface
                        $objInfoServicioTecnico         = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneByServicioId($intIdServicio);
                        $objInfoServicioTecnicoAnterior = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneByServicioId($intIdServicioAnterior);

                        $objInfoServicioTecnico->setElementoId($objInfoServicioTecnicoAnterior->getElementoId());
                        $objInfoServicioTecnico->setInterfaceElementoId($objInfoServicioTecnicoAnterior->getInterfaceElementoId());
                        $objInfoServicioTecnico->setElementoContenedorId($objInfoServicioTecnicoAnterior->getElementoContenedorId());
                        $objInfoServicioTecnico->setElementoConectorId($objInfoServicioTecnicoAnterior->getElementoConectorId());
                        $objInfoServicioTecnico->setInterfaceElementoConectorId($objInfoServicioTecnicoAnterior->getInterfaceElementoConectorId());
                        $this->emComercial->persist($objInfoServicioTecnico);
                        $this->emComercial->flush();

//copiar todas las caracteristicas del servicio reubicado
                        $objServicioProdCaracts = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                ->findBy(array(
                            "servicioId" => $intIdServicioAnterior,
                            "estado"     => 'Activo'));

                        foreach($objServicioProdCaracts as $objServicioProdCaract)
                        {
                            $objServicioProdCaractCopy = new InfoServicioProdCaract();
                            $objServicioProdCaractCopy = clone $objServicioProdCaract;
                            $objServicioProdCaractCopy->setServicioId($intIdServicio);
                            $objServicioProdCaractCopy->setFeCreacion(new \DateTime('now'));
                            $objServicioProdCaractCopy->setUsrCreacion($strUsrCreacion);

                            $this->emComercial->persist($objServicioProdCaractCopy);
                            $this->emComercial->flush();
                        }

//copiar todas las Ips
                        $objInfoIps = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                ->findBy(array(
                            "servicioId" => $intIdServicioAnterior,
                            "estado"     => 'Activo'));

                        foreach($objInfoIps as $objInfoIp)
                        {
                            $objInfoIpCopy = new InfoIp();
                            $objInfoIpCopy = clone $objInfoIp;
                            $objInfoIpCopy->setServicioId($intIdServicio);
                            $objInfoIpCopy->setFeCreacion(new \DateTime('now'));
                            $objInfoIpCopy->setUsrCreacion($strUsrCreacion);
                            $objInfoIpCopy->setIpCreacion($strIpCreacion);

                            $this->emInfraestructura->persist($objInfoIpCopy);
                            $this->emInfraestructura->flush();
                        }

//actualizo estados
                        $objSolicitud->setEstado("Asignada");
                        $this->emComercial->persist($objSolicitud);
                        $this->emComercial->flush();

//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                        {
                            $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                ->findOneDetalleSolicitudHistorial($intIdFactibilidad, 'AsignadoTarea');
                        }
                        else
                        {
                            $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                ->findOneDetalleSolicitudHistorial($intIdFactibilidad, 'Planificada');
                        }
                        
                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                        if($objLastDetalleSolhist)
                        {
                            $entityDetalleSolHist->setFeIniPlan($objLastDetalleSolhist->getFeIniPlan());
                            $entityDetalleSolHist->setFeFinPlan($objLastDetalleSolhist->getFeFinPlan());
                            $entityDetalleSolHist->setObservacion($objLastDetalleSolhist->getObservacion());
                        }
                        $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                        $entityDetalleSolHist->setEstado('Asignada');

                        $this->emComercial->persist($entityDetalleSolHist);
                        $this->emComercial->flush();

//SE ACTUALIZA EL ESTADO DEL SERVICIO
                        $objServicio->setEstado("Asignada");
                        $this->emComercial->persist($objServicio);
                        $this->emComercial->flush();

//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($objServicio);
                        $entityServicioHist->setIpCreacion($strIpCreacion);
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($strUsrCreacion);
                        $entityServicioHist->setEstado('Asignada');
                        $entityServicioHist->setObservacion('Por Reubicacion Pasa a Asignada');

                        $this->emComercial->persist($entityServicioHist);
                        $this->emComercial->flush();
                    }
                    $boolFlujoAsignadoAutomatico = false;
                    $strDescripcionPlanProd      = "";

                    /**
                     * Se agrega validación cuando el servicio esté asociado a un plan y si es cliente MD, para validar si el plan tiene
                     * asociada la característica FLUJO_PREPLANIFICACION_PLANIFICACION='SI', que servirá como referencia para pasar el servicio
                     * de estado 'AsignadoTarea' a 'Asignada'
                     */
                    if(is_object($objAdmiProducto))
                    {
                        /*
                         * Se modifica la validación para pasar el estado de la solicitud directamente a Asignada cuando el producto tenga de
                         * nombre técnico OTROS y no requieran información técnica o cuando sea empresa MD y no sea enlace y no requiere info tecnica
                         */
                        if(
                                ($strPrefijoEmpresa == 'MD' && $objAdmiProducto->getEsEnlace() == 'NO' && 
                                  $objAdmiProducto->getRequiereInfoTecnica() == 'NO') ||
                                ($objAdmiProducto->getNombreTecnico() == 'OTROS' && $objAdmiProducto->getRequiereInfoTecnica() == 'NO' || 
                                          $strEsVentaExterna == 'SI')
                        )
                        {
                            $boolFlujoAsignadoAutomatico = true;
                            $strDescripcionPlanProd      = $objAdmiProducto->getDescripcionProducto();
                        }

                        if ( $objAdmiProducto->getDescripcionProducto() == 'WIFI Alquiler Equipos')
                        {
                            $boolFlujoAsignadoAutomatico = true;
                            $strDescripcionPlanProd      = $objAdmiProducto->getDescripcionProducto();
                        }
                        
                    }
                    else if(is_object($objPlanServicio))
                    {
                        if($strPrefijoEmpresa == 'MD')
                        {
                            $arrayParamSigueFlujoPP           = array(
                                "intIdPlan"                        => $objPlanServicio->getId(),
                                "strDescripcionCaracteristicaPlan" => 'FLUJO_PREPLANIFICACION_PLANIFICACION',
                                "strEstado"                        => "Activo"
                            );
                            $arrayRespuestaSigueFlujoPP       = $this->emComercial->getRepository('schemaBundle:InfoPlanCaracteristica')
                                    ->getCaracteristicasPlanByCriterios($arrayParamSigueFlujoPP);
                            $intTotalCaractSigueFlujoPP       = $arrayRespuestaSigueFlujoPP['intTotal'];
                            $arrayResultadoCaractSigueFlujoPP = $arrayRespuestaSigueFlujoPP['arrayResultado'];

                            if($intTotalCaractSigueFlujoPP > 0 && $arrayResultadoCaractSigueFlujoPP[0] && 
                                    $arrayResultadoCaractSigueFlujoPP[0]['strValor'] == "SI")
                            {
                                $boolFlujoAsignadoAutomatico = true;
                                $strDescripcionPlanProd      = $objPlanServicio->getNombrePlan();
                            }
                        }
                    }
                    else
                    {
                        throw new \Exception("Ha ocurrido un error. No se ha encontrado ni el producto ni el plan del servicio");
                    }
                    if($boolFlujoAsignadoAutomatico && is_object($objServicio))
                    {
//actualizo estados
                        if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                        {
                            $objSolicitud->setEstado("AsignadoTarea");
                        }
                        else
                        {
                            $objSolicitud->setEstado("Asignada");
                        }
                        
                        $this->emComercial->persist($objSolicitud);
                        $this->emComercial->flush();

//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                        {
                            $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                ->findOneDetalleSolicitudHistorial($intIdFactibilidad, 'AsignadoTarea');
                        }
                        else
                        {
                            $objLastDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                ->findOneDetalleSolicitudHistorial($intIdFactibilidad, 'Planificada');
                        }
                        
                        $entityDetalleSolHist = new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                        if($objLastDetalleSolhist)
                        {
                            $entityDetalleSolHist->setFeIniPlan($objLastDetalleSolhist->getFeIniPlan());
                            $entityDetalleSolHist->setFeFinPlan($objLastDetalleSolhist->getFeFinPlan());
                            $entityDetalleSolHist->setObservacion($objLastDetalleSolhist->getObservacion());
                        }
                        $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                        if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                        {
                            $entityDetalleSolHist->setEstado('AsignadoTarea');
                        }
                        else
                        {
                            $entityDetalleSolHist->setEstado('Asignada');
                        }
                        
                        $this->emComercial->persist($entityDetalleSolHist);
                        $this->emComercial->flush();

//SE ACTUALIZA EL ESTADO DEL SERVICIO
                        if(is_object($objServicio->getProductoId()) && $objServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")
                        {
                            $arrayParamsServInternetValido  = array("intIdPunto" => $objServicio->getPuntoId()->getId(),
                                                                    "strCodEmpresa" => $strCodEmpresa);
                            if($objServicio->getTipoOrden() === "T")
                            {
                                $arrayParamsServInternetValido["omiteEstadoPunto"] = "SI";
                            }
                            $arrayRespuestaServInternetValido   = $this->serviceGeneral
                                                                       ->obtieneServicioInternetValido($arrayParamsServInternetValido);
                            $strStatusServInternetValido        = $arrayRespuestaServInternetValido["status"];
                            $objServicioInternet                = $arrayRespuestaServInternetValido["objServicioInternet"];
                            if($strStatusServInternetValido === "OK" && is_object($objServicioInternet))
                            {
                                if($objServicio->getTipoOrden() === "T")
                                {
                                    $arrayRespuestaPlanifWyApTraslado   = $this->gestionarPlanificacionWyApTraslado(array(
                                                                                    "objServicioInternet"   => $objServicioInternet,
                                                                                    "strCodEmpresa"         => $strCodEmpresa,
                                                                                    "objServicio"           => $objServicio,
                                                                                    "strUsrCreacion"        => $strUsrCreacion));
                                    if($arrayRespuestaPlanifWyApTraslado["status"] === "ERROR")
                                    {
                                        throw new \Exception($arrayRespuestaPlanifWyApTraslado["mensaje"]);
                                    }
                                    $strNuevoEstadoServicio = $arrayRespuestaPlanifWyApTraslado["nuevoEstadoServicio"];
                                }
                                else
                                {
                                    $arrayRespuestaWdbEnlazado  = $this->serviceGeneral
                                                                       ->verificaEquipoEnlazado(
                                                                                       array("intIdServicioInternet" => $objServicioInternet->getId(),
                                                                                             "strTipoEquipoABuscar"  => "WIFI DUAL BAND"));
                                    $strInfoEquipoWdbEnlazado   = $arrayRespuestaWdbEnlazado["infoEquipoEnlazado"];
                                    if(isset($strInfoEquipoWdbEnlazado) && !empty($strInfoEquipoWdbEnlazado))
                                    {
                                        $strNuevoEstadoServicio = "PendienteAp";
                                    }
                                    else
                                    {
                                        $strNuevoEstadoServicio = "Asignada";
                                    }
                                }
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el servicio de Internet asociado al punto");
                            }
                        }
                        else if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                        {
                            $strNuevoEstadoServicio = "AsignadoTarea";
                        }
                        else
                        {
                            $strNuevoEstadoServicio = "Asignada";
                        }
                        
                        $objServicio->setEstado($strNuevoEstadoServicio);
                        $this->emComercial->persist($objServicio);
                        $this->emComercial->flush();

//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($objServicio);
                        $entityServicioHist->setIpCreacion($strIpCreacion);
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($strUsrCreacion);
                        $entityServicioHist->setEstado($strNuevoEstadoServicio);
                        $entityServicioHist->setObservacion('Por ser ' . $strDescripcionPlanProd . ' Pasa a: '.$strNuevoEstadoServicio);
                        $this->emComercial->persist($entityServicioHist);
                        $this->emComercial->flush();
                    }
                }
                if($strOrigen == "local" &&
                        $boolGuardoGlobal &&
                        ($strTipoSolicitud == "solicitud agregar equipo" ||
                         $strTipoSolicitud == "solicitud agregar equipo masivo" ||
                         $strTipoSolicitud == "solicitud cambio equipo por soporte" ||
                         $strTipoSolicitud == "solicitud cambio equipo por soporte masivo" ||
                         $strTipoSolicitud == "solicitud reubicacion"))
                {
//actualizar las solicitudes caract
                    $arrayDetalleSolicitudCarac = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                            ->findBy(array(
                        "detalleSolicitudId" => $objSolicitud->getId(),
                        "estado"             => "PrePlanificada"));
                    foreach($arrayDetalleSolicitudCarac as $objDetalleSolCarac)
                    {
                        $objDetalleSolCarac->setEstado("Asignada");
                        $this->emComercial->persist($objDetalleSolCarac);
                        $this->emComercial->flush();
                    }

//actualizo estados
                    if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                    {
                        $objSolicitud->setEstado("AsignadoTarea");
                    }
                    else
                    {
                        $objSolicitud->setEstado("Asignada");
                    }
                    $this->emComercial->persist($objSolicitud);
                    $this->emComercial->flush();
                    
                    if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                    {
                        $objDetalleSolhistAnt = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                            ->findOneDetalleSolicitudHistorial($intIdFactibilidad, 'AsignadoTarea');
                    }
                    else
                    {
                        $objDetalleSolhistAnt = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                            ->findOneDetalleSolicitudHistorial($intIdFactibilidad, 'Planificada');
                    }

                    $objDetalleSolHist = new InfoDetalleSolHist();
                    $objDetalleSolHist->setDetalleSolicitudId($objSolicitud);
                    if(is_object($objDetalleSolhistAnt))
                    {
                        $objDetalleSolHist->setFeIniPlan($objDetalleSolhistAnt->getFeIniPlan());
                        $objDetalleSolHist->setFeFinPlan($objDetalleSolhistAnt->getFeFinPlan());
                        $objDetalleSolHist->setObservacion($objDetalleSolhistAnt->getObservacion());
                    }
                    $objDetalleSolHist->setIpCreacion($strIpCreacion);
                    $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolHist->setUsrCreacion($strUsrCreacion);
                    if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                    {
                        $objDetalleSolHist->setEstado('AsignadoTarea');
                    }
                    else
                    {
                        $objDetalleSolHist->setEstado('Asignada');
                    }
                    $this->emComercial->persist($objDetalleSolHist);
                    $this->emComercial->flush();
                }

//validacion si es cambio de equipo y no tiene equipo asignado le crea
                if($strPrefijoEmpresa == "TTCO" && $strOrigen == "local" && $boolGuardoGlobal && ($strTipoSolicitud == "solicitud cambio equipo"))
                {
//GUARDAR INFO DETALLE SOLICICITUD SOL CARACT
                    $objDetallesSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                            ->findBy(array(
                        "detalleSolicitudId" => $objSolicitud->getId(),
                        "estado"             => "Activo"));

                    if(!$objDetallesSolCaract)
                    {
                        $objServicio                      = $objSolicitud->getServicioId();
                        $intIdServicio                    = $objServicio->getId();
                        $objInfoServicioTecnico           = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                ->findOneByServicioId($intIdServicio);
                        $objCaracteristicaElementoCliente = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                ->findOneBy(array(
                            "descripcionCaracteristica" => "ELEMENTO CLIENTE",
                            "estado"                    => "Activo"));

                        $entityDetalleSolCaract = new InfoDetalleSolCaract();
                        $entityDetalleSolCaract->setCaracteristicaId($objCaracteristicaElementoCliente);
                        $entityDetalleSolCaract->setDetalleSolicitudId($objSolicitud);
                        $entityDetalleSolCaract->setValor($objInfoServicioTecnico->getElementoClienteId());
                        $entityDetalleSolCaract->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolCaract->setUsrCreacion($strUsrCreacion);
                        $entityDetalleSolCaract->setEstado('Activo');

                        $this->emComercial->persist($entityDetalleSolCaract);
                        $this->emComercial->flush();

//GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityServicioHist = new InfoServicioHistorial();
                        $entityServicioHist->setServicioId($objServicio);
                        $entityServicioHist->setIpCreacion($strIpCreacion);
                        $entityServicioHist->setFeCreacion(new \DateTime('now'));
                        $entityServicioHist->setUsrCreacion($strUsrCreacion);
                        $entityServicioHist->setEstado($objSolicitud->getEstado());
                        $entityServicioHist->setObservacion('Se agrego la caracteristica elemento id:' .
                                $objInfoServicioTecnico->getElementoClienteId());

                        $this->emComercial->persist($entityServicioHist);
                        $this->emComercial->flush();
                    }
                }
            }
            else
            {
                $boolGuardoGlobal          = false;
                $arrayRespuesta['mensaje'] = "No existe esa opcion";
            }
            
            //Realizar la asignación de Recursos de Red de manera automatica para flujos L2MPLS
            if($strPrefijoEmpresa == "TN" && is_object($objServicio) && $objServicio->getProductoId()->getNombreTecnico()=='L2MPLS')
            {                                                
                $arrayParametrosL2                   = array();
                $arrayParametrosL2['objServicio']    = $objServicio;
                $arrayParametrosL2['strUsrCreacion'] = $strUsrCreacion;
                $arrayParametrosL2['strIpCreacion']  = $strIpCreacion;
                $arrayParametrosL2['intIdEmpresa']   = $intIdEmpresa;
                $arrayParametrosL2['strPrefijo']     = $strPrefijoEmpresa;
                $arrayParametrosL2['objSolicitud']   = $objSolicitud;    
                $arrayResultado                    = $arrayParametros['serviceRecursosRed']->asignarRecursosRedL2MPLS($arrayParametrosL2);
                
                if($arrayResultado['status'] != 'OK')
                {
                    $boolGuardoGlobal = false;
                    $arrayRespuesta['mensaje'] = $arrayResultado['mensaje'];
                }                
            }

            //notificacion sms a clientes por instalacion y traslado de servicio MD
            if (isset($arrayRespuestaCreaTareaAsig['enviaSMS']) && $arrayRespuestaCreaTareaAsig['enviaSMS'] =='S' 
                && ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN"))
            {
                $arrayDatosSms = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                  ->getNumeroContactoPorServicio(array('idServicio' => $objServicio->getId()));

                if ($arrayDatosSms['status']=='Ok')
                {
                    $strFechaSms = '';
                    $strHoraIniSms = '';
                    $strHoraFinSms = '';
                    if ($strEsHal === 'S')
                    {
                        $strFechaSms = $strFechaHal;
                        $strHoraIniSms = $strHoraIniHal;
                        $strHoraFinSms = $strHoraFinHal;
                    }
                    else
                    {
                        $arrayFechaIni = explode(" ", $arrayParametros['strFechaInicio']);
                        $arrayFechaFin = explode(" ", $arrayParametros['strFechaFin']);
                        $strFechaSms = $arrayFechaIni[0];
                        $strHoraIniSms = $arrayFechaIni[1];
                        $strHoraFinSms = $arrayFechaFin[1];
                    }
                    $arrayNombreTecnico = explode(" ", $arrayRespuestaCreaTareaAsig['nombreTecnico']);
                    $arrayTokenCas      = $this->serviceTokenCas->generarTokenCas(); 
                    $arrayParametrosSms = array(
                        "companyCod"=> $strPrefijoEmpresa,
                        "clientCod" => $objInfoPersonaCliente->getIdentificacionCliente(),
                        "serviceType" => $arrayParamConfigSms['valor1'],
                        "serviceProviderCod" => $arrayParamConfigSms['valor2'],
                        "cellPhoneNumber" => "",
                        "data" => array(
                            "messageId" => $arrayParamConfigSms['valor4'],
                            "data" => array(
                                array(
                                    "name" => "NUMERO_TAREA",
                                    "value" => $objInfoComunicacion->getId()
                                ),
                                array(
                                    "name" => "FECHA",
                                    "value" => $strFechaSms
                                ),
                                array(
                                    "name" => "HORA_INICIO",
                                    "value" => $strHoraIniSms
                                ),
                                array(
                                    "name" => "HORA_FIN",
                                    "value" => $strHoraFinSms
                                ),
                                array(
                                    "name" => "CODIGO_TRABAJO",
                                    "value" => $arrayRespuestaCreaTareaAsig['codigoTrabajo']
                                ),
                                array(
                                    "name" => "NOMBRE_TECNICO",
                                    "value" => $arrayNombreTecnico[0]." ".$arrayNombreTecnico[2]
                                ),
                                array(
                                    "name" => "IDENTIFICACION_TECNICO",
                                    "value" => $arrayRespuestaCreaTareaAsig['cedulaTecnico']
                                )
                            )
                        ),
                        "mobileProvider" => "",
                        "processCod" => $arrayParamConfigSms['valor3'],
                        "creationUser" => $strUsrCreacion
                    );
                    foreach($arrayDatosSms['arrayNumerosContactos'] as $arrayContactos)
                    {
                        $strNumeroContacto = "593".substr($arrayContactos['numeroContacto'], 1);
                        $strMobileProvider = substr($arrayContactos['formaContacto'], 15);
                        $arrayParametrosSms['cellPhoneNumber'] = $strNumeroContacto;
                        $arrayParametrosSms['mobileProvider'] =  strtoupper($strMobileProvider);
                        $this->envioNotiSmsMs(array(
                                                "objRequest" =>$arrayParametrosSms,
                                                "token" =>$arrayTokenCas['strToken'],
                                                "ipCreacion" =>$strIpCreacion,
                                                "usrCreacion" => $strUsrCreacion));
                    }
                }
                else
                {
                    $this->utilServicio->insertError( 'Telcos+',
                                    'PlanificarService->asignarPlanificacion()',
                                    'Error al obtener formas de contacto del servicio '
                                    .$objServicio->getId().': '.$arrayDatosSms['mensaje'],
                                    $strUsrCreacion,
                                    $strIpCreacion
                                );
                }
            }

            if($boolGuardoGlobal)
            {
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                  $this->emComercial->getConnection()->commit();                  
                }
                if ($this->emInfraestructura->getConnection()->isTransactionActive())
                {
                  $this->emInfraestructura->getConnection()->commit();
                }
                if ($this->emSoporte->getConnection()->isTransactionActive())
                {
                  $this->emSoporte->getConnection()->commit();
                }
                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                  $this->emComunicacion->getConnection()->commit();
                }
                $this->emComercial->getConnection()->commit();
                $arrayRespuesta['mensaje'] = "Se asignaron la(s) Tarea(s) Correctamente.".$strMensajeWs;
                if(is_object($objEntityDetalle))
                {
                    $arrayRespuesta['idDetalle'] = $objEntityDetalle->getId();

                    $arrayParametrosInfoTarea['intDetalleId']   = is_object($objEntityDetalle)? $objEntityDetalle->getId():null;
                    $arrayParametrosInfoTarea['strUsrCreacion'] = isset($strUsrCreacion)? $strUsrCreacion:null;
                    $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                }
                if(is_object($objInfoComunicacion))
                {
                    $arrayRespuesta['idComunicacion'] = $objInfoComunicacion->getId();
                }
            }
        }
        catch(\Exception $e)
        {

            if (is_object($objInfoDocumentoComunicacion))
            {
                $this->emComunicacion->remove($objInfoDocumentoComunicacion);
            }
            if (is_object($objInfoDocumento))
            {
                $this->emComunicacion->remove($objInfoDocumento);
            }
            if (is_object($objInfoComunicacion))
            {
                $this->emComunicacion->remove($objInfoComunicacion);
            }
            $this->emComunicacion->flush();
            
            if (is_object($objInfoDetalle))
            {
                $this->emSoporte->remove($objInfoDetalle);
            }
            $this->emSoporte->flush();

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
                $this->emComercial->close();
            }
            if ($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->rollback();
                $this->emInfraestructura->close();
            }
            if ($this->emSoporte->getConnection()->isTransactionActive())
            {
                $this->emSoporte->rollback();
                $this->emSoporte->close();
            }
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->rollback();
                $this->emComunicacion->close();
            }
            $strMensajeError = "Error: " . $e->getMessage();
            $arrayRespuesta['mensaje'] = "Ocurrio error al intentar ejecutar proceso de asignar planificación, por favor consulte a Sistemas!";
            $arrayRespuesta['status']  = "ERROR";
            error_log("Error " . json_encode($e));
            $this->utilServicio->insertError( 'Telcos+',
                                              'PlanificarService->asignarPlanificacion()',
                                              $strMensajeError,
                                              $strUsrCreacion,
                                              $strIpCreacion
                                            );
        }
        return $arrayRespuesta;
    }


    /**
     * @author Emmanuel Fernando Martillo Siavichay <jacarrillo@telconet.ec> 
     * @version 1.0 17-03-2023
     * #Envia notificacion por medio de SMS al cliente.
     * @param  array $arrayParametros [
     *                                  "token"        :string:  Token cass,
     *                                  "objRequest"   :string:  Request para envio al Web Service,
     *                                  "usrCreacion"  :string:  Usuario de creación,
     *                                  "ipCreacion"   :string:  Ip de donde se realizo la accion
     *                                 ]
     * 
     */
    public function envioNotiSmsMs($arrayParametros)  
    {
        $arrayResultado  = array();
        $strIpCreacion              = $arrayParametros['ipCreacion'];
        $strUsrCreacion             = $arrayParametros['usrCreacion']; 
        try 
        {
            $objOptions         = array(
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_HTTPHEADER     => array(
                    'Content-Type: application/json',
                    'tokencas: ' . $arrayParametros['token']
                )
            );
     
            $strJsonData        = json_encode($arrayParametros['objRequest']);
            $strUrl             = $this->strMassSendUrl;
            $arrayResponseJson  = $this->restClient->postJSON( $strUrl , $strJsonData, $objOptions);
            
            $strJsonRespuesta   = json_decode($arrayResponseJson['result'], true);

            if (
                isset($strJsonRespuesta['code']) && $strJsonRespuesta['code'] == 0
                && isset($strJsonRespuesta['status'])
                && isset($strJsonRespuesta['message'])
            ) 
            {
                $arrayResponse = array(
                    'strStatus' => 'OK',
                    'strMensaje' => $strJsonRespuesta['message'],
                    'objData' => $strJsonRespuesta['data']
                );
                $arrayResultado = $arrayResponse;
            }
             else 
            {
                $arrayResultado['strStatus']       = "ERROR";  
                
                if (!empty($strJsonRespuesta['message']))
                {   
                    $arrayResultado['strMensaje']  = $strJsonRespuesta['message'];                   
                } 
                else 
                {
                    $arrayResultado['strMensaje']  = "No Existe Conectividad con el Microservicio Massend.";
                }
            }
        } catch (\Exception $e) 
        {
            $arrayResultado['strMensaje'] = "Error al ejecutar el re procesamiento del registro. Favor Notificar a Sistemas" . $e->getMessage();
  
            $this->utilServicio->insertError(
                'Telcos+',
                'PlanificarService.envioNotiSmsMs',
                'Error PlanificarService.envioNotiSmsMs:' . $e->getMessage(),
                $strIpCreacion,
                $strUsrCreacion
            );
        }
        return $arrayResultado;
    }

    /**
     * Función creada para planificar un traslado de W+AP evitando colocar programación en la función principal por observación del sonnar
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-11-2020
     * 
     */
    public function gestionarPlanificacionWyApTraslado($arrayParametros)
    {
        $objServicioInternet    = $arrayParametros["objServicioInternet"];
        $strCodEmpresa          = $arrayParametros["strCodEmpresa"];
        $objServicio            = $arrayParametros["objServicio"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strMensaje             = "";
        try
        {
            $objServicioTecnicoInternetValido   = $this->emComercial
                                                       ->getRepository('schemaBundle:InfoServicioTecnico')
                                                       ->findOneBy(array("servicioId" => $objServicioInternet->getId()));
            if(!is_object($objServicioTecnicoInternetValido))
            {
                throw new \Exception("No se ha podido obtener el servicio técnico del servicio de Internet");
            }
            $intIdElementoCliente = $objServicioTecnicoInternetValido->getElementoClienteId();
            if(!isset($intIdElementoCliente) || empty($intIdElementoCliente))
            {
                throw new \Exception("No se ha podido obtener el ID del elemento cliente del servicio técnico del Internet");
            }
            $objElementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                          ->find($intIdElementoCliente);
            if(!is_object($objElementoCliente))
            {
                throw new \Exception("No se ha podido obtener el objeto del elemento cliente "
                                     ."del servicio técnico del Internet");
            }
            $objModeloElementoCliente   = $objElementoCliente->getModeloElementoId();
            $arrayVerificaModeloWdb     = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                          ->getOne( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    'MODELOS_EQUIPOS',
                                                                    '',
                                                                    '',
                                                                    'WIFI DUAL BAND',
                                                                    $objModeloElementoCliente->getNombreModeloElemento(),
                                                                    $strCodEmpresa);
            if(isset($arrayVerificaModeloWdb) && !empty($arrayVerificaModeloWdb))
            {
                $strNuevoEstadoServicio = "PendienteAp";
            }
            else
            {
                $strNuevoEstadoServicio         = "Asignada";
                $objTipoSolicitudAgregarEquipo  = $this->emComercial
                                                        ->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                        ->findOneByDescripcionSolicitud("SOLICITUD AGREGAR EQUIPO");
                if(!is_object($objTipoSolicitudAgregarEquipo))
                {
                    throw new \Exception("No se encontró información acerca del tipo de solicitud de agregar equipo");
                }

                $objCaractWifiDualBand  = $this->emComercial->getRepository("schemaBundle:AdmiCaracteristica")
                                                            ->findOneBy(array('descripcionCaracteristica' => 'WIFI DUAL BAND',
                                                                              'estado'                    => 'Activo'));
                if (!is_object($objCaractWifiDualBand))
                {
                    throw new \Exception("No se encontró información acerca de característica WIFI DUAL BAND");
                }

                $objCaractElementoCliente   = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneBy(array('descripcionCaracteristica'  => 
                                                                                  'ELEMENTO CLIENTE',
                                                                                  'estado'                     => 'Activo'));
                if(!is_object($objCaractElementoCliente))
                {
                    throw new \Exception("No se encontró información acerca de característica ELEMENTO CLIENTE");
                }

                $arrayEstadosVerificaSolAgregarEquipo = array();
                $arrayEstadosAbiertosSolAgregarEquipo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_MD',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    'ESTADOS_SOLICITUDES_ABIERTAS',
                                                                                    'SOLICITUD AGREGAR EQUIPO',
                                                                                    '',
                                                                                    '',
                                                                                    '',
                                                                                    $strCodEmpresa);
                if(is_array($arrayEstadosAbiertosSolAgregarEquipo) && count($arrayEstadosAbiertosSolAgregarEquipo) > 0)
                {
                    foreach($arrayEstadosAbiertosSolAgregarEquipo as $arrayEstadoAbiertoSolAgregarEquipo)
                    {   
                        $arrayEstadosVerificaSolAgregarEquipo[] = $arrayEstadoAbiertoSolAgregarEquipo['valor3'];
                    }
                }

                if(!isset($arrayEstadosVerificaSolAgregarEquipo) || empty($arrayEstadosVerificaSolAgregarEquipo))
                {
                    throw new \Exception("No se han podido encontrar los estados permitidos para una solicitud "
                                         ."de agregar equipo");
                }
                $objSolAgregarEquipoAbierta = $this->emComercial
                                                   ->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                   ->findOneBy(array('servicioId'       => $objServicio->getId(),
                                                                    'tipoSolicitudId'   => 
                                                                    $objTipoSolicitudAgregarEquipo->getId(),
                                                                    'estado'            => 
                                                                    $arrayEstadosVerificaSolAgregarEquipo));
                if(!is_object($objSolAgregarEquipoAbierta))
                {
                    throw new \Exception("No se ha podido obtener la solicitud de agregar equipo asociada al servicio");
                }

                $objCaractSolWdbExistente   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findOneBy(
                                                                    array(
                                                                        "detalleSolicitudId"=> $objSolAgregarEquipoAbierta,
                                                                        "caracteristicaId"  => $objCaractWifiDualBand,
                                                                        "estado"            => "Asignada"
                                                                        )
                                                                    );
                if(is_object($objCaractSolWdbExistente))
                {
                    $objCaractSolWdbExistente->setEstado('Eliminada');
                    $objCaractSolWdbExistente->setUsrUltMod($strUsrCreacion);
                    $objCaractSolWdbExistente->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objCaractSolWdbExistente);
                    $this->emComercial->flush();
                    
                    $objCaractSolWdbNueva = new InfoDetalleSolCaract();
                    $objCaractSolWdbNueva->setCaracteristicaId($objCaractWifiDualBand);
                    $objCaractSolWdbNueva->setDetalleSolicitudId($objSolAgregarEquipoAbierta);
                    $objCaractSolWdbNueva->setValor("SI");
                    $objCaractSolWdbNueva->setEstado("Asignada");
                    $objCaractSolWdbNueva->setUsrCreacion($strUsrCreacion);
                    $objCaractSolWdbNueva->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objCaractSolWdbNueva);
                    $this->emComercial->flush();
                }
                $objCaractSolElementoClienteExistente   = $this->emComercial
                                                               ->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                               ->findOneBy(
                                                                    array(
                                                                        "detalleSolicitudId"=> $objSolAgregarEquipoAbierta,
                                                                        "caracteristicaId"  => $objCaractElementoCliente,
                                                                        "estado"            => "Asignada"
                                                                        )
                                                                    );
                if(is_object($objCaractSolElementoClienteExistente))
                {
                    $objCaractSolElementoClienteExistente->setEstado('Eliminada');
                    $objCaractSolElementoClienteExistente->setUsrUltMod($strUsrCreacion);
                    $objCaractSolElementoClienteExistente->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objCaractSolElementoClienteExistente);
                    $this->emComercial->flush();
                    
                    $objCaractSolElementoClienteNueva = new InfoDetalleSolCaract();
                    $objCaractSolElementoClienteNueva->setCaracteristicaId($objCaractElementoCliente);
                    $objCaractSolElementoClienteNueva->setDetalleSolicitudId($objSolAgregarEquipoAbierta);
                    $objCaractSolElementoClienteNueva->setValor($objElementoCliente->getId());
                    $objCaractSolElementoClienteNueva->setEstado("Asignada");
                    $objCaractSolElementoClienteNueva->setUsrCreacion($strUsrCreacion);
                    $objCaractSolElementoClienteNueva->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($objCaractSolElementoClienteNueva);
                    $this->emComercial->flush();
                }
            }
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje,
                                "nuevoEstadoServicio"   => $strNuevoEstadoServicio);
        return $arrayRespuesta;
    }

    /**
     * callMircoServiciosWebService
     *
     * Función para llamar a los web services de microservicios
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0
     * @since 14-04-2020
     *
     * @param  Array $arrayPeticiones [ información requerida por el WS segun el tipo del método a ejecutar ]
     *
     * @return Array $arrayRespuesta  [ información que retorna el método llamado ]
     */
    public function callMircoServiciosWebService($arrayPeticiones)
    {
        $arrayRespuesta = $this->executeScript($arrayPeticiones);

        return $arrayRespuesta;
    }


    /**
     * executeScript
     *
     * Método que se encarga de realizar la conexión contra el WS de Cloudform
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0
     * @since 14-04-2020
     *
     * @param  Array $arrayParametros [ información requerida por el WS segun el tipo del método a ejecutar ]
     *
     * @return Array $arrayResultado [ información que retorna el método llamado ]
     */
    private function executeScript($arrayParametros)
    {
        $arrayResultado = array();

        //Se genera el json a enviar al ws por tipo de proceso a ejecutar
        $strDataString = $this->generateJson($arrayParametros);
        $strUrl        = $this->microServicioRestURL;

        if($this->ambienteEjecuta == "S")
        {
            //Se obtiene el resultado de la ejecucion via rest hacia el ws
            $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);

            $arrayResponseJson = $this->restClient->postJSON($strUrl, $strDataString , $arrayOptions);
            $arrayResponse     = json_decode($arrayResponseJson['result'],true);

            if($arrayResponseJson['status'] == static::$strStatusOK)
            {
                $arrayResultado['data']       = $arrayResponse['data'];
                $arrayResultado['mensaje']    = "Llamada realizada con exito!";
                $arrayResultado['status']     = "OK";
                $arrayResultado['statusCode'] = static::$strStatusOK;
            }
            else
            {
                $arrayResultado['status']     = "ERROR";
                $arrayResultado['statusCode'] = 500;
                $arrayResultado['data']       = "";

                if($arrayResponseJson['status'] == "0")
                {
                    $arrayResultado['mensaje'] = "No Existe Conectividad con el WS MircroServicio.";
                }
                else
                {
                    if(isset($arrayResponse['message']) && !empty($arrayResponse['message']))
                    {
                        $strMensajeError = $arrayResponse['message'];
                    }

                    $arrayResultado['mensaje'] = "Error en el WS del MicroServicio : ".$strMensajeError;
                }
            }
        }
        else
        {
            $arrayResultado['data']       = "";
            $arrayResultado['status']     = "ERROR";
            $arrayResultado['mensaje']    = "La variable de ambiente se encuentra desactivada";
            $arrayResultado['statusCode'] = static::$strStatusOK;
        }

        return $arrayResultado;
    }


    /**
     * generateJson
     *
     * Función encargada de generar el array a enviar para el consumo del Web Service Rest de un microServicio
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.0
     * @since 14-04-2020
     *
     * @param  Array $arrayParametros [ informacion requerida por el WS segun el tipo del metodo a ejecutar ]
     * @return json {   datos  : {arrayData},
     *                  op     : 'opcion',
     *                  fuente : {arrayDataAuditoria}
     *              }
     */
    private function generateJson($arrayParametros)
    {
        $arrayDataAuditoria = array
                                (
                                    'name'         => $arrayParametros['strName'],
                                    'originID'     => $arrayParametros['strOriginID'],
                                    'tipoOriginID' => $arrayParametros['strTipoOriginID']
                                );

        $arrayData = array();

        //Variable que contiene la opción de acuerdo al método a invocar en el WS
        $strOp = $arrayParametros['op'];

        //Opción que sirve para actualizar el responsable de una planificación
        if($strOp == 'UPDATE')
        {
            $arrayData = array(
                            'nombreAplicativo' => $arrayParametros['strNombreAplicativo'],
                            'numeroReferencia' => $arrayParametros['strNumeroReferencia'],
                            'ipCreacion'       => $arrayParametros['strIpCreacion'],
                            'origen'           => $arrayParametros['strOrigen'],
                            'usrCreacion'      => $arrayParametros['strUsrCreacion'],
                            'codEmpresa'       => $arrayParametros['strCodEmpresa'],
                            'prefijoEmpresa'   => $arrayParametros['strPrefijoEmpresa'],
                            'tipoAsignacion'   => $arrayParametros['strTipoAsignacion'],
                            'idAsignacion'     => $arrayParametros['strIdAsignacion']
                          );
        }

        $objJsonArray = array
                        (
                            "datos"  => $arrayData,
                            "op"     => $strOp,
                            "fuente" => $arrayDataAuditoria
                        );

        return json_encode($objJsonArray);
    }


    /**
     * Service para crear tarea(s) de asignación que corresponden a la planificación y re-planificación.
     * En este proceso se realiza el llamado a las funciones de HAL (notificarSeleccionSugerencia y confirmarSugerencia) 
     * para la asignación de cuadrillas a instalaciones.
     * 
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 28-11-2019
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 14-04-2020 - Se implementa llamada al microServicio que actualiza el responsable en el pedido.
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.2 28-08-2020 - Se consulta si se Requiere Flujo para seleccionar fecha de tarea.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.3 27-05-2021 - Se aumenta un parametro de retorno para devolver los datos de la cuadrilla que se
     *                           asigno por HAL
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.4 20-03-2023 - Se agrega validacion por prefijo empresa Ecuanet (EN) para guardar caracteristica,
     *                           notificacion por SMS. 
     */
    public function crearTareaAsignarPlanificacion($arrayParametrosTarea)
    {
        $intIdPerEmpRolSesion       = $arrayParametrosTarea['intIdPerEmpRolSesion'];
        $intIdFactibilidad          = $arrayParametrosTarea['intIdFactibilidad'];
        $intIdPersona               = $arrayParametrosTarea['intIdPersona'];
        $intIdPersonaEmpresaRol     = $arrayParametrosTarea['intIdPersonaEmpresaRol'];
        $strObservacionServicio     = $arrayParametrosTarea['strObservacionServicio'];
        $strObservacion             = $arrayParametrosTarea['strObservacion'];
        $arrayParametrosPer         = $arrayParametrosTarea['arrayParametrosPer'];
        $arrayParametrosHist        = $arrayParametrosTarea['arrayParametrosHist'];
        $intPersonaEmpresaRol       = $arrayParametrosTarea['intPersonaEmpresaRol'];
        $intIdEmpresa               = $arrayParametrosTarea['intIdEmpresa'];
        $strPrefijoEmpresa          = $arrayParametrosTarea['strPrefijoEmpresa'];
        $strCodEmpresa              = $arrayParametrosTarea['strCodEmpresa'];
        $strUsrCreacion             = $arrayParametrosTarea['strUsrCreacion'];
        $strIpCreacion              = $arrayParametrosTarea['strIpCreacion'];
        $strResponsableTrazabilidad = $arrayParametrosTarea['strResponsableTrazabilidad'];
        $intIdDetalleSolicitud      = $arrayParametrosTarea['id'];
        $objAdmiTarea               = $arrayParametrosTarea['entitytarea'];
        $strObservacionTecnico      = $arrayParametrosTarea['strObservacionTecnico'];
        $strSolucion                = $arrayParametrosTarea['strSolucion'];
        $strDatosTelefonia          = $arrayParametrosTarea['strDatosTelefonia'];
        $objInfoServicio            = $arrayParametrosTarea['objInfoServicio'];
        $strTipoSolicitud           = $arrayParametrosTarea['strTipoSolicitud'];
        $boolGuardo                 = $arrayParametrosTarea['boolGuardo'];
        $arrayParamRespon           = $arrayParametrosTarea['arrayParamRespon'];
        $objSolicitud               = $arrayParametrosTarea['objSolicitud'];
        $strEsHal                   = $arrayParametrosTarea['strEsHal'];
        $intIdSugerenciaHal         = $arrayParametrosTarea['intIdSugerenciaHal'];
        $strAtenderAntes            = $arrayParametrosTarea['strAtenderAntes'];
        $boolEsReplanificacionHal   = $arrayParametrosTarea['boolEsReplanifHal'];
        $intIdDetalleExistente      = $arrayParametrosTarea['intIdDetalleExistente'];
        $strMensajeWs               = "";
        $boolRequiereFlujoSim       = $arrayParametrosTarea['boolRequiereFlujoSim'];
        $intIdRequiereFlujo         = $arrayParametrosTarea['idFlujoFactibilidad'];
        $strEsGestionSimultanea     = $arrayParametrosTarea['strEsGestionSimultanea'];
        $arrayTareaIdSMS  = array(849);
        $strTareaNuevaSMS = 'N';
        $strTareaEnviaSMS = 'N';
        $strCedulaLiderCuadrilla = '';
        $strEsPlan = isset($arrayParametrosTarea['strEsPlan']) ? $arrayParametrosTarea['strEsPlan']:"N";
        $strTipoProceso = isset($arrayParametrosTarea['strTipoProceso']) ? $arrayParametrosTarea['strTipoProceso']:"";
        $objInfoPunto               = $arrayParametrosTarea['objInfoPunto'];
        $objInfoDetalleSolPlanif    = $arrayParametrosTarea['objInfoDetalleSolPlanif'];
        $arrayDatosSinPunto         = $arrayParametrosTarea['arrayDatosSinPunto'];
        $strObservacion             = $arrayParametrosTarea['strObservacion'];
        try
        {
            $strBand   = "";
            $strCodigo = "";
            //BUSCA INFO_DETALLE EXISTENTE: SI NO ES REPLANIFICACION HAL BUSCA POR IDSOLICITUD Y IDTAREA
            //POR EL CONTRARIO BUSCA CON EL ID DETALLE EXISTENTE RECIBIDO POR PARAMETRO  
            if ($strTipoSolicitud == 'SOLICITUD INSPECCION')
            {
                $objInfoDetalleE = null;
                if(!empty($intIdDetalleExistente))
                {
                    $objInfoDetalleE = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intIdDetalleExistente);
                }
            }
            else
            {
                if (!$boolEsReplanificacionHal)
                {
                    $objInfoDetalleE = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                                    ->getOneDetalleByDetalleSolicitudTarea($intIdDetalleSolicitud, $objAdmiTarea->getId());
                }
                else
                {
                    $objInfoDetalleE = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intIdDetalleExistente);
                }
            }
            //***********************************************************
            //INICIO - CONFIRMACION DE SELECCION DE SUGERENCIA A HAL >>>
            //***********************************************************

            if($strEsHal === 'S')
            {
                    // Array para el envio a hal
                    $arrayParametrosHal = array ('intIdDetalleSolicitud' => intval($intIdDetalleSolicitud),
                                                'intIdDetalle'           => intval($intIdDetalleExistente),
                                                'strEsInstalacion'       => 'S',
                                                'intIdSugerencia'        => $intIdSugerenciaHal,
                                                'intIdPersonaEmpresaRol' => $intIdPerEmpRolSesion,
                                                'boolConfirmar'          => true,
                                                'strUrl'                 => $this->container->getParameter('ws_hal_confirmaSugerencia'));
                
                // Establecemos la comunicación con hal
                $arrayRespuestaHal  = $this->serviceSoporte->getSolicitarConfirmarSugerenciasHal($arrayParametrosHal);
                // Validaciones de la respuesta de hal
                if (strtoupper($arrayRespuestaHal['mensaje']) == 'FAIL')
                {
                    $strMensajeError = "Ocurrio un error en el metodo PlanificarService.crearTareaAsignarPlanificacion()".
                                       " al ejecutar getSolcitarConfirmarSugerenciasHal(): ".$arrayRespuestaHal['descripcion'];
                    throw new \Exception($strMensajeError);
                }
                elseif (strtoupper($arrayRespuestaHal['result']['respuesta']) == 'FAIL')
                {
                    $strMensajeError = "Ocurrio un error en el metodo PlanificarService.crearTareaAsignarPlanificacion()".
                                       " al ejecutar getSolcitarConfirmarSugerenciasHal(): ".$arrayRespuestaHal['result']['mensaje'];
                    throw new \Exception($strMensajeError);
                }
                else
                {
                    $boolGuardo = true;

                }    

            }
            //***********************************************************
            //FIN - CONFIRMACIÓN DE SELECCIÓN DE SUGERENCIA A HAL <<<
            //***********************************************************
            //Se consulta si Requiere Flujo para seleccionar la fecha de la tarea principal
            if ($boolRequiereFlujoSim)
            {
                $objInfoDetalleSolHist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                        ->findLastDetalleSolicitudHistorial($intIdRequiereFlujo);
            }
            else
            {
                $objInfoDetalleSolHist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                        ->findLastDetalleSolicitudHistorial($intIdDetalleSolicitud);
            }
            $objDetalleAllSolHist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                      ->findAllSolicitudHistorial($objInfoDetalleSolHist[0]['id']);
            
            if(!empty($strSolucion))
            {
                $strObservacionTecnico .= '<br>' . $strSolucion;
            }

            /*
             * Detectamos si ya existe una tarea abierta y en caso de no estarlo seteamos a null la varaible
             * $objInfoDetalleE para crear un nuevo Detalle.
            */
            if (is_object($objInfoDetalleE))
            {
                $arrayTareaAbierta = $this->emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                     ->getTareaAbierta(array ('intIdDetalle'      => $objInfoDetalleE->getId(),
                                                                              'arrayEstadosTarea' => array('Finalizada','Cancelada','Anulada')));
                if (empty($arrayTareaAbierta) || empty($arrayTareaAbierta['result']))
                {
                    $objInfoDetalleE = null;
                }
            }

            //Si es solicitud de inspección obtiene datos del punto 
            //y setea a null la infoDetalle si no envia parametro de detalle existente
            $intIdPunto     = "";
            $strLoginPunto  = "";
            $strLatitud     = "";
            $strLongitud    = "";
            $strUsrVendedor = "";

            if ($strTipoSolicitud == 'SOLICITUD INSPECCION' && is_object($objInfoPunto))
            {

                $intIdPunto     = $objInfoPunto->getId();
                $strLoginPunto  = $objInfoPunto->getLogin();
                $strLatitud     = $objInfoPunto->getLatitud();
                $strLongitud    = $objInfoPunto->getLongitud();
                $strUsrVendedor = $objInfoPunto->getUsrVendedor();

                if(!$intIdDetalleExistente)
                {
                    $objInfoDetalleE = null;
                }
            }
            elseif($strTipoSolicitud == 'SOLICITUD INSPECCION' && !is_object($objInfoPunto))
            {
                $strLongitud    = $arrayDatosSinPunto['strLongitud'];
                $strLatitud     = $arrayDatosSinPunto['strLatitud'];
                $strUsrVendedor = $arrayDatosSinPunto['strUsrVendedor'];

                if(!$intIdDetalleExistente)
                {
                    $objInfoDetalleE = null;
                }
            }
            else
            {
                $intIdPunto     = $objInfoServicio->getPuntoId()->getId();
                $strLoginPunto  = $objInfoServicio->getPuntoId()->getLogin();
                $strLatitud     = $objInfoServicio->getPuntoId()->getLatitud();
                $strLongitud    = $objInfoServicio->getPuntoId()->getLongitud();
                $strUsrVendedor = $objInfoServicio->getPuntoId()->getUsrVendedor();
            }

            if($objInfoDetalleE != null)
            {
                $objInfoDetalle = $objInfoDetalleE;
                if ($objDetalleAllSolHist->getFeIniPlan() != null)
                {
                    $objInfoDetalle->setFeSolicitada($objDetalleAllSolHist->getFeIniPlan());
                }   
                $objInfoDetalle->setObservacion($strObservacionTecnico);
                $this->emSoporte->persist($objInfoDetalle);
                $this->emSoporte->flush();

                $strTareaEnviaSMS = 'S';
            }
            else
            {
                $objInfoDetalle = new InfoDetalle();
                $objInfoDetalle->setDetalleSolicitudId($intIdDetalleSolicitud);
                $objInfoDetalle->setTareaId($objAdmiTarea);
                $objInfoDetalle->setLongitud($strLongitud);
                $objInfoDetalle->setLatitud($strLatitud);
                $objInfoDetalle->setObservacion($strObservacionTecnico.$strDatosTelefonia);
                $objInfoDetalle->setPesoPresupuestado(0);
                $objInfoDetalle->setValorPresupuestado(0);
                $objInfoDetalle->setIpCreacion($strIpCreacion);
                $objInfoDetalle->setFeCreacion(new \DateTime('now'));
                $objInfoDetalle->setFeSolicitada($objDetalleAllSolHist->getFeIniPlan());
                $objInfoDetalle->setUsrCreacion($strUsrCreacion);

                $this->emSoporte->persist($objInfoDetalle);
                $this->emSoporte->flush();
                $strTareaNuevaSMS = 'S';
            }
            $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();
            $arrayParametrosHist["strObservacion"] = $strObservacion;

            if($strObservacionTecnico != "")
            {
                //Se ingresa el historial de la info_detalle
                $arrayParametrosHist["strObservacion"] = $strObservacionTecnico;
                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);
                
            }
            
            //****************************************************************************** */

            //Se obtiene la clase del documento de una notificación generada automáticamente por el sistema
            $objAdmiClaseDocumento = $this->emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")->find(24);
            //Se crea el número de la tarea y se la asocia al id_detalle
            $objInfoDocumento = new InfoDocumento();
            $objInfoDocumento->setMensaje("Tarea generada automáticamente por el sistema Telcos");
            $objInfoDocumento->setNombreDocumento("Registro de llamada.");
            $objInfoDocumento->setClaseDocumentoId($objAdmiClaseDocumento);
            $objInfoDocumento->setFeCreacion(new \DateTime('now'));
            $objInfoDocumento->setEstado("Activo");
            $objInfoDocumento->setUsrCreacion($strUsrCreacion);
            $objInfoDocumento->setIpCreacion($strIpCreacion);
            $objInfoDocumento->setEmpresaCod($strCodEmpresa);
            $this->emComunicacion->persist($objInfoDocumento);
            $this->emComunicacion->flush();


            //Validar que no exista una tarea ya creada para el idDetalle
            $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                ->findOneBy(array(
                                                    'detalleId' => $objInfoDetalle->getId(),
                                                    'estado'    => 'Activo'
                                                )
                                            );
            if($objInfoComunicacion == null)
            {
                //Se crea el número de la tarea y se la asocia al id_detalle, se setea la
                //forma de contacto tipo correo electrónico
                $objInfoComunicacion = new InfoComunicacion();
                $objInfoComunicacion->setFormaContactoId(5);
                $objInfoComunicacion->setRemitenteId($intIdPunto);
                $objInfoComunicacion->setRemitenteNombre($strLoginPunto);
                $objInfoComunicacion->setClaseComunicacion("Recibido");
                $objInfoComunicacion->setDetalleId($objInfoDetalle->getId());
                $objInfoComunicacion->setFechaComunicacion(new \DateTime('now'));
                $objInfoComunicacion->setEstado("Activo");
                $objInfoComunicacion->setFeCreacion(new \DateTime('now'));
                $objInfoComunicacion->setUsrCreacion($strUsrCreacion);
                $objInfoComunicacion->setIpCreacion($strIpCreacion);
                $objInfoComunicacion->setEmpresaCod($strCodEmpresa);
                $this->emComunicacion->persist($objInfoComunicacion);
                $this->emComunicacion->flush();

                $objInfoDocumentoComunicacion = new InfoDocumentoComunicacion();
                $objInfoDocumentoComunicacion->setComunicacionId($objInfoComunicacion);
                $objInfoDocumentoComunicacion->setDocumentoId($objInfoDocumento);
                $objInfoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                $objInfoDocumentoComunicacion->setEstado('Activo');
                $objInfoDocumentoComunicacion->setUsrCreacion($strUsrCreacion);
                $objInfoDocumentoComunicacion->setIpCreacion($strIpCreacion);
                $this->emComunicacion->persist($objInfoDocumentoComunicacion);
                $this->emComunicacion->flush();
            }

            if ($strEsHal === 'S')
            {
                $this->emSoporte->getConnection()->commit();
                $this->emComunicacion->getConnection()->commit();

                //INICIO - CONFIRMACIÓN DE ASIGNACIÓN AUTOMÁTICA A HAL >>>

                //En este proceso se obtiene el id de la cuadrilla que retorna HAL
                // Establecemos la comunicación con hal
                $arrayRespuestaAsignaHal = $this->serviceSoporte->procesoAutomaticoHalAsigna(array (
                    'intIdDetalle'           => intval($objInfoDetalle->getId()),
                    'intIdComunicacion'      => intval($objInfoComunicacion->getId()),
                    'intIdPersonaEmpresaRol' => intval($intIdPerEmpRolSesion) ,
                    'intIdSugerencia'        => intval($intIdSugerenciaHal),
                    'boolEresHal'            => true,
                    'strAtenderAntes'        => $strAtenderAntes,
                    'strSolicitante'         => 'NA',
                    'strUrl'                 => $this->container->getParameter('ws_hal_confirmaAsignacionAutHal')
                ));
                // Validamos si la comunicación o la respuesta de hal fueron invalidas,
                // caso contrario seguimos con el flujo
                if (strtoupper($arrayRespuestaAsignaHal['mensaje']) != 'OK' 
                    || strtoupper($arrayRespuestaAsignaHal['result']['respuesta']) != 'OK')
                {
                    $strMensajeError = 'procesoAutomaticoHalAsigna:'.
                    ' IdComunicacion: '.$objInfoComunicacion->getId().
                    ' IdDetalle: '.$objInfoDetalle->getId().
                    ' User: '.$strUsrCreacion.
                    ' IpUser: '.$strIpCreacion.
                    ' Descripcion: '.($arrayRespuestaAsignaHal['descripcion'] ?
                        $arrayRespuestaAsignaHal['descripcion'] :
                        $arrayRespuestaAsignaHal['result']['mensaje']);

                    throw new \Exception($strMensajeError);
                }
                else
                {
                    $boolGuardo = true;

                    // Obtenemos el resultado de hal
                    $arrayResult = $arrayRespuestaAsignaHal['result'];

                    $arrayRespuesta['arrayRespuestaAsignaHal'] = $arrayResult;

                    $objDateFechaSolicitada = date_create(date('Y-m-d H:i',
                        strtotime($arrayResult['fecha'].' '.$arrayResult['horaIni'])));

                    $objInfoDetalle->setFeSolicitada($objDateFechaSolicitada);
                    $this->emSoporte->persist($objInfoDetalle);
                    $this->emSoporte->flush();
                    $arrayRespuesta['fechaHal']   =  $arrayResult['fecha'];
                    $arrayRespuesta['horaIniHal'] =  $arrayResult['horaIni'];
                    $arrayRespuesta['horaFinHal'] =  $arrayResult['horaFin'];
                    
                    $arrayParamRespon = array(0 => $objAdmiTarea->getId().'@@'.'cuadrilla'.'@@'.$arrayResult['idAsignado'].'@@@@');
                    $arrayRespuesta['arrayResponsablesHal'] = $arrayParamRespon;
                }
                //FIN - CONFIRMACIÓN DE ASIGNACIÓN AUTOMÁTICA A HAL <<<
            }
            if ($strEsHal != "S" && (!isset($arrayParamRespon) || empty($arrayParamRespon) || empty($arrayParamRespon[0]))) 
            {
                $arrayResult = $arrayParametrosTarea['arrayRespuestaAsignaHal'];
                $arrayRespuesta['arrayRespuestaAsignaHal'] = $arrayResult;

                $objDateFechaSolicitada = date_create(date('Y-m-d H:i',
                strtotime($arrayResult['fecha'].' '.$arrayResult['horaIni'])));

                $objInfoDetalle->setFeSolicitada($objDateFechaSolicitada);
                $this->emSoporte->persist($objInfoDetalle);
                $this->emSoporte->flush();
                $arrayRespuesta['fechaHal']   =  $arrayResult['fecha'];
                $arrayRespuesta['horaIniHal'] =  $arrayResult['horaIni'];
                $arrayRespuesta['horaFinHal'] =  $arrayResult['horaFin'];

                $arrayParamRespon = array(0 => $objAdmiTarea->getId().'@@'.'cuadrilla'.'@@'.$arrayResult['idAsignado'].'@@@@');
            }
            foreach($arrayParamRespon as $arrayResponsables):
                $arrayVariablesR = explode("@@", $arrayResponsables);
                if($arrayVariablesR && count($arrayVariablesR) > 0 
                    && ($objAdmiTarea->getId() == $arrayVariablesR[0] || $strEsGestionSimultanea === "SI"))
                {
                    $strBand              = $arrayVariablesR[1];
                    $strCodigo            = $arrayVariablesR[2];
                    $strCodigoAsignado    = $this->retornaEscogidoResponsable($strBand, $strCodigo, "codigo", $intIdEmpresa);
                    $strNombreAsignado    = $this->retornaEscogidoResponsable($strBand, $strCodigo, "nombre", $intIdEmpresa);
                    $strRefCodigoAsignado = $this->retornaEscogidoResponsable($strBand, $strCodigo, "ref_codigo", $intIdEmpresa);
                    $strRefNombreAsignado = $this->retornaEscogidoResponsable($strBand, $strCodigo, "ref_nombre", $intIdEmpresa);
                    if($strRefCodigoAsignado)
                    {
                        $arrayParametrosPer["strCodigoEmpresa"] = $intIdEmpresa;
                        $arrayParametrosPer["intPersonaId"]     = $strRefCodigoAsignado;
                        $intPersonaEmpresaRol                   = $this->emComercial
                                ->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->getPersonaEmpresaRolPorIdPersonaYEmpresa($arrayParametrosPer);
                    }
                    if($strBand == 'cuadrilla')
                    {
                        $intIdPersona           = $arrayVariablesR[3];
                        $intIdPersonaEmpresaRol = $arrayVariablesR[4];
                    }
                }
            endforeach;
            //********* GRABA -- DETALLE ASIGNACION **************
            $objDetalleAsignacion = new InfoDetalleAsignacion();
            $objDetalleAsignacion->setDetalleId($objInfoDetalle);
            $objDetalleAsignacion->setAsignadoId($strCodigoAsignado);
            if($strNombreAsignado)
            {
                $objDetalleAsignacion->setAsignadoNombre($strNombreAsignado);
            }

            if($strBand == 'cuadrilla')
            {
                $arrayIntegrantesCuadrilla = $this->emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                        ->getIntegrantesCuadrilla($strCodigoAsignado);

                if($intIdPersona != 0)
                {
                    $objPersona = $this->emComercial->getRepository('schemaBundle:InfoPersona')->find($intIdPersona);
                    $objDetalleAsignacion->setRefAsignadoId($intIdPersona);
                    $objDetalleAsignacion->setRefAsignadoNombre($objPersona->__toString());
                }
                else
                {
                    $arrayDatos = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findJefeCuadrilla($strCodigoAsignado);
                    if($arrayDatos['idPersona'] != '')
                    {
                        $objDetalleAsignacion->setRefAsignadoId($arrayDatos['idPersona']);
                        $objDetalleAsignacion->setRefAsignadoNombre($arrayDatos['nombres']);
                        $objDetalleAsignacion->setPersonaEmpresaRolId($arrayDatos['idPersonaEmpresaRol']);
                        $strPersonaEmpresaRol = $arrayDatos['idPersonaEmpresaRol'];

                        $objInfoPersonaEmpresaRol = $this->emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                ->find($arrayDatos['idPersonaEmpresaRol']);
                    }
                    else
                    {
                        if(count($arrayIntegrantesCuadrilla) > 0)
                        {
                            $objDetalleAsignacion->setRefAsignadoId($arrayIntegrantesCuadrilla[0]['idPersona']);
                            $objDetalleAsignacion->setRefAsignadoNombre($arrayIntegrantesCuadrilla[0]['nombres'] . ' ' .
                                    $arrayIntegrantesCuadrilla[0]['apellidos']);
                            $objDetalleAsignacion->setPersonaEmpresaRolId($arrayIntegrantesCuadrilla[0]['empresaRolId']);
                            $strPersonaEmpresaRol = $arrayIntegrantesCuadrilla[0]['empresaRolId'];
                        }
                    }
                }

                if($intIdPersonaEmpresaRol != 0)
                {
                    $objDetalleAsignacion->setPersonaEmpresaRolId($intIdPersonaEmpresaRol);
                    $strPersonaEmpresaRol = $intIdPersonaEmpresaRol;
                    $objInfoPersonaEmpresaRol = $this->emSoporte->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                ->find($intIdPersonaEmpresaRol);
                }
            }
            else
            {
                $objDetalleAsignacion->setRefAsignadoId($strRefCodigoAsignado);
                $objDetalleAsignacion->setRefAsignadoNombre($strRefNombreAsignado);
                $objDetalleAsignacion->setPersonaEmpresaRolId($intPersonaEmpresaRol);
                $strPersonaEmpresaRol = $intPersonaEmpresaRol;
            }
            $objDetalleAsignacion->setIpCreacion($strIpCreacion);
            $objDetalleAsignacion->setFeCreacion(new \DateTime('now'));
            $objDetalleAsignacion->setUsrCreacion($strUsrCreacion);
            /* Se determina tipo de asignado CUADRILLA, EMPLEADO O CONTRATISTA Y OBTIENE OBSERVACION DEL SERVICIO*/
            $strTipoAsignado = strtoupper($strBand);
            if($strTipoAsignado)
            {
                $objDetalleAsignacion->setTipoAsignado($strTipoAsignado);
                if($strTipoAsignado == "CUADRILLA")
                {
                    $strObservacionServicio  .= "<br>Asignada a: Cuadrilla";
                    $objCuadrilla            = $this->emComercial->getRepository("schemaBundle:AdmiCuadrilla")
                                                                ->find($objDetalleAsignacion->getAsignadoId());
                    $strObservacionServicio  .= "<br>Nombre: " . $objCuadrilla->getNombreCuadrilla();
                    $arrayDatos              = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                                ->findJefeCuadrilla($objDetalleAsignacion->getAsignadoId());
                    $strNombreLiderCuadrilla = "N/A";                    
                    if($arrayDatos)
                    {
                        $strNombreLiderCuadrilla = $arrayDatos['nombres'];
                        $strCedulaLiderCuadrilla = $arrayDatos['cedulaLider'];
                    }
                    $strObservacionServicio .= "<br>L&iacute;der de Cuadrilla: " . $strNombreLiderCuadrilla;
                    if (($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN") && 
                        ($strEsHal != 'S'))
                    {
                        $objCupos  = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                        ->findBy(array("solicitudId" => $intIdFactibilidad));

                        foreach($objCupos as $objCupo)
                        {
                            $objCupoPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                                        ->find($objCupo->getId());
                            $objCupoPlanificacion->setCuadrillaId($objCuadrilla->getId());
                            $this->emComercial->persist($objCupoPlanificacion);
                            $this->emComercial->flush();
                        }
                        
                    }
                }
                else if($strTipoAsignado == "EMPLEADO")
                {
                    $strObservacionServicio .= "<br>Asignada a: Empleado";
                    $strObservacionServicio .= "<br>Nombre: " . $objDetalleAsignacion->getRefAsignadoNombre();
                    $strObservacionServicio .= "<br>Departamento: " . $objDetalleAsignacion->getAsignadoNombre();
                }
                else if($strTipoAsignado == "EMPRESAEXTERNA")
                {
                    $strObservacionServicio .= "<br>Asignada a: Contratista";
                    $strObservacionServicio .= "<br>Nombre: " . $objDetalleAsignacion->getAsignadoNombre();
                }
                else
                {
                    $strObservacionServicio = "";
                }
                $strObservacionServicio .= "<br><br>";
            }
            //Graba la asignación de la cuadrilla solo cuando no es replanificación
            if(!$boolEsReplanificacionHal)
            {
                $this->emSoporte->persist($objDetalleAsignacion);
                $this->emSoporte->flush();
            }

            //*****************Se llama al web service de MicroServicio: para realizar la actualizacion del responsable****************//

            //Se obtiene el Id de la cotizacion por servicio
            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);
            if(is_object($objDetalleSolicitud))
            {
                $objServicio = $objDetalleSolicitud->getServicioId();

                if(is_object($objServicio))
                {
                    //Se consulta la caracteristica
                    $objCaractCotizacion = $this->serviceGeneral->getServicioProductoCaracteristica($objServicio,
                                                                                                   'COTIZACION_PRODUCTOS',
                                                                                                    $objServicio->getProductoId());

                    if(is_object($objCaractCotizacion))
                    {
                        $intIdCotizacion = $objCaractCotizacion->getValor();

                        //Si la cotización existe se llama al web service del microservicio
                        if(!empty($intIdCotizacion))
                        {
                            $arrayParametrosWS = array();
                            $arrayRespuestaWS  = array();

                            $arrayParametrosWS['strName']             = "Planificacion_Servicios";
                            $arrayParametrosWS['strOriginID']         = $strIpCreacion;
                            $arrayParametrosWS['strTipoOriginID']     = "IP";
                            $arrayParametrosWS['strNombreAplicativo'] = "TELCOS";
                            $arrayParametrosWS['strNumeroReferencia'] = $intIdCotizacion;
                            $arrayParametrosWS['strIpCreacion']       = $strIpCreacion;
                            $arrayParametrosWS['strOrigen']           = "N";
                            $arrayParametrosWS['strUsrCreacion']      = $strUsrCreacion;
                            $arrayParametrosWS['strCodEmpresa']       = $strCodEmpresa;
                            $arrayParametrosWS['strPrefijoEmpresa']   = $strPrefijoEmpresa;
                            $arrayParametrosWS['strTipoAsignacion']   = $strTipoAsignado;
                            $arrayParametrosWS['strIdAsignacion']     = $strPersonaEmpresaRol;
                            $arrayParametrosWS['op']                  = 'UPDATE';

                            $arrayRespuestaWS   = $this->callMircoServiciosWebService($arrayParametrosWS);
                            $arrayData          = $arrayRespuestaWS["data"];
                            $arrayRespuestaData = $arrayData["data"];
                            $intCodError        = $arrayData["codError"];
                            $strNumeroPedido    = $arrayRespuestaData[0]["numeroPedido"];

                            if($arrayRespuestaWS["status"] === "OK")
                            {
                                if($intCodError === 200)
                                {
                                    $strMensajeWs = " - El pedido: <b>".$strNumeroPedido."</b> fue actualizado exitosamente.";
                                }
                                if($intCodError === 500)
                                {
                                    $strMensajeWs = " - El estado del pedido: <b>".$strNumeroPedido."</b> no fue actualizado, debe ser ingresado.";
                                }
                            }
                            else
                            {
                                $strMensajeWs = " - ".$arrayRespuestaWS["mensaje"];
                            }
                        }
                    }
                }
            }
            //****************Se llama al web service de MicroServicio: para realizar la actualizacion del responsable****************//
            //Se calcula el responsable de la trazabilidad de coordinar retiro de equipo
            $objPersonaEmpresaRolUsr = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                            ->find($strPersonaEmpresaRol);
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
            //Se realiza el ingreso de la trazabilidad de los elementos a retirar
            if($strTipoSolicitud == "solicitud retiro equipo")
            {
                $objInfoDetalleSolCaract = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findBy(array("detalleSolicitudId" => $intIdFactibilidad,
                                                                            "caracteristicaId"   => 360));

                foreach($objInfoDetalleSolCaract as $objInfoDetalleSolCarac)
                {
                    //Se obtiene el número de la serie del elemento
                    $objInfoElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                                ->find($objInfoDetalleSolCarac->getValor());

                    $objInfoElementoTrazabilidad = new InfoElementoTrazabilidad();

                    if(is_object($objInfoElemento))
                    {
                        $objInfoElementoTrazabilidad->setNumeroSerie($objInfoElemento->getSerieFisica());
                    }

                    //Se obtiene el login asociado
                    if(is_object($objInfoServicio))
                    {
                        $strLoginTrazabilidad = $objInfoServicio->getPuntoId()->getLogin();
                    }

                    $objInfoElementoTrazabilidad->setEstadoTelcos("Eliminado");
                    $objInfoElementoTrazabilidad->setEstadoNaf("Instalado");
                    $objInfoElementoTrazabilidad->setEstadoActivo("Eliminado");
                    $objInfoElementoTrazabilidad->setUbicacion("Cliente");
                    $objInfoElementoTrazabilidad->setLogin($strLoginTrazabilidad);
                    $objInfoElementoTrazabilidad->setCodEmpresa($intIdEmpresa);
                    $objInfoElementoTrazabilidad->setTransaccion("Coordinar Retiro Equipo");
                    $objInfoElementoTrazabilidad->setObservacion("Coordinar Retiro Equipo");
                    $objInfoElementoTrazabilidad->setOficinaId(0);
                    $objInfoElementoTrazabilidad->setResponsable($strResponsableTrazabilidad);
                    $objInfoElementoTrazabilidad->setUsrCreacion($strUsrCreacion);
                    $objInfoElementoTrazabilidad->setFeCreacion(new \DateTime('now'));
                    $objInfoElementoTrazabilidad->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objInfoElementoTrazabilidad);
                    $this->emInfraestructura->flush();
                }
            }
            if(!$boolEsReplanificacionHal)
            {
                if($strBand == 'cuadrilla')
                {
                    //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                    $objCuadrillaTarea = $this->emComercial->getRepository('schemaBundle:InfoCuadrillaTarea')
                            ->getIntegrantesCuadrilla($strCodigoAsignado);

                    foreach($objCuadrillaTarea as $objDatoCuadrilla)
                    {
                        $objInfoCuadrillaTarea = new InfoCuadrillaTarea();
                        $objInfoCuadrillaTarea->setDetalleId($objInfoDetalle);
                        $objInfoCuadrillaTarea->setCuadrillaId($strCodigoAsignado);
                        $objInfoCuadrillaTarea->setPersonaId($objDatoCuadrilla['idPersona']);
                        $objInfoCuadrillaTarea->setUsrCreacion($strUsrCreacion);
                        $objInfoCuadrillaTarea->setFeCreacion(new \DateTime('now'));
                        $objInfoCuadrillaTarea->setIpCreacion($strIpCreacion);
                        $this->emSoporte->persist($objInfoCuadrillaTarea);
                        $this->emSoporte->flush();
                    }
                //*********************INGRESO DE INTEGRANTES DE CUADRILLA**********************
                }

                //Se ingresa el historial de la info_detalle
                if(is_object($objInfoDetalle))
                {
                    $arrayParametrosHist["intDetalleId"] = $objInfoDetalle->getId();
                }
                $arrayParametrosHist["strObservacion"] = "Tarea Asignada";
                $arrayParametrosHist["strOpcion"]      = "Historial";
                $arrayParametrosHist["strAccion"]      = "Asignada";

                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                $strAsignacion = $strRefNombreAsignado ? $strRefNombreAsignado : $strNombreAsignado;

                //Se ingresa el historial de la info_detalle
                $arrayParametrosHist["strObservacion"] = "Tarea Asignada a " . $strAsignacion;
                $arrayParametrosHist["strOpcion"]      = "Seguimiento";

                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                if($objDetalleAsignacion != null && $strBand == "cuadrilla")
                {
                    $objData = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                ->findByCuadrillaId($strCodigoAsignado);
                    if($objData && count($objData) > 0)
                    {
                        foreach($objData as $strKeyPersona => $entityPersona)
                        {
                            $strColRefCodigoAsignado = $entityPersona->getPersonaId()->getId();
                            $strColRefNombreAsignado = $entityPersona->getPersonaId()->getNombres() . " " .
                                    $entityPersona->getPersonaId()->getApellidos();

                            $strColCodigoAsignado = $this->retornaEscogidoResponsable("empleado", 
                                                                                        $strColRefCodigoAsignado, "codigo",$intIdEmpresa);
                            $strColNombreAsignado = $this->retornaEscogidoResponsable("empleado", 
                                                                                        $strColRefCodigoAsignado, "nombre",$intIdEmpresa);

                            if($strColNombreAsignado)
                            {
                                //********* SAVE -- DETALLE COLABORADOR**************
                                $objDetalleColaborador = new InfoDetalleColaborador();
                                $objDetalleColaborador->setDetalleAsignacionId($objDetalleAsignacion);
                                $objDetalleColaborador->setAsignadoId($strColCodigoAsignado);
                                $objDetalleColaborador->setAsignadoNombre($strColNombreAsignado);
                                $objDetalleColaborador->setRefAsignadoId($strColRefCodigoAsignado);
                                $objDetalleColaborador->setRefAsignadoNombre($strColRefNombreAsignado);
                                $objDetalleColaborador->setIpCreacion($strIpCreacion);
                                $objDetalleColaborador->setFeCreacion(new \DateTime('now'));
                                $objDetalleColaborador->setUsrCreacion($strUsrCreacion);

                                $this->emSoporte->persist($objDetalleColaborador);
                                $this->emSoporte->flush();
                            }
                        }
                    }
                }
            }
            if(!is_object($objDetalleAsignacion) && !$boolEsReplanificacionHal)
            {
                $boolGuardo = false;
            }
            elseif(is_object($objDetalleAsignacion) && !$boolEsReplanificacionHal)
            {
                //------- COMUNICACIONES --- NOTIFICACIONES
                if($objInfoDetalleE != null)
                {
                    $objEDetalleAsignacion = $this->emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                             ->getUltimaAsignacion($objInfoDetalleE->getId());
                    $strAsunto             = "Reasignacion de Tarea " . $objAdmiTarea->getNombreTarea();
                }
                else
                {
                    $objEDetalleAsignacion = null;
                    $strAsunto             = "Asignación de Tarea " . $objAdmiTarea->getNombreTarea();                    
                }

                if (strtoupper($strTipoSolicitud) == 'SOLICITUD INSPECCION')
                {
                    $strMensaje = $this->templating->render('planificacionBundle:AsignarResponsable:notificacionTareaInspeccion.html.twig', 
                                                            array(
                                                                        'detalleSolicitudPlanif'    => $objInfoDetalleSolPlanif,
                                                                        'punto'                     => $objInfoPunto,
                                                                        'infoNoCliente'             => $arrayDatosSinPunto,
                                                                        'tarea'                     => $objAdmiTarea,
                                                                        'detalleAsignacion'         => $objDetalleAsignacion,
                                                                        'detalleAsignacionAnterior' => $objEDetalleAsignacion,
                                                                        'numeroTarea'               => $objInfoComunicacion->getId())
                                                            );
                }
                else
                {
                    $strMensaje = $this->templating->render('planificacionBundle:AsignarResponsable:notificacion.html.twig', 
                                                            array(
                                                                        'detalleSolicitud'          => $objSolicitud,
                                                                        'tarea'                     => $objAdmiTarea,
                                                                        'detalleAsignacion'         => $objDetalleAsignacion,
                                                                        'detalleAsignacionAnterior' => $objEDetalleAsignacion,
                                                                        'numeroTarea'               => $objInfoComunicacion->getId())
                                                            );
                }

                //DESTINATARIOS....
                $arrayFormasContacto = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                         ->getContactosByLoginPersonaAndFormaContacto
                                                             (
                                                                 $strUsrVendedor,
                                                                 'Correo Electronico'
                                                             );

                $arrayTo   = array();
                $arrayTo[] = 'notificaciones_telcos@telconet.ec';

                if($arrayFormasContacto)
                {
                    foreach($arrayFormasContacto as $arrayFormaContacto)
                    {
                        $arrayTo[] = $arrayFormaContacto['valor'];
                    }
                }
                //Si es TN se verifica los alias del asignado y se copia la notificación al departamento
                if($strPrefijoEmpresa == "TN")
                {
                    if($strBand == 'cuadrilla' && $objInfoPersonaEmpresaRol)
                    {
                        $strCodigoAsignado = $objInfoPersonaEmpresaRol->getDepartamentoId();
                    }
                    $objAdmiAlias = $this->emComunicacion->getRepository('schemaBundle:AdmiAlias')
                            ->findBy(array(
                        "departamentoId" => $strCodigoAsignado,
                        "estado"         => "Activo",
                        "empresaCod"     => $intIdEmpresa));
                    if($objAdmiAlias)
                    {
                        foreach($objAdmiAlias as $alias)
                        {
                            $arrayTo[] = $alias->getValor();
                        }
                    }
                }
                /* @var $envioPlantilla EnvioPlantilla */
                $this->envioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strMensaje);
            }

            if (in_array($objAdmiTarea->getId(),$arrayTareaIdSMS) 
               && ($strPrefijoEmpresa == "MD" || $strPrefijoEmpresa == "EN")
               && $strEsPlan == "S" && $strTipoProceso == 'Programar'
               && ($strEsHal === 'S' || $strBand == "cuadrilla") )
            {   
                $arrayRespuesta['cedulaTecnico'] = $strCedulaLiderCuadrilla;
                $arrayRespuesta['nombreTecnico'] = $strNombreLiderCuadrilla;
                $arrayRespuesta['enviaSMS'] = 'S';    
                        
                if ($strTareaNuevaSMS =='S')
                {
                    $arrayRespuesta['codigoTrabajo'] = substr(str_shuffle("123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
                    $this->serviceSoporte->guardarTareaCaracteristica(array (
                        'strDescripcionCaracteristica' => 'CODIGO_TRABAJO',
                        'intComunicacionId'            => $objInfoComunicacion->getId(),
                        'idDetalle'                    => $objInfoDetalle->getId(),
                        'strUsrCreacion'               => $strUsrCreacion,
                        'strIpCreacion'                => $strIpCreacion,
                        'strCodigoTrabajo'             => $arrayRespuesta['codigoTrabajo']
                    ));
                }
                elseif($strTareaEnviaSMS =='S')
                {
                    $arrayRespuesta['codigoTrabajo'] = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                                                       ->getCodigoTrabajoTarea(array('idDetalle'=> $objInfoDetalle->getId()));
                }
            }

            $arrayRespuesta['mensaje']                      = 'OK';
            $arrayRespuesta['boolGuardo']                   = $boolGuardo;
            $arrayRespuesta['arrayParametrosHist']          = $arrayParametrosHist;
        }
        catch(\Exception $objError)
        {
            $arrayRespuesta['mensaje']    = $objError->getMessage();
            $arrayRespuesta['boolGuardo'] = false;
        }
        $arrayRespuesta['strMensajeWs']                 = $strMensajeWs;
        $arrayRespuesta['objInfoDetalle']               = $objInfoDetalle;
        $arrayRespuesta['objInfoDocumentoComunicacion'] = $objInfoDocumentoComunicacion;
        $arrayRespuesta['objInfoComunicacion']          = $objInfoComunicacion;
        $arrayRespuesta['objInfoDocumento']             = $objInfoDocumento;
        $arrayRespuesta['observacionServicio']          = $strObservacionServicio;
        return $arrayRespuesta;
    }


    

    /*
     * retornaEscogidoResponsable
     * 
     * ************** FUNCTION QUE RETORNA EL CODIGO Y EL NOMBRE DEL ASIGNADO -- ESCOGIDO DEL COMBO
     * 
     * Funcion que retorna los datos de los integrantes de un asignado a solicitud
     * @author: Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 05-Feb-2018
     * 
     * @author: Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 2018-12-03   Se agrega parámetro empresa por proyecto TNP
     * @since 1.0
     */
    private function retornaEscogidoResponsable($strBand, $strCodigo, $strRetorna, $strEmpresaCod)
    {
        $strRetornaValor = '';
        if (!empty($strEmpresaCod))
        {
            $strCodEmpresa = $strEmpresaCod;
        }
        else
        {
            $strCodEmpresa = '10';
        }
        if($strRetorna == "codigo" || $strRetorna == "nombre")
        {
            $strCodigoAsignado = 0;
            $strNombreAsignado = "";

            if($strBand == "empleado")
            {
                $arrayDepartamento = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                        ->getDepartamentoByEmpleado($strCodEmpresa, $strCodigo);
                $strCodigoAsignado = $arrayDepartamento["id_departamento"];
                $strNombreAsignado = $arrayDepartamento["nombre_departamento"];
            }
            if($strBand == "empresaExterna")
            {
                $strRetornaValor = '';
                if($strRetorna == "codigo")
                {
                    $strRetornaValor =  $strCodigo;
                }
                else
                {
                    $entityPersona     = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($strCodigo);
                    $strCodigoAsignado = $strCodigo;
                    $strNombreAsignado = sprintf("%s", $entityPersona);
                    $strRetornaValor   = $strNombreAsignado;
                }
                return $strRetornaValor;
            }
            else if($strBand == "cuadrilla")
            {
                $strRetornaValor = '';
                if($strRetorna == "codigo")
                {
                    $strRetornaValor = $strCodigo;
                }
                else
                {
                    $entityCuadrilla   = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')->findOneById($strCodigo);
                    $strCodigoAsignado = $strCodigo;
                    $strNombreAsignado = $entityCuadrilla->getNombreCuadrilla();
                    $strRetornaValor   =  $strNombreAsignado;
                }
                return $strRetornaValor;
            }
            else
            {
                $strCodigo = '';
            }
            if($strRetorna == "codigo")
            {
                return $strCodigoAsignado;
            }
            else if($strRetorna == "nombre")
            {
                return $strNombreAsignado;
            }
            else
            {
                return false;
            }
        }
        else if($strRetorna == "ref_codigo" || $strRetorna == "ref_nombre")
        {
            $strRefCodigoAsignado = 0;
            $strRefNombreAsignado = "";

            if($strBand == "empleado")
            {
                if($strRetorna == "ref_codigo")
                {
                    return $strCodigo;
                }
                $entityPersona        = $this->emComercial->getRepository('schemaBundle:InfoPersona')->findOneById($strCodigo);
                $strRefNombreAsignado = sprintf("%s", $entityPersona);
                return $strRefNombreAsignado;
            }

            if($strRetorna == "ref_codigo")
            {
                return $strRefCodigoAsignado;
            }
            else if($strRetorna == "ref_nombre")
            {
                return $strRefNombreAsignado;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $strRefCodigoAsignado = 0;
        }
    }

    /**
     *  Metodo utilizado para cargar el horario en la agenda de planificacion en linea
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.0 02-02-2018
     * 
     *  Actualizacion .- Se procede a traer cupos separando mobile de web
     *
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.1 23-05-2018
     * 
     * Actualizacion .- Se procede a cargar los cupos en base a la agenda establecida por fecha y jurisdiccion
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.2 08-06-2018
     * 
     * Actualizacion .- Se procede a cargar los cupos en base a la agenda y cantidad de cupos mobiles
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 1.3 02-07-2018 
     * 
     * @param arrayParametros
     * @return array
     *
     */
    public function getCuposMobil($arrayParametros)
    {
        $boolCupoMobile = true;
        $intHora = date("H");
        $intIniDia = 1;
        $intDias       = $this->container->getParameter('planificacion.mobile.dias');
        $intHoraCierre = $this->container->getParameter('planificacion.mobile.hora_cierre');    
        $intDias++;
        if ($intHora > $intHoraCierre)
        {
            $intDias++;
            $intIniDia++;

        }

        $arrayRespuesta = array();
        $i              = 0;
        $objFecha       = new \DateTime('now');
        $strFecha       = $objFecha->format('Y-m-d');     
        $objFechaFin    = date("Y-m-d", strtotime($strFecha . "+ $intDias days"));
        $arrayIntervalo = $this->emComercial
                ->getRepository('schemaBundle:AdmiPlantillaHorarioDet')
                ->getRegistrosParaMobile($arrayParametros);        
        $intCont        = count($arrayIntervalo);        
        if (date("w") >= 4 && date("w") <=5)
        {
         $intDias += 2;             
        }                 
        while($i < $intCont)
        {
            $strHoraFormato = $arrayIntervalo[$i]['horaInicio']->format('H:i') . ' - ' . $arrayIntervalo[$i]['horaFin']->format('H:i');
            $objFecha       = new \DateTime('now');
            $strFecha       = $objFecha->format('Y-m-d');             
   
            $strFecha       = date("Y-m-d", strtotime($strFecha . "+ $intIniDia days"));
            $objFecha       = new \DateTime($strFecha);
            $arrayDias      = array();
            while($objFecha->format("Y-m-d") <= $objFechaFin)
            {
                $arrayHoraInicio = $arrayIntervalo[$i]['horaInicio'];
                while($objFecha->format("Y-m-d") <= $objFechaFin && $arrayHoraInicio == $arrayIntervalo[$i]['horaInicio'])
                {
                    
                    $arrayCuadrilla = array();
                    $intCupo        = 0;
                    $strNombreDia      = $this->nombreDia($objFecha->format("w"));
                    $strFechaPar = $strFecha . " " . $arrayIntervalo[$i]['horaInicio']->format('H:i');
                    $strFechaPar = substr($strFechaPar, 0, -1);
                    $strFechaPar .= "1";
                    $strFechaPar = str_replace("-", "/", $strFechaPar);
                    
                    $strFechaPar2 = $strFecha . " " . $arrayIntervalo[$i]['horaInicio']->format('H:i');
                    $strFechaPar2 = str_replace("-", "/", $strFechaPar2);
                    
                    $arrayPar    = array(
                        "strFecha" => $strFechaPar,
                        "intJurisdiccion" => $arrayParametros['intJurisdiccionId']);
                    
                    if ($boolCupoMobile)
                    {
                        $arrayCount  = $this->emComercial
                                ->getRepository('schemaBundle:InfoCupoPlanificacion')
                                ->getCountOcupados($arrayPar);
                    }
                    else
                    {
                        $arrayCount  = $this->emComercial
                            ->getRepository('schemaBundle:InfoCupoPlanificacion')
                            ->getCountDisponibles($arrayPar);
                    }
                    $arrayParAgenda = array("strFechaDesde" => $strFechaPar2,
                                            "intJurisdiccionId" => $arrayParametros['intJurisdiccionId']);                    
                    $entityAgendaDetalle  = $this->emComercial
                         ->getRepository('schemaBundle:InfoAgendaCupoDet')
                         ->getDetalleAgenda($arrayParAgenda);        

                    $intCupoMobile = 0;
                    foreach ($entityAgendaDetalle['registros'] as $entityDet)
                    {
                      $intCupoMobile += $entityDet->getCuposMovil();
                    }
                    while($objFecha->format("Y-m-d") <= $objFechaFin &&
                    $arrayHoraInicio == $arrayIntervalo[$i]['horaInicio'] &&
                    $intCupo <= $intCupoMobile)
                    {
                        $strColorFondo      = '#2E86C1';
                        $strColorLetra      = '#FFFFFF';
                        $strNombreCuadrilla = "";
                        if ($boolCupoMobile)
                        {
                            $intDisponible      = 1;
                            $intCuantos = $arrayCount['CUANTOS'];
                            if($intCupo > $intCuantos)
                            {
                                $intDisponible = 0;
                            }
                        }
                        else
                        {
                            $intDisponible      = 0;
                            $intCuantos = $arrayCount[0]['CUANTOS'];
                            if($intCupo > $intCuantos)
                            {
                                $intDisponible = 1;
                            }
                        }
                        $intCupo++;

                        if ($objFecha->format("w") > 0 && $objFecha->format("w") < 6)
                        {

                            $arrayCuadrilla[] = array(
                                "cuadrillaId"     => $intCupo,
                                "nombreCuadrilla" => $strNombreCuadrilla,
                                "colorFondo"      => $strColorFondo,
                                "colorLetra"      => $strColorLetra,
                                "detalleAgendaId" => $intDisponible,
                                "liderCuadrilla"  => "",
                                "fechaInicio"     => $strFecha . "T" . $arrayIntervalo[$i]['horaInicio']->format('H:i:s'),
                                "fechaFin"        => $strFecha . "T" . $arrayIntervalo[$i]['horaFin']->format('H:i:s')
                            );                            
                        }
                    }
                    if ($objFecha->format("w") > 0 && $objFecha->format("w") < 6 )
                    {  
                        if (!((date('w') == 0 || date('w') == 6) && $objFecha->format("w") == 1)) //sabado y domingo dia del contrato
                        {
                            $arrayDias[] = array(
                                "descripcionDia"        => $strNombreDia,
                                "fechaDia"              => $strFecha,
                                "intervaloProgramacion" => $strHoraFormato,
                                "estadoDia"             => "A",
                                "cuadrillaProgramacion" => $arrayCuadrilla
                            );
                        }
                    }
                    $strFecha    = $objFecha->format('Y-m-d');
                    $strFecha    = date("Y-m-d", strtotime($strFecha . "+ 1 days"));
                    $objFecha    = new \DateTime($strFecha);
                }
            }
            $arrayRespuesta[] = array(
                "intervaloProgramacion" => $strHoraFormato,
                "planificacionHorarios" => $arrayDias,
                "estadoProgramacion"    => "D");
            $i++;
        }
        return $arrayRespuesta;
    }
    public function nombreDia($intDia)
    {
        $strDia = "";
        switch($intDia)
        {
            case 0:
                $strDia = "Domingo";
                break;
            case 1:
                $strDia = "Lunes";
                break;
            case 2:
                $strDia = "Martes";
                break;
            case 3:
                $strDia = "Miercoles";
                break;
            case 4:
                $strDia = "Jueves";
                break;
            case 5:
                $strDia = "Viernes";
                break;
            case 6:
                $strDia = "Sabado";
                break;
            default:
                $strDia = "";
                break;
        }
        return $strDia;
    }

    /*
     *
     * Método para obtener el tipo de esquema
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 14-03-2019  Se agrega funcionalidad para obtener el tipo de esquema seleccionado
     * del servicio enviado como parámetro.
     *
     * @param $objInfoServicio : Objeto de tipo schema:InfoServicio
     *
     * @return $objInfServProdCaract->getValor() OR null
     *
     */

    public function getTipoEsquema($objInfoServicio)
    {   // Valido si existe el metodo dentro del objeto, para controlar una excepcion.
        if(method_exists($objInfoServicio->getProductoId(), 'getNombreTecnico') && $objInfoServicio->getProductoId()->getNombreTecnico() == 'INTERNET WIFI')
        {
                // Obtengo el esquema del servicio elegido.
                $objAdmiCaract =   $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array(
                        "descripcionCaracteristica"     => 'TIPO_ESQUEMA'
                    ));
    
                $objProduct = $objInfoServicio->getProductoId();
    
                $objProCaract =   $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                    ->findOneBy(array(  "productoId"       => $objProduct->getId(),
                        "caracteristicaId" => $objAdmiCaract->getId()));

                $objInfServProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId'    =>     $objInfoServicio->getId(),
                            'productoCaracterisiticaId' =>   $objProCaract->getId()
                        ));

                if (is_object($objInfServProdCaract) && method_exists($objInfServProdCaract, 'getValor') ) 
                {
                    return $objInfServProdCaract->getValor();
                }

        }

        return null;
    }

    /**
     * Función que crea un elemento CPE-NODO-WIFI para instalación simultánea.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 22-05-2019 - Version Inicial
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 08-07-2019 - Se modifica función para que soporte multiples servicios internet wifi,
     * se pueda generar factibilidad con un solo click.
     *
     * @param $arrayParams
     * @return JsonResponse|Response
     * @throws \Exception
     */

    public function fechaFactibilidadNuevoElemento($arrayParams)
    {
        $objSession                = $arrayParams['objRequest'];
        $objPeticion               = $arrayParams['objPeticion'];
        $intIdSolicitud            = $arrayParams['idSolicitud'];
        $strFechaCreacionTramo     = $arrayParams['fechaProgramacion'];
        $strNombreElemento      = strtoupper($arrayParams['nombreElemento']);
        $strObservacion            = 'Factibilidad para instalacion simultanea';
        $strParroquiaId            = $arrayParams['idParroquia'];
        $intAlturaSnm              = 0;
        $strLongitudUbicacion      = $arrayParams['longitud'];
        $strLatitudUbicacion       = $arrayParams['latitud'];
        $strDireccionUbicacion     = $arrayParams['direccion'];
        $strDescripcionElemento    = $arrayParams['descripcion'];
        $strIdModeloElemento       = $arrayParams['tipoElemento'];
        $intIndex               = $arrayParams['index'];

        $emInf = $this->emInfraestructura;
        $emCom = $this->emComercial;

        $emInf->getConnection()->beginTransaction();
        $emCom->getConnection()->beginTransaction();


        try
        {
            $objDetalleSolicitud = $emInf->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);

            if($objDetalleSolicitud)
            {

                $objModeloElemento = $emInf->getRepository('schemaBundle:AdmiModeloElemento')->find($strIdModeloElemento);

                //actualizo el estado del servicio y creo el historial
                //cambio de estado a la info servicio Factible e historial
                $objServicio = $emCom->getRepository('schemaBundle:InfoServicio')->find($objDetalleSolicitud->getServicioId()->getId());

                $objServicio->setEstado('FactibilidadEnProceso');
                $emCom->persist($objServicio);
                $emCom->flush();

                if ($intIndex == 0)
                {
                    $objElemento = new InfoElemento();
                    $objElemento->setNombreElemento($strNombreElemento);
                    $objElemento->setDescripcionElemento($strDescripcionElemento);
                    $objElemento->setModeloElementoId($objModeloElemento);
                    $objElemento->setUsrResponsable($objSession->get('user'));
                    $objElemento->setUsrCreacion($objSession->get('user'));
                    $objElemento->setFeCreacion(new \DateTime('now'));
                    $objElemento->setIpCreacion($objPeticion->getClientIp());
                    $objElemento->setEstado("Pendiente");

                    $emInf->persist($objElemento);
                    $emInf->flush();

                    //historial elemento
                    $objHistorialElemento = new InfoHistorialElemento();
                    $objHistorialElemento->setElementoId($objElemento);
                    $objHistorialElemento->setEstadoElemento("Pendiente");
                    $objHistorialElemento->setObservacion("Se ingreso un Nodo Wifi");
                    $objHistorialElemento->setUsrCreacion($objSession->get('user'));
                    $objHistorialElemento->setFeCreacion(new \DateTime('now'));
                    $objHistorialElemento->setIpCreacion($objPeticion->getClientIp());
                    $emInf->persist($objHistorialElemento);
                    $emInf->flush();

                    $serviceInfoElemento = $this->infoElementoService;
                    $arrayRespuestaCoordenadas = $serviceInfoElemento->validarLimitesCoordenadasElemento(array(
                        "latitudElemento" => $strLatitudUbicacion,
                        "longitudElemento" => $strLongitudUbicacion,
                        "msjTipoElemento" => "del nodo wifi "
                    ));

                    if ($arrayRespuestaCoordenadas["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaCoordenadas['mensaje']);
                    }

                    //info ubicacion
                    $objParroquia = $emInf->find('schemaBundle:AdmiParroquia', $strParroquiaId);
                    $objUbicacionElemento = new InfoUbicacion();
                    $objUbicacionElemento->setLatitudUbicacion($strLatitudUbicacion);
                    $objUbicacionElemento->setLongitudUbicacion($strLongitudUbicacion);
                    $objUbicacionElemento->setDireccionUbicacion($strDireccionUbicacion);
                    $objUbicacionElemento->setAlturaSnm($intAlturaSnm);
                    $objUbicacionElemento->setParroquiaId($objParroquia);
                    $objUbicacionElemento->setUsrCreacion($objSession->get('user'));
                    $objUbicacionElemento->setFeCreacion(new \DateTime('now'));
                    $objUbicacionElemento->setIpCreacion($objPeticion->getClientIp());
                    $emInf->persist($objUbicacionElemento);
                    $emInf->flush();

                    //empresa elemento ubicacion
                    $objEmpresaElementoUbica = new InfoEmpresaElementoUbica();
                    $objEmpresaElementoUbica->setEmpresaCod($objSession->get('idEmpresa'));
                    $objEmpresaElementoUbica->setElementoId($objElemento);
                    $objEmpresaElementoUbica->setUbicacionId($objUbicacionElemento);
                    $objEmpresaElementoUbica->setUsrCreacion($objSession->get('user'));
                    $objEmpresaElementoUbica->setFeCreacion(new \DateTime('now'));
                    $objEmpresaElementoUbica->setIpCreacion($objPeticion->getClientIp());
                    $emInf->persist($objEmpresaElementoUbica);
                    $emInf->flush();

                    //empresa elemento
                    $objEmpresaElemento = new InfoEmpresaElemento();
                    $objEmpresaElemento->setElementoId($objElemento);
                    $objEmpresaElemento->setEmpresaCod($objSession->get('idEmpresa'));
                    $objEmpresaElemento->setEstado("Activo");
                    $objEmpresaElemento->setUsrCreacion($objSession->get('user'));
                    $objEmpresaElemento->setIpCreacion($objPeticion->getClientIp());
                    $objEmpresaElemento->setFeCreacion(new \DateTime('now'));
                    $emInf->persist($objEmpresaElemento);
                    $emInf->flush();

                    //GUARDA INFO DETALLE SOLICITUD
                    $objDetalleSolicitud->setObservacion($strObservacion);
                    $objDetalleSolicitud->setFeEjecucion($strFechaCreacionTramo);
                    $objDetalleSolicitud->setEstado("FactibilidadEnProceso");
                    $objDetalleSolicitud->setElementoId($objElemento->getId());
                    $emInf->persist($objDetalleSolicitud);
                    $emInf->flush();

                    $arrayRespuesta['idElemento']    = $objElemento->getId();

                }else
                {
                    $intIdElemento = $arrayParams['idElemento'];

                    //GUARDA INFO DETALLE SOLICITUD
                    $objDetalleSolicitud->setObservacion($strObservacion);
                    $objDetalleSolicitud->setFeEjecucion($strFechaCreacionTramo);
                    $objDetalleSolicitud->setEstado("FactibilidadEnProceso");
                    $objDetalleSolicitud->setElementoId($intIdElemento);
                    $emInf->persist($objDetalleSolicitud);
                    $emInf->flush();

                    $arrayRespuesta['idElemento'] = $intIdElemento;
                }


                // Guarda el nombre del CPE Wifi en el historial del servicio.
                $objServicioHist = new InfoServicioHistorial();
                $objServicioHist->setServicioId($objServicio);
                $objServicioHist->setObservacion("<b>CPE Nodo Wifi: </b>". $strNombreElemento."<br/>");
                $objServicioHist->setIpCreacion($objPeticion->getClientIp());
                $objServicioHist->setFeCreacion(new \DateTime('now'));
                $objServicioHist->setUsrCreacion($objSession->get('user'));
                $objServicioHist->setEstado($objServicio->getEstado());
                $emCom->persist($objServicioHist);
                $emCom->flush();

                //GUARDAR INFO DETALLE SOLICITUD HISTORIAL
                $objDetalleSolHist = new InfoDetalleSolHist();
                $objDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objDetalleSolHist->setIpCreacion($objPeticion->getClientIp());
                $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHist->setUsrCreacion($objSession->get('user'));
                $objDetalleSolHist->setEstado('FactibilidadEnProceso');
                $objDetalleSolHist->setObservacion($strObservacion);
                $emInf->persist($objDetalleSolHist);
                $emInf->flush();

                $emCom->commit();
                $emInf->commit();

                $arrayRespuesta['status']        = "OK";
                $arrayRespuesta['msg']           = "Se creo exitosamente el CPE WIFI";

            }else
            {
                throw new \Exception("Error al solicitar la factibilidad, notificar a Sistemas.");
            }

        }
        catch(\Exception $e)
        {
            if ($emInf->getConnection()->isTransactionActive())
            {
                $emInf->rollback();
                $emInf->close();
            }

            if ($emCom->getConnection()->isTransactionActive())
            {
                $emCom->rollback();
                $emCom->close();
            }

            $this->utilServicio->insertError('Telcos+',
                'PlanificarService.fechaFactibilidadNuevoElemento',
                "Error: <br>" . $e->getMessage(),
                $objSession->get('user'),
                $objPeticion->getClientIp());

            $arrayRespuesta['status'] = "ERROR";
            $arrayRespuesta['msg']    = "Error: " . $e->getMessage();
        }

        return $arrayRespuesta;
    }

    /**
     * Funcion que permite obtener el id del internet wifi para instalacion simultanea.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio tradicional.
     * @return null|$intIdWifiInstSim
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 22-05-2019 - Version Inicial.
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.1 18-03-2020 | Se realizan ajustes para que este metodo funcione para servicios
     *                           INTERNET WIFI y WIFI Alquiler Equipos.
     * 
     */

    public function getIdWifiInstSim($intIdServicio)
    {
        $arrayIdWifiInstSim = null;
        $arrayServiciosWifi = array();

        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);

        if (is_object($objServicioTradicional))
        {
            $objAdmiProductoIW = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'INTERNET WIFI'
                ));

            $objAdmiProductoWAE = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'WIFI Alquiler Equipos'
                ));

            $arrayAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_WIFI',
                    'estado' => 'Activo'
                ));

            foreach ($arrayAdmiCaract as $key => $objAdmiCaract) 
            {
                $objAdmiProdCaractIW = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                    ->findOneBy(array(
                        'caracteristicaId' => $objAdmiCaract->getId(),
                        'productoId' => $objAdmiProductoIW->getId()
                    ));

                $objAdmiProdCaractWAE = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                    ->findOneBy(array(
                        'caracteristicaId' => $objAdmiCaract->getId(),
                        'productoId' => $objAdmiProductoWAE->getId()
                    ));
            }

            $arrayServiciosIW = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProductoIW->getId()
                ));
            
            $arrayServiciosWAE = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProductoWAE->getId()
                ));

            $arrayServiciosWifi= array_merge($arrayServiciosIW, $arrayServiciosWAE);

            if (count($arrayServiciosWifi)>=1)
            {
                foreach ($arrayServiciosWifi as $key=>$objServWifi)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServWifi->getId(),
                            'productoCaracterisiticaId' => $objServWifi->getProductoId()->getDescripcionProducto() == "INTERNET WIFI" ? 
                            $objAdmiProdCaractIW->getId() : $objAdmiProdCaractWAE->getId()
                        ));

                    if (is_object($objInfoServProdCaract) && ($objServicioTradicional->getId() == intval($objInfoServProdCaract->getValor())))
                    {
                        $arrayIdWifiInstSim[$key] = $objServWifi->getId();
                    }
                }
            }
        }

        return $arrayIdWifiInstSim;
    }

    /**
     * Funcion que permite obtener un arreglo con los servicios internet wifi para instalacion simultanea.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 05-07-2019 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio.
     * @return null|$intIdWifiInstSim
     */

    public function getArrayServiciosWifiInstalacionSimultanea($intIdServicio)
    {
        $arrayServiciosWifi = null;

        $objServicioInternetWifi= $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($intIdServicio);

        if (is_object($objServicioInternetWifi))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'INTERNET WIFI'
                ));

            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_WIFI',
                    'estado' => 'Activo'
                ));

            $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId()
                ));

            $objInfoServProdCaractInternetWifi = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                ->findOneBy(array(
                    'servicioId' => $objServicioInternetWifi->getId(),
                    'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                ));

            $arrayServiciosWifi = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioInternetWifi->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId(),
                    'estado'     =>  $objServicioInternetWifi->getEstado()
                ));

            if (count($arrayServiciosWifi)>=1)
            {
                foreach ($arrayServiciosWifi as $key=>$objServWifi)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServWifi->getId(),
                            'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                    if(is_object($objInfoServProdCaract))
                    {
                        $boolIdWifiInstSim = intval($objInfoServProdCaractInternetWifi->getValor()) == intval($objInfoServProdCaract->getValor());

                        if (!$boolIdWifiInstSim)
                        {
                            unset($arrayServiciosWifi[$key]);
                        }
                    }
                }
            }
        }

        return $arrayServiciosWifi;
    }

    /**
     * Función que permite actualizar el estado y el historial de los servicios Wifi Alquiler de Equipos que dependen
     * de un servicio ya inspeccionado.
     *
     * @param $arrayParametros
     *
     *        • arrayServiciosWifiAlquiler ➜ Contiene un arreglo con los servicios Wifi Alquiler Equipos.
     *        • strEstado                  ➜ Contiene un string para establecer estado.
     *        • strObservacion             ➜ Contiene un string con una observación.
     *        • strIpCreacion              ➜ Contiene un string con una direción IP.
     *        • strUsrCreacion             ➜ Contiene un string con el usuario de creación.
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 27-08-2019 | Versión Inicial.
     *
     */
    public function actualizarEstadoHistorialWifiAlquiler($arrayParametros)
    {
         $arrayServiciosWifiAlquiler = $arrayParametros['arrayServiciosWifiAlquiler'];
         $strEstado                  = $arrayParametros['strEstado'];
         $strObservacion             = $arrayParametros['strObservacion'];
         $strIpCreacion              = $arrayParametros['strIpCreacion'];
         $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
         $intIdServicioPlanificador  = $arrayParametros['intServicioPlanificador'];
         $strDescripcionSolicitud    = isset($arrayParametros['strDescripcionSolicitud']) ?
                                             $arrayParametros['strDescripcionSolicitud'] :
                                             'SOLICITUD PLANIFICACION';
         $strEstadoSolicitud         = isset($arrayParametros['strEstadoSolicitud']) ?
                                             $arrayParametros['strEstadoSolicitud'] :
                                             'PrePlanificada';

        /* Obtengo el objeto de la solicitud de planificacion. */
        $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                              ->findOneBy(array(
                                                  'descripcionSolicitud' => $strDescripcionSolicitud
                                                ));

        foreach ($arrayServiciosWifiAlquiler as $intServicioWifiAlquiler)
        {
            $objServicioWifiAlquiler = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioWifiAlquiler);
            $strDescripcionPlanProd  = $objServicioWifiAlquiler->getProductoId()->getDescripcionProducto();

            if ($strDescripcionPlanProd == 'WIFI Alquiler Equipos' && $objServicioWifiAlquiler->getId() != $intIdServicioPlanificador)
            {
                    $objSolicitudPlanificacion = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                 ->findOneBy(array(
                                                     'servicioId'       => $objServicioWifiAlquiler->getId(),
                                                     'tipoSolicitudId'  => $objTipoSolicitud->getId(),
                                                     'estado'           => $strEstadoSolicitud
                                                 ));

                    if (is_object($objSolicitudPlanificacion))
                    {
                        /* Establezco la solicitud como finalizada. */
                        $objSolicitudPlanificacion->setEstado('Finalizada');
                        /* Guardo en el historial de la solicitud. */
                        $entitySolicitudHist = new InfoDetalleSolHist();
                        $entitySolicitudHist->setDetalleSolicitudId($objSolicitudPlanificacion);
                        $entitySolicitudHist->setEstado('Finalizada');
                        $entitySolicitudHist->setFeIniPlan(new \DateTime('now'));
                        $entitySolicitudHist->setFeFinPlan(new \DateTime('now'));
                        $entitySolicitudHist->setObservacion('Se finaliza debido a que fue planificada en una sola orden principal.');

                        $entitySolicitudHist->setUsrCreacion($strUsrCreacion);
                        $entitySolicitudHist->setFeCreacion(new \DateTime('now'));
                        $entitySolicitudHist->setIpCreacion($strIpCreacion);

                        $this->emComercial->persist($entitySolicitudHist);
                        $this->emComercial->flush();
                    }

                    /*SE ACTUALIZA EL ESTADO DEL SERVICIO*/
                    $objServicioWifiAlquiler->setEstado($strEstado);
                    $this->emComercial->persist($objServicioWifiAlquiler);
                    
                    /*GUARDAR INFO DETALLE SOLICICITUD HISTORIAL*/
                    $this->emComercial->flush();
                    $entityServicioHist = new InfoServicioHistorial();
                    $entityServicioHist->setServicioId($objServicioWifiAlquiler);
                    $entityServicioHist->setIpCreacion($strIpCreacion);
                    $entityServicioHist->setFeCreacion(new \DateTime('now'));
                    $entityServicioHist->setUsrCreacion($strUsrCreacion);
                    $entityServicioHist->setEstado($strEstado);
                    $entityServicioHist->setObservacion($strObservacion);
                    $this->emComercial->persist($entityServicioHist);
                    $this->emComercial->flush();

            }

        }

    }
    
    /**
     * Funcion que permite obtener el id del producto COU LINEAS TELEFONIA FIJA para instalacion simultanea.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 11-02-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio tradicional.
     * @return null|$intIdCouInstSim
     */

    public function getIdCouInstSim($intIdServicio)
    {
        $arrayIdCouInstSim = null;

        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);

        if (is_object($objServicioTradicional))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'COU LINEAS TELEFONIA FIJA'
                ));

            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA',
                    'estado' => 'Activo'
                ));

            $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId()
                ));

            $arrayServiciosCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId()
                ));

            if (count($arrayServiciosCou)>=1)
            {
                foreach ($arrayServiciosCou as $key=>$objServCouLineas)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServCouLineas->getId(),
                            'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                    if (is_object($objInfoServProdCaract) && ($objServicioTradicional->getId() == intval($objInfoServProdCaract->getValor())))
                    {
                        $arrayIdCouInstSim[$key] = $objServCouLineas->getId();
                    }
                }
            }
        }

        return $arrayIdCouInstSim;
    }

    /**
     * Funcion que permite obtener un arreglo con los servicios COU LINEAS TELEFONIA FIJA para instalacion simultanea.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 11-02-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio.
     * @return null|$intIdCouInstSim
     */

    public function getArrayServiciosCouInstalacionSimultanea($intIdServicio)
    {
        $arrayServiciosCou = null;

        $objServicioCouLineas = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($intIdServicio);

        if (is_object($objServicioCouLineas))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'COU LINEAS TELEFONIA FIJA'
                ));

            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA',
                    'estado' => 'Activo'
                ));

            $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId()
                ));

            $objInfoServProdCaractCouLineas = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                ->findOneBy(array(
                    'servicioId' => $objServicioCouLineas->getId(),
                    'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                ));

            $arrayServiciosCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioCouLineas->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId(),
                    'estado'     =>  $objServicioCouLineas->getEstado()
                ));

            if (count($arrayServiciosCou)>=1)
            {
                foreach ($arrayServiciosCou as $key=>$objServCouLineas)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServCouLineas->getId(),
                            'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                    if(is_object($objInfoServProdCaract))
                    {
                        $boolIdCouInstSim = intval($objInfoServProdCaractCouLineas->getValor()) == intval($objInfoServProdCaract->getValor());

                        if (!$boolIdCouInstSim)
                        {
                            unset($arrayServiciosCou[$key]);
                        }
                    }
                }
            }
        }

        return $arrayServiciosCou;
    }
    
    /**
     * Funcion que permite obtener el id del servicio tradicional del producto que tiene la marca de activación simultánea.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 21-02-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene activación simultánea.
     * @return null|$intIdCouInstSim
     */

    public function getIdTradInstSim($intIdServicio)
    {
        $arrayIdCouInstSim = null;
        
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);

        if (is_object($objServicioTradicional))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'COU LINEAS TELEFONIA FIJA'
                ));

            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA',
                    'estado' => 'Activo'
                ));

            $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId()
                ));

            $arrayServiciosCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId()
                ));

            if (count($arrayServiciosCou)>=1)
            {
                foreach ($arrayServiciosCou as $intKey=>$objServCouLineas)
                {
                    //Solo pendientes, asignada, asignada tarea
                    if ($objServCouLineas->getEstado() !== 'Anulado' || $objServCouLineas->getEstado() !== 'Rechazada' ||
                        $objServCouLineas->getEstado() !== 'Cancel')
                    {
                        $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findOneBy(array('servicioId' => $objServCouLineas->getId(),
                                                                  'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                        if (is_object($objInfoServProdCaract))
                        {
                            $intKey = 0;
                            $arrayIdCouInstSim[$intKey]     = $objInfoServProdCaract->getValor();
                            $arrayIdCouInstSim[$intKey + 1] = $objInfoServProdCaract->getServicioId();
                            $arrayIdCouInstSim[$intKey + 2] = $objServicioTradicional->getPuntoId();
                        }
                    }
                }
            }
        }

        return $arrayIdCouInstSim;
    }
    
    /**
     * Funcion que permite obtener el id del servicio tradicional del producto L3MPLS.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 06-03-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene activación simultánea.
     * @return null|$intIdCouInstSim
     */

    public function getIdServInstSim($intIdServicio)
    {
        $arrayIdCouInstSim = null;
        
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);

        if (is_object($objServicioTradicional))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'descripcionProducto' => 'L3MPLS',
                    'nombreTecnico'       => 'L3MPLS'
                ));
            
            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'ENLACE_DATOS',
                    'estado' => 'Activo'
                ));
            
             $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId(),
                    'productoId'       => $objAdmiProducto->getId()
                ));
            
            $arrayServiciosCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId()
                ));
            
            if (count($arrayServiciosCou)>=1)
            {
                foreach ($arrayServiciosCou as $intKey=>$objServCouLineas)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServCouLineas->getId(),
                            'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                    if (is_object($objInfoServProdCaract))
                    {
                        $intKey = 0;
                        $arrayIdCouInstSim[$intKey]     = $objInfoServProdCaract->getServicioId();
                    }
                }
            }
        }

        return $arrayIdCouInstSim;
    }
    
    /**
     * Funcion que permite obtener el id del servicio tradicional del producto que tiene la marca de activación simultánea.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 21-02-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene activación simultánea.
     * @return null|$intIdCouInstSim
     */

    public function getIdTradInstSimCanaTelefonia($intIdServicio,$intIdProducto)
    {
        $arrayIdCouInstSim = null;
        
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
            ->find($intIdServicio);

        if (is_object($objServicioTradicional))
        {
            $objAdmiProducto = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                ->findOneBy(array(
                    'id'                  => $intIdProducto,
                    'descripcionProducto' => 'L3MPLS'
                ));

            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA_COU_TELEFONIA_FIJA',
                    'estado' => 'Activo'
                ));

            $objAdmiProdCaract = $this->emInfraestructura->getRepository('schemaBundle:AdmiProductoCaracteristica')
                ->findOneBy(array(
                    'caracteristicaId' => $objAdmiCaract->getId()
                ));

            $arrayServiciosCou = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'id'         =>  $intIdServicio,
                    'puntoId'    =>  $objServicioTradicional->getPuntoId(),
                    'productoId' =>  $objAdmiProducto->getId()
                ));

            if (count($arrayServiciosCou)>=1)
            {
                foreach ($arrayServiciosCou as $intKey=>$objServCouLineas)
                {
                    //Solo pendientes, asignada, asignada tarea
                    if ($objServCouLineas->getEstado() !== 'Anulado' || $objServCouLineas->getEstado() !== 'Rechazada' ||
                        $objServCouLineas->getEstado() !== 'Cancel')
                    {
                        $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                                                ->findOneBy(array('servicioId' => $objServCouLineas->getId(),
                                                                  'productoCaracterisiticaId' => $objAdmiProdCaract->getId()
                        ));

                        if (is_object($objInfoServProdCaract))
                        {
                            $intKey = 0;
                            $arrayIdCouInstSim[$intKey]     = $objInfoServProdCaract->getValor();
                            $arrayIdCouInstSim[$intKey + 1] = $objInfoServProdCaract->getServicioId();
                            $arrayIdCouInstSim[$intKey + 2] = $objServicioTradicional->getPuntoId();
                        }
                    }
                }
            }
        }

        return $arrayIdCouInstSim;
    }

    /**
     * Función que permite obtener un arreglo con los servicios para instalación simultanea.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 30-05-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene activación simultánea.
     * @return null|$intIdCouInstSim
     */

    public function getServiciosInstalacionSimultanea($intIdServicio)
    {
        $arrayServiciosSimultaneos = array();

        /*Obtenemos el array del parámetro.*/
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

        /*Validamos que el arreglo no este vacío.*/
        if (is_array($objParamsDet) && !empty($objParamsDet))
        {
            $objCaracteristicasServiciosSimultaneos = json_decode($objParamsDet[0]['valor1'], true);
            $arrayProductosSimultaneos = $this->serviceGeneral->getArraybyKey('PRODUCTO_ID', $objCaracteristicasServiciosSimultaneos);
        }
        
        /* Obtengo un objeto con el servicio tradicional. */
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($intIdServicio);

        /* Se valida que no esten vacios. */
        if (is_object($objServicioTradicional) && isset($arrayProductosSimultaneos) && !is_null($arrayProductosSimultaneos))
        {
            /* Se trae un arreglo de servicios que cumplan con las condiciones de puntoId y productoId. */
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId' => $objServicioTradicional->getPuntoId(),
                    'productoId' => $arrayProductosSimultaneos
                ));
            
            /* Se trae un objeto de la caracteristica. */
            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'INSTALACION_SIMULTANEA',
                    'estado' => 'Activo'
                ));
            
            /* Se valida que los servicios del punto sean 1 o mas. */
            if (count($arrayServiciosPunto)>=1)
            {
                /* Se define un arreglo para filtrar por estado los servicios simultaneos. */
                $arrayEstados = array('Activo', 'Rechazado', 'Rechazada', 'Anulado', 'Anulada');

                /* Se recorre el arreglo de servicios del punto */
                foreach ($arrayServiciosPunto as $key=>$objServicio)
                {
                    $objInfoServProdCaract = $this->emInfraestructura->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy(array(
                            'servicioId' => $objServicio->getId(),
                            'valor'      => $objServicioTradicional->getId()
                        ));

                    if(is_object($objInfoServProdCaract) && !empty($objInfoServProdCaract))
                    {
                        /* Se define variable para validar si el valor de la caracteristica es igual al del servicio tradicional. */
                        $boolIdInstSim = intval($objServicioTradicional->getId()) == intval($objInfoServProdCaract->getValor());
                        
                        /* Se valida el tema de la caracteristica, ademas de si el estado del servicio no esta incluido en el arreglo de estados. */
                        if ($boolIdInstSim && !in_array($objServicio->getEstado(), $arrayEstados))
                        {
                            /* Si cumple se agrega al arreglo de respuesta. */
                            array_push($arrayServiciosSimultaneos, $objServicio->getId());
                        }
                    }
                }
            }
        }

        return $arrayServiciosSimultaneos;
    }
    
    /**
    * Documentación para la función 'getPedidosByDepartamento'.
    *
    * Función encargada de recuperar los pedidos asociados a la cotización.
    *
    * @author David Leon <mdleon@telconet.ec>
    * @version 1.0 - 07-05-2020
    *
    */
    public function getPedidosByDepartamento($arrayParametros)
    {
        try
        {
            $strMensajeError     = "";
            $strStatus           = "200";
            $arrayResultado      = array();
            if(is_array($arrayParametros) && !empty($arrayParametros))
            {
                $arrayOptions      = array(CURLOPT_SSL_VERIFYPEER => false);
                $arrayResponse     = $this->restClientPedidos->postJSON($this->strMicroServUrl,json_encode($arrayParametros) ,$arrayOptions);
                
                if(!isset($arrayResponse['result']) || $arrayResponse['status']!="200")
                {
                    throw new \Exception('Problemas al obtener información del MicroServicio: '.$arrayResultado["error"]);
                }
                $arrayResultado = json_decode($arrayResponse['result'],true);

                if((empty($arrayResultado) || !is_array($arrayResultado))|| (!isset($arrayResultado['message']) || $arrayResultado["message"] == "") 
                    && !$arrayResultado['status']=='OK')
                {
                    throw new \Exception('Problemas al obtener información del MicroServicio, reintente nuevamente.');
                }

                $arrayDataTemp = $arrayResultado["data"];

                if((empty($arrayDataTemp) || !is_array($arrayDataTemp))|| (!isset($arrayDataTemp["codError"]) || 
                    $arrayDataTemp["codError"] !=$strStatus  || $arrayDataTemp["mensajeUsuario"] !='OK'|| $arrayDataTemp["mensajeTecnico"] !='OK'))
                {
                    throw new \Exception('Problemas al obtener información, reintente nuevamente.');
                }
                $arrayDatosPedidos = $arrayDataTemp["data"];
            }
            else
            {
                throw new \Exception('Problemas al obtener información, reintente nuevamente.');
            }
        }
        catch( \Exception $e )
        {
            $strMensajeError = $e->getMessage();
            $strStatus       = "500";
        }
        $arrayRespuesta = array('error'  => $strMensajeError,
                                'datos'  => $arrayDatosPedidos,
                                'status' => $strStatus);
        return $arrayRespuesta;
    }
    

    /**
    * Documentación para la función 'programarPlanificacion'.
    *
    * Función encargada de realizar la planificación para solicitudes
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 27-10-2020
    *
    * @author Emmanuel Martillo <emartillo@telconet.ec>
    * @version 1.1 20-03-2023 - Se agrega validacion por prefijo empresa Ecuanet (EN) para validar el cupo de planificacion disponible. 
    *
    */
    public function programarPlanificacion($arrayParametros)
    {
        $intIdSolicitud      = $arrayParametros['intIdSolicitud'];
        $intIdCuadrilla      = $arrayParametros['intIdCuadrilla'];
        $strFeProgramacion   = $arrayParametros['strFechaProgramacion'];
        $strHoraIniProgram   = $arrayParametros['strHoraIni'];
        $strHoraFinProgram   = $arrayParametros['strHoraFin'];
        $strObservacion      = $arrayParametros['strObservacion'];
        $strUser             = $arrayParametros['strUser'];
        $strIp               = $arrayParametros['strIp'];
        $intOpcion           = 0;//0 programar 1 asignar responsable
        $strMensaje          = "";
        $strStatus           = 200;
        $arrayResultado      = array();
        $intIdCanton         = 0;
        $strNombreTecnico    = "";
        try
        {
            if (empty($intIdCuadrilla))
            {
                throw new \Exception('Falta enviar el parámetro idCuadrilla');
            }
            if (empty($intIdSolicitud))
            {
                throw new \Exception('Falta enviar el parámetro idSolicitud');
            }
            if (empty($strFeProgramacion) || empty($strHoraIniProgram) || empty($strHoraFinProgram))
            {
                throw new \Exception('Falta enviar uno de los siguientes parámetros: fecha, hora inicio u hora fin de programación');
            }

            $arrayFechaProgramacion       = explode("-",$strFeProgramacion);
            $arrayHoraInicio              = explode(":",$strHoraIniProgram);
            $arrayHoraFin                 = explode(":",$strHoraFinProgram);

            if (count($arrayFechaProgramacion) != 3)
            {
                throw new \Exception('Error en la fechaProgramacion enviado, el formato correcto debe ser dd-mm-yyyy');
            }

            $strFechaProgramacionYmd      = $arrayFechaProgramacion[2].'-'.$arrayFechaProgramacion[1].'-'.$arrayFechaProgramacion[0];

            if (count($arrayHoraInicio) != 2)
            {
                throw new \Exception('Error en la horaInicio enviado, el formato correcto debe ser hh:mm');
            }

            if (count($arrayHoraFin) != 2)
            {
                throw new \Exception('Error en la horaFin enviado, el formato correcto debe ser hh:mm');
            }

            /*Obtenemos el array del parámetro.*/
            $objParamsDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('EMPRESA_SERVICE_PLANIFICACION',
                    'PLANIFICACION',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    10);

            /*Validamos que el arreglo no este vacío.*/
            if (!is_array($objParamsDet) || empty($objParamsDet))
            {
                throw new \Exception('Falta configurar parámetros para obtener departamento y empresa.');
            }

            $arrayParametrosEmpleado = array(
                'codEmpresa' => $objParamsDet[0]['valor3'],
                'intStart'   => 0,
                'intLimit'   => 1000,
                'criterios'  => array(  'nombres'        => '', 
                                        'apellidos'      => '',
                                        'identificacion' => $objParamsDet[0]['valor1'],
                                        'estado'         => 'Activo',
                                        'departamento'   => '',
                                        'canton'         => ''
                                        )
            );

            $arrayResultadoEmpleado = $this->emComercial->getRepository('schemaBundle:InfoPersona')
                                                ->getResultadoEmpleados($arrayParametrosEmpleado);
                                                
            $arrayRegistros=$arrayResultadoEmpleado['resultado'];

            if (is_array($arrayRegistros)) 
            {
                foreach ($arrayRegistros as $data)
                {	
                    $strCodEmpresa       = $data['idEmpresa'];
                    $intIdDepartamento   = $data['idDepartamento'];        
                }
            }

            $objEmpresa = $this->emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->find($strCodEmpresa);
            if (!is_object($objEmpresa))
            {
                throw new \Exception('No se pudo obtener información de la empresa.');                
            }

            if (!isset($intIdDepartamento))
            {
                throw new \Exception('No se pudo obtener el id del departamento.');
            }

            $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolicitud);

            if (!is_object($objDetalleSolicitud))
            {
                throw new \Exception('No existe solicitud correspondiente al idSolicitud enviado.');
            }

            if ($objDetalleSolicitud->getEstado() != 'PrePlanificada')
            {
                throw new \Exception('La solicitud ya fue planificada y se encuentra en estado '.$objDetalleSolicitud->getEstado());
            }

            $objAdmiCuadrilla = $this->emComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrilla);

            if (!is_object($objAdmiCuadrilla))
            {
                throw new \Exception('No existe cuadrilla correspondiente al idCuadrilla enviado.');
            }

            $arrayParametrosTareasByProceso['servicioId']     = $objDetalleSolicitud->getServicioId()->getId();
            $arrayParametrosTareasByProceso['id_solicitud']   = $objDetalleSolicitud->getId();
            $arrayParametrosTareasByProceso['nombreTarea']    = "todas";
            $arrayParametrosTareasByProceso['estado']         = "Activo";
            $arrayParametrosTareasByProceso['start']          = 0;
            $arrayParametrosTareasByProceso['limit']          = 1000;
            $arrayParametrosTareasByProceso['accion']         = "";
            $arrayParametrosTareasByProceso['idEmpresa']      = $objEmpresa->getId();
            $arrayParametrosTareasByProceso['prefijoEmpresa'] = $objEmpresa->getPrefijo();

            $strRespuestaJsonTareas   = $this->getTareasByProcesoAndTarea($arrayParametrosTareasByProceso);
            $arrayRespuestaJsonTareas = json_decode($strRespuestaJsonTareas);
            $arrayAdmiTarea           = $arrayRespuestaJsonTareas->encontrados;

            foreach($arrayAdmiTarea as $objAdmiTarea)
            {
                $strParamResponsables .= $objAdmiTarea->idTarea.'@@'.'cuadrilla'.'@@'.$intIdCuadrilla.'@@@@'.'|';
            }

            $arrayParametrosPlanif['intIdPerEmpRolSesion']   = '';
            $arrayParametrosPlanif['strOrigen']              = 'local';
            $arrayParametrosPlanif['intIdFactibilidad']      = $intIdSolicitud;
            $arrayParametrosPlanif['intIdCuadrilla']         = $intIdCuadrilla;
            $arrayParametrosPlanif['strParametro']           = $intIdSolicitud;
            $arrayParametrosPlanif['strParamResponsables']   = $strParamResponsables;
            $arrayParametrosPlanif['intIdPersona']           = '';
            $arrayParametrosPlanif['intIdPersonaEmpresaRol'] = '';
            $arrayParametrosPlanif['intIdPerTecnico']        = '';
            $arrayParametrosPlanif['strCodEmpresa']          = $objEmpresa->getId();
            $arrayParametrosPlanif['strPrefijoEmpresa']      = $objEmpresa->getPrefijo();
            $arrayParametrosPlanif['intIdDepartamento']      = $intIdDepartamento;


            $arrayParametrosPlanif['dateF']                   = array($arrayFechaProgramacion[2],
                                                                      $arrayFechaProgramacion[1],
                                                                      $arrayFechaProgramacion[0]);
            $arrayParametrosPlanif['dateFecha']               = array($strFechaProgramacionYmd,$strHoraIniProgram);
            $arrayParametrosPlanif['strFechaInicio']          = str_replace("-","/",$strFechaProgramacionYmd)." ".$strHoraIniProgram;
            $arrayParametrosPlanif['strFechaFin']             = str_replace("-","/",$strFechaProgramacionYmd)." ".$strHoraFinProgram;
            $arrayParametrosPlanif['strHoraInicioServicio']   = array($arrayHoraInicio[0],$arrayHoraInicio[1],"00");
            $arrayParametrosPlanif['strHoraFinServicio']      = array($arrayHoraFin[0],$arrayHoraFin[1],"00");
            $arrayParametrosPlanif['dateFechaProgramacion']   = $strFechaProgramacionYmd;
            $arrayParametrosPlanif['strHoraInicio']           = array($arrayHoraInicio[0],$arrayHoraInicio[1],"00");
            $arrayParametrosPlanif['strHoraFin']              = array($arrayHoraInicio[0],$arrayHoraInicio[1],"00");
            $arrayParametrosPlanif['strObservacionServicio']  = $strObservacion;
            $arrayParametrosPlanif['strIpCreacion']           = $strIp;
            $arrayParametrosPlanif['strUsrCreacion']          = $strUser;
            $arrayParametrosPlanif['strObservacionSolicitud'] = $strObservacion;
            $arrayParametrosPlanif['strAtenderAntes']         = '';
            $arrayParametrosPlanif['strEsHal']                = 'N';
            $arrayParametrosPlanif['intIdSugerenciaHal']      = '';
            $boolControlaCupo = true;

            $arrayParametrosPlanif['idIntWifiSim']     = null;
            $arrayParametrosPlanif['idIntCouSim']      = null;
            $arrayParametrosPlanif['arraySimultaneos'] = null;

            $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($objDetalleSolicitud->getServicioId()->getId());

            if (is_object($objServicio))
            {
                /*Array con los Id's de los servicios internet Wifi para instalacion simultanea*/
                $arrayParametrosPlanif['idIntWifiSim']     = $this->getIdWifiInstSim($objServicio->getId());
                /*Array con los Id's de los servicios COU LINEAS TELEFONIA FIJA para instalacion simultanea*/
                $arrayParametrosPlanif['idIntCouSim']      = $this->getIdCouInstSim($objServicio->getId());
                /* Array con los ID's de los servicios para instalación simultanea en estado pendiente.*/
                $arrayParametrosPlanif['arraySimultaneos'] = $this->getServiciosInstalacionSimultanea($objServicio->getId());
            }

            $intJurisdicionId = $objDetalleSolicitud->getServicioId()->getPuntoId()->getPuntoCoberturaId()->getId();
            $intControlaCupo  = $objDetalleSolicitud->getServicioId()->getPuntoId()->getPuntoCoberturaId()->getCupo();

            if ( is_null($intControlaCupo) ||  $intControlaCupo <= 0 || $arrayParametrosPlanif['strEsHal'] === 'S' )
            {
                $boolControlaCupo = false;
            }
                    
            if (is_object($objServicio) && is_object($objServicio->getProductoId()) && $boolControlaCupo)
            {
                $intProductoId       = $objServicio->getProductoId()->getId();
                $intCaracteristicaId = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica('PRODUCTO CONTROLA CUPO');

                $objControlaCupo  =  $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findOneBy(array("productoId"       => $intProductoId,
                                                                            "caracteristicaId" => $intCaracteristicaId));
                if (is_object($objControlaCupo))
                {
                    $boolControlaCupo = false;
                }           
            }

            if (($arrayParametrosPlanif['strPrefijoEmpresa'] == "MD" || 
                 $arrayParametrosPlanif['strPrefijoEmpresa'] == "EN") && 
                 $intOpcion == 0 && $boolControlaCupo )
            {
                $strFechaPar    = substr($arrayParametrosPlanif['strFechaInicio'], 0, -1);
                $strFechaPar   .= "1";
                $strFechaPar    = str_replace("-", "/", $strFechaPar);
                $strFechaAgenda = str_replace("-", "/", $arrayParametrosPlanif['strFechaInicio']);    
                $intHoraCierre  = $this->container->getParameter('planificacion.mobile.hora_cierre');

                $arrayPar    = array(
                                        "strFecha"        => $strFechaPar,
                                        "strFechaAgenda"  => $strFechaAgenda,
                                        "intJurisdiccion" => $intJurisdicionId,
                                        "intHoraCierre"   => $intHoraCierre
                                    );

                $arrayCount  = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')->getCountDisponiblesWeb($arrayPar);

                if ($arrayCount == 0)
                {
                    $objRespuesta->setContent("No hay cupo disponible para este horario, seleccione otro horario por favor!");
                    return $objRespuesta;            
                }
            }

            $strNombreTecnico        = is_object($objServicio->getProductoId()) ? $objServicio->getProductoId()->getNombreTecnico():'';
            
            if ($intOpcion == 0)
            {
                $arrayResultado         = $this->coordinarPlanificacion($arrayParametrosPlanif);
                if(isset($arrayResultado['codigoRespuesta']) && !empty($arrayResultado['codigoRespuesta']) && $arrayResultado['codigoRespuesta'] > 0)
                {
                    $strStatusCoordinar = "OK";
                }
                else
                {
                    $strStatusCoordinar = "ERROR";
                }
            }
            else
            {
                $strStatusCoordinar = "OK";
            }

            $boolEsHousing = false;

            //Asigno responsable
            if (
                ($arrayParametrosPlanif['strEsHal'] <> "S" && 
                 $arrayParametrosPlanif['strParamResponsables'] <> "" && !$boolEsHousing && $strStatusCoordinar === "OK") ||
                 ($arrayParametrosPlanif['strEsHal'] === "S"        && !$boolEsHousing && $strStatusCoordinar === "OK")
               )
            {
                $arrayParametrosPlanif['objDetalleSolHist']       = $arrayResultado['entityDetalleSolHist'];
                $arrayParametrosPlanif['serviceRecursosRed']      = $this->serviceRecursosRed;
                $arrayParametrosPlanif['objServicioHistorial']    = $arrayResultado['entityServicioHistorial'];
                $arrayParametrosPlanif['planificacionAutomatica'] = true;
                $arrayResultado                                   = $this->asignarPlanificacion($arrayParametrosPlanif);
                $intIdDetalle                                     = $arrayResultado['idDetalle'];
                $intIdComunicacion                                = $arrayResultado['idComunicacion'];
                $strMensaje                                       = "Se planificó correctamente";
            }

            if($intIdDetalle <= 0 && $intIdComunicacion <= 0)
            {
                throw new \Exception("Error en el proceso PlanificarService->asignarPlanificacion() ".$arrayResultado['mensaje']);
            }
        }
        catch( \Exception $e )
        {
            $strMensaje = $e->getMessage();
            $strStatus  = 500;
            error_log('Error en PlanificarService->programarPlanificacion() => '.$strMensaje);
            $this->utilServicio->insertError( 'Telcos+',
                                              'Error en PlanificarService->programarPlanificacion() => ',
                                              $strMensaje,
                                              $strUser,
                                              $strIp
                                            );
        }
            
        $arrayRespuesta = array('mensaje'          => $strMensaje,
                                'idDetalle'      => $intIdDetalle,
                                'idComunicacion' => $intIdComunicacion,
                                'status'         => $strStatus);
        return $arrayRespuesta;
    }

    /**
    * Documentación para la función 'getTareasByProcesoAndTarea'.
    *
    * Función encargada de obtener tareas (AdmiTarea) según el nombre del proceso
    *
    * @author Andrés Montero <amontero@telconet.ec>
    * @version 1.0 27-10-2020
    *
    * @author Felix Caicedo <facaicedo@telconet.ec>
    * @version 1.1 21-05-2021 - Se valida si el servicio posee en los detalles de parámetro de la red GPON el
    *                           nombre de proceso para la generación tarea.
    *
    * @author Emmanuel Martillo <emartillo@telconet.ec>
    * @version 1.2 19-10-2022 - Se agrega parametrizacion de  los productos NetlifeCam.
    *
    */
    public function getTareasByProcesoAndTarea($arrayParametros)
    {
        $intServicioId        = $arrayParametros['servicioId'];
        $intIdSolicitud       = $arrayParametros['id_solicitud'];
        $strNombreTarea       = $arrayParametros['nombreTarea'];
        $strEstado            = $arrayParametros['estado'];
        $intStart             = $arrayParametros['start'];
        $intLimit             = $arrayParametros['limit'];
        $strAccion            = $arrayParametros['accion'];
        $strCodEmpresa        = ($arrayParametros['idEmpresa'] ? $arrayParametros['idEmpresa'] : "");
        $strPrefijoEmpresa    = ($arrayParametros['prefijoEmpresa'] ? $arrayParametros['prefijoEmpresa'] : "");
        $objEmComercial       = $this->emComercial;
        $objEmSoporte         = $this->emSoporte;
        $objEmInfraestructura = $this->emInfraestructura;
        $objEmGeneral         = $this->emGeneral;
        $strCodEmpresaTmp     = "";
        $strNombreProceso     = "";
        $arrayParametros      = array();
        $intIdProducto        = 0;
        $intIdServicio        = 0;
        $intIdDetSolicitud    = 0;
        //Variable para validar si el producto pertenece a NetlifeCam
        $arrayParamProducNetCam   = $this->serviceGeneral->paramProductosNetlifeCam();
        //Variable que describe la solicitud de requiere trabajo
        $strRequiereTrabajo                 = 'REQUIERE TRABAJO';
        $strSolicitudCableadoEstructurado   = 'SOLICITUD CABLEADO ESTRUCTURADO';
        
        $boolInstalacionSimultanea          = false;
        $boolRequiereFlujoSimultanea        = false;

        //Consultamos si el servicio tiene un servicio relacionado
        $objInfoServProdCaractRelacion = $objEmComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                        ->findOneBy( array("valor"  => $intServicioId,
                                        "estado" => 'Activo'));
        if(is_object($objInfoServProdCaractRelacion))
        {
            $intIdServicio                  = $objInfoServProdCaractRelacion->getServicioId();
            $intIdProductoCaracteristica    = $objInfoServProdCaractRelacion->getProductoCaracterisiticaId();
            
            $objAdmiCaracterisitica         = $objEmComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneBy( array("descripcionCaracteristica" => 'INSTALACION_SIMULTANEA',
                                                                    "estado" => "Activo"));
            if(is_object($objAdmiCaracterisitica))
            {
                $objAdmiProdCaracterisitica = $objEmComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                ->findOneBy( array("id"                => $intIdProductoCaracteristica,
                                                                    "caracteristicaId"  => $objAdmiCaracterisitica->getId(),
                                                                    "estado"            => "Activo"));
                if (is_object($objAdmiProdCaracterisitica))
                {
                    $intIdProducto = $objAdmiProdCaracterisitica->getProductoId();
                    $objProducto =  $objEmComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProducto);
                    if (is_object($objProducto))
                    {
                        $strNombreProducto = $objProducto->getDescripcionProducto();
                    }
                    $boolInstalacionSimultanea = true;
                    
                    //Consultamos la solicitud del servicio relacionado
                    $objSolicitud    = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->findOneBy(array("servicioId"    => $intIdServicio,
                                                            "estado"        => "PrePlanificada"));
                    if (is_object($objSolicitud))
                    {
                        $intIdDetSolicitud = $objSolicitud->getId();
                    }
                }
            }
        }
        
        //Consultamos si el servicio tiene servicio relacionado por Instalación Simultánea
        if ($boolInstalacionSimultanea)
        {
            //Consultamos si el producto del servicio relacionado requiere flujo
            $arrayParametrosRequiereFlujo =   $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->getOne("REQUIERE_FLUJO", 
                                                                "TECNICO", 
                                                                "", 
                                                                "", 
                                                                $strNombreProducto, 
                                                                "", 
                                                                "",
                                                                "",
                                                                "",
                                                                10
                                                                );
            if(is_array($arrayParametrosRequiereFlujo) && !empty($arrayParametrosRequiereFlujo))
            {
                $boolRequiereFlujoSimultanea = true;
            }
        }
        
        if ($intIdSolicitud) 
        {
            $objDetalleSolicitud = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($intIdSolicitud);
            $strTipoSolicitud    = strtolower($objDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());

            if ($strTipoSolicitud == "solicitud cambio equipo") 
            {
                $strNombreProceso = "SOLICITAR CAMBIO EQUIPO";
            }

            if ($strTipoSolicitud == "solicitud retiro equipo") 
            {
                $strNombreProceso = "SOLICITAR RETIRO EQUIPO";
            }
            
            if ($strTipoSolicitud == "solicitud agregar equipo") 
            {
                $strNombreProceso = "SOLICITUD AGREGAR EQUIPO";
            }

            if ($strTipoSolicitud == "solicitud agregar equipo masivo")
            {
                $strNombreProceso = "SOLICITUD AGREGAR EQUIPO";
            }

            if($strTipoSolicitud == "solicitud cambio equipo por soporte")
            {
                $strNombreProceso = "SOLICITUD CAMBIO EQUIPO POR SOPORTE";
            }

            if($strTipoSolicitud == "solicitud cambio equipo por soporte masivo")
            {
                $strNombreProceso = "SOLICITUD CAMBIO EQUIPO POR SOPORTE";
            }

            if ($strTipoSolicitud == "solicitud reubicacion") 
            {
                $strNombreProceso = "SOLICITUD REUBICACION";
            }
            
            if(!$strNombreProceso)
            {
                $objServicio = $objEmComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
                if(is_object($objServicio) && is_object($objServicio->getProductoId()) && $strCodEmpresa == '10' )
                {
                    //Consultamos si el producto requiere flujo ya que antes no lo tenia
                    $arrayParametrosRequiereFlujo =   $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->getOne("REQUIERE_FLUJO", 
                                                                                "TECNICO", 
                                                                                "", 
                                                                                "", 
                                                                                $objServicio->getProductoId()->getDescripcionProducto(),
                                                                                "", 
                                                                                "",
                                                                                "",
                                                                                "",
                                                                                10
                                                                                );
                    if(!is_array($arrayParametrosRequiereFlujo) && empty($arrayParametrosRequiereFlujo))
                    {
                        $boolRequiereFlujo = false;
                    }
                    else
                    {
                        $boolRequiereFlujo = true;
                    }
                        
                    $strProductoFlujo = $objServicio->getProductoId()->getDescripcionProducto();
                    
                    $objPlan = $objServicio->getPlanId();
                    if(is_object($objPlan))
                    {
                        $intIdPlan = $objPlan->getId();
                        $arrayResultadoNetlifecam  = $objEmComercial->getRepository('schemaBundle:InfoPlanDet')
                                                        ->getProductosPlan(array("intIdPlan"        => $intIdPlan,
                                                                                "strNombreTecnico" => "CAMARA IP"));

                        if(!empty($arrayResultadoNetlifecam))
                        {
                            $strNombreProceso = "SOLICITAR NUEVO SERVICIO NETLIFECAM";
                        }
                    }
                    
                    if($strPrefijoEmpresa == 'TN' && strpos($objServicio->getProductoId()->getGrupo(),'DATACENTER')!==false)
                    {
                        //Mostrar TAREA para PROCESO DC previo a asignacion de recursos de red para INTERNET DC                        
                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                    }
                }
            }
        }

        //obtengo el nombre del proceso del servicio por detalles del parametro de la red GPON
        $objServicio = $objEmComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
        $strNombreTecnico = is_object($objServicio->getProductoId()) ? 
                            $objServicio->getProductoId()->getNombreTecnico() : null;
        if(is_object($objServicio) && is_object($objServicio->getProductoId()))
        {
            $arrayParDetCamSafeCity = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                       ->getOne('NUEVA_RED_GPON_TN',
                                                'COMERCIAL',
                                                '',
                                                'TAREA DE INSTALACION DEL SERVICIO',
                                                $objServicio->getProductoId()->getId(),
                                                '',
                                                '',
                                                '',
                                                '',
                                                $strCodEmpresa);
            if( isset($arrayParDetCamSafeCity) && !empty($arrayParDetCamSafeCity)
                && isset($arrayParDetCamSafeCity['valor2'])&& !empty($arrayParDetCamSafeCity['valor2']) )
            {
                $strNombreProceso = $arrayParDetCamSafeCity['valor2'];
            }
        }

        if (!$strNombreProceso) 
        {
            $objInfoServicio = $objEmComercial->getRepository('schemaBundle:InfoServicio')->find($intServicioId);
            //SE VALIDA QUE EXISTA ID_PLAN EN LA TABLA INFO_SERVICIO PARA CONTINUAR CON FLUJO NETLIFECAM
            if ($objInfoServicio->getPlanId()) 
            {
                $strNombrePlan = $objInfoServicio->getPlanId()->getNombrePlan();
            }
            else 
            {
                $strNombrePlan = '';
            }

            $strCodEmpresaTmp = $objEmComercial->getRepository('schemaBundle:AdmiParametroDet')->getEmpresaEquivalente(
                                                                                                                       $intServicioId,
                                                                                                                       $strPrefijoEmpresa
                                                                                                                      );
            
            $objInfoServicioTecnico = $objEmComercial->getRepository('schemaBundle:InfoServicioTecnico')->findOneByServicioId($intServicioId);
            if($objInfoServicioTecnico)
            {
                $boolUmFttx = false;
                if($objInfoServicioTecnico->getUltimaMillaId() && !$boolRequiereFlujo)
                {
                    $objTipoMedio = $objEmInfraestructura->getRepository('schemaBundle:AdmiTipoMedio')
                                                         ->find($objInfoServicioTecnico->getUltimaMillaId());

                    if ($objTipoMedio->getNombreTipoMedio() == "Cobre") 
                    {
                        if (strrpos($strNombrePlan, "ADSL"))
                        {
                            $strNombreProceso = "SOLICITAR NUEVO SERVICIO COBRE ADSL"; 
                        } 
                        else
                        {
                            $strNombreProceso = "SOLICITAR NUEVO SERVICIO COBRE VDSL";
                        }  
                    }
                    else if($objTipoMedio->getNombreTipoMedio() == "Fibra Optica")
                    {
                        /* Si el producto es NetlifeCam, la tarea sera la siguiente*/
                        if(in_array($strNombreTecnico, $arrayParamProducNetCam))
                        {
                            $objNombreProceso =  $this->emComercial
                                                                    ->getRepository('schemaBundle:AdmiParametroDet')
                                                                    ->getOne('PROYECTO NETLIFECAM',
                                                                            'INFRAESTRUCTURA',
                                                                            '',
                                                                            'PARAMETRIZACION DE NOMBRES TECNICOS DE PRODUCTOS NETLIFE CAM',
                                                                            $strNombreTecnico,
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            '18');
                            $strNombreProceso = $objNombreProceso['valor2'];
                        } 
                        else
                        {
                            $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                        } 
                    }
                    else if($objTipoMedio->getNombreTipoMedio() == "FTTx")
                    {
                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO FTTx";
                        $boolUmFttx    = true;
                    }
                    else
                    {
                        $strNombreProceso = "SOLICITAR NUEVO SERVICIO RADIO";
                    }
                }
                else if (is_object($objServicio->getProductoId()) &&
                    $objServicio->getProductoId()->getDescripcionProducto() == 'WIFI Alquiler Equipos')
                {
                    $strNombreProceso = "TAREA DE RADIOENLACE - INSTALACION WIFI ALQUILER DE EQUIPOS";
                }
                //Si el producto es Cableado Estructurado se asigna el nombre de proceso para ese producto
                else if (is_object($objServicio->getProductoId()) && $boolRequiereFlujo)
                {
                    $strNombreProceso = $strSolicitudCableadoEstructurado;
                }
                //Si no tiene última milla, por defecto se escoge el proceso de fibra
                else
                {
                    $strNombreProceso = "SOLICITAR NUEVO SERVICIO FIBRA";
                }

                if ($strCodEmpresaTmp && !$boolUmFttx)
                {
                    $strCodEmpresa = $strCodEmpresaTmp['id'];
                }

                $objProceso  = $objEmSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($strNombreProceso);
                //Si el producto requiere trabajo se valida cuántos departamentos lo requieren para visualizar en la pantalla de tareas
                if ($strNombreProceso == $strSolicitudCableadoEstructurado)
                {
                    if (is_object($objProceso))
                    {
                        $arrayParametrosTareasRqTrab['procesoId']       = $objProceso->getId();
                        $arrayParametrosTareasRqTrab['codEmpresa']      = $strCodEmpresa;
                        $arrayParametrosTareasRqTrab['estado']          = $strEstado;
                        $arrayParametrosTareasRqTrab['start']           = $intStart;
                        $arrayParametrosTareasRqTrab['limit']           = $intLimit;
                        $arrayParametrosTareasRqTrab['servicioId']      = $intServicioId;
                        $arrayParametrosTareasRqTrab['idSolicitud']     = $intIdSolicitud;
                        $arrayParametrosTareasRqTrab['tarea']           = $strNombreTarea;
                        $arrayParametrosTareasRqTrab['caracteristica']  = $strRequiereTrabajo;
                        $arrayParametrosTareasRqTrab['producto']        = $strProductoFlujo;
                    
                        $objJson    = $objEmSoporte->getRepository('schemaBundle:AdmiTarea')
                                                   ->generarJsonTareasConMaterialesByProcesoRequiereTrabajo(
                                                                                                            $objEmComercial,
                                                                                                            $arrayParametrosTareasRqTrab
                                                                                                           );
                    }
                    else
                    {
                        $objJson= '{"total":"0","encontrados":[]}';
                    }
                }
                else
                {
                    //Consultamos si el servicio relacionado requiere flujo y si esta relacionado con un servicio de instalación simultánea
                    if ($boolRequiereFlujoSimultanea && $strAccion !== 'Replanificar')
                    {
                        //Generamos las tareas tanto para el servicio principal como para el servicio relacionado por instalación simultánea
                        $arrayParametrosTareasRqTrab['boolRequiereFlujoSimultanea'] = 'SI';
                        
                        $objProceso  = $objEmSoporte->getRepository('schemaBundle:AdmiProceso')
                                                    ->findOneByNombreProceso($strSolicitudCableadoEstructurado);
                        if (is_object($objProceso))
                        {
                            $arrayParametrosTareasRqTrab['procesoIdSimultaneo'] = $objProceso->getId();
                        }
                        
                        $arrayParametrosTareasRqTrab['codEmpresa']      = $strCodEmpresa;
                        $arrayParametrosTareasRqTrab['estado']          = $strEstado;
                        $arrayParametrosTareasRqTrab['start']           = $intStart;
                        $arrayParametrosTareasRqTrab['limit']           = $intLimit;
                        $arrayParametrosTareasRqTrab['servicioId']      = $intIdServicio;
                        $arrayParametrosTareasRqTrab['idSolicitud']     = $intIdDetSolicitud;
                        $arrayParametrosTareasRqTrab['tarea']           = $strNombreTarea;
                        $arrayParametrosTareasRqTrab['caracteristica']  = $strRequiereTrabajo;
                        $arrayParametrosTareasRqTrab['producto']        = $strNombreProducto;
                        $arrayParametrosTareasRqTrab['procesoId']       = $objProceso->getId();

                        $objJson = $objEmSoporte->getRepository('schemaBundle:AdmiTarea')
                                                ->generarJsonTareasConMaterialesByProcesoRequiereTrabajo(
                                                                                                         $objEmComercial, 
                                                                                                         $arrayParametrosTareasRqTrab
                                                                                                        );
                    }
                    else
                    {
                        $objJson = $objEmSoporte->getRepository('schemaBundle:AdmiTarea')
                                                ->generarJsonTareasConMaterialesByProceso(
                                                                                          $objEmComercial,
                                                                                          $intStart,
                                                                                          $intLimit,
                                                                                          $strEstado,
                                                                                          $objProceso->getId(),
                                                                                          $strCodEmpresa
                                                                                         );
                    }
                }
            }
            else
            {
                $objJson= '{"total":"0","encontrados":[]}';
            }
        }
        else
        {
            $objProceso  = $objEmSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso($strNombreProceso);
            $objJson     = $objEmSoporte->getRepository('schemaBundle:AdmiTarea')
                                        ->generarJsonTareasConMaterialesByProceso(
                                                                                  $objEmComercial,
                                                                                  $intStart,
                                                                                  $intLimit,
                                                                                  $strEstado,
                                                                                  $objProceso->getId(),
                                                                                  $strCodEmpresa
                                                                                 );
        }
        return $objJson;
    }

    /**
     * Funcion que actualiza los estados de los serivicio adionales de cableado ethernet
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 10-03-2021 - Version Inicial.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.1 02-07-2021 - Se invoca a nuevo metodo para creacion de tareas adicionales 
     *
     * @param $arrayDatosEstado -> Contiene todos los datos del punto y los estados
     *        arrayParametrosTarea -> Contiene la informacion para crear la tarea en CE empaquetado.
     * 
    */
    public function actualizaEstadoProductosAdicionales($arrayDatosEstado, $arrayParametrosTarea)
    {
        // Obtenemos los parametros para producto adicional de Cableado ethernet
        $arrayParametroTipos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->get('VALIDA_PROD_ADICIONAL','COMERCIAL','',
                'Solicitud cableado ethernet','','','','','','18');
        if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
        {
            $objCableParametro = $arrayParametroTipos[0];
        }
        // Validamos si debe crearse tareas
        $strCreaTareas = 'N';
        if (is_array($arrayParametrosTarea) && !empty($arrayParametrosTarea))
        {
            $strCreaTareas = 'S';
        }
        // Verificamos si posee servicios de CE adicionales
        $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->findServiciosByPuntoAndEstado($arrayDatosEstado['intIdPunto'], null, null);
        foreach($arrayServicios['registros'] as $objServicio)
        {
            $strProducto = $objServicio->getProductoId();
            // Actualizamos el estado de los servicios adicionales
            if (!empty($strProducto) && $strProducto->getId() == $objCableParametro['valor1'] &&
                $objServicio->getId() != $arrayDatosEstado['intIdServicio'])
            {
                $entityServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findOneById($objServicio->getId());
                if ($entityServicio->getEstado() == $arrayDatosEstado['strEstadoAnt'])
                {
                    $entityServicio->setEstado($arrayDatosEstado['strEstado']);
                    $this->emComercial->persist($entityServicio);
                    $this->emComercial->flush();

                    //GUARDAR INFO SERVICIO HISTORIAL
                    $entityServicioHist = new InfoServicioHistorial();
                    $entityServicioHist->setServicioId($entityServicio);
                    $entityServicioHist->setIpCreacion($arrayDatosEstado['strIpCreacion']);
                    $entityServicioHist->setFeCreacion(new \DateTime('now'));
                    $entityServicioHist->setUsrCreacion($arrayDatosEstado['strUsrCreacion']);
                    $entityServicioHist->setEstado($arrayDatosEstado['strEstado']);
                    $entityServicioHist->setObservacion($arrayDatosEstado['strObservacionServicio']);
                    $this->emComercial->persist($entityServicioHist);
                    $this->emComercial->flush();
                }
            }
            // Actualizamos los estados de los detalles de la solicitud seleccionada
            $arrayDetalles = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                ->findByParameters(array('servicioId' => $objServicio->getId()));
            foreach($arrayDetalles as $detalle)
            {
                $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                ->findOneById($detalle['tipoSolicitudId']);
                if ($objTipoSolicitud->getDescripcionSolicitud() == $objCableParametro['valor2'])
                {
                    $entityDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneById($detalle['id']);
                    // Creamos la tarea antes de quitarle el estado de planificada
                    if (($strCreaTareas == 'S' && $arrayDatosEstado['strSolCableado'] == 'NO') || 
                        ($strCreaTareas == 'S' && $objServicio->getId() != $arrayDatosEstado['intIdServicio']))
                    {
                        // Invocamos al metodo de crear tareas adicionales
                        $arrayParametrosTarea['id'] = $entityDetSolicitud->getId();
                        $arrayParametrosTarea['objSolicitud'] = $entityDetSolicitud;
                        $arrayParametrosTarea['objInfoServicio'] = $objServicio;
                        $arrayParametrosTarea['strTipoSolicitud'] = "solicitud de instalacion cableado ethernet";
                        $this->crearTareaServicioAdicional($arrayParametrosTarea);
                    }
                    // Cambiamos el estado de las solicitudes
                    if ($entityDetSolicitud->getEstado() == $arrayDatosEstado['strEstadoAnt'])
                    {
                        $entityDetSolicitud->setEstado($arrayDatosEstado['strEstado']);
                        $this->emComercial->persist($entityDetSolicitud);
                        $this->emComercial->flush();
                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityDetalleSolHist= new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($entityDetSolicitud);
                        $entityDetalleSolHist->setFeIniPlan(new \DateTime($arrayDatosEstado['strFechaInicio']));
                        $entityDetalleSolHist->setFeFinPlan(new \DateTime($arrayDatosEstado['strFechaFin']));
                        $entityDetalleSolHist->setObservacion(substr($arrayDatosEstado['strObservacionSolicitud'], 0, 1499));
                        $entityDetalleSolHist->setIpCreacion($arrayDatosEstado['strIpCreacion']);
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($arrayDatosEstado['strUsrCreacion']);
                        $entityDetalleSolHist->setEstado($arrayDatosEstado['strEstado']);
                        $this->emComercial->persist($entityDetalleSolHist);
                        $this->emComercial->flush();
                    }
                }
            }
        }
    }

    /**
     * guardarAdjuntoImagen
     *
     * Metodo encargado de guardar los archivos (imagenes) enrutados a la solicitud
     * validando el directorio y sus propiedades.
     *
     *
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 12-03-2021 
     */ 
    public function guardarAdjuntoImagen($arrayParametros)
    {        
        $strFeCreacion      = new \DateTime('now');
        $objServicio        = $arrayParametros['servicio'];
        $intIdSolicitud         = $arrayParametros['idTarea'];
        $strOrigenMateriales     = $arrayParametros['origenMateriales'];
        $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
        $strUser            = $arrayParametros['strUser'];
        $strIdEmpresa       = $arrayParametros['strIdEmpresa'];
        $arrayArchivos      = $arrayParametros['arrayArchivos'];
        $strApp             = "";
        $arrayPathAdicional = [];
        $strSubModulo       = "";
        $this->utilServicio       = $this->container->get('schema.Util');
        $arrayRespuesta     = array();
        $this->emComunicacion->getConnection()->beginTransaction();

        try
        {
            if ($arrayArchivos == "" || $arrayArchivos == null)
            {
                throw new \Exception('No existen archivos para cargar, favor revisar nuevamente!!');
            }

            foreach($arrayArchivos as $objArchivo)
            {
                if (is_object($objArchivo))
                {
                    $strNameFile                        = $objArchivo->getClientOriginalName();
                    $arrayPartsNombreArchivo            = explode('.', $strNameFile);
                    $strLast                            = array_pop($arrayPartsNombreArchivo);
                    $arrayPartsNombreArchivo            = array(implode('_', $arrayPartsNombreArchivo), $strLast);
                    $strNombreArchivo                   = $arrayPartsNombreArchivo[0];
                    $strExtArchivo                      = $arrayPartsNombreArchivo[1];
                    $strTipo                            = $strExtArchivo;
                    $strPrefijo                         = substr(md5(uniqid(rand())),0,6);
                    $strNuevoNombre                     = $strNombreArchivo . "_" . $strPrefijo . "." . $strExtArchivo;
                    $strTofind                          = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                    $strReplac                          = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                    $strNuevoNombre                     = strtr($strNuevoNombre,$strTofind,$strReplac);

                    //Si proviene de subida a partir del servicio del cliente o de la creacion de un caso
                    if($objServicio || $strOrigenMateriales == 'S')
                    {
                        $strApp       = "TelcosWeb";

                        $strBand      = "";
                        if($strOrigenMateriales == 'S')
                        {
                            $strSubModulo = "Solicitud/Autorizaciones";
                            $strBand      = "M";
                        }

                        if($strBand == "M")
                        {
                            $strSolicitud = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->find($intIdSolicitud);
                        }
                        
                            //####################################
                            //INICIO DE SUBIR ARCHIVO AL NFS >>>>>
                            //####################################

                            $strFile         = base64_encode(file_get_contents($objArchivo->getPathName()));
                            $arrayParamNfs   = array(
                                                    'prefijoEmpresa'       => $strPrefijoEmpresa,
                                                    'strApp'               => $strApp,
                                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                                    'strBase64'            => $strFile,
                                                    'strNombreArchivo'     => $strNuevoNombre,
                                                    'strUsrCreacion'       => $strUser,
                                                    'strSubModulo'         => $strSubModulo);
                            
                            
                            $arrayRespNfs = $this->utilServicio->guardarArchivosNfs($arrayParamNfs);

                            if ($arrayRespNfs['intStatus'] == 200 || $strSolicitud != "")
                            {
                                $strFicheroSubido = $arrayRespNfs['strUrlArchivo'];
                                $arrayRespuesta     = array('status' => 'Ok', 
                                                            'mensaje' => 'Los archivos se subieron exitosamente', 
                                                            'success' => true);
                            }
                            else
                            {
                                throw new \Exception('Ocurrio un error al subir archivo al servidor Nfs : '.$arrayRespNfs['strMensaje']);
                            }

                            //##################################
                            //<<<<< FIN DE SUBIR ARCHIVO AL NFS
                            //##################################

                            $objInfoDocumento = new InfoDocumento();

                            if($strBand == "M")
                            {
                                $objInfoDocumento->setNombreDocumento('Adjunto Materiales');
                                $objInfoDocumento->setMensaje('Documento que se adjunta a una solicitud de Materiales');
                            }
                            $objInfoDocumento->setUbicacionFisicaDocumento($strFicheroSubido);
                            $objInfoDocumento->setUbicacionLogicaDocumento($strNuevoNombre);

                            $objInfoDocumento->setEstado('Activo');
                            $objInfoDocumento->setFeCreacion($strFeCreacion);
                            $objInfoDocumento->setFechaDocumento($strFeCreacion);
                            $objInfoDocumento->setIpCreacion('127.0.0.1');
                            $objInfoDocumento->setUsrCreacion($strUser);
                            $objInfoDocumento->setEmpresaCod($strIdEmpresa);

                            $strTipoDoc=  strtoupper($strTipo);
                            if($strTipoDoc=='JPG' || $strTipo=='JPEG')
                            {
                               $strTipoDoc = "JPG" ;
                            }

                            $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                                     ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento'=> $strTipoDoc));

                            if( $objTipoDocumento != null)
                            {
                                $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                            }
                            else
                            {
                                //Inserto registro con la extension del archivo a subirse
                                $objAdmiTipoDocumento = new AdmiTipoDocumento();
                                $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($strTipoDoc));
                                $objAdmiTipoDocumento->setTipoMime(strtoupper($strTipoDoc));
                                $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.$strTipoDoc);
                                $objAdmiTipoDocumento->setEstado('Activo');
                                $objAdmiTipoDocumento->setUsrCreacion( $strUser );
                                $objAdmiTipoDocumento->setFeCreacion( $strFeCreacion );
                                $this->emComunicacion->persist( $objAdmiTipoDocumento );
                                $this->emComunicacion->flush();
                                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);
                            }
                            
                            unlink($objArchivo->getPathName());

                            $this->emComunicacion->persist($objInfoDocumento);
                            $this->emComunicacion->flush();

                            //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                            $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                            $objInfoDocumentoRelacion->setModulo('PLANIFICACION');
                            $objInfoDocumentoRelacion->setServicioId($objServicio);
                            $objInfoDocumentoRelacion->setEstado('Activo');
                            $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                            $objInfoDocumentoRelacion->setUsrCreacion($strUser);


                            $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());

                            $this->emComunicacion->persist($objInfoDocumentoRelacion);
                            $this->emComunicacion->flush();
                        
                    }
                }
            }
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->commit();
            }
            $this->emComunicacion->getConnection()->close();

            //REGISTRAMOS EN LOG
            $arrayParametrosLog['enterpriseCode']   = $strIdEmpresa; 
            $arrayParametrosLog['logType']          = "1";
            $arrayParametrosLog['logOrigin']        = "TELCOS";
            $arrayParametrosLog['application']      = "TELCOS";
            $arrayParametrosLog['appClass']         = "PlanificarService";
            $arrayParametrosLog['appMethod']        = "guardarAdjuntoImagen";
            $arrayParametrosLog['messageUser']      = "No aplica.";
            $arrayParametrosLog['status']           = "Fallido";
            $arrayParametrosLog['descriptionError'] = "Se guarda archivo correctamente atravez de microservicio de Nfs (".$strFicheroSubido.")";
            $arrayParametrosLog['inParameters']     = json_encode($arrayParametros);
            $arrayParametrosLog['creationUser']     = "TELCOS";
            
            $this->utilServicio->insertLog($arrayParametrosLog);
            return $arrayRespuesta;
       }
       catch(\Exception $objE)
       {
           $strMensajeError  = 'Ha ocurrido un error, por favor reporte a Sistemas';

           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }
           $this->emComunicacion->getConnection()->close();

           if (strpos(strtolower($objE->getMessage()), strtolower("Archivo con extensión")) >= 0)
           {
               $strMensajeError =  $objE->getMessage();
           }
           $this->utilServicio->insertError('Telcos+',
                                           'PlanificarService.guardarAdjuntoImagen',
                                           'Error PlanificarService.guardarAdjuntoImagen:'.$objE->getMessage(),
                                           $strUser,
                                           '127.0.0.1');
            error_log($objE->getMessage());
           $arrayRespuesta     = array('status' => 'Error', 'mensaje' => $strMensajeError, 'success' => 'false');
           return $arrayRespuesta;
       }
    }


    /**
     * generarDirectorioFechaActualAction
     * Metodo encargado de verificar y generar el directorio de carpetas por fecha en el directorio enviado como parametro.
     * @param  string $strDirectorio
     * @return string $strDirectorioGenerado
     * 
     * @author Mario Ayerve <mayerve@telconet.ec>
     * @version 1.0 12-03-2021 
     *
     */
    public function generarDirectorioFechaActual($strDirectorio)
    {
        $strAnioActual   = date("Y");
        $strMesActual    = date("m");
        $strDiaActual    = date("d");

        $strDirAnioActual      = $strDirectorio.$strAnioActual."/";
        $strDirMesActual       = $strDirAnioActual.$strMesActual."/";
        $strDirDiaActual       = $strDirMesActual.$strDiaActual."/";
        $strDirectorioGenerado ="";

        $boolGeneraDirectorio = true;

        if(file_exists($strDirectorio) && is_dir($strDirectorio))
        {
            do
            {
                if(file_exists($strDirAnioActual) && is_dir($strDirAnioActual))
                {
                    if(file_exists($strDirMesActual) && is_dir($strDirMesActual))
                    {
                        if(file_exists($strDirDiaActual) && is_dir($strDirDiaActual))
                        {
                            $boolGeneraDirectorio = false;
                        }
                        else
                        {
                            mkdir($strDirDiaActual, 0777, true);
                        }
                    }
                    else
                    {
                        mkdir($strDirMesActual, 0777, true);
                    }

                }
                else
                {
                    mkdir($strDirAnioActual, 0777, true);
                }
            }while($boolGeneraDirectorio);

            $strDirectorioGenerado = $strAnioActual."/".$strMesActual."/".$strDiaActual."/";

            return $strDirectorioGenerado;
        }
        else
        {
            return $strDirectorioGenerado;
        }
    }

    /**
     * Funcion que actualiza los estados de los serivicio adionales manuales
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 28-06-2021 - Version Inicial.
     *
     * @param $arrayDatosEstado -> Contiene todos los datos del punto y los estados
     *        arrayParametrosTarea -> Contiene la informacion para crear la tarea en CE empaquetado.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.1 05-10-2021. Se agregó un proceso que permite llamar un nuevo WS de HAL para la planificación de procesos adicionales
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.2 29-06-2022 - Mejora la validacion para incluir los EDB en simultaneo por HAL.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 1.3 06-10-2022. Se corrigió el proceso que entraba en excepción al realizar una reeplanificación de servicios.
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 1.4 19-10-2022 - Se agrega parametrizacion de  los productos NetlifeCam.
    */
    public function actualizaEstProdAdiManuales($arrayDatosEstado, $arrayParametrosTarea)
    {
        $intIdPunto      = $arrayDatosEstado['intIdPunto'];
        $intIdServicio   = $arrayDatosEstado['intIdServicio'];
        $strEstadoNue    = $arrayDatosEstado['strEstado'];
        $strEstadoAnt    = $arrayDatosEstado['strEstadoAnt'];
        $strFechaIni     = $arrayDatosEstado['strFechaInicio'];
        $strFechaFin     = $arrayDatosEstado['strFechaFin'];
        $strObsServicio  = $arrayDatosEstado['strObservacionServicio'];
        $strObsSolicitud = $arrayDatosEstado['strObservacionSolicitud'];
        $strIpCreacion   = $arrayDatosEstado['strIpCreacion'];
        $strUsrCreacion  = $arrayDatosEstado['strUsrCreacion'];
        $intIdEmpresa    = $arrayDatosEstado['intIdEmpresa'];
        $strSolCableado  = $arrayDatosEstado['strSolCableado'];
        $strEsHal        = $arrayDatosEstado['strEsHal'];
        $strDescripcion  = "";
        $arrayParamProducNetCam   = $this->serviceGeneral->paramProductosNetlifeCam();
        $objIdTarea      = ""; 
        // Validamos si debe crearse tareas
        $strCreaTareas = 'N';
        if (is_array($arrayParametrosTarea) && !empty($arrayParametrosTarea))
        {
            $strCreaTareas    = 'S';
        }
        // Obtendremos los productos adicionales manuales que se deben cancelar
        if ($strEsHal == "S")
        {
            $strDescripcion = 'Productos para planificacion con HAL';
        }
        else
        {
            $strDescripcion  = 'Productos adicionales manuales para activar';
        }
        $arrayProducAdicioManuales = array();
        $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                    $strDescripcion,'','','','','',$intIdEmpresa);
        if (is_array($arrayParamValores) && !empty($arrayParamValores))
        {
            $arrayProducAdicioManuales = $this->utilServicio->obtenerValoresParametro($arrayParamValores);
        }
        // Obtenemos los tipos de solicitudes permitidos para esos productos manuales
        $arrayTiposSolicitudes = array();
        $arrayTareasComplementarias = array();
        $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                    'Solicitudes anexas a los servicios adicionales manuales',
                                    '','','','','',$intIdEmpresa);
        if (is_array($arrayParamValores) && !empty($arrayParamValores))
        {
            $arrayTiposSolicitudes = $this->utilServicio->obtenerValoresParametro($arrayParamValores);
        }
        // Verificamos si posee servicios adicionales
        $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                ->findServiciosByPuntoAndEstado($intIdPunto,
                                                                null,
                                                                null);
        foreach($arrayServicios['registros'] as $objServicio)
        {
            $strProducto = $objServicio->getProductoId();
            // Actualizamos el estado de los servicios adicionales de cableado ethernet
            if (!empty($strProducto) &&
                in_array($strProducto->getId(), $arrayProducAdicioManuales) &&
                $objServicio->getEstado() == $strEstadoAnt &&
                $objServicio->getId() !== $intIdServicio)
            {
                $objServicio->setEstado($strEstadoNue);
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();

                //GUARDAR INFO SERVICIO HISTORIAL
                $entityServicioHist = new InfoServicioHistorial();
                $entityServicioHist->setServicioId($objServicio);
                $entityServicioHist->setIpCreacion($strIpCreacion);
                $entityServicioHist->setFeCreacion(new \DateTime('now'));
                $entityServicioHist->setUsrCreacion($strUsrCreacion);
                $entityServicioHist->setEstado($strEstadoNue);
                $entityServicioHist->setObservacion($strObsServicio);
                $this->emComercial->persist($entityServicioHist);
                $this->emComercial->flush();

                // Si es HAL actualizamos la informacion del historico
                if ($strEsHal == "S" && $strCreaTareas == "S")
                {
                    $strObservacionHal  = $arrayDatosEstado['strObservacionHal'];
                    $arrayHistorialesServicio = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                                                ->findHistServicioIdEstado(array('intIdServicio' => $objServicio->getId(),
                                                                                 'strEstado'     => $strEstadoAnt));
                    if(isset($arrayHistorialesServicio) && !empty($arrayHistorialesServicio))
                    {
                        $objAntHistServicio = $arrayHistorialesServicio[0];
                        if (is_object($objAntHistServicio))
                        {
                            $objAntHistServicio->setObservacion($strObservacionHal);
                            $this->emComercial->persist($objAntHistServicio);
                            $this->emComercial->flush();
                        }
                    }
                }
            }
            // Actualizamos los estados de los detalles de la solicitud seleccionada
            $strPlanServicio = $objServicio->getPlanId();
            if (!empty($strPlanServicio) || (!empty($strProducto) &&                
                 in_array($strProducto->getId(), $arrayProducAdicioManuales)))
            {
                $arrayDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->findByParameters(array('servicioId' => $objServicio->getId(),
                                                                 'estado'     => $strEstadoAnt));
                foreach($arrayDetSolicitud as $objSolicitud)
                {
                    //Se agrega Validacion para los productos Netlife Cam
                    $strNombreTecnico = is_object($objServicio->getProductoId()) ? 
                                        $objServicio->getProductoId()->getNombreTecnico() : null;
                    if(in_array($strNombreTecnico,$arrayParamProducNetCam))
                    {
                        $objNombreProceso =  $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('PROYECTO NETLIFECAM','INFRAESTRUCTURA','',
                                                'PARAMETRIZACION DE NOMBRES TECNICOS DE PRODUCTOS NETLIFE CAM',
                                                        $strNombreTecnico,'','','','','18');
                        $strNombreProceso = $objNombreProceso['valor2'];
                        $objProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                                            ->findOneByNombreProceso($strNombreProceso);
                        $objTareaNetlife = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                            ->findOneBy(array(
                                                'procesoId'   => $objProceso->getId()
                                            ));
                        $objIdTarea = $objTareaNetlife->getId();
                    }
                    $intIdTipoSolicitud = $objSolicitud['tipoSolicitudId'];
                    if (in_array($intIdTipoSolicitud, $arrayTiposSolicitudes))
                    {                
                        $objDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneById($objSolicitud['id']);
                        $objTipoSolicitud = $this->emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneById($intIdTipoSolicitud);
                        // Creamos la tarea antes de quitarle el estado de planificada
                        if ($strCreaTareas == 'S' && $objTipoSolicitud->getDescripcionSolicitud() != '' &&
                            ($objServicio->getId() != $intIdServicio || $strSolCableado == 'NO'))
                        {
                            
                            // Obtenemos la tarea asociada a la solicitud
                            $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->get('PRODUCTOS ADICIONALES MANUALES',
                                                            'COMERCIAL','',
                                                            'Listado de tareas asociadas a la solicitud',
                                                            $objTipoSolicitud->getId(),
                                                            $objTipoSolicitud->getDescripcionSolicitud(),
                                                            '',$objIdTarea,'',$intIdEmpresa);
                            if(isset($arrayParamValores) && !empty($arrayParamValores))
                            {
                                $arrayDatos = $arrayParamValores[0];
                                $intIdTarea = $arrayDatos["valor4"];
                            }
                            $objTarea = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                                            ->findOneById($intIdTarea);
                            // Invocamos al metodo de crear tareas adicionales
                            $arrayParametrosTarea['entitytarea'] = $objTarea;
                            $arrayParametrosTarea['id'] = $objDetSolicitud->getId();
                            $arrayParametrosTarea['objSolicitud'] = $objDetSolicitud;
                            $arrayParametrosTarea['objInfoServicio'] = $objServicio;
                            $arrayParametrosTarea['strTipoSolicitud'] = strtolower($objTipoSolicitud->getDescripcionSolicitud());
                            $arrayParametrosTarea['strEsGestionSimultanea'] = "SI";
                            $arrayParametrosTarea['strEsHal'] = "N";
                            $strInfoEquiposTecnicoTarea     = "";
                            $objEntityServicie = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findOneById($objServicio->getId());
                            if($objEntityServicie->getId() == $objServicio->getId() &&
                            in_array($strNombreTecnico,$arrayParamProducNetCam))
                            {
                                $arrayParamsModelosEquiposNetlife   = array();
                                $arrayParamsModelosEquiposNetlife  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                    ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_MD','','','',
                                                                                           'MODELOS_EQUIPOS','','',$strNombreTecnico,
                                                                                           '','18');
                                foreach($arrayParamsModelosEquiposNetlife as $arrayParamModeloEquipoNetlife)
                                {
                                    $arrayParamsModelosEquiposNetlifeIns[] = $arrayParamModeloEquipoNetlife['valor5'];
                                }
                                $strModelosEquiposNetlifeCam = implode(',', $arrayParamsModelosEquiposNetlifeIns);
                                $strInfoEquiposTecnicoTarea     .=  "<div style ='color:red'>"
                                                                ."Servicio con ".
                                                                $objServicio->getProductoId()->getDescripcionProducto()
                                                                .",<br> se requiere agregar el equipo<br>"
                                                                ."con cualquiera de los siguientes<br>"
                                                                ."modelos:<br>"
                                                                .$strModelosEquiposNetlifeCam."</div>";
                                $arrayParametrosTarea['strObservacionTecnico']  = (unset)$arrayParametrosTarea['strObservacionTecnico'] ;
                                $arrayParametrosTarea['strObservacionTecnico']  = $strInfoEquiposTecnicoTarea;
                            }
                            if($arrayParametrosTarea['boolEsReplanifHal'] === true && $strEsHal == "S") 
                            {
                              $objDetSol        = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                              ->findOneBy(array('detalleSolicitudId' => $objSolicitud['id']));

                              $arrayParametrosTarea['intIdDetalleExistente'] = $objDetSol->getId();
                            }
                            $arrayResultadoServAdicional = $this->crearTareaServicioAdicional($arrayParametrosTarea);
                            if($strEsHal == "S" && isset($arrayResultadoServAdicional['objInfoComunicacion'])
                            && !empty($arrayResultadoServAdicional['objInfoComunicacion']))
                            {
                                $arrayTareasComplementarias[] = $arrayResultadoServAdicional['objInfoComunicacion']->getId();
                            }
                        }
                        // Cambiamos el estado de las solicitudes
                        $objDetSolicitud->setEstado($strEstadoNue);
                        $this->emComercial->persist($objDetSolicitud);
                        $this->emComercial->flush();
                        // Si es HAL actualizamos la informacion del historico
                        if ($strEsHal == "S" && $strCreaTareas == "S")
                        {
                            $strFechaHal   = $arrayDatosEstado['strFechaHal'];
                            $strHoraIniHal = $arrayDatosEstado['strHoraIniHal'];
                            $strHoraFinHal = $arrayDatosEstado['strHoraFinHal'];
                            $objDateFechaIniHal = date_create(date('Y-m-d H:i', strtotime($strFechaHal.' '.$strHoraIniHal)));
                            $objDateFechaFinHal = date_create(date('Y-m-d H:i', strtotime($strFechaHal.' '.$strHoraFinHal)));
                            $objAntDetalleSolhist = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolHist')
                                                        ->findOneDetalleSolicitudHistorial($objDetSolicitud->getId(),
                                                                                           $strEstadoAnt);
                            if (is_object($objAntDetalleSolhist) && !empty($objAntDetalleSolhist))
                            {
                                $objAntDetalleSolhist->setFeIniPlan($objDateFechaIniHal);
                                $objAntDetalleSolhist->setFeFinPlan($objDateFechaFinHal);
                                $this->emComercial->persist($objAntDetalleSolhist);
                                $this->emComercial->flush();
                            }
                        }
                        //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                        $entityDetalleSolHist= new InfoDetalleSolHist();
                        $entityDetalleSolHist->setDetalleSolicitudId($objDetSolicitud);
                        if ($strEsHal == "S" && $strCreaTareas == "S")
                        {
                            $entityDetalleSolHist->setFeIniPlan($objDateFechaIniHal);
                            $entityDetalleSolHist->setFeFinPlan($objDateFechaFinHal);
                        }
                        else
                        {
                            $entityDetalleSolHist->setFeIniPlan(new \DateTime($strFechaIni));
                            $entityDetalleSolHist->setFeFinPlan(new \DateTime($strFechaFin));
                        }
                        $entityDetalleSolHist->setObservacion(substr($strObsSolicitud, 0, 1499));
                        $entityDetalleSolHist->setIpCreacion($strIpCreacion);
                        $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $entityDetalleSolHist->setUsrCreacion($strUsrCreacion);
                        $entityDetalleSolHist->setEstado($strEstadoNue);
                        $this->emComercial->persist($entityDetalleSolHist);
                        $this->emComercial->flush();
                        
                        if ($strEstadoNue == 'Replanificada')
                        {
                            $arrayDatosActuaTarea = array(
                                'objSolicitud'   => $objDetSolicitud,
                                'objRequest'     => $arrayDatosEstado['objRequest'],
                                'strEstNuevo'    => 'Replanificada',
                                'strEstActual'   => 'Asignada',
                                'strObservacion' => 'Replanificación de Orden de Trabajo'
                            );
                            $this->actualizaTareasAdicionales($arrayDatosActuaTarea);
                        }
                    }
                }
            }
        }
        if($strEsHal == "S" && $strCreaTareas == "S" && isset($arrayTareasComplementarias) && !empty($arrayTareasComplementarias))
        {
          $this->emSoporte->getConnection()->commit();
          $this->emComunicacion->getConnection()->commit();

          $intIdDetalleSolicitud  = $arrayParametrosTarea['intIdFactibilidad'];
          $objPunto               = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPunto);
          $objDetSolicitud        = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                  ->findOneBy(array('detalleSolicitudId' => $intIdDetalleSolicitud));
          $intNumeroTarea         = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                                  ->getMinimaComunicacionPorDetalleId($objDetSolicitud->getId());
          // Array para el envio a hal
          $arrayParametrosHal     = array ('loginCliente'                 => $objPunto->getLogin(),
                                      'idDetalleSolicitud'            => intval($intIdDetalleSolicitud),
                                      'idComunicacionPlanificada'     => intval($intNumeroTarea),
                                      'listaIdComunicacionNuevos'     => $arrayTareasComplementarias
                                     );

          $arrayParametrosWs      = array ( 'strUrl'       => $this->container->getParameter('ws_hal_agrega_tareas_complentarias'),
                                       'arrayData'    => $arrayParametrosHal,
                                       'arrayOptions' => array(CURLOPT_SSL_VERIFYPEER => false));

          // Se establece la comunicación
          $arrayResponseHal = $this->serviceSoporte->comunicacionWsRestClient($arrayParametrosWs);   
          return $arrayResponseHal;
        }
    }

    /**
     * Funcion que creara las tareas y los datos para presentar la informacion en el grid de tareas
     * para los servicos adicionales que se generen
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 28-06-2021 - Version Inicial.
     *
     * @param $arrayDatosEstado -> Contiene todos los datos del punto y los estados
     *        arrayParametrosTarea -> Contiene la informacion para crear la tarea en CE empaquetado.
    */
    private function crearTareaServicioAdicional($arrayParametrosTarea)
    {
        $strEsHal        = $arrayParametrosTarea['strEsHal'];
        $objDetSolicitud = $arrayParametrosTarea['objSolicitud'];
        $objServicio     = $arrayParametrosTarea['objInfoServicio'];
        $strUserCreacion = $arrayParametrosTarea['strUsrCreacion'];
        $strIpCreacion   = $arrayParametrosTarea['strIpCreacion'];
        // Invocamos al metodo de crear las tareas y validamos su resultadi
        $arrayRespCrearTarea = $this->crearTareaAsignarPlanificacion($arrayParametrosTarea);
        $objEntityDetalle = $arrayRespCrearTarea['objInfoDetalle'];
        if (!empty($strEsHal) && $strEsHal === 'S')
        {
            $this->emSoporte->getConnection()->beginTransaction();
            $this->emComunicacion->getConnection()->beginTransaction();
        }
        $strOpcion = 'Cliente: ' . $objServicio->getPuntoId()->getNombrePunto().' | OPCION: Punto Cliente';
        $objInfoCriterioA = $this->emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                    ->getInfoParteAfectadaExistente($objEntityDetalle->getId());
        $intIdCA = 1;
        if($objInfoCriterioA == null)
        {
            $objInfoCriterioAfectado = new InfoCriterioAfectado();
            $objInfoCriterioAfectado->setId($intIdCA);
            $objInfoCriterioAfectado->setDetalleId($objEntityDetalle);
            $objInfoCriterioAfectado->setCriterio("Clientes");
            $objInfoCriterioAfectado->setOpcion($strOpcion);
            $objInfoCriterioAfectado->setUsrCreacion($strUserCreacion);
            $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
            $objInfoCriterioAfectado->setIpCreacion($strIpCreacion);
            $this->emSoporte->persist($objInfoCriterioAfectado);
            $this->emSoporte->flush();

            $entityInfoParteAfectada = new InfoParteAfectada();
            $entityInfoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
            $entityInfoParteAfectada->setDetalleId($objEntityDetalle->getId());
            $entityInfoParteAfectada->setAfectadoId($objServicio->getPuntoId()->getId());
            $entityInfoParteAfectada->setTipoAfectado("Cliente");
            $entityInfoParteAfectada->setAfectadoNombre($objServicio->getPuntoId()->getLogin());
            $entityInfoParteAfectada->setAfectadoDescripcion($objServicio->getPuntoId()->getNombrePunto());
            $entityInfoParteAfectada->setFeIniIncidencia($objDetSolicitud->getFeCreacion());
            $entityInfoParteAfectada->setUsrCreacion($strUserCreacion);
            $entityInfoParteAfectada->setFeCreacion(new \DateTime('now'));
            $entityInfoParteAfectada->setIpCreacion($strIpCreacion);
            $this->emSoporte->persist($entityInfoParteAfectada);
            $this->emSoporte->flush();
        }
        return $arrayRespCrearTarea;
    }

    /**
     * Funcion para actualizar las tareas de una solicitud cuando existe un evento fuera de creacion
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 28-06-2021 - Version Inicial.
     *
     * @param $arrayDatosEstado -> Contiene los datos para la actualizacion de las tareas
    */
    private function actualizaTareasAdicionales($arrayDatosActuaTarea)
    {
        $objSolicitud   = $arrayDatosActuaTarea['objSolicitud'];
        $objRequest     = $arrayDatosActuaTarea['objRequest'];
        $strEstNuevo    = $arrayDatosActuaTarea['strEstNuevo'];
        $strObservacion = $arrayDatosActuaTarea['strObservacion'];
        $arrayDetalles = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                    ->findBy(array('detalleSolicitudId' => $objSolicitud->getId()));
        if(isset($arrayDetalles) && !empty($arrayDetalles))
        {
            foreach($arrayDetalles as $objDetalle)
            {
                if ($objSolicitud->getEstado() == $strEstNuevo)
                {
                    $this->serviceSoporte->cambiarEstadoTarea($objDetalle, 
                                                              null, 
                                                              $objRequest, 
                                                              array("observacion"   => $strObservacion,
                                                                    "cargarTiempo"  => "cliente",
                                                                    "estado"        => $strEstNuevo,
                                                                    "esSolucion"    => "N"));
                }
            }
        }
    }
        
    /**
     * Función para obtener las opciones permitidas en la consulta de PYL y en el grid de activación
     * 
     * @param array $arrayParametros [
     *                                  "strPrefijoSesion"      => Prefijo en sesión,
     *                                  "strUserSesion"         => Usuario en sesión,
     *                                  "strCodEmpresaSesion"   => Código de empresa en sesión,
     *                                  "strProcesoEjecutante"  => Opción ejecutante
     *                               ]
     * @return array $arrayRespuesta [
     *                                  "intNumTotalFiltrosConsultaCoordinar"   => número de filtro que se ejecutarán en la consulta de coordinar,
     *                                  "arrayFiltrosConsultaCoordinar"         => arreglo con los filtros de la consulta del grid de Coordinar,
     *                                  "arrayPersonalizacionOpcionesGridCoordinar" => arreglo con la personalización de las opciones permitidas 
     *                                                                                 en el grid de Coordinar
     *                                  "arrayPersonalizacionOpcionesGridTecnico"   => arreglo de opciones permitidas en el grid técnico
     *                               ]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-04-2022
     * 
     */
    public function obtenerInfoPerfilesParamsCoordinacionYActivacion($arrayParametros)
    {
        $strPrefijoSesion                           = $arrayParametros['strPrefijoSesion'];
        $strUserSesion                              = $arrayParametros['strUserSesion'];
        $strCodEmpresaSesion                        = $arrayParametros['strCodEmpresaSesion'];
        $strProcesoEjecutante                       = $arrayParametros['strProcesoEjecutante'];
        $arrayPerfilesCoordinacionYActivacion       = array();
        $arrayFiltrosConsultaCoordinar              = array();
        $arraySolProdParamsConsultaCoordinar        = array();
        $arrayPersonalizacionOpcionesGridCoordinar  = array();
        $arrayPersonalizacionOpcionesGridTecnico    = array();
        $intContadorFiltrosConsultaCoordinar        = 0;
        try
        {
            $arrayParamsPerfilesCoordYActiv  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoSesion, 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION', 
                                                                        'NOMBRES_PERFILES', 
                                                                        '', 
                                                                        '', 
                                                                        '', 
                                                                        $strCodEmpresaSesion);
            if(isset($arrayParamsPerfilesCoordYActiv) && !empty($arrayParamsPerfilesCoordYActiv))
            {
                foreach($arrayParamsPerfilesCoordYActiv as $arrayParamPerfilCoordYActiv)
                {
                    $arrayPerfilesCoordinacionYActivacion[] = $arrayParamPerfilCoordYActiv["valor3"];
                }
            }
            
            if(isset($arrayPerfilesCoordinacionYActivacion) && !empty($arrayPerfilesCoordinacionYActivacion)
                && isset($strProcesoEjecutante) && !empty($strProcesoEjecutante))
            {
                $arrayDatosUsuarioSesion    = $this->emComercial
                                                   ->getRepository('schemaBundle:InfoPersona')->getPersonaDepartamentoPorUser($strUserSesion);
                if(isset($arrayDatosUsuarioSesion) && !empty($arrayDatosUsuarioSesion))
                {
                    foreach($arrayPerfilesCoordinacionYActivacion as $strPerfilCoordinacionYActivacion)
                    {
                        $arrayRespPerfilPersonaCoordYActiv  = $this->emSeguridad->getRepository('schemaBundle:SeguPerfilPersona')
                                                                                ->getAccesoPorPerfilPersona($strPerfilCoordinacionYActivacion, 
                                                                                                            $arrayDatosUsuarioSesion['ID_PERSONA']);
                        if(count($arrayRespPerfilPersonaCoordYActiv) >= 1 )
                        {
                            if($strProcesoEjecutante === "GESTION_COORDINAR")
                            {
                                $arrayParamsFiltrosConsultaCoord    = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                                      ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoSesion,
                                                                                            '', 
                                                                                            '', 
                                                                                            '', 
                                                                                            'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION', 
                                                                                            'FILTROS_CONSULTA_COORDINAR_SOL_Y_PRODUCTO',
                                                                                            $strProcesoEjecutante,
                                                                                            $strPerfilCoordinacionYActivacion,
                                                                                            '',
                                                                                            $strCodEmpresaSesion);
                                if(isset($arrayParamsFiltrosConsultaCoord) && !empty($arrayParamsFiltrosConsultaCoord))
                                {
                                    foreach($arrayParamsFiltrosConsultaCoord as $arrayParamFiltroConsultaCoord)
                                    {
                                        $intContadorFiltrosConsultaCoordinar++;
                                        $arrayFiltrosConsultaCoordinar[]= array("tipoSolicitud"          => $arrayParamFiltroConsultaCoord['valor5'],
                                                                                "nombreTecnicoProd"      => $arrayParamFiltroConsultaCoord['valor6']);
                                        $arraySolProdParamsConsultaCoordinar[]  = $arrayParamFiltroConsultaCoord['valor5']
                                                                                  ."-".$arrayParamFiltroConsultaCoord['valor6'];
                                    }
                                }

                                $arrayParamsOpcionesGridCoord = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoSesion, 
                                                                                    '', 
                                                                                    '', 
                                                                                    '', 
                                                                                    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION', 
                                                                                    'PERSONALIZACION_OPCIONES_GRID_COORDINAR',
                                                                                    $strProcesoEjecutante,
                                                                                    $strPerfilCoordinacionYActivacion, 
                                                                                    '', 
                                                                                    $strCodEmpresaSesion);
                                if(isset($arrayParamsOpcionesGridCoord) && !empty($arrayParamsOpcionesGridCoord))
                                {
                                    foreach($arrayParamsOpcionesGridCoord as $arrayParamOpcionGridCoord)
                                    {
                                        $arrayPersonalizacionOpcionesGridCoordinar[$arrayParamOpcionGridCoord['valor5']] = 
                                            $arrayParamOpcionGridCoord['valor6']."|".$arrayParamOpcionGridCoord['valor7'];
                                    }
                                }
                            }
                            else if($strProcesoEjecutante === "GESTION_TECNICA")
                            {
                                $arrayParamsOpcionesGridTecnico = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                              ->get('PARAMETROS_ASOCIADOS_A_SERVICIOS_'.$strPrefijoSesion,
                                                                                    '', 
                                                                                    '', 
                                                                                    '', 
                                                                                    'PARAMETRIZACIONES_PERFILES_COORDINACION_Y_ACTIVACION', 
                                                                                    'PERSONALIZACION_OPCIONES_GRID_TECNICO',
                                                                                    $strProcesoEjecutante,
                                                                                    $strPerfilCoordinacionYActivacion, 
                                                                                    '', 
                                                                                    $strCodEmpresaSesion);
                                if(isset($arrayParamsOpcionesGridTecnico) && !empty($arrayParamsOpcionesGridTecnico))
                                {
                                    foreach($arrayParamsOpcionesGridTecnico as $arrayParamOpcionGridTecnico)
                                    {
                                        $arrayPersonalizacionOpcionesGridTecnico[$arrayParamOpcionGridTecnico['valor5']][] = 
                                            $arrayParamOpcionGridTecnico['valor7'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            error_log("Error al verificar los perfiles parametrizados ".$e->getMessage());
        }
        
        $arrayRespuesta = array("intNumTotalFiltrosConsultaCoordinar"       => $intContadorFiltrosConsultaCoordinar,
                                "arrayFiltrosConsultaCoordinar"             => $arrayFiltrosConsultaCoordinar,
                                "arraySolProdParamsConsultaCoordinar"       => array_unique($arraySolProdParamsConsultaCoordinar),
                                "arrayPersonalizacionOpcionesGridCoordinar" => $arrayPersonalizacionOpcionesGridCoordinar,
                                "arrayPersonalizacionOpcionesGridTecnico"   => $arrayPersonalizacionOpcionesGridTecnico);
        return $arrayRespuesta;
    }
}
