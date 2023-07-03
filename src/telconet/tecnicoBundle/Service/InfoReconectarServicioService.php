<?php
namespace telconet\tecnicoBundle\Service;

use Doctrine\ORM\EntityManager;

use telconet\schemaBundle\Entity\InfoElemento;
use telconet\schemaBundle\Entity\InfoIpElemento;
use telconet\schemaBundle\Entity\InfoInterfaceElemento;
use telconet\schemaBundle\Entity\InfoServicioProdCaract;
use telconet\schemaBundle\Entity\InfoEnlace;
use telconet\schemaBundle\Entity\InfoEmpresaElemento;
use telconet\schemaBundle\Entity\InfoEmpresaElementoUbica;
use telconet\schemaBundle\Entity\InfoHistorialElemento;
use telconet\schemaBundle\Entity\InfoUbicacion; 
use telconet\schemaBundle\Entity\InfoDetalleInterface;
use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoIp;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use telconet\schemaBundle\Entity\InfoPuntoDatoAdicional;
use telconet\schemaBundle\Entity\InfoDocumento;
use telconet\schemaBundle\Entity\InfoDocumentoComunicacion;
use telconet\schemaBundle\Entity\InfoComunicacion;
use telconet\schemaBundle\Entity\AdmiMotivo;
use telconet\schemaBundle\Entity\InfoDetalleSolicitud;
use telconet\schemaBundle\Entity\InfoDetalleSolHist;
use telconet\schemaBundle\Entity\InfoPersonaEmpresaRolHisto;
use telconet\schemaBundle\Entity\InfoDetalle;
use telconet\schemaBundle\Entity\InfoDetalleAsignacion;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

class InfoReconectarServicioService{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $servicioGeneral;
    private $cancelarService;
    private $serviceCliente;
    private $activarService;
    private $cortarService;
    private $cambiarPuertoService;
    private $licenciasMcAfee;
    private $licenciasOffice365;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters;    
    private $networkingScripts;   
    private $wifiNetlife;
    private $rdaMiddleware;
    private $opcion                 = "REACTIVAR";
    private $ejecutaComando;
    private $strConfirmacionTNMiddleware;
    private $serviceUtil;
    private $serviceLicenciasKaspersky;
    private $servicePromociones;
	private $servicePortalNetCam;
    private $serviceProcesoMasivo;
    
    public function __construct(Container $container) {
        $this->container            = $container;
        $this->emSoporte            = $this->container->get('doctrine')->getManager('telconet_soporte');
        $this->emInfraestructura    = $this->container->get('doctrine')->getManager('telconet_infraestructura');
        $this->emSeguridad          = $this->container->get('doctrine')->getManager('telconet_seguridad');
        $this->emComercial          = $this->container->get('doctrine')->getManager('telconet');
        $this->emComunicacion       = $this->container->get('doctrine')->getManager('telconet_comunicacion');
        $this->emNaf                = $this->container->get('doctrine')->getManager('telconet_naf');
        $this->emGeneral            = $this->container->get('doctrine')->getManager('telconet_general');
        $this->host                 = $this->container->getParameter('host');
        $this->pathTelcos           = $this->container->getParameter('path_telcos');
        $this->pathParameters       = $this->container->getParameter('path_parameters');
        $this->ejecutaComando       = $container->getParameter('ws_rda_ejecuta_scripts');
        $this->strConfirmacionTNMiddleware = $container->getParameter('ws_rda_opcion_confirmacion_middleware');
    }    
    
    public function setDependencies(InfoServicioTecnicoService $servicioGeneral           , InfoCancelarServicioService $cancelarService,
                                    InfoActivarPuertoService   $activarService            , InfoCortarServicioService   $cortarService,
                                    InfoCambiarPuertoService   $cambiarPuertoService      , LicenciasMcAfeeService      $licenciasMcAfeeServicio,
                                    NetworkingScriptsService   $networkingScript          , WifiService                 $wifiNetlife,
                                    Container $container                                  , RedAccesoMiddlewareService  $redAccesoMiddleware) 
    {
        $this->servicioGeneral      = $servicioGeneral;
        $this->cancelarService      = $cancelarService;
        $this->activarService       = $activarService;
        $this->cortarService        = $cortarService;
        $this->cambiarPuertoService = $cambiarPuertoService;
        $this->licenciasMcAfee      = $licenciasMcAfeeServicio;
        $this->licenciasOffice365   = $container->get('tecnico.LicenciasOffice365');
        $this->serviceCliente       = $container->get('comercial.Cliente');
        $this->networkingScripts    = $networkingScript;
        $this->wifiNetlife          = $wifiNetlife;
        $this->rdaMiddleware        = $redAccesoMiddleware;
        $this->serviceUtil          = $container->get('schema.Util');
        $this->serviceLicenciasKaspersky = $container->get('tecnico.LicenciasKaspersky');
        $this->servicePromociones        = $container->get('tecnico.Promociones');
	    $this->servicePortalNetCam       = $container->get('tecnico.PortalNetlifeCam3dEYEService');
        $this->serviceProcesoMasivo      = $container->get('tecnico.ProcesoMasivo');
    }

    /**
     * Método que nos permite actualizar el estado de la caracteristica de un servicio.
     * Registramos evento en historial de servicio.
     * 
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.0 11-11-2021
     * @since 1.0
     * 
     * @param array $arrayParametros [ 'servicio'          => $servicio,
     *                                 'servicioTecnico'   => $servicioTecnico,
     *                                 'interfaceElemento' => $interfaceElemento,
     *                                 'producto'          => $producto,
     *                                 'usrCreacion'       => $usrCreacion,
     *                                 'ipCreacion'        => $ipCreacion,
     *                                 'idEmpresa'         => $idEmpresa,
     *                                 'flagMiddleware'    => $flagMiddleware,
     *                                 'strEsIsb'          => $strEsIsb,
     *                                 'strPrefijoEmpresaOrigen' => $strPrefijoEmpresaOrigen,
     *                                 'intIdAccion'             => $idAccion
     *                               ]
     * @return array $arrayFinal[] - status estado de proceso, mensaje mensaje de informacion.
     * 
     */
    
    public function actualizarEstadoInaudit($arrayPeticiones) 
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $objServicio       = $arrayPeticiones['servicio'];
        $strUsrCreacion    = $arrayPeticiones['usrCreacion'];
        $strIpCreacion     = $arrayPeticiones['ipCreacion'];
        $intFlagProcesoMasivo     = $arrayPeticiones['intFlagProcesoMasivo'];
        $intIdAccion       = $arrayPeticiones['intIdAccion'];
        
        //*DECLARACION DE VARIABLES-----------------------------------------------*/
        $strMensaje = '';
        $strStatus = '';

        //Obtenemos caracteristica Inaudit desde tabla AdmiCaracteristica
        $objAdmiCaractInaudit = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
        ->findOneBy(array(
            'descripcionCaracteristica' => 'InAudit', //deberia ir aracteristica de Inaudit
            'estado' => 'Activo'
        ));

