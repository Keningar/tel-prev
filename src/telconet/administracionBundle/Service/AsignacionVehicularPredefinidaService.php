<?php

namespace telconet\administracionBundle\Service;
use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleSolCaract;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;

class AsignacionVehicularPredefinidaService
{
    const TIPO_ELEMENTO_VEHICULO                            =  'VEHICULO';

    const DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA            = 'SOLICITUD ASIGNACION VEHICULAR PREDEFINIDA';
    const NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA            = 'ZONA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA           = 'TAREA_PREDEFINIDA_ASIGNACION_VEHICULAR';
    const NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO    = 'DEPARTAMENTO_PREDEFINIDO_ASIGNACION_VEHICULAR';

    
    const DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED          = 'ASIGNACION_VEHICULAR_ID_SOL_PREDEF_CUADRILLA';
    const DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED  = 'ASIGNACION_PROVISIONAL_ID_SOLICITUD_PREDEF';

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    private $emSoporte;
    private $emGeneral;
    private $emInfraestructura;
    private $session;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container                        = $container;
        $this->em                               = $container->get('doctrine.orm.telconet_entity_manager');     
        $this->emSoporte                        = $container->get('doctrine.orm.telconet_soporte_entity_manager');
        $this->emGeneral                        = $container->get('doctrine.orm.telconet_general_entity_manager');     
        $this->emInfraestructura                = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');        
        $this->session                          = $container->get('session');
    }

    /**
     * 
     * Documentación para el método 'crearAsignacionVehicularPredefinida'.
     *
     * Creación de una nueva asignación Vehicular Predefinida 
     *
     * @return Response 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-04-2016
     * 
     * @author Modificado: Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 23-08-2016 Se realizan ajustes para guardar los horarios de la asignación 
     * 
     */ 
    public function crearAsignacionVehicularPredefinida($arrayParametros)
    {
        $objRequest         = $this->container->get('request');
        $strUserSession     = $this->session->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMensaje                         = '';

        $datetimeActual     = new \DateTime('now');

        $strEstadoActivo    = 'Activo';

        $intIdElementoVehiculo  = $arrayParametros["intIdElementoVehiculo"];
        $objElementoVehiculo    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                    ->findOneBy( array('id' => $intIdElementoVehiculo, 'estado' => $strEstadoActivo) );

        $strTipoAsignacion                  = $arrayParametros["strTipoAsignacion"];

        $intIdZonaPredefinida               = $arrayParametros["intIdZonaPredefinida"];
        $intIdTareaPredefinida              = $arrayParametros["intIdTareaPredefinida"];

        $intIdDepartamentoPredefinido       = $arrayParametros["intIdDepartamentoPredefinido"];
        
        $strHoraDesdeAsignacionPredefinida  = $arrayParametros['strHoraDesdeAsignacionPredefinida'];
        $strHoraHastaAsignacionPredefinida  = $arrayParametros['strHoraHastaAsignacionPredefinida'];
        
        $boolActualizarDetallesSolVehiculos = $arrayParametros['boolActualizarDetallesSolVehiculos'];

        $intIdPerChoferPredefinido          = $arrayParametros["intIdPerChoferPredefinido"];
        $objPerChoferPredefinido            = $this->em->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($intIdPerChoferPredefinido);
        $objPersonaChoferPredefinido        = $objPerChoferPredefinido->getPersonaId();

        $strObservacionAsignacionPredefinida    = "Se realiza asignaci&oacute;n vehicular predefinida:<br/>";

        $strMensajeObservacionChoferPredefinido         = "";
        $strMensajeObservacionDepartamentoPredefinido   = "";
        $strMensajeObservacionZonaTareaPredefinida      = "";

        if($objElementoVehiculo && $objPerChoferPredefinido)
        {
            $this->emInfraestructura->getConnection()->beginTransaction();
            $this->emSoporte->getConnection()->beginTransaction();
            $this->em->getConnection()->beginTransaction();
            try
            {
                $strMensajeObservacionChoferPredefinido    .= "Datos del Chofer:<br/>";
                $strMensajeObservacionChoferPredefinido    .= "C&eacute;dula: ".$objPersonaChoferPredefinido->getIdentificacionCliente()."<br/>";
                $strMensajeObservacionChoferPredefinido    .= "Nombres: ".$objPersonaChoferPredefinido->getNombres()."<br/>";
                $strMensajeObservacionChoferPredefinido    .= "Apellidos: ".$objPersonaChoferPredefinido->getApellidos()."<br/>";


                $objTipoSolicitud = $this->em->getRepository('schemaBundle:AdmiTipoSolicitud')
                                        ->findOneByDescripcionSolicitud(self::DESCRIPCION_TIPO_SOLICITUD_PREDEFINIDA);

                $objInfoDetalleSolicitud = new InfoDetalleSolicitud();
                $objInfoDetalleSolicitud->setElementoId($intIdElementoVehiculo);
                $objInfoDetalleSolicitud->setEstado($strEstadoActivo);
                $objInfoDetalleSolicitud->setTipoSolicitudId($objTipoSolicitud);
                $objInfoDetalleSolicitud->setFeCreacion($datetimeActual);
                $objInfoDetalleSolicitud->setUsrCreacion($strUserSession);
                $this->em->persist($objInfoDetalleSolicitud);
                $this->em->flush();

                //Se crea un info detalle para luego poder asignar el chofer predefinido
                $objInfoDetalle = new InfoDetalle();
                $objInfoDetalle->setDetalleSolicitudId($objInfoDetalleSolicitud->getId());
                $objInfoDetalle->setFeCreacion($datetimeActual);
                $objInfoDetalle->setUsrCreacion($strUserSession);
                $objInfoDetalle->setIpCreacion($strIpUserSession);
                $objInfoDetalle->setPesoPresupuestado(0);
                $objInfoDetalle->setValorPresupuestado(0);
                $this->emSoporte->persist($objInfoDetalle);
                $this->emSoporte->flush();

                //Se hace la asignacion del chofer predefinido
                $objInfoDetalleAsignacion = new InfoDetalleAsignacion();

                $objInfoDetalleAsignacion->setDetalleId($objInfoDetalle);
                $objInfoDetalleAsignacion->setFeCreacion($datetimeActual);
                $objInfoDetalleAsignacion->setUsrCreacion($strUserSession);
                $objInfoDetalleAsignacion->setIpCreacion($strIpUserSession);
                $objInfoDetalleAsignacion->setTipoAsignado("EMPLEADO");
                $objInfoDetalleAsignacion->setAsignadoId($objPerChoferPredefinido->getDepartamentoId());

                $objDepartamentoPerChofer=$this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                                    ->find($objPerChoferPredefinido->getDepartamentoId());

                $objInfoDetalleAsignacion->setAsignadoNombre($objDepartamentoPerChofer->getNombreDepartamento());
                $objInfoDetalleAsignacion->setRefAsignadoId($objPersonaChoferPredefinido->getId());
                $objInfoDetalleAsignacion->setRefAsignadoNombre($objPersonaChoferPredefinido->getNombres()
                                                                ." ".$objPersonaChoferPredefinido->getApellidos());
                $objInfoDetalleAsignacion->setPersonaEmpresaRolId($intIdPerChoferPredefinido);
                $this->emSoporte->persist($objInfoDetalleAsignacion);
                $this->emSoporte->flush();

                if($strTipoAsignacion=='ZONA')
                {
                    //Se crea un Info Detalle Solicitud Carcteristica para la zona predefinida
                    $objCaracteristicaZonaPredefinida = $this->em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);

                    $objInfoDetalleSolCaractZona = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractZona->setCaracteristicaId($objCaracteristicaZonaPredefinida);
                    $objInfoDetalleSolCaractZona->setValor($intIdZonaPredefinida);
                    $objInfoDetalleSolCaractZona->setDetalleSolicitudId($objInfoDetalleSolicitud);
                    $objInfoDetalleSolCaractZona->setEstado($strEstadoActivo);
                    $objInfoDetalleSolCaractZona->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolCaractZona->setUsrCreacion($strUserSession);
                    $this->em->persist($objInfoDetalleSolCaractZona);
                    $this->em->flush();

                    $objZona=$this->emGeneral->getRepository('schemaBundle:AdmiZona')->find($intIdZonaPredefinida);
                    $strMensajeObservacionZonaTareaPredefinida.="Zona Predefinida:".$objZona->getNombreZona()."<br/>";
                }
                else if($strTipoAsignacion=='TAREA')
                {
                    //Se crea un Info Detalle Solicitud Carcteristica para la tarea predefinida
                    $objCaracteristicaTareaPredefinida = $this->em->getRepository('schemaBundle:AdmiCaracteristica')
                                            ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);

                    $objInfoDetalleSolCaractTarea = new InfoDetalleSolCaract();
                    $objInfoDetalleSolCaractTarea->setCaracteristicaId($objCaracteristicaTareaPredefinida);
                    $objInfoDetalleSolCaractTarea->setValor($intIdTareaPredefinida);
                    $objInfoDetalleSolCaractTarea->setDetalleSolicitudId($objInfoDetalleSolicitud);
                    $objInfoDetalleSolCaractTarea->setEstado($strEstadoActivo);
                    $objInfoDetalleSolCaractTarea->setFeCreacion($datetimeActual);
                    $objInfoDetalleSolCaractTarea->setUsrCreacion($strUserSession);
                    $this->em->persist($objInfoDetalleSolCaractTarea);
                    $this->em->flush();

                    $objTarea=$this->emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTareaPredefinida);
                    $strMensajeObservacionZonaTareaPredefinida.="Tarea Predefinida:".$objTarea->getNombreTarea()."<br/>";
                }



                //Se crea un Info Detalle Solicitud Carcteristica para el departamento predefinido
                $objCaracteristicaDepartamentoPredefinido = $this->em->getRepository('schemaBundle:AdmiCaracteristica')
                                        ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);
                $objInfoDetalleSolCaractDepartamento = new InfoDetalleSolCaract();
                $objInfoDetalleSolCaractDepartamento->setCaracteristicaId($objCaracteristicaDepartamentoPredefinido);
                $objInfoDetalleSolCaractDepartamento->setValor($intIdDepartamentoPredefinido);
                $objInfoDetalleSolCaractDepartamento->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolCaractDepartamento->setEstado($strEstadoActivo);
                $objInfoDetalleSolCaractDepartamento->setFeCreacion($datetimeActual);
                $objInfoDetalleSolCaractDepartamento->setUsrCreacion($strUserSession);
                $this->em->persist($objInfoDetalleSolCaractDepartamento);
                $this->em->flush();

                $objDepartamento=$this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')->find($intIdDepartamentoPredefinido);
                $strMensajeObservacionDepartamentoPredefinido.="Departamento Predefinido:".$objDepartamento->getNombreDepartamento()."<br/>";

                /*Se crea un Info Detalle Solicitud Historial 'Activo' con la fecha en que se realiza 
                 *la asignación vehicular predefinida y las horas de inicio en FeIniPlan y la hora fin 
                 *en FeFinPlan
                 */
                $strMensajeObservacionFinal = $strObservacionAsignacionPredefinida.$strMensajeObservacionChoferPredefinido;
                $strMensajeObservacionFinal.= $strMensajeObservacionZonaTareaPredefinida.$strMensajeObservacionDepartamentoPredefinido;
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setEstado($strEstadoActivo);
                $objInfoDetalleSolHist->setFeCreacion($datetimeActual);
                $objInfoDetalleSolHist->setUsrCreacion($strUserSession);
                $objInfoDetalleSolHist->setIpCreacion($strIpUserSession);
                
                list($horaDesdeAsignacionPredefinida,$minutosDesdeAsignacionPredefinida)=explode(':',$strHoraDesdeAsignacionPredefinida);
                $datetimeDesdeAsignacionPredefinida     = new \DateTime();
                $datetimeDesdeAsignacionPredefinida->setTime($horaDesdeAsignacionPredefinida, $minutosDesdeAsignacionPredefinida, '00');
                
                list($horaHastaAsignacionPredefinida,$minutosHastaAsignacionPredefinida)=explode(':',$strHoraHastaAsignacionPredefinida);
                $datetimeHastaAsignacionPredefinida     = new \DateTime();
                $datetimeHastaAsignacionPredefinida->setTime($horaHastaAsignacionPredefinida, $minutosHastaAsignacionPredefinida, '00');
                
                $objInfoDetalleSolHist->setFeIniPlan($datetimeDesdeAsignacionPredefinida);
                $objInfoDetalleSolHist->setFeFinPlan($datetimeHastaAsignacionPredefinida);
                
                $objInfoDetalleSolHist->setObservacion($strMensajeObservacionFinal);
                $this->em->persist($objInfoDetalleSolHist);
                $this->em->flush();

                $objInfoHistorialChoferPredefinido   = new InfoHistorialElemento();
                $objInfoHistorialChoferPredefinido->setElementoId($objElementoVehiculo);
                $objInfoHistorialChoferPredefinido->setObservacion($strMensajeObservacionFinal);
                $objInfoHistorialChoferPredefinido->setFeCreacion($datetimeActual);
                $objInfoHistorialChoferPredefinido->setUsrCreacion($strUserSession);
                $objInfoHistorialChoferPredefinido->setIpCreacion($strIpUserSession);
                $objInfoHistorialChoferPredefinido->setEstadoElemento($strEstadoActivo);
                $this->emInfraestructura->persist($objInfoHistorialChoferPredefinido);
                $this->emInfraestructura->flush();
                
                /*Actualizando los detalles de los vehículos con los id's de las solicitudes predefinidas asignadas*/
                if($boolActualizarDetallesSolVehiculos)
                {
                    $idDetalleSolicitudNuevo  = $objInfoDetalleSolicitud->getId();
                    $objsDetallesSolVehiculos = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                     ->findBy(array("detalleNombre"=>self::DETALLE_ASIGNACION_VEHICULAR_ID_SOL_PRED,
                                                                    "detalleValor"=> $arrayParametros["idSolicitudPredefinidaAnterior"],
                                                                    "estado"      => 'Activo'));
                    
                    foreach($objsDetallesSolVehiculos as $obDetalleSolVehiculo)
                    {
                        $obDetalleSolVehiculo->setDetalleValor($idDetalleSolicitudNuevo);
                        $this->emInfraestructura->persist($obDetalleSolVehiculo);
                        $this->emInfraestructura->flush();
                    }
                    $objsDetallesSolPredefinidasProv = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                            ->findBy(array("detalleNombre"=>self::DETALLE_ASIGNACION_PROVISIONAL_ID_SOLICITUD_PRED,
                                                                    "detalleValor"=> $arrayParametros["idSolicitudPredefinidaAnterior"],
                                                                    "estado"      => 'Activo'));
                    
                    foreach($objsDetallesSolPredefinidasProv as $objDetalleSolPredefinidaProv)
                    {
                        $objDetalleSolPredefinidaProv->setDetalleValor($idDetalleSolicitudNuevo);
                        $this->emInfraestructura->persist($objDetalleSolPredefinidaProv);
                        $this->emInfraestructura->flush();
                    }
                }

                $this->emInfraestructura->getConnection()->commit();
                $this->emInfraestructura->getConnection()->close();

                $this->emSoporte->getConnection()->commit();
                $this->emSoporte->getConnection()->close();

                $this->em->getConnection()->commit();
                $this->em->getConnection()->close();

                $strMensaje = 'OK';

            }
            catch (Exception $ex) 
            {
                error_log($ex->getMessage());

                $strMensaje = 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

                $this->emInfraestructura->getConnection()->rollback();
                $this->emInfraestructura->getConnection()->close();

                $this->emSoporte->getConnection()->rollback();
                $this->emSoporte->getConnection()->close();

                $this->em->getConnection()->rollback();
                $this->em->getConnection()->close();
            }
        }
        return $strMensaje;
    }


    public function eliminarAsignacionVehicularPredefinida($intIdDetalleSolicitud,$idMotivoEliminacion)
    {
        $strEstadoActivo        = 'Activo';
        $strEstadoFinalizado    ='Finalizado';


        $objRequest         = $this->container->get('request');
        $strUserSession     = $this->session->get('user');
        $strIpUserSession   = $objRequest->getClientIp();
        $strMsg             = '';

        $datetimeActual     = new \DateTime('now');

        $objDetalleSolicitud    = $this->em->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdDetalleSolicitud);

        if($objDetalleSolicitud)
        {
            $this->em->getConnection()->beginTransaction();
            try
            {

                //Se busca la zona predefinida y se la cambia a estado Finalizado
                $objCaracteristicaZonaPredefinida = $this->em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_ZONA_PREDEFINIDA);


                $objDetalleSolCaracteristicaZona = $this->em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(
                                                array(
                                                    "detalleSolicitudId"=> $objDetalleSolicitud,
                                                    "caracteristicaId"  => $objCaracteristicaZonaPredefinida,
                                                    "estado"            => $strEstadoActivo
                                                    )
                                                );
                if($objDetalleSolCaracteristicaZona)
                {
                    $objDetalleSolCaracteristicaZona->setEstado('Finalizada');
                    $this->em->persist($objDetalleSolCaracteristicaZona);
                    $this->em->flush();
                }

                //Se busca la tarea predefinida y se la cambia a estado Finalizado
                $objCaracteristicaTareaPredefinida = $this->em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_TAREA_PREDEFINIDA);


                $objDetalleSolCaracteristicaTarea = $this->em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(
                                                array(
                                                    "detalleSolicitudId"=> $objDetalleSolicitud,
                                                    "caracteristicaId"  => $objCaracteristicaTareaPredefinida,
                                                    "estado"            => $strEstadoActivo
                                                    )
                                                );
                if($objDetalleSolCaracteristicaTarea)
                {
                    $objDetalleSolCaracteristicaTarea->setEstado('Finalizada');
                    $this->em->persist($objDetalleSolCaracteristicaTarea);
                    $this->em->flush();
                }


                $objCaracteristicaDepartamentoPredefinido = $this->em->getRepository('schemaBundle:AdmiCaracteristica')
                                                ->findOneByDescripcionCaracteristica(self::NOMBRE_CARACTERISTICA_DEPARTAMENTO_PREDEFINIDO);


                $objDetalleSolCaracteristicaDepartamento = $this->em->getRepository('schemaBundle:InfoDetalleSolCaract')
                                            ->findOneBy(
                                                array(
                                                    "detalleSolicitudId"=> $objDetalleSolicitud,
                                                    "caracteristicaId"  => $objCaracteristicaDepartamentoPredefinido,
                                                    "estado"            => $strEstadoActivo
                                                    )
                                                );
                if($objDetalleSolCaracteristicaDepartamento)
                {
                    $objDetalleSolCaracteristicaDepartamento->setEstado('Finalizada');
                    $this->em->persist($objDetalleSolCaracteristicaDepartamento);
                    $this->em->flush();
                }

                //Buscar el Info Detalle Solicitud Historial que se encuentra con el estado Activo
                $objDetalleSolHistActivo = $this->em->getRepository('schemaBundle:InfoDetalleSolHist')
                                            ->findOneBy(
                                                array(
                                                    "detalleSolicitudId"=> $objDetalleSolicitud,
                                                    "estado"            => $strEstadoActivo
                                                    )
                                                );

                //Se obtiene la fecha de Inicio
                $timestampFechaDesde=$objDetalleSolHistActivo->getFeIniPlan();

                /*Crear un Info Detalle Solicitud Historial con el estado Finalizado y con las fechas respectivas
                de la asignacion vehicular predefinida*/
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objDetalleSolicitud);
                $objInfoDetalleSolHist->setEstado($strEstadoFinalizado);
                $objInfoDetalleSolHist->setFeCreacion($datetimeActual);
                $objInfoDetalleSolHist->setUsrCreacion($strUserSession);
                $objInfoDetalleSolHist->setIpCreacion($strIpUserSession);
                $objInfoDetalleSolHist->setFeIniPlan($timestampFechaDesde);
                $objInfoDetalleSolHist->setFeFinPlan($datetimeActual);
                $objInfoDetalleSolHist->setMotivoId($idMotivoEliminacion);
                $this->em->persist($objInfoDetalleSolHist);
                $this->em->flush();

                //Cambio el estado de Activo a Finalizado
                $objDetalleSolicitud->setEstado($strEstadoFinalizado);
                $this->em->persist($objInfoDetalleSolHist);
                $this->em->flush();

                $this->em->getConnection()->commit();
                $this->em->getConnection()->close();

                $strMsg .= 'OK';
            }
            catch (Exception $ex) 
            {
                error_log($ex->getMessage());
                $strMsg .= 'Hubo un problema de base de datos, por favor contactar con el departamento de sistemas';

                $this->em->getConnection()->rollback();
                $this->em->getConnection()->close();
            }
        }
        return $strMsg;
    }
}
