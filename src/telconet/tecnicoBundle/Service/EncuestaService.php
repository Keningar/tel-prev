<?php

namespace telconet\tecnicoBundle\Service;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoEncuesta;
use telconet\schemaBundle\Entity\InfoEncuestaPregunta;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;

/**
 * Clase que sirve para el manejo de las encuestas de servicio y soporte
 * 
 * @author Francisco Adum <fadum@telconet.ec>
 * @version 1.0 30-07-2015
 */
class EncuestaService
{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emNaf;
    private $servicioGeneral;
    private $procesarImagenesService;
    private $envioPlantilla;
    private $soporteService;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;
    
    public function __construct(Container $container) 
    {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->envioPlantilla       = $this->container->get('soporte.EnvioPlantilla');
        $this->soporteService       = $this->container->get('soporte.SoporteService');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
    }    
    
    public function setDependencies(InfoServicioTecnicoService $servicioGeneral, ProcesarImagenesService $procesarImagenesService) 
    {
        $this->servicioGeneral          = $servicioGeneral;
        $this->procesarImagenesService  = $procesarImagenesService;
        $this->serviceUtil              = $this->container->get('schema.Util');
    }
    
    /**
     * Funcion que sirve parar grabar la encuesta del servicio
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 12-06-2015
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 02-12-2015 - Se realizan ajustes para relacionar el detalleId de la ultima solicitud de planificacion o migracion
     *                           que tenga realacionado el servicio
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.2 05-05-2017 - Se obtiene por parametro el codigo de la plantilla para consultar la plantilla de la encuesta por instalacion
     * 
     * @author Modificado: Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.3 25-10-2018 - Se realizan validaciones tanto en Objetos como en arrays ya antes declarados o llamados.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.4 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.5 30-09-2021 
     * - Se agregan paámetros para la generación de actas con nuevos ISO.
     * - Se agrega validaciones de estos nuevos parámetros.
     *        ['strIntegrantesCuadrilla'] -> Integrante de cuadrilla.
     *        ['strNombreCuadrilla'] -> nombre de la cuadrilla.
     *        ['strJefeCuadrilla'] -> nombre de jefe de cuadrilla.
     *        ['strTelfPersonaSitio'] -> telf. ingresado en el móvil por la persona en sitio.
     *        ['strFechaContrato'] -> fecha del contrato. 
     * @param array $arrayParametros 
     * @param array $respuestaFinal
     */
    public function grabarEncuesta($arrayParametros)
    {
        $idEmpresa                  =   $arrayParametros['idEmpresa'];
        $prefijoEmpresa             =   $arrayParametros['prefijoEmpresa'];
        $idServicio                 =   $arrayParametros['idServicio'];
        $idDetalle                  =   $arrayParametros['idDetalle'];
        $firmaCoordenadas           =   $arrayParametros['firmaCoordenadas'];
        $firmaBase64                =   $arrayParametros['firmaBase64'];
        $arrayPreguntaRespuesta     =   $arrayParametros['preguntaRespuesta'];
        $strCodigoPlantilla         =   $arrayParametros['strCodigoPlantilla'];
        $usrCreacion                =   $arrayParametros['usrCreacion'];
        $ipCreacion                 =   $arrayParametros['ipCreacion'];
        $feCreacion                 =   $arrayParametros['feCreacion'];
        $serverRoot                 =   $arrayParametros['serverRoot'];
        $pathSource                 =   $arrayParametros['pathSource'];
        $strIntegrantesCuadrilla    =   $arrayParametros['strIntegrantesCuadrilla'];
        $strNombreCuadrilla         =   $arrayParametros['strNombreCuadrilla'];
        $strJefeCuadrilla           =   $arrayParametros['strJefeCuadrilla'];
        $strTelfPersonaSitio        =   $arrayParametros['strTelfPersonaSitio'];
        $strFechaContrato           =   $arrayParametros['strFechaContrato'];
        $hora                       =   date('Y-m-d-His');
        $status                     =   "ERROR";
        $strRutaFisicaCompleta      =   '';
        $arrayFirma                 =   array();
        $arrayDocumento             =   array();
        $boolGenerar                =   false;

        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                  ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            //datos para grabar la firma
            if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
            {
                $strRutaFisicaCompleta  = $arrayParametros['strRutaFisicaCompleta'];
                $strRutaFisicaArchivo   = substr($strRutaFisicaCompleta, 0, strrpos($strRutaFisicaCompleta, '/')+1) . 'firmas/';
            }
            else
            {
                $strRutaFisicaCompleta  = 'public/uploads/documentos';
                $strRutaFisicaArchivo   = 'public/uploads/firmas/';
            }

            $extensionArchivo   = 'png';
            $nombreArchivo      = $idServicio;

            $arrayFirma['strPath']      = $strRutaFisicaArchivo;
            $arrayDocumento['strPath']  = $strRutaFisicaCompleta;
            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                $boolGenerar = true;
            }
            else
            {
                $boolGenerar = ("100" === $this->serviceUtil->creaDirectorio($arrayFirma)->getStrStatus() &&
                                "100" === $this->serviceUtil->creaDirectorio($arrayDocumento)->getStrStatus()) ? 1 : 0;
            }
            // Si el directorio se crea exitosamente retornara valor 100.
            if($boolGenerar)
            {
            
                //grabar firma en imagen
                if($firmaCoordenadas!="")
                {
                    $this->procesarImagenesService->grabarImagenCoordenadas($firmaCoordenadas, 
                                                                            $nombreArchivo, 
                                                                            $strRutaFisicaArchivo, 
                                                                            $extensionArchivo);
                }
                if($firmaBase64!="")
                {
                    $this->procesarImagenesService->grabarImagenBase64($firmaBase64, $nombreArchivo, $strRutaFisicaArchivo, $extensionArchivo);
                }
                
                //InfoEncuesta
                $infoEncuesta = new InfoEncuesta();
                $infoEncuesta->setEstado('Activo');
                $codigo = $this->emComunicacion->getRepository('schemaBundle:InfoEncuesta')->getCodigoEncuesta();
                $infoEncuesta->setCodigo($codigo);
                $infoEncuesta->setNombreEncuesta('Encuesta Codigo : ' . $codigo);
                $infoEncuesta->setFeCreacion($feCreacion);
                $infoEncuesta->setUsrCreacion($usrCreacion);
                $infoEncuesta->setIpCreacion($ipCreacion);
                $this->emComunicacion->persist($infoEncuesta);
                $this->emComunicacion->flush();

                $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $punto    = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                if(!is_object($infoEncuesta) || $infoEncuesta->getId()==null || $infoEncuesta->getId()==0){
                    throw new \Exception("Problema al procesar el archivo. 
                    Objeto encuesta no se ha guardado correctamente, intente nuevamente");
                }

                //Preguntas con sus Respuestas
                $arrayPreguntasRespuestas = explode("|", $arrayPreguntaRespuesta);

                foreach($arrayPreguntasRespuestas as $pregResp)
                {
                    $respuestas = explode("-", $pregResp);

                    if(count($respuestas) > 1)
                    {
                        $infoEncuestaPregunta = new InfoEncuestaPregunta();
                        $infoEncuestaPregunta->setPreguntaId($respuestas[0]);
                        $infoEncuestaPregunta->setEncuestaId($infoEncuesta->getId());
                        $infoEncuestaPregunta->setValor($respuestas[1]);
                        $infoEncuestaPregunta->setEstado('Activo');
                        $infoEncuestaPregunta->setFeCreacion($feCreacion);
                        $infoEncuestaPregunta->setUsrCreacion($usrCreacion);
                        $infoEncuestaPregunta->setIpCreacion($ipCreacion);
                        $this->emComunicacion->persist($infoEncuestaPregunta);
                        $this->emComunicacion->flush();
                    }
                }

                $tipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');
                if(!is_object($tipoDocumento))
                {
                    throw new \Exception("Problema al procesar el archivo. 
                    Objeto tipo documento PDF no se ha encontrado, intente nuevamente");
                }
                            
                //Se genera documento fisico del PDF relacionado a la plantilla
                $plantilla            = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->findOneByCodigo($strCodigoPlantilla);
                $html                 = $plantilla->getPlantilla();

                //obtener las preguntas con sus respuestas para generar el pdf
                $infoEncuestaPregunta = $this->emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                                            ->findByEncuestaId($infoEncuesta->getId());
                foreach($infoEncuestaPregunta as $encuestaPregunta)
                {
                    $pregunta = $this->emComunicacion->getRepository('schemaBundle:AdmiPregunta')->find($encuestaPregunta->getPreguntaId());

                    $arrayPreguntaEncuestaPdf[] = array(
                                                        'idPregunta'    => $pregunta->getId(),
                                                        'pregunta'      => $pregunta->getPregunta(),
                                                        'respuesta'     => $encuestaPregunta->getValor(),
                                                        'tipoRespuesta' => $pregunta->getTipoRespuesta()
                                                        );
                }
                
                $archivo              = fopen($pathSource . '/Resources/views/Default/encuesta.html.twig', "w");

                //Obtengo los detalles y datos generales del servicio.
                $arrayParamServ       = array(
                                                'idServicio'        => $idServicio,
                                            );
                $arrayDetalleServicio = $this->detallesDelServicio($arrayParamServ);
                $arrayDatosGenerales  = $this->datoGeneralServicio($arrayParamServ);
                $strNombreContactoSitio = '';
                $strNumeroContactoSitio = '';
                foreach($arrayPreguntaEncuestaPdf as $objDatos)
                {
                    if($objDatos["pregunta"] === "NOMBRE Y APELLIDO DEL CONTACTO EN SITIO")
                    {
                            $strNombreContactoSitio = $objDatos['respuesta'];
                    }
                    if($objDatos["pregunta"] === "Número Telf. del Contacto en sitio: ")
                    {
                            $strNumeroContactoSitio = $objDatos['respuesta'];
                    }
                }
                if($archivo)
                {
                    fwrite($archivo, $html);
                    fclose($archivo);
                    //generar PDF
                    $arrayPdf = array   (
                                            'serverRoot'                => $serverRoot,
                                            'idServicio'                => $idServicio,
                                            'prefijoEmpresa'            => $prefijoEmpresa,
                                            'preguntaEncuesta'          => $arrayPreguntaEncuestaPdf,
                                            'detalleCliente'            => $arrayDetalleServicio,
                                            'datoGeneral'               => $arrayDatosGenerales,
                                            'codigo'                    => $codigo,
                                            'hora'                      => $hora,
                                            'pathSource'                => $pathSource,
                                            'strRutaFisicaCompleta'     => $strRutaFisicaCompleta,
                                            'strRutaFisicaArchivo'      => $strRutaFisicaArchivo,
                                            'bandNfs'                   => $arrayParametros['bandNfs'],
                                            'strApp'                    => $arrayParametros['strAplicacion'],
                                            'strSubModulo'              => $arrayParametros['strOrigenAccion'],
                                            'idComunicacion'            => $idDetalle,
                                            'strUsrCreacion'            => $usrCreacion,
                                            'strIntegrantesCuadrilla'   => $strIntegrantesCuadrilla,
                                            'strNombreContactoSitio'    => $strNombreContactoSitio ,
                                            'strNombreCuadrilla'        => $strNombreCuadrilla,
                                            'strJefeCuadrilla'          => $strJefeCuadrilla,
                                            'strNumeroContactoSitio'    => $strTelfPersonaSitio,
                                            'strFechaContrato'          => $strFechaContrato
                                        );
                    $strRutaDocumento  = $this->generarPdf($arrayPdf);
                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        $strRutaFisicaCompleta = $strRutaDocumento;
                    }

                    //enviar por mail plantilla
                    $arrayPlantilla = array(
                                                'serverRoot'            => $serverRoot,
                                                'codigo'                => $codigo,
                                                'hora'                  => $hora,
                                                'punto'                 => $punto,
                                                'prefijoEmpresa'        => $prefijoEmpresa,
                                                'idEmpresa'             => $idEmpresa,
                                                'idServicio'            => $idServicio,
                                                'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                                'bandNfs'               => $arrayParametros['bandNfs']
                                            );
                    $this->enviarPlantilla($arrayPlantilla);
                    
                    //InfoDocumento
                    $infoDocumento = new InfoDocumento();
                    $infoDocumento->setTipoDocumentoId($tipoDocumento);
                    $infoDocumento->setTipoDocumentoGeneralId(7);
                    $infoDocumento->setNombreDocumento('Encuesta Codigo : ' . $codigo);
                    $infoDocumento->setUbicacionLogicaDocumento('Encuesta_' . $codigo . '-' . $hora . '.pdf');
                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        $infoDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta);
                    }
                    else
                    {
                        $infoDocumento->setUbicacionFisicaDocumento($serverRoot . '/' .
                                                                $strRutaFisicaCompleta . '/Encuesta_' .
                                                                $codigo . '-' . $hora . '.pdf');
                    }
                    $infoDocumento->setEstado('Activo');
                    $infoDocumento->setEmpresaCod($idEmpresa);
                    $infoDocumento->setFechaDocumento($feCreacion);
                    $infoDocumento->setUsrCreacion($usrCreacion);
                    $infoDocumento->setFeCreacion($feCreacion);
                    $infoDocumento->setIpCreacion($ipCreacion);
                    $this->emComunicacion->persist($infoDocumento);
                    $this->emComunicacion->flush();

                    //InfoDocumento
                    if(!is_object($infoDocumento) || $infoDocumento->getId()==null || $infoDocumento->getId()==0)
                    {
                        throw new \Exception("Problema al procesar el archivo.
                        Objeto documento no se ha guardado correctamente, intente nuevamente");
                    }

                    //InfoDocumentoRelacion
                    $infoDocumentoRelacion = new InfoDocumentoRelacion();
                    $infoDocumentoRelacion->setDocumentoId($infoDocumento->getId());
                    $infoDocumentoRelacion->setModulo('TECNICO');
                    $infoDocumentoRelacion->setServicioId($idServicio);
                    $infoDocumentoRelacion->setEncuestaId($infoEncuesta->getId());

                    if($servicio)
                    {
                        $infoDocumentoRelacion->setPuntoId($servicio->getPuntoId()->getId());
                    }

                    if($punto)
                    {
                        $infoDocumentoRelacion->setPersonaEmpresaRolId($punto->getPersonaEmpresaRolId()->getId());
                        $login = $punto->getLogin();
                    }
                    else
                    {
                        $login = 'N/A';
                    }

                    if(isset($idDetalle) && !empty($idDetalle))
                    {
                        $detalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')->findOneBy(array("id" => $idDetalle));
                    }
                    else
                    {
                        //Se obtiene el id de la solicitud de planificacion que tiene asociado el servicio, con el objetivo de relacionarlo con el
                        //documento
                        $detalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')->getUltimoDetalleSolicitud($idServicio);
                    }
                    if(is_object($detalle))
                    {
                        $infoDocumentoRelacion->setDetalleId($detalle->getId());
                    }
                    else if(is_array($detalle))
                    {
                        $infoDocumentoRelacion->setDetalleId($detalle['IDDETALLE']);
                    }
                    else
                    {
                        throw new \Exception("Problema al procesar la encuesta no se ha encontrado la solicitud asociada a este servicio, intente nuevamente.");
                    }

                    $infoDocumentoRelacion->setEstado('Activo');
                    $infoDocumentoRelacion->setFeCreacion($feCreacion);
                    $infoDocumentoRelacion->setUsrCreacion($usrCreacion);
                    $this->emComunicacion->persist($infoDocumentoRelacion);
                    $this->emComunicacion->flush();

                    if(!is_object($infoDocumentoRelacion) || $infoDocumentoRelacion->getId()==null || $infoDocumentoRelacion->getId()==0){
                        throw new \Exception("Problema al procesar la encuesta, no se ha podido relacionar el documento. Intente nuevamente.");
                    }
                    $mensaje = 'Encuesta procesada correctamente';
                    $status = "OK";
                }
                else
                {
                    throw new \Exception("Problema al procesar el archivo (Permisos), intente nuevamente");
                }
            }
            else
            {
                $status     = "ERROR";
                $mensaje    = "No se puede crear directorio.";
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $status             = "ERROR";
            $mensaje            = "Mensaje: ".$e->getMessage();
            $respuestaFinal     = array('status'=>$status, 'mensaje'=>$mensaje);
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
     * Funcion que sirve parar grabar la encuesta del servicio por soporte
     *
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 3-08-2015
     *
     * @author Modificado: Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.1 02-12-2015 - Se realizan ajustes para relacionar el detalleId de la ultima solicitud de planificacion o migracion
     *                           que tenga realacionado el servicio
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.2 06-06-2017 - Se envia el idServicio para generar la encuesta para la empresa TN y MD
     * 
     * @author Modificado: Wilmer Vera G. <wvera@telconet.ec>
     * @version 1.3 25-10-2018 - Se realizan validaciones tanto en Objetos como en arrays ya antes declarados o llamados.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.4 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.5 11-11-2020 - Almacenar documento en el servidor nfs remoto.
     * 
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.5 30-09-2021 
     * - Se agregan paámetros para la generación de actas con nuevos ISO.
     * - Se agrega validaciones de estos nuevos parámetros.
     *        ['strIntegrantesCuadrilla'] -> Integrante de cuadrilla.
     *        ['strNombreCuadrilla'] -> nombre de la cuadrilla.
     *        ['strJefeCuadrilla'] -> nombre de jefe de cuadrilla.
     *        ['strTelfPersonaSitio'] -> telf. ingresado en el móvil por la persona en sitio.
     *        ['strFechaContrato'] -> fecha del contrato. 
     *
     * @param array $arrayParametros 
     * @param array $respuestaFinal
     */
    public function grabarEncuestaSoporte($arrayParametros)
    {
        $idEmpresa              = $arrayParametros['idEmpresa'];
        $prefijoEmpresa         = $arrayParametros['prefijoEmpresa'];
        $idCaso                 = $arrayParametros['idCaso'];
        $idServicio             = $arrayParametros['idServicio'];
        $idDetalle              = $arrayParametros['idDetalle'];
        $firmaCoordenadas       = $arrayParametros['firmaCoordenadas'];
        $firmaBase64            = $arrayParametros['firmaBase64'];
        $arrayPreguntaRespuesta = $arrayParametros['preguntaRespuesta'];
        $strCodigoPlantilla     = $arrayParametros['strCodigoPlantilla'];
        $usrCreacion            = $arrayParametros['usrCreacion'];
        $ipCreacion             = $arrayParametros['ipCreacion'];
        $feCreacion             = $arrayParametros['feCreacion'];
        $serverRoot             = $arrayParametros['serverRoot'];
        $pathSource             = $arrayParametros['pathSource'];
        $strIntegrantesCuadrilla    =   $arrayParametros['strIntegrantesCuadrilla'];
        $strNombreCuadrilla         =   $arrayParametros['strNombreCuadrilla'];
        $strJefeCuadrilla           =   $arrayParametros['strJefeCuadrilla'];
        $strFechaSubsContrato       =   $arrayParametros['fechaContrato'];
        $strTelfPersonaSitio        =   $arrayParametros['strTelfPersonaSitio'];
        
        $hora                   = date('Y-m-d-His');
        $status                 = "ERROR";
        $strRutaFisicaCompleta  = '';
        $arrayFirma             = array();
        $arrayDocumento         = array();
        $boolGenerar            = false;
        
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                  ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }
        
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComunicacion->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/
        
        try
        {
            //datos para grabar la firma
            if(isset($arrayParametros['strRutaFisicaCompleta']) && !empty($arrayParametros['strRutaFisicaCompleta']))
            {
                $strRutaFisicaCompleta  = $arrayParametros['strRutaFisicaCompleta'];
                $strRutaFisicaArchivo      = substr($strRutaFisicaCompleta, 0, strrpos($strRutaFisicaCompleta, '/')+1) . 'firmas/';
            }
            else
            {
                $strRutaFisicaCompleta  = 'public/uploads/documentos';
                $strRutaFisicaArchivo      = 'public/uploads/firmas/';
            }

            $extensionArchivo   = 'png';
            $nombreArchivo      = $idServicio;
            
            $arrayFirma['strPath']      = $strRutaFisicaArchivo;
            $arrayDocumento['strPath']  = $strRutaFisicaCompleta;
            // Si el directorio se crea exitosamente retornara valor 100.
            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                $boolGenerar = true;
            }
            else
            {
                $boolGenerar = ("100" === $this->serviceUtil->creaDirectorio($arrayFirma)->getStrStatus() &&
                                "100" === $this->serviceUtil->creaDirectorio($arrayDocumento)->getStrStatus()) ? 1 : 0;
            }
            if($boolGenerar)
            {

                //grabar firma en imagen
                if($firmaCoordenadas!="")
                {
                    $this->procesarImagenesService->grabarImagenCoordenadas($firmaCoordenadas, 
                                                                            $nombreArchivo, 
                                                                            $strRutaFisicaArchivo, 
                                                                            $extensionArchivo);
                }
                if($firmaBase64!="")
                {
                    $this->procesarImagenesService->grabarImagenBase64($firmaBase64, $nombreArchivo, $strRutaFisicaArchivo, $extensionArchivo);
                }
                
                //InfoEncuesta
                $infoEncuesta = new InfoEncuesta();
                $infoEncuesta->setEstado('Activo');
                $codigo = $this->emComunicacion->getRepository('schemaBundle:InfoEncuesta')->getCodigoEncuesta();
                $infoEncuesta->setCodigo($codigo);
                $infoEncuesta->setNombreEncuesta('Encuesta Codigo : ' . $codigo);
                $infoEncuesta->setFeCreacion($feCreacion);
                $infoEncuesta->setUsrCreacion($usrCreacion);
                $infoEncuesta->setIpCreacion($ipCreacion);
                $this->emComunicacion->persist($infoEncuesta);
                $this->emComunicacion->flush();

                $servicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
                $punto    = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($servicio->getPuntoId());
                if(!is_object($infoEncuesta) || $infoEncuesta->getId()==null || $infoEncuesta->getId()==0){
                    throw new \Exception("Problema al procesar el archivo. 
                    Objeto encuesta no se ha guardado correctamente, intente nuevamente");
                }

                //Preguntas con sus Respuestas
                $arrayPreguntasRespuestas = explode("|", $arrayPreguntaRespuesta);

                foreach($arrayPreguntasRespuestas as $pregResp)
                {
                    $respuestas = explode("-", $pregResp);

                    if(count($respuestas) > 1)
                    {
                        $infoEncuestaPregunta = new InfoEncuestaPregunta();
                        $infoEncuestaPregunta->setPreguntaId($respuestas[0]);
                        $infoEncuestaPregunta->setEncuestaId($infoEncuesta->getId());
                        $infoEncuestaPregunta->setValor($respuestas[1]);
                        $infoEncuestaPregunta->setEstado('Activo');
                        $infoEncuestaPregunta->setFeCreacion($feCreacion);
                        $infoEncuestaPregunta->setUsrCreacion($usrCreacion);
                        $infoEncuestaPregunta->setIpCreacion($ipCreacion);
                        $this->emComunicacion->persist($infoEncuestaPregunta);
                        $this->emComunicacion->flush();
                    }
                }

                $tipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');
                if(!is_object($tipoDocumento))
                {
                    throw new \Exception("Problema al procesar el archivo. 
                    Objeto tipo documento PDF no se ha encontrado, intente nuevamente");
                }

                //Se genera documento fisico del PDF relacionado a la plantilla            
                $plantilla      = $this->emComunicacion->getRepository('schemaBundle:AdmiPlantilla')->findOneByCodigo($strCodigoPlantilla);
                $html           = $plantilla->getPlantilla();

                //obtener las preguntas con sus respuestas para generar el pdf
                $infoEncuestaPregunta = $this->emComunicacion->getRepository('schemaBundle:InfoEncuestaPregunta')
                                            ->findByEncuestaId($infoEncuesta->getId());
                foreach($infoEncuestaPregunta as $encuestaPregunta)
                {
                    $pregunta = $this->emComunicacion->getRepository('schemaBundle:AdmiPregunta')->find($encuestaPregunta->getPreguntaId());

                    $arrayPreguntaEncuestaPdf[] = array(
                                                        'idPregunta'    => $pregunta->getId(),
                                                        'pregunta'      => $pregunta->getPregunta(),
                                                        'respuesta'     => $encuestaPregunta->getValor(),
                                                        'tipoRespuesta' => $pregunta->getTipoRespuesta()
                                                        );
                }
                
                $archivo              = fopen($pathSource . '/Resources/views/Default/encuesta.html.twig', "w");
                //Obtengo los detalles y datos generales del servicio.
                $arrayParamServ       = array(
                                                'idServicio'        => $idServicio,
                                            );
                $arrayDetalleServicio = $this->detallesDelServicio($arrayParamServ);
                $arrayDatosGenerales  = $this->datoGeneralServicio($arrayParamServ);
                $strNombreContactoSitio = '';
                $strNumeroContactoSitio = '';
                foreach($arrayPreguntaEncuestaPdf as $objDatos)
                {
                    if($objDatos["pregunta"] === "NOMBRE Y APELLIDO DEL CONTACTO EN SITIO")
                    {
                            $strNombreContactoSitio = $objDatos['respuesta'];
                    }
                    if($objDatos["pregunta"] === "Número Telf. del Contacto en sitio: ")
                    {
                            $strNumeroContactoSitio = $objDatos['respuesta'];
                    }
                }
                if($archivo)
                {
                    fwrite($archivo, $html);
                    fclose($archivo);

                    //generar PDF
                    $arrayPdf = array   (
                                            'serverRoot'            => $serverRoot,
                                            'idServicio'            => $idServicio,
                                            'prefijoEmpresa'        => $prefijoEmpresa,
                                            'preguntaEncuesta'      => $arrayPreguntaEncuestaPdf,
                                            'detalleCliente'        => $arrayDetalleServicio,
                                            'datoGeneral'           => $arrayDatosGenerales,
                                            'codigo'                => $codigo,
                                            'hora'                  => $hora,
                                            'pathSource'            => $pathSource,
                                            'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                            'strRutaFisicaArchivo'  => $strRutaFisicaArchivo,
                                            'bandNfs'               => $arrayParametros['bandNfs'],
                                            'strApp'                => $arrayParametros['strAplicacion'],
                                            'strSubModulo'          => $arrayParametros['strOrigenAccion'],
                                            'idComunicacion'        => $idDetalle,
                                            'strUsrCreacion'        => $usrCreacion,
                                            'strIntegrantesCuadrilla'   => $strIntegrantesCuadrilla,
                                            'strNombreContactoSitio'    => $strNombreContactoSitio ,
                                            'strNombreCuadrilla'        => $strNombreCuadrilla,
                                            'strJefeCuadrilla'          => $strJefeCuadrilla,
                                            'strFechaContrato'          => $strFechaSubsContrato,
                                            'strNumeroContactoSitio'    => $strTelfPersonaSitio
                                        );
                    $strRutaDocumento  = $this->generarPdf($arrayPdf);
                    if(!isset($arrayParametros['bandNfs']) && !file_exists($strRutaDocumento))
                    {
                        throw new \Exception("Problema al crear el archivo PDF en el directorio, intenta nuevamente");    
                    }

                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        $strRutaFisicaCompleta = $strRutaDocumento;
                    }
                    //enviar por mail plantilla
                    $arrayPlantilla = array(
                                                'serverRoot'            => $serverRoot,
                                                'codigo'                => $codigo,
                                                'hora'                  => $hora,
                                                'punto'                 => $punto,
                                                'prefijoEmpresa'        => $prefijoEmpresa,
                                                'idEmpresa'             => $idEmpresa,
                                                'idServicio'            => $idServicio,
                                                'strRutaFisicaCompleta' => $strRutaFisicaCompleta,
                                                'bandNfs'               => $arrayParametros['bandNfs']
                                            );
                    $this->enviarPlantilla($arrayPlantilla);
                    
                    //InfoDocumento
                    $infoDocumento = new InfoDocumento();
                    $infoDocumento->setTipoDocumentoId($tipoDocumento);
                    $infoDocumento->setTipoDocumentoGeneralId(7);
                    $infoDocumento->setNombreDocumento('Encuesta Codigo : ' . $codigo);
                    $infoDocumento->setUbicacionLogicaDocumento('Encuesta_' . $codigo . '-' . $hora . '.pdf');
                    if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
                    {
                        $infoDocumento->setUbicacionFisicaDocumento($strRutaFisicaCompleta);
                    }
                    else
                    {
                        $infoDocumento->setUbicacionFisicaDocumento($serverRoot . '/' .
                                                                $strRutaFisicaCompleta . '/Encuesta_' .
                                                                $codigo . '-' . $hora . '.pdf');
                    }
                    $infoDocumento->setEstado('Activo');
                    $infoDocumento->setEmpresaCod($idEmpresa);
                    $infoDocumento->setFechaDocumento($feCreacion);
                    $infoDocumento->setUsrCreacion($usrCreacion);
                    $infoDocumento->setFeCreacion($feCreacion);
                    $infoDocumento->setIpCreacion($ipCreacion);
                    $this->emComunicacion->persist($infoDocumento);
                    $this->emComunicacion->flush();

                    //InfoDocumentoRelacion
                    if(!is_object($infoDocumento) || $infoDocumento->getId()==null || $infoDocumento->getId()==0)
                    {
                        throw new \Exception("Problema al procesar el archivo.
                        Objeto documento no se ha guardado correctamente, intente nuevamente");
                    }
                    $infoDocumentoRelacion = new InfoDocumentoRelacion();
                    $infoDocumentoRelacion->setDocumentoId($infoDocumento->getId());
                    $infoDocumentoRelacion->setModulo('SOPORTE');
                    $infoDocumentoRelacion->setCasoId($idCaso);
                    $infoDocumentoRelacion->setServicioId($idServicio);
                    $infoDocumentoRelacion->setEncuestaId($infoEncuesta->getId());
                    if($servicio)
                    {
                        $infoDocumentoRelacion->setPuntoId($servicio->getPuntoId()->getId());
                    }
                    if($punto)
                    {
                        $infoDocumentoRelacion->setPersonaEmpresaRolId($punto->getPersonaEmpresaRolId()->getId());
                        $login = $punto->getLogin();
                    }
                    else
                    {
                        $login = 'N/A';
                    }

                    if(isset($idDetalle) && !empty($idDetalle))
                    {
                        $detalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')->findOneBy(array("id" => $idDetalle));
                    }
                    else
                    {
                        //Se obtiene el id de la solicitud de planificacion que tiene asociado el servicio, con el objetivo de relacionarlo con el
                        //documento
                        $detalle = $this->emComercial->getRepository('schemaBundle:InfoDetalle')->getUltimoDetalleSolicitud($idServicio);
                    }
                    if(is_object($detalle))
                    {
                        $infoDocumentoRelacion->setDetalleId($detalle->getId());
                    }
                    else if(is_array($detalle))
                    {
                        $infoDocumentoRelacion->setDetalleId($detalle['IDDETALLE']);
                    }
                    else
                    {
                        throw new \Exception("Problema al procesar la encuesta no se ha encontrado la solicitud asociada a este servicio, intente nuevamente.");
                    }

                    $infoDocumentoRelacion->setEstado('Activo');
                    $infoDocumentoRelacion->setFeCreacion($feCreacion);
                    $infoDocumentoRelacion->setUsrCreacion($usrCreacion);
                    $this->emComunicacion->persist($infoDocumentoRelacion);
                    $this->emComunicacion->flush();

                    if(!is_object($infoDocumentoRelacion) || $infoDocumentoRelacion->getId()==null || $infoDocumentoRelacion->getId()==0){
                        throw new \Exception("Problema al procesar la encuesta, no se ha podido relacionar el documento. Intente nuevamente.");
                    }
                    $mensaje = 'Encuesta procesada correctamente';
                    $status = "OK";
                }
                else
                {
                    throw new \Exception("Problema al procesar el archivo (Permisos), intenta nuevamente");
                }
            }
            else
            {
                $status     = "ERROR";
                $mensaje    = "No se puede crear directorio.";
            }
        }
        catch(\Exception $e)
        {
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            $status             = "ERROR";
            $mensaje            = "Mensaje: ".$e->getMessage();
            $respuestaFinal     = array('status'=>$status, 'mensaje'=>$mensaje);
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
     * Funcion que sirve para enviar la plantilla de la encuesta por mail al cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-06-2015
     *
     * @author Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 16-06-2017 - Se parametriza el envio de correo de las encuesta.
     * 
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 18-01-2019 - Se valida el envío correo para Netvoice.
     * 
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 02-06-2020 - Se modifica código para crear nueva estructura de archivos.
     * 
     * Se reemplaza logica para extraer dato del producto, haciendo uso del ORM de SF.
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.4 28-01-2022 
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.5 20/03/2023 - Se agrega el asunto del correo enviado al cliente para encuestas de instalación EN.
     *  
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.6 05/04/2023 - Se cambia el remitente y se agrega nueva plantilla
     * 
     * @param $arrayParametros
     */
    public function enviarPlantilla($arrayParametros)
    {
        $serverRoot     = $arrayParametros['serverRoot'];
        $codigo         = $arrayParametros['codigo'];
        $hora           = $arrayParametros['hora'];
        $punto          = $arrayParametros['punto'];
        $idEmpresa      = $arrayParametros['idEmpresa'];
        $strAsuntoCorreo= '';
        $intIdServicio  = $arrayParametros['idServicio'];
        
        $strDirDocumentos = $serverRoot . '/' . $arrayParametros['strRutaFisicaCompleta'] . '/';
        
        $correos = $this->emComercial->getRepository('schemaBundle:InfoPersonaFormaContacto')->findCorreosPorPunto($punto->getId());

        if(!empty($intIdServicio) && $intIdServicio != 0 )                    
        {
            $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);  
            if(is_object($objServicio) && is_object($objServicio->getProductoId()))
            {
                $strDescTarea = $objServicio->getProductoId()->getDescripcionProducto();
            }
        }
        
        if($correos != '')
        {
            $correos = explode(",", $correos);                   

            $strArchivoAdjunto = $strDirDocumentos . 'Encuesta_' . $codigo . '-' . $hora . '.pdf';
            if(isset($arrayParametros['bandNfs']) && $arrayParametros['bandNfs'])
            {
                $strArchivoAdjunto = $arrayParametros['strRutaFisicaCompleta'];
            }

            $objPersona = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                            ->find($punto->getPersonaEmpresaRolId()->getPersonaId()->getId());

            if($objPersona)
            {
                $arrayParametros['cliente'] = sprintf($objPersona);                    
            }
            else
            {
                $arrayParametros['cliente'] = '';
            }

            if($arrayParametros['prefijoEmpresa'] == 'TN')
            {
                $strNombreEmpresa = "TELCONET";
                $strCodigoCorreo  = 'ENC-CLI-COR-TN';
                $strCorreo        = 'notificaciones_telcos@telconet.ec';
                $strAsuntoCorreo  = 'TELCONET, Resultados  Encuesta de Satisfacción de servicios.';
            }
            else if($arrayParametros['prefijoEmpresa'] == 'MD')
            {
                $strNombreEmpresa = "NETLIFE";
                $strCodigoCorreo  = 'ENC-CLI-CORREO';
                $strCorreo        = 'notificaciones@netlife.net.ec';
                $strAsuntoCorreo  = 'Tu servicio de NETLIFE se ha instalado exitosamente, Adjunto encuentra la Encuesta '
                                    . 'de Satisfacción de tu Instalación';
            }
            else if($arrayParametros['prefijoEmpresa'] == 'EN')
            {
                $strNombreEmpresa = "ECUANET";
                $strCodigoCorreo  = 'ENC-EN-CORREO';
                $strCorreo        = 'notificacionesecuanet@ecuanet.com.ec';
                $strAsuntoCorreo  = 'Tu servicio de ECUANET se ha instalado exitosamente, Adjunto encuentra la Encuesta '
                                    . 'de Satisfacción de tu Instalación';
            }
            if($strDescTarea == 'NETVOICE')
            {
                $strAsuntoCorreo = 'Tu servicio de NETVOICE se ha instalado exitosamente, Adjunto encuentra la Encuesta de Satisfacción de tu Instalación.';
                $strCodigoCorreo = 'ENC-CLI-COR-NV';
                $strCorreo       = 'notificaciones@netvoice.ec';
            }
            
            $this->envioPlantilla->generarEnvioPlantilla($strAsuntoCorreo,
                                                   $correos, 
                                                   $strCodigoCorreo,
                                                   $arrayParametros, 
                                                   $idEmpresa, 
                                                   '', 
                                                   '', 
                                                   $strArchivoAdjunto,
                                                   false,
                                                   $strCorreo);
        }
    }
    
    /**
     * Funcion que sirve para generar pdf de la encuesta llenada por el cliente
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 11-06-2015
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.1 13-06-2017 - Se obtiene pathSource por parametro para poder obtener el bundle que
     *                           se usa para renderizar el twig que sera convertido a pdf.
     *
     * @author Ronny Morán <rmoranc@telconet.ec>
     * @version 1.2 11-01-2019 - Se agrega validación para mostrar el logo de Netvoice en la encuesta generada. 
     *
     * @author Jean Pierre Nazareno <jnazareno@telconet.ec>
     * @version 1.3 02-06-2020 - Se modifica código para crear nueva estructura de archivos.                          
     *
     * @author Wilmer Vera <wvera@telconet.ec>
     * @version 1.5 30-09-2021 
     * - Se agregan paámetros para la generación de actas con nuevos ISO.
     * - Se agrega validaciones de estos nuevos parámetros.
     *        ['strIntegrantesCuadrilla'] -> Integrante de cuadrilla.
     *        ['strNombreCuadrilla'] -> nombre de la cuadrilla.
     *        ['strJefeCuadrilla'] -> nombre de jefe de cuadrilla.
     *        ['strTelfPersonaSitio'] -> telf. ingresado en el móvil por la persona en sitio.
     *        ['strFechaContrato'] -> fecha del contrato. 
     * 
     * 
     * @author Jeampier Carriel <jacarriel@telconet.ec>
     * @version 1.6 20/03/2023 - Se agrega el imagen de empresa Ecuanet para encuestas de servicio de instalacion.
     * 
     * 
     * @param $arrayParametros
     */
    public function generarPdf($arrayParametros)
    {
        $strResult              = '';
        $serverRoot             = $arrayParametros['serverRoot'];
        $codigo                 = $arrayParametros['codigo'];
        $hora                   = $arrayParametros['hora'];
        $idServicio             = $arrayParametros['idServicio'];
        $arrayPreguntaEncuesta  = $arrayParametros['preguntaEncuesta'];
        $prefijoEmpresa         = $arrayParametros['prefijoEmpresa'];
        $pathSource             = $arrayParametros['pathSource'];
        $strIntegrantesCuadrilla   = $arrayParametros['strIntegrantesCuadrilla'];
        $strNombreContactoSitio    = $arrayParametros['strNombreContactoSitio'];
        $strNumeroContactoSitio    = $arrayParametros['strNumeroContactoSitio'];
        $strNombreCuadrilla     = $arrayParametros['strNombreCuadrilla'];
        $strJefeCuadrilla       = $arrayParametros['strJefeCuadrilla'];
        $strFechaContrato       = $arrayParametros['strFechaContrato'];
        $strDescTarea           = '';
        $strBundle      = "tecnicoBundle";
        if (strpos($pathSource,"soporte") > 0)
        {
            $strBundle  = "soporteBundle";
        }
        $strDirFirmas      = $serverRoot . '/' . $arrayParametros['strRutaFisicaArchivo'];
        $strDirDocumentos  = $serverRoot . '/' . $arrayParametros['strRutaFisicaCompleta']  . '/';

        if($prefijoEmpresa == 'MD')
        {
            $imagen = $this->pathTelcos . 'telcos/web/public/images/logo_netlife_big.jpg';
        }
        else if($prefijoEmpresa == 'TN')
        {
            $imagen = $this->pathTelcos . 'telcos/web/public/images/logo_telconet_plantilla.jpg';
        }
        else if($prefijoEmpresa == 'TTCO')
        {
            $imagen = $this->pathTelcos . 'telcos/web/public/images/logo_transtelco_new.jpg';
        }
        else if($prefijoEmpresa == 'EN')
        {
            $imagen = $this->pathTelcos . 'telcos/web/public/images/logo_ecuanet.png';
        }

        if(!empty($idServicio) && $idServicio != 0 )                    
        {
            $objServicio    = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);  
            if(is_object($objServicio) && is_object($objServicio->getProductoId()))
            {
                $strDescTarea = $objServicio->getProductoId()->getDescripcionProducto();
            }
        } 
                
        if($strDescTarea == 'NETVOICE')
        {
            $imagen = $this->pathTelcos . 'telcos/web/public/images/logo_netvoice.png';
        }

        $strPathFirmaCliente = $strDirFirmas . $idServicio . '.png';
        $arrayPDFCorreo      = array('cuerpo'               => $arrayPreguntaEncuesta,
                                     'detalleCliente'       => $arrayParametros['detalleCliente'],
                                     'datoGeneral'          => $arrayParametros['datoGeneral'],
                                     'firmaCliente'         => $strPathFirmaCliente, 
                                     'imagenCabecera'       => $imagen,
                                     'strIntegrantesCuadrilla' => $strIntegrantesCuadrilla,
                                     'strNombreContactoSitio'  => strtoupper($strNombreContactoSitio),
                                     'strNumeroContactoSitio'  => $strNumeroContactoSitio,
                                     'strNombreCuadrilla'      => $strNombreCuadrilla,
                                     'strJefeCuadrilla'        => $strJefeCuadrilla,
                                     'strFechaContrato'        => $strFechaContrato  );

        $htmlPdf             = $this->container->get('templating')->render($strBundle.':Default:encuesta.html.twig', $arrayPDFCorreo);

        $strResult = $strDirDocumentos . 'Encuesta_' . $codigo . '-' . $hora . '.pdf';

        if($arrayParametros['bandNfs'])
        {
            $objFile                = $this->container->get('knp_snappy.pdf')->getOutputFromHtml($htmlPdf);
            $arrayPathAdicional     = null;
            $strKey                 = isset($arrayParametros['idComunicacion']) ? $arrayParametros['idComunicacion'] : 'SinTarea';
            $arrayPathAdicional[]   = array('key' => $strKey);
            $strNombreArchivo       = 'Encuesta_' . $codigo . '-' . $hora . '.pdf';
            $arrayParamNfs          = array(
                                            'prefijoEmpresa'       => $arrayParametros['prefijoEmpresa'],
                                            'strApp'               => $arrayParametros['strApp'],
                                            'strSubModulo'         => $arrayParametros['strSubModulo'],
                                            'arrayPathAdicional'   => $arrayPathAdicional,
                                            'strBase64'            => base64_encode($objFile),
                                            'strNombreArchivo'     => $strNombreArchivo,
                                            'strUsrCreacion'       => $arrayParametros['strUsrCreacion']
                                        );
            $arrayRespNfsPdf        = $this->serviceUtil->guardarArchivosNfs($arrayParamNfs);
            if(isset($arrayRespNfsPdf) && $arrayRespNfsPdf['intStatus'] == 200)
            {
                $strResult = $arrayRespNfsPdf['strUrlArchivo'];
            }
            else
            {
                throw new \Exception($arrayRespNfsPdf['strMensaje'].'  -> EncuestaService->generarPdf()');
            }
        }
        else
        {
            $this->container->get('knp_snappy.pdf')->generateFromHtml($htmlPdf, $strResult);
        }

        return $strResult;
    }

    /*
     * Funcion que sirve para obtener el detalle del servicio
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 13-06-2017
     * @param  array $arrayParametros [
     *                                  "idServicio"        : Id del Servicio,
     *                                ]
     * @return array $arrayRespuesta
     */
    public function detallesDelServicio($arrayParametros)
    {
        $arrayRespuesta                 = array();
        $objDetalleServicio             = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['idServicio']);
        if(is_object($objDetalleServicio))
        {
            $arrayRespuesta['strLogin']      = $objDetalleServicio->getPuntoId()->getLogin();
            $arrayRespuesta['strCanton']     = '';
            $arrayRespuesta['strProvincia']  = '';
            $arrayRespuesta['strTipoEnlace'] = '';
            if(is_object($objDetalleServicio->getPuntoId()->getSectorId()))
            {
                if(is_object($objDetalleServicio->getPuntoId()->getSectorId()->getParroquiaId()))
                {
                    if(is_object($objDetalleServicio->getPuntoId()->getSectorId()->getParroquiaId()->getCantonId()))
                    {
                        $arrayRespuesta['strCanton'] = $objDetalleServicio->getPuntoId()->getSectorId()
                                                                          ->getParroquiaId()->getCantonId()->getNombreCanton();
                        if(is_object($objDetalleServicio->getPuntoId()->getSectorId()->getParroquiaId()->getCantonId()->getProvinciaId()))
                        {
                            $arrayRespuesta['strProvincia'] = $objDetalleServicio->getPuntoId()
                                                                                 ->getSectorId()
                                                                                 ->getParroquiaId()
                                                                                 ->getCantonId()
                                                                                 ->getProvinciaId()
                                                                                 ->getNombreProvincia();
                        }
                    }
                }
            }
        }
        $objServicioTecnico              = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                             ->findOneBy(array( "servicioId" => $arrayParametros['idServicio']));
        if(is_object($objServicioTecnico))
        {
            $arrayRespuesta['strTipoEnlace'] = $objServicioTecnico->getTipoEnlace();
            $objUltimaMilla                  = $this->emComercial->getRepository('schemaBundle:AdmiTipoMedio')
                                                                 ->findOneById($objServicioTecnico->getUltimaMillaId());
            $arrayRespuesta['strUltimaMill'] = (is_object($objUltimaMilla)) ? $objUltimaMilla->getNombreTipoMedio() : '';
        }
        return $arrayRespuesta;
    }

    /*
     * Funcion que sirve para obtener los datos generales del servicio
     *
     * @author Walther Joao Gaibor C <wgaibor@telconet.ec>
     * @version 1.0 13-06-2017
     * @param  array $arrayParametros [
     *                                  "idServicio"        : Id del Servicio,
     *                                ]
     * @return array $arrayRespuesta
     */
    public function datoGeneralServicio($arrayParametros)
    {
        $objDetalleServicio                  = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayParametros['idServicio']);
        if(is_object($objDetalleServicio))
        {
            $arrayRespuesta['strRazonSocial']= $objDetalleServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId()->__toString();
        }
        $arrayRespuesta['fechaEncuesta']     = (new \DateTime('now'))->format('Y-m-d H:i');

        return $arrayRespuesta;
    }
}