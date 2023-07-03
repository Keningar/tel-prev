<?php

    namespace telconet\soporteBundle\Service;

    use telconet\schemaBundle\Entity\InfoDocumento;
    use telconet\schemaBundle\Entity\InfoCasoHistorial;
    use telconet\schemaBundle\Entity\InfoCasoAsignacion;
    use telconet\schemaBundle\Entity\InfoDetalle;
    use telconet\schemaBundle\Entity\InfoComunicacion;
    use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
    use telconet\schemaBundle\Entity\InfoTareaCaracteristica;
    use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolCarac;

    /**
     * Clase SoporteProcesosService
     *
     * Clase para alojar los métodos genéricos del módulo de Soporte.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 24-01-2019
     */
    class SoporteProcesosService
    {
        private $emComercial;
        private $emSoporte;
        private $emInfraestructura;
        private $emComunicacion;
        private $emFinanciero;
        private $emGeneral;
        private $serviceSoporte;
        private $serviceUtil;
        private $serviceEnvioPlantilla;

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
            $this->emComercial           = $objContainer->get('doctrine.orm.telconet_entity_manager');
            $this->emComunicacion        = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
            $this->emInfraestructura     = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
            $this->emSoporte             = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
            $this->emFinanciero          = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
            $this->emGeneral             = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
            $this->serviceUtil           = $objContainer->get('schema.Util');
            $this->serviceSoporte        = $objContainer->get('soporte.SoporteService');
            $this->serviceEnvioPlantilla = $objContainer->get('soporte.EnvioPlantilla');
        }

        /*
         * Método encargado de reasignar un caso.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 11-09-2018
         *
         * @param  $arrayParametros [
         *                              intIdCaso                 => Id del caso,
         *                              intInfoPersonaEmpresaRol  => Id de la INFO_PERSONA_EMPRESA_ROL (Persona Asignada),
         *                              strObservacion            => Observación de la reasignación del caso,
         *                              strUsuario                => Usuario quien reasigna el caso,
         *                              strIp                     => Ip de quien reasigna el caso,
         *                              strPrefijoEmpresa         => Prefijo de la empresa del usuario quien reasigna el caso
         *                          ]
         * @return $arrayRespuesta
         */
        public function putReasignarCaso($arrayParametros)
        {
            $intIdCaso                = $arrayParametros['intIdCaso'];
            $intInfoPersonaEmpresaRol = $arrayParametros['intInfoPersonaEmpresaRol'];
            $strObservacion           = $arrayParametros['strObservacion'];
            $strUsuario               = $arrayParametros['strUsuario'];
            $strIp                    = $arrayParametros['strIp'];
            $strPrefijoEmpresa        = $arrayParametros['strPrefijoEmpresa'];
            $arrayResultado           = array();
            $arrayAsignacion          = array();
            $arrayUpdateAsignacion    = array();

            $this->emSoporte->getConnection()->beginTransaction();
            $this->emComunicacion->getConnection()->beginTransaction();

            try
            {
                $objInfoPersonaLogin = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                    ->findOneBy(array ('login'  => $strUsuario,
                                       'estado' => 'Activo'));

                if (!is_object($objInfoPersonaLogin))
                {
                    throw new \Exception("Error : Usuario $strUsuario no existe");
                }

                $objInfoCaso = $this->emSoporte->getRepository("schemaBundle:InfoCaso")
                    ->find($intIdCaso);

                if (!is_object($objInfoCaso))
                {
                    throw new \Exception("Error : El caso $intIdCaso no existe");
                }

                $arrayInfoCasoHistorial = $this->emSoporte->getRepository("schemaBundle:InfoCasoHistorial")
                    ->findBy(array ('casoId' => $objInfoCaso),
                             array ("id"     => "DESC"));

                if (!empty($arrayInfoCasoHistorial) && count($arrayInfoCasoHistorial) > 0 &&
                    strtolower($arrayInfoCasoHistorial[0]->getEstado()) === 'cerrado')
                {
                    throw new \Exception("Error : El caso $intIdCaso se encuentra cerrado");
                }

                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->find($intInfoPersonaEmpresaRol);

                if (!is_object($objInfoPersonaEmpresaRol))
                {
                    throw new \Exception("Error : El asignado $intInfoPersonaEmpresaRol no existe");
                }

                if (is_null($objInfoPersonaEmpresaRol->getDepartamentoId()))
                {
                    throw new \Exception("Error : El asignado $intInfoPersonaEmpresaRol no tiene un departamento asociado");
                }

                $objAdmiDepartamento = $this->emGeneral->getRepository("schemaBundle:AdmiDepartamento")
                    ->find($objInfoPersonaEmpresaRol->getDepartamentoId());

                if (!is_object($objAdmiDepartamento))
                {
                    throw new \Exception("Error : No existe el departamento del usuario asignado $intInfoPersonaEmpresaRol");
                }

                $objInfoPersona = $objInfoPersonaEmpresaRol->getPersonaId();

                if (!is_object($objInfoPersona))
                {
                    throw new \Exception("Error : El asignado $intInfoPersonaEmpresaRol no es una persona");
                }

                $arrayInfoDetalleHipotesis = $this->emSoporte->getRepository('schemaBundle:InfoDetalleHipotesis')
                    ->findByCasoId($objInfoCaso->getId());

                if (empty($arrayInfoDetalleHipotesis) || count($arrayInfoDetalleHipotesis) < 1)
                {
                    throw new \Exception("Error : El caso no tiene hipótesis registradas");
                }

                foreach($arrayInfoDetalleHipotesis as $objInfoDetalleHipotesis)
                {
                    $arrayInfoCasoAsignacion = $this->emSoporte->getRepository('schemaBundle:InfoCasoAsignacion')
                        ->findByDetalleHipotesisId($objInfoDetalleHipotesis->getId());

                    if (empty($arrayInfoCasoAsignacion) || count($arrayInfoCasoAsignacion) < 1)
                    {
                        $arrayAsignacion['objInfoCasoAsignacion'] = new InfoCasoAsignacion();
                        $arrayAsignacion['boolNuevaAsignacion']   = true;
                        $arrayUpdateAsignacion[]                  = $arrayAsignacion;
                    }
                    else
                    {
                        foreach($arrayInfoCasoAsignacion as $objInfoCasoAsignacion)
                        {
                            $arrayAsignacion['objInfoCasoAsignacion'] = $objInfoCasoAsignacion;
                            $arrayAsignacion['boolNuevaAsignacion']   = false;

                            if($objInfoCasoAsignacion->getAsignadoId() != $objAdmiDepartamento->getId())
                            {
                               $arrayAsignacion['boolNuevaAsignacion'] = true;
                            }

                            $arrayUpdateAsignacion[] = $arrayAsignacion;
                        }
                    }

                    foreach($arrayUpdateAsignacion as $arrayObject)
                    {
                        $objInfoCasoAsignacion = $arrayObject['objInfoCasoAsignacion'];
                        $boolNuevaAsignacion   = $arrayObject['boolNuevaAsignacion'];

                        $objInfoCasoAsignacion->setAsignadoId($objAdmiDepartamento->getId());
                        $objInfoCasoAsignacion->setAsignadoNombre($objAdmiDepartamento->getNombreDepartamento());
                        $objInfoCasoAsignacion->setRefAsignadoId($objInfoPersona->getId());
                        $objInfoCasoAsignacion->setRefAsignadoNombre($objInfoPersona->getNombres().' '.$objInfoPersona->getApellidos());
                        $objInfoCasoAsignacion->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol->getId());
                        $objInfoCasoAsignacion->setMotivo($this->serviceSoporte->eliminarSimbolosDeTags($strObservacion));
                        $objInfoCasoAsignacion->setUsrCreacion($strUsuario);
                        $objInfoCasoAsignacion->setFeCreacion(new \DateTime('now'));
                        $objInfoCasoAsignacion->setIpCreacion($strIp);
                        $objInfoCasoAsignacion->setDetalleHipotesisId($objInfoDetalleHipotesis);
                        $this->emSoporte->persist($objInfoCasoAsignacion);
                        $this->emSoporte->flush();

                        if ($boolNuevaAsignacion)
                        {
                            $objInfoCasoHistorial = new InfoCasoHistorial();
                            $objInfoCasoHistorial->setCasoId($objInfoCaso);
                            $objInfoCasoHistorial->setObservacion("Asignacion del caso");
                            $objInfoCasoHistorial->setEstado("Asignado");
                            $objInfoCasoHistorial->setFeCreacion(new \DateTime('now'));
                            $objInfoCasoHistorial->setUsrCreacion($strUsuario);
                            $objInfoCasoHistorial->setIpCreacion($strIp);
                            $this->emSoporte->persist($objInfoCasoHistorial);
                            $this->emSoporte->flush();

                            $objInfoDetalle = new InfoDetalle();
                            $objInfoDetalle->setDetalleHipotesisId($objInfoDetalleHipotesis->getId());
                            $objInfoDetalle->setPesoPresupuestado(0);
                            $objInfoDetalle->setValorPresupuestado(0);
                            $objInfoDetalle->setFeCreacion(new \DateTime('now'));
                            $objInfoDetalle->setUsrCreacion($strUsuario);
                            $objInfoDetalle->setIpCreacion($strIp);
                            $this->emSoporte->persist($objInfoDetalle);
                            $this->emSoporte->flush();

                            /************************************************************************************
                                                    ENVIO MAILS Y COMUNICACION
                            ************************************************************************************/

                            $objAdmiClaseDocumento = $this->emComunicacion->getRepository("schemaBundle:AdmiClaseDocumento")
                                ->findOneByNombreClaseDocumento("Notificacion");

                            if (is_object($objAdmiClaseDocumento))
                            {
                                $objInfoDocumento= new InfoDocumento();
                                $objInfoDocumento->setClaseDocumentoId($objAdmiClaseDocumento);
                                $objInfoDocumento->setMensaje("Asignacion de Caso: ".$objInfoCaso->getNumeroCaso());
                                $objInfoDocumento->setEstado('Activo');
                                $objInfoDocumento->setNombreDocumento("Asignacion de Caso: ".$objInfoCaso->getNumeroCaso());
                                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                                $objInfoDocumento->setUsrCreacion($strUsuario);
                                $objInfoDocumento->setIpCreacion($strIp);
                                $objInfoDocumento->setEmpresaCod($objAdmiDepartamento->getEmpresaCod());
                                $this->emComunicacion->persist($objInfoDocumento);
                                $this->emComunicacion->flush();

                                $objInfoComunicacion = new InfoComunicacion();
                                $objInfoComunicacion->setCasoId($objInfoCaso->getId());
                                $objInfoComunicacion->setDetalleId($objInfoDetalle->getId());
                                $objInfoComunicacion->setFormaContactoId(5);
                                $objInfoComunicacion->setClaseComunicacion("Enviado");
                                $objInfoComunicacion->setFechaComunicacion(new \DateTime('now'));
                                $objInfoComunicacion->setFeCreacion(new \DateTime('now'));
                                $objInfoComunicacion->setEstado('Activo');
                                $objInfoComunicacion->setUsrCreacion($strUsuario);
                                $objInfoComunicacion->setIpCreacion($strIp);
                                $objInfoComunicacion->setEmpresaCod($objAdmiDepartamento->getEmpresaCod());
                                $this->emComunicacion->persist($objInfoComunicacion);
                                $this->emComunicacion->flush();

                                $objInfoDocumentoComunicacion = new InfoDocumentoComunicacion();
                                $objInfoDocumentoComunicacion->setComunicacionId($objInfoComunicacion);
                                $objInfoDocumentoComunicacion->setDocumentoId($objInfoDocumento);
                                $objInfoDocumentoComunicacion->setFeCreacion(new \DateTime('now'));
                                $objInfoDocumentoComunicacion->setEstado('Activo');
                                $objInfoDocumentoComunicacion->setUsrCreacion($strUsuario);
                                $objInfoDocumentoComunicacion->setIpCreacion($strIp);
                                $this->emComunicacion->persist($objInfoDocumentoComunicacion);
                                $this->emComunicacion->flush();

                                $objInfoPersonaFormaContacto = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')
                                    ->findOneBy(array ('personaId'       => $objInfoPersona->getId(),
                                                       'formaContactoId' => 5,
                                                       'estado'          => "Activo"));

                                if(is_object($objInfoPersonaFormaContacto))
                                {
                                    $arrayTo[] = $objInfoPersonaFormaContacto->getValor();
                                }

                                // Obtenemos el canton
                                $intCantonId = '';
                                $objInfoOficinaGrupo = $this->emComercial->getRepository('schemaBundle:InfoOficinaGrupo')
                                    ->find($objInfoPersonaEmpresaRol->getOficinaId()->getId());

                                if (is_object($objInfoOficinaGrupo))
                                {
                                    $intCantonId = $objInfoOficinaGrupo->getCantonId();
                                }

                                //Obtenemos los afectados del caso
                                $arrayAfectados = $this->serviceSoporte->getAfectacionDetalladaPorCaso($objInfoCaso->getId());

                                $arrayParametrosPlantilla = array('caso'            => $objInfoCaso,
                                                                  'afectadoPadre'   => $arrayAfectados['afectadosPadre'],
                                                                  'afectadoDetalle' => $arrayAfectados['afectadosDetalle'],
                                                                  'tieneDetalle'    => $arrayAfectados['tieneDetalle'],
                                                                  'asignacion'      => $objInfoCasoAsignacion,
                                                                  'empleadoLogeado' => $strUsuario,
                                                                  'empresa'         => $strPrefijoEmpresa);

                                $this->serviceEnvioPlantilla->generarEnvioPlantilla(
                                                "Asignacion de Caso: ".$objInfoCaso->getNumeroCaso(),
                                                $arrayTo,
                                                'CASOASIG',
                                                $arrayParametrosPlantilla,
                                                $objAdmiDepartamento->getEmpresaCod(),
                                                $intCantonId,
                                                $objAdmiDepartamento->getId());
                            }
                        }
                    }
                    $arrayUpdateAsignacion = array();
                }

                $this->emSoporte->getConnection()->commit();
                $this->emComunicacion->getConnection()->commit();

                $arrayResultado['status']  = 'ok';
                $arrayResultado['message'] = 'La asignación se proceso correctamente';
            }
            catch (\Exception $objException)
            {
                if ($this->emSoporte->getConnection()->isTransactionActive())
                {
                    $this->emSoporte->getConnection()->rollback();
                }

                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->rollback();
                }

                $this->emSoporte->getConnection()->close();
                $this->emComunicacion->getConnection()->close();

                $strMessage = 'Error en el Reasignación del Caso';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->putReasignarCaso',
                                                 $objException->getMessage(),
                                                 $strUsuario,
                                                 $strIp);

                $arrayResultado['status']  = 'fail';
                $arrayResultado['message'] = $strMessage;
            }

            return $arrayResultado;
        }

        /**
         * Método encargado de asignar la zona prestada a una cuadrilla planificada por HAL.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 11-09-2018
         *
         * @param  $arrayParametros [
         *                              intIdCab          => Id de la tabla INFO_CUADRILLA_PLANIF_CAB,
         *                              intIdZonaPrestada => Id de la tabla ADMI_ZONA,
         *                              strUsuario        => Usuario quien realiza la modificación,
         *                              strIp             => Ip de quien realiza la modificación
         *                          ]
         * @return $arrayRespuesta
         */
        public function putAsignarZonaPrestada($arrayParametros)
        {
            $intIdCab          = $arrayParametros['intIdCab'];
            $intIdZonaPrestada = $arrayParametros['intIdZonaPrestada'];
            $strUsuario        = $arrayParametros['strUsuario'];
            $strIp             = $arrayParametros['strIp'];
            $arrayResultado    = array();

            $this->emSoporte->getConnection()->beginTransaction();

            try
            {
                $objInfoCuadrillaPlanifCab = $this->emSoporte->getRepository("schemaBundle:InfoCuadrillaPlanifCab")
                        ->find($intIdCab);

                if (!is_object($objInfoCuadrillaPlanifCab))
                {
                    throw new \Exception("Error : No existe el id $intIdCab");
                }

                if (!empty($intIdZonaPrestada) || $intIdZonaPrestada !== null || $intIdZonaPrestada !== '')
                {
                    $objAdmiZona = $this->emGeneral->getRepository("schemaBundle:AdmiZona")->find($intIdZonaPrestada);

                    if (!is_object($objAdmiZona))
                    {
                        throw new \Exception("Error : No existe el id de zona $intIdZonaPrestada");
                    }
                }

                $objInfoCuadrillaPlanifCab->setZonaPrestadaId($intIdZonaPrestada);
                $objInfoCuadrillaPlanifCab->setUsrModificacion($strUsuario);
                $objInfoCuadrillaPlanifCab->setIpModificacion($strIp);
                $objInfoCuadrillaPlanifCab->setFeModificacion(new \DateTime('now'));
                $this->emSoporte->persist($objInfoCuadrillaPlanifCab);
                $this->emSoporte->flush();
                $this->emSoporte->commit();

                $arrayResultado['status']  = 'ok';
                $arrayResultado['message'] = 'Transacción exitosa';
            }
            catch (\Exception $objException)
            {
                if ($this->emSoporte->getConnection()->isTransactionActive())
                {
                    $this->emSoporte->rollback();
                }

                $this->emSoporte->close();

                $strMessage = 'Error en la transacción';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->setAsignarZonaPrestada',
                                                 $objException->getMessage(),
                                                 $strUsuario,
                                                 $strIp);

                $arrayResultado['status']  = 'fail';
                $arrayResultado['message'] = $strMessage;
            }
            return $arrayResultado;
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
         *                              strEstadoTarea   => Proceso,
         *                              strEstadoProceso => Detalle del error
         *                         ]
         * @return $arrayRespuesta
         */
        public function getTareasProcesos($arrayParametros)
        {
            $arrayRespuesta = $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                    ->getTareasProcesos($arrayParametros);

            if (!empty($arrayRespuesta) && $arrayRespuesta['status'] === 'ok' && empty($arrayRespuesta['result']))
            {
                $arrayRespuesta = array('status' => 'fail', 'message' => 'La consulta no retornó valores');
            }
            return $arrayRespuesta;
        }

        /**
         * Función encargada de ingresar la característica de una tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 30-04-2019
         *
         * @param Array $arrayParametros [
         *                                 intIdComunicacion = Número de tarea o también conocido como idComunicacion.
         *                                 intIdDetalle      = Id de detalle de la tarea.
         *                                 strCaracteristica = Descripción de la característica.
         *                                 strValor          = Valor de la característica.
         *                                 strEstado         = Estado,
         *                                 strUser           = Usuario de Creación o modificación.
         *                                 strIp             = Ip de Creación o modificación.
         *                               ]
         *
         * @return Array $arrayRespuesta;
         */
        public function putTareaCaracteristica($arrayParametros)
        {
            $intIdComunicacion  = $arrayParametros['intIdComunicacion'];
            $intIdDetalle       = $arrayParametros['intIdDetalle'];
            $strCaracteristica  = $arrayParametros['strCaracteristica'];
            $strValor           = $arrayParametros['strValor'];
            $strEstado          = $arrayParametros['strEstado'];
            $strUser            = $arrayParametros['strUser'];
            $strIp              = $arrayParametros['strIp'];

            try
            {
                $this->emSoporte->getConnection()->beginTransaction();

                $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneByDescripcionCaracteristica($strCaracteristica);

                if (!is_object($objAdmiCaracteristica))
                {
                    throw new \Exception("Error : La característica $strCaracteristica no existe");
                }

                if (in_array($objAdmiCaracteristica->getEstado(),array('Inactivo','Eliminado')))
                {
                    throw new \Exception("Error : La característica $strCaracteristica, se encuentra en estado ".
                                         $objAdmiCaracteristica->getEstado());
                }

                $objInfoTareaCaracteristica = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findOneBy(array('tareaId'          => $intIdComunicacion,
                                          'caracteristicaId' => $objAdmiCaracteristica->getId()));

                if (is_object($objInfoTareaCaracteristica))
                {
                    $objInfoTareaCaracteristica->setFeModificacion(new \DateTime('now'));
                    $objInfoTareaCaracteristica->setUsrModificacion($strUser);
                    $objInfoTareaCaracteristica->setIpModificacion($strIp);
                }
                else
                {
                    $objInfoTareaCaracteristica = new InfoTareaCaracteristica();
                    $objInfoTareaCaracteristica->setTareaId(intval($intIdComunicacion));
                    $objInfoTareaCaracteristica->setDetalleId(intval($intIdDetalle));
                    $objInfoTareaCaracteristica->setCaracteristicaId($objAdmiCaracteristica->getId());
                    $objInfoTareaCaracteristica->setFeCreacion(new \DateTime('now'));
                    $objInfoTareaCaracteristica->setUsrCreacion($strUser);
                    $objInfoTareaCaracteristica->setIpCreacion($strIp);
                }

                $objInfoTareaCaracteristica->setValor(strtoupper($strValor));
                $objInfoTareaCaracteristica->setEstado($strEstado);

                $this->emSoporte->persist($objInfoTareaCaracteristica);
                $this->emSoporte->flush();
                $this->emSoporte->getConnection()->commit();

                $arrayRespuesta = array('status'  => 'ok',
                                        'message' => 'La característica se ingreso correctamente');
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error al ingresar la característica';

                if ($this->emSoporte->getConnection()->isTransactionActive())
                {
                    $this->emSoporte->getConnection()->rollback();
                    $this->emSoporte->getConnection()->close();
                }

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteProcesosService->putTareaCaracteristica',
                                                 $objException->getMessage(),
                                                 $strUser,
                                                 $strIp);

                $arrayRespuesta = array('status'  => 'fail',
                                        'message' => $strMessage);
            }
            return $arrayRespuesta;
       }

        /**
         * Método encargado de crear o actualizar la característica de una persona.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 05-06-2019
         *
         * @param $arrayParametros [
         *                              intIdPersonaEmpresaRol => Id de la InfoPersonaEmpresaRol.
         *                              strCaracteristica      => Descripción de la característica.
         *                              boolReiniciar          => Valor booleano que indica el reinicio de la característica.
         *                              strUsuarioCrea         => Usuario quien crea o modifica la característica.
         *                              strIpCrea              => Ip del usuario quien crea o modifica la característica.
         *                         ]
         * @return $arrayRespuesta
         */
        public function putInfoInfoPersonaEmpresaRolCarac($arrayParametros)
        {
            $intIdPersonaEmpresaRol = $arrayParametros['intIdPersonaEmpresaRol'];
            $strCaracteristica      = $arrayParametros['strCaracteristica'];
            $boolReiniciar          = $arrayParametros['boolReiniciar'];
            $strUsuarioCrea         = $arrayParametros['strUsuarioCrea'];
            $strIpCrea              = $arrayParametros['strIpCrea'];

            $this->emComercial->getConnection()->beginTransaction();

            try
            {
                $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                        ->findOneById($intIdPersonaEmpresaRol);

                $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                    ->findOneBy(array ('descripcionCaracteristica' => $strCaracteristica,
                                       'estado'                    => 'Activo'));

                if (is_object($objAdmiCaracteristica) && is_object($objInfoPersonaEmpresaRol))
                {
                    $objInfoPersonaEmpresaRolCarac = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRolCarac')
                            ->findOneBy(array ('caracteristicaId'    => $objAdmiCaracteristica->getId(),
                                               'personaEmpresaRolId' => $intIdPersonaEmpresaRol));

                    if (!is_object($objInfoPersonaEmpresaRolCarac))
                    {
                        $objInfoPersonaEmpresaRolCarac = new InfoPersonaEmpresaRolCarac();
                        $objInfoPersonaEmpresaRolCarac->setPersonaEmpresaRolId($objInfoPersonaEmpresaRol);
                        $objInfoPersonaEmpresaRolCarac->setCaracteristicaId($objAdmiCaracteristica);
                        $objInfoPersonaEmpresaRolCarac->setValor(1);
                        $objInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolCarac->setUsrCreacion($strUsuarioCrea);
                        $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpCrea);
                        $objInfoPersonaEmpresaRolCarac->setEstado('Activo');
                    }
                    else
                    {
                        $objInfoPersonaEmpresaRolCarac->setFeUltMod(new \DateTime('now'));
                        $objInfoPersonaEmpresaRolCarac->setUsrUltMod($strUsuarioCrea);
                        $objInfoPersonaEmpresaRolCarac->setIpCreacion($strIpCrea);

                        if ($boolReiniciar)
                        {
                            $objInfoPersonaEmpresaRolCarac->setFeCreacion(new \DateTime('now'));
                            $objInfoPersonaEmpresaRolCarac->setValor(1);
                        }
                        else
                        {
                            $intValor = $objInfoPersonaEmpresaRolCarac->getValor() == null ? 1 :
                                    intval($objInfoPersonaEmpresaRolCarac->getValor()) + 1;
                            $objInfoPersonaEmpresaRolCarac->setValor($intValor);
                        }
                    }

                    $this->emComercial->persist($objInfoPersonaEmpresaRolCarac);
                    $this->emComercial->flush();
                    $this->emComercial->commit();
                }
            }
            catch (\Exception $objException)
            {
                if ($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }

                $this->emComercial->close();

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteProcesosService->putInfoInfoPersonaEmpresaRolCarac',
                                                 $objException->getMessage(),
                                                 $strUsuarioCrea,
                                                 $strIpCrea);
            }
        }
    }
