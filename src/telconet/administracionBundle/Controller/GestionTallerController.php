<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
namespace telconet\administracionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use JMS\SecurityExtraBundle\Annotation\Secure;
use telconet\schemaBundle\Entity\InfoMantenimientoElemento;
use telconet\schemaBundle\Entity\InfoMantenimientoElementoDet;


/**
 * GestionTaller controller.
 *
 * Controlador que se encargará de administrar las funcionalidades respecto a la opción de Gestión de Taller(Órdenes de trabajo y Mantenimientos)
 *
 * @author Lizbeth Cruz <mlcruz@telconet.ec>
 * @version 1.0 10-07-2016
 */
class GestionTallerController extends Controller
{ 
    const TIPO_ELEMENTO_VEHICULO				= 'VEHICULO';
    const ESTADO_ACTIVO					= 'Activo';
    const ESTADO_PENDIENTE					= 'Pendiente';
    const PROCESO_CATEGORIAS_TAREAS				= 'CATEGORIAS TAREAS OT TALLER Y MOVILIZACION';
    const TIPOS_MANTENIMIENTOS				= 'TIPOS MANTENIMIENTOS TALLER Y MOVILIZACION';
    const DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA            = 'SOLICITUD ASIGNACION VEHICULAR PREDEFINIDA';
    const NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA            = 'ZONA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA           = 'TAREA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO    = 'DEPARTAMENTO_PREDEFINIDO_ASIGNACION_VEHICULAR';


    const NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT   	=  'TIPO_MANTENIMIENTO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT   	= 'ID_PLAN_MANTENIMIENTO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT        	= 'ID_MANTENIMIENTO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_CASO_OT                 	= 'ID_CASO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_KM_ACTUAL_OT            	= 'KM_ACTUAL_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_TIPO_ASIGNADO_OT        	= 'TIPO_ASIGNADO_ORDEN_TRABAJO_VEHICULO'; 
    const NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT         	= 'ID_PER_ASIGNADO_ORDEN_TRABAJO_VEHICULO'; 
    const NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT      	= 'ID_PER_CHOFER_PREDEFINIDO_ORDEN_TRABAJO_VEHICULO'; 
    const NOMBRE_CARACTERISTICA_VER_NUMERACION_OT      	= 'VER_NUMERACION_ORDEN_TRABAJO_VEHICULO';

