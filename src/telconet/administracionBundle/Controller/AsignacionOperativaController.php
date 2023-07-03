<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\seguridadBundle\Controller\TokenAuthenticatedController;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;

use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRol;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;

use telconet\schemaBundle\Entity\AdmiCuadrillaHistorial;

/**
 * AsignacionOperativa controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Asignación operativa
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 26-12-2015
 */
class AsignacionOperativaController extends Controller implements TokenAuthenticatedController
{
    const DETALLE_ASOCIADO_ELEMENTO_VEHICULO            = 'CUADRILLA';

    const CATEGORIA_ELEMENTO_TRANSPORTE                 = 'transporte';
    const TIPO_ELEMENTO_VEHICULO                        = 'VEHICULO';

    const DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO = 'ASIGNACION_VEHICULAR_FECHA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN    = 'ASIGNACION_VEHICULAR_FECHA_FIN_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO  = 'ASIGNACION_VEHICULAR_HORA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_HORA_FIN     = 'ASIGNACION_VEHICULAR_HORA_FIN_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED  = 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA'; 

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER                 = 'ASIGNACION_PROVISIONAL_CHOFER';

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO    = 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN       = 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO     = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN        = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN'; 
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO          = 'ASIGNACION_PROVISIONAL_CHOFER_MOTIVO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_ZONA            = 'ASIGNACION_PROVISIONAL_CHOFER_ZONA';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_TAREA           = 'ASIGNACION_PROVISIONAL_CHOFER_TAREA';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO    = 'ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO';
    const DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED      = 'ASIGNACION_PROVISIONAL_ID_SOLICITUD_PREDEF';
    const DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV      = 'ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV'; 
    
    

    const DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA        = 'SOLICITUD ASIGNACION VEHICULAR PREDEFINIDA';
    const NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA        = 'ZONA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA       = 'TAREA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO= 'DEPARTAMENTO_PREDEFINIDO_ASIGNACION_VEHICULAR';
    
    const NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV        = 'ZONA_ASIGNACION_PROVISIONAL_CHOFER';
    const NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV       = 'TAREA_ASIGNACION_PROVISIONAL_CHOFER';
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV= 'DEPARTAMENTO_ASIGNACION_PROVISIONAL_CHOFER';

    const DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL = "SOLICITUD CHOFER PROVISIONAL";
    
    /**
     * @Secure(roles="ROLE_328-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redirección a la pantalla principal de la administracion de Asignación Operativa
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 26-12-2015
     *
     */
    public function indexAction()
    {
        $emSeguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        
        $entityItemMenu  = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("328", "1");
        
        $rolesPermitidos = array();

        
        //MODULO 328 - ASIGNACIONOPERATIVA/asignarChoferAVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_328-3659'))
        {
            $rolesPermitidos[] = 'ROLE_328-3659';
        }
        
        //MODULO 328 - ASIGNACIONOPERATIVA/eliminarAsignacionVehiculoChofer
        if(true === $this->get('security.context')->isGranted('ROLE_328-3537'))
        {
            $rolesPermitidos[] = 'ROLE_328-3537';
        }
        
        //MODULO 328 - ASIGNACIONOPERATIVA/showHistorialAsignacionesXVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_328-3657'))
        {
            $rolesPermitidos[] = 'ROLE_328-3657';
        }
        
        //MODULO 328 - ASIGNACIONOPERATIVA/showHistorialAsignacionProvisionalChofer
        if(true === $this->get('security.context')->isGranted('ROLE_328-3658'))
        {
            $rolesPermitidos[] = 'ROLE_328-3658';
        }
        //MODULO 328 - ASIGNACIONOPERATIVA/exportPDFAsignacionProvisionalChofer
        if(true === $this->get('security.context')->isGranted('ROLE_328-3677'))
        {
            $rolesPermitidos[] = 'ROLE_328-3677';
        }

        return $this->render( 'administracionBundle:AsignacionOperativa:index.html.twig', 
                              array(
                                        'item'                   => $entityItemMenu,
                                        'rolesPermitidos'        => $rolesPermitidos,
                                        'strCategoriaTransporte' => self::CATEGORIA_ELEMENTO_TRANSPORTE
                                    )
                            );
    }
    
    /**
     * @Secure(roles="ROLE_328-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todos los vehículos: placa, modelo, número de motor y chasis.
     * Si el vehículo está asignado a una cuadrilla, se muestra la información de la cuadrilla : nombre, turno de Inicio y turno Fin,
     * así como también la información del chofer asignado a dicha cuadrilla.
     * Si el vehículo tiene asignado un chofer se mostrará la información de este chofer.
     *
     *
     * @return Response 

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 02-02-2016 - Se realizaron ajustes para asignar un chofer a un vehículo.
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 24-08-2016 - Se realizaron ajustes para incluir las asignaciones predefinidas de chofer por horario
     * 
     */ 
    public function gridAction()
    {
        $em                 = $this->getDoctrine()->getManager();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $objResponse        = new Response();
        
        $strPlaca                   = $objRequest->query->get('placa') ? $objRequest->query->get('placa') : "";
        $strDisco                   = $objRequest->query->get('disco') ? $objRequest->query->get('disco') : "";
        $intModeloMedioTransporte   = $objRequest->query->get('modeloMedioTransporte') ? $objRequest->query->get('modeloMedioTransporte') : "";
        $arrayModelosElemento       = $intModeloMedioTransporte ? array( $intModeloMedioTransporte ) : array();
        $strTipoElemento            = array( self::TIPO_ELEMENTO_VEHICULO );
        
        $strNombreCuadrilla         = $objRequest->query->get('nombreCuadrilla') ? $objRequest->query->get('nombreCuadrilla') : "";
        $strNombresChofer           = $objRequest->query->get('nombres') ? $objRequest->query->get('nombres') : "";
        $strApellidosChofer         = $objRequest->query->get('apellidos') ? $objRequest->query->get('apellidos') : "";
        $strIdentificacionChofer    = $objRequest->query->get('identificacion') ? $objRequest->query->get('identificacion') : "";
        
        $intStart                   = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                   = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
        $codEmpresaSession          = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        
        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        $objCaracteristicaZonaPredefinida = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);
        
