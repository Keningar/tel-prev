<?php

namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPagoLinea;
use telconet\schemaBundle\Entity\InfoProcesoMasivoCab;
use telconet\schemaBundle\Entity\InfoProcesoMasivoDet;
use telconet\schemaBundle\Entity\InfoRecaudacion;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Repository\InfoServicioTecnicoRepository;
// ...
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProcesoMasivoService {
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emComercial;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emFinanciero;
    
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $emGeneral;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $emInfraestructura;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\SerializerService
     */
    private $serializer;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\RestClientService
     */
    private $restClient;
    
    /**
     *
     * @var \telconet\schemaBundle\Service\MailerService
     */
    private $mailer;
    
    /**
     *
     * @var telconet\soporteBundle\Service\EnvioPlantillaService
     */
    private $serviceEnvioPlantilla;
    // ...
    private $WebServiceProcesoMasivoURL;
    private $WebServiceProcesoMasivoRestURL;
    
    private $pathTelcos;
    private $pathErrorLog;
    
    private $serviceServicioTecnico;
    private $intIdEmpresaMd;
    private $serviceUtil;
    private $objContainer;
    
    /**
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 20-05-2021 Se agrega la obtención del valor del parámetro idEmpresa_megadatos que es el id de la empresa MD
     * @since 1.0
     * 
     */
    public function setDependencies(\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        // ...Entities Managers
        $this->objContainer      = $container;
        $this->emComercial       = $container->get('doctrine.orm.telconet_entity_manager');
        $this->emFinanciero      = $container->get('doctrine.orm.telconet_financiero_entity_manager');
        $this->emInfraestructura = $container->get('doctrine.orm.telconet_infraestructura_entity_manager');
        $this->emGeneral         = $container->get('doctrine.orm.telconet_general_entity_manager');
        // ...Parameters
        $this->WebServiceProcesoMasivoURL     = $container->getParameter('ws_proceso_masivo');
        $this->WebServiceProcesoMasivoRestURL = $container->getParameter('ws_proceso_masivo_rest');
        // ...Services
        $this->restClient            = $container->get('schema.RestClient');
        $this->serializer            = $container->get('schema.Serializer');
        $this->mailer                = $container->get('schema.Mailer');
        $this->pathTelcos            = $container->getParameter('path_telcos');
        $this->pathErrorLog          = $container->getParameter('general.path.errorLog');
        $this->serviceEnvioPlantilla = $container->get('soporte.EnvioPlantilla');
        $this->serviceServicioTecnico= $container->get('tecnico.InfoServicioTecnico');
        $this->intIdEmpresaMd        = $container->getParameter('idEmpresa_megadatos');
        $this->serviceUtil           = $container->get('schema.Util');
        
    }

    public function obtenerPuntosReactivacion($arrayParametros, $start, $limit, $recaudacionId=NULL, $pagoLineaId=NULL, $debitoId=NULL) {   
        /* @var $repo \telconet\schemaBundle\Repository\InfoServicioTecnicoRepository */
        $repo = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $datos = $repo->getPuntosReactivacion($arrayParametros, $start, $limit, $recaudacionId, $pagoLineaId, $debitoId);        
        return $datos;
    }
    public function obtenerPuntosReactivacionIds($idEmpresa, $fechaCorteDesde, $fechaCorteHasta, $valorMontoDeuda, $idsOficinas, $start, $limit, $recaudacionId=NULL, $pagoLineaId=NULL, $debitoId=NULL) {
        $repo = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        /* @var $repo \telconet\schemaBundle\Repository\InfoServicioTecnicoRepository */
        $datos = $repo->getPuntosReactivacionIds($idEmpresa, $fechaCorteDesde, $fechaCorteHasta, $valorMontoDeuda, $idsOficinas, $start, $limit, $recaudacionId, $pagoLineaId, $debitoId);
        return $datos;
    }

    public function obtenerServiciosCambioPlan($idEmpresa, $idsOficinas, $planId, $start, $limit) {
        /* @var $repo \telconet\schemaBundle\Repository\InfoServicioTecnicoRepository */
        $repo = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico');
        $datos = $repo->getServiciosParaCambioPlan($idEmpresa, $idsOficinas, $planId, $start, $limit);
        return $datos;
    }
    
    public function generarJsonPuntosReactivacion($arrayParametros, $start, $limit) 
    {        
        $datos = $this->obtenerPuntosReactivacion($arrayParametros, $start, $limit);
        
        $registros = array();
        
        $registros = $datos['registros'];
        $total = $datos['total'];
        if ($registros) {
            if ($total == 0) {
                $resultado = array(
                                'total' => 0,
                                'encontrados' => array()
                );
                $resultado = json_encode($resultado);
                return $resultado;
            } else {
                $data = $this->serializer->serialize($registros);
                $resultado = '{"total":"' . $total . '","encontrados":' . $data . '}';
                return $resultado;
            }
        } else {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    public function generarJsonServiciosParaCambioPlan($idEmpresa, $idsOficinas, $planId, $start, $limit) {
        $datos = $this->obtenerServiciosCambioPlan($idEmpresa, $idsOficinas, $planId, $start, $limit);
        
        $registros = array();
        
        $registros = $datos['registros'];
        $total = $datos['total'];
        if ($registros) {
            if ($total == 0) {
                $resultado = array(
                                'total' => 0,
                                'encontrados' => array()
                );
                $resultado = json_encode($resultado);
                return $resultado;
            } else {
                $data = $this->serializer->serialize($registros);
                $resultado = '{"total":"' . $total . '","encontrados":' . $data . '}';
                return $resultado;
            }
        } else {
            $resultado = '{"total":"0","encontrados":[]}';
            return $resultado;
        }
    }

    /**
     * guardarPuntosPorCorteMasivo
     * 
     * Metodo para realizar la generación de cabecera y detalle de procesos masivos de corte
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 12-01-2017
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 07-02-2018 Se regularizan cambios en caliente, se desactiva verificacion SSL
     * @since 1.1
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 04-05-2022 Se modifica el uso de la función loggerProcesoMasivo debido a que esta opción sólo es usada de corte masivo por página
     *                         por lo que se al usar un error_log sería suficiente puesto que éste es el devuelto con el throw.
     *
     * @param  - $prefijoEmpresa        Cadena de caracteres que indica el prefijo de la empresa utilizado en el proceso
     *         - $idEmpresa             Cadena de caracteres que indica el id de la empresa
     *         - $numFacturasAbiertas   Parametro que indica que numero de facturas abiertas en el proceso masivo a generar
     *         - $fechaEmisionFactura   Parametro que indica la fecha de emisión de facturas a setear en proceso masivo
     *         - $valorMontoDeuda       Parametro que indica el valor total de la deuda del proceso masivo a generar
     *         - $idFormaPago           Parametro que indica la forma de pago a setear en el proceso masivo a generar
     *         - $idsBancosTarjetas     Parametro que indica los identificadores de tarjetas bancarias a procesar
     *         - $idsOficinas           Parametro que indica los identificadores de oficinas a procesar
     *         - $idsPuntos             Parametro que indica los identificadores de los puntos a procesar
     *         - $cantidadPuntos        Parametro que indica el numero total de puntos a procesar   
     *         - $usrCreacion           Cadena de caracteres que indica el usuario que procesa el corte masivo        
     *         - $clientIp              Cadena de caracteres que indica la ip del usuario que procesa el corte masivo
     */
    public function guardarPuntosPorCorteMasivo( $prefijoEmpresa, 
                                                 $idEmpresa, 
                                                 $numFacturasAbiertas, 
                                                 $fechaEmisionFactura, 
                                                 $valorMontoDeuda, 
                                                 $idFormaPago, 
                                                 $idsBancosTarjetas, 
                                                 $idsOficinas, 
                                                 $idsPuntos, 
                                                 $cantidadPuntos, 
                                                 $usrCreacion, 
                                                 $clientIp 
                                               ) 
    {
        $this->emInfraestructura->beginTransaction();
        $this->emComercial->beginTransaction();
        $arrayParametrosMail  = array();
        $arrayPuntos          = array();
        $intContadorRegistros = 0;
        $dateFechaProceso     = new \DateTime('now');
        $strNombreProceso     = '';
        try 
        {
            $dateFechaProceso     = $dateFechaProceso->format('d-m-Y_H:i:s');
            // ....................................................... //
            // CREAMOS EL PROCESO MASIVO
            // ....................................................... //
            $entityInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
            $entityInfoProcesoMasivoCab->setTipoProceso("CortarCliente");
            // ...
            $entityInfoProcesoMasivoCab->setCantidadPuntos($cantidadPuntos);
            // ...
            $entityInfoProcesoMasivoCab->setFacturasRecurrentes($numFacturasAbiertas);
            if (isset($fechaEmisionFactura) && $fechaEmisionFactura != null) 
            {
                $entityInfoProcesoMasivoCab->setFechaEmisionFactura($fechaEmisionFactura);
            }
            $entityInfoProcesoMasivoCab->setValorDeuda($valorMontoDeuda);
            $entityInfoProcesoMasivoCab->setFormaPagoId($idFormaPago);
            $entityInfoProcesoMasivoCab->setIdsBancosTarjetas($idsBancosTarjetas);
            $entityInfoProcesoMasivoCab->setIdsOficinas($idsOficinas);
            $entityInfoProcesoMasivoCab->setEmpresaCod($idEmpresa);
            // ...
            if ($prefijoEmpresa == "TTCO")
            {
                $entityInfoProcesoMasivoCab->setEstado("Finalizada");
                $entityInfoProcesoMasivoCab->setUsrUltMod($usrCreacion);
                $entityInfoProcesoMasivoCab->setFeUltMod(new \DateTime('now'));
            }
            else
            {
                $entityInfoProcesoMasivoCab->setEstado("Pendiente");
            }
            
            $entityInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $entityInfoProcesoMasivoCab->setUsrCreacion($usrCreacion);
            $entityInfoProcesoMasivoCab->setIpCreacion($clientIp);
            // ...
            $this->emInfraestructura->persist($entityInfoProcesoMasivoCab);
            $this->emInfraestructura->flush();
            // ...
            $puntos = explode('|', $idsPuntos);
            // ... Detalles del proceso masivo
            foreach ($puntos as $puntoId) 
            {
                // ...
                $entityInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                $entityInfoProcesoMasivoDet->setPuntoId($puntoId);
                $entityInfoProcesoMasivoDet->setProcesoMasivoCabId($entityInfoProcesoMasivoCab);
                // ...
                if ($prefijoEmpresa == "TTCO") 
                {
                    $entityInfoProcesoMasivoDet->setEstado("In-Corte");
                    $entityInfoProcesoMasivoDet->setUsrUltMod($usrCreacion);
                    $entityInfoProcesoMasivoDet->setFeUltMod(new \DateTime('now'));
                    
                    $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId);
                    if (is_object($objPunto))
                    {
                        $strUltimaMilla = $this->emComercial
                                               ->getRepository('schemaBundle:InfoPunto')
                                               ->getUltimaMillaPorPunto($objPunto->getId());
                        
                        if ($strUltimaMilla == 'RAD')
                        {
                            $intContadorRegistros ++;
                            $arrayPuntos[] = array( 'contador'        => $intContadorRegistros,
                                                    'login'           => $objPunto->getLogin(),
                                                    'puntoId'         => $objPunto->getId(),
                                                    'feCreacion'      => $dateFechaProceso ,
                                                    'feUltMod'        => $dateFechaProceso,
                                                    'estado'          => $entityInfoProcesoMasivoDet->getEstado(),
                                                    'nombreTipoMedio' => 'Radio',
                                                    'observacion'     => ''
                                                  );
                        }
                    }
                } 
                else
                {
                    $entityInfoProcesoMasivoDet->setEstado("Pendiente");
                }
                $entityInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                $entityInfoProcesoMasivoDet->setUsrCreacion($usrCreacion);
                $entityInfoProcesoMasivoDet->setIpCreacion($clientIp);
                // ...
                $this->emInfraestructura->persist($entityInfoProcesoMasivoDet);
                $this->emInfraestructura->flush();
            }
            // ...
            $this->emInfraestructura->flush();
            $this->emInfraestructura->getConnection()->commit();
            // ...
            $this->emComercial->flush();
            $this->emComercial->getConnection()->commit();
            
            /* SE COMENTA WS POR MODIFICACION DE VIRGO  
            
            if ($prefijoEmpresa != "TTCO") 
            {
                $data = array(
                                "Data" => array(
                                                "tipoProceso"            => "CortarCliente",
                                                "infoProcesoMasivoCabId" => $entityInfoProcesoMasivoCab->getId()
                                               )
                             );
                $data_string = json_encode($data);
                // ...
                $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
                $mensajeWS    = $this->restClient->postJSON($this->WebServiceProcesoMasivoRestURL, $data_string, $arrayOptions);
                if ($mensajeWS['status'] == 200 && $mensajeWS['result'] != false) 
                {
                    $mensajeWS = json_decode($mensajeWS['result']);
                    $mensajeWS = $mensajeWS->Data->mensaje;
                    // ...
                    $this->loggerProcesoMasivo($mensajeWS, $entityInfoProcesoMasivoCab->getId());
                    // ...
                    if (substr($mensajeWS, 1, 1) != "1") 
                    {
                        if (substr($mensajeWS, 1, 1) != "2") 
                        {
                            throw new \Exception('No se pudo procesar la transacción - ' . $mensajeWS);
                        }
                        else
                        {
                            throw new \Exception($mensajeWS);
                        }
                    }
                }
                else
                {
                    $this->loggerProcesoMasivo( $mensajeWS['error'].$mensajeWS['status'].$mensajeWS['result'], 
                                                $entityInfoProcesoMasivoCab->getId());
                    //...
                    $subject    = 'Inconvenientes en Procesos Masivos';
                    $from       = 'telcos@telconet.ec';
                    $to         = 'sistemas@telconet.ec';
                    $twig       = 'tecnicoBundle:procesomasivo:mailerProcesoMasivo.html.twig';
                    $parameters = array(
                                         'empresa'       => $prefijoEmpresa,
                                         'procesoMasivo' => $entityInfoProcesoMasivoCab,
                                         'mensaje'       => 'La Petición de Corte fue registrada pero falló la comunicación con '.
                                                            'el servidor de Procesos Masivos',
                                         'error'  => $mensajeWS['error'],
                                         'status' => $mensajeWS['status'],
                                         'result' => $mensajeWS['result']
                                       );
                    //...
                    $this->mailer->sendTwig($subject, $from, $to, $twig, $parameters);
                }
            }
            else
            {
                if ($intContadorRegistros > 0 )
                {
                    $strAsunto           = "Proceso Masivo: ".$entityInfoProcesoMasivoCab->getId();
                    $strNombreProceso    = 'Proceso Masivo Corte';
                    $arrayParametrosMail = array (
                                                    'proceso'          => $strNombreProceso,
                                                    'ambiente'         => '',
                                                    'mensajeDetalle'   => 'Se finalizó el siguiente proceso: '.$strNombreProceso,
                                                    'usrCreacion'      => $usrCreacion,
                                                    'fechaInicio'      => $dateFechaProceso,
                                                    'fechaFin'         => $dateFechaProceso,
                                                    'puntosProcesados' => $arrayPuntos,
                                                    'empresaCod'       => 'MD'
                                                 );
                    
                    $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto, 
                                                                         null, 
                                                                         'PLANTILLA_R_MD', 
                                                                         $arrayParametrosMail, 
                                                                         '', 
                                                                         '', 
                                                                         ''
                                                                       );
                }
            }*/
            
        }
        catch (\Exception $e)
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emInfraestructura->getConnection()->close();
            $this->emComercial->getConnection()->close();
            // ...
            error_log("Error al realizar el corte masivo por página ".$e->getMessage());
            // ...
            throw $e;
        }
    }
    
    /**
     * cortarClientesMasivoPorLotes
     * 
     * Método para realizar la generación de cabecera y detalle de procesos masivos de corte
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-09-2021
     * 
     * @param array $arrayParametros [
     *                                  "strPrefijoEmpresa"         => prefijo de la empresa
     *                                  "strUsrCreacion"            => Usuario de creación
     *                                  "strIpCreacion"             => ip de creación
     *                                  "strCodEmpresa"             => código de la empresa
     *                                  "strFechaCreacionDoc"       => fecha de creación del documento
     *                                  "strTiposDocumentos"        => códigos de los tipos de documentos concatenados por ,
     *                                  "strNumDocsAbiertos"        => número de documentos abiertos
     *                                  "strValorMontoCartera"      => valor de monto de cartera
     *                                  "strIdTipoNegocio"          => id del tipo de negocio
     *                                  "strValorClienteCanal"      => 'Todos', 'SI', 'NO'
     *                                  "strNombreUltimaMilla"      => nombre de la última milla
     *                                  "strIdCicloFacturacion"     => id del ciclo de facturación
     *                                  "strIdsOficinas"            => ids de oficinas concatenados por ,
     *                                  "strIdsFormasPago"          => ids de la formas de pago concatenados por ,
     *                                  "strValorCuentaTarjeta"     => 'Cuenta', 'Tarjeta'
     *                                  "strIdsTiposCuentaTarjeta"  => ids de tipos de cuenta concatenados por ,
     *                                  "strIdsBancos"              => ids de bancos concatenados por ,
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"                => OK o ERROR,
     *                                  "mensaje"               => mensaje de error,
     *                                  "intIdSolCortePorLotes" => id de la solicitud de corte masivo por lotes
     *                                ]
     * 
     */
    public function cortarClientesMasivoPorLotes($arrayParametros)
    {
        try
        {
            $objCaractPmCabFo       = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array(  "descripcionCaracteristica" => "ID_PROCESO_MASIVO_CAB_FO",
                                                                            "estado"                    => "Activo"));
            if(!is_object($objCaractPmCabFo))
            {
                throw new \Exception("No se han podido obtener las características de los procesos masivos");
            }
            
            $arrayParamsCorte   = array("strDatabaseDsn"                => $this->objContainer->getParameter('database_dsn'),
                                        "strUserInfraestructura"        => $this->objContainer->getParameter('user_infraestructura'),
                                        "strPasswordInfraestructura"    => $this->objContainer->getParameter('passwd_infraestructura'),
                                        "strPrefijoEmpresa"             => $arrayParametros["strPrefijoEmpresa"],
                                        "strUsrCreacion"                => $arrayParametros["strUsrCreacion"],
                                        "strIpCreacion"                 => $arrayParametros["strIpCreacion"],
                                        "arrayParamsBusqueda"           => 
                                            array(
                                                    "strCodEmpresa"             => $arrayParametros["strCodEmpresa"],
                                                    "strFechaCreacionDoc"       => $arrayParametros["strFechaCreacionDoc"],
                                                    "strTiposDocumentos"        => $arrayParametros["strTiposDocumentos"],
                                                    "strNumDocsAbiertos"        => $arrayParametros["strNumDocsAbiertos"],
                                                    "strValorMontoCartera"      => $arrayParametros["strValorMontoCartera"],
                                                    "strIdTipoNegocio"          => $arrayParametros["strIdTipoNegocio"],
                                                    "strValorClienteCanal"      => $arrayParametros["strValorClienteCanal"],
                                                    "strNombreUltimaMilla"      => $arrayParametros["strNombreUltimaMilla"],
                                                    "strIdCicloFacturacion"     => $arrayParametros["strIdCicloFacturacion"],
                                                    "strIdsOficinas"            => $arrayParametros["strIdsOficinas"],
                                                    "strIdsFormasPago"          => $arrayParametros["strIdsFormasPago"],
                                                    "strValorCuentaTarjeta"     => $arrayParametros["strValorCuentaTarjeta"],
                                                    "strIdsTiposCuentaTarjeta"  => $arrayParametros["strIdsTiposCuentaTarjeta"],
                                                    "strIdsBancos"              => $arrayParametros["strIdsBancos"],
                                                    "strFechaLimActivacion"     => $arrayParametros["strFechaLimActivacion"],
                                                    "arrayFinalIdExcluidas"     => $arrayParametros["arrayFinalIdExcluidas"],
                                                    "strProceso"                => $arrayParametros["strProceso"]
                                                )
                                    );
            $arrayRespuestaCorteMasivoPorLotes      = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                              ->creaCorteMasivoPorLotes($arrayParamsCorte);
            $strStatusRespuestaCorteMasivoPorLotes  = $arrayRespuestaCorteMasivoPorLotes["status"];
            $strMensajeRespuestaCorteMasivoPorLotes = $arrayRespuestaCorteMasivoPorLotes["mensaje"];
            if($strStatusRespuestaCorteMasivoPorLotes === "ERROR")
            {
                throw new \Exception($strMensajeRespuestaCorteMasivoPorLotes);
            }
            $intIdSolCortePorLotes = $arrayRespuestaCorteMasivoPorLotes["idSolCortePorLotes"];
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            $intIdSolCortePorLotes = 0;
        }
        $arrayRespuesta = array("status"                => $strStatus,
                                "mensaje"               => $strMensaje,
                                "intIdSolCortePorLotes" => $intIdSolCortePorLotes);
        return $arrayRespuesta;
    }
    
    /**
     * ejecutaCorteClientesMasivoCoRad
     * 
     * Método para ejecutar el corte masivo de clientes con Cobre o Radio
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 20-09-2021
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 21-03-2022 Se cambia el orden de ejecución del comando java que se ejecuta, ya que actualmente 
     *                         no estaba dando de baja a todos los registros de cobre/radio
     * 
     * @param array $arrayParametros [
     *                                  "intIdSolCortePorLotes"     => id de la solicitud SOLICITUD CORTE MASIVO POR LOTES
     *                                  "objCaractPmCabCoRad"       => objeto de la característica ID_PROCESO_MASIVO_CAB_CO_RAD
     *                                  "strNumDocsAbiertos"        => número de documentos abiertos
     *                                  "strValorMontoCartera"      => valor de monto de cartera
     *                                  "strIdsOficinas"            => ids de oficinas concatenados por ,
     *                                  "strIdsFormasPago"          => ids de la formas de pago concatenados por ,
     *                                  "strUsrCreacion"            => Usuario de creación
     *                                  "strIpCreacion"             => ip de creación
     *                                ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje de error
     *                                ]
     * 
     */
    public function ejecutaCorteClientesMasivoCoRad($arrayParametros)
    {
        $intIdSolCortePorLotes  = $arrayParametros["intIdSolCortePorLotes"];
        $strNumDocsAbiertos     = $arrayParametros["strNumDocsAbiertos"];
        $strValorMontoCartera   = $arrayParametros["strValorMontoCartera"];
        $strIdsOficinas         = $arrayParametros["strIdsOficinas"];
        $strIdsFormasPago       = $arrayParametros["strIdsFormasPago"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strIpCreacion          = $arrayParametros["strIpCreacion"];
        $arrayIdsPuntosCoRad    = array();
        $this->emComercial->beginTransaction();
        $this->emInfraestructura->beginTransaction();
        try
        {
            $objCaractPmCabCoRad    = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findOneBy(array(  "descripcionCaracteristica" => "ID_PROCESO_MASIVO_CAB_CO_RAD",
                                                                            "estado"                    => "Activo"));
            if(!is_object($objCaractPmCabCoRad))
            {
                throw new \Exception("No se han podido obtener las características de los procesos masivos");
            }
            
            $objSolicitudCortePorLotes  = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')->find($intIdSolCortePorLotes);
            if(!is_object($objSolicitudCortePorLotes))
            {
                throw new \Exception("No se ha podido obtener la solicitud para corte masivo por lotes");
            }
            
            $arrayDetsSolCaractPmCabCoRad   = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolCaract')
                                                                ->findBy(array( "detalleSolicitudId"=> $objSolicitudCortePorLotes,
                                                                                "caracteristicaId"  => $objCaractPmCabCoRad,
                                                                                "estado"            => "PorEjecutar"
                                                                                ));
            if(isset($arrayDetsSolCaractPmCabCoRad) && !empty($arrayDetsSolCaractPmCabCoRad))
            {
                foreach($arrayDetsSolCaractPmCabCoRad as $objDetSolCaractPmCabCoRad)
                {
                    $intIdPmCabCoRad    = $objDetSolCaractPmCabCoRad->getValor();
                    
                    $objDetSolHistIniCortePorLotes = new InfoDetalleSolHist();
                    $objDetSolHistIniCortePorLotes->setDetalleSolicitudId($objSolicitudCortePorLotes);
                    $objDetSolHistIniCortePorLotes->setIpCreacion($strIpCreacion);
                    $objDetSolHistIniCortePorLotes->setFeCreacion(new \DateTime('now'));
                    $objDetSolHistIniCortePorLotes->setUsrCreacion($strUsrCreacion);
                    $objDetSolHistIniCortePorLotes->setEstado($objSolicitudCortePorLotes->getEstado());
                    $objDetSolHistIniCortePorLotes->setObservacion("Se inicia la ejecución del proceso masivo de corte de Cobre/Radio #"
                                                                   .$intIdPmCabCoRad);
                    $this->emComercial->persist($objDetSolHistIniCortePorLotes);
                    $this->emComercial->flush();

                    $objPmCabCoRad      = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoCab')->find($intIdPmCabCoRad);
                    if(is_object($objPmCabCoRad))
                    {
                        $arrayPmDetsPorCabCoRad = $this->emInfraestructura->getRepository('schemaBundle:InfoProcesoMasivoDet')
                                                                          ->findBy(array('procesoMasivoCabId' => $objPmCabCoRad->getId()));
                        if(isset($arrayPmDetsPorCabCoRad) && !empty($arrayPmDetsPorCabCoRad))
                        {
                            $strIdsPuntosCoRad = "";
                            foreach($arrayPmDetsPorCabCoRad as $objPmDetPorCabCoRad)
                            {
                                $intIdPuntoCoRad = $objPmDetPorCabCoRad->getPuntoId();
                                if(isset($intIdPuntoCoRad) && !empty($intIdPuntoCoRad))
                                {
                                    $objPuntoCoRad = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($intIdPuntoCoRad);
                                    if(is_object($objPuntoCoRad))
                                    {
                                        $strIdsPuntosCoRad .= $objPuntoCoRad->getId(). '|';
                                    }
                                }
                                $objPmDetPorCabCoRad->setEstado("In-Corte");
                                $objPmDetPorCabCoRad->setUsrUltMod($strUsrCreacion);
                                $objPmDetPorCabCoRad->setFeUltMod(new \DateTime('now'));
                                $this->emInfraestructura->persist($objPmDetPorCabCoRad);
                                $this->emInfraestructura->flush();
                            }
                            if(isset($strIdsPuntosCoRad) && !empty($strIdsPuntosCoRad))
                            {
                                $arrayIdsPuntosCoRad[] = substr($strIdsPuntosCoRad, 0, -1);
                            }
                        }
                        $objPmCabCoRad->setEstado("Finalizada");
                        $objPmCabCoRad->setUsrUltMod($strUsrCreacion);
                        $objPmCabCoRad->setFeUltMod(new \DateTime('now'));
                        $this->emInfraestructura->persist($objPmCabCoRad);
                        $this->emInfraestructura->flush();
                        $this->emInfraestructura->commit();
                    }
                    
                    $objDetSolCaractPmCabCoRad->setEstado("Ejecutada");
                    $objDetSolCaractPmCabCoRad->setUsrUltMod($strUsrCreacion);
                    $objDetSolCaractPmCabCoRad->setFeUltMod(new \DateTime('now'));
                    $this->emComercial->persist($objDetSolCaractPmCabCoRad);
                    $this->emComercial->flush();
                    
                    $objDetSolHistCortePorLotes = new InfoDetalleSolHist();
                    $objDetSolHistCortePorLotes->setDetalleSolicitudId($objSolicitudCortePorLotes);
                    $objDetSolHistCortePorLotes->setIpCreacion($strIpCreacion);
                    $objDetSolHistCortePorLotes->setFeCreacion(new \DateTime('now'));
                    $objDetSolHistCortePorLotes->setUsrCreacion($strUsrCreacion);
                    $objDetSolHistCortePorLotes->setEstado($objSolicitudCortePorLotes->getEstado());
                    $objDetSolHistCortePorLotes->setObservacion("El proceso masivo de corte de Cobre/Radio #"
                                                                .$intIdPmCabCoRad.' ha ejecutado todos sus detalles asociados');
                    $this->emComercial->persist($objDetSolHistCortePorLotes);
                    $this->emComercial->flush();
                    $this->emComercial->commit();
                }
            }
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus  = "ERROR";
            $strMensaje = $e->getMessage();
            
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->rollback();
            }
            $this->emInfraestructura->close();
            
            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->rollback();
            }
            $this->emComercial->close();
        }
        
        if($strStatus === "OK" && isset($arrayIdsPuntosCoRad) && !empty($arrayIdsPuntosCoRad))
        {
            foreach($arrayIdsPuntosCoRad as $strIdsPuntosCoRadComando)
            {
                $objDate                        = date("Y-m-d H:i:s");            
                $strComandoCortePorLoteCoRad    = "nohup java -jar -Djava.security.egd=file:/dev/./urandom "
                                                  ."/home/telcos/src/telconet/tecnicoBundle/batch/ttco_corteMasivo.jar '" 
                                                  . $strIdsPuntosCoRadComando . "' "
                                                  . " '" . $strValorMontoCartera . "|" . $strNumDocsAbiertos . "|" 
                                                  . $strIdsOficinas . "|" . $strIdsFormasPago . "' "
                                                  . " '" . $strUsrCreacion . "' '" . $strIpCreacion . "' "
                                                  . " >> /home/telcos/src/telconet/tecnicoBundle/batch/corteMasivo-$objDate.txt &";
                shell_exec($strComandoCortePorLoteCoRad);
            }
        }
        
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * 
     * Método para guardar los registros con la información de los servicios TelcoHome que se cortarán o reactivarán de manera masiva
     * 
     * @param Array $arrayParametros [
     *                                  "strServicios"      => cadena con los ids de los servicios que se cortarán o reactivarán
     *                                  "strTipoProceso"    => tipo de la cabecera del proceso masivo
     *                                  "intTotalServicios" => total de servicios
     *                                  "strCodEmpresa"     => código de la empresa
     *                                  "strUsrCreacion"    => usuario de creación
     *                                  "strIpCreacion"     => ip de creación
     *                                ]
     * @return string Retorna el estado de la generación del proceso masivo para servicios TelcoHome
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 21-03-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 04-05-2021 Se modifica el uso de la función loggerProcesoMasivo y se lo cambia por un error_log, ya que con eso sería suficiente
     *                         puesto que el error es devuelto con un throw
     * 
     */
    public function guardarServiciosCorteReactivacionTelcoHome($arrayParametros) 
    {
        $strServicios       = $arrayParametros["strServicios"];
        $strTipoProceso     = $arrayParametros["strTipoProceso"];
        $intTotalServicios  = $arrayParametros["intTotalServicios"];
        $strCodEmpresa      = $arrayParametros["strCodEmpresa"];
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
        $strIpCreacion      = $arrayParametros["strIpCreacion"];
        $this->emInfraestructura->beginTransaction();
        try 
        {
            $objInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
            $objInfoProcesoMasivoCab->setTipoProceso($strTipoProceso);
            $objInfoProcesoMasivoCab->setCantidadPuntos($intTotalServicios);
            $objInfoProcesoMasivoCab->setEmpresaCod($strCodEmpresa);
            $objInfoProcesoMasivoCab->setEstado("Pendiente");
            $objInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $objInfoProcesoMasivoCab->setUsrCreacion($strUsrCreacion);
            $objInfoProcesoMasivoCab->setIpCreacion($strIpCreacion);
            $this->emInfraestructura->persist($objInfoProcesoMasivoCab);
            $this->emInfraestructura->flush();
            
            $arrayServiciosCliente = explode(',', $strServicios);
            foreach($arrayServiciosCliente as $intIdServicio) 
            {
                $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
                if(is_object($objServicio))
                {
                    $objInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                    $objInfoProcesoMasivoDet->setServicioId($intIdServicio);
                    $objInfoProcesoMasivoDet->setPuntoId($objServicio->getPuntoId()->getId());
                    $objInfoProcesoMasivoDet->setProcesoMasivoCabId($objInfoProcesoMasivoCab);
                    $objInfoProcesoMasivoDet->setEstado("Pendiente");
                    $objInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                    $objInfoProcesoMasivoDet->setUsrCreacion($strUsrCreacion);
                    $objInfoProcesoMasivoDet->setIpCreacion($strIpCreacion);
                    $this->emInfraestructura->persist($objInfoProcesoMasivoDet);
                    $this->emInfraestructura->flush();
                }
            }
            $this->emInfraestructura->flush();
            $this->emInfraestructura->commit();
            $strStatus = "OK";
        }
        catch (\Exception $e)
        {
            $strStatus = "Ha ocurrido un error. Por favor notificar a Sistemas";
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->rollback();
            }
            
            $this->emInfraestructura->close();
            error_log("Error al guardar procesos masivos por corte/reactivación de TelcoHome ".$e->getMessage().", Ids de servicios: ".$strServicios);
            throw $e;
        }
        return $strStatus;
    }

    /**
     * cortaCliente, inserta punto en las tablas de procesos masivos de clientes a cortar.
     * El proceso de corte (proyecto en java) lee de la tabla de procesos masivos para realizar el corte de los clientes
     * 
     * @param Array $arrayRequest ['strPunto' => Contiene los ID's puntos concatenados por un "|" para poderlos iterar]
     * @return string Retorna un OK
     * 
     * @author Alexander Samaniego <awsamaniego@telconet.ec>
     * @version 1.0 28/03/2019
     * 
     */
    public function cortaCliente($arrayRequest) 
    {
        $intIdEmpresa           = $arrayRequest['intIdEmpresa'];
        $strPrefijoEmpresa      = $arrayRequest['strPrefijoEmpresa'];
        $strUsrCreacion         = $arrayRequest['usrCreacion'];
        $strNumFactAbiertas     = $arrayRequest['strNumFactAbiertas'];
        $strFechaEmisionFact    = $arrayRequest['strFechaEmisionFact'];
        $intMontoDeuda          = $arrayRequest['intMontoDeuda'];
        $intIdFormaPago         = $arrayRequest['intIdFormaPago'];
        $intIdBancosTarjetas    = $arrayRequest['intIdBancosTarjetas'];
        $intIdOficina           = $arrayRequest['intIdOficina'];
        $strIp                  = $arrayRequest['strIp'];
        $strPunto               = $arrayRequest['strPunto'];

        $arrayPuntosUM          = $this->obtenerPuntosPorUltimaMilla($strPunto);
        $strIdsPuntosFO         = $arrayPuntosUM['FO'];
        $strIdsPuntosCR         = $arrayPuntosUM['CR'];

        $intTotalFO     = $arrayPuntosUM['totalFO'];
        $intTotalCoRa   = $arrayPuntosUM['totalCoRa'];
        if($strIdsPuntosCR != '')
        {
            $objFecha   = date("Y-m-d");
            $strComando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_corteMasivo.jar '"
                        . $strIdsPuntosCR . "' " . " '" . $intMontoDeuda . "|" . $strNumFactAbiertas . "|" . $intIdOficina . "|" . $intIdFormaPago . "' "
                        . " '" . $strUsrCreacion . "' '" . $strIp . "' "
                        . " >> /home/telcos/src/telconet/tecnicoBundle/batch/corteMasivo-$objFecha.txt &";
            shell_exec($strComando);
            $arrayParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                   ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $strPrefijoEmpresa, 'CO', "", "");
            if($arrayParametro)
            {
                $strPrefijoEmpresaEqui = $arrayParametro['valor3'];
                $objEmpresa            = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                                              ->findOneByPrefijo($strPrefijoEmpresaEqui);
                if(is_object($objEmpresa))
                {
                    $intIdEmpresa = $objEmpresa->getId();
                }
            }
            $this->guardarPuntosPorCorteMasivo($strPrefijoEmpresaEqui, $intIdEmpresa, $strNumFactAbiertas, $strFechaEmisionFact,
                                               $intMontoDeuda, $intIdFormaPago, $intIdBancosTarjetas, $intIdOficina, $strIdsPuntosCR,
                                               $intTotalCoRa, $strUsrCreacion, $strIp);
        }

        if($strIdsPuntosFO != '')
        {
            $arrayParametro = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                             ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $strPrefijoEmpresa, 'FO', "", "");
            if($arrayParametro)
            {
                $strPrefijoEmpresaEqui = $arrayParametro['valor3'];
                $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')
                                   ->findOneByPrefijo($strPrefijoEmpresaEqui);
                if(is_object($objEmpresa))
                {
                    $intIdEmpresa = $objEmpresa->getId();
                }
            }
            $this->guardarPuntosPorCorteMasivo($strPrefijoEmpresaEqui, $intIdEmpresa, $strNumFactAbiertas, $strFechaEmisionFact,
                                               $intMontoDeuda, $intIdFormaPago, $intIdBancosTarjetas, $intIdOficina, $strIdsPuntosFO,
                                               $intTotalFO, $strUsrCreacion, $strIp);
        }
        return ['status' => '200', 'message' => 'Puntos guardados'];
    }

    /**
     * guardarPuntosPorReactivacionMasivo
     * 
     * Metodo para realizar la generación de cabecera y detalle de procesos masivos de reactivacion
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 12-01-2017
     * @since 1.0
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 07-02-2018 Se regularizan cambios en caliente, se desactiva verificacion SSL
     * @since 1.1
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.3 18-10-2018
     * @since 1.2 - Se agregan registros en logs, para tener un historial de lo realizado en la función.
     *
     * @param  - $canalPagoLineaId      Parametro que indica el identificador del canal de pago en linea
     *         - $prefijoEmpresa        Cadena de caracteres que indica el prefijo de la empresa utilizado en el proceso
     *         - $idEmpresa             Cadena de caracteres que indica el id de la empresa
     *         - $fechaCorteDesde       Parametro que indica la fecha de corte desde a procesar
     *         - $fechaCorteHasta       Parametro que indica la fecha de corte hasta a procesar
     *         - $valorMontoDeuda       Parametro que indica el valor del monto de la deuda a procesar
     *         - $idsOficinas           Parametro que indica el identificador de las oficinas a procesar
     *         - $idsPuntos             Parametro que indica los identificadores de los puntos a procesar
     *         - $cantidadPuntos        Parametro que indica la cantidad de puntos a procesar
     *         - $usrCreacion           Cadena de caracteres que indica el usuario que procesa el corte masivo
     *         - $clientIp              Cadena de caracteres que indica la ip del usuario que procesa el corte masivo
     *         - $pagoId                Parametro que indica el identificador del pago
     *         - $pagoLineaId           Parametro que indica el identificador del pago en linea
     *         - $recaudacionId         Parametro que indica el identificador de la recaudacion
     *         - $debitoId              Parametro que indica el identificador del debito
     */
    public function guardarPuntosPorReactivacionMasivo( $canalPagoLineaId, 
                                                        $prefijoEmpresa, 
                                                        $idEmpresa, 
                                                        $fechaCorteDesde, 
                                                        $fechaCorteHasta, 
                                                        $valorMontoDeuda,
                                                        $idsOficinas, 
                                                        $idsPuntos, 
                                                        $cantidadPuntos, 
                                                        $usrCreacion, 
                                                        $clientIp, 
                                                        $pagoId, 
                                                        $pagoLineaId, 
                                                        $recaudacionId, 
                                                        $debitoId ) 
    {
        $intContadorRegistros = 0;
        $dateFechaProceso     = new \DateTime('now');
        $strNombreProceso     = '';
        $this->emInfraestructura->beginTransaction();
        $this->emComercial->beginTransaction();
        try 
        {
            /*Se almacenan los datos recibidos*/
            $this->loggerProcesoMasivo('Función: guardarPuntosPorReactivacionMasivo-ProcesoMasivoService', 'Datos Recibidos:'.
                                       ' |Puntos:'.json_encode($idsPuntos) .' |Pref:' . $prefijoEmpresa . ' |Emp:'.$idEmpresa.
                                       ' |Usr:' .$usrCreacion .' |Ip:'. $clientIp. ' |DebId:' .$debitoId);
            
            $dateFechaProceso = $dateFechaProceso->format('d-m-Y_H:i:s');
            $entityInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
            $entityInfoProcesoMasivoCab->setTipoProceso("ReconectarCliente");
            $entityInfoProcesoMasivoCab->setCantidadPuntos($cantidadPuntos);
            $entityInfoProcesoMasivoCab->setEmpresaCod($idEmpresa);
            $entityInfoProcesoMasivoCab->setValorDeuda($valorMontoDeuda);
            $entityInfoProcesoMasivoCab->setIdsOficinas($idsOficinas);
            if ($prefijoEmpresa == "TTCO") 
            {
                $entityInfoProcesoMasivoCab->setEstado("Activo");
                $entityInfoProcesoMasivoCab->setUsrUltMod($usrCreacion);
                $entityInfoProcesoMasivoCab->setFeUltMod(new \DateTime('now'));
            }
            else
            {
                $entityInfoProcesoMasivoCab->setEstado("Pendiente");
            }
            $entityInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $entityInfoProcesoMasivoCab->setUsrCreacion($usrCreacion);
            $entityInfoProcesoMasivoCab->setIpCreacion($clientIp);
            $entityInfoProcesoMasivoCab->setCanalPagoLineaId($canalPagoLineaId);
            $entityInfoProcesoMasivoCab->setPagoId($pagoId);
            $entityInfoProcesoMasivoCab->setPagoLineaId($pagoLineaId);
            $entityInfoProcesoMasivoCab->setRecaudacionId($recaudacionId);
            $entityInfoProcesoMasivoCab->setDebitoId($debitoId);
            $this->emInfraestructura->persist($entityInfoProcesoMasivoCab);
            $this->emInfraestructura->flush();
            
            /*Se almacena registro de InfoProcesoMasivoCab*/
            $this->loggerProcesoMasivo('Func: guardarPuntosPorReactivacionMasivo-InfoProcesoMasivoCab', 'Id: '.$entityInfoProcesoMasivoCab->getId().
                                       ' |Proceso:'.$entityInfoProcesoMasivoCab->getTipoProceso() . 
                                       ' |Estado:' .$entityInfoProcesoMasivoCab->getEstado());
            
            if (is_array($idsPuntos)) 
            {
                $puntos = $idsPuntos;
                $idsPuntos = implode('|', $puntos);
            }
            else
            {
                $puntos = explode('|', $idsPuntos);
            }
            foreach ($puntos as $puntoId) 
            {
                $entityInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                $entityInfoProcesoMasivoDet->setPuntoId($puntoId);
                $entityInfoProcesoMasivoDet->setProcesoMasivoCabId($entityInfoProcesoMasivoCab);
                if ($prefijoEmpresa == "TTCO") 
                {
                    $entityInfoProcesoMasivoDet->setEstado("Activo");
                    $entityInfoProcesoMasivoDet->setUsrUltMod($usrCreacion);
                    $entityInfoProcesoMasivoDet->setFeUltMod(new \DateTime('now'));
                    
                    $objPunto = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId);
                    if (is_object($objPunto))
                    {
                        $strUltimaMilla = $this->emComercial
                                               ->getRepository('schemaBundle:InfoPunto')
                                               ->getUltimaMillaPorPunto($objPunto->getId());
                        
                        if ($strUltimaMilla == 'RAD')
                        {
                            $intContadorRegistros ++;
                            $arrayPuntos[] = array( 'contador'        => $intContadorRegistros,
                                                    'login'           => $objPunto->getLogin(),
                                                    'puntoId'         => $objPunto->getId(),
                                                    'feCreacion'      => $dateFechaProceso ,
                                                    'feUltMod'        => $dateFechaProceso,
                                                    'estado'          => $entityInfoProcesoMasivoDet->getEstado(),
                                                    'nombreTipoMedio' => 'Radio',
                                                    'observacion'     => ''
                                                  );
                        }
                    }
                }
                else
                {
                    $entityInfoProcesoMasivoDet->setEstado("Pendiente");
                }
                $entityInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                $entityInfoProcesoMasivoDet->setUsrCreacion($usrCreacion);
                $entityInfoProcesoMasivoDet->setIpCreacion($clientIp);
                $this->emInfraestructura->persist($entityInfoProcesoMasivoDet);
                $this->emInfraestructura->flush();
                
                $this->loggerProcesoMasivo('Func:guardarPuntosPorReactivacionMasivo-InfoProcesoMasivoDet','Id:'.$entityInfoProcesoMasivoDet->getId().
                                           ' |Punto:' . $entityInfoProcesoMasivoDet->getPuntoId() .
                                           ' |Estado:' . $entityInfoProcesoMasivoCab->getEstado());
                
            }
            
            $this->emInfraestructura->flush();
            $this->emInfraestructura->getConnection()->commit();
            $this->emComercial->flush();
            $this->emComercial->getConnection()->commit();
            
            /**
             * LLAMADA AL WEBSERVICES REST
             */
            /* SE COMENTA LLAMADA DE WS POR CAMBIO EN FUNCIONAMIENTO DE LOS VIRGOS
            if ($prefijoEmpresa != "TTCO") 
            {
                $data = array(
                                "Data" => array(
                                                "tipoProceso"            => "ReconectarCliente",
                                                "infoProcesoMasivoCabId" => $entityInfoProcesoMasivoCab->getId()
                                               )
                             );
                $data_string  = json_encode($data);
                $arrayOptions = array(CURLOPT_SSL_VERIFYPEER => false);
                $mensajeWS    = $this->restClient->postJSON($this->WebServiceProcesoMasivoRestURL, $data_string, $arrayOptions);
                if ($mensajeWS['status'] == 200 && $mensajeWS['result'] != false) 
                {
                    $mensajeWS = json_decode($mensajeWS['result']);
                    $mensajeWS = $mensajeWS->Data->mensaje;
                    $this->loggerProcesoMasivo($mensajeWS, $entityInfoProcesoMasivoCab->getId());
                    if (substr($mensajeWS, 1, 1) != "1") 
                    {
                        if (substr($mensajeWS, 1, 1) != "2") 
                        {
                            throw new \Exception('No se pudo procesar la transacción - ' . $mensajeWS);
                        }
                        else
                        {
                            throw new \Exception($mensajeWS);
                        }
                    }
                }
                else
                {
                    $this->loggerProcesoMasivo($mensajeWS['error'].$mensajeWS['status'].$mensajeWS['result'], $entityInfoProcesoMasivoCab->getId());
                    $subject    = 'Inconvenientes en Procesos Masivos';
                    $from       = 'telcos@telconet.ec';
                    $to         = 'sistemas@telconet.ec';
                    $twig       = 'tecnicoBundle:procesomasivo:mailerProcesoMasivo.html.twig';
                    $parameters = array( 'empresa'       => $prefijoEmpresa,
                                         'procesoMasivo' => $entityInfoProcesoMasivoCab,
                                         'mensaje'       => 'La Petición de Reactivación fue registrada pero falló la comunicación '.
                                                            'con el servidor de Procesos Masivos',
                                         'error'         => $mensajeWS['error'],
                                         'status'        => $mensajeWS['status'],
                                         'result'        => $mensajeWS['result']
                                       );
                    $this->mailer->sendTwig($subject, $from, $to, $twig, $parameters);
                }
             }
             else
             {
                 if ($intContadorRegistros > 0 )
                 {
                     $strAsunto           = "Proceso: ".$entityInfoProcesoMasivoCab->getId();
                     $strNombreProceso    = 'Proceso Reactivación';
                     $arrayParametrosMail = array (
                                                    'proceso'          => $strNombreProceso,
                                                    'ambiente'         => '',
                                                    'mensajeDetalle'   => 'Se finalizó el siguiente proceso: '.$strNombreProceso,
                                                    'usrCreacion'      => $usrCreacion,
                                                    'fechaInicio'      => $dateFechaProceso,
                                                    'fechaFin'         => $dateFechaProceso,
                                                    'puntosProcesados' => $arrayPuntos,
                                                    'empresaCod'       => 'MD'
                                                  );

                     $this->serviceEnvioPlantilla->generarEnvioPlantilla( $strAsunto, 
                                                                          null, 
                                                                          'PLANTILLA_R_MD', 
                                                                          $arrayParametrosMail, 
                                                                          '', 
                                                                          '', 
                                                                          ''
                                                                        );
                }
            }
           */ 
            return $entityInfoProcesoMasivoCab->getId();
        }
        catch (\Exception $e) 
        {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) 
            {
                $this->emInfraestructura->getConnection()->rollback();
            }
            if ($this->emComercial->getConnection()->isTransactionActive()) 
            {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emInfraestructura->getConnection()->close();
            $this->emComercial->getConnection()->close();
            $this->loggerProcesoMasivo('Error: ProcesosMasivosService.guardarPuntosPorReactivacionMasivo','Excepción: '.$e->getMessage().
                                       ' |Usr:'.$usrCreacion .' |Puntos:' . $idsPuntos);
            throw $e;
        }
    }

    public function guardarServiciosPorCambioPlan($prefijoEmpresa, $idEmpresa, $idsOficinas, $newPlanId, $newPlanValor, $idsServicios, $cantidadServicios, $usrCreacion, $clientIp) {
        $this->emInfraestructura->beginTransaction();
        $this->emComercial->beginTransaction();
        try {
            $servicios = explode('|', $idsServicios);
            // ....
            $entityInfoProcesoMasivoCab = new InfoProcesoMasivoCab();
            $entityInfoProcesoMasivoCab->setTipoProceso("CambioPlan");
            // ...
            $entityInfoProcesoMasivoCab->setCantidadPuntos($cantidadServicios);
            $entityInfoProcesoMasivoCab->setEmpresaCod($idEmpresa);
            // ...
            $entityInfoProcesoMasivoCab->setPlanId($newPlanId);
            $entityInfoProcesoMasivoCab->setPlanValor($newPlanValor);
            // ...
            $entityInfoProcesoMasivoCab->setIdsOficinas($idsOficinas);
            // ...
            $entityInfoProcesoMasivoCab->setEstado("Pendiente");
            $entityInfoProcesoMasivoCab->setFeCreacion(new \DateTime('now'));
            $entityInfoProcesoMasivoCab->setUsrCreacion($usrCreacion);
            $entityInfoProcesoMasivoCab->setIpCreacion($clientIp);
            // ...
            $this->emInfraestructura->persist($entityInfoProcesoMasivoCab);
            $this->emInfraestructura->flush();
            // ..
            foreach ($servicios as $servicioId) {
                // ...
                $entityInfoProcesoMasivoDet = new InfoProcesoMasivoDet();
                $entityInfoProcesoMasivoDet->setServicioId($servicioId);
                $entityInfoProcesoMasivoDet->setProcesoMasivoCabId($entityInfoProcesoMasivoCab);
                // ...
                $entityInfoProcesoMasivoDet->setEstado("Pendiente");
                $entityInfoProcesoMasivoDet->setFeCreacion(new \DateTime('now'));
                $entityInfoProcesoMasivoDet->setUsrCreacion($usrCreacion);
                $entityInfoProcesoMasivoDet->setIpCreacion($clientIp);
                // ...
                $this->emInfraestructura->persist($entityInfoProcesoMasivoDet);
                $this->emInfraestructura->flush();
            }
            // ...
            $this->emInfraestructura->flush();
            $this->emInfraestructura->getConnection()->commit();
            // ...
            $this->emComercial->flush();
            $this->emComercial->getConnection()->commit();
        } catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()) {
                $this->emInfraestructura->getConnection()->rollback();
            }
            if ($this->emComercial->getConnection()->isTransactionActive()) {
                $this->emComercial->getConnection()->rollback();
            }
            $this->emInfraestructura->getConnection()->close();
            $this->emComercial->getConnection()->close();
            throw $e;
        }
    }
    
    /**
    * @author Héctor Lozano <hlozano@telconet.ec>
    * @version 1.0 25-10-2018
    * @since 1.0 - Se cambia el directorio para registros de logs.
    * 
    * @author Hector Lozano <hlozano@telconet.ec>
    * @version 1.1 09-05-2022 
    * @since 1.1  Se modifica función para realizar el guardado de los logs en la tabla INFO_ERROR del esquema DB_GENERAL. 
    */
    public function loggerProcesoMasivo($strMensaje1, $strMensaje2)
    {
        
        date_default_timezone_set('America/Guayaquil');
        $strMsgLog = '[' . date('Y-m-d H:i:s') . '] ' . ' [' . $strMensaje1 . '] ' . ' [' . $strMensaje2 . '] ';
       
        $this->serviceUtil->insertError( 'Telcos+','loggerProcesoMasivo', $strMsgLog, 'logProcMasivo', '127.0.0.1' );
    }

    /**
     * reactivarServiciosPorRecaudacion
     * Permite reactivar servicios obtenidos en una especifica recaudacion
     * Actualizacion (version 1.1): Se envia parametro de id de empresa en la funcion reactivarServiciosPorRecaudacion 
     * @param InfoRecaudacion $entityRecaudacion
     * @param string $prefijoEmpresa
     * @version 1.1 09/03/2016
     * @autor amontero@telconet.ec
     * @since 1.0
     * @return array con dos valores: boolean 'isReactivado' indica si se ha reactivado puntos o servicios, integer 'procesoMasivoId' id del proceso masivo creado  
     */
    public function reactivarServiciosPorRecaudacion(InfoRecaudacion $entityRecaudacion, $prefijoEmpresa)
    {

        $isReactivado = false;
        $procesoMasivoId = null;
        $idEmpresa = $entityRecaudacion->getEmpresaCod();
        $prefijoEmpresaSesion = $prefijoEmpresa;

        $arrayPuntosPorUltimaMilla = $this->obtenerPuntosParaReactivar($entityRecaudacion->getId(), '',$idEmpresa);

        if(!empty($arrayPuntosPorUltimaMilla))
        {
            $strIdsPuntosFO = $arrayPuntosPorUltimaMilla['FO']; //Puntos con Fibra Optica
            $strIdsPuntosCR = $arrayPuntosPorUltimaMilla['CR']; //Puntos con Radio/Cobre

            $intTotalFO = $arrayPuntosPorUltimaMilla['totalFO']; //Total de puntos con Fibra a cortar

            $intTotalCR = $arrayPuntosPorUltimaMilla['totalCoRa']; //Total de puntos con Cobre/radio a cortar

            if($strIdsPuntosCR != '')
            {   
                //realizo validacion para obtener la empresa para el flujo
                $parametros = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                     ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'CO', "", "","","");
                if($parametros)
                {
                    $prefijoEmpresa = $parametros['valor3'];
                    $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                    $idEmpresa = $objEmpresa->getId(); 
                }

                //reactivo los servicios
                $isReactivado = $this->reactivarServiciosTTCO($strIdsPuntosCR, $entityRecaudacion->getUsrCreacion(), 
                    $entityRecaudacion->getIpCreacion(), 'recaudaciones');
                //guardo en la tabla de procesos masivos
                $procesoMasivoId = $this->guardarPuntosPorReactivacionMasivo(null, $prefijoEmpresa, $idEmpresa, null, null, 0, null, $strIdsPuntosCR,
                    $intTotalCR, $entityRecaudacion->getUsrCreacion(), $entityRecaudacion->getIpCreacion(), null, null, $entityRecaudacion->getId(), 
                    null);
            }

            if($strIdsPuntosFO != '')
            {
                
                //realizo validacion para obtener la empresa para el flujo
                $parametros = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                     ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'FO', "", "","","");
                if($parametros)
                {
                    $prefijoEmpresa = $parametros['valor3'];
                    $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                    $idEmpresa = $objEmpresa->getId(); 
                }
                //guardo en la tabla de procesos masivos
                $procesoMasivoId = $this->guardarPuntosPorReactivacionMasivo(null, $prefijoEmpresa, $idEmpresa, null, null, 0, null, $strIdsPuntosFO, 
                    $intTotalFO, $entityRecaudacion->getUsrCreacion(), $entityRecaudacion->getIpCreacion(), null, null, $entityRecaudacion->getId(),
                    null);
            }

            if($procesoMasivoId)
            {
                $entityRecaudacion->setProcesoMasivoId($procesoMasivoId);
                $this->emFinanciero->persist($entityRecaudacion);
                $this->emFinanciero->flush();

                $isReactivado = true;
            }
        }
        return array('isReactivado' => $isReactivado, 'procesoMasivoId' => $procesoMasivoId);
    }

    /**
     * reactivarServiciosPorPagoLinea
     * Permite reactivar servicios obtenidos en un especifico proceso de pago en linea
     * Actualizacion (version 1.1): Se envia parametro de id de empresa en la funcion reactivarServiciosPorPagoLinea 
     * @version 1.1 09/03/2016
     * @since 1.0
     * @param InfoPagoLinea $entityPagoLinea
     * @param string $prefijoEmpresa
     * @return array con dos valores: boolean 'isReactivado' indica si se ha reactivado puntos o servicios, integer 'procesoMasivoId' id del proceso masivo creado
     */
    public function reactivarServiciosPorPagoLinea(InfoPagoLinea $entityPagoLinea, $prefijoEmpresa)
    {
        $isReactivado = false;
        $procesoMasivoId = null;
        $idEmpresa = $entityPagoLinea->getEmpresaId();
        $prefijoEmpresaSesion = $prefijoEmpresa;
                
        $arrayPuntosPorUltimaMilla = $this->obtenerPuntosParaReactivar('', $entityPagoLinea->getId(),$idEmpresa);
        
        if(!empty($arrayPuntosPorUltimaMilla))
        {
        
            $strIdsPuntosFO = $arrayPuntosPorUltimaMilla['FO']; //Puntos con Fibra Optica
            $strIdsPuntosCR = $arrayPuntosPorUltimaMilla['CR']; //Puntos con Radio/Cobre

            $intTotalFO = $arrayPuntosPorUltimaMilla['totalFO']; //Total de puntos con Fibra a cortar
            $intTotalCoRa = $arrayPuntosPorUltimaMilla['totalCoRa']; //Total de puntos con Cobre/radio a cortar

            if ($strIdsPuntosCR)
            {
                            
                //realizo validacion para obtener la empresa para el flujo
                $parametros = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                     ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'CO', "", "","","");
                if($parametros)
                {
                    $prefijoEmpresa = $parametros['valor3'];
                    $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                    $idEmpresa = $objEmpresa->getId(); 
                }
                
                $isReactivado = $this->reactivarServiciosTTCO($strIdsPuntosCR, $entityPagoLinea->getUsrCreacion(), '127.0.0.1', 'pago en linea');
                $procesoMasivoId = $this->guardarPuntosPorReactivacionMasivo($entityPagoLinea->getCanalPagoLinea()->getId(), $prefijoEmpresa,
                    $idEmpresa, null, null, 0, null, $strIdsPuntosCR, $intTotalCoRa, $entityPagoLinea->getUsrCreacion(), '127.0.0.1', null, 
                    $entityPagoLinea->getId(), null, null);
            }

            if ($strIdsPuntosFO)
            {
                
                //realizo validacion para obtener la empresa para el flujo
                $parametros = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                     ->getOne("EMPRESA_EQUIVALENTE", "", "", "", $prefijoEmpresaSesion, 'FO', "", "","","");
                if($parametros)
                {
                    $prefijoEmpresa = $parametros['valor3'];
                    $objEmpresa = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->findOneByPrefijo($prefijoEmpresa);
                    $idEmpresa = $objEmpresa->getId(); 
                }
                
                $procesoMasivoId = $this->guardarPuntosPorReactivacionMasivo($entityPagoLinea->getCanalPagoLinea()->getId(), $prefijoEmpresa, 
                    $idEmpresa, null, null, 0, null, $strIdsPuntosFO, $intTotalFO, $entityPagoLinea->getUsrCreacion(), '127.0.0.1', null, 
                    $entityPagoLinea->getId(), null, null);
            }

            if($procesoMasivoId)
            {
                $entityPagoLinea->setProcesoMasivoId($procesoMasivoId);
                $this->emFinanciero->persist($entityPagoLinea);
                $this->emFinanciero->flush();
                $isReactivado = true;
            }
        }
        return array('isReactivado' => $isReactivado, 'procesoMasivoId' => $procesoMasivoId);

    }
    
    
    
    /**
    * obtenerPuntosParaReactivar
    *
    * obtiene los puntos a reactivar según el tipo de pago y los devuelve en un array clasificados en el tipo de medio.
    * Se actualiza (version 1.2) permitiendo recibir el id de la empresa para poder obtener valor permisible por empresa
    * @author John Vera <javera@telconet.ec>
    * @author Alexander Samaniego <awsamaniego@telconet.ec>
    * @version 1.2 09-03-2016
    * @since 1.0
    *
    * @author Javier Hidalgo <jihidalgo@telconet.ec>
    * @version 1.3 09-12-2021 - Se agrega validación la cual omite la reconexión automática de un servicio
    *                           si este se encuentra en estado In-Audit.
    *
    * @param string $idRecaudacion
    * @param string $idPagoLinea
    * @param string $intEmpresaId 
    * @return array $arrayPuntosPorUltimaMilla
    */


    function obtenerPuntosParaReactivar($idRecaudacion, $idPagoLinea, $intEmpresaId)
    {
        $arrayPuntosPorUltimaMilla  = '';
        $strPuntos                  = '';
        //Busca el valor permisible para la activacion del cliente.
        $arrayValorPermisible = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                ->getOne('LIMITE_SALDO_REACTIVACION',
                                                         'FINANCIERO',
                                                         '',
                                                         '',
                                                         'VALOR_PERMISIBLE',
                                                         '',
                                                         '',
                                                         '',
                                                         '',
                                                         $intEmpresaId);
        $intValorPermisible = 0;
        // Obtenemos registro de servicios con caracteristica InAudit con estado Activo
        $objAdmiCaractInaudit = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
        ->findOneBy(array(
            'descripcionCaracteristica' => 'InAudit',
            'estado' => 'Activo'
        ));
        $arrayPuntosInaudit = array();

        //Valida que array sea diferente de vacio
        if(!empty($arrayValorPermisible['valor2']))
        {
            $intValorPermisible = $arrayValorPermisible['valor2'];
        }
        //obtengo todos los puntos que estan en el pago en linea o la recaudacion
        $puntos = $this->emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtenerPuntosPorPagoRecaudacion($idRecaudacion, $idPagoLinea);
        //verifico que cada punto no tenga ningún valor pendiente y armo un string con los puntos
        
        foreach($puntos as $punto)
        {   $valor      = '';
            $saldoarr   = $this->emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($punto['puntoId']);
            $valor      = $saldoarr[0]['saldo'];
            //Valida que el saldo del cliente sean menor o igual al valor permisible configurado en la base.
            if($valor <= $intValorPermisible)
            {
                $arrServiciosCortados = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->findBy(array('estado'=>'In-Corte','puntoId'=>$punto['puntoId']));

                //Obtenemos todos los servicios que se encuentra en estado In-Corte e In-Audit
                foreach($arrServiciosCortados as $servCortados)
                {
                    $arrayServiciosCortadosInAudit = $this->emComercial->getRepository("schemaBundle:InfoServicioCaracteristica")
                    ->findBy(array(
                        'servicioId' => $servCortados,
                        'caracteristicaId' => $objAdmiCaractInaudit,
                        'estado' => 'Activo'));

                    if(!empty($arrayServiciosCortadosInAudit))
                    {
                        array_push($arrayPuntosInaudit,$arrayServiciosCortadosInAudit);
                    }
                }
                //Si encontramos algun servicio en InAudit se lo omite para la reconexión automática
                if(count($arrServiciosCortados)>0 && empty($arrayPuntosInaudit))
                {    
                    $strPuntos .= $punto['puntoId'] . '|';
                }
            }
        }
        //clasifico los puntos según el tipo de medio
        if($strPuntos)
        {
            $arrayPuntosPorUltimaMilla = $this->obtenerPuntosPorUltimaMilla(substr($strPuntos, 0, -1));
            
        }
        
        return $arrayPuntosPorUltimaMilla;
    }

    
    /**
     * 
     * Actualizacion (version 1.1): Se agrega que despues de llamar a script de reactivacion masiva
     * cambie el estado del servicio y del punto a Activo y cree un historial del servicio de reactivacion
     * @autor amontero@telconet.ec
     * @version 1.1 26/07/2016
     * 
     * Documentacion para funcion reactivarServiciosTTCO
     * Permite reactivar los servicios de la empresa Transtelco o los que tengan ultima milla cobre
     * 
     * @param array $puntosReactivar
     * @param string usrCreacion
     * @param string $clientIp
     * @param int $proceso
     * @version 1.0
     * @autor sistemas@telconet.ec
     * @return boolean true o false
     */        
    private function reactivarServiciosTTCO($puntosReactivar, $usrCreacion, $clientIp, $proceso)
    {
        $tieneServicios = false;
        $serviciosParaReactivar = "";
        
        $puntos = explode("|", $puntosReactivar);
                
        /* @var $infoServicioRepository \telconet\schemaBundle\Repository\InfoServicioRepository */
        $infoServicioRepository = $this->emComercial->getRepository('schemaBundle:InfoServicio');
        //obtengo los servicios de los puntos
        $idsServicios = $infoServicioRepository->getIdsServiciosByEstadoAndIdsPuntos('In-Corte', $puntos);

        $serviciosParaReactivar = implode('|', $idsServicios);       
        if ($serviciosParaReactivar)
        {
            //SE GRABA SERVICIO Y PUNTO COMO ACTIVO - ESTO ES PARA QUE SI NO SE REACTIVA POR MEDIO DEL 
            //SCRIPT SE ACTIVE CON LA SINCRONIZACION
            foreach ($idsServicios as $intIdServicio)
            {
                $this->actualizaEstadoActivoServicioPunto($intIdServicio);             
            }            
            
            $tieneServicios = true;
            $comando = "echo '\n\n\n\n\n\nREACTIVACION EN RECAUDACION\n--------------------------------------------------------------------' >> "
                . "/home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva";
            $fecha = date("Y-m-d");
            $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_reactivacionMasiva.jar '" . $serviciosParaReactivar .
                "' '" . $usrCreacion . "' '" . $clientIp . "' >> /home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva-$fecha.txt &";
            
            $salida = shell_exec($comando);
            $mensaje = "Se realizo el registro de $proceso y se reactivaron los servicios";            
        }
        else
        {
            $mensaje = "Se realizo el registro de $proceso y no se encontro servicios para reactivar";
        }
        return $tieneServicios;
    }
    
    /**
     * Actualizacion: se corrije el path para el llamado a el script de reactivacion.
     * @author amontero@telconet.ec
     * @version 1.2 2016-07-27
     * 
     * @author Héctor Lozano <hlozano@telconet.ec>
     * @version 1.3 18-10-2018
     * @since 1.1 - Se agregan registros en logs, para tener un historial de lo realizado en la función.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 17-05-2021 Se agrega validación para que se cree un sólo registro por punto para realizar la reactivación de servicios MD con 
     *                         Fibra óptica, tomando en cuenta que dicho punto tenga un servicio con plan de Internet en estado In-Corte
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.5 23-08-2021 - Se envía el usuario de creación en el método de actualizar el servicio
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.6 22-12-2021 - Se valida si el proceso proviene de Megadatos y si el usuario no tiene perfil para reactivar cliente In-Audit.
     *                           De ser asi se omite este cliente para la reactivacion masiva. 
     *                           Creamos una nueva respuesta para que en pantalla se muestre una nueva notificacion.                   
     * 
     * Documentacion para metodo que reactiva los servicios que esten cortados de uno o varios puntos
     * Actualizacion (version 1.1): Se consulta el valor permisible por empresa desde la tabla AdmiParametroDet
     * @param InfoPagoLinea $entityPagoLinea
     * @param array $arrpuntos
     * @param string $prefijoEmpresa
     * @param int $empresaId
     * @param int $oficinaId
     * @param string $usuarioCreacion
     * @param string $ip
     * @param int $idPago
     * @version 1.1 09/03/2016
     * @since 1.0 13/11/2014
     * @autor amontero@telconet.ec
     * @return string string_msg (Mensaje servicios reactivados)
     */    
    public function reactivarServiciosPuntos($arrayParams)
    {
        $arrayPuntos              = $arrayParams['puntos'];
        $prefijoEmpresa           = $arrayParams['prefijoEmpresa'];
        $empresaId                = $arrayParams['empresaId'];
        $oficinaId                = $arrayParams['oficinaId'];
        $usuarioCreacion          = $arrayParams['usuarioCreacion'];
        $ip                       = $arrayParams['ip'];
        $idPago                   = $arrayParams['idPago'];
        $debitoId                 = $arrayParams['debitoId'];
        $serviciosParaReactivarCo = "";
        $puntosParaReactivarFo    = array();
        $string_msg               = "nocerrar";
       
        //Verificamos si el usuario posee perfil con permiso para reactivar cliente InAudit
        $intEsPerfilReconectarAbusador = $this->emComercial->getRepository('schemaBundle:SistPerfil')
        ->getPerfilesReconexionAbusador($usuarioCreacion);

        //Validamos que el proceso provenga de Megadatos y si el Usuario no tiene permisos
        if($prefijoEmpresa == 'MD' && $intEsPerfilReconectarAbusador <> 1) 
        {          
            $arrayPuntosInaudit = array();
            $floatTotalSaldoPuntos = 0;

            //Obtenemos la característica InAudit
            $objAdmiCaractInaudit = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
            ->findOneBy(array(
                'descripcionCaracteristica' => 'InAudit',
                'estado' => 'Activo'
            ));
            foreach($arrayPuntos as $intKey=>$intPunto)
            {
                //OBTIENE SALDO DEL PUNTO
                $objSaldo = $this->emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($intPunto);
                $floatSaldo = $objSaldo[0]['saldo'];

                $arrayServiciosCortados = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                             ->findBy(array('estado'=>'In-Corte','puntoId'=>$intPunto));

                //Obtenemos todos los servicios que se encuentra en estado In-Corte e In-Audit
                foreach($arrayServiciosCortados as $objServCortados)
                {
                    $arrayServiciosCortadosInAudit = $this->emComercial->getRepository("schemaBundle:InfoServicioCaracteristica")
                    ->findBy(array(
                        'servicioId' => $objServCortados,
                        'caracteristicaId' => $objAdmiCaractInaudit,
                        'estado' => 'Activo'));

                    if(!empty($arrayServiciosCortadosInAudit))
                    {
                        array_push($arrayPuntosInaudit,$arrayServiciosCortadosInAudit);
                    }
                }
                //Si encontramos algun servicio en InAudit se lo omite para la reconexión automática
                //eliminandolo de nuestros puntos iniciales
                if(count($arrayPuntosInaudit)>0)
                {   
                    unset($arrayPuntos[$intKey]);
                }
                $floatTotalSaldoPuntos = $floatTotalSaldoPuntos + $floatSaldo;
            }

            //Si no existe puntos a reactivar y el saldo de este punto
            if(empty($arrayPuntos) && ($floatTotalSaldoPuntos==0))
            {
                $string_msg = "nocerrar-inaudit";
            }
        }
        
        /*Se Guardan los parámetros obtenidos */   
        $this->loggerProcesoMasivo('Función:reactivarServiciosPuntos-ProcesoMasivoService', 'Datos Recibidos:' .
                                   ' |Puntos:' . json_encode($arrayParams['puntos']).
                                   ' |Pref:'.$arrayParams['prefijoEmpresa']. ' |Emp:'.$arrayParams['empresaId']. 
                                   ' |Ofic:'. $arrayParams['oficinaId']. ' |Usr:'.$arrayParams['usuarioCreacion'].
                                   ' |Ip:'. $arrayParams['ip']. ' |DebId:'. $arrayParams['debitoId']);

        /*Se Guardan los parámetros afectados */   
        $this->loggerProcesoMasivo('Función:reactivarServiciosPuntos-ProcesoMasivoService', 'Datos Procesados:' .
                                   ' |Puntos:' . json_encode($arrayPuntos).
                                   ' |Pref:'.$arrayParams['prefijoEmpresa']. ' |Emp:'.$arrayParams['empresaId']. 
                                   ' |Ofic:'. $arrayParams['oficinaId']. ' |Usr:'.$arrayParams['usuarioCreacion'].
                                   ' |Ip:'. $arrayParams['ip']. ' |DebId:'. $arrayParams['debitoId']);
        
        //Busca el valor permisible para la activacion del cliente.
        $arrayValorPermisible = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                           ->getOne('LIMITE_SALDO_REACTIVACION',
                                               'FINANCIERO',
                                               '',
                                               '',
                                               'VALOR_PERMISIBLE',
                                               '',
                                               '',
                                               '',
                                               '',
                                               $empresaId);
        $intValorPermisible = 0;
        //Valida que array sea diferente de vacio
        if(!empty($arrayValorPermisible['valor2']))
        {
            $intValorPermisible = $arrayValorPermisible['valor2'];
        }          

        foreach($arrayPuntos as $punto)
        {
            //OBTIENE SALDO DEL PUNTO
            $saldoarr = $this->emFinanciero->getRepository('schemaBundle:InfoPagoCab')->obtieneSaldoPorPunto($punto);
            $valor = $saldoarr[0]['saldo'];
            
            /*Se registra el punto y saldos */   
            $this->loggerProcesoMasivo('Punto y Saldos','Punto: '.$punto.'| Saldo: '. $valor.'| Valor Permisible:'.$intValorPermisible);
            
            if($valor<=$intValorPermisible)
            {                        
                $serviciosInactivos = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findServiciosCortadosPorPuntos($punto);
                $string_msg="cerrar-sinservicios";                
                foreach($serviciosInactivos as $servicio)
                {
                    $objServicioTecnico = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                        ->findOneByServicioId($servicio->getId());
                    if ($objServicioTecnico)
                    {    
                        $objAdmiTipoMedio = $this->emComercial->getRepository('schemaBundle:AdmiTipoMedio')
                            ->find($objServicioTecnico->getUltimaMillaId());

                        if($objAdmiTipoMedio)
                        {    
                            $string_msg="cerrar-conservicios";                                
                            if (strtoupper($objAdmiTipoMedio->getCodigoTipoMedio())=='RAD' || 
                                strtoupper($objAdmiTipoMedio->getCodigoTipoMedio())=='CO')
                            {
                                $serviciosParaReactivarCo = $serviciosParaReactivarCo . $servicio->getId() . "|";
                                //SE GRABA SERVICIO Y PUNTO COMO ACTIVO - ESTO ES PARA QUE SI NO SE REACTIVA POR MEDIO DEL 
                                //SCRIPT SE ACTIVE CON LA SINCRONIZACION
                                $this->actualizaEstadoActivoServicioPunto($servicio->getId(),$usuarioCreacion);
	                                        
                            }
                            elseif (strtoupper($objAdmiTipoMedio->getCodigoTipoMedio())=='FO')
                            {
                                if($empresaId == $this->intIdEmpresaMd)
                                {
                                    $objPlanServicio = $servicio->getPlanId();
                                    if(is_object($objPlanServicio) && !in_array($punto, $puntosParaReactivarFo))
                                    {
                                        $arrayVerifProdInternetEnPlan   = $this->serviceServicioTecnico
                                                                               ->obtieneProductoEnPlan(array( 
                                                                                                        "intIdPlan"                 => 
                                                                                                        $objPlanServicio->getId(),
                                                                                                        "strNombreTecnicoProducto"  => "INTERNET"));
                                        if($arrayVerifProdInternetEnPlan["strProductoEnPlan"] === "SI")
                                        {
                                            $puntosParaReactivarFo[] = $punto;
                                        }
                                    }
                                }
                                else
                                {
                                    $puntosParaReactivarFo[]=$punto;
                                }
                            }
                        }
                    }
                }  
            }
        } 
        
        
        if($serviciosParaReactivarCo)
        {
            $this->loggerProcesoMasivo('Función:reactivarServiciosPuntos-ProcesoMasivoService','Mensaje Tipo-Medio: Reactivar por Cobre');
            
            $comando = "echo '\n\n\n\n\n\nREACTIVACION EN PAGOS".
                "\n--------------------------------------------------------------------'".
                " >> /home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva";            
            $salida= shell_exec($comando);
            $fecha= date("Y-m-d");
            $comando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom ".
                $this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/ttco_reactivacionMasiva.jar '".
                $serviciosParaReactivarCo."' '".$usuarioCreacion."' '".$ip.
                "' >> ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva-$fecha.txt &";               
            $salida= shell_exec($comando);            
            
        }    
        if(count($puntosParaReactivarFo)>0)
        {
            $this->loggerProcesoMasivo('Función:reactivarServiciosPuntos-ProcesoMasivoService','Mensaje Tipo-Medio: Reactivar por Fibra');
            if($empresaId == $this->intIdEmpresaMd)
            {
                $intNumPuntosPmCab = count($puntosParaReactivarFo);
            }
            else
            {
                $intNumPuntosPmCab = 1;
            }
            $this->guardarPuntosPorReactivacionMasivo(null, $prefijoEmpresa, $empresaId, null, null,$valor, 
                $oficinaId, $puntosParaReactivarFo, $intNumPuntosPmCab, $usuarioCreacion, $ip, $idPago, null,null, $debitoId);             
        }    
        
        return $string_msg;
    }    

   
