<?php

namespace telconet\administracionBundle\Service;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoRelacion;
use telconet\schemaBundle\Entity\InfoOrdenTrabajo;
use telconet\schemaBundle\Entity\InfoOrdenTrabajoDet;
use telconet\schemaBundle\Entity\AdmiTipoDocumento;
use telconet\schemaBundle\Entity\InfoOrdenTrabajoCaract;



class GestionTallerService {
    const NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT   =  'TIPO_MANTENIMIENTO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT   = 'ID_PLAN_MANTENIMIENTO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT        = 'ID_MANTENIMIENTO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_CASO_OT                 = 'ID_CASO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_KM_ACTUAL_OT            = 'KM_ACTUAL_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_TIPO_ASIGNADO_OT        = 'TIPO_ASIGNADO_ORDEN_TRABAJO_VEHICULO'; 
    const NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT         = 'ID_PER_ASIGNADO_ORDEN_TRABAJO_VEHICULO'; 
    const NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT      = 'ID_PER_CHOFER_PREDEFINIDO_ORDEN_TRABAJO_VEHICULO';
    const NOMBRE_CARACTERISTICA_VER_NUMERACION_OT	= 'VER_NUMERACION_ORDEN_TRABAJO_VEHICULO'; 
    

    private $emComercial;
    private $emInfraestructura;
    private $emGeneral;
    private $emComunicacion;
    private $emSeguridad;
    private $container;
    private $pathTelcos;
    private $templating;
    private $session;
    
    
    public function __construct(Container $container) 
    {
        $this->container            = $container;
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->templating           = $container->get('templating');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->session              = $container->get('session');
    }


    public function setDependencies(Container $container)
    {        
        
    }
    
    /**
     * Funcion que sirve para generar la orden de trabajo en pdf 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-07-2016
     * @param $arrayParametros
     */
    public function guardarOrdenTrabajoTransporte($arrayParametros)
    {
        $idPlanMantenimiento                = $arrayParametros["idPlanMantenimiento"];
        $idMantenimiento                    = $arrayParametros["idMantenimiento"];
        $asignadoA                          = $arrayParametros["asignadoA"];
        $kmActual                           = $arrayParametros["kmActual"];
        $idPerChoferPredefinido             = $arrayParametros["idPerChoferPredefinido"];
        $idPerAsignadoOrden                 = $arrayParametros["idPerContratista"];
        $idPerAutorizadoPor                 = $arrayParametros["idPerAutorizadoPor"];
        $fechaInicio                        = $arrayParametros["fechaInicio"];
        $fechaFin                           = $arrayParametros["fechaFin"];
        $strUserSession                     = $arrayParametros["userSession"];
        $intIdEmpresaSession                = $arrayParametros["idEmpresa"];
        $strPrefijoEmpresaSession           = $arrayParametros["prefijoEmpresa"];
        $strIpUserSession                   = $arrayParametros["ipUserSession"];
        $oficina                            = $arrayParametros["oficina"];
        $jsonTareasyCategoriasOrdenTrabajo  = $arrayParametros["jsonTareasyCategoriasOrdenTrabajo"];
        $idElemento                         = $arrayParametros["idElemento"];
        $observacionOT                      = $arrayParametros["observacionOT"];
        $tipoMantenimiento                  = $arrayParametros["tipoMantenimiento"];
        $idCasoMantenimiento                = $arrayParametros["idCasoMantenimiento"];
        $OTConNumeracionActual              = $arrayParametros["OTConNumeracionActual"];

        $this->emComercial->getConnection()->beginTransaction();
        $this->emComunicacion->getConnection()->beginTransaction();
        try
        {
            list($dayApertura,$mesApertura,$yearApertura)=explode('/',$fechaInicio);
            $datetimeApertura     = new \DateTime();
            $datetimeApertura->setDate($yearApertura, $mesApertura, $dayApertura );
            
            list($dayCierre,$mesCierre,$yearCierre)=explode('/',$fechaFin);
            $datetimeCierre     = new \DateTime();
            $datetimeCierre->setDate($yearCierre, $mesCierre, $dayCierre);
            
            $strCodigoNumeracion        = "";
            if($OTConNumeracionActual=="SI")
            {
                $strCodigoNumeracion    = "ORDVE";
            }
            else
            {
                $strCodigoNumeracion    = "ORDVP";
            }
            
            $nombrePerAsignadoOrden = "N/A";
            $objPerAsignadoOrden    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerAsignadoOrden);
            if($objPerAsignadoOrden)
            {
                $objPersonaAsignadoOrden= $objPerAsignadoOrden->getPersonaId();
                $nombrePerAsignadoOrden = sprintf('%s', $objPersonaAsignadoOrden);
            }
            
            $nombreAutorizadoPor    = "N/A";
            $objPerAutorizadoPor    = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerAutorizadoPor);
            if($objPerAutorizadoPor)
            {
                $objPersonaAutorizadoPor= $objPerAutorizadoPor->getPersonaId();
                $nombreAutorizadoPor    = sprintf("%s",$objPersonaAutorizadoPor);
            }
            
            
            $datosNumeracion                = $this->emComercial->getRepository('schemaBundle:AdmiNumeracion')
                                                    ->findByEmpresaYOficina($intIdEmpresaSession,$oficina,$strCodigoNumeracion);
            $secuenciaAsig                  = str_pad($datosNumeracion->getSecuencia(),7, "0", STR_PAD_LEFT); 
            $numeroOrdenTrabajo             = $datosNumeracion->getNumeracionUno()."-".$datosNumeracion->getNumeracionDos()."-".$secuenciaAsig;
            $entityOrdenTrabajo             = new InfoOrdenTrabajo();
            $entityOrdenTrabajo->setElementoId($idElemento);
            $entityOrdenTrabajo->setNumeroOrdenTrabajo($numeroOrdenTrabajo);
            $entityOrdenTrabajo->setFeCreacion(new \DateTime('now'));
            $entityOrdenTrabajo->setUsrCreacion($strUserSession);
            $entityOrdenTrabajo->setIpCreacion($strIpUserSession);
            $entityOrdenTrabajo->setOficinaId(null);
            $entityOrdenTrabajo->setEstado('Activo');
            $entityOrdenTrabajo->setObservacion($observacionOT);
            $entityOrdenTrabajo->setFeInicio($datetimeApertura);
            $entityOrdenTrabajo->setFeFin($datetimeCierre);
            $entityOrdenTrabajo->setPerAutorizacionId($idPerAutorizadoPor);
            $this->emComercial->persist($entityOrdenTrabajo);
            $this->emComercial->flush();
            
