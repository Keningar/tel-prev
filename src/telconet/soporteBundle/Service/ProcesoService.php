<?php

    namespace telconet\soporteBundle\Service;

    use telconet\schemaBundle\Entity\InfoDocumento;
    use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
    use telconet\schemaBundle\Entity\AdmiTipoDocumento;
    use telconet\seguridadBundle\Service\TokenValidatorService;

    /**
     * Clase ProcesoService
     *
     * Clase que maneja las Transacciones realizadas en el módulo de Soporte.
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.0 24-01-2019
     */
    class ProcesoService
    {
        private $emComercial;
        private $emSoporte;
        private $emInfraestructura;
        private $emComunicacion;
        private $emFinanciero;
        private $emGeneral;
        private $serviceEnvioPlantilla;
        private $serviceTokenValidator;
        private $serviceSoporte;
        private $serviceUtil;
        private $serviceRestClient;
        private $strUrlSysCloudCenter;
        private $strExecuteSysCloudCenter;
        private $strHost;
        private $strPath;
        private $strPathUpload;

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
            $this->serviceRestClient        = $objContainer->get('schema.RestClient');
            $this->serviceTokenValidator    = $objContainer->get('seguridad.TokenValidator');
            $this->serviceSoporte           = $objContainer->get('soporte.SoporteService');
            $this->strHost                  = $objContainer->getParameter('host_scripts');
            $this->strPath                  = $objContainer->getParameter('path_telcos');
            $this->strPathUpload            = $objContainer->getParameter('ruta_uploadDataGis');
            $this->strUrlSysCloudCenter     = $objContainer->getParameter('ws_sysCloudCenter_url');
            $this->strExecuteSysCloudCenter = $objContainer->getParameter('sysCloudCenter_execute');
        }

        /**
         * Método encargado de almacenar los archivos que vienen vía WS en base64
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 - 20-11-2018
         *
         * @param Array $arrayParametros [
         *                                  strFileBase64     = Archivo en base 64,
         *                                  strFileName       = Nombre del archivo,
         *                                  strFileExtension  = Extensión del archivo,
         *                                  intNumeroTarea    = Número de la tarea,
         *                                  strNumeroCaso     = Número del caso,
         *                                  strOrigen         = Origen (Caso o Tarea),
         *                                  strPrefijoEmpresa = Prefijo de la empresa,
         *                                  strUsuario        = Usuario,
         *                                  strIp             = Ip
         *                               ]
         *
         * @return Array $arrayResultado
         * 
         * @author Modificado: David De La Cruz  <ddelacruz@telconet.ec>
         * @version 1.1 30-07-2021  Se actualiza para que los archivos sean almacenados en el Gluster NFS
         * 
         */
        public function putFile($arrayParametros)
        {
            $strServerRoot      = $this->strPath."telcos/web";
            $strFileBase64      = $arrayParametros['strFileBase64'];
            $strFileName        = $arrayParametros['strFileName'];
            $strFileExtension   = $arrayParametros['strFileExtension'];
            $intNumeroTarea     = $arrayParametros['intNumeroTarea'];
            $intNumeroCaso      = $arrayParametros['strNumeroCaso'];
            $strOrigen          = strtoupper($arrayParametros['strOrigen']);
            $strPrefijoEmpresa  = $arrayParametros['strPrefijoEmpresa'];
            $boolIsInFileServer = $arrayParametros['boolIsInFileServer'];
            $strUsuario         = $arrayParametros['strUsuario'];
            $strIp              = $arrayParametros['strIp'];
            $strFicheroSubido   = '';
            $strSubModulo       = '';
            $boolExitoNfs       = false;

            $this->emComunicacion->getConnection()->beginTransaction();

            try
            {
                if ($strOrigen !== 'C' && $strOrigen !== 'T')
                {
                    return array ('status'  => 'fail', 'message' => 'Origen no considerado');
                }

                $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                        ->findOneByPrefijo($strPrefijoEmpresa);

                if (!is_object($objEmpresa))
                {
                   return array ('status'  => 'fail', 'message' => 'El prefijo empresa no existe en telcos');
                }


                /* Se validan extensiones restringidas */ 
                $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                        ->findOneBy(array(
                                                                        'nombreParametro' => 'MODULOS_APP',
                                                                        'estado'          => 'Activo'
                                                        ));

                if (!is_object($objAdmiParametroCab))
                {
                    throw new \Exception('No existe la configuración de los módulos y extensiones en la base de datos, '.
                                         'por favor reportar a sistemas.');
                }

                $objExtensionesRestringidas = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'descripcion' => 'EXTENSIONES_RESTRINGIDAS',
                                                                'estado'      => 'Activo'
                                                ));
            
                if (!is_object($objExtensionesRestringidas))
                {
                    throw new \Exception('No existe la configuración de extensiones restringidas, '.
                                         'por favor reportar a sistemas.');
                }


                if (!(strpos($objExtensionesRestringidas->getValor1(), strtolower($strFileExtension)) === false)) 
                {
                    throw new \Exception('Archivo con extensión <' . $strFileExtension . '> no permitida');
                }
                
                if ($strOrigen === 'C')
                {
                    $strFuncion   = "casos/";
                    $strSubmodulo = "CASOS";
                    $strSubModulo = 'Casos';
                    $objInfoCaso  = $this->emSoporte->getRepository('schemaBundle:InfoCaso')
                            ->findOneByNumeroCaso($intNumeroCaso);

                    if (!is_object($objInfoCaso))
                    {
                        return array ('status'  => 'fail', 'message' => 'El caso no existe en telcos');
                    }
                }
                else
                {
                    $strFuncion          = "tareas/";
                    $strSubmodulo        = "TAREAS";
                    $strSubModulo        = 'Tareas';
                    $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                            ->find($intNumeroTarea);

                    if (!is_object($objInfoComunicacion))
                    {
                        return array ('status'  => 'fail', 'message' => 'Número de tarea no existe en telcos');
                    }

                    $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                            ->find($objInfoComunicacion->getDetalleId());

                    if (!is_object($objInfoDetalle))
                    {
                        return array ('status'  => 'fail', 'message' => 'No existe el detalle en telcos');
                    }
                }
                
                if (!$boolIsInFileServer)
                {
                    $strModulo        = "soporte/";
                    $strTipoDoc       = strtoupper($strFileExtension);
                    $intCodigo        = substr(md5(uniqid(rand())),0,6);
                    $strNuevoNombre   = $strFileName."_". $intCodigo.".".strtolower($strFileExtension);
                    $strTofind        = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ?·";
                    $strReplac        = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn--";
                    $strNuevoNombre   = strtr($strNuevoNombre,$strTofind,$strReplac);
                    $strDestino       = $strServerRoot."/public/uploads/".$strPrefijoEmpresa."/".$strModulo.$strFuncion;
                    $strRutaFisica    = "public/uploads/".$strPrefijoEmpresa."/".$strModulo.$strFuncion;
                    $strFicheroSubido = $strDestino.$strNuevoNombre;
                }
                else
                {

                    $objAppModulo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                    ->findOneBy(array(
                                                                    'parametroId' => $objAdmiParametroCab,
                                                                    'descripcion'      => 'TELCOS_SOPORTE',
                                                                    'estado'      => 'Activo'
                                                    ));
                
                    if (!is_object($objAppModulo))
                    {
                        throw new \Exception('Configuración TELCOS-SOPORTE no existe');
                    }
                    

                    $strNuevoNombre   = $strFileName . "." . strtolower($strFileExtension);

                    $arrayConfigRuta['strApp']            = $objAppModulo->getValor2();
                    $arrayConfigRuta['strModulo']         = $objAppModulo->getValor3();
                    $arrayConfigRuta['strSubModulo']      = $strSubmodulo;
                    $arrayConfigRuta['strPrefijoEmpresa'] = $strPrefijoEmpresa;

                    $strPathFileServe = $this->getFilePath($arrayConfigRuta);


                    if ($strPathFileServe['status'] == 'fail')
                    {
                        throw new \Exception($strPathFileServe['message']);
                    }

                    $strDestino       = $strServerRoot . "/public/uploads/" . $strPathFileServe['path'];
                    $strRutaFisica    = "public/uploads/" . $strPathFileServe['path'];
                    $strFicheroSubido = $strDestino.$strNuevoNombre;

                    if (file_exists($strFicheroSubido))
                    {
                        return array (
                            'status'  => 'fail', 
                            'message' => 'El archivo <' . $strNuevoNombre . '> ya existe, por favor cambiar de nombre'
                        );
                    }


                }                

                // Se reemplazan caracteres que no cumplen con el patron definido para el nombre del archivo
                $strPatronABuscar = '/[^a-zA-Z0-9._-]/';
                $strCaracterReemplazo = '_';
                $strNuevoNombre = preg_replace($strPatronABuscar,$strCaracterReemplazo,$strNuevoNombre);

                //####################################
                //INICIO DE SUBIR ARCHIVO AL NFS >>>>>
                //####################################
                $arrayParamNfs = array(
                    'prefijoEmpresa'       => $strPrefijoEmpresa,
                    'strApp'               => 'TelcosWeb' ,
                    'arrayPathAdicional'   => [],
                    'strBase64'            => $strFileBase64,
                    'strNombreArchivo'     => $strNuevoNombre,
                    'strUsrCreacion'       => $strUsuario,
                    'strSubModulo'         => $strSubModulo);

                $arrayRespNfs = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                //##################################
                //<<<<< FIN DE SUBIR ARCHIVO AL NFS
                //##################################

                if(isset($arrayRespNfs) && $arrayRespNfs['intStatus'] == 200)
                {
                    $strFicheroSubido = $arrayRespNfs['strUrlArchivo'];
                    $strRutaFisica    = $arrayRespNfs['strUrlArchivo'];
                    $boolExitoNfs = true;
                }
                else
                {
                    return array ('status'  => 'fail', 'message' => 'Error al escribir archivo');
                }

                $objInfoDocumento = new InfoDocumento();

                if ($strOrigen == "C")
                {
                    $objInfoDocumento->setNombreDocumento('Adjunto Caso');
                    $objInfoDocumento->setMensaje('Documento que se adjunta en la creación de un Caso');
                }
                else
                {
                    $objInfoDocumento->setNombreDocumento('Adjunto Tarea');
                    $objInfoDocumento->setMensaje('Documento que se adjunta a una tarea');
                }

                $objInfoDocumento->setUbicacionFisicaDocumento($strFicheroSubido);
                $objInfoDocumento->setUbicacionLogicaDocumento($strNuevoNombre);
                $objInfoDocumento->setEstado('Activo');
                $objInfoDocumento->setFeCreacion(new \DateTime('now'));
                $objInfoDocumento->setFechaDocumento(new \DateTime('now'));
                $objInfoDocumento->setIpCreacion($strIp);
                $objInfoDocumento->setUsrCreacion($strUsuario);
                $objInfoDocumento->setEmpresaCod($objEmpresa->getId());

                if ($strTipoDoc === 'JPEG')
                {
                   $strTipoDoc = "JPG" ;
                }

                $objAdmiTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                        ->findOneByExtensionTipoDocumento($strTipoDoc);

                if (!is_object($objAdmiTipoDocumento))
                {
                    $objAdmiTipoDocumento = new AdmiTipoDocumento();
                    $objAdmiTipoDocumento->setExtensionTipoDocumento($strTipoDoc);
                    $objAdmiTipoDocumento->setTipoMime($strTipoDoc);
                    $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.$strTipoDoc);
                    $objAdmiTipoDocumento->setEstado('Activo');
                    $objAdmiTipoDocumento->setFeCreacion(new \DateTime('now'));
                    $objAdmiTipoDocumento->setUsrCreacion($strUsuario);
                    $this->emComunicacion->persist($objAdmiTipoDocumento);
                    $this->emComunicacion->flush();
                }

                $objInfoDocumento->setTipoDocumentoId($objAdmiTipoDocumento);

                if (strtoupper($strFileExtension) === "JPG"  ||
                    strtoupper($strFileExtension) === 'JPEG' ||
                    strtoupper($strFileExtension) === 'PNG')
                {
                    $arrayCoordenadas = $this->serviceSoporte->obtenerCoordenadasImg(array ("strRutaFisicaArchivo" => $strRutaFisica,
                                                                                            "strNombreArchivo"     => $strNuevoNombre,
                                                                                            "boolExitoNfs"         => $boolExitoNfs));

                    if (isset($arrayCoordenadas["floatLatitud"]) && !empty($arrayCoordenadas["floatLatitud"]))
                    {
                        $objInfoDocumento->setLatitud($arrayCoordenadas["floatLatitud"]);
                    }

                    if (isset($arrayCoordenadas["floatLongitud"]) && !empty($arrayCoordenadas["floatLongitud"]))
                    {
                        $objInfoDocumento->setLongitud($arrayCoordenadas["floatLongitud"]);
                    }
                }

                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();

                //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                $objInfoDocumentoRelacion = new InfoDocumentoRelacion();
                $objInfoDocumentoRelacion->setModulo('SOPORTE');
                $objInfoDocumentoRelacion->setEstado('Activo');
                $objInfoDocumentoRelacion->setFeCreacion(new \DateTime('now'));

                $objInfoDocumentoRelacion->setUsrCreacion($strUsuario);

                if ($strOrigen === "C")
                {
                    $objInfoDocumentoRelacion->setCasoId($objInfoCaso->getId());
                }
                else
                {
                    $objInfoDocumentoRelacion->setDetalleId($objInfoDetalle->getId());
                }

                $objInfoDocumentoRelacion->setDocumentoId($objInfoDocumento->getId());

                $this->emComunicacion->persist($objInfoDocumentoRelacion);
                $this->emComunicacion->flush();

                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->commit();
                }

                $this->emComunicacion->getConnection()->close();

                $arrayResponse = array ('status'  => 'ok', 'message' => 'Archivo subido correctamente');

                if ($boolIsInFileServer)
                {
                    $arrayResponse['path'] = $strPathFileServe['path'];
                }

                return $arrayResponse;
           }
           catch (\Exception $objException)
           {
              
                if (!is_null($strFicheroSubido))
                {
                    unlink($strFicheroSubido);
                }

                if ($this->emComunicacion->getConnection()->isTransactionActive())
                {
                    $this->emComunicacion->getConnection()->rollback();
                }

                $this->emComunicacion->getConnection()->close();

                return array ('status'  => 'fail',
                              'message' => $objException->getMessage());
           }
        }

        /**
         * Método encargado de realizar la gestión de acuerdo al cambio de estado generado desde el aplicativo TelcoSys.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 22-10-2018
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 29-03-2019 - Al momento de insertar el error, se cambia el mensaje por la Excepción.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.2 01-01-2019 - En la validación del error se añade el insert para almacenar el json que se recibe vía WS.
         *
         * @author Néstor Naula <nnaulal@telconet.ec>
         * @version 1.3 06-02-2020 - Se realiza validación del departamento en base al id empresa rol del usuario.
         * @since 1.2
         * 
         * @author Néstor Naula <nnaulal@telconet.ec>
         * @version 1.4 06-07-2020 - Se realiza cambio para que encuentre departamento Activo y Modificado.
         * @since 1.3
         * 
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.5 22-10-2020 - Se realiza cambio para que encuentre el departamento en estado 'Activo' y 'Modificado'.
         * 
         * @author José Bedón <jobedon@telconet.ec>
         * @version 1.6 17-05-2021 - Se agrega campo de idFinTarea para la reasignación, se corrige departamento origen
         *                           para reasignacion y finalizacion de tarea
         *
         * @author Pedro Velez <psvelez@telconet.ec>
         * @version 1.7 22-11-2021 - Se agrega filtro de compañia en la obtencion de objeto departamento
         * 
         * @author Pedro Velez <psvelez@telconet.ec>
         * @version 1.8 22-11-2021 - Se ordena de forma ascendente por compañia el resultado de la busqueda 
         *                           de departamento por nombre 
         * 
         * @author Pedro Velez <psvelez@telconet.ec>
         * @version 1.9 07-02-2022 - Se obtiene prefijo de compañia para una mejor validacion de departamentos 
         *                           por compañia 
         * 
         * @param  Array $arrayParametros
         * @return Array $arrayRespuesta
         */
        public function gestionarTareasTelcoSys($arrayParametros)
        {
            $arrayRespuesta = array();
            $arrayData      = $arrayParametros['data'];
            $arrayDataAudit = $arrayParametros['dataAuditoria'];
            $intIdTarea     = $arrayData['idTarea'];
            $strUsrCreacion = $arrayDataAudit['usrCreacion'];
            $strIpCreacion  = $arrayDataAudit['ipCreacion'];
            $strOrigen      = $arrayData['estado'];
            $strCodigo      = '';
            $objAdmiTarea   = null;
            $arrayEmpresa   = array('MD','TN');
            
            try
            {
                $objInfoComunicacion = $this->emComunicacion->getRepository("schemaBundle:InfoComunicacion")->find($intIdTarea);
                if (!is_object($objInfoComunicacion))
                {
                    throw new \Exception("ERROR : El número de tarea $intIdTarea no existe en telcos");
                }
                $objInfoDetalle = $this->emSoporte->getRepository("schemaBundle:InfoDetalle")->find($objInfoComunicacion->getDetalleId());
                if (!is_object($objInfoDetalle))
                {
                    throw new \Exception('ERROR : No se encontró referencia de la Tarea en Telcos');
                }
                if (isset($arrayData['prefijoEmpresa']))
                {
                    if (in_array($arrayData['prefijoEmpresa'],$arrayEmpresa))
                    {                    
                        $strPrefijoEmpresa = $arrayData['prefijoEmpresa'];
                    }
                    else
                    {
                        throw new \Exception('ERROR : El prefijo de empresa '.$arrayData['prefijoEmpresa'].
                        ' no existe para la gestion de tareas desde Syscloud');
                    }

                }
                else
                {
                    $strPrefijoEmpresa = "TN";
                }
                $objInfoEmpresaGrupo = $this->emComercial->getRepository("schemaBundle:InfoEmpresaGrupo")
                    ->findOneBy(array ('prefijo' => $strPrefijoEmpresa,
                                       'estado'  => 'Activo'));
                if (!is_object($objInfoEmpresaGrupo))
                {
                    throw new \Exception('ERROR : La empresa no existe en telcos, por favor comunicarse con Sistemas');
                }

                if (isset($arrayData['prefijoEmpresa']))
                {
                    $objAdmiDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                        ->findOneBy(array ('nombreDepartamento' => $arrayData['nombreDepartamento'],
                                           'estado'             => array('Activo','Modificado'),
                                           'empresaCod'         => $objInfoEmpresaGrupo->getId())
                                        );
                }
                else
                {
                $objAdmiDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                ->findOneBy(array ('nombreDepartamento' => $arrayData['nombreDepartamento'],
                                    'estado'             => array('Activo','Modificado')),
                                    array('empresaCod'   => 'Asc')
                                    );                    
                }

                if (!is_object($objAdmiDepartamento))
                {
                    throw new \Exception('ERROR : El departamento '.$arrayData['nombreDepartamento'].' no existe');
                }

                $objInfoPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                    ->findOneByLogin($strUsrCreacion);

                if (!is_object($objInfoPersona) || !in_array($objInfoPersona->getEstado(), array('Activo','Pendiente','Modificado')))
                {
                    throw new \Exception('ERROR : La persona no existe o no se encuentra Activa');
                }

                $intIdPersona = $objInfoPersona->getId();

                if ($strOrigen === 'iniciar' || $strOrigen === 'pausar' || $strOrigen === 'reanudar')
                {
                    $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                        ->findOneBy(array ('personaId'      => $objInfoPersona->getId(),
                                           'estado'         => 'Activo',
                                           'departamentoId' => $objAdmiDepartamento->getId()));

                    if (!is_object($objInfoPersonaEmpresaRol))
                    {
                        throw new \Exception('ERROR : La persona no tiene un rol persona empresa asociado');
                    }

                    $arrayAdministrarTarea["strTipo"]              = $strOrigen;
                    $arrayAdministrarTarea["objDetalle"]           = $objInfoDetalle;
                    $arrayAdministrarTarea["strObservacion"]       = urldecode($arrayData['observacion']);
                    $arrayAdministrarTarea["strUser"]              = $strUsrCreacion;
                    $arrayAdministrarTarea["strIpUser"]            = $strIpCreacion;
                    $arrayAdministrarTarea["strCodEmpresa"]        = $objInfoEmpresaGrupo->getId();
                    $arrayAdministrarTarea["intPersonaEmpresaRol"] = $objInfoPersonaEmpresaRol->getId();
                    $arrayAdministrarTarea["idDepartamento"]       = $objAdmiDepartamento->getId();

                    $arrayRetorno = $this->serviceSoporte->administrarTarea($arrayAdministrarTarea);

                    if (empty($arrayRetorno) || $arrayRetorno['strRespuesta'] !== 'OK')
                    {
                        throw new \Exception('ERROR : No se pudo cambiar el estado de la tarea');
                    }
                }
                elseif ($strOrigen === 'reasignar' || $strOrigen === 'reprogramar')
                {
                    $objAdmiCanton = $this->emComercial->getRepository("schemaBundle:AdmiCanton")
                        ->findOneByNombreCanton($arrayData['nombreCanton']);
                    if (!is_object($objAdmiCanton))
                    {
                        throw new \Exception('ERROR : El cantón '.$arrayData['nombreCanton'].' no existe en telcos, '.
                                'por favor comunicarse con Sistemas');
                    }

                    if ($strOrigen === 'reasignar')
                    {
                        if (isset($arrayData['asignado']) && !empty($arrayData['asignado']))
                        {
                            $objInfoPersonaEmpresaRol = $this->emComercial->getRepository("schemaBundle:InfoPersonaEmpresaRol")
                                                                            ->findOneBy(array ('id'             => $arrayData['asignado'],
                                                                                               'estado'         => 'Activo'));
       
                            if(!empty($objInfoPersonaEmpresaRol))
                            {
                                $objInfoOficinaGrupo = $this->emComercial->getRepository("schemaBundle:InfoOficinaGrupo")
                                                                        ->find($objInfoPersonaEmpresaRol->getOficinaId());

                                if(!empty($objInfoOficinaGrupo))
                                {
                                    $objAdmiDepartamento = $this->emSoporte->getRepository("schemaBundle:AdmiDepartamento")
                                            ->findOneBy(array('nombreDepartamento' => $arrayData['nombreDepartamento'],
                                                              'estado'             => array('Activo','Modificado'),
                                                              'empresaCod'         => $objInfoOficinaGrupo->getEmpresaId()->getId()));

                                    if (!is_object($objAdmiDepartamento))
                                    {
                                        throw new \Exception('ERROR : El asignado no existe o se encuentra en un departamento distinto');
                                    }
                                }
                                else
                                {
                                    throw new \Exception('ERROR : El asignado no existe o se encuentra en una oficina distinta');
                                }
                            }
                            else
                            {
                                throw new \Exception('ERROR : El asignado no existe');
                            }
                            $intIdPersona = $objInfoPersonaEmpresaRol->getPersonaId()->getId();
                        }
                        else
                        {
                            $arrayParametrosResponsable["intCantonId"]     = $objAdmiCanton->getId();
                            $arrayParametrosResponsable["strEstado"]       = "Activo";
                            $arrayParametrosResponsable["intDepartamento"] = $objAdmiDepartamento->getId();
                            $arrayParametrosResponsable["strRol"]          = "Jefe Departamental";
                            $arrayParametrosResponsable["strTipoRol"]      = "Empleado";
                            $arrayParametrosResponsable["strEmpresaCod"]   = $objInfoEmpresaGrupo->getId();
                            $arrayEmpleadoJefe = $this->emComercial->getRepository("schemaBundle:InfoDetalleSolicitud")
                                ->getJefePorDepartamento($arrayParametrosResponsable);
                            if (empty($arrayEmpleadoJefe) || is_null($arrayEmpleadoJefe['idPersona']))
                            {
                                throw new \Exception('ERROR : No se pudo obtener los datos del jefe departamental');
                            }
                            $intIdPersona = $arrayEmpleadoJefe['idPersona'];
                        }
                                                 
                        $objAdmiTarea= $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                            ->findOneBy(array ('id' => $arrayData['idFinTarea'],
                                                'estado'      => 'Activo'));
                        if (!is_object($objAdmiTarea))
                        {
                            throw new \Exception('ERROR : El id tarea '.$arrayData['idTarea'].' no existe en telcos');
                        }

                    }

                    $arrayParametrosReasignacion['idEmpresa']             = $objInfoEmpresaGrupo->getId();
                    $arrayParametrosReasignacion['prefijoEmpresa']        = $objInfoEmpresaGrupo->getPrefijo();
                    $arrayParametrosReasignacion['id_detalle']            = $objInfoDetalle->getId();
                    $arrayParametrosReasignacion['motivo']                = $arrayData['observacion'];
                    $arrayParametrosReasignacion['departamento_asignado'] = $objAdmiDepartamento->getId();
                    $arrayParametrosReasignacion['id_departamento']       = $objAdmiDepartamento->getId();
                    $arrayParametrosReasignacion['empleado_asignado']     = $intIdPersona;
                    $arrayParametrosReasignacion['fecha_ejecucion']       = $arrayData['fecha'].' '.$arrayData['hora'];
                    $arrayParametrosReasignacion['user']                  = $strUsrCreacion;
                    $arrayParametrosReasignacion['empleado_logueado']     = $strUsrCreacion;
                    $arrayParametrosReasignacion['clientIp']              = ($strIpCreacion ? $strIpCreacion : "127.0.0.1");
                    $arrayParametrosReasignacion['idFinTarea']            = ($objAdmiTarea != null) ? $objAdmiTarea->getId() : null;
                    $arrayRespuestaReasgiancion = $this->serviceSoporte->reasignarTarea($arrayParametrosReasignacion);
                    if ($arrayRespuestaReasgiancion['success'] === false)
                    {
                        throw new \Exception('ERROR : No se pudo cambiar el estado de la tarea');
                    }
                }
                elseif ($strOrigen === 'cancelar')
                {
                    $arrayPeticion = array ('intIdDepartamentoOrigen' => $objAdmiDepartamento->getId(),
                                            'strUserCreacion'         => $strUsrCreacion,
                                            'strIpCreacion'           => ($strIpCreacion ? $strIpCreacion : "127.0.0.1"),
                                            'intIdEmpresa'            => $objInfoEmpresaGrupo->getId());

                    $arrayResultadoCancelada = $this->serviceSoporte->cancelarTarea($objInfoDetalle,
                                                                                    null,
                                                                                    'N',
                                                                                    $arrayPeticion,
                                                                                    array ('observacion' => $arrayData['observacion'],
                                                                                           'esRequest'   => 'N'));

                    if (empty($arrayResultadoCancelada) || $arrayResultadoCancelada !== 'OK')
                    {
                        throw new \Exception('ERROR : No se pudo cambiar el estado de la tarea');
                    }
                }
                elseif ($strOrigen === 'finalizar')
                {
                    $intTareasIniciadas = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->getVecesTareasIniciadas(array ('intDetalleId' => $objInfoDetalle->getId()));
                    if ($intTareasIniciadas > 1)
                    {
                        $objDateTareaFechaInicio = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                            ->getTareaIniciada(array ('intDetalleId' => $objInfoDetalle->getId()));
                        $strFeCreacionTareaAceptada = strval(date_format($objDateTareaFechaInicio, "d-m-Y H:i"));
                        $strFechaCreacionTarea      = new \DateTime($strFeCreacionTareaAceptada);
                    }
                    else
                    {
                        $arrayFechaInicioTarea = $this->emSoporte->getRepository('schemaBundle:InfoTareaSeguimiento')
                            ->getFechaInicioTarea($objInfoDetalle->getId());
                        if (!empty($arrayFechaInicioTarea))
                        {
                            $strFeCreacionTareaAceptada = ($arrayFechaInicioTarea[0]["FechaInicioTarea"] ?
                                strval(date_format($arrayFechaInicioTarea[0]["FechaInicioTarea"], "d-m-Y H:i")) : "");
                            $strFechaCreacionTarea = new \DateTime($strFeCreacionTareaAceptada);
                        }
                    }
                    $objDatetimeFinal            = new \DateTime();
                    $objDatetimeDiferenciaFechas = $objDatetimeFinal->diff($strFechaCreacionTarea);
                    $intMinutos                  = $objDatetimeDiferenciaFechas->days * 24 * 60;
                    $intMinutos                 += $objDatetimeDiferenciaFechas->h * 60;
                    $intMinutos                 += $objDatetimeDiferenciaFechas->i;
                    $arrayFechaFinalizacion      = explode(" ", $objDatetimeFinal->date);
                    $arrayHoraFinalizacion       = explode(".", $arrayFechaFinalizacion[1]);
                    $arrayFechaEjecucion         = explode(" ", $objInfoDetalle->getFeSolicitada()->date);
                    $arrayHoraEjecucion          = explode(".", $arrayFechaEjecucion[1]);
                    $objAdmiProceso = $this->emSoporte->getRepository('schemaBundle:AdmiProceso')
                        ->findOneBy(array ('nombreProceso' => $arrayData['nombreProceso'],
                                           'estado'        => 'Activo'));
                    if (!is_object($objAdmiProceso))
                    {
                        throw new \Exception('ERROR : El proceso '.$arrayData['nombreProceso'].' no existe en telcos');
                    }
                    $objAdmiTarea= $this->emSoporte->getRepository('schemaBundle:AdmiTarea')
                        ->findOneBy(array ('nombreTarea' => $arrayData['nombreTarea'],
                                           'estado'      => 'Activo',
                                           'procesoId'   => $objAdmiProceso));
                    if (!is_object($objAdmiTarea))
                    {
                        throw new \Exception('ERROR : La tarea '.$arrayData['nombreTarea'].' no existe en telcos');
                    }
                    $arrayRespuestaFinalizar = $this->serviceSoporte->finalizarTarea(array (
                            'idEmpresa'            => $objInfoEmpresaGrupo->getId(),
                            'prefijoEmpresa'       => $objInfoEmpresaGrupo->getPrefijo(),
                            'idDetalle'            => $objInfoDetalle->getId(),
                            'tarea'                => $objAdmiTarea->getId(),
                            'tiempoTotal'          => $intMinutos,
                            'fechaCierre'          => $arrayFechaFinalizacion[0],
                            'horaCierre'           => $arrayHoraFinalizacion[0],
                            'fechaEjecucion'       => $arrayFechaEjecucion[0],
                            'horaEjecucion'        => $arrayHoraEjecucion[0],
                            'esSolucion'           => $arrayData['esSolucion'],
                            'idAsignado'           => $objInfoPersona->getId(),
                            'observacion'          => $arrayData['observacion'],
                            'empleado'             => $objInfoPersona->getNombres().' '.$objInfoPersona->getApellidos(),
                            'usrCreacion'          => $strUsrCreacion,
                            'ipCreacion'           => $strIpCreacion,
                            'accionTarea'          => 'finalizada',
                            'boolEsAppSyscloud'    => true,
                            'strEnviaDepartamento' => 'N',
                            'intIdDepartamento'    => $objAdmiDepartamento->getId()));
                    if (empty($arrayRespuestaFinalizar) || $arrayRespuestaFinalizar['success'] === false)
                    {
                        throw new \Exception('ERROR : No se pudo cambiar el estado de la tarea');
                    }
                }
                else
                {
                    throw new \Exception("ERROR : Estado $strOrigen no permitido");
                }
                $arrayRespuesta['status']    = 'OK';
                $arrayRespuesta['mensaje']   = 'Cambio de estado realizado correctamente';
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error general en el cambio de Estado de la Tarea, por favor comunicarse con Sistemas';
                if(strpos($objException->getMessage(),'ERROR : ') !== false)
                {
                    $strMessage = explode('ERROR : ',$objException->getMessage())[1];
                }

                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->gestionarTareasTelcoSys()',
                                                 $strCodigo.'|'.$objException->getMessage(),
                                                 $arrayDataAudit['usrCreacion'],
                                                 $arrayDataAudit['ipCreacion']);

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->gestionarTareasTelcoSys()',
                                                 $strCodigo.'|'.json_encode($arrayParametros),
                                                 $arrayDataAudit['usrCreacion'],
                                                 $arrayDataAudit['ipCreacion']);

                $arrayRespuesta = array('status' => 'ERROR','mensaje' => $strMessage);
            }
            return $arrayRespuesta;
        }

        /**
         * Método encargado de notificar el cambio de estado de la tarea al sistema de Sys Cloud-Center.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 22-10-2018
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 04-12-2021 - Se modifica el método para validar si la tarea tiene alguna asignación a los departamentos
         *                           que pertenece al área de data-center.
         * 
         * 
         * @author Pedro Velez <psvelez@telconet.ec>
         * @version 1.2 13-08-2021 - Se modifica para verificar si la tarea fue creada por los departamentos que pertenece
         *                           al area de Data-Center
         *
         *
         * @param  Array $arrayParametros [
         *                                  intIdComunicacion : Id de comunicación o Número de tarea.
         *                                  strObservacion    : Observación.
         *                                  strCodEmpresa     : Código de la empresa.
         *                                  strUser           : Usuario quien realiza la acción en la tarea.
         *                                  strIp             : Ip del usuario quien realiza la acción en la tarea.
         *                                ]
         * @return Array $arrayRespuesta
         */
        public function notificarCambioEstadoSysCloud($arrayParametros)
        {
            $strSistema        = 'Sys Cloud-Center';
            $strLogin          = '';
            $strRazonSocial    = '';
            $boolSeguirFlujo   =  false;
            $strUsuario        =  $arrayParametros['strUser'] ? $arrayParametros['strUser'] : 'telcos';
            $strIpUsuario      =  $arrayParametros['strIp']   ? $arrayParametros['strIp']   : '127.0.0.1';
            $intIdComunicacion =  $arrayParametros['intIdComunicacion'];
            $strObservacion    =  $arrayParametros['strObservacion'];
            $strCodEmpresa     =  $arrayParametros['strCodEmpresa'];

            try
            {
                //Verificamos si la bandera se encuentra inhabilitada.
                if ($this->strExecuteSysCloudCenter === null || $this->strExecuteSysCloudCenter === ''
                        || strtoupper($this->strExecuteSysCloudCenter) !== 'S')
                {
                    throw new \Exception('Error : La bandera para la comunicación con el sistema de '.
                                          $strSistema.' se encuentra inhabilitada');
                }

                //Validamos que los parámetro principales no sean nulos.
                if (!isset($arrayParametros['intIdComunicacion']) || empty($arrayParametros['intIdComunicacion']) ||
                    !isset($arrayParametros['strObservacion'])    || empty($arrayParametros['strObservacion'])    ||
                    !isset($arrayParametros['strCodEmpresa'])     || empty($arrayParametros['strCodEmpresa']))
                {
                    throw new \Exception('Error : Información Incompleta');
                }

                //Obtenemos el objeto InfoComunicacion.
                $objInfoComunicacion = $this->emComunicacion->getRepository('schemaBundle:InfoComunicacion')
                        ->find($intIdComunicacion);

                if (!is_object($objInfoComunicacion))
                {
                    throw new \Exception('Error : Id de comunicación no existe en telcos');
                }

                //Obtenemos el objeto InfoDetalle.
                $objInfoDetalle = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->find($objInfoComunicacion->getDetalleId());

                if (!is_object($objInfoDetalle))
                {
                    throw new \Exception('Error : Id de detalle no existe en telcos');
                }

                //Obtenemos la fecha de creación de la tarea.
                $objFechaCreacionTarea = $objInfoDetalle->getFeCreacion();
                if (!is_object($objFechaCreacionTarea))
                {
                    throw new \Exception('Error : La fecha de creación de la tarea es nula');
                }

                $objAdmiTarea     = is_object($objInfoDetalle->getTareaId()) ? $objInfoDetalle->getTareaId() : null;
                $objAdmiProceso   = is_object($objAdmiTarea) && is_object($objAdmiTarea->getProcesoId()) ?
                        $objAdmiTarea->getProcesoId() : null;
                $strNombreTarea   = is_object($objAdmiTarea)   ? $objAdmiTarea->getNombreTarea()     : null;
                $strNombreProceso = is_object($objAdmiProceso) ? $objAdmiProceso->getNombreProceso() : null;

                //Obtenemos todas las asignaciones y verificacimos si existe alguna asignación hacia el área de data-center.
                $arrayInfoDetalleAsignacion = $this->emSoporte->getRepository('schemaBundle:InfoDetalleAsignacion')
                        ->findByDetalleId($objInfoComunicacion->getDetalleId());

                foreach ($arrayInfoDetalleAsignacion as $objDetalleAsignacion)
                {
                    if (is_object($objDetalleAsignacion))
                    {
                        $objAdmiDepartamento = $this->emGeneral->getRepository('schemaBundle:AdmiDepartamento')
                                ->find($objDetalleAsignacion->getAsignadoId());

                        if (is_object($objAdmiDepartamento))
                        {
                            $arrayAdmiParametroUsrs = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('USUARIOS LIMITADORES DE GESTION DE TAREAS',
                                             'SOPORTE','','','',
                                              $objAdmiDepartamento->getNombreDepartamento(),'','','','');

                            if (!empty($arrayAdmiParametroUsrs) && $arrayAdmiParametroUsrs > 0)
                            {
                                $boolSeguirFlujo = true;
                                break;
                            }
                        }
                    }
                }

                $arrayInfoDetalleHistorial = $this->emSoporte->getRepository('schemaBundle:InfoDetalleHistorial')
                ->findBy(array(
                    'detalleId' => $objInfoComunicacion->getDetalleId(),
                    'estado'    => 'Asignada'
                ), array(
                    'id' => 'ASC'
                ));
        
                $boolEsUsrLimitador = false;
                if (count($arrayInfoDetalleHistorial) >= 0)
                {
                    $intDptoOrigen = (!empty($arrayInfoDetalleHistorial[0])) ? $arrayInfoDetalleHistorial[0]->getDepartamentoOrigenId() : null;
                    $arrayAdmiParametroUsrs = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                    ->getOne('USUARIOS LIMITADORES DE GESTION DE TAREAS',
                                            'SOPORTE','','',$intDptoOrigen,
                                            '','','','','');
                    if (!empty($arrayAdmiParametroUsrs) && $arrayAdmiParametroUsrs > 0)
                    {
                        $boolEsUsrLimitador = true;
                    }
                }

                if (!$boolSeguirFlujo && !$boolEsUsrLimitador)
                {
                    return;
                }

                //Obtenemos los datos de la persona que finaliza, anula o cancela la tarea.
                $arrayEstadoPersona = array('Activo','Modificado');
                $arrayDatosPersonas = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                        ->getInfoDatosPersona(array ('strRol'                     => 'Empleado',
                                                     'strCodEmpresa'              =>  $strCodEmpresa,
                                                     'strLogin'                   =>  $strUsuario,
                                                     'strEstadoPersona'           =>  $arrayEstadoPersona,
                                                     'strEstadoPersonaEmpresaRol' => 'Activo'));

                if (empty($arrayDatosPersonas) || $arrayDatosPersonas['status'] !== 'ok')
                {
                    $arrayDatosPersonas['result'][0]['nombres']   = 'Proceso';
                    $arrayDatosPersonas['result'][0]['apellidos'] = 'Masivo';
                    $arrayDatosPersonas['result'][0]['nombreDepartamento'] = 'SISTEMAS';
                }

                //Obtenemos el afectado
                $arrayResultAfectado = $this->getAfectado(array ('objInfoComunicacion' => $objInfoComunicacion,
                                                                 'strUser'             => $strUsuario,
                                                                 'strIp'               => $strIpUsuario));

                if ($arrayResultAfectado['status']   === 'ok' &&
                    $arrayResultAfectado['strLogin'] !== null &&
                    $arrayResultAfectado['strLogin'] !== '')
                {
                    $strLogin = $arrayResultAfectado['strLogin'];

                    //Obtenemos la razón social del punto
                    $arrayPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                            ->getArrayPuntosClientesPorGrupoProducto(array('strLogin'          =>  $strLogin,
                                                                           'strDescripcionRol' => 'Cliente',
                                                                           'strEstado'         =>  array('Activo','Modificado')));

                    if (strtoupper($arrayPunto['status']) === 'OK' && !empty($arrayPunto['result']))
                    {
                        $strRazonSocial = $arrayPunto['result'][0]['razonSocial'];
                    }

                    if (empty($strRazonSocial))
                    {
                        $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                ->findOneByLogin($strLogin);

                        if (is_object($objPunto))
                        {
                            $arrayPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                    ->obtieneTitularPorLogin(array('intPuntoId' => $objPunto->getId()));

                            if (!empty($arrayPunto))
                            {
                                $strRazonSocial = $arrayPunto[0]['TITULAR'];
                            }
                        }
                    }
                }

                //Array Resource.
                $arraySource = array('name' => 'APP.TELCOSYS','tipoOriginID' => 'IP','originID' => $strIpUsuario);

                //Solicitamos el token.
                $arrayRespuestaToken = $this->serviceTokenValidator
                        ->generateToken($arraySource, 'Telcos', 'TelcoSysWSController', 'procesarAction', 'TELCOSYS');

                //Validamos la respuesta de la solicitud del token.
                if($arrayRespuestaToken['status'] !== TokenValidatorService::$TOKEN_OK)
                {
                    throw new \Exception('Error al solicitar el token '.$arrayRespuestaToken['message']);
                }

                //Preparamos el Json para enviar al Sistema de Sys Cloud-Center.
                $arrayParametrosSCC['token']   =  $arrayRespuestaToken['token'];
                $arrayParametrosSCC['user']    = 'TELCOSYS';
                $arrayParametrosSCC['source']  =  $arraySource;
                $arrayParametrosSCC['opTarea'] = 'putTareasSysCluod';

                $arrayParametrosSCC['dataAuditoria'] = array ('usrCreacion' => $strUsuario,
                                                              'ipCreacion'  => $strIpUsuario);

                $strUsrFinaliza = $arrayDatosPersonas['result'][0]['nombres'].' '.
                                  $arrayDatosPersonas['result'][0]['apellidos'];
                $strDepFinaliza = $arrayDatosPersonas['result'][0]['nombreDepartamento'];

                //Obtenemos el ultimo estado de la tarea.
                $strEstadoTarea = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->getUltimoEstado($objInfoComunicacion->getDetalleId());

                $arrayParametrosSCC['dataTarea'] = array ('login'                => $strLogin,
                                                          'razonSocial'          => $strRazonSocial,
                                                          'idTarea'              => $intIdComunicacion,
                                                          'estado'               => $strEstadoTarea,
                                                          'observacion'          => $strObservacion,
                                                          'fecha'                => date("Y-m-d"),
                                                          'hora'                 => date('H:i:s'),
                                                          'usuarioAsigna'        => $strUsrFinaliza,
                                                          'departamentoAsigna'   => $strDepFinaliza,
                                                          'usuarioAsignado'      => $strUsrFinaliza,
                                                          'departamentoAsignado' => $strDepFinaliza,
                                                          'proceso'              => $strNombreProceso,
                                                          'tarea'                => $strNombreTarea,
                                                          'fechaCreacion'        => date_format($objFechaCreacionTarea, 'Y-m-d'),
                                                          'horaCreacion'         => date_format($objFechaCreacionTarea, 'H:i:s'));

                //Establecemos la comunicación.
                $arrayResult = $this->serviceSoporte->comunicacionWsRestClient(
                        array ('strUrl'       => $this->strUrlSysCloudCenter,
                               'arrayData'    => $arrayParametrosSCC,
                               'arrayOptions' => array(CURLOPT_SSL_VERIFYPEER => false)));

                //Validamos la respuesta.
                if ($arrayResult['mensaje'] === 'fail')
                {
                    throw new \Exception('Error : '.$arrayResult['descripcion']);
                }

                //Validamos el resultado.
                if (strtoupper($arrayResult['result']['status']) !== 'OK')
                {
                    throw new \Exception('Error : '.$arrayResult['result']['message']);
                }

                $arrayRespuesta = array ('status' => 'ok', 'message' => 'El proceso se ejecutó correctamente.');
            }
            catch (\Exception $objException)
            {
                $strMessage = 'Error al notificar la tarea';

                if(strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = explode('Error : ',$objException->getMessage())[1];
                }

                $this->serviceUtil->insertError('Telcos+',
                                                'ProcesoService->notificarCambioEstadoSysCloud',
                                                 $strMessage,
                                                 $strUsuario,
                                                 $strIpUsuario);

                $arrayRespuesta = array ('status' => 'fail', 'message' => $strMessage);
            }
            return $arrayRespuesta;
       }

        /**
         * Método encargado de enviar a crear la tarea en el Sistema de Sys cloud-Center
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 - 17-01-2019
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 - 19-01-2021 - Se inserta en el log los parámetros recibios en el método.
         *
         * @param Array $arrayParametros [
         *                                  objInfoComunicacion = Objeto de la info comunicación,
         *                                  strNombreTarea      = Nombre de la tarea,
         *                                  strNombreProceso    = Nombre del proceso,
         *                                  strObservacion      = Observación,
         *                                  strFechaApertura    = Fecha de apertura de la tarea,
         *                                  strHoraApertura     = Hora de apertura de la tarea,
         *                                  strUser             = Usuario u/o Login de la persona quien manda a crear la tarea,
         *                                  strIpAsigna         = Ip de la persona quien manda a crear la tarea
         *                                  strUserAsigna'      = Persona quien asigna la tarea (Nombres/Apellidos o Razón Social),
         *                                  strDeparAsigna'     = Departamento quien asigna la tarea,
         *                                  strUserAsignado'    = Persona quien se le asigna la tarea (Nombres/Apellidos o Razón Social),
         *                                  strDeparAsignado'   = Departamento quien se le asigna la tarea
         *                               ]
         *
         * @return Array
         */
        public function putTareasSysCluod($arrayParametros)
        {
            $strSistema          = 'Sys Cloud-Center';
            $arrayParametrosSCC  = array();
            $arrayFiles          = array();
            $strLogin            = null;
            $objInfoComunicacion = $arrayParametros['objInfoComunicacion'];
            $boolBanderaSyscloud = $arrayParametros['boolBanderaSyscloud'];
            $strCodigo           = '';

            try
            {
                if ($this->strExecuteSysCloudCenter === null || $this->strExecuteSysCloudCenter === ''
                        || strtoupper($this->strExecuteSysCloudCenter) !== 'S')
                {
                    throw new \Exception('Error : La bandera para la comunicación con el sistema de '.$strSistema.
                            ' se encuentra inhabilitada');
                }

                if (!is_object($objInfoComunicacion) || $arrayParametros['strNombreTarea']   === '' ||
                    $arrayParametros['strNombreTarea']   === null || $arrayParametros['strNombreProceso'] === '' ||
                    $arrayParametros['strNombreProceso'] === null || $arrayParametros['strFechaApertura'] === '' ||
                    $arrayParametros['strFechaApertura'] === null || $arrayParametros['strHoraApertura']  === '' ||
                    $arrayParametros['strHoraApertura']  === null)
                {
                    throw new \Exception('Error : La Información a enviar está incompleta.');
                }

                if ($arrayParametros['strUserAsigna']    === ''   || $arrayParametros['strUserAsigna'] === null    ||
                    $arrayParametros['strDeparAsigna']   === ''   || $arrayParametros['strDeparAsigna']   === null ||
                    $arrayParametros['strUserAsignado']  === ''   || $arrayParametros['strUserAsignado']  === null ||
                    $arrayParametros['strDeparAsignado'] === ''   || $arrayParametros['strDeparAsignado'] === null)
                {
                    throw new \Exception('Error : La Información a enviar está incompleta..');
                }

                $arrayAdmiParametroUsrs = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('USUARIOS LIMITADORES DE GESTION DE TAREAS',
                                 'SOPORTE','','','',$arrayParametros['strDeparAsignado'],'','','','');

                if((empty($arrayAdmiParametroUsrs) || $arrayAdmiParametroUsrs < 1) && empty($boolBanderaSyscloud))
                {
                    throw new \Exception('Error : El departamento '.$arrayParametros['strDeparAsignado'].' no se encuentra configurado');
                }

                $arraySource = array("name"         => 'APP.TELCOSYS',
                                     "originID"     => $arrayParametros["strIpAsigna"],
                                     "tipoOriginID" => "IP");

                $arrayRespuestaToken = $this->serviceTokenValidator
                        ->generateToken($arraySource, 'Telcos', 'TelcoSysWSController', 'procesarAction', 'TELCOSYS');

                //Validamos la respuesta de la solicitud del token.
                if($arrayRespuestaToken['status'] !== TokenValidatorService::$TOKEN_OK)
                {
                    throw new \Exception('Error al solicitar el token '.$arrayRespuestaToken['message']);
                }

                //Vericamos el afectado
                $arrayResultAfectado = $this->getAfectado(array ('objInfoComunicacion' => $objInfoComunicacion,
                                                                 'strUser'             => $arrayParametros['strUser'],
                                                                 'strIp'               => $arrayParametros['strIpAsigna']));

                if ($arrayResultAfectado['status']   === 'ok' &&
                    $arrayResultAfectado['strLogin'] !== null &&
                    $arrayResultAfectado['strLogin'] !== '')
                {
                    $strLogin = $arrayResultAfectado['strLogin'];

                    //Obtenemos la razón social del punto
                    $arrayPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                            ->getArrayPuntosClientesPorGrupoProducto(array('strLogin'          => $strLogin,
                                                                           'strDescripcionRol' => 'Cliente',
                                                                           'strEstado'         => array('Activo','Modificado')));

                    if (strtoupper($arrayPunto['status']) === 'OK' && !empty($arrayPunto['result']))
                    {
                        $strRazonSocial = $arrayPunto['result'][0]['razonSocial']; 
                    }
                    if(empty($strRazonSocial))
                    {
                        $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                      ->findOneByLogin($strLogin);
                                                        
                        if(is_object($objPunto))
                        {
                            $arrayPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                                                        ->obtieneTitularPorLogin(array('intPuntoId' => $objPunto->getId()));
                        }

                        if (!empty($arrayPunto))
                        {
                            $strRazonSocial = $arrayPunto[0]['TITULAR']; 
                        }
                        else
                        {
                            $strRazonSocial = "";
                        }
                    }
                }

                //Obtenemos el ultimo estado de la tarea.
                $strEstadoTarea = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                        ->getUltimoEstado($objInfoComunicacion->getDetalleId());
                $strEstadoTarea = strtolower($strEstadoTarea) == 'rechazada' ? 'Asignada' : $strEstadoTarea;

                //Preparamos el Json para enviar al Sistema de Sys Cloud-Center.
                $arrayParametrosSCC['token']   = $arrayRespuestaToken['token'];
                $arrayParametrosSCC['user']    = 'TELCOSYS';
                $arrayParametrosSCC['source']  = $arraySource;
                $arrayParametrosSCC['opTarea'] = 'putTareasSysCluod';

                $arrayParametrosSCC['dataAuditoria'] = array ('usrCreacion' => $arrayParametros['strUser'],
                                                              'ipCreacion'  => $arrayParametros['strIpAsigna']);

                //Obtenemos la fecha de creación de la tarea.
                $objFechaCreacionTarea = $objInfoComunicacion->getFeCreacion();
                if (!is_object($objFechaCreacionTarea))
                {
                    throw new \Exception('Error : La fecha de creación de la tarea es nula');
                }

                //Array Principal
                $arrayParametrosSCC['dataTarea'] = array ('login'                => $strLogin,
                                                          'razonSocial'          => $strRazonSocial,
                                                          'idTarea'              => $objInfoComunicacion->getId(),
                                                          'estado'               => $strEstadoTarea,
                                                          'observacion'          => $arrayParametros['strObservacion'],
                                                          'fecha'                => $arrayParametros['strFechaApertura'],
                                                          'hora'                 => $arrayParametros['strHoraApertura'],
                                                          'usuarioAsigna'        => $arrayParametros['strUserAsigna'],
                                                          'departamentoAsigna'   => $arrayParametros['strDeparAsigna'],
                                                          'usuarioAsignado'      => $arrayParametros['strUserAsignado'],
                                                          'departamentoAsignado' => $arrayParametros['strDeparAsignado'],
                                                          'proceso'              => $arrayParametros['strNombreProceso'],
                                                          'tarea'                => $arrayParametros['strNombreTarea'],
                                                          'fechaCreacion'        => date_format($objFechaCreacionTarea, 'Y-m-d'),
                                                          'horaCreacion'         => date_format($objFechaCreacionTarea, 'H:i:s'));

                //Verificamos si la tarea creada tiene archivos adjuntos.
                $strJsonArchivo = $this->emComunicacion->getRepository('schemaBundle:InfoCaso')
                        ->getJsonDocumentosCaso(array ('strTareaIncAudMant' => 'N',
                                                       'intIdDetalle'       => $objInfoComunicacion->getDetalleId(),
                                                       'strPathTelcos'      => $this->strPath."telcos/web"),
                                                $this->emInfraestructura,
                                                $arrayParametros['strUser']);

                //Verificamos si la consulta retorno datos.
                $arrayArchivos = json_decode($strJsonArchivo);
                if (isset($arrayArchivos->total) && isset($arrayArchivos->encontrados) && $arrayArchivos->total > 0)
                {
                    foreach ($arrayArchivos->encontrados as $objFiles)
                    {

                        //Obtenemos el archivo
                        $objFile       = file_get_contents($objFiles->linkVerDocumento); //Obtenemos el archivo.
                        $strFile64     = base64_encode($objFile); //Codificamos el archivo a base64.
                        $arrayFileName = explode('.', $objFiles->ubicacionLogica); //Separamos el nombre con la extensión.

                        //Armamos el array de los archivos adjuntos.
                        $arrayFiles[]['archivo'] = array ('fileName'      => $arrayFileName[0],
                                                          'fileExtension' => $arrayFileName[1],
                                                          'file'          => $strFile64);
                    }

                    $arrayParametrosSCC['archivos'] = $arrayFiles;
                }

                //Establecemos la comunicación.
                $arrayResult = $this->serviceSoporte->comunicacionWsRestClient(
                        array ('strUrl'       => $this->strUrlSysCloudCenter,
                               'arrayData'    => $arrayParametrosSCC,
                               'arrayOptions' => array(CURLOPT_SSL_VERIFYPEER => false)));

                //Validamos la respuesta.
                if ($arrayResult['mensaje'] === 'fail')
                {
                    throw new \Exception('Error : '.$arrayResult['descripcion']);
                }

                //Validamos el resultado.
                if (strtoupper($arrayResult['result']['status']) !== 'OK')
                {
                    throw new \Exception('Error : '.$arrayResult['result']['message']);
                }

                //Obtenemos la característica TAREA_SYS_CLOUD_CENTER.
                $objAdmiCaracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                        ->findOneBy(array ('descripcionCaracteristica' => 'TAREA_SYS_CLOUD_CENTER',
                                           'estado'                    => 'Activo'));

                if (is_object($objAdmiCaracteristica))
                {
                    //Verificamos si existe la tarea con la característica TAREA_SYS_CLOUD_CENTER.
                    $objInfoTareaCaracteristica = $this->emSoporte->getRepository('schemaBundle:InfoTareaCaracteristica')
                        ->findOneBy(array ('tareaId'          => $objInfoComunicacion->getId(),
                                           'caracteristicaId' => $objAdmiCaracteristica->getId()));

                    $strOpcion = 'new';
                    if (is_object($objInfoTareaCaracteristica))
                    {
                        $strOpcion = 'edit';
                    }

                    $this->serviceSoporte->setTareaCaracteristica(
                            array ('intIdComunicacion'          => $objInfoComunicacion->getId(),
                                   'intIdDetalle'               => $objInfoComunicacion->getDetalleId(),
                                   'intCaracteristicaId'        => $objAdmiCaracteristica->getId(),
                                   'strValor'                   => 'E', //Enviado
                                   'strUsrCreacion'             => $arrayParametros['strUser'],
                                   'strIpCreacion'              => $arrayParametros['strIpAsigna'],
                                   'strEstado'                  => 'Activo',
                                   'ojbInfoTareaCaracteristica' => $objInfoTareaCaracteristica,
                                   'strOpcion'                  => $strOpcion));
                }

                $arrayRespuesta = array ('status'  => 'ok',
                                         'message' => 'La tarea se envió al '.$strSistema);
            }
            catch (\Exception $objException)
            {
                $arrayParametros['idTarea'] = is_object($arrayParametros['objInfoComunicacion']) ?
                        $arrayParametros['objInfoComunicacion']->getId() : null;
                unset($arrayParametros['objInfoComunicacion']);

                $strMessage = 'Error al crear la tarea en '.$strSistema;

                if (strpos($objException->getMessage(),'Error : ') !== false)
                {
                    $strMessage = $objException->getMessage();
                }

                $strCodigo = (new \DateTime('now'))->format('YmdHis').substr(md5(uniqid(rand())),0,6);

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->putTareasSysCluod',
                                                 $strCodigo.'| 1 |'.$objException->getMessage(),
                                                 $strSistema.' '.$arrayParametros['strUser'],
                                                 $arrayParametros['strIpAsigna']);

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->putTareasSysCluod',
                                                 $strCodigo.'| 2 |'.json_encode($arrayParametrosSCC),
                                                 $strSistema.' '.$arrayParametros['strUser'],
                                                 $arrayParametros['strIpAsigna']);

                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->putTareasSysCluod',
                                                 $strCodigo.'| 3 |'.json_encode($arrayParametros),
                                                 $strSistema.' '.$arrayParametros['strUser'],
                                                 $arrayParametros['strIpAsigna']);

                $arrayRespuesta = array ('status'  => 'fail',
                                         'message' => $strMessage);
            }
            return $arrayRespuesta;
        }

        public function getFilePath($arrayParametros)
        {
            $strApp            = $arrayParametros['strApp'];
            $strModulo         = $arrayParametros['strModulo'];
            $strSubModulo      = $arrayParametros['strSubModulo'];
            $strPrefijoEmpresa = $arrayParametros['strPrefijoEmpresa'];
            $strModo           = $arrayParametros['strModo'];
            $strBasePath       = "";
            try
            {   
                if (!empty($strModo) && !is_string($strModo))
                {
                    $strModo = "public";
                }

                $objAdmiParametroCab = $this->emGeneral->getRepository('schemaBundle:AdmiParametroCab')
                                                    ->findOneBy(array(
                                                                    'nombreParametro' => 'RUTAS_ARCHIVOS',
                                                                    'estado'          => 'Activo'
                                                    ));

                if (!is_object($objAdmiParametroCab))
                {
                    throw new \Exception('Configuración de rutas no existente');
                }
                
                $objRutaSufijo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor1'      => $strApp,
                                                                'valor2'      => $strModulo,
                                                                'valor3'      => $strSubModulo,
                                                                'estado'      => 'Activo'
                                                ));

                if (!is_object($objRutaSufijo))
                {
                    throw new \Exception('Configuración Sufijo de rutas no existente');
                }

                $objRutaPrefijo = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->findOneBy(array(
                                                                'parametroId' => $objAdmiParametroCab,
                                                                'valor2'      => $strPrefijoEmpresa,
                                                                'estado'      => 'Activo'
                                                ));
                
                if (!is_object($objRutaPrefijo))
                {
                    throw new \Exception('Configuración Prefijo <' . $strPrefijoEmpresa .  '> de rutas no existente');
                }
                
                $strRutaPrefijo = $objRutaPrefijo->getValor4();
                $strRutaSufijo  = $objRutaSufijo->getValor4();
                $strFecha       = date('Y/m/d') . '/';

                if ($strModo == "root")
                {
                    $strBasePath = $this->strPath . $this->strPathUpload;
                }
                elseif ($strModo == "telcos")
                {
                    $strBasePath = $this->strPathUpload;
                }
                elseif ($strModo == "web")
                {
                    $strBasePath = "public/uploads/";
                }
                elseif ($strModo == "uploads")
                {
                    $strBasePath = "";
                }
                $strRuta = $strRutaPrefijo . $strRutaSufijo . $strFecha;

                $strCheckRuta = "public/uploads/" . $strRuta;

                if (!file_exists($strCheckRuta)) 
                {
                    $objOldMask = umask(0);
                    if(!mkdir($strCheckRuta, 0777, true))
                    {
                        throw new \Exception('Ocurrió un error al crear el directorio <' . $strRuta . '>');
                    }
                    umask($objOldMask);
                }

                $strRuta = $strBasePath . $strRuta;

                return array (
                                'status'  => 'ok', 
                                'message' => 'Ruta generada',
                                'path'    => $strRuta
                );
            } 
            catch (\Exception $objException)
            {
                return array ('status'  => 'fail', 'message' => $objException->getMessage());
            }

        }

        /**
         * Método que obtiene el login del afectado de una tarea o caso.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.0 - 17-01-2019
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.1 - 20-01-2021 - Se agrega la validación para retornar el
         *                             login del cliente desde la info_comunicacion.
         *
         * @author Germán Valenzuela <gvalenzuela@telconet.ec>
         * @version 1.2 - 28-01-2021 - Se agrega la validación para verificar si el remitente obtenido de
         *                             la info_comunicacion es un punto existente.
         *
         * @param Array $arrayParametros [
         *                                  objInfoComunicacion = Objeto de la info comunicación,
         *                                  strUser             = Usuario quien realiza la petición,
         *                                  strIp               = Ip del usuario quien realiza la petición
         *                               ]
         *
         * @return Array
         */
        public function getAfectado($arrayParametros)
        {
            $objInfoComunicacion = $arrayParametros['objInfoComunicacion'];

            try
            {
                if (!is_object($objInfoComunicacion))
                {
                    throw new \Exception('El parámetro objInfoComunicacion es inválido');
                }            
                $arrayClientesAfectados = $this->emSoporte->getRepository("schemaBundle:InfoDetalle")
                        ->getRegistrosAfectadosTotal($objInfoComunicacion->getDetalleId(), 'Cliente', 'Data');
                if (!empty($arrayClientesAfectados) && count($arrayClientesAfectados) > 0)
                {
                    $arrayClientes = false;
                    foreach ($arrayClientesAfectados as $arrayAfectado)
                    {
                        $arrayClientes[] = $arrayAfectado["afectadoNombre"];
                    }
                    $strLogin = "".implode(",",$arrayClientes)."";
                }
                if ($strLogin === "" || $strLogin === null)
                {
                    $objClienteAfectado = $this->emSoporte->getRepository("schemaBundle:InfoDetalle")
                            ->getClienteAfectadoTarea(array ('intIdDetalle' => $objInfoComunicacion->getDetalleId()));
                    if (is_object($objClienteAfectado))
                    {
                        $strLogin = $objClienteAfectado->getLogin();
                    }
                }
                if (($strLogin === "" || $strLogin === null) &&
                        ($objInfoComunicacion->getCasoId() !== null && $objInfoComunicacion->getCasoId() !== ''))
                {
                    $arrayDetalleInicial = $this->emSoporte->getRepository('schemaBundle:InfoDetalle')
                            ->getDetalleInicialCaso($objInfoComunicacion->getCasoId());
                    if ($arrayDetalleInicial[0]["detalleInicial"])
                    {
                        $arrayParteAfectada = $this->emSoporte->getRepository('schemaBundle:InfoParteAfectada')
                                ->findByDetalleId($arrayDetalleInicial[0]["detalleInicial"]);
                        if ($arrayParteAfectada)
                        {
                            $strLogin = $arrayParteAfectada[0]->getAfectadoNombre() ? $arrayParteAfectada[0]->getAfectadoNombre() : '';
                        }
                    }
                }
                if (($strLogin === "" || $strLogin === null) && $objInfoComunicacion->getRemitenteId() !== null
                        && $objInfoComunicacion->getRemitenteNombre() !== null)
                {
                   $objInfoPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')
                           ->findOneBy(array("id"    => $objInfoComunicacion->getRemitenteId(),
                                             "login" => $objInfoComunicacion->getRemitenteNombre()));

                   $strLogin = is_object($objInfoPunto) ? $objInfoPunto->getLogin() : "";
                }
                return array ('status' => 'ok', 'strLogin' => $strLogin);
            }
            catch (\Exception $objException)
            {
                $this->serviceUtil->insertError('Telcos+',
                                                'SoporteService->getAfectado',
                                                 $objException->getMessage(),
                                                 $arrayParametros['strUser'],
                                                 $arrayParametros['strIp']);
                return array ('status'  => 'fail', 'message' => 'Error al obtener la parte afectada');
            }
        }
    }