    /**
     * @Secure(roles="ROLE_337-1")
     * 
     * Documentación para el método 'indexAction'.
     *
     * Redirección a la pantalla principal de la administracion de la Gestión de Taller
     * @return render.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 11-07-2016
     *
     */
    public function indexAction()
    {
        $arrayRolesPermitidos   = array();
        $emSeguridad            = $this->getDoctrine()->getManager('telconet_seguridad');
        $entityItemMenu         = $emSeguridad->getRepository('schemaBundle:SeguRelacionSistema')->searchItemMenuByModulo("337", "1");

        //MODULO 337 - GestionTaller/grid
        if(true === $this->get('security.context')->isGranted('ROLE_337-7'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-7';
        }
        //MODULO 337 - GestionTaller/newOrdenTrabajoVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_337-3557'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-3557';
        }
        //MODULO 337 - GestionTaller/showOrdenTrabajoVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_337-3558'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-3558';
        }
        //MODULO 337 - GESTIONTALLER/createOrdenTrabajoVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_337-3559'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-3559';
        }
        //MODULO 337 - GestionTaller/exportOrdenTrabajoVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_337-3560'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-3560';
        }
        //MODULO 337 - GestionTaller/gridOrdenesTrabajoVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_337-3562'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-3562';
        }
        //MODULO 337 - GestionTaller/showMantenimientosOrdenesTrabajoVehiculo
        if(true === $this->get('security.context')->isGranted('ROLE_337-4397'))
        {
            $arrayRolesPermitidos[] = 'ROLE_337-4397';
        }

        return $this->render('administracionBundle:GestionTaller:index.html.twig', array(
                                                                                            'item'              => $entityItemMenu,
                                                                                            'rolesPermitidos'   => $arrayRolesPermitidos
        ));
    }
 
    /**
     * @Secure(roles="ROLE_337-7")
     * 
     * Documentación para el método 'gridAction'.
     *
     * Genera el grid de los vehículos
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-07-2016
     *
     */
    public function gridAction()
    {
        $emComercial              = $this->getDoctrine()->getManager('telconet');
        $emGeneral                = $this->getDoctrine()->getManager('telconet_general');
        $jsonResponse             = new JsonResponse();
        $objRequest               = $this->get('request');
        $objSession               = $objRequest->getSession();
        $intIdEmpresaSession      = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPlaca                 = $objRequest->query->get('placa') ? $objRequest->query->get('placa') : "";
        $strChasis                = $objRequest->query->get('chasis') ? $objRequest->query->get('chasis') : "";
        $strMotor                 = $objRequest->query->get('motor') ? $objRequest->query->get('motor') : "";
        $strDisco                 = $objRequest->query->get('disco') ? $objRequest->query->get('disco') : "";
        $intModeloMedioTransporte = $objRequest->query->get('modeloMedioTransporte') ? $objRequest->query->get('modeloMedioTransporte') : "";
        $intStart                 = $objRequest->query->get('start') ? $objRequest->query->get('start') : 0;
        $intLimit                 = $objRequest->query->get('limit') ? $objRequest->query->get('limit') : 0;
        $idOficina                = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina               = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);

        $strRegion      = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $strRegion      = $objCanton ? $objCanton->getRegion() : '';
        }
        $serviceInfoElemento      = $this->get('tecnico.InfoElemento');
        $arrayTiposElemento       = array( self::TIPO_ELEMENTO_VEHICULO );
        $arrayModelosElemento     = $intModeloMedioTransporte ? array( $intModeloMedioTransporte ) : array(); 
        
        
        $arrayParametros = array(
                                    'intStart'             => $intStart,
                                    'intLimit'             => $intLimit,
                                    'intEmpresa'           => $intIdEmpresaSession,
                                    'strEstadoActivo'      => self::ESTADO_ACTIVO,
                                    'strCategoriaElemento' => 'transporte',
                                    'criterios'            => array( 'nombre'           => $strPlaca,
                                                                     'tipoElemento'     => $arrayTiposElemento,
                                                                     'modeloElemento'   => $arrayModelosElemento,
                                                                     'detallesElemento' => array(
                                                                                                    'chasis' => $strChasis,
                                                                                                    'motor'  => $strMotor,
                                                                                                    'disco'  => $strDisco,
                                                                                                    'region' => $strRegion
                                                                                           )
                                                                     )
                                );
        
        $arrayResultados = $serviceInfoElemento->getListadoElementosByCriterios($arrayParametros);
        
        $jsonResponse->setData( $arrayResultados );
        
        return $jsonResponse;
    }
    
    
    /**
     * 
     * Documentación para el método 'getPlanesMantenimientoAction'.
     *
     * Obtiene los planes de mantenimiento.
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-07-2016
     *
     */    
    public function getPlanesMantenimientoAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest                 = $this->get('request');
        $nombrePlanMantenimiento    = $objRequest->get('query');

        $session    = $objRequest->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $parametros = array();
        $parametros["esPlanMantenimiento"]  = 'S';
        
        $start = $objRequest->get('start');
        $limit = $objRequest->get('limit');

        $objJson = $this->getDoctrine()->getManager("telconet_soporte")
                    ->getRepository('schemaBundle:AdmiProceso')
                    ->generarJson($parametros, $nombrePlanMantenimiento, "Activo", $start, $limit,$codEmpresa);

        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    
    /**
     * 
     * Documentación para el método 'getMantenimientosByIdPlanMantenimientoAction'.
     * 
     * Permite eliminar un mantenimiento asociado a un vehículo.
     * 
     * @param integer $id // id del plan de mantenimiento o proceso padre
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-07-2016
     * 
     */ 
    public function getMantenimientosByIdPlanMantenimientoAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest             = $this->get('request');
        $nombreMantenimiento    = $objRequest->get('query');
        
        $idPlanMantenimiento    = $objRequest->get('idPlanMantenimiento');

        $session    = $objRequest->getSession();
        $codEmpresa = $session->get('idEmpresa');

        $parametros = array();
        $parametros["procesoPadreId"]  = $idPlanMantenimiento;
        
        $start = $objRequest->get('start');
        $limit = $objRequest->get('limit');

        $objJson = $this->getDoctrine()->getManager("telconet_soporte")
            ->getRepository('schemaBundle:AdmiProceso')
            ->generarJson($parametros, $nombreMantenimiento, "Activo", $start, $limit,$codEmpresa);

        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    
    /**
     * 
     * Documentación para el método 'getPerAutorizacionOrdenTrabajoTransporteAction'.
     *
     * Obtiene los jefes que pueden autorizar una orden de trabajo de taller
     * 
     * @param integer $idCaso
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-07-2016
     *
     */
    public function getPerAutorizacionOrdenTrabajoTransporteAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();
        $idDepartamento         = $objSession->get('idDepartamento');
        $idEmpresa              = $objSession->get('idEmpresa');
        $strNombresPersona      = $objRequest->get('query');
        
        $arrayParametros = array();
        $arrayParametros["idDepartamento"]      = $idDepartamento;
        $arrayParametros["idEmpresa"]           = $idEmpresa;
        $arrayParametros["estado"]              = "Activo";
        $arrayParametros["nombreApellidoPer"]   = $strNombresPersona;
        $arrayParametros["esJefe"]              = "S";
        
        
        $objJson = $this->getDoctrine()->getManager("telconet")->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                       ->getJSONPersonaEmpresaRolPorCriterios($arrayParametros);        
        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    /**
     * 
     * Documentación para el método 'getTareasMantenimientosOCasoTransporteAction'.
     *
     * Obtiene las tareas con su respectiva información que están asociadas a un caso de un transporte.
     * 
     * @param integer $idCaso
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-07-2016
     *
     */
    public function getTareasMantenimientosOCasoTransporteAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest         = $this->get('request');

        $strNombreTarea     = $objRequest->query->get('nombreTarea') ? $objRequest->query->get('nombreTarea') : "";
        $strEstadoTarea     = $objRequest->query->get('estadoTarea') ? $objRequest->query->get('estadoTarea') : "";

        $idCaso             = $objRequest->query->get('idCaso') ? $objRequest->query->get('idCaso') : 0;
        $idMantenimiento    = $objRequest->query->get('idMantenimiento') ? $objRequest->query->get('idMantenimiento') : 0;
        $tipoMantenimiento  = $objRequest->query->get('tipoMantenimiento') ? $objRequest->query->get('tipoMantenimiento') :"";
        
        
        $arrayParametrosTareas = array();
        $arrayParametrosTareas["nombreTarea"]       = $strNombreTarea;
        $arrayParametrosTareas["estadoTarea"]       = $strEstadoTarea;
        if($tipoMantenimiento=="PREVENTIVO")
        {
            
            $arrayParametrosTareas["idMantenimiento"]   = $idMantenimiento;
            $objJson = $this->getDoctrine()->getManager("telconet_soporte")
                                            ->getRepository('schemaBundle:InfoMantenimientoTarea')
                                            ->getJSONTareasyCategoriasByCriterios($arrayParametrosTareas); 
            
        }
        else if($tipoMantenimiento=="CORRECTIVO")
        {
            $arrayParametrosTareas["idCaso"]    = $idCaso;
            $objJson                            = $this->getDoctrine()->getManager("telconet_soporte")
                                                        ->getRepository('schemaBundle:InfoDetalle')
                                                        ->generarJsonDetallesTareasTNXParametros($arrayParametrosTareas);	
        }

        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    
    
    /**
     * 
     * Documentación para el método 'getTareasCasosMovilizacionTransporteAction'.
     *
     * Obtiene todas las tareas con su respectiva información que están asociadas a un caso de un transporte.
     * 
     * @param integer $idCaso
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-07-2016
     *
     */
    public function getTareasCasosMovilizacionTransporteAction()
    {
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');

        $objRequest = $this->get('request');
        
        $parametroProceso = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                      ->get("PROCESOS TIPO CASO MOVILIZACION", "", "", "","", "", "", "");

        
        $idProceso                              = $parametroProceso[0]['valor1'];

        $strNombreTarea                         = $objRequest->query->get('nombreTarea');
        
        
        $arrayParametrosTareas = array();
        $arrayParametrosTareas["nombre"]        = $strNombreTarea;
        $arrayParametrosTareas["idProceso"]     = $idProceso;
        $arrayParametrosTareas["estado"]        = "Activo";
        $objJson                                = $emSoporte->getRepository('schemaBundle:AdmiTarea')->generarJson($arrayParametrosTareas);        
        $objResponse->setContent($objJson);

        return $objResponse;
    }

    /**
     * @Secure(roles="ROLE_337-3561")
     * 
     * Documentación para el método 'getCategoriasTareasOTyMantenimientosTransporteAction.'
     *
     * Obtiene las categorías de las tareas que se mostrarán en la orden de trabajo y en los mantenimientos.
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 13-07-2016
     *
     */
    public function getCategoriasTareasOTyMantenimientosTransporteAction()
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');

        $objJson    = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                ->getJSONDetallesParametroGeneral(self::PROCESO_CATEGORIAS_TAREAS,"","S");
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    /**
     * 
     * Documentación para el método 'getCasosXTransporteAction'
     *
     * Obtiene los casos de un transporte.
     * @param integer $id // id del transporte
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-08-2016
     *
     */
    public function getCasosXTransporteAction($id)
    {
        $objResponse = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $emSoporte  = $this->getDoctrine()->getManager('telconet_soporte');

        $objRequest         = $this->get('request');
        $numeroCaso         = $objRequest->get('query');
        $arrayParametros    = array("idElemento"=>$id,"numeroCaso"=>$numeroCaso);
        $objJson            = $emSoporte->getRepository('schemaBundle:InfoCaso')->getJSONCasosXAfectado($arrayParametros);
        $objResponse->setContent($objJson);
        return $objResponse;
    }
    
    
   
    /**
     * 
     * Documentación para el método 'getChoferesAction'.
     *
     * Obtiene el listado de los choferes
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 17-08-2016
     *
     */
    public function getChoferesAction()
    {
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $emGeneral      = $this->getDoctrine()->getManager('telconet_general');
        $objRequest     = $this->getRequest();
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $identificacion = $objRequest->get("identificacionChoferOrdenTrabajo") ? $objRequest->get("identificacionChoferOrdenTrabajo") : '';
        $nombres        = $objRequest->get("nombresChoferOrdenTrabajo") ? $objRequest->get("nombresChoferOrdenTrabajo") : '';
        $apellidos      = $objRequest->get("apellidosChoferOrdenTrabajo") ? $objRequest->get("apellidosChoferOrdenTrabajo") : '';
        $limit          = $objRequest->get("limit");
        $start          = $objRequest->get("start");
        $page           = $objRequest->get("page");
        
        $objSession     = $objRequest->getSession();
        $idDepartamento = $objSession->get('idDepartamento');
        $idEmpresa      = $objSession->get('idEmpresa');
        
        $idOficina      = $objSession->get('idOficina') ? $objSession->get('idOficina') : '';
        $objOficina     = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($idOficina);
        $region         = '';
        if($objOficina)
        {
            $objCanton      = $emGeneral->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());
            $region         = $objCanton ? $objCanton->getRegion() : '';
        }
        

        $arrayParametros = array(
                                    'idEmpresa'                 => $idEmpresa,
                                    'nombresPersona'            => $nombres,
                                    'apellidosPersona'          => $apellidos,
                                    'identificacionPersona'     => $identificacion,
                                    'limit'                     => $limit,
                                    'page'                      => $page,
                                    'start'                     => $start,
                                    'estado'                    => 'Activo',
                                    'strDescripcionRol'         => 'Chofer',
                                    'strDescripcionTipoRol'     => 'Empleado',
                                    'idDepartamento'            => $idDepartamento,
                                    'region'                    => $region
        );

        $objJson= $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->getJSONPersonaEmpresaRolPorCriterios($arrayParametros);
        $objResponse->setContent($objJson);
        return $objResponse;
    }

    
    
   
    /**
     * @Secure(roles="ROLE_337-3557")
     * 
     * Documentación para el método 'newOrdenTrabajoTransporteAction'.
     *
     * Muestra toda la información del vehículo al que se le desea generar la orden de trabajo
     * con la información del chofer predefinido asignado
     * las tareas asociadas al caso de un respectivo vehículo, y se debe elegir la categoría de cada tarea 
     * que aparecerá en la orden de trabajo que se va a generar.
     * 
     * @param integer $id id de elemento
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 10-07-2016
     *
     */
    public function newOrdenTrabajoTransporteAction($id)
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        
        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        

        

        $arrayDetalle = array('GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'MOTOR' => '','TIPO_VEHICULO' => '','REGION'=>'');

        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayDetalle[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $entityContrato             = array();
        $entityPlanMantenimiento    = array();
        if($arrayDetalle['TIPO_VEHICULO']=='EMPRESA')
        {
            $idPlanMantenimiento                    = $arrayDetalle['PLAN_MANTENIMIENTO'];
            $entityPlanMantenimiento                = $emComercial->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
        }
        else if($arrayDetalle['TIPO_VEHICULO']=='SUBCONTRATADO')
        {
            if($arrayDetalle['CONTRATO'])
            {
                $idContrato        = $arrayDetalle['CONTRATO'];
                $entityContrato    = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
            }      
        }
        
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objMedioTransporte, 'estado' => self::ESTADO_ACTIVO) );
        $strNombreEmpresa = '';

        if( $objInfoEmpresa )
        {
            $strCodEmpresa = $objInfoEmpresa->getEmpresaCod();
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($strCodEmpresa);
            }
            
            if( $objEmpresa )
            {
                $strNombreEmpresa = $objEmpresa->getNombreEmpresa();
            }
        }
        
        
        $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

        $objCaracteristicaZonaPredefinida = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

        $objCaracteristicaTareaPredefinida = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);

        $objCaracteristicaDepartamentoPredefinido = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);

        $arrayParametros = array(
                                    'intEmpresa'                                => $intIdEmpresa,
                                    'tipoElemento'                              => 'VEHICULO',
                                    'strEstadoActivo'                           => self::ESTADO_ACTIVO,
                                    'intIdTipoSolicitud'                        => $objTipoSolicitud->getId(),
                                    'intIdCaracteristicaZonaPredefinida'        => $objCaracteristicaZonaPredefinida->getId(),
                                    'intIdCaracteristicaTareaPredefinida'       => $objCaracteristicaTareaPredefinida->getId(),
                                    'intIdCaracteristicaDepartamentoPredefinido'=> $objCaracteristicaDepartamentoPredefinido->getId(),
                                    'criterios'                                 => array(   
                                                                                            'idElemento'    => $id
                                                                                        )
                                );
        
        
        $arrayResultado     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->getResultadoAsignacionVehicularPredefinidaByCriterios($arrayParametros,$emComercial);
        $resultado          = $arrayResultado['resultado'];
        $intTotal           = $arrayResultado['total'];
        
        $idPerChoferPredefinido='';
        $idPersonaChoferPredefinido='';
        $strNombreApellidoChoferPredefinido='N/A';
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                if($data['idPerChoferPredefinido'])
                {
                    $idPerChoferPredefinido                 = $data['idPerChoferPredefinido'];
                    $idPersonaChoferPredefinido             = $data['idPersonaChoferPredefinido'];
                    $strNombreApellidoChoferPredefinido     = $data['nombresChoferPredefinido'] ." ".$data['apellidosChoferPredefinido'];
                }
            }
        }
        
        $arrayDetalle["idPerChoferPredefinido"]             = $idPerChoferPredefinido;
        $arrayDetalle["idPersonaChoferPredefinido"]         = $idPersonaChoferPredefinido;
        $arrayDetalle["strNombreApellidoChoferPredefinido"] = $strNombreApellidoChoferPredefinido;
            

        return $this->render('administracionBundle:GestionTaller:newOrdenTrabajoTransporte.html.twig',
                                array(
                                        'medioTransporte'   => $objMedioTransporte,
                                        'detalles'          => $arrayDetalle,
                                        'empresa'           => $strNombreEmpresa,
                                        'contrato'          => $entityContrato,
                                        'planMantenimiento' => $entityPlanMantenimiento
                            ));       
    }

    /**
     * @Secure(roles="ROLE_337-3559")
     * 
     * Documentación para el método 'createOrdenTrabajoTransporteAction'.
     * 
     * Crear un nuevo registro para el mantenimiento de un vehículo.
     * 
     * @param integer $id // id del vehículo
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-07-2016
     * 
     */
    public function createOrdenTrabajoTransporteAction($id)
    {
        $objRequest             = $this->get('request');
        $objSession             = $objRequest->getSession();

        $idPlanMantenimiento    = $objRequest->get('escogido_plan_mantenimiento_id') ? $objRequest->get('escogido_plan_mantenimiento_id') : 0;
        $idMantenimiento        = $objRequest->get('escogido_mantenimiento_id') ? $objRequest->get('escogido_mantenimiento_id') : 0;
        $idCasoMantenimiento    = $objRequest->get('escogido_caso_mantenimiento_id') ? $objRequest->get('escogido_caso_mantenimiento_id') : 0;
        $asignadoA              = $objRequest->get('escogido_tipo_asignado') ? $objRequest->get('escogido_tipo_asignado') : "";
        $kmActual               = $objRequest->get('kmActual') ? $objRequest->get('kmActual') : "";
        $idPerChoferPredefinido = $objRequest->get('idPerChoferPredefinido') ? $objRequest->get('idPerChoferPredefinido') : 0;
        $idPerContratista       = $objRequest->get('escogido_contratista_id') ? $objRequest->get('escogido_contratista_id') : 0;
        $idPerAutorizadoPor     = $objRequest->get('idPerAutorizadoPor') ? $objRequest->get('idPerAutorizadoPor') : 0;
        $fechaInicio            = $objRequest->get("fechaInicio") ? $objRequest->get('fechaInicio') : "";
        $fechaFin               = $objRequest->get("fechaFin") ? $objRequest->get('fechaFin') : "";
        $observacionOT          = $objRequest->get('strObservacion') ? $objRequest->get('strObservacion') : "";
        $tipoMantenimiento      = $objRequest->get('escogido_tipo_mantenimiento') ? $objRequest->get('escogido_tipo_mantenimiento') : "";
        $OTConNumeracionActual  = $objRequest->get('OT_numeracion_actual') ? $objRequest->get('OT_numeracion_actual') : "";
        
        $strUserSession         = $objSession->get('user')? $objSession->get('user') : "";
        $intIdEmpresa           = $objSession->get('idEmpresa')? $objSession->get('idEmpresa') : 0;
        $strIpUserSession       = $objRequest->getClientIp() ? $objRequest->getClientIp() : "";
        $oficina                = $objSession->get('idOficina')? $objSession->get('idOficina') : 0;
        $strPrefijoEmpresa      = $objSession->get('prefijoEmpresa')? $objSession->get('prefijoEmpresa') : "";

        $jsonTareasyCategoriasOrdenTrabajo = $objRequest->get('json_tareas_y_categorias_orden_trabajo') ? 
                                             $objRequest->get('json_tareas_y_categorias_orden_trabajo') : '';
        
        $arrayParametros=array(
                                "idPlanMantenimiento"               => $idPlanMantenimiento,
                                "idMantenimiento"                   => $idMantenimiento,
                                "asignadoA"                         => $asignadoA,
                                "kmActual"                          => $kmActual,
                                "idPerChoferPredefinido"            => $idPerChoferPredefinido,
                                "idPerContratista"                  => $idPerContratista,
                                "idPerAutorizadoPor"                => $idPerAutorizadoPor,
                                "fechaInicio"                       => $fechaInicio,
                                "fechaFin"                          => $fechaFin,
                                "userSession"                       => $strUserSession,
                                "idEmpresa"                         => $intIdEmpresa,
                                "ipUserSession"                     => $strIpUserSession,
                                "oficina"                           => $oficina,
                                "jsonTareasyCategoriasOrdenTrabajo" => $jsonTareasyCategoriasOrdenTrabajo,
                                "prefijoEmpresa"                    => $strPrefijoEmpresa,
                                "idElemento"                        => $id,
                                "observacionOT"                     => $observacionOT,
                                "tipoMantenimiento"                 => $tipoMantenimiento,
                                "idCasoMantenimiento"               => $idCasoMantenimiento,
                                "OTConNumeracionActual"             => $OTConNumeracionActual
            );
        
        try
        {
            //Generar la orden de trabajo y guardarlo en formato pdf
            $gestionTallerService   = $this->get('administracion.GestionTaller');
            $arrResultado           = $gestionTallerService->guardarOrdenTrabajoTransporte($arrayParametros);
            return $this->redirect($this->generateUrl('gestiontaller_showOrdenTrabajoTransporte', array('id' => $arrResultado["idOrdenTrabajo"])));
            
        }
        catch (\Exception $e)
        {   
            error_log($e->getMessage());

            $this->get('session')->getFlashBag()->add('notice', "Ha ocurrido un problema, por favor informar a Sistemas");
            return $this->redirect($this->generateUrl('gestiontaller_newOrdenTrabajoTransporte', array('id' => $id)));
        }
        
    }
    

    /**
     * @Secure(roles="ROLE_337-3558")
     * 
     * Documentación para el método 'showOrdenTrabajoTransporteAction'.
     *
     * Muestra una orden de trabajo en el formato especificado.
     * 
     * @param integer $id id de la orden de trabajo
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 02-08-2016
     *
     */
    public function showOrdenTrabajoTransporteAction($id)
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emGeneral          = $this->getDoctrine()->getManager('telconet_general');
        $objOrdenTrabajoCab = $emComercial->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
        $objElemento        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objOrdenTrabajoCab->getElementoId());
        $objDetallesElemento= $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findBy( array('elementoId' => $objElemento->getId(), 'estado' => self::ESTADO_ACTIVO) );

        $arrayDetallesElemento = array( 'DISCO' => '', 'ANIO' => '');

        if( $objDetallesElemento )
        {
            foreach( $objDetallesElemento as $objDetalle  )
            {
                $arrayDetallesElemento[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }

        $objRequest                     = $this->get('request');
        $objSession                     = $objRequest->getSession();
        $intIdEmpresaSession            = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $strPrefijoEmpresaSession       = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : '';
        $arrayParametrosOrdenTrabajo    = array("idOrdenTrabajoCab"=>$id,"codEmpresaSession"=>$intIdEmpresaSession);
        
        
        $gestionTallerService           = $this->get('administracion.GestionTaller');
        $arrayDetallesOrdenTrabajo      = $gestionTallerService->getDetallesOrdenTrabajoTransporte($arrayParametrosOrdenTrabajo);
        
        
        $idPerAutorizadoPor     = $objOrdenTrabajoCab->getPerAutorizacionId();
        $objPerAutorizadoPor    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerAutorizadoPor);
        $nombreAutorizadoPor    = "";
        if($objPerAutorizadoPor)
        {
            $objPersonaAutorizadoPor    = $objPerAutorizadoPor->getPersonaId();
            $nombreAutorizadoPor        = sprintf('%s', $objPersonaAutorizadoPor);
        }
        
        $objTipoDocumentoGeneral= $emGeneral->getRepository("schemaBundle:AdmiTipoDocumentoGeneral")->findOneByCodigoTipoDocumento('FIRMA');
        if($objTipoDocumentoGeneral)
        {
            $idTipoDocumentoGeneral         = $objTipoDocumentoGeneral->getId();
            
            $arrayParametrosAutorizadoPor   =array(
                                                    "idPersonaEmpresaRol"   => $idPerAutorizadoPor,
                                                    "tipoDocumentoGeneralId"=> $idTipoDocumentoGeneral,
                                                    "empresaCod"            => $intIdEmpresaSession
            );
        }
        

        $strRutaFirmaAutorizadoPor = "";
        $arrayResultadoFirmaAutorizadoPor   = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                          ->getResultadoDocumentosPersona($arrayParametrosAutorizadoPor);
        $arrayRegistrosFirmaAutorizadoPor   = $arrayResultadoFirmaAutorizadoPor['resultado'];
        if($arrayRegistrosFirmaAutorizadoPor)
        {
            list($strRoot,$strRutaFirmaAutorizadoPor)   = explode("telcos/web",$arrayRegistrosFirmaAutorizadoPor[0]["ubicacionFisicaDocumento"]);
        }
        
        $arrayCaracteristicasOT = array(
                                        self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT		=> '', 
                                        self::NOMBRE_CARACTERISTICA_TIPO_ASIGNADO_OT		=> '', 
                                        self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT		=> '',
                                        self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT	=> '',
                                        self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT		=> '');

        //Obtener Características para las órdenes de trabajo 
        $objsCaracteristicasOrdenTrabajo = $emComercial->getRepository('schemaBundle:InfoOrdenTrabajoCaract')
                                                       ->findBy( array('ordenTrabajo' => $objOrdenTrabajoCab, 'estado' => 'Activo') );
        
        if( $objsCaracteristicasOrdenTrabajo )
        {
            foreach( $objsCaracteristicasOrdenTrabajo as $objCaracteristicaOT  )
            {
                $descripcionCaracteristica  = $objCaracteristicaOT->getCaracteristica()->getDescripcionCaracteristica();
                $arrayCaracteristicasOT[$descripcionCaracteristica] = $objCaracteristicaOT->getValor();
            }
        }
        
        $nombrePerAsignadoOrden = "N/A";
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]!='')
        {
            $objPerAsignadoOrden    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                  ->find($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]);
            $objPersonaAsignadoOrden= $objPerAsignadoOrden->getPersonaId();
            $nombrePerAsignadoOrden = sprintf('%s', $objPersonaAsignadoOrden);
        }
        
        $nombrePerConductor     = 'N/A';
        $strRutaFirmaChofer     = "";
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]!='')
        {
            $objPerConductor    = $emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                              ->find($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]);
            $objPersonaConductor= $objPerConductor->getPersonaId();
            $nombrePerConductor = sprintf('%s', $objPersonaConductor);
            
            if($objTipoDocumentoGeneral)
            {
                $arrayParametrosChofer=array(
                                            "idPersonaEmpresaRol"   => $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT],
                                            "tipoDocumentoGeneralId"=> $objTipoDocumentoGeneral->getId(),
                                            "empresaCod"            => $intIdEmpresaSession
                );


                $arrayResultadoFirmaChofer          = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                                  ->getResultadoDocumentosPersona($arrayParametrosChofer);
                $arrayRegistrosFirmaChofer          = $arrayResultadoFirmaChofer['resultado'];
                if($arrayRegistrosFirmaChofer)
                {
                    list($strRoot,$strRutaFirmaChofer)   = explode("telcos/web",$arrayRegistrosFirmaChofer[0]["ubicacionFisicaDocumento"]);
                }

            }
            
            
        }
        
        $kmActual   = 0;
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT]!='')
        {
            $kmActual = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT];
        }
        
        $rutaImagenCabecera = "";
        if($strPrefijoEmpresaSession == 'TN')
        {
            $rutaImagenCabecera = "/public/images/logo_telconet.jpg";
        }
        else if($strPrefijoEmpresaSession == 'MD')
        {
            $rutaImagenCabecera = "/public/images/logo_netlife_big.jpg";
        }
        else if($strPrefijoEmpresaSession == 'TTCO')
        {
            $rutaImagenCabecera = "/public/images/logo_transtelco_new.jpg";
        }
        
        
        $arrayParams=array(
                            'idOrdenTrabajoTransporte'  => $id,
                            'numeroOrdenTrabajo'        => $objOrdenTrabajoCab->getNumeroOrdenTrabajo(),
                            'observacion'               => $objOrdenTrabajoCab->getObservacion(),
                            'idElemento'                => $objOrdenTrabajoCab->getElementoId(),
                            'ordenTrabajoDets'          => $arrayDetallesOrdenTrabajo,
                            'placa'                     => $objElemento->getNombreElemento(),
                            'modeloElemento'            => $objElemento->getModeloElementoId()->getNombreModeloElemento(),
                            'detallesElemento'          => $arrayDetallesElemento,
                            'idEmpresaSession'          => $intIdEmpresaSession,
                            'rutaimagenCabecera'        => $rutaImagenCabecera,
                            'fechaInicio'               => $objOrdenTrabajoCab->getFeInicio()->format('d/m/Y'),
                            'fechaFin'                  => $objOrdenTrabajoCab->getFeFin()->format('d/m/Y'),
                            'nombrePerAsignadoOrden'    => $nombrePerAsignadoOrden,
                            'nombrePerConductor'        => $nombrePerConductor,
                            'nombreAutorizadoPor'       => $nombreAutorizadoPor,
                            'kmActual'                  => number_format( $kmActual ,  0 , "," , "." ),
                            'rutaFirmaAutorizadoPor'    => $strRutaFirmaAutorizadoPor,
                            'rutaFirmaChofer'           => $strRutaFirmaChofer,
                            'tipoMantenimientoOT'       => $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT],
                            'verNumeracionOT'           => $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT]
        );
        return $this->render('administracionBundle:GestionTaller:showOrdenTrabajoTransporte.html.twig', $arrayParams);
 
    }


    
    /**
     * @Secure(roles="ROLE_337-3562")
     * 
     * Documentación para el método 'getOrdenesTrabajoXTransporteAction'.
     *
     * Obtiene las órdenes de Trabajo que se han generado de un vehículo
     * 
     * @param integer $id // id del vehículo
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2016
     *
     */
    public function getOrdenesTrabajoXTransporteAction($id)
    {
        $respuesta = new Response();
        $respuesta->headers->set('Content-Type', 'text/json');

        $emComercial                = $this->getDoctrine()->getManager('telconet');
        $emGeneral                  = $this->getDoctrine()->getManager('telconet_general');
        
        $objTipoDocumentoGeneral= $emGeneral->getRepository("schemaBundle:AdmiTipoDocumentoGeneral")->findOneByCodigoTipoDocumento('ORTRA');
        $idTipoDocumentoGeneral = $objTipoDocumentoGeneral->getId();
        
        $objCaracteristicaTipoMantenimiento = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT);

        $objCaracteristicaKmActual = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                 ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT);

        $objCaracteristicaNumeracion = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                   ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT);
        
        $objRequest                 = $this->get('request');
        $start                      = $objRequest->get('start');
        $limit                      = $objRequest->get('limit');
        $arrayParametros=array(
                                "idElemento"                => $id,
                                "tipoDocumentoGeneralId"    => $idTipoDocumentoGeneral,
                                "idCaractTipoMantenimiento" => $objCaracteristicaTipoMantenimiento->getId(),
                                "idCaractKmActual"          => $objCaracteristicaKmActual->getId(),
                                "idCaractNumeracion"        => $objCaracteristicaNumeracion->getId(),
                                "intStart"                  => $start,
                                "intLimit"                  => $limit
            );
        
        $objJson =  $emComercial->getRepository('schemaBundle:InfoOrdenTrabajo')->getJSONOrdenesTrabajoVehiculo($arrayParametros);                

        $respuesta->setContent($objJson);

        return $respuesta;
    }
    
    /**
     * @Secure(roles="ROLE_337-3562")
     * 
     * Documentación para el método 'showOrdenesTrabajoXTransporteAction'.
     *
     * Obtiene la información del vehículo al que se le ha generado la orden de trabajo
     * 
     * @param integer $id // id del vehículo
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 01-08-2016
     *
     */
    public function showOrdenesTrabajoXTransporteAction($id)
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $objRequest         = $this->get('request');
        $objSession         = $objRequest->getSession();
        $intIdEmpresa       = $objSession->get('idEmpresa');
        
        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        

        

        $arrayDetalle = array('GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'MOTOR' => '','TIPO_VEHICULO' => '','REGION'=>'');

        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayDetalle[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $entityContrato             = array();
        $entityPlanMantenimiento    = array();
        if($arrayDetalle['TIPO_VEHICULO']=='EMPRESA')
        {
            $idPlanMantenimiento                    = $arrayDetalle['PLAN_MANTENIMIENTO'];
            $entityPlanMantenimiento                = $emComercial->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
        }
        else if($arrayDetalle['TIPO_VEHICULO']=='SUBCONTRATADO')
        {
            if($arrayDetalle['CONTRATO'])
            {
                $idContrato        = $arrayDetalle['CONTRATO'];
                $entityContrato    = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
            }      
        }
        
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objMedioTransporte, 'estado' => self::ESTADO_ACTIVO) );
        $strNombreEmpresa = '';

        if( $objInfoEmpresa )
        {
            $strCodEmpresa = $objInfoEmpresa->getEmpresaCod();
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($strCodEmpresa);
            }
            
            if( $objEmpresa )
            {
                $strNombreEmpresa = $objEmpresa->getNombreEmpresa();
            }
        }

        
        $objTipoSolicitud = $emComercial->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

        $objCaracteristicaZonaPredefinida = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

        $objCaracteristicaTareaPredefinida = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                         ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);

        $objCaracteristicaDepartamentoPredefinido = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);

        $arrayParametros = array(
                                    'intEmpresa'                                => $intIdEmpresa,
                                    'tipoElemento'                              => 'VEHICULO',
                                    'strEstadoActivo'                           => self::ESTADO_ACTIVO,
                                    'intIdTipoSolicitud'                        => $objTipoSolicitud->getId(),
                                    'intIdCaracteristicaZonaPredefinida'        => $objCaracteristicaZonaPredefinida->getId(),
                                    'intIdCaracteristicaTareaPredefinida'       => $objCaracteristicaTareaPredefinida->getId(),
                                    'intIdCaracteristicaDepartamentoPredefinido'=> $objCaracteristicaDepartamentoPredefinido->getId(),
                                    'criterios'                                 => array(   
                                                                                            'idElemento'                => $id
                                                                                        )
                                );
        
        
        $arrayResultado     = $emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                ->getResultadoAsignacionVehicularPredefinidaByCriterios($arrayParametros,$emComercial);
        $resultado          = $arrayResultado['resultado'];
        $intTotal           = $arrayResultado['total'];
        
        $idPerChoferPredefinido='';
        $strNombreApellidoChoferPredefinido='N/A';
        $total = 0;

        if($resultado)
        {
            $total = $intTotal;
            foreach($resultado as $data)
            {
                if($data['idPerChoferPredefinido'])
                {
                    $idPerChoferPredefinido                    =  $data['idPerChoferPredefinido'];
                    $strNombreApellidoChoferPredefinido     = $data['nombresChoferPredefinido'] ." ".$data['apellidosChoferPredefinido'];
                }
            }
        }
        
        $arrayDetalle["idPerChoferPredefinido"]             = $idPerChoferPredefinido;
        $arrayDetalle["strNombreApellidoChoferPredefinido"] = $strNombreApellidoChoferPredefinido;
            

        return $this->render('administracionBundle:GestionTaller:showOrdenesTrabajoXTransporte.html.twig',array(
                                                                                            'medioTransporte'   => $objMedioTransporte,
                                                                                            'detalles'          => $arrayDetalle,
                                                                                            'empresa'           => $strNombreEmpresa,
                                                                                            'contrato'          => $entityContrato,
                                                                                            'planMantenimiento' => $entityPlanMantenimiento
                                                                                        ));
    }
    
    
    
    /**
     * @Secure(roles="ROLE_337-4377")
     * 
     * Documentación para el método 'newMantenimientoTransporteAction'.
     * 
     * Permite ingresar un nuevo registro para el mantenimiento de un vehículo donde previamente se debió generar una orden de trabajo.
     * 
     * @param integer $id // id de la orden de trabajo
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-08-2016
     * 
     */
    public function newMantenimientoTransporteAction($id)
    {
        $em                 = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $objOrdenTrabajoCab = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
        $objElemento        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objOrdenTrabajoCab->getElementoId());
        
        

        $objDetallesElemento= $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findBy( array('elementoId' => $objElemento->getId(), 'estado' => self::ESTADO_ACTIVO) );

        $arrayDetallesElemento = array( 'GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'MOTOR' => '','TIPO_VEHICULO' => '','REGION'=>'');

        if( $objDetallesElemento )
        {
            foreach( $objDetallesElemento as $objDetalle  )
            {
                $arrayDetallesElemento[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objElemento, 'estado' => self::ESTADO_ACTIVO) );
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

        $objRequest                     = $this->get('request');
        $objSession                     = $objRequest->getSession();
        $intIdEmpresaSession            = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        $user                           = $objSession->get('user') ? $objSession->get('user') : "";
        $arrayParametrosOrdenTrabajo    = array("idOrdenTrabajoCab"=>$id,"codEmpresaSession"=>$intIdEmpresaSession);
        
        
        $gestionTallerService           = $this->get('administracion.GestionTaller');
        $arrayDetallesOrdenTrabajo      = $gestionTallerService->getDetallesOrdenTrabajoTransporte($arrayParametrosOrdenTrabajo);
        
        
        $arrayCaracteristicasOT = array(
                                        self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT  	=> '', 
                                        self::NOMBRE_CARACTERISTICA_TIPO_ASIGNADO_OT		=> '', 
                                        self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT		=>'',
                                        self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT	=>'',
                                        self::NOMBRE_CARACTERISTICA_CASO_OT			=>'',
                                        self::NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT	=>'',
                                        self::NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT		=>''
                                    );

        //Obtener Características para las órdenes de trabajo 
        $objsCaracteristicasOrdenTrabajo = $em->getRepository('schemaBundle:InfoOrdenTrabajoCaract')
                                              ->findBy( array('ordenTrabajo' => $objOrdenTrabajoCab, 'estado' => 'Activo') );
        
        if( $objsCaracteristicasOrdenTrabajo )
        {
            foreach( $objsCaracteristicasOrdenTrabajo as $objCaracteristicaOT  )
            {
                $descripcionCaracteristica  = $objCaracteristicaOT->getCaracteristica()->getDescripcionCaracteristica();
                $arrayCaracteristicasOT[$descripcionCaracteristica] = $objCaracteristicaOT->getValor();
            }
        }
        
        $nombrePerAsignadoOrden = "N/A";
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]!='')
        {
            $objPerAsignadoOrden    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                         ->find($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]);
            $objPersonaAsignadoOrden= $objPerAsignadoOrden->getPersonaId();
            $nombrePerAsignadoOrden = sprintf('%s', $objPersonaAsignadoOrden);
        }
        
        $nombrePerConductor     = 'N/A';
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]!='')
        {
            $objPerConductor    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                     ->find($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]);
            $objPersonaConductor= $objPerConductor->getPersonaId();
            $nombrePerConductor = sprintf('%s', $objPersonaConductor);

        }
        
        $kmActual   = 0;
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT]!='')
        {
            $kmActual = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT];
        }
        $tipoMantenimientoOT    = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT];
        
        $idPlanMantenimiento        = 0;
        $strNombrePlanMantenimiento = "";
        $idMantenimiento            = 0;
        $strNombreMantenimiento     = "";
        
        $idCaso                     = 0;
        $strNumeroCaso              = "";
        if($tipoMantenimientoOT=="PREVENTIVO")
        {
            $idPlanMantenimiento        = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT];
            $idMantenimiento            = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT];
            $objPlanMantenimiento       = $emSoporte->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
            $strNombrePlanMantenimiento = $objPlanMantenimiento->getNombreProceso();
            $objMantenimiento           = $emSoporte->getRepository('schemaBundle:AdmiProceso')->find($idMantenimiento);
            $strNombreMantenimiento     = $objMantenimiento->getNombreProceso();
        }
        else if($tipoMantenimientoOT=="CORRECTIVO")
        {
            $idCaso         = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CASO_OT];
            $objCaso        = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($idCaso);
            $strNumeroCaso  = $objCaso->getNumeroCaso();
            
        }
        
        $idPerAutorizadoPor     = $objOrdenTrabajoCab->getPerAutorizacionId();
        $objPerAutorizadoPor    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerAutorizadoPor);
        $objPersonaAutorizadoPor= $objPerAutorizadoPor->getPersonaId();
        $nombreAutorizadoPor = sprintf('%s', $objPersonaAutorizadoPor);
        $arrayParams=array(
                            'medioTransporte'           => $objElemento,
                            'detalles'                  => $arrayDetallesElemento,
                            'empresa'                   => $strNombreEmpresa,
                            'idOrdenTrabajoTransporte'  => $id,
                            'numeroOrdenTrabajo'        => $objOrdenTrabajoCab->getNumeroOrdenTrabajo(),
                            'idPlanMantenimiento'       => $idPlanMantenimiento,
                            'nombrePlanMantenimiento'   => $strNombrePlanMantenimiento,
                            'idMantenimiento'           => $idMantenimiento,
                            'nombreMantenimiento'       => $strNombreMantenimiento,
                            'idCaso'                    => $idCaso,
                            'numeroCaso'                => $strNumeroCaso,
                            'km'                        => number_format( $kmActual ,  0 , "," , "." ),
                            'nombreAutorizadoPor'       => $nombreAutorizadoPor,
                            'nombrePerAsignadoOrden'    => $nombrePerAsignadoOrden,
                            'nombrePerConductor'        => $nombrePerConductor,
                            'fechaInicioOrdenTrabajo'   => $objOrdenTrabajoCab->getFeInicio()->format('d/m/Y'),
                            'fechaFinOrdenTrabajo'      => $objOrdenTrabajoCab->getFeFin()->format('d/m/Y'),
                            'usrCreacion'               => $objOrdenTrabajoCab->getUsrCreacion(),
                            'ordenTrabajoDets'          => $arrayDetallesOrdenTrabajo,
                            'idEmpresaSession'          => $intIdEmpresaSession,
                            'tipoMantenimientoOT'       => $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT]
        );
        return $this->render('administracionBundle:GestionTaller:newMantenimientoTransporte.html.twig', $arrayParams);  
    }
    
    /**
     * @Secure(roles="ROLE_337-4377")
     * 
     * Documentación para el método 'createMantenimientoTransporteAction'.
     * 
     * Crear un nuevo registro para el mantenimiento de un vehículo.
     * 
     * @param integer $id // id de la orden de trabajo
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 03-08-2016
     * 
     */  
    public function createMantenimientoTransporteAction($id)
    {
        $objRequest                         = $this->get('request');
        $objSession                         = $objRequest->getSession();
        $emComercial                        = $this->getDoctrine()->getManager('telconet');
        $valorTotal                         = $objRequest->get('valorTotal') ? $objRequest->get('valorTotal') : "";
        $fechaApertura                      = $objRequest->get("fechaApertura") ? $objRequest->get('fechaApertura') : "";
        $fechaCierre                        = $objRequest->get("fechaCierre") ? $objRequest->get('fechaCierre') : "";
        $strUserSession                     = $objSession->get('user') ? $objSession->get('user') :"";
        $strIdEmpresa                       = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : '';
        $strIpUserSession                   = $objRequest->getClientIp() ? $objRequest->getClientIp() : "";
        $strPrefijoEmpresa                  = $objSession->get('prefijoEmpresa') ? $objSession->get('prefijoEmpresa') : "";
        $datetimeActual                     = new \DateTime('now');
        $idElemento                         = $objRequest->get('idElemento') ? $objRequest->get('idElemento') : 0;
        $jsonValoresCategoriasMantenimientos= $objRequest->get('json_valores_categorias_mantenimientos') ? 
                                              $objRequest->get('json_valores_categorias_mantenimientos') : '';
        
        $emComercial->getConnection()->beginTransaction();
        try
        {
            $objMantenimiento=new InfoMantenimientoElemento();
            $objMantenimiento->setOrdenTrabajoId($id);
            $objMantenimiento->setElementoId($idElemento);
            $objMantenimiento->setValorTotal($valorTotal);
            list($dayApertura,$mesApertura,$yearApertura)=explode('/',$fechaApertura);
            $datetimeApertura     = new \DateTime();
            $datetimeApertura->setDate($yearApertura, $mesApertura, $dayApertura );
            $objMantenimiento->setFeInicio($datetimeApertura);
            list($dayCierre,$mesCierre,$yearCierre)=explode('/',$fechaCierre);
            $datetimeCierre     = new \DateTime();
            $datetimeCierre->setDate($yearCierre, $mesCierre, $dayCierre);
            $objMantenimiento->setFeFin($datetimeCierre);
            $objMantenimiento->setUsrCreacion($strUserSession);
            $objMantenimiento->setIpCreacion($strIpUserSession);
            $objMantenimiento->setFeCreacion($datetimeActual);
            $objMantenimiento->setEstado('Activo');
            
            $emComercial->persist($objMantenimiento);
            $emComercial->flush();
            
            
            $objTmpJsonValoresCategoriasMantenimientos  = json_decode($jsonValoresCategoriasMantenimientos);
            $intTotalValoresCategoriasMantenimientos    = $objTmpJsonValoresCategoriasMantenimientos->total;
            
            if( $intTotalValoresCategoriasMantenimientos )
            {
                if( $intTotalValoresCategoriasMantenimientos > 0 )
                {
                    $arrayValoresCategoriasMantenimientos = $objTmpJsonValoresCategoriasMantenimientos->valoresCategoriasMantenimientos;
                    foreach( $arrayValoresCategoriasMantenimientos as $objItemValoresCategoriasMantenimientos )
                    {
                        $idCategoriaMantenimientoTransporte = $objItemValoresCategoriasMantenimientos->idParametroDet;
                        $valorTotalCategoria                = $objItemValoresCategoriasMantenimientos->valorTotalCategoria;
                        
                        $objInfoMantenimientoElementoDet = new InfoMantenimientoElementoDet();
                        $objInfoMantenimientoElementoDet->setCategoriaId($idCategoriaMantenimientoTransporte);
                        $objInfoMantenimientoElementoDet->setMantenimientoElementoId($objMantenimiento->getId());
                        $objInfoMantenimientoElementoDet->setFeCreacion($datetimeActual);
                        $objInfoMantenimientoElementoDet->setIpCreacion($strIpUserSession);
                        $objInfoMantenimientoElementoDet->setUsrCreacion($strUserSession);
                        $objInfoMantenimientoElementoDet->setValorTotal($valorTotalCategoria);
                        
                        $emComercial->persist($objInfoMantenimientoElementoDet);
                        $emComercial->flush();
                    }
                }
            }

            
            /**Guardar Archivos**/
            $arrayParametrosArchivos     = array(
                                                    "idMantenimientoElemento"   => $objMantenimiento->getId(),
                                                    "idOrdenTrabajo"            => $id,
                                                    "idElemento"                => $idElemento,
                                                    "strPrefijoEmpresa"         => $strPrefijoEmpresa,
                                                    "strUser"                   => $strUserSession,
                                                    "strIdEmpresa"              => $strIdEmpresa
                                            );
            $gestionTallerService           = $this->get('administracion.GestionTaller');
            $gestionTallerService->guardarMultiplesAdjuntosMantenimientosTransporte($arrayParametrosArchivos);
            
            $emComercial->getConnection()->commit();
            $emComercial->getConnection()->close();
        } 
        catch (\Exception $e) 
        {
            error_log($e->getMessage());

            $emComercial->getConnection()->rollback();
            $emComercial->getConnection()->close();
            
            $this->get('session')->getFlashBag()->add('notice', "Ha ocurrido un problema, por favor informar a Sistemas");
            return $this->redirect($this->generateUrl('gestiontaller_showMantenimientosXOrdenTrabajoTransporte', array('id' => $id)));
        }
        
        return $this->redirect($this->generateUrl('gestiontaller_showMantenimientosXOrdenTrabajoTransporte', array('id' => $id))); 
    }
    
    /**
     * @Secure(roles="ROLE_337-4397")
     * 
     * Documentación para el método 'showMantenimientosXOrdenTrabajoTransporteAction'.
     * 
     * Permite observar todos los mantenimientos que se han realizado a un vehículo.
     * 
     * @param integer $id // id de la orden de trabajo del vehículo 
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 18-08-2016
     * 
     */ 
    public function showMantenimientosXOrdenTrabajoTransporteAction($id)
    {
        $em                 = $this->getDoctrine()->getManager();
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        $emSoporte          = $this->getDoctrine()->getManager('telconet_soporte');
        $objOrdenTrabajoCab = $em->getRepository('schemaBundle:InfoOrdenTrabajo')->find($id);
        $objElemento        = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objOrdenTrabajoCab->getElementoId());

        $objDetallesElemento= $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                ->findBy( array('elementoId' => $objElemento->getId(), 'estado' => self::ESTADO_ACTIVO) );

        $arrayDetallesElemento = array( 'GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'MOTOR' => '','TIPO_VEHICULO' => '','REGION'=>'');

        if( $objDetallesElemento )
        {
            foreach( $objDetallesElemento as $objDetalle  )
            {
                $arrayDetallesElemento[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objElemento, 'estado' => self::ESTADO_ACTIVO) );
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
        
        
        $arrayCaracteristicasOT = array(
                                        self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT		=> '', 
                                        self::NOMBRE_CARACTERISTICA_TIPO_ASIGNADO_OT		=> '', 
                                        self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT		=>'',
                                        self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT	=>'',
                                        self::NOMBRE_CARACTERISTICA_CASO_OT			=>'',
                                        self::NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT	=>'',
                                        self::NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT		=>'',
                                        self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT		=>''
                                    );

        //Obtener Características para las órdenes de trabajo 
        $objsCaracteristicasOrdenTrabajo = $em->getRepository('schemaBundle:InfoOrdenTrabajoCaract')
                                         ->findBy( array('ordenTrabajo' => $objOrdenTrabajoCab, 'estado' => 'Activo') );
        
        if( $objsCaracteristicasOrdenTrabajo )
        {
            foreach( $objsCaracteristicasOrdenTrabajo as $objCaracteristicaOT  )
            {
                $descripcionCaracteristica  = $objCaracteristicaOT->getCaracteristica()->getDescripcionCaracteristica();
                $arrayCaracteristicasOT[$descripcionCaracteristica] = $objCaracteristicaOT->getValor();
            }
        }
        
        $nombrePerAsignadoOrden = "N/A";
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]!='')
        {
            $objPerAsignadoOrden    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                         ->find($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]);
            $objPersonaAsignadoOrden= $objPerAsignadoOrden->getPersonaId();
            $nombrePerAsignadoOrden = sprintf('%s', $objPersonaAsignadoOrden);
        }
        
        $nombrePerConductor     = 'N/A';
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]!='')
        {
            $objPerConductor    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                     ->find($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]);
            $objPersonaConductor= $objPerConductor->getPersonaId();
            $nombrePerConductor = sprintf('%s', $objPersonaConductor);

        }
        
        $kmActual   = 0;
        if($arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT]!='')
        {
            $kmActual = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT];
        }
        $tipoMantenimientoOT    = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT];
        
        $idPlanMantenimiento        = 0;
        $strNombrePlanMantenimiento = "";
        $idMantenimiento            = 0;
        $strNombreMantenimiento     = "";
        
        $idCaso                     = 0;
        $strNumeroCaso              = "";
        if($tipoMantenimientoOT=="PREVENTIVO")
        {
            $idPlanMantenimiento        = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT];
            $idMantenimiento            = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT];
            $objPlanMantenimiento       = $emSoporte->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
            $strNombrePlanMantenimiento = $objPlanMantenimiento->getNombreProceso();
            $objMantenimiento           = $emSoporte->getRepository('schemaBundle:AdmiProceso')->find($idMantenimiento);
            $strNombreMantenimiento     = $objMantenimiento->getNombreProceso();
        }
        else if($tipoMantenimientoOT=="CORRECTIVO")
        {
            $idCaso         = $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_CASO_OT];
            $objCaso        = $emSoporte->getRepository('schemaBundle:InfoCaso')->find($idCaso);
            $strNumeroCaso  = $objCaso->getNumeroCaso();
            
        }
        
        $idPerAutorizadoPor     = $objOrdenTrabajoCab->getPerAutorizacionId();
        $objPerAutorizadoPor    = $em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerAutorizadoPor);
        $objPersonaAutorizadoPor= $objPerAutorizadoPor->getPersonaId();
        $nombreAutorizadoPor = sprintf('%s', $objPersonaAutorizadoPor);

            

        return $this->render(   'administracionBundle:GestionTaller:showMantenimientosXOrdenTrabajoTransporte.html.twig',
                                array(
                                        'verNumeracionOT'           => $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT],
                                        'tipoMantenimientoOT'       => $arrayCaracteristicasOT[self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT],
                                        'idOrdenTrabajoTransporte'  => $id,
                                        'numeroOrdenTrabajo'        => $objOrdenTrabajoCab->getNumeroOrdenTrabajo(),
                                        'idPlanMantenimiento'       => $idPlanMantenimiento,
                                        'detalles'                  => $arrayDetallesElemento,
                                        'nombrePlanMantenimiento'   => $strNombrePlanMantenimiento,
                                        'idMantenimiento'           => $idMantenimiento,
                                        'nombreMantenimiento'       => $strNombreMantenimiento,
                                        'idCaso'                    => $idCaso,
                                        'numeroCaso'                => $strNumeroCaso,
                                        'km'                        => number_format( $kmActual ,  0 , "," , "." ),
                                        'nombreAutorizadoPor'       => $nombreAutorizadoPor,
                                        'nombrePerAsignadoOrden'    => $nombrePerAsignadoOrden,
                                        'nombrePerConductor'        => $nombrePerConductor,
                                        'fechaInicioOrdenTrabajo'   => $objOrdenTrabajoCab->getFeInicio()->format('d/m/Y'),
                                        'fechaFinOrdenTrabajo'      => $objOrdenTrabajoCab->getFeFin()->format('d/m/Y'),
                                        'medioTransporte'           => $objElemento,
                                        'usrCreacion'               => $objOrdenTrabajoCab->getUsrCreacion(),
                                        'empresa'                   => $strNombreEmpresa
                                    ));
    }    
        
    
    /**
     * @Secure(roles="ROLE_337-4397")
     * 
     * Documentación para el método 'showMantenimientosVehiculoAction'.
     * 
     * Permite observar todos los mantenimientos que se han realizado a un vehículo.
     * 
     * @param integer $id // id del vehículo 
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-07-2016
     * 
     */ 
    public function showMantenimientosTransporteAction($id)
    {
        $emComercial        = $this->getDoctrine()->getManager('telconet');
        $emInfraestructura  = $this->getDoctrine()->getManager('telconet_infraestructura');
        
        $objMedioTransporte = $emInfraestructura->getRepository('schemaBundle:InfoElemento')->findOneById($id);
        
        if( !$objMedioTransporte )
        {
            throw new NotFoundHttpException('No existe el InfoElemento que se quiere mostrar');
        }
        
        $objDetalles = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                         ->findBy( array('elementoId' => $id, 'estado' => self::ESTADO_ACTIVO) );
        

        

        $arrayDetalle = array('GPS' => '', 'DISCO' => '', 'ANIO' => '', 'CHASIS' => '', 'MOTOR' => '','TIPO_VEHICULO' => '','REGION'=>'');

        
        if( $objDetalles )
        {
            foreach( $objDetalles as $objDetalle  )
            {
                $arrayDetalle[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }
        
        $entityContrato             = array();
        $entityPlanMantenimiento    = array();
        if($arrayDetalle['TIPO_VEHICULO']=='EMPRESA')
        {
            $idPlanMantenimiento                    = $arrayDetalle['PLAN_MANTENIMIENTO'];
            $entityPlanMantenimiento                = $emComercial->getRepository('schemaBundle:AdmiProceso')->find($idPlanMantenimiento);
        }
        else if($arrayDetalle['TIPO_VEHICULO']=='SUBCONTRATADO')
        {
            if($arrayDetalle['CONTRATO'])
            {
                $idContrato        = $arrayDetalle['CONTRATO'];
                $entityContrato    = $emComercial->getRepository('schemaBundle:InfoContrato')->find($idContrato);
            }      
        }
        
        
        $objInfoEmpresa   = $emInfraestructura->getRepository('schemaBundle:InfoEmpresaElemento')
                                              ->findOneBy( array ('elementoId' => $objMedioTransporte, 'estado' => self::ESTADO_ACTIVO) );
        $strNombreEmpresa = '';

        if( $objInfoEmpresa )
        {
            $strCodEmpresa = $objInfoEmpresa->getEmpresaCod();
            
            $objEmpresa = null;
            if( $strCodEmpresa )
            {
                $objEmpresa = $emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($strCodEmpresa);
            }
            
            if( $objEmpresa )
            {
                $strNombreEmpresa = $objEmpresa->getNombreEmpresa();
            }
        }   

        return $this->render(   'administracionBundle:GestionTaller:showMantenimientosTransporte.html.twig',
                                array(
                                        'medioTransporte'   => $objMedioTransporte,
                                        'detalles'          => $arrayDetalle,
                                        'empresa'           => $strNombreEmpresa,
                                        'contrato'          => $entityContrato,
                                        'planMantenimiento' => $entityPlanMantenimiento
                                    ));
    }
    
    
    /**
     * @Secure(roles="ROLE_337-4397")
     * 
     * Documentación para el método 'getMantenimientosTransporteAction'.
     * 
     * Permite obtener un mantenimiento asociado a un vehículo.
     * 
     * @param integer $id // id de elementoId
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 06-07-2016
     * 
     */ 
    public function getMantenimientosTransporteAction($id)
    {
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest     = $this->get('request');

        $parametros     = array();
        
        $objCaracteristicaTipoMantenimiento = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT);
        
        $objCaracteristicaKmActual          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT);
        
        $objCaracteristicaNumeracion        = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT);
        
        
        $start = $objRequest->get('start');
        $limit = $objRequest->get('limit');
        
        $parametros["idElemento"]                           = $id;
        $parametros["idCaracteristicaTipoMantenimiento"]    = $objCaracteristicaTipoMantenimiento->getId();
        $parametros["idCaracteristicaKm"]                   = $objCaracteristicaKmActual->getId();
        $parametros["idCaracteristicaNumeracion"]           = $objCaracteristicaNumeracion->getId();
        $parametros["intStart"]                             = $start;
        $parametros["intLimit"]                             = $limit;

        $objJson = $emComercial->getRepository('schemaBundle:InfoMantenimientoElemento')->getJSONMantenimientosXParametros($parametros);

        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    /**
     * @Secure(roles="ROLE_337-4397")
     * 
     * Documentación para el método 'getMantenimientosXOrdenTrabajoTransporte'.
     * 
     * Permite obtener un mantenimiento asociado a una orden de trabajo de un vehículo.
     * 
     * @param integer $id // id de la orden de trabajo
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-08-2016
     * 
     */ 
    public function getMantenimientosXOrdenTrabajoTransporteAction($id)
    {
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');

        $objRequest     = $this->get('request');

        $parametros = array();

        $objCaracteristicaTipoMantenimiento = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT);

        $objCaracteristicaKmActual          = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT);

        $objCaracteristicaNumeracion        = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                          ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT);


        $start = $objRequest->get('start');
        $limit = $objRequest->get('limit');

        $parametros["idOrdenTrabajo"]                       = $id;
        $parametros["idCaracteristicaTipoMantenimiento"]    = $objCaracteristicaTipoMantenimiento->getId();
        $parametros["idCaracteristicaKm"]                   = $objCaracteristicaKmActual->getId();
        $parametros["idCaracteristicaNumeracion"]           = $objCaracteristicaNumeracion->getId();
        $parametros["intStart"]                             = $start;
        $parametros["intLimit"]                             = $limit;

        $objJson = $emComercial->getRepository('schemaBundle:InfoMantenimientoElemento')->getJSONMantenimientosXParametros($parametros);

        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    
    /**
     * @Secure(roles="ROLE_337-4437")
     * 
     * Documentación para el método 'getDetallesMantenimientoTransporteAction'.
     * 
     * Obtiene los detalles de un determinado mantenimiento del transporte
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 08-07-2016
     * 
     */ 
    public function getDetallesMantenimientoTransporteAction()
    {
        $objResponse    = new Response();
        $objResponse->headers->set('Content-Type', 'text/json');
        $emComercial    = $this->getDoctrine()->getManager('telconet');
        
        $objRequest     = $this->get('request');
        $arrayParametros=array("idMantenimientoTransporte"=>$objRequest->get('idMantenimientoTransporte'));
        
        $objJson        = $emComercial->getRepository('schemaBundle:InfoMantenimientoElementoDet')
                                      ->getJSONDetallesMantenimientosXParametros($arrayParametros);

        $objResponse->setContent($objJson);

        return $objResponse;
    }
    
    /**
     * 
     * @Secure(roles="ROLE_337-4438")
     * 
     * Documentación para el método 'getAdjuntosMantenimientoTransporteAction'.
     * 
     * Función en Ajax que lista los archivos digitales asociados al mantenimiento.
     * 
     * @param integer $id
     * 
     * @return Response.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 12-12-2015
     */      
    public function getAdjuntosMantenimientoTransporteAction($id) 
    {
        $objRequest  = $this->getRequest();
        $start       = $objRequest->get('start', 0);
        $limit       = $objRequest->get('limit', 10);
        $response    = new Response();
        $response->headers->set('Content-type', 'text/json');		
        $emComunicacion   = $this->getDoctrine()->getManager('telconet_comunicacion');

        $objInfoDocumentoRelacion = $emComunicacion->getRepository('schemaBundle:InfoDocumentoRelacion');
        $objEntities              = $objInfoDocumentoRelacion->findBy(array('mantenimientoElementoId' => $id,'estado' => 'Activo'), 
                                                                      array('id' => 'DESC'), $limit, $start);
        $intTotal                 = $objInfoDocumentoRelacion->findBy(array('mantenimientoElementoId' => $id,'estado' => 'Activo'));
             
        $arrayResponse          = array();
        $arrayResponse['total'] = count($intTotal);
        $arrayResponse['logs']  = array();
        
        foreach ($objEntities as $entity) 
		{
            $arrayEntity                                = array();
            $arrayEntity['id']                          = $entity->getDocumentoId();

            $infoDocumento                              = $emComunicacion->getRepository('schemaBundle:InfoDocumento')->find($entity->getDocumentoId());
            $arrayEntity['ubicacionLogicaDocumento']    = $infoDocumento->getUbicacionLogicaDocumento();
            $arrayEntity['feCreacion']                  = date_format($entity->getFeCreacion(), 'd-m-Y H:i:s');
            if($infoDocumento->getFechaPublicacionHasta())
            {
                $arrayEntity['feCaducidad']             = $infoDocumento->getFechaPublicacionHasta()->format('d-m-Y');
            }
            else
            {
                $arrayEntity['feCaducidad']             = "";
            }

            $arrayEntity['usrCreacion']                 = $entity->getUsrCreacion();     
            $arrayEntity['linkVerDocumento']            = $infoDocumento->getUbicacionFisicaDocumento();            
            $arrayResponse['encontrados'][]             = $arrayEntity;
        }
        
        $response->setContent(json_encode($arrayResponse));
        return $response;
    }
    

    /**
     * @Secure(roles="ROLE_337-3877")
     * 
     * Documentación para el método 'newReinicioNumeracionOrdenTrabajoTransporteAction'.
     *
     * Muestra el formulario para reiniciar la numeración de la orden de trabajo
     * 
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 
     *
     */
    public function newReinicioNumeracionOrdenTrabajoTransporteAction()
    {
        return $this->render('administracionBundle:GestionTaller:newReinicioNumeracionOrdenTrabajo.html.twig',
                             array("error"=>""));
    }
    
    
    
    /**
     * @Secure(roles="ROLE_337-3877")
     * 
     * Documentación para el método 'reiniciarNumeracionOrdenTrabajoTransporteAction'.
     *
     * Obtiene la última numeración de las órdenes de trabajo y valida que el valor a reiniciar sea mayor a éste.
     *
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-08-2016
     *
     */
    public function reiniciarNumeracionOrdenTrabajoTransporteAction()
    {
        $em                     = $this->getDoctrine()->getManager();
        $objRequest             = $this->get('request');
        
        $secuenciaReinicio      = $objRequest->get('numero_reinicio') ? $objRequest->get('numero_reinicio'): '';
        $objSession             = $objRequest->getSession();
        $oficina                = $objSession->get('idOficina');
        $intIdEmpresaSession    = $objSession->get('idEmpresa') ? $objSession->get('idEmpresa') : 0;
        
        
        $em->getConnection()->beginTransaction();
        try
        {
            $datosNumeracion        = $em->getRepository('schemaBundle:AdmiNumeracion')->findByEmpresaYOficina($intIdEmpresaSession,$oficina,"ORDVE");
            $secuenciaAsig         = str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 

            $strMensaje='';
            if($secuenciaReinicio<$secuenciaAsig)
            {
                $strMensaje="La renumeración no se puede realizar porque el número que ingresó es menor a la secuencia de la próxima "
                            ."orden de trabajo a generarse. ";
                $strMensaje.="Próxima secuencia a generarse: ".$secuenciaAsig;
                $em->getConnection()->close();
                return $this->render('administracionBundle:GestionTaller:newReinicioNumeracionOrdenTrabajo.html.twig', array(
                                     "error" => $strMensaje
                ));
            }
            else
            {
                //Actualizo la secuencia de la numeración
                $datosNumeracion->setSecuencia($secuenciaReinicio);
                $em->persist($datosNumeracion);
                $em->flush();
                $em->getConnection()->commit();
                $em->getConnection()->close();
                return $this->redirect($this->generateUrl('gestiontaller_showNumeracionOrdenTrabajoTransporte', array('id' => $datosNumeracion->getId())));
            }
        } catch (Exception $e) 
        {
            error_log($e->getMessage());
            $this->get('session')->getFlashBag()->add('notice', "Ha ocurrido un problema, por favor informar a Sistemas");
            return $this->redirect($this->generateUrl('newReinicioNumeracionOrdenTrabajo', array("error" => $e->getMessage())));

        }
        
    }
    
    /**
     * @Secure(roles="ROLE_337-3877")
     * 
     * Documentación para el método 'showNumeracionOrdenTrabajoTransporteAction'.
     *
     * Muestra la última secuencua de la orden de trabajo.
     *
     * @return Response.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 04-08-2016 
     *
     */
    public function showNumeracionOrdenTrabajoTransporteAction($id)
    {
        $em                     = $this->getDoctrine()->getManager();
        $datosNumeracion        = $em->getRepository('schemaBundle:AdmiNumeracion')->find($id);
        
        return $this->render('administracionBundle:GestionTaller:showNumeracionOrdenTrabajo.html.twig', array(
                             'datosNumeracion' => $datosNumeracion
        ));
    }

}