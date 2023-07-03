<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\soporteBundle\Service\SoporteService;
use telconet\tecnicoBundle\Service\InfoInterfaceElementoService;
use telconet\tecnicoBundle\Service\InfoElementoWifiService;
use telconet\tecnicoBundle\Service\InfoServicioTecnicoService;
use telconet\financieroBundle\Service\InfoNotaCreditoService;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;

/**
 * Clase CoordinarService
 *
 * Clase que se encarga de realizar acciones de submenu Coordinacion
 *
 * @author Jesus Bozada <jbozada@telconet.ec>
 * @version 1.0 28-09-2014
 * 
 * @author Antonio Ayala <afayala@telconet.ec>
 * @version 1.1 06-03-2020  Se agrego variable $servicePlanificacion para consultar productos con la marca
 *                          de activación simultánea
 * 
 * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
 * @version 1.2 19-10-2020 
 *          - Se agrega Variable donde se accede al servicio de Fox premium para consultar 
 *            si el producto adicional es PRAMOUNT, NOGGIN O FOX, cuando anule una orden de traslado.
 * 
 * @author Karen Rodríguez V.  <kyrodriguez@telconet.ec>
 * @version 1.3 26-02-2021 
 *          - Se agrega Variable $envioPlantilla para envio de correo con plantilla
 * 
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.4 14-05-2021 - Se agrega variable $serviceUtil para proceso de depuracion de parametros masivos
 * 
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.5 18-06-2021 - Se agrega Variable $serviceUtil para obtener valores de parametros masivos
 * 
 */
class CoordinarService
{

