<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\seguridadBundle\Controller\TokenAuthenticatedController;


/**
 * AsignacionVehicularPredefinida controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de asignaciones vehiculares predefinidad
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 08-04-2016
 */
class AsignacionVehicularPredefinidaController extends Controller implements TokenAuthenticatedController
{
    const ESTADO_ACTIVO                                     = 'Activo';
    const TIPO_ELEMENTO_VEHICULO                            =  'VEHICULO';

    const DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA            = 'SOLICITUD ASIGNACION VEHICULAR PREDEFINIDA';
    const NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA            = 'ZONA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA           = 'TAREA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO    = 'DEPARTAMENTO_PREDEFINIDO_ASIGNACION_VEHICULAR';

    const DETALLE_ASOCIADO_ELEMENTO_VEHICULO        = 'CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO = 'ASIGNACION_VEHICULAR_FECHA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN    = 'ASIGNACION_VEHICULAR_FECHA_FIN_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO  = 'ASIGNACION_VEHICULAR_HORA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_HORA_FIN     = 'ASIGNACION_VEHICULAR_HORA_FIN_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED  = 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA';
        
    
    /**
     * @Secure(roles="ROLE_342-1")
     * 
     * Documentación para el método 'indexAction'.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-10-2016 - Se agrega el permiso respectivo para el gridAction necesario para exportar el listado en PDF
     *
     */
    public function indexAction()
    {
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu         = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("342", "1");
        $arrayRolesPermitidos   = array();

        //MODULO 342 - ASIGNACIONVEHICULARPREDEFINIDA/crearAsignacionVehicularPredefinida
        if(true === $this->get('security.context')->isGranted('ROLE_342-3777'))
        {
           $arrayRolesPermitidos[] = 'ROLE_342-3777';
        }
        //MODULO 342 - ASIGNACIONVEHICULARPREDEFINIDA/eliminarAsignacionVehicularPredefinida
        if(true === $this->get('security.context')->isGranted('ROLE_342-3778'))
        {
           $arrayRolesPermitidos[] = 'ROLE_342-3778';
        }
        //MODULO 342 - ASIGNACIONVEHICULARPREDEFINIDA/editarAsignacionVehicularPredefinida
        if(true === $this->get('security.context')->isGranted('ROLE_342-3797'))
        {
           $arrayRolesPermitidos[] = 'ROLE_342-3797';
        }
        //MODULO 342 - ASIGNACIONVEHICULARPREDEFINIDA/grid
        if(true === $this->get('security.context')->isGranted('ROLE_342-7'))
        {
           $arrayRolesPermitidos[] = 'ROLE_342-7';
        }
        return $this->render( 'administracionBundle:AsignacionVehicularPredefinida:index.html.twig', 
                             array(
                                       'item'                   => $entityItemMenu,
                                       'rolesPermitidos'        => $arrayRolesPermitidos
                                   )
                           );
    }
    
    
    /**
     * @Secure(roles="ROLE_342-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Muestra el listado de todos los medios de transporte creados de acuerdo a la region del usuario con la información
     * de los choferes predefinidos
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 06-10-2016 - Se realizan ajustes para filtrar por horario las asignaciones predefinidas
     */
    public function gridAction()
    {
        $em                         = $this->getDoctrine()->getManager();
        $emInfraestructura          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $objResponse                = new Response();
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $intIdEmpresaSession        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPlaca                   = $objRequest->query->get('placa') ? $objRequest->query->get('placa') : "";
        $strDisco                   = $objRequest->query->get('disco') ? $objRequest->query->get('disco') : "";
        
        $strIdentificacionChoferPredefinido = $objRequest->query->get('identificacionChoferPredefinido') 
                                              ? $objRequest->query->get('identificacionChoferPredefinido') : "";
        
        $strNombresChoferPredefinido        = $objRequest->query->get('nombresChoferPredefinido') 
                                              ? $objRequest->query->get('nombresChoferPredefinido') : "";
        
        $strApellidosChoferPredefinido      = $objRequest->query->get('apellidosChoferPredefinido') 
                                              ? $objRequest->query->get('apellidosChoferPredefinido') : "";
        
        
        $intModeloMedioTransporte           = $objRequest->query->get('modeloMedioTransporte') ? $objRequest->query->get('modeloMedioTransporte') : "";
        $intIdParamDetHorarioAsignacion     = $objRequest->query->get('horarioAsignacionPredefinida') ? 
                                              $objRequest->query->get('horarioAsignacionPredefinida') : "";
        $strHoraInicioAsignacion            = "";
        $strHoraFinAsignacion               = "";
        if($intIdParamDetHorarioAsignacion)
        {
            $objHorario = $emGeneral->getRepository("schemaBundle:AdmiParametroDet")->find($intIdParamDetHorarioAsignacion);
            if($objHorario)
            {
                list($strHoraInicioAsignacion,$strHoraFinAsignacion)    = explode(" - ",$objHorario->getValor1());
            }
        }
        
        $intStart                           = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                           = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $arrayModelosElemento               = $intModeloMedioTransporte ? array( $intModeloMedioTransporte ) : array(); 
        
        
        $objTipoSolicitud                           = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                         ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

        $objCaracteristicaZonaPredefinida           = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

        $objCaracteristicaTareaPredefinida          = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);

