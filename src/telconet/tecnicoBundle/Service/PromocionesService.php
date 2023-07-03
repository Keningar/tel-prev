<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Clase para ejecutar procesos relacionados a las promociones de ancho de banda de clientes MD
 * 
 * @author Jesús Bozada <jbozada@telconet.ec>
 * @version 1.0 20-08-2019
 */
class PromocionesService
{
    private $objContainer;
    private $emGeneral;
    private $emComercial;
    private $serviceUtil;
    private $servicieServicioTecnico;
    private $serviceRecursosDeRed;
    private $serviceRdaMiddleware;
    private $opcion = "APLICAR_PROMOCION";
    private $ejecutaComando;
    private $serviceSoporte;
    private $serviceEnvioPlantilla;
    
    public function setDependencies(Container $objContainer)
    {
        $this->objContainer = $objContainer;
        $this->emComercial  = $objContainer->get('doctrine')->getManager('telconet');
        $this->emGeneral    = $objContainer->get('doctrine')->getManager('telconet_general');
        $this->serviceUtil  = $objContainer->get('schema.Util');
        $this->servicieServicioTecnico = $objContainer->get('tecnico.InfoServicioTecnico');
        $this->serviceRecursosDeRed    = $objContainer->get('planificacion.RecursosDeRed');
        $this->serviceRdaMiddleware    = $objContainer->get('tecnico.RedAccesoMiddleware');
        $this->ejecutaComando          = $objContainer->getParameter('ws_rda_ejecuta_scripts');
        $this->serviceSoporte          = $objContainer->get('soporte.SoporteService');
        $this->serviceEnvioPlantilla   = $objContainer->get('soporte.EnvioPlantilla');
    }

