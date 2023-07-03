<?php
namespace telconet\tecnicoBundle\Service;

use telconet\schemaBundle\Entity\InfoServicio;
use telconet\schemaBundle\Entity\InfoServicioHistorial;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;
use telconet\schemaBundle\Entity\InfoServicioCaracteristica;

class InfoCortarServicioService{
    private $emComercial;
    private $emInfraestructura;
    private $emSoporte;
    private $emComunicacion;
    private $emSeguridad;
    private $emGeneral;
    private $servicioGeneral;
    private $cancelarService;
    private $activarService;
    private $reconectarService;
    private $licenciasMcAfee;
    private $licenciasOffice365;
    private $cambiarPuertoService;
    private $container;
    private $host;
    private $pathTelcos;
    private $pathParameters; 
    private $networkingScripts;
    private $wifiNetlife;
    private $serviceUtil;
    private $rdaMiddleware;
    private $opcion                 = "CORTAR";
    private $ejecutaComando;
    private $strConfirmacionTNMiddleware;
    private $serviceLicenciasKaspersky;
	private $servicePortalNetCam;
    
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
                                    InfoActivarPuertoService $activarService              , InfoReconectarServicioService $reconectarService,
                                    InfoCambiarPuertoService $cambiarPuertoService        , LicenciasMcAfeeService $licenciasMcAfeeServicio,
                                    NetworkingScriptsService $networkingScript            , WifiService $wifiNetlife,
                                    Container $container                                  , RedAccesoMiddlewareService  $redAccesoMiddleware) 
    {
        $this->servicioGeneral      = $servicioGeneral;
        $this->cancelarService      = $cancelarService;
        $this->activarService       = $activarService;
        $this->reconectarService    = $reconectarService;
        $this->cambiarPuertoService = $cambiarPuertoService;
        $this->licenciasMcAfee      = $licenciasMcAfeeServicio;
        $this->licenciasOffice365   = $container->get('tecnico.LicenciasOffice365');
        $this->networkingScripts    = $networkingScript;
        $this->wifiNetlife          = $wifiNetlife;
        $this->serviceUtil          = $container->get('schema.Util');
        $this->rdaMiddleware        = $redAccesoMiddleware;
        $this->serviceLicenciasKaspersky = $container->get('tecnico.LicenciasKaspersky');
	    $this->servicePortalNetCam  = $container->get('tecnico.PortalNetlifeCam3dEYEService');
    }
    
    /**
     * Funcion que sirve para cortar el servicio para Fibra, Cobre y Radio
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 21-04-2015
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.2 16-03-2016     Se agrega filtro de estado de Plan en consultas de información para obtener Detalles de Plan
     * 
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @version 1.3 08-07-2016     Se agrega proceso para corte del servicio Netlife Zone
     * @since 1.0
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.4 27-12-2017     Se corrige codigo comentado que generaba inconsistencia con LDAP de servicios
     * @since 1.3
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.5 27-12-2017     Se agrega validacion por el producto "INTERNET SMALL BUSINESS", OLT debe de contener MIDDLEWARE para ejecutar el 
     *                             Corte del Cliente.
     *                             Se añade nuevo parametro a la llamda del ldap (Prefijo Empresa) para el producto "INTERNET SMALL BUSINESS".
     *                             Se añade bandera "SI", cuando se un producto "INTERNET SMALL BUSINESS".
     *                             Se modifica asunto de notificacion de Corte, Si el servicio tiene un producto asociado se recupera la descripcion,
     *                             caso contrario Si el servicio tiene un plan asociado se recupera la descripcion del plan. 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.6 15-05-2018 Se realiza ajuste para considerar las ips adicionales al cortar un servicio Small Business. 
     *                         El estado del punto no es modificado ya que es un servicio TN
     * @since 1.5
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 28-11-2018 Se agregan validaciones para gestionar los productos de la empresa TNP
     * @since 1.5
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.7 16-12-2018 Se agrega corte de producto McAfee incluido en el plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 11-02-2019 Se agrega corte de servicios TelcoHome con sus respectivas Ips
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 08-03-2019 Se agrega validación adicional para cortar servicios de ips adicionales sin considerar a los servicios de telcohome
     * 
     * @author Jesus BenchenCruz <jbanchen@telconet.ec>
     * @version 2.0 29-04-2019 Se modifico por limite de lineas de codigo sonar y sus standares de variables
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 05-08-2019 Se agrega validación para evitar que se corte de manera lógica los servicios I. PROTEGIDO MULTI PAID
     *                          con tecnología Kaspersky y realice el corte de manera correcta con la invocación de la función 
     *                          cortarServiciosAdicionalesPorPunto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.1 28-08-2019 Se elimina envío de variable strMsjHistorial a la función cortarServiciosAdicionalesPorPunto, ya que 
     *                          dicho valor se lo obtiene dentro de la función
     * 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.2 07-10-2019 Se agrega el parámetro acción en proceso de corte MD
     * @since 1.5
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.3 21-11-2019 - Se agrega el proceso para notificar el corte del servicio a konibit mediante GDA en caso de aplicar.
     *
     * @author Marlon Plúas <mpluas@telconet.ec>
     * @version 2.4  20-12-2019 - Se agrega el proceso para cortar el servicio NetCam en la plataforma 3dEYE.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.5 14-02-2020 Se agrega la programación para actualizar la característica con el último historial In-Corte válido 
     *                          que debe ser tomado en cuenta para la cancelación masiva
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.6 03-05-2020 Se elimina la función obtenerInfoMapeoProdPrefYProdsAsociados y en su lugar se usa obtenerParametrosProductosTnGpon,
     *                          debido a los cambios realizados por la reestructuración de servicios Small Business y TelcoHome
     * 
     * @author Javier Hidalgo <jihidalgo@telconet.ec>
     * @version 2.7 09-12-2021 Se agrega parámetro que indica si el motivo seleccionado Corta servicio de cliente Posible Abusador
     *                          Se agrega característica InAudit y evento al historial de servicios a tablas respectivas.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 2.8 08-11-2021 Se agrega la invocación del web service para confirmación de opción de Tn a Middleware
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 2.9 11-07-2022 Se agrega la validación de la caracteristica del servicio principal INTERNET VPNoGPON,
     *                         para obtener los servicios de las ip asociadas al servicio principal.
     * 
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 3.0 01-08-2022 - Se agrega la validación para cortar los servicios adicionales safecity del servicio principal INTERNET VPNoGPON.
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 3.1 28-02-2023 - Se agrega Bandera de Ecuanet para permitir el flujo de corte Md, actualice la información de corte,
     *                           inaudite el servicio en posible caso y envie prefijo de empresa al ldap.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 3.2 15-05-2023 - Se elimina de MD para evitar error de corte, cuando va por el flujo de ldap. 
     * Adicional se agrego     sentencia try-catch para validar posibles errores.
     * 
     * @author Alberto Arias <farias@telconet.ec>
     * @version 3.3 15-06-2023 - Se agrega empresa ECUANET en la validación para obtener el ID del servicio historial de corte del Internet
     */
    public function cortarServicio($arrayPeticiones)
    {
        //*OBTENCION DE PARAMETROS-----------------------------------------------*/
        $idEmpresa      = $arrayPeticiones['idEmpresa'];
        $prefijoEmpresa = $arrayPeticiones['prefijoEmpresa'];
        $idServicio     = $arrayPeticiones['idServicio'];
        $idProducto     = $arrayPeticiones['idProducto'];
        $capacidad1     = $arrayPeticiones['capacidad1'];
        $capacidad2     = $arrayPeticiones['capacidad2'];
        $usrCreacion    = $arrayPeticiones['usrCreacion'];
        $ipCreacion     = $arrayPeticiones['ipCreacion'];
        $motivo         = $arrayPeticiones['motivo'];
        $idAccion       = $arrayPeticiones['idAccion'];
        $strEsIsb       = $arrayPeticiones['strEsIsb'] ? $arrayPeticiones['strEsIsb'] : "NO";
        $strEsMotivoInaudit = $arrayPeticiones['boolEsMotivoInaudit']; //Indica si el motivo de corte es para Inauditar Cliente
        $ejecutaLdap    = "NO";
        $strMsjAdicional= "";
        $strPrefijoEmpresaOrigen = $arrayPeticiones['prefijoEmpresa'];
        //migracion_ttco_md
        $arrayEmpresaMigra = $this->emComercial->getRepository('schemaBundle:AdmiParametroDet')
            ->getEmpresaEquivalente($idServicio, $prefijoEmpresa);

        if($arrayEmpresaMigra)
        {
            if($arrayEmpresaMigra['prefijo'] == 'TTCO')
            {
                $idEmpresa = $arrayEmpresaMigra['id'];
                $prefijoEmpresa = $arrayEmpresaMigra['prefijo'];
            }
        }

        $motivoObj              = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($motivo);
        $accionObj              = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($idAccion);
        $servicio               = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($idServicio);               
        $servicioTecnico        = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneBy(array("servicioId" => $servicio->getId()));
        $interfaceElementoId    = $servicioTecnico->getInterfaceElementoId();
        $interfaceElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                          ->find($interfaceElementoId);
        $nombreInterfaceElemento = $interfaceElemento->getNombreInterfaceElemento();
        $elementoId             = $interfaceElemento->getElementoId();
        $elemento               = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($elementoId);
        $modeloElementoId       = $elemento->getModeloElementoId();
        $modeloElemento         = $this->emInfraestructura->getRepository('schemaBundle:AdmiModeloElemento')->find($modeloElementoId);
        $producto               = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($idProducto);
        $objDetalleElemento     = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                        ->findOneBy(array(  "elementoId"   => $servicioTecnico->getElementoId(),
                                                            "detalleNombre"=> 'MIDDLEWARE',
                                                            "estado"       => 'Activo'));
        $flagMiddleware         = false;
        $intIdUltHistoInCorte   = 0;
        $arrayDataConfirmacionTn= array();
        //*---------------------------------------------------------------------*/
        //*DECLARACION DE TRANSACCIONES------------------------------------------*/
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //Obtenemos los productos adicionales con la característica KONIBIT.
        $arrayServiciosProdKonibit = $this->emComercial->getRepository('schemaBundle:InfoPunto')->getServiciosProductoKonibit(
                    array ('arrayEstadosServicio' =>  array('Activo'),
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
                $respuestaArray = $this->cortarClienteTtco($modeloElemento, $interfaceElemento, $servicio, $producto, 
                                                           $capacidad1, $capacidad2, $usrCreacion, $ipCreacion);
                $status = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];
            }
            else if($prefijoEmpresa == "MD" || $prefijoEmpresa == "TNP" || $prefijoEmpresa == "EN")
            {        
                if(!$flagMiddleware && $strEsIsb === 'SI')
                {
                     $strStatus         = "ERROR";
                     $strMensaje        = "No se pudo Cortar al Cliente - OLT sin middleware ";
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
                                            'servicio'                  => $servicio,
                                            'servicioTecnico'           => $servicioTecnico,
                                            'modeloElemento'            => $modeloElemento,
                                            'interfaceElemento'         => $interfaceElemento,
                                            'producto'                  => $producto,
                                            'usrCreacion'               => $usrCreacion,
                                            'ipCreacion'                => $ipCreacion,
                                            'idEmpresa'                 => $idEmpresa,
                                            'flagMiddleware'            => $flagMiddleware,
                                            'strEsIsb'                  => $strEsIsb,
                                            'strPrefijoEmpresaOrigen'   => $strPrefijoEmpresaOrigen,
                                            'intIdAccion'               => $idAccion
                                        );
                
                $respuestaArray = $this->cortarServicioMd($arrayParametros);
                $status = $respuestaArray[0]['status'];
                $mensaje = $respuestaArray[0]['mensaje'];
                $strMsjAdicional = $respuestaArray[0]['msjAdicional'];
                $arrayDataConfirmacionTn = $respuestaArray[0]['arrayDataConfirmacionTn'];
            }                       
            if($status == "OK")
            {
                $mensaje = "Se Corto el Servicio";
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
                                                         ->findBy(array("planId" => $planServicio->getId(), 
                                                                        "estado" => $planServicio->getEstado()));
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
                    $objServiciosPunto1 = $this->emComercial->getRepository('schemaBundle:InfoServicio')->findBy(array("puntoId" => $puntoPref->getId()));

                    for($i = 0; $i < count($objServiciosPunto1); $i++)
                    {
                        $objServ1 = $objServiciosPunto1[$i];
                        //solo se buscaran el preferencial en servicios activos
                        if($objServ1->getEstado() == "Activo")
                        {
                            $objPlan = $objServ1->getPlanId();
                            if($objPlan)
                            {
                                $objPlanDet = $this->emComercial->getRepository('schemaBundle:InfoPlanDet')
                                                             ->findBy(array("planId" => $objPlan->getId(), "estado" => $objPlan->getEstado()));
                                for($j = 0; $j < count($objPlanDet); $j++)
                                {
                                    $objProdServicio = $objPlanDet[$j]->getProductoId();
                                    $objProductoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($objProdServicio);

                                    if($objProductoServicio->getEsPreferencia() == "SI")
                                    {
                                        $contProdPref++;
                                    }
                                }
                            }
                            else
                            {
                                $objProdServicio = $objServ1->getProductoId();
                                $objProductoServicio = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($objProdServicio);

                                if($objProductoServicio->getEsPreferencia() == "SI")
                                {
                                    $contProdPref++;
                                }
                            }
                        }//cierre if servicio activo
                    }
                }
                if(($flagProd == 1 && $contProdPref < 2) || $strEsIsb === 'SI')
                {
                    $arrayIdsProdsIps   = array();
                    $punto = $servicio->getPuntoId();
                    if($strEsIsb !== 'SI')
                    {
                        $punto->setEstado("In-Corte");
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
                                $strMensaje = "No se ha podido obtener el correcto mapeo del servicio con la ip respectiva";
                                throw new \Exception($strMensaje);
                            }
                        }
                    }
                    $boolFalse      = false;
                    if(isset($strCaractRelProdIp) && !empty($strCaractRelProdIp) &&
                       isset($intIdProdIp) && !empty($intIdProdIp) )
                    {
                        $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
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
                        $arrayServiciosPunto[] = $servicio;
                    }
                    else
                    {
                        $arrayServiciosPunto = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                                        ->findBy(array("puntoId" => $punto->getId()));
                    }
                    //cortar servicios adicionales safecity
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
                                if(is_object($objServicioAdd) && $objServicioAdd->getEstado() == "Activo")
                                {
                                    $objServicioAdd->setEstado("In-Corte");
                                    $this->emComercial->persist($objServicioAdd);
                                    $this->emComercial->flush();
                                    //ingresar historial
                                    $objServicioHistorialAdd = new InfoServicioHistorial();
                                    $objServicioHistorialAdd->setServicioId($objServicioAdd);
                                    $objServicioHistorialAdd->setObservacion("Se corto el Servicio");
                                    $objServicioHistorialAdd->setEstado("In-Corte");
                                    $objServicioHistorialAdd->setMotivoId($motivoObj->getId());
                                    $objServicioHistorialAdd->setUsrCreacion($usrCreacion);
                                    $objServicioHistorialAdd->setFeCreacion(new \DateTime('now'));
                                    $objServicioHistorialAdd->setIpCreacion($ipCreacion);
                                    $objServicioHistorialAdd->setAccion($accionObj->getNombreAccion());
                                    $this->emComercial->persist($objServicioHistorialAdd);
                                    $this->emComercial->flush();
                                }
                            }
                            //cortar servicio security ng firewall
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
                                if(is_object($objServicioAdd) && $objServicioAdd->getEstado() == "Activo")
                                {
                                    $objServicioAdd->setEstado("In-Corte");
                                    $this->emComercial->persist($objServicioAdd);
                                    $this->emComercial->flush();
                                    //ingresar historial
                                    $objServicioHistorialAdd = new InfoServicioHistorial();
                                    $objServicioHistorialAdd->setServicioId($objServicioAdd);
                                    $objServicioHistorialAdd->setObservacion("Se corto el Servicio");
                                    $objServicioHistorialAdd->setEstado("In-Corte");
                                    $objServicioHistorialAdd->setMotivoId($motivoObj->getId());
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
                    foreach($arrayServiciosPunto as $objServ)
                    {
                        if($objServ->getEstado() == "Activo" && ($strEsIsb !== 'SI' 
                            || ($strEsIsb === 'SI' && is_object($objServ->getProductoId()) 
                                && ($objServ->getId() === $servicio->getId()
                                    || (isset($arrayIdsProdsIps) && !empty($arrayIdsProdsIps)
                                        && in_array($objServ->getProductoId()->getId(), $arrayIdsProdsIps))))))
                        {
                            $objProductoAdicional = $objServ->getProductoId();
                            if(is_object($objProductoAdicional) 
                                && strpos($objProductoAdicional->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID') !== $boolFalse)
                            {
                                $objSpcSuscriberId  = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServ, "SUSCRIBER_ID", $objProductoAdicional);
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
	                            // CORTAR SERVICIO NETCAM
	                            $arrayParamsBusqCaracServCam = array("intIdServicio"                => $objServ->getId(),
	                                                                 "strDescripcionCaracteristica" => "CAMARA 3DEYE",
	                                                                 "strEstadoSpc"                 => "Activo");
	
	                            $objServCaractCam = $this->emComercial->getRepository('schemaBundle:InfoServicio')
	                                                                  ->getCaracteristicaServicio($arrayParamsBusqCaracServCam);
	
	                            $arrayParamsBusqCaracServRol = array("intIdServicio"                => $objServ->getId(),
	                                                                 "strDescripcionCaracteristica" => "ROL 3DEYE",
	                                                                 "strEstadoSpc"                 => "Activo");
	
	                            $objServCaractRol = $this->emComercial->getRepository('schemaBundle:InfoServicio')
	                                                                  ->getCaracteristicaServicio($arrayParamsBusqCaracServRol);
	
	                            if(is_object($objServCaractCam) && is_object($objServCaractRol))
	                            {
		                            $arrayRespCortarServicio = $this->servicePortalNetCam->cortarServicioNetCam($objServ->getId(), $idAccion);
	                            }
	
	                            if($arrayRespCortarServicio["strStatus"] == "ERROR")
	                            {
		                            throw new \Exception("No se pudo cortar el servicio del cliente!<br>
																	Mensaje:".$arrayRespCortarServicio["strMessage"]);
	                            }
	                            
	                            $objServ->setEstado("In-Corte");
                                $this->emComercial->persist($objServ);
                                $this->emComercial->flush();

                                $servicioHistorial = new InfoServicioHistorial();
                                $servicioHistorial->setServicioId($objServ);
                                $servicioHistorial->setObservacion("Se corto el Servicio");
                                $servicioHistorial->setEstado("In-Corte");
                                $servicioHistorial->setMotivoId($motivoObj->getId());
                                $servicioHistorial->setUsrCreacion($usrCreacion);
                                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                                $servicioHistorial->setIpCreacion($ipCreacion);
                                $servicioHistorial->setAccion($accionObj->getNombreAccion());
                                $this->emComercial->persist($servicioHistorial);
                                $this->emComercial->flush();
                                if((($prefijoEmpresa == "MD" || $prefijoEmpresa == "TTCO" || $prefijoEmpresa == "EN") && $strEsIsb !== 'SI')
                                    && $objServ->getId() === $servicio->getId())
                                {
                                    $intIdUltHistoInCorte = $servicioHistorial->getId();
                                }
                            }
                        }
                    }
                }
                else
                {
                    $servicio->setEstado("In-Corte");
                    $this->emComercial->persist($servicio);
                    $this->emComercial->flush();

                    $servicioHistorial = new InfoServicioHistorial();
                    $servicioHistorial->setServicioId($servicio);
                    $servicioHistorial->setObservacion("Se corto el Servicio");
                    $servicioHistorial->setEstado("In-Corte");
                    $servicioHistorial->setMotivoId($motivoObj->getId());
                    $servicioHistorial->setUsrCreacion($usrCreacion);
                    $servicioHistorial->setFeCreacion(new \DateTime('now'));
                    $servicioHistorial->setIpCreacion($ipCreacion);
                    $servicioHistorial->setAccion($accionObj->getNombreAccion());
                    $this->emComercial->persist($servicioHistorial);
                    $this->emComercial->flush();
                }
                if($servicio->getPlanId())
                {
                    $strDescripcion =  $servicio->getPlanId()->getNombrePlan();
                }
                else
                {
                    $strDescripcion =  $servicio->getProductoId()->getDescripcionProducto();
                }
                    
                //enviar mail
                $asunto = "Cortar Servicio : " . $servicio->getPuntoId()->getLogin() . " : " . $strDescripcion;
                $this->servicioGeneral->enviarMailCortarCliente($asunto, $servicio, $motivo, $elemento, $nombreInterfaceElemento, 
                                                                $servicioHistorial, $usrCreacion, $ipCreacion, $prefijoEmpresa);
            }
            else
            {
                $status = "ERROR";
                $mensaje = "No se pudo Cortar al Cliente! <br> Mensaje:".$mensaje;
                throw new \Exception($mensaje);                
            }
        }
        catch (\Exception $e) {
            if ($this->emInfraestructura->getConnection()->isTransactionActive()){
                $this->emInfraestructura->getConnection()->rollback();
            }
            
            if ($this->emComercial->getConnection()->isTransactionActive()){
                $this->emComercial->getConnection()->rollback();
            }
            $status         = "ERROR";
            $mensaje        = $e->getMessage();
            $arrayFinal[]   = array('status'=>"ERROR", 'mensaje'=>$mensaje);
            $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
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
        
        try 
        {
            if($status === "OK" && $prefijoEmpresa === "MD")
            {
                $arrayRespuestaCorteAdicPlan    = $this->cortarProductosAdicionalesEnPlan(array("objServicio"       => $servicio, 
                                                                                                "strUsrCreacion"    => $usrCreacion, 
                                                                                                "strClientIp"       => $ipCreacion,
                                                                                                "strCodEmpresa"     => $idEmpresa));
                if($arrayRespuestaCorteAdicPlan["status"] === "ERROR")
                {
                    $mensaje = $mensaje . "<br>" .$arrayRespuestaCorteAdicPlan["mensaje"];
                }
                
                $this->cortarServiciosAdicionalesPorPunto(array("objServicioInternet"   => $servicio,
                                                                "idAccion"              => $idAccion,
                                                                "usrCreacion"           => $usrCreacion, 
                                                                "clientIp"              => $ipCreacion,
                                                                "strCodEmpresa"         => $idEmpresa));
            }
            if($status === "OK" && (($prefijoEmpresa == "MD" || $prefijoEmpresa == "TTCO" || $prefijoEmpresa == "EN") && $strEsIsb !== 'SI'))
            {
                $strStatusActualizaInformacionInCorte   = str_repeat(' ', 5);
                $strMensajeActualizaInformacionInCorte  = str_repeat(' ', 4000);
                $strProcesoHistoInCorte                 = "CORTE";
                $strObservacionUltHistoInCorte          = 'Característica ingresada desde un corte individual con el último historial In-Corte válido '
                                                          .'para la cancelación masiva';
                try
                {
                    $strSql                 = "BEGIN INFRK_TRANSACCIONES.P_ACTUALIZA_INFORMACION_CORTE(:intIdServicio, :intIdUltHistoInCorte, "
                                                                                                    . ":strProceso, :strObservacion, "
                                                                                                    . ":strUsrCreacion, :strIpCreacion, "
                                                                                                    . ":strStatus, :strMensaje); END;";
                    $objStmt                = $this->emInfraestructura->getConnection()->prepare($strSql);
                    $objStmt->bindParam('intIdServicio', $servicio->getId());
                    $objStmt->bindParam('intIdUltHistoInCorte', $intIdUltHistoInCorte);
                    $objStmt->bindParam('strProceso', $strProcesoHistoInCorte);
                    $objStmt->bindParam('strObservacion', $strObservacionUltHistoInCorte);
                    $objStmt->bindParam('strUsrCreacion', $usrCreacion);
                    $objStmt->bindParam('strIpCreacion', $ipCreacion);
                    $objStmt->bindParam('strStatus', $strStatusActualizaInformacionInCorte);
                    $objStmt->bindParam('strMensaje', $strMensajeActualizaInformacionInCorte);
                    $objStmt->execute();
                    if($strStatusActualizaInformacionInCorte === "ERROR")
                    {
                        $mensaje = $mensaje . "<br>" .$strMensajeActualizaInformacionInCorte;
                    }
                }
                catch (\Exception $e)
                {
                    $mensaje = $mensaje . "<br>" ."No se ha podido actualizar la información de corte que será considerada en el proceso de cancelación";
                }
            }
        } catch (\Exception $objExp) 
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCortarServicioService->cortarServicio->adicionalPActualizaInfoCorte',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objExp->getMessage(),
                                             $usrCreacion,
                                             $ipCreacion);
        }
        
        //Proceso para notificar el corte del servicio a konibit mediante GDA en caso de aplicar.
        try
        {
            if (is_object($servicio) && strtoupper($status === "OK") && strtoupper($prefijoEmpresa === "MD"))
            {
                $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                        ->notificarKonibit(array ('intIdServicio'  =>  $servicio->getId(),
                                                  'strTipoProceso' => 'CORTAR',
                                                  'strTipoTrx'     => 'INDIVIDUAL',
                                                  'strUsuario'     =>  $usrCreacion,
                                                  'strIp'          =>  $ipCreacion,
                                                  'objUtilService' =>  $this->serviceUtil));

                //Se notifica el corte de los productos adicionales con la característica de KONIBIT.
                if (!empty($arrayServiciosProdKonibit['result']) && count($arrayServiciosProdKonibit['result']) > 0)
                {
                    foreach($arrayServiciosProdKonibit['result'] as $arrayValue)
                    {
                        $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                                ->notificarKonibit(array ('intIdServicio'  =>  $arrayValue['idServicio'],
                                                          'strTipoProceso' => 'CORTAR',
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
                                            'InfoCortarServicioService->cortarServicio->adicional',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objException->getMessage(),
                                             $usrCreacion,
                                             $ipCreacion);
        }

        //Si es un servico de Megadatos y el motivo ingresado es para un corte a cliente posible abusador,
        //se procede a cortar e inauditar el servicio.
        try
        {
            if($status == "OK" && (($idEmpresa == 18 || $prefijoEmpresa == "EN") && $strEsMotivoInaudit == 'true'))
            {
                //Se busca el registro Inaudit en la tabla de AdmiCaracteristica 
                $objAdmiCaract = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                ->findOneBy(array(
                    'descripcionCaracteristica' => 'InAudit',
                    'estado' => 'Activo'
                ));
              
                //Se inserta en la tabla InfoServicioHistorial un registro por el proceso de inaudit realizado
                $objServicioHistorial = new InfoServicioHistorial();
                $objServicioHistorial->setServicioId($servicio);
                $objServicioHistorial->setObservacion("Posible Abusador-Inspección en Campo.");
                $objServicioHistorial->setEstado("In-Corte");
                $objServicioHistorial->setMotivoId($motivo);
                $objServicioHistorial->setUsrCreacion($usrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($ipCreacion);
                $objServicioHistorial->setAccion($accionObj->getNombreAccion());
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();

                // Una vez ingresado el registro de historial por Inaudit, procedemos a buscar el registro 
                // para posteriormente obtener su ID
                $objServHist = $this->emComercial->getRepository('schemaBundle:InfoServicioHistorial')
                ->findMaxHistorialPorServicio($idServicio);

                //Se inserta en la tabla InfoServicioCaracteristica con caracteristica $objAdminCaract 
                //un registro por el proceso de inaudit realizado
                $objServicioCaracteristica = new InfoServicioCaracteristica();
                $objServicioCaracteristica->setServicioId($servicio);
                $objServicioCaracteristica->setCaracteristicaId($objAdmiCaract);
                $objServicioCaracteristica->setValor($objServHist->getId());
                $objServicioCaracteristica->setEstado("Activo");
                $objServicioCaracteristica->setObservacion('Característica ingresada desde un Corte - Inauditado ' .
                                                            'con el último historial In-Corte válido para la cancelación masiva');
                $objServicioCaracteristica->setUsrCreacion($usrCreacion);
                $objServicioCaracteristica->setIpCreacion($ipCreacion);
                $objServicioCaracteristica->setFeCreacion(new \DateTime('now'));
                $this->emComercial->persist($objServicioCaracteristica);
                $this->emComercial->flush();
            }   
            
        }
        catch (\Exception $e)
        {
            $mensaje = $mensaje . "<br>" ."No se ha podido Inauditar el Plan del cliente seleccionado";
        }
        try 
        {
            if($ejecutaLdap == "SI" && $status=="OK")
            {
                if($strEsIsb === 'SI')
                {
                    //envio al ldap
                    $arrayJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $idServicio, 'TN');
                }
                else
                {
                    if ($strPrefijoEmpresaOrigen == 'TNP')
                    {
                        $arrayJsonLdap = $this->servicioGeneral->ejecutarComandoLdap("A", $idServicio, $strPrefijoEmpresaOrigen);
                    }
                }
                if(isset($arrayJsonLdap->status) && $arrayJsonLdap->status!="OK")
                {
                    $mensaje = $mensaje ."<br>". $respuestaArray[0]['mensaje'] . "<br>" . $arrayJsonLdap->mensaje;
                }
            }
        } 
        catch (\Exception $objExcep) 
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCortarServicioService->cortarServicio->adicionalCorteEjecutaLdap',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objExcep->getMessage(),
                                             $usrCreacion,
                                             $ipCreacion);
        }
        $this->rdaMiddleware->ejecutaWsSinEsperarRespuestaMiddleware($arrayDataConfirmacionTn);
        $arrayFinal[] = array('status' => "OK", 'mensaje' => $mensaje);
        return $arrayFinal;
    }
    
    /**
     * Función que realiza el corte de los servicios adicionales de un punto, en donde se incluye el corte de servicios 
     * I. PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 05-08-2019
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 28-08-2019 Se especifica la tecnología en el historial del servicio
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
    public function cortarServiciosAdicionalesPorPunto($arrayParametros)
    {
        $boolFalse              = false;
        $objServicioInternet    = $arrayParametros["objServicioInternet"];
        $strUsrCreacion         = $arrayParametros["usrCreacion"];
        $strIpCreacion          = $arrayParametros["clientIp"];
        try
        {
            $arrayServicioAdicionales   = $this->emComercial->getRepository('schemaBundle:InfoServicio')
                                                            ->findBy(array('puntoId' => $objServicioInternet->getPuntoId(),
                                                                           'estado'  => 'Activo'));
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
                            $arrayParametros["strMsjHistorial"] = "Se cortó el servicio ".$objProducto->getDescripcionProducto().
                                                                  " con tecnología ".$objSpcAntivirus->getValor();
                        }
                        else
                        {
                            $arrayParametros["strMsjHistorial"] = "Se corto el Servicio";
                        }
                        $objSpcSuscriberId  = $this->servicioGeneral
                                                   ->getServicioProductoCaracteristica($objServicioAdicional, "SUSCRIBER_ID", $objProducto);
                        if(is_object($objSpcSuscriberId))
                        {
                            $arrayParametros["idServicio"] = $objServicioAdicional->getId();
                            $this->cortarServiciosOtros($arrayParametros);
                        }
                    }
                }
            }
        }
        catch (\Exception $e)
        {
            $this->serviceUtil->insertError('Telcos+', 
                                            'InfoCortarServicioService->cortarServiciosAdicionalesPorPunto', 
                                            $e->getMessage(), 
                                            $strUsrCreacion, 
                                            $strIpCreacion);
        }   
    }
       
    /**
     * Funcion para realizar el corte del servicio/producto de la empresa TNG
     * @author Jesús Banchen <jbanchen@telconet.ec>
     * @version 1. 28-03-2018 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 15-08-2019 Se modifica el mensaje de corte por cambios en js
     * 
     * @param array $arrayPeticiones [id_servicio,usrcreacion,ipcreacion,motivo,idAccion]
     * @return array $arrayRespuesta [status,mensaje]
     * 
     */
    public function cortarServicioTng($arrayPeticiones)
    {
        $strServicioId  = $arrayPeticiones['idServicio'];
        $strUsrCreacion = $arrayPeticiones['usrCreacion'];
        $strIpCreacion  = $arrayPeticiones['ipCreacion'];
        $intIdMotivo    = $arrayPeticiones['motivo'];
        $intIdAccion    = $arrayPeticiones['idAccion'];
        try
        {
            $this->emComercial->getConnection()->beginTransaction();
            $objServicio = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($strServicioId);
            $objMotivo   = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($intIdMotivo);
            $objAccion   = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);

            $objServicio->setEstado("In-Corte");
            $this->emComercial->persist($objServicio);
            $this->emComercial->flush();

            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se cortó el servicio");
            $objServicioHistorial->setEstado("In-Corte");
            $objServicioHistorial->setMotivoId($objMotivo->getId());
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
            
            $strStatus  = "OK";
            $strMensaje = "Se cortó el servicio";
        }
        catch (\Exception $ex)
        {
            if ($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->getConnection()->rollback();
                $this->emComercial->getConnection()->close();
            }
            $this->serviceUtil->insertError('Telcos+', 'InfoCortarServicioService->cortarServicioTng', $ex->getMessage(), $strUsrCreacion, $strIpCreacion);
            $strStatus = "ERROR";
            $strMensaje = "Error en el procesamiento de los datos..";
        }
        $arrayRespuesta[] = array('status' => $strStatus, 'mensaje' => $strMensaje);
        return $arrayRespuesta;
    }

    /**
     * Función que sirve para cortar productos adicionales que forman parte de un plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 28-03-2019 Se corrige filtro de detalles de plan del servicio
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 05-08-2019 Se agrega el corte del servicio I. PROTEGIDO MULTI PAID con tecnología Kaspersky dentro del plan
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.3 22-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a función gestionarLicencias, ya que no está siendo usada
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 28-07-2020 Se elimina validación de planes nuevos vigentes, ya que los detalles de los productos no son dependientes a ésta
     * 
     * @author Karen Rodríguez V. <kyrodriguez@telconet.ec>
     * @version 1.5 10-03-2021 Se agrega parámetro strEstadoSpc en la consulta de característica de servicio
     * 
     * @param array $arrayParametros [
     *                                  "objServicio"       => objeto del servicio,
     *                                  "strUsrCreacion"    => usuario de creación,
     *                                  "strClientIp"       => ip del cliente,
     *                                  "strCodEmpresa"     => código de la empresa
     *                               ]
     */
    public function cortarProductosAdicionalesEnPlan($arrayParametros)
    {
        $boolFalse          = false;
        $boolMacAfeeEnPlan  = false;
        $objProductoMcAfee  = null;
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
                    $arrayParametros["objProducto"] = $objProductoMcAfee;
                    $objSpcSuscriberId              = $this->servicioGeneral
                                                           ->getServicioProductoCaracteristica($objServicio, "SUSCRIBER_ID", $objProductoMcAfee);
                    if(!is_object($objSpcSuscriberId))
                    {
                        $arrayParametrosProdCaract['strEstadoSpc'] = 'Pendiente';
                        $objSpcSuscriberId      = $this->servicioGeneral
                                                 ->getServicioProductoCaracteristica($objServicio, "SUSCRIBER_ID", 
                                                 $objProductoMcAfee, $arrayParametrosProdCaract);
                    }
                    if(is_object($objSpcSuscriberId))
                    {
                        $arrayParamsLicencias           = array("strProceso"                => "CORTE_ANTIVIRUS",
                                                                "strEscenario"              => "CORTE_PROD_EN_PLAN",
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
                        $arrayRespuesta = $this->corteProductoMcAfeeEnPlan($arrayParametros);
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
                $strMensaje = "No se ha podido realizar el corte de los productos dentro del plan";
            }
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoActivarPuertoService->cortarProductosAdicionalesEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    /**
     * Función que realiza el corte de un servicio con el producto McAfee incluido en el plan 
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.0 16-12-2018
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.1 02-04-2019 Se agrega log de errores en problemas de cortes de suscripción McAfee
     * @since 1.0
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.2 22-08-2019 Se elimina cierre de conexión por evitar problemas al realizar el proceso de corte
     *                          y se agrega observación en la ejecución del web service donde se especifica la tecnología
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
    public function corteProductoMcAfeeEnPlan($arrayParametros)
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
                                                                                    "strEsActivacion"   => "NO",
                                                                                    "objProductoMcAfee" => $objProductoMcAfee));
            
            $arrayInfoClienteMcAfee["strTipoTransaccion"] = 'Suspension';
            if ($arrayInfoClienteMcAfee["strError"] == 'true')
            {
                $strStatus = "ERROR";
                throw new \Exception("problemas al obtener informacion del cliente");
            }
            $arrayInfoClienteMcAfee["strNombre"]         = "";
            $arrayInfoClienteMcAfee["strApellido"]       = "";
            $arrayInfoClienteMcAfee["strIdentificacion"] = "";
            $arrayInfoClienteMcAfee["strPassword"]       = "";
            $arrayInfoClienteMcAfee["strMetodo"]         = 'CancelarSuscripcion';
            $arrayInfoClienteMcAfee["intLIC_QTY"]        = $arrayInfoClienteMcAfee["strCantidadDispositivos"];
            $arrayInfoClienteMcAfee["intQTY"]            = 1;
            
            $arrayRespuestaSuscripcion = $this->licenciasMcAfee->operacionesSuscripcionCliente($arrayInfoClienteMcAfee);
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
                $objServicioHistorial->setObservacion("No se ha podido cortar el producto ".$objProductoMcAfee->getDescripcionProducto()
                                                      ." con tecnología MCAFEE incluido en el plan<br>"
                                                      .$arrayRespuestaSuscripcion["mensajeRespuesta"]);
                $objServicioHistorial->setEstado($objServicio->getEstado());
                $objServicioHistorial->setUsrCreacion($strUsrCreacion);
                $objServicioHistorial->setFeCreacion(new \DateTime('now'));
                $objServicioHistorial->setIpCreacion($strClientIp);
                $this->emComercial->persist($objServicioHistorial);
                $this->emComercial->flush();
                $this->emComercial->commit();
                $strStatus  = 'ERROR';
                throw new \Exception($arrayRespuestaSuscripcion["mensajeRespuesta"]);
            }
            
            $objServicioHistorial = new InfoServicioHistorial();
            $objServicioHistorial->setServicioId($objServicio);
            $objServicioHistorial->setObservacion("Se cortó el producto ".$objProductoMcAfee->getDescripcionProducto()
                                                  ." con tecnología MCAFEE incluido en el plan");
            $objServicioHistorial->setEstado($objServicio->getEstado());
            $objServicioHistorial->setUsrCreacion($strUsrCreacion);
            $objServicioHistorial->setFeCreacion(new \DateTime('now'));
            $objServicioHistorial->setIpCreacion($strClientIp);
            $this->emComercial->persist($objServicioHistorial);
            $this->emComercial->flush();
            
            $strStatus = "OK";
            $this->emComercial->commit();
        }
        catch (\Exception $e)
        {
            if($this->emComercial->getConnection()->isTransactionActive())
            {
                $this->emComercial->rollback();
            }
            
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
                                            'InfoCortarServicioService->corteProductoMcAfeeEnPlan',
                                            $e->getMessage(),
                                            $strUsrCreacion,
                                            $strClientIp);
        }
        $arrayRespuesta = array("status"    => $strStatus,
                                "mensaje"   => $strMensaje);
        return $arrayRespuesta;
    }
    
    
    /**
     * cortarServicioTN          
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 14-12-2016 Se invoca llamado a funcion que realice proceso de corte en enlaces backups en caso de existir
     *                         Se invoca funcion encargada de devolver la informacion de la vlan del servicio
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 05-07-2016 Se cambia envio de vlan->mac al WS y que sea solo del cliente a configurar
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.1 14-04-2016 Integracion con service para invocar WS de NW para ejecucion de scripts en equipos de TN al momento de cortar
     * @since 1.0
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.2 23-11-2016 Se ajusta validacion para determinar si un servicio es pseudope o normal
     * @since 1.1
     * 
     * @author Allan Suarez <arsuarez@telconet.ec>
     * @version 1.3 04-07-2017 Se agrega funcion de calculo de ancho de banda de concentrador
     * @since 1.2
     * 
     * @author John Vera <javera@telconet.ec>
     * @version 1.4 20-07-2017 Se registra el historial del servicio en corte y se valida objeto para recuperar el tipo de enlace
     *      
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.4 19-07-2017 Se envian los parametros en un array a la función "getArrayInfoCambioPlanPorSolicitud"
     * 
     * @author Pablo Pin <ppin@telconet.ec>
     * @version 1.5 14-05-2020 Se realiza un ajuste del orden de ejecución de las instrucciones para que el registro en el historial sea el correcto.
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.6 01-06-2020 - Se agrega el id del servicio a la url 'configSW' del ws de networking para la validación del BW
     *
     * @author Felix Caicedo <facaicedo@telconet.ec>
     * @version 1.7 14-07-2020 - Se agrega la acción de cortar en la actualización de la capacidad del concentrador
     *
     * @author Antonio Ayala <afayala@telconet.ec>
     * @version 1.8 24-09-2020 - Se agrega la acción de cortar un servicio FastCloud si el servicio principal es DIRECTLINK MPLS
     * 
     * @param Array $arrayPeticiones [  'idServicio' , 'idProducto' , 'idMotivo' , 'idAccion' , 'vlan' , 'usrCreacion' , 'ipCreacion ] 
     * @return Array $arrayFinal[ status , mensaje ]
     */
    public function cortarServicioTN($arrayPeticiones)
    {                              
        //Se obtiene la informacion a enviar
        $arrayParametrosCambP   = array();        
        $objMotivo              = $this->emGeneral->getRepository('schemaBundle:AdmiMotivo')->find($arrayPeticiones['idMotivo']);
        $objAccion              = $this->emSeguridad->getRepository('schemaBundle:SistAccion')->find($arrayPeticiones['idAccion']);
        $objServicio            = $this->emComercial->getRepository('schemaBundle:InfoServicio')->find($arrayPeticiones['idServicio']);
        
        $boolEsPesudoPe         = $this->emComercial->getRepository('schemaBundle:InfoServicio')->esServicioPseudoPe($objServicio);
        
        $objServicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                    ->findOneByServicioId($objServicio->getId());     
        
        if(!$boolEsPesudoPe)
        {
            $objServicioTecnico     = $this->emComercial->getRepository('schemaBundle:InfoServicioTecnico')
                                                        ->findOneByServicioId($objServicio->getId());        
            $objInterfaceElemento   = $this->emInfraestructura->getRepository('schemaBundle:InfoInterfaceElemento')
                                                              ->find($objServicioTecnico->getInterfaceElementoId());                
            $objElemento            = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($objServicioTecnico->getElementoId());
            $objProducto            = $this->emComercial->getRepository('schemaBundle:AdmiProducto')->find($arrayPeticiones['idProducto']);

            //Capacidades totales de los servicios activos ligados a un puerto
            $arrayCapacidades       = $this->emInfraestructura->getRepository("schemaBundle:InfoInterfaceElemento")
                                                              ->getResultadoCapacidadesPorInterface($objInterfaceElemento->getId());
            //vlan
            $strVlan                = $this->servicioGeneral->obtenerVlanServicio($objServicio);

            if(!$strVlan)
            {
                $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>'No se encontro la Vlan del Servicio');
                return $arrayFinal;
            }

            $arrayMacServicio[] = $arrayPeticiones['mac'];
            $arrayMacVlan = array($strVlan=>$arrayMacServicio);
            
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

            //accion a ejecuta        
            $arrayPeticiones['url']          = 'configSW';
            $arrayPeticiones['accion']       = 'cortar';                
            $arrayPeticiones['id_servicio']  = $objServicio->getId();
            $arrayPeticiones['nombreMetodo'] = 'InfoCortarServicioService.cortarServicioTN';
            $arrayPeticiones['sw']           = $objElemento->getNombreElemento();
            $arrayPeticiones['macVlan']      = $arrayMacVlan;
            $arrayPeticiones['user_name']    = $arrayPeticiones['usrCreacion'];
            $arrayPeticiones['user_ip']      = $arrayPeticiones['ipCreacion'];                
            $arrayPeticiones['bw_up']        = intval($arrayCapacidades['totalCapacidad1']) - intval($arrayPeticiones['capacidadUno']);
            $arrayPeticiones['bw_down']      = intval($arrayCapacidades['totalCapacidad2']) - intval($arrayPeticiones['capacidadDos']);
            $arrayPeticiones['servicio']     = $objProducto->getNombreTecnico();
            $arrayPeticiones['login_aux']    = $objServicio->getLoginAux();
            $arrayPeticiones['descripcion']  = 'cce_'.$objServicio->getLoginAux().$strDescripcion;
            $arrayPeticiones['pto']          = $objInterfaceElemento->getNombreInterfaceElemento();                     

            //Ejecucion del metodo via WS para realizar la configuracion del SW
            $arrayRespuesta = $this->networkingScripts->callNetworkingWebService($arrayPeticiones);

            $status     = $arrayRespuesta['status'];
            $mensaje    = $arrayRespuesta['mensaje'];
            $statusCode = $arrayRespuesta['statusCode'];                            
        }
        else
        {
            $status     = "OK";
            $statusCode = 200;
            $mensaje    = "OK";
        }                

        //*DECLARACION DE TRANSACCIONES------------------------------------------*/        
        $this->emComercial->getConnection()->beginTransaction();
        $this->emInfraestructura->getConnection()->beginTransaction();
        //*----------------------------------------------------------------------*/

        //LOGICA DE NEGOCIO-----------------------------------------------------*/
        try{

            if($status=="OK")
            {
                /*Ejecución en Concentrador
                Se realiza validación para que solo ejecute recalculo de BW para Servicios con tipo de enlace PRINCIPAL
                bajar bw del concentrador*/
                $arrayParametrosBw = array(
                    "objServicio"       => $objServicio,
                    "nombreAccionBw"    => 'cortar',
                    "usrCreacion"       => $arrayPeticiones['usrCreacion'],
                    "ipCreacion"        => $arrayPeticiones['ipCreacion'],
                    "capacidadUnoNueva" => intval($arrayPeticiones['capacidadUno']),
                    "capacidadDosNueva" => intval($arrayPeticiones['capacidadDos']),
                    "operacion"         => "-",
                    "accion"            => "Se actualiza Capacidades por Corte de "
                        . "servicio : <b>".$objServicio->getLoginAux()."<b>"
                );

                //Se actualiza las capacidades del Concentrador
                $this->servicioGeneral->actualizarCapacidadesEnConcentrador($arrayParametrosBw);

                //historial del servicio
                $servicioHistorial = new InfoServicioHistorial();
                $servicioHistorial->setServicioId($objServicio);
                $servicioHistorial->setObservacion("Se Corto el Servicio");
                $servicioHistorial->setEstado("In-Corte");
                $servicioHistorial->setUsrCreacion($arrayPeticiones['usrCreacion']);
                $servicioHistorial->setFeCreacion(new \DateTime('now'));
                $servicioHistorial->setIpCreacion($arrayPeticiones['ipCreacion']);
                $servicioHistorial->setAccion($objAccion->getNombreAccion());
                $servicioHistorial->setMotivoId($objMotivo->getId());
                $this->emComercial->persist($servicioHistorial);
                $this->emComercial->flush();
               
                $objServicio->setEstado("In-Corte");
                $this->emComercial->persist($objServicio);
                $this->emComercial->flush();
               
                $strTipoEnlace = '';
                if(is_object($objServicioTecnico))
                {
                    $strTipoEnlace = $objServicioTecnico->getTipoEnlace();
                }
               //ejecutar corte de servicio para enlace backup cuando se requiere cortar enlace Principal
                if($strTipoEnlace == 'PRINCIPAL')
                {
                    $arrayParametros                    = array();
                    $arrayParametros['objServicio']     = $objServicio;
                    $arrayParametros['intCapacidadUno'] = intval($arrayCapacidades['totalCapacidad1']) - intval($arrayPeticiones['capacidadUno']);
                    $arrayParametros['intCapacidadDos'] = intval($arrayCapacidades['totalCapacidad2']) - intval($arrayPeticiones['capacidadDos']);
                    $arrayParametros['strUsrCreacion']  = $arrayPeticiones['usrCreacion'];
                    $arrayParametros['strIpCreacion']   = $arrayPeticiones['ipCreacion'];
                    $arrayParametros['objMotivo']       = $objMotivo;
                    $arrayParametros['objAccion']       = $objAccion;
                    $arrayParametros['strAccion']       = 'cortar';
                    $arrayParametros['strEstado']       = 'In-Corte';
                    $arrayParametros['strObservacion']  = "Se Cortó el Servicio <b>Backup</b> por Corte en Servicio <b>Principal : ".
                                                          $objServicio->getLoginAux().'</b>';
                    
                    $arrayRespuestaBck = $this->servicioGeneral->cortarReconectarServicioBackup($arrayParametros);
                    
                    if($arrayRespuestaBck['strStatus'] != 'OK')
                    {
                        $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=> $arrayRespuestaBck['strMensaje']);
                        return $arrayFinal;
                    }

                }
               
                $arrayFinal[] = array('status'=>$status, 'mensaje'=>"OK", 'statusCode'=>$statusCode);            
            }
            else
            {
                $arrayFinal[] = array('status'  => $status, 
                                      'mensaje' => "Error: ".$mensaje,
                                      'statusCode'=>$statusCode);
            }

            $arrayParametrosCambP["idServicio"]  = $arrayPeticiones['idServicio'];
            $arrayParametrosCambP["tipoProceso"] = "";

            //Verificar si no existe solicitud de CancelacionMasiva
            $arrayResultado = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                ->getArrayInfoCambioPlanPorSolicitud($arrayParametrosCambP);
            
            //Existe solicitud EnProceso de Cancelacion para el servicio ( Proceso Masivo )
            if($arrayResultado && count($arrayResultado)>0)
            {
                $objDetalleSolicitud = $this->emComercial->getRepository('schemaBundle:InfoDetalleSolicitud')
                                                         ->find($arrayResultado['idSolicitud']);

                if($objDetalleSolicitud)
                {
                    if($status == "OK")
                    {
                        //Finalizar la solicitud de cambio de plan                        
                        $objDetalleSolicitud->setEstado("Finalizada");
                        $this->emComercial->persist($objDetalleSolicitud);
                        $this->emComercial->flush();   
                        
                        $arrayPeticiones['idSolicitudPadre'] = $arrayResultado['idSolicitudPadre'];
                        
                        $this->servicioGeneral->finalizarSolicitudPadrePorProcesoMasivo($arrayPeticiones);
                    }

                    //Se crea Historial de Servicio
                    $objDetalleSolsHist = new InfoDetalleSolHist();
                    $objDetalleSolsHist->setDetalleSolicitudId($objDetalleSolicitud);
                    $objDetalleSolsHist->setEstado($objDetalleSolicitud->getEstado());
                    $objDetalleSolsHist->setFeCreacion(new \DateTime('now'));
                    $objDetalleSolsHist->setUsrCreacion($arrayPeticiones['usrCreacion']);
                    $objDetalleSolsHist->setIpCreacion($arrayPeticiones['ipCreacion']);
                    $objDetalleSolsHist->setObservacion($status == "OK"?"Se Realizo Corte exitosamente":$mensaje);
                    $this->emComercial->persist($objDetalleSolsHist);
                    $this->emComercial->flush();
                }
            } 
            
            if($status=="OK")
            {
                //Consultamos si el servicio tiene relacionado servicios como FastCloud
                $arrayServiciosRelacion = $this->getServiciosRelacion($arrayPeticiones['idServicio']);
                foreach ($arrayServiciosRelacion as $arrayServiciosRel)
                {
                    $arrayPeticionesServiciosRel = array();
                    $arrayPeticionesServiciosRel['idServicio']           = $arrayServiciosRel;
                    $arrayPeticionesServiciosRel['strCodEmpresa']        = '10';
                    $arrayPeticionesServiciosRel['idAccion']             = $arrayPeticiones['idAccion'];
                    $arrayPeticionesServiciosRel['idMotivo']             = $arrayPeticiones['idMotivo'];
                    $arrayPeticionesServiciosRel['usrCreacion']          = $arrayPeticiones['usrCreacion'];
                    $arrayPeticionesServiciosRel['clientIp']             = $arrayPeticiones['ipCreacion'];
                                    
                    $arrayRespuestaSer  = $this->cortarServiciosOtros($arrayPeticionesServiciosRel);
                    $strStatus          = $arrayRespuestaSer['status'];
                }
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
            $mensaje = "ERROR, ".$e->getMessage();
            $arrayFinal[] = array('status'=>"ERROR", 'mensaje'=>$mensaje,'statusCode'=>500);
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
     * Funcion que sirve para cortar el servicio de un cliente para la empresa MD
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 15-04-2015
     * @since 1.0
     * 
     * Opcion para crte de servicio NETLIFE WIFI
     * @author Veronica Carrasco <vcarrasco@telconet.ec>
     * @since 1.1 05-07-2016
     * 
     * @author Francisco Adum <fadum@netlife.net.ec>
     * @version 1.2 18-05-2017  Se actualiza metodo para que utilice el middleware de RDA.
     *                          Se eliminan validaciones de equipos conectados y obtencion de Ip ARP.
     * 
     * @author Ricardo Coello Quezada <rcoello@telconet.ec>
     * @version 1.3 25-01-2018  Se realiza ajuste para el producto de TN "INTERNET SMALL BUSINESS". No ingrese al flujo de Corte de servicio 
     *                          NETLIFE WIFI.
     *                          Se añade nuevo parametro de envio al servicio de RDA [empresa]. Si el producto es "INTERNET SMALL BUSINESS" 
     *                          se enviará 'TN' caso contrario 'MD'.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.4 15-05-2018  Se agrega prefijo de empresa en caso de existir error al cortar, para que el envío de los parámetros al reconectar 
     *                          sea correcto
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.5 23-05-2018  Se modifica la forma de obtener el line profile para los Servicios Small Business 
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.6 09-07-2018  Se agrega programación para flujo de servicios con tecnología ZTE
     * @since 1.5
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.7 28-11-2018  Se agregan validaciones para gestionar el producto de la empresa TNP
     * @since 1.6
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.8 08-03-2019 Se agrega validación para que el tipo de negocio sea HOME para servicios TelcoHome 
     *                          y PYME para servicios Small Business
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 07-10-2019  Se modifica proceso de corte de servicios Netlifezone
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.10 11-05-2020 Se unifica las validaciones por marca y no por modelo de olt
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.11 09-11-2020  Se agrega envío de nuevos parámetros al middleware en caso de clientes PYME (ip_fija_wan, tipo_plan_actual)
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 08-11-2021 Se construye el arreglo con la información que se enviará al web service para confirmación de opción 
     *                          de Tn a Middleware
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.2 18-03-2023 - Se agrega Validación y envio de prefijo empresa para llamada al middleware al Cortar servicio en ecuanet.
     *  
     * @param Array $arrayParametros [servicioTecnico, interfaceElemento, modeloElemento, servicio, producto, usrCreacion, ipCreacion, idEmpresa]
     */
    private function cortarServicioMd($arrayParametros)
    {
        $strPrefEmpOrigen   = $arrayParametros['strPrefijoEmpresaOrigen'];
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $modeloElemento     = $arrayParametros['modeloElemento'];
        $servicio           = $arrayParametros['servicio'];
        $producto           = $arrayParametros['producto'];
        $usrCreacion        = $arrayParametros['usrCreacion'];
        $ipCreacion         = $arrayParametros['ipCreacion'];
        $flagMiddleware     = $arrayParametros['flagMiddleware'];
        $strEsIsb           = $arrayParametros['strEsIsb'];
        $intIdAccion        = $arrayParametros['intIdAccion'];
        $strCodEmpresa      = $arrayParametros['idEmpresa'];
        $strMsjAdicional    = "";
        $strCapacidad1      = "";
        $strCapacidad2      = "";
        $strPrefijoEmpRDA   = ($strEsIsb == 'SI') ? 'TN' : 'MD';
        $strPrefijoEmpRDA   = ($strPrefEmpOrigen == 'TNP') ? 'TNP' : $strPrefijoEmpRDA ;
        $strPrefijoEmpRDA   = ($strPrefEmpOrigen == 'EN') ? 'EN' : $strPrefijoEmpRDA ;
        $status             = "ERROR";
        $mensaje            = "Error Desconocido";
        $spcIndiceCliente   = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "INDICE CLIENTE", $producto);
        $elemento           = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')->find($servicioTecnico->getElementoId());
        $objIpElemento      = $this->emInfraestructura->getRepository('schemaBundle:InfoIp')
                                    ->findOneBy(array('elementoId' => $elemento->getId(), 'estado' => 'Activo'));
        $arrayDataConfirmacionTn = array();
        
                           
        try
        {
            $strMarcaOlt    = $modeloElemento->getMarcaElementoId()->getNombreMarcaElemento();
            /*
             * Codigo para diferenciar si se necesita utilizar middleware o si se necesita utilizar
             * el flujo tradicional
             */
            if($flagMiddleware)
            {
                //Se agrega validacion de Olt para no ejecutar fisicamente la cancelación en caso de que se encuentre NO Operativos
                $entityDetalleElemento = $this->emInfraestructura->getRepository('schemaBundle:InfoDetalleElemento')
                                                                 ->findOneBy(array("elementoId"     => $interfaceElemento->getElementoId()->getId(), 
                                                                                   "detalleNombre"  => "OLT OPERATIVO"));
                if ($entityDetalleElemento)
                {
                    if ($entityDetalleElemento->getDetalleValor() == "NO")
                    {
                        $status         = "OK";
                        $mensaje        = "OK";
                        $arrayFinal[]   = array('status' => $status, 'mensaje' => $mensaje);
                        return $arrayFinal;
                    }
                }

                //OBTENER NOMBRE CLIENTE
                $objPersona = $servicio->getPuntoId()->getPersonaEmpresaRolId()->getPersonaId();
                $strNombreCliente       = ($objPersona->getRazonSocial() != "") ? $objPersona->getRazonSocial() : 
                                                                    $objPersona->getNombres()." ".$objPersona->getApellidos();

                //OBTENER IDENTIFICACION
                $strIdentificacion      = $objPersona->getIdentificacionCliente();

                //OBTENER LOGIN
                $strLogin               = $servicio->getPuntoId()->getLogin();
                
                //obtener tipo de negocio
                $strTipoNegocio         = $servicio->getPuntoId()->getTipoNegocioId()->getNombreTipoNegocio();

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
                        $strLineProfile = $spcLineProfile->getValor();
                        $arrayPerfil    = explode("_", $strLineProfile);
                        if($strEsIsb === "SI")
                        {
                            $strLineProfile = $arrayPerfil[0]."_".$arrayPerfil[1]."_".$arrayPerfil[2];
                        }
                        else
                        {
                            $strLineProfile = $arrayPerfil[0]."_".$arrayPerfil[1];  
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
                }
                if ($strPrefijoEmpRDA == 'TNP')
                {
                    if ($strEsIsb == 'SI')
                    {
                        $strTipoNegocio = 'PYME';
                    }
                    else
                    {
                        $strTipoNegocio = 'HOME';
                    }
                }
                else if($strPrefijoEmpRDA === "TN")
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
                                        'mac_wifi'              => $strMacWifi,
                                        'capacidad_up'          => $strCapacidad1,
                                        'capacidad_down'        => $strCapacidad2,
                                        'tipo_negocio_actual'   => $strTipoNegocio,
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
                                                                                       "strTipoProceso"    => "CORTAR_PLAN",
                                                                                       "arrayInformacion"  => $arrayDatos));
                    if($arrayRespuestaSeteaInfo["strStatus"]  === "OK")
                    {
                        $arrayDatos = $arrayRespuestaSeteaInfo["arrayInformacion"];
                    }
                    else
                    {
                        $arrayFinal[] = array('status'  => $arrayRespuestaSeteaInfo["strStatus"],
                                              'mensaje' => "Existieron problemas al recuperar información ".
                                                           "necesaria para ejecutar proceso, favor notifique a Sistemas.");
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
                    $scriptArray1 = $this->servicioGeneral->obtenerArregloScript("obtenerMacIpDinamica",$modeloElemento);
                    $idDocumentoMac= $scriptArray1[0]->idDocumento;
                    $usuarioMac= $scriptArray1[0]->usuario;
                    $macDinamica = $this->getMacIpDinamica($servicioTecnico, $usuarioMac, $interfaceElemento, 
                                                           $spcIndiceCliente, $idDocumentoMac);

                    //2. OLT-OBTENCION IP WIFI - ARP
                    $scriptArray2 = $this->servicioGeneral->obtenerArregloScript("obtenerIpDinamicaArp",$modeloElemento);
                    $idDocumento2= $scriptArray2[0]->idDocumento;
                    $usuarioIp= $scriptArray2[0]->usuario;
                    $ipDinamicaArp = $this->getIpDinamica($servicioTecnico, $usuarioIp, $interfaceElemento, $macDinamica->mensaje, $idDocumento2);

                    //2. OLT-OBTENCION IP WIFI - DHCP
                    $scriptArray3 = $this->servicioGeneral->obtenerArregloScript("obtenerIpDinamicaDhcp",$modeloElemento);
                    $idDocumento3= $scriptArray3[0]->idDocumento;
                    $ipDinamicaDhcp = $this->getIpDinamica($servicioTecnico, $usuarioIp, $interfaceElemento, $macDinamica->mensaje, $idDocumento3);

                    //cambiar perfil a cortado 
                    $arrParamCorte = array  (
                                                'elemento'          => $elemento,
                                                'interfaceElemento' => $interfaceElemento,
                                                'spcIndiceCliente'  => $spcIndiceCliente,
                                                'spcSPid'           => "",
                                                'servicioTecnico'   => $servicioTecnico,
                                                'serviceProfile'    => ""
                                            );
                    $resultadJson = $this->cortarServicioOlt($arrParamCorte);

                    if($resultadJson->status == "OK"){
                        if($ipDinamicaDhcp->mensaje!=""){
                            //4.  OLT-ACTUALIZACION TABLA IP DHCP
                            $scriptArray5 = $this->servicioGeneral->obtenerArregloScript("clearTablaIpDhcp",$modeloElemento);
                            $idDocumento5= $scriptArray5[0]->idDocumento;
                            $this->clearTablaIp($servicioTecnico, $idDocumento5,$ipDinamicaDhcp->mensaje);
                        }

                        if($ipDinamicaArp->mensaje!=""){
                            //4.  OLT-ACTUALIZACION TABLA IP ARP
                            $scriptArray4 = $this->servicioGeneral->obtenerArregloScript("clearTablaIpArp",$modeloElemento);
                            $idDocumento4= $scriptArray4[0]->idDocumento;
                            $this->clearTablaIp($servicioTecnico, $idDocumento4,$ipDinamicaArp->mensaje);
                        }

                        $status = "OK";
                        $mensaje = "OK";
                    }
                    else{
                        $status = "ERROR";
                        $mensaje = $resultadJson->mensaje;
                        throw new \Exception($mensaje);
                    }
                }
                else if($modeloElemento->getNombreModeloElemento()=="MA5608T")
                {
                    //obtener ont
                    $elementoCliente = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                            ->find($servicioTecnico->getElementoClienteId());
                    $nombreModeloElementoCliente = $elementoCliente->getModeloElementoId()->getNombreModeloElemento();

                    $spcSpid    = $this->servicioGeneral->getServicioProductoCaracteristica($servicio, "SPID", $producto);

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
                        //En caso de no existir reconfigurar  el SPID para el cliente colocando la informacion de su perfil                   
                        $arrayVerificarSpidExistente = array('spid'               => $spcSpid->getValor(),
                                                             'vlan'               => $spcVlan->getValor(),
                                                             'interfaceElemento'  => $interfaceElemento,
                                                             'ontId'              => $spcIndiceCliente->getValor(),
                                                             'gemPort'            => $spcGemPort->getValor(),
                                                             'trafficTable'       => $spcTraffic->getValor(),
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

                    $arrParamCorte = array  (
                                                'elemento'          => $elemento,
                                                'interfaceElemento' => $interfaceElemento,
                                                'spcIndiceCliente'  => $spcIndiceCliente,
                                                'spcSpid'           => $spcSpid,
                                                'servicioTecnico'   => $servicioTecnico,
                                                'serviceProfile'    => $nombreModeloElementoCliente
                                            );
                    $resultadJson = $this->cortarServicioOlt($arrParamCorte);
                    $status       = $resultadJson->status;

                    if($status!="OK")
                    {                    
                        //rollback de corte
                        $arrParamRollback = array   (
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
                        $jsonRollback = $this->reconectarService->reconectarServicioOltHuawei($arrParamRollback);

                        $status  = "ERROR";
                        $mensaje = "No se pudo Cortar al Cliente! <br> Mensaje Error:".$resultadJson->mensaje ."<br>".
                                    "Mensaje Rollback:".$jsonRollback->mensaje;
                        throw new \Exception($mensaje);
                    }//if($status!="OK")
                }//else if($modeloElemento->getNombreModeloElemento()=="MA5608T")
                else
                {
                    throw new \Exception("Modelo de OLT no tiene aprovisionamiento");
                }
            }
            
            //Corte de servicio NETLIFE WIFI
            if($status == "OK")
            {
                /*Se agrega código corregido para corte de servicios NetlifeZone*/
                $mensaje = $mensaje;

                if($strEsIsb !== "SI")
                {
                    $arrayParametrosCortarNz = array();
                    $arrayParametrosCortarNz['objServicio']    = $servicio;
                    $arrayParametrosCortarNz['intIdEmpresa']   = $strCodEmpresa;
                    $arrayParametrosCortarNz['intIdAccion']    = $intIdAccion;
                    $arrayParametrosCortarNz['strUsrCreacion'] = $usrCreacion;
                    $arrayParametrosCortarNz['strIpCreacion']  = $ipCreacion;
                    //Obtenemos los usuarios no procesados
                    $strMsjAdicional = $this->cortarServiciosNetlifeWifi($arrayParametrosCortarNz);
                }
            }
            else
            {
                if($flagMiddleware)
                {
                    //REACTIVAR AL CLIENTE POR ERROR
                    $arrayDatos['estado_servicio'] = 'In-Corte';
                    $arrayDatosMiddleware = array(
                                                    'nombre_cliente'        => $strNombreCliente,
                                                    'login'                 => $strLogin,
                                                    'identificacion'        => $strIdentificacion,
                                                    'datos'                 => $arrayDatos,
                                                    'opcion'                => 'REACTIVAR',
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
        }//try
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
    
    /**
     * Funcion que sirve para cortar clientes transtelco
     * 
     * @author Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 13-02-2016
     * @param String $modeloElemento
     * @param String $interfaceElemento
     * @param String $servicio
     * @param String $producto
     * @param String $capacidad1
     * @param String $capacidad2
     * @param String $capacidad2
     * @param String $usrCreacion
     * @param String $ipCreacion
     */
    public function cortarClienteTtco($modeloElemento, $interfaceElemento, $servicio, $producto, $capacidad1, $capacidad2, $usrCreacion, $ipCreacion)
    {
        $status               = "NA";
        $mensaje              = "NA";
        $nombreModeloElemento = $modeloElemento->getNombreModeloElemento();
        $reqAprovisionamiento = $modeloElemento->getReqAprovisionamiento();
        
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray = $this->servicioGeneral->obtenerArregloScript("cortarCliente",$modeloElemento);
        $idDocumento = $scriptArray[0]->idDocumento;
        $usuario     = $scriptArray[0]->usuario;
        $protocolo   = $scriptArray[0]->protocolo;
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
        
        if($reqAprovisionamiento=="SI")
        {
            if($nombreModeloElemento=="6524")
            {
                $resultadJson = $this->cortarCliente6524($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                         $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="7224")
            {
                $resultadJson = $this->cortarCliente7224($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                         $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="R1AD24A")
            {
                $resultadJson = $this->cortarClienteR1AD24A($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                            $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="R1AD48A")
            {
                $resultadJson = $this->cortarClienteR1AD48A($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                            $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="A2024")
            {
                $resultadJson = $this->cortarClienteA2024($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                          $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="A2048")
            {
                $resultadJson = $this->cortarClienteA2048($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                          $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="MEA1")
            {
                $resultadJson = $this->cortarClienteMea1($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                         $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="MEA3")
            {
                $resultadJson = $this->cortarClienteMea3($idDocumento, $usuario, $protocolo, $interfaceElemento->getElementoId(), 
                                                         $interfaceElemento->getNombreInterfaceElemento());
            }
            else if($nombreModeloElemento=="IPTECOM" || $nombreModeloElemento=="411AH" || $nombreModeloElemento=="433AH")
            {
                $puntoId = $servicio->getPuntoId();
                $punto   = $this->emComercial->getRepository('schemaBundle:InfoPunto')->find($puntoId->getId());
                $login   = $punto->getLogin();
                
                $caracteristica = $this->emComercial->getRepository('schemaBundle:AdmiCaracteristica')
                                                    ->findBy(array( "descripcionCaracteristica" => "MAC"));
                $prodCaract     = $this->emComercial->getRepository('schemaBundle:AdmiProductoCaracteristica')
                                                    ->findBy(array( "productoId"       => $producto->getId(), 
                                                                    "caracteristicaId" => $caracteristica[0]->getId()));
                $servicioProdCaract = $this->emComercial->getRepository('schemaBundle:InfoServicioProdCaract')
                                           ->findBy(array( "servicioId" => $servicio->getId(), "productoCaracterisiticaId"=>$prodCaract[0]->getId()));
                $mac                = $servicioProdCaract[0]->getValor();

                /*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray1 = $this->servicioGeneral->obtenerArregloScript("encontrarNumbersMac",$modeloElemento);
                $idDocumento1 = $scriptArray1[0]->idDocumento;
                $usuario1     = $scriptArray1[0]->usuario;
                /*----------------------------------------------------------------------*/

                //numbers de la mac
                $datos2        = $mac;
                $resultadJson2 = $this->cortarClienteIPTECOM($idDocumento1, $usuario1, "radio", $interfaceElemento->getElementoId(), $datos2);
                $resultado     = $resultadJson2->mensaje;

                $numbers = explode("\n", $resultado);
                $flag    = 0;

                for ($i = 0; $i < count($numbers); $i++)
                {
                    if (stristr($numbers[$i], $mac) === FALSE)
                    {
                        
                    }
                    else
                    {

                        if ($nombreModeloElemento == "411AH")
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
                    $status           = "ERROR";
                    $mensaje          = "ERROR ELEMENTO";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                    return $arrayRespuesta;
                }

                //base
                if($nombreModeloElemento=="411AH"){
                    $datos = $mac.",".$numero[0];
                }
                else{
                    $datos = $mac.",".$numero[1];
                }

                $resultadJson1 = $this->cortarClienteIPTECOM($idDocumento, $usuario, "radio", $interfaceElemento->getElementoId(), $datos);
                $statusBase    = $resultadJson1->status;

                //servidor
                $datos1           = $login;
                $elementoIdRadius = $this->emInfraestructura->getRepository('schemaBundle:InfoElemento')
                                                            ->findOneBy(array( "nombreElemento" => "ttcoradius"));

                /*OBTENER SCRIPT--------------------------------------------------------*/
                $scriptArray = $this->servicioGeneral->obtenerArregloScriptGeneral("cortarClienteRADIUS",$elementoIdRadius->getModeloElementoId());
                $idDocumento = $scriptArray[0]->idDocumento;
                $usuario     = $scriptArray[0]->usuario;
                $protocolo   = $scriptArray[0]->protocolo;
                /*----------------------------------------------------------------------*/
                
                $resultadJson = $this->cortarClienteRADIUSM($idDocumento, $usuario, "servidor", $elementoIdRadius, $datos1);
                $statusRadius = $resultadJson->status;

                if($statusBase=="OK" && $statusRadius=="OK")
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

            if ($nombreModeloElemento != "IPTECOM" && $nombreModeloElemento != "411AH" && $nombreModeloElemento != "433AH")
            {
                if ($resultadJson->status == "OK")
                {
                    $status           = "OK";
                    $mensaje          = "OK";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                }
                else
                {
                    $status           = "ERROR";
                    $mensaje          = "ERROR";
                    $arrayRespuesta[] = array('status' => $status, 'mensaje' => $mensaje);
                }
            }
        }
        else{
            $status           = "OK";
            $mensaje          = "OK";
            $arrayRespuesta[] = array('status'=>$status, 'mensaje'=>$mensaje);
        }
        
        return $arrayRespuesta;
    }
    
    public function clearTablaIp($servicioTecnico,$idDocumento,$ip){
        $datos = $ip;
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".$this->host."' '".
            $idDocumento."' 'usuario' 'SSH' '".$servicioTecnico->getElementoId()."' '".$datos."' '".$this->pathParameters."'";
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
    
    private function apagarPuertoOlt($elemento,$interfaceElemento,$servProdCaracIndiceCliente,$servicioTecnico){
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray = $this->servicioGeneral->obtenerArregloScript("apagarPuertoOlt",$elemento->getModeloElementoId());
        $idDocumento= $scriptArray[0]->idDocumento;
        $usuario= $scriptArray[0]->usuario;
        $protocolo= $scriptArray[0]->protocolo;
        /*----------------------------------------------------------------------*/
                    
        $datos = $interfaceElemento->getNombreInterfaceElemento().",".$servProdCaracIndiceCliente->getValor();
        $comando = "java -jar -Djava.security.egd=file:/dev/./urandom ".$this->pathTelcos."telcos/src/telconet/tecnicoBundle/batch/md_ejecucion.jar '".
            $this->host."' '".$idDocumento."' '".$usuario."' '".$protocolo."' '".
            $servicioTecnico->getElementoId()."' '".$datos."' '".$this->pathParameters."'";
        $salida= shell_exec($comando);
        $pos = strpos($salida, "{"); 
        $jsonObj= substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);
        
        return $resultadJson;
    }
    
    /**
     * Funcion que sirve para cortar el servicio de un cliente en el elemento Olt y para realizar rollback al reconectar el servicio
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.1 11-05-2015
     * @since 1.0
     * @param $arrayParametros (elemento, interfaceElemento, spcIndiceCliente, spcSpid, servicioTecnico)
     */
    public function cortarServicioOlt($arrayParametros)
    {
        $elemento           = $arrayParametros['elemento'];
        $interfaceElemento  = $arrayParametros['interfaceElemento'];
        $spcIndiceCliente   = $arrayParametros['spcIndiceCliente'];
        $spcSpid            = $arrayParametros['spcSpid'];
        $servicioTecnico    = $arrayParametros['servicioTecnico'];
        $serviceProfile     = $arrayParametros['serviceProfile'];
        
        /*OBTENER SCRIPT--------------------------------------------------------*/
        $scriptArray    = $this->servicioGeneral->obtenerArregloScript("cortarCliente",$elemento->getModeloElementoId());
        $idDocumento    = $scriptArray[0]->idDocumento;
        $usuario        = $scriptArray[0]->usuario;
        $protocolo      = $scriptArray[0]->protocolo;
        /*----------------------------------------------------------------------*/
        
        if($elemento->getModeloElementoId()->getNombreModeloElemento()=="EP-3116")
        {
            $datos = $interfaceElemento->getNombreInterfaceElemento().",".$spcIndiceCliente->getValor().",".$spcIndiceCliente->getValor();
        }
        else if($elemento->getModeloElementoId()->getNombreModeloElemento()=="MA5608T")
        {
            //dividir interface para obtener tarjeta y puerto pon
            list($tarjeta, $puertoPon) = split('/',$interfaceElemento->getNombreInterfaceElemento());
            $datos = $spcSpid->getValor().",".$tarjeta.",".$puertoPon.",".$spcIndiceCliente->getValor().",".$serviceProfile.",".$spcSpid->getValor()
                     .",".$tarjeta.",".$puertoPon.",".$spcIndiceCliente->getValor().",".$tarjeta.",".$puertoPon.",".$spcIndiceCliente->getValor()
                     .",".$puertoPon.",".$spcIndiceCliente->getValor();
        }
        
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
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo A2024
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteA2024($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo A2048
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteA2048($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);        
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo R1AD24A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteR1AD24A($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo R1AD48A
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteR1AD48A($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo 6524
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarCliente6524($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo 7224
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarCliente7224($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo MEA1
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteMea1($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
    {
        $salida = $this->servicioGeneral->ejecutarComandoDslam($idDocumento, $usuario, $protocolo, $elementoId->getId(), $nombreInterfaceElemento);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }

    /**
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un dslam de modelo MEA3
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteMea3($idDocumento, $usuario, $protocolo, $elementoId, $nombreInterfaceElemento)
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
     * Funcion que sirve cortar el servicio de un cliente
     * que se encuentra configurado en un servidor RADIUS
     * 
     * @author Francisco Adum <fadum@telconet.ec>
     * @version 1.0 26-11-2014
     */
    public function cortarClienteRADIUSM($idDocumento, $usuario, $tipo, $elementoId, $datos)
    {
        $salida = $this->servicioGeneral->ejecutarComandoRadio($idDocumento, $usuario, $tipo, $elementoId->getId(), $datos);
        $pos = strpos($salida, "{");
        $jsonObj = substr($salida, $pos);
        $resultadJson = json_decode($jsonObj);

        return $resultadJson;
    }
    
    /**
     * Funcion que genera realizar el corte de servicios OTROS
     * 
     * @author  Creado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.0 22-10-2015
     * 
     * @author  Modificado: Jesus Bozada <jbozada@telconet.ec>
     * @version 1.1 09-09-2016     Se modifica cadena de parametro enviado a WS de mcafee para procesar transaccion
     * 
     * @param  $array $arrayParametrosActivacion
     * 
     * @author Walther Joao Gaibo <wgaibor@telconet.ec>
     * @version 1.2 28-09-2016 - Se añade logica para cortar servicio Office 365
     * 
     * @author  Modificado: Walther Joao Gaibor <wgaibor@telconet.ec>
     * @version 1.3 01-12-2016 - Se crea producto NetlifeCloud en reemplazo del Office 365, se procede a cambiar el producto
     *
     * @author Walther Joao Gaibor C. <wgaibor@telconet.ec>
     * @version 1.4 21-06-2017 Se envia false como parametro a la función getDatosClientePorIdServicio.
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.9 16-12-2018 Se modifica el envío de parámetros a las funciones obtenerInformacionClienteMcAffe
     * 
     * @author Jesús Bozada <jbozada@telconet.ec>
     * @version 1.10 02-04-2019 Se agrega log de errores en problemas de corte de suscripción McAfee
     * @since 1.9
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.11 13-08-2019 Se agrega el corte de servicios adicionales I.PROTEGIDO MULTI PAID con tecnología Kaspersky
     * 
     * @author Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.12 23-08-2019 Se elimina envío de variable strMsjErrorAdicHtml a función gestionarLicencias, ya que no está siendo usada
     *
     * @author Germán Valenzuela <gvalenzuela@telconet.ec>
     * @version 2.0 21-11-2019 - Se agrega el proceso para notificar el corte del servicio a konibit mediante GDA en caso de aplicar.
     */
    public function cortarServiciosOtros($arrayParametrosActivacion)
    {
        $intIdServicio                        = $arrayParametrosActivacion['idServicio'];
        $intIdAccion                          = $arrayParametrosActivacion['idAccion'];
        $strUsrCreacion                       = $arrayParametrosActivacion['usrCreacion'];
        $strClientIp                          = $arrayParametrosActivacion['clientIp'];
        $arrayParametros                      = array();
        $arrayRespuestaServicio               = array();
        $entityAdmiProducto                   = null;
        $entityInfoPlanCab                    = null;
        $strPlan                              = "";
        $booleanValidaProducto                = false;
        $booleanValidaProductoProteccionTotal = false;
        $booleanValidaProductoOffice          = false;
        $booleanValidaProtegido               = false;
        $booleanValidaOfficeMig               = false;
        $boolEsProdIProtegMultiPaid           = false;
        $boolFalse                            = false;
        $strTieneSuscriberId                  = "NO";
        $strCodEmpresa                        = $arrayParametrosActivacion['strCodEmpresa'] ? $arrayParametrosActivacion['strCodEmpresa'] : "18";
        $strMsjHistorial                      = $arrayParametrosActivacion['strMsjHistorial'] 
                                                ? $arrayParametrosActivacion['strMsjHistorial'] : "Otros: Se corto el servicio";
        $strNombreServicio                    = "";
        
        $em                      = $this->emComercial;
        $emSeguridad             = $this->emSeguridad;
        
        $em->getConnection()->beginTransaction();

        try
        {
            $servicio = $em->getRepository('schemaBundle:InfoServicio')->find($intIdServicio);
            $accion   = $emSeguridad->getRepository('schemaBundle:SistAccion')->find($intIdAccion);
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
            $servicio->setEstado("In-Corte");
            $em->persist($servicio);
            $em->flush();

            //historial del servicio
            $servicioHistorial = new InfoServicioHistorial();
            $servicioHistorial->setServicioId($servicio);
            $servicioHistorial->setObservacion($strMsjHistorial);
            $servicioHistorial->setEstado("In-Corte");
            $servicioHistorial->setUsrCreacion($strUsrCreacion);
            $servicioHistorial->setFeCreacion(new \DateTime('now'));
            $servicioHistorial->setIpCreacion($strClientIp);
            $servicioHistorial->setAccion($accion->getNombreAccion());
            $em->persist($servicioHistorial);
            $em->flush();

            //Cortar servicios Nuevos McAfee, servicios McAfee antiguos MIGRADOS y NetlifeCloud
            if($entityAdmiProducto || $entityInfoPlanCab)
            {
                //Se verifica si es un producto Nuevo McAfee o un producto NetlifeCloud
                if ($entityAdmiProducto)
                {
                    $boolEsProdIProtegMultiPaid           = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO MULTI PAID');
                    $booleanValidaProducto                = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTEGIDO');
                    $booleanValidaProductoProteccionTotal = strpos($entityAdmiProducto->getDescripcionProducto(), 'I. PROTECCION');
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
                //Se verifica si es un producto Antiguo McAfee u NetlifeCloud
                else
                {
                    $booleanValidaProtegido = strpos($entityInfoPlanCab->getCodigoPlan(), 'MCAFEE');
                    $booleanValidaOfficeMig = strpos($entityInfoPlanCab->getCodigoPlan(), 'NetlifeCloud');
                }
                
                if($strTieneSuscriberId === "SI")
                {
                    $arrayParamsLicencias           = array("strProceso"                => "CORTE_ANTIVIRUS",
                                                            "strEscenario"              => "CORTE_PROD_ADICIONAL",
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
                //Se valida que sea un producto McAfee
                else if($booleanValidaProducto !== false || $booleanValidaProductoProteccionTotal!== false ||  $booleanValidaProtegido !== false )
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
                                                                                    "strEsActivacion"   => "NO"));
                    
                    $arrayParametros["strTipoTransaccion"] = 'Suspension';
                    if($arrayParametros["strError"] == 'true')
                    {
                        $arrayRespuestaServicio['status'] = 'ERROR';
                        throw new \Exception("problemas al obtener informacion del cliente");
                    }

                    $arrayParametros["strNombre"]         = "";
                    $arrayParametros["strApellido"]       = "";
                    $arrayParametros["strIdentificacion"] = "";
                    $arrayParametros["strPassword"]       = "";
                    $arrayParametros["strMetodo"]         = 'CancelarSuscripcion';
                    if($booleanValidaProducto !== false)
                    {
                        $arrayParametros["intLIC_QTY"]        = $arrayParametros["strCantidadDispositivos"];
                        $arrayParametros["intQTY"]            = 1;
                    }
                    else if($booleanValidaProductoProteccionTotal !== false || $booleanValidaProtegido !== false)
                    {
                        $arrayParametros["intLIC_QTY"]        = 0;
                        $arrayParametros["intQTY"]            = 1;
                    }

                    $respuestaServicio = $this->licenciasMcAfee->operacionesSuscripcionCliente($arrayParametros);
                    
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
                        $objServicioHistorial->setObservacion("No se ha podido cortar el servicio ".$strNombreServicio.
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
                    //Se envia vacio $arrayParametros["strMetodo"] debido a que no existen métodos para cortar servicio por parte del proveedor.
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


        //Proceso para notificar el corte del servicio a konibit mediante GDA en caso de aplicar.
        try
        {
            $objInfoEmpresaGrupo = $this->emComercial->getRepository('schemaBundle:InfoEmpresaGrupo')->find($strCodEmpresa);
            if (is_object($objInfoEmpresaGrupo) && $objInfoEmpresaGrupo->getPrefijo() === 'MD'
                    && $arrayRespuestaServicio['status'] === 'OK' && is_object($servicio))
            {
                $this->emInfraestructura->getRepository('schemaBundle:InfoServicioTecnico')
                        ->notificarKonibit(array ('intIdServicio'  => $servicio->getId(),
                                                  'strTipoProceso' => 'CORTAR',
                                                  'strTipoTrx'     => 'INDIVIDUAL',
                                                  'strUsuario'     => $strUsrCreacion,
                                                  'strIp'          => $strClientIp,
                                                  'objUtilService' => $this->serviceUtil));
            }
        }
        catch (\Exception $objException)
        {
            $this->serviceUtil->insertError('Telcos+',
                                            'InfoCortarServicioService->cortarServiciosOtros->adicional',
                                            'IdServicio: '.$servicio->getId().' - Error: '.$objException->getMessage(),
                                             $strUsrCreacion,
                                             $strClientIp);
        }


        return $arrayRespuestaServicio;
    }
    
    /**
     * cortarServiciosNetlifeWifi
     * 
     * Proceso de corte de productos Netlife Wifi para clientes cuyo
     * servicio de internet ha sido colocado como In-Corte
     * 
     * @param type $servicio Objeto Servicio
     * @param type $idEmpresa Codigo Empresa
     * 
     * @author  Veronica Carrasco Idrovo <vcarrasco@telconet.ec>
     * @version 1.0 30-06-2016
     * 
     * @author  Lizbeth Cruz <mlcruz@telconet.ec>
     * @version 1.1 07-10-2019 Se modifica proceso para utilizar nuevo esquema Netlifezone
     *
     * @author Richard Cabrera <rcabrera@telconet.ec>
     * @version 1.2 28-01-2021 - Se agrega quita la validacion del estado por Inactivacion del producto Netlife Zone
     * 
     * @author Jonathan Mazón Sánchez <jmazon@telconet.ec>
     * @version 1.3 28-02-2023 - Se agrega validación de existencia del producto, ya que para empresa Ecuanet no existe el producto.
     * 
     */
    public function cortarServiciosNetlifeWifi($arrayParametros)
    {
        $strRespuesta   = "";
        $strEstado      = "In-Corte";
        $strObservacion = "Netlife Zone: Servicio cortado.";
        $strMetodoWs    = "inactive_user";
        $objServicio    = $arrayParametros['objServicio'];
        $strCodEmpresa  = $arrayParametros['intIdEmpresa'];
        $intIdAccion    = $arrayParametros['intIdAccion'];
        $strUsrCreacion = $arrayParametros['strUsrCreacion'] ;
        $strIpCreacion  = $arrayParametros['strIpCreacion'];
                
        $objProductoNetwifi     = $this->emComercial->getRepository('schemaBundle:AdmiProducto')
                                                    ->findOneBy(array("nombreTecnico" =>  "NETWIFI",
                                                                      "empresaCod"    =>  $strCodEmpresa));
        if(is_object($objProductoNetwifi ))
        {
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
                $strRespuestaNetlifeZone                      =  $this->wifiNetlife->procesarOperacionesNetlifeWifi($arrayParametrosOperaciones);
                if($strRespuestaNetlifeZone !== "OK")
                {
                    $strRespuesta = $strRespuestaNetlifeZone;
                }
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
}
