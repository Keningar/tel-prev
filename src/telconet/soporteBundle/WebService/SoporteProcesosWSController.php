<?php

    namespace telconet\soporteBundle\WebService;

    use telconet\schemaBundle\DependencyInjection\BaseWSController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    /**
     * Clase que contiene las funciones necesarias para el funcionamiento de los WebService del módulo de Soporte.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 28-01-2019
     */
    class SoporteProcesosWSController extends BaseWSController
    {
        /**
         * Función que sirve para procesar las opciones que son solicitadas por Externos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 28-01-2019
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 30-04-2019 - Se implementa el método putTareaCaracteristica, encargado de ingresar la
         *                           característica de una tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.2 13-07-2019 - Se implementa el método putTiemposPlanificacionTarea, encargado de realizar el cálculo
         *                           de los tiempos de la tarea.
         *
         * @param  Request
         * @return Response
         */
        public function procesarAction(Request $objRequest)
        {
            $arrayData   = json_decode($objRequest->getContent(),true);
            $objResponse = new Response();

            if (isset($arrayData['source']))
            {
                $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);

                if (!$strToken)
                {
                    $arrayResponse = array ('status'  => $this->status['TOKEN'],
                                            'message' => $this->mensaje['TOKEN']);

                    return new Response(json_encode($arrayResponse));
                }
            }

            if (!isset($arrayData['op']) || empty($arrayData['op']))
            {
                $arrayResponse = array ('status'  => $this->status['NULL'],
                                        'message' => $this->mensaje['NULL']);

                return new Response(json_encode($arrayResponse));
            }

            switch ($arrayData['op'])
            {
                /********OPCIONES DE GET*************/
                case 'getInfoError':
                    $arrayResponse = $this->getInfoError($arrayData);
                    break;
                case 'getElementoZona':
                    $arrayResponse = $this->getElementoZona($arrayData);
                    break;
                case 'getTareasProcesos':
                    $arrayResponse = $this->getTareasProcesos($arrayData);
                    break;
                case 'getTareasCaracteristicas':
                    $arrayResponse = $this->getTareasCaracteristicas($arrayData);
                    break;
                /********OPCIONES DE PUT*************/
                case 'putReasignarCaso':
                    $arrayResponse = $this->putReasignarCaso($arrayData);
                    break;
                case 'putAsignarZonaPrestada':
                    $arrayResponse = $this->putAsignarZonaPrestada($arrayData);
                    break;
                case 'putTareaCaracteristica':
                    $arrayResponse = $this->putTareaCaracteristica($arrayData);
                    break;
                case 'putTiemposPlanificacionTarea':
                    $arrayResponse = $this->putTiemposPlanificacionTarea($arrayData);
                    break;
                default:
                    $arrayResponse = array ('status'  => $this->status['METODO'],
                                            'message' => $this->mensaje['METODO']);
            }

            $arrayResponse['token'] = $strToken;
            $objResponse->headers->set('Content-Type', 'application/json');
            $objResponse->setContent(json_encode($arrayResponse));
            return $objResponse;
        }

        /**
         * Método encargado de obtener los elementos zonificados.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 12-09-2018
         *
         * @param  Array $arrayParametros
         * @return Array $arrayRespuesta
         */
        private function getElementoZona($arrayParametros)
        {
            $emInfraestructura = $this->getDoctrine()->getManager("telconet_infraestructura");
            $objServiceUtil    = $this->get('schema.Util');
            $arrayData         = $arrayParametros['data'];
            $intIdZona         = $arrayData['idZona'];
            $strUsuario        = ($arrayParametros['user'] ? $arrayParametros['user'] : 'Telcos+');
            $strIp             = ($arrayParametros['ip']   ? $arrayParametros['ip']   : '127.0.0.1');
            $arrayRespuesta    = array();

            try
            {
                if(!isset($intIdZona) || empty($intIdZona) || !is_finite($intIdZona))
                {
                     throw new \Exception('Error : idZona Invalido');
                }

                $arrayInfoDetalleElemento = $emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                        ->findBy(array ("detalleNombre" => 'ZONA',
                                        "estado"        => 'Activo',
                                        'detalleValor'  => $intIdZona));

                if (empty($arrayInfoDetalleElemento) || count($arrayInfoDetalleElemento) < 1)
                {
                    throw new \Exception('Error : La consulta no devolvio resultado');
                }

                foreach($arrayInfoDetalleElemento as $objInfoDetalleElemento)
                {
                     $arrayIdElemento[] = $objInfoDetalleElemento->getElementoId();
                }

                $arrayRespuesta['status'] = 'ok';
                $arrayRespuesta['result'] = $arrayIdElemento;
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService getElementoZona';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteWSController.getElementoZona',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = $strMessage;
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de reasignar un caso.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 11-09-2018
         *
         * @param  Array $arrayData
         * @return Array $arrayRespuesta
         */
        private function putReasignarCaso($arrayData)
        {
            $objSoporteProcesoService = $this->get('soporte.SoporteProcesos');
            $objServiceUtil           = $this->get('schema.Util');
            $intIdCaso                = $arrayData['data']['idCaso'];
            $intInfoPersonaEmpresaRol = $arrayData['data']['idAsignado'];
            $strObservacion           = $arrayData['data']['observacion'];
            $strPrefijoEmpresa        = $arrayData['data']['prefijoEmpresa'];
            $strUsuario               = $arrayData['user'] ? $arrayData['user'] : 'Telcos+';
            $strIp                    = $arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1';

            try
            {
                if(!isset($intIdCaso) || empty($intIdCaso) || !is_finite($intIdCaso))
                {
                    throw new \Exception("Error : idCaso Invalido");
                }

                if(!isset($intInfoPersonaEmpresaRol) || empty($intInfoPersonaEmpresaRol) || !is_finite($intInfoPersonaEmpresaRol))
                {
                    throw new \Exception("Error : idAsignado Invalido");
                }

                if(!isset($strObservacion) || empty($strObservacion) || !is_string($strObservacion))
                {
                    throw new \Exception("Error : observacion Invalido");
                }

                if(!isset($strUsuario) || empty($strUsuario) || !is_string($strUsuario))
                {
                    throw new \Exception("Error : user Invalido");
                }

                if(!isset($strPrefijoEmpresa) || empty($strPrefijoEmpresa) || !is_string($strPrefijoEmpresa))
                {
                    throw new \Exception("Error : prefijoEmpresa Invalido");
                }

                $arrayRespuesta = $objSoporteProcesoService->putReasignarCaso(array ('intIdCaso'                => $intIdCaso,
                                                                                     'intInfoPersonaEmpresaRol' => $intInfoPersonaEmpresaRol,
                                                                                     'strObservacion'           => $strObservacion,
                                                                                     'strUsuario'               => $strUsuario,
                                                                                     'strIp'                    => $strIp,
                                                                                     'strPrefijoEmpresa'        => $strPrefijoEmpresa));
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService putReasignarCaso';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteWSController.putReasignarCaso',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = $strMessage;
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de obtener la información de la info_error.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 14-09-2018
         *
         * @param  $arrayData
         * @return $arrayRespuesta
         */
        private function getInfoError($arrayData)
        {
            $objServiceUtil     = $this->get('schema.Util');
            $intIdError         = $arrayData['data']['idError'];
            $strAplicacion      = $arrayData['data']['aplicacion'];
            $strProceso         = $arrayData['data']['proceso'];
            $strDetalleError    = $arrayData['data']['detalleError'];
            $strUsuarioCreacion = $arrayData['data']['usuarioCreacion'];
            $strIpCreacion      = $arrayData['data']['ipCreacion'];
            $strFeCreaIni       = $arrayData['data']['feCreaIni'];
            $strFeCreaFin       = $arrayData['data']['feCreaFin'];
            $strUsuario         = ($arrayData['user'] ? $arrayData['user'] : '127.0.0.1');
            $strIp              = ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1');

            try
            {
                if (!isset($intIdError) || empty($intIdError))
                {
                    if ((!isset($strFeCreaIni) || empty($strFeCreaIni)) || (!isset($strFeCreaFin) || empty($strFeCreaFin)))
                    {
                        throw new \Exception("Error : La feCreaIni y|o feCreaFin no pueden ser nulos");
                    }
                    else
                    {
                        $objDateIni      = new \DateTime($strFeCreaIni);
                        $objDateFin      = new \DateTime($strFeCreaFin);
                        $objDateInterval = $objDateIni->diff($objDateFin);

                        if ($objDateIni > $objDateFin)
                        {
                            throw new \Exception("Error : La feCreaIni no puede ser mayor a la feCreaFin");
                        }

                        if (($objDateInterval->y > 0) || ($objDateInterval->m > 0 && $objDateInterval->d > 0))
                        {
                            throw new \Exception("Error : La consulta no puede ser mayor a 1 mes");
                        }
                    }
                }

                $arrayRespuesta = $objServiceUtil->getInfoError(array ('intIdError'         => $intIdError,
                                                                       'strAplicacion'      => $strAplicacion,
                                                                       'strProceso'         => $strProceso,
                                                                       'strDetalleError'    => $strDetalleError,
                                                                       'strUsuarioCreacion' => $strUsuarioCreacion,
                                                                       'strIpCreacion'      => $strIpCreacion,
                                                                       'strFeCreaIni'       => $strFeCreaIni,
                                                                       'strFeCreaFin'       => $strFeCreaFin));
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService getInfoError';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteWSController.getInfoError',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = $strMessage;
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de asignar la zona prestada a una cuadrilla planificada por HAL.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 12-09-2018
         *
         * @param  $arrayData
         * @return $arrayRespuesta
         */
        private function putAsignarZonaPrestada($arrayData)
        {
            $objSoporteProcesoService = $this->get('soporte.SoporteProcesos');
            $objServiceUtil           = $this->get('schema.Util');
            $intIdCab                 = $arrayData['data']['idCuadrillaPlanifCab'];
            $intIdZonaPrestada        = $arrayData['data']['idZonaPrestada'];
            $strUsuario               = ($arrayData['user'] ? $arrayData['user'] : '127.0.0.1');
            $strIp                    = ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1');

            try
            {
                if (!isset($intIdCab) || empty($intIdCab) || !is_finite($intIdCab))
                {
                    throw new \Exception("Error : idCuadrillaPlanifCab Invalido");
                }

                $arrayRespuesta = $objSoporteProcesoService->putAsignarZonaPrestada(array ('intIdCab'          => $intIdCab,
                                                                                           'intIdZonaPrestada' => $intIdZonaPrestada,
                                                                                           'strUsuario'        => $strUsuario,
                                                                                           'strIp'             => $strIp));
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService putAsignarZonaPrestada';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteWSController.putAsignarZonaPrestada',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = $strMessage;
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de realizar la consulta de las tareas con sus respectivos procesos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 17-09-2018
         *
         * @param  $arrayData
         * @return $arrayRespuesta
         */
        private function getTareasProcesos($arrayData)
        {
            $objSoporteProcesoService = $this->get('soporte.SoporteProcesos');
            $objServiceUtil           = $this->get('schema.Util');
            $arrayIdTarea             = $arrayData['data']['listaIdTarea'];
            $arrayIdProceso           = $arrayData['data']['listaIdProceso'];
            $strEstadoTarea           = $arrayData['data']['estadoTarea'];
            $strEstadoProceso         = $arrayData['data']['estadoProceso'];
            $strUsuario               = $arrayData['user'] ? $arrayData['user'] : '127.0.0.1';
            $strIp                    = $arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1';

            try
            {
                if (!empty($arrayIdTarea) && !is_array($arrayIdTarea))
                {
                    throw new \Exception("Error : listaIdTarea Invalido");
                }

                if (!empty($arrayIdProceso) && !is_array($arrayIdProceso))
                {
                    throw new \Exception("Error : listaIdProceso Invalido");
                }

                if (!empty($strEstadoTarea) && !is_string($strEstadoTarea))
                {
                    throw new \Exception("Error : estadoTarea Invalido");
                }

                if (!empty($strEstadoProceso) && !is_string($strEstadoProceso))
                {
                    throw new \Exception("Error : estadoProceso Invalido");
                }

                $arrayRespuesta = $objSoporteProcesoService->getTareasProcesos(array ('arrayIdTarea'     => $arrayIdTarea,
                                                                                      'arrayIdProceso'   => $arrayIdProceso,
                                                                                      'strEstadoTarea'   => $strEstadoTarea,
                                                                                      'strEstadoProceso' => $strEstadoProceso));
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error al obtener los datos';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteWSController.getTareasProcesos',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = $strMessage;
            }
            return $arrayRespuesta;
        }

        /**
         * Función encargada de ingresar la característica de una tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 30-04-2019
         *
         * @param  $arrayParametros
         * @return $arrayRespuesta
         */
        private function putTareaCaracteristica($arrayParametros)
        {
            $objSoporteProcesos = $this->get('soporte.SoporteProcesos');
            $objServiceUtil     = $this->get('schema.Util');
            $arrayData          = $arrayParametros['data'];
            $strUser            = $arrayParametros['user'] ? $arrayParametros['user'] : 'Telcos+';
            $strIp              = $arrayParametros['ip']   ? $arrayParametros['ip']   : '127.0.0.1';

            try
            {
                if (!isset($arrayData['idComunicacion']) || empty($arrayData['idComunicacion']))
                {
                    throw new \Exception("Error : idComunicacion Inválido");
                }

                if (!isset($arrayData['caracteristica']) || empty($arrayData['caracteristica']))
                {
                    throw new \Exception("Error : caracteristica Inválido");
                }

                if (!isset($arrayData['valor']) || empty($arrayData['valor']))
                {
                    throw new \Exception("Error : valor Inválido");
                }

                if (!isset($arrayData['idDetalle']) || empty($arrayData['idDetalle']))
                {
                    throw new \Exception("Error : idDetalle Inválido");
                }

                $arrayDatos = array('intIdComunicacion' => $arrayData['idComunicacion'],
                                    'intIdDetalle'      => $arrayData['idDetalle'],
                                    'strCaracteristica' => $arrayData['caracteristica'],
                                    'strValor'          => $arrayData['valor'],
                                    'strEstado'         => 'Activo',
                                    'strUser'           => $strUser,
                                    'strIp'             => $strIp);

                $arrayRespuesta = $objSoporteProcesos->putTareaCaracteristica($arrayDatos);
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService putTareaCaracteristica';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteProcesosWSController->putTareaCaracteristica',
                                              $objException->getMessage(),
                                              $strUser,
                                              $strIp);

                $arrayRespuesta = array('status'  => 'fail',
                                        'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Función encargada de obtener las características de una tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 31-08-2018
         *
         * @param  $arrayParametros
         * @return $arrayRespuesta
         */
        private function getTareasCaracteristicas($arrayParametros)
        {
            $objServiceUtil = $this->get('schema.Util');
            $arrayData      = $arrayParametros['data'];
            $strUser        = $arrayParametros['user'] ? $arrayData['user'] : 'Telcos+';
            $strIp          = $arrayParametros['ip']   ? $arrayData['ip']   : '127.0.0.1';

            try
            {
                if (!isset($arrayData['listaIdComunicacion']) || empty($arrayData['listaIdComunicacion'])
                        || !is_array($arrayData['listaIdComunicacion']))
                {
                    throw new \Exception("Error : listaIdComunicacion Inválido ");
                }

                if (!isset($arrayData['listaCaracteristica']) || empty($arrayData['listaCaracteristica'])
                        || !is_array($arrayData['listaCaracteristica']))
                {
                    throw new \Exception("Error : listaCaracteristica Inválido ");
                }

                $arrayDatos = array ('arrayIdComunicacion' => $arrayData['listaIdComunicacion'],
                                     'arrayCaracteristica' => $arrayData['listaCaracteristica'],
                                     'strEstado'           => 'Activo',
                                     'strUser'             => $strUser,
                                     'strIp'               => $strIp);

                $emSoporte      = $this->getDoctrine()->getManager("telconet_soporte");
                $arrayRespuesta = $emSoporte->getRepository("schemaBundle:InfoTareaCaracteristica")
                        ->getTareasCaracteristicas($arrayDatos);
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService getTareasCaractAtenderAntes';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteProcesosWSController->getTareasCaracteristicas',
                                              $objException->getMessage(),
                                              $strUser,
                                              $strIp);

                $arrayRespuesta = array('status'  => 'fail',
                                        'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de realizar el cálculo de los tiempos de la tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 13-07-2019
         *
         * @param  Array $arrayData
         * @return Array $arrayRespuesta
         */
        private function putTiemposPlanificacionTarea($arrayData)
        {
            $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
            $serviceSoporte    = $this->get('soporte.SoporteService');
            $objServiceUtil    = $this->get('schema.Util');
            $intIdDetalle      = $arrayData['data']['idDetalle'];
            $strSolicitante    = $arrayData['data']['solicitante'];
            $intMinutosEmpresa = $arrayData['data']['mEmpresa'];
            $intMinutosCliente = $arrayData['data']['mCliente'];
            $strUsuario        = $arrayData['user'] ? $arrayData['user'] : 'Telcos+';
            $strIp             = $arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1';

            $emSoporte->beginTransaction();

            try
            {
                if (empty($intIdDetalle))
                {
                     throw new \Exception('Error : idDetalle Invalido');
                }

                $strEstadoActualTarea = $emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->getUltimoEstado($intIdDetalle);

                $arrayRespuesta = $serviceSoporte->calcularTiempoEstado(array('strEstadoActual'   => $strEstadoActualTarea,
                                                                              'intIdDetalle'      => $intIdDetalle,
                                                                              'strTipoReprograma' => $strSolicitante,
                                                                              'intMinutosEmpresa' => $intMinutosEmpresa,
                                                                              'intMinutosCliente' => $intMinutosCliente,
                                                                              'strUser'           => $strUsuario,
                                                                              'strIp'             => $strIp));

                if ($arrayRespuesta['status'] !== 'ok')
                {
                    throw new \Exception('Error : '.$arrayRespuesta['message']);
                }

                if ($emSoporte->getConnection()->isTransactionActive())
                {
                    $emSoporte->getConnection()->commit();
                }
            }
            catch (\Exception $objException)
            {
                if ($emSoporte->getConnection()->isTransactionActive())
                {
                    $emSoporte->getConnection()->rollback();
                }

                $strMessage = 'Error en el WebService putTiemposPlanificacionTarea';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'SoporteProcesosWSController.putTiemposPlanificacionTarea',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta = array("status" => 'fail','message' => $strMessage);
            }
            return $arrayRespuesta;
        }
    }