/**
 * obtenerPuntosPorUltimaMilla
 * 
 * @author John Vera <javera@telconet.ec>
 * @param string $strPuntos
 * @return array con los puntos por tipo de ultima milla : Fibra Optica o Cobre/Radio y la cantidad de los mismos a cortar/reactivar
 * para segun eso ejecutar los procesos respectivos y guardar en las tablas de procesos masivos
 */
    public function obtenerPuntosPorUltimaMilla($strPuntosUM)
    {
        $arrayPuntosUM = explode('|', $strPuntosUM);
        $strPuntosFo = '';
        $strPuntosCoRa = '';
        $intTotalFo = 0;
        $intTotalCoRa = 0;

        foreach($arrayPuntosUM as $puntoUM)
        {
            $arrayUltimaMilla = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getUltimaMillaPorPunto($puntoUM);
            if($arrayUltimaMilla)
            {
                if($arrayUltimaMilla == 'FO')
                {
                    $strPuntosFo .= $puntoUM . '|';
                    $intTotalFo = $intTotalFo + 1;
                }
                else if(($arrayUltimaMilla == 'CO') || ($arrayUltimaMilla == 'RAD'))
                {
                    $strPuntosCoRa .= $puntoUM . '|';
                    $intTotalCoRa = $intTotalCoRa + 1;
                }
            }
        }

        $arrayClientesPorUM = array('FO' => substr($strPuntosFo, 0, -1),
            'totalFO' => $intTotalFo,
            'CR' => substr($strPuntosCoRa, 0, -1),
            'totalCoRa' => $intTotalCoRa
        );
        
        return $arrayClientesPorUM;
    }

    /**
     * 
     * Documentacion para funcion actualizaEstadoActivoServicioPunto
     * Cambia el estado a Activo del servicio y del punto
     * Tambien crea un estado en el historial del servicio
     * 
     * @version 1.0 27/07/2016
     * @autor amontero@telconet.ec
     * 
     * @version 1.1 18/08/2016
     * @autor jbozada@telconet.ec   Se agrega seteo de campo Accion en registro de historial de servicio
     *
     * @version 1.2 23/08/2021
     * @autor facaicedo@telconet.ec - Se agrega campo, para recibir el usuario de sesión
     *
     * @param integer $intIdServicio
     * @param String $strUsrCreacion
     */ 
    public function actualizaEstadoActivoServicioPunto($intIdServicio, $strUsrCreacion = "procesosmasivos")
    {
                $entityInfoServicio=$this->emComercial->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);  
                if($entityInfoServicio)
                {    
                    $entityInfoServicio->setEstado('Activo');
                    $this->emComercial->persist($entityInfoServicio);
                    $this->emComercial->flush(); 

                    $entityInfoPunto = $entityInfoServicio->getPuntoId();
                    $entityInfoPunto->setEstado('Activo');
                    $this->emComercial->persist($entityInfoPunto);
                    $this->emComercial->flush();                

                    $entityServicioHistorial = new InfoServicioHistorial();
                    $entityServicioHistorial->setObservacion('El servicio se reactivo exitosamente');
                    $entityServicioHistorial->setServicioId($entityInfoServicio);
                    $entityServicioHistorial->setEstado('Activo');
                    $entityServicioHistorial->setAccion("reconectarCliente");
                    $entityServicioHistorial->setUsrCreacion($strUsrCreacion);
                    $entityServicioHistorial->setIpCreacion('127.0.0.1');
                    $entityServicioHistorial->setFeCreacion(new \DateTime('now'));
                    $this->emComercial->persist($entityServicioHistorial);
                    $this->emComercial->flush();
                }
    }    
    
    /**
     * execJar, metodo para ejecutar comandos en el servidor
     * 
     * @author Alexander Samaniego <awsamaniego>
     * @version 21-07-2019
     * @since 1.0
     * 
     * @param array $arrayParamertros
     * @return string Retorna ok
     */
    public function execJar($arrayParamertros)
    {
        $strLineaComando = "";
        foreach($arrayParamertros as $strComando):
            $strLineaComando .= $strComando;
        endforeach;
        if(!empty($strLineaComando))
        {
            shell_exec($strLineaComando);
        }
        return "OK";
    }
    
    /**
     * Función que finaliza los procesos masivos al ejecutar una acción parametrizada
     * 
     * @param $arrayParametros [
     *                              "intIdPunto"            => Id del punto,
     *                              "strOpcionEjecutante"   => Opción que ejecuta el proceso,
     *                              "strCodEmpresa"         => Id de la empresa,
     *                              "strPrefijoEmpresa"     => Prefijo de empresa,
     *                              "strUsrCreacion"        => Usuario de creación,
     *                              "strIpCreacion"         => Ip de creación
     *                          ]
     * 
     * @return array $arrayRespuesta [ 
     *                                  "status"    => 'OK' o 'ERROR'
     *                                  "mensaje"   => Mensaje de error
     *                                ]
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 07-12-2021
     * 
     */
    public function finalizaPmsPorOpcion($arrayParametros)
    {
        $intIdPunto             = $arrayParametros["intIdPunto"];
        $strOpcionEjecutante    = $arrayParametros["strOpcionEjecutante"];
        $strCodEmpresa          = $arrayParametros["strCodEmpresa"];
        $strPrefijoEmpresa      = $arrayParametros["strPrefijoEmpresa"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strIpCreacion          = $arrayParametros["strIpCreacion"];
        $strStatus              = str_repeat(' ', 5);
        $strMsjError            = str_repeat(' ', 1000);
        try
        {
            $strSql = " BEGIN 
                          DB_INFRAESTRUCTURA.INKG_TRANSACCIONES_MASIVAS.P_FINALIZA_PMS_POR_OPCION(  :intIdPunto,
                                                                                                    :strOpcionEjecutante,
                                                                                                    :strCodEmpresa,
                                                                                                    :strPrefijoEmpresa,
                                                                                                    :strStatus, 
                                                                                                    :strMsjError); 
                        END;";
            $objStmt = $this->emInfraestructura->getConnection()->prepare($strSql);
            $objStmt->bindParam('intIdPunto', $intIdPunto);
            $objStmt->bindParam('strOpcionEjecutante', $strOpcionEjecutante);
            $objStmt->bindParam('strCodEmpresa', $strCodEmpresa);
            $objStmt->bindParam('strPrefijoEmpresa', $strPrefijoEmpresa);
            $objStmt->bindParam('strStatus', $strStatus);
            $objStmt->bindParam('strMsjError', $strMsjError);
            $objStmt->execute();
            if(strlen(trim($strStatus)) > 0)
            {
                $strStatusRespuesta     = $strStatus;
                $strMensajeRespuesta    = $strMsjError;
            }
            else
            {
                $strStatusRespuesta     = "ERROR";
                $strMensajeRespuesta    = "Se presentaron problemas al intentar finalizar los procesos masivos. "
                                          ."Por favor comuníquese con Sistemas!";
            }
        }
        catch (\Exception $e)
        {
            $strStatusRespuesta     = "ERROR";
            $strMensajeRespuesta    = 'Ha ocurrido un error inesperado y no se ha podido finalizar los procesos masivos. '
                                      .'Por favor comuníquese con Sistemas!';
            $this->serviceUtil->insertError( 'Telcos+', 
                                              'ProcesoMasivoService->finalizaPmsPorOpcion', 
                                              $strMensajeRespuesta.$e->getMessage(), 
                                              $strUsrCreacion, 
                                              $strIpCreacion );
            error_log($strMensajeRespuesta.$e->getMessage());
        }
        $arrayRespuesta = array("status"    => $strStatusRespuesta,
                                "mensaje"   => $strMensajeRespuesta);
        return $arrayRespuesta;
    }
}
