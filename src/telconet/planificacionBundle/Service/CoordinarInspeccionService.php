<?php

namespace telconet\planificacionBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use telconet\soporteBundle\Service\SoporteService;
use telconet\schemaBundle\Entity\InfoPunto;
use telconet\schemaBundle\Entity\AdmiParametroCab;
use telconet\schemaBundle\Entity\AdmiParametroDet;
use telconet\schemaBundle\Entity\InfoDetalleHistorial;
use telconet\schemaBundle\Entity\InfoTareaSeguimiento;
use telconet\schemaBundle\Entity\InfoDetalleSolPlanif;
use telconet\schemaBundle\Entity\InfoDetalleSolPlanifHist;
use telconet\schemaBundle\Entity\InfoCriterioAfectado;
use telconet\schemaBundle\Entity\InfoParteAfectada;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;

/**
 * Clase CoordinarInspeccionService
 *
 * Clase que se encarga de realizar acciones de submenu Coordinacion
 *
 * @author Andrés Montero H <amontero@telconet.ec>
 * @version 1.0 19-01-2022
 * 
 * 
 */
class CoordinarInspeccionService
{

    private $objContainer;
    private $objEntManGeneral;
    private $objEntManComercial;
    private $objEntManComunicacion;
    private $objEntManSoporte;
    private $objTemplating;
    private $objMailer;
    private $objMailerSend;
    private $objServicePlanificacion;
    private $objEnvioPlantilla;
    private $objServiceEnvioPlantilla;
    private $objServiceEnvioSms;
    private $objServiceSoporte;
    private $objServiceUtil;
    private $objServiceGestionarInsp;
    