            if ($entityOrdenTrabajo)
            {
                //Actualizo la numeracion en la tabla
                $numeroActual=($datosNumeracion->getSecuencia()+1);
                $datosNumeracion->setSecuencia($numeroActual);
                $this->emComercial->persist($datosNumeracion);
                $this->emComercial->flush();
            }
            else
            {
                throw new \Exception('No se ha podido guardar la orden de trabajo. Por favor informe a Sistemas');
            }
            $objTmpJsonTareasyCategoriasOrdenTrabajo    = json_decode($jsonTareasyCategoriasOrdenTrabajo);
            if($objTmpJsonTareasyCategoriasOrdenTrabajo)
            {
                $intTotalTareasyCategoriasOrdenTrabajo      = $objTmpJsonTareasyCategoriasOrdenTrabajo->total;

                if( $intTotalTareasyCategoriasOrdenTrabajo )
                {
                    if( $intTotalTareasyCategoriasOrdenTrabajo > 0 )
                    {
                        $arrayTareasyCategoriasOrdenTrabajo = $objTmpJsonTareasyCategoriasOrdenTrabajo->tareasyCategoriasOrdenTrabajoTransporte;
                        foreach( $arrayTareasyCategoriasOrdenTrabajo as $objItemValoresTareasyCategoriasOrdenTrabajo )
                        {
                            $idTarea                = $objItemValoresTareasyCategoriasOrdenTrabajo->idTarea;
                            $idCategoriaTarea       = $objItemValoresTareasyCategoriasOrdenTrabajo->id_categoria_tarea;

                            $entityOrdenTrabajoDet  = new InfoOrdenTrabajoDet();
                            $entityOrdenTrabajoDet->setOrdenTrabajoId($entityOrdenTrabajo);
                            $entityOrdenTrabajoDet->setCategoriaTareaId($idCategoriaTarea);
                            $entityOrdenTrabajoDet->setTareaId($idTarea);
                            $entityOrdenTrabajoDet->setFeCreacion(new \DateTime('now'));
                            $entityOrdenTrabajoDet->setUsrCreacion($strUserSession);
                            $entityOrdenTrabajoDet->setIpCreacion($strIpUserSession);
                            $entityOrdenTrabajoDet->setEstado('Activo');
                            $this->emComercial->persist($entityOrdenTrabajoDet);
                            $this->emComercial->flush();
                        }

                    }
                }
            }
            /***Características de las órdenes de trabajo***/
            $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_TIPO_MANTENIMIENTO_OT]    = $tipoMantenimiento;
            $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_KM_ACTUAL_OT]             = $kmActual;
            $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_TIPO_ASIGNADO_OT]         = $asignadoA;
            $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_PER_ASIGNADO_OT]          = $idPerAsignadoOrden;
            $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_VER_NUMERACION_OT]	= $OTConNumeracionActual;
            
            
            /******Guardando las características de acuerdo al tipo de mantenimiento**********/
            $nombrePerConductor     = 'N/A';
            if($idPerChoferPredefinido)
            {
                $objPerConductor        = $this->emComercial->getRepository('schemaBundle:InfoPersonaEmpresaRol')->find($idPerChoferPredefinido);
                $objPersonaConductor    = $objPerConductor->getPersonaId();
                $nombrePerConductor     = sprintf("%s",$objPersonaConductor);
                $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_CHOFER_ASIGNADO_OT]   = $idPerChoferPredefinido;
            }
            
            if($tipoMantenimiento=="PREVENTIVO")
            {
                $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_PLAN_MANTENIMIENTO_OT]    = $idPlanMantenimiento;
                $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_MANTENIMIENTO_OT]         = $idMantenimiento;
                
            }
            else if($tipoMantenimiento=="CORRECTIVO")
            {
                $arrayCaracteristicas[self::NOMBRE_CARACTERISTICA_CASO_OT]  = $idCasoMantenimiento;
            }
            
            
            foreach( $arrayCaracteristicas as $nombreCaracteristica=>$valorCaracteristica)
            {
                $entityOrdenTrabajoCaract   = new InfoOrdenTrabajoCaract();
                $objCaracteristica          = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                                ->findOneByDescripcionCaracteristica($nombreCaracteristica);
                $entityOrdenTrabajoCaract->setOrdenTrabajo($entityOrdenTrabajo);
                $entityOrdenTrabajoCaract->setCaracteristica($objCaracteristica);
                $entityOrdenTrabajoCaract->setValor($valorCaracteristica);
                $entityOrdenTrabajoCaract->setEstado('Activo');
                $entityOrdenTrabajoCaract->setFeCreacion(new \DateTime('now'));
                $entityOrdenTrabajoCaract->setUsrCreacion($strUserSession);
                $entityOrdenTrabajoCaract->setIpCreacion($strIpUserSession);
                $this->emComercial->persist($entityOrdenTrabajoCaract);
                $this->emComercial->flush();
            }
            
            
            $fechaHora          = date('Y-m-d-His');
            $serverRoot         = $_SERVER['DOCUMENT_ROOT'];
            $objElemento        = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($entityOrdenTrabajo->getElementoId());
            $placa              = $objElemento->getNombreElemento();
            $modeloElemento     = $objElemento->getModeloElementoId()->getNombreModeloElemento();
            
            $dirDocumentosOTTransporte  = $serverRoot . '/public/uploads/'.$strPrefijoEmpresaSession."/administracion/ordenesTrabajoTransporte/";
            $nombreArchivoOTPDF         = "Orden_Trabajo_".$numeroOrdenTrabajo.'_'.$placa.'_'.$fechaHora.'.pdf';
            
            $tipoDocumento  = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')->findOneByExtensionTipoDocumento('PDF');
            
            $tipoDocumentoGeneral  = $this->emGeneral->getRepository('schemaBundle:AdmiTipoDocumentoGeneral')->findOneByCodigoTipoDocumento('ORTRA');
            
            if($tipoDocumentoGeneral)
            {
                //Genero el registro del PDF de la Orden de Trabajo
                $infoDocumento = new InfoDocumento();
                $infoDocumento->setTipoDocumentoId($tipoDocumento);
                $infoDocumento->setTipoDocumentoGeneralId($tipoDocumentoGeneral->getId());
                $infoDocumento->setNombreDocumento('Orden de Trabajo : ' . $numeroOrdenTrabajo);
                $infoDocumento->setUbicacionLogicaDocumento($nombreArchivoOTPDF);
                $infoDocumento->setUbicacionFisicaDocumento($dirDocumentosOTTransporte.$nombreArchivoOTPDF);
                $infoDocumento->setEstado('Activo');
                $infoDocumento->setEmpresaCod($intIdEmpresaSession);
                $infoDocumento->setFechaDocumento(new \DateTime('now'));
                $infoDocumento->setUsrCreacion($strUserSession);
                $infoDocumento->setFeCreacion(new \DateTime('now'));
                $infoDocumento->setIpCreacion($strIpUserSession);
                $this->emComunicacion->persist($infoDocumento);
                $this->emComunicacion->flush();

                $infoDocumentoRelacion = new InfoDocumentoRelacion();
                $infoDocumentoRelacion->setDocumentoId($infoDocumento->getId());
                $infoDocumentoRelacion->setOrdenTrabajoId($entityOrdenTrabajo->getId());
                $infoDocumentoRelacion->setElementoId($idElemento);
                $infoDocumentoRelacion->setEstado('Activo');
                $infoDocumentoRelacion->setFeCreacion(new \DateTime('now'));
                $infoDocumentoRelacion->setUsrCreacion($strUserSession);
                $this->emComunicacion->persist($infoDocumentoRelacion);
                $this->emComunicacion->flush();
            }
            else
            {
                throw new \Exception('No existe el tipo de documento ORDEN DE TRABAJO. Por favor informe a Sistemas ');
            }
            
            $objTipoDocumentoGeneral        = $this->emGeneral->getRepository("schemaBundle:AdmiTipoDocumentoGeneral")
                                                              ->findOneByCodigoTipoDocumento('FIRMA');
            $idTipoDocumentoGeneral         = $objTipoDocumentoGeneral->getId();
            
            $arrayParametrosAutorizadoPor   = array(
                                                    "idPersonaEmpresaRol"   => $idPerAutorizadoPor,
                                                    "tipoDocumentoGeneralId"=> $idTipoDocumentoGeneral,
                                                    "empresaCod"            => $intIdEmpresaSession
                                              );
            
            $strRutaFirmaAutorizadoPor = "";
            $arrayResultadoFirmaAutorizadoPor   = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                    ->getResultadoDocumentosPersona($arrayParametrosAutorizadoPor);
            $arrayRegistrosFirmaAutorizadoPor   = $arrayResultadoFirmaAutorizadoPor['resultado'];
            if($arrayRegistrosFirmaAutorizadoPor)
            {
                if($arrayRegistrosFirmaAutorizadoPor[0]["ubicacionFisicaDocumento"])
                {
                    $strRutaFirmaAutorizadoPor=$this->pathTelcos.$arrayRegistrosFirmaAutorizadoPor[0]["ubicacionFisicaDocumento"];
                }
            }
            
            $arrayParametrosChofer=array(
                                            "idPersonaEmpresaRol"   => $idPerChoferPredefinido,
                                            "tipoDocumentoGeneralId"=> $idTipoDocumentoGeneral,
                                            "empresaCod"            => $intIdEmpresaSession
                                    );
            $strRutaFirmaChofer                 = "";
            $arrayResultadoFirmaChofer          = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                                    ->getResultadoDocumentosPersona($arrayParametrosChofer);
            $arrayRegistrosFirmaChofer          = $arrayResultadoFirmaChofer['resultado'];
            if($arrayRegistrosFirmaChofer)
            {
                if($arrayRegistrosFirmaChofer[0]["ubicacionFisicaDocumento"])
                {
                    $strRutaFirmaChofer=$this->pathTelcos.$arrayRegistrosFirmaChofer[0]["ubicacionFisicaDocumento"];
                }
                
            }
            
            $idOrdenTrabajo = $entityOrdenTrabajo->getId();
            $arrayParametrosPDF = array(
                                        "idOrdenTrabajoTransporte"  => $idOrdenTrabajo,
                                        "idEmpresaSession"          => $intIdEmpresaSession,
                                        "prefijoEmpresaSession"     => $strPrefijoEmpresaSession,
                                        "serverRoot"                => $serverRoot,
                                        "dirDocumentosOTTransporte" => $dirDocumentosOTTransporte,
                                        "nombreArchivoOTPDF"        => $nombreArchivoOTPDF,
                                        "numeroOrdenTrabajo"        => $numeroOrdenTrabajo,
                                        "idElemento"                => $idElemento,
                                        "placa"                     => $placa,
                                        "modeloElemento"            => $modeloElemento,
                                        "fechaInicio"               => date_format($datetimeApertura, "d/m/Y"),
                                        "fechaFin"                  => date_format($datetimeCierre, "d/m/Y"),
                                        "nombrePerAsignadoOrden"    => $nombrePerAsignadoOrden,
                                        "nombrePerConductor"        => $nombrePerConductor,
                                        "nombreAutorizadoPor"       => $nombreAutorizadoPor,
                                        "kmActual"                  => $kmActual,
                                        "strRutaFirmaAutorizadoPor" => $strRutaFirmaAutorizadoPor,
                                        "strRutaFirmaChofer"        => $strRutaFirmaChofer,
                                        "strObservacionOT"          => $observacionOT,
                                        "tipoMantenimientoOT"       => $tipoMantenimiento,
                                        "OTConNumeracionActual"     => $OTConNumeracionActual
                                    );
            
            $this->generarPDFOrdenTrabajoTransporte($arrayParametrosPDF);
            
            $mensaje    = 'Orden de Trabajo procesada correctamente';
            $status     = "OK";
            
        }
        catch (\Exception $e) 
        {
            error_log($e->getMessage());

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            
            if ($this->emComunicacion->getConnection()->isTransactionActive())
            {
                $this->emComunicacion->getConnection()->rollback();
            }
            
            $status             = "ERROR";
            $mensaje            = "Mensaje: ".$e->getMessage();
            $respuestaFinal     = array('status'=>$status, 'mensaje'=>$mensaje);
            return $respuestaFinal;
        }
        
        if ($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }
        $this->emComercial->getConnection()->close();
        
        
        if ($this->emComunicacion->getConnection()->isTransactionActive())
        {
            $this->emComunicacion->getConnection()->commit();
        }
        $this->emComunicacion->getConnection()->close();
        
        $respuestaFinal = array('status' => $status, 'mensaje' => $mensaje,'idOrdenTrabajo'=> $idOrdenTrabajo);
        return $respuestaFinal;
        
    }
    
    
    /**
     * Funcion que sirve para generar la orden de trabajo en pdf 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-07-2016
     * @param $arrayParametros[
     *                          "idOrdenTrabajoTransporte"  : id de la orden de trabajo asociada al vehículo,
     *                          "idEmpresaSession"          : id de la empresa en sesión
     *                          "prefijoEmpresaSession"     : prefijo de la empresa en sesión
     *                          "serverRoot"                : ruta raíz para obtener los logos de las empresas
     *                          "dirDocumentosOTTransporte" : ruta donde se guardará el pdf
     *                          "nombreArchivoOTPDF"        : nombre del pdf que se generará
     *                          "numeroOrdenTrabajo"        : número de la orden de trabajo 
     *                          "idElemento"                : id del vehículo
     *                          "placa"                     : placa del vehículo
     *                          "modeloElemento"            : modelo del vehículo
     *                          "fechaInicio"               : fecha inicio de la orden de trabajo
     *                          "fechaFin"                  : fecha fin de la orden de trabajo
     *                          "nombrePerAsignadoOrden"    : nombre del contratista asignado
     *                          "nombrePerConductor"        : nombre del conductor predefinido asociado
     *                          "nombreAutorizadoPor"       : nombre de la persona que autoriza
     *                          "kmActual"                  : km actual del vehículo
     *                          "strRutaFirmaAutorizadoPor" : ruta donde se encuentra la firma de la persona que autoriza
     *                          "strRutaFirmaChofer"        : ruta de la firma del chofer predefinido asociado
     *                          "strObservacionOT"          : observación de la orden de trabajo
     *                          "tipoMantenimientoOT"       : tipo de mantenimiento: PREVENTIVO O CORRECTIVO
     *                          "OTConNumeracionActual"     : "SI" o "NO" se generó con numeración la orden de trabajo
     *                      ]
     * 
     */
    public function generarPDFOrdenTrabajoTransporte($arrayParametros)
    {
        $serverRoot                 = $arrayParametros['serverRoot'];
        $idOrdenTrabajoTransporte   = $arrayParametros["idOrdenTrabajoTransporte"];
        $idEmpresa                  = $arrayParametros["idEmpresaSession"];
        $prefijoEmpresa             = $arrayParametros["prefijoEmpresaSession"];
        $dirDocumentosOTTransporte  = $arrayParametros["dirDocumentosOTTransporte"];
        $nombreArchivoOTPDF         = $arrayParametros["nombreArchivoOTPDF"];
        $numeroOrdenTrabajo         = $arrayParametros["numeroOrdenTrabajo"];
        $idElemento                 = $arrayParametros["idElemento"];
        $placa                      = $arrayParametros["placa"];
        $modeloElemento             = $arrayParametros["modeloElemento"];
        $fechaInicio                = $arrayParametros["fechaInicio"];
        $fechaFin                   = $arrayParametros["fechaFin"];
        $nombrePerAsignadoOrden     = $arrayParametros["nombrePerAsignadoOrden"];
        $nombrePerConductor         = $arrayParametros["nombrePerConductor"];
        $nombreAutorizadoPor        = $arrayParametros["nombreAutorizadoPor"];
        $kmActual                   = $arrayParametros["kmActual"];
        $strRutaFirmaAutorizadoPor  = $arrayParametros["strRutaFirmaAutorizadoPor"];
        $strRutaFirmaChofer         = $arrayParametros["strRutaFirmaChofer"];
        $strObservacionOT           = $arrayParametros["strObservacionOT"];
        $strTipoMantenimientoOT     = $arrayParametros["tipoMantenimientoOT"];
        $OTConNumeracionActual      = $arrayParametros["OTConNumeracionActual"];

        $objDetallesElemento        = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                      ->findBy( array('elementoId' => $idElemento, 'estado' => "Activo") );

        $arrayDetallesElemento      = array( 'DISCO' => '', 'ANIO' => '');

        if( $objDetallesElemento )
        {
            foreach( $objDetallesElemento as $objDetalle  )
            {
                $arrayDetallesElemento[$objDetalle->getDetalleNombre()] = $objDetalle->getDetalleValor();
            }
        }

        $arrayParametrosOrdenTrabajo    = array("idOrdenTrabajoCab"=>$arrayParametros["idOrdenTrabajoTransporte"]);
        
        $arrayDetallesOrdenTrabajo      = $this->getDetallesOrdenTrabajoTransporte($arrayParametrosOrdenTrabajo);
        
        $rutaImagenCabecera = "";
        if($prefijoEmpresa == 'TN')
        {
            $rutaImagenCabecera = $serverRoot.'/public/images/logo_telconet.jpg';
        }
        else if($prefijoEmpresa == 'MD')
        {
            $rutaImagenCabecera = $serverRoot.'/public/images/logo_netlife_big.jpg';
        }
        else if($prefijoEmpresa == 'TTCO')
        {
            $rutaImagenCabecera = $serverRoot.'/public/images/logo_transtelco_new.jpg';
        }
       
        
        
        $arrayPDFOTTransporte = array(
                                        'idOrdenTrabajoTransporte'  => $idOrdenTrabajoTransporte,
                                        'numeroOrdenTrabajo'        => $numeroOrdenTrabajo,
                                        'ordenTrabajoDets'          => $arrayDetallesOrdenTrabajo,
                                        'placa'                     => $placa,
                                        'modeloElemento'            => $modeloElemento,
                                        'detallesElemento'          => $arrayDetallesElemento,
                                        'idEmpresaSession'          => $idEmpresa,
                                        'rutaimagenCabecera'        => $rutaImagenCabecera,
                                        'fechaInicio'               => $fechaInicio,
                                        'fechaFin'                  => $fechaFin,
                                        'nombrePerAsignadoOrden'    => $nombrePerAsignadoOrden,
                                        'nombrePerConductor'        => $nombrePerConductor,
                                        'nombreAutorizadoPor'       => $nombreAutorizadoPor,
                                        'kmActual'                  => number_format( $kmActual ,  0 , "," , "." ),
                                        'rutaFirmaAutorizadoPor'    => $strRutaFirmaAutorizadoPor,
                                        'rutaFirmaChofer'           => $strRutaFirmaChofer,
                                        'observacion'               => $strObservacionOT,
                                        'tipoMantenimientoOT'       => $strTipoMantenimientoOT,
                                        'OTConNumeracionActual'     => $OTConNumeracionActual
                                );

        $htmlPdf = $this->container->get('templating')->render('administracionBundle:GestionTaller:exportPDFOrdenTrabajoTransporte.html.twig', 
                                                                $arrayPDFOTTransporte);

        $this->container->get('knp_snappy.pdf')->generateFromHtml($htmlPdf, $dirDocumentosOTTransporte.$nombreArchivoOTPDF);

    }
    
    
    

    /**
     * 
     * Documentación para el método 'getDetallesOrdenTrabajoTransporte'.
     *
     * Función que obtiene el arreglo de las tareas asoaciadas a la respectiva categoría de tarea.
     * 
     * @param array $arrayParametrosOrdenTrabajo
     * 
     * @return array $arrayDetalleOrdenTrabajo.
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 14-07-2016
     *
     */
    public function getDetallesOrdenTrabajoTransporte($arrayParametrosOrdenTrabajo)
    {
        $arrayOrdenTrabajoDets  = $this->emComercial->getRepository('schemaBundle:InfoOrdenTrabajoDet')->getRegistros($arrayParametrosOrdenTrabajo);

        $arrayRegistrosOrdenTrabDet = $arrayOrdenTrabajoDets["resultado"];
        $arrayDetallesOrdenTrabajo   = array();
        foreach($arrayRegistrosOrdenTrabDet as $ordenTrabajoDet)
        {

                $arrayDetallesOrdenTrabajo[$ordenTrabajoDet["idCategoriaTarea"]][]=
                                                                                array(
                                                                                    "idOrdenTrabajoCab"     => $ordenTrabajoDet["idOrdenTrabajoCab"],
                                                                                    "idOrdenTrabajoDet"     => $ordenTrabajoDet["idOrdenTrabajoDet"],
                                                                                    "idTarea"               => $ordenTrabajoDet["idTarea"],
                                                                                    "nombreTarea"           => $ordenTrabajoDet["nombreTarea"],
                                                                                    "nombreCategoriaTarea"  => $ordenTrabajoDet["nombreCategoria"]
                                                                                );

        }
        $arrayResultadoCategoriasTareasOT= $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getResultadoDetallesParametro('CATEGORIAS TAREAS OT TALLER Y MOVILIZACION',"","");
        $arrayCategoriasTareasOT  = $arrayResultadoCategoriasTareasOT["registros"];
        
        foreach ($arrayCategoriasTareasOT as $categoriaTarea)
        {
            if(!isset($arrayDetallesOrdenTrabajo[$categoriaTarea["id"]]))
            {
                $arrayDetallesOrdenTrabajo[$categoriaTarea["id"]][]=array(
                                                                        "idOrdenTrabajoCab"     => $arrayParametrosOrdenTrabajo["idOrdenTrabajoCab"],
                                                                        "idOrdenTrabajoDet"     => '',
                                                                        "idTarea"               => '',
                                                                        "nombreTarea"           => '',
                                                                        "nombreCategoriaTarea"  => $categoriaTarea["valor1"]
                                                                            );
            }

        }

        ksort($arrayDetallesOrdenTrabajo);

        return $arrayDetallesOrdenTrabajo;
    }
    
    
    /**
     * guardarMultiplesAdjuntosMantenimientosTransporte
     *
     * Metodo encargado de guardar en base y en el directorio los archivos que el usuario desee subir.
     *
     * @param array $arrayParametros [ "idMantenimientoElemento","strCodigoDocumento","strPrefijoEmpresa","strIdEmpresa"]
     * 
     * @return bool 
     *
     * @author Lizbeth Cruz  <mlcruz@telconet.ec>
     * @version 1.0 07-07-2016
     */
    public function guardarMultiplesAdjuntosMantenimientosTransporte($arrayParametros)
    {
        $fecha_creacion     = new \DateTime('now'); 
        $serverRoot         = $this->pathTelcos."telcos/web";
        
        $idMantenimientoElemento    = $arrayParametros['idMantenimientoElemento'];
        $idOrdenTrabajo             = $arrayParametros['idOrdenTrabajo'];
        $idElemento                 = $arrayParametros['idElemento'];
        $strPrefijoEmpresa          = $arrayParametros['strPrefijoEmpresa'];
        $strUser                    = $arrayParametros['strUser'];
        $strIdEmpresa               = $arrayParametros['strIdEmpresa'];
        
        $arrayRutasArchivosSubidos = array();
        $this->emComunicacion->getConnection()->beginTransaction();
        
        try
        {
            foreach ($_FILES["archivos"]["error"] as $key => $error) 
            {
                if ($error == 0) 
                {
                    $nameFile       = $_FILES["archivos"]["name"][$key];
                    $partsNombreArchivo = explode('.', $nameFile);
                    $last = array_pop($partsNombreArchivo);
                    $partsNombreArchivo = array(implode('_', $partsNombreArchivo), $last);
                    
                    $nombreArchivo= $partsNombreArchivo[0];
                    $extArchivo   = $partsNombreArchivo[1];
                    $tipo                               = $extArchivo;
                    $prefijo                            = substr(md5(uniqid(rand())),0,6);
                    $nuevoNombre                        = $nombreArchivo . "_" . $prefijo . "." . $extArchivo;
                    $tofind                             = "#ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ·";
                    $replac                             = "_AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn-";
                    $nuevoNombre                        = strtr($nuevoNombre,$tofind,$replac);
                    $destino                            = $serverRoot."/public/uploads/".$strPrefijoEmpresa."/";
                    
                    $modulo     = "administracion/";
                    $funcion    = "adjuntosMantenimientosTransporte/";
                    $destino.=$modulo.$funcion;
                    $fichero_subido=$destino.$nuevoNombre;
                    
                    $entity = new InfoDocumento();
                    $entity->setNombreDocumento('Adjunto Mantenimiento Transporte');
                    $entity->setMensaje('Documento que se adjunta en un mantenimiento del transporte');
                    
                    $entity->setUbicacionFisicaDocumento($fichero_subido);
                    $entity->setUbicacionLogicaDocumento($nuevoNombre);

                    $entity->setEstado('Activo');
                    $entity->setFeCreacion($fecha_creacion);
                    $entity->setFechaDocumento($fecha_creacion);
                    $entity->setIpCreacion('127.0.0.1');
                    $entity->setUsrCreacion($strUser);
                    $entity->setEmpresaCod($strIdEmpresa);

                    $tipoDoc=  strtoupper($tipo);
                    if($tipoDoc=='JPG' || $tipo=='JPEG')
                    {
                       $tipoDoc = "JPG" ;
                    }

                    $objTipoDocumento = $this->emComunicacion->getRepository('schemaBundle:AdmiTipoDocumento')
                                                             ->findOneByExtensionTipoDocumento(array('extensionTipoDocumento'=> $tipoDoc));

                    if( $objTipoDocumento != null)
                    {
                        $entity->setTipoDocumentoId($objTipoDocumento);                                
                    }
                    else
                    {   
                        //Inserto registro con la extension del archivo a subirse
                        $objAdmiTipoDocumento = new AdmiTipoDocumento(); 
                        $objAdmiTipoDocumento->setExtensionTipoDocumento(strtoupper($tipoDoc));
                        $objAdmiTipoDocumento->setTipoMime(strtoupper($tipoDoc));                            
                        $objAdmiTipoDocumento->setDescripcionTipoDocumento('ARCHIVO FORMATO '.$tipoDoc);
                        $objAdmiTipoDocumento->setEstado('Activo');
                        $objAdmiTipoDocumento->setUsrCreacion( $strUser );
                        $objAdmiTipoDocumento->setFeCreacion( $fecha_creacion );                        
                        $this->emComunicacion->persist( $objAdmiTipoDocumento );
                        $this->emComunicacion->flush(); 
                        $entity->setTipoDocumentoId($objAdmiTipoDocumento);   
                    }
                    

                    move_uploaded_file($_FILES['archivos']['tmp_name'][$key], $fichero_subido);
                    $arrayRutasArchivosSubidos[]=$fichero_subido;

                    $this->emComunicacion->persist($entity);
                    $this->emComunicacion->flush();
                    
                    
                    //Entidad de la tabla INFO_DOCUMENTO_RELACION donde se relaciona el documento cargado con el IdCaso
                    $entityRelacion = new InfoDocumentoRelacion();
                    $entityRelacion->setModulo('SOPORTE');
                    $entityRelacion->setEstado('Activo');
                    $entityRelacion->setFeCreacion(new \DateTime('now'));
                    $entityRelacion->setUsrCreacion($strUser);
                    $entityRelacion->setMantenimientoElementoId($idMantenimientoElemento);
                    $entityRelacion->setOrdenTrabajoId($idOrdenTrabajo);
                    $entityRelacion->setElementoId($idElemento);
                    $entityRelacion->setDocumentoId($entity->getId());

                    $this->emComunicacion->persist($entityRelacion);
                    $this->emComunicacion->flush();
 
                } 
            }
            if ($this->emComunicacion->getConnection()->isTransactionActive()){
                $this->emComunicacion->getConnection()->commit();
            }                
            $this->emComunicacion->getConnection()->close();  
            return true;
       }
       catch(\Exception $e)
       {
           //Eliminar Archivos subidos
           foreach($arrayRutasArchivosSubidos as $rutaEliminar)
           {
               unlink($rutaEliminar);
           }
           
           if ($this->emComunicacion->getConnection()->isTransactionActive())
           {
               $this->emComunicacion->getConnection()->rollback();
           }                            
           $this->emComunicacion->getConnection()->close();  
           return false;
       }        
    }
}