        //Obtenemos el servicio con caracteristica Inaudit estado Activo desde tabla InfoServicioCaracteristica
        $objInfoServCaract = $this->emComercial->getRepository("schemaBundle:InfoServicioCaracteristica")
                ->findOneBy(array(
                    'servicioId' => $objServicio,
                    'caracteristicaId' => $objAdmiCaractInaudit,
                    'estado' => 'Activo'));
        try
        {
            if(is_object($objInfoServCaract))
            {
                $objInfoServCaract->setEstado('Inactivo');
                $objInfoServCaract->setUsrUltMod($strUsrCreacion);
                $objInfoServCaract->setFeUltMod(new \DateTime('now'));
                $objInfoServCaract->setIpUltMod($strIpCreacion);
                $this->emComercial->persist($objInfoServCaract);
                $this->emComercial->flush();

                //Se inserta en la tabla InfoServicioHistorial un registro por el proceso de inaudit realizado
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setEstado("Activo");
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strIpCreacion);
                if(is_null($intFlagProcesoMasivo))
                {
                    $objServicioHistorial->setObservacion("Se reactiva Cliente previamente en estado InAudit.");
                }
                else
                {
                    $objServicioHistorial->setObservacion("Se reactiva Cliente previamente en estado InAudit"
                                                            ." por la ejecución de Reactivación Masiva.");
                }
                if(isset($intIdAccion))
                {
                    $objAccion     = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
                    $objServicioHistorial->setAccion($objAccion->getNombreAccion());
                }
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                $strMensaje = 'OK';
                $strStatus = 'OK';
            }
        }
        catch (\Exception $e)
        {
            $strMensaje = $e->getMessage() . "<br>" ."No se ha podido Inauditar el Plan del cliente seleccionado";
            $strStatus = 'ERROR';
        }

        $arrayFinal[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayFinal;
    }

    /**
     * Funcion que sirve para reconectar Servicios de cualquier
     * ultima Milla
     * 
     * @param $arrayPeticiones  arreglo con las variables necesarias para realizar la reconexion del equipo
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 16-02-2016     Se agrega filtro de estado de Plan en consultas de información para obtener Detalles de Plan
     * @since 1.0
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.1 16-02-2016     Se agrega proceso de reconexion de cliente para producto NETLIFE ZONE
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.3 16-02-2016     Se agrega validacion por "INTERNET SMALL BUSINESS", OLT debe contener MIDDLEWARE.
     *                             Se agrega bandera "SI" por producto "INTERNET SMALL BUSINESS".
     *                             Se envia un nuevo parametro prefijo empresa adicional a llamada del ldap por ISB.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 15-05-2018 Se realiza ajuste para considerar las ips adicionales al reconectar un servicio Small Business. 
     *                         El estado del punto no es modificado ya que es un servicio TN
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 15-05-2018 Se envía parámetro para identificar si es un servicio Small Business
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 28-11-2018 Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 1.6 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 11-02-2019 Se agrega reconexión de servicios TelcoHome con sus respectivas Ips
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 08-03-2019 Se agrega validación adicional para reconectar servicios de ips adicionales sin considerar a los 
     *                          servicios de telcohome
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 05-08-2019 Se agrega validación para evitar que se reactive de manera lógica los servicios I. PROTEGIDO MULTI PAID
     *                           con tecnología Kaspersky y realice la reactivación de manera correcta con la invocación de la función 
     *                           reactivarServiciosAdicionalesPorPunto
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 16-09-2019  Se agrega proceso para validar promociones en servicios de clientes, en caso de 
     *                           que aplique a alguna promoción se le configurarán los anchos de bandas promocionales
     * 
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 07-10-2019 Se agrega el parámetro acción en proceso de reconexión MD
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.0  21-11-2019 - Se agrega el proceso para notificar la reconexión del servicio a konibit mediante GDA en caso de aplicar.
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 2.1  20-12-2019 - Se agrega el proceso para reconectar el servicio NetCam en la plataforma 3dEYE.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 04-05-2020 Se elimina la invocación a la función obtenerInfoMapeoProdPrefYProdsAsociados, puesto que ya no es necesario enviar
     *                          el parámetro strNombreTecnicoIp a la función reconectarServicioMd
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.3 07-12-2021 Se agrega programación para finalización de procesos masivos que ya no aplican al reactivar el servicio de Internet 
     *                         de manera manual
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 2.4 09-12-2021 - Se agrega parámetro el cual indica si servicio esta InAudit.
     *                            Se actualiza estado de caracteristica de servicio y se reactiva cliente.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.7 08-11-2021 Se agrega la invocación del web service para confirmación de opción de Tn a Middleware
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.8 11-07-2022 Se agrega la validación de la caracteristica del servicio principal INTERNET VPNoGPON,
     *                         para obtener los servicios de las ip asociadas al servicio principal.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.9 01-08-2022 - Se agrega la validación para reconectar los servicios adicionales safecity del servicio principal INTERNET VPNoGPON.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.0 28-02-2023 - Se agrega bandera para empresa Ecuanet para que permita flujo de reconectar el servicio.
     * 
     */
    public function reconectarServicio($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones['prefijoEmpresa'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $idProducto     = $arrayPeticiones['idProducto'];
        $idAccion       = $arrayPeticiones['idAccion'];
        $strEsIsb       = $arrayPeticiones['strEsIsb'] ? $arrayPeticiones['strEsIsb'] : "NO";
        $ejecutaLdap    = "NO";
        $strMsjAdicional= "";
        $strPrefijoEmpresaOrigen = $arrayPeticiones['prefijoEmpresa'];
        $strEstaInaudit = $arrayPeticiones['strEstaInaudit']; //Indica si nuestro servicio se encuentra InAudit
        $intEsPerfilReconectarAbusador = 0;
        $arrayDataConfirmacionTn = array();
        
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
            ->getEmpresaEquivalente($idServicio,$prefijoEmpresa );

        if($arrayEmpresaMigra)
        { 
            if ($arrayEmpresaMigra['prefijo']=='TTCO')
            {
                 $idEmpresa= $arrayEmpresaMigra['id'];
                 $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }
        
        $accionObj          = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        $servicio           = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);
        $servicioTecnico    = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                   ->findOneBy(array( "servicioId" => $servicio->getId()));
        $interfaceElemento  = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                   ->find($servicioTecnico->getInterfaceElementoId());
        $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                   ->find($interfaceElemento->getElementoId());
        $modeloElemento     = $elemento->getModeloElementoId();
        $producto           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($idProducto);
        $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                            "detalleNombre"=> 'MIDDLEWARE',
                                                            "estado"       => 'Activo'));
        $flagMiddleware     = false;
        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //Obtenemos los productos adicionales con la característica KONIBIT.
        $arrayServiciosProdKonibit = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getServiciosProductoKonibit(
                    array ('arrayEstadosServicio' =>  array('In-Corte'),
                           'intIdPunto'           =>  $servicio->getPuntoId()->getId(),
                           'strEstadoProdCaract'  => 'Activo',
                           'strDescripcionCaract' => 'KONIBIT',
                           'strUsuario'           =>  $usrCreacion,
                           'strIp'                =>  $ipCreacion,
                           'objUtilService'       =>  $this->serviceUtil));

        //LOGICA DE NEGOCIO-----------------------------------------------------
        try
        {
            if($objDetalleElemento)
            {
                if($objDetalleElemento->getDetalleValor() == 'SI')
                {
                    $flagMiddleware = true;
                }
            }
                        
            if($prefijoEmpresa == "TTCO")
            {
                $respuestaArray = $this->reconectarServicioTtco($servicio, $servicioTecnico, $interfaceElemento, 
                                                                $elemento, $modeloElemento);
                $status = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];
            }
            else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "TNP" || $prefijoEmpresa == "EN")
            {
                if(!$flagMiddleware && $strEsIsb === 'SI')
                {
                    $strStatus         = "ERROR";
                    $strMensaje        = "No se pudo realizar la Reconexion del Servicio - OLT sin middleware ";
                    $arrayFinal[]      = array('status'=>$strStatus, 'mensaje'=> $strMensaje);
                    return $arrayFinal;
                }
                
                if(!$flagMiddleware)
                {
                    if($modeloElemento->getNombreModeloElemento() == "EP-3116")
                    {
                        //verifico si el olt esta aprovisionando el CNR
                        $objDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                      ->findOneBy(array('detalleNombre' => 'OLT MIGRADO CNR',
                                                                                        'elementoId' => $interfaceElemento->getElementoId()->getId()));
                        if($objDetalleElemento)
                        {
                            $ejecutaLdap = "SI";
                        }
                    }
                    else 
                    {
                        $ejecutaLdap = "SI";
                    }   
                }
                else
                {
                    $ejecutaLdap = "SI";
                }
                 
                $arrayParametros = array(
                                            'servicio'          => $servicio,
                                            'servicioTecnico'   => $servicioTecnico,
                                            'interfaceElemento' => $interfaceElemento,
                                            'producto'          => $producto,
                                            'usrCreacion'       => $usrCreacion,
                                            'ipCreacion'        => $ipCreacion,
                                            'idEmpresa'         => $idEmpresa,
                                            'flagMiddleware'    => $flagMiddleware,
                                            'strEsIsb'          => $strEsIsb,
                                            'strPrefijoEmpresaOrigen' => $strPrefijoEmpresaOrigen,
                                            'intIdAccion'             => $idAccion
                                        );  

                //Si cliente se encuentra InAudit procedemos a validar si el usuario posee perfil con permisos.
                //De ser asi reactiva el servicio del cliente, caso contrario indica que escale a coordinador.
                $intEsPerfilReconectarAbusador = $this->emSeguridad->getRepository('schemaBundle:SistPerfil')
                                                                    ->getPerfilesReconexionAbusador($usrCreacion);

                if($strEstaInaudit == 'N') 
                {
                    $respuestaArray = $this->reconectarServicioMd($arrayParametros);
                    $status = $respuestaArray[0]['status'];
                    $mensaje = $respuestaArray[0]['mensaje'];
                    $strMsjAdicional = $respuestaArray[0]['msjAdicional'];
                    $arrayDataConfirmacionTn = $respuestaArray[0]['arrayDataConfirmacionTn'];
                }
                else if ($intEsPerfilReconectarAbusador == 1)
                {
                    $arrayResActInaudit = $this->actualizarEstadoInaudit($arrayParametros);
                    if($arrayResActInaudit[0]['status'] == 'OK')
                    {
                        $respuestaArray = $this->reconectarServicioMd($arrayParametros);
                        $status = $respuestaArray[0]['status'];
                        $mensaje = $respuestaArray[0]['mensaje'];
                        $strMsjAdicional = $respuestaArray[0]['msjAdicional'];
                        $arrayDataConfirmacionTn = $respuestaArray[0]['arrayDataConfirmacionTn'];
                    }
                    else
                    {
                        $status = 'ERROR';
                        $mensaje = $arrayResActInaudit[0]['mensaje'];
                    }
                }
                else 
                {
                    $status = 'ERROR';
                    $mensaje = 'Cliente suspendido por posible ISP - CYBER, por favor escale a su coordinador.';
                }
            }
            
            if($status == "OK")
            {
                $mensaje = "Se Reactivo el Cliente!";
                if(isset($strMsjAdicional) && !empty($strMsjAdicional))
                {
                    $mensaje .= "<br>".$strMsjAdicional;
                }
                $flagProd = 0;
                $contProdPref = 0;
                //verificar prod preferencial
                $planServicio = $servicio->getPlanId();
                if($planServicio != "" || $planServicio != null)
                {
                    $planDetServicio = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                            ->findBy(array("planId" => $planServicio->getId(), "estado" => $planServicio->getEstado()));
                    for($i = 0; $i < count($planDetServicio); $i++)
                    {
                        $prodServicio1 = $planDetServicio[$i]->getProductoId();

                        $productoServicio1 = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio1);

                        if($productoServicio1->getEsPreferencia() == "SI")
                        {
                            $flagProd = 1;
                        }
                    }
                }
                else
                {
                    $prodServicio1 = $servicio->getProductoId();
                    $productoServicio1 = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio1);

                    if($productoServicio1->getEsPreferencia() == "SI")
                    {
                        $flagProd = 1;
                    }
                }
                //verificar si existe otro producto preferencial
                if($flagProd == 1)
                {
                    $puntoPref = $servicio->getPuntoId();
                    $serviciosPunto1 = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                            ->findBy(array("puntoId" => $puntoPref->getId()));

                    for($i = 0; $i < count($serviciosPunto1); $i++)
                    {
                        $serv1 = $serviciosPunto1[$i];

                        //solo se buscaran el preferencial en servicios cortados o suspendidos temporalmente
                        if($serv1->getEstado() == "In-Corte" || $serv1->getEstado() == "In-Corte-SinEje" || 
                           $serv1->getEstado() == "In-Temp")
                        {
                            $plan = $serv1->getPlanId();
                            if($plan != "" || $plan != null)
                            {
                                $planDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                ->findBy(array("planId" => $plan->getId(), "estado" => $plan->getEstado()));
                                for($j = 0; $j < count($planDet); $j++)
                                {
                                    $prodServicio = $planDet[$j]->getProductoId();

                                    $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                             ->find($prodServicio);

                                    if($productoServicio->getEsPreferencia() == "SI")
                                    {
                                        $contProdPref++;
                                    }
                                }
                            }
                            else
                            {
                                $prodServicio = $serv1->getProductoId();
                                $productoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($prodServicio);

                                if($productoServicio->getEsPreferencia() == "SI")
                                {
                                    $contProdPref++;
                                }
                            }
                        }//cierre if servicio activo
                    }
                }

                if(($flagProd == 1 && $contProdPref < 2) || $strEsIsb === "SI")
                {
                    $arrayIdsProdsIps   = array();
                    $punto = $servicio->getPuntoId();
                    if($strEsIsb !== "SI")
                    {
                        $punto->setEstado("Activo");
                        $this->emComercial->persist($punto);
                        $this->emComercial->flush();
                    }
                    else
                    {
                        if(is_object($servicio->getProductoId()) && $servicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS")
                        {
                            $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                            "strCodEmpresa"                 => $idEmpresa,
                                                            "intIdProductoInternet"         => $servicio->getProductoId()->getId());
                            $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                            if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                            {
                                foreach($arrayInfoMapeoProds as $arrayInfoProd)
                                {
                                    $intIdProdIp        = $arrayInfoProd["intIdProdIp"];
                                    $strCaractRelProdIp = $arrayInfoProd["strCaractRelProdIp"];
                                    $arrayIdsProdsIps[] = $arrayInfoProd["intIdProdIp"];
                                }
                            }
                            else
                            {
                                throw new \Exception("No se ha podido obtener el correcto mapeo del servicio con la ip respectiva");
                            }
                        }
                    }
                    $boolFalse      = false;
                    if(isset($strCaractRelProdIp) && !empty($strCaractRelProdIp) &&
                       isset($intIdProdIp) && !empty($intIdProdIp))
                    {
                        $arrayServicios     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                            ->createQueryBuilder('s')
                            ->innerJoin('schemaBundle:InfoServicioProdCaract', 'car', 'WITH', 'car.servicioId = s.id')
                            ->innerJoin('schemaBundle:AdmiProductoCaracteristica', 'pc', 'WITH',
                                    'pc.id = car.productoCaracterisiticaId')
                            ->innerJoin('schemaBundle:AdmiCaracteristica', 'c', 'WITH', 'c.id = pc.caracteristicaId')
                            ->where('s.puntoId = :puntoId')
                            ->andWhere("s.productoId = :productoId")
                            ->andWhere("car.valor = :idServioInt")
                            ->andWhere("c.descripcionCaracteristica = :desCaracteristica")
                            ->andWhere("car.estado = :estadoActivo")
                            ->setParameter('puntoId', $punto->getId())
                            ->setParameter('productoId', $intIdProdIp)
                            ->setParameter('idServioInt', $servicio->getId())
                            ->setParameter('desCaracteristica', $strCaractRelProdIp)
                            ->setParameter('estadoActivo', 'Activo')
                            ->getQuery()
                            ->getResult();
                        $arrayServicios[]   = $servicio;
                    }
                    else
                    {
                        $arrayServicios = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                               ->findBy(array("puntoId" => $punto->getId()));
                    }
                    //reconectar servicios adicionales safecity
                    if(is_object($servicio) && is_object($servicio->getProductoId()))
                    {
                        $arrayParServAdd = array(
                            "intIdProducto"      => $servicio->getProductoId()->getId(),
                            "intIdServicio"      => $servicio->getId(),
                            "strNombreParametro" => 'CONFIG_PRODUCTO_DATOS_SAFE_CITY',
                            "strUsoDetalles"     => 'AGREGAR_SERVICIO_ADICIONAL',
                        );
                        $arrayProdCaracConfProAdd  = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                ->getServiciosPorProdAdicionalesSafeCity($arrayParServAdd);
                        if($arrayProdCaracConfProAdd['status'] == 'OK' && count($arrayProdCaracConfProAdd['result']) > 0)
                        {
                            foreach($arrayProdCaracConfProAdd['result'] as $arrayServicioConfProAdd)
                            {
                                $objServicioAdd = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                ->find($arrayServicioConfProAdd['idServicio']);
                                if(is_object($objServicioAdd) && $objServicioAdd->getEstado() == "In-Corte")
                                {
                                    $objServicioAdd->setEstado("Activo");
                                    $this->emComercial->persist($objServicioAdd);
                                    $this->emComercial->flush();
                                    //ingresar historial
                                    $objServicioHistorialAdd = new InfoServicioHistorial();
                                    $objServicioHistorialAdd->setServicioId($objServicioAdd);
                                    $objServicioHistorialAdd->setObservacion("Se reactivo el Servicio");
                                    $objServicioHistorialAdd->setEstado("Activo");
                                    $objServicioHistorialAdd->setUsrCreacion($usrCreacion);
                                    $objServicioHistorialAdd->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorialAdd->setIpCreacion($ipCreacion);
                                    $objServicioHistorialAdd->setAccion($accionObj->getNombreAccion());
                                    $this->emComercial->persist($objServicioHistorialAdd);
                                    $this->emComercial->flush();
                                }
                            }
                            //reconectar servicio security ng firewall
                            $strParametroProdAdd = "PRODUCTO_SECURITY_NG_FIREWALL";
                            if(is_object($objServicioAdd))
                            {
                                $arrayParametrosDet  = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
                                                            ->getOne('PARAMETROS PROYECTO GPON SAFECITY',
                                                                    'INFRAESTRUCTURA',
                                                                    'PARAMETROS',
                                                                    'VALIDAR RELACION SERVICIO ADICIONAL CON DATOS SAFECITY',
                                                                    $objServicioAdd->getProductoId()->getId(),
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    '',
                                                                    $idEmpresa);
                                if(!empty($arrayParametrosDet["valor7"]) && isset($arrayParametrosDet["valor7"]))
                                {
                                    $strParametroProdAdd = $arrayParametrosDet["valor7"];
                                }
                            }
                            $arrayParObtenerServ = array(
                                "objPunto"       => $servicio->getPuntoId(),
                                "strParametro"   => $strParametroProdAdd,
                                "strCodEmpresa"  => $idEmpresa,
                                "strUsrCreacion" => $usrCreacion,
                                "strIpCreacion"  => $ipCreacion
                            );
                            $arrayResultSerNgFirewall = $this->servicioGeneral->getServicioGponPorProducto($arrayParObtenerServ);
                            if($arrayResultSerNgFirewall["status"] == "OK")
                            {
                                $objServicioAdd = $arrayResultSerNgFirewall["objServicio"];
                                if(is_object($objServicioAdd) && $objServicioAdd->getEstado() == "In-Corte")
                                {
                                    $objServicioAdd->setEstado("Activo");
                                    $this->emComercial->persist($objServicioAdd);
                                    $this->emComercial->flush();
                                    //ingresar historial
                                    $objServicioHistorialAdd = new InfoServicioHistorial();
                                    $objServicioHistorialAdd->setServicioId($objServicioAdd);
                                    $objServicioHistorialAdd->setObservacion("Se reactivo el Servicio");
                                    $objServicioHistorialAdd->setEstado("Activo");
                                    $objServicioHistorialAdd->setUsrCreacion($usrCreacion);
                                    $objServicioHistorialAdd->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorialAdd->setIpCreacion($ipCreacion);
                                    $objServicioHistorialAdd->setAccion($accionObj->getNombreAccion());
                                    $this->emComercial->persist($objServicioHistorialAdd);
                                    $this->emComercial->flush();
                                }
                            }
                        }
                    }
                    //cortar servicios adicionales
                    foreach($arrayServicios as $objServicioPun)
                    {
                        if(($objServicioPun->getEstado() == "In-Corte" || $objServicioPun->getEstado() == "In-Corte-SinEje"
                            || $objServicioPun->getEstado() == "In-Temp")
                            && ($strEsIsb !== 'SI' 
                                || ($strEsIsb === 'SI' && is_object($objServicioPun->getProductoId()) 
                                    && ($objServicioPun->getId() === $servicio->getId() 
                                        || (isset($arrayIdsProdsIps) && !empty($arrayIdsProdsIps)
                                            && in_array($objServicioPun->getProductoId()->getId(), $arrayIdsProdsIps))))))
                        {
                            $objProductoAdicional = $objServicioPun->getProductoId();
                            if(is_object($objProductoAdicional) 
                                && strpos($objProductoAdicional->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID') !== $boolFalse)
                            {
                                $objSpcSuscriberId  = $this->servicioGeneral
                                                        ->getServicioProductoCaracteristica($objServicioPun, "SUSCRIBER_ID", $objProductoAdicional);
                                if(is_object($objSpcSuscriberId))
                                {
                                    $strActualizaServicio = "NO";
                                }
                                else
                                {
                                    $strActualizaServicio = "SI";
                                }
                            }
                            else
                            {
                                $strActualizaServicio = "SI";
                            }
                            
                            if($strActualizaServicio === "SI")
                            {
	                            // RECONECTAR SERVICIO NETCAM
	                            $arrayParamsBusqCaracServCam = array("intIdServicio"                => $objServicioPun->getId(),
	                                                                 "strDescripcionCaracteristica" => "CAMARA 3DEYE",
	                                                                 "strEstadoSpc"                 => "Activo");
	
	                            $objServCaractCam = $this->emComercial->getRepository('schemaBundle:InfoServicio')
	                                                                  ->getCaracteristicaServicio($arrayParamsBusqCaracServCam);
	
	                            $arrayParamsBusqCaracServRol = array("intIdServicio"                => $objServicioPun->getId(),
	                                                                 "strDescripcionCaracteristica" => "ROL 3DEYE",
	                                                                 "strEstadoSpc"                 => "Activo");
	
	                            $objServCaractRol = $this->emComercial->getRepository('schemaBundle:InfoServicio')
	                                                                  ->getCaracteristicaServicio($arrayParamsBusqCaracServRol);
	
	                            if(is_object($objServCaractCam) && is_object($objServCaractRol))
	                            {
		                            $arrayRespReconectarServicio = $this->servicePortalNetCam->reconectarServicioNetCam($objServicioPun->getId(),
		                                                                                                                $idAccion);
	                            }
	
	                            if($arrayRespReconectarServicio["strStatus"] == "ERROR")
	                            {
		                            throw new \Exception("No se pudo reactivar el servicio del cliente!<br>
																	Mensaje:".$arrayRespReconectarServicio["strMessage"]);
	                            }
	                            
                                $objServicioPun->setEstado("Activo");
                                $this->emComercial->persist($objServicioPun);
                                $this->emComercial->flush();

                                //historial del servicio
                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($objServicioPun);
                                $servicioHistorial->setObservacion("Se reactivo el Servicio");
                                $servicioHistorial->setEstado("Activo");
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $servicioHistorial->setAccion($accionObj->getNombreAccion());
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                            }
                        }
                    }
                }
                else
                {
                    $servicio->setEstado("Activo");
                    $this->emComercial->persist($servicio);
                    $this->emComercial->flush();

                    //historial del servicio
                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    $servicioHistorial->setObservacion("Se reactivo el Servicio");
                    $servicioHistorial->setEstado("Activo");
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $servicioHistorial->setAccion($accionObj->getNombreAccion());
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                }
                //enviar mail
                $asunto = "Reactivacion de Servicio";
                $this->servicioGeneral->enviarMailReconectarServicio($asunto, $servicio, '', $elemento, 
                                                                     $interfaceElemento->getNombreInterfaceElemento(), 
                                                                     $servicioHistorial, $usrCreacion, $ipCreacion, $prefijoEmpresa);
            }
            else
            {
                $status = "ERROR";
                $mensaje = "No se pudo Reactivar al Cliente! <br> Mensaje: ".$mensaje;
                throw new \Exception($mensaje);       
            }
        }
        catch(\Exception $e)
        {
            if($this->emInfraestructura->getConnection()->isTransactionActive())
            {
                $this->emInfraestructura->getConnection()->rollback();
            }

            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
            }
            $status = "ERROR";
            $mensaje = "ERROR EN LA LOGICA DE NEGOCIO, " . $e->getMessage();
            $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => $mensaje);
            return $arrayFinal;
        }

        //*---------------------------------------------------------------------*/   
        //*DECLARACION DE COMMITS*/
        if($this->emInfraestructura->getConnection()->isTransactionActive())
        {
            $this->emInfraestructura->getConnection()->commit();
        }

        if($this->emComercial->getConnection()->isTransactionActive())
        {
            $this->emComercial->getConnection()->commit();
        }

        //*----------------------------------------------------------------------*/
        
        if($ejecutaLdap =="SI" && $status=="OK")
        {
            $strPrefijoEmpresaOrigen = ($strEsIsb === 'SI' && $strPrefijoEmpresaOrigen === 'MD') ? 'TN': $strPrefijoEmpresaOrigen ;
            $objResultadoJsonLdap    = $this->servicioGeneral->ejecutarComandoLdap("A", $idServicio, $strPrefijoEmpresaOrigen);
            
            if($objResultadoJsonLdap->status != "OK")
            {
                $objResultadoJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("N", $idServicio, $strPrefijoEmpresaOrigen);
                if($objResultadoJsonLdap->status != "OK")
                {
                    $mensaje = $mensaje . "<br>" . $objResultadoJsonLdap->mensaje;
                }
            }
            
            $this->serviceProcesoMasivo->finalizaPmsPorOpcion(array("intIdPunto"            => $servicio->getPuntoId()->getId(),
                                                                    "strOpcionEjecutante"   => "REACTIVACION_INDIVIDUAL_INTERNET",
                                                                    "strCodEmpresa"         => $idEmpresa,
                                                                    "strPrefijoEmpresa"     => $strPrefijoEmpresaOrigen,
                                                                    "strUsrCreacion"        => $usrCreacion,
                                                                    "strIpCreacion"         => $ipCreacion));
        }
        
        if($status === "OK" && $prefijoEmpresa === "MD")
        {
            $arrayRespuestaReactivacionAdicPlan = $this->reactivarProductosAdicionalesEnPlan(array( "objServicio"       => $servicio,
                                                                                                    "strUsrCreacion"    => $usrCreacion,
                                                                                                    "strClientIp"       => $ipCreacion,
                                                                                                    "strCodEmpresa"     => $idEmpresa));
            if($arrayRespuestaReactivacionAdicPlan["status"] === "ERROR")
            {
                $mensaje = $mensaje . "<br>" .$arrayRespuestaReactivacionAdicPlan["mensaje"];
            }
            
            $this->reactivarServiciosAdicionalesPorPunto(array( "objServicioInternet"   => $servicio,
                                                                "idAccion"              => $idAccion,
                                                                "usrCreacion"           => $usrCreacion, 
                                                                "clientIp"              => $ipCreacion,
                                                                "strCodEmpresa"         => $idEmpresa,
                                                                "strMsjHistorial"       => "Se reactivo el Servicio"));
            
            //EJECUTAR VALIDACIÓN DE PROMOCIONES BW
            $arrayParametrosInfoBw = array();
            $arrayParametrosInfoBw['intIdServicio']     = $servicio->getId();
            $arrayParametrosInfoBw['intIdEmpresa']      = $idEmpresa;
            $arrayParametrosInfoBw['strTipoProceso']    = "CAMBIO_EQUIPO";
            $arrayParametrosInfoBw['strValor']          = $servicio->getId();
            $arrayParametrosInfoBw['strUsrCreacion']    = $usrCreacion;
            $arrayParametrosInfoBw['strIpCreacion']     = $ipCreacion;
            $arrayParametrosInfoBw['strPrefijoEmpresa'] = $strPrefijoEmpresaOrigen;
            $this->servicePromociones->configurarPromocionesBW($arrayParametrosInfoBw);
        }


        //Proceso para notificar la reconexión del servicio a konibit mediante GDA en caso de aplicar.
        try
        {
            if ($status === "OK" && $prefijoEmpresa === "MD" && is_object($servicio))
            {
                $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                        ->notificarKonibit(array ('intIdServicio'  =>  $servicio->getId(),
                                                  'strTipoProceso' => 'RECONECTAR',
                                                  'strTipoTrx'     => 'INDIVIDUAL',
                                                  'strUsuario'     =>  $usrCreacion,
                                                  'strIp'          =>  $ipCreacion,
                                                  'objUtilService' =>  $this->serviceUtil));

                //Se notifica la reconexión de los productos adicionales con la característica de KONIBIT.
                if (!empty($arrayServiciosProdKonibit['result']) && count($arrayServiciosProdKonibit['result']) > 0)
                {
                    foreach($arrayServiciosProdKonibit['result'] as $arrayValue)
                    {
                        $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                ->notificarKonibit(array ('intIdServicio'  =>  $arrayValue['idServicio'],
                                                          'strTipoProceso' => 'RECONECTAR',
                                                          'strTipoTrx'     => 'INDIVIDUAL',
                                                          'strUsuario'     =>  $usrCreacion,
                                                          'strIp'          =>  $ipCreacion,
                                                          'objUtilService' =>  $this->serviceUtil));
                    }
                }
            }
        }
        catch (\Exception $objException)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoReconectarServicioService->reconectarServicio->adicional',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objException->getMessage(),
                                             $usrCreacion,
                                             $ipCreacion);
        }


        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
        $arrayFinal[] = array('status' => "OK", 'mensaje' => $mensaje);
        
        return $arrayFinal;
    }
    
    /**
     * Función que realiza la reactivación de los servicios adicionales de un punto, en donde se incluye la reactivación de servicios 
     * I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-09-2019 Se agrega el mensaje del historial del servicio especificando la tecnología usada para las licencias 
     *                          de Internet Protegido
     * 
     * @param array $arrayParametros [ "objServicioInternet"   => objeto del servicio de Internet,
     *                                  "idAccion"              => id de la acción que se ejecuta,
     *                                  "usrCreacion"           => usuario de creación, 
     *                                  "clientIp"              => ip de creación,
     *                                  "strCodEmpresa"         => código de la empresa,
     *                                  "strMsjHistorial"       => mensaje del historial del servicio
     *                                ]
     *           
     */
    public function reactivarServiciosAdicionalesPorPunto($arrayParametros)
    {
        $boolFalse              = false;
        $objServicioInternet    = $arrayParametros["objServicioInternet"];
        $strUsrCreacion         = $arrayParametros["usrCreacion"];
        $strIpCreacion          = $arrayParametros["clientIp"];
        try
        {
            $arrayServicioAdicionales   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array('puntoId' => $objServicioInternet->getPuntoId(),
                                                                           'estado'  => 'In-Corte'));
            foreach($arrayServicioAdicionales as $objServicioAdicional)
            {
                if(is_object($objServicioAdicional) && is_object($objServicioAdicional->getProductoId()))
                {
                    $objProducto                = $objServicioAdicional->getProductoId();
                    $boolEsProdIProtegMultiPaid = strpos($objProducto->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID');
                    if($boolEsProdIProtegMultiPaid !== $boolFalse )
                    {
                        $objSpcAntivirus    = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($objServicioAdicional, "ANTIVIRUS", $objProducto);
                        if(is_object($objSpcAntivirus))
                        {
                            $arrayParametros["strMsjHistorial"] = "Se reactivó el servicio ".$objProducto->getDescripcionProducto().
                                                                  " con tecnología ".$objSpcAntivirus->getValor();
                        }
                        else
                        {
                            $arrayParametros["strMsjHistorial"] = "Se reactivó el Servicio";
                        }
                        $objSpcSuscriberId  = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($objServicioAdicional, "SUSCRIBER_ID", $objProducto);
                        if(is_object($objSpcSuscriberId))
                        {
                            $arrayParametros["idServicio"] = $objServicioAdicional->getId();
                            $this->reactivarServiciosOtros($arrayParametros);
                        }
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCortarServicioService->reactivarServiciosAdicionalesPorPunto', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
        } 
    }
    
    /**
     * 
     * Funcion para reconectar el servicios de los productos de empresa TNG
     * 
     * @author Jesús Banchen <jbanchen@telconet.ec>
     * @version 1.0 28-03-2018 
     * 
     * @param array $arrayParametros [
     *                                  "idServicio"   => id del servicio,
     *                                  "usrCreacion"  => usuario de creación,
     *                                  "ipCreacion"   => ip de creación,
     *                                  "idAccion"       => id de la accion
     *                               ]
     * 
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje de la transacción ejecutada
     *                               ]         
         
     * 
     * 
     */
    public function reconectarServicioTng($arrayPeticiones)
    {
        $strServicioId  = $arrayPeticiones['idServicio'];
        $strUsrCreacion = $arrayPeticiones['usrCreacion'];
        $strIpCreacion  = $arrayPeticiones['ipCreacion'];
        $intIdAccion    = $arrayPeticiones['idAccion'];

        try
        {
            $this->emComercial->getConnection()->beginTransaction();
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strServicioId);
            $objAccion   = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);

            $objServicio->setEstado("Activo");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se reactivó el servicio");
            $objServicioHistorial->setEstado("Activo");
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strIpCreacion);
            $objServicioHistorial->setAccion($objAccion->getNombreAccion());

            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();

            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->commit();
            }

            $this->emComercial->getConnection()->close();
            
            $strStatus = "OK";
            $strMensaje = "OK";
        }
        catch (\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $this->serviceUtil->insertError('Telcos+',
                                                'InfoReconectarServicioService->reconectarServicioTng',
                                                 $ex->getMessage(),
                                                 $strUsrCreacion,
                                                 $strIpCreacion);

            $strStatus = "ERROR";
            $strMensaje = "Error en el procesamiento de los datos..";
        }
        
        $arrayRespuesta[] = array('status' => $strStatus, 'mensaje' => $strMensaje);

        return $arrayRespuesta;
    }

    /**
     * Función que sirve para reactivar productos adicionales que forman parte de un plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 28-03-2019 Se corrige filtro de detalles de plan del servicio
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 05-08-2019 Se agrega la reactivación del servicio I. PROTEGIDO MULTI PAID con tecnología Kaspersky dentro del plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 23-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a función gestionarLicencias, ya que no está siendo usada
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 28-07-2020 Se elimina validación de planes nuevos vigentes, ya que los detalles de los productos no son dependientes a ésta
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "strUsrCreacion"    => usuario de creación,
     *                                  "strClientIp"       => ip del cliente
     *                               ]
     */
    public function reactivarProductosAdicionalesEnPlan($arrayParametros)
    {
        $boolFalse          = false;
        $boolMacAfeeEnPlan  = false;
        $objProductoMcAfee  = null;
        $boolActivaMcAfee   = false;
        $strUsrCreacion     = $arrayParametros["strUsrCreacion"];
        $strClientIp        = $arrayParametros['strClientIp'];
        $strCodEmpresa      = $arrayParametros["strCodEmpresa"];
        $strMostrarError    = "NO";
        $strMensaje         = "";
        try
        {
            $objServicio = $arrayParametros["objServicio"];
            
            if(!is_object($objServicio))
            {
                throw new \Exception("No se ha enviado el objeto servicio");
            }
            $objPlanServicio = $objServicio->getPlanId();
            if(is_object($objPlanServicio))
            {
                $intIdPlanServicio          = $objPlanServicio->getId();
                $arrayDetallesPlanServicio  = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                                ->findBy(array("planId" => $intIdPlanServicio));
                                
                foreach($arrayDetallesPlanServicio as $objDetallePlanServicio)
                {
                    $objProductoDetallePlan = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                                ->find($objDetallePlanServicio->getProductoId());
                    if(is_object($objProductoDetallePlan))
                    {
                        $boolVerificaMacAfeeEnPlan  = strpos($objProductoDetallePlan->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID');

                        if($boolVerificaMacAfeeEnPlan !== $boolFalse)
                        {
                            $boolMacAfeeEnPlan  = $boolVerificaMacAfeeEnPlan;
                            $objProductoMcAfee  = $objProductoDetallePlan;
                        }
                    }
                }
                
                if($boolMacAfeeEnPlan !== $boolFalse && is_object($objProductoMcAfee))
                {
                    $arrayRespuestaGetSpc = $this->licenciasMcAfee
                                                 ->obtenerValorServicioProductoCaracteristicaPorServicio(array( "objServicio"       => $objServicio,
                                                                                                                "objProducto"       => 
                                                                                                                $objProductoMcAfee,
                                                                                                                "strCaracteristica" => 
                                                                                                                "ACTIVACION POR MASIVO"));
                    
                    if($arrayRespuestaGetSpc["status"] === 'OK')
                    {
                        $objSpcActivacionPorMasivo = $arrayRespuestaGetSpc["mensaje"];
                        if(is_object($objSpcActivacionPorMasivo) && $objSpcActivacionPorMasivo->getValor() === "SI")
                        {
                            $boolActivaMcAfee = true;
                        }
                    }
                    if($boolActivaMcAfee)
                    {
                        $arrayParametros["strTipoProceso"]  = "INDIVIDUAL";
                        $arrayParametros["strOpcion"]       = "ACTIVACION POR MASIVO";
                        $this->activarService->activarProductosAdicionalesEnPlan($arrayParametros);
                    }
                    else
                    {
                        $arrayParametros["objProducto"] = $objProductoMcAfee;
                        $objSpcSuscriberId              = $this->servicioGeneral
                                                               ->getServicioProductoCaracteristica($objServicio, "SUSCRIBER_ID", $objProductoMcAfee);
                        if(is_object($objSpcSuscriberId))
                        {
                            $arrayParamsLicencias           = array("strProceso"                => "REACTIVACION_ANTIVIRUS",
                                                                    "strEscenario"              => "REACTIVACION_PROD_EN_PLAN",
                                                                    "objServicio"               => $objServicio,
                                                                    "objPunto"                  => $objServicio->getPuntoId(),
                                                                    "strCodEmpresa"             => $strCodEmpresa,
                                                                    "objProductoIPMP"           => $objProductoMcAfee,
                                                                    "strUsrCreacion"            => $strUsrCreacion,
                                                                    "strIpCreacion"             => $strClientIp,
                                                                    "strEstadoServicioInicial"  => $objServicio->getEstado()
                                                                    );
                            $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                            $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                            $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                            $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];
                            if($strStatusGestionLicencias === "ERROR")
                            {
                                $strMostrarError = "SI";
                                throw new \Exception($strMensajeGestionLicencias);
                            }
                            else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] !== "OK")
                            {
                                $strMostrarError = "SI";
                                throw new \Exception($arrayRespuestaWs["mensaje"]);
                            }
                            $strStatus = "OK";
                        }
                        else
                        {
                            $arrayRespuesta = $this->reactivacionProductoMcAfeeEnPlan($arrayParametros);
                        }
                    }
                }
            }
        } 
        catch (\Exception $e)
        {
            $strStatus = "ERROR";
            if($strMostrarError === "SI")
            {
                $strMensaje = $e->getMessage();
            }
            else
            {
                $strMensaje = "No se ha podido realizar la reactivación de los productos dentro del plan";
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoReconectarServicioService->reconectarProductosAdicionalesEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza la reactivación de un servicio con el producto McAfee incluido en el plan 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 02-04-2019 Se agrega log de errores en problemas de reactivación de suscripción McAfee
     * @since 1.0
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "objProducto"       => objeto del producto McAfee,
     *                                  "strUsrCreacion"    => usuario de creación,
     *                                  "strClientIp"       => ip del cliente
     *                               ]
     * @return array $arrayRespuesta [
     *                                  "status"    => OK o ERROR,
     *                                  "mensaje"   => mensaje de la transacción ejecutada
     *                               ]
    */
    public function reactivacionProductoMcAfeeEnPlan($arrayParametros)
    {
        $objServicio            = $arrayParametros["objServicio"];
        $objProductoMcAfee      = $arrayParametros["objProducto"];
        $strUsrCreacion         = $arrayParametros["strUsrCreacion"];
        $strClientIp            = $arrayParametros['strClientIp'];
        $strStatus              = "";
        $strMensaje             = "";
        $this->emComercial->beginTransaction();
        try
        {
            $intIdServicio          = $objServicio->getId();
            $arrayDatosCliente      = $this->emComercial->getRepository("schemaBundle:InfoPersona")
                                                        ->getDatosClientePorIdServicio($intIdServicio,false);
            $arrayInfoClienteMcAfee = $this->licenciasMcAfee
                                           ->obtenerInformacionClienteMcAffe(array( "intIdPersona"      => $arrayDatosCliente['ID_PERSONA'],
                                                                                    "intIdServicio"     => $intIdServicio,
                                                                                    "strNombrePlan"     => "",
                                                                                    "strEsActivacion"   => "SI",
                                                                                    "objProductoMcAfee" => $objProductoMcAfee));
            $arrayInfoClienteMcAfee["strTipoTransaccion"] = 'Reactivacion';
            if ($arrayInfoClienteMcAfee["strError"] == 'true')
            {
                $strStatus = "ERROR";
                throw new \Exception("problemas al obtener informacion del cliente");
            }
            $arrayInfoClienteMcAfee["strMetodo"]   = 'CrearSuscripcionMultidispositivo';
            $arrayInfoClienteMcAfee["intLIC_QTY"]  = $arrayInfoClienteMcAfee["strCantidadDispositivos"];
            $arrayInfoClienteMcAfee["intQTY"]      = 1;
            
            $arrayParamsGuardarSpc = array( "objServicio"       => $objServicio,
                                            "strUsrCreacion"    => $strUsrCreacion,
                                            "objProducto"       => $objProductoMcAfee);
            $arrayParamsGuardarSpc["strCaracteristica"] = "PARTNERREF";
            $arrayParamsGuardarSpc["strValor"]          = $arrayInfoClienteMcAfee["strPartnerRef"];
            $arrayRespuestaGuardarSpc = $this->licenciasMcAfee->guardaServicioProductoCaracteristicaPorServicio($arrayParamsGuardarSpc);
            if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
            {
                $strStatus = 'ERROR';
                throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);

            }
            $arrayParamsGetSpc  = array("objServicio"       => $objServicio,
                                        "objProducto"       => $objProductoMcAfee);
            $arrayParamsGetSpc["strCaracteristica"] = "REFERENCIA";
            $arrayRespuestaGetSpc = $this->licenciasMcAfee->obtenerValorServicioProductoCaracteristicaPorServicio($arrayParamsGetSpc);
            if($arrayRespuestaGetSpc["status"] == 'ERROR')
            {
                $strStatus = 'ERROR';
                throw new \Exception($arrayRespuestaGetSpc["mensaje"]);
            }
            $objSpcReferencia = $arrayRespuestaGetSpc["mensaje"];
            if (is_object($objSpcReferencia))
            {
                $objSpcReferencia->setEstado('Eliminado');
                $this->emComercial->persist($objSpcReferencia);
            }

            $arrayRespuestaSuscripcion  = $this->licenciasMcAfee->operacionesSuscripcionCliente($arrayInfoClienteMcAfee);

            if($arrayRespuestaSuscripcion["procesoExitoso"] == "false")
            {
                if($this->emComercial->getConnection()->isTransactionActive())
                {
                    $this->emComercial->rollback();
                }
                $this->emComercial->getConnection()->beginTransaction();
                //historial del servicio
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($objServicio);
                $objServicioHistorial->setObservacion($arrayRespuestaSuscripcion["mensajeRespuesta"]);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strClientIp);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->getConnection()->commit();
                $strStatus = 'ERROR';
                throw new \Exception($arrayRespuestaSuscripcion["mensajeRespuesta"]);
            }
            
            $arrayParamsGuardarSpc["strCaracteristica"] = "REFERENCIA";
            $arrayParamsGuardarSpc["strValor"]          = $arrayRespuestaSuscripcion["referencia"];
            $arrayRespuestaGuardarSpc = $this->licenciasMcAfee->guardaServicioProductoCaracteristicaPorServicio($arrayParamsGuardarSpc);
            if($arrayRespuestaGuardarSpc["status"] == 'ERROR')
            {
                $strStatus = 'ERROR';
                throw new \Exception($arrayRespuestaGuardarSpc["mensaje"]);
            }

            //historial del servicio
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se reactivo el producto ".$objProductoMcAfee->getDescripcionProducto()." incluido en el plan");
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strClientIp);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strStatus = 'OK';
            $this->emComercial->commit();
        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            $this->emComercial->getConnection()->close();
            if ($strStatus === 'ERROR')
            {
                $strMensaje = $e->getMessage();    
            }
            else
            {
                $strStatus  = "ERROR";
                $strMensaje = "";
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoReconectarServicioService->reactivacionProductoMcAfeeEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve para reconectar el servicio de internet 
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 21-04-2015
     * @since 1.0
     * 
     * @author Jesus Bozada   <jbozada@telconet.ec>
     * @version 1.2 07-10-2015 Se agrego validación de Service Port Id en reactivaciones Hw
     * 
     * @author Jesus Bozada   <jbozada@telconet.ec>
     * @version 1.3 17-06-2016 Se agrego validación de operatividad de elemento en proceso de reconexión
     *
     * @author Veronica Carrasco   <vcarrasco@telconet.ec>
     * @version 1.4 06-07-2016 Se agregro reconexion para servicio NETLIFE ZONE 
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.5 18-05-2017  Se actualiza metodo para que utilice el middleware de RDA.
     *                          Se eliminan validaciones de equipos conectados y obtencion de Ip ARP.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.6 25-01-2018  Se realiza ajuste para el producto de TN "INTERNET SMALL BUSINESS". No ingrese al flujo de Corte de servicio 
     *                          NETLIFE WIFI.
     *                          Se añade nuevo parametro de envio al servicio de RDA [empresa]. Si el producto es "INTERNET SMALL BUSINESS" 
     *                          se enviará 'TN' caso contrario 'MD'.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 15-05-2018  Se agrega prefijo de empresa en caso de existir error al reconectar, para que el envío de los parámetros 
     *                          al cortar sea correcto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 23-05-2018  Se agregan validaciones para servicios Small Business con OLTs TELLION
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.9 09-07-2018  Se agrega programación para flujo de servicios con tecnología ZTE
     * @since 1.8
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 28-11-2018  Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 1.9
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.11 11-02-2019 Se envía por parámetro el respectivo nombre técnico del producto IP
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 08-03-2019 Se agregan validaciones para no tomar en cuenta los servicios ips cuando el servicio principal sea TelcoHome
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.8 23-10-2018  Se modifica el procesamiento de información de servicios NetlifeZone
     * @since 1.7
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 04-05-2020 Se modifica la obtención de la variable $arrayProdIp usando la función obtenerParametrosProductosTnGpon por la
     *                          reestructuración de servicios Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 09-11-2020  Se agrega envío de nuevos parámetros al middleware en caso de clientes PYME (ip_fija_wan, tipo_plan_actual)
     * 
     * @author Andrea Cardenas <ascardenas@telconet.ec>
     * @version 1.12 27-07-2021 Se elimina registro en reactivaciones individuales
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.13 07-12-2021 Se elimina programación errónea para finalización de procesos masivos
     *
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.14 09-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción 
     *                          de Tn a Middleware
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.0 11-07-2022 Se agrega la validación de la caracteristica del servicio principal INTERNET VPNoGPON,
     *                         para obtener los servicios de las ip asociadas al servicio principal.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 18-03-2023 - Se agrega Validación y envio de prefijo empresa para llamada al middleware al reconectar servicio en ecuanet.
     * 
     * @param array $arrayParametros [servicioTecnico, interfaceElemento, servicio, producto, usrCreacion, ipCreacion, idEmpresa]
     */
    public function reconectarServicioMd($arrayParametros)
    {
        $strPrefEmpOrigen   = $arrayParametros['strPrefijoEmpresaOrigen'];
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $servicio           = $arrayParametros['servicio'];
        $producto           = $arrayParametros['producto'];
        $usrCreacion        = $arrayParametros['usrCreacion'];
        $ipCreacion         = $arrayParametros['ipCreacion'];
        $idEmpresa          = $arrayParametros['idEmpresa'];
        $flagMiddleware     = $arrayParametros['flagMiddleware'];
        $strEsIsb           = $arrayParametros['strEsIsb'];
        $intIdAccion        = $arrayParametros['intIdAccion'];
        $strMsjAdicional    = "";
        $strCapacidad1      = "";
        $strCapacidad2      = "";
        $strPrefijoEmpRDA   = ($strEsIsb == 'SI') ? 'TN' : 'MD' ;
        $strPrefijoEmpRDA   = ($strPrefEmpOrigen == 'TNP') ? 'TNP' : $strPrefijoEmpRDA ;
        $strPrefijoEmpRDA   = ($strPrefEmpOrigen == 'EN') ? 'EN' : $strPrefijoEmpRDA ;
        $status             = "ERROR";
        $mensaje            = "Error Desconocido";
        $spcIndiceCliente   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
        $spcPerfil          = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
        $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());
        $objIpElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array('elementoId' => $elemento->getId(), 'estado' => 'Activo'));
        $modeloElemento     = $elemento->getModeloElementoId();
        $arrayProdIp        = array();
        $arrayDataConfirmacionTn = array();
        try
        {
            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
            //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
            $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                             ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                               "detalleNombre"  => "OLT OPERATIVO"));
            if ($entitydetalleElemento)
            {
                if ($entitydetalleElemento->getDetalleValor() == "NO")
                {
                    $status         = "OK";
                    $mensaje        = "OK";
                    $arrayFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayFinal;
                }
            }
            
            if(is_object($servicio) && is_object($servicio->getProductoId()) 
                && $servicio->getProductoId()->getNombreTecnico() === "INTERNET SMALL BUSINESS")
            {
                $arrayParamsInfoProds   = array("strValor1ParamsProdsTnGpon"    => "PRODUCTOS_RELACIONADOS_INTERNET_IP",
                                                "strCodEmpresa"                 => $idEmpresa,
                                                "intIdProductoInternet"         => $servicio->getProductoId()->getId());
                $arrayInfoMapeoProds    = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->obtenerParametrosProductosTnGpon($arrayParamsInfoProds);
                if(isset($arrayInfoMapeoProds) && !empty($arrayInfoMapeoProds))
                {
                    foreach($arrayInfoMapeoProds as $arrayInfoProd)
                    {
                        $intIdProductoIp    = $arrayInfoProd["intIdProdIp"];
                        $strCaractRelProdIp = $arrayInfoProd["strCaractRelProdIp"];
                        $objProdIPSB        = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($intIdProductoIp);
                        $arrayProdIp[]      = $objProdIPSB;
                    }
                }
                else
                {
                    throw new \Exception("No se ha podido obtener el correcto mapeo del servicio con la ip respectiva");
                }
            }
            else
            {
                $arrayProdIp    = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->findBy(array( "nombreTecnico"  => "IP",
                                                                                                                "empresaCod"     => $idEmpresa,
                                                                                                                "estado"         => "Activo"));
            }
            
            /*
             * Codigo para diferenciar si se necesita utilizar middleware o si se necesita utilizar
             * el flujo tradicional
             */
            if($flagMiddleware)
            {
                $intIpFijasActivas = 0 ;
                
                //OBTENER NOMBRE CLIENTE
                $objPersona = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strNombreCliente       = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                                    $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER IDENTIFICACION
                $strIdentificacion      = $objPersona->getIdentificacionCliente();

                //OBTENER LOGIN
                $strLogin               = $servicio->getPuntoId()->getLogin();
                
                //OBTENER INDICE CLIENTE
                $spcIndiceCliente       = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
                if($spcIndiceCliente)
                {
                    $strIndiceCliente   = $spcIndiceCliente->getValor();
                }

                //OBTENER MAC ONT
                $spcMacOnt      = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC ONT", $producto);
                if($spcMacOnt)
                {
                    $strMacOnt  = $spcMacOnt->getValor();
                }

                //OBTENER SERIE ONT
                $elementoCliente    = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->find($servicioTecnico->getElementoClienteId());
                $strSerieOnt        = $elementoCliente->getSerieFisica();
                
                //obtener tipo de negocio
                $strTipoNegocio = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();

                //OBTENER SERVICIOS DEL PUNTO
                if(isset($strCaractRelProdIp) && !empty($strCaractRelProdIp) &&
                   isset($intIdProductoIp) && !empty($intIdProductoIp))
                {
                    $arrayServicios     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                        ->createQueryBuilder('s')
                        ->innerJoin('schemaBundle:InfoServicioProdCaract', 'car', 'WITH', 'car.servicioId = s.id')
                        ->innerJoin('schemaBundle:AdmiProductoCaracteristica', 'pc', 'WITH',
                                'pc.id = car.productoCaracterisiticaId')
                        ->innerJoin('schemaBundle:AdmiCaracteristica', 'c', 'WITH', 'c.id = pc.caracteristicaId')
                        ->where('s.puntoId = :puntoId')
                        ->andWhere("s.productoId = :productoId")
                        ->andWhere("car.valor = :idServioInt")
                        ->andWhere("c.descripcionCaracteristica = :desCaracteristica")
                        ->andWhere("car.estado = :estadoActivo")
                        ->andWhere("s.estado = :estadoServicio")
                        ->setParameter('puntoId', $servicio->getPuntoId()->getId())
                        ->setParameter('productoId', $intIdProductoIp)
                        ->setParameter('idServioInt', $servicio->getId())
                        ->setParameter('desCaracteristica', $strCaractRelProdIp)
                        ->setParameter('estadoActivo', 'Activo')
                        ->setParameter('estadoServicio', $servicio->getEstado())
                        ->getQuery()
                        ->getResult();
                    $arrayServicios[]   = $servicio;
                }
                else if(isset($arrayProdIp) && !empty($arrayProdIp))
                {
                    $arrayServicios     = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array( "puntoId"   => $servicio->getPuntoId()->getId(), 
                                                                            "estado"    => $servicio->getEstado()));
                }

                if($strMarcaOlt == "TELLION")
                {
                    //OBTENER MAC WIFI
                    $spcMacWifi   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "MAC WIFI", $producto);
                    if($spcMacWifi)
                    {
                        $strMacWifi = $spcMacWifi->getValor();
                    }

                    //OBTENER PERFIL
                    $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "PERFIL", $producto);
                    if(is_object($spcLineProfile))
                    {
                        $strLineProfile     = $spcLineProfile->getValor();
                        $arrayPerfil        = explode("_", $strLineProfile);
                        if($strEsIsb === "SI")
                        {
                            $strLineProfile = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
                            if($arrayPerfil[3] === 1)
                            {
                                $intIpFijasActivas  = 0;
                            }
                            else
                            {
                                if(isset($arrayProdIp) && !empty($arrayProdIp))
                                {
                                    $arrayDatosIp       = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServicios, $arrayProdIp, $servicio, 
                                                                                                        $servicio->getEstado(), 'Activo', $producto);
                                    $intIpFijasActivas  = $arrayDatosIp['ip_fijas_activas'];
                                }
                            }
                        }
                        else
                        {
                            $strLineProfile     = $arrayPerfil[0]."_".$arrayPerfil[1];
                            if($arrayPerfil[2] == 1)
                            {
                                $intIpFijasActivas  = 0;
                            }
                            else
                            {
                                $intIpFijasActivas  = $arrayPerfil[2];
                            }
                        }
                    }
                }
                else if($strMarcaOlt == "HUAWEI")
                {
                    //OBTENER SERVICE-PORT
                    $spcSpid        = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                    if($spcSpid)
                    {
                        $strSpid    = $spcSpid->getValor();
                    }

                    //OBTENER SERVICE PROFILE
                    $spcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                    if($spcServiceProfile)
                    {
                        $strServiceProfile = $spcServiceProfile->getValor();
                    }

                    //OBTENER LINE PROFILE NAME
                    $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
                    if($spcLineProfile)
                    {
                        $strLineProfile = $spcLineProfile->getValor();
                    }

                    //OBTENER VLAN
                    $spcVlan        = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                    if($spcVlan)
                    {
                        $strVlan    = $spcVlan->getValor();
                    }

                    //OBTENER GEM-PORT
                    $spcGemPort     = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
                    if($spcGemPort)
                    {
                        $strGemPort = $spcGemPort->getValor();
                    }

                    //OBTENER TRAFFIC-TABLE
                    $spcTraffic     = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);
                    if($spcTraffic)
                    {
                        $strTraffic = $spcTraffic->getValor();
                    }
                    
                    if(isset($arrayProdIp) && !empty($arrayProdIp))
                    {
                        $arrayDatosIp       = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServicios, $arrayProdIp, $servicio, 
                                                                                            $servicio->getEstado(), 'Activo', $producto);
                        $intIpFijasActivas  = $arrayDatosIp['ip_fijas_activas'];
                    }
                }
                else if($strMarcaOlt == "ZTE")
                {
                    $spcServiceProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SERVICE-PROFILE", $producto);
                    if($spcServiceProfile)
                    {
                        $strServiceProfile = $spcServiceProfile->getValor();
                    }
                    
                    //OBTENER SERVICE-PORT
                    $spcSpid        = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                    if($spcSpid)
                    {
                        $strSpid    = $spcSpid->getValor();
                    }
                    
                    //OBTENER CAPACIDAD1
                    $objCapacidad1 = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD1", $producto);
                    if(is_object($objCapacidad1))
                    {
                        $strCapacidad1 = $objCapacidad1->getValor();
                    }
                    
                    //OBTENER CAPACIDAD2
                    $objCapacidad2 = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "CAPACIDAD2", $producto);
                    if(is_object($objCapacidad2))
                    {
                        $strCapacidad2 = $objCapacidad2->getValor();
                    }
                    
                    if(isset($arrayProdIp) && !empty($arrayProdIp))
                    {
                        $arrayDatosIp       = $this->servicioGeneral->getInfoIpsFijaPunto(  $arrayServicios, $arrayProdIp, $servicio, 
                                                                                            $servicio->getEstado(), 'Activo', $producto);
                        $intIpFijasActivas  = $arrayDatosIp['ip_fijas_activas'];
                    }
                }
                
                if($strEsIsb === "SI")
                {
                    $objServProdCaracTipoNegocio = $this->servicioGeneral->getServicioProductoCaracteristica(   $servicio,
                                                                                                                "Grupo Negocio",
                                                                                                                $producto);
                    if(is_object($objServProdCaracTipoNegocio))
                    {
                        $strValorTipoNegocioProd    = $objServProdCaracTipoNegocio->getValor();
                        list($strTipoNegocio)       = explode($strPrefijoEmpRDA,$strValorTipoNegocioProd);
                    }
                    else
                    {
                        throw new \Exception("No existe Caracteristica Grupo Negocio asociada al servicio");
                    }
                }
                if($strPrefijoEmpRDA == 'TNP' && $strEsIsb != "SI")
                {
                    $strTipoNegocio = "HOME";
                }
                $arrayDatos = array(
                                        'serial_ont'            => $strSerieOnt,
                                        'mac_ont'               => $strMacOnt,
                                        'nombre_olt'            => $elemento->getNombreElemento(),
                                        'ip_olt'                => $objIpElemento->getIp(),
                                        'puerto_olt'            => $interfaceElemento->getNombreInterfaceElemento(),
                                        'modelo_olt'            => $modeloElemento->getNombreModeloElemento(),
                                        'gemport'               => $strGemPort,
                                        'service_profile'       => $strServiceProfile,
                                        'line_profile'          => $strLineProfile,
                                        'traffic_table'         => $strTraffic,
                                        'ont_id'                => $strIndiceCliente,
                                        'service_port'          => $strSpid,
                                        'vlan'                  => $strVlan,
                                        'estado_servicio'       => $servicio->getEstado(),
                                        'tipo_negocio_actual'   => $strTipoNegocio,
                                        'ip_fijas_activas'      => $intIpFijasActivas !== null ? $intIpFijasActivas : 0,
                                        'mac_wifi'              => $strMacWifi,
                                        'capacidad_up'          => $strCapacidad1,
                                        'capacidad_down'        => $strCapacidad2
                                    );
                if ($strPrefijoEmpRDA === 'MD')
                {
                    $arrayRespuestaSeteaInfo = $this->servicioGeneral
                                                    ->seteaInformacionPlanesPyme(array("intIdPlan"         => $servicio->getPlanId()->getId(),
                                                                                       "intIdPunto"        => $servicio->getPuntoId()->getId(),
                                                                                       "strConservarIp"    => "",
                                                                                       "strTipoNegocio"    => $strTipoNegocio,
                                                                                       "strPrefijoEmpresa" => $strPrefijoEmpRDA,
                                                                                       "strUsrCreacion"    => $usrCreacion,
                                                                                       "strIpCreacion"     => $ipCreacion,
                                                                                       "strTipoProceso"    => "REACTIVAR_PLAN",
                                                                                       "arrayInformacion"  => $arrayDatos));
                    if($arrayRespuestaSeteaInfo["strStatus"]  === "OK")
                    {
                        $arrayDatos = $arrayRespuestaSeteaInfo["arrayInformacion"];
                    }
                    else
                    {
                        $arrayFinal[] = array('status'  => $arrayRespuestaSeteaInfo["strStatus"],
                                              'mensaje' => "Existieron problemas al recuperar información necesaria ".
                                                           "para ejecutar proceso, favor notifique a Sistemas.");
                        return $arrayFinal;
                    }
                }
                $arrayDatosMiddleware = array(
                                                'nombre_cliente'        => $strNombreCliente,
                                                'login'                 => $strLogin,
                                                'identificacion'        => $strIdentificacion,
                                                'datos'                 => $arrayDatos,
                                                'opcion'                => $this->opcion,
                                                'ejecutaComando'        => $this->ejecutaComando,
                                                'usrCreacion'           => $usrCreacion,
                                                'ipCreacion'            => $ipCreacion,
                                                'empresa'               => $strPrefijoEmpRDA
                                            );

                $arrayFinal = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));

                $status     = $arrayFinal['status'];
                $mensaje    = $arrayFinal['mensaje'];
                
                $arrayDataConfirmacionTn    = array('nombre_cliente'    => $strNombreCliente,
                                                    'login'             => $strLogin,
                                                    'identificacion'    => $strIdentificacion,
                                                    'datos'             => array(   
                                                                                'serial_ont'                => $arrayDatos['serial_ont'],
                                                                                'nombre_olt'                => $arrayDatos['nombre_olt'],
                                                                                'ip_olt'                    => $arrayDatos['ip_olt'],
                                                                                'puerto_olt'                => $arrayDatos['puerto_olt'],
                                                                                'modelo_olt'                => $arrayDatos['modelo_olt'],
                                                                                'service_profile'           => $arrayDatos['service_profile'],
                                                                                'vlan'                      => $arrayDatos['vlan'],
                                                                                'service_port'              => $arrayDatos['service_port'],
                                                                                'estado_servicio'           => $arrayDatos['estado_servicio'],
                                                                                'tipo_negocio_actual'       => $arrayDatos['tipo_negocio_actual'],
                                                                                'opcion_confirmacion'       => $this->opcion,
                                                                                'respuesta_confirmacion'    => 'ERROR'
                                                                                ),
                                                    'opcion'            => $this->strConfirmacionTNMiddleware,
                                                    'ejecutaComando'    => $this->ejecutaComando,
                                                    'usrCreacion'       => $usrCreacion,
                                                    'ipCreacion'        => $ipCreacion,
                                                    'empresa'           => $strPrefijoEmpRDA,
                                                    'statusMiddleware'  => $status);
                
            }
            else
            {
                if($modeloElemento->getNombreModeloElemento()=="EP-3116")
                {
                    //1. OLT-OBTENCION DE LA MAC DEL WIFI
                    $scriptArray1   = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica",$modeloElemento);
                    $idDocumentoMac = $scriptArray1[0]->idDocumento;
                    $usuarioMac     = $scriptArray1[0]->usuario;
                    $macDinamica    = $this->getMacIpDinamica($servicioTecnico, $usuarioMac, $interfaceElemento, $spcIndiceCliente, $idDocumentoMac);

                    //2. OLT-OBTENCION IP WIFI - ARP
                    $scriptArray2   = $this->servicioGeneral->obtenerArregloScript("obtenerIpDinamicaArp",$modeloElemento);
                    $idDocumento2   = $scriptArray2[0]->idDocumento;
                    $usuarioIp      = $scriptArray2[0]->usuario;
                    $ipDinamicaArp  = $this->getIpDinamica($servicioTecnico, $usuarioIp, $interfaceElemento, $macDinamica->mensaje, $idDocumento2);

                    //2. OLT-OBTENCION IP WIFI - DHCP
                    $scriptArray3   = $this->servicioGeneral->obtenerArregloScript("obtenerIpDinamicaDhcp",$modeloElemento);
                    $idDocumento3   = $scriptArray3[0]->idDocumento;
                    $ipDinamicaDhcp = $this->getIpDinamica($servicioTecnico, $usuarioIp, $interfaceElemento, $macDinamica->mensaje, $idDocumento3);

                    //cambiar perfil a cortado
                    $resultadJson = $this->reconectarServicioOlt($elemento,$interfaceElemento,$spcIndiceCliente,$spcPerfil,$servicioTecnico);

                    if($resultadJson->status == "OK")
                    {
                        if($ipDinamicaDhcp->mensaje!="")
                        {
                            //4.  OLT-ACTUALIZACION TABLA IP DHCP
                            $scriptArray5 = $this->servicioGeneral->obtenerArregloScript("clearTablaIpDhcp",$modeloElemento);
                            $idDocumento5 = $scriptArray5[0]->idDocumento;
                            $this->clearTablaIp($servicioTecnico, $idDocumento5,$ipDinamicaDhcp->mensaje);
                        }

                        if($ipDinamicaArp->mensaje!="")
                        {
                            //4.  OLT-ACTUALIZACION TABLA IP ARP
                            $scriptArray4 = $this->servicioGeneral->obtenerArregloScript("clearTablaIpArp",$modeloElemento);
                            $idDocumento4 = $scriptArray4[0]->idDocumento;
                            $this->clearTablaIp($servicioTecnico, $idDocumento4,$ipDinamicaArp->mensaje);
                        }

                        $status  = "OK";
                        $mensaje = "OK";
                    }
                    else
                    {
                        $mensaje = $resultadJson->mensaje;
                        throw new \Exception($mensaje);
                    }
                }
                else if($modeloElemento->getNombreModeloElemento()=="MA5608T")
                {
                    //obtener ont
                    $elementoCliente             = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                        ->find($servicioTecnico->getElementoClienteId());
                    $nombreModeloElementoCliente = $elementoCliente->getModeloElementoId()->getNombreModeloElemento();

                    $spcSpid        = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);
                    $spcLineProfile = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "LINE-PROFILE-NAME", $producto);
                    $spcVlan        = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "VLAN", $producto);
                    $spcGemPort     = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "GEM-PORT", $producto);
                    $spcTraffic     = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "TRAFFIC-TABLE", $producto);

                    //se agrega validacion del Service Port
                    $arraySpid = array( 'modeloElemento'    => $modeloElemento,
                                        'interfaceElemento' => $interfaceElemento,
                                        'ontId'             => $spcIndiceCliente->getValor(),
                                        'servicioTecnico'   => $servicioTecnico);

                    $resultArraySpid = $this->cambiarPuertoService->getSpidHuawei($arraySpid);
                    $spidStatus      = $resultArraySpid[0]['status'];
                    $spidValor       = $resultArraySpid[0]['mensaje'];
                    //se verifica el status de recuperación de SPID
                    if($spidStatus != 'OK')
                    {                 
                        //Verificar si el SPID no existe en el equipo para otro cliente,
                        //En caso de no existir reconfigurar  el SPID para el cliente colocando 
                        //con el la informacion relacionado a perfil SUSFP
                        // vlan    -> 303
                        // gemPort -> 200
                        // traffic -> 200
                        $arrayVerificarSpidExistente = array('spid'               => $spcSpid->getValor(),
                                                             'vlan'               => '303',
                                                             'interfaceElemento'  => $interfaceElemento,
                                                             'ontId'              => $spcIndiceCliente->getValor(),
                                                             'gemPort'            => '200',
                                                             'trafficTable'       => '200',
                                                             'servicioTecnico'    => $servicioTecnico
                                                            );

                        $resultArrayVerificarSpid = $this->cambiarPuertoService->verificarSpidHuawei($arrayVerificarSpidExistente);
                        $spidStatusVerificarSpid  = $resultArrayVerificarSpid[0]['status'];
                        $spidValorVerificarSpid   = $resultArrayVerificarSpid[0]['mensaje'];

                        if($spidStatusVerificarSpid != 'OK') //Si es diferente de OK significa que el SPID se encuentra configurado en otro cliente
                        {                                                
                            $arrayFinal[] = array('status' => "ERROR",
                                                  'mensaje' => 'No se puede realizar la operacion, <br>'. $spidValorVerificarSpid);
                            return $arrayFinal;
                        }
                    }
                    else
                    {
                        //se compara el service port del Telcos contra el valor devuelto desde el equipo
                        if($spidValor != $spcSpid->getValor())
                        {                        
                            $arrayFinal[] = array('status' => "ERROR", 'mensaje' => 'El cliente no se encuentra en el SPID correcto, '.
                                                                                    'esta registrado físicamente en el SPID: '.$spidValor.
                                                                                    ', favor regularizar.');
                            return $arrayFinal;
                        }
                    }

                    $arrParamReconectar = array   (
                                                        'elemento'          => $elemento,
                                                        'interfaceElemento' => $interfaceElemento,
                                                        'spcIndiceCliente'  => $spcIndiceCliente,
                                                        'spcSpid'           => $spcSpid,
                                                        'servicioTecnico'   => $servicioTecnico,
                                                        'spcServiceProfile' => $nombreModeloElementoCliente,
                                                        'spcLineProfile'    => $spcLineProfile,
                                                        'spcVlan'           => $spcVlan,
                                                        'spcGemPort'        => $spcGemPort,
                                                        'spcTrafficTable'   => $spcTraffic
                                                    );
                    $resultadJson = $this->reconectarServicioOltHuawei($arrParamReconectar);

                    $status       = $resultadJson->status;

                    if($status!="OK")
                    {
                        $arrParamCorte = array  (
                                                    'elemento'          => $elemento,
                                                    'interfaceElemento' => $interfaceElemento,
                                                    'spcIndiceCliente'  => $spcIndiceCliente,
                                                    'spcSpid'           => $spcSpid,
                                                    'servicioTecnico'   => $servicioTecnico,
                                                    'serviceProfile'    => $nombreModeloElementoCliente
                                                );
                        $jsonRollback = $this->cortarService->cortarServicioOlt($arrParamCorte);

                        $status  = "ERROR";
                        $mensaje = "No se pudo Reconectar al Cliente! <br> Mensaje Error:".$resultadJson->mensaje ."<br>".
                                    "Mensaje Rollback:".$jsonRollback->mensaje;
                        throw new \Exception($mensaje);
                    }
                }
                else
                {
                    throw new \Exception("Modelo de ONT no tiene aprovisionamiento");
                }
            }
            
            //Reconexion de servicio NETLIFE WIFI
            if($status == "OK")
            {
                if ($strEsIsb !== "SI" && $strPrefEmpOrigen != 'TNP')
                {
                    //Se agrega programación corregida para la reconexión de servicios NetlifeZone
                    $arrayParametrosCortarNz = array();
                    $arrayParametrosCortarNz['objServicio']    = $servicio;
                    $arrayParametrosCortarNz['intIdEmpresa']   = $idEmpresa;
                    $arrayParametrosCortarNz['intIdAccion']    = $intIdAccion;
                    $arrayParametrosCortarNz['strUsrCreacion'] = $usrCreacion;
                    $arrayParametrosCortarNz['strIpCreacion']  = $ipCreacion;
                    $strMsjAdicional                           = $this->reconectarServiciosNetlifeWifi($arrayParametrosCortarNz);
                }
            }
            else
            {
                if($flagMiddleware)
                {
                    //CORTAR AL CLIENTE POR ERROR
                    $arrayDatos['estado_servicio'] = 'Activo';
                    unset($arrayDatos['ip_fijas_activas']);
                    $arrayDatosMiddleware = array(
                                                    'nombre_cliente'        => $strNombreCliente,
                                                    'login'                 => $strLogin,
                                                    'identificacion'        => $strIdentificacion,
                                                    'datos'                 => $arrayDatos,
                                                    'opcion'                => 'CORTAR',
                                                    'ejecutaComando'        => $this->ejecutaComando,
                                                    'usrCreacion'           => $usrCreacion,
                                                    'ipCreacion'            => $ipCreacion,
                                                    'empresa'               => $strPrefijoEmpRDA
                                                );

                    $arrayReactivar = $this->rdaMiddleware->middleware(json_encode($arrayDatosMiddleware));

                    $mensajeRollback  = "No se pudo Cortar al Cliente! <br> Mensaje Error:".$mensaje ."<br>".
                                        "Mensaje Rollback:".$arrayReactivar['mensaje'];
                    throw new \Exception($mensajeRollback);
                }
            }
            $arrayDataConfirmacionTn['datos']['respuesta_confirmacion'] = "OK";
        }
        catch(\Exception $e)
        {
            $status         = "ERROR";
            $mensaje        = $e->getMessage();
            $arrayFinal[]   = array('status'                    => "ERROR", 
                                    'mensaje'                   => $mensaje, 
                                    'msjAdicional'              => $strMsjAdicional,
                                    'arrayDataConfirmacionTn'   => $arrayDataConfirmacionTn);
            return $arrayFinal;
        }
        
        $arrayRespuesta[] = array(  'status'                    => $status, 
                                    'mensaje'                   => $mensaje, 
                                    'msjAdicional'              => $strMsjAdicional,
                                    'arrayDataConfirmacionTn'   => $arrayDataConfirmacionTn);
        return $arrayRespuesta;
    }
    
    public function clearTablaIp($servicioTecnico,$idDocumento,$ip){
        $datos = $ip;
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
            $this->host."' '".$idDocumento."' 'usuario' 'SSH' '".$servicioTecnico->getElementoId()."' '".
            $datos."' '".$this->pathParameters."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
        $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    public function getMacIpDinamica($servicioTecnico, $usuario, $interfaceElemento, $servProdCaractIndice, $idDocumentoMac){
        $datos = $interfaceElemento->getNombreInterfaceElemento().",".$servProdCaractIndice->getValor();
        $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'obtenerMacIpDinamica' '".$servicioTecnico->getElementoId()."' '".$usuario."' '".
            $interfaceElemento->getNombreInterfaceElemento()."' '".$idDocumentoMac."' '".$datos."' '".$this->pathParameters."'";
        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    public function getIpDinamica($servicioTecnico, $usuario, $interfaceElemento, $mac, $idDocumento){
        $datos = $mac;
        $comando1 = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_datos.jar '".
            $this->host."' 'obtenerIpDinamica' '".$servicioTecnico->getElementoId()."' '".$usuario."' '".
            $interfaceElemento->getNombreInterfaceElemento()."' '".$idDocumento."' '".$datos."' '".$this->pathParameters."'";
        $salida1= shell_exec($comando1);
        $pos1 = strpos($salida1, "{"); 
        $jsonObj1= substr($salida1, $pos1);
        $resultadJson1 = json_decode($jsonObj1);
        
        return $resultadJson1;
    }
    
    private function reconectarServicioOlt($elemento,$interfaceElemento,$servProdCaracIndiceCliente,$servProdCaractPerfil,$servicioTecnico){
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray = $this->servicioGeneral->obtenerArregloScript("reconectarCliente",$elemento->getModeloElementoId());
        $idDocumento= $scriptArray[0]->idDocumento;
        $usuario= $scriptArray[0]->usuario;
        $protocolo= $scriptArray[0]->protocolo;
        /*----------------------------------------------------------------------*/
        
        $datos = $interfaceElemento->getNombreInterfaceElemento().",".$servProdCaracIndiceCliente->getValor().",".$servProdCaracIndiceCliente->getValor().",".$servProdCaractPerfil->getValor();
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
            $this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".$servicioTecnico->getElementoId()."' '".
            $datos."' '".$this->pathParameters."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
        $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    /**
     * Funcion que sirve para realizar la reconexion del servicio y para rollback del corte en olt huawei
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 11-05-2015
     * @version 1.1 01-09-2015 Se agregan nuevos parametros en la ejecución de script
     * @param $arrayParametros (elemento, interfaceElemento, spcIndiceCliente, spcSpid, servicioTecnico, spcServiceProfile, spcLineProfile,
     *                          spcVlan, spcGemPort, spcTrafficTable)
     */
    public function reconectarServicioOltHuawei($arrayParametros)
    {
        $elemento           = $arrayParametros['elemento'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $spcIndiceCliente   = $arrayParametros['spcIndiceCliente'];
        $spcSpid            = $arrayParametros['spcSpid'];
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $serviceProfile     = $arrayParametros['spcServiceProfile'];
        $lineProfile        = $arrayParametros['spcLineProfile'];
        $vlan               = $arrayParametros['spcVlan'];
        $gemPort            = $arrayParametros['spcGemPort'];
        $trafficTable       = $arrayParametros['spcTrafficTable'];
        
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray    = $this->servicioGeneral->obtenerArregloScript("reconectarCliente",$elemento->getModeloElementoId());
        $idDocumento    = $scriptArray[0]->idDocumento;
        $usuario        = $scriptArray[0]->usuario;
        $protocolo      = $scriptArray[0]->protocolo;
        /*----------------------------------------------------------------------*/
        
         //dividir interface para obtener tarjeta y puerto pon
        list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
        $datos = $spcSpid->getValor().",".$tarjeta.",".$puertoPon.",".$spcIndiceCliente->getValor().",".$lineProfile->getValor()
                 .",".$serviceProfile.",".$spcSpid->getValor().",".$vlan->getValor().",".$tarjeta.",".$puertoPon
                 .",".$spcIndiceCliente->getValor().",".$gemPort->getValor().",".$trafficTable->getValor().",".$trafficTable->getValor().",".$tarjeta
                 .",".$puertoPon.",".$spcIndiceCliente->getValor().",".$puertoPon.",".$spcIndiceCliente->getValor().",".$tarjeta.",".$puertoPon
                 .",".$spcIndiceCliente->getValor().",".$trafficTable->getValor();
        $comando        = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
                            $this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".
                            $servicioTecnico->getElementoId()."' '".$datos."' '".$this->pathParameters."'";

        $salida         = shell_exec($comando);
        
        $pos            = strpos($salida, "{"); 
        $jsonObj        = substr($salida, $pos);
        $resultadJson   = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    /**
     * Funcion que sirve para reconectar clientes transtelco
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 13-02-2016
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 09-05-2016 Se agrega parametro empresa en metodo reconectarServicioTtco por conflictos de producto INTERNET DEDICADO
     * 
     * @param String $servicio
     * @param String $servicioTecnico
     * @param String $interfaceElemento
     * @param String $elemento
     * @param String $modeloElemento
     */
    public function reconectarServicioTtco($servicio, $servicioTecnico, $interfaceElemento, $elemento, $modeloElemento)
    {
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray          = $this->servicioGeneral->obtenerArregloScript("reconectarCliente",$modeloElemento);
        $idDocumento          = $scriptArray[0]->idDocumento;
        $usuario              = $scriptArray[0]->usuario;
        $protocolo            = $scriptArray[0]->protocolo;
        $reqAprovisionamiento = $modeloElemento->getReqAprovisionamiento();
        /*----------------------------------------------------------------------*/
        
        //Se agrega validacion de Radio para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
        $entitydetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                         ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                           "detalleNombre"  => "RADIO OPERATIVO"));

        if ($entitydetalleElemento)
        {
            if ($entitydetalleElemento->getDetalleValor() == "NO")
            {
                $reqAprovisionamiento = "NO";
            }
        }
        
        if($reqAprovisionamiento == "SI")
        {
            if($modeloElemento->getNombreModeloElemento()=="6524")
            {
                $resultadJson = $this->reconectarCliente6524($idDocumento, $usuario, $protocolo, 
                                                             $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="7224")
            {
                $resultadJson = $this->reconectarCliente7224($idDocumento, $usuario, $protocolo, 
                                                             $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="AuD8000-12")
            {
                $resultadJson = $this->reconectarClienteAuD8000($idDocumento, $usuario, $protocolo, 
                                                                $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="R1AD24A")
            {
                $resultadJson = $this->reconectarClienteR1AD24A($idDocumento, $usuario, $protocolo, 
                                                                $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="R1AD48A")
            {
                $resultadJson = $this->reconectarClienteR1AD48A($idDocumento, $usuario, $protocolo, 
                                                                $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="A2024")
            {
                $resultadJson = $this->reconectarClienteA2024($idDocumento, $usuario, $protocolo, 
                                                              $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="A2048")
            {
                $resultadJson = $this->reconectarClienteA2048($idDocumento, $usuario, $protocolo, 
                                                              $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="MEA1")
            {
                $resultadJson = $this->reconectarClienteMea1($idDocumento, $usuario, $protocolo, 
                                                             $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($modeloElemento->getNombreModeloElemento()=="MEA3")
            {
                $resultadJson = $this->reconectarClienteMea3($idDocumento, $usuario, $protocolo, 
                                                             $elemento, $interfaceElemento->getNombreInterfaceElemento());
            }
            else if( $modeloElemento->getNombreModeloElemento()=="IPTECOM" || 
                     $modeloElemento->getNombreModeloElemento()=="411AH"   || 
                     $modeloElemento->getNombreModeloElemento()=="433AH" )
            {
                //base
                $puntoId = $servicio->getPuntoId();
                $punto   = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId->getId());
                $login   = $punto->getLogin();

                $producto           = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                        ->findBy(array( "descripcionProducto" => "INTERNET DEDICADO", 
                                                                        "estado"              => "Activo",
                                                                        "empresaCod"          => '18'));
                $caracteristica     = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                        ->findBy(array( "descripcionCaracteristica" => "MAC"));
                $prodCaract         = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                        ->findBy(array( "productoId"       => $producto[0]->getId(), 
                                                                        "caracteristicaId" => $caracteristica[0]->getId()));
                $servicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                        ->findBy(array( "servicioId"                => $servicio->getId(), 
                                                                        "productoCaracterisiticaId" => $prodCaract[0]->getId()));
                $mac                = $servicioProdCaract[0]->getValor();

                /*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray1 = $this->servicioGeneral->obtenerArregloScript("encontrarNumbersMac",$modeloElemento);
                $idDocumento1 = $scriptArray1[0]->idDocumento;
                $usuario1     = $scriptArray1[0]->usuario;
                /*----------------------------------------------------------------------*/
                
                //numbers de la mac
                $datos2        = $mac;
                $resultadJson2 = $this->cortarClienteIPTECOM($idDocumento1, $usuario1, "radio", $elemento, $datos2);
                $resultado     = $resultadJson2->mensaje;
                
                $numbers       = explode("\n", $resultado);
                $flag          = 0;

                for ($i = 0; $i < count($numbers); $i++)
                {
                    if (stristr($numbers[$i], $mac) === FALSE)
                    {
                        
                    }
                    else
                    {

                        if ($modeloElemento->getNombreModeloElemento() == "411AH")
                        {
                            $numero = explode(" ", $numbers[$i]);
                        }
                        else
                        {
                            $numero = explode(" ", $numbers[$i - 1]);
                        }
                        $flag = 1;
                        break;
                    }
                }

                if ($flag == 0)
                {
                    $status           = "ACL";
                    $mensaje          = "ERROR ACL";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayRespuesta;
                }

                //base
                if ($modeloElemento->getNombreModeloElemento() == "411AH")
                {
                    $datos = $mac . "," . $numero[0];
                }
                else
                {
                    $datos = $mac . "," . $numero[1];
                }

                $resultadJson1 = $this->reconectarClienteIPTECOM($idDocumento, $usuario, "radio", $elemento, $datos);

                //servidor
                $datos1         = $login;
                $elementoRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                          ->findOneBy(array( "nombreElemento" => "ttcoradius"));

                /*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("reconectarClienteRadius",$elementoRadius->getModeloElementoId());
                $idDocumento = $scriptArray[0]->idDocumento;
                $usuario     = $scriptArray[0]->usuario;
                $protocolo   = $scriptArray[0]->protocolo;
                /*----------------------------------------------------------------------*/
                
                $resultadJson = $this->reconectarClienteRADIUS($idDocumento, $usuario, "servidor", $elementoRadius, $datos1);

                if($resultadJson->status=="OK" && $resultadJson1->status=="OK")
                {
                    $status           = "OK";
                    $mensaje          = "OK";
                    $arrayRespuesta[] = array('status'=>$status, 'mensaje'=>$mensaje);
                }
                else
                {
                    $status           = "ERROR";
                    $mensaje          = "ERROR";
                    $arrayRespuesta[] = array('status'=>$status, 'mensaje'=>$mensaje);
                }
            }

            if($modeloElemento->getNombreModeloElemento()!="IPTECOM" && 
               $modeloElemento->getNombreModeloElemento()!="411AH"   && 
               $modeloElemento->getNombreModeloElemento()!="433AH")
            {
                if($resultadJson->status=="OK")
                {
                    $status           = "OK";
                    $mensaje          = "OK";
                    $arrayRespuesta[] = array('status'=>$status, 'mensaje'=>$mensaje);
                }
                else
                {
                    $status           = "ERROR";
                    $mensaje          = "ERROR";
                    $arrayRespuesta[] = array('status'=>$status, 'mensaje'=>$mensaje);
                }
            }
        }
        else
        {
            $status           = "OK";
            $mensaje          = "OK";
            $arrayRespuesta[] = array('status'=>$status, 'mensaje'=>$mensaje);
        }
        
        return $arrayRespuesta;
    }
    
    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteA2024($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteA2048($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarCliente6524($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarCliente7224($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo AUD8000
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteAuD8000($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo MEA1
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteMea1($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo MEA3
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteMea3($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un radio IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve reactivar el servicio de un cliente
     * que se encuentra configurado en un radio IPTECOM
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteIPTECOM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un servidor RADIUS
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function reconectarClienteRADIUS($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }
    
    /**
     * Funcion que genera realizar la reactivacion de servicios OTROS
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 22-10-2015
     * 
     * @author  Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.1 08-08-2016  Se adiciona la reconección para el producto Office 365
     * 
     * @author  Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.2 01-12-2016 - Se crea producto NetlifeCloud en reemplazo del Office 365, se procede a cambiar el producto
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.3 21-06-2017 Se envia false como parametro a la función getDatosClientePorIdServicio.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 15-12-2018 Se modifica el envío de parámetros a la función obtenerValorServicioProductoCaracteristicaPorServicio,
     *                         obtenerInformacionClienteMcAffe y guardaServicioProductoCaracteristicaPorServicio
     *
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.5 02-04-2019 Se agrega log de errores en problemas de reactivación de suscripción McAfee
     * @since 1.4
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 05-08-2019 Se agrega la reactivación de servicios adicionales I.PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 23-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a función gestionarLicencias, ya que no está siendo usada 
     *                          y se especifica en el historial la tecnología
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 1.8 21-11-2019 - Se agrega el proceso para notificar la reconexión del servicio a konibit mediante GDA en caso de aplicar.
     *
     * @param  $array $arrayParametrosActivacion
     * 
    */
    public function reactivarServiciosOtros($arrayParametrosActivacion)
    {
        $intIdServicio                             = $arrayParametrosActivacion['idServicio'];
        $intIdAccion                               = $arrayParametrosActivacion['idAccion'];
        $strUsrCreacion                            = $arrayParametrosActivacion['usrCreacion'];
        $strClientIp                               = $arrayParametrosActivacion['clientIp'];
        $arrayParametros                           = array();
        $arrayRespuestaServicio                    = array();
        $entityAdmiProducto                        = null;
        $entityInfoPlanCab                         = null;
        $strPlan                                   = "";
        $booleanValidaProducto                     = false;
        $booleanValidaProductoProteccionTotal      = false;
        $booleanValidaProductoOffice               = false;
        $booleanValidaProtegido                    = false;
        $booleanValidaOfficeMig                    = false;
        $boolEsProdIProtegMultiPaid                = false;
        $boolFalse                                 = false;
        $strTieneSuscriberId                       = "NO";
        $strCodEmpresa                             = $arrayParametrosActivacion['strCodEmpresa'] 
                                                     ? $arrayParametrosActivacion['strCodEmpresa'] : "18";
        $strMsjHistorial                           = $arrayParametrosActivacion['strMsjHistorial'] 
                                                     ? $arrayParametrosActivacion['strMsjHistorial'] : "Otros: Se reactivo el servicio";
        $strNombreServicio                         = "";
        
        $em                      = $this->emComercial;
        $emSeguridad             = $this->emSeguridad;
        
        $em->getConnection()->beginTransaction();
        
        try
        {
            $servicio                      = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $accion                        = $emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
            if($servicio->getProductoId())
            {
                $entityAdmiProducto = $em->getRepository('schemaBundle:AdmiProducto')->find($servicio->getProductoId());
                $strNombreServicio  = $entityAdmiProducto->getDescripcionProducto();
            }
            else
            {
                $entityInfoPlanCab = $em->getRepository('schemaBundle:InfoPlanCab')->find($servicio->getPlanId());
            }
            $strEstadoServicioInicial = $servicio->getEstado();
            //servicio
            $servicio->setEstado("Activo");
            $em->persist($servicio);
            $em->flush();

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion($strMsjHistorial);
            $servicioHistorial->setEstado("Activo");
            $servicioHistorial->setUsrCreacion($strUsrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($strClientIp);
            $servicioHistorial->setAccion($accion->getNombreAccion());
            $em->persist($servicioHistorial);
            $em->flush();

            //Reconectar servicios Nuevos McAfee, servicios McAfee antiguos MIGRADOS y servicios NetlifeCloud
            if($entityAdmiProducto || $entityInfoPlanCab)
            {
                //Se verifica si es un producto Nuevo McAfee u NetlifeCloud
                if ($entityAdmiProducto)
                {
                    $boolEsProdIProtegMultiPaid           = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID');
                    $booleanValidaProducto                = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO MULTI');
                    $booleanValidaProductoProteccionTotal = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTECCION TOTAL');
                    $booleanValidaProductoOffice          = strpos($entityAdmiProducto->getDescripcionProducto(), 'NetlifeCloud');
                    if($boolEsProdIProtegMultiPaid !== $boolFalse)
                    {
                        $objSpcSuscriberId  = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($servicio, "SUSCRIBER_ID", $entityAdmiProducto);
                        if(is_object($objSpcSuscriberId))
                        {
                            $strTieneSuscriberId = "SI";
                        }
                    }
                }
                //Se verifica si es un producto Antiguo McAfee MIGRADO u NetlifeCloud
                else
                {
                    $booleanValidaProtegido               = strpos($entityInfoPlanCab->getCodigoPlan(), 'MCAFEE');
                    $booleanValidaOfficeMig               = strpos($entityInfoPlanCab->getCodigoPlan(), 'NetlifeCloud');
                }
                
                if($strTieneSuscriberId === "SI")
                {
                    $arrayParamsLicencias           = array("strProceso"                => "REACTIVACION_ANTIVIRUS",
                                                            "strEscenario"              => "REACTIVACION_PROD_ADICIONAL",
                                                            "objServicio"               => $servicio,
                                                            "objPunto"                  => $servicio->getPuntoId(),
                                                            "strCodEmpresa"             => $strCodEmpresa,
                                                            "objProductoIPMP"           => null,
                                                            "strUsrCreacion"            => $strUsrCreacion,
                                                            "strIpCreacion"             => $strClientIp,
                                                            "strEstadoServicioInicial"  => $strEstadoServicioInicial
                                                            );
                    $arrayRespuestaGestionLicencias = $this->serviceLicenciasKaspersky->gestionarLicencias($arrayParamsLicencias);
                    $strStatusGestionLicencias      = $arrayRespuestaGestionLicencias["status"];
                    $strMensajeGestionLicencias     = $arrayRespuestaGestionLicencias["mensaje"];
                    $arrayRespuestaWs               = $arrayRespuestaGestionLicencias["arrayRespuestaWs"];

                    if($strStatusGestionLicencias === "ERROR")
                    {
                        $arrayRespuestaServicio['status'] = "ERROR";
                        throw new \Exception($strMensajeGestionLicencias);
                    }
                    else if(isset($arrayRespuestaWs) && !empty($arrayRespuestaWs) && $arrayRespuestaWs["status"] !== "OK")
                    {
                        $arrayRespuestaServicio['status']  = 'ERROR';
                        $arrayRespuestaServicio['mensaje'] = $arrayRespuestaWs["mensaje"];
                        return $arrayRespuestaServicio;
                    }
                }
                //Se valida que sea un producto McAffe
                else if($booleanValidaProducto !== false || $booleanValidaProductoProteccionTotal !== false || $booleanValidaProtegido !== false )
                {
                    if ($booleanValidaProtegido !== false && $entityInfoPlanCab)
                    {
                        $strPlan = $entityInfoPlanCab->getNombrePlan();
                        $strNombreServicio = $strPlan;
                    }
                    
                    $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),"esProducto");
                    
                    if (!$datosCliente['ID_PERSONA'])
                    {
                        $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),false);
                    }
                    
                    $arrayParametros = $this->licenciasMcAfee
                                            ->obtenerInformacionClienteMcAffe(array("intIdPersona"      => $datosCliente['ID_PERSONA'],
                                                                                    "intIdServicio"     => $servicio->getId(),
                                                                                    "strNombrePlan"     => $strPlan,
                                                                                    "strEsActivacion"   => "SI"));
                    $arrayParametros["strTipoTransaccion"] = 'Reactivacion';
                    if($arrayParametros["strError"] == 'true')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception("problemas al obtener informacion del cliente");
                    }

                    if($booleanValidaProducto !== false)
                    {
                        $arrayParametros["strMetodo"]   = 'CrearSuscripcionMultidispositivo';
                        $arrayParametros["intLIC_QTY"]  = $arrayParametros["strCantidadDispositivos"];
                        $arrayParametros["intQTY"]      = 1;
                        
                    }
                    else if($booleanValidaProductoProteccionTotal !== false || $booleanValidaProtegido !== false)
                    {
                        $arrayParametros["strMetodo"]   = 'CrearNuevaSuscripcion';
                        $arrayParametros["intLIC_QTY"]  = 0;
                        $arrayParametros["intQTY"]      = 1;
                    }
                    $arrayParamsGuardarSpc = array( "objServicio"       => $servicio,
                                                    "strUsrCreacion"    => $strUsrCreacion);
                    $arrayParamsGuardarSpc["strCaracteristica"] = "PARTNERREF";
                    $arrayParamsGuardarSpc["strValor"]          = $arrayParametros["strPartnerRef"];
                    $respuesta = $this->licenciasMcAfee->guardaServicioProductoCaracteristicaPorServicio($arrayParamsGuardarSpc);
                    if($respuesta["status"] == 'ERROR')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception($respuesta["mensaje"]);

                    }
                    
                    $respuesta = $this->licenciasMcAfee
                                      ->obtenerValorServicioProductoCaracteristicaPorServicio(array("strCaracteristica" => "REFERENCIA", 
                                                                                                    "objServicio"       => $servicio));
                    
                    if($respuesta["status"] == 'ERROR')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception($respuesta["mensaje"]);
                    }
                    $entityInfoServicioProdCaract = $respuesta["mensaje"];
                    
                    if ($entityInfoServicioProdCaract)
                    {
                        $entityInfoServicioProdCaract->setEstado('Eliminado');
                        $em->persist($entityInfoServicioProdCaract);
                    }

                    $respuestaServicio             = $this->licenciasMcAfee->operacionesSuscripcionCliente($arrayParametros);
                    
                    if($respuestaServicio["procesoExitoso"] == "false")
                    {
                        if($em->getConnection()->isTransactionActive())
                        {
                            $em->getConnection()->rollback();
                        }
                        $em->getConnection()->beginTransaction();
                        //historial del servicio
                        $objServicioHistorial = new InfoServicioHistorial();
                        $objServicioHistorial->setServicioId($servicio);
                        $objServicioHistorial->setObservacion("No se ha podido reactivar el servicio ".$strNombreServicio.
                                                              " con tecnología MCAFEE<br>".$respuestaServicio["mensajeRespuesta"]);
                        $objServicioHistorial->setEstado($servicio->getEstado());
                        $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                        $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                        $objServicioHistorial->setIpCreacion($strClientIp);
                        $em->persist($objServicioHistorial);
                        $em->flush();
                        $em->getConnection()->commit();
                        $arrayRespuestaServicio['status']  = 'ERROR';
                        $arrayRespuestaServicio['mensaje'] = $respuestaServicio["mensajeRespuesta"];
                        return $arrayRespuestaServicio;
                        
                    }
                    $arrayParamsGuardarSpc["strCaracteristica"] = "REFERENCIA";
                    $arrayParamsGuardarSpc["strValor"]          = $respuestaServicio["referencia"];
                    $respuesta = $this->licenciasMcAfee->guardaServicioProductoCaracteristicaPorServicio($arrayParamsGuardarSpc);
                    if($respuesta["status"] == 'ERROR')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception($respuesta["mensaje"]);
                    }
                } //Se valida que sea un producto NetlifeCloud
                else if($booleanValidaProductoOffice !== false || $booleanValidaOfficeMig !== false)
                {
                    $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),"esProducto");
                    
                    if (!$datosCliente['ID_PERSONA'])
                    {
                        $datosCliente = $em->getRepository("schemaBundle:InfoPersona")->getDatosClientePorIdServicio($servicio->getId(),false);
                    }
                    
                    $arrayObtenerInformacion                  = array();
                    $arrayObtenerInformacion["intIdPersona"]  = $datosCliente['ID_PERSONA'];
                    $arrayObtenerInformacion["intIdServicio"] = $servicio->getId();
                    $arrayObtenerInformacion["strUser"]       = $strUsrCreacion;
                    $arrayObtenerInformacion["strIpClient"]   = $strClientIp;
                    if ($booleanValidaOfficeMig !== false && $entityInfoPlanCab)
                    {
                        $strPlan = $entityInfoPlanCab->getNombrePlan();
                    }                    
                    
                    $arrayParametros = $this->licenciasOffice365->obtenerInformacionClienteOffice365($arrayObtenerInformacion);
                    
                    if($arrayParametros["strError"] == 'true')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception("problemas al obtener informacion del cliente");
                    }

                    //Se envia vacio $arrayParametros["strMetodo"] debido a que no existen métodos para reconectar servicio por parte del proveedor.
                    $arrayParametros["strMetodo"]         = "";
                    $arrayParametros["strUser"]           = $strUsrCreacion;
                    $arrayParametros["strIpClient"]       = $strClientIp;

                    if($booleanValidaProductoOffice !== false)
                    {
                        $arrayParametros["intLIC_QTY"]    = 0;
                        $arrayParametros["intQTY"]        = 1;
                    }

                    $respuestaServicio = $this->licenciasOffice365->operacionesSuscripcionCliente($arrayParametros);
                    
                    if($respuestaServicio["procesoExitoso"] == "false")
                    {
                        if($em->getConnection()->isTransactionActive())
                        {
                            $em->getConnection()->rollback();
                        }
                        $arrayRespuestaServicio['status']  = 'ERROR';
                        $arrayRespuestaServicio['mensaje'] = $respuestaServicio["mensajeRespuesta"];
                        return $arrayRespuestaServicio;
                    }
                }
            }//if($entityAdmiProducto || $entityInfoPlanCab)
            
            $arrayRespuestaServicio['status']  = 'OK';
            $arrayRespuestaServicio['mensaje'] = '';
            $em->flush();
            $em->getConnection()->commit();
        }
        catch (\Exception $ex)
        {
            if($em->getConnection()->isTransactionActive())
            {
                $em->getConnection()->rollback();
            }
            error_log("error: " . $ex->getMessage());
            if ($arrayRespuestaServicio['status']  == 'ERROR')
            {
                $arrayRespuestaServicio['mensaje'] = $ex->getMessage();    
            }
            else
            {
                $arrayRespuestaServicio['status']  = 'ERROR';
                $arrayRespuestaServicio['mensaje'] = '';  
            }
            
        }


        //Proceso para notificar la reconexión del servicio a konibit mediante GDA en caso de aplicar.
        try
        {
            $objInfoEmpresaGrupo = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
            if (is_object($objInfoEmpresaGrupo) && $objInfoEmpresaGrupo->getPrefijo() === 'MD'
                    && $arrayRespuestaServicio['status'] === 'OK' && is_object($servicio))
            {
                $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                        ->notificarKonibit(array ('intIdServicio'  => $servicio->getId(),
                                                  'strTipoProceso' => 'RECONECTAR',
                                                  'strTipoTrx'     => 'INDIVIDUAL',
                                                  'strUsuario'     => $strUsrCreacion,
                                                  'strIp'          => $strClientIp,
                                                  'objUtilService' => $this->serviceUtil));
            }
        }
        catch (\Exception $objException)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoReconectarServicioService->reactivarServiciosOtros->adicional',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objException->getMessage(),
                                             $strUsrCreacion,
                                             $strClientIp);
        }


        return $arrayRespuestaServicio;
    }
    
    /**
     * reactivarServicioTN          
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 01-02-2017 Se agrega llamado a funcion que realiza accion sobr backup cuando su principal es afectado
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 23-11-2016 Se valida para que soporte escenario de pseudope          
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 22-07-2016 Se modifica para que la reconeccion reciba vlan mac de manera correcta y no vaya a ejecutar en PE                              
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 05-07-2016 Se cambia envio de vlan->mac al WS y que sea solo del cliente a configurar
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 14-04-2016 Integracion con service para invocar WS de NW para ejecucion de scripts en equipos de TN al momento de reconectar o
     *                         reconfigurar         
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.4 04-07-2017 Se agrega funcion de calculo de ancho de banda de concentrador
     * @since 1.3
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.5 11-07-2017 Se agrega sumatoria de nuevo BW solo para proceso de reconeccion omitiendo la reconfiguracion
     * @since 1.4
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @since 24-05-2018
     * @version 1.5 - Se envia descripcion de acuerdo a la Ultima milla del servicio para identificacion de NW
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.6  20-03-2019 - Se envia la clase_servicio: INTERNET-HSRP y DATOS-HSRP para las ordenes de servicio que tienen definido
     *                            el esquema PE-HSRP
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.7  29-03-2019 - Se corrige el envio de parametros al webservive de NetWorkingm url: configPE
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.8 19-06-2019 - Se agregan parámetros de razon_social y route_target export e import en el método configPE, con el objetivo de
     *                           enviar a configurar una lineas adicionales que permitan al cliente el monitoreo sus enlaces de datos
     * 
     * @author David Leon <mdleon@telconet.ec>
     * @version 1.9 05-08-2019 - Se agregan validaciones para el producto L3MPLS SDWAN siga el mismo flujo que el L3MPLS.
     *
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 2.0 - Se realiza ajuste a lógica para que valide si el servicio es 'CANAL TELEFONIA' y asigne valores al 
     *                arreglo de parametros.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.1 12-05-2020 - Se valida la llave 'clase_servicio' de la opción configPE del ws de networking
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.2 01-06-2020 - Se agrega el id del servicio a la url 'configSW' del ws de networking para la validación del BW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.3 14-07-2020 - Se agrega la acción de reconectar en la actualización de la capacidad del concentrador
     * 
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 2.4 24-09-2020 - Se agrega validación de relación con servicio FastCloud
     *
     * @param Array $arrayPeticiones [  'idServicio' , 'idProducto' , , 'idAccion' , 'vlan' , 'usrCreacion' , 'ipCreacion , capacidadUno ,
     *                                  capacidadDos ] 
     * @return Array $arrayFinal[ status , mensaje ]
     */
    public function reactivarServicioTN($arrayPeticiones)
    {
        $strBanderaLineasBravco = "N";
        $strRouteTargetExport   = "";
        $strRouteTargetImport   = "";
        $strRazonSocial         = "";

        //Se obtiene la informacion a enviar
        $objAccion                = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($arrayPeticiones['idAccion']);
        $objServicio              = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        $objProducto              = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($arrayPeticiones['idProducto']);
        
        $boolEsPesudoPe           = $this->emComercial->getRepository('schemaBundle:InfoServicio')->esServicioPseudoPe($objServicio);
        
        $objServicioTecnico       = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                      ->findOneByServicioId($objServicio->getId());
        $strBanderaServProdCaract = "N";
        if(!$boolEsPesudoPe)
        {
            $objInterfaceElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($objServicioTecnico->getInterfaceElementoId());                
            $objElemento            = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());         

            //Capacidades totales de los servicios activos ligados a un puerto
            $arrayCapacidades = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                        ->getResultadoCapacidadesPorInterface($objInterfaceElemento->getId());

            $bwUp   = $arrayCapacidades['totalCapacidad1'];
            $bwDown = $arrayCapacidades['totalCapacidad2'];

            //Si es reconfiguracion envia todas las vlans->macs existentes en el puerto
            if($objAccion->getNombreAccion() != "reconectarCliente")
            {
                //Mac y Vlans por servicio activos en el puerto
                $arrayRespuesta  = $this->servicioGeneral->getArrayMacVlansPorInterface($objInterfaceElemento->getId());

                //Se verifica que no existan servicios con informacion de mac o vlan faltantes
                if($arrayRespuesta['serviciosSinInformacion'])
                {
                    $mensajeError = 'No se puede realizar reconfiguración de puerto porque existen servicios con información incompleta.<br/>'
                                  . 'Verificar información de los siguientes servicios : '
                                  . '';

                    foreach($arrayRespuesta['serviciosSinInformacion'] as $servicio)
                    {
                        $mensajeError .= '<br/><b>'.$servicio['loginAux'].'</b> -> '.$servicio['motivo'];
                    }

                    $arrayFinal[] = array('status'  => "ERROR", 
                                          'mensaje' => $mensajeError);

                    return $arrayFinal;
                }
                else
                {
                    $arrayMacVlan = $arrayRespuesta['macVlan'];
                }
            }
            else //Para reactivar envia solo la mac->vlan del servicio a reconectar
            {
                $arrayMacServicio[] = $arrayPeticiones['mac'];
                $arrayMacVlan       = array($arrayPeticiones['vlan']=>$arrayMacServicio);  
                
                //Capacidad a ser configurada para reactivar un Servicio
                $bwUp   = $bwUp   + $arrayPeticiones['capacidadUno'];
                $bwDown = $bwDown + $arrayPeticiones['capacidadDos'];
            }     

            $arrayParametros = array();
            
            $objAdmiTipoMedio   = $this->emInfraestructura->getRepository("schemaBundle:AdmiTipoMedio")
                                                          ->find($objServicioTecnico->getUltimaMillaId());
                
            if(is_object($objAdmiTipoMedio))
            {
                $strUltimaMilla = $objAdmiTipoMedio->getNombreTipoMedio();
            }

            $strDescripcion = '';

            if($strUltimaMilla == 'Fibra Optica')
            {
                $strDescripcion = '_fib';
            }
            if($strUltimaMilla == 'Radio')
            {
                $strDescripcion = '_rad';
            }
            if($strUltimaMilla == 'UTP')
            {
                $strDescripcion = '_utp';
            }

            if ($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA') 
            {
                $arrayParametros['servicio'] = 'NETVOICE-L3MPLS';
            }
            elseif($objProducto->getDescripcionProducto() == 'DIRECTLINK MPLS')
            {
                $arrayParametros['servicio'] = 'DIRECTLINK-L3MPLS';      
            }
            else 
            {
                $arrayParametros['servicio'] = $objProducto->getNombreTecnico();
            }

            //accion a ejecutar
            $arrayParametros['url']          = 'configSW';
            $arrayParametros['accion']       = 'reconectar';                
            $arrayParametros['id_servicio']  = $objServicio->getId();
            $arrayParametros['nombreMetodo'] = 'InfoReconectarServicioService.reactivarServicioTN';
            $arrayParametros['sw']           = $objElemento->getNombreElemento();
            $arrayParametros['macVlan']      = $arrayMacVlan;
            $arrayParametros['user_name']    = $arrayPeticiones['usrCreacion'];
            $arrayParametros['user_ip']      = $arrayPeticiones['ipCreacion'];        
            $arrayParametros['bw_up']        = $bwUp;
            $arrayParametros['bw_down']      = $bwDown;
            $arrayParametros['anillo']       = '';
            $arrayParametros['login_aux']    = $objServicio->getLoginAux();
            $arrayParametros['descripcion']  = 'cce_'.$objServicio->getLoginAux().$strDescripcion;
            $arrayParametros['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();                       

            //Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayParametros);

            $status  = $arrayRespuesta['status'];
            $mensaje = $arrayRespuesta['mensaje'];
        }
        else
        {
            $status  = "OK";
            $mensaje = "OK"; 
        }

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try{
            if($status=="OK")
            {
                //validaciones
                $arrayParametros = array();
                if ($objServicio->getdescripcionpresentafactura() == 'CANAL TELEFONIA')
                {
                    $arrayParametros['clase_servicio'] = 'NETVOICE-L3MPLS';
                }
                elseif($objProducto->getDescripcionProducto() == 'DIRECTLINK MPLS')
                {
                    $arrayParametros['clase_servicio']    = 'DIRECTLINK-L3MPLS';      
                }
                else
                {
                    $arrayParametros['clase_servicio'] = $objProducto->getNombreTecnico();
                }

                //*************Validar si la orden de servicio tiene seteado el esquema de Pe-Hsrp*************//
                $arrayParametros['banderaBravco'] = 'NO';
                $strBanderaServProdCaract         = "N";
                $arrayParametrosProdCaract["strCaracteristica"] = "PE-HSRP";
                $arrayParametrosProdCaract["objProducto"]       = $objProducto;
                $arrayParametrosProdCaract["objServicio"]       = $objServicio;

                $strBanderaServProdCaract = $this->serviceCliente->consultaServicioProdCaract($arrayParametrosProdCaract);

                if($strBanderaServProdCaract === "S")
                {
                    $arrayParametros['banderaBravco']  = 'SI';
                    $arrayParametros['clase_servicio'] = $objProducto->getClasificacion().'-HSRP';
                }
                //*************Validar si la orden de servicio tiene seteado el esquema de Pe-Hsrp*************//

                if(($objProducto->getNombreTecnico()=="L3MPLS" || $objProducto->getNombreTecnico()=="L3MPLS SDWAN")
                    && $objAccion->getNombreAccion() != "reconectarCliente")
                {
                    //Se setea el tipo de enlace PRINCIPAL SI ES L3MPLS SDWAN
                    if($objProducto->getNombreTecnico()=="L3MPLS SDWAN")
                    {
                        $arrayPeticiones['tipoEnlace'] = "PRINCIPAL";
                    }

                    //Consultar Razon Social
                    $objInfoPersona = $objServicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();

                    if(is_object($objInfoPersona))
                    {
                        $strRazonSocial = $objInfoPersona->getRazonSocial();
                    }

                    if(!empty($strRazonSocial))
                    {
                        $arrayRazonesSociales = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
                                                                  ->getOne('PROYECTO MONITOREO CLIENTES GRUPO BRAVCO',
                                                                           'INFRAESTRUCTURA',
                                                                           'ACTIVAR SERVICIO',
                                                                           'RAZON SOCIAL GRUPO BRAVCO',
                                                                           $strRazonSocial,
                                                                           '',
                                                                           '',
                                                                           '',
                                                                           '',
                                                                           '');
                    }

                    if(isset($arrayRazonesSociales["valor1"]) && !empty($arrayRazonesSociales["valor1"]))
                    {
                        $strBanderaLineasBravco = "S";
                        $strRouteTargetExport   = $arrayRazonesSociales["valor2"];
                        $strRouteTargetImport   = $arrayRazonesSociales["valor3"];
                        $strRazonSocial         = $arrayRazonesSociales["valor4"];
                    }
                    
                    //accion a ejecutar
                    $arrayParametros['url']                   = 'configPE';
                    $arrayParametros['accion']                = 'Activar';        
                    $arrayParametros['sw']                    = $arrayPeticiones['elementoNombre'];

                    $arrayParametros['vrf']                   = $arrayPeticiones['vrf'];
                    $arrayParametros['pe']                    = $arrayPeticiones['elementoPadre'];
                    $arrayParametros['anillo']                = $arrayPeticiones['anillo'];
                    $arrayParametros['vlan']                  = $arrayPeticiones['vlan'];
                    $arrayParametros['subred']                = $arrayPeticiones['subredServicio'];
                    $arrayParametros['mascara']               = $arrayPeticiones['mascaraSubredServicio'];
                    $arrayParametros['gateway']               = $arrayPeticiones['gwSubredServicio'];
                    $arrayParametros['rd_id']                 = $arrayPeticiones['rdId'];
                    $arrayParametros['descripcion_interface'] = $arrayPeticiones['loginAux'];
                    $arrayParametros['ip_bgp']                = $arrayPeticiones['ipServicio'];
                    $arrayParametros['asprivado']             = $arrayPeticiones['asPrivado'];
                    $arrayParametros['nombre_sesion_bgp']     = $arrayPeticiones['loginAux'];
                    $arrayParametros['default_gw']            = $arrayPeticiones['defaultGateway'];
                    $arrayParametros['protocolo']             = $arrayPeticiones['protocolo'];
                    $arrayParametros['servicio']              = $objProducto->getNombreTecnico();
                    $arrayParametros['login_aux']             = $arrayPeticiones['loginAux'];
                    $arrayParametros['tipo_enlace']           = $arrayPeticiones['tipoEnlace'];
                    $arrayParametros['weight']                = null;

                    $arrayParametros['user_name']             = $arrayPeticiones['usrCreacion'];
                    $arrayParametros['user_ip']               = $arrayPeticiones['ipCreacion'];

                    //Se envian a configurar lineas de monitoreo de enlaces de datos
                    if($strBanderaLineasBravco === "S")
                    {
                        $arrayParametros['razon_social'] = $strRazonSocial;
                        $arrayParametros['rt_export']    = $strRouteTargetExport;
                        $arrayParametros['rt_import']    = $strRouteTargetImport;
                    }

                    //Ejecucion del metodo via WS para realizar la configuracion en el Pe
                    $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayParametros);

                    $status  = $arrayRespuesta['status'];
                    $mensaje = $arrayRespuesta['mensaje'];
                    
                    if($status!="OK")
                    {
                        $arrayFinal[] = array('status'  => "ERROR", 
                                            'mensaje' => "Error: ".$mensaje);
                    }
                }
                $strValidaEnlace = ($objServicioTecnico->getTipoEnlace() !== null) ?
                                       substr($objServicioTecnico->getTipoEnlace(), 0, 9):$objServicioTecnico->getTipoEnlace();

                if(is_object($objProducto) && $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                {
                    $strValidaEnlace = 'PRINCIPAL';
                }

                if($objAccion->getNombreAccion()        == "reconectarCliente" &&
                   ($objProducto->getNombreTecnico()     == "L3MPLS" || $objProducto->getNombreTecnico() == "L3MPLS SDWAN") &&
                   $strValidaEnlace == 'PRINCIPAL')
                {
                    $arrayParametrosBw = array( 
                                                "objServicio"       => $objServicio,
                                                "nombreAccionBw"    => 'reconectar',
                                                "usrCreacion"       => $arrayPeticiones['usrCreacion'],
                                                "ipCreacion"        => $arrayPeticiones['ipCreacion'],
                                                "capacidadUnoNueva" => intval($arrayPeticiones['capacidadUno']),
                                                "capacidadDosNueva" => intval($arrayPeticiones['capacidadDos']),
                                                "operacion"         => "+",
                                                "accion"            => "Se actualiza Capacidades por Reactivacion de "
                                                                       . "servicio : <b>".$objServicio->getLoginAux()."<b>"
                                               );

                    //Se actualiza las capacidades del Concentrador
                    $this->servicioGeneral->actualizarCapacidadesEnConcentrador($arrayParametrosBw);
                }
                                
                //Cuando se reconfigura solo se genera historial por accion ejecutada
                if($objAccion->getNombreAccion() != "reconectarCliente")
                {
                    $strMensajeHistorial = "Se Reconfiguró el puerto";
                    $strEstado           = $objServicio->getEstado();
                }
                else //Cuando se reactiva el servicio se actualiza estado del servicio para luego escribir historial
                {
                    $strMensajeHistorial = "Se Reactivó el Servicio";
                    $strEstado           = "Activo";
                    
                    $objServicio->setEstado($strEstado);
                    $this->emComercial->persist($objServicio);
                    $this->emComercial->flush();
                }
                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($objServicio);
                $servicioHistorial->setObservacion($strMensajeHistorial);
                $servicioHistorial->setEstado($strEstado);
                $servicioHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
                $servicioHistorial->setAccion($objAccion->getNombreAccion());            
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();

                //Se el servicio principal es reconectado el servicio backup también deberá ser cambiado
                if($objAccion->getNombreAccion() == "reconectarCliente")
                {
                    if(is_object($objProducto) && $objProducto->getNombreTecnico() == "L3MPLS SDWAN")
                    {
                        $strValidaEnlace = 'PRINCIPAL';
                    }

                    $strValidaEnlace = ($objServicioTecnico->getTipoEnlace() !== null) ?
                                       substr($objServicioTecnico->getTipoEnlace(), 0, 9):$objServicioTecnico->getTipoEnlace();
                    if($strValidaEnlace == 'PRINCIPAL')
                    {
                        $arrayParametros                    = array();
                        $arrayParametros['objServicio']     = $objServicio;
                        $arrayParametros['intCapacidadUno'] = $bwUp;
                        $arrayParametros['intCapacidadDos'] = $bwDown;
                        $arrayParametros['strUsrCreacion']  = $arrayPeticiones['usrCreacion'];
                        $arrayParametros['strIpCreacion']   = $arrayPeticiones['ipCreacion'];
                        $arrayParametros['objMotivo']       = null;
                        $arrayParametros['objAccion']       = $objAccion;
                        $arrayParametros['strAccion']       = 'reconectar';
                        $arrayParametros['strEstado']       = 'Activo';
                        $arrayParametros['strObservacion']  = "Se Reconectó el Servicio <b>Backup</b> por Reconexión en Servicio <b>Principal : ".
                                                              $objServicio->getLoginAux().'</b>';

                        $arrayRespuestaBck = $this->servicioGeneral->cortarReconectarServicioBackup($arrayParametros);

                        if($arrayRespuestaBck['strStatus'] != 'OK')
                        {
                            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=> $arrayRespuestaBck['strMensaje']);
                            return $arrayFinal;
                        }
                    }
                }
                                
                $arrayFinal[] = array('status'=>"OK", 'mensaje'=>"OK");
                
                //Consultamos si el servicio tiene relacionado servicios como FastCloud
                $arrayServiciosRelacion = $this->getServiciosRelacion($arrayPeticiones['idServicio']);
                foreach ($arrayServiciosRelacion as $arrayServiciosRel)
                {
                    $arrayPeticionesServiciosRel = array();
                    $arrayPeticionesServiciosRel['idServicio']           = $arrayServiciosRel;
                    $arrayPeticionesServiciosRel['strCodEmpresa']        = '10';
                    $arrayPeticionesServiciosRel['idAccion']             = $arrayPeticiones['idAccion'];
                    $arrayPeticionesServiciosRel['usrCreacion']          = $arrayPeticiones['usrCreacion'];
                    $arrayPeticionesServiciosRel['clientIp']             = $arrayPeticiones['ipCreacion'];
                                    
                    $arrayRespuestaSer  = $this->reactivarServiciosOtros($arrayPeticionesServiciosRel);
                    $strStatus          = $arrayRespuestaSer['status'];
                }
            }
            else
            {
                $arrayFinal[] = array('status'  => "ERROR", 
                                      'mensaje' => "Error: ".$mensaje);
            }
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
                        
            $status="ERROR";
            $mensaje = "ERROR EN LA LOGICA DE NEGOCIO, ".$e->getMessage();
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            return $arrayFinal;
        }
        //*---------------------------------------------------------------------*/
        //*DECLARACION DE COMMITS*/
        if ($this->emInfraestructura->getConnection()->isTransactionActive()){
            $this->emInfraestructura->getConnection()->commit();
        }

        if ($this->emComercial->getConnection()->isTransactionActive()){
            $this->emComercial->getConnection()->commit();
        }                
        
        $this->emInfraestructura->getConnection()->close();
        $this->emComercial->getConnection()->close();
        
        //*----------------------------------------------------------------------*/
        return $arrayFinal;        
    }           
    
    /**
     * 
     * Proceso de reconexión del producto Netlife Zone para clientes cuyo
     * servicio de internet ha sido colocado como In-Corte
     * 
     * @param type $servicio Objeto Servicio
     * 
     * @author  Veronica Carrasco Idrovo <vcarrasco@telconet.ec>
     * @version 1.0 30-06-2016
     * 
     * @author  Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-10-2019 Se modifica proceso para utilizar nuevo esquema Netlifezone
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 28-01-2021 - Se agrega quita la validacion del estado por Inactivacion del producto Netlife Zone
     */
    public function reconectarServiciosNetlifeWifi($arrayParametros)
    {
        $strRespuesta   = "";
        $strEstado      = "Activo";
        $strObservacion = "Netlife Zone: Servicio reconectado.";
        $strMetodoWs    = "active_user";
        $objServicio    = $arrayParametros['objServicio'];
        $strCodEmpresa  = $arrayParametros['intIdEmpresa'];
        $intIdAccion    = $arrayParametros['intIdAccion'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'] ;
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
                
        $objProductoNetwifi     = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array("nombreTecnico" =>  "NETWIFI",
                                                                      "empresaCod"    =>  $strCodEmpresa));
        
        $arrayServiciosNetwifi  = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->findBy(array("puntoId"       =>   $objServicio->getPuntoId(),
                                                                   "productoId"    =>   $objProductoNetwifi,
                                                                   "estado"        =>   "Activo"));
        
        //Cancelamos los servicios NETWIFI
        foreach ($arrayServiciosNetwifi as $objServicioNetwifi)
        {
            $arrayParametrosOperaciones = array();
            $arrayParametrosOperaciones['intIdEmpresa']   = $strCodEmpresa;
            $arrayParametrosOperaciones['intIdServicio']  = $objServicioNetwifi->getId();
            $arrayParametrosOperaciones['intIdAccion']    = $intIdAccion;
            $arrayParametrosOperaciones['strUsuario']     = $strUsrCreacion;
            $arrayParametrosOperaciones['strIpCliente']   = $strIpCreacion;
            $arrayParametrosOperaciones['strEstado']      = $strEstado;
            $arrayParametrosOperaciones['strObservacion'] = $strObservacion;
            $arrayParametrosOperaciones['strMetodoWs']    = $strMetodoWs;
            $strRespuestaNetlifeZone                      = $this->wifiNetlife->procesarOperacionesNetlifeWifi($arrayParametrosOperaciones);
            if($strRespuestaNetlifeZone !== "OK")
            {
                $strRespuesta = $strRespuestaNetlifeZone;
            }
        }
        return $strRespuesta;
    }
    
    /**
     * Función que permite obtener un arreglo con los servicios relacionados al servicio principal como DIRECTLINK MPLS.
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.0 24-09-2020 - Version Inicial.
     *
     * @param $intIdServicio -> Contiene un int que representa el Id del servicio que tiene servicios relacionados.
     * @return null|$intIdCouInstSim
     */

    public function getServiciosRelacion($intIdServicio)
    {
        $arrayServiciosRelacionados = array();

        /*Obtenemos el array del parámetro.*/
        $objParamsDet = $this->emGeneral->getRepository('schemaBundle:AdmiParametroDet')
            ->get('CARACTERISTICAS_SERVICIO_CONFIRMACION',
                'TECNICO',
                'SERVICIO_CONFIRMACION',
                '',
                '',
                '',
                '',
                '',
                '',
                10);

        /*Validamos que el arreglo no este vacío.*/
        if (is_array($objParamsDet) && !empty($objParamsDet))
        {
            $objCaracteristicasServiciosRelacionados = json_decode($objParamsDet[0]['valor1'], true);
            $arrayProductosRelacionados = $this->servicioGeneral->getArraybyKey('PRODUCTO_ID', $objCaracteristicasServiciosRelacionados);
        }
        
        /* Obtengo un objeto con el servicio tradicional. */
        $objServicioTradicional = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                    ->find($intIdServicio);

        /* Se valida que no esten vacios. */
        if (is_object($objServicioTradicional) && isset($arrayProductosRelacionados) && !is_null($arrayProductosRelacionados))
        {
            /* Se trae un arreglo de servicios que cumplan con las condiciones de puntoId y productoId. */
            $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                ->findBy(array(
                    'puntoId' => $objServicioTradicional->getPuntoId(),
                    'productoId' => $arrayProductosRelacionados
                ));
            
            /* Se trae un objeto de la caracteristica. */
            $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'RELACION_FAST_CLOUD',
                    'estado' => 'Activo'
                ));
            
            /* Se valida que los servicios del punto sean 1 o mas. */
            if (count($arrayServiciosPunto)>=1)
            {
                /* Se define un arreglo para filtrar por estado los servicios simultaneos. */
                $arrayEstados = array('Rechazado', 'Rechazada', 'Anulado', 'Anulada');

                /* Se recorre el arreglo de servicios del punto */
                foreach ($arrayServiciosPunto as $key=>$objServicio)
                {
                    $objProdCaract = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                          ->findOneBy(array(
                                                                            "productoId"       => $objServicioTradicional->getProductoId(),
                                                                            "caracteristicaId" => $objAdmiCaract->getId(),
                                                                            "estado"           => "Activo"
                                                                            ));
                    if(is_object($objProdCaract))
                    {
                        $objInfoServProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                                                    ->findOneBy(array(
                                                                                    'productoCaracterisiticaId' => $objProdCaract->
                                                                                                                    getId(),
                                                                                    'servicioId' => $objServicioTradicional->getId(),
                                                                                    'valor'      => $objServicio->getId()
                                                                    ));
                    }
                    
                    if(is_object($objInfoServProdCaract) && !empty($objInfoServProdCaract))
                    {
                        /* Se define variable para validar si el valor de la caracteristica es igual al del servicio tradicional. */
                        $boolIdInstSim = intval($objServicio->getId()) == intval($objInfoServProdCaract->getValor());
                        
                        /* Se valida el tema de la caracteristica, ademas de si el estado del servicio no esta incluido en el arreglo de estados. */
                        if ($boolIdInstSim && !in_array($objServicio->getEstado(), $arrayEstados))
                        {
                            array_push($arrayServiciosRelacionados, $objServicio->getId());
                        }
                    }
                }
            }
        }

        return $arrayServiciosRelacionados;
    }

    /**
     * Funcion que sirve para llamar y ejecutar Jar de Reactivacion.
     *
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 1.0 26-07-2022
     *
     * @param $arrayParametros -> Contiene servicios a Reconectar, usuario e ip del ciente,
     *                            tipo de proceso y su fecha de procesado.
     * @return $arrayResultado
     */

    public function callJarReactivacion($arrayParametros)
    {
        $strServicios    = $arrayParametros['servicios'];
        $strUsrCreacion  = $arrayParametros['usrCreacion'];
        $strIpCliente    = $arrayParametros['ipCliente'];
        $strProceso      = $arrayParametros['proceso'];
        $strFechaProceso = $arrayParametros['fechaProceso'];
        $objFecha = date("Y-m-d");
        $strComando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom ".
            "/home/telcos/src/telconet/tecnicoBundle/batch/ttco_reactivacionMasiva.jar '" . $strServicios .
            "' '" . $strUsrCreacion . "' '" . $strIpCliente . "' >> ".
            "/home/telcos/src/telconet/tecnicoBundle/batch/reactivacionMasiva-$objFecha.txt &";
        
        $strSalida = shell_exec($strComando);
        $strMensaje = "Se realizo el registro de $strProceso y se reactivaron los servicios el ".$strFechaProceso;
        $arrayResultado['status']  = "OK";
        $arrayResultado['mensaje'] = $strMensaje;
        return $arrayResultado;
    }
    /**
     * Funcion que sirve para llamar y ejecutar Jar de Corte Masivo
     *
     * @author Milen Ortega <mortega1@telconet.ec>
     * @version 1.0 27-11-2022
     * @param $arrayParametros -> Contiene servicios a Reconectar, usuario e ip del ciente,
     *                            tipo de proceso y su fecha de procesado.
     * @return $arrayResultado
     */

    public function callJarCorteMasivo($arrayParametros)
    {
        $strIdsPuntosCR     = $arrayData['data']['idsPuntosCR'];
        $intMontoDeuda      = $arrayData['data']['montoDeuda'];
        $strNumFactAbiertas = $arrayData['data']['numFactAbiertas'];
        $intIdOficina       = $arrayData['data']['idOficina'];
        $intIdFormaPago     = $arrayData['data']['idFormaPago'];
        $strUsrCreacion     = $arrayData['data']['usrCreacion'];
        $strIpCliente       = $arrayData['data']['ipCliente'];
        $strProceso         = $arrayData['data']['proceso'];
        $strFechaProceso    = $arrayData['data']['fechaProceso'];
        $objFecha = date("Y-m-d");
        $strComando = "nohup java -jar -Djava.security.egd=file:/dev/./urandom /home/telcos/src/telconet/tecnicoBundle/batch/ttco_corteMasivo.jar '"
                        . $strIdsPuntosCR . "' " . " '" . $intMontoDeuda . "|" . $strNumFactAbiertas . "|" . $intIdOficina . "|" . $intIdFormaPago . "' "
                        . " '" . $strUsrCreacion . "' '" . $strIp . "' "
                        . " >> /home/telcos/src/telconet/tecnicoBundle/batch/corteMasivo-$objFecha.txt &";
        
        $strSalida = shell_exec($strComando);
        $strMensaje = "Se realizo el registro de $strProceso y se reactivaron los servicios el ".$strFechaProceso;
        $arrayResultado['status']  = "OK";
        $arrayResultado['mensaje'] = $strMensaje;
        return $arrayResultado;
    }
}
