<?php

namespace telconet\tecnicoBundle\Service;
use Symfony\Component\HttpFoundation\File\File;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase que sirve para grabar y procesar imagenes (fotos, firmas)
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 * 
 * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
 * @version 1.1 09-11-2019 Se agrega variable "serviceCompararImagen" para consumo del servicio
 * de comparacion de imagenes
 * 
 */
class ProcesarImagenesService
{
    private $convertirService;
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $emNaf;
    private $envioPlantilla;
    private $soporteService;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    private $rutaFisicaIncidenciasElementos;
    private $rutaFisicaActivaciones;
    private $rutaFisicaSoporte;
    private $strRutaFisicaTareas;
    private $extensionArchivo;
    private $serviceUtil;
    private $serviceCompararImagen;

    /**
    * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
    * @author Wilmer Vera <wvera@telconet.ec>
    * @version 1.0 03-04-2019 Se elimina el constructor y se crea las importaciones en las dependencias. 
    *
    * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
    * @version 1.1 09-11-2019 Se agrega dependencia para consumo del servicio de comparacion de imagenes
    *
    */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) 
    {
        $this->container            = $container;
        $this->convertirService = $this->container->get('tecnico.ConvertirJsonEnImagen');
        
        $this->emSoporte                = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura        = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad              = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial              = $this->container->get('doctrine')->getManager('telconet');
        $this->emGeneral                = $this->container->get('doctrine')->getManager('telconet_general');
        $this->emComunicacion           = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                    = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->envioPlantilla           = $this->container->get('soporte.EnvioPlantilla');
        $this->soporteService           = $this->container->get('soporte.SoporteService');
        $this->serviceUtil              = $this->container->get('schema.Util');
        $this->serviceCompararImagen    = $this->container->get('schema.CompararImagen');
        $this->host                     = $this->container->getParameter('host');
        $this->pathTelcos               = $this->container->getParameter('path_telcos');
        $this->pathParameters           = $this->container->getParameter('path_parameters');
        
        $this->rutaFisicaIncidenciasElementos   = "public/uploads/incidencias/";
        $this->rutaFisicaActivaciones           = "public/uploads/activaciones/";
        $this->rutaFisicaSoporte                = "public/uploads/soporte/";
        $this->strRutaFisicaTareas              = "public/uploads/tareas/";
        $this->extensionArchivo                 = "jpg";
    }
    
    /**
     * Funcion que sirve para grabar la foto (soporte) y se relacion
     * la foto con un caso.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 23-07-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 21-07-2017 Se guarda las coordenadas que se encuentran en la metadata de la imagen en caso de que exista dicha información
     * 
     * @param array $arrayParametros 
     */
    public function grabarDetalleDocumentoCaso($arrayParametros)
    {
        $idCaso         = $arrayParametros['idCaso'];
        $foto           = $arrayParametros['foto'];
        $nombreArchivo  = $arrayParametros['nombreFoto'];
        $usrCreacion    = $arrayParametros['usrCreacion'];
        $ipCreacion     = $arrayParametros['ipCreacion'];
        $feCreacion     = $arrayParametros['feCreacion'];
        $accion         = $arrayParametros['accion'];
        $intIdEmpresa   = $arrayParametros['idEmpresa'];
        $status         = "";
        $mensaje        = "";
        
        //formato de carpeta
        $strNombreCarpeta = date_format($feCreacion, "Y_m");
        
        //verificar si existe la carpeta
        if(!file_exists($this->rutaFisicaSoporte."".$strNombreCarpeta))
        {
            //crear la carpeta
            mkdir($this->rutaFisicaSoporte."".$strNombreCarpeta);
        }
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            //grabamos la foto
            $this->grabarImagenBase64($foto, $nombreArchivo, $this->rutaFisicaSoporte."".$strNombreCarpeta."/", $this->extensionArchivo);
            
            $strPath              = $this->rutaFisicaSoporte."".$strNombreCarpeta."/";
            $arrayCoordenadas     = $this->soporteService->obtenerCoordenadasImg(array( "strRutaFisicaArchivo"  => $strPath,
                                                                                        "strNombreArchivo"      => $nombreArchivo.'.'
                                                                                                                   .$this->extensionArchivo));

            $caso = $this->emSoporte->getRepository('schemaBundle:InfoCaso')->find($idCaso);

            //obtener tipo documento general
            $tipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                    ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                                                                      'estado'                   => 'Activo'));
            
            //creamos el documento
            $documento = new InfoDocumento();
            $documento->setNombreDocumento($accion.": ".$caso->getNumeroCaso());
            $documento->setTipoDocumentoGeneralId($tipoDocumentoGeneral->getId());
            $documento->setUbicacionLogicaDocumento($nombreArchivo);
            $documento->setUbicacionFisicaDocumento($this->rutaFisicaSoporte."".$strNombreCarpeta."/".
                                                    $nombreArchivo.".".$this->extensionArchivo);
            $documento->setUsrCreacion($usrCreacion);
            $documento->setIpCreacion($ipCreacion);
            $documento->setFeCreacion($feCreacion);
            $documento->setEstado("Activo");
            $documento->setEmpresaCod($intIdEmpresa);
            
            if(isset($arrayCoordenadas["floatLatitud"]) && !empty($arrayCoordenadas["floatLatitud"]))
            {
                $floatLatitud   = $arrayCoordenadas["floatLatitud"];
                $documento->setLatitud($floatLatitud);
            }

            if(isset($arrayCoordenadas["floatLongitud"]) && !empty($arrayCoordenadas["floatLongitud"]))
            {
                $floatLongitud  = $arrayCoordenadas["floatLongitud"];
                $documento->setLongitud($floatLongitud);
            }
            
            $this->emComunicacion->persist($documento);
            $this->emComunicacion->flush();

            //relacionamos el documento con el caso
            $documentoRelacion = new InfoDocumentoRelacion();
            $documentoRelacion->setDocumentoId($documento->getId());
            $documentoRelacion->setCasoId($idCaso);
            $documentoRelacion->setEstado("Activo");
            $documentoRelacion->setUsrCreacion($usrCreacion);
            $documentoRelacion->setFeCreacion($feCreacion);
            $this->emComunicacion->persist($documentoRelacion);
            $this->emComunicacion->flush();
            
            $status     = "OK";
            $mensaje    = "Se grabo la foto exitosamente!";
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $status             = "ERROR";
            $mensaje            = "Mensaje: ".$e->getMessage();
            $respuestaFinal[]   = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }
        
        $this->emComunicacion->getConnection()->close();
        
        //*RESPUESTA-------------------------------------------------------------*/
        $respuestaFinal = array('status' => $status, 'mensaje' => $mensaje);
        return $respuestaFinal;
        //*----------------------------------------------------------------------*/ 
    }
    
    /**
     * Funcion que sirve para grabar la foto (activacion) y se relacion
     * la foto con un servicio.
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 09-07-2015
     * 
     * @author Jonathan Mazon Sanchez<jmazon@telconet.ec>
     * @version 1.1 10-03-2021  Se modifica metodo para el guardado de imagen al NFS
     * 
     * @param array $arrayParametros 
     */
    public function grabarDetalleDocumentoServicio($arrayParametros)
    {        
        $intIdServicio  = $arrayParametros['idServicio'];
        $strFoto        = $arrayParametros['foto'];
        $nombreArchivo  = $arrayParametros['nombreFoto'];
        $strUsrCreacion = $arrayParametros['usrCreacion'];
        $ipCreacion     = $arrayParametros['ipCreacion'];
        $feCreacion     = $arrayParametros['feCreacion'];
        $accion         = $arrayParametros['accion'];
        $strCodEmpresa   = $arrayParametros['idEmpresa'];
        $status         = "";
        $mensaje        = "";
        $strEmpresa     = "";
        //formato de carpeta
        $strNombreCarpeta = date_format($feCreacion, "Y_m");
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {   
            //conversion de cod empresa
            if($strCodEmpresa == 18)
            {
                $strEmpresa = 'MD';
            }
            else if($strCodEmpresa == 10)
            {
                $strEmpresa = 'TN';
            }
            
            if(empty($strEmpresa))
            {
                throw new \Exception('El codigo de la empresa no es el correcto para el guardado');
            }
            if(empty($intIdServicio))
            {
                throw new \Exception('El Id del Servicio se encuentra vacio o no esta definido');
            }
            if(empty($strFoto))
            {
                throw new \Exception('El campo foto se encuentra vacio o no esta definido');
            }
            //validacion de usuario de usuario
            if(empty($strUsrCreacion))
            {
                throw new \Exception('El campo Usuario se encuentra vacio o no esta definido');
            }
            //Validacion del servicio
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            if(!is_object($objServicio))
            {
                throw new \Exception('El Id del Servicio no es correcto');
            }
            $strCodEmpresaServicio = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getEmpresaRolId()->getEmpresaCod()->getId();
            if($strCodEmpresaServicio != $strCodEmpresa)
            {
                throw new \Exception('No Existe el Id Servicio para la Empresa ingresada');
            }
            //Parametros para obtener la aplicacion y el subModulo
            $arrayParamDirectorios = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('GESTION_DIRECTORIOS',
                                                    'TECNICO',
                                                    '', 
                                                    'ACTIVACIONES',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    '');
            
            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'] && is_array($arrayParamDirectorios))
            {
                $arrayPathAdicional   =  array();
                if(isset($arrayParametros['pathAdicional']) && !empty($arrayParametros['pathAdicional']))
                {
                    $arrayPathAdicional   =  $arrayParametros['pathAdicional'];
                }
                
                if(empty($nombreArchivo))
                {
                    throw new \Exception('El campo Nombre de la foto se encuentra vacio o no esta definido');
                }
                $strNFSNomArchivo       = $nombreArchivo.".".$this->extensionArchivo;
                $arrayParamNfs          = array(
                                                'prefijoEmpresa'       => $strEmpresa,
                                                'strApp'               => $arrayParamDirectorios['valor3'],
                                                'strSubModulo'         => $arrayParamDirectorios['valor2'],
                                                'arrayPathAdicional'   => $arrayPathAdicional,
                                                'strBase64'            => $strFoto,
                                                'strNombreArchivo'     => $strNFSNomArchivo,
                                                'strUsrCreacion'       => $strUsrCreacion);

                $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);

                if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                {
                    $strUrlArchivo = $arrayRespNfsPdf['strUrlArchivo'];
                }
                else
                {
                    throw new \Exception($arrayRespNfsPdf['strMensaje']);
                }
            }
            else
            {
                throw new \Exception('No tiene permitido guardar en Nfs');
            }
            
            //obtener tipo documento general
            $tipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                    ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                                                                      'estado'                   => 'Activo'));

            //creamos el documento
            $documento = new InfoDocumento();
            $documento->setNombreDocumento($accion.": ".$objServicio->getPuntoId()->getLogin());
            $documento->setTipoDocumentoGeneralId($tipoDocumentoGeneral->getId());
            $documento->setUbicacionLogicaDocumento($nombreArchivo);
            $documento->setUbicacionFisicaDocumento($strUrlArchivo);
            $documento->setUsrCreacion($strUsrCreacion);
            $documento->setIpCreacion($ipCreacion);
            $documento->setFeCreacion($feCreacion);
            $documento->setEstado("Activo");
            $documento->setEmpresaCod($strCodEmpresa);
            $this->emComunicacion->persist($documento);
            $this->emComunicacion->flush();

            //relacionamos el documento con el servicio
            $documentoRelacion = new InfoDocumentoRelacion();
            $documentoRelacion->setDocumentoId($documento->getId());
            $documentoRelacion->setServicioId($intIdServicio);
            $documentoRelacion->setEstado("Activo");
            $documentoRelacion->setUsrCreacion($strUsrCreacion);
            $documentoRelacion->setFeCreacion($feCreacion);
            $this->emComunicacion->persist($documentoRelacion);
            $this->emComunicacion->flush();
            
            $status     = "OK";
            $mensaje    = "Se grabo la foto exitosamente!";
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $status                 = "ERROR";
            $mensaje                = $e->getMessage();
            $arrayRespuestaFinal    = array('status'=>$status, 'mensaje'=>$mensaje);
            return $arrayRespuestaFinal;
        }
        
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }
        
        $this->emComunicacion->getConnection()->close();
        
        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuestaFinal = array('status' => $status, 'mensaje' => $mensaje);
        return $arrayRespuestaFinal;
        //*----------------------------------------------------------------------*/        
    }
    
    /**
     * Funcion que sirve para grabar la foto (incidencia) y se relaciona la foto
     * con una tarea, elemento
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 18-06-2015
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 21-07-2017 Se guarda las coordenadas que se encuentran en la metadata de la imagen en caso de que exista dicha información
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 21-05-2018 Se adiciona imagenes por eventos para la nueva app-operativo.
     * 
     * @author John Vera R. <javera@telconet.ec>
     * @version 1.3 02-04-2018 Se valida las coordenadas de la imagen
     * 
     * @author Robinson Salgado <rsalgado@telconet.ec>
     * @version 1.4 20-05-2018 Se valida la duplicidad de los archivos
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.5 09-07-2019 Se agrega variable "strEtiquetaFoto" para 
     * el registro de la etiqueta del documento, se elimína lógica de elemento
     * por standar de nombre en la foto, se elimina campo "accion" porque no se utiliza
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.6 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.7 12-11-2020 - Almacenar documento en el servidor nfs remoto.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.8 02-03-2021 - Se elimina setActividadId ya que no tiene utilidad y se esta 
     * guardando información erronea que esta ocacionando inconvenientes.
     *
     * @param array $arrayParametros
     */
    public function grabarDetalleDocumentoElemento($arrayParametros)
    {
        $intIdDetalle       = $arrayParametros['idDetalle'];
        $intIdCaso          = $arrayParametros['idCaso'];
        $strFoto            = $arrayParametros['foto'];
        $strNombreArchivo   = $arrayParametros['nombreFoto'];
        $strUserCreacion    = $arrayParametros['usrCreacion'];
        $intIpCreacion      = $arrayParametros['ipCreacion'];
        $strFeCreacion      = $arrayParametros['feCreacion'];
        $intIdEmpresa       = $arrayParametros['idEmpresa'];
        $strEtiquetaFoto    = $arrayParametros['strEtiquetaFoto'];
        $strStatus          = "";
        $strMensaje         = "";
        
        //formato de carpeta
        $strNombreCarpeta = date_format($strFeCreacion, "Y_m");

        //Carpeta a crear
        if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
        {
            $strRutaFisicaCompleta = $arrayParametros['strRutaFisicaCompleta'];
        }
        else
        {
            $strRutaFisicaCompleta = $this->rutaFisicaIncidenciasElementos."".$strNombreCarpeta;
        }
        
        //verificar si existe la carpeta
        if(!file_exists($strRutaFisicaCompleta))
        {
            //crear la carpeta
            mkdir($strRutaFisicaCompleta, 0777, true);
        }
                       
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            //Validamos la duplicidad de elementos
            $documentoRepetido = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
                                                      ->findOneBy(["nombreDocumento" => $strNombreArchivo]);
             
            if(!is_object($documentoRepetido))
            {
                //grabamos la foto
                $this->grabarImagenBase64($strFoto, $strNombreArchivo, 
                $strRutaFisicaCompleta . "/", $this->extensionArchivo);

                $detalleTareaElemento   = $this->emSoporte->getRepository('schemaBundle:InfoDetalleTareaElemento')
                                               ->findOneBy(array("detalleId" => $intIdDetalle));
                if(is_object($detalleTareaElemento))
                {
                    $elemento               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                               ->find($detalleTareaElemento->getElementoId());
                }

                //obtener tipo documento general
                $tipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                        ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                                                                          'estado'                   => 'Activo'));

                $strPath              = $strRutaFisicaCompleta . "/";

                if($arrayParametros['floatLatitud'] && $arrayParametros['floatLongitud'])
                {
                    $arrayCoordenadas     = $this->soporteService->getCoordenadasImg(array( "GPSLatitude"       => $arrayParametros['floatLatitud'],
                                                                                            "GPSLatitudeRef"    => $arrayParametros['strLatitudRef'],
                                                                                            "GPSLongitude"      => $arrayParametros['floatLongitud'],
                                                                                            "GPSLongitudeRef"   => $arrayParametros['strLongitudRef']));
                }
                else
                {
                    $arrayCoordenadas     = $this->soporteService->obtenerCoordenadasImg(array( "strRutaFisicaArchivo"  => $strPath,
                                                                                                "strNombreArchivo"      => $strNombreArchivo.'.'
                                                                                                           .$this->extensionArchivo));
                }    

                if($arrayParametros['bandNfs'])
                {
                    $arrayPathAdicional     = null;
                    $objFile                = file_get_contents($strRutaFisicaCompleta."/".$strNombreArchivo.".".$this->extensionArchivo);
                    $strKey                 = isset($arrayParametros['idDetalle']) ? $arrayParametros['idDetalle'] : 'SinTarea';
                    $arrayPathAdicional[]   = array('key' => $strKey);
                    $strNFSNomArchivo       = $strNombreArchivo.".".$this->extensionArchivo;
                    $arrayParamNfs          = array(
                                                    'prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                                    'strApp'               => $arrayParametros['strApp'],
                                                    'strSubModulo'         => $arrayParametros['strSubModulo'],
                                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                                    'strBase64'            => base64_encode($objFile),
                                                    'strNombreArchivo'     => $strNFSNomArchivo,
                                                    'strUsrCreacion'       => $strUserCreacion);
                    $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        $strUrlArchivo = $arrayRespNfsPdf['strUrlArchivo'];
                        unlink($strRutaFisicaCompleta."/".$strNombreArchivo.".".$this->extensionArchivo);
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdf()');
                    }
                }
                //creamos el documento
                $documento = new InfoDocumento();

                $documento->setNombreDocumento($strNombreArchivo);
                $documento->setTipoDocumentoGeneralId($tipoDocumentoGeneral->getId());
                $documento->setUbicacionLogicaDocumento($strNombreArchivo);
                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $documento->setUbicacionFisicaDocumento($strUrlArchivo);
                }
                else
                {
                    $documento->setUbicacionFisicaDocumento($strRutaFisicaCompleta . "/" .
                                                            $strNombreArchivo.".".$this->extensionArchivo);
                }
                if(is_object($elemento))
                {
                    $documento->setElementoId($elemento->getId());
                }
                $documento->setUsrCreacion($strUserCreacion);
                $documento->setIpCreacion($intIpCreacion);
                $documento->setFeCreacion($strFeCreacion);
                $documento->setEstado("Activo");
                $documento->setEmpresaCod($intIdEmpresa);
                $documento->setStrEtiquetaDocumento($strEtiquetaFoto);

                if(isset($arrayCoordenadas["floatLatitud"]) && !empty($arrayCoordenadas["floatLatitud"]))
                {
                    $floatLatitud   = $arrayCoordenadas["floatLatitud"];
                    $documento->setLatitud($floatLatitud);
                }

                if(isset($arrayCoordenadas["floatLongitud"]) && !empty($arrayCoordenadas["floatLongitud"]))
                {
                    $floatLongitud  = $arrayCoordenadas["floatLongitud"];
                    $documento->setLongitud($floatLongitud);
                }

                $this->emComunicacion->persist($documento);
                $this->emComunicacion->flush();

                //relacionamos el documento con la tarea y el elemento
                $documentoRelacion = new InfoDocumentoRelacion();
                $documentoRelacion->setDocumentoId($documento->getId());

                if(is_object($elemento))
                {
                    $documentoRelacion->setElementoId($elemento->getId());
                }

                if(isset($intIdDetalle) && !empty($intIdDetalle))
                {
                    $documentoRelacion->setDetalleId($intIdDetalle);
                }

                if(isset($intIdCaso) && !empty($intIdCaso))
                {
                    $documentoRelacion->setCasoId($intIdCaso);
                }
                
                $documentoRelacion->setEstado("Activo");
                $documentoRelacion->setUsrCreacion($strUserCreacion);
                $documentoRelacion->setFeCreacion($strFeCreacion);
                $this->emComunicacion->persist($documentoRelacion);
                $this->emComunicacion->flush();
                $strMensaje = "Se grabo la foto exitosamente!";
            }
            else
            {                
                $strMensaje = "Foto ya existe!";
            }
            
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $strStatus                      = "ERROR";
            $strMensaje                     = "Mensaje: ".$e->getMessage();
            $arrayRespuestaFinal            = array('status'=>$strStatus, 'mensaje'=>$strMensaje);
            return $arrayRespuestaFinal;
        }
        
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }
        
        $this->emComunicacion->getConnection()->close();
        
        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuestaFinal = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
        //*----------------------------------------------------------------------*/
    }
    
    /**
     * Funcion que sirve para grabar la foto que puede ser subida por una tarea.
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.0 24-11-2017
     *
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     */
    public function grabarDetalleDocumentoTarea($arrayParametros)
    {
        $intIdDetalle      = $arrayParametros['idDetalle'];
        $strFoto           = $arrayParametros['foto'];
        $strNombreArchivo  = $arrayParametros['nombreFoto'];
        $strUsrCreacion    = $arrayParametros['usrCreacion'];
        $strIpCreacion     = $arrayParametros['ipCreacion'];
        $strFeCreacion     = $arrayParametros['feCreacion'];
        $strAccion         = $arrayParametros['accion'];
        $intIdEmpresa      = $arrayParametros['idEmpresa'];
        $strStatus         = "";
        $strMensaje        = "";
        $arrayRespuesta    = array();
        $arrayArchivo      = array();


        //formato de carpeta
        $strNombreCarpeta        = date_format($strFeCreacion, "Y_m");

        try
        {
            $arrayArchivo['strPath'] = $this->strRutaFisicaTareas."".$strNombreCarpeta;
            // Si el directorio se crea exitosamente retornara valor 100.
            if("100" === $this->serviceUtil->creaDirectorio($arrayArchivo)->getStrStatus())
            {
                //*DECLARACION DE TRANSACCIONES------------------------------------------*/
                $this->emComunicacion->getConnection()->beginTransaction();
                //*----------------------------------------------------------------------*/
                //grabamos la foto
                $this->grabarImagenBase64($strFoto, $strNombreArchivo, $this->strRutaFisicaTareas."".$strNombreCarpeta."/", $this->extensionArchivo);

                $strPath              = $this->strRutaFisicaTareas."".$strNombreCarpeta."/";
                $arrayCoordenadas     = $this->soporteService->obtenerCoordenadasImg(array( "strRutaFisicaArchivo"  => $strPath,
                                                                                            "strNombreArchivo"      => $strNombreArchivo.'.'
                                                                                                                       .$this->extensionArchivo));


                $objTarea = $this->emSoporte->getRepository('schemaBundle:InfoComunicacion')->findOneByDetalleId($intIdDetalle);

                //se valida si existe entidad tarea
                if(is_object($objTarea))
                {
                    //obtener tipo documento general
                    $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                               ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                                                                                 'estado'                   => 'Activo'));

                    //creamos el documento
                    $objDocumento = new InfoDocumento();
                    $objDocumento->setNombreDocumento($strAccion.": ".$objTarea->getId());
                    $objDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                    $objDocumento->setUbicacionLogicaDocumento($strNombreArchivo);
                    $objDocumento->setUbicacionFisicaDocumento($this->strRutaFisicaTareas."".$strNombreCarpeta."/".
                                                            $strNombreArchivo.".".$this->extensionArchivo);
                    $objDocumento->setUsrCreacion($strUsrCreacion);
                    $objDocumento->setIpCreacion($strIpCreacion);
                    $objDocumento->setFeCreacion($strFeCreacion);
                    $objDocumento->setEstado("Activo");
                    $objDocumento->setEmpresaCod($intIdEmpresa);

                    if(isset($arrayCoordenadas["floatLatitud"]) && !empty($arrayCoordenadas["floatLatitud"]))
                    {
                        $floatLatitud   = $arrayCoordenadas["floatLatitud"];
                        $objDocumento->setLatitud($floatLatitud);
                    }

                    if(isset($arrayCoordenadas["floatLongitud"]) && !empty($arrayCoordenadas["floatLongitud"]))
                    {
                        $floatLongitud  = $arrayCoordenadas["floatLongitud"];
                        $objDocumento->setLongitud($floatLongitud);
                    }

                    $this->emComunicacion->persist($objDocumento);
                    $this->emComunicacion->flush();

                    //relacionamos el documento con el caso
                    $objDocumentoRelacion = new InfoDocumentoRelacion();
                    $objDocumentoRelacion->setDocumentoId($objDocumento->getId());
                    $objDocumentoRelacion->setDetalleId($intIdDetalle);
                    $objDocumentoRelacion->setEstado("Activo");
                    $objDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                    $objDocumentoRelacion->setFeCreacion($strFeCreacion);
                    $this->emComunicacion->persist($objDocumentoRelacion);
                    $this->emComunicacion->flush();

                    $strStatus     = "OK";
                    $strMensaje    = "Se grabo la foto exitosamente!";
                }
                else
                {
                    $strStatus     = "ERROR";
                    $strMensaje    = "No se puede asociar la imagen enviada a la tarea.";
                }
            }
            else
            {
                $strStatus     = "ERROR";
                $strMensaje    = "No se puede crear directorio.";
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $strStatus             = "ERROR";
            $strMensaje            = "Mensaje: ".$e->getMessage();
            $arrayRespuesta[]   = array('status'=>$strStatus, 'mensaje'=>$strMensaje);
            return $arrayRespuesta;
        }

        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }

        $this->emComunicacion->getConnection()->close();

        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuesta;
        //*----------------------------------------------------------------------*/
    }

     /**
     * Funcion que sirve para grabar un documento asociado a una tarea.
     *
     * @author Wilmer Vera González. <wvera@telconet.ec>
     * @version 1.0 19-03-2019
     *
     * Se agrega lógica para almacenar latitud y longitud del documento enviado.
     * Se parametriza los campos de empresa, extensión de archivo y coordenadas.
     *
     * @author Wilmer Vera González. <wvera@telconet.ec>
     * @version 1.1 04-07-2019
     * @since 1.0
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * @since 1.1
     * 
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 12-11-2020 - Almacenar documento en el servidor nfs remoto.
     * @since 1.2
     *
     * @param array $arrayParametros
     * @return array $arrayRespuesta
     */
    public function guardarDetalleDocumentoTarea($arrayParametros)
    {
        $intIdDetalle      = $arrayParametros['idDetalle'];
        $strDocumento      = $arrayParametros['documento'];
        $strNombreArchivo  = $arrayParametros['nombreDocumento'];
        $strUsrCreacion    = $arrayParametros['usrCreacion'];
        $strIpCreacion     = $arrayParametros['ipCreacion'];
        $strFeCreacion     = $arrayParametros['feCreacion'];
        $strAccion         = $arrayParametros['accion'];
        $intIdEmpresa      = $arrayParametros['idEmpresa'];
        $strLatitud        = $arrayParametros['strLatitud'];
        $strLongitud       = $arrayParametros['strLongitud'];
        $strExt            = $arrayParametros['strExt'];
        $strStatus         = "";
        $strMensaje        = "";
        $arrayRespuesta    = array();
        $arrayArchivo      = array();
        $boolGenerar       = false;

        //formato de carpeta
        $strNombreCarpeta        = date_format($strFeCreacion, "Y_m");
        //Carpeta a crear
        if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
        {
            $strRutaFisicaCompleta = $arrayParametros['strRutaFisicaCompleta'];
        }
        else
        {
            $strRutaFisicaCompleta = $this->strRutaFisicaTareas."".$strNombreCarpeta;
        }

        try
        {
            $arrayArchivo['strPath'] = $strRutaFisicaCompleta;
            $boolGenerar = ("100" === $this->serviceUtil->creaDirectorio($arrayArchivo)->getStrStatus()) ? 1 : 0;
            // Si el directorio se crea exitosamente retornara valor 100.
            if($boolGenerar)
            {
                //*DECLARACION DE TRANSACCIONES------------------------------------------*/
                $this->emComunicacion->getConnection()->beginTransaction();
                //*----------------------------------------------------------------------*/
                //grabamos la foto
                $this->grabarImagenBase64($strDocumento, $strNombreArchivo, $strRutaFisicaCompleta."/", $strExt);

                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $arrayPathAdicional     = null;
                    $arrayParamNfs          = null;
                    $objFile                = file_get_contents($strRutaFisicaCompleta."/".$strNombreArchivo.".".$strExt);
                    $strKey                 = isset($intIdDetalle) ? $intIdDetalle : 'SinTarea';
                    $arrayPathAdicional[]   = array('key' => $strKey);

                    $arrayParamNfs          = array(
                                                    'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                                    'strApp'               => $arrayParametros['strApp'],
                                                    'strSubModulo'         => $arrayParametros['strSubModulo'],
                                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                                    'strBase64'            => base64_encode($objFile),
                                                    'strNombreArchivo'     => $strNombreArchivo.".".$strExt,
                                                    'strUsrCreacion'       => $strUsrCreacion);

                    $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        $strUrl = $arrayRespNfsPdf['strUrlArchivo'];
                        unlink($strRutaFisicaCompleta."/".$strNombreArchivo.".".$strExt);
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> guardarDetalleDocumentoTarea()');
                    }
                }

                $objTarea = $this->emSoporte->getRepository('schemaBundle:InfoComunicacion')->findOneByDetalleId($intIdDetalle);

                //se valida si existe entidad tarea
                if(is_object($objTarea))
                {
                    $strCodigoTipoDoc = $strExt;
                    if(strtoupper($strExt) === "JPG" || strtoupper($strExt) === "JPEG" || strtoupper($strExt) === "PNG")
                    {
                        $strCodigoTipoDoc = 'IMG';        
                    }
                    //obtener tipo documento general
                    $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                               ->findOneBy(array('codigoTipoDocumento'      => strtoupper($strCodigoTipoDoc) ,
                                                                                 'estado'                   => 'Activo'));
                    //creamos el documento
                    $objDocumento = new InfoDocumento();
                    $objDocumento->setNombreDocumento($strAccion.": ".$objTarea->getId());
                    $objDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                    $objDocumento->setUbicacionLogicaDocumento($strNombreArchivo.".".$strExt);
                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        $objDocumento->setUbicacionFisicaDocumento($strUrl);
                    }
                    else
                    {
                        $objDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta."/".
                                                            $strNombreArchivo.".".$strExt);
                    }
                    $objDocumento->setUsrCreacion($strUsrCreacion);
                    $objDocumento->setIpCreacion($strIpCreacion);
                    $objDocumento->setFeCreacion($strFeCreacion);
                    $objDocumento->setEstado("Activo");
                    $objDocumento->setEmpresaCod($intIdEmpresa);

                    if(isset($strLatitud) && !empty($strLatitud))
                    {
                        $objDocumento->setLatitud($strLatitud);
                    }

                    if(isset($strLongitud) && !empty($strLongitud))
                    {
                        $objDocumento->setLongitud($strLongitud);
                    }
                    $this->emComunicacion->persist($objDocumento);
                    $this->emComunicacion->flush();
                    
                    //relacionamos el documento con el caso
                    $objDocumentoRelacion = new InfoDocumentoRelacion();
                    $objDocumentoRelacion->setDocumentoId($objDocumento->getId());
                    $objDocumentoRelacion->setDetalleId($intIdDetalle);
                    $objDocumentoRelacion->setEstado("Activo");
                    $objDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                    $objDocumentoRelacion->setFeCreacion($strFeCreacion);
                    $this->emComunicacion->persist($objDocumentoRelacion);
                    $this->emComunicacion->flush();
                    $strStatus     = "OK";
                    $strMensaje    = "Se grabó el documento exitosamente!";
                }
                else
                {
                    $strStatus     = "ERROR";
                    $strMensaje    = "No se puede asociar el documento enviada a la tarea.";
                }
            }
            else
            {
                $strStatus     = "ERROR";
                $strMensaje    = "No se puede crear directorio.";
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $strStatus          = "ERROR";
            $strMensaje         = "Mensaje: ".$e->getMessage();
            $arrayRespuesta[]   = array('status'=>$strStatus, 'mensaje'=>$strMensaje);
            return $arrayRespuesta;
        }

        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }

        $this->emComunicacion->getConnection()->close();

        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuesta = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuesta;
        //*----------------------------------------------------------------------*/
    }

    /**
     * Funcion que sirve para grabar la firma en coordenadas a una imagen en el servidor
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-06-2015
     * @param $data
     * @param $nombreArchivo
     * @param $rutaFisicaArchivo
     * @param $extensionArchivo
     */
    public function grabarImagenCoordenadas($data, $nombreArchivo, $rutaFisicaArchivo, $extensionArchivo)
    {    
        $imagenFirma = $this->convertirService->sigJsonToImage($data);
        imagepng($imagenFirma, $rutaFisicaArchivo . '' . $nombreArchivo . '.'.$extensionArchivo);
    }
    
    /**
     * Escribe un archivo temporal con la data que debe estar encodada en base64,
     * devuelve la ruta del archivo.
     * @param string $data
     * @param $nombreArchivo
     * @param $rutaFisicaArchivo
     * @param $extensionArchivo
     * @return string ruta del archivo creado
     */
    private function writeTempFile($data, $nombreArchivo, $rutaFisicaArchivo, $extensionArchivo) 
    {
        $path = $rutaFisicaArchivo.''.$nombreArchivo.'.'.$extensionArchivo;
        $ifp = fopen($path, "wb");
        if (strpos($data, ',') !== false) 
        {
            $data = explode(',', $data)[1];
        }
        fwrite($ifp, base64_decode($data));
        fclose($ifp);
        return $path;
    }

    /**
     * Devuelve una referencia a un archivo existente en el servidor segun la ruta dada
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    private function getFile($path) 
    {
        $file = new File($path);
        return $file;
    }

    /**
     * Escribe un archivo con la data base64 y devuelve una referencia al mismo
     * @param string $data (encodado en base64)
     * @param $nombreArchivo
     * @param $rutaFisicaArchivo
     * @param $extensionArchivo
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function grabarImagenBase64($data, $nombreArchivo, $rutaFisicaArchivo, $extensionArchivo) 
    {
        if (empty($data)) 
        {
            return null;
        }
        return $this->getFile($this->writeTempFile($data,$nombreArchivo,$rutaFisicaArchivo,$extensionArchivo));
    }

    /** 
     * Se agrega variable "intIdCuarillaHistorial" para 
     * que sea relacionados los documentos con la tabla "cuadrillaHistorial".
     *
     * @author Wilmer Vera González <wvera@telconet.ec>
     * @version 1.0 23-08-2019 
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 12-11-2020 - Almacenar documento en el servidor nfs remoto.
     *
     * @param array $arrayParametros
     */
    public function grabarFotoFiscalizacion($arrayParametros)
    {
        $strFoto                    = $arrayParametros['foto'];
        $strNombreArchivo           = $arrayParametros['nombreFoto'];
        $strUserCreacion            = $arrayParametros['usrCreacion'];
        $intIpCreacion              = $arrayParametros['ipCreacion'];
        $strFeCreacion              = $arrayParametros['feCreacion'];
        $intIdEmpresa               = $arrayParametros['idEmpresa'];
        $intIdCuarillaHistorial     = $arrayParametros['intIdCuarillaHistorial'];
        $strStatus                  = "";
        $strMensaje                 = "";
        
        //formato de carpeta
        $strNombreCarpeta = date_format($strFeCreacion, "Y_m");

        //Carpeta a crear
        if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
        {
            $strRutaFisicaCompleta = $arrayParametros['strRutaFisicaCompleta'];
        }
        else
        {
            $strRutaFisicaCompleta = $this->rutaFisicaIncidenciasElementos."".$strNombreCarpeta;
        }
        
        //verificar si existe la carpeta
        if(!file_exists($strRutaFisicaCompleta))
        {
            //crear la carpeta
            mkdir($strRutaFisicaCompleta, 0777, true);
        }
                       
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            {
                //grabamos la foto
                $this->grabarImagenBase64($strFoto, $strNombreArchivo, 
                $strRutaFisicaCompleta . "/", $this->extensionArchivo);
                
                if($arrayParametros['bandNfs'])
                {
                    $arrayPathAdicional     = null;
                    $objFile                = file_get_contents($strRutaFisicaCompleta."/".$strNombreArchivo.".".$this->extensionArchivo);
                    $strKey                 = isset($arrayParametros['idDetalle']) ? $arrayParametros['idDetalle'] : 'SinTarea';
                    $arrayPathAdicional[]   = array('key' => $strKey);
                    $strNFSNomArchivo       = $strNombreArchivo.".".$this->extensionArchivo;
                    $arrayParamNfs          = array(
                                                    'prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                                    'strApp'               => $arrayParametros['strApp'],
                                                    'strSubModulo'         => $arrayParametros['strSubModulo'],
                                                    'arrayPathAdicional'   => $arrayPathAdicional,
                                                    'strBase64'            => base64_encode($objFile),
                                                    'strNombreArchivo'     => $strNFSNomArchivo,
                                                    'strUsrCreacion'       => $strUserCreacion);
                    $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                    if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                    {
                        $strUrlArchivo = $arrayRespNfsPdf['strUrlArchivo'];
                        unlink($strRutaFisicaCompleta."/".$strNombreArchivo.".".$this->extensionArchivo);
                    }
                    else
                    {
                        throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdf()');
                    }
                }

                //obtener tipo documento general
                $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                                        ->findOneBy(array('descripcionTipoDocumento' => 'IMAGENES',
                                                                          'estado'                   => 'Activo'));

                $strPath = $strRutaFisicaCompleta . "/";

                if($arrayParametros['floatLatitud'] && $arrayParametros['floatLongitud'])
                {
                    $arrayCoordenadas = $this->soporteService->getCoordenadasImg(
                                                                array( "GPSLatitude"     => $arrayParametros['floatLatitud'],
                                                                       "GPSLatitudeRef"  => $arrayParametros['strLatitudRef'],
                                                                       "GPSLongitude"    => $arrayParametros['floatLongitud'],
                                                                       "GPSLongitudeRef" => $arrayParametros['strLongitudRef']));
                }
                else
                {
                    $arrayCoordenadas     = $this->soporteService->obtenerCoordenadasImg(array( "strRutaFisicaArchivo"  => $strPath,
                                                                                                "strNombreArchivo"      => $strNombreArchivo.'.'
                                                                                                           .$this->extensionArchivo));
                }    

                $objInfoDocumento = new InfoDocumento();
                $objInfoDocumento->setNombreDocumento($strNombreArchivo);
                $objInfoDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                $objInfoDocumento->setUbicacionLogicaDocumento($strNombreArchivo);
                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $objInfoDocumento->setUbicacionFisicaDocumento($strUrlArchivo);
                }
                else
                {
                    $objInfoDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta . "/" .
                                                            $strNombreArchivo.".".$this->extensionArchivo);
                }
                $objInfoDocumento->setElementoId(0);
                $objInfoDocumento->setUsrCreacion($strUserCreacion);
                $objInfoDocumento->setIpCreacion($intIpCreacion);
                $objInfoDocumento->setFeCreacion($strFeCreacion);
                $objInfoDocumento->setEstado("Activo");
                $objInfoDocumento->setEmpresaCod($intIdEmpresa);
                $objInfoDocumento->setIntCuadrillaHistorialId($intIdCuarillaHistorial);

                if(isset($arrayCoordenadas["floatLatitud"]) && !empty($arrayCoordenadas["floatLatitud"]))
                {
                    $floatLatitud   = $arrayCoordenadas["floatLatitud"];
                    $objInfoDocumento->setLatitud($floatLatitud);
                }

                if(isset($arrayCoordenadas["floatLongitud"]) && !empty($arrayCoordenadas["floatLongitud"]))
                {
                    $floatLongitud  = $arrayCoordenadas["floatLongitud"];
                    $objInfoDocumento->setLongitud($floatLongitud);
                }
                $this->emComunicacion->persist($objInfoDocumento);
                $this->emComunicacion->flush();
                $strMensaje = "Se grabó la foto exitosamente!";
            }
            
            $strStatus = "OK";
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $strStatus               = "ERROR";
            $strMensaje              = "Mensaje: ".$e->getMessage();
            $arrayRespuestaFinal[]   = array('status'=>$strStatus, 'mensaje'=>$strMensaje);
            return $arrayRespuestaFinal;
        }
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }
        $this->emComunicacion->getConnection()->close();

        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuestaFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuestaFinal;
        //*----------------------------------------------------------------------*/
    }

    /**
     * Función que guarda, compara y valida una imagen con su imagen ideal.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.0 31-10-2019
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.1 23-12-2019
     * @since 1.0
     * Se corrige nombre de parámetro en array, "idDepartameto" por "idDepartamento"
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.2 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * @since 1.1
     * 
     * @param array $arrayParametros
     * 
     * @return array $arrayRespuestaFinal
     * 
     * 
     */
    public function comparaValidaImagen($arrayParametros)
    {
        $intIdDetalle                   = $arrayParametros['idDetalle'];
        $strFoto                        = $arrayParametros['foto'];
        $strNombreFoto                  = $arrayParametros['nombreFoto'];
        $strUsrCreacion                 = $arrayParametros['usrCreacion'];
        $strUsrEvaluacion               = "Telcos+";
        $strIpCreacion                  = $arrayParametros['ipCreacion'];
        $strFeCreacion                  = $arrayParametros['feCreacion'];
        $strFeEvaluacion                = $arrayParametros['feCreacion'];
        $intIdEmpresa                   = $arrayParametros['idEmpresa'];
        $floatLatitud                   = $arrayParametros['floatLatitud'];
        $floatLongitud                  = $arrayParametros['floatLongitud'];
        $strEtiquetaFoto                = $arrayParametros['strEtiquetaFoto'];
        $strEstadoEvaluacion            = '';
        $strEvaluacionTrabajo           = '';
        $strExt                         = $this->extensionArchivo;
        $strStatus                      = "";
        $strMensaje                     = "";
        $strImagen1                     = "";
        $strImagen2                     = "";
        $strFotoIdeal                   = "";
        $arrayRespuesta                 = array();
        $floatSimilitud                 = 0;
        $strBanderaAuditoriaCoordinador = "";
        $strValorEvaluacionOk           = "";

        //formato de carpeta
        $strNombreCarpeta = date_format($strFeCreacion, "Y_m");

        //Carpeta a crear
        if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
        {
            $strRutaFisicaCompleta = $arrayParametros['strRutaFisicaCompleta'];
        }
        else
        {
            $strRutaFisicaCompleta = $this->strRutaFisicaTareas."".$strNombreCarpeta;
        }

        //verificar si existe la carpeta
        if(!file_exists($strRutaFisicaCompleta))
        {
            //crear la carpeta
            mkdir($strRutaFisicaCompleta, 0777, true);
        }
                       
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        try
        {         
            //Validamos la duplicidad de fotos
            $arrayResultadoFotos = $this->emComunicacion->getRepository('schemaBundle:InfoDocumento')
            ->getResultadoFotosAntesDespues(
                [
                    "nombreDocumento" => $strNombreFoto,
                    "usuario"         => $strUsrCreacion
                ]
            );

            if($arrayResultadoFotos['status'] == 'OK')
            {
                $intIntentos = ($arrayResultadoFotos['total']+1);
            }
            else
            {
                $this->serviceUtil->insertError(
                    'Telcos+', 
                    'comparaValidaImagen', 
                    $arrayResultadoFotos['resultado'], 
                    $strUsrCreacion, 
                    $strIpCreacion
                );
            }

            $strNombreFoto = $strNombreFoto.'_'.date_format($strFeCreacion, "dmY")
                                ."_".$intIntentos;

            //grabamos la foto
            $this->grabarImagenBase64($strFoto, 
            $strNombreFoto, 
            $strRutaFisicaCompleta."/", 
            $strExt);

            if($arrayParametros['bandNfs'])
            {
                $arrayPathAdicional     = null;
                $objFile                = file_get_contents($strRutaFisicaCompleta."/".$strNombreFoto.".".$strExt);
                $strKey                 = isset($intIdDetalle) ? $intIdDetalle : 'SinTarea';
                $arrayPathAdicional[]   = array('key' => $strKey);
                $strNombreArchivo       = $strNombreFoto.".".$strExt;
                $arrayParamNfs          = array(
                                                'prefijoEmpresa'       => $arrayParametros['strPrefijoEmpresa'],
                                                'strApp'               => $arrayParametros['strApp'],
                                                'strSubModulo'         => $arrayParametros['strSubModulo'],
                                                'arrayPathAdicional'   => $arrayPathAdicional,
                                                'strBase64'            => base64_encode($objFile),
                                                'strNombreArchivo'     => $strNombreArchivo,
                                                'strUsrCreacion'       => $strUsrCreacion);
                $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
                
                if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
                {
                    $strUrlArchivo = $arrayRespNfsPdf['strUrlArchivo'];
                }
                else
                {
                    throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> generarPdf()');
                }
            }

            if($intIdDetalle > 0)
            {
                $objTarea = $this->emSoporte->getRepository('schemaBundle:InfoComunicacion')->findOneByDetalleId($intIdDetalle);
            }

            //se valida si existe entidad tarea
            if(is_object($objTarea))
            {
                $strCodigoTipoDoc = $strExt;
                if(strtoupper($strExt) === "JPG" || strtoupper($strExt) === "JPEG" || strtoupper($strExt) === "PNG")
                {
                    $strCodigoTipoDoc = 'IMG';        
                }
                //obtener tipo documento general
                $objTipoDocumentoGeneral = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')
                                            ->findOneBy(array('codigoTipoDocumento'      => strtoupper($strCodigoTipoDoc) ,
                                                                'estado'                   => 'Activo'));
                //creamos el documento
                $objDocumento = new InfoDocumento();
                $objDocumento->setNombreDocumento($strNombreFoto);
                if(is_object($objTipoDocumentoGeneral) && $objTipoDocumentoGeneral->getId() > 0)
                {
                    $objDocumento->setTipoDocumentoGeneralId($objTipoDocumentoGeneral->getId());
                }
                $objDocumento->setUbicacionLogicaDocumento($strNombreFoto.".".$strExt);
                if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                {
                    $objDocumento->setUbicacionFisicaDocumento($strUrlArchivo);
                }
                else
                {
                    $objDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta."/".
                                            $strNombreFoto.".".$strExt);
                }
                $objDocumento->setUsrCreacion($strUsrCreacion);
                $objDocumento->setIpCreacion($strIpCreacion);
                $objDocumento->setFeCreacion($strFeCreacion);
                $objDocumento->setEstado("Activo");
                $objDocumento->setEmpresaCod($intIdEmpresa);
                $objDocumento->setStrEtiquetaDocumento($strEtiquetaFoto);
                
                if(isset($floatLatitud) && !empty($floatLatitud))
                {
                    $objDocumento->setLatitud($floatLatitud);
                }
                if(isset($floatLongitud) && !empty($floatLongitud))
                {
                    $objDocumento->setLongitud($floatLongitud);
                }
                $this->emComunicacion->persist($objDocumento);
                $this->emComunicacion->flush();

                $arrayCronologiaFotos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                ->getOne('CRONOLOGIA_FOTOS_OBLIGATORIAS', 
                '', 
                '', 
                '', 
                '', 
                '', 
                '', 
                ''
                );
                if (is_array($arrayCronologiaFotos))
                {
                    $strCronologiaFotoDespues = !empty($arrayCronologiaFotos['valor2']) ? $arrayCronologiaFotos['valor2'] : "DESPUES";
                }

                if( $arrayParametros['cronologia'] == $strCronologiaFotoDespues)
                {
                    $arrayPorcentajeFotos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('PORCENTAJE_MIN_FOTOS_OBLIGATORIAS', 
                    '', 
                    '', 
                    '', 
                    '', 
                    '', 
                    '', 
                    ''
                    );
                    if (is_array($arrayPorcentajeFotos))
                    {
                        $floatPorcentajeFotos = !empty($arrayPorcentajeFotos['valor1']) ? $arrayPorcentajeFotos['valor1'] : 100;
                    }

                    $arrayIntentosMaximos = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('INTENTOS_MAX_FOTOS_OBLIGATORIAS', 
                    '', 
                    '', 
                    '', 
                    '', 
                    '', 
                    '', 
                    ''
                    );
                    if (is_array($arrayIntentosMaximos))
                    {
                        $intIntentosMaximos = !empty($arrayIntentosMaximos['valor1']) ? $arrayIntentosMaximos['valor1'] : 2;
                    }

                    $arrayBanderaAuditoriaCoordinador = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('BANDERA_AUDITORIA_COORDINADOR', 
                    '', 
                    '', 
                    '', 
                    '', 
                    '', 
                    '', 
                    ''
                    );
                    if (is_array($arrayBanderaAuditoriaCoordinador))
                    {
                        $strBanderaAuditoriaCoordinador = !empty($arrayBanderaAuditoriaCoordinador['valor1']) 
                                                            ? $arrayBanderaAuditoriaCoordinador['valor1'] : 'S';
                    }

                    $arrayValorEvaluacionOk = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne('VALORES_EVALUACION_TRABAJO', 
                    '', 
                    '', 
                    '', 
                    'OK', 
                    '', 
                    '', 
                    ''
                    );
                    if (is_array($arrayValorEvaluacionOk))
                    {
                        $strValorEvaluacionOk = !empty($arrayValorEvaluacionOk['valor2']) ? $arrayValorEvaluacionOk['valor2'] : "OK";
                    }

                    if($intIntentos <= $intIntentosMaximos)
                    {
                        $arrayImagenesIdeales = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('RUTA_BASE_IMAGENES_IDEALES', 
                        '', 
                        '', 
                        '', 
                        '', 
                        '', 
                        '', 
                        ''
                        );
                        if (is_array($arrayImagenesIdeales))
                        {
                            $strRutaImagenesIdeales = !empty($arrayImagenesIdeales['valor1']) ? $arrayImagenesIdeales['valor1'] : "";
                        }
        
                        $arrayFotoIdeal = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                        ->getOne('FOTOS_OBLIGATORIAS', 
                        '', 
                        '', 
                        '', 
                        '', 
                        $strEtiquetaFoto, 
                        '', 
                        $arrayParametros['idTarea'],
                        $arrayParametros['idDepartamento'],
                        $arrayParametros['idEmpresa']
                        );
                        if (is_array($arrayFotoIdeal))
                        {
                            $strFotoIdeal = !empty($arrayFotoIdeal['valor3']) ? $arrayFotoIdeal['valor3'] : "";
                        }
        
                        $strImagen1             = $strRutaImagenesIdeales.$strFotoIdeal;
                        $strImagen2             = $strRutaFisicaCompleta."/".$strNombreFoto.".".$strExt;

                        $arrayParametrosImagen = array(
                            'strImagen1'    => $strImagen1, 
                            'strImagen2'    => $strImagen2
                        );

                        $floatSimilitud         = $this->serviceCompararImagen->compararImagenes($arrayParametrosImagen);
                        
                        if(isset($strBanderaAuditoriaCoordinador) 
                            && !empty($strBanderaAuditoriaCoordinador) 
                            && $strBanderaAuditoriaCoordinador == 'N')
                        {
                            
                            if($floatSimilitud >= floatval($floatPorcentajeFotos))
                            {
                                $strEstadoEvaluacion    = 'Auditada';
                                $strEvaluacionTrabajo = $strValorEvaluacionOk;
                            }
                            else
                            {
                                $strEstadoEvaluacion    = 'Pendiente';
                                $strUsrEvaluacion       = '';
                            }
                        }
                    }

                    if(isset($strBanderaAuditoriaCoordinador) && !empty($strBanderaAuditoriaCoordinador) && $strBanderaAuditoriaCoordinador == 'S')
                    {
                        $strEstadoEvaluacion    = 'Pendiente';
                        $strUsrEvaluacion       = '';
                    }

                    $strStatus  = "OK";
                    $strMensaje = "Se guardó la foto exitosamente!";
                    $arrayData  = array(
                                            'estadoEvaluacion'  => $strEstadoEvaluacion,
                                            'evaluacionTrabajo' => $strEvaluacionTrabajo, 
                                            'intentos'          => $intIntentos
                                        );
                }
                else
                {
                    $strStatus          = "OK";
                    $strMensaje         = "Se guardó la foto exitosamente!";
                    $strUsrEvaluacion   = '';
                    $strFeEvaluacion    = null;
                }

                $objDocumentoRelacion = new InfoDocumentoRelacion();
                $objDocumentoRelacion->setDocumentoId($objDocumento->getId());
                $objDocumentoRelacion->setDetalleId($intIdDetalle);
                $objDocumentoRelacion->setEstado("Activo");
                $objDocumentoRelacion->setUsrCreacion($strUsrCreacion);
                $objDocumentoRelacion->setFeCreacion($strFeCreacion);
                $objDocumentoRelacion->setEstadoEvaluacion($strEstadoEvaluacion);
                $objDocumentoRelacion->setEvaluacionTrabajo($strEvaluacionTrabajo);
                $objDocumentoRelacion->setFeInicioEvaluacion($strFeEvaluacion);
                $objDocumentoRelacion->setUsrEvaluacion($strUsrEvaluacion);
                $objDocumentoRelacion->setPorcentajeEvaluacionBase(floatval($floatPorcentajeFotos));
                $objDocumentoRelacion->setPorcentajeEvaluado($floatSimilitud);
                $this->emComunicacion->persist($objDocumentoRelacion);
                $this->emComunicacion->flush();
            }
            else
            {
                $strStatus     = "ERROR";
                $strMensaje    = "No se puede asociar la imagen enviada a la tarea.";

                $this->serviceUtil->insertError(
                                                    'Telcos+', 
                                                    'comparaValidaImagen', 
                                                    $strMensaje, 
                                                    $strUsrCreacion, 
                                                    $strIpCreacion
                                                );
            }

            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                unlink($strRutaFisicaCompleta."/".$strNombreFoto.".".$strExt);
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }

            $strStatus          = "ERROR";
            $strMensaje         = "Mensaje: ".$e->getMessage();
            $arrayRespuesta[]   = array('status'=>$strStatus, 'mensaje'=>$strMensaje);

            $this->serviceUtil->insertError(
                'Telcos+', 
                'comparaValidaImagen', 
                $strMensaje, 
                $strUsrCreacion, 
                $strIpCreacion
            );

            return $arrayRespuesta;
        }

        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }

        $this->emComunicacion->getConnection()->close();

        //*RESPUESTA-------------------------------------------------------------*/
        $arrayRespuesta = array(
                                    'status'    => $strStatus, 
                                    'mensaje'   => $strMensaje,
                                    'data'      => $arrayData
                                );
        return $arrayRespuesta;
        //*----------------------------------------------------------------------*/
    }
}
