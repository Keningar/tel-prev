<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use telconet\schemaBundle\Entity\AdmiCuadrillaHistorial;

use Symfony\Component\HttpFoundation\Response; 
use JMS\SecurityExtraBundle\Annotation\Secure;

use Symfony\Component\HttpFoundation\JsonResponse;
use telconet\schemaBundle\Entity\InfoDetalleElemento;
use telconet\schemaBundle\Entity\InfoHistorialElemento;

/**
 * AsignacionVehicular controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Cuadrillas
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 13-10-2015
 */
class AsignacionVehicularController extends Controller
{ 
    const CARACTERISTICA_PRESTAMO_CUADRILLA     = 'PRESTAMO CUADRILLA';
    const CARACTERISTICA_PRESTAMO_EMPLEADO      = 'PRESTAMO EMPLEADO';
    const DETALLE_ASOCIADO_ELEMENTO_VEHICULO    = 'CUADRILLA';

    const DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED  = 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO = 'ASIGNACION_VEHICULAR_FECHA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN    = 'ASIGNACION_VEHICULAR_FECHA_FIN_CUADRILLA';

    const DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO	= 'ASIGNACION_VEHICULAR_HORA_INICIO_CUADRILLA';
    const DETALLE_ASIGNACION_VEHICULAR_HORA_FIN		= 'ASIGNACION_VEHICULAR_HORA_FIN_CUADRILLA'; 

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER             = 'ASIGNACION_PROVISIONAL_CHOFER';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO	= 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_INICIO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN	= 'ASIGNACION_PROVISIONAL_CHOFER_FECHA_FIN';

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO     = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_INICIO';
    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN        = 'ASIGNACION_PROVISIONAL_CHOFER_HORA_FIN'; 

    const DETALLE_ASIGNACION_PROVISIONAL_CHOFER_MOTIVO	= 'ASIGNACION_PROVISIONAL_CHOFER_MOTIVO';
    
    
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO= 'DEPARTAMENTO_PREDEFINIDO_ASIGNACION_VEHICULAR';
    
    const DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA                    ='SOLICITUD ASIGNACION VEHICULAR PREDEFINIDA';
    
    /**
     * @Secure(roles="ROLE_340-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     */
    public function indexAction()
    {
        $emSeguridad   = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu  = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("340", "1");
        $rolesPermitidos = array();

        
        //MODULO 340 - asignacionVehicular/showHistorialAsignacionVehicularXCuadrilla
        if(true === $this->get('security.context')->isGranted('ROLE_340-3617'))
        {
            $rolesPermitidos[] = 'ROLE_340-3617';
        }
        
        
        return $this->render( 'administracionBundle:AsignacionVehicular:index.html.twig', 
                                array(
                                        'item'                   => $entityItemMenu,
                                        'rolesPermitidos'        => $rolesPermitidos
                                    )
                            );
    }

    /**
     * @Secure(roles="ROLE_340-7")
     * 
     * Documentación para el método 'gridAction'.
     * Muestra todas las cuadrillas con su respectivo vehículo asignado.
     *
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 Se envía parámetro con la descripción de la característica del id de la solicitud predefinida
     */
    public function gridAction()
    {
        $objResponse        = new Response();
        $objRequest         = $this->get('request');
        $em                 = $this->getDoctrine()->getManager('telconet');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $strEstadoActivo    = 'Activo';
        $objSession         = $objRequest->getSession();
        $intIdEmpresaSession= $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        

        $strNombre          = $objRequest->query->get('nombre') ? $objRequest->query->get('nombre') : "";
        $strEstado          = $objRequest->query->get('estado') ? $objRequest->query->get('estado') : '';
        $strDepartamento    = $objRequest->query->get('departamento') ? $objRequest->query->get('departamento') : "";
        
        $strNombresChofer           = $objRequest->query->get('nombresChofer') ? $objRequest->query->get('nombresChofer') : "";
        $strApellidosChofer         = $objRequest->query->get('apellidosChofer') ? $objRequest->query->get('apellidosChofer') : '';
        $strIdentificacionChofer    = $objRequest->query->get('identificacionChofer') ? $objRequest->query->get('identificacionChofer') : "";

        $intStart = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        
       $objTipoSolicitudAsignacionPredefinida = $em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                                    ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);
        
        $objCaracteristicaDepartamentoPredefinido = $em->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
        
        