    /**
     *  Metodo utilizado para setear dependencia
     * 
     * @author Andrés Montero H <amontero@telconet.ec>
     * @version 1.0 19-01-2022
     * 
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer) 
    {
        $this->objContainer                 = $objContainer;
        $this->objEntManGeneral             = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->objEntManComercial           = $objContainer->get('doctrine')->getManager('telconet');
        $this->objEntManSoporte             = $objContainer->get('doctrine')->getManager('telconet_soporte');
        $this->objMailer                    = $objContainer->get('mailer');
        $this->objMailerSend                = $objContainer->getParameter('mailer_send');    
        $this->objServicePlanificacion      = $objContainer->get('planificacion.Planificar');
        $this->objEnvioPlantilla            = $objContainer->get('soporte.EnvioPlantilla');
        $this->objServiceUtil               = $objContainer->get('schema.Util');
        $this->objServiceEnvioSms           = $objContainer->get('comunicaciones.SMS');
        $this->objServiceEnvioPlantilla     = $objContainer->get('soporte.EnvioPlantilla');
        $this->serviceCoordinar             = $objContainer->get('planificacion.CoordinarInspeccion');
        $this->objServiceGestionarInsp      = $objContainer->get('planificacion.GestionarInspeccion');
        $this->objServiceSoporte            = $objContainer->get('soporte.SoporteService');
        $this->objTemplating                = $objContainer->get('templating');
        $this->objEntManComunicacion        = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');

    }


    /**
     * Función que permite crear registro de una planificación de inspección
     * @param Array arrayParametros[
     *   idDetalleSolicitud => id de la solicitud
     *   idDetalleSolPlanif => id de la planificación de inspección (referencia a infoDetalleSolicitudPlanif)
     *   idAsignado         => id del asignado (cuadrilla o empleado)
     *   tipoAsignado       => id del tipo de asignado
     *   fechaIniPlan       => fecha de la planificación
     *   fechaFinPlan       => fecha fin de la planificación
     *   idTarea            => id de la tarea (AdmiTarea)
     *   idMotivo           => id del motivo (AdmiMotivo)
     *   estado             => estado de la planificación de inspección
     *   usrCreacion        => usuario de creación
     *   ipCreacion         => ip de creación
     *   observacion        => observación
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 02-12-2021
     * @return OCI_B_CURSOR
     */
    public function crearPlanificacionInspeccion($arrayParametros)
    {
        $objEmComercial          = $this->objEntManComercial;
        $arrayRespuesta          = array();
        $intIdDetalleSolicitud   = $arrayParametros['idDetalleSolicitud'];
        $intIdDetalleSolPlanif   = $arrayParametros['idDetalleSolPlanif'];
        $intIdAsignado           = $arrayParametros['idAsignado'];
        $strTipoAsignado         = $arrayParametros['tipoAsignado'];
        $objFechaIniPlan         = $arrayParametros['fechaIniPlan'];
        $objFechaFinPlan         = $arrayParametros['fechaFinPlan'];
        $intIdTarea              = $arrayParametros['idTarea'];
        $intIdMotivo             = $arrayParametros['idMotivo'];
        $strEstado               = $arrayParametros['estado'];
        $strUsrCreacion          = $arrayParametros['usrCreacion'];
        $strIpCreacion           = $arrayParametros['ipCreacion'];
        $strObservacion          = $arrayParametros['observacion'];
        $objInfoDetalleSolicitud = null;
        $objInfoDetalleSolPlanif = null;

        if(!empty($intIdDetalleSolicitud))
        {
            $objInfoDetalleSolicitud  = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                       ->findOneById($intIdDetalleSolicitud);
        }
        if(!empty($intIdDetalleSolPlanif))
        {
            $objInfoDetalleSolPlanif  = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                    ->findOneById($intIdDetalleSolPlanif);
        }

        $objEmComercial->getConnection()->beginTransaction();

        try
        {
            if (!is_object($objInfoDetalleSolPlanif))
            {
                //GUARDAR INFO DETALLE SOLICICITUD PLANIFICACION
                $objInfoDetalleSolPlanif = new InfoDetalleSolPlanif();
                $objInfoDetalleSolPlanif->setDetalleSolicitudId($objInfoDetalleSolicitud);
                if (!empty($intIdAsignado))
                {
                    $objInfoDetalleSolPlanif->setAsignadoId($intIdAsignado);
                }
                if (!empty($strTipoAsignado))
                {
                    $objInfoDetalleSolPlanif->setTipoAsignado($strTipoAsignado);
                }
                if (!empty($intIdTarea))
                {
                    $objInfoDetalleSolPlanif->setTareaId($intIdTarea);
                }
                if (!empty($intIdMotivo))
                {
                    $objInfoDetalleSolPlanif->setMotivoId($intIdMotivo);
                }
                if (is_object($objFechaIniPlan))
                {
                    $objInfoDetalleSolPlanif->setFeIniPlan($objFechaIniPlan);
                }
                if (is_object($objFechaFinPlan))
                {
                    $objInfoDetalleSolPlanif->setFeFinPlan($objFechaFinPlan);
                }
                $objInfoDetalleSolPlanif->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolPlanif->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolPlanif->setIpCreacion($strIpCreacion);
                $objInfoDetalleSolPlanif->setEstado($strEstado);
                $objEmComercial->persist($objInfoDetalleSolPlanif);
                $objEmComercial->flush();
            }
            else
            {
                if (!empty($intIdTarea))
                {
                    $objInfoDetalleSolPlanif->setTareaId($intIdTarea);
                }
                if (!empty($intIdAsignado))
                {
                    $objInfoDetalleSolPlanif->setAsignadoId($intIdAsignado);
                }
                if (!empty($strTipoAsignado))
                {
                    $objInfoDetalleSolPlanif->setTipoAsignado($strTipoAsignado);
                }
                if (!empty($intIdMotivo))
                {
                    $objInfoDetalleSolPlanif->setMotivoId($intIdMotivo);
                }
                if (is_object($objFechaIniPlan))
                {
                    $objInfoDetalleSolPlanif->setFeIniPlan($objFechaIniPlan);
                }
                if (is_object($objFechaFinPlan))
                {
                    $objInfoDetalleSolPlanif->setFeFinPlan($objFechaFinPlan);
                }
                $objInfoDetalleSolPlanif->setEstado($strEstado);
                $objEmComercial->persist($objInfoDetalleSolPlanif);
                $objEmComercial->flush();
            }
            //GUARDAR HISTORIAL DE INFO DETALLE SOLICICITUD PLANIFICACION
            $objInfoDetalleSolPlanifHist = new InfoDetalleSolPlanifHist();
            $objInfoDetalleSolPlanifHist->setDetalleSolPlanifId($objInfoDetalleSolPlanif);
            if (!empty($intIdTarea))
            {
                $objInfoDetalleSolPlanifHist->setTareaId($intIdTarea);
            }
            if (!empty($intIdAsignado))
            {
                $objInfoDetalleSolPlanifHist->setAsignadoId($intIdAsignado);
            }
            if (!empty($strTipoAsignado))
            {
                $objInfoDetalleSolPlanifHist->setTipoAsignado($strTipoAsignado);
            }
            if (!empty($intIdMotivo))
            {
                $objInfoDetalleSolPlanifHist->setMotivoId($intIdMotivo);
            }
            if (is_object($objFechaIniPlan))
            {
                $objInfoDetalleSolPlanifHist->setFeIniPlan($objFechaIniPlan);
            }
            if (is_object($objFechaFinPlan))
            {
                $objInfoDetalleSolPlanifHist->setFeFinPlan($objFechaFinPlan);
            }
            $objInfoDetalleSolPlanifHist->setFeCreacion(new \DateTime('now'));
            $objInfoDetalleSolPlanifHist->setUsrCreacion($strUsrCreacion);
            $objInfoDetalleSolPlanifHist->setIpCreacion($strIpCreacion);
            if (!empty($strObservacion))
            {
                $objInfoDetalleSolPlanifHist->setObservacion($strObservacion);
            }

            $objInfoDetalleSolPlanifHist->setEstado($strEstado);
            $objEmComercial->persist($objInfoDetalleSolPlanifHist);
            $objEmComercial->flush();


            $objEmComercial->getConnection()->commit();
            $arrayRespuesta['status'] = 'ok';
            $arrayRespuesta['mensaje'] = 'Planificación de inspección creada correctamente';
            $arrayRespuesta['objDetalleSolPlanif'] = $objInfoDetalleSolPlanif;
            $arrayRespuesta['objDetalleSolPlanifHist'] = $objInfoDetalleSolPlanifHist;
        }
        catch(\Exception $e)
        {
            $objEmComercial->getConnection()->rollback();
            $strMensajeError = "Error: " . $e->getMessage();
            $arrayRespuesta['status'] = 'Error';
            $arrayRespuesta['mensaje'] = $strMensajeError;
            $arrayRespuesta['objDetalleSolPlanif'] = null;
            $this->objServiceUtil->insertError('TELCOS+',
                                                'GestionarInspeccionService.crearPlanificacionInspeccion',
                                                $strMensajeError,
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }
        return $arrayRespuesta;
    }

    /**
     * Función que permite programar o planificar una inspección
     * @param Array arrayParametros[
     *   usrCreacion          => usuario de creación
     *   ipCreacion           => ip de creación
     *   idOficina            => id de la oficina del usuario en sesión
     *   idDepartamento       => id del departamento del usuario en sesión
     *   intIdPerEmpRolSesion => id persona empresa rol del usuario en sesión
     *   strPrefijoEmpresa    => prefijo de la empresa en sesión
     *   asignados            => grupo de asignados (cuadrillas) para las inspecciones
     *                           'idAsignado*fechaInicio*fechaFin*tipoAsignado*idSolicitud*loginCliente*idSolicitudInspeccion(idDetalleSolPlanif)|'
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 02-12-2021
     * @return OCI_B_CURSOR
     */
    public function programarInspeccion($arrayParametros)
    {
        $objEmComercial          = $this->objEntManComercial;
        $objEmComunicacion       = $this->objEntManComunicacion;
        $objEmSoporte            = $this->objEntManSoporte;
        $objEmGeneral            = $this->objEntManGeneral;
        $objCoordinarService     = $this->serviceCoordinar;
        $objPlanificacionService = $this->objServicePlanificacion;
        $objSoporteService       = $this->objServiceSoporte;
        $arrayRespuesta          = array();
        $strIpCreacion           = $arrayParametros['ipCreacion'];
        $strCodEmpresa           = $arrayParametros['codEmpresa'];
        $intIdOficina            = $arrayParametros['idOficina'];
        $intIdDepartamento       = $arrayParametros['idDepartamento'];
        $strUsrCreacion          = $arrayParametros['usrCreacion'];
        $intPerEmpRolSesion      = $arrayParametros['intIdPerEmpRolSesion'];
        $strPrefijoEmpresa       = $arrayParametros['strPrefijoEmpresa'];
        $objDateFechaHoy         = date("Y/m/d");
        $strObsPlanifInspeccion  = "Se planifica realizar inspección en el cliente.";
        $strAsignados            = $arrayParametros['asignados'];
        $objInfoPunto            = "";
        $strUsrNotificar         = "";
        $strSeProcesoInspecciones= "N";
        $objEmComercial->getConnection()->beginTransaction();
        $objEmComunicacion->getConnection()->beginTransaction();
        $arrayCaracteristicasPlus          = array();
        $intIdComunicacion        = null;
        try
        {
            $arrayAsignadosGen = explode("|",$strAsignados);
            foreach($arrayAsignadosGen as $strAsignadoItem)
            {
                $strNombreCuadirlla      = "";
                $strObservacionTarea     = "";
                $arrayDatosSinPunto = array();
                $arrayAsignadoItem  = explode("*",$strAsignadoItem);

                if (strtoupper($arrayAsignadoItem[4]) == "NUEVO")
                {
                    $intIdAsignado         = $arrayAsignadoItem[0]; /* idPersona */
                    $strFeIniPlan          = $arrayAsignadoItem[2];
                    $strFeFinPlan          = $arrayAsignadoItem[3];
                    $strTipoAsignado       = $arrayAsignadoItem[1];
                    $intIdDetalleSolicitud = $arrayAsignadoItem[5];
                    $strLogin              = $arrayAsignadoItem[6];
                    $intIdDetalleSolPlanif = $arrayAsignadoItem[7];
                    $strObsPlanifInspeccion = $arrayAsignadoItem[8];
                    $arrayTareaProgramacion = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                           ->get(
                                                                 'TAREA_PROGRAMAR_INSPECCION','COMERCIAL','','','','','','','', $strCodEmpresa,''
                                                                );

                    foreach($arrayTareaProgramacion as $arrayTareaProg)
                    {
                        $intIdTarea = $arrayTareaProg['valor1'];
                    }

                    if (empty($intIdTarea))
                    {
                        throw new \Exception("Error al obtener tarea");
                    }

                    $objFeIniPlan          = new \DateTime(date("Y/m/d H:i", strtotime($strFeIniPlan)));
                    $objFeFinPlan          = new \DateTime(date("Y/m/d H:i", strtotime($strFeFinPlan)));

                    $intIdDetalle          = null;

                    $objInfoDetalleSolicitud = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                              ->find($intIdDetalleSolicitud);
                    if (!is_object($objInfoDetalleSolicitud))
                    {
                        throw new \Exception("Error al obtener la solicitud, no existe solicitud:".$intIdDetalleSolicitud);
                    }


                    


                    if (!empty($strLogin))
                    {
                        $arrayInfoPunto = $objEmComercial->getRepository('schemaBundle:InfoPunto')->findByLogin($strLogin);

                        if (!empty($strLogin) && !is_array($arrayInfoPunto))
                        {
                            throw new \Exception("Error al obtener punto, no existe punto con login:".$strLogin);
                        }

                        $objInfoPunto          = $arrayInfoPunto[0];
                        $strUsrNotificar       = $objInfoPunto->getUsrVendedor();
                        $objInfoPersonaCliente = $objInfoPunto->getPersonaEmpresaRolId()->getPersonaId();

                        if(is_object($objInfoPersonaCliente))
                        {
                            $strNombreCliente      = sprintf("%s", $objInfoPersonaCliente);
                            $strObservacionTarea    .= "<b>Informaci&oacute;n del Cliente</b><br/>";
                            $strObservacionTarea    .= "Nombre: " . $strNombreCliente . "<br/>";
                            $strObservacionTarea    .= "Direcci&oacute;n: " . $objInfoPersonaCliente->getDireccionTributaria() . "<br/>";
                        }
                        $strObservacionTarea .= "<br/><b>Informaci&oacute;n del Punto</b><br/>";
                        $strObservacionTarea .= "Nombre: " . $objInfoPunto->getNombrePunto() . "<br/>";

                        $strObservacionTarea       .= "Direcci&oacute;n: " . $objInfoPunto->getDireccion() . "<br/>";
                        $strObservacionTarea       .= "Referencia: " . $objInfoPunto->getDescripcionPunto() . "<br/>";
                        $strObservacionTarea       .= "Latitud: " . $objInfoPunto->getLatitud() . "<br/>";
                        $strObservacionTarea       .= "Longitud: " . $objInfoPunto->getLongitud() . "<br/><br/>";
                        $arrayFormasContactoPunto = $objEmComercial->getRepository('schemaBundle:InfoPuntoFormaContacto')
                                                                   ->findPorEstadoPorPunto($objInfoPunto->getId(), 'Activo', 6, 0);

                        if($arrayFormasContactoPunto['registros'])
                        {
                            $arrayFormasContactoPunto = $arrayFormasContactoPunto['registros'];
                            $strObservacionTarea       .= "Contactos<br/>";
                            foreach($arrayFormasContactoPunto as $objFormaContactoPunto)
                            {
                                $strFormaContactoPunto  = $objFormaContactoPunto->getFormaContactoId()->getDescripcionFormaContacto();
                                $strObservacionTarea   .= $strFormaContactoPunto . ": " .$objFormaContactoPunto->getValor() . "<br/>";
                            }
                        }

                    }
                    else
                    {
                        //Buscar las caracteristicas de la solicitud de inspección para obtener datos del punto
                        $arrayParametrosCaracteristicas['idSolicitud'] = $objInfoDetalleSolicitud->getId();
                        $arrayDatosSinPunto = $this->objServiceGestionarInsp->obtenerCaracteristicas($arrayParametrosCaracteristicas);

                        //SI NO SE OBTUVO EL VENDEDOR SE OBTIENE USR_CREACION DE QUIEN CREA LA SOLICITUD
                        if (empty($arrayDatosSinPunto['strUsrVendedor']))
                        {
                            $arrayDatosSinPunto['strUsrVendedor'] = $objInfoDetalleSolicitud->getUsrCreacion();
                            $strUsrNotificar                      = $objInfoDetalleSolicitud->getUsrCreacion();
                        }
                        else
                        {
                            $strUsrNotificar = $arrayDatosSinPunto['strUsrVendedor'];
                        }
                        $strObservacionTarea .= "<br/><b>Informaci&oacute;n del Cliente</b><br/>";
                        if (!empty($arrayDatosSinPunto['strNombreCliente']))
                        {
                            $strObservacionTarea .= "Nombre: " . $arrayDatosSinPunto['strNombreCliente'] . "<br/>";
                        }

                        $strObservacionTarea .= "Direcci&oacute;n: " .$arrayDatosSinPunto['strCiudad']. $arrayDatosSinPunto['strDireccion'] . "<br/>";
                        $strObservacionTarea .= "Referencia: " . $arrayDatosSinPunto['strNombresContacto'] . "<br/>";
                        $strObservacionTarea .= "Latitud: " . $arrayDatosSinPunto['strLatitud'] . "<br/>";
                        $strObservacionTarea .= "Longitud: " . $arrayDatosSinPunto['strLongitud'] . "<br/><br/>";
                        $strObservacionTarea .=  "Tel&eacute;fono: " .$arrayDatosSinPunto['strTelefonoContacto'] . "<br/>";
                    }

                    $arrayResponsable = array();

                    if (strtolower($strTipoAsignado) == 'empleado' )
                    {
                        $arrayParametrosRol = array();
                        $arrayParametrosRol['intPersonaId'] = $intIdAsignado;
                        $arrayParametrosRol['strCodigoEmpresa'] = $strCodEmpresa;
                        

                        $intInfoPersonaEmpresaRolid = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getPersonaEmpresaRolPorIdPersonaYEmpresa($arrayParametrosRol);

                        $objInfoPersonaEmpresaRol = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->find($intInfoPersonaEmpresaRolid);

                        $arrayResponsable = array(0 => $intIdTarea."@@".strtolower($strTipoAsignado)."@@".
                                        $objInfoPersonaEmpresaRol->getPersonaId()->getId()."@@".$objInfoPersonaEmpresaRol->getId());
                    }
                    elseif (strtolower($strTipoAsignado) == 'cuadrilla' )
                    {
                        $arrayResponsable   = array(0 => $intIdTarea."@@".strtolower($strTipoAsignado)."@@".$intIdAsignado."@@@@");

                        $objCuadrilla       = $objEmComercial->getRepository("schemaBundle:AdmiCuadrilla")->find($intIdAsignado);

                        $strNombreCuadirlla = $objCuadrilla->getNombreCuadrilla();
                    }

                    if (!empty($intIdDetalleSolPlanif))
                    {
                        $objInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')->find($intIdDetalleSolPlanif);
                        $intIdComunicacion       = $objInfoDetalleSolPlanif->getTareaId();
                        $objInfoComunicacion     = $objEmComercial->getRepository('schemaBundle:InfoComunicacion')->findOneById($intIdComunicacion);
                        $intIdDetalle            = $objInfoComunicacion->getDetalleId();
                    }

                    //Crea planificación de inspección con estado Planificada
                    $arrayParametrosSolPlanif['idDetalleSolicitud']   = $intIdDetalleSolicitud;
                    $arrayParametrosSolPlanif['idAsignado']           = $intIdAsignado;
                    $arrayParametrosSolPlanif['tipoAsignado']         = $strTipoAsignado;
                    $arrayParametrosSolPlanif['fechaIniPlan']         = $objFeIniPlan;
                    $arrayParametrosSolPlanif['fechaFinPlan']         = $objFeFinPlan;
                    $arrayParametrosSolPlanif['idDetalleSolPlanif']   = $intIdDetalleSolPlanif;
                    $arrayParametrosSolPlanif['idTarea']              = null;
                    $arrayParametrosSolPlanif['estado']               = 'Planificada';
                    $arrayParametrosSolPlanif['usrCreacion']          = $strUsrCreacion;
                    $arrayParametrosSolPlanif['ipCreacion']           = $strIpCreacion;
                    $arrayParametrosSolPlanif['observacion']          = $strObservacionPlanifInsp;
                    $arrayRespuestaCreaPlanifInsp = $objCoordinarService->crearPlanificacionInspeccion($arrayParametrosSolPlanif);
                    if(strtoupper($arrayRespuestaCreaPlanifInsp['status']) == 'OK')
                    {
                        $objInfoDetalleSolPlanif = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanif'];
                        $objInfoDetalleSolPlanifHist = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanifHist']; 
                    }

                    //Obtener tarea que se asigna a la tarea
                    $objAdmiTarea = $objEmSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTarea);
                    if (!is_object($objAdmiTarea))
                    {
                        throw new \Exception("Error al obtener tarea, no existe tarea:".$intIdTarea);
                    }
                    $arrayParametrosHist["strCodEmpresa"]           = $strCodEmpresa;
                    $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
                    $arrayParametrosHist["strEstadoActual"]         = "Asignada";
                    $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
                    $arrayParametrosHist["strOpcion"]               = "Seguimiento";
                    $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;

                    $arrayParametrosTarea['intIdPerEmpRolSesion']       = $intPerEmpRolSesion;
                    $arrayParametrosTarea['intIdFactibilidad']          = $intIdDetalleSolicitud;
                    $arrayParametrosTarea['intIdPersona']               = 0;
                    $arrayParametrosTarea['intIdPersonaEmpresaRol']     = 0;
                    $arrayParametrosTarea['strObservacionServicio']     = "";//confirmar si aqui enviamos observación de la solicitud de inspección
                    $arrayParametrosTarea['arrayParametrosPer']         = array();
                    $arrayParametrosTarea['arrayParametrosHist']        = $arrayParametrosHist;
                    $arrayParametrosTarea['intPersonaEmpresaRol']       = 0;
                    $arrayParametrosTarea['intIdEmpresa']               = $strCodEmpresa;
                    $arrayParametrosTarea['strPrefijoEmpresa']          = $strPrefijoEmpresa;
                    $arrayParametrosTarea['strCodEmpresa']              = $strCodEmpresa;
                    $arrayParametrosTarea['strUsrCreacion']             = $strUsrCreacion;
                    $arrayParametrosTarea['strIpCreacion']              = $strIpCreacion;
                    $arrayParametrosTarea['strResponsableTrazabilidad'] = null;
                    $arrayParametrosTarea['id']                         = $intIdDetalleSolicitud;
                    $arrayParametrosTarea['entitytarea']                = $objAdmiTarea;
                    $arrayParametrosTarea['strSolucion']                = null;
                    $arrayParametrosTarea['strDatosTelefonia']          = null;
                    $arrayParametrosTarea['objInfoServicio']            = null;
                    $arrayParametrosTarea['strTipoSolicitud']           = $objInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud();
                    $arrayParametrosTarea['boolGuardo']                 = false;
                    $arrayParametrosTarea['arrayParamRespon']           = $arrayResponsable;
                    $arrayParametrosTarea['objSolicitud']               = $objInfoDetalleSolicitud;
                    $arrayParametrosTarea['strEsHal']                   = 'N';
                    $arrayParametrosTarea['intIdSugerenciaHal']         = null;
                    $arrayParametrosTarea['strAtenderAntes']            = null;
                    $arrayParametrosTarea['boolEsReplanifHal']          = false;
                    $arrayParametrosTarea['intIdDetalleExistente']      = $intIdDetalle;
                    $arrayParametrosTarea['boolRequiereFlujoSim']       = false;
                    $arrayParametrosTarea['idFlujoFactibilidad']        = null;
                    $arrayParametrosTarea['strEsGestionSimultanea']     = null;
                    $arrayParametrosTarea['objInfoPunto']               = $objInfoPunto;
                    $arrayParametrosTarea['objInfoDetalleSolPlanif']    = $objInfoDetalleSolPlanif;
                    $arrayParametrosTarea['arrayDatosSinPunto']         = $arrayDatosSinPunto;
                    $arrayParametrosTarea['strObservacionTecnico']      = $strObsPlanifInspeccion;

                    $arrayResultadoTarea = $objPlanificacionService->crearTareaAsignarPlanificacion($arrayParametrosTarea);

                    $objInfoDetalle      = $arrayResultadoTarea['objInfoDetalle'];
                    $objInfoComunicacion = $arrayResultadoTarea['objInfoComunicacion'];

                    if(is_object($objInfoDetalle) && empty($intIdDetalle))
                    {
                        $strObservacionTarea .= "<br/><b>Datos de Planificaci&oacute;n</b><br/>";
                        $strObservacionTarea .= "Observaci&oacute;n: " . $strObservacionPlanifInsp;
                        $strObservacionTarea .= "<br>Fecha: " . $objFeIniPlan->format('d-m-Y');
                        $strObservacionTarea .= "<br>Hora Inicio: " . $objFeIniPlan->format('h:i A');
                        $strObservacionTarea .= "<br>Hora Fin: " . $objFeFinPlan->format('h:i A')."<br/>";
                        $strObservacionTarea .= "Tarea Asignada a " . $strNombreCuadirlla;

                        //SE INGRESA EL SEGUIMIENTO EN TAREA CON DATOS DE INSPECCION
                        $arrayParametrosHist["intDetalleId"]   = $objInfoDetalle->getId();
                        $arrayParametrosHist["strObservacion"] = $strObservacionTarea;
                        $objSoporteService->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                        //INGRESA PARTE AFECTADA DE LA TAREA
                        if (is_object($objInfoPunto))
                        {
                            $intIdCA = 1;
                            $strOpcion = 'Cliente: ' . $objInfoPunto->getNombrePunto() . ' | OPCION: Punto Cliente';

                            $objInfoCriterioAfectado = new InfoCriterioAfectado();
                            $objInfoCriterioAfectado->setId($intIdCA);
                            $objInfoCriterioAfectado->setDetalleId($objInfoDetalle);
                            $objInfoCriterioAfectado->setCriterio("Clientes");
                            $objInfoCriterioAfectado->setOpcion($strOpcion);
                            $objInfoCriterioAfectado->setUsrCreacion($strUsrCreacion);
                            $objInfoCriterioAfectado->setFeCreacion(new \DateTime('now'));
                            $objInfoCriterioAfectado->setIpCreacion($strIpCreacion);
                            $objEmSoporte->persist($objInfoCriterioAfectado);
                            $objEmSoporte->flush();

                            $entityInfoParteAfectada = new InfoParteAfectada();
                            $entityInfoParteAfectada->setCriterioAfectadoId($objInfoCriterioAfectado->getId());
                            $entityInfoParteAfectada->setDetalleId($objInfoDetalle->getId());
                            $entityInfoParteAfectada->setAfectadoId($objInfoPunto->getId());
                            $entityInfoParteAfectada->setTipoAfectado("Cliente");
                            $entityInfoParteAfectada->setAfectadoNombre($objInfoPunto->getLogin());
                            $entityInfoParteAfectada->setAfectadoDescripcion($objInfoPunto->getNombrePunto());
                            $entityInfoParteAfectada->setFeIniIncidencia($objInfoDetalleSolicitud->getFeCreacion());
                            $entityInfoParteAfectada->setUsrCreacion($strUsrCreacion);
                            $entityInfoParteAfectada->setFeCreacion(new \DateTime('now'));
                            $entityInfoParteAfectada->setIpCreacion($strIpCreacion);

                            $objEmSoporte->persist($entityInfoParteAfectada);
                            $objEmSoporte->flush();
                        }
                        //REPLICA TAREA EN TABLA INFO_TAREA
                        $arrayParametrosInfoTarea['intDetalleId']   = is_object($objInfoDetalle)? $objInfoDetalle->getId():null;
                        $arrayParametrosInfoTarea['strUsrCreacion'] = isset($strUsrCreacion)? $strUsrCreacion:null;
                        $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);
                    }

                    $objAdmiCuadrilla = $objEmComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdAsignado);

                    //------- COMUNICACIONES --- NOTIFICACIONES
                    $strContenidoCorreo = $this->objTemplating->render( 'planificacionBundle:Coordinar:notificacionInspeccion.html.twig', array(
                                                                        'detalleSolicitud'           => $objInfoDetalleSolicitud,
                                                                        'detalleSolicitudPlanif'     => $objInfoDetalleSolPlanif,
                                                                        'infoPunto'                  => $objInfoPunto,
                                                                        'infoNoCliente'              => $arrayDatosSinPunto,
                                                                        'detalleSolicitudPlanifHist' => $objInfoDetalleSolPlanifHist,
                                                                        'motivo'                     => null,
                                                                        'admiCuadrilla'              => $objAdmiCuadrilla));
                    $strAsunto = "";
                    if (is_object($objAdmiCuadrilla))
                    {
                        $strAsunto = "Planificación de Solicitud de Inspección #" . $objInfoDetalleSolicitud->getId(). 
                                 " Cuadrilla: ".$objAdmiCuadrilla->getNombreCuadrilla();
                    }else
                    {
                        $strAsunto = "Planificación de Solicitud de Inspección #" . $objInfoDetalleSolicitud->getId(). 
                                 " Cuadrilla: Sin cuadrilla";
                    }                                                  
                    

                    //DESTINATARIOS....
                    $arrayFormasContacto    = $objEmComercial->getRepository('schemaBundle:InfoPersona')
                                                             ->getContactosByLoginPersonaAndFormaContacto(
                                                                $strUsrNotificar,
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
                    $this->objServiceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strContenidoCorreo);

                    //ACTUALIZA ESTADO DE LA INSPECCION A AsignadoTarea
                    if (is_object($objInfoDetalleSolPlanif) && is_object($objInfoComunicacion))
                    {
                        $arrayParametrosHistorialSolPlanif['idDetalleSolicitud'] = $intIdDetalleSolicitud;
                        $arrayParametrosHistorialSolPlanif['idAsignado']         = $intIdAsignado;
                        $arrayParametrosHistorialSolPlanif['tipoAsignado']       = $strTipoAsignado;
                        $arrayParametrosHistorialSolPlanif['fechaIniPlan']       = $objFeIniPlan;
                        $arrayParametrosHistorialSolPlanif['fechaFinPlan']       = $objFeFinPlan;
                        $arrayParametrosHistorialSolPlanif['idDetalleSolPlanif'] = $objInfoDetalleSolPlanif->getId();
                        $arrayParametrosHistorialSolPlanif['idTarea']            = $objInfoComunicacion->getId();
                        $arrayParametrosHistorialSolPlanif['estado']             = 'AsignadoTarea';
                        $arrayParametrosHistorialSolPlanif['usrCreacion']        = $strUsrCreacion;
                        $arrayParametrosHistorialSolPlanif['ipCreacion']         = $strIpCreacion;
                        $arrayParametrosHistorialSolPlanif['observacion']        = "Se asigna tarea ".$objInfoComunicacion->getId().
                                                                                " para realizar inspección en cliente.";
                        $arrayRespuestaCreaPlanifInsp = $objCoordinarService->crearPlanificacionInspeccion($arrayParametrosHistorialSolPlanif);
                        if(strtoupper($arrayRespuestaCreaPlanifInsp['status']) == 'OK')
                        {
                            $objInfoDetalleSolPlanif     = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanif'];
                            $objInfoDetalleSolPlanifHist = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanifHist']; 
                        }
                    }
                    $strSeProcesoInspecciones = 'S';
                }
            }