        $objCaracteristicaDepartamentoPredefinido   = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);

        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $em->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }  
        $arrayParametros = array(
                                    'intStart'                                  => $intStart,
                                    'intLimit'                                  => $intLimit,
                                    'intEmpresa'                                => $intIdEmpresaSession,
                                    'tipoElemento'                              => 'VEHICULO',
                                    'strEstadoActivo'                           => self::ESTADO_ACTIVO,
                                    'intIdTipoSolicitud'                        => $objTipoSolicitud->getId(),
                                    'intIdCaracteristicaZonaPredefinida'        => $objCaracteristicaZonaPredefinida->getId(),
                                    'intIdCaracteristicaTareaPredefinida'       => $objCaracteristicaTareaPredefinida->getId(),
                                    'intIdCaracteristicaDepartamentoPredefinido'=> $objCaracteristicaDepartamentoPredefinido->getId(),
                                    'strDetalleDisco'                           => 'DISCO',
                                    'strHoraInicioAsignacion'                   => $strHoraInicioAsignacion,
                                    'strHoraFinAsignacion'                      => $strHoraFinAsignacion,
                                    'criterios'                                 => array(   'placa'             => $strPlaca,
                                                                                            'modeloElemento'    => $arrayModelosElemento,
                                                                                            'detallesElemento'  => array(
                                                                                                                    'disco'     => $strDisco,
                                                                                                                    'region'    => $strRegion
                                                                                                                    )
                                                                                        ),
                                    'criteriosChoferPredefinido'                => array(   
                                                                        'strIdentificacionChoferPredefinido'=> $strIdentificacionChoferPredefinido,
                                                                        'strNombresChoferPredefinido'       => $strNombresChoferPredefinido,
                                                                        'strApellidosChoferPredefinido'     => $strApellidosChoferPredefinido
                                                                                        )
                                );
        $objJson        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->getJSONAsignacionVehicularPredefinidaByCriterios( $arrayParametros ,$em);
        $objResponse->setContent($objJson);
        return $objResponse;
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
    
    
    /**
     * 
     * Documentación para el método 'gridChoferesPredefinidosDisponiblesAction'.
     *
     * Muestra el listado de todos los choferes con su respectiva información: identificación, nombres y apellidos 
     * que aún no han sido asignados de manera predefinida a un vehículo .
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2016 Se realizan las modificaciones para obtener los choferes disponibles de acuerdo al horario seleccionado
     * 
     */ 
    public function gridChoferesPredefinidosDisponiblesAction()
    {
        $em                     = $this->get('doctrine')->getManager('telconet');
        $objRequest             = $this->getRequest();
        $objResponse            = new Response();
        
        $boolErrorHoras                         = $objRequest->get('errorHoras') ? $objRequest->get('errorHoras') : 0;
        $strHoraDesdeAsignacionPredefinida      = $objRequest->get('strHoraDesdeAsignacionPredefinida') ? 
                                                  trim($objRequest->get('strHoraDesdeAsignacionPredefinida')) : '';
        $strHoraHastaAsignacionPredefinida      = $objRequest->get('strHoraHastaAsignacionPredefinida') ? 
                                                  trim($objRequest->get('strHoraHastaAsignacionPredefinida')) : '';


        $strIdentificacionChoferDisponible  = $objRequest->get("identificacionChoferDisponible") ? 
                                              $objRequest->get("identificacionChoferDisponible") : '';
        $strNombresChoferDisponible         = $objRequest->get("nombresChoferDisponible") ? $objRequest->get("nombresChoferDisponible") : '';
        $strApellidosChoferDisponible       = $objRequest->get("apellidosChoferDisponible") ? $objRequest->get("apellidosChoferDisponible") : '';

        $intLimit                       = $objRequest->get("limit");
        $intStart                       = $objRequest->get("start");

        $intIdEmpresaSession            = $objRequest->getSession()->get('idEmpresa');


        $strEstadoEliminado             = 'Eliminado';
        $strDescripcionCaracteristica   = 'CARGO';
        $strDescripcionRol              = 'Chofer';
        
        
        $objTipoSolicitud = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        $intIdTipoSolicitud=$objTipoSolicitud->getId();
        
        

        $arrayParametros = array(
                                    'intLimit'                                      => $intLimit,
                                    'intStart'                                      => $intStart,
                                    'strEstadoActivo'                               => 'Activo',
                                    'intEmpresa'                                    => $intIdEmpresaSession,
                                    'strDescripcionCaracteristica'                  => $strDescripcionCaracteristica,
                                    'strEstadoEliminado'                            => $strEstadoEliminado,
                                    'strDescripcionRol'                             => $strDescripcionRol,
                                    'intTipoSolicitud'                              => $intIdTipoSolicitud,
                                    'boolErrorHoras'                                => $boolErrorHoras,
                                    'strHoraDesdeAsignacionPredefinida'             => $strHoraDesdeAsignacionPredefinida,
                                    'strHoraHastaAsignacionPredefinida'             => $strHoraHastaAsignacionPredefinida,
                                    'criterios_chofer'                              => 
                                                                    array(
                                                                        'identificacionChoferDisponible'    => $strIdentificacionChoferDisponible,
                                                                        'nombresChoferDisponible'           => $strNombresChoferDisponible,
                                                                        'apellidosChoferDisponible'         => $strApellidosChoferDisponible
                                                                    )                      
                                );
        $objJson    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getJSONChoferesPredefinidosDisponibles( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_342-3777")
     * 
     * Documentación para el método 'crearAsignacionPredefinida'.
     *
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2016 Se realizan ajustes para obtener el horario de asignacion
     * 
     */ 
    public function crearAsignacionVehicularPredefinidaAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        $em                 = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $strEstadoActivo    = 'Activo';
        
        $intIdElementoVehiculo  = $objRequest->get('idElementoVehiculo') ? $objRequest->get('idElementoVehiculo') : '';
        $objElementoVehiculo    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneBy( array('id' => $intIdElementoVehiculo, 'estado' => $strEstadoActivo) );
        
        $strTipoAsignacion                  = $objRequest->get('strTipoAsignacion') ? $objRequest->get('strTipoAsignacion') : '';
        
        $intIdZonaPredefinida               = $objRequest->get('idZonaPredefinida') ? $objRequest->get('idZonaPredefinida') : '';
        $intIdTareaPredefinida              = $objRequest->get('idTareaPredefinida') ? $objRequest->get('idTareaPredefinida') : '';
        
        
        $strHoraDesdeAsignacionPredefinida  = $objRequest->get('strHoraDesdeAsignacionPredefinida') ? 
                                                trim($objRequest->get('strHoraDesdeAsignacionPredefinida')) : '';
        $strHoraHastaAsignacionPredefinida  = $objRequest->get('strHoraHastaAsignacionPredefinida') ? 
                                                trim($objRequest->get('strHoraHastaAsignacionPredefinida')) : '';
        
        $intIdDepartamentoPredefinido       = $objRequest->get('idDepartamentoPredefinido') ? $objRequest->get('idDepartamentoPredefinido') : '';
            
        $intIdPerChoferPredefinido          = $objRequest->get('idPerChoferPredefinido') ? $objRequest->get('idPerChoferPredefinido') : '';
        $objPerChoferPredefinido            = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerChoferPredefinido);
        $objPersonaChoferPredefinido        = $objPerChoferPredefinido->getPersonaId();
        
        $strMsg     ='';
        if($objElementoVehiculo && $objPerChoferPredefinido && $objPersonaChoferPredefinido)
        {
            $arrayParametros= array(
                                    "intIdElementoVehiculo"             => $intIdElementoVehiculo,
                                    "strTipoAsignacion"                 => $strTipoAsignacion,
                                    "intIdZonaPredefinida"              => $intIdZonaPredefinida,
                                    "intIdTareaPredefinida"             => $intIdTareaPredefinida,
                                    "intIdDepartamentoPredefinido"      => $intIdDepartamentoPredefinido,
                                    "intIdPerChoferPredefinido"         => $intIdPerChoferPredefinido,
                                    "strHoraDesdeAsignacionPredefinida" => $strHoraDesdeAsignacionPredefinida,
                                    "strHoraHastaAsignacionPredefinida" => $strHoraHastaAsignacionPredefinida,
                                    "boolActualizarDetallesSolVehiculos"=> false,
                                    "idSolicitudPredefinidaAnterior"    => 0
                                );
            /* @var $serviceAVPredefinida \telconet\administracionBundle\Service\AsignacionVehicularPredefinidaService */
            $serviceAVPredefinida   = $this->get('administracion.AsignacionVehicularPredefinida');
            $strMsg                 = $serviceAVPredefinida->crearAsignacionVehicularPredefinida($arrayParametros);
        }
        else
        {
            $strMsg.="No existe el elemento o el chofer que quiere asociar";
        }
        $objResponse->setContent( $strMsg );
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'verificarVehiculosAsignacionPredefinidaAction'.
     *
     * Obtiene las cuadrillas que se encuentran asignadas a un vehículo
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 24-08-2016 Se procede a enviar el id de la solicitud predefinida que se encuentra asociada
     * 
     */ 
    public function verificarCuadrillasConAsignacionVehicularPredefinidaAction()
    {
        $em                 = $this->getDoctrine()->getManager();
        $objRequest         = $this->get('request');
        $objResponse        = new Response();
        $strMsg             = '';

        $strEstadoActivo    = 'Activo';
        
        $strMensajeCuadrillas   = "";
        
        $intIdDetalleSolicitud  = $objRequest->get('idDetalleSolicitud') ? $objRequest->get('idDetalleSolicitud') : '';
        $intIdElementoVehiculo  = $objRequest->get('idElementoVehiculo') ? $objRequest->get('idElementoVehiculo') : '';
        $arrayParametros    =   array( 
                                    'estadoActivo'          => $strEstadoActivo,
                                    'detalleCuadrilla'      => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                    'detalleFechaInicio'    => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                    'detalleHoraInicio'     => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                    'detalleHoraFin'        => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN,
                                    'detalleSolicitud'      => self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                    'elementoId'            => $intIdElementoVehiculo,
                                    'idDetalleSolicitud'    => $intIdDetalleSolicitud
                                );
        
        
        $objetosCuadrillasConVehiculo = $em->getRepository('schemaBundle:AdmiCuadrilla')
                                            ->getResultadoCuadrillasXVehiculoAsignado($arrayParametros);


        if($objetosCuadrillasConVehiculo)
        {
            foreach($objetosCuadrillasConVehiculo as $cuadrilla)
            {
                $strMensajeCuadrillas.= "<b>Nombre: </b>".$cuadrilla["nombreCuadrilla"];
                $strMensajeCuadrillas.= "<br/>Vehículo asignado desde ".$cuadrilla["fechaInicioAsignacionVehicular"];
                $strMensajeCuadrillas.= " en horario de ".$cuadrilla["horaInicioAsignacionVehicular"];
                $strMensajeCuadrillas.= " a ".$cuadrilla["horaFinAsignacionVehicular"]."<br/><br/>";
            }
            $strMsg = $strMensajeCuadrillas;
        }
        else
        {
            $strMsg = "OK";
        }

        $objResponse->setContent( $strMsg );
        return $objResponse;

    }
    
    /**
     * @Secure(roles="ROLE_342-3797")
     * Documentación para el método 'editarAsignacionVehicularPredefinidaAction'.
     *
     * Edita el chofer que se encuentra asignado actualmente en la asignación vehicular predefinida
     * 
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-08-2016  Se obtiene el valor del id de la solicitud predefinida a editar para proceder a eliminar sus características
     *                          por éste y no por el id del elemento
     * 
     */ 
    public function editarAsignacionVehicularPredefinidaAction()
    {
        $objRequest             = $this->get('request');
        $objResponse            = new Response();
        
        $intIdDetalleSolicitud  = $objRequest->get('idDetalleSolicitud') ? $objRequest->get('idDetalleSolicitud') : '';
        $intIdMotivoCambioDeChoferPredefinido  = $objRequest->get('idMotivoCambioDeChoferPredefinido') 
                                                    ? $objRequest->get('idMotivoCambioDeChoferPredefinido') : '';
        
        
        /* @var $serviceAVPredefinida \telconet\administracionBundle\Service\AsignacionVehicularPredefinida */
        $serviceAVPredefinida   = $this->get('administracion.AsignacionVehicularPredefinida');
        $strMsgEliminacion      = $serviceAVPredefinida->eliminarAsignacionVehicularPredefinida(
                                                                $intIdDetalleSolicitud,
                                                                $intIdMotivoCambioDeChoferPredefinido);
        $strMsgCreacion         ='';
        
        if($strMsgEliminacion=="OK")
        {
            $objResponse        = new Response();
            $objRequest         = $this->get('request');
            $em                 = $this->getDoctrine()->getManager();
            $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');

            $strEstadoActivo    = 'Activo';

            $intIdElementoVehiculo  = $objRequest->get('idElementoVehiculo') ? $objRequest->get('idElementoVehiculo') : '';
            $objElementoVehiculo    = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->findOneBy( array('id' => $intIdElementoVehiculo, 'estado' => $strEstadoActivo) );

            $strTipoAsignacion                  = $objRequest->get('strTipoAsignacion') ? $objRequest->get('strTipoAsignacion') : '';

            $intIdZonaPredefinida               = $objRequest->get('idZonaPredefinida') ? $objRequest->get('idZonaPredefinida') : '';
            $intIdTareaPredefinida              = $objRequest->get('idTareaPredefinida') ? $objRequest->get('idTareaPredefinida') : '';

            $intIdDepartamentoPredefinido       = $objRequest->get('idDepartamentoPredefinido') ? $objRequest->get('idDepartamentoPredefinido') : '';

            $intIdPerChoferPredefinidoNuevo     = $objRequest->get('idPerChoferPredefinidoNuevo') ? $objRequest->get('idPerChoferPredefinidoNuevo') 
                                                  : '';
            
            $objPerChoferPredefinidoNuevo       = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerChoferPredefinidoNuevo);
            $objPersonaChoferPredefinidoNuevo   = $objPerChoferPredefinidoNuevo->getPersonaId();
            
            $strHoraDesdeAsignacionPredefinida  = $objRequest->get('strHoraInicioPredefinido') ? $objRequest->get('strHoraInicioPredefinido') : '';
            $strHoraHastaAsignacionPredefinida  = $objRequest->get('strHoraFinPredefinido') ? $objRequest->get('strHoraFinPredefinido') : '';
            
            if($objElementoVehiculo && $objPerChoferPredefinidoNuevo && $objPersonaChoferPredefinidoNuevo)
            {
                $arrayParametros= array(
                                        "intIdElementoVehiculo"             => $intIdElementoVehiculo,
                                        "strTipoAsignacion"                 => $strTipoAsignacion,
                                        "intIdZonaPredefinida"              => $intIdZonaPredefinida,
                                        "intIdTareaPredefinida"             => $intIdTareaPredefinida,
                                        "intIdDepartamentoPredefinido"      => $intIdDepartamentoPredefinido,
                                        "intIdPerChoferPredefinido"         => $intIdPerChoferPredefinidoNuevo,
                                        "strHoraDesdeAsignacionPredefinida" => $strHoraDesdeAsignacionPredefinida,
                                        "strHoraHastaAsignacionPredefinida" => $strHoraHastaAsignacionPredefinida,
                                        "boolActualizarDetallesSolVehiculos"=> true,
                                        "idSolicitudPredefinidaAnterior"    => $intIdDetalleSolicitud
                );
                /* @var $serviceAVPredefinida \telconet\administracionBundle\Service\AsignacionVehicularPredefinidaService */
                $serviceAVPredefinida   = $this->get('administracion.AsignacionVehicularPredefinida');
                $strMsgCreacion         = $serviceAVPredefinida->crearAsignacionVehicularPredefinida($arrayParametros);
            }
            else
            {
                $strMsgCreacion.="No existe el elemento o el chofer que quiere asociar";
            }
        }
        $strMsg = "";
        if($strMsgEliminacion=="OK" && $strMsgCreacion=="OK")
        {
            $strMsg="OK";
        }
        else
        {
            $strMsg="ERROR";
        }
        
        $objResponse->setContent( $strMsg );
        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_342-3778")
     * Documentación para el método 'eliminarAsignacionVehicularPredefinidaAction'.
     *
     * Elimina la asignación vehicular predefinida que tenga un vehículo
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 09-04-2016
     * 
     */ 
    public function eliminarAsignacionVehicularPredefinidaAction()
    {
        $objRequest             = $this->get('request');
        $objResponse            = new Response();
        
        $intIdDetalleSolicitud  = $objRequest->get('idDetalleSolicitud') ? $objRequest->get('idDetalleSolicitud') : '';
        
        $intIdMotivoEliminarAsignacionPredefinida  = $objRequest->get('idMotivoEliminacionAVPredefinida') 
                                                    ? $objRequest->get('idMotivoEliminacionAVPredefinida') : '';
        
        /* @var $serviceAVPredefinida \telconet\administracionBundle\Service\AsignacionVehicularPredefinida */
        $serviceAVPredefinida   = $this->get('administracion.AsignacionVehicularPredefinida');
        $strMsg                 = $serviceAVPredefinida->eliminarAsignacionVehicularPredefinida(
                                                                        $intIdDetalleSolicitud,
                                                                        $intIdMotivoEliminarAsignacionPredefinida);
        $objResponse->setContent( $strMsg );
        return $objResponse;
        
    }
    
    
    
    /**
     * 
     * Documentación para el método 'motivosAction'.
     *
     * Muestra todos los motivos por los cuales se puede cambiar el chofer predefinido
     *
     * @return Response 

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     *
     */ 
    public function getMotivosAVPredefinidaAction()
    {
        $objResponse    = new Response();
        $objRequest     = $this->get('request');
        $em             = $this->getDoctrine()->getManager('telconet');

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
     * Documentación para el método 'validarHorarioAsignacionPredefinidaAction'.
     *
     * Validar que las asignaciones predefinidas por horario no se traslapen con otros horarios realizados al mismo vehículo
     *
     * @return Response 

     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 23-08-2016
     */ 
    public function validarHorarioAsignacionPredefinidaAction()
    {
        $objResponse                        = new Response();
        $objRequest                         = $this->get('request');
        $emComercial                        = $this->getDoctrine()->getManager('telconet');
        $idElemento                         = $objRequest->request->get('idElemento') ? $objRequest->request->get('idElemento') : '';
        $strHoraDesdeAsignacionPredefinida  = $objRequest->request->get('strHoraDesdeAsignacionPredefinida') ? 
                                              $objRequest->request->get('strHoraDesdeAsignacionPredefinida') : '';
        
        $strHoraHastaAsignacionPredefinida  = $objRequest->request->get('strHoraHastaAsignacionPredefinida') ? 
                                              $objRequest->request->get('strHoraHastaAsignacionPredefinida') : '';
        $objTipoSolicitud                   = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        if($objTipoSolicitud)
        {
            $arrayParametros = array(
                                        "idElemento"                        => $idElemento,
                                        "strHoraDesdeAsignacionPredefinida" => $strHoraDesdeAsignacionPredefinida,
                                        "strHoraHastaAsignacionPredefinida" => $strHoraHastaAsignacionPredefinida,
                                        "idTipoSolicitud"                   => $objTipoSolicitud->getId()
                                );

            $objAsignacionesPredefinidasHorariosSolapados = $emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                                        ->getAsignacionesPredefinidasHorariosSolapados($arrayParametros);

            $strMensaje     = '';

            if($objAsignacionesPredefinidasHorariosSolapados)
            {
                $strMensaje.="Actualmente existe una Asignación Predefinida de Chofer cuyo horario se traslapa con el que usted desea ingresar <br>"
                            .'<b>Horario que desea ingresar</b>: De '. $strHoraDesdeAsignacionPredefinida.' a '.$strHoraHastaAsignacionPredefinida.'<br>'
                            ."<br><b>Asignación Predefinida de Chofer Existente</b><br>"
                            .'Chofer: '.$objAsignacionesPredefinidasHorariosSolapados[0]['apellidosChoferPredefinido']." "
                            .$objAsignacionesPredefinidasHorariosSolapados[0]['nombresChoferPredefinido'].'<br>'
                            .'Horario: De '. $objAsignacionesPredefinidasHorariosSolapados[0]['horaDesdeAsignacionPredefinida'].' a '
                            . $objAsignacionesPredefinidasHorariosSolapados[0]['horaHastaAsignacionPredefinida'].'<br>'
                            ."<br>Si desea realizar ésta asignación, por favor cambie el horario";
            }
            else
            {
                $strMensaje = 'OK';
            }
        }
        else
        {
            $strMensaje = "No existe el tipo de solicitud predefinida. Por favor informe a Sistemas.";
        }
        $objResponse->setContent( $strMensaje );
        
        return $objResponse;
        
    }
    
    /**
     * 
     * Documentación para el método 'getHorariosAsignacionPredefinidaAction.'
     *
     * Obtiene los horarios de las asignaciones predefinidas.
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-10-2016
     *
     */
    public function getHorariosAsignacionPredefinidaAction()
    {
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');

        $objJson        = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getJSONDetallesParametroGeneral("HORARIOS_ASIGNACION_PREDEFINIDA","","");
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_342-7")
     * Documentación para el método 'exportPDFAsignacionPredefinidaAction.'
     *
     * Obtiene las asignaciones predefinidas de chofer en formato PDF.
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-10-2016
     *
     */
    public function exportPDFAsignacionPredefinidaAction()
    {
        $emComercial                = $this->getDoctrine()->getManager();
        $emInfraestructura          = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral                  = $this->getDoctrine()->getManager("telconet_general");
        $objRequest                 = $this->get('request');
        $objSession                 = $objRequest->getSession();
        $intIdEmpresaSession        = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPlaca                   = $objRequest->get('strPlaca') ? $objRequest->get('strPlaca') : "";
        $strDisco                   = $objRequest->get('strNumDisco') ? $objRequest->get('strNumDisco') : "";

        $strPrefijoEmpresa          = $objSession->get('prefijoEmpresa');
        
        $strPathTelcos              = $this->container->getParameter('path_telcos');
        $strLogoEmpresa             = '';
        if($strPrefijoEmpresa == 'TN')
        {
            $strLogoEmpresa = $strPathTelcos.'/telcos/web/public/images/logo_telconet.jpg';
        }
        else if($strPrefijoEmpresa == 'MD')
        {
            $strLogoEmpresa = $strPathTelcos.'/telcos/web/public/images/logo_netlife_big.jpg';
        }
        else if($strPrefijoEmpresa == 'TTCO')
        {
            $strLogoEmpresa = $strPathTelcos.'/telcos/web/public/images/logo_transtelco_new.jpg';
        }
        
        $strIdentificacionChoferPredefinido = $objRequest->get('strBusqIdentificacionChoferPredefinido') 
                                              ? $objRequest->get('strBusqIdentificacionChoferPredefinido') : "";
        
        $strNombresChoferPredefinido        = $objRequest->get('strBusqNombresChoferPredefinido') 
                                              ? $objRequest->get('strBusqNombresChoferPredefinido') : "";
        
        $strApellidosChoferPredefinido      = $objRequest->get('strBusqApellidosChoferPredefinido') 
                                              ? $objRequest->get('strBusqApellidosChoferPredefinido') : "";
        
        
        $intModeloMedioTransporte           = $objRequest->get('idModeloMedioTransporte') ? 
                                              $objRequest->get('idModeloMedioTransporte') : "";
        $intIdParamDetHorarioAsignacion     = $objRequest->get('idHorarioPredefinido') ? 
                                              $objRequest->get('idHorarioPredefinido') : "";
        $strHoraInicioAsignacion            = "";
        $strHoraFinAsignacion               = "";
        $strHorarioAsignacion               = "";
        if($intIdParamDetHorarioAsignacion)
        {
            $objHorario = $emGeneral->getRepository("schemaBundle:AdmiParametroDet")->find($intIdParamDetHorarioAsignacion);
            if($objHorario)
            {
                $strHorarioAsignacion                                   = $objHorario->getValor1();
                list($strHoraInicioAsignacion,$strHoraFinAsignacion)    = explode(" - ",$strHorarioAsignacion);
            }
        }
        
        $arrayModelosElemento               = array();
        $strNombreModeloElemento            = "";
        if($intModeloMedioTransporte)
        {
            $arrayModelosElemento[]         = $intModeloMedioTransporte;
            $objModeloElemento              = $emInfraestructura->getRepository("schemaBundle:AdmiModeloElemento")->find($intModeloMedioTransporte);
            if($objModeloElemento)
            {
                $strNombreModeloElemento    = $objModeloElemento->getNombreModeloElemento();
            }
        }
        
        $objTipoSolicitud                   = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                          ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

        $objCaractZonaPredefinida           = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

        $objCaractTareaPredefinida          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);

        $objCaractDepartPredefinido         = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);

        
        $strRegion          = '';
        $inIdOficina        = $objSession->get('idOficina') ? $objSession->get('idOficina') : 0;
        if($inIdOficina)
        {
            $objOficina     = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($inIdOficina);
            if($objOficina)
            {
                $objCanton  = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
                $strRegion  = $objCanton ? $objCanton->getRegion() : '';
            } 
        }
        
        $arrayParametros    = array(
                                    'intEmpresa'                                => $intIdEmpresaSession,
                                    'tipoElemento'                              => 'VEHICULO',
                                    'strEstadoActivo'                           => self::ESTADO_ACTIVO,
                                    'intIdTipoSolicitud'                        => $objTipoSolicitud->getId(),
                                    'intIdCaracteristicaZonaPredefinida'        => $objCaractZonaPredefinida ? 
                                                                                   $objCaractZonaPredefinida->getId() : 0,
                                    'intIdCaracteristicaTareaPredefinida'       => $objCaractTareaPredefinida ? 
                                                                                   $objCaractTareaPredefinida->getId() : 0,
                                    'intIdCaracteristicaDepartamentoPredefinido'=> $objCaractDepartPredefinido ? 
                                                                                   $objCaractDepartPredefinido->getId() : 0,
                                    'strDetalleDisco'                           => 'DISCO',
                                    'strHoraInicioAsignacion'                   => $strHoraInicioAsignacion,
                                    'strHoraFinAsignacion'                      => $strHoraFinAsignacion,
                                    'criterios'                                 => array(   'placa'             => $strPlaca,
                                                                                            'modeloElemento'    => $arrayModelosElemento,
                                                                                            'detallesElemento'  => array(
                                                                                                                    'disco'     => $strDisco,
                                                                                                                    'region'    => $strRegion
                                                                                                                    )
                                                                                        ),
                                    'criteriosChoferPredefinido'                => array('strIdentificacionChoferPredefinido'    => 
                                                                                         $strIdentificacionChoferPredefinido,
                                                                                         'strNombresChoferPredefinido'           => 
                                                                                         $strNombresChoferPredefinido,
                                                                                         'strApellidosChoferPredefinido'         => 
                                                                                         $strApellidosChoferPredefinido
                                                                                        )
                                );
        $arrayResultadoFinal= $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->getResultadoAsignacionVehicularPredefinidaByCriterios( $arrayParametros ,$emComercial);
        
        $html = $this->renderView('administracionBundle:AsignacionVehicularPredefinida:showPlantillaPDFAsignacionPredefinidaChofer.html.twig',
                                    array(
                                            'encontrados'                           => $arrayResultadoFinal['resultado'],
                                            'total'                                 => $arrayResultadoFinal['total'],
                                            'horario'                               => $strHorarioAsignacion,
                                            "modelo"                                => $strNombreModeloElemento,
                                            "placa"                                 => $strPlaca,
                                            "disco"                                 => $strDisco,
                                            "strNombresChoferAPredefinida"          => strtoupper($strNombresChoferPredefinido),
                                            "strApellidosChoferAPredefinida"        => strtoupper($strApellidosChoferPredefinido),
                                            "strIdentificacionChoferAPredefinida"   => $strIdentificacionChoferPredefinido,
                                            'logoEmpresa'                           => $strLogoEmpresa
                                        ));
        
        return new Response(
                            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                            200,
                            array(
                                    'Content-Type'          => 'application/pdf',
                                    'Content-Disposition'   => 'attachment; filename=reporteAsignacionPredefinidaChofer_'.trim(date("Y-m-d")).'.pdf',
                            )
        );
    }

}