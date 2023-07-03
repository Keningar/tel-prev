<?php

namespace telconet\comercialBundle\Service;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;

/**
 * SolicitudCambioDocumentoService
 * @author Luis Cabrera <lcabrera@telconet.ec>
 * 31-07-2017 - Service que permite realizar la lógica de las solicitudes de cambio de documento.
 */
class SolicitudCambioDocumentoService
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emcom;
    private $serviceUtil;
    /* @var $serviceServicioHistorial \telconet\comercialBundle\Service\InfoServicioHistorialService */
    private $serviceServicioHistorial;
    /* @var $serviceAutorizaciones \telconet\comercialBundle\Service\Autorizaciones */
    private $serviceAutorizaciones;

    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->emcom                    = $container->get('doctrine.orm.telconet_entity_manager');
        $this->serviceUtil              = $container->get('schema.Util');
        $this->serviceServicioHistorial = $container->get('comercial.InfoServicioHistorial');
        $this->serviceAutorizaciones    = $container->get('comercial.Autorizaciones');
        $this->serviceEnvioPlantilla    = $container->get('soporte.EnvioPlantilla'); 
    }

    /**
     * Documentación de aprobarCambioDocumento
     * @author Luis Cabrera <lcabrera@telconet.ec>
     * @version 1.0 - 31-07-2017 - Permite aprobar una solicitud de Cambio de Documento.
     * 
     * @author Douglas Natha <dnatha@telconet.ec>
     * @version 1.1 - 11-12-2019 - Se agrega el campo observación en el historial del servicio.
     * 
     * @author Kevin Baque Puya <kbaque@telconet.ec>
     * @version 1.2 11-10-2022 - Se envía notificación a la asistente, vendedor y subgerente.
     *
     * @param array $arrayParametros Contiene los valores necesarios.
     * @return array $arrayRespuesta "strMensaje"     -> Mensaje del proceso
     *                               "intRespuesta"   -> Código de Respuesta: 0: error, 1: éxito.
     */
    public function aprobarCambioDocumento($arrayParametros)
    {
        $arrayRespuesta = array(
            "strMensaje"   => "",
            "intRespuesta" => 0
        );
        $strCodEmpresa                 = $arrayParametros['strCodEmpresa'];
        $strPrefijoEmpresa             = $arrayParametros['strPrefijoEmpresa'];
        $strIpClient                   = $arrayParametros['strIpClient'];
        $strEmpleado                   = $arrayParametros['empleado'];
        $strUsrCreacion                = $arrayParametros['strUsrCreacion'];
        $strParametro                  = $arrayParametros['param'];
        $strObservacion                = ( isset($arrayParametros['obs']) && !empty($arrayParametros['obs']) ) ? $arrayParametros['obs'] : '';
        $arrayIdsSolicitudes           = explode("|", $strParametro);
        $strTipoDoc                    = $arrayParametros['tipoDoc'];
        $arrayTipodoc                  = explode("|", $strTipoDoc);
        $serviceServicioHistorialLocal = $this->serviceServicioHistorial;
        $arrayData                     = array();
        $this->emcom->getConnection()->beginTransaction();
        try
        {
            for($intI = 0; $intI < count($arrayIdsSolicitudes); $intI++)
            {
                $strCliente      = '';
                $strLogin        = '';
                $strProductoPlan = '';
                $strMotivo       = '';
                $strValor        = '';
                $objDetalleSol   = $this->emcom->getRepository('schemaBundle:InfoDetalleSolicitud')->find($arrayIdsSolicitudes[$intI]);
                if(!$objDetalleSol)
                {
                    throw $this->createNotFoundException('No se encontró la solicitud buscada');
                }
                $objDetalleSol->setEstado('Aprobado');
                $this->emcom->persist($objDetalleSol);
                $this->emcom->flush();
                //Grabamos en la tabla de historial de la solicitud
                $objDetalleSolHistorial = new InfoDetalleSolHist();
                $objDetalleSolHistorial->setEstado('Aprobado');
                $objDetalleSolHistorial->setDetalleSolicitudId($objDetalleSol);
                $objDetalleSolHistorial->setUsrCreacion($strUsrCreacion);
                $objDetalleSolHistorial->setFeCreacion(new \DateTime('now'));
                $objDetalleSolHistorial->setObservacion($strObservacion);
                $objDetalleSolHistorial->setIpCreacion($strIpClient);
                $this->emcom->persist($objDetalleSolHistorial);
                $this->emcom->flush();
                //CAMBIA CAMPO ES_VENTA EN EL SERVICIO
                $objServicio = $objDetalleSol->getServicioId();
                $strTipodoc  = "";
                /* Se obtiene la información necesaria para el archivo que se enviará como adjunto del correo */
                if($objServicio)
                {
                    $objProducto = $objServicio->getProductoId();
                    $objPlan     = $objServicio->getPlanId();
                    $objPunto    = $objServicio->getPuntoId();
                    $strValor    = $objServicio->getPrecioVenta();
                    if($objProducto)
                    {
                        $strProductoPlan = $objProducto->getDescripcionProducto();
                    }
                    else
                    {
                        if($objPlan)
                        {
                            $strProductoPlan = $objPlan->getNombrePlan();
                        }
                    }

                    if($objPunto)
                    {
                        $objPersonaEmpresaRol = $objPunto->getPersonaEmpresaRolId();
                        if($objPersonaEmpresaRol)
                        {
                            $objPersona = $objPersonaEmpresaRol->getPersonaId();
                            if($objPersona)
                            {
                                $strCliente = sprintf('%s', $objPersona);
                            }
                        }
                        $strLogin = $objPunto->getLogin();
                    }

                    if(strtoupper($arrayTipodoc[$intI]) == "CORTESIA" || strtoupper($arrayTipodoc[$intI]) == 'C')
                    {
                        $objServicio->setEsVenta('N');
                        $strTipodoc = "Cortesía";
                    }
                    elseif(strtoupper($arrayTipodoc[$intI]) == "DEMO" || strtoupper($arrayTipodoc[$intI]) == 'D')
                    {
                        $objServicio->setEsVenta('N');
                        $strTipodoc = "Demo";
                    }
                    elseif(strtoupper($arrayTipodoc[$intI]) == "VENTA" || strtoupper($arrayTipodoc[$intI]) == 'V')
                    {
                        $objServicio->setEsVenta('S');
                        $strTipodoc = "Venta";
                    }
                    else
                    {
                        throw $this->createNotFoundException('No se encontró el tipo de documento deseado.');
                    }

                    $this->emcom->persist($objServicio);
                    $this->emcom->flush();
                    //SE ALMACENA LA INFORMACION EN EL HISTORIAL DEL SERVICIO.
                    $arrayParametros      = array(
                        'objServicio'     => $objServicio,
                        'strIpClient'     => $strIpClient,
                        'strUsrCreacion'  => $strUsrCreacion,
                        'strObservacion'  => $strObservacion . " - Cambio de Documento a: ".$strTipodoc,
                        'strAccion'       => 'cambioDocumento'
                    );
                    $objServicioHistorial = $serviceServicioHistorialLocal->crearHistorialServicio($arrayParametros);
                    $this->emcom->persist($objServicioHistorial);
                    $this->emcom->flush();
                }
                else
                {
                    throw $this->createNotFoundException('No se encontró el servicio asociado a la solicitud buscada');
                }
                if($objDetalleSol->getMotivoId())
                {
                    $objMotivo = $this->emcom->getRepository('schemaBundle:AdmiMotivo')->find($objDetalleSol->getMotivoId());
                    if($objMotivo)
                    {
                        $strMotivo = $objMotivo->getNombreMotivo();
                    }
                }

                $arrayData[] = array(
                    "servicio"        => $strProductoPlan,
                    "login"           => $strLogin,
                    "cliente"         => $strCliente,
                    "motivo"          => $strMotivo,
                    "valor"           => $strValor,
                    "tipo_doc"        => $strTipodoc,
                    "observacion"     => $objDetalleSol->getObservacion(),
                    "fechaCreacion"   => strval(date_format($objDetalleSol->getFeCreacion(), "d/m/Y G:i")),
                    "usuarioCreacion" => $objDetalleSol->getUsrCreacion()
                );
                if($strPrefijoEmpresa == "TN")
                {
                    $arrayDestinatarios = array();
                    $strVendedor        = (is_object($objPunto)) ? $objPunto->getUsrVendedor():"";
                    $strCliente         = "";
                    $strIdentificacion  = (is_object($objPersona)) ? $objPersona->getIdentificacionCliente():"";
                    $strCliente         = (is_object($objPersona) && $objPersona->getRazonSocial()) ? 
                                           $objPersona->getRazonSocial(): $objPersona->getNombres() . " " .$objPersona->getApellidos();
                    //Correo del vendedor.
                    $arrayCorreos = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                         ->getContactosByLoginPersonaAndFormaContacto($strVendedor,
                                                                                      "Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    //Correo del subgerente
                    $arrayResultadoCorreo    = $this->emcom->getRepository('schemaBundle:InfoPersonaEmpresaRol')
                                                    ->getSubgerentePorLoginVendedor(array("strLogin"=>$strVendedor));
                    if(!empty($arrayResultadoCorreo["registros"]) && isset($arrayResultadoCorreo["registros"]))
                    {
                        $arrayRegistrosCorreo = $arrayResultadoCorreo['registros'];
                        $arrayCorreos         = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                                     ->getContactosByLoginPersonaAndFormaContacto($arrayRegistrosCorreo[0]["LOGIN_SUBGERENTE"],
                                                                                                  "Correo Electronico");
                        if(!empty($arrayCorreos) && is_array($arrayCorreos))
                        {
                            foreach($arrayCorreos as $arrayItem)
                            {
                                if( !empty($arrayItem['valor']) && isset($arrayItem['valor']) )
                                {
                                    $arrayDestinatarios[] = $arrayItem['valor'];
                                }
                            }
                        }
                    }
                    //Correo de la persona quien crea la solicitud.
                    $arrayCorreos = $this->emcom->getRepository('schemaBundle:InfoPersona')
                                         ->getContactosByLoginPersonaAndFormaContacto($strUsrCreacion,"Correo Electronico");
                    if(!empty($arrayCorreos) && is_array($arrayCorreos))
                    {
                        foreach($arrayCorreos as $arrayItem)
                        {
                            if(!empty($arrayItem['valor']) && isset($arrayItem['valor']))
                            {
                                $arrayDestinatarios[] = $arrayItem['valor'];
                            }
                        }
                    }
                    $strCuerpoCorreo      = "El presente correo es para indicarle que se aprobó una solicitud en TelcoS+ con los siguientes datos:";
                    $arrayParametrosMail  = array("strNombreCliente"         => $strCliente,
                                                  "strIdentificacionCliente" => $strIdentificacion,
                                                  "strObservacion"           => $objDetalleSol->getObservacion(),
                                                  "strCuerpoCorreo"          => $strCuerpoCorreo,
                                                  "strCargoAsignado"         => "Gerente General");
                    $this->serviceEnvioPlantilla->generarEnvioPlantilla("APROBACIÓN DE SOLICITUD DE CORTESÍA",
                                                                        array_unique($arrayDestinatarios),
                                                                        "NOTIFICACION",
                                                                        $arrayParametrosMail,
                                                                        $strPrefijoEmpresa,
                                                                        "",
                                                                        "",
                                                                        null,
                                                                        true,
                                                                        "notificaciones_telcos@telconet.ec");
                }
            }

            /* Envío Correo Solicitudes Cambio de Documento Aprobadas
             * Para los diferentes tipos de solicitudes que serán aprobadas por Gerencia, se utilizará la misma plantilla de aprobación,
             * con la diferencia de que dependiendo del tipo de solicitud que se desea aprobar, se adjuntará un PDF con la información de las 
             * distintas solicitudes que fueron aprobadas. Es por esta razón que se enviarán como parámetros los nombres de las cabeceras 
             * de las columnas de la tabla con el contenido de las solicitudes.
             * Además se envían los parámetros necesarios para el contenido del correo que se enviará utilizando plantillas.
             */
            $arrayNombresCabeceraAdjunto = array(
                "Servicio",
                "Login",
                "Cliente",
                "Motivo",
                "Valor",
                "Tipo Doc",
                "Observación",
                "Fecha Creación",
                "Usuario Creación");

            $arrayParametrosMail = array(
                "idEmpresaSession"              => $strCodEmpresa,
                "prefijoEmpresaSession"         => $strPrefijoEmpresa,
                "codigoPlantilla"               => "APROB_AUTORIZAC",
                "usrCreacion"                   => $strUsrCreacion,
                "ipClient"                      => $strIpClient,
                "empleadoSession"               => $strEmpleado,
                "tituloAdjunto"                 => "APROBACIÓN DE SOLICITUDES DE CAMBIO DE DOCUMENTO",
                "tipoAutorizacion"              => "AUTORIZACIÓN DE CAMBIO DE DOCUMENTO",
                "tipoGestion"                   => "APROBACIÓN",
                "nombreTipoAutorizacionAdjunto" => "Aprobacion_Autorizacion_Cambio_Documento",
                "arrayNombresCabeceraAdjunto"   => $arrayNombresCabeceraAdjunto,
                "arrayDataAdjunto"              => $arrayData,
                "asunto"                        => "Gestion en Solicitudes de Cambio de Documento",
            );


            $serviceAutorizacionesLocal = $this->serviceAutorizaciones;
            $serviceAutorizacionesLocal->envioMailAutorizaciones($arrayParametrosMail);

            $this->emcom->getConnection()->commit();
            $arrayRespuesta["strMensaje"]   = "Se aprobaron las solicitudes con éxito.";
            $arrayRespuesta["intRespuesta"] = 1;
        }
        catch(\Exception $e)
        {
            if($this->emcom->getConnection()->isTransactionActive())
            {
                $this->emcom->getConnection()->rollback();
            }
            $this->emcom->getConnection()->close();

            $this->serviceUtil->insertError(
                    'Telcos+', 'SolicitudCambioDocumentoService->aprobarCambioDocumento', $e->getMessage(), $strUsrCreacion, $strIpClient
            );
            $arrayRespuesta["strMensaje"]   = "Ha ocurrido un problema. Por favor informe a Sistemas";
            $arrayRespuesta["intRespuesta"] = 0;
        }
        return $arrayRespuesta;
    }

}