            if ($strSeProcesoInspecciones == 'S')
            {
                //ACTUALIZA ESTADO DE LA SOLICITUD
                $objInfoDetalleSolicitud->setEstado('AsignadoTarea');
                $objEmComercial->persist($objInfoDetalleSolicitud);
                $objEmComercial->flush();

                //ACTUALIZA ESTADO DE SOLICITUD A AsignadoTarea
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setObservacion("Se asigna tarea(s) a responsable(s) para realizar inspección en cliente");
                $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setEstado('AsignadoTarea');

                $objEmComercial->persist($objInfoDetalleSolHist);
                $objEmComercial->flush();

                $objEmComercial->getConnection()->commit();

                $arrayRespuesta['status']  = "ok";
                $arrayRespuesta["mensaje"] = "Se realizo programación con éxito!";
            }
            else
            {
                throw new \Exception("No se envio inspecciones para programar!");
            }

            //Buscar las caracteristicas para obtener los productos.
            // Si existen productos tomo los checklist para guardarlos como adjuntos en cada tarea.
            $arrayParametrosCaracteristicas['idSolicitud'] = $objInfoDetalleSolicitud->getId();
            $arrayCaracteristicasPlus = $this->objServiceGestionarInsp->obtenerCaracteristicas($arrayParametrosCaracteristicas);
            