        $arrayParametros = array(
                                    'intStart'                  => $intStart,
                                    'intLimit'                  => $intLimit,
                                    'idEmpresa'                 => $intIdEmpresaSession,
                                    'strEstadoActivo'           => $strEstadoActivo,
                                    'strDetalleCuadrilla'       => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                    'strDetalleSolAsignacionVehicular'          => self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                    'intIdTipoSolicitudAsignacionPredefinida'   => $objTipoSolicitudAsignacionPredefinida->getId(),
                                    'intIdCaractDepartamentoPredefinido'        => $objCaracteristicaDepartamentoPredefinido->getId(),
                                    'arrayDetallesFechasYHoras' => array(
                                                                            'strFechaInicioAV'  => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                                                            'strFechaFinAV'     => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN,

                                                                            'strHoraInicioAV'   => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                                                            'strHoraFinAV'     => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN,

                                                                        ),
                                    'criterios'                 => array( 
                                                                            'nombre'        => $strNombre,
                                                                            'estado'        => $strEstado,
                                                                            'departamento'  => $strDepartamento,
                                                                            'nombresChofer' => $strNombresChofer,
                                                                            'apellidosChofer' => $strApellidosChofer,
                                                                            'identificacionChofer' => $strIdentificacionChofer
                                                                        )
                                );
        $objJson = $em->getRepository('schemaBundle:AdmiCuadrilla')
                        ->getJSONCuadrillasAsignacionVehicular($arrayParametros,$emInfraestructura,$emGeneral,$emSoporte);
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * Documentación para el método 'departamentosAction'.
     *
     * Departamentos correspondientes a los empleados
     *
     * @return JsonResponse 
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0  
     */
    public function departamentosAction()
    {
        $objResponse     = new JsonResponse();
        $session      = $this->get( 'session' ); 
        $intIdEmpresa = ($session->get('idEmpresa') ? $session->get('idEmpresa') : ""); 

        $objRequest            = $this->get('request');
        $strNombreDepartamento = $objRequest->query->get('query');

        $emSoporte = $this->getDoctrine()->getManager("telconet_general");  

        $intTotal           = 0;
        $arrayDepartamentos = array();

        $arrayResultados = $emSoporte->getRepository('schemaBundle:AdmiDepartamento')
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
     * @Secure(roles="ROLE_340-3617")
     * 
     * Documentación para el método 'showHistorialAsignacionVehicularXCuadrillaAction'.
     *
     * Método que muestra todos los vehículos que se han asignado a determinada cuadrilla
     * 
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0
     */
    public function showHistorialAsignacionVehicularXCuadrillaAction()
    {
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objResponse        = new Response();
        $objRequest         = $this->get('request');

        $intIdCuadrilla     = $objRequest->get('idCuadrilla') ? $objRequest->get('idCuadrilla') : '';
        $strFechaDesde      = $objRequest->get('strFechaDesdeAsignacion') ? $objRequest->get('strFechaDesdeAsignacion') : '';
        $strFechaHasta      = $objRequest->get('strFechaHastaAsignacion') ? $objRequest->get('strFechaHastaAsignacion') : '';
        $intStart           = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit           = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $boolErrorFechas    = $objRequest->get('errorFechas') ? $objRequest->get('errorFechas') : 0;
        
        $arrayParametros    = array(
                                    "intStart"                      => $intStart,
                                    "intLimit"                      => $intLimit,
                                    "strFechaDesde"                 => $strFechaDesde,
                                    "strFechaHasta"                 => $strFechaHasta,
                                    "errorFechas"                   => $boolErrorFechas,
                                    "idCuadrilla"                   => $intIdCuadrilla,

                                    "strDetalleNombreCuadrilla"     => self::DETALLE_ASOCIADO_ELEMENTO_VEHICULO,
                                    "strDetalleNombreFechaInicio"   => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_INICIO,
                                    "strDetalleNombreFechaFin"      => self::DETALLE_ASIGNACION_VEHICULAR_FECHA_FIN,
                                    "strDetalleNombreHoraInicio"    => self::DETALLE_ASIGNACION_VEHICULAR_HORA_INICIO,
                                    "strDetalleNombreHoraFin"       => self::DETALLE_ASIGNACION_VEHICULAR_HORA_FIN
                                    );
        
        $objJson    = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->getJSONHistorialAsignacionVehicularXCuadrilla( $arrayParametros );
        $objResponse->setContent($objJson);
        return $objResponse;
    }


}