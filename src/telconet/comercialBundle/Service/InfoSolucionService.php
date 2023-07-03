<?php

    namespace telconet\comercialBundle\Service;

    /**
     * Clase InfoSolucionService
     *
     * Clase que maneja las Transacciones de las soluciones de DC.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 19-06-2020
     */
    class InfoSolucionService
    {
        private $emComercial;
        private $emComunicacion;
        private $emInfraestructura;
        private $emSoporte;
        private $emFinanciero;
        private $emGeneral;
        private $serviceUtil;
        private $serviceRestClient;
        private $strUrlSolucionDc;
        private $strFactibilidadDc;
        private $strSolicitud;
        private $objContainer;

        /**
         * Función que agrega dependencias usadas dentro de la clase InfoSolucionService
         *
         * @param ContainerInterface $objContainer
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 19-06-2020
         */
        public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $objContainer)
        {
            $this->emComercial       = $objContainer->get('doctrine.orm.telconet_entity_manager');
            $this->emComunicacion    = $objContainer->get('doctrine.orm.telconet_comunicacion_entity_manager');
            $this->emInfraestructura = $objContainer->get('doctrine.orm.telconet_infraestructura_entity_manager');
            $this->emSoporte         = $objContainer->get('doctrine.orm.telconet_soporte_entity_manager');
            $this->emFinanciero      = $objContainer->get('doctrine.orm.telconet_financiero_entity_manager');
            $this->emGeneral         = $objContainer->get('doctrine.orm.telconet_general_entity_manager');
            $this->serviceUtil       = $objContainer->get('schema.Util');
            $this->serviceRestClient = $objContainer->get('schema.RestClient');
            $this->strUrlSolucionDc  = $objContainer->getParameter('ws_solucion_dc');
            $this->strFactibilidadDc = $objContainer->getParameter('ws_factibilidad_dc');
            $this->strSolicitud      = $objContainer->getParameter('ws_solicitud');
            $this->objContainer      = $objContainer;
        }

        /**
         * Función para ejecutar los web-services de los micro-servicios de DC.
         *
         * @param Array $arrayParametros [
         *                                  strUser      : Usuario quien realiza la ejecución del método.
         *                                  strIp        : Ip del usuario quien realiza la ejecución del método.
         *                                  strOpcion    : Opción del web-service a ejecutar.
         *                                  strEndPoint  : End-Point del web-service a ejecutar.
         *                                  arrayRequest : Request a ejecutar.
         *                               ]
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 19-06-2020
         *
         */
        public function WsPostDc($arrayParametros)
        {
            set_time_limit(180);
            $strUser        = $arrayParametros['strUser'] ? $arrayParametros['strUser'] : "TelcosDc+";
            $strIp          = $arrayParametros['strIp']   ? $arrayParametros['strIp']   : '127.0.0.1';
            $strOpcion      = $arrayParametros['strOpcion'];
            $strEndPoint    = $arrayParametros['strEndPoint'];
            $arrayRequest   = $arrayParametros['arrayRequest'];
            $strJsonRequest = json_encode($arrayRequest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

            try
            {
                if (empty($strOpcion))
                {
                    throw new \Exception('Error : La opción para establecer la comunicación con el web-service de DC no puede estar vacia.');
                }

                if (empty($strEndPoint))
                {
                    throw new \Exception('Error : El endPoint para establecer la comunicación con el web-services de DC no puede estar vacio.');
                }

                if (empty($arrayRequest))
                {
                    throw new \Exception('Error : El request para establecer la comunicación con el web-Services de DC no puede estar vacio.');
                }

                //Obtenemos la url de acuerdo al ws a utilizar.
                if ($strOpcion === 'soluciondc')
                {
                    $strUrl = $this->strUrlSolucionDc;
                }
                elseif ($strOpcion === 'factibilidaddc')
                {
                    $strUrl = $this->strFactibilidadDc;
                }
                elseif ($strOpcion === 'solicitud')
                {
                    $strUrl = $this->strSolicitud;
                }
                else
                {
                    throw new \Exception("Error : Opción inválida ($strOpcion)");
                }

                $strUrl = str_replace("{endPoint}",$strEndPoint,$strUrl);

                //Ejecución del WS.
                $arrayOptions  = array(CURLOPT_SSL_VERIFYPEER => false);
                $arrayResponse = $this->serviceRestClient->postJSON($strUrl,$strJsonRequest,$arrayOptions);

                //Preguntamos si la comunicación no fue válida.
                if ($arrayResponse['status'] != 200)
                {
                    $strMessage = "Error : Error al establecer la comunicación con los web-services de DC.";

                    if (isset($arrayResponse['result']) && !empty($arrayResponse['result']))
                    {
                        $arrayResult = is_array($arrayResponse['result']) ? $arrayResponse['result'] :
                                       json_decode($arrayResponse['result'],true);
                        $strMessage  = $arrayResult['message'] ? $arrayResult['message'] : 'Error : Sin mensaje de Error.';
                    }
                    else
                    {
                        $strMessage = $arrayResponse['error'] ? $strMessage.' - ('.$arrayResponse['error'].')' : $strMessage;
                    }

                    throw new \Exception($strMessage);
                }

                //Obtenemos el resultado del ejecución.
                $arrayResult = json_decode($arrayResponse['result'],true);

                if (!isset($arrayResult['data']))
                {
                    $strMessage = $arrayResult['message'] ? $arrayResult['message'] : "Error : El web-service no retorno datos.";
                    throw new \Exception($strMessage);
                }

                //Retornamos la información del WS.
                $arrayRespuesta = array('status'  => true,
                                        'message' => $arrayResult['message'],
                                        'data'    => $arrayResult['data']);
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error general al ejecutar los Web-Services de DC. Por favor notificar a Sistemas.';
                $strCodigo  = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);
                $strJson    = json_encode($arrayParametros);

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ',$objException->getMessage())[1].' Por favor notificar a Sistemas.';
                }

                if (strpos($objException->getMessage(),' - ERROR_BACKTRACE') !== false)
                {
                    $strMessage = explode(' - ERROR_BACKTRACE',$objException->getMessage())[0].' Por favor notificar a Sistemas.';
                }

                $this->serviceUtil->insertError('InfoSolucionService',
                                                'WsPostDc',
                                                 $strCodigo.'|'.$objException->getMessage(),
                                                 $strUser,
                                                 $strIp);

                //Insertamos minimo 3 veces el error para obtener el json completo.
                $this->serviceUtil->insertError('InfoSolucionService',
                                                'WsPostDc',
                                                 $strCodigo.'|'.substr($strJson, 0, 3000),
                                                 $strUser,
                                                 $strIp);

                $strJson = substr($strJson, 3001, 6000);

                if (is_string($strJson) && !empty($strJson))
                {
                    $this->serviceUtil->insertError('InfoSolucionService',
                                                    'WsPostDc',
                                                     $strCodigo.'|'.$strJson,
                                                     $strUser,
                                                     $strIp);
                }

                $strJson = substr($strJson, 6001, 9000);

                if (is_string($strJson) && !empty($strJson))
                {
                    $this->serviceUtil->insertError('InfoSolucionService',
                                                    'WsPostDc',
                                                     $strCodigo.'|'.$strJson,
                                                     $strUser,
                                                     $strIp);
                }

                $arrayRespuesta = array('status' => false,'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Función encargado de crear la solucion DC.
         *
         * @param Array $arrayParametros [
         *                                  strUser       : Usuario quien realiza la ejecución del método.
         *                                  strIp         : Ip del usuario quien realiza la ejecución del método.
         *                                  arraySolucion : Array de la solución a crear.
         *                               ]
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 19-06-2020
         *
         */
        public function crearSolucionDc($arrayParametros)
        {
            $strUser         = $arrayParametros['strUser'] ? $arrayParametros['strUser'] : "TelcosDc+";
            $strIp           = $arrayParametros['strIp']   ? $arrayParametros['strIp']   : '127.0.0.1';
            $intIdEmpresa    = $arrayParametros['intIdEmpresa'];
            $arraySolucion   = $arrayParametros['arraySolucion'];
            $arrayRecursos   = $arrayParametros['arrayRecursos'];
            $arrayServicioMV = $arrayParametros['arrayServicioMV'];
            $arraySubTipos   = $arrayParametros['arraySubTipos'];

            try
            {
                //Array para crear la solución.
                $arrayCrearSolucion['habilitaCommit'] =  true;
                $arrayCrearSolucion['estado']         = "Activo";
                $arrayCrearSolucion['usrCreacion']    =  $strUser;
                $arrayCrearSolucion['ipCreacion']     =  $strIp;
                $arrayCrearSolucion['solucion']       =  $arraySolucion;
                $arrayCrearSolucion['dataRecurso']    =  array ('estado'   => 'Activo',
                                                                'recursos' => $arrayRecursos);

                $arrayEsPreferencial = array_map(function($arrayResultDc)
                {
                    return $arrayResultDc['esPreferencial'];
                },$arrayCrearSolucion['solucion']['detalle']);

                $arrayEsCore = array_map(function($arrayResultDc)
                {
                    return $arrayResultDc['esCore'];
                },$arrayCrearSolucion['solucion']['detalle']);

                $arraykeysEsPreferencial = array_keys($arrayEsPreferencial, 'SI');
                $arraykeysEsCore         = array_keys($arrayEsCore, 'SI');

                //Creamos el array de referencia.
                $arrayReferencia = array();
                foreach ($arraykeysEsPreferencial as $intPosicioni)
                {
                    $arrayServiciosRef   = array();
                    $arrayDetSol1        = $arrayCrearSolucion['solucion']['detalle'][$intPosicioni];
                    $arraySolReferencial = array_filter(explode("|", $arrayDetSol1['solucionReferencial']));

                    foreach ($arraySolReferencial as $strValor)
                    {
                        foreach ($arraykeysEsCore as $intPosicionj)
                        {
                            $arrayDetSol2 = $arrayCrearSolucion['solucion']['detalle'][$intPosicionj];

                            if ($strValor == $arrayDetSol2['tipoSolucion'])
                            {
                                $arrayServiciosRef[] = $arrayDetSol2['servicioId'];
                            }
                        }
                    }

                    if (!empty($arrayServiciosRef))
                    {
                        $arrayReferencia[] = array('servicio'  => $arrayDetSol1['servicioId'],
                                                   'servicios' => $arrayServiciosRef);
                    }
                }

                if (!empty($arraySubTipos) && count($arraySubTipos))
                {
                    $arrayReferencia = array_merge($arrayReferencia,$arraySubTipos);
                }

                $arrayCrearSolucion['solucion']['referencia'] = $arrayReferencia;

                //Obtenemos el array de maquinas virtuales con sus respectivos recursos y servicios asociados.
                if (!empty($arrayServicioMV))
                {
                    $arrayServicioMV['arrayServicioMV'] = $arrayServicioMV;
                    $arrayServicioMV['arrayRecursos']   = $arrayRecursos;
                    $arrayServicioMV['intIdEmpresa']    = $intIdEmpresa;
                    $arrayResponse                      = $this->obtenerMaquinasVirtuales($arrayServicioMV);

                    if ($arrayResponse['message'] !== null)
                    {
                        throw new \Exception('Error : '.$arrayResponse['message']);
                    }
                    $arrayCrearSolucion['maquinasVirtuales'] = $arrayResponse['arrayMaquinasVirtuales'];
                }

                $arrayRespuesta = $this->WsPostDc(array('strUser'      => $strUser,
                                                        'strIp'        => $strIp,
                                                        'strOpcion'    => 'soluciondc',
                                                        'strEndPoint'  => 'crearSolucion',
                                                        'arrayRequest' => $arrayCrearSolucion));
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error general al crear la solución DC. Por favor notificar a Sistemas.';

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ',$objException->getMessage())[1];
                }

                $this->serviceUtil->insertError('InfoSolucionService',
                                                'crearSolucionDc',
                                                 $objException->getMessage(),
                                                 $strUser,
                                                 $strIp);

                $arrayRespuesta = array('status' => false,'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de obtener las maquinas virtuales de
         * una solución con sus respectivos recursos.
         *
         * @param Array $arrayParametros [
         *                                  arrayServicioMV : Array de maquinas virtuales.
         *                                  arrayRecursos   : Array de recursos de la solución.
         *                                  intIdEmpresa    : Id de la empresa.
         *                               ]
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 24-06-2020
         *
         */
        public function obtenerMaquinasVirtuales($arrayParametros)
        {
            $arrayServicioMV        = $arrayParametros['arrayServicioMV'];
            $arrayRecursos          = $arrayParametros['arrayRecursos'];
            $intIdEmpresa           = $arrayParametros['intIdEmpresa'];
            $arrayTiposSO           = array('BASE DE DATOS','APLICACIONES','SISTEMA OPERATIVO','OTROS');
            $arrayMaquinasVirtuales = array();
            $strMessageError        = null;

            //Obtenemos el modelo de las maquinas virtules.
            $objModeloMaquinaVirtual = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')
                    ->findOneBy(array('nombreModeloElemento' => 'MODELO MAQUINA VIRTUAL DC',
                                      'estado'               => 'Activo'));

            if(!is_object($objModeloMaquinaVirtual))
            {
                $strMessageError = 'No se encontró el modelo genérico de Máquina Virtual. Por favor notificar a Sistemas.';
                return array('message' => $strMessageError);
            }

            //For principal de los servicios que tienen maquinas virtuales
            foreach($arrayServicioMV as $arrayValue)
            {
                $intIdServicio = $arrayValue['intIdServicio'];
                $intIdVCenter  = $arrayValue['intIdVcenter'];
                $arrayVM       = json_decode($arrayValue['strJson']);

                //Obtener ubicacion donde estara alojada la maquina virtual.
                $objElementoUbica = $this->emInfraestructura->getRepository("schemaBundle:InfoEmpresaElementoUbica")
                        ->findOneBy(array('elementoId' => $intIdVCenter,
                                          'empresaCod' => $intIdEmpresa));

                //For para de las maquinas virtuales del servicio.
                foreach($arrayVM as $objVM)
                {
                    $arrayMaquinasVirtual               =  array();
                    $arrayMaquinasVirtual['estado']     = 'Activo';
                    $arrayMaquinasVirtual['servicioId'] =  $intIdServicio;
                    $arrayMaquinasVirtual['elemento']   =  array('nombreElemento'      =>  $objVM->nombre,
                                                                 'descripcionElemento' => "Maquina Virtual DC",
                                                                 'modeloElementoId'    =>  $objModeloMaquinaVirtual->getId());

                    $arrayMaquinasVirtual['empresaElementoUbica'] = array('empresaCod'  => $intIdEmpresa,
                                                                          'ubicacionId' => is_object($objElementoUbica) ? 
                                                                                           is_object($objElementoUbica)->getUbicacionId() :
                                                                                           null);

                    $arrayMaquinasVirtual['detalle'][] = array('detalleNombre' => 'CARPETA',
                                                               'detalleValor'  =>  $objVM->carpeta);
                    $arrayMaquinasVirtual['detalle'][] = array('detalleNombre' => 'TARJETA_RED',
                                                               'detalleValor'  =>  $objVM->tarjeta);

                    //Array de los recursos de la maquina virtual.
                    $arrayRecursosVM = json_decode($objVM->arrayRecursos);

                    //For de los recursos de la maquina virtual.
                    foreach ($arrayRecursosVM as $objJsonRecursosVM)
                    {
                        $boolAgregar   = true;
                        $intServicioId = null;

                        //En caso que el tipo de recurso sea de licenciamiento, obtenemos el servicio asociado.
                        if (in_array($objJsonRecursosVM->tipo,$arrayTiposSO))
                        {
                            $arrayRecursosLic = array_map(function($arrayResultDc)
                            {
                                return $arrayResultDc['secuencial'];
                            },$arrayRecursos);

                            $arrayRecursosLic = array_keys($arrayRecursosLic, $objJsonRecursosVM->secuencial);

                            foreach ($arrayRecursosLic as $intPosicion)
                            {
                                $arrayServicioLic = $arrayRecursos[$intPosicion];
                                if ($arrayServicioLic['tipoRecurso']        == $objJsonRecursosVM->tipo &&
                                    $arrayServicioLic['descripcionRecurso'] == $objJsonRecursosVM->caracteristica)
                                {
                                    $intServicioId = $arrayServicioLic['servicioId'];
                                    break;
                                }
                            }

                            //Proceso para eliminar la duplicidad del licenciamiento.
                            $arrayRecursosLic = array_map(function($arrayResultDc)
                            {
                                return $arrayResultDc['servicioId'];
                            },$arrayMaquinasVirtual['detalleRecursos']);

                            $arrayRecursosLic = array_keys($arrayRecursosLic, $intServicioId);

                            foreach ($arrayRecursosLic as $intPosicion)
                            {
                                $arrayServicioLic = $arrayMaquinasVirtual['detalleRecursos'][$intPosicion];
                                if ($arrayServicioLic['tipoRecurso']        == $objJsonRecursosVM->tipo &&
                                    $arrayServicioLic['descripcionRecurso'] == $objJsonRecursosVM->caracteristica)
                                {
                                    $boolAgregar = false;
                                    $intCantidad = $arrayMaquinasVirtual['detalleRecursos'][$intPosicion]['cantidad'] +
                                                   $objJsonRecursosVM->asignar;
                                    $arrayMaquinasVirtual['detalleRecursos'][$intPosicion]['cantidad'] = $intCantidad;
                                }
                            }
                        }

                        if ($boolAgregar)
                        {
                            $arrayMaquinasVirtual['detalleRecursos'][] = array(
                                'tipoRecurso'        => $objJsonRecursosVM->tipo,
                                'descripcionRecurso' => $objJsonRecursosVM->caracteristica,
                                'cantidad'           => $objJsonRecursosVM->asignar,
                                'servicioId'         => $intServicioId);
                        }
                    }

                    $arrayMaquinasVirtuales[] = $arrayMaquinasVirtual;
                }

            }

            return array('arrayMaquinasVirtuales' => $arrayMaquinasVirtuales);
        }

        /**
         * Método encargado de obtener las maquinas virtuales de
         * una solución con sus respectivos recursos.
         *
         * @param Array $arrayParametros [
         *                                  intIdServicio : Id del servicio.
         *                               ]
         *
         * @author Karen Rodríguez V <kyrodriguez@telconet.ec>
         * @version 1.0 17-07-2020
         */
        public function getArrayCaracteristicasLicencias($arrayParametros)
        {
            $intIdServicio          = $arrayParametros['intIdServicio'];
            $arrayResultado         = array();
            $intContador            = 0;

            $objServicio = $this->emComercial->getRepository("schemaBundle:InfoServicio")->find($intIdServicio);

            if (is_object($objServicio))
            {
                //Obtenemos las licencias contratadas.
                $arrayLicenciasContratadas = $this->emComercial->getRepository("schemaBundle:InfoServicioRecursoCab")
                                                  ->findBy(array('servicioId' =>  $objServicio->getId(),
                                                                 'estado'     => 'Activo'));

                //Recorremos las licencias contratadas para verificar si están asociadas a una o mas máquinas.
                foreach ($arrayLicenciasContratadas as $objLicenciaCab)
                {
                    $intLicenciasContratas   = $objLicenciaCab->getCantidad();
                    $intContadorLicOcupadas  = 0;

                    $arrayLicenciasAsignadas = $this->emComercial->getRepository("schemaBundle:InfoServicioRecursoDet")
                            ->findBy(array('servicioRecursoCabId' =>  $objLicenciaCab->getId(),
                                           'estado'               => 'Activo'));

                    if (!empty($arrayLicenciasAsignadas))
                    {
                        foreach ($arrayLicenciasAsignadas as $objLicenciaDet)
                        {
                            $arrayResultado[$intContador]['idMaquina']   = $objLicenciaDet->getElementoId();
                            $arrayResultado[$intContador]['id']          = $objLicenciaCab->getId();
                            $arrayResultado[$intContador]['idDetalle']   = $objLicenciaDet->getId();
                            $arrayResultado[$intContador]['idServicio']  = $objServicio->getId();
                            $arrayResultado[$intContador]['descripcion'] = $objLicenciaCab->getTipoRecurso();
                            $arrayResultado[$intContador]['valor']       = $objLicenciaCab->getDescripcionRecurso();
                            $arrayResultado[$intContador]['valorCaract'] = $objLicenciaDet->getCantidad();
                            $arrayResultado[$intContador]['tipoIngreso'] = 'S';
                            $intContadorLicOcupadas = $intContadorLicOcupadas + $objLicenciaDet->getCantidad();
                            $intContador++;
                        }

                        if ($intContadorLicOcupadas < $intLicenciasContratas)
                        {
                            $arrayResultado[$intContador]['idMaquina']   = null;
                            $arrayResultado[$intContador]['id']          = $objLicenciaCab->getId();
                            $arrayResultado[$intContador]['idServicio']  = $objServicio->getId();
                            $arrayResultado[$intContador]['descripcion'] = $objLicenciaCab->getTipoRecurso();
                            $arrayResultado[$intContador]['valor']       = $objLicenciaCab->getDescripcionRecurso();
                            $arrayResultado[$intContador]['valorCaract'] = $intLicenciasContratas - $intContadorLicOcupadas;
                            $arrayResultado[$intContador]['tipoIngreso'] = 'S';
                            $intContador++;
                        }
                    }
                    else
                    {
                        $arrayResultado[$intContador]['idMaquina']   = null;
                        $arrayResultado[$intContador]['id']          = $objLicenciaCab->getId();
                        $arrayResultado[$intContador]['idServicio']  = $objServicio->getId();
                        $arrayResultado[$intContador]['descripcion'] = $objLicenciaCab->getTipoRecurso();
                        $arrayResultado[$intContador]['valor']       = $objLicenciaCab->getDescripcionRecurso();
                        $arrayResultado[$intContador]['valorCaract'] = $intLicenciasContratas;
                        $arrayResultado[$intContador]['tipoIngreso'] = 'S';
                        $intContador++;
                    }
                }
            }
            return $arrayResultado;
        }
    }