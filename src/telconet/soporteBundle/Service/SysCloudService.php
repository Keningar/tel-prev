<?php

    namespace telconet\soporteBundle\Service;
    use telconet\schemaBundle\Entity\InfoDetalle;
    use telconet\schemaBundle\Entity\InfoCriterioAfectado;
    use telconet\schemaBundle\Entity\InfoParteAfectada;
    use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
    use telconet\schemaBundle\Entity\InfoComunicacion;
    use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
    use telconet\schemaBundle\Entity\InfoDocumento;

    /**
     * Clase SysCloudService
     *
     * Clase que maneja las Transacciones realizadas en el módulo de Soporte.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 24-01-2019
     */
    class SysCloudService
    {
        private $emComercial;
        private $emSoporte;
        private $emInfraestructura;
        private $emComunicacion;
        private $emFinanciero;
        private $emGeneral;
        private $serviceEnvioPlantilla;
        private $serviceSoporte;
        private $serviceUtil;
        private $strHost;
        private $strPath;

        /**
         * Documentación para la función setDependencies
         *
         * Función que agrega dependencias usadas dentro de la clase ProcesoService
         *
         * @param ContainerInterface $objContainer
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 24-01-2019
         *
         */
        public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
        {
            $this->emComercial              = $objContainer->get('doctrine.orm.telconet_entity_manager');
            $this->emComunicacion           = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
            $this->emInfraestructura        = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
            $this->emSoporte                = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
            $this->emFinanciero             = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
            $this->emGeneral                = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
            $this->serviceEnvioPlantilla    = $objContainer->get('soporte.EnvioPlantilla');
            $this->serviceUtil              = $objContainer->get('schema.Util');
            $this->serviceSoporte           = $objContainer->get('soporte.SoporteService');
            $this->strHost                  = $objContainer->getParameter('host_scripts');
            $this->strPath                  = $objContainer->getParameter('path_telcos');
        }

        /**
         * Método encargado de crear una sub-tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 14-02-2019
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 29-03-2019 - Al momento de insertar el error se cambia el mensaje por la Excepción.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.2 01-01-2019 - En la validación del error se añade el insert para almacenar el json que se recibe vía WS.
         * 
         * @author Andrés Montero <amontero@telconet.ec>
         * @version 1.3 08-07-2020 - Actualización: Se agrega bloque de código para actualizar datos de tareas en nueva tabla DB_SOPORTE.INFO_TAREA
         *
         * @param Array $arrayParametros [
         *                                  strFechaSolicitada         : Fecha de ejecución de la tarea ,
         *                                  strHoraSolicitada          : Hora de ejecución de la tarea,
         *                                  intIdTarea                 : Id de la AdminTarea,
         *                                  strObservacion             : Observación,
         *                                  intIdComunicacion          : Id de comunicación o Número de Tarea,
         *                                  intIdAsignado              : Id del asignado,
         *                                  strNombreAsignado          : Nombre del Asignado,
         *                                  intIdRefAsignado           : Id del refAsignado,
         *                                  strRefAsignadoNombre       : Nombre del refAsignado,
         *                                  strTipoAsignacion          : Tipo de asignación,
         *                                  intIdPersonaEmpRolAsignado : Id de la persona empresa rol asignado,
         *                                  strPrefijoEmpresaAsignado  : Prefijo empresa del asignado,
         *                                  intIdDeparAsigna           : Id del departamento que asigna la tarea,
         *                                  intIdPersonaEmpRolAsigna   : Id persona empresa rol de la persona que asigna la tarea,
         *                                  strCodEmpresaAsigna        : Código de la empresa que asigna la tarea,
         *                                  strNombresEmpleadoAsigna   : Nombres y apellidos de la persona que asigna la tarea,
         *                                  strUser                    : usuario de la persona quien asigna la tarea,
         *                                  strIp                      : Ip de la persona quien asigna la tarea.
         *                               ]
         * @return Array $arrayRespuesta
         */
        public function putSubTarea($arrayParametros)
        {
            $strFechaSolicitada       = $arrayParametros['strFechaSolicitada']; //Formato YYYY-MM-DD.
            $strHoraSolicitada        = $arrayParametros['strHoraSolicitada'];  //Formato HH24:MI:SS.
            $intIdTarea               = $arrayParametros['intIdTarea'];
            $strObservacion           = $arrayParametros['strObservacion'];
            $intIdComunicacion        = $arrayParametros['intIdComunicacion']; //Número de Tarea.
            $intIdAsignado            = $arrayParametros['intIdAsignado'];
            $strNombreAsignado        = $arrayParametros['strNombreAsignado'];
            $intIdRefAsignado         = $arrayParametros['intIdRefAsignado'];
            $strRefAsignadoNombre     = $arrayParametros['strRefAsignadoNombre'];
            $strTipoAsignacion        = $arrayParametros['strTipoAsignacion']; //EMPLEADO
            $intIdPersonaEmpRol       = $arrayParametros['intIdPersonaEmpRolAsignado'];
            $strPrefijoEmpresa        = $arrayParametros['strPrefijoEmpresaAsignado'];
            $intIdDeparAsigna         = $arrayParametros['intIdDeparAsigna'];
            $intIdPersonaEmpRolAsigna = $arrayParametros['intIdPersonaEmpRolAsigna'];
            $strCodEmpresa            = $arrayParametros['strCodEmpresaAsigna'];
            $strNombresEmpleadoAsigna = $arrayParametros['strNombresEmpleadoAsigna'];
            $strUser                  = $arrayParametros['strUser'];
            $strIp                    = $arrayParametros['strIp'];
            $strAplicacion            = $arrayParametros['strAplicacion'];
            $boolReprogramadaInicio   = false;

            try
            {
                $arrayFecha = explode('-', $strFechaSolicitada);
                if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]))
                {
                    throw new \Exception('Error : Formato de fecha Invalido');
                }

                if (strtotime($strHoraSolicitada) === false)
                {
                    throw new \Exception('Error : Formato de hora Invalido');
                }

                $objDate = date_create(date('Y-m-d H:i', strtotime($strFechaSolicitada.' '.$strHoraSolicitada)));

                //Si la fecha de ejecucion es mayor a la actual se determina una reprogramacion inicial
                if ($objDate > new \DateTime('now'))
                {
                    $boolReprogramadaInicio = true;
                }

                $objAdmiTarea = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')->find($intIdTarea);

                if (!is_object($objAdmiTarea))
                {
                    throw new \Exception('Error : La tarea no existe');
                }

                $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')->find($intIdComunicacion);

                if (!is_object($objInfoComunicacion))
                {
                    throw new \Exception('Error : El número de tarea no existe');
                }

                //Obtenemos el ultimo estado de la tarea.
                $strEstadoTareaPadre = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->getUltimoEstado($objInfoComunicacion->getDetalleId());

                if (in_array($strEstadoTareaPadre, array('Cancelada','Anulada','Finalizada')))
                {
                    throw new \Exception('Error : La sub-tarea no puede ser creada por motivos que la tarea padre se encuentra en estado '.
                                         $strEstadoTareaPadre);
                }

                $this->emSoporte->getConnection()->beginTransaction();
                $this->emComunicacion->getConnection()->beginTransaction();

                $objInfoDetalle = new InfoDetalle();
                $objInfoDetalle->setTareaId($objAdmiTarea);
                $objInfoDetalle->setPesoPresupuestado(0);
                $objInfoDetalle->setValorPresupuestado(0);
                $objInfoDetalle->setObservacion($strObservacion);
                $objInfoDetalle->setFeCreacion(new \DateTime('now'));
                $objInfoDetalle->setFeSolicitada($objDate);
                $objInfoDetalle->setObservacion($strObservacion);
                $objInfoDetalle->setUsrCreacion($strAplicacion ? $strAplicacion : $strUser);
                $objInfoDetalle->setIpCreacion($strIp);
                $objInfoDetalle->setDetalleIdRelacionado($objInfoComunicacion->getDetalleId());
                $this->emSoporte->persist($objInfoDetalle);
                $this->emSoporte->flush();

                //Se hereda el mismo login de la tarea padre
                $objInfoParteAfectada = $this->emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                                  ->findOneByDetalleId($objInfoComunicacion->getDetalleId());

                if (is_object($objInfoParteAfectada))
                {
                    $objInfoCriterioAfectado = $this->emSoporte->getRepository('schemaBundle:InfoCriterioAfectado')
                                                         ->findOneByDetalleId($objInfoComunicacion->getDetalleId());

                    if (is_object($objInfoCriterioAfectado))
                    {
                        $objInfoCriterioAfectadoNuevo = new InfoCriterioAfectado();
                        $objInfoCriterioAfectadoNuevo->setId($objInfoCriterioAfectado->getId());
                        $objInfoCriterioAfectadoNuevo->setDetalleId($objInfoDetalle);
                        $objInfoCriterioAfectadoNuevo->setCriterio("Clientes");
                        $objInfoCriterioAfectadoNuevo->setOpcion($objInfoCriterioAfectado->getOpcion());
                        $objInfoCriterioAfectadoNuevo->setFeCreacion(new \DateTime('now'));
                        $objInfoCriterioAfectadoNuevo->setUsrCreacion($strUser);
                        $objInfoCriterioAfectadoNuevo->setIpCreacion($strIp);
                        $this->emSoporte->persist($objInfoCriterioAfectadoNuevo);
                        $this->emSoporte->flush();
                    }

                    $objInfoParteAfectadaNuevo = new InfoParteAfectada();
                    $objInfoParteAfectadaNuevo->setTipoAfectado($objInfoParteAfectada->getTipoAfectado());
                    $objInfoParteAfectadaNuevo->setDetalleId($objInfoDetalle->getId());
                    $objInfoParteAfectadaNuevo->setCriterioAfectadoId($objInfoParteAfectada->getCriterioAfectadoId());
                    $objInfoParteAfectadaNuevo->setAfectadoId($objInfoParteAfectada->getAfectadoId());
                    $objInfoParteAfectadaNuevo->setFeIniIncidencia($objInfoParteAfectada->getFeIniIncidencia());
                    $objInfoParteAfectadaNuevo->setAfectadoNombre($objInfoParteAfectada->getAfectadoNombre());
                    $objInfoParteAfectadaNuevo->setAfectadoDescripcion($objInfoParteAfectada->getAfectadoDescripcion());
                    $objInfoParteAfectadaNuevo->setFeCreacion(new \DateTime('now'));
                    $objInfoParteAfectadaNuevo->setUsrCreacion($strUser);
                    $objInfoParteAfectadaNuevo->setIpCreacion($strIp);
                    $this->emSoporte->persist($objInfoParteAfectadaNuevo);
                    $this->emSoporte->flush();
                }

                $objInfoDetalleAsignacion = new InfoDetalleAsignacion();
                $objInfoDetalleAsignacion->setDetalleId($objInfoDetalle);
                $objInfoDetalleAsignacion->setMotivo($strObservacion);
                $objInfoDetalleAsignacion->setUsrCreacion($strUser);
                $objInfoDetalleAsignacion->setFeCreacion(new \DateTime('now'));
                $objInfoDetalleAsignacion->setIpCreacion($strIp);
                $objInfoDetalleAsignacion->setTipoAsignado($strTipoAsignacion);
                $objInfoDetalleAsignacion->setAsignadoId($intIdAsignado);
                $objInfoDetalleAsignacion->setAsignadoNombre($strNombreAsignado);
                $objInfoDetalleAsignacion->setRefAsignadoId($intIdRefAsignado);
                $objInfoDetalleAsignacion->setRefAsignadoNombre($strRefAsignadoNombre);
                $objInfoDetalleAsignacion->setPersonaEmpresaRolId($intIdPersonaEmpRol);
                $objInfoDetalleAsignacion->setDepartamentoId($intIdDeparAsigna);

                if (!empty($intIdPersonaEmpRolAsigna) && $intIdPersonaEmpRolAsigna !== '')
                {
                    $objPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                            ->find($intIdPersonaEmpRolAsigna);

                    if (is_object($objPersonaEmpresaRol))
                    {
                        $objInfoOficinaGrupo = $this->emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                ->find($objPersonaEmpresaRol->getOficinaId());

                        if (is_object($objInfoOficinaGrupo))
                        {
                            $objInfoDetalleAsignacion->setCantonId($objInfoOficinaGrupo->getCantonId());
                        }
                    }
                }

                $this->emSoporte->persist($objInfoDetalleAsignacion);
                $this->emSoporte->flush();

                $arrayParametrosHist["strCodEmpresa"]           = $strCodEmpresa;
                $arrayParametrosHist["intIdDepartamentoOrigen"] = $intIdDeparAsigna;
                $arrayParametrosHist["intAsignadoId"]           = $intIdAsignado;
                $arrayParametrosHist["strUsrCreacion"]          = $strUser;
                $arrayParametrosHist["strIpCreacion"]           = $strIp;
                $arrayParametrosHist["intDetalleId"]            = $objInfoDetalle->getId();
                $arrayParametrosHist["strObservacion"]          = "Tarea Asignada";
                $arrayParametrosHist["strEstadoActual"]         = "Asignada";
                $arrayParametrosHist["strOpcion"]               = "Historial";
                $arrayParametrosHist["strAccion"]               = "Asignada";
                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                //Obtencion del mensaje generico para el seguiemiento de la tarea
                $strMensaje = "Tarea fue Asignada a " . $strRefAsignadoNombre;
                if($boolReprogramadaInicio)
                {
                    $strMensaje = "Tarea fue Asignada a ".$strRefAsignadoNombre." y Reprogramada para el ".
                                   date_format($objDate,'Y-m-d H:i');
                }

                $arrayParametrosHist["strObservacion"]  = $strMensaje;
                $arrayParametrosHist["strEstadoActual"] = "Asignada";
                $arrayParametrosHist["strOpcion"]       = "Seguimiento";
                $this->serviceSoporte->ingresaHistorialYSeguimientoPorTarea($arrayParametrosHist);

                $objClase = $this->emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")
                        ->findOneByNombreClaseDocumento("Notificacion");

                $strAsunto  = "Asignación de Subtarea relacionada a la Tarea # : ".$objInfoComunicacion->getId();
                $strMensaje = "Asignación de Tarea a " . $strRefAsignadoNombre;

                $objInfoDocumento = new InfoDocumento();
                $objInfoDocumento->setMensaje($strMensaje);
                $objInfoDocumento->setClaseDocumentoId($objClase);
                $objInfoDocumento->setEstado('Activo');
                $objInfoDocumento->setNombreDocumento($strAsunto);
                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                $objInfoDocumento->setUsrCreacion($strUser);
                $objInfoDocumento->setIpCreacion($strIp);
                $objInfoDocumento->setEmpresaCod($strCodEmpresa);
                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();

                $objInfoComunicacionSub = new InfoComunicacion();
                $objInfoComunicacionSub->setDetalleId($objInfoDetalle->getId());
                $objInfoComunicacionSub->setFormaContactoId(5);
                $objInfoComunicacionSub->setClaseComunicacion("Enviado");
                $objInfoComunicacionSub->setFechaComunicacion(new \DateTime('now'));
                $objInfoComunicacionSub->setFeCreacion(new \DateTime('now'));
                $objInfoComunicacionSub->setEstado('Activo');
                $objInfoComunicacionSub->setUsrCreacion($strUser);
                $objInfoComunicacionSub->setIpCreacion($strIp);
                $objInfoComunicacionSub->setEmpresaCod($strCodEmpresa);
                $this->emComunicacion->persist($objInfoComunicacionSub);
                $this->emComunicacion->flush();

                $objInfoDocumentoComunicacion = new InfoDocumentoComunicacion();
                $objInfoDocumentoComunicacion->setComunicacionId($objInfoComunicacionSub);
                $objInfoDocumentoComunicacion->setDocumentoId($objInfoDocumento);
                $objInfoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                $objInfoDocumentoComunicacion->setEstado('Activo');
                $objInfoDocumentoComunicacion->setUsrCreacion($strUser);
                $objInfoDocumentoComunicacion->setIpCreacion($strIp);
                $this->emComunicacion->persist($objInfoDocumentoComunicacion);
                $this->emComunicacion->flush();

                $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                        ->findOneBy(array('personaId'       => $intIdRefAsignado,
                                          'formaContactoId' => 5,
                                          'estado'          => "Activo"));

                if (is_object($objInfoPersonaFormaContacto))
                {
                    $arrayTo[] = $objInfoPersonaFormaContacto->getValor();
                }

                //Envio de Notificacion de generacion de nueva tarea
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->find($intIdPersonaEmpRol);

                if (is_object($objInfoPersonaEmpresaRol))
                {
                    $objOficina = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                            ->find($objInfoPersonaEmpresaRol->getOficinaId()->getId());

                    if (is_object($objOficina))
                    {
                        $intIdCanton = $objOficina->getCantonId();
                    }
                }

                $strNombreProceso = $objAdmiTarea->getProcesoId()->getNombreProceso();
                $strAsunto        = $strAsunto . " | PROCESO: ".$strNombreProceso;

                $arrayDatos = array('nombreProceso'  => $strNombreProceso,
                                    'actividad'      => $objInfoComunicacionSub,
                                    'asignacion'     => $objInfoDetalleAsignacion,
                                    'nombreTarea'    => $objAdmiTarea->getNombreTarea(),
                                    'empleadoLogeado'=> $strNombresEmpleadoAsigna,
                                    'empresa'        => $strPrefijoEmpresa,
                                    'detalle'        => $objInfoDetalle);

                $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsunto,
                                                                    $arrayTo,
                                                                    'TAREAACT',
                                                                    $arrayDatos,
                                                                    $strPrefijoEmpresa,
                                                                    $intIdCanton,
                                                                    $intIdAsignado);

                $this->emSoporte->getConnection()->commit();
                $this->emComunicacion->getConnection()->commit();

                //Proceso que graba tarea en INFO_TAREA
                if (is_object($objInfoDetalle))
                {
                    $arrayParametrosInfoTarea['intDetalleId']   = $objInfoDetalle->getId();
                    $arrayParametrosInfoTarea['strUsrCreacion'] = $strUser;
                    $this->serviceSoporte->crearInfoTarea($arrayParametrosInfoTarea);
                }
                $arrayRespuesta = array ('status'      => 'ok',
                                         'message'     => 'Tarea creada',
                                         'numeroTarea' => $objInfoComunicacionSub->getId());
            }
            catch (\Exception $objException)
            {
                if($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->rollback();
                }

                if($this->emSoporte->getConnection()->isTransactionActive())
                {
                    $this->emSoporte->getConnection()->rollback();
                }

                $this->emSoporte->getConnection()->close();
                $this->emComunicacion->getConnection()->close();

                $strMessage = 'Error al crear la Sub-Tarea';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ',$objException->getMessage())[1];
                }

                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $this->serviceUtil->insertError('Telcos+',
                                                'SysCloudService->putSubTarea',
                                                 $strCodigo.'|'.$objException->getMessage(),
                                                 $strUser,
                                                 $strIp);

                $this->serviceUtil->insertError('Telcos+',
                                                'SysCloudService->putSubTarea',
                                                 $strCodigo.'|'.json_encode($arrayParametros),
                                                 $strUser,
                                                 $strIp);

                $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de obtener los departamentos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 14-12-2018
         *
         * @param Array $arrayParametros [
         *                                  intIdArea             : id del área,
         *                                  intIdDepartamento     : id del departamento,
         *                                  strNombreArea         : nombre del área,
         *                                  strNombreDepartamento : nombre del departamento,
         *                                  strEstadoArea         : estado del área,
         *                                  strEstadoDepartamento : estado del departamento,
         *                                  strPrefijoEmpresa     : prefijo de la empresa
         *                               ]
         * @return Array $arrayRespuesta
         */
        public function getDepartamentos($arrayParametros)
        {
            $arrayRespuesta = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                ->getDepartamentos($arrayParametros);
            return $arrayRespuesta;
        }

        /**
         * Método encargado de obtener los datos de una persona de acuerdo a su rol y empresa.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 24-01-2019
         *
         * @param Array $arrayParametros [
         *                                  strRol          : Descripción del rol,
         *                                  strPrefijo      : Prefijo de la empresa,
         *                                  strDepartamento : Nombre del departamento,
         *                                  strLogin        : Login o usuario del empleado
         *                               ]
         * @return Array $arrayRespuesta
         */
        public function getInfoDatosPersona($arrayParametros)
        {
            $arrayRespuesta = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                ->getInfoDatosPersona($arrayParametros);
            return $arrayRespuesta;
        }

        /**
         * Método encargado de realizar la consulta de las tareas con sus respectivos procesos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 17-09-2018
         *
         * @param $arrayParametros [
         *                              arrayIdTarea     => Id del error,
         *                              arrayIdProceso   => Aplicacion,
         *                              strNombreTarea   => Nombre de la tarea,
         *                              strNombreProceso => Nombre del proceso,
         *                              strEstadoTarea   => Proceso,
         *                              strEstadoProceso => Detalle del error
         *                              strCodEmpresa    => Codigo de la Empresa,
         *                              boolFiltraEmpresa => Indica si se filtra por Empresa
         *                         ]
         * 
         * @author David De La Cruz <ddelacruz@telconet.ec>
         * @version 1.1 26-08-2021 - Se actualiza para incluir filtro por CodEmpresa
         * 
         * @return $arrayRespuesta
         */
        public function getTareasProcesos($arrayParametros)
        {
            $arrayRespuesta = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                ->getTareasProcesos($arrayParametros);
            if (!empty($arrayRespuesta) && $arrayRespuesta['status'] === 'ok' && empty($arrayRespuesta['result']))
            {
                $arrayRespuesta            = array();
                $arrayRespuesta['status']  = 'fail';
                $arrayRespuesta['message'] = 'La consulta no retornó valores con los parámetros ingresados.';
            }
            return $arrayRespuesta;
        }
    }
