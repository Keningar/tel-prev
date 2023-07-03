<?php

namespace telconet\comercialBundle\Service;

use Symfony\Component\HttpFoundation\Response;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolMaterial;
use telconet\schemaBundle\Entity\InfoServicioHistorial;

class AutorizacionesService {
    
    private $emComercial;
    private $serviceUtil;
    private $serviceEnvioPlantilla;
    private $strPathTelcos;
    private $serviceTemplating;
    private $serviceKnpSnappyPdf;
    private $emGeneral;

     
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emComercial              = $container->get('doctrine.orm.telconet_entity_manager');
        $this->serviceEnvioPlantilla    = $container->get('soporte.EnvioPlantilla'); 
        $this->serviceUtil              = $container->get('schema.Util');
        $this->serviceTemplating        = $container->get('templating');
        $this->serviceKnpSnappyPdf      = $container->get('knp_snappy.pdf');
        $this->strPathTelcos            = $container->getParameter('path_telcos');
        $this->emGeneral                = $container->get('doctrine.orm.telconet_general_entity_manager');   
        
    }
    
    /**
     * Función que sirve para realizar la llamada a la correspondiente plantilla de envío de mail con los parámetros enviados al realizar
     * las autorizaciones
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-09-2016
     * 
     * @param array $arrayParametros 
     */
    public function envioMailAutorizaciones($arrayParametros)
    {
        try
        {
            /*Se obtienen los parámetros necesarios para generar el archivo que se adjuntará al correo*/
            $strTokenIdentificador          = date("Y-m-d_G.i.s_") . substr(md5(uniqid(rand())),0,6);
            $arrayParametrosAdjunto         = array(
                                                    "nombreTipoAutorizacionAdjunto" => $arrayParametros["nombreTipoAutorizacionAdjunto"],
                                                    "usrCreacion"                   => $arrayParametros["usrCreacion"],
                                                    "ipClient"                      => $arrayParametros["ipClient"],
                                                    "tituloAdjunto"                 => $arrayParametros["tituloAdjunto"],
                                                    "strTokenIdentificador"         => $strTokenIdentificador,
                                                    "arrayNombresCabeceraAdjunto"   => $arrayParametros["arrayNombresCabeceraAdjunto"],
                                                    "arrayDataAdjunto"              => $arrayParametros["arrayDataAdjunto"],
                                                    "prefijoEmpresaSession"         => $arrayParametros["prefijoEmpresaSession"]
            );
            $strUbicacionAdjunto    = $this->generarAdjuntoPDFAutorizacionNotificacion($arrayParametrosAdjunto);

            /*
             * Envío de Correo por las autorizaciones
             * Se obtienen los parámetros necesarios para el envío de la plantilla, incluyendo la ruta del archivo adjunto
             */
            $strAsunto          = $arrayParametros["asunto"];
            $strTo              = array();
            $arrayParamPlantilla= array(
                                        "tipoAutorizacion"  => $arrayParametros["tipoAutorizacion"],
                                        "tipoGestion"       => $arrayParametros["tipoGestion"],
                                        "empleado"          => $arrayParametros["empleadoSession"],
                                        "prefijoEmpresa"    => $arrayParametros["prefijoEmpresaSession"],
                                        "motivoGestion"     => $arrayParametros["motivoGestion"] ? $arrayParametros["motivoGestion"] : "",
                                        "observacionGestion"=> $arrayParametros["observacionGestion"] ? $arrayParametros["observacionGestion"] : ""
                                  );
            $this->serviceEnvioPlantilla->generarEnvioPlantilla(    $strAsunto, 
                                                                    $strTo, 
                                                                    $arrayParametros["codigoPlantilla"], 
                                                                    $arrayParamPlantilla, 
                                                                    $arrayParametros["idEmpresaSession"], 
                                                                    '', 
                                                                    '',
                                                                    $strUbicacionAdjunto,
                                                                    false,
                                                                    'notificaciones_telcos@telconet.ec');
            
            unlink($strUbicacionAdjunto);
        }
        catch (\Exception $ex) 
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'envioMailAutorizaciones', 
                                            $ex->getMessage(), 
                                            $arrayParametros["usrCreacion"], 
                                            $arrayParametros['ipClient']
                                           );
        }
    }
    
    
    /**
     * Función que sirve para generar e adjunto en formato PDF de las autorizaciones aprobadas de acuerdo a los parámetros enviados
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 19-09-2016
     * @param $arrayParametros[
     *                          "idEmpresaSession"              : id de la empresa en session
     *                          "prefijoEmpresaSession"         : prefijo de la empresa en session
     *                          "codigoPlantilla"               : código de la plantilla a utilizarse para obtener los alias a los 
     *                                                            que se les enviará el correo
     *                          "usrCreacion"                   : usuario que realiza la autorización
     *                          "ipClient"                      : ip cliente 
     *                          "arrayNombresCabeceraAdjunto"   : array con los nombres de las columnas de la tabla que se generará
     *                          "arrayDataAdjunto"              : array con la data de las autorizaciones 
     *                         ]
     * 
     */
    public function generarAdjuntoPDFAutorizacionNotificacion($arrayParametros)
    {
        $strUbicacionAdjunto            = '';
        try
        {
            $strPrefijoEmpresaSession   = $arrayParametros["prefijoEmpresaSession"];

            $strRutaLogoEmpresa = "";
            if($strPrefijoEmpresaSession == 'TN')
            {
                $strRutaLogoEmpresa = $this->strPathTelcos.'/telcos/web'.'/public/images/logo_telconet.jpg';
            }
            else if($strPrefijoEmpresaSession == 'MD')
            {
                $strRutaLogoEmpresa = $this->strPathTelcos.'/telcos/web'.'/public/images/logo_netlife_big.jpg';
            }
            else if($strPrefijoEmpresaSession == 'TTCO')
            {
                $strRutaLogoEmpresa = $this->strPathTelcos.'/telcos/web'.'/public/images/logo_transtelco_new.jpg';
            }
            $strDirDocumentos       = $this->strPathTelcos.'/telcos/web'.'/public/uploads/documentos/';
            $strUbicacionAdjunto    = $strDirDocumentos.$arrayParametros["nombreTipoAutorizacionAdjunto"]
                                      .'_'.$arrayParametros["strTokenIdentificador"].'.pdf';

            $arrayParametros = array(
                "strRutaLogoEmpresa"            => $strRutaLogoEmpresa,
                "strTituloAdjunto"              => $arrayParametros["tituloAdjunto"],
                "arrayNombresCabeceraAdjunto"   => $arrayParametros["arrayNombresCabeceraAdjunto"],
                "arrayDataAdjunto"              => $arrayParametros["arrayDataAdjunto"],
                "numColumnasTabla"              => count($arrayParametros["arrayNombresCabeceraAdjunto"])
            );

            $strHtmlPdf = $this->serviceTemplating->render( 'comercialBundle:Default:adjuntoPDFNotificacionAutorizaciones.html.twig', 
                                                            $arrayParametros);
            
            
            $this->serviceKnpSnappyPdf->generateFromHtml($strHtmlPdf, $strUbicacionAdjunto);
            
        } 
        catch (\Exception $ex) 
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'generarAdjuntoPDFAutorizacionNotificacion', 
                                            $ex->getMessage(), 
                                            $arrayParametros["usrCreacion"], 
                                            $arrayParametros['ipClient']
                                           );
        }
        return $strUbicacionAdjunto;
    }


    /**
     * Función para Aprobar automaticamente la OT y sol. de Excedentes.
     *          Obtiene $La ipCreación($strClienteIp, Los datos del servicio($objServicio).
     *          Una descripción del seguimiento ($strSeguimiento)
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     * 
     */

    public function registroEstadoAprobadoInfoDetalleSolicitud($arrayParametros)
    {
        $emCom                  = $arrayParametros['emComercial'];
        $objServicio            = $arrayParametros['objServicio'];
        $strEstadoEnviado       = $arrayParametros['strEstadoEnviado'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        try
        {
            //SOLICITUD MATERIALES EXCEDENTES
            $objTipoSolExcMaterial = $emCom->getRepository("schemaBundle:AdmiTipoSolicitud")
                                            ->findByDescripcionSolicitud('SOLICITUD MATERIALES EXCEDENTES');

            /* Consulta la solicitud de excedente de material*/
            $objDetalleSolicitudExc = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                            ->findUlitmoDetalleSolicitudByIds( $objServicio->getId(),
                                                $objTipoSolExcMaterial[0]->getId());

            $intIdInfoDetalleSolicitud        = $objDetalleSolicitudExc->getId();
            
           if(is_object($objDetalleSolicitudExc))
           {
                //La solicitud de excedentes se aprueba, se debe cambiar automaticamente a Aprobada la OT (InfoDetalleSolicitud)            
                $entitySolicitud = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdInfoDetalleSolicitud);
                $entitySolicitud->setEstado($strEstadoEnviado); //aprobado
                $entitySolicitud->setUsrCreacion($strUsrCreacion);
                $entitySolicitud->setFeCreacion(new \DateTime('now'));

                $emCom->persist($entitySolicitud);
                $emCom->flush();
           }
           
            $strStatus = "OK";
            $strRespuesta = "Procesado con éxito registroEstadoAprobadoInfoDetalleSolicitud";
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'registroEstadoAprobadoInfoDetalleSolicitud', 
                                            $e->getMessage(), 
                                            $arrayParametros["usrCreacion"], 
                                            $arrayParametros['ipClient']
                                            );
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }

    /**
     * Función para Anular/Rechazar automaticamente la OT y sol. de Excedentes.
     *          Obtiene $La ipCreación($strClienteIp, Los datos del servicio($objServicio).
     *          Una descripción del seguimiento ($strSeguimiento)
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     * 
     */
    public function registroEstadoRechazoInfoDetalleSolicitud($arrayParametros)
    {
        $emCom            = $arrayParametros['emComercial'];
        $objServicio      = $arrayParametros['objServicio'];
        $strEstadoEnviado = $arrayParametros['strEstadoEnviado'];
        $strUsrCreacion   = $arrayParametros['strUsrCreacion'];
        try
        {
            //SOLICITUD MATERIALES EXCEDENTES
            $objTipoSolExcMaterial = $emCom->getRepository("schemaBundle:AdmiTipoSolicitud")
            ->findByDescripcionSolicitud('SOLICITUD MATERIALES EXCEDENTES');

            /* Consulta la solicitud de excedente de material*/
            $objDetalleSolicitudExc = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                    ->findUlitmoDetalleSolicitudByIds( $objServicio->getId(),
                                                        $objTipoSolExcMaterial[0]->getId());

            $intIdInfoDetalleSolicitud        = $objDetalleSolicitudExc->getId();
            
           if(is_object($objDetalleSolicitudExc))
           {
            $entitySolExcedente = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdInfoDetalleSolicitud);
            $entitySolExcedente->setEstado($strEstadoEnviado); //Rechazada
            $entitySolExcedente->setUsrRechazo($strUsrCreacion);
            $entitySolExcedente->setFeRechazo(new \DateTime('now'));
            $emCom->persist($entitySolExcedente);
            $emCom->flush();
           }

            $entityTipoSolicitudPla = $emCom->getRepository('schemaBundle:AdmiTipoSolicitud') 
                                   ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

            // SOLICITUD DE PLANIFICACION del servicio
            $objDetalleSolicitudPla = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud') 
                                    ->findOneBy(array("servicioId"      => $objServicio->getId(),
                                                      "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));
           
            if(is_object($objDetalleSolicitudPla))
            {
                $intIdInfoDetalleSolPlanificacion        = $objDetalleSolicitudPla->getId();
                $entitySolPlanificacion = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdInfoDetalleSolPlanificacion);
                $entitySolPlanificacion->setEstado($strEstadoEnviado); //Rechazada
                $entitySolPlanificacion->setFeCreacion(new \DateTime('now'));
                $emCom->persist($entitySolPlanificacion);
                $emCom->flush();  
            }

            // Eliminar las caracteristicas del servicio al rechazar GTN
            $arrayServProdCaractServicioPunto   = $emCom->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findBy(array("servicioId" => $objServicio->getId(), 
                                                                                    "estado"     => "Activo"));
            for($intIndexServProdCaract=0; $intIndexServProdCaract<count($arrayServProdCaractServicioPunto); $intIndexServProdCaract++)
            {
                $objServProdCaractPunto = $arrayServProdCaractServicioPunto[$intIndexServProdCaract];
                $objServProdCaractPunto->setEstado("Eliminado");
                $emCom->persist($objServProdCaractPunto);
                $emCom->flush();
            }

            // Actualiza el estado Anulado al servicio
            $entityInfoServicio = $emCom->getRepository('schemaBundle:InfoServicio')->find($objServicio->getId());
            $entityInfoServicio->setEstado('Anulado');
            $emCom->persist($entityInfoServicio);
            $emCom->flush();
            $strStatus = "OK";
            $strRespuesta = "Procesado con éxito registroEstadoRechazoInfoDetalleSolicitud";
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'registroEstadoRechazoInfoDetalleSolicitud', 
                                            $e->getMessage(), 
                                            $arrayParametros["usrCreacion"], 
                                            $arrayParametros['ipClient']
                                           );
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
                                    
            $emCom->getConnection()->rollback();
            $this->serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $strRespuesta, 
                                        $strUsrCreacion
                                    );
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }

    
    /**
     * Función para cambiar automaticamente a PrePlanificada la OT con fecha actual (InfoDetalleSolicitud).
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     */
    public function registroEstadoPrePlanificadaInfoDetalleSolicitud($arrayParametros)
    {
        $emCom                  = $arrayParametros['emComercial'];
        $strEstadoEnviado       = $arrayParametros['strEstadoEnviado'];
        $objServicio            = $arrayParametros['objServicio'];
        $strClienteIp           = $arrayParametros['strClienteIp'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $objRespuesta           = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
            $entityTipoSolicitudPla = $emCom->getRepository('schemaBundle:AdmiTipoSolicitud') 
                                   ->findOneByDescripcionSolicitud("SOLICITUD PLANIFICACION");

            // Datos de la SOLICITUD DE PLANIFICACION del servicio
            $objDetalleSolicitudPla = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud') 
                                    ->findOneBy(array("servicioId"      => $objServicio->getId(),
                                                      "tipoSolicitudId" => $entityTipoSolicitudPla->getId()));

            $objParametroDetValEstados =   $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                    ->getOne("ESTADO_EXCEDENTES",
                            "COMERCIAL", "", 
                            "CONDICIONAR LA PREPLANIFICACION POR EL ESTADO DEL SERVICIO - EXCEDENTES",
                            "", "", "", "", "", 10
                        );
            if (($objParametroDetValEstados)) 
            {
                $strValorDetenido           = $objParametroDetValEstados['valor1'];
                $strValorPrePlanificada     = $objParametroDetValEstados['valor2'];
                $strValorReplanificada      = $objParametroDetValEstados['valor3'];
            
                if(is_object($objDetalleSolicitudPla))
                {
                    $strEstadoServicio   = $objServicio->getEstado();

                    if (($strEstadoServicio == $strValorDetenido ) || ($strEstadoServicio == $strValorReplanificada) 
                        || ($strEstadoServicio == $strValorPrePlanificada))
                    {
                        $strObservacion                 = 'Se actualiza la OT con fecha actual';
                        $strEstadoEnviadoHtrServ        = $strEstadoEnviado;
                        //Actualizo InfoServicio solo cuando no està en estos estados
                        $entityInfoServicio = $emCom->getRepository('schemaBundle:InfoServicio')->find($objServicio->getId());
                        $entityInfoServicio->setEstado($strEstadoEnviado);
                        $emCom->persist($entityInfoServicio);
                        $emCom->flush();
                    }
                    else
                    {
                        $strEstadoEnviadoHtrServ        = $strEstadoServicio;
                        $strObservacion                 = 'Se actualiza el estado de la OT a '.$strEstadoEnviado.' con fecha actual';
                    }

                    // Actualizo InfoDetalleSolicitud
                    $entitySolPlanificacion = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($objDetalleSolicitudPla->getId());
                    $entitySolPlanificacion->setEstado($strEstadoEnviado);
                    $entitySolPlanificacion->setFeCreacion(new \DateTime('now'));
                    $emCom->persist($entitySolPlanificacion);
                    $emCom->flush();

                    //CREO InfoDetalleSolHist
                    $entityDetSolHistM = new InfoDetalleSolHist();
                    $entityDetSolHistM->setDetalleSolicitudId($objDetalleSolicitudPla);
                    $entityDetSolHistM->setObservacion('<b>Seguimiento:</b> '.$strObservacion);
                    $entityDetSolHistM->setIpCreacion($strClienteIp);
                    $entityDetSolHistM->setFeCreacion(new \DateTime('now'));
                    $entityDetSolHistM->setUsrCreacion($strUsrCreacion);
                    $entityDetSolHistM->setEstado($strEstadoEnviado);
                    $emCom->persist($entityDetSolHistM);
                    $emCom->flush();

                    // CREO InfoServicioHistorial
                    $entityServicioHistorial = new InfoServicioHistorial();
                    $entityServicioHistorial->setServicioId($objServicio);
                    $entityServicioHistorial->setObservacion('<b>Seguimiento:</b> '.$strObservacion);
                    $entityServicioHistorial->setIpCreacion($strClienteIp);
                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $entityServicioHistorial->setEstado($strEstadoEnviadoHtrServ);                
                    $emCom->persist($entityServicioHistorial);
                    $emCom->flush();

                    $strStatus = "OK";
                    $strRespuesta = "Procesado con éxito registroEstadoPrePlanificadaInfoDetalleSolicitud";
                }
            }
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
                                    
            $emCom->getConnection()->rollback();
            $this->serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $strRespuesta, 
                                        $strUsrCreacion, 
                                        $strClienteIp
                                    );
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }


     /**
     * Función para registrar la INFO_DETALLE_SOL_MATERIAL re registra el material utilizado y la solicitud.
     *          INSERTAR INFO DETALLE SOLICITUD MATERIAL - INFO_DETALLE_SOL_MATERIAL
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 16-11-2021
     */
    public function registroSolicitudMaterial($arrayParametros)
    {
        $emCom                      = $arrayParametros['emComercial'];
        $strClienteIp               = $arrayParametros['strClienteIp'];
        $intIdDetalleSolicitud      = $arrayParametros['intIdDetalleSolicitud'];
        $strUsrCreacion             = $arrayParametros['strUsrCreacion'];
        $strCodigoMaterial          = $arrayParametros['strCodigoMaterial'];
        $strCostoMaterial           = $arrayParametros['strCostoMaterial'];
        $strPrecioVentaMaterial     = $arrayParametros['strPrecioVentaMaterial'];
        $intCantidadEstimada        = $arrayParametros['intCantidadEstimada'];
        $intCantidadCliente         = $arrayParametros['intCantidadCliente'];
        $intCantidadUsada           = $arrayParametros['intCantidadUsada'];
        $intCantidadFacturada       = $arrayParametros['intCantidadFacturada'];
        $strValorCobrado            = $arrayParametros['strValorCobrado'];
        $objRespuesta               = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
            $objDetalleSolicitud =  $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                              ->findOneById($intIdDetalleSolicitud);

            $entityDetalleSolMaterial = $emCom->getRepository('schemaBundle:InfoDetalleSolMaterial')
            ->findOneBy(array("detalleSolicitudId" => $objDetalleSolicitud->getId()));
            if (is_object($entityDetalleSolMaterial))
            {
                //ACTUALIZO LOS DATOS EN INFO DETALLE SOLICITUD MATERIAL
                $entityDetalleSolMaterial->setCostoMaterial($strCostoMaterial);
                $entityDetalleSolMaterial->setPrecioVentaMaterial($strPrecioVentaMaterial);
                $entityDetalleSolMaterial->setCantidadEstimada($intCantidadEstimada);
                $entityDetalleSolMaterial->setCantidadCliente($intCantidadCliente);
                $entityDetalleSolMaterial->setValorCobrado($strValorCobrado);
                $entityDetalleSolMaterial->setUsrCreacion($strUsrCreacion);
                $entityDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolMaterial->setIpCreacion($strClienteIp);
                $entityDetalleSolMaterial->setCantidadUsada($intCantidadUsada);
                $entityDetalleSolMaterial->setCantidadFacturada($intCantidadFacturada);
            }
            else
            {

                //SI ES QUE NO HAY ENVÍO NUEVOS VALORES EN INFO DETALLE SOLICITUD MATERIAL
                $entityDetalleSolMaterial = new InfoDetalleSolMaterial();//guardo los valores usados
                $entityDetalleSolMaterial->setDetalleSolicitudId($objDetalleSolicitud);
                $entityDetalleSolMaterial->setMaterialCod($strCodigoMaterial);
                $entityDetalleSolMaterial->setCostoMaterial($strCostoMaterial);
                $entityDetalleSolMaterial->setPrecioVentaMaterial($strPrecioVentaMaterial);
                $entityDetalleSolMaterial->setCantidadEstimada($intCantidadEstimada);
                $entityDetalleSolMaterial->setCantidadCliente($intCantidadCliente);
                $entityDetalleSolMaterial->setValorCobrado($strValorCobrado);
                $entityDetalleSolMaterial->setUsrCreacion($strUsrCreacion);
                $entityDetalleSolMaterial->setFeCreacion(new \DateTime('now'));
                $entityDetalleSolMaterial->setIpCreacion($strClienteIp);
                $entityDetalleSolMaterial->setCantidadUsada($intCantidadUsada);
                $entityDetalleSolMaterial->setCantidadFacturada($intCantidadFacturada);
            }
            if (is_object($entityDetalleSolMaterial))
            {
                $emCom->persist($entityDetalleSolMaterial);
                $emCom->flush();
                $strStatus = "OK";
                $strRespuesta   = "Procesado con éxito registroSolicitudMaterial";
            }
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);

            $emCom->getConnection()->rollback();
            $this->serviceUtil->insertError(
                                            'Telcos+',
                                            'AutorizacionesService->registroSolicitudMaterial',
                                            $strRespuesta,
                                            $strUsrCreacion,
                                            $strClienteIp
                                    );
            return $arrayRespuesta; 
        }
        
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }



    /**
     * Función para anular una solicitud de autorizacion de excedentes.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 27-01-2022
     * 
     */
    public function anulacionSolicitudDeExcedenteMateriales($arrayParametros)
    {        
        $emCom                  = $arrayParametros['emComercial'];
        $strClienteIp           = $arrayParametros['strClienteIp'];
        $intIdSolicitudExcedente= $arrayParametros['intIdSolicitudExcedente'];
        $strSeguimiento         = $arrayParametros['strSeguimiento'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strEstadoEnviado       = $arrayParametros['strEstadoEnviado'];
        $objRespuesta           = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
           $entitySolicitud = $emCom->getRepository('schemaBundle:InfoDetalleSolicitud')
                                          ->findOneById($intIdSolicitudExcedente);

           $entitySolicitud->setEstado($strEstadoEnviado);	
           $entitySolicitud->setObservacion($strSeguimiento);
           $entitySolicitud->setUsrCreacion($strUsrCreacion);		
           $entitySolicitud->setFeCreacion(new \DateTime('now'));
           $emCom->persist($entitySolicitud);
           $emCom->flush(); 

            //CREO LA INFO DETALLE SOLICITUD HISTORIAL DE MATERIALES EXCEDENTES
            $entityDetSolHistM = new InfoDetalleSolHist();
            $entityDetSolHistM->setDetalleSolicitudId($entitySolicitud);
            $entityDetSolHistM->setObservacion($strSeguimiento);
            $entityDetSolHistM->setIpCreacion($strClienteIp);
            $entityDetSolHistM->setFeCreacion(new \DateTime('now'));
            $entityDetSolHistM->setUsrCreacion($strUsrCreacion);
            $entityDetSolHistM->setEstado($strEstadoEnviado);            
            $emCom->persist($entityDetSolHistM);
            $emCom->flush();

            $strStatus      = "OK";
            $strRespuesta   = "Procesado con éxito anulacionSolicitudDeExcedenteMateriales";
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
            
            $emCom->getConnection()->rollback();
            $this->serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $strRespuesta, 
                                        $strUsrCreacion, 
                                        $strClienteIp
                                    );
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }


     /**
      * 
     * Función registrar la trazabilidad de la OT.  Obtiene La $ipCreación($strClienteIp, Los datos del servicio($objServicio)
     *          INSERTAR INFO SERVICIO HISTORIAL      - InfoServicioHistorial
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     * 
     * Se condiciona el estado enviado al momento que el servicio no està en estado DETENIDO.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.1 27-12-2022
     * 
     */
    public function registroTrazabilidadDelServicio($arrayParametros)    
    {
        $emCom                  = $arrayParametros['emComercial'];
        $strClienteIp           = $arrayParametros['strClienteIp'];
        $objServicio            = $arrayParametros['objServicio'];
        $strSeguimiento         = $arrayParametros['strSeguimiento'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strAccion              = $arrayParametros['strAccion'];
        $strEstadoEnviado       = $arrayParametros['strEstadoEnviado'];
        $objRespuesta           = new Response();
        $strEstadoActual        = $objServicio->getEstado();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
            $entityServicioHistorial = new InfoServicioHistorial();
            $entityServicioHistorial->setServicioId($objServicio);
            $entityServicioHistorial->setObservacion('<b>Seguimiento:</b> '.$strSeguimiento);
            $entityServicioHistorial->setIpCreacion($strClienteIp);
            $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
            $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
            if($strEstadoActual=='Detenido')
            {
                $entityServicioHistorial->setEstado($strEstadoEnviado);                                
            }
            else
            {
                $entityServicioHistorial->setEstado($strEstadoActual);
            }

            $entityServicioHistorial->setAccion($strAccion);
            
            $emCom->persist($entityServicioHistorial);
            $emCom->flush();
            $strStatus = "OK";
            $strRespuesta = "Procesado con éxito registroTrazabilidadDelServicio";
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
                                    
            $emCom->getConnection()->rollback();
            $this->serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $strRespuesta, 
                                        $strUsrCreacion, 
                                        $strClienteIp
                                    );
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }

    /**
     * 
     * Función para registrar la trazabilidad de la OT.
     * @author Liseth Candelario <lcandelario@telconet.ec>
     * @version 1.0 22-10-2021
     * 
     */
    public function registroTrazabilidadDeLaSolicitud($arrayParametros)
    {
        $emCom                  = $arrayParametros['emComercial'];
        $strClienteIp           = $arrayParametros['strClienteIp'];
        $objDetalleSolicitudExc = $arrayParametros['objDetalleSolicitudExc'];
        $strObservacion         = $arrayParametros['strObservacion'];
        $strUsrCreacion         = $arrayParametros['strUsrCreacion'];
        $strEstadoEnviado       = $arrayParametros['strEstadoEnviado'];
        $objRespuesta           = new Response();
        $objRespuesta->headers->set('Content-Type', 'text/json');
        try
        {
            if(is_object($objDetalleSolicitudExc))
            {
                //CREO LA INFO DETALLE SOLICICITUD HISTORIAL DE MATERIALES EXCEDENTES
                $entityDetSolHistM = new InfoDetalleSolHist();
                $entityDetSolHistM->setDetalleSolicitudId($objDetalleSolicitudExc);
                $entityDetSolHistM->setObservacion('<b>Seguimiento:</b> '.$strObservacion);
                $entityDetSolHistM->setIpCreacion($strClienteIp);
                $entityDetSolHistM->setFeCreacion(new \DateTime('now'));
                $entityDetSolHistM->setUsrCreacion($strUsrCreacion);
                $entityDetSolHistM->setEstado($strEstadoEnviado);
                $emCom->persist($entityDetSolHistM);
                $emCom->flush();
                $strStatus = "OK";
                $strRespuesta = "Procesado con éxito registroTrazabilidadDeLaSolicitud";
            }
            else
            {
                throw new \Exception(': No se envía información de la solicitud');
            }
        }
        catch (\Exception $e)
        {
            $strStatus    = "ERROR";
            $strRespuesta = $e->getMessage();
            $arrayRespuesta = array("status"                => $strStatus,
                                    "mensaje"               => $strRespuesta);
                                    
            $emCom->getConnection()->rollback();
            $this->serviceUtil->insertError('Telcos+', 
                                        'CoordinarController->validadorExcedenteMaterialAction', 
                                        $strRespuesta, 
                                        $strUsrCreacion, 
                                        $strClienteIp
                                    );
            return $arrayRespuesta; 
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strRespuesta);
        return $arrayRespuesta;
    }

}