    private $container;
    private $emComunicacion;
    private $emGeneral;
    private $emComercial;
    private $emSoporte;
    private $templating;
    private $mailer;
    private $serviceInfoInterfaceElemento;
    private $serviceTecnico;
    private $mailerSend;
    private $serviceInfoNotaCredito;
    private $serviceServicioComercial;
    private $servicePlanificacion;
    private $serviceFoxPremium;
    private $envioPlantilla;
    private $serviceCoordinar2;
    /**
     *  Metodo utilizado para setear dependencia
     * 
     * @since 1.0
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 10-01-2017 Se agrega service para wifi
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.1 10-01-2017 Se agrega service para servicio tecnico
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 07-03-2019 Se modifica método para obtener las dependencias del service
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.3 06-03-2020 Se agrega service para Planificación
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 1.4 19-10-2020 
     *          - Se agrega service para determinar el producto.
     *
     * @author Karen Rodríguez V.  <kyrodriguez@telconet.ec>
     * @version 1.5 26-02-2021
     *          - Se agrega service de envioPlantilla.
     * 
     * @author Daniel Reyes  <djreyes@telconet.ec>
     * @version 1.6 14-05-2021 - Se agrega service para parametros masivos.
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.7 18-06-2021 - Se agrega service de serviceUtil.     * 
     * 
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                    = $container;
        $this->emComunicacion               = $container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral                    = $container->get('doctrine')->getManager('telconet_general');
        $this->emComercial                  = $container->get('doctrine')->getManager('telconet');
        $this->emSoporte                    = $container->get('doctrine')->getManager('telconet_soporte');
        $this->templating                   = $container->get('templating');
        $this->mailer                       = $container->get('mailer');
        $this->mailerSend                   = $container->getParameter('mailer_send');    
        $this->serviceInfoInterfaceElemento = $container->get('tecnico.InfoInterfaceElemento');
        $this->serviceWifi                  = $container->get('tecnico.InfoElementoWifi');
        $this->serviceTecnico               = $container->get('tecnico.InfoServicioTecnico');
        $this->serviceInfoNotaCredito       = $container->get('financiero.InfoNotaCredito');
        $this->serviceServicioComercial     = $container->get('comercial.InfoServicio');
        $this->servicePlanificacion         = $container->get('planificacion.Planificar');
        $this->serviceFoxPremium            = $container->get('tecnico.FoxPremium');
        $this->envioPlantilla               = $container->get('soporte.EnvioPlantilla');
        $this->serviceUtil                  = $container->get('schema.Util');
        $this->serviceCoordinar2            = $container->get('planificacion.Coordinar2');
    }

    /**
     *  Metodo utilizado para realizar anulaciones de solicitudes
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 30-12-2015 Se agrega validacion de estado del servicio al momento de ejecutar la accion
     *
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 18-06-2016 Se agrega validación de bandera de envió de correo
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.3 10-01-2017 Se agrega validación para liberar puertos de INTERNET WIFI
     * 
     * Se agrega la anulación de caracteristicas del servicio
     * @author Hector Ortega<haortega@telconet.ec>
     * @version 1.4 21-02-2017
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 30-03-2017 Se agrega validación para que las solicitudes de agregar equipo continúen con el flujo normal 
     *                         de anulación de solicitud
     * 
     * @author John Vera R <javera@telconet.ec>
     * @version 1.4 21-06-2017 Se agrega validación de reverso de factibilidad
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 17-07-2017 Se agrega validación para la cancelación de tareas asociadas a un plan netlifecam, así se cancelarán las tareas
     *                         asociadas a solicitudes de planificación y retiro de equipo de dicho plan que fueron replanificadas.
     * 
     * @author John Vera R <javera@telconet.ec>
     * @version 1.8 15-08-2017 se procede a validar que cuando sea un concentrador verifique si tiene extremos
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 04-10-2017 Se agrega código para eliminar caracteristica SERVICIO_MISMA_ULTIMA_MILLA de todos los servicios que dependan
     *                         del servicio rechazado
     * @since 1.8
     * 
     * @author Modificado: Jesús Bozada <jbozada@telconet.ec>
     * @version 2.0 09-03-2018 Se agrega validacion para gestionar solicitudes de reubicacion
     * @since 1.9
     * 
     * @author Edgar Pin Villavicencio <epin@telconet.ec>
     * @version 2.1 23-03-2018 Se agrega funcionalidad para liberar el cupo para planificacion mobile
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 22-06-2018 Se libera factibilidad al anular una solicitud de factibilidad de un servicio Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.3 27-11-2018 Se agregan validaciones para gestionar productos de empresa TNP
     * @since 2.2
     *
     * @author Anabelle Peñaherrera <apenaherrera@telconet.ec>
     * @version 2.4 07-12-2018- Se agrega que al momento de eliminar el servicio que se realice el proceso de reverso de la factura
     *                          de contrato digital, se debe validar que se genere NC de Reverso solo si no existe ya asociada una NC Activa. 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.5 06-02-2019 Se agregan validaciones para eliminar caracteristica CORREO ELECTRONICO de servicios de planes 
     *                         que incluyan McAfee como parte del mismo
     * @since 2.4
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 07-02-2019 Se libera factibilidad al anular una solicitud de factibilidad de un servicio TelcoHome
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.7 28-02-2019 Se agregan validaciones para servicios con tipo de orden traslados MD, se rechazaran todos 
     *                         los servicios adicionales en el destino del traslado y se activan nuevamente los servicios
     *                         adicionales en el origen del traslado
     * @since 2.5
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 07-03-2019 Se obtiene el service InfoServicio del módulo comercial para anular una orden de trabajo para
     *                          servicios Small Business y TelcoHome
     * 
     * @author Madeline Haz <mhaz@telconet.ec>
     * @version 2.8 11-03-2019 Se agrega validación de motivos para generar nota de crédito y se realiza el cambio a usuario telcos_rechazo_os.
     * 
     * @author Josselhin Moreira <kjmoreira@telconet.ec>
     * @version 2.9 26-03-2019  Se agrega la validación en el proceso de crear NC, según el motivo que se elige y se encuentra parametrizado.
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 2.10 19-06-2019 Se modifica el nombre del método utilizado para recrear las solicitudes de Agregar Equipo y de
     *                          de Cambio de equipo por soporte en ordenes de servicio de tipo traslado
     * 
     * @since 2.9
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 2.11 Se cambia el llamado de cancelarTarea por cambiarEstadoTarea,
     * se quita bloque de código ya que no se va a usar validación.
     * @since 2.10
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.12 Se agrega consulta del producto si tiene la marca de activación simultánea para reversar la factibilidad
     *               del producto marcado
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.13 03-04-2020 | Se realiza un ajuste para que cuando la característica sea ES_BACKUP, pase a estado "Eliminado".
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.14 23-04-2020 Se agrega el envío del parámetro objProductoPref en lugar del parámetro strNombreTecnicoPref a la función 
     *                          gestionarServiciosAdicionales, debido a los cambios realizados para la reestructuración de servicios Small Business
     * @since 2.11
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.15 05-05-2020 - Se agrega el tipo de solicitud: 'SOLICITUD CAMBIO EQUIPO POR SOPORTE MASIVO' y 'SOLICITUD AGREGAR EQUIPO MASIVO'
     * @since 2.13
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.16 19-09-2020 Se agrega validación para flujo de anulación de servicios W+AP con solicitudes de planificación o de agregar equipo
     *
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.17 19-10-2020 
     *          - Se agrega reactivacion automatica en el punto origen de la info-serv-prod-carac 
     *            de los productos Paramount y Noggin al Anular orden de traslado.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.18 13-11-2020 Se agrega la recreación de solicitud del servicio origen W+AP cuando se anula un servicio W+AP 
     *                          o el servicio de Internet que a su vez anula los servicios adicionales
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.19 28-12-2020 
     *          - Solvencia de Error al Anular Orden de trabajo para traslado en los productos adicionales Paramount y Noggin.
     *  
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.20 01-04-2021 - Se anexa filtro para poder anular solicitudes de instalacion de cableado ethernet
     *
     * @author Karen Rodríguez V.  <kyrodriguez@telconet.ec>
     * @version 2.21 26-02-2021 
     *          - Se consulta si existe solicitud de excedente de material asociada al servicio para anular
     *          - Se consulta si existe tarea por validación de excedente de material asociada a la solicitud
     *            de planificación, si existe tarea se finaliza.
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 2.22 07-05-2021 Se agrega logica para llamar al service que anule las n cantidad de servicios adicionales que tiene el un
     *                          preferencial, ejemplo: CAMARAS SAFE-CITY
     *
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.22 10-05-2021 Se modifican los parámetros enviados a la función liberarInterfaceSplitter
     * 
     * @author Daniel Reyes Peñafiel <djreyes@telconet.ec>
     * @version 2.23 17-05-2021 - Se anexa validacion para que al anular OT de servicio de internet, se anulen tambien
     *                            los servicios adicionales parametrizados.
     * 
     * @author Jonathan Mazon Sanchez  <jmazon@telconet.ec>
     * @version 2.22 06-04-2021
     *          - Flujo para anular Orden de trabajo para equipo Extender Dual Band
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.23 27-04-2021 Se elimina la recreación de solicitudes del extender al anular el servicio de Internet, ya que ahora se lo hará desde
     *                          la gestión simultánea
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.0 26-05-2021 Se valida si el servicio es tipo de red GPON para ejecutar el método de liberarInterfaceSplitter
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.1 13-08-2021 Se agrega la anulación de servicios adicionales El canal del fútbol al anular el servicio de Internet
     *
     * @author Jonathan Mazon Sanchez <jmazon@telconet.ec>
     * @version 3.2 13-08-2021 Se agrega rechazo para producto adicional parametrizado ECDF.
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 3.3 21-08-2021 Se realiza invocacion del metodo para anular todos los servicios adicionales manuales
     *                          "cancelacionSimulServicios" cuando se anula el servicio de internet
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.4 18-10-2021 Se valida el estado del servicio para la anulación de las ordenes de trabajo de los
     *                         servicios adicionales SafeCity.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.5 14-12-2021 - Se elimina el comentario del envió del correo
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 3.5 03-01-2022 Se elimina envío de parámetro strValorCaractTipoOntNuevo a la función recreaSolicitudCambioOntTraslado por cambio 
     *                         en dicha función para permitir Extender para ZTE
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 3.6 19-12-2022 Se agrega validacion al momento de anular el servicio de Internet con ECDF
     * 
     * @author Emmanuel Martillo <emartillo@telconet.ec>
     * @version 3.7 02-03-2023 Se agrega validacion por Prefijo de Empresa EN para que siga el Flujo de MD.
     * 
     * @author Geovanny Cudco <acudco@telconet.ec>
     * @version 3.8 18-05-2023 Se agrega la validación en la anulación de orden de trabajo por cambio de 
     *                         de tipo de medio.
     * 
     *  @param integer $idDetalleSolicitud
     *  @param integer $idMotivo
     *  @param String  $observacion
     *  @param String  $prefijoEmpresa
     *  @return String de mensaje de ejecucion de proceso
     */
    function anularOrdenDeTrabajo($idDetalleSolicitud, $idMotivo, $observacion, $prefijoEmpresa, $peticion)
    {
        $em                             = $this->emComercial;
        $em_general                     = $this->emGeneral;
        $em_comunicacion                = $this->emComunicacion;
        $strRespuesta                   = "";
        $strRespuestaCancelacionTarea   = "";
        $entityDetalleSolicitud         = $em->getRepository('schemaBundle:InfoDetalleSolicitud')->findOneById($idDetalleSolicitud);
        $entityMotivo                   = $em_general->getRepository('schemaBundle:AdmiMotivo')->findOneById($idMotivo);
        $tipoSolicitud                  = strtolower($entityDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());
        $em->getConnection()->beginTransaction();
        $em_comunicacion->getConnection()->beginTransaction();
        $this->emSoporte->getConnection()->beginTransaction();
        $strMotivo                      = 'Falta de pago de las facturas de instalación';
        $arrayParametroReversoNC        = array();        
        $strEmpresaCod                  = $peticion->getSession()->get('idEmpresa');
        $strUsrCreacion                 = "telcos_rechazo_os";
        $strCaracteristicaCorreo        = "CORREO ELECTRONICO";
        $objProductoMcAfee              = null;
        $boolFalse                      = false;
        $objPlanificarService           = $this->servicePlanificacion;
        $strUser                        = $peticion->getSession()->get('user');
        $strIp                          = $peticion->getClientIp();
        $strAnulaFactibPlanifInternetMd = "";
        $intIdDepartamento              = $peticion->getSession()->get('idDepartamento');
        $intIdPerEmpresaRol             = $peticion->getSession()->get('idPersonaEmpresaRol');
        
        try
        {
            $strEstadoServicio = "";
            if($entityDetalleSolicitud)
            {
                $entityServicio = $em->getRepository('schemaBundle:InfoServicio')
                                     ->findOneById($entityDetalleSolicitud->getServicioId());
                if(is_object($entityServicio))
                {
                    $strEstadoServicio = $entityServicio->getEstado();
                }
                // se agrega validacion del estado del servicio para bloquear operaciones incorrectas
                if($entityServicio->getEstado() == "Activo" && 
                    ($tipoSolicitud != 'solicitud migracion' &&
                     $tipoSolicitud != 'solicitud agregar equipo' &&
                     $tipoSolicitud != 'solicitud agregar equipo masivo' &&
                     $tipoSolicitud != 'solicitud cambio equipo por soporte' &&
                     $tipoSolicitud != 'solicitud cambio equipo por soporte masivo' &&
                     $tipoSolicitud != 'solicitud de instalacion cableado ethernet' &&
                     $tipoSolicitud != 'solicitud reubicacion' ))
                {
                    $strRespuesta = "El servicio Actualmente se encuentra con estado Activo, no es posible Anular Solicitud.";
                    return $strRespuesta;
                }
                $strTipoOrden = $entityServicio->getTipoOrden();
                
                if($prefijoEmpresa === "MD" && $strTipoOrden === "T" 
                    && is_object($entityServicio->getPlanId()) 
                    && ($tipoSolicitud === "solicitud planificacion" || $tipoSolicitud === "solicitud agregar equipo"))
                {
                    $strValorCaractMotivoCambioOnt      = "CAMBIO ONT POR AGREGAR EXTENDER";
                    $arrayRespuestaRecreaSolCambioOnt   = $this->serviceServicioComercial
                                                               ->recreaSolicitudCambioOntTraslado(
                                                                                    array(  
                                                                                            "objServicioPlanDestinoEnPunto"         => 
                                                                                            $entityServicio,
                                                                                            "strCodEmpresa"                         => $strEmpresaCod,
                                                                                            "strUsrCreacion"                        => $strUser,
                                                                                            "strIpCreacion"                         => $strIp,
                                                                                            "strValorCaractMotivoCambioOnt"         => 
                                                                                            $strValorCaractMotivoCambioOnt));
                    if($arrayRespuestaRecreaSolCambioOnt["status"] === "ERROR")
                    {
                        throw new \Exception($arrayRespuestaRecreaSolCambioOnt["mensaje"]);
                    }
                }
                //se agrego validacion de tipo de solicitud factibilidad, debe ser permitida la anulacion de estos servicios
                if($tipoSolicitud == "solicitud planificacion" || 
                   $tipoSolicitud == "solicitud factibilidad"  ||
                   (($tipoSolicitud == "solicitud agregar equipo" || $tipoSolicitud == "solicitud agregar equipo masivo") &&
                    is_object($entityServicio->getProductoId()) &&
                    ($entityServicio->getProductoId()->getNombreTecnico() === "WIFI_DUAL_BAND" ||
                     $entityServicio->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND" ||
                     $entityServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB")))
                {
                    $strEstadoActual = $entityServicio->getEstado();
                    $entityServicio->setEstado("Anulado");
                    $em->persist($entityServicio);
                    $em->flush();

                    // Anulamos los servicios adicionales parametrizados
                    if($tipoSolicitud == "solicitud planificacion")
                    {
                        $arrayDatosParametros = array(
                            "objServicio"     => $entityServicio,
                            "strEstado"       => "Anulado",
                            "strObservacion"  => "Se anula servicio adicional con servicio de internet",
                            "intCodEmpresa"   => $strEmpresaCod,
                            "strIpCreacion"   => $strIp,
                            "strUserCreacion" => $strUser,
                            "idPersonaRol"   => $intIdDepartamento,
                            "idDepartamento" => $intIdPerEmpresaRol
                        );
                        $this->serviceCoordinar2->cancelarProdAdicionalesAut($arrayDatosParametros);
                    }

                    // Anulamos todos los productos adicionales manuales con el internet
                    $strPlanServicio = $entityServicio->getPlanId();
                    if (!empty($strPlanServicio) && $tipoSolicitud == "solicitud planificacion")
                    {
                        $arrayDatosAnular = array(
                            "idPunto"        => $entityServicio->getPuntoId()->getId(),
                            "idServicio"     => $entityServicio->getId(),
                            "estadoActual"   => $strEstadoActual,
                            "estado"         => "Anulado",
                            "observacion"    => "Se anula el producto en simultaneo con el servicio de internet",
                            "usuario"        => $strUser,
                            "ipCreacion"     => $strIp,
                            "idEmpresa"      => $strEmpresaCod,
                            "idPersonaRol"   => $intIdDepartamento,
                            "idDepartamento" => $intIdPerEmpresaRol
                        );
                        $this->serviceCoordinar2->cancelacionSimulServicios($arrayDatosAnular);
                    }

                     //Productos adicionales supeditado al estado del internet
                    //NOMBRE TECNICO DE PRODUCTOS DE TVS PERMITIDOS PARA SUPEDITAR EL ESTADO DEL INTERNET AL RECHAZAR
                    $arrayNombreTecnicoPermitido = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->get('NOMBRE_TECNICO_PROD_PERMITIDOS_FLUJO_RECHAZADA_Y_ANULADA',//nombre parametro cab
                                                        'PLANIFICACION', //modulo cab
                                                        'OBTENER_PROD',//proceso cab
                                                        'PRODUCTO_TV', //descripcion det
                                                        '','','','','',
                                                        $strEmpresaCod); //empresa
                    foreach($arrayNombreTecnicoPermitido as $arrayNombreTecnico)
                    {
                        $arrayProdNombreTecnico[]   =   $arrayNombreTecnico['valor1'];
                    }
                    if(is_object($entityServicio) && $entityServicio->getPuntoId()->getId() && $strTipoOrden === "N"
                       && is_object($entityServicio->getPlanId()))
                    {
                        $arrayServiciosxPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array('puntoId' => $entityServicio->getPuntoId()->getId()));
                        foreach($arrayServiciosxPunto as $objServicioxPunto)
                        {
                            if(is_object($objServicioxPunto) && is_object($objServicioxPunto->getProductoId()) && 
                                        in_array($objServicioxPunto->getProductoId()->getNombreTecnico(), $arrayProdNombreTecnico))
                            {
                                //Se cambia el estado del servicio adicional
                                $objServicioxPunto->setEstado("Anulado");
                                $this->emComercial->persist($objServicioxPunto);
                                $this->emComercial->flush();

                                //GUARDAR INFO SERVICIO HISTORIAL
                                $objServicioHistorial = new InfoServicioHistorial();
                                $objServicioHistorial->setServicioId($objServicioxPunto);
                                $objServicioHistorial->setIpCreacion($peticion->getClientIp());
                                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $objServicioHistorial->setObservacion('Se anuló el servicio');
                                $objServicioHistorial->setMotivoId($idMotivo);
                                $objServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                $objServicioHistorial->setEstado('Anulado');
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
                    $arrayCouSim          = $objPlanificarService->getIdTradInstSim($entityServicio->getId());
                    $intIdServTradicional = $arrayCouSim[0];
                    $intIdServCou         = $arrayCouSim[1];
                    if ($intIdServTradicional !== null)
                    {
                        $objServicioCou = $em->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServCou);
                        if (!$objServicioCou)
                        {
                            $respuesta->setContent("No existe servicio COU LINEAS TELEFONIA FIJA relacionado");
                            return $respuesta;
                        }
                        else
                        {
                            $arrayPeticiones['strActSimu']        = 'S';
                            $arrayPeticiones['intIdServicio']     = $intIdServCou;
                            $arrayPeticiones['strUser']           = $strUser;
                            $arrayPeticiones['strIpClient']       = $strIp;
                            $arrayPeticiones['strPrefijoEmpresa'] = $prefijoEmpresa;
                            $arrayPeticiones['strEstado']         = 'Anulado';
                            
                            $objServicioCou->setEstado("Anulado");
                            $em->persist($objServicioCou);
                            $em->flush();
                            
                            //GUARDAR INFO SERVICIO HISTORIAL
                            $objServicioHistorialCou = new InfoServicioHistorial();
                            $objServicioHistorialCou->setServicioId($objServicioCou);
                            $objServicioHistorialCou->setIpCreacion($peticion->getClientIp());
                            $objServicioHistorialCou->setFeCreacion(new \DateTime('now'));
                            $objServicioHistorialCou->setObservacion($observacion.'-'.'Activación Simultánea');
                            $objServicioHistorialCou->setMotivoId($id_motivo);
                            $objServicioHistorialCou->setUsrCreacion($peticion->getSession()->get('user'));
                            $objServicioHistorialCou->setEstado('Anulado');
                            $em->persist($objServicioHistorialCou);
                            $em->flush();
                            
                            //Consulto si es FIJA ANALOGA o TRUNK
                            $objCaract = $this->serviceTecnico->getServicioProductoCaracteristica($objServicioCou,
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
                                $arrayCouSimFija      = $objPlanificarService->getIdServInstSim($entityServicio->getId());
                                $intIdServTradSim     = $arrayCouSimFija[0];
                                
                                $objServicioCouFija = $em->getRepository('schemaBundle:InfoServicio')->findOneById($intIdServTradSim);
                                if (!$objServicioCouFija)
                                {
                                    $respuesta->setContent("No existe servicio COU LINEAS TELEFONIA FIJA relacionado");
                                    return $respuesta;
                                }
                                else
                                {
                                    $objServicioCouFija->setEstado("Anulado");
                                    $em->persist($objServicioCouFija);
                                    $em->flush();
                            
                                    //GUARDAR INFO SERVICIO HISTORIAL
                                    $objServicioHistorialCou = new InfoServicioHistorial();
                                    $objServicioHistorialCou->setServicioId($objServicioCouFija);
                                    $objServicioHistorialCou->setIpCreacion($peticion->getClientIp());
                                    $objServicioHistorialCou->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorialCou->setObservacion($observacion.'-'.'Activación Simultánea');
                                    $objServicioHistorialCou->setMotivoId($id_motivo);
                                    $objServicioHistorialCou->setUsrCreacion($peticion->getSession()->get('user'));
                                    $objServicioHistorialCou->setEstado('Anulado');
                                    $em->persist($objServicioHistorialCou);
                                    $em->flush();
                                }
                                
                            }
                        }
                    }
                    
                    //liberar caracteristica de correo electronico para planes que incluyan mcafee
                    if (is_object($entityServicio->getPlanId()) && ($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN"))
                    {
                        if($tipoSolicitud == "solicitud planificacion" || $tipoSolicitud == "solicitud factibilidad" )
                        {
                            $strAnulaFactibPlanifInternetMd = "SI";
                        }
                        
                        $arrayDetallesPlanServicio  = $em->getRepository('schemaBundle:InfoPlanDet')
                                                         ->findByPlanIdYEstado($entityServicio->getPlanId()->getId(),"Activo");

                        foreach($arrayDetallesPlanServicio as $objDetallePlanServicio)
                        {
                            $objProductoDetallePlan = $em->getRepository('schemaBundle:AdmiProducto')
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
                            $objServProdCaractCorreo = $this->serviceTecnico->getServicioProductoCaracteristica( $entityServicio, 
                                                                                                                 $strCaracteristicaCorreo,
                                                                                                                 $objProductoMcAfee
                                                                                                               ); 
                            if (is_object($objServProdCaractCorreo))
                            {
                                $strValorAntesCorreo  = $objServProdCaractCorreo->getValor();
                                $strEstadoAntesCorreo = $objServProdCaractCorreo->getEstado();
                                $objServProdCaractCorreo->setValor('');
                                $objServProdCaractCorreo->setEstado('Eliminado');
                                $objServProdCaractCorreo->setFeUltMod(new \DateTime('now'));
                                $objServProdCaractCorreo->setUsrUltMod($peticion->getSession()->get('user'));
                                $em->persist($objServProdCaractCorreo);
                                $em->flush();

                                //REGISTRA EN LA TABLA DE HISTORIAL
                                $entityServicioHistorial = new InfoServicioHistorial();
                                $entityServicioHistorial->setServicioId($entityServicio);
                                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                                $entityServicioHistorial->setObservacion('Se actualizo caracteristica '.$strCaracteristicaCorreo.' con ID '.
                                                                         $objServProdCaractCorreo->getId().' : <br>'.
                                                                         'Valores Anteriores: <br>'.  
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                         'Valores Actuales: <br>'.  
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                                $entityServicioHistorial->setAccion('actualizaCaracteristica');
                                $entityServicioHistorial->setEstado($entityServicio->getEstado());
                                $em->persist($entityServicioHistorial);
                                $em->flush();
                          }
                        }
                    }
                                       
                    $objInfoServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($entityDetalleSolicitud->getServicioId());
                    if(!is_object($objInfoServicio))
                    { 
                        throw new \Exception('No encontro el Servicio que se desea Anular');
                    }
                    $objInfoPunto    = $em->getRepository('schemaBundle:InfoPunto')->find($objInfoServicio->getPuntoId()->getId());
                    if(!is_object($objInfoPunto))
                    { 
                        throw new \Exception('No encontro el Punto al cual pertenece el servicio que desea anular');
                    }      
            
                    $arrayParametroReversoNC = array ('strPrefijoEmpresa'       => $prefijoEmpresa,
                                                      'strEmpresaCod'           => $strEmpresaCod,
                                                      'strUsrCreacion'          => $strUsrCreacion,
                                                      'strIpCreacion'           => $peticion->getClientIp(),                                                      
                                                      'strMotivo'               => $strMotivo,                                              
                                                      'objInfoPunto'            => $objInfoPunto,
                                                      'objInfoServicio'         => $objInfoServicio);
                    //Validación de motivos para generar reverso.
                    $objMotivoServicio = $em->getRepository('schemaBundle:AdmiMotivo')->findOneById($idMotivo);
                    if(!is_object($objMotivoServicio))
                    { 
                        throw new \Exception('No encontró el Motivo que desea consultar');
                    }
                    
                    $objParametroCab = $em_general->getRepository('schemaBundle:AdmiParametroCab')
                                                  ->findOneBy(array("nombreParametro" => "NC_MOTIVOS_ORDEN_SERVICIO",
                                                                    "estado"          => "Activo"));
                    if (is_object($objParametroCab))
                    {
                        $arrayParametroDet = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                                        ->findBy(array("parametroId" => $objParametroCab,
                                                                       "estado"      => "Activo")); 
                        if ($arrayParametroDet)
                        { 
                            foreach ($arrayParametroDet as $parametroDet)
                            {
                                if ($parametroDet->getValor2() === $objMotivoServicio->getNombreMotivo() && 
                                    $parametroDet->getValor1() === "S" && $parametroDet->getEstado() == "Activo")
                                {
                                    //Se realiza reverso de Facturas de Contrato digital                                    
                                    $strMensajeReversoNC = $this->serviceInfoNotaCredito->generarReversoFacturasContratoFisicoDigital($arrayParametroReversoNC);
                                    if($strMensajeReversoNC)
                                    {
                                        throw new \Exception($strMensajeReversoNC);
                                    } 
                                }
                            }
                        }
                    }
                    
                    //liberacion de ptos para MD
                    if((($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN") && is_object($entityServicio->getPlanId()))
                        || (is_object($entityServicio->getProductoId()) 
                            && ($entityServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS" 
                                || $entityServicio->getProductoId()->getNombreTecnico() === "TELCOHOME"))
                        || ($prefijoEmpresa == "TNP" && is_object($entityServicio->getPlanId()))
                      )
                    {
                        /*
                         * Para el caso de MD, solo liberaría puerto cuando sea una solicitud de planificación o solicitud de factibilidad
                         * relacionada a un servicio asociado a un plan
                         */
                        $arrayRespuestaLiberaSplitter   = $this->serviceInfoInterfaceElemento
                                                               ->liberarInterfaceSplitter(array("objServicio"       => $entityServicio,
                                                                                                "strUsrCreacion"    => $strUser,
                                                                                                "strIpCreacion"     => $strIp,
                                                                                                "strProcesoLibera"  => 
                                                                                                " por anulación del servicio"));
                        $strStatusLiberaSplitter        = $arrayRespuestaLiberaSplitter["status"];
                        $strMensajeLiberaSplitter       = $arrayRespuestaLiberaSplitter["mensaje"];
                        if($strStatusLiberaSplitter === "ERROR")
                        {
                            $em->getConnection()->rollback();
                            $em_comunicacion->getConnection()->rollback();
                            return $strMensajeLiberaSplitter;
                        }
                    }
                    elseif ($prefijoEmpresa == 'TN')
                    {
                        if(is_object($entityServicio->getProductoId()))
                        {                        
                            //si es wifi se liberan los puertos
                            $strNombreProducto = $entityServicio->getProductoId()->getDescripcionProducto();
                            if($strNombreProducto == 'INTERNET WIFI')
                            {
                                $arrayParametros = array();
                                $arrayParametros['intIdServicio']   = $entityServicio->getId();
                                $arrayParametros['strUsrCreacion']  = $peticion->getSession()->get('user');
                                $arrayParametros["strIpCreacion"]   = $peticion->getClientIp();                

                                $arrayResultado = $this->serviceWifi->liberarPuertoWifi($arrayParametros);

                                if($arrayResultado['strStatus'] == 'ERROR')
                                {
                                    throw new \Exception($arrayResultado['strMensaje']);
                                }
                            }
                            //verificar si es GPON el servicio
                            $booleanTipoRedGpon = false;
                            $objCaractTipoRed = $this->serviceTecnico->getServicioProductoCaracteristica($entityServicio,
                                                                                                         "TIPO_RED",
                                                                                                         $entityServicio->getProductoId());
                            if(is_object($objCaractTipoRed))
                            {
                                $arrayParVerTipoRed = $em_general->getRepository('schemaBundle:AdmiParametroDet')
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
                            //verifico si es GPON para liberar splitter
                            if($booleanTipoRedGpon)
                            {
                                $arrayRespuestaLiberaSplitter   = $this->serviceInfoInterfaceElemento
                                                                        ->liberarInterfaceSplitter(array("objServicio"       => $entityServicio,
                                                                                            "strUsrCreacion"    => $strUser,
                                                                                            "strIpCreacion"     => $strIp,
                                                                                            "strVerificaLiberacion" => "SI",
                                                                                            "strPrefijoEmpresa"     => $prefijoEmpresa,
                                                                                            "booleanTipoRedGpon"    => $booleanTipoRedGpon,
                                                                                            "strProcesoLibera"  => 
                                                                                            " por anulación del servicio"));
                                $strStatusLiberaSplitter        = $arrayRespuestaLiberaSplitter["status"];
                                $strMensajeLiberaSplitter       = $arrayRespuestaLiberaSplitter["mensaje"];
                                if($strStatusLiberaSplitter === "ERROR")
                                {
                                    throw new \Exception($strMensajeLiberaSplitter);
                                }
                            }
                            else if($strNombreProducto == 'L3MPLS' || $strNombreProducto == 'Internet Dedicado')
                            {
                                $arrayParametros['intIdServicio'] = $entityServicio->getId();
                                $strMensaje = $this->serviceTecnico->reversaFactibilidad($arrayParametros);
                                if($strMensaje)
                                {
                                    throw new \Exception($strMensaje);
                                }
                            }
                            
                            if($entityServicio->getProductoId()->getEsConcentrador() == 'SI')
                            {
                                $arrayParametros['intIdServicio'] = $entityServicio->getId();
                                $arrayResult = $this->serviceTecnico->getServiciosPorConcentrador($arrayParametros);

                                if($arrayResult['strMensaje'])
                                {
                                    if($arrayResult['strStatus'] == 'OK')
                                    {
                                        $strMensaje = '<b>No se puede Eliminar el servicio concentrador, debido a que tiene extremos enlazados:</b>'
                                                      . '<br><br>'.$arrayResult['strMensaje'];
                                    }
                                    else
                                    {
                                        $strMensaje =  $arrayResult['strMensaje'];
                                    }
                                    throw new \Exception($strMensaje);
                                }
                            }                              
                            else
                            {
                                
                            }
                            
                            // Se anulan las caracteristicas asociadas al servicio
                            $arrayInfoServicioProdCaract = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                              ->findByServicioId($entityDetalleSolicitud->getServicioId());

                            if(count($arrayInfoServicioProdCaract)>0)
                            {
                                foreach($arrayInfoServicioProdCaract as $objInfoServicioProdCaract)
                                {
                                    $arrayAdmiCaract = $this->serviceTecnico
                                                            ->getInfoCaracteristica($objInfoServicioProdCaract->getId());

                                    $strEstado = "Anulado";

                                    if (!is_null($arrayAdmiCaract) && is_array($arrayAdmiCaract) &&
                                    $arrayAdmiCaract['descripcionCaracteristica'] == 'ES_BACKUP') 
                                    {
                                        $strEstado = 'Eliminado';
                                    }

                                    if (!is_null($arrayAdmiCaract) && is_array($arrayAdmiCaract) &&
                                    $arrayAdmiCaract['descripcionCaracteristica'] == 'ID_CAMBIO_TIPO_MEDIO') 
                                    {
                                        $strEstado = 'Eliminado';
                                        $objInfoServicioProdCaract->setValor(null);
                                    }
                                    
                                    $objInfoServicioProdCaract->setEstado($strEstado);
                                    $objInfoServicioProdCaract->setFeUltMod(new \DateTime('now'));
                                    $objInfoServicioProdCaract->setUsrUltMod($peticion->getSession()->get('user'));
                                    $em->persist($objInfoServicioProdCaract);
                                }
                                $em->flush();
                            }
                                                
                        }
                        $this->serviceTecnico->eliminarDependenciaMismaUM($entityServicio, 
                                                                          $peticion->getSession()->get('user'),
                                                                          $peticion->getClientIp());
                    }
                    //GUARDAR INFO SERVICIO HISTORIAL
                    $entityServicioHistorial = new InfoServicioHistorial();
                    $entityServicioHistorial->setServicioId($entityServicio);
                    $entityServicioHistorial->setMotivoId($idMotivo);
                    $entityServicioHistorial->setObservacion($observacion);
                    $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                    $entityServicioHistorial->setEstado('Anulado');
                    $em->persist($entityServicioHistorial);
                    $em->flush();
                    
                    if ($prefijoEmpresa == "MD" && $strTipoOrden == "T")
                    {
                        if(is_object($entityServicio->getProductoId()) 
                            && ($entityServicio->getProductoId()->getNombreTecnico() === "WDB_Y_EDB"))
                        {
                            $arrayRespuestaRecreaSolWyAP = $this->serviceServicioComercial->recreaSolicitudWyApTraslado(
                                                                                            array(  "objServicioDestino"    => $entityServicio,
                                                                                                    "strOpcion"             => "TRASLADO",
                                                                                                    "strCodEmpresa"         => $strEmpresaCod,
                                                                                                    "strUsrCreacion"        => 
                                                                                                    $peticion->getSession()->get('user'),
                                                                                                    "strIpCreacion"         => 
                                                                                                    $peticion->getClientIp()));
                            if($arrayRespuestaRecreaSolWyAP["status"] === "ERROR")
                            {
                                throw new \Exception($arrayRespuestaRecreaSolWyAP["mensaje"]);
                            }
                        }
                        else if (is_object($entityServicio->getProductoId()) 
                                    && ($entityServicio->getProductoId()->getNombreTecnico() === "EXTENDER_DUAL_BAND"))
                        {
                            $arrayRespuestaRecreaSolEDB = $this->serviceServicioComercial->recreaSolicitudEdbTraslado(
                                                                                            array(  "objServicioDestino"    => $entityServicio,
                                                                                                    "strOpcion"             => "TRASLADO",
                                                                                                    "strCodEmpresa"         => $strEmpresaCod,
                                                                                                    "strUsrCreacion"        => 
                                                                                                    $peticion->getSession()->get('user'),
                                                                                                    "strIpCreacion"         => 
                                                                                                    $peticion->getClientIp()));
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
                            $arrayParametrosTrasladoSol["objServicio"]    = $entityServicio;
                            $arrayParametrosTrasladoSol["strUsrCreacion"] = $peticion->getSession()->get('user');
                            $arrayParametrosTrasladoSol["strIpCreacion"]  = $peticion->getClientIp();
                            $arrayParametrosTrasladoSol["strEmpresaCod"]  = $strEmpresaCod;
                            $this->serviceServicioComercial->recrearSolicitudesPorTraslado($arrayParametrosTrasladoSol);
                        }
                        
                        $objProductoInternet = $em->getRepository('schemaBundle:AdmiProducto')
                                                      ->findOneBy(array("nombreTecnico" => "INTERNET",
                                                                        "empresaCod"    => $strEmpresaCod, 
                                                                        "estado"        => "Activo"));
                        
                        /*
                         * se regulariza caracteristica TRASLADO en servicios adicionales a rechazar y
                         * en caso de que el servicio se encuentre Trasladado pase nuevamente a estado Activo
                         */
                        if (is_object($objProductoInternet))
                        {
                            $objSpcTraslado = $this->serviceTecnico->getServicioProductoCaracteristica($entityServicio, "TRASLADO", $objProductoInternet);
                            if (is_object($objSpcTraslado))
                            {
                                $strValorAntesCorreo  = $objSpcTraslado->getValor();
                                $strEstadoAntesCorreo = $objSpcTraslado->getEstado();
                                $objSpcTraslado->setValor('');
                                $objSpcTraslado->setEstado('Eliminado');
                                $objSpcTraslado->setFeUltMod(new \DateTime('now'));
                                $objSpcTraslado->setUsrUltMod($peticion->getSession()->get('user'));
                                $em->persist($objSpcTraslado);
                                $em->flush();

                                //REGISTRA EN LA TABLA DE HISTORIAL
                                $entityServicioHistorial = new InfoServicioHistorial();
                                $entityServicioHistorial->setServicioId($entityServicio);
                                $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
                                $entityServicioHistorial->setObservacion('Se actualizó característica TRASLADO con ID '.
                                                                         $objSpcTraslado->getId().' : <br>'.
                                                                         'Valores Anteriores: <br>'.  
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  '.$strValorAntesCorreo.'<br>'.
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Estado: '.$strEstadoAntesCorreo.'<br>'.
                                                                         'Valores Actuales: <br>'.  
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Valor:  <br>'.
                                                                         '&nbsp;&nbsp;&nbsp;&nbsp;Estado: Eliminado');
                                $entityServicioHistorial->setAccion('actualizaCaracteristica');
                                $entityServicioHistorial->setEstado($entityServicio->getEstado());
                                $em->persist($entityServicioHistorial);
                                $em->flush();
                                
                                $objServicioOrigenTraslado = $em->getRepository('schemaBundle:InfoServicio')->find($strValorAntesCorreo);
                                if (is_object($objServicioOrigenTraslado) && $objServicioOrigenTraslado->getEstado() == "Trasladado")
                                {
                                    $objServicioOrigenTraslado->setEstado("Activo");
                                    $em->persist($objServicioOrigenTraslado);
                                    $em->flush();

                                    //GUARDAR INFO SERVICIO HISTORIAL
                                    $objHistorialServicioAdicional = new InfoServicioHistorial();
                                    $objHistorialServicioAdicional->setServicioId($objServicioOrigenTraslado);
                                    $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                    $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                    $objHistorialServicioAdicional->setObservacion("Se reactiva servicio por Anulación de Traslado del servicio".
                                                                                   " en el punto : ".$entityServicio->getPuntoId()->getLogin());
                                    $objHistorialServicioAdicional->setMotivoId($idMotivo);
                                    $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                    $objHistorialServicioAdicional->setEstado('Activo');
                                    $em->persist($objHistorialServicioAdicional);
                                    $em->flush();
                                }
                            }
                        }
                        
                        if($strAnulaFactibPlanifInternetMd === "SI")
                        {
                            //si el tipo de orden es traslado, los servicios adicionales tb son rechazados automaticamente
                            $arrayServiciosAdicionalesPunto = $em->getRepository('schemaBundle:InfoServicio')
                                                                 ->findBy(array("puntoId" => $entityServicio->getPuntoId()));
                            $arrayEstadosServicios = array('Anulado','Anulada','Eliminado',
                                                           'Eliminada','Cancel','Cancelado','Cancelada');
                            foreach ($arrayServiciosAdicionalesPunto as $objServicioAdicional)
                            {
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
                                        $arrayRespuestaRecreaSolWyAP = $this->serviceServicioComercial
                                                                            ->recreaSolicitudWyApTraslado(
                                                                                array(  "objServicioDestino"    => $objServicioAdicional,
                                                                                        "strOpcion"             => "TRASLADO",
                                                                                        "strCodEmpresa"         => $strEmpresaCod,
                                                                                        "strUsrCreacion"        => $strUsrCreacion,
                                                                                        "strIpCreacion"         => $peticion->getClientIp()));
                                        if($arrayRespuestaRecreaSolWyAP["status"] === "ERROR")
                                        {
                                            throw new \Exception($arrayRespuestaRecreaSolWyAP["mensaje"]);
                                        }
                                    }
                                    $objServicioAdicional->setEstado("Anulado");
                                    $em->persist($objServicioAdicional);
                                    $em->flush();

                                    //GUARDAR INFO SERVICIO HISTORIAL
                                    $objHistorialServicioAdicional = new InfoServicioHistorial();
                                    $objHistorialServicioAdicional->setServicioId($objServicioAdicional);
                                    $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                    $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                    $objHistorialServicioAdicional->setObservacion($observacion);
                                    $objHistorialServicioAdicional->setMotivoId($idMotivo);
                                    $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                    $objHistorialServicioAdicional->setEstado('Anulado');
                                    $em->persist($objHistorialServicioAdicional);
                                    $em->flush();

                                    //se regulariza caracteristica TRASLADO en servicios adicionales a rechazar
                                    if (is_object($objProductoInternet))
                                    {
                                        $objSpcTrasladoAdic = $this->serviceTecnico->getServicioProductoCaracteristica($objServicioAdicional, 
                                                                                                                       "TRASLADO", 
                                                                                                                       $objProductoInternet);
                                        if (is_object($objSpcTrasladoAdic))
                                        {
                                            $strValorAntesCorreo  = $objSpcTrasladoAdic->getValor();
                                            $strEstadoAntesCorreo = $objSpcTrasladoAdic->getEstado();
                                            $objSpcTrasladoAdic->setValor('');
                                            $objSpcTrasladoAdic->setEstado('Eliminado');
                                            $objSpcTrasladoAdic->setFeUltMod(new \DateTime('now'));
                                            $objSpcTrasladoAdic->setUsrUltMod($peticion->getSession()->get('user'));
                                            $em->persist($objSpcTrasladoAdic);
                                            $em->flush();

                                            //REGISTRA EN LA TABLA DE HISTORIAL
                                            $entityServicioHistorial = new InfoServicioHistorial();
                                            $entityServicioHistorial->setServicioId($objServicioAdicional);
                                            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                                            $entityServicioHistorial->setUsrCreacion($peticion->getSession()->get('user'));
                                            $entityServicioHistorial->setIpCreacion($peticion->getClientIp());
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
                                            $em->persist($entityServicioHistorial);
                                            $em->flush();
                                            //Reactivar Servicios-prod-carac de productos Paramount y Noggin en el punto Origen.
                                            $arrayProdCaracts = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findBy(array("servicioId" => $strValorAntesCorreo, "estado" => 'Cancelado'));
                                            if (is_array($arrayProdCaracts))
                                            {
                                                $objServicio = $em->getRepository('schemaBundle:InfoServicio')->findOneById($strValorAntesCorreo);
                                                $objParametroDet    = $em_general->getRepository('schemaBundle:AdmiParametroDet')->
                                                                                findOneBy(array('descripcion'=>'NOMBRES_TECNICOS_PRODUCTOS_TV'));
                                                $arrayProductosAdicionales  = array($objParametroDet->getValor1(),$objParametroDet->getValor2());
                                                if(is_object($objServicio->getProductoId())
                                                    && in_array($objServicio->getProductoId()->getNombreTecnico(), $arrayProductosAdicionales))
                                                {
                                                    $arrayProducto = $this->serviceFoxPremium->determinarProducto(array(
                                                                                        'intIdProducto' => $objServicio->getProductoId()->getId()));
                                                    $arrayParametrosFox = array();
                                                    $arrayParametrosFox["strDescripcionCaracteristica"] = $arrayProducto['strMigrar'];
                                                    $arrayParametrosFox["strNombreTecnico"]             = $arrayProducto['strNombreTecnico'];
                                                    $arrayParametrosFox["intIdServicio"]                = $objServicio->getId();
                                                    $arrayParametrosFox["strEstadoSpc"]                 = 'Cancelado';

                                                    $objRespuestaServProdCarac = $em->getRepository('schemaBundle:InfoServicio')
                                                                                        ->getCaracteristicaServicio($arrayParametrosFox);
                                                    if (is_object($objRespuestaServProdCarac))
                                                    {
                                                        $objRespuestaServProdCarac->setValor('N');
                                                        $em->persist($objRespuestaServProdCarac);
                                                        $em->flush(); 
                                                    }
                                                    foreach ($arrayProdCaracts as $servicioProdCaract)
                                                    {
                                                        $servicioProdCaract->setEstado('Activo');
                                                        $servicioProdCaract->setFeUltMod(new \DateTime('now'));
                                                        $servicioProdCaract->setUsrUltMod($peticion->getSession()->get('user'));
                                                        $em->persist($servicioProdCaract);
                                                        $em->flush(); 
                                                    }
                                                }
                                            }
                                            //reactivar servicio en origen de traslado en caso de tener estado TRASLADADO
                                            $objServicioOrigenTraslado = $em->getRepository('schemaBundle:InfoServicio')->find($strValorAntesCorreo);
                                            if (is_object($objServicioOrigenTraslado) && $objServicioOrigenTraslado->getEstado() == "Trasladado")
                                            {
                                                $objServicioOrigenTraslado->setEstado("Activo");
                                                $em->persist($objServicioOrigenTraslado);
                                                $em->flush();

                                                //GUARDAR INFO SERVICIO HISTORIAL
                                                $objHistorialServicioAdicional = new InfoServicioHistorial();
                                                $objHistorialServicioAdicional->setServicioId($objServicioOrigenTraslado);
                                                $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                                $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                                $objHistorialServicioAdicional->setObservacion("Se reactiva servicio por Anulación de ".
                                                                                               "Traslado del servicio en el punto: ".
                                                                                               $entityServicio->getPuntoId()->getLogin());
                                                $objHistorialServicioAdicional->setMotivoId($idMotivo);
                                                $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                                $objHistorialServicioAdicional->setEstado('Activo');
                                                $em->persist($objHistorialServicioAdicional);
                                                $em->flush();
                                            }
                                        }
                                    }
                                    ///Se eliminan las características asociadas al servicio de Internet del punto origen
                                    $arraySpcServicioAdi = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                              ->findBy(array( "servicioId"    => $objServicioAdicional->getId(),
                                                                              "estado"        => "Activo"));
                                    foreach($arraySpcServicioAdi as $objSpcServicioAdi)
                                    {
                                        $objSpcServicioAdi->setEstado('Eliminado');
                                        $objSpcServicioAdi->setUsrUltMod($peticion->getSession()->get('user'));
                                        $objSpcServicioAdi->setFeUltMod(new \DateTime('now'));
                                        $em->persist($objSpcServicioAdi);
                                        $em->flush();
                                    }
                                }
                            }
                        }
                    }
                    
                    if($prefijoEmpresa == "MD" && $strAnulaFactibPlanifInternetMd === "SI")
                    {
                        $arrayNombresTecnicosProdsPorAnular = array();
                        $arrayProdsAsociadosPorAnular       = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                                                         ->get( 'PARAMETROS_ASOCIADOS_A_SERVICIOS_MD', 
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                'NOMBRES_TECNICOS_ANULACION_SIMULTANEA_X_INTERNET',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                '',
                                                                                $strEmpresaCod);
                        if(isset($arrayProdsAsociadosPorAnular) && !empty($arrayProdsAsociadosPorAnular))
                        {
                            foreach($arrayProdsAsociadosPorAnular as $arrayProdAsociadoPorAnular)
                            {
                                $arrayNombresTecnicosProdsPorAnular[] = $arrayProdAsociadoPorAnular["valor2"];
                            }
                        }
                        
                        if(isset($arrayNombresTecnicosProdsPorAnular) && !empty($arrayNombresTecnicosProdsPorAnular))
                        {
                            $arrayRespuestaServiciosAdicPorAnular   = $this->serviceTecnico
                                                                           ->obtenerServiciosPorProducto(
                                                                                    array(  "intIdPunto"                    => 
                                                                                            $entityServicio->getPuntoId()->getId(),
                                                                                            "arrayNombresTecnicoProducto"   => 
                                                                                            $arrayNombresTecnicosProdsPorAnular,
                                                                                            "strCodEmpresa"                 => 
                                                                                            $strEmpresaCod));
                            
                            $arrayServiciosAdicPorAnular            = $arrayRespuestaServiciosAdicPorAnular["arrayServiciosPorProducto"];
                            if(isset($arrayServiciosAdicPorAnular) && !empty($arrayServiciosAdicPorAnular))
                            {
                                foreach($arrayServiciosAdicPorAnular as $objServicioAdicPorAnular)
                                {
                                    $objServicioAdicPorAnular->setEstado("Anulado");
                                    $em->persist($objServicioAdicPorAnular);
                                    $em->flush();
                                    
                                    $objHistorialServicioAdicional = new InfoServicioHistorial();
                                    $objHistorialServicioAdicional->setServicioId($objServicioAdicPorAnular);
                                    $objHistorialServicioAdicional->setIpCreacion($peticion->getClientIp());
                                    $objHistorialServicioAdicional->setFeCreacion(new \DateTime('now'));
                                    $objHistorialServicioAdicional->setObservacion($observacion);
                                    $objHistorialServicioAdicional->setMotivoId($idMotivo);
                                    $objHistorialServicioAdicional->setUsrCreacion($peticion->getSession()->get('user'));
                                    $objHistorialServicioAdicional->setEstado('Anulado');
                                    $em->persist($objHistorialServicioAdicional);
                                    $em->flush();
                                    
                                    $arraySpcServicioAdicPorAnular = $em->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                        ->findBy(array( "servicioId"    => $objServicioAdicPorAnular->getId(),
                                                                                        "estado"        => "Activo"));
                                    foreach($arraySpcServicioAdicPorAnular as $objSpcServicioAdicPorAnular)
                                    {
                                        $objSpcServicioAdicPorAnular->setEstado('Eliminado');
                                        $objSpcServicioAdicPorAnular->setUsrUltMod($peticion->getSession()->get('user'));
                                        $objSpcServicioAdicPorAnular->setFeUltMod(new \DateTime('now'));
                                        $em->persist($objSpcServicioAdicPorAnular);
                                        $em->flush();
                                    }
                                }
                                
                            }
                        }
                    }
                    
                    $entityRangos = $this->emComercial->getRepository('schemaBundle:InfoCupoPlanificacion')
                                                      ->findBy(array('solicitudId' => $idDetalleSolicitud));
                    foreach ($entityRangos as $entity) 
                    {
                        $entityCupoPlanificacion = $em->getRepository('schemaBundle:InfoCupoPlanificacion')
                                ->find($entity->getId());
                        $entityCupoPlanificacion->setSolicitudId(null);
                        $entityCupoPlanificacion->setCuadrillaId(null);
                        $em->persist($entityCupoPlanificacion);
                        $em->flush();
                    }
                    
                    if ($prefijoEmpresa == 'TN' && is_object($entityServicio->getProductoId()) 
                        && ($entityServicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS"
                            || $entityServicio->getProductoId()->getNombreTecnico() === "TELCOHOME")
                        && $tipoSolicitud == "solicitud planificacion")
                    {
                        $arrayParamsAdicionales     = array("objServicioPref"           => $entityServicio,
                                                            "objProductoPref"           => $entityServicio->getProductoId(),
                                                            "strUsrCreacion"            => $peticion->getSession()->get('user'),
                                                            "strObservacionServicio"    => "Se anula el servicio por anulación de ".
                                                                                           "servicio preferencial",
                                                            "strIpClient"               => $peticion->getClientIp(),
                                                            "strCodEmpresa"             => $peticion->getSession()->get('idEmpresa'),
                                                            "strNuevoEstadoSol"         => "Anulado",
                                                            "strObservacionSol"         => "Se realizó la anulación del servicio por anulación de "
                                                                                           ."solicitud de planificación del servicio preferencial "
                                                                                           ."y por ende se rechaza la solicitud"
                                                            );
                        $arrayRespuestaAdicionales  = $this->serviceServicioComercial->gestionarServiciosAdicionales($arrayParamsAdicionales);
                        if($arrayRespuestaAdicionales["strStatus"] !== "OK")
                        {
                            throw new \Exception($arrayRespuestaAdicionales["strMensaje"] );
                        }
                    }
                    //INI VALIDAMOS SI EXISTEN TAREAS POR SOLICITUD DE EXCEDENTE DE MATERIAL Y SE FINALIZA
                    $objParametroTarea   = $em->getRepository('schemaBundle:AdmiParametroCab')
                                         ->findOneBy(array('nombreParametro'=>'TAREA EXCESO DE MATERIAL'));
                    
                    if(is_object($objParametroTarea) && !empty($objParametroTarea))
                    {
                        $objParametroTareaDet  = $em->getRepository("schemaBundle:AdmiParametroDet")
                                            ->findOneBy(array('descripcion'=>'TAREA A FACTURACIÓN',
                                                              'parametroId'=>$objParametroTarea->getId(),
                                                            ));
                        //Obtenemos las tareas anteriores
                        $arrayTareasxSol = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                           ->getTareasxSolicitudxProceso(array(
                                                                    'detalleSolId'    => $entityDetalleSolicitud->getId(),
                                                                    'nombreTarea'     => $objParametroTareaDet->getValor2(),
                                                                    'nombreProceso'   => $objParametroTareaDet->getValor1()));
                        if($arrayTareasxSol['estado'] === 'ERROR')
                        {
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
                                $arrayParametrosFinTarea = array(
                                            'idEmpresa'               => $strEmpresaCod,
                                            'prefijoEmpresa'          => $prefijoEmpresa,
                                            'idDetalle'               => $tarea['detalleId'],
                                            'fechaEjecucion'          => $strFecha,
                                            'horaEjecucion'           => $objFechaEjecucion->format('H:i:sP'),
                                            'idAsignado'              => null,
                                            'observacion'             => $strObservacionFinTarea,
                                            'empleado'                => $peticion->getSession()->get('empleado'),
                                            'usrCreacion'             => $strUser,
                                            'ipCreacion'              => $peticion->getClientIp(),
                                            'numeroTarea'             => $tarea['numeroTarea'],
                                            'accionTarea'             => 'finalizada'
                                        );
                                $serviceSoport = $this->container->get('soporte.SoporteService');
                                $arrayRespTareaFin  = $serviceSoport->finalizarTarea($arrayParametrosFinTarea);
                                if($arrayRespTareaFin['status'] === 'ERROR')
                                {
                                    throw new \Exception('Error  al finalizar tareas por solicitud, '
                                                       . 'por favor comunicar a Sistemas.'.$arrayRespTareaFin['mensaje']);
                                }        
                                /* Envío de Correo al asesor como seguimiento */
                                $strAsunto = "Notificación de validación de excedente de material | "
                                            . $entityServicio->getPuntoId()->getLogin() ;
                                if($arrayFormasContactoAsesor)
                                {
                                    foreach($arrayFormasContactoAsesor as $arrayformaContacto)
                                    {
                                        $arrayDestinatario[] = $arrayformaContacto['valor'];
                                    }
                                }
                                $arrayParametrosMail = array(
                                                            "login"       => $entityServicio->getPuntoId()->getLogin(),
                                                            "producto"    => $entityServicio->getProductoId()->getDescripcionProducto(),
                                                            "mensaje"     => 'Tarea #'.$tarea['numeroTarea'].' '.$strObservacionFinTarea
                                                            );

                                $this->envioPlantilla
                                     ->generarEnvioPlantilla(
                                                             $strAsunto,
                                                             $arrayDestinatario,
                                                             'NOTIEXCMATASE',
                                                             $arrayParametrosMail,
                                                             $strEmpresaCod,
                                                             '',
                                                             '',
                                                             null,
                                                             false,
                                                             'notificaciones_telcos@telconet.ec'
                                                            );
                            }
                        }
                    }
                    //FIN VALIDAMOS
                    //INI VALIDAMOS SOLICITUD DE EXCEDENTE DE MATERIAL ATADA AL SERVICIO Y SE ANULA
                    $entityTipoSolicitud     = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                               ->findOneByDescripcionSolicitud("SOLICITUD MATERIALES EXCEDENTES");
                    $objDetalleSolicitudExc  = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                 ->findOneBy(array( "servicioId"      => $entityServicio->getId(),
                                                                    "estado"          => 'Pendiente',
                                                                    "tipoSolicitudId" => $entityTipoSolicitud));
                    if($objDetalleSolicitudExc)
                    {
                        //ACTUALIZAR DE ESTADO DE SOLICITUD DE MATERIALES EXCEDENTES
                        $objDetalleSolicitudExc->setEstado('Anulado');
                        $em->persist($objDetalleSolicitudExc);
                        $em->flush();
                        
                        $strSeguimiento =  'Se anula solicitud de excedente de material'
                                        .  ' #'.$objDetalleSolicitudExc->getId().'<br>'
                                        .  ' porque se anula solicitud de planificación';
                        $strMail        =  'Se anula solicitud de excedente de material'
                                        .  ' #'.$objDetalleSolicitudExc->getId()
                                        .  ' porque se anula solicitud de planificación';
                        //CREO HISTORIAL PARA SOLICITUD  DE MATERIALES EXCEDENTES
                        $entityDetSolHistM = new InfoDetalleSolHist();
                        $entityDetSolHistM->setDetalleSolicitudId($objDetalleSolicitudExc);
                        $entityDetSolHistM->setObservacion($strSeguimiento);
                        $entityDetSolHistM->setIpCreacion($peticion->getClientIp());
                        $entityDetSolHistM->setFeCreacion(new \DateTime('now'));
                        $entityDetSolHistM->setUsrCreacion($peticion->getSession()->get('user'));
                        $entityDetSolHistM->setEstado('Anulado');  
                        $em->persist($entityDetSolHistM);
                        $em->flush();  
                        
                        //INSERTAR INFO SERVICIO HISTORIAL
		        $entityServicioHist = new InfoServicioHistorial();
		        $entityServicioHist->setServicioId($entityServicio);
		        $entityServicioHist->setObservacion('<b>Seguimiento:</b> '.$strSeguimiento);
		        $entityServicioHist->setIpCreacion($peticion->getClientIp());
		        $entityServicioHist->setFeCreacion(new \DateTime('now'));
		        $entityServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
		        $entityServicioHist->setEstado($entityServicio->getEstado());
		        $entityServicioHist->setAccion('validaExcedenteMaterial');
		        $em->persist($entityServicioHist);
		        $em->flush();
                        
                        /* Envío de Correo al asesor como seguimiento */
                        $strAsunto = "Notificación de validación de excedente de material | "
                                   . $entityServicio->getPuntoId()->getLogin() ;
                        
                        //Obtenemos la forma de contacto del asesor
                        $arrayFormasContactoAsesor = $em->getRepository('schemaBundle:InfoPersona')
                        ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()
                        ->getUsrVendedor(),'Correo Electronico');
                        
                        if($arrayFormasContactoAsesor)
                        {
                            foreach($arrayFormasContactoAsesor as $arrayformaContacto)
                            {
                                $arrayDestinatario[] = $arrayformaContacto['valor'];
                            }
                        }
                        $arrayParametrosMail = array(
                                                    "login"      => $entityServicio->getPuntoId()->getLogin(),
                                                    "producto"   => $entityServicio->getProductoId()->getDescripcionProducto(),
                                                    "mensaje"    => $strMail
                                                    );

                        $this->envioPlantilla
                             ->generarEnvioPlantilla(
                                                     $strAsunto,
                                                     $arrayDestinatario,
                                                     'NOTIEXCMATASE',
                                                     $arrayParametrosMail,
                                                     $strEmpresaCod,
                                                     '',
                                                     '',
                                                     null,
                                                     false,
                                                     'notificaciones_telcos@telconet.ec'
                                                    );

                    }
                    //FIN VERIFICAMOS SI EXISTE SOLICITUD DE EXCEDENTE DE MATERIAL ATADA A LA DE PLANIFICACIÓN
                    
                }
                // Validamos para los servicios de cableado ethernet
                $strEstAnulado = 'Anulado';
                if ($tipoSolicitud == "solicitud de instalacion cableado ethernet")
                {
                    // Validamos que solo sea para producto cableado ethernet
                    $arrayParametroTipos = $em_general->getRepository('schemaBundle:AdmiParametroDet')
                                ->get('VALIDA_PROD_ADICIONAL','COMERCIAL','',
                                'Solicitud cableado ethernet','','','','','','18');
                    if (is_array($arrayParametroTipos) && !empty($arrayParametroTipos))
                    {
                        $objCableParametro = $arrayParametroTipos[0];
                    }
                    if ($entityServicio->getProductoId() != null &&
                        $entityServicio->getProductoId()->getId() == $objCableParametro['valor1'])
                    {
                        $entityServicio->setEstado($strEstAnulado);
                        $em->persist($entityServicio);
                        $em->flush();
                    }
                    // Ingresamos el historial del detalle de la solicitud
                    $objServicioHist = new InfoServicioHistorial();
                    $objServicioHist->setServicioId($entityServicio);
                    $objServicioHist->setObservacion('Se Anula el producto cableado ethernet');
                    $objServicioHist->setIpCreacion($peticion->getClientIp());
                    $objServicioHist->setUsrCreacion($peticion->getSession()->get('user'));
                    $objServicioHist->setFeCreacion(new \DateTime('now'));
                    $objServicioHist->setEstado($strEstAnulado);
                    $em->persist($objServicioHist);
                    $em->flush();
                }
                $entityDetalleSolicitud->setMotivoId($idMotivo);
                $entityDetalleSolicitud->setObservacion($observacion);
                $entityDetalleSolicitud->setEstado($strEstAnulado);
                $em->persist($entityDetalleSolicitud);
                $em->flush();

                //GUARDAR INFO DETALLE SOLICICITUD HISTORIAL
                $entityDetalleSolHist = new InfoDetalleSolHist();
                $entityDetalleSolHist->setDetalleSolicitudId($entityDetalleSolicitud);
                $entityDetalleSolHist->setObservacion($observacion);
                $entityDetalleSolHist->setMotivoId($idMotivo);
                $entityDetalleSolHist->setIpCreacion($peticion->getClientIp());
                $entityDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolHist->setUsrCreacion($peticion->getSession()->get('user'));
                $entityDetalleSolHist->setEstado($strEstAnulado);
                $em->persist($entityDetalleSolHist);
                $em->flush();
                $strRespuesta = "Se anulo la solicitud";
                
                //Anular y Cancelar tarea
                $arrayParametros                    = array();
                $arrayParametros['cargarTiempo']    = "cliente";
                $arrayParametros['esSolucion']      = "N";
                $arrayDetalles = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                                      ->findBy(array('detalleSolicitudId' => $entityDetalleSolicitud->getId()));
                if($arrayDetalles)
                {
                    foreach($arrayDetalles as $detalle)
                    {
                        $soporteService = $this->container->get('soporte.SoporteService');
                        //Cambiar estado a Anulada
                        $arrayParametros['observacion']     = "Anulación de Orden de Trabajo";
                        $arrayParametros['estado']          = "Anulada";
                        $strRespuestaAnulacionTarea = $soporteService->cambiarEstadoTarea($detalle, null, $peticion, $arrayParametros);
                        if($strRespuestaAnulacionTarea != "OK")
                        {
                            $strRespuesta = "Ciertas tareas no pudieron ser anuladas. Favor notificar a Sistemas.";
                            $em->getConnection()->rollback();
                            $em_comunicacion->getConnection()->rollback();
                            return $strRespuesta;
                        }
                        //Cambiar estado a Cancelada
                        $arrayParametros['observacion']     = "Cancelación automática por anulación de Orden de Trabajo";
                        $arrayParametros['estado']          = "Cancelada";
                        $strRespuestaCancelacionTarea = $soporteService->cambiarEstadoTarea($detalle, null, $peticion, $arrayParametros);
                        if($strRespuestaCancelacionTarea != "OK")
                        {
                            $strRespuesta = "Ciertas tareas no pudieron ser canceladas. Favor notificar a Sistemas.";
                            $em->getConnection()->rollback();
                            $em_comunicacion->getConnection()->rollback();
                            return $strRespuesta;
                        }
                    }
                }

                //------- COMUNICACIONES --- NOTIFICACIONES 

                $mensaje = $this->templating->render('planificacionBundle:Coordinar:notificacion.html.twig', 
                                                     array('detalleSolicitud' => $entityDetalleSolicitud,
                    'detalleSolicitudHist' => $entityDetalleSolHist,
                    'motivo' => $entityMotivo));

                if ($tipoSolicitud == 'solicitud cambio equipo por soporte' || $tipoSolicitud == 'solicitud cambio equipo por soporte masivo')
                {
                    $asunto = "Anulacion de Solicitud Cambio de Equipo por Soporte #" . $entityDetalleSolicitud->getId();
                }
                else
                {
                    $asunto = "Anulacion de Solicitud de Instalacion #" . $entityDetalleSolicitud->getId();
                }
                
                //DESTINATARIOS.... 
                $formasContacto = $em->getRepository('schemaBundle:InfoPersona')
                    ->getContactosByLoginPersonaAndFormaContacto($entityServicio->getPuntoId()->getUsrVendedor(), 'Correo Electronico');
                $to = array();
                $cc = array();
                $cc[] = 'notificaciones_telcos@telconet.ec';

                if($prefijoEmpresa == "TTCO")
                {
                    $to[] = 'rortega@trans-telco.com';
                    $cc[] = 'sac@trans-telco.com';
                }
                else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "EN")
                {
                    $to[] = 'notificaciones_telcos@telconet.ec';
                }

                if($formasContacto)
                {
                    foreach($formasContacto as $formaContacto)
                    {
                        $to[] = $formaContacto['valor'];
                    }
                }
                //ENVIO DE MAIL
                $message = \Swift_Message::newInstance()
                    ->setSubject($asunto)
                    ->setFrom('notificaciones_telcos@telconet.ec')
                    ->setTo($to)
                    ->setCc($cc)
                    ->setBody($mensaje, 'text/html')
                ;
                if($this->mailerSend == "true")
                {
                    $this->mailer->send($message);
                }
            }
            else
            {
                $strRespuesta = "No existe el detalle de solicitud";
            }

            /*Se identifica si es preferencial y tiene adicionales, de ser asi se envia a actualizar el estado de los servicios y
            solicitudes.*/
            if($prefijoEmpresa == "TN" && is_object($entityServicio))
            {
                $arrayParametros['strEstadoServicio']           = $strEstadoServicio;
                $arrayParametros["strOpcion"]                   = "Anulado";
                $arrayParametros["strObservacion"]              = $observacion;
                $arrayParametros["intIdMotivo"]                 = $idMotivo;
                $arrayParametros["objRequest"]                  = $peticion;
                $arrayParametros["objSession"]                  = $peticion->getSession();
                $arrayParametros["strUsrCreacion"]              = $peticion->getSession()->get('user');
                $arrayParametros["strIpCreacion"]               = $peticion->getClientIp();
                $arrayParametros["strOjServicioPreferencial"]   = $entityServicio;
                $arrayParametros["strCodEmpresa"]               = $strEmpresaCod;
                $arrayParametros["strPrefijoEmpresa"]           = $prefijoEmpresa;

                $this->serviceServicioComercial->actualizarServiciosYSolicitudesAdicionales($arrayParametros);
            }

            $em->getConnection()->commit();
            $em_comunicacion->getConnection()->commit();
            $this->emSoporte->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em_comunicacion->getConnection()->rollback();
            $this->emSoporte->getConnection()->rollback();
            $mensajeError = "Error: " . $e->getMessage();
            $strRespuesta = $mensajeError;
        }
        return $strRespuesta;
    }

    /**
     * Método que permite mostrar en una celda todos los servicios para instalacion simultanea contabilizados.
     *
     * @param [type] $arraySimultaneos
     * @return false|string|void
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.0 - Version inicial.
     *
     */
    public function getCeldaInstalacionSimultanea($arraySimultaneos)
    {
        $strRespuesta =  null;

        if (!empty($arraySimultaneos) && is_array($arraySimultaneos))
        {
            $emCom      = $this->emComercial;

            $arrayContabilizados = array();

            foreach ($arraySimultaneos as $key => $intIdServicio)
            {
                $objServicio  = $emCom->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);

                if (is_object($objServicio))
                {
                    $objProducto  = $emCom->getRepository('schemaBundle:AdmiProducto')->find($objServicio->getProductoId());

                    if (is_object($objProducto))
                    {
                        $arrayContabilizados[] = $objProducto->getDescripcionProducto();
                    }
                }

            }

            $arrayConteo = array_count_values($arrayContabilizados);
            $strTexto = "";

            foreach ($arrayConteo as $key => $value)
            {
                $strTexto .= "$key: $value - ";
            }

            $strRespuesta = substr($strTexto, 0, -3);
        }

        return $strRespuesta;
    }

}
