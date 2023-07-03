<?php

    namespace telconet\soporteBundle\WebService;

    use telconet\schemaBundle\DependencyInjection\BaseWSController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\HttpFoundation\Request;

    /**
     * Clase que contiene las funciones necesarias para el funcionamiento de la integración con Telcos y TelcosSys DC.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 24-08-2018
     */
    class TelcoSysWSController extends BaseWSController
    {
        /**
         * Método encargado de receptar el Request realizado via POST desde el TelcoSys
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0
         * 
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 11-06-2019 - Se agrega el método getTareasClientes encargado de retornar todas las tareas de un cliente
         *                           en un rango de fechas establecido.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 11-06-2019 - Se agrega el método getValidarToken encargado de verificar la validez del token.
         *                           Dicho método será consumido por una aplicación externa y verificar si el token enviado
         *                           desde telcos es correcto.
         *
         * @param Request $objRequest
         * @return Response
         */
        public function procesarAction(Request $objRequest)
        {
            $arrayData      = json_decode($objRequest->getContent(),true);
            $arrayResponse  = array();
            $objResponse    = new Response();
            $strOp          = $arrayData['op'];

            if($arrayData['source'])
            {
                $strToken = $this->validateGenerateToken($arrayData['token'], $arrayData['source'], $arrayData['user']);

                if(empty($strToken))
                {
                    return new Response(json_encode(array('status'  => 403,
                                                          'mensaje' => "token invalido")
                        )
                    );
                }
            }

            switch($strOp)
            {
                case 'putCrearTareaTelcoSys':
                    $arrayResponse = $this->putCrearTareaTelcosSys($arrayData);
                    break;

                case 'updateEstadoTareaTelcoSys':
                    $arrayResponse = $this->updateEstadoTareaTelcoSys($arrayData);
                    break;

                case 'getPuntosClientesDataCenter';
                    $arrayResponse = $this->getPuntosClientesDataCenter($arrayData);
                    break;
                
                case 'getPuntosClientes';
                    $arrayResponse = $this->getPuntosClientes($arrayData);
                    break;

                case 'getTareasDataCenter';
                    $arrayResponse = $this->getTareasDataCenter($arrayData);
                    break;

                case 'getSeguimientosTareaTelcoSys';
                    $arrayResponse = $this->getSeguimientosTareaTelcoSys($arrayData);
                    break;

                case 'putIngresarSeguimiento';
                    $arrayResponse = $this->putIngresarSeguimiento($arrayData);
                    break;

                case 'putFile':
                    $arrayResponse = $this->putFile($arrayData);
                    break;

                case 'putCrearTarea':
                    $arrayResponse = $this->putCrearTarea($arrayData);
                    break;

                case 'putConfirmarTareaSysCloud':
                    $arrayResponse = $this->putConfirmarTareaSysCloud($arrayData);
                    break;

                case 'getDepartamentos':
                    $arrayResponse = $this->getDepartamentos($arrayData);
                    break;

                case 'getInfoDatosPersona':
                    $arrayResponse = $this->getInfoDatosPersona($arrayData);
                    break;

                case 'putNotificarTarea':
                    $arrayResponse = $this->putNotificarTarea($arrayData);
                    break;

                    case 'getValidarToken':

                $arrayResponse['status']  = 200;
                    $arrayResponse['mensaje'] = 'token valido';
                    if(empty($strToken))
                    {
                        $arrayResponse['status']  =  403;
                        $arrayResponse['mensaje'] = 'token invalido';
                    }
                    break;

                case 'putSubTarea':
                    $arrayResponse = $this->putSubTarea($arrayData);
                    break;

                case 'getTareasClientes':
                    $arrayResponse = $this->getTareasClientes($arrayData);
                    break;
                
                case 'getFilePath':
                    $arrayResponse = $this->getFilePath($arrayData);
                    break;

                case 'getInfoTarea':
                    $arrayResponse = $this->getInfoTarea($arrayData);
                    break;

                default :
                    $arrayResponse['status']     = 'ERROR';
                    $arrayResponse['mensaje']    = 'No existe la opción requerida';
                    $arrayResponse['resultado']  = null;
                    break;
            }

            if(isset($objResponse))
            {
                $arrayResponse['token'] = $strToken;
                $objResponse->headers->set('Content-Type', 'text/json');
                $objResponse->setContent(json_encode($arrayResponse));
            }

            return $objResponse;
        }

        /**
         * Método encargado de retornar los datos de una tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 29-12-2020
         *
         * @param  Array $arrayDatos
         * @return Array $arrayRespuesta
         */
        private function getInfoTarea($arrayDatos)
        {
            $serviceUtil     = $this->get('schema.Util');
            $arrayAuditoria  = $arrayDatos['dataAuditoria'];
            $arrayData       = $arrayDatos['data'];
            $strUsuario      = $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Telcos';
            $strIpUsuario    = $arrayAuditoria['ipCreacion']  ? $arrayAuditoria['ipCreacion']  : '127.0.0.1';

            try
            {
                if (!isset($arrayData['numeroTarea']) || empty($arrayData['numeroTarea']))
                {
                    throw new \Exception('Error : numeroTarea inválido.');
                }

                $arrayParametros["esConsulta"]         = 'S';
                $arrayParametros['serviceUtil']        = $serviceUtil;
                $arrayParametros["strUsuarioSolicita"] = $strUsuario;
                $arrayParametros["actividad"]          = $arrayData['numeroTarea'];
                $arrayParametros["ociCon"] = array('userSoporte' => $this->container->getParameter('user_soporte'),
                                                   'passSoporte' => $this->container->getParameter('passwd_soporte'),
                                                   'databaseDsn' => $this->container->getParameter('database_dsn'));

                $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
                $emComercial       = $this->getDoctrine()->getManager("telconet");
                $arrayResultTareas = $emSoporte->getRepository('schemaBundle:InfoDetalle')->reporteTareas($arrayParametros);
                $intCantidad       = $arrayResultTareas['total'] ? $arrayResultTareas['total'] : 0;
                $arrayCsrResult    = oci_fetch_array($arrayResultTareas['objCsrResult'], OCI_ASSOC + OCI_RETURN_NULLS);

                if (strtoupper($arrayResultTareas['status']) !== 'OK')
                {
                    $strMensageError = $arrayResultTareas['message'] ? $arrayResultTareas['message'] : 'Error al obtener los datos.';
                    throw new \Exception($strMensageError);
                }

                if (empty($arrayResultTareas) || $intCantidad < 1 || empty($arrayCsrResult))
                {
                    throw new \Exception('Error : La consulta no retorno datos.');
                }

                if (is_object($arrayCsrResult['OBSERVACION_HISTORIAL']))
                {
                    $arrayCsrResult['OBSERVACION_HISTORIAL'] = $arrayCsrResult['OBSERVACION_HISTORIAL']->load();
                }

                if (is_object($arrayCsrResult['OBSERVACION']))
                {
                    $arrayCsrResult['OBSERVACION'] = $arrayCsrResult['OBSERVACION']->load();
                }

                if (is_object($arrayCsrResult['NOMBRE_TAREA']))
                {
                    $arrayCsrResult['NOMBRE_TAREA'] = $arrayCsrResult['NOMBRE_TAREA']->load();
                }

                if (is_object($arrayCsrResult['NOMBRE_PROCESO']))
                {
                    $arrayCsrResult['NOMBRE_PROCESO'] = $arrayCsrResult['NOMBRE_PROCESO']->load();
                }

                $strUsrTareaHistorial    = $arrayCsrResult["USR_CREACION"] ? $arrayCsrResult["USR_CREACION"] : "";
                $strNombreActualizadoPor = '';
                $objEmpleado = $emComercial->getRepository('schemaBundle:InfoPersona')->getDatosPersonaPorLogin($strUsrTareaHistorial);
                if (is_object($objEmpleado))
                {
                    $strNombreActualizadoPor = sprintf("%s",$objEmpleado);
                }

                $arrayInfoTarea = array ('numeroTarea'          => $arrayCsrResult['NUMERO_TAREA'],
                                         'nombreProceso'        => $arrayCsrResult['NOMBRE_PROCESO'],
                                         'nombreTarea'          => $arrayCsrResult['NOMBRE_TAREA'],
                                         'departamentoAsignado' => $arrayCsrResult['ASIGNADO_NOMBRE'],
                                         'usuarioAsignado'      => $arrayCsrResult['REF_ASIGNADO_NOMBRE'],
                                         'actualizadoPor'       => $strNombreActualizadoPor,
                                         'estado'               => $arrayCsrResult['ESTADO'],
                                         'observacion'          => $arrayCsrResult['OBSERVACION'],
                                         'fechaCreacion'        => $arrayCsrResult['FE_CREACION_DETALLE'],
                                         'fechaAsignacion'      => $arrayCsrResult['FE_CREACION_ASIGNACION'],
                                         'fechaEstado'          => $arrayCsrResult['FE_CREACION'],
                                         'fechaSolicitada'      => $arrayCsrResult['FE_SOLICITADA']);

                $arrayRespuesta = array ('status' => 'ok', 'data' => $arrayInfoTarea);
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error al consultar los datos en el web-service getInfoTarea.';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $serviceUtil->insertError('Telcos+',
                                          'TelcoSysWSController->getInfoTarea',
                                           $objException->getMessage(),
                                           $strUsuario,
                                           $strIpUsuario);

                $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
            }

            return $arrayRespuesta;
        }

        /**
         * Método encargado de notificar la finalización de una tarea.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 24-01-2019
         *
         * @param  Array $arrayParametros
         * @return Array
         */
        private function putNotificarTarea($arrayParametros)
        {
            $arrayData          = $arrayParametros['data'];
            $arrayDataAuditoria = $arrayParametros['dataAuditoria'];

            return $this->get('soporte.ProcesoService')->notificarCambioEstadoSysCloud(
                    array ('intIdComunicacion' => $arrayData['numeroTarea'],
                           'strObservacion'    => $arrayData['observacion'],
                           'strFechaFinaliza'  => $arrayData['fechaFinaliza'],
                           'strHoraFinaliza'   => $arrayData['horaFinaliza'],
                           'strCodEmpresa'     => $arrayData['codEmpresa'],
                           'strUser'           => $arrayDataAuditoria['usrCreacion'],
                           'strIp'             => $arrayDataAuditoria['ipCreacion']));
        }

        /**
         * Método encargado de obtener los datos de una persona de acuerdo a su rol y empresa.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 24-01-2019
         *
         * @param  Array $arrayParametros
         * @return Array $arrayRespuesta
         */
        private function getInfoDatosPersona($arrayParametros)
        {
            $serviceSysCloud = $this->get('soporte.SysCloudService');
            $objServiceUtil  = $this->get('schema.Util');
            $arrayAuditoria  = $arrayParametros['dataAuditoria'];
            $arrayData       = $arrayParametros['data'];

            try
            {
                if (empty($arrayData['prefijoEmpresa']))
                {
                     throw new \Exception('Error : El atributo prefijoEmpresa no puede ser nulo');
                }

                if (empty($arrayData['loginEmpleado'])      &&
                    empty($arrayData['nombres'])            &&
                    empty($arrayData['nombreCanton'])       &&
                    empty($arrayData['nombreDepartamento']) &&
                    empty($arrayData['apellidos']))
                {
                     throw new \Exception('Error : Como minimo debe tener un filtro adicional a parte del '.
                             'atributo prefijoEmpresa para poder realizar la consulta');
                }

                $arrayRespuesta = $serviceSysCloud->getInfoDatosPersona(
                        array ('strRol'                     => 'Empleado',
                               'strPrefijo'                 => $arrayData['prefijoEmpresa'],
                               'strLogin'                   => $arrayData['loginEmpleado'],
                               'strNombres'                 => $arrayData['nombres'],
                               'strApellidos'               => $arrayData['apellidos'],
                               'strDepartamento'            => $arrayData['nombreDepartamento'],
                               'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                               'strEstadoPersonaEmpresaRol' => 'Activo',
                               'strNombreCanton'            => $arrayData['nombreCanton']));
                

                if ($arrayRespuesta['status'] === 'fail')
                {
                   throw new \Exception($arrayRespuesta['message']);
                }

                return $arrayRespuesta;
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error al consultar los datos';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }

                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->getInfoDatosPersona',
                                              $objException->getMessage(),
                                              $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Telcos',
                                              $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion']   : '127.0.0.1');

                return array ('status' => 'fail', 'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de confirmar si la tarea ya se creo en el sistema de Sys Cloud-Center.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 23-01-2019
         *
         * @param  Array $arrayParametros
         * @return Array
         */
        private function putConfirmarTareaSysCloud($arrayParametros)
        {
            $emSoporte         = $this->getDoctrine()->getManager("telconet_soporte");
            $emComercial       = $this->getDoctrine()->getManager("telconet");
            $objSoporteService = $this->get('soporte.SoporteService');
            $objServiceUtil    = $this->get('schema.Util');
            $arrayAuditoria    = $arrayParametros['dataAuditoria'];
            $arrayData         = $arrayParametros['data'];

            try
            {
                if (!isset($arrayParametros['dataAuditoria']) || !isset($arrayParametros['data']))
                {
                    throw new \Exception('Error : dataAuditoria o data no ha sido definida en el json de envio');
                }

                if (!isset($arrayData['numeroTarea']) || empty($arrayData['numeroTarea']))
                {
                    throw new \Exception('Error : numeroTarea invalido');
                }

                if (!isset($arrayData['valor']) || empty($arrayData['valor'])
                        || !in_array(strtoupper($arrayData['valor']), array('S','N')))
                {
                    throw new \Exception('Error : valor invalido');
                }

                //Obtenemos la característica TAREA_SYS_CLOUD_CENTER.
                $objAdmiCaracteristica = $emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array ('descripcionCaracteristica' => 'TAREA_SYS_CLOUD_CENTER',
                                           'estado'                    => 'Activo'));

                if (!is_object($objAdmiCaracteristica))
                {
                    throw new \Exception('Error : Característica Invalida');
                }

                //Verificamos si existe la tarea con la característica TAREA_SYS_CLOUD_CENTER.
                $objInfoTareaCaracteristica = $emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                    ->findOneBy(array ('tareaId'          => $arrayData['numeroTarea'],
                                       'caracteristicaId' => $objAdmiCaracteristica->getId()));

                $strOpcion = 'new';
                if (is_object($objInfoTareaCaracteristica))
                {
                    $strOpcion = 'edit';
                }

                $arrayResult = $objSoporteService->setTareaCaracteristica(
                        array ('ojbInfoTareaCaracteristica' => $objInfoTareaCaracteristica,
                               'intIdComunicacion'          => $arrayData['numeroTarea'],
                               'intCaracteristicaId'        => $objAdmiCaracteristica->getId(),
                               'strValor'                   => strtoupper($arrayData['valor']),
                               'strUsrCreacion'             => $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Telcos',
                               'strIpCreacion'              => $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion']   : '127.0.0.1',
                               'strEstado'                  => 'Activo',
                               'strOpcion'                  => $strOpcion));

                if (empty($arrayResult) || $arrayResult['mensaje'] === 'fail')
                {
                     throw new \Exception('Error : '. $arrayResult['descripcion']);
                }

                return array('status' => 'ok', 'message' => $arrayResult['descripcion']);
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error al confirmar';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = $objException->getMessage();
                }

                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->putConfirmarTareaSysCloud',
                                              $objException->getMessage(),
                                              $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Telcos',
                                              $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion']   : '127.0.0.1');

                return array ('status' => 'fail', 'message' => $strMessage);
            }
        }

        /**
         * Método encargado de crear una tarea en el telcos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 29-11-2018
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 01-01-2019 - En la validación del error se añade el insert para almacenar el json que se recibe vía WS.
         *
         * @param  $arrayParametros
         * @return $arrayResult
         */
        private function putCrearTarea($arrayParametros)
        {
            $objSoporteService = $this->get('soporte.SoporteService');
            $objServiceUtil    = $this->get('schema.Util');
            $emComercial       = $this->getDoctrine()->getManager("telconet");
            $arrayAuditoria    = $arrayParametros['dataAuditoria'];
            $arrayData         = $arrayParametros['data'];
            $strCodigo         = '';
            $strPrefijoEmpresa = 'TN';

            if (!isset($arrayParametros['dataAuditoria']) || !isset($arrayParametros['data']))
            {
                return array ('status'  => 'fail',
                              'message' => 'dataAuditoria o data no ha sido definida en el json de envio');
            }

            if (empty($arrayAuditoria) || empty($arrayData))
            {
                return array ('status'  => 'fail',
                              'message' => 'dataAuditoria o data no pueden estar vacios');
            }

            if (!isset($arrayData['nombreProceso']) || empty($arrayData['nombreProceso']))
            {
                return array ('status'  => 'fail',
                              'message' => 'nombreProceso Invalido');
            }

            if (!isset($arrayData['nombreTarea']) || empty($arrayData['nombreTarea']))
            {
                return array ('status'  => 'fail',
                              'message' => 'nombreTarea Invalido');
            }

            if (!isset($arrayData['observacion']) || empty($arrayData['observacion']))
            {
                return array ('status'  => 'fail',
                              'message' => 'observacion Invalido');
            }

            if (!isset($arrayData['horaSolicitada'])  || empty($arrayData['horaSolicitada']) ||
                !isset($arrayData['fechaSolicitada']) || empty($arrayData['fechaSolicitada']))
            {
                return array ('status'  => 'fail',
                              'message' => 'horaSolicitada y(o) fechaSolicitada Invalido');
            }

            $arrayFecha = explode('-', $arrayData['fechaSolicitada']);
            if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]))
            {
                return array ('status'  => 'fail',
                              'message' => 'Formato de fecha Invalido');
            }

            if (strtotime($arrayData['horaSolicitada']) === false)
            {
                return array ('status'  => 'fail',
                              'message' => 'Formato de hora Invalido');
            }

            if (!isset($arrayAuditoria['usrCreacion'])  || empty($arrayAuditoria['usrCreacion']))
            {
                return array ('status'  => 'fail',
                              'message' => 'usrCreacion Invalido');
            }

            if (isset($arrayData['prefijoEmpresa']) && $arrayData['prefijoEmpresa'] !== "")
            {
                $objEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneBy(
                    array('prefijo' =>  strtoupper(trim($arrayData['prefijoEmpresa'])) ));

                if (!is_object($objEmpresa))
                {
                    return array ('status'  => 'fail',
                                  'message' => 'Prefijo de empresa Invalido');
                }else
                {
                    $strPrefijoEmpresa =  strtoupper(trim($arrayData['prefijoEmpresa']));
                }
            }

            $objInfoPersona = $emComercial->getRepository("schemaBundle:InfoPersona")
                ->findOneByLogin($arrayAuditoria['usrCreacion']);

            if (!is_object($objInfoPersona) || !in_array($objInfoPersona->getEstado(), array('Activo','Pendiente','Modificado')))
            {
                return array ('status'  => 'fail',
                              'message' => 'El usuario de creación no existe en telcos o no se encuentra Activo..!!');
            }

            $strUsuarioAsigna  = $objInfoPersona->getNombres()." ".$objInfoPersona->getApellidos();
            $arrayDatosPersona = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                    ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                 'strPrefijo'                 => $strPrefijoEmpresa,
                                                 'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                 'strEstadoPersonaEmpresaRol' => 'Activo',
                                                 'strDepartamento'            => $arrayData['nombreDepartamento'],
                                                 'strLogin'                   => $arrayData['loginAsignado']));

            if ($arrayDatosPersona['status'] === 'fail')
            {
                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->putCrearTarea',
                                              $arrayDatosPersona['message'],
                                              $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'SysCloudCenter',
                                              $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion'] : '127.0.0.1');

                return array ('status'  => 'fail',
                              'message' => 'Error al obtener los datos del asignado, por favor comunicar a Sistemas..!!');
            }

            if ($arrayDatosPersona['status'] === 'ok' && empty($arrayDatosPersona['result']))
            {
                return array ('status'  => 'fail',
                              'message' => 'Los filtros para encontrar al empleado asignado son incorrectos '.
                                           'o el empleado no existe en telcos');
            }

            $strObservacion = $arrayData['observacion'];

            $arrayRespuesta = $objSoporteService->crearTareaCasoSoporte(
                    array ('intIdPersonaEmpresaRol' => $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'],
                           'intIdEmpresa'           => $arrayDatosPersona['result'][0]['idEmpresa'],
                           'strPrefijoEmpresa'      => $arrayDatosPersona['result'][0]['prefijoEmpresa'],
                           'strNombreTarea'         => $arrayData['nombreTarea'],
                           'strNombreProceso'       => $arrayData['nombreProceso'],
                           'strObservacionTarea'    => $strObservacion,
                           'strMotivoTarea'         => $strObservacion,
                           'strTipoAsignacion'      => 'empleado',
                           'strIniciarTarea'        => $arrayData['esAutomatico'],
                           'strTipoTarea'           => 'T',
                           'strTareaRapida'         => 'N',
                           'strFechaHoraSolicitada' => $arrayData['fechaSolicitada'].' '.$arrayData['horaSolicitada'],
                           'boolAsignarTarea'       => true,
                           "strAplicacion"          => 'telcoSys',
                           'strUsuarioAsigna'       => $strUsuarioAsigna,
                           'strUserCreacion'        => $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'SysCloudCenter',
                           'strIpCreacion'          => $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion'] : '127.0.0.1'));

            if ($arrayRespuesta['mensaje'] === 'fail')
            {
                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->putCrearTarea->crearTareaCasoSoporte',
                                              $strCodigo.'|'.$arrayRespuesta['descripcion'],
                                              $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'SysCloudCenter',
                                              $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion'] : '127.0.0.1');

                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->putCrearTarea->crearTareaCasoSoporte',
                                              $strCodigo.'|'.json_encode($arrayData),
                                              $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'SysCloudCenter',
                                              $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion'] : '127.0.0.1');

                return array ('status'  => 'fail',
                              'message' => 'Error al crear la tarea, por favor comunicar a Sistemas..!!');
            }

            $arrayResult['status']              = $arrayRespuesta['mensaje'];
            $arrayResult['numeroTarea']         = $arrayRespuesta['numeroTarea'];
            $arrayResult['idDetalleTarea']      = $arrayRespuesta['numeroDetalle'];
            $arrayResult['infomacionAdicional'] = $arrayRespuesta['infomacionAdicional'];
            return $arrayResult;
        }

        /**
         * Método encargado de almacenar los archivos que vienen vía WS
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 20-11-2018
         *
         * @param  $arrayData
         * @return $arrayRespuesta
         */
        private function putFile($arrayData)
        {
            $strFileBase64    = $arrayData['data']['file'];
            $strFileName      = $arrayData['data']['fileName'];
            $strFileExtension = $arrayData['data']['fileExtension'];
            $intNumeroTarea   = $arrayData['data']['numeroTarea'];
            $strNumeroCaso    = $arrayData['data']['numeroCaso'];
            $strOrigen        = $arrayData['data']['origen'];
            $strUsrCreacion   = $arrayData['dataAuditoria']['usrCreacion'];
            $strIpCreacion    = $arrayData['dataAuditoria']['ipCreacion'];

            $strIsInFileServer  = $arrayData['data']['isInFileServer'];
            $strPrefijoEmpresa  = $arrayData['data']['prefijoEmpresa'];
            $boolIsInFileServer = isset($strIsInFileServer) && !empty($strIsInFileServer);
            $boolPrefijoEmpresa = isset($strPrefijoEmpresa) && !empty($strPrefijoEmpresa);
            $strPrefijoEmpresa  = ($boolPrefijoEmpresa) ? $strPrefijoEmpresa : 'TN';

            
            if ((!$boolIsInFileServer) &&
                (!isset($arrayData['data']['file']) || empty ($strFileBase64)))
            {
                return array ('status'  => 'fail',
                              'message' => 'file Inválido');
            }

            if (!isset($arrayData['data']['fileName']) || empty ($strFileName))
            {
                return array ('status'  => 'fail',
                              'message' => 'fileName Inválido');
            }

            if (!isset($arrayData['data']['fileExtension']) || empty ($strFileExtension))
            {
                return array ('status'  => 'fail',
                              'message' => 'fileExtension Inválido');
            }

            if (!isset($arrayData['data']['origen']) || empty ($strOrigen))
            {
                return array ('status'  => 'fail',
                              'message' => 'origen Inválido');
            }

            if ($arrayData['data']['origen'] == 'T' && (!isset($arrayData['data']['numeroTarea']) || empty ($intNumeroTarea)))
            {
                return array ('status'  => 'fail',
                              'message' => 'numeroTarea Inválido');
            }

            if ($arrayData['data']['origen'] == 'C' && (!isset($arrayData['data']['numeroCaso']) || empty ($strNumeroCaso)))
            {
                return array ('status'  => 'fail',
                              'message' => 'numeroCaso Inválido');
            }

            try
            {
                $serviceProceso = $this->get('soporte.ProcesoService');

                return $serviceProceso->putFile(array ('strFileBase64'      => $strFileBase64,
                                                       'strFileName'        => $strFileName,
                                                       'strFileExtension'   => $strFileExtension,
                                                       'intNumeroTarea'     => $intNumeroTarea,
                                                       'strNumeroCaso'      => $strNumeroCaso,
                                                       'strOrigen'          => $strOrigen,
                                                       'strPrefijoEmpresa'  => $strPrefijoEmpresa,
                                                       'boolIsInFileServer' => $boolIsInFileServer,
                                                       'strUsuario'         => $strUsrCreacion ? $strUsrCreacion : 'Telcos+',
                                                       'strIp'              => $strIpCreacion ? $strIpCreacion :'127.0.0.1'));
            }
            catch (\Exception $objException)
            {
                return array ('status'  => 'fail',
                              'message' => $objException->getMessage());
            }
        }

        /**
         * Documentación para la función putIngresarSeguimiento
         *
         * Función encargada de ingresar los seguimiento del aplicativo Sys Cloud-Center a Telcos.
         *
         * @author Germán Valenzuela<gvalenzuela@telconet.ec>
         * @version 1.0 23-10-2018
         *
         * @param  $arrayData
         * @return $arrayRespuesta
         */
        public function putIngresarSeguimiento($arrayData)
        {
            $objSoporteService   = $this->get('soporte.SoporteService');
            $objServiceUtil      = $this->get('schema.Util');
            $emSoporte           = $this->getDoctrine()->getManager("telconet_soporte");
            $emComunicacion      = $this->getDoctrine()->getManager("telconet_comunicacion");
            $emComercial         = $this->getDoctrine()->getManager("telconet");
            $arrayParametrosData = $arrayData['data'];
            $arrayAuditoria      = $arrayData['dataAuditoria'];
            $arrayRespuesta      = array();

            try
            {
                if (!isset($arrayParametrosData['numeroTarea']) || empty($arrayParametrosData['numeroTarea']))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "numeroTarea Invalido";
                    return $arrayRespuesta;
                }

                if (!isset($arrayParametrosData['seguimiento']) || empty($arrayParametrosData['seguimiento']))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "seguimiento Invalido";
                    return $arrayRespuesta;
                }

                if (!isset($arrayParametrosData['ejecucionTarea']) || empty($arrayParametrosData['ejecucionTarea']))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "ejecucionTarea Invalido";
                    return $arrayRespuesta;
                }

                if (!isset($arrayParametrosData['nombreDepartamento']) || empty($arrayParametrosData['nombreDepartamento']))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "nombreDepartamento Invalido";
                    return $arrayRespuesta;
                }

                $objInfoComunicacion = $emComunicacion->getRepository("schemaBundle:InfoComunicacion")
                        ->find($arrayParametrosData['numeroTarea']);

                if (!is_object($objInfoComunicacion))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "El número de tarea ".$arrayParametrosData['numeroTarea']." no existe";
                    return $arrayRespuesta;
                }

                //Obtenemos el departamento
                $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                    ->findOneBy(array('nombreDepartamento' => $arrayParametrosData['nombreDepartamento'],
                                      'estado'             => 'Activo'));

                if (!is_object($objDepartamento))
                {
                    throw new \Exception("ERROR : El departamento ".$arrayParametrosData['nombreDepartamento']." no existe");
                }

                //obtener los datos y departamento de la persona por empresa
                $arrayDatos = $emComercial->getRepository('schemaBundle:InfoPersona')
                    ->getPersonaDepartamentoPorUserEmpresa($arrayAuditoria['usrCreacion'], 10);

                $arrayParametros = array('idEmpresa'            => 10,
                                         'prefijoEmpresa'       => 'TN',
                                         'idDetalle'            => $objInfoComunicacion->getDetalleId(),
                                         'seguimiento'          => urldecode($arrayParametrosData['seguimiento']),
                                         'strEjecucionTarea'    => $arrayParametrosData['ejecucionTarea'],
                                         'departamento'         => $objDepartamento->getId(),
                                         'empleado'             => $arrayDatos['NOMBRES']." ".$arrayDatos['APELLIDOS'],
                                         'usrCreacion'          => $arrayAuditoria['usrCreacion'],
                                         'ipCreacion'           => $arrayAuditoria['ipCreacion'],
                                         'strEnviaDepartamento' => "N");

                $arrayResultado = $objSoporteService->ingresarSeguimientoTarea($arrayParametros);
                $arrayRespuesta["status"]  = $arrayResultado['status'];
                $arrayRespuesta["message"] = $arrayResultado['mensaje'];
            }
            catch(\Exception $objException)
            {
                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController.putIngresarSeguimiento',
                                              $objException->getMessage(),
                                              ($arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Sys Cloud-Center'),
                                              ($arrayAuditoria['ipCreacion']  ? $arrayAuditoria['ipCreacion']   : '127.0.0.1'));

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = "Fallo en el método SoporteWSController.setTareasCaractAtenderAntes";
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de realizar la creación de las tareas automáticas venidas del app TelcoSys
         *
         * @author Allan Suarez <arsuarez@telconet.ec>
         * @version 1.0 24-08-2018
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 23-10-2018 - Se modifica el método cambiando el departamento por defecto
         *                           por el departamento enviado por el aplicativo Sys Cloud-Center
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.2 01-01-2019 - En la validación del error se añade el insert para almacenar el json que se recibe vía WS.
         *
         * @author Néstor Naula <nnaulal@telconet.ec>
         * @version 1.3 01-01-2019 - Se agrega el parámetro 'ciudad' para la creación de la tarea al jefe encargado del departamento,
         *                           adicional se agrega el parámetro 'nombreProceso' para asociar el proceso con la tarea
         *                           Y se hace NO obligatorio el parámetro del 'login' del cliente.
         * @since 1.2
         * 
         * @author Néstor Naula <nnaulal@telconet.ec>
         * @version 1.4 09-06-2020 - Se agrega el parámetro 'loginAsignado' para la asignación de la tare al usuario,
         *                           adicional se agrega el parámetro 'fechaSolicitada' para setear la hora de ejecución
         *                           de la tarea.
         * @since 1.3
         * 
         * @author Néstor Naula <nnaulal@telconet.ec>
         * @version 1.5 22-06-2020 - Se agrega la validación del departamento Activo o Modificado.
         * @since 1.4
         * 
         * @author Pedro Velez <psvelez@telconet.ec>
         * @version 1.6 13-08-2021 - Se agrega parametro a enviar en funcion crearTareaRetiroEquipoPorDemo para indicar q es 
         *                           una tarea creada desde SysCloud.
         * @since 1.5
         * 
         * @author David De La Cruz <ddelacruz@telconet.ec>
         * @version 1.7 26-08-2021 - Se reemplaza la consulta de los datos del proceso, para incluir filtro por codEmpresa
         * @since 16
         * 
         * @param  Array $arrayJson
         * @return Array $arrayRespuesta
         */
        private function putCrearTareaTelcosSys($arrayJson)
        {
            $arrayRespuesta     = array();
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
            $serviceCambiarPlan = $this->get('tecnico.InfoCambiarPlan');
            $serviceUtil        = $this->get('schema.Util');
            $arrayData          = $arrayJson['data'];
            $arrayDataAudit     = $arrayJson['dataAuditoria'];
            $strCodEmpresa      = '';
            $strPrefijoEmpresa  = 'TN';
            $strCodigo          = '';
            $strLoginAsignado   = $arrayData["loginAsignado"] ? $arrayData["loginAsignado"] : "";
            $strFechaSolicitada = $arrayData["fechaSolicitada"] ? $arrayData["fechaSolicitada"] : "";
            $strHoraSolicitada  = $arrayData["horaSolicitada"] ? $arrayData["horaSolicitada"] : "";

            try
            {
                $objEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo($strPrefijoEmpresa);

                if(is_object($objEmpresa))
                {
                    $strCodEmpresa = $objEmpresa->getId();
                }

                //Validación existencia del login en el Telcos
                $objPunto   = $emComercial->getRepository("schemaBundle:InfoPunto")->findOneByLogin($arrayData['login']);

                //Obtener y verificar que el proceso y la tarea enviada exista en el Telcos
                $arrayResultado = $emSoporte->getRepository("schemaBundle:AdmiProceso")
                    ->getProceso(array ('nombre'     => $arrayData['nombreProceso'],
                                        'estado'     => 'Activo',
                                        'codEmpresa' => $strCodEmpresa));                                 

                if($arrayResultado['status'] === 'fail')
                {
                    throw new \Exception('ERROR : Nombre del proceso no existe en el Telcos, por favor comunicarse con Sistemas');
                }

                $objTarea = $emSoporte->getRepository("schemaBundle:AdmiTarea")
                        ->findOneBy(array ('procesoId'   => $arrayResultado['proceso']['idProceso'],
                                           'nombreTarea' => $arrayData['nombreTarea'],
                                           'estado'      => 'Activo'));

                if(!is_object($objTarea))
                {
                    throw new \Exception('ERROR : Nombre de Tarea no existe en el Telcos, por favor comunicarse con Sistemas');
                }

                //Cantón
                $strNombreCanton = '';

                if(is_object($objPunto))
                {
                    $intIdOficina = $objPunto->getPuntoCoberturaId()->getOficinaId();
                    $objOficina   = $emComercial->getRepository("schemaBundle:InfoOficinaGrupo")->find($intIdOficina);
                }

                if(is_object($objOficina))
                {
                    $objCanton = $emComercial->getRepository("schemaBundle:AdmiCanton")->find($objOficina->getCantonId());

                    if(is_object($objCanton))
                    {
                        $strRegion = $objCanton->getProvinciaId()->getRegionId()->getNombreRegion();
                    }
                }
                
                $arrayParametros =  $emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('RELACION REGION CON CIUDAD PARA DATACENTER',
                                                        'COMERCIAL',
                                                        '',
                                                        $strRegion,
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        '',
                                                        $strCodEmpresa);
                
                if(!empty($arrayParametros))
                {
                    $strNombreCanton = $arrayParametros['valor1'];
                }

                if(!empty($arrayData['ciudad']))
                {
                    $strNombreCanton   =  $arrayData['ciudad'];
                }

                $intIdCanton      = 0;

                if(!empty($strNombreCanton))
                {
                    $objCanton = $emSoporte->getRepository("schemaBundle:AdmiCanton")->findOneByNombreCanton($strNombreCanton);

                    if(is_object($objCanton))
                    {
                        $intIdCanton     = $objCanton->getId();
                    }
                }

                if (!isset($arrayData['nombreDepartamento']) || empty($arrayData['nombreDepartamento']))
                {
                    throw new \Exception("ERROR : El nombreDepartamento no puede ser nulo");
                }

                $strNombreDepartamento = $arrayData['nombreDepartamento'];

                //Obtenemos el departamento
                $objDepartamento = $emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                    ->findOneBy(array('nombreDepartamento' => $strNombreDepartamento,
                                      'empresaCod'         => $strCodEmpresa,
                                      'estado'             => array('Activo','Modificado')));

                if (!is_object($objDepartamento))
                {
                    throw new \Exception("ERROR : El departamento $strNombreDepartamento no existe");
                }

                if(isset($strLoginAsignado) && !empty($strLoginAsignado))
                {
                    $objInfoPersonaAsig = $emComercial->getRepository("schemaBundle:InfoPersona")
                                                      ->findOneByLogin($strLoginAsignado);

                    if (!is_object($objInfoPersonaAsig) || !in_array($objInfoPersonaAsig->getEstado(), array('Activo','Pendiente','Modificado')))
                    {
                        return array ('status'  => 'fail',
                                      'message' => 'El usuario de asignación no existe en telcos o no se encuentra Activo..!!');
                    }

                    $strNombrePerAsigna  = $objInfoPersonaAsig->getNombres()." ".$objInfoPersonaAsig->getApellidos();
                    $intIdPersonaAsig    = $objInfoPersonaAsig->getId();
                    $arrayDatosPersona   = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                            ->getInfoDatosPersona(array ('strRol'                    => 'Empleado',
                                                                        'strPrefijo'                 => 'TN',
                                                                        'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                                        'strEstadoPersonaEmpresaRol' => 'Activo',
                                                                        'strDepartamento'            => $arrayData['nombreDepartamento'],
                                                                        'strLogin'                   => $arrayData['loginAsignado']));

                    if(!empty($arrayDatosPersona['result'][0]))
                    {
                        $intIdPerRolAsiga = $arrayDatosPersona['result'][0]['idPersonaEmpresaRol'];
                    }
                    else
                    {
                        return array ('status'  => 'fail'.print_r($arrayDatosPersona['result'][0]),
                        'message' => 'El usuario de asignación no existe en telcos o no se encuentra Activo..!!');

                    }
                }

                if (isset($strHoraSolicitada)  && !empty($strHoraSolicitada) &&
                    isset($strFechaSolicitada) && !empty($strFechaSolicitada))
                {
                    $arrayFecha = explode('-', $strFechaSolicitada);
                    if(count($arrayFecha) !== 3 || !checkdate($arrayFecha[1], $arrayFecha[2], $arrayFecha[0]))
                    {
                        return array ('status'  => 'fail',
                                      'message' => 'Formato de fecha Invalido');
                    }

                    if (strtotime($strHoraSolicitada) === false)
                    {
                        return array ('status'  => 'fail',
                                      'message' => 'Formato de hora Invalido');
                    }

                    $strFechaTiempo = $strFechaSolicitada." ".$strHoraSolicitada;
                    $strFechaGenSol =  new \DateTime($strFechaTiempo);
                }

                $strObservacion = '<i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;'.
                                  '<b style="color:green;">Sys Cloud-Center</b>: '.urldecode($arrayData['observacion']);

                $arrayParametrosGeneracionTarea['strObservacion']     = $strObservacion;
                $arrayParametrosGeneracionTarea['strUsrCreacion']     = $arrayDataAudit['usrCreacion'];
                $arrayParametrosGeneracionTarea['strIpCreacion']      = $arrayDataAudit['ipCreacion'];
                $arrayParametrosGeneracionTarea['intDetalleSolId']    = null;
                $arrayParametrosGeneracionTarea['strTipoAfectado']    = 'Cliente';
                $arrayParametrosGeneracionTarea['objPunto']           = $objPunto;
                $arrayParametrosGeneracionTarea['objDepartamento']    = $objDepartamento;
                $arrayParametrosGeneracionTarea['strCantonId']        = $intIdCanton;
                $arrayParametrosGeneracionTarea['strEmpresaCod']      = $strCodEmpresa;
                $arrayParametrosGeneracionTarea['strPrefijoEmpresa']  = $strPrefijoEmpresa;
                $arrayParametrosGeneracionTarea['intTarea']           = $objTarea;
                $arrayParametrosGeneracionTarea["strBanderaTraslado"] = "";
                $arrayParametrosGeneracionTarea["boolEnviaCorreo"]    = true;
                $arrayParametrosGeneracionTarea["esAutomatico"]       = $arrayData['esAutomatico'];
                $arrayParametrosGeneracionTarea["origen"]             = 'ws';
                $arrayParametrosGeneracionTarea["seguimiento"]        = $arrayData['observacion'];
                $arrayParametrosGeneracionTarea["strAplicacion"]      = 'telcoSys';
                $arrayParametrosGeneracionTarea["intIdTareaTelcoSys"] = $arrayData['idRequerimiento'];

                $arrayParametrosGeneracionTarea["strFechaSolicitada"]   = $strFechaGenSol;
                $arrayParametrosGeneracionTarea["strIdPersonaAsig"]     = $intIdPersonaAsig;
                $arrayParametrosGeneracionTarea["strNombrePersonaAsig"] = $strNombrePerAsigna;
                $arrayParametrosGeneracionTarea["strIdPerRolAsig"]      = $intIdPerRolAsiga;
                $arrayParametrosGeneracionTarea["strCreaTareaSys"]      = "S";


                $strNumeroTarea = $serviceCambiarPlan->crearTareaRetiroEquipoPorDemo($arrayParametrosGeneracionTarea);

                if(empty($strNumeroTarea))
                {
                    throw new \Exception('ERROR : No pudo ser generada la Tarea Automática, por favor comunicarse con Sistemas');
                }
                else
                {
                    $arrayRespuesta['status']    = 'OK';
                    $arrayRespuesta['mensaje']   = 'OK';
                    $arrayRespuesta['resultado'] = $strNumeroTarea;

                    return $arrayRespuesta;
                }
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error general en creación de Tarea automática, por favor comunicarse con Sistemas';

                if (strpos($objException->getMessage(),'ERROR : ') !== false)
                {
                    $strMessage = $objException->getMessage();
                }

                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $serviceUtil->insertError('Telcos+',
                                          'TelcoSysWSController->putCrearTareaTelcosSys()',
                                           $strCodigo.'|'.$objException->getMessage(),
                                           $arrayDataAudit['usrCreacion'],
                                           $arrayDataAudit['ipCreacion']);

                $serviceUtil->insertError('Telcos+',
                                          'TelcoSysWSController->putCrearTareaTelcosSys()',
                                           $strCodigo.'|'.json_encode($arrayData),
                                           $arrayDataAudit['usrCreacion'],
                                           $arrayDataAudit['ipCreacion']);

                $arrayRespuesta['status']    = 'ERROR';
                $arrayRespuesta['mensaje']   = $strMessage;
                $arrayRespuesta['resultado'] = null;

                return $arrayRespuesta;
            }
        }

        /**
         *
         * Método encargado de devolver el seguimiento dada una tarea desde el aplicativo TelcoSys
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 13-09-2018
         *
         * @param  Array $arrayJson
         * @return Array
         */
        private function getSeguimientosTareaTelcoSys($arrayJson)
        {
            $arrayRespuesta     = array();
            $arrayResultSeg     = array();
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
            $emComunicacion     = $this->getDoctrine()->getManager("telconet_comunicacion");
            $serviceUtil        = $this->get('schema.Util');
            $arrayData          = $arrayJson['data'];
            $arrayDataAudit     = $arrayJson['dataAuditoria'];
            $strCodEmpresa      = '';
            $strPrefijoEmpresa  = 'TN';
            $intIdDetalle       = 0;

            try
            {
                $objTarea = $emComunicacion->getRepository("schemaBundle:InfoComunicacion")->find($arrayData['idTarea']);

                if(is_object($objTarea))
                {
                    $objDetalle = $emSoporte->getRepository("schemaBundle:InfoDetalle")->find($objTarea->getDetalleId());

                    if(is_object($objDetalle))
                    {
                        $intIdDetalle = $objDetalle->getId();
                    }
                }
                else
                {
                    $arrayRespuesta['status']    = 'ERROR';
                    $arrayRespuesta['mensaje']   = 'No existe referencia de la Tarea enviada';
                    $arrayRespuesta['resultado'] = array();
                    return $arrayRespuesta;
                }

                $objEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")->findOneByPrefijo($strPrefijoEmpresa);

                if(is_object($objEmpresa))
                {
                    $strCodEmpresa = $objEmpresa->getId();
                }

                //obtener los seguimientos de un detalle
                $arraySeguimientos = $emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                    ->findBy(array("detalleId" => $intIdDetalle),
                             array("id"        => "DESC"));

                foreach($arraySeguimientos as $objSeguimiento)
                {
                    $strObservacion    = $objSeguimiento->getObservacion();
                    $strUsrCreacion    = $objSeguimiento->getUsrCreacion();
                    $objFeCreacion     = $objSeguimiento->getFeCreacion();
                    $arrayDatosUsuario = $emComercial->getRepository('schemaBundle:InfoPersona')
                        ->getPersonaDepartamentoPorUserEmpresa($strUsrCreacion, $strCodEmpresa);

                    $arrayResultSeg[] = array('observacion'  => $strObservacion,
                                              'usrCreacion'  => $strUsrCreacion,
                                              'departamento' => $arrayDatosUsuario['NOMBRE_DEPARTAMENTO'],
                                              'feCreacion'   => date_format($objFeCreacion, 'd-m-Y G:i'));
                }

                $arrayRespuesta['status']    = 'OK';
                $arrayRespuesta['mensaje']   = 'OK';
                $arrayRespuesta['resultado'] = $arrayResultSeg;
            }
            catch(\Exception $ex)
            {
                $serviceUtil->insertError('Telcos+',
                                          'TelcoSysWSController->getSeguimientosTareaTelcoSys()',
                                          'Error -> '.$ex->getMessage(),
                                           $arrayDataAudit['usrCreacion'],
                                           $arrayDataAudit['ipCreacion']);

                $arrayRespuesta['status']    = 'ERROR';
                $arrayRespuesta['mensaje']   = 'No se pudo obtener los seguimientos de la tarea, notificar a Sistemas';
                $arrayRespuesta['resultado'] = array();
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de realizar la gestión de acuerdo al cambio de estado generado desde el aplicativo TelcoSys.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 22-10-2018
         *
         * @param  Array $arrayInformacion
         * @return Array $arrayRespuesta
         */
        private function updateEstadoTareaTelcoSys($arrayInformacion)
        {
            $serviceProceso = $this->get('soporte.ProcesoService');
            $arrayRespuesta = $serviceProceso->gestionarTareasTelcoSys($arrayInformacion);
            return $arrayRespuesta;
        }

        /**
         * Método encargado de devolver los puntos ligados a clientes que contengan servicios con Data Center
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 07-11-2018
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 19-03-2019 - Se agrega los parámetros listaEstadoPunto y listaEstadoServicio para
         *                           filtrar los servicios y puntos por estado.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.2 05-01-2021 - Se parametriza el grupo de productos para obtener los clientes de data-center.
         *
         * @param  Array $arrayData
         * @return Array $arrayRespuesta
         */
        private function getPuntosClientesDataCenter($arrayData)
        {
            $serviceUtil     = $this->get('schema.Util');
            $arrayAuditoria  = $arrayData['dataAuditoria'];
            $arrayParametros = $arrayData['data'];
            $emComercial     = $this->getDoctrine()->getManager("telconet");
            $emGeneral       = $this->getDoctrine()->getManager('telconet_general');
            $strUser         = $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Telcos+';
            $strIp           = $arrayAuditoria['ipCreacion']  ? $arrayAuditoria['ipCreacion']  : '127.0.0.1';

            try
            {
                if (isset($arrayParametros['listaEstadoPunto']) && !empty($arrayParametros['listaEstadoPunto']) &&
                        !is_array($arrayParametros['listaEstadoPunto']))
                {
                    throw new \Exception('Error : listaEstadoPunto Inválido');
                }

                if (isset($arrayParametros['listaEstadoServicio']) && !empty($arrayParametros['listaEstadoServicio']) &&
                        !is_array($arrayParametros['listaEstadoServicio']))
                {
                    throw new \Exception('Error : listaEstadoServicio Inválido');
                }

                $objInfoEmpresaGrupo = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")
                        ->findOneBy(array ('prefijo' => 'TN',
                                           'estado'  => 'Activo'));

                if (!is_object($objInfoEmpresaGrupo))
                {
                    throw new \Exception('Error : La empresa no existe');
                }

                //Obtenemos los grupos de productor que se encuentran parametrizados.
                $arrayGrupoProducto    = array('DATACENTER');
                $arrayAdmiParametroDet = $emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->get("GRUPO_PRODUCTOS_DC","","","","","","","","","");

                if (!empty($arrayAdmiParametroDet) && count($arrayAdmiParametroDet) > 0)
                {
                    $arrayGrupoProducto = array();
                    foreach ($arrayAdmiParametroDet as $arrayDatos)
                    {
                        $arrayGrupoProducto[] = $arrayDatos['valor1'];
                    }
                }

                $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")
                        ->getArrayPuntosClientesPorGrupoProducto(array ('intIdEmpresa'        => $objInfoEmpresaGrupo->getId(),
                                                                        'arrayGrupoProducto'  => $arrayGrupoProducto,
                                                                        'strDescripcionRol'   => 'Cliente',
                                                                        'strEstado'           => array('Activo','Modificado'),
                                                                        'strRazonSocial'      => $arrayParametros['razonSocial'],
                                                                        'strProducto'         => $arrayParametros['producto'],
                                                                        'strPuntoCobertura'   => $arrayParametros['puntoCobertura'],
                                                                        'strOficina'          => $arrayParametros['oficina'],
                                                                        'strLogin'            => $arrayParametros['login'],
                                                                        'arrayEstadoPunto'    => $arrayParametros['listaEstadoPunto'],
                                                                        'arrayEstadoServicio' => $arrayParametros['listaEstadoServicio']
                                                                       ));
            }
            catch(\Exception $objException)
            {
                $strMessage = 'Error en el WebService getPuntosClientesDataCenter, si el problema persiste comunique a Sistemas.';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }
                $serviceUtil->insertError('Telcos+',
                                          'TelcoSysWSController->getPuntosClientesDataCenter',
                                           $objException->getMessage(),
                                           $strUser,
                                           $strIp);

                $arrayRespuesta = array('status' => 'fail','message' => $strMessage);
            }
            return $arrayRespuesta;
        }
        
        /**
        * Método encargado de devolver los puntos ligados a clientes que contengan servicios de todo tipo.
        *
        * @author Karen Rodríguez <kyrodriguez@telconet.ec>
        * @version 1.0 15-08-2019
        *
        * @param  Array $arrayData
        * @return Array $arrayRespuesta
        */
        private function getPuntosClientes($arrayData)
        {
            $serviceUtil     = $this->get('schema.Util');
            $arrayAuditoria  = $arrayData['dataAuditoria'];
            $arrayParametros = $arrayData['data'];
            $emComercial     = $this->getDoctrine()->getManager("telconet");
            $strUser         = $arrayAuditoria['usrCreacion'] ? $arrayAuditoria['usrCreacion'] : 'Telcos+';
            $strIp           = $arrayAuditoria['ipCreacion'] ? $arrayAuditoria['ipCreacion'] : '127.0.0.1';

            try
            {
                if (isset($arrayParametros['listaEstadoPunto']) && !empty($arrayParametros['listaEstadoPunto']) &&
                        !is_array($arrayParametros['listaEstadoPunto']))
                {
                    throw new \Exception('Error : listaEstadoPunto Inválido');
                }

                if (isset($arrayParametros['listaEstadoServicio']) && !empty($arrayParametros['listaEstadoServicio']) &&
                        !is_array($arrayParametros['listaEstadoServicio']))
                {
                    throw new \Exception('Error : listaEstadoServicio Inválido');
                }

                $objInfoEmpresaGrupo = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")
                        ->findOneBy(array ('prefijo' => 'TN',
                                           'estado'  => 'Activo'));

                if (!is_object($objInfoEmpresaGrupo))
                {
                    throw new \Exception('Error : La empresa no existe');
                }

                $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")
                        ->getArrayPuntosClientesPorGrupoProducto(array ('intIdEmpresa'        => $objInfoEmpresaGrupo->getId(),
                                                                        'arrayGrupoProducto'  => null,
                                                                        'strDescripcionRol'   => 'Cliente',
                                                                        'strEstado'           => array('Activo','Modificado'),
                                                                        'strRazonSocial'      => $arrayParametros['razonSocial'],
                                                                        'strProducto'         => $arrayParametros['producto'],
                                                                        'strPuntoCobertura'   => $arrayParametros['puntoCobertura'],
                                                                        'strOficina'          => $arrayParametros['oficina'],
                                                                        'strLogin'            => $arrayParametros['login'],
                                                                        'arrayEstadoPunto'    => $arrayParametros['listaEstadoPunto'],
                                                                        'arrayEstadoServicio' => $arrayParametros['listaEstadoServicio']
                                                                       ));
            }
            catch(\Exception $objException)
            {
                $strMessage = 'Error en el WebService getPuntosClientes, si el problema persiste comunique a Sistemas.';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ', $objException->getMessage())[1];
                }
                $serviceUtil->insertError('Telcos+',
                                          'TelcoSysWSController->getPuntosClientes',
                                           $objException->getMessage(),
                                           $strUser,
                                           $strIp);

                $arrayRespuesta = array('status' => 'fail','message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de realizar la consulta de las tareas con sus respectivos procesos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 31-10-2018
         * 
         * @author David De La Cruz <ddelacruz@telconet.ec>
         * @version 1.1 26-08-2021 - Se actualiza para incluir filtro por CodEmpresa
         *
         * @return Array $arrayRespuesta
         */
        private function getTareasDataCenter($arrayData)
        {
            $objSysCloudService = $this->get('soporte.SysCloudService');
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $objServiceUtil     = $this->get('schema.Util');
            $arrayIdTarea       = $arrayData['data']['listaIdTarea'];
            $arrayIdProceso     = $arrayData['data']['listaIdProceso'];
            $strNombreTarea     = $arrayData['data']['nombreTarea'];
            $strNombreProceso   = $arrayData['data']['nombreProceso'];
            $strEstadoTarea     = $arrayData['data']['estadoTarea'];
            $strEstadoProceso   = $arrayData['data']['estadoProceso'];
            $strUsuario         = ($arrayData['user'] ? $arrayData['user'] : '127.0.0.1');
            $strIp              = ($arrayData['ip']   ? $arrayData['ip']   : '127.0.0.1');
            $strPrefijoEmpresa  = isset($arrayData['data']['prefijoEmpresa']) ? $arrayData['data']['prefijoEmpresa'] : 'TN';
            $boolFiltraEmpresa  = true;

            try
            {
                if (!empty($arrayIdTarea) && !is_array($arrayIdTarea))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "listaIdTarea Invalido";
                    return $arrayRespuesta;
                }

                if (!empty($arrayIdProceso) && !is_array($arrayIdProceso))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "listaIdProceso Invalido";
                    return $arrayRespuesta;
                }

                if (!empty($strNombreTarea) && !is_string($strNombreTarea))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "nombreTarea Invalido";
                    return $arrayRespuesta;
                }

                if (!empty($strNombreProceso) && !is_string($strNombreProceso))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "nombreProceso Invalido";
                    return $arrayRespuesta;
                }

                if (!empty($strEstadoTarea) && !is_string($strEstadoTarea))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "estadoTarea Invalido";
                    return $arrayRespuesta;
                }

                if (!empty($strEstadoProceso) && !is_string($strEstadoProceso))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "estadoProceso Invalido";
                    return $arrayRespuesta;
                }

                if (!empty($strPrefijoEmpresa) && !is_string($strPrefijoEmpresa))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "prefijoEmpresa Invalido";
                    return $arrayRespuesta;
                }
                else
                {
                    $objEmpresa = $emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")
                                                ->findOneByPrefijo($strPrefijoEmpresa);

                    if(is_object($objEmpresa))
                    {
                        $strCodEmpresa = $objEmpresa->getId();
                    }
                }

                $arrayRespuesta = $objSysCloudService->getTareasProcesos(array ('arrayIdTarea'    => $arrayIdTarea,
                                                                               'arrayIdProceso'   => $arrayIdProceso,
                                                                               'strNombreTarea'   => $strNombreTarea,
                                                                               'strNombreProceso' => $strNombreProceso,
                                                                               'strEstadoTarea'   => $strEstadoTarea,
                                                                               'strEstadoProceso' => $strEstadoProceso,
                                                                               'strCodEmpresa'    => $strCodEmpresa,
                                                                               'boolFiltraEmpresa'=> $boolFiltraEmpresa));
            }
            catch (\Exception $objException)
            {
                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController.getTareasDataCenter',
                                              $objException->getMessage(),
                                              $strUsuario,
                                              $strIp);

                $arrayRespuesta["status"]  = 'fail';
                $arrayRespuesta["message"] = "Error en el método getTareasDataCenter";
            }

            return $arrayRespuesta;
        }

        /**
         * Método encargado de obtener los departamentos.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 14-12-2018
         *
         * @param  Array $arrayData
         * @return Array $arrayRespuesta
         */
        private function getDepartamentos($arrayData)
        {
            $objSysCloudService = $this->get('soporte.SysCloudService');
            $objServiceUtil     = $this->get('schema.Util');
            $strUser            = $arrayData['dataAuditoria']['usrCreacion'];
            $strIp              = $arrayData['dataAuditoria']['ipCreacion'];

            try
            {
                $arrayParametros = array ('intIdArea'             => $arrayData['data']['idArea'],
                                          'intIdDepartamento'     => $arrayData['data']['idDepartamento'],
                                          'strNombreArea'         => $arrayData['data']['nombreArea'],
                                          'strNombreDepartamento' => $arrayData['data']['nombreDepartamento'],
                                          'strEstadoArea'         => $arrayData['data']['estadoArea'],
                                          'strEstadoDepartamento' => $arrayData['data']['estadoDepartamento'],
                                          'strPrefijoEmpresa'     => $arrayData['data']['prefijoEmpresa']);

                $arrayRespuesta = $objSysCloudService->getDepartamentos($arrayParametros);

                if (!empty($arrayRespuesta['exception']))
                {
                    $objServiceUtil->insertError('Telcos+',
                                                 $arrayRespuesta['exception']['method'],
                                                 $arrayRespuesta['exception']['message'],
                                                 $strUser ? $strUser : 'Telcos+',
                                                 $strIp   ? $strIp   : '127.0.0.1');
                }

                return $arrayRespuesta['data'];
            }
            catch (\Exception $objException)
            {
                $objServiceUtil->insertError('Telcos+',
                                             'SoporteWSController.getDepartamentos',
                                              $objException->getMessage(),
                                              $strUser ? $strUser : 'Telcos+',
                                              $strIp   ? $strIp   : '127.0.0.1');

                return array ("status" => 'fail', "mensaje" => 'Error en el WebService');
            }
        }

        /**
         * Método encargado de crear Sub-Tareas
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 14-02-2019
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 01-01-2019 - En la validación del error se añade el insert para almacenar el json que se recibe vía WS.
         *
         * @param  Array $arrayParametros
         * @return Array $arrayRespuesta
         */
        private function putSubTarea($arrayParametros)
        {
            $emSoporte          = $this->getDoctrine()->getManager("telconet_soporte");
            $emComercial        = $this->getDoctrine()->getManager("telconet");
            $objSysCloudService = $this->get('soporte.SysCloudService');
            $objServiceUtil     = $this->get('schema.Util');
            $arrayData          = $arrayParametros['data'];
            $strUser            = $arrayParametros['dataAuditoria']['usrCreacion'];
            $strIp              = $arrayParametros['dataAuditoria']['ipCreacion'];
            $strCodigo          = '';

            try
            {
                $objAdmiProceso = $emSoporte->getRepository("schemaBundle:AdmiProceso")
                        ->findOneBy(array ('nombreProceso' => $arrayData['nombreProceso'],
                                           'estado'        => 'Activo'));

                if (!is_object($objAdmiProceso))
                {
                    throw new \Exception('Error : El nombre del proceso no existe');
                }

                $objAdmiTarea = $emSoporte->getRepository("schemaBundle:AdmiTarea")
                        ->findOneBy(array ('procesoId'   => $objAdmiProceso->getId(),
                                           'nombreTarea' => $arrayData['nombreTarea'],
                                           'estado'      => 'Activo'));

                if (!is_object($objAdmiTarea))
                {
                    throw new \Exception('Error : El nombre de la tarea no existe');
                }

                $arrayDatosPersonaAsigna = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                        ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                     'strPrefijo'                 => 'TN',
                                                     'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                     'strEstadoPersonaEmpresaRol' => 'Activo',
                                                     'strLogin'                   => $strUser));

                if ($arrayDatosPersonaAsigna['status'] == 'fail')
                {
                    throw new \Exception($arrayDatosPersonaAsigna['message']. ' - PersonaAsigna');
                }

                $arrayResultAsigna       = $arrayDatosPersonaAsigna['result'];
                $strNombresPersonaAsigna = $arrayResultAsigna[0]['nombres'].' '.$arrayResultAsigna[0]['apellidos'];

                if (!isset($arrayData['asignado']) || empty($arrayData['asignado']))
                {
                   throw new \Exception("Error : Atributo (asignado) Inválido");
                }

                $arrayDatosPersonaAsignado = $emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                        ->getInfoDatosPersona(array ('intIdPersonaEmpresaRol'     => $arrayData['asignado'],
                                                     'strEstadoPersona'           => array('Activo','Pendiente','Modificado'),
                                                     'strEstadoPersonaEmpresaRol' => 'Activo'));

                if ($arrayDatosPersonaAsignado['status'] == 'fail')
                {
                    throw new \Exception($arrayDatosPersonaAsignado['message']. ' - PersonaAsignado');
                }

                $arrayResultAsignado       = $arrayDatosPersonaAsignado['result'];
                $strNombresPersonaAsignado = $arrayResultAsignado[0]['nombres'].' '.$arrayResultAsignado[0]['apellidos'];

                $strObservacion = '<i class="fa fa-tasks" aria-hidden="true"></i>&nbsp;'.
                              '<b style="color:green;">Sys Cloud-Center</b>: '.urldecode($arrayData['observacion']);

                $arrayRespuesta = $objSysCloudService->putSubTarea(array (
                    'intIdComunicacion'          => $arrayData['numeroTareaPadre'],
                    'strFechaSolicitada'         => $arrayData['fechaSolicitada'],
                    'strHoraSolicitada'          => $arrayData['horaSolicitada'],
                    'strObservacion'             => $strObservacion,
                    'strTipoAsignacion'          => 'EMPLEADO',
                    'intIdTarea'                 => $objAdmiTarea->getId(),
                    'intIdAsignado'              => $arrayResultAsignado[0]['idDepartamento'],
                    'strNombreAsignado'          => $arrayResultAsignado[0]['nombreDepartamento'],
                    'intIdRefAsignado'           => $arrayResultAsignado[0]['idPersona'],
                    'strRefAsignadoNombre'       => $strNombresPersonaAsignado,
                    'intIdPersonaEmpRolAsignado' => $arrayResultAsignado[0]['idPersonaEmpresaRol'],
                    'strPrefijoEmpresaAsignado'  => $arrayResultAsignado[0]['prefijoEmpresa'],
                    'intIdDeparAsigna'           => $arrayResultAsigna[0]['idDepartamento'],
                    'intIdPersonaEmpRolAsigna'   => $arrayResultAsigna[0]['idPersonaEmpresaRol'],
                    'strCodEmpresaAsigna'        => $arrayResultAsigna[0]['idEmpresa'],
                    'strNombresEmpleadoAsigna'   => $strNombresPersonaAsigna,
                    'strUser'                    => $strUser,
                    'strAplicacion'              => 'telcoSys',
                    'strIp'                      => $strIp));
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error en el WebService putSubTarea';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ',$objException->getMessage())[1];
                }

                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $objServiceUtil->insertError('Telcos+',
                                             'ProcesoService->putSubTarea',
                                              $strCodigo.'|'.$objException->getMessage(),
                                              $strUser,
                                              $strIp);

                $objServiceUtil->insertError('Telcos+',
                                             'ProcesoService->putSubTarea',
                                              $strCodigo.'|'.json_encode($arrayData),
                                              $strUser,
                                              $strIp);

                $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
            }
            return $arrayRespuesta;
        }


        private function getFilePath($arrayData)
        {
            $strApp            = $arrayData['data']['app'];
            $strModulo         = $arrayData['data']['modulo'];
            $strSubModulo      = $arrayData['data']['submodulo'];
            $strPrefijoEmpresa = $arrayData['data']['prefijoEmpresa'];
            $strModo         = $arrayData['data']['modo'];

            try
            {

                if (!empty($strApp) && !is_string($strApp))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "app Inválido";
                    return $arrayRespuesta;
                }
                if (!empty($strModulo) && !is_string($strModulo))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "módulo Inválido";
                    return $arrayRespuesta;
                }
                if (!empty($strSubModulo) && !is_string($strSubModulo))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "submódulo Inválido";
                    return $arrayRespuesta;
                }
                if (!empty($strPrefijoEmpresa) && !is_string($strPrefijoEmpresa))
                {
                    $arrayRespuesta["status"]  = 'fail';
                    $arrayRespuesta["message"] = "prefijoEmpresa Inválido";
                    return $arrayRespuesta;
                }

                $serviceProceso = $this->get('soporte.ProcesoService');

                return $serviceProceso->getFilePath(array('strApp'            => $strApp,
                                                          'strModulo'         => $strModulo,
                                                          'strSubModulo'      => $strSubModulo,
                                                          'strPrefijoEmpresa' => $strPrefijoEmpresa,
                                                          'strModo'           => $strModo));
            }
            catch (\Exception $objException)
            {
                return array ('status'  => 'fail',
                              'message' => $objException->getMessage());
            }
        }

        /**
         * Función encargada de devolver todas las tareas creadas de un cliente.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 16-07-2019
         *
         * @param  Array $arrayParametros
         * @return Array $arrayRespuesta
         */
        private function getTareasClientes($arrayParametros)
        {
            $emComercial    = $this->getDoctrine()->getManager("telconet");
            $objServiceUtil = $this->get('schema.Util');
            $arrayData      = $arrayParametros['data'];
            $strUser        = $arrayParametros['dataAuditoria']['usrCreacion'];
            $strIp          = $arrayParametros['dataAuditoria']['ipCreacion'];
            $boolIsLogin    = false;
            $boolIsEstado   = false;
            $boolIsFechaIni = false;
            $boolIsFechaFin = false;

            try
            {
                if (!isset($arrayData['login']) || empty($arrayData['login']))
                {
                    $boolIsLogin = true;
                }

                if (!isset($arrayData['estado']) || empty($arrayData['estado']))
                {
                    $boolIsEstado = true;
                }

                if (!isset($arrayData['fechaInicio']) || empty($arrayData['fechaInicio']))
                {
                    $boolIsFechaIni = true;
                }

                if (!isset($arrayData['fechaFin']) || empty($arrayData['fechaFin']))
                {
                    $boolIsFechaFin = true;
                }

                if ($boolIsLogin || $boolIsEstado || $boolIsFechaIni || $boolIsFechaFin)
                {
                    throw new \Exception('Error : Ninguno de lo siguiente valores puede ser nulo: '.
                                         '[login,estado,fechaInicio,fechaFin]');
                }

                $arrayFechaIni = explode('-', $arrayData['fechaInicio']);
                if(count($arrayFechaIni) !== 3 || !checkdate($arrayFechaIni[1], $arrayFechaIni[2], $arrayFechaIni[0])) //M-D-Y
                {
                    throw new \Exception('Error : Formato de fecha inválido en fechaInicio');
                }

                $arrayFechaFin = explode('-', $arrayData['fechaFin']);
                if(count($arrayFechaFin) !== 3 || !checkdate($arrayFechaFin[1], $arrayFechaFin[2], $arrayFechaFin[0])) //M-D-Y
                {
                    throw new \Exception('Error : Formato de fecha inválido en fechaFin');
                }

                $objFechaInicio = new \DateTime($arrayData['fechaInicio']);
                $objFechaFin    = new \DateTime($arrayData['fechaFin']);

                if ($objFechaInicio > $objFechaFin)
                {
                    throw new \Exception('Error : La fechaInicio no puede ser mayor a la fechaFin');
                }

                $objDiferenciaFechas = $objFechaFin->diff($objFechaInicio);

                if ($objDiferenciaFechas->y > 0 || $objDiferenciaFechas->m > 1 ||
                        ($objDiferenciaFechas->m === 1 && $objDiferenciaFechas->d > 0))
                {
                    throw new \Exception('Error : La consulta no puede ser mayor a un mes');
                }

                $arrayRespuesta = $emComercial->getRepository("schemaBundle:InfoPunto")
                        ->getTareasClientes(array ('strLogin'    => $arrayData['login'],
                                                   'strEstado'   => $arrayData['estado'],
                                                   'strFechaIni' => $arrayData['fechaInicio'],
                                                   'strFechaFin' => $arrayData['fechaFin']));

                if ($arrayRespuesta['status'] === 'fail')
                {
                    throw new \Exception($arrayRespuesta['message']);
                }
            }
            catch(\Exception $objException)
            {
                $strMessage = 'Error en el WebService getTareasClientes';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ',$objException->getMessage())[1];
                }

                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->getTareasClientes',
                                              $strCodigo.'|'.$objException->getMessage(),
                                              $strUser,
                                              $strIp);

                $objServiceUtil->insertError('Telcos+',
                                             'TelcoSysWSController->getTareasClientes',
                                              $strCodigo.'|'.json_encode($arrayData),
                                              $strUser,
                                              $strIp);

                $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
            }

            return $arrayRespuesta;
        }
    }