    /**
     * configurarPromocionesBW
     * 
     * Función que sirve para configurar las promociones de un servicio que aplique
     * a alguna existente ó que haya tenido alguna con meses pendientes de aplicar
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 22-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 11-05-2020 Se envía el parámetro strMarcaOlt a la función registrarCaracteristicasPromocionales
     * 
     * @author Daniel Reyes <djreyes@telconet.ec>
     * @version 1.2 09-03-2022 Se coloca bandera para no ejecutar flujo ambiguo y no se esta utilizando
     *                      en estos momentos y esta provocando creacion de tareas erroneas.
     * 
     * @param array $arrayParametros [
     *                                intIdServicio  => id del servicio
     *                                intIdEmpresa   => id de la empresa en sesión
     *                                strTipoProceso => tipo del proceso ejecutado
     *                                strValor       => valor correspondiente al tipo de proceso
     *                                                  (
     *                                                     strTipoProceso      strValor
     *                                                     ACITVACION       = ID_SERVICIO
     *                                                     CAMBIO_EQUIPO    = ID_SERVICIO
     *                                                     CAMBIO_LINEA_PON = ID_OLT
     *                                                     CAMBIO_PLAN      = ID_NUEVO_PLAN
     *                                                     TRASLADO         = ID_SERVICIO_ANTERIOR
     *                                                  )
     *                                strUsrCreacion => usuario de creación
     *                                strIpCreacion  => ip del cliente
     *                               ]
     */
    public function configurarPromocionesBW($arrayParametros)
    {
        $intIdServicio     = ( isset($arrayParametros['intIdServicio']) && !empty($arrayParametros['intIdServicio']) )
                             ? $arrayParametros['intIdServicio'] : null;
        $intIdEmpresa      = ( isset($arrayParametros['intIdEmpresa']) && !empty($arrayParametros['intIdEmpresa']) )
                             ? $arrayParametros['intIdEmpresa'] : null;
        $strTipoProceso    = ( isset($arrayParametros['strTipoProceso']) && !empty($arrayParametros['strTipoProceso']) )
                             ? $arrayParametros['strTipoProceso'] : null;
        $strValor          = ( isset($arrayParametros['strValor']) && !empty($arrayParametros['strValor']) )
                             ? $arrayParametros['strValor'] : null;
        $strIpCreacion     = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                             ? $arrayParametros['strIpCreacion'] : null;
        $strUsrCreacion    = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                             ? $arrayParametros['strUsrCreacion'] : null;
        $strPrefijoEmpresa = ( isset($arrayParametros['strPrefijoEmpresa']) && !empty($arrayParametros['strPrefijoEmpresa']) )
                             ? $arrayParametros['strPrefijoEmpresa'] : null;

        $strStatus   = "ERROR";
        $strMensaje  = "";
        $objServicio = null;
        $objProductoInternet = null;
        $strMensajeProceso   = "";
        $arrayResponse       = null;
        $arrayRespPromo      = array();
        $arrayFinal          = array();

        $this->emComercial->beginTransaction();

        // Se crea bandera para ejecutar proceso
        $strBandConfigPromo = 'NO';
        $arrayParamDetalle  = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                            ->getOne('PROMOCION ANCHO BANDA', 'COMERCIAL','PROMO_ANCHO_BANDA',
                                            'Bandera para ejecutar proceso de configuracion',
                                            'PROM_BW','','','','',$intIdEmpresa);
        if(!empty($arrayParamDetalle))
        {
            $strBandConfigPromo = $arrayParamDetalle["valor2"];
        }
        // Finaliza bandera
        try
        {
            //VALIDAR PARÁMETROS DE ENTRADA
            if(!empty($intIdServicio)  && !empty($intIdEmpresa)  && !empty($strValor) &&
               !empty($strTipoProceso) && !empty($strIpCreacion) && !empty($strUsrCreacion) &&
               !empty($strPrefijoEmpresa))
            {
                //OBTIENE INFORMACIÓN DEL PRODUCTO INTERNET DEDICADO
                $objProductoInternet = $this->emComercial
                                            ->getRepository('schemaBundle:AdmiProducto')
                                            ->findOneBy(array("descripcionProducto" => "INTERNET DEDICADO", 
                                                              "estado"              => "Activo",
                                                              "empresaCod"          => $intIdEmpresa));
                if(!is_object($objProductoInternet))
                {
                    throw new \Exception("No se logró recuperar información del producto internet.");
                }
                
                //OBTIENE INFORMACIÓN DEL SERVICIO
                $objServicio = $this->emComercial
                                    ->getRepository('schemaBundle:InfoServicio')
                                    ->find($intIdServicio);
                if (!is_object($objServicio))
                {
                    throw new \Exception("No se logró recuperar información del servicio a procesar.");
                }
                
                $objSpcReintentoPromo = $this->servicieServicioTecnico
                                             ->getServicioProductoCaracteristica($objServicio, "REINTENTO-PROMO", $objProductoInternet);
                
                //INVOCAR A PROCEDURE DE PROMOCIONES
                $arrayParametrosPromo = array();
                $arrayParametrosPromo['intIdServicio']  = $intIdServicio;
                $arrayParametrosPromo['strEmpresaCod']  = $intIdEmpresa;
                $arrayParametrosPromo['strTipoProceso'] = $strTipoProceso;
                $arrayParametrosPromo['strValor']       = $strValor;

                $arrayRespPromo = $this->emComercial
                                        ->getRepository('schemaBundle:InfoServicio')
                                        ->procesarPromocionesBw($arrayParametrosPromo);

                // Validamos que no configure promocion
                if ($strBandConfigPromo === 'SI')
                {
                    if ($arrayRespPromo['strStatus'] === "OK")
                    {
                        if (is_object($objSpcReintentoPromo))
                        {
                            $objSpcConfiguraPromo = $this->servicieServicioTecnico
                                                        ->getServicioProductoCaracteristica($objServicio, "CONFIGURA-PROMO", $objProductoInternet);
                            if (is_object($objSpcConfiguraPromo))
                            {
                                $arrayRespPromo['strConfiguraBw'] = $objSpcConfiguraPromo->getValor();
                            }
                        }
                        
                        if(($arrayRespPromo['strAplicaPromoExiste'] === "SI" && 
                            $arrayRespPromo['strMapeaPromo']        === "SI" && 
                            $arrayRespPromo['strTeniaPromo']        === "SI" ) ||
                        ($arrayRespPromo['strAplicaPromoExiste'] === "SI" &&
                            $arrayRespPromo['strMapeaPromo']        === "SI" &&
                            $arrayRespPromo['strTeniaPromo']        === "NO" ) ||
                        ($arrayRespPromo['strAplicaPromoExiste'] === "SI" && 
                            $arrayRespPromo['strMapeaPromo']        === "NO" && 
                            $arrayRespPromo['strTeniaPromo']        === "SI" )
                        )
                        {
                            
                            //OBTENER INFORMACIÓN TÉCNICA A UTILIZAR EN WS
                            $arrayParametrosInfoBw = array();
                            $arrayParametrosInfoBw['intIdServicio']  = $intIdServicio;
                            $arrayParametrosInfoBw['intEmpresaCod']  = $intIdEmpresa;
                            $arrayParametrosInfoBw['intIdPlan']      = $arrayRespPromo['intIdPlanPromo'];
                            $arrayParametrosInfoBw['strUsrCreacion'] = $strUsrCreacion;
                            $arrayParametrosInfoBw['strIpCreacion']  = $strIpCreacion;
                            $arrayRespuestaInfo = $this->serviceRecursosDeRed->obtenerInformacionPromocionesBw($arrayParametrosInfoBw);
                            if ($arrayRespuestaInfo['strStatus'] == "OK")
                            {
                                if ($arrayRespPromo['strConfiguraBw'] === "SI")
                                {
                                    //INVOCAR A WS DE RDA
                                    $arrayDatos = array(
                                                        'serial_ont'            => $arrayRespuestaInfo['strSerieOnt'],
                                                        'mac_ont'               => $arrayRespuestaInfo['strMacOnt'],
                                                        'nombre_olt'            => $arrayRespuestaInfo['strNombreOlt'],
                                                        'ip_olt'                => $arrayRespuestaInfo['strIpOlt'],
                                                        'puerto_olt'            => $arrayRespuestaInfo['strPuertoOlt'],
                                                        'modelo_olt'            => $arrayRespuestaInfo['strModeloOlt'],
                                                        'gemport'               => $arrayRespuestaInfo['strGemPort'],
                                                        'service_profile'       => $arrayRespuestaInfo['strServiceProfile'],
                                                        'line_profile'          => $arrayRespuestaInfo['strLineProfile'],
                                                        'traffic_table'         => $arrayRespuestaInfo['strTrafficTable'],
                                                        'ont_id'                => $arrayRespuestaInfo['strOntId'],
                                                        'service_port'          => $arrayRespuestaInfo['strSpid'],
                                                        'vlan'                  => $arrayRespuestaInfo['strVlan'],
                                                        'estado_servicio'       => $arrayRespuestaInfo['strEstadoServicio'],
                                                        'mac_wifi'              => $arrayRespuestaInfo['strMacWifi'],
                                                        'tipo_negocio_actual'   => $arrayRespuestaInfo['strTipoNegocioActual'],
                                                        'line_profile_nuevo'    => $arrayRespuestaInfo['strLineProfilePromo'],
                                                        'gemport_nuevo'         => $arrayRespuestaInfo['strGemPortPromo'],
                                                        'traffic_table_nueva'   => $arrayRespuestaInfo['strTrafficTablePromo'],
                                                        'tipo_negocio_nuevo'    => $arrayRespuestaInfo['strTipoNegocioNuevo'],
                                                        'vlan_nueva'            => $arrayRespuestaInfo['strVlanPromo'],
                                                        'ip'                    => $arrayRespuestaInfo['strIpServicio'],
                                                        'scope'                 => $arrayRespuestaInfo['strScope'],
                                                        'ip_fijas_activas'      => $arrayRespuestaInfo['intIpFijasActivas'],
                                                        'capacidad_up'          => $arrayRespuestaInfo['strCapacidadUp'],
                                                        'capacidad_down'        => $arrayRespuestaInfo['strCapacidadDown'],
                                                        'capacidad_up_nueva'    => $arrayRespuestaInfo['strCapacidadUpPromo'],
                                                        'capacidad_down_nueva'  => $arrayRespuestaInfo['strCapacidadDownPromo']
                                                    );

                                    $arrayDatosMiddleware = array(
                                                                    'nombre_cliente'        => $arrayRespuestaInfo['strNombreCliente'],
                                                                    'login'                 => $arrayRespuestaInfo['strLogin'],
                                                                    'identificacion'        => $arrayRespuestaInfo['strIdentificacion'],
                                                                    'datos'                 => $arrayDatos,
                                                                    'opcion'                => $this->opcion,
                                                                    'ejecutaComando'        => $this->ejecutaComando,
                                                                    'usrCreacion'           => $strUsrCreacion,
                                                                    'ipCreacion'            => $strIpCreacion,
                                                                    'empresa'               => 'MD'
                                                                );
                                    $arrayFinal = $this->serviceRdaMiddleware->middleware(json_encode($arrayDatosMiddleware));
                                    $strStatusMw  = $arrayFinal['status'];
                                    $strMensajeMw = $arrayFinal['mensaje'];

                                    //INVOCAR A PROCEDURE DE APLICAR PROMOCIONES
                                    if ($strStatusMw == "OK")
                                    {
                                        if(($arrayRespPromo['strAplicaPromoExiste'] === "SI" && 
                                            $arrayRespPromo['strMapeaPromo']        === "SI" && 
                                            $arrayRespPromo['strTeniaPromo']        === "SI" ) ||
                                        ($arrayRespPromo['strAplicaPromoExiste'] === "SI" &&
                                            $arrayRespPromo['strMapeaPromo']        === "SI" &&
                                            $arrayRespPromo['strTeniaPromo']        === "NO" ) || 
                                        ($arrayRespPromo['strAplicaPromoExiste'] === "SI" && 
                                            $arrayRespPromo['strMapeaPromo']        === "NO" && 
                                            $arrayRespPromo['strTeniaPromo']        === "SI" &&
                                            is_object($objSpcReintentoPromo))
                                        ) 
                                        {
                                            //INVOCAR A PROCEDURE DE PROMOCIONES
                                            $arrayParametrosPromoApli = array();
                                            $arrayParametrosPromoApli['intIdServicio']  = $intIdServicio;
                                            $arrayParametrosPromoApli['strEmpresaCod']  = $intIdEmpresa;
                                            $this->emComercial
                                                ->getRepository('schemaBundle:InfoServicio')
                                                ->aplicarPromocionesBw($arrayParametrosPromoApli);
                                            
                                            
                                        }

                                        if(is_object($objSpcReintentoPromo))
                                        {
                                            $strMensaje = "Proceso ejecutado con éxito. Se aplicó promoción al cliente!";
                                        }
                                    }
                                    else
                                    {
                                        $strMensajeProceso = "Se presentaron errores al procesar mediante ".
                                                            "middleware la información promocional del servicio";
                                        throw new \Exception($strMensajeProceso.', '.
                                                            $strMensajeMw);
                                    }
                                }
                                //CREAR CARACTERÍSTICAS PROMOCIONALES (ELIMINAR ANTIGUAS SI EXISTEN Y SOLO DEJAR LAS NUEVAS)
                                $arrayParamRegistraCaract = array();
                                $arrayParamRegistraCaract['objServicio']     = $objServicio;
                                $arrayParamRegistraCaract['strIpCreacion']   = $strIpCreacion;
                                $arrayParamRegistraCaract['strUsrCreacion']  = $strUsrCreacion;
                                $arrayParamRegistraCaract['strVlanPromo']    = $arrayRespuestaInfo['strVlanPromo'];
                                $arrayParamRegistraCaract['strTrafficPromo'] = $arrayRespuestaInfo['strTrafficTablePromo'];
                                $arrayParamRegistraCaract['strGemPortPromo'] = $arrayRespuestaInfo['strGemPortPromo'];
                                $arrayParamRegistraCaract['strLineProfilePromo']   = $arrayRespuestaInfo['strLineProfilePromo'];
                                $arrayParamRegistraCaract['strPerfilEquiPromo']    = $arrayRespuestaInfo['strLineProfilePromo'];
                                $arrayParamRegistraCaract['strCapacidadUpPromo']   = $arrayRespuestaInfo['strCapacidadUpPromo'];
                                $arrayParamRegistraCaract['strCapacidadDownPromo'] = $arrayRespuestaInfo['strCapacidadDownPromo'];
                                
                                $arrayParamRegistraCaract['objProducto']         = $objProductoInternet;
                                $arrayParamRegistraCaract['strModeloOlt']        = $arrayRespuestaInfo['strModeloOlt'];
                                $arrayParamRegistraCaract['strMarcaOlt']         = $arrayRespuestaInfo['strMarcaOlt'];
                                if ($arrayRespPromo['strConfiguraBw'] === "SI" && 
                                    isset($arrayFinal['AB_promo']) && 
                                    !empty($arrayFinal['AB_promo']))
                                {
                                    $arrayParamRegistraCaract['strAbPromo'] = $arrayFinal['AB_promo'];
                                }
                                else
                                {
                                    $arrayParamRegistraCaract['strAbPromo'] = "Pendiente";
                                }

                                $this->registrarCaracteristicasPromocionales($arrayParamRegistraCaract);
                            }
                            else
                            {
                                $strMensajeProceso = "Se presentaron errores al obtener la información promocional del servicio a aplicar";
                                throw new \Exception($strMensajeProceso.', '.
                                                    $arrayRespuestaInfo['strMensaje']);
                            }
                        }
                        else
                        {
                            if(is_object($objSpcReintentoPromo))
                            {
                                if (($arrayRespPromo['strAplicaPromoExiste'] === "SI" && 
                                    $arrayRespPromo['strMapeaPromo']        === "SI" && 
                                    $arrayRespPromo['strTeniaPromo']        === "SI" ) ||
                                ($arrayRespPromo['strAplicaPromoExiste'] === "SI" &&
                                    $arrayRespPromo['strMapeaPromo']        === "SI" &&
                                    $arrayRespPromo['strTeniaPromo']        === "NO" ) ||
                                ($arrayRespPromo['strAplicaPromoExiste'] === "SI" && 
                                    $arrayRespPromo['strMapeaPromo']        === "NO" && 
                                    $arrayRespPromo['strTeniaPromo']        === "SI" ))
                                {
                                    $strMensaje = "Proceso exitoso. Cliente tiene promoción, se aplicara en los meses siguientes!";
                                }
                                else
                                {
                                    $strMensaje = "Proceso exitoso. Cliente NO tiene promoción!";
                                }
                            }
                        }
                    }
                    else
                    {
                        $arrayRespPromo['strConfiguraBw'] = null;
                        $strMensajeProceso = "Se presentaron errores al validar la información promocional del servicio";
                        throw new \Exception($strMensajeProceso.', '.
                                            $arrayRespPromo['strMensaje']);
                    }
                    $arrayParamEliminaCaract = array();
                    $arrayParamEliminaCaract['objServicio']    = $objServicio;
                    $arrayParamEliminaCaract['objProducto']    = $objProductoInternet;
                    $arrayParamEliminaCaract['strIpCreacion']  = $strIpCreacion;
                    $arrayParamEliminaCaract['strUsrCreacion'] = $strUsrCreacion;
                    $arrayParamEliminaCaract['strNombreCaracteristica'] = "REINTENTO-PROMO";
                    $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                    $arrayParamEliminaCaract['strNombreCaracteristica'] = "PROCESO-PROMO";
                    $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                    $arrayParamEliminaCaract['strNombreCaracteristica'] = "VALOR-PROMO";
                    $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                    $arrayParamEliminaCaract['strNombreCaracteristica'] = "CONFIGURA-PROMO";
                    $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                }
                $this->emComercial->commit();

                $strStatus  = "OK";
                $strMensaje = empty($strMensaje)?"Validación de Promoción procesada correctamente.":$strMensaje;
            }
            else
            {
                throw new \Exception( 'No se han enviado los parámetros adecuados para procesar la información. - intIdServicio('.
                                      $intIdServicio.'), intIdEmpresa('.$intIdEmpresa.'), strTipoProceso('.$strTipoProceso.'), '.
                                      'strValor('.$strValor.'), strIpCreacion('.$strIpCreacion.', strUsrCreacion('.$strUsrCreacion.'))' );
            }
        }
        catch (\Exception $objEx)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $strStatus  = "ERROR";
            $strMensaje = empty($strMensajeProceso)?"Se presentó un error general en el proceso, favor Notificar a Sistemas":$strMensajeProceso;
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoActivarPuertoService->configurarPromocionesBW',
                                            $objEx->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
            //CREAR CARACTERÍSTICA PARA REINTENTAR PROCESO DE VALIDACIONES DE PROMOCIONES
            if((is_object($objServicio) && 
               is_object($objProductoInternet) &&
               !empty($strTipoProceso) &&
               !empty($strValor)) && $strBandConfigPromo === 'SI')
            {
                $objSpcReintentoPromo = $this->servicieServicioTecnico
                                             ->getServicioProductoCaracteristica($objServicio, "REINTENTO-PROMO", $objProductoInternet);
                
                if (is_object($objSpcReintentoPromo))
                {
                    $strValorSpc = $objSpcReintentoPromo->getValor();
                    $intValorSpc = intval($strValorSpc) + 1;
                    $objSpcReintentoPromo->setValor($intValorSpc);
                    $objSpcReintentoPromo->setUsrUltMod($strUsrCreacion);
                    $objSpcReintentoPromo->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objSpcReintentoPromo);
                    $this->emComercial->flush();
                    $strMensaje = "Reintento #".$intValorSpc.". $strMensaje".".";
                    //MENSAJE DE ERROR AL USUARIO Y REGISTRO DE HISTORIAL DE REINTENTO DE PROCESO DE VALIDACIÓN DE PROMOCIÓN
                    $objServicioHistorial = new InfoServicioHistorial();
                    $objServicioHistorial->setServicioId($objServicio);
                    $objServicioHistorial->setObservacion($strMensaje);
                    $objServicioHistorial->setEstado($objServicio->getEstado());
                    $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $objServicioHistorial->setIpCreacion($strIpCreacion);
                    $this->emComercial->persist($objServicioHistorial);
                    $this->emComercial->flush();
                    if ($intValorSpc == 3)
                    {
                        $arrayParamEliminaCaract = array();
                        $arrayParamEliminaCaract['objServicio']    = $objServicio;
                        $arrayParamEliminaCaract['objProducto']    = $objProductoInternet;
                        $arrayParamEliminaCaract['strIpCreacion']  = $strIpCreacion;
                        $arrayParamEliminaCaract['strUsrCreacion'] = $strUsrCreacion;
                        $arrayParamEliminaCaract['strNombreCaracteristica'] = "REINTENTO-PROMO";
                        $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                        $arrayParamEliminaCaract['strNombreCaracteristica'] = "PROCESO-PROMO";
                        $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                        $arrayParamEliminaCaract['strNombreCaracteristica'] = "VALOR-PROMO";
                        $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                        $arrayParamEliminaCaract['strNombreCaracteristica'] = "CONFIGURA-PROMO";
                        $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
                        
                        //ENVÍO DE NOTIFICACIÓN A NETLIFE PARA GESTIONAR MANUALMENTE EL PROCESO DE VALIDACIÓN DE PROMOCIÓN
                        $strAsuntoCorreo        = "REINTENTO DE PROCESO DE VALIDACIÓN DE PROMOCIÓN SUPERÓ EL LÍMITE";
                        if(is_object($objServicio->getPlanId()))
                        {
                            $strTipoServicio        = "Plan";
                            $strNombreServicio      = $objServicio->getPlanId()->getNombrePlan();
                        }
                        $objPunto               = $objServicio->getPuntoId();
                        $objPersonaEmpresaRol   = $objPunto->getPersonaEmpresaRolId();
                        $objPersona             = $objPersonaEmpresaRol->getPersonaId();
                        $objJurisdiccion        = $objPunto->getPuntoCoberturaId();
                        $strLogin               = $objPunto->getLogin();
                        $strNombreCliente       = sprintf("%s",$objPersona);
                        if(is_object($objJurisdiccion))
                        {
                            $strNombreJurisdiccion  = $objJurisdiccion->getNombreJurisdiccion();
                        }
                        else
                        {
                            $strNombreJurisdiccion  = "";
                        }
                        $arrayParametrosMail    = array( 
                                                        "cliente"               => $strNombreCliente,
                                                        "login"                 => $strLogin,
                                                        "nombreJurisdiccion"    => $strNombreJurisdiccion,
                                                        "tipoServicio"          => $strTipoServicio,
                                                        "nombreServicio"        => $strNombreServicio,
                                                        "observacion"           => $strMensaje,
                                                        "nombrePlan"            => $objServicio->getPlanId()->getNombrePlan(),
                                                        "estadoServicio"        => $objServicio->getEstado()
                                                       );
                        $this->serviceEnvioPlantilla->generarEnvioPlantilla($strAsuntoCorreo, 
                                                                            array(), 
                                                                            'ERROR_PROMOBW', 
                                                                            $arrayParametrosMail,
                                                                            '',
                                                                            '',
                                                                            '',
                                                                            null, 
                                                                            false,
                                                                            'notificacionesnetlife@netlife.info.ec');
                    }
                }
                else
                {
                    $this->servicieServicioTecnico
                         ->ingresarServicioProductoCaracteristica($objServicio, 
                                                                  $objProductoInternet, 
                                                                  "REINTENTO-PROMO", 
                                                                  "0", 
                                                                  $strUsrCreacion);
                    $this->servicieServicioTecnico
                         ->ingresarServicioProductoCaracteristica($objServicio, 
                                                                  $objProductoInternet, 
                                                                  "PROCESO-PROMO", 
                                                                  $strTipoProceso, 
                                                                  $strUsrCreacion);
                    
                    $this->servicieServicioTecnico
                         ->ingresarServicioProductoCaracteristica($objServicio, 
                                                                  $objProductoInternet, 
                                                                  "VALOR-PROMO", 
                                                                  $strValor, 
                                                                  $strUsrCreacion);
                    if ( isset($arrayRespPromo['strConfiguraBw']) && !empty($arrayRespPromo['strConfiguraBw']) )
                    {
                        $this->servicieServicioTecnico
                             ->ingresarServicioProductoCaracteristica($objServicio, 
                                                                      $objProductoInternet, 
                                                                      "CONFIGURA-PROMO", 
                                                                      $arrayRespPromo['strConfiguraBw'], 
                                                                      $strUsrCreacion);
                    }
                    $this->serviceSoporte
                        ->crearTareaReintentoPromo(
                            array(
                                "objPunto"            => $objServicio->getPuntoId(),
                                "strIpClient"         => $strIpCreacion,
                                "strCodEmpresa"       => $intIdEmpresa,
                                "strUsrCreacion"      => $strUsrCreacion,
                                "strPrefijoEmpresa"   => $strPrefijoEmpresa,
                                "strObservacionTarea" => $strMensaje
                        ));
                }
            }
        }
        $arrayResponse = array("strStatus" => $strStatus, "strMensaje" => $strMensaje);
        return $arrayResponse;
    }

    /**
     * trasladarPromocionesMensuales
     * 
     * Función que sirve para trasladar las promociones pendientes de
     * aplicar que aún apliquen con los nuevos datos del cliente
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 07-04-2022
     * 
     * @param array $arrayParametros [
     *                                strEmpresaCod     => Código de la empresa que realizará el proceso
     *                                intIdPuntoOrigen  => Identificador del punto origen del traslado
     *                                intIdPuntoDestino => Identificador del punto destino del traslado
     *                                strUsrCreacion    => usuario de creación
     *                                strIpCreacion     => ip del cliente
     *                               ]
     * @return array $arrayResponse [
     *                                strStatus  => Estado de ejecución del proceso
     *                                strMensaje => Mensaje de ejecución del proceso
     *                               ]
     */
    public function trasladarPromocionesMensuales($arrayParametros)
    {
        $strEmpresaCod     = ( isset($arrayParametros['strEmpresaCod']) && !empty($arrayParametros['strEmpresaCod']) )
                             ? $arrayParametros['strEmpresaCod'] : null;
        $intIdPuntoOrigen  = ( isset($arrayParametros['intIdPuntoOrigen']) && !empty($arrayParametros['intIdPuntoOrigen']) )
                             ? $arrayParametros['intIdPuntoOrigen'] : null;
        $intIdPuntoDestino = ( isset($arrayParametros['intIdPuntoDestino']) && !empty($arrayParametros['intIdPuntoDestino']) )
                             ? $arrayParametros['intIdPuntoDestino'] : null;
        $strIpCreacion     = ( isset($arrayParametros['strIpCreacion']) && !empty($arrayParametros['strIpCreacion']) )
                             ? $arrayParametros['strIpCreacion'] : null;
        $strUsrCreacion    = ( isset($arrayParametros['strUsrCreacion']) && !empty($arrayParametros['strUsrCreacion']) )
                             ? $arrayParametros['strUsrCreacion'] : null;
        
        $strStatus   = "ERROR";
        $strMensaje  = "";
        $strMensajeProceso   = "";
        $arrayResponse       = null;
        $arrayRespPromo      = array();
        try
        {
            //VALIDAR PARÁMETROS DE ENTRADA
            if(!empty($strEmpresaCod)  && !empty($intIdPuntoOrigen)  && !empty($intIdPuntoDestino) &&
               !empty($strIpCreacion) && !empty($strUsrCreacion))
            {
                //INVOCAR A PROCEDURE DE PROMOCIONES
                $arrayParametrosPromo = array();
                $arrayParametrosPromo['strEmpresaCod']     = $strEmpresaCod;
                $arrayParametrosPromo['intIdPuntoOrigen']  = $intIdPuntoOrigen;
                $arrayParametrosPromo['intIdPuntoDestino'] = $intIdPuntoDestino;
                $arrayRespPromo = $this->emComercial
                                       ->getRepository('schemaBundle:InfoServicio')
                                       ->trasladaPromocionesMensuales($arrayParametrosPromo);
                if ($arrayRespPromo['strTrasladoPromo'] === "ERROR")
                {
                    //Inserta historial de servicio con mensaje indicando que hubo error al procesar el traslado
                    $strMensajeProceso = "Se presentaron errores al trasladar la información promocional del servicio";
                    throw new \Exception($strMensajeProceso.', '.
                                         $arrayRespPromo['strMensaje']);
                }
                $strStatus  = "OK";
                $strMensaje = "Traslado de Promoción procesado correctamente.";
            }
            else
            {
                throw new \Exception( 'No se han enviado los parámetros adecuados para procesar la información. - intIdServicio('.
                                      $intIdServicio.'), intIdEmpresa('.$intIdEmpresa.'), strTipoProceso('.$strTipoProceso.'), '.
                                      'strValor('.$strValor.'), strIpCreacion('.$strIpCreacion.', strUsrCreacion('.$strUsrCreacion.'))' );
            }
        }
        catch (\Exception $objEx)
        {
            $strStatus  = "ERROR";
            $strMensaje = empty($strMensajeProceso) ?
                          "Se presentó un error general en el traslado de promociones, favor Notificar a Sistemas" :
                          $strMensajeProceso;
            
            $this->serviceUtil->insertError('Telcos+',
                                            'PromocionesService->trasladarPromocionesMensuales',
                                            $objEx->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
        $arrayResponse = array("strStatus" => $strStatus, "strMensaje" => $strMensaje);
        return $arrayResponse;
    }
    
    /**
     * registrarCaracteristicasPromocionales
     * 
     * Función que sirve para eliminar todas las características promocionales activas de un tipo de característica
     * enviada por parámetro de un servicio específico y adicional realiza el registro de las nuevas características
     * promocionales a colocar en el servicio
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 23-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt 
     * 
     * @param array $arrayParametros [
     *                                objServicio             => id del servicio
     *                                objProducto             => producto internet
     *                                strUsrCreacion          => usuario de creación
     *                                strIpCreacion           => ip del cliente
     *                                strVlanPromo            => vlan promocional del cliente
     *                                strTrafficPromo         => traffic promocional del cliente
     *                                strGemPortPromo         => gemport promocional del cliente
     *                                strLineProfilePromo     => line profile promocional del cliente
     *                                strPerfilEquiPromo      => perfil equivalente del cliente
     *                                strModeloOlt            => modelo del elemento olt del cliente
     *                               ]
     */
    public function registrarCaracteristicasPromocionales($arrayParametros)
    {
        $objServicio     = $arrayParametros['objServicio'];
        $objProducto     = $arrayParametros['objProducto'];
        $strIpCreacion   = $arrayParametros['strIpCreacion'];
        $strUsrCreacion  = $arrayParametros['strUsrCreacion'];
        $strTrafficPromo = $arrayParametros['strTrafficPromo'];
        $strGemPortPromo = $arrayParametros['strGemPortPromo'];
        $strLineProfilePromo    = $arrayParametros['strLineProfilePromo'];
        $strPerfilEquiPromo     = $arrayParametros['strPerfilEquiPromo'];
        $strCapacidadUpPromo    = $arrayParametros['strCapacidadUpPromo'];
        $strCapacidadDownPromo  = $arrayParametros['strCapacidadDownPromo'];
        $strModeloOlt           = $arrayParametros['strModeloOlt'];
        $strMarcaOlt            = $arrayParametros['strMarcaOlt'];
        $strAbPromo             = $arrayParametros['strAbPromo'];
        try
        {
            $arrayParamEliminaCaract = array();
            $arrayParamEliminaCaract['objServicio']    = $objServicio;
            $arrayParamEliminaCaract['objProducto']    = $objProducto;
            $arrayParamEliminaCaract['strIpCreacion']  = $strIpCreacion;
            $arrayParamEliminaCaract['strUsrCreacion'] = $strUsrCreacion;
            
            // ELIMINAR CARACTERÍSTICAS ANTIGUAS PROMOCIONALES
            
            //ELIMINAR AB PROMO
            $arrayParamEliminaCaract['strNombreCaracteristica'] = "AB-PROMO";
            $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
            
            if($strMarcaOlt == "TELLION")
            {
                //ELIMINAR PERFIL EQUIVALENTE PROMO
                $arrayParamEliminaCaract['strNombreCaracteristica'] = "PERFIL-PROMO";
                $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
            }
            else if($strMarcaOlt == "HUAWEI")
            {
                //ELIMINAR TRAFFIC PROMO
                $arrayParamEliminaCaract['strNombreCaracteristica'] = "TRAFFIC-TABLE-PROMO";
                $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);

                //ELIMINAR GEMPORT PROMO
                $arrayParamEliminaCaract['strNombreCaracteristica'] = "GEM-PORT-PROMO";
                $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);

                //ELIMINAR LINEPROFILE PROMO
                $arrayParamEliminaCaract['strNombreCaracteristica'] = "LINE-PROFILE-NAME-PROMO";
                $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
            }
            else 
            {
                //ELIMINAR CAPACIDAD 1 PROMOCIONAL
                $arrayParamEliminaCaract['strNombreCaracteristica'] = "CAPACIDAD1-PROMO";
                $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);

                //ELIMINAR CAPACIDAD 2 PROMOCIONAL
                $arrayParamEliminaCaract['strNombreCaracteristica'] = "CAPACIDAD2-PROMO";
                $this->eliminarCaracteristicasServicio($arrayParamEliminaCaract);
            }
            
            //CREAR NUEVAS CARACTERÍSTICAS PROMOCIONALES
            
            //CREAR PERFIL EQUIVALENTE PROMO
            $this->servicieServicioTecnico
                 ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                           $objProducto, 
                                                           "AB-PROMO", 
                                                           $strAbPromo, 
                                                           $strUsrCreacion);
                
            if($strMarcaOlt == "TELLION")
            {
                //CREAR PERFIL EQUIVALENTE PROMO
                $this->servicieServicioTecnico
                     ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                               $objProducto, 
                                                               "PERFIL-PROMO", 
                                                               $strPerfilEquiPromo, 
                                                               $strUsrCreacion);
            }
            else if($strMarcaOlt == "HUAWEI")
            {
                //CREAR TRAFFIC PROMO
                $this->servicieServicioTecnico
                     ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                               $objProducto, 
                                                               "TRAFFIC-TABLE-PROMO", 
                                                               $strTrafficPromo, 
                                                               $strUsrCreacion);

                //CREAR GEMPORT PROMO
                $this->servicieServicioTecnico
                     ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                               $objProducto, 
                                                               "GEM-PORT-PROMO", 
                                                               $strGemPortPromo, 
                                                               $strUsrCreacion);

                //CREAR LINEPROFILE PROMO
                $this->servicieServicioTecnico
                     ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                               $objProducto, 
                                                               "LINE-PROFILE-NAME-PROMO", 
                                                               $strLineProfilePromo, 
                                                               $strUsrCreacion);
            }
            else
            {
                //CREAR CAPACIDAD1 PROMO
                $this->servicieServicioTecnico
                     ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                               $objProducto, 
                                                               "CAPACIDAD1-PROMO", 
                                                               $strCapacidadUpPromo, 
                                                               $strUsrCreacion);
                //CREAR CAPACIDAD2 PROMO
                $this->servicieServicioTecnico
                     ->ingresarServicioProductoCaracteristica( $objServicio, 
                                                               $objProducto, 
                                                               "CAPACIDAD2-PROMO", 
                                                               $strCapacidadDownPromo, 
                                                               $strUsrCreacion);
            }
            
            //Actualizar tablas de mapeo mensual que lee RDA para aplicar promoción
            //INVOCAR A PROCEDURE DE PROMOCIONES
            $arrayParametrosPromoActRda = array();
            $arrayParametrosPromoActRda['intIdServicio']         = $objServicio->getId();
            $arrayParametrosPromoActRda['strLineProfilePromo']   = $strLineProfilePromo;
            $arrayParametrosPromoActRda['strTrafficPromo']       = $strTrafficPromo;
            $arrayParametrosPromoActRda['strGemPortPromo']       = $strGemPortPromo;
            $arrayParametrosPromoActRda['strCapacidadUpPromo']   = $strCapacidadUpPromo;
            $arrayParametrosPromoActRda['strCapacidadDownPromo'] = $strCapacidadDownPromo;
            $arrayParametrosPromoActRda['strUsrCreacion']        = $strUsrCreacion;
            $arrayParametrosPromoActRda['strIpCreacion']         = $strIpCreacion;
            $this->emComercial
                 ->getRepository('schemaBundle:InfoServicio')
                 ->actualizarRdaPromocionesBw($arrayParametrosPromoActRda);
        }
        catch (\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoActivarPuertoService->registrarCaracteristicasPromocionales',
                                            $objEx->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
    }
    
    /**
     * eliminarCaracteristicasServicio
     * 
     * Función que sirve para eliminar todas las características activas de un tipo de caracteristica
     * enviada por parámetro de un servicio específico
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.0 23-08-2019
     * 
     * @param array $arrayParametros [
     *                                objServicio             => id del servicio
     *                                objProducto             => producto relacionado con característica
     *                                strNombreCaracteristica => nombre de la característica
     *                                strUsrCreacion          => usuario de creación
     *                                strIpCreacion           => ip del cliente
     *                               ]
     */
    public function eliminarCaracteristicasServicio($arrayParametros)
    {
        $objServicio    = $arrayParametros['objServicio'];
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'];
        $objProducto    = $arrayParametros['objProducto'];
        $strNombreCaracteristica = $arrayParametros['strNombreCaracteristica'];
        try
        {
            $objCaracteristica = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiCaracteristica')
                                      ->findOneBy(array("descripcionCaracteristica" => $strNombreCaracteristica, 
                                                        "estado"                    => "Activo"));

            if (is_object($objCaracteristica))
            {
                $objProdCaract = $this->emComercial
                                      ->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                      ->findOneBy(array("productoId"       => $objProducto->getId(), 
                                                        "caracteristicaId" => $objCaracteristica->getId(), 
                                                        "estado"           => "Activo"));

                if (is_object($objProdCaract))
                {
                    $arrayProdCaract = $this->emComercial
                                            ->getRepository('schemaBundle:InfoServicioProdCaract')
                                            ->findBy(array('servicioId'                => $objServicio->getId(),
                                                           'productoCaracterisiticaId' => $objProdCaract->getId(),
                                                           'estado'                    => 'Activo'));
                    foreach($arrayProdCaract as $objProdCaractItem)
                    {
                        $objProdCaractItem->setEstado("Eliminado");
                        $objProdCaractItem->setUsrUltMod($strUsrCreacion);
                        $objProdCaractItem->setFeUltMod(new \DateTime('now'));
                        $this->emComercial->persist($objProdCaractItem);
                        $this->emComercial->flush();
                    }
                }
            }
        }
        catch (\Exception $objEx)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoActivarPuertoService->eliminarCaracteristicasServicio',
                                            $objEx->getMessage(),
                                            $strUsrCreacion,
                                            $strIpCreacion);
        }
    }
}