        $objCaracteristicaTareaPredefinida = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);
        
        $objCaracteristicaDepartamentoPredefinido = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
        
        
        
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $em->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }  
        $arrayParametrosAsignacion  = array(
                                        'codEmpresa'                => $codEmpresaSession,
                                        'intStart'                  => $intStart,
                                        'intLimit'                  => $intLimit,
                                        'tipoElemento'              => $strTipoElemento,
                                        'intIdTipoSolicitud'        => $objTipoSolicitud->getId(),
                                        'intIdCaracteristicaDepartamentoPredefinido'    => $objCaracteristicaDepartamentoPredefinido->getId(),
                                        'intIdCaracteristicaZonaPredefinida'            => $objCaracteristicaZonaPredefinida->getId(),
                                        'intIdCaracteristicaTareaPredefinida'           => $objCaracteristicaTareaPredefinida->getId(),
                                        'arrayDetallesFechasYHorasAsignacionVehicular' => array(
                                                                                                'strDetalleSolicitudAsignacionVehicular'    =>
                                                                                                    self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                                                                                'strDetalleFechaInicioAsignacionVehicular'  =>
                                                                                                    self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                                                                                'strDetalleFechaFinAsignacionVehicular'     =>
                                                                                                    self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN,
                                                                                                'strDetalleHoraInicioAsignacionVehicular'   =>
                                                                                                    self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                                                                                'strDetalleHoraFinAsignacionVehicular'      =>
                                                                                                    self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN
                                                                        ),
                                         'arrayDetallesFechasYHorasAsignacionProvisional' => array(
                                                                                        'strDetalleSolPredProv'                             =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                                                                        'strDetalleSolProv'                                 =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV,
                                                                                        'strDetalleFechaInicioAsignacionProvisionalChofer'  =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO,
                                                                                        'strDetalleFechaFinAsignacionProvisionalChofer'     =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN,
                                                                                        'strDetalleHoraInicioAsignacionProvisionalChofer'   =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO,
                                                                                        'strDetalleHoraFinAsignacionProvisionalChofer'      =>
                                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN
                                                                        ),
                                         'arrayDetallesDepartamentoZonaTareaAsignacionProvisional' => array(
                                                                            'strDetalleDepartamentoAsignacionProvisionalChofer'             =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO,
                                                                            'strDetalleZonaAsignacionProvisionalChofer'                     =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_ZONA,
                                                                            'strDetalleTareaAsignacionProvisionalChofer'                    =>
                                                                                self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_TAREA
                                                                        ),
                                                                                                       
            
                                        'strDetalleCuadrilla'           => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                        'strDetalleChoferProvisional'   => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER,
                                        'strEstadoActivo'               => 'Activo',
                                        'strEstadoPrestado'             => 'Prestado',
                                        'criterios'                     => array(
                                                                            'placa'                     => $strPlaca,
                                                                            'modeloElemento'            => $arrayModelosElemento,
                                                                            'detallesElemento'          => array(
                                                                                                            'disco'     => $strDisco,
                                                                                                            'region'    => $strRegion
                                                                                                        ),
                                                                            'nombreCuadrilla'           => $strNombreCuadrilla,
                                                                            'nombresChofer'             => $strNombresChofer,
                                                                            'apellidosChofer'           => $strApellidosChofer,
                                                                            'identificacionChofer'      => $strIdentificacionChofer,
                                                                            
                                                                            )
                                            );
        
        $objJson    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                        ->getJSONAsignacionOperativaByCriterios( $arrayParametrosAsignacion,$em,$emGeneral,$emSoporte);
        $objResponse->setContent($objJson);
        return $objResponse;
    }

    /**
     * Secure(roles="ROLE_328-7")
     * 
     * Documentación para el método 'motivosAction'.
     *
     * Muestra todos los motivos por los cuales un chofer es reemplazado por otro chofer
     *
     * @return Response 

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 28-12-2015
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 02-02-2016 - Se realizaron ajustes para asignar un chofer a un vehículo.
     * 
     */ 
    public function motivosAction()
    {
        $objResponse    = new Response();
        $objRequest  = $this->get('request');
        $em = $this->getDoctrine()->getManager('telconet');

        $strModulo    = $objRequest->get('strModulo');
        $strAccion    = $objRequest->get('strAccion');
        
        $arrayParametros    = array(
            "nombreModulo"  => $strModulo,
            "nombreAccion"  => $strAccion,
            "estados"       => array(
                "estadoActivo"    => "Activo",
                "estadoModificado"=> "Modificado"
            )
            
        );
        
        $objJson    = $em->getRepository('schemaBundle:AdmiMotivo')
                                        ->getJSONMotivosPorModuloYPorAccion( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * 
     * Documentación para el método 'gridChoferesDisponiblesAction'.
     *
     * Muestra el listado de todos los choferes con su respectiva información: identificación, nombres y apellidos 
     * que aún no han sido asignados a un vehículo .
     *
     * @return Response 

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-02-2016

     */ 
    public function gridChoferesDisponiblesAction()
    {
        $em                     = $this->get('doctrine')->getManager('telconet');
        $objRequest             = $this->getRequest();
        $objResponse            = new Response();

        $identificacionChoferDisponible = $objRequest->get("identificacionChoferDisponible") ? 
                                            $objRequest->get("identificacionChoferDisponible") : '';
        $nombresChoferDisponible        = $objRequest->get("nombresChoferDisponible") ? $objRequest->get("nombresChoferDisponible") : '';
        $apellidosChoferDisponible      = $objRequest->get("apellidosChoferDisponible") ? $objRequest->get("apellidosChoferDisponible") : '';

        $idPerChoferAsignadoXVehiculo   = $objRequest->get("idPerChoferAsignadoXVehiculo") ? $objRequest->get("idPerChoferAsignadoXVehiculo") : '';

        
        $boolErrorFechasHoras           = $objRequest->get('errorFechasHoras') ? $objRequest->get('errorFechasHoras') : 0;
        $intLimit                       = $objRequest->get("limit");
        $intStart                       = $objRequest->get("start");

        $intIdEmpresaSession            = $objRequest->getSession()->get('idEmpresa');

        $strFechaDesdeAsignacion    = $objRequest->get('strFechaDesdeAsignacion') ? trim($objRequest->get('strFechaDesdeAsignacion')) : '';
        $strFechaHastaAsignacion    = $objRequest->get('strFechaHastaAsignacion') ? trim($objRequest->get('strFechaHastaAsignacion')) : '';
        $strHoraDesdeAsignacion     = $objRequest->get('strHoraDesdeAsignacion') ? trim($objRequest->get('strHoraDesdeAsignacion')) : '';
        $strHoraHastaAsignacion     = $objRequest->get('strHoraHastaAsignacion') ? trim($objRequest->get('strHoraHastaAsignacion')) : '';

        $strEstadoEliminado             = 'Eliminado';
        $strDescripcionCaracteristica   = 'CARGO';
        $strDescripcionRol              = 'Chofer';


        $objTipoSolicitudAsignacionPredefinida = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        $objCaracteristicaDepartamentoPredefinido = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
        $arrayParametros = array(
                                    'intLimit'                      => $intLimit,
                                    'intStart'                      => $intStart,
                                    'strEstadoActivo'               => 'Activo',
                                    'intEmpresa'                    => $intIdEmpresaSession,
                                    'strFechaDesdeAsignacion'       => $strFechaDesdeAsignacion,
                                    'strFechaHastaAsignacion'       => $strFechaHastaAsignacion,
                                    'strHoraDesdeAsignacion'        => $strHoraDesdeAsignacion,
                                    'strHoraHastaAsignacion'        => $strHoraHastaAsignacion,
                                    'strDescripcionCaracteristica'  => $strDescripcionCaracteristica,
                                    'strEstadoEliminado'            => $strEstadoEliminado,
                                    'strDescripcionRol'             => $strDescripcionRol,
                                    'idPerChoferAsignadoXVehiculo'  => $idPerChoferAsignadoXVehiculo,
                                    'errorFechasHoras'              => $boolErrorFechasHoras,
                                    'intIdTipoSolicitudAsignacionPredefinida'   => $objTipoSolicitudAsignacionPredefinida->getId(),
                                    'intIdCaractDepartamentoPredefinido'        => $objCaracteristicaDepartamentoPredefinido->getId(),
                                    'criterios_chofer'              => array(
                                                                                'identificacionChoferDisponible'  => $identificacionChoferDisponible,
                                                                                'nombresChoferDisponible'         => $nombresChoferDisponible,
                                                                                'apellidosChoferDisponible'       => $apellidosChoferDisponible
                                                                    ),
                                    "arrayDetallesVehicular"=> array(
                                                                        'strDetalleSolicitudAsignacionVehicular'    =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                                                        'strDetalleCuadrillaAsignacionVehicular'    =>
                                                                            self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                        'strDetalleFechaInicioAsignacionVehicular'   =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                                                        'strDetalleHoraInicioAsignacionVehicular'    =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                                                        'strDetalleHoraFinAsignacionVehicular'    =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN
                                                                    ),
                                    'arrayDetallesProvisional'=> array(
                                                                        'strDetalleChoferAsignacionProvisional'    =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER, 
                                                                        'strDetalleFechaInicioAsignacionProvisional'   =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO,
                                                                        'strDetalleFechaFinAsignacionProvisional'   =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN,
                                                                        'strDetalleHoraInicioAsignacionProvisional'    =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO,
                                                                        'strDetalleHoraFinAsignacionProvisional'    =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN,
                                                                        'strDetalleMotivoAsignacionProvisional'    =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO
                                                                    )                          
                                );


        $objJson    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getJSONChoferesDisponibles( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_328-3659")
     * 
     * Documentación para el método 'asignarChoferProvisionalAVehiculoAction'.
     *
     * Asignar un chofer como un detalle del elemento o vehículo 
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-02-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 25-08-2016  Se realizan modificaciones en la asignación provisional, ya que ahora también se deben considerar los horarios
     *                          de las asignaciones predefinidas de chofer
     */ 
    public function asignarChoferProvisionalAVehiculoAction()
    {
        
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $em                 = $this->getDoctrine()->getManager();
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $strMensaje         = '';
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado= 'Eliminado';
        $strEstadoPendiente = 'Pendiente';
        
        $intIdElementoVehiculo  = $objRequest->get('idElementoVehiculo') ? $objRequest->get('idElementoVehiculo') : '';
        
        $intIdPerChoferCuadrilla= $objRequest->get('intIdPerChoferCuadrilla') ? $objRequest->get('intIdPerChoferCuadrilla') : '';
        
        $intIdPerChoferPredefinido= $objRequest->get('intIdPerChoferPredefinido') ? $objRequest->get('intIdPerChoferPredefinido') : '';
        
        $intIdPerChofer         = $objRequest->get('idPersonaEmpresaRolChofer') ? $objRequest->get('idPersonaEmpresaRolChofer') : '';
        
        $objElementoVehiculo    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneBy( array('id' => $intIdElementoVehiculo, 'estado' => $strEstadoActivo) );
        
        $objPerChoferAsignacion = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerChofer);
        
        $objPersonaChoferAsignacion=$emInfraestructura->getRepository('schemaBundle:InfoPersona')
                                                        ->find($objPerChoferAsignacion->getPersonaId()->getId());
        $strDetalleChoferProvisional= self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER;
        $strTipoAsignacionZonaTarea   = $objRequest->get('strTipoAsignacionZonaTarea') ? $objRequest->get('strTipoAsignacionZonaTarea') : '';
        $intIdZonaChoferProv          = $objRequest->get('intIdZona') ? $objRequest->get('intIdZona') : '';
        $intIdTareaChoferProv         = $objRequest->get('intIdTarea') ? $objRequest->get('intIdTarea') : '';

        $intIdDepartamentoChoferProv = $objRequest->get('intIdDepartamento') ? $objRequest->get('intIdDepartamento') : '';

        $strMensajeObservacionProvisional= 'Se asigna chofer provisional ';
        $strMotivoChoferProvisional     = 'Se asigna chofer provisional';
        $objMotivoChoferProvisional     = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoChoferProvisional);
        $intIdMotivoChoferProvisional   = $objMotivoChoferProvisional ? $objMotivoChoferProvisional->getId() : 0;


        $strMensajeObservacionDepartamentoChoferProv   = "";
        $strMensajeObservacionZonaTareaChoferProv      = "";
        
        $em->getConnection()->beginTransaction();
        $emSoporte->getConnection()->beginTransaction();
        $emInfraestructura->getConnection()->beginTransaction();
        try
        {
            if($objPerChoferAsignacion)
            {
                
                $boolCrearDetalleChofer = false;

                $boolCrearDetalleFechaInicio = false;
                $boolCrearDetalleFechaFin  = false;
                
                $boolCrearDetalleHoraInicio = false;
                $boolCrearDetalleHoraFin  = false;

                $boolCrearDetalleMotivo = false;
                
                $boolCrearDetalleDepartamento   = false;
                $boolCrearDetalleZona           = false;
                $boolCrearDetalleTarea          = false;
                $boolCrearDetalleIdSolPredef    = false;
                $boolCrearDetalleIdSolProv      = false;
                
                
                $strDetalleFechaInicio  = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO;
                $strDetalleFechaFin     = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN;
                $strDetalleHoraInicio   = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO;
                $strDetalleHoraFin      = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN;
                $strDetalleMotivo       = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO;
                $strDetalleDepartamento = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO;
                $strDetalleZona         = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_ZONA;
                $strDetalleTarea        = self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_TAREA;
                $strDetalleIdSolPred    = self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED;
                $strDetalleIdSolProv    = self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV;
                
                $strTipoAsignacion  = $objRequest->get('strTipoAsignacion') ? trim($objRequest->get('strTipoAsignacion')) : '';
                $intIdCuadrilla     = $objRequest->get('intIdCuadrilla') ? $objRequest->get('intIdCuadrilla') : 0;
                $strNombreCuadrilla = $objRequest->get('strNombreCuadrilla') ? $objRequest->get('strNombreCuadrilla') : '';
                $intIdMotivo        = $objRequest->get('intIdMotivo') ? $objRequest->get('intIdMotivo') : 0;
                $strObservacion     = $objRequest->get('strObservacionAsignacion') ? trim($objRequest->get('strObservacionAsignacion')) : '';
                $strFechaDesdeAsignacion    = $objRequest->get('strFechaDesdeAsignacion') ? trim($objRequest->get('strFechaDesdeAsignacion')) : '';
                $strFechaHastaAsignacion    = $objRequest->get('strFechaHastaAsignacion') ? trim($objRequest->get('strFechaHastaAsignacion')) : '';
                $strHoraDesdeAsignacion     = $objRequest->get('strHoraDesdeAsignacion') ? trim($objRequest->get('strHoraDesdeAsignacion')) : '';
                $strHoraHastaAsignacion     = $objRequest->get('strHoraHastaAsignacion') ? trim($objRequest->get('strHoraHastaAsignacion')) : '';
                
                $intIdSolicitudChoferPredefinido    = $objRequest->get('intIdSolicitudChoferPredefinido') ? 
                                                        trim($objRequest->get('intIdSolicitudChoferPredefinido')) : '';

                
                //Crear un Info Detalle Solicitud
                $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL);
                
                $objInfoDetalleSolicitud = new InfoDetalleSolicitud();
                
                $objInfoDetalleSolicitud->setElementoId($intIdElementoVehiculo);
                $objInfoDetalleSolicitud->setEstado($strEstadoPendiente);
                $objInfoDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                
                //Se hace obligatorio escoger un motivo para cualquier caso
                if($intIdMotivo!=0)
                {
                    $objInfoDetalleSolicitud->setMotivoId($intIdMotivo);
                }
                
                $objInfoDetalleSolicitud->setObservacion($strObservacion);
                $objInfoDetalleSolicitud->setFeCreacion($datetimeActual);
                $objInfoDetalleSolicitud->setUsrCreacion($strUserSession);
                $em->persist($objInfoDetalleSolicitud);
                $em->flush();
                
                //Crear un Info Detalle Solicitud Historial Pendiente
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setEstado($strEstadoPendiente);
                $objInfoDetalleSolHist->setFeCreacion($datetimeActual);
                $objInfoDetalleSolHist->setUsrCreacion($strUserSession);
                $objInfoDetalleSolHist->setIpCreacion($strIpUserSession);
                
                //Se hace obligatorio escoger un motivo para cualquier caso
                if($intIdMotivo!=0)
                {
                    $objInfoDetalleSolHist->setMotivoId($intIdMotivo);
                }
                
                list($dayDesdeAsignacion,$mesDesdeAsignacion,$yearDesdeAsignacion)=explode('/',$strFechaDesdeAsignacion);
                list($horaDesdeAsignacion,$minutosDesdeAsignacion)=explode(':',$strHoraDesdeAsignacion);
                $datetimeDesdeAsignacion     = new \DateTime();
                $datetimeDesdeAsignacion->setDate($yearDesdeAsignacion, $mesDesdeAsignacion, $dayDesdeAsignacion);
                $datetimeDesdeAsignacion->setTime($horaDesdeAsignacion, $minutosDesdeAsignacion, '00');
                
                list($dayHastaAsignacion,$mesHastaAsignacion,$yearHastaAsignacion)=explode('/',$strFechaHastaAsignacion);
                list($horaHastaAsignacion,$minutosHastaAsignacion)=explode(':',$strHoraHastaAsignacion);
                $datetimeHastaAsignacion     = new \DateTime();
                $datetimeHastaAsignacion->setDate($yearHastaAsignacion, $mesHastaAsignacion, $dayHastaAsignacion);
                $datetimeHastaAsignacion->setTime($horaHastaAsignacion, $minutosHastaAsignacion, '00');
                
                $objInfoDetalleSolHist->setFeIniPlan($datetimeDesdeAsignacion);
                $objInfoDetalleSolHist->setFeFinPlan($datetimeHastaAsignacion);
                $em->persist($objInfoDetalleSolHist);
                $em->flush();
                
                $objInfoDetalle = new InfoDetalle();
                $objInfoDetalle->setDetalleSolicitudId($objInfoDetalleSolicitud->getId());
                $objInfoDetalle->setFeCreacion($datetimeActual);
                $objInfoDetalle->setUsrCreacion($strUserSession);
                $objInfoDetalle->setIpCreacion($strIpUserSession);
                $objInfoDetalle->setPesoPresupuestado(0);
                $objInfoDetalle->setValorPresupuestado(0);
                $emSoporte->persist($objInfoDetalle);
                $emSoporte->flush();
                
                $objInfoDetalleAsignacion = new InfoDetalleAsignacion();
                $objInfoDetalleAsignacion->setDetalleId($objInfoDetalle);
                $objInfoDetalleAsignacion->setFeCreacion($datetimeActual);
                $objInfoDetalleAsignacion->setUsrCreacion($strUserSession);
                $objInfoDetalleAsignacion->setIpCreacion($strIpUserSession);
              
                //CUADRILLA o EMPLEADO O VEHICULO
                $objInfoDetalleAsignacion->setTipoAsignado($strTipoAsignacion);
                
                if($strTipoAsignacion=='CUADRILLA')
                {
                    $objCuadrilla=$em->getRepository("schemaBundle:AdmiCuadrilla")->find($intIdCuadrilla);
                    
                    //$asignadoId idCuadrilla
                    $objInfoDetalleAsignacion->setAsignadoId($objCuadrilla->getId());
                    
                    //$asignadoNombre nombreCuadrilla
                    $objInfoDetalleAsignacion->setAsignadoNombre($objCuadrilla->getNombreCuadrilla());
                    
                    $objMotivoChofer=$emGeneral->getRepository("schemaBundle:AdmiMotivo")->find($intIdMotivo);
                    
                    $objInfoDetalleAsignacion->setMotivo($objMotivoChofer->getNombreMotivo());
                    
                    //Busca si existe el detalle de asociación entre el vehículo y la 'CUADRILLA'
                    $objDetalleVehiculoCuadrilla = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                                    'estado'        => $strEstadoActivo,
                                                                                    'detalleValor'  => $intIdCuadrilla
                                                                                ) 
                                                                           );
                    $objDetalleElementoChoferAsignado     = null;
                    if($objDetalleVehiculoCuadrilla)
                    {
                        //Busca si existe el detalle de asociación entre el vehículo y un 'CHOFER' provisional asociado a la 'CUADRILLA' 
                        $objDetalleElementoChoferAsignado = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                ->findOneBy( array( 
                                                                                                'elementoId'    => $intIdElementoVehiculo,
                                                                                                'detalleNombre' => $strDetalleChoferProvisional,
                                                                                                'estado'        => $strEstadoEliminado,
                                                                                                'parent'        => $objDetalleVehiculoCuadrilla
                                                                                            ) 
                                                                                       );
                        
                        if($objDetalleElementoChoferAsignado)
                        {
                            $objDetalleSolicitudPredefinida = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                                ->findOneBy( array( 
                                                                                                'elementoId'    => $intIdElementoVehiculo,
                                                                                                'detalleNombre' => $strDetalleIdSolPred,
                                                                                                'detalleValor'  => $intIdSolicitudChoferPredefinido,
                                                                                                'estado'        => $strEstadoEliminado,
                                                                                                'parent'        => $objDetalleElementoChoferAsignado
                                                                                            ) 
                                                                                       );
                            
                        }
                    }
                }
                else if($strTipoAsignacion=='EMPLEADO')
                {
                    $objDepartamentoChofer=$emGeneral->getRepository("schemaBundle:AdmiDepartamento")
                                                    ->find($objPerChoferAsignacion->getDepartamentoId());
                    
                    //$asignadoId idDepartamento
                    $objInfoDetalleAsignacion->setAsignadoId($objDepartamentoChofer->getId());
                    
                    //$asignadoNombre nombreDepartamento
                    $objInfoDetalleAsignacion->setAsignadoNombre($objDepartamentoChofer->getNombreDepartamento());
                    
                    //Busca si existe el detalle de asociación entre el vehículo y un 'CHOFER' provisional pero que no está asociado a la cuadrilla
                    
                    //Buscar detalle con el id de la solicitud predefinida 
                    $objDetalleSolicitudPredefinida = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findOneBy( array( 
                                                                                            'elementoId'    => $intIdElementoVehiculo,
                                                                                            'detalleNombre' => $strDetalleIdSolPred,
                                                                                            'detalleValor'  => $intIdSolicitudChoferPredefinido,
                                                                                            'estado'        => $strEstadoEliminado
                                                                                        ) 
                                                                                   );
                    
                    if($objDetalleSolicitudPredefinida)
                    {
                        $objDetalleElementoChoferAsignado =  $objDetalleSolicitudPredefinida->getParent();
                    }
                    
                    
                     
               }
                //Se asigna directamente al vehiculo)
                else
                {
                    $objInfoDetalleAsignacion->setAsignadoId($intIdElementoVehiculo);
                    
                    //$asignadoNombre nombreDepartamento
                    $objInfoDetalleAsignacion->setAsignadoNombre($objElementoVehiculo->getNombreElemento());
                    
                    $objDetalleElementoChoferAsignado = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                            ->findOneBy( array( 
                                                                                            'elementoId'    => $intIdElementoVehiculo,
                                                                                            'detalleNombre' => $strDetalleChoferProvisional,
                                                                                            'estado'        => $strEstadoEliminado
                                                                                        ) 
                                                                                   );
                    
                }
                
                //$refAsignadoId personaId
                $objInfoDetalleAsignacion->setRefAsignadoId($objPersonaChoferAsignacion->getId());

                //NombrePersonaId
                $objInfoDetalleAsignacion->setRefAsignadoNombre($objPersonaChoferAsignacion->getNombres()
                                                                ." ".$objPersonaChoferAsignacion->getApellidos());

                $objInfoDetalleAsignacion->setPersonaEmpresaRolId($intIdPerChofer);
                
                $emSoporte->persist($objInfoDetalleAsignacion);
                $emSoporte->flush();
                
                
                if($strTipoAsignacionZonaTarea=='ZONA')
                {
                    //Se crea un Info Detalle Solicitud Caracteristica para la zona del chofer provisional
                    $objCaracteristicaZonaChoferProv = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV);

                    $objInfoDetalleSolCaractZona = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractZona->setCaracteristicaId($objCaracteristicaZonaChoferProv);
                    $objInfoDetalleSolCaractZona->setValor($intIdZonaChoferProv);
                    $objInfoDetalleSolCaractZona->setDetalleSolicitudId($objInfoDetalleSolicitud);
                    $objInfoDetalleSolCaractZona->setEstado($strEstadoActivo);
                    $objInfoDetalleSolCaractZona->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolCaractZona->setUsrCreacion($strUserSession);
                    $em->persist($objInfoDetalleSolCaractZona);
                    $em->flush();

                    $objZona=$emGeneral->getRepository('schemaBundle:AdmiZona')->find($intIdZonaChoferProv);
                    $strMensajeObservacionZonaTareaChoferProv.="Zona:".$objZona->getNombreZona()."<br/>";
                    
                    
                }
                else if($strTipoAsignacionZonaTarea=='TAREA')
                {
                    //Se crea un Info Detalle Solicitud Carcteristica para la tarea del chofer provisional
                    $objCaracteristicaTareaChoferProv = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV);

                    $objInfoDetalleSolCaractTarea = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractTarea->setCaracteristicaId($objCaracteristicaTareaChoferProv);
                    $objInfoDetalleSolCaractTarea->setValor($intIdTareaChoferProv);
                    $objInfoDetalleSolCaractTarea->setDetalleSolicitudId($objInfoDetalleSolicitud);
                    $objInfoDetalleSolCaractTarea->setEstado($strEstadoActivo);
                    $objInfoDetalleSolCaractTarea->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolCaractTarea->setUsrCreacion($strUserSession);
                    $em->persist($objInfoDetalleSolCaractTarea);
                    $em->flush();

                    $objTarea=$emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTareaChoferProv);
                    $strMensajeObservacionZonaTareaChoferProv.="Tarea:".$objTarea->getNombreTarea()."<br/>";
                }



                //Se crea un Info Detalle Solicitud Carcteristica para el departamento de la asignacion provisional
                $objCaracteristicaDepartamentoChoferProv = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV);

                $objInfoDetalleSolCaractDepartamento = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaractDepartamento->setCaracteristicaId($objCaracteristicaDepartamentoChoferProv);
                $objInfoDetalleSolCaractDepartamento->setValor($intIdDepartamentoChoferProv);
                $objInfoDetalleSolCaractDepartamento->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolCaractDepartamento->setEstado($strEstadoActivo);
                $objInfoDetalleSolCaractDepartamento->setFeCreacion($datetimeActual);
                $objInfoDetalleSolCaractDepartamento->setUsrCreacion($strUserSession);
                $em->persist($objInfoDetalleSolCaractDepartamento);
                $em->flush();

                
                $objDepartamento=$emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamentoChoferProv);
                $strMensajeObservacionDepartamentoChoferProv.="Departamento:".$objDepartamento->getNombreDepartamento()."<br/>";

                if($objDetalleElementoChoferAsignado)
                {
                    $objDetalleElementoChoferAsignado->setDetalleValor($intIdPerChofer);
                    $objDetalleElementoChoferAsignado->setEstado($strEstadoActivo);
                    $emInfraestructura->persist($objDetalleElementoChoferAsignado);
                    $emInfraestructura->flush();
                    
                    //Verificar si existe el detalle de el id de la solicitud predefinida, solo en caso de asignación al empleado
                    if($strTipoAsignacion=="EMPLEADO" || $strTipoAsignacion=="CUADRILLA")
                    {
                        if($objDetalleSolicitudPredefinida)
                        {
                            $objDetalleSolicitudPredefinida->setDetalleValor($intIdSolicitudChoferPredefinido);
                            $objDetalleSolicitudPredefinida->setEstado($strEstadoActivo);
                            $emInfraestructura->persist($objDetalleSolicitudPredefinida);
                            $emInfraestructura->flush();
                        }
                        else
                        {
                            $boolCrearDetalleIdSolPredef=true;
                        }
                    }
                    
                    //Buscar detalle con el id de la solicitud provisional
                    $objDetalleChoferSolProv = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                 ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => $strDetalleIdSolProv,
                                                                                    'parent'        => $objDetalleElementoChoferAsignado,
                                                                                    'estado'        => $strEstadoEliminado
                                                                                ) 
                                                                           );
                    if($objDetalleChoferSolProv)
                    {
                        $objDetalleChoferSolProv->setDetalleValor($objInfoDetalleSolicitud->getId());
                        $objDetalleChoferSolProv->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferSolProv);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleIdSolProv=true;
                    }
                    
                    //Buscar detalle de fecha inicio
                    $objDetalleChoferFechaInicio = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => $strDetalleFechaInicio,
                                                                                    'parent'        => $objDetalleElementoChoferAsignado,
                                                                                    'estado'        => $strEstadoEliminado
                                                                                ) 
                                                                           );
                    if($objDetalleChoferFechaInicio)
                    {
                        $objDetalleChoferFechaInicio->setDetalleValor($strFechaDesdeAsignacion);
                        $objDetalleChoferFechaInicio->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferFechaInicio);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleFechaInicio=true;
                    }
                    
                    //Buscar detalle de fecha fin
                    $objDetalleChoferFechaFin = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => $strDetalleFechaFin,
                                                                                    'parent'        => $objDetalleElementoChoferAsignado,
                                                                                    'estado'        => $strEstadoEliminado
                                                                                ) 
                                                                           );
                    if($objDetalleChoferFechaFin)
                    {
                        $objDetalleChoferFechaFin->setDetalleValor($strFechaHastaAsignacion);
                        $objDetalleChoferFechaFin->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferFechaFin);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleFechaFin=true;
                    }
                    
                    
                    //Buscar Detalle de Hora Inicio
                    $objDetalleChoferHoraInicio = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => $strDetalleHoraInicio,
                                                                                    'parent'        => $objDetalleElementoChoferAsignado,
                                                                                    'estado'        => $strEstadoEliminado
                                                                                ) 
                                                                           );
                    if($objDetalleChoferHoraInicio)
                    {
                        $objDetalleChoferHoraInicio->setDetalleValor($strHoraDesdeAsignacion);
                        $objDetalleChoferHoraInicio->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferHoraInicio);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleHoraInicio=true;
                    }
                    
                    
                    //Buscar Detalle de Hora Fin
                    $objDetalleChoferHoraFin = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => $strDetalleHoraFin,
                                                                                    'parent'        => $objDetalleElementoChoferAsignado,
                                                                                    'estado'        => $strEstadoEliminado
                                                                                ) 
                                                                           );
                    if($objDetalleChoferHoraFin)
                    {
                        $objDetalleChoferHoraFin->setDetalleValor($strHoraHastaAsignacion);
                        $objDetalleChoferHoraFin->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferHoraFin);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleHoraFin=true;
                    }
                    
                    
                    
                    //Buscar Detalle de Departamento
                    $objDetalleChoferDepartamento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => $strDetalleDepartamento,
                                                                                    'parent'        => $objDetalleElementoChoferAsignado,
                                                                                    'estado'        => $strEstadoEliminado
                                                                                ) 
                                                                           );
                    if($objDetalleChoferDepartamento)
                    {
                        $objDetalleChoferDepartamento->setDetalleValor($intIdDepartamentoChoferProv);
                        $objDetalleChoferDepartamento->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferDepartamento);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleDepartamento=true;
                    }
                    
                    
                    if($strTipoAsignacionZonaTarea=='ZONA')
                    {
                        //Buscar Detalle de Zona
                        $objDetalleChoferZona = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                        ->findOneBy( array( 
                                                                                        'elementoId'    => $intIdElementoVehiculo,
                                                                                        'detalleNombre' => $strDetalleZona,
                                                                                        'parent'        => $objDetalleElementoChoferAsignado,
                                                                                        'estado'        => $strEstadoEliminado
                                                                                    ) 
                                                                               );
                        if($objDetalleChoferZona)
                        {
                            $objDetalleChoferZona->setDetalleValor($intIdZonaChoferProv);
                            $objDetalleChoferZona->setEstado($strEstadoActivo);
                            $emInfraestructura->persist($objDetalleChoferZona);
                            $emInfraestructura->flush();
                        }
                        else
                        {
                            $boolCrearDetalleZona=true;
                        }
                    }
                    else if($strTipoAsignacionZonaTarea=='TAREA')
                    {
                        //Buscar Detalle de Tarea
                        $objDetalleChoferTarea = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                        ->findOneBy( array( 
                                                                                        'elementoId'    => $intIdElementoVehiculo,
                                                                                        'detalleNombre' => $strDetalleTarea,
                                                                                        'parent'        => $objDetalleElementoChoferAsignado,
                                                                                        'estado'        => $strEstadoEliminado
                                                                                    ) 
                                                                               );
                        if($objDetalleChoferTarea)
                        {
                            $objDetalleChoferTarea->setDetalleValor($intIdTareaChoferProv);
                            $objDetalleChoferTarea->setEstado($strEstadoActivo);
                            $emInfraestructura->persist($objDetalleChoferTarea);
                            $emInfraestructura->flush();
                        }
                        else
                        {
                            $boolCrearDetalleTarea=true;
                        }

                    }
                    
                    //Buscar Detalle de Motivo, ahora es obligatorio para todos los casos
                    $objDetalleChoferMotivo = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy( array( 
                                                                                        'elementoId'    => $intIdElementoVehiculo,
                                                                                        'detalleNombre' => $strDetalleMotivo,
                                                                                        'parent'        => $objDetalleElementoChoferAsignado,
                                                                                        'estado'        => $strEstadoEliminado
                                                                                    ) 
                                                                               );
                    if($objDetalleChoferMotivo)
                    {
                        $objDetalleChoferMotivo->setDetalleValor($intIdMotivo);
                        $objDetalleChoferMotivo->setEstado($strEstadoActivo);
                        $emInfraestructura->persist($objDetalleChoferMotivo);
                        $emInfraestructura->flush();
                    }
                    else
                    {
                        $boolCrearDetalleMotivo=true;
                    }
                }
                else
                {
                    $boolCrearDetalleChofer=true;
                    
                    $boolCrearDetalleFechaInicio=true;
                    $boolCrearDetalleFechaFin=true;
                    $boolCrearDetalleHoraInicio=true;
                    $boolCrearDetalleHoraFin=true;
                    $boolCrearDetalleDepartamento=true;
                    $boolCrearDetalleMotivo=true;
                    
                    if($strTipoAsignacion=="EMPLEADO" || $strTipoAsignacion=="CUADRILLA")
                    {
                        $boolCrearDetalleIdSolPredef=true;
                    }
                    
                    if($strTipoAsignacionZonaTarea=='ZONA')
                    {
                        $boolCrearDetalleZona=true;
                    }
                    else if($strTipoAsignacionZonaTarea=='TAREA')
                    {
                        $boolCrearDetalleTarea=true;
                    }
                    $boolCrearDetalleIdSolProv = true;
                    
                }
                 
                if($boolCrearDetalleChofer)
                {
                    //Crea un detalle Chofer
                    $objDetalleElementoChoferAsignado = new InfoDetalleElemento();
                    $objDetalleElementoChoferAsignado->setElementoId($intIdElementoVehiculo);
                    $objDetalleElementoChoferAsignado->setDetalleNombre($strDetalleChoferProvisional);
                    $objDetalleElementoChoferAsignado->setDetalleValor($intIdPerChofer);
                    $objDetalleElementoChoferAsignado->setDetalleDescripcion($strDetalleChoferProvisional);
                    $objDetalleElementoChoferAsignado->setFeCreacion($datetimeActual);
                    $objDetalleElementoChoferAsignado->setUsrCreacion($strUserSession);
                    $objDetalleElementoChoferAsignado->setIpCreacion($strIpUserSession);
                    $objDetalleElementoChoferAsignado->setEstado($strEstadoActivo);
                    if($strTipoAsignacion=='CUADRILLA')
                    {
                        $objDetalleElementoChoferAsignado->setParent($objDetalleVehiculoCuadrilla);
                    }
                    $emInfraestructura->persist($objDetalleElementoChoferAsignado);
                    $emInfraestructura->flush();
                }
                
                if($boolCrearDetalleFechaInicio)
                {
                    //Fechas
                    $objInfoDetalleFechaInicioAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleFechaInicioAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleFechaInicioAsignacion->setDetalleNombre($strDetalleFechaInicio);
                    $objInfoDetalleFechaInicioAsignacion->setDetalleValor($strFechaDesdeAsignacion);
                    $objInfoDetalleFechaInicioAsignacion->setDetalleDescripcion($strDetalleFechaInicio);
                    $objInfoDetalleFechaInicioAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleFechaInicioAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleFechaInicioAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleFechaInicioAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleFechaInicioAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleFechaInicioAsignacion);
                    $emInfraestructura->flush();
                }
                if($boolCrearDetalleFechaFin)
                {
                    //Fechas
                    $objInfoDetalleFechaFinAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleFechaFinAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleFechaFinAsignacion->setDetalleNombre($strDetalleFechaFin);
                    $objInfoDetalleFechaFinAsignacion->setDetalleValor($strFechaHastaAsignacion);
                    $objInfoDetalleFechaFinAsignacion->setDetalleDescripcion($strDetalleFechaFin);
                    $objInfoDetalleFechaFinAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleFechaFinAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleFechaFinAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleFechaFinAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleFechaFinAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleFechaFinAsignacion);
                    $emInfraestructura->flush();
                }
                
                
                if($boolCrearDetalleHoraInicio)
                {
                    //Horas
                    $objInfoDetalleHoraInicioAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleHoraInicioAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleHoraInicioAsignacion->setDetalleNombre($strDetalleHoraInicio);
                    $objInfoDetalleHoraInicioAsignacion->setDetalleValor($strHoraDesdeAsignacion);
                    $objInfoDetalleHoraInicioAsignacion->setDetalleDescripcion($strDetalleHoraInicio);
                    $objInfoDetalleHoraInicioAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleHoraInicioAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleHoraInicioAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleHoraInicioAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleHoraInicioAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleHoraInicioAsignacion);
                    $emInfraestructura->flush(); 
                }
                
                if($boolCrearDetalleHoraFin)
                {
                    //Horas
                    $objInfoDetalleHoraFinAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleHoraFinAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleHoraFinAsignacion->setDetalleNombre($strDetalleHoraFin);
                    $objInfoDetalleHoraFinAsignacion->setDetalleValor($strHoraHastaAsignacion);
                    $objInfoDetalleHoraFinAsignacion->setDetalleDescripcion($strDetalleHoraFin);
                    $objInfoDetalleHoraFinAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleHoraFinAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleHoraFinAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleHoraFinAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleHoraFinAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleHoraFinAsignacion);
                    $emInfraestructura->flush(); 
                }
                 
                
                if($boolCrearDetalleDepartamento)
                {
                    //Departamento
                    $objInfoDetalleDepartamentoAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleDepartamentoAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleDepartamentoAsignacion->setDetalleNombre($strDetalleDepartamento);
                    $objInfoDetalleDepartamentoAsignacion->setDetalleValor($intIdDepartamentoChoferProv);
                    $objInfoDetalleDepartamentoAsignacion->setDetalleDescripcion($strDetalleDepartamento);
                    $objInfoDetalleDepartamentoAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleDepartamentoAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleDepartamentoAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleDepartamentoAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleDepartamentoAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleDepartamentoAsignacion);
                    $emInfraestructura->flush(); 
                }
                
                if($boolCrearDetalleZona)
                {
                    //tarea
                    $objInfoDetalleZonaAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleZonaAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleZonaAsignacion->setDetalleNombre($strDetalleZona);
                    $objInfoDetalleZonaAsignacion->setDetalleValor($intIdZonaChoferProv);
                    $objInfoDetalleZonaAsignacion->setDetalleDescripcion($strDetalleZona);
                    $objInfoDetalleZonaAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleZonaAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleZonaAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleZonaAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleZonaAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleZonaAsignacion);
                    $emInfraestructura->flush(); 
                }
                
                if($boolCrearDetalleTarea)
                {
                    //tarea
                    $objInfoDetalleTareaAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleTareaAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleTareaAsignacion->setDetalleNombre($strDetalleTarea);
                    $objInfoDetalleTareaAsignacion->setDetalleValor($intIdTareaChoferProv);
                    $objInfoDetalleTareaAsignacion->setDetalleDescripcion($strDetalleTarea);
                    $objInfoDetalleTareaAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleTareaAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleTareaAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleTareaAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleTareaAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleTareaAsignacion);
                    $emInfraestructura->flush(); 
                }
                
                
                if($boolCrearDetalleIdSolPredef)
                {
                    //solicitud predefinida
                    $objInfoDetalleSolicitudPredefinida = new InfoDetalleElemento();
                    $objInfoDetalleSolicitudPredefinida->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleSolicitudPredefinida->setDetalleNombre($strDetalleIdSolPred);
                    $objInfoDetalleSolicitudPredefinida->setDetalleValor($intIdSolicitudChoferPredefinido);
                    $objInfoDetalleSolicitudPredefinida->setDetalleDescripcion($strDetalleIdSolPred);
                    $objInfoDetalleSolicitudPredefinida->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolicitudPredefinida->setUsrCreacion($strUserSession);
                    $objInfoDetalleSolicitudPredefinida->setIpCreacion($strIpUserSession);
                    $objInfoDetalleSolicitudPredefinida->setEstado($strEstadoActivo);
                    $objInfoDetalleSolicitudPredefinida->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleSolicitudPredefinida);
                    $emInfraestructura->flush(); 
                    
                }
                
                
                if($boolCrearDetalleIdSolProv)
                {
                    //solicitud provisional
                    $objInfoDetalleSolicitudProvisional = new InfoDetalleElemento();
                    $objInfoDetalleSolicitudProvisional->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleSolicitudProvisional->setDetalleNombre($strDetalleIdSolProv);
                    $objInfoDetalleSolicitudProvisional->setDetalleValor($objInfoDetalleSolicitud->getId());
                    $objInfoDetalleSolicitudProvisional->setDetalleDescripcion($strDetalleIdSolProv);
                    $objInfoDetalleSolicitudProvisional->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolicitudProvisional->setUsrCreacion($strUserSession);
                    $objInfoDetalleSolicitudProvisional->setIpCreacion($strIpUserSession);
                    $objInfoDetalleSolicitudProvisional->setEstado($strEstadoActivo);
                    $objInfoDetalleSolicitudProvisional->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleSolicitudProvisional);
                    $emInfraestructura->flush(); 
                    
                }
                
                
                if($boolCrearDetalleMotivo)
                {
                    $objInfoDetalleMotivoAsignacion = new InfoDetalleElemento();
                    $objInfoDetalleMotivoAsignacion->setElementoId($intIdElementoVehiculo);
                    $objInfoDetalleMotivoAsignacion->setDetalleNombre($strDetalleMotivo);
                    $objInfoDetalleMotivoAsignacion->setDetalleValor($intIdMotivo);
                    $objInfoDetalleMotivoAsignacion->setDetalleDescripcion($strDetalleMotivo);
                    $objInfoDetalleMotivoAsignacion->setFeCreacion($datetimeActual);
                    $objInfoDetalleMotivoAsignacion->setUsrCreacion($strUserSession);
                    $objInfoDetalleMotivoAsignacion->setIpCreacion($strIpUserSession);
                    $objInfoDetalleMotivoAsignacion->setEstado($strEstadoActivo);
                    $objInfoDetalleMotivoAsignacion->setParent($objDetalleElementoChoferAsignado);
                    $emInfraestructura->persist($objInfoDetalleMotivoAsignacion);
                    $emInfraestructura->flush();
                }
                
                $strFechasYHoras="Fecha Inicio: ".$strFechaDesdeAsignacion."<br/>";
                $strFechasYHoras.="Fecha Fin: ".$strFechaHastaAsignacion."<br/>";
                $strFechasYHoras.="Hora Inicio: ".$strHoraDesdeAsignacion."<br/>";
                $strFechasYHoras.="Hora Fin: ".$strHoraHastaAsignacion."<br/>";
                
                $strInfoChoferAsignacion ="<b>Datos del Chofer</b>";
                $strInfoChoferAsignacion.="Cedula: ".$objPerChoferAsignacion->getPersonaId()->getIdentificacionCliente()."<br/>";
                $strInfoChoferAsignacion.="Nombres: ".$objPerChoferAsignacion->getPersonaId()->getNombres()."<br/>";
                $strInfoChoferAsignacion.="Apellidos: ".$objPerChoferAsignacion->getPersonaId()->getApellidos()."<br/>";
                

                
                $strInfoChoferAReemplazar="";
                if($strTipoAsignacion=='CUADRILLA')
                {
                    
                    if($intIdPerChoferPredefinido)
                    {
                        $objPerChoferCuadrilla = $emInfraestructura->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                    ->find($intIdPerChoferPredefinido);
                        if($objPerChoferCuadrilla)
                        {
                            $strInfoChoferAReemplazar.="<b>Chofer A Reemplazar</b>";
                            $strInfoChoferAReemplazar.="Cedula: ".$objPerChoferCuadrilla->getPersonaId()->getIdentificacionCliente()."<br/>";
                            $strInfoChoferAReemplazar.="Nombres: ".$objPerChoferCuadrilla->getPersonaId()->getNombres()."<br/>";
                            $strInfoChoferAReemplazar.="Apellidos: ".$objPerChoferCuadrilla->getPersonaId()->getApellidos()."<br/>";
                        }
                    }
                    

                    $strMensajeObservacionProvisional.="por la cuadrilla ".$strNombreCuadrilla."<br/>";
                    //Historial Cuadrilla
                    $strMensajeObservacion =$strMensajeObservacionProvisional;
                    $strMensajeObservacion.=$strMensajeObservacionDepartamentoChoferProv.$strMensajeObservacionZonaTareaChoferProv;
                    $strMensajeObservacion.=$strInfoChoferAsignacion.$strFechasYHoras.$strInfoChoferAReemplazar;
                    
                    $objCuadrillaChoferProvisionalHistorial = new AdmiCuadrillaHistorial();
                    $objCuadrillaChoferProvisionalHistorial->setCuadrillaId($objCuadrilla);
                    $objCuadrillaChoferProvisionalHistorial->setEstado($objCuadrilla->getEstado());
                    $objCuadrillaChoferProvisionalHistorial->setFeCreacion($datetimeActual);
                    $objCuadrillaChoferProvisionalHistorial->setUsrCreacion($strUserSession);
                    $objCuadrillaChoferProvisionalHistorial->setObservacion($strMensajeObservacion);
                    $objCuadrillaChoferProvisionalHistorial->setMotivoId($intIdMotivoChoferProvisional);
                    $em->persist($objCuadrillaChoferProvisionalHistorial);
                    $em->flush();
                  
                }
                else if($strTipoAsignacion=="EMPLEADO")
                {
                    if($intIdPerChoferPredefinido)
                    {
                        $objPerChoferPredefinido = $emInfraestructura->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                                     ->find($intIdPerChoferPredefinido);
                        if($objPerChoferPredefinido)
                        {
                            $strInfoChoferAReemplazar.="<b>Chofer A Reemplazar</b>";
                            $strInfoChoferAReemplazar.="Cedula: ".$objPerChoferPredefinido->getPersonaId()->getIdentificacionCliente()."<br/>";
                            $strInfoChoferAReemplazar.="Nombres: ".$objPerChoferPredefinido->getPersonaId()->getNombres()."<br/>";
                            $strInfoChoferAReemplazar.="Apellidos: ".$objPerChoferPredefinido->getPersonaId()->getApellidos()."<br/>";
                        }
                    }
                }
                else
                {
                    $strInfoChoferAReemplazar.="Asignación directa al vehículo";
                }
               
                $strMensajeObservacion =$strMensajeObservacionProvisional;
                $strMensajeObservacion.=$strMensajeObservacionDepartamentoChoferProv.$strMensajeObservacionZonaTareaChoferProv;
                $strMensajeObservacion.=$strInfoChoferAsignacion.$strFechasYHoras.$strInfoChoferAReemplazar;
                $objInfoHistorialChoferProvisional = new InfoHistorialElemento();
                $objInfoHistorialChoferProvisional->setElementoId($objElementoVehiculo);
                $objInfoHistorialChoferProvisional->setObservacion($strMensajeObservacion);
                $objInfoHistorialChoferProvisional->setFeCreacion($datetimeActual);
                $objInfoHistorialChoferProvisional->setUsrCreacion($strUserSession);
                $objInfoHistorialChoferProvisional->setIpCreacion($strIpUserSession);
                $objInfoHistorialChoferProvisional->setEstadoElemento($strEstadoActivo);
                $emInfraestructura->persist($objInfoHistorialChoferProvisional);
                $emInfraestructura->flush();
                
            }
            $strMensaje .= 'OK';
            
            $em->flush();     
            $em->getConnection()->commit();
            
            $emSoporte->flush();     
            $emSoporte->getConnection()->commit();

            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
        }
        catch (Exception $ex) 
        {
            error_log($ex->getMessage());

            $strMensaje .= 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';
            $em->getConnection()->rollback();
            $em->getConnection()->close();
            
            $emSoporte->getConnection()->rollback();
            $emSoporte->getConnection()->close();
            
            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
           
        }
        
        $objResponse->setContent( $strMensaje );
        
        return $objResponse;

    }
    
    /**
     * @Secure(roles="ROLE_328-3537")
     * 
     * Documentación para el método 'eliminarAsignacionChoferAVehiculoProvisionalAction'.
     *
     * Coloca en estado Eliminado la asociación entre un vehículo y un chofer 
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-02-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 26-08-2016 Se realizan modificaciones para que al eliminar la asignación provisional se elimine también el detalle con el id
     *                         de la solicitud predefinida asociada si es que existiera
     */
    public function eliminarAsignacionChoferAVehiculoProvisionalAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $em                 = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $strMensaje         = '';
        $strUserSession     = $objSession->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $datetimeActual     = new \DateTime('now');
        $strEstadoActivo    = 'Activo';
        $strEstadoEliminado = 'Eliminado';
        $strEstadoFinalizado= 'Finalizado';
        $strEstadoPendiente = 'Pendiente';
        $strFechasHorasAsignacionProv='';
        $strNombreCuadrilla                 = $objRequest->get('nombreCuadrilla') ? $objRequest->get('nombreCuadrilla') : '';
        $strMensajeObservacionProvisional   = 'Se elimina asignaci&oacute;n provisional del chofer ';
        $strMotivoElementoProvisional       = 'Se elimina asignacion provisional del chofer';
        $objMotivoChoferProvisional         = $emGeneral->getRepository('schemaBundle:AdmiMotivo')->findOneByNombreMotivo($strMotivoElementoProvisional);
        $intIdMotivoChoferProvisional       = $objMotivoChoferProvisional ? $objMotivoChoferProvisional->getId() : 0;
        
        
        $intIdElementoVehiculo       = $objRequest->get('idElementoVehiculo') ? $objRequest->get('idElementoVehiculo') : '';
        $idPerChoferAsignadoXVehiculo= $objRequest->get('idPerChoferAsignadoXVehiculo') ? trim($objRequest->get('idPerChoferAsignadoXVehiculo')):'';
        $strTipoAsignacion           = $objRequest->get('strTipoAsignacion') ? trim($objRequest->get('strTipoAsignacion')) : '';
        $intIdCuadrilla              = $objRequest->get('intIdCuadrilla')?$objRequest->get('intIdCuadrilla') : '';
        $objElementoVehiculo         = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneBy( array('id' => $intIdElementoVehiculo, 'estado' => $strEstadoActivo) );
        
        $objPerChoferAsignado = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->find( array('id' => $idPerChoferAsignadoXVehiculo, 'estado' => $strEstadoActivo) );
        
        
        if($strTipoAsignacion=='CUADRILLA')
        {
            $objDetalleVehiculoCuadrilla = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'detalleNombre' => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                                    'estado'        => $strEstadoActivo,
                                                                                    'detalleValor'  => $intIdCuadrilla
                                                                                ) 
                                                                           );
            
            $objDetalleElementoChoferAsignado = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy( 
                                                                array( 
                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                    'detalleNombre' => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER,
                                                                    'detalleValor'  => $idPerChoferAsignadoXVehiculo,
                                                                    'estado'        => $strEstadoActivo,
                                                                    'parent'        => $objDetalleVehiculoCuadrilla
                                                                     ) 
                                                                );
        }
        else
        {
            $objDetalleElementoChoferAsignado = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findOneBy( 
                                                                array( 
                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                    'detalleNombre' => self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER,
                                                                    'detalleValor'  => $idPerChoferAsignadoXVehiculo,
                                                                    'estado'        => $strEstadoActivo
                                                                     ) 
                                                                );
        }
        
        $em->getConnection()->beginTransaction();	
        $emInfraestructura->getConnection()->beginTransaction();	
        try
        {
            if($objDetalleElementoChoferAsignado)
            {
               
                $idPerChofer = $objDetalleElementoChoferAsignado->getDetalleValor();
                $objPerChofer= $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerChofer);
                
                $strFechaActualAPChoferEliminarDetalle = $datetimeActual->format('d-M-Y');
                $strFechaActualAPChoferEliminarSolicitud = $datetimeActual->format('d/M/Y');

                if($objPerChofer)
                {
                    $strMensajeObservacionProvisional.= $objPerChofer->getPersonaId()->getNombres()." ";
                    $strMensajeObservacionProvisional.= $objPerChofer->getPersonaId()->getApellidos()."<br/>";
                }
                
                $objDetalleElementoChoferAsignado->setEstado($strEstadoEliminado);
                $emInfraestructura->persist($objDetalleElementoChoferAsignado);
                
                $arrayDetallesAEliminarAsignacionProvisional=array
                                                                    (
                                                                        'Departamento'                      =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_DEPARTAMENTO,
                                                                        'Zona'                              =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_ZONA,
                                                                        'Tarea'                             =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_TAREA,
                                                                        'Fecha Inicio Provisional'          =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO,
                                                                        'Fecha Fin Provisional'             =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN,
                                                                        'Hora Inicio Provisional'           =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO,
                                                                        'Hora Fin Provisional'              =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN,   
                                                                        'Motivo Provisional'                =>
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO,
                                                                        'Id Solicitud Chofer Predefinido'   => 
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                                                        'Id Solicitud Chofer Provisional'   => 
                                                                            self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV
                                                                        
                    
                                                                    );
                
                
                
                foreach($arrayDetallesAEliminarAsignacionProvisional as $detalleNombreAliasProvisional=>$detalleNombreProvisional)
                {
                    $objDetalleAEliminar = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                ->findOneBy( array( 
                                                                                    'elementoId'    => $intIdElementoVehiculo,
                                                                                    'estado'        => $strEstadoActivo, 
                                                                                    'detalleNombre' => $detalleNombreProvisional, 
                                                                                    'parent'        => $objDetalleElementoChoferAsignado ) 
                                                                           );
                    if($objDetalleAEliminar)
                    {
                        
                        if($detalleNombreAliasProvisional=="Fecha Fin Provisional")
                        {
                            $objDetalleAEliminar->setDetalleValor($strFechaActualAPChoferEliminarDetalle);
                        }
                        
                        $strFechasHorasAsignacionProv.= $detalleNombreAliasProvisional.": ".$objDetalleAEliminar->getDetalleValor()."<br/>";
                        $objDetalleAEliminar->setEstado($strEstadoEliminado);
                        $emInfraestructura->persist($objDetalleAEliminar);
                        $emInfraestructura->flush();
                    }
                }
                
                
                
                $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL);
                

                $objDetalleSolicitud = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                    ->findOneBy( array( 
                                                                                    'elementoId'        => $intIdElementoVehiculo,
                                                                                    'tipoSolicitudId'   => $objTipoSolicitud,
                                                                                    'estado'            => $strEstadoPendiente
                                                                                ) 
                                                                           );
                
                if($objDetalleSolicitud)
                {
                    $intIdMotivoEliminacionAPChofer        = $objRequest->get('intIdMotivoEliminacionAPChofer') 
                                                                ? $objRequest->get('intIdMotivoEliminacionAPChofer') : 0;
                    
                    
                    //Se obtiene el detalle solicitud pendiente en el historial
                    $objDetalleSolHistPendienteAPChofer = $em->getRepository('schemaBundle:InfoDetalleSolHist')
                                           ->findOneBy(
                                               array(
                                                   "detalleSolicitudId"=> $objDetalleSolicitud,
                                                   "estado"            => $strEstadoPendiente
                                                   )
                                               );
                    
                    
                    //Se obtiene la fecha de Inicio 
                    $timestampFechaDesdeAPChoferPendiente   = $objDetalleSolHistPendienteAPChofer->getFeIniPlan();
                    
                    $timestampFechaHastaAPChoferPendiente   = $objDetalleSolHistPendienteAPChofer->getFeFinPlan();
                    //$strFechaHastaAPChoferPendiente         = $timestampFechaHastaAPChoferPendiente->format('d/M/Y');
                    $strHoraHastaAPChoferPendiente          = $timestampFechaHastaAPChoferPendiente->format('H:i:s');
                    
                    
                    //$strHoraActualAPChoferEliminar  = $datetimeActual->format('H:i:s');
                    
                    
                    list($dayHastaAPChoferEliminar,$mesHastaAPChoferEliminar,$yearHastaAPChoferEliminar)=explode('/',$strFechaActualAPChoferEliminarSolicitud);
                    list($horaHastaAPChoferEliminar,$minutosHastaAPChoferEliminar)=explode(':',$strHoraHastaAPChoferPendiente);
                    
                    
                    $datetimeHastaAPChoferEliminar  = new \DateTime();
                    $datetimeHastaAPChoferEliminar->setDate($yearHastaAPChoferEliminar, $mesHastaAPChoferEliminar, $dayHastaAPChoferEliminar);
                    $datetimeHastaAPChoferEliminar->setTime($horaHastaAPChoferEliminar, $minutosHastaAPChoferEliminar, '00');

                    
                    //Crear un Info Detalle Solicitud Historial Finalizado
                    $objInfoDetalleSolHist = new InfoDetalleSolHist();
                    $objInfoDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objInfoDetalleSolHist->setEstado($strEstadoFinalizado);
                    $objInfoDetalleSolHist->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolHist->setUsrCreacion($strUserSession);
                    $objInfoDetalleSolHist->setIpCreacion($strIpUserSession);
                    $objInfoDetalleSolHist->setMotivoId($intIdMotivoEliminacionAPChofer);
                    
                    $objInfoDetalleSolHist->setFeIniPlan($timestampFechaDesdeAPChoferPendiente);
                    $objInfoDetalleSolHist->setFeFinPlan($datetimeHastaAPChoferEliminar);
                    
                    $em->persist($objInfoDetalleSolHist);
                    
                    $objDetalleSolicitud->setEstado($strEstadoFinalizado);
                    $em->persist($objInfoDetalleSolHist);
                    
                    
                    
                    
                    
                    //Se busca la zona y se la cambia a estado Finalizado
                    $objCaracteristicaZona = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV);


                    $objDetalleSolCaracteristicaZona = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(
                                                    array(
                                                        "detalleSolicitudId"=> $objDetalleSolicitud,
                                                        "caracteristicaId"  => $objCaracteristicaZona,
                                                        "estado"            => $strEstadoActivo
                                                        )
                                                    );
                    if($objDetalleSolCaracteristicaZona)
                    {
                        $objDetalleSolCaracteristicaZona->setEstado('Finalizada');
                        $em->persist($objDetalleSolCaracteristicaZona);
                        $em->flush();
                    }

                    //Se busca la tarea y se la cambia a estado Finalizado
                    $objCaracteristicaTarea = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV);


                    $objDetalleSolCaracteristicaTarea = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(
                                                    array(
                                                        "detalleSolicitudId"=> $objDetalleSolicitud,
                                                        "caracteristicaId"  => $objCaracteristicaTarea,
                                                        "estado"            => $strEstadoActivo
                                                        )
                                                    );
                    if($objDetalleSolCaracteristicaTarea)
                    {
                        $objDetalleSolCaracteristicaTarea->setEstado('Finalizada');
                        $em->persist($objDetalleSolCaracteristicaTarea);
                        $em->flush();
                    }


                    $objCaracteristicaDepartamento = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV);


                    $objDetalleSolCaracteristicaDepartamento = $em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                ->findOneBy(
                                                    array(
                                                        "detalleSolicitudId"=> $objDetalleSolicitud,
                                                        "caracteristicaId"  => $objCaracteristicaDepartamento,
                                                        "estado"            => $strEstadoActivo
                                                        )
                                                    );
                    if($objDetalleSolCaracteristicaDepartamento)
                    {
                        $objDetalleSolCaracteristicaDepartamento->setEstado('Finalizada');
                        $em->persist($objDetalleSolCaracteristicaDepartamento);
                        $em->flush();
                    }
                    
                    
                }
                
            }

            if($objPerChoferAsignado)
            {
                if($strTipoAsignacion=='CUADRILLA')
                {
                    $strMensajeObservacionProvisional.="por la cuadrilla ".$strNombreCuadrilla."<br/>";
                    $objCuadrilla=$em->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdCuadrilla);
            
                    $objCuadrillaHistorialProvisional = new AdmiCuadrillaHistorial();
                    $objCuadrillaHistorialProvisional->setCuadrillaId($objCuadrilla);
                    $objCuadrillaHistorialProvisional->setEstado($objCuadrilla->getEstado());
                    $objCuadrillaHistorialProvisional->setFeCreacion($datetimeActual);
                    $objCuadrillaHistorialProvisional->setUsrCreacion($strUserSession);
                    $objCuadrillaHistorialProvisional->setObservacion($strMensajeObservacionProvisional.$strFechasHorasAsignacionProv);
                    $objCuadrillaHistorialProvisional->setMotivoId($intIdMotivoChoferProvisional);
                    $em->persist($objCuadrillaHistorialProvisional);
                    $em->flush();
                    
                }
                
                $objInfoHistorialElemento = new InfoHistorialElemento();
                $objInfoHistorialElemento->setElementoId($objElementoVehiculo);
                $objInfoHistorialElemento->setObservacion($strMensajeObservacionProvisional.$strFechasHorasAsignacionProv);
                $objInfoHistorialElemento->setFeCreacion($datetimeActual);
                $objInfoHistorialElemento->setUsrCreacion($strUserSession);
                $objInfoHistorialElemento->setIpCreacion($strIpUserSession);
                $objInfoHistorialElemento->setEstadoElemento($strEstadoActivo);
                $emInfraestructura->persist($objInfoHistorialElemento);
                $emInfraestructura->flush(); 
            }
            
            
            $em->flush();     
            $em->getConnection()->commit();

            $emInfraestructura->flush();
            $emInfraestructura->getConnection()->commit();
            $strMensaje .= 'OK';
        } 
        catch (Exception $ex) 
        {
            error_log($ex->getMessage());

            $strMensaje .= 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';
            
            $em->getConnection()->rollback();
            $em->getConnection()->close();

            $emInfraestructura->getConnection()->rollback();
            $emInfraestructura->getConnection()->close();
        }
        
        $objResponse->setContent( $strMensaje );
        return $objResponse;     
    }
    

    /**
     * @Secure(roles="ROLE_328-3657")
     * 
     * Documentación para el método 'showHistorialAsignacionesXVehiculoAction'.
     *
     * Muestra las consultas que se han realizado de determinado vehículo
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     *
     */
    public function showHistorialAsignacionesXVehiculoAction($id)
    {
        $em                 = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => 'Activo') );
        
        
        $arrayDetalle = array('GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'MOTOR' => '');
        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayDetalle[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        
        
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objMedioTransporte, 'estado' => 'Activo') );
        $strNombreEmpresa = '';

        if( $objInfoEmpresa )
        {
            $strCodEmpresa = $objInfoEmpresa->getEmpresaCod();
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $em->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($strCodEmpresa);
            }
            
            if( $objEmpresa )
            {
                $strNombreEmpresa = $objEmpresa->getNombreEmpresa();
            }
        }
        
        return $this->render('administracionBundle:AsignacionOperativa:showAsignacionesXVehiculo.html.twig', array(
                                                                                            'medioTransporte' => $objMedioTransporte,
                                                                                            'detalles'        => $arrayDetalle,
                                                                                            'empresa'         => $strNombreEmpresa
                                                                                         )
                            );
        
    }
    
    
    /**
     * 
     * Documentación para el método 'showHistorialAsignacionVehicularXVehiculoAction'.
     *
     * Consulta las cuadrillas a las que se le ha asignado un determinado vehículo
     * @return json.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 - Se realizan ajustes para tomar en cuenta los horarios en las asignaciones predefinidas
     *
     */
    public function showHistorialAsignacionVehicularXVehiculoAction()
    {
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $em                 = $this->getDoctrine()->getManager();
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        
        $intIdElemento      = $objRequest->get('idElemento') ? $objRequest->get('idElemento') : 0;
        $strFechaDesde      = $objRequest->get('fechaDesde') ? $objRequest->get('fechaDesde') : '';
        $strFechaHasta      = $objRequest->get('fechaHasta') ? $objRequest->get('fechaHasta') : '';
        $boolErrorFechas    = $objRequest->get('errorFechas') ? $objRequest->get('errorFechas') : 0;
        
                
        $strNombresChoferAV      = $objRequest->get('nombresChoferAV') ? trim($objRequest->get('nombresChoferAV')) : '';
        $strApellidosChoferAV    = $objRequest->get('apellidosChoferAV') ? trim($objRequest->get('apellidosChoferAV')) : '';
        $strIdentificacionChoferAV    = $objRequest->get('identificacionChoferAV') ? trim($objRequest->get('identificacionChoferAV')) : '';
        

        
        $intStart           = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit           = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
        $objTipoSolicitudAsignacionPredefinida = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        $arrayParametros    = array(
                                    "intStart"                  => $intStart,
                                    "intLimit"                  => $intLimit,
                                    "idElemento"                => $intIdElemento,
                                    "strFechaDesde"             => $strFechaDesde,
                                    "strFechaHasta"             => $strFechaHasta,
                                    "errorFechas"               => $boolErrorFechas,
                                    "idTipoSolicitud"           => $objTipoSolicitudAsignacionPredefinida->getId(),
                                    "arrayDetallesVehicular"=> array(
                                                                        'strDetalleSolicitudAsignacionVehicular'    =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                                                        'strDetalleCuadrillaAsignacionVehicular'    =>
                                                                            self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                                                        'strDetalleFechaInicioAsignacionVehicular'  =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                                                        'strDetalleFechaFinAsignacionVehicular'     =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN,
                                                                        'strDetalleHoraInicioAsignacionVehicular'   =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                                                        'strDetalleHoraFinAsignacionVehicular'      =>
                                                                            self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN 
                                                                    ),
                                    "criterios_busqueda"        => array(
                                                                            "strNombresChoferAV"        => $strNombresChoferAV,
                                                                            "strApellidosChoferAV"      => $strApellidosChoferAV,
                                                                            "strIdentificacionChoferAV" => $strIdentificacionChoferAV
                                                                        )
                                    );
        
        $objJson    = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->getJSONHistorialAsignacionVehicularXElemento( $arrayParametros ,$em);
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * 
     * Documentación para el método 'showHistorialAsignacionProvisionalXVehiculoAction'.
     *
     * Consulta las asignaciones provisionales de choferes que se han realizado a un vehículo
     * @return json.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     *
     */
    public function showHistorialAsignacionProvisionalXVehiculoAction()
    {
        $em             = $this->getDoctrine()->getManager();
        $objResponse    = new Response();
        $objRequest     = $this->get('request');

        $boolErrorFechas    = $objRequest->get('errorFechas') ? $objRequest->get('errorFechas') : 0;

        $intIdElemento      = $objRequest->get('idElemento') ? $objRequest->get('idElemento') : 0;

        $strFechaDesde      = $objRequest->get('fechaDesde') ? $objRequest->get('fechaDesde') : '';
        $strFechaHasta      = $objRequest->get('fechaHasta') ? $objRequest->get('fechaHasta') : '';
        
        $strNombresChoferAP      = $objRequest->get('nombresChoferProvisional') ? trim($objRequest->get('nombresChoferProvisional')): '';
        $strApellidosChoferAP    = $objRequest->get('apellidosChoferProvisional') ? trim($objRequest->get('apellidosChoferProvisional')) : '';
        $strIdentificacionChoferAP    = $objRequest->get('identificacionChoferProvisional') ? trim($objRequest->get('identificacionChoferProvisional')) : '';
        

        $intStart = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL);
        $objCaractDepartamentoAPChofer  = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV);
        $objCaractZonaAPChofer          = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV);
        $objCaractTareaAPChofer         = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV);
                
        $arrayParametros    = array(
                                    "intStart"                  => $intStart,
                                    "intLimit"                  => $intLimit,
                                    "idElemento"                => $intIdElemento,
                                    "idTipoSolicitud"           => $objTipoSolicitud->getId(),
                                    "errorFechas"               => $boolErrorFechas,
                                    "strFechaDesde"             => $strFechaDesde,
                                    "strFechaHasta"             => $strFechaHasta,
                                    "strDetalleSolProv"         => self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV,
                                    "strDetalleSolPredProv"     => self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                    "idCaractDepartamentoAPChofer"  => $objCaractDepartamentoAPChofer->getId(),
                                    "idCaractZonaAPChofer"          => $objCaractZonaAPChofer->getId(),
                                    "idCaractTareaAPChofer"         => $objCaractTareaAPChofer->getId(),
                                    "criterios_busqueda"        => array(
                                                                "strNombresChoferAP"        => $strNombresChoferAP,
                                                                "strApellidosChoferAP"      => $strApellidosChoferAP,
                                                                "strIdentificacionChoferAP" => $strIdentificacionChoferAP
                                                            )
                                    );
        
        $objJson    = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->getJSONHistorialAsignacionProvisionalXElemento( $arrayParametros);
        
        
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    /**
     * @Secure(roles="ROLE_328-3658")
     * 
     * Documentación para el método 'showHistorialAsignacionProvisionalChoferAction'.
     *
     * Redirige a la pantalla de consulta de historiales de asignaciones provisionales de chofer
     *
     * @return Response 

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     */
    public function showHistorialAsignacionProvisionalChoferAction()
    {
        $rolesPermitidos=array();
        //MODULO 328 - ASIGNACIONOPERATIVA/exportPDFAsignacionProvisionalChofer
        if(true === $this->get('security.context')->isGranted('ROLE_328-3677'))
        {
            $rolesPermitidos[] = 'ROLE_328-3677';
        }
        return $this->render( 'administracionBundle:AsignacionOperativa:showHistorialAsignacionProvisionalChofer.html.twig', 
                              array(
                                        'rolesPermitidos'        => $rolesPermitidos
                                    )
                            );
    }
    
    /**
     * @Secure(roles="ROLE_328-3658")
     * 
     * Documentación para el método 'gridHistorialAsignacionProvisionalChoferAction'.
     *
     * Consulta las asignaciones provisionales de choferes que se han realizado en un determinado período
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se realizan ajustes en la consulta realizada de las asignaciones provisionales
     *
     */
    public function gridHistorialAsignacionProvisionalChoferAction()
    {
        $em                 = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objResponse        = new Response();
        $objRequest         = $this->get('request');

        $boolErrorFechas    = $objRequest->get('errorFechas') ? $objRequest->get('errorFechas') : 0;

        $strFechaDesde      = $objRequest->get('fechaDesde') ? $objRequest->get('fechaDesde') : '';
        $strFechaHasta      = $objRequest->get('fechaHasta') ? $objRequest->get('fechaHasta') : '';
        
        $strNombresChoferAP      = $objRequest->get('nombresChoferProvisional') ? trim($objRequest->get('nombresChoferProvisional')) : '';
        $strApellidosChoferAP    = $objRequest->get('apellidosChoferProvisional') ? trim($objRequest->get('apellidosChoferProvisional')) : '';
        $strIdentificacionChoferAP    = $objRequest->get('identificacionChoferProvisional') ? trim($objRequest->get('identificacionChoferProvisional')) : '';
        
        $strPlaca    = $objRequest->get('placa') ? trim($objRequest->get('placa')) : '';
        $strDisco    = $objRequest->get('disco') ? trim($objRequest->get('disco')) : '';

        $intStart = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;

        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL);
        $objTipoSolicitudAsignacionPredefinida = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        $objCaracteristicaDepartamentoPredefinido = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
        
        $objCaractDepartamentoAPChofer  = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV);
        $objCaractZonaAPChofer          = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV);
        $objCaractTareaAPChofer         = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV);
            
        $arrayParametros    = array(
                                    "intStart"                          => $intStart,
                                    "intLimit"                          => $intLimit,
                                    "strEstadoAsignacion"               => 'Pendiente',
                                    "idTipoSolicitudPredefinida"        => $objTipoSolicitudAsignacionPredefinida->getId(),
                                    "idCaractDepartamentoPredefinido"   => $objCaracteristicaDepartamentoPredefinido->getId(),
                                    "idTipoSolicitud"                   => $objTipoSolicitud->getId(),
                                    "idCaractDepartamentoAPChofer"      => $objCaractDepartamentoAPChofer->getId(),
                                    "idCaractZonaAPChofer"              => $objCaractZonaAPChofer->getId(),
                                    "idCaractTareaAPChofer"             => $objCaractTareaAPChofer->getId(),
                                    "errorFechas"                       => $boolErrorFechas,
                                    "strFechaDesde"                     => $strFechaDesde,
                                    "strFechaHasta"                     => $strFechaHasta,
                                    "strDetalleSolProv"                 => self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV,
                                    "strDetalleSolPredProv"             => self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                    "criterios_busqueda"                => array(
                                                                                    "placa"                     => $strPlaca,
                                                                                    "disco"                     => $strDisco,
                                                                                    "strNombresChoferAP"        => $strNombresChoferAP,
                                                                                    "strApellidosChoferAP"      => $strApellidosChoferAP,
                                                                                    "strIdentificacionChoferAP" => $strIdentificacionChoferAP
                                                                            )
                                );
        
        $objJson    = $em->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->getJSONHistorialAsignacionProvisionalChofer($arrayParametros,$emInfraestructura,$emGeneral);
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_328-3677")
     * 
     * Documentación para el método 'exportPDFAsignacionProvisionalChoferAction'.
     *
     * Exporta las asignaciones provisionales de choferes que se han realizado en un determinado período.
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se realizan ajustes para que se incluya el número de disco en el PDF
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 10-10-2016  Se modifica la fecha en el nombre del PDF a exportar. La fecha que constará en el nombre será la fecha del filtro 
     *                          Fecha Desde de la búsqueda de asignaciones provisionales realizada  
     */
    public function exportPDFAsignacionProvisionalChoferAction()
    {
        $emComercial        = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objRequest         = $this->get('request');
        
        $strFechaDesde              = $objRequest->get('fechaDesdeAsignacionAPChofer') ? $objRequest->get('fechaDesdeAsignacionAPChofer') : '';
        $strFechaHasta              = $objRequest->get('fechaHastaAsignacionAPChofer') ? $objRequest->get('fechaHastaAsignacionAPChofer') : '';
        
        $strNombresChoferAP         = $objRequest->get('strBuscarXNombresChoferProvisional') ? 
                                      trim($objRequest->get('strBuscarXNombresChoferProvisional')) : '';
        $strApellidosChoferAP       = $objRequest->get('strBuscarXApellidosChoferProvisional') ? 
                                      trim($objRequest->get('strBuscarXApellidosChoferProvisional')) : '';
        $strIdentificacionChoferAP  = $objRequest->get('strBuscarXIdentificacionChoferProvisional') ? 
                                      trim($objRequest->get('strBuscarXIdentificacionChoferProvisional')) : '';
        
        
        $strPlaca                   = $objRequest->get('strBuscarXPlaca') ? trim($objRequest->get('strBuscarXPlaca')) : '';
        $strDisco                   = $objRequest->get('strBuscarXDisco') ? trim($objRequest->get('strBuscarXDisco')) : '';

        $objTipoSolicitud                     = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                  ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_CHOFER_PROVISIONAL);
        $objTipoSolicitudAsignacionPredefinida= $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                                  ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        $objCaractDepartPredefinido     = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
        $objCaractDepartamentoAPChofer  = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_CHOFER_PROV);
        $objCaractZonaAPChofer          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_CHOFER_PROV);
        $objCaractTareaAPChofer         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                      ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_CHOFER_PROV);
            
        $objSession     = $objRequest->getSession();
        $prefijoEmpresa = $objSession->get('prefijoEmpresa');
        $strLogoEmpresa ='';
        if($prefijoEmpresa == 'MD')
        {
            $strLogoEmpresa = 'logo_netlife_big.jpg';
        }
        elseif($prefijoEmpresa == 'TN')
        {
            $strLogoEmpresa = 'logo_telconet.jpg';
        }
        $arrayParametros    = array(
                                    "strEstadoAsignacion"               => 'Pendiente',
                                    "idTipoSolicitud"                   => $objTipoSolicitud->getId(),
                                    "idTipoSolicitudPredefinida"        => $objTipoSolicitudAsignacionPredefinida->getId(),
                                    "idCaractDepartamentoPredefinido"   => $objCaractDepartPredefinido->getId(),
                                    "idCaractDepartamentoAPChofer"      => $objCaractDepartamentoAPChofer->getId(),
                                    "idCaractZonaAPChofer"              => $objCaractZonaAPChofer->getId(),
                                    "idCaractTareaAPChofer"             => $objCaractTareaAPChofer->getId(),
                                    "strFechaDesde"                     => $strFechaDesde,
                                    "strFechaHasta"                     => $strFechaHasta,
                                    "strDetalleSolProv"                 => self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PROV,
                                    "strDetalleSolPredProv"             => self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                    "criterios_busqueda"                => array(
                                                                                    "placa"                     => $strPlaca,
                                                                                    "disco"                     => $strDisco,
                                                                                    "strNombresChoferAP"        => $strNombresChoferAP,
                                                                                    "strApellidosChoferAP"      => $strApellidosChoferAP,
                                                                                    "strIdentificacionChoferAP" => $strIdentificacionChoferAP
                                                                            )
                                );

        $arrayResultadoFinal=$emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                         ->getInfoFinalHistorialAsignacionProvisionalChofer($arrayParametros,$emInfraestructura,$emGeneral);

        $html = $this->renderView('administracionBundle:AsignacionOperativa:showPlantillaPDFHistorialAsignacionProvisionalChofer.html.twig',
                                    array(
                                        'encontrados'=> $arrayResultadoFinal['resultado'],
                                        'total'      => $arrayResultadoFinal['total'],
                                        'fechaDesde' => $strFechaDesde,
                                        'fechaHasta' => $strFechaHasta,
                                        "placa"                     => $strPlaca,
                                        "disco"                     => $strDisco,
                                        "strNombresChoferAP"        => strtoupper($strNombresChoferAP),
                                        "strApellidosChoferAP"      => strtoupper($strApellidosChoferAP),
                                        "strIdentificacionChoferAP" => $strIdentificacionChoferAP,
                                        'logoEmpresa'               => $strLogoEmpresa
                                        ));
        $dateFechaDesde=date_create_from_format("d/m/Y",$strFechaDesde);
        return new Response(
                            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                            200,
                            array(
                                    'Content-Type'          => 'application/pdf',
                                    'Content-Disposition'   => 'attachment; filename=reporteAsignacionProvisionalChofer_'.
                                                               date_format($dateFechaDesde,"Y-m-d").'.pdf',
                            )
            );
    }
    
    /**
     * Documentación para el método 'getDepartamentosAction'.
     *
     * Lista Todos los departamentos
     *
     * @return JsonResponse 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getDepartamentosAction()
    {
        $objResponse     = new JsonResponse();
        $session      = $this->get( 'session' ); 
        $intIdEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 

        $objRequest            = $this->get('request');
        $strNombreDepartamento = $objRequest->query->get('query');

        $emGeneral = $this->getDoctrine()->getManager("telconet_general");  

        $intTotal           = 0;
        $arrayDepartamentos = array();

        $arrayResultados = $emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                     ->getDepartamentosByEmpresaYNombre($intIdEmpresa,$strNombreDepartamento);

        if($arrayResultados)
        {
            foreach($arrayResultados as $arrayDepartamento)
            {
                $item              = array();
                $item['strValue']  = $arrayDepartamento->getId();
                $item['strNombre'] = $arrayDepartamento->getNombreDepartamento();

                $arrayDepartamentos[] = $item;

                $intTotal++;
            }
        }

        $objResponse->setData( array('total' => $intTotal, 'encontrados' => $arrayDepartamentos) );

        return $objResponse;
    }
    
    /**
     * Documentación para el método 'getZonasAction'.
     *
     * Lista todas las zonas
     *
     * @return JsonResponse 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getZonasAction()
    {
        $objResponse    = new JsonResponse();
        $em             = $this->getDoctrine()->getManager("telconet");
        $emGeneral      = $this->getDoctrine()->getManager("telconet_general");
        $objRequest     = $this->get('request');
        $strNombreZona  = $objRequest->query->get('query');
        
        $session        = $objRequest->getSession();
        $idOficina      = $session->get('idOficina') ? $session->get('idOficina') : '';
        $objOficina     = $em->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }
        $arrayParametros=array(
            "nombre"  => $strNombreZona,
            "region"  => $strRegion,
            "estados"   => array(
                'estadoEliminado'   =>'Eliminado',
                'estadoInactivo'    =>'Inactivo'
                )
        );
        $objJson        = $em->getRepository('schemaBundle:AdmiZona')->getJSONZonasByParametros($arrayParametros);   
        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    
    /**
     * Documentación para el método 'getTareasAction'.
     *
     * Lista todas las zonas
     *
     * @return JsonResponse 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function getTareasAction()
    {
        $objResponse    = new JsonResponse();
        
        $objRequest     = $this->get('request');
        $strNombreTarea = $objRequest->query->get('query');

        $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");  

        $intIdEmpresaSession    = $objRequest->getSession()->get('idEmpresa');
        
        $objProcesoTareas       = $emSoporte->getRepository('schemaBundle:AdmiProceso')->findOneByNombreProceso("ACTIVIDADES CUADRILLA");
        
        $arrayParametros    = array(
            "nombre"        => $strNombreTarea,
            "idProceso"     => $objProcesoTareas->getId(),
            "codEmpresa"    => $intIdEmpresaSession,
            "estado"        => "Activo"
            );
        
        
        $objJson            = $emSoporte->getRepository('schemaBundle:AdmiTarea')->generarJson($arrayParametros);
        
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
}
