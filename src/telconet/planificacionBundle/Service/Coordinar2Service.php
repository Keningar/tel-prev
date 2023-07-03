<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;

/**
 * Clase CoordinarService
 *
 * Clase que se encarga de realizar acciones de submenu Coordinacion 2
 *
 * @author Daniel Reyes <djreyes@telconet.ec>
 * @version 1.0 14-09-2021
 * 
 */
class Coordinar2Service
{
    private $objContainer;
    private $emComercial;
    private $emComunicacion;
    private $emInfraestructura;
    private $emSoporte;
    private $emGeneral;
    private $serviceInfoServicio;
    private $serviceCoordinar;
    private $servicePlanificacion;
    private $serviceEnvioPlantilla;
    private $serviceUtil;
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer             = $objContainer;
        $this->emComercial              = $objContainer->get('doctrine')->getManager('telconet');
        $this->emComunicacion           = $objContainer->get('doctrine')->getManager('telconet_comunicacion');
        $this->emInfraestructura        = $objContainer->get('doctrine')->getManager("telconet_infraestructura");
        $this->emSoporte                = $objContainer->get('doctrine')->getManager("telconet_soporte");
        $this->emGeneral                = $objContainer->get('doctrine')->getManager("telconet_general");
        $this->serviceInfoServicio      = $objContainer->get('comercial.InfoServicio');
        $this->serviceCoordinar         = $objContainer->get('planificacion.Coordinar');
        $this->servicePlanificacion     = $objContainer->get('planificacion.Planificar');
        $this->serviceEnvioPlantilla    = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceUtil              = $objContainer->get('schema.Util');
    }

    /**
     * Funcion que permite realizar la cancelacion (eliminar, anular, rechazar, detener)
     * de los productos adicionales automaticos anexos al servicio de internet
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 29-07-2021 - Version inicial
     * 
     * @author Emmanuel Martllo <emartillo@telconet.ec>
     * @version 1.1 10-11-2022 - Se agrega validacion para anular simultaneamente productos NetlifeCam
     * @param array $arrayDatosParametros
     * 
    */
    public function cancelarProdAdicionalesAut($arrayDatosParametros)
    {
        $objServicio     = $arrayDatosParametros['objServicio'];
        $strEstado       = $arrayDatosParametros['strEstado'];
        $strObservacion  = $arrayDatosParametros['strObservacion'];
        $intCodEmpresa   = $arrayDatosParametros['intCodEmpresa'];
        $strIpCreacion   = $arrayDatosParametros['strIpCreacion'];
        $strUserCreacion = $arrayDatosParametros['strUserCreacion'];
        $intIdDepartamento = $arrayDatosParametros['idPersonaRol'];
        $intIdPersonaEmpresaRol = $arrayDatosParametros['idDepartamento'];
        $objPlanServicio = $objServicio->getPlanId();
        $objProdServicio = $objServicio->getProductoId();
        $arrayProduNetlifeCam   = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PROYECTO NETLIFECAM',
                                            'INFRAESTRUCTURA',
                                            '',
                                            'PARAMETRIZACION DE NOMBRES TECNICOS DE PRODUCTOS NETLIFE CAM',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '',
                                            '18');
        $arrayParamProducNetCam   = $this->serviceUtil->obtenerValoresParametro($arrayProduNetlifeCam); 
        if (!empty($objPlanServicio) && empty($objProdServicio))
        {
            // Seleccionamos los estados permitidos para anular los productos adicionales
            $arrayEstaPermitidos = array();
            $arrayValoresParametros = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                    'Estados permitidos para los productos adicionales',
                                                    '','','','','',$intCodEmpresa);
            if(is_array($arrayValoresParametros) && !empty($arrayValoresParametros))
            {
                $arrayEstaPermitidos = $this->serviceUtil->obtenerValoresParametro($arrayValoresParametros);
            }
            // Obtenemos los productos adicionales permitidos
            $arrayListadoServicios = array();
            $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                    'Lista de productos adicionales automaticos',
                                                    '','','','','',$intCodEmpresa);
            // Obtenemos los servicios del punto
            $intIdPunto = $objServicio->getPuntoId()->getid();
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findServiciosByPuntoAndEstado($intIdPunto,null,null);
            foreach($arrayServiciosPunto['registros'] as $objServicioPunto)
            {
                $objProducto = $objServicioPunto->getProductoId();
                $strNombreTecnico = is_object($objServicioPunto->getProductoId()) ? 
                                            $objServicioPunto->getProductoId()->getNombreTecnico() : null;
                if (!empty($objProducto) &&
                    (in_array($objServicioPunto->getEstado(), $arrayEstaPermitidos)|| 
                    in_array($objServicioPunto->getProductoId()->getNombreTecnico(), $arrayParamProducNetCam)))
                {
                    foreach($arrayListadoServicios as $objListado)
                    {
                        if ($objProducto->getId() == $objListado['valor1'])
                        {
                            $objServicioPunto->setEstado($strEstado);
                            $this->emComercial->persist($objServicioPunto);

                            $entityServHistorial = new InfoServicioHistorial();
                            $entityServHistorial->setServicioId($objServicioPunto);
                            $entityServHistorial->setObservacion($strObservacion);
                            $entityServHistorial->setEstado($strEstado);
                            $entityServHistorial->setUsrCreacion($strUserCreacion);
                            $entityServHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServHistorial->setIpCreacion($strIpCreacion);
                            $this->emComercial->persist($entityServHistorial);
                            $this->emComercial->flush();
                            if(in_array($strNombreTecnico, $arrayParamProducNetCam))
                            {  
                                $objSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->findBy(
                                    array("servicioId" => $objServicioPunto->getId()));
                                $intSolNet  = $objSolicitud[0];
                                $objInfoDetNet = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')->findOneBy(
                                    array("detalleSolicitudId" => $intSolNet->getId()));
                                if(is_object($objInfoDetNet))
                                {
                                    $objInfoTarNet = $this->emSoporte->getRepository('schemaBundle:InfoTarea')->findOneBy(
                                        array("detalleId" => $objInfoDetNet->getId()));
                                    $arraySolNet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PRODUCTOS ADICIONALES MANUALES',
                                                'COMERCIAL','',
                                                'Listado de tareas asociadas a la solicitud',
                                                $intSolNet->getTipoSolicitudId()->getId(),
                                                $intSolNet->getTipoSolicitudId()->getDescripcionSolicitud(),
                                                '',$objInfoTarNet->getTareaId(),'',"18");
                                }
                                $arrayDatosCancelar = array ('objSolicitud'   => $objSolicitud[0],
                                                                'strObservacion'    => $strObservacion,
                                                                    'strEstadoTarea'    => $strEstado,
                                                                    'strUsuCreacion'    => $strUserCreacion,
                                                                    'strIpCreacion'     => $strIpCreacion,
                                                                    'arraySolNet'       => $arraySolNet,
                                                                    'intIdPersonaRol'   => $intIdDepartamento,
                                                                    'intIdDepartamento' => $intIdPersonaEmpresaRol,
                                                                    'intCodEmpresa'     => $intCodEmpresa);
                                $this->serviceInfoServicio->cancelarTareaSolicitud($arrayDatosCancelar); 
                            }
                        }
                    }
                }
            }
        }
    }

    /*
     * Método que permite anular, rechazar o detener los servicios y las solicitudes de los productos adicionales
     * manuales asociados a un servicio de internet de forma simultanea a la misma.
     *
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 - Version inicial.
     * 
     * @param $arrayDatosEstado -> Contiene todos los datos del punto y los estados.
    */
    public function cancelacionSimulServicios($arrayDatosEstado)
    {
        $intIdPunto        = $arrayDatosEstado['idPunto'];
        $intIdServicio     = $arrayDatosEstado['idServicio'];
        $strEstado         = $arrayDatosEstado['estado'];
        $strObservacion    = $arrayDatosEstado['observacion'];
        $strUsuario        = $arrayDatosEstado['usuario'];
        $strIpCreacion     = $arrayDatosEstado['ipCreacion'];
        $intIdEmpresa      = $arrayDatosEstado['idEmpresa'];
        $intIdPersonaRol   = $arrayDatosEstado['idPersonaRol'];
        $intIdDepartamento = $arrayDatosEstado['idDepartamento'];
        // Obtendremos los productos adicionales manuales que se deben cancelar
        $arrayProducAdicioManuales = array();
        $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                    'Productos adicionales manuales para inactivar','','','',
                                    '','',$intIdEmpresa);
        if (is_array($arrayParamValores) && !empty($arrayParamValores))
        {
            $arrayProducAdicioManuales = $this->serviceUtil->obtenerValoresParametro($arrayParamValores);
        }
        // Obtenemos los estados permitidos para esos productos manuales
        $arrayEstadoAdicioManuales = array();
        $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                    'Estados permitidos para cancelar productos manuales',
                                    '','','','','',$intIdEmpresa);
        if (is_array($arrayParamValores) && !empty($arrayParamValores))
        {
            $arrayEstadoAdicioManuales = $this->serviceUtil->obtenerValoresParametro($arrayParamValores);
        }

        // Obtenemos los tipos de solicitudes permitidos para esos productos manuales
        $arrayTiposSolicitudes = array();
        $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                    'Solicitudes anexas a los servicios adicionales manuales',
                                    '','','','','',$intIdEmpresa);
        if (is_array($arrayParamValores) && !empty($arrayParamValores))
        {
            $arrayTiposSolicitudes = $this->serviceUtil->obtenerValoresParametro($arrayParamValores);
        }
        
        // Obtener todos los servicios de un punto
        $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findServiciosByPuntoAndEstado($intIdPunto, null, null);
        
        foreach ($arrayServiciosPunto['registros'] as $objServicio)
        {
            if ($intIdServicio == $objServicio->getId())
            {
                $strEstadoActual = $arrayDatosEstado['estadoActual'];
            }
            else
            {
                $strEstadoActual = $objServicio->getEstado();
            }
            $strProducto = $objServicio->getProductoId();
            if (!empty($strProducto) &&
                in_array($strProducto->getId(), $arrayProducAdicioManuales) &&
                in_array($objServicio->getEstado(), $arrayEstadoAdicioManuales))
            {
                $entityServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                        ->findOneById($objServicio->getId());
                // Actualizamos el estado del servicio
                $entityServicio->setEstado($strEstado);
                $this->emComercial->persist($entityServicio);
                $this->emComercial->flush();

                // Guardamos la bitacora en el historial
                $entityServicioHist = new InfoServicioHistorial();
                $entityServicioHist->setServicioId($entityServicio);
                $entityServicioHist->setIpCreacion($strIpCreacion);
                $entityServicioHist->setFeCreacion(new \DateTime('now'));
                $entityServicioHist->setUsrCreacion($strUsuario);
                $entityServicioHist->setEstado($strEstado);
                $entityServicioHist->setObservacion($strObservacion);
                $this->emComercial->persist($entityServicioHist);
                $this->emComercial->flush();
            }
            if (in_array($strEstadoActual, $arrayEstadoAdicioManuales))
            {
                // Actualizamos los estados de los detalles de la solicitud seleccionada
                $arrayDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                        ->findByParameters(array('servicioId' => $objServicio->getId()));
                // Obtendremos los estados para cancelar las tareas de las solicitudes
                $arrayEstadosTareas = array();
                $arrayParamValores = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->get('PRODUCTOS ADICIONALES MANUALES','COMERCIAL','',
                                            'Estados permitidos para cancelar productos manuales','','','','',
                                            '',$intIdEmpresa);
                if (is_array($arrayParamValores) && !empty($arrayParamValores))
                {
                    $arrayEstadosTareas = $this->serviceUtil->obtenerValoresParametro($arrayParamValores);
                }
                foreach($arrayDetSolicitud as $objSolicitud)
                {
                    $objDetSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findOneById($objSolicitud['id']);
                    $intIdTipoSolicitud = $objDetSolicitud->getTipoSolicitudId()->getId();
                    if (in_array($intIdTipoSolicitud, $arrayTiposSolicitudes) &&
                        in_array($objDetSolicitud->getEstado(), $arrayEstadoAdicioManuales))
                    {
                        // Cerramos las tareas pendientes de la solicitud
                        if (in_array($objDetSolicitud->getEstado(), $arrayEstadosTareas))
                        {
                            $strObsTarea = $strEstado." la tarea por accion simultanea con servicio de internet.";
                            $arrayDatosCancelar = array ('objSolicitud'   => $objDetSolicitud,
                                                        'strObservacion'    => $strObsTarea,
                                                        'strEstadoTarea'    => $strEstado,
                                                        'strUsuCreacion'    => $strUsuario,
                                                        'strIpCreacion'     => $strIpCreacion,
                                                        'intIdPersonaRol'   => $intIdPersonaRol,
                                                        'intIdDepartamento' => $intIdDepartamento,
                                                        'intCodEmpresa'     => $intIdEmpresa);
                            $this->serviceInfoServicio->cancelarTareaSolicitud($arrayDatosCancelar);
                        }
                        // Actualizamos el estado de la solicitud
                        $objDetSolicitud->setEstado($strEstado);
                        $this->emComercial->persist($objDetSolicitud);
                        $this->emComercial->flush();
                        // Guardamos la bitacora del historial de la solicitud
                        $objDetalleSolHist= new InfoDetalleSolHist();
                        $objDetalleSolHist->setDetalleSolicitudId($objDetSolicitud);
                        $objDetalleSolHist->setObservacion($strObservacion);
                        $objDetalleSolHist->setIpCreacion($strIpCreacion);
                        $objDetalleSolHist->setFeCreacion(new \DateTime('now'));
                        $objDetalleSolHist->setUsrCreacion($strUsuario);
                        $objDetalleSolHist->setEstado($strEstado);
                        $this->emComercial->persist($objDetalleSolHist);
                        $this->emComercial->flush();
                    }
                }
            }
        }
    }

    /**
     * Funcion que permite realizar la actualizacion del estado de un servicios adicional automatico
     * parametrizado desde un estado X a un estado Y cuando el servicio sale de un estado pausado 
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.0 29-08-2021 - Version inicial
     * 
     * @param array $arrayDatosParametros - Recibe los datos del servicio y sus estados
     * 
    */
    public function cambioEstadosProdAdicionalesAut($arrayDatosParametros)
    {
        $objServicio     = $arrayDatosParametros['objServicio'];
        $strEstActual    = $arrayDatosParametros['strEstActual'];
        $strEstNuevo     = $arrayDatosParametros['strEstNuevo'];
        $strObservacion  = $arrayDatosParametros['strObservacion'];
        $intCodEmpresa   = $arrayDatosParametros['intCodEmpresa'];
        $strIpCreacion   = $arrayDatosParametros['strIpCreacion'];
        $strUserCreacion = $arrayDatosParametros['strUserCreacion'];

        $objPlanServicio = $objServicio->getPlanId();
        $objProdServicio = $objServicio->getProductoId();
        if (!empty($objPlanServicio) && empty($objProdServicio))
        {
            // Obtenemos los productos adicionales permitidos
            $arrayListadoServicios = array();
            $arrayListadoServicios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->get('PRODUCTOS ADICIONALES AUTOMATICOS','COMERCIAL','',
                                                    'Lista de productos adicionales automaticos',
                                                    '','','','','',$intCodEmpresa);
            // Obtenemos los servicios del punto
            $intIdPunto = $objServicio->getPuntoId()->getid();
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                    ->findServiciosByPuntoAndEstado($intIdPunto,null,null);
            foreach($arrayServiciosPunto['registros'] as $objServicioPunto)
            {
                $objProducto = $objServicioPunto->getProductoId();
                if (!empty($objProducto) && $objServicioPunto->getEstado() == $strEstActual)
                {
                    foreach($arrayListadoServicios as $objListado)
                    {
                        if ($objProducto->getId() == $objListado['valor1'])
                        {
                            $objServicioPunto->setEstado($strEstNuevo);
                            $this->emComercial->persist($objServicioPunto);

                            $entityServHistorial = new InfoServicioHistorial();
                            $entityServHistorial->setServicioId($objServicioPunto);
                            $entityServHistorial->setObservacion($strObservacion);
                            $entityServHistorial->setEstado($strEstNuevo);
                            $entityServHistorial->setUsrCreacion($strUserCreacion);
                            $entityServHistorial->setFeCreacion(new \DateTime('now'));
                            $entityServHistorial->setIpCreacion($strIpCreacion);
                            $this->emComercial->persist($entityServHistorial);
                            $this->emComercial->flush();
                        }
                    }
                }
            }
        }
    }

}