            if (!empty($arrayCaracteristicasPlus['arrayProductos']))
            {
                $objJsonCheckList = json_decode($arrayCaracteristicasPlus['arrayProductos'],true);
                foreach($objJsonCheckList as $arrayProducto)
                {


                    $strChecklist = $arrayProducto['checklist'];
                    
                    $arrayPartsNombreArchivo  = explode('.',$strChecklist);
                    
                    $arrayPartsNombreArchivo            = explode('.', $strChecklist);
                    $strExtension                        = array_pop($arrayPartsNombreArchivo);
                    $strExtension=  strtoupper($strExtension);
                    $objInfoDocumento = new InfoDocumento();
                    $objInfoDocumento->setNombreDocumento('Adjunto Tarea');
                    $objInfoDocumento->setMensaje('Documento que se adjunta a una tarea');
                    $objInfoDocumento->setUbicacionFisicaDocumento($strChecklist);
                    $objInfoDocumento->setUbicacionLogicaDocumento($strChecklist);
                    $objInfoDocumento->setEstado('Activo');
                    $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                    $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                    $objInfoDocumento->setIpCreacion($strIpCreacion);
                    $objInfoDocumento->setUsrCreacion($strUsrCreacion);
                    $objInfoDocumento->setEmpresaCod($strCodEmpresa);
                    $objTipoDocumento = $objEmComunicacion
                    ->getRepository('schemaBundle:AdmiTipoDocumento')
                    ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento'=> $strExtension));
                    if( $objTipoDocumento != null)
                    {
                        $objInfoDocumento->setTipoDocumentoId($objTipoDocumento);
                    }
                   $objEmComunicacion->persist($objInfoDocumento);
                   $objEmComunicacion->flush();
                    

                    //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                    $objInfoTarea = $objEmSoporte->getRepository('schemaBundle:InfoTarea')->find($intIdTarea);
                                                           
                    
                    
                    $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                    $objInfoDocumentoRelacion->setModulo('SOPORTE');
                    $objInfoDocumentoRelacion->setEstado('Activo');
                    $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                    $objInfoDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                    $objInfoDocumentoRelacion->setDetalleId($objInfoDetalle->getId());
                    $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());
                    $objEmComunicacion->persist($objInfoDocumentoRelacion);
                    $objEmComunicacion->flush();
                   
                
                }
            }
            $objEmComunicacion->getConnection()->commit();
        }
        catch(\Exception $objE)
        {
            if ($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->getConnection()->rollback();
            }

            if($objEmComunicacion->getConnection()->isTransactionActive())
            {
                $objEmComunicacion->getConnection()->rollback();
            }
            $strMensajeError = "Error: " . $objE->getMessage();
            error_log($strMensajeError);
            $this->objServiceUtil->insertError('TELCOS+',
                                                'CoordinarInspeccionService.programarInspeccion',
                                                $strMensajeError,
                                                $strUsrCreacion,
                                                $strIpCreacion);
            $arrayRespuesta['status'] = 'Error';
            $arrayRespuesta['mensaje'] = $strMensajeError;
        }
        return $arrayRespuesta;
    }

    /**
     * Función que permite replanificar una inspección
     * @param Array arrayParametros[
     *   idDetalleSolicitud  => id de la solicitud
     *   idDetalleSolPlanif  => id de la planificación de inspección (referencia a infoDetalleSolicitudPlanif)
     *   idDetalle           => id detalle de la tarea (infoDetalle)
     *   idAsignado          => id del asignado (id de la cuadrilla)
     *   tipoAsignado        => tipo de asignado (Cuadrilla o Empleado)
     *   objFechaIniPlan     => objeto de la fecha inicial de planificación de la inspección
     *   objFechaFinPlan     => objeto de la fecha fin de planificación de la inspección
     *   strFechaIniPlan     => string de la fecha inicial de planificación de la inspección
     *   strFechaFinPlan     => string de la fecha inicial de planificación de la inspección
     *   idTarea             => id de la tarea (referencia a AdmiTarea)
     *   usrCreacion         => usuario de creación
     *   ipCreacion          => ip de creación
     *   codEmpresa          => id de la empresa del usuario en sesión
     *   prefijoEmpresa      => id de la empresa del usuario en sesión
     *   idDepartamento      => id departamento del usuario en sesión
     *   intIdMotivo         => id del motivo (AdmiMotivo)
     *   arrayResponsable    => array resonsables de la inspección
     *   idPersonaEmpresaRol => id persona empresa rol del usuario en sesión
     *   objRequest          => request que es recibido en el controlador
     *   login               => login del cliente de la inspección
     *   observacion         => observación de la inspección
     * ]
     * @author Andrés Montero <amontero@telconet.ec>
     * @version 1.0 02-12-2021
     * @return OCI_B_CURSOR
     */
    public function replanificarInspeccion($arrayParametros)
    {
        $objEmComercial          = $this->objEntManComercial;
        $objEmSoporte            = $this->objEntManSoporte;
        $objCoordinarService     = $this->serviceCoordinar;
        $objPlanificacionService = $this->objServicePlanificacion;
        $objSoporteService       = $this->objServiceSoporte;
        $arrayRespuesta          = array();
        $arrayDatosSinPunto      = array();
        $strUsrVendedor          = "";
        $intIdDetalleSolicitud   = $arrayParametros['idDetalleSolicitud'];
        $intIdDetalleSolPlanif   = $arrayParametros['idDetalleSolPlanif'];
        $intIdDetalle            = $arrayParametros['idDetalle'];
        $intIdAsignado           = $arrayParametros['idAsignado'];
        $strTipoAsignado         = $arrayParametros['tipoAsignado'];
        $objFeIniPlan            = $arrayParametros['objFechaIniPlan'];
        $objFeFinPlan            = $arrayParametros['objFechaFinPlan'];
        $strFechaIniPlan         = $arrayParametros['strFechaIniPlan'];
        $strFechaFinPlan         = $arrayParametros['strFechaFinPlan'];
        $intIdTarea              = $arrayParametros['idTarea'];
        $strEstado               = $arrayParametros['estado'];
        $strUsrCreacion          = $arrayParametros['usrCreacion'];
        $strIpCreacion           = $arrayParametros['ipCreacion'];
        $strCodEmpresa           = $arrayParametros['codEmpresa'];
        $strPrefijoEmpresa       = $arrayParametros['prefijoEmpresa'];
        $intIdDepartamento       = $arrayParametros['idDepartamento'];
        $intIdMotivo             = $arrayParametros["intIdMotivo"];
        $arrayResponsable        = $arrayParametros["arrayResponsable"];
        $intIdPersonaEmpresaRol  = $arrayParametros["idPersonaEmpresaRol"];
        $objRequest              = $arrayParametros["objRequest"];
        $strLogin                = $arrayParametros["login"];
        $strObservacion          = $arrayParametros['observacion'];

        $objEmComercial->getConnection()->beginTransaction();

        try
        {
            //Obtiene la solicitud de la planificación
            $objInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                      ->findOneById($intIdDetalleSolPlanif);
            //Obtiene la solicitud
            $objDetalleSolicitud     = $objInfoDetalleSolPlanif->getDetalleSolicitudId();

            //Obtiene la solicitud de la planificación
            $intIdComunicacion   = $objInfoDetalleSolPlanif->getTareaId();
            $objInfoComunicacion = $objEmComercial->getRepository('schemaBundle:InfoComunicacion')
                                                    ->findOneById($intIdComunicacion);
            $intIdDetalle = $objInfoComunicacion->getDetalleId();

            //Obtiene el punto
            $arrayInfoPunto          = $objEmComercial->getRepository('schemaBundle:InfoPunto')->findByLogin($strLogin);
            $objInfoPunto            = $arrayInfoPunto[0];

            //Obtiene el Motivo
            $objAdmiMotivo           = $objEmComercial->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);

            //Obtener tarea (AdmiTarea) que se asigna a la tarea
            $objAdmiTarea = $objEmSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTarea);

            $arrayFechaIniPlan   = explode(" ",$strFechaIniPlan);
            $arrayFechaFinPlan   = explode(" ",$strFechaFinPlan);

            if (is_object($objInfoDetalleSolPlanif) && is_object($objDetalleSolicitud) )
            {
                $intIdSolicitud = $objDetalleSolicitud->getId();
                //Crea historial en la planificación de inspección con estado Replanificada
                $arrayParametrosHistorialSolPlanif['idDetalleSolicitud'] = $intIdDetalleSolicitud;
                $arrayParametrosHistorialSolPlanif['idAsignado']         = $intIdAsignado;
                $arrayParametrosHistorialSolPlanif['tipoAsignado']       = $strTipoAsignado;
                $arrayParametrosHistorialSolPlanif['fechaIniPlan']       = $objFeIniPlan;
                $arrayParametrosHistorialSolPlanif['fechaFinPlan']       = $objFeFinPlan;
                $arrayParametrosHistorialSolPlanif['idDetalleSolPlanif'] = $objInfoDetalleSolPlanif->getId();
                $arrayParametrosHistorialSolPlanif['idTarea']            = null;
                $arrayParametrosHistorialSolPlanif['idMotivo']           = $objAdmiMotivo->getId();
                $arrayParametrosHistorialSolPlanif['estado']             = 'Replanificada';
                $arrayParametrosHistorialSolPlanif['usrCreacion']        = $strUsrCreacion;
                $arrayParametrosHistorialSolPlanif['ipCreacion']         = $strIpCreacion;
                $arrayParametrosHistorialSolPlanif['observacion']        = $strObservacion;
                $arrayRespuestaCreaPlanifInsp = $objCoordinarService->crearPlanificacionInspeccion($arrayParametrosHistorialSolPlanif);

                if(strtoupper($arrayRespuestaCreaPlanifInsp['status']) != 'OK')
                {
                    throw new \Exception($arrayRespuestaCreaPlanifInsp['mensaje']);
                }
                else
                {
                    $objInfoDetalleSolPlanif = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanif'];
                    $objInfoDetalleSolPlanifHist = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanifHist'];                    
                }

                if (!is_object($objInfoPunto))
                {
                    //Buscar las caracteristicas de la solicitud de inspección para obtener datos del punto
                    $arrayParametrosCaracteristicas['idSolicitud'] = $intIdSolicitud;
                    $arrayDatosSinPunto = $this->objServiceGestionarInsp->obtenerCaracteristicas($arrayParametrosCaracteristicas);

                    //SI NO SE OBTUVO EL VENDEDOR SE OBTIENE USR_CREACION DE QUIEN CREA LA SOLICITUD
                    if (empty($arrayDatosSinPunto['strUsrVendedor']))
                    {
                        $arrayDatosSinPunto['strUsrVendedor'] = $objDetalleSolicitud->getUsrCreacion();
                        $strUsrVendedor                       = $objDetalleSolicitud->getUsrCreacion();
                    }
                    else
                    {
                        $strUsrVendedor = $arrayDatosSinPunto['strUsrVendedor'];
                    }
                }
                else
                {
                    $strUsrVendedor = $objInfoPunto->getUsrVendedor();
                }

                $objAdmiCuadrilla = null;

                if($strTipoAsignado == 'Empleado')
                {
                    $arrayParametrosRol = array();
                    $arrayParametrosRol['intPersonaId'] = $intIdAsignado;
                    $arrayParametrosRol['strCodigoEmpresa'] = $strCodEmpresa;
                    
                    
                    $intInfoPersonaEmpresaRolid = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->getPersonaEmpresaRolPorIdPersonaYEmpresa($arrayParametrosRol);

                    $objInfoPersonaEmpresaRol = $objEmComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                    ->find($intInfoPersonaEmpresaRolid);

                    $objAdmiCuadrilla = $objEmComercial
                    ->getRepository('schemaBundle:AdmiCuadrilla')
                    ->find($objInfoPersonaEmpresaRol->getCuadrillaId());
                    
                }


                if($strTipoAsignado == 'Cuadrilla')
                {
                    $objAdmiCuadrilla = $objEmComercial->getRepository('schemaBundle:AdmiCuadrilla')->find($intIdAsignado);
                }


                //------- COMUNICACIONES --- NOTIFICACIONES
                $strContenidoCorreo = $this->objTemplating->render( 'planificacionBundle:Coordinar:notificacionInspeccion.html.twig', array(
                    'detalleSolicitud'           => $objDetalleSolicitud,
                    'detalleSolicitudPlanif'     => $objInfoDetalleSolPlanif,
                    'infoPunto'                  => $objInfoPunto,
                    'infoNoCliente'              => $arrayDatosSinPunto,
                    'detalleSolicitudPlanifHist' => $objInfoDetalleSolPlanifHist,
                    'motivo'                     => $objAdmiMotivo,
                    'admiCuadrilla'              => $objAdmiCuadrilla));



                    $strAsunto = "Planificación de Solicitud de Inspección Replanificada #" . $objDetalleSolicitud->getId() . 
                    " Cuadrilla: Sin cuadrilla";



                    if (is_object($objAdmiCuadrilla))
                    {
                        $strAsunto = "Planificación de Solicitud de Inspección Replanificada #" . $objDetalleSolicitud->getId() . 
                        " Cuadrilla: ". $objAdmiCuadrilla->getNombreCuadrilla();

                    }

                

                   
                //DESTINATARIOS....
                $arrayFormasContacto    = $objEmComercial->getRepository('schemaBundle:InfoPersona')
                                                        ->getContactosByLoginPersonaAndFormaContacto(
                                                        $strUsrVendedor,
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
                $this->objServiceEnvioPlantilla->enviarCorreo($strAsunto, $arrayTo, $strContenidoCorreo);


                $objDetalle = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);

                //Cambiar estado de tarea a Replanificar
                $strEstadoActualTarea   = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($objDetalle->getId());
                $arrayEstadosFinalizados = array("Finalizada","Cancelada","Anulada");
                if (!in_array($strEstadoActualTarea,$arrayEstadosFinalizados))
                {

                    $strObs = "Replanificación de Orden de Trabajo";

                    if($strObservacion != null)
                    {
                        $strObs = $strObs . " Detalle: ".$strObservacion;
                    }

                    $strRespuestaCancelacionTarea   = $objSoporteService->cambiarEstadoTarea(  
                                                                            $objDetalle,
                                                                            null, 
                                                                            $objRequest, 
                                                                            array(  "observacion"   => $strObs,
                                                                                    "cargarTiempo"  => "cliente",
                                                                                    "estado"        => "Replanificada",
                                                                                    "esSolucion"    => "N"));
                    if($strRespuestaCancelacionTarea != "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser replanificadas. Favor notificar a Sistemas.");
                    }
                }

                //Cambiar estado de tarea a Asignada
                $arrayParametrosHist["strCodEmpresa"]           = $strCodEmpresa;
                $arrayParametrosHist["strUsrCreacion"]          = $strUsrCreacion;
                $arrayParametrosHist["strEstadoActual"]         = "Asignada";
                $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDepartamento;
                $arrayParametrosHist["strOpcion"]               = "Seguimiento";
                $arrayParametrosHist["strIpCreacion"]           = $strIpCreacion;

                $arrayParametrosTarea['intIdPerEmpRolSesion']       = $intIdPersonaEmpresaRol;
                $arrayParametrosTarea['intIdFactibilidad']          = $intIdDetalleSolicitud;
                $arrayParametrosTarea['intIdPersona']               = 0;
                $arrayParametrosTarea['intIdPersonaEmpresaRol']     = 0;
                $arrayParametrosTarea['strObservacionServicio']     = "";//confirmar si aqui enviamos observación de la solicitud de inspección
                $arrayParametrosTarea['arrayParametrosPer']         = array();
                $arrayParametrosTarea['arrayParametrosHist']        = $arrayParametrosHist;
                $arrayParametrosTarea['intPersonaEmpresaRol']       = 0;
                $arrayParametrosTarea['intIdEmpresa']               = $strCodEmpresa;
                $arrayParametrosTarea['strPrefijoEmpresa']          = $strPrefijoEmpresa;
                $arrayParametrosTarea['strCodEmpresa']              = $strCodEmpresa;
                $arrayParametrosTarea['strUsrCreacion']             = $strUsrCreacion;
                $arrayParametrosTarea['strIpCreacion']              = $strIpCreacion;
                $arrayParametrosTarea['strResponsableTrazabilidad'] = null;
                $arrayParametrosTarea['id']                         = $intIdDetalleSolicitud;
                $arrayParametrosTarea['entitytarea']                = $objAdmiTarea;
                $arrayParametrosTarea['strSolucion']                = null;
                $arrayParametrosTarea['strDatosTelefonia']          = null;
                $arrayParametrosTarea['objInfoServicio']            = null;
                $arrayParametrosTarea['strTipoSolicitud']           = $objDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud();
                $arrayParametrosTarea['boolGuardo']                 = false;
                $arrayParametrosTarea['arrayParamRespon']           = $arrayResponsable;
                $arrayParametrosTarea['objSolicitud']               = $objDetalleSolicitud;
                $arrayParametrosTarea['strEsHal']                   = 'N';
                $arrayParametrosTarea['intIdSugerenciaHal']         = null;
                $arrayParametrosTarea['strAtenderAntes']            = null;
                $arrayParametrosTarea['boolEsReplanifHal']          = false;
                $arrayParametrosTarea['intIdDetalleExistente']      = $intIdDetalle;
                $arrayParametrosTarea['boolRequiereFlujoSim']       = false;
                $arrayParametrosTarea['idFlujoFactibilidad']        = null;
                $arrayParametrosTarea['strEsGestionSimultanea']     = null;
                $arrayParametrosTarea['objInfoPunto']               = $objInfoPunto;
                $arrayParametrosTarea['objInfoDetalleSolPlanif']    = $objInfoDetalleSolPlanif;
                
                if ($strObservacion != null)
                {
                    $arrayParametrosTarea['strObservacion'] = $strObservacion;
                }
                $arrayResultadoTarea = $objPlanificacionService->crearTareaAsignarPlanificacion($arrayParametrosTarea);
                $objInfoDetalle      = $arrayResultadoTarea['objInfoDetalle'];
                $objInfoComunicacion = $arrayResultadoTarea['objInfoComunicacion'];

                //REPLICA TAREA EN TABLA INFO_TAREA
                $arrayParametrosInfoTarea['intDetalleId']   = is_object($objInfoDetalle)? $objInfoDetalle->getId():null;
                $arrayParametrosInfoTarea['strUsrCreacion'] = isset($strUsrCreacion)? $strUsrCreacion:null;
                $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);

                //Crear Historial en la planificación de inspección con estado AsignadoTarea
                if (is_object($objInfoDetalleSolPlanif) && is_object($objInfoComunicacion))
                {
                    $arrayParametrosHistorialSolPlanif['idDetalleSolicitud'] = $intIdDetalleSolicitud;
                    $arrayParametrosHistorialSolPlanif['idAsignado']         = $intIdAsignado;
                    $arrayParametrosHistorialSolPlanif['tipoAsignado']       = $strTipoAsignado;
                    $arrayParametrosHistorialSolPlanif['idMotivo']           = $objAdmiMotivo->getId();
                    $arrayParametrosHistorialSolPlanif['fechaIniPlan']       = $objFeIniPlan;
                    $arrayParametrosHistorialSolPlanif['fechaFinPlan']       = $objFeFinPlan;
                    $arrayParametrosHistorialSolPlanif['idDetalleSolPlanif'] = $objInfoDetalleSolPlanif->getId();
                    $arrayParametrosHistorialSolPlanif['idTarea']            = $objInfoComunicacion->getId();
                    $arrayParametrosHistorialSolPlanif['estado']             = 'AsignadoTarea';
                    $arrayParametrosHistorialSolPlanif['usrCreacion']        = $strUsrCreacion;
                    $arrayParametrosHistorialSolPlanif['ipCreacion']         = $strIpCreacion;
                    $strMotivoModed = "Se asigna tarea ".$objInfoComunicacion->getId().
                    " para realizar inspección en cliente.";
                    if ($strObservacion != null)
                    {
                        $strMotivoModed =  $strMotivoModed . " Detalle: ". $strObservacion;
                    }

                    $arrayParametrosHistorialSolPlanif['observacion']        = $strMotivoModed;
                    $objCoordinarService->crearPlanificacionInspeccion($arrayParametrosHistorialSolPlanif);
                }
                $arrayRespuesta['status']  = 'ok';
                $arrayRespuesta['mensaje'] = 'proceso ejecutado con exito';
            }
            $objEmComercial->getConnection()->commit();
        }
        catch(\Exception $objE)
        {
            if ($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->getConnection()->rollback();
            }
            $strMensajeError = "Error: " . $objE->getMessage();
            $this->objServiceUtil->insertError('TELCOS+',
                                                'CoordinarInspeccionService.replanificarInspeccion',
                                                $strMensajeError,
                                                $strUsrCreacion,
                                                $strIpCreacion);
            $arrayRespuesta['status'] = 'Error';
            $arrayRespuesta['mensaje'] = $strMensajeError;
        }
        return $arrayRespuesta;
    }



    /**
     * 
     * Función usada para gestionar una solicitud de inspección
     * 
     * @param array arrayParametros [
     *                                  estado                  => estado de la solicitud inspección
     *                                  intIdMotivo             => id del motivo
     *                                  strObservacion           => observación de la solicitud
     *                                  strCodEmpresa            => id de la empresa
     *                                  strPrefijoEmpresa         => prefijo de la empresa
     *                                  intIdDepartamentoSession  => id del departamento en sesión
     *                                  intIdEmpleadoSession      => id del usuario en sesión
     *                                  strIpCreacion             => ip de creación
     *                                  strUsrCreacion            => usuario de creación
     *                                  idDetalleSolPlanif        => id de la planificación de inspección (referencia a infoDetalleSolicitudPlanif)
     *                                  objRequest                => Objeto con el request
     *                                  login                     => Objeto del punto
     *                                ]
     * 
     * @return array arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 05-01-2022
     * 
     */
    public function gestionarInspeccion($arrayParametros)
    {
        $objEmComercial             = $this->objEntManComercial;
        $objEmSoporte               = $this->objEntManSoporte;
        $objCoordinarService        = $this->serviceCoordinar;
        $objPlanificacionService    = $this->objServicePlanificacion;
        $objSoporteService          = $this->objServiceSoporte;
        $strEstado                  = $arrayParametros["estado"];
        $intIdMotivo                = $arrayParametros["intIdMotivo"];
        $strObservacion             = $arrayParametros["strObservacion"];
        $strCodEmpresa              = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa          = $arrayParametros["strPrefijoEmpresa"];
        $intIdDepartamentoSession   = $arrayParametros["intIdDepartamentoSession"];
        $intIdEmpleadoSession       = $arrayParametros["intIdEmpleadoSession"];
        $objRequest                 = $arrayParametros["objRequest"];
        $strIpCreacion              = $arrayParametros["strIpCreacion"];
        $strUsrCreacion             = $arrayParametros["strUsrCreacion"];
        $intIdDetalleSolPlanif      = $arrayParametros['idDetalleSolPlanif'];
        $strLogin                   = $arrayParametros["login"];
        $boolMostrarMsjErrorUsr     = false;
        $strObservacionTarea        = "";
        $strObservacionCancelaTarea = "";
        $strAsuntoNotificacion      = "";
        $strUsrVendedor             = "";
        $arrayDatosSinPunto         = array();
        $objEmComercial->getConnection()->beginTransaction();
        $objEmSoporte->getConnection()->beginTransaction();
        try
        {
            $objMotivo      = $objEmComercial->getRepository('schemaBundle:AdmiMotivo')->findOneById($intIdMotivo);

            //Obtiene el punto
            $arrayInfoPunto = $objEmComercial->getRepository('schemaBundle:InfoPunto')->findByLogin($strLogin);
            $objInfoPunto   = $arrayInfoPunto[0];

            //Obtiene la solicitud de la planificación
            $objInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                      ->findOneById($intIdDetalleSolPlanif);

            //Obtiene la solicitud
            $objSolicitud     = $objInfoDetalleSolPlanif->getDetalleSolicitudId();
            $intIdSolicitud   = $objSolicitud->getId();
            $strTipoSolicitud = strtolower($objSolicitud->getTipoSolicitudId()->getDescripcionSolicitud());


            if (!is_object($objInfoPunto))
            {
                //Buscar las caracteristicas de la solicitud de inspección para obtener datos del punto
                $arrayParametrosCaracteristicas['idSolicitud'] = $intIdSolicitud;
                $arrayDatosSinPunto = $this->objServiceGestionarInsp->obtenerCaracteristicas($arrayParametrosCaracteristicas);
                //SI NO SE OBTUVO EL VENDEDOR SE OBTIENE USR_CREACION DE QUIEN CREA LA SOLICITUD
                if (empty($arrayDatosSinPunto['strUsrVendedor']))
                {
                    $arrayDatosSinPunto['strUsrVendedor'] = $objSolicitud->getUsrCreacion();
                    $strUsrVendedor                       = $objSolicitud->getUsrCreacion();
                }
                else
                {
                    $strUsrVendedor = $arrayDatosSinPunto['strUsrVendedor'];
                }
            }
            else
            {
                $strUsrVendedor = $objInfoPunto->getUsrVendedor();
            }

            //Obtiene la tarea (InfoDetalle)
            $intIdComunicacion   = $objInfoDetalleSolPlanif->getTareaId();
            $objInfoComunicacion = $objEmComercial->getRepository('schemaBundle:InfoComunicacion')
                                                  ->findOneById($intIdComunicacion);
            $intIdDetalle        = $objInfoComunicacion->getDetalleId();

            //Obtiene el objeto de la tarea (InfoDetalle)
            $objDetalle          = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);

            if (is_object($objInfoDetalleSolPlanif) && is_object($objSolicitud) && is_object($objDetalle) )
            {
                //Obtiene el último historial de la planificación con estado AsignadoTarea
                $arrayInfoDetalleSolPlanifHist = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanifHist')
                                                            ->findBy(array('detalleSolPlanifId' => $objInfoDetalleSolPlanif->getId(),
                                                                            'estado'            => 'AsignadoTarea'),
                                                                    array('feCreacion' => 'DESC'));

                $objAdmiCuadrillaUltAsignada = $objEmComercial->getRepository('schemaBundle:AdmiCuadrilla')
                                                              ->find($arrayInfoDetalleSolPlanifHist[0]->getAsignadoId());

                //Configura el asunto de notificación, observación tarea y observación de cancelar Tarea
                if ($strEstado == "Detenido")
                {
                    $strObservacionTarea    = "Detención de Inspección";
                    $strAsuntoNotificacion  = "Planificación de Solicitud de Inspección Detenida #" . $objSolicitud->getId() . 
                                              " Cuadrilla:".$objAdmiCuadrillaUltAsignada->getNombreCuadrilla();
                }
                elseif ($strEstado == "Rechazada")
                {
                    $strObservacionTarea        = "Rechazo de Inspección";
                    $strObservacionCancelaTarea = "Cancelación automática por rechazo de Orden de Trabajo";
                    $strAsuntoNotificacion      = "Planificación de Solicitud de Inspección Rechazada #" . $objSolicitud->getId() . 
                                                  " Cuadrilla:".$objAdmiCuadrillaUltAsignada->getNombreCuadrilla();
    
                }
                elseif ($strEstado == "Anulada")
                {
                    $strObservacionTarea        = "Anulación de Inspección";
                    $strObservacionCancelaTarea = "Cancelación automática por anulación de Orden de Trabajo";
                    $strAsuntoNotificacion      = "Planificación de Solicitud de Inspección Anulada #" . $objSolicitud->getId() . 
                                                  " Cuadrilla:".$objAdmiCuadrillaUltAsignada->getNombreCuadrilla();
                }


                //Crea historial en la planificación de inspección
                $arrayParametrosHistorialSolPlanif['idDetalleSolicitud'] = $intIdSolicitud;
                $arrayParametrosHistorialSolPlanif['idDetalleSolPlanif'] = $objInfoDetalleSolPlanif->getId();
                $arrayParametrosHistorialSolPlanif['idTarea']            = null;
                $arrayParametrosHistorialSolPlanif['idMotivo']           = $objMotivo->getId();
                $arrayParametrosHistorialSolPlanif['estado']             = $strEstado;
                $arrayParametrosHistorialSolPlanif['usrCreacion']        = $strUsrCreacion;
                $arrayParametrosHistorialSolPlanif['ipCreacion']         = $strIpCreacion;
                $arrayParametrosHistorialSolPlanif['observacion']        = $strObservacion;
                $arrayRespuestaCreaPlanifInsp = $objCoordinarService->crearPlanificacionInspeccion($arrayParametrosHistorialSolPlanif);

                if(strtoupper($arrayRespuestaCreaPlanifInsp['status']) != 'OK')
                {
                    throw new \Exception($arrayRespuestaCreaPlanifInsp['mensaje']);
                }
                else
                {
                    $objInfoDetalleSolPlanif     = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanif'];
                    $objInfoDetalleSolPlanifHist = $arrayRespuestaCreaPlanifInsp['objDetalleSolPlanifHist'];                    
                }

                //------- COMUNICACIONES --- NOTIFICACIONES     
                $strContenidoCorreo = $this->objTemplating->render( 'planificacionBundle:Coordinar:notificacionInspeccion.html.twig', 
                                                                    array(
                                                                                'detalleSolicitud'           => $objSolicitud,
                                                                                'detalleSolicitudPlanif'     => $objInfoDetalleSolPlanif,
                                                                                'infoPunto'                  => $objInfoPunto,
                                                                                'infoNoCliente'              => $arrayDatosSinPunto,
                                                                                'detalleSolicitudPlanifHist' => $objInfoDetalleSolPlanifHist,
                                                                                'motivo'                     => $objMotivo,
                                                                                'admiCuadrilla'              => $objAdmiCuadrillaUltAsignada));

                //DESTINATARIOS....
                $arrayFormasContacto = $objEmComercial->getRepository('schemaBundle:InfoPersona')
                                                            ->getContactosByLoginPersonaAndFormaContacto($strUsrVendedor,
                                                                                                        'Correo Electronico');
                $arrayTo              = array();
                $arrayTo[]            = 'notificaciones_telcos@telconet.ec';
                if (isset($arrayFormasContacto) && !empty($arrayFormasContacto))
                {
                    foreach ($arrayFormasContacto as $arrayFormaContacto)
                    {
                        $arrayTo[] = $arrayFormaContacto['valor'];
                    }
                }
                $this->objServiceEnvioPlantilla->enviarCorreo($strAsuntoNotificacion, $arrayTo, $strContenidoCorreo);

                //CONSULTAMOS EL ÚLTIMO ESTADO DE LA TAREA
                $strEstadoActualTarea   = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')->getUltimoEstado($objDetalle->getId());
                $arrayEstadosFinalizados = array("Finalizada","Cancelada","Anulada");

                //SI ES DETENIDO SE DETIENE LA TAREA
                //SI ES RECHAZO O ANULACION SE CANCELA LA TAREA
                if ($strEstado == "Detenido" && !in_array($strEstadoActualTarea,$arrayEstadosFinalizados))
                {

                    $arrayParametrosReasignacion    = array("idEmpresa"             => $strCodEmpresa,
                                                            "prefijoEmpresa"        => $strPrefijoEmpresa,
                                                            "motivo"                => $strObservacionTarea,
                                                            "departamento_asignado" => $intIdDepartamentoSession,
                                                            "id_departamento"       => $intIdDepartamentoSession,
                                                            "empleado_asignado"     => $intIdEmpleadoSession,
                                                            "user"                  => $strUsrCreacion,
                                                            "empleado_logueado"     => $strUsrCreacion,
                                                            "clientIp"              => $strIpCreacion);
                    $arrayParametrosReasignacion['id_detalle']      = $objDetalle->getId();
                    $arrayParametrosReasignacion['fecha_ejecucion'] = (new \DateTime('now'))->format('Y-m-d H:i');

                    $arrayResultadoReasignacion  = $objSoporteService->reasignarTarea($arrayParametrosReasignacion);
                    if(!$arrayResultadoReasignacion["success"])
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser reasignadas. Favor notificar a Sistemas.");
                    }

                    //Cambiar estado de la tarea
                    $arrayParamsCambiarEstadoTarea  = array("observacion"   => $strObservacionTarea,
                                                            "cargarTiempo"  => "cliente",
                                                            "estado"        => $strEstado,
                                                            "esSolucion"    => "N");
                    $strRespuestaCancelacionTarea   = $objSoporteService
                                                        ->cambiarEstadoTarea($objDetalle, 
                                                                                null, 
                                                                                $objRequest, 
                                                                                $arrayParamsCambiarEstadoTarea);
                    if($strRespuestaCancelacionTarea != "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser cambiadas a ".$strEstado.". Favor notificar a Sistemas.");
                    }
                    //REPLICA TAREA EN TABLA INFO_TAREA
                    $arrayParametrosInfoTarea['intDetalleId']   = is_object($objDetalle)? $objDetalle->getId():null;
                    $arrayParametrosInfoTarea['strUsrCreacion'] = isset($strUsrCreacion)? $strUsrCreacion:null;
                    $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);
                }
                elseif ( ($strEstado=='Rechazada' || $strEstado=='Anulada') && !in_array($strEstadoActualTarea,$arrayEstadosFinalizados) )
                {
                    //Cambiar estado a Cancelada
                    $arrayParametrosCambiarEstadoTarea['observacion']     = $strObservacionCancelaTarea;
                    $arrayParametrosCambiarEstadoTarea['estado']          = "Cancelada";
                    $strRespuestaCancelacionTarea   = $this->objServiceSoporte
                                                           ->cambiarEstadoTarea($objDetalle, null, $objRequest, $arrayParametrosCambiarEstadoTarea);
                    if($strRespuestaCancelacionTarea !== "OK")
                    {
                        $boolMostrarMsjErrorUsr = true;
                        throw new \Exception("Ciertas tareas no pudieron ser canceladas. Favor notificar a Sistemas.");
                    }
                    
                    //REPLICA TAREA EN TABLA INFO_TAREA
                    $arrayParametrosInfoTarea['intDetalleId']   = is_object($objDetalle)? $objDetalle->getId():null;
                    $arrayParametrosInfoTarea['strUsrCreacion'] = isset($strUsrCreacion)? $strUsrCreacion:null;
                    $objSoporteService->crearInfoTarea($arrayParametrosInfoTarea);
                }

                $strStatus  = "OK";
                $strMensaje = "Se actualizo el estado de la solicitud a ".$strEstado;
                $objEmComercial->getConnection()->commit();
                $objEmSoporte->getConnection()->commit();
            }
            else
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe el detalle de solicitud");
            }
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = "Error: " . $e->getMessage();
            error_log($strMensaje);
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo detener la solicitud. Comuníquese con el Dep. de Sistemas!";
            }

            if($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->rollback();
            }
            $objEmComercial->close();

            if($objEmSoporte->getConnection()->isTransactionActive())
            {
                $objEmSoporte->rollback();
            }
            $objEmSoporte->close();
            $this->objServiceUtil->insertError('TELCOS+',
                                                'CoordinarInspeccionService.gestionarInspeccion',
                                                $strMensajeError,
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }


    /**
     * 
     * Función usada para finalizar una solicitud de inspección
     * 
     * @param array arrayParametros [
     *                                  strObservacion           => observación de finalización de la solicitud de inspección
     *                                  strIpCreacion             => ip de creación
     *                                  strUsrCreacion            => usuario de creación
     *                                  idSolicitud               => id de la solicitud de inspección
     *                                ]
     * 
     * @return array arrayRespuesta[
     *                                  "status"                => 'OK' o 'ERROR'
     *                                  "mensaje"               => mensaje de la ejecución de la función
     *                              ]
     * @author Andrés Montero Holguin <amontero@telconet.ec>
     * @version 1.0 04-04-2022
     * 
     */
    public function finalizarSolicitudInspeccion($arrayParametros)
    {
        $objEmComercial             = $this->objEntManComercial;
        $objEmSoporte               = $this->objEntManSoporte;
        $objEmGeneral               = $this->objEntManGeneral;

        $objServiceSop              = $this->objServiceSoporte;
        $objServiceGestionarInspec  = $this->objServiceGestionarInsp;
        $strObservacion             = $arrayParametros["observacion"];
        $strIpCreacion              = $arrayParametros["ipCreacion"];
        $strUsrCreacion             = $arrayParametros["usrCreacion"];
        $intIdSolicitud             = $arrayParametros["idSolicitud"];
        $strEmpresaCod              = $arrayParametros["empresaCod"];
        $boolMostrarMsjErrorUsr     = false;
        $strAsuntoNotificacion      = "";
        $objEmComercial->getConnection()->beginTransaction();
        $strStatus                 = 200;
        $strMensaje                = "Se finalizo con éxito la solicitud de inspección!";
        try
        {
            //Obtiene la solicitud
            $objInfoDetalleSolicitud = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                      ->findOneById($intIdSolicitud);

            if(is_object($objInfoDetalleSolicitud) &&  $objInfoDetalleSolicitud->getEstado() == "Finalizada")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("La solicitud ya fue finalizada anteriormente!");
            }
            if(is_object($objInfoDetalleSolicitud) &&  $objInfoDetalleSolicitud->getEstado() == "PrePlanificada")
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No se puede finalizar, la solicitud aun se encuentra en estado PrePlanificada!");
            }
            //Obtiene la empresa
            $objEmpresa = $objEmComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneById($strEmpresaCod);

            if(!is_object($objEmpresa))
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("La empresa no existe!");
            }

            if (is_object($objInfoDetalleSolicitud) && 
                $objInfoDetalleSolicitud->getTipoSolicitudId()->getDescripcionSolicitud() == "SOLICITUD INSPECCION" )
            {

                $arrayInfoDetalleSolPlanif = $objEmComercial->getRepository('schemaBundle:InfoDetalleSolPlanif')
                                                            ->findBy(array('detalleSolicitudId'=>$objInfoDetalleSolicitud->getId()));

                $strHayTareasCanceladas = "N";

                //RECORRE LAS INSPECCIONES DE LA SOLICITUD Y LAS FINALIZA
                foreach($arrayInfoDetalleSolPlanif as $objInfoDetalleSolPlanif)
                {
                    $strStatusFinaliza   = "OK";

                    //SE CONSULTA LA TAREA DE LA INSPECCIÓN Y LA FINALIZA
                    //Obtiene la tarea (InfoDetalle)
                    $intIdComunicacion   = $objInfoDetalleSolPlanif->getTareaId();
                    $objInfoComunicacion = $objEmComercial->getRepository('schemaBundle:InfoComunicacion')
                                                        ->findOneById($intIdComunicacion);

                    if (is_object($objInfoComunicacion))
                    {
                        $intIdDetalle = $objInfoComunicacion->getDetalleId();

                        //Obtiene el objeto de la tarea (InfoDetalle)
                        $objDetalle = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')->findOneById($intIdDetalle);
                        if ($objDetalle)
                        {
                            //CONSULTAMOS EL ÚLTIMO ESTADO DE LA TAREA
                            $strEstadoActualTarea   = $objEmSoporte->getRepository('schemaBundle:InfoDetalle')
                                                                    ->getUltimoEstado($objDetalle->getId());
                            $arrayEstadosFinalizados = array("Finalizada");
                            $arrayEstadosAnulado     = array("Cancelada","Anulada");

                            if (in_array($strEstadoActualTarea,$arrayEstadosAnulado))
                            {
                                $strHayTareasCanceladas = "S";
                                $strStatusFinaliza = "ERROR";
                            }
                            if (!in_array($strEstadoActualTarea,$arrayEstadosFinalizados) && $strStatusFinaliza != "ERROR")
                            {
                                $intIdDetalleTarea  = $objDetalle->getId();
                                //FINALIZA TAREA
                                $objInfoDetalleAsignado = $objEmSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                                                                        ->getUltimaAsignacion($intIdDetalleTarea);
                                
                                if (is_object($objInfoDetalleAsignado))
                                {
                                    $intIdAsignado = $objInfoDetalleAsignado->getRefAsignadoId();
                                    $strEmpleado   = $objInfoDetalleAsignado->getRefAsignadoNombre();
                                }

                                $objFechaHoy       = new \DateTime('now');
                                $strFechaFinaliza  = $objFechaHoy->format('Y-m-d');
                                $strHoraFinaliza   = $objFechaHoy->format('H:i:s');

                                $arrayTareaProgramacion = $objEmGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                                        ->get(
                                                                                'TAREA_PROGRAMAR_INSPECCION','COMERCIAL','',
                                                                                '','','','','','', $strEmpresaCod,''
                                                                            );

                                foreach($arrayTareaProgramacion as $arrayTareaProg)
                                {
                                    $intIdTarea = $arrayTareaProg['valor1'];
                                }

                                $arrayParametrosTareaF = array(
                                        'idEmpresa'             => $objEmpresa->getId(),
                                        'prefijoEmpresa'        => $objEmpresa->getPrefijo(),
                                        'idCaso'                => "",
                                        'idDetalle'             => $intIdDetalleTarea,
                                        'tarea'                 => $intIdTarea,
                                        'fechaCierre'           => $strFechaFinaliza,
                                        'horaCierre'            => $strHoraFinaliza,
                                        'fechaEjecucion'        => $strFechaFinaliza,
                                        'horaEjecucion'         => $strHoraFinaliza,
                                        'esSolucion'            => "",
                                        'fechaApertura'         => "",
                                        'horaApertura'          => "",
                                        'jsonMateriales'        => "",
                                        'idAsignado'            => $intIdAsignado,
                                        'observacion'           => "Se finaliza tarea en forma automática",
                                        'empleado'              => $strEmpleado,
                                        'usrCreacion'           => $strUsrCreacion,
                                        'ipCreacion'            => $strIpCreacion,
                                        'strEnviaDepartamento'  => "N",
                                        "clientes"              => "",
                                        "strOrigenComunicacion" => "Interno",
                                        "strClase"              => "Registro Interno"
                                );

                                $arrayRespuestaFinaliza = $objServiceSop->finalizarTarea($arrayParametrosTareaF);
                                $strStatusFinaliza      = $arrayRespuestaFinaliza['status'];

                            }
                        }
                        //FINALIZA INSPECCION
                        if($strStatusFinaliza == "OK")
                        {
                            $arrayParametrosFinSolInsp["strObservacion"] = "Se finaliza planificación de inspección por finalización de tarea" ;
                            $arrayParametrosFinSolInsp["strIpCreacion"]  = $strIpCreacion;
                            $arrayParametrosFinSolInsp["strUsrCreacion"] = $strUsrCreacion;
                            $arrayParametrosFinSolInsp['objDetalle']     = $objDetalle;
                            $objServiceGestionarInspec->finalizarInspeccion($arrayParametrosFinSolInsp);
                        }
                    }
                }

                if ($strHayTareasCanceladas == 'S')
                {
                    $boolMostrarMsjErrorUsr = true;
                    throw new \Exception("No se puede finalizar la solicitud, hay inspecciones que contienen tareas Canceladas o Anuladas!");
                }

                //ACTUALIZA ESTADO DE LA SOLICITUD
                $objInfoDetalleSolicitud->setEstado('Finalizada');
                $objEmComercial->persist($objInfoDetalleSolicitud);
                $objEmComercial->flush();

                //ACTUALIZA ESTADO DE SOLICITUD A Finalizada
                $objInfoDetalleSolHist = new InfoDetalleSolHist();
                $objInfoDetalleSolHist->setDetalleSolicitudId($objInfoDetalleSolicitud);
                $objInfoDetalleSolHist->setObservacion("Se Finaliza la solicitud de inspección con la Obs:".$strObservacion);
                $objInfoDetalleSolHist->setIpCreacion($strIpCreacion);
                $objInfoDetalleSolHist->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleSolHist->setUsrCreacion($strUsrCreacion);
                $objInfoDetalleSolHist->setEstado('Finalizada');

                $objEmComercial->persist($objInfoDetalleSolHist);
                $objEmComercial->flush();

                $objEmComercial->getConnection()->commit();
            }
            else
            {
                $boolMostrarMsjErrorUsr = true;
                throw new \Exception("No existe la solicitud de inspección");
            }
        }
        catch (\Exception $objE)
        {
            $strStatus  = 500;
            $strMensaje = "Error: " . $objE->getMessage();
            error_log($strMensaje);
            if($boolMostrarMsjErrorUsr)
            {
                $strMensaje = $objE->getMessage();
            }
            else
            {
                $strMensaje = "No se pudo finalizar la solicitud. Comuníquese con el Dep. de Sistemas!";
            }

            if($objEmComercial->getConnection()->isTransactionActive())
            {
                $objEmComercial->rollback();
                $objEmComercial->close();
            }
            $this->objServiceUtil->insertError('TELCOS+',
                                                'GestionarInspeccionService.finalizarSolicitudInspeccion',
                                                $objE->getMessage(),
                                                $strUsrCreacion,
                                                $strIpCreacion);
        }

        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }


}
